<?php
$title              = get_sub_field('title');
$description        = get_sub_field('description');
$subscribe_text     = get_sub_field('subscribe_button_text') ?: 'Subscribe';
$bg                 = get_sub_field('background_image');
$layout_width       = get_sub_field('layout_width'); // 'full' | 'narrow'
$form_position      = get_sub_field('form_position');


// controla la columna
$col_class = ($layout_width === 'full_width')
    ? 'col-12'
    : 'col-12 col-md-10';

$bg_url = !empty($bg['sizes']['large'])
    ? $bg['sizes']['large']
    : ($bg['url'] ?? '');

$justify_class = 'justify-content-center';
if (is_string($form_position)) {
    $pos = strtolower(trim($form_position));
    if ($pos === 'left') {
        $justify_class = 'justify-content-start';
    } elseif ($pos === 'right' || $pos === 'rigth') {
        $justify_class = 'justify-content-end';
    } elseif ($pos === 'center') {
        $justify_class = 'justify-content-center';
    }
}

?>

<section class="newsletter-subscribe-banner">
    <div class="row g-0">

        <div class="<?php echo esc_attr($col_class); ?> mx-auto">
            <div class="newsletter-subscribe-banner__background"
                 style="background-image:url('<?php echo esc_url($bg_url); ?>')">

                <div class="newsletter-subscribe-banner__overlay"></div>

                <div class="newsletter-subscribe-banner__content">
                    <div class="container">
                        <div class="row g-0 <?php echo esc_attr($justify_class); ?>">
                            <div class="col-12 col-md-5">
                                <div class="newsletter-subscribe-banner__inner">

                                    <?php if ($title): ?>
                                        <h2 class="newsletter-subscribe-banner__title">
                                            <?php echo esc_html($title); ?>
                                        </h2>
                                    <?php endif; ?>

                                    <?php if ($description): ?>
                                        <p class="newsletter-subscribe-banner__description">
                                            <?php echo esc_html($description); ?>
                                        </p>
                                    <?php endif; ?>

                                    <form class="newsletter-subscribe-banner__form">
                                        <input
                                                type="email"
                                                class="newsletter-subscribe-banner__input"
                                                placeholder="Type your email address"
                                                required
                                        >
                                        <button type="submit"
                                                class="newsletter-subscribe-banner__submit arrow-circle__link">
                                            <span class="arrow-circle__label">
                                                        <?php echo esc_html($subscribe_text); ?>
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
                                                            <svg width="28" height="30" viewBox="0 0 28 28" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.375" y="0.375"
                                                                      width="27.25" height="27.25"
                                                                      rx="13.625"
                                                                      stroke="white" stroke-width="0.75"/>
                                                            </svg>
                                                        </span>
                                                        </span>
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</section>

<style>
    /* ===============================
   Newsletter Subscribe Banner
   First Mobile
   =============================== */

    /* SECTION spacing */
    .newsletter-subscribe-banner {
        padding: 3rem 0;
    }

    /* background */
    .newsletter-subscribe-banner__background {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: center;
        background-size: cover;
        background-position: center;
    }

    /* overlay */
    .newsletter-subscribe-banner__overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 1;
    }

    /* content layer */
    .newsletter-subscribe-banner__content {
        position: relative;
        z-index: 2;
        width: 100%;
        border-top: 1px solid rgba(255, 255, 255, 0.25);
        border-bottom: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0;
    }

    .newsletter-subscribe-banner__inner {
        padding: 40px 0;
        max-width: 100%;
    }

    /* typography */
    .newsletter-subscribe-banner__title {
        color: #fff;
        font-family: var(--pm-font-secondary);
        font-size: 24px;
        font-weight: 500;
        font-style: italic;
        letter-spacing: 2px;
        margin-bottom: 0.75rem;
    }

    .newsletter-subscribe-banner__description {
        color: #fff;
        font-size: 16px;
        font-weight: 300;
        margin-bottom: 1.5rem;
    }

    /* form */
    .newsletter-subscribe-banner__form {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-width: 100%;
    }

    .newsletter-subscribe-banner__input {
        width: 100%;
        height: 48px;
        padding: 0 1rem;
        background: white;
        font-size: 16px;
        font-weight: 300;
        color: var(--pm-secondary-800);
        border: none;
    }

    .newsletter-subscribe-banner__submit {
        height: 48px;
        background: transparent;
        color: #fff;
        border: 0;
    }

    @media (min-width: 768px) {

        .newsletter-subscribe-banner {
            padding: 4rem 0;
        }

        .newsletter-subscribe-banner__background {
            min-height: 520px;
        }

        .newsletter-subscribe-banner__inner {
            padding: 3.5rem 0;
            max-width: 620px;
        }

        .newsletter-subscribe-banner__content {
            padding: 16px 0;
        }

        .newsletter-subscribe-banner__title {
            font-size: 40px;
            letter-spacing: 2px;
        }

        .newsletter-subscribe-banner__form {
            flex-direction: row;
            flex-wrap: wrap;
            max-width: 75%;
        }

        .newsletter-subscribe-banner__input {
            flex: 1 1 260px;
        }
    }

</style>