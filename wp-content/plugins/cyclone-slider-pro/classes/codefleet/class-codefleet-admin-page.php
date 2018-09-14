<?php
if(!class_exists('Codefleet_Admin_Page')):

	/**
	* Class for wrapping WP add_menu_page.
	*/
	class Codefleet_Admin_Page {
		
		/**
		* Class variables relating to WP add_menu_page
		*/
		protected $page_title;
		protected $menu_title;
		protected $capability;
		protected $menu_slug;
		protected $icon_url;
		protected $position;
		
		/**
		* Initialize 
		*/
		public function __construct() {
			$this->page_title = 'Custom Settings';
			$this->menu_title = 'Custom Settings';
			$this->capability = 'manage_options';
			$this->menu_slug = 'custom-settings-page';
			$this->icon_url = null;
			$this->position = null;
		}
		
		/**
		* Show settings page by hooking 'em up with WP.
		*/
		public function show(){
			
			// Add page
			add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
		}
		
		/**
		* Menu page action hook 
		*/
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

		/**
		* Render page. This function should output the HTML of the page.
		*/
		public function render_page($post){
			?>
			<div class="wrap">
				<?php echo get_screen_icon('options-general'); ?>
				<h2><?php echo $this->page_title; ?></h2>
				<div class="intro">
					<p>This is the intro text</p>
				</div>

				<form method="post" action="">

					<table class="form-table">
						<tr>
							<th><label for="<?php echo esc_attr( 'sample_text' ); ?>"><?php echo esc_attr( 'Sample Text' ); ?></label></th>
							<td>
								<input type="text" id="sample_text" name="sample_text" value="" />
							</td>
						</tr>
					</table>
					<?php submit_button( 'Save Options', 'primary', 'submit', false) ?>
					<?php submit_button( 'Restore Defaults', 'secondary', 'reset', false) ?>
				</form>
				
			</div><?php
		}

		
	
		
		/**
		* SETTER FUNCTIONS
		*/
		public function set_page_title( $value ){
			$this->page_title = $value;
		}
		
		public function set_menu_title( $value ){
			$this->menu_title = $value;
		}
		
		public function set_capability( $value ){
			$this->capability = $value;
		}
		
		public function set_menu_slug( $value ){
			$this->menu_slug = $value;
		}
		
		public function set_icon_url( $value ){
			$this->icon_url = $value;
		}
		
		public function set_position( $value ){
			$this->position = $value;
		}

		/**
		* GETTER FUNCTIONS
		*/
		public function get_page_title(){
			return $this->page_title;
		}
		
		public function get_menu_title(){
			return $this->menu_title;
		}
		
		public function get_capability(){
			return $this->capability;
		}
		
		public function get_menu_slug(){
			return $this->menu_slug;
		}
		
		public function get_icon_url(){
			return $this->icon_url;
		}
		
		public function get_position(){
			return $this->position;
		}
		
	} // end class
	
endif;