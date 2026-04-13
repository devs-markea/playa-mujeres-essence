<?php
$title        = get_sub_field('title');
$description  = get_sub_field('description');
$image        = get_sub_field('image'); // ACF image array
$layout_width = get_sub_field('layout_width');
$button_link  = get_sub_field('button_link'); // ACF link array
$layout_height = get_sub_field('layout_height'); // tall | standard | compact

$layout_height = is_string($layout_height) ? trim($layout_height) : '';
if (!in_array($layout_height, array('tall', 'standard', 'compact'), true)) {
    $layout_height = 'standard';
}

$height_modifier_class = 'things-events--' . $layout_height;

$image_id  = (is_array($image) && !empty($image['ID'])) ? (int) $image['ID'] : 0;
$image_alt = (is_array($image) && !empty($image['alt'])) ? $image['alt'] : ($title ?: '');

?>

<section data-anim="slide-up delay-2" class="things-events <?php echo esc_attr($height_modifier_class); ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-8 mx-auto">
                <div class="things-events__content">
                    <?php if ($title): ?>
                        <h2 class="things-events__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <p class="things-events__description"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($button_link)): ?>
                        <a class="things-events__button btn btn-primary btn-border-bottom-black"
                           href="<?php echo esc_url($button_link['url']); ?>"
                           target="<?php echo esc_attr($button_link['target'] ?? '_self'); ?>">
                            <?php echo esc_html($button_link['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($image_id): ?>
        <div class="things-events__media">
            <div class="row g-0 justify-content-center">
                <div class="col-12 <?php echo esc_attr($layout_width === 'contained' ? 'col-md-10' : ''); ?>">
                    <div class="things-events__image-wrapper">
                        <?php
                        echo wp_get_attachment_image(
                            $image_id,
                            'large',
                            false,
                            array(
                                'class'    => 'things-events__image',
                                'alt'      => $image_alt,
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                                'sizes'    => '(min-width: 768px) 83vw, 100vw',
                            )
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
