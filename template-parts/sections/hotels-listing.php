<?php
$title = get_sub_field('title');
$description = get_sub_field('description');
$hotels = get_posts([
        'post_type'      => 'hotel',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'rand',
]);



// Divide el array en 2 mitades
$count = is_array($hotels) ? count($hotels) : 0;
$half  = (int) ceil($count / 2);

$row1 = array_slice($hotels, 0, $half);
$row2 = array_slice($hotels, $half);
?>

<section class="hotels-parallax" id="hotels-parallax">
    <div class="hotel-extraordinary-heading container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <div class="row g-0">
                    <div class="col-8 col-md-4 mx-auto">
                        <?php
                        $title = esc_html($title);
                        $words = explode(' ', $title);
                        if (count($words) > 1) {
                            $words[1] = '<span class="underline">' . $words[1] . '</span>';
                        }
                        $formatted_title = implode(' ', $words);
                        ?>
                        <h2><?= $formatted_title; ?></h2>
                    </div>
                    <div class="col-12 col-md-7 offset-md-1">
                        <div class="hotel-extraordinary-heading__description">
                            <?php echo wp_kses_post( $description ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hotels-parallax__row row g-3 g-md-4 parallax-row parallax-row--right">
        <?php foreach ($row1 as $hotel) :

            $logo_data = function_exists('pm_get_hotel_primary_showcase_logo') ? pm_get_hotel_primary_showcase_logo($hotel->ID) : null;
            $featured  = get_the_post_thumbnail_url($hotel->ID);
            $hotel_url = get_permalink($hotel->ID);

            $logo_src = is_array($logo_data) && !empty($logo_data['url']) ? $logo_data['url'] : '';
            $logo_alt = is_array($logo_data) && !empty($logo_data['alt']) ? $logo_data['alt'] : get_the_title($hotel->ID);
            ?>
            <div class="col-6 col-md-3" data-hotel-id="<?= esc_attr($hotel->ID); ?>">
                <div onclick="location.href='<?= esc_url($hotel_url); ?>';"
                     class="cover-cc-bg p-2 hotel-extraordinary-card__image-wrapper"
                     style="background-image:url('<?= esc_url($featured); ?>');">
                    <img class="hotel-extraordinary-card__logo d-block mt-3"
                         src="<?= esc_url($logo_src); ?>"
                         alt="<?= esc_attr($logo_alt); ?>">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="hotels-parallax__row row g-3 g-md-4 parallax-row parallax-row--left">
        <?php foreach ($row2 as $hotel) :
            $logo_data = function_exists('pm_get_hotel_primary_showcase_logo') ? pm_get_hotel_primary_showcase_logo($hotel->ID) : null;
            $featured  = get_the_post_thumbnail_url($hotel->ID);
            $hotel_url = get_permalink($hotel->ID);

            $logo_src = is_array($logo_data) && !empty($logo_data['url']) ? $logo_data['url'] : '';
            $logo_alt = is_array($logo_data) && !empty($logo_data['alt']) ? $logo_data['alt'] : get_the_title($hotel->ID);
            ?>
            <div class="col-6 col-md-3" data-hotel-id="<?= esc_attr($hotel->ID); ?>">
                <div onclick="location.href='<?= esc_url($hotel_url); ?>';"
                     class="cover-cc-bg p-2 hotel-extraordinary-card__image-wrapper"
                     style="background-image:url('<?= esc_url($featured); ?>');">
                    <img class="hotel-extraordinary-card__logo d-block mt-3"
                         src="<?= esc_url($logo_src); ?>"
                         alt="<?= esc_attr($logo_alt); ?>">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


