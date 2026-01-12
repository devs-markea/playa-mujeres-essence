<?php
$image_media       = get_sub_field( 'media' );
$title = get_sub_field('title');
$description = get_sub_field('description');
$button_settings = get_sub_field('button_settings');
$button_title  = '';
$button_url    = '';
$button_target = '_self';
$layout_direction = get_sub_field('desktop_layout_direction') ?: 'right';

if ( $button_settings ) {

    $show_button = ! empty( $button_settings['show_button'] );

    if ( $show_button && ! empty( $button_settings['button_link'] ) ) {
        $button_link   = $button_settings['button_link'];
        $button_title  = $button_link['title']  ?? '';
        $button_url    = $button_link['url']    ?? '';
        $button_target = $button_link['target'] ?? '_self';
    }
}

?>
<section class="two-column-layout g-0 <?php echo $layout_direction == 'left' ? 'is-left' : 'is-right' ?>">

    <div class="col-12 col-lg-6 col-content">
        <div class="row g-0">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="two-column-layout__content">
                    <?php if ( $title ) : ?>
                        <h2 class="mb-3"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $description ) : ?>
                        <div class="two-column-layout__content-description mb-3">
                            <?php echo wp_kses_post( $description ); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( $show_button && $button_url && $button_title ) : ?>
                        <a
                            href="<?php echo esc_url( $button_url ); ?>"
                            target="<?php echo esc_attr( $button_target ); ?>"
                            class="btn btn-primary btn-border-bottom-black"
                        >

                            <?php echo esc_html( $button_title ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 col-image">
        <div class="two-column-layout__content-image">
            <?php if ( $image_media ) : ?>
                <img src="<?php echo esc_url( $image_media['url'] ); ?>"
                     alt="<?php echo esc_attr( $image_media['alt'] ); ?>"
                     class="img-fluid">
            <?php endif; ?>
        </div>
    </div>

</section>
<style>
    /* Mobile first */
    .two-column-layout {
        display: flex;
        flex-direction: column-reverse;   /* Apilado en mobile */
        align-items: center;
    }

    .two-column-layout__content {
        text-align: center;
        margin: 0 32px 42px;
        padding: 24px;
        box-shadow: 0px 0px 20px -3px rgba(0, 0, 0, 0.25);
        position: relative;
        top: -2rem;
        background: white;
        z-index: 1;
    }

    .two-column-layout__content h2 {
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-size: 20px;
        letter-spacing: 2px;
    }

    .two-column-layout__content-description {
        text-align: start;
        font-size: 1rem;
        font-weight: 300;
        color: var(--pm-secondary-900);
    }

    .two-column-layout__content-image {
        width: 100%;
        height: 332px;
    }

    .two-column-layout__content-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Desktop */
    @media (min-width: 992px) {
        .two-column-layout {
            padding-bottom: 96px;
        }
        .two-column-layout__content {
            margin: 0;
            padding: 0;
            box-shadow: none;
            position: static;
            top: 0;
        }
        .two-column-layout__content h2 {
            font-size: 32px;
        }
        .two-column-layout__content-description {
            text-align: center;
        }
        .two-column-layout {
            flex-direction: row;
            align-items: center;
            gap: 0;
        }
        .two-column-layout__content-image {
            width: 100%;
            height: 580px;
        }


        .two-column-layout .col-image:not(:first-child):not(:last-child),
        .two-column-layout__content-image {
            position: static;
            top: 0;
        }

        .two-column-layout.is-left {
            flex-direction: row-reverse; /* CTA a la izquierda */
        }

        .two-column-layout.is-right {
            flex-direction: row; /* CTA a la derecha */
        }
    }

    @media (min-width: 556px) {
        .two-column-layout__content {
            min-height: 226px;
        }
    }


</style>
