<?php

/*
*  FieldMaster Password Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_password
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_password') ) :

class fieldmaster_field_password extends fieldmaster_field {
	
	
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
		$this->name = 'password';
		$this->label = __("Password",'fieldmaster');
		$this->defaults = array(
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> '',
			'readonly'		=> 0,
			'disabled'		=> 0,
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$atts = array();
		$o = array( 'type', 'id', 'class', 'name', 'value', 'placeholder' );
		$s = array( 'readonly', 'disabled' );
		$e = '';
		
		
		// prepend
		if( $field['prepend'] !== '' ) {
		
			$field['class'] .= ' fieldmaster-is-prepended';
			$e .= '<div class="fieldmaster-input-prepend">' . $field['prepend'] . '</div>';
			
		}
		
		
		// append
		if( $field['append'] !== '' ) {
		
			$field['class'] .= ' fieldmaster-is-appended';
			$e .= '<div class="fieldmaster-input-append">' . $field['append'] . '</div>';
			
		}
		
		
		// append atts
		foreach( $o as $k ) {
		
			$atts[ $k ] = $field[ $k ];	
			
		}
		
		
		// append special atts
		foreach( $s as $k ) {
		
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
			
		}
		
		
		// render
		$e .= '<div class="fieldmaster-input-wrap">';
		$e .= '<input ' . fieldmaster_esc_attr( $atts ) . ' />';
		$e .= '</div>';
		
		
		// return
		echo $e;
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// placeholder
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text','fieldmaster'),
			'instructions'	=> __('Appears within the input','fieldmaster'),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// prepend
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Prepend','fieldmaster'),
			'instructions'	=> __('Appears before the input','fieldmaster'),
			'type'			=> 'text',
			'name'			=> 'prepend',
		));
		
		
		// append
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Append','fieldmaster'),
			'instructions'	=> __('Appears after the input','fieldmaster'),
			'type'			=> 'text',
			'name'			=> 'append',
		));
	}
	
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_password() );

endif; // class_exists check

?>