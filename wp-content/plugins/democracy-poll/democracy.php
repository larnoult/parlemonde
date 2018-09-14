<?php
/**
 * Plugin Name: Democracy Poll
 * Description: Allows to create democratic polls. Visitors can vote for more than one answer & add their own answers.
 *
 * Author: Kama
 * Author URI: http://wp-kama.ru/
 * Plugin URI: http://wp-kama.ru/id_67/plagin-oprosa-dlya-wordpress-democracy-poll.html
 *
 * Text Domain: democracy-poll
 * Domain Path: /languages/
 *
 * Version: 5.5.6
 *
 * PHP: 5.3+
 */


if( !defined('ABSPATH') ) exit; // no direct access

__('Allows to create democratic polls. Visitors can vote for more than one answer & add their own answers.');


$data = get_file_data( __FILE__, array('Version'=>'Version', 'Domain Path'=>'Domain Path') );
define('DEM_VER', $data['Version'] );
define('DEM_DOMAIN_PATH', '/'. trim($data['Domain Path'],'/') .'/' ); // /languages/

define('DEM_MAIN_FILE',  __FILE__ );
define('DEMOC_URL',  plugin_dir_url(DEM_MAIN_FILE) );
define('DEMOC_PATH', plugin_dir_path(DEM_MAIN_FILE) );


## Set table names for MU
function dem_set_dbtables(){
	global $wpdb;
	$wpdb->democracy_q   = $wpdb->prefix .'democracy_q';
	$wpdb->democracy_a   = $wpdb->prefix .'democracy_a';
	$wpdb->democracy_log = $wpdb->prefix .'democracy_log';
}
dem_set_dbtables();


//if( is_admin() )
require_once DEMOC_PATH .'admin/upgrade-activate-funcs.php';
require_once DEMOC_PATH .'theme-functions.php';

require_once DEMOC_PATH .'class-DemPoll.php';
require_once DEMOC_PATH .'class-Democracy_Poll.php';
require_once DEMOC_PATH .'admin/class-Democracy_Poll_Admin.php';

register_activation_hook( DEM_MAIN_FILE, 'democracy_activate' );

add_action('plugins_loaded', 'democracy_poll_init' );
function democracy_poll_init(){
	// first init
	democr();

	// enable widget
	if( democr()->opt('use_widget') ) require_once DEMOC_PATH . 'widget_democracy.php';
}

function democr(){
	return Democracy_Poll::init();
}



