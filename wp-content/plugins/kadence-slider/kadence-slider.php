<?php

/*
Plugin Name: Kadence Slider
Description: Responsive image slider with css animations for layers.
Version: 2.2.5
Author: Kadence Themes
Author URI: http://kadencethemes.com/
License: GPLv2 or later
*/
function kadence_slider_activation() {
}
register_activation_hook(__FILE__, 'kadence_slider_activation');

function kadence_slider_deactivation() {
}
register_deactivation_hook(__FILE__, 'kadence_slider_deactivation');

// Define constants
add_action('plugins_loaded', 'kadence_slider_init');
function kadence_slider_init() {
	define('KADENCE_SLIDER_PATH', realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR );
	define('KADENCE_SLIDER_URL', plugin_dir_url(__FILE__) );

	define('KS_VERSION', '2.2.5' );
	define('KS_DEBUG', false);

	// Legacy Slider
	require_once( KADENCE_SLIDER_PATH . 'kadence-slider-legacy.php');

	// Frontoutput class
	require_once( KADENCE_SLIDER_PATH . 'kadence-slider-pro-frontend.php');
	KadenceSliderPro_Output::addShortcode();

	// Admin functions
	require_once( KADENCE_SLIDER_PATH . 'admin/ks-manage.php');
	require_once( KADENCE_SLIDER_PATH . 'admin/database.php');
	require_once( KADENCE_SLIDER_PATH . 'admin/typography/typography.php');

	if(is_admin()) {
		if(KS_DEBUG || KS_VERSION != get_option('ksp_version')) {
		  KadenceSliderProDatabase::setDatabase();
		}
		if(KS_VERSION != get_option('ksp_version')) {
		  KadenceSliderProDatabase::ksp_setversion();
		}

		KadenceSliderAdmin::ksp_init_admin();
		KadenceSliderAdmin::ksp_hook_admin_scripts();

		require_once (KADENCE_SLIDER_PATH . 'admin/ajax_functions.php');
		require_once (KADENCE_SLIDER_PATH . 'admin/slider_preview.php');
	}
}
// Frontend Scripts
function kadence_slider_scripts() {
	wp_enqueue_style( 'kadence_slider_css',  KADENCE_SLIDER_URL . 'css/ksp.css', array(), '220' );
  	wp_enqueue_script('kadence_slider_js', KADENCE_SLIDER_URL . 'js/min/ksp-min.js', array('jquery'), '220', true);
}
add_action('wp_enqueue_scripts', 'kadence_slider_scripts', 100);
add_action( 'wp_enqueue_scripts', 'ksp_remove_scripts', 160 );
function ksp_remove_scripts(){
  	global $kadence_slider;
  	if(isset($kadence_slider['ksp_load_fonts']) && $kadence_slider['ksp_load_fonts'] == 0) {
   		wp_dequeue_style('redux-google-fonts-kadence_slider');
  	}
}




function ksp_get_image_id_by_link($link){
    global $wpdb;
    $link = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link);

    return $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE BINARY guid='$link'");
}

// Plugin Updates

require_once('wp-updates-plugin.php');
$kad_slider_updater = new PluginUpdateChecker_2_0 ('https://kernl.us/api/v1/updates/5679e8dd6f276b6452e41eb4/',__FILE__, 'kadence-slider', 1);