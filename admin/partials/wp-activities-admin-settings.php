<?php
/**
 * Template for the admin settings page.
 *
 * @since      1.0.0
 * @package    WP_Activities
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap wp-activities-settings-page">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        // Output security fields
        settings_fields('wp_activities_options');
        // Output setting sections
        do_settings_sections('wp-activities-settings');
        // Submit button
        submit_button();
        ?>
    </form>
    
    <div class="wp-activities-settings-section">
        <h2><?php _e('How to Use', 'wp-activities'); ?></h2>
        <p><?php _e('Use the shortcode below to display activities on any page or post:', 'wp-activities'); ?></p>
        <code>[wp_activities]</code>
        
        <h3><?php _e('Available Parameters', 'wp-activities'); ?></h3>
        <ul>
            <li><code>category</code> - <?php _e('Filter by category slug (comma-separated for multiple)', 'wp-activities'); ?></li>
            <li><code>limit</code> - <?php _e('Number of activities to display (default: 10)', 'wp-activities'); ?></li>
            <li><code>orderby</code> - <?php _e('Order by field (default: date)', 'wp-activities'); ?></li>
            <li><code>order</code> - <?php _e('Order direction (default: DESC)', 'wp-activities'); ?></li>
        </ul>
        
        <h4><?php _e('Example', 'wp-activities'); ?>:</h4>
        <code>[wp_activities category="fitness,combat" limit="6" orderby="title" order="ASC"]</code>
    </div>

    <div class="wp-activities-settings-section">
        <h2><?php _e('Style 2 Shortcode', 'wp-activities'); ?></h2>
        <p><?php _e('Use the shortcode below to display activities in style 2 on any page or post:', 'wp-activities'); ?></p>
        <code>[wp_activities_style_2]</code>
        
        <h3><?php _e('Available Parameters', 'wp-activities'); ?></h3>
        <ul>
            <li><code>category</code> - <?php _e('Filter by category slug (comma-separated for multiple)', 'wp-activities'); ?></li>
            <li><code>limit</code> - <?php _e('Number of activities to display (default: 6)', 'wp-activities'); ?></li>
        </ul>
        
        <h4><?php _e('Example', 'wp-activities'); ?>:</h4>
        <code>[wp_activities_style_2 category="fitness,combat" limit="6"]</code>
    </div>
</div>
