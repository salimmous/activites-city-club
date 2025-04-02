/**
 * Admin JavaScript for WP Activities plugin
 * Includes Media Library integration for gallery.
 *
 * @package    WP_Activities
 */

(function($) {
    'use strict';

    /**
     * Initialize the admin functionality
     */
    function initAdmin() {
        // Initialize color picker if available
        if ($.fn.wpColorPicker) {
            $('.wp-activities-color-picker').wpColorPicker();
        }

        // Toggle fields based on checkbox state
        $('.wp-activities-toggle-checkbox').on('change', function() {
            const targetSelector = $(this).data('target');
            if (targetSelector) {
                if ($(this).is(':checked')) {
                    $(targetSelector).show();
                } else {
                    $(targetSelector).hide();
                }
            }
        }).trigger('change'); // Trigger on load

        // --- Media Library Gallery ---
        let mediaFrame;
        const $galleryWrapper = $('.activity-gallery-preview-wrapper');
        const $galleryIdsInput = $('#activity_gallery_ids');

        $('#add_gallery_images_button').on('click', function(e) {
            e.preventDefault();

            // If the frame already exists, reopen it
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            // Create a new media frame
            mediaFrame = wp.media({
                title: 'Select or Upload Images for Gallery',
                button: {
                    text: 'Use these images'
                },
                library: {
                    type: 'image' // Only allow images
                },
                multiple: true // Allow multiple selections
            });

            // When images are selected, run a callback
            mediaFrame.on('select', function() {
                const selection = mediaFrame.state().get('selection');
                let attachmentIds = [];

                // Clear previous previews
                $galleryWrapper.html('');

                // Loop through selected images
                selection.each(function(attachment) {
                    const attachmentData = attachment.toJSON();
                    attachmentIds.push(attachmentData.id);

                    // Append preview image
                    $galleryWrapper.append(
                        '<div class="gallery-image-preview" data-attachment-id="' + attachmentData.id + '">' +
                        '<img src="' + attachmentData.sizes.thumbnail.url + '" alt="">' + // Use thumbnail size for preview
                        '<button type="button" class="button button-small remove-gallery-image">X</button>' +
                        '</div>'
                    );
                });

                // Update the hidden input field with comma-separated IDs
                $galleryIdsInput.val(attachmentIds.join(','));

                // Make previews sortable (optional)
                makeGallerySortable();
            });

            // Finally, open the modal
            mediaFrame.open();
        });

        // Handle removal of gallery images
        $galleryWrapper.on('click', '.remove-gallery-image', function(e) {
            e.preventDefault();
            const $previewDiv = $(this).closest('.gallery-image-preview');
            const attachmentIdToRemove = $previewDiv.data('attachment-id');

            // Remove the preview
            $previewDiv.remove();

            // Update the hidden input field
            let currentIds = $galleryIdsInput.val().split(',').map(Number); // Convert to numbers
            currentIds = currentIds.filter(id => id !== attachmentIdToRemove && id !== 0); // Remove the ID and any potential zeros
            $galleryIdsInput.val(currentIds.join(','));
        });

        // Make gallery previews sortable (requires jQuery UI sortable)
        function makeGallerySortable() {
             if (typeof $galleryWrapper.sortable === 'function') {
                $galleryWrapper.sortable({
                    items: '.gallery-image-preview',
                    cursor: 'move',
                    scrollSensitivity: 40,
                    forcePlaceholderSize: true,
                    forceHelperSize: false,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'gallery-sortable-placeholder',
                    start: function(event, ui) {
                        ui.item.css('background-color', '#f6f6f6');
                    },
                    stop: function(event, ui) {
                        ui.item.removeAttr('style'); // Remove background color
                        // Update the hidden input field based on new order
                        let newIds = [];
                        $galleryWrapper.find('.gallery-image-preview').each(function() {
                            newIds.push($(this).data('attachment-id'));
                        });
                        $galleryIdsInput.val(newIds.join(','));
                    }
                });
            }
        }
         // Initial call to make existing items sortable on page load
         makeGallerySortable();

    } // end initAdmin

    // Initialize when document is ready
    $(document).ready(function() {
        // Ensure wp.media exists before initializing (important for timing)
        if (typeof wp !== 'undefined' && wp.media) {
            initAdmin();
        } else {
            // Fallback or wait if wp.media is not immediately available
            $(window).on('load', function() {
                 if (typeof wp !== 'undefined' && wp.media) {
                    initAdmin();
                 } else {
                     console.error("WP Media library not loaded.");
                 }
            });
        }
    });

})(jQuery);
