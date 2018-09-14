<?php
if(!class_exists('Codefleet_Settings_Page')):

	/**
	* Class for settings page creation.
	*/
	class Codefleet_Settings_Page {
		
		/**
		* Class variables relating to WP settings API
		*/
		protected $option_group;
		protected $option_name;
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
			$this->option_group = 'mytheme_option_group';
			$this->option_name = 'mytheme_option_name';
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

			// Add settings
			add_action( 'admin_init', array( $this, 'register_settings') );
		
			// Add settings page
			add_action( 'admin_menu', array( $this, 'add_menu_and_page'));
		}
		
		/**
		* Prepare option data
		*/
		public function register_settings() {
			// Use built-in WP function
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
			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'render_settings_page'),
				$this->icon_url,
				$this->position
			);
		}

		/**
		* Render settings page. This function should echo the HTML form of the settings page.
		*/
		public function render_settings_page($post){
			?>
			<div class="wrap">
				<?php echo get_screen_icon('options-general'); ?>
				<h2><?php echo $this->page_title; ?></h2>
				<div class="intro">
					<p>This is the intro text</p>
				</div>
				<?php settings_errors(); print_r($this->get_settings_data()); ?>
				
				<form method="post" action="options.php">
					<?php
					echo $this->settings_fields( $this->option_group );
					?>
					<table class="form-table">
						<tr>
							<th><label for="<?php echo esc_attr( 'footer' ); ?>"><?php echo esc_attr( 'Footer Text' ); ?></label></th>
							<td>
								<input type="text" id="<?php echo esc_attr( 'footer' ); ?>" name="<?php echo esc_attr( $this->option_name."[footer]" ); ?>" value="<?php echo esc_attr( $this->get_data('footer') ); ?>" />
							</td>
						</tr>
					</table>
					<?php submit_button( 'Save Options', 'primary', 'submit', false) ?>
					<?php submit_button( 'Restore Defaults', 'secondary', 'reset', false) ?>
				</form>
				
			</div><?php
		}

		/**
		* Validate data from form
		*/
		public function validate_options( $input ) {
			$input = wp_parse_args($input, $this->get_settings_data());
			if( isset($_POST['reset']) ){
				$input = $this->get_default_settings_data();
				add_settings_error( $this->menu_slug, 'restore_defaults', __( 'Default options restored.'), 'updated fade' );
			}
			return $input;
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
		
		/**
		* Get settings data. If there is no data from database, use default values
		*/
		public function get_settings_data(){
			return get_option( $this->option_name, $this->get_default_settings_data() );
		}	
		
		/**
		* Get settings data by uid
		*/
		public function get_data($uid){
			$settings_data = $this->get_settings_data();
			if(isset($settings_data[$uid])){
				return $settings_data[$uid];
			}
			return false;
		}
		
		/**
		* Apply default values
		*/
		public function get_default_settings_data() {
			$defaults = array();

			return $defaults;
		}
		
		/**
		* SETTER FUNCTIONS
		*/
		public function set_option_group( $value ){
			$this->option_group = $value;
		}
		
		public function set_option_name( $value ){
			$this->option_name = $value;
		}
		
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
		public function get_option_group(){
			return $this->option_group;
		}
		
		public function get_option_name(){
			return $this->option_name;
		}
		
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