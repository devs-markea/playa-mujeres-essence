<?php

$image        = get_sub_field('image'); // ACF image array
$layout_width = get_sub_field('layout_width');
$button_link  = get_sub_field('button_link'); // ACF link array
$layout_height = get_sub_field('layout_height'); // tall | standard | compact

$layout_height = is_string($layout_height) ? trim($layout_height) : '';
if (!in_array($layout_height, array('tall', 'standard', 'compact'), true)) {
    $layout_height = 'standard';
}

$height_modifier_class = 'things-events--' . $layout_height;

$image_id  = (is_array($image) && !empty($image['ID'])) ? (int) $image['ID'] : 0;
$image_alt = (is_array($image) && !empty($image['alt'])) ? $image['alt'] : '';

?>

<section class="blog-content-image-block <?php echo esc_attr($height_modifier_class); ?>">
    <?php if ($image_id): ?>
        <div class="row g-0">
            <div class="col-12">
                <div class="blog-content-image-block__image-wrapper">
                    <?php
                    echo wp_get_attachment_image(
                        $image_id,
                        'large',
                        false,
                        array(
                            'class'    => 'blog-content-image-block__image',
                            'alt'      => $image_alt,
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                            'sizes'    => '(min-width: 768px) 83vw, 100vw',
                        )
                    );
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<style>
    /* Imagen */
    .blog-content-image-block__image-wrapper{
        position: relative;
        width: 100%;
        overflow: hidden;
        padding-top: 56.25%; /* default 16:9 fallback */
    }

    .blog-content-image-block__image{
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    @supports (aspect-ratio: 16 / 9){
        .blog-content-image-block__image-wrapper{
            padding-top: 0;
            aspect-ratio: 16 / 9;
        }
    }

    .blog-content-image-block__image{
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .blog-content-image-block {
        margin: 2rem 0;
    }

</style>
