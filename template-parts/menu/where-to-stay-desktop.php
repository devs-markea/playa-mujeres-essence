<div class="mega-panel mega-panel__where-to-stay">
    <div class="container mega-panel__inner">
        <p class="mega-panel__headline">
            <?php pll_e('Because paradise feels different for everyone.'); ?>
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

            <div class="row g-3">
                <?php foreach ($hotels as $hotel) :
                    $logo_data = function_exists('pm_get_hotel_primary_showcase_logo_dark') ? pm_get_hotel_primary_showcase_logo_dark($hotel->ID) : null;
                    $featured  = get_the_post_thumbnail_url($hotel->ID);
                    $hotel_url = get_permalink($hotel->ID);

                    $logo_id     = is_array($logo_data) && !empty($logo_data['ID'])  ? (int) $logo_data['ID']  : 0;
                    $logo_alt    = is_array($logo_data) && !empty($logo_data['alt']) ? $logo_data['alt'] : get_the_title($hotel->ID);
                    $featured_id = get_post_thumbnail_id($hotel->ID);
                    ?>
                    <div class="col-6 col-md-3">
                        <div onclick="location.href='<?= esc_url($hotel_url); ?>';" class="cover-cc-bg p-2 hotel-card__image-wrapper">
                            <?php if ( $featured_id ) : ?>
                                <?php echo wp_get_attachment_image( $featured_id, 'large', false, [
                                    'class'   => 'hotel-card__image',
                                    'alt'     => '',
                                    'loading' => 'lazy',
                                    'decoding'=> 'async',
                                ] ); ?>
                            <?php endif; ?>
                            <?php if ( $logo_id ) : ?>
                                <div class="hotel-card__logo-wrapper">
                                    <?php echo wp_get_attachment_image( $logo_id, 'medium', false, [
                                        'class'   => 'hotel-card__logo',
                                        'alt'     => esc_attr( $logo_alt ),
                                        'loading' => 'lazy',
                                        'decoding'=> 'async',
                                    ] ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

