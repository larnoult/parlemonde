<?php
/**
* Abstract class for WP admin page
*/
abstract class CycloneSlider_WpAdminPage {
	protected $page_title;
	protected $menu_title;
	protected $capability;
	protected $menu_slug;
	protected $icon_url;
	protected $position;
	
	/**
	* Constructor
	*/
	public function __construct ( $page_title = '', $menu_title = '', $capability = '', $menu_slug = '', $icon_url = '', $position = '' ){
	
		$this->page_title = $page_title;
		$this->menu_title = $menu_title;
		$this->capability = $capability;
		$this->menu_slug = $menu_slug;
		$this->icon_url = $icon_url;
		$this->position = $position;
		
	}
	
	public function run() {
		
		// Add settings page
		add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
	}
	
	public function add_menu_and_page(){
		// Use built-in WP function
		add_menu_page(
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			array( $this, 'render_page'),
			$this->icon_url,
			$this->position
		);
	}
	
	public function render_page(){
		?><h1>Wp Admin Page</h1><?php
	}
}
