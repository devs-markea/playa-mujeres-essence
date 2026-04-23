<?php
/**
 * Content Carousel (ACF) — Variante: essence
 */

$items = get_sub_field('items');
if (empty($items) || !is_array($items)) {
    return;
}

?>
<section data-anim="slide-up delay-2" class="content-carousel content-carousel--essence<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>" data-content-carousel
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