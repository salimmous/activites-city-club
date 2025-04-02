<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    WP_Activities
 */

namespace WP_Activities_Plugin; // Added namespace declaration

// If this file is called directly, abort.
if (!defined('WPINC')) {
    exit;
}

/**
 * The core plugin class.
 */
class WP_Activities {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WP_Activities_Post_Types    $post_types    Manages the custom post types.
     */
    protected $post_types;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WP_Activities_Taxonomies    $taxonomies    Manages the taxonomies.
     */
    protected $taxonomies;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WP_Activities_Admin    $admin    Manages the admin area functionality.
     */
    protected $admin;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WP_Activities_Shortcodes    $shortcodes    Manages the shortcodes.
     */
    protected $shortcodes;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Use fully qualified names or add 'use' statements if classes are in the same namespace
        $this->post_types = new WP_Activities_Post_Types();
        $this->taxonomies = new WP_Activities_Taxonomies();
        $this->admin = new WP_Activities_Admin();
        $this->shortcodes = new WP_Activities_Shortcodes();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        add_action('init', array($this->post_types, 'register_post_types'), 10);
        add_action('init', array($this->taxonomies, 'register_taxonomies'), 10);
        add_action('admin_menu', array($this->admin, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this->admin, 'add_meta_boxes'));
        add_action('save_post_activity', array($this->admin, 'save_meta_boxes'), 10, 2); // Specific hook for CPT
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Plugin activation/deactivation hooks
        // Use the constant defined in the loader
        register_deactivation_hook(WPAP_PLUGIN_BASENAME, array($this, 'deactivate'));
    }

    /**
     * The code that runs during plugin activation.
     * This needs to be static or called from a static context during activation.
     * For simplicity, we'll keep it instance-based for now, assuming the loader handles instantiation before activation.
     * A better approach might involve a dedicated static activation method.
     *
     * @since    1.0.0
     */
    public function activate() {
        // Ensure dependencies are loaded if not already
        $this->load_dependencies();
        // Create custom post types and taxonomies
        // $this->post_types->register_post_types(); // Moved to init hook
        // $this->taxonomies->register_taxonomies(); // Moved to init hook

        // Import test data
        $this->import_test_data();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * The code that runs during plugin deactivation.
     *
     * @since    1.0.0
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'wp-activities',
            WPAP_PLUGIN_URL . 'assets/css/wp-activities-public.css', // Use defined constant
            array(),
            WP_ACTIVITIES_VERSION, // Use defined constant
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'wp-activities',
            WPAP_PLUGIN_URL . 'assets/js/wp-activities-public.js', // Use defined constant
            array('jquery'),
            WP_ACTIVITIES_VERSION, // Use defined constant
            true
        );
    }

    /**
     * Import test data.
     *
     * @since    1.0.0
     */
    private function import_test_data() {
        $activities = array(
            array(
                'title' => 'Yoga for Beginners',
                'content' => 'A gentle introduction to yoga for beginners.',
                'duration' => 60,
                'level' => 'Débutant',
                'certified' => true,
                'popular' => true,
                'category' => 'bien-etre',
                'image' => 'https://via.placeholder.com/300x200',
                'button_1_text' => 'Book Now',
                'button_1_url' => '#',
                'button_2_text' => 'Learn More',
                'button_2_url' => '#',
            ),
            array(
                'title' => 'Cardio Blast',
                'content' => 'A high-intensity cardio workout.',
                'duration' => 45,
                'level' => 'Intermédiaire',
                'certified' => false,
                'popular' => false,
                'category' => 'cardio',
                'image' => 'https://via.placeholder.com/300x200',
                'button_1_text' => 'Book Now',
                'button_1_url' => '#',
                'button_2_text' => 'Learn More',
                'button_2_url' => '#',
            ),
            array(
                'title' => 'Zumba Dance',
                'content' => 'A fun and energetic Zumba dance class.',
                'duration' => 60,
                'level' => 'Tous niveaux',
                'certified' => false,
                'popular' => true,
                'category' => 'dance',
                'image' => 'https://via.placeholder.com/300x200',
                'button_1_text' => 'Book Now',
                'button_1_url' => '#',
                'button_2_text' => 'Learn More',
                'button_2_url' => '#',
            ),
        );

        foreach ($activities as $activity) {
            // Check if the activity already exists
            $args = array(
                'post_type' => 'activity',
                'title' => $activity['title'],
                'posts_per_page' => 1,
                'ignore_sticky_posts' => true,
            );
            $query = new \WP_Query( $args );
            if ( $query->have_posts() ) {
                continue;
            }

            // if (!$existing_activity) {
                $post_id = wp_insert_post(array(
                    'post_title' => $activity['title'],
                    'post_content' => $activity['content'],
                    'post_status' => 'publish',
                    'post_type' => 'activity',
                ));

                if ($post_id) {
                    update_post_meta($post_id, '_activity_duration', $activity['duration']);
                    update_post_meta($post_id, '_activity_level', $activity['level']);
                    update_post_meta($post_id, '_activity_certified', $activity['certified']);
                    update_post_meta($post_id, '_activity_is_popular', $activity['popular']);
                    update_post_meta($post_id, '_activity_button_link_1_text', $activity['button_1_text']);
                    update_post_meta($post_id, '_activity_button_link_1_url', $activity['button_1_url']);
                    update_post_meta($post_id, '_activity_button_link_2_text', $activity['button_2_text']);
                    update_post_meta($post_id, '_activity_button_link_2_url', $activity['button_2_url']);

                    // Set category
                    wp_set_object_terms($post_id, $activity['category'], 'activity_category');

                    // Set featured image
                    $this->set_featured_image($post_id, $activity['image']);
                }
            // }
        }
    }

    /**
     * Set featured image.
     *
     * @since    1.0.0
     */
    private function set_featured_image($post_id, $image_url) {
        // Add code to download the image and set it as featured image
        // This is a complex task and requires more code
        // For simplicity, we will skip this step
    }
}
