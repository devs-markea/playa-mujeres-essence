<?php
/**
 * Critical CSS — inline delivery + CSS deferral.
 *
 * Inlinea el CSS crítico (above-the-fold) directo en <head> para eliminar
 * el bloqueo de renderización. El resto del CSS se carga de forma diferida
 * con el truco preload/onload.
 *
 * Para regenerar el CSS crítico:
 *   node scripts/generate-critical.mjs
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Devuelve la ruta al archivo de critical CSS que corresponde a la página actual.
 */
function pm_get_critical_css_slug(): string {
    if ( is_singular( 'post' ) || is_home() || is_category() ) {
        return 'blog';
    }
    // Páginas de hotel — ajusta los slugs a los reales del sitio
    if ( is_singular( 'hotel' ) ) {
        return 'hotel';
    }
    return 'home';
}

/**
 * Inlinea el CSS crítico en <head> (prioridad 1 para que vaya lo antes posible).
 */
function pm_inline_critical_css(): void {
    $slug = pm_get_critical_css_slug();
    $file = get_template_directory() . '/assets/css/critical/' . $slug . '.css';

    if ( ! file_exists( $file ) ) {
        // Fallback al home si el slug específico no existe aún
        $file = get_template_directory() . '/assets/css/critical/home.css';
    }

    if ( ! file_exists( $file ) ) {
        return; // El script de generación aún no se ha ejecutado
    }

    $css = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    if ( empty( $css ) ) return;

    echo '<style id="pm-critical-css">' . $css . '</style>' . "\n";
}
add_action( 'wp_head', 'pm_inline_critical_css', 2 );

/**
 * Difiere solo Bootstrap CSS — el mayor bloqueador (27KB / ~1,890ms).
 * Diferir todo el CSS a la vez aumenta el TBT (recalculación masiva simultánea).
 * El critical CSS inline cubre el above-the-fold, Bootstrap puede cargarse diferido.
 */
add_filter( 'style_loader_tag', function ( string $html, string $handle ): string {
    // Solo aplica si el critical CSS ya existe (garantiza que el above-the-fold esté cubierto)
    $critical_exists = file_exists( get_template_directory() . '/assets/css/critical/home.css' );
    if ( ! $critical_exists ) {
        return $html;
    }

    $defer_handles = [
        'pm-bootstrap-css',   // 27.2 KiB
        'essence-components', // 128 KiB — mayor bloqueador de render
        'essence-mainstyles', // 11 KiB  — @font-faces locales
        'essence-style',      // 0.3 KiB — style.css (solo header de tema)
        'pm-blog',            // 7 KiB   — solo páginas blog
        'pm-swiper',
        'pm-essence-fonts',
        'pm-light-box2-css',
        'custom-gallery-style',
        // WordPress Popular Posts plugin — render-blocking en casi todas las páginas
        'wpp-css',
        'wpp-styles',
        'wordpress-popular-posts-css',
    ];

    if ( ! in_array( $handle, $defer_handles, true ) ) {
        return $html;
    }

    preg_match( '/href=[\'"]([^\'"]+)[\'"]/', $html, $m );
    if ( empty( $m[1] ) ) {
        return $html;
    }

    $href = esc_url( $m[1] );

    return '<link rel="preload" href="' . $href . '" as="style" '
         . 'onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
         . '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>' . "\n";
}, 10, 2 );
