<?php
$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$eyebrow       = get_sub_field( 'eyebrow' );
$section_title = get_sub_field( 'section_title' );
$section_desc  = get_sub_field( 'section_description' );
$title_level   = get_sub_field( 'title_level' );
$title_tag     = pm_essence_heading_tag_or_null( $title_level, 'h2' );

$hotel_gallery = pm_essence_get_hotels_gallery_data();

$trans_data  = pm_essence_get_translation_json( 'gallery' );
$translation = pm_essence_get_translation_level( $trans_data, [ 'gallery' ] );
$t_all       = pm_essence_translation_text( $translation, 'all' );
$t_not_title = pm_essence_translation_text( $translation, 'notFoundTitle' );
$t_not_again = pm_essence_translation_text( $translation, 'notFoundTryAgain' );
$t_not_reset = pm_essence_translation_text( $translation, 'notFoundReset' );

$available_hotels = array_filter( $hotel_gallery['hotels'], fn( $h ) => $h['isAvailable'] );
?>

<section data-anim="slide-up delay-2" class="hg-section<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>" data-force-header-theme="menu">
    <div class="container">

        <?php if ( $eyebrow || $section_title || $section_desc ) : ?>
        <div class="hg-header">

            <?php if ( $eyebrow ) : ?>
            <div class="hg-eyebrow">
                <span class="hg-eyebrow__line"></span>
                <span class="hg-eyebrow__text"><?php echo esc_html( $eyebrow ); ?></span>
                <span class="hg-eyebrow__line"></span>
            </div>
            <?php endif; ?>

            <?php if ( $section_title ) : ?>
            <<?php echo esc_html( $title_tag ); ?> class="hg-title">
                <?php echo esc_html( $section_title ); ?>
            </<?php echo esc_html( $title_tag ); ?>>
            <?php endif; ?>

            <?php if ( $section_desc ) : ?>
            <div class="hg-description">
                <?php echo wp_kses_post( $section_desc ); ?>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <div class="hg-controls">

            <?php if ( count( $available_hotels ) > 1 ) : ?>
            <div class="hg-hotel-filter">
                <div class="hg-select-wrap">
                    <select class="hg-select-resort" aria-label="<?php esc_attr_e( 'Filter by hotel', 'mqp' ); ?>">
                        <option value="all"><?php esc_html_e( 'All Hotels', 'mqp' ); ?></option>
                        <?php foreach ( $available_hotels as $hotel ) : ?>
                        <option value="<?php echo esc_attr( $hotel['filterId'] ); ?>">
                            <?php echo esc_html( $hotel['title'] ); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="hg-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $hotel_gallery['categories'] ) ) : ?>
            <div class="hg-categories" role="group" aria-label="<?php esc_attr_e( 'Filter by category', 'mqp' ); ?>">
                <button class="hg-cat-btn active" data-category="all" type="button">
                    <?php echo esc_html( $t_all ); ?>
                    <svg class="hg-cat-close" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5.28033 4.21967C4.98744 3.92678 4.51256 3.92678 4.21967 4.21967C3.92678 4.51256 3.92678 4.98744 4.21967 5.28033L6.93934 8L4.21967 10.7197C3.92678 11.0126 3.92678 11.4874 4.21967 11.7803C4.51256 12.0732 4.98744 12.0732 5.28033 11.7803L8 9.06066L10.7197 11.7803C11.0126 12.0732 11.4874 12.0732 11.7803 11.7803C12.0732 11.4874 12.0732 11.0126 11.7803 10.7197L9.06066 8L11.7803 5.28033C12.0732 4.98744 12.0732 4.51256 11.7803 4.21967C11.4874 3.92678 11.0126 3.92678 10.7197 4.21967L8 6.93934L5.28033 4.21967Z" fill="white"/></svg>
                </button>
                <?php foreach ( $hotel_gallery['categories'] as $category ) : ?>
                <button class="hg-cat-btn" data-category="<?php echo esc_attr( pm_essence_gallery_esc_filter_class( $category ) ); ?>" type="button">
                    <?php echo esc_html( $category ); ?>
                    <svg class="hg-cat-close" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5.28033 4.21967C4.98744 3.92678 4.51256 3.92678 4.21967 4.21967C3.92678 4.51256 3.92678 4.98744 4.21967 5.28033L6.93934 8L4.21967 10.7197C3.92678 11.0126 3.92678 11.4874 4.21967 11.7803C4.51256 12.0732 4.98744 12.0732 5.28033 11.7803L8 9.06066L10.7197 11.7803C11.0126 12.0732 11.4874 12.0732 11.7803 11.7803C12.0732 11.4874 12.0732 11.0126 11.7803 10.7197L9.06066 8L11.7803 5.28033C12.0732 4.98744 12.0732 4.51256 11.7803 4.21967C11.4874 3.92678 11.0126 3.92678 10.7197 4.21967L8 6.93934L5.28033 4.21967Z" fill="white"/></svg>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>

        <div class="row g-3 hg-grid">
            <?php foreach ( $hotel_gallery['gallery'] as $item ) :
                $cat_classes  = pm_essence_gallery_implode_filter( $item['categories'], ' ' );
                $resort_class = implode( ' ', $item['resort']['classes'] );
                $filter_data  = pm_essence_gallery_esc_filter_class( $item['resort']['title'], false )
                    . ',' . pm_essence_gallery_implode_filter( $item['categories'], ',', false )
                    . ',' . pm_essence_gallery_implode_filter( $item['tags'], ',', false );
                $title_attr   = $item['resort']['title']
                    . ( ! empty( $item['categories'] ) ? ' – ' . implode( ', ', $item['categories'] ) : '' );
            ?>
            <div class="col-12 col-sm-6 col-lg-4 hg-grid-item <?php echo esc_attr( trim( $cat_classes . ' ' . $resort_class ) ); ?>"
                 data-filter="<?php echo esc_attr( $filter_data ); ?>">
                <div class="hg-grid-item__inner">
                    <a href="<?php echo esc_url( $item['image'] ); ?>"
                       class="custom-gallery-image"
                       data-lightbox="resorts-gallery"
                       data-title="<?php echo esc_attr( $title_attr ); ?>">
                        <?php echo wp_get_attachment_image( $item['imageId'], 'large', false, [
                            'class'   => 'hg-img',
                            'loading' => 'lazy',
                            'alt'     => esc_attr( $title_attr ),
                        ] ); ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="hg-load-more-wrap">
            <button class="hg-load-more" type="button" style="display:none;">
                <?php esc_html_e( 'Load more', 'mqp' ); ?>
            </button>
        </div>

        <div class="gallery-not-found d-none p-4">
            <p class="mb-2 title"><?php echo esc_html( $t_not_title ); ?></p>
            <p class="mb-2 fw-normal"><?php echo esc_html( $t_not_again ); ?></p>
            <p class="m-0 as-link fw-semibold reset-filters"><?php echo esc_html( $t_not_reset ); ?></p>
        </div>

    </div>
</section>
