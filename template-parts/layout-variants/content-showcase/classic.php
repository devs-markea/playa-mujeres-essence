<?php
/**
 * template-parts/layout-variants/content-showcase/classic.php
 *
 * Variante original: tabs + swiper de experiencias.
 * El loader (content-showcase.php) provee:
 * - $title, $title_tag
 * - $description
 * - $items
 */
?>
<section data-anim="slide-up delay-2" class="content-showcase content-showcase--classic<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="px-4 px-md-0">
        <div class="row g-0 align-items-center">

            <div class="col-12 col-md-5 offset-md-1">
                <aside class="content-showcase__nav" aria-label="Experiences" data-showcase-block>

                    <?php if ($title) : ?>
                        <<?= esc_html($title_tag); ?> class="content-showcase__title">
                            <?= esc_html($title); ?>
                        </<?= esc_html($title_tag); ?>>
                    <?php endif; ?>

                    <!-- Desktop: rail vertical con indicador -->
                    <div class="content-showcase__rail content-showcase__rail--desktop">
                        <div class="content-showcase__indicator" aria-hidden="true"></div>

                        <?php foreach ($items as $i => $item) : ?>
                            <button
                                type="button"
                                class="content-showcase__tab <?= $i === 0 ? 'is-active' : ''; ?>"
                                data-slide="<?= esc_attr($i); ?>"
                                aria-controls="showcase-slide-<?= esc_attr($i); ?>"
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
                                            aria-controls="showcase-slide-<?= esc_attr($i); ?>"
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

            <!-- Swiper contenido derecha -->
            <div class="col-12 col-md-6">
                <div class="content-showcase__content">
                    <div class="swiper content-showcase__swiper" data-showcase-swiper>
                        <div class="swiper-wrapper">

                            <?php foreach ($items as $i => $item) :
                                $img    = $item['image'];
                                $desc   = $item['description'];
                                $btn    = $item['_button_settings'] ?? null;
                                $btn_ok = ! empty($btn['show_button']) && ! empty($btn['button_link']);
                                $link   = $btn_ok ? $btn['button_link'] : null;

                                $img_id  = (is_array($img) && ! empty($img['ID'])) ? (int) $img['ID'] : 0;
                                $img_alt = '';
                                if (is_array($img) && ! empty($img['alt'])) {
                                    $img_alt = $img['alt'];
                                } elseif (! empty($item['name'])) {
                                    $img_alt = $item['name'];
                                }
                            ?>
                                <article
                                    class="swiper-slide content-showcase__slide"
                                    id="showcase-slide-<?= esc_attr($i); ?>"
                                    data-slide-index="<?= esc_attr($i); ?>">

                                    <div class="content-showcase__slide-media">
                                        <?php if ($img_id) : ?>
                                            <?= wp_get_attachment_image($img_id, 'large', false, [
                                                'alt'      => $img_alt,
                                                'loading'  => $i === 0 ? 'eager' : 'lazy',
                                                'decoding' => 'async',
                                            ]); ?>
                                        <?php elseif (! empty($img['url'])) : ?>
                                            <img src="<?= esc_url($img['url']); ?>"
                                                 alt="<?= esc_attr($img_alt); ?>"
                                                 loading="<?= $i === 0 ? 'eager' : 'lazy'; ?>"
                                                 decoding="async">
                                        <?php endif; ?>
                                    </div>

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

        <button type="button" class="content-showcase__exit" data-showcase-exit aria-label="Exit">
            Exit
        </button>
    </div>
</section>
