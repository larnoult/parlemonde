<?php
/**
 * @package post-volume-stats
 * @version 3.1.17
 */
/*
 * Plugin Name: Post Volume Stats
 * Plugin URI: https://www.postvolumestats.com/
 * Description: Displays the post stats in the admin area with pie and bar charts, also exports tag and category stats to detailed lists and line graphs that can be exported to posts.
 * Author: Neil Ludlow
 * Text Domain: post-volume-stats
 * Version: 3.1.17
 * Author URI: http://www.shortdark.net/
 */

/**************************
 ** PREVENT DIRECT ACCESS
 **************************/

defined('ABSPATH') or die('No script kiddies please!');

if (!defined('WPINC')) {
	die ;
}

// Avoid direct calls to this file.
if (!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/****************
 ** DEFINE
 ****************/

define('SDPVS__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SDPVS__PLUGIN_FOLDER', 'post-volume-stats');
define('SDPVS__VERSION_NUMBER', '3.1.17');

/******************
 ** SETUP THE PAGE
 ******************/

// Add the includes

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_arrays.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_info.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_bar.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_pie.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_lists.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_main.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_subs.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_widget.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_settings.php');

/**********************
 ** ASSEMBLE THE PAGE
 **********************/

// This assembles the plugin page.
function sdpvs_post_volume_stats_assembled() {

	if (is_admin()) {
		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_info = new sdpvsInfo();
		$sdpvs_content = new sdpvsMainContent();

		// Admin notices can be used once they're properly dimissable
		// echo '<div class="notice notice-info is-dismissible"><p class="sdpvs"><strong>' . esc_html__('NEW: You can now export category and tag data directly into a new blog post at the click of a button. There is also a Post Volume Stats widget to add bar charts into your sidebar.', 'post-volume-stats') . '</strong></p></div>';

		// Title
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats', 'post-volume-stats') . '</h1>';

		// Load content for the main page
		$sdpvs_content -> sdpvs_page_content();

		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Div for ajax list box
		echo "<div id='sdpvs_listcontent'>";
		echo "</div>";

		// Div for ajax list box
		echo "<div id='sdpvs_listcompare'>";
		echo "</div>";
	}

	$sdpvs_info -> sdpvs_info();

	// Stop the timer and show the results
	$time_end = microtime(true);
	$elapsed_time = sprintf("%.5f", $time_end - $time_start);
	echo '<p>' . sprintf(esc_html__('Post Volume Stats Version %s, Script time elapsed: %f seconds', 'post-volume-stats'), SDPVS__VERSION_NUMBER, $elapsed_time) . '</p>';

}

// Category page
function sdpvs_category_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_sub = new sdpvsSubPages();

		// Call the method
		$sdpvs_sub -> sdpvs_combined_page_content('category');

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';

		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo '<p>' . sprintf(esc_html__('Post Volume Stats Version %s, Script time elapsed: %f seconds', 'post-volume-stats'), SDPVS__VERSION_NUMBER, $elapsed_time) . '</p>';

	}
	return;
}

// Tag page
function sdpvs_tag_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_sub = new sdpvsSubPages();

		// Call the method
		$sdpvs_sub -> sdpvs_combined_page_content('tag');

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';

		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo '<p>' . sprintf(esc_html__('Post Volume Stats Version %s, Script time elapsed: %f seconds', 'post-volume-stats'), SDPVS__VERSION_NUMBER, $elapsed_time) . '</p>';

	}
	return;
}

// Custom page
function sdpvs_custom_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_sub = new sdpvsSubPages();

		$genoptions = get_option('sdpvs_general_settings');
		$sdpvs_page_value = filter_var ( $_SERVER['QUERY_STRING'], FILTER_SANITIZE_STRING);
		// When changing year the "&settings-updated=true" string is added to the URL!
		preg_match('/^page=post-volume-stats-([^&]*)(&settings-updated=true)?/',$sdpvs_page_value,$matches);
		$customvalue = filter_var ( $matches[1], FILTER_SANITIZE_STRING);

		// Call the method
		$sdpvs_sub -> sdpvs_combined_page_content($customvalue);

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';

		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo '<p>' . sprintf(esc_html__('Post Volume Stats Version %s, Script time elapsed: %f seconds', 'post-volume-stats'), SDPVS__VERSION_NUMBER, $elapsed_time) . '</p>';

	}
	return;
}




