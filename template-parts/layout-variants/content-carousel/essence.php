<?php
/**
 * Content Carousel (ACF) — Variante: essence
 */

$items = get_sub_field('items');
if (empty($items) || !is_array($items)) {
    return;
}

if (!function_exists('pm_safe_heading_tag')) {
    function pm_safe_heading_tag($tag) {
        $allowed = array('h1','h2','h3','h4','h5','h6','p','span','div');
        $tag = strtolower(trim((string) $tag));
        return in_array($tag, $allowed, true) ? $tag : 'h3';
    }
}

if (!function_exists('pm_render_icon_picker')) {
    function pm_render_icon_picker($icon) {
        $img_classes = 'content-carousel__meta-icon';
        $span_classes = 'content-carousel__meta-icon';

        // 1) ACF icon picker / media library (array)
        if (is_array($icon)) {

            // Media library: attachment array con ID
            if (!empty($icon['ID'])) {
                $id = (int) $icon['ID'];
                $html = wp_get_attachment_image($id, 'thumbnail', false, array(
                    'class' => $img_classes,
                    'alt' => '',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ));
                if ($html) {
                    echo $html;
                    return;
                }
            }

            // Algunos icon pickers regresan url directo
            if (!empty($icon['url'])) {
                echo '<img class="' . esc_attr($img_classes) . '" src="' . esc_url($icon['url']) . '" alt="" loading="lazy" decoding="async" />';
                return;
            }

            // Algunos regresan value (ej: dashicons-clock)
            if (!empty($icon['value']) && is_string($icon['value'])) {
                $icon = $icon['value']; // continuar al handler de string
            } else {
                return;
            }
        }

        // 2) String: puede ser dashicons-*, URL, o HTML inline (svg)
        if (is_string($icon)) {
            $icon = trim($icon);
            if ($icon === '') return;

            // URL directa
            if (filter_var($icon, FILTER_VALIDATE_URL)) {
                echo '<img class="' . esc_attr($img_classes) . '" src="' . esc_url($icon) . '" alt="" loading="lazy" decoding="async" />';
                return;
            }

            // Dashicons: "dashicons-clock" => <span class="dashicons dashicons-clock ..."></span>
            if (strpos($icon, 'dashicons-') === 0) {
                echo '<span class="dashicons ' . esc_attr($icon) . ' ' . esc_attr($span_classes) . '" aria-hidden="true"></span>';
                return;
            }

            // Si viene SVG/HTML (por si el picker lo devuelve así)
            if (strpos($icon, '<svg') !== false || strpos($icon, '<span') !== false || strpos($icon, '<i') !== false) {
                echo '<span class="' . esc_attr($span_classes) . '">' . wp_kses_post($icon) . '</span>';
                return;
            }

            // Fallback: texto plano (último recurso)
            echo '<span class="' . esc_attr($span_classes) . '">' . esc_html($icon) . '</span>';
        }
    }

}

?>
<section data-anim="slide-up delay-2" class="content-carousel content-carousel--essence" data-content-carousel
         data-content-carousel-variant="essence">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-4 mx-auto">
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
        <div
                class="content-carousel__swiper swiper"
                data-content-carousel-swiper
                data-content-carousel-variant="essence">
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

                $tag = pm_safe_heading_tag($heading_level);

                $img_url = !empty($image['url']) ? $image['url'] : '';
                $img_alt = !empty($image['alt']) ? $image['alt'] : '';
                ?>
                <div class="swiper-slide">
                    <article class="content-carousel-card content-carousel-card--essence" data-variant="essence">
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

                        <div class="content-carousel-card__footer">
                            <?php
                            if ($show_button && !empty($button_link) && is_array($button_link)) :
                                $btn_url = !empty($button_link['url']) ? $button_link['url'] : '';
                                $btn_title = !empty($button_link['title']) ? $button_link['title'] : '';
                                $btn_target = !empty($button_link['target']) ? $button_link['target'] : '_self';
                                if ($btn_url && $btn_title) :
                                    ?>
                                    <div class="content-carousel-card__actions">
                                        <a class="btn btn-primary btn-border-bottom-black content-carousel-card__btn"
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

                            <?php if ($enable_meta && !empty($meta_info) && is_array($meta_info)) : ?>
                                <ul class="content-carousel-card__meta"
                                    aria-label="<?php echo esc_attr__('Información adicional', 'textdomain'); ?>">
                                    <?php foreach ($meta_info as $meta) : ?>
                                        <?php
                                        $meta_icon = isset($meta['meta_icon']) ? $meta['meta_icon'] : '';
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
                        </div>
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
   Content Carousel — Essence
   ========================================================================== */

    .content-carousel.content-carousel--essence {
        padding: 64px 0;
    }
    .content-carousel.content-carousel--essence .content-carousel__heading {
        font-family: var(--pm-font-secondary);
        font-size: 24px;
        font-style: italic;
        font-weight: 500;
        letter-spacing: 2px;
        text-align: center;
        margin-bottom: 1rem;
        color: #323232;
    }

    .content-carousel.content-carousel--essence .content-carousel__inner {
        max-width: 1200px;
        margin: 0 auto;
    }



    .content-carousel-card__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    @media (min-width: 991px) {

        .content-carousel.content-carousel--essence .content-carousel__heading {
            font-size: 32px;
        }
    }

    /* Pagination (scoped a essence) */
    .content-carousel.content-carousel--essence .content-carousel__pagination {
        position: static;
        margin: 12px 0;
        display: flex;
        justify-content: center;
        gap: 10px;
        text-align: center;
    }

    .content-carousel.content-carousel--essence
    .content-carousel__pagination .swiper-pagination-bullet {
        width: 16px;
        height: 8px;
        background: #A9AAAA;
        opacity: 0.6;
        border-radius: 12px;
        margin: 0 !important;
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    .content-carousel.content-carousel--essence
    .content-carousel__pagination .swiper-pagination-bullet-active {
        width: 32px;
        height: 8px;
        background: #CFAB76;
        opacity: 1;
        border-radius: 12px;
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    /* Card (scoped a essence) */
    .content-carousel.content-carousel--essence .content-carousel-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        overflow: hidden;
        height: 100%;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__media {
        position: relative;
        aspect-ratio: 4 / 3;
        background: #f3f3f3;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__body {
        padding: 24px;
        text-align: center;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__title {
        font-family: var(--pm-font-secondary);
        font-size: 20px;
        font-style: italic;
        font-weight: 500;
        line-height: 20px;
        letter-spacing: 1px;
        text-align: start;
        color: #323232;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__desc {
        color: #323232;
        text-align: start;
        font-size: 14px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__meta {
        list-style: none;
        padding: 0;
        margin: 0;
        display: inline-flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #323232;
        font-size: 14px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
    }

    .content-carousel.content-carousel--essence .content-carousel__meta-icon img,
    .content-carousel.content-carousel--essence .content-carousel-card__meta-icon {
        width: 16px;
        height: 16px;
        display: inline-block;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__actions {
        margin-top: 6px;
    }

    .content-carousel.content-carousel--essence .content-carousel-card__btn {
        padding-bottom: 0;
        font-size: 14px;
    }



    /* ==========================================================================
       END Content Carousel — Essence
       ========================================================================== */

</style>