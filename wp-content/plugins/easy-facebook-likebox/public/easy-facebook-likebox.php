<?php
/**
 * Plugin Name.
 *
 * @package   EasyFacebookLikeBox
 * @author    Sajid Javed <sjaved_87@yahoo.compact> 
 * @license   GPL-2.0+
 * @link      http://jwesolb.com
 * @copyright 2014 Your Name or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package EasyFacebookLikeBox
 * @author  Sajid Javed <sjaved_87@yahoo.compact>
 */
// Include and instantiate the class.
require_once 'includes/Mobile_Detect.php';
$mDetect = new EFBL_Mobile_Detect;

class Easy_Facebook_Likebox {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.1.0
	 *
	 * @var     string
	 */
	const VERSION = '4.3.8';

	/**
	 * @TODO - Rename "plugin-name" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'easy-facebook-likebox';

	/**
	 * Instance of this class.
	 *
	 * @since    1.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Instance of the like box render funcion
	 *
	 * @since    1.1.0
	 *
	 * @var      object
	 */
	public $likebox_instance = 1;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.1.0
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		// Load plugin text domain
		add_action( 'wp_footer', array( $this, 'efbl_display_popup' ), 50 );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_shortcode( 'efb_likebox', array( $this, 'efb_likebox_shortcode' ) );
		add_shortcode( 'efb_pageplugin', array( $this, 'efb_pageplugin_shortcode' ) );
		
		add_shortcode( 'efb_feed', array( $this, 'efb_feed_shortcode' ) );

		add_action( 'wp_ajax_efbl_generate_popup_html', array( $this, 'efbl_generate_popup_html' ) );

