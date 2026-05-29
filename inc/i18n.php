<?php
/**
 * Register translatable strings for Polylang.
 * Manage translations at: Languages → String translations (group: "pm-essence").
 *
 * Format: 'short-name' => 'Full string shown in templates with pll__()'
 * The name (key) is only a label in the Polylang admin — keep it short.
 * The string (value) must match exactly what pll__() / pll_e() receives in templates.
 */

add_action( 'init', function () {
    if ( ! function_exists( 'pll_register_string' ) ) {
        return;
    }

    $strings = [
        // General UI
        'ui-read-more'     => 'Read more',
        'ui-load-more'     => 'Load more',
        'ui-view-more'     => 'View more',
        'ui-close'         => 'Close',
        'ui-back'          => 'Back',
        'ui-next'          => 'Next',
        'ui-previous'      => 'Previous',
        'ui-see-all'       => 'See all',

        // Forms
        'form-send'        => 'Send',
        'form-send-form'   => 'Send form',
        'form-submit'      => 'Submit',
        'form-name'        => 'Name',
        'form-lastname'    => 'Last name',
        'form-email'       => 'Email',
        'form-phone'       => 'Phone',
        'form-message'     => 'Message',
        'form-required'    => 'Required field',
        'form-sending'     => 'Sending...',
        'form-sent'        => 'Message sent',
        'form-error'       => 'Error sending',

        // Navigation
        'nav-menu'         => 'Menu',
        'nav-search'       => 'Search',
        'nav-book-now'     => 'Book now',
        'nav-reserve'      => 'Reserve',
        'nav-where-to-stay' => 'Where to stay',
        'nav-experiences'  => 'Experiences',

        // Blog
        'blog-hero-heading' => 'Discover Everything You Can Do At This Exclusive Destination',
        'blog-all'         => 'All',
        'blog-filter'      => 'Filter',
        'blog-no-results'  => 'No results found',
        'blog-share'       => 'Share',
        'blog-label'       => 'Blog',
        'blog-categories'  => 'Categories',
        'blog-top-posts'   => 'Top Posts',
        'blog-subscribe'   => 'Subscribe',
        'blog-email-placeholder'  => 'Type your email address',
        'blog-no-posts'           => 'No posts were found for this category.',
        'blog-prev-posts'         => 'Previous posts',
        'blog-next-posts'         => 'Next posts',
        'blog-read-more-exp'      => 'Read More About Our Experiences',
        'blog-related-posts'      => 'Related Posts',
        'blog-share-post'         => 'Share this post',

        // Newsletter sidebar
        'newsletter-label'   => 'Newsletter',
        'newsletter-sidebar-desc' => 'Subscribe for exclusive Playa Mujeres updates and luxury travel inspiration',

        // Newsletter banner
        'newsletter-banner-title' => 'Join An Exclusive Club Of Sophisticated Travelers',
        'newsletter-banner-desc'  => 'Receive our monthly Playa Mujeres newsletter that offers you the best recommendations for your next getaway.',

        // Mega menu / panels
        'menu-resorts-desc'       => 'Discover a collection of world-class resorts in Playa Mujeres',
        'menu-paradise-tagline'   => 'Because paradise feels different for everyone.',
        'menu-group-desc'         => 'Select the group you are travelling with and discover everything we have to offer you.',
        'menu-filter-hotel'       => 'Filter By Hotel',
        'menu-filter-hotel-desc'  => 'You can filter by hotel, choose one from the list below.',
        'menu-view-results'       => 'View results',

        // Weather widget
        'widget-weather'   => 'Weather Now',

        // Footer
        'footer-find-us'   => 'Find Us',
        'footer-hotels'    => 'Hotels',
        'footer-links'     => 'Links',
        'footer-address'   => 'ADDRESS: MZA 1 SMZA 3, PUNTA SAM, ISLA MUJERES, QUINTANA ROO, C.P. 77400.',
        'footer-copyright' => '© Playa Mujeres Resort. All rights reserved. Powered by',
    ];

    foreach ( $strings as $name => $string ) {
        pll_register_string( $name, $string, 'pm-essence' );
    }
} );
