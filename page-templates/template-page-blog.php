<?php
get_header();

$blog_page_id  = get_queried_object_id();
$blog_settings = get_field('blog_page_settings', $blog_page_id);

?>

    <main id="primary" class="site-main site-main--blog">
        <?php
        get_template_part('template-parts/blog/blog-hero', null, array(
            'blog_page_id'  => $blog_page_id,
            'blog_settings' => $blog_settings,
        ));

//        get_template_part('template-parts/blog/content-listing', null, array(
//            'blog_page_id'  => $blog_page_id,
//            'blog_settings' => $blog_settings,
//        ));
//
//        get_template_part('template-parts/blog/newsletter-subscribe-banner', null, array(
//            'blog_page_id'  => $blog_page_id,
//            'blog_settings' => $blog_settings,
//        ));
        ?>
    </main>

<?php get_footer(); ?>