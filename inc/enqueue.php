<?php

function pm_enqueue_assets() {
    global $pm_essence_version;


    /* ---------------------------------
     *  CORE: JQuery + Bootstrap
     * --------------------------------- */

    // WordPress jQuery
    wp_enqueue_script( 'jquery' );

    // Bootstrap CSS
    wp_enqueue_style(
        'pm-bootstrap-css',
        PM_ESSENCE_TEMPLATE_URI . '/assets/libs/bootstrap/bootstrap.min.css',
        array(),
        '5.3.3',
        'all'
    );

    // Lightbox
    wp_enqueue_style('pm-light-box2-css', PM_ESSENCE_TEMPLATE_URI . '/assets/plugins/lightbox2/css/lightbox2.css',
    array(),
    $pm_essence_version,
    'all'
    );

    // Lightbox JS
    wp_enqueue_script('pm-light-box2-js',
        PM_ESSENCE_TEMPLATE_URI . '/assets/plugins/lightbox2/js/lightbox2.js',
        array('jquery'),
        $pm_essence_version,
        true);

    // Bootstrap JS
    wp_enqueue_script(
        'pm-bootstrap-js',
        PM_ESSENCE_TEMPLATE_URI . '/assets/libs/bootstrap/bootstrap.min.js',
        array( 'jquery' ),
        '5.3.3',
        true
    );

    /* ---------------------------------
     *  GLOBAL STYLES
     * --------------------------------- */

    wp_enqueue_style(
        'essence-components',
        PM_ESSENCE_TEMPLATE_URI . '/assets/css/components.css',
        array( 'essence-mainstyles' ),
        $pm_essence_version,
        'all'
    );

    wp_enqueue_style(
        'essence-utilities',
        PM_ESSENCE_TEMPLATE_URI . '/assets/css/utilities.css',
        array( 'essence-components' ),
        $pm_essence_version,
        'all'
    );

    /* ---------------------------------
     *  PAGE-SPECIFIC: Front Page
     * --------------------------------- */
    if ( is_front_page() ) {
        wp_enqueue_style(
            'pm-front-page-css',
            PM_ESSENCE_TEMPLATE_URI . '/assets/css/pages/homepage.css',
            array( 'essence-utilities' ),
            $pm_essence_version,
            'all'
        );

    }


    /* ---------------------------------
     *  PAGE-SPECIFIC: Gallery
     * --------------------------------- */

    if ( is_page( 'gallery' ) ) {
        wp_enqueue_style(
            'custom-gallery-style',
            PM_ESSENCE_TEMPLATE_URI . '/assets/css/custom-gallery.css',
            array(),
            $pm_essence_version,
            'all'
        );
        wp_enqueue_script('lightbox2-init', PM_ESSENCE_TEMPLATE_URI . '/assets/js/custom-gallery-init.js',
            ['pm-light-box2-js', 'jquery']);
    }

    /* ---------------------------------
     *  OPTIONAL: Dashicons (solo cuando hace falta)
     * --------------------------------- */
    if ( is_user_logged_in() ) {
        wp_enqueue_style( 'dashicons' );
    }

    /* ---------------------------------
         *  Swiper (registrar y cargar)
         * --------------------------------- */
    wp_register_style(
        'pm-swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        '11.0.0',
        'all'
    );
    wp_register_script(
        'pm-swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array(),
        '11.0.0',
        true
    );

    // Mejor: que el JS no bloquee el parseo (WP soporta strategy en versiones modernas).
    if ( function_exists('wp_script_add_data') ) {
        wp_script_add_data('pm-swiper', 'strategy', 'defer');
    }

    /**
     * Por defecto: TRUE (porque indicas que Swiper se usa en todas las páginas).
     * Más adelante puedes cambiarlo a detección real por ACF o por template.
     */
    $load_swiper = true;
    $load_swiper = apply_filters('pm_essence_should_load_swiper', $load_swiper);

    if ( $load_swiper ) {
        wp_enqueue_style('pm-swiper');
        wp_enqueue_script('pm-swiper');
        wp_enqueue_script('pm-gsap');
        wp_enqueue_script('pm-gsap-st');
    }

    // GSAP
    wp_register_script(
        'pm-gsap',
        'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js',
        array(),
        '3.14.1',
        true
    );
    wp_register_script(
        'pm-gsap-st',
        'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/ScrollTrigger.min.js',
        array('pm-gsap'),
        '3.14.1',
        true
    );

    // reCAPTCHA (NO lo cargues globalmente; solo en páginas con formularios)
    $recaptcha_site_key = (string) get_theme_mod('pm_recaptcha_site_key', '');
    if ( $recaptcha_site_key !== '' ) {
        wp_register_script(
            'pm-recaptcha',
            'https://www.google.com/recaptcha/api.js?render=' . rawurlencode($recaptcha_site_key),
            array(),
            null,
            true
        );
    }

    /* ---------------------------------
     *  SPLIDE
     * --------------------------------- */
    /*
    wp_enqueue_style(
        'pm-splide-css',
        PM_ESSENCE_TEMPLATE_URI . '/assets/libs/splide-4.1.3/css/splide.min.css',
        array(),
        '4.1.3',
        'all'
    );

    wp_enqueue_script(
        'pm-splide-js',
        PM_ESSENCE_TEMPLATE_URI . '/assets/libs/splide-4.1.3/js/splide.min.js',
        array(),
        '4.1.3',
        true
    );
    */
}


add_action( 'wp_enqueue_scripts', 'pm_enqueue_assets', 20 );

