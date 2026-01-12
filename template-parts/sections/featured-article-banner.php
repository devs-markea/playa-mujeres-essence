<?php
// Fields
$background_image_override = get_sub_field('background_image_override');
$bg_desktop = $background_image_override['desktop'] ?? null;
$bg_mobile  = $background_image_override['mobile']  ?? null;

$layout_width = get_sub_field('layout_width') ?: 'full';

$posts = get_sub_field('post'); // relationship

$post_id = 0;

if (is_array($posts) && !empty($posts)) {
    $first = $posts[0];
    $post_id = ($first instanceof WP_Post) ? $first->ID : (int) $first;
}

// Post data
$post_link  = $post_id ? get_permalink($post_id) : '';
$post_title = $post_id ? get_the_title($post_id) : '';
$post_excerpt = $post_id ? get_the_excerpt($post_id) : '';

// Background fallbacks
$bg_desktop_url = $bg_desktop['url'] ?? ($post_id ? get_the_post_thumbnail_url($post_id, 'full') : '');
$bg_mobile_url  = $bg_mobile['url']  ?? ($post_id ? get_the_post_thumbnail_url($post_id, 'large') : $bg_desktop_url);
?>

<?php if ( $post_id && $post_link ) : ?>
    <section class="featured-article-banner featured-article-banner--<?php echo esc_attr($layout_width); ?>">
        <a class="featured-article-banner__link" href="<?php echo esc_url($post_link); ?>">
            <picture class="featured-article-banner__media">
                <?php if ( $bg_mobile_url ) : ?>
                    <source media="(max-width: 767px)" srcset="<?php echo esc_url($bg_mobile_url); ?>">
                <?php endif; ?>
                <img src="<?php echo esc_url($bg_desktop_url); ?>" alt="" loading="lazy">
            </picture>

            <div class="featured-article-banner__overlay" aria-hidden="true"></div>

            <div class="featured-article-banner__inner container">
                <div class="featured-article-banner__content">

                    <?php if ( $post_title ) : ?>
                        <h2 class="featured-article-banner__title"><?php echo esc_html($post_title); ?></h2>
                    <?php endif; ?>
                </div>
                <span class="featured-article-banner__cta text-white">Read more</span>
            </div>
        </a>
    </section>
<?php endif; ?>

<style>
    /* ===============================
   Featured Article Banner
   =============================== */

    .featured-article-banner {
        display: block;
        position: relative;
        overflow: hidden;
        background: #000;
    }

    /* Width variants */
    .featured-article-banner--contained .featured-article-banner__inner {
        max-width: 1140px;
        margin: 0 auto;
    }

    .featured-article-banner--full {
        width: 100%;
    }

    /* Clickable wrapper */
    .featured-article-banner__link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    /* Media */
    .featured-article-banner__media {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .featured-article-banner__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Overlay */
    .featured-article-banner__overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.20);
        z-index: 2;
    }

    /* Content wrapper */
    .featured-article-banner__inner {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 3;
        padding: 40px 0 20px;
    }

    /* Content */
    .featured-article-banner__content {
        border-bottom: 1px solid rgba(255, 255, 255, 0.25);;
        border-top: 1px solid rgba(255, 255, 255, 0.25);;
        max-width: 640px;
        color: #fff;
        padding: 84px 28px;
        margin-bottom: 20px;
    }

    /* Label */
    .featured-article-banner__label {
        display: inline-block;
        margin-bottom: 12px;
        font-size: 0.75rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        opacity: 0.85;
    }

    /* Title */
    .featured-article-banner__title {
        font-family: var(--pm-font-secondary);
        font-weight: 500;
        font-style: italic;
        font-size: 24px;
        line-height: 100%;
        letter-spacing: 2px;
        text-align: center;
        margin: 0;
        text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    }

    /* Excerpt */
    .featured-article-banner__excerpt {
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 24px;
        opacity: 0.9;
    }

    /* CTA */
    .featured-article-banner__cta {
        display: inline-block;
        pointer-events: none;
    }

    /* ===============================
       Desktop
       =============================== */
    @media (min-width: 767px) {
        .featured-article-banner{
            display: none;
        }
    }

</style>
