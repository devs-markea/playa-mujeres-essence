<?php
// Campos ACF principales
$title       = get_sub_field( 'title' );
$description = get_sub_field( 'description' );
$poster      = get_sub_field( 'poster' );

// Repeater de highlights
$highlights = get_sub_field( 'highlights' );
?>

<section class="about-highlights">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 mx-auto">
                <?php if ( $title ) : ?>
                    <h2 class="about-highlights__title mb-3">
                        <?php echo esc_html( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $description ) : ?>
                    <div class="about-highlights__description mb-4">
                        <?php echo wp_kses_post( $description ); ?>
                    </div>
                <?php endif; ?>

                <div class="about-highlight__list row justify-content-center">
                    <?php if ( ! empty( $highlights ) && is_array( $highlights ) ) : ?>
                    <?php foreach ( $highlights as $item ) :

                    $icon  = $item['icon'] ?? null; // obligatorio
                    $label = $item['label'] ?? '';

                    if ( empty( $icon ) ) {
                        continue; // sin icono, no se muestra
                    }

                    // LINK: puede ser string o campo ACF tipo "link" (array)
                    $link_field  = $item['link'] ?? '';
                    $link_url    = '';
                    $link_target = $item['link_target'] ?? '';

                    if ( is_array( $link_field ) ) {
                        // Campo ACF Link
                        $link_url = $link_field['url'] ?? '';

                        // Si no viene target manual, usamos el del field
                        if ( empty( $link_target ) && ! empty( $link_field['target'] ) ) {
                            $link_target = $link_field['target'];
                        }
                    } else {
                        // Campo tipo URL (string)
                        $link_url = $link_field;
                    }

                    $has_link = ! empty( $link_url );

                    // Tag dinámico
                    $tag = $has_link ? 'a' : 'div';

                    // Rel para target _blank
                    $rel_attr = ( $has_link && '_blank' === $link_target ) ? ' rel="noopener noreferrer"' : '';
                    ?>

                    <<?php echo $tag; ?> class="about-highlight__item col-6 col-lg-2 p-3 <?php echo $has_link ? ' about-highlight--link' : ''; ?>"
                    <?php if ( $has_link ) : ?>
                        href="<?php echo esc_url( $link_url ); ?>"
                        target="<?php echo esc_attr( $link_target ); ?>"<?php echo $rel_attr; ?>
                    <?php endif; ?>>
                    <div class="about-highlight__icon mb-2">
                        <img
                                src="<?php echo esc_url( $icon['url'] ); ?>"
                                alt="<?php echo esc_attr( ! empty( $icon['alt'] ) ? $icon['alt'] : $label ); ?>"
                                class="img-fluid">
                    </div>

                    <?php if ( ! empty( $label ) ) : ?>
                        <div class="about-highlight__label">
                            <?php echo esc_html( $label ); ?>
                        </div>
                    <?php endif; ?>
                </<?php echo $tag; ?>>


            <?php endforeach; ?>
            <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
