<?php
/**
 * Checks if a plugin is active.
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Checks if a plugin is enabled
 *
 * @category Class
 */
class Virtue_Plugin_Check {
	/**
	 * Static var active plugins
	 *
	 * @var $active_plugins
	 */
	private static $active_plugins;

	/**
	 * Initialize
	 */
	public static function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}

	/**
	 * Active Check
	 *
	 * @param string $plugin_base_name is plugin folder/filename.php.
	 */
	public static function active_check( $plugin_base_name ) {

		if ( ! self::$active_plugins ) {
			self::init();
		}
		return in_array( $plugin_base_name, self::$active_plugins, true ) || array_key_exists( $plugin_base_name, self::$active_plugins );
	}
}
