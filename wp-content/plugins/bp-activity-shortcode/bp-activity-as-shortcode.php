<?php
/**
 * Plugin Name: BuddyPress Activity ShortCode
 * Description: Embed activity stream in page/post using shortcode
 * Author: BuddyDev
 * Plugin URI: https://buddydev.com/plugins/bp-activity-shortcode/
 * Author URI: https://buddydev.com/
 * Version: 1.1.5
 * License: GPL
 */

// exit if access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper class.
 */

/**
 * Class BD_Activity_Stream_Shortcodes_Helper
 *
 * @property-read string $url plugin url.
 * @property-read string $path plugin path.
 */
class BD_Activity_Stream_Shortcodes_Helper {

	/**
	 * Singleton instance.
	 *
	 * @var BD_Activity_Stream_Shortcodes_Helper
	 */
	private static $instance;

	/**
	 * Plugin absolute path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugin directory url
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Constructor
	 */
	private function __construct() {

		$this->path = plugin_dir_path( __FILE__ );
		$this->url  = plugin_dir_url( __FILE__ );

		$this->setup();
	}

	/**
	 * Get Instance
	 *
	 * @return BD_Activity_Stream_Shortcodes_Helper
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Accessor.
	 *
	 * @param string $name property name.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		return isset( $this->{$name} ) ? $this->{$name}: null;
	}

	/**
	 *  Callback to buddypress actions
	 */
	public function setup() {
		add_action( 'bp_loaded', array( $this, 'load' ) );
		add_action( 'bp_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Load plugin files
	 */
	public function load() {

		$files = array(
			'core/class-bpas-shortcode-util.php',
			'core/class-bpas-ajax-handler.php',
			'core/class-bpas-shortcode-helper.php',
		);

		foreach ( $files as $file ) {
			require_once $this->path . $file;
		}
	}

	/**
	 * Load plugin assets
	 */
	public function load_assets() {
		wp_register_script( 'bpas-loadmore-js', $this->url . 'assets/js/bpas-loadmore.js', array( 'jquery' ) );
		wp_enqueue_script( 'bpas-loadmore-js' );
	}
}

BD_Activity_Stream_Shortcodes_Helper::get_instance();
