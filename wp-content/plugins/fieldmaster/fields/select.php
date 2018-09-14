<?php

/*
*  FieldMaster Select Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_select
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_select') ) :

class fieldmaster_field_select extends fieldmaster_field {
	
	
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
		$this->name = 'select';
		$this->label = _x('Select', 'noun', 'fieldmaster');
		$this->category = 'choice';
		$this->defaults = array(
			'multiple' 		=> 0,
			'allow_null' 	=> 0,
			'choices'		=> array(),
			'default_value'	=> '',
			'ui'			=> 0,
			'ajax'			=> 0,
			'placeholder'	=> '',
			'return_format'	=> 'value'
		);
		$this->l10n = array(
			'matches_1'				=> _x('One result is available, press enter to select it.',	'Select2 JS matches_1',	'fieldmaster'),
			'matches_n'				=> _x('%d results are available, use up and down arrow keys to navigate.',	'Select2 JS matches_n',	'fieldmaster'),
			'matches_0'				=> _x('No matches found',	'Select2 JS matches_0',	'fieldmaster'),
			'input_too_short_1'		=> _x('Please enter 1 or more characters', 'Select2 JS input_too_short_1', 'fieldmaster' ),
			'input_too_short_n'		=> _x('Please enter %d or more characters', 'Select2 JS input_too_short_n', 'fieldmaster' ),
			'input_too_long_1'		=> _x('Please delete 1 character', 'Select2 JS input_too_long_1', 'fieldmaster' ),
			'input_too_long_n'		=> _x('Please delete %d characters', 'Select2 JS input_too_long_n', 'fieldmaster' ),
			'selection_too_long_1'	=> _x('You can only select 1 item', 'Select2 JS selection_too_long_1', 'fieldmaster' ),
			'selection_too_long_n'	=> _x('You can only select %d items', 'Select2 JS selection_too_long_n', 'fieldmaster' ),
			'load_more'				=> _x('Loading more results&hellip;', 'Select2 JS load_more', 'fieldmaster' ),
			'searching'				=> _x('Searching&hellip;', 'Select2 JS searching', 'fieldmaster' ),
			'load_fail'           	=> _x('Loading failed', 'Select2 JS load_fail', 'fieldmaster' ),
		);
		
		
		// ajax
		add_action('wp_ajax_fieldmaster/fields/select/query',				array($this, 'ajax_query'));
		add_action('wp_ajax_nopriv_fieldmaster/fields/select/query',		array($this, 'ajax_query'));
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		// bail ealry if no enqueue
	   	if( !fieldmaster_get_setting('enqueue_select2') ) return;
	   	
	   	
		// globals
		global $wp_scripts, $wp_styles;
		
		
		// vars
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$major = fieldmaster_get_setting('select2_version');
		$version = '';
		$script = '';
		$style = '';
		
		
		// attempt to find 3rd party Select2 version
		// - avoid including v3 CSS when v4 JS is already enququed
		if( isset($wp_scripts->registered['select2']) ) {
			
			$major = (int) $wp_scripts->registered['select2']->ver;
		
		}
		
		
		// v4
		if( $major == 4 ) {
			
			$version = '4.0';
			$script = fieldmaster_get_dir("assets/inc/select2/4/select2.full{$min}.js");
			$style = fieldmaster_get_dir("assets/inc/select2/4/select2{$min}.css");
		
		// v3
		} else {
			
			$version = '3.5.2';
			$script = fieldmaster_get_dir("assets/inc/select2/3/select2{$min}.js");
			$style = fieldmaster_get_dir("assets/inc/select2/3/select2.css");
			
		}
		
		
		// enqueue
		wp_enqueue_script('select2', $script, array('jquery'), $version );
		wp_enqueue_style('select2', $style, '', $version );
		
	}
	
	
	/*
	*  ajax_query
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
	
	function ajax_query() {
		
		// validate
		if( !fieldmaster_verify_ajax() ) die();
		
		
		// get choices
		$response = $this->get_ajax_query( $_POST );
		
		
		// return
		fieldmaster_send_ajax_results($response);
			
	}
	
	
	/*
	*  get_ajax_query
	*
	*  This function will return an array of data formatted for use in a select2 AJAX response
	*
	*  @type	function
	*  @date	15/10/2014
	*  @since	5.0.9
	*
	*  @param	$options (array)
	*  @return	(array)
	*/
	
	function get_ajax_query( $options = array() ) {
		
   		// defaults
   		$options = fieldmaster_parse_args($options, array(
			'post_id'		=> 0,
			's'				=> '',
			'field_key'		=> '',
			'paged'			=> 1
		));
		
		
		// load field
		$field = fieldmaster_get_field( $options['field_key'] );
		if( !$field ) return false;
		
		
		// vars
   		$results = array();
   		$s = false;
   		$is_search = false;
   		
   		
   		// search
		if( $options['s'] !== '' ) {
			
			// strip slashes (search may be integer)
			$s = wp_unslash( strval($options['s']) );
			
			
			// update vars
			$is_search = true;
			
		}
		
		
		// bail ealry if no choices
		if( empty($field['choices']) ) return false;
		
		
		// loop 
		foreach( $field['choices'] as $k => $v ) {
			
			// ensure $v is a string
			$v = strval( $v );
			
			
			// if searching, but doesn't exist
			if( $is_search && stripos($v, $s) === false ) continue;
			
			
			// append
			$results[] = array(
				'id'	=> $k,
				'text'	=> $v
			);
			
		}
		
		
		// vars
		$response = array(
			'results'	=> $results
		);
		
		
		// return
		return $response;
			
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
		
		// convert
		$field['value'] = fieldmaster_get_array($field['value'], false);
		$field['choices'] = fieldmaster_get_array($field['choices']);
		
		
		// placeholder
		if( empty($field['placeholder']) ) {
		
			$field['placeholder'] = _x('Select', 'verb', 'fieldmaster');
			
		}
		
		
		// add empty value (allows '' to be selected)
		if( empty($field['value']) ) {
			
			$field['value'] = array('');
			
		}
		
		
		// allow null
		// - have tried array_merge but this causes keys to re-index if is numeric (post ID's)
		if( $field['allow_null'] && !$field['multiple'] ) {
			
			$prepend = array(''	=> '- ' . $field['placeholder'] . ' -');
			$field['choices'] = $prepend + $field['choices'];
			
		}
		
		
		// vars
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> $field['class'],
			'name'				=> $field['name'],
			'data-ui'			=> $field['ui'],
			'data-ajax'			=> $field['ajax'],
			'data-multiple'		=> $field['multiple'],
			'data-placeholder'	=> $field['placeholder'],
			'data-allow_null'	=> $field['allow_null']
		);
		
		
		// multiple
		if( $field['multiple'] ) {
		
			$atts['multiple'] = 'multiple';
			$atts['size'] = 5;
			$atts['name'] .= '[]';
			
		}
		
		
		// special atts
		foreach( array( 'readonly', 'disabled' ) as $k ) {
		
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
			
		}
		
		
		// custom  ajax action
		if( !empty($field['ajax_action']) ) {
			
			$atts['data-ajax_action'] = $field['ajax_action'];
			
		}
		
		
		// hidden input
		if( $field['ui'] ) {
			
			$v = $field['value'];
			
			if( $field['multiple'] ) {
				
				$v = implode('||', $v);
				
			} else {
				
				$v = fieldmaster_maybe_get($v, 0, '');
				
			}
			
			fieldmaster_hidden_input(array(
				'id'	=> $field['id'] . '-input',
				'name'	=> $field['name'],
				'value'	=> $v
			));
			
		} elseif( $field['multiple'] ) {
			
			fieldmaster_hidden_input(array(
				'id'	=> $field['id'] . '-input',
				'name'	=> $field['name']
			));
			
		}
		
		
		
		// open
		echo '<select ' . fieldmaster_esc_attr($atts) . '>';	
		
		
		// walk
		$this->walk( $field['choices'], $field['value'] );
		
		
		// close
		echo '</select>';
		
	}
	
	
	/*
	*  walk
	*
	*  description
	*
	*  @type	function
	*  @date	22/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function walk( $choices, $values ) {
		
		// bail ealry if no choices
		if( empty($choices) ) return;
		
		
		// loop
		foreach( $choices as $k => $v ) {
			
			// optgroup
			if( is_array($v) ){
				
				// optgroup
				echo '<optgroup label="' . esc_attr($k) . '">';
				
				
				// walk
				$this->walk( $v, $values );
				
				
				// close optgroup
				echo '</optgroup>';
				
				
				// break
				continue;
				
			}
			
			
			// vars
			$search = html_entity_decode($k);
			$pos = array_search($search, $values);
			$atts = array( 'value' => $k );
			
			
			// validate selected
			if( $pos !== false ) {
				
				$atts['selected'] = 'selected';
				$atts['data-i'] = $pos;
				
			}
			
			
			// option
			echo '<option ' . fieldmaster_esc_attr($atts) . '>' . $v . '</option>';
			
		}
		
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
			'name'			=> 'choices',
			'type'			=> 'textarea',
		));	
		
		
		// default_value
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Default Value','fieldmaster'),
			'instructions'	=> __('Enter each default value on a new line','fieldmaster'),
			'name'			=> 'default_value',
			'type'			=> 'textarea',
		));
		
		
		// allow_null
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','fieldmaster'),
			'instructions'	=> '',
			'name'			=> 'allow_null',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// multiple
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Select multiple values?','fieldmaster'),
			'instructions'	=> '',
			'name'			=> 'multiple',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// ui
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Stylised UI','fieldmaster'),
			'instructions'	=> '',
			'name'			=> 'ui',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
				
		
		// ajax
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Use AJAX to lazy load choices?','fieldmaster'),
			'instructions'	=> '',
			'name'			=> 'ajax',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// return_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Return Format','fieldmaster'),
			'instructions'	=> __('Specify the value returned','fieldmaster'),
			'type'			=> 'select',
			'name'			=> 'return_format',
			'choices'		=> array(
				'value'			=> __('Value','fieldmaster'),
				'label'			=> __('Label','fieldmaster'),
				'array'			=> __('Both (Array)','fieldmaster')
			)
		));
			
	}
	
	
	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	function load_value( $value, $post_id, $field ) {
		
		// FieldMaster4 null
		if( $value === 'null' ) return false;
		
		
		// return
		return $value;
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
		
		// decode choices (convert to array)
		$field['choices'] = fieldmaster_decode_choices($field['choices']);
		$field['default_value'] = fieldmaster_decode_choices($field['default_value'], true);
		
		
		// return
		return $field;
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
		
		// validate
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// array
		if( is_array($value) ) {
			
			// save value as strings, so we can clearly search for them in SQL LIKE statements
			$value = array_map('strval', $value);
			
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
		
		// translate
		$field['choices'] = fieldmaster_translate( $field['choices'] );
		
		
		// return
		return $field;
		
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
		
		// array
		if( fieldmaster_is_array($value) ) {
			
			foreach( $value as $i => $v ) {
				
				$value[ $i ] = $this->format_value_single( $v, $post_id, $field );
				
			}
			
		} else {
			
			$value = $this->format_value_single( $value, $post_id, $field );
			
		}
		
		
		// return
		return $value;
		
	}
	
	
	function format_value_single( $value, $post_id, $field ) {
		
		// bail ealry if is empty
		if( fieldmaster_is_empty($value) ) return $value;
		
		
		// vars
		$label = fieldmaster_maybe_get($field['choices'], $value, $value);
		
		
		// value
		if( $field['return_format'] == 'value' ) {
			
			// do nothing
		
		// label	
		} elseif( $field['return_format'] == 'label' ) {
			
			$value = $label;
		
		// array	
		} elseif( $field['return_format'] == 'array' ) {
			
			$value = array(
				'value'	=> $value,
				'label'	=> $label
			);
			
		}
		
		
		// return
		return $value;
		
	}
	
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_select() );

endif; // class_exists check

?>