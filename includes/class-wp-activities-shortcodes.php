<?php
/**
 * The shortcodes functionality of the plugin.
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
 * The shortcodes class.
 */
class WP_Activities_Shortcodes {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_shortcode('wp_activities', array($this, 'activities_shortcode'));
        add_shortcode('wp_activities_style_2', array($this, 'activities_shortcode_style_2'));
    }

    /**
     * Shortcode to display activities.
     * Example: [wp_activities category="fitness,combat" limit="6" orderby="title" order="ASC"]
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string            The shortcode output HTML.
     */
    public function activities_shortcode($atts) {
        // Normalize attribute keys to lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // Override default attributes with user attributes
        $atts = shortcode_atts(
            array(
                'category' => '',       // Comma-separated list of category slugs
                'limit'    => 10,       // Max number of activities to show
                'orderby'  => 'date',   // Field to order by (date, title, rand, menu_order)
                'order'    => 'DESC',   // Order direction (ASC, DESC)
            ),
            $atts,
            'wp_activities' // Shortcode name
        );

        // Sanitize attributes
        $limit = intval($atts['limit']);
        $orderby = sanitize_key($atts['orderby']);
        $order = strtoupper($atts['order']);
        $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC'; // Ensure valid order

        // --- WP_Query arguments ---
        $args = array(
            'post_type'      => 'activity',
            'post_status'    => 'publish',
            'posts_per_page' => $limit > 0 ? $limit : -1, // Use -1 to show all if limit is 0 or less
            'orderby'        => $orderby,
            'order'          => $order,
            'no_found_rows'  => true, // Improve performance if pagination is not needed
        );

        // Add taxonomy query if categories are specified
        if (!empty($atts['category'])) {
            $category_slugs = array_map('sanitize_title', explode(',', $atts['category']));
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'activity_category',
                    'field'    => 'slug',
                    'terms'    => $category_slugs,
                    'operator' => 'IN', // Default operator
                ),
            );
        }

        // --- Execute the query ---
        $activities = new \WP_Query($args); // Use fully qualified WP_Query

        // --- Generate output ---
        ob_start(); // Start output buffering

        if ($activities->have_posts()) {
            // Include the template partial to display the activities
            // Pass the query object to the template
            include WPAP_PLUGIN_DIR . 'public/partials/wp-activities-public-display.php'; // Use defined constant
        } else {
            // Optional: Display a message if no activities are found
            // echo '<p>' . esc_html__('No activities found matching your criteria.', 'wp-activities') . '</p>';
        }

        wp_reset_postdata(); // Restore original post data

        return ob_get_clean(); // Return the buffered content
    }

    /**
     * Shortcode to display activities in style 2.
     * Example: [wp_activities_style_2 category="fitness,combat" limit="6"]
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string            The shortcode output HTML.
     */
    public function activities_shortcode_style_2($atts) {
        // Normalize attribute keys to lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // Override default attributes with user attributes
        $atts = shortcode_atts(
            array(
                'category' => '',       // Comma-separated list of category slugs
                'limit'    => 6,       // Max number of activities to show
            ),
            $atts,
            'wp_activities_style_2' // Shortcode name
        );

        // Sanitize attributes
        $limit = intval($atts['limit']);

        // --- WP_Query arguments ---
        $args = array(
            'post_type'      => 'activity',
            'post_status'    => 'publish',
            'posts_per_page' => $limit > 0 ? $limit : -1, // Use -1 to show all if limit is 0 or less
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true, // Improve performance if pagination is not needed
        );

        // Add taxonomy query if categories are specified
        if (!empty($atts['category'])) {
            $category_slugs = array_map('sanitize_title', explode(',', $atts['category']));
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'activity_category',
                    'field'    => 'slug',
                    'terms'    => $category_slugs,
                    'operator' => 'IN', // Default operator
                ),
            );
        }

        // --- Execute the query ---
        $activities = new \WP_Query($args); // Use fully qualified WP_Query

        // --- Generate output ---
        ob_start(); // Start output buffering

        if ($activities->have_posts()) {
            // Include the template partial to display the activities
            // Pass the query object to the template
            include WPAP_PLUGIN_DIR . 'public/partials/wp-activities-public-display-style-2.php'; // Use defined constant
        } else {
            // Optional: Display a message if no activities are found
            echo '<p>' . esc_html__('No activities found matching your criteria.', 'wp-activities') . '</p>';
        }

        wp_reset_postdata(); // Restore original post data

        return ob_get_clean(); // Return the buffered content
    }
}
