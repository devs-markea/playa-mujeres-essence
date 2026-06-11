<div class="pm-menu-experiences">
    <div class="back-arrow-menu-experiences">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.75 19.5L8.25 12L15.75 4.5" stroke="#323232" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div class="pm-menu-experiences__content">
        <p class="pm-menu-panel__title"><?php pll_e('Experiences'); ?></p>
        <p><?php pll_e('Because paradise feels different for everyone.'); ?></p>
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
                        <a href="<?= esc_url($experience_url); ?>" class="cover-cc-bg p-2 hotel-card__image-wrapper" style="background-image:url('<?= esc_url($featured); ?>');">
                            <img class="hotel-card__logo d-block mx-auto mt-3" src="<?= esc_url($logo_dark['url']); ?>" alt="<?= esc_attr(get_the_title($experience->ID)); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
