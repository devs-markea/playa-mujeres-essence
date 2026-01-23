<?php
/**
 * ACF Layout: primary_showcase_hero (dentro de Flexible Content: sections)
 *
 * Fields:
 * - logo (image array)
 * - heading (text)
 * - heading_level (select: none|h1..h6)
 * - description (textarea)
 * - primary_cta (link)
 * - key_attributes (repeater -> text)
 * - background_image_desktop (image array)
 * - background_image_mobile (image array)
 * - enable_overlay (true/false)
 * - overlay_opacity (range 0..75)
 * - location_link (link)
 * - contact_dropdown_label (text)
 * - contact_options (repeater -> type, label, value)
 */
// Si no hay slides, no hay nada que renderizar
if ( ! have_rows('slides') ) {
    return;
}

// Usamos el primer slide como hero
the_row();


$logo                  = get_sub_field('logo'); // image array
$heading               = get_sub_field('heading');
$heading_level         = get_sub_field('heading_level'); // none|h1..h6
$description           = get_sub_field('description');
$primary_cta           = get_sub_field('primary_cta'); // link array
$bg_desktop            = get_sub_field('background_image_desktop'); // image array
$bg_mobile             = get_sub_field('background_image_mobile'); // image array
$enable_overlay         = (bool) get_sub_field('enable_overlay');
$overlay_opacity        = get_sub_field('overlay_opacity'); // 0..75
$location_link         = get_sub_field('location_link'); // link array
$contact_dropdown_label = get_sub_field('contact_dropdown_label');
$contact_options        = get_sub_field('contact_options'); // repeater



$heading_tag = pm_essence_heading_tag_or_null($heading_level, 'h1');


$logo_url = is_array($logo) && !empty($logo['url']) ? $logo['url'] : '';
$logo_alt = is_array($logo) && !empty($logo['alt']) ? $logo['alt'] : '';

$bg_desktop_id = (is_array($bg_desktop) && ! empty($bg_desktop['ID'])) ? (int) $bg_desktop['ID'] : 0;
$bg_mobile_id  = (is_array($bg_mobile) && ! empty($bg_mobile['ID'])) ? (int) $bg_mobile['ID'] : 0;
$hero_alt = $heading ? $heading : get_the_title();


$cta_url    = is_array($primary_cta) && !empty($primary_cta['url']) ? $primary_cta['url'] : '';
$cta_title  = is_array($primary_cta) && !empty($primary_cta['title']) ? $primary_cta['title'] : '';
$cta_target = is_array($primary_cta) && !empty($primary_cta['target']) ? $primary_cta['target'] : '';

$overlay_alpha = 0.0;
if ($enable_overlay) {
    $overlay_opacity = is_numeric($overlay_opacity) ? (float) $overlay_opacity : 0.0; // 0..75
    if ($overlay_opacity < 0) { $overlay_opacity = 0; }
    if ($overlay_opacity > 75) { $overlay_opacity = 75; }
    $overlay_alpha = $overlay_opacity / 100.0; // 0.00 .. 0.75
}
?>


