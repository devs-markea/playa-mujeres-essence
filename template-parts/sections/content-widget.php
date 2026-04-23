<?php
// template-parts/sections/content-widget.php

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$heading_title  = get_sub_field('heading_title');
$heading_level  = get_sub_field('heading_level');
$widget_items   = get_sub_field('widget_items');

$heading_tag = pm_essence_heading_tag_or_null($heading_level, 'h2');
if (empty($heading_tag)) {
    $heading_tag = 'h2';
}

if (! $widget_items) return;
?>

<section data-anim="slide-up delay-2" class="content-widget<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">

                <?php if ($heading_title) : ?>
                    <div class="content-widget__header">
                        <<?php echo esc_html($heading_tag); ?> class="content-widget__title">
                            <?php echo esc_html($heading_title); ?>
                        </<?php echo esc_html($heading_tag); ?>>
                    </div>
                <?php endif; ?>

                <div class="content-widget__grid row g-3">
                    <?php foreach ($widget_items as $item) :
                        $eyebrow     = $item['eyebrow']     ?? '';
                        $title       = $item['title']       ?? '';
                        $description = $item['description'] ?? '';
                        $meta        = $item['meta']        ?? '';
                        $icon        = $item['icon']        ?? null;

                        $icon_id  = (is_array($icon) && ! empty($icon['ID'])) ? (int) $icon['ID'] : 0;
                        $icon_alt = (is_array($icon) && ! empty($icon['alt'])) ? $icon['alt'] : '';
                    ?>
                        <div class="col-12 col-md-4">
                            <div class="content-widget__card">

                                <div class="content-widget__card-top">
                                    <?php if ($eyebrow) : ?>
                                        <span class="content-widget__eyebrow"><?php echo esc_html($eyebrow); ?></span>
                                    <?php endif; ?>

                                    <?php if ($icon_id || $meta) : ?>
                                        <div class="content-widget__meta">
                                            <?php if ($icon_id) : ?>
                                                <?php echo wp_get_attachment_image($icon_id, 'thumbnail', false, [
                                                    'class'    => 'content-widget__meta-icon',
                                                    'alt'      => $icon_alt,
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                                ]); ?>
                                            <?php endif; ?>
                                            <?php if ($meta) : ?>
                                                <span class="content-widget__meta-label"><?php echo esc_html($meta); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($title) : ?>
                                    <div class="content-widget__card-title"><?php echo esc_html($title); ?></div>
                                <?php endif; ?>

                                <?php if ($description) : ?>
                                    <div class="content-widget__card-description">
                                        <?php echo wp_kses_post($description); ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</section>
