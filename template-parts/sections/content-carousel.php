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


// Heading tag (none => null)
$heading               = get_sub_field('heading');
$heading_level         = get_sub_field('heading_level'); // none|h1..h6
$heading_tag = pm_essence_heading_tag_or_null($heading_level, 'h1');
$hero_alt    = $heading ? $heading : get_the_title();


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


$variant_template_path = dirname(__DIR__) . '/layout-variants/content-carousel/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/content-carousel/classic.php';
}
?>

