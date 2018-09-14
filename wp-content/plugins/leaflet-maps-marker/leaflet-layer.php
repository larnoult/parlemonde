<?php
/*
    Edit layer - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-layer.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php');
global $wpdb, $allowedtags, $locale, $allowedposttags;
$additionaltags = array('iframe' => array('id' => true,'name' => true,'src' => true,'class' => true,'style' => true,'frameborder' => true,'scrolling' => true,'align' => true,'width' => true,'height' => true,'marginwidth' => true,'marginheight' => true),'style' => array('media' => true,'scoped' => true,'type' => true));
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
$current_editor = get_option( 'leafletmapsmarker_editor' );
$new_editor = isset($_GET['new_editor']) ? $_GET['new_editor'] : '';
$current_editor_css = ($current_editor == 'simplified') ? 'display:none;' : '';
//info: workaround - select shortcode on input focus doesnt work on iOS
global $wp_version;
$is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
if ( version_compare( $wp_version, '3.4', '>=' ) ) {
	 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
} else {
	 $shortcode_select = '';
}
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$layerlist = $wpdb->get_results('SELECT l.id as lid,l.name as lname FROM `'.$table_name_layers.'` as l WHERE l.multi_layer_map = 0 and l.id != 0 ORDER BY l.id ASC', ARRAY_A);
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$oid = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : '');
$lat_check = isset($_POST['layerviewlat']) ? $_POST['layerviewlat'] : (isset($_GET['layerviewlat']) ? $_GET['layerviewlat'] : '');
$lon_check = isset($_POST['layerviewlon']) ? $_POST['layerviewlon'] : (isset($_GET['layerviewlon']) ? $_GET['layerviewlon'] : '');
$layerid = isset($_GET['layerid']) ? intval($_GET['layerid']) : ''; //info: for switcheditor-js-forward

if (!empty($action)) {
	$layernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($layernonce, 'layer-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').''); };
  if ($action == 'add') {
  if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
		global $current_user;
		wp_get_current_user();
		//info: set values for wms checkboxes status
		$wms_checkbox = isset($_POST['wms']) ? '1' : '0';
		$wms2_checkbox = isset($_POST['wms2']) ? '1' : '0';
		$wms3_checkbox = isset($_POST['wms3']) ? '1' : '0';
		$wms4_checkbox = isset($_POST['wms4']) ? '1' : '0';
		$wms5_checkbox = isset($_POST['wms5']) ? '1' : '0';
		$wms6_checkbox = isset($_POST['wms6']) ? '1' : '0';
		$wms7_checkbox = isset($_POST['wms7']) ? '1' : '0';
		$wms8_checkbox = isset($_POST['wms8']) ? '1' : '0';
		$wms9_checkbox = isset($_POST['wms9']) ? '1' : '0';
		$wms10_checkbox = isset($_POST['wms10']) ? '1' : '0';
		$listmarkers_checkbox = isset($_POST['listmarkers']) ? '1' : '0';
		$panel_checkbox = isset($_POST['panel']) ? '1' : '0';
		$layername_quotes = str_replace("\\\\","/", str_replace("\"","'", sanitize_text_field($_POST['name']))); //info: backslash and double quotes break geojson
		$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", sanitize_text_field($_POST['address']))); //info: tabs break geojson
		$multi_layer_map_checkbox = isset($_POST['multi_layer_map']) ? '1' : '0';
		$mlm_checked_imploded = isset($_POST['mlm-all']) ? 'all' : '';
		$clustering = '1';  //info: added for compat
		$gpx_url = ''; //info: added for compat
		$gpx_panel_checkbox = '0'; //info: added for compat
		if ($mlm_checked_imploded != 'all') {
			$mlm_checked_temp = '';
			foreach ($layerlist as $mlmrow){
				$mlm_checked{$mlmrow['lid']} = isset($_POST['mlm-'.$mlmrow['lid'].'']) ? $mlmrow['lid'].',' : '';
				$mlm_checked_temp .= $mlm_checked{$mlmrow['lid']};
			}
			$mlm_checked_imploded = substr($mlm_checked_temp, 0, -1);
		}

		$result = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d)", $layername_quotes, $_POST['basemap'], $_POST['layerzoom'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, str_replace(',', '.', floatval($_POST['layerviewlat'])), str_replace(',', '.', floatval($_POST['layerviewlon'])), $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $listmarkers_checkbox, $multi_layer_map_checkbox, $mlm_checked_imploded, $address, $clustering, $gpx_url, $gpx_panel_checkbox );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $wpdb->insert_id . '&status=published&Layername=' . $layername_quotes . '"; </script> ';
		}
	    else
		{
		echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
    }
  }
  elseif ($action == 'edit') {
  if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
		global $current_user;
		wp_get_current_user();
		//info: set values for wms checkboxes status
		$wms_checkbox = isset($_POST['wms']) ? '1' : '0';
		$wms2_checkbox = isset($_POST['wms2']) ? '1' : '0';
		$wms3_checkbox = isset($_POST['wms3']) ? '1' : '0';
		$wms4_checkbox = isset($_POST['wms4']) ? '1' : '0';
		$wms5_checkbox = isset($_POST['wms5']) ? '1' : '0';
		$wms6_checkbox = isset($_POST['wms6']) ? '1' : '0';
		$wms7_checkbox = isset($_POST['wms7']) ? '1' : '0';
		$wms8_checkbox = isset($_POST['wms8']) ? '1' : '0';
		$wms9_checkbox = isset($_POST['wms9']) ? '1' : '0';
		$wms10_checkbox = isset($_POST['wms10']) ? '1' : '0';
		$listmarkers_checkbox = isset($_POST['listmarkers']) ? '1' : '0';
		$panel_checkbox = isset($_POST['panel']) ? '1' : '0';
		$layername_quotes = str_replace("\\\\","/", str_replace("\"","'", $_POST['name'])); //info: backslash and double quotes break geojson
		$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", $_POST['address'])); //info: tabs break geojson
		$multi_layer_map_checkbox = isset($_POST['multi_layer_map']) ? '1' : '0';
		$mlm_checked_imploded = isset($_POST['mlm-all']) ? 'all' : '';
		$clustering = '1';  //info: added for compat
		$gpx_url = ''; //info: added for compat
		$gpx_panel_checkbox = '0'; //info: added for compat
		if ($mlm_checked_imploded != 'all') {
			$mlm_checked_temp = '';
			foreach ($layerlist as $mlmrow){
				$mlm_checked{$mlmrow['lid']} = isset($_POST['mlm-'.$mlmrow['lid'].'']) ? $mlmrow['lid'].',' : '';
				$mlm_checked_temp .= $mlm_checked{$mlmrow['lid']};
			}
			$mlm_checked_imploded = substr($mlm_checked_temp, 0, -1);
		}

		$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $layername_quotes, $_POST['basemap'], $_POST['layerzoom'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, str_replace(',', '.', $_POST['layerviewlat']), str_replace(',', '.', $_POST['layerviewlon']), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $listmarkers_checkbox, $multi_layer_map_checkbox, $mlm_checked_imploded, $address, $clustering, $gpx_url, $gpx_panel_checkbox, $oid );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $oid . '&status=updated&Layername=' . urlencode($_POST['name']) . '"; </script> ';
  }
  else
	{
		echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
    }
  }
  elseif ($action == 'deleteboth') {
		$result = $wpdb->prepare( "DELETE FROM `$table_name_markers` WHERE `layer` = %d", $oid );
		$wpdb->query( $result );
		$result2 = $wpdb->prepare( "DELETE FROM `$table_name_layers` WHERE `id` = %d", $oid );
		$wpdb->query( $result2 );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
        echo '<p><div class="updated" style="padding:10px;">' . __('Layer and assigned markers have been successfully deleted','lmm') . '</div><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('list all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
  }
  elseif ($action == 'delete') {
		$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `layer` = 0 WHERE `layer` = %d", $oid );
		$wpdb->query( $result );
		$result2 = $wpdb->prepare( "DELETE FROM `$table_name_layers` WHERE `id` = %d", $oid );
		$wpdb->query( $result2 );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		echo '<div class="updated" style="padding:10px;">' . __('Layer has been successfully deleted (assigned markers have not been deleted)','lmm') . '</div><p><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('list all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
  }
  elseif ($action == 'switcheditor') {
	if ($new_editor == 'advanced') {
		update_option( 'leafletmapsmarker_editor', $new_editor );
		if ( $layerid != NULL ) {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $layerid . '&status=advanced"; </script> ';
		} else {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&status=advanced"; </script> ';
		}
	} else if ($new_editor == 'simplified') {
		update_option( 'leafletmapsmarker_editor', $new_editor );
		if ( $layerid != NULL ) {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $layerid . '&status=simplified"; </script> ';
		} else {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&status=simplified"; </script> ';
		}
	}
  }
}
else {
  global $current_user;
  wp_get_current_user();
  $id = '';
  $name = '';
  $basemap = $lmm_options[ 'standard_basemap' ];
  //info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
  if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
  } else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
  }
  $layerviewlat = str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lat' ]));
  $layerviewlon = str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lon' ]));
  $layerzoom = intval($lmm_options[ 'defaults_layer_zoom' ]);
  $mapwidth = intval($lmm_options[ 'defaults_layer_mapwidth' ]);
  $mapwidthunit = $lmm_options[ 'defaults_layer_mapwidthunit' ];
  $mapheight = intval($lmm_options[ 'defaults_layer_mapheight' ]);
  $panel = $lmm_options[ 'defaults_layer_panel' ];
  $lcreatedby = '';
  $lcreatedon = '';
  $lupdatedby = '';
  $lupdatedon = '';
  $lcontrolbox = $lmm_options[ 'defaults_layer_controlbox' ];
  $loverlays_custom = ( (isset($lmm_options[ 'defaults_layer_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
  $loverlays_custom2 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
  $loverlays_custom3 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
  $loverlays_custom4 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
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
  $llistmarkers = $lmm_options[ 'defaults_layer_listmarkers' ];
  $multi_layer_map = 0;
  $multi_layer_map_list = array();
  $multi_layer_map_list_exploded = array();
  $laddress = '';
  $markercount = 0;
  $isedit = isset($_GET['id']);
  if ($isedit) {
    $id = intval($_GET['id']);
    $row = $wpdb->get_row('SELECT l.id as lid, l.name as lname, l.basemap as lbasemap, l.layerzoom as llayerzoom, l.mapwidth as lmapwidth, l.mapwidthunit as lmapwidthunit, l.mapheight as lmapheight, l.panel as lpanel, l.layerviewlat as llayerviewlat, l.layerviewlon as llayerviewlon, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon, l.controlbox as lcontrolbox, l.overlays_custom as loverlays_custom, l.overlays_custom2 as loverlays_custom2, l.overlays_custom3 as loverlays_custom3, l.overlays_custom4 as loverlays_custom4,l.wms as lwms, l.wms2 as lwms2, l.wms3 as lwms3, l.wms4 as lwms4, l.wms5 as lwms5, l.wms6 as lwms6, l.wms7 as lwms7, l.wms8 as lwms8, l.wms9 as lwms9, l.wms10 as lwms10, l.listmarkers as llistmarkers, l.multi_layer_map as lmulti_layer_map, l.address as laddress, m.id as markerid, m.markername as markername, m.lat as mlat, m.lon as mlon, m.icon as micon, m.popuptext as mpopuptext, m.zoom as mzoom, m.mapwidth as mmapwidth, m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight, m.address as maddress FROM `'.$table_name_layers.'` as l LEFT OUTER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$id, ARRAY_A);
    $name = htmlspecialchars($row['lname']);
    $basemap = $row['lbasemap'];
	//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
    if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
    } else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
    }
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
    $micon = $row['micon'];
    $popuptext = $row['mpopuptext'];
    $markerzoom = $row['mzoom'];
    $markermapwidth = $row['mmapwidth'];
    $markermapwidthunit = $row['mmapwidthunit'];
    $markermapheight = $row['mmapheight'];
    $panel = $row['lpanel'];
    $lcreatedby = esc_html($row['lcreatedby']);
    $lcreatedon = $row['lcreatedon'];
    $lupdatedby = esc_html($row['lupdatedby']);
    $lupdatedon = $row['lupdatedon'];
    $lcontrolbox = $row['lcontrolbox'];
    $loverlays_custom = $row['loverlays_custom'];
    $loverlays_custom2 = $row['loverlays_custom2'];
    $loverlays_custom3 = $row['loverlays_custom3'];
    $loverlays_custom4 = $row['loverlays_custom4'];
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
    $llistmarkers = $row['llistmarkers'];
    $multi_layer_map = $row['lmulti_layer_map'];
    $multi_layer_map_list = $wpdb->get_var('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id='.$id);
    $multi_layer_map_list_exploded = explode(",", $wpdb->get_var('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id='.$id));
    $laddress = htmlspecialchars($row['laddress']);

	//info: markercount
	if ($multi_layer_map == 0) {
	    $markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$id);
	} else 	if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == 'all' ) ) {
		$markercount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
	} else 	if ( ($multi_layer_map == 1) && ( $multi_layer_map_list != NULL ) && ($multi_layer_map_list != 'all') ) {
		foreach ($multi_layer_map_list_exploded as $mlmrowcount){
		$mlm_count_temp{$mlmrowcount} = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$mlmrowcount);
		}
		$markercount = array_sum($mlm_count_temp);
	} else 	if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == NULL ) ) {
		$markercount = 0;
	}
  }
	//info: sqls for singe and multi-layer-maps
	if ($id == NULL) { //info: no mysql-query on new layer creation
		$layer_marker_list = NULL;
		$layer_marker_list_table = NULL;
	} else if ($multi_layer_map == 0) {
   		//info: overwrite where statement for new layer maps (otherwise debug error sql statements $layer_marker_list and $layer_marker_list_table
		if ($id == '') { $sql_where = ''; } else { $sql_where = 'WHERE l.id=' . $id; }
		$layer_marker_list = $wpdb->get_results('SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer ' . $sql_where . ' ORDER BY ' . $lmm_options[ 'defaults_layer_listmarkers_order_by' ] . ' ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
		$layer_marker_list_table = $wpdb->get_results('SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer ' . $sql_where . ' ORDER BY ' . $lmm_options[ 'defaults_layer_listmarkers_order_by' ] . ' ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
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
				$mlm_query_table = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM " . $table_name_layers . "  as l INNER JOIN " . $table_name_markers . " AS m ON l.id=m.layer WHERE l.id='" . $multi_layer_map_list . "')";
				$mlm_query_table .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['defaults_layer_listmarkers_limit']) . "";
				$layer_marker_list_table = $wpdb->get_results($mlm_query_table, ARRAY_A);
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
				$mlm_query_table = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query_table .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $row . "')";
				}
				$mlm_query_table .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['defaults_layer_listmarkers_limit']) . "";
				$layer_marker_list_table = $wpdb->get_results($mlm_query_table, ARRAY_A);
			} //info: end else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all')
			else if ($multi_layer_map_list == 'all') {
				$first_mlm_id = '0';
				$mlm_all_layers = $wpdb->get_results( "SELECT `id` FROM `$table_name_layers`", ARRAY_A );
				$other_mlm_ids = array_slice($mlm_all_layers,1);
				$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $row['id'] . "')";
				}
				$mlm_query .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['defaults_layer_listmarkers_limit']) . "";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
				$mlm_query_table = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
				foreach ($other_mlm_ids as $row) {
					$mlm_query_table .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON l.id=m.layer WHERE l.id='" . $row['id'] . "')";

				}
				$mlm_query_table .= " ORDER BY " . $sort_order_mlm . " " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['defaults_layer_listmarkers_limit']) . "";
				$layer_marker_list_table = $wpdb->get_results($mlm_query_table, ARRAY_A);
			} //info: end else if ($multi_layer_map_list == 'all')
			else { //info: if ($multi_layer_map == 1) but no layers selected
				$layer_marker_list_table = array();
			}
    } //info: end main - else if ($multi_layer_map == 1)
//info: check if layer exists - part 1
if ($layerviewlat === NULL) {
$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), htmlspecialchars($_GET['id']));
echo '<p><div class="error" style="padding:10px;">' . $error_layer_not_exists . '</div></p>';
echo '<p><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('list all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
} else { ?>

<?php
$edit_status = isset($_GET['status']) ? $_GET['status'] : '';
if ( $edit_status == 'updated') {
	echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully updated','lmm') . '</div>';
} else if ( $edit_status == 'published') {
	echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully published','lmm') . '</div>';
} else if ( $edit_status == 'simplified') {
	echo '<p><div class="updated" style="padding:10px;">' . __('You successfully switched to the simplified editor.','lmm') . '</div>';
} else if ( $edit_status == 'advanced') {
	echo '<p><div class="updated" style="padding:10px;">' . __('You successfully switched to the advanced editor.','lmm') . '</div>';
} ?>

	<?php $nonce= wp_create_nonce('layer-nonce'); ?>
	<form id="layer-add-edit" method="post">
		<?php wp_nonce_field('layer-nonce'); ?>
		<input type="hidden" id="id" name="id" value="<?php echo $id ?>" />
		<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
		<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
		<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $loverlays_custom ?>" />
		<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $loverlays_custom2 ?>" />
		<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $loverlays_custom3 ?>" />
		<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $loverlays_custom4 ?>" />
		<?php
		$noncelink = wp_create_nonce('layer-nonce');
		if ($current_editor == 'simplified') {
			echo '<div id="editmodeswitch" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<div style="float:right;"><a style="text-decoration:none;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&action=switcheditor&new_editor=advanced&_wpnonce=' . $noncelink . '&layerid=' . $id . '" onclick="return confirm(\'' . esc_attr__('Please note that unsaved input will not be passed to the new editor! Please click "OK" to switch the editor anyway or "Cancel" to go back and save first.','lmm') . '\')"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="Editor-Switch-Icon" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
		} else if ($current_editor == 'advanced') {
			echo '<div id="editmodeswitch" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<div style="float:right;"><a style="text-decoration:none;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&action=switcheditor&new_editor=simplified&_wpnonce=' . $noncelink . '&layerid=' . $id . '" onclick="return confirm(\'' . esc_attr__('Please note that unsaved input will not be passed to the new editor! Please click "OK" to switch the editor anyway or "Cancel" to go back and save first.','lmm') . '\')"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="Editor-Switch-Icon" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
		}
		?>
		<h1 style="margin:10px 0 0 0;"><?php ($isedit === true) ? _e('Edit layer','lmm') : _e('Add new layer','lmm') ?>
		<?php echo ($isedit === true) ?' "' . stripslashes($name) . '" (ID '.$id.')' : '' ?></h1>
		
		<table class="layer_buttons_table">
			<tr style="display:block;padding-bottom:5px;">
				<td>
					<input id="submit_top" style="font-weight:bold;" class="submit button-primary" type="submit" name="layer" value="<?php ($isedit === true) ? _e('update','lmm') : _e('publish','lmm') ?>" />
					</form>
				</td>
				<td>
					<?php $multi_layer_map_edit_button = ( ($multi_layer_map == 0) && ($id != NULL) ) ? '<a class="button-secondary lmm-nav-secondary" style="margin-left:15px;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $oid . '">' . __('add new marker to this layer','lmm') . '</a>' : '';
					echo $multi_layer_map_edit_button; ?>
				</td>
				
				<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
				<td>
					<form method="post">
						<?php wp_nonce_field('layer-nonce'); ?>
						<input type="hidden" name="id" value="<?php echo $id ?>" />
						<input type="hidden" name="action" value="delete" />
						<?php $confirm = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s)?','lmm'), $row['lname'], $id) ?>
						<input id="delete" class="submit button-secondary lmm-nav-secondary" style="color:#FF0000;margin-left:20px;" type="submit" name="layer" value="<?php _e('delete layer only', 'lmm') ?>" onclick="return confirm('<?php echo $confirm ?>')"/>
					</form>
				</td>
				<?php } ?>
				<?php if ($isedit) { ?>
				<td>
					<span class="show-advanced-layer-edit-buttons" style="display:inline;"><a style="cursor:pointer;" title="<?php esc_attr_e('show advanced functions','lmm'); ?>">>>></a></span>
					<span class="hide-advanced-layer-edit-buttons" style="display:none;"><a style="cursor:pointer;" title="<?php esc_attr_e('hide advanced functions','lmm'); ?>"><<<</a></span>
				</td>
				<?php } ?>
				
				<td class="advanced-layer-edit-button" style="display:none;text-align:center;">
					<?php
						echo '<a style="text-decoration:none;"href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-feature.png" width="70" height="15" /><br/>' . __('duplicate layer only', 'lmm') . '</a>';
					?>
					</td>
					<td class="advanced-layer-edit-button" style="display:none;text-align:center;padding-left:20px">
					<?php
						echo '<a style="text-decoration:none;"href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-feature.png" width="70" height="15" /><br/>' . __('duplicate layer and assigned markers', 'lmm') . '</a>';
					?>
				</td>
				
				<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
				<td class="advanced-layer-edit-button" style="display:none;">
					<form method="post">
						<?php wp_nonce_field('layer-nonce'); ?>
						<input type="hidden" name="id" value="<?php echo $id ?>" />
						<input type="hidden" name="action" value="deleteboth" />
						<?php $confirm2 = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s) and all %3$s assigned markers?','lmm'), $row['lname'], $id, $markercount) ?>
						<?php if ($multi_layer_map == 0) {
							echo "<input id='delete_layer_and_markers' class='submit button-secondary lmm-nav-secondary' style='color:#FF0000;margin-left:20px;' type='submit' name='layer' value='" . __('delete layer AND assigned markers', 'lmm') . "' onclick='return confirm(\"".$confirm2 ."\")' />";
						} ?>
					</form>
				</td>
				<?php } ?>
			</tr>
		</table>
		<p style="margin-top:0px;">
		<?php echo sprintf(__('To display multiple locations at the same time, create a layer first (e.g. "company locations") and then add markers (e.g. "headquarters", "store A", "store B") assigned to that layer.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer'); ?>
		</p>
		<table class="widefat">
			<?php if ($isedit === true) { ?>
			<tr>
				<td style="width:230px;"><label for="shortcode"><strong><?php _e('Shortcode and API links','lmm') ?>:</strong></label></td>
				<td><input <?php echo $shortcode_select; ?> style="width:206px;background:#f3efef;" type="text" value="[<?php echo htmlspecialchars($lmm_options[ 'shortcode' ]); ?> layer=&quot;<?php echo $id?>&quot;]">
				<?php
				if ($current_editor == 'simplified') {
					echo '<div id="apilinkstext" style="display:inline;"><a tabindex="100" style="cursor:pointer;">' . __('show API links','lmm') . '</a></div>';
					echo '<span id="apilinks" style="display:none;">';
				}
				?>
					 <a tabindex="101" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '' ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a tabindex="102" href="https://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="103" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> <?php _e('Fullscreen','lmm'); ?></a> <span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="104" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-qr.php?layer=' . $id . '&callback=jsonp&full=yes&full_icon_url=yes' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> <?php _e('QR code','lmm'); ?></a> <span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span><?php if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; } ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="105" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $geojson_api_link . '&callback=jsonp&full=yes&full_icon_url=yes' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a tabindex="106" href="https://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="107" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" /> GeoRSS</a> <a tabindex="108" href="https://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="109" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a tabindex="110" href="https://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="134" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_apis"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-page.png" width="16" height="16" alt="Mapsmarker-Logo" /> Maps Marker API</a>
				<?php
					if ($current_editor == 'simplified') {
						echo '</span>';
					}
				?>
					<br/>
					<small>
					<?php _e('Use this shortcode in posts or pages on your website or one of the API links for embedding in external websites or apps','lmm'); ?>.<br/>
					<?php echo sprintf(__('Tip: highlight an assigned marker by adding the marker ID as shortcode attribute (e.g. %1$s) or add %2$s to the URL where the map is embedded','lmm'), '[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' layer="' . $id . '" <strong>highlightmarker="1"</strong>]', '<strong>?highlightmarker=1</strong>'); ?> - <a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a>
					</small>
				</td>
			</tr>
			<?php } ?>
			<?php if ($isedit === true) { $used_in_content_visibility = 'table-row'; } else { $used_in_content_visibility = 'none'; }?>
			<tr style="display:<?php echo $used_in_content_visibility; ?>;">
				<td style="width:230px;" class="lmm-border"><strong><?php _e('Used in content','lmm') ?></strong></td>
				<td class="lmm-border"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a></td>
			</tr>
			<tr>
				<td style="width:230px;" class="lmm-border"><label for="layername"><strong><?php _e('Layer name', 'lmm') ?></strong></label></td>
				<td class="lmm-border"><input autofocus="autofocus" style="width: 640px;" maxlenght="255" type="text" id="layername" name="name" value="<?php echo stripslashes($name) ?>" /> (<a target="_blank" href="https://www.mapsmarker.com/multilingual" target="_blank"><?php _e('translate', 'lmm'); ?></a> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/pro-feature-banner-small.png" width="68" height="9" border="0">)</td>
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
					echo '<input style="margin:0px;width:640px;' . $location_input_css . '" type="text" id="address" name="address" value="' . stripslashes(htmlspecialchars($laddress)) . '" placeholder="' .  $placeholder_text . '" />';
					?>
                     (<a target="_blank" href="https://www.mapsmarker.com/multilingual" target="_blank"><?php _e('translate', 'lmm'); ?></a> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/pro-feature-banner-small.png" width="68" height="9" border="0">)
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
				<input id="mapwidthunit_px" type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>><label for="mapwidthunit_px" title="<?php esc_attr_e('pixel','lmm'); ?>">px</label>&nbsp;&nbsp;&nbsp;
				<input id="mapwidthunit_percent" type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>><label for="mapwidthunit_percent">%</label><br/>
				<label for="mapheight"><?php _e('Height','lmm') ?>:</label>
				<input size="3" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" /> <span title="<?php esc_attr_e('pixel','lmm'); ?>">px</span>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<label for="layerzoom"><strong><?php _e('Zoom','lmm') ?></strong> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('You can also change zoom level by clicking on + or - on preview map or using your mouse wheel'); ?>" width="12" height="12" border="0"/></label>&nbsp;<input id="layerzoom" style="width: 40px;" type="text" id="layerzoom" name="layerzoom" value="<?php echo $layerzoom ?>" />
				<?php
				echo ' <span style="' . $current_editor_css . '"><br/><small>' . __('Global maximum zoom level','lmm') . ': <a title="' . esc_attr__('If the native maximum zoom level of a basemap is lower, tiles will be upscaled automatically.','lmm') . '" tabindex="111" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('pro version only','lmm') . '</a></small></span>';
				?>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<strong><label for="listmarkers"><?php _e('Show list of markers below map','lmm') ?></label></strong>&nbsp;<input type="checkbox" name="listmarkers" id="listmarkers" <?php checked($llistmarkers, 1 ); ?>><br/>
				<?php
						echo '<small>';
						_e('Max. number of markers to display:','lmm');
						echo ' ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]);
						if (current_user_can('activate_plugins')) {
							echo ' <span style="' . $current_editor_css . '"><a tabindex="113" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-list_of_markers">(' . __('Settings','lmm') . ')</a></span>';
						}
						echo '</small>';
				?>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<label for="clustering"><strong><?php _e('Marker clustering','lmm') ?></strong></label>&nbsp;&nbsp;<input type="checkbox" name="clustering" id="clustering" disabled="disabled" />
				<?php if (current_user_can('activate_plugins')) {
					echo '&nbsp;&nbsp;<small>(<a tabindex="115" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-clustering">' . __('Settings','lmm') . '</a>)</small>';
				} ?>
				<br/><small>
				<a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a>
				</small>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<div style="float:right;"><label for="gpx_panel"><?php _e('display panel','lmm') ?></label>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="gpx_panel" id="gpx_panel" disabled="disabled"></div>
				<label for="gpx_url"><strong><?php _e('URL to GPX track','lmm') ?></strong></label><br/>
				<input style="width:229px;" type="text" id="gpx_url" name="gpx_url" value="<?php echo __(' Feature available in pro version only','lmm'); ?>" disabled="disabled" /><br/>
				<?php echo '<small>' . __('add','lmm') . ' | ' . __('convert','lmm') . ' | ' . __('merge','lmm') . ' | ' . __('settings','lmm') . ' | ' . __('fit bounds','lmm') . '</small>'; ?>
				</p>
				<div style="<?php echo $current_editor_css; ?>">
				<p>
				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
				<strong><?php _e('Controlbox for basemaps/overlays','lmm') ?>:</strong><br/>
				<input style="margin-top:1px;" id="controlbox_hidden" type="radio" name="controlbox" value="0" <?php checked($lcontrolbox, 0); ?>><label for="controlbox_hidden"><?php _e('hidden','lmm') ?></label><br/>
				<input style="margin-top:1px;" id="controlbox_collapsed" type="radio" name="controlbox" value="1" <?php checked($lcontrolbox, 1); ?>><label for="controlbox_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
				<input style="margin-top:1px;" id="controlbox_expanded" type="radio" name="controlbox" value="2" <?php checked($lcontrolbox, 2); ?>><label for="controlbox_expanded"><?php _e('expanded','lmm') ?></label>

				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

				<strong><label for="panel"><?php _e('Display panel','lmm') ?></label></strong>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="panel" id="panel" <?php checked($panel, 1 ); ?>><br/>
				<small><?php _e('If checked, panel on top of map is displayed','lmm') ?></small>
				</div>
				</td>
				<td style="padding-bottom:5px;" class="lmm-border">
					<?php
					echo '<div id="lmm" class="lmm-rtl" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
					//info: panel for layer name and API URLs
					$panel_state = ($panel == 1) ? 'block' : 'none';
					echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_background_color' ])) . ';">'.PHP_EOL;
					echo '<div class="lmm-panel-api">';
						if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
							echo '<a tabindex="114" href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
							echo '<a tabindex="115" href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
							echo '<a tabindex="116" href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?layer=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
							if ($multi_layer_map == 0 ) { $geojson_api_link = $id; } else { $geojson_api_link = $multi_layer_map_list; }
							echo '<a tabindex="117" href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $geojson_api_link . '&callback=jsonp&full=yes&full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
							echo '<a tabindex="118" href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
							echo '<a tabindex="119" href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
						}
					echo '</div>'.PHP_EOL;
					echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ])) . '">' . (($name == NULL) ? __('if set, layername will be displayed here','lmm') : stripslashes($name)) . '</div>'.PHP_EOL;
					?>
					</div> <!--end lmm-panel-->
					<div id="selectlayer" style="height:<?php echo $mapheight; ?>px;"></div>
					<?php
					//info: display a list of markers
					$listmarkers_state = ($llistmarkers == 0) ? 'none' : 'block';
					echo '<div id="lmm-listmarkers" class="lmm-listmarkers" style="display:' . $listmarkers_state . ';">'.PHP_EOL;
					//info: set list markers width to be 100% of maps width
					if ($mapwidthunit == '%') {
						$layer_marker_list_width = '100%';
					} else {
						$layer_marker_list_width = $mapwidth.$mapwidthunit;
					}
					echo '<table id="lmm-listmarkers-table" cellspacing="0" style="width:' . $layer_marker_list_width . ';" class="lmm-listmarkers-table">';
					if ($markercount == 0) {
						echo '<tr><td style="border-style:none;width:35px;"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" /></td>';
						echo '<td style="border-style:none;"><div style="float:right;"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" class="lmm-panel-api-images" /></div><strong>'.__('Markers assigned to this layer will be listed here', 'lmm').'</strong></td></tr>';
					} else {
						if ($layer_marker_list != NULL) { //info: to prevent PHP errors
							foreach ($layer_marker_list as $row){
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_icon' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_icon' ] == 1 ) ) {
									echo '<tr><td class="lmm-listmarkers-icon">';
									if ($row['micon'] != null) {
										echo '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/'.$row['micon'].'" />';
									} else {
										echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" />';
									};
								} else {
									echo '<tr><td>';
								}
								echo '</td><td class="lmm-listmarkers-popuptext"><div class="lmm-listmarkers-panel-icons">';
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
											$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
										}
										echo '<a tabindex="127" href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
									} else if ($lmm_options['directions_provider'] == 'yours') {
										if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
										echo '<a tabindex="128" href="http://www.yournavigation.org/?tlat=' . $row['mlat'] . '&tlon=' . $row['mlon'] . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
									} else if ($lmm_options['directions_provider'] == 'ors') {
										if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
										echo '<a tabindex="130" href="http://www.openrouteservice.org/?pos=' . $row['mlon'] . ',' . $row['mlat'] . '&wp=' . $row['mlon'] . ',' . $row['mlat'] . '&zoom=' . $row['mzoom'] . '&routeWeigh=' . $lmm_options[ 'directions_ors_routeWeigh' ] . '&routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
									} else if ($lmm_options['directions_provider'] == 'bingmaps') {
										if ( $row['maddress'] != NULL ) { $bing_to = '_' . urlencode($row['maddress']); } else { $bing_to = ''; }
										echo '<a tabindex="130" href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $row['mlat'] . '_' . $row['mlon'] . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" /></a>';
									}
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="131" href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_kml' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="132" href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['markerid'] . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="133" href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $row['markerid'] . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="134" href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $row['markerid'] . '&callback=jsonp&full=yes&full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_georss' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="135" href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] == 1 ) ) {
									echo '&nbsp;<a tabindex="136" href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
								}
								echo '</div>';
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_markername' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_markername' ] == 1 ) ) {
									echo '<span class="lmm-listmarkers-markername">' . stripslashes(htmlspecialchars($row['markername'])) . '</span> (<a title="' . esc_attr__('Edit marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'">' . __('edit','lmm') . '</a>)';
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
									echo '<br/><span class="lmm-listmarkers-popuptext-only">' . $popuptext_sanitized . '</span>';
								}
								if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_address' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_address' ] == 1 ) ) {
									if ( $row['mpopuptext'] == NULL ) {
										echo stripslashes(htmlspecialchars($row['maddress']));
									} else if ( ($row['mpopuptext'] != NULL) && ($row['maddress'] != NULL) ) {
										echo '<br/><div class="lmm-listmarkers-hr">' . stripslashes(htmlspecialchars($row['maddress'])) . '</div>';
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
						} else if ($lmm_options['defaults_layer_listmarkers_order_by'] == 'm.address') {
							$orderby = __('address','lmm');
						}
						echo '<tr><td colspan="2" style="text-align:center">' . sprintf(__('The table above is listing %1s out of %2s markers (sorted by %3s %4s)','lmm'), intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), $markercount, $orderby, $asc_desc) . '</td></tr>';
					}
					?>
					</table>
					</div> <!--end lmm-listmarkers-->
					</div><!--end mapsmarker div-->
				</td>
			</tr>
			<tr>
				<td class="lmm-border"><p><strong><label for="multi_layer_map"><?php _e('Multi Layer Map','lmm') ?></label></strong>&nbsp;
					<input type="checkbox" name="multi_layer_map" id="multi_layer_map" <?php checked($multi_layer_map, 1 ); ?>><br/>
					<small><?php _e('Show markers from other layers on this map','lmm') ?></small></p>
					<p>
				<strong><?php _e('Filter controlbox:','lmm'); ?></strong><br/>
				<input style="margin-top:1px;" id="controlbox_mlm_filter_hidden" type="radio" name="controlbox_mlm_filter" value="0" disabled="disabled"><label for="controlbox_mlm_filter_hidden"><?php _e('hidden','lmm') ?></label><br/>
				<input style="margin-top:1px;" id="controlbox_mlm_filter_collapsed" type="radio" name="controlbox_mlm_filter" value="1" checked="checked" disabled="disabled"><label for="controlbox_mlm_filter_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
				<input style="margin-top:1px;" id="controlbox_mlm_filter_expanded" type="radio" name="controlbox_mlm_filter" value="2" disabled="disabled"><label for="controlbox_mlm_filter_expanded"><?php _e('expanded','lmm') ?></label><br/>
				<small><?php _e('Allows you to toggle marker display on frontend','lmm') ?>
				<?php if (current_user_can('activate_plugins')) {
				echo '<span id="toggle-mlm-filters-settings">(<a tabindex="125" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-filtering">' . __('Settings','lmm') . '</a>)</span>';
			} ?>
				<a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a>
				</small>
				</p>
				</td>
				<td class="lmm-border">
					<?php
					$multi_layer_map_state = ($multi_layer_map == 1) ? 'block' : 'none';
					echo '<div id="lmm-multi_layer_map" style="display:' . $multi_layer_map_state . ';">'.PHP_EOL;
					echo __('Please select the layers, whose markers you would like to display on this multi layer map.','lmm') . ' ' .
					__('The following features are not supported for multi layer maps: adding markers directly and dynamic preview on backend.','lmm') . ' ' .
					__('Please do not change an existing layer map with assigned markers into a multi layer map, as those assigned markers will not be displayed on the multi layer map!','lmm').PHP_EOL;
					$mlm_checked_all = ( in_array('all', $multi_layer_map_list_exploded) ) ? ' checked="checked"' : '';
					echo '<br/><br/><input id="mlm-all" type="checkbox" id="mlm-all" name="mlm-all" ' . $mlm_checked_all . '> <label for="mlm-all">' . __('display all markers','lmm') . '</label><br/><br/><strong>' . __('Display markers from selected layers only','lmm') . '</strong><br/>';

					$pro_feature_banner_inline = ' <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/pro-feature-banner.png" width="75" height="15" style="display:inline;" /></a>';
					echo '<table  cellspacing="0" id="list-markers" class="wp-list-table widefat lmm-mlm-layers-table" style="width:100%;margin-top:5px;">
							<thead>
								<tr>
									<th class="manage-column column-cb check-column" scope="col"></th>
									<th class="manage-column before_primary column-id" scope="col"><strong>ID</strong></th>
									<th class="manage-column column-primary column-layername" scope="col"><strong>' . __('layer name','lmm') . '</strong></th>
									<th class="manage-column column-markercount" scope="col" style="text-align:center;"><strong>' . __('marker count','lmm') . '</strong></th>
									<th class="manage-column column-addtocontrolbox" scope="col"><strong>' . __('add layer to filter controlbox?','lmm') . '</strong>' . $pro_feature_banner_inline . '</th>
									<th class="manage-column column-icon" scope="col"><strong>' . __('icon url for filter controlbox','lmm') . '</strong>' . $pro_feature_banner_inline . '</th>
									<th class="manage-column column-filtername" scope="col"><strong>' . __('name for filter controlbox','lmm') . '</strong>' . $pro_feature_banner_inline . '</th>
								</tr>
							</thead>
							<tbody id="the-list">';

					if ($layerlist == NULL) {
						echo '<tr><td colspan="5">' . __('No layer has been created yet','lmm') . '</td></tr>';
					} else {
						foreach ($layerlist as $mlmrow){
							$mlm_markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$mlmrow['lid']);
							if ( in_array($mlmrow['lid'], $multi_layer_map_list_exploded) ) {
								$mlm_checked{$mlmrow['lid']} = ' checked="checked"';
							} else {
								$mlm_checked{$mlmrow['lid']} = '';
							}
							if ( ($id != $mlmrow['lid']) || ($mlm_markercount != 0) ) { //info: make current layer selectable for MLM if has markers only
								echo '<tr valign="middle" class="alternate">
								<th class="check-column" scope="row">
								<input type="checkbox" id="mlm-'.$mlmrow['lid'].'" name="mlm-'.$mlmrow['lid'].'" ' . $mlm_checked{$mlmrow['lid']} . '> <label for="mlm-'.$mlmrow['lid'].'" />
								</th>
								<td class="before_primary" style="padding-left:0px;padding-right:0px;" data-colname="ID">
									<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id='.$mlmrow['lid'].'" title="' . esc_attr__('show map','lmm') . '" target="_blank">' . $mlmrow['lid'] . '</a>
								</td>
								<td class="column-primary has-row-actions" data-colname="'.__('layer name', 'lmm').'">
									' . stripslashes(htmlspecialchars($mlmrow['lname'])) . ' <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
								</td>
								<td style="text-align:center;" data-colname="'.__('marker count', 'lmm').'">
									' . $mlm_markercount . '
								</td>
								<td data-colname="'.__('add layer to filter controlbox?', 'lmm').'">
									<select id="mlm_filter_status_' . $mlmrow['lid'] . '" class="mlm_filter_status" data-layerid="'. $mlmrow['lid'] .'"  name="mlm_filter_status_' . $mlmrow['lid'] . '">
										<option value="pro" disabled="disabled" selected="selected">' . esc_attr__('Feature available in pro version only','lmm') . '</option>
										<option value="0" disabled="disabled">' . esc_attr__('no','lmm') . '</option>
										<option value="1" disabled="disabled">' . esc_attr__('yes (checked)','lmm') . '</option>
										<option value="2" disabled="disabled">' . esc_attr__('yes (unchecked)','lmm') . '</option>
									</select>
								</td>
								<td data-colname="'.__('icon url for filter controlbox', 'lmm').'">
									<input style="height:25px; width:100%;" id="mlm_filter_icon_' . $mlmrow['lid'] . '" type="url" value="" placeholder="' . esc_attr__('Feature available in pro version only','lmm') . '" disabled="disabled"/>
								</td>
								<td data-colname="'.__('name for filter controlbox', 'lmm').'">
									<input style="height:25px; width:100%;" id="mlm_filter_name_' . $mlmrow['lid'] . '" type="text" value="" placeholder="'.esc_attr__('Feature available in pro version only','lmm').'" disabled="disabled"/>
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
			<tr id="advanced-settings">
				<td class="lmm-border"><strong><?php _e('Advanced settings','lmm') ?></strong></td>
				<td class="lmm-border">
				<div style="<?php echo $current_editor_css; ?>">
				<p><strong><?php _e('WMS layers','lmm') ?></strong> <?php if (current_user_can('activate_plugins')) { echo '<a tabindex="137" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#wms">(' . __('Settings','lmm') . ')</a>'; } ?></p>
				<?php
				//info: define available wms layers (for markers and layers)
				if ( (isset($lmm_options[ 'wms_wms_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms" name="wms"';
					if ($wms == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms">' . strip_tags($lmm_options[ 'wms_wms_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 1 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms1"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms2_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms2_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms2" name="wms2"';
					if ($wms2 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms2">' . strip_tags($lmm_options[ 'wms_wms2_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 2 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms2"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms3_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms3_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms3" name="wms3"';
					if ($wms3 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms3">' . strip_tags($lmm_options[ 'wms_wms3_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 3 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms3"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms4_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms4_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms4" name="wms4"';
					if ($wms4 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms4">' . strip_tags($lmm_options[ 'wms_wms4_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 4 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms4"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms5_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms5_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms5" name="wms5"';
					if ($wms5 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms5">' . strip_tags($lmm_options[ 'wms_wms5_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 5 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms5"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms6_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms6_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms6" name="wms6"';
					if ($wms6 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms6">' . strip_tags($lmm_options[ 'wms_wms6_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 6 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms6"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms7_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms7_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms7" name="wms7"';
					if ($wms7 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms7">' . strip_tags($lmm_options[ 'wms_wms7_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 7 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms7"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms8_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms8_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms8" name="wms8"';
					if ($wms8 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms8">' . strip_tags($lmm_options[ 'wms_wms8_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 8 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms8"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms9_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms9_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms9" name="wms9"';
					if ($wms9 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms9">' . strip_tags($lmm_options[ 'wms_wms9_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 9 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms9"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms10_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms10_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms10" name="wms10"';
					if ($wms10 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms10">' . strip_tags($lmm_options[ 'wms_wms10_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 10 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms10"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>';
				}
				?>
				<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
				</div>
				<label for="hide-backlinks"><strong style="font-size:98%"><?php _e('Hide MapsMarker.com backlinks','lmm') ?></strong></label>
					&nbsp;&nbsp;<input type="checkbox" name="hide-backlinks" id="hide-backlinks" disabled="disabled" /> <a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a>

					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
					<strong><?php _e('Minimap settings','lmm'); ?></strong> <a tabindex="110" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-minimap"><?php _e('Please visit Settings / Maps / Minimap settings','lmm'); ?></a>
					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
					<strong><?php _e('Geolocate settings','lmm'); ?> </strong>
					<a tabindex="111" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-geolocate"><?php _e('Please visit Settings / Maps / Geolocate settings','lmm'); ?></a>
				</td>
			</tr>
			<?php if ($lcreatedby != NULL) {?>
			<tr style="<?php echo $current_editor_css; ?>">
				<td><small><strong><?php _e('Audit','lmm') ?>:</strong></small></td>
				<td><small>
					<?php
					echo __('Layer added by','lmm') . ' ';
					echo $lcreatedby . ' - ' . $lcreatedon;
					if ($lupdatedon != $lcreatedon) {
						echo ', ' . __('last update by','lmm');
						echo ' ' . $lupdatedby . ' - ' . $lupdatedon;
					}; ?>
					</small></td>
			</tr>
			<?php }; ?>
		</table>

		<table class="layer_buttons_table" >
			<tr style="display:block;padding-top:10px;">
				<td>
					<input id="submit_top" style="font-weight:bold;" class="submit button-primary" type="submit" name="layer" value="<?php ($isedit === true) ? _e('update','lmm') : _e('publish','lmm') ?>" />
					</form>
				</td>
				<td>
					<?php $multi_layer_map_edit_button = ( ($multi_layer_map == 0) && ($id != NULL) ) ? '<a class="button-secondary lmm-nav-secondary" style="margin-left:15px;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $oid . '">' . __('add new marker to this layer','lmm') . '</a>' : '';
					echo $multi_layer_map_edit_button; ?>
				</td>
				
				<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
				<td>
					<form method="post">
						<?php wp_nonce_field('layer-nonce'); ?>
						<input type="hidden" name="id" value="<?php echo $id ?>" />
						<input type="hidden" name="action" value="delete" />
						<?php $confirm = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s)?','lmm'), $row['lname'], $id) ?>
						<input id="delete" class="submit button-secondary lmm-nav-secondary" style="color:#FF0000;margin-left:20px;" type="submit" name="layer" value="<?php _e('delete layer only', 'lmm') ?>" onclick="return confirm('<?php echo $confirm ?>')"/>
					</form>
				</td>
				<?php } ?>
				<?php if ($isedit) { ?>
				<td>
					<span class="show-advanced-layer-edit-buttons" style="display:inline;"><a style="cursor:pointer;" title="<?php esc_attr_e('show advanced functions','lmm'); ?>">>>></a></span>
					<span class="hide-advanced-layer-edit-buttons" style="display:none;"><a style="cursor:pointer;" title="<?php esc_attr_e('hide advanced functions','lmm'); ?>"><<<</a></span>
				</td>
				<?php } ?>
				
				<td class="advanced-layer-edit-button" style="display:none;text-align:center;">
					<?php
						echo '<a style="text-decoration:none;"href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-feature.png" width="70" height="15" /><br/>' . __('duplicate layer only', 'lmm') . '</a>';
					?>
					</td>
					<td class="advanced-layer-edit-button" style="display:none;text-align:center;padding-left:20px">
					<?php
						echo '<a style="text-decoration:none;"href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-feature.png" width="70" height="15" /><br/>' . __('duplicate layer and assigned markers', 'lmm') . '</a>';
					?>
				</td>
				
				<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
				<td class="advanced-layer-edit-button" style="display:none;">
					<form method="post">
						<?php wp_nonce_field('layer-nonce'); ?>
						<input type="hidden" name="id" value="<?php echo $id ?>" />
						<input type="hidden" name="action" value="deleteboth" />
						<?php $confirm2 = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s) and all %3$s assigned markers?','lmm'), $row['lname'], $id, $markercount) ?>
						<?php if ($multi_layer_map == 0) {
							echo "<input id='delete_layer_and_markers' class='submit button-secondary lmm-nav-secondary' style='color:#FF0000;margin-left:20px;' type='submit' name='layer' value='" . __('delete layer AND assigned markers', 'lmm') . "' onclick='return confirm(\"".$confirm2 ."\")' />";
						} ?>
					</form>
				</td>
				<?php } ?>
			</tr>
		</table>

	<?php if ($isedit) { ?>
	<h2 id="assigned_markers">
		<?php
		if ($multi_layer_map == 0) {
			$assigned_markers_layername = sprintf(__('Markers assigned to layer "%1s" (ID %2s)','lmm'), $name, $id);
			echo $assigned_markers_layername;
		} else if ($multi_layer_map == 1) {
			$assigned_markers_layername = sprintf(__('Markers assigned to multi layer map "%1s" (ID %2s)','lmm'), $name, $id);
			echo $assigned_markers_layername;
		}
		?>
	</h2>
	<p>
		<?php _e('Total','lmm') ?>: <?php echo $markercount; ?> <?php _e('markers','lmm') ?>
	</p>
	<p> <?php
	if ($multi_layer_map == 0) {
		echo "<a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-add.png\" /></a> <a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id&Layername=" . urlencode(stripslashes($name)) . "\" style=\"text-decoration:none;\">" . __('add new marker to this layer','lmm') . "</a>";
	} ?> </p>
	<table cellspacing="0" class="wp-list-table widefat fixed">
		<thead>
			<tr>
					<!--<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>-->
					<th class="manage-column column-id" scope="col"><span>ID</span></span></th>
					<th class="manage-column column-icon" scope="col"><span><?php _e('Icon', 'lmm') ?></span></span></th>
					<th class="manage-column column-markername" scope="col"><span><?php _e('Marker name','lmm') ?></span></span></a></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) { ?>
					<th class="manage-column column-address" scope="col"><span><?php _e('Location','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext" scope="col"><span><?php _e('Popup text','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layername' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layername' ] == 1 )) { ?>
					<th class="manage-column column-layername" scope="col"><span><?php _e('Layer name','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><span><?php _e('Popup status', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><span><?php _e('Zoom', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><span><?php _e('Basemap', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby" scope="col"><span><?php _e('Created by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon" scope="col"><span><?php _e('Created on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby" scope="col"><span><?php _e('Updated by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon" scope="col"><span><?php _e('Updated on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><span><?php _e('Controlbox status','lmm') ?></span></span></th><?php } ?>
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
					<!--<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>-->
					<th class="manage-column column-id" scope="col"><span>ID</span></span></th>
					<th class="manage-column column-icon" scope="col"><span><?php _e('Icon', 'lmm') ?></span></span></th>
					<th class="manage-column column-markername" scope="col"><span><?php _e('Marker name','lmm') ?></span></span></a></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) { ?>
					<th class="manage-column column-address" scope="col"><span><?php _e('Location','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext" scope="col"><span><?php _e('Popup text','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layername' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layername' ] == 1 )) { ?>
					<th class="manage-column column-layername" scope="col"><span><?php _e('Layer name','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><span><?php _e('Popup status', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><span><?php _e('Zoom', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><span><?php _e('Basemap', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby" scope="col"><span><?php _e('Created by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon" scope="col"><span><?php _e('Created on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby" scope="col"><span><?php _e('Updated by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon" scope="col"><span><?php _e('Updated on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><span><?php _e('Controlbox status','lmm') ?></span></span></th><?php } ?>
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
$markernonce = wp_create_nonce('marker-nonce'); //info: for delete-links
	//info: delete link
	if (current_user_can( $lmm_options[ 'capabilities_delete' ])) {
	 $confirm3 = sprintf( esc_attr__('Do you really want to delete marker %1$s (ID %2$s)?','lmm'), stripslashes($row['markername']), $row['markerid']);
		$delete_link_marker = ' | </span><span class="delete"><a onclick="if ( confirm( \'' . $confirm3 . '\' ) ) { return true;}return false;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=delete&id='.$row['markerid'].'&_wpnonce=' . $markernonce . '" class="submitdelete">' . __('delete','lmm') . '</a></span>';
	} else {
		$delete_link_marker = '';
	}
  if (count($layer_marker_list_table) < 1)
    echo '<tr><td colspan="7">'.__('No marker assigned to this layer', 'lmm').'</td></tr>';
  else
    foreach ($layer_marker_list_table as $row){
	//info: set column display variables - need for for-each
	$column_layer_name = '<td class="lmm-border">' . $row['lname'] . '</td>';
	$column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border">' . stripslashes(htmlspecialchars($row['maddress'])) . '</td>' : '';
	$column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ?
'<td class="lmm-border">' . $row['mopenpopup'] . '</td>' : '';
	$column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td>Lat: ' . $row['mlat'] . '<br/>Lon: ' . $row['mlon'] . '</td>' : '';
	$column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border">' . __('Width','lmm') . ': '.$row['mmapwidth'].$row['mmapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mmapheight'].'px</td>' : '';
	$column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border">' . $row['mzoom'] . '</td>' : '';
	$column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border">'.$row['mcontrolbox'].'</td>' : '';
	$column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border"><input ' . $shortcode_select . ' style="width:206px;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['markerid'] . '&quot;]" readonly></td>' : '';
	$column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['markerid'] . '&name=' . $lmm_options[ 'misc_kml' ] . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /><br/>KML</a></td>' : '';
    $column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
    $column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo"><br/>' . __('QR code','lmm') . '</a></td>' : '';
	$column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $row['markerid'] . '&callback=jsonp&full=yes&full_icon_url=yes" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-logo"><br/>GeoJSON</a></td>' : '';
    $column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo"><br/>GeoRSS</a></td>' : '';
	$column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-logo"><br/>Wikitude</a></td>' : '';
	$column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border">' . $row['mbasemap'] . '</td>' : '';
	$column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border">' . esc_html($row['mcreatedby']) . '</td>' : '';
	$column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border">' . $row['mcreatedon'] . '</td>' : '';
	$column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border">' . esc_html($row['mupdatedby']) . '</td>' : '';
	$column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border">' . $row['mupdatedon'] . '</td>' : '';
	$openpopupstatus = ($row['mopenpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
	$popuptextabstract = (strlen($row['mpopuptext']) >= 90) ? "...": "";
	$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ?
'<td class="lmm-border"><a title="' . esc_attr__('Edit marker', 'lmm') . ' ' . $row['markerid'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'] . '" >' . mb_substr(strip_tags(stripslashes($row['mpopuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
	echo '<tr valign="middle" class="alternate" id="link-'.$row['markerid'].'">
      <td class="lmm-border">'.$row['markerid'].'</td>
      <td class="lmm-border">';
      if ($row['micon'] != null) {
         echo '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/'.$row['micon'].'" title="'.$row['micon'].'" />';
         } else {
         echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';};
      echo '</td>
      <td class="lmm-border"><strong><a title="' . esc_attr__('Edit marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><span class="edit"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$row['markerid'].'">' . __('edit','lmm') . '</a>' . $delete_link_marker . '</div></td>
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
?>
		</tbody>
	</table>
		<p> <?php
		if ($multi_layer_map == 0) {
			echo "<a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-add.png\" /></a> <a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id&Layername=" . urlencode(stripslashes($name)) . "\" style=\"text-decoration:none;\">" . __('add new marker to this layer','lmm') . "</a>";
		} ?> </p>
	<?php } //end $isedit ?>
	<!--isedit-->
</div>
<!--wrap-->
<script type="text/javascript">
/* //<![CDATA[ */
var marker,selectlayer,googleLayer_roadmap,googleLayer_satellite,googleLayer_hybrid,googleLayer_terrain,bingaerial,bingaerialwithlabels,bingroad,osm_mapnik,stamen_terrain,stamen_toner,stamen_watercolor,mapquest_osm,mapquest_aerial,mapquest_hybrid,ogdwien_basemap,ogdwien_satellite,mapbox,mapbox2,mapbox3,custom_basemap,custom_basemap2,custom_basemap3,empty_basemap,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl;
var markers = {};