// Settings page
function sdpvs_settings_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Content goes here
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Settings', 'post-volume-stats') . '</h1>';

		echo "<form action='" . esc_url(admin_url('options.php')) . "' method='POST'>";
		settings_fields( 'sdpvs_general_option' );
		do_settings_sections( 'post-volume-stats-settings' );
		submit_button('Save');
		echo "</form>";

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo '<p>' . sprintf(esc_html__('Post Volume Stats Version %s, Script time elapsed: %f seconds', 'post-volume-stats'), SDPVS__VERSION_NUMBER, $elapsed_time) . '</p>';
	}
	return;
}

// Settings page
function sdpvs_date_range_select_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Content goes here
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Date Range Select', 'post-volume-stats') . '</h1>';
		echo "<p>On this page you can either select a year or you can select a date range.</p>";
		echo "<p>Selecting a year on this page is an alternative to clicking the bars of the \"Year\" bar chart on the other pages. To de-select the year and view all years together select the blank option at the top.</p>";
		echo "<p>Only if the \"Year\" is blank will the date range be used. You must enter both a start date and an end date. If a date range is entered (with no year selected) it will be applied to the main page, but not the Tag/Category/Custom pages.</p>";

		echo "<form action='" . esc_url(admin_url('options.php')) . "' method='POST'>";
		settings_fields( 'sdpvs_year_option' );
		do_settings_sections( 'post-volume-stats-daterange' );
		submit_button('Save');
		echo "</form>";

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo '<p>' . sprintf(esc_html__('Post Volume Stats Version %s, Script time elapsed: %f seconds', 'post-volume-stats'), SDPVS__VERSION_NUMBER, $elapsed_time) . '</p>';
	}
	return;
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'sdpvs_admin_plugin_settings_link' );
function sdpvs_admin_plugin_settings_link( $links ) { 
	$settings_link = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=post-volume-stats-settings') ) .'">'.__('Settings', 'post-volume-stats').'</a>';
	array_unshift( $links, $settings_link ); 
	return $links; 
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'sdpvs_admin_plugin_pvs_link' );
function sdpvs_admin_plugin_pvs_link( $links ) { 
	$pvs_link = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=post-volume-stats') ) .'">'.__('Post Volume Stats', 'post-volume-stats').'</a>';
	array_unshift( $links, $pvs_link ); 
	return $links; 
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	$genoptions = get_option('sdpvs_general_settings');
	$customoff = filter_var ( $genoptions['customoff'], FILTER_SANITIZE_STRING);
	$customvalue = filter_var ( $genoptions['customvalue'], FILTER_SANITIZE_STRING);
	$showrange = filter_var ( $genoptions['showrange'], FILTER_SANITIZE_STRING);
	add_menu_page(esc_html__('Post Volume Stats', 'post-volume-stats'), esc_html__('Post Volume Stats', 'post-volume-stats'), 'manage_options', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
	add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Categories', 'post-volume-stats'), esc_html__('Categories', 'post-volume-stats'), 'read', 'post-volume-stats-categories', 'sdpvs_category_page');
	add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Tags', 'post-volume-stats'), esc_html__('Tags', 'post-volume-stats'), 'read', 'post-volume-stats-tags', 'sdpvs_tag_page');
	if( "yes" == $customoff and "_all_taxonomies" == $customvalue ){
		// Custom Taxonomies
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		$all_taxes = get_taxonomies( $args );
		$count_taxes = count( $all_taxes );
		if( 1 < $count_taxes ){
			foreach ( $all_taxes as $taxonomy ) {
				if("category" != $taxonomy and "post_tag" != $taxonomy){
					$tax_labels = get_taxonomy($taxonomy);
					add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: ' . $tax_labels->label, 'post-volume-stats'), $tax_labels->label, 'read', 'post-volume-stats-' . $tax_labels->name, 'sdpvs_custom_page');
				}
			}
		}
	}elseif( "yes" == $customoff and "" != $customvalue ){
		$customvalue = filter_var ( $genoptions['customvalue'], FILTER_SANITIZE_STRING);
		$tax_labels = get_taxonomy($customvalue);
		add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: ' . $tax_labels->label, 'post-volume-stats'), $tax_labels->label, 'read', 'post-volume-stats-' . $customvalue, 'sdpvs_custom_page');
	}
	if( "yes" == $showrange ){
		add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Date Range', 'post-volume-stats'), esc_html__('Date Range', 'post-volume-stats'), 'manage_options', 'post-volume-stats-daterange', 'sdpvs_date_range_select_page');
	}
	add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Settings', 'post-volume-stats'), esc_html__('Settings', 'post-volume-stats'), 'manage_options', 'post-volume-stats-settings', 'sdpvs_settings_page');
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/**
 * Load plugin textdomain.
 */
function sdpvs_load_textdomain() {
	load_plugin_textdomain('post-volume-stats', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('init', 'sdpvs_load_textdomain');



/*****************
 ** ADMIN TOOLBAR
 *****************/

// Add Toolbar Menus
function sdpvs_custom_toolbar() {
	global $wp_admin_bar;

	$genoptions = get_option('sdpvs_general_settings');
	$admintool = filter_var ( $genoptions['admintool'], FILTER_SANITIZE_STRING);

	if("yes" == $admintool){
		$url = admin_url("admin.php?page=" . SDPVS__PLUGIN_FOLDER);
		$args = array(
			'id'     => 'sdpvs_link',
			'title'  => __( 'PVS', 'text_domain' ),
			'href'   => $url,
			'group'  => false
		);
		$wp_admin_bar->add_node( $args );
	}

}
add_action( 'wp_before_admin_bar_render', 'sdpvs_custom_toolbar' );





/*************
 ** AJAX...
 *************/

function sdpvs_load_all_admin_scripts() {
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.6', 'screen');

	// Importing external JQuery UI element using "wp-includes/script-loader.php"
	wp_enqueue_script("jquery-ui-draggable");

	wp_register_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__));
	wp_enqueue_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__), array('jquery'), '1.0.3', true);

	$whichdata = "";
	$whichcats = "";
	$whichtags = "";
	$whichcustom = "";
	$comparedata = "";

	//Here we create a javascript object variable called "sdpvs_vars". We can access any variable in the array using sdpvs_vars.name_of_sub_variable
	wp_localize_script('sdpvs_loader', 'sdpvs_vars', array(
	//To use this variable in javascript use "sdpvs_vars.ajaxurl"
	'ajaxurl' => admin_url('admin-ajax.php'),
	//To use this variable in javascript use "sdpvs_vars.whichdata"
	'whichdata' => $whichdata,
	//To use this variable in javascript use "sdpvs_vars.whichcats"
	'whichcats' => $whichcats,
	//To use this variable in javascript use "sdpvs_vars.whichtags"
	'whichtags' => $whichtags,
	//To use this variable in javascript use "sdpvs_vars.whichcustom"
	'whichcustom' => $whichcustom,
	//To use this variable in javascript use "sdpvs_vars.comparedata"
	'comparedata' => $comparedata,
	// nonce...
	'ajax_nonce' => wp_create_nonce('num-of-posts'), ));

}

