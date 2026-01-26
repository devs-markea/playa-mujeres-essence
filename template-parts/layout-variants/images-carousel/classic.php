<?php
$title           = get_sub_field('title');
$description     = get_sub_field('description');
$button_settings = get_sub_field('button_settings');
$images          = get_sub_field('images');


$overlay = get_sub_field('overlay'); // group array

$enable_overlay  = !empty($overlay['enable_images_overlay']);
$overlay_opacity = isset($overlay['images_overlay_opacity']) ? $overlay['images_overlay_opacity'] : 0;

// normalización 0..75
$overlay_opacity = is_numeric($overlay_opacity) ? (float) $overlay_opacity : 0.0;
if ($overlay_opacity < 0) { $overlay_opacity = 0; }
if ($overlay_opacity > 75) { $overlay_opacity = 75; }

$overlay_alpha = $enable_overlay ? ($overlay_opacity / 100.0) : 0.0;

?>

<section class="images-carousel images-carousel--classic">
    <div class="images-carousel__header px-4 px-md-0">
        <div class="row g-0">
            <div class="col-12 col-md-8 mx-auto">
                <?php if ($title): ?>
                    <h2 class="images-carousel__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description): ?>
                    <div class="images-carousel__description">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($button_settings['show_button']) && !empty($button_settings['button_link'])): ?>
                    <a href="<?php echo esc_url($button_settings['button_link']['url']); ?>"
                       target="<?php echo esc_attr($button_settings['button_link']['target'] ?: '_self'); ?>"
                       class="images-carousel__button btn btn-primary btn-border-bottom-black">
                        <?php echo esc_html($button_settings['button_link']['title']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($images): ?>
        <div class="images-carousel__media swiper px-4 px-md-0" data-images-carousel-variant="classic">
            <div class="images-carousel__wrapper swiper-wrapper">

                <?php foreach ($images as $item):
                    $img  = isset($item['image']) ? $item['image'] : null;
                    $link = isset($item['image_link']) ? $item['image_link'] : '';

                    if (empty($img) || !is_array($img)) {
                        continue;
                    }

                    $img_url = '';
                    if (!empty($img['sizes']) && is_array($img['sizes']) && !empty($img['sizes']['large'])) {
                        $img_url = $img['sizes']['large'];
                    } elseif (!empty($img['url'])) {
                        $img_url = $img['url'];
                    }
                    ?>
                    <div class="images-carousel__item swiper-slide">

                        <?php if (!empty($link)): ?>
                        <a class="images-carousel__link" href="<?php echo esc_url($link); ?>">
                            <?php endif; ?>

                            <figure class="images-carousel__image"
                                    style="background-image:url('<?php echo esc_url($img_url); ?>')">

                                <?php if ($enable_overlay && $overlay_alpha > 0) : ?>
                                    <div class="images-carousel__overlay"
                                         style="background: linear-gradient(0deg, rgba(0,0,0,<?php echo esc_attr($overlay_alpha); ?>) 0%, rgba(0,0,0,0) 65%);"></div>
                                <?php endif; ?>


                                <?php if (!empty($item['caption'])): ?>
                                    <div class="images-carousel__caption">
                                        <?php
                                        $has_item_description = !empty($item['description']);
                                        $caption_class = $has_item_description
                                                ? 'images-carousel__caption-title-variant'
                                                : 'images-carousel__caption-title';
                                        ?>
                                        <figcaption class="<?php echo esc_attr($caption_class); ?>">
                                            <?php echo esc_html($item['caption']); ?>
                                        </figcaption>

                                        <?php if ($has_item_description): ?>
                                            <p class="images-carousel__caption-text"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>

                                    </div>
                                <?php endif; ?>
                            </figure>

                            <?php if (!empty($link)): ?>
                        </a>
                    <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            </div>

            <!-- bullets solo mobile -->
            <div class="images-carousel__pagination"></div>
        </div>
    <?php endif; ?>
</section>