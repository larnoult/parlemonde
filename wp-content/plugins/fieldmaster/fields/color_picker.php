<?php

/*
*  FieldMaster Color Picker Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_color_picker
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_color_picker') ) :

class fieldmaster_field_color_picker extends fieldmaster_field {
	
	
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
		$this->name = 'color_picker';
		$this->label = __("Color Picker",'fieldmaster');
		$this->category = 'jquery';
		$this->defaults = array(
			'default_value'	=> '',
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
		
		// globals
		global $wp_scripts, $wp_styles;
		
		
		// register if not already (on front end)
		// http://wordpress.stackexchange.com/questions/82718/how-do-i-implement-the-wordpress-iris-picker-into-my-plugin-on-the-front-end
		if( !isset($wp_scripts->registered['iris']) ) {
			
			// styles
			wp_register_style('wp-color-picker', admin_url('css/color-picker.css'), array(), '', true);
			
			
			// scripts
			wp_register_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), '1.0.7', true);
			wp_register_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), '', true);
			
			
			// localize
		    wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
		        'clear'			=> __('Clear', 'fieldmaster' ),
		        'defaultString'	=> __('Default', 'fieldmaster' ),
		        'pick'			=> __('Select Color', 'fieldmaster' ),
		        'current'		=> __('Current Color', 'fieldmaster' )
		    )); 
			
		}
		
		
		// enqueue
		wp_enqueue_style('wp-color-picker');
	    wp_enqueue_script('wp-color-picker');
	    
	    			
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
		$text = fieldmaster_get_sub_array( $field, array('id', 'class', 'name', 'value') );
		$hidden = fieldmaster_get_sub_array( $field, array('name', 'value') );
		$e = '';
		
		
		// render
		?>
		<div class="fieldmaster-color_picker">
			<?php fieldmaster_hidden_input($hidden); ?>
			<input type="text" <?php echo fieldmaster_esc_attr($text); ?> />
		</div>
		<?php
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
		
		// display_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Default Value','fieldmaster'),
			'instructions'	=> '',
			'type'			=> 'text',
			'name'			=> 'default_value',
			'placeholder'	=> '#FFFFFF'
		));
		
	}
	
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_color_picker() );

endif; // class_exists check

?>