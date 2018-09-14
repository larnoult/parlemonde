<?php

/*
*  FieldMaster Time Picker Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_time_picker
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_time_picker') ) :

class fieldmaster_field_time_picker extends fieldmaster_field {
	
	
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
		$this->name = 'time_picker';
		$this->label = __("Time Picker",'fieldmaster');
		$this->category = 'jquery';
		$this->defaults = array(
			'display_format'		=> 'g:i a',
			'return_format'			=> 'g:i a'
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
		
		// format value
		$display_value = '';
		
		if( $field['value'] ) {
			
			$display_value = fieldmaster_format_date( $field['value'], $field['display_format'] );
			
		}
		
		
		// vars
		$e = '';
		$div = array(
			'class'					=> 'fieldmaster-time-picker fieldmaster-input-wrap',
			'data-time_format'		=> fieldmaster_convert_time_to_js($field['display_format'])
		);
		$hidden = array(
			'id'					=> $field['id'],
			'class' 				=> 'input-alt',
			'type'					=> 'hidden',
			'name'					=> $field['name'],
			'value'					=> $field['value'],
		);
		$input = array(
			'class' 				=> 'input',
			'type'					=> 'text',
			'value'					=> $display_value,
		);
		
		
		// html
		$e .= '<div ' . fieldmaster_esc_attr($div) . '>';
			$e .= '<input ' . fieldmaster_esc_attr($hidden). '/>';
			$e .= '<input ' . fieldmaster_esc_attr($input). '/>';
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
		
		// vars
		$g_i_a = date('g:i a');
		$H_i_s = date('H:i:s');
		
		
		// display_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Display Format','fieldmaster'),
			'instructions'	=> __('The format displayed when editing a post','fieldmaster'),
			'type'			=> 'radio',
			'name'			=> 'display_format',
			'other_choice'	=> 1,
			'choices'		=> array(
				'g:i a'	=> '<span>' . $g_i_a . '</span><code>g:i a</code>',
				'H:i:s'	=> '<span>' . $H_i_s . '</span><code>H:i:s</code>',
				'other'	=> '<span>' . __('Custom:','fieldmaster') . '</span>'
			)
		));
				
		
		// return_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Return Format','fieldmaster'),
			'instructions'	=> __('The format returned via template functions','fieldmaster'),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'other_choice'	=> 1,
			'choices'		=> array(
				'g:i a'	=> '<span>' . $g_i_a . '</span><code>g:i a</code>',
				'H:i:s'	=> '<span>' . $H_i_s . '</span><code>H:i:s</code>',
				'other'	=> '<span>' . __('Custom:','fieldmaster') . '</span>'
			)
		));
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		return fieldmaster_format_date( $value, $field['return_format'] );
		
	}
	
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_time_picker() );

endif; // class_exists check

?>