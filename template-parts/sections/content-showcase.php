<?php
$title        = get_sub_field('title');
$title_level  = get_sub_field('title_level');
$description = get_sub_field('description');

$title_tag = pm_essence_heading_tag_or_null($title_level, 'h2');
if (empty($title_tag)) {
    $title_tag = 'h2';
}

$items = get_sub_field('items');
if (!$items) return;

?>

<section data-anim="slide-up delay-2" class="experiences-tabs">
    <div class="px-4 px-md-0">
        <div class="row g-0 align-items-center">

            <div class="col-12 col-md-5 offset-md-1">
                <!-- Tabs -->
                <aside class="experiences-tabs__nav" aria-label="Experiences" data-experiences-block>
                    <?php if ( $title ) : ?>
                        <<?php echo esc_html($title_tag); ?> class="experiences-tabs__title">
                            <?php echo esc_html( $title ); ?>
                        </<?php echo esc_html($title_tag); ?>>
                    <?php endif; ?>

                    <!-- Desktop rail (vertical) -->
                    <div class="experiences-tabs__rail experiences-tabs__rail--desktop">
                        <div class="experiences-tabs__indicator" aria-hidden="true"></div>

                        <?php foreach ($items as $i => $item): ?>
                            <button
                                    type="button"
                                    class="experiences-tab <?php echo $i === 0 ? 'is-active' : ''; ?>"
                                    data-slide="<?php echo esc_attr($i); ?>"
                                    aria-controls="experience-slide-<?php echo esc_attr($i); ?>"
                                    aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                            >
                                <?php echo esc_html($item['name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Mobile tabs swiper (horizontal) -->
                    <div class="experiences-tabs__tabs-swiper experiences-tabs__rail--mobile">
                        <div class="swiper experiences-tabs-swiper" data-experiences-tabs-swiper>
                            <div class="swiper-wrapper">
                                <?php foreach ($items as $i => $item): ?>
                                    <div class="swiper-slide experiences-tab-slide">
                                        <button
                                                type="button"
                                                class="experiences-tab <?php echo $i === 0 ? 'is-active' : ''; ?>"
                                                data-slide="<?php echo esc_attr($i); ?>"
                                                aria-controls="experience-slide-<?php echo esc_attr($i); ?>"
                                                aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                                            <?php echo esc_html($item['name']); ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- underline móvil -->
                            <span class="experiences-tabs__underline" aria-hidden="true"></span>
                        </div>
                    </div>

                </aside>

            </div>

            <div class="col-12 col-md-6">
                <!-- Swiper -->
                <div class="experiences-tabs__content">
                    <div class="swiper experiences-swiper" data-experiences-swiper>
                        <div class="swiper-wrapper">

                            <?php foreach ($items as $i => $item):
                                $img   = $item['image'];
                                $desc  = $item['description'];
                                $btn   = $item['_button_settings'] ?? null;

                                $btn_ok = !empty($btn['show_button']) && !empty($btn['button_link']);
                                $link   = $btn_ok ? $btn['button_link'] : null;

                                $img_id  = (is_array($img) && !empty($img['ID'])) ? (int) $img['ID'] : 0;
                                $img_alt = '';
                                if (is_array($img) && !empty($img['alt'])) {
                                    $img_alt = $img['alt'];
                                } elseif (!empty($item['name'])) {
                                    $img_alt = $item['name'];
                                }
                                ?>
                                <article
                                        class="swiper-slide experience-slide"
                                        id="experience-slide-<?php echo esc_attr($i); ?>"
                                        data-slide-index="<?php echo esc_attr($i); ?>"
                                >
                                    <div class="experience-slide__media">
                                        <?php if ($img_id) : ?>
                                            <?php
                                            echo wp_get_attachment_image(
                                                $img_id,
                                                'large',
                                                false,
                                                array(
                                                    'alt'      => $img_alt,
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                                )
                                            );
                                            ?>
                                        <?php elseif (!empty($img['url'])): ?>
                                            <img
                                                    src="<?php echo esc_url($img['url']); ?>"
                                                    alt="<?php echo esc_attr($img_alt); ?>"
                                                    loading="lazy"
                                                    decoding="async"
                                            />
                                        <?php endif; ?>
                                    </div>

                                    <div class="experience-slide__card">
                                        <?php if ($desc): ?>
                                            <div class="experience-slide__text">
                                                <?php echo wp_kses_post($desc); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($btn_ok): ?>
                                            <a
                                                    class="btn btn-primary btn-border-bottom-black experience-slide__cta"
                                                    href="<?php echo esc_url($link['url']); ?>"
                                                    target="<?php echo esc_attr($link['target'] ?? '_self'); ?>"
                                                    rel="<?php echo ($link['target'] ?? '') === '_blank' ? 'noopener noreferrer' : ''; ?>"
                                            >
                                                <?php echo esc_html($link['title']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>

                        </div>
                        <div class="swiper-button-prev experiences-content__prev"></div>
                        <div class="swiper-button-next experiences-content__next"></div>
                        <div class="swiper-pagination experiences-content__pagination"></div>

                    </div>
                </div>
            </div>

        </div>
        <button type="button" class="experiences-exit" data-experiences-exit aria-label="Exit experiences">
            Exit
        </button>
    </div>

</section>