<section class="hotel-hero primary-showcase-hero">
    <div class="hotel-hero__media">
        <?php if ( $bg_desktop_id ) : ?>
            <picture>
                <?php if ( $bg_mobile_id ) : ?>
                    <source
                            media="(max-width: 991.98px)"
                            srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $bg_mobile_id, 'full' ) ); ?>"
                            sizes="100vw">
                <?php endif; ?>

                <?php
                echo wp_get_attachment_image(
                        $bg_desktop_id,
                        'full',
                        false,
                        array(
                                'class'         => 'hotel-hero__bg',
                                'alt'           => $hero_alt,
                                'loading'       => 'eager',
                                'decoding'      => 'async',
                                'fetchpriority' => 'high',
                                'sizes'         => '100vw',
                        )
                );
                ?>
            </picture>
        <?php endif; ?>


        <?php if ($enable_overlay && $overlay_alpha > 0) : ?>
            <div class="hotel-hero__overlay" style="background: rgba(0,0,0,<?php echo esc_attr($overlay_alpha); ?>);"></div>
        <?php endif; ?>
    </div>

    <div class="hotel-hero__content container">
        <div class="hotel-hero__wrapper">
            <?php if (have_rows('key_attributes')) : ?>
                <div class="hotel-hero__tags">
                    <?php
                    while (have_rows('key_attributes')) : the_row();
                        $attr_text = get_sub_field('attribute');
                        $attr_text = is_string($attr_text) ? trim($attr_text) : '';
                        if ($attr_text === '') {
                            continue;
                        }
                        echo '<span class="hotel-hero__tag">' . esc_html($attr_text) . '</span>';
                    endwhile;
                    ?>
                </div>
            <?php endif; ?>

            <div class="hotel-hero__inner">
                <div class="row g-0">
                    <div class="col-12 col-lg-7">
                        <div class="hotel-hero__content-mobile row align-items-center g-0">
                            <div class="col-6 col-md-12 flex-column-reverse gap-1 align-items-center">
                                <?php if (!empty($logo_url)) : ?>
                                    <div class="hotel-hero__logo">
                                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt ? $logo_alt : ($heading ? $heading : get_the_title())); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-6 d-flex d-md-none">
                                <?php if (is_array($location_link) && !empty($location_link['url']) && !empty($location_link['title'])) : ?>
                                    <div class="primary-showcase-hero__cta">
                                        <a class="btn btn-secondary"
                                           href="<?php echo esc_url($location_link['url']); ?>"
                                                <?php echo !empty($location_link['target']) ? 'target="' . esc_attr($location_link['target']) . '"' : ''; ?>
                                                <?php echo (isset($location_link['target']) && $location_link['target'] === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                                            <?php echo esc_html($location_link['title']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $has_contacts = have_rows('contact_options');
                                $dropdown_label = is_string($contact_dropdown_label) ? trim($contact_dropdown_label) : '';
                                ?>

                                <?php if ($has_contacts) : ?>
                                    <div class="primary-showcase-hero__contact mt-4">
                                        <?php
                                        $label_text = ($dropdown_label !== '') ? $dropdown_label : 'Contact';
                                        ?>

                                        <div class="primary-showcase-hero__dropdown" data-psh-dropdown>
                                            <button type="button"
                                                    class="primary-showcase-hero__dropdown-toggle"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                <span class="primary-showcase-hero__dropdown-label">
                                    <?php echo esc_html($label_text); ?>
                                </span>

                                                <svg class="primary-showcase-hero__dropdown-caret" width="16" height="16" viewBox="0 0 14 14" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                                    <path d="M9.75 4.125L6 7.875L2.25 4.125"
                                                          stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>

                                            <div class="primary-showcase-hero__dropdown-menu" role="menu">
                                                <?php while (have_rows('contact_options')) : the_row(); ?>
                                                    <?php
                                                    $type  = get_sub_field('type');
                                                    $label = get_sub_field('label');
                                                    $value = get_sub_field('value');

                                                    $label = is_string($label) ? trim($label) : '';
                                                    $value = is_string($value) ? trim($value) : '';

                                                    $href = pm_essence_contact_href($type, $value, $location_link);
                                                    if ($href === '' || $label === '') {
                                                        continue;
                                                    }

                                                    $target = '';
                                                    $rel    = '';
                                                    if (strpos($href, 'http') === 0) {
                                                        $target = ' target="_blank"';
                                                        $rel    = ' rel="noopener noreferrer"';
                                                    }
                                                    ?>
                                                    <a class="primary-showcase-hero__dropdown-item"
                                                       role="menuitem"
                                                       href="<?php echo esc_url($href); ?>"<?php echo $target; ?><?php echo $rel; ?>>
                                                        <?php echo esc_html($label); ?>
                                                    </a>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($heading) && !empty($heading_tag)) : ?>
                        <<?php echo esc_html($heading_tag); ?> class="hotel-hero__title">
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_html($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (have_rows('key_attributes')) : ?>
                        <div class="hotel-hero__pills">
                            <?php
                            while (have_rows('key_attributes')) : the_row();
                                $attr_text = get_sub_field('attribute');
                                $attr_text = is_string($attr_text) ? trim($attr_text) : '';
                                if ($attr_text === '') {
                                    continue;
                                }
                                echo '<span class="hotel-hero__pill">' . esc_html($attr_text) . '</span>';
                            endwhile;
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($description)) : ?>
                        <p class="hotel-hero__description">
                            <?php echo esc_html($description); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($cta_url) && !empty($cta_title)) : ?>
                        <div class="arrow-circle">
                            <a href="<?php echo esc_url($cta_url); ?>"
                               class="video-hero__button arrow-circle__link"
                                    <?php echo !empty($cta_target) ? 'target="' . esc_attr($cta_target) . '"' : ''; ?>
                                    <?php echo ($cta_target === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                                <span class="arrow-circle__label"><?php echo esc_html($cta_title); ?></span>
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
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.375" y="0.375"
                                                  width="27.25" height="27.25"
                                                  rx="13.625"
                                                  stroke="white" stroke-width="0.75"/>
                                        </svg>
                                    </span>
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>


                </div>

                <div class="col-12 col-lg-5 d-flex justify-content-lg-end align-items-start align-items-lg-end">
                    <?php if (is_array($location_link) && !empty($location_link['url']) && !empty($location_link['title'])) : ?>
                        <div class="primary-showcase-hero__cta">
                            <a class="btn btn-secondary"
                               href="<?php echo esc_url($location_link['url']); ?>"
                                    <?php echo !empty($location_link['target']) ? 'target="' . esc_attr($location_link['target']) . '"' : ''; ?>
                                    <?php echo (isset($location_link['target']) && $location_link['target'] === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                                <?php echo esc_html($location_link['title']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php
                    $has_contacts = have_rows('contact_options');
                    $dropdown_label = is_string($contact_dropdown_label) ? trim($contact_dropdown_label) : '';
                    ?>

                    <?php if ($has_contacts) : ?>
                        <div class="primary-showcase-hero__contact mt-4">
                            <?php
                            $label_text = ($dropdown_label !== '') ? $dropdown_label : 'Contact';
                            ?>

                            <div class="primary-showcase-hero__dropdown" data-psh-dropdown>
                                <button type="button"
                                        class="primary-showcase-hero__dropdown-toggle"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                <span class="primary-showcase-hero__dropdown-label">
                                    <?php echo esc_html($label_text); ?>
                                </span>

                                    <svg class="primary-showcase-hero__dropdown-caret" width="16" height="16" viewBox="0 0 14 14" fill="none"
                                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path d="M9.75 4.125L6 7.875L2.25 4.125"
                                              stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>

                                <div class="primary-showcase-hero__dropdown-menu" role="menu">
                                    <?php while (have_rows('contact_options')) : the_row(); ?>
                                        <?php
                                        $type  = get_sub_field('type');
                                        $label = get_sub_field('label');
                                        $value = get_sub_field('value');

                                        $label = is_string($label) ? trim($label) : '';
                                        $value = is_string($value) ? trim($value) : '';

                                        $href = pm_essence_contact_href($type, $value, $location_link);
                                        if ($href === '' || $label === '') {
                                            continue;
                                        }

                                        $target = '';
                                        $rel    = '';
                                        if (strpos($href, 'http') === 0) {
                                            $target = ' target="_blank"';
                                            $rel    = ' rel="noopener noreferrer"';
                                        }
                                        ?>
                                        <a class="primary-showcase-hero__dropdown-item"
                                           role="menuitem"
                                           href="<?php echo esc_url($href); ?>"<?php echo $target; ?><?php echo $rel; ?>>
                                            <?php echo esc_html($label); ?>
                                        </a>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
