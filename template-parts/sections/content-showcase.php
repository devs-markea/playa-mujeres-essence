<?php
$title = get_sub_field('title');
$description = get_sub_field('description');
$items = get_sub_field('items'); // <- tu repeater
if (!$items) return;
?>

<section class="experiences-tabs">
    <div class="px-4 px-md-0">
        <div class="row g-0 align-items-center">

            <div class="col-12 col-md-5 offset-md-1">
                <!-- Tabs -->
                <aside class="experiences-tabs__nav" aria-label="Experiences" data-experiences-block>
                    <h2 class="experiences-tabs__title"><?= esc_html($title ?: 'Experiences'); ?></h2>

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
                                                aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                                        >
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
                                ?>
                                <article
                                        class="swiper-slide experience-slide"
                                        id="experience-slide-<?php echo esc_attr($i); ?>"
                                        data-slide-index="<?php echo esc_attr($i); ?>"
                                >
                                    <div class="experience-slide__media">
                                        <?php if (!empty($img['url'])): ?>
                                            <img
                                                    src="<?php echo esc_url($img['url']); ?>"
                                                    alt="<?php echo esc_attr($img['alt'] ?: $item['name']); ?>"
                                                    loading="lazy"
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
<style>

    .experiences-tabs { position: relative; }

    .experiences-exit{
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 50;
        padding: 10px 12px;
        border: 1px solid rgba(0,0,0,.2);
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(6px);
        cursor: pointer;
        display: none; /* solo cuando esté activo */
    }

    .experiences-tabs.is-scrolllock-active .experiences-exit{
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    body.is-experiences-scrolllocked{
        overflow: hidden;
    }

    /* ===============================
   BASE (Mobile First)
================================ */

    .experiences-tabs {
        padding: 40px 0;

    }
    .experiences-swiper,
    .experiences-swiper .swiper-slide {
        overflow: visible; /* 🔥 clave */
    }


    .experiences-tabs__nav { flex: 1; }

    /* ---------- Title ---------- */
    .experiences-tabs__title {
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        font-size: 20px;
        letter-spacing: 2px;
        margin-bottom: 12px;
        text-align: center;
    }

    /* ---------- Desktop rail (hidden on mobile) ---------- */
    .experiences-tabs__rail--desktop { display: none; }
    .experiences-tabs__rail--mobile  { display: block; }

    /* ---------- Mobile Tabs Swiper (3 visible) ---------- */
    .experiences-tabs__tabs-swiper { margin-bottom: 18px; }

    .experiences-tabs-swiper {
        position: relative;
        overflow: hidden;
    }

    /* Línea base gris */
    .experiences-tabs-swiper::after {
        content:"";
        position:absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 1px;
        background: rgba(0,0,0,.15);
    }

    /* 3 tabs visibles => cada slide ocupa 1/3 */
    .experiences-tabs__rail--mobile .swiper-slide {
        width: calc(100% / 3);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Tabs estilo texto centrado */
    .experiences-tabs__rail--mobile .experiences-tab {
        width: 100%;
        padding: 10px 6px;
        border: 0;
        background: transparent;
        text-align: center;
        font-size: 16px;
        font-weight: 300;
        cursor: pointer;
        transition: opacity .2s ease;
    }

    .experiences-tabs__rail--mobile .experiences-tab.is-active { opacity: 1; }

    /* Underline móvil que se mueve */
    .experiences-tabs__underline {
        position: absolute;
        left: 0;
        bottom: 0;
        height: 2px;
        width: 40px;
        background: #111;
        transform: translate3d(18px,0,0);
        transition: transform .25s ease, width .25s ease;
        z-index: 2;
    }

    /* ---------- Content Swiper ---------- */
    .experiences-tabs__content {
        position: relative;
        min-width: 0; /* clave para evitar brincos de width en grid/flex */
    }

    /* Blindaje Swiper */
    .experiences-swiper,
    .experiences-swiper .swiper-wrapper,
    .experiences-swiper .swiper-slide {
        width: 100%;
    }

    .experiences-swiper { overflow: hidden; }

    .experience-slide { position: relative;
        padding-bottom: 20px;
    }

    .experience-slide__media { position: relative; }

    .experience-slide__media img {
        width: 100%;
        height: 320px;
        object-fit: cover;
        display: block;
    }

    /* ---------- Card (Mobile) ---------- */
    .experience-slide__card {
        position: relative;
        margin: -26px auto 0;
        max-width: 92%;
        background: #fff;
        padding: 18px;
        box-shadow: 0 0 15px -3px rgba(0,0,0,.25);
        border-bottom: 3px solid #C8B18B;
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .experience-slide__text {
        font-size: 16px;
        color: var(--pm-secondary-900);
        font-weight: 300;
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
        margin-bottom: 12px;
    }

    .experience-slide__cta {
        font-weight: 400;
        padding: 0;
    }

    /* Underline utility */
    .underline {
        text-decoration: underline;
        text-underline-offset: 6px;
        text-decoration-thickness: 1px;
    }

    /* ---------- Mobile arrows on image ---------- */
    .experiences-content__prev,
    .experiences-content__next {
        width: 40px;
        height: 40px;
        top: 44%;
        color: rgba(0,0,0,.45);
    }

    .experiences-content__prev::after,
    .experiences-content__next::after {
        font-size: 18px;
    }

    /* ---------- Mobile bullets (capsules) ---------- */
    /* Styles Swiper*/

    .experiences-content__pagination{display: block}

    .experiences-content__pagination .swiper-pagination-bullet {
        width: 16px;
        height: 8px;
        background: #A9AAAA;
        opacity: 0.6;
        border-radius: 12px;
        margin: 0 !important;
        transition: all 0.3s ease;
    }
    .experiences-content__pagination .swiper-pagination-bullet-active {
        width: 32px;
        height: 8px;
        background: #CFAB76;
        opacity: 1;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .experiences-content__pagination .swiper-pagination-bullet,
    .experiences-content__pagination .swiper-pagination-bullet-active {
        transition: all 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }
    .experiences-content__pagination {
        position: static;
        margin-top: 12px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }


    /* ===============================
       TABLET (≥ 768px)
    ================================ */

    @media (min-width: 768px) {

        .experience-slide {
            padding-bottom: 80px;
        }

        .experience-slide__card {
            box-shadow: 0 0 20px -3px rgba(0,0,0,.25);
        }

        .experiences-tabs__title {
            font-size: 32px;
            text-align: left;
            margin-bottom: 16px;
        }

        /* En tablet+ escondemos tabs mobile y mostramos rail desktop */
        .experiences-tabs__rail--mobile  { display: none; }
        .experiences-tabs__rail--desktop { display: block; }

        /* Desktop rail */
        .experiences-tabs__rail--desktop {
            position: relative;
            padding-left: 24px;
        }

        .experiences-tabs__rail--desktop::before {
            content: "";
            position: absolute;
            left: 10px;
            top: 10px;
            bottom: 10px;
            width: 1px;
            background: rgba(0,0,0,.15);
        }

        .experiences-tabs__indicator {
            display: block;
            position: absolute;
            left: 10px;
            top: 0;
            width: 2px;
            height: 32px;
            background: #111;
            transform: translateY(0);
            transition: transform .25s ease;
        }

        .experiences-tabs__rail--desktop .experiences-tab {
            display: block;
            padding: 8px 0;
            background: none;
            font-size: 16px;
            font-weight: 300;
            color: var(--pm-secondary-900);
            border: 0;
            text-align: left;
            cursor: pointer;
            transition: opacity .2s ease;
        }


        .experiences-tabs__rail--desktop .experiences-tab.is-active {
            font-weight: 500;
        }

        /* Imagen y card overlay */
        .experience-slide__media img { height: 420px; }

        .experience-slide__card {
            position: absolute;
            left: 24px;
            bottom: 24px;
            width: min(520px, 85%);
            margin-top: 0;
            max-width: none;
        }

        .experiences-content__pagination{display: none}

        /* Bullets y flechas normalmente ya no son “mobile look”
           Si quieres que sigan, no las tocamos aquí. */
    }

    /* ===============================
       DESKTOP (≥ 1024px)
    ================================ */

    @media (min-width: 1024px) {

        .experiences-tabs { padding: 60px 0; }

        .experience-slide__media img { height: 520px; }

        .experience-slide__card {
            left: 60px;
            bottom: 25px;
            width: min(620px, 80%);
            padding: 22px 22px 18px;
        }

        .experiences-tabs__rail--desktop .experiences-tab {
            font-size: 20px;
            line-height: 32px;
        }

    }

</style>

