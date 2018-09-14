<?php
/*
    Edit marker - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-marker.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php'); 
require_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php' );
WP_Filesystem();
global $wpdb, $allowedtags, $wp_version, $locale, $wp_filesystem, $allowedposttags;
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
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$addtoLayer = isset($_GET['addtoLayer']) ? intval($_GET['addtoLayer']) : (isset($_POST['layer']) ? intval($_POST['layer']) : '');
$layername = isset($_GET['Layername']) ? stripslashes($_GET['Layername']) : '';
$layer_show_button = ($addtoLayer != NULL && $addtoLayer != 0) ? "<a class='button-secondary lmm-nav-secondary' href=" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $addtoLayer .">" . __('edit assigned layer','lmm') . "</a>&nbsp;&nbsp;&nbsp;" : "";
$oid = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : '');
$lat_check = isset($_POST['lat']) ? $_POST['lat'] : (isset($_GET['lat']) ? $_GET['lat'] : '');
$lon_check = isset($_POST['lon']) ? $_POST['lon'] : (isset($_GET['lon']) ? $_GET['lon'] : '');
$markerid = isset($_GET['markerid']) ? intval($_GET['markerid']) : ''; //info: for switcheditor-js-forward
//info: for datetime check in Firefox
function lmm_validateDate($date) {
	return (bool)strtotime($date);
}

if (!empty($action)) {
$markernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
if (! wp_verify_nonce($markernonce, 'marker-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
  $layer = isset($_POST['layer']) ? intval($_POST['layer']) : 0;
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
		$openpopup_checkbox = isset($_POST['openpopup']) ? '1' : '0';
		$panel_checkbox = isset($_POST['panel']) ? '1' : '0';
		$markername_quotes = str_replace("\\\\","/", str_replace("\"","'", sanitize_text_field($_POST['markername']))); //info: geojson validity fixes
		$popuptext = preg_replace("/\t/", " ", str_replace("\\\\","/", $_POST['popuptext'])); //info: geojson validity fixes
		$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", sanitize_text_field($_POST['address']))); //info: geojson validity fixes
		$gpx_url = ''; //info: added for compat
		$gpx_panel_checkbox = '0'; //info: added for compat
		if ($_POST['kml_timestamp'] == NULL) {
			$result = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), $_POST['icon_hidden'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $address, $gpx_url, $gpx_panel_checkbox );
		} else if ($_POST['kml_timestamp'] != NULL) {
			//info: for datetime check in Firefox
			$kml_timestamp = (lmm_validateDate($_POST['kml_timestamp']) == TRUE) ? $_POST['kml_timestamp'] : current_time('mysql',0);
			$result = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), $_POST['icon_hidden'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $kml_timestamp, $address, $gpx_url, $gpx_panel_checkbox );
		}
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $wpdb->insert_id . '&status=published"; </script> ';
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
		$openpopup_checkbox = isset($_POST['openpopup']) ? '1' : '0';
		$panel_checkbox = isset($_POST['panel']) ? '1' : '0';
		$markername_quotes = str_replace("\\\\","/", str_replace("\"","'", $_POST['markername'])); //info: geojson validity fixes
		$popuptext = preg_replace("/\t/", " ", str_replace("\\\\","/", $_POST['popuptext'])); //info: geojson validity fixes
		$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", $_POST['address'])); //info: geojson validity fixes
		$gpx_url = ''; //info: added for compat
		$gpx_panel_checkbox = '0'; //info: added for compat
		if ($_POST['kml_timestamp'] == NULL) {
			$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `markername` = %s, `basemap` = %s, `layer` = %d, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), $_POST['icon_hidden'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $address, $gpx_url, $gpx_panel_checkbox, $oid );
		} else if ($_POST['kml_timestamp'] != NULL) {
			//info: for datetime check in Firefox
			$kml_timestamp = (lmm_validateDate($_POST['kml_timestamp']) == TRUE) ? $_POST['kml_timestamp'] : current_time('mysql',0);
			$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `markername` = %s, `basemap` = %s, `layer` = %d, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `kml_timestamp` = %s, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), $_POST['icon_hidden'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $kml_timestamp, $address, $gpx_url, $gpx_panel_checkbox, $oid );
		}
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . intval($_POST['id']) . '&status=updated"; </script> ';
	    }
		else
		{
		echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
    	}
  }
  elseif ($action == 'delete') {
    if (!empty($oid)) {
	$result = $wpdb->prepare( "DELETE FROM `$table_name_markers` WHERE `id` = %d", $oid );
	$wpdb->query( $result );
	$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
        echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully deleted','lmm') . '</div><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('list all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
    }
  }
  elseif ($action == 'switcheditor') {
	if ($new_editor == 'advanced') {
		update_option( 'leafletmapsmarker_editor', $new_editor );
		if ( $markerid != NULL ) {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $markerid . '&status=advanced"; </script> ';
		} else {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&status=advanced"; </script> ';
		}
	} else if ($new_editor == 'simplified') {
		update_option( 'leafletmapsmarker_editor', $new_editor );
		if ( $markerid != NULL ) {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $markerid . '&status=simplified"; </script> ';
		} else {
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&status=simplified"; </script> ';
		}
	}
  }
} else {
  //info: get icons list
  $iconlist = array();
  $dir = opendir(LEAFLET_PLUGIN_ICONS_DIR);
  while ($file = readdir($dir)) {
	if ($file === false) {
		break;
	}
	if ($file != "." and $file != "..") {
		if (!is_dir($dir.$file) && ((substr($file, count($file)-5, 4) == '.png') || (substr($file, count($file)-5, 4) == '.jpg') || (substr($file, count($file)-5, 4) == '.gif'))) {
			$iconlist[] = $file;
		}
	}
  }
  closedir($dir);
  sort($iconlist);
  global $current_user;
  wp_get_current_user();
  //info: get layers list
  $layerlist = $wpdb->get_results('SELECT * FROM `'.$table_name_layers.'` WHERE `id` > 0', ARRAY_A);
  $id = '';
  $markername = '';
  $basemap = $lmm_options[ 'standard_basemap' ];
  //info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
  if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
  } else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
  }
  $layer = ($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '' : intval($lmm_options[ 'defaults_marker_default_layer' ]);
  $lat = str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lat' ]));
  $lon = str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lon' ]));
  $icon = ($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : esc_html($lmm_options[ 'defaults_marker_icon' ]);
  $popuptext = '';
  $zoom = intval($lmm_options[ 'defaults_marker_zoom' ]);
  $openpopup = $lmm_options[ 'defaults_marker_openpopup' ];
  $mapwidth = intval($lmm_options[ 'defaults_marker_mapwidth' ]);
  $mapwidthunit = $lmm_options[ 'defaults_marker_mapwidthunit' ];
  $mapheight = intval($lmm_options[ 'defaults_marker_mapheight' ]);
  $panel = $lmm_options[ 'defaults_marker_panel' ];
  $mcreatedby = '';
  $mcreatedon = '';
  $mupdatedby = '';
  $mupdatedon = '';
  $controlbox = $lmm_options[ 'defaults_marker_controlbox' ];
  $overlays_custom = ( (isset($lmm_options[ 'defaults_marker_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
  $overlays_custom2 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
  $overlays_custom3 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
  $overlays_custom4 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
  $wms = ( (isset($lmm_options[ 'defaults_marker_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms_active' ] == 1 ) ) ? '1' : '0';
  $wms2 = ( (isset($lmm_options[ 'defaults_marker_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms2_active' ] == 1 ) ) ? '1' : '0';
  $wms3 = ( (isset($lmm_options[ 'defaults_marker_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms3_active' ] == 1 ) ) ? '1' : '0';
  $wms4 = ( (isset($lmm_options[ 'defaults_marker_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms4_active' ] == 1 ) ) ? '1' : '0';
  $wms5 = ( (isset($lmm_options[ 'defaults_marker_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms5_active' ] == 1 ) ) ? '1' : '0';
  $wms6 = ( (isset($lmm_options[ 'defaults_marker_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms6_active' ] == 1 ) ) ? '1' : '0';
  $wms7 = ( (isset($lmm_options[ 'defaults_marker_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms7_active' ] == 1 ) ) ? '1' : '0';
  $wms8 = ( (isset($lmm_options[ 'defaults_marker_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms8_active' ] == 1 ) ) ? '1' : '0';
  $wms9 = ( (isset($lmm_options[ 'defaults_marker_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms9_active' ] == 1 ) ) ? '1' : '0';
  $wms10 = ( (isset($lmm_options[ 'defaults_marker_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms10_active' ] == 1 ) ) ? '1' : '0';
  $kml_timestamp = '';
  $address = '';

  $isedit = isset($_GET['id']);
  if ($isedit) {
    $id = intval($_GET['id']);
    $row = $wpdb->get_row('SELECT `markername`,`basemap`,`layer`,`lat`,`lon`,`icon`,`popuptext`,`zoom`,`openpopup`,`mapwidth`,`mapwidthunit`,`mapheight`,`panel`,`createdby`,`createdon`,`updatedby`,`updatedon`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`kml_timestamp`,`address` FROM `'.$table_name_markers.'` WHERE `id`='.$id, ARRAY_A);
    $markername = esc_js(htmlspecialchars($row['markername']));
    $basemap = $row['basemap'];
    //info: fallback for existing maps if MapQuest API key is not set
    if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
	   $basemap = 'osm_mapnik';
    }
    $layer = $row['layer'];
    $lat = $row['lat'];
    $lon = $row['lon'];
    $icon = $row['icon'];
    $popuptext = $row['popuptext'];
    $zoom = $row['zoom'];
    $openpopup = $row['openpopup'];
    $mapwidth = $row['mapwidth'];
    $mapwidthunit = $row['mapwidthunit'];
    $mapheight = $row['mapheight'];
    $panel = $row['panel'];
    $mcreatedby = esc_html($row['createdby']);
    $mcreatedon = $row['createdon'];
    $mupdatedby = esc_html($row['updatedby']);
    $mupdatedon = $row['updatedon'];
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
	//info: for datetime check in Firefox
	if ($row['kml_timestamp'] == NULL) {
		$kml_timestamp = '';
	} else {
		$kml_timestamp = date('Y-m-d\TH:i:s', strtotime($row['kml_timestamp']));
	}
    $address = htmlspecialchars($row['address']);
  }
?>
<?php //info: check if marker exists - part 1
if ($lat === NULL) {
$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), htmlspecialchars($_GET['id']));
echo '<p><div class="error" style="padding:10px;">' . $error_marker_not_exists . '</div></p>';
echo '<p><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('list all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
} else { ?>

<?php
$edit_status = isset($_GET['status']) ? $_GET['status'] : '';
if ( $edit_status == 'updated') {
	echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully updated','lmm') . '</div>';
} else if ( $edit_status == 'published') {
	echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully published','lmm') . '</div>';
} else if ( $edit_status == 'simplified') {
	echo '<p><div class="updated" style="padding:10px;">' . __('You successfully switched to the simplified editor.','lmm') . '</div>';
} else if ( $edit_status == 'advanced') {
	echo '<p><div class="updated" style="padding:10px;">' . __('You successfully switched to the advanced editor.','lmm') . '</div>';
} ?>

	<?php $nonce= wp_create_nonce('marker-nonce'); ?>
	<form id="marker-add-edit" method="post">
		<?php wp_nonce_field('marker-nonce'); ?>
		<input type="hidden" id="id" name="id" value="<?php echo $id ?>" />
		<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
		<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
		<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $overlays_custom ?>" />
		<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $overlays_custom2 ?>" />
		<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $overlays_custom3 ?>" />
		<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $overlays_custom4 ?>" />
		<input id="icon_hidden" type="hidden" name="icon_hidden" value="<?php echo $icon; ?>" /> <!-- //info: IE11 fix -->
		<?php
		$noncelink = wp_create_nonce('marker-nonce');
		if ($current_editor == 'simplified') {
			echo '<div id="editmodeswitch" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<div style="float:right;"><a style="text-decoration:none;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=switcheditor&new_editor=advanced&_wpnonce=' . $noncelink . '&markerid=' . $id . '" onclick="return confirm(\'' . esc_attr__('Please note that unsaved input will not be passed to the new editor! Please click "OK" to switch the editor anyway or "Cancel" to go back and save first.','lmm') . '\')"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="Editor-Switch-Icon" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
		} else if ($current_editor == 'advanced') {
			echo '<div id="editmodeswitch" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<div style="float:right;"><a style="text-decoration:none;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=switcheditor&new_editor=simplified&_wpnonce=' . $noncelink . '&markerid=' . $id . '" onclick="return confirm(\'' . esc_attr__('Please note that unsaved input will not be passed to the new editor! Please click "OK" to switch the editor anyway or "Cancel" to go back and save first.','lmm') . '\')"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="Editor-Switch-Icon" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
		}
		?>
        <h1 style="margin:10px 0 10px 0;"><?php ($isedit === true) ? _e('Edit marker','lmm') : _e('Add new marker','lmm') ?>
		<?php echo ($isedit === true) ? ' "' . stripslashes($markername) . '" (ID '.$id.')' : '' ?>
		<input id="submit_top" disabled style="font-weight:bold;margin-left:10px;" type="submit" name="marker" class="submit button-primary" value="<?php ($isedit === true) ? _e('update','lmm') : _e('publish','lmm') ?>" />
		</h1>
		<p style="margin-top:0px;">
		<?php echo sprintf(__('Add a new marker to display a single location. To display multiple locations at the same time, <a href="%1$s">create a layer</a> first (e.g. "company locations") and then add markers (e.g. "headquarters", "store A", "store B") assigned to that layer.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer'); ?>
		</p>
		<table class="widefat">
			<?php if ($isedit === true) { ?>
			<tr>
				<td style="width:230px;" class="lmm-border"><label for="shortcode"><strong><?php _e('Shortcode and API links','lmm') ?></strong></label></td>
				<td class="lmm-border"><input id="shortcode" style="width:206px;background:#f3efef;" type="text" value="[<?php echo htmlspecialchars($lmm_options[ 'shortcode' ]); ?> marker=&quot;<?php echo $id?>&quot;]" <?php echo $shortcode_select; ?>>
				<?php
					if ($current_editor == 'simplified') {
						echo '<div id="apilinkstext" style="display:inline;"><a tabindex="123" style="cursor:pointer;">' . __('show API links','lmm') . '</a></div>';
						echo '<span id="apilinks" style="display:none;">';
					}
				?>
				<a tabindex="125" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '' ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a tabindex="126" href="https://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="127" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> <?php _e('Fullscreen','lmm'); ?></a> <span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="128" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> <?php _e('QR code','lmm'); ?></a> <span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="129" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp&full=yes&full_icon_url=yes' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a tabindex="130" href="https://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="131" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-georss.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoRSS</a> <a tabindex="132" href="https://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="133" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a tabindex="134" href="https://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="134" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_apis"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-page.png" width="16" height="16" alt="Mapsmarker-Logo" /> Maps Marker API</a>
				<?php
					if ($current_editor == 'simplified') {
						echo '</span>';
					}
				?>
				<br><small><?php _e('Use this shortcode in posts or pages on your website or one of the API links for embedding in external websites or apps','lmm') ?></small>
					</td>
			</tr>
			<?php } ?>
			<?php if ($isedit === true) { $used_in_content_visibility = 'table-row'; } else { $used_in_content_visibility = 'none'; }?>
            <tr style="display:<?php echo $used_in_content_visibility; ?>;">
	            <td style="width:230px;" class="lmm-border"><strong><?php _e('Used in content','lmm') ?></strong></td>
    	        <td class="lmm-border"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a></td>
            </tr>
            <tr>
				<td style="width:230px;" class="lmm-border"><label for="markername"><strong><?php _e('Marker name','lmm') ?></strong></label></td>
				<td class="lmm-border"><input autofocus="autofocus" style="width:640px;" type="text" id="markername" name="markername" value="<?php echo stripslashes($markername) ?>" /> (<a target="_blank" href="https://www.mapsmarker.com/multilingual" target="_blank"><?php _e('translate', 'lmm'); ?></a> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/pro-feature-banner-small.png" width="68" height="9" border="0">)</td>
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
		
					} else if ($lmm_options['geocoding_provider'] == 'mapquest-geocoding') {
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
									if ($isedit) {
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
									if ($isedit) {
										echo "var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}else{
										echo "var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
									}
									echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['algolia'].'\');';
									echo 'algolia_places.init();
								} else if (new_geocoding_provider == "photon") {
									jQuery("#geocoding-provider-status").html("&nbsp;");
									jQuery("head").append("<link rel=\"preconnect\" href=\"https://photon.mapsmarker.com\" crossorigin />");
									jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-photon");';
									if ($isedit) {
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
									if ($isedit) {
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
									if ($isedit) {
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
					} else if (($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL)  && ($lmm_options['geocoding_provider'] == 'mapquest-geocoding')) {
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
					//info: needed in free version only for geocoding.js
					if ($address == NULL) {
						echo '<span id="popup-address" style="display:none;">' . esc_attr__('if set, address will be displayed here','lmm') . '</span>';
					} else {
						echo '<span id="popup-address" style="display:none;">' . stripslashes(htmlspecialchars($address)) . '</span>';
					}
					?>
                     (<a target="_blank" href="https://www.mapsmarker.com/multilingual" target="_blank"><?php _e('translate', 'lmm'); ?></a> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/pro-feature-banner-small.png" width="68" height="9" border="0">)
					<div id="toggle-coordinates" style="clear:both;margin-top:5px;<?php echo $current_editor_css; ?>">
					<?php echo __('or paste coordinates here','lmm') . ' - '; ?>
					<?php _e('latitude','lmm') ?>: <input style="width: 100px;" type="text" id="lat" name="lat" value="<?php echo $lat; ?>" />
					<?php _e('longitude','lmm') ?>: <input style="width: 100px;" type="text" id="lon" name="lon" value="<?php echo $lon; ?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="lmm-border"><p style="margin-bottom:0px;"><strong><?php _e('Map size','lmm') ?></strong><br/>
					<label for="mapwidth"><?php _e('Width','lmm') ?>:</label>
					<input size="3" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo $mapwidth ?>" style="margin-left:5px;" />
					<input id="mapwidthunit_px" type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>><label for="mapwidthunit_px" title="<?php esc_attr_e('pixel','lmm'); ?>">px</label>&nbsp;&nbsp;&nbsp;
					<input id="mapwidthunit_percent" type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>><label for="mapwidthunit_percent">%</label><br/>
					<label for="mapheight"><?php _e('Height','lmm') ?>:</label>
					<input size="3" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" /> <span title="<?php esc_attr_e('pixel','lmm'); ?>">px</span>

					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

					<label for="zoom"><strong><?php _e('Zoom','lmm') ?></strong> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('You can also change zoom level by clicking on + or - on preview map or using your mouse wheel'); ?>" width="12" height="12" border="0"/></label>&nbsp;<input style="width: 40px;" type="text" id="zoom" name="zoom" value="<?php echo $zoom ?>" />
					<?php __('You can also change zoom level by clicking on + or - on preview map or using your mouse wheel','lmm');
					echo ' <span style="' . $current_editor_css . '"><br/><small>' . __('Global maximum zoom level','lmm') . ': <a title="' . esc_attr__('If the native maximum zoom level of a basemap is lower, tiles will be upscaled automatically.','lmm') . '" tabindex="111" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('pro version only','lmm') . '</a></small></span>';
					?>

					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

					<label for="layer"><strong><?php _e('Layer','lmm') ?></strong></label>
					<small><?php echo '<br/><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('Upgrade to pro for assigning markers to multiple layers','lmm') . '</a><br/>' ?></small>
					<select id="layer" name="layer">
						<option value="0">
						<?php _e('not assigned to a layer','lmm') ?>
						</option>
						<?php
						foreach ($layerlist as $row) {
							$layername_abstract = (strlen($row['name']) >= 25) ? '...': '';
							if ($row['multi_layer_map'] == 0) {
								echo '<option value="' . $row['id'] . '"' . ( (($row['id'] == $layer) || ($row['id'] == $addtoLayer)) ? ' selected="selected"' : '') . ' title="' . stripslashes(htmlspecialchars($row['name'])) . '">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 25) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
							} else {
								echo '<option title="' . stripslashes(htmlspecialchars($row['name'])) . ' (' . esc_attr__('This is a multi-layer map - markers cannot be assigned to this layer directly','lmm') . ')" value="' . $row['id'] . '"' . ( (($row['id'] == $layer) || ($row['id'] == $addtoLayer)) ? ' selected="selected"' : '') . ' disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 25) . $layername_abstract . ' (ID ' . $row['id'] . '/MLM)</option>';
							}
						}
						?>
					</select>
					<br>
					<small><?php echo $layereditlink = ($layer != 0) ? "<a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=".$layer."\">" . __('edit layer','lmm') . " (ID ".$layer.")</a> " . __('or','lmm') . "" : "" ?> <a tabindex="121" href="<?php LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer">
					<?php _e('add new layer','lmm') ?>
					</a>
					<br/>
					<a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php echo esc_attr__(' Feature available in pro version only','lmm'); ?>"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-eye-show.png" width="16" height="16" alt="preview icon" style="float:left;margin:2px 3px 0 0;" /> <?php _e('preview all markers from assigned layer(s)','lmm'); ?></a>
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
					<strong><?php _e('Controlbox for basemaps/overlays','lmm') ?>:</strong></label><br/>
					<input style="margin-top:1px;" id="controlbox_hidden" type="radio" name="controlbox" value="0" <?php checked($controlbox, 0); ?>><label for="controlbox_hidden"><?php _e('hidden','lmm') ?></label><br/>
					<input style="margin-top:1px;" id="controlbox_collapsed" type="radio" name="controlbox" value="1" <?php checked($controlbox, 1); ?>><label for="controlbox_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
					<input style="margin-top:1px;" id="controlbox_expanded" type="radio" name="controlbox" value="2" <?php checked($controlbox, 2); ?>><label for="controlbox_expanded"><?php _e('expanded','lmm') ?></label>

					<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

					<strong><label for="panel"><?php _e('Display panel','lmm') ?></label></strong>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="panel" id="panel" <?php checked($panel, 1 ); ?>><br/>
					<small><?php _e('If checked, panel on top of map is displayed','lmm') ?></small>
					</p>
					</div>
				</td>
				<td style="padding-bottom:5px;" class="lmm-border">
					<?php
					echo '<div id="lmm" class="lmm-rtl" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
					//info: panel for marker name and API URLs
					$panel_state = ($panel == 1) ? 'block' : 'none';
					echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . ';">'.PHP_EOL;
					echo '<div class="lmm-panel-api">';
						if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
								//info: Google language localization (directions)
								if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
									$google_language = '';
								} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
									if ($locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
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
									echo '<a tabindex="105" href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
								} else if ($lmm_options['directions_provider'] == 'yours') {
									if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
									echo '<a tabindex="105" href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
								} else if ($lmm_options['directions_provider'] == 'ors') {
									if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
									echo '<a tabindex="105" href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&wp=' . $lon . ',' . $lat . '&zoom=' . $zoom . '&routeWeigh=' . $lmm_options[ 'directions_ors_routeWeigh' ] . '&routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
								} else if ($lmm_options['directions_provider'] == 'bingmaps') {
									if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
									echo '<a tabindex="105" href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" /></a>';
								}
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
						echo '<a tabindex="106" href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
						echo '<a tabindex="107" href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
						echo '<a tabindex="108" href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
						echo '<a tabindex="109" href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp&full=yes&full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
						echo '<a tabindex="110" href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
						echo '<a tabindex="111" href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
						}
					echo '</div>'.PHP_EOL;
					echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ])) . '">' . (($markername == NULL) ? __('if set, markername will be displayed here','lmm') : stripslashes($markername)) . '</div>'.PHP_EOL;
					?>
					</div> <!--end lmm-panel-->
					<div id="selectlayer" style="height:<?php echo $mapheight; ?>px;"></div>
					</div><!--end mapsmarker div-->
					<?php
					if ($lmm_options['directions_popuptext_panel'] == 'yes') {
						if ($lmm_options['directions_provider'] == 'googlemaps') {
							if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
							if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
							$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
							$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
							$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
							$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
							$directionslink = "http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ];
						} else if ($lmm_options['directions_provider'] == 'yours') {
							$directionslink = "http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . $lmm_options[ 'directions_yours_route_type' ] . "&layer=" . $lmm_options[ 'directions_yours_layer' ];
						} else if ($lmm_options['directions_provider'] == 'ors') {
							$directionslink = "http://www.openrouteservice.org/?pos=" . $lon . "," . $lat . "&wp=" . $lon . "," . $lat . "&zoom=" . $zoom . "&routeWeigh=" . $lmm_options[ 'directions_ors_routeWeigh' ] . "&routeOpt=" . $lmm_options[ 'directions_ors_routeOpt' ] . "&layer=" . $lmm_options[ 'directions_ors_layer' ];
						} else if ($lmm_options['directions_provider'] == 'bingmaps') {
							if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
							$directionslink = "https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to;
						}
						$mpopuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
						$directions_settings_link = ( (current_user_can("activate_plugins")) && ($current_editor == "advanced") ) ? " (<a tabindex='103' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_settings#directions' title='" . esc_attr__("change directions settings","lmm") . "'>" . __("Settings","lmm") . "</a>)" : "";
					} else {
						//info: outside next condition to prevent PHP warnings if disabled
						$directionslink = '';
						$directions_settings_link = '';
						//info: needed for geocoding.js, line 426
						if ($address == NULL) {
							echo '<span id="popup-address" style="display:none;">' . esc_attr__('if set, address will be displayed here','lmm') . '</span>';
						} else {
							echo '<span id="popup-address" style="display:none;">' . stripslashes(htmlspecialchars($address)) . '</span>';
						}
					} ?>
				</td>
			</tr>
			<tr>
				<td class="lmm-border"><label for="default_icon"><strong><?php _e('Icon', 'lmm') ?></strong></label>
					<br/>
					<?php
					if ($current_editor == 'simplified') {
						echo '<div id="mapiconscollection" style="display:none;">';
					} ?>
					<a tabindex="122" title="Maps Icons Collection - https://mapicons.mapsmarker.com" href="https://mapicons.mapsmarker.com" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/logo-mapicons.png" width="88" heigh="31" style="float:left;margin-right:10px;" /></a>
					<small>
					<?php
					$mapicons_admin = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and upload the new icons to the directory %2$s/','lmm'), '<a tabindex="112" href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a>', LEAFLET_PLUGIN_ICONS_URL);
					$mapicons_user = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and ask your WordPress admin to upload the new icons to the directory %2$s/','lmm'), '<a tabindex="113" href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a>', LEAFLET_PLUGIN_ICONS_URL);
					$upload_icons_button_text = '<br/><br/>' . __('You can also upload the icons by clicking the button "upload new icon"','lmm') . ' <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '">(' . __('pro version only','lmm') . ')';
					if (current_user_can('activate_plugins')) { echo $mapicons_admin . $upload_icons_button_text; } else { echo $mapicons_user . $upload_icons_button_text; }
					if (is_multisite()) {
					$mapicons_directory = sprintf( __('As you are running your blog within a WordPress Multisite installation, please use this icon directory on your server: %2$s/','lmm'), '<a tabindex="114" href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a>', LEAFLET_PLUGIN_ICONS_DIR);
					echo '<br/>' . $mapicons_directory;
					}
					?>
					<?php
					if ($current_editor == 'simplified') {
						echo '</div>';
					} ?>
					</small>
				</td>
				<td class="lmm-border"><?php
					if ($current_editor == 'simplified') {
						if ($icon == NULL) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
						echo '<div class="div-marker-icon div-marker-icon-default" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input class="marker-icon-radio-button" id="default_icon" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
						if ($icon != NULL) {
							$filename_without_dots = str_replace('.','_',$icon); //info: dots in not allowed in selectors
							echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="background:#5e5d5d;"><label for="' . $icon . '"><img src="' . LEAFLET_PLUGIN_ICONS_URL . '/' . $icon . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" title="' . esc_attr__('filename','lmm') . ': ' . $icon . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . $icon .'" alt="' . $icon . '" /></label><br/><input class="marker-icon-radio-button" id="' . $icon . '" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="' . $icon . '" checked="" /></div>';
							echo '<div id="moreiconslink" style="display:block;padding:7px 0 0 70px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
							echo '<div id="moreicons" style="display:none;">';
							foreach ($iconlist as $row) {
								$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
								if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
								$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
								if ($icon_data == NULL) { //info: workaround #1 due to support request
									$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
								}
								if ($icon_data == NULL) { //info: workaround #2 due to support request
									$ch = curl_init();
									curl_setopt($ch, CURLOPT_HEADER, 0);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
									curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
									curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
									$icon_data = curl_exec($ch);
									curl_close($ch);
								}
								$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
								echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';
							}
						} else {
							echo '<div id="moreiconslink" style="display:block;padding:7px 0 0 70px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
							echo '<div id="moreicons" style="display:none;">';
							foreach ($iconlist as $row) {
								$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
								if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
								$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
								if ($icon_data == NULL) { //info: workaround #1 due to support request
									$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
								}
								if ($icon_data == NULL) { //info: workaround #2 due to support request
									$ch = curl_init();
									curl_setopt($ch, CURLOPT_HEADER, 0);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
									curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
									curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
									$icon_data = curl_exec($ch);
									curl_close($ch);
								}
								$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
								echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';							
							}
						}
						echo '<div style="text-align:center;float:left;line-height:0px;padding:17px 5px 0 5px;" id="showlessicons"><a style="cursor:pointer;">' . __('show fewer icons','lmm') . '</a></div>';
						echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img style="margin:3px 0 0 7px;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-filter-icons.png"></a>';
						echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img style="margin:3px 0 0 7px;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-upload-icons.png"></a>';
						echo '</div>';

					} else if ($current_editor == 'advanced') {
						if ($icon == NULL) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
						echo '<div class="div-marker-icon div-marker-icon-default" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input class="marker-icon-radio-button" id="default_icon" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
						foreach ($iconlist as $row) {
							$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
							if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
							$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
							if ($icon_data == NULL) { //info: workaround #1 due to support request
								$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
							}
							if ($icon_data == NULL) { //info: workaround #2 due to support request
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_HEADER, 0);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
								$icon_data = curl_exec($ch);
								curl_close($ch);
							}
							$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
							echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';
						}
						echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img style="margin:3px 0 0 7px;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-filter-icons.png"></a>';
						echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img style="margin:3px 0 0 7px;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/pro-feature-upload-icons.png"></a>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="lmm-border"><p><label for="popuptext"><strong><?php _e('Popup text','lmm') ?></strong></label>
				<br /><br />
				<label for="openpopup"><?php _e('open popup','lmm') ?></label>&nbsp;&nbsp;<input type="checkbox" name="openpopup" id="openpopup" <?php checked($openpopup, 1 ); ?>>
				<br/><small>
				<?php _e('If unchecked, the popup will only be visible after clicking on the marker on marker- or layer-maps.','lmm') ?>
				</small>
				<br /><br />
				<label for="add-markername"><?php _e('add markername','lmm') ?></label>&nbsp;&nbsp;<input type="checkbox" name="add-markername" id="add-markername" disabled="disabled"><br/>
				<small><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm'); ?>"><?php _e('Feature available in pro version only','lmm'); ?></a></small>
				<br /><br />
				<label for="add-directions"><?php _e('add directions link','lmm') ?></label>&nbsp;&nbsp;<?php if ($lmm_options['directions_popuptext_panel'] == 'yes') { echo '<input type="checkbox" name="add-directions" id="add-directions" checked="checked" disabled="disabled">'; } else { echo '<input type="checkbox" name="add-directions" id="add-directions" disabled="disabled">'; } ?>
				<br/><small><a tabindex="119" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-directions"><?php _e('Please visit Settings to change this globally','lmm'); ?></a></small>
				</p>
				</td>
				<td class="lmm-border">
				<script type="text/javascript">var unsaved = false;</script>
				<?php
				if ($lmm_options['directions_popuptext_panel'] == 'yes') {
					$popup_panel = '+popup_panel';
				} else {
					$popup_panel = '';
				}
				if ( version_compare( $wp_version, '3.9-alpha', '>=' ) ) {
					$settings = array(
						'wpautop' => true,
						'tinymce' => array(
							'height' => '250',
							'content_style' => 'img {' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '} a {text-decoration:none;} a:hover {text-decoration:underline;}',
							'setup' => 'function(ed) {
											ed.on("keyup", function(ed,e) {
												var popup_panel = "<div style=\"border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;\">"+document.getElementById("address").value+" <a href=\"' . $directionslink . '\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a>' . $directions_settings_link . '</div>";
												marker._popup.setContent(tinymce.activeEditor.getContent()' . $popup_panel . ');
												unsaved = true;
											});
											//info: remove disabled attribute for HTML editor mode only - text mode see line 2600
											ed.on("LoadContent", function(ed, e){
												jQuery("#submit_top").removeAttr("disabled");
												jQuery("#submit_bottom").removeAttr("disabled");
												jQuery("#delete").removeAttr("disabled");
											});
									}'
						 ),
					'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,code,close'));
				} else {
					$settings = array(
						'wpautop' => true,
						'tinymce' => array(
							'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,link,unlink,|,ltr,rtl',
							'theme' => 'advanced',
							'height' => '250',
							'content_style' => 'html .mcecontentbody {font:12px/1.4 "Helvetica Neue",Arial,Helvetica,sans-serif; max-width:' . intval($lmm_options['defaults_marker_popups_maxwidth']) + 1 . 'px; /* Default + 1 fix */ word-wrap:break-word;} .mcecontentbody a {text-decoration:none;} .mcecontentbody a:hover {text-decoration:underline;} .mcecontentbody img {' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '}',
							'theme_advanced_statusbar_location' => 'bottom',
							'setup' => 'function(ed) {
								ed.onKeyUp.add(function(ed, e) {
									var popup_panel = "<div style=\"border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;\">"+document.getElementById("address").value+" <a href=\"' . $directionslink . '\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a>' . $directions_settings_link . '</div>";
									marker._popup.setContent(ed.getContent()' . $popup_panel . ');
									unsaved = true;
								});
								ed.onInit.add(function(ed) {
									jQuery("#submit_top").removeAttr("disabled");
									jQuery("#submit_bottom").removeAttr("disabled");
									jQuery("#delete").removeAttr("disabled");
								});
							}'
						),
					'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,code,close'));
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
				$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, stripslashes(preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $popuptext)));
				//info: strip evil scripts
				if ($lmm_options['wp_kses_status'] == 'enabled') {
					$popuptext_sanitized = wp_kses($popuptext_sanitized, array_merge($allowedposttags, $additionaltags));
				}
				wp_editor( $popuptext_sanitized, 'popuptext', $settings);
				?>
				<span style="display:none;" id="selectlayer-popuptext-hidden"><?php echo do_shortcode($popuptext_sanitized); ?></span>
                 (<a target="_blank" href="https://www.mapsmarker.com/multilingual" target="_blank"><?php _e('translate', 'lmm'); ?></a> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/pro-feature-banner-small.png" width="68" height="9" border="0">)<br/>
				<small>
					<?php
					echo '<span style="' . $current_editor_css . '">' . sprintf( esc_attr__('Note: if you add an image, the following CSS definition will be applied: %1$s','lmm'), '<code>' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '</code>');
					if (current_user_can('activate_plugins')) { echo ' <a tabindex="102" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-marker_popups" title="' . esc_attr__('can be changed at section "Default values for marker popups"','lmm') . '">(' . __('Settings','lmm') . ')</a>'; }
					?>
					</span>
					</small>
				</td>
			</tr>
			<tr id="advanced-settings">
				<td class="lmm-border"><strong><?php _e('Advanced settings','lmm') ?></strong></td>
				<td class="lmm-border">
				<div style="<?php echo $current_editor_css; ?>">
					<p><strong><?php _e('WMS layers','lmm') ?></strong> <?php if (current_user_can('activate_plugins')) { echo '<a tabindex="101" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#wms">(' . __('Settings','lmm') . ')</a>'; } ?></p>
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
					<script type="text/javascript">
						var $j = jQuery.noConflict();
						if ($j.isFunction($j(document).datetimepicker)) {
							$j(function() {
								$j("#kml_timestamp").datetimepicker({
									dateFormat: 'yy-mm-dd',
									changeMonth: true,
									changeYear: true,
									timeText: '<?php esc_attr_e('Time','lmm'); ?>',
									hourText: '<?php esc_attr_e('Hour','lmm'); ?>',
									minuteText: '<?php esc_attr_e('Minute','lmm'); ?>',
									secondText: '<?php esc_attr_e('Second','lmm'); ?>',
									currentText: '<?php esc_attr_e('Now','lmm'); ?>',
									closeText: '<?php esc_attr_e('Add','lmm'); ?>',
									timeFormat: 'hh:mm:ss',
									showSecond: true,
								});
							});
						}
					</script>
					<label for="kml_timestamp"><strong><?php _e('Timestamp for KML animation','lmm') ?>:</strong></label> <a tabindex="104" href="https://www.mapsmarker.com/kml-timestamp" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('Click here for more information on animations in KML/Google Earth','lmm'); ?>" width="12" height="12" border="0"/></a><br/>
					<input type="<?php echo $datetime_ios_workaround; ?>" id="kml_timestamp" name="kml_timestamp" value="<?php echo $kml_timestamp ; ?>" /><br/>
					<small><?php _e('If empty, marker creation date will be used','lmm') ?></small>
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

			<?php if ($mcreatedby != NULL) {?>
			<tr style="<?php echo $current_editor_css; ?>">
				<td><small><strong><?php _e('Audit','lmm') ?></strong></small></td>
				<td><small>
					<?php
					echo __('Marker added by','lmm') . ' ';
					echo $mcreatedby . ' - ' . $mcreatedon;
					if ($mupdatedon != $mcreatedon) {
						echo ', ' . __('last update by','lmm');
						echo ' ' . $mupdatedby . ' - ' . $mupdatedon;
					}; ?>
					</small></td>
			</tr>
			<?php }; ?>
		</table>

	<table><tr><td>
	<input id="submit_bottom" disabled style="font-weight:bold;<?php echo $margin_top = ($isedit === false) ? 'margin-top:17px;' : '' ?>" type="submit" name="marker" class="submit button-primary" value="<?php ($isedit === true) ? _e('update','lmm') : _e('publish','lmm') ?>" />
	</form>
	</td>
	<td>
		<?php
				echo '<form method="post">';
				echo '<div class="submit" style="margin:0 0 0 40px;">';
				echo '<input title="' . esc_attr__('Feature available in pro version only','lmm') . '" class="submit button-secondary lmm-nav-secondary" type="submit" name="marker" value="' . __('duplicate', 'lmm') . '" disabled="disabled" />';
				echo '</div></form>';
		?>
	</td>
	<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
	<td>
		<form method="post">
			<?php wp_nonce_field('marker-nonce'); ?>
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="action" value="delete" />
				<?php $confirm = sprintf( esc_attr__('Do you really want to delete marker %1$s (ID %2$s)?','lmm'), $markername, $id) ?>
				<div class="submit" style="margin:0 0 0 40px;">
				<input id="delete" class="submit button-secondary lmm-nav-secondary" style="color:#FF0000;" type="submit" name="marker" value="<?php _e('delete', 'lmm') ?>" onclick="return confirm('<?php echo $confirm ?>')" disabled="disabled" />
				</div>
		</form>
	</td>
	<?php } ?>
