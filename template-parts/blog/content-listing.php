<?php
$args = wp_parse_args($args ?? array(), array(
    'blog_page_id'    => 0,
    'blog_settings'   => array(),
    'current_term'    => null,
    'listing_context' => 'blog',
    'hero_post_ids'   => array(),
));

$blog_page_id  = (int) $args['blog_page_id'];
$blog_settings = is_array($args['blog_settings']) ? $args['blog_settings'] : array();
$current_term  = $args['current_term'] instanceof WP_Term ? $args['current_term'] : null;
$listing_context = in_array($args['listing_context'], array('blog', 'category'), true) ? $args['listing_context'] : 'blog';
$hero_post_ids = array_filter( array_map( 'absint', (array) $args['hero_post_ids'] ) );

$listing_heading       = isset($blog_settings['heading_listing']) ? $blog_settings['heading_listing'] : '';
$listing_heading_level = isset($blog_settings['heading_level_listing']) ? $blog_settings['heading_level_listing'] : 'h2';
$listing_description   = isset($blog_settings['description_listing']) ? $blog_settings['description_listing'] : '';

$display_categories_filter = ! empty($blog_settings['display_categories_filter']);
$display_featured_posts    = ! empty($blog_settings['display_featured_posts']);

$enable_load_more    = ! empty($blog_settings['enable_load_more']);
$posts_per_page      = isset($blog_settings['posts_per_page']) ? (int) $blog_settings['posts_per_page'] : 8;
$posts_per_page      = $posts_per_page > 0 ? $posts_per_page : 8;
$load_more_text      = isset($blog_settings['button_text']) ? $blog_settings['button_text'] : '';

$listing_heading_tag = function_exists('pm_essence_heading_tag_or_null') ? pm_essence_heading_tag_or_null($listing_heading_level, 'h2') : 'h2';

$selected_category_slug = isset($_GET['blog_category']) ? sanitize_title(wp_unslash($_GET['blog_category'])) : '';
$selected_category      = $selected_category_slug ? get_term_by('slug', $selected_category_slug, 'category') : $current_term;

if ($selected_category && is_wp_error($selected_category)) {
    $selected_category      = false;
    $selected_category_slug = '';
}

if ($current_term instanceof WP_Term) {
    $listing_heading     = '';
    $listing_description = '';
}

$categories = get_categories(array(
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
    'exclude'    => array( get_cat_ID( 'Uncategorized' ) ),
));

$sidebar_featured_posts = array();
$featured_swiper_posts  = array();

// Posts destacados: tag 'featured' (Polylang filtra automáticamente por idioma)
$featured_swiper_query = new WP_Query( array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'tag'                 => 'featured',
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
) );
$featured_swiper_posts = $featured_swiper_query->have_posts() ? $featured_swiper_query->posts : array();
wp_reset_postdata();
// Sin posts con tag 'featured' → swiper se oculta

$swiper_post_ids = ! empty( $featured_swiper_posts )
    ? array_map( function( $p ) { return $p->ID; }, $featured_swiper_posts )
    : array();

// Cargamos los 2 primeros lotes (8 + 8) en una sola query
$pm_batch   = 8;
$listing_query_args = array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => $pm_batch * 2,
    'paged'               => 1,
    'ignore_sticky_posts' => true,
);

$exclude_ids = array_values( array_unique( array_merge( $hero_post_ids, $swiper_post_ids ) ) );
if ( ! empty( $exclude_ids ) ) {
    $listing_query_args['post__not_in'] = $exclude_ids;
}

if ($selected_category instanceof WP_Term) {
    $listing_query_args['tax_query'] = array(
        array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $selected_category->term_id,
        ),
    );
}

$listing_query = new WP_Query($listing_query_args);
$listing_posts = $listing_query->have_posts() ? $listing_query->posts : array();
$listing_posts = array_values($listing_posts);

$first_listing_posts = array_slice($listing_posts, 0, $pm_batch);
$remaining_posts     = array_slice($listing_posts, $pm_batch);

