<?php

/*
*  FieldMaster Output Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_output
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_output') ) :

class fieldmaster_field_output extends fieldmaster_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->name = 'output';
		$this->label = 'output';
		$this->public = false;
		$this->defaults = array(
			'html'	=> false
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// bail early if no html
		if( !$field['html'] ) return;
		
		
		// html
		if( is_string($field['html']) && !function_exists($field['html']) ) {
			
			echo $field['html'];
		
		// function	
		} else {
			
			call_user_func_array($field['html'], array($field));
			
		}
		
	}
		
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_output() );

endif; // class_exists check

?>