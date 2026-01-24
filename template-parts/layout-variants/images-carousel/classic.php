<?php
$title           = get_sub_field('title');
$description     = get_sub_field('description');
$button_settings = get_sub_field('button_settings');
$images          = get_sub_field('images');
?>

<section class="images-carousel images-carousel--classic">
    <div class="images-carousel__header px-4 px-md-0">
        <div class="row g-0">
            <div class="col-12 col-md-4 mx-auto">
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

<style>

    .images-carousel.images-carousel--classic {
        padding-top: 20px;
        padding-bottom: 80px;
    }

    .images-carousel.images-carousel--classic .images-carousel__header{
        text-align: center;
    }

    .images-carousel.images-carousel--classic .images-carousel__description p {
        text-align: start;
    }

    .images-carousel.images-carousel--classic .images-carousel__title {
        font-size: 20px;
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        letter-spacing: 2px;
    }

    .images-carousel.images-carousel--classic .images-carousel__description p{
        font-size: 16px;
        font-weight: 300;
    }

    .images-carousel.images-carousel--classic .images-carousel__media {
        margin-top: 3rem;
    }

    .images-carousel.images-carousel--classic .images-carousel__image {
        position: relative;
        height: 360px;
        background-size: cover;
        background-position: center;
    }

    .images-carousel.images-carousel--classic .images-carousel__caption {
        position: absolute;
        bottom: 1.5rem;
        left: 1.5rem;
        color: #fff;
        font-size: 16px;
        padding: 2px 1rem;
        border-left: 1px solid white;
        line-height: 20px;
        font-weight: 500;
    }

    .images-carousel.images-carousel--classic .images-carousel__caption-title-variant{
        font-family: var(--pm-font-secondary);
        font-style: italic;
    }

    .images-carousel.images-carousel--classic .images-carousel__caption-text{
        margin: 0;
    }

    /* Paginación (scope al variant) */
    .images-carousel.images-carousel--classic .images-carousel__pagination { display: block; }

    .images-carousel.images-carousel--classic .images-carousel__pagination .swiper-pagination-bullet {
        width: 16px;
        height: 8px;
        background: #A9AAAA;
        opacity: 0.6;
        border-radius: 12px;
        margin: 0 !important;
        transition: all 0.3s ease;
    }

    .images-carousel.images-carousel--classic .images-carousel__pagination .swiper-pagination-bullet-active {
        width: 32px;
        height: 8px;
        background: #CFAB76;
        opacity: 1;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .images-carousel.images-carousel--classic .images-carousel__pagination .swiper-pagination-bullet,
    .images-carousel.images-carousel--classic .images-carousel__pagination .swiper-pagination-bullet-active {
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    .images-carousel.images-carousel--classic .images-carousel__pagination {
        position: static;
        margin-top: 12px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    @media (min-width: 768px) {
        .images-carousel.images-carousel--classic {
            padding-top: 120px;
            padding-bottom: 80px;
        }

        .images-carousel.images-carousel--classic .images-carousel__title {
            font-size: 32px;
        }

        .images-carousel.images-carousel--classic .images-carousel__caption-title-variant {
            font-size: 18px;
        }

        .images-carousel.images-carousel--classic .images-carousel__description p {
            text-align: center;
        }

        .images-carousel.images-carousel--classic .images-carousel__pagination {
            display: flex !important;
        }

        .images-carousel.images-carousel--classic .images-carousel__image { height: 580px; }
    }
</style>

<script>
    (function () {
        function initImagesCarouselSwipers() {
            if (typeof window.Swiper === 'undefined') return;

            let els = document.querySelectorAll('.images-carousel__media[data-images-carousel-variant="classic"]');

            if (!els || !els.length) return;

            els.forEach(function (el) {
                if (el.dataset.swiperInitialized === '1') return;
                el.dataset.swiperInitialized = '1';

                new Swiper(el, {
                    slidesPerView: 1.25,
                    spaceBetween: 16,
                    speed: 600,
                    pagination: {
                        el: el.querySelector('.images-carousel__pagination'),
                        clickable: true
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 16
                        },
                        992: {
                            slidesPerView: 3,
                            spaceBetween: 16
                        }
                    }
                });
            });
        }

        window.addEventListener('load', initImagesCarouselSwipers);
    })();
</script>