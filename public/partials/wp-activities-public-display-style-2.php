<?php
/**
 * Template for displaying activities in style 2 (Grid Layout).
 * Card design based on Zumba/Cardio/Yoga examples.
 *
 * @since      1.0.0
 * @package    WP_Activities
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

$options = get_option('wp_activities_options');
$activities_title = isset($options['activities_title']) ? esc_html($options['activities_title']) : __('Popular Activities', 'wp-activities');
$activities_subtitle = isset($options['activities_subtitle']) ? esc_html($options['activities_subtitle']) : __('Discover our wide range of fitness activities designed for all levels and interests.', 'wp-activities');
$view_all_text = isset($options['view_all_text']) ? esc_html($options['view_all_text']) : __('View All Activities →', 'wp-activities');
$view_all_url = isset($options['view_all_url']) ? esc_url($options['view_all_url']) : get_post_type_archive_link('activity');
?>

<div class="wp-activities-style-2-container">
    <div class="wp-activities-style-2-header">
        <div class="wp-activities-style-2-header-content">
             <h2 class="wp-activities-style-2-title"><?php echo $activities_title; ?></h2>
             <p class="wp-activities-style-2-subtitle"><?php echo $activities_subtitle; ?></p>
        </div>
        <?php if ($view_all_url && $view_all_text) : ?>
            <a href="<?php echo $view_all_url; ?>" class="wp-activities-style-2-view-all"><?php echo $view_all_text; ?></a>
        <?php endif; ?>
    </div>

    <div class="wp-activities-style-2-categories"> <?php /* Centered via CSS */ ?>
        <a href="#" class="wp-activities-style-2-category-link active" data-category="">All</a>
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'activity_category',
            'hide_empty' => true,
        ));
        usort($categories, function($a, $b) { return strcmp($a->name, $b->name); });

        foreach ($categories as $category) :
        ?>
            <a href="#" class="wp-activities-style-2-category-link" data-category="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?></a>
        <?php endforeach; ?>
    </div>

    <div class="wp-activities-style-2-list"> <?php /* Grid container */ ?>
        <?php
        while ($activities->have_posts()) : $activities->the_post();
            $post_id = get_the_ID();

            // Get activity meta
            $duration = get_post_meta($post_id, '_activity_duration', true);
            $level = get_post_meta($post_id, '_activity_level', true);
            $certified = get_post_meta($post_id, '_activity_certified', true);
            $is_popular = get_post_meta($post_id, '_activity_is_popular', true);
            $gallery_ids_string = get_post_meta($post_id, '_activity_gallery_ids', true);
            // Use Button 1 text/URL, default to "Book Now" for this design
            $button_text = get_post_meta($post_id, '_activity_button_link_1_text', true) ?: __('Book Now', 'wp-activities');
            $button_url = get_post_meta($post_id, '_activity_button_link_1_url', true) ?: get_permalink($post_id); // Link to post if no URL

            // Prepare Cover Image (Gallery > Featured > Placeholder)
            $cover_image_url = '';
            if (!empty($gallery_ids_string)) {
                $gallery_ids = array_map('intval', explode(',', $gallery_ids_string));
                $gallery_ids = array_filter($gallery_ids);
                if (!empty($gallery_ids)) {
                    $cover_image_url = wp_get_attachment_image_url($gallery_ids[0], 'large');
                }
            }
            if (empty($cover_image_url) && has_post_thumbnail()) {
                $cover_image_url = get_the_post_thumbnail_url($post_id, 'large');
            }
            if (empty($cover_image_url)) {
                 $cover_image_url = 'https://via.placeholder.com/400x250?text=' . urlencode(get_the_title()); // Placeholder
            }

            // Get activity category for badge and filtering
            $categories = get_the_terms($post_id, 'activity_category');
            $category_name = '';
            $category_classes = '';
            if ($categories && !is_wp_error($categories)) {
                $category_name = esc_html($categories[0]->name); // Use first category name for badge
                foreach ($categories as $category) {
                    $category_classes .= ' category-' . $category->slug;
                }
            }
        ?>
            <div class="wp-activities-style-2-item<?php echo esc_attr($category_classes); ?>">
                <div class="wp-activities-style-2-item-image-container">
                    <?php if ($is_popular) : ?>
                        <div class="wp-activities-style-2-popular-badge">Popular</div>
                    <?php endif; ?>
                    <?php if ($category_name) : ?>
                        <div class="wp-activities-style-2-category-badge"><?php echo $category_name; ?></div>
                    <?php endif; ?>
                    <?php if ($cover_image_url) : ?>
                        <a href="<?php echo esc_url($button_url); ?>" class="wp-activities-style-2-image-link">
                           <img src="<?php echo esc_url($cover_image_url); ?>" alt="<?php the_title_attribute(); ?>" class="wp-activities-style-2-item-image">
                        </a>
                    <?php endif; ?>
                </div>
                <div class="wp-activities-style-2-item-content">
                    <h3 class="wp-activities-style-2-item-title">
                         <a href="<?php echo esc_url($button_url); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php // Re-add meta info for this design ?>
                    <div class="wp-activities-style-2-item-meta">
                        <?php if ($duration) : ?>
                            <span class="wp-activities-style-2-item-duration"><i class="dashicons dashicons-clock"></i> <?php echo esc_html($duration); ?> min</span>
                        <?php endif; ?>
                        <?php if ($level) : ?>
                            <span class="wp-activities-style-2-item-level"><i class="dashicons dashicons-chart-bar"></i> <?php echo esc_html($level); ?></span>
                        <?php endif; ?>
                        <?php if ($certified) : ?>
                            <span class="wp-activities-style-2-item-certified"><i class="dashicons dashicons-yes-alt"></i> Certified</span>
                        <?php endif; ?>
                    </div>
                    <div class="wp-activities-style-2-item-description">
                         <?php
                            // Use excerpt if available, otherwise trimmed content
                            if (has_excerpt()) {
                                the_excerpt();
                            } else {
                                echo '<p>' . wp_trim_words(get_the_content(), 15, '...') . '</p>'; // Adjust word count as needed
                            }
                         ?>
                    </div>
                    <a href="<?php echo esc_url($button_url); ?>" class="wp-activities-style-2-item-link"><?php echo esc_html($button_text); ?></a>
                </div>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</div>