// ¿Hay más posts más allá de los 16 cargados?
$has_more_posts = $listing_query->found_posts > ( $pm_batch * 2 );
$load_more_page     = 3; // página siguiente tras los 2 lotes iniciales
$load_more_exclude  = implode( ',', $exclude_ids );
$load_more_category = ( $selected_category instanceof WP_Term ) ? (int) $selected_category->term_id : 0;
$load_more_lang     = function_exists( 'pll_current_language' ) ? pll_current_language() : '';

if ( $display_featured_posts ) {
    $sidebar_featured_posts = $featured_swiper_posts;
}

$has_categories_sidebar = $display_categories_filter && ! empty($categories);
$has_top_posts_sidebar  = $display_featured_posts && ! empty($sidebar_featured_posts);

$sticky_sidebar_target = '';

if ($has_categories_sidebar) {
    $sticky_sidebar_target = 'categories';
} elseif ($has_top_posts_sidebar) {
    $sticky_sidebar_target = 'top-posts';
}

$has_listing_content = $listing_heading || $listing_description || ! empty($listing_posts) || ! empty($featured_swiper_posts) || ! empty($sidebar_featured_posts);

if (! $has_listing_content) {
    return;
}


?>

<section class="blog-listing container" data-blog-listing-root>
    <div class="row g-0">
        <div class="col-10 mx-auto">
            <?php if ($listing_description || $listing_heading_tag) : ?>
            <div class="blog-listing__intro row g-0">
                <div class="col-md-12">
                    <?php if ($listing_heading_tag) : ?>
                    <<?php echo tag_escape($listing_heading_tag); ?> class="blog-listing__heading">
                    <?php pll_e('Read More About Our Experiences'); ?>
                </<?php echo tag_escape($listing_heading_tag); ?>>
                <?php endif; ?>

                <?php if ($listing_description) : ?>
                    <div class="blog-listing__description">
                        <?php echo wp_kses_post($listing_description); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="blog-listing__layout row g-5">
            <div class="col-md-3 order-2 order-md-1">
                <aside class="blog-listing__sidebar<?php echo $sticky_sidebar_target ? ' blog-listing__sidebar--sticky blog-listing__sidebar--sticky-' . esc_attr($sticky_sidebar_target) : ''; ?>">
                    <?php if ($has_categories_sidebar) : ?>
                        <div class="blog-listing__sidebar-block blog-listing__sidebar-block--categories">
                            <h3 class="blog-listing__sidebar-title"><?php pll_e('Categories'); ?></h3>
                            <ul class="blog-listing__categories">
                                <?php foreach ($categories as $category) : ?>
                                    <?php
                                    $is_active = $selected_category instanceof WP_Term && (int) $selected_category->term_id === (int) $category->term_id;
                                    $url       = get_term_link( $category );
                                    ?>
                                    <li class="blog-listing__categories-item<?php echo $is_active ? ' is-active' : ''; ?>">
                                        <a href="<?php echo esc_url($url); ?>" class="blog-listing__categories-link">
                                            <span><?php echo esc_html($category->name); ?></span>
                                            <span class="blog-listing__categories-arrow" aria-hidden="true">&rarr;</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ( function_exists('wpp_get_mostpopular') ) : ?>
                        <div class="blog-listing__sidebar-block blog-listing__sidebar-block--panel blog-listing__sidebar-block--top-posts">
                            <h3 class="blog-listing__sidebar-title"><?php pll_e('Top Posts'); ?></h3>
                            <?php
                            wpp_get_mostpopular( array(
                                'limit'              => 4,
                                'range'              => 'all',
                                'thumbnail_width'    => 0,
                                'thumbnail_height'   => 0,
                                'display_post_title' => 1,
                                'wpp_start'          => '<ul class="blog-listing__top-posts">',
                                'wpp_end'            => '</ul>',
                                'post_html'          => '<li class="blog-listing__top-posts-item"><a href="{url}" class="blog-listing__top-posts-link">{text_title}</a></li>',
                            ) );
                            ?>
                        </div>
                    <?php elseif ($has_top_posts_sidebar) : ?>
                        <div class="blog-listing__sidebar-block blog-listing__sidebar-block--panel blog-listing__sidebar-block--top-posts">
                            <h3 class="blog-listing__sidebar-title"><?php pll_e('Top Posts'); ?></h3>
                            <ul class="blog-listing__top-posts">
                                <?php foreach ($sidebar_featured_posts as $featured_post) : ?>
                                    <li class="blog-listing__top-posts-item">
                                        <a href="<?php echo esc_url(get_permalink($featured_post)); ?>" class="blog-listing__top-posts-link">
                                            <?php echo esc_html(get_the_title($featured_post)); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                </aside>
            </div>
            <div class="col-md-9 order-1 order-md-2">
            <div class="blog-listing__content" data-blog-listing-content>
                <?php if (! empty($listing_posts)) : ?>
                    <?php if (! empty($first_listing_posts)) : ?>
                        <div class="blog-listing__grid row g-0" data-blog-listing-grid data-blog-listing-items>
                            <div class="col-12">
                                <div class="row g-0">
                                    <?php foreach ($first_listing_posts as $index => $listing_post) : ?>
                                        <?php
                                        $post_id        = (int) $listing_post->ID;
                                        $post_title     = get_the_title($post_id);
                                        $post_permalink = get_permalink($post_id);
                                        $post_terms     = get_the_terms($post_id, 'category');
                                        $post_term_name = (! is_wp_error($post_terms) && ! empty($post_terms)) ? $post_terms[0]->name : '';
                                        ?>
                                        <div class="col-12 col-lg-6 mb-4 mb-lg-5" data-blog-listing-grid-item>
                                            <article class="blog-listing__card card">
                                                <a href="<?php echo esc_url($post_permalink); ?>" class="blog-listing__card-media-link" aria-label="<?php echo esc_attr($post_title); ?>">
                                                    <?php if (has_post_thumbnail($post_id)) : ?>
                                                        <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'blog-listing__card-image card-img-left example-card-img-responsive')); ?>
                                                    <?php else : ?>
                                                        <span class="blog-listing__card-image blog-listing__card-image--placeholder card-img-left example-card-img-responsive"></span>
                                                    <?php endif; ?>
                                                </a>

                                                <div class="blog-listing__card-content card-body d-flex flex-column justify-content-center">
                                                    <?php if ($post_term_name) : ?>
                                                        <p class="blog-listing__card-taxonomy card-text"><?php echo esc_html($post_term_name); ?></p>
                                                    <?php endif; ?>

                                                    <h3 class="blog-listing__card-title card-title h5 h4-sm">
                                                        <a href="<?php echo esc_url($post_permalink); ?>">
                                                            <?php echo esc_html($post_title); ?>
                                                        </a>
                                                    </h3>

                                                    <a href="<?php echo esc_url($post_permalink); ?>" class="blog-listing__card-link card-text">
                                                        <?php pll_e('Read more'); ?>
                                                    </a>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="blog-listing__empty">
                        <p><?php pll_e('No posts were found for this category.'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (! empty($featured_swiper_posts)) : ?>
                <div class="row g-0 blog-listing__featured-row">
                    <div class="col-12">
                        <div class="blog-listing__featured-swiper-block">
                            <div class="blog-listing__featured-swiper swiper" data-blog-listing-featured-swiper>
                                <div class="swiper-wrapper">
                                    <?php foreach ($featured_swiper_posts as $featured_post) : ?>
                                        <?php
                                        $featured_post_id    = (int) $featured_post->ID;
                                        $featured_title      = get_the_title($featured_post_id);
                                        $featured_permalink  = get_permalink($featured_post_id);
                                        $featured_terms      = get_the_terms($featured_post_id, 'category');
                                        $featured_term_name  = (! is_wp_error($featured_terms) && ! empty($featured_terms)) ? $featured_terms[0]->name : '';
                                        ?>
                                        <div class="swiper-slide blog-listing__featured-slide">
                                            <article class="blog-listing__swiper flex-column">
                                                <div class="blog-listing__swiper-media" aria-label="<?php echo esc_attr($featured_title); ?>">
                                                    <?php if (has_post_thumbnail($featured_post_id)) : ?>
                                                        <?php echo get_the_post_thumbnail($featured_post_id, 'large', array('class' => 'blog-listing__card-image card-img-left example-card-img-responsive')); ?>
                                                    <?php else : ?>
                                                        <span class="blog-listing__swiper-image blog-listing__card-image--placeholder card-img-left example-card-img-responsive"></span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="blog-listing__swiper-content card-body d-flex flex-column justify-content-center">
                                                    <h3 class="blog-listing__card-title card-title h5 h4-sm">
                                                        <a href="<?php echo esc_url($featured_permalink); ?>">
                                                            <?php echo esc_html($featured_title); ?>
                                                        </a>
                                                    </h3>

                                                    <a href="<?php echo esc_url($featured_permalink); ?>" class="blog-listing__card-link card-text">
                                                        <?php pll_e('Read more'); ?>
                                                    </a>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-0 blog-listing__remaining-row">
                <div class="col-12">
                    <?php if (! empty($remaining_posts)) : ?>
                        <div class="blog-listing__grid row g-0" data-blog-listing-more-grid>
                            <?php foreach ($remaining_posts as $listing_post) : ?>
                                <?php
                                $post_id        = (int) $listing_post->ID;
                                $post_title     = get_the_title($post_id);
                                $post_permalink = get_permalink($post_id);
                                $post_terms     = get_the_terms($post_id, 'category');
                                $post_term_name = (! is_wp_error($post_terms) && ! empty($post_terms)) ? $post_terms[0]->name : '';
                                ?>
                                <div class="col-12 col-lg-6 mb-4 mb-lg-5" data-blog-listing-grid-item>
                                    <article class="blog-listing__card card">
                                        <a href="<?php echo esc_url($post_permalink); ?>" class="blog-listing__card-media-link" aria-label="<?php echo esc_attr($post_title); ?>">
                                            <?php if (has_post_thumbnail($post_id)) : ?>
                                                <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'blog-listing__card-image card-img-left example-card-img-responsive')); ?>
                                            <?php else : ?>
                                                <span class="blog-listing__card-image blog-listing__card-image--placeholder card-img-left example-card-img-responsive"></span>
                                            <?php endif; ?>
                                        </a>
                                        <div class="blog-listing__card-content card-body d-flex flex-column justify-content-center">
                                            <?php if ($post_term_name) : ?>
                                                <p class="blog-listing__card-taxonomy card-text"><?php echo esc_html($post_term_name); ?></p>
                                            <?php endif; ?>
                                            <h3 class="blog-listing__card-title card-title h5 h4-sm">
                                                <a href="<?php echo esc_url($post_permalink); ?>"><?php echo esc_html($post_title); ?></a>
                                            </h3>
                                            <a href="<?php echo esc_url($post_permalink); ?>" class="blog-listing__card-link card-text"><?php pll_e('Read more'); ?></a>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_more_posts) : ?>
                        <div class="blog-listing__actions">
                            <button
                                type="button"
                                class="blog-listing__load-more"
                                data-blog-listing-load-more
                                data-page="<?php echo esc_attr($load_more_page); ?>"
                                data-exclude="<?php echo esc_attr($load_more_exclude); ?>"
                                data-category="<?php echo esc_attr($load_more_category); ?>"
                                data-lang="<?php echo esc_attr($load_more_lang); ?>"
                                data-nonce="<?php echo esc_attr(wp_create_nonce('pm_load_more')); ?>"
                            >
                                <?php pll_e('Load more'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
</section>
