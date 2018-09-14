<?php /*
+----+-----+-----+-----+-----+----+-----+-----+-----+-----+-----+-----+
|          . _..::__:  ,-"-"._       |7       ,     _,.__             |
|  _.___ _ _<_>`!(._`.`-.    /        _._     `_ ,_/  '  '-._.---.-.__|
|.{     " " `-==,',._\{  \  / {)     / _ ">_,-' `                mt-2_|
+ \_.:--.       `._ )`^-. "'      , [_/(                       __,/-' +
|'"'     \         "    _L       oD_,--'                )     /. (|   |
|         |           ,'         _)_.\\._<> 6              _,' /  '   |
|         `.         /          [_/_'` `"(                <'}  )      |
+          \\    .-. )          /   `-'"..' `:._          _)  '       +
|   `        \  (  `(          /         `:\  > \  ,-^.  /' '         |
|             `._,   ""        |           \`'   \|   ?_)  {\         |
|                `=.---.       `._._       ,'     "`  |' ,- '.        |
+                  |    `-._        |     /          `:`<_|h--._      +
|                  (        >       .     | ,          `=.__.`-'\     |
|                   `.     /        |     |{|              ,-.,\     .|
|                    |   ,'          \   / `'            ,"     \     |
+                    |  /             |_'                |  __  /     +
|                    | |                                 '-'  `-'   \.|
|                    |/             Leaflet Maps Marker             / |
|                    \.    The most comprehensive & user-friendly   ' |
+                              mapping solution for WordPress         +
|                     ,/           ______._.--._ _..---.---------._   |
|    ,-----"-..?----_/ )      _,-'"             "                  (  |
|.._(                  `-----'                                      `-|
+----+-----+-----+-----+-----+----+-----+-----+-----+-----+-----+-----+
ASCII Map (C) 1998 Matthew Thomas (freely usable as long as this line is included)
Plugin Name: Leaflet Maps Marker
Plugin URI: https://www.mapsmarker.com
Description: The most comprehensive & user-friendly mapping solution for WordPress
Tags: map, maps, Leaflet, OpenStreetMap, geoJSON, json, jsonp, OSM, travelblog, opendata, open data, opengov, open government, ogdwien, WMTS, geoRSS, location, geo, geo-mashup, geocoding, geolocation, travel, mapnick, osmarender, mapquest, geotag, geocaching, gpx, OpenLayers, mapping, bikemap, coordinates, geocode, geocoding, geotagging, latitude, longitude, position, route, tracks, google maps, googlemaps, gmaps, google map, google map short code, google map widget, google maps v3, google earth, gmaps, ar, augmented-reality, wikitude, wms, web map service, geocache, geocaching, qr, qr code, fullscreen, marker, marker icons, layer, multiple markers, karte, blogmap, geocms, geographic, routes, tracks, directions, navigation, routing, location plan, YOURS, yournavigation, ORS, openrouteservice, widget, bing, bing maps, microsoft, map short code, map widget, kml, cross-browser, fully documented, traffic, bike lanes, map short code, custom marker text, custom marker icons and text, gpx
Version: 3.12.1
Author: MapsMarker.com e.U.
Author URI: https://www.mapsmarker.com
Requires at least: 3.3
Tested up to: 4.8
Copyright 2011-2017 - MapsMarker.com e.U. - All rights reserved
MapsMarker &reg;

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License v2 as published by the Free Software Foundation. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You have received a copy of the full GNU General Public License along with this program (see file licence-gpl20.txt)
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-maps-marker.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

function lmm_register_activation_hook_free() {
	global $wp_version;
	if (version_compare($wp_version,"3.3","<")){
		exit('[Leaflet Maps Marker Plugin - installation failed!]: WordPress Version 3.3 or higher is needed for this plugin (you are using version '.$wp_version.') - please upgrade your WordPress installation!');
	}
	if (version_compare(phpversion(),"5.2","<")){
		exit('[Leaflet Maps Marker Plugin - installation failed]: PHP 5.2 is needed for this plugin (you are using PHP '.phpversion().'; note: support for PHP 4 has been officially discontinued since 2007-12-31!) - please upgrade your PHP installation!');
	}
}
register_activation_hook( __FILE__, 'lmm_register_activation_hook_free' );

function lmm_register_deactivation_hook_free() {
	wp_clear_scheduled_hook('lmm_wp_session_garbage_collection');
}
register_deactivation_hook( __FILE__, 'lmm_register_deactivation_hook_free' );

//info: die if pro version is active
if ( is_admin() ) {
	include_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php' );
	if (is_plugin_active('leaflet-maps-marker-pro/leaflet-maps-marker.php') ) {
		if (!is_multisite()) {
			exit('Too bad you want to use the free version again :-( Please deactivate "Maps Marker Pro" first before downgrading to the free version!<br/>Please tell us what we can do to win you as a happy pro user at <a href="https://www.mapsmarker.com/feedback" target="_blank">www.mapsmarker.com/feedback</a> and receive a discount voucher!');
		} else {
			if (is_network_admin()) {
				echo 'Network wide activation of the plugin "Leaflet Maps Marker" failed as the plugin "Maps Marker Pro" is still active on subsites. Please activate "Leaflet Maps Marker" on desired subsites only!<br/>Please tell us what we can do to win you as a happy pro user at <a href="https://www.mapsmarker.com/feedback" target="_blank">www.mapsmarker.com/feedback</a> and receive a discount voucher!<br/><br/>';
			} else {
				echo 'Too bad you want to use the free version again :-( Please deactivate "Maps Marker Pro" first before downgrading to the free version!<br/>Please tell us what we can do to win you as a happy pro user at <a href="https://www.mapsmarker.com/feedback" target="_blank">www.mapsmarker.com/feedback</a> and receive a discount voucher!<br/><br/>';
			}
		}
	}
}

//info: define necessary paths and urls
define( 'LEAFLET_WP_ADMIN_URL', get_admin_url() );
define ("LEAFLET_PLUGIN_URL", plugin_dir_url(__FILE__));
define ("LEAFLET_PLUGIN_DIR", plugin_dir_path(__FILE__));
$lmm_upload_dir = wp_upload_dir();
define ("LEAFLET_PLUGIN_ICONS_URL", $lmm_upload_dir['baseurl'] . "/leaflet-maps-marker-icons");
define ("LEAFLET_PLUGIN_ICONS_DIR", $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons");

class Leafletmapsmarker
{
	function __construct() {
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		add_action('init', array(&$this, 'lmm_load_translation_files'),1);
		add_action('admin_init', array(&$this, 'lmm_load_settings_class'),2);
		add_action('admin_init', array(&$this, 'lmm_install_and_updates'),3); //info: register_action_hook not used as otherwise Wordpress Network installs break

		//info: deregister Google Maps scripts by other plugin&themes
		if ( isset($lmm_options['google_api_deregister_scripts']) && ($lmm_options['google_api_deregister_scripts'] == 'enabled') ){
			add_action('wp_enqueue_scripts', array(&$this, 'lmm_deregister_scripts'),4);
			add_action('wp_head', array(&$this, 'lmm_deregister_scripts'),5);
			add_action('init', array(&$this, 'lmm_deregister_scripts'),6);
			add_action('wp_footer', array(&$this, 'lmm_deregister_scripts'),7);
			add_action('wp_print_scripts', array(&$this, 'lmm_deregister_scripts'),8);
		}

		add_action('wp_enqueue_scripts', array(&$this, 'lmm_frontend_enqueue_scripts'), 5);
		add_action('wp_print_styles', array(&$this, 'lmm_frontend_enqueue_stylesheets'),6);
		add_action('admin_menu', array(&$this, 'lmm_admin_menu'),7);
		add_action('admin_init', array(&$this, 'lmm_plugin_meta_links'),8);
		add_action('admin_bar_menu', array(&$this, 'lmm_add_admin_bar_menu'),149);
		if ( !empty($lmm_options) ) { //info: needed to suppress warning when reseting settings
			add_shortcode($lmm_options['shortcode'], array(&$this, 'lmm_showmap'));
		}
		add_filter('widget_text', 'do_shortcode'); //info: needed for widgets
		if ( isset($lmm_options['misc_global_admin_notices']) && ($lmm_options['misc_global_admin_notices'] == 'show') ){
			add_action('admin_notices', array(&$this, 'lmm_compatibility_checks'));
		}
		if ( !empty($lmm_options) ) { //info: needed to suppress warning when reseting settings
			if ($lmm_options['misc_add_georss_to_head'] == 'enabled') {
				add_action( 'wp_head', array( &$this, 'lmm_add_georss_to_head' ) );
			}
		}

		if ( isset($lmm_options['misc_tinymce_button']) && ($lmm_options['misc_tinymce_button'] == 'enabled') ) {
			require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'tinymce-plugin.php' );
		}
		if ( isset($lmm_options['misc_plugin_language']) && ($lmm_options['misc_plugin_language'] != 'automatic') ){
			add_filter('plugin_locale', array(&$this,'lmm_set_plugin_locale'), 'lmm');
		}
		add_action('widgets_init', create_function('', 'return register_widget("Class_leaflet_recent_marker_widget");'));
		if ( isset($lmm_options['misc_admin_dashboard_widget']) && ($lmm_options['misc_admin_dashboard_widget'] == 'enabled') ){
			if ( !is_multisite() ) {
				add_action('wp_dashboard_setup', array( &$this,'lmm_register_widgets' ));
			} else {
				add_action('wp_network_dashboard_setup', array( &$this,'lmm_register_widgets' ));
				add_action('wp_dashboard_setup', array( &$this,'lmm_register_widgets' ));
			}
		}
		if ( (isset($lmm_options['misc_pointers'])) && ($lmm_options['misc_pointers'] == 'enabled') ) {
			//info: dont show update pointers on new installs
			$version_before_update = get_option('leafletmapsmarker_version_before_update');
			if ($version_before_update != '0') {
				add_action( 'admin_enqueue_scripts', array( $this, 'lmm_update_pointer_admin_scripts' ),1001);
			}
		}
		//info: add features pointers
		add_action( 'admin_enqueue_scripts', array( $this, 'lmm_feature_pointer_admin_scripts' ),1002);
		//info: multisite only - delete tables+options+files if blog deleted from network admin
		if ( is_multisite() ) {
			add_action('delete_blog', array( &$this,'lmm_delete_multisite_blog' ));
		}
		//info: check template files for do_shortcode()-action
		if ( (isset($lmm_options['misc_conditional_css_loading'])) && ($lmm_options['misc_conditional_css_loading'] == 'enabled') ){
			add_action('template_include', array( &$this,'lmm_template_check_shortcode' ));
		}
		//info: style & add extra links to plugin page
		add_action('plugin_row_meta', array( &$this,'lmm_plugins_page_add_links' ), 10, 2);
		add_action( 'admin_enqueue_scripts', array( $this, 'lmm_style_plugins_page' ));
	}
	function lmm_deregister_scripts() {
		global $wp_scripts;
		if (isset($wp_scripts->registered) && is_array($wp_scripts->registered)) {
			foreach ( $wp_scripts->registered as $script) {
				if (strpos($script->src, 'maps.google.com/maps/api/js') !== false) {
					wp_dequeue_script($script->handle);
				}
				if (strpos($script->src, 'maps.googleapis.com/maps/api/js') !== false) {
					wp_dequeue_script($script->handle);
				}
			}
		}
	}
	function lmm_style_plugins_page() {
		global $pagenow;
		if ($pagenow == "plugins.php") {
			$plugin_version = get_option('leafletmapsmarker_version_pro');
			wp_register_style( 'leafletmapsmarker-plugin-styling', LEAFLET_PLUGIN_URL . 'inc/css/leafletmapsmarker-plugins-styling.css', array(), $plugin_version);
			wp_enqueue_style( 'leafletmapsmarker-plugin-styling' );
		}
	}
	function lmm_plugins_page_add_links($links, $file) {
		$plugin = plugin_basename(__FILE__);
		if ($file == $plugin) {
			$go_pro_link = '<a style="float:left;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('Upgrade to pro version for even more features - click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img style="margin-top:4px;margin-right:5px;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-upgrade.png" width="80" height="15" alt="go pro"></a>';
			$affiliate_link = '<a style="text-decoration:none;" title="' . esc_attr__('MapsMarker affiliate program - sign up now and receive commissions up to 50%!','lmm') . '" href="https://affiliates.mapsmarker.com/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-affiliates.png" width="16" height="16" alt="affiliates"></a>';
			$reseller_link = '<a style="text-decoration:none;" title="' . esc_attr__('MapsMarker reseller program - re-sell with a 20% discount!','lmm') . '" href="https://www.mapsmarker.com/reseller" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-resellers.png" width="16" height="16" alt="resellers"></a>';
			$rate_link = '<a style="text-decoration:none;" href="https://www.mapsmarker.com/reviews" target="_blank" title="' . esc_attr__('please rate this plugin on wordpress.org','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-star.png" width="16" height="16" alt="ratings"></a>';
			$translation_link = '<a href="https://translate.mapsmarker.com/projects/lmm" target="_blank" title="' . esc_attr__('translations','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-translations.png" width="16" height="16" alt="translations"></a>';
			$hackerone_link = '<a href="https://www.mapsmarker.com/hackerone" target="_blank" title="' . esc_attr__('Bounty Hunters wanted! Find security bugs to earn cash and licenses','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-hackerone.png" width="16" height="16" alt="hackerone"></a>';
			$fbook_link = '<a href="https://facebook.com/mapsmarker" target="_blank" title="' . esc_attr__('Follow MapsMarker on Facebook','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-facebook.png" width="16" height="16" alt="facebook"></a>';
			$twitter_link = '<a href="https://twitter.com/mapsmarker" target="_blank" title="' . esc_attr__('Follow @MapsMarker on Twitter','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-twitter.png" width="16" height="16" alt="twitter"></a>';
			$googleplus_link = '<a href="https://www.mapsmarker.com/+" target="_blank" title="' . esc_attr__('Follow MapsMarker on Google+','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-google-plus.png" width="16" height="16" alt="google+"></a>';
			$rss_link = '<a href="https://feeds.feedburner.com/MapsMarker" target="_blank" title="RSS"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-rss.png" width="16" height="16" alt="rss"></a>';
			$rss_email_link = '<a href="http://feedburner.google.com/fb/a/mailverify?uri=MapsMarker" target="_blank" title="RSS (via Email)"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-rss-email.png" width="16" height="16" alt="rss-email"></a>';
			$links[] = $go_pro_link . $affiliate_link . '&nbsp;' . $reseller_link . '&nbsp;' . $rate_link . '&nbsp;' . $translation_link . '&nbsp;&nbsp;' . $hackerone_link . '&nbsp;&nbsp;' . $fbook_link . '&nbsp;&nbsp;&nbsp;' . $twitter_link . '&nbsp;&nbsp;&nbsp;' . $googleplus_link . '&nbsp;&nbsp;&nbsp;' . $rss_link . '&nbsp;&nbsp;&nbsp;' . $rss_email_link;
		}
		return $links;
	}
	function lmm_delete_multisite_blog($blog_id) {
		switch_to_blog($blog_id);
		/* Remove tables */
		$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
		$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
		/*remove map icons directory for subsite*/
		$lmm_upload_dir = wp_upload_dir();
		$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
		if (is_dir($icons_directory)) {
			foreach(glob($icons_directory.'*.*') as $v) {
				unlink($v);
			}
			rmdir($icons_directory);
		}
	}
	function lmm_update_pointer_admin_scripts() {
		$page = (isset($_GET['page']) ? $_GET['page'] : '');
		if ($page != 'leafletmapsmarker_pro_upgrade') {
			$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
			$dismissed_pointers = array_flip($dismissed_pointers);
			$do_add_script = false;
			$lmm_version_new = get_option( 'leafletmapsmarker_version' );
			$version_without_dots = "lmmv" . str_replace('.', '', $lmm_version_new);
	
			if ( !isset($dismissed_pointers[$version_without_dots]) ) {
				//info: delete expired lmmv(p)* update pointer IDs for current user
				$current_dismissed_wp_pointers = get_user_meta(get_current_user_id(), "dismissed_wp_pointers");
				$replace_lmmv = preg_replace('/(lmmv(p)?(\\d)+(,)?)/',NULL,$current_dismissed_wp_pointers['0']);
				$replace_without_end_comma = preg_replace('/(,)$/',NULL,$replace_lmmv);
				update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $replace_without_end_comma);
	
				$do_add_script = true;
				add_action( 'admin_print_footer_scripts', array( $this, 'lmm_update_pointer_footer_script' ) );
			}
			if ( $do_add_script ) {
				wp_enqueue_script( 'wp-pointer' );
				wp_enqueue_style( 'wp-pointer' );
			}
		}
	}
	function lmm_update_pointer_footer_script() {
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		$lmm_version_new = get_option( 'leafletmapsmarker_version' );
		$version_without_dots = "lmmv" . str_replace('.', '', $lmm_version_new);
		$pointer_content = '<h3>' . sprintf(esc_attr__('Leaflet Maps Marker plugin update to v%1s was successful','lmm'), $lmm_version_new) . '</h3>';
		//info: for dynamic changelog / multi-user-blog
		if (get_option('leafletmapsmarker_update_info') == 'show') {
			$changelog_url = '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers' . '" style="text-decoration:none;">' . __('changelog','lmm') . '</a>';
		} else {
			$changelog_url = '<a href="https://www.mapsmarker.com/v' . $lmm_version_new . '" style="text-decoration:none;" target="_blank">' . __('changelog','lmm') . '</a>';
		}
		$blogpost_url = '<a href="https://www.mapsmarker.com/v' . $lmm_version_new . '" target="_blank" style="text-decoration:none;">mapsmarker.com</a>';
		$pointer_content .= '<p>' . sprintf(esc_attr__('Please see the %1s for new features or the blog post on %2s for more details','lmm'), $changelog_url, $blogpost_url) . '</p>';
		$pointer_content .= '<hr noshade size="1"/><p><a style="background:#f99755;display:block;padding:5px;text-decoration:none;color:#2702c6;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('Upgrade to pro version for even more features - click here to find out how you can start a free 30-day-trial easily','lmm') . '</a></p>';
	  ?>
		<script type="text/javascript">// <![CDATA[
		jQuery(document).ready(function($) {
			if(typeof(jQuery().pointer) != 'undefined') {
				$('#toplevel_page_leafletmapsmarker_markers').pointer({
					content: '<?php echo $pointer_content; ?>',
					position: {
						edge: 'left',
						align: 'center'
					},
					close: function() {
						$.post( ajaxurl, {
							pointer: '<?php echo $version_without_dots; ?>',
							action: 'dismiss-wp-pointer'
						});
					}
				}).pointer('open');
			}
		});
		// ]]></script>
		<?php
	}
	function lmm_feature_pointer_admin_scripts() {
		$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$dismissed_pointers = array_flip($dismissed_pointers);
		$do_add_script = false;
		//info: add new feature pointer IDs below
		if ( !isset($dismissed_pointers["lmmesw"]) ) {
			$do_add_script = true;
			add_action( 'admin_print_footer_scripts', array( $this, 'lmm_feature_pointer_footer_script' ) );
		}
		if ( $do_add_script ) {
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
		}
	}
	function lmm_feature_pointer_footer_script() {
    	include('inc' . DIRECTORY_SEPARATOR . 'feature-pointers.php');
	}
	function lmm_register_widgets(){
		wp_add_dashboard_widget( 'lmm-admin-dashboard-widget', __('Leaflet Maps Marker - recent markers','lmm'), array( &$this,'lmm_dashboard_widget'), array( &$this,'lmm_dashboard_widget_control'));
	}
	function lmm_dashboard_widget(){
		global $wpdb;
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
		$widgets = get_option( 'dashboard_widget_options' );
		$widget_id = 'lmm-admin-dashboard-widget';
		$number_of_markers =  isset( $widgets[$widget_id] ) && isset( $widgets[$widget_id]['items'] ) ? absint( $widgets[$widget_id]['items'] ) : 4;
		$result = $wpdb->get_results($wpdb->prepare("SELECT `id`,`markername`,`icon`,`createdon`,`createdby` FROM `$table_name_markers` ORDER BY `createdon` desc LIMIT %d", $number_of_markers), ARRAY_A);
		echo '<p><a style="background:#f99755;display:block;padding:5px;text-decoration:none;color:#2702c6;text-align:center;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('Upgrade to pro version for even more features - click here to find out how you can start a free 30-day-trial easily','lmm') . '</a><hr style="border:0;height:1px;background-color:#d8d8d8;"/></p>';
		if ($result != NULL) {
			echo '<table style="margin-bottom:5px;"><tr>';
			foreach ($result as $row ) {
				$icon = ($row['icon'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' : LEAFLET_PLUGIN_ICONS_URL . '/' . $row['icon'];
				echo '<td><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" title="' . esc_attr__('edit marker','lmm') . '"><img src="' . $icon . '" style="width:80%;"></a>';
				echo '<td style="vertical-align:top;line-height:1.2em;">';
				echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" title="' . esc_attr__('edit marker','lmm') . '">'.htmlspecialchars(stripslashes($row['markername'])).'</a><br/>' . __('created on','lmm') . ' ' . date("Y-m-d - h:m", strtotime($row['createdon'])) . ', ' . __('created by','lmm') . ' ' . $row['createdby'];
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
			echo '<p style="margin-bottom:5px;">' . __('No marker created yet','lmm') . '</p>';
		}
		if  ( !isset($widgets[$widget_id]['blogposts']) ) {
			$show_rss = 1;
		} else if ( isset($widgets[$widget_id]['blogposts']) && ($widgets[$widget_id]['blogposts'] == 1) ) {
			$show_rss = 0;
		} else {
			$show_rss = 1;
		}
		//info: use custom name to prevent false malware detection by WordFence plugin
		function lmm_spc_custom_name($string)	{
			return 'mapsmarker-dashboard-widget-rss-item-cache';
		}
		if ($show_rss == 1)	{
				require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-simplepie.php');
				$feed = new SimplePie();
				if ( file_exists(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'readme-icons.txt') ) {
					$feed->enable_cache(true);
					$feed->set_cache_location($location = LEAFLET_PLUGIN_ICONS_DIR);
					$feed->set_cache_name_function('lmm_spc_custom_name');
					$feed->set_cache_duration(86400);
				} else {
					$feed->enable_cache(false);
				}
				$feed->set_feed_url('http://feeds.feedburner.com/MapsMarkerPro');
				$feed->set_stupidly_fast(true);
				$feed->enable_order_by_date(true);
				$feed->init();
				$feed->handle_content_type();
				echo '<hr style="border:0;height:1px;background-color:#d8d8d8;"/><p style="margin:0.5em 0px;font-weight:bold;">' . __('Latest blog posts from www.mapsmarker.com','lmm') . '</p>';
				if ($feed->get_items() == NULL) {
					$blogpost_url = '<a href="https://www.mapsmarker.com/news" target="_blank">https://www.mapsmarker.com/news</a>';
					echo sprintf(__('Feed could not be retrieved, please try again later or read the latest blog posts at %s','lmm'),$blogpost_url);
				}
				foreach ($feed->get_items(0,3) as $item) {
					echo '<p style="margin:0.5em 0;">' . $item->get_date('j F Y') . ': <a href="' . $item->get_permalink() . '?ref=dashboard">' . str_replace('div>', 'span>', $item->get_title()) . '</a></p>'.PHP_EOL;
				}
				echo '<hr style="border:0;height:1px;background-color:#d8d8d8;"/>
				<div style="display:inline-block;"><a style="text-decoration:none;" href="https://www.mapsmarker.com" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-website-home.png" width="16" height="16" alt="mapsmarker.com"> MapsMarker.com</a></div>&nbsp;
				<div style="display:inline-block;"><a style="text-decoration:none;" title="' . esc_attr__('MapsMarker affiliate program - sign up now and receive commissions up to 50%!','lmm') . '" href="https://affiliates.mapsmarker.com/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-affiliates.png" width="16" height="16" alt="' . esc_attr__('MapsMarker affiliate program - sign up now and receive commissions up to 50%!','lmm') . '"> ' . __('Affiliates','lmm') . '</a></div>&nbsp;
				<div style="display:inline-block;"><a style="text-decoration:none;" title="' . esc_attr__('MapsMarker reseller program - re-sell with a 20% discount!','lmm') . '" href="https://www.mapsmarker.com/reseller" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-resellers.png" width="16" height="16" alt="' . esc_attr__('MapsMarker reseller program - re-sell with a 20% discount!','lmm') . '"> ' . __('Resellers','lmm') . '</a></div>&nbsp;
				<div style="display:inline-block;"><a style="text-decoration:none;" href="https://www.mapsmarker.com/reviews" target="_blank" title="' . esc_attr__('please rate this plugin on wordpress.org','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-star.png" width="16" height="16" alt="' . esc_attr__('please rate this plugin on wordpress.org','lmm') . '"> ' . __('rate plugin','lmm') . '</a></div>&nbsp;
				<div style="display:inline-block;"><a href="https://translate.mapsmarker.com/projects/lmm" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-translations.png" width="16" height="16" alt="translations"> ' . __('translations','lmm') . '</a></div>&nbsp;
				<div style="display:inline-block;"><a href="https://www.mapsmarker.com/hackerone" target="_blank" title="' . esc_attr__('Bounty Hunters wanted! Find security bugs to earn cash and licenses','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-hackerone.png" width="16" height="16" alt="hackerone"> hackerone</a></div>&nbsp;
				<div style="display:inline-block;"><a href="https://twitter.com/mapsmarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-twitter.png" width="16" height="16" alt="twitter"> Twitter</a></div>&nbsp;
				<div style="display:inline-block;"><a href="https://facebook.com/mapsmarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-facebook.png" width="16" height="16" alt="facebook"> Facebook</a></div>&nbsp;
				<div style="display:inline-block;"><a href="https://www.mapsmarker.com/+" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-google-plus.png" width="16" height="16" alt="google+"> Google+</a></div>&nbsp;
				<div style="display:inline-block;"><a style="text-decoration:none;" href="https://www.mapsmarker.com/changelog/pro" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-changelog-header.png" width="16" height="16" alt="changelog"> ' . __('Changelog','lmm') . '</a></div>&nbsp;
				<div style="display:inline-block;"><a href="https://feeds.feedburner.com/MapsMarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-rss.png" width="16" height="16" alt="rss"> RSS</a></div>&nbsp;
				<div style="display:inline-block;"><a href="http://feedburner.google.com/fb/a/mailverify?uri=MapsMarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-rss-email.png" width="16" height="16" alt="rss-email"> ' . __('E-Mail','lmm') . '</a></div>';
		}
	}
	function lmm_dashboard_widget_control(){
		$widget_id = 'lmm-admin-dashboard-widget';
		$form_id = 'lmm-admin-dashboard-widget-control';
		$update = false;
		if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		  $widget_options = array();
		if ( !isset($widget_options[$widget_id]) ) {
		//info: set default value
		  $widget_options[$widget_id] = array(
				'blogposts' => 0,
				'items' => 5
		  );
		  $update = true;
		}
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$form_id]) ) {
		  $number = ($_POST[$form_id]['items'] == NULL) ? '3' : absint( $_POST[$form_id]['items'] );
		  //$number = absint( $_POST[$form_id]['items'] );
		  $blogposts = isset($_POST[$form_id]['blogposts']) ? '1' : '0';
		  $widget_options[$widget_id]['items'] = $number;
		  $widget_options[$widget_id]['blogposts'] = $blogposts;
		  $update = true;
		}
		if($update) update_option( 'dashboard_widget_options', $widget_options );
		$number = isset( $widget_options[$widget_id]['items'] ) ? (int) $widget_options[$widget_id]['items'] : '';
		echo '<p><label for="lmm-admin-dashboard-widget-number">' . __('Number of markers to show:') . ' </label>';
		echo '<input id="lmm-admin-dashboard-widget-number" name="'.$form_id.'[items]" type="text" value="' . $number . '" size="2" /></p>';
		echo '<p><label for="lmm-admin-dashboard-widget-blogposts">' . __('Hide blog posts and link section:') . ' </label>';
		echo '<input id="lmm-admin-dashboard-widget-blogposts" name="'.$form_id.'[blogposts]" type="checkbox" ' . checked($widget_options[$widget_id]['blogposts'],1,false) . '/></p>';
	}
	function lmm_load_translation_files() {
		load_plugin_textdomain('lmm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
	function lmm_set_plugin_locale( $lang ) {
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		global $locale;
		if ($lmm_options['misc_plugin_language_area'] == 'backend') {
			return is_admin() ? $lmm_options['misc_plugin_language'] : $locale;
		} else if ($lmm_options['misc_plugin_language_area'] == 'frontend') {
			return is_admin() ? $locale : $lmm_options['misc_plugin_language'];
		} else if ($lmm_options['misc_plugin_language_area'] == 'both') {
			return $lmm_options['misc_plugin_language'];
		} else {
			return $locale;
		}
	}
	function lmm_compatibility_checks() {
		include('inc' . DIRECTORY_SEPARATOR . 'compatibility-checks.php');
	}
	function lmm_help() {
		include('leaflet-help-credits.php');
	}
	function lmm_settings() {
		global $lmm_options_class;
		$lmm_options_class->display_page();
	}
	function lmm_list_layers() {
		include('leaflet-list-layers.php');
	}
	function lmm_list_markers() {
		include('leaflet-list-markers.php');
	}
	function lmm_layer() {
		include('leaflet-layer.php');
	}
	function lmm_marker() {
		include('leaflet-marker.php');
	}
	function lmm_import_export() {
		include('leaflet-import-export.php');
	}
	function lmm_tools() {
		include('leaflet-tools.php');
	}
	function lmm_apis(){
		if( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		settings_errors();
		echo '<div class="wrap">';
		require_once LEAFLET_PLUGIN_DIR . 'inc/admin-header.php';
		require_once LEAFLET_PLUGIN_DIR . 'inc/admin-content-apis.php';
		require_once LEAFLET_PLUGIN_DIR . 'inc/admin-footer.php';
	}
	function lmm_add_georss_to_head() {
		$georss_to_head = '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' GeoRSS-Feed" href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=all" />'.PHP_EOL;
		echo $georss_to_head;
	}
	function lmm_showmap($atts) {
		require('inc' . DIRECTORY_SEPARATOR . 'showmap.php');
		return $lmm_out;
	}
	function lmm_load_settings_class() {
		if ( is_admin() ) {
			require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'class-leaflet-options.php' );
			global $lmm_options_class;
			$lmm_options_class = new Class_leaflet_options();
		}
	}
	function lmm_admin_menu() {
		global $wp_version;
		if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) { //info: for mp6 theme compatibility
			$mp6_icon = '-white';
		} else {
			$mp6_icon = '';
		}
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( !empty($lmm_options) ) { //info: needed to suppress warning when reseting settings
			$capabilities = $lmm_options[ 'capabilities_edit' ];
		} else {
			$capabilities = 'edit_posts';
		}
		$page = add_menu_page('Maps Marker', 'Maps Marker', $capabilities, 'leafletmapsmarker_markers', array(&$this, 'lmm_list_markers'), LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-page' . $mp6_icon . '.png', '25.071' );
		if ( !empty($lmm_options) ) { //info: needed to suppress warning when reseting settings
			$page2 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('List all markers', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-list' . $mp6_icon . '.png"> ' . __('List all markers', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_markers', array(&$this, 'lmm_list_markers') );
			$page3 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('add/edit marker', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-add' . $mp6_icon . '.png"> ' . __('Add new marker', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_marker', array(&$this, 'lmm_marker') );
			$page4 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('List all layers', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-list' . $mp6_icon . '.png"> ' . __('List all layers', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_layers', array(&$this, 'lmm_list_layers') );
			$page5 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('add/edit layer', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-add' . $mp6_icon . '.png"> ' . __('Add new layer', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_layer', array(&$this, 'lmm_layer') );
			$page8 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('Support', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-help' . $mp6_icon . '.png"> ' . __('Support', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_help', array(&$this, 'lmm_help') );
		} else {
			$page = '';
			$page2 = '';
			$page3 = '';
			$page4 = '';
			$page5 = '';
			$page8 = '';
		}
		if ( !empty($lmm_options) ) { //info: needed to suppress warning when reseting settings
			$page3b = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('Import/Export', 'lmm'), '<hr noshade size="1" style="margin-top:0px;"/><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-import-export' . $mp6_icon . '.png"> ' . __('Import/Export', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_import_export', array(&$this, 'lmm_import_export') );
		} else {
			$page3b = '';
		}
		$page6 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('Tools', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-tools' . $mp6_icon . '.png"> ' . __('Tools', 'lmm'), 'activate_plugins','leafletmapsmarker_tools', array(&$this, 'lmm_tools') );
		$page7 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('Settings', 'lmm'), '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-settings' . $mp6_icon . '.png"> ' . __('Settings', 'lmm'), 'activate_plugins', 'leafletmapsmarker_settings', array(&$this, 'lmm_settings') );
		$page11 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - Maps Marker API', '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-api' . $mp6_icon . '.png"> ' . __('Maps Marker APIs', 'lmm'), 'activate_plugins', 'leafletmapsmarker_apis', array(&$this, 'lmm_apis') );
		if ( !empty($lmm_options) ) { //info: needed to suppress warning when reseting settings
			$page10 = add_submenu_page('leafletmapsmarker_markers', 'Maps Marker - ' . __('Upgrade to Pro', 'lmm'), '<div style="background:#F99755;color:#000;padding:3px;line-height:1.8em;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-up.png"> ' . __('Upgrade to Pro', 'lmm') . '&nbsp;&nbsp;&nbsp;</div>', $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_pro_upgrade', array(&$this, 'lmm_pro_upgrade') );
		} else {
			$page10 = '';
		}
		//info: add javascript - leaflet.js - for admin area
		add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_admin_enqueue_scripts'),7);
		add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_admin_enqueue_scripts'),8);
		add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_admin_selectjs'),9);
		add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_admin_selectjs'),9);
		add_action('admin_print_scripts-'.$page7, array(&$this, 'lmm_admin_selectjs'),9);
		//info: add leaflet css styles for map pages
		add_action('admin_print_styles-'.$page3, array(&$this, 'lmm_admin_enqueue_stylesheets_leaflet'),19);
		add_action('admin_print_styles-'.$page5, array(&$this, 'lmm_admin_enqueue_stylesheets_leaflet'),19);
		//info: add css styles for admin area
		add_action('admin_print_styles-'.$page, array(&$this, 'lmm_admin_enqueue_stylesheets'),17);
		add_action('admin_print_styles-'.$page2, array(&$this, 'lmm_admin_enqueue_stylesheets'),18);
		add_action('admin_print_styles-'.$page3, array(&$this, 'lmm_admin_enqueue_stylesheets'),19);
		add_action('admin_print_styles-'.$page3b, array(&$this, 'lmm_admin_enqueue_stylesheets'),19);
		add_action('admin_print_styles-'.$page4, array(&$this, 'lmm_admin_enqueue_stylesheets'),20);
		add_action('admin_print_styles-'.$page5, array(&$this, 'lmm_admin_enqueue_stylesheets'),21);
		add_action('admin_print_styles-'.$page6, array(&$this, 'lmm_admin_enqueue_stylesheets'),22);
		add_action('admin_print_styles-'.$page7, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);
		add_action('admin_print_styles-'.$page8, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);
		add_action('admin_print_styles-'.$page10, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);
		add_action('admin_print_styles-'.$page11, array(&$this, 'lmm_admin_enqueue_stylesheets'),26);
		//info: add css for adminbar entry for MP6
		add_action('admin_enqueue_scripts', array(&$this, 'lmm_admin_enqueue_stylesheets_adminbar'),25);
		//info: add contextual help on all pages
		add_action('admin_print_scripts-'.$page, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page2, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page3b, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page4, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page6, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page7, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page8, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page10, array(&$this, 'lmm_add_contextual_help'));
		add_action('admin_print_scripts-'.$page11, array(&$this, 'lmm_add_contextual_help'));
	}
	function lmm_pro_upgrade() {
		include('leaflet-pro-upgrade.php');
	}
	function lmm_add_admin_bar_menu() {
		global $wp_version;
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( $lmm_options[ 'admin_bar_integration' ] == 'enabled' && current_user_can($lmm_options[ 'capabilities_edit' ]) )
		{
			global $wp_admin_bar;
			//info: mp6 only for WP3.8+
			if ( version_compare( $wp_version, '3.8-alpha', '>' ) ) {
				$mp6_icon = '-white';
				$admin_bar_main = '<span class="ab-icon"></span><span class="ab-label">Maps Marker</span>';
			} else {
				$admin_bar_main = '<img style="float:left;margin:3px 5px 0 0;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-tinymce.png"/> Maps Marker';
				$mp6_icon = '';
			}
			$menu_items = array(
				array(
					'id' => 'lmm',
					'title' => $admin_bar_main,
					'href' => '',
					'meta' => array( 'title' => 'Wordpress-Plugin ' . __('by','lmm') . ' www.mapsmarker.com' )
				),
				array(
					'id' => 'lmm-markers',
					'parent' => 'lmm',
					'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-list' . $mp6_icon . '.png"> ' . __('List all markers','lmm'),
					'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers'
				),
				array(
					'id' => 'lmm-add-marker',
					'parent' => 'lmm',
					'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-add' . $mp6_icon . '.png"> ' . __('Add new marker','lmm'),
					'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker'
				),
				array(
					'id' => 'lmm-layers',
					'parent' => 'lmm',
					'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-list' . $mp6_icon . '.png"> ' . __('List all layers','lmm'),
					'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers'
				),
				array(
					'id' => 'lmm-add-layers',
					'parent' => 'lmm',
					'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-add' . $mp6_icon . '.png"> ' . __('Add new layer','lmm'),
					'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer'
				),
				array(
					'id' => 'lmm-help-credits',
					'parent' => 'lmm',
					'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-help' . $mp6_icon . '.png"> ' . __('Support','lmm'),
					'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_help'
				),
				array(
					'id' => 'lmm-import-export',
					'parent' => 'lmm',
					'title' => '<hr style="margin:3px 0;" noshade size="1"/><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-import-export' . $mp6_icon . '.png"> ' . __('Import/Export','lmm'),
					'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_import_export'
				)
			);
			if ( current_user_can( 'activate_plugins' ) ) {
				$menu_items = array_merge($menu_items, array(
					array(
						'id' => 'lmm-tools',
						'parent' => 'lmm',
						'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-tools' . $mp6_icon . '.png"> ' . __('Tools','lmm'),
						'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools'
					),
					array(
						'id' => 'lmm-settings',
						'parent' => 'lmm',
						'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-settings' . $mp6_icon . '.png"> ' . __('Settings','lmm'),
						'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings'
					),
					array(
						'id' => 'lmm-api',
						'parent' => 'lmm',
						'title' => '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-api' . $mp6_icon . '.png"> Maps Marker APIs',
						'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_apis'
					)
				));
			}
			$menu_items = array_merge($menu_items, array(
					array(
						'id' => 'lmm-upgrade',
						'parent' => 'lmm',
						'title' => '<span style="background:#F99755;color:#000;padding:3px;text-shadow:none;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-up.png"> ' . __('Upgrade to Pro','lmm') . '&nbsp;&nbsp;</span>',
						'href' => LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade'
					)
				));

			foreach ($menu_items as $menu_item) {
				$wp_admin_bar->add_menu($menu_item);
			}
		}
	}
	function lmm_add_contextual_help() {
		$helptext = '<p>' . __('Do you have questions or issues? Please use the following support channels appropriately.','lmm') . '<br/>';
		$helptext .= '<strong>' . __('One personal request: before you post a new support ticket in the <a href="http://wordpress.org/support/plugin/leaflet-maps-marker" target="_blank">Wordpress Support Forum</a>, please follow the instructions from <a href="http://www.mapsmarker.com/readme-first" target="_blank">http://www.mapsmarker.com/readme-first</a> which give you a guideline on how to deal with the most common issues.','lmm') . '</strong></p>';
		$helptext .= '<ul>';
		$helptext .= '<li><a href="https://www.mapsmarker.com/faq/" target="_blank">' . __('FAQ','lmm') . '</a> (' . __('frequently asked questions','lmm') . ')</li>';
		$helptext .= '<li><a href="https://www.mapsmarker.com/docs/" target="_blank">' . __('Documentation','lmm') . '</a></li>';
		$helptext .= '<li><a href="http://wordpress.org/support/plugin/leaflet-maps-marker" target="_blank">WordPress Support Forum</a> (' . __('free community support','lmm') . ')</li>';
		$helptext .= '</ul>';
		$helptext .= '<a style="background:#f99755;display:block;padding:5px 5px 5px 10px;text-decoration:none;color:#2702c6;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL .
	'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('If you want to get dedicated 1:1 support from the plugin author, please upgrade to the pro version. Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>';
		$screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'lmm_help_tab', 'title' => __('Help & Support','lmm'), 'content' => $helptext ));
	}
	function lmm_admin_selectjs() {
		//info: dequeue bootstrap from 3rd party plugins
		global $wp_scripts;
		if (isset($wp_scripts->registered) && is_array($wp_scripts->registered)) {
			foreach ( $wp_scripts->registered as $script) {
				if ( (strpos($script->handle, 'bootstrap') !== false) || (strpos($script->src, 'bootstrap') !== false) ) {
					wp_dequeue_script($script->handle);
				}
			}
		}
		$plugin_version = get_option('leafletmapsmarker_version');
		wp_enqueue_script( array ( 'jquery' ) );
		wp_enqueue_script( 'leafletmapsmarker-bootstrap-tabs', LEAFLET_PLUGIN_URL . 'inc/js/bootstrap-tabs.min.js', array('jquery'), $plugin_version);
		wp_enqueue_script( 'leafletmapsmarker-select2', LEAFLET_PLUGIN_URL . 'inc/js/select2/select2.min.js', array('jquery'), $plugin_version);
		wp_enqueue_style( 'leafletmapsmarker-select2', LEAFLET_PLUGIN_URL . 'inc/js/select2/select2.css', array(), $plugin_version);
		wp_localize_script('leafletmapsmarker-select2', 'mapsmarkerjs_selectjs', array(
				'settings_search_placeholder' => __( 'start full-text search', 'lmm'),
				'settings_search_no_results' => __( 'No matches found', 'lmm'),
				'lmm_current_page' => (isset($_GET['page']) ? $_GET['page'] : '')
		) );
	}
	function lmm_frontend_enqueue_scripts() {
		global $locale;
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		$plugin_version = get_option('leafletmapsmarker_version');
		
		//info: Bing culture code for script localization
		if ($lmm_options['bingmaps_culture'] == 'automatic') {
			if ( $locale != NULL ) { $bing_culture = str_replace("_","-", $locale); } else { $bing_culture =  'en_us'; }
		} else {
			$bing_culture = $lmm_options['bingmaps_culture'];
		}
		
		if ($lmm_options['google_maps_api_status'] == 'enabled') {
			//info: Google language localization (JSON API)
			if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
				$google_language = '';
			} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
				if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
			} else {
				$google_language = "&language=" . $lmm_options['google_maps_language_localization'];
			}
			if ($lmm_options['google_maps_base_domain_custom'] == '') {
				$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain'];
			} else {
				$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain_custom'];
			}
			//info: Google API key
			if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
			//info: register or enqueue scripts
			if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
				wp_register_script( 'leafletmapsmarker-googlemaps-loader', 'https://www.google.com/jsapi'.$google_maps_api_key, array(), 3.7, true);
				wp_register_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array('leafletmapsmarker-googlemaps-loader', 'jquery' ), $plugin_version, true);
				wp_register_script( 'show_map', LEAFLET_PLUGIN_URL . 'inc/js/show_map.js', array('leafletmapsmarker' ), $plugin_version, true);				
			} else {
				wp_enqueue_script( array ( 'jquery' ) );
				wp_enqueue_script( 'leafletmapsmarker-googlemaps-loader', 'https://www.google.com/jsapi'.$google_maps_api_key, array(), NULL);
				wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array('leafletmapsmarker-googlemaps-loader'), $plugin_version);
			}
			wp_localize_script('leafletmapsmarker', 'mapsmarkerjs', array(
				'zoom_in' => __( 'Zoom in', 'lmm' ),
				'zoom_out' => __( 'Zoom out', 'lmm' ),
				'google_maps_api_status' => $lmm_options['google_maps_api_status'],
				'googlemaps_language' => $google_language,
				'googlemaps_base_domain' => $gmaps_base_domain,
				'google_maps_api_key' => esc_js(trim($lmm_options['google_maps_api_key'])),
				'bing_culture' => $bing_culture
			) );
		} else { //info: next: google_maps_api_status = disabled
			//info: register or enqueue scripts
			if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
				wp_register_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array( 'jquery' ), $plugin_version, true);
				wp_register_script( 'show_map', LEAFLET_PLUGIN_URL . 'inc/js/show_map.js', array('leafletmapsmarker' ), $plugin_version, true);				
			} else {
				wp_enqueue_script( array ( 'jquery' ) );
				wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array( 'jquery' ), $plugin_version);
			}
			wp_localize_script('leafletmapsmarker', 'mapsmarkerjs', array(
				'zoom_in' => __( 'Zoom in', 'lmm' ),
				'zoom_out' => __( 'Zoom out', 'lmm' ),
				'google_maps_api_status' => $lmm_options['google_maps_api_status'],
				'bing_culture' => $bing_culture
			) );
		}
		//info: load MapQuest script
		if ($lmm_options['mapquest_api_key'] != NULL) {
			if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
				wp_register_script( 'leafletmapsmarker-mapquest', 'https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])), array('leafletmapsmarker'), $plugin_version, false);
			} else {
				wp_enqueue_script( 'leafletmapsmarker-mapquest', 'https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])), array('leafletmapsmarker'), $plugin_version, false);
			}
		}
	}
	function lmm_admin_enqueue_scripts() {
		global $locale;
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		$plugin_version = get_option('leafletmapsmarker_version');
		if ( $locale != NULL ) { $lang = substr($locale, 0, 2); } else { $lang =  'en'; }
		
		//info: Bing culture code
		if ($lmm_options['bingmaps_culture'] == 'automatic') {
			if ( $locale != NULL ) { $bing_culture = str_replace("_","-", $locale); } else { $bing_culture =  'en_us'; }
		} else {
			$bing_culture = $lmm_options['bingmaps_culture'];
		}
		
		//info: geocoding libraries
		wp_enqueue_script( 'leafletmapsmarker-autocomplete-loader', LEAFLET_PLUGIN_URL . 'inc/js/autocomplete.jquery.min.js', array( 'jquery' ), $plugin_version);
		wp_enqueue_script( 'leafletmapsmarker-geocoding-loader', LEAFLET_PLUGIN_URL . 'inc/js/geocoding.js', array( 'jquery' ), $plugin_version);
		
		if ($lmm_options['google_maps_api_status'] == 'enabled') {
		
			//info: Google language localization (JSON API)
			if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
				$google_language = '';
			} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
				if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
			} else {
				$google_language = "&language=" . $lmm_options['google_maps_language_localization'];
			}
			if ($lmm_options['google_maps_base_domain_custom'] != '') {
				$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain'];
			} else {
				$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain_custom'];
			}
			wp_enqueue_script( array ( 'jquery' ) );
			//info: Google API key
			if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
			wp_enqueue_script( 'leafletmapsmarker-googlemaps-loader', 'https://www.google.com/jsapi'.$google_maps_api_key, array(), 3.7, true);			
			//info: load leaflet.js + plugins
			wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array('leafletmapsmarker-googlemaps-loader'), $plugin_version);
			
			wp_localize_script('leafletmapsmarker', 'mapsmarkerjs', array(
			'zoom_in' => __( 'Zoom in', 'lmm' ),
			'zoom_out' => __( 'Zoom out', 'lmm' ),
			'google_maps_api_status' => $lmm_options['google_maps_api_status'],
			'googlemaps_language' => $google_language,
			'googlemaps_base_domain' => $gmaps_base_domain,
			'google_maps_api_key' => esc_js(trim($lmm_options['google_maps_api_key'])),
			'bing_culture' => $bing_culture			
			) );
		} else { //info: next: google_maps_api_status = disabled
			wp_enqueue_script( array ( 'jquery' ) );
			wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array('jquery'), $plugin_version);
			wp_localize_script('leafletmapsmarker', 'mapsmarkerjs', array(
			'zoom_in' => __( 'Zoom in', 'lmm' ),
			'zoom_out' => __( 'Zoom out', 'lmm' ),
			'google_maps_api_status' => $lmm_options['google_maps_api_status'],
			'bing_culture' => $bing_culture
			) );
		}

		//info: needed for geocoding.js
		wp_localize_script('leafletmapsmarker', 'lmm_ajax_vars', array(
			'lmm_ajax_nonce' => wp_create_nonce('lmm-ajax-nonce'),
			'lmm_ajax_leaflet_plugin_url' => LEAFLET_PLUGIN_URL,
			'lmm_ajax_admin_url' => LEAFLET_WP_ADMIN_URL)
		);
		
		//info: load MapQuest script
		if ($lmm_options['mapquest_api_key'] != NULL) {	
			wp_enqueue_script( 'leafletmapsmarker-mapquest', 'https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])), array('leafletmapsmarker'), NULL, false);
		}
	}
	function lmm_frontend_enqueue_stylesheets() {
		//info: conditional loading of css files
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( function_exists( 'is_rtl' ) && is_rtl() ) {
			$css_enqueue_handle = 'leafletmapsmarker-rtl';
			$css_file_name = 'leaflet-rtl.css';
		} else {
			$css_enqueue_handle = 'leafletmapsmarker';
			$css_file_name = 'leaflet.css';
		}
		if ( (isset($lmm_options['misc_conditional_css_loading'])) && ($lmm_options['misc_conditional_css_loading'] == 'enabled') ){
				global $wp_query;
				$posts = $wp_query->posts;
				$pattern = get_shortcode_regex();

				$plugin_version = get_option('leafletmapsmarker_version');
				global $wp_styles;
				wp_register_style($css_enqueue_handle, LEAFLET_PLUGIN_URL . 'leaflet-dist/' . $css_file_name, array(), $plugin_version);

				if (is_array($posts)) {
					foreach ($posts as $post) {
						if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( $lmm_options['shortcode'], $matches[2] ) ) {
							wp_enqueue_style($css_enqueue_handle);
							break;
						}
					}
					//info: override max image width in popups
					$lmm_custom_css = ".leaflet-popup-content img { " . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . " }";
						wp_add_inline_style($css_enqueue_handle,$lmm_custom_css);
				}
		} else {
				global $wp_styles;
				$plugin_version = get_option('leafletmapsmarker_version');
				wp_register_style($css_enqueue_handle, LEAFLET_PLUGIN_URL . 'leaflet-dist/' . $css_file_name, array(), $plugin_version);
				wp_enqueue_style($css_enqueue_handle);
				//info: override max image width in popups
				$lmm_custom_css = ".leaflet-popup-content img { " . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . " }";
					wp_add_inline_style($css_enqueue_handle,$lmm_custom_css);
		}
	}
	function lmm_template_check_shortcode( $template ) {
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		$searchterm = '[' . $lmm_options['shortcode'];
		$files = array( $template, get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'header.php', get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'footer.php' );
		if ( function_exists( 'is_rtl' ) && is_rtl() ) {
			$css_enqueue_handle = 'leafletmapsmarker-rtl';
			$css_file_name = 'leaflet-rtl.css';
		} else {
			$css_enqueue_handle = 'leafletmapsmarker';
			$css_file_name = 'leaflet.css';
		}
		foreach( $files as $file ) {
			if( file_exists($file) ) {
				$contents = file_get_contents($file);
				if( strpos( $contents, $searchterm )  ) {
					global $wp_styles;
					$plugin_version = get_option('leafletmapsmarker_version');
					wp_register_style($css_enqueue_handle, LEAFLET_PLUGIN_URL . 'leaflet-dist/' . $css_file_name, array(), $plugin_version);
					wp_enqueue_style($css_enqueue_handle);
					break;
				}
			}
		}
		return $template;
	}
	function lmm_admin_enqueue_stylesheets() {
		$plugin_version = get_option('leafletmapsmarker_version');
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( function_exists( 'is_rtl' ) && is_rtl() ) {
			wp_register_style( 'leafletmapsmarker-admin-rtl', LEAFLET_PLUGIN_URL . 'inc/css/leafletmapsmarker-admin-rtl.css', array(), $plugin_version);
			wp_enqueue_style('leafletmapsmarker-admin-rtl' );
			//info: override max image width in popups
			$lmm_custom_css = ".leaflet-popup-content img { " . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . " }";
			wp_add_inline_style('leafletmapsmarker-admin-rtl',$lmm_custom_css);
		} else {
			wp_register_style( 'leafletmapsmarker-admin', LEAFLET_PLUGIN_URL . 'inc/css/leafletmapsmarker-admin.css', array(), $plugin_version);
			wp_enqueue_style('leafletmapsmarker-admin' );
			//info: override max image width in popups
			$lmm_custom_css = ".leaflet-popup-content img { " . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . " }";
			wp_add_inline_style('leafletmapsmarker-admin',$lmm_custom_css);
		}
	}
	function lmm_admin_enqueue_stylesheets_leaflet() {
		global $wp_styles;
		$plugin_version = get_option('leafletmapsmarker_version');
		if ( function_exists( 'is_rtl' ) && is_rtl() ) {
			$css_enqueue_handle = 'leafletmapsmarker-rtl';
			$css_file_name = 'leaflet-rtl.css';
		} else {
			$css_enqueue_handle = 'leafletmapsmarker';
			$css_file_name = 'leaflet.css';
		}
		wp_register_style( $css_enqueue_handle, LEAFLET_PLUGIN_URL . 'leaflet-dist/' . $css_file_name, array(), $plugin_version);
		wp_enqueue_style( $css_enqueue_handle );
	}
	function lmm_admin_enqueue_stylesheets_adminbar() {
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( $lmm_options[ 'admin_bar_integration' ] == 'enabled' && current_user_can($lmm_options[ 'capabilities_edit' ]) )
		{
			$plugin_version = get_option('leafletmapsmarker_version');
			wp_register_style( 'leafletmapsmarker-admin-adminbar', LEAFLET_PLUGIN_URL . 'inc/css/leafletmapsmarker-admin-adminbar.css', array(), $plugin_version);
			wp_enqueue_style( 'leafletmapsmarker-admin-adminbar' );
		}
	}
	function lmm_install_and_updates() {
		//info: set transient to execute install & update-routine only once a day
		$current_version = "v3121"; //2do - mandatory: change on each update to new version!
		$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version;
		$install_update_schedule = get_transient( $schedule_transient );
		if ( $install_update_schedule === FALSE ) {
			$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version;
			set_transient( $schedule_transient, 'execute install and update-routine only once a day', 60*60*24 );
			include('inc' . DIRECTORY_SEPARATOR . 'install-and-updates.php');
		}
	}
	function lmm_plugin_meta_links() {
		define( 'FB_BASENAME', plugin_basename( __FILE__ ) );
		define( 'FB_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
		define( 'FB_FILENAME', str_replace( FB_BASEFOLDER.'/', '', plugin_basename(__FILE__) ) );
		function leafletmapsmarker_filter_plugin_meta($links, $file) {
			if ( $file == FB_BASENAME ) {
				array_unshift(
					$links,
					'<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">'.__('Markers','lmm').'</a>',
					'<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers">'.__('Layers','lmm').'</a>' ,
					'<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings">'.__('Settings','lmm').'</a>'
				);
			}
			return $links;
		}
		add_filter( 'plugin_action_links', 'leafletmapsmarker_filter_plugin_meta', 10, 2 );
	  } //info: end plugin_meta_links()
} //info: end class
$run_leafletmapsmarker = new Leafletmapsmarker();
//info: include widget class
require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'class-leaflet-recent-marker-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'class-google-places-geocoding.php' );
unset($run_leafletmapsmarker);
?>