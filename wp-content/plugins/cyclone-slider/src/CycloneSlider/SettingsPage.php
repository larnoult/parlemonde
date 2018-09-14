<?php
/**
* Class for settings page
*/
class CycloneSlider_SettingsPage extends CycloneSlider_WpSubPage {
	
	protected $data;
	protected $debug;
	protected $view;
	
	public function __construct( $settings_page_properties, $data, $debug, $view ){
		parent::__construct( $settings_page_properties );
		
		$this->data = $data;
		$this->debug = $debug;
		$this->view = $view;
	}
	
	/**
	* Render settings page. This function should echo the HTML form of the settings page.
	*/
	public function render_page(){
		
		$settings_data = $this->data->get_settings_page_data();
		$templates = $this->data->get_all_templates();

		$settings_data['load_templates'] = $this->data->get_enabled_templates($settings_data, $templates);// Filter load templates

		$vars = array();
		$vars['page_title'] = $this->settings_page_properties['page_title'];
		$vars['screen_icon'] = $this->get_screen_icon('options-general');
		$vars['settings_fields'] = $this->settings_fields( $this->settings_page_properties['option_group'] );
		$vars['option_name'] = $this->settings_page_properties['option_name'];
		$vars['templates'] = $templates;
		$vars['settings_data'] = $settings_data;
		$vars['debug'] = ($this->debug) ? cyclone_slider_debug( $vars['settings_data'] ) : '';
		
		$this->view->render( 'settings-page.php', $vars);
	}

	/**
	* Validate data from HTML form
	*/
	public function validate_options( $input ) {
		$input = wp_parse_args($input, $this->data->get_settings_page_data());
		
        delete_site_transient('update_plugins'); // Force check. Regenerate package url for updater
		
		if( isset($_POST['reset']) ){
			$input = $this->data->get_default_settings_page_data();
			add_settings_error( $this->settings_page_properties['menu_slug'], 'restore_defaults', __( 'Default options restored.', 'cycloneslider' ), 'updated fade' );
		}
		return $input;
	}
	
	
}
