<?php
/**
 * Template Name: Página de Blog ACF
 */
get_header();

$blog_page_id  = get_queried_object_id();
$blog_settings = function_exists('pm_essence_get_blog_settings')
    ? pm_essence_get_blog_settings('blog')
    : array();

?>

    <main id="primary" class="site-main site-main--blog" data-force-header-theme="menu">
        <?php
        get_template_part('template-parts/blog/blog-hero', null, array(
            'blog_page_id'  => $blog_page_id,
            'blog_settings' => $blog_settings,
        ));

        get_template_part('template-parts/blog/content-listing', null, array(
            'blog_page_id'  => $blog_page_id,
            'blog_settings' => $blog_settings,
            'listing_context' => 'blog',
        ));

        get_template_part('template-parts/blog/newsletter-subscribe-banner', null, array(
            'blog_page_id'  => $blog_page_id,
            'blog_settings' => $blog_settings,
        ));
        ?>
    </main>

<?php get_footer(); ?>
