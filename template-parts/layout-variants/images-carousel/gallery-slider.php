<?php
$title           = get_sub_field('title');
$description     = get_sub_field('description');
$button_settings = get_sub_field('button_settings');
$images          = get_sub_field('images');
?>

<section class="images-carousel images-carousel--gallery-slider">
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
        <div class="images-carousel__media swiper " data-images-carousel-variant="gallery-slider">
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
                                <?php if (!empty($item['caption'])): ?>
                                    <div class="images-carousel__caption">
                                        <figcaption class="images-carousel__caption-title"><?php echo esc_html($item['caption']); ?></figcaption>
                                        <?php if (!empty($item['description'])): ?>
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