<?php

function pm_add_svg_to_items_with_children( $title, $item, $args, $depth ) {
    if ( ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
        return $title;
    }

    $svg = '
        <svg class="pm-menu-chevron" fill="none" width="16" height="16" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <path d="M9.75 4.125L6 7.875L2.25 4.125" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

    ';

    // Envolvemos el texto en un span para poder alinearlo mejor
    $title = '<span class="pm-menu-text">' . $title . '</span>' . $svg;

    return $title;
}
add_filter( 'nav_menu_item_title', 'pm_add_svg_to_items_with_children', 10, 4 );

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

