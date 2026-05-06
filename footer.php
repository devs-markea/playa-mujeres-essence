<?php
/**
 * The template for displaying the footer
 *
 * @package pm-essence
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<footer class="site-footer">
    <?php get_template_part( 'template-parts/footer/footer', 'default' ); ?>
</footer>
<?php get_template_part('inc/components/ui/to-top'); ?>
<?php get_template_part('template-parts/menu/filters/gastronomy/filter-by-hotel'); ?>

<?php wp_footer(); ?>

</body>
</html>
