<?php
// template-parts/sections/content-carousel.php (solo loader de variantes)

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
$allowed_variants = array('classic', 'essence');
if (! in_array($variant, $allowed_variants, true)) {
    $variant = 'classic';
}


// Heading tag (none => null)
$heading               = get_sub_field('heading');
$heading_level         = get_sub_field('heading_level'); // none|h1..h6
$heading_tag = pm_essence_heading_tag_or_null($heading_level, 'h1');
$hero_alt    = $heading ? $heading : get_the_title();


pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$variant_template_path = dirname(__DIR__) . '/layout-variants/content-carousel/' . $variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/content-carousel/classic.php';
}
?>

