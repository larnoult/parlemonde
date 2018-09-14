<?php
/*
     Fullscreen standalone maps - Leaflet Maps Marker Plugin
*/
//info: construct path to wp-load.php and get $wp_path
while(!is_file('wp-load.php')) {
	if(is_dir('..' . DIRECTORY_SEPARATOR)) chdir('..' . DIRECTORY_SEPARATOR);
	else die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
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
if (!lmm_is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php') ) {
	echo sprintf(__('The plugin "Leaflet Maps Marker" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), antispambot(get_bloginfo('admin_email')) );
} else {
global $wpdb, $allowedtags, $locale, $allowedposttags;
$additionaltags = array('iframe' => array('id' => true,'name' => true,'src' => true,'class' => true,'style' => true,'frameborder' => true,'scrolling' => true,'align' => true,'width' => true,'height' => true,'marginwidth' => true,'marginheight' => true),'style' => array('media' => true,'scoped' => true,'type' => true));
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$lmm_options = get_option( 'leafletmapsmarker_options' );
//info: set marker shadow url
if ( $lmm_options['defaults_marker_icon_shadow_url_status'] == 'default' ) {
	if ( $lmm_options['defaults_marker_icon_shadow_url'] == NULL ) {
		$marker_shadow_url = '';
	} else {
		$marker_shadow_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png';
	}
} else {
	$marker_shadow_url = htmlspecialchars($lmm_options['defaults_marker_icon_shadow_url']);
}
$plugin_version = get_option('leafletmapsmarker_version');
if (isset($_GET['layer'])) {
	$layer = intval($_GET['layer']);
	$uid = substr(md5(''.rand()), 0, 8);
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$row = $wpdb->get_row($wpdb->prepare('SELECT `id`,`name`,`basemap`,`mapwidth`,`mapheight`,`mapwidthunit`,`panel`,`layerzoom`,`layerviewlat`,`layerviewlon`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`multi_layer_map`,`multi_layer_map_list` FROM `'.$table_name_layers.'` WHERE `id` = %d',$layer), ARRAY_A);
	$id = $row['id'];
	$layername = $row['name'];
	$basemap = $row['basemap'];
	//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
	if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
	} else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
	}
	$lat = $row['layerviewlat'];
	$lon = $row['layerviewlon'];
	$zoom = $row['layerzoom'];
	$mapwidth = $row['mapwidth'];
	$mapheight = $row['mapheight'];
	$mapwidthunit = $row['mapwidthunit'];
	$panel = $row['panel'];
	$paneltext = ($row['name'] == NULL) ? '&nbsp;' : htmlspecialchars(stripslashes($row['name']));
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
	$multi_layer_map = $row['multi_layer_map'];
	$multi_layer_map_list = $row['multi_layer_map_list'];
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
	$lmm_out .= '<title>' . $title_layername . ' - ' . get_bloginfo('name') . '</title>'.PHP_EOL;
	$lmm_out .= '<meta charset="UTF-8" />'.PHP_EOL;
	$lmm_out .= '<meta name="geo.position" content="' . $lat . ';' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="ICBM" content="' . $lat . ', ' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="page-type" content="' . __('map','lmm') . '" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-57x57.png">'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon-precomposed" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-57x57.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" sizes="114x114" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-retina-114x114.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" sizes="72x72" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-72x72.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" sizes="144x144" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-retina-144x144.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-1024x748.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-retina-2048x1496.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-768x1004.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-retina-1536x2008.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/iso-launch-image-iphone-320x460.png" media="screen and (max-device-width: 320px)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x920.png" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x1096.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" />'.PHP_EOL;
	if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-rtl-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-rtl.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	} else {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	}
	$lmm_out .= '<style type="text/css" id="leafletmapsmarker-image-css-override">.leaflet-popup-content img { ' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . ' }</style>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>'.PHP_EOL;
	//info: Google API key
	if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		$lmm_out .= '<script type="text/javascript" src="https://www.google.com/jsapi' .$google_maps_api_key . '"></script>'.PHP_EOL;
	}
	//info: Google language localization (JSON API)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
	} else {
		$google_language = "&language=" . $lmm_options['google_maps_language_localization'];
	}
	if ($lmm_options['google_maps_base_domain_custom'] == 'maps.google.com') {
		$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain'];
	} else {
		$gmaps_base_domain = "&base_domain=" . htmlspecialchars($lmm_options['google_maps_base_domain_custom']);
	}
  	//info: Bing culture code
	if ($lmm_options['bingmaps_culture'] == 'automatic') {
		if ( $locale != NULL ) { $bing_culture = str_replace("_","-", $locale); } else { $bing_culture =  'en_us'; }
	} else {
		$bing_culture = $lmm_options['bingmaps_culture'];
	}
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= 'var mapsmarkerjs = {"zoom_in":"' . __('Zoom in','lmm') . '","zoom_out":"' . __('Zoom out','lmm') . '","google_maps_api_status":"' . $lmm_options['google_maps_api_status'] . '","googlemaps_language":"' . $google_language . '","googlemaps_base_domain":"' . $gmaps_base_domain . '","google_maps_api_key":"' . esc_js(trim($lmm_options['google_maps_api_key'])) . '","bing_culture":"' . $bing_culture . '"};'.PHP_EOL;
	$lmm_out .= '/* ]]> */'.PHP_EOL;
	$lmm_out .= '</script>'.PHP_EOL;
	$lmm_out .= '<style>form { margin: 0 ; } </style>'.PHP_EOL; //info: for layer controlbox
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js?ver=' . $plugin_version . '"></script>'.PHP_EOL;
	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= '<script type="text/javascript" src="https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])) . '"></script>'.PHP_EOL;	
	}
	$lmm_out .= '</head>'.PHP_EOL;
	$lmm_out .= '<body style="margin:0;padding:0;height:100%;background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . ';overflow:hidden;">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		if ( function_exists( 'is_rtl' ) && is_rtl() ) { $panel_fullscreen_text = 'text-align:right;'; } else { $panel_fullscreen_text = 'text-align:left;'; }
		$lmm_out .= '<div id="panel_top_' . $uid . '" class="lmm-panel" style="' . $panel_fullscreen_text  . 'background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . '; width:99%; padding:5px;">'.PHP_EOL;
		$lmm_out .= '<span style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ])) . '">' . $paneltext . '</span><span class="lmm-panel-api-fullscreen">';
		if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '&amp;name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?layer=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
			if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; }
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $geojson_api_link . '&amp;callback=jsonp&amp;full=yes&amp;full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</span></div>'.PHP_EOL;
	}

	//info: if panel enabled, only 94% height as otherwise attribution wont be visible
	if ($panel == 1) {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:94%; height:auto !important; min-height: 94%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></div>'. PHP_EOL;
	} else {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:100%; height:auto !important; min-height: 100%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></div>'. PHP_EOL;
	}
	//info: add geo microformats
	$layermarklist = $wpdb->get_results($wpdb->prepare('SELECT l.id as lid,l.name as lname, m.lon as mlon, m.lat as mlat, m.markername as markername,m.id as markerid FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id = %d LIMIT 1000',$layer), ARRAY_A);
	if (count($layermarklist) < 1) {
		$lmm_out .= '<div class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
	} else {
		foreach ($layermarklist as $row){
			$lmm_out .= '<div class="lmm-geo-tags geo">' . htmlspecialchars($row['markername']) . ': <span class="latitude">' . $row['mlat'] . '</span>, <span class="longitude">' . $row['mlon'] . '</span></div>'.PHP_EOL;
		}
	}
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= '/* Maps created with Leaflet Maps Marker - #1 mapping plugin for WordPress (www.mapsmarker.com) */'.PHP_EOL;
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var mapsmarker_'.$uid.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
	$attrib_prefix = '<a href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker for WordPress - helping you to share your favorite spots and tracks','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Leaflet Maps Marker') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Leaflet Maps Marker') . '\">icons</a>)';
	$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
	$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	
	$lmm_out .= '(function($) {'.PHP_EOL;
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { dragging: ' . $lmm_options['misc_map_dragging'] . ', touchZoom: ' . $lmm_options['misc_map_touchzoom'] . ', scrollWheelZoom: ' . $lmm_options['misc_map_scrollwheelzoom'] . ', doubleClickZoom: ' . $lmm_options['misc_map_doubleclickzoom'] . ', boxzoom: ' . $lmm_options['map_interaction_options_boxzoom'] . ', trackResize: ' . $lmm_options['misc_map_trackresize'] . ', worldCopyJump: ' . $lmm_options['map_interaction_options_worldcopyjump'] . ', closePopupOnClick: ' . $lmm_options['misc_map_closepopuponclick'] . ', keyboard: ' . $lmm_options['map_keyboard_navigation_options_keyboard'] . ', keyboardPanOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) . ', keyboardZoomOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) . ', inertia: ' . $lmm_options['map_panning_inertia_options_inertia'] . ', inertiaDeceleration: ' . intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) . ', inertiaMaxSpeed: ' . intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) . ', zoomControl: ' . $lmm_options['misc_map_zoomcontrol'] . ', crs: ' . $lmm_options['misc_projections'] . ' });'.PHP_EOL;
	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;

	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified") - not sure if needed in leaflet-fullscreen.php too
	$maxzoom = 21; //info: customizable in MMP only
	$lmm_out .= $mapname.'._layersMaxZoom = ' . $maxzoom . ';'.PHP_EOL;

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
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $osm_attribution . '", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  $lmm_options[ 'stamen_terrain_flavor' ] . '/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  $lmm_options[ 'stamen_toner_flavor' ] . '/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	
	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= 'if (typeof MQ !== "undefined") {';
		$lmm_out .= 'mapquest_osm = new MQ.mapLayer();';
		$lmm_out .= 'mapquest_aerial = new MQ.satelliteLayer();';
		$lmm_out .= 'mapquest_hybrid = new MQ.hybridLayer();';
		$lmm_out .= '} else { if (window.console) { console.log("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); } }';
	}
	
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		$lmm_out .= 'var googleLayer_roadmap = new L.Google("ROADMAP", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var googleLayer_satellite = new L.Google("SATELLITE", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var googleLayer_hybrid = new L.Google("HYBRID", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var googleLayer_terrain = new L.Google("TERRAIN", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
		$lmm_out .= 'var bingaerial = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var bingaerialwithlabels = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var bingroad = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	};
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: MapBox basemaps
	$lmm_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
    $lmm_out .= 'var custom_basemap = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap_tms' ] . ', ' . $error_tile_url_custom_basemap . 'attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
 	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap2_tms' ] . ', ' . $error_tile_url_custom_basemap2 . 'attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap2_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap3_tms' ] . ', ' . $error_tile_url_custom_basemap3 . 'attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap3_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var empty_basemap = new L.TileLayer("");'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';

	//info: define overlays
    $lmm_out .= 'var overlays_custom = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom_tms' ] . ', ' . $error_tile_url_overlays_custom . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom2_tms' ] . ', ' . $error_tile_url_overlays_custom2 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom2_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom3_tms' ] . ', ' . $error_tile_url_overlays_custom3 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom3_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom4_tms' ] . ', ' . $error_tile_url_overlays_custom4 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom4_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;

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
	$lmm_out .= 'var wms = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) . '", {wmsid: "wms", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ])) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ])) . '"' . $wms_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms2 == 1) {
	$lmm_out .= 'var wms2 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) . '", {wmsid: "wms2", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ])) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ])) . '"' . $wms2_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms3 == 1) {
	$lmm_out .= 'var wms3 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) . '", {wmsid: "wms3", layers: "' . htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ]))) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ])) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ])) . '"' . $wms3_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms4 == 1) {
	$lmm_out .= 'var wms4 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) . '", {wmsid: "wms4", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ])) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ])) . '"' . $wms4_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms5 == 1) {
	$lmm_out .= 'var wms5 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) . '", {wmsid: "wms5", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ])) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ])) . '"' . $wms5_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms6 == 1) {
	$lmm_out .= 'var wms6 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) . '", {wmsid: "wms6", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ])) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ])) . '"' . $wms6_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms7 == 1) {
	$lmm_out .= 'var wms7 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) . '", {wmsid: "wms7", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ])) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ])) . '"' . $wms7_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms8 == 1) {
	$lmm_out .= 'var wms8 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) . '", {wmsid: "wms8", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ])) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ])) . '"' . $wms8_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms9 == 1) {
	$lmm_out .= 'var wms9 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) . '", {wmsid: "wms9", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ])) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ])) . '"' . $wms9_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms10 == 1) {
	$lmm_out .= 'var wms10 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) . '", {wmsid: "wms10", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ])) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ])) . '"' . $wms10_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
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
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
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
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ')';
	//info: controlbox - check active overlays on marker/layer level
	//2do - remove isset-check - not necessary anymore, as sql result check is now global
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= ".addLayer(overlays_custom)";
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= ".addLayer(overlays_custom2)";
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= ".addLayer(overlays_custom3)";
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= ".addLayer(overlays_custom4)";
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= ".addLayer(wms)";
	if ( $wms2 == 1 )
		$lmm_out .= ".addLayer(wms2)";
	if ( $wms3 == 1 )
		$lmm_out .= ".addLayer(wms3)";
	if ( $wms4 == 1 )
		$lmm_out .= ".addLayer(wms4)";
	if ( $wms5 == 1 )
		$lmm_out .= ".addLayer(wms5)";
	if ( $wms6 == 1 )
		$lmm_out .= ".addLayer(wms6)";
	if ( $wms7 == 1 )
		$lmm_out .= ".addLayer(wms7)";
	if ( $wms8 == 1 )
		$lmm_out .= ".addLayer(wms8)";
	if ( $wms9 == 1 )
		$lmm_out .= ".addLayer(wms9)";
	if ( $wms10 == 1 )
		$lmm_out .= ".addLayer(wms10)";
	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? ".addControl(layersControl);" : ";".PHP_EOL;
	//info: add scale control
	if ( $lmm_options['map_scale_control'] == 'enabled' ) {
	$lmm_out .= "L.control.scale({position:'" . $lmm_options['map_scale_control_position'] . "', maxWidth: " . intval($lmm_options['map_scale_control_maxwidth']) . ", metric: " . $lmm_options['map_scale_control_metric'] . ", imperial: " . $lmm_options['map_scale_control_imperial'] . ", updateWhenIdle: " . $lmm_options['map_scale_control_updatewhenidle'] . "}).addTo(" . $mapname . ");".PHP_EOL;
	}

	//info: js for layer only
	if (!empty($layer) ) {
		$lmm_out .= 'var geojsonObj, mapIcon, marker_clickable, marker_title;'.PHP_EOL;
		//info: load GeoJSON for layer maps
		if (!empty($layer) && ($multi_layer_map == 0) ) {
			$lmm_out .= 'var xhReq = new XMLHttpRequest();'.PHP_EOL;
			$lmm_out .= 'xhReq.open("GET", "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '", true);'.PHP_EOL; //info: for caching add &timestamp=' . time() . '
				$lmm_out .= 'xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) {'.PHP_EOL; //info: async 1a/2
		} else if (!empty($layer) && ($multi_layer_map == 1) ) {
			$lmm_out .= 'var xhReq = new XMLHttpRequest();'.PHP_EOL;
			$lmm_out .= 'xhReq.open("GET", "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $multi_layer_map_list . '", true);'.PHP_EOL; //info: for caching add &timestamp=' . time() . '
				$lmm_out .= 'xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) {'.PHP_EOL; //info: async 1b/2
		}

		//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
		$lmm_out .= 'if (xhReq.responseText.indexOf(\'{"type"\') != 0) {
	var position = xhReq.responseText.indexOf(\'{"type"\');
	try { geojsonObj = JSON.parse(xhReq.responseText.slice(position)); } catch (e) { console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message); }'.PHP_EOL;
		$lmm_out .= '} else {
	try { geojsonObj = JSON.parse(xhReq.responseText); } catch (e) { console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message); }'.PHP_EOL;
		$lmm_out .= '}'.PHP_EOL;

		$lmm_out .= 'L.geoJson(geojsonObj, {'.PHP_EOL;
		$lmm_out .= '		onEachFeature: function(feature, marker) {'.PHP_EOL;
		$lmm_out .= "			if (feature.properties.text != '') {".PHP_EOL;
		$lmm_out .= '			marker.bindPopup(feature.properties.text, {'.PHP_EOL;
		$lmm_out .= '			maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', '.PHP_EOL;
		$lmm_out .= '			minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', '.PHP_EOL;
		$lmm_out .= '			maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', '.PHP_EOL;
		$lmm_out .= '			autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', '.PHP_EOL;
		$lmm_out .= '			closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', '.PHP_EOL;
		$lmm_out .= '			autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
		$lmm_out .= '			});'.PHP_EOL;
		$lmm_out .= '			}'.PHP_EOL;
		$lmm_out .= '		},'.PHP_EOL;
		$lmm_out .= 'pointToLayer: function (feature, latlng) {'.PHP_EOL;
		$lmm_out .= '	mapIcon = L.icon({ '.PHP_EOL;
		$lmm_out .= "		iconUrl: (feature.properties.icon != '') ? '" . LEAFLET_PLUGIN_ICONS_URL . "/' + feature.properties.icon : '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png" . "',".PHP_EOL;
		$lmm_out .= '		iconSize: [' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		iconAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		popupAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= "		shadowUrl: '" . $marker_shadow_url . "',".PHP_EOL;
		$lmm_out .= '		shadowSize: [' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		shadowAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= "		className: (feature.properties.icon == '') ? 'lmm_marker_icon_default' : 'lmm_marker_icon_'+ feature.properties.icon.slice(0,-4)".PHP_EOL;
		$lmm_out .= '	});'.PHP_EOL;
		$lmm_out .= "if (feature.properties.text == '') { marker_clickable = false } else { marker_clickable = true };".PHP_EOL;
		if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') {
		$lmm_out .= "if (feature.properties.markername == '') { marker_title = '' } else { marker_title = feature.properties.markername };".PHP_EOL;
		}
		$lmm_out .= 'return L.marker(latlng, {icon: mapIcon, clickable: marker_clickable, title: marker_title, opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
		$lmm_out .= '}'.PHP_EOL;
		$lmm_out .= '}).addTo(' . $mapname . ');'.PHP_EOL;
		$lmm_out .= '} else { if (window.console) { console.error(xhReq.statusText); } } } }; xhReq.onerror = function (e) { if (window.console) { console.error(xhReq.statusText); } }; xhReq.send(null);'.PHP_EOL; //info: async 2/2
    }
  $lmm_out .= '})(jQuery);'.PHP_EOL;
  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>';
  $lmm_out .= '</body>';
  $lmm_out .= '</html>';
  echo $lmm_out;
  	} //info: end check if marker/layer exists
} //info: end isset($_GET['layer'])
elseif (isset($_GET['marker'])) {
	$markerid = intval($_GET['marker']);
	$uid = substr(md5(''.rand()), 0, 8);
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
		$row = $wpdb->get_row($wpdb->prepare('SELECT `id`,`markername`,`basemap`,`layer`,`lat`,`lon`,`icon`,`popuptext`,`zoom`,`openpopup`,`mapwidth`,`mapwidthunit`,`mapheight`,`panel`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`address` FROM `'.$table_name_markers.'` WHERE `id` = %d',$markerid), ARRAY_A);
		if(!empty($row)) {
			$id = $row['id'];
			$markername = esc_js($row['markername']);
			$basemap = $row['basemap'];
			//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
			if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
				$basemap = 'osm_mapnik';
			} else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
				$basemap = 'osm_mapnik';
			}
			$lon = $row['lon'];
			$lat = $row['lat'];
			$coords = $lat.', '.$lon;
			$icon = $row['icon'];
			//info: strip evil scripts
			if ($lmm_options['wp_kses_status'] == 'enabled') {
				$popuptext = wp_kses($row['popuptext'], array_merge($allowedposttags, $additionaltags));
			} else {
				$popuptext = $row['popuptext'];
			}
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
			$paneltext = ($row['markername'] == NULL) ? '&nbsp;' : htmlspecialchars(stripslashes($row['markername']));
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
			$address = $row['address'];
			$mapname = 'mapsmarker_'.$uid;
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
	$lmm_out .= '<title>' . $title_markername . ' - ' . get_bloginfo('name') . '</title>'.PHP_EOL;
	$lmm_out .= '<meta charset="UTF-8" />'.PHP_EOL;
	$lmm_out .= '<meta name="geo.position" content="' . $lat . ';' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="ICBM" content="' . $lat . ', ' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="page-type" content="' . __('map','lmm') . '" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-57x57.png">'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon-precomposed" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-57x57.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" sizes="114x114" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-iphone-retina-114x114.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" sizes="72x72" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-72x72.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-icon" sizes="144x144" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-app-icon-ipad-retina-144x144.png" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-1024x748.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-landscape-retina-2048x1496.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-768x1004.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-ipad-portrait-retina-1536x2008.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/iso-launch-image-iphone-320x460.png" media="screen and (max-device-width: 320px)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x920.png" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />'.PHP_EOL;
	$lmm_out .= '<link rel="apple-touch-startup-image" href="' . LEAFLET_PLUGIN_URL . 'inc/img/ios-launch-image-iphone-retina-640x1096.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" />'.PHP_EOL;
	if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-rtl-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet-rtl.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	} else {
		$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css?ver=' . $plugin_version . '" type="text/css" media="all">'.PHP_EOL;
	}

	$lmm_out .= '<style type="text/css" id="leafletmapsmarker-image-css-override">.leaflet-popup-content img { ' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . ' }</style>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>'.PHP_EOL;
	//info: Google API key
	if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		$lmm_out .= '<script type="text/javascript" src="https://www.google.com/jsapi' . $google_maps_api_key . '"></script>'.PHP_EOL;
	}
	//info: Google language localization (JSON API)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
	} else {
		$google_language = "&language=" . $lmm_options['google_maps_language_localization'];
	}
	if ($lmm_options['google_maps_base_domain_custom'] == 'maps.google.com') {
		$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain'];
	} else {
		$gmaps_base_domain = "&base_domain=" . htmlspecialchars($lmm_options['google_maps_base_domain_custom']);
	}
	//info: Bing culture code
	if ($lmm_options['bingmaps_culture'] == 'automatic') {
		if ( $locale != NULL ) { $bing_culture = str_replace("_","-", $locale); } else { $bing_culture =  'en_us'; }
	} else {
		$bing_culture = $lmm_options['bingmaps_culture'];
	}
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= 'var mapsmarkerjs = {"zoom_in":"' . __('Zoom in','lmm') . '","zoom_out":"' . __('Zoom out','lmm') . '","google_maps_api_status":"' . $lmm_options['google_maps_api_status'] . '","googlemaps_language":"' . $google_language . '","googlemaps_base_domain":"' . $gmaps_base_domain . '","google_maps_api_key":"' . esc_js(trim($lmm_options['google_maps_api_key'])) . '","bing_culture":"' . $bing_culture . '"};'.PHP_EOL;
	$lmm_out .= '/* ]]> */'.PHP_EOL;
	$lmm_out .= '</script>'.PHP_EOL;
	$lmm_out .= '<style>form { margin: 0 ; } </style>'.PHP_EOL; //info: for layer controlbox
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js?ver=' . $plugin_version . '"></script>'.PHP_EOL;
	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= '<script type="text/javascript" src="https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=' . esc_js(trim($lmm_options['mapquest_api_key'])) . '"></script>'.PHP_EOL;	
	}
	$lmm_out .= '</head>'.PHP_EOL;
	$lmm_out .= '<body style="margin:0;padding:0;height:100%;background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . ';overflow:hidden;">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		if ( function_exists( 'is_rtl' ) && is_rtl() ) { $panel_fullscreen_text = 'text-align:right;'; } else { $panel_fullscreen_text = 'text-align:left;'; }
		$lmm_out .= '<div id="panel_top_' . $uid . '" class="lmm-panel" style="' . $panel_fullscreen_text . 'background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . '; width:99%; padding:5px;">'.PHP_EOL;
		$lmm_out .= '<span style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ])) . '">' . $paneltext . '</span><span class="lmm-panel-api-fullscreen">';
		if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
				//info: Google language localization (directions)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
				} else {
					$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
				}
				//info: build directions provider links
				if ($lmm_options['directions_provider'] == 'googlemaps') {
					if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
					if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
					if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
					$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
					$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
					$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
					$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
					$lmm_out .= '<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . $directions_transport_type_icon . '" /></a>';
				} else if ($lmm_options['directions_provider'] == 'yours') {
					if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
					$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . $directions_transport_type_icon . '" /></a>';
				} else if ($lmm_options['directions_provider'] == 'ors') {
					if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
					$lmm_out .= '<a href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&wp=' . $lon . ',' . $lat . '&zoom=' . $zoom . '&routeWeigh=' . $lmm_options[ 'directions_ors_routeWeigh' ] . '&routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . $directions_transport_type_icon . '" /></a>';
				} else if ($lmm_options['directions_provider'] == 'bingmaps') {
					if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
					$lmm_out .= '<a href="https://www.bing.com/maps/default.aspx?v=2&amp;rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="icon-car" /></a>';
				}
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '&amp;name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&amp;callback=jsonp&amp;full=yes&amp;full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</span></div>'.PHP_EOL;
	}

	//info: if panel enabled, only 94% height as otherwise attribution wont be visible
	if ($panel == 1) {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:94%; height:auto !important; min-height: 94%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></div>'. PHP_EOL;
	} else {
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="width:100%; height:100%; height:auto !important; min-height: 100%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"><noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></div>'. PHP_EOL;
	}
	//info: add geo microformats
	$lmm_out .= '<div class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= '/* Maps created with Leaflet Maps Marker - #1 mapping plugin for WordPress (www.mapsmarker.com) */'.PHP_EOL;
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var mapsmarker_'.$uid.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
	$attrib_prefix = '<a href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker for WordPress - helping you to share your favorite spots and tracks','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Leaflet Maps Marker') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Leaflet Maps Marker') . '\">icons</a>)';
	$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
	$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
    $attrib_osm_mapnik = __("Map",'lmm').': &copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	$attrib_basemapat = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\" style=\"\">data.wien.gv.at</a>)';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	
	$lmm_out .= '(function($) {'.PHP_EOL;
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { dragging: ' . $lmm_options['misc_map_dragging'] . ', touchZoom: ' . $lmm_options['misc_map_touchzoom'] . ', scrollWheelZoom: ' . $lmm_options['misc_map_scrollwheelzoom'] . ', doubleClickZoom: ' . $lmm_options['misc_map_doubleclickzoom'] . ', boxzoom: ' . $lmm_options['map_interaction_options_boxzoom'] . ', trackResize: ' . $lmm_options['misc_map_trackresize'] . ', worldCopyJump: ' . $lmm_options['map_interaction_options_worldcopyjump'] . ', closePopupOnClick: ' . $lmm_options['misc_map_closepopuponclick'] . ', keyboard: ' . $lmm_options['map_keyboard_navigation_options_keyboard'] . ', keyboardPanOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) . ', keyboardZoomOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) . ', inertia: ' . $lmm_options['map_panning_inertia_options_inertia'] . ', inertiaDeceleration: ' . intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) . ', inertiaMaxSpeed: ' . intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) . ', zoomControl: ' . $lmm_options['misc_map_zoomcontrol'] . ', crs: ' . $lmm_options['misc_projections'] . ' });'.PHP_EOL;
	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;

	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified") - not sure if needed in leaflet-fullscreen.php too
	$maxzoom = 21; //info: customizable in MMP only
	$lmm_out .= $mapname.'._layersMaxZoom = ' . $maxzoom . ';'.PHP_EOL;

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
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $osm_attribution . '", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  $lmm_options[ 'stamen_terrain_flavor' ] . '/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  $lmm_options[ 'stamen_toner_flavor' ] . '/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $lmm_options['map_retina_detection'] . '});'.PHP_EOL;

	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmm_out .= 'if (typeof MQ !== "undefined") {';
		$lmm_out .= 'mapquest_osm = new MQ.mapLayer();';
		$lmm_out .= 'mapquest_aerial = new MQ.satelliteLayer();';
		$lmm_out .= 'mapquest_hybrid = new MQ.hybridLayer();';
		$lmm_out .= '} else { if (window.console) { console.log("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); } }';
	}

	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		$lmm_out .= 'var googleLayer_roadmap = new L.Google("ROADMAP", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var googleLayer_satellite = new L.Google("SATELLITE", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var googleLayer_hybrid = new L.Google("HYBRID", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var googleLayer_terrain = new L.Google("TERRAIN", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
		$lmm_out .= 'var bingaerial = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var bingaerialwithlabels = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var bingroad = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	};
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: MapBox basemaps
	$lmm_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
    $lmm_out .= 'var custom_basemap = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap_tms' ] . ', ' . $error_tile_url_custom_basemap . 'attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
 	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap2_tms' ] . ', ' . $error_tile_url_custom_basemap2 . 'attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap2_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap3_tms' ] . ', ' . $error_tile_url_custom_basemap3 . 'attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap3_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var empty_basemap = new L.TileLayer("");'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';

	//info: define overlays
    $lmm_out .= 'var overlays_custom = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom_tms' ] . ', ' . $error_tile_url_overlays_custom . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom2_tms' ] . ', ' . $error_tile_url_overlays_custom2 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom2_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom3_tms' ] . ', ' . $error_tile_url_overlays_custom3 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom3_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
    $lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom4_tms' ] . ', ' . $error_tile_url_overlays_custom4 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom4_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;

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
	$lmm_out .= 'var wms = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) . '", {wmsid: "wms", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ])) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ])) . '"' . $wms_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms2 == 1) {
	$lmm_out .= 'var wms2 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) . '", {wmsid: "wms2", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ])) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ])) . '"' . $wms2_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms3 == 1) {
	$lmm_out .= 'var wms3 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) . '", {wmsid: "wms3", layers: "' . htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ]))) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ])) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ])) . '"' . $wms3_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms4 == 1) {
	$lmm_out .= 'var wms4 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) . '", {wmsid: "wms4", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ])) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ])) . '"' . $wms4_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms5 == 1) {
	$lmm_out .= 'var wms5 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) . '", {wmsid: "wms5", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ])) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ])) . '"' . $wms5_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms6 == 1) {
	$lmm_out .= 'var wms6 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) . '", {wmsid: "wms6", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ])) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ])) . '"' . $wms6_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms7 == 1) {
	$lmm_out .= 'var wms7 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) . '", {wmsid: "wms7", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ])) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ])) . '"' . $wms7_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms8 == 1) {
	$lmm_out .= 'var wms8 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) . '", {wmsid: "wms8", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ])) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ])) . '"' . $wms8_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms9 == 1) {
	$lmm_out .= 'var wms9 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) . '", {wmsid: "wms9", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ])) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ])) . '"' . $wms9_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms10 == 1) {
	$lmm_out .= 'var wms10 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) . '", {wmsid: "wms10", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ])) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ])) . '"' . $wms10_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
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
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
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
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ')';
	//info: controlbox - check active overlays on marker/layer level
	//2do - remove isset-check - not necessary anymore, as sql result check is now global
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= ".addLayer(overlays_custom)";
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= ".addLayer(overlays_custom2)";
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= ".addLayer(overlays_custom3)";
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= ".addLayer(overlays_custom4)";
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= ".addLayer(wms)";
	if ( $wms2 == 1 )
		$lmm_out .= ".addLayer(wms2)";
	if ( $wms3 == 1 )
		$lmm_out .= ".addLayer(wms3)";
	if ( $wms4 == 1 )
		$lmm_out .= ".addLayer(wms4)";
	if ( $wms5 == 1 )
		$lmm_out .= ".addLayer(wms5)";
	if ( $wms6 == 1 )
		$lmm_out .= ".addLayer(wms6)";
	if ( $wms7 == 1 )
		$lmm_out .= ".addLayer(wms7)";
	if ( $wms8 == 1 )
		$lmm_out .= ".addLayer(wms8)";
	if ( $wms9 == 1 )
		$lmm_out .= ".addLayer(wms9)";
	if ( $wms10 == 1 )
		$lmm_out .= ".addLayer(wms10)";
	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? ".addControl(layersControl);" : ";".PHP_EOL;
	//info: add scale control
	if ( $lmm_options['map_scale_control'] == 'enabled' ) {
	$lmm_out .= "L.control.scale({position:'" . $lmm_options['map_scale_control_position'] . "', maxWidth: " . intval($lmm_options['map_scale_control_maxwidth']) . ", metric: " . $lmm_options['map_scale_control_metric'] . ", imperial: " . $lmm_options['map_scale_control_imperial'] . ", updateWhenIdle: " . $lmm_options['map_scale_control_updatewhenidle'] . "}).addTo(" . $mapname . ");".PHP_EOL;
	}
	//info: js for marker only
	if (!(empty($mlat) or empty($mlon)) ) {
	if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') { $defaults_marker_icon_title = "title: '" . strip_tags(htmlspecialchars_decode($markername)) . "', "; } else { $defaults_marker_icon_title = ""; };
	$lmm_out .= 'var marker = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'),{ ' . $defaults_marker_icon_title . ' opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
 	if ($micon == NULL) {
  		$lmm_out .= "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
  	} else {
  		$lmm_out .= "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_ICONS_URL . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
	};
	if ( ($mpopuptext == NULL) && ($lmm_options['directions_popuptext_panel'] == 'no') ) { $lmm_out .= 'marker.options.clickable = false;'.PHP_EOL; };
	$lmm_out .= $mapname.'.addLayer(marker);'.PHP_EOL;

	if ($lmm_options['directions_popuptext_panel'] == 'yes') {

	 	$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
		$mpopuptext = $mpopuptext . '<div style=\'' . $mpopuptext_css . '\'>' . strip_tags($address) . ' (';

		if ($lmm_options['directions_provider'] == 'googlemaps') {
			if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
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
				$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
			}
			$mpopuptext = $mpopuptext . "<a href='http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ] . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		} else if ($lmm_options['directions_provider'] == 'yours') {
			$mpopuptext = $mpopuptext . "<a href='http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . $lmm_options[ 'directions_yours_route_type' ] . "&layer=" . $lmm_options[ 'directions_yours_layer' ] . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		} else if ($lmm_options['directions_provider'] == 'ors') {
			$mpopuptext = $mpopuptext . "<a href='http://www.openrouteservice.org/?pos=" . $lon . "," . $lat . "&wp=" . $lon . "," . $lat . "&zoom=" . $zoom . "&routeWeigh=" . $lmm_options[ 'directions_ors_routeWeigh' ] . "&routeOpt=" . $lmm_options[ 'directions_ors_routeOpt' ] . "&layer=" . $lmm_options[ 'directions_ors_layer' ] . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		} else if ($lmm_options['directions_provider'] == 'bingmaps') {
			if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
			$mpopuptext = $mpopuptext . "<a href='https://www.bing.com/maps/default.aspx?v=2&amp;rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to . "' target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		}
		$mpopuptext = $mpopuptext . ')</div>';
	}
	$sanitize_popuptext_from = array(
		'#<ul(.*?)>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
		'#</li>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
		'#</li>(\s)*(<br\s*/?>)*(\s)*</ul>#si',
		'#<ol(.*?)>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
		'#</li>(\s)*(<br\s*/?>)*(\s)*</ol>#si',
		'#(<br\s*/?>){1}\s*<ul(.*?)>#si',
		'#(<br\s*/?>){1}\s*<ol(.*?)>#si',
		'#</ul>\s*(<br\s*/?>){1}#si',
		'#</ol>\s*(<br\s*/?>){1}#si',
	);
	$sanitize_popuptext_to = array(
		'<ul$1><li$5>',
		'</li><li$4>',
		'</li></ul>',
		'<ol$1><li$5>',
		'</li></ol>',
		'<ul$2>',
		'<ol$2>',
		'</ul>',
		'</ol>'
	);
	$mpopuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $mpopuptext));
	if (!empty($mpopuptext)) $lmm_out .= 'marker.bindPopup("' . $mpopuptext_sanitized . '", {maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})'.$mopenpopup.';'.PHP_EOL;
  }
  $lmm_out .= '})(jQuery);'.PHP_EOL;
  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>';
  $lmm_out .= '</body>';
  $lmm_out .= '</html>';
  echo $lmm_out;
  	} //info: end check if marker/layer exists
} //info: end isset($_GET['marker'])
} //info: end plugin active check
?>