add_action('admin_enqueue_scripts', 'sdpvs_load_all_admin_scripts');

// Add scripts and stylesheets to the public-facing blog
function sdpvs_load_all_public_scripts() {
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.5', 'screen');
}

add_action('wp_enqueue_scripts', 'sdpvs_load_all_public_scripts');

function sdpvs_process_ajax() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	// create an instance of the list class
	$sdpvs_lists = new sdpvsTextLists();

	// Extract the variable from serialized string
	$gotit = filter_var($_POST['whichdata'], FILTER_SANITIZE_STRING);
	$after_equals = strpos($gotit, "=") + 1;
	$answer = substr($gotit, $after_equals);

	$year = get_option('sdpvs_year_option');
	$searchyear = absint($year['year_number']);
		if(isset($year['start_date'])){
			$start_date = filter_var ( $year['start_date'], FILTER_SANITIZE_STRING);
		}
		if(isset($year['end_date'])){
			$end_date = filter_var ( $year['end_date'], FILTER_SANITIZE_STRING);
		}
	$authoroptions = get_option('sdpvs_author_option');
	$searchauthor = absint($authoroptions['author_number']);

	if ("year" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_year_list($searchauthor);
	} elseif ("hour" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_hour_list($searchyear, $searchauthor, $start_date, $end_date);
	} elseif ("dayofweek" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_dayofweek_list($searchyear, $searchauthor, $start_date, $end_date);
	} elseif ("month" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_month_list($searchyear, $searchauthor, $start_date, $end_date);
	} elseif ("dayofmonth" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_day_of_month_list($searchyear, $searchauthor, $start_date, $end_date);
	} elseif ("author" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_author_list($searchyear, $start_date, $end_date );
	} elseif ("words" == $answer){
		echo $sdpvs_lists -> sdpvs_words_per_post_list($searchyear, $searchauthor, $start_date, $end_date);
	} elseif ("interval" == $answer){
		echo $sdpvs_lists -> sdpvs_interval_between_posts_list($searchyear, $searchauthor, $start_date, $end_date);
	} else {
		echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($answer, $searchyear, $searchauthor, $start_date, $end_date, 'admin', '');
	}

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_get_results', 'sdpvs_process_ajax');

