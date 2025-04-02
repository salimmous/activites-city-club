<?php
/**
 * The admin-specific functionality of the plugin.
 * Updated for Media Library gallery selection.
 *
 * @since      1.0.0
 * @package    WP_Activities
 */

namespace WP_Activities_Plugin; // Added namespace declaration

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The admin-specific functionality of the plugin.
 */
class WP_Activities_Admin {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register the settings.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting(
            'wp_activities_options', // Option group
            'wp_activities_options', // Option name
            array($this, 'sanitize_settings') // Sanitize
        );

        add_settings_section(
            'wp_activities_settings_section', // ID
            __('General Settings', 'wp-activities'), // Title
            array($this, 'print_section_info'), // Callback
            'wp-activities-settings' // Page
        );

        add_settings_field(
            'activities_title', // ID
            __('Activities Title (Style 2)', 'wp-activities'), // Title
            array($this, 'activities_title_callback'), // Callback
            'wp-activities-settings', // Page
            'wp_activities_settings_section' // Section
        );

        add_settings_field(
            'activities_subtitle', // ID
            __('Activities Subtitle (Style 2)', 'wp-activities'), // Title
            array($this, 'activities_subtitle_callback'), // Callback
            'wp-activities-settings', // Page
            'wp_activities_settings_section' // Section
        );

