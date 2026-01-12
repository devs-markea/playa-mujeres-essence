<div class="mega-panel mega-panel__where-to-stay">
    <div class="mega-panel__inner">
        <p class="mega-panel__headline">
            Because paradise feels different for everyone.
        </p>

        <div class="where-to-stay__list">
            <?php
            $hotels = get_posts([
                'post_type'      => 'hotel',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'rand',
            ]);

            $hotels = (array) $hotels;
            shuffle($hotels);
            ?>

            <div class="row g-2">
                <?php foreach ($hotels as $hotel) :
                    $logo_dark = get_field('logo_dark', $hotel->ID);
                    $featured  = get_the_post_thumbnail_url($hotel->ID);
                    $hotel_url = get_permalink($hotel->ID);
                    ?>
                    <div class="col-6 col-md-3">
                        <div onclick="location.href='<?= esc_url($hotel_url); ?>';" class="cover-cc-bg p-2 hotel-card__image-wrapper" style="background-image:url('<?= esc_url($featured); ?>');">
                            <img class="hotel-card__logo d-block mx-auto mt-3 position-relative z-1" src="<?= esc_url($logo_dark['url']); ?>" alt="<?= esc_attr(get_the_title($hotel->ID)); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

