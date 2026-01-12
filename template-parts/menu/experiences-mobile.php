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
                    'post_type'      => 'experience',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'rand',
            ]);


            ?>

            <div class="row g-2">
                <?php foreach ($experiences as $experience) :
                    $title     = get_the_title($experience->ID);
                    $featured  = get_the_post_thumbnail_url($experience->ID);
                    $hotel_url = get_permalink($experience->ID);
                    ?>
                    <div class="col-6 col-md-3">
                        <div onclick="location.href='<?= esc_url($hotel_url); ?>';" class="cover-cc-bg p-2 experience-card__image-wrapper" style="background-image:url('<?= esc_url($featured); ?>');">
                            <h5><?= esc_html($title); ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>

    .experience-card__image-wrapper::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        background: linear-gradient( to top, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.0) 100% );
        pointer-events: none;
    }
    .experience-card__image-wrapper {
        position: relative;
        cursor: pointer;
        height: 162px;
        border-radius: 0.5rem;
        overflow: hidden;
        background-size: cover;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
    }
    .experience-card__image-wrapper h5 {
        position: relative;
        color: white;
        z-index: 2;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        letter-spacing: 1px;
    }
</style>