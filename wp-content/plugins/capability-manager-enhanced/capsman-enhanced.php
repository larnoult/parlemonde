<?php
/**
 * Capability Manager. Main Plugin File.
 * Plugin to create and manage Roles and Capabilities.
 *
 * @package 	capability-manager-enhanced
 * @author		Jordi Canals, Kevin Behrens
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals; modifications Copyright (C) 2012-2018 Kevin Behrens
 * @license		GNU General Public License version 3
 * @link		http://agapetry.net
 * @version 	1.5.9
 */

/*
Plugin Name: Capability Manager Enhanced
Plugin URI: http://presspermit.com/capability-manager
Description: Manage WordPress role definitions, per-site or network-wide. Organizes post capabilities by post type and operation.
Version: 1.5.9
Author: Jordi Canals, Kevin Behrens
Author URI: http://agapetry.net
Text Domain: capsman-enhanced
Domain Path: /lang/
License: GPLv3
*/

if ( ! defined( 'CAPSMAN_VERSION' ) ) {
	define( 'CAPSMAN_VERSION', '1.5.9' );
	define( 'CAPSMAN_ENH_VERSION', '1.5.9' );
}

if ( cme_is_plugin_active( 'capsman.php' ) ) {
	$message = __( '<strong>Error:</strong> Capability Manager Extended cannot function because another copy of Capability Manager is active.', 'capsman-enhanced' );
	add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade" style="color: black">' . $message . '</div>\';'));
	return;
} else {
	define ( 'CME_FILE', __FILE__ );
	define ( 'AK_CMAN_PATH', dirname(__FILE__) );
	define ( 'AK_CMAN_LIB', AK_CMAN_PATH . '/includes' );

	/**
	 * Sets an admin warning regarding required PHP version.
	 *
	 * @hook action 'admin_notices'
	 * @return void
	 */
	function _cman_php_warning() {
		$data = get_plugin_data(__FILE__);
		load_plugin_textdomain('capsman-enhanced', false, basename(dirname(__FILE__)) .'/lang');

		echo '<div class="error"><p><strong>' . __('Warning:', 'capsman-enhanced') . '</strong> '
			. sprintf(__('The active plugin %s is not compatible with your PHP version.', 'capsman-enhanced') .'</p><p>',
				'&laquo;' . $data['Name'] . ' ' . $data['Version'] . '&raquo;')
			. sprintf(__('%s is required for this plugin.', 'capsman-enhanced'), 'PHP-5 ')
			. '</p></div>';
	}

	// ============================================ START PROCEDURE ==========

	// Check required PHP version.
	if ( version_compare(PHP_VERSION, '5.0.0', '<') ) {
		// Send an armin warning
		add_action('admin_notices', '_cman_php_warning');
	} else {
		global $pagenow;
	
		if ( is_admin() && 
		( isset($_REQUEST['page']) && in_array( $_REQUEST['page'], array( 'capsman', 'capsman-tool' ) ) 
		|| ( ! empty($_SERVER['SCRIPT_NAME']) && strpos( $_SERVER['SCRIPT_NAME'], 'p-admin/plugins.php' ) && ! empty($_REQUEST['action'] ) ) 
		|| ( isset($_GET['action']) && 'reset-defaults' == $_GET['action'] )
		|| in_array( $pagenow, array( 'users.php', 'user-edit.php', 'profile.php', 'user-new.php' ) )
		) ) {
			global $capsman;
			
			// Run the plugin
			include_once ( AK_CMAN_PATH . '/framework/loader.php' );
			include ( AK_CMAN_LIB . '/manager.php' );
			$capsman = new CapabilityManager(__FILE__, 'capsman');
			
			if ( isset($_REQUEST['page']) && ( 'capsman' == $_REQUEST['page'] ) ) {
				add_action( 'admin_enqueue_scripts', '_cme_pp_scripts' );
			}
		} else {
			load_plugin_textdomain('capsman-enhanced', false, basename(dirname(__FILE__)) .'/lang');
			add_action( 'admin_menu', 'cme_submenus', 20 );
		}
	}
}

add_action( 'plugins_loaded', '_cme_act_pp_active', 1 );

function _cme_act_pp_active() {
	if ( defined('PP_VERSION') || defined('PPC_VERSION') )
		define( 'PP_ACTIVE', true );
}

function _cme_pp_scripts() {
	wp_enqueue_style( 'plugin-install' );
	wp_enqueue_script( 'plugin-install' );
	add_thickbox();
}

// perf enchancement: display submenu links without loading framework and plugin code
function cme_submenus() {
	$cap_name = ( is_super_admin() ) ? 'manage_capabilities' : 'restore_roles';
	add_management_page(__('Capability Manager', 'capsman-enhanced'),  __('Capability Manager', 'capsman-enhanced'), $cap_name, 'capsman' . '-tool', 'cme_fakefunc');
	
	if ( did_action( 'pp_admin_menu' ) ) {	// Put Capabilities link on Permissions menu if Press Permit is active and user has access to it
		global $pp_admin;
		$menu_caption = ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) ? __('Capabilities', 'capsman-enhanced') : 'Role Capabilities';
		add_submenu_page( $pp_admin->get_menu('options'), __('Capability Manager', 'capsman-enhanced'),  $menu_caption, 'manage_capabilities', 'capsman', 'cme_fakefunc' );
	} else {
		add_users_page( __('Capability Manager', 'capsman-enhanced'),  __('Capabilities', 'capsman-enhanced'), 'manage_capabilities', 'capsman', 'cme_fakefunc');	
	}
}

function cme_is_plugin_active($check_plugin_file) {
	if ( ! $check_plugin_file )
		return false;

	$plugins = get_option('active_plugins');

	foreach ( $plugins as $plugin_file ) {
		if ( false !== strpos($plugin_file, $check_plugin_file) )
			return $plugin_file;
	}
}

// if a role is marked as hidden, also default it for use by Press Permit as a Pattern Role (when PP Collaborative Editing is activated and Advanced Settings enabled)
function _cme_pp_default_pattern_role( $role ) {
	if ( ! $pp_role_usage = get_option( 'pp_role_usage' ) )
		$pp_role_usage = array();
		
	if ( empty( $pp_role_usage[$role] ) ) {
		$pp_role_usage[$role] = 'pattern';
		update_option( 'pp_role_usage', $pp_role_usage );
	}
}

if ( is_multisite() )
	require_once ( AK_CMAN_PATH . '/includes/network.php' );