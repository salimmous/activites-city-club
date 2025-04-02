/**
 * Public JavaScript for WP Activities plugin
 * Includes Image Gallery Slider functionality for Style 1.
 * Removed slider logic for Style 2 (now a grid).
 *
 * @package    WP_Activities
 */

(function($) {
    'use strict';

    // --- Functions for Style 1 Image Gallery ---

    /**
     * Shows a specific slide in an activity's image gallery (Style 1).
     * @param {jQuery} $item The activity item element.
     * @param {number} index The index of the slide to show.
     */
    function showImageSlide($item, index) {
        const $slides = $item.find('.wp-activities-slide');
        const $dots = $item.find('.wp-activities-image-slider-dots .wp-activities-dot'); // Target image dots specifically
        const totalSlides = $slides.length;

        if (totalSlides <= 1) return; // Don't do anything if no slider

        // Ensure index is within bounds
        if (index >= totalSlides) index = 0;
        if (index < 0) index = totalSlides - 1;

        // Update slides
        $slides.removeClass('active');
        $slides.eq(index).addClass('active');

        // Update dots
        $dots.removeClass('active');
        $dots.eq(index).addClass('active');

        // Store current index
        $item.data('current-slide-index', index);
    }

    /**
     * Initializes the image gallery slider for a single activity item (Style 1).
     * @param {jQuery} $item The activity item element.
     */
    function initImageGallery($item) {
        const $slides = $item.find('.wp-activities-slide');
        if ($slides.length <= 1) return; // No slider needed for 0 or 1 image

        const $prevButton = $item.find('.wp-activities-img-nav.prev');
        const $nextButton = $item.find('.wp-activities-img-nav.next');
        const $dots = $item.find('.wp-activities-image-slider-dots .wp-activities-dot'); // Target image dots specifically

        // Initialize index
        $item.data('current-slide-index', 0);

        // Previous button click
        $prevButton.on('click', function(e) {
            e.preventDefault();
            let currentIndex = $item.data('current-slide-index') || 0;
            showImageSlide($item, currentIndex - 1);
        });

        // Next button click
        $nextButton.on('click', function(e) {
            e.preventDefault();
            let currentIndex = $item.data('current-slide-index') || 0;
            showImageSlide($item, currentIndex + 1);
        });

        // Dots click
        $dots.on('click', function(e) {
            e.preventDefault();
            const index = $(this).data('slide-index');
            showImageSlide($item, index);
        });

        // Show the initial slide
        showImageSlide($item, 0);
    }


    /**
     * Initialize the main activities functionality (filtering, etc.)
     */
    function initActivities() {
        // --- Category Filtering (Applies to both Style 1 and Style 2 containers) ---
        $('.wp-activities-category-link').on('click', function(e) {
            e.preventDefault();
            const $this = $(this);
            // Find the closest parent container (could be style-1 or style-2)
            const $container = $this.closest('.wp-activities-container, .wp-activities-style-2-container');
            const $categoryLinks = $container.find('.wp-activities-category-link, .wp-activities-style-2-category-link');
            // Find items within that specific container
            const $activityItems = $container.find('.wp-activities-item, .wp-activities-style-2-item');

            // Update active class for category links within the container
            $categoryLinks.removeClass('active');
            $this.addClass('active');

            const category = $this.data('category');

            if (!category) {
                // Show all activities within the container
                $activityItems.show();
            } else {
                // Hide all activities within the container first
                $activityItems.hide();
                // Show only activities with the selected category within the container
                $activityItems.filter('.category-' + category).show();
            }

            // Re-initialize Style 1 sliders for visible items if needed (optional)
            // $container.find('.wp-activities-item:visible[data-has-gallery="true"]').each(function() {
            //     initImageGallery($(this));
            // });
        });

        // --- Initialize Style 1 Image Galleries ---
        $('.wp-activities-container.style-1 .wp-activities-item[data-has-gallery="true"]').each(function() {
            initImageGallery($(this));
        });

        // --- REMOVED Style 2 Slider Logic ---
        // $('.wp-activities-style-2-category-link').on('click', ...); // Handled by combined filter above
        // $('.wp-activities-style-2-nav-prev, .wp-activities-style-2-nav-next').on('click', ...); // Removed

    }

    // Initialize when document is ready
    $(document).ready(function() {
        initActivities();
    });

})(jQuery);
