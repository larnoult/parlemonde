<?php
/**
* Class for handling styles and scripts
*/
class CycloneSlider_AssetLoader {

	/**
	 * @var string
	 */
	protected $url;
	/**
	 * @var string
	 */
	protected $version;
	/**
	 * @var array
	 */
	protected $settings_page_data;
	/**
	 * @var CycloneSlider_Data
	 */
	protected $data;
	
	public function __construct( $settings_page_data, $url, $version, $data ){
        $this->settings_page_data = $settings_page_data;
		$this->url = $url;
		$this->version = $version;
		$this->data = $data;
    }
	
	public function run() {
		
		// Register frontend styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ), 100 );
		
		// Add scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 10);
		
		add_action( 'admin_enqueue_scripts', array( $this, 'register_frontend_scripts_in_admin' ), 10);
    }
	
    /**
	* Scripts and styles for slider admin area
	*/ 
	public function register_admin_scripts( $hook ) {
 
		if( 'cycloneslider' == get_post_type() || $hook == 'cycloneslider_page_cycloneslider-settings' || $hook == 'cycloneslider_page_cycloneslider-export' || $hook == 'cycloneslider_page_cycloneslider-import' || $hook == 'cycloneslider_page_cycloneslider-export-nextgen' ){ // Limit loading to certain admin pages
			
			// Required media files for new media manager. Since WP 3.5+
			wp_enqueue_media();
			
			// Main style
			wp_enqueue_style( 'cycloneslider-admin-styles', $this->url.'css/admin.css', array(), $this->version  );
			
			// Disable autosave
			wp_dequeue_script( 'autosave' );
			
			// For sortable elements
			wp_enqueue_script('jquery-ui-sortable');
			
			// For localstorage
			wp_enqueue_script( 'store', $this->url.'js/store-json2.min.js', array('jquery'), $this->version );
			
			// Allow translation to script texts
			wp_register_script( 'cycloneslider-admin-script', $this->url.'js/admin.js', array('jquery'), $this->version  );
			wp_localize_script( 'cycloneslider-admin-script', 'cycloneslider_admin_vars',
				array(
					'title'     => __( 'Select an image', 'cycloneslider' ), // This will be used as the default title
					'title2'     => __( 'Select Images - Use Ctrl + Click or Shift + Click', 'cycloneslider' ),
					'button'    => __( 'Add to Slide', 'cycloneslider' ), // This will be used as the default button text
					'button2'    => __( 'Add Images as Slides', 'cycloneslider' ),
					'youtube_url_error'    => __( 'Error. Make sure its a valid YouTube URL.', 'cycloneslider' )
				)
			);
			wp_enqueue_script( 'cycloneslider-admin-script');
			
		}
	}
    
	/**
	 * Scripts and styles for slider to run in admin preview. Must be hook to either admin_enqueue_scripts or wp_enqueue_scripts
	 *
	 * @param string $hook Hook name passed by WP
	 * @return void
	 */
	public function register_frontend_scripts_in_admin( $hook ) {
		if( get_post_type() == 'cycloneslider' || 'cycloneslider_page_cycloneslider-settings' == $hook || 'cycloneslider_page_cycloneslider-export' == $hook || 'cycloneslider_page_cycloneslider-import' == $hook ){ // Limit loading to certain admin pages
			$this->register_frontend_scripts( $hook );
		}
	}
	
