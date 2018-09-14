<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fieldmaster_admin_install_network') ) :

class fieldmaster_admin_install_network {

	/*
	*  __construct
	*
	*  A good place to add actions / filters
	*
	*  @type	function
	*  @date	11/08/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// actions
		add_action('network_admin_menu', array($this,'network_admin_menu'), 20);
		
	}
	
	
	/*
	*  network_admin_menu
	*
	*  This function will chck for available updates and add actions if needed
	*
	*  @type	function
	*  @date	2/04/2015
	*  @since	5.1.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function network_admin_menu() {
		
		// vars
		$prompt = false;
		
		
		// loop through sites and find updates
		$sites = fieldmaster_get_sites();
		
		if( $sites ) {
			
			foreach( $sites as $site ) {
				
				// switch blog
				switch_to_blog( $site['blog_id'] );
				
				
				// get site updates
				$updates = fieldmaster_get_db_updates();
				
				
				// restore
				restore_current_blog();
				
				
				if( $updates ) {
				
					$prompt = true;
					break;
					
				}
				
			}
			
		}
		
		
		// bail if no prompt
		if( !$prompt ) return;
		
		
		// actions
		add_action('network_admin_notices', array($this, 'network_admin_notices'), 1);
		
		
		// add page
		$page = add_submenu_page('index.php', __('Upgrade Database','fieldmaster'), __('Upgrade Database','fieldmaster'), fieldmaster_get_setting('capability'), 'fieldmaster-upgrade-network', array($this,'network_html'));
		
		
		// actions
		add_action('load-' . $page, array($this,'network_load'));
		
	}
	
	
	/*
	*  load
	*
	*  This function will look at the $_POST data and run any functions if needed
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function network_load() {
		
		// hide notice on this page 
		remove_action('network_admin_notices', array($this, 'network_admin_notices'), 1);
		
		
		// load fieldmaster scripts
		fieldmaster_enqueue_scripts();
		
	}
	
	
	
	/*
	*  network_admin_notices
	*
	*  This function will render the update notice
	*
	*  @type	function
	*  @date	2/04/2015
	*  @since	5.1.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function network_admin_notices() {
			
		// view
		$view = array(
			'button_text'	=> __("Review sites & upgrade", 'fieldmaster'),
			'button_url'	=> network_admin_url('index.php?page=fieldmaster-upgrade-network'),
			'confirm'		=> false
		);
		
		
		// load view
		fieldmaster_get_view('install-notice', $view);
		
	}
	
	
	/*
	*  network_html
	*
	*  This function will render the HTML for the network upgrade page
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function network_html() {
		
		// vars
		$plugin_version = fieldmaster_get_setting('version');
		
		
		// loop through sites and find updates
		$sites = fieldmaster_get_sites();
		
		if( $sites ) {
			
			foreach( $sites as $i => $site ) {
				
				// switch blog
				switch_to_blog( $site['blog_id'] );
				
				
				// extra info
				$site['name'] = get_bloginfo('name');
				$site['url'] = home_url();
				
				
				// get site updates
				$site['updates'] = fieldmaster_get_db_updates();
				
				
				// get site version
				$site['fieldmaster_version'] = get_option('fieldmaster_version');
				
				
				// no value equals new instal
				if( !$site['fieldmaster_version'] ) {
					
					$site['fieldmaster_version'] = $plugin_version;
					
				}
				
				
				// update
				$sites[ $i ] = $site;
				
				
				// restore
				restore_current_blog();
				
			}
			
		}
		
		
		// view
		$view = array(
			'sites'				=> $sites,
			'plugin_version'	=> $plugin_version
		);
		
		
		// load view
		fieldmaster_get_view('install-network', $view);
		
	}
			
}

// initialize
new fieldmaster_admin_install_network();

endif; // class_exists check


/*
*  fieldmaster_get_sites
*
*  This function will return an array of site data
*
*  @type	function
*  @date	29/08/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	(array)
*/

function fieldmaster_get_sites() {
	
	// vars
	$sites = array();
	
	
	// WP >= 4.6
	if( function_exists('get_sites') ) {
		
		$_sites = get_sites(array(
			'number' => 0
		));
		
		foreach( $_sites as $_site ) {
			
	        $_site = get_site( $_site );
	        $sites[] = $_site->to_array();
	        
	    }
		
	// WP < 4.6	
	} else {
		
		$sites = wp_get_sites(array(
			'limit' => 0
		));
		
	}
	
	
	// return
	return $sites;
	
}

?>