<?php
/**
 * Playa Mujeres Essence Engine Room
 *
 * @package PM_Essence
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define("PM_ESSENCE_TEMPLATE_DIR", get_template_directory());
define("PM_ESSENCE_TEMPLATE_URI", get_template_directory_uri());
define("PM_ESSENCE_ASSETS_URI", PM_ESSENCE_TEMPLATE_URI . '/assets');

//include('inc/wp-bootstrap-navwalker.php');

/**
 * Assign the Playa Mujeres Essence version to a var
 */
$theme              = wp_get_theme();
$pm_essence_version = $theme->get( 'Version' );

$pm_essence = (object) array(
	'version'    => $pm_essence_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require PM_ESSENCE_TEMPLATE_DIR . '/inc/class-pm-essence-core.php',
	'customizer' => require PM_ESSENCE_TEMPLATE_DIR . '/inc/customizer/class-pm-essence-customizer.php',
);

require PM_ESSENCE_TEMPLATE_DIR . '/inc/enqueue.php';
require PM_ESSENCE_TEMPLATE_DIR . '/inc/helpers.php';
require PM_ESSENCE_TEMPLATE_DIR . '/inc/class-wp-bootstrap-navwalker.php';

require PM_ESSENCE_TEMPLATE_DIR . '/inc/template-hooks.php';
require PM_ESSENCE_TEMPLATE_DIR . '/inc/template-functions.php';
//require PM_ESSENCE_TEMPLATE_DIR . 'inc/components/name-component/name-component-functions.php';

if ( is_admin() ) {
    //Require php file for admin only

}

