<?php
$args = wp_parse_args($args ?? array(), array(
    'blog_page_id'  => 0,
    'blog_settings' => array(),
));

$blog_settings = is_array($args['blog_settings']) ? $args['blog_settings'] : array();

$heading_hero          = isset($blog_settings['page_heading']) ? $blog_settings['page_heading'] : '';
$heading_level_hero    = isset($blog_settings['page_heading_level']) ? $blog_settings['page_heading_level'] : 'h1';

$description_hero      = isset($blog_settings['page_description']) ? $blog_settings['page_description'] : '';

$allowed_tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
if (! in_array($heading_level_hero, $allowed_tags, true)) {
    $heading_level_hero = 'h1';
}


$hero_query = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 8,
    'ignore_sticky_posts' => true,
));

if (! $hero_query->have_posts() && ! $heading_hero &&  ! $description_hero) {
    return;
}
?>

<section class="blog-hero">
    <div class="container">

        <?php if ( $heading_hero || $description_hero) : ?>
        <div class="row mb-4 mb-lg-5">
            <div class="col-lg-8">
                <div class="blog-hero__subheading mb-2">
                    <div class="blog-hero__subheading--divider"></div>
                    <span>
                        <?php the_title(); ?>
                    </span>
                </div>

            <?php if ($heading_hero) : ?>
            <<?php echo esc_attr($heading_level_hero); ?> class="blog-hero__heading mb-0">
            <?php echo esc_html($heading_hero); ?>
        </<?php echo esc_attr($heading_level_hero); ?>>
    <?php endif; ?>

        <?php if ($description_hero) : ?>
            <div class="blog-hero__description mt-3">
                <?php echo wp_kses_post($description_hero); ?>
            </div>
        <?php endif; ?>

    </div>
    </div>
    <?php endif; ?>

    <?php if ($hero_query->have_posts()) : ?>
        <div class="blog-hero__slider-wrap">
            <div class="blog-hero__swiper swiper" data-blog-hero-swiper>
                <div class="swiper-wrapper">
                    <?php
                    while ($hero_query->have_posts()) :
                        $hero_query->the_post();
                        ?>
                        <div class="blog-hero__slide swiper-slide">
                            <?php
                            get_template_part(
                                'template-parts/blog/hero-main-card',
                                null,
                                array(
                                    'post_id'     => get_the_ID(),
                                    'heading_tag' => 'h2',
                                )
                            );
                            ?>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>

            <div class="blog-hero__controls">
                <button type="button" class="blog-hero__nav blog-hero__nav--prev" aria-label="Previous posts">
                    <span aria-hidden="true">←</span>
                </button>
                <button type="button" class="blog-hero__nav blog-hero__nav--next" aria-label="Next posts">
                    <span aria-hidden="true">→</span>
                </button>
            </div>
        </div>
    <?php endif; ?>

    </div>
</section>

