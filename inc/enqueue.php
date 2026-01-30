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

    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], null);
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);

    wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js?render=6LeGNlgsAAAAAHc_b3oI50c6z0qJf5WNrNOrpY3_', [], null, true);

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

