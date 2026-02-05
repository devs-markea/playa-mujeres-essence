<?php
    $heroOpacity = 0.45; // valor entre 0 y 1
    $heroImageDesktop = "https://stg-playamujeres.markeagroup.com/wp-content/uploads/2021/12/Background-Dreams.webp";
    $heroImageMobile  = "https://stg-playamujeres.markeagroup.com/wp-content/uploads/2021/12/Background-Dreams-768x432.webp";
?>
<section class="residences-hero" style="--hero-overlay-opacity: <?= $heroOpacity ?>;">
    <div class="residence-hero__media">
        <picture>
            <!-- Mobile -->
            <source
                    srcset="<?= $heroImageMobile ?>"
                    media="(max-width: 768px)"
            >

            <!-- Desktop -->
            <img
                    src="<?= $heroImageDesktop ?>"
                    width="2160"
                    height="1214"
                    alt="Residences Playa Mujeres"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    class="residences-hero__bg"
            >
        </picture>
    </div>
    <div class="residence-hero__content">
        <p class ="residences-hero__title">Residences Playa Mujeres</p>
    </div>
</section>
<section class="residence-content">
    <div class="container">

        <!-- Título -->
        <div class="row justify-content-center text-center mb-5 residence-content__title-content">
            <div class="col-12">
                <h1 class="pm-text-block__heading residence-content__title">
                    Luxury and Private Development
                </h1>
            </div>
        </div>

        <!-- Cards -->
        <div class="row g-5 justify-content-center residence-content__cards">
            <!-- Card -->
            <div class="col-12 col-md-6 col-lg-5">
                <div class="residence-content__card">
                    <div class="residence-content__card-image">
                        <figure style="background-image:url('https://stg-playamujeres.markeagroup.com/wp-content/uploads/2021/12/Culinary-Offer-1-Dreams.webp')"></figure>

                        <div class="residence-content__card-details">
                            <div class="residence-content__card-inner">
                                <img src="https://stg-playamujeres.markeagroup.com/wp-content/uploads/2021/12/logo-dark-dreams.webp" alt="Dreams Playa Mujeres">

                                <p class="residence-content__card-description">
                                    Go deep into a natural world where the mundane becomes  extraordinary, time stops and each moment is worth treasuring. In this  space of natural connection, harmony with the environment will lead you  to love life and truly live the La Amada experience.
                                </p>

                                <a href="#" class="btn btn-primary btn-border-bottom-black">
                                    Get to know the La Amada residences
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card -->
            <div class="col-12 col-md-6 col-lg-5">
                <div class="residence-content__card">
                    <div class="residence-content__card-image">
                        <figure style="background-image:url('https://stg-playamujeres.markeagroup.com/wp-content/uploads/2021/12/Culinary-Offer-1-Dreams.webp')"></figure>

                        <div class="residence-content__card-details">
                            <div class="residence-content__card-inner">
                                <img src="https://stg-playamujeres.markeagroup.com/wp-content/uploads/2021/12/logo-dark-dreams.webp" alt="Dreams Playa Mujeres">

                                <p class="residence-content__card-description">
                                    Go deep into a natural world where the mundane becomes  extraordinary, time stops and each moment is worth treasuring. In this  space of natural connection, harmony with the environment will lead you  to love life and truly live the La Amada experience.
                                </p>

                                <a href="#" class="btn btn-primary btn-border-bottom-black">
                                    Get to know the La Amada residences
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    /* =========================
       Sección Residence Hero
    ========================= */
    .residences-hero {
        position: relative;
        width: 100%;
        height: 80vh; /* altura completa del viewport */
        overflow: hidden;
    }

    /* Contenedor de la imagen */
    .residence-hero__media {
        position: absolute;
        inset: 0; /* top, right, bottom, left: 0 */
        z-index: 1;
    }
    /* Overlay */
    .residences-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, var(--hero-overlay-opacity, 0.4));
        z-index: 2;
    }
    /* Imagen fullscreen */
    .residence-hero__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Contenido */
    .residence-hero__content {
        position: absolute;
        bottom: 48px;
        left: 60px;
        right: 4rem;
        z-index: 2;

        color: #fff;
        max-width: 600px;
        /* opcional: mejora legibilidad */
        text-shadow: 0 2px 10px rgba(0, 0, 0, 9);
    }
    .residences-hero__title {
        font-size: 48px;
        font-style: italic;
        font-weight: 700;
        line-height: 1.2; /* recomendado para títulos */
        font-family: var(--pm-font-secondary);
    }

    /* Responsive opcional */
    @media (max-width: 768px) {
        .residence-hero__content {
            bottom: 1.5rem;
            left: 1.5rem;
            max-width: 90%;
        }
    }
    /* =========================
       Sección Residence Content
    ========================= */
    .residence-content {
        margin-bottom: 276px;
    }

    /* =========================
       Título
    ========================= */
    .residence-content__title {
        max-width: 18ch;
        margin: 14vh auto;   /* ← ESTO es lo que faltaba */
        font-family: var(--pm-font-secondary);
        font-size: 48px;
        font-style: italic;
        line-height: 1.4;
        font-weight: 500;
    }

    /* =========================
       Cards
    ========================= */
    .residence-content__card {
        position: relative;
        width: 532px;
        max-width: 100%;
    }

    /* Mobile */
    @media (max-width: 767px) {
        .residence-content__card {
            width: 100%;
        }
    }

    /* =========================
       Card inner
    ========================= */
    .residence-content__card-inner {
        padding: 30px 30px 0;
        box-sizing: border-box;
    }

    /* =========================
       Imagen principal
    ========================= */
    .residence-content__card-image figure {
        width: 100%;
        height: 420px;
        margin: 0 auto;
        background-size: cover;
        background-position: center;
    }
    /* Tablet */
    @media (max-width: 991px) {
        .residence-content__card-image figure {
            height: 340px;
        }
    }

    /* Mobile */
    @media (max-width: 575px) {
        .residence-content__card-image figure {
            height: 260px;
        }
    }
    /* =========================
       Logo
    ========================= */
    .residence-content__card-details img {
        max-width: 200px;
        margin-bottom: 1rem;
    }

    /* =========================
       Descripción
    ========================= */
    .residence-content__card-description {
        font-family: var(--pm-font-primary);
        font-weight: 300;
        font-size: 16px;
        line-height: 1.4;
    }

    /* =========================
       Zig-Zag (solo desktop)
    ========================= */
    @media (min-width: 768px) {

        /* Columna izquierda: sube */
        .residence-content__cards > .col-lg-5:nth-child(1) {
            transform: translateY(-60px);
        }

        /* Columna derecha: baja */
        .residence-content__cards > .col-lg-5:nth-child(2) {
            transform: translateY(180px);
        }
    }

    /* =========================
       Reset mobile
    ========================= */
    @media (max-width: 991px) {
        .residence-content__card {
            transform: none;
        }
    }


    /* =========================
       Mobile: 1 columna
    ========================= */
    @media (max-width: 991px) {
        .residence-content__cards {
            grid-template-columns: 1fr;
        }

        .residence-content__card {
            transform: none;
        }
    }

</style>