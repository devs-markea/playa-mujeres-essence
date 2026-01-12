<?php
$title          = get_sub_field('title');
$description    = get_sub_field('description');
$button_settings = get_sub_field('button_settings');
$images         = get_sub_field('images');
?>

<section class="things-nearby">
    <div class="things-nearby__header px-4 px-md-0">
        <div class="row g-0">
            <div class="col-12 col-md-4 mx-auto">
                <?php if ($title): ?>
                    <h2><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description): ?>
                    <?php echo wp_kses_post( $description ); ?>
                <?php endif; ?>

                <?php if (!empty($button_settings['show_button']) && !empty($button_settings['button_link'])): ?>
                    <a href="<?php echo esc_url($button_settings['button_link']['url']); ?>"
                       target="<?php echo esc_attr($button_settings['button_link']['target'] ?: '_self'); ?>"
                       class="btn btn-primary btn-border-bottom-black things-nearby__button">
                        <?php echo esc_html($button_settings['button_link']['title']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($images): ?>
        <div class="things-nearby__media swiper px-4 px-md-0">
            <div class="swiper-wrapper">

                <?php foreach ($images as $item):
                    $img = $item['image'];
                    $link = $item['image_link'];
                    ?>
                    <div class="swiper-slide things-nearby__item">

                        <?php if ($link): ?>
                        <a href="<?php echo esc_url($link); ?>">
                            <?php endif; ?>

                            <figure class="things-nearby__image"
                                    style="background-image:url('<?php echo esc_url($img['sizes']['large']); ?>')">
                                <?php if (!empty($item['caption'])): ?>
                                    <figcaption><?php echo esc_html($item['caption']); ?></figcaption>
                                <?php endif; ?>
                            </figure>

                            <?php if ($link): ?>
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
    .images-carousel__pagination{display: block}

    .images-carousel__pagination .swiper-pagination-bullet {
        width: 16px;
        height: 8px;
        background: #A9AAAA;
        opacity: 0.6;
        border-radius: 12px;
        margin: 0 !important;
        transition: all 0.3s ease;
    }
    .images-carousel__pagination .swiper-pagination-bullet-active {
        width: 32px;
        height: 8px;
        background: #CFAB76;
        opacity: 1;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .images-carousel__pagination .swiper-pagination-bullet,
    .images-carousel__pagination .swiper-pagination-bullet-active {
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }
    .images-carousel__pagination {
        position: static;
        margin-top: 12px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .things-nearby {
        padding-top: 120px;
        padding-bottom: 80px;
    }
    .things-nearby__header{
        text-align: center;
    }
    .things-nearby__header p {
        text-align: start;
    }
    .things-nearby__header h2 {
        font-size: 20px;
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        letter-spacing: 2px;
    }
    .things-nearby__header p{
        font-size: 16px;
        font-weight: 300;
    }
    .things-nearby__media {
        margin-top: 3rem;
    }

    /* Desktop: grid estático */
    @media (min-width: 768px) {
        .things-nearby__header h2 {
            font-size: 32px;
        }
        .things-nearby__header p {
            text-align: center;
        }
        .things-nearby__media .swiper-wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            transform: none !important;
        }

        .things-nearby__media .swiper-slide {
            width: 100% !important;
        }

        .images-carousel__pagination {
            display: none !important;
        }

        .things-nearby__media .swiper-pagination {
            display: none;
        }
    }

    .things-nearby__image {
        position: relative;
        aspect-ratio: 3 / 4;
        background-size: cover;
        background-position: center;
    }

    .things-nearby__image figcaption {
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

</style>

<script>
    (function () {
        let nearbySwiper = null;

        function initNearbySwiper() {
            const el = document.querySelector('.things-nearby__media');

            if (!el) return;

            if (window.innerWidth < 768 && !nearbySwiper) {
                nearbySwiper = new Swiper(el, {
                    slidesPerView: 1.25,
                    spaceBetween: 16,
                    pagination: {
                        el: el.querySelector('.images-carousel__pagination'),
                        clickable: true
                    }
                });
            }

            if (window.innerWidth >= 768 && nearbySwiper) {
                nearbySwiper.destroy(true, true);
                nearbySwiper = null;
            }
        }

        window.addEventListener('load', initNearbySwiper);
        window.addEventListener('resize', initNearbySwiper);
    })();

</script>