<style>
    /* ── Mobile-first base styles ── */
    .blog-hero {
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #f7f4ef;
        min-height: calc(90vh - 135px);
        padding: 0;
        margin-top: 135px;
    }

    .blog-hero__subheading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .blog-hero__subheading--divider {
        width: 2rem;
        height: 1px;
        background: black;
    }

    .blog-hero__subheading span {
        color: black;
        font-size: 16px;
        font-style: normal;
        font-weight: var(--fw-light);
        line-height: normal;
        text-transform: uppercase;
    }

    .blog-hero__heading {
        font-family: var(--pm-font-secondary);
        font-size: 26px;
        font-style: italic;
        font-weight: var(--fw-medium);
        letter-spacing: 2px;
        color: #323232;
    }

    .blog-hero__description {
        color: #323232;
        font-size: 14px;
        line-height: 1.7;
    }

    .blog-hero__slider-wrap {
        position: relative;
        overflow: hidden;
    }

    .blog-hero__swiper {
        overflow: hidden;
        --blog-hero-featured-width: 82%;
        --blog-hero-compact-width: 82%;
    }

    .blog-hero__swiper .swiper-wrapper {
        align-items: stretch;
        gap: 0;
    }

    .blog-hero__slide {
        width: 82%;
        flex-shrink: 0;
        height: auto;
        cursor: pointer;
        min-width: 0;
    }

    .blog-hero-card {
        position: relative;
        min-height: 420px;
        height: 100%;
        background: #d8d8d8;
        overflow: hidden;
    }

    .blog-hero-card__media {
        position: absolute;
        inset: 0;
        z-index: 0;
    }

    .blog-hero-card__media img,
    .blog-hero-card__image {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .blog-hero-card__overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background: linear-gradient(
                180deg,
                rgba(0, 0, 0, 0) 9.33%,
                rgba(0, 0, 0, 0.60) 78.15%
        );
    }

    .blog-hero-card__content {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 1.5rem;
        z-index: 2;
        padding: 24px 18px 20px;
        opacity: 1;
        transform: translateY(0);
        transition:
                opacity 0.4s ease-out,
                transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        pointer-events: none;
    }

    .blog-hero-card__title {
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: var(--fw-medium);
        line-height: 1.15;
        color: #fff;
        font-size: 24px;
        max-width: 100%;
        opacity: 0.9;
        transform: translateY(6px);
        transition:
                font-size 0.5s cubic-bezier(0.22, 1, 0.36, 1),
                line-height 0.5s cubic-bezier(0.22, 1, 0.36, 1),
                opacity 0.45s ease-out,
                transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .blog-hero-card__title-label {
        display: none;
    }

    .blog-hero-card__drawer {
        opacity: 1;
        transform: none;
        pointer-events: auto;
        transition:
                opacity 0.45s ease-out,
                transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .blog-hero-card__excerpt {
        display: block;
        max-width: 100%;
    }

    .blog-hero-card__footer {
        margin-top: 18px;
        pointer-events: auto;
    }

    .blog-hero-card__cta-link {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        color: #fff;
        text-decoration: none;
        pointer-events: auto;
    }

    .blog-hero-card__cta-label {
        font-size: 14px;
        line-height: 1;
        color: #fff;
    }

    .blog-hero-card__cta-icon {
        width: 34px;
        height: 34px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex: 0 0 34px;
    }

    .blog-hero__controls {
        display: flex;
        justify-content: flex-end;
        gap: 16px;
        margin-top: 18px;
    }

    .blog-hero__nav.is-disabled {
        opacity: 0.3;
        pointer-events: none;
    }

    .blog-hero__nav {
        width: 40px;
        height: 40px;
        border: 0;
        background: transparent;
        color: #323232;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        line-height: 1;
        padding: 0;
        cursor: pointer;
    }

    /* ── Desktop styles ── */
    @media (min-width: 992px) {
        .blog-hero {
            flex-direction: column;
            min-height: calc(100vh - 90px);
            padding: 0;
            margin-top: 90px;
        }

        .blog-hero__heading {
            font-size: 32px;
        }

        .blog-hero__swiper .swiper-wrapper {
            display: flex;
            align-items: stretch;
            gap: unset;
        }

        .blog-hero__slide {
            width: var(--blog-hero-compact-width) !important;
            flex: 0 0 var(--blog-hero-compact-width) !important;
            transition:
                    width 0.45s cubic-bezier(0.22, 1, 0.36, 1),
                    flex-basis 0.45s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.45s ease,
                    opacity 0.35s ease;
            overflow: hidden;
        }

        .blog-hero__slide.is-featured {
            width: var(--blog-hero-featured-width) !important;
            flex-basis: var(--blog-hero-featured-width) !important;
        }

        .blog-hero__slide:not(.is-featured) .blog-hero-card__content {
            padding: 20px 14px 16px;
            transform: translateY(10px);
        }

        .blog-hero__slide.is-featured .blog-hero-card__content {
            padding: 32px 24px 24px;
            opacity: 1;
            transform: translateY(0);
        }

        .blog-hero__slide .blog-hero-card__title-label {
            display: block;
            font-family: var(--pm-font-secondary);
            font-style: italic;
            font-weight: var(--fw-medium);
            font-size: 16px;
            line-height: 1.25;
            color: #fff;
            opacity: 1;
            transform: translateY(0);
            transition:
                    opacity 0.15s ease,
                    transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .blog-hero__slide .blog-hero-card__drawer {
            position: absolute;
            left: 24px;
            right: 24px;
            bottom: 24px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(12px);
            pointer-events: none;
            transition:
                    opacity 0.42s ease,
                    transform 0.42s cubic-bezier(0.22, 1, 0.36, 1),
                    visibility 0s linear 0.42s;
        }

        .blog-hero__slide .blog-hero-card__title {
            font-size: 24px;
            line-height: 1.16;
            max-width: 100%;
            opacity: 1;
            transform: translateY(0);
        }

        .blog-hero__slide .blog-hero-card__excerpt {
            display: block;
            width: auto;
            max-width: 100%;
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
            line-height: 1.45;
        }

        .blog-hero__slide .blog-hero-card__footer {
            align-items: end;
        }

        .blog-hero__slide.is-featured .blog-hero-card__title-label {
            opacity: 0;
            transform: translateY(20px);
        }

        .blog-hero__slide.is-featured .blog-hero-card__drawer {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
            transition-delay: 0.36s;
        }

        .blog-hero__slide:not(.is-featured) .blog-hero-card__drawer {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(12px) !important;
            pointer-events: none !important;
            transition:
                    opacity 0.16s ease !important,
                    transform 0.2s cubic-bezier(0.22, 1, 0.36, 1) !important,
                    visibility 0s linear 0.16s !important;
        }

        .blog-hero__slide:not(.is-featured) .blog-hero-card__title-label {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        .blog-hero-card {
            min-height: 448px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .blog-hero__slide,
        .blog-hero-card__content {
            transition: none !important;
        }
    }

</style>
