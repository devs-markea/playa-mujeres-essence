<?php
// template-parts/sections/content-highlights.php (loader de variantes)

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';

// Variante ACF: classic | essence
$layout_variant = get_sub_field('layout_variant');
$layout_variant = is_string($layout_variant) ? trim($layout_variant) : '';
if ($layout_variant === '') {
    $layout_variant = 'classic';
}

$allowed_variants = ['classic', 'essence'];
if (! in_array($layout_variant, $allowed_variants, true)) {
    $layout_variant = 'classic';
}

// Campos comunes
$heading_title          = get_sub_field('heading_title');
$heading_level_title    = get_sub_field('heading_level_title');
$heading_position_title = get_sub_field('heading_position_title') ?: 'center';
$highlights_items       = get_sub_field('highlights_items');
$show_intro_text        = (bool) get_sub_field('show_intro_text');
$intro_text             = get_sub_field('intro_text');
$show_description       = get_sub_field('show_description');
$description            = get_sub_field('description');
$show_image             = get_sub_field('show_image');
$image                  = get_sub_field('image');

// Heading tag principal
$heading_tag = pm_essence_heading_tag_or_null($heading_level_title, 'h2');
if (empty($heading_tag)) {
    $heading_tag = 'h2';
}

$position_css = is_string($heading_position_title) && $heading_position_title !== '' ? $heading_position_title : 'center';

// Imagen principal
$image_id  = (is_array($image) && ! empty($image['ID'])) ? (int) $image['ID'] : 0;
$image_alt = '';
if (is_array($image) && ! empty($image['alt'])) {
    $image_alt = $image['alt'];
} elseif (is_string($heading_title) && trim($heading_title) !== '') {
    $image_alt = $heading_title;
}

pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$variant_path = dirname(__DIR__) . '/layout-variants/content-highlights/' . $layout_variant . '.php';

if (file_exists($variant_path)) {
    require $variant_path;
} else {
    require dirname(__DIR__) . '/layout-variants/content-highlights/classic.php';
}
