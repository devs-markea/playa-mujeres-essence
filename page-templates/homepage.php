<?php
/*
Template Name: Homepage Playa Mujeres Essence
*/

get_header();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main overflow-hidden" role="main">
            <?php
            /**
             * Functions hooked in to homepage action
             *
             * @hooked pm_essence_homepage_section_hero
             */
            do_action('homepage');
            ?>

        </main>
    </div>

<?php

get_footer();