<?php
// Variante ACF: classic | gallery-slider
$layout_variant = get_sub_field('layout_variant');

// Default
$variant = is_string($layout_variant) ? trim($layout_variant) : '';
if ($variant === '') {
    $variant = 'classic';
}

// Whitelist de variantes permitidas
$allowed_variants = array('classic', 'gallery-slider');
if (!in_array($variant, $allowed_variants, true)) {
    $variant = 'classic';
}

$variant_template_path = dirname(__DIR__) . '/layout-variants/images-carousel/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/images-carousel/classic.php';
}