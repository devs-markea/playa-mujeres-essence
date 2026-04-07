<?php
/**
 * template-parts/layout-variants/page-cover/essence.php
 *
 * Espera que el loader (page-cover.php) ya tenga disponibles:
 * - $heading, $heading_tag, $description
 * - $layout_height
 * - $essence_group (get_sub_field('essence'))
 * - $hero_alt
 * - helper: pm_page_cover_image_id_from_group()
 */

$primary_image_id   = pm_page_cover_image_id_from_group($essence_group, 'primary_image');
$secondary_image_id = pm_page_cover_image_id_from_group($essence_group, 'secondary_image');

$classes = array(
    'page-cover',
    'page-cover--variant-essence',
    'page-cover--height-' . $layout_height,
);
?>

<section data-anim="slide-up delay-2" class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-force-header-theme="menu">
    <div class="page-cover__essence">
        <div class="container px-0 px-md-4">
            <div class="page-cover__essence-grid row g-0 gx-md-4">

                <div class="page-cover__essence-inner-left">
                    <div class="page-cover__essence-heading">
                        <?php if (! empty($heading_tag) && ! empty($heading)) : ?>
                        <<?php echo tag_escape($heading_tag); ?> class="page-cover__heading">
                        <?php echo esc_html($heading); ?>
                    </<?php echo tag_escape($heading_tag); ?>>
                    <?php elseif (! empty($heading)) : ?>
                        <div class="page-cover__heading page-cover__heading--text-only">
                            <?php echo esc_html($heading); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="page-cover__essence-media-b" aria-hidden="true">
                    <?php if ($secondary_image_id) : ?>
                        <?php echo wp_get_attachment_image($secondary_image_id, 'large', false, [
                                'class'         => 'page-cover__image page-cover__image--secondary',
                                'alt'           => esc_attr($hero_alt),
                                'loading'       => 'lazy',
                                'decoding'      => 'async',
                                'fetchpriority' => 'low',
                                'sizes'         => '(min-width: 992px) 40vw, 70vw',
                        ]); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="page-cover__essence-inner-right">
                <div class="page-cover__essence-media-a" aria-hidden="true">
                    <?php if ($primary_image_id) : ?>
                        <?php echo wp_get_attachment_image($primary_image_id, 'medium', false, [
                                'class'         => 'page-cover__image page-cover__image--primary',
                                'alt'           => esc_attr($hero_alt),
                                'loading'       => 'eager',
                                'decoding'      => 'async',
                                'fetchpriority' => 'high',
                                'sizes'         => '(min-width: 992px) 50vw, 100vw',
                        ]); ?>
                    <?php endif; ?>
                </div>
                <div class="page-cover__essence-description">
                    <?php if (! empty($description)) : ?>
                        <?= wp_kses_post($description); ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    </div>
</section>
<style>
    /* ============================================================
       PAGE COVER – ESSENCE  |  mobile-first
       Mobile:  heading → img-primary(a) → description → img-secondary(b)
       Desktop: [left: heading + img-secondary(b)] [right: img-primary(a) + description]
    ============================================================ */

    /* ── Section ── */
    .page-cover--variant-essence {
        display: flex;
        flex-direction: column;
        align-items: start;
        justify-content: start;
        min-height: calc(100vh - 134px);
        margin-top: 134px;
    }

    /* ── Heading (mobile) ── */
    .page-cover__heading {
        font-family: var(--pm-font-secondary);
        font-size: 24px;
        font-style: italic;
        font-weight: 500;
        line-height: normal;
        letter-spacing: 2px;
        margin: 0;
        color: var(--pm-secondary-900);
    }

    .page-cover__heading,
    .page-cover__heading--text-only {
        text-align: center;
    }

    /* ── Images ── */
    .page-cover--variant-essence .page-cover__image,
    .page-cover--variant-essence .page-cover__image img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .pm-text-block__decorative-image { display: none; }

    /* ── Grid: dissolve columnas en mobile para controlar order ── */
    .page-cover__essence-grid {
        align-items: flex-start;
    }

    .page-cover__essence-inner-left,
    .page-cover__essence-inner-right {
        display: contents;
    }

    /* ── Order mobile ── */
    .page-cover__essence-heading {
        order: 1;
        width: 80%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 2rem auto;
    }

    .page-cover__essence-media-a {
        order: 2;
        width: 100%;
        height: 304px;
    }


    .page-cover__essence-description strong {
        font-weight: 500;
    }

    .page-cover__essence-description {
        order: 3;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 24px 32px 32px;
    }


    .page-cover__essence-media-b {
        order: 4;
        width: 70%;
        height: 180px;
        margin: 0 0 0 auto;
    }

    /* ============================================================
       DESKTOP ≥ 992px  — restaurar layout de dos columnas
    ============================================================ */
    @media (min-width: 992px) {

        .pm-text-block__decorative-image { display: block; }

        /* Section */
        .page-cover--variant-essence {
            min-height: calc(100vh - 92px);
            margin-top: 92px;
            align-items: center;
            justify-content: center;
        }

        /* Heading */
        .page-cover__heading {
            font-size: 40px;
        }

        /* Grid */
        .page-cover__essence-grid {
            align-items: start;
        }

        /* Restaurar columnas como flex reales */
        .page-cover__essence-inner-left {
            display: flex;
            flex-direction: column;
            align-items: start;
            flex: 0 0 40%;
            gap: 40px;
            padding-top: 60px;
        }

        .page-cover__essence-inner-right {
            display: flex;
            flex-direction: column;
            align-items: start;
            flex: 1;
            /*gap: 28px;*/
            height: 100%;
        }

        /* Resetear order */
        .page-cover__essence-heading    {
            order: unset;
            width: 80%;
            margin: 4rem auto; }
        .page-cover__essence-media-a    { order: unset; width: 100%; height: 420px; }
        .page-cover__essence-description{ order: unset;
            width: 80%;
            margin: 4rem auto 0;
        }
        .page-cover__essence-media-b    { order: unset; width: 100%; height: 326px; margin: 0; }
    }
</style>