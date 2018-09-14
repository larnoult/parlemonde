<?php

/*
Plugin Name: View own posts and media library items only
Plugin URI: http://www.shinephp.com/view-own-posts-and-images-only-wordpress-plugin/
Description: Limits posts and media library items available for contributors and authors by their own (added, uploaded, attached) only.
Version: 1.3
Author: Vladimir Garagulya
Author URI: http://www.shinephp.com
Text Domain: view-own-posts-media-only
Domain Path: /lang/
*/

/*
Copyright 2013-2014  Vladimir Garagulya  (email: vladimir@shinephp.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

/*
 * Main code is taken from http://wordpress.stackexchange.com/questions/1482/restricting-users-to-view-only-media-library-items-they-have-uploaded
 * Thanks to http://wordpress.stackexchange.com/users/9485/paul
 */


define('VOPMO_PLUGIN_NAME', 'View Own Posts Images Only' );

if ( !function_exists("get_option" ) ) {
  exit;  // Silence is golden, direct call is prohibited
}

$vopmo_wp_version = get_bloginfo('version');  // as global $wp_version could be unavailable.
if (version_compare( $vopmo_wp_version, "3.2","<" ) ) {  
	if ( is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
		$exit_msg = VOPMO_PLUGIN_NAME .' requires WordPress 3.2 or newer <a href="http://codex.wordpress.org/Upgrading_WordPress"> Please update!</a>';
    wp_die( $exit_msg );
	} else {
		return;
	}
}

if (version_compare(PHP_VERSION, '5.2.4', '<')) {
	if ( is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
		$exit_msg = VOPMO_PLUGIN_NAME .' requires PHP 5.2.4 or newer <a href="http://codex.wordpress.org/Upgrading_WordPress"> Please update!</a>';
    wp_die( $exit_msg );
	} else {
		return;
	}
}

if ( !class_exists('View_Own_Posts_Media_Only') ) {	
	define('VOPMO_PLUGIN_URL', plugin_dir_url(__FILE__) );
	define('VOPMO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define('VOPMO_PLUGIN_DIR_NAME', dirname( plugin_basename( __FILE__ ) ));
	require_once(VOPMO_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'class-view-own-posts-media-only-library.php');
	require_once(VOPMO_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'class-view-own-posts-media-only.php');
	
	$view_own_posts_media_only = new View_Own_Posts_Media_Only();
}