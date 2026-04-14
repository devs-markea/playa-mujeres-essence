<?php
// template-parts/sections/content-showcase.php
// ACF layout_variant: classic | essence

$layout_variant = get_sub_field('layout_variant');
$layout_variant = is_string($layout_variant) ? trim($layout_variant) : '';

$allowed_variants = ['classic', 'essence'];
if (! in_array($layout_variant, $allowed_variants, true)) {
    $layout_variant = 'classic';
}

// Campos comunes disponibles para ambas variantes
$title       = get_sub_field('title');
$title_level = get_sub_field('title_level');
$description = get_sub_field('description');
$items       = get_sub_field('items');

$title_tag = pm_essence_heading_tag_or_null($title_level, 'h2');
if (empty($title_tag)) {
    $title_tag = 'h2';
}

if (! $items) return;

$variant_path = dirname(__DIR__) . '/layout-variants/content-showcase/' . $layout_variant . '.php';

if (file_exists($variant_path)) {
    require $variant_path;
} else {
    require dirname(__DIR__) . '/layout-variants/content-showcase/classic.php';
}
