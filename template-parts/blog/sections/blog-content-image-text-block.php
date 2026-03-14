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
            <div class="col-12 col-lg-5 pe-4">
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

<style>


    .blog-content-image-text-block__row {
        row-gap: 32px;
    }

    .blog-content-image-text-block__media {
        overflow: hidden;
        background: #e7e2da;
    }

    .blog-content-image-text-block__image {
        display: block;
        width: 100%;
        height: auto;
        aspect-ratio: 1.45 / 1;
        object-fit: cover;
    }



    .blog-content-image-text-block__title {
        margin: 0 0 28px;
        color: #323232;
        font-size: 20px;
        font-weight: 500;
        line-height: 1.35;
    }

    .blog-content-image-text-block__text,
    .blog-content-image-text-block__text p {
        color: #323232;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.5;
    }

    .blog-content-image-text-block__text p {
        margin-bottom: 24px;
    }

    .blog-content-image-text-block__text p:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 991.98px) {
        .blog-content-image-text-block {
            margin: 56px 0;
        }

        .blog-content-image-text-block__content {
            padding-top: 0;
        }
    }
</style>
