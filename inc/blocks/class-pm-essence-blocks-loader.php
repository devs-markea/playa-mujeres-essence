<?php
/**
 * PM_Essence_Blocks_Loader Class
 *
 * @since    1.0.0
 * @package  pm-essence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PM_Essence_Blocks_Loader' ) ) :


class PM_Essence_Blocks_Loader {
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	public function register_blocks() {

		register_block_type(
			get_template_directory() . '/inc/blocks/video-hero'
		);
        
	}
}

endif;

new PM_Essence_Blocks_Loader();
