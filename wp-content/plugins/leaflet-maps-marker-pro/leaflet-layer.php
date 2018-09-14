<?php
/*
    Edit layer - Maps Marker Pro
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-layer.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php');

global $wpdb, $current_user, $wp_version, $allowedtags, $locale, $is_chrome, $is_safari;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();
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

$current_editor = get_option( 'leafletmapsmarker_editor' );
$current_editor_css = ($current_editor == 'simplified') ? 'display:none;' : 'display:block';
$current_editor_css_inline = ($current_editor == 'simplified') ? 'display:none;' : 'display:inline';
$current_editor_css_audit = ($current_editor == 'simplified') ? 'display:none;' : '';

//info: workaround - select shortcode on input focus doesnt work on iOS
$is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
if ( version_compare( $wp_version, '3.4', '>=' ) ) {
	 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
} else {
	 $shortcode_select = '';
}
//info: workaround for datetime-local issue on iOS
if ($is_ios) {
	$datetime_ios_workaround = 'text';
} else {
	$datetime_ios_workaround = 'datetime-local';
}
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$layerlist = $wpdb->get_results('SELECT l.id as lid,l.name as lname FROM `'.$table_name_layers.'` as l WHERE l.multi_layer_map = 0 and l.id != 0 ORDER BY l.id ASC', ARRAY_A);

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$oid = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : '');
$lat_check = isset($_POST['layerviewlat']) ? $_POST['layerviewlat'] : (isset($_GET['layerviewlat']) ? $_GET['layerviewlat'] : '');
$lon_check = isset($_POST['layerviewlon']) ? $_POST['layerviewlon'] : (isset($_GET['layerviewlon']) ? $_GET['layerviewlon'] : '');

if (!empty($action)) {
	$layernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($layernonce, 'layer-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').''); };

	if ($action == 'deleteboth') { //info: for single maps only, not supported for multi-layer-maps
		$createdby_check = $wpdb->get_var( 'SELECT `createdby` FROM `'.$table_name_layers.'` WHERE `id`='.$oid );
		if (MMP_Globals::check_capability('delete', $createdby_check)) {
			//info: delete qr code cache images for assigned markers
			$layer_marker_list_qr = $wpdb->get_results('SELECT m.id as markerid,m.layer as mlayer,l.id as lid FROM `'.$table_name_layers.'` as l INNER JOIN '.$table_name_markers.' AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id=' . $oid, ARRAY_A);
			foreach ($layer_marker_list_qr as $row){
				if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png') ) {
					unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png');
				}
			}
			//info: delete qr code cache image for layer
			if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $oid . '.png') ) {
				unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $oid . '.png');
			}

			$markers_of_layer = $wpdb->get_results(" SELECT id,layer FROM  `$table_name_markers` WHERE layer LIKE '%\"".$oid."\"%' ");
			if(!empty($markers_of_layer)){
				foreach( $markers_of_layer as $marker ){
					$marker_layers = json_decode($marker->layer,true);
					if(count($marker_layers) == 1){
						$result = $wpdb->prepare( "DELETE FROM `$table_name_markers` WHERE `id` = %d", $marker->id );
						$wpdb->query( $result );
					}else{
						$layer_key = array_search($oid, $marker_layers);
						unset($marker_layers[$layer_key]);
						$new_layer = json_encode($marker_layers);
						$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `layer` = '".$new_layer."' WHERE `id` = %d", $marker->id );
						$wpdb->query( $result );
					}
				}
			}
			$result2 = $wpdb->prepare( "DELETE FROM `$table_name_layers` WHERE `id` = %d", $oid );
			$wpdb->query( $result2 );

			$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
			echo '<p><div class="updated" style="padding:10px;">' . __('Layer and assigned markers have been successfully deleted (or the reference to the layer has been removed if marker was assigned to multiple layers)','lmm') . '</div><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('list all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Error: your user does not have the permission to delete layers from other users!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
		}
	} elseif($action == 'duplicatelayerandmarkers'){ //info: for single maps only, not supported for multi-layer-maps
		global $current_user;
		$selected_layer = $_POST['id'];
		$new_layer_ids = array();
		$result = $wpdb->get_row( $wpdb->prepare('SELECT * FROM `'.$table_name_layers.'` WHERE `id`= %d',$selected_layer), ARRAY_A);
		if ($result['mlm_filter_details'] == NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d)", $result['name'], $result['basemap'], $result['layerzoom'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $result['layerviewlat'], $result['layerviewlon'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['listmarkers'], $result['multi_layer_map'], $result['multi_layer_map_list'], $result['address'], $result['clustering'], $result['gpx_url'], $result['gpx_panel'] );
		} else {
			//info: save filters details to the database.
			$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d, %s)", $result['name'], $result['basemap'], $result['layerzoom'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $result['layerviewlat'], $result['layerviewlon'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['listmarkers'], $result['multi_layer_map'], $result['multi_layer_map_list'], $result['address'], $result['clustering'], $result['gpx_url'], $result['gpx_panel'], $result['mlm_filter'], $result['mlm_filter_details'] );
		}
		$wpdb->query( $sql_duplicate );
		$new_layer_id = $wpdb->insert_id;
		//info: duplicate the markers.
		$duplicated_markers = $wpdb->get_results("SELECT * FROM `$table_name_markers` WHERE layer LIKE '%\"". $selected_layer ."\"%'", ARRAY_A);
		foreach($duplicated_markers as $result){
			$layer_duplicate = json_encode(array(strval($new_layer_id)));
			if ($result['kml_timestamp'] == NULL) {
				$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $result['markername'], $result['basemap'], $layer_duplicate, $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['address'], $result['gpx_url'], $result['gpx_panel'] );
			} else if ($result['kml_timestamp'] != NULL) {
				$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $result['markername'], $result['basemap'], $layer_duplicate, $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['kml_timestamp'], $result['address'], $result['gpx_url'], $result['gpx_panel'] );
			}
			$wpdb->query( $sql_duplicate );
			$new_marker_ids[] = $wpdb->insert_id;
		}
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $new_layer_id . '&status=duplicated"; </script> ';
	}
} else { //info: !empty($action) 2/3
	$isedit = isset($_GET['id']);
	if (!$isedit) {
		//info: prepare translate URL
		if(MMP_Globals::check_multilingual() == 'wpml'){
			$translate_url_layername = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php');
			$translate_url_layeraddress = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php');
		}elseif(MMP_Globals::check_multilingual() == 'pll'){
			$translate_url_layername = admin_url('admin.php?page=mlang_strings');
			$translate_url_layeraddress = admin_url('admin.php?page=mlang_strings');
		}
		else{
			$translate_url_layername = 'https://www.mapsmarker.com/multilingual';
			$translate_url_layeraddress = 'https://www.mapsmarker.com/multilingual';
		}
		//info: prepare layer vars
		$id = '';
		$name = '';
		$basemap = $lmm_options[ 'standard_basemap' ];
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
		$layerviewlat = str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lat' ]));
		$layerviewlon = str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lon' ]));
		$layerzoom = intval($lmm_options[ 'defaults_layer_zoom' ]);
		$mapwidth = intval($lmm_options[ 'defaults_layer_mapwidth' ]);
		$mapwidthunit = $lmm_options[ 'defaults_layer_mapwidthunit' ];
		$mapheight = intval($lmm_options[ 'defaults_layer_mapheight' ]);
		$panel = $lmm_options[ 'defaults_layer_panel' ];
		$createdby = $current_user->user_login;
		$createdon = current_time('mysql',0);
		$updatedby = $current_user->user_login;
		$updatedon = current_time('mysql',0);
		$controlbox = $lmm_options[ 'defaults_layer_controlbox' ];
		$overlays_custom = ( (isset($lmm_options[ 'defaults_layer_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
		$overlays_custom2 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
		$overlays_custom3 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
		$overlays_custom4 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
		$wms = ( (isset($lmm_options[ 'defaults_layer_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms_active' ] == 1 ) ) ? '1' : '0';
		$wms2 = ( (isset($lmm_options[ 'defaults_layer_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms2_active' ] == 1 ) ) ? '1' : '0';
		$wms3 = ( (isset($lmm_options[ 'defaults_layer_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms3_active' ] == 1 ) ) ? '1' : '0';
		$wms4 = ( (isset($lmm_options[ 'defaults_layer_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms4_active' ] == 1 ) ) ? '1' : '0';
		$wms5 = ( (isset($lmm_options[ 'defaults_layer_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms5_active' ] == 1 ) ) ? '1' : '0';
		$wms6 = ( (isset($lmm_options[ 'defaults_layer_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms6_active' ] == 1 ) ) ? '1' : '0';
		$wms7 = ( (isset($lmm_options[ 'defaults_layer_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms7_active' ] == 1 ) ) ? '1' : '0';
		$wms8 = ( (isset($lmm_options[ 'defaults_layer_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms8_active' ] == 1 ) ) ? '1' : '0';
		$wms9 = ( (isset($lmm_options[ 'defaults_layer_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms9_active' ] == 1 ) ) ? '1' : '0';
		$wms10 = ( (isset($lmm_options[ 'defaults_layer_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms10_active' ] == 1 ) ) ? '1' : '0';
		$listmarkers = $lmm_options[ 'defaults_layer_listmarkers' ];
		$multi_layer_map = 0;
		$multi_layer_map_list = array();
		$multi_layer_map_list_exploded = array();
		$address = '';
		$clustering = ($lmm_options[ 'defaults_layer_clustering' ] == 'enabled' ) ? '1' : '0';
		$markercount = 0;
		$gpx_url = '';
		$gpx_panel = 0;
		$check_mlm = '';
		$mlm_disabled = '';
		$mlm_filter_controlbox = $lmm_options[ 'defaults_layer_mlm_filter_controlbox' ];
	} else {
		//prepare layer vars
		$id = intval($_GET['id']);
		$row = $wpdb->get_row('SELECT l.id as lid, l.name as lname, l.basemap as lbasemap, l.layerzoom as llayerzoom, l.mapwidth as lmapwidth, l.mapwidthunit as lmapwidthunit, l.mapheight as lmapheight, l.panel as lpanel, l.layerviewlat as llayerviewlat, l.layerviewlon as llayerviewlon, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon, l.controlbox as lcontrolbox, l.overlays_custom as loverlays_custom, l.overlays_custom2 as loverlays_custom2, l.overlays_custom3 as loverlays_custom3, l.overlays_custom4 as loverlays_custom4,l.wms as lwms, l.wms2 as lwms2, l.wms3 as lwms3, l.wms4 as lwms4, l.wms5 as lwms5, l.wms6 as lwms6, l.wms7 as lwms7, l.wms8 as lwms8, l.wms9 as lwms9, l.wms10 as lwms10, l.listmarkers as llistmarkers, l.multi_layer_map as lmulti_layer_map,l.multi_layer_map_list as multi_layer_map_list, l.address as laddress, l.clustering as lclustering, l.gpx_url as lgpx_url, l.gpx_panel as lgpx_panel, m.id as markerid, m.markername as markername, m.lat as mlat, m.lon as mlon, m.icon as micon, m.popuptext as mpopuptext, m.zoom as mzoom, m.mapwidth as mmapwidth, m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight, m.address as maddress, l.mlm_filter as mlm_filter, l.mlm_filter_details as filter_details  FROM `'.$table_name_layers.'` as l LEFT OUTER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id, ARRAY_A);
		$name = htmlspecialchars($row['lname']);
		$basemap = $row['lbasemap'];
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
		//info: get filters sort order from the settings.
		$filters_active_sort_order = ($lmm_options['mlm_filter_active_sort_order'] == 'DESC')?SORT_DESC:SORT_ASC;
		$filters_inactive_sort_order = ($lmm_options['mlm_filter_inactive_sort_order'] == 'DESC')?SORT_DESC:SORT_ASC;
		$layerzoom = $row['llayerzoom'];
		$mapwidth = $row['lmapwidth'];
		$mapwidthunit = $row['lmapwidthunit'];
		$mapheight = $row['lmapheight'];
		$layerviewlat = $row['llayerviewlat'];
		$layerviewlon = $row['llayerviewlon'];
		$markerid = $row['markerid'];
		$markername = htmlspecialchars($row['markername']);
		$mlat = $row['mlat'];
		$mlon = $row['mlon'];
		$coords = $mlat.', '.$mlon;
		$micon = esc_html($row['micon']);
		$popuptext = $row['mpopuptext'];
		$markerzoom = $row['mzoom'];
		$markermapwidth = $row['mmapwidth'];
		$markermapwidthunit = $row['mmapwidthunit'];
		$markermapheight = $row['mmapheight'];
		$panel = $row['lpanel'];
		$createdby = $row['lcreatedby'];
		$createdon = $row['lcreatedon'];
		$updatedby = $row['lupdatedby'];
		$updatedon = $row['lupdatedon'];
		$controlbox = $row['lcontrolbox'];
		$overlays_custom = $row['loverlays_custom'];
		$overlays_custom2 = $row['loverlays_custom2'];
		$overlays_custom3 = $row['loverlays_custom3'];
		$overlays_custom4 = $row['loverlays_custom4'];
		$wms = $row['lwms'];
		$wms2 = $row['lwms2'];
		$wms3 = $row['lwms3'];
		$wms4 = $row['lwms4'];
		$wms5 = $row['lwms5'];
		$wms6 = $row['lwms6'];
		$wms7 = $row['lwms7'];
		$wms8 = $row['lwms8'];
		$wms9 = $row['lwms9'];
		$wms10 = $row['lwms10'];
		$listmarkers = $row['llistmarkers'];
		$multi_layer_map = $row['lmulti_layer_map'];
		$filter_details =json_decode( $row['filter_details'], true );
		if($filter_details){
			//info: prepare html label for filters
			$filter_show_markercount = (isset($lmm_options['mlm_filter_controlbox_markercount']) && $lmm_options['mlm_filter_controlbox_markercount'] == '1')?'1':'0';
			$filter_show_icon = (isset($lmm_options['mlm_filter_controlbox_icon']) && $lmm_options['mlm_filter_controlbox_icon'] == '1')?'1':'0';
			$filter_show_name = (isset($lmm_options['mlm_filter_controlbox_name']) && $lmm_options['mlm_filter_controlbox_name'] == '1')?'1':'0';
			//info: in case all of the 3 attributes unchecked, display the name
			if($filter_show_markercount == '0' && $filter_show_icon == '0' && $filter_show_name == '0'){
				$filter_show_name = '1';
			}
			$active_layers_order  = $lmm_options['mlm_filter_active_sort_order'];
			$active_layers_orderby  = ($lmm_options['mlm_filter_active_orderby'] == 'id')?'layer_id':$lmm_options['mlm_filter_active_orderby'];
			$prepare_active_ordered_filters = array();
			foreach($filter_details as $key => $value){
				if($value['status'] == 'active'){
					$prepare_active_ordered_filters[$key] = $value;
					//info: get markercount for each filter
					$filter_details[$key]['markercount'] = intval($wpdb->get_var( "SELECT COUNT(*) FROM $table_name_markers WHERE layer LIKE concat('%\"',$key,'\"%')" ));
					$prepare_active_ordered_filters[$key]['markercount'] = intval($filter_details[$key]['markercount']);
					//info: escape filter's name and icon.
					$filter_details[$key]['name'] = stripslashes($filter_details[$key]['name']);
					$filter_details[$key]['icon'] = esc_url($filter_details[$key]['icon']);
				}
			}
			//info: order active layers
			if($lmm_options['mlm_filter_active_orderby'] == 'id'){
				if($filters_active_sort_order === SORT_DESC){
					krsort( $prepare_active_ordered_filters );
				}else{
					ksort( $prepare_active_ordered_filters );
				}
			}else{
				$prepare_active_ordered_filters = MMP_Globals::array_sort( $prepare_active_ordered_filters , $lmm_options['mlm_filter_active_orderby'], $filters_active_sort_order);
			}
			$prepare_inactive_ordered_filters = array();
			foreach($filter_details as $key => $value){
				if($value['status'] == 'inactive'){
					$prepare_inactive_ordered_filters[$key] = $value;
					//info: get markercount for each filter
					$filter_details[$key]['markercount'] =  intval($wpdb->get_var( "SELECT COUNT(*) FROM $table_name_markers WHERE layer LIKE concat('%\"',$key,'\"%')" ));
					$prepare_inactive_ordered_filters[$key]['markercount'] = intval($filter_details[$key]['markercount']);
					//info: escape filter's name and icon.
					$filter_details[$key]['name'] = stripslashes($filter_details[$key]['name']);
					$filter_details[$key]['icon'] = esc_url($filter_details[$key]['icon']);
				}
			}
			//info: order inactive layers
			if($lmm_options['mlm_filter_inactive_orderby'] == 'id'){
				if($filters_inactive_sort_order === SORT_DESC){
					krsort( $prepare_inactive_ordered_filters );
				}else{
					ksort( $prepare_inactive_ordered_filters );
				}
			}else{
				$prepare_inactive_ordered_filters = MMP_Globals::array_sort( $prepare_inactive_ordered_filters, $lmm_options['mlm_filter_inactive_orderby'], $filters_inactive_sort_order);
			}
			//info: combine active and inactive layers
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
		$multi_layer_map_list = $wpdb->get_var('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id='.$id);
		$multi_layer_map_list_exploded = explode(",", $wpdb->get_var('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id='.$id));
		if (empty($multi_layer_map_list)) {
			if($filter_details){
				$multi_layer_map_list = implode(',', array_keys($prepare_active_ordered_filters));
			}else{
				$multi_layer_map_list = $row['multi_layer_map_list'];
			}
		} else {
			$multi_layer_map_list = esc_sql($multi_layer_map_list);
		}
		$multi_layer_map_list_exploded = explode(",", $multi_layer_map_list);
		$non_filterbox_layers = array();
		if(!empty($multi_layer_map_list)){
			foreach($multi_layer_map_list_exploded as $layer_map){
				if ( ($layer_map != 'all') && (is_array($layer_map)) ) {
					if(!in_array($layer_map, array_keys($filter_details))){
						$non_filterbox_layers[$layer_map] =  intval($wpdb->get_var( "SELECT COUNT(*) FROM $table_name_markers WHERE layer LIKE concat('%\"',$layer_map,'\"%')" ));
					}
				}
 			}
		}
		$address = htmlspecialchars($row['laddress']);
		$clustering = $row['lclustering'];
		$gpx_url = esc_url($row['lgpx_url']);
		$mlm_filter_controlbox = $row['mlm_filter'];
		$gpx_panel = $row['lgpx_panel'];
		$check_mlm = $wpdb->get_results("SELECT id FROM `$table_name_markers` WHERE layer LIKE '%\"".$id."\"%'");
		$mlm_disabled = '';
		if($check_mlm){
			if($multi_layer_map == 0){
				$mlm_disabled = 'disabled="disabled"';
			}
		}
		if ( isset($row['mlm_filter']) && $row['mlm_filter'] == '1' ){ $filters_collapsed = 'true'; }elseif($row['mlm_filter'] == '2'){ $filters_collapsed = 'false'; }else{ $filters_collapsed = 'hidden'; }
		//info: markercount
		if ($multi_layer_map == 0) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id);
		} else 	if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == 'all' ) ) {
			$markercount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
		} else 	if ( ($multi_layer_map == 1) && ( $multi_layer_map_list != NULL ) && ($multi_layer_map_list != 'all') ) {
			foreach ($multi_layer_map_list_exploded as $mlmrowcount){
				$mlm_count_temp{$mlmrowcount} = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$mlmrowcount);
			}
			$markercount = array_sum($mlm_count_temp);
		} else 	if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == NULL ) ) {
			$markercount = 0;
		}
		//info: prepare translate URL
		if(MMP_Globals::check_multilingual() == 'wpml'){
			$translate_url_layername = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($name));
			$translate_url_layeraddress = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($address));
		}elseif(MMP_Globals::check_multilingual() == 'pll'){
			$translate_url_layername = admin_url('admin.php?page=mlang_strings&s='. urlencode($name) .'&group=Maps+Marker+Pro');
			$translate_url_layeraddress = admin_url('admin.php?page=mlang_strings&s='. urlencode($address) .'&group=Maps+Marker+Pro');
		}
		else{
			$translate_url_layername = 'https://www.mapsmarker.com/multilingual';
			$translate_url_layeraddress = 'https://www.mapsmarker.com/multilingual';
		}
	}

	//info: sqls for singe and multi-layer-maps
	if ($id == NULL) { //info: no mysql-query on new layer creation
		$layer_marker_list = NULL;
		$layer_marker_list_table = NULL;
	} else if ($multi_layer_map == 0) {
		//info: overwrite where statement for new layer maps (otherwise debug error sql statements $layer_marker_list and $layer_marker_list_table
		if ($id == '') { $sql_where = ''; } else { $sql_where = 'WHERE l.id=' . $id; }

		if( $lmm_options[ 'defaults_layer_listmarkers_show_distance_unit' ] == 'km' || $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.km' ){ //info: needed fallback as setting name has changed
			$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
			$layer_marker_list = $wpdb->get_results('SELECT '. $distance_query .' l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') ' . $sql_where . ' ORDER BY distance ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
		} else if ( $lmm_options[ 'defaults_layer_listmarkers_show_distance_unit' ] == 'mile' || $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.mile' ){
			$distance_query = " ( 3959 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
			$layer_marker_list = $wpdb->get_results('SELECT '. $distance_query .' l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') ' . $sql_where . ' ORDER BY distance ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
		} else {
			$layer_marker_list = $wpdb->get_results('SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') ' . $sql_where . ' ORDER BY ' . $lmm_options[ 'defaults_layer_listmarkers_order_by' ] . ' ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
		}
		$layer_marker_list_table = $wpdb->get_results('SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') ' . $sql_where . ' ORDER BY m.id ASC LIMIT ' . intval($lmm_options[ 'markers_per_page' ]), ARRAY_A);

	} else if ($multi_layer_map == 1) {

			$distance_query = '';
			if( $lmm_options[ 'defaults_layer_listmarkers_show_distance_unit' ] == 'km' ||  $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.km' ||  $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.mile' ){ //info: needed fallback as setting name has changed
				if( $lmm_options[ 'defaults_layer_listmarkers_show_distance_unit' ] == 'km' ||  $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.km' ){
					$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
				}else if( $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.mile' ){
					$distance_query = " ( 3959 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
				}
			}
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
				} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance_layer_center' || $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.km' || $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.mile') {
				$sort_order_mlm = 'distance';
				} else if ( $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance_current_position' || $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.km' || $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.mile') {
				$sort_order_mlm = 'distance';
			}

			if ( (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') && ($multi_layer_map_list != NULL) ) { //info: only 1 layer selected
				$mlm_query = "SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%')  WHERE l.id='" . $multi_layer_map_list . "' ORDER BY " . $sort_order_mlm . " " . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . " LIMIT " . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]);
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);

				$mlm_query_table = "(SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "`  as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $multi_layer_map_list . "')";
				$mlm_query_table .= " ORDER BY markerid ASC LIMIT " . intval($lmm_options[ 'markers_per_page' ]) . "";
				$layer_marker_list_table = $wpdb->get_results($mlm_query_table, ARRAY_A);
			} //info: end (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') && ($multi_layer_map_list != NULL)
			else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all') ) {
				$first_mlm_id = $multi_layer_map_list_exploded[0];
				$other_mlm_ids = array_slice($multi_layer_map_list_exploded,1);
				$mlm_query = "(SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON  m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $row . "')";
				}
				$mlm_query .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]) . "";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
				$mlm_query_table = "(SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query_table .= " UNION (SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $row . "')";
				}
				$mlm_query_table .= " ORDER BY markerid ASC LIMIT " . intval($lmm_options[ 'markers_per_page' ]) . "";
				$layer_marker_list_table = $wpdb->get_results($mlm_query_table, ARRAY_A);
			} //info: end else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all')
			else if ($multi_layer_map_list == 'all') {
				$first_mlm_id = '0';
				$mlm_all_layers = $wpdb->get_results( "SELECT id FROM $table_name_layers", ARRAY_A );
				$other_mlm_ids = array_slice($mlm_all_layers,1);
				$mlm_query = "(SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $row['id'] . "')";
				}
				$mlm_query .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]) . "";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);

				$mlm_query_table = "(SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query_table .= " UNION (SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $row['id'] . "')";

				}
				$mlm_query_table .= " ORDER BY markerid ASC LIMIT " . intval($lmm_options[ 'markers_per_page' ]) . "";
				$layer_marker_list_table = $wpdb->get_results($mlm_query_table, ARRAY_A);
			} //info: end else if ($multi_layer_map_list == 'all')
			else { //info: if ($multi_layer_map == 1) but no layers selected
				$layer_marker_list = NULL;
				$layer_marker_list_table = array();
			}
	} //info: end main - else if ($multi_layer_map == 1)

	//info: check if user is allowed to view layer - part 1
	if (!MMP_Globals::check_capability('view_others', $createdby)) {
		echo '<div class="notice notice-error" style="padding: 10px;">' . __('Error: your user does not have the permission to view layers from other users!','lmm') . '</div>';
	} else {

	//info: check if layer exists - part 1
	if ($layerviewlat === NULL) {
		$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), htmlspecialchars($_GET['id']));
		echo '<p><div class="notice notice-error" style="padding:10px;">' . $error_layer_not_exists . '</div></p>';
		echo '<p><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('list all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
	} else {
		$edit_status = isset($_GET['status']) ? $_GET['status'] : '';
		if ( $edit_status == 'updated') {
			echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully updated','lmm') . '</div>';
		} else if ( $edit_status == 'published') {
			echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully published','lmm') . '</div>';
		} else if ( $edit_status == 'duplicated') {
			echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully duplicated','lmm') . '</div>';
		}
		$nonce= wp_create_nonce('layer-nonce');
		//info: filter details js variables initialization
		if(isset($filter_details) && $filter_details){
			echo '<script>'.PHP_EOL;
			echo 'var ordered_filter_details = '. json_encode($ordered_filters) .';'.PHP_EOL;
			echo 'var filter_details = '. json_encode($filter_details) .';'.PHP_EOL;
			echo 'var called_layers = [];'.PHP_EOL;
			echo 'var active_layers = [];'.PHP_EOL;
			echo 'var filtered_layers = [];'.PHP_EOL;
			echo 'var non_filterbox_layers = '. json_encode($non_filterbox_layers) .';'.PHP_EOL;
			echo 'var active_layers_order = "'.$active_layers_order.'";'.PHP_EOL;
			echo 'var active_layers_orderby = "'.$active_layers_orderby.'";'.PHP_EOL;
			echo 'var filter_show_markercount = "'.$filter_show_markercount.'";'.PHP_EOL;
			echo 'var filter_show_icon = "'.$filter_show_icon.'";'.PHP_EOL;
			echo 'var filter_show_name = "'.$filter_show_name.'";'.PHP_EOL;
			//info: whether the controlbox of filters is collapsed, opened, or hidden.
			$filters_collapsed_option = ($filters_collapsed!= 'hidden')?'"collapsed":'.$filters_collapsed.',':'';
			echo 'var filters_options = {"position":"'.$lmm_options['mlm_filter_controlbox_position'].'",'.$filters_collapsed_option.'};'.PHP_EOL;
			echo '</script>'.PHP_EOL;
		} ?>
		<form id="layer-add-edit" method="post">
		<?php wp_nonce_field('layer-nonce'); ?>
		<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
		<input type="hidden" id="action-layer-add-edit" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
		<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
		<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $overlays_custom ?>" />
		<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $overlays_custom2 ?>" />
		<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $overlays_custom3 ?>" />
		<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $overlays_custom4 ?>" />
		<input type="hidden" id="active_editor" name="active_editor" value="<?php echo $current_editor ?>" />
		<!-- default texts for AJAX-->
		<input type="hidden" id="defaults_texts_add_new_marker" value="<?php echo __('Add new marker','lmm'); ?>" />
		<input type="hidden" id="defaults_texts_publish" value="<?php echo __('publish','lmm'); ?>" />
		<input type="hidden" id="defaults_texts_update" value="<?php echo __('update','lmm'); ?>" />
		<input type="hidden" id="defaults_texts_panel_text" value="<?php echo __('if set, layername will be displayed here','lmm'); ?>" />

		<div id="lmm_ajax_results_top" class="updated" style="padding:10px;display:none;"></div>

		<div id="div-layer-editor-hide-on-ajax-delete" style="clear:both;">
		<?php
			if ($current_editor == 'simplified') {
				echo '<div id="switch-link-visible" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-advanced-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to advanced editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
				echo '<div id="switch-link-hidden" class="switch-link-rtl" style="display:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-simplified-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to simplified editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
			} else {
				echo '<div id="switch-link-visible" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-simplified-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to simplified editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
				echo '<div id="switch-link-hidden" class="switch-link-rtl" style="display:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-advanced-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to advanced editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
			}
		?>
		<h1 style="margin:10px 0 0 0;"><span id="layer-heading"><?php ($isedit === true) ? _e('Edit layer','lmm') : _e('Add new layer','lmm') ?>
		<?php
			if ($isedit === true) {	echo ' "' . stripslashes($name) . '" (ID '.$id.')'; }
			echo '</span>'; ?>
		</h1>
		<table class="layer_buttons_table">
		<tr style="display:block;padding-bottom:5px;">
			<td>
		<?php
			if (MMP_Globals::check_capability('edit', $createdby)) {
				if ($isedit === true) { $button_text = __('update','lmm'); } else { $button_text = __('publish','lmm'); }
				echo '<div class="submit"><input id="submit_top" style="font-weight:bold;" type="submit" name="layer" class="button button-primary" value="' . $button_text . '" />';
				echo '<img src="' . admin_url('/images/wpspin_light.gif') . '" class="waiting" id="lmm_ajax_loading_top" style="margin-left:5px; display:none;"/></div>';
			} else {
				if ($isedit === true) {
					echo __('Your user does not have the permission to add a new layer!','lmm');
				} else {
					echo __('Your user does not have the permission to update this layer!','lmm');
				}
			}
		?>
		</form>

		</td>
		<td class="hide_on_new">
		<?php
			if (MMP_Globals::check_capability('edit', $createdby)) {
				$multi_layer_map_edit_button_visibility = ($multi_layer_map == 0) ? 'display:block;' : 'display:none;';
				echo '<a class="button button-secondary addmarker_link button-add-new-marker-to-this-layer" style="font-size:13px;margin-left:15px;text-decoration:none;' . $multi_layer_map_edit_button_visibility . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $oid . '">' . __('add new marker to this layer','lmm') . '</a>';
			}
		?>
		</td>
		<td class="hide_on_new">
			<?php
			if (MMP_Globals::check_capability('delete', $createdby)) {
				echo '<form method="post" style="margin-bottom:0px;">';
				wp_nonce_field('layer-nonce');
				echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="delete" />';
				$confirm = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s)?','lmm'), $name, $id);
				echo '<a id="delete_button_top" href="javascript:void(0);" id="layer-delete" class="button button-secondary" style="font-size:13px;text-decoration:none;color:#FF0000;margin-left:20px;">' . __('delete layer only', 'lmm') . '</a>';
				echo '</form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to delete this layer!','lmm') . '</span>';
			}
			?>
		</td>
		<td class="hide_on_new">
			<span class="show-advanced-layer-edit-buttons" style="display:inline;"><a style="cursor:pointer;" title="<?php esc_attr_e('show advanced functions','lmm'); ?>">>>></a></span>
			<span class="hide-advanced-layer-edit-buttons" style="display:none;"><a style="cursor:pointer;" title="<?php esc_attr_e('hide advanced functions','lmm'); ?>"><<<</a></span>
		</td>
		<td class="hide_on_new advanced-layer-edit-button" style="display:none;">
		<?php
			if (MMP_Globals::check_capability('edit', $createdby)) {
				echo '<form method="post" style="margin-bottom:0px;">';
				wp_nonce_field('layer-nonce');
				echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="duplicate" />';
				echo '<a id="duplicate_button_top" href="javascript:void(0);" class="button button-secondary" style="font-size:13px;text-decoration:none;">' . __('duplicate layer only', 'lmm') . '</a>';
				echo '</form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to duplicate this layer!','lmm') . '</span>';
			}
		?>
		</td>
		<td class="hide_on_new advanced-layer-edit-button" style="display:none;">
			<?php
			if ($multi_layer_map == 0) {
				if (MMP_Globals::check_capability('edit', $createdby)) {
					echo '<form method="post" style="margin-bottom:0px;">';
					wp_nonce_field('layer-nonce');
					echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
					echo '<input type="hidden" name="action" value="duplicatelayerandmarkers" />';
					echo '<input class="button button-secondary" style="margin-left:20px;" type="submit" name="layer" value="' . __('duplicate layer and assigned markers', 'lmm') . '" />';
					echo '</form>';
				} else {
					echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to duplicate this layer!','lmm') . '</span>';
				}
			}
		?>
		</td>
		<td class="hide_on_new advanced-layer-edit-button" style="display:none;">
			<?php
			if (MMP_Globals::check_capability('delete', $createdby)) {
				echo '<form method="post" style="margin-bottom:0px;">';
				wp_nonce_field('layer-nonce');
				echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="deleteboth" />';
				$confirm2 = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s) and all assigned markers? (if a marker is assigned to multiple layers only the reference to the layer will be removed)','lmm'), $name, $id);
				if ($multi_layer_map == 0) {
					echo "<input id='delete_layer_and_markers' class='button button-secondary' style='color:#FF0000;margin-left:20px;' type='submit' name='layer' value='" . __('delete layer AND assigned markers', 'lmm') . "' onclick='return confirm(\"".$confirm2 ."\")' />";
				}
				echo '</form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to delete this layer and all assigned markers!','lmm') . '</span>';
			}
			?>
		</td>

		</tr>
		</table>
		<p style="margin-top:0px;">
		<?php echo sprintf(__('To display multiple locations at the same time, create a layer first (e.g. "company locations") and then add markers (e.g. "headquarters", "store A", "store B") assigned to that layer.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer'); ?>
		</p>
		<table class="widefat" style="border-radius:5px;">
				<?php if ($isedit === true) { $shortcode_visibility = 'table-row'; } else { $shortcode_visibility = 'none'; }?>
					<tr id="tr-shortcode" style="display:<?php echo $shortcode_visibility; ?>;">
						<td style="width:230px;" class="lmm-border"><label for="shortcode"><strong><?php _e('Shortcode and API links','lmm') ?></strong></label></td>
						<td class="lmm-border"><input id="shortcode" style="width:206px;background:#f3efef;" type="text" value="[<?php echo htmlspecialchars($lmm_options[ 'shortcode' ]); ?> layer=&quot;<?php echo $id?>&quot;]" <?php echo $shortcode_select; ?>>
						<?php
							if ($current_editor == 'simplified') {
								echo '<div id="apilinkstext" style="display:inline;"><a tabindex="123" style="cursor:pointer;">' . __('show API links','lmm') . '</a></div>';
							}
							echo '<span id="apilinks" style="' . $current_editor_css_inline . '">';
							echo '<a id="shortcode-link-kml" tabindex="125" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/layer/' . $id . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /> KML</a> <a tabindex="126" href="https://www.mapsmarker.com/kml" target="_blank" title="' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '"/></a>';
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-fullscreen" tabindex="127" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" /> ' . __('Fullscreen','lmm') . '</a> <span title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"/></span>';
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-qr" tabindex="128" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/layer/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" /> ' . __('QR code','lmm') . '</a> <span title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"/></span>';
							if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; }
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-geojson" tabindex="129" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $geojson_api_link . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" /> GeoJSON</a> <a tabindex="130" href="https://www.mapsmarker.com/geojson" target="_blank" title="' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '"/></a>';
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-georss" tabindex="131" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" /> GeoRSS</a> <a tabindex="132" href="https://www.mapsmarker.com/georss" target="_blank" title="' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '"/></a>';
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-wikitude" tabindex="133" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/layer/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" /> Wikitude</a> <a tabindex="134" href="https://www.mapsmarker.com/wikitude" target="_blank" title="' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '"/></a>';
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="134" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_apis"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-page.png" width="16" height="16" alt="' . esc_attr__('Settings','lmm') . '" /> Maps Marker API</a>';
							echo '</span>';
						?>
						<br/>
						<small>
						<?php _e('Use this shortcode in posts or pages on your website or one of the API links for embedding in external websites or apps','lmm'); ?>.<br/>
						<?php echo sprintf(__('Tip: highlight an assigned marker by adding the marker ID as shortcode attribute (e.g. %1$s) or add %2$s to the URL where the map is embedded','lmm'), '[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' layer="' . $id . '" <strong>highlightmarker="1"</strong>]', '<strong>?highlightmarker=1</strong>'); ?>
						</small>
							</td>
			</tr>
			<?php if ($isedit === true) { $used_in_content_visibility = 'table-row'; } else { $used_in_content_visibility = 'none'; }?>
			<tr id="tr-usedincontent" style="display:<?php echo $used_in_content_visibility; ?>;">
				<td style="width:230px;" class="lmm-border"><strong><?php _e('Used in content','lmm') ?></strong></td>
				<td class="lmm-border"><?php echo MMP_Globals::get_map_shortcodes($id, 'layer'); ?></td>
			</tr>
			<tr>
				<td style="width:230px;" class="lmm-border"><label for="layername"><strong><?php _e('Layer name', 'lmm') ?></strong></label></td>
				<td class="lmm-border"><input autofocus="autofocus" style="width: 640px;" maxlenght="255" type="text" id="layername" name="name" value="<?php echo stripslashes($name) ?>" /> <?php if ($isedit === true) { ?>  <span class="wpml-layertranslatelink wpml-layername">(<a target="_blank" href="<?php echo $translate_url_layername; ?>"><?php _e('translate', 'lmm'); ?></a>) </span> <?php }else{  ?> <span class="wpml-layertranslatelink wpml-layername">  (<a target="_blank" href="<?php echo $translate_url_layername; ?>"><?php _e('translate', 'lmm'); ?></a>) </span>  <?php } ?></td>
			</tr>
			<tr>
				<td class="lmm-border">
					<label for="address"><strong><?php _e('Location','lmm') ?></strong></label>
					<br/>
					<?php
					//info: to prevent PHP warnings
					$geocoding_provider_selected_mapzen = '';
					$geocoding_provider_selected_algolia = '';
					$geocoding_provider_selected_photon = '';
					$geocoding_provider_selected_mapquest = '';
					$geocoding_provider_selected_google = '';

					if ($lmm_options['geocoding_provider'] == 'mapzen-search') {
						$geocoding_provider_settings_internal_link = '#lmm-geocoding-mapzen';
						$geocoding_provider_selected_mapzen = 'selected';
						$mapzen_rate_limit = '30.000';
						$mapzen_rate_limit_period = __('day','lmm');
						$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': <span id="mapzen-rate-limit-static" style="display:inline;">' . sprintf(__('%1$s requests/%2$s','lmm'), $mapzen_rate_limit, $mapzen_rate_limit_period) . '</span><span id="mapzen-rate-limit-dynamic" style="display:none;">' . sprintf(__('%1$s out of %2$s requests/%3$s','lmm'), '<span id="mapzen-rate-limit-consumed"></span>', $mapzen_rate_limit, $mapzen_rate_limit_period) . '</span>';

					} else if ($lmm_options['geocoding_provider'] == 'algolia-places') {
						$geocoding_provider_settings_internal_link = '#lmm-geocoding-algolia';
						$geocoding_provider_selected_algolia = 'selected';
						if ( ($lmm_options['geocoding_algolia_appId'] != NULL) && ($lmm_options['geocoding_algolia_apiKey'] != NULL) ){
							$algolia_rate_limit = '100.000';
							$algolia_rate_limit_period = __('month','lmm');
						} else {
							$algolia_rate_limit = '1.000';
							$algolia_rate_limit_period = __('day','lmm');
						}
						$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/domain/%2$s','lmm'), $algolia_rate_limit, $algolia_rate_limit_period);

					} else if ($lmm_options['geocoding_provider'] == 'photon') {
						$geocoding_provider_settings_internal_link = '#lmm-geocoding-photon';
						$geocoding_provider_selected_photon = 'selected';
						$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': <span id="photon-rate-limit-static" style="display:inline;">' . sprintf(__('%1$s requests/domain/%2$s','lmm'), '2.500', __('day','lmm')) . '</span><span id="photon-rate-limit-dynamic" style="display:none;">' . sprintf(__('%1$s out of %2$s requests/%3$s','lmm'), '<span id="photon-rate-limit-consumed"></span>', '<span id="photon-rate-limit"></span>', __('day','lmm')) . '</span>';

					} else  if ($lmm_options['geocoding_provider'] == 'mapquest-geocoding') {
						$geocoding_provider_settings_internal_link = '#lmm-geocoding-mapquest';
						$geocoding_provider_selected_mapquest = 'selected';
						$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '15.000', __('month','lmm'));

					} else if ($lmm_options['geocoding_provider'] == 'google-geocoding') {
						$geocoding_provider_settings_internal_link = '#lmm-geocoding-google';
						$geocoding_provider_selected_google = 'selected';
						$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '1.000', __('day','lmm'));
					}
					//info: Get all the limits for each provider
					$geocoding_limits = array();

					$mapzen_rate_limit = '30.000';
					$mapzen_rate_limit_period = __('day','lmm');
					$geocoding_limits['mapzen'] = __('Rate limit','lmm') . ': <span id="mapzen-rate-limit-static" style="display:inline;">' . sprintf(__('%1$s requests/%2$s','lmm'), $mapzen_rate_limit, $mapzen_rate_limit_period) . '</span><span id="mapzen-rate-limit-dynamic" style="display:none;">' . sprintf(__('%1$s out of %2$s requests/%3$s','lmm'), '<span id="mapzen-rate-limit-consumed"></span>', $mapzen_rate_limit, __('day','lmm')) . '</span>';

					if ( ($lmm_options['geocoding_algolia_appId'] != NULL) && ($lmm_options['geocoding_algolia_apiKey'] != NULL) ){
						$algolia_rate_limit = '100.000';
						$algolia_rate_limit_period = __('month','lmm');
						$geocoding_limits['algolia']  = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), $algolia_rate_limit, $algolia_rate_limit_period);
					} else {
						$algolia_rate_limit = '1.000';
						$algolia_rate_limit_period = __('day','lmm');
						$geocoding_limits['algolia']  = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/domain/%2$s','lmm'), $algolia_rate_limit, $algolia_rate_limit_period);
					}
					$geocoding_limits['photon'] = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s out of %2$s requests/domain/%3$s','lmm'), '<span id="photon-rate-limit-consumed">?</span>', '<span id="photon-rate-limit">2.500</span>', __('day','lmm'));
					$geocoding_limits['mapquest'] = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '15.000', __('month','lmm'));
					$geocoding_limits['google'] = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '1.000', __('day','lmm'));

					//info: check if Mapzen Search API key is set
					$geocoding_provider_mapzen_disabled = '';
					if ($lmm_options['geocoding_mapzen_search_api_key'] == NULL) {
						$option_mapzen_inactive = '<optgroup label="' . esc_attr__('Inactive (API key required)','lmm') . '"><option value="mapzen_geocoding" ' . $geocoding_provider_selected_mapzen . ' disabled="disabled">Mapzen Search (' . __('recommended','lmm') . ')</option></optgroup>';
						$option_mapzen_active = '';
					} else {
						$option_mapzen_active = '<option value="mapzen_geocoding" ' . $geocoding_provider_selected_mapzen . '>Mapzen Search</option>';
						$option_mapzen_inactive = '';
					}

					//info: check if MapQuest API key is set
					$geocoding_provider_mapquest_disabled = '';
					if ($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL) {
						$option_mapquest_inactive = '<optgroup label="' . esc_attr__('Inactive (API key required)','lmm') . '"><option value="mapquest_geocoding" ' . $geocoding_provider_selected_mapquest . ' disabled="disabled">MapQuest Geocoding</option></optgroup>';
						$option_mapquest_active = '';
					} else {
						$option_mapquest_active = '<option value="mapquest_geocoding" ' . $geocoding_provider_selected_mapquest . '>MapQuest Geocoding</option>';
						$option_mapquest_inactive = '';
					}

					//info: check if Google Geocoding API key is set
					$geocoding_provider_google_disabled = '';
					if
					(
						( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] == NULL) )
						||
						( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] == NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] == NULL)) )
					) {
						$option_google_inactive = '<optgroup label="' . esc_attr__('Inactive (API key required)','lmm') . '"><option value="google_geocoding" ' . $geocoding_provider_selected_google . ' disabled="disabled">Google Geocoding</option></optgroup>';
						$option_google_active = '';
					} else {
						$option_google_active = '<option value="google_geocoding" ' . $geocoding_provider_selected_google . '>Google Geocoding</option>';
						$option_google_inactive = '';
					}

					echo '<small><a tabindex="136" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#geocoding" title="' . esc_attr__('click to change default geocoding provider','lmm') . '">' . __('Geocoding provided by','lmm') . '</a>:';
					if (current_user_can('activate_plugins')) {
						echo '<div style="float:right;display:inline-block;"><a id="geocoding-provider-settings-link" title="' . esc_attr__('click to change current geocoding provider settings','lmm') . '" tabindex="136" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings' . $geocoding_provider_settings_internal_link . '">' . __('Settings','lmm') . '</a></div>';
					}
					echo '</small>
					<select tabindex="122" id="geocoding-provider-select" style="width: 100%">
					<optgroup label="' . esc_attr__('Available providers','lmm') . '">
						<option value="algolia_places" ' . $geocoding_provider_selected_algolia . '>Algolia Places</option>
						<option value="photon" ' . $geocoding_provider_selected_photon . '>Photon@MapsMarker</option>
						' . $option_mapzen_active  . '
						' . $option_mapquest_active  . '
						' . $option_google_active  . '
					</optgroup>
					' . $option_mapzen_inactive  . '
					' . $option_mapquest_inactive  . '
					' . $option_google_inactive  . '
					</select>';

					echo '<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery("#geocoding-provider-select").select2({
								minimumResultsForSearch: Infinity
							});
							jQuery("#geocoding-provider-select").on("change", function (e) {
								var new_geocoding_provider = jQuery("#geocoding-provider-select").val();
								jQuery("#address").places_autocomplete("destroy");
								jQuery("#address").off("autocomplete:selected");
								jQuery("head").find("link[rel=preconnect]").remove();
								if (new_geocoding_provider == "mapzen_search") {
									jQuery("#geocoding-provider-status").html("&nbsp;");
									jQuery("head").append("<link rel=\"preconnect\" href=\"https://search.mapzen.com\" crossorigin />");
									jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapzen");';
									if (($isedit) && ($multi_layer_map == 0)) {
										echo "var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}else{
										echo "var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}
									echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['mapzen'].'\');';
									echo 'mapzen_search.init();
								} else if (new_geocoding_provider == "algolia_places") {
									jQuery("#geocoding-provider-status").html("&nbsp;");
									jQuery("head").append("<link rel=\"preconnect\" href=\"https://places-dsn.algolia.net\" crossorigin />");
									jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-algolia");';
									if (($isedit) && ($multi_layer_map == 0)) {
										echo "var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}else{
										echo "var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}
									echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['algolia'].'\');';
									echo 'algolia_places.init();
								} if (new_geocoding_provider == "photon") {
									jQuery("#geocoding-provider-status").html("&nbsp;");
									jQuery("head").append("<link rel=\"preconnect\" href=\"https://photon.mapsmarker.com\" crossorigin />");
									jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-photon");';
									if (($isedit) && ($multi_layer_map == 0)) {
										echo "var photon = new MMP_Geocoding('photon', geocoding_options, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}else{
										echo "var photon = new MMP_Geocoding('photon', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}
									echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['photon'].'\');';
									echo 'photon.init();
								} else if (new_geocoding_provider == "mapquest_geocoding") {
									jQuery("#geocoding-provider-status").html("<a href=\"https://www.mapsmarker.com/mapquest-api-key\" tabindex=\"150\" target=\"_blank\">' . __('Please note that MapQuest basemaps should also be used if MapQuest geocoding is selected!','lmm') . '</a>");
									jQuery("head").append("<link rel=\"preconnect\" href=\"https://www.mapquestapi.com\" crossorigin />");
									jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapquest");';
									if (($isedit) && ($multi_layer_map == 0)) {
										echo "var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}else{
										echo "var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}
									echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['mapquest'].'\');';
									echo 'mapquest_geocoding.init();
								} else if (new_geocoding_provider == "google_geocoding") {
									jQuery("#geocoding-provider-status").html("&nbsp;");
									jQuery("head").append("<link rel=\"preconnect\" href=\"https://maps.googleapis.com\" crossorigin />");
									jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-google");';
									if (($isedit) && ($multi_layer_map == 0)) {
										echo "var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}else{
										echo "var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}
									echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['google'].'\');';
									echo 'google_geocoding.init();
								}
							});'.PHP_EOL;

							//info: preconnect geocoding providers (DNS, TCP, TLS)
							echo 'jQuery("head").find("link[rel=preconnect]").remove();'.PHP_EOL;
							if ($lmm_options['geocoding_provider'] == 'mapzen-search') {
								echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://search.mapzen.com\" crossorigin />");';
							} else if ($lmm_options['geocoding_provider'] == 'algolia-places') {
								echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://places-dsn.algolia.net\" crossorigin />");';
							} else if ($lmm_options['geocoding_provider'] == 'photon') {
								echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://photon.mapsmarker.com\" crossorigin />");';
							} else if ($lmm_options['geocoding_provider'] == 'mapquest-geocoding') {
								echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://www.mapquestapi.com\" crossorigin />");';
							} else if ($lmm_options['geocoding_provider'] == 'google-geocoding') {
								echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://maps.googleapis.com\" crossorigin />");';
							}
						echo'});
					</script>';
					echo '<small><span id="geocoding-rate-limit-details">' . $geocoding_provider_rate_limit_details . '</span></small>';
					?>
				</td>
				<td class="lmm-border">
					<?php
					if
					(
						( ($lmm_options['geocoding_provider'] == 'google-geocoding') || ($lmm_options['geocoding_provider_fallback'] == 'google-geocoding')	)
						&&
						(
							( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] == NULL) )
							||
							( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] == NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] == NULL)) )
						)
					) {
						$geocoding_provider_status_text = sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-google', 'Google Geocoding', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
						$location_input_css = 'background:#ff9999;';
					} else if (($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL) && ($lmm_options['geocoding_provider'] == 'mapquest-geocoding')) {
						$geocoding_provider_status_text = sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapquest', 'MapQuest Geocoding', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
						$location_input_css = 'background:#ff9999;';
					} else if (($lmm_options['geocoding_mapquest_geocoding_api_key'] != NULL)  && ($lmm_options['geocoding_provider'] == 'mapquest-geocoding')) {
						$geocoding_provider_status_text = '<a href="https://www.mapsmarker.com/mapquest-api-key" tabindex="150" target="_blank">' . __('Please note that MapQuest basemaps should also be used if MapQuest geocoding is selected!','lmm') . '</a>';
						$location_input_css = '';
					} else if (($lmm_options['geocoding_mapzen_search_api_key'] == NULL)  && ($lmm_options['geocoding_provider'] == 'mapzen-search')) {
						$geocoding_provider_status_text = sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapzen', 'Mapzen Search', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
						$location_input_css = 'background:#ff9999;';
					} else if ($lmm_options['geocoding_provider'] == $lmm_options['geocoding_provider_fallback']) {
						$geocoding_provider_status_text = sprintf(__('Warning: you did not configure an <a href="%1$s">alternative geocoding provider</a> - no fallback will be available if main geocoding provider is unavailable!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
						$location_input_css = 'background:yellow;';
					} else {
						$geocoding_provider_status_text = '&nbsp;';
						$location_input_css = '';
					}
					echo '<div id="geocoding-provider-status" style="font-size:11px;">' . $geocoding_provider_status_text . '</div>';
					$placeholder_text = ( (intval($lmm_options['geocoding_min_chars_search_autostart']) == 0) || (intval($lmm_options['geocoding_min_chars_search_autostart']) == 1) ) ? esc_attr__('Please enter a location','lmm') : sprintf(esc_attr__('Please enter a location (%1$s characters minimum to start typeahead suggestions)','lmm'), intval($lmm_options['geocoding_min_chars_search_autostart']));
					echo '<input style="margin:0px;width:640px;' . $location_input_css . '" type="text" id="address" name="address" value="' . stripslashes(htmlspecialchars($address)) . '" placeholder="' .  $placeholder_text . '" />';
					?>
					<?php if ($isedit === true) { ?>  <span class="wpml-layertranslatelink wpml-layeraddress">(<a target="_blank" href="<?php echo $translate_url_layeraddress; ?>"><?php _e('translate', 'lmm'); ?></a>) </span> <?php }else{  ?> <span class="wpml-layertranslatelink wpml-layeraddress">  (<a target="_blank" href="<?php echo $translate_url_layeraddress; ?>"><?php _e('translate', 'lmm'); ?></a>) </span>  <?php } ?>
					<div id="toggle-coordinates" style="clear:both;margin-top:5px;<?php echo $current_editor_css; ?>">
					<?php echo __('or paste coordinates here','lmm') . ' - '; ?>
					<?php _e('latitude','lmm') ?>: <input style="width: 100px;" type="text" id="layerviewlat" name="layerviewlat" value="<?php echo $layerviewlat; ?>" />
					<?php _e('longitude','lmm') ?>: <input style="width: 100px;" type="text" id="layerviewlon" name="layerviewlon" value="<?php echo $layerviewlon; ?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="lmm-border"><p>
				<strong><?php _e('Map size','lmm') ?></strong><br/>
				<label for="mapwidth"><?php _e('Width','lmm') ?>:</label>
				<input size="3" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo $mapwidth ?>" style="margin-left:5px;" />
				<input id="mapwidthunit_px" class="mapwidthunit" type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>><label for="mapwidthunit_px" title="<?php esc_attr_e('pixel','lmm'); ?>">px</label>&nbsp;&nbsp;&nbsp;
				<input id="mapwidthunit_percent" class="mapwidthunit" type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>><label for="mapwidthunit_percent">%</label><br/>
				<label for="mapheight"><?php _e('Height','lmm') ?>:</label>
				<input size="3" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" /> <span title="<?php esc_attr_e('pixel','lmm'); ?>">px</span>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<label for="layerzoom"><strong><?php _e('Zoom','lmm') ?></strong> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('You can also change zoom level by clicking on + or - on preview map or using your mouse wheel'); ?>" width="12" height="12" border="0"/></label>&nbsp;<input id="layerzoom" style="width:40px;" type="text" id="layerzoom" name="layerzoom" value="<?php echo $layerzoom ?>" />
				<small>
				<?php
				echo '<span id="toogle-global-maximum-zoom-level" style="' . $current_editor_css_inline . '"><br/>' . __('Global maximum zoom level','lmm') . ': ';
				if (current_user_can('activate_plugins')) {
					echo '<a title="' . esc_attr__('If the native maximum zoom level of a basemap is lower, tiles will be upscaled automatically.','lmm') . '" tabindex="111" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-default_basemaps">' . intval($lmm_options['global_maxzoom_level']) . '</a>';
				} else {
					echo intval($lmm_options['global_maxzoom_level']);
				}
				?>
				</span>
				</small>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<strong><label for="listmarkers"><?php _e('Show list of markers below map','lmm') ?></label></strong>&nbsp;<input type="checkbox" name="listmarkers" id="listmarkers" <?php checked($listmarkers, 1 ); ?>><br/>
				<?php
						echo '<small>';
						_e('Default number of markers for paging:','lmm');
						echo ' ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]);
						if (current_user_can('activate_plugins')) {
							echo ' <span id="toggle-listofmarkerssettings" style="' . $current_editor_css_inline . '"><a tabindex="113" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-list_of_markers">(' . __('Settings','lmm') . ')</a></span>';
						}
						echo '</small>';
				?>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<label for="clustering"><strong><?php _e('Marker clustering','lmm') ?></strong></label>&nbsp;&nbsp;<input type="checkbox" name="clustering" id="clustering" <?php checked($clustering, 1 ); ?>>
				<?php if (current_user_can('activate_plugins')) {
					echo '<span id="toggle-clustersettings" style="' . $current_editor_css_inline . '">&nbsp;&nbsp;<small>(<a tabindex="115" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-clustering">' . __('Settings','lmm') . '</a>)</small></span>';
				} ?>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<div style="float:right;"><label for="gpx_panel"><?php _e('display panel','lmm') ?></label>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="gpx_panel" id="gpx_panel" <?php checked($gpx_panel, 1 ); ?>></div>
				<label for="gpx_url"><strong><?php _e('URL to GPX track','lmm') ?></strong></label><br/>
				<?php
					if ($gpx_url != NULL) {

						//info: load gpx_content
						$gpx_content_array = wp_remote_get( $gpx_url, array( 'sslverify' => false, 'timeout' => 30 ) );

						if (is_wp_error($gpx_content_array)) {
							echo '<div class="notice notice-error" style="padding:10px;">' . sprintf(__('The GPX file could not be loaded due to the following error:<br/>%s!','lmm'), $gpx_content_array->get_error_message()) . '</div>';
						} else if ($gpx_content_array['response']['code'] == '404') {
							echo '<div class="gpx-error-notice">' . __('Error', 'lmm') . ' ' . $gpx_content_array['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), '<a href="' . $gpx_url . '" target="_blank">' . $gpx_url . '</a>') . '</div>';
							echo '<div class="notice notice-error" style="padding:10px;">' . __('Error', 'lmm') . ' ' . $gpx_content_array['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), '<a href="' . $gpx_url . '" target="_blank">' . $gpx_url . '</a>') . '</div>';
						}
					}
				?>
				<input style="width:229px;" type="text" id="gpx_url" name="gpx_url" value="<?php echo $gpx_url ?>" /><br/>
				<?php if (current_user_can('upload_files')) { echo '<small><span style="color:#21759B;cursor:pointer;" onMouseOver="this.style.color=\'#D54E21\'" onMouseOut="this.style.color=\'#21759B\'" id="upload_gpx_file">' . __('add','lmm') . '</span> |'; } ?>
				<a tabindex="117" href="https://www.mapsmarker.com/gpx-convert" target="_blank" title="<?php esc_attr_e('Click here for a tutorial on how to convert a non-GPX-track file into a GPX track file','lmm'); ?>"><?php _e('convert','lmm'); ?></a> |
				<a tabindex="118" href="https://www.mapsmarker.com/gpx-merge" target="_blank" title="<?php esc_attr_e('Click here for a tutorial on how to merge multiple GPX-track files into one GPX track file','lmm'); ?>"><?php _e('merge','lmm'); ?></a>
				<?php if (current_user_can('activate_plugins')) { echo ' | <a tabindex="116" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-gpx">' . __('settings','lmm') . '</a>'; } ?>
				<?php if ($gpx_url != NULL) { $fitbounds_css = 'display:inline;'; } else { $fitbounds_css = 'display:none;'; }
				echo '<span id="gpx_fitbounds_link" style="color:#21759B;cursor:pointer;' . $fitbounds_css . '" onMouseOver="this.style.color=\'#D54E21\'" onMouseOut="this.style.color=\'#21759B\'" class="gpxfitbounds"> | ' . __('fit bounds','lmm') . '</small></span>'; ?>
				</p>
				<div id="toggle-controlbox-panel-kmltimestamp-backlinks-minimaps" style="<?php echo $current_editor_css; ?>">
				<p>
				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
				<strong><?php _e('Controlbox for basemaps/overlays','lmm') ?>:</strong><br/>
				<input style="margin-top:1px;" id="controlbox_hidden" class="layer-controlbox" type="radio" name="controlbox" value="0" <?php checked($controlbox, 0); ?>><label for="controlbox_hidden"><?php _e('hidden','lmm') ?></label><br/>
				<input style="margin-top:1px;" id="controlbox_collapsed" class="layer-controlbox" type="radio" name="controlbox" value="1" <?php checked($controlbox, 1); ?>><label for="controlbox_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
				<input style="margin-top:1px;" id="controlbox_expanded" class="layer-controlbox" type="radio" name="controlbox" value="2" <?php checked($controlbox, 2); ?>><label for="controlbox_expanded"><?php _e('expanded','lmm') ?></label>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<strong><label for="panel"><?php _e('Display panel','lmm') ?></label></strong>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="panel" id="panel" <?php checked($panel, 1 ); ?>><br/>
				<small><?php _e('If checked, panel on top of map is displayed','lmm') ?></small>
				</p>
				</div>
				</td>
				<td id="lmm_backend" style="padding-bottom:5px;" class="lmm-border">
					<?php
					echo '<div id="lmm" class="lmm-rtl" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;

					//info: panel for layer name and API URLs
					$panel_state = ($panel == 1) ? 'block' : 'none';
					echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . ';">'.PHP_EOL;
					echo '<div class="lmm-panel-api">';
						if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
							echo '<a id="panel-link-kml" tabindex="114" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/layer/' . $id . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
							echo '<a id="panel-link-fullscreen" tabindex="115" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
							echo '<a id="panel-link-qr" tabindex="116" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/layer/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
							if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; }
							echo '<a id="panel-link-geojson" tabindex="117" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $geojson_api_link . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
							echo '<a id="panel-link-georss" tabindex="118" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
							echo '<a id="panel-link-wikitude" tabindex="119" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/layer/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" class="lmm-panel-api-images" /></a>';
						}

					echo '</div>'.PHP_EOL;
					echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ])) . '">' . (($name == NULL) ? __('if set, layername will be displayed here','lmm') : stripslashes($name)) . '</div>'.PHP_EOL;
					?>
					</div> <!--end lmm-panel-->
					<div id="selectlayer" style="height:<?php echo $mapheight; ?>px;"><span id="selectlayer-loading" class="lmm-markers-loading"></span></div>
					<?php $gpx_panel_state = ($gpx_panel == 1) ? 'block' : 'none'; ?>
					<div id="gpx-panel-selectlayer" class="gpx-panel" style="display:<?php echo $gpx_panel_state; ?>; background: <?php echo htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])); ?>;">
					<?php
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
					echo $gpx_metadata;
					if ( (isset($lmm_options[ 'gpx_metadata_gpx_download' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_gpx_download' ] == 1 ) ) {
						echo '<span class="gpx-delimiter">|</span> <span id="gpx-download"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/download/?map_type=layer&map_id=' . $id . '&format=gpx') . '" title="' . esc_attr__('download GPX file','lmm') . '" class="lmm-icon-download-gpx">' . esc_attr__('download GPX file','lmm') . ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-download-gpx.png" width="10" height="10" alt="' . esc_attr__('download GPX file','lmm') . '"></a></span>';
					}
					?>
					</div>
					<?php
					//info: display a list of markers
					$listmarkers_state = ($listmarkers == 0) ? 'none' : 'block';
					echo '<div id="lmm-listmarkers" class="lmm-listmarkers" style="display:' . $listmarkers_state . ';">'.PHP_EOL;
					//info: set list markers width to be 100% of maps width
					if ($mapwidthunit == '%') {
						$layer_marker_list_width = '100%';
					} else {
						$layer_marker_list_width = $mapwidth.$mapwidthunit;
					}
					echo '<input type="hidden" id="admin_multi_layer_map" name="multi_layer_map" value="' . $multi_layer_map . '" />';
					$mlm_list = (!is_array($multi_layer_map_list))?$multi_layer_map_list:'';
					echo '<input type="hidden" id="admin_multi_layer_map_list" name="multi_layer_map_list" value="' . $mlm_list. '" />';
					echo '<table id="lmm_listmarkers_table_admin" cellspacing="0" style="width:' . $layer_marker_list_width . ';" class="lmm-listmarkers-table" data-mapname="selectlayer">';
					$order_by = $lmm_options['defaults_layer_listmarkers_order_by'];
					if ($markercount == 0) {
						echo '<tr><td style="border-style:none;width:35px;"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" /></td>';
						echo '<td style="border-style:none;"><div style="float:right;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" class="lmm-panel-api-images" /></div><strong>'.__('Markers assigned to this layer will be listed here', 'lmm').'</strong></td></tr>';
					} else {
						if ($layer_marker_list != NULL) { //info: to prevent action bar on layer with no markers assigned
							if($lmm_options['defaults_layer_listmarkers_action_bar'] != 'hide'){
								echo '<tr id="search_markers_row_admin">'.PHP_EOL;
								echo '	<td colspan="2">'.PHP_EOL;
								if($lmm_options['defaults_layer_listmarkers_action_bar'] != 'show-sort-order-selection-only'){
									$defaults_layer_listmarkers_searchtext = ($lmm_options['defaults_layer_listmarkers_searchtext'] == NULL) ? __('Search markers','lmm') : esc_attr(strip_tags($lmm_options['defaults_layer_listmarkers_searchtext']));
									$defaults_layer_listmarkers_searchtext_hover = ($lmm_options['defaults_layer_listmarkers_searchtext_hover'] == NULL) ? __('start typing to find marker entries based on markername or popuptext','lmm') : esc_attr(strip_tags( $lmm_options['defaults_layer_listmarkers_searchtext_hover']));
									echo '		<input id="search_markers_admin" class="lmm-search-markers"  type="text" value="" data-mapid="admin" placeholder="'.$defaults_layer_listmarkers_searchtext.'" title="'.$defaults_layer_listmarkers_searchtext_hover.'" />'.PHP_EOL;
								}
								if($lmm_options['defaults_layer_listmarkers_action_bar'] == 'show-sort-order-selection-only' || $lmm_options['defaults_layer_listmarkers_action_bar'] == 'show-full'){
									$order_class = ($lmm_options[ 'defaults_layer_listmarkers_sort_order' ] == 'ASC')?'up':'down';
									$order_hover_text = ($order_class == 'up')?__('sort order ascending','lmm'):__('sort order descending','lmm');
									$order_value_hover_text = ($order_class == 'down')?__('ascending','lmm'):__('descending','lmm');
									$order_text = MMP_Globals::get_order_text($order_by);
									echo '<div id="dropdown_admin" class="dropdown '.$order_class.'" title="' . esc_attr__('sort order','lmm') . '" data-sortby="'.$order_by.'">'.PHP_EOL;
									echo '  		  <button class="dropbtn '. $order_class .'" title="'.$order_hover_text.'">'. $order_text .'</button>'.PHP_EOL;
									echo '			  <div class="dropdown-content" data-mapid="admin">'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_id']) && $lmm_options['defaults_layer_listmarkers_sort_id'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), 'ID', $order_value_hover_text) . '" data-sortby="m.id" class="lmm-sort-by ' . ($order_by == 'm.id'?$order_class:'') .'">ID</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_markername']) && $lmm_options['defaults_layer_listmarkers_sort_markername'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('marker name','lmm'), $order_value_hover_text) . '" data-sortby="m.markername" class="lmm-sort-by ' . ($order_by == 'm.markername'?$order_class:'') .'">'.__('marker name','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_popuptext']) && $lmm_options['defaults_layer_listmarkers_sort_popuptext'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('popuptext','lmm'), $order_value_hover_text) . '" data-sortby="m.popuptext" class="lmm-sort-by ' . ($order_by == 'm.popuptext'?$order_class:'') .'">'.__('popuptext','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_icon']) && $lmm_options['defaults_layer_listmarkers_sort_icon'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('icon','lmm'), $order_value_hover_text) . '" data-sortby="m.icon" class="lmm-sort-by ' . ($order_by == 'm.icon'?$order_class:'') .'">'.__('icon','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_created_by']) && $lmm_options['defaults_layer_listmarkers_sort_created_by'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('created by','lmm'), $order_value_hover_text) . '" data-sortby="m.createdby" class="lmm-sort-by ' . ($order_by == 'm.createdby'?$order_class:'') .'">'.__('created by','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_created_on']) && $lmm_options['defaults_layer_listmarkers_sort_created_on'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('created on','lmm'), $order_value_hover_text) . '" data-sortby="m.createdon" class="lmm-sort-by ' . ($order_by == 'm.createdon'?$order_class:'') .'">'.__('created on','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_updated_by']) && $lmm_options['defaults_layer_listmarkers_sort_updated_by'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('updated by','lmm'), $order_value_hover_text) . '" data-sortby="m.updatedby" class="lmm-sort-by ' . ($order_by == 'm.updatedby'?$order_class:'') .'">'.__('updated by','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_updated_on']) && $lmm_options['defaults_layer_listmarkers_sort_updated_on'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('updated on','lmm'), $order_value_hover_text) . '" data-sortby="m.updatedon" class="lmm-sort-by ' . ($order_by == 'm.updatedon'?$order_class:'') .'">'.__('updated on','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_layer_id']) && $lmm_options['defaults_layer_listmarkers_sort_layer_id'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('layer ID','lmm'), $order_value_hover_text) . '" data-sortby="m.layer" class="lmm-sort-by ' . ($order_by == 'm.layer'?$order_class:'') .'">'.__('layer ID','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_address']) && $lmm_options['defaults_layer_listmarkers_sort_address'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('address','lmm'), $order_value_hover_text) . '" data-sortby="m.address" class="lmm-sort-by ' . ($order_by == 'm.address'?$order_class:'') .'">'.__('address','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_kml_timestamp']) && $lmm_options['defaults_layer_listmarkers_sort_kml_timestamp'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('KML timestamp','lmm'), $order_value_hover_text) . '" data-sortby="m.kml_timestamp" class="lmm-sort-by ' . ($order_by == 'm.kml_timestamp'?$order_class:'') .'">'.__('KML timestamp','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_distance_layer_center']) && $lmm_options['defaults_layer_listmarkers_sort_distance_layer_center'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('distance from layer center','lmm'), $order_value_hover_text) . '" data-sortby="distance_layer_center" class="lmm-sort-by ' . ($order_by == 'distance_layer_center'?$order_class:'') .'">'.__('distance from layer center','lmm').'</a>'.PHP_EOL;
									if(isset($lmm_options['defaults_layer_listmarkers_sort_distance_current_pos']) && $lmm_options['defaults_layer_listmarkers_sort_distance_current_pos'] == 1)
										echo '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('distance from current position','lmm'), $order_value_hover_text) . '" data-sortby="distance_current_position"  class="lmm-sort-by ' . ($order_by == 'distance_current_position'?$order_class:'') .'">'.__('distance from current position','lmm').'</a>'.PHP_EOL;
									echo '			  </div>'.PHP_EOL;
									echo '			</div>'.PHP_EOL;
								}
								echo '	</td>'.PHP_EOL;
								echo '</tr>'.PHP_EOL;
							}
							foreach ($layer_marker_list as $row) {
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_icon' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_icon' ] == 1 ) ) {
									echo '<tr id="marker_'.$row['markerid'].'"><td class="lmm-listmarkers-icon">';
									if ($lmm_options['defaults_layer_listmarkers_link_action'] != 'disabled') {
										$listmarkers_href_a = '<a href="#address" onclick="javascript:listmarkers_action(' . $row['markerid'] . ')">';
										$listmarkers_href_b = '</a>';
									} else {
										$listmarkers_href_a = '';
										$listmarkers_href_b = '';
									}
									if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
										$markername_on_hover = 'title="' . stripslashes(htmlspecialchars(preg_replace('/[\x00-\x1F\x7F]/', '', $row['markername']))) . '"';
									} else {
										$markername_on_hover = '';
									}
									if ($row['micon'] != null) {
										echo $listmarkers_href_a . '<img src="' . $defaults_marker_icon_url . '/'.esc_html($row['micon']).'" ' . $markername_on_hover . ' width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" alt="marker icon" />' . $listmarkers_href_b;
									} else {
										echo $listmarkers_href_a . '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" ' . $markername_on_hover . ' alt="marker icon" />' . $listmarkers_href_b;
									};
								} else {
									echo '<tr><td>';
								}
								echo '</td><td class="lmm-listmarkers-popuptext"><div class="lmm-listmarkers-panel-icons">';

								$edit_link = (current_user_can( $lmm_options[ 'capabilities_edit_others' ]))?'<a title="' . esc_attr__('Edit marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'"><img class="lmm-panel-api-images" style="margin-right:3px !important;" src="' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png" width="16" height="16" alt="' . esc_attr__('Edit marker','lmm') . ' ID ' . $row['markerid'] . '"></a>':'';
								echo $edit_link;

								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_directions' ] == 1 ) ) {
									if ($lmm_options['directions_provider'] == 'googlemaps') {
										if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
										if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
										if ( $row['maddress'] != NULL ) { $google_from = urlencode($row['maddress']); } else { $google_from = $row['mlat'] . ',' . $row['mlat']; }
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
										echo '<a tabindex="127" href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . esc_html($lmm_options[ 'directions_googlemaps_map_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_googlemaps_traffic' ]) . '&doflg=' . esc_html($lmm_options[ 'directions_googlemaps_distance_units' ]) . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . intval($lmm_options[ 'directions_googlemaps_overview_map' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
									} else if ($lmm_options['directions_provider'] == 'yours') {
										if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
										echo '<a tabindex="128" href="http://www.yournavigation.org/?tlat=' . $row['mlat'] . '&tlon=' . $row['mlon'] . '&v=' . esc_html($lmm_options[ 'directions_yours_type_of_transport' ]) . '&fast=' . intval($lmm_options[ 'directions_yours_route_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_yours_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
									} else if ($lmm_options['directions_provider'] == 'ors') {
										if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
										echo '<a tabindex="130" href="http://www.openrouteservice.org/?pos=' . $row['mlon'] . ',' . $row['mlat'] . '&wp=' . $row['mlon'] . ',' . $row['mlat'] . '&zoom=' . $row['mzoom'] . '&routeWeigh=' . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . '&routeOpt=' . esc_html($lmm_options[ 'directions_ors_routeOpt' ]) . '&layer=' . esc_html($lmm_options[ 'directions_ors_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
									} else if ($lmm_options['directions_provider'] == 'bingmaps') {
										if ( $row['maddress'] != NULL ) { $bing_to = '_' . urlencode($row['maddress']); } else { $bing_to = ''; }
										echo '<a tabindex="130" href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $row['mlat'] . '_' . $row['mlon'] . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
									}
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="131" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['markerid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_kml' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="132" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['markerid'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="133" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="134" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['markerid'] . '?/callback=jsonp&full=yes&full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_georss' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="135" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['markerid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="136" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['markerid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_distance' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_show_distance' ] == 1 ) && ($lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance_layer_center' || $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance_current_position' || $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.km' || $lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.mile') ) { //info: needed fallback as setting name has changed
									if ( ($lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.km') || ($lmm_options['defaults_layer_listmarkers_show_distance_unit'] == 'km') ) {
										echo '<br/><br/><span class="lmm-distance" title="' . esc_attr__('calculated from map center','lmm') . '">' . __('distance', 'lmm').': ' . round($row['distance'], intval($lmm_options[ 'defaults_layer_listmarkers_show_distance_precision' ])) . ' ' . __('km','lmm') . '</span>';
									} else if ( ($lmm_options[ 'defaults_layer_listmarkers_order_by' ] == 'distance.mile') || ($lmm_options['defaults_layer_listmarkers_show_distance_unit'] == 'mile') ) {
										echo '<br/><br/><span class="lmm-distance" title="' . esc_attr__('calculated from map center','lmm') . '">' . __('distance', 'lmm').': ' . round($row['distance'], intval($lmm_options[ 'defaults_layer_listmarkers_show_distance_precision' ])) . ' ' . __('miles','lmm') . '</span>';
									}
								}
								echo '</div>';
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_markername' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_markername' ] == 1 ) ) {
									if ($lmm_options['defaults_layer_listmarkers_link_action'] != 'disabled') {
										echo '<span class="lmm-listmarkers-markername"><a title="' . esc_attr__('show marker on map','lmm') . '" href="#address" onclick="javascript:listmarkers_action(' . $row['markerid'] . ')">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></span>';
									} else {
										echo '<span class="lmm-listmarkers-markername">' . stripslashes(htmlspecialchars($row['markername'])) . '</span>';
									}
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ] == 1 ) ) {
									$popuptext_sanitized = MMP_Globals::sanitize_popuptext($row['mpopuptext']);
									echo '<br/><span class="lmm-listmarkers-popuptext-only">' . do_shortcode($popuptext_sanitized) . '</span>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_address' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_address' ] == 1 ) ) {
									if ( $row['mpopuptext'] == NULL ) {
										echo stripslashes(htmlspecialchars($row['maddress']));
									} else if ( ($row['mpopuptext'] != NULL) && ($row['maddress'] != NULL) ) {
										echo '<div class="lmm-listmarkers-hr">' . stripslashes(htmlspecialchars($row['maddress'])) . '</div>';
									}
								}
								echo '</td></tr>';
							} //info: end foreach
						} //info: end ($layer_marker_list != NULL)
					} //info: end $isedit

					//info: adding info if more markers are available than listed in markers list
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
						}else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.address') {
							$orderby = __('address','lmm');
						} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.km') {
							$orderby = __('distance from layer center in km','lmm');
						} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.mile') {
							$orderby = __('distance from layer center in miles','lmm');
						}
					}
					if ($layer_marker_list != NULL) {
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
							} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'distance_layer_center') {
								if($lmm_options['defaults_layer_listmarkers_show_distance_unit'] == 'km')
									$orderby = __('distance from layer center in km','lmm');
								else
									$orderby = __('distance from layer center in mile','lmm');
							} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'distance_current_position') {
								if($lmm_options['defaults_layer_listmarkers_show_distance_unit'] == 'km' || $lmm_options['defaults_layer_listmarkers_order_by'] == 'distance.km')
									$orderby = __('distance from layer center in km','lmm');
								else
									$orderby = __('distance from layer center in mile','lmm');
							}
							echo '<tr id="pagination_row_admin"><td colspan="2" style="text-align:center">' . MMP_Globals::get_markers_pagination('admin', $markercount, $multi_layer_map, $multi_layer_map_list, $order_by) . '</td></tr>';
						}else{

							if($lmm_options['defaults_layer_listmarkers_action_bar'] == 'show-sort-order-selection-only' || $lmm_options['defaults_layer_listmarkers_action_bar'] == 'show-full'){
								echo '<input type="hidden" id="markers_per_page_admin"  value="'.intval($lmm_options[ "defaults_layer_listmarkers_limit" ]).'" data-mapid="admin" />';
								echo '<input type="hidden" id="admin_orderby" name="orderby" value="' . $order_by . '" />';
								echo '<input type="hidden" id="admin_order" name="order" value="' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . '" />';
								echo '<input type="hidden" id="admin_multi_layer_map" name="multi_layer_map" value="' . $multi_layer_map . '" />';
								echo '<input type="hidden" id="admin_multi_layer_map_list" name="multi_layer_map_list" value="' . $multi_layer_map_list. '" />';
								echo '<input type="hidden" id="admin_markercount" name="markercount" value="' . $markercount. '" />';
							}
						}
					}
					?>
					</table>
					</div> <!--end lmm-listmarkers-->
					</div><!--end mapsmarker div-->
					<!--//info: preload area for CSS background images (home button etc)-->
					<div class="lmm-preload-area"></div>
				</td>
			</tr>
			<tr>
				<td class="lmm-border"><p style="margin-bottom:0px;"><strong><label for="multi_layer_map"><?php _e('Multi Layer Map','lmm') ?></label></strong>&nbsp;
					<input <?php echo $mlm_disabled; ?> style="margin-top:1px;" type="checkbox" name="multi_layer_map" id="multi_layer_map" <?php checked($multi_layer_map, 1 ); ?>><br/>
					<small><?php _e('Show markers from other layers on this map','lmm') ?></small></p>

					<?php $multi_layer_map_state = ($multi_layer_map == 1) ? 'block' : 'none'; ?>
					<div id="lmm-multi_layer_map_filter" style="display:<?php echo $multi_layer_map_state; ?>">
					<p><br/>
					<strong><?php _e('Filter controlbox:','lmm'); ?></strong><br/>
					<input style="margin-top:1px;" id="controlbox_mlm_filter_hidden" class="filter-controlbox" type="radio" name="controlbox_mlm_filter" <?php checked($mlm_filter_controlbox, 0); ?> value="0" /><label for="controlbox_mlm_filter_hidden"><?php _e('hidden','lmm') ?></label><br/>
					<input style="margin-top:1px;" id="controlbox_mlm_filter_collapsed" class="filter-controlbox" type="radio" name="controlbox_mlm_filter" <?php checked($mlm_filter_controlbox, 1); ?> value="1" /><label for="controlbox_mlm_filter_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
					<input style="margin-top:1px;" id="controlbox_mlm_filter_expanded" class="filter-controlbox" type="radio" name="controlbox_mlm_filter" <?php checked($mlm_filter_controlbox, 2); ?> value="2" /><label for="controlbox_mlm_filter_expanded"><?php _e('expanded','lmm') ?></label><br/>
					<small><?php _e('Allows you to toggle marker display on frontend','lmm') ?>
					<?php if (current_user_can('activate_plugins')) {
					echo '<span id="toggle-mlm-filters-settings" style="' . $current_editor_css_inline . '">(<a tabindex="125" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-filtering">' . __('Settings','lmm') . '</a>)</span>';
				} ?>
					</small>
					</p>
					</div>
				</td>
				<td class="lmm-border">
					<?php
					if (($check_mlm != NULL) && ($multi_layer_map == 0)) {
						echo '<div id="lmm-check-mlm-text">';
						echo sprintf(__('This layer cannot be converted into a multi-layer-map as %1$s markers have been directly assigned to this layer.','lmm'), $markercount);
						echo '<br/>' . sprintf(__('To create a multi-layer-map, please <a href="%1$s">add a new layer map</a> or <a href="%2$s">move all markers from layer ID %3$s to another layer</a>.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools#move-markers', $id);
						echo '</div>';
					}
					echo '<div id="lmm-multi_layer_map" style="display:' . $multi_layer_map_state . ';">'.PHP_EOL;
					echo __('Please select the layers, whose markers you would like to display on this multi layer map.','lmm').PHP_EOL;
					$mlm_checked_all = ( in_array('all', $multi_layer_map_list_exploded) ) ? ' checked="checked"' : '';
					echo '<br/><br/><input id="mlm-all" type="checkbox" id="mlm-all" name="mlm-all" ' . $mlm_checked_all . '> <label for="mlm-all">' . __('display all markers','lmm') . '</label><br/><br/>'.PHP_EOL;
					echo '<strong>' . __('Display markers from selected layers only','lmm') . '</strong><div id="mlm-filters-note" style="float:right;background-color:#ffcc33;font-weight:bold;padding:0 5px;display:none;">' . __('To preview the filter controlbox, please save layer map first and then reload the page!','lmm') . '</div><br/>';
					echo '<table  cellspacing="0" id="list-markers" class="wp-list-table widefat lmm-mlm-layers-table" style="width:100%;margin-top:5px;">
							<thead>
								<tr>
									<th class="manage-column column-cb check-column" scope="col"></th>
									<th class="manage-column before_primary column-id" scope="col"><strong>ID</strong></th>
									<th class="manage-column column-primary column-layername" scope="col"><strong>' . __('layer name','lmm') . '</strong></th>
									<th class="manage-column column-markercount" scope="col" style="text-align:center;"><strong>' . __('marker count','lmm') . '</strong></th>
									<th class="manage-column column-addtocontrolbox" scope="col"><strong>' . __('add layer to filter controlbox?','lmm') . '</strong></th>
									<th class="manage-column column-icon" scope="col"><strong>' . __('icon url for filter controlbox','lmm') . '</strong></th>
									<th class="manage-column column-filtername" scope="col"><strong>' . __('name for filter controlbox','lmm') . '</strong></th>
								</tr>
							</thead>
							<tbody>';

					if ($layerlist == NULL) {
						echo '<tr><td colspan="5">' . __('No layer has been created yet','lmm') . '</td></tr>';
					} else {
						foreach ($layerlist as $mlmrow){
							$filter_status = (isset($filter_details[ $mlmrow['lid'] ]))?($filter_details[ $mlmrow['lid'] ]['status'] == 'active')?1:2:'';
							$filter_name = (isset($filter_details[ $mlmrow['lid'] ]))?$filter_details[ $mlmrow['lid'] ]['name']:'';
							$filter_icon = (isset($filter_details[ $mlmrow['lid'] ]))?$filter_details[ $mlmrow['lid'] ]['icon']:'';
							$mlm_markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$mlmrow['lid']);

							if ( in_array($mlmrow['lid'], $multi_layer_map_list_exploded) ) {
								$mlm_checked{$mlmrow['lid']} = ' checked="checked"';
							} else {
								$mlm_checked{$mlmrow['lid']} = '';
							}
							if ( ($id != $mlmrow['lid']) || ($mlm_markercount != 0) ) { //info: make current layer selectable for MLM if has markers only
								echo '<tr valign="middle" class="alternate">
								<th class="check-column" scope="row">
								<input class="filter-checkbox" type="checkbox" id="mlm-'.$mlmrow['lid'].'" name="mlm-'.$mlmrow['lid'].'" ' . $mlm_checked{$mlmrow['lid']} . '> <label for="mlm-'.$mlmrow['lid'].'" />
								</th>
								<td class="before_primary" data-colname="ID">
									<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id='.$mlmrow['lid'].'" title="' . esc_attr__('show map','lmm') . '" target="_blank">' . $mlmrow['lid'] . '</a>
								</td>
								<td class="column-primary">
									' . stripslashes(htmlspecialchars($mlmrow['lname'])) . ' <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
								</td>
								<td style="text-align:center;" data-colname="'.__('marker count', 'lmm').'">
									' . $mlm_markercount . '
								</td>
								<td data-colname="'.__('add layer to filter controlbox?', 'lmm').'">
									<select id="mlm_filter_status_' . $mlmrow['lid'] . '" class="mlm_filter_status" data-layerid="'. $mlmrow['lid'] .'"  name="mlm_filter_status_' . $mlmrow['lid'] . '">
										<option value="0">' . esc_attr__('no','lmm') . '</option>
										<option '. selected($filter_status, 1, false) .' value="1">' . esc_attr__('yes (checked)','lmm') . '</option>
										<option '. selected($filter_status, 2, false) .' value="2">' . esc_attr__('yes (unchecked)','lmm') . '</option>
									</select>
								</td>
								<td data-colname="'.__('icon url for filter controlbox', 'lmm').'">
									<input class="filter-icon-url" style="height:25px; width:100%;" id="mlm_filter_icon_' . $mlmrow['lid'] . '" type="text" value="'. esc_attr( $filter_icon ) .'" placeholder="' . esc_attr__('do not add an icon','lmm') . '" />
								</td>
								<td data-colname="'.__('name for filter controlbox', 'lmm').'">
									<input class="filter-name" style="height:25px; width:100%;" id="mlm_filter_name_' . $mlmrow['lid'] . '" type="text" value="'. esc_attr( stripslashes($filter_name) ) .'" data-default="'.esc_attr(stripslashes($mlmrow['lname'])).'" />
								</td>
								</tr>';
							}
						}
					};
					echo '</tbody></table>';
					echo '</div>'.PHP_EOL;
					?>
				</td>
			</tr>

			<tr id="toggle-advanced-settings" style="<?php echo $current_editor_css_audit; ?>">
				<td class="lmm-border"><strong><?php _e('Advanced settings','lmm') ?></strong></td>
				<td class="lmm-border">
					<p><strong><?php _e('WMS layers','lmm') ?></strong> <?php if (current_user_can('activate_plugins')) { echo '<a tabindex="101" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#wms">(' . __('Settings','lmm') . ')</a>'; } ?></p>
					<?php
					//info: define available wms layers (for markers and layers)
					if ( (isset($lmm_options[ 'wms_wms_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms" class="wms" name="wms"';
						if ($wms == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms">' . strip_tags($lmm_options[ 'wms_wms_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 1 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms1"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms2_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms2_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms2" class="wms" name="wms2"';
						if ($wms2 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms2">' . strip_tags($lmm_options[ 'wms_wms2_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 2 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms2"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms3_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms3_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms3"class="wms"  name="wms3"';
						if ($wms3 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms3">' . strip_tags($lmm_options[ 'wms_wms3_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 3 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms3"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms4_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms4_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms4" class="wms" name="wms4"';
						if ($wms4 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms4">' . strip_tags($lmm_options[ 'wms_wms4_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 4 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms4"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms5_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms5_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms5" class="wms" name="wms5"';
						if ($wms5 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms5">' . strip_tags($lmm_options[ 'wms_wms5_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 5 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms5"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms6_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms6_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms6" class="wms" name="wms6"';
						if ($wms6 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms6">' . strip_tags($lmm_options[ 'wms_wms6_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 6 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms6"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms7_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms7_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms7" class="wms" name="wms7"';
						if ($wms7 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms7">' . strip_tags($lmm_options[ 'wms_wms7_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 7 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms7"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms8_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms8_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms8" class="wms" name="wms8"';
						if ($wms8 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms8">' . strip_tags($lmm_options[ 'wms_wms8_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 8 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms8"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms9_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms9_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms9" class="wms" name="wms9"';
						if ($wms9 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms9">' . strip_tags($lmm_options[ 'wms_wms9_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 9 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms9"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms10_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms10_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms10" class="wms" name="wms10"';
						if ($wms10 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms10">' . strip_tags($lmm_options[ 'wms_wms10_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 10 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms10"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>';
					}
					?>

					<?php
					if (current_user_can('activate_plugins')) {
						if ( $lmm_options['misc_backlinks'] == 'show' ) {
							echo '<hr style="border:none;color:#edecec;background:#edecec;height:1px;"><strong>' . __('Hide MapsMarker.com backlinks','lmm') .'</strong>: ';
							echo '<a tabindex="110" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc">' . __('Please visit Settings / Misc to disable MapsMarker.com backlinks','lmm') . '</a>';
						}
					}
					?>
					<?php if (current_user_can('activate_plugins')) { ?>
					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
					<strong><?php _e('Minimap settings','lmm'); ?> </strong>
					<a tabindex="110" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-minimap"><?php _e('Please visit Settings / Maps / Minimap settings','lmm'); ?></a>
					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
					<strong><?php _e('Geolocate settings','lmm'); ?> </strong>
					<a tabindex="111" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-geolocate"><?php _e('Please visit Settings / Maps / Geolocate settings','lmm'); ?></a>
					<?php } ?>
				</td>
			</tr>

			<?php if ($isedit) { ?>
			<tr id="toggle-audit" style="<?php echo $current_editor_css_audit; ?>">
				<td class="lmm-border"><small><strong><?php _e('Audit','lmm') ?>:</strong></small></td>
				<td class="lmm-border"><small>
					<?php
					echo __('Layer added by','lmm') . ' ';

					if (current_user_can('activate_plugins')) {
						echo '<input title="' . esc_attr__('Please use valid WordPress usernames as otherwise non-admins might not be able to access this map on backend (depending on your access settings)','lmm') . '" type="text" id="createdby" name="createdby" value="' . esc_html($createdby) . '" style="font-size:small;width:110px;height:24px;" />';
						echo '<input type="' . $datetime_ios_workaround . '" id="createdon" name="createdon" value="' . date('Y-m-d\TH:i:s', strtotime($createdon)) . '" style="font-size:small;height:24px;" /> ';
						if ($updatedon != $createdon) {
							echo __('last update by','lmm');
							echo ' ' . esc_html($updatedby) . ' - ' . $updatedon;
						}
					} else {
						echo '<input type="hidden" id="createdby" name="createdby" value="' . esc_html($createdby) . '" />';
						echo '<input type="hidden" id="createdon" name="createdon" value="' . $createdon . '" /> ';
						echo esc_html($createdby) . ' - ' . $createdon;
						if ($updatedon != $createdon) {
							echo ', ' . __('last update by','lmm');
							echo ' ' . esc_html($updatedby) . ' - ' . $updatedon;
						};
					}
					?>
					</small></td>
			</tr>
			<?php }; ?>
		</table>

		<table class="layer_buttons_table"><tr style="display:block;padding-top:5px;"><td>
		<?php
		echo '<input type="hidden" id="createdby" name="createdby" value="' . esc_html($createdby) . '" />';
		echo '<input type="hidden" id="createdon" name="createdon" value="' . $createdon . '" /> ';
		echo '<input type="hidden" id="updatedby_next" name="updatedby_next" value="' . $current_user->user_login . '" />';
		echo '<input type="hidden" id="updatedon_next" name="updatedon_next" value="' . current_time('mysql',0) . '" />';
		?>
		<?php
			if (MMP_Globals::check_capability('edit', $createdby)) {
				if ($isedit === true) { $button_text = __('update','lmm'); } else { $button_text = __('publish','lmm'); }
				echo '<div class="submit"><input id="submit_bottom" style="font-weight:bold;" type="submit" name="layer" class="button button-primary" value="' . $button_text . '" />';
				echo '<img src="' . admin_url('/images/wpspin_light.gif') . '" class="waiting" id="lmm_ajax_loading_top" style="margin-left:5px; display:none;"/></div>';
			} else {
				if ($isedit === true) {
					echo __('Your user does not have the permission to add a new layer!','lmm');
				} else {
					echo __('Your user does not have the permission to update this layer!','lmm');
				}
			}
		?>
		</form>

		</td>
		<td class="hide_on_new">
		<?php
			$multi_layer_map_edit_button_visibility = ($multi_layer_map == 0) ? 'display:block;' : 'display:none;';
			echo '<a class="button button-secondary addmarker_link button-add-new-marker-to-this-layer" style="font-size:13px;margin-left:15px;text-decoration:none;' . $multi_layer_map_edit_button_visibility . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $oid . '">' . __('add new marker to this layer','lmm') . '</a>';
		?>
		</td>
		<td class="hide_on_new">
			<?php
			if (MMP_Globals::check_capability('delete', $createdby)) {
				echo '<form method="post" style="margin-bottom:0px;">';
				wp_nonce_field('layer-nonce');
				echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="delete" />';
				$confirm = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s)?','lmm'), $name, $id);
				echo '<a id="delete_button_top" href="javascript:void(0);" id="layer-delete" class="button button-secondary" style="font-size:13px;text-decoration:none;color:#FF0000;margin-left:20px;">' . __('delete layer only', 'lmm') . '</a>';
				echo '</form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to delete this layer!','lmm') . '</span>';

			}
			?>
		</td>
		<td class="hide_on_new">
			<span class="show-advanced-layer-edit-buttons" style="display:inline;"><a style="cursor:pointer;" title="<?php esc_attr_e('show advanced functions','lmm'); ?>">>>></a></span>
			<span class="hide-advanced-layer-edit-buttons" style="display:none;"><a style="cursor:pointer;" title="<?php esc_attr_e('hide advanced functions','lmm'); ?>"><<<</a></span>
		</td>
		<td class="hide_on_new advanced-layer-edit-button" style="display:none;">
		<?php
			if (MMP_Globals::check_capability('edit', $createdby)) {
				echo '<form method="post" style="margin-bottom:0px;">';
				wp_nonce_field('layer-nonce');
				echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="duplicate" />';
				echo '<div class="submit" style="margin:0;">';
				echo '<a id="duplicate_button_bottom" href="javascript:void(0);" class="button button-secondary" style="font-size:13px;text-decoration:none;">' . __('duplicate layer only', 'lmm') . '</a>';
				echo '</div></form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to duplicate this layer!','lmm') . '</span>';
			}
		?>
		</td>
		<td class="hide_on_new advanced-layer-edit-button" style="display:none;">
			<?php
			if ($multi_layer_map == 0) {
				if (MMP_Globals::check_capability('edit', $createdby)) {
					echo '<form method="post" style="margin-bottom:0px;">';
					wp_nonce_field('layer-nonce');
					echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
					echo '<input type="hidden" name="action" value="duplicatelayerandmarkers" />';
					echo '<input class="button button-secondary" style="margin-left:20px;" type="submit" name="layer" value="' . __('duplicate layer and assigned markers', 'lmm') . '" />';
					echo '</form>';
				} else {
					echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to duplicate this layer!','lmm') . '</span>';
				}
			}
		?>
		</td>
		<td class="hide_on_new advanced-layer-edit-button" style="display:none;">
			<?php
			if (MMP_Globals::check_capability('delete', $createdby)) {
				echo '<form method="post" style="margin-bottom:0px;">';
				wp_nonce_field('layer-nonce');
				echo '<input type="hidden" class="btns_layer_id" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="deleteboth" />';
				$confirm2 = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s) and all assigned markers? (if a marker is assigned to multiple layers only the reference to the layer will be removed)','lmm'), $name, $id);
				if ($multi_layer_map == 0) {
					echo "<input id='delete_layer_and_markers' class='button button-secondary' style='color:#FF0000;margin-left:20px;' type='submit' name='layer' value='" . __('delete layer AND assigned markers', 'lmm') . "' onclick='return confirm(\"".$confirm2 ."\")' />";
				}
				echo '</form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to delete this layer and all assigned markers!','lmm') . '</span>';
			}
			?>
		</td>
		</tr></table>
		<div style="height:30px;padding:0 0 15px 0;">
			<div id="lmm_ajax_results_bottom" style="padding:10px;display:none;"></div>
		</div>
		<?php if ($isedit) { ?>
		<span id="assigned-markers-table">
		<h2 id="assigned_markers">
			<?php
			echo '<span id="listmarker-table-heading">';
			if ($multi_layer_map == 0) {
				$assigned_markers_layername = sprintf(__('Markers assigned to layer "%1s" (ID %2s)','lmm'), $name, $id);
				echo $assigned_markers_layername;
			} else if ($multi_layer_map == 1) {
				$assigned_markers_layername = sprintf(__('Markers assigned to multi layer map "%1s" (ID %2s)','lmm'), $name, $id);
				echo $assigned_markers_layername;
			}
			echo '</span>';
			?>
		</h2>
		<p>
			<?php _e('Total','lmm') ?>: <span id="markercount"><?php echo $markercount; ?></span> <?php _e('markers','lmm') ?>
		</p>
		<p>
		<?php
		$addmarker_link_visibility = ($multi_layer_map == 0) ? 'display:inline;' : 'display:none;';
		echo '<a class="addmarker_link" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $id . '" style="text-decoration:none;' . $addmarker_link_visibility . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-add.png" /> ' . __('add new marker to this layer','lmm') . '</a>';
		?>
		</p>
		<?php
		//info:  get pagination
		//info: security check if input variable is valid
		$columnsort_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox');
		$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_marker_listing_sort_order_by' ]);
		$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_marker_listing_sort_order_by' ];

		//info: security check if input variable is valid
		$columnsortorder_values = array('asc','desc','ASC','DESC');
		$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ]);
		$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];

		$radius = 1;
		$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
		$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
		$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
		if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
		if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
		$pager = '';
		if ($markercount > intval($lmm_options[ 'markers_per_page' ])) {
		  $pager = '<div class="tablenav">';
		  $maxpage = intval(ceil($markercount / intval($lmm_options[ 'markers_per_page' ])));
		  if ($maxpage > 1) {
			$pager .= '<div class="tablenav-pages backend">' . __('Markers per page','lmm') . ': ';
			if (current_user_can('activate_plugins')) {
				$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_markers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
			} else {
				$pager .= intval($lmm_options[ "markers_per_page" ]) . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
			}
			$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">' . __('page','lmm') . ' ';
			$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
			$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
			$pager .= '<input type="hidden" name="layer_id" value="' . $id . '" />';
			$pager .= '<input type="hidden" name="multi_layer_map_list" value="' . $multi_layer_map_list . '" />';
			$pager .= '<input type="hidden" name="totalmarkers" value="' . $markercount . '" />';
			if ($pagenum > (2 + $radius * 2)) {
			  foreach (range(1, 1 + $radius) as $num)
				$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page-backend">'.$num.'</a>';
			  $pager .= '...';
			  foreach (range($pagenum - $radius, $pagenum - 1) as $num)
				$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page-backend">'.$num.'</a>';
			}
			else
			  if ($pagenum > 1)
				foreach (range(1, $pagenum - 1) as $num)
				  $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page-backend">'.$num.'</a>';
			$pager .= '<a href="#" class="first-page-backend current-page">' . $pagenum . '</a>';
			if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
			  foreach (range($pagenum + 1, $pagenum + $radius) as $num)
				$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page-backend">'.$num.'</a>';
			  $pager .= '...';
			  foreach (range($maxpage - $radius, $maxpage) as $num)
				$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page-backend">'.$num.'</a>';
			}
			else
			  if ($pagenum < $maxpage)
				foreach (range($pagenum + 1, $maxpage) as $num)
				  $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page-backend">'.$num.'</a>';
			$pager .= '</div></form>';
		  }
			$pager .= '</div>';
		}
		?>
		<?php echo $pager; ?>
	<form method="POST" id="form-list-markers">
		<table cellspacing="0" id="list-markers" class="wp-list-table widefat fixed bookmarks" style="width:100%;margin-top:5px;border-radius:5px;">
			<thead>
				<tr>
					<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
					<th class="manage-column before_primary column-id" scope="col">ID</th>
					<th class="manage-column column-primary column-markername" scope="col"><?php _e('Marker name','lmm') ?></a></th>
					<th class="manage-column column-icon" scope="col"><?php _e('Icon', 'lmm') ?></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) { ?>
					<th class="manage-column column-address" scope="col"><?php _e('Location','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext" scope="col"><?php _e('Popup text','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layername' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layername' ] == 1 )) { ?>
					<th class="manage-column column-layername" scope="col"><?php _e('Layer name','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><?php _e('Popup status', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><?php _e('Zoom', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><?php _e('Basemap', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby" scope="col"><?php _e('Created by','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon" scope="col"><?php _e('Created on','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby" scope="col"><?php _e('Updated by','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon" scope="col"><?php _e('Updated on','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><?php _e('Controlbox status','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) { ?>
					<th class="manage-column column-kml" scope="col">KML<a href="https://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) { ?>
					<th class="manage-column column-fullscreen" scope="col"><?php _e('Fullscreen', 'lmm') ?><span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) { ?>
					<th class="manage-column column-qr-code" scope="col"><?php _e('QR code', 'lmm') ?><span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) { ?>
					<th class="manage-column column-geojson" scope="col">GeoJSON<a href="https://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) { ?>
					<th class="manage-column column-georss" scope="col">GeoRSS<a href="https://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) { ?>
					<th class="manage-column column-wikitude" scope="col">Wikitude<a href="https://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
					<th class="manage-column before_primary column-id" scope="col">ID</th>
					<th class="manage-column column-primary column-markername" scope="col"><?php _e('Marker name','lmm') ?></a></th>
					<th class="manage-column column-icon" scope="col"><?php _e('Icon', 'lmm') ?></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) { ?>
					<th class="manage-column column-address" scope="col"><?php _e('Location','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext" scope="col"><?php _e('Popup text','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layername' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layername' ] == 1 )) { ?>
					<th class="manage-column column-layername" scope="col"><?php _e('Layer name','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><?php _e('Popup status', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><?php _e('Zoom', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><?php _e('Basemap', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby" scope="col"><?php _e('Created by','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon" scope="col"><?php _e('Created on','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby" scope="col"><?php _e('Updated by','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon" scope="col"><?php _e('Updated on','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><?php _e('Controlbox status','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) { ?>
					<th class="manage-column column-kml" scope="col">KML<a href="https://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) { ?>
					<th class="manage-column column-fullscreen" scope="col"><?php _e('Fullscreen', 'lmm') ?><span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) { ?>
					<th class="manage-column column-qr-code" scope="col"><?php _e('QR code', 'lmm') ?><span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) { ?>
					<th class="manage-column column-geojson" scope="col">GeoJSON<a href="https://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) { ?>
					<th class="manage-column column-georss" scope="col">GeoRSS<a href="https://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) { ?>
					<th class="manage-column column-wikitude" scope="col">Wikitude<a href="https://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				</tr>
			</tfoot>
			<tbody id="the-list">
				<?php
				$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links
				if (count($layer_marker_list_table) < 1) {
					echo '<tr><td colspan="7">'.__('No marker assigned to this layer', 'lmm').'</td></tr>';
				} else {
					$ml_checked = MMP_Globals::check_multilingual();
					foreach ($layer_marker_list_table as $row){
						//info: delete link
						if (MMP_Globals::check_capability('delete', $row['mcreatedby'])) {
							$confirm3 = sprintf( esc_attr__('Do you really want to delete marker %1$s (ID %2$s)?','lmm'), stripslashes($row['markername']), $row['markerid']);
							$delete_link_marker = ' | </span><span class="delete"><a onclick="if ( confirm( \'' . $confirm3 . '\' ) ) { return true;}return false;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&action=delete&id='.$row['markerid'].'&_wpnonce=' . $markernonce . '" class="submitdelete">' . __('delete','lmm') . '</a></span>';
						} else {
							$delete_link_marker = '';
						}
						if (MMP_Globals::check_capability('edit', $row['mcreatedby'])) {
							$edit_link_marker = '<strong><a title="' . esc_attr__('Edit marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><span class="edit"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$row['markerid'].'">' . __('edit','lmm') . '</a>';
						} else {
							$edit_link_marker = '<strong><a title="' . esc_attr__('View marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><span class="edit"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$row['markerid'].'">' . __('view','lmm') . '</a>';
						}
						if ($ml_checked == 'wpml') {
							$translate_url = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($row['markername']));
						} elseif ($ml_checked == 'pll') {
							$translate_url = admin_url('admin.php?page=mlang_strings&s='. urlencode($row['markername']) . '&group=Maps+Marker+Pro');
						} else {
							$translate_url = 'https://www.mapsmarker.com/multilingual';
						}
						$translate_link = '&nbsp;|&nbsp;<a href="'. $translate_url .'">'.  __('translate', 'lmm')  .'</a>';
						//info: set column display variables - need for for-each
						$column_layer_name = '<td class="lmm-border" data-colname="'.__('Layer name','lmm').'">' . $row['lname'] . '</td>';
						$column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Location','lmm').'">' . stripslashes(htmlspecialchars($row['maddress'])) . '</td>' : '';
						$column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Popup status','lmm').'">' . $row['mopenpopup'] . '</td>' : '';
						$column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Coordinates','lmm').'">Lat: ' . $row['mlat'] . '<br/>Lon: ' . $row['mlon'] . '</td>' : '';
						$column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Map size','lmm').'">' . __('Width','lmm') . ': '.$row['mmapwidth'].$row['mmapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mmapheight'].'px</td>' : '';
						$column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Zoom','lmm').'">' . $row['mzoom'] . '</td>' : '';
						$column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Controlbox status','lmm').'">'.$row['mcontrolbox'].'</td>' : '';
						$column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Shortcode','lmm').'"><input ' . $shortcode_select . ' style="width:90%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['markerid'] . '&quot;]" readonly></td>' : '';
						$column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('KML','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['markerid'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
						$column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Fullscreen','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
						$column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('QR code','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
						$column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('GeoJSON','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['markerid'] . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
						$column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('GeoRSS','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
						$column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Wikitude','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
						$column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Basemap','lmm').'">' . $row['mbasemap'] . '</td>' : '';
						$column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Created by','lmm').'">' . esc_html($row['mcreatedby']) . '</td>' : '';
						$column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Created on','lmm').'">' . $row['mcreatedon'] . '</td>' : '';
						$column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Updated by','lmm').'">' . esc_html($row['mupdatedby']) . '</td>' : '';
						$column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Updated on','lmm').'">' . $row['mupdatedon'] . '</td>' : '';
						$openpopupstatus = ($row['mopenpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
						$popuptextabstract = (strlen($row['mpopuptext']) >= 90) ? "...": "";
						$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ?
						'<td class="lmm-border" data-colname="'.__('Popup text','lmm').'"><a title="' . esc_attr__('Edit marker', 'lmm') . ' ' . $row['markerid'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'] . '" >' . mb_substr(strip_tags(stripslashes($row['mpopuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
						if (MMP_Globals::check_capability('edit', $row['mcreatedby'])) {
							$css_table_background = '';
						} else {
							$css_table_background = 'background:#f6f6f6;';
						}
						echo '<tr valign="middle" class="alternate" id="link-'.$row['markerid'].'" style="' . $css_table_background . '">
							<th class="lmm-border check-column" scope="row"><input value="'.$row['markerid'].'" name="checkedmarkers[]" data-layertype="marker" type="checkbox"></th>
							<td class="lmm-border before_primary" data-colname="'.__('ID','lmm').'">'.$row['markerid'].'</td>
							<td class="lmm-border column-primary">' . $edit_link_marker . $delete_link_marker . $translate_link . '</div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
							<td class="lmm-border"  data-colname="'.__('Icon','lmm').'">';
							if ($row['micon'] != null) {
								echo '<img src="' . $defaults_marker_icon_url . '/'.esc_html($row['micon']).'" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="'.esc_html($row['micon']).'" />';
							} else {
								echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';
							};
							echo '</td>';
							echo '
						' . $column_address . '
						' . $column_popuptext . '
						' . $column_layer_name . '
						' . $column_openpopup . '
						' . $column_coordinates . '
						' . $column_mapsize . '
						' . $column_zoom . '
						' . $column_basemap . '
						' . $column_createdby . '
						' . $column_createdon . '
						' . $column_updatedby . '
						' . $column_updatedon . '
						' . $column_controlbox . '
						' . $column_shortcode . '
						' . $column_kml . '
						' . $column_fullscreen . '
						' . $column_qr_code . '
						' . $column_geojson . '
						' . $column_georss . '
						' . $column_wikitude . '
						  </tr>';
					}//info: end foreach
				}
				?>
			</tbody>
		</table>
		<?php echo $pager; ?>
			<p>
			<?php
			$addmarker_link_visibility = ($multi_layer_map == 0) ? 'display:inline;' : 'display:none;';
			echo '<a class="addmarker_link" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $id . '" style="text-decoration:none;' . $addmarker_link_visibility . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-add.png" /> ' . __('add new marker to this layer','lmm') . '</a>';
			?>
			</p>
		</span><!--assigned-markers-table-->
		<?php } //end $isedit ?>
		<!--isedit-->
		<?php if(!empty($layer_marker_list_table )){ ?>
			<table cellspacing="0" style="width:auto;margin-top:20px;border-radius:5px;" class="wp-list-table widefat fixed bookmarks">
			<tr><td>
			<p><b><?php _e('Bulk actions for selected markers','lmm') ?></b></p>
			<?php
			if (current_user_can( $lmm_options[ 'capabilities_delete_others' ])) {
				$deleteselected_visibility = '';
				$deleteselected_infotext = '';
			} else {
				$deleteselected_visibility = 'disabled="disabled"';
				$deleteselected_infotext = __('Your user does not have the right to perform this action','lmm');
			} ?>
	        <input <?php echo $deleteselected_visibility; ?> type="radio" id="deleteselected" name="bulkactions-markers" value="deleteselected" /> <label for="deleteselected" title="<?php echo $deleteselected_infotext; ?>"><?php _e('delete','lmm') ?></label>
			<input id="bulk-actions-btn" class="button-secondary" type="submit" value="<?php _e('submit', 'lmm') ?>" style="margin: 0 0 0 18px;" disabled="disabled" />
			</td></tr></table>
		<?php } ?>
		</form>
	<script type="text/javascript">
	/* //<![CDATA[ */
	var marker,selectlayer,googleLayer_roadmap,googleLayer_satellite,googleLayer_hybrid,googleLayer_terrain,bingaerial,bingaerialwithlabels,bingroad,osm_mapnik,stamen_terrain,stamen_toner,stamen_watercolor,mapquest_osm,mapquest_aerial,mapquest_hybrid,ogdwien_basemap,ogdwien_satellite,mapbox,mapbox2,mapbox3,custom_basemap,custom_basemap2,custom_basemap3,empty_basemap,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl,markercluster,geojson_markers,filter_controlbox;
	var markerID = {};

	(function($) {
		jQuery('.lmm-mlm-layers-table .toggle-row').click(function(){
		   jQuery(this).closest('tr').toggleClass('is-expanded');
		   jQuery(this).parent().toggleClass('dynamic_border');
		});
		//info: enable submit button on bulk action selection
		$('#form-list-markers input[name="bulkactions-markers"]').click(function () {
			document.getElementById('bulk-actions-btn').disabled = false;
		});
		//info: show confirm alert for delete bulk action
		$('#form-list-markers').submit(function (e) {
			if($('input[name="bulkactions-markers"]:checked').val() == 'deleteselected')
			{
				if (confirm('<?php esc_attr_e('Do you really want to delete the selected marker(s)? (cannot be undone)','lmm') ?>')) {
					e.preventDefault();
					var checkedmarkers =  	jQuery('input[name^="checkedmarkers"]').map(function() {
											  if(jQuery(this).is(":checked")) return this.value;
											}).get();
					$.ajax({
						url:ajaxurl,
						data: {
							action: 'mapsmarker_ajax_actions_backend',
							lmm_ajax_subaction: 'markers-bulk-delete',
							lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
							checkedmarkers:  checkedmarkers,
						},
						method:'POST',
						success: function(response){
							var results = response.replace(/^\s*[\r\n]/gm, '');
							var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
							var res = JSON.parse(results);
							if(res['status-class'] == 'notice notice-success'){
								$.each(checkedmarkers,function(i,e){
									$('#link-' + e).css('background','#ff0000');
									$('#link-' + e).hide('slow', function(){
										$('#link-' + e).remove();
									});
								});
							}
						}
					});
				} else {
					return false;
				}
			}
		});
		<?php
		$maxzoom = intval($lmm_options['global_maxzoom_level']);
		$dragging_setting = ($lmm_options['misc_map_dragging'] == 'false-touch') ? '!L.Browser.mobile' : esc_js($lmm_options['misc_map_dragging']);
		 //info: true for leaflet-fullscreen.php only
		if ($lmm_options['misc_map_scrollwheelzoom'] == 'true') {
			$scrollwheelzoom_setting = 'true';
		} else if ( ($lmm_options['misc_map_scrollwheelzoom'] == 'true-fullscreen-only') || ($lmm_options['misc_map_scrollwheelzoom'] == 'false') ){
			$scrollwheelzoom_setting = 'false';
		}
		?>
		var markers_counter = 0; //info: #328
		selectlayer = new L.Map("selectlayer", { dragging: <?php echo $dragging_setting ?>, touchZoom: <?php echo esc_js($lmm_options['misc_map_touchzoom']); ?>, scrollWheelZoom: <?php echo $scrollwheelzoom_setting ?>, doubleClickZoom: <?php echo esc_js($lmm_options['misc_map_doubleclickzoom']); ?>, boxzoom: <?php echo esc_js($lmm_options['map_interaction_options_boxzoom']); ?>, trackResize: <?php echo esc_js($lmm_options['misc_map_trackresize']); ?>, worldCopyJump: <?php echo esc_js($lmm_options['map_interaction_options_worldcopyjump']); ?>, closePopupOnClick: <?php echo esc_js($lmm_options['misc_map_closepopuponclick']); ?>, keyboard: <?php echo esc_js($lmm_options['map_keyboard_navigation_options_keyboard']); ?>, keyboardPanDelta: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardpandelta']) ?>, inertia: <?php echo esc_js($lmm_options['map_panning_inertia_options_inertia']); ?>, inertiaDeceleration: <?php echo intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) ?>, inertiaMaxSpeed: <?php echo intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) ?>, zoomControl: <?php echo $lmm_options['misc_map_zoomcontrol']; ?>, crs: <?php echo esc_js($lmm_options['misc_projections']); ?>, fullscreenControl: <?php echo esc_js($lmm_options['map_fullscreen_button']); ?>, tap: <?php echo esc_js($lmm_options['map_interaction_options_tap']); ?>, tapTolerance: <?php echo intval($lmm_options['map_interaction_options_taptolerance']) ?>, bounceAtZoomLimits: <?php echo esc_js($lmm_options['map_interaction_options_bounceatzoomlimits']); ?> });

		//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified") (Google Mutant)
		selectlayer._layersMaxZoom = <?php echo $maxzoom; ?>;

		<?php
			if ( $lmm_options['misc_backlinks'] == 'show' ) {
				$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
				$attrib_prefix = '<a tabindex=\"115\" href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Maps Marker Pro - #1 mapping plugin for WordPress','lmm') . '\">MapsMarker.com</a> (<a tabindex=\"116\" href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Maps Marker Pro') . '\">Leaflet</a>/<a tabindex=\"117\" href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Maps Marker Pro') . '\">icons</a>)';
			} else {
				$attrib_prefix = '';
			}
			$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a tabindex=\"119\" href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $layerviewlat . '&amp;lon=' . $layerviewlon . '&zoom=' . $layerzoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
			$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
			$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
			$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
			$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
			$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
		?>
		selectlayer.attributionControl.setPrefix("<?php echo $attrib_prefix; ?>");

		<?php
		//info: osm_mapnik js name+db entries not renamed due to backward compatibility reasons!
		$osm_attrib_general = __("Map",'lmm').': &copy; <a tabindex=\"123\" href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>';
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
			$osm_attribution = __("Map",'lmm').': &copy; <a tabindex=\"123\" href=\"https://www.openstreetmap.fr\" target=\"_blank\">Openstreetmap France</a> & <a tabindex=\"123\" href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
		} else if ($lmm_options['openstreetmap_variants'] == 'osm-hot') {
			$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
			$osm_maxNativeZoom = 20;
			$osm_attribution = $osm_attrib_general . ', ' . __("Tiles courtesy of","lmm") . ' <a tabindex=\"123\" href=\"https://hotosm.org/\" target=\"_blank\">Humanitarian OpenStreetMap Team</a>' . $osm_editlink;
		}
		//info: define edgeBuffertiles
		if ($lmm_options['map_interaction_options_bounceatzoomlimits'] != '0') {
			$edgebuffertiles = ', edgeBufferTiles: ' . floatval(str_replace(",",".", $lmm_options['edgeBufferTiles']));
		} else {
			$edgebuffertiles = '';
		}
		$error_tile_url = $lmm_options['basemaps_nowrap_enabled'] == 'true' ? '' : LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png';
		?>
		osm_mapnik = new L.TileLayer("<?php echo $osm_tile_url; ?>", {mmid: 'osm_mapnik', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo $osm_maxNativeZoom; ?>, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $osm_attribution; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/<?php echo esc_html($lmm_options[ 'stamen_terrain_flavor' ]); ?>/{z}/{x}/{y}.png", {mmid: 'stamen_terrain', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/<?php echo esc_html($lmm_options[ 'stamen_toner_flavor' ]); ?>/{z}/{x}/{y}.png", {mmid: 'stamen_toner', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 20, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {mmid: 'stamen_watercolor', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});

		<?php
		if ($lmm_options['mapquest_api_key'] != NULL) {
			echo 'if (typeof MQ !== "undefined") {';
			echo 'mapquest_osm = new MQ.mapLayer({mmid: "mapquest_osm"});';
			echo 'mapquest_aerial = new MQ.satelliteLayer({mmid: "mapquest_aerial"});';
			echo 'mapquest_hybrid = new MQ.hybridLayer({mmid: "mapquest_hybrid"});';
			echo '} else { alert("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); }'.PHP_EOL;
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
					echo 'var deferred_google_layers = {
									roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
									satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
									hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0}); }},
									terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0}); }}
							};
							var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
							var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
							var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
							var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					echo 'var googleLayer_roadmap = new L.gridLayer.googleMutant({type: "roadmap", mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					echo 'var googleLayer_satellite = new L.gridLayer.googleMutant({type: "satellite", mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					echo 'var googleLayer_hybrid = new L.gridLayer.googleMutant({type: "hybrid", mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					echo 'var googleLayer_terrain = new L.gridLayer.googleMutant({type: "terrain", mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
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
					echo 'var deferred_google_layers = {
									roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
									satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
									hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
									terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }}
							};
							var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
							var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
							var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
							var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					echo 'var googleLayer_roadmap = new L.Google("ROADMAP", {mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					echo 'var googleLayer_satellite = new L.Google("SATELLITE", {mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					echo 'var googleLayer_hybrid = new L.Google("HYBRID", {mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					echo 'var googleLayer_terrain = new L.Google("TERRAIN", {mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				}
			}
		}
		?>

		<?php if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) { ?>
			bingaerial = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingaerial', type: 'Aerial', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: <?php echo $error_tile_url ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
			bingaerialwithlabels = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingaerialwithlabels', type: 'AerialWithLabels', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: <?php echo $error_tile_url ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
			bingroad = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingroad', type: 'Road', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: <?php echo $error_tile_url ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		<?php }; ?>
		ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {mmid: 'ogdwien_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, attribution: "<?php echo $attrib_basemapat; ?>", subdomains: ['maps1', 'maps2', 'maps3', 'maps4'], detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_satellite', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, attribution: "<?php echo $attrib_basemapat; ?>", subdomains: ['maps1', 'maps2', 'maps3', 'maps4'], detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		//info: MapBox basemaps
		<?php
		if ($lmm_options[ 'mapbox_access_token' ] != NULL) {
			echo 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox_access_token' ])) . '&secure=1", {mmid: "mapbox", minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		} else {  //info: v3 fallback for default maps
			echo 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {mmid: "mapbox", minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		}
		if ($lmm_options[ 'mapbox2_access_token' ] != NULL) {
			echo 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox2_access_token' ])) . '&secure=1", {mmid: "mapbox2", minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		} else {
			echo 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {mmid: "mapbox2", minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		}
		if ($lmm_options[ 'mapbox3_access_token' ] != NULL) {
			echo 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox3_access_token' ])) . '&secure=1", {mmid: "mapbox3", minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		} else {
			echo 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {mmid: "mapbox3", minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		}
		?>
		//info: check if subdomains are set for custom basemaps
		<?php
		$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		?>
		custom_basemap = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) ?>", {mmid: 'custom_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, tms: <?php echo esc_js($lmm_options[ 'custom_basemap_tms' ]); ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap_continuousworld_enabled' ]; ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap_nowrap_enabled' ]; ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>});
		custom_basemap2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, tms: <?php echo esc_js($lmm_options[ 'custom_basemap2_tms' ]); ?>, <?php echo $error_tile_url_custom_basemap2; ?>attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap2_continuousworld_enabled' ]; ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap2_nowrap_enabled' ]; ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>});
		custom_basemap3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, tms: <?php echo esc_js($lmm_options[ 'custom_basemap3_tms' ]); ?>, <?php echo $error_tile_url_custom_basemap3; ?>attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap3_continuousworld_enabled' ]; ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap3_nowrap_enabled' ]; ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>});
		empty_basemap = new L.TileLayer("", {mmid: 'empty_basemap'});

		//info: check if subdomains are set for custom overlays
		<?php
		$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
		$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		?>

		overlays_custom = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) ?>", {olid: 'overlays_custom', tms: <?php echo $lmm_options[ 'overlays_custom_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom_minzoom' ]) ?><?php echo $overlays_custom_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		overlays_custom2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) ?>", {olid: 'overlays_custom2', tms: <?php echo $lmm_options[ 'overlays_custom2_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom2; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom2_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom2_minzoom' ]) ?><?php echo $overlays_custom2_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		overlays_custom3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) ?>", {olid: 'overlays_custom3', tms: <?php echo $lmm_options[ 'overlays_custom3_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom3; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom3_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom3_minzoom' ]) ?><?php echo $overlays_custom3_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		overlays_custom4 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) ?>", {olid: 'overlays_custom4', tms: <?php echo $lmm_options[ 'overlays_custom4_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom4; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom4_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom4_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom4_minzoom' ]) ?><?php echo $overlays_custom4_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		//info: check if subdomains are set for wms layers
		<?php
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
		$wms_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms2_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms2_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms2_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms2_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms3_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms3_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms3_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms3_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms4_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms4_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms4_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms4_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms5_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms5_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms5_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms5_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms6_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms6_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms6_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms6_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms7_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms7_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms7_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms7_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms8_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms8_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms8_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms8_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms9_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms9_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms9_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms9_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms10_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms10_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms10_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms10_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		?>

		//info: define wms layers
		wms = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) ?>", {wmsid: 'wms', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ]))?>', attribution: '<?php echo $wms_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ]))?>'<?php echo $wms_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms2 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) ?>", {wmsid: 'wms2', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ]))?>', attribution: '<?php echo $wms2_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms2_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ]))?>'<?php echo $wms2_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms3 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) ?>", {wmsid: 'wms3', layers: '<?php echo htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ])))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ]))?>', attribution: '<?php echo $wms3_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms3_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ]))?>'<?php echo $wms3_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms4 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) ?>", {wmsid: 'wms4', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ]))?>', attribution: '<?php echo $wms4_attribution ?>', transparent: '<?php echo $lmm_options[ 'wms_wms4_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ]))?>'<?php echo $wms4_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms5 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) ?>", {wmsid: 'wms5', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ]))?>', attribution: '<?php echo $wms5_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms5_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ]))?>'<?php echo $wms5_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms6 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) ?>", {wmsid: 'wms6', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ]))?>', attribution: '<?php echo $wms6_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms6_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ]))?>'<?php echo $wms6_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms7 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) ?>", {wmsid: 'wms7', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ]))?>', attribution: '<?php echo $wms7_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms7_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ]))?>'<?php echo $wms7_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms8 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) ?>", {wmsid: 'wms8', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ]))?>', attribution: '<?php echo $wms8_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms8_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ]))?>'<?php echo $wms8_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms9 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) ?>", {wmsid: 'wms9', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ]))?>', attribution: '<?php echo $wms9_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms9_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ]))?>'<?php echo $wms9_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		wms10 = new L.TileLayer.WMS("<?php echo  htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) ?>", {wmsid: 'wms10', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ]))?>', attribution: '<?php echo $wms10_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms10_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ]))?>'<?php echo $wms10_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});

		//info: controlbox - define basemaps
		layersControl = new L.Control.Layers(
		{
		<?php
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
			echo substr($basemaps_available, 0, -1);
		?>
		},

		//info: controlbox - add available overlays
		{
		<?php
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
			echo substr($overlays_custom_available, 0, -1);
		?>
		},
		{
		//info: set controlbox visibility 1/2
		collapsed: true
		});
		selectlayer.setView(new L.LatLng(<?php echo $layerviewlat . ', ' . $layerviewlon; ?>), <?php echo $layerzoom ?>);


		selectlayer.addControl(layersControl);
		selectlayer.addLayer(<?php echo $basemap; ?>);

		//info: controlbox - add active overlays on layer level
		<?php
			if ( $wms == 1 )
				echo "selectlayer.addLayer(wms);".PHP_EOL;
			if ( $wms2 == 1 )
				echo "selectlayer.addLayer(wms2);".PHP_EOL;
			if ( $wms3 == 1 )
				echo "selectlayer.addLayer(wms3);".PHP_EOL;
			if ( $wms4 == 1 )
				echo "selectlayer.addLayer(wms4);".PHP_EOL;
			if ( $wms5 == 1 )
				echo "selectlayer.addLayer(wms5);".PHP_EOL;
			if ( $wms6 == 1 )
				echo "selectlayer.addLayer(wms6);".PHP_EOL;
			if ( $wms7 == 1 )
				echo "selectlayer.addLayer(wms7);".PHP_EOL;
			if ( $wms8 == 1 )
				echo "selectlayer.addLayer(wms8);".PHP_EOL;
			if ( $wms9 == 1 )
				echo "selectlayer.addLayer(wms9);".PHP_EOL;
			if ( $wms10 == 1 )
				echo "selectlayer.addLayer(wms10);".PHP_EOL;
		?>

		//info: controlbox - check active overlays on layer level
		<?php
			if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
				echo "selectlayer.addLayer(overlays_custom);".PHP_EOL;
			if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
				echo "selectlayer.addLayer(overlays_custom2);".PHP_EOL;
			if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
				echo "selectlayer.addLayer(overlays_custom3);".PHP_EOL;
			if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
				echo "selectlayer.addLayer(overlays_custom4);".PHP_EOL;
		?>
		layersControl._container.className += " controlbox_basemaps";
		<?php //info: add minimap
		if ($lmm_options['minimap_status'] != 'hidden') {
			echo 'var osm_mapnik_minimap = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
			echo 'var stamen_terrain_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_terrain_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
			echo 'var stamen_toner_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_toner_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
			echo 'var stamen_watercolor_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
			//info: MapQuest minimap
			if ($lmm_options['mapquest_api_key'] != NULL) {
				echo 'if (typeof MQ !== "undefined") {';
				echo 'mapquest_osm_minimap = new MQ.mapLayer();';
				echo 'mapquest_aerial_minimap = new MQ.satelliteLayer();';
				echo 'mapquest_hybrid_minimap = new MQ.hybridLayer();';
				echo '}'.PHP_EOL;
			}
			//info: google maps minimap
			if ($lmm_options['google_maps_api_status'] == 'enabled') {

				if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

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
						echo 'var deferred_google_layers = {
										roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0}); }},
										satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0}); }},
										hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0}); }},
										terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0}); }}
								};
								var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers.roadmap);
								var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers.satellite);
								var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers.hybrid);
								var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
					} else { //info: undeferred loading
						echo 'var googleLayer_roadmap_minimap = new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
						echo 'var googleLayer_satellite_minimap = new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
						echo 'var googleLayer_hybrid_minimap = new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
						echo 'var googleLayer_terrain_minimap = new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
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
					echo 'var deferred_google_layers = {
									roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
									satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
									hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
									terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }}
							};
							var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers.roadmap);
							var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers.satellite);
							var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers.hybrid);
							var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
					} else { //info: undeferred loading
						echo 'var googleLayer_roadmap_minimap = new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
						echo 'var googleLayer_satellite_minimap = new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
						echo 'var googleLayer_hybrid_minimap = new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
						echo 'var googleLayer_terrain_minimap = new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					}
				}
			}
			//info: bing minimaps
			if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
				echo 'var bingaerial_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
				echo 'var bingaerialwithlabels_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
				echo 'var bingroad_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
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
			echo "var miniMap = new L.Control.MiniMap(" . $minimap_basemap . ", {position: '" . esc_js($lmm_options['minimap_position']) . "', width: " . intval($lmm_options['minimap_width']) . ", height: " . intval($lmm_options['minimap_height']) . ", collapsedWidth: " . intval($lmm_options['minimap_collapsedWidth']) . ", collapsedHeight: " . intval($lmm_options['minimap_collapsedHeight']) . ", zoomLevelOffset: " . intval($lmm_options['minimap_zoomLevelOffset']) . ", " . $zoomlevelfixed . " zoomAnimation: " . esc_js($lmm_options['minimap_zoomAnimation']) . ", toggleDisplay: " . esc_js($lmm_options['minimap_toggleDisplay']) . ", autoToggleDisplay: " . esc_js($lmm_options['minimap_autoToggleDisplay']) . ", minimized: " . $minimap_minimized . "}).addTo(selectlayer);".PHP_EOL;
		} ?>

		//info: gpx tracks
		<?php if ($gpx_url != NULL) {
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
			echo 'function display_gpx_selectlayer() {
						var gpx_panel = document.getElementById("gpx-panel-selectlayer");
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
						}).addTo(selectlayer);
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
			//info: to prevent console XML parsing errors
			if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
				echo 'display_gpx_selectlayer();'.PHP_EOL;
			}
		}
		?>

		//info: add scale control
		<?php if ( $lmm_options['map_scale_control'] == 'enabled' ) { ?>
		L.control.scale({position:'<?php echo esc_js($lmm_options['map_scale_control_position']); ?>', maxWidth: <?php echo intval($lmm_options['map_scale_control_maxwidth']) ?>, metric: <?php echo esc_js($lmm_options['map_scale_control_metric']); ?>, imperial: <?php echo esc_js($lmm_options['map_scale_control_imperial']); ?>, updateWhenIdle: <?php echo esc_js($lmm_options['map_scale_control_updatewhenidle']); ?>}).addTo(selectlayer);
		<?php }; ?>

		//info: add geolocate control
		<?php
		if ($lmm_options['geolocate_status'] == 'true') {
			if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) { $onlocationerror = ', onLocationError: function () {}'; } else { $onlocationerror = ''; }
			//info: prepare geolocate setView
			if ($lmm_options[ 'geolocate_setView' ] == 'false') {
				$geolocate_setview = "false";
			} else {
				$geolocate_setview = "'" . esc_js($lmm_options[ 'geolocate_setView' ]) . "'";
			}
			echo "var locatecontrol_selectlayer = L.control.locate({
					position: '" . esc_js($lmm_options[ 'geolocate_position' ]) . "',
					drawCircle: " . esc_js($lmm_options[ 'geolocate_drawCircle' ]) . ",
					drawMarker: " . esc_js($lmm_options[ 'geolocate_drawMarker' ]) . ",
					setView: " . $geolocate_setview . ",
					keepCurrentZoomLevel: " . esc_js($lmm_options[ 'geolocate_keepCurrentZoomLevel' ]) . ",
					clickBehavior: {
						inView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_inView' ]) . "',
						outOfView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_outOfView' ]) . "'
					},
					circleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_circleStyle' ])) . "},
					markerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_markerStyle' ])) . "},
					followCircleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followCircleStyle' ])) . "},
					followMarkerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followMarkerStyle' ])) . "},
					icon: '" . esc_js($lmm_options[ 'geolocate_icon' ]) . "',
					circlePadding: " . esc_js($lmm_options[ 'geolocate_circlePadding' ]) . ",
					metric: " . esc_js($lmm_options[ 'geolocate_units' ]) . ",
					showPopup: " . esc_js($lmm_options[ 'geolocate_showPopup' ]) . ",
					strings: {
						title: '" . __('Show me where I am','lmm') . "',
						metersUnit: '" . __('meters','lmm') . "',
						feetUnit: '" . __('feet','lmm') . "',
						popup: '" . sprintf(__('You are within %1$s %2$s from this point','lmm'), '{distance}', '{unit}') . "',
						outsideMapBoundsMsg: '" . __('You seem located outside the boundaries of the map','lmm') . "'
					},
					locateOptions: { " . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_locateOptions' ])) . " }" . $onlocationerror . "
				}).addTo(selectlayer);".PHP_EOL;
			if ( $lmm_options['geolocate_autostart'] == 'true' ) {
				echo "locatecontrol_selectlayer.start();";
			}
		}
		?>

		mapcentermarker = new L.Marker(new L.LatLng(<?php echo $layerviewlat . ', ' . $layerviewlon; ?>),{ title: '<?php esc_attr_e('use this pin to center the layer (will only be shown in the admin area)','lmm'); ?>', interactive: true, draggable: true, zIndexOffset: 1000, opacity: 0.6 });
		mapcentermarker.options.icon = new L.Icon({iconUrl:'<?php echo LEAFLET_PLUGIN_URL . 'inc/img/icon-layer-center.png' ?>',iconSize: [32, 37],iconAnchor: [17, 37],shadowUrl: ''});
		<?php if (($isedit) && ($multi_layer_map == 0)) {
				echo 'mapcentermarker.bindPopup("<a class=\"addmarker_link\" target=\"_blank\" href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer='.$id.'&lat=' . $layerviewlat . '&lon=' . $layerviewlon . '&zoom=' . $layerzoom . '\" style=\"text-decoration:none;\">' . __('add new marker here','lmm') . '</a>",
							{
							autoPan: true,
							closeButton: true,
							autoPanPadding: new L.Point(5,5)
							});';
		} ?>
		mapcentermarker.addTo(selectlayer);
		var layers = {};
		var geojsonObj, mapIcon, marker_title;
		<?php
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
		?>
		<?php
		//info: use layerSupport plugin to support filters with cluster groups
		$disable_clustering_at_zoom = intval($lmm_options['clustering_disableClusteringAtZoom']) == 0 ? 'null' : intval($lmm_options['clustering_disableClusteringAtZoom']);
		if(isset($filter_details) && $filter_details){ ?>
			window.markercluster_selectlayer = new L.markerClusterGroup.layerSupport({ zoomToBoundsOnClick: <?php echo esc_js($lmm_options['clustering_zoomToBoundsOnClick']); ?>, showCoverageOnHover: <?php echo esc_js($lmm_options['clustering_showCoverageOnHover']); ?>, spiderfyOnMaxZoom: <?php echo esc_js($lmm_options['clustering_spiderfyOnMaxZoom']); ?>, animateAddingMarkers: <?php echo esc_js($lmm_options['clustering_animateAddingMarkers']); ?>, disableClusteringAtZoom: <?php echo $disable_clustering_at_zoom; ?>, maxClusterRadius: <?php echo intval($lmm_options['clustering_maxClusterRadius']) ?>, polygonOptions: {<?php echo $polygon_options ?>}, singleMarkerMode: <?php echo esc_js($lmm_options['clustering_singleMarkerMode']); ?>, spiderfyDistanceMultiplier: <?php echo intval($lmm_options['clustering_spiderfyDistanceMultiplier']) ?>, spiderLegPolylineOptions: {<?php echo $spiderLeg_polyline_options ?>}, chunkedLoading: true, chunkProgress: updateMarkersLoading, animate: <?php echo $lmm_options['clustering_animate']; ?> });
			var overlays_filters = [];
		<?php }else{ ?>
			window.markercluster_selectlayer = new L.MarkerClusterGroup({ zoomToBoundsOnClick: <?php echo esc_js($lmm_options['clustering_zoomToBoundsOnClick']); ?>, showCoverageOnHover: <?php echo esc_js($lmm_options['clustering_showCoverageOnHover']); ?>, spiderfyOnMaxZoom: <?php echo esc_js($lmm_options['clustering_spiderfyOnMaxZoom']); ?>, animateAddingMarkers: <?php echo esc_js($lmm_options['clustering_animateAddingMarkers']); ?>, disableClusteringAtZoom: <?php echo $disable_clustering_at_zoom; ?>, maxClusterRadius: <?php echo intval($lmm_options['clustering_maxClusterRadius']) ?>, polygonOptions: {<?php echo $polygon_options ?>}, singleMarkerMode: <?php echo esc_js($lmm_options['clustering_singleMarkerMode']); ?>, spiderfyDistanceMultiplier: <?php echo intval($lmm_options['clustering_spiderfyDistanceMultiplier']) ?>, spiderLegPolylineOptions: {<?php echo $spiderLeg_polyline_options ?>}, chunkedLoading: true, chunkProgress: updateMarkersLoading });
		<?php } ?>

		//info: markercluster loading indicator
		function updateMarkersLoading(processed, total, elapsed, layersArray) {
			if (elapsed > 0) {
				markersLoading.style.display = 'block';
			}
			if (processed === total) {
				markersLoading.style.display = 'none';
			}
		}
		<?php
			if ($id != NULL) { //info: dont load geojson.php on new layer maps to save mysql queries+http requests
				echo 'var markersLoading = document.getElementById("selectlayer-loading");';
				echo 'markersLoading.style.display = "block";'.PHP_EOL;
				if ($multi_layer_map == 0) { $id_for_geojson_url = $id; } else { $id_for_geojson_url = $multi_layer_map_list; }
				echo 'var xhReq = new XMLHttpRequest();'.PHP_EOL;
				echo 'xhReq.open("GET", "' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $id_for_geojson_url . '/?listmarkers=1') . '", true);'.PHP_EOL; //info: always user listmarkers=1 for dynamic preview
				echo 'xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) {'.PHP_EOL; //info: async 1/2
				//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity; try/catch needed for multi-layer-maps without checked layers (unexpected token error otherwise)
				echo 'if (xhReq.responseText.indexOf(\'{"type"\') != 0) {
							var position = xhReq.responseText.indexOf(\'{"type"\');
							try {
								geojsonObj = JSON.parse(xhReq.responseText.slice(position));
								markersLoading.style.display = "none";
							} catch (e) {
								console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message);
							}
					  } else {
							try {
								geojsonObj = JSON.parse(xhReq.responseText);
								markersLoading.style.display = "none";
							} catch (e) {
								console.log("' . esc_attr__('Error - invalid GeoJSON object:','lmm') . ' "+e.message);
							}
					}'.PHP_EOL;

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
					?>

					geojson_markers = L.geoJson(geojsonObj, {
						onEachFeature: function(feature, marker) {

							//info: marker tooltips
							<?php if ($lmm_options[ 'marker_tooltip_status' ] == 'enabled') {
									echo "if (feature.properties.markername != '') { marker.bindTooltip(feature.properties.markername, {offset: L.point(" . intval($lmm_options[ 'marker_tooltip_offset_x' ]) . "," . intval($lmm_options[ 'marker_tooltip_offset_y' ]) . "), direction: '" . esc_js($lmm_options[ 'marker_tooltip_direction' ]) . "', permanent: " . esc_js($lmm_options[ 'marker_tooltip_permanent' ]) . ", sticky: " . esc_js($lmm_options[ 'marker_tooltip_sticky' ]) . ", interactive: " . esc_js($lmm_options[ 'marker_tooltip_interactive' ]) . ", opacity: " . str_replace(',', '.', floatval($lmm_options[ 'marker_tooltip_opacity' ])) . "});}".PHP_EOL;
							}?>

							<?php if($lmm_options['defaults_marker_popups_rise_on_hover'] == 'true'){ ?>
								marker.on("mouseover", function (e) {
						   			this.openPopup();
						 	    });
							<?php  } ?>
							markerID[feature.properties.markerid] = marker;
							var requested_layer = feature.properties.requested_layer;
							<?php //info: assign markers to filters ?>
							<?php if($filter_details){ ?>
									if (requested_layer.length > 0){
										jQuery.each(requested_layer, function(index, layer){
											if(typeof filtered_layers[layer] != "object"){
												filtered_layers[layer] = L.layerGroup();
												filtered_layers[layer]["layer_id"] = layer;
												filtered_layers[layer]["markercount"] = non_filterbox_layers[layer];
												filtered_layers[layer]["non_filterbox"] = true;
											}
											marker.addTo(filtered_layers[layer]);
											if(typeof filter_details[layer] !="undefined"){
												filtered_layers[layer]["status"] = filter_details[layer]["status"];
												filtered_layers[layer]["name"] = filter_details[layer]["name"];
												filtered_layers[layer]["markercount"] = filter_details[layer]["markercount"];
												filtered_layers[layer]["layer_id"] = layer;
												filtered_layers[layer]["non_filterbox"] = false;
											}
										});
									}
							<?php } ?>
						<?php
						if ($lmm_options['directions_popuptext_panel'] == 'yes') {
							echo 'if (feature.properties.text != "") { var css = "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;"; } else { var css = ""; }'.PHP_EOL;
							if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
								echo 'if (feature.properties.markername != "") { var divmarkername1 = "<div class=\"popup-markername\"  style=\"border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;\">"; var divmarkername2 = "</div>" } else { var divmarkername1 = ""; var divmarkername2 = ""; }'.PHP_EOL;
								echo 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+divmarkername1+feature.properties.markername+divmarkername2+feature.properties.text+"<div class=\"popup-directions\" style=\""+css+"\">"+feature.properties.address+" <a href=\""+feature.properties.dlink+"\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a></div></div>", {'.PHP_EOL;
							} else {
								echo 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+feature.properties.text+"<div class=\"popup-directions\" style=\""+css+"\">"+feature.properties.address+" <a href=\""+feature.properties.dlink+"\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a></div></div>", {'.PHP_EOL;
							}
								echo 'maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ','.PHP_EOL;
								echo 'minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ','.PHP_EOL;
								echo 'maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ','.PHP_EOL;
								echo 'autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ','.PHP_EOL;
								echo 'closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ','.PHP_EOL;
								echo 'autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
							echo '});'.PHP_EOL;
						} else {
							echo 'if (feature.properties.text != "") {'.PHP_EOL;
							if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
								echo 'if (feature.properties.markername != "") { var divmarkername1 = "<div class=\"popup-markername\"  style=\"border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;\">"; var divmarkername2 = "</div>" } else { var divmarkername1 = ""; var divmarkername2 = ""; }'.PHP_EOL;
								echo 'if ( (feature.properties.text != "") && (feature.properties.markername != "") ) {'; //info: to prevent empty popups
								echo 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+divmarkername1+feature.properties.markername+divmarkername2+feature.properties.text+"</div>", {'.PHP_EOL;
							} else {
								echo 'if (feature.properties.text != "") {'; //info: to prevent empty popups
								echo 'marker.bindPopup("<img id=\"popup-loading-"+feature.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\"'.LEAFLET_PLUGIN_URL.'inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+feature.properties.markerid+"\">"+feature.properties.text+"</div>", {'.PHP_EOL;
							}
									echo 'maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ','.PHP_EOL;
									echo 'minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ','.PHP_EOL;
									echo 'maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ','.PHP_EOL;
									echo 'autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ','.PHP_EOL;
									echo 'closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ','.PHP_EOL;
									echo 'autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
								echo '});'.PHP_EOL;
							echo '}'.PHP_EOL;
							echo '}'.PHP_EOL;
						}
						?>
						},
						pointToLayer: function (feature, latlng) {
							mapIcon = L.icon({
								iconUrl: (feature.properties.icon != '') ? "<?php echo $defaults_marker_icon_url ?>/" + feature.properties.icon : "<?php echo LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' ?>",
								iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],
								iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],
								popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],
								shadowUrl: '<?php echo $marker_shadow_url; ?>',
								shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],
								shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],
								className: (feature.properties.icon == '') ? "lmm_marker_icon_default" : "lmm_marker_icon_"+ feature.properties.icon.slice(0,-4)
							});
							<?php if ( ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') && ($lmm_options[ 'marker_tooltip_status' ] == 'disabled') ) { ?>
								if (feature.properties.markername == '') { marker_title = ''; alt_title = ''; } else { marker_title = feature.properties.markername; alt_title = marker_title; };
							<?php } else { ; ?>
								marker_title = '';
								if (feature.properties.markername == '') { alt_title = '' } else { alt_title = feature.properties.markername };
							<?php } ?>
							return L.marker(latlng, {icon: mapIcon, interactive: true, title: marker_title, opacity: <?php echo floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) ?>, alt: alt_title });
						}
					});
					<?php if($filter_details){ ?>
						//info: add layers with markercount = 0
						jQuery.each(filter_details, function(lid, filter){
						  if(filter.status == "active"){
								called_layers[lid] = true;
								active_layers.push(lid);
						  }
						  if(filter.markercount == "0" && filter.status == "active"){
								filtered_layers[lid] = L.layerGroup();
								filtered_layers[lid]["status"] = filter.status;
								filtered_layers[lid]["name"] = filter.name;
								filtered_layers[lid]["layer_id"] = lid;
								filtered_layers[lid]["markercount"] = 0;
								called_layers[lid] = true;
								active_layers.push(lid);
							 }
						});
					<?php } ?>

					<?php
						if ($clustering == '1') {
							if($filter_details){
								//info: sort filters
								echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
								echo '		filtered_layers.sort(function(a, b){'.PHP_EOL;
								echo '		  	if(active_layers_orderby =="name"){'.PHP_EOL;
								echo '				b[active_layers_orderby] = b[active_layers_orderby].toLowerCase();'.PHP_EOL;
								echo ' 				a[active_layers_orderby] = a[active_layers_orderby].toLowerCase();'.PHP_EOL;
								echo '			}'.PHP_EOL;
								echo '			if(active_layers_order == "DESC"){'.PHP_EOL;
								echo '				if(b[active_layers_orderby] < a[active_layers_orderby])	return -1;'.PHP_EOL;
								echo '				else if(b[active_layers_orderby] > a[active_layers_orderby])	return 1;'.PHP_EOL;
								echo '				return 0;'.PHP_EOL;
								echo '			}else{'.PHP_EOL;
								echo '				if(b[active_layers_orderby] < a[active_layers_orderby])	return 1;'.PHP_EOL;
								echo '				else if(b[active_layers_orderby] > a[active_layers_orderby])	return -1;'.PHP_EOL;
								echo '				return 0;'.PHP_EOL;
								echo ' 		}'.PHP_EOL;
								echo '		});'.PHP_EOL;
								echo ' }'.PHP_EOL;
								//info: prepare html label for filters
								echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
								echo '		jQuery.each(filtered_layers, function(lid, group){'.PHP_EOL;
								echo '				try{'.PHP_EOL;
								echo '					if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter_details[group.layer_id].markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
								echo '						if(filter_details[group.layer_id]["icon"] != "" && filter_show_icon != "0"){  var filter_icon = "<img src=\'"+ filter_details[group.layer_id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
								echo '							if(filter_show_name != "0"){ var filter_name = filter_details[group.layer_id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
								echo '								group["markercount"] = filter_details[group.layer_id].markercount;'.PHP_EOL;
								echo '								overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name + "</span>" + markercount   ] = group;'.PHP_EOL;
								echo '								if(group["status"] == "active"){'.PHP_EOL;
								echo '									if(called_layers[group.layer_id] == true){'.PHP_EOL;
								echo '										markercluster_selectlayer.addLayer(group);'.PHP_EOL;
								echo '									}'.PHP_EOL;
								echo '									called_layers[group.layer_id] = false;'.PHP_EOL;
								echo '						} '.PHP_EOL;
								echo '				}catch(n){}'.PHP_EOL;
								echo '		});'.PHP_EOL;
								echo '}'.PHP_EOL;
								//info: add inactive layers to the controlbox
								echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
								echo '		jQuery.each(ordered_filter_details, function(lid, filter){'.PHP_EOL;
								echo '			if(filter["status"] == "inactive"){'.PHP_EOL;
								echo '				filtered_layers[filter.id] = L.layerGroup();'.PHP_EOL;
								echo ' 				filtered_layers[filter.id]["layer_id"] =filter.id;'.PHP_EOL;
								echo ' 				filtered_layers[filter.id]["markercount"] = filter.markercount;'.PHP_EOL;
								echo '				if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter.markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
								echo '				if(filter_details[filter.id]["icon"] != "" && filter_show_icon != "0"){ var filter_icon = "<img class=\"mlm-filters-icon\" src=\'"+ filter_details[filter.id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
								echo '				if(filter_show_name != "0"){ var filter_name = filter_details[filter.id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
								echo '				overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name + "</span>" + markercount  ] = filtered_layers[filter.id];'.PHP_EOL;
								echo '			}'.PHP_EOL;
								echo '		});'.PHP_EOL;
								echo '}'.PHP_EOL;
								//info: set the controlbox on the map
								if ($filters_collapsed != 'hidden') {
									echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
									echo '		filter_controlbox = L.control.filters(null, overlays_filters, filters_options);'.PHP_EOL;
									echo '		filter_controlbox.addTo(selectlayer);'.PHP_EOL;
									echo '}'.PHP_EOL;
								}

								echo 'geojson_markers.addTo(window.markercluster_selectlayer);'.PHP_EOL;
								echo 'selectlayer.addLayer(window.markercluster_selectlayer);'.PHP_EOL;
								//info: make ajax calls when a filter clicked
								echo 'selectlayer.on("overlayadd",function(){'.PHP_EOL;
									echo 'jQuery(".lmm-filter").click(function(){'.PHP_EOL;
									echo '		var layer_id = jQuery(this).attr("id");'.PHP_EOL;
									echo '		if(jQuery(this).is(":checked")){'.PHP_EOL;
									echo '         if(called_layers[layer_id] !== true && active_layers.indexOf(layer_id) == -1){'.PHP_EOL;
									echo '				'.PHP_EOL;
									echo '				xhReq.open("GET","' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/"+ layer_id +"/?full=no&full_icon_url=no&listmarkers='. $listmarkers) . '",true);'.PHP_EOL;
									echo '				xhReq.send();'.PHP_EOL;
									echo '				called_layers[layer_id] = true;'.PHP_EOL;
									echo '				window["mmp_no_controlbox"] = true;'.PHP_EOL;
									echo '			}'.PHP_EOL;
									//info: add markers rows to the markers list
									if($listmarkers == 1){
										echo '			jQuery.each(filtered_layers,function(index,l){'.PHP_EOL;
										echo '				if(typeof l != "undefined"){'.PHP_EOL;
										echo '					if(l.layer_id == layer_id){'.PHP_EOL;
										echo '						window["current_called_layer"] = index;'.PHP_EOL;
										echo '						lmm_add_to_list();'.PHP_EOL;
										echo '					}'.PHP_EOL;
										echo '				}'.PHP_EOL;
										echo '			});'.PHP_EOL;
										echo '			mmp_calculate_total_markers_admin();'.PHP_EOL;
										//info: re-draw pagination
										echo '			mmp_debounce(mmp_askForMarkersFromPagination(null, 1, "admin"),100);'.PHP_EOL;
										echo '		}else{'.PHP_EOL;
										echo '			mmp_calculate_total_markers_admin();'.PHP_EOL;
										//info: re-draw pagination
										echo '			mmp_debounce(mmp_askForMarkersFromPagination(null, 1, "admin"),100);'.PHP_EOL;
										echo '			jQuery.each(filtered_layers,function(index,l){'.PHP_EOL;
										echo '				if(typeof l != "undefined"){'.PHP_EOL;
										echo '					if(l.layer_id == layer_id){'.PHP_EOL;
										echo '						jQuery.each(filtered_layers[index]._layers,function(i,marker){'.PHP_EOL;
										echo '						 	jQuery("#marker_" + marker.feature.properties.markerid ).hide();'.PHP_EOL;
										echo '						});'.PHP_EOL;
										echo '					}'.PHP_EOL;
										echo '				}'.PHP_EOL;
										echo '			});'.PHP_EOL;
									}
									echo '		}'.PHP_EOL;
									echo '});'.PHP_EOL;
								echo ' });'.PHP_EOL;
								echo 'selectlayer.fire("overlayadd");'.PHP_EOL;
								if($listmarkers == 1){
									//info: add layers to the markers list
									echo 'function lmm_add_to_list(){'.PHP_EOL;
									echo '	if(typeof window["current_called_layer"] != "undefined"){'.PHP_EOL;
									echo '		jQuery.each(filtered_layers[window["current_called_layer"]]._layers,function(index,marker){'.PHP_EOL;
									echo '	 		if(marker.hasOwnProperty("feature")){'.PHP_EOL;
									echo '	 			if(marker.feature.hasOwnProperty("properties")){'.PHP_EOL;
									echo '	 				if(marker.feature.properties.hasOwnProperty("html_row")){'.PHP_EOL;
									echo '						if(jQuery("#marker_" + marker.feature.properties.markerid).length != 0){;'.PHP_EOL;
									echo '							jQuery("#marker_" + marker.feature.properties.markerid).show();'.PHP_EOL;
									echo '						}else{'.PHP_EOL;
									echo '							var markerid = marker.feature.properties.markerid;'.PHP_EOL;
									echo '							if(marker._icon!= null){'.PHP_EOL;
									echo '								var icon_url = marker._icon.src;'.PHP_EOL;
									echo '							}else{'.PHP_EOL;
									echo '								var icon_url = "";'.PHP_EOL;
									echo '							}'.PHP_EOL;
									echo '							var marker_dlink = marker.feature.properties.dlink;'.PHP_EOL;
									echo '							var marker_row =  marker.feature.properties.html_row.split("{uid}").join("admin");'.PHP_EOL;
									echo '							var marker_row_decoded =  jQuery("<textarea />").html(marker_row).text();'.PHP_EOL;
									echo '							if(jQuery("#lmm_listmarkers_table_admin").length == 0){'.PHP_EOL;
									echo '								jQuery("#lmm-listmarkers").after("<table style=\"width:'.$layer_marker_list_width.';\" id=\"lmm_listmarkers_table_admin\"  class=\"lmm-listmarkers-table\">").append(marker_row_decoded);'.PHP_EOL;
									echo '							}else{'.PHP_EOL;
									echo '								jQuery("#pagination_row_admin").before(marker_row_decoded);'.PHP_EOL;
									echo '							}'.PHP_EOL;
									echo '						}'.PHP_EOL;
									echo '					}'.PHP_EOL;
									echo '				}'.PHP_EOL;
									echo '		 	 }'.PHP_EOL;
									echo '		});'.PHP_EOL;
									echo '}}'.PHP_EOL;
									//info: call the function to add the markers of the ajax request.
									echo 'lmm_add_to_list();'.PHP_EOL;
									//info re-calculate toatal markers when the AJAX call finished.
									echo 'if(window["mmp_no_controlbox"] === true){'.PHP_EOL;
									echo '		mmp_calculate_total_markers_admin();'.PHP_EOL;
									echo '}'.PHP_EOL;
								}
							}else{
									echo 'geojson_markers.addTo(window.markercluster_selectlayer);'.PHP_EOL;
									echo 'selectlayer.addLayer(window.markercluster_selectlayer);';
							}
						//info: if no clustering
					} else {
						if($filter_details){
							//info: sort filters
							echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
							echo '		filtered_layers.sort(function(a, b){'.PHP_EOL;
							echo '		  	if(active_layers_orderby =="name"){'.PHP_EOL;
							echo '				b[active_layers_orderby] = b[active_layers_orderby].toLowerCase();'.PHP_EOL;
							echo ' 				a[active_layers_orderby] = a[active_layers_orderby].toLowerCase();'.PHP_EOL;
							echo '			}'.PHP_EOL;
							echo '			if(active_layers_order == "DESC"){'.PHP_EOL;
							echo '				if(b[active_layers_orderby] < a[active_layers_orderby])	return -1;'.PHP_EOL;
							echo '				else if(b[active_layers_orderby] > a[active_layers_orderby])	return 1;'.PHP_EOL;
							echo '				return 0;'.PHP_EOL;
							echo '			}else{'.PHP_EOL;
							echo '				if(b[active_layers_orderby] < a[active_layers_orderby])	return 1;'.PHP_EOL;
							echo '				else if(b[active_layers_orderby] > a[active_layers_orderby])	return -1;'.PHP_EOL;
							echo '				return 0;'.PHP_EOL;
							echo ' 		}'.PHP_EOL;
							echo '		});'.PHP_EOL;
							echo ' }'.PHP_EOL;
							//info: prepare html label for filters and add them to the controlbox
							echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
							echo '		jQuery.each(filtered_layers, function(lid, group){ '.PHP_EOL;
							echo '				try{ '.PHP_EOL;
							echo '					if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter_details[group.layer_id].markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
							echo '					if(filter_details[group.layer_id]["icon"] != "" && filter_show_icon != "0"){  var filter_icon = "<img src=\'"+ filter_details[group.layer_id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
							echo '					if(filter_show_name != "0"){ var filter_name = filter_details[group.layer_id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
							echo '					group["markercount"] = filter_details[group.layer_id].markercount;'.PHP_EOL;
							echo '					overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name  + "</span>" +  markercount  ] = group;'.PHP_EOL;
							echo '					if(group["status"] == "active"){'.PHP_EOL;
							echo '						if(called_layers[group.layer_id] == true){'.PHP_EOL;
							echo '							selectlayer.addLayer(group);'.PHP_EOL;
							echo '						}'.PHP_EOL;
							echo '						called_layers[group.layer_id] = false;'.PHP_EOL;
							echo '					}'.PHP_EOL;
							echo '				}catch(n){' .PHP_EOL;
							echo '							try{  '.PHP_EOL;
							echo '								jQuery.each(group._layers, function(i, marker) {'.PHP_EOL;
							echo '									marker.addTo(selectlayer);'.PHP_EOL;
							echo '								});'.PHP_EOL;
							echo '							}catch(n){  }'.PHP_EOL;
							echo '				}'.PHP_EOL;
							echo '		});'.PHP_EOL;
							echo '}'.PHP_EOL;
							//info: add inactive layers to the controlbox
							echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
							echo '		jQuery.each(ordered_filter_details, function(lid, filter){'.PHP_EOL;
							echo '			if(filter["status"] == "inactive"){'.PHP_EOL;
							echo '				filtered_layers[filter.id] = L.layerGroup();'.PHP_EOL;
							echo ' 			filtered_layers[filter.id]["layer_id"] =filter.id; '.PHP_EOL;
							echo ' 			filtered_layers[filter.id]["markercount"] = filter_details[filter.id].markercount; '.PHP_EOL;
							echo '				if(filter_show_markercount != "0"){ var markercount = "&nbsp;&nbsp;<span class=\"mlm-filters-markercount\" title=\"' . esc_attr__('number of markers','lmm') . '\">[ " + filter_details[filter.id].markercount + " ]</span>"; }else{ var markercount = ""; } '.PHP_EOL;
							echo '				if(filter_details[filter.id]["icon"] != "" && filter_show_icon != "0"){  var filter_icon = "<img src=\'"+ filter_details[filter.id]["icon"] +"\' />"; }else{ var filter_icon = "";  }'.PHP_EOL;
							echo '				if(filter_show_name != "0"){ var filter_name = filter_details[filter.id]["name"]; }else{ var filter_name = "";  }'.PHP_EOL;
							echo '				overlays_filters[ "<span class=\"mlm-filters-icon\">" + filter_icon + "</span><span class=\"mlm-filters-layername\">" + filter_name + "</span>" + markercount ] = filtered_layers[filter.id];'.PHP_EOL;
							echo '			}'.PHP_EOL;
							echo '		});'.PHP_EOL;
							echo '}'.PHP_EOL;
							//info: set the controlbox on the map
							if ($filters_collapsed != 'hidden') {
								echo ' if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
								echo '	filter_controlbox = L.control.filters(null, overlays_filters, filters_options);'.PHP_EOL;
								echo '	filter_controlbox.addTo(selectlayer);'.PHP_EOL;
								echo '}'.PHP_EOL;
							}
								echo 'if(window["mmp_no_controlbox"] !== true){'.PHP_EOL;
								echo 'jQuery(".lmm-filter").on("click", function(){'.PHP_EOL;
								echo '		if(jQuery(this).is(":checked")){'.PHP_EOL;
								echo '         var layer_id = jQuery(this).attr("id");'.PHP_EOL;
								echo '         if(called_layers[layer_id] !== true && active_layers.indexOf(layer_id) == -1){'.PHP_EOL;
								echo '				xhReq.open("GET", "' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/"+ layer_id +"/?full=no&full_icon_url=no&listmarkers='. $listmarkers) . '", true);'.PHP_EOL;
								echo '				xhReq.send(null);'.PHP_EOL;
								echo '				called_layers[layer_id] = true;'.PHP_EOL;
								echo '				window["mmp_no_controlbox"] = true;'.PHP_EOL;
								echo '			}'.PHP_EOL;
								echo '		}'.PHP_EOL;
								echo '		mmp_calculate_total_markers_admin();'.PHP_EOL;
								//info: re-draw pagination
								echo '		mmp_debounce(mmp_askForMarkersFromPagination(null, 1, "admin"),100);'.PHP_EOL;
								echo '});'.PHP_EOL;
								echo '}'.PHP_EOL;
								if($listmarkers == 1){
									//info: Dynamic markers list.
									echo 'selectlayer.on("layeradd",function(marker){'.PHP_EOL;
										echo 'if(typeof marker.layer.feature != "undefined"){'.PHP_EOL;
										echo '	 if(marker.hasOwnProperty("feature")){'.PHP_EOL;
										echo '	 	if(marker.feature.hasOwnProperty("properties")){'.PHP_EOL;
										echo '	 		if(marker.feature.properties.hasOwnProperty("html_row")){'.PHP_EOL;
										echo '				if(jQuery("#marker_" + marker.layer.feature.properties.markerid).length != 0){;'.PHP_EOL;
										echo '					jQuery("#marker_" + marker.layer.feature.properties.markerid).show();'.PHP_EOL;
										echo '				}else{'.PHP_EOL;
										echo '					var markerid = marker.layer.feature.properties.markerid;'.PHP_EOL;
										echo '					var icon_url = marker.layer._icon.src;'.PHP_EOL;
										echo '					var marker_dlink = marker.layer.feature.properties.dlink;'.PHP_EOL;
										echo '					var marker_row =  marker.layer.feature.properties.html_row.split("{uid}").join("admin");'.PHP_EOL;
										echo '					var marker_row_decoded =  jQuery("<textarea />").html(marker_row).text();'.PHP_EOL;
										echo '					if(jQuery("#lmm_listmarkers_table_admin").length == 0){'.PHP_EOL;
										echo '						jQuery("#lmm-listmarkers").after("<table style=\"width:'.($listmarkers == 1?$layer_marker_list_width:'100%').';\" id=\"lmm_listmarkers_table_admin\"  class=\"lmm-listmarkers-table\">").append(marker_row_decoded);'.PHP_EOL;
										echo '					}else{'.PHP_EOL;
										echo '						jQuery("#pagination_row_admin").before(marker_row_decoded);'.PHP_EOL;
										echo '					}'.PHP_EOL;
										echo '				}'.PHP_EOL;
										echo '	 	 	}'.PHP_EOL;
										echo '	  	}'.PHP_EOL;
										echo '	  }'.PHP_EOL;
										echo '}'.PHP_EOL;
									echo '});'.PHP_EOL;
									// TODO: (Search "leafletjs filters and markercluster") Needs working to remove both markers in and out clusters.
									echo 'selectlayer.on("layerremove",function(marker){'.PHP_EOL;
										echo 'if(typeof marker.layer.feature != "undefined"){'.PHP_EOL;
										echo '		jQuery("#marker_" + marker.layer.feature.properties.markerid ).hide();'.PHP_EOL;
										echo '}'.PHP_EOL;
									echo '});'.PHP_EOL;
									//info re-calculate total markers when the AJAX call finished.
									echo 'if(window["mmp_no_controlbox"] === true){'.PHP_EOL;
									echo '		mmp_calculate_total_markers_admin();'.PHP_EOL;
									echo '}'.PHP_EOL;
								}else{
									echo 'geojson_markers.addTo(selectlayer);'.PHP_EOL;
								}
						}else{
							echo 'geojson_markers.addTo(selectlayer);'.PHP_EOL;
						}
					}
						echo 'markers_counter = Object.keys(markerID).length - markers_counter;'.PHP_EOL; //info: #328
						//info: calculate total markers
						echo 'window["mmp_calculate_total_markers_admin"] = function(){'.PHP_EOL;
						echo '		var current_total_markers =0;'.PHP_EOL;
						echo '		var activated_layers =[];'.PHP_EOL;
						echo '		jQuery.each(filtered_layers,function (i,layer){'.PHP_EOL;
						echo '			if(typeof layer != "undefined"){'.PHP_EOL;
						echo '				if(layer.hasOwnProperty("_map")  || layer.non_filterbox == true){'.PHP_EOL;
						echo '					if(layer._map != null || layer.non_filterbox == true ){'.PHP_EOL;
						echo '					current_total_markers = current_total_markers + (parseInt(layer.markercount)  || 0);'.PHP_EOL; //info: #328
						echo '						current_total_markers = current_total_markers + parseInt(layer.markercount);'.PHP_EOL;
						echo '						activated_layers.push(layer.layer_id);'.PHP_EOL;
						echo '					}'.PHP_EOL;
						echo '				}'.PHP_EOL;
						echo '			}'.PHP_EOL;
						echo '		});'.PHP_EOL;
						echo '	jQuery(".markercount").html(parseInt(current_total_markers) + (Object.keys(markerID).length - markers_counter) );'.PHP_EOL; //info: #328
						echo '	jQuery("#admin_multi_layer_map_list").val(activated_layers.join(","));'.PHP_EOL;
						echo '	return parseInt(current_total_markers) + (Object.keys(markerID).length - markers_counter);'.PHP_EOL; //info: #328
						echo '}'.PHP_EOL;
						//info: reset the map
						if($lmm_options['map_home_button'] == 'true-ondemand'){
							echo 'selectlayer.on("moveend",function(e){'.PHP_EOL;
							echo '		jQuery("#leaflet-control-zoomhome-admin").show();'.PHP_EOL;
							echo '});'.PHP_EOL;
						}
						?>
				} else { if (window.console) { console.error(xhReq.statusText); } } } }; xhReq.onerror = function (e) { if (window.console) { console.error(xhReq.statusText); } }; xhReq.send(null); //info: async 2/2

		<?php } //info: end if ($id != NULL) ?>

		<?php
		//info: set controlbox visibility 2/2
		if ($controlbox == '0') {
			echo "$('.leaflet-control-layers.controlbox_basemaps').hide();";
		} else if ($controlbox == '2') {
			echo "layersControl._expand();";
		}?>

		//info: load wms layer when checkbox gets checked
		$('#toggle-advanced-settings input:checkbox').click(function(el) {
			if(el.target.checked) {
				selectlayer.addLayer(window[el.target.id]);
			} else {
				selectlayer.removeLayer(window[el.target.id]);
			}

		});
		//info: update basemap when chosing from control box
		selectlayer.on('layeradd', function(e) {
		if (e.layer.options != undefined) { //needed for gpx
			if(e.layer.options.mmid) {
				selectlayer.attributionControl._attributions = [];
				$('#basemap').val(e.layer.options.mmid);
			}
		}
		});
		//info: when custom overlay gets checked from control box update hidden field
		selectlayer.on('layeradd', function(e) {
		if (e.layer.options != undefined) { //needed for gpx
			if(e.layer.options.olid) {
				$('#'+e.layer.options.olid).attr('value', '1');
			}
		}
		});
		//info: when custom overlay gets unchecked from control box update hidden field
		selectlayer.on('layerremove', function(e) {
		if (e.layer.options != undefined) {
			if(e.layer.options.olid) {
				$('#'+e.layer.options.olid).attr('value', '0');
			}
		}
		});
		selectlayer.on('moveend', function(e) { document.getElementById('layerzoom').value = selectlayer.getZoom();});
		selectlayer.on('click', function(e) {
		  document.getElementById('layerviewlat').value = e.latlng.lat.toFixed(6);
		  document.getElementById('layerviewlon').value = e.latlng.lng.toFixed(6);
		  selectlayer.setView(e.latlng,selectlayer.getZoom());
		  mapcentermarker.setLatLng(e.latlng);
		});
		//info: set new coordinates on mapcentermarker drag
		mapcentermarker.on('dragend', function(e) {
			var newlocation = mapcentermarker.getLatLng();
			$('.addmarker_link').attr('href',lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=leafletmapsmarker_marker&addtoLayer='+ $('#oid').val()+'&lat=' + newlocation['lat'].toFixed(6) + '&lon=' + newlocation['lng'].toFixed(6) + '&zoom=' + selectlayer.getZoom());
			<?php
			if (($isedit) && ($multi_layer_map == 0)) {
					echo 'mapcentermarker.bindPopup("<a class=\"addmarker_link\" target=\"_blank\" href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer="+ $("#oid").val()+"&lat=" + newlocation["lat"].toFixed(6) + "&lon=" + newlocation["lng"].toFixed(6) + "&zoom=" + selectlayer.getZoom() +"\" style=\"text-decoration:none;\">" + $("#defaults_texts_add_new_marker_here").val() + "</a>",
								{
								autoPan: true,
								closeButton: true,
								autoPanPadding: new L.Point(5,5)
								});';
			}
			?>
			var newlat = newlocation['lat'];
			var newlon = newlocation['lng'];
			document.getElementById('layerviewlat').value = newlat.toFixed(6);
			document.getElementById('layerviewlon').value = newlon.toFixed(6);
			selectlayer.setView(newlocation,selectlayer.getZoom());
		});
		selectlayer.on('click',function(e){
			var newlocation = e.latlng;
			$('.addmarker_link').attr('href',lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=leafletmapsmarker_marker&addtoLayer='+ $('#oid').val()+'&lat=' + newlocation['lat'].toFixed(6) + '&lon=' + newlocation['lng'].toFixed(6) + '&zoom=' + selectlayer.getZoom());
			<?php
			if (($isedit) && ($multi_layer_map == 0)) {
					echo 'mapcentermarker.bindPopup("<a class=\"addmarker_link\" target=\"_blank\" href=\"' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer="+ $("#oid").val()+"&lat=" + newlocation["lat"].toFixed(6) + "&lon=" + newlocation["lng"].toFixed(6) + "&zoom=" + selectlayer.getZoom() +"\" style=\"text-decoration:none;\">" + $("#defaults_texts_add_new_marker_here").val() + "</a>",
								{
								autoPan: true,
								closeButton: true,
								autoPanPadding: new L.Point(5,5)
								});';
			}
			 ?>
		});
		//info: reset control
		<?php
		if ($isedit) { //info: only show on edits of existing maps, as otherwise reset uses default lat/lon values
			if($lmm_options['map_home_button'] != 'false'){
				$zoomhome_ondemand = ($lmm_options['map_home_button'] == 'true-ondemand')?'true':'false';
				if (!$isedit) { $filter_details = ''; } //info: to avoid PHP warning
				$reenableclustering = ($filter_details && $clustering == '1')?'true':'false';
				echo  'var reset_control = L.Control.zoomHome({position: "'. $lmm_options['map_home_button_position'] .'", mapId: "admin", mapnameJS: "selectlayer", ondemand: '.$zoomhome_ondemand.', zoomHomeTitle:"'.esc_attr__('reset map view','lmm').'", reenableClustering:"' .$reenableclustering.'" });'.PHP_EOL;
				echo  'reset_control.addTo(selectlayer);'.PHP_EOL;
			}
		}
		?>

		<?php if ($lmm_options['defaults_marker_popups_center_map'] == 'true') {
			echo "//info: center map on popup instead of marker".PHP_EOL;
			echo "selectlayer.on('popupopen', function(e) {".PHP_EOL;
			echo "	setTimeout(function(){".PHP_EOL; //info: to prevent wrong map centers after listmarkers_openpopup_" . $mapname_js . "(id) is called
			echo "		var px = selectlayer.project(e.popup._latlng);".PHP_EOL;
			echo "		px.y -= e.popup._container.clientHeight/2".PHP_EOL;
			echo "		selectlayer.panTo(selectlayer.unproject(px),{animate: true});".PHP_EOL;
			echo "	},100);".PHP_EOL;
			echo "});".PHP_EOL;
		}?>

		var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), layerviewlat = $('#layerviewlat'), layerviewlon = $('#layerviewlon'), panel = $('#lmm-panel'), gpxpanel = $('#gpx-panel-selectlayer'), gpxpanelcheckbox = $('#gpx_panel'), lmm = $('#lmm'), gpx_fitbounds_link = $('#gpx_fitbounds_link'), layername = $('#layername'), listmarkers = $('#lmm-listmarkers'), listmarkers_table = $('#lmm_listmarkers_table_admin'), multi_layer_map = $('#lmm-multi_layer_map'), multi_layer_map_filter = $('#lmm-multi_layer_map_filter'), zoom = $('#layerzoom'), clustering = $('#clustering');
		//info: change zoom level when changing form field
		zoom.on('change', function(e) {
			if(isNaN(zoom.val())) {
					alert('<?php esc_attr_e('Invalid format! Please only use numbers!','lmm') ?>');
			} else {
			selectlayer.setZoom(zoom.val());
			}
		});
		//info: bugfix causing maps not to show up in WP 3.0 and errors in WP <3.3
		layername.on('change', function(e) {
			if( layername.val() ){
				$('#lmm-panel-text').text(layername.val());
			} else {
				$('#lmm-panel-text').text('&nbsp;');
			};
		});
		mapWidth.change(function() {
			if(!isNaN(mapWidth.val())) {
				lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				listmarkers.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				listmarkers_table.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				selectlayer.invalidateSize();
			}
		});
		$('input:radio[name=mapwidthunit]').click(function() {
				lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				listmarkers.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				listmarkers_table.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				selectlayer.invalidateSize();
		});
		mapHeight.change(function() {
			if(!isNaN(mapHeight.val())) {
				mapElement.css("height",mapHeight.val()+"px");
				selectlayer.invalidateSize();
			}
		});
		//info: show/hide panel for layername & API URLs
		$('input:checkbox[name=panel]').click(function() {
			if($('input:checkbox[name=panel]').is(':checked')) {
				panel.css("display",'block');
			} else {
				panel.css("display",'none');
			}
		});
		<?php //info: upload to media library
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

		//info: create nonce for gpx proxy
		$gpx_proxy_nonce = wp_create_nonce('gpx-proxy-nonce');

		if ( version_compare( $wp_version, '3.5', '>=' ) ) {
			echo "var custom_uploader;
			$('#upload_gpx_file').click(function(e) {
				e.preventDefault();
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: '" . esc_attr__('Upload GPX track','lmm') . "',
					frame: 'select',
					library: { type: 'application/gpx+xml' },
					button: {
						text: '" . esc_attr__('Insert GPX track','lmm') . "'
					},
					multiple: false
				});
				//info: when a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$('#gpx_url').val(attachment.url);
					gpxpanelcheckbox.attr('checked','checked');
					gpxpanel.css('display','block');
					gpx_fitbounds_link.css('display','inline');

					$.ajax({
						url: '" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/proxy/?url='+attachment.url+'&nonce=" . $gpx_proxy_nonce) . "',
						dataType: 'text',
						type: 'POST'
					}).done(function(data) {
							//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
							if (data.indexOf('<?xml') != 0) {
								var position = data.indexOf('<?xml');
								if (position === -1) {
									if (data.indexOf('<gpx') != 0) { //info: for non-standard GPX files
										var position = data.indexOf('<gpx');
										var data = data.slice(position);
									}
								} else {
									var data = data.slice(position);
								}
							}
							//info: search data for <gpx tag (IIS7.0 issue)
							try {
								if (window.addEventListener) { //info: indexof only available in IE9+
									if (data.toLowerCase().indexOf('<gpx') >= 0) { if (window.console) { console.log('GPX file seems to be ok'); } } else { jquery.error; };
								}
							} catch (err) {
								alert('" . esc_attr__('GPX file could not be parsed - please check your browser console for more information!','lmm') . "');
								if (window.console) console.log(data);
							}
							var gpx_panel = document.getElementById('gpx-panel-selectlayer');
								function _c(c) { return gpx_panel.querySelectorAll('.'+c)[0]; }
							var gpx_track = new L.GPX(attachment.url, {
								gpx_content: data,
								async: true,
								max_point_interval: " .  intval($lmm_options['gpx_max_point_interval']) . ",
								marker_options: {
									startIconUrl: '" . $gpx_startIconUrl . "',
									endIconUrl: '" . $gpx_endIconUrl . "',
									shadowUrl: '" . $gpx_shadowUrl . "',
									iconSize: [" .  intval($lmm_options['gpx_iconSize_x']) . ", " .  intval($lmm_options['gpx_iconSize_y']) . "],
									shadowSize: [" .  intval($lmm_options['gpx_shadowSize_x']) . ", " .  intval($lmm_options['gpx_shadowSize_y']) . "],
									iconAnchor: [" .  intval($lmm_options['gpx_iconAnchor_x']) . ", " .  intval($lmm_options['gpx_iconAnchor_y']) . "],
									shadowAnchor: [" .  intval($lmm_options['gpx_shadowAnchor_x']) . ", " .  intval($lmm_options['gpx_shadowAnchor_y']) . "],
									className: 'lmm_gpx_icons'
								},
								polyline_options: {
									color: '" . $gpx_track_color . "',
									weight: " . intval($lmm_options['gpx_track_weight']) . ",
									opacity: '" . str_replace(',', '.', floatval($lmm_options['gpx_track_opacity'])) . "',
									smoothFactor: '" . str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])) . "',
									interactive: " . esc_js($lmm_options['gpx_track_clickable']) . ",
									noClip: " . esc_js($lmm_options['gpx_track_noClip']) . "
								}
								}).addTo(selectlayer);
							gpx_track.on('gpx_loaded', function(e) {
									var gpx = e.target;
									selectlayer.fitBounds(e.target.getBounds(), { padding: [25,25] });

									var new_mapcentermarker = selectlayer.getCenter()
									mapcentermarker.setLatLng(new_mapcentermarker);
									document.getElementById('layerviewlat').value = new_mapcentermarker.lat.toFixed(6);
									document.getElementById('layerviewlon').value = new_mapcentermarker.lng.toFixed(6);

									" . $gpx_metadata_name_js . "
									" . $gpx_metadata_start_js . "
									" . $gpx_metadata_end_js . "
									" . $gpx_metadata_distance_js . "
									" . $gpx_metadata_duration_moving_js . "
									" . $gpx_metadata_duration_total_js . "
									" . $gpx_metadata_avpace_js . "
									" . $gpx_metadata_avhr_js . "
									" . $gpx_metadata_elev_gain_js . "
									" . $gpx_metadata_elev_loss_js . "
									" . $gpx_metadata_elev_net_js . "
									" . $gpx_metadata_elev_full_js . "
									" . $gpx_metadata_hr_full_js . "
							});
						});
				});
				custom_uploader.open();
			});";
		} else { //info: WP <3.5
			echo "jQuery(document).ready(function() {
				jQuery('#upload_gpx_file').click(function() {
					formfield = jQuery('#gpx_url').attr('name');
					tb_show('', 'media-upload.php?tab=library&post_mime_type=text%2Fgpx&amp;TB_iframe=true');
					jQuery('#TB_overlay').css('z-index','1000');
					jQuery('#TB_window').css('z-index','10000');
					return false;
				});
				window.send_to_editor = function(html) {
					gpxurl = jQuery(html).attr('href');
					jQuery('#gpx_url').val(gpxurl);
					tb_remove();
				}
				 });";
		} ?>

		//info: show/hide gpx panel
		$('input:checkbox[name=gpx_panel]').click(function() {
			if($('input:checkbox[name=gpx_panel]').is(':checked')) {
				gpxpanel.css("display",'block');
			} else {
				gpxpanel.css("display",'none');
			}
		});
		//info: show fitbounds link on focus
		$('#gpx_url').focus(function() {
			gpx_fitbounds_link.css("display",'inline');
		});
		//info: fit gpx map bounds on click
		$('.gpxfitbounds').click(function(e){
			var current_gpx_url = $('#gpx_url').val();
			$.ajax({
				url: '<?php echo MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug); ?>/proxy/?url='+current_gpx_url+'&nonce=<?php echo $gpx_proxy_nonce; ?>',
				dataType: 'text',
				type: 'POST'
			}).done(function(data) {
				//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
				if (data.indexOf('<\?xml') != 0) {
					var position = data.indexOf('<\?xml');
					if (position === -1) {
						if (data.indexOf('<gpx') != 0) { //info: for non-standard GPX files
							var position = data.indexOf('<gpx');
							var data = data.slice(position);
						}
					} else {
						var data = data.slice(position);
					}
				}
				//info: search data for <gpx tag (IIS7.0 issue)
				try {
					if (window.addEventListener) { //info: indexof only available in IE9+
						if (data.toLowerCase().indexOf("<gpx") >= 0) { if (window.console) { console.log("GPX file seems to be ok"); } } else { jquery.error; };
					}
				} catch (err) {
					alert("<?php echo esc_attr__('GPX file could not be parsed - please check your browser console for more information!','lmm'); ?>");
					if (window.console) console.log(data);
				}
				var gpx_panel = document.getElementById('gpx-panel-selectlayer');
				function _c(c) { return gpx_panel.querySelectorAll('.'+c)[0]; }
				var gpx_track = new L.GPX(gpx_url, {
					gpx_content: data,
					async: true,
					max_point_interval: <?php echo intval($lmm_options['gpx_max_point_interval']); ?>,
					marker_options: {
						startIconUrl: "<?php echo $gpx_startIconUrl; ?>",
						endIconUrl: "<?php echo $gpx_endIconUrl; ?>",
						shadowUrl: "<?php echo $gpx_shadowUrl; ?>",
						iconSize: [<?php echo intval($lmm_options['gpx_iconSize_x']); ?>, <?php echo intval($lmm_options['gpx_iconSize_y']); ?>],
						shadowSize: [<?php echo intval($lmm_options['gpx_shadowSize_x']); ?>, <?php echo intval($lmm_options['gpx_shadowSize_y']); ?>],
						iconAnchor: [<?php echo intval($lmm_options['gpx_iconAnchor_x']); ?>, <?php echo intval($lmm_options['gpx_iconAnchor_y']); ?>],
						shadowAnchor: [<?php echo intval($lmm_options['gpx_shadowAnchor_x']); ?>, <?php echo intval($lmm_options['gpx_shadowAnchor_y']); ?>],
						className: 'lmm_gpx_icons'
					},
					polyline_options: {
						color: "<?php echo $gpx_track_color; ?>",
						weight: <?php echo intval($lmm_options['gpx_track_weight']); ?>,
						opacity: "<?php echo str_replace(',', '.', floatval($lmm_options['gpx_track_opacity'])); ?>",
						smoothFactor: "<?php echo str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])); ?>",
						interactive: <?php echo esc_js($lmm_options['gpx_track_clickable']); ?>,
						noClip: <?php echo esc_js($lmm_options['gpx_track_noClip']); ?>
					}
					}).addTo(selectlayer);

					gpx_track.on('gpx_loaded', function(e) {
						var gpx = e.target;
						selectlayer.fitBounds(e.target.getBounds(), { padding: [25,25] } );

						var new_mapcentermarker = selectlayer.getCenter()
						mapcentermarker.setLatLng(new_mapcentermarker);
						document.getElementById('layerviewlat').value = new_mapcentermarker.lat.toFixed(6);
						document.getElementById('layerviewlon').value = new_mapcentermarker.lng.toFixed(6);

						<?php echo $gpx_metadata_name_js; ?>
						<?php echo $gpx_metadata_start_js; ?>
						<?php echo $gpx_metadata_end_js; ?>
						<?php echo $gpx_metadata_distance_js; ?>
						<?php echo $gpx_metadata_duration_moving_js; ?>
						<?php echo $gpx_metadata_duration_total_js; ?>
						<?php echo $gpx_metadata_avpace_js; ?>
						<?php echo $gpx_metadata_avhr_js; ?>
						<?php echo $gpx_metadata_elev_gain_js; ?>
						<?php echo $gpx_metadata_elev_loss_js; ?>
						<?php echo $gpx_metadata_elev_net_js; ?>
						<?php echo $gpx_metadata_elev_full_js; ?>
						<?php echo $gpx_metadata_hr_full_js; ?>
					});
			});
		});

		//info: show/hide markers list
		$('input:checkbox[name=listmarkers]').click(function() {
			if($('input:checkbox[name=listmarkers]').is(':checked')) {
				listmarkers.css("display",'block');
			} else {
				listmarkers.css("display",'none');
			}
		});
		//info: show/hide multi-layer-map layer list
		$('input:checkbox[name=multi_layer_map]').click(function() {
			if($('input:checkbox[name=multi_layer_map]').is(':checked')) {
				multi_layer_map.css("display",'block');
				multi_layer_map_filter.css("display",'block');
				$('input[name=controlbox_mlm_filter][value="1"]').prop('checked', true);
			} else {
				multi_layer_map.css("display",'none');
				multi_layer_map_filter.css("display",'none');
				$('input[name=controlbox_mlm_filter][value="0"]').prop('checked', true);
			}
		});
		//info: toggle marker clustering
		$('input:checkbox[name=clustering]').click(function() {
			if($('input:checkbox[name=clustering]').is(':checked')) {
				selectlayer.removeLayer(window.group_for_clustering);
				if(typeof geojson_markers != 'undefined'){
					selectlayer.removeLayer(geojson_markers);
					geojson_markers.addTo(window.markercluster_selectlayer);
				}
				window.group_for_clustering.addTo(window.markercluster_selectlayer);
				selectlayer.addLayer(window.markercluster_selectlayer);
				window.markercluster_selectlayer.enableClustering();
			} else {
				window.markercluster_selectlayer.disableClustering();
			}
			$('#preview_clustering_info').toggle();
		});
		//info: check if layerviewlat is a number
		$('input:text[name=layerviewlat]').change(function(e) {
			if(isNaN(layerviewlat.val())) {
					alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
			}
		});
		//info: check if layerviewlon is a number
		$('input:text[name=layerviewlon]').change(function(e) {
			if(isNaN(layerviewlon.val())) {
					alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
			}
		});
		//info: dynamic update of control box status
		$('input:radio[name=controlbox]').click(function() {
			if($('input:radio[name=controlbox]:checked').val() == 0) {
				$('.leaflet-control-layers.controlbox_basemaps').hide();
			}
			if($('input:radio[name=controlbox]:checked').val() == 1) {
				$('.leaflet-control-layers.controlbox_basemaps').show();
				layersControl._collapse();
			}
			if($('input:radio[name=controlbox]:checked').val() == 2) {
				$('.leaflet-control-layers.controlbox_basemaps').show();
				layersControl._expand();
			}
		});
		//info: dynamic update of filter control box status
		$('input:radio[name=controlbox_mlm_filter]').click(function() {
			if($('input:radio[name=controlbox_mlm_filter]:checked').val() == 0) {
				$('.leaflet-control-layers:not(.controlbox_basemaps)').hide();
			}
			if($('input:radio[name=controlbox_mlm_filter]:checked').val() == 1) {
				$('.leaflet-control-layers:not(.controlbox_basemaps)').show();
				if(typeof filter_controlbox != "undefined"){
					filter_controlbox._collapse();
				}
			}
			if($('input:radio[name=controlbox_mlm_filter]:checked').val() == 2) {
				$('.leaflet-control-layers:not(.controlbox_basemaps)').show();
				if(typeof filter_controlbox != "undefined"){
					filter_controlbox._expand();
				}
			}
		});
		//info: show all API links on click on simplified editor
		$('#apilinkstext').click(function(e) {
			$('#apilinkstext').hide();
			$('#apilinks').show('fast');
		});
		//info: sets map center to new layer center position when entering lat/lon manually
		$('input:text[name=layerviewlat],input:text[name=layerviewlon]').change(function(e) {
			var mapcentermarker_new = new L.LatLng(layerviewlat.val(),layerviewlon.val());
			mapcentermarker.setLatLng(mapcentermarker_new);
			selectlayer.setView(mapcentermarker_new, selectlayer.getZoom());
		});
		//info: warn on unsaved changes when leaving page
		var unsaved = false;
		$("#layername, #address, #layerviewlat, #layerviewlon, #mapwidth, .mapwidthunit, #mapheight, #layerzoom, #listmarkers, #clustering, #gpx_url, #gpx_panel, .layer-controlbox, #panel, #multi_layer_map, .filter-controlbox, #mlm-all, .filter-checkbox, .filter-icon-url, .filter-name, .wms").change(function(){ //info: :input did not work on layer edit page due to .mlm_filter_status in ajax-actions-backend.js
			unsaved = true;
		});
		selectlayer.on('zoomend click', function(e) {
			unsaved = true;
		});
		mapcentermarker.on('dragend', function(e) {
			unsaved = true;
		});
		$('#submit_top, #submit_bottom, #delete, #delete_layer_and_markers').click(function() {
			unsaved = false;
		});
		function unloadPage(){
			if(unsaved){
				return "<?php esc_attr_e('You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?','lmm'); ?>";
			}
		}
		window.onbeforeunload = unloadPage;

		$('#list-markers input').change(function(e){
			$('#mlm-filters-note').show();
		});
		setTimeout(function(){
			$('.mlm_filter_status').change(function(e){
				$('#mlm-filters-note').show();
			});
		},3000);
		//info: change submenu URL to prevent reloading & supporting AJAX when adding new layers
		$(document).ready(function(){
			$('.wp-menu-open.menu-top.toplevel_page_leafletmapsmarker_markers.menu-top-last ul.wp-submenu.wp-submenu-wrap li.current a.current').attr('href', $('#defaults_add_new_layer_link').val());
		});
		//info: update "add new marker to this layer" link on layer edit load
		$(document).ready(function(){
			$('.addmarker_link').attr('href','<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_marker&addtoLayer=<?php echo $id; ?>&lat=<?php echo $layerviewlat; ?>&lon=<?php echo $layerviewlon; ?>&zoom=<?php echo $layerzoom; ?>');
		});

		//info: select layername, address, mapwith, mapheight & zoom input field on focus
		$("#layername, #address, #mapwidth, #mapheight, #layerzoom").focus(function() {
			$(this).select();
		});

		//info: toggle advanced layer buttons
		jQuery('.show-advanced-layer-edit-buttons, .hide-advanced-layer-edit-buttons').click(function(e) {
			jQuery('.show-advanced-layer-edit-buttons').toggle();
			jQuery('.hide-advanced-layer-edit-buttons').toggle();
			jQuery('.advanced-layer-edit-button').toggle();
		});

		<?php //info: workaround for Chrome fullscreen exit issue
		if (esc_js($lmm_options['map_fullscreen_button']) == 'true') {
			echo 'selectlayer.on("fullscreenchange",function(e){'.PHP_EOL;
			echo 'if (!selectlayer.isFullscreen()) {';
			echo 'selectlayer.invalidateSize();';
			echo '}});';
		} ?>
		//info: fix for autofocus in Chrome
		$(document).ready(function(){
			$('input[autofocus="autofocus"]').focus();
		});
	})(jQuery)

	//info: openpopup and center map on click on markername in list of markers
	function listmarkers_action(id) {
		var newlocation = markerID[id].getLatLng();
		selectlayer.setView(newlocation,selectlayer.getZoom());
		<?php
		if($lmm_options['defaults_layer_listmarkers_link_action_zoom'] == 'marker-zoom'){
			if(
				(isset($filter_details) && $filter_details)
				||
				$clustering == '1')
			{
				echo '	setTimeout(function(){ '.PHP_EOL;
				echo "	var newlocation = markerID[id].getLatLng();".PHP_EOL;
				echo 		'selectlayer.setView(newlocation,markerID[id].feature.properties.zoom);'.PHP_EOL;
				echo '	setTimeout(function(){ '.PHP_EOL;
				echo ' 	if(typeof markercluster_selectlayer.getVisibleParent(markerID[id]).spiderfy == "function"){ '.PHP_EOL;
				echo '			markercluster_selectlayer.getVisibleParent(markerID[id]).spiderfy();'.PHP_EOL;
				echo '		}'.PHP_EOL;
				if ($lmm_options['defaults_layer_listmarkers_link_action'] == 'setview-open') {
					echo '		markerID[id].openPopup();'.PHP_EOL;
				}
				echo '},1000);'.PHP_EOL;
				echo '	 }, 300);'.PHP_EOL;
			}
		}else{
			if ($lmm_options['defaults_layer_listmarkers_link_action'] == 'setview-open') {
				echo "	markerID[id].openPopup();".PHP_EOL;
			}
			if($clustering == '1'){
				echo '	setTimeout(function(){ '.PHP_EOL;
				echo "	var newlocation = markerID[id].getLatLng();".PHP_EOL;
				echo 'selectlayer.setView(newlocation,selectlayer.getZoom());'.PHP_EOL;
				echo 'if(typeof markercluster_selectlayer.getVisibleParent(markerID[id]).spiderfy == "function"){ '.PHP_EOL;
				echo '		markercluster_selectlayer.getVisibleParent(markerID[id]).spiderfy();'.PHP_EOL;
				echo '}'.PHP_EOL;
				if ($lmm_options['defaults_layer_listmarkers_link_action'] == 'setview-open') {
					echo "	markerID[id].openPopup();".PHP_EOL;
				}
				echo '},1000);'.PHP_EOL;
			}else{
				echo "	var newlocation = markerID[id].getLatLng();".PHP_EOL;
				echo "selectlayer.setView(newlocation,selectlayer.getZoom());".PHP_EOL;
			}
		}
		?>
	}

	<?php //info: show alternative error on gelocation fail for Google Chrome
	if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) {
		echo 'selectlayer.on("locationerror",function(e){'.PHP_EOL;
		echo '	alert("' . sprintf(esc_attr__('Geolocation failed: your current location can only be retrieved if the map is accessed securely using https - see %1$s for more details!','lmm'), 'https://www.mapsmarker.com/geolocation-https-only') . '");'.PHP_EOL;
		echo '});'.PHP_EOL;
	}

	//info: show loading indicator if popup contains images and update popup after images have loaded to prevent broken popups
	echo PHP_EOL."selectlayer.on('popupopen', function(e) {".PHP_EOL;
	echo "  if (typeof (e.popup._source.feature) !== 'undefined') {".PHP_EOL;
	echo '    var popup_markerid = e.popup._source.feature.properties.markerid;'.PHP_EOL;
	echo '  } else {'.PHP_EOL;
	echo '    var popup_markerid = e.popup._source.options.markerid;'.PHP_EOL;
	echo '  }'.PHP_EOL;
	echo "  var popup_images = jQuery('.leaflet-popup-content-wrapper #popup-content-'+popup_markerid+' img');".PHP_EOL;
	echo '  if (popup_images.length > 0) {'.PHP_EOL;
	echo '    var image_counter = 0;'.PHP_EOL;
	echo "    jQuery('#popup-content-'+popup_markerid).css('display', 'none');".PHP_EOL;
	echo "    jQuery('#popup-loading-'+popup_markerid).css('display', 'block');".PHP_EOL;
	echo '    jQuery(popup_images).each(function() {'.PHP_EOL;
	echo "      jQuery(this).on('load', function() {".PHP_EOL;
	echo '        image_counter++;'.PHP_EOL;
	echo '        if (image_counter == popup_images.length) {'.PHP_EOL;
	echo "          jQuery('#popup-loading-'+popup_markerid).css('display', 'none');".PHP_EOL;
	echo "          jQuery('#popup-content-'+popup_markerid).css('display', 'block');".PHP_EOL;
	echo '          e.popup.update();'.PHP_EOL;
	echo '        }'.PHP_EOL;
	echo '      });'.PHP_EOL;
	echo '    });'.PHP_EOL;
	echo '  }'.PHP_EOL;
	echo '});'.PHP_EOL;

	//info: prepare Mapzen Search options
	$mapzen_sources = array();
	if(isset($lmm_options['mapzen_search_sources_osm'])){
		array_push($mapzen_sources, 'osm');
	}
	if(isset($lmm_options['mapzen_search_sources_oa'])){
		array_push($mapzen_sources, 'oa');
	}
	if(isset($lmm_options['mapzen_search_sources_geonames'])){
		array_push($mapzen_sources, 'geonames');
	}
	if(isset($lmm_options['mapzen_search_sources_wof'])){
		array_push($mapzen_sources, 'wof');
	}
	$mapzen_sources = (!empty($mapzen_sources))?implode(',', $mapzen_sources):'';

	//info: prepare Algolia Places options
	if (isset($lmm_options["geocoding_algolia_language"]) && $lmm_options["geocoding_algolia_language"]!="") {
		$algolia_language = esc_js(trim($lmm_options["geocoding_algolia_language"]));
	} else {
		$algolia_language = substr($locale, 0, 2);
	}

	//info: prepare Photon options
	if ($lmm_options["geocoding_photon_language"]=="automatic") {
		$locale_for_photon = strtolower(substr($locale, 0,2));
		if ($locale_for_photon = 'de') {
			$photon_language = 'de';
		} else if ($locale_for_photon = 'fr') {
			$photon_language = 'fr';
		} else if ($locale_for_photon = 'it') {
			$photon_language = 'it';
		} else {
			$photon_language = 'en';
		}
	} else {
		$photon_language = $lmm_options["geocoding_photon_language"];
	}
	?>
	var geocoding_options = {
		mapzen_search: {
			api_key: '<?php echo (isset($lmm_options["geocoding_mapzen_search_api_key"]))?esc_js(trim($lmm_options["geocoding_mapzen_search_api_key"])):""; ?>',
			focuspointlat: '<?php echo (isset($lmm_options["geocoding_mapzen_search_focus_lat"]) && $lmm_options["geocoding_mapzen_search_focus_lat"]!="")?str_replace(',', '.',floatval($lmm_options["geocoding_mapzen_search_focus_lat"])):"none"; ?>',
			focuspointlon: '<?php echo (isset($lmm_options["geocoding_mapzen_search_focus_lon"]) && $lmm_options["geocoding_mapzen_search_focus_lon"]!="")?str_replace(',', '.',floatval($lmm_options["geocoding_mapzen_search_focus_lon"])):"none"; ?>',
			sources:'<?php echo $mapzen_sources; ?>',
			layers: '<?php echo (isset($lmm_options["geocoding_mapzen_search_layer"]) && $lmm_options["geocoding_mapzen_search_layer"]!="none")?$lmm_options["geocoding_mapzen_search_layer"]:"none"; ?>',
			country: '<?php echo (isset($lmm_options["geocoding_mapzen_search_country"]) && $lmm_options["geocoding_mapzen_search_country"]!="")?esc_js(trim($lmm_options["geocoding_mapzen_search_country"])):""; ?>',
			narrow_search: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_search"]) && $lmm_options["geocoding_mapzen_search_narrow_search"]!="")?esc_js(trim($lmm_options["geocoding_mapzen_search_narrow_search"])):""; ?>',
			rect_lat_min: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lat_min"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lat_min"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lat_min"])):""; ?>',
			rect_lon_min: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lon_min"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lon_min"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lon_min"])):""; ?>',
			rect_lat_max: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lat_max"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lat_max"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lat_max"])):""; ?>',
			rect_lon_max: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lon_max"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lon_max"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lon_max"])):""; ?>',
			circle_lat: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_circle_lat"]) && $lmm_options["geocoding_mapzen_search_narrow_circle_lat"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_circle_lat"])):""; ?>',
			circle_lon: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_circle_lon"]) && $lmm_options["geocoding_mapzen_search_narrow_circle_lon"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_circle_lon"])):""; ?>',
			circle_radius: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_circle_radius"]) && $lmm_options["geocoding_mapzen_search_narrow_circle_radius"]!="")?floatval($lmm_options["geocoding_mapzen_search_narrow_circle_radius"]):""; ?>'
		},
		algolia_places: {
			appId: '<?php echo (isset($lmm_options["geocoding_algolia_appId"]))?esc_js(trim($lmm_options["geocoding_algolia_appId"])):""; ?>',
			apiKey: '<?php echo (isset($lmm_options["geocoding_algolia_apiKey"]))?esc_js(trim($lmm_options["geocoding_algolia_apiKey"])):""; ?>',
			language: '<?php echo $algolia_language; ?>',
			countries: '<?php echo (isset($lmm_options["geocoding_algolia_countries"]))?esc_js(trim($lmm_options["geocoding_algolia_countries"])):""; ?>',
			aroundLatLngViaIP: '<?php echo (isset($lmm_options["geocoding_algolia_aroundLatLngViaIP"]))?$lmm_options["geocoding_algolia_aroundLatLngViaIP"]:"true"; ?>',
			aroundLatLng: '<?php echo (isset($lmm_options["geocoding_algolia_aroundLatLng"]))?esc_js(trim($lmm_options["geocoding_algolia_aroundLatLng"])):""; ?>'
		},
		photon: {
			language: '<?php echo $photon_language; ?>',
			locationbiaslat: '<?php echo (isset($lmm_options["geocoding_photon_location_bias_lat"]) && $lmm_options["geocoding_photon_location_bias_lat"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_photon_location_bias_lat"])):"none"; ?>',
			locationbiaslon: '<?php echo (isset($lmm_options["geocoding_photon_location_bias_lon"]) && $lmm_options["geocoding_photon_location_bias_lon"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_photon_location_bias_lon"])):"none"; ?>',
			filter: '<?php echo (isset($lmm_options["geocoding_photon_filter_results"]) && $lmm_options["geocoding_photon_filter_results"]!="")?esc_js(trim($lmm_options["geocoding_photon_filter_results"])):"none"; ?>'
		},
		mapquest_geocoding: {
			api_key: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_api_key"]))?esc_js(trim($lmm_options["geocoding_mapquest_geocoding_api_key"])):""; ?>',
			boundingBox: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_status"]))?esc_js(trim($lmm_options["geocoding_mapquest_geocoding_bounds_status"])):"disabled"; ?>',
			lat1: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lat1"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lat1"])):""; ?>',
			lon1: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lon1"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lon1"])):""; ?>',
			lat2: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lat2"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lat2"])):""; ?>',
			lon2: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lon2"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lon2"])):""; ?>'
		},
		google_geocoding: {
			<?php $google_places_endpoint_nonce = wp_create_nonce('google-places-endpoint-nonce'); ?>
			nonce: '<?php echo $google_places_endpoint_nonce ?>'
		},
		fallback: '<?php echo str_replace("-", "_", $lmm_options["geocoding_provider_fallback"]); ?>'
	};
	<?php
	if ($lmm_options['geocoding_provider'] == 'mapzen-search')  { ?>
		jQuery(document).ready(function(){
			<?php if (($isedit) && ($multi_layer_map == 0)) { ?>
				var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			mapzen_search.init();
		});
	<?php
	} else if ($lmm_options['geocoding_provider'] == 'algolia-places')  {  ?>
		jQuery(document).ready(function(){
			<?php if (($isedit) && ($multi_layer_map == 0)) { ?>
				var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			algolia_places.init();
		});
	<?php
	} else if ($lmm_options['geocoding_provider'] == 'photon')  {  ?>
		jQuery(document).ready(function(){
			<?php if (($isedit) && ($multi_layer_map == 0)) { ?>
				var photon = new MMP_Geocoding('photon', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var photon = new MMP_Geocoding('photon', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			photon.init();
		});
	<?php
	} else if ( ($lmm_options['geocoding_provider'] == 'mapquest-geocoding' ) && ($lmm_options['geocoding_mapquest_geocoding_api_key'] != NULL) ) { ?>
		jQuery(document).ready(function(){
			<?php if (($isedit) && ($multi_layer_map == 0)) { ?>
				var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			mapquest_geocoding.init();
		});
	<?php
	} else if
		(
			($lmm_options['geocoding_provider'] == 'google-geocoding')
			&&
			(
				( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] != NULL) )
				||
				( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] != NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] != NULL)) )
			)
		)  {
	?>
		jQuery(document).ready(function(){
			<?php if (($isedit) && ($multi_layer_map == 0)) { ?>
				var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
				google_geocoding.init();
		});
	<?php } ?>

	<?php
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		echo '//info: detect if Google Maps JavaScript API returns an error
		if(window.console){
			console.error = function(message){
				if(message.indexOf("Google") != -1){ //info: only execute on Google console errors
					jQuery("#google-maps-api-status-info ").hide(); //info: hide admin notice visible for fresh pro installs and upgrades from free <3.11
					var message_stripped = message.replace(/(?:https?|ftp):\/\/[\n\S]+/g, "");
					jQuery("#google-api-error-admin-header").html(\'<strong>\'+message_stripped+\'</strong> (<a href="https://developers.google.com/maps/documentation/javascript/error-messages#no-api-keys" target="_blank">' . esc_attr__('error message details on google.com','lmm') . '</a>)<br/>\');
					jQuery("#google-api-error-admin-header").append(\'<hr noshade size="1"/><strong>' . __('Background','lmm') . '</strong>: ' . sprintf(__( 'Since June 22nd 2016 <a href="%1$s" target="_blank">Google requires a Google Maps API key</a> when using any Google Map service on your website.','lmm'), 'https://googlegeodevelopers.blogspot.co.at/2016/06/building-for-scale-updates-to-google.html') . ' ' . sprintf(__('Your personal API key can be obtained from the <a href="%1$s" target="_blank">Google API Console</a>.', 'lmm'), 'https://console.developers.google.com/apis/') . '<br/>' . sprintf(__('For a tutorial including screenshots on how to register a Google Maps API key <a href="%1$s" target="_blank">please click here</a>.', 'lmm'), 'https://www.mapsmarker.com/google-maps-javascript-api') . '<br/>\');
					jQuery("#google-api-error-admin-header").append(\'<hr noshade size="1"/><strong>' . __('Solution','lmm') . '</strong>: ' . sprintf(__('please add or verify your Google Maps JavaScript API key at <a href="%1$s">Settings / Basemaps / Google Maps JavaScript API</a>','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-google_js_api') . '\');
					jQuery("#google-api-error-admin-header").css("display", "block");
				}
			}
		}';
	}
	?>
	/* //]]> */
	</script>
	<!--default & current values for AJAX-->
	<input type="hidden" id="defaults_add_new_layer_link" value="<?php echo LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer#'; ?>" />
	<input type="hidden" id="defaults_lat" value="<?php echo str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lat' ])); ?>" />
	<input type="hidden" id="defaults_lon" value="<?php echo str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lon' ])); ?>" />
	<input type="hidden" id="defaults_zoom" value="<?php echo intval($lmm_options[ 'defaults_layer_zoom' ]); ?>" />
	<input type="hidden" id="defaults_mapwidth" value="<?php echo intval($lmm_options[ 'defaults_layer_mapwidth' ]); ?>" />
	<input type="hidden" id="defaults_mapwidthunit" value="<?php echo esc_html($lmm_options[ 'defaults_layer_mapwidthunit' ]); ?>" />
	<input type="hidden" id="defaults_mapheight" value="<?php echo intval($lmm_options[ 'defaults_layer_mapheight' ]); ?>" />
	<input type="hidden" id="defaults_panel" value="<?php echo $lmm_options[ 'defaults_layer_panel' ]; ?>" />
	<input type="hidden" id="defaults_clustering" value="<?php echo ($lmm_options[ 'defaults_layer_clustering' ] == 'enabled' ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_controlbox" value="<?php echo $lmm_options[ 'defaults_layer_controlbox' ]; ?>" />
	<input type="hidden" id="defaults_overlays_custom" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_overlays_custom2" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_overlays2_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom2_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_overlays_custom3" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_overlays3_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom3_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_overlays_custom4" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_overlays4_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom4_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms2" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms2_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms3" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms3_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms4" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms4_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms5" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms5_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms6" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms6_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms7" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms7_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms8" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms8_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms9" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms9_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<input type="hidden" id="defaults_wms10" value="<?php echo ( (isset($lmm_options[ 'defaults_layer_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms10_active' ] == 1 ) ) ? '1' : '0'; ?>" />
	<!--defaults for marker icons-->
	<?php
		echo '<input type="hidden" id="defaults_layer_icon_url" value="' . LEAFLET_PLUGIN_URL . '/leaflet-dist/images/marker.png" />'.PHP_EOL;
	?>
	<input type="hidden" id="defaults_layer_icon_shadow_url" value="<?php echo $marker_shadow_url;?>" />
	<?php

		echo '<input type="hidden" id="defaults_icon_className" value="lmm_marker_icon_default" />'.PHP_EOL;
		echo '<input type="hidden" id="defaults_icon_opacity_selector" value=".div-marker-icon-default" />'.PHP_EOL;
	?>
	<!--defaults for google directions link-->
	<input type="hidden" id="defaults_directions_directions_provider" value="<?php echo $lmm_options['directions_provider'];?>" />
	<input type="hidden" id="defaults_directions_directions_googlemaps_map_type" value="<?php echo $lmm_options['directions_googlemaps_map_type'];?>" />
	<input type="hidden" id="defaults_directions_directions_googlemaps_traffic" value="<?php echo $lmm_options['directions_googlemaps_traffic'];?>" />
	<input type="hidden" id="defaults_directions_directions_googlemaps_distance_units" value="<?php echo $lmm_options['directions_googlemaps_distance_units'];?>" />
	<input type="hidden" id="defaults_directions_directions_directions_googlemaps_overview_map" value="<?php echo $lmm_options['directions_googlemaps_overview_map'];?>" />
	<?php
	if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) {
		echo '<input type="hidden" id="defaults_directions_gmaps_base_domain_directions" value="' . $lmm_options['google_maps_base_domain'] . '" />'.PHP_EOL;
	} else {
		echo '<input type="hidden" id="defaults_directions_gmaps_base_domain_directions" value="' . htmlspecialchars($lmm_options['google_maps_base_domain_custom']) . '" />'.PHP_EOL;
	}
	if ( (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ) {
		echo '<input type="hidden" id="defaults_directions_google_avoidhighways" value="&dirflg=h" />'.PHP_EOL;
	} else {
		echo '<input type="hidden" id="defaults_directions_google_avoidhighways" value="" />'.PHP_EOL;
	}
	if ( (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ) {
		echo '<input type="hidden" id="defaults_directions_google_avoidtolls" value="&dirflg=t" />'.PHP_EOL;
	} else {
		echo '<input type="hidden" id="defaults_directions_google_avoidtolls" value="" />'.PHP_EOL;
	}
	if ( (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ) {
		echo '<input type="hidden" id="defaults_directions_google_publictransport" value="&dirflg=r" />'.PHP_EOL;
	} else {
		echo '<input type="hidden" id="defaults_directions_google_publictransport" value="" />'.PHP_EOL;
	}
	if ( (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ) {
		echo '<input type="hidden" id="defaults_directions_google_walking" value="&dirflg=w" />'.PHP_EOL;
	} else {
		echo '<input type="hidden" id="defaults_directions_google_walking" value="" />'.PHP_EOL;
	}

	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		echo '<input type="hidden" id="defaults_directions_google_language" value="" />'.PHP_EOL;
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( $locale != NULL ) {
			echo '<input type="hidden" id="defaults_directions_google_language" value="&hl=' . substr($locale, 0, 2) .'" />'.PHP_EOL;
		} else {
			echo '<input type="hidden" id="defaults_directions_google_language" value="&hl=en" />'.PHP_EOL;
		}
	} else {
		echo '<input type="hidden" id="defaults_directions_google_language" value="&hl=' . $lmm_options['google_maps_language_localization'] . '" />'.PHP_EOL;
	}
	?>
	<!--defaults for other direction link providers -->
	<input type="hidden" id="oid" value="<?php echo $oid; ?>" />
	<input type="hidden" id="defaults_directions_directions_yours_type_of_transport" value="<?php echo $lmm_options['directions_yours_type_of_transport'];?>" />
	<input type="hidden" id="defaults_directions_directions_yours_route_type" value="<?php echo $lmm_options['directions_yours_route_type'];?>" />
	<input type="hidden" id="defaults_directions_directions_yours_layer" value="<?php echo $lmm_options['directions_yours_layer'];?>" />
	<input type="hidden" id="defaults_directions_directions_ors_routeWeigh" value="<?php echo $lmm_options['directions_ors_routeWeigh'];?>" />
	<input type="hidden" id="defaults_directions_directions_ors_routeOpt" value="<?php echo $lmm_options['directions_ors_routeOpt'];?>" />
	<input type="hidden" id="defaults_directions_directions_ors_layer" value="<?php echo $lmm_options['directions_ors_layer'];?>" />
	<!-- default texts for AJAX-->
	<input type="hidden" id="defaults_texts_add_new_layer" value="<?php echo __('Add new layer','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_publish" value="<?php echo __('publish','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_update" value="<?php echo __('update','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_panel_text" value="<?php echo __('if set, layername will be displayed here','lmm'); ?>" />
	<!--defaults needed for mlm layer preview ajax loading-->
	<input type="hidden" id="defaults_marker_icon_iconsize_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_iconsize_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_iconanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_iconanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_popupanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_popupanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_shadow_url" value="<?php echo $marker_shadow_url;?>" />
	<input type="hidden" id="defaults_marker_icon_shadowsize_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_shadowsize_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_shadowanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]);?>" />
	<input type="hidden" id="defaults_marker_icon_shadowanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]);?>" />
	<input type="hidden" id="defaults_marker_popups_maxWidth" value="<?php echo intval($lmm_options['defaults_marker_popups_maxwidth']);?>" />
	<input type="hidden" id="defaults_marker_popups_minWidth" value="<?php echo intval($lmm_options['defaults_marker_popups_minwidth']);?>" />
	<input type="hidden" id="defaults_marker_popups_maxHeight" value="<?php echo intval($lmm_options['defaults_marker_popups_maxheight']);?>" />
	<input type="hidden" id="defaults_marker_popups_autoPan" value="<?php echo esc_js($lmm_options['defaults_marker_popups_autopan']);?>" />
	<input type="hidden" id="defaults_marker_popups_closeButton" value="<?php echo esc_js($lmm_options['defaults_marker_popups_closebutton']);?>" />
	<input type="hidden" id="defaults_marker_popups_autopanpadding_x" value="<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']);?>" />
	<input type="hidden" id="defaults_marker_popups_autopanpadding_y" value="<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']);?>" />
	<input type="hidden" id="defaults_marker_popups_add_markername" value="<?php echo $lmm_options['defaults_marker_popups_add_markername'];?>" />
	<input type="hidden" id="defaults_directions_popuptext_panel" value="<?php echo $lmm_options['directions_popuptext_panel'];?>" />
	<!-- default texts for AJAX-->
	<input type="hidden" id="defaults_texts_list_markers" value="<?php echo __('Markers assigned to this layer will be listed here','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_list_markers_ajax_info" value="<?php echo __('To preview the updated list of markers, please save layer map first and then reload the page!','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_add_new_marker_here" value="<?php echo __('add new marker here','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_no_assigned_markers" value="<?php echo __('No marker assigned to this layer','lmm'); ?>" />
	<!--default texts for geocoding.js-->
	<span style="display:none;" id="defaults_texts_geocoding_mapzen_api_key_needed"><?php echo sprintf(__('Fallback geocoding provider was activated as Mapzen Search requires an API key since 04/2017.<br/>We recommend <a href="%1$s" target="_blank">registering a free Mapzen Search API key</a> which allows up to %2$s requests/%3$s and a maximum of %4$s requests/%5$s.','lmm'), 'https://www.mapsmarker.com/mapzen-search#tutorial', '30.000', __('day','lmm'), '6', __('second','lmm')); ?></span>
	<span style="display:none;" id="defaults_texts_mapquest_key_issue"><?php echo sprintf(__('MapQuest Geocoding error - please contact support at <a href="%1$s">support@mapquest.com</a>'), 'mailto:support@mapquest.com?subject=Issue with API key ' . esc_js(trim($lmm_options['geocoding_mapquest_geocoding_api_key']))); ?></span>
	<input type="hidden" id="defaults_texts_geocoding_fallback_info" value="<?php echo __('Automatically switched to fallback provider','lmm'); ?>" />
	<input type="hidden" id="defaults_texts_geocoding_results_header" value="<?php echo __('To select a location, please click on a result or press','lmm'); ?>" />
	<span style="display:none;" id="defaults_texts_geocoding_footer_tips"><div id="geocoding-footer-tips" style="float:left;margin:4px 0 0 0;"><a href="https://www.mapsmarker.com/geocoding-optimization" target="_blank" style="text-decoration:underline;" title="<?php echo esc_attr__('show tutorial at mapsmarker.com','lmm'); ?>"><?php echo __('Tip: adjust geocoding settings for more targeted search results','lmm'); ?></a></div></span>
	<?php
	echo '<input type="hidden" id="defaults_wpml_status" value="' . MMP_Globals::check_multilingual() . '" />';
	} //info: check if layer exists - part 2
	} //info: check if user is allowed to view layer - part 2
} //info: !empty($action) 3/3
echo '</div><!--end div-layer-editor-hide-on-ajax-delete-->';
include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');
