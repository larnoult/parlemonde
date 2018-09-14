<?php

/*
*  FieldMaster Checkbox Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_checkbox
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_checkbox') ) :

class fieldmaster_field_checkbox extends fieldmaster_field {
	
	
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
		$this->name = 'checkbox';
		$this->label = __("Checkbox",'fieldmaster');
		$this->category = 'choice';
		$this->defaults = array(
			'layout'			=> 'vertical',
			'choices'			=> array(),
			'default_value'		=> '',
			'allow_custom'		=> 0,
			'save_custom'		=> 0,
			'toggle'			=> 0,
			'return_format'		=> 'value'
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
		
		// ensure array
		$field['value'] = fieldmaster_get_array($field['value'], false);
		$field['choices'] = fieldmaster_get_array($field['choices']);
		
		
		// hiden input
		fieldmaster_hidden_input( array('name' => $field['name']) );
		
		
		// vars
		$i = 0;
		$li = '';
		$all_checked = true;
		
		
		// checkbox saves an array
		$field['name'] .= '[]';
		
		
		// foreach choices
		if( !empty($field['choices']) ) {
			
			foreach( $field['choices'] as $value => $label ) {
				
				// increase counter
				$i++;
				
				
				// vars
				$atts = array(
					'type'	=> 'checkbox',
					'id'	=> $field['id'], 
					'name'	=> $field['name'],
					'value'	=> $value,
				);
				
				
				// is choice selected?
				if( in_array($value, $field['value']) ) {
					
					$atts['checked'] = 'checked';
					
				} else {
					
					$all_checked = false;
					
				}
				
				
				if( isset($field['disabled']) && fieldmaster_in_array($value, $field['disabled']) ) {
				
					$atts['disabled'] = 'disabled';
					
				}
				
				
				// each input ID is generated with the $key, however, the first input must not use $key so that it matches the field's label for attribute
				if( $i > 1 ) {
				
					$atts['id'] .= '-' . $value;
					
				}
				
				
				// append HTML
				$li .= '<li><label><input ' . fieldmaster_esc_attr( $atts ) . '/>' . $label . '</label></li>';
				
			}
			
			
			// toggle all
			if( $field['toggle'] ) {
				
				// vars
				$label = __("Toggle All", 'fieldmaster');
				$atts = array(
					'type'	=> 'checkbox',
					'class'	=> 'fieldmaster-checkbox-toggle'
				);
				
				
				// custom label
				if( is_string($field['toggle']) ) {
					
					$label = $field['toggle'];
					
				}
				
				
				// checked
				if( $all_checked ) {
					
					$atts['checked'] = 'checked';
					
				}
				
				
				// append HTML
				$li = '<li><label><input ' . fieldmaster_esc_attr( $atts ) . '/>' . $label . '</label></li>' . $li;
					
			}
		
		}
		
		
		// allow_custom
		if( $field['allow_custom'] ) {
			
			
			// loop
			foreach( $field['value'] as $value ) {
				
				// ignore if already eixsts
				if( isset($field['choices'][ $value ]) ) continue;
				
				
				// vars
				$atts = array(
					'type'	=> 'text',
					'name'	=> $field['name'],
					'value'	=> $value,
				);
				
				
				// append
				$li .= '<li><input class="fieldmaster-checkbox-custom" type="checkbox" checked="checked" /><input ' . fieldmaster_esc_attr( $atts ) . '/></li>';
				
			}
			
			
			// append button
			$li .= '<li><a href="#" class="button fieldmaster-add-checkbox">' . __('Add new choice', 'fieldmaster') . '</a></li>';
			
		}
		
		
		// class
		$field['class'] .= ' fieldmaster-checkbox-list';
		$field['class'] .= ($field['layout'] == 'horizontal') ? ' fieldmaster-hl' : ' fieldmaster-bl';

		
		// return
		echo '<ul ' . fieldmaster_esc_attr(array( 'class' => $field['class'] )) . '>' . $li . '</ul>';
		
		
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
		
		// encode choices (convert from array)
		$field['choices'] = fieldmaster_encode_choices($field['choices']);
		$field['default_value'] = fieldmaster_encode_choices($field['default_value'], false);
				
		
		// choices
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Choices','fieldmaster'),
			'instructions'	=> __('Enter each choice on a new line.','fieldmaster') . '<br /><br />' . __('For more control, you may specify both a value and label like this:','fieldmaster'). '<br /><br />' . __('red : Red','fieldmaster'),
			'type'			=> 'textarea',
			'name'			=> 'choices',
		));	
		
		
		// other_choice
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Allow Custom','fieldmaster'),
			'instructions'	=> '',
			'name'			=> 'allow_custom',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'message'		=> __("Allow 'custom' values to be added", 'fieldmaster'),
		));
		
		
		// save_other_choice
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Save Custom','fieldmaster'),
			'instructions'	=> '',
			'name'			=> 'save_custom',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'message'		=> __("Save 'custom' values to the field's choices", 'fieldmaster')
		));
		
		
		// default_value
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Default Value','fieldmaster'),
			'instructions'	=> __('Enter each default value on a new line','fieldmaster'),
			'type'			=> 'textarea',
			'name'			=> 'default_value',
		));
		
		
		// layout
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Layout','fieldmaster'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'layout',
			'layout'		=> 'horizontal', 
			'choices'		=> array(
				'vertical'		=> __("Vertical",'fieldmaster'), 
				'horizontal'	=> __("Horizontal",'fieldmaster')
			)
		));
		
		
		// layout
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Toggle','fieldmaster'),
			'instructions'	=> __('Prepend an extra checkbox to toggle all choices','fieldmaster'),
			'name'			=> 'toggle',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// return_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Return Value','fieldmaster'),
			'instructions'	=> __('Specify the returned value on front end','fieldmaster'),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'value'			=> __('Value','fieldmaster'),
				'label'			=> __('Label','fieldmaster'),
				'array'			=> __('Both (Array)','fieldmaster')
			)
		));		
		
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = fieldmaster)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field ) {
		
		return fieldmaster_get_field_type('select')->update_field( $field );
		
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// bail early if is empty
		if( empty($value) ) return $value;
		
		
		// select -> update_value()
		$value = fieldmaster_get_field_type('select')->update_value( $value, $post_id, $field );
		
		
		// save_other_choice
		if( $field['save_custom'] ) {
			
			// get raw $field (may have been changed via repeater field)
			// if field is local, it won't have an ID
			$selector = $field['ID'] ? $field['ID'] : $field['key'];
			$field = fieldmaster_get_field( $selector, true );
			
			
			// bail early if no ID (JSON only)
			if( !$field['ID'] ) return $value;
			
			
			// loop
			foreach( $value as $v ) {
				
				// ignore if already eixsts
				if( isset($field['choices'][ $v ]) ) continue;
				
				
				// unslash (fixes serialize single quote issue)
				$v = wp_unslash($v);
				
				
				// append
				$field['choices'][ $v ] = $v;
				
			}
			
			
			// save
			fieldmaster_update_field( $field );
			
		}		
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  translate_field
	*
	*  This function will translate field settings
	*
	*  @type	function
	*  @date	8/03/2016
	*  @since	5.3.2
	*
	*  @param	$field (array)
	*  @return	$field
	*/
	
	function translate_field( $field ) {
		
		return fieldmaster_get_field_type('select')->translate_field( $field );
		
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
		
		return fieldmaster_get_field_type('select')->format_value( $value, $post_id, $field );
		
	}
	
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_checkbox() );

endif; // class_exists check

?>