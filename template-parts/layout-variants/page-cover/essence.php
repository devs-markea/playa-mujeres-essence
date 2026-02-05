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

<section class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="page-cover__media" aria-hidden="true">
        <?php if ($primary_image_id) : ?>
            <div class="page-cover__primary">
                <?php
                echo wp_get_attachment_image(
                    $primary_image_id,
                    'full',
                    false,
                    array(
                        'class'   => 'page-cover__image page-cover__image--primary',
                        'alt'     => esc_attr($hero_alt),
                        'loading' => 'lazy',
                    )
                );
                ?>
            </div>
        <?php endif; ?>

        <?php if ($secondary_image_id) : ?>
            <div class="page-cover__secondary">
                <?php
                echo wp_get_attachment_image(
                    $secondary_image_id,
                    'full',
                    false,
                    array(
                        'class'   => 'page-cover__image page-cover__image--secondary',
                        'alt'     => esc_attr($hero_alt),
                        'loading' => 'lazy',
                    )
                );
                ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="page-cover__content">
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