<?php
// template-parts/sections/content-accordion.php

$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$heading_title       = get_sub_field('heading_title');
$heading_level_title = get_sub_field('heading_level_title');
$accordion_items     = get_sub_field('accordion_items');

// heading_level_title default es "none" → fallback h2
$heading_tag = pm_essence_heading_tag_or_null($heading_level_title, 'h2');
if (empty($heading_tag)) {
    $heading_tag = 'h2';
}

// ID único para el accordion (evita colisiones si hay varios en la misma página)
$accordion_id = 'content-accordion-' . uniqid();
?>

<section data-anim="slide-up delay-2" class="content-accordion<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container">

        <div class="row g-0">
            <div class="col-12 col-md-6 mx-auto">
                <?php if ($heading_title) : ?>
                <div class="content-accordion__header">
                    <<?php echo esc_html($heading_tag); ?> class="content-accordion__title">
                    <?php echo esc_html($heading_title); ?>
                </<?php echo esc_html($heading_tag); ?>>
            </div>
            <?php endif; ?>
            </div>
        </div>

        <?php if ($accordion_items) : ?>
            <div class="content-accordion__list" id="<?php echo esc_attr($accordion_id); ?>">
                <?php foreach ($accordion_items as $index => $item) :
                    $item_heading       = $item['heading']       ?? '';
                    $item_heading_level = $item['heading_level'] ?? 'none';
                    $item_description   = $item['description']   ?? '';

                    // heading_level item default es "none" → fallback h3
                    $item_tag = pm_essence_heading_tag_or_null($item_heading_level, 'h3');
                    if (empty($item_tag)) {
                        $item_tag = 'h3';
                    }

                    $collapse_id = $accordion_id . '-collapse-' . $index;
                    $is_first    = $index === 0;
                ?>
                    <div class="content-accordion__item">

                        <button
                            class="content-accordion__trigger <?php echo $is_first ? '' : 'collapsed'; ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo esc_attr($collapse_id); ?>"
                            aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
                            aria-controls="<?php echo esc_attr($collapse_id); ?>">

                            <<?php echo esc_html($item_tag); ?> class="content-accordion__item-heading">
                                <?php echo esc_html($item_heading); ?>
                            </<?php echo esc_html($item_tag); ?>>

                            <span class="content-accordion__chevron" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6L8 11L13 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </button>

                        <div
                            class="collapse <?php echo $is_first ? 'show' : ''; ?>"
                            id="<?php echo esc_attr($collapse_id); ?>"
                            data-bs-parent="#<?php echo esc_attr($accordion_id); ?>">
                            <div class="content-accordion__body">
                                <?php echo wp_kses_post($item_description); ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
