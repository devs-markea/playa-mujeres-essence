<?php
/**
 * Template Name: Página con Sections ACF
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>

        <main id="primary" class="site-main">

            <?php
            // Recorremos las secciones definidas en el Flexible Content "sections".
            if ( have_rows( 'sections' ) ) :

                while ( have_rows( 'sections' ) ) :
                    the_row();

                    $layout = get_row_layout(); // Ej: 'hero', 'content_image_section', etc.

                    $template = str_replace( '_', '-', $layout );

                    // Cargar un template parcial según el layout.
                    get_template_part( 'template-parts/sections/' . $template );

                endwhile;

            endif;

            ?>

        </main>

    <?php
    endwhile;
endif;

get_footer();