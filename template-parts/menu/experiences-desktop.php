<div class="mega-panel mega-panel__experiences">
    <div class="mega-panel__inner">
        <p class="mega-panel__headline">
            Discover a collection of world-class resorts in Playa Mujeres
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
                    $title     = get_the_title($experience->ID);
                    $featured  = get_the_post_thumbnail_url($experience->ID);
                    $hotel_url = get_permalink($experience->ID);
                    $special_positions = [12428, 12427, 12429];
                    $bg_position = in_array($experience->ID, $special_positions)
                            ? 'background-position: bottom;'
                            : '';
                    ?>
                    <div class="col-6 col-md-3">
                        <div
                                onclick="location.href='<?= esc_url($hotel_url); ?>';"
                                class="cover-cc-bg p-2 experience-card__image-wrapper"
                                style="<?= $bg_position ?> background-image:url('<?= esc_url($featured); ?>');">
                        <h5><?= esc_html($title); ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

