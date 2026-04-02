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

