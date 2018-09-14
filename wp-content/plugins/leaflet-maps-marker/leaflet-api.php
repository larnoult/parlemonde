<?php
/*
    MapsMarker API - Leaflet Maps Marker Plugin
*/
//info: construct path to wp-load.php
while(!is_file('wp-load.php')) {
	if(is_dir('..' . DIRECTORY_SEPARATOR)) chdir('..' . DIRECTORY_SEPARATOR);
	else die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'globals.php' );

$lmm_options = get_option( 'leafletmapsmarker_options' );
$callback = isset($_POST['callback']) ? preg_replace( '/[^a-zA-Z0-9_]/', '', $_POST['callback']) : (isset($_GET['callback']) ? preg_replace( '/[^a-zA-Z0-9_]/', '', $_GET['callback']) : $lmm_options['api_json_callback']);
$format = ( isset($_POST['format']) && ( ($_POST['format'] == 'json') || ($_POST['format'] == 'xml')) ) ? $_POST['format'] : ( isset($_GET['format']) && ( ($_GET['format'] == 'json') || ($_GET['format'] == 'xml') ) ? $_GET['format'] : $lmm_options['api_default_format']);

//info: API authentication functions
function lmm_check_signature() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$api_key = $lmm_options['api_key'];
	$expires = isset($_POST['expires']) ? $_POST['expires'] : (isset($_GET['expires']) ? $_GET['expires'] : '');
	$string_to_check = sprintf("%s:%s", $api_key, $expires);
	$calculated_sig = lmm_calculate_signature($string_to_check);
	if (time() >= $expires) {
		return false; 
	}
	$signature = isset($_POST['signature']) ? urldecode($_POST['signature']) : (isset($_GET['signature']) ? $_GET['signature'] : '');
	$is_valid = $signature == $calculated_sig;
	return $is_valid;
}
function lmm_calculate_signature($string) {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$api_key_private = $lmm_options['api_key_private'];
	$hash = hash_hmac("sha1", $string, $api_key_private, true);
	$sig  = base64_encode($hash);
	return $sig;
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
if (!lmm_is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php') ) {
	if ($format == 'json') {
		header('Content-type: application/json; charset=utf-8');
		if ($callback != NULL) { echo $callback . '('; }
		echo '{'.PHP_EOL;
		echo '"success":false,'.PHP_EOL;
		echo '"message":"' . sprintf(esc_attr__('The plugin "Leaflet Maps Marker" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), get_bloginfo('admin_email') ) . '",'.PHP_EOL;
		echo '"data": { }'.PHP_EOL;
		echo '}';
		if ($callback != NULL) { echo ');'; }
	} else if ($format == 'xml') {
		header('Content-type: application/xml; charset=utf-8');
		echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
		echo '<mapsmarker>'.PHP_EOL;
		echo '<success>false</success>'.PHP_EOL;
		echo '<message>' . sprintf(esc_attr__('The plugin "Leaflet Maps Marker" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), get_bloginfo('admin_email') ) . '</message>'.PHP_EOL;
		echo '<data></data>'.PHP_EOL;
		echo '</mapsmarker>';
	}
} else {
	require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'class-mmp-geocoding.php' );
	$request_method = $_SERVER['REQUEST_METHOD'];
	global $wpdb;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$version = isset($_POST['version']) ? $_POST['version'] : (isset($_GET['version']) ? $_GET['version'] : '');
	$geocode = isset($_POST['geocode']) ? $_POST['geocode'] : (isset($_GET['geocode']) ? $_GET['geocode'] : '');
	//info: remap marker + layer
	$remap_id = isset($_POST['remap_id']) ? $_POST['remap_id'] : (isset($_GET['remap_id']) ? $_GET['remap_id'] : 'id');
	$remap_markername = isset($_POST['remap_markername']) ? $_POST['remap_markername'] : (isset($_GET['remap_markername']) ? $_GET['remap_markername'] : 'markername');
	$remap_basemap = isset($_POST['remap_basemap']) ? $_POST['remap_basemap'] : (isset($_GET['remap_basemap']) ? $_GET['remap_basemap'] : 'basemap');
	$remap_layer = isset($_POST['remap_layer']) ? $_POST['remap_layer'] : (isset($_GET['remap_layer']) ? $_GET['remap_layer'] : 'layer');
	$remap_lat = isset($_POST['remap_lat']) ? $_POST['remap_lat'] : (isset($_GET['remap_lat']) ? $_GET['remap_lat'] : 'lat');
	$remap_lon = isset($_POST['remap_lon']) ? $_POST['remap_lon'] : (isset($_GET['remap_lon']) ? $_GET['remap_lon'] : 'lon');
	$remap_icon = isset($_POST['remap_icon']) ? $_POST['remap_icon'] : (isset($_GET['remap_icon']) ? $_GET['remap_icon'] : 'icon');
	$remap_popuptext = isset($_POST['remap_popuptext']) ? $_POST['remap_popuptext'] : (isset($_GET['remap_popuptext']) ? $_GET['remap_popuptext'] : 'popuptext');
	$remap_zoom = isset($_POST['remap_zoom']) ? $_POST['remap_zoom'] : (isset($_GET['remap_zoom']) ? $_GET['remap_zoom'] : 'zoom');
	$remap_openpopup = isset($_POST['remap_openpopup']) ? $_POST['remap_openpopup'] : (isset($_GET['remap_openpopup']) ? $_GET['remap_openpopup'] : 'openpopup');
	$remap_mapwidth = isset($_POST['remap_mapwidth']) ? $_POST['remap_mapwidth'] : (isset($_GET['remap_mapwidth']) ? $_GET['remap_mapwidth'] : 'mapwidth');
	$remap_mapwidthunit = isset($_POST['remap_mapwidthunit']) ? $_POST['remap_mapwidthunit'] : (isset($_GET['remap_mapwidthunit']) ? $_GET['remap_mapwidthunit'] : 'mapwidthunit');
	$remap_mapheight = isset($_POST['remap_mapheight']) ? $_POST['remap_mapheight'] : (isset($_GET['remap_mapheight']) ? $_GET['remap_mapheight'] : 'mapheight');
	$remap_panel = isset($_POST['remap_panel']) ? $_POST['remap_panel'] : (isset($_GET['remap_panel']) ? $_GET['remap_panel'] : 'panel');
	$remap_createdby = isset($_POST['remap_createdby']) ? $_POST['remap_createdby'] : (isset($_GET['remap_createdby']) ? $_GET['remap_createdby'] : 'createdby');
	$remap_createdon = isset($_POST['remap_createdon']) ? $_POST['remap_createdon'] : (isset($_GET['remap_createdon']) ? $_GET['remap_createdon'] : 'createdon');
	$remap_updatedby = isset($_POST['remap_updatedby']) ? $_POST['remap_updatedby'] : (isset($_GET['remap_updatedby']) ? $_GET['remap_updatedby'] : 'updatedby');
	$remap_updatedon = isset($_POST['remap_updatedon']) ? $_POST['remap_updatedon'] : (isset($_GET['remap_updatedon']) ? $_GET['remap_updatedon'] : 'updatedon');
	$remap_controlbox = isset($_POST['remap_controlbox']) ? $_POST['remap_controlbox'] : (isset($_GET['remap_controlbox']) ? $_GET['remap_controlbox'] : 'controlbox');
	$remap_overlays_custom = isset($_POST['remap_overlays_custom']) ? $_POST['remap_overlays_custom'] : (isset($_GET['remap_overlays_custom']) ? $_GET['remap_overlays_custom'] : 'overlays_custom');
	$remap_overlays_custom2 = isset($_POST['remap_overlays_custom2']) ? $_POST['remap_overlays_custom2'] : (isset($_GET['remap_overlays_custom2']) ? $_GET['remap_overlays_custom2'] : 'overlays_custom2');
	$remap_overlays_custom3 = isset($_POST['remap_overlays_custom3']) ? $_POST['remap_overlays_custom3'] : (isset($_GET['remap_overlays_custom3']) ? $_GET['remap_overlays_custom3'] : 'overlays_custom3');
	$remap_overlays_custom4 = isset($_POST['remap_overlays_custom4']) ? $_POST['remap_overlays_custom4'] : (isset($_GET['remap_overlays_custom4']) ? $_GET['remap_overlays_custom4'] : 'overlays_custom4');
	$remap_wms = isset($_POST['remap_wms']) ? $_POST['remap_wms'] : (isset($_GET['remap_wms']) ? $_GET['remap_wms'] : 'wms');
	$remap_wms2 = isset($_POST['remap_wms2']) ? $_POST['remap_wms2'] : (isset($_GET['remap_wms2']) ? $_GET['remap_wms2'] : 'wms2');
	$remap_wms3 = isset($_POST['remap_wms3']) ? $_POST['remap_wms3'] : (isset($_GET['remap_wms3']) ? $_GET['remap_wms3'] : 'wms3');
	$remap_wms4 = isset($_POST['remap_wms4']) ? $_POST['remap_wms4'] : (isset($_GET['remap_wms4']) ? $_GET['remap_wms4'] : 'wms4');
	$remap_wms5 = isset($_POST['remap_wms5']) ? $_POST['remap_wms5'] : (isset($_GET['remap_wms5']) ? $_GET['remap_wms5'] : 'wms5');
	$remap_wms6 = isset($_POST['remap_wms6']) ? $_POST['remap_wms6'] : (isset($_GET['remap_wms6']) ? $_GET['remap_wms6'] : 'wms6');
	$remap_wms7 = isset($_POST['remap_wms7']) ? $_POST['remap_wms7'] : (isset($_GET['remap_wms7']) ? $_GET['remap_wms7'] : 'wms7');
	$remap_wms8 = isset($_POST['remap_wms8']) ? $_POST['remap_wms8'] : (isset($_GET['remap_wms8']) ? $_GET['remap_wms8'] : 'wms8');
	$remap_wms9 = isset($_POST['remap_wms9']) ? $_POST['remap_wms9'] : (isset($_GET['remap_wms9']) ? $_GET['remap_wms9'] : 'wms9');
	$remap_wms10 = isset($_POST['remap_wms10']) ? $_POST['remap_wms10'] : (isset($_GET['remap_wms10']) ? $_GET['remap_wms10'] : 'wms10');
	$remap_kml_timestamp = isset($_POST['remap_kml_timestamp']) ? $_POST['remap_kml_timestamp'] : (isset($_GET['remap_kml_timestamp']) ? $_GET['remap_kml_timestamp'] : 'kml_timestamp');
	$remap_address = isset($_POST['remap_address']) ? $_POST['remap_address'] : (isset($_GET['remap_address']) ? $_GET['remap_address'] : 'address');
	//info: remap layer only
	$remap_name = isset($_POST['remap_name']) ? $_POST['remap_name'] : (isset($_GET['remap_name']) ? $_GET['remap_name'] : 'name');
	$remap_layerzoom = isset($_POST['remap_layerzoom']) ? $_POST['remap_layerzoom'] : (isset($_GET['remap_layerzoom']) ? $_GET['remap_layerzoom'] : 'layerzoom');
	$remap_layerviewlat = isset($_POST['remap_layerviewlat']) ? $_POST['remap_layerviewlat'] : (isset($_GET['remap_layerviewlat']) ? $_GET['remap_layerviewlat'] : 'layerviewlat');
	$remap_layerviewlon = isset($_POST['remap_layerviewlon']) ? $_POST['remap_layerviewlon'] : (isset($_GET['remap_layerviewlon']) ? $_GET['remap_layerviewlon'] : 'layerviewlon');
	$remap_listmarkers = isset($_POST['remap_listmarkers']) ? $_POST['remap_listmarkers'] : (isset($_GET['remap_listmarkers']) ? $_GET['remap_listmarkers'] : 'listmarkers');
	$remap_multi_layer_map = isset($_POST['remap_multi_layer_map']) ? $_POST['remap_multi_layer_map'] : (isset($_GET['remap_multi_layer_map']) ? $_GET['remap_multi_layer_map'] : 'multi_layer_map');
	$remap_multi_layer_map_list = isset($_POST['remap_multi_layer_map_list']) ? $_POST['remap_multi_layer_map_list'] : (isset($_GET['remap_multi_layer_map_list']) ? $_GET['remap_multi_layer_map_list'] : 'multi_layer_map_list');
	$remap_address = isset($_POST['remap_address']) ? $_POST['remap_address'] : (isset($_GET['remap_address']) ? $_GET['remap_address'] : 'address');

	if ($lmm_options['api_status'] == 'enabled') {

		if ( (($request_method == 'GET') && ($lmm_options['api_request_type_get'] == TRUE)) || (($request_method == 'POST') && ($lmm_options['api_request_type_post'] == TRUE)) ) {

			if ( ($version == '1') || ($version == '') ) { //info: change OR condition if v2 is available

				$api_key = isset($_POST['key']) ? $_POST['key'] : (isset($_GET['key']) ? $_GET['key'] : '');
				if ($api_key == $lmm_options['api_key']) {
				
					if (lmm_check_signature()) { 

						$referer = wp_get_referer();
						if ( ($lmm_options['api_allowed_referer'] == NULL) || ( ($lmm_options['api_allowed_referer'] != NULL) && ($referer == $lmm_options['api_allowed_referer'])) ) {

							if ( ($lmm_options['api_allowed_ip'] == null) || (($lmm_options['api_allowed_ip'] != null) && (strpos ($_SERVER['REMOTE_ADDR'], str_replace("..",".",str_replace("...",".",str_replace("*", "", $lmm_options['api_allowed_ip'])))) === 0)) ) {
								$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
								$id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : '');
								$type = isset($_POST['type']) ? $_POST['type'] : (isset($_GET['type']) ? $_GET['type'] : '');

								if ($action == 'view') {
									if ( $lmm_options['api_permissions_view'] == TRUE ) {
										if ($type == 'marker') {
											$query_result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `id` = %d", $id), ARRAY_A);
											if (count($query_result) >= 1) {
												$mpopuptext = stripslashes(str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$query_result['popuptext'])));
												$address = stripslashes(str_replace('"', '\'', $query_result['address']));
												if ($format == 'json') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":true,'.PHP_EOL;
													echo '"message":"' . esc_attr__('API call was successful','lmm') . '",'.PHP_EOL;
													echo '"data": {'.PHP_EOL;
														echo '"' . $remap_id . '":"' . $query_result['id'] . '",'.PHP_EOL;
														echo '"' . $remap_markername . '":"' . stripslashes(esc_js($query_result['markername'])) . '",'.PHP_EOL;
														echo '"' . $remap_basemap . '":"' . $query_result['basemap'] . '",'.PHP_EOL;
														echo '"' . $remap_layer . '":"' . $query_result['layer'] . '",'.PHP_EOL;
														echo '"' . $remap_lat . '":"' . $query_result['lat'] . '",'.PHP_EOL;
														echo '"' . $remap_lon . '":"' . $query_result['lon'] . '",'.PHP_EOL;
														echo '"' . $remap_icon . '":"' . $query_result['icon'] . '",'.PHP_EOL;
														echo '"' . $remap_popuptext . '":"' . $mpopuptext . '",'.PHP_EOL;
														echo '"' . $remap_zoom . '":"' . $query_result['zoom'] . '",'.PHP_EOL;
														echo '"' . $remap_openpopup . '":"' . $query_result['openpopup'] . '",'.PHP_EOL;
														echo '"' . $remap_mapwidth . '":"' . $query_result['mapwidth'] . '",'.PHP_EOL;
														echo '"' . $remap_mapwidthunit . '":"' . $query_result['mapwidthunit'] . '",'.PHP_EOL;
														echo '"' . $remap_mapheight . '":"' . $query_result['mapheight'] . '",'.PHP_EOL;
														echo '"' . $remap_panel . '":"' . $query_result['panel'] . '",'.PHP_EOL;
														echo '"' . $remap_createdby . '":"' . $query_result['createdby'] . '",'.PHP_EOL;
														echo '"' . $remap_createdon . '":"' . $query_result['createdon'] . '",'.PHP_EOL;
														echo '"' . $remap_updatedby . '":"' . $query_result['updatedby'] . '",'.PHP_EOL;
														echo '"' . $remap_updatedon . '":"' . $query_result['updatedon'] . '",'.PHP_EOL;
														echo '"' . $remap_controlbox . '":"'.$query_result['controlbox'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom . '":"' . $query_result['overlays_custom'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom2 . '":"' . $query_result['overlays_custom2'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom3 . '":"' . $query_result['overlays_custom3'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom4 . '":"' . $query_result['overlays_custom4'] . '",'.PHP_EOL;
														echo '"' . $remap_wms . '":"' . $query_result['wms'] . '",'.PHP_EOL;
														echo '"' . $remap_wms2 . '":"' . $query_result['wms2'] . '",'.PHP_EOL;
														echo '"' . $remap_wms3 . '":"' . $query_result['wms3'] . '",'.PHP_EOL;
														echo '"' . $remap_wms4 . '":"' . $query_result['wms4'] . '",'.PHP_EOL;
														echo '"' . $remap_wms5 . '":"' . $query_result['wms5'] . '",'.PHP_EOL;
														echo '"' . $remap_wms6 . '":"' . $query_result['wms6'] . '",'.PHP_EOL;
														echo '"' . $remap_wms7 . '":"' . $query_result['wms7'] . '",'.PHP_EOL;
														echo '"' . $remap_wms8 . '":"' . $query_result['wms8'] . '",'.PHP_EOL;
														echo '"' . $remap_wms9 . '":"' . $query_result['wms9'] . '",'.PHP_EOL;
														echo '"' . $remap_wms10 . '":"' . $query_result['wms10'] . '",'.PHP_EOL;
														echo '"' . $remap_kml_timestamp . '":"' . $query_result['kml_timestamp'] . '",'.PHP_EOL;
														echo '"' . $remap_address . '":"' . $address . '"'.PHP_EOL;
														echo '}';
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<!DOCTYPE mapsmarker ['.PHP_EOL;
													echo '<!ELEMENT mapsmarker ((success, message, data))>'.PHP_EOL;
													echo '<!ATTLIST mapsmarker xmlns:xsi CDATA #FIXED "http://www.w3.org/2001/XMLSchema-instance" >'.PHP_EOL;
													echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layer . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . '))>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layer . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_lat . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_lon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_icon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_popuptext . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_zoom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_openpopup . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidth . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidthunit . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapheight . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_panel . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_controlbox . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms5 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms6 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms7 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms8 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms9 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms10 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_kml_timestamp . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_address . ' (#PCDATA)>'.PHP_EOL;
													echo ']>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>true</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('API call was successful','lmm') . '</message>'.PHP_EOL;
													echo '<data>'.PHP_EOL;
														echo '<' . $remap_id . '>' . $query_result['id'] . '</' . $remap_id . '>'.PHP_EOL;
														echo '<' . $remap_markername . '><![CDATA[' . stripslashes(esc_js($query_result['markername'])) . ']]></' . $remap_markername . '>'.PHP_EOL;
														echo '<' . $remap_basemap . '>' . $query_result['basemap'] . '</' . $remap_basemap . '>'.PHP_EOL;
														echo '<' . $remap_layer . '>' . $query_result['layer'] . '</' . $remap_layer . '>'.PHP_EOL;
														echo '<' . $remap_lat . '>' . $query_result['lat'] . '</' . $remap_lat . '>'.PHP_EOL;
														echo '<' . $remap_lon . '>' . $query_result['lon'] . '</' . $remap_lon . '>'.PHP_EOL;
														echo '<' . $remap_icon . '><![CDATA[' . $query_result['icon'] . ']]></' . $remap_icon . '>'.PHP_EOL;
														echo '<' . $remap_popuptext . '><![CDATA[' . $mpopuptext . ']]></' . $remap_popuptext . '>'.PHP_EOL;
														echo '<' . $remap_zoom . '>' . $query_result['zoom'] . '</' . $remap_zoom . '>'.PHP_EOL;
														echo '<' . $remap_openpopup . '>' . $query_result['openpopup'] . '</' . $remap_openpopup . '>'.PHP_EOL;
														echo '<' . $remap_mapwidth . '>' . $query_result['mapwidth'] . '</' . $remap_mapwidth . '>'.PHP_EOL;
														echo '<' . $remap_mapwidthunit . '>' . $query_result['mapwidthunit'] . '</' . $remap_mapwidthunit . '>'.PHP_EOL;
														echo '<' . $remap_mapheight . '>' . $query_result['mapheight'] . '</' . $remap_mapheight . '>'.PHP_EOL;
														echo '<' . $remap_panel . '>' . $query_result['panel'] . '</' . $remap_panel . '>'.PHP_EOL;
														echo '<' . $remap_createdby . '><![CDATA[' . $query_result['createdby'] . ']]></' . $remap_createdby . '>'.PHP_EOL;
														echo '<' . $remap_createdon . '>' . $query_result['createdon'] . '</' . $remap_createdon . '>'.PHP_EOL;
														echo '<' . $remap_updatedby . '><![CDATA[' . $query_result['updatedby'] . ']]></' . $remap_updatedby . '>'.PHP_EOL;
														echo '<' . $remap_updatedon . '>' . $query_result['updatedon'] . '</' . $remap_updatedon . '>'.PHP_EOL;
														echo '<' . $remap_controlbox . '>' . $query_result['controlbox'] . '</' . $remap_controlbox . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom . '>' . $query_result['overlays_custom'] . '</' . $remap_overlays_custom . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom2 . '>' . $query_result['overlays_custom2'] . '</' . $remap_overlays_custom2 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom3 . '>' . $query_result['overlays_custom3'] . '</' . $remap_overlays_custom3 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom4 . '>' . $query_result['overlays_custom4'] . '</' . $remap_overlays_custom4 . '>'.PHP_EOL;
														echo '<' . $remap_wms . '>' . $query_result['wms'] . '</' . $remap_wms . '>'.PHP_EOL;
														echo '<' . $remap_wms2 . '>' . $query_result['wms2'] . '</' . $remap_wms2 . '>'.PHP_EOL;
														echo '<' . $remap_wms3 . '>' . $query_result['wms3'] . '</' . $remap_wms3 . '>'.PHP_EOL;
														echo '<' . $remap_wms4 . '>' . $query_result['wms4'] . '</' . $remap_wms4 . '>'.PHP_EOL;
														echo '<' . $remap_wms5 . '>' . $query_result['wms5'] . '</' . $remap_wms5 . '>'.PHP_EOL;
														echo '<' . $remap_wms6 . '>' . $query_result['wms6'] . '</' . $remap_wms6 . '>'.PHP_EOL;
														echo '<' . $remap_wms7 . '>' . $query_result['wms7'] . '</' . $remap_wms7 . '>'.PHP_EOL;
														echo '<' . $remap_wms8 . '>' . $query_result['wms8'] . '</' . $remap_wms8 . '>'.PHP_EOL;
														echo '<' . $remap_wms9 . '>' . $query_result['wms9'] . '</' . $remap_wms9 . '>'.PHP_EOL;
														echo '<' . $remap_wms10 . '>' . $query_result['wms10'] . '</' . $remap_wms10 . '>'.PHP_EOL;
														echo '<' . $remap_kml_timestamp . '>' . $query_result['kml_timestamp'] . '</' . $remap_kml_timestamp . '>'.PHP_EOL;
														echo '<' . $remap_address . '><![CDATA[' . $address . ']]></' . $remap_address . '>'.PHP_EOL;
													echo '</data>'.PHP_EOL;
													echo '</mapsmarker>';
												} //info: end format marker / view
											} else if ($id == null) {
												if ($format == 'json') {
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":false,'.PHP_EOL;
													echo '"message":"' . esc_attr__('API parameter id has to be set','lmm') . '",'.PHP_EOL;
													echo '"data": { }'.PHP_EOL;
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>false</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('API parameter id has to be set','lmm') . '</message>'.PHP_EOL;
													echo '<data></data>'.PHP_EOL;
													echo '</mapsmarker>';
												}
											} else {
												if ($format == 'json') {
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":false,'.PHP_EOL;
													echo '"message":"' . sprintf(esc_attr__('A marker with the ID %1s does not exist','lmm'), $id) . '",'.PHP_EOL;
													echo '"data": { }'.PHP_EOL;
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>false</success>'.PHP_EOL;
													echo '<message>' . sprintf(esc_attr__('A marker with the ID %1s does not exist','lmm'), $id) . '</message>'.PHP_EOL;
													echo '<data></data>'.PHP_EOL;
													echo '</mapsmarker>';
												}
											} //info: end check if query_result markers >=1 / view
										} else if ($type == 'layer') {
											$query_result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE `id` = %d", $id), ARRAY_A);
											if (count($query_result) >= 1) {
												if ($format == 'json') {
													$address = stripslashes(str_replace('"', '\'', $query_result['address']));
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":true,'.PHP_EOL;
													echo '"message":"' . esc_attr__('API call was successful','lmm') . '",'.PHP_EOL;
													echo '"data": {'.PHP_EOL;
														echo '"' . $remap_id . '":"' . $query_result['id'] . '",'.PHP_EOL;
														echo '"' . $remap_name . '":"' . stripslashes($query_result['name']) . '",'.PHP_EOL;
														echo '"' . $remap_basemap . '":"' . $query_result['basemap'] . '",'.PHP_EOL;
														echo '"' . $remap_layerzoom . '":"' . $query_result['layerzoom'] . '",'.PHP_EOL;
														echo '"' . $remap_mapwidth . '":"' . $query_result['mapwidth'] . '",'.PHP_EOL;
														echo '"' . $remap_mapwidthunit . '":"' . $query_result['mapwidthunit'] . '",'.PHP_EOL;
														echo '"' . $remap_mapheight . '":"' . $query_result['mapheight'] . '",'.PHP_EOL;
														echo '"' . $remap_panel . '":"' . $query_result['panel'] . '",'.PHP_EOL;
														echo '"' . $remap_layerviewlat . '":"' . $query_result['layerviewlat'] . '",'.PHP_EOL;
														echo '"' . $remap_layerviewlon . '":"' . $query_result['layerviewlon'] . '",'.PHP_EOL;
														echo '"' . $remap_createdby . '":"' . $query_result['createdby'] . '",'.PHP_EOL;
														echo '"' . $remap_createdon . '":"' . $query_result['createdon'] . '",'.PHP_EOL;
														echo '"' . $remap_updatedby . '":"' . $query_result['updatedby'] . '",'.PHP_EOL;
														echo '"' . $remap_updatedon . '":"' . $query_result['updatedon'] . '",'.PHP_EOL;
														echo '"' . $remap_controlbox . '":"' . $query_result['controlbox'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom . '":"' . $query_result['overlays_custom'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom2 . '":"' . $query_result['overlays_custom2'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom3 . '":"' . $query_result['overlays_custom3'] . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom4 . '":"' . $query_result['overlays_custom4'] . '",'.PHP_EOL;
														echo '"' . $remap_wms . '":"' . $query_result['wms'] . '",'.PHP_EOL;
														echo '"' . $remap_wms2 . '":"' . $query_result['wms2'] . '",'.PHP_EOL;
														echo '"' . $remap_wms3 . '":"' . $query_result['wms3'] . '",'.PHP_EOL;
														echo '"' . $remap_wms4 . '":"' . $query_result['wms4'] . '",'.PHP_EOL;
														echo '"' . $remap_wms5 . '":"' . $query_result['wms5'] . '",'.PHP_EOL;
														echo '"' . $remap_wms6 . '":"' . $query_result['wms6'] . '",'.PHP_EOL;
														echo '"' . $remap_wms7 . '":"' . $query_result['wms7'] . '",'.PHP_EOL;
														echo '"' . $remap_wms8 . '":"' . $query_result['wms8'] . '",'.PHP_EOL;
														echo '"' . $remap_wms9 . '":"' . $query_result['wms9'] . '",'.PHP_EOL;
														echo '"' . $remap_wms10 . '":"' . $query_result['wms10'] . '",'.PHP_EOL;
														echo '"' . $remap_listmarkers . '":"' . $query_result['listmarkers'] . '",'.PHP_EOL;
														echo '"' . $remap_multi_layer_map . '":"' . $query_result['multi_layer_map'] . '",'.PHP_EOL;
														echo '"' . $remap_multi_layer_map_list . '":"' . $query_result['multi_layer_map_list'] . '",'.PHP_EOL;
														echo '"' . $remap_address . '":"' . $address . '"'.PHP_EOL;
														echo '}';
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<!DOCTYPE mapsmarker ['.PHP_EOL;
													echo '<!ELEMENT mapsmarker ((success, message, data))>'.PHP_EOL;
													echo '<!ATTLIST mapsmarker xmlns:xsi CDATA #FIXED "http://www.w3.org/2001/XMLSchema-instance" >'.PHP_EOL;
													echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . '))>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_name . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layerzoom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidth . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidthunit . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapheight . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_panel . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layerviewlat . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layerviewlon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_controlbox . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms5 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms6 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms7 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms8 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms9 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms10 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_listmarkers . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_multi_layer_map . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_multi_layer_map_list . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_address . ' (#PCDATA)>'.PHP_EOL;
													echo ']>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>true</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('API call was successful','lmm') . '</message>'.PHP_EOL;
													echo '<data>'.PHP_EOL;
														echo '<' . $remap_id . '>' . $query_result['id'] . '</' . $remap_id . '>'.PHP_EOL;
														echo '<' . $remap_name . '><![CDATA[' . stripslashes($query_result['name']) . ']]></' . $remap_name . '>'.PHP_EOL;
														echo '<' . $remap_basemap . '>'.$query_result['basemap'] . '</' . $remap_basemap . '>'.PHP_EOL;
														echo '<' . $remap_layerzoom . '>' . $query_result['layerzoom'] . '</' . $remap_layerzoom . '>'.PHP_EOL;
														echo '<' . $remap_mapwidth . '>' . $query_result['mapwidth'] . '</' . $remap_mapwidth . '>'.PHP_EOL;
														echo '<' . $remap_mapwidthunit . '>' . $query_result['mapwidthunit'] . '</' . $remap_mapwidthunit . '>'.PHP_EOL;
														echo '<' . $remap_mapheight . '>' . $query_result['mapheight'] . '</' . $remap_mapheight . '>'.PHP_EOL;
														echo '<' . $remap_panel . '>' . $query_result['panel'] . '</' . $remap_panel . '>'.PHP_EOL;
														echo '<' . $remap_layerviewlat . '>' . $query_result['layerviewlat'] . '</' . $remap_layerviewlat . '>'.PHP_EOL;
														echo '<' . $remap_layerviewlon . '>' . $query_result['layerviewlon'] . '</' . $remap_layerviewlon . '>'.PHP_EOL;
														echo '<' . $remap_createdby . '><![CDATA[' . $query_result['createdby'] . ']]></' . $remap_createdby . '>'.PHP_EOL;
														echo '<' . $remap_createdon . '>' . $query_result['createdon'] . '</' . $remap_createdon . '>'.PHP_EOL;
														echo '<' . $remap_updatedby . '><![CDATA[' . $query_result['updatedby'] . ']]></' . $remap_updatedby . '>'.PHP_EOL;
														echo '<' . $remap_updatedon . '>' . $query_result['updatedon'] . '</' . $remap_updatedon . '>'.PHP_EOL;
														echo '<' . $remap_controlbox . '>' . $query_result['controlbox'] . '</' . $remap_controlbox . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom . '>' . $query_result['overlays_custom'] . '</' . $remap_overlays_custom . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom2 . '>' . $query_result['overlays_custom2'] . '</' . $remap_overlays_custom2 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom3 . '>' . $query_result['overlays_custom3'] . '</' . $remap_overlays_custom3 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom4 . '>' . $query_result['overlays_custom4'] . '</' . $remap_overlays_custom4 . '>'.PHP_EOL;
														echo '<' . $remap_wms . '>' . $query_result['wms'] . '</' . $remap_wms . '>'.PHP_EOL;
														echo '<' . $remap_wms2 . '>' . $query_result['wms2'] . '</' . $remap_wms2 . '>'.PHP_EOL;
														echo '<' . $remap_wms3 . '>' . $query_result['wms3'] . '</' . $remap_wms3 . '>'.PHP_EOL;
														echo '<' . $remap_wms4 . '>' . $query_result['wms4'] . '</' . $remap_wms4 . '>'.PHP_EOL;
														echo '<' . $remap_wms5 . '>' . $query_result['wms5'] . '</' . $remap_wms5 . '>'.PHP_EOL;
														echo '<' . $remap_wms6 . '>' . $query_result['wms6'] . '</' . $remap_wms6 . '>'.PHP_EOL;
														echo '<' . $remap_wms7 . '>' . $query_result['wms7'] . '</' . $remap_wms7 . '>'.PHP_EOL;
														echo '<' . $remap_wms8 . '>' . $query_result['wms8'] . '</' . $remap_wms8 . '>'.PHP_EOL;
														echo '<' . $remap_wms9 . '>' . $query_result['wms9'] . '</' . $remap_wms9 . '>'.PHP_EOL;
														echo '<' . $remap_wms10 . '>' . $query_result['wms10'] . '</' . $remap_wms10 . '>'.PHP_EOL;
														echo '<' . $remap_listmarkers . '>' . $query_result['listmarkers'] . '</' . $remap_listmarkers . '>'.PHP_EOL;
														echo '<' . $remap_multi_layer_map . '>' . $query_result['multi_layer_map'] . '</' . $remap_multi_layer_map . '>'.PHP_EOL;
														echo '<' . $remap_multi_layer_map_list . '>' . $query_result['multi_layer_map_list'] . '</' . $remap_multi_layer_map_list . '>'.PHP_EOL;
														echo '<' . $remap_address . '>' . $query_result['address'] . '</' . $remap_address . '>'.PHP_EOL;
													echo '</data>'.PHP_EOL;
													echo '</mapsmarker>';
												} //info: end format layer / view
											} else {
												if ($format == 'json') {
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":false,'.PHP_EOL;
													echo '"message":"' . sprintf(esc_attr__('A layer with the ID %1s does not exist','lmm'), $id) . '",'.PHP_EOL;
													echo '"data": { }'.PHP_EOL;
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>false</success>'.PHP_EOL;
													echo '<message>' . sprintf(esc_attr__('A layer with the ID %1s does not exist','lmm'), $id) . '</message>'.PHP_EOL;
													echo '<data></data>'.PHP_EOL;
													echo '</mapsmarker>';
												}
											} //info: end check if query_result layers >=1 / view
										} else if ($type == '') {
											if ($format == 'json') {
												header('Content-type: application/json; charset=utf-8');
												if ($callback != NULL) { echo $callback . '('; }
												echo '{'.PHP_EOL;
												echo '"success":false,'.PHP_EOL;
												echo '"message":"' . esc_attr__('API parameter type has to be set','lmm') . '",'.PHP_EOL;
												echo '"data": { }'.PHP_EOL;
												echo '}';
												if ($callback != NULL) { echo ');'; }
											} else if ($format == 'xml') {
												header('Content-type: application/xml; charset=utf-8');
												echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
												echo '<mapsmarker>'.PHP_EOL;
												echo '<success>false</success>'.PHP_EOL;
												echo '<message>' . esc_attr__('API parameter type has to be set','lmm') . '</message>'.PHP_EOL;
												echo '<data></data>'.PHP_EOL;
												echo '</mapsmarker>';
											}
										} else {
											if ($format == 'json') {
												header('Content-type: application/json; charset=utf-8');
												if ($callback != NULL) { echo $callback . '('; }
												echo '{'.PHP_EOL;
												echo '"success":false,'.PHP_EOL;
												echo '"message":"' . esc_attr__('API parameter type is invalid','lmm') . '",'.PHP_EOL;
												echo '"data": { }'.PHP_EOL;
												echo '}';
												if ($callback != NULL) { echo ');'; }
											} else if ($format == 'xml') {
												header('Content-type: application/xml; charset=utf-8');
												echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
												echo '<mapsmarker>'.PHP_EOL;
												echo '<success>false</success>'.PHP_EOL;
												echo '<message>' . esc_attr__('API parameter type is invalid','lmm') . '</message>'.PHP_EOL;
												echo '<data></data>'.PHP_EOL;
												echo '</mapsmarker>';
											}
										} //info: end type check / view
									} else {
										if ($format == 'json') {
											header('Content-type: application/json; charset=utf-8');
											if ($callback != NULL) { echo $callback . '('; }
											echo '{'.PHP_EOL;
											echo '"success":false,'.PHP_EOL;
											echo '"message":"' . esc_attr__('API action is not allowed','lmm') . '",'.PHP_EOL;
											echo '"data": { }'.PHP_EOL;
											echo '}';
											if ($callback != NULL) { echo ');'; }
										} else if ($format == 'xml') {
											header('Content-type: application/xml; charset=utf-8');
											echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
											echo '<mapsmarker>'.PHP_EOL;
											echo '<success>false</success>'.PHP_EOL;
											echo '<message>' . esc_attr__('API action is not allowed','lmm') . '</message>'.PHP_EOL;
											echo '<data></data>'.PHP_EOL;
											echo '</mapsmarker>';
										}
									} //info: end permission check / view
								/******************************
								* action add                  *
								******************************/
								} else if ($action == 'add') {
									if ( $lmm_options['api_permissions_add'] == TRUE ) {
										if ($type == 'marker') {
											$markername = isset($_POST['markername']) ? $_POST['markername'] : (isset($_GET['markername']) ? $_GET['markername'] : '');
											$markername_quotes = str_replace("\\\\","/", str_replace("\"", "'", $markername)); //info: backslash breaks GeoJSON
											$mpopuptext = isset($_POST['popuptext']) ? str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$_POST['popuptext'])) : (isset($_GET['popuptext']) ? str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$_GET['popuptext'])) : '');

											$basemap = isset($_POST['basemap']) && in_array($_POST['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_POST['basemap'] : (isset($_GET['basemap']) && in_array($_GET['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_GET['basemap'] : $lmm_options[ 'standard_basemap' ]);
											$layer = isset($_POST['layer']) ? intval($_POST['layer']) : (isset($_GET['layer']) ? intval($_GET['layer']) : (($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '0' : intval($lmm_options[ 'defaults_marker_default_layer' ])));
											$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : (isset($_GET['lat']) ? floatval($_GET['lat']) : str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lat' ])));
											$lon = isset($_POST['lon']) ? floatval($_POST['lon']) : (isset($_GET['lon']) ? floatval($_GET['lon']) : str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lon' ])));
											$icon = isset($_POST['icon']) ? $_POST['icon'] : (isset($_GET['icon']) ? $_GET['icon'] : (($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : esc_html($lmm_options[ 'defaults_marker_icon' ])));
											$popuptext = $mpopuptext;
											$zoom = isset($_POST['zoom']) ? intval($_POST['zoom']) : (isset($_GET['zoom']) ? intval($_GET['zoom']) : intval($lmm_options[ 'defaults_marker_zoom' ]));
											$openpopup = ( isset($_POST['openpopup']) && ( ($_POST['openpopup'] == '0') || ($_POST['openpopup'] == '1')) ) ? $_POST['openpopup'] : ( isset($_GET['openpopup']) && ( ($_GET['openpopup'] == '0') || ($_GET['openpopup'] == '1') ) ? $_GET['openpopup'] : $lmm_options[ 'defaults_marker_openpopup' ]);
											$mapwidth = isset($_POST['mapwidth']) ? $_POST['mapwidth'] : (isset($_GET['mapwidth']) ? $_GET['mapwidth'] : intval($lmm_options[ 'defaults_marker_mapwidth' ]));
											$mapwidthunit = ( isset($_POST['mapwidthunit']) && ( ($_POST['mapwidthunit'] == 'px') || ($_POST['mapwidthunit'] == '%') ) ) ? $_POST['mapwidthunit'] : ( isset($_GET['mapwidthunit']) && ( ($_GET['mapwidthunit'] == 'px') || ($_GET['mapwidthunit'] == '%')  ) ? $_GET['mapwidthunit'] : $lmm_options[ 'defaults_marker_mapwidthunit' ]);
											$mapheight = isset($_POST['mapheight']) ? $_POST['mapheight'] : (isset($_GET['mapheight']) ? $_GET['mapheight'] : intval($lmm_options[ 'defaults_marker_mapheight' ]));
											$panel = ( isset($_POST['panel']) && ( ($_POST['panel'] == '0') || ($_POST['panel'] == '1')) ) ? $_POST['panel'] : ( isset($_GET['panel']) && ( ($_GET['panel'] == '0') || ($_GET['panel'] == '1') ) ? $_GET['panel'] : $lmm_options[ 'defaults_marker_panel' ]);
											$createdby = isset($_POST['createdby']) ? esc_html($_POST['createdby']) : (isset($_GET['createdby']) ? esc_html($_GET['createdby']) : 'MapsMarker API');
											$createdon = isset($_POST['createdon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['createdon'] : (isset($_GET['createdon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['createdon'] : current_time('mysql',0));
											$updatedby = isset($_POST['updatedby']) ? esc_html($_POST['updatedby']) : (isset($_GET['updatedby']) ? esc_html($_GET['updatedby']) : 'MapsMarker API');
											$updatedon = isset($_POST['updatedon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['updatedon'] : (isset($_GET['updatedon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['updatedon'] : current_time('mysql',0));
											$controlbox = ( isset($_POST['controlbox']) && ( ($_POST['controlbox'] == '0') || ($_POST['controlbox'] == '1') || ($_POST['controlbox'] == '2')) ) ? $_POST['controlbox'] : ( isset($_GET['controlbox']) && ( ($_GET['controlbox'] == '0') || ($_GET['controlbox'] == '1') || ($_GET['controlbox'] == '2') ) ? $_GET['controlbox'] : $lmm_options[ 'defaults_marker_controlbox' ]);
											$overlays_custom = ( isset($_POST['overlays_custom']) && ( ($_POST['overlays_custom'] == '0') || ($_POST['overlays_custom'] == '1')) ) ? $_POST['overlays_custom'] : ( isset($_GET['overlays_custom']) && ( ($_GET['overlays_custom'] == '0') || ($_GET['overlays_custom'] == '1') ) ? $_GET['overlays_custom'] : (isset($lmm_options[ 'defaults_marker_overlays_custom_active' ]) ? '1' : '0'));
											$overlays_custom2 = ( isset($_POST['overlays_custom2']) && ( ($_POST['overlays_custom2'] == '0') || ($_POST['overlays_custom2'] == '1')) ) ? $_POST['overlays_custom2'] : ( isset($_GET['overlays_custom2']) && ( ($_GET['overlays_custom2'] == '0') || ($_GET['overlays_custom2'] == '1') ) ? $_GET['overlays_custom2'] : (isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ]) ? '1' : '0'));
											$overlays_custom3 = ( isset($_POST['overlays_custom3']) && ( ($_POST['overlays_custom3'] == '0') || ($_POST['overlays_custom3'] == '1')) ) ? $_POST['overlays_custom3'] : ( isset($_GET['overlays_custom3']) && ( ($_GET['overlays_custom3'] == '0') || ($_GET['overlays_custom3'] == '1') ) ? $_GET['overlays_custom3'] : (isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ]) ? '1' : '0'));
											$overlays_custom4 = ( isset($_POST['overlays_custom4']) && ( ($_POST['overlays_custom4'] == '0') || ($_POST['overlays_custom4'] == '1')) ) ? $_POST['overlays_custom4'] : ( isset($_GET['overlays_custom4']) && ( ($_GET['overlays_custom4'] == '0') || ($_GET['overlays_custom4'] == '1') ) ? $_GET['overlays_custom4'] : (isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ]) ? '1' : '0'));
											$wms = ( isset($_POST['wms']) && ( ($_POST['wms'] == '0') || ($_POST['wms'] == '1')) ) ? $_POST['wms'] : ( isset($_GET['wms']) && ( ($_GET['wms'] == '0') || ($_GET['wms'] == '1') ) ? $_GET['wms'] : (isset($lmm_options[ 'defaults_marker_wms_active' ]) ? '1' : '0'));
											$wms2 = ( isset($_POST['wms2']) && ( ($_POST['wms2'] == '0') || ($_POST['wms2'] == '1')) ) ? $_POST['wms2'] : ( isset($_GET['wms2']) && ( ($_GET['wms2'] == '0') || ($_GET['wms2'] == '1') ) ? $_GET['wms2'] : (isset($lmm_options[ 'defaults_marker_wms2_active' ]) ? '1' : '0'));
											$wms3 = ( isset($_POST['wms3']) && ( ($_POST['wms3'] == '0') || ($_POST['wms3'] == '1')) ) ? $_POST['wms3'] : ( isset($_GET['wms3']) && ( ($_GET['wms3'] == '0') || ($_GET['wms3'] == '1') ) ? $_GET['wms3'] : (isset($lmm_options[ 'defaults_marker_wms3_active' ]) ? '1' : '0'));
											$wms4 = ( isset($_POST['wms4']) && ( ($_POST['wms4'] == '0') || ($_POST['wms4'] == '1')) ) ? $_POST['wms4'] : ( isset($_GET['wms4']) && ( ($_GET['wms4'] == '0') || ($_GET['wms4'] == '1') ) ? $_GET['wms4'] : (isset($lmm_options[ 'defaults_marker_wms4_active' ]) ? '1' : '0'));
											$wms5 = ( isset($_POST['wms5']) && ( ($_POST['wms5'] == '0') || ($_POST['wms5'] == '1')) ) ? $_POST['wms5'] : ( isset($_GET['wms5']) && ( ($_GET['wms5'] == '0') || ($_GET['wms5'] == '1') ) ? $_GET['wms5'] : (isset($lmm_options[ 'defaults_marker_wms5_active' ]) ? '1' : '0'));
											$wms6 = ( isset($_POST['wms6']) && ( ($_POST['wms6'] == '0') || ($_POST['wms6'] == '1')) ) ? $_POST['wms6'] : ( isset($_GET['wms6']) && ( ($_GET['wms6'] == '0') || ($_GET['wms6'] == '1') ) ? $_GET['wms6'] : (isset($lmm_options[ 'defaults_marker_wms6_active' ]) ? '1' : '0'));
											$wms7 = ( isset($_POST['wms7']) && ( ($_POST['wms7'] == '0') || ($_POST['wms7'] == '1')) ) ? $_POST['wms7'] : ( isset($_GET['wms7']) && ( ($_GET['wms7'] == '0') || ($_GET['wms7'] == '1') ) ? $_GET['wms7'] : (isset($lmm_options[ 'defaults_marker_wms7_active' ]) ? '1' : '0'));
											$wms8 = ( isset($_POST['wms8']) && ( ($_POST['wms8'] == '0') || ($_POST['wms8'] == '1')) ) ? $_POST['wms8'] : ( isset($_GET['wms8']) && ( ($_GET['wms8'] == '0') || ($_GET['wms8'] == '1') ) ? $_GET['wms8'] : (isset($lmm_options[ 'defaults_marker_wms8_active' ]) ? '1' : '0'));
											$wms9 = ( isset($_POST['wms9']) && ( ($_POST['wms9'] == '0') || ($_POST['wms9'] == '1')) ) ? $_POST['wms9'] : ( isset($_GET['wms9']) && ( ($_GET['wms9'] == '0') || ($_GET['wms9'] == '1') ) ? $_GET['wms9'] : (isset($lmm_options[ 'defaults_marker_wms9_active' ]) ? '1' : '0'));
											$wms10 = ( isset($_POST['wms10']) && ( ($_POST['wms10'] == '0') || ($_POST['wms10'] == '1')) ) ? $_POST['wms10'] : ( isset($_GET['wms10']) && ( ($_GET['wms10'] == '0') || ($_GET['wms10'] == '1') ) ? $_GET['wms10'] : (isset($lmm_options[ 'defaults_marker_wms10_active' ]) ? '1' : '0'));
											$kml_timestamp = isset($_POST['kml_timestamp']) && ( $_POST['kml_timestamp'] == date('Y-m-d H:i:s',strtotime($_POST['kml_timestamp'])) ) ? $_POST['kml_timestamp'] : (isset($_GET['kml_timestamp']) && ( $_GET['kml_timestamp'] == date('Y-m-d H:i:s',strtotime($_GET['kml_timestamp'])) ) ? $_GET['kml_timestamp'] : '');
											$address = isset($_POST['address']) ? $_POST['address'] : (isset($_GET['address']) ? $_GET['address'] : '');
											$gpx_url = ''; //info: added for compat
											$gpx_panel = '0'; //info: added for compat
											if ($geocode != NULL) {
												$do_geocoding = MMP_Geocoding::getLatLng($geocode);
												if ($do_geocoding['success'] == true) {
													$lat = $do_geocoding['lat'];
													$lon = $do_geocoding['lon'];
													$address = $do_geocoding['address'];
												} else {
													if ($format == 'json') {
														header('Content-type: application/json; charset=utf-8');
														if ($callback != NULL) { echo $callback . '('; }
														echo '{'.PHP_EOL;
														echo '"success":false,'.PHP_EOL;
														echo '"message":"' . sprintf(esc_attr__('Geocoding error: %1s','lmm'), $do_geocoding['message']) . '",'.PHP_EOL;
														echo '"data": { }'.PHP_EOL;
														echo '}';
														if ($callback != NULL) { echo ');'; }
													} else if ($format == 'xml') {
														header('Content-type: application/xml; charset=utf-8');
														echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
														echo '<mapsmarker>'.PHP_EOL;
														echo '<success>false</success>'.PHP_EOL;
														echo '<message>' . sprintf(esc_attr__('Geocoding error: %1s','lmm'), $do_geocoding['message']) . '</message>'.PHP_EOL;
														echo '<data></data>'.PHP_EOL;
														echo '</mapsmarker>';
													}
													exit();
												}
											}
											if ($kml_timestamp == NULL) {
												$query_add = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel );
											} else {
												$query_add = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel );
											}
											$result_add = $wpdb->query( $query_add );
											if ($result_add == TRUE) {
												if ($format == 'json') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":true,'.PHP_EOL;
													echo '"message":"' . esc_attr__('Marker has been successfully published','lmm') . '",'.PHP_EOL;
													echo '"data": {'.PHP_EOL;
														echo '"' . $remap_id . '":"'. $wpdb->insert_id . '",'.PHP_EOL;
														echo '"' . $remap_markername . '":"' . stripslashes($markername_quotes) . '",'.PHP_EOL;
														echo '"' . $remap_basemap . '":"' . $basemap . '",'.PHP_EOL;
														echo '"' . $remap_layer . '":"' . $layer . '",'.PHP_EOL;
														echo '"' . $remap_lat . '":"' . $lat . '",'.PHP_EOL;
														echo '"' . $remap_lon . '":"' . $lon . '",'.PHP_EOL;
														echo '"' . $remap_icon . '":"' . $icon . '",'.PHP_EOL;
														echo '"' . $remap_popuptext . '":"' . stripslashes($popuptext) . '",'.PHP_EOL;
														echo '"' . $remap_zoom . '":"' . $zoom . '",'.PHP_EOL;
														echo '"' . $remap_openpopup . '":"' . $openpopup . '",'.PHP_EOL;
														echo '"' . $remap_mapwidth . '":"' . $mapwidth . '",'.PHP_EOL;
														echo '"' . $remap_mapwidthunit . '":"' . $mapwidthunit . '",'.PHP_EOL;
														echo '"' . $remap_mapheight . '":"' . $mapheight . '",'.PHP_EOL;
														echo '"' . $remap_panel . '":"' . $panel . '",'.PHP_EOL;
														echo '"' . $remap_createdby . '":"' . $createdby . '",'.PHP_EOL;
														echo '"' . $remap_createdon . '":"' . $createdon . '",'.PHP_EOL;
														echo '"' . $remap_updatedby . '":"' . $updatedby . '",'.PHP_EOL;
														echo '"' . $remap_updatedon . '":"' . $updatedon . '",'.PHP_EOL;
														echo '"' . $remap_controlbox . '":"'.$controlbox . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom . '":"' . $overlays_custom . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom2 . '":"' . $overlays_custom2 . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom3 . '":"' . $overlays_custom3 . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom4 . '":"' . $overlays_custom4 . '",'.PHP_EOL;
														echo '"' . $remap_wms . '":"' . $wms . '",'.PHP_EOL;
														echo '"' . $remap_wms2 . '":"' . $wms2 . '",'.PHP_EOL;
														echo '"' . $remap_wms3 . '":"' . $wms3 . '",'.PHP_EOL;
														echo '"' . $remap_wms4 . '":"' . $wms4 . '",'.PHP_EOL;
														echo '"' . $remap_wms5 . '":"' . $wms5 . '",'.PHP_EOL;
														echo '"' . $remap_wms6 . '":"' . $wms6 . '",'.PHP_EOL;
														echo '"' . $remap_wms7 . '":"' . $wms7 . '",'.PHP_EOL;
														echo '"' . $remap_wms8 . '":"' . $wms8 . '",'.PHP_EOL;
														echo '"' . $remap_wms9 . '":"' . $wms9 . '",'.PHP_EOL;
														echo '"' . $remap_wms10 . '":"' . $wms10 . '",'.PHP_EOL;
														echo '"' . $remap_kml_timestamp . '":"' . $kml_timestamp . '",'.PHP_EOL;
														echo '"' . $remap_address . '":"' . $address . '"'.PHP_EOL;
														echo '}';
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<!DOCTYPE mapsmarker ['.PHP_EOL;
													echo '<!ELEMENT mapsmarker ((success, message, data))>'.PHP_EOL;
													echo '<!ATTLIST mapsmarker xmlns:xsi CDATA #FIXED "http://www.w3.org/2001/XMLSchema-instance" >'.PHP_EOL;
													echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layer . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . '))>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layer . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_lat . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_lon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_icon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_popuptext . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_zoom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_openpopup . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidth . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidthunit . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapheight . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_panel . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_controlbox . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms5 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms6 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms7 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms8 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms9 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms10 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_kml_timestamp . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_address . ' (#PCDATA)>'.PHP_EOL;
													echo ']>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>true</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('Marker has been successfully published','lmm') . '</message>'.PHP_EOL;
													echo '<data>'.PHP_EOL;
														echo '<' . $remap_id . '>' . $wpdb->insert_id . '</' . $remap_id . '>'.PHP_EOL;
														echo '<' . $remap_markername . '><![CDATA[' . stripslashes($markername_quotes) . ']]></' . $remap_markername . '>'.PHP_EOL;
														echo '<' . $remap_basemap . '>' . $basemap . '</' . $remap_basemap . '>'.PHP_EOL;
														echo '<' . $remap_layer . '>' . $layer . '</' . $remap_layer . '>'.PHP_EOL;
														echo '<' . $remap_lat . '>' . $lat . '</' . $remap_lat . '>'.PHP_EOL;
														echo '<' . $remap_lon . '>' . $lon . '</' . $remap_lon . '>'.PHP_EOL;
														echo '<' . $remap_icon . '><![CDATA[' . $icon . ']]></' . $remap_icon . '>'.PHP_EOL;
														echo '<' . $remap_popuptext . '><![CDATA[' . stripslashes($popuptext) . ']]></' . $remap_popuptext . '>'.PHP_EOL;
														echo '<' . $remap_zoom . '>' . $zoom . '</' . $remap_zoom . '>'.PHP_EOL;
														echo '<' . $remap_openpopup . '>' . $openpopup . '</' . $remap_openpopup . '>'.PHP_EOL;
														echo '<' . $remap_mapwidth . '>' . $mapwidth . '</' . $remap_mapwidth . '>'.PHP_EOL;
														echo '<' . $remap_mapwidthunit . '>' . $mapwidthunit . '</' . $remap_mapwidthunit . '>'.PHP_EOL;
														echo '<' . $remap_mapheight . '>' . $mapheight . '</' . $remap_mapheight . '>'.PHP_EOL;
														echo '<' . $remap_panel . '>' . $panel . '</' . $remap_panel . '>'.PHP_EOL;
														echo '<' . $remap_createdby . '><![CDATA[' . $createdby . ']]></' . $remap_createdby . '>'.PHP_EOL;
														echo '<' . $remap_createdon . '>' . $createdon . '</' . $remap_createdon . '>'.PHP_EOL;
														echo '<' . $remap_updatedby . '><![CDATA[' . $updatedby . ']]></' . $remap_updatedby . '>'.PHP_EOL;
														echo '<' . $remap_updatedon . '>' . $updatedon . '</' . $remap_updatedon . '>'.PHP_EOL;
														echo '<' . $remap_controlbox . '>' . $controlbox . '</' . $remap_controlbox . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom . '>' . $overlays_custom . '</' . $remap_overlays_custom . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom2 . '>' . $overlays_custom2 . '</' . $remap_overlays_custom2 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom3 . '>' . $overlays_custom3 . '</' . $remap_overlays_custom3 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom4 . '>' . $overlays_custom4 . '</' . $remap_overlays_custom4 . '>'.PHP_EOL;
														echo '<' . $remap_wms . '>' . $wms . '</' . $remap_wms . '>'.PHP_EOL;
														echo '<' . $remap_wms2 . '>' . $wms2 . '</' . $remap_wms2 . '>'.PHP_EOL;
														echo '<' . $remap_wms3 . '>' . $wms3 . '</' . $remap_wms3 . '>'.PHP_EOL;
														echo '<' . $remap_wms4 . '>' . $wms4 . '</' . $remap_wms4 . '>'.PHP_EOL;
														echo '<' . $remap_wms5 . '>' . $wms5 . '</' . $remap_wms5 . '>'.PHP_EOL;
														echo '<' . $remap_wms6 . '>' . $wms6 . '</' . $remap_wms6 . '>'.PHP_EOL;
														echo '<' . $remap_wms7 . '>' . $wms7 . '</' . $remap_wms7 . '>'.PHP_EOL;
														echo '<' . $remap_wms8 . '>' . $wms8 . '</' . $remap_wms8 . '>'.PHP_EOL;
														echo '<' . $remap_wms9 . '>' . $wms9 . '</' . $remap_wms9 . '>'.PHP_EOL;
														echo '<' . $remap_wms10 . '>' . $wms10 . '</' . $remap_wms10 . '>'.PHP_EOL;
														echo '<' . $remap_kml_timestamp . '>' . $kml_timestamp . '</' . $remap_kml_timestamp . '>'.PHP_EOL;
														echo '<' . $remap_address . '><![CDATA[' . $address . ']]></' . $remap_address . '>'.PHP_EOL;
													echo '</data>'.PHP_EOL;
													echo '</mapsmarker>';
												} //info: end format marker / add
											} else {
												if ($format == 'json') {
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":false,'.PHP_EOL;
													echo '"message":"' . esc_attr__('You have an error in your SQL syntax','lmm') . '",'.PHP_EOL;
													echo '"data": { }'.PHP_EOL;
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>false</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('You have an error in your SQL syntax','lmm') . '</message>'.PHP_EOL;
													echo '<data></data>'.PHP_EOL;
													echo '</mapsmarker>';
												} //info: end query check marker ok / add
											} //info: end add marker
										} else if ($type == 'layer') {
											$name = isset($_POST['name']) ? $_POST['name'] : (isset($_GET['name']) ? $_GET['name'] : '');
											$name_quotes = str_replace("\\\\", "/", str_replace("\"", "'", $name));
											$basemap = isset($_POST['basemap']) && in_array($_POST['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_POST['basemap'] : (isset($_GET['basemap']) && in_array($_GET['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_GET['basemap'] : $lmm_options[ 'standard_basemap' ]);
											$layerzoom = isset($_POST['layerzoom']) ? intval($_POST['layerzoom']) : (isset($_GET['layerzoom']) ? intval($_GET['layerzoom']) : intval($lmm_options[ 'defaults_layer_zoom' ]));
											$mapwidth = isset($_POST['mapwidth']) ? $_POST['mapwidth'] : (isset($_GET['mapwidth']) ? $_GET['mapwidth'] : intval($lmm_options[ 'defaults_layer_mapwidth' ]));
											$mapwidthunit = ( isset($_POST['mapwidthunit']) && ( ($_POST['mapwidthunit'] == 'px') || ($_POST['mapwidthunit'] == '%') ) ) ? $_POST['mapwidthunit'] : ( isset($_GET['mapwidthunit']) && ( ($_GET['mapwidthunit'] == 'px') || ($_GET['mapwidthunit'] == '%')  ) ? $_GET['mapwidthunit'] : $lmm_options[ 'defaults_layer_mapwidthunit' ]);
											$mapheight = isset($_POST['mapheight']) ? $_POST['mapheight'] : (isset($_GET['mapheight']) ? $_GET['mapheight'] : intval($lmm_options[ 'defaults_layer_mapheight' ]));
											$panel = ( isset($_POST['panel']) && ( ($_POST['panel'] == '0') || ($_POST['panel'] == '1')) ) ? $_POST['panel'] : ( isset($_GET['panel']) && ( ($_GET['panel'] == '0') || ($_GET['panel'] == '1') ) ? $_GET['panel'] : $lmm_options[ 'defaults_layer_panel' ]);
											$layerviewlat = isset($_POST['layerviewlat']) ? floatval($_POST['layerviewlat']) : (isset($_GET['layerviewlat']) ? floatval($_GET['layerviewlat']) : str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lat' ])));
											$layerviewlon = isset($_POST['layerviewlon']) ? floatval($_POST['layerviewlon']) : (isset($_GET['layerviewlon']) ? floatval($_GET['layerviewlon']) : str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lon' ])));
											$createdby = isset($_POST['createdby']) ? esc_html($_POST['createdby']) : (isset($_GET['createdby']) ? esc_html($_GET['createdby']) : 'MapsMarker API');
											$createdon = isset($_POST['createdon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['createdon'] : (isset($_GET['createdon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['createdon'] : current_time('mysql',0));
											$updatedby = isset($_POST['updatedby']) ? esc_html($_POST['updatedby']) : (isset($_GET['updatedby']) ? esc_html($_GET['updatedby']) : 'MapsMarker API');
											$updatedon = isset($_POST['updatedon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['updatedon'] : (isset($_GET['updatedon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['updatedon'] : current_time('mysql',0));
											$controlbox = ( isset($_POST['controlbox']) && ( ($_POST['controlbox'] == '0') || ($_POST['controlbox'] == '1') || ($_POST['controlbox'] == '2')) ) ? $_POST['controlbox'] : ( isset($_GET['controlbox']) && ( ($_GET['controlbox'] == '0') || ($_GET['controlbox'] == '1') || ($_GET['controlbox'] == '2') ) ? $_GET['controlbox'] : $lmm_options[ 'defaults_layer_controlbox' ]);
											$overlays_custom = ( isset($_POST['overlays_custom']) && ( ($_POST['overlays_custom'] == '0') || ($_POST['overlays_custom'] == '1')) ) ? $_POST['overlays_custom'] : ( isset($_GET['overlays_custom']) && ( ($_GET['overlays_custom'] == '0') || ($_GET['overlays_custom'] == '1') ) ? $_GET['overlays_custom'] : (isset($lmm_options[ 'defaults_layer_overlays_custom_active' ]) ? '1' : '0'));
											$overlays_custom2 = ( isset($_POST['overlays_custom2']) && ( ($_POST['overlays_custom2'] == '0') || ($_POST['overlays_custom2'] == '1')) ) ? $_POST['overlays_custom2'] : ( isset($_GET['overlays_custom2']) && ( ($_GET['overlays_custom2'] == '0') || ($_GET['overlays_custom2'] == '1') ) ? $_GET['overlays_custom2'] : (isset($lmm_options[ 'defaults_layer_overlays_custom2_active' ]) ? '1' : '0'));
											$overlays_custom3 = ( isset($_POST['overlays_custom3']) && ( ($_POST['overlays_custom3'] == '0') || ($_POST['overlays_custom3'] == '1')) ) ? $_POST['overlays_custom3'] : ( isset($_GET['overlays_custom3']) && ( ($_GET['overlays_custom3'] == '0') || ($_GET['overlays_custom3'] == '1') ) ? $_GET['overlays_custom3'] : (isset($lmm_options[ 'defaults_layer_overlays_custom3_active' ]) ? '1' : '0'));
											$overlays_custom4 = ( isset($_POST['overlays_custom4']) && ( ($_POST['overlays_custom4'] == '0') || ($_POST['overlays_custom4'] == '1')) ) ? $_POST['overlays_custom4'] : ( isset($_GET['overlays_custom4']) && ( ($_GET['overlays_custom4'] == '0') || ($_GET['overlays_custom4'] == '1') ) ? $_GET['overlays_custom4'] : (isset($lmm_options[ 'defaults_layer_overlays_custom4_active' ]) ? '1' : '0'));
											$wms = ( isset($_POST['wms']) && ( ($_POST['wms'] == '0') || ($_POST['wms'] == '1')) ) ? $_POST['wms'] : ( isset($_GET['wms']) && ( ($_GET['wms'] == '0') || ($_GET['wms'] == '1') ) ? $_GET['wms'] : (isset($lmm_options[ 'defaults_layer_wms_active' ]) ? '1' : '0'));
											$wms2 = ( isset($_POST['wms2']) && ( ($_POST['wms2'] == '0') || ($_POST['wms2'] == '1')) ) ? $_POST['wms2'] : ( isset($_GET['wms2']) && ( ($_GET['wms2'] == '0') || ($_GET['wms2'] == '1') ) ? $_GET['wms2'] : (isset($lmm_options[ 'defaults_layer_wms2_active' ]) ? '1' : '0'));
											$wms3 = ( isset($_POST['wms3']) && ( ($_POST['wms3'] == '0') || ($_POST['wms3'] == '1')) ) ? $_POST['wms3'] : ( isset($_GET['wms3']) && ( ($_GET['wms3'] == '0') || ($_GET['wms3'] == '1') ) ? $_GET['wms3'] : (isset($lmm_options[ 'defaults_layer_wms3_active' ]) ? '1' : '0'));
											$wms4 = ( isset($_POST['wms4']) && ( ($_POST['wms4'] == '0') || ($_POST['wms4'] == '1')) ) ? $_POST['wms4'] : ( isset($_GET['wms4']) && ( ($_GET['wms4'] == '0') || ($_GET['wms4'] == '1') ) ? $_GET['wms4'] : (isset($lmm_options[ 'defaults_layer_wms4_active' ]) ? '1' : '0'));
											$wms5 = ( isset($_POST['wms5']) && ( ($_POST['wms5'] == '0') || ($_POST['wms5'] == '1')) ) ? $_POST['wms5'] : ( isset($_GET['wms5']) && ( ($_GET['wms5'] == '0') || ($_GET['wms5'] == '1') ) ? $_GET['wms5'] : (isset($lmm_options[ 'defaults_layer_wms5_active' ]) ? '1' : '0'));
											$wms6 = ( isset($_POST['wms6']) && ( ($_POST['wms6'] == '0') || ($_POST['wms6'] == '1')) ) ? $_POST['wms6'] : ( isset($_GET['wms6']) && ( ($_GET['wms6'] == '0') || ($_GET['wms6'] == '1') ) ? $_GET['wms6'] : (isset($lmm_options[ 'defaults_layer_wms6_active' ]) ? '1' : '0'));
											$wms7 = ( isset($_POST['wms7']) && ( ($_POST['wms7'] == '0') || ($_POST['wms7'] == '1')) ) ? $_POST['wms7'] : ( isset($_GET['wms7']) && ( ($_GET['wms7'] == '0') || ($_GET['wms7'] == '1') ) ? $_GET['wms7'] : (isset($lmm_options[ 'defaults_layer_wms7_active' ]) ? '1' : '0'));
											$wms8 = ( isset($_POST['wms8']) && ( ($_POST['wms8'] == '0') || ($_POST['wms8'] == '1')) ) ? $_POST['wms8'] : ( isset($_GET['wms8']) && ( ($_GET['wms8'] == '0') || ($_GET['wms8'] == '1') ) ? $_GET['wms8'] : (isset($lmm_options[ 'defaults_layer_wms8_active' ]) ? '1' : '0'));
											$wms9 = ( isset($_POST['wms9']) && ( ($_POST['wms9'] == '0') || ($_POST['wms9'] == '1')) ) ? $_POST['wms9'] : ( isset($_GET['wms9']) && ( ($_GET['wms9'] == '0') || ($_GET['wms9'] == '1') ) ? $_GET['wms9'] : (isset($lmm_options[ 'defaults_layer_wms9_active' ]) ? '1' : '0'));
											$wms10 = ( isset($_POST['wms10']) && ( ($_POST['wms10'] == '0') || ($_POST['wms10'] == '1')) ) ? $_POST['wms10'] : ( isset($_GET['wms10']) && ( ($_GET['wms10'] == '0') || ($_GET['wms10'] == '1') ) ? $_GET['wms10'] : (isset($lmm_options[ 'defaults_layer_wms10_active' ]) ? '1' : '0'));
											$listmarkers = ( isset($_POST['listmarkers']) && ( ($_POST['listmarkers'] == '0') || ($_POST['listmarkers'] == '1')) ) ? $_POST['listmarkers'] : ( isset($_GET['listmarkers']) && ( ($_GET['listmarkers'] == '0') || ($_GET['listmarkers'] == '1') ) ? $_GET['listmarkers'] : (isset($lmm_options[ 'defaults_layer_listmarkers' ]) ? '1' : '0'));
											$multi_layer_map = ( isset($_POST['multi_layer_map']) && ( ($_POST['multi_layer_map'] == '0') || ($_POST['multi_layer_map'] == '1')) ) ? $_POST['multi_layer_map'] : ( isset($_GET['multi_layer_map']) && ( ($_GET['multi_layer_map'] == '0') || ($_GET['multi_layer_map'] == '1') ) ? $_GET['multi_layer_map'] : '0');
											$multi_layer_map_list = isset($_POST['multi_layer_map_list']) ? $_POST['multi_layer_map_list'] : (isset($_GET['multi_layer_map_list']) ? $_GET['multi_layer_map_list'] : '');
											$address = isset($_POST['address']) ? $_POST['address'] : (isset($_GET['address']) ? $_GET['address'] : '');
											$clustering = '1';  //info: added for compat
											$gpx_url = ''; //info: added for compat
											$gpx_panel = '0'; //info: added for compat
											if ($geocode != NULL) {
												$do_geocoding = MMP_Geocoding::getLatLng($geocode);
												if ($do_geocoding['success'] == true) {
													$layerviewlat = $do_geocoding['lat'];
													$layerviewlon = $do_geocoding['lon'];
													$address = $do_geocoding['address'];
												} else {
													if ($format == 'json') {
														header('Content-type: application/json; charset=utf-8');
														if ($callback != NULL) { echo $callback . '('; }
														echo '{'.PHP_EOL;
														echo '"success":false,'.PHP_EOL;
														echo '"message":"' . sprintf(esc_attr__('Geocoding error: %1s','lmm'), $do_geocoding['message']) . '",'.PHP_EOL;
														echo '"data": { }'.PHP_EOL;
														echo '}';
														if ($callback != NULL) { echo ');'; }
													} else if ($format == 'xml') {
														header('Content-type: application/xml; charset=utf-8');
														echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
														echo '<mapsmarker>'.PHP_EOL;
														echo '<success>false</success>'.PHP_EOL;
														echo '<message>' . sprintf(esc_attr__('Geocoding error: %1s','lmm'), $do_geocoding['message']) . '</message>'.PHP_EOL;
														echo '<data></data>'.PHP_EOL;
														echo '</mapsmarker>';
													}
													exit();
												}
											}
											$query_add = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d)", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel );
											$result_add = $wpdb->query( $query_add );
											if ($result_add == TRUE) {
												if ($format == 'json') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":true,'.PHP_EOL;
													echo '"message":"' . esc_attr__('Layer has been successfully published','lmm') . '",'.PHP_EOL;
													echo '"data": {'.PHP_EOL;
														echo '"' . $remap_id . '":"' . $wpdb->insert_id . '",'.PHP_EOL;
														echo '"' . $remap_name . '":"' . stripslashes($name_quotes) . '",'.PHP_EOL;
														echo '"' . $remap_basemap . '":"' . $basemap . '",'.PHP_EOL;
														echo '"' . $remap_layerzoom . '":"' . $layerzoom . '",'.PHP_EOL;
														echo '"' . $remap_mapwidth . '":"' . $mapwidth . '",'.PHP_EOL;
														echo '"' . $remap_mapwidthunit . '":"' . $mapwidthunit . '",'.PHP_EOL;
														echo '"' . $remap_mapheight . '":"' . $mapheight . '",'.PHP_EOL;
														echo '"' . $remap_panel . '":"' . $panel . '",'.PHP_EOL;
														echo '"' . $remap_layerviewlat . '":"' . $layerviewlat . '",'.PHP_EOL;
														echo '"' . $remap_layerviewlon . '":"' . $layerviewlon . '",'.PHP_EOL;
														echo '"' . $remap_createdby . '":"' . $createdby . '",'.PHP_EOL;
														echo '"' . $remap_createdon . '":"' . $createdon . '",'.PHP_EOL;
														echo '"' . $remap_updatedby . '":"' . $updatedby . '",'.PHP_EOL;
														echo '"' . $remap_updatedon . '":"' . $updatedon . '",'.PHP_EOL;
														echo '"' . $remap_controlbox . '":"' . $controlbox . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom . '":"' . $overlays_custom . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom2 . '":"' . $overlays_custom2 . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom3 . '":"' . $overlays_custom3 . '",'.PHP_EOL;
														echo '"' . $remap_overlays_custom4 . '":"' . $overlays_custom4 . '",'.PHP_EOL;
														echo '"' . $remap_wms . '":"' . $wms . '",'.PHP_EOL;
														echo '"' . $remap_wms2 . '":"' . $wms2 . '",'.PHP_EOL;
														echo '"' . $remap_wms3 . '":"' . $wms3 . '",'.PHP_EOL;
														echo '"' . $remap_wms4 . '":"' . $wms4 . '",'.PHP_EOL;
														echo '"' . $remap_wms5 . '":"' . $wms5 . '",'.PHP_EOL;
														echo '"' . $remap_wms6 . '":"' . $wms6 . '",'.PHP_EOL;
														echo '"' . $remap_wms7 . '":"' . $wms7 . '",'.PHP_EOL;
														echo '"' . $remap_wms8 . '":"' . $wms8 . '",'.PHP_EOL;
														echo '"' . $remap_wms9 . '":"' . $wms9 . '",'.PHP_EOL;
														echo '"' . $remap_wms10 . '":"' . $wms10 . '",'.PHP_EOL;
														echo '"' . $remap_listmarkers . '":"' . $listmarkers . '",'.PHP_EOL;
														echo '"' . $remap_multi_layer_map . '":"' . $multi_layer_map . '",'.PHP_EOL;
														echo '"' . $remap_multi_layer_map_list . '":"' . $multi_layer_map_list . '",'.PHP_EOL;
														echo '"' . $remap_address . '":"'.$address.'"'.PHP_EOL;
														echo '}';
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Cache-Control: no-cache, must-revalidate');
													header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<!DOCTYPE mapsmarker ['.PHP_EOL;
													echo '<!ELEMENT mapsmarker ((success, message, data))>'.PHP_EOL;
													echo '<!ATTLIST mapsmarker xmlns:xsi CDATA #FIXED "http://www.w3.org/2001/XMLSchema-instance" >'.PHP_EOL;
													echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . '))>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_name . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layerzoom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidth . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapwidthunit . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mapheight . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_panel . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layerviewlat . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layerviewlon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_createdon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedby . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_updatedon . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_controlbox . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_overlays_custom4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms2 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms3 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms4 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms5 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms6 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms7 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms8 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms9 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_wms10 . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_listmarkers . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_multi_layer_map . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_multi_layer_map_list . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_address . ' (#PCDATA)>'.PHP_EOL;
													echo ']>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>true</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('Layer has been successfully published','lmm') . '</message>'.PHP_EOL;
													echo '<data>'.PHP_EOL;
														echo '<' . $remap_id . '>' . $wpdb->insert_id . '</' . $remap_id . '>'.PHP_EOL;
														echo '<' . $remap_name . '><![CDATA[' . stripslashes($name_quotes) . ']]></' . $remap_name . '>'.PHP_EOL;
														echo '<' . $remap_basemap . '>' . $basemap . '</' . $remap_basemap . '>'.PHP_EOL;
														echo '<' . $remap_layerzoom . '>' . $layerzoom . '</' . $remap_layerzoom . '>'.PHP_EOL;
														echo '<' . $remap_mapwidth . '>' . $mapwidth . '</' . $remap_mapwidth . '>'.PHP_EOL;
														echo '<' . $remap_mapwidthunit . '>' . $mapwidthunit . '</' . $remap_mapwidthunit . '>'.PHP_EOL;
														echo '<' . $remap_mapheight . '>' . $mapheight . '</' . $remap_mapheight . '>'.PHP_EOL;
														echo '<' . $remap_panel . '>' . $panel . '</' . $remap_panel . '>'.PHP_EOL;
														echo '<' . $remap_layerviewlat . '>' . $layerviewlat . '</' . $remap_layerviewlat . '>'.PHP_EOL;
														echo '<' . $remap_layerviewlon . '>' . $layerviewlon . '</' . $remap_layerviewlon . '>'.PHP_EOL;
														echo '<' . $remap_createdby . '><![CDATA[' . $createdby . ']]></' . $remap_createdby . '>'.PHP_EOL;
														echo '<' . $remap_createdon . '>' . $createdon . '</' . $remap_createdon . '>'.PHP_EOL;
														echo '<' . $remap_updatedby . '><![CDATA[' . $updatedby . ']]></' . $remap_updatedby . '>'.PHP_EOL;
														echo '<' . $remap_updatedon . '>' . $updatedon . '</' . $remap_updatedon . '>'.PHP_EOL;
														echo '<' . $remap_controlbox . '>' . $controlbox . '</' . $remap_controlbox . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom . '>' . $overlays_custom . '</' . $remap_overlays_custom . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom2 . '>' . $overlays_custom2 . '</' . $remap_overlays_custom2 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom3 . '>' . $overlays_custom3 . '</' . $remap_overlays_custom3 . '>'.PHP_EOL;
														echo '<' . $remap_overlays_custom4 . '>' . $overlays_custom4 . '</' . $remap_overlays_custom4 . '>'.PHP_EOL;
														echo '<' . $remap_wms . '>' . $wms . '</' . $remap_wms . '>'.PHP_EOL;
														echo '<' . $remap_wms2 . '>' . $wms2 . '</' . $remap_wms2 . '>'.PHP_EOL;
														echo '<' . $remap_wms3 . '>' . $wms3 . '</' . $remap_wms3 . '>'.PHP_EOL;
														echo '<' . $remap_wms4 . '>' . $wms4 . '</' . $remap_wms4 . '>'.PHP_EOL;
														echo '<' . $remap_wms5 . '>' . $wms5 . '</' . $remap_wms5 . '>'.PHP_EOL;
														echo '<' . $remap_wms6 . '>' . $wms6 . '</' . $remap_wms6 . '>'.PHP_EOL;
														echo '<' . $remap_wms7 . '>' . $wms7 . '</' . $remap_wms7 . '>'.PHP_EOL;
														echo '<' . $remap_wms8 . '>' . $wms8 . '</' . $remap_wms8 . '>'.PHP_EOL;
														echo '<' . $remap_wms9 . '>' . $wms9 . '</' . $remap_wms9 . '>'.PHP_EOL;
														echo '<' . $remap_wms10 . '>' . $wms10 . '</' . $remap_wms10 . '>'.PHP_EOL;
														echo '<' . $remap_listmarkers . '>' . $listmarkers . '</' . $remap_listmarkers . '>'.PHP_EOL;
														echo '<' . $remap_multi_layer_map . '>' . $multi_layer_map . '</' . $remap_multi_layer_map . '>'.PHP_EOL;
														echo '<' . $remap_multi_layer_map_list . '>' . $multi_layer_map_list . '</' . $remap_multi_layer_map_list . '>'.PHP_EOL;
														echo '<' . $remap_address . '>' . $address . '</' . $remap_address . '>'.PHP_EOL;
													echo '</data>'.PHP_EOL;
													echo '</mapsmarker>';
												} //info: end format layer / add
											} else {
												if ($format == 'json') {
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":false,'.PHP_EOL;
													echo '"message":"' . esc_attr__('You have an error in your SQL syntax','lmm') . '",'.PHP_EOL;
													echo '"data": { }'.PHP_EOL;
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>false</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('You have an error in your SQL syntax','lmm') . '</message>'.PHP_EOL;
													echo '<data></data>'.PHP_EOL;
													echo '</mapsmarker>';
												} //info: end query check layer ok / add
											} //info: end add layer
										} else if ($type == '') {
											if ($format == 'json') {
												header('Content-type: application/json; charset=utf-8');
												if ($callback != NULL) { echo $callback . '('; }
												echo '{'.PHP_EOL;
												echo '"success":false,'.PHP_EOL;
												echo '"message":"' . esc_attr__('API parameter type has to be set','lmm') . '",'.PHP_EOL;
												echo '"data": { }'.PHP_EOL;
												echo '}';
												if ($callback != NULL) { echo ');'; }
											} else if ($format == 'xml') {
												header('Content-type: application/xml; charset=utf-8');
												echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
												echo '<mapsmarker>'.PHP_EOL;
												echo '<success>false</success>'.PHP_EOL;
												echo '<message>' . esc_attr__('API parameter type has to be set','lmm') . '</message>'.PHP_EOL;
												echo '<data></data>'.PHP_EOL;
												echo '</mapsmarker>';
											}
										} else {
											if ($format == 'json') {
												header('Content-type: application/json; charset=utf-8');
												if ($callback != NULL) { echo $callback . '('; }
												echo '{'.PHP_EOL;
												echo '"success":false,'.PHP_EOL;
												echo '"message":"' . esc_attr__('API parameter type is invalid','lmm') . '",'.PHP_EOL;
												echo '"data": { }'.PHP_EOL;
												echo '}';
												if ($callback != NULL) { echo ');'; }
											} else if ($format == 'xml') {
												header('Content-type: application/xml; charset=utf-8');
												echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
												echo '<mapsmarker>'.PHP_EOL;
												echo '<success>false</success>'.PHP_EOL;
												echo '<message>' . esc_attr__('API parameter type is invalid','lmm') . '</message>'.PHP_EOL;
												echo '<data></data>'.PHP_EOL;
												echo '</mapsmarker>';
											}
										} //info: end type check / add
									} else {
										if ($format == 'json') {
											header('Content-type: application/json; charset=utf-8');
											if ($callback != NULL) { echo $callback . '('; }
											echo '{'.PHP_EOL;
											echo '"success":false,'.PHP_EOL;
											echo '"message":"' . esc_attr__('API action is not allowed','lmm') . '",'.PHP_EOL;
											echo '"data": { }'.PHP_EOL;
											echo '}';
										if ($callback != NULL) { echo ');'; }
										} else if ($format == 'xml') {
											header('Content-type: application/xml; charset=utf-8');
											echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
											echo '<mapsmarker>'.PHP_EOL;
											echo '<success>false</success>'.PHP_EOL;
											echo '<message>' . esc_attr__('API action is not allowed','lmm') . '</message>'.PHP_EOL;
											echo '<data></data>'.PHP_EOL;
											echo '</mapsmarker>';
										}
									} //info: end permission check / add
								/******************************
								* action update               *
								******************************/
								} else if ($action == 'update') {
									if ($format == 'json') {
										header('Content-type: application/json; charset=utf-8');
										if ($callback != NULL) { echo $callback . '('; }
										echo '{'.PHP_EOL;
										echo '"success":false,'.PHP_EOL;
										echo '"message":"' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . ': https://www.mapsmarker.com",'.PHP_EOL;
										echo '"data": { }'.PHP_EOL;
										echo '}';
										if ($callback != NULL) { echo ');'; }
									} else if ($format == 'xml') {
										header('Content-type: application/xml; charset=utf-8');
										echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
										echo '<mapsmarker>'.PHP_EOL;
										echo '<success>false</success>'.PHP_EOL;
										echo '<message>' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . ': https://www.mapsmarker.com</message>'.PHP_EOL;
										echo '<data></data>'.PHP_EOL;
										echo '</mapsmarker>';
									}
								/******************************
								* action delete                  *
								******************************/
								} else if ($action == 'delete') {
									if ($format == 'json') {
										header('Content-type: application/json; charset=utf-8');
										if ($callback != NULL) { echo $callback . '('; }
										echo '{'.PHP_EOL;
										echo '"success":false,'.PHP_EOL;
										echo '"message":"' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . ': https://www.mapsmarker.com",'.PHP_EOL;
										echo '"data": { }'.PHP_EOL;
										echo '}';
										if ($callback != NULL) { echo ');'; }
									} else if ($format == 'xml') {
										header('Content-type: application/xml; charset=utf-8');
										echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
										echo '<mapsmarker>'.PHP_EOL;
										echo '<success>false</success>'.PHP_EOL;
										echo '<message>' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . ': https://www.mapsmarker.com</message>'.PHP_EOL;
										echo '<data></data>'.PHP_EOL;
										echo '</mapsmarker>';
									}
								/******************************
								* action search                  *
								******************************/
								} else if ($action == 'search') {
									if ($format == 'json') {
										header('Content-type: application/json; charset=utf-8');
										if ($callback != NULL) { echo $callback . '('; }
										echo '{'.PHP_EOL;
										echo '"success":false,'.PHP_EOL;
										echo '"message":"' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . ': https://www.mapsmarker.com",'.PHP_EOL;
										echo '"data": { }'.PHP_EOL;
										echo '}';
										if ($callback != NULL) { echo ');'; }
									} else if ($format == 'xml') {
										header('Content-type: application/xml; charset=utf-8');
										echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
										echo '<mapsmarker>'.PHP_EOL;
										echo '<success>false</success>'.PHP_EOL;
										echo '<message>' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . ': https://www.mapsmarker.com</message>'.PHP_EOL;
										echo '<data></data>'.PHP_EOL;
										echo '</mapsmarker>';
									}
								} else if ($action == '') {
									if ($format == 'json') {
										header('Content-type: application/json; charset=utf-8');
										if ($callback != NULL) { echo $callback . '('; }
										echo '{'.PHP_EOL;
										echo '"success":false,'.PHP_EOL;
										echo '"message":"' . esc_attr__('API parameter action has to be set','lmm') . '",'.PHP_EOL;
										echo '"data": { }'.PHP_EOL;
										echo '}';
										if ($callback != NULL) { echo ');'; }
									} else if ($format == 'xml') {
										header('Content-type: application/xml; charset=utf-8');
										echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
										echo '<mapsmarker>'.PHP_EOL;
										echo '<success>false</success>'.PHP_EOL;
										echo '<message>' . esc_attr__('API parameter action has to be set','lmm') . '</message>'.PHP_EOL;
										echo '<data></data>'.PHP_EOL;
										echo '</mapsmarker>';
									}
								} else {
									if ($format == 'json') {
										header('Content-type: application/json; charset=utf-8');
										if ($callback != NULL) { echo $callback . '('; }
										echo '{'.PHP_EOL;
										echo '"success":false,'.PHP_EOL;
										echo '"message":"' . esc_attr__('API parameter action is invalid','lmm') . '",'.PHP_EOL;
										echo '"data": { }'.PHP_EOL;
										echo '}';
										if ($callback != NULL) { echo ');'; }
									} else if ($format == 'xml') {
										header('Content-type: application/xml; charset=utf-8');
										echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
										echo '<mapsmarker>'.PHP_EOL;
										echo '<success>false</success>'.PHP_EOL;
										echo '<message>' . esc_attr__('API parameter action is invalid','lmm') . '</message>'.PHP_EOL;
										echo '<data></data>'.PHP_EOL;
										echo '</mapsmarker>';
									}
								} //info: end check action / general
							} else {
								if ($format == 'json') {
									header('Content-type: application/json; charset=utf-8');
									if ($callback != NULL) { echo $callback . '('; }
									echo '{'.PHP_EOL;
									echo '"success":false,'.PHP_EOL;
									echo '"message":"' . sprintf(esc_attr__('API access via IP %1s is not allowed','lmm'), $_SERVER['REMOTE_ADDR']) . '",'.PHP_EOL;
									echo '"data": { }'.PHP_EOL;
									echo '}';
									if ($callback != NULL) { echo ');'; }
								} else if ($format == 'xml') {
									header('Content-type: application/xml; charset=utf-8');
									echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
									echo '<mapsmarker>'.PHP_EOL;
									echo '<success>false</success>'.PHP_EOL;
									echo '<message>' . sprintf(esc_attr__('API access via IP %1s is not allowed','lmm'), $_SERVER['REMOTE_ADDR']) . '</message>'.PHP_EOL;
									echo '<data></data>'.PHP_EOL;
									echo '</mapsmarker>';
								}
							} //info: end ip access check / general
						} else {
							if ($format == 'json') {
								header('Content-type: application/json; charset=utf-8');
								if ($callback != NULL) { echo $callback . '('; }
								echo '{'.PHP_EOL;
								echo '"success":false,'.PHP_EOL;
								echo '"message":"' . sprintf(esc_attr__('Referer (%1s) is not allowed','lmm'), $referer) . '",'.PHP_EOL;
								echo '"data": { }'.PHP_EOL;
								echo '}';
								if ($callback != NULL) { echo ');'; }
							} else if ($format == 'xml') {
								header('Content-type: application/xml; charset=utf-8');
								echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
								echo '<mapsmarker>'.PHP_EOL;
								echo '<success>false</success>'.PHP_EOL;
								echo '<message>' . sprintf(esc_attr__('Referer (%1s) is invalid','lmm'), $referer) . '</message>'.PHP_EOL;
								echo '<data></data>'.PHP_EOL;
								echo '</mapsmarker>';
							}
						} //info: end referer check / general
					} else {
						if ($format == 'json') {
							header('Content-type: application/json; charset=utf-8');
							if ($callback != NULL) { echo $callback . '('; }
							echo '{'.PHP_EOL;
							echo '"success":false,'.PHP_EOL;
							echo '"message":"' . esc_attr__('signature is invalid','lmm') . '",'.PHP_EOL;
							echo '"data": { }'.PHP_EOL;
							echo '}';
							if ($callback != NULL) { echo ');'; }
						} else if ($format == 'xml') {
							header('Content-type: application/xml; charset=utf-8');
							echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
							echo '<mapsmarker>'.PHP_EOL;
							echo '<success>false</success>'.PHP_EOL;
							echo '<message>' . esc_attr__('signature is invalid','lmm') . '</message>'.PHP_EOL;
							echo '<data></data>'.PHP_EOL;
							echo '</mapsmarker>';
						}
					} //info: end signature validity check / general
				} else {
					if ($format == 'json') {
						header('Content-type: application/json; charset=utf-8');
						if ($callback != NULL) { echo $callback . '('; }
						echo '{'.PHP_EOL;
						echo '"success":false,'.PHP_EOL;
						echo '"message":"' . esc_attr__('Public API key is invalid','lmm') . '",'.PHP_EOL;
						echo '"data": { }'.PHP_EOL;
						echo '}';
						if ($callback != NULL) { echo ');'; }
					} else if ($format == 'xml') {
						header('Content-type: application/xml; charset=utf-8');
						echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
						echo '<mapsmarker>'.PHP_EOL;
						echo '<success>false</success>'.PHP_EOL;
						echo '<message>' . esc_attr__('Public API key is invalid','lmm') . '</message>'.PHP_EOL;
						echo '<data></data>'.PHP_EOL;
						echo '</mapsmarker>';
					}
				} //info: end publickey validity check / general
			} else { //info: change if v2 is released
				if ($format == 'json') {
					header('Content-type: application/json; charset=utf-8');
					if ($callback != NULL) { echo $callback . '('; }
					echo '{'.PHP_EOL;
					echo '"success":false,'.PHP_EOL;
					echo '"message":"' . esc_attr__('API version is invalid','lmm') . '",'.PHP_EOL;
					echo '"data": { }'.PHP_EOL;
					echo '}';
					if ($callback != NULL) { echo ');'; }
				} else if ($format == 'xml') {
					header('Content-type: application/xml; charset=utf-8');
					echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
					echo '<mapsmarker>'.PHP_EOL;
					echo '<success>false</success>'.PHP_EOL;
					echo '<message>' . esc_attr__('API version is invalid','lmm') . '</message>'.PHP_EOL;
					echo '<data></data>'.PHP_EOL;
					echo '</mapsmarker>';
				}
			} //info: end API version check
		} else {
			if ($format == 'json') {
				header('Content-type: application/json; charset=utf-8');
				if ($callback != NULL) { echo $callback . '('; }
				echo '{'.PHP_EOL;
				echo '"success":false,'.PHP_EOL;
				echo '"message":"' . sprintf(esc_attr__('The request method %1s is not allowed','lmm'), $request_method) . '",'.PHP_EOL;
				echo '"data": { }'.PHP_EOL;
				echo '}';
				if ($callback != NULL) { echo ');'; }
			} else if ($format == 'xml') {
				header('Content-type: application/xml; charset=utf-8');
				echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
				echo '<mapsmarker>'.PHP_EOL;
				echo '<success>false</success>'.PHP_EOL;
				echo '<message>' . sprintf(esc_attr__('The request method %1s is not allowed','lmm'), $request_method) . '</message>'.PHP_EOL;
				echo '<data></data>'.PHP_EOL;
				echo '</mapsmarker>';
			}
		} //info: end request method check
	} else {
		if ($format == 'json') {
			header('Content-type: application/json; charset=utf-8');
			if ($callback != NULL) { echo $callback . '('; }
			echo '{'.PHP_EOL;
			echo '"success":false,'.PHP_EOL;
			echo '"message":"' . esc_attr__('API is disabled','lmm') . '",'.PHP_EOL;
			echo '"data": { }'.PHP_EOL;
			echo '}';
			if ($callback != NULL) { echo ');'; }
		} else if ($format == 'xml') {
			header('Content-type: application/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
			echo '<mapsmarker>'.PHP_EOL;
			echo '<success>false</success>'.PHP_EOL;
			echo '<message>' . esc_attr__('API is disabled','lmm') . '</message>'.PHP_EOL;
			echo '<data></data>'.PHP_EOL;
			echo '</mapsmarker>';
		}
	} //info: end api_status enabled
} //info: end plugin active check
?>