function sdpvs_compare_data_over_years() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	// create an instance of the list class
	$sdpvs_lists = new sdpvsTextLists();

	// Extract the variable from serialized string
	$gotit = filter_var($_POST['comparedata'], FILTER_SANITIZE_STRING);
	$after_equals = strpos($gotit, "=") + 1;
	$answer = substr($gotit, $after_equals);

	$authoroptions = get_option('sdpvs_author_option');
	$searchauthor = absint($authoroptions['author_number']);

	echo $sdpvs_lists -> sdpvs_compare_years_rows($answer, $searchauthor);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_compare_years', 'sdpvs_compare_data_over_years');

function sdpvs_cats_lists() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	$sdpvs_sub = new sdpvsSubPages();

	// Extract the variables from serialized string
	$gotit = filter_var($_POST['whichcats'], FILTER_SANITIZE_STRING);
	preg_match_all('/=([0-9]*)/', $gotit, $matches);

	echo $sdpvs_sub -> update_ajax_lists('category', $matches);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_select_cats', 'sdpvs_cats_lists');

function sdpvs_tags_lists() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	$sdpvs_sub = new sdpvsSubPages();

	// Extract the variables from serialized string
	$gotit = filter_var($_POST['whichtags'], FILTER_SANITIZE_STRING);
	preg_match_all('/=([0-9]*)/', $gotit, $matches);

	echo $sdpvs_sub -> update_ajax_lists('tag', $matches);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_select_tags', 'sdpvs_tags_lists');

function sdpvs_custom_lists() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	$genoptions = get_option('sdpvs_general_settings');
	$customvalue = filter_var ( $genoptions['customvalue'], FILTER_SANITIZE_STRING);

	$sdpvs_sub = new sdpvsSubPages();

	// Extract the variables from serialized string
	$gotit = filter_var($_POST['whichcustom'], FILTER_SANITIZE_STRING);
	preg_match_all('/=([0-9]*)/', $gotit, $matches);

	if("_all_taxonomies" == $customvalue){
		preg_match('/customname=([0-9a-zA-Z\-_]*)&/', $gotit, $filtertype);
		$customvalue = $filtertype[1];
	}

	echo $sdpvs_sub -> update_ajax_lists($customvalue, $matches);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_select_custom', 'sdpvs_custom_lists');

function sdpvs_remove_admin_notice() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	// activate this with AJAX, #sdpvs-notice
	set_transient('sdpvs-admin-notice-004', true, 0);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_admin_notice', 'sdpvs_remove_admin_notice');

/*************
 ** EXPORT...
 *************/

function sdpvs_admin_export_lists() {
	$sdpvs_lists = new sdpvsTextLists();
	$sdpvs_bar = new sdpvsBarChart();

	// Extract the variables from encoded string
	$matches_string = filter_var($_POST['matches'], FILTER_SANITIZE_STRING);
	preg_match_all('/\[([0-9]*)\]/', $matches_string, $matches);

	$year = get_option('sdpvs_year_option');
	$searchyear = absint($year['year_number']);
	$authoroptions = get_option('sdpvs_author_option');
	$searchauthor = absint($authoroptions['author_number']);

	$whichlist = filter_var($_POST['whichlist'], FILTER_SANITIZE_STRING);
	// $howmuch = filter_var($_POST['howmuch'], FILTER_SANITIZE_STRING);

	if (isset($_POST['all'])) {
		$howmuch = "all";
	} elseif (isset($_POST['graph'])) {
		$howmuch = "graph";
	} elseif (isset($_POST['list'])) {
		$howmuch = "list";
	}

	if("" != $searchauthor){
		$user = get_user_by( 'id', $searchauthor );
		$extradesc = ": $user->display_name";
	}else{
		$extradesc = "";
	}

	if ($searchyear)
		$title = ucfirst($whichlist) . ' Stats' . $extradesc;
	else
		$title = ucfirst($whichlist) . ' Stats'. $extradesc;

	$color = $sdpvs_lists -> sdpvs_color_list();
	$link = "https://wordpress.org/plugins/post-volume-stats/";
	$linkdesc = "Post Volume Stats";

	if ("all" == $howmuch or "graph" == $howmuch) {
		$post_content = $sdpvs_bar -> sdpvs_comparison_line_graph($whichlist, $matches, $searchauthor, $color,"y");
	}

	if ("all" == $howmuch or "list" == $howmuch) {
		$post_content .= $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($whichlist, $searchyear, $searchauthor, '','','export', $matches, $color);
	}

	if ("all" == $howmuch or "graph" == $howmuch or "list" == $howmuch) {
		$post_content .= '<p class="alignright">Stats presented with ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';
		$my_post = array('post_title' => $title, 'post_content' => $post_content, 'post_status' => 'draft');
		// Insert the post into the database and get the post ID.
		$post_id = wp_insert_post($my_post, $wp_error);

		$url = admin_url("post.php?post=$post_id&action=edit");

		if (wp_redirect($url)) {
			exit ;
		}
	}

}

