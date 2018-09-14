<?php

/*
*  FieldMaster Admin Update Class
*
*  All the logic for updates
*
*  @class 		fieldmaster_admin_update
*  @package		FieldMaster
*  @subpackage	Admin
*/

if( ! class_exists('fieldmaster_admin_update') ) :

class fieldmaster_admin_update {

	/*
	*  __construct
	*
	*  A good place to add actions / filters
	*
	*  @type	function
	*  @date	11/08/13
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct() {

		// actions
		add_action('admin_menu', 						array($this,'admin_menu'), 20);
		add_action('network_admin_menu', 				array($this,'network_admin_menu'), 20);


		// ajax
		add_action('wp_ajax_fields/admin/data_upgrade',	array($this, 'ajax_upgrade'));

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

		// bail early if no show_admin
		if( !fieldmaster_get_setting('show_admin') ) {

			return;

		}


		// vars
		$prompt = false;


		// loop through sites and find updates
		$sites = wp_get_sites();

		if( $sites ) {

			foreach( $sites as $site ) {

				// switch blog
				switch_to_blog( $site['blog_id'] );


				// get site updates
				$updates = fieldmaster_get_updates();


				// restore
				restore_current_blog();


				if( $updates ) {

					$prompt = true;
					break;

				}

			}

		}


		// bail if no prompt
		if( !$prompt ) {

			return;

		}


		// actions
		add_action('network_admin_notices', array($this, 'network_admin_notices'), 1);


		// add page
		add_submenu_page('update-core.php', __('Upgrade FieldMaster','fields'), __('Upgrade FieldMaster','fields'), fieldmaster_get_setting('capability'),'fields-upgrade', array($this,'network_html'));

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

		// bail ealry if already on update page
		if( fieldmaster_is_screen('admin_page_fields-upgrade-network') ) {

			return;

		}


		// view
		$view = array(
			'button_text'	=> __("Review sites & upgrade", 'fields'),
			'button_url'	=> network_admin_url('update-core.php?page=fields-upgrade'),
			'confirm'		=> false
		);


		// load view
		fieldmaster_get_view('update-notice', $view);

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
		$sites = wp_get_sites();

		if( $sites ) {

			foreach( $sites as $i => $site ) {

				// switch blog
				switch_to_blog( $site['blog_id'] );


				// extra info
				$site['name'] = get_bloginfo('name');
				$site['url'] = home_url();


				// get site updates
				$site['updates'] = fieldmaster_get_updates();


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
			'sites' => $sites,
			'plugin_version'	=> $plugin_version
		);


		// enqueue
		fieldmaster_enqueue_scripts();


		// load view
		fieldmaster_get_view('update-network', $view);

	}


	/*
	*  admin_menu
	*
	*  This function will chck for available updates and add actions if needed
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function admin_menu() {

		// vars
		$plugin_version = fieldmaster_get_setting('version');
		$fieldmaster_version = get_option('fieldmaster_version');


		// bail early if a new install
		if( !$fieldmaster_version ) {

			update_option('fieldmaster_version', $plugin_version );
			return;

		}


		// bail early if $fieldmaster_version is >= $plugin_version
		if( version_compare( $fieldmaster_version, $plugin_version, '>=') ) {

			return;

		}


		// vars
		$updates = fieldmaster_get_updates();


		// bail early if no updates available
		if( empty($updates) ) {

			update_option('fieldmaster_version', $plugin_version );
			return;

		}


		// bail early if no show_admin
		if( !fieldmaster_get_setting('show_admin') ) {

			return;

		}


		// actions
		add_action('admin_notices', array($this, 'admin_notices'), 1);


		// add page
		add_submenu_page('edit.php?post_type=fields-field-group', __('Upgrade','fields'), __('Upgrade','fields'), fieldmaster_get_setting('capability'),'fields-upgrade', array($this,'html') );

	}


	/*
	*  admin_notices
	*
	*  This function will render any admin notices
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function admin_notices() {

		// bail ealry if already on update page
		if( fieldmaster_is_screen('custom-fieldmaster_page_fields-upgrade') ) {

			return;

		}


		// view
		$view = array(
			'button_text'	=> __("Upgrade Database", 'fields'),
			'button_url'	=> admin_url('edit.php?post_type=fields-field-group&page=fields-upgrade')
		);


		// load view
		fieldmaster_get_view('update-notice', $view);

	}


	/*
	*  html
	*
	*  description
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function html() {

		// view
		$view = array(
			'updates'			=> fieldmaster_get_updates(),
			'plugin_version'	=> fieldmaster_get_setting('version')
		);


		// enqueue
		fieldmaster_enqueue_scripts();


		// load view
		fieldmaster_get_view('update', $view);

	}

	/*
	*  ajax_upgrade
	*
	*  description
	*
	*  @type	function
	*  @date	24/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function ajax_upgrade() {

   		// options
   		$options = wp_parse_args( $_POST, array(
			'nonce'		=> '',
			'blog_id'	=> '',
		));


		// validate
		if( !wp_verify_nonce($options['nonce'], 'fieldmaster_upgrade') ) {

			wp_send_json_error();

		}


		// switch blog
		if( $options['blog_id'] ) {

			switch_to_blog( $options['blog_id'] );

		}


		// vars
		$updates = fieldmaster_get_updates();
		$message = '';


		// bail early if no updates
		if( empty($updates) ) {

			wp_send_json_error(array(
				'message' => 'No updates available'
			));

		}

		// updates complete
		update_option('fieldmaster_version', fieldmaster_get_setting('version'));

		// return
		wp_send_json_success(array(
			'message' => $message
		));

	}

}

// initialize
new fieldmaster_admin_update();

endif;

?>
