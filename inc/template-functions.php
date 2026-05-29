<?php
/**
 * Playa Mujeres Essence template functions.
 *
 * @package pm-essence
 */

if ( ! function_exists( 'pm_get_logo' ) ) {
    /**
     * Get the logo image from theme mods.
     *
     * Soporta:
     * - theme_mod como ID de attachment (recomendado)
     * - theme_mod como URL (fallback, convierte a attachment si es posible)
     *
     * @param string $class   Extra CSS classes for the image.
     * @param string $size    Image size (for attachment-based logos).
     * @param string $variant Logo variant: desktop_light (default), desktop_dark, mobile_light, mobile_dark.
     *
     * @return string HTML image tag or site title fallback.
     */
    function pm_get_logo( $class = '', $size = 'full', $variant = 'desktop_light' ) {

        static $cache = array();

        $cache_key = $class . '|' . $size . '|' . $variant;
        if ( isset( $cache[ $cache_key ] ) ) {
            return $cache[ $cache_key ];
        }

        switch ( $variant ) {
            case 'desktop_dark':
                $mod_key = 'pm_header_logo_dark';
                break;
            case 'mobile_light':
                $mod_key = 'pm_header_logo_mobile';
                break;
            case 'mobile_dark':
                $mod_key = 'pm_header_logo_mobile_dark';
                break;
            case 'desktop_light':
            default:
                $mod_key = 'pm_header_logo';
                break;
        }

        $raw_value = get_theme_mod( $mod_key );

        // Fallback: si ese variant no tiene logo, usamos el desktop_light clásico.
        if ( empty( $raw_value ) ) {
            $raw_value = get_theme_mod( 'pm_header_logo' );
        }

        // Si sigue sin haber logo, mostramos el título del sitio.
        if ( empty( $raw_value ) ) {
            $cache[ $cache_key ] = '<span class="site-title ' . esc_attr( $class ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
            return $cache[ $cache_key ];
        }

        // Permite guardar el customizer como ID o como URL.
        $logo_id  = 0;
        $logo_url = '';

        if ( is_numeric( $raw_value ) ) {
            $logo_id = absint( $raw_value );
        } else {
            $logo_url = (string) $raw_value;
            $logo_id  = attachment_url_to_postid( $logo_url );
        }

        // data-logo-* (para JS): siempre en URL
        $light_raw = get_theme_mod( 'pm_header_logo' );
        $dark_raw  = get_theme_mod( 'pm_header_logo_dark' );

        $light_url = is_numeric( $light_raw ) ? wp_get_attachment_image_url( absint( $light_raw ), 'full' ) : (string) $light_raw;
        $dark_url  = is_numeric( $dark_raw )  ? wp_get_attachment_image_url( absint( $dark_raw ), 'full' )  : (string) $dark_raw;

        // Si no es attachment (URL externa, etc.), devolvemos <img> simple.
        if ( ! $logo_id ) {
            if ( $logo_url === '' ) {
                $logo_url = is_numeric( $raw_value ) ? wp_get_attachment_image_url( absint( $raw_value ), $size ) : (string) $raw_value;
            }

            $cache[ $cache_key ] = sprintf(
                    '<img src="%1$s" alt="%2$s" class="site-logo %3$s" loading="lazy" decoding="async" data-logo-light="%4$s" data-logo-dark="%5$s" />',
                    esc_url( $logo_url ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_attr( $class ),
                    esc_url( $light_url ),
                    esc_url( $dark_url )
            );
            return $cache[ $cache_key ];
        }

        // Attachment: usa wp_get_attachment_image() => incluye srcset/sizes/width/height automáticamente.
        $html = wp_get_attachment_image(
                $logo_id,
                $size,
                false,
                array(
                        'class'           => trim( 'site-logo ' . $class ),
                        'loading'         => 'eager',
                        'decoding'        => 'async',
                        'fetchpriority'   => 'high',
                        'data-logo-light' => esc_url( $light_url ),
                        'data-logo-dark'  => esc_url( $dark_url ),
                        'alt'             => get_bloginfo( 'name' ),
                )
        );

        // Si por alguna razón WP no devuelve HTML, fallback seguro.
        if ( empty( $html ) ) {
            $src = wp_get_attachment_image_url( $logo_id, $size );
            $html = sprintf(
                    '<img src="%1$s" alt="%2$s" class="site-logo %3$s" loading="lazy" decoding="async" data-logo-light="%4$s" data-logo-dark="%5$s" />',
                    esc_url( $src ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_attr( $class ),
                    esc_url( $light_url ),
                    esc_url( $dark_url )
            );
        }

        $cache[ $cache_key ] = $html;
        return $cache[ $cache_key ];
    }
}

if ( ! function_exists( 'pm_get_footer_logo' ) ) {
    /**
     * Get the footer logo image from theme mods.
     *
     * Soporta:
     * - theme_mod como ID de attachment (recomendado)
     * - theme_mod como URL (fallback, convierte a attachment si es posible)
     *
     * @param string $class Extra CSS classes for the image.
     * @param string $size  Image size (for attachment-based logos).
     *
     * @return string HTML image tag o string vacío si no hay logo.
     */
    function pm_get_footer_logo( $class = '', $size = 'full' ) {

        static $cache = array();

        $cache_key = $class . '|' . $size;
        if ( isset( $cache[ $cache_key ] ) ) {
            return $cache[ $cache_key ];
        }

        $raw_value = get_theme_mod( 'pm_footer_logo' );

        if ( empty( $raw_value ) ) {
            $cache[ $cache_key ] = '';
            return $cache[ $cache_key ];
        }

        $logo_id  = 0;
        $logo_url = '';

        if ( is_numeric( $raw_value ) ) {
            $logo_id = absint( $raw_value );
        } else {
            $logo_url = (string) $raw_value;
            $logo_id  = attachment_url_to_postid( $logo_url );
        }

        // Si no es attachment (URL externa, etc.), devolvemos <img> simple.
        if ( ! $logo_id ) {
            if ( $logo_url === '' ) {
                $logo_url = is_numeric( $raw_value ) ? wp_get_attachment_image_url( absint( $raw_value ), $size ) : (string) $raw_value;
            }

            $cache[ $cache_key ] = sprintf(
                    '<img src="%1$s" alt="%2$s" class="footer-logo %3$s" loading="lazy" decoding="async" />',
                    esc_url( $logo_url ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_attr( $class )
            );
            return $cache[ $cache_key ];
        }

        // Attachment: wp_get_attachment_image() genera srcset/sizes/width/height cuando aplica.
        $html = wp_get_attachment_image(
                $logo_id,
                $size,
                false,
                array(
                        'class'    => trim( 'footer-logo ' . $class ),
                        'loading'  => 'lazy',
                        'decoding' => 'async',
                        'alt'      => get_bloginfo( 'name' ),
                )
        );

        // Fallback seguro si WP no devuelve HTML.
        if ( empty( $html ) ) {
            $src = wp_get_attachment_image_url( $logo_id, $size );
            $html = sprintf(
                    '<img src="%1$s" alt="%2$s" class="footer-logo %3$s" loading="lazy" decoding="async" />',
                    esc_url( $src ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_attr( $class )
            );
        }

        $cache[ $cache_key ] = $html;
        return $cache[ $cache_key ];
    }
}

//if ( ! function_exists( 'pm_get_logo_with_dark' ) ) {
//
//    /**
//     * Muestra el logo principal desde el Customizer
//     * Y además agrega data-logo-light y data-logo-dark
//     * para intercambiarlo con JS.
//     *
//     * @param string $class
//     * @param string $size
//     * @param string $dark_filename     Archivo dark dentro de /assets/images/
//     * @param string $light_filename    Archivo light dentro de /assets/images/
//     *
//     * @return string
//     */
//    function pm_get_logo_with_dark( $class = '', $size = 'full', $dark_filename = 'logo-dark.svg', $light_filename = 'logo-light.svg' ) {
//
//        // Rutas a las imágenes locales
//        $dark_url  = PM_ESSENCE_TEMPLATE_URI . '/assets/images/' . $dark_filename;
//        $light_url = PM_ESSENCE_TEMPLATE_URI . '/assets/images/' . $light_filename;
//
//        // Logo del Customizer
//        $logo_url = get_theme_mod('pm_header_logo');
//
//        // Si no hay logo en customizer → mostrar el "light" por defecto
//        if ( empty( $logo_url ) ) {
//            return sprintf(
//                    '<img
//                    src="%1$s"
//                    alt="%2$s"
//                    class="site-logo %3$s"
//                    loading="lazy"
//                    data-logo-light="%4$s"
//                    data-logo-dark="%5$s"
//                />',
//                    esc_url( $light_url ),
//                    esc_attr( get_bloginfo('name') ),
//                    esc_attr( $class ),
//                    esc_url( $light_url ),
//                    esc_url( $dark_url )
//            );
//        }
//
//        // Convertir URL a ID
//        $logo_id = attachment_url_to_postid( $logo_url );
//
//        // Si NO es un attachment → usamos sin srcset
//        if ( ! $logo_id ) {
//            return sprintf(
//                    '<img
//                    src="%1$s"
//                    alt="%2$s"
//                    class="site-logo %3$s"
//                    loading="lazy"
//                    data-logo-light="%4$s"
//                    data-logo-dark="%5$s"
//                />',
//                    esc_url( $logo_url ),
//                    esc_attr( get_bloginfo('name') ),
//                    esc_attr( $class ),
//                    esc_url( $light_url ),
//                    esc_url( $dark_url )
//            );
//        }
//
//        // Obtener imágenes responsive
//        $src     = wp_get_attachment_image_url( $logo_id, $size );
//        $srcset  = wp_get_attachment_image_srcset( $logo_id, $size );
//        $sizes   = wp_get_attachment_image_sizes( $logo_id, $size );
//
//        $meta = wp_get_attachment_metadata( $logo_id );
//        $width  = isset( $meta['width'] ) ? $meta['width'] : '';
//        $height = isset( $meta['height'] ) ? $meta['height'] : '';
//
//        return sprintf(
//                '<img
//                src="%1$s"
//                srcset="%2$s"
//                sizes="%3$s"
//                width="%4$s"
//                height="%5$s"
//                alt="%6$s"
//                class="site-logo %7$s"
//                loading="lazy"
//                data-logo-light="%8$s"
//                data-logo-dark="%9$s"
//            />',
//                esc_url( $src ),
//                esc_attr( $srcset ),
//                esc_attr( $sizes ),
//                esc_attr( $width ),
//                esc_attr( $height ),
//                esc_attr( get_bloginfo('name') ),
//                esc_attr( $class ),
//                esc_url( $light_url ),
//                esc_url( $dark_url )
//        );
//    }
//}

if ( ! function_exists( 'pm_essence_nav_menu' ) ) {
    /**
     * Renderiza el menú de navegación con fallback:
     *
     * - $location = 'desktop' → intenta desktop, si no hay, usa mobile.
     * - $location = 'mobile'  → intenta mobile, si no hay, usa desktop.
     *
     * @param string $location 'desktop' o 'mobile'.
     */
    function pm_essence_nav_menu( $location = 'desktop' ) {

        // IDs configurados en el Customizer
        $desktop_menu_id = (int) get_theme_mod( 'pm_header_menu_id', 0 );
        $mobile_menu_id  = (int) get_theme_mod( 'pm_mobile_menu_id', 0 );

        $menu_id = 0;

        if ( 'mobile' === $location ) {
            // Primero mobile, si no existe → desktop
            if ( $mobile_menu_id && is_nav_menu( $mobile_menu_id ) ) {
                $menu_id = $mobile_menu_id;
            } elseif ( $desktop_menu_id && is_nav_menu( $desktop_menu_id ) ) {
                $menu_id = $desktop_menu_id;
            }
        } else {
            // Primero desktop, si no existe → mobile
            if ( $desktop_menu_id && is_nav_menu( $desktop_menu_id ) ) {
                $menu_id = $desktop_menu_id;
            } elseif ( $mobile_menu_id && is_nav_menu( $mobile_menu_id ) ) {
                $menu_id = $mobile_menu_id;
            }
        }

        // Si no hay ningún menú válido, no imprime nada.
        if ( ! $menu_id ) {
            return;
        }

        wp_nav_menu(
            array(
                'menu'           => $menu_id,
                'container'      => false,
                'menu_class'     => 'navbar-nav d-flex flex-column flex-lg-row mb-0 pm-navbar pm-navbar-' . esc_attr( $location ),
                'fallback_cb'    => '__return_false',
                'depth'          => 2,
                'pm_location'    => $location
            )
        );
    }
}


if ( ! function_exists( 'pm_essence_homepage_section_hero' ) ) {
    /**
     * Display Section Hero
     * Hooked into the `homepage` action in the homepage template
     *
     * @return  void
     */
    function pm_essence_homepage_section_hero() {
        get_template_part( 'template-parts/sections/homepage/hero.php', '' );
    }
}

if ( ! function_exists( 'pm_essence_lang_switcher' ) ) {
    /**
     * Display Lang Switcher
     *
     * @return string HTML list tag.
     */
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return;
    }
    function pm_essence_lang_switcher($class='') {

        $langs = pll_the_languages( array( 'raw' => 1 ) );

        $current = function_exists( 'pll_current_language' )
            ? pll_current_language()
            : 'en';
        if ( ! isset( $langs['es'] ) ) {
            $langs['es'] = array(
                'name'         => 'Español',
                'slug'         => 'es',
                'url'          => home_url( '/es/' ),
                'current_lang' => 0,
            );
        }

        if ( empty( $langs ) ) {
            return;
        }

        ?>
        <div class="pm-lang-switcher">

            <button class="pm-lang-switcher__current <?php echo esc_attr( $class ); ?>" type="button">
                <span><?php echo strtoupper( $current ); ?></span>

                <svg class="pm-lang-switcher__caret" width="16" height="16" viewBox="0 0 14 14"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.75 4.125L6 7.875L2.25 4.125"
                          stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <ul class="pm-lang-switcher__list">
                <?php foreach ( $langs as $lang ) : ?>
                    <li class="pm-lang-switcher__item <?php echo $lang['current_lang'] ? 'is-active' : ''; ?>">
                        <a href="<?php echo esc_url( $lang['url'] ); ?>">
                            <?php echo esc_html( strtoupper( $lang['slug'] ) ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php

    }
}

if ( ! function_exists( 'pm_essence_menu_mobile' ) ) {
    /**
     * Display Menu Mobile
     *
     * @return  void
     */
    function pm_essence_menu_mobile() {
        get_template_part( 'template-parts/menu/menu-mobile', '' );
    }
}

if ( ! function_exists( 'pm_essence_show_social_links' ) ) {
    /**
     * Display Weather Now
     *
     * @return  void
     */
    function pm_essence_show_social_links() {
        $show_mobile_social = (int) get_theme_mod( 'pm_mobile_show_social_links', 1 );

        if ( $show_mobile_social === 1 ) :

            $facebook_url  = trim( get_theme_mod( 'pm_social_facebook_url', '' ) );
            $instagram_url = trim( get_theme_mod( 'pm_social_instagram_url', '' ) );

            if ( $facebook_url || $instagram_url ) :
                ?>
                <div class="social-links">
                    <h4>Social Links</h4>
                    <div>
                        <?php if ( $facebook_url ) : ?>
                            <a href="<?php echo esc_url( $facebook_url ); ?>" class="social-links__item" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="#323232" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M16.9265 8.02637H13.9816C12.9378 8.02637 12.0894 8.86847 12.0817 9.91229L11.9964 21.4268M10.082 14.0017H14.8847" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php if ( $instagram_url ) : ?>
                            <a href="<?php echo esc_url( $instagram_url ); ?>" class="social-links__item" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.503 6.73102C11.4682 6.73102 10.4567 7.03786 9.59631 7.61274C8.73593 8.18762 8.06536 9.00473 7.66937 9.96072C7.27338 10.9167 7.16978 11.9687 7.37165 12.9835C7.57352 13.9984 8.07181 14.9306 8.80349 15.6623C9.53518 16.394 10.4674 16.8923 11.4823 17.0942C12.4972 17.296 13.5491 17.1924 14.5051 16.7965C15.4611 16.4005 16.2782 15.7299 16.8531 14.8695C17.428 14.0091 17.7348 12.9976 17.7348 11.9629C17.7321 10.5761 17.1801 9.24693 16.1995 8.26635C15.2189 7.28577 13.8897 6.7337 12.503 6.73102ZM12.503 15.3143C11.8401 15.3143 11.1921 15.1178 10.641 14.7495C10.0898 14.3812 9.66027 13.8578 9.40661 13.2454C9.15294 12.633 9.08657 11.9591 9.21589 11.309C9.34521 10.6589 9.6644 10.0617 10.1331 9.59301C10.6018 9.1243 11.199 8.8051 11.8491 8.67579C12.4992 8.54647 13.1731 8.61284 13.7855 8.8665C14.3979 9.12017 14.9213 9.54974 15.2896 10.1009C15.6579 10.652 15.8544 11.3 15.8544 11.9629C15.8504 12.8505 15.496 13.7006 14.8684 14.3283C14.2407 14.9559 13.3906 15.3103 12.503 15.3143Z" fill="#323232"/>
                                    <path d="M17.9388 7.79159C18.5931 7.79159 19.1235 7.26117 19.1235 6.60685C19.1235 5.95254 18.5931 5.42212 17.9388 5.42212C17.2845 5.42212 16.754 5.95254 16.754 6.60685C16.754 7.26117 17.2845 7.79159 17.9388 7.79159Z" fill="#323232"/>
                                    <path d="M21.0051 3.54297C20.4415 2.9862 19.7701 2.55035 19.0322 2.2621C18.2942 1.97385 17.5052 1.83927 16.7135 1.8666H8.29232C7.50165 1.81892 6.70986 1.93954 5.96925 2.22049C5.22863 2.50144 4.55606 2.93634 3.99595 3.49644C3.43585 4.05655 3.00095 4.72912 2.72 5.46973C2.43905 6.21034 2.31843 7.00214 2.36611 7.79281V16.1721C2.33632 16.9801 2.4734 17.7855 2.76881 18.538C3.06422 19.2906 3.5116 19.9742 4.08303 20.5461C5.23458 21.617 6.76239 22.1899 8.33414 22.1402H16.6716C18.256 22.1931 19.7976 21.6205 20.9633 20.5461C21.5237 19.9767 21.9615 19.2986 22.2498 18.5535C22.5381 17.8085 22.6708 17.0122 22.6397 16.2139V7.79281C22.6646 7.0112 22.5328 6.2325 22.2521 5.50261C21.9714 4.77272 21.5474 4.10642 21.0051 3.54297ZM20.8417 16.2139C20.8614 16.7662 20.7663 17.3166 20.5623 17.8302C20.3584 18.3439 20.0501 18.8096 19.6569 19.198C18.8378 19.9259 17.7678 20.3071 16.6729 20.261H8.33414C7.23923 20.3071 6.16926 19.9259 5.35013 19.198C4.97203 18.7941 4.67939 18.318 4.48975 17.7983C4.3001 17.2785 4.21736 16.7259 4.24648 16.1734V7.79281C4.22092 7.24683 4.30545 6.70129 4.49505 6.18865C4.68465 5.67601 4.97545 5.20675 5.35013 4.8088C6.16565 4.07486 7.2383 3.69317 8.33414 3.74697H16.754C17.3 3.72141 17.8455 3.80594 18.3582 3.99554C18.8708 4.18514 19.3401 4.47593 19.738 4.85061C20.4714 5.65292 20.8668 6.70737 20.8417 7.79408V16.2139Z" fill="#323232"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            endif;
        endif;
    }
}

if ( ! function_exists( 'pm_essence_nav_menu_trigger_arrow' ) ) {
    function pm_essence_nav_menu_trigger_arrow( $item_output, $item, $depth, $args ) {
        $classes = is_array( $item->classes ) ? $item->classes : array();
        $has_trigger = in_array( 'trigger-filters', $classes, true ) || in_array( 'trigger-experiences', $classes, true );

        if ( ! $has_trigger ) {
            return $item_output;
        }

        $svg = '<svg width="16" height="16" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9.75 4.125L6 7.875L2.25 4.125" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

        return str_replace( '</a>', $svg . '</a>', $item_output );
    }
}
add_filter( 'walker_nav_menu_start_el', 'pm_essence_nav_menu_trigger_arrow', 10, 4 );

if ( ! function_exists( 'pm_essence_menu_where_to_stay_mobile' ) ) {
    /**
     * Display Menu Mobile
     *
     * @return  void
     */
    function pm_essence_menu_where_to_stay_mobile() {
        get_template_part( 'template-parts/menu/where-to-stay-mobile', '' );
    }
}

if ( ! function_exists( 'pm_essence_menu_experiences_mobile' ) ) {
    /**
     * Display Menu Mobile
     *
     * @return  void
     */
    function pm_essence_menu_experiences_mobile() {
        get_template_part( 'template-parts/menu/experiences-mobile', '' );
    }
}

if ( ! function_exists( 'pm_essence_menu_where_to_stay_desktop' ) ) {
    /**
     * Display Menu Desktop
     *
     * @return  void
     */
    function pm_essence_menu_where_to_stay_desktop() {
        get_template_part( 'template-parts/menu/where-to-stay-desktop', '' );
    }
}

if ( ! function_exists( 'pm_essence_menu_experiences_desktop' ) ) {
    /**
     * Display Menu Desktop
     *
     * @return  void
     */
    function pm_essence_menu_experiences_desktop() {
        get_template_part( 'template-parts/menu/experiences-desktop', '' );
    }
}

if ( ! function_exists( 'pm_essence_heading_tag_or_null' ) ) {
    /**
     * Select a heading tag
     *
     * @return  void
     */
    function pm_essence_heading_tag_or_null($level, $default_tag) {
        if (empty($level)) {
            return $default_tag;
        }

        $level = strtolower(trim((string) $level));

        if ($level === 'none') {
            return null;
        }

        // Accept only h1..h6 from ACF
        if (preg_match('/^h([1-6])$/', $level, $m)) {
            return 'h' . $m[1];
        }

        return $default_tag;
    }
}

if ( ! function_exists( 'pm_essence_get_blog_settings' ) ) {
    /**
     * Resolve blog settings by context using the shared option group.
     *
     * Supported contexts:
     * - blog:     base blog_page_settings values
     * - category: base blog_page_settings values with category_page_* overrides
     *
     * @param string $context
     * @return array
     */
    function pm_essence_get_blog_settings( $context = 'blog' ) {
        if ( ! function_exists( 'get_field' ) ) {
            return array();
        }

        $context  = sanitize_key( (string) $context );
        $settings = get_field( 'blog_page_settings', 'option' );

        if ( ! is_array( $settings ) ) {
            return array();
        }

        if ( 'category' !== $context ) {
            return $settings;
        }

        $category_overrides = array(
            'display_categories_filter' => 'category_page_display_categories_filter',
            'display_featured_posts'    => 'category_page_display_featured_posts',
            'display_newsletter_form'   => 'category_page_display_newsletter_form',
            'posts_per_page'            => 'category_page_posts_per_page',
            'enable_load_more'          => 'category_page_enable_load_more',
            'button_text'               => 'category_page_button_text',
        );

        foreach ( $category_overrides as $base_key => $override_key ) {
            if ( array_key_exists( $override_key, $settings ) ) {
                $settings[ $base_key ] = $settings[ $override_key ];
            }
        }

        return $settings;
    }
}

if (!function_exists('pm_get_hotel_primary_showcase_logo_dark')) {
    /**
     * Devuelve el logo preferido del primary_showcase_hero:
     * - Primero: logo_dark
     * - Fallback: logo
     *
     * Busca en: sections (flexible) -> primary_showcase_hero -> slides[0]
     *
     * @param int $post_id Hotel ID
     * @return array|null ACF image array
     */
    function pm_get_hotel_primary_showcase_logo_dark($post_id)
    {
        $post_id = (int)$post_id;
        if ($post_id <= 0) return null;

        $sections = get_field('sections', $post_id);
        if (!is_array($sections) || empty($sections)) return null;

        foreach ($sections as $section) {
            if (!is_array($section)) continue;

            $layout = isset($section['acf_fc_layout']) ? $section['acf_fc_layout'] : '';
            if ($layout !== 'primary_showcase_hero') continue;

            $slides = isset($section['slides']) ? $section['slides'] : null;
            if (!is_array($slides) || empty($slides)) return null;

            $first_slide = $slides[0];
            if (!is_array($first_slide)) return null;

            $logo_dark = isset($first_slide['logo_dark']) ? $first_slide['logo_dark'] : null;
            if (is_array($logo_dark) && !empty($logo_dark['url'])) {
                return $logo_dark;
            }

            $logo = isset($first_slide['logo']) ? $first_slide['logo'] : null;
            if (is_array($logo) && !empty($logo['url'])) {
                return $logo;
            }

            return null;
        }

        return null;
    }
}

if (!function_exists('pm_get_hotel_primary_showcase_logo')) {
    /**
     * Devuelve el logo "normal" del primary_showcase_hero:
     * - Primero: logo
     * - Fallback: logo_dark
     *
     * Busca en: sections (flexible) -> primary_showcase_hero -> slides[0]
     *
     * @param int $post_id Hotel ID
     * @return array|null ACF image array
     */
    function pm_get_hotel_primary_showcase_logo($post_id)
    {
        $post_id = (int)$post_id;
        if ($post_id <= 0) return null;

        $sections = get_field('sections', $post_id);
        if (!is_array($sections) || empty($sections)) return null;

        foreach ($sections as $section) {
            if (!is_array($section)) continue;

            $layout = isset($section['acf_fc_layout']) ? $section['acf_fc_layout'] : '';
            if ($layout !== 'primary_showcase_hero') continue;

            $slides = isset($section['slides']) ? $section['slides'] : null;
            if (!is_array($slides) || empty($slides)) return null;

            $first_slide = $slides[0];
            if (!is_array($first_slide)) return null;

            $logo = isset($first_slide['logo']) ? $first_slide['logo'] : null;
            if (is_array($logo) && !empty($logo['url'])) {
                return $logo;
            }

            $logo_dark = isset($first_slide['logo_dark']) ? $first_slide['logo_dark'] : null;
            if (is_array($logo_dark) && !empty($logo_dark['url'])) {
                return $logo_dark;
            }

            return null;
        }

        return null;
    }
}


add_filter( 'rest_pre_dispatch', function( $result, $server, $request ) {
    $route = $request->get_route();

    if ( strpos( $route, '/wordpress-popular-posts/' ) === false ) {
        return $result;
    }

    if ( 'POST' !== $request->get_method() ) {
        return $result;
    }

    if ( ! preg_match( '#/v2/views/(\d+)#', $route, $matches ) ) {
        return $result;
    }

    $post_id = (int) $matches[1];
    if ( ! $post_id ) {
        return $result;
    }

    $ip = '';
    if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ip = trim( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] )[0] );
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if ( ! $ip ) {
        return $result;
    }

    $transient_key = 'wpp_ip_' . md5( $ip . '_' . $post_id );

    if ( false !== get_transient( $transient_key ) ) {
        return new WP_REST_Response( array( 'results' => 'WPP: OK. Already counted.' ), 200 );
    }

    set_transient( $transient_key, 1, DAY_IN_SECONDS );

    return $result;
}, 10, 3 );


if (!function_exists('pm_essence_contact_href')) {
    /**
     *
     *
     * @param int $type
     * @param $value
     * @param array $location_link
     * @return mixed|string $value
     */
    function pm_essence_contact_href($type, $value, $location_link) {
        $type  = strtolower(trim((string) $type));
        $value = trim((string) $value);

        if ($type === 'location') {
            if (is_array($location_link) && !empty($location_link['url'])) {
                return $location_link['url'];
            }
            return $value;
        }

        if ($type === 'email') {
            return $value !== '' ? ('mailto:' . $value) : '';
        }

        if ($type === 'phone') {
            // Acepta números con +, espacios, guiones; los limpiamos para tel:
            $tel = preg_replace('/[^0-9\+]/', '', $value);
            return $tel !== '' ? ('tel:' . $tel) : '';
        }

        if ($type === 'whatsapp') {
            // Espera número en formato internacional. Limpiamos todo excepto dígitos.
            $wa = preg_replace('/\D+/', '', $value);
            return $wa !== '' ? ('https://wa.me/' . $wa) : '';
        }

        // custom
        return $value;
    }
}

/**
 * @param $hreflangs
 * @return array
 */
if ( ! function_exists( 'disable_pll_hreflang_output' ) ) {
    function disable_pll_hreflang_output( $hreflangs ) {
        return array();
    }
}
add_filter( 'pll_rel_hreflang_attributes', 'disable_pll_hreflang_output', 10, 1 );

/**
 * Filters the array of presenters to remove instances of Locale_Presenter.
 *
 * @param array $presenters Array of presenter instances.
 * @return array Filtered array of presenters, excluding Locale_Presenter instances.
 */
if ( ! function_exists( 'remove_locale_presenter' ) ) {
    function remove_locale_presenter( $presenters ) {
        return array_map( function( $presenter ) {
            if ( ! $presenter instanceof Yoast\WP\SEO\Presenters\Open_Graph\Locale_Presenter ) {
                return $presenter;
            }
        }, $presenters );
    }
}
add_action( 'wpseo_frontend_presenters', 'remove_locale_presenter' );
if ( ! function_exists( 'custom_og_locale_tag' ) ) {
    function custom_og_locale_tag() {
        if ( function_exists( 'get_bloginfo' ) ) {
            $current_lang = get_bloginfo('language');

            switch ( $current_lang ) {
                case 'es':
                    $locale = 'es';
                    break;
                case 'fr':
                    $locale = 'fr';
                    break;
                default:
                    $locale = 'en';
                    break;
            }

            echo '<meta class="yoast-locale" property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";
        }
    }
}
add_action( 'wp_head', 'custom_og_locale_tag' );

if ( ! function_exists( 'setLangAttr' ) ) {
    function setLangAttr() {
        $current_lang = get_bloginfo('language');

        if (isset($current_lang) && !empty($current_lang)) {
            switch ( $current_lang ) {
                case 'es':
                    return 'lang="es"';
                case 'fr':
                    return 'lang="fr"';
                default:
                    return 'lang="en"';
            }
        }
        return 'lang="en"';
    }
}

add_filter( 'language_attributes', 'setLangAttr' );

/**
 * Disables Yoast SEO JSON-LD output completely
 *
 * @author dennis@markea.agency
 */
add_filter('wpseo_json_ld_output', '__return_false');