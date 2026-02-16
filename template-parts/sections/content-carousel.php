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
<section class="content-carousel" data-content-carousel>
    <div class="content-carousel__inner">
        <div class="content-carousel__swiper swiper" data-content-carousel-swiper>
            <div class="swiper-wrapper">
                <?php foreach ($items as $item) : ?>
                    <?php
                    $heading         = isset($item['heading']) ? $item['heading'] : '';
                    $heading_level   = isset($item['heading_level']) ? $item['heading_level'] : 'h3';
                    $description     = isset($item['description']) ? $item['description'] : '';
                    $show_button     = !empty($item['show_button']);
                    $button_link     = isset($item['button_link']) ? $item['button_link'] : array();
                    $enable_meta     = !empty($item['enable_meta_info']);
                    $meta_info       = isset($item['meta_info']) ? $item['meta_info'] : array();
                    $image           = isset($item['image']) ? $item['image'] : array();

                    // ✅ Variant dentro del repeater
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
                        <article class="content-carousel-card content-carousel-card--<?php echo esc_attr(sanitize_title($layout_variant)); ?>"
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

                                <?php if ($enable_meta && !empty($meta_info) && is_array($meta_info)) : ?>
                                    <ul class="content-carousel-card__meta" aria-label="<?php echo esc_attr__('Información adicional', 'textdomain'); ?>">
                                        <?php foreach ($meta_info as $meta) : ?>
                                            <?php
                                            $meta_icon  = isset($meta['meta_icon']) ? $meta['meta_icon'] : '';
                                            $meta_label = isset($meta['meta_label']) ? $meta['meta_label'] : '';
                                            if (!$meta_icon && !$meta_label) continue;
                                            ?>
                                            <li class="content-carousel-card__meta-item">
                                                <?php pm_render_icon_picker($meta_icon); ?>
                                                <?php if ($meta_label) : ?>
                                                    <span class="content-carousel-card__meta-label"><?php echo esc_html($meta_label); ?></span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php
                                if ($show_button && !empty($button_link) && is_array($button_link)) :
                                    $btn_url    = !empty($button_link['url']) ? $button_link['url'] : '';
                                    $btn_title  = !empty($button_link['title']) ? $button_link['title'] : '';
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
</section>

<style>
    .content-carousel {
        background: #FBFAF7;
        padding: 64px 0;
    }


    .content-carousel__inner {
        max-width: 1200px;
        margin: 0 auto;
    }

    .content-carousel__swiper {
        padding: 0 16px;
    }

    .content-carousel__pagination {
        position: static;
        margin-top: 12px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .content-carousel__pagination .swiper-pagination-bullet {
        width: 16px;
        height: 8px;
        background: #A9AAAA;
        opacity: 0.6;
        border-radius: 12px;
        margin: 0 !important;
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }


    .content-carousel__pagination .swiper-pagination-bullet-active {
        width: 32px;
        height: 8px;
        background: #CFAB76;
        opacity: 1;
        border-radius: 12px;
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    @media (min-width: 768px) {
        .content-carousel__swiper {
            padding: 0;
        }
    }

    .content-carousel-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        overflow: hidden;
        height: 100%;
    }

    .content-carousel-card__media {
        position: relative;
        aspect-ratio: 4 / 3;
        background: #f3f3f3;
    }

    .content-carousel-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .content-carousel-card__body {
        padding: 24px;
        text-align: center;
    }

    .content-carousel-card__title {
        font-family: var(--pm-font-secondary);
        font-size: 20px;
        font-style: italic;
        font-weight: 500;
        line-height: 20px;
        letter-spacing: 1px;
        text-align: center;
        color: #323232;
    }

    .content-carousel-card__desc {
        color: #323232;
        text-align: center;
        font-size: 14px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
    }

    .content-carousel-card__meta {
        list-style: none;
        padding: 0;
        margin: 0 0 14px;
        display: inline-flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .content-carousel-card__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: rgba(44,44,44,0.75);
    }

    .content-carousel__meta-icon img,
    .content-carousel-card__meta-icon {
        width: 16px;
        height: 16px;
        display: inline-block;
    }

    .content-carousel-card__actions {
        margin-top: 6px;
    }

    .content-carousel-card__btn {
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

    .content-carousel-card__btn:hover {
        background: #2c2c2c;
        color: #fff;
    }

    .content-carousel__pagination {
        margin-top: 14px;
        text-align: center;
    }

</style>

