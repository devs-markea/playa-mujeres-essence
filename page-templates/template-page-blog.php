<?php
/**
 * Template Name: Página de Blog ACF
 */
get_header();

$blog_page_id  = get_queried_object_id();
$blog_settings = function_exists('pm_essence_get_blog_settings')
    ? pm_essence_get_blog_settings('blog')
    : array();

// Query centralizado de los 4 posts más recientes para el hero.
// Los IDs se pasan a content-listing para excluirlos del listing.
$hero_query = new WP_Query( array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'ignore_sticky_posts' => true,
) );
$hero_post_ids = $hero_query->have_posts()
    ? array_map( function( $p ) { return (int) $p->ID; }, $hero_query->posts )
    : array();
wp_reset_postdata();

?>

    <main id="primary" class="site-main site-main--blog" data-force-header-theme="menu">
        <?php
        get_template_part('template-parts/blog/blog-hero', null, array(
            'blog_page_id'  => $blog_page_id,
            'blog_settings' => $blog_settings,
            'hero_post_ids' => $hero_post_ids,
        ));

        get_template_part('template-parts/blog/content-listing', null, array(
            'blog_page_id'   => $blog_page_id,
            'blog_settings'  => $blog_settings,
            'listing_context' => 'blog',
            'hero_post_ids'  => $hero_post_ids,
        ));

        get_template_part('template-parts/blog/newsletter-subscribe-banner', null, array(
            'blog_page_id'  => $blog_page_id,
            'blog_settings' => $blog_settings,
        ));
        ?>
    </main>

<?php get_footer(); ?>
