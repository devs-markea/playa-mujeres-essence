<?php get_header(); ?>

<?php if( have_posts() ): ?>
    <?php while( have_posts() ): the_post(); ?>
        <section class="py-5">
            <div class="container">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

