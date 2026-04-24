<?php
$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

// Campos ACF principales
$main_image       = get_sub_field('main_image');
$decorative_image = get_sub_field('decorative_image');

$title        = get_sub_field('title');
$title_level  = get_sub_field('title_level');
$description  = get_sub_field('description');

$button_settings = get_sub_field('button_settings');
$show_button     = is_array($button_settings) && !empty($button_settings['show_button']);
$button_link     = (is_array($button_settings) && !empty($button_settings['button_link']) && is_array($button_settings['button_link']))
    ? $button_settings['button_link']
    : null;
$button_classes  = (is_array($button_settings) && !empty($button_settings['button_classes'])) ? $button_settings['button_classes'] : '';

$title_tag = pm_essence_heading_tag_or_null($title_level, 'h2');
if (empty($title_tag)) {
    $title_tag = 'h2';
}

// Normaliza IDs + alt
$main_image_id = (is_array($main_image) && !empty($main_image['ID'])) ? (int) $main_image['ID'] : 0;
$main_image_alt = '';
if (is_array($main_image) && !empty($main_image['alt'])) {
    $main_image_alt = $main_image['alt'];
} elseif (!empty($title)) {
    $main_image_alt = $title;
}

$decor_image_id = (is_array($decorative_image) && !empty($decorative_image['ID'])) ? (int) $decorative_image['ID'] : 0;
// decorativa normalmente es decorativa => alt vacío (o usa el alt del campo si sí quieres)
$decor_image_alt = '';
if (is_array($decorative_image) && !empty($decorative_image['alt'])) {
    $decor_image_alt = $decorative_image['alt'];
}
?>
<section data-anim="slide-up delay-2" class="content-two-image-section<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container p-0 px-md-3">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <div class="row align-items-stretch g-0 gx-md-4">
                    <div class="col-12 col-lg-6 order-2 order-lg-1">
                        <div class="content-two-image-section__content h-100">
                            <?php if ( $title ) : ?>
                                <<?php echo esc_html($title_tag); ?> class="mb-3 content-two-image-section__heading">
                                    <?php echo esc_html( $title ); ?>
                                </<?php echo esc_html($title_tag); ?>>
                            <?php endif; ?>
                            <?php if ( $description ) : ?>
                                <div class="content-two-image-section__description mb-3">
                                    <?php echo wp_kses_post( $description ); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $show_button && ! empty( $button_link['url'] ) ) : ?>
                                <?php
                                $btn_url    = esc_url( $button_link['url'] );
                                $btn_title  = esc_html( $button_link['title'] ?: 'Learn more' );
                                $btn_target = ! empty( $button_link['target'] ) ? $button_link['target'] : '_self';
                                $btn_rel    = ( '_blank' === $btn_target ) ? ' rel="noopener noreferrer"' : '';
                                ?>

                                <a href="<?php echo $btn_url; ?>"
                                   target="<?php echo esc_attr( $btn_target ); ?>"<?php echo $btn_rel; ?>
                                   class="btn btn-primary btn-border-bottom-black <?php echo esc_attr( $button_classes ); ?>">
                                    <?php echo $btn_title; ?>
                                </a>
                            <?php endif; ?>
                            <div class="content-two-image-section__decorative-image">
                                <?php
                                $decor_image_id = (is_array($decorative_image) && !empty($decorative_image['ID'])) ? (int) $decorative_image['ID'] : 0;

                                $decor_image_alt = '';
                                if (is_array($decorative_image) && !empty($decorative_image['alt'])) {
                                    $decor_image_alt = $decorative_image['alt'];
                                }
                                ?>

                                <?php if ($decor_image_id) : ?>
                                    <?php
                                    echo wp_get_attachment_image(
                                            $decor_image_id,
                                            'large',
                                            false,
                                            array(
                                                    'class'    => 'img-fluid',
                                                    'alt'      => $decor_image_alt,
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                            )
                                    );
                                    ?>
                                <?php elseif (is_array($decorative_image) && !empty($decorative_image['url'])) : ?>
                                    <img src="<?php echo esc_url($decorative_image['url']); ?>"
                                         alt="<?php echo esc_attr($decor_image_alt); ?>"
                                         class="img-fluid"
                                         loading="lazy"
                                         decoding="async">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 order-1 order-lg-2">
                        <div class="content-two-image-section__image">
                            <?php if ($main_image_id) : ?>
                                <?php
                                echo wp_get_attachment_image(
                                    $main_image_id,
                                    'large',
                                    false,
                                    array(
                                        'class'    => 'img-fluid',
                                        'alt'      => $main_image_alt,
                                        'loading'  => 'lazy',
                                        'decoding' => 'async'
                                    )
                                );
                                ?>
                            <?php elseif (is_array($main_image) && !empty($main_image['url'])) : ?>
                                <img src="<?php echo esc_url($main_image['url']); ?>"
                                     alt="<?php echo esc_attr($main_image_alt); ?>"
                                     class="img-fluid"
                                     loading="lazy"
                                     decoding="async">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


