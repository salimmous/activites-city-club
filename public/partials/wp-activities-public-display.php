<?php
/**
 * Template for displaying activities.
 * Updated for single-row layout, image gallery using Media IDs, and fixed duplicate dots.
 *
 * @since      1.0.0
 * @package    WP_Activities
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wp-activities-container style-1">
    <h2 class="wp-activities-title">NOS ACTIVITÉS</h2>
    <p class="wp-activities-subtitle">Découvrez notre large gamme d'activités pour tous les niveaux et tous les objectifs</p>

    <div class="wp-activities-categories">
        <a href="#" class="wp-activities-category-link active" data-category="">TOUTES LES ACTIVITÉS</a>
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'activity_category',
            'hide_empty' => true,
        ));
        usort($categories, function($a, $b) { return strcmp($a->name, $b->name); });
        foreach ($categories as $category) :
        ?>
            <a href="#" class="wp-activities-category-link" data-category="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html(strtoupper($category->name)); ?></a>
        <?php endforeach; ?>
    </div>

    <div class="wp-activities-list-wrapper">
        <div class="wp-activities-list">
            <?php
            $activity_index = 0;
            while ($activities->have_posts()) : $activities->the_post();
                $activity_index++;
                $post_id = get_the_ID();

                // Get activity meta
                $duration = get_post_meta($post_id, '_activity_duration', true);
                $level = get_post_meta($post_id, '_activity_level', true);
                $certified = get_post_meta($post_id, '_activity_certified', true);
                $is_popular = get_post_meta($post_id, '_activity_is_popular', true);
                $gallery_ids_string = get_post_meta($post_id, '_activity_gallery_ids', true); // Get IDs string
                $button_1_text = get_post_meta($post_id, '_activity_button_link_1_text', true) ?: "ESSAYER L'ACTIVITÉ";
                $button_1_url = get_post_meta($post_id, '_activity_button_link_1_url', true) ?: '#';
                $button_2_text = get_post_meta($post_id, '_activity_button_link_2_text', true) ?: "VOIR LE PLANNING";
                $button_2_url = get_post_meta($post_id, '_activity_button_link_2_url', true) ?: '#';

                // Prepare gallery images from IDs
                $gallery_image_urls = [];
                if (!empty($gallery_ids_string)) {
                    $gallery_ids = array_map('intval', explode(',', $gallery_ids_string));
                    $gallery_ids = array_filter($gallery_ids); // Remove empty/zero IDs
                    if (!empty($gallery_ids)) {
                        foreach ($gallery_ids as $id) {
                            $img_url = wp_get_attachment_image_url($id, 'large'); // Get URL for 'large' size
                            if ($img_url) {
                                $gallery_image_urls[] = $img_url;
                            }
                        }
                    }
                }
                $has_gallery = count($gallery_image_urls) > 0;

                // Get activity category and set background color
                $categories = get_the_terms($post_id, 'activity_category');
                $category_classes = '';
                $bg_color = '#b22222'; // Default red
                if ($categories && !is_wp_error($categories)) {
                    $first_category_slug = $categories[0]->slug;
                    foreach ($categories as $category) { $category_classes .= ' category-' . $category->slug; }
                    switch ($first_category_slug) {
                        case 'fitness': case 'combat': case 'zumba-dance': case 'yoga-for-beginners': case 'musculation': $bg_color = '#b22222'; break;
                        case 'cardio': case 'functional-training': $bg_color = '#2e8b57'; break;
                        case 'dance': case 'artistique': $bg_color = '#9932cc'; break;
                        case 'bien-etre': $bg_color = '#9370db'; break;
                        case 'aquatique': $bg_color = '#1e90ff'; break;
                        default: $bg_color = '#b22222';
                    }
                }

                $layout_class = 'layout-odd';
            ?>
                <div class="wp-activities-item <?php echo esc_attr($category_classes); ?> <?php echo esc_attr($layout_class); ?>" style="--activity-bg-color: <?php echo esc_attr($bg_color); ?>" data-activity-id="<?php echo $post_id; ?>" <?php if ($has_gallery) echo 'data-has-gallery="true"'; ?>>
                    <?php if ($is_popular) : ?>
                        <span class="wp-activities-popular">POPULAIRE</span>
                    <?php endif; ?>

                    <div class="wp-activities-item-content">
                        <h3 class="wp-activities-item-title"><?php the_title(); ?></h3>
                        <div class="wp-activities-item-description">
                            <?php
                            if (has_excerpt()) { the_excerpt(); }
                            else { $subtitle = get_post_meta($post_id, '_activity_subtitle', true); echo '<p>' . ($subtitle ? esc_html($subtitle) : wp_trim_words(get_the_content(), 20, '...')) . '</p>'; }
                            ?>
                        </div>

                        <div class="wp-activities-item-meta">
                            <?php if ($duration) : ?><div><i class="far fa-clock"></i> <span><?php echo esc_html($duration); ?> min</span></div><?php endif; ?>
                            <?php if ($level) : ?><div><i class="fas fa-users"></i> <span><?php echo esc_html($level); ?></span></div><?php endif; ?>
                            <?php if ($certified) : ?><div><i class="fas fa-award"></i> <span>Certifié</span></div><?php endif; ?>
                        </div>

                        <div class="wp-activities-item-actions">
                            <?php if ($button_1_text && $button_1_url) : ?><a href="<?php echo esc_url($button_1_url); ?>" class="wp-activities-button button-primary"><?php echo esc_html($button_1_text); ?></a><?php endif; ?>
                            <?php if ($button_2_text && $button_2_url) : ?><a href="<?php echo esc_url($button_2_url); ?>" class="wp-activities-button button-secondary"><?php echo esc_html($button_2_text); ?></a><?php else : ?><span class="wp-activities-button button-secondary is-placeholder"></span><?php endif; ?>
                            <?php /* REMOVED content slider dots: <div class="wp-activities-content-slider-dots">...</div> */ ?>
                        </div>
                    </div>

                    <div class="wp-activities-item-image">
                        <div class="wp-activities-image-slider-wrapper">
                            <?php if ($has_gallery) : ?>
                                <?php foreach ($gallery_image_urls as $index => $image_url) : ?>
                                    <div class="wp-activities-slide <?php echo ($index === 0) ? 'active' : ''; ?>">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?> - Image <?php echo $index + 1; ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php elseif (has_post_thumbnail()) : ?>
                                <div class="wp-activities-slide active"><?php the_post_thumbnail('large'); ?></div>
                            <?php else : ?>
                                <div class="wp-activities-slide active"><img src="https://via.placeholder.com/600x400?text=<?php echo urlencode(get_the_title()); ?>" alt="<?php the_title_attribute(); ?>"></div>
                            <?php endif; ?>
                        </div>

                        <div class="wp-activities-item-overlay">
                            <h3 class="wp-activities-overlay-title"><?php the_title(); ?></h3>
                            <?php if ($has_gallery) : ?>
                                <button class="wp-activities-img-nav prev"><i class="fas fa-chevron-left"></i></button>
                                <button class="wp-activities-img-nav next"><i class="fas fa-chevron-right"></i></button>
                            <?php endif; ?>
                        </div>

                         <?php if ($has_gallery) : ?>
                         <div class="wp-activities-image-slider-dots">
                             <?php foreach ($gallery_image_urls as $index => $image_url) : ?>
                                 <span class="wp-activities-dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-slide-index="<?php echo $index; ?>"></span>
                             <?php endforeach; ?>
                         </div>
                         <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>
