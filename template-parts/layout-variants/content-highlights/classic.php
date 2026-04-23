<?php
/**
 * template-parts/layout-variants/content-highlights/classic.php
 *
 * El loader (content-highlights.php) provee:
 * - $heading_title, $heading_tag, $position_css
 * - $show_intro_text, $intro_text
 * - $highlights_items
 * - $show_description, $description
 * - $show_image, $image, $image_id, $image_alt
 */
?>
<section data-anim="slide-up delay-2" class="content-highlights content-highlights--classic<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">

    <div class="container px-0 px-md-4">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">

                <?php if ($heading_title) : ?>
                    <div class="content-highlights__header">
                        <<?php echo esc_html($heading_tag); ?> class="content-highlights__title content-highlights__title--<?php echo esc_attr($position_css); ?>">
                            <?php echo esc_html($heading_title); ?>
                        </<?php echo esc_html($heading_tag); ?>>
                    </div>
                <?php endif; ?>

                <?php if ($show_intro_text && $intro_text) : ?>
                    <div class="content-highlights__intro">
                        <?php echo wp_kses_post($intro_text); ?>
                    </div>
                <?php endif; ?>

                <?php if ($highlights_items) : ?>
                    <div class="content-highlights__items row g-0 g-md-3">
                        <?php foreach ($highlights_items as $item) :
                            $item_heading       = $item['heading']       ?? '';
                            $item_heading_level = $item['heading_level'] ?? 'none';
                            $item_description   = $item['description']   ?? '';
                            $item_image         = $item['image']         ?? null;

                            $item_tag = pm_essence_heading_tag_or_null($item_heading_level, 'h3');
                            if (empty($item_tag)) { $item_tag = 'h3'; }

                            $item_image_id  = (is_array($item_image) && ! empty($item_image['ID'])) ? (int) $item_image['ID'] : 0;
                            $item_image_alt = (is_array($item_image) && ! empty($item_image['alt'])) ? $item_image['alt'] : $item_heading;
                        ?>
                            <div class="col-12 col-md-4 content-highlights__item">
                                <div class="content-highlights__item-inner">

                                    <?php if ($item_image_id) : ?>
                                        <div class="content-highlights__item-icon" aria-hidden="true">
                                            <?php echo wp_get_attachment_image($item_image_id, 'large', false, [
                                                'class'    => 'content-highlights__item-icon-img',
                                                'alt'      => $item_image_alt,
                                                'loading'  => 'lazy',
                                                'decoding' => 'async',
                                            ]); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="content-highlights__item-body">
                                        <?php if ($item_heading) : ?>
                                            <<?php echo esc_html($item_tag); ?> class="content-highlights__item-heading">
                                                <?php echo esc_html($item_heading); ?>
                                            </<?php echo esc_html($item_tag); ?>>
                                        <?php endif; ?>

                                        <?php if ($item_description) : ?>
                                            <div class="content-highlights__item-description">
                                                <?php echo wp_kses_post($item_description); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_description && $description) : ?>
                    <div class="content-highlights__description">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_image) : ?>
                    <div class="content-highlights__media">
                        <?php if ($image_id) : ?>
                            <?php echo wp_get_attachment_image($image_id, 'full', false, [
                                'class'    => 'content-highlights__media-img',
                                'alt'      => $image_alt,
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]); ?>
                        <?php elseif (is_array($image) && ! empty($image['url'])) : ?>
                            <img src="<?php echo esc_url($image['url']); ?>"
                                 alt="<?php echo esc_attr($image_alt); ?>"
                                 class="content-highlights__media-img"
                                 loading="lazy"
                                 decoding="async">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</section>
