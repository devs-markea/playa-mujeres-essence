<?php
/**
 * PM_Essence_Core Class
 *
 * @since    1.0.0
 * @package  pm-essence
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PM_Essence_Core' ) ) :

    /**
     * The main PM_Essence_Core class
     */
    class PM_Essence_Core {

        /**
         * Setup class.
         *
         * @since 1.0
         */
        public function __construct() {
            add_action( 'after_setup_theme', array( $this, 'setup' ) );
            add_action( 'widgets_init', array( $this, 'widgets_init' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 10 );
            add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
            add_filter( 'body_class', array( $this, 'body_classes' ) );
            add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );

        }

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         */
        public function setup() {
            /*
             * Load Localisation files.
             *
             * Note: the first-loaded translation file overrides any following ones if the same translation is present.
             */


            /**
             * Add default posts and comments RSS feed links to head.
             */
            add_theme_support( 'automatic-feed-links' );

            /*
             * Enable support for Post Thumbnails on posts and pages.
             *
             * @link https://developer.wordpress.org/reference/functions/add_theme_support/#Post_Thumbnails
             */
            add_theme_support( 'post-thumbnails' );

            /**
             * Enable support for site logo.
             */
            add_theme_support(
                'custom-logo',
                apply_filters(
                    'pm_essence_custom_logo_args',
                    array(
                        'height'      => 110, //Ajustar a la medida que se requiera para el tema.
                        'width'       => 470, //Ajustar a la medida que se requiera para el tema.
                        'flex-width'  => true,
                        'flex-height' => true,
                    )
                )
            );

            /**
             * Register menu locations.
             */
            register_nav_menus(
                apply_filters(
                    'pm_essence_register_nav_menus',
                    array(
                        'desktop'   => __( 'Menú (Desktop)', 'pm-essence' ),
                        'mobile' => __( 'Menú (Mobile)', 'pm-essence' ),
                        'footer-1'  => __( 'Footer 1', 'pm-essence' ),
                        'footer-2'  => __( 'Footer 2', 'pm-essence' ),
                        'footer-3'  => __( 'Footer 3', 'pm-essence' ),
                    )
                )
            );

            /*
             * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
             * to output valid HTML5.
             */
            add_theme_support(
                'html5',
                apply_filters(
                    'pm_essence_html5_args',
                    array(
                        'search-form',
                        'comment-form',
                        'comment-list',
                        'gallery',
                        'caption',
                        'widgets',
                        'style',
                        'script',
                    )
                )
            );

            /**
             * Setup the WordPress core custom background feature.
             */
            add_theme_support(
                'custom-background',
                apply_filters(
                    'pm_essence_custom_background_args',
                    array(
                        'default-color' => apply_filters( 'pm_essence_default_background_color', 'ffffff' ),
                        'default-image' => '',
                    )
                )
            );

            /**
             * Declare support for title theme feature.
             */
            add_theme_support( 'title-tag' );

            /**
             * Declare support for selective refreshing of widgets.
             */
            add_theme_support( 'customize-selective-refresh-widgets' );

            /**
             * Add support for Block Styles.
             */
            add_theme_support( 'wp-block-styles' );

            /**
             * Add support for full and wide align images.
             */
            add_theme_support( 'align-wide' );

            /**
             * Add support for editor styles.
             */
            add_theme_support( 'editor-styles' );

            /**
             * Add support for editor font sizes.
             */
            add_theme_support(
                'editor-font-sizes',
                array(
                    array(
                        'name' => __( 'Small', 'pm-essence' ),
                        'size' => 14,
                        'slug' => 'small',
                    ),
                    array(
                        'name' => __( 'Normal', 'pm-essence' ),
                        'size' => 16,
                        'slug' => 'normal',
                    ),
                    array(
                        'name' => __( 'Medium', 'pm-essence' ),
                        'size' => 23,
                        'slug' => 'medium',
                    ),
                    array(
                        'name' => __( 'Large', 'pm-essence' ),
                        'size' => 26,
                        'slug' => 'large',
                    ),
                    array(
                        'name' => __( 'Huge', 'pm-essence' ),
                        'size' => 37,
                        'slug' => 'huge',
                    ),
                )
            );

            /**
             * Enqueue editor styles.
             */
            add_editor_style( array( 'assets/css/base/gutenberg-editor.css', $this->google_fonts() ) );

            /**
             * Add support for responsive embedded content.
             */
            add_theme_support( 'responsive-embeds' );

            /**
             * Add support for appearance tools.
             *
             * @link https://wordpress.org/documentation/wordpress-version/version-6-5/#add-appearance-tools-to-classic-themes
             */
            add_theme_support( 'appearance-tools' );
        }

        /**
         * Register widget area.
         *
         * @link https://codex.wordpress.org/Function_Reference/register_sidebar
         */
        public function widgets_init() {

        }

        /**
         * Enqueue scripts and styles.
         *
         * @since  1.0.0
         */
        public function scripts() {
            global $pm_essence_version;

            /**
             * Styles
             */
            wp_enqueue_style(
                'essence-style',
                PM_ESSENCE_TEMPLATE_URI . '/style.css',
                array('pm-bootstrap-css'), // depende de bootstrap
                $pm_essence_version
            );

            wp_enqueue_style(
                'essence-mainstyles',
                PM_ESSENCE_TEMPLATE_URI . '/assets/css/main.css',
                array('pm-bootstrap-css', 'essence-style'), // depende de ambos
                $pm_essence_version
            );



            //wp_enqueue_style( 'hero-icons', 'https://cdn.jsdelivr.net/npm/heroicons/css/heroicons.min.css' );

            /**
             * Fonts
             */
            // wp_enqueue_style( 'pm-essence-fonts', $this->google_fonts(), array(), $pm_essence_version );
            wp_enqueue_style( 'pm-essence-fonts', $this->adobe_fonts_url(), array(), $pm_essence_version );

            /**
             * Scripts
             */
            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            wp_enqueue_script('jquery');

            //wp_enqueue_script( 'blockui-js', get_template_directory_uri() . '/assets/libs/blockui/jquery.blockUI.min.js', array('jquery'), $pm_essence_version, false );
            wp_enqueue_script( 'main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $pm_essence_version, false );
        }

        /**
         * Register Google fonts.
         *
         * @since 1.0.0
         * @return string Google fonts URL for the theme.
         */
        public function google_fonts() {
            $google_fonts = apply_filters(
                'pm_essence_google_font_families',
                array(
                    //'Raleway' => 'Raleway:ital,wght@0,100,200,300,400,500,600,700,800,900;1,100,200,300,400,500,600,700,800,900&',
                    //'Playfair Display' => 'Playfair+Display:ital,wght@0,400,500,600,700,800,900;1,400,500,600,700,800,900',
                )
            );

            $query_args = array(
                'family' => implode( '|', $google_fonts ),
                'subset' => rawurlencode( 'latin,latin-ext' ),
            );

            $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

            return $fonts_url;
        }

        /**
         * Return Adobe Fonts URL (Typekit)
         *
         * Allows devs to filter or replace the Kit ID dynamically.
         *
         * Usage:
         * - Add Adobe Kit ID via filter: pm_essence_adobe_kit_id
         * - Returns full CSS URL ready to enqueue.
         */
        public function adobe_fonts_url() {

            // Permitir override con filtros (igual que Google Fonts)
            $kit_id = apply_filters(
                'pm_essence_adobe_kit_id',
                'jky6aby' // Default vacío
            );

            // Si no hay kit ID, no cargamos nada
            if ( empty( $kit_id ) ) {
                return '';
            }

            // Construir URL
            $fonts_url = sprintf( 'https://use.typekit.net/%s.css', trim( $kit_id ) );

            return esc_url( $fonts_url );
        }


        /**
         * Enqueue block assets.
         *
         * @since 1.0.0
         */
        public function block_assets() {
            global $pm_essence_version;

            // Styles.
            //wp_enqueue_style( 'essence-gutenberg-blocks', get_template_directory_uri() . '/assets/css/base/gutenberg-blocks.css', '', $pm_essence_version );
        }

        /**
         * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
         *
         * @param array $args Configuration arguments.
         * @return array
         */
        public function page_menu_args( $args ) {
            $args['show_home'] = true;
            return $args;
        }

        /**
         * Adds custom classes to the array of body classes.
         *
         * @param array $classes Classes for the body element.
         * @return array
         */
        public function body_classes( $classes ) {
            // Adds a class to blogs with more than 1 published author.
            if ( is_multi_author() ) {
                $classes[] = 'group-blog';
            }

            return $classes;
        }
    }
endif;

return new PM_Essence_Core();
