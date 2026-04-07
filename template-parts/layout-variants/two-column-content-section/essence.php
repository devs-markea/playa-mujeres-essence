<?php
/**
 * template-parts/layout-variants/two-column-content-section/essence.php
 *
 * Espera que el loader (two-column-content-section.php) ya tenga disponibles:
 * - $title, $title_tag
 * - $description
 * - $show_button, $button_title, $button_url, $button_target
 * - $layout_direction ('left' | 'right')
 * - $variant (mobile_content_style)
 * - $image_media_id, $image_media_alt, $image_media
 */
?>
<section data-anim="slide-up delay-2" class="container px-0 px-md-4">
    <div class="row g-0">
        <div class="col-12 col-md-10 mx-auto">
            <div class="row g-0 g-md-4 two-column-layout two-column-layout--variant-essence <?php echo $layout_direction == 'left' ? 'is-left' : 'is-right' ?>">

                <div class="col-12 col-lg-6 col-content">
                    <div class="row g-0">
                        <div class="col-12 col-lg-9">
                            <div class="two-column-layout__content two-column-layout__content-variant-<?php echo esc_attr($variant); ?>">
                                <?php if ( $title ) : ?>
                                    <<?php echo esc_html($title_tag); ?> class="mb-3 two-column-layout__heading-essence">
                                        <?php echo esc_html( $title ); ?>
                                    </<?php echo esc_html($title_tag); ?>>
                                <?php endif; ?>

                                <?php if ( $description ) : ?>
                                    <div class="two-column-layout__content-description mb-3">
                                        <?php echo wp_kses_post( $description ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $show_button && $button_url && $button_title ) : ?>
                                    <a
                                        href="<?php echo esc_url( $button_url ); ?>"
                                        target="<?php echo esc_attr( $button_target ); ?>"
                                        class="btn btn-primary btn-border-bottom-black">
                                        <?php echo esc_html( $button_title ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-image">
                    <div class="two-column-layout__content-image">
                        <?php if ( $image_media_id ) : ?>
                            <?php
                            echo wp_get_attachment_image(
                                $image_media_id,
                                'full',
                                false,
                                array(
                                    'class'    => 'img-fluid',
                                    'alt'      => $image_media_alt,
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                )
                            );
                            ?>
                        <?php elseif ( is_array( $image_media ) && ! empty( $image_media['url'] ) ) : ?>
                            <img src="<?php echo esc_url( $image_media['url'] ); ?>"
                                 alt="<?php echo esc_attr( $image_media_alt ); ?>"
                                 class="img-fluid"
                                 loading="lazy"
                                 decoding="async">
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<style>
    /* ===== Mobile (default) ===== */
    .two-column-layout--variant-essence {
        display: flex;
        flex-direction: column-reverse;
        align-items: center;
    }

    .two-column-layout--variant-essence .two-column-layout__content-variant-card {
        text-align: center;
        margin: 0 32px 42px;
        padding: 24px;
        box-shadow: 0px 0px 20px -3px rgba(0, 0, 0, 0.25);
        position: relative;
        top: -2rem;
        background: #fff;
        z-index: 1;
    }

    .two-column-layout--variant-essence .two-column-layout__content-variant-inline {
        text-align: start;
        padding: 4rem 2rem;
    }

    .two-column-layout--variant-essence .two-column-layout__content .two-column-layout__heading-essence {
        color: var(--pm-secondary-900);
        font-size: 18px;
        font-weight: 400;
    }

    .two-column-layout--variant-essence .two-column-layout__content-description {
        text-align: start;
        font-size: 1rem;
        font-weight: 300;
        color: var(--pm-secondary-900);
    }

    .two-column-layout--variant-essence .two-column-layout__content-description strong {
        font-weight: 500;
    }

    .two-column-layout--variant-essence .two-column-layout__content-image {
        width: 100%;
        height: auto;
    }

    .two-column-layout--variant-essence .two-column-layout__content-image img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    /* ===== Breakpoint >= 556px ===== */
    @media (min-width: 556px) {
        .two-column-layout--variant-essence .two-column-layout__content {
            min-height: 226px;
        }
    }

    /* ===== Desktop >= 992px ===== */
    @media (min-width: 992px) {
        .two-column-layout--variant-essence {
            flex-direction: row;
            align-items: center;
            padding-bottom: 96px;
        }

.two-column-layout--variant-essence .two-column-layout__content {
            margin: 0;
            padding: 0;
            box-shadow: none;
            position: static;
            top: 0;
            align-content: center;
        }

        .two-column-layout--variant-essence .two-column-layout__content-image {
            position: static;
            top: 0;
        }

        .two-column-layout--variant-essence.is-left {
            flex-direction: row-reverse;
        }

        .two-column-layout--variant-essence.is-right {
            flex-direction: row;
        }
    }
</style>
