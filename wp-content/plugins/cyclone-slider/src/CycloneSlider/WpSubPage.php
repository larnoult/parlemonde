<?php
/**
* Class for WP sub page
*/
class CycloneSlider_WpSubPage {
	protected $settings_page_properties;
	
	public function __construct( $settings_page_properties ){
		$this->settings_page_properties = $settings_page_properties;
	}
	
	public function run() {

		// Add settings
		add_action( 'admin_init', array( $this, 'register_settings') );
		
		// Add settings page
		add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
	}
	
	public function add_menu_and_page(){
		// Use built-in WP function
		add_submenu_page(
			$this->settings_page_properties['parent_slug'],
			$this->settings_page_properties['page_title'],
			$this->settings_page_properties['menu_title'],
			$this->settings_page_properties['capability'],
			$this->settings_page_properties['menu_slug'],
			array( $this, 'render_page')
		);
	}
	
	/**
	* Prepare option data
	*/
	public function register_settings() {
		register_setting(
			$this->settings_page_properties['option_group'],
			$this->settings_page_properties['option_name'],
			array( $this, 'validate_options')
		);
	}
	
	/**
	* Output needed fields for security
	*/
	function settings_fields( $option_group ) {
		$fields = "<input type='hidden' name='option_page' value='" . esc_attr($option_group) . "' />";
		$fields .= '<input type="hidden" name="action" value="update" />';
		$fields .= wp_nonce_field("$option_group-options", '_wpnonce', true, false);
		return $fields;
	}
	
	protected function get_screen_icon( $icon ){
		global $wp_version;
		
		if ( version_compare( $wp_version, '3.7', '<=' ) ) { // WP 3.7 and below
			return get_screen_icon( $icon );
		}
		return ''; // Screen icons are no longer used as of WordPress 3.8
	}
	
	public function render_page(){
		
	}
	
	public function validate_options( $input ) {
		
		return $input;
	}
}
