<div class="pm-menu-desktop">
    <div class="pm-menu-desktop__content container d-flex flex-column">
        <div class="pm-menu-desktop__scroll">
<!--            --><?php //pm_essence_menu_where_to_stay_desktop(); ?>

        </div>
    </div>
</div>

<style>

    .pm-menu-desktop__header{
        padding: 16px;
        border-bottom: 1px solid #E5E5E5;
    }

    .pm-menu-desktop__nav .navbar {
        padding: 0;
    }

    .pm-menu-desktop__nav {
        margin: 48px 0 32px;
    }

    .pm-menu-desktop__content{
        position: relative;
        padding: 16px;
        flex: 1;
        overflow: hidden;
    }

    .pm-menu-desktop__scroll {
        overflow-y: auto;
    }

    .pm-menu-desktop__footer {
        margin-top: auto;
        margin-bottom: 48px;
    }

    .pm-menu-desktop {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        flex-direction: column;
        background-color: white;
        z-index: 1500;
        pointer-events: none;
        transform: translateX(-100%);
        transition: transform 0.35s ease-in-out;
    }

    .pm-menu-desktop.-is-active {
        pointer-events: auto;
        transform: translateX(0);
    }

    .pm-menu-desktop__nav .pm-navbar-mobile .menu-item > a {
        color: var(--pm-secondary-900);
        font-size: 20px;
        font-weight: 400;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .pm-menu-desktop__nav .pm-navbar-mobile .menu-item {
        margin-bottom: 24px;
    }

    .pm-menu-desktop__nav .pm-navbar-mobile .menu-item:last-child {
        margin-bottom: 0;
    }

    .pm-menu-desktop__nav .pm-navbar-mobile .menu-item.current_page_item > a {
        color: var(--pm-primary-900);
        font-weight: 600;
    }

    .decorative-sun {
        position: absolute;
        bottom: 0;
        right: 0;
        margin-bottom: 110px;
    }

    .pm-menu-desktop__footer .social-links h4,
    .pm-menu-desktop__footer .weather-now h4 {
        font-family: var(--pm-font-secondary);
        font-size: 16px;
        font-style: italic;
        font-weight: 500;
        color: var(--pm-secondary-900);
    }

    .pm-menu-desktop__footer .weather-now h4{
        margin-bottom: 8px;
    }

    .pm-menu-desktop__footer .social-links h4{
        margin-bottom: 16px;
    }

    .weather-now .weather-now__content {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 50px;
        background: #E5E0D1;
    }

</style>