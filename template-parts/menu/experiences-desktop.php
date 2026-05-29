<div class="mega-panel mega-panel__experiences">
    <div class="container mega-panel__inner">
        <p class="mega-panel__headline">
            <?php pll_e('Discover a collection of world-class resorts in Playa Mujeres'); ?>
        </p>
        <div class="experiences__list">
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
                    $title       = get_the_title($experience->ID);
                    $experience_url = get_permalink($experience->ID);
                    $featured_id = get_post_thumbnail_id($experience->ID);
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
                                <h5 class="experience-card__title"><?= esc_html($title); ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

