<?php
/**
 * Plugin Name:     Username Changer
 * Description:     Change usernames easily
 * Version:         3.1.3
 * Author:          Daniel J Griffiths
 * Author URI:      https://evertiro.com
 * Text Domain:     username-changer
 *
 * @package         UsernameChanger
 * @author          Daniel J Griffiths <dgriffiths@evertiro.com>
 * @copyright       Copyright (c) 2014, Daniel J Griffiths
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Main Username_Changer class
 *
 * @since       2.0.0
 */
if ( ! class_exists( 'Username_Changer' ) ) {

	class Username_Changer {


		/**
		 * @var         Username_Changer $instance The one true Username_Changer
		 * @since       2.0.0
		 */
		private static $instance;


		/**
		 * @var         object $settings The settings object
		 * @since       3.0.0
		 */
		public $settings;


		/**
		 * @var         object $template_tags The template tags object
		 * @since       3.0.0
		 */
		public $template_tags;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       2.0.0
		 * @return      object self::$instance The one true Username_Changer
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new Username_Changer();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->template_tags = new Username_Changer_Template_Tags();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       2.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin path
			define( 'USERNAME_CHANGER_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'USERNAME_CHANGER_URL', plugin_dir_url( __FILE__ ) );

			// Plugin version
			define( 'USERNAME_CHANGER_VER', '3.1.3' );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			global $username_changer_options;

			// Setup the plugin settings
			require_once USERNAME_CHANGER_DIR . 'includes/admin/settings/register.php';
			if ( ! class_exists( 'S214_Settings' ) ) {
				require_once USERNAME_CHANGER_DIR . 'includes/libraries/s214-settings/source/class.s214-settings.php';
			}
			$this->settings           = new S214_Settings( 'username_changer', 'settings' );
			$username_changer_options = $this->settings->get_settings();

			require_once USERNAME_CHANGER_DIR . 'includes/functions.php';
			require_once USERNAME_CHANGER_DIR . 'includes/scripts.php';
			require_once USERNAME_CHANGER_DIR . 'includes/class.template-tags.php';

			if ( is_admin() ) {
				require_once USERNAME_CHANGER_DIR . 'includes/admin/actions.php';
			}
		}


		/**
		 * Load plugin language files
		 *
		 * @access      public
		 * @since       2.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'username_changer_lang_dir', $lang_dir );

			// WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'username-changer' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'username-changer', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/username-changer/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/username-changer folder
				load_textdomain( 'username-changer', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/username-changer/languages/ filder
				load_textdomain( 'username-changer', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'username-changer', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true Username_Changer
 * instance to functions everywhere.
 *
 * @since       2.0.0
 * @return      Username_Changer The one true Username_Changer
 */
function username_changer() {
	return Username_Changer::instance();
}
add_action( 'plugins_loaded', 'username_changer' );
