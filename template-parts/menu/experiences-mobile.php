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
                    'post_type'      => 'experience',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'rand',
            ]);


            ?>

            <div class="row g-2">
                <?php foreach ($experiences as $experience) :
                    $title          = get_the_title($experience->ID);
                    $experience_url = get_permalink($experience->ID);
                    $featured_id    = get_post_thumbnail_id($experience->ID);
                    ?>
                    <div class="col-6 col-md-3">
                        <div onclick="location.href='<?= esc_url($experience_url); ?>';" class="experience-card__image-wrapper">
                            <?php if ( $featured_id ) : ?>
                                <?php echo wp_get_attachment_image( $featured_id, 'large', false, [
                                    'class'   => 'experience-card__image',
                                    'alt'     => '',
                                    'loading' => 'lazy',
                                    'decoding'=> 'async',
                                ] ); ?>
                            <?php endif; ?>
                            <div class="experience-card__title-wrapper">
                                <p class="experience-card__title"><?= esc_html($title); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>