</div>
	</tr></table>
<!--wrap-->
<script type="text/javascript">
/* //<![CDATA[ */
var marker,selectlayer,googleLayer_roadmap,googleLayer_satellite,googleLayer_hybrid,googleLayer_terrain,bingaerial,bingaerialwithlabels,bingroad,osm_mapnik,stamen_terrain,stamen_toner,stamen_watercolor,mapquest_osm,mapquest_aerial,mapquest_hybrid,ogdwien_basemap,ogdwien_satellite,mapbox,mapbox2,mapbox3,custom_basemap,custom_basemap2,custom_basemap3,empty_basemap,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl;
(function($) {
  selectlayer = new L.Map("selectlayer", { dragging: <?php echo $lmm_options['misc_map_dragging'] ?>, touchZoom: <?php echo $lmm_options['misc_map_touchzoom'] ?>, scrollWheelZoom: <?php echo $lmm_options['misc_map_scrollwheelzoom'] ?>, doubleClickZoom: <?php echo $lmm_options['misc_map_doubleclickzoom'] ?>, boxzoom: <?php echo $lmm_options['map_interaction_options_boxzoom'] ?>, trackResize: <?php echo $lmm_options['misc_map_trackresize'] ?>, worldCopyJump: <?php echo $lmm_options['map_interaction_options_worldcopyjump'] ?>, closePopupOnClick: <?php echo $lmm_options['misc_map_closepopuponclick'] ?>, keyboard: <?php echo $lmm_options['map_keyboard_navigation_options_keyboard'] ?>, keyboardPanOffset: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) ?>, keyboardZoomOffset: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) ?>, inertia: <?php echo $lmm_options['map_panning_inertia_options_inertia'] ?>, inertiaDeceleration: <?php echo intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) ?>, inertiaMaxSpeed: <?php echo intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) ?>, zoomControl: <?php echo $lmm_options['misc_map_zoomcontrol'] ?>, crs: <?php echo $lmm_options['misc_projections'] ?> });
	<?php
		$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
		$attrib_prefix = '<a tabindex=\"115\" href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker for WordPress - helping you to share your favorite spots and tracks','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Leaflet Maps Marker') . '\">Leaflet</a>/<a href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Leaflet Maps Marker') . '\">icons</a>)';
		$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
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
		$osm_attribution = $osm_attrib_general . ', ' . __("Tiles courtesy of","lmm") . ' <a  tabindex=\"123\" href=\"https://hotosm.org/\" target=\"_blank\">Humanitarian OpenStreetMap Team</a>' . $osm_editlink;
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
	var custom_basemap = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) ?>", {mmid: 'custom_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap_tms' ] ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	var custom_basemap2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap2_tms' ] ?>, <?php echo $error_tile_url_custom_basemap2; ?>attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap2_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	var custom_basemap3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap3_tms' ] ?>, <?php echo $error_tile_url_custom_basemap3; ?>attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap3_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
	var empty_basemap = new L.TileLayer("", {mmid: 'empty_basemap'});

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
	}); //info open layer control box by default on all devices on backend

  selectlayer.setView(new L.LatLng(<?php echo $lat . ', ' . $lon; ?>), <?php echo $zoom ?>);
  selectlayer.addLayer(<?php echo $basemap; ?>)
	//info: controlbox - add active overlays on marker level
	<?php
		if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
			echo ".addLayer(overlays_custom)";
		if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
			echo ".addLayer(overlays_custom2)";
		if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
			echo ".addLayer(overlays_custom3)";
		if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
			echo ".addLayer(overlays_custom4)";
	?>
	//info: controlbox - add active overlays on marker level
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
  marker = new L.Marker(new L.LatLng(<?php echo $lat . ", " . $lon; ?>),{ <?php if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') { $markername_title = strip_tags(htmlspecialchars_decode($markername)); echo "title: '$markername_title', "; }; ?> opacity: <?php echo floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) ?>, draggable: true});
  <?php if ($icon == NULL) {
  	echo "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
  } else {
  	echo "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_ICONS_URL . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
  } ?>
  <?php if ( ($popuptext == NULL) && ($lmm_options['directions_popuptext_panel'] == 'no') ) { ?>
  marker.options.clickable = false;
  <?php }?>
  selectlayer.addLayer(marker);
  <?php
 //info: set controlbox visibility 2/2
 if ($controlbox == '0') {
	echo "$('.leaflet-control-layers').hide();";
 } else if ($controlbox == '2') {
	echo "layersControl._expand();";
 }

 if ($lmm_options['directions_popuptext_panel'] == 'yes') {
	 $directions_settings_link = ( (current_user_can('activate_plugins')) && ($current_editor == 'advanced') ) ? ' (<a tabindex="103" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-directions" title="' . esc_attr__('change directions settings','lmm') . '">' . __('Settings','lmm') . '</a>)' : '';
	 if ($address == NULL) {
		$google_from = $lat . ',' . $lon;
		$address = esc_attr__('if set, address will be displayed here','lmm');
	} else {
		$google_from = urlencode($address);
	}
	 $address = (($address == NULL) ? esc_attr__('if set, address will be displayed here','lmm') : $address);
	 $popuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
	 $popuptext = $popuptext . '<div class="popup-directions" style="' . $popuptext_css . '">' . addslashes(str_replace("\'","&#39;",$address)) . ' ';

	 if ($lmm_options['directions_provider'] == 'googlemaps') {
		 if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
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
		$popuptext = $popuptext . '(<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
	 } else if ($lmm_options['directions_provider'] == 'yours') {
		 $popuptext = $popuptext . '(<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>';
	 } else if ($lmm_options['directions_provider'] == 'ors') {
		 $popuptext = $popuptext . '(<a href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&wp=' . $lon . ',' . $lat . '&zoom=' . $zoom . '&routeWeigh=' . $lmm_options[ 'directions_ors_routeWeigh' ] . '&routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
	 } else if ($lmm_options['directions_provider'] == 'bingmaps') {
		if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
		 $popuptext = $popuptext . '(<a href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
	 }
	 $popuptext = $popuptext . $directions_settings_link . '</div>';
 }
 ?>
 <?php
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
	$popuptext_sanitized_js = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $popuptext));
	//info: strip evil scripts
	if ($lmm_options['wp_kses_status'] == 'enabled') {
		$popuptext_sanitized_js = wp_kses($popuptext_sanitized_js, array_merge($allowedposttags, $additionaltags));
	}
  ?>
  marker.bindPopup('<?php echo $popuptext_sanitized_js; ?>',{maxWidth: <?php echo intval($lmm_options['defaults_marker_popups_maxwidth']) ?>, minWidth: <?php echo intval($lmm_options['defaults_marker_popups_minwidth']) ?>, maxHeight: <?php echo intval($lmm_options['defaults_marker_popups_maxheight']) ?>, autoPan: <?php echo $lmm_options['defaults_marker_popups_autopan'] ?>, closeButton: <?php echo $lmm_options['defaults_marker_popups_closebutton'] ?>, autoPanPadding: new L.Point(<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']) ?>, <?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']) ?>)})<?php  if ($openpopup == 1) { echo '.openPopup()'; } ?>;
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
		//info: when custom overlay gets checked from control box
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
  selectlayer.on('moveend', function(e) { document.getElementById('zoom').value = selectlayer.getZoom();});
  selectlayer.on('click', function(e) {
      selectlayer.setView(e.latlng,selectlayer.getZoom());
      document.getElementById('lat').value = e.latlng.lat.toFixed(6);
      document.getElementById('lon').value = e.latlng.lng.toFixed(6);
      marker.setLatLng(e.latlng);
      <?php if ($popuptext != NULL) { ?>
      marker.bindPopup('<?php echo $popuptext_sanitized_js; ?>',{maxWidth: <?php echo intval($lmm_options['defaults_marker_popups_maxwidth']) ?>, minWidth: <?php echo intval($lmm_options['defaults_marker_popups_minwidth']) ?>, maxHeight: <?php echo intval($lmm_options['defaults_marker_popups_maxheight']) ?>, autoPan: <?php echo $lmm_options['defaults_marker_popups_autopan'] ?>, closeButton: <?php echo $lmm_options['defaults_marker_popups_closebutton'] ?>, autoPanPadding: new L.Point(<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']) ?>, <?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']) ?>)});
      <?php }?>
	  if($('input:checkbox[name=openpopup]').is(':checked')) {
		  marker.openPopup();
	  }
  });
  //info: set new coordinates on marker drag
  marker.on('dragend', function(e) {
      var newlocation = marker.getLatLng();
	  var newlat = newlocation['lat'];
  	  var newlon = newlocation['lng'];
	  document.getElementById('lat').value = newlat.toFixed(6);
	  document.getElementById('lon').value = newlon.toFixed(6);
	  selectlayer.setView(newlocation,selectlayer.getZoom());
	  if($('input:checkbox[name=openpopup]').is(':checked')) {
			marker.openPopup();
	  }
  });
  //info: define variables
  var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), popupText = $('#popuptext'), lat = $('#lat'), lon = $('#lon'), panel = $('#lmm-panel'), lmm = $('#lmm'), markername = $('#markername'), zoom = $('#zoom');
	//info: bugfix causing maps not to show up in WP 3.0 and errors in WP <3.3
	<?php if ( version_compare( $wp_version, '3.3', '>=' ) ) { ?>
	//info: change zoom level when changing form field
	zoom.on('change', function(e) {
		if(isNaN(zoom.val())) {
                alert('<?php esc_attr_e('Invalid format! Please only use numbers!','lmm') ?>');
		} else {
		selectlayer.setZoom(zoom.val());
		}
	});
	markername.on('change', function(e) {
		if( markername.val() ){
			$('#lmm-panel-text').text(markername.val());
		} else {
			$('#lmm-panel-text').text('&nbsp;');
		}
	});
	<?php } ?>
	mapWidth.change(function() {
		if(!isNaN(mapWidth.val())) {
			lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
			selectlayer.invalidateSize();
		}
	});
	$('input:radio[name=mapwidthunit]').click(function() {
			lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
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
	$('input:checkbox[name=openpopup]').click(function() {
		if($('input:checkbox[name=openpopup]').is(':checked')) {
			marker.openPopup();
		} else {
			marker.closePopup();
		}
	});
	//info: check if lat is a number
	$('input:text[name=lat]').change(function(e) {
		if(isNaN(lat.val())) {
                alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: check if lon is a number
	$('input:text[name=lon]').change(function(e) {
		if(isNaN(lon.val())) {
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
	//info: show all icons on click on simplified editor
	$('#moreiconslink').click(function(e) {
			$('#moreiconslink').hide();
			$('#moreicons').show('fast');
			$('#mapiconscollection').show('fast');
	});
	//info: show less icons on click on simplified editor
	$('#showlessicons').click(function(e) {
			$('#moreicons').hide();
			$('#mapiconscollection').hide();
			$('#moreiconslink').show('fast');
	});
	//info: sets map center to new marker position when entering lat/lon manually
	$('input:text[name=lat],input:text[name=lon]').change(function(e) {
		var markerLocation = new L.LatLng(lat.val(),lon.val());
		marker.closePopup();
		marker.setLatLng(markerLocation);
		selectlayer.setView(markerLocation, selectlayer.getZoom());
		if($('input:radio[name=openpopup]:checked').val() == 1) {
			marker.openPopup();
		}
	});
	//info: update marker icon upon click
	$('.div-marker-icon').click(function(e) {
		var newicon = $(this).children('.marker-icon-radio-button').attr("value");
		document.getElementById('icon_hidden').value = newicon; //info: IE11 fix
		$('.div-marker-icon').css('background','none');
		$('.div-marker-icon').css('opacity','0.4');
		if (newicon) {
			marker.setIcon(new L.Icon({iconUrl: '<?php echo LEAFLET_PLUGIN_ICONS_URL . '/' ?>' + newicon,iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],shadowUrl: '<?php echo $marker_shadow_url; ?>',shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],className: 'lmm_marker_icon_default'}));
			var newicon_without_dots = newicon.replace('.', '_'); //info: dots in not allowed in selectors
			$('.div-marker-icon-'+newicon_without_dots).css("opacity","1");
			$('.div-marker-icon-'+newicon_without_dots).css("background","#5e5d5d");
		} else {
			marker.setIcon(new L.Icon({iconUrl: '<?php echo LEAFLET_PLUGIN_URL . '/leaflet-dist/images/marker.png' ?>',iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],shadowUrl: '<?php echo $marker_shadow_url; ?>',shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],className: 'lmm_marker_icon_<?php echo substr($icon, 0, -4); ?>'}));
			$('.div-marker-icon-default').css("opacity","1");
			$('.div-marker-icon-default').css("background","#5e5d5d");
		}
	});
	//info: warn on unsaved changes when leaving page
	$(":input, textarea").change(function(){
		unsaved = true;
	});
	selectlayer.on('zoomend click', function(e) {
		unsaved = true;
	});
	marker.on('dragend', function(e) {
		unsaved = true;
	});
	$('#submit_top, #submit_bottom, #delete').click(function() {
		unsaved = false;
	});
	function unloadPage(){
		if(unsaved){
			return "<?php esc_attr_e('You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?','lmm'); ?>";
		}
	}
	window.onbeforeunload = unloadPage;

	//info: select markername, address, mapwith, mapheight & zoom input field on focus
	$("#markername, #address, #mapwidth, #mapheight, #zoom").focus(function() {
		$(this).select();
	});
	//info: fix for autofocus in Chrome
	$(document).ready(function(){
		$('input[autofocus="autofocus"]').focus();
	});
	//info: remove disabled attribute for publish/update button if text editor is used
	$(document).ready(function(){
		if($('#wp-popuptext-wrap').hasClass('html-active')){
			$('#submit_top').removeAttr('disabled');
		    $('#submit_bottom').removeAttr('disabled');
		    $('#delete').removeAttr('disabled');
		}
	});
	//info: workaround for conflicts with other plugins also using TinyMCE-LoadContent event, preventing enabling of buttons	
	$(document).ready(function(){
		function lmm_initialize_popuptext_editor(){
			if(typeof tinymce !== "undefined" && typeof tinymce.editors !== "undefined" && typeof tinymce.editors.popuptext !== "undefined"){
				tinymce.editors.popuptext.on("LoadContent", function(ed, e){
						jQuery("#submit_top").removeAttr("disabled");
						jQuery("#submit_bottom").removeAttr("disabled");
						jQuery("#delete").removeAttr("disabled");
					});
			}else if(!$('#wp-popuptext-wrap').hasClass('html-active')){ //info: do not run if TinyMCE text mode is enabled
				setTimeout(function(){
					lmm_initialize_popuptext_editor();
				},250);
			}
		}
		lmm_initialize_popuptext_editor();
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
		aroundLatLng: '<?php echo (isset($lmm_options["geocoding_algolia_aroundLatLng"]))?esc_js(trim($lmm_options["geocoding_algolia_aroundLatLng"])):""; ?>',
		referrer: '<?php echo (isset($lmm_options["geocoding_algolia_referrer"]))?esc_js(trim($lmm_options["geocoding_algolia_referrer"])):get_home_url(); ?>',
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
		<?php if ($isedit) { ?>
			var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php }else{ ?>
			var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php } ?>
		mapzen_search.init();
	});
<?php
} else if ($lmm_options['geocoding_provider'] == 'algolia-places')  {  ?>
	jQuery(document).ready(function(){
		<?php if ($isedit) { ?>
			var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php }else{ ?>
			var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php } ?>
		algolia_places.init();
	});
<?php
} else if ($lmm_options['geocoding_provider'] == 'photon')  {  ?>
	jQuery(document).ready(function(){
		<?php if ($isedit) { ?>
			var photon = new MMP_Geocoding('photon', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php }else{ ?>
			var photon = new MMP_Geocoding('photon', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php } ?>
		photon.init();
	});
<?php
} else if ( ($lmm_options['geocoding_provider'] == 'mapquest-geocoding' ) && ($lmm_options['geocoding_mapquest_geocoding_api_key'] != NULL) ) { ?>
	jQuery(document).ready(function(){
		<?php if ($isedit) { ?>
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
	<?php if ($isedit) { ?>
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
<span style="display:none;" id="defaults_texts_geocoding_mapzen_api_key_needed"><?php echo sprintf(__('Fallback geocoding provider was activated as Mapzen Search requires an API key since 04/2017.<br/>We recommend <a href="%1$s" target="_blank">registering a free Mapzen Search API key</a> which allows up to %2$s requests/%3$s and a maximum of %4$s requests/%5$s.','lmm'), 'https://www.mapsmarker.com/mapzen-search#tutorial', '30.000', __('day','lmm'), '6', __('second','lmm')); ?></span>
<span style="display:none;" id="defaults_texts_mapquest_key_issue"><?php echo sprintf(__('MapQuest Geocoding error - please contact support at <a href="%1$s">support@mapquest.com</a>'), 'mailto:support@mapquest.com?subject=Issue with API key ' . esc_js(trim($lmm_options['geocoding_mapquest_geocoding_api_key']))); ?></span>
<input type="hidden" id="defaults_texts_geocoding_fallback_info" value="<?php echo __('Automatically switched to fallback provider','lmm'); ?>" />
<input type="hidden" id="defaults_texts_geocoding_results_header" value="<?php echo __('To select a location, please click on a result or press','lmm'); ?>" />
<span style="display:none;" id="defaults_texts_geocoding_footer_tips"><div id="geocoding-footer-tips" style="float:left;margin:4px 0 0 0;"><a href="https://www.mapsmarker.com/geocoding-optimization" target="_blank" style="text-decoration:underline;" title="<?php echo esc_attr__('show tutorial at mapsmarker.com','lmm'); ?>"><?php echo __('Tip: adjust geocoding settings for more targeted search results','lmm'); ?></a></div></span>
<input type="hidden" id="defaults_marker_popups_maxWidth" value="<?php echo intval($lmm_options['defaults_marker_popups_maxwidth']);?>" />
<input type="hidden" id="defaults_marker_popups_minWidth" value="<?php echo intval($lmm_options['defaults_marker_popups_minwidth']);?>" />
<input type="hidden" id="defaults_marker_popups_maxHeight" value="<?php echo intval($lmm_options['defaults_marker_popups_maxheight']);?>" />
<input type="hidden" id="defaults_marker_popups_autoPan" value="<?php echo $lmm_options['defaults_marker_popups_autopan'];?>" />
<input type="hidden" id="defaults_marker_popups_closeButton" value="<?php echo $lmm_options['defaults_marker_popups_closebutton'];?>" />
<input type="hidden" id="defaults_marker_popups_autopanpadding_x" value="<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']);?>" />
<input type="hidden" id="defaults_marker_popups_autopanpadding_y" value="<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']);?>" />
<?php //info: check if marker exists - part 2
} ?>
<?php }
include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');
?>