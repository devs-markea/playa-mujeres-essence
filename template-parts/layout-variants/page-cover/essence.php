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
<script>document.body.style.opacity='0';window.__pageCoverReady=false;</script>

<section data-anim="slide-up delay-2" class="<?php echo esc_attr(implode(' ', $classes)); ?><?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>" data-force-header-theme="menu">
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
                        <?php echo wp_get_attachment_image($secondary_image_id, 'medium', false, [
                                'class'         => 'page-cover__image page-cover__image--secondary',
                                'alt'           => esc_attr($hero_alt),
                                'loading'       => 'eager',
                                'decoding'      => 'async',
                                'fetchpriority' => 'high',
                                'sizes'         => '(min-width: 992px) 40vw, 100vw',
                        ]); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="page-cover__essence-inner-right">
                <div class="page-cover__essence-media-a" aria-hidden="true">
                    <?php if ($primary_image_id) : ?>
                        <?php echo wp_get_attachment_image($primary_image_id, 'large', false, [
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

<script>
(function () {
    // Solo la imagen primary dispara el revealPage — la secondary se ignora
    var img = document.querySelector('.page-cover--variant-essence .page-cover__image--primary');
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