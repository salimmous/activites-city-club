<?php
/**
 * The post types functionality of the plugin.
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
 * The post types class.
 */
class WP_Activities_Post_Types {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Nothing to do here for now
    }

    /**
     * Register the post types.
     * Hooked to 'init' action.
     *
     * @since    1.0.0
     */
    public function register_post_types() {
        $this->register_activity_post_type();
    }

    /**
     * Register the 'activity' post type.
     *
     * @since    1.0.0
     */
    private function register_activity_post_type() {
        $labels = array(
            'name'                  => _x('Activities', 'Post type general name', 'wp-activities'),
            'singular_name'         => _x('Activity', 'Post type singular name', 'wp-activities'),
            'menu_name'             => _x('Activities', 'Admin Menu text', 'wp-activities'),
            'name_admin_bar'        => _x('Activity', 'Add New on Toolbar', 'wp-activities'),
            'add_new'               => __('Add New', 'wp-activities'),
            'add_new_item'          => __('Add New Activity', 'wp-activities'),
            'new_item'              => __('New Activity', 'wp-activities'),
            'edit_item'             => __('Edit Activity', 'wp-activities'),
            'view_item'             => __('View Activity', 'wp-activities'),
            'all_items'             => __('All Activities', 'wp-activities'),
            'search_items'          => __('Search Activities', 'wp-activities'),
            'parent_item_colon'     => __('Parent Activities:', 'wp-activities'),
            'not_found'             => __('No activities found.', 'wp-activities'),
            'not_found_in_trash'    => __('No activities found in Trash.', 'wp-activities'),
            'featured_image'        => _x('Activity Cover Image', 'Overrides the "Featured Image" metabox', 'wp-activities'),
            'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" text', 'wp-activities'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" text', 'wp-activities'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" text', 'wp-activities'),
            'archives'              => _x('Activity archives', 'The post type archive label used in nav menus', 'wp-activities'),
            'insert_into_item'      => _x('Insert into activity', 'Overrides the "Insert into post/page" text', 'wp-activities'),
            'uploaded_to_this_item' => _x('Uploaded to this activity', 'Overrides the "Uploaded to this post/page" text', 'wp-activities'),
            'filter_items_list'     => _x('Filter activities list', 'Screen reader text for the filter links', 'wp-activities'),
            'items_list_navigation' => _x('Activities list navigation', 'Screen reader text for the pagination', 'wp-activities'),
            'items_list'            => _x('Activities list', 'Screen reader text for the items list', 'wp-activities'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true, // This ensures it appears in the admin menu
            'query_var'          => true,
            'rewrite'            => array('slug' => 'activity'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20, // Position in the menu (optional, below Pages)
            'menu_icon'          => 'dashicons-universal-access', // Icon for the menu item
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'), // Added custom-fields support
            'show_in_rest'       => true, // Enable Gutenberg editor support
        );

        register_post_type('activity', $args);
    } // Closing brace for register_activity_post_type
} // Closing brace for the class