        add_settings_field(
            'view_all_text', // ID
            __('View All Text (Style 2)', 'wp-activities'), // Title
            array($this, 'view_all_text_callback'), // Callback
            'wp-activities-settings', // Page
            'wp_activities_settings_section' // Section
        );
    }

    /**
     * Sanitize the settings.
     *
     * @since    1.0.0
     * @param    array    $input    Array of new values.
     * @return   array            Sanitized array.
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        if (isset($input['activities_title'])) {
            $sanitized['activities_title'] = sanitize_text_field($input['activities_title']);
        }
        if (isset($input['activities_subtitle'])) {
            $sanitized['activities_subtitle'] = sanitize_text_field($input['activities_subtitle']);
        }
        if (isset($input['view_all_text'])) {
            $sanitized['view_all_text'] = sanitize_text_field($input['view_all_text']);
        }
        return $sanitized;
    }

    /**
     * Print the Section text.
     *
     * @since    1.0.0
     */
    public function print_section_info() {
        _e('Enter settings for the [wp_activities_style_2] shortcode:', 'wp-activities');
    }

    /**
     * Activities Title Callback
     *
     * @since    1.0.0
     */
    public function activities_title_callback() {
        $options = get_option('wp_activities_options');
        $title = isset($options['activities_title']) ? esc_attr($options['activities_title']) : __('Popular Activities', 'wp-activities');
        printf(
            '<input type="text" id="activities_title" name="wp_activities_options[activities_title]" value="%s" class="regular-text" />',
            $title
        );
    }

    /**
     * Activities Subtitle Callback
     *
     * @since    1.0.0
     */
    public function activities_subtitle_callback() {
        $options = get_option('wp_activities_options');
        $subtitle = isset($options['activities_subtitle']) ? esc_attr($options['activities_subtitle']) : __('Discover our wide range of fitness activities designed for all levels and interests.', 'wp-activities');
        printf(
            '<input type="text" id="activities_subtitle" name="wp_activities_options[activities_subtitle]" value="%s" class="regular-text" />',
            $subtitle
        );
    }

    /**
     * View All Text Callback
     *
     * @since    1.0.0
     */
    public function view_all_text_callback() {
        $options = get_option('wp_activities_options');
        $text = isset($options['view_all_text']) ? esc_attr($options['view_all_text']) : __('View All Activities →', 'wp-activities');
        printf(
            '<input type="text" id="view_all_text" name="wp_activities_options[view_all_text]" value="%s" class="regular-text" />',
            $text
        );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Only enqueue on the relevant admin pages
        $screen = get_current_screen();
        if ($screen && ($screen->id === 'activity' || $screen->id === 'edit-activity' || strpos($screen->id, 'wp-activities-') !== false)) {
            wp_enqueue_style(
                'wp-activities-admin',
                WPAP_PLUGIN_URL . 'admin/css/wp-activities-admin.css', // Use defined constant
                array('wp-color-picker'),
                WP_ACTIVITIES_VERSION, // Use defined constant
                'all'
            );
            wp_enqueue_style( 'wp-color-picker' );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix) {
         // Only enqueue on the relevant admin pages (post edit screen for 'activity')
        $screen = get_current_screen();
        if ($screen && $screen->id === 'activity') {
            // Enqueue WordPress media scripts
            wp_enqueue_media();

            wp_enqueue_script(
                'wp-activities-admin',
                WPAP_PLUGIN_URL . 'admin/js/wp-activities-admin.js', // Use defined constant
                array('jquery', 'wp-color-picker', 'media-upload', 'thickbox'), // Add media dependencies
                WP_ACTIVITIES_VERSION, // Use defined constant
                true
            );
        }
         // Enqueue for settings page if needed (currently only color picker)
         if (strpos($hook_suffix, 'wp-activities-settings') !== false) {
             wp_enqueue_script('wp-activities-admin-settings', WPAP_PLUGIN_URL . 'admin/js/wp-activities-admin.js', array('jquery', 'wp-color-picker'), WP_ACTIVITIES_VERSION, true);
         }
    }

    /**
     * Add admin menu items.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=activity',
            __('Activities Settings', 'wp-activities'),
            __('Settings', 'wp-activities'),
            'manage_options',
            'wp-activities-settings',
            array($this, 'display_settings_page')
        );
        add_submenu_page(
            'edit.php?post_type=activity',
            __('Export Activities', 'wp-activities'),
            __('Export', 'wp-activities'),
            'manage_options',
            'wp-activities-export',
            array($this, 'display_export_page')
        );
        add_submenu_page(
            'edit.php?post_type=activity',
            __('Import Activities', 'wp-activities'),
            __('Import', 'wp-activities'),
            'manage_options',
            'wp-activities-import',
            array($this, 'display_import_page')
        );
    }

    /** Display Settings Page */
    public function display_settings_page() {
        if (!current_user_can('manage_options')) { wp_die(__('Access Denied')); }
        include_once WPAP_PLUGIN_DIR . 'admin/partials/wp-activities-admin-settings.php';
    }

    /** Display Export Page */
    public function display_export_page() {
        if (!current_user_can('manage_options')) { wp_die(__('Access Denied')); }
        if (isset($_POST['export_activities'])) { $this->export_activities(); }
        ?>
        <div class="wrap">
            <h1><?php _e('Export Activities', 'wp-activities'); ?></h1>
            <p><?php _e('Export all activities to a JSON file.', 'wp-activities'); ?></p>
            <form method="post">
                <?php wp_nonce_field('wp_activities_export_nonce', 'wp_activities_export_nonce_field'); ?>
                <input type="submit" name="export_activities" class="button button-primary" value="<?php _e('Export', 'wp-activities'); ?>">
            </form>
        </div>
        <?php
    }

    /** Display Import Page */
    public function display_import_page() {
        if (!current_user_can('manage_options')) { wp_die(__('Access Denied')); }
        if (isset($_POST['import_activities']) && check_admin_referer('wp_activities_import_nonce', 'wp_activities_import_nonce_field')) {
            $this->import_activities();
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Import Activities', 'wp-activities'); ?></h1>
            <p><?php _e('Import activities from a JSON file (previously exported from this plugin).', 'wp-activities'); ?></p>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('wp_activities_import_nonce', 'wp_activities_import_nonce_field'); ?>
                <p>
                    <label for="import_file"><?php _e('Select JSON file:', 'wp-activities'); ?></label>
                    <input type="file" name="import_file" id="import_file" accept=".json">
                </p>
                <input type="submit" name="import_activities" class="button button-primary" value="<?php _e('Import', 'wp-activities'); ?>">
            </form>
        </div>
        <?php
    }

    /** Export Activities Logic */
    public function export_activities() {
        if (!isset($_POST['wp_activities_export_nonce_field']) || !wp_verify_nonce($_POST['wp_activities_export_nonce_field'], 'wp_activities_export_nonce')) {
             wp_die(__('Security check failed!', 'wp-activities'));
        }
        if (!current_user_can('manage_options')) { wp_die(__('Access Denied')); }

        $activities = get_posts(array('post_type' => 'activity', 'posts_per_page' => -1, 'post_status' => 'any'));
        $data = array();
        foreach ($activities as $activity) {
            $meta = get_post_meta($activity->ID);
            // Clean up meta - remove single element arrays if desired, handle protected meta
            $cleaned_meta = [];
            foreach ($meta as $key => $value) {
                // Skip protected meta unless needed
                if (substr($key, 0, 1) === '_') continue;
                 $cleaned_meta[$key] = maybe_unserialize($value[0]);
            }
             // Add specific meta we need
             $cleaned_meta['_activity_duration'] = get_post_meta($activity->ID, '_activity_duration', true);
             $cleaned_meta['_activity_level'] = get_post_meta($activity->ID, '_activity_level', true);
             $cleaned_meta['_activity_certified'] = get_post_meta($activity->ID, '_activity_certified', true);
             $cleaned_meta['_activity_is_popular'] = get_post_meta($activity->ID, '_activity_is_popular', true);
             $cleaned_meta['_activity_gallery_ids'] = get_post_meta($activity->ID, '_activity_gallery_ids', true);
             $cleaned_meta['_activity_button_link_1_text'] = get_post_meta($activity->ID, '_activity_button_link_1_text', true);
             $cleaned_meta['_activity_button_link_1_url'] = get_post_meta($activity->ID, '_activity_button_link_1_url', true);
             $cleaned_meta['_activity_button_link_2_text'] = get_post_meta($activity->ID, '_activity_button_link_2_text', true);
             $cleaned_meta['_activity_button_link_2_url'] = get_post_meta($activity->ID, '_activity_button_link_2_url', true);
             $cleaned_meta['_thumbnail_id'] = get_post_thumbnail_id($activity->ID); // Export featured image ID

            $data[] = array(
                'title' => $activity->post_title,
                'content' => $activity->post_content,
                'excerpt' => $activity->post_excerpt,
                'status' => $activity->post_status,
                'meta' => $cleaned_meta,
                'terms' => wp_get_object_terms($activity->ID, 'activity_category', array('fields' => 'slugs')), // Export term slugs
            );
        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="wp-activities-export-' . date('Y-m-d') . '.json"');
        header("Pragma: no-cache");
        header("Expires: 0");
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /** Import Activities Logic */
    public function import_activities() {
        if (!current_user_can('manage_options')) { wp_die(__('Access Denied')); }

        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
            $file_path = $_FILES['import_file']['tmp_name'];
            $file_type = wp_check_filetype($_FILES['import_file']['name']);

            if ($file_type['ext'] !== 'json') {
                 echo '<div class="error"><p>' . __('Error: Please upload a valid .json file.', 'wp-activities') . '</p></div>';
                 return;
            }

            $data = json_decode(file_get_contents($file_path), true);

            if (is_array($data)) {
                $imported_count = 0;
                $skipped_count = 0;
                foreach ($data as $item) {
                    // Basic validation
                    if (empty($item['title'])) {
                        $skipped_count++;
                        continue;
                    }

                    // Check if activity exists by title (simple check)
                    $existing_activity = get_page_by_title($item['title'], OBJECT, 'activity');
                    if ($existing_activity) {
                        // Optionally update existing? For now, skip.
                        $skipped_count++;
                        continue;
                    }

                    // Create post
                    $post_data = array(
                        'post_title' => sanitize_text_field($item['title']),
                        'post_content' => wp_kses_post($item['content'] ?? ''),
                        'post_excerpt' => wp_kses_post($item['excerpt'] ?? ''),
                        'post_status' => isset($item['status']) && in_array($item['status'], ['publish', 'draft', 'pending', 'private']) ? $item['status'] : 'publish',
                        'post_type' => 'activity',
                    );
                    $post_id = wp_insert_post($post_data);

                    if ($post_id && !is_wp_error($post_id)) {
                        $imported_count++;
                        // Update meta
                        if (isset($item['meta']) && is_array($item['meta'])) {
                            foreach ($item['meta'] as $key => $value) {
                                // Only update our specific meta keys
                                if (in_array($key, [
                                    '_activity_duration', '_activity_level', '_activity_certified',
                                    '_activity_is_popular', '_activity_gallery_ids',
                                    '_activity_button_link_1_text', '_activity_button_link_1_url',
                                    '_activity_button_link_2_text', '_activity_button_link_2_url',
                                    '_thumbnail_id'
                                ])) {
                                     // Basic sanitization based on expected type
                                     if ($key === '_thumbnail_id' || $key === '_activity_duration') {
                                         $value = intval($value);
                                     } elseif (strpos($key, '_url') !== false) {
                                         $value = esc_url_raw($value);
                                     } else {
                                         $value = sanitize_text_field($value);
                                     }
                                     if ($key === '_thumbnail_id' && $value > 0) {
                                         set_post_thumbnail($post_id, $value);
                                     } else {
                                         update_post_meta($post_id, $key, $value);
                                     }
                                }
                            }
                        }
                        // Set terms (using slugs)
                        if (isset($item['terms']) && is_array($item['terms'])) {
                            wp_set_object_terms($post_id, $item['terms'], 'activity_category');
                        }
                    } else {
                         $skipped_count++;
                    }
                }
                echo '<div class="updated"><p>' . sprintf(__('Activities import complete: %d imported, %d skipped.', 'wp-activities'), $imported_count, $skipped_count) . '</p></div>';
            } else {
                echo '<div class="error"><p>' . __('Error: Invalid JSON file format.', 'wp-activities') . '</p></div>';
            }
        } else {
            echo '<div class="error"><p>' . __('Error: Please upload a file or file upload failed.', 'wp-activities') . '</p></div>';
        }
    }

    /**
     * Add meta boxes for the activity post type.
     * Hooked to 'add_meta_boxes' action.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'activity_details', // ID of the meta box
            __('Activity Details', 'wp-activities'), // Title of the meta box
            array($this, 'render_activity_details_meta_box'), // Callback function
            'activity', // Post type
            'normal', // Context (normal, side, advanced)
            'high' // Priority (high, core, default, low)
        );
    }

    /**
     * Render the activity details meta box content.
     * Updated for Media Library gallery.
     *
     * @since    1.0.0
     * @param    \WP_Post    $post    The post object.
     */
    public function render_activity_details_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('activity_details_nonce_action', 'activity_details_nonce');

        // Get current values
        $duration = get_post_meta($post->ID, '_activity_duration', true);
        $level = get_post_meta($post->ID, '_activity_level', true);
        $certified = get_post_meta($post->ID, '_activity_certified', true);
        $is_popular = get_post_meta($post->ID, '_activity_is_popular', true);
        $gallery_ids_string = get_post_meta($post->ID, '_activity_gallery_ids', true); // Get IDs
        $button_link_1_text = get_post_meta($post->ID, '_activity_button_link_1_text', true);
        $button_link_1_url = get_post_meta($post->ID, '_activity_button_link_1_url', true);
        $button_link_2_text = get_post_meta($post->ID, '_activity_button_link_2_text', true);
        $button_link_2_url = get_post_meta($post->ID, '_activity_button_link_2_url', true);

        $gallery_ids = !empty($gallery_ids_string) ? explode(',', $gallery_ids_string) : [];
        ?>
        <div class="activity-details-form">
            <div class="activity-detail-field">
                <label for="activity_duration"><?php esc_html_e('Duration (minutes)', 'wp-activities'); ?>:</label>
                <input type="number" id="activity_duration" name="activity_duration" value="<?php echo esc_attr($duration); ?>" min="1" max="999" step="1">
            </div>

            <div class="activity-detail-field">
                <label for="activity_level"><?php esc_html_e('Level', 'wp-activities'); ?>:</label>
                <select id="activity_level" name="activity_level">
                    <option value="" <?php selected($level, ''); ?>><?php esc_html_e('-- Select --', 'wp-activities'); ?></option>
                    <option value="Tous niveaux" <?php selected($level, 'Tous niveaux'); ?>><?php esc_html_e('Tous niveaux', 'wp-activities'); ?></option>
                    <option value="Débutant" <?php selected($level, 'Débutant'); ?>><?php esc_html_e('Débutant', 'wp-activities'); ?></option>
                    <option value="Intermédiaire" <?php selected($level, 'Intermédiaire'); ?>><?php esc_html_e('Intermédiaire', 'wp-activities'); ?></option>
                    <option value="Avancé" <?php selected($level, 'Avancé'); ?>><?php esc_html_e('Avancé', 'wp-activities'); ?></option>
                </select>
            </div>

            <div class="activity-detail-field">
                <label><input type="checkbox" name="activity_certified" value="1" <?php checked($certified, '1'); ?>> <?php esc_html_e('Certified Activity', 'wp-activities'); ?></label>
            </div>

            <div class="activity-detail-field">
                 <label><input type="checkbox" name="activity_is_popular" value="1" <?php checked($is_popular, '1'); ?>> <?php esc_html_e('Popular Activity', 'wp-activities'); ?></label>
                 <p class="description"><?php esc_html_e('Mark this activity as popular to highlight it.', 'wp-activities'); ?></p>
            </div>

            <div class="activity-detail-field">
                <label><?php esc_html_e('Activity Gallery', 'wp-activities'); ?>:</label>
                <div class="activity-gallery-preview-wrapper">
                    <?php
                    if (!empty($gallery_ids)) {
                        foreach ($gallery_ids as $image_id) {
                            $image_id = intval($image_id);
                            if ($image_id > 0) {
                                echo '<div class="gallery-image-preview" data-attachment-id="' . esc_attr($image_id) . '">';
                                echo wp_get_attachment_image($image_id, 'thumbnail'); // Show thumbnail preview
                                echo '<button type="button" class="button button-small remove-gallery-image">X</button>';
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                </div>
                <input type="hidden" id="activity_gallery_ids" name="activity_gallery_ids" value="<?php echo esc_attr($gallery_ids_string); ?>">
                <button type="button" class="button" id="add_gallery_images_button"><?php esc_html_e('Add/Edit Gallery Images', 'wp-activities'); ?></button>
                <p class="description"><?php esc_html_e('Select images for the activity gallery slider.', 'wp-activities'); ?></p>
            </div>


            <hr> <?php // Separator ?>

            <div class="activity-detail-field">
                <label for="activity_button_link_1_text"><?php esc_html_e('Button 1 Text', 'wp-activities'); ?>:</label>
                <input type="text" id="activity_button_link_1_text" name="activity_button_link_1_text" value="<?php echo esc_attr($button_link_1_text); ?>" placeholder="<?php esc_attr_e("e.g., ESSAYER L'ACTIVITÉ", 'wp-activities'); ?>">
            </div>

            <div class="activity-detail-field">
                <label for="activity_button_link_1_url"><?php esc_html_e('Button 1 URL', 'wp-activities'); ?>:</label>
                <input type="url" id="activity_button_link_1_url" name="activity_button_link_1_url" value="<?php echo esc_attr($button_link_1_url); ?>" placeholder="#">
            </div>

            <div class="activity-detail-field">
                <label for="activity_button_link_2_text"><?php esc_html_e('Button 2 Text', 'wp-activities'); ?>:</label>
                <input type="text" id="activity_button_link_2_text" name="activity_button_link_2_text" value="<?php echo esc_attr($button_link_2_text); ?>" placeholder="<?php esc_attr_e("e.g., VOIR LE PLANNING", 'wp-activities'); ?>">
            </div>

            <div class="activity-detail-field">
                <label for="activity_button_link_2_url"><?php esc_html_e('Button 2 URL', 'wp-activities'); ?>:</label>
                <input type="url" id="activity_button_link_2_url" name="activity_button_link_2_url" value="<?php echo esc_attr($button_link_2_url); ?>" placeholder="#">
            </div>
        </div>
        <?php
    }

    /**
     * Save the meta box data when the post is saved.
     * Updated for Media Library gallery IDs.
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @param    \WP_Post   $post       The post object.
     */
    public function save_meta_boxes($post_id, $post) {

        // Nonce check
        if (!isset($_POST['activity_details_nonce']) || !wp_verify_nonce($_POST['activity_details_nonce'], 'activity_details_nonce_action')) { return $post_id; }
        // Permission check
        if (!current_user_can('edit_post', $post_id)) { return $post_id; }
        // Autosave check
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
        // Post type check
        if ('activity' !== $post->post_type) { return $post_id; }

        // --- Save Fields ---

        // Duration
        $duration = isset($_POST['activity_duration']) ? intval($_POST['activity_duration']) : '';
        update_post_meta($post_id, '_activity_duration', $duration);

        // Level
        $level = isset($_POST['activity_level']) ? sanitize_text_field($_POST['activity_level']) : '';
        update_post_meta($post_id, '_activity_level', $level);

        // Certified
        $certified = isset($_POST['activity_certified']) ? '1' : '0';
        update_post_meta($post_id, '_activity_certified', $certified);

        // Popular
        $is_popular = isset($_POST['activity_is_popular']) ? '1' : '0';
        update_post_meta($post_id, '_activity_is_popular', $is_popular);

        // Gallery IDs
        if (isset($_POST['activity_gallery_ids'])) {
            $gallery_ids_raw = explode(',', sanitize_text_field($_POST['activity_gallery_ids']));
            $gallery_ids_sanitized = array_map('intval', $gallery_ids_raw); // Ensure integers
            $gallery_ids_sanitized = array_filter($gallery_ids_sanitized); // Remove zeros/empty
            $gallery_ids_string = implode(',', $gallery_ids_sanitized);
            update_post_meta($post_id, '_activity_gallery_ids', $gallery_ids_string);
        } else {
            delete_post_meta($post_id, '_activity_gallery_ids');
        }
        // Delete the old URL-based meta field if it exists
        delete_post_meta($post_id, '_activity_gallery');


        // Button 1 Text
        $button_1_text = isset($_POST['activity_button_link_1_text']) ? sanitize_text_field($_POST['activity_button_link_1_text']) : '';
        update_post_meta($post_id, '_activity_button_link_1_text', $button_1_text);

        // Button 1 URL
        $button_1_url = isset($_POST['activity_button_link_1_url']) ? esc_url_raw($_POST['activity_button_link_1_url']) : '';
        update_post_meta($post_id, '_activity_button_link_1_url', $button_1_url);

        // Button 2 Text
        $button_2_text = isset($_POST['activity_button_link_2_text']) ? sanitize_text_field($_POST['activity_button_link_2_text']) : '';
        update_post_meta($post_id, '_activity_button_link_2_text', $button_2_text);

        // Button 2 URL
        $button_2_url = isset($_POST['activity_button_link_2_url']) ? esc_url_raw($_POST['activity_button_link_2_url']) : '';
        update_post_meta($post_id, '_activity_button_link_2_url', $button_2_url);

    }
}