add_action('admin_post_export_lists', 'sdpvs_admin_export_lists');



/*****************
 ** CSV DOWNLOAD...
 *****************/

function add_endpoint() {
    add_rewrite_endpoint( 'download-csv', EP_NONE );
}
add_action( 'init', 'add_endpoint' );

function sdpvs_download_redirect() {
	$answer = "";
	$genoptions = get_option('sdpvs_general_settings');
	$exportcsv = filter_var ( $genoptions['exportcsv'], FILTER_SANITIZE_STRING);
	$authoroptions = get_option('sdpvs_author_option');
	$searchauthor = absint($authoroptions['author_number']);
	if("yes"==$exportcsv and is_user_logged_in() ){

		$searchstring = $_SERVER['REQUEST_URI'];
		$pattern = "/\/wp-admin\/download-csv\/([0-9a-zA-Z-]+)\.csv/";
		preg_match($pattern, $searchstring, $matches);
		if( isset($matches[1]) ){
			$answer = $matches[1];
		}

		if("words"!=$answer and "hour"!=$answer and "dayofweek"!=$answer and "month"!=$answer and "dayofmonth"!=$answer and "tag"!=$answer and "category"!=$answer and "interval"!=$answer){
				#check that the taxonomy exists
				$foundit = 0;
				$args = array(
					'public'   => true,
					'_builtin' => false
				);
				$all_taxes = get_taxonomies( $args );
				foreach ( $all_taxes as $taxonomy ) {
					if("category" != $taxonomy and "post_tag" != $taxonomy){
						$tax_labels = get_taxonomy($taxonomy);
						if($taxonomy == $answer or $tax_labels->label == $answer or $tax_labels->name == $answer){
							$foundit = 1;
						}
					}
				}
				if(0 == $foundit){
					return;
				}
		}

		if($answer){
			// create an instance of the list class
			$sdpvs_lists = new sdpvsTextLists();
			$csv = $sdpvs_lists -> sdpvs_create_csv_output($answer, $searchauthor);
			$length = strlen($csv);

			// Download the file.
			ob_clean(); //clear buffer
			header('Content-Disposition: attachment; filename="' . $answer . '.csv"');
			header("Content-Type: application/force-download",true,200);
			header("Content-Length: " . $length);
			header("Pragma: no-cache");
			header("Expires: 0");
			header("Connection: close");
			echo $csv;
			exit();
		}

	}
}
add_action( 'template_redirect', 'sdpvs_download_redirect' );

/*****************
 ** ADMIN NOTICE...
 *****************/

add_action('admin_notices', 'sdpvs_check_activation_notice');
function sdpvs_check_activation_notice() {
	if (!get_transient('sdpvs-admin-notice-004')) {
		# When a new admin notice is added, make sure to change "sdpvs-admin-notice-004" to the next number.
		# Also, remember to update the AJAX to remove the notice with the new number.

		$sdpvs_link = admin_url('admin.php?page=' . SDPVS__PLUGIN_FOLDER);
		echo '<div id="sdpvs-notice" class="notice notice-info is-dismissible"><p class="sdpvs"><strong>' . sprintf(wp_kses(__('NEW to <a href="%1$s">Post Volume Stats</a>: Compare tags and categories in line graphs.', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($sdpvs_link)) . '</strong></p></div>';
	}
}
?>
