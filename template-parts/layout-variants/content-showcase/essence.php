<?php
/**
 * template-parts/layout-variants/content-showcase/essence.php
 *
 * Layout: heading + intro text arriba.
 * Izquierda: rail desktop (indicador vertical) + mobile (swiper horizontal).
 * Derecha: Swiper de imágenes con flechas — sincronizado con el rail.
 *
 * El loader (content-showcase.php) provee:
 * - $title, $title_tag
 * - $description  (intro text debajo del heading)
 * - $items        (cada item: name, image)
 */

if (! $items) return;
?>

<section data-anim="slide-up delay-2" class="content-showcase content-showcase--essence content-showcase--header-<?= esc_attr($header_alignment); ?><?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">

                <?php if ($title || $description) :
                    $header_col   = $header_alignment === 'center' ? 'col-12 col-md-6 mx-auto' : 'col-12';
                    $header_align = $header_alignment === 'center' ? 'text-start text-md-center' : 'text-start';
                ?>
                    <div class="content-showcase__header">
                        <div class="row g-0">
                            <div class="<?= esc_attr($header_col); ?> <?= esc_attr($header_align); ?>">
                                <?php if ($title) : ?>
                                    <<?= esc_html($title_tag); ?> class="content-showcase__title">
                                        <?= esc_html($title); ?>
                                    </<?= esc_html($title_tag); ?>>
                                <?php endif; ?>

                                <?php if ($description) : ?>
                                    <div class="content-showcase__intro">
                                        <?= wp_kses_post($description); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row g-0 g-lg-5 align-items-center content-showcase__body">

                    <!-- Rail izquierda -->
                    <div class="col-12 col-lg-5">
                        <aside class="content-showcase__nav" aria-label="<?= esc_attr($title ?: 'Content'); ?>" data-showcase-block>

                            <!-- Desktop: rail vertical con indicador -->
                            <div class="content-showcase__rail content-showcase__rail--desktop">
                                <div class="content-showcase__indicator" aria-hidden="true"></div>

                                <?php foreach ($items as $i => $item) : ?>
                                    <button
                                        type="button"
                                        class="content-showcase__tab <?= $i === 0 ? 'is-active' : ''; ?>"
                                        data-slide="<?= esc_attr($i); ?>"
                                        aria-selected="<?= $i === 0 ? 'true' : 'false'; ?>">
                                        <?= esc_html($item['name']); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <!-- Mobile: swiper horizontal con underline -->
                            <div class="content-showcase__tabs-swiper-wrap content-showcase__rail--mobile">
                                <div class="swiper content-showcase__tabs-swiper" data-showcase-tabs-swiper>
                                    <div class="swiper-wrapper">
                                        <?php foreach ($items as $i => $item) : ?>
                                            <div class="swiper-slide content-showcase__tab-slide">
                                                <button
                                                    type="button"
                                                    class="content-showcase__tab <?= $i === 0 ? 'is-active' : ''; ?>"
                                                    data-slide="<?= esc_attr($i); ?>"
                                                    aria-selected="<?= $i === 0 ? 'true' : 'false'; ?>">
                                                    <?= esc_html($item['name']); ?>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <span class="content-showcase__underline" aria-hidden="true"></span>
                                </div>
                            </div>

                        </aside>
                    </div>

                    <!-- Swiper imágenes derecha -->
                    <div class="col-12 col-lg-7">
                        <div class="content-showcase__media">
                            <div class="swiper content-showcase__swiper" data-showcase-swiper>
                                <div class="swiper-wrapper">
                                    <?php foreach ($items as $i => $item) :
                                        $img     = $item['image'] ?? null;
                                        $img_id  = (is_array($img) && ! empty($img['ID'])) ? (int) $img['ID'] : 0;
                                        $img_alt = (is_array($img) && ! empty($img['alt'])) ? $img['alt'] : ($item['name'] ?? '');

                                        $desc   = $item['description'] ?? null;
                                        $btn    = $item['_button_settings'] ?? null;
                                        $btn_ok = ! empty($btn['show_button']) && ! empty($btn['button_link']);
                                        $link   = $btn_ok ? $btn['button_link'] : null;
                                    ?>
                                        <article class="swiper-slide content-showcase__slide" id="showcase-slide-<?= esc_attr($i); ?>" data-slide-index="<?= esc_attr($i); ?>">

                                            <div class="content-showcase__slide-media">
                                                <?php if ($img_id) : ?>
                                                    <?= wp_get_attachment_image($img_id, 'large', false, [
                                                        'alt'      => $img_alt,
                                                        'loading'  => 'lazy',
                                                        'decoding' => 'async',
                                                    ]); ?>
                                                <?php elseif (! empty($img['url'])) : ?>
                                                    <img src="<?= esc_url($img['url']); ?>"
                                                         alt="<?= esc_attr($img_alt); ?>"
                                                         loading="lazy"
                                                         decoding="async">
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($desc || $btn_ok) : ?>
                                                <div class="content-showcase__slide-card">
                                                    <?php if ($desc) : ?>
                                                        <div class="content-showcase__slide-text">
                                                            <?= wp_kses_post($desc); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($btn_ok) : ?>
                                                        <a class="btn btn-primary btn-border-bottom-black content-showcase__slide-cta"
                                                           href="<?= esc_url($link['url']); ?>"
                                                           target="<?= esc_attr($link['target'] ?? '_self'); ?>"
                                                           rel="<?= ($link['target'] ?? '') === '_blank' ? 'noopener noreferrer' : ''; ?>">
                                                            <?= esc_html($link['title']); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                        </article>
                                    <?php endforeach; ?>
                                </div>
                                <div class="swiper-button-prev content-showcase__prev"></div>
                                <div class="swiper-button-next content-showcase__next"></div>
                                <div class="swiper-pagination content-showcase__pagination"></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
