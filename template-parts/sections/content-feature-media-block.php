<?php
// template-parts/sections/content-feature-media-block.php

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$heading_title    = get_sub_field( 'heading_title' );
$heading_level    = get_sub_field( 'heading_level_title' );
$heading_position = get_sub_field( 'heading_position_title' );
$media_type       = get_sub_field( 'media_' );
$media_image      = get_sub_field( 'media_image' );
$media_shortcode        = get_sub_field( 'media_shortcode' );
$media_shortcode_mobile = get_sub_field( 'media_shortcode_mobile' );

$heading_tag = pm_essence_heading_tag_or_null( $heading_level, 'h2' );
if ( empty( $heading_tag ) ) {
    $heading_tag = 'h2';
}

$image_id  = ( is_array( $media_image ) && ! empty( $media_image['ID'] ) ) ? (int) $media_image['ID'] : 0;
$image_alt = ( is_array( $media_image ) && ! empty( $media_image['alt'] ) ) ? $media_image['alt'] : '';
?>

<section data-anim="slide-up delay-2" class="content-feature-media-block<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">

                <div class="row g-0">
                    <div class="col-12 col-md-6 mx-auto">
                        <?php if ( $heading_title ) : ?>
                        <div class="content-feature-media-block__header text-<?php echo esc_attr( $heading_position ?: 'left' ); ?>">
                            <<?php echo $heading_tag; ?> class="content-feature-media-block__title">
                            <?php echo esc_html( $heading_title ); ?>
                        </<?php echo $heading_tag; ?>>
                    </div>
                    <?php endif; ?>
                    </div>
                </div>

                <div class="content-feature-media-block__media">

                    <?php if ( $media_type === 'image' && $image_id ) : ?>
                        <?php echo wp_get_attachment_image( $image_id, 'full', false, [
                            'class'   => 'content-feature-media-block__image',
                            'alt'     => $image_alt,
                            'loading' => 'lazy',
                        ] ); ?>

                    <?php elseif ( $media_type === 'map' ) : ?>
                        <?php if ( $media_shortcode_mobile ) : ?>
                            <div class="content-feature-media-block__map d-block d-md-none">
                                <?php echo do_shortcode( '[' . sanitize_key( $media_shortcode_mobile ) . ']' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $media_shortcode ) : ?>
                            <div class="content-feature-media-block__map<?php echo $media_shortcode_mobile ? ' d-none d-md-block' : ''; ?>">
                                <?php echo do_shortcode( '[' . sanitize_key( $media_shortcode ) . ']' ); ?>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>

            </div>
        </div>
    </div>
</section>
