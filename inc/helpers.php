<?php

//function pm_add_svg_to_items_with_children( $title, $item, $args, $depth ) {
//    if ( ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
//        return $title;
//    }
//
//    $svg = '
//        <svg class="pm-menu-chevron" fill="none" width="16" height="16" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
//            <path d="M9.75 4.125L6 7.875L2.25 4.125" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
//        </svg>
//
//    ';
//
//    // Envolvemos el texto en un span para poder alinearlo mejor
//    $title = '<span class="pm-menu-text">' . $title . '</span>' . $svg;
//
//    return $title;
//}
//add_filter( 'nav_menu_item_title', 'pm_add_svg_to_items_with_children', 10, 4 );

add_filter('show_admin_bar', '__return_false');


if (!function_exists('pm_parse_video')) {

    /**
     * Universal video parser for MP4, YouTube, Vimeo.
     *
     * @param string $url
     * @return array
     */
    function pm_parse_video($url)
    {
        if (empty($url)) {
            return [
                'type' => 'none',
                'id' => null,
                'embed_url' => null,
                'thumbnail' => null,
                'original_url' => null
            ];
        }

        $url = trim($url);

        // ---------- YOUTUBE ----------
        $youtube_patterns = [
            '/youtu\.be\/([^\?&]+)/',
            '/youtube\.com\/watch\?v=([^\?&]+)/',
            '/youtube\.com\/embed\/([^\?&]+)/',
            '/youtube\.com\/shorts\/([^\?&]+)/',
        ];

        foreach ($youtube_patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                $id = $m[1];

                // Parámetros para "background video" (sin UI)
                // enablejsapi=1 -> necesario para controlarlo con YT.Player en JS
                // controls=0, disablekb=1, fs=0, iv_load_policy=3 -> sin controles / sin UI
                // loop=1 + playlist=id -> loop real en iframe
                $params = http_build_query([
                    'autoplay' => 1,
                    'mute' => 1,
                    'loop' => 1,
                    'playlist' => $id,
                    'controls' => 0,
                    'disablekb' => 1,
                    'fs' => 0,
                    'iv_load_policy' => 3,
                    'modestbranding' => 1,
                    'rel' => 0,
                    'playsinline' => 1,
                    'enablejsapi' => 1,
                ], '', '&');

                return [
                    'type' => 'youtube',
                    'id' => $id,
                    'embed_url' => "https://www.youtube.com/embed/$id?$params",
                    'thumbnail' => "https://img.youtube.com/vi/$id/maxresdefault.jpg",
                    'thumbnails' => [
                        'hq'  => "https://img.youtube.com/vi/$id/hqdefault.jpg",   // 480x360
                        'sd'  => "https://img.youtube.com/vi/$id/sddefault.jpg",   // 640x480
                        'max' => "https://img.youtube.com/vi/$id/maxresdefault.jpg", // 1280x720
                    ],
                    'original_url' => $url,
                ];
            }
        }

        // ---------- VIMEO ----------
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            $id = $m[1];
            return [
                'type' => 'vimeo',
                'id' => $id,
                'embed_url' => "https://player.vimeo.com/video/$id?autoplay=1&muted=1&loop=1",
                'thumbnail' => null, // Vimeo necesita API para thumbnail
                'original_url' => $url,
            ];
        }

        // ---------- MP4 FILE ----------
        if (preg_match('/\.mp4(\?.*)?$/', $url)) {
            return [
                'type' => 'mp4',
                'id' => null,
                'embed_url' => $url,
                'thumbnail' => null,
                'original_url' => $url,
            ];
        }

        // ---------- Unknown / fallback ----------
        return [
            'type' => 'unknown',
            'id' => null,
            'embed_url' => null,
            'thumbnail' => null,
            'original_url' => $url,
        ];
    }
}

