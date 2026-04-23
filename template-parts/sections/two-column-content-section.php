<?php
// template-parts/sections/two-column-content-section.php (solo loader de variantes)

// Variante ACF:         classic | essence
// show_section_header:  switch — activa section_title y section_description
// heading_font_style:   primary | secondary (solo essence)

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';

$layout_variant = get_sub_field('if_layout_variant');
$layout_variant = is_string($layout_variant) ? trim($layout_variant) : '';
if ($layout_variant === '') {
    $layout_variant = 'classic';
}

$allowed_variants = array('classic', 'essence');
if (! in_array($layout_variant, $allowed_variants, true)) {
    $layout_variant = 'classic';
}

// Campos comunes
$image_media         = get_sub_field('media');
$layout_direction    = get_sub_field('desktop_layout_direction') ?: 'right';
$variant             = get_sub_field('mobile_content_style');

// Header de sección (opcional, controlado por switch)
$show_section_header  = (bool) get_sub_field('show_section_header');
$section_title        = $show_section_header ? get_sub_field('section_title')        : '';
$section_title_level  = $show_section_header ? get_sub_field('section_title_level')  : '';
$section_description  = $show_section_header ? get_sub_field('section_description')  : '';
$section_title_tag    = pm_essence_heading_tag_or_null($section_title_level, 'h2');
if (empty($section_title_tag)) {
    $section_title_tag = 'h2';
}

// Campos de contenido del two-column (planos, sin grupos)
$title           = get_sub_field('title');
$title_level     = get_sub_field('title_level');
$description     = get_sub_field('description');
$button_settings = get_sub_field('button_settings');

$heading_font_style = get_sub_field('heading_font_style'); // solo essence: primary | secondary
$heading_font_style = in_array($heading_font_style, ['primary', 'secondary'], true) ? $heading_font_style : 'secondary';

$variant = is_string($variant) ? trim($variant) : '';
if ($variant === '') {
    $variant = 'card';
}

$title_tag = pm_essence_heading_tag_or_null($title_level, 'h2');
if (empty($title_tag)) {
    $title_tag = 'h2';
}

$show_button   = false;
$button_title  = '';
$button_url    = '';
$button_target = '_self';

if ($button_settings) {
    $show_button = ! empty($button_settings['show_button']);

    if ($show_button && ! empty($button_settings['button_link'])) {
        $button_link   = $button_settings['button_link'];
        $button_title  = $button_link['title']  ?? '';
        $button_url    = $button_link['url']    ?? '';
        $button_target = $button_link['target'] ?? '_self';
    }
}

$image_media_id  = (is_array($image_media) && ! empty($image_media['ID'])) ? (int) $image_media['ID'] : 0;
$image_media_alt = '';
if (is_array($image_media) && ! empty($image_media['alt'])) {
    $image_media_alt = $image_media['alt'];
} elseif (is_string($title) && trim($title) !== '') {
    $image_media_alt = $title;
}

pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$variant_template_path = dirname(__DIR__) . '/layout-variants/two-column-content-section/' . $layout_variant . '.php';

if (file_exists($variant_template_path)) {
    require $variant_template_path;
} else {
    require dirname(__DIR__) . '/layout-variants/two-column-content-section/classic.php';
}
