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

                return [
                    'type' => 'youtube',
                    'id' => $id,
                    'embed_url' => "https://www.youtube.com/embed/$id?autoplay=1&mute=1&loop=1&playlist=$id",
                    'thumbnail' => "https://img.youtube.com/vi/$id/maxresdefault.jpg",
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
