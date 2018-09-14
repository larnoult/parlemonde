<?php
if(!class_exists('Codefleet_Admin_Sub_Page') and class_exists('Codefleet_Admin_Page')):

	/**
	* Class for wrapping WP add_submenu_page.
	*/
	class Codefleet_Admin_Sub_Page extends Codefleet_Admin_Page {
		
		/**
		* Class variables relating to WP add_submenu_page
		*/
		protected $parent_slug;
		
		/**
		* Initialize 
		*/
		public function __construct() {
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
		
			// Add settings page
			add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
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
				array( $this, 'render_page')
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