<?php
/**
 * The taxonomies functionality of the plugin.
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
 * The taxonomies class.
 */
class WP_Activities_Taxonomies {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Nothing to do here for now
    }

    /**
     * Register the taxonomies.
     * Hooked to 'init' action.
     *
     * @since    1.0.0
     */
    public function register_taxonomies() {
        $this->register_activity_category_taxonomy();
    }

    /**
     * Register the 'activity_category' taxonomy.
     *
     * @since    1.0.0
     */
    private function register_activity_category_taxonomy() {
        $labels = array(
            'name'                       => _x('Activity Categories', 'Taxonomy general name', 'wp-activities'),
            'singular_name'              => _x('Activity Category', 'Taxonomy singular name', 'wp-activities'),
            'search_items'               => __('Search Activity Categories', 'wp-activities'),
            'popular_items'              => __('Popular Activity Categories', 'wp-activities'),
            'all_items'                  => __('All Activity Categories', 'wp-activities'),
            'parent_item'                => __('Parent Activity Category', 'wp-activities'),
            'parent_item_colon'          => __('Parent Activity Category:', 'wp-activities'),
            'edit_item'                  => __('Edit Activity Category', 'wp-activities'),
            'update_item'                => __('Update Activity Category', 'wp-activities'),
            'add_new_item'               => __('Add New Activity Category', 'wp-activities'),
            'new_item_name'              => __('New Activity Category Name', 'wp-activities'),
            'separate_items_with_commas' => __('Separate activity categories with commas', 'wp-activities'),
            'add_or_remove_items'        => __('Add or remove activity categories', 'wp-activities'),
            'choose_from_most_used'      => __('Choose from the most used activity categories', 'wp-activities'),
            'not_found'                  => __('No activity categories found.', 'wp-activities'),
            'menu_name'                  => __('Categories', 'wp-activities'), // Changed for brevity in admin menu
            'back_to_items'              => __('Back to activity categories', 'wp-activities'),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true, // Like categories
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false, // Usually false for hierarchical taxonomies
            'show_in_rest'      => true, // Enable Gutenberg support
            'rewrite'           => array('slug' => 'activity-category'),
        );

        register_taxonomy('activity_category', array('activity'), $args); // Associate with 'activity' post type

        // Create default categories if they don't exist
        $this->create_default_activity_categories();
    }

    /**
     * Create default activity categories.
     *
     * @since    1.0.0
     */
    private function create_default_activity_categories() {
        $default_categories = array(
            'fitness' => __('Fitness', 'wp-activities'),
            'combat' => __('Combat', 'wp-activities'),
            'bien-etre' => __('Bien-être', 'wp-activities'),
            'artistique' => __('Artistique', 'wp-activities'),
            'aquatique' => __('Aquatique', 'wp-activities'),
            'cardio' => __('Cardio', 'wp-activities'),
            'dance' => __('Dance', 'wp-activities'),
            'functional-training' => __('Functional Training', 'wp-activities'),
            'musculation' => __('Musculation', 'wp-activities'),
        );

        foreach ($default_categories as $slug => $name) {
            if (!term_exists($name, 'activity_category')) {
                wp_insert_term($name, 'activity_category', array('slug' => $slug));
            }
        }
    }
}
