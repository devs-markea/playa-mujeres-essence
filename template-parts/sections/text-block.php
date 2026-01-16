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

$row_direction_class = ($variant === 'stacked') ? ' flex-column' : '';

?>

<section class="pm-text-block pm-text-block--<?= esc_attr(sanitize_title($variant)); ?>">
    <div class="container">
        <div class="row align-items-center text-center g-4 g-lg-5<?= esc_attr($row_direction_class); ?>">
            <div class="col-12 col-lg-5 pm-text-block__inner">
                <?php if (!empty($heading) && !empty($heading_tag)) : ?>
                <<?= esc_html($heading_tag); ?> class="pm-text-block__heading mb-0">
                <?= esc_html($heading); ?>
            </<?= esc_html($heading_tag); ?>>
            <?php endif; ?>
        </div>

        <div class="col-12 col-lg-7">
            <div class="pm-text-block__right border-lg-start ps-lg-5">
                <?php if (!empty($subheading) && !empty($subheading_tag)) : ?>
                <<?= esc_html($subheading_tag); ?> class="pm-text-block__subheading mb-2">
                <?= esc_html($subheading); ?>
            </<?= esc_html($subheading_tag); ?>>
            <?php endif; ?>

            <?php if (!empty($text)) : ?>
                <div class="pm-text-block__text">
                    <?= wp_kses_post($text); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($btn_url) && !empty($btn_title)) : ?>
                <div class="pm-text-block__cta mt-3">
                    <a class="btn btn-primary"
                       href="<?= esc_url($btn_url); ?>"
                        <?= !empty($btn_target) ? 'target="' . esc_attr($btn_target) . '"' : ''; ?>
                        <?= ($btn_target === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                        <?= esc_html($btn_title); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
    </div>
</section>


<style>
    /* Base */
    .pm-text-block {
        padding: 48px 0;
        margin: 104px 0;
    }

    .pm-text-block__heading {
        margin: 0;
        font-weight: 400;
        letter-spacing: 0.5px;
    }

    .pm-text-block__subheading {
        margin: 0 0 12px 0;
        font-weight: 400;
        letter-spacing: 0.5px;
    }

    .pm-text-block__text p:last-child {
        margin-bottom: 0;
    }

    .pm-text-block__cta {
        margin-top: 16px;
    }

    /* =========================
       STACKED (Vertical)
       ========================= */
    .pm-text-block--stacked .pm-text-block__heading {
        margin-bottom: 12px;
    }

    .pm-text-block--stacked .pm-text-block__subheading {
        margin-bottom: 12px;
    }

    .pm-text-block--stacked .pm-text-block__right {
        border-left: 0;
        padding-left: 0;
    }

    /* En stacked, si quieres que el contenido no quede tan ancho */
    @media (min-width: 992px) {
        .pm-text-block--columns .pm-text-block__heading {
            color: var(--pm-secondary-900);
            font-family: var(--pm-font-secondary);
            font-size: 32px;
            font-style: italic;
            font-weight: 500;
            line-height: normal;
            letter-spacing: 2px;
        }

        .pm-text-block--columns .pm-text-block__inner {
            border-right: 1px solid rgba(0, 0, 0, 0.2);
        }
    }

    .pm-text-block--columns .pm-text-block__right {
        border-left: 0;
        padding-left: 0;
    }

</style>