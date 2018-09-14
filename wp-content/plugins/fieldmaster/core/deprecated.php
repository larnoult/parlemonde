<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fieldmaster_deprecated') ) :

class fieldmaster_deprecated {
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// settings
		add_filter('fieldmaster/settings/show_admin',			array($this, 'fieldmaster_settings_show_admin'), 5, 1);				// 5.0.0
		add_filter('fieldmaster/settings/l10n_textdomain',		array($this, 'fieldmaster_settings_l10n_textdomain'), 5, 1);		// 5.3.3
		add_filter('fieldmaster/settings/l10n_field',			array($this, 'fieldmaster_settings_l10n_field'), 5, 1);				// 5.3.3
		add_filter('fieldmaster/settings/l10n_field_group',		array($this, 'fieldmaster_settings_l10n_field'), 5, 1);				// 5.3.3
		
		
		// filters
		add_filter('fieldmaster/validate_field', 				array($this, 'fieldmaster_validate_field'), 10, 1); 				// 5.5.6
		add_filter('fieldmaster/validate_field_group', 			array($this, 'fieldmaster_validate_field_group'), 10, 1); 			// 5.5.6
		add_filter('fieldmaster/validate_post_id', 				array($this, 'fieldmaster_validate_post_id'), 10, 2); 			// 5.5.6
		
	}
	
	
	/*
	*  fieldmaster_settings_show_admin
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function fieldmaster_settings_show_admin( $setting ) {
		
		// 5.0.0 - removed FieldMaster_LITE
		return ( defined('FieldMaster_LITE') && FieldMaster_LITE ) ? false : $setting;
		
	}
	
	
	/*
	*  fieldmaster_settings_l10n_textdomain
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function fieldmaster_settings_l10n_textdomain( $setting ) {
		
		// 5.3.3 - changed filter name
		return fieldmaster_get_setting( 'export_textdomain', $setting );
		
	}
	
	
	/*
	*  fieldmaster_settings_l10n_field
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function fieldmaster_settings_l10n_field( $setting ) {
		
		// 5.3.3 - changed filter name
		return fieldmaster_get_setting( 'export_translate', $setting );
		
	}
	
	
	/*
	*  fieldmaster_validate_field
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function fieldmaster_validate_field( $field ) {
		
		// 5.5.6 - changed filter name
		$field = apply_filters( "fieldmaster/get_valid_field", $field );
		$field = apply_filters( "fieldmaster/get_valid_field/type={$field['type']}", $field );
		
		
		// return
		return $field;
		
	}
	
	
	/*
	*  fieldmaster_validate_field_group
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function fieldmaster_validate_field_group( $field_group ) {
		
		// 5.5.6 - changed filter name
		$field_group = apply_filters('fieldmaster/get_valid_field_group', $field_group);
		
		
		// return
		return $field_group;
		
	}
	
	
	/*
	*  fieldmaster_validate_post_id
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	6/2/17
	*  @since	5.5.6
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function fieldmaster_validate_post_id( $post_id, $_post_id ) {
		
		// 5.5.6 - changed filter name
		$post_id = apply_filters('fieldmaster/get_valid_post_id', $post_id, $_post_id);
		
		
		// return
		return $post_id;
		
	}
	
}


// initialize
fieldmaster()->deprecated = new fieldmaster_deprecated();

endif; // class_exists check

?>