<?php
// template-parts/sections/page-cover.php (solo loader de variantes)

// Variante ACF: classic | essence
$layout_variant = get_sub_field('layout_variant');

// Default
$variant = is_string($layout_variant) ? trim($layout_variant) : '';
if ($variant === '') {
    $variant = 'classic';
}

// Whitelist de variantes permitidas
$allowed_variants = array('classic', 'essence');
if (! in_array($variant, $allowed_variants, true)) {
    $variant = 'classic';
}

// Layout height: compact | tall | full_height  (disponible para las variantes)
$layout_height = get_sub_field('layout_height');
$layout_height = is_string($layout_height) ? trim($layout_height) : '';
$allowed_heights = array('compact', 'tall', 'full_height');
if ($layout_height === '' || ! in_array($layout_height, $allowed_heights, true)) {
    $layout_height = 'tall';
}

// Groups ACF (disponibles para las variantes)
$classic_group = get_sub_field('classic'); // array
$essence_group = get_sub_field('essence'); // array

if (! function_exists('pm_page_cover_image_id_from_group')) {
    function pm_page_cover_image_id_from_group($group, $key) {
        if (! is_array($group) || empty($group[$key]) || ! is_array($group[$key]) || empty($group[$key]['ID'])) {
            return 0;
        }
        return (int) $group[$key]['ID'];
    }
}

if (! function_exists('pm_page_cover_overlay_alpha')) {
    function pm_page_cover_overlay_alpha($enable_overlay, $overlay_opacity) {
        if (! $enable_overlay) {
            return 0.0;
        }

        $overlay_opacity = is_numeric($overlay_opacity) ? (float) $overlay_opacity : 0.0; // 0..75
        if ($overlay_opacity < 0) { $overlay_opacity = 0; }
        if ($overlay_opacity > 75) { $overlay_opacity = 75; }

        return $overlay_opacity / 100.0; // 0.00 .. 0.75
    }
}


$variant_template_path = dirname(__DIR__) . '/layout-variants/page-cover/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/page-cover/classic.php';
}
