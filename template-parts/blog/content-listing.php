<?php
$args = wp_parse_args($args ?? array(), array(
    'blog_page_id'  => 0,
    'blog_settings' => array(),
));

$blog_page_id  = (int) $args['blog_page_id'];
$blog_settings = is_array($args['blog_settings']) ? $args['blog_settings'] : array();

$listing_heading       = isset($blog_settings['heading_listing']) ? $blog_settings['heading_listing'] : '';
$listing_heading_level = isset($blog_settings['heading_level_listing']) ? $blog_settings['heading_level_listing'] : 'h2';
$listing_description   = isset($blog_settings['description_listing']) ? $blog_settings['description_listing'] : '';

$display_categories_filter = ! empty($blog_settings['display_categories_filter']);
$display_featured_posts    = ! empty($blog_settings['display_featured_posts']);
$display_newsletter_form   = ! empty($blog_settings['display_newsletter_form']);

$newsletter_heading       = isset($blog_settings['sidebar_heading']) ? $blog_settings['sidebar_heading'] : '';
$newsletter_heading_level = isset($blog_settings['sidebar_heading_level']) ? $blog_settings['sidebar_heading_level'] : 'h3';
$newsletter_description   = isset($blog_settings['sidebar_description']) ? $blog_settings['sidebar_description'] : '';
$newsletter_placeholder   = isset($blog_settings['placeholder']) ? $blog_settings['placeholder'] : '';
$newsletter_submit_text   = isset($blog_settings['submit_text']) ? $blog_settings['submit_text'] : '';

$enable_load_more    = ! empty($blog_settings['enable_load_more']);
$posts_per_page      = isset($blog_settings['posts_per_page']) ? (int) $blog_settings['posts_per_page'] : 8;
$posts_per_page      = $posts_per_page > 0 ? $posts_per_page : 8;
$load_more_text      = isset($blog_settings['button_text']) ? $blog_settings['button_text'] : '';

$listing_heading_tag    = function_exists('pm_essence_heading_tag_or_null') ? pm_essence_heading_tag_or_null($listing_heading_level, 'h2') : 'h2';
$newsletter_heading_tag = function_exists('pm_essence_heading_tag_or_null') ? pm_essence_heading_tag_or_null($newsletter_heading_level, 'h3') : 'h3';

$selected_category_slug = isset($_GET['blog_category']) ? sanitize_title(wp_unslash($_GET['blog_category'])) : '';
$selected_category      = $selected_category_slug ? get_term_by('slug', $selected_category_slug, 'category') : false;

if ($selected_category && is_wp_error($selected_category)) {
    $selected_category      = false;
    $selected_category_slug = '';
}

$categories = get_categories(array(
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
));

$listing_query_args = array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => -1,
    'ignore_sticky_posts' => true,
);

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

$initial_visible_posts = min($posts_per_page, count($listing_posts));
$primary_visible_limit = min(8, $initial_visible_posts);
$secondary_visible_posts = max(0, $initial_visible_posts - $primary_visible_limit);
$load_more_start_index = $initial_visible_posts;
$has_hidden_listing_posts = $enable_load_more && count($listing_posts) > $load_more_start_index;

$first_listing_posts = array_slice($listing_posts, 0, $primary_visible_limit);
$remaining_posts     = array_slice($listing_posts, $primary_visible_limit);

if (! $enable_load_more) {
    $remaining_posts = array_slice($remaining_posts, 0, $secondary_visible_posts);
}

$sidebar_featured_posts = array();
$featured_swiper_posts  = array();
$sticky_posts           = array_map('absint', (array) get_option('sticky_posts'));

$featured_base_query_args = array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'ignore_sticky_posts' => false,
);

if ($selected_category instanceof WP_Term) {
    $featured_base_query_args['tax_query'] = array(
        array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $selected_category->term_id,
        ),
    );
}

if (! empty($sticky_posts)) {
    $featured_base_query_args['post__in'] = $sticky_posts;
    $featured_base_query_args['orderby']  = 'post__in';
}

$featured_swiper_query = new WP_Query($featured_base_query_args);

