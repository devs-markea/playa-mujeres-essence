<?php
$title = get_sub_field('title');
$description = get_sub_field('description');
$hotels = get_sub_field('hotels');


// Divide el array en 2 mitades
$count = is_array($hotels) ? count($hotels) : 0;
$half  = (int) ceil($count / 2);

$row1 = array_slice($hotels, 0, $half);
$row2 = array_slice($hotels, $half);
?>

<section class="hotels-parallax" id="hotels-parallax">
    <div class="hotel-extraordinary-heading container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <div class="row g-0">
                    <div class="col-8 col-md-4 mx-auto">
                        <?php
                        $title = esc_html($title);
                        $words = explode(' ', $title);
                        if (count($words) > 1) {
                            $words[1] = '<span class="underline">' . $words[1] . '</span>';
                        }
                        $formatted_title = implode(' ', $words);
                        ?>
                        <h2><?= $formatted_title; ?></h2>
                    </div>
                    <div class="col-12 col-md-7 offset-md-1">
                        <div class="hotel-extraordinary-heading__description">
                            <?php echo wp_kses_post( $description ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hotels-parallax__row row g-3 g-md-4 parallax-row parallax-row--right">
        <?php foreach ($row1 as $hotel) :
            $logo_dark = get_field('logo_dark', $hotel->ID);
            $featured  = get_the_post_thumbnail_url($hotel->ID);
            $hotel_url = get_permalink($hotel->ID);
            ?>
            <div class="col-6 col-md-3" data-hotel-id="<?= esc_attr($hotel->ID); ?>">
                <div onclick="location.href='<?= esc_url($hotel_url); ?>';"
                     class="cover-cc-bg p-2 hotel-extraordinary-card__image-wrapper"
                     style="background-image:url('<?= esc_url($featured); ?>');">
                    <img class="hotel-extraordinary-card__logo d-block mx-auto mt-3"
                         src="<?= esc_url($logo_dark['url'] ?? ''); ?>"
                         alt="<?= esc_attr(get_the_title($hotel->ID)); ?>">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="hotels-parallax__row row g-3 g-md-4 parallax-row parallax-row--left">
        <?php foreach ($row2 as $hotel) :
            $logo_dark = get_field('logo_dark', $hotel->ID);
            $featured  = get_the_post_thumbnail_url($hotel->ID);
            $hotel_url = get_permalink($hotel->ID);
            ?>
            <div class="col-6 col-md-3" data-hotel-id="<?= esc_attr($hotel->ID); ?>">
                <div onclick="location.href='<?= esc_url($hotel_url); ?>';"
                     class="cover-cc-bg p-2 hotel-extraordinary-card__image-wrapper"
                     style="background-image:url('<?= esc_url($featured); ?>');">
                    <img class="hotel-extraordinary-card__logo d-block mx-auto mt-3"
                         src="<?= esc_url($logo_dark['url'] ?? ''); ?>"
                         alt="<?= esc_attr(get_the_title($hotel->ID)); ?>">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<style>
    /* =========================
   MOBILE FIRST (default)
========================= */
    .hotels-parallax {
        padding-top: 80px;
        padding-bottom: 120px;
    }
    .hotel-extraordinary-heading {
        margin-bottom: 32px;
    }
    .hotel-extraordinary-heading h2 {
        font-family: var(--pm-font-secondary);
        font-weight: 500;
        font-style: italic;
        font-size: 20px;
        letter-spacing: 2px;
        text-align: center;
    }
    .hotel-extraordinary-heading .underline {
        text-decoration: underline;
    }

    .hotel-extraordinary-heading p {
        font-weight: 300;
        font-size: 16px;
    }

    .parallax-row--right,
    .parallax-row--left{
        margin-bottom: 1rem;
    }

    .hotel-extraordinary-card__image-wrapper {
        cursor: pointer;
        /*height: 274px;*/
        aspect-ratio: 2 / 3;
        overflow: hidden;
        background-size: cover;
        position: relative;
    }

    .hotel-extraordinary-card__image-wrapper::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient( to bottom, rgba(0, 0, 0, 0.6) 0%, rgba(255, 255, 255, 0) 50% );
        pointer-events: none;
        z-index: 1;
    }

    .hotel-extraordinary-card__logo {
        width: 100%;
        max-width: 5.5rem;
        height: auto;
        position: relative;
        z-index: 2;
    }

    /* Section wrapper */
    .hotels-parallax {
        overflow-x: hidden;
        overflow-y: visible;
    }

    /* Mobile: SIN parallax */
    .parallax-row {
        transform: none;
    }

    /* Mobile: último centrado (cuando hay impares) */
    .parallax-row > div:last-child {
        margin-left: auto;
        margin-right: auto;
    }

    /* =========================
       DESKTOP (parallax ON)
    ========================= */
    @media (min-width: 768px) {

        .hotels-parallax {
            /* zona segura para desplazamiento lateral */
            --parallax-safe: 0;
            padding: 128px var(--parallax-safe) 216px;
        }

        .hotel-extraordinary-heading h2 {
            font-family: var(--pm-font-secondary);
            font-size: 32px;
            text-align: start;
        }

        .hotel-extraordinary-card__image-wrapper {
            /*height: 440px;*/
            aspect-ratio: 3/4;
        }

        .parallax-row {
            will-change: transform;
        }

        .parallax-row--right {
            margin-bottom: 2rem;
        }

        /* Ya no forzamos centrado en desktop */
        .parallax-row > div:last-child {
            margin-left: 0;
            margin-right: 0;
        }
    }

    /* =========================
       REDUCED MOTION
    ========================= */
    @media (prefers-reduced-motion: reduce) {
        .parallax-row {
            transform: none !important;
            transition: none !important;
        }
    }

</style>

