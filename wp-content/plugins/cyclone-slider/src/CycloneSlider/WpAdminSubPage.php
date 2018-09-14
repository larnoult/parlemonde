<?php
/**
* Abstract class for WP admin sub page
*/
abstract class CycloneSlider_WpAdminSubPage {
	protected $parent_slug;
	protected $page_title;
	protected $menu_title;
	protected $capability;
	protected $menu_slug;
	
	/**
	* Constructor
	*/
	public function __construct ( $parent_slug = '', $page_title = '', $menu_title = '', $capability = '', $menu_slug = '' ){
	
		$this->parent_slug = $parent_slug;
		$this->page_title = $page_title;
		$this->menu_title = $menu_title;
		$this->capability = $capability;
		$this->menu_slug = $menu_slug;
		
	}
	
	public function run() {
		
		// Add settings page
		add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
	}
	
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
	
	public function render_page(){
		?><h1>WP Admin Sub Page</h1><?php
	}
}
