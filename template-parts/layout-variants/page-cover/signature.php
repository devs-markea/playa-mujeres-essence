<?php
/**
 * template-parts/layout-variants/page-cover/signature.php
 *
 * Variante Signature: heading + description a la izquierda,
 * seguido de una imagen principal a full width.
 *
 * Requiere que el loader (page-cover.php) ya tenga disponibles:
 * - $heading, $heading_level, $heading_tag, $description
 * - $signature_group (get_sub_field('signature'))
 * - $hero_alt
 * - helper: pm_page_cover_image_id_from_group()
 */

$image_id = pm_page_cover_image_id_from_group($signature_group, 'image');

$classes = array(
    'page-cover',
    'page-cover--variant-signature',
    'page-cover--height-' . $layout_height,
);
?>

<script>document.body.style.opacity='0';window.__pageCoverReady=false;</script>

<section data-anim="slide-up delay-2" class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-force-header-theme="menu">

    <div class="container px-0 px-md-4">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <?php if (! empty($heading) || ! empty($description)) : ?>
                <div class="page-cover-signature__header">

                    <?php if (! empty($heading_tag) && ! empty($heading)) : ?>
                    <<?php echo tag_escape($heading_tag); ?> class="page-cover-signature__heading">
                    <?php echo esc_html($heading); ?>
                </<?php echo tag_escape($heading_tag); ?>>
            <?php elseif (! empty($heading)) : ?>
                <div class="page-cover-signature__heading page-cover-signature__heading--text-only">
                    <?php echo esc_html($heading); ?>
                </div>
            <?php endif; ?>

                <?php if (! empty($description)) : ?>
                    <div class="page-cover-signature__description">
                        <?= wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>
            </div>
        </div>

    <?php if ($image_id) : ?>
        <div class="page-cover-signature__media">
            <?php echo wp_get_attachment_image(
                    $image_id,
                    'full',
                    false,
                    array(
                            'class'         => 'page-cover-signature__img',
                            'alt'           => esc_attr($hero_alt),
                            'loading'       => 'eager',
                            'decoding'      => 'async',
                            'fetchpriority' => 'high',
                            'sizes'         => '(min-width: 992px) 50vw, 100vw',
                    )
            ); ?>
        </div>
    <?php endif; ?>
    </div>

</section>

<style>
    /* ============================================================
       PAGE COVER — SIGNATURE
       Heading + description / una imagen principal full width
    ============================================================ */

    .page-cover--variant-signature {
        display: flex;
        flex-direction: column;
        align-items: start;
        justify-content: start;
        height: calc(80svh - 134px);
        margin-top: 134px;
    }

    /* ── Header (texto) ── */
    .page-cover-signature__header {
        margin: 40px 0;
        padding-right: 2rem;
        padding-left: 2rem;
    }

    .page-cover-signature__heading {
        font-family: var(--pm-font-secondary);
        font-size: 26px;
        font-style: italic;
        font-weight: var(--fw-medium);
        line-height: 1.2;
        letter-spacing: 1px;
        color: var(--pm-secondary-900);
        margin: 0 0 16px;
    }

    .page-cover-signature__heading--text-only {
        font-style: normal;
    }

    .page-cover-signature__description {
        font-size: 15px;
        font-weight: var(--fw-light);
        line-height: 1.6;
        color: var(--pm-secondary-900);
        max-width: 680px;
        margin: 0;
    }

    /* ── Imagen principal ── */
    .page-cover-signature__media {
        width: 100%;
        overflow: hidden;
    }

    .page-cover-signature__img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        display: block;
    }

    /* ============================================================
       DESKTOP ≥ 992px
    ============================================================ */
    @media (min-width: 992px) {
        .page-cover--variant-signature {
            min-height: calc(100vh - 92px);
            margin-top: 92px;
            align-items: center;
            justify-content: center;
        }

        .page-cover-signature__header {
            margin: 0 0 48px;
        }

        .page-cover-signature__heading {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .page-cover-signature__description {
            font-size: 16px;
        }

        .page-cover-signature__img {
            height: 520px;
        }
    }
</style>
<script>
    (function () {
        // Solo la imagen primary dispara el revealPage — la secondary se ignora
        var img = document.querySelector('.page-cover--variant-signature .page-cover-signature__img');
        if (!img) {
            window.__pageCoverReady = true;
            if (typeof window.__revealPage === 'function') window.__revealPage();
            return;
        }
        function onImageReady() {
            window.__pageCoverReady = true;
            if (typeof window.__revealPage === 'function') window.__revealPage();
        }
        if (img.complete && img.naturalWidth > 0) {
            onImageReady();
        } else {
            img.addEventListener('load',  onImageReady, { once: true });
            img.addEventListener('error', onImageReady, { once: true });
        }
    })();
</script>