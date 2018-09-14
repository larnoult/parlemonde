<?php

/*
*  FieldMaster Date Time Picker Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_date_and_time_picker
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_date_and_time_picker') ) :

class fieldmaster_field_date_and_time_picker extends fieldmaster_field {
	
	
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
		$this->name = 'date_time_picker';
		$this->label = __("Date Time Picker",'fieldmaster');
		$this->category = 'jquery';
		$this->defaults = array(
			'display_format'	=> 'd/m/Y g:i a',
			'return_format'		=> 'd/m/Y g:i a',
			'first_day'			=> 1
		);
		$this->l10n = array(
			'timeOnlyTitle'		=> _x('Choose Time',	'Date Time Picker JS timeOnlyTitle',	'fieldmaster'),
	        'timeText'       	=> _x('Time',			'Date Time Picker JS timeText', 		'fieldmaster'),
	        'hourText'        	=> _x('Hour',			'Date Time Picker JS hourText', 		'fieldmaster'),
	        'minuteText'  		=> _x('Minute',			'Date Time Picker JS minuteText', 		'fieldmaster'),
	        'secondText'		=> _x('Second',			'Date Time Picker JS secondText', 		'fieldmaster'),
	        'millisecText'		=> _x('Millisecond',	'Date Time Picker JS millisecText', 	'fieldmaster'),
	        'microsecText'		=> _x('Microsecond',	'Date Time Picker JS microsecText', 	'fieldmaster'),
	        'timezoneText'		=> _x('Time Zone',		'Date Time Picker JS timezoneText', 	'fieldmaster'),
	        'currentText'		=> _x('Now',			'Date Time Picker JS currentText', 		'fieldmaster'),
	        'closeText'			=> _x('Done',			'Date Time Picker JS closeText', 		'fieldmaster'),
	        'selectText'		=> _x('Select',			'Date Time Picker JS selectText', 		'fieldmaster'),
	        'amNames'			=> array(
		        					_x('AM',			'Date Time Picker JS amText', 			'fieldmaster'),
									_x('A',				'Date Time Picker JS amTextShort', 		'fieldmaster'),
								),
	        'pmNames'			=> array(
		        					_x('PM',			'Date Time Picker JS pmText', 			'fieldmaster'),
									_x('P',				'Date Time Picker JS pmTextShort', 		'fieldmaster'),
								)
		);
		
		
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
	   	if( !fieldmaster_get_setting('enqueue_datetimepicker') ) return;
	   	
	   	
		// vars
		$version = '1.6.1';
		
		
		// script
		wp_enqueue_script('fieldmaster-timepicker', fieldmaster_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.js'), array('jquery-ui-datepicker'), $version);
		
		
		// style
		wp_enqueue_style('fieldmaster-timepicker', fieldmaster_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.css'), '', $version);
		
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
		$hidden_value = '';
		$display_value = '';
		
		if( $field['value'] ) {
			
			$hidden_value = fieldmaster_format_date( $field['value'], 'Y-m-d H:i:s' );
			$display_value = fieldmaster_format_date( $field['value'], $field['display_format'] );
			
		}
		
		
		// convert display_format to date and time
		// the letter 'm' is used for date and minute in JS, so this must be done here in PHP
		$formats = fieldmaster_split_date_time($field['display_format']);
		
		
		// vars
		$e = '';
		$div = array(
			'class'					=> 'fieldmaster-date-time-picker fieldmaster-input-wrap',
			'data-date_format'		=> fieldmaster_convert_date_to_js($formats['date']),
			'data-time_format'		=> fieldmaster_convert_time_to_js($formats['time']),
			'data-first_day'		=> $field['first_day'],
		);
		$hidden = array(
			'id'					=> $field['id'],
			'class' 				=> 'input-alt',
			'type'					=> 'hidden',
			'name'					=> $field['name'],
			'value'					=> $hidden_value,
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
		
		// global
		global $wp_locale;
		
		
		// vars
		$d_m_Y = date_i18n('d/m/Y g:i a');
		$m_d_Y = date_i18n('m/d/Y g:i a');
		$F_j_Y = date_i18n('F j, Y g:i a');
		$Ymd = date_i18n('Y-m-d H:i:s');
		
		
		// display_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Display Format','fieldmaster'),
			'instructions'	=> __('The format displayed when editing a post','fieldmaster'),
			'type'			=> 'radio',
			'name'			=> 'display_format',
			'other_choice'	=> 1,
			'choices'		=> array(
				'd/m/Y g:i a'	=> '<span>' . $d_m_Y . '</span><code>d/m/Y g:i a</code>',
				'm/d/Y g:i a'	=> '<span>' . $m_d_Y . '</span><code>m/d/Y g:i a</code>',
				'F j, Y g:i a'	=> '<span>' . $F_j_Y . '</span><code>F j, Y g:i a</code>',
				'Y-m-d H:i:s'	=> '<span>' . $Ymd . '</span><code>Y-m-d H:i:s</code>',
				'other'			=> '<span>' . __('Custom:','fieldmaster') . '</span>'
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
				'd/m/Y g:i a'	=> '<span>' . $d_m_Y . '</span><code>d/m/Y g:i a</code>',
				'm/d/Y g:i a'	=> '<span>' . $m_d_Y . '</span><code>m/d/Y g:i a</code>',
				'F j, Y g:i a'	=> '<span>' . $F_j_Y . '</span><code>F j, Y g:i a</code>',
				'Y-m-d H:i:s'	=> '<span>' . $Ymd . '</span><code>Y-m-d H:i:s</code>',
				'other'			=> '<span>' . __('Custom:','fieldmaster') . '</span>'
			)
		));
				
		
		// first_day
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Week Starts On','fieldmaster'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'first_day',
			'choices'		=> array_values( $wp_locale->weekday )
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
fieldmaster_register_field_type( new fieldmaster_field_date_and_time_picker() );

endif; // class_exists check

?>