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

<section class="<?php echo esc_attr(implode(' ', $classes)); ?>"
         data-force-header-theme="menu">
    <div class="page-cover__essence">
        <div class="container px-0 px-md-5">
            <div class="page-cover__essence-grid row g-0 g-md-5">
                <div class="col-12 col-lg-5">
                    <div class="page-cover__essence-inner-left">
                        <div class="page-cover__essence-heading row">
                            <div class="col-7 col-lg-8 mx-auto">
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
                        </div>

                        <div class="page-cover__essence-media-b" aria-hidden="true">
                        <?php if ($secondary_image_id) : ?>
                            <?php
                            echo wp_get_attachment_image(
                                    $secondary_image_id,
                                    'large',
                                    false,
                                    array(
                                            'class'   => 'page-cover__image page-cover__image--secondary',
                                            'alt'     => esc_attr($hero_alt),
                                            'loading' => 'lazy',
                                    )
                            );
                            ?>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            <div class="col-12 col-lg-7">
                <div class="page-cover__essence-inner-right">
                    <div class="page-cover__essence-media-a" aria-hidden="true">
                        <?php if ($primary_image_id) : ?>
                            <?php
                            echo wp_get_attachment_image(
                                    $primary_image_id,
                                    'large',
                                    false,
                                    array(
                                            'class'   => 'page-cover__image page-cover__image--primary',
                                            'alt'     => esc_attr($hero_alt),
                                            'loading' => 'lazy',
                                    )
                            );
                            ?>
                        <?php endif; ?>
                    </div>

                    <div class="page-cover__essence-description row">
                        <div class="col-11 col-lg-8 mx-auto">
                            <?php if (! empty($description)) : ?>
                                <div class="page-cover__description">
                                    <?=  wp_kses_post($description); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <style>
        /* Essence layout (ideal: mover a tu CSS del theme) */
        .page-cover__heading {
            font-family: var(--pm-font-secondary);
            font-size: 24px;
            font-style: italic;
            font-weight: 500;
            line-height: normal;
            letter-spacing: 2px;
            color: var(--pm-secondary-900);
        }
        .page-cover__heading,
        .page-cover__heading--text-only{
            text-align: center;
        }
        .page-cover--variant-essence .page-cover__essence{
            padding: 7.5rem 0;
            /*display: flex;*/
            /*flex-direction: column;*/
            /*justify-content: end;*/
            /*height: 100vh;*/
        }
        .page-cover__essence-inner-right{
            display: flex;
            flex-direction: column-reverse;
            align-items: end;
            height: 100%;
        }

        .page-cover__essence-media-a {
            width: 65%;
        }
        .page-cover__essence-media-b {
            width: 100%;
            height: auto;
        }

        .page-cover__essence-inner-left .page-cover__essence-heading,
        .page-cover__essence-inner-right .page-cover__essence-description{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            margin: 2rem 0;
        }
        .pm-text-block__decorative-image{
            display: none;
        }

        .page-cover--variant-essence .page-cover__image,
        .page-cover--variant-essence .page-cover__image img{
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        @media (min-width: 992px){

            .pm-text-block__decorative-image{
                display: block;
            }
            .page-cover__heading {
                font-size: 40px;
            }
            .page-cover__essence-inner-left,
            .page-cover__essence-inner-right{
                flex-direction: column;
                align-items: start;
            }


            .page-cover__essence-media-a {
                width: 100%;
                max-height: 420px;
            }
            .page-cover__essence-media-b {
                width: 100%;
               height: 326px;
            }

            .page-cover__essence-inner-left .page-cover__essence-heading,
            .page-cover__essence-inner-right .page-cover__essence-description{
                margin: 4rem 0;
            }

            .page-cover--variant-essence .page-cover__essence{
                padding: 8.25rem 0;
                /*justify-content: center;*/
            }
        }
    </style>
</section>