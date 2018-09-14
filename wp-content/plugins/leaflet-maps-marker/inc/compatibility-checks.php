<?php
/*
    Checks for incompatible plugins and settings - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'compatibility-checks.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . "pluggable.php");
global $wpdb;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$lmm_options = get_option( 'leafletmapsmarker_options' ); //info: required for bing maps api key check

//info: check if bing maps api key is defined
if (( (($lmm_options['standard_basemap'] == 'bingaerial') || ($lmm_options['standard_basemap'] == 'bingaerialwithlabels') || ($lmm_options['standard_basemap'] == 'bingroad'))
|| ((isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1 ))
|| ((isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1 ))
|| ((isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1 ))
) && ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] == NULL )
)) {
	echo '<p><div class="notice notice-warning" style="padding:10px;"><strong>' . __('Warning: you enabled support for bing maps but did not provide an API key. Please visit <a href="http://www.mapsmarker.com/bing-maps" target="_blank">http://www.mapsmarker.com/bing-maps</a> for info on how to get a free bing maps API key!','lmm') . '</strong></div></p>';
}
//info: check if MapQuest api key is defined
if (( (($lmm_options['standard_basemap'] == 'mapquest_osm') || ($lmm_options['standard_basemap'] == 'mapquest_aerial') || ($lmm_options['standard_basemap'] == 'mapquest_hybrid'))
|| ((isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1 ))
|| ((isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1 ))
|| ((isset($lmm_options[ 'controlbox_mapquest_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_hybrid' ] == 1 ))
) && ( isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] == NULL )
)) {
	echo '<p><div class="notice notice-warning" style="padding:10px;"><strong>' . __('Warning','lmm') . ': ' . sprintf(__( 'Since July 11 2016, MapQuest requires to <a href="%1$s" target="_blank">sign up for a plan on their Developer Network</a> if you want to use their maps on your site!','lmm'), 'https://developer.mapquest.com/plans') . '</strong><br/>' . sprintf(__( 'To fix this, please <a href="%1$s">add your MapQuest API key here</a> or disable MapQuest basemaps <a href="%2$s">as available basemaps in the controlbox</a>, <a href="%3$s">as default basemap for new marker/layer maps</a> (if applicable) and <a href="%4$s">as default basemap for minimaps</a> (if applicable).<br/>For more details and a tutorials on how to sign up for a free MapQuest API key (including %5$s transactions/month) <a href="%6$s" target="_blank">please click here</a>.', 'lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-mapquest', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-available_basemaps_controlbox', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-default_basemap', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-minimap', '15.000', 'https://www.mapsmarker.com/mapquest-api-key') . '</div></p>';
}
//info: check if any map is using MapQuest basemaps if no API key is set
if ( isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] == NULL )) {
	$mapquest_maps_markers = $wpdb->get_results("SELECT id FROM `" . $table_name_markers . "` WHERE `basemap` LIKE 'mapquest%' ORDER BY id ASC", ARRAY_A);
	$mapquest_maps_layers = $wpdb->get_results("SELECT id FROM `" . $table_name_layers . "` WHERE `basemap` LIKE 'mapquest%' ORDER BY id ASC", ARRAY_A);
	if (($mapquest_maps_markers != NULL) || ($mapquest_maps_layers != NULL)) {
		echo '<p><div class="notice notice-warning" style="padding:10px;">' . sprintf(__( 'Warning: you did not add your MapQuest API key which is mandatory since July 11 2016. As a result the following existing maps using MapQuest basemaps will automatically switch to OpenStreetMap:','lmm'), 'https://developer.mapquest.com/plans');
		echo '<ul>';
		if ($mapquest_maps_markers != NULL) {
			echo '<li>' . __('marker ID','lmm') . ': ';
			foreach($mapquest_maps_markers as $marker){
				echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $marker['id'] . '">' . $marker['id'] . '</a>, ';
			}
			echo '</li>';
		}
		if ($mapquest_maps_layers != NULL) {
			echo '<li>' . __('layer ID','lmm') . ': ';
			foreach($mapquest_maps_layers as $layer){
				echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $layer['id'] . '">' . $layer['id'] . '</a>, ';
			}
			echo '</li>';
		}
		echo '</ul>';
		echo sprintf(__( 'To fix this, please <a href="%1$s">add your MapQuest API key here</a> or change the basemap used for the maps linked above to OpenStreetMap for example.', 'lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-mapquest');
		echo '</div></p>';
	}
}
//info: plugin JavaScript to Footer
if (is_plugin_active('footer-javascript/footer-javascript.php') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin %1$s which is causing maps created with %2$s to break! Please deactivate that plugin so that maps can be displayed properly.','lmm'), '"Javascript to Footer"', '"Leaflet Maps Marker"' ) . '</div></p>';
}
//info: plugin jQuery Colorbox
if (is_plugin_active('jquery-colorbox/jquery-colorbox.php') ) {
	$lmm_jquery_colorbox_options = get_option( 'jquery-colorbox_settings' );
	if ($lmm_jquery_colorbox_options['autoColorbox'] == TRUE) {
		echo '<p><div class="notice notice-error" style="padding:10px;">' . __('<strong>Warning: you are using the plugin jQuery Colorbox which is causing maps to break!</strong><br/><br/>Here is how to fix this:<br/>1. click on to "Settings" / "jQuery Colorbox" in your WordPress admin menu<br/>2. Uncheck the setting "Automate jQuery Colorbox for all images in pages, posts and galleries:"<br/>3. check the setting "Automate jQuery Colorbox for images in WordPress galleries only:" instead<br/>4. save changes<br/><br/>This message will disappear automatically when the jQuery Colorbox option was updated.','lmm') . '</div></p>';
	}
}
//info: plugin cformsII
if (is_plugin_active('cforms/cforms.php') ) {
	$lmm_cforms_options = get_option( 'cforms_settings' );
	if ($lmm_cforms_options['global'][ 'cforms_show_quicktag_js' ] == FALSE) {
		echo '<p><div class="notice notice-error" style="padding:10px;">' . __('<strong>Warning: you are using the plugin cformsII which is causing the TinyMCE editor to break when creating new maps!</strong><br/><br/>Here is how to fix this:<br/>1. click on to "cformsII" / "Global Settings" in your WordPress admin menu<br/>2. open the tab "WP Editor Button support"<br/>3. check the option "Fix TinyMCE error"<br/>4. save changes<br/><br/>If you do not see this option in your settings, please upgrade to the latest version first (this has to be done manually - see plugin website http://www.deliciousdays.com/cforms-plugin/ for details)<br/><br/>This message will disappear automatically when the cformsII option "Fix TinyMCE error" is checked.','lmm') . '</div></p>';
	}
}
//info: plugin WP Google Analytics
if (is_plugin_active('wp-google-analytics/wp-google-analytics.php') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;"><strong>' . sprintf(__('Warning: you are using the outdated plugin WP Google Analytics which is incompatible with %1$s. Please update to a more current Google analytics plugin like %2$s','lmm'), 'Leaflet Maps Marker', '<a href="https://wordpress.org/extend/plugins/google-analytics-for-wordpress/" target="_blank">Google Analytics by MonsterInsights</a>') . '</strong></div></p>';
}
//info: plugin Better WordPress Minify
if (is_plugin_active('bwp-minify/bwp-minify.php') ) {
	$lmm_bwpminify_options = get_option( 'bwp_minify_general' );
	if ($lmm_bwpminify_options['enable_min_js'] == 'yes') {
		if ((strpos($lmm_bwpminify_options['input_ignore'], 'leafletmapsmarker') === false) || (strpos($lmm_bwpminify_options['input_ignore'], 'jquery-core') === false))  {
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "Better WordPress Minify" which can cause %1$s to break if the option "Minify JS files automatically?" is active. Please disable this option (BWP Minify / General Settings) or navigate to BWP Minify / "Manage Enqueued Files", click on "Scripts to be ignored (not minified)" and add %2$s (one line for each)','lmm'), 'Leaflet Maps Marker', '<strong>leafletmapsmarker-core</strong> & <strong>leafletmapsmarker-addons</strong>') . '</div></p>';
		}
	}
}
//info: plugin WP Minify
if (is_plugin_active('wp-minify/wp-minify.php') ) {
	$lmm_bwpminify_options = get_option( 'wp_minify' );
	if ($lmm_bwpminify_options['enable_html'] == '1') {
			echo '<p><div class="notice notice-error" style="padding:10px;"><strong>' . sprintf(__('Warning: you are using the plugin "WP Minify" which is causing %1$s layer maps to break as the option "Enable HTML Minification" is active. Please disable this option under Settings / WP Minify.','lmm'), 'Leaflet Maps Marker') . '</strong></div></p>';
	}
}
//info: plugin W3 Total Cache check for Minify & CDN
if (is_plugin_active('w3-total-cache/w3-total-cache.php') ) {
	$w3tc_metadata = get_plugin_data(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'w3-total-cache' . DIRECTORY_SEPARATOR . 'w3-total-cache.php');
	//info: check for minify reject settings - not for 0.9.5 which was broken
	if ( (version_compare($w3tc_metadata['Version'],"0.9.5","<")) || (version_compare($w3tc_metadata['Version'],"0.9.5",">")) ) {
		if (function_exists('w3_instance')) { //safety check
			$w3tc_config = w3_instance('W3_Config');
			$w3tc_minify = $w3tc_config->get_boolean('minify.enabled');
			if ($w3tc_minify == true) {
				$w3tc_js = $w3tc_config->get_boolean('minify.js.enable');
				if ($w3tc_js == true) {
						$w3tc_js_exclude = $w3tc_config->get_array('minify.reject.files.js');
						if (in_array('wp-content/plugins/leaflet-maps-marker/leaflet-dist/leaflet.js', $w3tc_js_exclude) == false) {
							echo '<p><div class="notice notice-error" style="padding:10px;"><strong>' . sprintf(__('Warning: you are using the plugin "W3 Total Cache" with the feature "JS Minify" enabled which is causing maps to break.<br/>To fix this, please navigate to <a href="%1s">Performance / Minify / Advanced</a> and add <strong>%2s</strong> to "Never minify the following JS files:"','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=w3tc_minify', 'wp-content/plugins/leaflet-maps-marker/leaflet-dist/leaflet.js') . '</strong></div></p>';
						}
				}
			}
		}
	}
	//info: check for CDN - not needed anymore with 0.9.3
	if (version_compare($w3tc_metadata['Version'],"0.9.3","<")){
		if (function_exists('w3_instance')) { //safety check
			$w3tc_config = w3_instance('W3_Config');
			$w3tc_cdn = $w3tc_config->get_boolean('cdn.enabled');
			if ($w3tc_cdn == true) {
				$w3tc_cdn_exclude = $w3tc_config->get_array('cdn.reject.files');
				if (in_array('wp-content/uploads/leaflet-maps-marker-icons/*', $w3tc_cdn_exclude) == false) {
					echo '<p><div class="notice notice-error" style="padding:10px;"><strong>' . sprintf(__('Warning: you are using the plugin "W3 Total Cache" with the feature "CDN" enabled which is causing layer maps to break.<br/>To fix this, please navigate to <a href="%1s">Performance / CDN / Advanced</a> and add <strong>%2s</strong> to "Rejected files:"','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=w3tc_cdn', 'wp-content/uploads/leaflet-maps-marker-icons/*') . '</strong></div></p>';
				}
			}
		}
	}
}
//info: plugin Root Relative URLs
if (is_plugin_active('root-relative-urls/sb_root_relative_urls.php') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin %1$s which is causing maps created with %2$s to break! Please deactivate that plugin so that maps can be displayed properly.','lmm'), '"Root Relative URLs"', '"Leaflet Maps Marker"' ) . '</div></p>';
}
//info: plugin WP Deferred JavaScripts
if (is_plugin_active('wp-deferred-javascripts/wp-deferred-javascripts.php') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin %1$s which is causing maps created with %2$s to break! Please deactivate that plugin so that maps can be displayed properly.','lmm'), '"WP Deferred JavaScripts"', '"Leaflet Maps Marker"' ) . '</div></p>';
}
//info: Page Builder by SiteOrigin plugin incompatibility
if (is_plugin_active('siteorigin-panels/siteorigin-panels.php') ) {
	$pagebuilder_metadata = get_plugin_data(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'siteorigin-panels' . DIRECTORY_SEPARATOR . 'siteorigin-panels.php');
	if (version_compare($pagebuilder_metadata['Version'],"2.1","<")){
		if ($lmm_options['misc_javascript_header_footer_pro'] == 'footer') {
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the Plugin %1$s which is causing maps to break! To fix this, please navigate to <a href="%2$s">Settings / Misc / Compatibility Settings</a> and set the Option "Where to insert Javascript files on frontend?" to "header (+ inline javascript)".','lmm'), '"Page Builder by SiteOrigin"', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-compatibility' ) . '</div></p>';
		}
	}
}
//info: plugin WP External Links
if (is_plugin_active('wp-external-links/wp-external-links.php') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin %1$s which can cause layer maps to break - please deactivate the plugin %1$s in order to display maps created with %2$s properly!','lmm'), '"WP External Links"', '"Leaflet Maps Marker"' ) . '</div></p>';
}
//info: check if custom mapbox basemaps are used
if ( ($lmm_options[ 'mapbox_user' ] != 'mapbox') || ($lmm_options[ 'mapbox2_user' ] != 'mapbox') || ($lmm_options[ 'mapbox3_user' ] != 'mapbox') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: as Mapbox now requires to use a custom API access token, custom Mapbox basemaps will not work anymore if you registered your Mapbox account after January 2015.<br/>In case your Mapbox maps are broken, please switch to another basemap like OpenStreetMap or <a href="%1$s">upgrade to Maps Marker Pro</a>, which enables you to continue using custom Mapbox basemaps - even with accounts created after January 2015 (please also note that Mapbox might discontinue the usage of their old API for existing users in the long run too!).','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade') . '</div></p>';
}
//info: plugin Autoptimize
if (is_plugin_active('autoptimize/autoptimize.php') ) {
	if (class_exists('autoptimizeConfig')) {
		$conf = autoptimizeConfig::instance();
		if ( ($conf->get('autoptimize_js') == 'on') && (strpos($conf->get('autoptimize_js_exclude'), 'leaflet.js') === false) ) { 
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "Autoptimize" which is currently causing maps to break!<br/>To fix this, please navigate to <a href="%1$s">Autoptimize settings</a>, click on button "Show advanced settings" and add the following to the end the option "Exclude scripts from Autoptimize:": %2$s','lmm'), LEAFLET_WP_ADMIN_URL . 'options-general.php?page=autoptimize', '<strong>,leaflet.js</strong>') . '</div></p>';
		}
		if ($conf->get('autoptimize_js_include_inline') == 'on') {
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "Autoptimize" which is currently causing maps to break!<br/>To fix this, please navigate to <a href="%1$s">Autoptimize settings</a> and uncheck the option "Also aggregate inline JS?"','lmm'), LEAFLET_WP_ADMIN_URL . 'options-general.php?page=autoptimize') . '</div></p>';
		}
	} else {
		echo '<p><div class="notice notice-info" style="padding:10px;">' . __('Warning: you seem to be using an old version of the plugin "Autoptimize" which can currently cause maps to break!<br/>Please update this plugin to the latest version to prevent potential issues.','lmm') . '</div></p>';	
	}
}
//info: check if plugin directory has not been renamed (=broken GeoJSON)
$plugin_basename = explode("/", plugin_basename( __FILE__ ));
if ($plugin_basename[0] != 'leaflet-maps-marker') {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the custom directory name %1$s for the plugin %2$s which causes layer maps to break!<br/>To fix this, please disable the plugin temporarily, rename the current plugin folder located at %3$s to %4$s and re-enable the plugin.','lmm'), '<strong>' . $plugin_basename[0] . '</strong>', '<strong>Leaflet Maps Marker</strong>', WP_PLUGIN_DIR, '<strong>leaflet-maps-marker</strong>') . '</div></p>';
}
//info: compatibility check for Page Builder by SiteOrigin plugin incompatibility + Yoast SEO incompatibility
if ( (is_plugin_active('siteorigin-panels/siteorigin-panels.php')) && (is_plugin_active('wordpress-seo/wp-seo.php')) ) {
	$social_options = get_option( 'wpseo_social' );
	if ($social_options['opengraph'] == 1) {
		echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin %1$s together with %2$s and the current settings can cause maps to break if a %3$s shortcode is added to a %1$s widget!<br/>To fix this, please navigate to <a href="%4$s">SEO / Social / Facebook</a> and disable the option "Add Open Graph meta data".<br/>If you explicitly need a Facebook Open Graph integration though, we recommend to additionally activate the following plugin for that purpose only: %5$s','lmm'), '"Page Builder by SiteOrigin"', '"Yoast SEO"', 'Maps Marker Pro', LEAFLET_WP_ADMIN_URL . 'admin.php?page=wpseo_social#top#facebook', '<a href="https://wordpress.org/support/plugin/wp-facebook-open-graph-protocol" target="_blank">WP Facebook Open Graph protocol</a>' ) . '</div></p>';
	}
}
//info: check if Google Maps API is loaded by other plugins/themes
if ( $lmm_options['google_maps_api_status'] == 'enabled') {
	if ( $lmm_options['google_api_deregister_scripts'] == 'disabled') {
		global $wp_scripts;
		$scripts_found = '';
		if (isset($wp_scripts->registered) && is_array($wp_scripts->registered)) {
			foreach ( $wp_scripts->registered as $script) {    
				if (strpos($script->src, 'maps.google.com/maps/api/js') !== false) {
					$scripts_found .= $script->handle;
				}
				if (strpos($script->src, 'maps.googleapis.com/maps/api/js') !== false) {
					$scripts_found .= $script->handle;
				}
			}
		}
		if ($scripts_found != '') {
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('<b>Warning: an active plugin or theme is also embedding the Google Maps API (script handle: %1$s) - this can break (Google) maps and address search on backend for %2$s!</b><br/>To fix this, please either remove that additional Google Maps API call manually or enable the compatibility option "<a href="%3$s">Deregister Google Maps API scripts enqueued by third parties</a>"','lmm'), $scripts_found, 'Leaflet Maps Marker', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-google_js_api') . '</div></p>';
		}
	}
}
//info: Geo Redirect plugin
if (is_plugin_active('geographical-redirect/geo-redirect.php')) {
	$file = WP_PLUGIN_DIR . '/geographical-redirect/geo-redirect.php';
	if( file_exists($file) ) {
		$contents = file_get_contents($file);
		if( !strpos( $contents, 'geojson' ) ) {
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin %1$s which is currently causing layer maps to break! In order to fix this, please follow <a href="%1$s" target="_blank">this tutorial</a>.','lmm'), '"Geo Redirect"', 'https://www.mapsmarker.com/faqs/how-fix-geo-redirect-conflict/' ) . '</div></p>';
		}
	}
}
//info: WP Rocket
if (is_plugin_active('wp-rocket/wp-rocket.php') ) {
	if (function_exists('get_rocket_option')) { //safety check
		//info: check for JS Minify exclusion
		$wp_rocket_js_exclude = get_rocket_option('exclude_js');
		if (in_array('/wp-content/plugins/leaflet-maps-marker/leaflet-dist/leaflet.js', $wp_rocket_js_exclude) == false) {
			echo '<p><div class="notice notice-error" style="padding:10px;"><strong>' . sprintf(__('Warning: you are using the plugin "WP Rocket" which is currently causing maps to break.<br/>To fix this, please navigate to <a href="%1s">Settings / WP Rocket / Advanced Options</a> and add <strong>%2s</strong> and <strong>%3s</strong> to "JS files to exclude from minification:"','lmm'), LEAFLET_WP_ADMIN_URL . 'options-general.php?page=wprocket', '<code>/wp-content/plugins/leaflet-maps-marker/leaflet-dist/leaflet.js</code>') . '</strong></div></p>';
		}
		//info: check for JS File optimization
		$wp_rocket_js_file_optimiziation = get_rocket_option('minify_js');
		if ($wp_rocket_js_file_optimiziation == 1) {
			echo '<p><div class="notice notice-error" style="padding:10px;"><strong>' . sprintf(__('Warning: you are using the plugin "WP Rocket" with the option "Files optimisation (Minification & Concatenation)" for Javascript enabled, which is causing maps to break! <br/>To fix this, please navigate to <a href="%1s">Settings / WP Rocket / Basic Options / Files optimisation</a>, untick the "<strong>JS</strong>" checkbox and save the change.','lmm'), LEAFLET_WP_ADMIN_URL . 'options-general.php?page=wprocket') . '</strong></div></p>';
		}		
	}
}
//info: plugin Async Javascript
if (is_plugin_active('async-javascript/async-javascript.php') ) {
	if ( get_option( 'aj_enabled' ) == 1 ) {
		$aj_exclusions = get_option('aj_exclusions');
		if ( (strpos($aj_exclusions, 'leaflet-core.js') === false) || (strpos($aj_exclusions, 'leaflet-core.js') === false) || (strpos($aj_exclusions, 'mq-map.js') === false )) { 
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "Async Javascript" which is currently causing maps to break!<br/>To fix this, please navigate to <a href="%1$s">Async JavaScript Settings</a> and add the following to the end the option "Exclusions": %2$s','lmm'), LEAFLET_WP_ADMIN_URL . 'options-general.php?page=async-javascript', '<strong><code>,leaflet-core.js,leaflet-addons.js,mq-map.js</code></strong>') . '</div></p>';
		}
	}
}
//info: WP Super Cache debug check
if (is_plugin_active('wp-super-cache/wp-cache.php') ) {
	global $wp_super_cache_debug, $wp_super_cache_comments;
	if ( (checked( 1, $wp_super_cache_debug, false )) && (checked( 1, $wp_super_cache_comments, false )) ) {
		echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "WP Super Cache" which is currently causing layer maps to break!<br/>To fix this, please navigate to <a href="%1$s">WP Super Cache Settings / Debug</a> and disable debugging or the checkbox above "Display comments at the end of every page like this:"','lmm'), LEAFLET_WP_ADMIN_URL . 'options-general.php?page=wpsupercache&tab=debug') . '</div></p>';
	} 
}
//info: Admin Custom Login
if (is_plugin_active('admin-custom-login/admin-custom-login.php') ) {
	echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "%1$s" which is currently causing the navigation on %2$s settings page to break!<br/>To fix this, please temporarily deactivate this plugin if you want change the settings!','lmm'), 'Admin Custom Login', 'Leaflet Maps Marker') . '</div></p>';
}
//info: Fast Velocity Minify
if (is_plugin_active('fast-velocity-minify/fvm.php') ) {
	if ( get_option( 'fastvelocity_min_disable_js_merge' ) == NULL ) {
		$fvm_options = get_option('fastvelocity_min_ignore');
		if ((strpos($fvm_options, 'leaflet-core.js') === false) || (strpos($fvm_options, 'leaflet-addons.js') === false) || (strpos($fvm_options, 'mq-map.js') === false))  {
			echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the plugin "%1$s" which is currently causing maps to break!<br/>To fix this, please navigate to <a href="%2$s">Advanced settings tab</a>, section "JS and CSS Exceptions" and add the following text to the "Ignore List": %3$s','lmm'), 'Fast Velocity Minify', LEAFLET_WP_ADMIN_URL . 'options-general.php?page=fastvelocity-min&tab=adv', '<br/><strong><code>leaflet-core.js</code><br/><code>leaflet-addons.js</code><br/><code>mq-map.js</code></strong>') . '</div></p>';
		}
	}
}
//info: divi theme script check
if ( (wp_get_theme()->Name == 'Divi') && (version_compare(wp_get_theme()->Version, '3', '>') === TRUE) ) {
	if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
		echo '<p><div class="notice notice-error" style="padding:10px;">' . sprintf(__('Warning: you are using the theme "%1$s" which is currently causing maps to break!<br/>To fix this, please navigate to <a href="%2$s">Settings / Misc / Compatibility Settings</a> and set the Option "Where to insert Javascript files on frontend?" to "header (+ inline javascript)".','lmm'), 'Divi (v' . wp_get_theme()->Version . ')', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-compatibility') . '</div></p>';
	}
}