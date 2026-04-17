<?php
// template-parts/sections/content-collection.php (solo loader de variantes)

$layout_variant   = get_sub_field('layout_variant');
$heading          = get_sub_field('heading');
$heading_level    = get_sub_field('heading_level'); // none|h1..h6
$description      = get_sub_field('description');
$items            = get_sub_field('items');
$collection_items = get_sub_field('collection_items');
$heading_tag      = pm_essence_heading_tag_or_null($heading_level, 'h1');

$variant = is_string($layout_variant) ? trim(str_replace('_', '-', $layout_variant)) : '';

$allowed_variants = array('cascade', 'dynamic-collection');
if ($variant === '' || ! in_array($variant, $allowed_variants, true)) {
    $variant = 'cascade';
}

$variant_template_path = dirname(__DIR__) . '/layout-variants/content-collection/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/content-collection/cascade.php';
}
