<?php
// template-parts/sections/page-cover.php (solo loader de variantes)

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';

// Variante ACF: classic | essence
$layout_variant = get_sub_field('layout_variant');

// Default
$variant = is_string($layout_variant) ? trim($layout_variant) : '';
if ($variant === '') {
    $variant = 'classic';
}

// Whitelist de variantes permitidas
$allowed_variants = array('classic', 'essence', 'signature');
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
$classic_group   = get_sub_field('classic');   // array
$essence_group   = get_sub_field('essence');   // array
$signature_group = get_sub_field('signature'); // array

// Heading tag (none => null)
$heading               = get_sub_field('heading');
$heading_level         = get_sub_field('heading_level'); // none|h1..h6
$description           = get_sub_field('description');

$heading_tag = pm_essence_heading_tag_or_null($heading_level, 'h1');
$hero_alt    = $heading ? $heading : get_the_title();


pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$variant_template_path = dirname(__DIR__) . '/layout-variants/page-cover/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/page-cover/classic.php';
}
