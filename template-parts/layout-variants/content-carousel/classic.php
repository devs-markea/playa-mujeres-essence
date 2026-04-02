<?php
/**
 * Content Carousel (ACF)
 * Fields:
 * - items (repeater)
 *   - heading (text)
 *   - heading_level (select: h2|h3|h4|p|span ...)
 *   - description (text)
 *   - show_button (true/false)
 *   - button_link (link)
 *   - enable_meta_info (true/false)
 *   - meta_info (repeater)
 *       - meta_icon (icon picker)
 *       - meta_label (text)
 *   - image
 *   - layout_variant (select: classic)
 */

$items = get_sub_field('items');
if (empty($items) || !is_array($items)) {
    return;
}

function pm_safe_heading_tag($tag) {
    $allowed = array('h1','h2','h3','h4','h5','h6','p','span','div');
    $tag = strtolower(trim((string) $tag));
    return in_array($tag, $allowed, true) ? $tag : 'h3';
}

function pm_render_icon_picker($icon) {
    if (empty($icon)) return;

    if (is_array($icon)) {
        if (!empty($icon['url'])) {
            echo '<img class="content-carousel__meta-icon" src="' . esc_url($icon['url']) . '" alt="" loading="lazy" decoding="async" />';
            return;
        }
        if (!empty($icon['value'])) {
            echo '<span class="content-carousel__meta-icon">' . $icon['value'] . '</span>';
            return;
        }
    }

    if (is_string($icon)) {
        echo '<span class="content-carousel__meta-icon">' . $icon . '</span>';
    }
}

?>
<section data-anim="slide-up delay-2" class="content-carousel content-carousel--classic" data-content-carousel
         data-content-carousel-variant="classic">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-6 mx-auto">
                <?php if (!empty($heading_tag) && !empty($heading)) : ?>
                <<?php echo tag_escape($heading_tag); ?> class="content-carousel__heading">
                <?php echo esc_html($heading); ?>
            </<?php echo tag_escape($heading_tag); ?>>
            <?php elseif (!empty($heading)) : ?>
                <div class="content-collection__heading content-collection__heading--text-only">
                    <?php echo esc_html($heading); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="content-carousel__inner">
        <div class="content-carousel__swiper swiper" data-content-carousel-swiper
             data-content-carousel-variant="classic">
            <div class="swiper-wrapper">
                <?php foreach ($items

                as $item) : ?>
                <?php
                $heading = isset($item['heading']) ? $item['heading'] : '';
                $heading_level = isset($item['heading_level']) ? $item['heading_level'] : 'h3';
                $description = isset($item['description']) ? $item['description'] : '';
                $show_button = !empty($item['show_button']);
                $button_link = isset($item['button_link']) ? $item['button_link'] : array();
                $enable_meta = !empty($item['enable_meta_info']);
                $meta_info = isset($item['meta_info']) ? $item['meta_info'] : array();
                $image = isset($item['image']) ? $item['image'] : array();

                // Variant dentro del repeater
                $layout_variant = isset($item['layout_variant']) ? $item['layout_variant'] : '';
                $layout_variant = is_string($layout_variant) ? trim($layout_variant) : '';
                if ($layout_variant === '') {
                    $layout_variant = 'classic';
                }

                $tag = pm_safe_heading_tag($heading_level);

                $img_url = !empty($image['url']) ? $image['url'] : '';
                $img_alt = !empty($image['alt']) ? $image['alt'] : '';
                ?>
                <div class="swiper-slide">
                    <article
                            class="content-carousel-card content-carousel-card--<?php echo esc_attr(sanitize_title($layout_variant)); ?>"
                            data-variant="<?php echo esc_attr($layout_variant); ?>">
                        <?php if ($img_url) : ?>
                            <div class="content-carousel-card__media">
                                <img
                                        class="content-carousel-card__img"
                                        src="<?php echo esc_url($img_url); ?>"
                                        alt="<?php echo esc_attr($img_alt); ?>"
                                        loading="lazy"
                                        decoding="async"
                                />
                            </div>
                        <?php endif; ?>

                        <div class="content-carousel-card__body">
                            <?php if ($heading) : ?>
                            <<?php echo $tag; ?> class="content-carousel-card__title">
                            <?php echo esc_html($heading); ?>
                        </<?php echo $tag; ?>>
                    <?php endif; ?>

                        <?php if ($description) : ?>
                            <div class="content-carousel-card__desc">
                                <?php echo wp_kses_post($description); ?>
                            </div>
                        <?php endif; ?>


                        <?php
                        if ($show_button && !empty($button_link) && is_array($button_link)) :
                            $btn_url = !empty($button_link['url']) ? $button_link['url'] : '';
                            $btn_title = !empty($button_link['title']) ? $button_link['title'] : '';
                            $btn_target = !empty($button_link['target']) ? $button_link['target'] : '_self';
                            if ($btn_url && $btn_title) :
                                ?>
                                <div class="content-carousel-card__actions">
                                    <a class="btn btn-primary btn-border-bottom-black"
                                       href="<?php echo esc_url($btn_url); ?>"
                                       target="<?php echo esc_attr($btn_target); ?>"
                                       rel="noopener">
                                        <?php echo esc_html($btn_title); ?>
                                    </a>
                                </div>
                            <?php
                            endif;
                        endif;
                        ?>
                </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="content-carousel__pagination swiper-pagination" aria-hidden="false"></div>
    </div>
    </div>
    </div>
