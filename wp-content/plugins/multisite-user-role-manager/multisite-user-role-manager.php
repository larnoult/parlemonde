<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://wpartisan.me
 * @since             1.0.0
 * @package           Multisite_User_Role_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Multisite User Role Manager
 * Plugin URI:        https://wpartisan.me/plugins/multisite-user-role-manager
 * Description:       Manage user roles for each blog from a single screen on WPMU setups
 * Version:           1.0.7
 * Author:            OzTheGreat (WPArtisan)
 * Author URI:        https://wpartisan.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       multisite-user-role-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-multisite-user-role-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_multisite_user_role_manager() {

	$plugin = new Multisite_User_Role_Manager();
	$plugin->run();

}
run_multisite_user_role_manager();