if ($featured_swiper_query->have_posts()) {
    $featured_swiper_posts = $featured_swiper_query->posts;
} elseif (empty($sticky_posts)) {
    $featured_swiper_fallback_args = $featured_base_query_args;
    $featured_swiper_fallback_args['ignore_sticky_posts'] = true;
    unset($featured_swiper_fallback_args['post__in'], $featured_swiper_fallback_args['orderby']);

    $featured_swiper_fallback_query = new WP_Query($featured_swiper_fallback_args);
    $featured_swiper_posts          = $featured_swiper_fallback_query->have_posts() ? $featured_swiper_fallback_query->posts : array();
    wp_reset_postdata();
}

wp_reset_postdata();

if ($display_featured_posts) {
    $sidebar_featured_posts = $featured_swiper_posts;
}

$has_categories_sidebar = $display_categories_filter && ! empty($categories);
$has_top_posts_sidebar  = $display_featured_posts && ! empty($sidebar_featured_posts);
$has_newsletter_sidebar = $display_newsletter_form && ($newsletter_heading || $newsletter_description || $newsletter_submit_text);

$sticky_sidebar_target = '';

if ($has_categories_sidebar) {
    $sticky_sidebar_target = 'categories';
} elseif ($has_top_posts_sidebar) {
    $sticky_sidebar_target = 'top-posts';
} elseif ($has_newsletter_sidebar) {
    $sticky_sidebar_target = 'newsletter';
}

$has_listing_content = $listing_heading || $listing_description || ! empty($listing_posts) || ! empty($featured_swiper_posts) || ! empty($sidebar_featured_posts) || $has_newsletter_sidebar;

if (! $has_listing_content) {
    return;
}


?>

