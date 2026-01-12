<?php
 $hotel_hero_image_url = get_stylesheet_directory_uri() . '/assets/images/hotel-example.webp';
 $hotel_hero_image_alt = 'Hotel Playa Mujeres';
 $hotel_id = get_queried_object_id();
 $logo_dark = get_field('logo_dark', $hotel_id);
?>

<section class="hotel-hero">
    <div class="hotel-hero__media">
        <img class="hotel-hero__bg" src="<?php echo esc_url($hotel_hero_image_url); ?>" alt="<?php echo esc_attr($hotel_hero_image_alt); ?>" loading="eager" decoding="async">
        <div class="hotel-hero__overlay"></div>
    </div>
    <div class="hotel-hero__content container">
        <div class="hotel-hero__tags">
            <span >All Inclusive</span> / <span >Adults Only</span> / <span >Weddings</span>
        </div>
        <div class="hotel-hero__inner">
            <div class="row g-0">
                <div class="col-12 col-lg-6">
                    <div class="hotel-hero__logo">
                        <img src="<?php echo esc_url($logo_dark['url']); ?>" alt="<?php echo esc_attr(get_the_title($hotel_id->ID)); ?>">
                    </div>
                    <p class="hotel-hero__description">Welcome to Excellence Coral Playa Mujeres, where elevated luxury  experiences spoil guests throughout their stay. Right along the white  sands of the Mexican Caribbean, this contemporary escape for adults only combines the best stunning oceanfront scenery with exquisite All  Inclusive enhancements. Uncover each onsite wonder and discover the full immersive experience of this sophisticated Cancun paradise resort.</p>
                    <div class="arrow-circle">
                        <a href="/" class="video-hero__button arrow-circle__link" target="_blank">
                        <span class="arrow-circle__label">
                            Book your stay at Excellence Coral Playa Mujeres
                        </span>
                            <span class="arrow-circle__icon">
                            <span class="arrow">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.25 15.75L21 12M21 12L17.25 8.25M21 12H3"
                                          stroke="white" stroke-width="0.75"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="circle">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.375" y="0.375"
                                          width="27.25" height="27.25"
                                          rx="13.625"
                                          stroke="white" stroke-width="0.75"/>
                                </svg>
                            </span>
                        </span>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-lg-6 d-flex justify-content-lg-end align-items-start align-items-lg-end">
                    <a class="btn btn-primary hotel-hero__cta" href="#">Reservar ahora</a>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    .hotel-hero {
        position: relative;
        overflow: hidden;
    }
    .hotel-hero__media {
        position: absolute;
        inset: 0;
        z-index: 0;
    }
    .hotel-hero__bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }
    .hotel-hero__overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 1;
    }
    .hotel-hero__content {
        position: relative;
        z-index: 2;
    }
    .hotel-hero__tags {
        color: white;
        font-family: var(--pm-font-secondary);
        font-size: 16px;
        font-style: italic;
        font-weight: 500;
        line-height: normal;
        letter-spacing: 2px;
    }
    .hotel-hero__description {
        color: white;
        text-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
    }
    .hotel-hero__cta {
        color: white;
    }
    .hotel-hero__logo {
        display: flex;
        justify-content: start;
        margin-bottom: 24px;
    }
    .hotel-hero__logo img {
        width: 242px;
        object-fit: cover;
        height: auto;
    }

    @media (min-width: 992px) {
        .hotel-hero {
            min-height: 100vh;
        }
        .hotel-hero {
            display: flex;
            align-items: stretch;
        }
        .hotel-hero__content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-top: 140px;
            padding-bottom: 72px;
        }
        .hotel-hero__cta {
            white-space: nowrap;
        }
    }
</style>