if ( ! function_exists( 'pm_get_cached_yt_thumbnail' ) ) {

    /**
     * Descarga el thumbnail de YouTube y lo guarda en uploads para servirlo
     * desde nuestro dominio con cache headers largos (en lugar de los 2h de YouTube).
     *
     * @param string $video_id  ID del video de YouTube.
     * @param string $quality   'hq' (480w), 'sd' (640w), 'max' (1280w).
     * @return string           URL local o URL de YouTube como fallback.
     */
    function pm_get_cached_yt_thumbnail( $video_id, $quality = 'max' ) {
        $quality_map = [
            'hq'  => 'hqdefault',
            'sd'  => 'sddefault',
            'max' => 'maxresdefault',
        ];

        $yt_file    = ( $quality_map[ $quality ] ?? 'maxresdefault' ) . '.jpg';
        $remote_url = "https://img.youtube.com/vi/{$video_id}/{$yt_file}";

        $transient_key = 'pm_yt_thumb_' . $video_id . '_' . $quality;
        $cached        = get_transient( $transient_key );
        if ( $cached ) {
            return $cached;
        }

        $upload     = wp_upload_dir();
        $dir_path   = $upload['basedir'] . '/yt-thumbnails/';
        $local_file = $dir_path . "yt-{$video_id}-{$yt_file}";
        $local_url  = $upload['baseurl'] . "/yt-thumbnails/yt-{$video_id}-{$yt_file}";

        if ( ! file_exists( $local_file ) ) {
            wp_mkdir_p( $dir_path );

            // Crear .htaccess con cache de 1 año si no existe
            $htaccess = $dir_path . '.htaccess';
            if ( ! file_exists( $htaccess ) ) {
                file_put_contents( $htaccess,
                    "<IfModule mod_expires.c>\n" .
                    "  ExpiresActive On\n" .
                    "  ExpiresByType image/jpeg \"access plus 1 year\"\n" .
                    "</IfModule>\n" .
                    "<IfModule mod_headers.c>\n" .
                    "  Header set Cache-Control \"max-age=31536000, public, immutable\"\n" .
                    "</IfModule>\n"
                );
            }

            $response = wp_remote_get( $remote_url, [ 'timeout' => 10 ] );

            if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
                return $remote_url; // fallback a YouTube
            }

            file_put_contents( $local_file, wp_remote_retrieve_body( $response ) );
        }

        set_transient( $transient_key, $local_url, MONTH_IN_SECONDS );

        return $local_url;
    }
}

if ( ! function_exists( 'pm_collection_to_post_id' ) ) {
    /**
     * Normaliza un item de colección ACF a un post ID entero.
     *
     * @param int|object $related
     * @return int  0 si no es válido.
     */
    function pm_collection_to_post_id( $related ) {
        if ( is_numeric( $related ) ) return (int) $related;
        if ( is_object( $related ) && ! empty( $related->ID ) ) return (int) $related->ID;
        return 0;
    }
}

if ( ! function_exists( 'pm_acf_rel_to_ids' ) ) {
    /**
     * Convierte el valor de un campo ACF Relationship a un array de post IDs únicos.
     *
     * @param mixed $value
     * @return int[]
     */
    function pm_acf_rel_to_ids( $value ) {
        $ids = array();
        if ( empty( $value ) ) return $ids;

        foreach ( (array) $value as $v ) {
            if ( is_numeric( $v ) ) {
                $ids[] = (int) $v;
            } elseif ( is_object( $v ) && ! empty( $v->ID ) ) {
                $ids[] = (int) $v->ID;
            }
        }

        return array_values( array_unique( array_filter( $ids ) ) );
    }
}

if ( ! function_exists( 'pm_safe_heading_tag' ) ) {
    /**
     * Valida y retorna un tag de heading permitido.
     *
     * @param string $tag
     * @return string
     */
    function pm_safe_heading_tag( $tag ) {
        $allowed = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' );
        $tag     = strtolower( trim( (string) $tag ) );
        return in_array( $tag, $allowed, true ) ? $tag : 'h3';
    }
}

