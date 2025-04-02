<?php
/**
 * Plugin Name: WP Activities Manager
 * Plugin URI: https://example.com/wp-activities
 * Description: A WordPress plugin to manage and display fitness activities.
 * Version: 1.0.0
 * Author: WordPress Developer
 * Author URI: https://example.com
 * Text Domain: wp-activities
 * License: GPL-2.0+
 */

namespace WP_Activities_Plugin;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Ensure the loader class hasn't been loaded before (extra safety)
if (!class_exists('WP_Activities_Plugin\Plugin_Loader')) {

    final class Plugin_Loader {
        private static $instance = null;
        private $constants_defined = false;
        private $dependencies_loaded = false;
        private $plugin_initialized = false;

        private function __construct() {
            // Define constants immediately
            $this->define_constants();
            // Hook actions to WordPress load cycle
            add_action('plugins_loaded', [$this, 'load_dependencies'], 5); // Load dependencies early
            add_action('plugins_loaded', [$this, 'init_plugin'], 10);      // Initialize plugin after dependencies
        }

        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function define_constants() {
            // Prevent defining constants multiple times even if constructor is somehow called again
            if ($this->constants_defined) {
                return;
            }

            $constants = [
                'WP_ACTIVITIES_VERSION'      => '1.0.0',
                // Use the namespace in constant names to reduce collision risk further (optional but good practice)
                'WPAP_PLUGIN_DIR'            => plugin_dir_path(__FILE__),
                'WPAP_PLUGIN_URL'            => plugin_dir_url(__FILE__),
                'WPAP_PLUGIN_BASENAME'       => plugin_basename(__FILE__)
            ];

            foreach ($constants as $constant => $value) {
                if (!defined($constant)) {
                    define($constant, $value);
                }
            }
            // Define the old constants too for compatibility if needed, but check first
             if (!defined('WP_ACTIVITIES_PLUGIN_DIR')) define('WP_ACTIVITIES_PLUGIN_DIR', WPAP_PLUGIN_DIR);
             if (!defined('WP_ACTIVITIES_PLUGIN_URL')) define('WP_ACTIVITIES_PLUGIN_URL', WPAP_PLUGIN_URL);
             if (!defined('WP_ACTIVITIES_BASENAME')) define('WP_ACTIVITIES_BASENAME', WPAP_PLUGIN_BASENAME);


            $this->constants_defined = true;
        }

        public function load_dependencies() {
            if ($this->dependencies_loaded) {
                return;
            }

            $files = [
                'class-wp-activities',
                'class-wp-activities-post-types',
                'class-wp-activities-taxonomies',
                'class-wp-activities-admin',
                'class-wp-activities-shortcodes'
            ];

            foreach ($files as $file) {
                // Use the defined constant for directory path
                $file_path = WPAP_PLUGIN_DIR . "includes/{$file}.php";
                $class_name = __NAMESPACE__ . '\\' . str_replace('-', '_', $file); // Construct namespaced class name

                // Check file existence before requiring
                if (file_exists($file_path)) {
                     // Check class existence *after* potentially requiring
                    if (!class_exists($class_name)) {
                       require_once $file_path;
                    }
                } else {
                    // Optional: Log error if a required file is missing
                    // error_log("WP Activities Plugin Error: File not found - " . $file_path);
                }
            }
            $this->dependencies_loaded = true;
        }

        public function init_plugin() {
            if ($this->plugin_initialized) {
                return;
            }

            // Ensure the main plugin class exists within the namespace
            if (class_exists(__NAMESPACE__ . '\WP_Activities')) {
                // Instantiate using the fully qualified namespace
                $plugin = new \WP_Activities_Plugin\WP_Activities();
                $plugin->run();
                $this->plugin_initialized = true;

                 // Call the activation method here
                $plugin->activate();
            } else {
                 // Optional: Log error if main class is missing after loading dependencies
                 // error_log("WP Activities Plugin Error: Main class WP_Activities_Plugin\WP_Activities not found.");
            }
        }

        // Prevent cloning and unserialization
        private function __clone() {}
        public function __wakeup() {}

    } // End Plugin_Loader class

} // End if class_exists check

// Ensure the get_instance method exists before calling (extra safety)
if (class_exists('WP_Activities_Plugin\Plugin_Loader') && method_exists('WP_Activities_Plugin\Plugin_Loader', 'get_instance')) {
    // Initialize the plugin via the singleton instance
    \WP_Activities_Plugin\Plugin_Loader::get_instance();
} else {
    // Optional: Log error if the loader class itself is missing
    // error_log("WP Activities Plugin Error: Plugin_Loader class not available.");
    // You might want a fallback or admin notice here
    add_action('admin_notices', function() {
        echo '<div class="error"><p>WP Activities Manager plugin failed to load correctly. The Plugin_Loader class is missing.</p></div>';
    });
}

/**
 * The code that runs during plugin activation.
 * This needs to be static or called from a static context during activation.
 * For simplicity, we'll keep it instance-based for now, assuming the loader handles instantiation before activation.
 * A better approach might involve a dedicated static activation method.
 *
 * @since    1.0.0
 */
// Remove this function and the register_activation_hook call
// function activate_wp_activities() {
//     $plugin = new \WP_Activities_Plugin\WP_Activities();
//     $plugin->activate();
// }
// register_activation_hook( __FILE__, 'WP_Activities_Plugin\activate_wp_activities' );