</section>

<style>

    /* ==========================================================================
       Content Carousel — Classic
       ========================================================================== */

    .content-carousel.content-carousel--classic {
        background: #FBFAF7;
        padding: 64px 0;
    }

    .content-carousel.content-carousel--classic .content-carousel__inner {
        max-width: 1200px;
        margin: 0 auto;
    }

    .content-carousel.content-carousel--classic .content-carousel__heading {
        font-family: var(--pm-font-secondary);
        font-size: 24px;
        font-style: italic;
        font-weight: 500;
        letter-spacing: 2px;
        text-align: center;
        margin-bottom: 1rem;
        color: #323232;
    }



    @media (min-width: 991px) {

        .content-carousel.content-carousel--classic .content-carousel__heading {
            font-size: 32px;
        }
    }

    /* Pagination (scoped a classic) */
    .content-carousel.content-carousel--classic .content-carousel__pagination {
        position: static;
        margin: 12px 0;
        display: flex;
        justify-content: center;
        gap: 10px;
        text-align: center;
    }

    .content-carousel.content-carousel--classic
    .content-carousel__pagination .swiper-pagination-bullet {
        width: 16px;
        height: 8px;
        background: #A9AAAA;
        opacity: 0.6;
        border-radius: 12px;
        margin: 0 !important;
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    .content-carousel.content-carousel--classic
    .content-carousel__pagination .swiper-pagination-bullet-active {
        width: 32px;
        height: 8px;
        background: #CFAB76;
        opacity: 1;
        border-radius: 12px;
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    /* Card (scoped a classic) */
    .content-carousel.content-carousel--classic .content-carousel-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        overflow: hidden;
        height: 100%;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__media {
        position: relative;
        aspect-ratio: 4 / 3;
        background: #f3f3f3;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__body {
        padding: 24px;
        text-align: center;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__title {
        font-family: var(--pm-font-secondary);
        font-size: 20px;
        font-style: italic;
        font-weight: 500;
        line-height: 20px;
        letter-spacing: 1px;
        text-align: center;
        color: #323232;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__desc {
        color: #323232;
        text-align: center;
        font-size: 14px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__meta {
        list-style: none;
        padding: 0;
        margin: 0 0 14px;
        display: inline-flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: rgba(44,44,44,0.75);
    }

    .content-carousel.content-carousel--classic .content-carousel__meta-icon img,
    .content-carousel.content-carousel--classic .content-carousel-card__meta-icon {
        width: 16px;
        height: 16px;
        display: inline-block;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__actions {
        margin-top: 6px;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        border: 1px solid rgba(44,44,44,0.35);
        text-decoration: none;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #2c2c2c;
        transition: all 200ms ease;
    }

    .content-carousel.content-carousel--classic .content-carousel-card__btn:hover {
        background: #2c2c2c;
        color: #fff;
    }

    /* ==========================================================================
       END Content Carousel — Classic
       ========================================================================== */

</style>

