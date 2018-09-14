<?php
/*
Plugin Name: FieldMaster
Plugin URI: https://goldhat.ca
Description: FieldMaster for create field interfaces and data storage in WordPress.
Version: 6.0.1
Author: GoldHat Group
Author URI: https://goldhat.ca
Copyright: GoldHat Group, Elliot Condon
Text Domain: fieldmaster
Domain Path: /lang
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fieldmaster') ) :

class fieldmaster {

	// vars
	var $version = '6.0.1';


	/*
	*  __construct
	*
	*  A dummy constructor to ensure FieldMaster is only initialized once
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct() {

		/* Do nothing here */

	}


	/*
	*  initialize
	*
	*  The real constructor to initialize FieldMaster
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function initialize() {

		// vars
		$this->settings = array(

			// basic
			'name'				=> __('FieldMaster', 'fieldmaster'),
			'version'			=> $this->version,

			// urls
			'file'				=> __FILE__,
			'basename'			=> plugin_basename( __FILE__ ),
			'path'				=> plugin_dir_path( __FILE__ ),
			'dir'				=> plugin_dir_url( __FILE__ ),

			// options
			'show_admin'				=> true,
			'show_updates'				=> true,
			'stripslashes'				=> false,
			'local'						=> true,
			'json'						=> true,
			'save_json'					=> '',
			'load_json'					=> array(),
			'default_language'			=> '',
			'current_language'			=> '',
			'capability'				=> 'manage_options',
			'uploader'					=> 'wp',
			'autoload'					=> false,
			'l10n'						=> true,
			'l10n_textdomain'			=> '',
			'google_api_key'			=> '',
			'google_api_client'			=> '',
			'enqueue_google_maps'		=> true,
			'enqueue_select2'			=> true,
			'enqueue_datepicker'		=> true,
			'enqueue_datetimepicker'	=> true,
			'select2_version'			=> 3,
			'row_index_offset'			=> 1,
			'remove_wp_meta_box'		=> false // todo: set to true in 5.6.0
		);


		// constants
		$this->define( 'FieldMaster', 			true );
		$this->define( 'FieldMaster_VERSION', 	$this->settings['version'] );
		$this->define( 'FieldMaster_PATH', 		$this->settings['path'] );


		// include helpers
		include_once( FieldMaster_PATH . 'api/api-helpers.php');


		// api
		fieldmaster_include('api/api-value.php');
		fieldmaster_include('api/api-field.php');
		fieldmaster_include('api/api-field-group.php');
		fieldmaster_include('api/api-template.php');
		fieldmaster_include('api/api-options-page.php');

		// core
		fieldmaster_include('core/ajax.php');
		fieldmaster_include('core/cache.php');
		fieldmaster_include('core/compatibility.php');
		fieldmaster_include('core/deprecated.php');
		fieldmaster_include('core/field.php');
		fieldmaster_include('core/fields.php');
		fieldmaster_include('core/form.php');
		fieldmaster_include('core/input.php');
		fieldmaster_include('core/validation.php');
		fieldmaster_include('core/json.php');
		fieldmaster_include('core/local.php');
		fieldmaster_include('core/location.php');
		fieldmaster_include('core/loop.php');
		fieldmaster_include('core/media.php');
		fieldmaster_include('core/revisions.php');
		fieldmaster_include('core/third_party.php');


		// forms
		fieldmaster_include('forms/attachment.php');
		fieldmaster_include('forms/comment.php');
		fieldmaster_include('forms/post.php');
		fieldmaster_include('forms/taxonomy.php');
		fieldmaster_include('forms/user.php');
		fieldmaster_include('forms/widget.php');


		// admin
		if( is_admin() ) {

			fieldmaster_include('admin/admin.php');
			fieldmaster_include('admin/field-group.php');
			fieldmaster_include('admin/field-groups.php');
			fieldmaster_include('admin/install.php');
			fieldmaster_include('admin/settings-tools.php');
			fieldmaster_include('admin/settings-info.php');
			fieldmaster_include('admin/options-page.php');
			fieldmaster_include('admin/settings-updates.php');



			// network
			if( is_network_admin() ) {

				fieldmaster_include('admin/install-network.php');

			}
		}

		// actions
		add_action('init',	array($this, 'init'), 5);
		add_action('init',	array($this, 'register_post_types'), 5);
		add_action('init',	array($this, 'register_post_status'), 5);
		add_action('init',	array($this, 'register_assets'), 5);
		add_action('fieldmaster/input/admin_enqueue_scripts',			array($this, 'input_admin_enqueue_scripts'));
		add_action('fieldmaster/field_group/admin_enqueue_scripts',		array($this, 'field_group_admin_enqueue_scripts'));


		// filters
		add_filter('posts_where',		array($this, 'posts_where'), 10, 2 );

	}


	/*
	*  init
	*
	*  This function will run after all plugins and theme functions have been included
	*
	*  @type	action (init)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function init() {

		// bail early if too early
		// ensures all plugins have a chance to add fields, etc
		if( !did_action('plugins_loaded') ) return;


		// bail early if already init
		if( fieldmaster_has_done('init') ) return;


		// vars
		$major = intval( fieldmaster_get_setting('version') );


		// redeclare dir
		// - allow another plugin to modify dir (maybe force SSL)
		fieldmaster_update_setting('dir', plugin_dir_url( __FILE__ ));


		// textdomain
		$this->load_plugin_textdomain();


		// include wpml support
		if( defined('ICL_SITEPRESS_VERSION') ) fieldmaster_include('core/wpml.php');


		// field types
		fieldmaster_include('fields/text.php');
		fieldmaster_include('fields/textarea.php');
		fieldmaster_include('fields/number.php');
		fieldmaster_include('fields/email.php');
		fieldmaster_include('fields/url.php');
		fieldmaster_include('fields/password.php');
		fieldmaster_include('fields/wysiwyg.php');
		fieldmaster_include('fields/oembed.php');
		fieldmaster_include('fields/image.php');
		fieldmaster_include('fields/file.php');
		fieldmaster_include('fields/select.php');
		fieldmaster_include('fields/checkbox.php');
		fieldmaster_include('fields/radio.php');
		fieldmaster_include('fields/true_false.php');
		fieldmaster_include('fields/post_object.php');
		fieldmaster_include('fields/page_link.php');
		fieldmaster_include('fields/relationship.php');
		fieldmaster_include('fields/taxonomy.php');
		fieldmaster_include('fields/user.php');
		fieldmaster_include('fields/google-map.php');
		fieldmaster_include('fields/date_picker.php');
		fieldmaster_include('fields/date_time_picker.php');
		fieldmaster_include('fields/time_picker.php');
		fieldmaster_include('fields/color_picker.php');
		fieldmaster_include('fields/message.php');
		fieldmaster_include('fields/tab.php');
		fieldmaster_include('fields/repeater.php');
		fieldmaster_include('fields/flexible-content.php');
		fieldmaster_include('fields/gallery.php');
		fieldmaster_include('fields/clone.php');


		// 3rd party field types
		do_action('fieldmaster/include_field_types', $major);


		// local fields
		do_action('fieldmaster/include_fields', $major);


		// action for 3rd party
		do_action('fieldmaster/init');

	}


	/*
	*  load_plugin_textdomain
	*
	*  This function will load the textdomain file
	*
	*  @type	function
	*  @date	3/5/17
	*  @since	5.5.13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function load_plugin_textdomain() {

		// vars
		$domain = 'fieldmaster';
		$locale = apply_filters( 'plugin_locale', fieldmaster_get_locale(), $domain );
		$mofile = $domain . '-' . $locale . '.mo';


		// load from the languages directory first
		load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile );


		// load from plugin lang folder
		load_textdomain( $domain, fieldmaster_get_path( 'lang/' . $mofile ) );

	}


	/*
	*  register_post_types
	*
	*  This function will register post types and statuses
	*
	*  @type	function
	*  @date	22/10/2015
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function register_post_types() {

		// vars
		$cap = fieldmaster_get_setting('capability');


		// register post type 'fm-field-group'
		register_post_type('fm-field-group', array(
			'labels'			=> array(
			    'name'					=> __( 'Field Groups', 'fieldmaster' ),
				'singular_name'			=> __( 'Field Group', 'fieldmaster' ),
			    'add_new'				=> __( 'Add New' , 'fieldmaster' ),
			    'add_new_item'			=> __( 'Add New Field Group' , 'fieldmaster' ),
			    'edit_item'				=> __( 'Edit Field Group' , 'fieldmaster' ),
			    'new_item'				=> __( 'New Field Group' , 'fieldmaster' ),
			    'view_item'				=> __( 'View Field Group', 'fieldmaster' ),
			    'search_items'			=> __( 'Search Field Groups', 'fieldmaster' ),
			    'not_found'				=> __( 'No Field Groups found', 'fieldmaster' ),
			    'not_found_in_trash'	=> __( 'No Field Groups found in Trash', 'fieldmaster' ),
			),
			'public'			=> false,
			'show_ui'			=> true,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array('title'),
			'show_in_menu'		=> false,
		));


		// register post type 'fm-field'
		register_post_type('fm-field', array(
			'labels'			=> array(
			    'name'					=> __( 'Fields', 'fieldmaster' ),
				'singular_name'			=> __( 'Field', 'fieldmaster' ),
			    'add_new'				=> __( 'Add New' , 'fieldmaster' ),
			    'add_new_item'			=> __( 'Add New Field' , 'fieldmaster' ),
			    'edit_item'				=> __( 'Edit Field' , 'fieldmaster' ),
			    'new_item'				=> __( 'New Field' , 'fieldmaster' ),
			    'view_item'				=> __( 'View Field', 'fieldmaster' ),
			    'search_items'			=> __( 'Search Fields', 'fieldmaster' ),
			    'not_found'				=> __( 'No Fields found', 'fieldmaster' ),
			    'not_found_in_trash'	=> __( 'No Fields found in Trash', 'fieldmaster' ),
			),
			'public'			=> false,
			'show_ui'			=> false,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array('title'),
			'show_in_menu'		=> false,
		));

	}


	/*
	*  register_post_status
	*
	*  This function will register custom post statuses
	*
	*  @type	function
	*  @date	22/10/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function register_post_status() {

		// fieldmaster-disabled
		register_post_status('fieldmaster-disabled', array(
			'label'                     => __( 'Inactive', 'fieldmaster' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'fieldmaster' ),
		));

	}


	/*
	*  register_assets
	*
	*  This function will register scripts and styles
	*
	*  @type	function
	*  @date	22/10/2015
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function register_assets() {

		// vars
		$version = fieldmaster_get_setting('version');
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		// scripts
		wp_register_script('fieldmaster-input', fieldmaster_get_dir("assets/js/fieldmaster-input{$min}.js"), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-resizable'), $version );
		wp_register_script('fieldmaster-field-group', fieldmaster_get_dir("assets/js/fieldmaster-field-group{$min}.js"), array('fieldmaster-input'), $version );
		wp_register_script('fieldmaster-pro-input', fieldmaster_get_dir( "assets/js/fieldmaster-pro-input{$min}.js" ), array('fieldmaster-input'), $version );
		wp_register_script('fieldmaster-pro-field-group', fieldmaster_get_dir( "assets/js/fieldmaster-pro-field-group{$min}.js" ), array('fieldmaster-field-group'), $version );

		// styles
		wp_register_style('fieldmaster-global', fieldmaster_get_dir('assets/css/fieldmaster-global.css'), array(), $version );
		wp_register_style('fieldmaster-input', fieldmaster_get_dir('assets/css/fieldmaster-input.css'), array('fieldmaster-global'), $version );
		wp_register_style('fieldmaster-field-group', fieldmaster_get_dir('assets/css/fieldmaster-field-group.css'), array('fieldmaster-input'), $version );
		wp_register_style('fieldmaster-pro-input', fieldmaster_get_dir( 'assets/css/fieldmaster-pro-input.css' ), array('fieldmaster-input'), $version );
		wp_register_style('fieldmaster-pro-field-group', fieldmaster_get_dir( 'assets/css/fieldmaster-pro-field-group.css' ), array('fieldmaster-input'), $version );

	}

	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function input_admin_enqueue_scripts() {

		wp_enqueue_script('fieldmaster-pro-input');
		wp_enqueue_style('fieldmaster-pro-input');

	}

	/*
	*  field_group_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function field_group_admin_enqueue_scripts() {

		wp_enqueue_script('fieldmaster-pro-field-group');
		wp_enqueue_style('fieldmaster-pro-field-group');

	}


	/*
	*  posts_where
	*
	*  This function will add in some new parameters to the WP_Query args allowing fields to be found via key / name
	*
	*  @type	filter
	*  @date	5/12/2013
	*  @since	5.0.0
	*
	*  @param	$where (string)
	*  @param	$wp_query (object)
	*  @return	$where (string)
	*/

	function posts_where( $where, $wp_query ) {

		// global
		global $wpdb;


		// fieldmaster_field_key
		if( $field_key = $wp_query->get('fieldmaster_field_key') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key );

	    }


	    // fieldmaster_field_name
	    if( $field_name = $wp_query->get('fieldmaster_field_name') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name );

	    }


	    // fieldmaster_group_key
		if( $group_key = $wp_query->get('fieldmaster_group_key') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key );

	    }


	    // return
	    return $where;

	}


	/*
	*  define
	*
	*  This function will safely define a constant
	*
	*  @type	function
	*  @date	3/5/17
	*  @since	5.5.13
	*
	*  @param	$name (string)
	*  @param	$value (mixed)
	*  @return	n/a
	*/

	function define( $name, $value = true ) {

		if( !defined($name) ) define( $name, $value );

	}


	/*
	*  get_setting
	*
	*  This function will return a value from the settings array found in the fieldmaster object
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$name (string) the setting name to return
	*  @param	$value (mixed) default value
	*  @return	$value
	*/

	function get_setting( $name, $value = null ) {

		// check settings
		if( isset($this->settings[ $name ]) ) {

			$value = $this->settings[ $name ];

		}


		// filter for 3rd party customization
		if( substr($name, 0, 1) !== '_' ) {

			$value = apply_filters( "fieldmaster/settings/{$name}", $value );

		}


		// return
		return $value;

	}


	/*
	*  update_setting
	*
	*  This function will update a value into the settings array found in the fieldmaster object
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$name (string)
	*  @param	$value (mixed)
	*  @return	n/a
	*/

	function update_setting( $name, $value ) {

		$this->settings[ $name ] = $value;

		return true;

	}

}


/*
*  fieldmaster
*
*  The main function responsible for returning the one true fieldmaster Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php $fieldmaster = fieldmaster(); ?>
*
*  @type	function
*  @date	4/09/13
*  @since	4.3.0
*
*  @param	N/A
*  @return	(object)
*/

function fieldmaster() {

	global $fieldmaster;

	if( !isset($fieldmaster) ) {

		$fieldmaster = new fieldmaster();

		$fieldmaster->initialize();

	}

	return $fieldmaster;

}


// initialize
fieldmaster();


endif; // class_exists check

?>
