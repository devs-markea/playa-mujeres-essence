<?php
$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

// Fields
$background_image_override = get_sub_field('background_image_override');
$bg_desktop = $background_image_override['desktop'] ?? null;
$bg_mobile  = $background_image_override['mobile']  ?? null;

$layout_width = get_sub_field('layout_width') ?: 'full';

$posts = get_sub_field('post'); // relationship

$post_id = 0;

if (is_array($posts) && !empty($posts)) {
    $first = $posts[0];
    $post_id = ($first instanceof WP_Post) ? $first->ID : (int) $first;
}

// Post data
$post_link  = $post_id ? get_permalink($post_id) : '';
$post_title = $post_id ? get_the_title($post_id) : '';
$post_excerpt = $post_id ? get_the_excerpt($post_id) : '';

// Background fallbacks
$bg_desktop_url = $bg_desktop['url'] ?? ($post_id ? get_the_post_thumbnail_url($post_id, 'full') : '');
$bg_mobile_url  = $bg_mobile['url']  ?? ($post_id ? get_the_post_thumbnail_url($post_id, 'large') : $bg_desktop_url);
?>

<?php if ( $post_id && $post_link ) : ?>
    <section data-anim="slide-up delay-2" class="featured-article-banner featured-article-banner--<?php echo esc_attr($layout_width); ?><?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
        <a class="featured-article-banner__link" href="<?php echo esc_url($post_link); ?>">
            <picture class="featured-article-banner__media">
                <?php if ( $bg_mobile_url ) : ?>
                    <source media="(max-width: 767px)" srcset="<?php echo esc_url($bg_mobile_url); ?>">
                <?php endif; ?>
                <img src="<?php echo esc_url($bg_desktop_url); ?>" alt="" loading="lazy">
            </picture>

            <div class="featured-article-banner__overlay" aria-hidden="true"></div>

            <div class="featured-article-banner__inner container">
                <div class="featured-article-banner__content">

                    <?php if ( $post_title ) : ?>
                        <h2 class="featured-article-banner__title"><?php echo esc_html($post_title); ?></h2>
                    <?php endif; ?>
                </div>
                <span class="featured-article-banner__cta text-white">Read more</span>
            </div>
        </a>
    </section>
<?php endif; ?>
