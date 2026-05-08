<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package pm-essence
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$image_id = get_theme_mod( 'pm_404_image' );
if ( $image_id ) {
    $image_url  = wp_get_attachment_image_url( $image_id, 'large' );
    $image_srcset = wp_get_attachment_image_srcset( $image_id, 'large' );
} else {
    $image_url    = get_template_directory_uri() . '/assets/images/404-beach.jpg';
    $image_srcset = 'https://playamujeres.com.mx/wp-content/themes/playamujeres/assets/img/sections/home/playa-mujeres-hotels.webp';
}
?>

<main class="page-404" data-force-header-theme="menu">
    <div class="page-404__content">
        <span class="page-404__badge"><?php esc_html_e( 'Error 404', 'pm-essence' ); ?></span>

        <p class="page-404__number" aria-hidden="true">404</p>

        <h1 class="page-404__title">
            <?php esc_html_e( 'Oops, looks like you followed the wrong path to the sea!', 'pm-essence' ); ?>
        </h1>

        <p class="page-404__description">
            <?php esc_html_e( 'Please go back to our navigation bar and find all you need to know about Playa Mujeres', 'pm-essence' ); ?>
        </p>

        <a class="page-404__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php esc_html_e( 'Return to home', 'pm-essence' ); ?>
        </a>
    </div>

    <div class="page-404__media" role="presentation">
        <?php if ( $image_url ) : ?>
            <img
                class="page-404__image"
                src="<?php echo esc_url( $image_url ); ?>"
                <?php if ( $image_srcset ) : ?>
                    srcset="<?php echo esc_attr( $image_srcset ); ?>"
                    sizes="50vw"
                <?php endif; ?>
                alt=""
                loading="eager"
                fetchpriority="high"
            >
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