if ( ! function_exists( 'pm_render_icon_picker' ) ) {
    /**
     * Renderiza un icono proveniente de un ACF icon picker.
     * Soporta: attachment array (con ID), URL directa, dashicons y SVG/HTML inline.
     *
     * @param array|string $icon
     */
    function pm_render_icon_picker( $icon ) {
        $img_classes  = 'content-carousel__meta-icon';
        $span_classes = 'content-carousel__meta-icon';

        if ( is_array( $icon ) ) {
            if ( ! empty( $icon['ID'] ) ) {
                $html = wp_get_attachment_image( (int) $icon['ID'], 'thumbnail', false, array(
                    'class'   => $img_classes,
                    'alt'     => '',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ) );
                if ( $html ) { echo $html; return; }
            }

            if ( ! empty( $icon['url'] ) ) {
                echo '<img class="' . esc_attr( $img_classes ) . '" src="' . esc_url( $icon['url'] ) . '" alt="" loading="lazy" decoding="async" />';
                return;
            }

            if ( ! empty( $icon['value'] ) && is_string( $icon['value'] ) ) {
                $icon = $icon['value'];
            } else {
                return;
            }
        }

        if ( is_string( $icon ) ) {
            $icon = trim( $icon );
            if ( $icon === '' ) return;

            if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
                echo '<img class="' . esc_attr( $img_classes ) . '" src="' . esc_url( $icon ) . '" alt="" loading="lazy" decoding="async" />';
                return;
            }

            if ( strpos( $icon, 'dashicons-' ) === 0 ) {
                echo '<span class="dashicons ' . esc_attr( $icon ) . ' ' . esc_attr( $span_classes ) . '" aria-hidden="true"></span>';
                return;
            }

            if ( strpos( $icon, '<svg' ) !== false || strpos( $icon, '<span' ) !== false || strpos( $icon, '<i' ) !== false ) {
                echo '<span class="' . esc_attr( $span_classes ) . '">' . wp_kses_post( $icon ) . '</span>';
                return;
            }

            echo '<span class="' . esc_attr( $span_classes ) . '">' . esc_html( $icon ) . '</span>';
        }
    }
}

if ( ! function_exists( 'pm_page_cover_image_id_from_group' ) ) {
    /**
     * Extrae el ID de un attachment desde un grupo ACF.
     *
     * @param array  $group Grupo ACF (array de campos).
     * @param string $key   Clave del campo imagen dentro del grupo.
     * @return int          Attachment ID o 0 si no existe.
     */
    function pm_page_cover_image_id_from_group( $group, $key ) {
        if ( ! is_array( $group ) || empty( $group[ $key ] ) || ! is_array( $group[ $key ] ) || empty( $group[ $key ]['ID'] ) ) {
            return 0;
        }
        return (int) $group[ $key ]['ID'];
    }
}

if ( ! function_exists( 'pm_normalize_acf_image' ) ) {
    /**
     * Normaliza un campo imagen ACF (ID, array o URL string) a [id, url, alt].
     *
     * @param int|array|string $image
     * @return array { int $id, string $url, string $alt }
     */
    function pm_normalize_acf_image( $image ) {
        $id  = 0;
        $url = '';
        $alt = '';

        if ( is_numeric( $image ) ) {
            $id = (int) $image;
        } elseif ( is_array( $image ) ) {
            $id  = ! empty( $image['ID'] ) ? (int) $image['ID'] : 0;
            $url = ! empty( $image['url'] ) ? (string) $image['url'] : '';
            $alt = ! empty( $image['alt'] ) ? (string) $image['alt'] : '';
        } elseif ( is_string( $image ) && $image !== '' ) {
            $url = $image;
        }

        if ( $id <= 0 && $url !== '' ) {
            $maybe_id = (int) attachment_url_to_postid( $url );
            if ( $maybe_id > 0 ) {
                $id = $maybe_id;
            }
        }

        return array( $id, $url, $alt );
    }
}

