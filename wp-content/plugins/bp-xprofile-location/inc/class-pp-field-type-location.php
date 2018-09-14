<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if( !class_exists('PP_Field_Type_Location') ) {
	
	class PP_Field_Type_Location extends BP_XProfile_Field_Type {
		
		public function __construct() {
			parent::__construct();
			$this->category = _x( 'Single Fields', 'xprofile field type category', 'buddypress' );
			$this->name  = _x( 'Location', 'xprofile field type', 'bp-profile-location' );
			$this->accepts_null_value   = true;
			$this->supports_options = true;
			$this->set_format( '/^.+$/', 'replace' );
			do_action( 'bp_xprofile_field_type_location', $this );
		}
		
		public function admin_field_html (array $raw_properties = array ()) {
			$html = $this->get_edit_field_html_elements( array_merge(
				array(
					'type' => 'text',
				),
				$raw_properties
			) );
			?>
			<input <?php echo $html; ?>>
			<?php
		}
		
		public function edit_field_html ( array $raw_properties = array () ) {
			if ( isset( $raw_properties['user_id'] ) ) {
				unset( $raw_properties['user_id'] );
			}
			if ( bp_get_the_profile_field_is_required() ) {
				$raw_properties['required'] = 'required';
			}
			$value = bp_get_the_profile_field_edit_value();
			
				if ( $value == 'a:0:{}' ) {
					
					$value = 'PROBLEM ' . bp_get_the_profile_field_id();
				}
				
			$html = $this->get_edit_field_html_elements( array_merge(
				array(
					'type'          => 'text',
					'value'         => $value,  //bp_get_the_profile_field_edit_value(),
					'placeholder'   => __( 'Start typing an address', 'bp-profile-location' ),
					'class'         => 'form-control',
					'autocomplete'  => 'false'
				),
				$raw_properties
			) );
			?>
			<?php
			/** Setting autocomplete to false on the form and / or a field is not enough to always prevent autofill
			  * This ugly hack prevents autofill if field label is a word ( ex. Address ) that triggers autofill
			  * Inserts a zero-width space character into the string
			  */
			$label_name = bp_get_the_profile_field_name();
			$label_name = substr_replace($label_name, '&#8203;', 1, 0);
			$save_geocode = bp_xprofile_get_meta( bp_get_the_profile_field_id(), 'data', 'geocode' );
			if( empty( $save_geocode ) )
				$save_geocode = '0';
			?>
			<label for="<?php bp_the_profile_field_input_name(); ?>"><?php echo $label_name; ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php esc_html_e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
			<?php do_action( bp_get_the_profile_field_errors_action() ); ?>
			<input <?php echo $html; ?>  />
			<?php bp_the_profile_field_description(); ?>										   
			<script>
				pp_<?php bp_the_profile_field_id(); ?>_geo_initialize();
				function pp_<?php bp_the_profile_field_id(); ?>_geo_initialize() {
					var location_field_name = '<?php bp_the_profile_field_input_name() ?>';
					var save_geocode = '<?php echo $save_geocode ?>';
					// if you want to use place names as well as proper addresses, use this instead
					// pp_<?php bp_the_profile_field_id(); ?>_autocomplete = new google.maps.places.Autocomplete( (document.getElementById(location_field_name)) );
					pp_<?php bp_the_profile_field_id(); ?>_autocomplete = new google.maps.places.Autocomplete( (document.getElementById(location_field_name)), { types: ['geocode'] });
					google.maps.event.addListener(pp_<?php bp_the_profile_field_id(); ?>_autocomplete, 'place_changed', function() {
						var address = pp_<?php bp_the_profile_field_id(); ?>_autocomplete.getPlace();
						document.getElementById(location_field_name).value = address.formatted_address;
						if ( save_geocode == '1' )
							pp_<?php bp_the_profile_field_id(); ?>_extract_geocode();
					});
				}
				<?php if ( '1' == $save_geocode ) : ?>
					function pp_<?php bp_the_profile_field_id(); ?>_extract_geocode() {
						var place = pp_<?php bp_the_profile_field_id(); ?>_autocomplete.getPlace();
						console.log( place );
						var lat = place.geometry.location.lat();
						var lng = place.geometry.location.lng();
						var latlng = lat + ',' + lng;
						document.getElementById('pp_<?php bp_the_profile_field_id(); ?>_geocode').value = latlng;
					}
				<?php endif; ?>
			</script>
			<?php if ( '1' == $save_geocode ) : ?>
				<input type="hidden" id="pp_<?php bp_the_profile_field_id(); ?>_geocode" name="pp_<?php bp_the_profile_field_id(); ?>_geocode" />
			<?php endif; ?>
		<?php
		}
		
		public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
			$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
			if ( false === $type ) {
				return;
			}
			$class  = $current_field->type != $type ? 'display: none;' : '';
			$current_type_obj = bp_xprofile_create_field_type( $type );
			$geocode_option = bp_xprofile_get_meta( $current_field->id, 'data', 'geocode' );
			if ( false == $geocode_option )
				$geocode_option = 1;
			?>
			<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
				<div class="inside">
					<h4><?php _e( 'Do you want this field to save a geocode for each member?', 'bp-profile-location' ); ?></h4>
					<p>
                        <select name="<?php echo esc_attr( "{$type}_option[1]" ); ?>" id="<?php echo esc_attr( "{$type}_option1" ); ?>">
	                        <?php for ($j=2;$j>=1;$j--): ?>
	                            <option value="<?php echo $j; ?>"<?php if ($j === (int)$geocode_option): ?> selected="selected"<?php endif; ?>><?php if ( $j == 2 ) echo 'No'; else echo 'Yes'; ?></option>
	                        <?php endfor; ?>
                        </select>
					</p>
					<?php _e( 'The geocode will be saved in the usermeta table in this format:', 'bp-profile-location' ); ?>
					<ul>
						<li>meta_key = geocode_[field id]</li>
						<li>meta_value = [latitude], [longitude]</li>
					</ul>
					You can then use the geocode in your mapping solution.
					<br/>Or use a solution like <a href="http://www.philopress.com/products/bp-maps-for-members/">BP Maps for Members</a>.
					<br/>For Groups Maps, see <a href="http://www.philopress.com/products/bp-maps-for-groups/">BP Maps for Groups</a>.
				</div>
			</div>
		<?php
		}
		
        public function is_valid( $values ) {
            $this->validation_whitelist = null;
            return parent::is_valid($values);
        }
		
	}
}