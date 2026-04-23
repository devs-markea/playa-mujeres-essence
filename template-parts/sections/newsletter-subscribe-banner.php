<?php
$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

$title              = get_sub_field('title');
$description        = get_sub_field('description');
$subscribe_text     = get_sub_field('subscribe_button_text') ?: 'Subscribe';
$bg                 = get_sub_field('background_image');
$layout_width       = get_sub_field('layout_width'); // 'full' | 'narrow'
$form_position      = get_sub_field('form_position');


// controla la columna
$col_class = ($layout_width === 'full_width')
    ? 'col-12'
    : 'col-12 col-md-10';

$bg_url = !empty($bg['sizes']['full'])
    ? $bg['sizes']['large']
    : ($bg['url'] ?? '');

$justify_class = 'justify-content-center';
if (is_string($form_position)) {
    $pos = strtolower(trim($form_position));
    if ($pos === 'left') {
        $justify_class = 'justify-content-start';
    } elseif ($pos === 'right') {
        $justify_class = 'justify-content-end';
    } elseif ($pos === 'center') {
        $justify_class = 'justify-content-center';
    }
}

?>

<section class="newsletter-subscribe-banner<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="row g-0">

        <div class="<?php echo esc_attr($col_class); ?> mx-auto">
            <div class="newsletter-subscribe-banner__background"
                 style="background-image:url('<?php echo esc_url($bg_url); ?>')">

                <div class="newsletter-subscribe-banner__overlay"></div>

                <div class="newsletter-subscribe-banner__content">
                    <div class="container">
                        <div class="row g-0 <?php echo esc_attr($justify_class); ?>">
                            <div class="col-12 col-md-5">
                                <div class="newsletter-subscribe-banner__inner">

                                    <?php if ($title): ?>
                                        <h2 class="newsletter-subscribe-banner__title">
                                            <?php echo esc_html($title); ?>
                                        </h2>
                                    <?php endif; ?>

                                    <?php if ($description): ?>
                                        <p class="newsletter-subscribe-banner__description">
                                            <?php echo esc_html($description); ?>
                                        </p>
                                    <?php endif; ?>

                                    <form class="newsletter-subscribe-banner__form">
                                        <input
                                                type="email"
                                                class="newsletter-subscribe-banner__input"
                                                placeholder="Type your email address"
                                                required
                                        >
                                        <button type="submit"
                                                class="newsletter-subscribe-banner__submit arrow-circle__link">
                                            <span class="arrow-circle__label">
                                                        <?php echo esc_html($subscribe_text); ?>
                                                    </span>
                                            <span class="arrow-circle__icon">
                                                        <span class="arrow">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M17.25 15.75L21 12M21 12L17.25 8.25M21 12H3"
                                                                      stroke="white" stroke-width="0.75"
                                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </span>
                                                            <span class="circle">
                                                            <svg width="28" height="30" viewBox="0 0 28 28" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.375" y="0.375"
                                                                      width="27.25" height="27.25"
                                                                      rx="13.625"
                                                                      stroke="white" stroke-width="0.75"/>
                                                            </svg>
                                                        </span>
                                                        </span>
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</section>