if ( ! function_exists( 'pm_page_cover_overlay_alpha' ) ) {
    /**
     * Calcula el valor alpha del overlay (0.00–0.75) a partir de los campos ACF.
     *
     * @param bool       $enable_overlay  Si el overlay está habilitado.
     * @param int|float  $overlay_opacity Opacidad en rango 0–75.
     * @return float                      Valor alpha CSS (0.00–0.75).
     */
    function pm_page_cover_overlay_alpha( $enable_overlay, $overlay_opacity ) {
        if ( ! $enable_overlay ) {
            return 0.0;
        }

        $overlay_opacity = is_numeric( $overlay_opacity ) ? (float) $overlay_opacity : 0.0;
        $overlay_opacity = max( 0, min( 75, $overlay_opacity ) );

        return $overlay_opacity / 100.0;
    }
}

if ( ! function_exists( 'pm_get_weather' ) ) {
    /**
     * Obtiene la temperatura actual de Playa Mujeres usando OpenWeatherMap.
     * Requiere la constante KEY_API_WEATHER definida en wp-config.php.
     * Cachea el resultado 30 minutos con un transient.
     *
     * @return array|null { int $celsius, int $fahrenheit } o null si falla.
     */
    function pm_get_weather() {
        $api_key = defined( 'KEY_API_WEATHER' ) ? KEY_API_WEATHER : 'a70f2c1055a585f45dc780ccb8275f9a';
        if ( $api_key === '' ) {
            return array( 'error' => 'no_api_key' );
        }

        $transient_key = 'pm_weather_cache';
        $cached        = get_transient( $transient_key );
        if ( $cached !== false ) {
            return $cached;
        }

        // Playa Mujeres, Quintana Roo, MX
        $url = add_query_arg( array(
            'lat'   => '21.3148',
            'lon'   => '-86.8467',
            'units' => 'metric',
            'appid' => $api_key,
        ), 'https://api.openweathermap.org/data/2.5/weather' );

        $response = wp_remote_get( $url, array(
            'timeout'   => 8,
            'sslverify' => true,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'error' => 'wp_error', 'message' => $response->get_error_message() );
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );

        if ( 200 !== $code ) {
            return array( 'error' => 'http_' . $code, 'body' => $body );
        }

        $data = json_decode( $body, true );
        if ( empty( $data['main']['temp'] ) ) {
            return array( 'error' => 'no_temp', 'body' => $body );
        }

        $celsius    = (int) round( (float) $data['main']['temp'] );
        $fahrenheit = (int) round( $celsius * 9 / 5 + 32 );

        $result = array(
            'celsius'    => $celsius,
            'fahrenheit' => $fahrenheit,
        );

        set_transient( $transient_key, $result, 30 * MINUTE_IN_SECONDS );

        return $result;
    }
}

if ( ! function_exists( 'pm_essence_render_section_spacing' ) ) {
    function pm_essence_render_section_spacing( $uid, $pt, $pb, $pt_mob, $pb_mob ) {
        if ( ! $uid ) return;

        $has_desktop = ( $pt !== '' && $pt !== false && $pt !== null ) ||
                       ( $pb !== '' && $pb !== false && $pb !== null );
        $has_mobile  = ( $pt_mob !== '' && $pt_mob !== false && $pt_mob !== null ) ||
                       ( $pb_mob !== '' && $pb_mob !== false && $pb_mob !== null );

        if ( ! $has_desktop && ! $has_mobile ) return;
        ?>
        <style>
            <?php if ( $has_desktop ) : ?>
            .<?php echo esc_attr( $uid ); ?> {
                <?php if ( $pt !== '' && $pt !== false && $pt !== null ) : ?>padding-top: <?php echo (int) $pt; ?>px;<?php endif; ?>
                <?php if ( $pb !== '' && $pb !== false && $pb !== null ) : ?>padding-bottom: <?php echo (int) $pb; ?>px;<?php endif; ?>
            }
            <?php endif; ?>
            <?php if ( $has_mobile ) : ?>
            @media (max-width: 768px) {
                .<?php echo esc_attr( $uid ); ?> {
                    <?php if ( $pt_mob !== '' && $pt_mob !== false && $pt_mob !== null ) : ?>padding-top: <?php echo (int) $pt_mob; ?>px;<?php endif; ?>
                    <?php if ( $pb_mob !== '' && $pb_mob !== false && $pb_mob !== null ) : ?>padding-bottom: <?php echo (int) $pb_mob; ?>px;<?php endif; ?>
                }
            }
            <?php endif; ?>
        </style>
        <?php
    }
}

