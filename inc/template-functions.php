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
     * @param string $class   Extra CSS classes for the image.
     * @param string $size    Image size (for attachment-based logos).
     * @param string $variant Logo variant: desktop_light (default), desktop_dark, mobile_light, mobile_dark.
     *
     * @return string HTML image tag or site title fallback.
     */
    function pm_get_logo( $class = '', $size = 'full', $variant = 'desktop_light' ) {

        // Elegimos el theme_mod según el variant.
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

        // Obtenemos la URL principal según el variant.
        $logo_url = get_theme_mod( $mod_key );

        // Fallback: si ese variant no tiene logo, usamos el desktop_light clásico.
        if ( empty( $logo_url ) ) {
            $logo_url = get_theme_mod( 'pm_header_logo' );
        }

        // Si sigue sin haber logo, mostramos el título del sitio.
        if ( empty( $logo_url ) ) {
            return '<span class="site-title ' . esc_attr( $class ) . '">' . get_bloginfo( 'name' ) . '</span>';
        }

        // Intentamos obtener el ID del adjunto (para srcset, sizes, etc.).
        $logo_id = attachment_url_to_postid( $logo_url );

        // Si no es un adjunto de la librería (URL externa, etc.), devolvemos un <img> sencillo.
        if ( ! $logo_id ) {
            return sprintf(
                    '<img src="%1$s" alt="%2$s" class="site-logo %3$s" loading="lazy" />',
                    esc_url( $logo_url ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_attr( $class )
            );
        }

        // Con adjunto: usamos srcset, sizes y dimensiones.
        $src    = wp_get_attachment_image_url( $logo_id, $size );
        $srcset = wp_get_attachment_image_srcset( $logo_id, $size );
        $sizes  = wp_get_attachment_image_sizes( $logo_id, $size );

        $meta   = wp_get_attachment_metadata( $logo_id );
        $width  = isset( $meta['width'] )  ? $meta['width']  : '';
        $height = isset( $meta['height'] ) ? $meta['height'] : '';

        return sprintf(
                '<img 
                src="%1$s"
                srcset="%2$s"
                sizes="%3$s"
                width="%4$s"
                height="%5$s"
                alt="%6$s"
                class="site-logo %7$s"
                loading="lazy"
                data-logo-light="%8$s"
                data-logo-dark="%9$s"
            />',
                esc_url( $src ),
                esc_attr( $srcset ),
                esc_attr( $sizes ),
                esc_attr( $width ),
                esc_attr( $height ),
                esc_attr( get_bloginfo( 'name' ) ),
                esc_attr( $class ),
                esc_url( get_theme_mod('pm_header_logo') ),
                esc_url( get_theme_mod('pm_header_logo_dark'))
        );
    }
}

if ( ! function_exists( 'pm_get_logo_with_dark' ) ) {

    /**
     * Muestra el logo principal desde el Customizer
     * Y además agrega data-logo-light y data-logo-dark
     * para intercambiarlo con JS.
     *
     * @param string $class
     * @param string $size
     * @param string $dark_filename     Archivo dark dentro de /assets/images/
     * @param string $light_filename    Archivo light dentro de /assets/images/
     *
     * @return string
     */
    function pm_get_logo_with_dark( $class = '', $size = 'full', $dark_filename = 'logo-dark.svg', $light_filename = 'logo-light.svg' ) {

        // Rutas a las imágenes locales
        $dark_url  = PM_ESSENCE_TEMPLATE_URI . '/assets/images/' . $dark_filename;
        $light_url = PM_ESSENCE_TEMPLATE_URI . '/assets/images/' . $light_filename;

        // Logo del Customizer
        $logo_url = get_theme_mod('pm_header_logo');

        // Si no hay logo en customizer → mostrar el "light" por defecto
        if ( empty( $logo_url ) ) {
            return sprintf(
                    '<img 
                    src="%1$s"
                    alt="%2$s"
                    class="site-logo %3$s"
                    loading="lazy"
                    data-logo-light="%4$s"
                    data-logo-dark="%5$s"
                />',
                    esc_url( $light_url ),
                    esc_attr( get_bloginfo('name') ),
                    esc_attr( $class ),
                    esc_url( $light_url ),
                    esc_url( $dark_url )
            );
        }

        // Convertir URL a ID
        $logo_id = attachment_url_to_postid( $logo_url );

        // Si NO es un attachment → usamos sin srcset
        if ( ! $logo_id ) {
            return sprintf(
                    '<img 
                    src="%1$s"
                    alt="%2$s"
                    class="site-logo %3$s"
                    loading="lazy"
                    data-logo-light="%4$s"
                    data-logo-dark="%5$s"
                />',
                    esc_url( $logo_url ),
                    esc_attr( get_bloginfo('name') ),
                    esc_attr( $class ),
                    esc_url( $light_url ),
                    esc_url( $dark_url )
            );
        }

        // Obtener imágenes responsive
        $src     = wp_get_attachment_image_url( $logo_id, $size );
        $srcset  = wp_get_attachment_image_srcset( $logo_id, $size );
        $sizes   = wp_get_attachment_image_sizes( $logo_id, $size );

        $meta = wp_get_attachment_metadata( $logo_id );
        $width  = isset( $meta['width'] ) ? $meta['width'] : '';
        $height = isset( $meta['height'] ) ? $meta['height'] : '';

        return sprintf(
                '<img 
                src="%1$s"
                srcset="%2$s"
                sizes="%3$s"
                width="%4$s"
                height="%5$s"
                alt="%6$s"
                class="site-logo %7$s"
                loading="lazy"
                data-logo-light="%8$s"
                data-logo-dark="%9$s"
            />',
                esc_url( $src ),
                esc_attr( $srcset ),
                esc_attr( $sizes ),
                esc_attr( $width ),
                esc_attr( $height ),
                esc_attr( get_bloginfo('name') ),
                esc_attr( $class ),
                esc_url( $light_url ),
                esc_url( $dark_url )
        );
    }
}

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
                'menu_class'     => 'navbar-nav d-flex mb-0 pm-navbar pm-navbar-' . esc_attr( $location ),
                'fallback_cb'    => '__return_false',
                'depth'          => 1,
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
        $show_mobile_social = (int) get_theme_mod( 'pm_mobile_show_social_links', 0 );

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