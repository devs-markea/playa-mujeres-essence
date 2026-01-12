<?php
$title        = get_sub_field('title');
$description  = get_sub_field('description');
$image        = get_sub_field('image'); // ACF image array
$layout_width = get_sub_field('layout_width'); // 'container' | 'container-fluid' | 'narrow' (ejemplo)
$button_link  = get_sub_field('button_link'); // ACF link array

// fallback de ancho

?>

<section class="things-events">
    <div class="<?php echo esc_attr($layout_width === 'contained' ? 'container' : ''); ?>">

    </div>
    <div class="row g-0 justify-content-center">
        <div class="col-12 <?php echo esc_attr($layout_width === 'contained' ? 'col-md-4' : ''); ?> things-events__header">

            <?php if ($title): ?>
                <h2 class="things-events__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($description): ?>
                <p class="things-events__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if (!empty($button_link)): ?>
                <a class="things-events__button"
                   href="<?php echo esc_url($button_link['url']); ?>"
                   target="<?php echo esc_attr($button_link['target'] ?? '_self'); ?>">
                    <?php echo esc_html($button_link['title']); ?>
                </a>
            <?php endif; ?>

        </div>
    </div>

    <?php if (!empty($image)): ?>
        <div class="row g-0 justify-content-center">
            <div class="col-12 <?php echo esc_attr($layout_width === 'contained' ? 'col-md-10' : ''); ?>">
                <div class="things-events__image-wrapper">
                    <img
                        class="things-events__image"
                        src="<?php echo esc_url($image['sizes']['large']); ?>"
                        alt="<?php echo esc_attr($image['alt'] ?: $title ?: ''); ?>"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
<style>
    /* MOBILE FIRST */
    .things-events{
        padding: 3.5rem 0; /* más compacto en mobile */
    }

    .things-events__header{
        padding: 0 1.25rem; /* respiración lateral en mobile */
    }

    .things-events__header .things-events__title {
        text-align: center;
    }

    .things-events__header .things-events__description {
        text-align: start;
    }

    .things-events__title{
        font-size: 20px;
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        letter-spacing: 2px;
        margin: 0 0 .75rem;
    }

    .things-events__description{
        font-size: 16px;
        font-weight: 300;
        margin: 0 auto 1.5rem;
    }

    .things-events__button{
        display: inline-block;
        margin-top: .25rem;
        text-decoration: none;
        border-bottom: 1px solid currentColor;
        padding-bottom: 4px;
    }

    /* Imagen */
    .things-events__image-wrapper{
        position: relative;
        width: 100%;
        overflow: hidden;
        margin-top: 2rem;
        /* fallback para navegadores sin aspect-ratio */
        padding-top: 56.25%; /* 16:9 */
    }

    .things-events__image{
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Si soporta aspect-ratio, usamos ratio real y quitamos padding fallback */
    @supports (aspect-ratio: 16 / 9){
        .things-events__image-wrapper{
            padding-top: 0;
            aspect-ratio: 16 / 9;
        }
    }

    /* DESKTOP */
    @media (min-width: 768px){
        .things-events{
            padding: 5rem 0;
        }
        .things-events__title{
            font-size: 32px;

        }

        .things-events__header .things-events__description {
            text-align: center;
        }

        .things-events__header{
            padding: 0; /* ya lo centra la grid */
        }

        /* Ratio cinematic */
        @supports (aspect-ratio: 16 / 6){
            .things-events__image-wrapper{
                aspect-ratio: 16 / 6;
            }
        }

        /* Fallback del ratio 16/6 si no hay aspect-ratio */
        @supports not (aspect-ratio: 16 / 6){
            .things-events__image-wrapper{
                padding-top: 37.5%; /* 6/16 = 0.375 */
            }
        }
    }

</style>
