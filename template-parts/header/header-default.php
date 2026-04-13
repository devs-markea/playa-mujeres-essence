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
            <?php
            $more_label = 'Plan your trip';
            if ( function_exists( 'pll_current_language' ) && pll_current_language() === 'es' ) {
                $more_label = 'Planea tu viaje';
            }
            ?>
            <div class="pm-header__menu" data-more-label="<?php echo esc_attr( $more_label ); ?>">
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
<?php
$pm_filter_path = dirname(__DIR__) . '/menu/filters/gastronomy/filter-by-hotel.php';
if (is_string($pm_filter_path) && file_exists($pm_filter_path)) {
    require $pm_filter_path;
}
?>