<section class="blog-listing container" data-blog-listing-root>
    <div class="row g-0">
        <div class="col-10 mx-auto">
            <?php if ($listing_heading || $listing_description) : ?>
            <div class="blog-listing__intro row g-0">
                <div class="col-md-12">
                    <?php if ($listing_heading && $listing_heading_tag) : ?>
                    <<?php echo tag_escape($listing_heading_tag); ?> class="blog-listing__heading">
                    <?php echo esc_html($listing_heading); ?>
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
            <div class="col-md-3">
                <aside class="blog-listing__sidebar<?php echo $sticky_sidebar_target ? ' blog-listing__sidebar--sticky blog-listing__sidebar--sticky-' . esc_attr($sticky_sidebar_target) : ''; ?>">
                    <?php if ($has_categories_sidebar) : ?>
                        <div class="blog-listing__sidebar-block blog-listing__sidebar-block--categories">
                            <h3 class="blog-listing__sidebar-title">Categories</h3>
                            <ul class="blog-listing__categories">
                                <?php foreach ($categories as $category) : ?>
                                    <?php
                                    $is_active = $selected_category instanceof WP_Term && (int) $selected_category->term_id === (int) $category->term_id;
                                    $url       = remove_query_arg('blog_category', get_permalink($blog_page_id ?: get_the_ID()));
                                    $url       = add_query_arg('blog_category', $category->slug, $url);
                                    ?>
                                    <li class="blog-listing__categories-item<?php echo $is_active ? ' is-active' : ''; ?>">
                                        <a href="<?php echo esc_url($url); ?>" class="blog-listing__categories-link">
                                            <span><?php echo esc_html($category->name); ?></span>
                                            <?php if ($is_active) : ?>
                                                <span class="blog-listing__categories-arrow" aria-hidden="true">&rarr;</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_top_posts_sidebar) : ?>
                        <div class="blog-listing__sidebar-block blog-listing__sidebar-block--panel blog-listing__sidebar-block--top-posts">
                            <h3 class="blog-listing__sidebar-title">Top Posts</h3>
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

                    <?php if ($has_newsletter_sidebar) : ?>
                    <div class="blog-listing__sidebar-block blog-listing__sidebar-block--newsletter">
                        <?php if ($newsletter_heading && $newsletter_heading_tag) : ?>
                        <<?php echo tag_escape($newsletter_heading_tag); ?> class="blog-listing__sidebar-title">
                        <?php echo esc_html($newsletter_heading); ?>
                    </<?php echo tag_escape($newsletter_heading_tag); ?>>
                <?php endif; ?>

                    <?php if ($newsletter_description) : ?>
                        <div class="blog-listing__newsletter-copy">
                            <?php echo wp_kses_post($newsletter_description); ?>
                        </div>
                    <?php endif; ?>

                    <form class="blog-listing__newsletter newsletter-subscribe-banner__form">
                        <input
                                type="email"
                                class="blog-listing__newsletter-input newsletter-subscribe-banner__input"
                                placeholder="<?php echo esc_attr($newsletter_placeholder ?: 'Type your email address'); ?>"
                                required
                        >
                        <button
                                type="submit"
                                class="blog-listing__newsletter-submit arrow-circle__link"
                        >
                            <span class="arrow-circle__label"><?php echo esc_html($newsletter_submit_text ?: 'Subscribe'); ?></span>
                            <span class="arrow-circle__icon">
                                    <span class="arrow">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.25 15.75L21 12M21 12L17.25 8.25M21 12H3" stroke="currentColor" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="circle">
                                        <svg width="28" height="30" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.375" y="0.375" width="27.25" height="27.25" rx="13.625" stroke="currentColor" stroke-width="0.75"/>
                                        </svg>
                                    </span>
                                </span>
                        </button>
                    </form>
            </div>
            <?php endif; ?>
                </aside>
            </div>
            <div class="col-md-9">
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
                                        $is_hidden      = false;
                                        ?>
                                        <div class="col-12 col-lg-6 mb-4 mb-lg-5" data-blog-listing-grid-item>
                                            <article class="blog-listing__card card flex-row<?php echo $is_hidden ? ' is-hidden' : ''; ?>" <?php echo $is_hidden ? 'data-blog-listing-hidden="true"' : ''; ?>>
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
                                                        Read more
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
                        <p>No posts were found for this category.</p>
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
                                                        Read more
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

            <?php if (! empty($remaining_posts)) : ?>
                <div class="row g-0 blog-listing__remaining-row">
                    <div class="col-12">
                        <div class="blog-listing__grid row g-0" data-blog-listing-grid>
                            <?php foreach ($remaining_posts as $index => $listing_post) : ?>
                                <?php
                                $post_id        = (int) $listing_post->ID;
                                $post_title     = get_the_title($post_id);
                                $post_permalink = get_permalink($post_id);
                                $post_terms     = get_the_terms($post_id, 'category');
                                $post_term_name = (! is_wp_error($post_terms) && ! empty($post_terms)) ? $post_terms[0]->name : '';
                                $absolute_index = $primary_visible_limit + $index;
                                $is_hidden      = $has_hidden_listing_posts && $absolute_index >= $load_more_start_index;
                                ?>
                                <div class="col-12 col-lg-6 mb-4 mb-lg-5<?php echo $is_hidden ? ' is-hidden' : ''; ?>" data-blog-listing-grid-item <?php echo $is_hidden ? 'data-blog-listing-hidden="true"' : ''; ?>>
                                    <article class="blog-listing__card card flex-row<?php echo $is_hidden ? ' is-hidden' : ''; ?>" <?php echo $is_hidden ? 'data-blog-listing-hidden="true"' : ''; ?>>
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
                                                Read more
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($has_hidden_listing_posts) : ?>
                            <div class="blog-listing__actions">
                                <button
                                        type="button"
                                        class="blog-listing__load-more"
                                        data-blog-listing-load-more
                                        data-batch-size="<?php echo esc_attr($posts_per_page); ?>"
                                        data-blog-listing-trigger="items"
                                >
                                    <?php echo esc_html($load_more_text ?: 'Load more'); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    </div>
    </div>
</section>

