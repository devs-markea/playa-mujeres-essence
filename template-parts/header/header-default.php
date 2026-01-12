<?php
/**
 * Default Header Template
 *
 * @package pm-essence
 */
?>

<header class="pm-header pm-header--fixed">
    <nav class="navbar navbar-expand-lg navbar-light" data-header>
        <div class="container d-flex flex-row justify-content-between">
            <div class="pm-header__logo">
                <a class="navbar-brand pm-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <?php echo pm_get_logo( 'logo-desktop'); ?>
                </a>
            </div>
            <div class="pm-header__menu">
                <?php pm_essence_nav_menu(); ?>
            </div>

            <div class="d-flex flex-row align-items-center justify-content-center">
                <?php if ( get_theme_mod( 'pm_header_show_lang_switcher', 1 ) ) : ?>
                    <?php pm_essence_lang_switcher(); ?>
                <?php endif; ?>

                <div class="pm-header__menu-mobile" data-menu-hamburger>
                    <svg class="icon icon--hamburger" viewBox="0 0 32 16" xmlns="http://www.w3.org/2000/svg">
                        <line x1="0" y1="1"  x2="32" y2="1"  stroke="currentColor" stroke-width="2"/>
                        <line x1="0" y1="8"  x2="32" y2="8"  stroke="currentColor" stroke-width="2"/>
                        <line x1="8" y1="15" x2="32" y2="15" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>

            </div>

        </div>
    </nav>
</header>
<?php pm_essence_menu_where_to_stay_desktop(); ?>
<?php pm_essence_menu_experiences_desktop(); ?>
<div class="header-overlay"></div>
<?php pm_essence_menu_mobile(); ?>
<style>
    .pm-header__menu-mobile {
        color: #FFF;
    }

    .pm-header__menu-mobile:hover {
        color: #000;
    }

    .icon--hamburger {
        width: 32px;
        height: 16px;
    }

</style>