if ( ! function_exists( 'pm_weather_ajax_handler' ) ) {
    function pm_weather_ajax_handler() {
        $weather = pm_get_weather();

        // Respuesta exitosa: tiene celsius y fahrenheit, sin clave 'error'
        if ( ! empty( $weather['celsius'] ) ) {
            wp_send_json_success( $weather );
        }

        // Respuesta de error: incluye diagnóstico
        wp_send_json_error( $weather, 503 );
    }
}

// ── Hotels Gallery helpers ─────────────────────────────────────────────────

if ( ! function_exists( 'pm_essence_gallery_esc_filter_class' ) ) {
    function pm_essence_gallery_esc_filter_class( $value, $use_prefix = true ) {
        return ( $use_prefix ? 'filter-' : '' ) . strtolower( str_replace( [ ' ', '&', "'", '"' ], '-', $value ) );
    }
}

if ( ! function_exists( 'pm_essence_gallery_implode_filter' ) ) {
    function pm_essence_gallery_implode_filter( $data, $sep, $use_prefix = true ) {
        if ( ! is_array( $data ) || empty( $data ) ) return '';
        return implode( $sep, array_map( function ( $item ) use ( $use_prefix ) {
            return pm_essence_gallery_esc_filter_class( $item, $use_prefix );
        }, $data ) );
    }
}

if ( ! function_exists( 'pm_essence_get_translation_json' ) ) {
    function pm_essence_get_translation_json( $name ) {
        $lang_path = PM_ESSENCE_TEMPLATE_DIR . "/languages/{$name}.json";
        if ( ! file_exists( $lang_path ) ) return [];

        $wpLang  = explode( '_', get_option( 'WPLANG' ) );
        $default = ( isset( $wpLang[0] ) && ! empty( $wpLang[0] ) ) ? $wpLang[0] : 'en';

        $lang_code = function_exists( 'pll_current_language' ) ? pll_current_language() : $default;
        if ( empty( $lang_code ) ) $lang_code = $default;

        $data = json_decode( file_get_contents( $lang_path ), true );
        if ( ! is_array( $data ) ) return [];

        $translation              = $data[ $lang_code ] ?? $data[ $default ] ?? [];
        $translation['default']   = $data[ $default ] ?? [];
        return $translation;
    }
}

if ( ! function_exists( 'pm_essence_translation_text' ) ) {
    function pm_essence_translation_text( $data, $key ) {
        return $data[ $key ] ?? $data['default'][ $key ] ?? '';
    }
}

if ( ! function_exists( 'pm_essence_get_translation_level' ) ) {
    function pm_essence_get_translation_level( $data, $level = [] ) {
        if ( empty( $level ) ) return $data;
        $tmp = [];
        foreach ( $level as $lv ) {
            $search_in          = empty( $tmp ) ? $data : $tmp;
            $tmp                = $search_in[ $lv ] ?? [];
            $tmp['default']     = $search_in['default'][ $lv ] ?? [];
        }
        return $tmp;
    }
}

