<?php

/*
*  FieldMaster AJAX Class
*
*  All the logic for misc AJAX functionality
*
*  @class 		fieldmaster_ajax
*  @package		FieldMaster
*  @subpackage	Core
*/

if( ! class_exists('fieldmaster_ajax') ) :

class fieldmaster_ajax {
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// fieldmaster/update_user_setting
		add_action( 'wp_ajax_fieldmaster/update_user_setting',			array($this, 'update_user_setting') );
		add_action( 'wp_ajax_nopriv_fieldmaster/update_user_setting',	array($this, 'update_user_setting') );
		
	}
	
	
	/*
	*  update_user_setting
	*
	*  This function will update a user setting
	*
	*  @type	function
	*  @date	15/07/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function update_user_setting() {
		
		// options
		$options = wp_parse_args( $_POST, array(
			'name'		=> '',
			'value'		=> '',
			'nonce'		=> '',
		));
		
		
		// validate
		if( ! wp_verify_nonce($options['nonce'], 'fieldmaster_nonce') || empty($options['name']) ) {
		
			die('0');
			
		}
		
		
		// upadte setting
		fieldmaster_update_user_setting( $options['name'], $options['value'] );
		
		
		// return
		die('1');
		
	}
	
}

new fieldmaster_ajax();

endif;

?>