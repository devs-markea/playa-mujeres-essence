<?php
$image_media       = get_sub_field( 'media' );
$title = get_sub_field('title');
$description = get_sub_field('description');
$button_settings = get_sub_field('button_settings');
$button_title  = '';
$button_url    = '';
$button_target = '_self';
$layout_direction = get_sub_field('desktop_layout_direction') ?: 'right';

if ( $button_settings ) {

    $show_button = ! empty( $button_settings['show_button'] );

    if ( $show_button && ! empty( $button_settings['button_link'] ) ) {
        $button_link   = $button_settings['button_link'];
        $button_title  = $button_link['title']  ?? '';
        $button_url    = $button_link['url']    ?? '';
        $button_target = $button_link['target'] ?? '_self';
    }
}

$image_media_id  = ( is_array( $image_media ) && ! empty( $image_media['ID'] ) ) ? (int) $image_media['ID'] : 0;
$image_media_alt = '';
if ( is_array( $image_media ) && ! empty( $image_media['alt'] ) ) {
    $image_media_alt = $image_media['alt'];
} elseif ( is_string( $title ) && trim( $title ) !== '' ) {
    $image_media_alt = $title;
}
?>
<section class="two-column-layout g-0 <?php echo $layout_direction == 'left' ? 'is-left' : 'is-right' ?>">

    <div class="col-12 col-lg-6 col-content">
        <div class="row g-0">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="two-column-layout__content">
                    <?php if ( $title ) : ?>
                        <h2 class="mb-3"><?php echo esc_html( $title ); ?></h2>
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

</section>

