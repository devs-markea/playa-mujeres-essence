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
// ... existing code ...
?>

<section class="things-events <?php echo esc_attr($height_modifier_class); ?>">
    <div class="things-events__header">
        <div class="row g-0 justify-content-center">
            <div class="col-12 col-md-8 d-flex flex-column align-items-center text-center">
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

<style>
    /* MOBILE FIRST (base) */
    .things-events{
        padding: 3.5rem 0;
    }

    .things-events__header{
        padding: 0 1.25rem;
    }

    .things-events__title{
        font-size: 20px;
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        letter-spacing: 2px;
        margin: 0 0 .75rem;
        text-align: center;
    }

    .things-events__description{
        font-size: 16px;
        font-weight: 300;
        margin: 0 auto 1.5rem;
        text-align: start;
    }

    .things-events__button{
        display: inline-block;
        margin-top: .25rem;
        text-decoration: none;
        border-bottom: 1px solid currentColor;
        padding-bottom: 4px;
    }

    .things-events__media{
        margin-top: 3rem;
    }

    /* Imagen */
    .things-events__image-wrapper{
        position: relative;
        width: 100%;
        overflow: hidden;
        padding-top: 56.25%; /* default 16:9 fallback */
    }

    .things-events__image{
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    @supports (aspect-ratio: 16 / 9){
        .things-events__image-wrapper{
            padding-top: 0;
            aspect-ratio: 16 / 9;
        }
    }

    /* BEM modifiers: height */
    .things-events--compact{ padding: 2.5rem 0; }
    .things-events--standard{ padding: 3.5rem 0; }
    .things-events--tall{ padding: 4.5rem 0; }

    /* BEM modifiers: height (ratios) */
    .things-events--compact .things-events__image-wrapper{ padding-top: 56.25%; }  /* 16:9 */
    .things-events--standard .things-events__image-wrapper{ padding-top: 75%; }   /* 4:3 (casi) */
    .things-events--tall .things-events__image-wrapper{ padding-top: 80%; }       /* 5:4 (más alto que 4:3) */

    @supports (aspect-ratio: 4 / 3){
        .things-events--compact .things-events__image-wrapper{ padding-top: 0; aspect-ratio: 16 / 9; }
        .things-events--standard .things-events__image-wrapper{ padding-top: 0; aspect-ratio: 4 / 3; }
        .things-events--tall .things-events__image-wrapper{ padding-top: 0; aspect-ratio: 5 / 4; }
    }

    /* Asegura que wp_get_attachment_image (img) respete el wrapper */
    .things-events__image{
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* DESKTOP */
    @media (min-width: 768px){
        .things-events__header{
            padding: 0;
        }

        .things-events__title{
            font-size: 32px;
        }

        .things-events__description{
            text-align: center;
        }

        .things-events--compact{ padding: 3.5rem 0; }
        .things-events--standard{ padding: 5rem 0; }
        .things-events--tall{ padding: 6.5rem 0; }

        /* ratios desktop por modifier (solo si hay aspect-ratio) */
        @supports (aspect-ratio: 16 / 6){
            .things-events--compact .things-events__image-wrapper{ aspect-ratio: 16 / 8; }
            .things-events--standard .things-events__image-wrapper{ aspect-ratio: 16 / 9; }
            .things-events--tall .things-events__image-wrapper{ aspect-ratio: 16 / 10; }
        }

        @supports not (aspect-ratio: 16 / 6){
            .things-events--compact .things-events__image-wrapper{ padding-top: 50%; } /* 7/16 */
            .things-events--standard .things-events__image-wrapper{ padding-top: 56.25%; } /* 6/16 */
            .things-events--tall .things-events__image-wrapper{ padding-top: 62.5%; } /* 5/16 */
        }
    }
</style>
