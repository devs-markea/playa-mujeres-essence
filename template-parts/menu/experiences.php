<div class="pm-menu-experiences">
    <div class="back-arrow-menu-experiences">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.75 19.5L8.25 12L15.75 4.5" stroke="#323232" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div class="pm-menu-experiences__content">
        <h4>Experiences</h4>
        <p>Because paradise feels different for everyone.</p>
        <div class="pm-menu-experiences__list">
            <?php
            $experiences = get_posts([
                'post_type'      => 'experiences',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'rand',
            ]);

            $experiences = (array) $experiences;
            shuffle($experiences);
            ?>

            <div class="row g-2">
                <?php foreach ($experiences as $experience) :
                    $logo_dark = get_field('logo_dark', $experience->ID);
                    $featured  = get_the_post_thumbnail_url($experience->ID);
                    $experience_url = get_permalink($experience->ID);
                    ?>
                    <div class="col-6 col-md-3">
                        <div onclick="location.href='<?= esc_url($experience_url); ?>';" class="cover-cc-bg p-2 hotel-card__image-wrapper" style="background-image:url('<?= esc_url($featured); ?>');">
                            <img class="hotel-card__logo d-block mx-auto mt-3" src="<?= esc_url($logo_dark['url']); ?>" alt="<?= esc_attr(get_the_title($experience->ID)); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .pm-menu-experiences {
        position: absolute;
        inset: 0;
        background: #fff;
        padding: 1rem;
        z-index: 10;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        transform: translateX(100%);
        transition: transform 0.35s ease;
    }

    .pm-menu-experiences__content {
        display: flex;
        flex-direction: column;
        text-align: center;
    }

    .pm-menu-experiences__content h4 {
        color: var(--pm-secondary-900);
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .pm-menu-experiences__content p {
        color: var(--pm-secondary-900);
        font-size: 16px;
        font-style: normal;
        font-weight: 300;
        line-height: normal;
        letter-spacing: 1px;
    }

    .back-arrow {
        cursor: pointer;
        width: fit-content;
        padding: 0.5rem 0;
    }

    .pm-menu-experiences.is-open {
        transform: translateX(0);
    }

    .hotel-card__image-wrapper {
        cursor:pointer;
        height: 200px;
        border-radius: 0.5rem;
        border: 1px solid #E5E5E5;
        overflow: hidden;
        background-size: cover;
        position: relative;
    }

    .hotel-card__image-wrapper::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(
                to top,
                rgba(255, 255, 255, 0) 50%,
                rgba(255, 255, 255, 1) 95%
        );
        pointer-events: none;
        z-index: 1;
    }

    .hotel-card__logo {
        width: 100%;
        max-width: 4.5rem;
        height: auto;

    }


</style>