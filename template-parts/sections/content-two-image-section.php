<?php
// Campos ACF principales
$main_image       = get_sub_field( 'main_image' );
$decorative_image = get_sub_field( 'decorative_image' );
$description      = get_sub_field( 'description' );
$button_settings   = get_sub_field( 'button_settings' );

$show_button = $button_settings['show_button'] ?? false;
$button_link = $button_settings['button_link'] ?? null;

?>
<section class="content-two-image-section">
    <div class="container p-0 px-md-3">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <div class="row align-items-stretch g-0 gx-md-4">
                    <div class="col-12 col-lg-6 order-2 order-lg-1">
                        <div class="content-two-image-section__content h-100">
                            <?php if ( $description ) : ?>
                                <div class="content-two-image-section__description mb-3">
                                    <?php echo wp_kses_post( $description ); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $show_button && ! empty( $button_link['url'] ) ) : ?>
                                <?php
                                $btn_url    = esc_url( $button_link['url'] );
                                $btn_title  = esc_html( $button_link['title'] ?: 'Learn more' );
                                $btn_target = ! empty( $button_link['target'] ) ? $button_link['target'] : '_self';
                                $btn_rel    = ( '_blank' === $btn_target ) ? ' rel="noopener noreferrer"' : '';
                                ?>

                                <a href="<?php echo $btn_url; ?>"
                                   target="<?php echo esc_attr( $btn_target ); ?>"<?php echo $btn_rel; ?>
                                   class="btn btn-primary btn-border-bottom-black">
                                    <?php echo $btn_title; ?>
                                </a>
                            <?php endif; ?>
                            <div class="content-two-image-section__decorative-image">
                                <?php if ( $decorative_image ) : ?>
                                    <img src="<?php echo esc_url( $decorative_image['url'] ); ?>"
                                         alt="<?php echo esc_attr( $decorative_image['alt'] ); ?>"
                                         class="img-fluid">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 order-1 order-lg-2">
                        <div class="content-two-image-section__image">
                            <?php if ( $main_image ) : ?>
                                <img src="<?php echo esc_url( $main_image['url'] ); ?>"
                                     alt="<?php echo esc_attr( $main_image['alt'] ); ?>"
                                     class="img-fluid">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


