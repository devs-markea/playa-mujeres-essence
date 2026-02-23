<?php
// template-parts/sections/text-block.php

// ACF sub fields (Flexible Content layout: text_block)
$heading          = get_sub_field('heading');
$heading_level    = get_sub_field('heading_level');
$subheading       = get_sub_field('subheading');
$subheading_level = get_sub_field('subheading_level');
$text             = get_sub_field('text');
$button_link      = get_sub_field('button_link');
$layout_variant   = get_sub_field('layout_variant');

// NEW: Decorative image (below title)
$enable_decorative_image = (bool) get_sub_field('enable_decorative_image');
$decorative_image        = get_sub_field('decorative_image'); // puede ser array (con ID) o ID según config ACF

$heading_tag    = pm_essence_heading_tag_or_null($heading_level, 'h2');
$subheading_tag = pm_essence_heading_tag_or_null($subheading_level, 'h3');

$btn_url    = '';
$btn_title  = '';
$btn_target = '';
if (is_array($button_link)) {
    $btn_url    = isset($button_link['url']) ? $button_link['url'] : '';
    $btn_title  = isset($button_link['title']) ? $button_link['title'] : '';
    $btn_target = isset($button_link['target']) ? $button_link['target'] : '';
}

// ACF layout_variant: columns | stacked
$variant = is_string($layout_variant) ? trim($layout_variant) : '';
if ($variant === '') {
    $variant = 'stacked';
}

// Helpers de contenido para alinear correctamente cuando faltan título/descr.
$has_heading     = (! empty($heading) && ! empty($heading_tag));
$has_decor_image = ($enable_decorative_image && ! empty($decorative_image));
$has_left        = ($has_heading || $has_decor_image);

$has_subheading = (! empty($subheading) && ! empty($subheading_tag));
$has_text       = (! empty($text));
$has_button     = (! empty($btn_url) && ! empty($btn_title));
$has_right      = ($has_subheading || $has_text || $has_button);

// Soporte para ACF image como array o ID.
$decorative_image_id = 0;
if ($has_decor_image) {
    if (is_array($decorative_image) && ! empty($decorative_image['ID'])) {
        $decorative_image_id = (int) $decorative_image['ID'];
    } elseif (is_numeric($decorative_image)) {
        $decorative_image_id = (int) $decorative_image;
    }
}

// Clases dependientes de variante y “excepciones” cuando falta título/descr.
$row_direction_class = ($variant === 'stacked') ? ' flex-column' : '';
$row_align_class     = ($variant === 'stacked') ? ' text-center' : ' text-center text-lg-start';

$left_col_class  = 'col-12 col-lg-5 text-start text-md-center pm-text-block__inner';
$right_col_class = 'col';

$right_inner_class = 'pm-text-block__right';
if ($variant === 'columns' && $has_left && $has_right) {
    $right_inner_class .= ' border-lg-start ps-lg-5';
}

if (! $has_left && $has_right) {
    $right_col_class = 'col-4 mx-auto text-center';
}
if ($has_left && ! $has_right) {
    $left_col_class = 'col-4 mx-auto text-center';
}

if ($has_decor_image && $variant === 'stacked'){
    $text_block_class = 'text-start';
} else {
    $text_block_class = 'text-start text-md-center';
}
?>

<section class="pm-text-block pm-text-block--<?= esc_attr(sanitize_title($variant)); ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <div class="row align-items-center g-4 g-lg-1<?= esc_attr($row_direction_class); ?><?= esc_attr($row_align_class); ?>">

                    <?php if ($has_left) : ?>
                    <div class="<?= esc_attr($left_col_class); ?>">
                        <?php if ($has_heading) : ?>
                        <<?= esc_html($heading_tag); ?> class="pm-text-block__heading mb-0">
                        <?= esc_html($heading); ?>
                    </<?= esc_html($heading_tag); ?>>
                <?php endif; ?>

                    <?php if ($has_heading) : ?>
                        <?php if ($decorative_image_id) : ?>
                            <div class="pm-text-block__decorative-image" aria-hidden="true">
                                <?php
                                echo wp_get_attachment_image(
                                        $decorative_image_id,
                                        'full',
                                        false,
                                        array(
                                                'class'   => 'img-fluid',
                                                'alt'     => '',
                                                'loading' => 'lazy',
                                        )
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php endif;?>
                </div>
                <?php endif; ?>

                    <?php if ($has_right) : ?>
                <div class="<?= esc_attr($right_col_class); ?>">
                    <div class="<?= esc_attr($right_inner_class); ?> <?= esc_attr($text_block_class); ?>">
                        <?php if ($has_subheading) : ?>
                        <<?= esc_html($subheading_tag); ?> class="pm-text-block__subheading">
                        <?= esc_html($subheading); ?>
                    </<?= esc_html($subheading_tag); ?>>
                    <?php endif; ?>

                    <?php if ($has_text) : ?>
                        <div class="pm-text-block__text">
                            <?= wp_kses_post($text); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_button) : ?>
                        <div class="pm-text-block__cta mt-3">
                            <a class="btn btn-primary"
                               href="<?= esc_url($btn_url); ?>"
                                    <?= ! empty($btn_target) ? 'target="' . esc_attr($btn_target) . '"' : ''; ?>
                                    <?= ($btn_target === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                                <?= esc_html($btn_title); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>