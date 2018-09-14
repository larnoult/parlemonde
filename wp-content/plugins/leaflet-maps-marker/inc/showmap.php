<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'showmap.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
	global $wpdb, $allowedtags, $locale, $allowedposttags;
	$additionaltags = array('iframe' => array('id' => true,'name' => true,'src' => true,'class' => true,'style' => true,'frameborder' => true,'scrolling' => true,'align' => true,'width' => true,'height' => true,'marginwidth' => true,'marginheight' => true),'style' => array('media' => true,'scoped' => true,'type' => true));
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	include_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php' ); //info: for is_plugin_active()
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
	$uid = substr(md5(''.rand()), 0, 8);
	extract(shortcode_atts(array(
		'lat' => '', 'lon' => '',
		'mlat' => '', 'mlon' => '',
		'basemap' => $lmm_options[ 'defaults_marker_shortcode_basemap'],
		'mpopuptext' => '',
		'popuptext' => '',
		'micon' => '',
		'zoom' => intval($lmm_options[ 'defaults_marker_shortcode_zoom' ]),
		'openpopup' => '',
		'layer' => '',
		'marker' => '',
		'markername' => '',
		'panel' => '0',
		'mapwidth' => intval($lmm_options[ 'defaults_marker_shortcode_mapwidth' ]),
		'mapwidthunit' => $lmm_options[ 'defaults_marker_shortcode_mapwidthunit' ],
		'mapheight' => intval($lmm_options[ 'defaults_marker_shortcode_mapheight' ]),
		'mapname' => 'lmm_map_'.$uid
	), $atts));

	//info: prepare layers
	if (!empty($layer)) {
		$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
		$row = $wpdb->get_row('SELECT `id`,`name`,`basemap`,`mapwidth`,`mapheight`,`mapwidthunit`,`panel`,`layerzoom`,`layerviewlat`,`layerviewlon`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`listmarkers`,`multi_layer_map`,`multi_layer_map_list` FROM `'.$table_name_layers.'` WHERE `id`='.intval($layer), ARRAY_A);
		$id = $row['id'];
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
		$paneltext = ($row['name'] == NULL) ? '&nbsp;' : htmlspecialchars($row['name']);
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
		$listmarkers = $row['listmarkers'];
		$multi_layer_map = $row['multi_layer_map'];
		$multi_layer_map_list = $row['multi_layer_map_list'];
		$multi_layer_map_list_exploded = explode(",", $multi_layer_map_list);
	}
	//info: prepare markers
	if (!empty($marker))  {
			$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
				$row = $wpdb->get_row('SELECT `id`,`markername`,`basemap`,`layer`,`lat`,`lon`,`icon`,`popuptext`,`zoom`,`openpopup`,`mapwidth`,`mapwidthunit`,`mapheight`,`panel`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`address` FROM `'.$table_name_markers.'` WHERE `id`='.intval($marker), ARRAY_A);
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
					//$layer = $row['layer']; //info: not needed in showmap.php, would overwrite if (!empty($layer))-check!
					$mlat = $lat;
					$mlon = $lon;
					$mpopuptext = $popuptext;
					$micon = $icon;
					$mapwidth = $row['mapwidth'];
					$mapwidthunit = $row['mapwidthunit'];
					$mapheight = $row['mapheight'];
					$panel = $row['panel'];
					$paneltext = ($row['markername'] == NULL) ? '&nbsp;' : htmlspecialchars($row['markername']);
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
					$address = htmlspecialchars($row['address']);
					$listmarkers = 0;
				}
	}
	//info: prepare markers only added by shortcode and not defined in backend
	if (empty($layer) and empty($marker)) {
		$lon = floatval($lon);
		$lat = floatval($lat);
		$icon = ($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : esc_html($lmm_options[ 'defaults_marker_icon' ]);
		$micon = $icon;
		$controlbox = $lmm_options[ 'defaults_marker_shortcode_controlbox' ];
		$overlays_custom = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom_active' ] == 1 ) ? '1' : '0';
		$overlays_custom2 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom2_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom2_active' ] == 1 ) ? '1' : '0';
		$overlays_custom3 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom3_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom3_active' ] == 1 ) ? '1' : '0';
		$overlays_custom4 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom4_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom4_active' ] == 1 ) ? '1' : '0';
		$wms = isset($lmm_options[ 'defaults_marker_shortcode_wms_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms_active' ] == 1 ) ? '1' : '0';
		$wms2 = isset($lmm_options[ 'defaults_marker_shortcode_wms2_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms2_active' ] == 1 ) ? '1' : '0';
		$wms3 = isset($lmm_options[ 'defaults_marker_shortcode_wms3_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms3_active' ] == 1 ) ? '1' : '0';
		$wms4 = isset($lmm_options[ 'defaults_marker_shortcode_wms4_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms4_active' ] == 1 ) ? '1' : '0';
		$wms5 = isset($lmm_options[ 'defaults_marker_shortcode_wms5_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms5_active' ] == 1 ) ? '1' : '0';
		$wms6 = isset($lmm_options[ 'defaults_marker_shortcode_wms6_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms6_active' ] == 1 ) ? '1' : '0';
		$wms7 = isset($lmm_options[ 'defaults_marker_shortcode_wms7_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms7_active' ] == 1 ) ? '1' : '0';
		$wms8 = isset($lmm_options[ 'defaults_marker_shortcode_wms8_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms8_active' ] == 1 ) ? '1' : '0';
		$wms9 = isset($lmm_options[ 'defaults_marker_shortcode_wms9_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms9_active' ] == 1 ) ? '1' : '0';
		$wms10 = isset($lmm_options[ 'defaults_marker_shortcode_wms10_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms10_active' ] == 1 ) ? '1' : '0';
		$mopenpopup = '';
		$mpopuptext = $popuptext;
		$listmarkers = 0;
		$address = '';
		//info: legacy
		if (!empty($mlat)) { $lat = $mlat; } else { $mlat = $lat; }
		if (!empty($mlon)) { $lon = $mlon; } else { $mlon = $lon; }
	}

	//info: show static image with link in feeds
	if (is_feed()) {
		if ($lat != NULL) { //info: marker exists?
			if (empty($layer)) {
			$lmm_out = '<p>' . $paneltext . '<br/><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-rss-feed.png"/><br/>' . __('Show embedded map in full-screen mode','lmm') . '</a></p>';
			}
			if (empty($marker)) {
			$lmm_out = '<p>' . $paneltext . '<br/><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-rss-feed.png"/><br/>' . __('Show embedded map in full-screen mode','lmm') . '</a></p>';
			}
			//return $lmm_out;
		}
	} else {

	//info: check if layer/marker ID exists
	if ($lat == NULL) {
	$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), $layer);
	$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $marker);
	$lmm_out = '<div id="lmm_error" style="margin:10px 0;">'.PHP_EOL;
		if (empty($layer)) {
			$lmm_out .= $error_marker_not_exists . '<br/>';
		}
		if (empty($marker)) {
			$lmm_out .= $error_layer_not_exists . '<br/>';
		}
	$lmm_out .= '<a href="https://www.mapsmarker.com" target="_blank" title="' . esc_attr__('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-deleted-image.png" width="244" height="224" /></a></div>';
	} else {
	//info: starting output on frontend
	$lmm_out = '';
	if (!empty($layer)) { 
		$css_classes = 'layermap layer-'. intval($layer); 
	} else if (!empty($marker))  {
		$css_classes = 'markermap marker-'. intval($marker); 
	} else {
		$css_classes = '';
	}
	$lmm_out .= '<div id="lmm_'.$uid.'" style="width:' . $mapwidth.$mapwidthunit . ';" class="mapsmarker ' . $css_classes . '">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		if (!empty($marker)) { 
			$panel_background = htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])); 
		} else if (!empty($layer)) { 
			$panel_background = htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])); 
		} else {
			$panel_background = '';
		}
		$lmm_out .= '<div id="lmm_panel_'.$uid.'" class="lmm-panel" style="background:' . $panel_background . ';">'.PHP_EOL;
		if (!empty($marker))
		{
			$lmm_out .= '<div id="lmm_panel_api_'.$uid.'" class="lmm-panel-api">';
			if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
					if ($lmm_options['directions_provider'] == 'googlemaps') {
						if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
						if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
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
						$lmm_out .= '<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&amp;t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&amp;layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&amp;doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&amp;om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img alt="' . $directions_transport_type_icon . '" src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
					} else if ($lmm_options['directions_provider'] == 'yours') {
						if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
						$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $lat . '&amp;tlon=' . $lon . '&amp;v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&amp;fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&amp;layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img alt="' . $yours_transport_icon . '" src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
					} else if ($lmm_options['directions_provider'] == 'ors') {
						if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
						$lmm_out .= '<a href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&amp;wp=' . $lon . ',' . $lat . '&amp;zoom=' . $zoom . '&amp;routeWeigh=' . $lmm_options[ 'directions_ors_routeWeigh' ] . '&amp;routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&amp;layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
					} else if ($lmm_options['directions_provider'] == 'bingmaps') {
						if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
						$lmm_out .= '<a href="https://www.bing.com/maps/default.aspx?v=2&amp;rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to .'" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="icon-car" /></a>';
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
		$lmm_out .= '</div><div id="lmm_panel_text_'.$uid.'" class="lmm-panel-text" style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ])) . '">' . stripslashes($paneltext) . '</div>';
		}

		if (!empty($layer) && empty($marker)) //info: check if problems get reported - fix for marker name shown twice when layer+marker map on 1 page
		{
			$lmm_out .= '<div id="lmm_panel_api_'.$uid.'" class="lmm-panel-api">';
			if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '&amp;name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?layer=' . $id . '&amp;callback=jsonp&amp;full=yes&amp;full_icon_url=yes" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
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
		$lmm_out .= '</div><div id="lmm_panel_text_'.$uid.'" class="lmm-panel-text" style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ])) . '">' . stripslashes($paneltext) . '</div>'.PHP_EOL;
		}
	$lmm_out .= '</div>'.PHP_EOL; //info: <!--end lmm-panel-->
	}
	$admin_error_link = (current_user_can( 'manage_options' )) ? '<br/><small><a href="https://www.mapsmarker.com/wp_footer" target="_blank" style="text-decoration:none;">' . __('click here for troubleshooting','lmm') . '</a></small>' : '';
	$lmm_out .= '<div id="'.$mapname.'" class="lmm-map" style="background:#f6f6f6;border:1px solid #ccc;height:'.$mapheight.'px; overflow:hidden;padding:0;"><p style="font-size:80%;color:#9f9e9e;margin-left:5px;">' . __('loading map - please wait...','lmm') . $admin_error_link . '<noscript><br/><strong>' . __('Map could not be loaded - please enable Javascript!','lmm') . '</strong><br/><a style="text-decoration:none;" href="https://www.mapsmarker.com/js-disabled" target="_blank">&rarr; ' . __('more information','lmm') . '</a></noscript></p></div>'. PHP_EOL;
	//info: add geo microformats for layer maps
	if (!empty($layer) && empty($marker))
	{
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$layer_mark_list_microformats = $wpdb->get_results($wpdb->prepare('SELECT l.id as lid,l.name as lname, m.lon as mlon, m.lat as mlat, m.markername as markername,m.id as markerid FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id = %d LIMIT 1000',$layer), ARRAY_A);
		if (count($layer_mark_list_microformats) < 1) {
			$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
		} else {
			foreach ($layer_mark_list_microformats as $row){
				$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'_'.$row['markerid'].'" class="lmm-geo-tags geo">' . htmlspecialchars($row['markername']) . ': <span class="latitude">' . $row['mlat'] . '</span>, <span class="longitude">' . $row['mlon'] . '</span></div>'.PHP_EOL;
			}
		}
	}
	//info: add geo microformats for marker maps
	if (!empty($marker))
	{
	//info: add geo microformats
	$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">'.PHP_EOL;
	$lmm_out .= '<span class="paneltext">' . $paneltext . '</span>'.PHP_EOL;
	$lmm_out .= '<span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span>'.PHP_EOL;
	$lmm_out .= '<span class="popuptext">' . strip_tags($popuptext) .'</span>'.PHP_EOL;
	$lmm_out .= '</div>'.PHP_EOL;
	}
	//info: add geo microformats for marker maps added directly via shortcode
	if (empty($layer) && empty($marker))
	{
	//info: add geo microformats
	$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">'.PHP_EOL;
	$lmm_out .= '<span class="latitude">' . $mlat . '</span>, <span class="longitude">' . $mlon . '</span>'.PHP_EOL;
	$lmm_out .= '</div>'.PHP_EOL;
	}
	//info: display a list of markers under the map
	if ( !empty($layer) && empty($marker) && ($listmarkers == 1) )
	{
	//info: sqls for singe and multi-layer-maps
	if ($multi_layer_map == 0) {
		$layer_marker_list = $wpdb->get_results('SELECT l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$id.' ORDER BY ' . $lmm_options[ 'defaults_layer_listmarkers_order_by' ] . ' ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
	} else if ($multi_layer_map == 1) {

			//info: set sort order for multi-layer-maps based on list-marker-setting
			if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.id') {
				$sort_order_mlm = 'markerid';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.markername') {
				$sort_order_mlm = 'markername';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.popuptext') {
				$sort_order_mlm = 'mpopuptext';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.icon') {
				$sort_order_mlm = 'micon';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.createdby') {
				$sort_order_mlm = 'mcreatedby';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.createdon') {
				$sort_order_mlm = 'mcreatedon';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.updatedby') {
				$sort_order_mlm = 'mupdatedby';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.updatedon') {
				$sort_order_mlm = 'mupdatedon';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.layer') {
				$sort_order_mlm = 'mlayer';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.address') {
				$sort_order_mlm = 'maddress';
			} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'm.kml_timestamp') {
				$sort_order_mlm = 'mkml_timestamp';
			}

			if ( (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') && ($multi_layer_map_list != NULL) ) { //info: only 1 layer selected
				$mlm_query = "SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $multi_layer_map_list . "' ORDER BY " . $sort_order_mlm . " " . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . " LIMIT " . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]);
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
			} //info: end (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') && ($multi_layer_map_list != NULL)
			else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all') ) {
				$first_mlm_id = $multi_layer_map_list_exploded[0];
				$other_mlm_ids = array_slice($multi_layer_map_list_exploded,1);
				$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $row . "')";
				}
				$mlm_query .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['defaults_layer_listmarkers_limit']) . "";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
			} //info: end else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all')
			else if ($multi_layer_map_list == 'all') {
				$first_mlm_id = '0';
				$mlm_all_layers = $wpdb->get_results( "SELECT id FROM $table_name_layers", ARRAY_A );
				$other_mlm_ids = array_slice($mlm_all_layers,1);
				$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $row['id'] . "')";
				}
				$mlm_query .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['defaults_layer_listmarkers_limit']) . "";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
			} //info: end else if ($multi_layer_map_list == 'all')
			else { //info: if ($multi_layer_map == 1) but no layers selected
				$layer_marker_list = array();
			}
	} //info: end main - else if ($multi_layer_map == 1)

	//info: set list markers width to be 100% of maps width
	if ($mapwidthunit == '%') {
		$layer_marker_list_width = '100%';
	} else {
		$layer_marker_list_width = $mapwidth.$mapwidthunit;
	}
	$lmm_out .= '<div id="lmm_listmarkers_'.$uid.'" class="lmm-listmarkers" style="width:' . $layer_marker_list_width . ';">'.PHP_EOL;
	$lmm_out .= '<table style="width:' . $layer_marker_list_width . ';" id="lmm_listmarkers_table_'.$uid.'" class="lmm-listmarkers-table">';
	foreach ($layer_marker_list as $row){
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_icon' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_icon' ] == 1 ) ) {
			$lmm_out .= '<tr><td class="lmm-listmarkers-icon">';
			if ($row['micon'] != null) {
				$lmm_out .= '<img style="border-radius:0;box-shadow:none;" alt="marker icon" src="' . LEAFLET_PLUGIN_ICONS_URL . '/'.$row['micon'].'" />';
			} else {
				$lmm_out .= '<img style="border-radius:0;box-shadow:none;" alt="marker icon" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" />';
			};
		} else {
			$lmm_out .= '<tr><td>';
		};
		$lmm_out .= '</td><td class="lmm-listmarkers-popuptext"><div class="lmm-listmarkers-panel-icons">';
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_directions' ] == 1 ) ) {
			if ($lmm_options['directions_provider'] == 'googlemaps') {
				if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
				if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
				if ( $row['maddress'] != NULL ) { $google_from = urlencode($row['maddress']); } else { $google_from = $row['mlat'] . ',' . $row['mlon']; }
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
				$lmm_out .= '<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&amp;t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&amp;layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&amp;doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&amp;om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img alt="' . $directions_transport_type_icon . '" src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'yours') {
				if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
				$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $row['mlat'] . '&amp;tlon=' . $row['mlon'] . '&amp;v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&amp;fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&amp;layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img alt="' . $directions_transport_type_icon . '" src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'ors') {
				if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
				$lmm_out .= '<a href="http://www.openrouteservice.org/?pos=' . $row['mlon'] . ',' . $row['mlat'] . '&amp;wp=' . $row['mlon'] . ',' . $row['mlat'] . '&amp;zoom=' . $row['mzoom'] . '&amp;routeWeigh=' . $lmm_options[ 'directions_ors_routeWeigh' ] . '&amp;routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&amp;layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'bingmaps') {
				if ( $row['maddress'] != NULL ) { $bing_to = '_' . urlencode($row['maddress']); } else { $bing_to = ''; }
				$lmm_out .= '<a href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $row['mlat'] . '_' . $row['mlon'] . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="icon-car" /></a>';
			}
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_kml' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['markerid'] . '&amp;name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $row['markerid'] . '&amp;callback=jsonp&amp;full=yes&amp;full_icon_url=yes" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $row['markerid'] . '&amp;callback=jsonp&amp;full=yes&amp;full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_georss' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</div>';
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_markername' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_markername' ] == 1 ) ) {
			$lmm_out .= '<span class="lmm-listmarkers-markername">' . stripslashes(htmlspecialchars($row['markername'])) . '</span>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ] == 1 ) ) {
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
			$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, stripslashes(preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $row['mpopuptext'])));
			//info: strip evil scripts
			if ($lmm_options['wp_kses_status'] == 'enabled') {
				$popuptext_sanitized = wp_kses($popuptext_sanitized, array_merge($allowedposttags, $additionaltags));
			}
			$lmm_out .= '<br/><span class="lmm-listmarkers-popuptext-only">' . do_shortcode($popuptext_sanitized) . '</span>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_address' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_address' ] == 1 ) ) {
			if ( $row['mpopuptext'] == NULL ) {
				$lmm_out .= stripslashes(htmlspecialchars($row['maddress']));
			} else if ( ($row['mpopuptext'] != NULL) && ($row['maddress'] != NULL) ) {
				$lmm_out .= '<br/><div class="lmm-listmarkers-hr">' . stripslashes(htmlspecialchars($row['maddress'])) . '</div>';
			}
		}
		$lmm_out .= '</td></tr>';
	} //info: end foreach

   //info: adding info if more markers are available than listed in markers list
   $markercount = 0;
   if ($multi_layer_map == 0) {
		$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.intval($id));
   } else if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == 'all' ) ) {
		$markercount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
   } else if ( ($multi_layer_map == 1) && ( $multi_layer_map_list != NULL ) && ($multi_layer_map_list != 'all') ) {
		foreach ($multi_layer_map_list_exploded as $mlmrowcount){
		$mlm_count_temp{$mlmrowcount} = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.intval($mlmrowcount));
		}
		$markercount = array_sum($mlm_count_temp);
   } else if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == NULL ) ) {
		$markercount = 0;
   }
   if ($markercount > $lmm_options[ 'defaults_layer_listmarkers_limit' ]) {
	$asc_desc = ($lmm_options['defaults_layer_listmarkers_sort_order'] == 'ASC') ? __('ascending','lmm') : __('descending','lmm');
	if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.id') {
		$orderby = 'ID';
	} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.markername') {
		$orderby = __('marker name','lmm');
	} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.createdon') {
		$orderby = __('created on','lmm');
	} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.updatedon') {
		$orderby = __('updated on','lmm');
	} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.layer') {
		$orderby = __('layer ID','lmm');
	} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.address') {
		$orderby = __('address','lmm');
	}
	$lmm_out .= '<tr><td colspan="2" style="text-align:center">' . sprintf(__('The table above is listing %1s out of %2s markers (sorted by %3s %4s)','lmm'), intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), $markercount, $orderby, $asc_desc) . '</td></tr>';
	}
	$lmm_out .= '</table></div>';
	} //info: end display a list of markers under the map

	//info: fallback for adding js to footer 1
	if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
		$lmm_out .= '</div>'; //info: end leaflet_maps_marker_$uid
		global $lmmjs_out; //info: dont add to WP<3.3 as js gets duplicated!
	//info: fallback for adding js to footer 2
	} else {
		$lmmjs_out = '<script type="text/javascript">'.PHP_EOL;
 	}
	$lmmjs_out .= '/* Maps created with Leaflet Maps Marker - #1 mapping plugin for WordPress (www.mapsmarker.com) */'.PHP_EOL;
	if (!empty($layer)) {
		$mapname_js = 'layermap_' . intval($layer);
	} else if (!empty($marker)) {
		$mapname_js = 'markermap_' . intval($marker);
	} else if (empty($layer) and empty($marker)) {
		$mapname_js = 'markermap_' . str_replace(array('.',','),'_', abs($mlat)) . '_'  . str_replace(array('.',','),'_', abs($mlon));
	}
	$lmmjs_out .= 'var '.$mapname_js.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
	$attrib_prefix = '<a href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker for WordPress - helping you to share your favorite spots and tracks','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Leaflet Maps Marker') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Leaflet Maps Marker') . '\">icons</a>)';
	$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
	$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	//info: workaround for #230 "Uncaught Map has no maxZoom specified" 
	if ($lmm_options['maxzoom_compatibility_mode'] == 'enabled') {
		$map_retina_detection = 'false';
	} else {
		$map_retina_detection = $lmm_options['map_retina_detection'];
	}
	$lmmjs_out .= '(function($) {'.PHP_EOL;
	$lmmjs_out .= $mapname_js.' = new L.Map("'.$mapname.'", { dragging: ' . $lmm_options['misc_map_dragging'] . ', touchZoom: ' . $lmm_options['misc_map_touchzoom'] . ', scrollWheelZoom: ' . $lmm_options['misc_map_scrollwheelzoom'] . ', doubleClickZoom: ' . $lmm_options['misc_map_doubleclickzoom'] . ', boxzoom: ' . $lmm_options['map_interaction_options_boxzoom'] . ', trackResize: ' . $lmm_options['misc_map_trackresize'] . ', worldCopyJump: ' . $lmm_options['map_interaction_options_worldcopyjump'] . ', closePopupOnClick: ' . $lmm_options['misc_map_closepopuponclick'] . ', keyboard: ' . $lmm_options['map_keyboard_navigation_options_keyboard'] . ', keyboardPanOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) . ', keyboardZoomOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) . ', inertia: ' . $lmm_options['map_panning_inertia_options_inertia'] . ', inertiaDeceleration: ' . intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) . ', inertiaMaxSpeed: ' . intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) . ', zoomControl: ' . $lmm_options['misc_map_zoomcontrol'] . ', crs: ' . $lmm_options['misc_projections'] . '});'.PHP_EOL;
	$lmmjs_out .= $mapname_js.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;
	
	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified")
	$maxzoom = 21;  //info: customizable in MMP only
	$lmmjs_out .= $mapname_js.'._layersMaxZoom = ' . $maxzoom . ';'.PHP_EOL;
	
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
		$osm_attribution = __("Map",'lmm').': &copy; <a href=\"https://www.openstreetmap.fr\" target=\"_blank\">Openstreetmap France</a> & <a tabindex=\"123\" href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-hot') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = $osm_attrib_general . ', ' . __("Tiles courtesy of","lmm") . ' <a href=\"https://hotosm.org/\" target=\"_blank\">Humanitarian OpenStreetMap Team</a>' . $osm_editlink;
	}
	$lmmjs_out .= 'var osm_mapnik = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $osm_attribution . '", detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  $lmm_options[ 'stamen_terrain_flavor' ] . '/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  $lmm_options[ 'stamen_toner_flavor' ] . '/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' .  LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' .  $attrib_stamen . '", detectRetina: ' .  $map_retina_detection . '});'.PHP_EOL;
	
	if ($lmm_options['mapquest_api_key'] != NULL) {
		$lmmjs_out .= 'if (typeof MQ !== "undefined") {';
		$lmmjs_out .= 'mapquest_osm = new MQ.mapLayer();'.PHP_EOL;
		$lmmjs_out .= 'mapquest_aerial = new MQ.satelliteLayer();'.PHP_EOL;
		$lmmjs_out .= 'mapquest_hybrid = new MQ.hybridLayer();'.PHP_EOL;
		$lmmjs_out .= '} else { if (window.console) { console.log("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); } }';
	}
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		$lmmjs_out .= 'var googleLayer_roadmap = new L.Google("ROADMAP", {detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
		$lmmjs_out .= 'var googleLayer_satellite = new L.Google("SATELLITE", {detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
		$lmmjs_out .= 'var googleLayer_hybrid = new L.Google("HYBRID", {detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
		$lmmjs_out .= 'var googleLayer_terrain = new L.Google("TERRAIN", {detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
		$lmmjs_out .= 'var bingaerial = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
		$lmmjs_out .= 'var bingaerialwithlabels = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
		$lmmjs_out .= 'var bingroad = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	};
	$lmmjs_out .= 'var ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps4"], detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1, attribution: "' . $attrib_basemapat . '", subdomains: ["maps1", "maps2", "maps3", "maps3"], detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	//info: MapBox basemaps
	$lmmjs_out .= 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;	
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$lmmjs_out .= 'var custom_basemap = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap_tms' ] . ', ' . $error_tile_url_custom_basemap . 'attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap_nowrap_enabled' ] . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var custom_basemap2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap2_tms' ] . ', ' . $error_tile_url_custom_basemap2 . 'attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap2_nowrap_enabled' ] . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var custom_basemap3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap3_tms' ] . ', ' . $error_tile_url_custom_basemap3 . 'attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap3_nowrap_enabled' ] . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var empty_basemap = new L.TileLayer("");'.PHP_EOL;

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
	$lmmjs_out .= 'var overlays_custom = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom_tms' ] . ', ' . $error_tile_url_overlays_custom . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var overlays_custom2 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom2_tms' ] . ', ' . $error_tile_url_overlays_custom2 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom2_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var overlays_custom3 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom3_tms' ] . ', ' . $error_tile_url_overlays_custom3 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom3_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	$lmmjs_out .= 'var overlays_custom4 = new L.TileLayer("' . str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) . '", {tms: ' . $lmm_options[ 'overlays_custom4_tms' ] . ', ' . $error_tile_url_overlays_custom4 . 'attribution: "' . addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom4_opacity' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;

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
	$lmmjs_out .= 'var wms = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) . '", {wmsid: "wms", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ])) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ])) . '"' . $wms_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms2 == 1) {
	$lmmjs_out .= 'var wms2 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) . '", {wmsid: "wms2", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ])) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ])) . '"' . $wms2_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms3 == 1) {
	$lmmjs_out .= 'var wms3 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) . '", {wmsid: "wms3", layers: "' . htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ]))) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ])) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ])) . '"' . $wms3_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms4 == 1) {
	$lmmjs_out .= 'var wms4 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) . '", {wmsid: "wms4", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ])) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ])) . '"' . $wms4_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms5 == 1) {
	$lmmjs_out .= 'var wms5 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) . '", {wmsid: "wms5", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ])) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ])) . '"' . $wms5_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms6 == 1) {
	$lmmjs_out .= 'var wms6 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) . '", {wmsid: "wms6", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ])) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ])) . '"' . $wms6_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms7 == 1) {
	$lmmjs_out .= 'var wms7 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) . '", {wmsid: "wms7", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ])) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ])) . '"' . $wms7_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms8 == 1) {
	$lmmjs_out .= 'var wms8 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) . '", {wmsid: "wms8", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ])) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ])) . '"' . $wms8_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms9 == 1) {
	$lmmjs_out .= 'var wms9 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) . '", {wmsid: "wms9", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ])) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ])) . '"' . $wms9_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	if ($wms10 == 1) {
	$lmmjs_out .= 'var wms10 = new L.TileLayer.WMS("' . htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) . '", {wmsid: "wms10", layers: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ])) . '", styles: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ])) . '", format: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ])) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ])) . '"' . $wms10_subdomains  . ', detectRetina: ' . $map_retina_detection . '});'.PHP_EOL;
	}
	//info: controlbox - basemaps
	$lmmjs_out .= 'var layersControl_'.$mapname_js.' = new L.Control.Layers('.PHP_EOL;
	$lmmjs_out .= '{';
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
	$lmmjs_out .= substr($basemaps_available, 0, -1);
	$lmmjs_out .= '},'.PHP_EOL;

	//info: controlbox - add available overlays
	$lmmjs_out .= '{';
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
	$lmmjs_out .= substr($overlays_custom_available, 0, -1);
	$lmmjs_out .= '},'.PHP_EOL;

	//info: controlbox - hidden / collapsed / expanded status
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 0 ) )
		$lmmjs_out .= '{ } );'.PHP_EOL;
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 1 ) )
		$lmmjs_out .= '{ collapsed: true } );'.PHP_EOL;
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 2 ) )
		$lmmjs_out .= '{ collapsed: false } );'.PHP_EOL;
	$lmmjs_out .= $mapname_js.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;
	$lmmjs_out .= $mapname_js.'.addLayer(' . $basemap . ')';
	//info: controlbox - check active overlays on marker/layer level
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmmjs_out .= ".addLayer(overlays_custom)";
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmmjs_out .= ".addLayer(overlays_custom2)";
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmmjs_out .= ".addLayer(overlays_custom3)";
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmmjs_out .= ".addLayer(overlays_custom4)";
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmmjs_out .= ".addLayer(wms)";
	if ( $wms2 == 1 )
		$lmmjs_out .= ".addLayer(wms2)";
	if ( $wms3 == 1 )
		$lmmjs_out .= ".addLayer(wms3)";
	if ( $wms4 == 1 )
		$lmmjs_out .= ".addLayer(wms4)";
	if ( $wms5 == 1 )
		$lmmjs_out .= ".addLayer(wms5)";
	if ( $wms6 == 1 )
		$lmmjs_out .= ".addLayer(wms6)";
	if ( $wms7 == 1 )
		$lmmjs_out .= ".addLayer(wms7)";
	if ( $wms8 == 1 )
		$lmmjs_out .= ".addLayer(wms8)";
	if ( $wms9 == 1 )
		$lmmjs_out .= ".addLayer(wms9)";
	if ( $wms10 == 1 )
		$lmmjs_out .= ".addLayer(wms10)";
	$lmmjs_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? ".addControl(layersControl_".$mapname_js.");" : ";".PHP_EOL;
	//info: add scale control
	if ( $lmm_options['map_scale_control'] == 'enabled' ) {
		$lmmjs_out .= PHP_EOL . "var scale_".$mapname_js." = L.control.scale({position:'" . $lmm_options['map_scale_control_position'] . "', maxWidth: " . intval($lmm_options['map_scale_control_maxwidth']) . ", metric: " . $lmm_options['map_scale_control_metric'] . ", imperial: " . $lmm_options['map_scale_control_imperial'] . ", updateWhenIdle: " . $lmm_options['map_scale_control_updatewhenidle'] . "});".PHP_EOL;
		$lmmjs_out .= "scale_".$mapname_js.".addTo(".$mapname_js.");".PHP_EOL;
	}
	if (!(empty($mlat) or empty($mlon)) ) {
	if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') { $defaults_marker_icon_title = "title: '" . strip_tags(htmlspecialchars_decode($markername)) . "', "; } else { $defaults_marker_icon_title = ""; };
	$lmmjs_out .= 'var marker_'.$mapname_js.' = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'),{ ' . $defaults_marker_icon_title . ' opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
	 if ($micon == NULL) {
		  $lmmjs_out .= "marker_".$mapname_js.".options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
	  } else {
		  $lmmjs_out .= "marker_".$mapname_js.".options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_ICONS_URL . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
	};
	if ( (empty($mpopuptext)) && ($lmm_options['directions_popuptext_panel'] == 'no') ) $lmmjs_out .= 'marker_'.$mapname_js.'.options.clickable = false;'.PHP_EOL;
	$lmmjs_out .= $mapname_js.'.addLayer(marker_'.$mapname_js.');'.PHP_EOL;

	if ($lmm_options['directions_popuptext_panel'] == 'yes') {

	 	$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
		$mpopuptext = $mpopuptext . '<div class=\'popup-directions\' style=\'' . $mpopuptext_css . '\'>' . $address . ' (';

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
			$mpopuptext = $mpopuptext . "<a href=http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		} else if ($lmm_options['directions_provider'] == 'yours') {
			$mpopuptext = $mpopuptext . "<a href=http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . $lmm_options[ 'directions_yours_route_type' ] . "&layer=" . $lmm_options[ 'directions_yours_layer' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		} else if ($lmm_options['directions_provider'] == 'ors') {
			$mpopuptext = $mpopuptext . "<a href=http://www.openrouteservice.org/?pos=" . $lon . "," . $lat . "&wp=" . $lon . "," . $lat . "&zoom=" . $zoom . "&routeWeigh=" . $lmm_options[ 'directions_ors_routeWeigh' ] . "&routeOpt=" . $lmm_options[ 'directions_ors_routeOpt' ] . "&layer=" . $lmm_options[ 'directions_ors_layer' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		} else if ($lmm_options['directions_provider'] == 'bingmaps') {
			if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
			$mpopuptext = $mpopuptext . "<a href=https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a>";
		}
		$mpopuptext = $mpopuptext . ')</div>';
	}
		if (!empty($mpopuptext)) {
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
			$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $mpopuptext));
			$lmmjs_out .= 'marker_'.$mapname_js.'.bindPopup("' . $popuptext_sanitized . '", {maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})'.$mopenpopup.';'.PHP_EOL;
		}
  } else if (!empty($geojson) or !empty($geojsonurl) or !empty($layer) ) {
	$lmmjs_out .= 'var geojsonObj_'.$mapname_js.', mapIcon, marker_clickable, marker_title;'.PHP_EOL;
	//info: load GeoJSON for layer maps
	if (!empty($layer)) {
		$lmmjs_out .= 'var xhReq_'.$mapname_js.' = new XMLHttpRequest();'.PHP_EOL;
		if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; }
			$lmmjs_out .= 'xhReq_'.$mapname_js.'.open("GET", "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $geojson_api_link . '&full=no&full_icon_url=no", true);'.PHP_EOL; //info: for caching add &timestamp=' . time() . '
			$lmmjs_out .= 'xhReq_'.$mapname_js.'.onreadystatechange = function xhReq_'.$uid.'(e) { if (xhReq_'.$mapname_js.'.readyState === 4) { if (xhReq_'.$mapname_js.'.status === 200) {'.PHP_EOL; //info: async 1/2			
			
			//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
			$lmmjs_out .= 'if (xhReq_'.$mapname_js.'.responseText.indexOf(\'{"type"\') != 0) {
	var '.$mapname_js.'_position = xhReq_'.$mapname_js.'.responseText.indexOf(\'{"type"\');
	try { geojsonObj_'.$mapname_js.' = JSON.parse(xhReq_'.$mapname_js.'.responseText.slice('.$mapname_js.'_position)); } catch (e) { console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message); }'.PHP_EOL;
			$lmmjs_out .= '} else {
	try { geojsonObj_'.$mapname_js.' = JSON.parse(xhReq_'.$mapname_js.'.responseText); } catch (e) { console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message); }'.PHP_EOL;
			$lmmjs_out .= '}'.PHP_EOL;
			
	} 
	$lmmjs_out .= 'L.geoJson(geojsonObj_'.$mapname_js.', {'.PHP_EOL;
	$lmmjs_out .= '		onEachFeature: function(feature, marker) {'.PHP_EOL;
	$lmmjs_out .= "			if (feature.properties.text != '') {".PHP_EOL;
	$lmmjs_out .= '			marker.bindPopup(feature.properties.text, {'.PHP_EOL;
	$lmmjs_out .= '			maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', '.PHP_EOL;
	$lmmjs_out .= '			minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', '.PHP_EOL;
	$lmmjs_out .= '			maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', '.PHP_EOL;
	$lmmjs_out .= '			autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', '.PHP_EOL;
	$lmmjs_out .= '			closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', '.PHP_EOL;
	$lmmjs_out .= '			autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
	$lmmjs_out .= '			});'.PHP_EOL;
	$lmmjs_out .= '			}'.PHP_EOL;
	$lmmjs_out .= '		},'.PHP_EOL;
	$lmmjs_out .= 'pointToLayer: function (feature, latlng) {'.PHP_EOL;
	//info: keep GeoJSON small for internal use
	$lmmjs_out .= " if (feature.properties.iconUrl == undefined) {".PHP_EOL;
	$lmmjs_out .= '	mapIcon = L.icon({ '.PHP_EOL;
	$lmmjs_out .= "		iconUrl: (feature.properties.icon != '') ? '" . LEAFLET_PLUGIN_ICONS_URL . "/' + feature.properties.icon : '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png" . "',".PHP_EOL;
	$lmmjs_out .= '		iconSize: [' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= '		iconAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= '		popupAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= "		shadowUrl: '" . $marker_shadow_url . "',".PHP_EOL;
	$lmmjs_out .= '		shadowSize: [' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= '		shadowAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= "		className: (feature.properties.icon == '') ? 'lmm_marker_icon_default' : 'lmm_marker_icon_'+ feature.properties.icon.slice(0,-4)".PHP_EOL;
	$lmmjs_out .= '	});'.PHP_EOL;
	//info: use full icon url for external use
	$lmmjs_out .= '} else {'.PHP_EOL;
	$lmmjs_out .= '	mapIcon = L.icon({ '.PHP_EOL;
	$lmmjs_out .= "		iconUrl: feature.properties.iconUrl,".PHP_EOL;
	$lmmjs_out .= '		iconSize: [' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= '		iconAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= '		popupAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= "		shadowUrl: '" . $marker_shadow_url . "',".PHP_EOL;
	$lmmjs_out .= '		shadowSize: [' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= '		shadowAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . '],'.PHP_EOL;
	$lmmjs_out .= "		className: (feature.properties.icon == '') ? 'lmm_marker_icon_default' : 'lmm_marker_icon_'+ feature.properties.icon.slice(0,-4)".PHP_EOL;
	$lmmjs_out .= '	});'.PHP_EOL;
	$lmmjs_out .= '};'.PHP_EOL;
	$lmmjs_out .= "if (feature.properties.text == '') { marker_clickable = false } else { marker_clickable = true };".PHP_EOL;
	if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') {
	$lmmjs_out .= "if (feature.properties.markername == '') { marker_title = '' } else { marker_title = feature.properties.markername };".PHP_EOL;
	}
	$lmmjs_out .= 'return L.marker(latlng, {icon: mapIcon, clickable: marker_clickable, title: marker_title, opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
	$lmmjs_out .= '}'.PHP_EOL;
	$lmmjs_out .= '}).addTo(' . $mapname_js . ');'.PHP_EOL;
}
	if (!empty($layer)) {
		$lmmjs_out .= '} else { if (window.console) { console.error(xhReq_'.$mapname_js.'.statusText); } } } }; xhReq_'.$mapname_js.'.onerror = function xhReq_'.$uid.'(e) { if (window.console) { console.error(xhReq_'.$mapname_js.'.statusText); } }; xhReq_'.$mapname_js.'.send(null);'.PHP_EOL; //info: async 2/2
	}
	$lmmjs_out .= '})(jQuery);'.PHP_EOL;

	//info: support for responsive templates
	if ( ($mapwidthunit != '%') && ($lmm_options['misc_responsive_support'] == 'enabled') ) {
		$lmmjs_out .= "jQuery(document).ready( function($) {
function resizeMap() {
	var map = jQuery('#lmm_".$uid."');
	";
	if ($listmarkers == 1) {
		$lmmjs_out .= "var map_list_markers_div = jQuery('#lmm_listmarkers_".$uid."');".PHP_EOL;
		$lmmjs_out .= "\t" . "var map_list_markers_table = jQuery('#lmm_listmarkers_table_".$uid."');".PHP_EOL;
	}
	$lmmjs_out .= "\t" . "var map_parent_size = jQuery('#lmm_".$uid."').parent().width();
	if( (map_parent_size >0) && (map_parent_size < ".$mapwidth.") ) {
		map.css({ 'width': '100%'});".PHP_EOL;
		if ($listmarkers == 1) {
			$lmmjs_out .= "\t\t" . "map_list_markers_div.css({ 'width': '100%'});".PHP_EOL;
			$lmmjs_out .= "\t\t" . "map_list_markers_table.css({ 'width': '100%'});".PHP_EOL;
		}
	$lmmjs_out .= "\t\t" . $mapname_js.".invalidateSize();
	}".PHP_EOL;
		$lmmjs_out .= "}
resizeMap();".PHP_EOL;
	//info: fix for incomplete map tiles display after device orientation change on mobile devices
	if ( wp_is_mobile() ) {
		$lmmjs_out .= "window.addEventListener('orientationchange', function lmm_invalidateSize_".$mapname_js."() { ".$mapname_js.".invalidateSize();}, false);".PHP_EOL;
	}
	//info: fix for loading maps in bootstrap tabs, jQuery UI tabs & jQuery Mobile
	$lmmjs_out .= "if (typeof jQuery().modal == 'function') {
		".$mapname_js.".invalidateSize();
		jQuery('a[data-toggle=\"tab\"],.tabbed-area a,.nav-tabs a').on('shown.bs.tab', function (e) {
			".$mapname_js.".invalidateSize();
		});
	}
	if (typeof jQuery.ui != 'undefined') {
	".$mapname_js.".invalidateSize();
	jQuery('.ui-tabs').on('tabsactivate', function(event, ui) {
		".$mapname_js.".invalidateSize();
	});
}
if (typeof jQuery.mobile != 'undefined') {
	jQuery(document).bind('pageinit', function( event, data ){
		".$mapname_js.".invalidateSize();
	});".PHP_EOL;
	if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
		$jquery_mobile_warning = (current_user_can( 'manage_options' )) ? '		if (window.console) { console.log("' . esc_attr__('Warning: your site is using jQuery Mobile which can cause Maps Marker Pro maps to break. If this is true on your site, please navigate to Maps Marker (Pro)-Settings / Misc / General settings and set the option -Where to insert Javascript files on frontend?- to -header+inline javascript- to fix this issue.','lmm') . '"); }'.PHP_EOL : '';
		$lmmjs_out .= $jquery_mobile_warning;
	}
	$lmmjs_out .= "}".PHP_EOL;
	$lmmjs_out .= "});".PHP_EOL;
}
	//info: fix for loading maps in woocommmerce tabs / setTimeout needed
	if (is_plugin_active('woocommerce/woocommerce.php') ) {
	$lmmjs_out .= "
		if (typeof jQuery != 'undefined') {
			jQuery(document).ready(function($) {
				".$mapname_js.".invalidateSize();
				jQuery('.wc-tabs ul.tabs li a, .woocommerce-tabs ul.tabs li a, ul.wc-tabs li a').click(function(){
					setTimeout(function(){
						".$mapname_js.".invalidateSize();
					}, 1);
				});
			});
		}
	";
	}
	//info: fix tabs switching for WPBakery Visual Composer (since 4.7?)
    if (is_plugin_active('js_composer/js_composer.php') ) {
		$lmmjs_out .= "
			if (typeof jQuery != 'undefined') {
				jQuery(document).ready(function($) {
					".$mapname_js.".invalidateSize();
					jQuery('.vc_tta').on('show.vc.tab, show.vc.accordion', function(event, ui) { 
						".$mapname_js.".invalidateSize(); 
					}); 
				});
			}
		".PHP_EOL;
	}
	//info: fallback for adding js to footer 3
	if ($lmm_options['misc_javascript_header_footer'] == 'footer') {
		//info: enqueue map js to footer
		global $wp_scripts;
		if ($lmm_options['mapquest_api_key'] != NULL) {
			wp_enqueue_script( 'leafletmapsmarker-mapquest' );
		}
		wp_enqueue_script( 'show_map' );
		$wp_scripts->add_data( 'show_map', 'data', $lmmjs_out );
	} else {
		$lmmjs_out .= '</script>'.PHP_EOL;
		$lmmjs_out .= '</div>'; //info: end leaflet_maps_marker_$uid
		$lmm_out = $lmm_out . $lmmjs_out;
	}

	//info: if do_shortcode() within template files is used to show maps or for shortcodes in widgets
	global $wp_styles;
	if ( function_exists( 'is_rtl' ) && is_rtl() ) { 
		$css_enqueue_handle = 'leafletmapsmarker-rtl';
	} else { 
		$css_enqueue_handle = 'leafletmapsmarker'; 
	}
	if (!wp_style_is( $css_enqueue_handle, 'done' )) {
		wp_enqueue_style($css_enqueue_handle);
		wp_enqueue_style('leafletmapsmarker-ie-only');
		$wp_styles->add_data('leafletmapsmarker-ie-only', 'conditional', 'lt IE 9');
		//info: override max image width in popups
		$lmm_custom_css = ".leaflet-popup-content img { " . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . " }";
			wp_add_inline_style($css_enqueue_handle,$lmm_custom_css);
	}
  } //info: end (!is_feed())
}
?>