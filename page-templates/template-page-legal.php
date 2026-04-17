<?php
/**
 * Template Name: Página Legal
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
?>

<main id="primary" class="site-main site-main--legal" data-force-header-theme="menu">

    <!-- Hero -->
    <header class="legal-hero">
        <div class="container">
            <div class="row g-0">
                <div class="col-12 col-md-8 mx-auto text-center">

                    <p class="legal-hero__eyebrow">
                        <span class="legal-hero__eyebrow-line" aria-hidden="true"></span>
                        <?php esc_html_e( 'Legals', 'pm-essence' ); ?>
                        <span class="legal-hero__eyebrow-line" aria-hidden="true"></span>
                    </p>

                    <h1 class="legal-hero__title"><?php the_title(); ?></h1>

                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <article class="legal-content">
        <div class="container">
            <div class="row g-0">
                <div class="col-12 col-md-8 mx-auto">
                    <div data-anim="slide-up delay-2" class="legal-content__body">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </article>

</main>

<?php
    endwhile;
endif;

get_footer();
?>
