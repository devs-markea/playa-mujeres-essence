<?php
$args = wp_parse_args($args ?? array(), array(
    'blog_page_id'  => 0,
    'blog_settings' => array(),
    'hero_post_ids' => array(),
));

$blog_settings = is_array($args['blog_settings']) ? $args['blog_settings'] : array();
$hero_post_ids = array_filter( array_map( 'absint', (array) $args['hero_post_ids'] ) );

$heading_hero       = isset($blog_settings['page_heading']) ? $blog_settings['page_heading'] : '';
$heading_level_hero = isset($blog_settings['page_heading_level']) ? $blog_settings['page_heading_level'] : 'h1';
$description_hero   = isset($blog_settings['page_description']) ? $blog_settings['page_description'] : '';

$allowed_tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
if (! in_array($heading_level_hero, $allowed_tags, true)) {
    $heading_level_hero = 'h1';
}

// Usar los IDs pre-consultados desde template-page-blog.php (máximo 4)
$hero_post_ids = array_slice( $hero_post_ids, 0, 4 );

if ( ! empty( $hero_post_ids ) ) {
    $hero_query = new WP_Query( array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'post__in'            => $hero_post_ids,
        'orderby'             => 'post__in',
        'posts_per_page'      => 4,
        'ignore_sticky_posts' => true,
    ) );
} else {
    $hero_query = new WP_Query( array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 4,
        'ignore_sticky_posts' => true,
    ) );
}

if (! $hero_query->have_posts() && ! $heading_hero && ! $description_hero) {
    return;
}
?>

<section class="blog-hero">
    <div class="container">

        <?php if ( $heading_hero || $description_hero) : ?>
        <div class="row mb-4 mb-lg-5">
            <div class="col-lg-8">
                <div class="blog-hero__subheading mb-2">
                    <div class="blog-hero__subheading--divider"></div>
                    <span>
                        <?php the_title(); ?>
                    </span>
                </div>

                <?php if ($heading_hero) : ?>
                <<?php echo esc_attr($heading_level_hero); ?> class="blog-hero__heading mb-0">
                <?php echo esc_html($heading_hero); ?>
            </<?php echo esc_attr($heading_level_hero); ?>>
            <?php endif; ?>

            <?php if ($description_hero) : ?>
                <div class="blog-hero__description mt-3">
                    <?php echo wp_kses_post($description_hero); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if ($hero_query->have_posts()) : ?>
        <div class="blog-hero__slider-wrap">
            <div class="blog-hero__swiper swiper" data-blog-hero-swiper>
                <div class="swiper-wrapper">
                    <?php
                    while ($hero_query->have_posts()) :
                        $hero_query->the_post();
                        ?>
                        <div class="blog-hero__slide swiper-slide">
                            <?php
                            get_template_part(
                                    'template-parts/blog/hero-main-card',
                                    null,
                                    array(
                                            'post_id'     => get_the_ID(),
                                            'heading_tag' => 'h2',
                                    )
                            );
                            ?>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>

            <div class="blog-hero__controls">
                <button type="button" class="blog-hero__nav blog-hero__nav--prev" aria-label="Previous posts">
                    <span aria-hidden="true">←</span>
                </button>
                <button type="button" class="blog-hero__nav blog-hero__nav--next" aria-label="Next posts">
                    <span aria-hidden="true">→</span>
                </button>
            </div>
        </div>
    <?php endif; ?>

    </div>
</section>
