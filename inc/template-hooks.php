<?php
/**
 * Playa Mujeres Essence hooks
 *
 * @package pm-essence
 */

/**
 * Homepage
 *
 * @see  pp_essence_homepage()
 */

add_action('wp_ajax_nopriv_newsletter_submit', 'handle_newsletter_submit');
add_action('wp_ajax_newsletter_submit', 'handle_newsletter_submit');

add_action('wp_ajax_nopriv_pm_weather', 'pm_weather_ajax_handler');
add_action('wp_ajax_pm_weather', 'pm_weather_ajax_handler');

//add_action( 'homepage', 'pm_essence_homepage_section_hero', 20 );