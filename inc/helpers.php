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

if (!function_exists('pm_get_hotel_primary_showcase_logo')) {
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