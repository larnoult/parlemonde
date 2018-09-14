<?php
if(!class_exists('Codefleet_Settings_Sub_Page') and class_exists('Codefleet_Settings_Page')):

	/**
	* Class for settings for sub page creation.
	*/
	class Codefleet_Settings_Sub_Page extends Codefleet_Settings_Page {
		
		/**
		* Class variables relating to WP settings API
		*/
		protected $parent_slug;
		
		/**
		* Initialize 
		*/
		public function __construct() {		
			$this->option_group = 'mytheme_option_group';
			$this->option_name = 'mytheme_option_name';
			$this->page_title = 'Settings Sub Page';
			$this->menu_title = 'Settings Sub Page';
			$this->capability = 'manage_options';
			$this->menu_slug = 'custom-settings-sub-page';
			$this->icon_url = null;
			$this->position = null;
			$this->parent_slug = 'options-general.php';
		}
		
		
		/**
		* Show settings page by hooking 'em up with WP.
		*/
		public function show(){

			// Add settings
			add_action( 'admin_init', array( $this, 'register_settings') );
		
			// Add settings page
			add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
		}
		
		/**
		* Prepare option data
		*/
		public function register_settings() {
			register_setting(
				$this->option_group,
				$this->option_name,
				array( $this, 'validate_options')
			);
		}
		
		/**
		* Menu page action hook 
		*/
		public function add_menu_and_page(){
			// Use built-in WP function
			add_submenu_page(
				$this->parent_slug,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'render_settings_page')
			);
		}
		
		/**
		* SETTER FUNCTIONS
		*/
		public function set_parent_slug( $value ){
			$this->parent_slug = $value;
		}
		
		/**
		* GETTER FUNCTIONS
		*/
		public function get_parent_slug(){
			return $this->parent_slug;
		}
		
	} // end class
	
endif;