<?php
/**
 * Register translatable strings for Polylang.
 * Manage translations at: Languages → String translations (group: "pm-essence").
 * Usage in templates: pll__('string') or pll_e('string').
 */

add_action( 'init', function () {
    if ( ! function_exists( 'pll_register_string' ) ) {
        return;
    }

    $strings = [
        // General UI
        'Read more'        => 'Read more',
        'Load more'        => 'Load more',
        'View more'        => 'View more',
        'Close'            => 'Close',
        'Back'             => 'Back',
        'Next'             => 'Next',
        'Previous'         => 'Previous',
        'See all'          => 'See all',

        // Forms
        'Send'             => 'Send',
        'Send form'        => 'Send form',
        'Submit'           => 'Submit',
        'Name'             => 'Name',
        'Last name'        => 'Last name',
        'Email'            => 'Email',
        'Phone'            => 'Phone',
        'Message'          => 'Message',
        'Required field'   => 'Required field',
        'Sending...'       => 'Sending...',
        'Message sent'     => 'Message sent',
        'Error sending'    => 'Error sending',

        // Navigation
        'Menu'             => 'Menu',
        'Search'           => 'Search',
        'Book now'         => 'Book now',
        'Reserve'          => 'Reserve',
        'Where to stay'    => 'Where to stay',
        'Experiences'      => 'Experiences',

        // Blog
        'All'              => 'All',
        'Filter'           => 'Filter',
        'No results found' => 'No results found',
        'Share'            => 'Share',
        'Blog'             => 'Blog',
        'Categories'       => 'Categories',
        'Top Posts'        => 'Top Posts',
        'Subscribe'        => 'Subscribe',
        'Type your email address'                  => 'Type your email address',
        'No posts were found for this category.'   => 'No posts were found for this category.',
        'Previous posts'   => 'Previous posts',
        'Next posts'       => 'Next posts',

        // Blog listing
        'Read More About Our Experiences'                                                           => 'Read More About Our Experiences',
        'Related Posts'    => 'Related Posts',
        'Share this post'  => 'Share this post',

        // Newsletter sidebar
        'Newsletter'                                                                                => 'Newsletter',
        'Subscribe for exclusive Playa Mujeres updates and luxury travel inspiration'               => 'Subscribe for exclusive Playa Mujeres updates and luxury travel inspiration',

        // Newsletter banner
        'Join An Exclusive Club Of Sophisticated Travelers'                                         => 'Join An Exclusive Club Of Sophisticated Travelers',
        'Receive our monthly Playa Mujeres newsletter that offers you the best recommendations for your next getaway.' => 'Receive our monthly Playa Mujeres newsletter that offers you the best recommendations for your next getaway.',

        // Mega menu / panels
        'Discover a collection of world-class resorts in Playa Mujeres' => 'Discover a collection of world-class resorts in Playa Mujeres',
        'Because paradise feels different for everyone.'                 => 'Because paradise feels different for everyone.',
        'Select the group you are travelling with and discover everything we have to offer you.' => 'Select the group you are travelling with and discover everything we have to offer you.',
        'Filter By Hotel'                                                => 'Filter By Hotel',
        'You can filter by hotel, choose one from the list below.'       => 'You can filter by hotel, choose one from the list below.',
        'View results'                                                   => 'View results',

        // Weather widget
        'Weather Now'      => 'Weather Now',
    ];

    foreach ( $strings as $name => $string ) {
        pll_register_string( $name, $string, 'pm-essence' );
    }
} );
