<?php
// template-parts/sections/content-highlights.php

$heading_title          = get_sub_field('heading_title');
$heading_level_title    = get_sub_field('heading_level_title');
$heading_position_title = get_sub_field('heading_position_title') ?: 'center';
$highlights_items       = get_sub_field('highlights_items');
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

// Imagen principal (bottom)
$image_id  = (is_array($image) && ! empty($image['ID'])) ? (int) $image['ID'] : 0;
$image_alt = '';
if (is_array($image) && ! empty($image['alt'])) {
    $image_alt = $image['alt'];
} elseif (is_string($heading_title) && trim($heading_title) !== '') {
    $image_alt = $heading_title;
}
?>

<section class="content-highlights">

    <div class="container px-0 px-md-4">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">

                <?php if ($heading_title) : ?>
                <div class="content-highlights__header">
                    <<?php echo esc_html($heading_tag); ?> class="content-highlights__title content-highlights__title--<?php echo esc_attr($position_css); ?>">
                    <?php echo esc_html($heading_title); ?>
                </<?php echo esc_html($heading_tag); ?>>
            </div>
            <?php endif; ?>

            <?php if ($highlights_items) : ?>
            <div class="content-highlights__items row g-3">
                <?php foreach ($highlights_items as $item) :
                $item_heading       = $item['heading']       ?? '';
                $item_heading_level = $item['heading_level'] ?? 'none';
                $item_description   = $item['description']   ?? '';
                $item_image         = $item['image']         ?? null;

                $item_tag = pm_essence_heading_tag_or_null($item_heading_level, 'h3');
                if (empty($item_tag)) {
                    $item_tag = 'h3';
                }

                $item_image_id  = (is_array($item_image) && ! empty($item_image['ID'])) ? (int) $item_image['ID'] : 0;
                $item_image_alt = (is_array($item_image) && ! empty($item_image['alt'])) ? $item_image['alt'] : $item_heading;
                ?>
                <div class="col-12 col-md-4 content-highlights__item">
                    <div class="content-highlights__item-inner">

                        <?php if ($item_image_id) : ?>
                            <div class="content-highlights__item-icon" aria-hidden="true">
                                <?php echo wp_get_attachment_image(
                                        $item_image_id,
                                        'large',
                                        false,
                                        array(
                                                'class'    => 'content-highlights__item-icon-img',
                                                'alt'      => $item_image_alt,
                                                'loading'  => 'lazy',
                                                'decoding' => 'async',
                                        )
                                ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="content-highlights__item-body">
                            <?php if ($item_heading) : ?>
                            <<?php echo esc_html($item_tag); ?> class="content-highlights__item-heading">
                            <?php echo esc_html($item_heading); ?>
                        </<?php echo esc_html($item_tag); ?>>
                    <?php endif; ?>

                        <?php if ($item_description) : ?>
                            <div class="content-highlights__item-description">
                                <?php echo wp_kses_post($item_description); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($show_description && $description) : ?>
            <div class="content-highlights__description">
                <?php echo wp_kses_post($description); ?>
            </div>
        <?php endif; ?>

        <?php if ($show_image) : ?>
            <div class="content-highlights__media">
                <?php if ($image_id) : ?>
                    <?php echo wp_get_attachment_image(
                            $image_id,
                            'full',
                            false,
                            array(
                                    'class'    => 'content-highlights__media-img',
                                    'alt'      => $image_alt,
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                            )
                    ); ?>
                <?php elseif (is_array($image) && ! empty($image['url'])) : ?>
                    <img src="<?php echo esc_url($image['url']); ?>"
                         alt="<?php echo esc_attr($image_alt); ?>"
                         class="content-highlights__media-img"
                         loading="lazy"
                         decoding="async">
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
        </div>
    </div>

</section>
<style>
    /* ============================================================
       CONTENT HIGHLIGHTS  |  mobile-first
    ============================================================ */

    .content-highlights {
        padding: 64px 0;
    }

    /* ── Header ── */
    .content-highlights__header {
        margin-bottom: 40px;
        padding: 0 32px;
    }

    .content-highlights__title {
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        font-size: 24px;
        letter-spacing: 2px;
        color: var(--pm-secondary-900);
        margin: 0;
    }

    .content-highlights__title--left   { text-align: start; }
    .content-highlights__title--center { text-align: start; }
    .content-highlights__title--right  { text-align: start; }

    /* ── Items ── */
    .content-highlights__items {
        margin-bottom: 40px;
        padding: 0 32px;
    }

    .content-highlights__item {
        margin-bottom: 32px;
    }

    .content-highlights__item-inner {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 16px;
    }

    /* ── Icon ── */
    .content-highlights__item-icon {
        flex-shrink: 0;
        width: 40px;
    }

    .content-highlights__item-icon-img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: contain;
    }

    /* ── Body ── */
    .content-highlights__item-body {
        flex: 1;
        min-width: 0;
    }

    .content-highlights__item-heading {
        font-family: var(--pm-font-primary);
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: var(--pm-secondary-900);
        margin: 0 0 8px;
        text-transform: uppercase;
    }

    .content-highlights__item-description {
        font-size: 16px;
        font-weight: 300;
        line-height: 1.6;
        color: var(--pm-secondary-900);
    }

    .content-highlights__item-description p {
        margin: 0;
    }

    /* ── Description global ── */
    .content-highlights__description {
        font-size: 16px;
        line-height: 1.7;
        color: var(--pm-secondary-900);
        margin-bottom: 40px;
        padding: 0 32px;
    }

    /* ── Media ── */
    .content-highlights__media {
        width: 100%;
        margin-top: 40px;
    }

    .content-highlights__media-img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    .content-highlights__description h2,
    .content-highlights__description h3,
    .content-highlights__description h4,
    .content-highlights__description h5,
    .content-highlights__description h6{
        color: var(---pm-secondary-900);
        font-size: 16px;
        font-weight: 500;
    }

    .content-highlights__description a {
        position: relative;
        border-bottom: none !important;
        padding-left: 0;
        padding-right: 0;
        width: fit-content;
        margin: 0 auto;
        padding-bottom: 0;
        color: var(--pm-secondary-900);
    }

    .content-highlights__description a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: var(--pm-secondary-900);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .content-highlights__description a:hover::after {
        transform: scaleX(1);
    }

    /* ============================================================
       DESKTOP >= 992px
    ============================================================ */
    @media (min-width: 992px) {
        .content-highlights__header {
            padding: 0;
        }

        .content-highlights__items {
            padding: 0;
        }

        .content-highlights__description {
            padding: 0;
        }

        .content-highlights {
            padding: 64px 0 0;
        }

        .content-highlights__title {
            font-size: 32px;
        }

        .content-highlights__item {
            margin-bottom: 0;
        }

        .content-highlights__title--left   { text-align: left; }
        .content-highlights__title--center { text-align: center; }
        .content-highlights__title--right  { text-align: right; }
    }
</style>
