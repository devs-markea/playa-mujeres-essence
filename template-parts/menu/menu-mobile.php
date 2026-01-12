<div class="pm-menu-mobile">
    <div class="pm-menu-mobile__header container d-flex align-items-center justify-content-between">
        <div class="pm-menu-mobile__logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php echo pm_get_logo( 'site-logo-dark', 'full', 'desktop_dark' ); ?>
            </a>
        </div>
        <div class="d-flex flex-row align-items-center justify-content-center">
            <?php if ( get_theme_mod( 'pm_header_show_lang_switcher', 1 ) ) : ?>
                <?php pm_essence_lang_switcher('lang-dark'); ?>
            <?php endif; ?>

            <div class="pm-menu-mobile__close" data-menu-close>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.75 0.75L0.75108 16.7489M16.7489 16.75L0.75 0.751134" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="pm-menu-mobile__content container d-flex flex-column">
        <div class="pm-menu-mobile__scroll">
            <div class="pm-menu-mobile__nav">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <?php pm_essence_nav_menu('mobile'); ?>
                </nav>
            </div>
            <div class="pm-menu-mobile__footer d-flex justify-content-between">
                <?php pm_essence_show_social_links(); ?>
                <div class="weather-now">
                    <h4>Weather Now</h4>
                    <div class="weather-now__content">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.5">
                                <path d="M17 12C17 14.7614 14.7614 17 12 17C9.23858 17 7 14.7614 7 12C7 9.23858 9.23858 7 12 7C14.7614 7 17 9.23858 17 12Z" stroke="#323232" stroke-width="1.5"/>
                                <path d="M12 2V3.5M12 20.5V22M19.0708 19.0713L18.0101 18.0106M5.98926 5.98926L4.9286 4.9286M22 12H20.5M3.5 12H2M19.0713 4.92871L18.0106 5.98937M5.98975 18.0107L4.92909 19.0714" stroke="#323232" stroke-width="1.5" stroke-linecap="round"/>
                            </g>
                        </svg>
                        <span>25°C</span>
                    </div>
                </div>
            </div>
            <div class="decorative-sun">
                <img src="<?php echo PM_ESSENCE_ASSETS_URI ?>/images/decorative-sun-playa-mujeres.webp" alt="Background Decorative Sun">
            </div>
        </div>
        <?php pm_essence_menu_where_to_stay_mobile(); ?>
        <?php pm_essence_menu_experiences_mobile(); ?>
    </div>
</div>

