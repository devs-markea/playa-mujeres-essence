<?php
/**
 * PM_Essence_Customizer Class
 *
 * Handles all Customizer integrations for the theme.
 *
 * @since    1.0.0
 * @package  PM_Essence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PM_Essence_Customizer' ) ) :

    /**
	 * Main Customizer Class
	 */
	class PM_Essence_Customizer {
    
        /**
		 * Single instance.
		 *
		 * @var PM_Essence_Customizer|null
		 */
		protected static $instance = null;

        /**
		 * Get instance.
		 *
		 * @return PM_Essence_Customizer
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

        /**
		 * Constructor.
		 *
		 * Private to enforce singleton usage.
		 */
		private function __construct() {
			// Register Customizer settings & controls.
			add_action( 'customize_register', array( $this, 'register' ) );

			// Live preview JS (optional).
//			add_action( 'customize_preview_init', array( $this, 'preview_js' ) );

			// CSS dynamic output (optional).
			add_action( 'wp_head', array( $this, 'dynamic_css' ) );
		}

        /**
		 * Register Customizer panels, sections, settings, and controls.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function register( $wp_customize ) {

			// Panel general de opciones del tema.
			$wp_customize->add_panel(
				'pm_theme_options',
				array(
					'title'       => __( 'PM Essence Theme Options', 'pm-essence' ),
					'description' => __( 'Global theme content & layout options.', 'pm-essence' ),
					'priority'    => 10,
				)
			);

			self::section_header( $wp_customize );
			self::section_mobile_menu( $wp_customize );
			self::section_footer( $wp_customize );
			self::section_social_links( $wp_customize );
		}

		/**
		 * Devuelve los menús disponibles como choices para selects.
		 *
		 * @return array
		 */
		protected static function get_menus_choices() {
			$choices   = array( 0 => __( '— Select a menu —', 'pm-essence' ) );
			$nav_menus = wp_get_nav_menus();

			if ( ! empty( $nav_menus ) && ! is_wp_error( $nav_menus ) ) {
				foreach ( $nav_menus as $menu ) {
					$choices[ $menu->term_id ] = $menu->name;
				}
			}

			return $choices;
		}

		/**
		 * Sanitiza checkbox/boolean.
		 *
		 * @param mixed $checked Value.
		 * @return int 1|0
		 */
		public static function sanitize_checkbox( $checked ) {
			return ( isset( $checked ) && ( true === $checked || '1' === $checked || 1 === $checked ) ) ? 1 : 0;
		}

		/**
		 * SECTION: Header
		 */
		protected static function section_header( $wp_customize ) {

            $wp_customize->add_section(
				'pm_header_section',
				array(
					'title'    => __( 'Header', 'pm-essence' ),
					'panel'    => 'pm_theme_options',
					'priority' => 10,
				)
			);

			// Logo (desktop).
			$wp_customize->add_setting(
				'pm_header_logo',
				array(
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'pm_header_logo',
					array(
						'label'    => __( 'Logo (Desktop)', 'pm-essence' ),
						'section'  => 'pm_header_section',
						'settings' => 'pm_header_logo',
					)
				)
			);

			// Logo (mobile).
			$wp_customize->add_setting(
				'pm_header_logo_mobile',
				array(
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'pm_header_logo_mobile',
					array(
						'label'    => __( 'Logo (Mobile)', 'pm-essence' ),
						'section'  => 'pm_header_section',
						'settings' => 'pm_header_logo_mobile',
					)
				)
			);

            /* ----------------------------------------
 *  NEW: Logo (Desktop) Dark
 * ---------------------------------------- */
            $wp_customize->add_setting(
                'pm_header_logo_dark',
                array(
                    'default'           => '',
                    'sanitize_callback' => 'esc_url_raw',
                )
            );

            $wp_customize->add_control(
                new WP_Customize_Image_Control(
                    $wp_customize,
                    'pm_header_logo_dark',
                    array(
                        'label'    => __( 'Logo (Desktop) – Dark Version', 'pm-essence' ),
                        'section'  => 'pm_header_section',
                        'settings' => 'pm_header_logo_dark',
                    )
                )
            );

            /* ----------------------------------------
             *  NEW: Logo (Mobile) Dark
             * ---------------------------------------- */
            $wp_customize->add_setting(
                'pm_header_logo_mobile_dark',
                array(
                    'default'           => '',
                    'sanitize_callback' => 'esc_url_raw',
                )
            );

            $wp_customize->add_control(
                new WP_Customize_Image_Control(
                    $wp_customize,
                    'pm_header_logo_mobile_dark',
                    array(
                        'label'    => __( 'Logo (Mobile) – Dark Version', 'pm-essence' ),
                        'section'  => 'pm_header_section',
                        'settings' => 'pm_header_logo_mobile_dark',
                    )
                )
            );

			// Selector de menú para header.
			$wp_customize->add_setting(
				'pm_header_menu_id',
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'pm_header_menu_id',
				array(
					'label'   => __( 'Header menu', 'pm-essence' ),
					'section' => 'pm_header_section',
					'type'    => 'select',
					'choices' => self::get_menus_choices(),
				)
			);

			// Toggle lang switcher.
			$wp_customize->add_setting(
				'pm_header_show_lang_switcher',
				array(
					'default'           => 1,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'pm_header_show_lang_switcher',
				array(
					'label'   => __( 'Show language switcher', 'pm-essence' ),
					'section' => 'pm_header_section',
					'type'    => 'checkbox',
				)
			);
		}

		/**
		 * SECTION: Mobile Menu
		 */
		protected static function section_mobile_menu( $wp_customize ) {

			$wp_customize->add_section(
				'pm_mobile_menu_section',
				array(
					'title'    => __( 'Mobile Menu', 'pm-essence' ),
					'panel'    => 'pm_theme_options',
					'priority' => 20,
				)
			);

			// Selector de menú mobile.
			$wp_customize->add_setting(
				'pm_mobile_menu_id',
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'pm_mobile_menu_id',
				array(
					'label'   => __( 'Mobile menu', 'pm-essence' ),
					'section' => 'pm_mobile_menu_section',
					'type'    => 'select',
					'choices' => self::get_menus_choices(),
				)
			);

			// Toggle social links en menú mobile.
			$wp_customize->add_setting(
				'pm_mobile_show_social_links',
				array(
					'default'           => 1,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'pm_mobile_show_social_links',
				array(
					'label'   => __( 'Show social links in mobile menu', 'pm-essence' ),
					'section' => 'pm_mobile_menu_section',
					'type'    => 'checkbox',
				)
			);

			// Toggle widget del clima.
			$wp_customize->add_setting(
				'pm_mobile_show_weather_widget',
				array(
					'default'           => 1,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'pm_mobile_show_weather_widget',
				array(
					'label'   => __( 'Show weather widget in mobile menu', 'pm-essence' ),
					'section' => 'pm_mobile_menu_section',
					'type'    => 'checkbox',
				)
			);
		}

		/**
		 * SECTION: Footer
		 */
		protected static function section_footer( $wp_customize ) {

			$wp_customize->add_section(
				'pm_footer_section',
				array(
					'title'    => __( 'Footer', 'pm-essence' ),
					'panel'    => 'pm_theme_options',
					'priority' => 30,
				)
			);

			// Logo footer.
			$wp_customize->add_setting(
				'pm_footer_logo',
				array(
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'pm_footer_logo',
					array(
						'label'    => __( 'Footer logo', 'pm-essence' ),
						'section'  => 'pm_footer_section',
						'settings' => 'pm_footer_logo',
					)
				)
			);

			// Footer menu column 1.
			$wp_customize->add_setting(
				'pm_footer_menu_col_1',
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'pm_footer_menu_col_1',
				array(
					'label'   => __( 'Footer Column 1 Menu', 'pm-essence' ),
					'section' => 'pm_footer_section',
					'type'    => 'select',
					'choices' => self::get_menus_choices(),
				)
			);

			// Footer menu column 2.
			$wp_customize->add_setting(
				'pm_footer_menu_col_2',
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'pm_footer_menu_col_2',
				array(
					'label'   => __( 'Footer Column 2 Menu', 'pm-essence' ),
					'section' => 'pm_footer_section',
					'type'    => 'select',
					'choices' => self::get_menus_choices(),
				)
			);

			// Footer menu column 3.
			$wp_customize->add_setting(
				'pm_footer_menu_col_3',
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'pm_footer_menu_col_3',
				array(
					'label'   => __( 'Footer Column 3 Menu', 'pm-essence' ),
					'section' => 'pm_footer_section',
					'type'    => 'select',
					'choices' => self::get_menus_choices(),
				)
			);

			// Copyright textarea.
			$wp_customize->add_setting(
				'pm_footer_copyright',
				array(
					'default'           => '',
					'sanitize_callback' => 'wp_kses_post',
				)
			);

			$wp_customize->add_control(
				'pm_footer_copyright',
				array(
					'label'       => __( 'Footer copyright text', 'pm-essence' ),
					'description' => __( 'You can use basic HTML (links, <br>, &copy;, etc.).', 'pm-essence' ),
					'section'     => 'pm_footer_section',
					'type'        => 'textarea',
				)
			);
		}

		/**
		 * SECTION: Social Links
		 *
		 * (URLs para redes principales; luego podrás consumirlas
		 *  en header, footer y/o menú mobile).
		 */
		protected static function section_social_links( $wp_customize ) {

			$wp_customize->add_section(
				'pm_social_links_section',
				array(
					'title'    => __( 'Social Links', 'pm-essence' ),
					'panel'    => 'pm_theme_options',
					'priority' => 40,
				)
			);

			$networks = array(
				'facebook'    => __( 'Facebook URL', 'pm-essence' ),
				'instagram'   => __( 'Instagram URL', 'pm-essence' ),
				'x'           => __( 'X (Twitter) URL', 'pm-essence' ),
				'tiktok' => __( 'Tiktok URL', 'pm-essence' ),
				'youtube'     => __( 'YouTube URL', 'pm-essence' ),
			);

			foreach ( $networks as $key => $label ) {

				$setting_id = 'pm_social_' . $key . '_url';

				$wp_customize->add_setting(
					$setting_id,
					array(
						'default'           => '',
						'sanitize_callback' => 'esc_url_raw',
					)
				);

				$wp_customize->add_control(
					$setting_id,
					array(
						'label'       => $label,
						'section'     => 'pm_social_links_section',
						'type'        => 'url',
						'input_attrs' => array(
							'placeholder' => 'https://',
						),
					)
				);
			}
		}

		/**
		 * Load Customizer live preview JS.
		 */
		public function preview_js() {
//			wp_enqueue_script(
//				'pm-essence-customizer-preview',
//				get_template_directory_uri() . '/assets/js/customizer-preview.js',
//				array( 'customize-preview' ),
//				false,
//				true
//			);
		}

		/**
		 * Dynamic CSS output (based on Customizer values).
		 */
		public function dynamic_css() {
			$primary   = get_theme_mod( 'pm_essence_primary_color', '#0055a5' );
			$secondary = get_theme_mod( 'pm_essence_secondary_color', '#a89968' );

			echo '<style>
				:root {
					--pm-primary: ' . esc_attr( $primary ) . ';
					--pm-secondary: ' . esc_attr( $secondary ) . ';
				}
			</style>';
		}
	}


endif;

/**
 * Return instance.
 */
return PM_Essence_Customizer::get_instance();