if ( ! function_exists( 'pm_essence_get_hotels_gallery_data' ) ) {
    function pm_essence_get_hotels_gallery_data() {
        $hotels = get_posts( [
            'post_type'   => 'hotel',
            'post_status' => 'publish',
            'orderby'     => 'title',
            'order'       => 'ASC',
            'numberposts' => -1,
        ] );

        $lang_code   = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
        $lang_suffix = ( ! in_array( $lang_code, [ 'es', 'en', '' ], true ) ) ? '_' . $lang_code : '';

        $result = [
            'hotels'     => [],
            'categories' => [],
            'gallery'    => [],
        ];

        $seen_categories = [];

        foreach ( $hotels as $hotel ) {
            $filter_id = 'filter-resort-' . $hotel->ID;

            $result['hotels'][ $hotel->ID ] = [
                'title'       => $hotel->post_title,
                'filterId'    => $filter_id,
                'isAvailable' => false,
                'classes'     => [ $filter_id ],
            ];

            if ( ! intval( get_field( 'available_hotel_filter_gallery', $hotel->ID ), 10 ) ) continue;

            $result['hotels'][ $hotel->ID ]['isAvailable'] = true;

            $gallery_group = get_field( 'gallery_option_group', $hotel->ID );
            if ( empty( $gallery_group ) ) continue;

            foreach ( $gallery_group as $gg ) {
                if ( empty( $gg['hotel_image_gallery']['id'] ) ) continue;

                $cat_raw = $gg[ 'category_image_gallery' . $lang_suffix ] ?? $gg['category_image_gallery'] ?? [];
                $cats    = is_array( $cat_raw ) ? $cat_raw : array_filter( [ $cat_raw ] );

                $tag_raw = $gg['custom_tag_filter'] ?? [];
                $tags    = is_array( $tag_raw )
                    ? $tag_raw
                    : array_values( array_filter( explode( ',', (string) $tag_raw ) ) );

                foreach ( $cats as $cat ) {
                    if ( $cat && ! in_array( $cat, $seen_categories, true ) ) {
                        $seen_categories[]    = $cat;
                        $result['categories'][] = $cat;
                    }
                }

                $result['gallery'][] = [
                    'imageId'    => $gg['hotel_image_gallery']['id'],
                    'image'      => $gg['hotel_image_gallery']['url'],
                    'categories' => $cats,
                    'tags'       => $tags,
                    'resort'     => [
                        'title'   => $hotel->post_title,
                        'classes' => [ $filter_id ],
                    ],
                ];
            }
        }

        shuffle( $result['gallery'] );
        return $result;
    }
}

if ( ! function_exists( 'pm_page_has_section_layout' ) ) {
    function pm_page_has_section_layout( $layout_name ) {
        $post_id = get_queried_object_id();
        if ( ! $post_id ) return false;
        $sections = get_field( 'sections', $post_id );
        if ( ! is_array( $sections ) ) return false;
        foreach ( $sections as $section ) {
            if ( ( $section['acf_fc_layout'] ?? '' ) === $layout_name ) return true;
        }
        return false;
    }

    /**
     * Returns the reCAPTCHA notice HTML required by Google when hiding the badge.
     * Strings are translatable via Polylang (pll__) with fallback to wp i18n.
     */
    function pm_recaptcha_notice() {
        $t = function( $str ) {
            return function_exists('pll__') ? pll__($str) : __($str, 'pm-essence');
        };

        $privacy_link = '<a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">' . esc_html($t('Privacy Policy')) . '</a>';
        $terms_link   = '<a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">' . esc_html($t('Terms of Service')) . '</a>';

        $notice = sprintf(
            /* translators: %1$s = Privacy Policy link, %2$s = Terms of Service link */
            $t('This site is protected by reCAPTCHA and the Google %1$s and %2$s apply.'),
            $privacy_link,
            $terms_link
        );

        echo '<p class="recaptcha-notice">' . $notice . '</p>';
    }
}

