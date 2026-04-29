<?php
// template-parts/sections/text-block.php
// ACF layout_variant : stacked | columns | textonly
// ACF alignment      : left | center | right
// ACF width          : full | narrow  (solo textonly)

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

// ACF sub fields
$heading          = get_sub_field('heading');
$heading_level    = get_sub_field('heading_level');
$subheading       = get_sub_field('subheading');
$subheading_level = get_sub_field('subheading_level');
$text             = get_sub_field('text');
$button_link      = get_sub_field('button_link');
$layout_variant   = get_sub_field('layout_variant');
$alignment        = get_sub_field('alignment');
$width            = get_sub_field('width');

// Decorative image
$enable_decorative_image = (bool) get_sub_field('enable_decorative_image');
$decorative_image        = get_sub_field('decorative_image');

$heading_tag    = pm_essence_heading_tag_or_null($heading_level, 'h2');
$subheading_tag = pm_essence_heading_tag_or_null($subheading_level, 'h3');

// Button fields
$btn_url    = '';
$btn_title  = '';
$btn_target = '';
if (is_array($button_link)) {
    $btn_url    = $button_link['url']    ?? '';
    $btn_title  = $button_link['title']  ?? '';
    $btn_target = $button_link['target'] ?? '';
}

// Variant
$allowed_variants = ['stacked', 'columns', 'textonly'];
$variant = is_string($layout_variant) ? trim($layout_variant) : '';
if (! in_array($variant, $allowed_variants, true)) {
    $variant = 'stacked';
}

// Alignment → Bootstrap text utility
$allowed_alignments = ['left', 'center', 'right'];
$alignment = is_string($alignment) ? trim($alignment) : '';
if (! in_array($alignment, $allowed_alignments, true)) {
    $alignment = 'left';
}
$align_class = [
    'left'   => 'text-start',
    'center' => 'text-center',
    'right'  => 'text-end',
][$alignment];

// textonly: mobile/md siempre left, desde lg aplica el alignment del ACF.
$align_class_textonly = [
    'left'   => 'text-start',
    'center' => 'text-start text-lg-center',
    'right'  => 'text-start text-lg-end',
][$alignment];

// Content presence flags
$has_heading    = (! empty($heading) && ! empty($heading_tag));
$has_subheading = (! empty($subheading) && ! empty($subheading_tag));
$has_text       = ! empty($text);
$has_button     = (! empty($btn_url) && ! empty($btn_title));

// Decorative image ID
$decorative_image_id = 0;
if ($enable_decorative_image && ! empty($decorative_image)) {
    if (is_array($decorative_image) && ! empty($decorative_image['ID'])) {
        $decorative_image_id = (int) $decorative_image['ID'];
    } elseif (is_numeric($decorative_image)) {
        $decorative_image_id = (int) $decorative_image;
    }
}

$has_decor_image = ($decorative_image_id > 0);
$has_left        = ($has_heading || $has_decor_image);
$has_right       = ($has_subheading || $has_text || $has_button);
?>

<section data-anim="slide-up delay-2"
         class="pm-text-block pm-text-block--<?= esc_attr($variant); ?> pm-text-block--<?= esc_attr($alignment); ?><?= ! $has_decor_image ? ' pm-text-block--no-decor' : ''; ?><?= $section_uid ? ' ' . esc_attr($section_uid) : ''; ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">

                <?php if ($variant === 'columns') : ?>
                    <!-- Columns: heading left | content right with divider -->
                    <div class="row align-items-center g-4 g-lg-1">

                        <?php if ($has_left) : ?>
                            <div class="col-12 col-lg-5 pm-text-block__inner <?= esc_attr($align_class); ?>">
                                <?php if ($has_heading) : ?>
                                    <<?= esc_html($heading_tag); ?> class="pm-text-block__heading mb-0">
                                        <?= esc_html($heading); ?>
                                    </<?= esc_html($heading_tag); ?>>
                                <?php endif; ?>

                                <?php if ($has_decor_image) : ?>
                                    <div class="pm-text-block__decorative-image" aria-hidden="true">
                                        <?= wp_get_attachment_image($decorative_image_id, 'full', false, [
                                            'class'   => 'img-fluid',
                                            'alt'     => '',
                                            'loading' => 'lazy',
                                        ]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($has_right) : ?>
                            <div class="col">
                                <div class="pm-text-block__right<?= $has_left ? ' border-lg-start ps-lg-5' : ''; ?> <?= esc_attr($align_class); ?>">
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
                                        <div class="pm-text-block__cta">
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

                <?php elseif ($variant === 'textonly') : ?>
                    <!-- Text Only: width full (col-12) o narrow (col-6 centrado) -->
                    <?php $textonly_col = ($width === 'narrow') ? 'col-12 col-lg-8 mx-auto' : 'col-12'; ?>
                    <div class="row g-0">
                        <div class="<?= esc_attr($textonly_col); ?>">
                            <div class="pm-text-block__text-only <?= esc_attr($align_class_textonly); ?>">
                                <?php if ($has_text) : ?>
                                    <div class="pm-text-block__text">
                                        <?= wp_kses_post($text); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($has_button) : ?>
                                    <div class="pm-text-block__cta">
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
                    </div>

                <?php else : ?>
                    <!-- Stacked: solo el heading sigue alignment (col-6 si center), body siempre col-12 text-start -->
                    <?php $heading_col = ($alignment === 'center') ? 'col-12 col-lg-6 mx-auto' : 'col-12'; ?>

                    <?php if ($has_heading || $has_decor_image) : ?>
                        <div class="row g-0 mb-4">
                            <div class="<?= esc_attr($heading_col); ?> <?= esc_attr($align_class); ?>">
                                <?php if ($has_heading) : ?>
                                    <<?= esc_html($heading_tag); ?> class="pm-text-block__heading">
                                        <?= esc_html($heading); ?>
                                    </<?= esc_html($heading_tag); ?>>
                                <?php endif; ?>

                                <?php if ($has_decor_image) : ?>
                                    <div class="pm-text-block__decorative-image" aria-hidden="true">
                                        <?= wp_get_attachment_image($decorative_image_id, 'full', false, [
                                            'class'   => 'img-fluid',
                                            'alt'     => '',
                                            'loading' => 'lazy',
                                        ]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row g-0">
                        <div class="col-12 pm-text-block__body text-start">
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
                                <div class="pm-text-block__cta">
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
</section>