	/**
	 * Scripts and styles for slider to run. Must be hook to either admin_enqueue_scripts or wp_enqueue_scripts
	 *
	 * @param string $hook Hook name passed by WP
	 * @return void
	 */
	public function register_frontend_scripts( $hook ) {

		$in_footer = true;
		if($this->settings_page_data['load_scripts_in'] == 'header'){
			$in_footer = false;
		}
		
		/*** Magnific Popup Style ***/
		if($this->settings_page_data['load_magnific'] == 1){
			wp_enqueue_style( 'jquery-magnific-popup', $this->url.'libs/magnific-popup/magnific-popup.css', array(), $this->version );
		}
		
		/*** Templates Styles ***/
		$this->enqueue_templates_css();
		
		/*****************************/
		
		/*** Core Cycle2 Scripts ***/
		if($this->settings_page_data['load_cycle2'] == 1){
			wp_enqueue_script( 'jquery-cycle2', $this->url.'libs/cycle2/jquery.cycle2.min.js', array('jquery'), $this->version, $in_footer );
		}
		if($this->settings_page_data['load_cycle2_carousel'] == 1){
			wp_enqueue_script( 'jquery-cycle2-carousel', $this->url.'libs/cycle2/jquery.cycle2.carousel.min.js', array('jquery', 'jquery-cycle2'), $this->version, $in_footer );
		}
		if($this->settings_page_data['load_cycle2_swipe'] == 1){
			wp_enqueue_script( 'jquery-cycle2-swipe', $this->url.'libs/cycle2/jquery.cycle2.swipe.min.js', array('jquery', 'jquery-cycle2'), $this->version, $in_footer );
		}
		if($this->settings_page_data['load_cycle2_tile'] == 1){
			wp_enqueue_script( 'jquery-cycle2-tile', $this->url.'libs/cycle2/jquery.cycle2.tile.min.js', array('jquery', 'jquery-cycle2'), $this->version, $in_footer );
		}
		if($this->settings_page_data['load_cycle2_video'] == 1){
			wp_enqueue_script( 'jquery-cycle2-video', $this->url.'libs/cycle2/jquery.cycle2.video.min.js', array('jquery', 'jquery-cycle2'), $this->version, $in_footer );
		}
		
		/*** Easing Script***/
		if($this->settings_page_data['load_easing'] == 1){
			wp_enqueue_script( 'jquery-easing', $this->url.'libs/jquery-easing/jquery.easing.1.3.1.min.js', array('jquery'), $this->version, $in_footer );
		}
		
		/*** Magnific Popup Scripts ***/
		if($this->settings_page_data['load_magnific'] == 1){
			wp_enqueue_script( 'jquery-magnific-popup', $this->url.'libs/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), $this->version, $in_footer );
		}
		
		/*** Templates Scripts ***/
		$this->enqueue_templates_scripts();

		/*** Vimeo JS API ***/
		wp_enqueue_script( 'vimeo-player-js', $this->url.'libs/vimeo-player-js/player.js', array(), $this->version, $in_footer );

		/*** Client Script ***/
		wp_enqueue_script( 'cyclone-client', $this->url.'js/client.js', array('jquery'), $this->version, $in_footer );

	}
	
	/**
	* Enqueues templates styles.
	*/
	private function enqueue_templates_css(){

		$template_folders = $this->data->get_all_templates();
		$active_templates = $this->data->get_enabled_templates( $this->settings_page_data, $template_folders );
		
		foreach($template_folders as $name=>$template){
			
			if( 1 == $active_templates[$name] ){ // Active
				foreach($template['styles'] as $count=>$style) {
					wp_enqueue_style( 'cyclone-template-style-'.sanitize_title($name).'-'.$count, $template['url'].'/'.$style, array(), $this->version );
				}
			}
		}
	}
   
	/**
	* Enqueues templates scripts.
	*/
	private function enqueue_templates_scripts(){

		$in_footer = true;
		if( $this->settings_page_data['load_scripts_in'] == 'header'){
			$in_footer = false;
		}
		
		$template_folders = $this->data->get_all_templates();
		$active_templates = $this->data->get_enabled_templates( $this->settings_page_data, $template_folders );
		
		foreach($template_folders as $name=>$template){
			
			if( 1 == $active_templates[$name] ){
				foreach($template['scripts'] as $count=>$script) {
					wp_enqueue_script( 'cyclone-template-script-'.sanitize_title($name).'-'.$count, $template['url'].'/'.$script, array(), $this->version, $in_footer );
				}
			}
		}
	}
}