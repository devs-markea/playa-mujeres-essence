<?php
$section_image = get_sub_field( 'image' );

$section_text = get_sub_field( 'description' );

$image_html = '';

if ( is_array( $section_image ) && ! empty( $section_image['ID'] ) ) {
    $image_html = wp_get_attachment_image(
        (int) $section_image['ID'],
        'large',
        false,
        array(
            'class' => 'blog-content-image-text-block__image',
        )
    );
} elseif ( is_numeric( $section_image ) ) {
    $image_html = wp_get_attachment_image(
        (int) $section_image,
        'large',
        false,
        array(
            'class' => 'blog-content-image-text-block__image',
        )
    );
}

if ( empty( $section_text ) ) {
    return;
}
?>

<section class="blog-content-image-text-block">
    <div class="row g-0 align-items-center blog-content-image-text-block__row">
        <?php if ( $image_html ) : ?>
            <div class="col-12 col-lg-5 pe-0 pe-md-4">
                <div class="blog-content-image-text-block__media">
                    <?php echo $image_html; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-12 col-md-7">
            <div class="blog-content-image-text-block__content">
                <?php if ( $section_text ) : ?>
                    <div class="blog-content-image-text-block__text">
                        <?php echo wp_kses_post( $section_text ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
