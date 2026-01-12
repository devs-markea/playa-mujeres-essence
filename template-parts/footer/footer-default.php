<?php
$footer_logo       = get_theme_mod( 'pm_footer_logo' );
$footer_menu_col_1 = (int) get_theme_mod( 'pm_footer_menu_col_1' );
$footer_menu_col_2 = (int) get_theme_mod( 'pm_footer_menu_col_2' );
$footer_menu_col_3 = (int) get_theme_mod( 'pm_footer_menu_col_3' );
$footer_copyright  = get_theme_mod( 'pm_footer_copyright' );

$facebook_url  = trim( get_theme_mod( 'pm_social_facebook_url', '' ) );
$instagram_url = trim( get_theme_mod( 'pm_social_instagram_url', '' ) );
?>

<div class="footer-top container">
    <div class="row g-0">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="row gx-0 gy-4 gx-md-3 gy-md-3 justify-content-center">
                <div class="col-md-3 col-12 footer-top__logo">
                    <?php if ( $footer_logo ) : ?>
                        <div class="footer-logo mb-3">
                            <img src="<?php echo esc_url( $footer_logo ); ?>"
                                 alt="<?php bloginfo( 'name' ); ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-3 col-12 footer-top__find-us">
                    <div class="footer-top__menu-heading">
                        <h6>Find Us</h6>
                    </div>
                    <div class="address">
                        <p>ADDRESS: MZA 1 SMZA 3, PUNTA SAM, ISLA MUJERES, QUINTANA ROO, C.P. 77400.</p>
                    </div>
                    <?php
                    if ( $facebook_url || $instagram_url ) :
                    ?>
                    <div class="social-links">
                        <div class="social-links__inner">
                            <?php if ( $facebook_url ) : ?>
                                <a href="<?php echo esc_url( $facebook_url ); ?>" class="social-links__item" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                    <svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_170_1387)">
                                            <path d="M7.435 5.18288H4.903V3.52188C4.88924 3.42358 4.8973 3.32345 4.92661 3.22861C4.95592 3.13377 5.00575 3.04655 5.07257 2.97314C5.13939 2.89974 5.22156 2.84195 5.31323 2.80388C5.4049 2.76581 5.50384 2.7484 5.603 2.75288H7.39V0.0118824L4.933 0.00588233C4.4836 -0.0298224 4.03181 0.0324118 3.6088 0.188289C3.18579 0.344167 2.80164 0.589975 2.48287 0.90875C2.16409 1.22752 1.91828 1.61167 1.76241 2.03468C1.60653 2.45769 1.5443 2.90948 1.58 3.35888V5.18688H0V8.00588H1.58V16.0059H4.903V8.00588H7.145L7.435 5.18288Z" fill="#819496"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_170_1387">
                                                <rect width="7.435" height="16" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ( $instagram_url ) : ?>
                                <a href="<?php echo esc_url( $instagram_url ); ?>" class="social-links__item" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_170_1393)">
                                            <path d="M7.99985 3.83984C7.18321 3.83984 6.38491 4.08201 5.7059 4.53571C5.02689 4.98941 4.49767 5.63427 4.18515 6.38874C3.87264 7.14322 3.79087 7.97342 3.95019 8.77437C4.10951 9.57532 4.50276 10.311 5.08021 10.8885C5.65766 11.4659 6.39338 11.8592 7.19432 12.0185C7.99527 12.1778 8.82548 12.0961 9.57995 11.7835C10.3344 11.471 10.9793 10.9418 11.433 10.2628C11.8867 9.58378 12.1289 8.78548 12.1289 7.96884C12.1267 6.87441 11.691 5.82541 10.9172 5.05153C10.1433 4.27765 9.09428 3.84196 7.99985 3.83984ZM7.99985 10.6138C7.47672 10.6138 6.96534 10.4587 6.53037 10.1681C6.0954 9.87744 5.75638 9.46435 5.55619 8.98104C5.356 8.49773 5.30362 7.96591 5.40567 7.45283C5.50773 6.93975 5.75964 6.46845 6.12955 6.09855C6.49946 5.72864 6.97076 5.47672 7.48384 5.37467C7.99692 5.27261 8.52874 5.32499 9.01205 5.52518C9.49536 5.72537 9.90845 6.06439 10.1991 6.49936C10.4897 6.93433 10.6449 7.44571 10.6449 7.96884C10.6417 8.66937 10.362 9.3403 9.86666 9.83565C9.37131 10.331 8.70038 10.6107 7.99985 10.6138Z" fill="#819496"/>
                                            <path d="M12.29 4.67469C12.8064 4.67469 13.225 4.25607 13.225 3.73969C13.225 3.2233 12.8064 2.80469 12.29 2.80469C11.7736 2.80469 11.355 3.2233 11.355 3.73969C11.355 4.25607 11.7736 4.67469 12.29 4.67469Z" fill="#819496"/>
                                            <path d="M14.71 1.3232C14.2651 0.8838 13.7353 0.539821 13.1529 0.312332C12.5705 0.0848427 11.9478 -0.0213687 11.323 0.000201891H4.67696C4.05295 -0.03743 3.42806 0.0577626 2.84357 0.279493C2.25908 0.501223 1.72828 0.844444 1.28624 1.28648C0.844199 1.72852 0.500979 2.25932 0.279249 2.84381C0.0575187 3.42831 -0.0376742 4.0532 -4.22625e-05 4.6772V11.2902C-0.0235561 11.9278 0.0846327 12.5635 0.317769 13.1574C0.550906 13.7513 0.903984 14.2908 1.35496 14.7422C2.26377 15.5873 3.46953 16.0395 4.70996 16.0002H11.29C12.5404 16.042 13.757 15.5901 14.677 14.7422C15.1192 14.2928 15.4647 13.7576 15.6923 13.1696C15.9198 12.5816 16.0245 11.9532 16 11.3232V4.6772C16.0196 4.06035 15.9157 3.44579 15.6941 2.86976C15.4726 2.29373 15.1379 1.76788 14.71 1.3232ZM14.581 11.3232C14.5965 11.7591 14.5215 12.1934 14.3605 12.5988C14.1996 13.0042 13.9563 13.3717 13.646 13.6782C12.9995 14.2527 12.1551 14.5536 11.291 14.5172H4.70996C3.84585 14.5536 3.00142 14.2527 2.35496 13.6782C2.05657 13.3595 1.82561 12.9837 1.67594 12.5736C1.52628 12.1634 1.46097 11.7272 1.48396 11.2912V4.6772C1.46378 4.24631 1.53049 3.81576 1.68013 3.41119C1.82976 3.00661 2.05926 2.63627 2.35496 2.3222C2.99857 1.74297 3.84512 1.44174 4.70996 1.4842H11.355C11.7858 1.46402 12.2164 1.53074 12.621 1.68037C13.0256 1.83001 13.3959 2.0595 13.71 2.3552C14.2888 2.98839 14.6008 3.82057 14.581 4.6782V11.3232Z" fill="#819496"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_170_1393">
                                                <rect width="16" height="16" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <div class="col-md-3 col-12 footer-top__hotels">
                    <div class="footer-top__menu-heading">
                        <h6>Hotels</h6>
                    </div>
                    <?php
                    if ( $footer_menu_col_2 ) {
                        wp_nav_menu( array(
                            'menu'        => $footer_menu_col_2,
                            'container'   => false,
                            'menu_class'  => 'footer-menu list-unstyled',
                            'fallback_cb' => false,
                            'depth'       => 1,
                        ) );
                    }
                    ?>
                </div>

                <div class="col-md-3 col-12 footer-top__links">
                    <div class="footer-top__menu-heading">
                        <h6>Links</h6>
                    </div>
                    <?php
                    if ( $footer_menu_col_3 ) {
                        wp_nav_menu( array(
                            'menu'        => $footer_menu_col_3,
                            'container'   => false,
                            'menu_class'  => 'footer-menu list-unstyled',
                            'fallback_cb' => false,
                            'depth'       => 1,
                        ) );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer-bottom d-flex justify-content-center align-items-center">
    <div class="footer-bottom__copyright">
        <p>
            <?php if ( $footer_copyright ) : ?>
                <?php echo wp_kses_post( $footer_copyright ); ?>
            <?php endif; ?>
        </p>
    </div>
</div>
<style>

    .footer-top .footer-top__logo,
    .footer-top .footer-top__find-us,
    .footer-top .footer-top__hotels,
    .footer-top .footer-top__links {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .footer-logo img {
        max-width: 180px;
        height: auto;
    }

    .footer-menu li + li {
        margin-top: .25rem;
    }

    .footer-menu a {
        text-decoration: none;
        color: #323232;
        font-size: 14px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .footer-top {
        padding: 2rem 0;
    }

    .footer-top .social-links__inner > a:first-child svg {
        margin-right: 1rem;
    }

    .footer-top__menu-heading {
        margin-bottom: 1.5rem;
    }

    .footer-top__menu-heading h6 {
        text-transform: uppercase;
        color: #323232;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        letter-spacing: 1px;
        margin: 0;
    }

    .footer-bottom {
        border-top: 1px solid rgba(0, 0, 0, 0.25);
        padding: 0.75rem 0;
    }

    .footer-bottom .footer-bottom__copyright {
        padding: 0 3rem;
    }

    .footer-bottom .footer-bottom__copyright p {
        color: #323232;
        text-align: center;
        font-size: 15px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
        margin: 0;
    }

    .footer-top .address p{
        color: #323232;
        font-size: 14px;
        font-style: normal;
        font-weight: 300;
        line-height: 20px;
        letter-spacing: 1px;
    }

    @media (min-width: 992px) {
        .footer-top .footer-top__logo,
        .footer-top .footer-top__find-us,
        .footer-top .footer-top__hotels,
        .footer-top .footer-top__links {
            align-items: flex-start;
            text-align: left;
        }
    }

</style>