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