		add_action( 'wp_ajax_nopriv_efbl_generate_popup_html', array( $this, 'efbl_generate_popup_html' ) );
  
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.1.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.1.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.1.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.1.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );

		wp_enqueue_style( $this->plugin_slug . '-font-awesome', plugins_url( 'assets/css/font-awesome.css', __FILE__ ), array(), self::VERSION );

		wp_enqueue_style( $this->plugin_slug . '-animate', plugins_url( 'assets/css/animate.css', __FILE__ ), array(), self::VERSION );
		
		wp_enqueue_style( $this->plugin_slug . '-popup-styles', plugins_url( 'assets/popup/magnific-popup.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_scripts() {
 		wp_enqueue_script( $this->plugin_slug . '-popup-script', plugins_url( 'assets/popup/jquery.magnific-popup.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-cookie-script', plugins_url( 'assets/js/jquery.cookie.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		
		wp_enqueue_script( $this->plugin_slug . '-public-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-popup-script',  $this->plugin_slug . '-cookie-script'), self::VERSION );
 		 wp_localize_script( $this->plugin_slug . '-public-script', 'public_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
			));

	}
	 
	
	public function efb_likebox_shortcode($atts, $content=""){
		return $this->render_fb_page_plugin($atts);
	}
	
	public function efb_pageplugin_shortcode($atts, $content=""){
		return $this->render_fb_page_plugin($atts);
	}
	
	public function efb_feed_shortcode($atts, $content=""){
		return $this->render_fbfeed_box($atts);


	}	
	
	public function render_fbfeed_box($atts) {
		
		$defaults = '';
		$instance = wp_parse_args( (array) $atts, $defaults );
 		
 		ob_start();
		
		include('views/feed.php');
		
		$returner = ob_get_contents();
		
		ob_end_clean();
		
		
 		return $returner;
		 
	}
	
	/**
	 * 		  This fucntion will render the facebook page plugin
 	 *
	 *
	 * @since    4.0
	 */
	
	public function render_fb_page_plugin($options) {
		/*echo "<pre>";
		print_r($options);
		exit;*/
		
		extract($options, EXTR_SKIP);
		
		if( empty( $fb_appid ) ){
			$fb_appid = '395202813876688';
		}
		
		if( empty( $locale ) ){
			$locale = 'en_US';
		}
 		
		if( !empty( $locale_other ) ){
			$locale = $locale_other;
		}
		
 		$page_name_id = efbl_parse_url(  $fanpage_url );
 
 		$show_stream = ( $show_stream == 1 ) ? 'data-show-posts=true' : 'data-show-posts=false'; 
		$show_faces = ( $show_faces == 1 ) ? 'data-show-facepile=true' : 'data-show-facepile=false'; 
		$hide_cover = ( $hide_cover == 1 ) ? 'data-hide-cover="true"' : 'data-hide-cover=false' ;
		
		$responsive = ( $responsive == 1 ) ? 'data-adapt-container-width=true' : 'data-adapt-container-width=false'; 
		$hide_cta = ( $hide_cta == 1 ) ? 'data-hide-cta=true' : 'data-hide-cta=false'; 
		$small_header = ( $small_header == 1 ) ? 'data-small-header="true"' : 'data-small-header="false"' ;
  		
  		$preLoader =  plugins_url( 'assets/images/loader.gif', __FILE__ );

		$returner = '<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.async=true; 
					  js.src = "//connect.facebook.net/'.$locale.'/all.js#xfbml=1&appId='.$fb_appid.'";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, \'script\', \'facebook-jssdk\'));</script>';

		$likebox_instance = $this->likebox_instance;		

		$returner .= ' <div class="efbl-like-box '.$likebox_instance.'">
							<img class="efbl-loader" src="'.$preLoader.'" >
							<div class="fb-page" data-animclass="';

							if($animate_effect)
							$returner .= ''.$animate_effect.'';

							$returner .= ' " data-href="https://www.facebook.com/'.$page_name_id.'" '.$hide_cover.' data-width="'.$box_width.'" data-height="'.$box_height.'" '.$show_faces.'  '.$show_stream.' '.$responsive.' '.$hide_cta.' '.$small_header.'>
							</div> 
							
						</div>
					';
		
   		return $returner;
	
		$this->likebox_instance++;	 
	}
	
	function efbl_generate_popup_html(){
	
		$rand_id = $_GET['rand_id'];

		$returner = null;

		$returner = '<div id="efblcf_holder" class="white-popup" data-rand_id="'.$rand_id.'">
	
			<div class="efbl_popup_wraper">
			
				<div class="efbl_popup_left_container">	
				  <img src="" class="efbl_popup_image" />
				  <iframe src="" class="efbl_popup_if_video" ></iframe>
				  <video src="" class="efbl_popup_video" id="html_video" controls></video>
				  <div class="efbl-popup-nav">
				  	<a class="efbl-popup-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
					<a class="efbl-popup-next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>	
				  </div>
				</div>
				
				 <div class="efbl_popupp_footer">
				 </div>
				 
			</div>	 
				 
		</div>';

		echo $returner;
		die();
	}


	function efbl_display_popup(){
		global $mDetect;
		$options = get_option( 'efbl_settings_display_options' );
		
		//Return if not enable
		if($options['efbl_enable_popup'])
		if($options['efbl_enable_popup'] != 1 ) return; 
		
		//check if to display to logged in users
		if($options['efbl_enabe_if_home'])
		if($options['efbl_enabe_if_home'] == 1 ) {
			
			//Do not show if not home page
			if(is_home() || is_front_page()){
				//do nothing
			}else{
				return; 
			}
		}
		
		//check if to display to logged in users
		if($options['efbl_enabe_if_login'])
		if($options['efbl_enabe_if_login'] == 1 ) {
			
			//Do not show when user is not logged in
			if(!is_user_logged_in()) return; 
		}
		
		//check if to display to logged in users
		if($options['efbl_enabe_if_login'])
		if($options['efbl_enabe_if_login'] == 1 ) {
			
			//Do not show when user is not logged in
			if(!is_user_logged_in()) return; 
		}
		
 		
 		//check if to display to not-logged in users
 		if($options['efbl_enabe_if_not_login'])
		if($options['efbl_enabe_if_not_login'] == 1 ) {
			
			//Do not show when user is logged in
			if(is_user_logged_in()) return; 
		}
		
 		//check if to display to not-logged in users
 		if($options['efbl_do_not_show_on_mobile'])
		if($options['efbl_do_not_show_on_mobile'] == 1 ) {
			
			// do not show on mobile 
			if( $mDetect->isMobile() && !$mDetect->isTablet() ) return; 
			 
		} 
		
			/*echo "<pre>";
			print_r($mDetect);
			exit;*/
		 		
 		include('views/public.php');
		
	}
	
	 

}
$efbl = new Easy_Facebook_Likebox();