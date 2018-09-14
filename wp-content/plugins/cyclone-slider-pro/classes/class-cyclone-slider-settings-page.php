<?php
if(!class_exists('Cyclone_Slider_Settings_Page') and class_exists('Codefleet_Settings_Sub_Page')):
	/**
	* Class for plugin settings
	*/
	class Cyclone_Slider_Settings_Page extends Codefleet_Settings_Sub_Page {
		
		protected $view; // Holds the instance of Cyclone_Slider_View
		protected $templates_manager; // Holds template manager object
		
		/**
		* Initialize 
		*/
		public function __construct( $view, $templates_manager ) {
			parent::__construct();
			 // Dependency injections
			$this->view = $view;
			$this->templates_manager = $templates_manager;
		}
		
		/**
		* Render settings page. This function should echo the HTML form of the settings page.
		*/
		public function render_settings_page($post){
			$this->view->set_view_file( CYCLONE_PATH . 'views/settings-page.php' );
            
			$settings_data = $this->get_settings_data();
			$templates = $this->templates_manager->get_all_templates();

			$settings_data['load_templates'] = $this->templates_manager->get_active_templates( $settings_data );// Filter load templates

			
            $vars = array();
            $vars['page_title'] = $this->page_title;
            $vars['screen_icon'] = get_screen_icon('options-general'); ;
            
			
			$vars['settings_fields'] = $this->settings_fields( $this->option_group );
			$vars['option_name'] = $this->option_name;
			
			
			$vars['templates'] = $templates;
			$vars['settings_data'] = $settings_data;
			
            $vars['debug'] = (CYCLONE_DEBUG) ? cyclone_slider_debug( $vars['settings_data'] ) : '';
            
            $this->view->set_vars( $vars );
            $this->view->render();
		}
		
		/**
		* Validate data from HTML form
		*/
		public function validate_options( $input ) {
			$input = wp_parse_args($input, $this->get_settings_data());
			
			
			if( isset($_POST['reset']) ){
				$input = $this->get_default_settings_data();
				add_settings_error( $this->menu_slug, 'restore_defaults', __( 'Default options restored.', 'cycloneslider'), 'updated fade' );
			} else {
				
			}
			return $input;
		}
		
		/**
		* Apply default values
		*/
		public function get_default_settings_data() {
			$defaults = array();
			$defaults['load_scripts_in'] = 'footer';
			
			$defaults['load_cycle2'] = 1;
			$defaults['load_cycle2_carousel'] = 1;
			$defaults['load_cycle2_swipe'] = 1;
			$defaults['load_cycle2_tile'] = 1;
			$defaults['load_cycle2_video'] = 1;

			$defaults['load_easing'] = 1;
			
			$defaults['load_magnific'] = 1;
			
			$defaults['load_templates'] = array();
			
			$defaults['script_priority'] = 100;
			return $defaults;
		}
		
		
	} // end class
	
endif;