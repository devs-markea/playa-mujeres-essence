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

    // Lightbox — en gallery page, hotels_gallery o images_carousel (gallery-slider variant)
    if ( is_page( 'gallery' ) || pm_page_has_section_layout( 'hotels_gallery' ) || pm_page_has_section_layout( 'images_carousel' ) ) {
        wp_enqueue_style('pm-light-box2-css', PM_ESSENCE_TEMPLATE_URI . '/assets/plugins/lightbox2/css/lightbox2.css',
            array(),
            $pm_essence_version,
            'all'
        );
        wp_enqueue_script('pm-light-box2-js',
            PM_ESSENCE_TEMPLATE_URI . '/assets/plugins/lightbox2/js/lightbox2.js',
            array('jquery'),
            $pm_essence_version,
            true);
    }

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
        array( 'pm-bootstrap-css' ), // sin dependencia de essence-mainstyles — descarga en paralelo
        $pm_essence_version,
        'all'
    );


    /* ---------------------------------
     *  PAGE-SPECIFIC: Blog
     * --------------------------------- */

    if ( is_home() || is_category() || is_singular( 'post' ) || is_page_template( 'page-templates/template-page-blog.php' ) ) {
        wp_enqueue_style(
            'pm-blog',
            PM_ESSENCE_TEMPLATE_URI . '/assets/css/pages/blog.css',
            array( 'essence-components', 'pm-swiper' ),
            $pm_essence_version,
            'all'
        );
    }

    /* ---------------------------------
     *  PAGE-SPECIFIC: Gallery
     * --------------------------------- */

    if ( is_page( 'gallery' ) || pm_page_has_section_layout( 'hotels_gallery' ) || pm_page_has_section_layout( 'images_carousel' ) ) {
        wp_enqueue_style(
            'custom-gallery-style',
            PM_ESSENCE_TEMPLATE_URI . '/assets/css/custom-gallery.css',
            array( 'pm-light-box2-css' ),
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
        null, // null → no ?ver=; la versión ya está fijada en la URL del CDN
        'all'
    );
    wp_register_script(
        'pm-swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array(),
        null, // null → no ?ver=; la versión ya está fijada en la URL del CDN
        true
    );

    // Mejor: que el JS no bloquee el parseo (WP soporta strategy en versiones modernas).
    if ( function_exists('wp_script_add_data') ) {
        wp_script_add_data('pm-swiper', 'strategy', 'defer');
    }

    // Layouts ACF que usan Swiper — añadir aquí si se incorporan nuevos.
    $swiper_layouts = [ 'content_showcase', 'content_carousel', 'images_carousel' ];
    $load_swiper    = is_home() || is_category() || is_singular( 'post' ) || is_page_template( 'page-templates/template-page-blog.php' );
    if ( ! $load_swiper ) {
        foreach ( $swiper_layouts as $_layout ) {
            if ( pm_page_has_section_layout( $_layout ) ) {
                $load_swiper = true;
                break;
            }
        }
    }
    $load_swiper = apply_filters( 'pm_essence_should_load_swiper', $load_swiper );

    // GSAP — siempre se carga: lo usan initPageCoverReveal e initFadeAnimations en todas las páginas
    wp_register_script(
        'pm-gsap',
        'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js',
        array(),
        null,
        true
    );
    wp_register_script(
        'pm-gsap-st',
        'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/ScrollTrigger.min.js',
        array('pm-gsap'),
        null,
        true
    );
    wp_enqueue_script('pm-gsap');
    wp_enqueue_script('pm-gsap-st');

    // Swiper — solo en páginas que lo necesitan
    if ( $load_swiper ) {
        wp_enqueue_style('pm-swiper');
        wp_enqueue_script('pm-swiper');
    }

    // youtube-background (solo se encola cuando hay un video hero de YouTube)
    wp_register_script(
        'pm-youtube-background',
        PM_ESSENCE_TEMPLATE_URI . '/assets/libs/youtube-background/jquery.youtube-background.min.js',
        array( 'jquery' ),
        '1.1.8',
        true
    );
    wp_script_add_data( 'pm-youtube-background', 'strategy', 'defer' );

    // reCAPTCHA — solo en páginas con formulario de newsletter
    $recaptcha_site_key = defined('KEY_API_RECAPTCHA') ? KEY_API_RECAPTCHA : (string) get_theme_mod('pm_recaptcha_site_key', '');
    $has_newsletter     = pm_page_has_section_layout('newsletter_subscribe_banner');

    if ( $recaptcha_site_key !== '' && $has_newsletter ) {
        wp_enqueue_script(
            'pm-recaptcha',
            'https://www.google.com/recaptcha/api.js?render=' . rawurlencode($recaptcha_site_key),
            array(),
            null,
            true
        );

        // Pasar ajaxurl y site key al JS de forma segura
        wp_localize_script( 'main-js', 'pmNewsletter', array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'recaptchaSiteKey' => $recaptcha_site_key,
        ) );
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


/**
 * Output a <link rel="preload"> for the LCP image (video-hero poster) so the
 * browser discovers it in the initial HTML response instead of waiting for the
 * template to render (~2 s resource load delay).
 */
function pm_preload_lcp_image() {
    $post_id = get_the_ID();
    if ( ! $post_id ) return;

    $sections = get_field( 'sections', $post_id );
    if ( empty( $sections ) || ! is_array( $sections ) ) return;

    $first = $sections[0];
    if ( ! isset( $first['acf_fc_layout'] ) || $first['acf_fc_layout'] !== 'video_hero' ) return;

    $poster_url = '';
    $srcset     = '';

    if ( ! empty( $first['poster_image']['ID'] ) ) {
        $img_src    = wp_get_attachment_image_src( $first['poster_image']['ID'], '2048x2048' );
        $srcset     = wp_get_attachment_image_srcset( $first['poster_image']['ID'], '2048x2048' );
        $poster_url = $img_src ? $img_src[0] : '';
    } elseif ( ! empty( $first['video_url'] ) ) {
        $video      = pm_parse_video( $first['video_url'] );
        $poster_url = $video['thumbnail'] ?? '';
    }

    if ( $poster_url ) {
        $tag = '<link rel="preload" as="image" href="' . esc_url( $poster_url ) . '"';
        if ( $srcset ) {
            $tag .= ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="100vw"';
        }
        $tag .= ' fetchpriority="high">' . "\n";
        echo $tag;
    }
}
add_action( 'wp_head', 'pm_preload_lcp_image', 1 );

/**
 * Preconnect a dominios externos usados en above-the-fold.
 */
function pm_preconnect_hints() {
    // Swiper + GSAP vienen de jsDelivr — preconnect elimina el DNS/TLS handshake en runtime.
    echo '<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>' . "\n";
    // YouTube — usado en video_hero como iframe y como fuente de thumbnails.

}
add_action( 'wp_head', 'pm_preconnect_hints', 1 );

/**
 * Desencola el CSS de Gutenberg block library en el frontend.
 * No se usa en este tema (no hay bloques de contenido con estilos del core).
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
}, 100 );

/**
 * Difiere CSS no crítico para el above-the-fold usando el media trick:
 * el browser lo descarga en background y lo aplica sin bloquear el render.
 */
add_filter( 'style_loader_tag', function ( $html, $handle ) {
    // CSS diferidos (no críticos above-the-fold):
    // - pm-swiper, pm-essence-fonts: siempre diferidos
    // - essence-components: estilos de componentes, no todos son above-the-fold
    // - pm-bootstrap-css: grid y utilidades, diferido con noscript fallback
    // NOTA: si aparece FOUC, mueve pm-bootstrap-css fuera de esta lista.
    $defer_handles = [
        'pm-swiper',
        'pm-essence-fonts',
    ];

    if ( ! in_array( $handle, $defer_handles, true ) ) {
        return $html;
    }

    preg_match( '/href=[\'"]([^\'"]+)[\'"]/', $html, $m );
    if ( empty( $m[1] ) ) {
        return $html;
    }

    $href = esc_url( $m[1] );

    return '<link rel="preload" href="' . $href . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
         . '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>' . "\n";
}, 10, 2 );