(function($) {
  selectlayer = new L.Map("selectlayer", { dragging: <?php echo $lmm_options['misc_map_dragging'] ?>, touchZoom: <?php echo $lmm_options['misc_map_touchzoom'] ?>, scrollWheelZoom: <?php echo $lmm_options['misc_map_scrollwheelzoom'] ?>, doubleClickZoom: <?php echo $lmm_options['misc_map_doubleclickzoom'] ?>, boxzoom: <?php echo $lmm_options['map_interaction_options_boxzoom'] ?>, trackResize: <?php echo $lmm_options['misc_map_trackresize'] ?>, worldCopyJump: <?php echo $lmm_options['map_interaction_options_worldcopyjump'] ?>, closePopupOnClick: <?php echo $lmm_options['misc_map_closepopuponclick'] ?>, keyboard: <?php echo $lmm_options['map_keyboard_navigation_options_keyboard'] ?>, keyboardPanOffset: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) ?>, keyboardZoomOffset: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) ?>, inertia: <?php echo $lmm_options['map_panning_inertia_options_inertia'] ?>, inertiaDeceleration: <?php echo intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) ?>, inertiaMaxSpeed: <?php echo intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) ?>, zoomControl: <?php echo $lmm_options['misc_map_zoomcontrol'] ?>, crs: <?php echo $lmm_options['misc_projections'] ?> });
	<?php
		$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
		$attrib_prefix = '<a tabindex=\"115\" href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker for WordPress - helping you to share your favorite spots and tracks','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Leaflet Maps Marker') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Leaflet Maps Marker') . '\">icons</a>)';
		$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $layerviewlat . '&amp;lon=' . $layerviewlon . '&zoom=' . $layerzoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
		$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
		$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
		$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
		$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
		$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	?>
	
	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified")
	<?php $maxzoom = 21; ?> //info: customizable in MMP only
	selectlayer._layersMaxZoom = <?php echo $maxzoom; ?>;

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
	?>
	osm_mapnik = new L.TileLayer("<?php echo $osm_tile_url; ?>", {mmid: 'osm_mapnik', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo $osm_maxNativeZoom; ?>, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $osm_attribution; ?>", detectRetina: <?php echo $lmm_options['map_retina_detection']; ?>});
	stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/<?php echo $lmm_options[ 'stamen_terrain_flavor' ]; ?>/{z}/{x}/{y}.png", {mmid: 'stamen_terrain', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo $lmm_options['map_retina_detection']; ?>});
	stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/<?php echo $lmm_options[ 'stamen_toner_flavor' ]; ?>/{z}/{x}/{y}.png", {mmid: 'stamen_toner', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 20, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo $lmm_options['map_retina_detection']; ?>});
	stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {mmid: 'stamen_watercolor', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo $lmm_options['map_retina_detection']; ?>});

	<?php
	if ($lmm_options['mapquest_api_key'] != NULL) {
		echo 'if (typeof MQ !== "undefined") {';
		echo 'mapquest_osm = new MQ.mapLayer({mmid: "mapquest_osm"});';
		echo 'mapquest_aerial = new MQ.satelliteLayer({mmid: "mapquest_aerial"});';
		echo 'mapquest_hybrid = new MQ.hybridLayer({mmid: "mapquest_hybrid"});';
		echo '} else { alert("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); }';
	}?>
	
	<?php
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		echo 'googleLayer_roadmap = new L.Google("ROADMAP", {mmid: "googleLayer_roadmap", detectRetina: ' . $lmm_options['map_retina_detection'] . '});';
		echo 'googleLayer_satellite = new L.Google("SATELLITE", {mmid: "googleLayer_satellite", detectRetina: ' . $lmm_options['map_retina_detection'] . '});';
		echo 'googleLayer_hybrid = new L.Google("HYBRID", {mmid: "googleLayer_hybrid", detectRetina: ' . $lmm_options['map_retina_detection'] . '});';
		echo 'googleLayer_terrain = new L.Google("TERRAIN", {mmid: "googleLayer_terrain", detectRetina: ' . $lmm_options['map_retina_detection'] . '});';
	}?>
	
	<?php if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) { ?>
	bingaerial = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingaerial', type: 'Aerial', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	bingaerialwithlabels = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingaerialwithlabels', type: 'AerialWithLabels', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	bingroad = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingroad', type: 'Road', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	<?php }; ?>
	ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {mmid: 'ogdwien_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, attribution: "<?php echo $attrib_basemapat; ?>", subdomains: ['maps1', 'maps2', 'maps3', 'maps4'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_satellite', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, attribution: "<?php echo $attrib_basemapat; ?>", subdomains: ['maps1', 'maps2', 'maps3', 'maps4'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	//info: MapBox basemaps
	var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/<?php echo htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])); ?>.<?php echo htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])); ?>/{z}/{x}/{y}.png", {mmid: 'mapbox', minZoom: <?php echo intval($lmm_options[ 'mapbox_minzoom' ]); ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'mapbox_maxzoom' ]); ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)); ?>", subdomains: ['a','b','c','d'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/<?php echo htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])); ?>.<?php echo htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])); ?>/{z}/{x}/{y}.png", {mmid: 'mapbox2', minZoom: <?php echo intval($lmm_options[ 'mapbox2_minzoom' ]); ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'mapbox2_maxzoom' ]); ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)); ?>", subdomains: ['a','b','c','d'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/<?php echo htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])); ?>.<?php echo htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])); ?>/{z}/{x}/{y}.png", {mmid: 'mapbox3', minZoom: <?php echo intval($lmm_options[ 'mapbox3_minzoom' ]); ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'mapbox3_maxzoom' ]); ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)); ?>", subdomains: ['a','b','c','d'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	//info: check if subdomains are set for custom basemaps
	<?php
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	?>
	custom_basemap = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) ?>", {mmid: 'custom_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap_tms' ] ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	custom_basemap2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap2_tms' ] ?>, <?php echo $error_tile_url_custom_basemap2; ?>attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap2_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	custom_basemap3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap3_tms' ] ?>, <?php echo $error_tile_url_custom_basemap3; ?>attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap3_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
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
	overlays_custom = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) ?>", {olid: 'overlays_custom', tms: <?php echo $lmm_options[ 'overlays_custom_tms' ] ?>, <?php echo $error_tile_url_overlays_custom; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom_minzoom' ]) ?><?php echo $overlays_custom_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	overlays_custom2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) ?>", {olid: 'overlays_custom2', tms: <?php echo $lmm_options[ 'overlays_custom2_tms' ] ?>, <?php echo $error_tile_url_overlays_custom2; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom2_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom2_minzoom' ]) ?><?php echo $overlays_custom2_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	overlays_custom3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) ?>", {olid: 'overlays_custom3', tms: <?php echo $lmm_options[ 'overlays_custom3_tms' ] ?>, <?php echo $error_tile_url_overlays_custom3; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom3_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom3_minzoom' ]) ?><?php echo $overlays_custom3_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	overlays_custom4 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) ?>", {olid: 'overlays_custom4', tms: <?php echo $lmm_options[ 'overlays_custom4_tms' ] ?>, <?php echo $error_tile_url_overlays_custom4; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom4_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom4_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom4_minzoom' ]) ?><?php echo $overlays_custom4_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
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
	wms = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) ?>", {wmsid: 'wms', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ]))?>', attribution: '<?php echo $wms_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ]))?>'<?php echo $wms_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms2 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) ?>", {wmsid: 'wms2', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ]))?>', attribution: '<?php echo $wms2_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms2_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ]))?>'<?php echo $wms2_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms3 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) ?>", {wmsid: 'wms3', layers: '<?php echo htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ])))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ]))?>', attribution: '<?php echo $wms3_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms3_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ]))?>'<?php echo $wms3_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms4 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) ?>", {wmsid: 'wms4', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ]))?>', attribution: '<?php echo $wms4_attribution ?>', transparent: '<?php echo $lmm_options[ 'wms_wms4_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ]))?>'<?php echo $wms4_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms5 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) ?>", {wmsid: 'wms5', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ]))?>', attribution: '<?php echo $wms5_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms5_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ]))?>'<?php echo $wms5_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms6 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) ?>", {wmsid: 'wms6', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ]))?>', attribution: '<?php echo $wms6_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms6_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ]))?>'<?php echo $wms6_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms7 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) ?>", {wmsid: 'wms7', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ]))?>', attribution: '<?php echo $wms7_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms7_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ]))?>'<?php echo $wms7_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms8 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) ?>", {wmsid: 'wms8', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ]))?>', attribution: '<?php echo $wms8_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms8_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ]))?>'<?php echo $wms8_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms9 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) ?>", {wmsid: 'wms9', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ]))?>', attribution: '<?php echo $wms9_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms9_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ]))?>'<?php echo $wms9_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	wms10 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) ?>", {wmsid: 'wms10', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ]))?>', attribution: '<?php echo $wms10_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms10_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ]))?>'<?php echo $wms10_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});

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
		echo substr($basemaps_available, 0, -1);
	?>
	},

	//info: controlbox - add available overlays
	{
	<?php
		$overlays_custom_available = '';
		if ( ((isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 )) || ($loverlays_custom == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom_name' ]))."': overlays_custom,";
		if ( ((isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 )) || ($loverlays_custom2 == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom2_name' ]))."': overlays_custom2,";
		if ( ((isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 )) || ($loverlays_custom3 == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom3_name' ]))."': overlays_custom3,";
		if ( ((isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 )) || ($loverlays_custom4 == 1) )
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
  selectlayer.addLayer(<?php echo $basemap ?>)
	//info: controlbox - check active overlays on layer level
	<?php
		if ( (isset($loverlays_custom) == TRUE) && ($loverlays_custom == 1) )
			echo ".addLayer(overlays_custom)";
		if ( (isset($loverlays_custom2) == TRUE) && ($loverlays_custom2 == 1) )
			echo ".addLayer(overlays_custom2)";
		if ( (isset($loverlays_custom3) == TRUE) && ($loverlays_custom3 == 1) )
			echo ".addLayer(overlays_custom3)";
		if ( (isset($loverlays_custom4) == TRUE) && ($loverlays_custom4 == 1) )
			echo ".addLayer(overlays_custom4)";
	?>
	//info: controlbox - add active overlays on layer level
	<?php
		if ( $wms == 1 )
			echo ".addLayer(wms)";
		if ( $wms2 == 1 )
			echo ".addLayer(wms2)";
		if ( $wms3 == 1 )
			echo ".addLayer(wms3)";
		if ( $wms4 == 1 )
			echo ".addLayer(wms4)";
		if ( $wms5 == 1 )
			echo ".addLayer(wms5)";
		if ( $wms6 == 1 )
			echo ".addLayer(wms6)";
		if ( $wms7 == 1 )
			echo ".addLayer(wms7)";
		if ( $wms8 == 1 )
			echo ".addLayer(wms8)";
		if ( $wms9 == 1 )
			echo ".addLayer(wms9)";
		if ( $wms10 == 1 )
			echo ".addLayer(wms10)";
	?>

  .addControl(layersControl);
  //info: add scale control
  <?php if ( $lmm_options['map_scale_control'] == 'enabled' ) { ?>
  L.control.scale({position:'<?php echo $lmm_options['map_scale_control_position'] ?>', maxWidth: <?php echo intval($lmm_options['map_scale_control_maxwidth']) ?>, metric: <?php echo $lmm_options['map_scale_control_metric'] ?>, imperial: <?php echo $lmm_options['map_scale_control_imperial'] ?>, updateWhenIdle: <?php echo $lmm_options['map_scale_control_updatewhenidle'] ?>}).addTo(selectlayer);
  <?php }; ?>
  mapcentermarker = new L.Marker(new L.LatLng(<?php echo $layerviewlat . ', ' . $layerviewlon; ?>),{ title: '<?php esc_attr_e('use this pin to center the layer (will only be shown in the admin area)','lmm'); ?>', clickable: true, draggable: true, zIndexOffset: 1000, opacity: 0.6 });
  mapcentermarker.options.icon = new L.Icon({iconUrl:'<?php echo LEAFLET_PLUGIN_URL . 'inc/img/icon-layer-center.png' ?>',iconSize: [32, 37],iconAnchor: [17, 37],shadowUrl: ''});
  mapcentermarker.addTo(selectlayer);
  var layers = {};
  var geojsonObj, mapIcon, marker_clickable, marker_title;

  <?php
  if ($id != NULL) { //info: dont load geojson.php on new layer maps to save mysql queries+http requests
	  if ($multi_layer_map == 0) { $id_for_geojson_url = $id; } else { $id_for_geojson_url = $multi_layer_map_list; }
		echo 'var xhReq = new XMLHttpRequest();'.PHP_EOL;
		echo 'xhReq.open("GET", "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id_for_geojson_url . '", true);'.PHP_EOL;
		echo 'xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) {'.PHP_EOL; ?> //info: async 1/2
		//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
			if (xhReq.responseText.indexOf('{"type"') != 0) {
					var position = xhReq.responseText.indexOf('{"type"');
					try {
						geojsonObj = JSON.parse(xhReq.responseText.slice(position));
					} catch (e) {
								console.log("Error - invalid GeoJSON object: "+e.message);
					}
			  } else {
					try {
						geojsonObj = JSON.parse(xhReq.responseText);
					} catch (e) {
								console.log("Error - invalid GeoJSON object: "+e.message);
					}
			}

			L.geoJson(geojsonObj, {
				onEachFeature: function(feature, marker) {
					if (feature.properties.text != '') {
							marker.bindPopup(feature.properties.text, {
							maxWidth: <?php echo intval($lmm_options['defaults_marker_popups_maxwidth']) ?>,
							minWidth: <?php echo intval($lmm_options['defaults_marker_popups_minwidth']) ?>,
							maxHeight: <?php echo intval($lmm_options['defaults_marker_popups_maxheight']) ?>,
							autoPan: <?php echo $lmm_options['defaults_marker_popups_autopan'] ?>,
							closeButton: <?php echo $lmm_options['defaults_marker_popups_closebutton'] ?>,
							autoPanPadding: new L.Point(<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']) ?>, <?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']) ?>)
						});
					}
				},
				pointToLayer: function (feature, latlng) {
					mapIcon = L.icon({
						iconUrl: (feature.properties.icon != '') ? "<?php echo LEAFLET_PLUGIN_ICONS_URL ?>/" + feature.properties.icon : "<?php echo LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' ?>",
						iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],
						iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],
						popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],
						shadowUrl: '<?php echo $marker_shadow_url; ?>',
						shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],
						shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],
						className: (feature.properties.icon == '') ? "lmm_marker_icon_default" : "lmm_marker_icon_"+ feature.properties.icon.slice(0,-4)
					});
					if (feature.properties.text == '') { marker_clickable = false } else { marker_clickable = true };
					<?php if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') { ?>
						if (feature.properties.markername == '') { marker_title = '' } else { marker_title = feature.properties.markername };
					<?php }; ?>
					return L.marker(latlng, {icon: mapIcon, clickable: marker_clickable, title: marker_title, opacity: <?php echo floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) ?>});
				}
			}).addTo(selectlayer);
		} else { if (window.console) { console.error(xhReq.statusText); } } } }; xhReq.onerror = function (e) { if (window.console) { console.error(xhReq.statusText); } }; xhReq.send(null); //info: async 2/2
  <?php } //info: end if ($id != NULL) ?>

  <?php
  //info: set controlbox visibility 2/2
  if ($lcontrolbox == '0') {
	echo "$('.leaflet-control-layers').hide();";
  } else if ($lcontrolbox == '2') {
	echo "layersControl._expand();";
  }?>
  //info: load wms layer when checkbox gets checked
	$('#advanced-settings input:checkbox').click(function(el) {
		if(el.target.checked) {
			selectlayer.addLayer(window[el.target.id]);
		} else {
			selectlayer.removeLayer(window[el.target.id]);
		}

	});

  //info: update basemap when chosing from control box
  selectlayer.on('layeradd', function(e) {
		if(e.layer.options.mmid) {
			selectlayer.attributionControl._attributions = [];
			$('#basemap').val(e.layer.options.mmid);
  		}
  });
  //info: when custom overlay gets checked from control box update hidden field
  selectlayer.on('layeradd', function(e) {
		if(e.layer.options.olid) {
			$('#'+e.layer.options.olid).attr('value', '1');
		}
  });
  //info: when custom overlay gets unchecked from control box update hidden field
  selectlayer.on('layerremove', function(e) {
		if(e.layer.options.olid) {
			$('#'+e.layer.options.olid).attr('value', '0');
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
	  var newlat = newlocation['lat'];
  	  var newlon = newlocation['lng'];
	  document.getElementById('layerviewlat').value = newlat.toFixed(6);
	  document.getElementById('layerviewlon').value = newlon.toFixed(6);
	  selectlayer.setView(newlocation,selectlayer.getZoom());
  });
  var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), layerviewlat = $('#layerviewlat'), layerviewlon = $('#layerviewlon'), panel = $('#lmm-panel'), lmm = $('#lmm'), layername = $('#layername'), listmarkers = $('#lmm-listmarkers'), listmarkers_table = $('#lmm-listmarkers-table'), multi_layer_map = $('#lmm-multi_layer_map'), zoom = $('#layerzoom');
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
	//info: show/hide panel for markername & API URLs
	$('input:checkbox[name=panel]').click(function() {
		if($('input:checkbox[name=panel]').is(':checked')) {
			panel.css("display",'block');
		} else {
			panel.css("display",'none');
		}
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
		} else {
			multi_layer_map.css("display",'none');
		}
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
			$('.leaflet-control-layers').hide();
		}
		if($('input:radio[name=controlbox]:checked').val() == 1) {
			$('.leaflet-control-layers').show();
			layersControl._collapse();
		}
		if($('input:radio[name=controlbox]:checked').val() == 2) {
			$('.leaflet-control-layers').show();
			layersControl._expand();
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
	$(":input, textarea").change(function(){
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
	
	//info: toggle advanced layer buttons
	jQuery('.show-advanced-layer-edit-buttons, .hide-advanced-layer-edit-buttons').click(function(e) {
		jQuery('.show-advanced-layer-edit-buttons').toggle();
		jQuery('.hide-advanced-layer-edit-buttons').toggle();
		jQuery('.advanced-layer-edit-button').toggle();
	});
	//info: select layername, address, mapwith, mapheight & zoom input field on focus
	$("#layername, #address, #mapwidth, #mapheight, #layerzoom").focus(function() {
		$(this).select();
	});
	//info: fix for autofocus in Chrome
	$(document).ready(function(){
		$('input[autofocus="autofocus"]').focus();
	});
})(jQuery)

<?php
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
				jQuery("#google-api-error-admin-header").append(\'<hr noshade size="1"/><strong>' . __('Background','lmm') . '</strong>: ' . sprintf(__( 'Since June 22nd 2016 <a href="%1$s" target="_blank">Google requires a Google Maps API key</a> when using any Google Map service on your website.','lmm'), 'https://googlegeodevelopers.blogspot.co.at/2016/06/building-for-scale-updates-to-google.html') . ' ' . sprintf(__('Your personal API key can be obtained from the <a href="%1$s" target="_blank">Google API Console</a>.', 'lmm'), 'https://console.developers.google.com/apis/') . '<br/>' . sprintf(__('For a tutorial including screenshots on how to register a Google Maps JavaScript API key <a href="%1$s" target="_blank">please click here</a>.', 'lmm'), 'https://www.mapsmarker.com/google-maps-javascript-api') . '<br/>\');
				jQuery("#google-api-error-admin-header").append(\'<hr noshade size="1"/><strong>' . __('Solution','lmm') . '</strong>: ' . sprintf(__('please add or verify your Google Maps API key at <a href="%1$s">Settings / Basemaps / Google Maps JavaScript API</a>','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-google_js_api') . '\');
				jQuery("#google-api-error-admin-header").css("display", "block");
			}
		}
	}';
}
?>
/* //]]> */
</script>
<!--default texts for geocoding.js-->
<input type="hidden" id="defaults_texts_add_new_marker_here" value="<?php echo __('add new marker here','lmm'); ?>" />
<span style="display:none;" id="defaults_texts_geocoding_mapzen_api_key_needed"><?php echo sprintf(__('Fallback geocoding provider was activated as Mapzen Search requires an API key since 04/2017.<br/>We recommend <a href="%1$s" target="_blank">registering a free Mapzen Search API key</a> which allows up to %2$s requests/%3$s and a maximum of %4$s requests/%5$s.','lmm'), 'https://www.mapsmarker.com/mapzen-search#tutorial', '30.000', __('day','lmm'), '6', __('second','lmm')); ?></span>
<span style="display:none;" id="defaults_texts_mapquest_key_issue"><?php echo sprintf(__('MapQuest Geocoding error - please contact support at <a href="%1$s">support@mapquest.com</a>'), 'mailto:support@mapquest.com?subject=Issue with API key ' . esc_js(trim($lmm_options['geocoding_mapquest_geocoding_api_key']))); ?></span>
<input type="hidden" id="defaults_texts_geocoding_fallback_info" value="<?php echo __('Automatically switched to fallback provider','lmm'); ?>" />
<input type="hidden" id="defaults_texts_geocoding_results_header" value="<?php echo __('To select a location, please click on a result or press','lmm'); ?>" />
<span style="display:none;" id="defaults_texts_geocoding_footer_tips"><div id="geocoding-footer-tips" style="float:left;margin:4px 0 0 0;"><a href="https://www.mapsmarker.com/geocoding-optimization" target="_blank" style="text-decoration:underline;" title="<?php echo esc_attr__('show tutorial at mapsmarker.com','lmm'); ?>"><?php echo __('Tip: adjust geocoding settings for more targeted search results','lmm'); ?></a></div></span>
<?php //info: check if layer exists - part 2
} ?>
<?php }
include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');
?>