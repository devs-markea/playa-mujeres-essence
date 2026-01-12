<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post(); ?>

        <main id="primary" class="site-main">

            <?php
            get_template_part( 'template-parts/sections/single-hotel-example');
            ?>

        </main>

    <?php endwhile;
endif;

get_footer();
