<?php
/*
     Fullscreen standalone maps - Maps Marker Pro
*/
//info redirect to permalink if file is being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-fullscreen.php') {
	while (!is_file('wp-load.php')) {
		if (is_dir('..' . DIRECTORY_SEPARATOR)) {
			chdir('..' . DIRECTORY_SEPARATOR);
		} else {
			die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
		}
	}
	require_once('wp-load.php');
	if (isset($_GET['layer'])) {
		$layer = esc_html($_GET['layer']);
		unset($_GET['layer']);
		$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
		wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/fullscreen/layer/' . $layer . '/' . $argv), 301);
	} elseif (isset($_GET['marker'])) {
		$marker = esc_html($_GET['marker']);
		unset($_GET['marker']);
		$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
		wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/fullscreen/marker/' . $marker . '/' . $argv), 301);
	}
	exit;
}

//info: check if plugin is active (didnt use is_plugin_active() due to problems reported by users)
function lmm_is_plugin_active( $plugin ) {
	$active_plugins = get_option('active_plugins');
	$active_plugins = array_flip($active_plugins);
	if ( isset($active_plugins[$plugin]) || lmm_is_plugin_active_for_network( $plugin ) ) { return true; }
}
function lmm_is_plugin_active_for_network( $plugin ) {
	if ( !is_multisite() )
		return false;
	$plugins = get_site_option( 'active_sitewide_plugins');
	if ( isset($plugins[$plugin]) )
				return true;
	return false;
}
if (!lmm_is_plugin_active('leaflet-maps-marker-pro/leaflet-maps-marker.php') ) {
	echo sprintf(__('The plugin "Maps Marker Pro" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), antispambot(get_bloginfo('admin_email')) );
} else {
global $wpdb, $allowedtags, $locale, $current_user, $is_chrome, $is_safari;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();
//info: initiate filter_details
$filter_details = false;
//info: set custom marker icon dir/url
if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
	$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
} else {
	$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
}
//info: set marker shadow url
if ( $lmm_options['defaults_marker_icon_shadow_url_status'] == 'default' ) {
	if ( $lmm_options['defaults_marker_icon_shadow_url'] == NULL ) {
		$marker_shadow_url = '';
	} else {
		$marker_shadow_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png';
	}
} else {
	$marker_shadow_url = esc_url($lmm_options['defaults_marker_icon_shadow_url']);
}
$plugin_version = get_option('leafletmapsmarker_version_pro');
if (get_query_var('layer', false)) {
	$layer = intval(get_query_var('layer'));
	$uid = substr(md5(''.rand()), 0, 8);
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$row = $wpdb->get_row($wpdb->prepare('SELECT `id`,`name`,`basemap`,`mapwidth`,`mapheight`,`mapwidthunit`,`panel`,`layerzoom`,`layerviewlat`,`layerviewlon`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`multi_layer_map`,`multi_layer_map_list`,`clustering`,`gpx_url`,`gpx_panel`, `mlm_filter`, `mlm_filter_details` FROM `'.$table_name_layers.'` WHERE `id` = %d',$layer), ARRAY_A);
	$id = $row['id'];
	//info: get filter sort order
	$filters_active_sort_order = ($lmm_options['mlm_filter_active_sort_order'] == 'DESC')?SORT_DESC:SORT_ASC;
	$filters_inactive_sort_order = ($lmm_options['mlm_filter_inactive_sort_order'] == 'DESC')?SORT_DESC:SORT_ASC;
	$filter_details = json_decode($row['mlm_filter_details'], true);
	if($filter_details){
		$filter_show_markercount = (isset($lmm_options['mlm_filter_controlbox_markercount']) && $lmm_options['mlm_filter_controlbox_markercount'] == '1')?'1':'0';
		$filter_show_icon = (isset($lmm_options['mlm_filter_controlbox_icon']) && $lmm_options['mlm_filter_controlbox_icon'] == '1')?'1':'0';
		$filter_show_name = (isset($lmm_options['mlm_filter_controlbox_name']) && $lmm_options['mlm_filter_controlbox_name'] == '1')?'1':'0';
		//info: in case all of the 3 attributes unchecked, display the name
		if($filter_show_markercount == '0' && $filter_show_icon == '0' && $filter_show_name == '0'){
			$filter_show_name = '1';
		}
		$active_layers_order  = $lmm_options['mlm_filter_active_sort_order'];
		$active_layers_orderby  = ($lmm_options['mlm_filter_active_orderby'] == 'id')?'layer_id':$lmm_options['mlm_filter_active_orderby'];
		//info: order active layers
		$prepare_active_ordered_filters = array();
		foreach($filter_details as $key => $value){
			if($value['status'] == 'active'){
				$prepare_active_ordered_filters[$key] = $value;
				$filter_details[$key]['markercount'] = intval($wpdb->get_var( "SELECT COUNT(*) FROM $table_name_markers WHERE layer LIKE concat('%\"',$key,'\"%')" ));
				$prepare_active_ordered_filters[$key]['markercount'] = intval($filter_details[$key]['markercount']);
				$filter_details[$key]['name'] = stripslashes($filter_details[$key]['name']);
				$filter_details[$key]['icon'] = esc_url($filter_details[$key]['icon']);
			}
		}
		if($lmm_options['mlm_filter_active_orderby'] == 'id'){
			if($filters_active_sort_order === SORT_DESC){
				krsort( $prepare_active_ordered_filters );
			}else{
				ksort( $prepare_active_ordered_filters );
			}
		}else{
			$prepare_active_ordered_filters = MMP_Globals::array_sort( $prepare_active_ordered_filters , $lmm_options['mlm_filter_active_orderby'], $filters_active_sort_order);
		}
		//info: order inactive layers
		$prepare_inactive_ordered_filters = array();
		foreach($filter_details as $key => $value){
			if($value['status'] == 'inactive'){
				$prepare_inactive_ordered_filters[$key] = $value;
				$filter_details[$key]['markercount'] =  intval($wpdb->get_var( "SELECT COUNT(*) FROM $table_name_markers WHERE layer LIKE concat('%\"',$key,'\"%')" ));
				$prepare_inactive_ordered_filters[$key]['markercount'] = intval($filter_details[$key]['markercount']);
				$filter_details[$key]['name'] = stripslashes($filter_details[$key]['name']);
				$filter_details[$key]['icon'] = esc_url($filter_details[$key]['icon']);
			}
		}
		if($lmm_options['mlm_filter_inactive_orderby'] == 'id'){
			if($filters_inactive_sort_order === SORT_DESC){
				krsort( $prepare_inactive_ordered_filters );
			}else{
				ksort( $prepare_inactive_ordered_filters );
			}
		}else{
			$prepare_inactive_ordered_filters = MMP_Globals::array_sort( $prepare_inactive_ordered_filters, $lmm_options['mlm_filter_inactive_orderby'], $filters_inactive_sort_order);
		}
		//info: combine active and inactive filters
		$ordered_filters = array();
		$i = 0;
		foreach($prepare_active_ordered_filters as $layer_id => $detail){
			$ordered_filters[$i] = $detail;
			$ordered_filters[$i]['id'] = $layer_id;
			$i++;
		}
		foreach($prepare_inactive_ordered_filters as $layer_id => $detail){
			$ordered_filters[$i] = $detail;
			$ordered_filters[$i]['id'] = $layer_id;
			$i++;
		}
	}

	$layername = $row['name'];
	$basemap = $row['basemap'];
	//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
	if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
	} else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
	}
	//info: GoogleMutant fallback for unsupported browsers (automatically switch to OSM)
	if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($lmm_options['google_maps_plugin'] == 'google_mutant') ) {
		if ( MMP_Globals::check_google_mutant_fallback() === TRUE ) {
			$google_mutant_fallback = TRUE;
			if (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) {
				$basemap = 'osm_mapnik';
			}
		} else {
			$google_mutant_fallback = FALSE;
		}
	} else {
		$google_mutant_fallback = FALSE; //info: to avoid PHP undefined warnings
	}
	$lat = $row['layerviewlat'];
	$lon = $row['layerviewlon'];
	$zoom = $row['layerzoom'];
	$mapwidth = $row['mapwidth'];
	$mapheight = $row['mapheight'];
	$mapwidthunit = $row['mapwidthunit'];
	$panel = $row['panel'];
	$paneltext = ($row['name'] == NULL) ? '&nbsp;' : htmlspecialchars( stripslashes( MMP_Globals::translate_single_string($row['name'], "Layer (ID {$layer}) name") ) );
	$controlbox = $row['controlbox'];
	$overlays_custom = $row['overlays_custom'];
	$overlays_custom2 = $row['overlays_custom2'];
	$overlays_custom3 = $row['overlays_custom3'];
	$overlays_custom4 = $row['overlays_custom4'];
	$wms = $row['wms'];
	$wms2 = $row['wms2'];
	$wms3 = $row['wms3'];
	$wms4 = $row['wms4'];
	$wms5 = $row['wms5'];
	$wms6 = $row['wms6'];
	$wms7 = $row['wms7'];
	$wms8 = $row['wms8'];
	$wms9 = $row['wms9'];
	$wms10 = $row['wms10'];
	$mapname = 'mapsmarker_'.$uid;
	$mapname_js = 'layermap_' . intval($layer);
	$multi_layer_map = $row['multi_layer_map'];
	$multi_layer_map_list = $row['multi_layer_map_list'];
	if (empty($multi_layer_map_list)) {
		if($filter_details){
			$multi_layer_map_list = implode(',', array_keys($prepare_active_ordered_filters));
		}else{
			$multi_layer_map_list = $row['multi_layer_map_list'];
		}
	} else {
		$multi_layer_map_list = esc_sql($multi_layer_map_list);
	}
	$clustering = $row['clustering'];
	$gpx_url = esc_url($row['gpx_url']);
	$gpx_panel = $row['gpx_panel'];
	//info: prepare controlbox collapsed option js variable
	if ( isset($row['mlm_filter']) && $row['mlm_filter'] == '1' ){ $filters_collapsed = 'true'; }elseif($row['mlm_filter'] == '2'){ $filters_collapsed = 'false'; }else{ $filters_collapsed = 'hidden'; }
	//info: check if layer/marker ID exists
	if ($row == NULL) {
		$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), $layer);
		echo $error_layer_not_exists . '<br/>';
		echo '<a href="https://www.mapsmarker.com" target="_blank" title="' . esc_attr__('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-deleted-image.png"></a><br/>';
	} else {

	//info: starting output on frontend
	$lmm_out = '<!DOCTYPE html>'.PHP_EOL;
	$lmm_out .= '<!--[if IE 8]>'.PHP_EOL;
	$lmm_out .= '<html id="ie8" dir="ltr" lang="' . substr($locale, 0, 2) . '">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->'.PHP_EOL;
	$lmm_out .= '<html dir="ltr" lang="' . substr($locale, 0, 2) . '">'.PHP_EOL;
	$lmm_out .= '<!--<![endif]-->'.PHP_EOL;
	$lmm_out .= '<head>'.PHP_EOL;
	if ($layername == '') { $title_layername = get_bloginfo('name'); } else { $title_layername = htmlspecialchars(stripslashes($layername)); }
	$lmm_out .= '<title>' . $title_layername;
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$lmm_out .= ' - ' . __('powered by','lmm') . ' MapsMarker.com';
	}
	$lmm_out .=  ' - ' . get_bloginfo('name') . '</title>'.PHP_EOL;
	$lmm_out .= '<meta charset="UTF-8" />'.PHP_EOL;
	$lmm_out .= '<meta name="geo.position" content="' . $lat . ';' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="ICBM" content="' . $lat . ', ' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="page-type" content="' . __('map','lmm') . '" />'.PHP_EOL;
	//info: viewport + mobile web app settings, details: https://gist.github.com/jdaihl/472519 & https://gist.github.com/tfausak/2222823 & http://developer.apple.com/library/ios/#documentation/userexperience/conceptual/mobilehig/IconsImages/IconsImages.html
	$lmm_out .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">'.PHP_EOL;
	$lmm_out .= '<meta name="apple-mobile-web-app-capable" content="yes">'.PHP_EOL;
	$lmm_out .= '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">'.PHP_EOL;
	$lmm_out .= '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
	if ( $lmm_options['map_webapp_images'] == 'default' ) {
		$ios_icon_57 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-57x57.png';
		$ios_icon_114 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-retina-114x114.png';
		$ios_icon_72 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-72x72.png';
		$ios_icon_144 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-retina-144x144.png';
		$ios_launch_1024 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-1024x748.png';
		$ios_launch_2048 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-retina-2048x1496.png';
		$ios_launch_768 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-768x1004.png';
		$ios_launch_1536 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-retina-1536x2008.png';
		$ios_launch_320 = LEAFLET_PLUGIN_URL . 'inc/img/iso-launch-image-iphone-320x460.png';
		$ios_launch_640 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x920.png';
		$ios_launch_640_1096 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x1096.png';
	} else if ( $lmm_options['map_webapp_images'] == 'custom' ) {
		$ios_icon_57 = esc_url($lmm_options['map_webapp_icon57']);
		$ios_icon_114 = esc_url($lmm_options['map_webapp_icon114']);
		$ios_icon_72 = esc_url($lmm_options['map_webapp_icon72']);
		$ios_icon_144 = esc_url($lmm_options['map_webapp_icon144']);
		$ios_launch_1024 = esc_url($lmm_options['map_webapp_launch1024']);
		$ios_launch_2048 = esc_url($lmm_options['map_webapp_launch2048']);
		$ios_launch_768 = esc_url($lmm_options['map_webapp_launch768']);
		$ios_launch_1536 = esc_url($lmm_options['map_webapp_launch1536']);
		$ios_launch_320 = esc_url($lmm_options['map_webapp_launch320']);
		$ios_launch_640 = esc_url($lmm_options['map_webapp_launch640']);
		$ios_launch_640_1096 = esc_url($lmm_options['map_webapp_launch640_1096']);
	}
	if ( $lmm_options['map_webapp_images'] != 'none' ) {
		$lmm_out .= '<link rel="apple-touch-icon" href="' . $ios_icon_57 . '">'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon-precomposed" href="' . $ios_icon_57 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon" sizes="114x114" href="' . $ios_icon_114 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon" sizes="72x72" href="' . $ios_icon_72 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon" sizes="144x144" href="' . $ios_icon_144 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_1024 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_2048 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_768 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_1536 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_320 . '" media="screen and (max-device-width: 320px)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_640 . '" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_640_1096 . '" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" />'.PHP_EOL;
	}
	if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-rtl-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-rtl.min.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	} else {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.min.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	}
	$lmm_out .= '<style type="text/css" id="leafletmapsmarker-image-css-override">.leaflet-popup-content img { ' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . ' } .marker-cluster-small {	background-color: ' . htmlspecialchars($lmm_options['clustering_color_small']) . '; } .marker-cluster-small div { background-color: ' . htmlspecialchars($lmm_options['clustering_color_small_inner']) . '; color: ' . htmlspecialchars($lmm_options['clustering_color_small_text']) . '; } .marker-cluster-medium { background-color: ' . htmlspecialchars($lmm_options['clustering_color_medium']) . '; } .marker-cluster-medium div { background-color: ' . htmlspecialchars($lmm_options['clustering_color_medium_inner']) . '; color: ' . htmlspecialchars($lmm_options['clustering_color_medium_text']) . '; } .marker-cluster-large { background-color: ' . htmlspecialchars($lmm_options['clustering_color_large']) . '; } .marker-cluster-large div { background-color: ' . htmlspecialchars($lmm_options['clustering_color_large_inner']) . '; color: ' . htmlspecialchars($lmm_options['clustering_color_large_text']) . '; }</style>'.PHP_EOL;

	//info: Google API key
	if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {
				$lmm_out .= '<script type="text/javascript" src="https://www.google.com/jsapi' . $google_maps_api_key . '"></script>'.PHP_EOL;
		} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {
			if ($lmm_options['google_maps_api_deferred_loading'] == 'disabled') {
				$lmm_out .= '<script type="text/javascript" src="https://www.google.com/jsapi' . $google_maps_api_key . '"></script>'.PHP_EOL;
			}
		}
	}

	//info: Google language localization (JSON API)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
	} else {
		$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
	}
	if ($lmm_options['google_maps_base_domain_custom'] == 'maps.google.com') {
		$gmaps_base_domain = "&base_domain=" . esc_html($lmm_options['google_maps_base_domain']);
	} else {
		$gmaps_base_domain = "&base_domain=" . esc_html($lmm_options['google_maps_base_domain_custom']);
	}

	//info: Google Maps styling
	$google_styling_json = ($lmm_options['google_styling_json'] == NULL) ? 'disabled' : str_replace("\"", "'", $lmm_options['google_styling_json']);

  	//info: Bing culture code
	if ($lmm_options['bingmaps_culture'] == 'automatic') {
		if ( $locale != NULL ) { $bing_culture = str_replace("_","-", $locale); } else { $bing_culture =  'en_us'; }
	} else {
		$bing_culture = $lmm_options['bingmaps_culture'];
	}
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= 'var mapsmarkerjspro = {"zoom_in":"' . __('Zoom in','lmm') . '","zoom_out":"' . __('Zoom out','lmm') . '","googlemaps_language":"' . $google_language . '","googlemaps_base_domain":"' . $gmaps_base_domain . '","google_maps_api_key":"' . esc_js(trim($lmm_options['google_maps_api_key'])) . '","bing_culture":"' . $bing_culture . '","google_styling_json":"' . $google_styling_json . '","minimap_show":"' . __( 'Show minimap', 'lmm' ) .'","minimap_hide":"' . __( 'Hide minimap', 'lmm' ) .'","minimap_status":"' . $lmm_options['minimap_status'] . '","fullscreen_button_title":"' . __('View fullscreen','lmm') . '","fullscreen_button_title_exit":"' . __('Exit fullscreen','lmm') . '","fullscreen_button_position":"' . esc_js($lmm_options['map_fullscreen_button_position']) . '","maxzoom":"' . intval($lmm_options['global_maxzoom_level']) . '","google_maps_api_status":"' . esc_js($lmm_options['google_maps_api_status']) . '","meters":"' . __('meters','lmm') . '","feet":"' . __('feet','lmm') . '","gpx_icons_status":"' . esc_js($lmm_options['gpx_icons_status']) . '","google_deferred_loading":"' . esc_js($lmm_options['google_maps_api_deferred_loading']) . '","google_maps_plugin":"' . esc_js($lmm_options['google_maps_plugin']) . '"};'.PHP_EOL;
	$lmm_out .= '/* ]]> */'.PHP_EOL;
	$lmm_out .= '</script>'.PHP_EOL;
	$lmm_out .= '<style>form { margin: 0 ; } </style>'.PHP_EOL; //info: for layer controlbox
	$lmm_out .= '<script type="text/javascript" src="' . includes_url( 'js/jquery/jquery.js' ) . '"></script>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-core.js?ver=' . $plugin_version . '"></script>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-addons.js?ver=' . $plugin_version . '"></script>'.PHP_EOL;

	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= '<script type="text/javascript" src="https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])) . '"></script>'.PHP_EOL;
	}
	$lmm_out .= '</head>'.PHP_EOL;
	$lmm_out .= '<body style="margin:0;padding:0;height:100%;background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . ';overflow:hidden;">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		if ( function_exists( 'is_rtl' ) && is_rtl() ) { $panel_fullscreen_text = 'text-align:right;'; } else { $panel_fullscreen_text = 'text-align:left;'; }
		$lmm_out .= '<div id="panel_top_' . $uid . '" class="lmm-panel" style="' . $panel_fullscreen_text . 'background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . '; width:99%; padding:5px;">'.PHP_EOL;
		$lmm_out .= '<span style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ])) . '">' . $paneltext . '</span><span class="lmm-panel-api-fullscreen">';
		if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/layer/' . $id .'/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/layer/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
			if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; }
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $geojson_api_link . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/layer/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</span></div>'.PHP_EOL;
	}

	//info: set margin top & hide api icon links for iOS fullscreen view
	$lmm_out .= '<script type="text/javascript">if (window.navigator.standalone == true) { document.body.style.margin = "21px 0 0 0"; document.getElementById("lmm-panel-api-fullscreen").style.display = "none"; } </script>'.PHP_EOL;

	//info: add gpx panel
	if ($gpx_url != NULL) {
		$gpx_panel_state = ($gpx_panel == 1) ? 'block' : 'none';
		$lmm_out .= '<div id="gpx-panel-' . $uid . '" class="gpx-panel" style="display:' . $gpx_panel_state . '; background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . ';">'.PHP_EOL;
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') { $gpx_unit_distance = 'km'; $gpx_unit_elevation = 'm'; } else { $gpx_unit_distance = 'mi'; $gpx_unit_elevation = 'ft'; }
		if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
			$gpx_metadata_name = '<label for="gpx-name">' . __('Track name','lmm') . ':</label> <span id="gpx-name" class="gpx-name"></span>';
		} else { $gpx_metadata_name = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
			$gpx_metadata_start = '<label for="gpx-start">' . __('Start','lmm') . ':</label> <span id="gpx-start" class="gpx-start"></span>';
		} else { $gpx_metadata_start = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
			$gpx_metadata_end = '<label for="gpx-end">' . __('End','lmm') . ':</label> <span id="gpx-end" class="gpx-end"></span>';
		} else { $gpx_metadata_end = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
			$gpx_metadata_distance = '<label for="gpx-distance">' . __('Distance','lmm') . ':</label> <span id="gpx-distance"><span class="gpx-distance"></span> ' . $gpx_unit_distance . '</span>';
		} else { $gpx_metadata_distance = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
			$gpx_metadata_duration_moving = '<label for="gpx-duration-moving">' . __('Moving time','lmm') . ':</label> <span id="gpx-duration-moving" class="gpx-duration-moving"></span> ';
		} else { $gpx_metadata_duration_moving = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
			$gpx_metadata_duration_total = '<label for="gpx-duration-total">' . __('Duration','lmm') . ':</label> <span id="gpx-duration-total" class="gpx-duration-total"></span> ';
		} else { $gpx_metadata_duration_total = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
			$gpx_metadata_avpace = '<label for="gpx-avpace">&#216;&nbsp;' . __('Pace','lmm') . ':</label> <span id="gpx-avpace"><span class="gpx-avpace"></span>/' . $gpx_unit_distance . '</span>';
		} else { $gpx_metadata_avpace = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
			$gpx_metadata_avhr = '<label for="gpx-avghr">&#216;&nbsp;' . __('Heart rate','lmm') . ':</label> <span id="gpx-avghr" class="gpx-avghr"></span>';
		} else { $gpx_metadata_avhr = NULL; }
		if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
			$gpx_metadata_elevation_title = '<label for="gpx-elevation">' . __('Elevation','lmm') . ':</label> <span id="gpx-elevation">';
		} else { $gpx_metadata_elevation_title = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
			$gpx_metadata_elev_gain = '+<span class="gpx-elevation-gain"></span>' . $gpx_unit_elevation;
		} else { $gpx_metadata_elev_gain = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
			$gpx_metadata_elev_loss = '-<span class="gpx-elevation-loss"></span>' . $gpx_unit_elevation;
		} else { $gpx_metadata_elev_loss = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
			$gpx_metadata_elev_net = '(' . __('net','lmm') . ': <span class="gpx-elevation-net"></span>' . $gpx_unit_elevation . ')</span>'; //info: </span> ->elevation-ID
		} else { $gpx_metadata_elev_net = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
			$gpx_metadata_elev_full = '<br/><label for="gpx-elevation-full">' . __('Full elevation data','lmm') . ':</label><br/><span id="gpx-elevation-full" class="gpx-elevation-full"></span>';
		} else { $gpx_metadata_elev_full = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
			$gpx_metadata_hr_full = '<br/><label for="gpx-heartrate-full">' . __('Full heart rate data','lmm') . ':</label><br/><span id="gpx-heartrate-full" class="gpx-heartrate-full"></span>';
		} else { $gpx_metadata_hr_full = NULL; }
		$gpx_metadata_elevation_array = array($gpx_metadata_elevation_title, $gpx_metadata_elev_gain, $gpx_metadata_elev_loss, $gpx_metadata_elev_net);
		$gpx_metadata_elevation = implode(' ',$gpx_metadata_elevation_array);
		if ($gpx_metadata_elevation == '   ') { $gpx_metadata_elevation = NULL; } //info: for no trailing |
		$gpx_metadata_array_all = array($gpx_metadata_name, $gpx_metadata_start, $gpx_metadata_end, $gpx_metadata_distance, $gpx_metadata_duration_moving, $gpx_metadata_duration_total, $gpx_metadata_avpace, $gpx_metadata_avhr, $gpx_metadata_elevation, $gpx_metadata_elev_full, $gpx_metadata_hr_full);

		$gpx_metadata_array_not_null = array();
		foreach ($gpx_metadata_array_all as $key => $value) {
			if (is_null($value) === false) {
				$gpx_metadata_array_not_null[$key] = $value;
			}
		}
		$gpx_metadata = implode(' <span class="gpx-delimiter">|</span> ',$gpx_metadata_array_not_null);
		$lmm_out .= $gpx_metadata;
		if ( (isset($lmm_options[ 'gpx_metadata_gpx_download' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_gpx_download' ] == 1 ) ) {
			$lmm_out .= ' <span class="gpx-delimiter">|</span> <span id="gpx-download"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/download/?map_type=layer&map_id=' . $id . '&format=gpx') . '" title="' . esc_attr__('download GPX file','lmm') . '">' . esc_attr__('download GPX file','lmm') . ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-download-gpx.png" width="10" height="10" alt="' . esc_attr__('download GPX file','lmm') . '" class="lmm-icon-download-gpx"></a></span>'.PHP_EOL;
		}
		$lmm_out .= '</div>';
	}

	//info: if panel enabled, only 94% height as otherwise attribution wont be visible
	if ($panel == 1) {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:94%; height:auto !important; min-height: 94%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript><span id="lmm-markers-loading" class="lmm-markers-loading"></span></div>'. PHP_EOL;
	} else {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:100%; height:auto !important; min-height: 100%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript><span id="lmm-markers-loading" class="lmm-markers-loading"></span></div>'. PHP_EOL;
	}

	//info: add geo microformats
	$layermarklist = $wpdb->get_results($wpdb->prepare('SELECT l.id as lid,l.name as lname, m.lon as mlon, m.lat as mlat, m.markername as markername,m.id as markerid FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id = %d LIMIT 250',$layer), ARRAY_A);
	if (count($layermarklist) < 1) {
		$lmm_out .= '<div class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
	} else {
		foreach ($layermarklist as $row){
			$lmm_out .= '<div class="lmm-geo-tags geo">' . htmlspecialchars($row['markername']) . ': <span class="latitude">' . $row['mlat'] . '</span>, <span class="longitude">' . $row['mlon'] . '</span></div>'.PHP_EOL;
		}
	}

	//info: preload area for CSS background images (home button etc)
	$lmm_out .= '<div class="lmm-preload-area"></div>'.PHP_EOL;

	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$lmm_out .= '/* Maps created with Maps Marker Pro - #1 premium mapping plugin for WordPress (www.mapsmarker.com) */'.PHP_EOL;
	}
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var mapsmarker_'.$uid.' = {};'.PHP_EOL;
	$lmm_out .= 'var markerID_mapsmarker_'.$uid . ' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
		$attrib_prefix = '<a href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Maps Marker Pro - #1 mapping plugin for WordPress','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Maps Marker Pro') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Maps Marker Pro') . '\">icons</a>)';
	} else {
		$attrib_prefix = '';
	}
	//info: add edit link
	if ( (current_user_can($lmm_options[ 'capabilities_edit_others' ])) || ((current_user_can( $lmm_options[ 'capabilities_edit' ]) && ( $current_user->user_login == $createdby))) ) {
		if (!empty($layer)) {
			$attrib_prefix = '<a href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $id . '\"><img style=\"display:inline;\" src=\"' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png\" width=\"16\" height=\"16\" alt=\"' . esc_attr__('edit layer','lmm') . ' ID ' . $id . '\" title=\"' . esc_attr__('edit layer','lmm') . ' ID ' . $id . '\"></a> ' . $attrib_prefix;
		} else if (!empty($marker)) {
			$attrib_prefix = '<a href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $id . '\"><img style=\"display:inline;\" src=\"' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png\" width=\"16\" height=\"16\" alt=\"' . esc_attr__('edit marker','lmm') . ' ID ' . $id . '\" title=\"' . esc_attr__('edit marker','lmm') . ' ID ' . $id . '\"></a> ' . $attrib_prefix;
		} else if (empty($layer) && empty($marker)) {
			$attrib_prefix = '<img style=\"display:inline;\" src=\"' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png\" width=\"16\" height=\"16\" alt=\"' . __('marker created directly by using shortcode - no edit link available','lmm') . '\" title=\"' . __('marker created directly by using shortcode - no edit link available','lmm') . '\"></a> ' . $attrib_prefix;
		}
	}
	$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
	$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	$dragging_setting = ($lmm_options['misc_map_dragging'] == 'false-touch') ? '!L.Browser.mobile' : esc_js($lmm_options['misc_map_dragging']);
	$maxzoom = intval($lmm_options['global_maxzoom_level']);
	 //info: true for leaflet-fullscreen.php only
	if ( ($lmm_options['misc_map_scrollwheelzoom'] == 'true') || ($lmm_options['misc_map_scrollwheelzoom'] == 'true-fullscreen-only') ){
		$scrollwheelzoom_setting = 'true';
	} else	if ($lmm_options['misc_map_scrollwheelzoom'] == 'false') {
		$scrollwheelzoom_setting = 'false';
	}
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { dragging: ' . $dragging_setting . ', touchZoom: ' . esc_js($lmm_options['misc_map_touchzoom']) . ', scrollWheelZoom: ' . $scrollwheelzoom_setting . ', doubleClickZoom: ' . esc_js($lmm_options['misc_map_doubleclickzoom']) . ', boxzoom: ' . esc_js($lmm_options['map_interaction_options_boxzoom']) . ', trackResize: ' . esc_js($lmm_options['misc_map_trackresize']) . ', worldCopyJump: ' . esc_js($lmm_options['map_interaction_options_worldcopyjump']) . ', closePopupOnClick: ' . esc_js($lmm_options['misc_map_closepopuponclick']) . ', keyboard: ' . esc_js($lmm_options['map_keyboard_navigation_options_keyboard']) . ', keyboardPanDelta: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardpandelta']) . ', inertia: ' . esc_js($lmm_options['map_panning_inertia_options_inertia']) . ', inertiaDeceleration: ' . intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) . ', inertiaMaxSpeed: ' . intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) . ', zoomControl: ' . $lmm_options['misc_map_zoomcontrol'] . ', crs: ' . esc_js($lmm_options['misc_projections']) . ', fullscreenControl: ' . esc_js($lmm_options['map_fullscreen_button']) . ', tap: ' . esc_js($lmm_options['map_interaction_options_tap']) . ', tapTolerance: ' . intval($lmm_options['map_interaction_options_taptolerance']) . ', bounceAtZoomLimits: ' . esc_js($lmm_options['map_interaction_options_bounceatzoomlimits']) . ' });'.PHP_EOL;

	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified") (Google Mutant) - not sure if needed in leaflet-fullscreen.php too
	$lmm_out .= $mapname.'._layersMaxZoom = ' . $maxzoom . ';'.PHP_EOL;

	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;
	//info: define basemaps
	$osm_attrib_general = __("Map",'lmm').': &copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>';
	if ($lmm_options['openstreetmap_variants'] == 'osm-mapnik') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 19;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-blackandwhite') {
		$osm_tile_url = 'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 18;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-de') {
		$osm_tile_url = 'http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 19;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-france') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = __("Map",'lmm').': &copy; <a href=\"https://www.openstreetmap.fr\" target=\"_blank\">Openstreetmap France</a> & <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-hot') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = $osm_attrib_general . ', ' . __("Tiles courtesy of","lmm") . ' <a href=\"https://hotosm.org/\" target=\"_blank\">Humanitarian OpenStreetMap Team</a>' . $osm_editlink;
	}
	//info: define edgeBuffertiles
	if ($lmm_options['map_interaction_options_bounceatzoomlimits'] != '0') {
		$edgebuffertiles = ', edgeBufferTiles: ' . floatval(str_replace(",",".", $lmm_options['edgeBufferTiles']));
	} else {
		$edgebuffertiles = '';
	}
	$error_tile_url = $lmm_options['basemaps_nowrap_enabled'] == 'true' ? '' : LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png';
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' . $osm_attribution . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});';
	$lmm_out .= 'var stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_terrain_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' .  $attrib_stamen . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_toner_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' .  $attrib_stamen . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' .  $attrib_stamen . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;

	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= 'if (typeof MQ !== "undefined") {';
		$lmm_out .= 'mapquest_osm = new MQ.mapLayer();';
		$lmm_out .= 'mapquest_aerial = new MQ.satelliteLayer();';
		$lmm_out .= 'mapquest_hybrid = new MQ.hybridLayer();';
		$lmm_out .= '} else { if (window.console) { console.log("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); } }';
	}

	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === TRUE) ) {
			$lmmjs_out .= 'if (window.console) { console.log("' . esc_attr__('You are using an outdated browser therefore maps automatically switched to OpenStreetMap. An update to a current browser is recommended.','lmm') . '"); }'.PHP_EOL;
		} elseif ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

			if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js' . $google_maps_api_key . $google_language;
				$lmm_out .= 'var deferred_google_layers = {
		roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }}
	};
	var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
	var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
	var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
	var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
			} else { //info: undeferred loading
				$lmm_out .= 'var googleLayer_roadmap = new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				$lmm_out .= 'var googleLayer_satellite = new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				$lmm_out .= 'var googleLayer_hybrid = new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				$lmm_out .= 'var googleLayer_terrain = new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
			}

		} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {
			if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '&key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js?callback=L.Google.asyncInitialize' . $google_maps_api_key . $google_language;
				$lmm_out .= 'var deferred_google_layers = {
		roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }},
		satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }},
		hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }},
		terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }}
	};
	var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
	var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
	var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
	var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
			} else { //info: undeferred loading
				$lmm_out .= 'var googleLayer_roadmap = new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
				$lmm_out .= 'var googleLayer_satellite = new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
				$lmm_out .= 'var googleLayer_hybrid = new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
				$lmm_out .= 'var googleLayer_terrain = new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
			}
		}
	}
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
		$lmm_out .= 'var bingaerial = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
		$lmm_out .= 'var bingaerialwithlabels = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
		$lmm_out .= 'var bingroad = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	};
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	//info: MapBox basemaps
	if ($lmm_options[ 'mapbox_access_token' ] != NULL) {
		$lmm_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox_access_token' ])) . '&secure=1", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	} else {  //info: v3 fallback for default maps
		$lmm_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($lmm_options[ 'mapbox2_access_token' ] != NULL) {
		$lmm_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox2_access_token' ])) . '&secure=1", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	} else {
		$lmm_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($lmm_options[ 'mapbox3_access_token' ] != NULL) {
		$lmm_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox3_access_token' ])) . '&secure=1", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	} else {
		$lmm_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
    $lmm_out .= 'var custom_basemap = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', tms: ' . esc_js($lmm_options[ 'custom_basemap_tms' ]) . ', ' . $error_tile_url_custom_basemap . 'attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap_nowrap_enabled' ] . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . '});'.PHP_EOL;
 	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', tms: ' . esc_js($lmm_options[ 'custom_basemap2_tms' ]) . ', ' . $error_tile_url_custom_basemap2 . 'attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap2_nowrap_enabled' ] . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', tms: ' . esc_js($lmm_options[ 'custom_basemap3_tms' ]) . ', ' . $error_tile_url_custom_basemap3 . 'attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap3_nowrap_enabled' ] . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . '});'.PHP_EOL;
	$lmm_out .= 'var empty_basemap = new L.TileLayer("");'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';
	$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';
	$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';
	$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';

	//info: define overlays
    $lmm_out .= 'var overlays_custom = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom_tms' ] . ', ' . $error_tile_url_overlays_custom . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom2_tms' ] . ', ' . $error_tile_url_overlays_custom2 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom2_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom3_tms' ] . ', ' . $error_tile_url_overlays_custom3 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom3_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom4_tms' ] . ', ' . $error_tile_url_overlays_custom4 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom4_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;

	//info: check if subdomains are set for wms layers
	$wms_subdomains = ((isset($lmm_options[ 'wms_wms_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms2_subdomains = ((isset($lmm_options[ 'wms_wms2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms3_subdomains = ((isset($lmm_options[ 'wms_wms3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms4_subdomains = ((isset($lmm_options[ 'wms_wms4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms5_subdomains = ((isset($lmm_options[ 'wms_wms5_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms5_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms5_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms6_subdomains = ((isset($lmm_options[ 'wms_wms6_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms6_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms6_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms7_subdomains = ((isset($lmm_options[ 'wms_wms7_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms7_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms7_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms8_subdomains = ((isset($lmm_options[ 'wms_wms8_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms8_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms8_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms9_subdomains = ((isset($lmm_options[ 'wms_wms9_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms9_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms9_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms10_subdomains = ((isset($lmm_options[ 'wms_wms10_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms10_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms10_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define wms legends
	$wms_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms2_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms2_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms2_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms2_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms3_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms3_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms3_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms3_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms4_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms4_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms4_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms4_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms5_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms5_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms5_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms5_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms6_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms6_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms6_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms6_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms7_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms7_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms7_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms7_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms8_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms8_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms8_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms8_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms9_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms9_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms9_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms9_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms10_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms10_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms10_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms10_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	//info: define wms layers
	if ($wms == 1) {
	$lmm_out .= 'var wms = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) . '", {wmsid: "wms", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ])) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ])) . '"' . $wms_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms2 == 1) {
	$lmm_out .= 'var wms2 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) . '", {wmsid: "wms2", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ])) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ])) . '"' . $wms2_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms3 == 1) {
	$lmm_out .= 'var wms3 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) . '", {wmsid: "wms3", layers: "' . htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ]))) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ])) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ])) . '"' . $wms3_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms4 == 1) {
	$lmm_out .= 'var wms4 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) . '", {wmsid: "wms4", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ])) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ])) . '"' . $wms4_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms5 == 1) {
	$lmm_out .= 'var wms5 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) . '", {wmsid: "wms5", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ])) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ])) . '"' . $wms5_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms6 == 1) {
	$lmm_out .= 'var wms6 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) . '", {wmsid: "wms6", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ])) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ])) . '"' . $wms6_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms7 == 1) {
	$lmm_out .= 'var wms7 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) . '", {wmsid: "wms7", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ])) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ])) . '"' . $wms7_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms8 == 1) {
	$lmm_out .= 'var wms8 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) . '", {wmsid: "wms8", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ])) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ])) . '"' . $wms8_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms9 == 1) {
	$lmm_out .= 'var wms9 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) . '", {wmsid: "wms9", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ])) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ])) . '"' . $wms9_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms10 == 1) {
	$lmm_out .= 'var wms10 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) . '", {wmsid: "wms10", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ])) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ])) . '"' . $wms10_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if( (isset($controlbox) == TRUE) && ($controlbox != 0) ){

		//info: controlbox - basemaps
		$lmm_out .= 'var layersControl = new L.Control.Layers('.PHP_EOL;
		$lmm_out .= '{';
		$basemaps_available = "";
		if ( (isset($lmm_options[ 'controlbox_osm_mapnik' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_mapnik' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_osm_mapnik' ])) . "': osm_mapnik,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_terrain' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_terrain' ])) . "': stamen_terrain,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_toner' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_toner' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_toner' ])) . "': stamen_toner,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_watercolor' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_watercolor' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_watercolor' ])) . "': stamen_watercolor,";
		}
		if ($lmm_options[ 'mapquest_api_key' ] != NULL) {
			if ( (isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_osm' ])) . "': mapquest_osm,";
			}
			if ( (isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_aerial' ])) . "': mapquest_aerial,";
			}
			if ( (isset($lmm_options[ 'controlbox_mapquest_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_hybrid' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_hybrid' ])) . "': mapquest_hybrid,";
			}
		}
		if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) ) {
			if ( (isset($lmm_options[ 'controlbox_googleLayer_roadmap' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_roadmap' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_roadmap' ])) . "': googleLayer_roadmap,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_satellite' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_satellite' ])) . "': googleLayer_satellite,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_hybrid' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_hybrid' ])) . "': googleLayer_hybrid,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_terrain' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_terrain' ])) . "': googleLayer_terrain,";
			}
		}
		if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
			if ( (isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingaerial' ])) . "': bingaerial,";
			}
			if ( (isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingaerialwithlabels' ])) . "': bingaerialwithlabels,";
			}
			if ( (isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingroad' ])) . "': bingroad,";
			}
		};
		if ( (isset($lmm_options[ 'controlbox_ogdwien_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_basemap' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_ogdwien_basemap' ])) . "': ogdwien_basemap,";
		}
		if ( (isset($lmm_options[ 'controlbox_ogdwien_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_satellite' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_ogdwien_satellite' ])) . "': ogdwien_satellite,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox_name' ]))."': mapbox,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox2' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox2' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox2_name' ]))."': mapbox2,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox3' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox3' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox3_name' ]))."': mapbox3,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap_name' ]))."': custom_basemap,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap2' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap2' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap2_name' ]))."': custom_basemap2,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap3' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap3' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap3_name' ]))."': custom_basemap3,";
		}
		if ( (isset($lmm_options[ 'controlbox_empty_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_empty_basemap' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'empty_basemap_name' ]))."': empty_basemap,";
		}
		//info: needed for IE7 compatibility
		$lmm_out .= substr($basemaps_available, 0, -1);
		$lmm_out .= '},'.PHP_EOL;

	    //info: controlbox - add available overlays
	    $lmm_out .= '{';
	    $overlays_custom_available = '';
	    if ( ((isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 )) || ($overlays_custom == 1) )
	        $overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom_name' ]))."': overlays_custom,";
	    if ( ((isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 )) || ($overlays_custom2 == 1) )
	        $overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom2_name' ]))."': overlays_custom2,";
	    if ( ((isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 )) || ($overlays_custom3 == 1) )
	        $overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom3_name' ]))."': overlays_custom3,";
	    if ( ((isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 )) || ($overlays_custom4 == 1) )
	    	$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom4_name' ]))."': overlays_custom4,";
		//info: needed for IE7 compatibility
		$lmm_out .= substr($overlays_custom_available, 0, -1);
		$lmm_out .= '},'.PHP_EOL;

		//info: controlbox - hidden / collapsed / expanded status
		if ( (isset($controlbox) == TRUE ) && ( $controlbox == 0 ) )
			$lmm_out .= '{ } );'.PHP_EOL;
		if ( (isset($controlbox) == TRUE ) && ( $controlbox == 1 ) )
			$lmm_out .= '{ collapsed: true } );'.PHP_EOL;
		if ( (isset($controlbox) == TRUE ) && ( $controlbox == 2 ) )
			$lmm_out .= '{ collapsed: false } );'.PHP_EOL;
	}
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;


	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? $mapname.".addControl(layersControl);" : "".PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ');';
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= $mapname.".addLayer(wms);".PHP_EOL;
	if ( $wms2 == 1 )
		$lmm_out .= $mapname.".addLayer(wms2);".PHP_EOL;
	if ( $wms3 == 1 )
		$lmm_out .= $mapname.".addLayer(wms3);".PHP_EOL;
	if ( $wms4 == 1 )
		$lmm_out .= $mapname.".addLayer(wms4);".PHP_EOL;
	if ( $wms5 == 1 )
		$lmm_out .= $mapname.".addLayer(wms5);".PHP_EOL;
	if ( $wms6 == 1 )
		$lmm_out .= $mapname.".addLayer(wms6);".PHP_EOL;
	if ( $wms7 == 1 )
		$lmm_out .= $mapname.".addLayer(wms7);".PHP_EOL;
	if ( $wms8 == 1 )
		$lmm_out .= $mapname.".addLayer(wms8);".PHP_EOL;
	if ( $wms9 == 1 )
		$lmm_out .= $mapname.".addLayer(wms9);".PHP_EOL;
	if ( $wms10 == 1 )
		$lmm_out .= $mapname.".addLayer(wms10);".PHP_EOL;
	//info: controlbox - check active overlays on marker/layer level
	//2do - remove isset-check - not necessary anymore, as sql result check is now global
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom);".PHP_EOL;
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom2);".PHP_EOL;
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom3);".PHP_EOL;
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom4);".PHP_EOL;
	//info: add minimap
	if ($lmm_options['minimap_status'] != 'hidden') {
		$lmm_out .= 'var osm_mapnik_minimap = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		$lmm_out .= 'var stamen_terrain_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_terrain_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		$lmm_out .= 'var stamen_toner_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_toner_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		$lmm_out .= 'var stamen_watercolor_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		//info: MapQuest minimap
		if ($lmm_options['mapquest_api_key'] != NULL) {
			$lmm_out .= 'if (typeof MQ !== "undefined") {';
			$lmm_out .= 'mapquest_osm_minimap = new MQ.mapLayer();';
			$lmm_out .= 'mapquest_aerial_minimap = new MQ.satelliteLayer();';
			$lmm_out .= 'mapquest_hybrid_minimap = new MQ.hybridLayer();';
			$lmm_out .= '}';
		}
		//info: google maps minimap
		if ($lmm_options['google_maps_api_status'] == 'enabled') {
			if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

				if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
							$lmm_out .= 'var deferred_google_layers_minimap = {
		roadmap: { name: "roadmap minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		satellite: { name: "satellite minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });; }},
		hybrid: { name: "hybrid minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		terrain: { name: "terrain minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }}
	};'.PHP_EOL;
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers_minimap.roadmap);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers_minimap.satellite);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers_minimap.hybrid);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers_minimap.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				}

			} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {
				if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
							$lmm_out .= 'var deferred_google_layers_minimap = {
		roadmap: { name: "roadmap minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
		satellite: { name: "satellite minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
		hybrid: { name: "hybrid minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
		terrain: { name: "terrain minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }}
	};'.PHP_EOL;
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers_minimap.roadmap);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers_minimap.satellite);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers_minimap.hybrid);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers_minimap.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				}
			}
		}
		//info: bing minimaps
		if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
			$lmm_out .= 'var bingaerial_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			$lmm_out .= 'var bingaerialwithlabels_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			$lmm_out .= 'var bingroad_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
		};
		if ($lmm_options['minimap_zoomLevelFixed'] != NULL) { $zoomlevelfixed =  'zoomLevelFixed: ' . intval($lmm_options['minimap_zoomLevelFixed']) . ','; } else { $zoomlevelfixed = ''; }
		if ($lmm_options['minimap_basemap'] == 'automatic') {
			if ($basemap == 'osm_mapnik') {
				$minimap_basemap = 'osm_mapnik_minimap';
			} else if ($basemap == 'stamen_terrain') {
				$minimap_basemap = 'stamen_terrain_minimap';
			} else if ($basemap == 'stamen_toner') {
				$minimap_basemap = 'stamen_toner_minimap';
			} else if ($basemap == 'stamen_watercolor') {
				$minimap_basemap = 'stamen_watercolor_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_osm')){
				$minimap_basemap = 'mapquest_osm_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_aerial')){
				$minimap_basemap = 'mapquest_aerial_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_hybrid')){
				$minimap_basemap = 'mapquest_hybrid_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_roadmap') ) {
				$minimap_basemap = 'googleLayer_roadmap_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_satellite') ) {
				$minimap_basemap = 'googleLayer_satellite_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_hybrid') ) {
				$minimap_basemap = 'googleLayer_hybrid_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_terrain') ) {
				$minimap_basemap = 'googleLayer_terrain_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerial')){
				$minimap_basemap = 'bingaerial_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerialwithlabels')){
				$minimap_basemap = 'bingaerialwithlabels_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingroad')){
				$minimap_basemap = 'bingroad_minimap';
			} else {
				$minimap_basemap = 'osm_mapnik_minimap';
			}
		} else {
			$minimap_basemap = $lmm_options['minimap_basemap'];
			//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
			if (($lmm_options['google_maps_api_status'] == 'disabled') && (($minimap_basemap == 'googleLayer_roadmap') || ($minimap_basemap == 'googleLayer_satellite') || ($minimap_basemap == 'googleLayer_hybrid') || ($minimap_basemap == 'googleLayer_terrain')) ) {
				$minimap_basemap = 'osm_mapnik_minimap';
			} else if (($lmm_options['mapquest_api_key'] == NULL) && (($minimap_basemap == 'mapquest_osm') || ($minimap_basemap == 'mapquest_aerial') || ($minimap_basemap == 'mapquest_hybrid')) ) {
				$minimap_basemap = 'osm_mapnik_minimap';
			}
		}
		$minimap_minimized = ($lmm_options['minimap_status'] == 'collapsed') ? 'true' : 'false';
		$lmm_out .= "var miniMap = new L.Control.MiniMap(" . $minimap_basemap . ", {position: '" . esc_js($lmm_options['minimap_position']) . "', width: " . intval($lmm_options['minimap_width']) . ", height: " . intval($lmm_options['minimap_height']) . ", collapsedWidth: " . intval($lmm_options['minimap_collapsedWidth']) . ", collapsedHeight: " . intval($lmm_options['minimap_collapsedHeight']) . ", zoomLevelOffset: " . intval($lmm_options['minimap_zoomLevelOffset']) . ", " . $zoomlevelfixed . " zoomAnimation: " . esc_js($lmm_options['minimap_zoomAnimation']) . ", toggleDisplay: " . esc_js($lmm_options['minimap_toggleDisplay']) . ", autoToggleDisplay: " . esc_js($lmm_options['minimap_autoToggleDisplay']) . ", minimized: " . $minimap_minimized . "}).addTo(" . $mapname . ");".PHP_EOL;
	}
	//info: filter details js variables initialization
	if($filter_details){
		$lmm_out .= 'var ordered_filter_details = '. json_encode($ordered_filters) .';'.PHP_EOL;
		$lmm_out .= 'var filter_details = '. json_encode($filter_details) .';'.PHP_EOL;
		$lmm_out .= 'var called_layers_'.$mapname.' = [];'.PHP_EOL;
		$lmm_out .= 'var filtered_layers = [];'.PHP_EOL;
		$lmm_out .= 'var active_layers_order = "'.$active_layers_order.'";'.PHP_EOL;
		$lmm_out .= 'var active_layers_orderby = "'.$active_layers_orderby.'";'.PHP_EOL;
		$lmm_out .= 'var filter_show_markercount = "'.$filter_show_markercount.'";'.PHP_EOL;
		$lmm_out .= 'var filter_show_icon = "'.$filter_show_icon.'";'.PHP_EOL;
		$lmm_out .= 'var filter_show_name = "'.$filter_show_name.'";'.PHP_EOL;
		$filters_collapsed_option = ($filters_collapsed!= 'hidden')?'"collapsed":'.$filters_collapsed.',':'';
		$lmm_out .= 'var filters_options = {"position":"'.$lmm_options['mlm_filter_controlbox_position'].'",'.$filters_collapsed_option.'};'.PHP_EOL;
	}
	//info: gpx tracks
	if ($gpx_url != NULL) {
		$gpx_track_color = '#' . str_replace('#', '', esc_js($lmm_options['gpx_track_color']));
		$gpx_startIconUrl = ($lmm_options['gpx_startIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png' : esc_url($lmm_options['gpx_startIconUrl']);
		$gpx_endIconUrl = ($lmm_options['gpx_endIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png' : esc_url($lmm_options['gpx_endIconUrl']);
		$gpx_shadowUrl = ($lmm_options['gpx_shadowUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png' : esc_url($lmm_options['gpx_shadowUrl']);
		if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
			$gpx_metadata_name_js = 'if (gpx.get_name() != undefined) { _c("gpx-name").innerHTML = gpx.get_name(); } else { _c("gpx-name").innerHTML = "n/a"; }';
		} else { $gpx_metadata_name_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
			$gpx_metadata_start_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-start").innerHTML = gpx.get_start_time().toDateString() + ", " + gpx.get_start_time().toLocaleTimeString(); } else { _c("gpx-start").innerHTML = "n/a"; }';
		} else { $gpx_metadata_start_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
			$gpx_metadata_end_js = 'if (gpx.get_end_time() != undefined) { _c("gpx-end").innerHTML = gpx.get_end_time().toDateString() + ", " + gpx.get_end_time().toLocaleTimeString(); } else { _c("gpx-end").innerHTML = "n/a"; }';
		} else { $gpx_metadata_end_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = (gpx.get_distance()/1000).toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			} else {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = gpx.get_distance_imp().toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_distance_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
			$gpx_metadata_duration_moving_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-moving").innerHTML = gpx.get_duration_string(gpx.get_moving_time()); } else { _c("gpx-duration-moving").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_moving_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
			$gpx_metadata_duration_total_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-total").innerHTML = gpx.get_duration_string(gpx.get_total_time()); } else { _c("gpx-duration-total").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_total_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			} else {
			$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace_imp(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_avpace_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
			$gpx_metadata_avhr_js = 'if (isNaN(gpx.get_average_hr())) { _c("gpx-avghr").innerHTML = "n/a"; } else { _c("gpx-avghr").innerHTML = gpx.get_average_hr() + "bpm"; }';
		} else { $gpx_metadata_avhr_js = ''; }
		if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
			$gpx_metadata_elevation_title_js = '';
		} else { $gpx_metadata_elevation_title_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.get_elevation_gain().toFixed(0);';
			} else {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_gain_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_loss_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.get_elevation_gain().toFixed(0) - gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.to_ft(gpx.get_elevation_gain() - gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_net_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data();';
			} else {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data_imp();';
			}
		} else { $gpx_metadata_elev_full_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data();';
			} else {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data_imp();';
			}
		} else { $gpx_metadata_hr_full_js = ''; }

			//info: load gpx_content
			$gpx_content_array = wp_remote_get( $gpx_url, array( "sslverify" => false, "timeout" => 30 ) );
			//info: do not load GPX if error on wp_remote_get occured
			if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
				$gpx_content = esc_js(str_replace("\xEF\xBB\xBF",'',$gpx_content_array['body'])); //info: replace UTF8-BOM for Chrome
				//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
				if ( (strrpos($gpx_content, '&lt;?xml') !== FALSE) && (strrpos($gpx_content, '&lt;?xml') != 0) ){
					$position = strrpos($gpx_content, '&lt;?xml');
					$gpx_content = substr($gpx_content, $position);
				}
			} else {
				$gpx_content = '';
			}
			$lmm_out .= '
				function display_gpx_' . $uid . '() {
					var gpx_panel = document.getElementById("gpx-panel-' . $uid . '");
					var gpx_url = "'.$gpx_url.'";

					function _c(c) { return gpx_panel.querySelectorAll("."+c)[0]; }

					var gpx_track = new L.GPX(gpx_url, {
						gpx_content: "'.$gpx_content.'",
						async: true,
						max_point_interval: ' . intval($lmm_options['gpx_max_point_interval']) . ',
						marker_options: {
							startIconUrl: "' . $gpx_startIconUrl . '",
							endIconUrl: "' . $gpx_endIconUrl . '",
							shadowUrl: "' . $gpx_shadowUrl . '",
							iconSize: [' . intval($lmm_options['gpx_iconSize_x']) . ', ' . intval($lmm_options['gpx_iconSize_y']) . '],
							shadowSize: [' . intval($lmm_options['gpx_shadowSize_x']) . ', ' . intval($lmm_options['gpx_shadowSize_y']) . '],
							iconAnchor: [' . intval($lmm_options['gpx_iconAnchor_x']) . ', ' . intval($lmm_options['gpx_iconAnchor_y']) . '],
							shadowAnchor: [' . intval($lmm_options['gpx_shadowAnchor_x']) . ', ' . intval($lmm_options['gpx_shadowAnchor_y']) . '],
							className: "lmm_gpx_icons"
						},
						polyline_options: {
							color: "' . $gpx_track_color . '",
							weight: ' . intval($lmm_options['gpx_track_weight']) . ',
							opacity: "' . str_replace(',', '.', floatval($lmm_options['gpx_track_opacity'])) . '",
							smoothFactor: "' . str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])) . '",
							interactive: ' . esc_js($lmm_options['gpx_track_clickable']) . ',
							noClip: ' . esc_js($lmm_options['gpx_track_noClip']) . '
						}
					}).addTo(' . $mapname . ');
					gpx_track.on("gpx_loaded", function(e) {
						var gpx = e.target;
						' . $gpx_metadata_name_js . '
						' . $gpx_metadata_start_js . '
						' . $gpx_metadata_end_js . '
						' . $gpx_metadata_distance_js . '
						' . $gpx_metadata_duration_moving_js . '
						' . $gpx_metadata_duration_total_js . '
						' . $gpx_metadata_avpace_js . '
						' . $gpx_metadata_avhr_js . '
						' . $gpx_metadata_elev_gain_js . '
						' . $gpx_metadata_elev_loss_js . '
						' . $gpx_metadata_elev_net_js . '
						' . $gpx_metadata_elev_full_js . '
						' . $gpx_metadata_hr_full_js . '
					});
				}'.PHP_EOL;
		//info: to prevent console XML prsing errors
		if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
			$lmm_out .= 'display_gpx_' . $uid . '();'.PHP_EOL;
		} else {
			$gpx_url_error = (current_user_can( 'manage_options' )) ? '	if (window.console) { console.log("' . esc_attr__('Error', 'lmm') . ' ' . $gpx_content_array['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), $gpx_url) . '"); }'.PHP_EOL : '';
			$lmm_out .= $gpx_url_error;
		}
	}

	//info: add scale control
	if ( $lmm_options['map_scale_control'] == 'enabled' ) {
		$lmm_out .= "L.control.scale({position:'" . esc_js($lmm_options['map_scale_control_position']) . "', maxWidth: " . intval($lmm_options['map_scale_control_maxwidth']) . ", metric: " . esc_js($lmm_options['map_scale_control_metric']) . ", imperial: " . esc_js($lmm_options['map_scale_control_imperial']) . ", updateWhenIdle: " . esc_js($lmm_options['map_scale_control_updatewhenidle']) . "}).addTo(" . $mapname . ");".PHP_EOL;
	}

	//info: add geolocate control
	if ($lmm_options['geolocate_status'] == 'true') {
		if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) { $onlocationerror = ', onLocationError: function () {}'; } else { $onlocationerror = ''; }
		//info: prepare geolocate setView
		if ($lmm_options[ 'geolocate_setView' ] == 'false') {
			$geolocate_setview = "false";
		} else {
			$geolocate_setview = "'" . esc_js($lmm_options[ 'geolocate_setView' ]) . "'";
		}
		$lmm_out .= "var locatecontrol = L.control.locate({	position: '" . esc_js($lmm_options[ 'geolocate_position' ]) . "', drawCircle: " . esc_js($lmm_options[ 'geolocate_drawCircle' ]) . ", drawMarker: " . esc_js($lmm_options[ 'geolocate_drawMarker' ]) . ", setView: " . $geolocate_setview . ", keepCurrentZoomLevel: " . esc_js($lmm_options[ 'geolocate_keepCurrentZoomLevel' ]) . ", clickBehavior: { inView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_inView' ]) . "', outOfView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_outOfView' ]) . "'}, circleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_circleStyle' ])) . "}, markerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_markerStyle' ])) . "}, followCircleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followCircleStyle' ])) . "}, followMarkerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followMarkerStyle' ])) . "}, icon: '" . esc_js($lmm_options[ 'geolocate_icon' ]) . "', circlePadding: " . esc_js($lmm_options[ 'geolocate_circlePadding' ]) . ", metric: " . esc_js($lmm_options[ 'geolocate_units' ]) . ", showPopup: " . esc_js($lmm_options[ 'geolocate_showPopup' ]) . ", strings: { title: '" . __('Show me where I am','lmm') . "', metersUnit: '" . __('meters','lmm') . "', feetUnit: '" . __('feet','lmm') . "', popup: '" . sprintf(__('You are within %1$s %2$s from this point','lmm'), '{distance}', '{unit}') . "', outsideMapBoundsMsg: '" . __('You seem located outside the boundaries of the map','lmm') . "' }, locateOptions: { " . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_locateOptions' ])) . " }" . $onlocationerror . " }).addTo(" . $mapname . ");".PHP_EOL;
		if ( $lmm_options['geolocate_autostart'] == 'true' ) {
			$lmm_out .= "locatecontrol.start();";
		}
	}

	//info: js for layer only
	if (!empty($geojson) or !empty($geojsonurl) or !empty($layer) ) {
		$lmm_out .= 'var geojsonObj, mapIcon, marker_title, alt_title;'.PHP_EOL;
		$lmm_out .= 'var markersLoading = document.getElementById("lmm-markers-loading");'.PHP_EOL;
		$lmm_out .= 'markersLoading.style.display = "block";'.PHP_EOL;
		//info: load GeoJSON for layer maps
		if (!empty($layer) && ($multi_layer_map == 0) ) {
			$lmm_out .= 'var xhReq = new XMLHttpRequest();'.PHP_EOL;
			$lmm_out .= 'xhReq.open("GET", "' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $id . '/') . '", true);'.PHP_EOL; //info: for caching add &timestamp=' . time() . '
				$lmm_out .= 'xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) {'.PHP_EOL; //info: async 1a/2
		} else if (!empty($layer) && ($multi_layer_map == 1) ) {
			$lmm_out .= 'var xhReq = new XMLHttpRequest();'.PHP_EOL;
			$lmm_out .= 'xhReq.open("GET", "' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $multi_layer_map_list .'/') . '", true);'.PHP_EOL; //info: for caching add &timestamp=' . time() . '
				$lmm_out .= 'xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) {'.PHP_EOL; //info: async 1b/2
		}
		if($filter_details){
			$lmm_out .= 'jQuery(".lmm-filter").removeAttr("disabled");'.PHP_EOL;
		}
		//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
		$lmm_out .= 'if (xhReq.responseText.indexOf(\'{"type"\') != 0) {
	var position = xhReq.responseText.indexOf(\'{"type"\');
	try { geojsonObj = JSON.parse(xhReq.responseText.slice(position)); markersLoading.style.display = "none"; } catch (e) { console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message); }'.PHP_EOL;
		$lmm_out .= '} else {
	try { geojsonObj = JSON.parse(xhReq.responseText); markersLoading.style.display = "none"; } catch (e) { console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message); }'.PHP_EOL;
		$lmm_out .= '}'.PHP_EOL;

		//info: clustering 1/2
		if ($clustering == '1') {
			$polygon_options_stroke = 'stroke: ' . esc_js($lmm_options['clustering_polygonOptions_stroke']);
			$polygon_options_color = 'color: \'#' . str_replace('#', '', esc_js($lmm_options['clustering_polygonOptions_color'])) . '\'';
			$polygon_options_weight = 'weight: ' . str_replace(',', '.', floatval($lmm_options['clustering_polygonOptions_weight']));
			$polygon_options_opacity = 'opacity: ' . str_replace(',', '.', floatval($lmm_options['clustering_polygonOptions_opacity']));
			$polygon_options_fillcolor = 'fillColor: \'#' . str_replace('#', '', esc_js($lmm_options['clustering_polygonOptions_fillColor'])) . '\'';
			$polygon_options_fillopacity = 'fillOpacity: ' . str_replace(',', '.', floatval($lmm_options['clustering_polygonOptions_fillopacity']));
			$polygon_options_clickable= 'interactive: ' . esc_js($lmm_options['clustering_polygonOptions_clickable']);
			if ($lmm_options['clustering_polygonOptions_fill'] == 'auto') {
				$polygon_options_array = array($polygon_options_stroke, $polygon_options_color, $polygon_options_weight, $polygon_options_opacity, $polygon_options_fillcolor, $polygon_options_fillopacity, $polygon_options_clickable);
			} else {
				$polygon_options_fill = 'fill: false';
				$polygon_options_array = array($polygon_options_stroke, $polygon_options_color, $polygon_options_weight, $polygon_options_opacity, $polygon_options_fill, $polygon_options_fillcolor, $polygon_options_fillopacity, $polygon_options_clickable);
			}
			$polygon_options = implode(', ',$polygon_options_array);
			//info: spiderLegPolylineOptions
			$spiderLeg_polyline_options_color = 'color: \'#' . str_replace('#', '', esc_js($lmm_options['clustering_spiderLegPolylineOptions_color'])) . '\'';
			$spiderLeg_polyline__options_weight = 'weight: ' . str_replace(',', '.', floatval($lmm_options['clustering_spiderLegPolylineOptions_weight']));
			$spiderLeg_polyline_options_opacity = 'opacity: ' . str_replace(',', '.', floatval($lmm_options['clustering_spiderLegPolylineOptions_opacity']));
			$spiderLeg_polyline_options = $spiderLeg_polyline_options_color . ',' . $spiderLeg_polyline__options_weight . ',' . $spiderLeg_polyline_options_opacity;

			//info: markercluster loading indicator
			$lmm_out .= "function updateMarkersLoading(processed, total, elapsed, layersArray) {
							if (elapsed > 0) {
								markersLoading.style.display = 'block';
							}
							if (processed === total) {
								markersLoading.style.display = 'none';
							}
						}".PHP_EOL;
			$disable_clustering_at_zoom = intval($lmm_options['clustering_disableClusteringAtZoom']) == 0 ? 'null' : intval($lmm_options['clustering_disableClusteringAtZoom']);
			if($filter_details){
				//info: use layerSupport plugin to support filters with cluster groups
				$lmm_out .= 'markercluster_'.$mapname_js.' = new L.markerClusterGroup.layerSupport({ zoomToBoundsOnClick: ' . esc_js($lmm_options['clustering_zoomToBoundsOnClick']) . ', showCoverageOnHover: ' . esc_js($lmm_options['clustering_showCoverageOnHover']) . ', spiderfyOnMaxZoom: ' . esc_js($lmm_options['clustering_spiderfyOnMaxZoom']) . ', animateAddingMarkers: ' . esc_js($lmm_options['clustering_animateAddingMarkers']) . ', disableClusteringAtZoom: ' . $disable_clustering_at_zoom . ', maxClusterRadius: ' . intval($lmm_options['clustering_maxClusterRadius']) . ', polygonOptions: {' . $polygon_options . '}, singleMarkerMode: ' . esc_js($lmm_options['clustering_singleMarkerMode']) . ', spiderfyDistanceMultiplier: ' . intval($lmm_options['clustering_spiderfyDistanceMultiplier']) . ', spiderLegPolylineOptions: {' . $spiderLeg_polyline_options . '}, chunkedLoading: true, chunkProgress: updateMarkersLoading, animate: ' . $lmm_options['clustering_animate'] . ' });'.PHP_EOL;
			}else{
				$lmm_out .= 'var markercluster_'.$mapname_js.' = new L.MarkerClusterGroup({ zoomToBoundsOnClick: ' . esc_js($lmm_options['clustering_zoomToBoundsOnClick']) . ', showCoverageOnHover: ' . esc_js($lmm_options['clustering_showCoverageOnHover']) . ', spiderfyOnMaxZoom: ' . esc_js($lmm_options['clustering_spiderfyOnMaxZoom']) . ', animateAddingMarkers: ' . esc_js($lmm_options['clustering_animateAddingMarkers']) . ', disableClusteringAtZoom: ' . $disable_clustering_at_zoom . ', maxClusterRadius: ' . intval($lmm_options['clustering_maxClusterRadius']) . ', polygonOptions: {' . $polygon_options . '}, singleMarkerMode: ' . esc_js($lmm_options['clustering_singleMarkerMode']) . ', spiderfyDistanceMultiplier: ' . intval($lmm_options['clustering_spiderfyDistanceMultiplier']) . ', spiderLegPolylineOptions: {' . $spiderLeg_polyline_options . '}, chunkedLoading: true, chunkProgress: updateMarkersLoading, animate: ' . $lmm_options['clustering_animate'] . ' });'.PHP_EOL;
			}
		}
		if($filter_details){
			$lmm_out .= 'var overlays_filters = [];'.PHP_EOL;
		}
		$lmm_out .= 'var geojson_markers = L.geoJson(geojsonObj, {'.PHP_EOL;
		$lmm_out .= '		onEachFeature: function(feature, marker) {'.PHP_EOL;
		if($lmm_options['defaults_marker_popups_rise_on_hover'] == 'true'){
			$lmm_out .= '		marker.on("mouseover", function (e) {'.PHP_EOL;
	   		$lmm_out .= '  			this.openPopup();'.PHP_EOL;
	 	    $lmm_out .= '		});'.PHP_EOL;
		}
		$lmm_out .= '			markerID_mapsmarker_'.$uid . '[feature.properties.markerid] = marker;'.PHP_EOL;
			//info: add the markers to the filters
			if($filter_details){
				$lmm_out .= '			var filter_exist = feature.properties.layers.filter(function(n) {'.PHP_EOL;
		    	$lmm_out .= '				return Object.keys(filter_details).indexOf(n) != -1;'.PHP_EOL;
				$lmm_out .= '			});'.PHP_EOL;
				$lmm_out .= '			if (filter_exist.length != 0 ){'.PHP_EOL;
				$lmm_out .= '				if(typeof filtered_layers[filter_exist[0]] != "object"){'.PHP_EOL;
				$lmm_out .= '					filtered_layers[filter_exist[0]] = L.layerGroup();'.PHP_EOL;
				$lmm_out .= '				}'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter_exist[0]].addLayer(marker);'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter_exist[0]]["status"] = filter_details[filter_exist[0]]["status"];'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter_exist[0]]["name"] = filter_details[filter_exist[0]]["name"];'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter_exist[0]]["markercount"] = filter_details[filter_exist[0]]["markercount"];'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter_exist[0]]["layer_id"] = filter_exist[0];'.PHP_EOL;
				$lmm_out .= '				called_layers_'.$mapname.'[filter_exist[0]] = true;'.PHP_EOL;
				$lmm_out .= '			}'.PHP_EOL;
			}
		if ($lmm_options['directions_popuptext_panel'] == 'yes') {

			$lmm_out .= 'if (feature.properties.text != "") { var css = "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;"; } else { var css = ""; }'.PHP_EOL;
			if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
				$lmm_out .= 'if (feature.properties.markername != "") { var divmarkername1 = "<div class=\"popup-markername\"  style=\"border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;\">"; var divmarkername2 = "</div>" } else { var divmarkername1 = ""; var divmarkername2 = ""; }'.PHP_EOL;
				$lmm_out .= 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+divmarkername1+feature.properties.markername+divmarkername2+feature.properties.text+"<div class=\"popup-directions\" style=\""+css+"\">"+feature.properties.address+" <a href=\""+feature.properties.dlink+"\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a></div></div>", {'.PHP_EOL;
			} else {
				$lmm_out .= 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+feature.properties.text+"<div class=\"popup-directions\" style=\""+css+"\">"+feature.properties.address+" <a href=\""+feature.properties.dlink+"\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a></div></div>", {'.PHP_EOL;
			}
				$lmm_out .= 'maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ','.PHP_EOL;
				$lmm_out .= 'minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ','.PHP_EOL;
				$lmm_out .= 'maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ','.PHP_EOL;
				$lmm_out .= 'autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ','.PHP_EOL;
				$lmm_out .= 'closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ','.PHP_EOL;
				$lmm_out .= 'autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
			$lmm_out .= '});'.PHP_EOL;
		} else {
			$lmm_out .= 'if (feature.properties.text != "") {'.PHP_EOL;
			if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
				$lmm_out .= 'if (feature.properties.markername != "") { var divmarkername1 = "<div class=\"popup-markername\"  style=\"border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;\">"; var divmarkername2 = "</div>" } else { var divmarkername1 = ""; var divmarkername2 = ""; }'.PHP_EOL;
				$lmm_out .= 'if ( (feature.properties.text != "") && (feature.properties.markername != "") ) {'; //info: to prevent empty popups
				$lmm_out .= 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+divmarkername1+feature.properties.markername+divmarkername2+feature.properties.text+"</div>", {'.PHP_EOL;
			} else {
				$lmm_out .= 'if (feature.properties.text != "") {'; //info: to prevent empty popups
				$lmm_out .= 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+feature.properties.text+"</div>", {'.PHP_EOL;
			}
			$lmm_out .= 'maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ','.PHP_EOL;
			$lmm_out .= 'minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ','.PHP_EOL;
			$lmm_out .= 'maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ','.PHP_EOL;
			$lmm_out .= 'autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ','.PHP_EOL;
			$lmm_out .= 'closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ','.PHP_EOL;
			$lmm_out .= 'autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
			$lmm_out .= '});'.PHP_EOL;
			$lmm_out .= '}'.PHP_EOL;
			$lmm_out .= '}'.PHP_EOL;
		}
		//info: marker tooltips
		if ($lmm_options[ 'marker_tooltip_status' ] == 'enabled') {
			$lmm_out .= "if (feature.properties.markername != '') {".PHP_EOL;
			$lmm_out .= "	marker.bindTooltip(feature.properties.markername, {offset: L.point(" . intval($lmm_options[ 'marker_tooltip_offset_x' ]) . "," . intval($lmm_options[ 'marker_tooltip_offset_y' ]) . "), direction: '" . esc_js($lmm_options[ 'marker_tooltip_direction' ]) . "', permanent: " . esc_js($lmm_options[ 'marker_tooltip_permanent' ]) . ", sticky: " . esc_js($lmm_options[ 'marker_tooltip_sticky' ]) . ", interactive: " . esc_js($lmm_options[ 'marker_tooltip_interactive' ]) . ", opacity: " . str_replace(',', '.', floatval($lmm_options[ 'marker_tooltip_opacity' ])) . "});".PHP_EOL;
			$lmm_out .= "}".PHP_EOL;
		}
		$lmm_out .= '},'.PHP_EOL;
		$lmm_out .= 'pointToLayer: function (feature, latlng) {'.PHP_EOL;
		$lmm_out .= '	mapIcon = L.icon({ '.PHP_EOL;
		$lmm_out .= "		iconUrl: (feature.properties.icon != '') ? '" . $defaults_marker_icon_url . "/' + feature.properties.icon : '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png" . "',".PHP_EOL;
		$lmm_out .= '		iconSize: [' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		iconAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		popupAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= "		shadowUrl: '" . $marker_shadow_url . "',".PHP_EOL;
		$lmm_out .= '		shadowSize: [' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		shadowAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= "		className: (feature.properties.icon == '') ? 'lmm_marker_icon_default' : 'lmm_marker_icon_'+ feature.properties.icon.slice(0,-4)".PHP_EOL;
		$lmm_out .= '	});'.PHP_EOL;
		if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show' && $lmm_options[ 'marker_tooltip_status' ] == 'disabled') {
			$lmm_out .= "if (feature.properties.markername == '') { marker_title = ''; alt_title = ''; } else { marker_title = feature.properties.markername; alt_title = marker_title; };".PHP_EOL;
		} else {
			$lmm_out .= "marker_title = '';".PHP_EOL;
			$lmm_out .= "if (feature.properties.markername == '') { alt_title = '' } else { alt_title = feature.properties.markername };".PHP_EOL;
		}
		$lmm_out .= 'return L.marker(latlng, {icon: mapIcon, interactive: true, title: marker_title, alt: alt_title, opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
		$lmm_out .= '}});'.PHP_EOL;
		if($filter_details){
			//info: add layers with markercount = 0
			$lmm_out .= 'jQuery.each(filter_details, function(lid, filter){'.PHP_EOL;
			$lmm_out .= '  if(filter.markercount == "0" && filter.status == "active"){'.PHP_EOL;
			$lmm_out .= '		filtered_layers[lid] = L.layerGroup();'.PHP_EOL;
			$lmm_out .= '		filtered_layers[lid]["status"] = filter.status;'.PHP_EOL;
			$lmm_out .= '		filtered_layers[lid]["name"] = filter.name;'.PHP_EOL;
			$lmm_out .= '		filtered_layers[lid]["layer_id"] = lid;'.PHP_EOL;
			$lmm_out .= '		called_layers_'.$mapname.'[lid] = true;'.PHP_EOL;
			$lmm_out .= '	 }'.PHP_EOL;
			$lmm_out .= '});'.PHP_EOL;
		}
		//info: clustering 2/2
		if ($clustering == '1') {
			if($filter_details){
				$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
				$lmm_out .= '	filtered_layers.sort(function(a, b){'.PHP_EOL;
				$lmm_out .= '		if(active_layers_orderby =="name"){'.PHP_EOL;
				$lmm_out .= ' 			b[active_layers_orderby] = b[active_layers_orderby].toLowerCase();'.PHP_EOL;
				$lmm_out .= ' 			a[active_layers_orderby] = a[active_layers_orderby].toLowerCase();'.PHP_EOL;
				$lmm_out .= ' 	    }'.PHP_EOL;
				$lmm_out .= '			if(active_layers_order == "DESC"){'.PHP_EOL;
				$lmm_out .= '				if(b[active_layers_orderby] < a[active_layers_orderby])	return -1;'.PHP_EOL;
				$lmm_out .= '				else if(b[active_layers_orderby] > a[active_layers_orderby])	return 1;'.PHP_EOL;
				$lmm_out .= '				return 0;'.PHP_EOL;
				$lmm_out .= '			}else{'.PHP_EOL;
				$lmm_out .= '				if(b[active_layers_orderby] < a[active_layers_orderby])	return 1;'.PHP_EOL;
				$lmm_out .= '				else if(b[active_layers_orderby] > a[active_layers_orderby])	return -1;'.PHP_EOL;
				$lmm_out .= '				return 0;'.PHP_EOL;
				$lmm_out .= ' 		}'.PHP_EOL;
				$lmm_out .= '	});'.PHP_EOL;
				$lmm_out .= '}'.PHP_EOL;
				//info: prepare html label for filters
				$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
				$lmm_out .= '	jQuery.each(filtered_layers, function(lid, group){'.PHP_EOL;
				$lmm_out .= '				try{'.PHP_EOL;
				$lmm_out .= '					if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter_details[group.layer_id].markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
				$lmm_out .= '					if(filter_details[group.layer_id]["icon"] != "" && filter_show_icon != "0"){  var filter_icon = "<img src=\'"+ filter_details[group.layer_id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
				$lmm_out .= '					if(filter_show_name != "0"){ var filter_name = filter_details[group.layer_id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
				$lmm_out .= '					overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name + "</span>" + markercount   ] = group;'.PHP_EOL;
				$lmm_out .= '					if(group["status"] == "active"){'.PHP_EOL;
				$lmm_out .= '						markercluster_'.$mapname_js.'.addLayer(group);'.PHP_EOL;
				$lmm_out .= '					}'.PHP_EOL;
				$lmm_out .= '				}catch(n){}'.PHP_EOL;
				$lmm_out .= '	});'.PHP_EOL;
				$lmm_out .= '}'.PHP_EOL;
				//info: add inactive layers to the controlbox
				$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
				$lmm_out .= '		jQuery.each(ordered_filter_details, function(lid, filter){'.PHP_EOL;
				$lmm_out .= '			if(filter["status"] == "inactive"){'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter.id] = L.layerGroup();'.PHP_EOL;
				$lmm_out .= ' 			filtered_layers[filter.id]["layer_id"] =filter.id;'.PHP_EOL;
				$lmm_out .= ' 			filtered_layers[filter.id]["markercount"] = filter.markercount;'.PHP_EOL;
				$lmm_out .= '				if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter.markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
				$lmm_out .= '				if(filter_details[filter.id]["icon"] != "" && filter_show_icon != "0"){ var filter_icon = "<img class=\"mlm-filters-icon\" src=\'"+ filter_details[filter.id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
				$lmm_out .= '				if(filter_show_name != "0"){ var filter_name = filter_details[filter.id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
				$lmm_out .= '				overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name + "</span>" + markercount  ] = filtered_layers[filter.id];'.PHP_EOL;
				$lmm_out .= '			}'.PHP_EOL;
				$lmm_out .= '		});'.PHP_EOL;
				$lmm_out .= '}'.PHP_EOL;
				//info: add the controlbox on the map
				if ($filters_collapsed != 'hidden') {
					$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
					$lmm_out .= '		L.control.filters(null, overlays_filters, filters_options).addTo('.$mapname.');'.PHP_EOL;
					$lmm_out .= '}'.PHP_EOL;
				}

				$lmm_out .= 		'geojson_markers.addTo(markercluster_'.$mapname_js.');'.PHP_EOL;
				$lmm_out .= $mapname . '.addLayer(markercluster_'.$mapname_js.');'.PHP_EOL;
				//info: making ajax call when the filterbox clicked
				$lmm_out .= $mapname . '.on("overlayadd",function(){'.PHP_EOL;
					$lmm_out .= 'jQuery(".lmm-filter").click(function(){'.PHP_EOL;
					$lmm_out .= '		if(jQuery(this).is(":checked")){'.PHP_EOL;
					$lmm_out .= '   		var layer_id = jQuery(this).attr("id");'.PHP_EOL;
					$lmm_out .= '         if(called_layers_'.$mapname.'[layer_id] !== true){'.PHP_EOL;
					$lmm_out .= '				jQuery(".lmm-filter").attr("disabled","disabled");'.PHP_EOL;
					$lmm_out .= '				xhReq.open("GET","' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/" + layer_id + "/') . '", true);'.PHP_EOL;
					$lmm_out .= '				xhReq.send(null);'.PHP_EOL;
					$lmm_out .= '				called_layers_'.$mapname.'[layer_id] = true;'.PHP_EOL;
					$lmm_out .= '				window["mmp_no_controlbox"] = true;'.PHP_EOL;
					$lmm_out .= '			}'.PHP_EOL;
					$lmm_out .= '		}'.PHP_EOL;
					$lmm_out .= '});';
				$lmm_out .= '})'.PHP_EOL;
				$lmm_out .= $mapname . '.fire("overlayadd");'.PHP_EOL;
			}else{
				$lmm_out .= 'geojson_markers.addTo(markercluster_'.$mapname_js.');'.PHP_EOL;
				$lmm_out .= $mapname . '.addLayer(markercluster_'.$mapname_js.');'.PHP_EOL;
			}
		} else {
			if($filter_details){
				//info: sort filters by js
				$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
				$lmm_out .= '	filtered_layers.sort(function(a, b){'.PHP_EOL;
				$lmm_out .= '		if(active_layers_orderby =="name"){'.PHP_EOL;
				$lmm_out .= ' 			b[active_layers_orderby] = b[active_layers_orderby].toLowerCase();'.PHP_EOL;
				$lmm_out .= ' 			a[active_layers_orderby] = a[active_layers_orderby].toLowerCase();'.PHP_EOL;
				$lmm_out .= ' 	    }'.PHP_EOL;
				$lmm_out .= '		if(active_layers_order == "DESC"){'.PHP_EOL;
				$lmm_out .= '			if(b[active_layers_orderby] < a[active_layers_orderby])	return -1;'.PHP_EOL;
				$lmm_out .= '			else if(b[active_layers_orderby] > a[active_layers_orderby])	return 1;'.PHP_EOL;
				$lmm_out .= '			return 0;'.PHP_EOL;
				$lmm_out .= '		}else{'.PHP_EOL;
				$lmm_out .= '			if(b[active_layers_orderby] < a[active_layers_orderby])	return 1;'.PHP_EOL;
				$lmm_out .= '			else if(b[active_layers_orderby] > a[active_layers_orderby])	return -1;'.PHP_EOL;
				$lmm_out .= '			return 0;'.PHP_EOL;
				$lmm_out .= ' 		}'.PHP_EOL;
				$lmm_out .= '	});'.PHP_EOL;
				$lmm_out .= '}'.PHP_EOL;
				//info: prepare html of filters label and add them to the controlbox
				$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
				$lmm_out .= '	jQuery.each(filtered_layers, function(lid, group){ '.PHP_EOL;
				$lmm_out .= '				try{ '.PHP_EOL;
				$lmm_out .= '					if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter_details[group.layer_id].markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
				$lmm_out .= '					if(filter_details[group.layer_id]["icon"] != "" && filter_show_icon != "0"){  var filter_icon = "<img src=\'"+ filter_details[group.layer_id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
				$lmm_out .= '					if(filter_show_name != "0"){ var filter_name = filter_details[group.layer_id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
				$lmm_out .= '					group["markercount"] = filter_details[group.layer_id].markercount;'.PHP_EOL;
				$lmm_out .= '					overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name  + "</span>" +  markercount  ] = group;'.PHP_EOL;
				$lmm_out .= '					if(group["status"] == "active"){'.PHP_EOL;
				$lmm_out .= '						'.$mapname.'.addLayer(group);'.PHP_EOL;
				$lmm_out .= '					}'.PHP_EOL;
				$lmm_out .= '				}catch(n){}'.PHP_EOL;
				$lmm_out .= '	});'.PHP_EOL;
				$lmm_out .= '}'.PHP_EOL;
				//info: add inactive layers to the controlbox
				$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
				$lmm_out .= '		jQuery.each(ordered_filter_details, function(lid, filter){'.PHP_EOL;
				$lmm_out .= '			if(filter["status"] == "inactive"){'.PHP_EOL;
				$lmm_out .= '				filtered_layers[filter.id] = L.layerGroup();'.PHP_EOL;
				$lmm_out .= ' 			filtered_layers[filter.id]["layer_id"] =filter.id; '.PHP_EOL;
				$lmm_out .= '				if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter_details[filter.id].markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
				$lmm_out .= '				if(filter_details[filter.id]["icon"] != "" && filter_show_icon != "0"){  var filter_icon = "<img src=\'"+ filter_details[filter.id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
				$lmm_out .= '				if(filter_show_name != "0"){ var filter_name = filter_details[filter.id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
				$lmm_out .= '				overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name + "</span>" + markercount ] = filtered_layers[filter.id];'.PHP_EOL;
				$lmm_out .= '			}'.PHP_EOL;
				$lmm_out .= '		});'.PHP_EOL;
				$lmm_out .= ' }'.PHP_EOL;
				//info: add the controlbox to the map, if it is not hidden
				if ($filters_collapsed != 'hidden') {
					$lmm_out .= 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
					$lmm_out .= '		L.control.filters(null, overlays_filters, filters_options).addTo(' . $mapname . ');'.PHP_EOL;
					$lmm_out .= '}'.PHP_EOL;
				}
					//info: perform ajax calls when a filter clicked
					$lmm_out .= 'jQuery(".lmm-filter").on("click", function(){'.PHP_EOL;
					$lmm_out .= '		if(jQuery(this).is(":checked")){'.PHP_EOL;
					$lmm_out .= '         var layer_id = jQuery(this).attr("id");'.PHP_EOL;
					$lmm_out .= '         if(called_layers_'.$mapname.'[layer_id] !== true){'.PHP_EOL;
					$lmm_out .= '				jQuery(".lmm-filter").attr("disabled","disabled");'.PHP_EOL;
					$lmm_out .= '				xhReq.open("GET", "' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/" + layer_id + "/') . '", true);'.PHP_EOL;
					$lmm_out .= '				xhReq.send(null); '.PHP_EOL;
					$lmm_out .= '				called_layers_'.$mapname.'[layer_id] = true;'.PHP_EOL;
					$lmm_out .= '				window["mmp_no_controlbox"] = true;'.PHP_EOL;
					$lmm_out .= '			}'.PHP_EOL;
					$lmm_out .= '		}'.PHP_EOL;
					$lmm_out .= '});'.PHP_EOL;
			}else{
				//info: @TODO Needs testing
				$lmm_out .= 'geojson_markers.addTo(' . $mapname . ');'.PHP_EOL;
			}
		}
		$lmm_out .= '} else { if (window.console) { console.error(xhReq.statusText); } } } }; xhReq.onerror = function (e) { if (window.console) { console.error(xhReq.statusText); } }; xhReq.send(null);'.PHP_EOL; //info: async 2/2

		//info: open highlighted marker via the URL
		if(isset($_GET['highlightmarker']) && is_numeric($_GET['highlightmarker'])) {
			$highlight = intval($_GET['highlightmarker']);
			if($clustering == '1'){
				$lmm_out .= 'function waitForHighlightedMarkers(){'.PHP_EOL;
				$lmm_out .= '	   if(typeof markerID_mapsmarker_'.$uid.'['.$highlight.'] !== "undefined" && markercluster_'.$mapname_js.'.getVisibleParent(markerID_mapsmarker_'.$uid.'['. $highlight .']) != null){'.PHP_EOL;
				$lmm_out .= '	       	var newlocation = markerID_mapsmarker_'.$uid.'['.$highlight.'].getLatLng();'.PHP_EOL;
				$lmm_out .= "			mapsmarker_".$uid.".setView(newlocation,mapsmarker_" . $uid. ".getZoom());".PHP_EOL;
				$lmm_out .= '			try{ markercluster_'.$mapname_js.'.getVisibleParent(markerID_mapsmarker_'.$uid.'['. $highlight .']).spiderfy(); }catch(e){}'.PHP_EOL;
				$lmm_out .= "			markerID_mapsmarker_" . $uid . "[" . $highlight . "].openPopup();".PHP_EOL;
				$lmm_out .= '	   }'.PHP_EOL;
				$lmm_out .= '	    else{'.PHP_EOL;
				$lmm_out .= '	        setTimeout(function(){'.PHP_EOL;
				$lmm_out .= '	            waitForHighlightedMarkers();'.PHP_EOL;
				$lmm_out .= '	        },250);'.PHP_EOL;
				$lmm_out .= '	    }'.PHP_EOL;
				$lmm_out .= '	}'.PHP_EOL;
				$lmm_out .= 'waitForHighlightedMarkers();'.PHP_EOL;
			}else{
				$lmm_out .= PHP_EOL. 'mapsmarker_'.$uid.'.on("layeradd",function(e){'.PHP_EOL;
				$lmm_out .= 'if("feature" in e.layer){'.PHP_EOL;
				$lmm_out .= 'if(e.layer.feature.properties.markerid == "'.$highlight.'"){'.PHP_EOL;
				$lmm_out .= '			mapsmarker_'.$uid.'.setView(new L.LatLng(e.layer.feature.geometry.coordinates[1], e.layer.feature.geometry.coordinates[0]));'.PHP_EOL;
				$lmm_out .= "			markerID_mapsmarker_" . $uid . "[" . $highlight . "].openPopup();".PHP_EOL;
				$lmm_out .= ' 		} '.PHP_EOL;
				$lmm_out .= "	}".PHP_EOL;
				$lmm_out .= "});".PHP_EOL;
			}
		}
		//info: reset control
		if($lmm_options['map_home_button'] != 'false'){
			$zoomhome_ondemand = ($lmm_options['map_home_button'] == 'true-ondemand')?'true':'false';
			$reenableclustering = ($filter_details && $clustering == '1')?'true':'false';
			$lmm_out .= 'var reset_control_'.$mapname.' = L.Control.zoomHome({position: "'. $lmm_options['map_home_button_position'] .'", mapId: "'.$uid.'", mapnameJS: "'.$mapname_js.'", ondemand: '.$zoomhome_ondemand.', zoomHomeTitle:"'.esc_attr__('reset map view','lmm').'", reenableClustering:"' .$reenableclustering.'" });'.PHP_EOL;
			$lmm_out .= 'reset_control_'.$mapname.'.addTo('.$mapname.');'.PHP_EOL;
		}
		if($lmm_options['map_home_button'] == 'true-ondemand'){
			$lmm_out .= $mapname.'.on("moveend",function(e){'.PHP_EOL;
			$lmm_out .= '		jQuery("#leaflet-control-zoomhome-'.$uid.'").show();'.PHP_EOL;
			$lmm_out .= '});'.PHP_EOL;
		}
		//info: leaflet-hash.js (incompatible with jQuery mobile)
		if ($lmm_options['leaflet_hash_status'] == 'enabled') {
			$lmm_out .= 'var maphash_' . $uid . ' = new L.Hash(' . $mapname . ');'.PHP_EOL;
		}

		//info: center map on popup instead of marker
		if ($lmm_options['defaults_marker_popups_center_map'] == 'true') {
			$lmm_out .= $mapname.".on('popupopen', function(e) {".PHP_EOL;
			$lmm_out .= "	var px = ".$mapname.".project(e.popup._latlng);".PHP_EOL;
			$lmm_out .= "	px.y -= e.popup._container.clientHeight/2;".PHP_EOL;
			$lmm_out .= "	".$mapname.".panTo(".$mapname.".unproject(px),{animate: true});".PHP_EOL;
			$lmm_out .= "});".PHP_EOL;
		}
  }

  //info: show alternative error on gelocation fail for Google Chrome
  if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) {
	  $lmm_out .= $mapname . '.on("locationerror",function(e){'.PHP_EOL;
	  $lmm_out .= '	alert("' . sprintf(esc_attr__('Geolocation failed: your current location can only be retrieved if the map is accessed securely using https - see %1$s for more details!','lmm'), 'https://www.mapsmarker.com/geolocation-https-only') . '");'.PHP_EOL;
	  $lmm_out .= '});'.PHP_EOL;
  }

  //info: show loading indicator if popup contains images and update popup after images have loaded to prevent broken popups
  $lmm_out .= PHP_EOL.$mapname.".on('popupopen', function(e) {".PHP_EOL;
  $lmm_out .= '  var popup_markerid = e.popup._source.feature.properties.markerid;'.PHP_EOL;
  $lmm_out .= "  var popup_images = jQuery('.leaflet-popup-content-wrapper #popup-content-'+popup_markerid+' img');".PHP_EOL;
  $lmm_out .= '  if (popup_images.length > 0) {'.PHP_EOL;
  $lmm_out .= '    var image_counter = 0;'.PHP_EOL;
  $lmm_out .= "    jQuery('#popup-content-'+popup_markerid).css('display', 'none');".PHP_EOL;
  $lmm_out .= "    jQuery('#popup-loading-'+popup_markerid).css('display', 'block');".PHP_EOL;
  $lmm_out .= '    jQuery(popup_images).each(function() {'.PHP_EOL;
  $lmm_out .= "      jQuery(this).on('load', function() {".PHP_EOL;
  $lmm_out .= '        image_counter++;'.PHP_EOL;
  $lmm_out .= '        if (image_counter == popup_images.length) {'.PHP_EOL;
  $lmm_out .= "          jQuery('#popup-loading-'+popup_markerid).css('display', 'none');".PHP_EOL;
  $lmm_out .= "          jQuery('#popup-content-'+popup_markerid).css('display', 'block');".PHP_EOL;
  $lmm_out .= '          e.popup.update();'.PHP_EOL;
  $lmm_out .= '        }'.PHP_EOL;
  $lmm_out .= '      });'.PHP_EOL;
  $lmm_out .= '    });'.PHP_EOL;
  $lmm_out .= '  }'.PHP_EOL;
  $lmm_out .= '});'.PHP_EOL;

  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>';
  $lmm_out .= '</body>';
  $lmm_out .= '</html>';
  echo $lmm_out;
  	} //info: end check if marker/layer exists
} //info: end get_query_var('layer', false)
elseif (get_query_var('marker', false)) {
	$markerid = intval(get_query_var('marker'));
	$uid = substr(md5(''.rand()), 0, 8);

	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
		$row = $wpdb->get_row($wpdb->prepare('SELECT `id`,`markername`,`basemap`,`layer`,`lat`,`lon`,`icon`,`popuptext`,`zoom`,`openpopup`,`mapwidth`,`mapwidthunit`,`mapheight`,`panel`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`address`,`gpx_url`,`gpx_panel` FROM `'.$table_name_markers.'` WHERE `id` = %d',$markerid), ARRAY_A);
		if(!empty($row)) {
			$id = $row['id'];
			$markername = esc_js( MMP_Globals::translate_single_string($row['markername'], "Marker (ID {$markerid}) name") );
			$basemap = $row['basemap'];
			//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
			if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
				$basemap = 'osm_mapnik';
			} else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
				$basemap = 'osm_mapnik';
			}
			//info: GoogleMutant fallback for unsupported browsers (automatically switch to OSM)
			if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($lmm_options['google_maps_plugin'] == 'google_mutant') ) {
				if ( MMP_Globals::check_google_mutant_fallback() === TRUE ) {
					$google_mutant_fallback = TRUE;
					if (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) {
						$basemap = 'osm_mapnik';
					}
				} else {
					$google_mutant_fallback = FALSE;
				}
			} else {
				$google_mutant_fallback = FALSE; //info: to avoid PHP undefined warnings
			}
			$lon = $row['lon'];
			$lat = $row['lat'];
			$coords = $lat.', '.$lon;
			$icon = esc_html($row['icon']);
			$popuptext = MMP_Globals::translate_single_string($row['popuptext'], "Marker (ID {$markerid}) popuptext");
			$zoom = $row['zoom'];
			$openpopup = ($row['openpopup'] == 1) ? '.openPopup()' : '';
			$mopenpopup = $openpopup;
			$layer = $row['layer'];
			$mlat = $lat;
			$mlon = $lon;
			$mpopuptext = $popuptext;
			$micon = $icon;
			$mapwidth = $row['mapwidth'];
			$mapwidthunit = $row['mapwidthunit'];
			$mapheight = $row['mapheight'];
			$panel = $row['panel'];
			$paneltext = ($row['markername'] == NULL) ? '&nbsp;' : htmlspecialchars( stripslashes( MMP_Globals::translate_single_string($row['markername'], "Marker (ID {$markerid}) name") ) );
			$controlbox = $row['controlbox'];
			$overlays_custom = $row['overlays_custom'];
			$overlays_custom2 = $row['overlays_custom2'];
			$overlays_custom3 = $row['overlays_custom3'];
			$overlays_custom4 = $row['overlays_custom4'];
			$wms = $row['wms'];
			$wms2 = $row['wms2'];
			$wms3 = $row['wms3'];
			$wms4 = $row['wms4'];
			$wms5 = $row['wms5'];
			$wms6 = $row['wms6'];
			$wms7 = $row['wms7'];
			$wms8 = $row['wms8'];
			$wms9 = $row['wms9'];
			$wms10 = $row['wms10'];
			$address = MMP_Globals::translate_single_string($row['address'], "Marker (ID {$markerid}) address");
			$mapname = 'mapsmarker_'.$uid;
			$mapname_js = 'markermap_' . intval($layer);
			$gpx_url = esc_url($row['gpx_url']);
			$gpx_panel = $row['gpx_panel'];
		}
	//info: check if layer/marker ID exists
	if ($row == NULL) {
		$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $markerid);
		echo $error_marker_not_exists . '<br/>';
		echo '<a href="https://www.mapsmarker.com" target="_blank" title="' . esc_attr__('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-deleted-image.png"></a><br/>';
	} else {

	//info: starting output on frontend
	$lmm_out = '<!DOCTYPE html>'.PHP_EOL;
	$lmm_out .= '<!--[if IE 8]>'.PHP_EOL;
	$lmm_out .= '<html id="ie8" dir="ltr" lang="' . substr($locale, 0, 2) . '">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->'.PHP_EOL;
	$lmm_out .= '<html dir="ltr" lang="' . substr($locale, 0, 2) . '">'.PHP_EOL;
	$lmm_out .= '<!--<![endif]-->'.PHP_EOL;
	$lmm_out .= '<head>'.PHP_EOL;
	if ($markername == '') { $title_markername = get_bloginfo('name'); } else { $title_markername = htmlspecialchars(stripslashes($markername)); }

	$lmm_out .= '<title>' . $title_markername;
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$lmm_out .= ' - ' . __('powered by','lmm') . ' MapsMarker.com';
	}
	$lmm_out .=  ' - ' . get_bloginfo('name') . '</title>'.PHP_EOL;
	$lmm_out .= '<meta charset="UTF-8" />'.PHP_EOL;
	$lmm_out .= '<meta name="geo.position" content="' . $lat . ';' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="ICBM" content="' . $lat . ', ' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="page-type" content="' . __('map','lmm') . '" />'.PHP_EOL;
	//info: viewport + mobile web app settings, details: https://gist.github.com/jdaihl/472519 & https://gist.github.com/tfausak/2222823 & http://developer.apple.com/library/ios/#documentation/userexperience/conceptual/mobilehig/IconsImages/IconsImages.html
	$lmm_out .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">'.PHP_EOL;
	$lmm_out .= '<meta name="apple-mobile-web-app-capable" content="yes">'.PHP_EOL;
	$lmm_out .= '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">'.PHP_EOL;
	$lmm_out .= '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
	if ( $lmm_options['map_webapp_images'] == 'default' ) {
		$ios_icon_57 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-57x57.png';
		$ios_icon_114 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-retina-114x114.png';
		$ios_icon_72 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-72x72.png';
		$ios_icon_144 = LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-retina-144x144.png';
		$ios_launch_1024 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-1024x748.png';
		$ios_launch_2048 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-retina-2048x1496.png';
		$ios_launch_768 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-768x1004.png';
		$ios_launch_1536 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-retina-1536x2008.png';
		$ios_launch_320 = LEAFLET_PLUGIN_URL . 'inc/img/iso-launch-image-iphone-320x460.png';
		$ios_launch_640 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x920.png';
		$ios_launch_640_1096 = LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x1096.png';
	} else if ( $lmm_options['map_webapp_images'] == 'custom' ) {
		$ios_icon_57 = esc_url($lmm_options['map_webapp_icon57']);
		$ios_icon_114 = esc_url($lmm_options['map_webapp_icon114']);
		$ios_icon_72 = esc_url($lmm_options['map_webapp_icon72']);
		$ios_icon_144 = esc_url($lmm_options['map_webapp_icon144']);
		$ios_launch_1024 = esc_url($lmm_options['map_webapp_launch1024']);
		$ios_launch_2048 = esc_url($lmm_options['map_webapp_launch2048']);
		$ios_launch_768 = esc_url($lmm_options['map_webapp_launch768']);
		$ios_launch_1536 = esc_url($lmm_options['map_webapp_launch1536']);
		$ios_launch_320 = esc_url($lmm_options['map_webapp_launch320']);
		$ios_launch_640 = esc_url($lmm_options['map_webapp_launch640']);
		$ios_launch_640_1096 = esc_url($lmm_options['map_webapp_launch640_1096']);
	}
	if ( $lmm_options['map_webapp_images'] != 'none' ) {
		$lmm_out .= '<link rel="apple-touch-icon" href="' . $ios_icon_57 . '">'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon-precomposed" href="' . $ios_icon_57 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon" sizes="114x114" href="' . $ios_icon_114 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon" sizes="72x72" href="' . $ios_icon_72 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-icon" sizes="144x144" href="' . $ios_icon_144 . '" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_1024 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_2048 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_768 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_1536 . '" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_320 . '" media="screen and (max-device-width: 320px)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_640 . '" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
		$lmm_out .= '<link rel="apple-touch-startup-image" href="' . $ios_launch_640_1096 . '" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" />'.PHP_EOL;
	}
	if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-rtl-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-rtl.min.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	} else {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.min.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	}
	$lmm_out .= '<style type="text/css" id="leafletmapsmarker-image-css-override">.leaflet-popup-content img { ' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . ' } .marker-cluster-small {	background-color: ' . htmlspecialchars($lmm_options['clustering_color_small']) . '; } .marker-cluster-small div { background-color: ' . htmlspecialchars($lmm_options['clustering_color_small_inner']) . '; color: ' . htmlspecialchars($lmm_options['clustering_color_small_text']) . '; } .marker-cluster-medium { background-color: ' . htmlspecialchars($lmm_options['clustering_color_medium']) . '; } .marker-cluster-medium div { background-color: ' . htmlspecialchars($lmm_options['clustering_color_medium_inner']) . '; color: ' . htmlspecialchars($lmm_options['clustering_color_medium_text']) . '; } .marker-cluster-large { background-color: ' . htmlspecialchars($lmm_options['clustering_color_large']) . '; } .marker-cluster-large div { background-color: ' . htmlspecialchars($lmm_options['clustering_color_large_inner']) . '; color: ' . htmlspecialchars($lmm_options['clustering_color_large_text']) . '; }</style>'.PHP_EOL;

	//info: Google API key
	if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {
				$lmm_out .= '<script type="text/javascript" src="https://www.google.com/jsapi' . $google_maps_api_key . '"></script>'.PHP_EOL;
		} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {
			if ($lmm_options['google_maps_api_deferred_loading'] == 'disabled') {
				$lmm_out .= '<script type="text/javascript" src="https://www.google.com/jsapi' . $google_maps_api_key . '"></script>'.PHP_EOL;
			}
		}
	}

	//info: Google language localization (JSON API)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
	} else {
		$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
	}
	if ($lmm_options['google_maps_base_domain_custom'] == 'maps.google.com') {
		$gmaps_base_domain = "&base_domain=" . esc_html($lmm_options['google_maps_base_domain']);
	} else {
		$gmaps_base_domain = "&base_domain=" . esc_html($lmm_options['google_maps_base_domain_custom']);
	}

	//info: Google Maps styling
	$google_styling_json = ($lmm_options['google_styling_json'] == NULL) ? 'disabled' : str_replace("\"", "'", $lmm_options['google_styling_json']);

	//info: Bing culture code
	if ($lmm_options['bingmaps_culture'] == 'automatic') {
		if ( $locale != NULL ) { $bing_culture = str_replace("_","-", $locale); } else { $bing_culture =  'en_us'; }
	} else {
		$bing_culture = $lmm_options['bingmaps_culture'];
	}
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= 'var mapsmarkerjspro = {"zoom_in":"' . __('Zoom in','lmm') . '","zoom_out":"' . __('Zoom out','lmm') . '","googlemaps_language":"' . $google_language . '","googlemaps_base_domain":"' . $gmaps_base_domain . '","google_maps_api_key":"' . esc_js(trim($lmm_options['google_maps_api_key'])) . '","bing_culture":"' . $bing_culture . '","google_styling_json":"' . $google_styling_json . '","minimap_show":"' . __( 'Show minimap', 'lmm' ) .'","minimap_hide":"' . __( 'Hide minimap', 'lmm' ) .'","minimap_status":"' . $lmm_options['minimap_status'] . '","fullscreen_button_title":"' . __('View fullscreen','lmm') . '","fullscreen_button_title_exit":"' . __('Exit fullscreen','lmm') . '","fullscreen_button_position":"' . esc_js($lmm_options['map_fullscreen_button_position']) . '","maxzoom":"' . intval($lmm_options['global_maxzoom_level']) . '","google_maps_api_status":"' . esc_js($lmm_options['google_maps_api_status']) . '","meters":"' . __('meters','lmm') . '","feet":"' . __('feet','lmm') . '","gpx_icons_status":"' . esc_js($lmm_options['gpx_icons_status']) . '","google_deferred_loading":"' . esc_js($lmm_options['google_maps_api_deferred_loading']) . '","google_maps_plugin":"' . esc_js($lmm_options['google_maps_plugin']) . '"};'.PHP_EOL;
	$lmm_out .= '/* ]]> */'.PHP_EOL;
	$lmm_out .= '</script>'.PHP_EOL;
	$lmm_out .= '<style>form { margin: 0 ; } </style>'.PHP_EOL; //info: for layer controlbox
	$lmm_out .= '<script type="text/javascript" src="' . includes_url( 'js/jquery/jquery.js' ) . '"></script>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-core.js?ver=' . $plugin_version . '"></script>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-addons.js?ver=' . $plugin_version . '"></script>'.PHP_EOL;

	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= '<script type="text/javascript" src="https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])) . '"></script>'.PHP_EOL;
	}
	$lmm_out .= '</head>'.PHP_EOL;
	$lmm_out .= '<body id="body" style="margin:0;padding:0;height:100%;background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . ';overflow:hidden;">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		if ( function_exists( 'is_rtl' ) && is_rtl() ) { $panel_fullscreen_text = 'text-align:right;'; } else { $panel_fullscreen_text = 'text-align:left;'; }
		//info: set panel margin top for iOS fullscreen maps
		$lmm_out .= '<div id="panel_top_' . $uid . '" class="lmm-panel" style="' . $panel_fullscreen_text . 'background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . '; width:99%; padding:5px;">'.PHP_EOL;
		$lmm_out .= '<span style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ])) . '">' . $paneltext . '</span><span id="lmm-panel-api-fullscreen" class="lmm-panel-api-fullscreen">';
		if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
				//info: Google language localization (directions)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
				} else {
					$google_language = '&hl=' . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: build directions provider links
				if ($lmm_options['directions_provider'] == 'googlemaps') {
					if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = esc_html($lmm_options['google_maps_base_domain']); } else { $gmaps_base_domain_directions = esc_html($lmm_options['google_maps_base_domain_custom']); }
					if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
					if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
					$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
					$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
					$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
					$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
					$lmm_out .= '<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . esc_html($lmm_options[ 'directions_googlemaps_map_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_googlemaps_traffic' ]) . '&doflg=' . esc_html($lmm_options[ 'directions_googlemaps_distance_units' ]) . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . intval($lmm_options[ 'directions_googlemaps_overview_map' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
				} else if ($lmm_options['directions_provider'] == 'yours') {
					if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
					$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . esc_html($lmm_options[ 'directions_yours_type_of_transport' ]) . '&fast=' . intval($lmm_options[ 'directions_yours_route_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_yours_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
				} else if ($lmm_options['directions_provider'] == 'ors') {
					if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
					$lmm_out .= '<a href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&wp=' . $lon . ',' . $lat . '&zoom=' . $zoom . '&routeWeigh=' . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . '&routeOpt=' . esc_html($lmm_options[ 'directions_ors_routeOpt' ]) . '&layer=' . esc_html($lmm_options[ 'directions_ors_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . $directions_transport_type_icon . '" /></a>';
				} else if ($lmm_options['directions_provider'] == 'bingmaps') {
					if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
					$lmm_out .= '<a href="https://www.bing.com/maps/default.aspx?v=2&ampt;rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
				}
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $id . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $id . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
			$lmm_out .= '<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude?marker/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</span></div>'.PHP_EOL;
	}

	//info: set margin top & hide api icon links for iOS fullscreen view
	$lmm_out .= '<script type="text/javascript">if (window.navigator.standalone == true) { document.body.style.margin = "21px 0 0 0"; document.getElementById("lmm-panel-api-fullscreen").style.display = "none"; } </script>'.PHP_EOL;

	//info: add gpx panel
	if ($gpx_url != NULL) {
		$gpx_panel_state = ($gpx_panel == 1) ? 'block' : 'none';
		$lmm_out .= '<div id="gpx-panel-' . $uid . '" class="gpx-panel" style="display:' . $gpx_panel_state . '; background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . ';">'.PHP_EOL;
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') { $gpx_unit_distance = 'km'; $gpx_unit_elevation = 'm'; } else { $gpx_unit_distance = 'mi'; $gpx_unit_elevation = 'ft'; }
		if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
			$gpx_metadata_name = '<label for="gpx-name">' . __('Track name','lmm') . ':</label> <span id="gpx-name" class="gpx-name"></span>';
		} else { $gpx_metadata_name = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
			$gpx_metadata_start = '<label for="gpx-start">' . __('Start','lmm') . ':</label> <span id="gpx-start" class="gpx-start"></span>';
		} else { $gpx_metadata_start = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
			$gpx_metadata_end = '<label for="gpx-end">' . __('End','lmm') . ':</label> <span id="gpx-end" class="gpx-end"></span>';
		} else { $gpx_metadata_end = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
			$gpx_metadata_distance = '<label for="gpx-distance">' . __('Distance','lmm') . ':</label> <span id="gpx-distance"><span class="gpx-distance"></span> ' . $gpx_unit_distance . '</span>';
		} else { $gpx_metadata_distance = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
			$gpx_metadata_duration_moving = '<label for="gpx-duration-moving">' . __('Moving time','lmm') . ':</label> <span id="gpx-duration-moving" class="gpx-duration-moving"></span> ';
		} else { $gpx_metadata_duration_moving = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
			$gpx_metadata_duration_total = '<label for="gpx-duration-total">' . __('Duration','lmm') . ':</label> <span id="gpx-duration-total" class="gpx-duration-total"></span> ';
		} else { $gpx_metadata_duration_total = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
			$gpx_metadata_avpace = '<label for="gpx-avpace">&#216;&nbsp;' . __('Pace','lmm') . ':</label> <span id="gpx-avpace"><span class="gpx-avpace"></span>/' . $gpx_unit_distance . '</span>';
		} else { $gpx_metadata_avpace = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
			$gpx_metadata_avhr = '<label for="gpx-avghr">&#216;&nbsp;' . __('Heart rate','lmm') . ':</label> <span id="gpx-avghr" class="gpx-avghr"></span>';
		} else { $gpx_metadata_avhr = NULL; }
		if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
			$gpx_metadata_elevation_title = '<label for="gpx-elevation">' . __('Elevation','lmm') . ':</label> <span id="gpx-elevation">';
		} else { $gpx_metadata_elevation_title = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
			$gpx_metadata_elev_gain = '+<span class="gpx-elevation-gain"></span>' . $gpx_unit_elevation;
		} else { $gpx_metadata_elev_gain = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
			$gpx_metadata_elev_loss = '-<span class="gpx-elevation-loss"></span>' . $gpx_unit_elevation;
		} else { $gpx_metadata_elev_loss = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
			$gpx_metadata_elev_net = '(' . __('net','lmm') . ': <span class="gpx-elevation-net"></span>' . $gpx_unit_elevation . ')</span>'; //info: </span> ->elevation-ID
		} else { $gpx_metadata_elev_net = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
			$gpx_metadata_elev_full = '<br/><label for="gpx-elevation-full">' . __('Full elevation data','lmm') . ':</label><br/><span id="gpx-elevation-full" class="gpx-elevation-full"></span>';
		} else { $gpx_metadata_elev_full = NULL; }
		if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
			$gpx_metadata_hr_full = '<br/><label for="gpx-heartrate-full">' . __('Full heart rate data','lmm') . ':</label><br/><span id="gpx-heartrate-full" class="gpx-heartrate-full"></span>';
		} else { $gpx_metadata_hr_full = NULL; }
		$gpx_metadata_elevation_array = array($gpx_metadata_elevation_title, $gpx_metadata_elev_gain, $gpx_metadata_elev_loss, $gpx_metadata_elev_net);
		$gpx_metadata_elevation = implode(' ',$gpx_metadata_elevation_array);
		if ($gpx_metadata_elevation == '   ') { $gpx_metadata_elevation = NULL; } //info: for no trailing |
		$gpx_metadata_array_all = array($gpx_metadata_name, $gpx_metadata_start, $gpx_metadata_end, $gpx_metadata_distance, $gpx_metadata_duration_moving, $gpx_metadata_duration_total, $gpx_metadata_avpace, $gpx_metadata_avhr, $gpx_metadata_elevation, $gpx_metadata_elev_full, $gpx_metadata_hr_full);

		$gpx_metadata_array_not_null = array();
		foreach ($gpx_metadata_array_all as $key => $value) {
			if (is_null($value) === false) {
				$gpx_metadata_array_not_null[$key] = $value;
			}
		}
		$gpx_metadata = implode(' <span class="gpx-delimiter">|</span> ',$gpx_metadata_array_not_null);
		$lmm_out .= $gpx_metadata;
		if ( (isset($lmm_options[ 'gpx_metadata_gpx_download' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_gpx_download' ] == 1 ) ) {
			$lmm_out .= ' <span class="gpx-delimiter">|</span> <span id="gpx-download"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/download/?map_type=marker&map_id=' . $id . '&format=gpx') . '" title="' . esc_attr__('download GPX file','lmm') . '">' . esc_attr__('download GPX file','lmm') . ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-download-gpx.png" width="10" height="10" alt="' . esc_attr__('download GPX file','lmm') . '" class="lmm-icon-download-gpx"></a></span>'.PHP_EOL;
		}
		$lmm_out .= '</div>';
	}

	//info: if panel enabled, only 94% height as otherwise attribution wont be visible
	if ($panel == 1) {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:94%; height:auto !important; min-height: 94%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></div>'. PHP_EOL;
	} else {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:100%; height:auto !important; min-height: 100%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></div>'. PHP_EOL;
	}

	//info: add geo microformats
	$lmm_out .= '<div class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;

	//info: add markername to popups?
	if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
		if ($markername != NULL) {
			$markername_popup_hidden = '<div class="popup-markername"  style="border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;">' . stripslashes(strip_tags(htmlspecialchars_decode($markername))) . '</div>';
		} else {
			$markername_popup_hidden = '';
		}
	} else {
		$markername_popup_hidden = '';
	}

	//info: add div for do_shortcode hidden output
	$popuptext_sanitized = MMP_Globals::sanitize_popuptext($popuptext);
	$lmm_out .= '<span style="display:none;" id="'.$mapname.'-popuptext-hidden"><img id="popup-loading-'.$id.'" style="display: none; margin: 20px auto;" src="'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif" /><div id="popup-content-'.$id.'">' . $markername_popup_hidden . do_shortcode($popuptext_sanitized) . '</div></span>'.PHP_EOL;
	if ($lmm_options['directions_popuptext_panel'] == 'yes') {
		if ($lmm_options['directions_provider'] == 'googlemaps') {
			if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = esc_html($lmm_options['google_maps_base_domain']); } else { $gmaps_base_domain_directions = esc_html($lmm_options['google_maps_base_domain_custom']); }
			if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
			$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
			$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
			$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
			$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
			$directionslink = "http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ];
		} else if ($lmm_options['directions_provider'] == 'yours') {
			$directionslink = "http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . intval($lmm_options[ 'directions_yours_route_type' ]) . "&layer=" . esc_html($lmm_options[ 'directions_yours_layer' ]);
		} else if ($lmm_options['directions_provider'] == 'ors') {
			$directionslink = "http://www.openrouteservice.org/?pos=" . $lon . "," . $lat . "&wp=" . $lon . "," . $lat . "&zoom=" . $zoom . "&routeWeigh=" . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . "&routeOpt=" . $lmm_options[ 'directions_ors_routeOpt' ] . "&layer=" . esc_html($lmm_options[ 'directions_ors_layer' ]);
		} else if ($lmm_options['directions_provider'] == 'bingmaps') {
			if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
			$directionslink = "https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to;
		}
		$mpopuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
		$lmm_out .= '<span id="' . $mapname . '-popuptext-dlink-hidden" style="display:none;"><div style="' . $mpopuptext_css . '">' . stripslashes(htmlspecialchars($address)) . ' <a href="' . $directionslink . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">(' . __('Directions','lmm') . ')</a></div></span>';
	}

	//info: preload area for CSS background images (home button etc)
	$lmm_out .= '<div class="lmm-preload-area"></div>'.PHP_EOL;

	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$lmm_out .= '/* Maps created with Maps Marker Pro - #1 premium mapping plugin for WordPress (www.mapsmarker.com) */'.PHP_EOL;
	}
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var mapsmarker_'.$uid.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
		$attrib_prefix = '<a href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Maps Marker Pro - #1 mapping plugin for WordPress','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Maps Marker Pro') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Maps Marker Pro') . '\">icons</a>)';
	} else {
		$attrib_prefix = '';
	}
		//info: add edit link
	if ( (current_user_can($lmm_options[ 'capabilities_edit_others' ])) || ((current_user_can( $lmm_options[ 'capabilities_edit' ]) && ( $current_user->user_login == $createdby))) ) {
		if (!empty($layer)) {
			$attrib_prefix = '<a href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $id . '\"><img style=\"display:inline;\" src=\"' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png\" width=\"16\" height=\"16\" alt=\"' . esc_attr__('edit layer','lmm') . ' ID ' . $id . '\" title=\"' . esc_attr__('edit layer','lmm') . ' ID ' . $id . '\"></a> ' . $attrib_prefix;
		} else if (!empty($marker)) {
			$attrib_prefix = '<a href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $id . '\"><img style=\"display:inline;\" src=\"' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png\" width=\"16\" height=\"16\" alt=\"' . esc_attr__('edit marker','lmm') . ' ID ' . $id . '\" title=\"' . esc_attr__('edit marker','lmm') . ' ID ' . $id . '\"></a> ' . $attrib_prefix;
		} else if (empty($layer) && empty($marker)) {
			$attrib_prefix = '<img style=\"display:inline;\" src=\"' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png\" width=\"16\" height=\"16\" alt=\"' . __('marker created directly by using shortcode - no edit link available','lmm') . '\" title=\"' . __('marker created directly by using shortcode - no edit link available','lmm') . '\"></a> ' . $attrib_prefix;
		}
	}
	$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
	$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	$dragging_setting = ($lmm_options['misc_map_dragging'] == 'false-touch') ? '!L.Browser.mobile' : esc_js($lmm_options['misc_map_dragging']);
	$maxzoom = intval($lmm_options['global_maxzoom_level']);
	 //info: true for leaflet-fullscreen.php only
	if ( ($lmm_options['misc_map_scrollwheelzoom'] == 'true') || ($lmm_options['misc_map_scrollwheelzoom'] == 'true-fullscreen-only') ){
		$scrollwheelzoom_setting = 'true';
	} else	if ($lmm_options['misc_map_scrollwheelzoom'] == 'false') {
		$scrollwheelzoom_setting = 'false';
	}
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { dragging: ' . $dragging_setting . ', touchZoom: ' . esc_js($lmm_options['misc_map_touchzoom']) . ', scrollWheelZoom: ' . $scrollwheelzoom_setting . ', doubleClickZoom: ' . esc_js($lmm_options['misc_map_doubleclickzoom']) . ', boxzoom: ' . esc_js($lmm_options['map_interaction_options_boxzoom']) . ', trackResize: ' . esc_js($lmm_options['misc_map_trackresize']) . ', worldCopyJump: ' . esc_js($lmm_options['map_interaction_options_worldcopyjump']) . ', closePopupOnClick: ' . esc_js($lmm_options['misc_map_closepopuponclick']) . ', keyboard: ' . esc_js($lmm_options['map_keyboard_navigation_options_keyboard']) . ', keyboardPanDelta: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardpandelta']) . ', inertia: ' . esc_js($lmm_options['map_panning_inertia_options_inertia']) . ', inertiaDeceleration: ' . intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) . ', inertiaMaxSpeed: ' . intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) . ', zoomControl: ' . $lmm_options['misc_map_zoomcontrol'] . ', crs: ' . esc_js($lmm_options['misc_projections']) . ', fullscreenControl: ' . esc_js($lmm_options['map_fullscreen_button']) . ', tap: ' . esc_js($lmm_options['map_interaction_options_tap']) . ', tapTolerance: ' . intval($lmm_options['map_interaction_options_taptolerance']) . ', bounceAtZoomLimits: ' . esc_js($lmm_options['map_interaction_options_bounceatzoomlimits']) . ' });'.PHP_EOL;

	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified") (Google Mutant) - not sure if needed in leaflet-fullscreen.php too
	$lmm_out .= $mapname.'._layersMaxZoom = ' . $maxzoom . ';'.PHP_EOL;

	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;
	//info: define basemaps
	$osm_attrib_general = __("Map",'lmm').': &copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>';
	if ($lmm_options['openstreetmap_variants'] == 'osm-mapnik') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 19;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-blackandwhite') {
		$osm_tile_url = 'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 18;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-de') {
		$osm_tile_url = 'http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 19;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-france') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = __("Map",'lmm').': &copy; <a href=\"https://www.openstreetmap.fr\" target=\"_blank\">Openstreetmap France</a> & <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-hot') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = $osm_attrib_general . ', ' . __("Tiles courtesy of","lmm") . ' <a href=\"https://hotosm.org/\" target=\"_blank\">Humanitarian OpenStreetMap Team</a>' . $osm_editlink;
	}
	//info: define edgeBuffertiles
	if ($lmm_options['map_interaction_options_bounceatzoomlimits'] != '0') {
		$edgebuffertiles = ', edgeBufferTiles: ' . floatval(str_replace(",",".", $lmm_options['edgeBufferTiles']));
	} else {
		$edgebuffertiles = '';
	}
	$error_tile_url = $lmm_options['basemaps_nowrap_enabled'] == 'true' ? '' : LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png';
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' . $osm_attribution . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});';
	$lmm_out .= 'var stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_terrain_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' .  $attrib_stamen . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_toner_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' .  $attrib_stamen . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' .  $attrib_stamen . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;

	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= 'if (typeof MQ !== "undefined") {';
		$lmm_out .= 'mapquest_osm = new MQ.mapLayer();';
		$lmm_out .= 'mapquest_aerial = new MQ.satelliteLayer();';
		$lmm_out .= 'mapquest_hybrid = new MQ.hybridLayer();';
		$lmm_out .= '} else { if (window.console) { console.log("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); } }';
	}

	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === TRUE) ) {
			$lmmjs_out .= 'if (window.console) { console.log("' . esc_attr__('You are using an outdated browser therefore maps automatically switched to OpenStreetMap. An update to a current browser is recommended.','lmm') . '"); }'.PHP_EOL;
		} elseif ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

			if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js' . $google_maps_api_key . $google_language;
				$lmm_out .= 'var deferred_google_layers = {
		roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }}
	};
	var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
	var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
	var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
	var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
			} else { //info: undeferred loading
				$lmm_out .= 'var googleLayer_roadmap = new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				$lmm_out .= 'var googleLayer_satellite = new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				$lmm_out .= 'var googleLayer_hybrid = new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				$lmm_out .= 'var googleLayer_terrain = new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
			}

		} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {
			if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '&key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js?callback=L.Google.asyncInitialize' . $google_maps_api_key . $google_language;
				$lmm_out .= 'var deferred_google_layers = {
		roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }},
		satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }},
		hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }},
		terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '}); }}
	};
	var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
	var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
	var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
	var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
			} else { //info: undeferred loading
				$lmm_out .= 'var googleLayer_roadmap = new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
				$lmm_out .= 'var googleLayer_satellite = new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
				$lmm_out .= 'var googleLayer_hybrid = new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
				$lmm_out .= 'var googleLayer_terrain = new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
			}
		}
	}
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
		$lmm_out .= 'var bingaerial = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
		$lmm_out .= 'var bingaerialwithlabels = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
		$lmm_out .= 'var bingroad = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	};
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	//info: MapBox basemaps
	if ($lmm_options[ 'mapbox_access_token' ] != NULL) {
		$lmm_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox_access_token' ])) . '&secure=1", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	} else {  //info: v3 fallback for default maps
		$lmm_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($lmm_options[ 'mapbox2_access_token' ] != NULL) {
		$lmm_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox2_access_token' ])) . '&secure=1", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	} else {
		$lmm_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($lmm_options[ 'mapbox3_access_token' ] != NULL) {
		$lmm_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox3_access_token' ])) . '&secure=1", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	} else {
		$lmm_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
    $lmm_out .= 'var custom_basemap = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', tms: ' . esc_js($lmm_options[ 'custom_basemap_tms' ]) . ', ' . $error_tile_url_custom_basemap . 'attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap_nowrap_enabled' ] . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . '});'.PHP_EOL;
 	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', tms: ' . esc_js($lmm_options[ 'custom_basemap2_tms' ]) . ', ' . $error_tile_url_custom_basemap2 . 'attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap2_nowrap_enabled' ] . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', tms: ' . esc_js($lmm_options[ 'custom_basemap3_tms' ]) . ', ' . $error_tile_url_custom_basemap3 . 'attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap3_nowrap_enabled' ] . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . '});'.PHP_EOL;
	$lmm_out .= 'var empty_basemap = new L.TileLayer("");'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';
	$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';
	$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';
	$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . $error_tile_url . '", ' : '';

	//info: define overlays
    $lmm_out .= 'var overlays_custom = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom_tms' ] . ', ' . $error_tile_url_overlays_custom . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom2_tms' ] . ', ' . $error_tile_url_overlays_custom2 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom2_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom3_tms' ] . ', ' . $error_tile_url_overlays_custom3 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom3_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom4_tms' ] . ', ' . $error_tile_url_overlays_custom4 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom4_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;

	//info: check if subdomains are set for wms layers
	$wms_subdomains = ((isset($lmm_options[ 'wms_wms_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms2_subdomains = ((isset($lmm_options[ 'wms_wms2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms3_subdomains = ((isset($lmm_options[ 'wms_wms3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms4_subdomains = ((isset($lmm_options[ 'wms_wms4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms5_subdomains = ((isset($lmm_options[ 'wms_wms5_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms5_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms5_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms6_subdomains = ((isset($lmm_options[ 'wms_wms6_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms6_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms6_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms7_subdomains = ((isset($lmm_options[ 'wms_wms7_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms7_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms7_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms8_subdomains = ((isset($lmm_options[ 'wms_wms8_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms8_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms8_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms9_subdomains = ((isset($lmm_options[ 'wms_wms9_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms9_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms9_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms10_subdomains = ((isset($lmm_options[ 'wms_wms10_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms10_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms10_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define wms legends
	$wms_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms2_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms2_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms2_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms2_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms3_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms3_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms3_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms3_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms4_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms4_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms4_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms4_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms5_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms5_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms5_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms5_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms6_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms6_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms6_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms6_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms7_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms7_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms7_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms7_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms8_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms8_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms8_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms8_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms9_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms9_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms9_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms9_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	$wms10_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms10_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms10_legend' ] != NULL )) ? ' (<a href=\"' . wp_kses($lmm_options[ 'wms_wms10_legend' ], $allowedtags) . '\" target=\"_blank\">' . __('Legend','lmm') . '</a>)' : '') .'';
	//info: define wms layers
	if ($wms == 1) {
	$lmm_out .= 'var wms = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) . '", {wmsid: "wms", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ])) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ])) . '"' . $wms_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms2 == 1) {
	$lmm_out .= 'var wms2 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) . '", {wmsid: "wms2", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ])) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ])) . '"' . $wms2_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms3 == 1) {
	$lmm_out .= 'var wms3 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) . '", {wmsid: "wms3", layers: "' . htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ]))) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ])) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ])) . '"' . $wms3_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms4 == 1) {
	$lmm_out .= 'var wms4 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) . '", {wmsid: "wms4", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ])) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ])) . '"' . $wms4_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms5 == 1) {
	$lmm_out .= 'var wms5 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) . '", {wmsid: "wms5", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ])) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ])) . '"' . $wms5_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms6 == 1) {
	$lmm_out .= 'var wms6 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) . '", {wmsid: "wms6", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ])) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ])) . '"' . $wms6_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms7 == 1) {
	$lmm_out .= 'var wms7 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) . '", {wmsid: "wms7", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ])) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ])) . '"' . $wms7_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms8 == 1) {
	$lmm_out .= 'var wms8 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) . '", {wmsid: "wms8", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ])) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ])) . '"' . $wms8_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms9 == 1) {
	$lmm_out .= 'var wms9 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) . '", {wmsid: "wms9", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ])) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ])) . '"' . $wms9_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if ($wms10 == 1) {
	$lmm_out .= 'var wms10 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) . '", {wmsid: "wms10", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ])) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . $error_tile_url . '", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ])) . '"' . $wms10_subdomains  . ', detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles . ', noWrap: ' . esc_js($lmm_options['basemaps_nowrap_enabled']) . '});'.PHP_EOL;
	}
	if( (isset($controlbox) == TRUE) && ($controlbox != 0) ){
		//info: controlbox - basemaps
		$lmm_out .= 'var layersControl = new L.Control.Layers('.PHP_EOL;
		$lmm_out .= '{';
		$basemaps_available = "";
		if ( (isset($lmm_options[ 'controlbox_osm_mapnik' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_mapnik' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_osm_mapnik' ])) . "': osm_mapnik,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_terrain' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_terrain' ])) . "': stamen_terrain,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_toner' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_toner' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_toner' ])) . "': stamen_toner,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_watercolor' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_watercolor' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_watercolor' ])) . "': stamen_watercolor,";
		}
		if ($lmm_options[ 'mapquest_api_key' ] != NULL) {
			if ( (isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_osm' ])) . "': mapquest_osm,";
			}
			if ( (isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_aerial' ])) . "': mapquest_aerial,";
			}
			if ( (isset($lmm_options[ 'controlbox_mapquest_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_hybrid' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_hybrid' ])) . "': mapquest_hybrid,";
			}
		}
		if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) ) {
			if ( (isset($lmm_options[ 'controlbox_googleLayer_roadmap' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_roadmap' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_roadmap' ])) . "': googleLayer_roadmap,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_satellite' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_satellite' ])) . "': googleLayer_satellite,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_hybrid' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_hybrid' ])) . "': googleLayer_hybrid,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_terrain' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_terrain' ])) . "': googleLayer_terrain,";
			}
		}
		if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
			if ( (isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingaerial' ])) . "': bingaerial,";
			}
			if ( (isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingaerialwithlabels' ])) . "': bingaerialwithlabels,";
			}
			if ( (isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingroad' ])) . "': bingroad,";
			}
		};
		if ( (isset($lmm_options[ 'controlbox_ogdwien_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_basemap' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_ogdwien_basemap' ])) . "': ogdwien_basemap,";
		}
		if ( (isset($lmm_options[ 'controlbox_ogdwien_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_satellite' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_ogdwien_satellite' ])) . "': ogdwien_satellite,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox_name' ]))."': mapbox,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox2' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox2' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox2_name' ]))."': mapbox2,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox3' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox3' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox3_name' ]))."': mapbox3,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap_name' ]))."': custom_basemap,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap2' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap2' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap2_name' ]))."': custom_basemap2,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap3' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap3' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap3_name' ]))."': custom_basemap3,";
		}
		if ( (isset($lmm_options[ 'controlbox_empty_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_empty_basemap' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'empty_basemap_name' ]))."': empty_basemap,";
		}
		//info: needed for IE7 compatibility
		$lmm_out .= substr($basemaps_available, 0, -1);
		$lmm_out .= '},'.PHP_EOL;

		//info: controlbox - add available overlays
	    $lmm_out .= '{';
	    $overlays_custom_available = '';
	    if ( ((isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 )) || ($overlays_custom == 1) )
	        $overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom_name' ]))."': overlays_custom,";
	    if ( ((isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 )) || ($overlays_custom2 == 1) )
	        $overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom2_name' ]))."': overlays_custom2,";
	    if ( ((isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 )) || ($overlays_custom3 == 1) )
	        $overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom3_name' ]))."': overlays_custom3,";
	    if ( ((isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 )) || ($overlays_custom4 == 1) )
	    	$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom4_name' ]))."': overlays_custom4,";
		//info: needed for IE7 compatibility
		$lmm_out .= substr($overlays_custom_available, 0, -1);
		$lmm_out .= '},'.PHP_EOL;

		//info: controlbox - hidden / collapsed / expanded status
		if ( (isset($controlbox) == TRUE ) && ( $controlbox == 0 ) )
			$lmm_out .= '{ } );';
		if ( (isset($controlbox) == TRUE ) && ( $controlbox == 1 ) )
			$lmm_out .= '{ collapsed: true } );';
		if ( (isset($controlbox) == TRUE ) && ( $controlbox == 2 ) )
			$lmm_out .= '{ collapsed: false } );';
	}
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;

	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? $mapname.".addControl(layersControl);" : "".PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ');';
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= $mapname.".addLayer(wms);".PHP_EOL;
	if ( $wms2 == 1 )
		$lmm_out .= $mapname.".addLayer(wms2);".PHP_EOL;
	if ( $wms3 == 1 )
		$lmm_out .= $mapname.".addLayer(wms3);".PHP_EOL;
	if ( $wms4 == 1 )
		$lmm_out .= $mapname.".addLayer(wms4);".PHP_EOL;
	if ( $wms5 == 1 )
		$lmm_out .= $mapname.".addLayer(wms5);".PHP_EOL;
	if ( $wms6 == 1 )
		$lmm_out .= $mapname.".addLayer(wms6);".PHP_EOL;
	if ( $wms7 == 1 )
		$lmm_out .= $mapname.".addLayer(wms7);".PHP_EOL;
	if ( $wms8 == 1 )
		$lmm_out .= $mapname.".addLayer(wms8);".PHP_EOL;
	if ( $wms9 == 1 )
		$lmm_out .= $mapname.".addLayer(wms9);".PHP_EOL;
	if ( $wms10 == 1 )
		$lmm_out .= $mapname.".addLayer(wms10);".PHP_EOL;

	//info: controlbox - check active overlays on marker/layer level
	//2do - remove isset-check - not necessary anymore, as sql result check is now global
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom);".PHP_EOL;
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom2);".PHP_EOL;
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom3);".PHP_EOL;
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= $mapname.".addLayer(overlays_custom4);".PHP_EOL;
	//info: add minimap
	if ($lmm_options['minimap_status'] != 'hidden') {
		$lmm_out .= 'var osm_mapnik_minimap = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		$lmm_out .= 'var stamen_terrain_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_terrain_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		$lmm_out .= 'var stamen_toner_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_toner_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		$lmm_out .= 'var stamen_watercolor_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		//info: MapQuest minimap
		if ($lmm_options['mapquest_api_key'] != NULL) {
			$lmm_out .= 'if (typeof MQ !== "undefined") {';
			$lmm_out .= 'mapquest_osm_minimap = new MQ.mapLayer();';
			$lmm_out .= 'mapquest_aerial_minimap = new MQ.satelliteLayer();';
			$lmm_out .= 'mapquest_hybrid_minimap = new MQ.hybridLayer();';
			$lmm_out .= '}';
		}
		//info: google maps minimap
		if ($lmm_options['google_maps_api_status'] == 'enabled') {
			if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

				if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
						$lmm_out .= 'var deferred_google_layers_minimap = {
		roadmap: { name: "roadmap minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		satellite: { name: "satellite minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		hybrid: { name: "hybrid minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
		terrain: { name: "terrain minimap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }}
	};'.PHP_EOL;
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers_minimap.roadmap);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers_minimap.satellite);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers_minimap.hybrid);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers_minimap.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				}

			} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {
				if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
						$lmm_out .= 'var deferred_google_layers_minimap = {
		roadmap: { name: "roadmap minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
		satellite: { name: "satellite minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
		hybrid: { name: "hybrid minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
		terrain: { name: "terrain minimap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }}
	};'.PHP_EOL;
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers_minimap.roadmap);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers_minimap.satellite);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers_minimap.hybrid);'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers_minimap.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					$lmm_out .= 'var googleLayer_roadmap_minimap = new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					$lmm_out .= 'var googleLayer_satellite_minimap = new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					$lmm_out .= 'var googleLayer_hybrid_minimap = new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					$lmm_out .= 'var googleLayer_terrain_minimap = new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				}
			}
		}
		//info: bing minimaps
		if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
			$lmm_out .= 'var bingaerial_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			$lmm_out .= 'var bingaerialwithlabels_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			$lmm_out .= 'var bingroad_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
		};
		if ($lmm_options['minimap_zoomLevelFixed'] != NULL) { $zoomlevelfixed =  'zoomLevelFixed: ' . intval($lmm_options['minimap_zoomLevelFixed']) . ','; } else { $zoomlevelfixed = ''; }
		if ($lmm_options['minimap_basemap'] == 'automatic') {
			if ($basemap == 'osm_mapnik') {
				$minimap_basemap = 'osm_mapnik_minimap';
			} else if ($basemap == 'stamen_terrain') {
				$minimap_basemap = 'stamen_terrain_minimap';
			} else if ($basemap == 'stamen_toner') {
				$minimap_basemap = 'stamen_toner_minimap';
			} else if ($basemap == 'stamen_watercolor') {
				$minimap_basemap = 'stamen_watercolor_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_osm')){
				$minimap_basemap = 'mapquest_osm_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_aerial')){
				$minimap_basemap = 'mapquest_aerial_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_hybrid')){
				$minimap_basemap = 'mapquest_hybrid_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_roadmap') ) {
				$minimap_basemap = 'googleLayer_roadmap_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_satellite') ) {
				$minimap_basemap = 'googleLayer_satellite_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_hybrid') ) {
				$minimap_basemap = 'googleLayer_hybrid_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_terrain') ) {
				$minimap_basemap = 'googleLayer_terrain_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerial')){
				$minimap_basemap = 'bingaerial_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerialwithlabels')){
				$minimap_basemap = 'bingaerialwithlabels_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingroad')){
				$minimap_basemap = 'bingroad_minimap';
			} else {
				$minimap_basemap = 'osm_mapnik_minimap';
			}
		} else {
			$minimap_basemap = $lmm_options['minimap_basemap'];
			//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
			if (($lmm_options['google_maps_api_status'] == 'disabled') && (($minimap_basemap == 'googleLayer_roadmap') || ($minimap_basemap == 'googleLayer_satellite') || ($minimap_basemap == 'googleLayer_hybrid') || ($minimap_basemap == 'googleLayer_terrain')) ) {
				$minimap_basemap = 'osm_mapnik_minimap';
			} else if (($lmm_options['mapquest_api_key'] == NULL) && (($minimap_basemap == 'mapquest_osm') || ($minimap_basemap == 'mapquest_aerial') || ($minimap_basemap == 'mapquest_hybrid')) ) {
				$minimap_basemap = 'osm_mapnik_minimap';
			}
		}
		$minimap_minimized = ($lmm_options['minimap_status'] == 'collapsed') ? 'true' : 'false';
		$lmm_out .= "var miniMap = new L.Control.MiniMap(" . $minimap_basemap . ", {position: '" . esc_js($lmm_options['minimap_position']) . "', width: " . intval($lmm_options['minimap_width']) . ", height: " . intval($lmm_options['minimap_height']) . ", collapsedWidth: " . intval($lmm_options['minimap_collapsedWidth']) . ", collapsedHeight: " . intval($lmm_options['minimap_collapsedHeight']) . ", zoomLevelOffset: " . intval($lmm_options['minimap_zoomLevelOffset']) . ", " . $zoomlevelfixed . " zoomAnimation: " . esc_js($lmm_options['minimap_zoomAnimation']) . ", toggleDisplay: " . esc_js($lmm_options['minimap_toggleDisplay']) . ", autoToggleDisplay: " . esc_js($lmm_options['minimap_autoToggleDisplay']) . ", minimized: " . $minimap_minimized . "}).addTo(" . $mapname . ");".PHP_EOL;
	}
	//info: gpx tracks
	if ($gpx_url != NULL) {
		$gpx_track_color = '#' . str_replace('#', '', esc_js($lmm_options['gpx_track_color']));
		$gpx_startIconUrl = ($lmm_options['gpx_startIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png' : esc_url($lmm_options['gpx_startIconUrl']);
		$gpx_endIconUrl = ($lmm_options['gpx_endIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png' : esc_url($lmm_options['gpx_endIconUrl']);
		$gpx_shadowUrl = ($lmm_options['gpx_shadowUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png' : esc_url($lmm_options['gpx_shadowUrl']);
		if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
			$gpx_metadata_name_js = 'if (gpx.get_name() != undefined) { _c("gpx-name").innerHTML = gpx.get_name(); } else { _c("gpx-name").innerHTML = "n/a"; }';
		} else { $gpx_metadata_name_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
			$gpx_metadata_start_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-start").innerHTML = gpx.get_start_time().toDateString() + ", " + gpx.get_start_time().toLocaleTimeString(); } else { _c("gpx-start").innerHTML = "n/a"; }';
		} else { $gpx_metadata_start_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
			$gpx_metadata_end_js = 'if (gpx.get_end_time() != undefined) { _c("gpx-end").innerHTML = gpx.get_end_time().toDateString() + ", " + gpx.get_end_time().toLocaleTimeString(); } else { _c("gpx-end").innerHTML = "n/a"; }';
		} else { $gpx_metadata_end_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = (gpx.get_distance()/1000).toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			} else {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = gpx.get_distance_imp().toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_distance_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
			$gpx_metadata_duration_moving_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-moving").innerHTML = gpx.get_duration_string(gpx.get_moving_time()); } else { _c("gpx-duration-moving").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_moving_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
			$gpx_metadata_duration_total_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-total").innerHTML = gpx.get_duration_string(gpx.get_total_time()); } else { _c("gpx-duration-total").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_total_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			} else {
			$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace_imp(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_avpace_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
			$gpx_metadata_avhr_js = 'if (isNaN(gpx.get_average_hr())) { _c("gpx-avghr").innerHTML = "n/a"; } else { _c("gpx-avghr").innerHTML = gpx.get_average_hr() + "bpm"; }';
		} else { $gpx_metadata_avhr_js = ''; }
		if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
			$gpx_metadata_elevation_title_js = '';
		} else { $gpx_metadata_elevation_title_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.get_elevation_gain().toFixed(0);';
			} else {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_gain_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_loss_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.get_elevation_gain().toFixed(0) - gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.to_ft(gpx.get_elevation_gain() - gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_net_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data();';
			} else {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data_imp();';
			}
		} else { $gpx_metadata_elev_full_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data();';
			} else {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data_imp();';
			}
		} else { $gpx_metadata_hr_full_js = ''; }

			//info: load gpx_content
			$gpx_content_array = wp_remote_get( $gpx_url, array( "sslverify" => false, "timeout" => 30 ) );

			//info: do not load GPX if error on wp_remote_get occured
			if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
				$gpx_content = esc_js(str_replace("\xEF\xBB\xBF",'',$gpx_content_array['body'])); //info: replace UTF8-BOM for Chrome
				//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
				if ( (strrpos($gpx_content, '&lt;?xml') !== FALSE) && (strrpos($gpx_content, '&lt;?xml') != 0) ){
					$position = strrpos($gpx_content, '&lt;?xml');
					$gpx_content = substr($gpx_content, $position);
				}
			} else {
				$gpx_content = '';
			}
			$lmm_out .= '
				function display_gpx_' . $uid . '() {
					var gpx_panel = document.getElementById("gpx-panel-' . $uid . '");
					var gpx_url = "'.$gpx_url.'";

					function _c(c) { return gpx_panel.querySelectorAll("."+c)[0]; }

					var gpx_track = new L.GPX(gpx_url, {
						gpx_content: "'.$gpx_content.'",
						async: true,
						max_point_interval: ' . intval($lmm_options['gpx_max_point_interval']) . ',
						marker_options: {
							startIconUrl: "' . $gpx_startIconUrl . '",
							endIconUrl: "' . $gpx_endIconUrl . '",
							shadowUrl: "' . $gpx_shadowUrl . '",
							iconSize: [' . intval($lmm_options['gpx_iconSize_x']) . ', ' . intval($lmm_options['gpx_iconSize_y']) . '],
							shadowSize: [' . intval($lmm_options['gpx_shadowSize_x']) . ', ' . intval($lmm_options['gpx_shadowSize_y']) . '],
							iconAnchor: [' . intval($lmm_options['gpx_iconAnchor_x']) . ', ' . intval($lmm_options['gpx_iconAnchor_y']) . '],
							shadowAnchor: [' . intval($lmm_options['gpx_shadowAnchor_x']) . ', ' . intval($lmm_options['gpx_shadowAnchor_y']) . '],
							className: "lmm_gpx_icons"
						},
						polyline_options: {
							color: "' . $gpx_track_color . '",
							weight: ' . intval($lmm_options['gpx_track_weight']) . ',
							opacity: "' . floatval($lmm_options['gpx_track_opacity']) . '",
							smoothFactor: "' . str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])) . '",
							interactive: ' . esc_js($lmm_options['gpx_track_clickable']) . ',
							noClip: ' . esc_js($lmm_options['gpx_track_noClip']) . '
						}
					}).addTo(' . $mapname . ');
					gpx_track.on("gpx_loaded", function(e) {
						var gpx = e.target;
						' . $gpx_metadata_name_js . '
						' . $gpx_metadata_start_js . '
						' . $gpx_metadata_end_js . '
						' . $gpx_metadata_distance_js . '
						' . $gpx_metadata_duration_moving_js . '
						' . $gpx_metadata_duration_total_js . '
						' . $gpx_metadata_avpace_js . '
						' . $gpx_metadata_avhr_js . '
						' . $gpx_metadata_elev_gain_js . '
						' . $gpx_metadata_elev_loss_js . '
						' . $gpx_metadata_elev_net_js . '
						' . $gpx_metadata_elev_full_js . '
						' . $gpx_metadata_hr_full_js . '
					});
				}'.PHP_EOL;
		//info: to prevent console XML prsing errors
		if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
			$lmm_out .= 'display_gpx_' . $uid . '();'.PHP_EOL;
		} else {
			$gpx_url_error = (current_user_can( 'manage_options' )) ? '	if (window.console) { console.log("' . esc_attr__('Error', 'lmm') . ' ' . $gpx_content_array['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), $gpx_url) . '"); }'.PHP_EOL : '';
			$lmm_out .= $gpx_url_error;
		}
	}

	//info: add scale control
	if ( $lmm_options['map_scale_control'] == 'enabled' ) {
		$lmm_out .= "L.control.scale({position:'" . esc_js($lmm_options['map_scale_control_position']) . "', maxWidth: " . intval($lmm_options['map_scale_control_maxwidth']) . ", metric: " . esc_js($lmm_options['map_scale_control_metric']) . ", imperial: " . esc_js($lmm_options['map_scale_control_imperial']) . ", updateWhenIdle: " . esc_js($lmm_options['map_scale_control_updatewhenidle']) . "}).addTo(" . $mapname . ");".PHP_EOL;
	}

	//info: add geolocate control
	if ($lmm_options['geolocate_status'] == 'true') {
		if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) { $onlocationerror = ', onLocationError: function () {}'; } else { $onlocationerror = ''; }
		//info: prepare geolocate setView
		if ($lmm_options[ 'geolocate_setView' ] == 'false') {
			$geolocate_setview = "false";
		} else {
			$geolocate_setview = "'" . esc_js($lmm_options[ 'geolocate_setView' ]) . "'";
		}
		$lmm_out .= "var locatecontrol = L.control.locate({	position: '" . esc_js($lmm_options[ 'geolocate_position' ]) . "', drawCircle: " . esc_js($lmm_options[ 'geolocate_drawCircle' ]) . ", drawMarker: " . esc_js($lmm_options[ 'geolocate_drawMarker' ]) . ", setView: " . $geolocate_setview . ", keepCurrentZoomLevel: " . esc_js($lmm_options[ 'geolocate_keepCurrentZoomLevel' ]) . ", clickBehavior: { inView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_inView' ]) . "', outOfView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_outOfView' ]) . "'}, circleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_circleStyle' ])) . "}, markerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_markerStyle' ])) . "}, followCircleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followCircleStyle' ])) . "}, followMarkerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followMarkerStyle' ])) . "}, icon: '" . esc_js($lmm_options[ 'geolocate_icon' ]) . "', circlePadding: " . esc_js($lmm_options[ 'geolocate_circlePadding' ]) . ", metric: " . esc_js($lmm_options[ 'geolocate_units' ]) . ", showPopup: " . esc_js($lmm_options[ 'geolocate_showPopup' ]) . ", strings: { title: '" . __('Show me where I am','lmm') . "', metersUnit: '" . __('meters','lmm') . "', feetUnit: '" . __('feet','lmm') . "', popup: '" . sprintf(__('You are within %1$s %2$s from this point','lmm'), '{distance}', '{unit}') . "', outsideMapBoundsMsg: '" . __('You seem located outside the boundaries of the map','lmm') . "' }, locateOptions: { " . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_locateOptions' ])) . " }" . $onlocationerror . " }).addTo(" . $mapname . ");".PHP_EOL;
		if ( $lmm_options['geolocate_autostart'] == 'true' ) {
			$lmm_out .= "locatecontrol.start();";
		}
	}

	//info: js for marker only
	if (!(empty($mlat) or empty($mlon)) ) {
		$markername_title = strip_tags(htmlspecialchars_decode($markername));
		if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show' && ($lmm_options[ 'marker_tooltip_status' ] == 'disabled')) {
			$defaults_marker_icon_title = "title: '" . $markername_title . "', ";
		} else {
			$defaults_marker_icon_title = "";
		};
		$lmm_out .= 'var marker = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'),{ ' . $defaults_marker_icon_title . ' opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . ', alt: "' . $markername_title . '"});'.PHP_EOL;
		if ($micon == NULL) {
			$lmm_out .= "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
		} else {
			$lmm_out .= "marker.options.icon = new L.Icon({iconUrl: '" . $defaults_marker_icon_url . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
		};

		//info: marker tooltips
		if ($lmm_options[ 'marker_tooltip_status' ] == 'enabled') {
			if ($markername_title != NULL) {
				$lmm_out .= "marker.bindTooltip('" . $markername_title . "', {offset: L.point(" . intval($lmm_options[ 'marker_tooltip_offset_x' ]) . "," . intval($lmm_options[ 'marker_tooltip_offset_y' ]) . "), direction: '" . esc_js($lmm_options[ 'marker_tooltip_direction' ]) . "', permanent: " . esc_js($lmm_options[ 'marker_tooltip_permanent' ]) . ", sticky: " . esc_js($lmm_options[ 'marker_tooltip_sticky' ]) . ", interactive: " . esc_js($lmm_options[ 'marker_tooltip_interactive' ]) . ", opacity: " . str_replace(',', '.', floatval($lmm_options[ 'marker_tooltip_opacity' ])) . "});".PHP_EOL;
			}
		}

		if ( ($mpopuptext == NULL) && ($lmm_options['directions_popuptext_panel'] == 'no') ) { $lmm_out .= 'marker.options.clickable = false;'.PHP_EOL; };
		$lmm_out .= $mapname.'.addLayer(marker);'.PHP_EOL;
		if($lmm_options['defaults_marker_popups_rise_on_hover'] == 'true'){
			$lmm_out .= 'marker.on("mouseover", function (e) {'.PHP_EOL;
			$lmm_out .= '  this.openPopup();'.PHP_EOL;
			$lmm_out .= '});'.PHP_EOL;
		}
		if ($lmm_options['directions_popuptext_panel'] == 'yes') {

			$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
			$mpopuptext = $mpopuptext . '<div style=\'' . $mpopuptext_css . '\'>' . strip_tags($address) . ' (';

			if ($lmm_options['directions_provider'] == 'googlemaps') {
				if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = esc_html($lmm_options['google_maps_base_domain']); } else { $gmaps_base_domain_directions = esc_html($lmm_options['google_maps_base_domain_custom']); }
				if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
				$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
				$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
				$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
				$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
				//info: Google language localization (directions)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
				} else {
					$google_language = '&hl=' . esc_html($lmm_options['google_maps_language_localization']);
				}
				$mpopuptext = $mpopuptext . "<a href='http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ] . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
			} else if ($lmm_options['directions_provider'] == 'yours') {
				$mpopuptext = $mpopuptext . "<a href='http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . intval($lmm_options[ 'directions_yours_route_type' ]) . "&layer=" . esc_html($lmm_options[ 'directions_yours_layer' ]) . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
			} else if ($lmm_options['directions_provider'] == 'ors') {
				$mpopuptext = $mpopuptext . "<a href='http://www.openrouteservice.org/?pos=" . $lon . "," . $lat . "&wp=" . $lon . "," . $lat . "&zoom=" . $zoom . "&routeWeigh=" . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . "&routeOpt=" . $lmm_options[ 'directions_ors_routeOpt' ] . "&layer=" . esc_html($lmm_options[ 'directions_ors_layer' ]) . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
			} else if ($lmm_options['directions_provider'] == 'bingmaps') {
				if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
				$mpopuptext = $mpopuptext . "<a href='https://www.bing.com/maps/default.aspx?v=2&amp;rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
			}
			$mpopuptext = $mpopuptext . ')</div>';
		}
		//info: needed for do_shortcode / direction link
		if ($lmm_options['directions_popuptext_panel'] == 'yes') {
			$lmm_out .= 'marker.bindPopup(document.getElementById("' . $mapname . '-popuptext-hidden").innerHTML+document.getElementById("' . $mapname . '-popuptext-dlink-hidden").innerHTML,{maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ', closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})'.$mopenpopup.';';
		} else {
			if ($popuptext != NULL) {
				$lmm_out .= 'marker.bindPopup(document.getElementById("' . $mapname . '-popuptext-hidden").innerHTML,{maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ', closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})'.$mopenpopup.';';
			}
		}
	}
	//info: reset control
	if($lmm_options['map_home_button'] != 'false'){
		$zoomhome_ondemand = ($lmm_options['map_home_button'] == 'true-ondemand')?'true':'false';
		$reenableclustering = 'false'; //no cluster on marker maps
		$lmm_out .= 'var reset_control_'.$mapname.' = L.Control.zoomHome({position: "'. $lmm_options['map_home_button_position'] .'", mapId: "'.$uid.'", mapnameJS: "'.$mapname_js.'", ondemand: '.$zoomhome_ondemand.', zoomHomeTitle:"'.esc_attr__('reset map view','lmm').'", reenableClustering:"' .$reenableclustering.'" });'.PHP_EOL;
		$lmm_out .= 'reset_control_'.$mapname.'.addTo('.$mapname.');'.PHP_EOL;
	}
	if($lmm_options['map_home_button'] == 'true-ondemand'){
		$lmm_out .= $mapname.'.on("moveend",function(e){'.PHP_EOL;
		$lmm_out .= '		jQuery("#leaflet-control-zoomhome-'.$uid.'").show();'.PHP_EOL;
		$lmm_out .= '});'.PHP_EOL;
	}
	//info: leaflet-hash.js (incompatible with jQuery mobile)
	if ($lmm_options['leaflet_hash_status'] == 'enabled') {
		$lmm_out .= 'var maphash_' . $uid . ' = new L.Hash(' . $mapname . ');'.PHP_EOL;
	}

	//info: center map on popup instead of marker
	if ($lmm_options['defaults_marker_popups_center_map'] == 'true') {
		$lmm_out .= $mapname.".on('popupopen', function(e) {".PHP_EOL;
		$lmm_out .= "	var px = ".$mapname.".project(e.popup._latlng);".PHP_EOL;
		$lmm_out .= "	px.y -= e.popup._container.clientHeight/2;".PHP_EOL;
		$lmm_out .= "	".$mapname.".panTo(".$mapname.".unproject(px),{animate: true});".PHP_EOL;
		$lmm_out .= "});".PHP_EOL;
	}

  //info: show alternative error on gelocation fail for Google Chrome
  if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) {
	  $lmm_out .= $mapname . '.on("locationerror",function(e){'.PHP_EOL;
	  $lmm_out .= '	alert("' . sprintf(esc_attr__('Geolocation failed: your current location can only be retrieved if the map is accessed securely using https - see %1$s for more details!','lmm'), 'https://www.mapsmarker.com/geolocation-https-only') . '");'.PHP_EOL;
	  $lmm_out .= '});'.PHP_EOL;
  }

  //info: show loading indicator if popup contains images and update popup after images have loaded to prevent broken popups
  $lmm_out .= PHP_EOL.$mapname.".on('popupopen', function(e) {".PHP_EOL;
  $lmm_out .= '  var popup_markerid = ' . intval($id) . ';'.PHP_EOL;
  $lmm_out .= "  var popup_images = jQuery('.leaflet-popup-content-wrapper #popup-content-'+popup_markerid+' img');".PHP_EOL;
  $lmm_out .= '  if (popup_images.length > 0) {'.PHP_EOL;
  $lmm_out .= '    var image_counter = 0;'.PHP_EOL;
  $lmm_out .= "    jQuery('#popup-content-'+popup_markerid).css('display', 'none');".PHP_EOL;
  $lmm_out .= "    jQuery('#popup-loading-'+popup_markerid).css('display', 'block');".PHP_EOL;
  $lmm_out .= '    jQuery(popup_images).each(function() {'.PHP_EOL;
  $lmm_out .= "      jQuery(this).on('load', function() {".PHP_EOL;
  $lmm_out .= '        image_counter++;'.PHP_EOL;
  $lmm_out .= '        if (image_counter == popup_images.length) {'.PHP_EOL;
  $lmm_out .= "          jQuery('#popup-loading-'+popup_markerid).css('display', 'none');".PHP_EOL;
  $lmm_out .= "          jQuery('#popup-content-'+popup_markerid).css('display', 'block');".PHP_EOL;
  $lmm_out .= '          e.popup.update();'.PHP_EOL;
  $lmm_out .= '        }'.PHP_EOL;
  $lmm_out .= '      });'.PHP_EOL;
  $lmm_out .= '    });'.PHP_EOL;
  $lmm_out .= '  }'.PHP_EOL;
  $lmm_out .= '});'.PHP_EOL;

  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>';
  $lmm_out .= '</body>';
  $lmm_out .= '</html>';
  echo $lmm_out;
  	} //info: end check if marker/layer exists
} //info: end get_query_var('marker', false)
} //info: end plugin active check
