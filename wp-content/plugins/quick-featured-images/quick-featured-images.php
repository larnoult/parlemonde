<?php
/**
 * @package   Quick_Featured_Images_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://stehle-internet.de/
 * @copyright 2014 Martin Stehle
 *
 * @wordpress-plugin
 * Plugin Name:       Quick Featured Images
 * Plugin URI:        http://wordpress.org/plugins/quick-featured-images
 * Description:       Your time-saving Swiss Army Knife for featured images: Set, replace and delete them in bulk, in posts lists and set default images for future posts.
 * Version:           13.3.2
 * Author:            Martin Stehle
 * Author URI:        http://stehle-internet.de
 * Text Domain:       quick-featured-images
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$qfi_root = plugin_dir_path( __FILE__ );

/*
 * since 1.0: Make object instance of base class
 *
 */
require_once( $qfi_root . 'admin/class-quick-featured-images-admin.php' );
add_action( 'plugins_loaded', array( 'Quick_Featured_Images_Admin', 'get_instance' ) );


if ( is_admin() ) {

	/*
	 * Register hooks that are fired when the plugin is activated or deactivated.
	 * When the plugin is deleted, the uninstall.php file is loaded.
	 *
	 */
	register_activation_hook( __FILE__, array( 'Quick_Featured_Images_Admin', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Quick_Featured_Images_Admin', 'deactivate' ) );

	/*
	 * Make object instance of bulk tools class
	 *
	 */
	require_once( $qfi_root . 'admin/class-quick-featured-images-tools.php' );
	add_action( 'plugins_loaded', array( 'Quick_Featured_Images_Tools', 'get_instance' ) );

}

/*
 * since 8.0: Make object instance of default images functions class
 *
 */
require_once( $qfi_root . 'admin/class-quick-featured-images-defaults.php' );
add_action( 'plugins_loaded', array( 'Quick_Featured_Images_Defaults', 'get_instance' ) );


if ( is_admin() ) {
	/*
	 * since 7.0: Make object instance of options page class
	 *
	 */
	require_once( $qfi_root . 'admin/class-quick-featured-images-settings.php' );
	add_action( 'plugins_loaded', array( 'Quick_Featured_Images_Settings', 'get_instance' ) );

	/*
	 * since 7.0: Make object instance of column functions class
	 *
	 */
	require_once( $qfi_root . 'admin/class-quick-featured-images-columns.php' );
	add_action( 'plugins_loaded', array( 'Quick_Featured_Images_Columns', 'get_instance' ) );

}
