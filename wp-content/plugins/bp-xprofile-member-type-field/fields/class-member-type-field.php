<?php
/**
 * Implementing member type as select field
 * 
 */
class BD_XProfile_Field_Type_MemberType extends BP_XProfile_Field_Type {
	

	public function __construct() {
		
		parent::__construct();

		$this->category = _x( 'Single Fields', 'xprofile field type category', 'bp-xprofile-member-type-field' );
		$this->name     = _x( 'Single Member Type', 'xprofile field type', 'bp-xprofile-member-type-field' );

		$this->set_format( '', 'replace' );
		
		$this->supports_multiple_defaults = false;
		$this->accepts_null_value         = true;
		$this->supports_options           = false;

		do_action( 'bd_xprofile_field_type_membertype', $this );
	}

	/**
	 * Is it a valid member type?
	 * 
	 * @param type $val
	 * @return boolean
	 */
	public function is_valid( $val ) {
		
		//if a registered member type,
		if ( empty( $val) || bp_get_member_type_object( $val ) ) {
			return true;
		}
		
		return false;
		
	}

	public function edit_field_html( array $raw_properties = array() ) {
		
		$this->_edit_field_html( $raw_properties );
		
		bp_xprofile_member_type_field_helper()->set_shown( bp_get_the_profile_field_id() );
	}

	/**
	 * Output the edit field options HTML for this field type.
	 *
	 * BuddyPress considers a field's "options" to be, for example, the items in a selectbox.
	 * These are stored separately in the database, and their templating is handled separately.
	 *
	 * This templating is separate from {@link BP_XProfile_Field_Type::edit_field_html()} because
	 * it's also used in the wp-admin screens when creating new fields, and for backwards compatibility.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param array $args Optional. The arguments passed to {@link bp_the_profile_field_options()}.
	 * 
	 */
	public function edit_field_options_html( array $args = array() ) {
		
		$original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		if ( ! empty( $_POST['field_' . $this->field_obj->id] ) ) {
			
			$option_values =  (array) $_POST['field_' . $this->field_obj->id] ;
			$option_values = array_map( 'sanitize_text_field', $option_values );
			
		} else {
			
			$option_values = (array)$original_option_values;
			
		}
		 //member types list as array
                
		$options = self::get_member_types();

		//$option_values = (array) $original_option_values;	

		if ( $this->display_as_radio() ) {
			$this->_edit_options_html_radio( $option_values, $options );
		} else {
			$this->_edit_options_html( $option_values, $options );
		}

	}

	public function admin_field_html( array $raw_properties = array() ) {
		
		$this->edit_field_html();

	}
	
