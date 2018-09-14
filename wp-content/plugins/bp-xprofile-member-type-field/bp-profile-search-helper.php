<?php
/**
 * Thanks to Andrea <http://dontdream.it/>
 * The plugin is now compatible with BP Profile Search
 */
class BD_Xprofile_Member_Type_Field_Search_Helper {
	
	private static $instance;
	
	private function __construct() {
		$this->setup();
	}
	
	public static function get_instance() {
		
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	private function setup() {
		add_filter( 'bps_field_validation', array( $this, 'field_validation' ), 10, 2 );
		
		add_filter( 'bps_field_data_for_search_form', array( $this, 'field_options' ) );
		add_filter( 'bps_field_data_for_filters', array( $this, 'field_options' ) );
		
		add_filter ( 'bps_field_type_for_query', array( $this, 'map_query_field_type' ) );
	}
	
	public function field_validation( $settings, $field ) {
    
		list( $value, $description, $range ) = $settings;
    
		if ( $field->type == 'membertype' ) {
			$range = false;
		}
		
		return array ( $value, $description, $range );
	}

	public function field_options( $field ) {
		
		if ( $field->type !='membertype' ) {
			return $field;
		}
		
			
		$field->display = 'selectbox';
		$field->values = isset( $_REQUEST[ $field->code ] ) ? (array) $_REQUEST[ $field->code] : array();

		$field->options = array();

		$registered_member_types = bp_get_member_types( null, 'object' );

		foreach( $registered_member_types as $type_name => $member_type_object ) {

			$field->options[ $type_name ] = $member_type_object->labels['singular_name'];
		}
		
		return $field;
	}

	public function map_query_field_type( $field_type ) {
		
		if ( $field_type == 'membertype' ) {
			$field_type = 'selectbox';//map to select box
		}
		
		return $field_type;
	}

}
//initialize
BD_Xprofile_Member_Type_Field_Search_Helper::get_instance();

