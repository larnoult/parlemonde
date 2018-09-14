<?php 
/**
 * Plugin Name:       Easy Facebook Likebox
 * Plugin URI:        httt://wordpress.org/plugins/easy-facebook-likebox
 * Description:       Easy Facebook like box WordPress plugin allows you to easly display facebook like box fan page on your website using either widget or shortcode to increase facbook fan page likes. You can use the shortcode generated after saving the facebook like box widget. Additionally it also now allows you to dipslay the cusetomized facebook feed on your website using the same color scheme of your website. Its completely customizable with lots of optional settings. Its also responsive facebook like box at the same time.
 * Version:           4.3.8
 * Author:            MaltaThemes 
 * Author URI:        https://maltathemes.com
 * Text Domain:       easy-facebook-likebox
 * Domain Path:       /languages
 */



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
//error_reporting(0);

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @EasyFacebookLikeBox:
 *
 * - Include class for public `easy-facebook-likebox.php`
 *
 */
 
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/core-functions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/easy-facebook-likebox.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Easy_Facebook_Likebox', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Easy_Facebook_Likebox', 'deactivate' ) );

/*
 * Get instance on plugins loaded
 */
add_action( 'plugins_loaded', array( 'Easy_Facebook_Likebox', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * Get instance of admin area class.
 *
 * The code below is intended to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/easy-facebook-likebox-admin.php' );
	add_action( 'plugins_loaded', array( 'Easy_Facebook_Likebox_Admin', 'get_instance' ) );

}


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * Including widget class.
 */
 
require_once( plugin_dir_path( __FILE__ ) . 'includes/easy-custom-facebook-feed-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/easy-facebook-page-plugin-widget.php' );

// register Foo_Widget widget
function register_fblx_widget() {
	register_widget( 'Easy_Custom_Facebook_Feed_Widget' );
	register_widget( 'Easy_Facebook_Page_Plugin_Widget' );
}
add_action( 'widgets_init', 'register_fblx_widget' );

add_action( 'wp_ajax_efbl_del_trans',  'efbl_del_trans_cb') ;

add_action( 'wp_ajax_nopriv_efbl_del_trans',  'efbl_del_trans_cb') ;


 function efbl_del_trans_cb(){

 	/* Saving ajax value in variable. */ 
	$value = $_POST['efbl_option'];
	
	$replaced_value = str_replace('_transient_', '', $value);

	$efbl_deleted_trans = delete_transient($replaced_value);

	if(isset($efbl_deleted_trans)) echo wp_send_json_success($value); die();

}