	public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
		
	}
	
	/**
	 * Format member type value for  display.
	 *
	 * @param string $field_value The member type name(key) value, as saved in the database.
	 * @return string the member type label
	 */
	public static function display_filter( $field_value, $field_id = '' ) {
		
		if ( empty( $field_value ) ) {
			return $field_value;
		}
		
		$member_types = self::get_member_types();
		
		if ( isset( $member_types[ $field_value ] ) ) {
			return $member_types[ $field_value ];
		}
		
		return '';
		
	}
	
	/**
	 * Get member types as associative array
	 * 
	 * @staticvar array $member_types
	 * @return array
	 */
	private static function get_member_types() {
		
		static $member_types = null;
		
		if ( isset( $member_types ) ) {
			return $member_types;
		}
		
		$registered_member_types = bp_get_member_types( null, 'object' );
		
		if ( empty( $registered_member_types ) ) {
			$member_types = $registered_member_types;
			return $member_types;
		}
		
		foreach ( $registered_member_types as $type_name => $member_type_object ) {
			$member_types[$type_name] = $member_type_object->labels['singular_name'];
		}
		
		return apply_filters( 'bp_xprofile_member_type_field_allowed_types', $member_types, $registered_member_types );
	}

	/**
	 * Show as radio or not?
	 * @return boolean
	 */
	public function display_as_radio() {
		return apply_filters( 'bd_xprofile_field_type_membertype_as_radio', false, $this );
	}
	//Helpers

	/**
	 * Display as select box
	 * @param array $raw_properties
	 */
	public function _edit_field_html( array $raw_properties = array() ) {

		// User_id is a special optional parameter that we pass to
		// {@link bp_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = bp_displayed_user_id();
		}

		if ( $this->display_as_radio() ) {
			$this->_element_html_radio( $raw_properties, $user_id );
		} else {
			$this->_element_html( $raw_properties, $user_id );
		}


	}

	protected function _element_html($raw_properties = array() , $user_id = null) {
		?>
		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</label>

		<?php

		/** This action is documented in bp-xprofile/bp-xprofile-classes */
		do_action( bp_get_the_profile_field_errors_action() );?>

		<select <?php echo $this->get_edit_field_html_elements( $raw_properties ); ?>>
			<?php bp_the_profile_field_options( array( 'user_id' => $user_id ) ); ?>
		</select>
		<?php
	}
	protected function _element_html_radio($raw_properties = array() , $user_id = null) {
		?>

		<div class="radio">

			<label for="<?php bp_the_profile_field_input_name(); ?>">
				<?php bp_the_profile_field_name(); ?>
				<?php bp_the_profile_field_required_label(); ?>
			</label>

			<?php

			/** This action is documented in bp-xprofile/bp-xprofile-classes */
			do_action( bp_get_the_profile_field_errors_action() ); ?>

			<?php bp_the_profile_field_options( array( 'user_id' => $user_id ) );

			if ( ! bp_get_the_profile_field_is_required() ) : ?>

				<a class="clear-value" href="javascript:clear( '<?php echo esc_js( bp_get_the_profile_field_input_name() ); ?>' );">
					<?php esc_html_e( 'Clear', 'buddypress' ); ?>
				</a>

			<?php endif; ?>

		</div>
		<?php
	}
	/**
	 * @param $option_values
	 * @param $options
	 */
	protected function _edit_options_html( $option_values, $options ) {
		$selected = '';
		if ( empty( $option_values ) || in_array( 'none', $option_values ) ) {
			$selected = ' selected="selected"';
		}

		$html = '<option value="" ' . $selected . ' >----' . /* translators: no option picked in select box */
		        '</option>';

		echo $html;

		foreach ( $options as $member_type => $label ) {

			$selected = '';
			// Run the allowed option name through the before_save filter, so we'll be sure to get a match
			$allowed_options = xprofile_sanitize_data_value_before_save( $member_type, false, false );

			// First, check to see whether the user-entered value matches
			if ( in_array( $allowed_options, (array) $option_values ) ) {
				$selected = ' selected="selected"';
			}

			echo apply_filters( 'bp_get_the_profile_field_options_member_type', '<option' . $selected . ' value="' . esc_attr( stripslashes( $member_type ) ) . '">' . $label . '</option>', $member_type, $this->field_obj->id, $selected );

		}
	}
	protected function _edit_options_html_radio( $option_values, $options ) {


		foreach ( $options as $member_type => $label ) {

			$selected = '';
			// Run the allowed option name through the before_save filter, so we'll be sure to get a match
			$allowed_options = xprofile_sanitize_data_value_before_save( $member_type, false, false );
			// First, check to see whether the user-entered value matches
			if ( in_array( $allowed_options, (array) $option_values ) ) {
				$selected = ' checked="checked"';
			}

			$new_html = sprintf( '<label for="%3$s"><input %1$s type="radio" name="%2$s" id="%3$s" value="%4$s">%5$s</label>',
				$selected,
				esc_attr( "field_{$this->field_obj->id}" ),
				esc_attr( "option_{$member_type}" ),
				esc_attr( stripslashes( $member_type ) ),
				esc_html( stripslashes( $label ) )
			);


			echo apply_filters( 'bp_get_the_profile_field_options_member_type', $new_html, $member_type, $this->field_obj->id, $selected );

		}

	}
}
