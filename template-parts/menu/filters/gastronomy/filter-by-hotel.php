<div class="pm-collection-filters-menu" aria-hidden="true">
    <div class="pm-collection-filters-menu__panel">
        <div class="pm-collection-filters-menu__header">
            <div class="container d-flex align-items-center justify-content-between">
                <div class="pm-menu-mobile__logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php echo pm_get_logo( 'site-logo-dark', 'full', 'desktop_dark' ); ?>
                    </a>
                </div>
                <div class="d-flex flex-row align-items-center justify-content-center">
                    <?php if ( get_theme_mod( 'pm_header_show_lang_switcher', 1 ) ) : ?>
                        <?php pm_essence_lang_switcher('lang-dark'); ?>
                    <?php endif; ?>

                    <div class="pm-menu-mobile__closeo" data-collection-filters-mobile-close>
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.75 0.75L0.75108 16.7489M16.7489 16.75L0.75 0.751134" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- aquí adentro va tu contenido (pills, etc.) -->
    </div>
</div>

<style>
    /* ==========================================================================
       COLLECTION FILTERS MENU (Mobile) — Animación tipo .pm-menu-mobile (slide X)
       ========================================================================== */

    .pm-collection-filters-menu{
        position: fixed;
        inset: 0;
        z-index: 1600;
        pointer-events: none;
    }

    .pm-collection-filters-menu__overlay{
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.45);
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    /* Panel a pantalla completa como el menú mobile */
    .pm-collection-filters-menu__panel{
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;

        display: flex;
        flex-direction: column;
        background-color: #fff;

        transform: translateX(-100%);
        transition: transform 0.35s ease-in-out;

        overflow: hidden; /* el scroll va en el body interno */
    }

    /* Estado activo */
    .pm-collection-filters-menu.-is-active{
        pointer-events: auto;
    }

    .pm-collection-filters-menu.-is-active .pm-collection-filters-menu__overlay{
        opacity: 1;
    }

    .pm-collection-filters-menu.-is-active .pm-collection-filters-menu__panel{
        transform: translateX(0);
    }

    /* Header */
    .pm-collection-filters-menu__header{
        padding: 1.9rem 0;
        border-bottom: 1px solid #E5E5E5;
        box-shadow: 0 2px 4px 0 rgba(50, 50, 105, 0.08);

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex: 0 0 auto;
    }

    .pm-collection-filters-menu__title{
        font-family: var(--pm-font-secondary);
        font-size: 16px;
        font-style: italic;
        font-weight: 500;
        letter-spacing: 1px;
        color: #323232;
    }

    .pm-collection-filters-menu__close{
        width: 40px;
        height: 40px;
        border-radius: 999px;
        border: 1px solid #E5E5E5;
        background: #fff;
        color: #323232;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
    }

    /* Contenido scrolleable (cuando metas pills/listas) */
    .pm-collection-filters-menu__body{
        flex: 1 1 auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 16px;
    }

    @media (prefers-reduced-motion: reduce){
        .pm-collection-filters-menu__overlay,
        .pm-collection-filters-menu__panel{
            transition: none;
        }
    }

    /* ==========================================================================
       END COLLECTION FILTERS MENU
       ========================================================================== */
</style>

<script>
    (function () {
        var body = document.body;

        var filtersMenu = document.querySelector('.pm-collection-filters-menu');
        if (!filtersMenu) return;

        var isOpen = function () {
            return filtersMenu.classList.contains('-is-active');
        };

        var openFiltersMenu = function () {
            filtersMenu.classList.add('-is-active');
            filtersMenu.setAttribute('aria-hidden', 'false');
            if (body) body.style.overflow = 'hidden';
        };

        var closeFiltersMenu = function () {
            filtersMenu.classList.remove('-is-active');
            filtersMenu.setAttribute('aria-hidden', 'true');
            if (body) body.style.overflow = '';
        };

        var toggleFiltersMenu = function () {
            if (isOpen()) closeFiltersMenu();
            else openFiltersMenu();
        };

        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-collection-filters-mobile-trigger]');
            if (!trigger) return;
            e.preventDefault();
            toggleFiltersMenu();
        });

        document.addEventListener('click', function (e) {
            var closer = e.target.closest('[data-collection-filters-mobile-close]');
            if (!closer) return;
            if (!isOpen()) return;
            e.preventDefault();
            closeFiltersMenu();
        });

        document.addEventListener('keydown', function (e) {
            var key = e.key || e.keyCode;
            if (!isOpen()) return;

            if (key === 'Escape' || key === 'Esc' || key === 27) {
                closeFiltersMenu();
            }
        });

        filtersMenu.setAttribute('aria-hidden', 'true');
    }());
</script>