<style>

    .blog-listing__featured-row {
        position: relative;
        min-height: 500px;
    }
    .blog-listing {
        padding: 56px 0 72px;
    }

    .blog-listing__intro {
        margin-bottom: 36px;
    }

    .blog-listing__heading {
        color: var(--pm-secondary-900);
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 0;
        padding: 0 12px;
    }

    .blog-listing__description {
        margin-top: 16px;
        color: var(--pm-secondary-900);
        font-size: 14px;
        line-height: 1.8;
    }

    .blog-listing__sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .blog-listing__sidebar-block {
        padding: 16px 12px;
    }

    .blog-listing__sidebar-block--panel {
        padding: 16px 12px;
        background: #FBFAF7;
    }

    .blog-listing__sidebar-title {
        margin: 0 0 18px;
        color: black;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .blog-listing__categories,
    .blog-listing__top-posts {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .blog-listing__top-posts .blog-listing__top-posts-item:last-child {
        border-bottom: 0;
    }

    .blog-listing__top-posts .blog-listing__top-posts-item:first-child {
        border-bottom: 1px solid black;
    }

    .blog-listing__top-posts-item {
        border-bottom: 1px solid rgba(50, 50, 50, 0.18);
    }

    .blog-listing__top-posts-link {
        padding: 12px 0 !important;
    }

    .blog-listing__top-posts .blog-listing__top-posts-item:first-child .blog-listing__top-posts-link {
        font-weight: 400;
    }

    .blog-listing__categories-link,
    .blog-listing__top-posts-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        font-size: 16px;
        font-style: normal;
        font-weight: 300;
        line-height: normal;
        color: black;
        text-decoration: none;
        transition: opacity 0.2s ease;
    }


    .blog-listing__categories-item.is-active .blog-listing__categories-link {
        font-weight: 400;
    }

    .blog-listing__categories-arrow {
        font-size: 18px;
        line-height: 1;
    }

    .blog-listing__newsletter-copy {
        margin-bottom: 18px;
        color: black;
        font-size: 16px;
        font-style: normal;
        font-weight: 300;
        line-height: normal;
    }

    .blog-listing__newsletter {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .blog-listing__newsletter-input {
        width: 100%;
        min-height: 52px;
        padding: 12px 4px;
        color: #323232;
        background: transparent;
        border: 0;
        border-bottom: 1px solid rgba(50, 50, 50, 0.18);
        border-radius: 0;
        box-shadow: none;
        font-size: 16px;
        font-style: normal;
        font-weight: 300;
        line-height: normal;
    }

    .blog-listing__newsletter-input::placeholder {
        color: #A5A5A5;
    }

    .blog-listing__newsletter-submit {
        color: black;
    }

    .blog-listing__content {
        min-width: 0;
    }

    .blog-listing__featured-swiper-block {
        position: absolute;
        left: 0;
        right: calc(-50vw + 69%);
        margin-top: 8px;
        margin-bottom: 40px;
        overflow: hidden;
    }

    .blog-listing__featured-swiper {
        overflow: visible;
    }

    .blog-listing__featured-slide {
        width: min(44vw, 440px);
        height: auto;
    }

    .blog-listing__card {
        height: 168px;
        border: 0;
        border-radius: 0;
        background: transparent;
        align-items: stretch;
    }

    .blog-listing__card.is-hidden {
        display: none;
    }

    [data-blog-listing-grid-item].is-hidden {
        display: none;
    }

    .blog-listing__card-media-link {
        display: block;
        flex: 0 0 50%;
        max-width: 50%;
        overflow: hidden;
        background: #e6e1d8;
        height: 100%;
        text-decoration: none;
    }

    .blog-listing__card-image,
    .blog-listing__card-image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .blog-listing__card-image--placeholder {
        background: linear-gradient(135deg, #d8d0c4 0%, #f0eae0 100%);
    }

    .blog-listing__card-content {
        width: 100%;
        flex: 1 1 auto;
        padding: 8px 24px;
    }

    .blog-listing__card-taxonomy {
        font-family: var(--pm-font-secondary);
        color: var(--pm-secondary-900);
        font-size: 14px;
        font-style: italic;
        font-weight: 500;
        line-height: 28px; /* 200% */
        letter-spacing: 1px;
    }

    .blog-listing__card-title {
        color: var(--pm-secondary-900);
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .blog-listing__card-title a {
        color: inherit;
        text-decoration: none;
    }

    .blog-listing__card-link {
        display: inline-block;
        color: #323232;
        font-size: 16px;
        text-decoration: none;
        border-bottom: 1px solid currentColor;
        align-self: flex-start;
    }

    .blog-listing__actions {
        margin-top: 32px;
        text-align: center;
    }

    .blog-listing__load-more {
        min-width: 180px;
        padding: 14px 28px;
        color: #fff;
        background: #323232;
        border: 0;
        border-radius: 999px;
        transition: opacity 0.2s ease;
    }

    .blog-listing__load-more:hover {
        opacity: 0.85;
    }

    .blog-listing__empty {
        padding: 24px;
        background: rgba(255, 255, 255, 0.45);
        color: #323232;
    }

    .newsletter-subscribe-banner--blog {
        padding-bottom: 72px;
        background: #f7f4ef;
    }

    @media (min-width: 992px) {
        .blog-listing {
            padding-top: 72px;
        }

        .blog-listing__sidebar--sticky {
            position: sticky;
            top: 32px;
        }
    }

    @media (max-width: 991.98px) {
        .blog-listing__heading {
            font-size: 26px;
        }

        .blog-listing__sidebar-title {
            font-size: 20px;
        }

        .blog-listing__card-title {
            font-size: 24px;
        }

        .blog-listing__card-content {
            padding: 20px 0 0;
        }

        .blog-listing__card-taxonomy {
            margin-bottom: 14px;
            font-size: 16px;
        }

        .blog-listing__card-title {
            margin-bottom: 18px;
        }

        .blog-listing__featured-slide {
            width: min(72vw, 440px);
        }
    }

    @media (max-width: 767.98px) {
        .blog-listing__card {
            flex-direction: column;
        }

        .blog-listing__card-media-link {
            flex-basis: auto;
            max-width: 100%;
        }

        .blog-listing__featured-slide {
            width: 84%;
        }

    }

    .blog-listing__swiper {
        position: relative;
        height: 450px;
    }
    .blog-listing__swiper-media{
        height: 100%;
    }
    .blog-listing__swiper-content.card-body{
        position: absolute;
        padding: 1rem;
        width: 90%;
        z-index: 20;
        bottom: 0;
        background: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Swiper !== 'undefined') {
            var featuredSwipers = document.querySelectorAll('[data-blog-listing-featured-swiper]');

            featuredSwipers.forEach(function (el) {
                if (el.dataset.swiperInitialized === '1') {
                    return;
                }

                el.dataset.swiperInitialized = '1';

                new window.Swiper(el, {
                    slidesPerView: '2.5',
                    spaceBetween: 40,
                    speed: 650,
                    grabCursor: true,
                    watchOverflow: true,
                    breakpoints: {
                        0: {
                            spaceBetween: 18
                        },
                        992: {
                            spaceBetween: 24
                        }
                    }
                });
            });
        }


        var listingRoots = document.querySelectorAll('[data-blog-listing-root]');
        if (!listingRoots.length) {
            return;
        }

        listingRoots.forEach(function (listingRoot) {
            var button = listingRoot.querySelector('[data-blog-listing-load-more][data-blog-listing-trigger="items"]');
            var itemsGrid = listingRoot.querySelector('[data-blog-listing-items]');

            if (!button || !itemsGrid) {
                return;
            }

            function getHiddenCards() {
                return itemsGrid.querySelectorAll('[data-blog-listing-grid-item][data-blog-listing-hidden="true"]');
            }

            function updateButtonVisibility() {
                button.style.display = getHiddenCards().length ? '' : 'none';
            }

            updateButtonVisibility();

            button.addEventListener('click', function () {
                var batchSize = parseInt(button.getAttribute('data-batch-size') || '8', 10);
                var hiddenCards = getHiddenCards();
                var revealed = 0;

                hiddenCards.forEach(function (card) {
                    if (revealed >= batchSize) {
                        return;
                    }

                    var article = card.querySelector('.blog-listing__card');

                    card.classList.remove('is-hidden');
                    card.removeAttribute('data-blog-listing-hidden');

                    if (article) {
                        article.classList.remove('is-hidden');
                        article.removeAttribute('data-blog-listing-hidden');
                    }

                    revealed += 1;
                });

                updateButtonVisibility();
            });
        });
    });
</script>
