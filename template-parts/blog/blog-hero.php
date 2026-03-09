<?php
$args = wp_parse_args($args ?? array(), array(
    'blog_page_id'  => 0,
    'blog_settings' => array(),
));

$blog_settings = is_array($args['blog_settings']) ? $args['blog_settings'] : array();

$heading_hero          = isset($blog_settings['heading_hero']) ? $blog_settings['heading_hero'] : '';
$heading_level_hero    = isset($blog_settings['heading_level_hero']) ? $blog_settings['heading_level_hero'] : 'h1';
$subheading_hero       = isset($blog_settings['subheading_hero']) ? $blog_settings['subheading_hero'] : '';
$subheading_level_hero = isset($blog_settings['subheading_level_hero']) ? $blog_settings['subheading_level_hero'] : 'h2';
$description_hero      = isset($blog_settings['description_hero']) ? $blog_settings['description_hero'] : '';

$allowed_tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
if (! in_array($heading_level_hero, $allowed_tags, true)) {
    $heading_level_hero = 'h1';
}
if (! in_array($subheading_level_hero, $allowed_tags, true)) {
    $subheading_level_hero = 'h2';
}

$hero_query = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'ignore_sticky_posts' => true,
));

if (! $hero_query->have_posts() && ! $heading_hero && ! $subheading_hero && ! $description_hero) {
    return;
}
?>

<section class="blog-hero" data-force-header-theme="menu">
    <div class="container">

        <?php if ($subheading_hero || $heading_hero || $description_hero) : ?>
        <div class="row mb-4 mb-lg-5">
            <div class="col-lg-8">

                <?php if ($subheading_hero) : ?>
                <<?php echo esc_attr($subheading_level_hero); ?> class="blog-hero__subheading mb-2">
                <?php echo esc_html($subheading_hero); ?>
            </<?php echo esc_attr($subheading_level_hero); ?>>
            <?php endif; ?>

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
    .blog-hero {
        background: #f7f4ef;
        margin-top: 90px;
    }

    .blog-hero__subheading {
        font-family: var(--pm-font-secondary);
        font-size: 18px;
        font-style: italic;
        font-weight: 500;
        letter-spacing: 1.5px;
        color: #323232;
    }

    .blog-hero__heading {
        font-family: var(--pm-font-secondary);
        font-size: 32px;
        font-style: italic;
        font-weight: 500;
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
    }

    .blog-hero__swiper .swiper-wrapper {
        align-items: stretch;
    }

    .blog-hero__slide {
        width: var(--blog-hero-compact-width);
        flex-shrink: 0;
        height: auto;
        cursor: pointer;
        min-width: 0;
    }

    .blog-hero-card {
        position: relative;
        min-height: 560px;
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
        transition: transform 0.5s ease;
    }

    .blog-hero-card__overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background: linear-gradient(
                180deg,
                rgba(0, 0, 0, 0.08) 0%,
                rgba(0, 0, 0, 0.28) 55%,
                rgba(0, 0, 0, 0.72) 100%
        );
    }

    .blog-hero-card__content {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 2;
        padding: 20px 14px 16px;
        transition:
                padding 0.35s ease,
                transform 0.35s ease,
                opacity 0.35s ease;
        pointer-events: none;
    }

    .blog-hero-card__title {
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        line-height: 1.15;
        color: #fff;
        font-size: 18px;
        transition:
                font-size 0.35s ease,
                max-width 0.35s ease;
    }

    .blog-hero-card__excerpt,
    .blog-hero-card__footer {
        display: none;
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

    @media (min-width: 992px) {
        .blog-hero {
            min-height: calc(100vh - 90px);
        }

        /*.blog-hero__swiper {*/
        /*    overflow: hidden;*/
        /*}*/

        .blog-hero__swiper .swiper-wrapper {
            display: flex;
            align-items: stretch;
            transform: none !important;
        }

        .blog-hero__slide {
            width: var(--blog-hero-compact-width) !important;
            flex: 0 0 var(--blog-hero-compact-width) !important;
            transition:
                    width 0.45s ease,
                    flex-basis 0.45s ease,
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
        }

        .blog-hero__slide.is-featured .blog-hero-card__content {
            padding: 32px 24px 24px;
        }

        .blog-hero__slide:not(.is-featured) .blog-hero-card__title {
            font-size: 18px;
            max-width: 100%;
        }

        .blog-hero__slide.is-featured .blog-hero-card__title {
            font-size: 30px;
            max-width: 78%;
        }

        .blog-hero__slide:not(.is-featured) .blog-hero-card__excerpt,
        .blog-hero__slide:not(.is-featured) .blog-hero-card__footer {
            display: none;
        }

        .blog-hero__slide.is-featured .blog-hero-card__excerpt {
            display: block;
            max-width: 70%;
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
            line-height: 1.45;
            animation: blogHeroFadeUp 0.35s ease;
        }

        .blog-hero__slide.is-featured .blog-hero-card__footer {
            display: block;
            margin-top: 18px;
            pointer-events: auto;
            animation: blogHeroFadeUp 0.4s ease;
        }

        .blog-hero__slide.is-featured .blog-hero-card__image {
            transform: scale(1.03);
        }
    }

    @media (max-width: 991.98px) {
        .blog-hero {
            padding: 110px 0 40px;
        }

        .blog-hero__heading {
            font-size: 26px;
        }

        .blog-hero__swiper {
            --blog-hero-featured-width: 82%;
            --blog-hero-compact-width: 82%;
        }

        .blog-hero__swiper .swiper-wrapper {
            gap: 0;
        }

        .blog-hero__slide {
            width: 82%;
            flex-shrink: 0;
        }

        .blog-hero-card {
            min-height: 420px;
        }

        .blog-hero-card__content,
        .blog-hero__slide.is-featured .blog-hero-card__content {
            padding: 24px 18px 20px;
        }

        .blog-hero-card__title,
        .blog-hero__slide.is-featured .blog-hero-card__title {
            font-size: 24px;
            max-width: 100%;
        }

        .blog-hero-card__excerpt,
        .blog-hero__slide.is-featured .blog-hero-card__excerpt {
            display: block;
            max-width: 100%;
        }

        .blog-hero-card__footer,
        .blog-hero__slide.is-featured .blog-hero-card__footer {
            display: block;
            margin-top: 18px;
            pointer-events: auto;
        }
    }

    @keyframes blogHeroFadeUp {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>