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

// Heading tag (none => null)
$heading               = get_sub_field('heading');
$heading_level         = get_sub_field('heading_level'); // none|h1..h6
$description           = get_sub_field('description');

$heading_tag = pm_essence_heading_tag_or_null($heading_level, 'h1');
$hero_alt    = $heading ? $heading : get_the_title();


$variant_template_path = dirname(__DIR__) . '/layout-variants/page-cover/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/page-cover/classic.php';
}
