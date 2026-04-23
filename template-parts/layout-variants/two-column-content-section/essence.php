<?php
/**
 * template-parts/layout-variants/two-column-content-section/essence.php
 *
 * Espera que el loader (two-column-content-section.php) ya tenga disponibles:
 * - $section_title, $section_description  (header de sección, arriba del layout)
 * - $title, $title_tag                    (heading dentro del two-column)
 * - $description                          (descripción dentro del two-column)
 * - $show_button, $button_title, $button_url, $button_target
 * - $layout_direction ('left' | 'right')
 * - $variant (mobile_content_style)
 * - $image_media_id, $image_media_alt, $image_media
 * - $heading_font_style ('primary' | 'secondary')
 */
?>
<section data-anim="slide-up delay-2" class="container px-0 px-md-4<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="row g-0">
        <div class="col-12 col-md-10 mx-auto">

            <?php if ($section_title || $section_description) : ?>
                <div class="two-column-layout__section-header">
                    <?php if ($section_title) : ?>
                        <<?php echo esc_html($section_title_tag); ?> class="two-column-layout__section-title"><?php echo esc_html($section_title); ?></<?php echo esc_html($section_title_tag); ?>>
                    <?php endif; ?>
                    <?php if ($section_description) : ?>
                        <div class="two-column-layout__section-description"><?php echo wp_kses_post($section_description); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row g-0 g-md-5 two-column-layout two-column-layout--variant-essence <?php echo $layout_direction == 'left' ? 'is-left' : 'is-right' ?>">

                <div class="col-12 col-lg-6 col-content">
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="two-column-layout__content two-column-layout__content-variant-<?php echo esc_attr($variant); ?>">
                                <?php if ( $title ) : ?>
                                    <<?php echo esc_html($title_tag); ?> class="mb-3 two-column-layout__heading-essence two-column-layout__heading-essence--font-<?php echo esc_attr($heading_font_style); ?>">
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
