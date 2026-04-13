<?php
/**
 * template-parts/layout-variants/page-cover/classic.php
 *
 * Requiere que el loader (page-cover.php) ya tenga disponibles:
 * - $heading, $heading_level, $heading_tag, $description
 * - $layout_height
 * - $classic_group (get_sub_field('classic'))
 * - $hero_alt
 * - helpers: pm_page_cover_image_id_from_group(), pm_page_cover_overlay_alpha()
 */

$bg_desktop_id = pm_page_cover_image_id_from_group($classic_group, 'cover_image_desktop');
$bg_mobile_id  = pm_page_cover_image_id_from_group($classic_group, 'cover_image_mobile');

$enable_overlay  = (is_array($classic_group) && isset($classic_group['enable_overlay'])) ? (bool) $classic_group['enable_overlay'] : false;
$overlay_opacity = (is_array($classic_group) && isset($classic_group['overlay_opacity'])) ? $classic_group['overlay_opacity'] : 0;
$overlay_alpha   = pm_page_cover_overlay_alpha($enable_overlay, $overlay_opacity);

$classes = array(
    'page-cover',
    'page-cover--variant-classic',
    'page-cover--height-' . $layout_height,
);
?>

<?php /* Script bloqueante: oculta el body antes de que se pinte nada */ ?>
<script>document.body.style.opacity='0';window.__pageCoverReady=false;</script>

<section class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="page-cover__media" aria-hidden="true">
        <?php if ($bg_desktop_id || $bg_mobile_id) : ?>
            <picture class="page-cover__picture">
                <?php if ($bg_mobile_id) : ?>
                    <source
                        media="(max-width: 767px)"
                        srcset="<?php echo esc_url(wp_get_attachment_image_url($bg_mobile_id, 'full')); ?>"
                    />
                <?php endif; ?>

                <?php
                $img_id = $bg_desktop_id ? $bg_desktop_id : $bg_mobile_id;

                echo wp_get_attachment_image(
                    $img_id,
                    'full',
                    false,
                    array(
                        'class'         => 'page-cover__image',
                        'alt'           => esc_attr($hero_alt),
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                        'decoding'      => 'async',
                    )
                );
                ?>
            </picture>
        <?php endif; ?>

        <?php if ($enable_overlay && $overlay_alpha > 0) : ?>
            <div class="page-cover__overlay" style="background-color: rgba(0,0,0,<?php echo esc_attr((string) $overlay_alpha); ?>);"></div>
        <?php endif; ?>
    </div>

    <div class="page-cover__content container">
        <?php if (! empty($heading_tag) && ! empty($heading)) : ?>
        <<?php echo tag_escape($heading_tag); ?> class="page-cover__heading">
        <?php echo esc_html($heading); ?>
    </<?php echo tag_escape($heading_tag); ?>>
    <?php elseif (! empty($heading)) : ?>
        <div class="page-cover__heading page-cover__heading--text-only">
            <?php echo esc_html($heading); ?>
        </div>
    <?php endif; ?>

    <?php if (! empty($description)) : ?>
        <div class="page-cover__description">
            <?php echo esc_html($description); ?>
        </div>
    <?php endif; ?>
    </div>
</section>
<style>
    .page-cover--variant-classic{
        position: relative;
        overflow: visible;
        display: flex;
        align-items: stretch;
        min-height: 50vh;
    }
    .page-cover--variant-classic .page-cover__media{
        position: absolute;
        inset: 0;
        z-index: 0;
        overflow: hidden;
    }
    .page-cover--variant-classic .page-cover__media .page-cover__overlay{
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .page-cover--variant-classic .page-cover__media .page-cover__image{
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
        object-position: top;
        will-change: transform;
    }
    .page-cover--variant-classic .page-cover__content{
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: end;
        margin-bottom: 32px;
        z-index: 3;
        color: #fff;
    }
    .page-cover--variant-classic .page-cover__heading{
        font-family: var(--pm-font-secondary);
        font-size: 24px;
        font-style: italic;
        font-weight: 500;
        line-height: normal;
        letter-spacing: 2px;
        margin-bottom: 0.5rem;
    }

    @media (min-width: 992px){
        .page-cover--variant-classic{
            min-height: 75vh;
        }
        .page-cover--variant-classic .page-cover__media .page-cover__image{
            object-position: 36%;
        }
        .page-cover--variant-classic .page-cover__heading {
            font-size: 40px;
            margin-bottom: 1rem;
        }
    }
</style>
<script>
(function () {
    var img = document.querySelector('.page-cover--variant-classic .page-cover__image');
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
