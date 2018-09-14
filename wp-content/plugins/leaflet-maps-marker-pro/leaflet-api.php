<?php
/*
    MapsMarker-API - Maps Marker Pro
*/
//info redirect to permalink if file is being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-api.php') {
	while (!is_file('wp-load.php')) {
		if (is_dir('..' . DIRECTORY_SEPARATOR)) {
			chdir('..' . DIRECTORY_SEPARATOR);
		} else {
			die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
		}
	}
	require_once('wp-load.php');
	$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
	wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/webapi/' . $argv), 301);
	exit;
}

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
if (!lmm_is_plugin_active('leaflet-maps-marker-pro/leaflet-maps-marker.php') ) {
	if ($format == 'json') {
		header('Content-type: application/json; charset=utf-8');
		if ($callback != NULL) { echo $callback . '('; }
		echo '{'.PHP_EOL;
		echo '"success":false,'.PHP_EOL;
		echo '"message":"' . sprintf(esc_attr__('The plugin "Maps Marker Pro" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), get_bloginfo('admin_email') ) . '",'.PHP_EOL;
		echo '"data": { }'.PHP_EOL;
		echo '}';
		if ($callback != NULL) { echo ');'; }
	} else if ($format == 'xml') {
		header('Content-type: application/xml; charset=utf-8');
		echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
		echo '<mapsmarker>'.PHP_EOL;
		echo '<success>false</success>'.PHP_EOL;
		echo '<message>' . sprintf(esc_attr__('The plugin "Maps Marker Pro" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), get_bloginfo('admin_email') ) . '</message>'.PHP_EOL;
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
	$remap_layers = isset($_POST['remap_layers']) ? $_POST['remap_layers'] : (isset($_GET['remap_layers']) ? $_GET['remap_layers'] : 'layers');
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
	$remap_gpx_url = isset($_POST['remap_gpx_url']) ? $_POST['remap_gpx_url'] : (isset($_GET['remap_gpx_url']) ? $_GET['remap_gpx_url'] : 'gpx_url');
	$remap_gpx_panel = isset($_POST['remap_gpx_panel']) ? $_POST['remap_gpx_panel'] : (isset($_GET['remap_gpx_panel']) ? $_GET['remap_gpx_panel'] : 'gpx_panel');
	$remap_mlm_filter = isset($_POST['remap_mlm_filter']) ? $_POST['remap_mlm_filter'] : (isset($_GET['remap_mlm_filter']) ? $_GET['remap_mlm_filter'] : 'mlm_filter');
	$remap_mlm_filter_details = isset($_POST['remap_mlm_filter_details']) ? $_POST['remap_mlm_filter_details'] : (isset($_GET['remap_mlm_filter_details']) ? $_GET['remap_mlm_filter_details'] : 'mlm_filter_details');
	//info: remap layer only
	$remap_name = isset($_POST['remap_name']) ? $_POST['remap_name'] : (isset($_GET['remap_name']) ? $_GET['remap_name'] : 'name');
	$remap_layerzoom = isset($_POST['remap_layerzoom']) ? $_POST['remap_layerzoom'] : (isset($_GET['remap_layerzoom']) ? $_GET['remap_layerzoom'] : 'layerzoom');
	$remap_layerviewlat = isset($_POST['remap_layerviewlat']) ? $_POST['remap_layerviewlat'] : (isset($_GET['remap_layerviewlat']) ? $_GET['remap_layerviewlat'] : 'layerviewlat');
	$remap_layerviewlon = isset($_POST['remap_layerviewlon']) ? $_POST['remap_layerviewlon'] : (isset($_GET['remap_layerviewlon']) ? $_GET['remap_layerviewlon'] : 'layerviewlon');
	$remap_listmarkers = isset($_POST['remap_listmarkers']) ? $_POST['remap_listmarkers'] : (isset($_GET['remap_listmarkers']) ? $_GET['remap_listmarkers'] : 'listmarkers');
	$remap_multi_layer_map = isset($_POST['remap_multi_layer_map']) ? $_POST['remap_multi_layer_map'] : (isset($_GET['remap_multi_layer_map']) ? $_GET['remap_multi_layer_map'] : 'multi_layer_map');
	$remap_multi_layer_map_list = isset($_POST['remap_multi_layer_map_list']) ? $_POST['remap_multi_layer_map_list'] : (isset($_GET['remap_multi_layer_map_list']) ? $_GET['remap_multi_layer_map_list'] : 'multi_layer_map_list');
	$remap_clustering = isset($_POST['remap_clustering']) ? $_POST['remap_clustering'] : (isset($_GET['remap_clustering']) ? $_GET['remap_clustering'] : 'clustering');

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
														echo '"' . $remap_layer . '":[';
														$assigned_layers = json_decode($query_result['layer']);
														$assigned_layers_sanitized = array();
														foreach ($assigned_layers as $assigned_layer) {
															$assigned_layers_sanitized[] = '"' . $assigned_layer . '"';
														}
														echo implode( ', ', $assigned_layers_sanitized ) . '],'.PHP_EOL;
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
														echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
														echo '"' . $remap_gpx_url . '":"' . $query_result['gpx_url'] . '",'.PHP_EOL;
														echo '"' . $remap_gpx_panel . '":"' . $query_result['gpx_panel'] . '"'.PHP_EOL;
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
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layers . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . '))>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layers . ' ((' . $remap_layer . '+))>'.PHP_EOL;
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
													echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
													echo ']>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>true</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('API call was successful','lmm') . '</message>'.PHP_EOL;
													echo '<data>'.PHP_EOL;
														echo '<' . $remap_id . '>' . $query_result['id'] . '</' . $remap_id . '>'.PHP_EOL;
														echo '<' . $remap_markername . '><![CDATA[' . stripslashes(esc_js($query_result['markername'])) . ']]></' . $remap_markername . '>'.PHP_EOL;
														echo '<' . $remap_basemap . '>' . $query_result['basemap'] . '</' . $remap_basemap . '>'.PHP_EOL;
														echo '<' . $remap_layers . '>'.PHP_EOL;
															$assigned_layers = json_decode($query_result['layer']);
															foreach ($assigned_layers as $assigned_layer) {
																echo '<' . $remap_layer . '>' . $assigned_layer . '</' . $remap_layer . '>'.PHP_EOL;
															}
														echo '</' . $remap_layers . '>'.PHP_EOL;
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
														echo '<' . $remap_gpx_url . '><![CDATA[' . $query_result['gpx_url'] . ']]></' . $remap_gpx_url . '>'.PHP_EOL;
														echo '<' . $remap_gpx_panel . '><![CDATA[' . $query_result['gpx_panel'] . ']]></' . $remap_gpx_panel . '>'.PHP_EOL;
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
														echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
														echo '"' . $remap_clustering . '":"' . $query_result['clustering'] . '",'.PHP_EOL;
														echo '"' . $remap_gpx_url . '":"' . $query_result['gpx_url'] . '",'.PHP_EOL;
														echo '"' . $remap_gpx_panel . '":"' . $query_result['gpx_panel'] . '",'.PHP_EOL;
														echo '"' . $remap_mlm_filter . '":"' . $query_result['mlm_filter'] . '",'.PHP_EOL;
														echo '"' . $remap_mlm_filter_details . '":"' . $query_result['mlm_filter_details'] . '"'.PHP_EOL;
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
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . ', ' . $remap_clustering . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel .', '.$remap_mlm_filter.', '.$remap_mlm_filter_details.  '))>'.PHP_EOL;
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
													echo '<!ELEMENT ' . $remap_clustering . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mlm_filter . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mlm_filter_details . ' (#PCDATA)>'.PHP_EOL;
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
														echo '<' . $remap_clustering . '>' . $query_result['clustering'] . '</' . $remap_clustering . '>'.PHP_EOL;
														echo '<' . $remap_gpx_url . '>' . $query_result['gpx_url'] . '</' . $remap_gpx_url . '>'.PHP_EOL;
														echo '<' . $remap_gpx_panel . '>' . $query_result['gpx_panel'] . '</' . $remap_gpx_panel . '>'.PHP_EOL;
														echo '<' . $remap_mlm_filter . '>' . $query_result['mlm_filter'] . '</' . $remap_mlm_filter . '>'.PHP_EOL;
														echo '<' . $remap_mlm_filter_details . '>' . $query_result['mlm_filter_details'] . '</' . $remap_mlm_filter_details . '>'.PHP_EOL;
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

											$markername_quotes = str_replace("\\\\","/", str_replace("\"", "'", $markername));
											$mpopuptext = isset($_POST['popuptext']) ? str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$_POST['popuptext'])) : (isset($_GET['popuptext']) ? str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$_GET['popuptext'])) : '');

											$basemap = isset($_POST['basemap']) && in_array($_POST['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_POST['basemap'] : (isset($_GET['basemap']) && in_array($_GET['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_GET['basemap'] : $lmm_options[ 'standard_basemap' ]);
											$layer = isset($_POST['layer']) ? $_POST['layer'] : (isset($_GET['layer']) ? $_GET['layer'] : (($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '0' : $lmm_options[ 'defaults_marker_default_layer' ]));
											// convert the layer id to json and add the option to assign multiple layers.
											$layer =  array_map('intval', explode (',', $layer));
											$layer = json_encode( array_map('strval', $layer ) );
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
											$gpx_url = isset($_POST['gpx_url']) ? esc_url_raw($_POST['gpx_url']) : (isset($_GET['gpx_url']) ? esc_url_raw($_GET['gpx_url']) : '');
											$gpx_panel = ( isset($_POST['gpx_panel']) && ( ($_POST['gpx_panel'] == '0') || ($_POST['gpx_panel'] == '1')) ) ? $_POST['gpx_panel'] : ( isset($_GET['gpx_panel']) && ( ($_GET['gpx_panel'] == '0') || ($_GET['gpx_panel'] == '1') ) ? $_GET['gpx_panel'] : '0');
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
												$query_add = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel );
											} else {
												$query_add = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel );
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
														echo '"' . $remap_layer . '":[';
														$assigned_layers = json_decode($layer);
														$assigned_layers_sanitized = array();
														foreach ($assigned_layers as $assigned_layer) {
															$assigned_layers_sanitized[] = '"' . $assigned_layer . '"';
														}
														echo implode( ', ', $assigned_layers_sanitized ) . '],'.PHP_EOL;
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
														echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
														echo '"' . $remap_gpx_url . '":"' . $gpx_url . '",'.PHP_EOL;
														echo '"' . $remap_gpx_panel . '":"' . $gpx_panel . '"'.PHP_EOL;
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
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layers . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . '))>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_layers . ' ((' . $remap_layer . '+))>'.PHP_EOL;
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
													echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
													echo ']>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>true</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('Marker has been successfully published','lmm') . '</message>'.PHP_EOL;
													echo '<data>'.PHP_EOL;
														echo '<' . $remap_id . '>' . $wpdb->insert_id . '</' . $remap_id . '>'.PHP_EOL;
														echo '<' . $remap_markername . '><![CDATA[' . stripslashes($markername_quotes) . ']]></' . $remap_markername . '>'.PHP_EOL;
														echo '<' . $remap_basemap . '>' . $basemap . '</' . $remap_basemap . '>'.PHP_EOL;
														echo '<' . $remap_layers . '>'.PHP_EOL;
															$assigned_layers = json_decode($layer);
															foreach ($assigned_layers as $assigned_layer) {
																echo '<' . $remap_layer . '>' . $assigned_layer . '</' . $remap_layer . '>'.PHP_EOL;
															}
														echo '</' . $remap_layers . '>'.PHP_EOL;
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
														echo '<' . $remap_gpx_url . '><![CDATA[' . $gpx_url . ']]></' . $remap_gpx_url . '>'.PHP_EOL;
														echo '<' . $remap_gpx_panel . '>' . $gpx_panel . '</' . $remap_gpx_panel . '>'.PHP_EOL;
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
											$name_quotes = str_replace("\\\\","/", str_replace("\"", "'", $name));
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
											$clustering = ( isset($_POST['clustering']) && ( ($_POST['clustering'] == '0') || ($_POST['clustering'] == '1')) ) ? $_POST['clustering'] : ( isset($_GET['clustering']) && ( ($_GET['clustering'] == '0') || ($_GET['clustering'] == '1') ) ? $_GET['clustering'] : ($lmm_options[ 'defaults_layer_clustering' ] == 'enabled') ? '1' : '0');
											$gpx_url = isset($_POST['gpx_url']) ? esc_url_raw($_POST['gpx_url']) : (isset($_GET['gpx_url']) ? esc_url_raw($_GET['gpx_url']) : '');
											$gpx_panel = ( isset($_POST['gpx_panel']) && ( ($_POST['gpx_panel'] == '0') || ($_POST['gpx_panel'] == '1')) ) ? $_POST['gpx_panel'] : ( isset($_GET['gpx_panel']) && ( ($_GET['gpx_panel'] == '0') || ($_GET['gpx_panel'] == '1') ) ? $_GET['gpx_panel'] : '0');
											$mlm_filter = ( isset($_POST['mlm_filter']) && ( ($_POST['mlm_filter'] == '0') || ($_POST['mlm_filter'] == '1')) ) ? $_POST['mlm_filter'] : ( isset($_GET['mlm_filter']) && ( ($_GET['mlm_filter'] == '0') || ($_GET['mlm_filter'] == '1') ) ? $_GET['mlm_filter'] : '0');
											$mlm_filter_details_input = isset($_POST['mlm_filter_details']) ? stripslashes($_POST['mlm_filter_details']) : (isset($_GET['mlm_filter_details']) ? stripslashes($_GET['mlm_filter_details']) : '');
											//info: make sure input is valid JSON
											$mlm_filter_details_input_decoded = json_decode($mlm_filter_details_input);
											if($mlm_filter_details_input_decoded === NULL) {
												$mlm_filter_details = '';
											} else {
												$mlm_filter_details = $mlm_filter_details_input;
											}

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
											if($mlm_filter_details_input_decoded == NULL){
												$query_add = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d)", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter );
											}else{
												$query_add = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter`, `mlm_filter_details` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d, %s)", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter, $mlm_filter_details );
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
														echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
														echo '"' . $remap_clustering . '":"' . $clustering . '",'.PHP_EOL;
														echo '"' . $remap_gpx_url . '":"' . $gpx_url . '",'.PHP_EOL;
														echo '"' . $remap_gpx_panel . '":"' . $gpx_panel . '",'.PHP_EOL;
														echo '"' . $remap_mlm_filter . '":"' . $mlm_filter . '",'.PHP_EOL;
														echo '"' . $remap_mlm_filter_details . '":"' . $mlm_filter_details . '"'.PHP_EOL;
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
													echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . ', ' . $remap_clustering . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel .', '.$remap_mlm_filter.', '. $remap_mlm_filter_details . '))>'.PHP_EOL;
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
													echo '<!ELEMENT ' . $remap_clustering . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mlm_filter . ' (#PCDATA)>'.PHP_EOL;
													echo '<!ELEMENT ' . $remap_mlm_filter_details . ' (#PCDATA)>'.PHP_EOL;
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
														echo '<' . $remap_clustering . '>' . $clustering . '</' . $remap_clustering . '>'.PHP_EOL;
														echo '<' . $remap_gpx_url . '>' . $gpx_url . '</' . $remap_gpx_url . '>'.PHP_EOL;
														echo '<' . $remap_gpx_panel . '>' . $gpx_panel . '</' . $remap_gpx_panel . '>'.PHP_EOL;
														echo '<' . $remap_mlm_filter . '>' . $mlm_filter . '</' . $remap_mlm_filter . '>'.PHP_EOL;
														echo '<' . $remap_mlm_filter_details . '>' . $mlm_filter_details . '</' . $remap_mlm_filter_details . '>'.PHP_EOL;
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
									if ( $lmm_options['api_permissions_update'] == TRUE ) {
										if ($type == 'marker') {
											$query_view = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `id` = %d", $id), ARRAY_A);
											if (count($query_view) >= 1) {
												$mpopuptext = stripslashes(str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$query_view['popuptext'])));
												$address = stripslashes(str_replace('"', '\'', $query_view['address']));
												$markername = isset($_POST['markername']) ? $_POST['markername'] : (isset($_GET['markername']) ? $_GET['markername'] : $query_view['markername']);
												$markername_quotes = str_replace("\\\\","/", str_replace("\"", "'", $markername)); //info: backslash breaks GeoJSON
												$basemap = isset($_POST['basemap']) && in_array($_POST['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_POST['basemap'] : (isset($_GET['basemap']) && in_array($_GET['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_GET['basemap'] : $query_view['basemap']);

												$layer = isset($_POST['layer']) ? $_POST['layer'] : (isset($_GET['layer']) ? $_GET['layer'] : $query_view['layer']);
												if($layer !== $query_view['layer']){
													// convert the layer id to json and add the option to assign multiple layers.
													$layer =  array_map('intval', explode (',', $layer));
													$layer = json_encode( array_map('strval', $layer ) );
												}
												$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : (isset($_GET['lat']) ? floatval($_GET['lat']) : $query_view['lat']);
												$lon = isset($_POST['lon']) ? floatval($_POST['lon']) : (isset($_GET['lon']) ? floatval($_GET['lon']) : $query_view['lon']);
												$icon = isset($_POST['icon']) ? $_POST['icon'] : (isset($_GET['icon']) ? $_GET['icon'] : $query_view['icon']);
												$popuptext = isset($_POST['popuptext']) ? $_POST['popuptext'] : (isset($_GET['popuptext']) ? $_GET['popuptext'] : $mpopuptext);
												$zoom = isset($_POST['zoom']) ? intval($_POST['zoom']) : (isset($_GET['zoom']) ? intval($_GET['zoom']) : $query_view['zoom']);
												$openpopup = ( isset($_POST['openpopup']) && ( ($_POST['openpopup'] == '0') || ($_POST['openpopup'] == '1')) ) ? $_POST['openpopup'] : ( isset($_GET['openpopup']) && ( ($_GET['openpopup'] == '0') || ($_GET['openpopup'] == '1') ) ? $_GET['openpopup'] : $query_view['openpopup']);
												$mapwidth = isset($_POST['mapwidth']) ? $_POST['mapwidth'] : (isset($_GET['mapwidth']) ? $_GET['mapwidth'] : $query_view['mapwidth']);
												$mapwidthunit = ( isset($_POST['mapwidthunit']) && ( ($_POST['mapwidthunit'] == 'px') || ($_POST['mapwidthunit'] == '%') ) ) ? $_POST['mapwidthunit'] : ( isset($_GET['mapwidthunit']) && ( ($_GET['mapwidthunit'] == 'px') || ($_GET['mapwidthunit'] == '%')  ) ? $_GET['mapwidthunit'] : $query_view['mapwidthunit']);
												$mapheight = isset($_POST['mapheight']) ? $_POST['mapheight'] : (isset($_GET['mapheight']) ? $_GET['mapheight'] : $query_view['mapheight']);
												$panel = ( isset($_POST['panel']) && ( ($_POST['panel'] == '0') || ($_POST['panel'] == '1')) ) ? $_POST['panel'] : ( isset($_GET['panel']) && ( ($_GET['panel'] == '0') || ($_GET['panel'] == '1') ) ? $_GET['panel'] : $query_view['panel']);
												$createdby = isset($_POST['createdby']) ? esc_html($_POST['createdby']) : (isset($_GET['createdby']) ? esc_html($_GET['createdby']) : $query_view['createdby']);
												$createdon = isset($_POST['createdon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['createdon'] : (isset($_GET['createdon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['createdon'] : $query_view['createdon']);
												$updatedby = isset($_POST['updatedby']) ? esc_html($_POST['updatedby']) : (isset($_GET['updatedby']) ? esc_html($_GET['updatedby']) : $query_view['updatedby']);
												$updatedon = isset($_POST['updatedon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['updatedon'] : (isset($_GET['updatedon']) && ( $_GET['updatedon'] == date('Y-m-d H:i:s',strtotime($_GET['updatedon'])) ) ? $_GET['updatedon'] : current_time('mysql',0));
												$controlbox = ( isset($_POST['controlbox']) && ( ($_POST['controlbox'] == '0') || ($_POST['controlbox'] == '1') || ($_POST['controlbox'] == '2')) ) ? $_POST['controlbox'] : ( isset($_GET['controlbox']) && ( ($_GET['controlbox'] == '0') || ($_GET['controlbox'] == '1') || ($_GET['controlbox'] == '2') ) ? $_GET['controlbox'] : $query_view['controlbox']);
												$overlays_custom = ( isset($_POST['overlays_custom']) && ( ($_POST['overlays_custom'] == '0') || ($_POST['overlays_custom'] == '1')) ) ? $_POST['overlays_custom'] : ( isset($_GET['overlays_custom']) && ( ($_GET['overlays_custom'] == '0') || ($_GET['overlays_custom'] == '1') ) ? $_GET['overlays_custom'] : $query_view['overlays_custom']);
												$overlays_custom2 = ( isset($_POST['overlays_custom2']) && ( ($_POST['overlays_custom2'] == '0') || ($_POST['overlays_custom2'] == '1')) ) ? $_POST['overlays_custom2'] : ( isset($_GET['overlays_custom2']) && ( ($_GET['overlays_custom2'] == '0') || ($_GET['overlays_custom2'] == '1') ) ? $_GET['overlays_custom2'] : $query_view['overlays_custom2']);
												$overlays_custom3 = ( isset($_POST['overlays_custom3']) && ( ($_POST['overlays_custom3'] == '0') || ($_POST['overlays_custom3'] == '1')) ) ? $_POST['overlays_custom3'] : ( isset($_GET['overlays_custom3']) && ( ($_GET['overlays_custom3'] == '0') || ($_GET['overlays_custom3'] == '1') ) ? $_GET['overlays_custom3'] : $query_view['overlays_custom3']);
												$overlays_custom4 = ( isset($_POST['overlays_custom4']) && ( ($_POST['overlays_custom4'] == '0') || ($_POST['overlays_custom4'] == '1')) ) ? $_POST['overlays_custom4'] : ( isset($_GET['overlays_custom4']) && ( ($_GET['overlays_custom4'] == '0') || ($_GET['overlays_custom4'] == '1') ) ? $_GET['overlays_custom4'] : $query_view['overlays_custom4']);
												$wms = ( isset($_POST['wms']) && ( ($_POST['wms'] == '0') || ($_POST['wms'] == '1')) ) ? $_POST['wms'] : ( isset($_GET['wms']) && ( ($_GET['wms'] == '0') || ($_GET['wms'] == '1') ) ? $_GET['wms'] : $query_view['wms']);
												$wms2 = ( isset($_POST['wms2']) && ( ($_POST['wms2'] == '0') || ($_POST['wms2'] == '1')) ) ? $_POST['wms2'] : ( isset($_GET['wms2']) && ( ($_GET['wms2'] == '0') || ($_GET['wms2'] == '1') ) ? $_GET['wms2'] : $query_view['wms2']);
												$wms3 = ( isset($_POST['wms3']) && ( ($_POST['wms3'] == '0') || ($_POST['wms3'] == '1')) ) ? $_POST['wms3'] : ( isset($_GET['wms3']) && ( ($_GET['wms3'] == '0') || ($_GET['wms3'] == '1') ) ? $_GET['wms3'] : $query_view['wms3']);
												$wms4 = ( isset($_POST['wms4']) && ( ($_POST['wms4'] == '0') || ($_POST['wms4'] == '1')) ) ? $_POST['wms4'] : ( isset($_GET['wms4']) && ( ($_GET['wms4'] == '0') || ($_GET['wms4'] == '1') ) ? $_GET['wms4'] : $query_view['wms4']);
												$wms5 = ( isset($_POST['wms5']) && ( ($_POST['wms5'] == '0') || ($_POST['wms5'] == '1')) ) ? $_POST['wms5'] : ( isset($_GET['wms5']) && ( ($_GET['wms5'] == '0') || ($_GET['wms5'] == '1') ) ? $_GET['wms5'] : $query_view['wms5']);
												$wms6 = ( isset($_POST['wms6']) && ( ($_POST['wms6'] == '0') || ($_POST['wms6'] == '1')) ) ? $_POST['wms6'] : ( isset($_GET['wms6']) && ( ($_GET['wms6'] == '0') || ($_GET['wms6'] == '1') ) ? $_GET['wms6'] : $query_view['wms6']);
												$wms7 = ( isset($_POST['wms7']) && ( ($_POST['wms7'] == '0') || ($_POST['wms7'] == '1')) ) ? $_POST['wms7'] : ( isset($_GET['wms7']) && ( ($_GET['wms7'] == '0') || ($_GET['wms7'] == '1') ) ? $_GET['wms7'] : $query_view['wms7']);
												$wms8 = ( isset($_POST['wms8']) && ( ($_POST['wms8'] == '0') || ($_POST['wms8'] == '1')) ) ? $_POST['wms8'] : ( isset($_GET['wms8']) && ( ($_GET['wms8'] == '0') || ($_GET['wms8'] == '1') ) ? $_GET['wms8'] : $query_view['wms8']);
												$wms9 = ( isset($_POST['wms9']) && ( ($_POST['wms9'] == '0') || ($_POST['wms9'] == '1')) ) ? $_POST['wms9'] : ( isset($_GET['wms9']) && ( ($_GET['wms9'] == '0') || ($_GET['wms9'] == '1') ) ? $_GET['wms9'] : $query_view['wms9']);
												$wms10 = ( isset($_POST['wms10']) && ( ($_POST['wms10'] == '0') || ($_POST['wms10'] == '1')) ) ? $_POST['wms10'] : ( isset($_GET['wms10']) && ( ($_GET['wms10'] == '0') || ($_GET['wms10'] == '1') ) ? $_GET['wms10'] : $query_view['wms10']);
												$kml_timestamp = isset($_POST['kml_timestamp']) && ( $_POST['kml_timestamp'] == date('Y-m-d H:i:s',strtotime($_POST['kml_timestamp'])) ) ? $_POST['kml_timestamp'] : (isset($_GET['kml_timestamp']) && ( $_GET['kml_timestamp'] == date('Y-m-d H:i:s',strtotime($_GET['kml_timestamp'])) ) ? $_GET['kml_timestamp'] : $query_view['kml_timestamp']);
												$address = isset($_POST['address']) ? $_POST['address'] : (isset($_GET['address']) ? $_GET['address'] : $address);
												$gpx_url = isset($_POST['gpx_url']) ? esc_url_raw($_POST['gpx_url']) : (isset($_GET['gpx_url']) ? esc_url_raw($_GET['gpx_url']) : esc_url_raw($query_view['gpx_url']));
												$gpx_panel = ( isset($_POST['gpx_panel']) && ( ($_POST['gpx_panel'] == '0') || ($_POST['gpx_panel'] == '1')) ) ? $_POST['gpx_panel'] : ( isset($_GET['gpx_panel']) && ( ($_GET['gpx_panel'] == '0') || ($_GET['gpx_panel'] == '1') ) ? $_GET['gpx_panel'] : $query_view['gpx_panel']);

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
													$query_update = $wpdb->prepare( "UPDATE `$table_name_markers` SET `markername` = %s, `basemap` = %s, `layer` = %s, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel, $id );
												} else {
													$query_update = $wpdb->prepare( "UPDATE `$table_name_markers` SET `markername` = %s, `basemap` = %s, `layer` = %s, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `kml_timestamp` = %s, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel, $id );
												}
												$result_update = $wpdb->query( $query_update );
												if ($result_update == TRUE) {
													if ($format == 'json') {
														header('Cache-Control: no-cache, must-revalidate');
														header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
														header('Content-type: application/json; charset=utf-8');
														if ($callback != NULL) { echo $callback . '('; }
														echo '{'.PHP_EOL;
														echo '"success":true,'.PHP_EOL;
														echo '"message":"' . esc_attr__('Marker has been successfully updated','lmm') . '",'.PHP_EOL;
														echo '"data": {'.PHP_EOL;
															echo '"' . $remap_id . '":"'. $id . '",'.PHP_EOL;
															echo '"' . $remap_markername . '":"' . stripslashes($markername_quotes) . '",'.PHP_EOL;
															echo '"' . $remap_basemap . '":"' . $basemap . '",'.PHP_EOL;
															echo '"' . $remap_layer . '":[';
															$assigned_layers = json_decode($layer);
															$assigned_layers_sanitized = array();
															foreach ($assigned_layers as $assigned_layer) {
																$assigned_layers_sanitized[] = '"' . $assigned_layer . '"';
															}
															echo implode( ', ', $assigned_layers_sanitized ) . '],'.PHP_EOL;
															echo '"' . $remap_lat . '":"' . $lat . '",'.PHP_EOL;
															echo '"' . $remap_lon . '":"' . $lon . '",'.PHP_EOL;
															echo '"' . $remap_icon . '":"' . $icon . '",'.PHP_EOL;
															echo '"' . $remap_popuptext . '":"' . stripslashes(str_replace('"', '\'', $popuptext)) . '",'.PHP_EOL;
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
															echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
															echo '"' . $remap_gpx_url . '":"' . $gpx_url . '",'.PHP_EOL;
															echo '"' . $remap_gpx_panel . '":"' . $gpx_panel . '"'.PHP_EOL;
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
														echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layers . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . '))>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_layers . ' ((' . $remap_layer . '+))>'.PHP_EOL;
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
														echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
														echo ']>'.PHP_EOL;
														echo '<mapsmarker>'.PHP_EOL;
														echo '<success>true</success>'.PHP_EOL;
														echo '<message>' . esc_attr__('Marker has been successfully updated','lmm') . '</message>'.PHP_EOL;
														echo '<data>'.PHP_EOL;
															echo '<' . $remap_id . '>' . $id . '</' . $remap_id . '>'.PHP_EOL;
															echo '<' . $remap_markername . '><![CDATA[' . stripslashes($markername_quotes) . ']]></' . $remap_markername . '>'.PHP_EOL;
															echo '<' . $remap_basemap . '>' . $basemap . '</' . $remap_basemap . '>'.PHP_EOL;
															echo '<' . $remap_layers . '>'.PHP_EOL;
															$assigned_layers = json_decode($layer);
															foreach ($assigned_layers as $assigned_layer) {
																echo '<' . $remap_layer . '>' . $assigned_layer . '</' . $remap_layer . '>'.PHP_EOL;
															}
															echo '</' . $remap_layers . '>'.PHP_EOL;
															echo '<' . $remap_lat . '>' . $lat . '</' . $remap_lat . '>'.PHP_EOL;
															echo '<' . $remap_lon . '>' . $lon . '</' . $remap_lon . '>'.PHP_EOL;
															echo '<' . $remap_icon . '><![CDATA[' . $icon . ']]></' . $remap_icon . '>'.PHP_EOL;
															echo '<' . $remap_popuptext . '><![CDATA[' . $popuptext . ']]></' . $remap_popuptext . '>'.PHP_EOL;
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
															echo '<' . $remap_gpx_url . '><![CDATA[' . $gpx_url . ']]></' . $remap_gpx_url . '>'.PHP_EOL;
															echo '<' . $remap_gpx_panel . '><![CDATA[' . $gpx_panel . ']]></' . $remap_gpx_panel . '>'.PHP_EOL;
														echo '</data>'.PHP_EOL;
														echo '</mapsmarker>';
													} //info: end format marker / update
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
													} //info: end query check marker ok / update
												} //info: end update marker
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
											} //info: end check if query_result markers >=1 / update
										} else if ($type == 'layer') {
											$query_view = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE `id` = %d", $id), ARRAY_A);
											if (count($query_view) >= 1) {
												$name = isset($_POST['name']) ? $_POST['name'] : (isset($_GET['name']) ? $_GET['name'] : $query_view['name']);
												$name_quotes = str_replace("\\\\","/", str_replace("\"", "'", $name));
												$basemap = isset($_POST['basemap']) && in_array($_POST['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_POST['basemap'] : (isset($_GET['basemap']) && in_array($_GET['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $_GET['basemap'] : $query_view['basemap']);
												$layerzoom = isset($_POST['layerzoom']) ? intval($_POST['layerzoom']) : (isset($_GET['layerzoom']) ? intval($_GET['layerzoom']) : $query_view['layerzoom']);
												$mapwidth = isset($_POST['mapwidth']) ? $_POST['mapwidth'] : (isset($_GET['mapwidth']) ? $_GET['mapwidth'] : $query_view['mapwidth']);
												$mapwidthunit = ( isset($_POST['mapwidthunit']) && ( ($_POST['mapwidthunit'] == 'px') || ($_POST['mapwidthunit'] == '%') ) ) ? $_POST['mapwidthunit'] : ( isset($_GET['mapwidthunit']) && ( ($_GET['mapwidthunit'] == 'px') || ($_GET['mapwidthunit'] == '%')  ) ? $_GET['mapwidthunit'] : $query_view['mapwidthunit']);
												$mapheight = isset($_POST['mapheight']) ? $_POST['mapheight'] : (isset($_GET['mapheight']) ? $_GET['mapheight'] : $query_view['mapheight']);
												$panel = ( isset($_POST['panel']) && ( ($_POST['panel'] == '0') || ($_POST['panel'] == '1')) ) ? $_POST['panel'] : ( isset($_GET['panel']) && ( ($_GET['panel'] == '0') || ($_GET['panel'] == '1') ) ? $_GET['panel'] : $query_view['panel']);
												$layerviewlat = isset($_POST['layerviewlat']) ? floatval($_POST['layerviewlat']) : (isset($_GET['layerviewlat']) ? floatval($_GET['layerviewlat']) : $query_view['layerviewlat']);
												$layerviewlon = isset($_POST['layerviewlon']) ? floatval($_POST['layerviewlon']) : (isset($_GET['layerviewlon']) ? floatval($_GET['layerviewlon']) : $query_view['layerviewlon']);
												$createdby = isset($_POST['createdby']) ? esc_html($_POST['createdby']) : (isset($_GET['createdby']) ? esc_html($_GET['createdby']) : $query_view['createdby']);
												$createdon = isset($_POST['createdon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['createdon'] : (isset($_GET['createdon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['createdon'] : $query_view['createdon']);
												$updatedby = isset($_POST['updatedby']) ? esc_html($_POST['updatedby']) : (isset($_GET['updatedby']) ? esc_html($_GET['updatedby']) : $query_view['updatedby']);
												$updatedon = isset($_POST['updatedon']) && ( $_POST['createdon'] == date('Y-m-d H:i:s',strtotime($_POST['createdon'])) ) ? $_POST['updatedon'] : (isset($_GET['updatedon']) && ( $_GET['createdon'] == date('Y-m-d H:i:s',strtotime($_GET['createdon'])) ) ? $_GET['updatedon'] : current_time('mysql',0));
												$controlbox = ( isset($_POST['controlbox']) && ( ($_POST['controlbox'] == '0') || ($_POST['controlbox'] == '1') || ($_POST['controlbox'] == '2')) ) ? $_POST['controlbox'] : ( isset($_GET['controlbox']) && ( ($_GET['controlbox'] == '0') || ($_GET['controlbox'] == '1') || ($_GET['controlbox'] == '2') ) ? $_GET['controlbox'] : $query_view['controlbox']);
												$overlays_custom = ( isset($_POST['overlays_custom']) && ( ($_POST['overlays_custom'] == '0') || ($_POST['overlays_custom'] == '1')) ) ? $_POST['overlays_custom'] : ( isset($_GET['overlays_custom']) && ( ($_GET['overlays_custom'] == '0') || ($_GET['overlays_custom'] == '1') ) ? $_GET['overlays_custom'] : $query_view['overlays_custom']);
												$overlays_custom2 = ( isset($_POST['overlays_custom2']) && ( ($_POST['overlays_custom2'] == '0') || ($_POST['overlays_custom2'] == '1')) ) ? $_POST['overlays_custom2'] : ( isset($_GET['overlays_custom2']) && ( ($_GET['overlays_custom2'] == '0') || ($_GET['overlays_custom2'] == '1') ) ? $_GET['overlays_custom2'] : $query_view['overlays_custom2']);
												$overlays_custom3 = ( isset($_POST['overlays_custom3']) && ( ($_POST['overlays_custom3'] == '0') || ($_POST['overlays_custom3'] == '1')) ) ? $_POST['overlays_custom3'] : ( isset($_GET['overlays_custom3']) && ( ($_GET['overlays_custom3'] == '0') || ($_GET['overlays_custom3'] == '1') ) ? $_GET['overlays_custom3'] : $query_view['overlays_custom3']);
												$overlays_custom4 = ( isset($_POST['overlays_custom4']) && ( ($_POST['overlays_custom4'] == '0') || ($_POST['overlays_custom4'] == '1')) ) ? $_POST['overlays_custom4'] : ( isset($_GET['overlays_custom4']) && ( ($_GET['overlays_custom4'] == '0') || ($_GET['overlays_custom4'] == '1') ) ? $_GET['overlays_custom4'] : $query_view['overlays_custom4']);
												$wms = ( isset($_POST['wms']) && ( ($_POST['wms'] == '0') || ($_POST['wms'] == '1')) ) ? $_POST['wms'] : ( isset($_GET['wms']) && ( ($_GET['wms'] == '0') || ($_GET['wms'] == '1') ) ? $_GET['wms'] : $query_view['wms']);
												$wms2 = ( isset($_POST['wms2']) && ( ($_POST['wms2'] == '0') || ($_POST['wms2'] == '1')) ) ? $_POST['wms2'] : ( isset($_GET['wms2']) && ( ($_GET['wms2'] == '0') || ($_GET['wms2'] == '1') ) ? $_GET['wms2'] : $query_view['wms2']);
												$wms3 = ( isset($_POST['wms3']) && ( ($_POST['wms3'] == '0') || ($_POST['wms3'] == '1')) ) ? $_POST['wms3'] : ( isset($_GET['wms3']) && ( ($_GET['wms3'] == '0') || ($_GET['wms3'] == '1') ) ? $_GET['wms3'] : $query_view['wms3']);
												$wms4 = ( isset($_POST['wms4']) && ( ($_POST['wms4'] == '0') || ($_POST['wms4'] == '1')) ) ? $_POST['wms4'] : ( isset($_GET['wms4']) && ( ($_GET['wms4'] == '0') || ($_GET['wms4'] == '1') ) ? $_GET['wms4'] : $query_view['wms4']);
												$wms5 = ( isset($_POST['wms5']) && ( ($_POST['wms5'] == '0') || ($_POST['wms5'] == '1')) ) ? $_POST['wms5'] : ( isset($_GET['wms5']) && ( ($_GET['wms5'] == '0') || ($_GET['wms5'] == '1') ) ? $_GET['wms5'] : $query_view['wms5']);
												$wms6 = ( isset($_POST['wms6']) && ( ($_POST['wms6'] == '0') || ($_POST['wms6'] == '1')) ) ? $_POST['wms6'] : ( isset($_GET['wms6']) && ( ($_GET['wms6'] == '0') || ($_GET['wms6'] == '1') ) ? $_GET['wms6'] : $query_view['wms6']);
												$wms7 = ( isset($_POST['wms7']) && ( ($_POST['wms7'] == '0') || ($_POST['wms7'] == '1')) ) ? $_POST['wms7'] : ( isset($_GET['wms7']) && ( ($_GET['wms7'] == '0') || ($_GET['wms7'] == '1') ) ? $_GET['wms7'] : $query_view['wms7']);
												$wms8 = ( isset($_POST['wms8']) && ( ($_POST['wms8'] == '0') || ($_POST['wms8'] == '1')) ) ? $_POST['wms8'] : ( isset($_GET['wms8']) && ( ($_GET['wms8'] == '0') || ($_GET['wms8'] == '1') ) ? $_GET['wms8'] : $query_view['wms8']);
												$wms9 = ( isset($_POST['wms9']) && ( ($_POST['wms9'] == '0') || ($_POST['wms9'] == '1')) ) ? $_POST['wms9'] : ( isset($_GET['wms9']) && ( ($_GET['wms9'] == '0') || ($_GET['wms9'] == '1') ) ? $_GET['wms9'] : $query_view['wms9']);
												$wms10 = ( isset($_POST['wms10']) && ( ($_POST['wms10'] == '0') || ($_POST['wms10'] == '1')) ) ? $_POST['wms10'] : ( isset($_GET['wms10']) && ( ($_GET['wms10'] == '0') || ($_GET['wms10'] == '1') ) ? $_GET['wms10'] : $query_view['wms10']);
												$listmarkers = ( isset($_POST['listmarkers']) && ( ($_POST['listmarkers'] == '0') || ($_POST['listmarkers'] == '1')) ) ? $_POST['listmarkers'] : ( isset($_GET['listmarkers']) && ( ($_GET['listmarkers'] == '0') || ($_GET['listmarkers'] == '1') ) ? $_GET['listmarkers'] : $query_view['listmarkers']);
												$multi_layer_map = ( isset($_POST['multi_layer_map']) && ( ($_POST['multi_layer_map'] == '0') || ($_POST['multi_layer_map'] == '1')) ) ? $_POST['multi_layer_map'] : ( isset($_GET['multi_layer_map']) && ( ($_GET['multi_layer_map'] == '0') || ($_GET['multi_layer_map'] == '1') ) ? $_GET['multi_layer_map'] : $query_view['multi_layer_map']);
												$multi_layer_map_list = isset($_POST['multi_layer_map_list']) ? $_POST['multi_layer_map_list'] : (isset($_GET['multi_layer_map_list']) ? $_GET['multi_layer_map_list'] : $query_view['multi_layer_map_list']);
												$address = isset($_POST['address']) ? $_POST['address'] : (isset($_GET['address']) ? $_GET['address'] : $query_view['address']);
												$clustering = ( isset($_POST['clustering']) && ( ($_POST['clustering'] == '0') || ($_POST['clustering'] == '1')) ) ? $_POST['clustering'] : ( isset($_GET['clustering']) && ( ($_GET['clustering'] == '0') || ($_GET['clustering'] == '1') ) ? $_GET['clustering'] : $query_view['clustering']);
												$gpx_url = isset($_POST['gpx_url']) ? esc_url_raw($_POST['gpx_url']) : (isset($_GET['gpx_url']) ? esc_url_raw($_GET['gpx_url']) : esc_url_raw($query_view['gpx_url']));
												$gpx_panel = ( isset($_POST['gpx_panel']) && ( ($_POST['gpx_panel'] == '0') || ($_POST['gpx_panel'] == '1')) ) ? $_POST['gpx_panel'] : ( isset($_GET['gpx_panel']) && ( ($_GET['gpx_panel'] == '0') || ($_GET['gpx_panel'] == '1') ) ? $_GET['gpx_panel'] : $query_view['gpx_panel']);
												$mlm_filter = ( isset($_POST['mlm_filter']) && ( ($_POST['mlm_filter'] == '0') || ($_POST['mlm_filter'] == '1')) ) ? $_POST['mlm_filter'] : ( isset($_GET['mlm_filter']) && ( ($_GET['mlm_filter'] == '0') || ($_GET['mlm_filter'] == '1') ) ? $_GET['mlm_filter'] : '0');
												$mlm_filter_details_input = isset($_POST['mlm_filter_details']) ? stripslashes($_POST['mlm_filter_details']) : (isset($_GET['mlm_filter_details']) ? stripslashes($_GET['mlm_filter_details']) : '');
												//info: make sure input is valid JSON
												$mlm_filter_details_input_decoded = json_decode($mlm_filter_details_input);
												if($mlm_filter_details_input_decoded === NULL) {
													$mlm_filter_details = '';
												} else {
													$mlm_filter_details = $mlm_filter_details_input;
												}

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
												if($mlm_filter_details_input_decoded == NULL){
													$query_update = $wpdb->prepare( "UPDATE `$table_name_layers` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d, `mlm_filter` = %d WHERE `id` = %d", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter, $id );
												}else{
													$query_update = $wpdb->prepare( "UPDATE `$table_name_layers` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d, `mlm_filter` = %d, `mlm_filter_details` = %s WHERE `id` = %d", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter, $mlm_filter_details, $id );
												}
												$result_update = $wpdb->query( $query_update );
												if ($result_update == TRUE) {
													if ($format == 'json') {
														header('Cache-Control: no-cache, must-revalidate');
														header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
														header('Content-type: application/json; charset=utf-8');
														if ($callback != NULL) { echo $callback . '('; }
														echo '{'.PHP_EOL;
														echo '"success":true,'.PHP_EOL;
														echo '"message":"' . esc_attr__('Layer has been successfully updated','lmm') . '",'.PHP_EOL;
														echo '"data": {'.PHP_EOL;
															echo '"' . $remap_id . '":"' . $id . '",'.PHP_EOL;
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
															echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
															echo '"' . $remap_clustering . '":"' . $clustering . '",'.PHP_EOL;
															echo '"' . $remap_gpx_url . '":"' . $gpx_url . '",'.PHP_EOL;
															echo '"' . $remap_gpx_panel . '":"' . $gpx_panel . '",'.PHP_EOL;
															echo '"' . $remap_mlm_filter . '":"' . $mlm_filter . '",'.PHP_EOL;
															echo '"' . $remap_mlm_filter_details . '":"' . $mlm_filter_details . '"'.PHP_EOL;
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
														echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . ', ' . $remap_clustering . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . ', '.$remap_mlm_filter.', '. $remap_mlm_filter_details .'))>'.PHP_EOL;
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
														echo '<!ELEMENT ' . $remap_clustering . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $mlm_filter . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $mlm_filter_details . ' (#PCDATA)>'.PHP_EOL;
														echo ']>'.PHP_EOL;
														echo '<mapsmarker>'.PHP_EOL;
														echo '<success>true</success>'.PHP_EOL;
														echo '<message>' . esc_attr__('Layer has been successfully updated','lmm') . '</message>'.PHP_EOL;
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
															echo '<' . $remap_clustering . '>' . $clustering . '</' . $remap_clustering . '>'.PHP_EOL;
															echo '<' . $remap_gpx_url . '>' . $gpx_url . '</' . $remap_gpx_url . '>'.PHP_EOL;
															echo '<' . $remap_gpx_panel . '>' . $gpx_panel . '</' . $remap_gpx_panel . '>'.PHP_EOL;
															echo '<' . $remap_mlm_filter . '>' . $mlm_filter . '</' . $remap_mlm_filter . '>'.PHP_EOL;
															echo '<' . $remap_mlm_filter_details . '>' . $mlm_filter_details . '</' . $remap_mlm_filter_details . '>'.PHP_EOL;
														echo '</data>'.PHP_EOL;
														echo '</mapsmarker>';
													} //info: end format layer / update
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
													} //info: end query check layer ok / update
												} //info: end update layer
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
											} //info: end check if query_result layers >=1 / update
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
										} //info: end type check / update
									} else {
										if ($format == 'json') {
											header('Content-type: application/json; charset=utf-8');
											if ($callback != NULL) { echo $callback . '('; }
											echo '{'.PHP_EOL;
											echo '"success":false,'.PHP_EOL;
											echo '"message":"' . esc_attr__('API action is not allowed','lmm') . '",'.PHP_EOL;
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
									} //info: end permission check / update
								/******************************
								* action delete                  *
								******************************/
								} else if ($action == 'delete') {
									if ( $lmm_options['api_permissions_delete'] == TRUE ) {
										if ($type == 'marker') {
											$query_result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `id` = %d", $id), ARRAY_A);
											if (count($query_result) >= 1) {
												//info: delete qr code cache image
												if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $id . '.png') ) {
													unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $id . '.png');
												}
												$query_delete = $wpdb->prepare( "DELETE FROM `$table_name_markers` WHERE `id` = %d", $id );
												$result_delete = $wpdb->query( $query_delete );
												if ($result_delete == TRUE) {
													$mpopuptext = stripslashes(str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$query_result['popuptext'])));
													$address = stripslashes(str_replace('"', '\'', $query_result['address']));
													if ($format == 'json') {
														header('Cache-Control: no-cache, must-revalidate');
														header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
														header('Content-type: application/json; charset=utf-8');

														if ($callback != NULL) { echo $callback . '('; }
														echo '{'.PHP_EOL;
														echo '"success":true,'.PHP_EOL;
														echo '"message":"' . esc_attr__('Marker has been successfully deleted','lmm') . '",'.PHP_EOL;
														echo '"data": {'.PHP_EOL;
															echo '"' . $remap_id . '":"' . $query_result['id'] . '",'.PHP_EOL;
															echo '"' . $remap_markername . '":"' . stripslashes(esc_js($query_result['markername'])) . '",'.PHP_EOL;
															echo '"' . $remap_basemap . '":"' . $query_result['basemap'] . '",'.PHP_EOL;
															echo '"' . $remap_layer . '":[';
															$assigned_layers = json_decode($query_result['layer']);
															$assigned_layers_sanitized = array();
															foreach ($assigned_layers as $assigned_layer) {
																$assigned_layers_sanitized[] = '"' . $assigned_layer . '"';
															}
															echo implode( ', ', $assigned_layers_sanitized ) . '],'.PHP_EOL;
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
															echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
															echo '"' . $remap_gpx_url . '":"' . $query_result['gpx_url'] . '",'.PHP_EOL;
															echo '"' . $remap_gpx_panel . '":"' . $query_result['gpx_panel'] . '"'.PHP_EOL;
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
														echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layers . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . '))>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_layers . ' ((' . $remap_layer . '+))>'.PHP_EOL;
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
														echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
														echo ']>'.PHP_EOL;
														echo '<mapsmarker>'.PHP_EOL;
														echo '<success>true</success>'.PHP_EOL;
														echo '<message>' . esc_attr__('Marker has been successfully deleted','lmm') . '</message>'.PHP_EOL;
														echo '<data>'.PHP_EOL;
															echo '<' . $remap_id . '>' . $query_result['id'] . '</' . $remap_id . '>'.PHP_EOL;
															echo '<' . $remap_markername . '><![CDATA[' . stripslashes(esc_js($query_result['markername'])) . ']]></' . $remap_markername . '>'.PHP_EOL;
															echo '<' . $remap_basemap . '>' . $query_result['basemap'] . '</' . $remap_basemap . '>'.PHP_EOL;
															echo '<' . $remap_layers . '>'.PHP_EOL;
															$assigned_layers = json_decode($query_result['layer']);
															foreach ($assigned_layers as $assigned_layer) {
																echo '<' . $remap_layer . '>' . $assigned_layer . '</' . $remap_layer . '>'.PHP_EOL;
															}
															echo '</' . $remap_layers . '>'.PHP_EOL;
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
															echo '<' . $remap_gpx_url . '>' . $query_result['gpx_url'] . '</' . $remap_gpx_url . '>'.PHP_EOL;
															echo '<' . $remap_gpx_panel . '>' . $query_result['gpx_panel'] . '</' . $remap_gpx_panel . '>'.PHP_EOL;
														echo '</data>'.PHP_EOL;
														echo '</mapsmarker>';
													} //info: end format marker / view
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
													} //info: end query check marker ok / delete
												} //info: end delete marker
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
												$delete_markers = ( isset($_POST['delete_markers']) && (($_POST['delete_markers'] == 'true') || ($_POST['delete_markers'] == 'false')) ) ? $_POST['delete_markers'] : ( (isset($_GET['delete_markers']) && ( ($_GET['delete_markers'] == 'true') || ($_GET['delete_markers'] == 'false')) ) ? $_GET['delete_markers'] :  'false');
												if ($delete_markers == 'false') {
													//info: delete qr code cache image for layer
													if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $id . '.png') ) {
														unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $id . '.png');
													}
													$associated_markers = $wpdb->get_results($wpdb->prepare("SELECT id,layer FROM `". $table_name_markers ."` WHERE layer LIKE '%%\"%d\"%%' ", $id) );
													foreach($associated_markers as $marker){
														$layer = json_decode($marker->layer, true);
														$marker_key = array_search($id, $layer);
														unset($layer[$marker_key]);
														//info: if not no layers left, make the marker unassigned.
														if(count($layer) == 0){
															$layer[] = "0";
															$layer = array_values($layer);
														}
														$wpdb->update($table_name_markers,
																	  array('layer'=> json_encode($layer)),
																	  array('id' => $marker->id)
																	  );
													}
													$query_delete = $wpdb->prepare( "DELETE FROM `$table_name_layers` WHERE `id` = %d", $id );
													$result_delete = $wpdb->query( $query_delete );
												} else if ($delete_markers == 'true') {
													//info: delete qr code cache images for assigned markers
													$layer_marker_list_qr = $wpdb->get_results('SELECT m.id as markerid,m.layer as mlayer,l.id as lid FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id=' . $id, ARRAY_A);

													foreach ($layer_marker_list_qr as $row){
														if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png') ) {
															unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png');
														}
													}
													//info: delete qr code cache image for layer
													if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $id . '.png') ) {
														unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $id . '.png');
													}
													$multi_layer_map_list_exploded = $wpdb->get_var($wpdb->prepare('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id=%d',$id));
													if(!is_null($multi_layer_map_list_exploded)){
														$multi_layer_map_list_exploded = explode(",", $multi_layer_map_list_exploded);
													}
													if(is_null($multi_layer_map_list_exploded)){
														$markers_to_delete = $wpdb->get_results($wpdb->prepare("SELECT id,layer FROM `$table_name_markers` WHERE `layer` LIKE %s ",'%"'.$id.'"%'));
													}
													else {
														//info: delete markers of mlm layers
														$markers_to_delete = array();
														foreach($multi_layer_map_list_exploded as $lid){
															$markers = $wpdb->get_results("SELECT id,layer FROM `$table_name_markers` WHERE `layer` LIKE '%\"".$lid."\"%'");
															if(!is_null($markers)){
																$markers_to_delete = array_merge($markers_to_delete,$markers);
															}
														}
													}
													foreach($markers_to_delete as $row){
														$layer = json_decode($row->layer);
														if(count($layer) === 1){
															$wpdb->query("DELETE FROM `$table_name_markers` WHERE `id` =".$row->id);
														} else {
															if(!is_null($multi_layer_map_list_exploded)){
																foreach($multi_layer_map_list_exploded as $mlm_layer){
																	$key = array_search($mlm_layer, $layer);
																	if($key !== FALSE)
																		unset( $layer[$key] );
																}
															}
															$key = array_search($id, $layer);
															if($key !== FALSE)
																unset($layer[$key]);
															$layer = array_values( $layer );
															$wpdb->update($table_name_markers,array('layer' => json_encode($layer) ), array('id' => $row->id));
														}
													}
													if(!is_null($multi_layer_map_list_exploded)){
														$wpdb->query( "DELETE FROM `$table_name_layers` WHERE `id` IN (" . htmlspecialchars(implode(',',$multi_layer_map_list_exploded)) . ")");
													}
													$query_delete = $wpdb->prepare( "DELETE FROM `$table_name_layers` WHERE `id` = %d", $id );
													$result_delete = $wpdb->query( $query_delete );
												}
												if ($result_delete == TRUE) {
													$delete_info = ($delete_markers == 'false') ? esc_attr__('Layer has been successfully deleted (assigned markers have not been deleted)','lmm') : esc_attr__('Layer and assigned markers have been successfully deleted (or the reference to the layer has been removed if marker was assigned to multiple layers)','lmm');
													if ($format == 'json') {
														$address = stripslashes(str_replace('"', '\'', $query_result['address']));
														header('Cache-Control: no-cache, must-revalidate');
														header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
														header('Content-type: application/json; charset=utf-8');
														if ($callback != NULL) { echo $callback . '('; }
														echo '{'.PHP_EOL;
														echo '"success":true,'.PHP_EOL;
														echo '"message":"' . $delete_info . '",'.PHP_EOL;
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
															echo '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
															echo '"' . $remap_clustering . '":"' . $query_result['clustering'] . '",'.PHP_EOL;
															echo '"' . $remap_gpx_url . '":"' . $query_result['gpx_url'] . '",'.PHP_EOL;
															echo '"' . $remap_gpx_panel . '":"' . $query_result['gpx_panel'] . '",'.PHP_EOL;
															echo '"' . $remap_mlm_filter . '":"' . $query_result['mlm_filter'] . '",'.PHP_EOL;
															echo '"' . $remap_mlm_filter_details . '":"' . $query_result['mlm_filter_details'] . '"'.PHP_EOL;
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
														echo '<!ELEMENT data ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . ', ' . $remap_clustering . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . ', '.$remap_mlm_filter.', '. $remap_mlm_filter_details . '))>'.PHP_EOL;
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
														echo '<!ELEMENT ' . $remap_clustering . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_mlm_filter . ' (#PCDATA)>'.PHP_EOL;
														echo '<!ELEMENT ' . $remap_mlm_filter_details . ' (#PCDATA)>'.PHP_EOL;
														echo ']>'.PHP_EOL;
														echo '<mapsmarker>'.PHP_EOL;
														echo '<success>true</success>'.PHP_EOL;
														echo '<message>' . $delete_info . '</message>'.PHP_EOL;
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
															echo '<' . $remap_clustering . '>' . $query_result['clustering'] . '</' . $remap_clustering . '>'.PHP_EOL;
															echo '<' . $remap_gpx_url . '>' . $query_result['gpx_url'] . '</' . $remap_gpx_url . '>'.PHP_EOL;
															echo '<' . $remap_gpx_panel . '>' . $query_result['gpx_panel'] . '</' . $remap_gpx_panel . '>'.PHP_EOL;
															echo '<' . $remap_mlm_filter . '>' . $query_result['mlm_filter'] . '</' . $remap_mlm_filter . '>'.PHP_EOL;
															echo '<' . $remap_mlm_filter_details . '>' . $query_result['mlm_filter_details'] . '</' . $remap_mlm_filter_details . '>'.PHP_EOL;
														echo '</data>'.PHP_EOL;
														echo '</mapsmarker>';
													} //info: end format layer / view
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
													} //info: end query check layer ok / delete
												} //info: end delete layer
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
											} //info: end check if query_result markers >=1 / view
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
										} //info: end type check / delete
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
									} //info: end permission check / delete
								/******************************
								* action search                  *
								******************************/
								} else if ($action == 'search') {

									if ( $lmm_options['api_permissions_search'] == TRUE ) {

										$searchkey = isset($_POST['searchkey']) ? $_POST['searchkey'] : (isset($_GET['searchkey']) ? $_GET['searchkey'] : '');
										if ( ($searchkey == 'id') || ($searchkey == 'markername') || ($searchkey == 'basemap') || ($searchkey == 'layer') || ($searchkey == 'lat') || ($searchkey == 'lon') || ($searchkey == 'icon') || ($searchkey == 'popuptext') || ($searchkey == 'zoom') || ($searchkey == 'openpopup') || ($searchkey == 'mapwidth') || ($searchkey == 'mapwidthunit') || ($searchkey == 'mapheight') || ($searchkey == 'panel') || ($searchkey == 'createdby') || ($searchkey == 'createdon') || ($searchkey == 'updatedby') || ($searchkey == 'updatedon') || ($searchkey == 'controlbox') || ($searchkey == 'overlays_custom') || ($searchkey == 'overlays_custom2') || ($searchkey == 'overlays_custom3') || ($searchkey == 'overlays_custom4') || ($searchkey == 'wms') || ($searchkey == 'wms2') || ($searchkey == 'wms3') || ($searchkey == 'wms4') || ($searchkey == 'wms5') || ($searchkey == 'wms6') || ($searchkey == 'wms7') || ($searchkey == 'wms8') || ($searchkey == 'wms9') || ($searchkey == 'wms10') || ($searchkey == 'kml_timestamp') || ($searchkey == 'address') || ($searchkey == 'gpx_url') || ($searchkey == 'gpx_panel') || ($searchkey == 'name') || ($searchkey == 'layerzoom') || ($searchkey == 'layerviewlat') || ($searchkey == 'layerviewlon') || ($searchkey == 'listmarkers') || ($searchkey == 'multi_layer_map') || ($searchkey == 'multi_layer_map_list') || ($searchkey == 'clustering') || ($searchkey == 'boundingbox') ) {


											$searchvalue = isset($_POST['searchvalue']) ? esc_sql($_POST['searchvalue']) : (isset($_GET['searchvalue']) ? esc_sql($_GET['searchvalue']) : '');
											if ( ($searchvalue != NULL) || ( ($searchvalue == NULL) && (($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') || ($searchkey == 'boundingbox') ) ) ) { //info: seachvalue optional for date and boundingbox searches!

												if ($type == 'marker') {

													if ($searchkey == 'layer') {
														$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `$searchkey` LIKE %s", '%' . intval($searchvalue) . '%'), ARRAY_A);
													} else if ( ($searchkey == 'id') || ($searchkey == 'zoom') || ($searchkey == 'openpopup') || ($searchkey == 'mapwidth') || ($searchkey == 'mapheight') || ($searchkey == 'panel') || ($searchkey == 'controlbox') || ($searchkey == 'overlays_custom') || ($searchkey == 'overlays_custom2') || ($searchkey == 'overlays_custom3') || ($searchkey == 'overlays_custom4') || ($searchkey == 'wms') || ($searchkey == 'wms2') || ($searchkey == 'wms3') || ($searchkey == 'wms4') || ($searchkey == 'wms5') || ($searchkey == 'wms6') || ($searchkey == 'wms7') || ($searchkey == 'wms8') || ($searchkey == 'wms9') || ($searchkey == 'wms10') || ($searchkey == 'gpx_panel') ) {
														$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `$searchkey` = %d", $searchvalue), ARRAY_A);
													} else if ( ($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') )   {
														$date_from = isset($_POST['date_from']) ? strtotime($_POST['date_from']) : (isset($_GET['date_from']) ? strtotime($_GET['date_from']) : '');
														$date_to = isset($_POST['date_to']) ? strtotime($_POST['date_to']) : (isset($_GET['date_to']) ? strtotime($_GET['date_to']) : '');
														if ( ($date_from != NULL) && ($date_to == NULL) ) {
															$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `$searchkey` > FROM_UNIXTIME(%d)", $date_from), ARRAY_A);
														} else if ( ($date_from == NULL) && ($date_to != NULL) ) {
															$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `$searchkey` < FROM_UNIXTIME(%d)", $date_to), ARRAY_A);
														} else if ( ($date_from != NULL) && ($date_to != NULL) ) {
															$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_markers` WHERE `$searchkey` > FROM_UNIXTIME(%d) AND `$searchkey` < FROM_UNIXTIME(%d)", $date_from, $date_to), ARRAY_A);
														} else { //info: if ($date_from == NULL) && ($date_to == NULL)
															if ($format == 'json') {
																header('Content-type: application/json; charset=utf-8');
																if ($callback != NULL) { echo $callback . '('; }
																echo '{'.PHP_EOL;
																echo '"success":false,'.PHP_EOL;
																echo '"message":"' . esc_attr__('API parameter date_from or date_to has be set when searching date fields','lmm') . '",'.PHP_EOL;
																echo '"data": { }'.PHP_EOL;
																echo '}';
																if ($callback != NULL) { echo ');'; }
															} else if ($format == 'xml') {
																header('Content-type: application/xml; charset=utf-8');
																echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
																echo '<mapsmarker>'.PHP_EOL;
																echo '<success>false</success>'.PHP_EOL;
																echo '<message>' . esc_attr__('API parameter date_from or date_to has be set when searching date fields','lmm') . '</message>'.PHP_EOL;
																echo '<data></data>'.PHP_EOL;
																echo '</mapsmarker>';
															}
														}

													} else if ($searchkey == 'boundingbox') {
														$lat_top_left = isset($_POST['lat_top_left']) ? floatval(str_replace(",",".", $_POST['lat_top_left'])) : (isset($_GET['lat_top_left']) ? floatval(str_replace(",",".", $_GET['lat_top_left'])) : '');
														$lon_top_left = isset($_POST['lon_top_left']) ? floatval(str_replace(",",".", $_POST['lon_top_left'])) : (isset($_GET['lon_top_left']) ? floatval(str_replace(",",".", $_GET['lon_top_left'])) : '');
														$lat_bottom_right = isset($_POST['lat_bottom_right']) ? floatval(str_replace(",",".", $_POST['lat_bottom_right'])) : (isset($_GET['lat_bottom_right']) ? floatval(str_replace(",",".", $_GET['lat_bottom_right'])) : '');
														$lon_bottom_right = isset($_POST['lon_bottom_right']) ? floatval(str_replace(",",".", $_POST['lon_bottom_right'])) : (isset($_GET['lon_bottom_right']) ? floatval(str_replace(",",".", $_GET['lon_bottom_right'])) : '');
														if ( ($lat_top_left == NULL) || ($lon_top_left == NULL) || ($lat_bottom_right == NULL) || ($lon_bottom_right == NULL) ) {
															if ($format == 'json') {
																header('Content-type: application/json; charset=utf-8');
																if ($callback != NULL) { echo $callback . '('; }
																echo '{'.PHP_EOL;
																echo '"success":false,'.PHP_EOL;
																echo '"message":"' . esc_attr__('API parameters lat_top_left, lon_top_left, lat_bottom_right and lon_bottom_right have be set when performing a boundingbox search','lmm') . '",'.PHP_EOL;
																echo '"data": { }'.PHP_EOL;
																echo '}';
																if ($callback != NULL) { echo ');'; }
															} else if ($format == 'xml') {
																header('Content-type: application/xml; charset=utf-8');
																echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
																echo '<mapsmarker>'.PHP_EOL;
																echo '<success>false</success>'.PHP_EOL;
																echo '<message>' . esc_attr__('API parameters lat_top_left, lon_top_left, lat_bottom_right and lon_bottom_right have be set when performing a boundingbox search','lmm') . '</message>'.PHP_EOL;
																echo '<data></data>'.PHP_EOL;
																echo '</mapsmarker>';
															}
															exit();
														} else {
															$query_result = $wpdb->get_results( "SELECT * FROM `$table_name_markers` WHERE `lat` <= '$lat_top_left' AND `lon` >= '$lon_top_left' AND `lat` >= '$lat_bottom_right' AND `lon` <= '$lon_bottom_right'", ARRAY_A);
														}
													} else { //info: markername, basemap, lat, lon, icon, popuptext, mapwidthunit, createdby, updatedby, address, gpx_url
														$query_result = $wpdb->get_results( "SELECT * FROM $table_name_markers WHERE $searchkey LIKE '%$searchvalue%'", ARRAY_A);
													}
													if (count($query_result) >= 1) {
														if ($format == 'json') {
															header('Cache-Control: no-cache, must-revalidate');
															header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
															header('Content-type: application/json; charset=utf-8');
															if ($callback != NULL) { echo $callback . '('; }
															echo '{'.PHP_EOL;
															echo '"success":true,'.PHP_EOL;
															echo '"searchkey":"' . $searchkey .'",'.PHP_EOL;
															if ($searchvalue != NULL) {
																if ( ($searchkey == 'id') || ($searchkey == 'layer') || ($searchkey == 'zoom') || ($searchkey == 'openpopup') || ($searchkey == 'mapwidth') || ($searchkey == 'mapheight') || ($searchkey == 'panel') || ($searchkey == 'controlbox') || ($searchkey == 'overlays_custom') || ($searchkey == 'overlays_custom2') || ($searchkey == 'overlays_custom3') || ($searchkey == 'overlays_custom4') || ($searchkey == 'wms') || ($searchkey == 'wms2') || ($searchkey == 'wms3') || ($searchkey == 'wms4') || ($searchkey == 'wms5') || ($searchkey == 'wms6') || ($searchkey == 'wms7') || ($searchkey == 'wms8') || ($searchkey == 'wms9') || ($searchkey == 'wms10') || ($searchkey == 'gpx_panel') ) {
																	echo '"searchvalue":"' . intval($searchvalue) .'",'.PHP_EOL;
																} else {
																	echo '"searchvalue":"' . $searchvalue .'",'.PHP_EOL;
																}
															}
															if (($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') ) {
																echo '"date_from":"' . date("Y-m-d H:i:s", $date_from) . '",'.PHP_EOL;
																echo '"date_to":"' . date("Y-m-d H:i:s", $date_to) . '",'.PHP_EOL;
															}
															if ($searchkey == 'boundingbox') {
																echo '"lat_top_left":"' . $lat_top_left . '",'.PHP_EOL;
																echo '"lon_top_left":"' . $lon_top_left . '",'.PHP_EOL;
																echo '"lat_bottom_right":"' . $lat_bottom_right . '",'.PHP_EOL;
																echo '"lon_bottom_right":"' . $lon_bottom_right . '",'.PHP_EOL;
															}
															echo '"searchresults":'.count($query_result).','.PHP_EOL;
															echo '"message":"' . sprintf(esc_attr__('Search completed - showing %d results below','lmm'), count($query_result)) . '",'.PHP_EOL;
															echo '"data": ['.PHP_EOL;
																$result ='';
																foreach ($query_result as $row) {
																	$mpopuptext = stripslashes(str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$row['popuptext'])));
																	$address = stripslashes(str_replace('"', '\'', $row['address']));
																	$result .= '{';
																		$result .= '"' . $remap_id . '":"' . $row['id'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_markername . '":"' . stripslashes(esc_js($row['markername'])) . '",'.PHP_EOL;
																		$result .= '"' . $remap_basemap . '":"' . $row['basemap'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_layer . '":[';
																		$assigned_layers = json_decode($row['layer']);
																		$assigned_layers_sanitized = array();
																		foreach ($assigned_layers as $assigned_layer) {
																			$assigned_layers_sanitized[] = '"' . $assigned_layer . '"';
																		}
																		$result .= implode( ', ', $assigned_layers_sanitized ) . '],'.PHP_EOL;
																		$result .= '"' . $remap_lat . '":"' . $row['lat'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_lon . '":"' . $row['lon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_icon . '":"' . $row['icon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_popuptext . '":"' . $mpopuptext . '",'.PHP_EOL;
																		$result .= '"' . $remap_zoom . '":"' . $row['zoom'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_openpopup . '":"' . $row['openpopup'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mapwidth . '":"' . $row['mapwidth'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mapwidthunit . '":"' . $row['mapwidthunit'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mapheight . '":"' . $row['mapheight'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_panel . '":"' . $row['panel'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_createdby . '":"' . $row['createdby'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_createdon . '":"' . $row['createdon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_updatedby . '":"' . $row['updatedby'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_updatedon . '":"' . $row['updatedon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_controlbox . '":"'.$row['controlbox'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom . '":"' . $row['overlays_custom'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom2 . '":"' . $row['overlays_custom2'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom3 . '":"' . $row['overlays_custom3'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom4 . '":"' . $row['overlays_custom4'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms . '":"' . $row['wms'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms2 . '":"' . $row['wms2'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms3 . '":"' . $row['wms3'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms4 . '":"' . $row['wms4'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms5 . '":"' . $row['wms5'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms6 . '":"' . $row['wms6'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms7 . '":"' . $row['wms7'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms8 . '":"' . $row['wms8'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms9 . '":"' . $row['wms9'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms10 . '":"' . $row['wms10'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_kml_timestamp . '":"' . $row['kml_timestamp'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
																		$result .= '"' . $remap_gpx_url . '":"' . $row['gpx_url'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_gpx_panel . '":"' . $row['gpx_panel'] . '"'.PHP_EOL;
																	$result .= '},';
																}
																echo $result = rtrim($result, ',');
															echo ']';//info: data
															echo '}';
															if ($callback != NULL) { echo ');'; }
														} else if ($format == 'xml') {
															header('Cache-Control: no-cache, must-revalidate');
															header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
															header('Content-type: application/xml; charset=utf-8');
															echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
															echo '<!DOCTYPE mapsmarker ['.PHP_EOL;
															if ($searchvalue != NULL) {
																echo '<!ELEMENT mapsmarker ((success, searchkey, searchvalue, searchresults, message, data))>'.PHP_EOL;
																echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchkey (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchvalue (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchresults (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
															} else if (($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') ) {
																echo '<!ELEMENT mapsmarker ((success, searchkey, date_from, date_to, searchresults, message, data))>'.PHP_EOL;
																echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchkey (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT date_from (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT date_to (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchresults (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
															} if ($searchkey == 'boundingbox') {
																echo '<!ELEMENT mapsmarker ((success, searchkey, lat_top_left, lon_top_left, lat_bottom_right, lon_bottom_right, searchresults, message, data))>'.PHP_EOL;
																echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchkey (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lat_top_left (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lon_top_left (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lat_bottom_right (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lon_bottom_right (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchresults (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
															}
															echo '<!ATTLIST mapsmarker xmlns:xsi CDATA #FIXED "http://www.w3.org/2001/XMLSchema-instance" >'.PHP_EOL;

															echo '<!ELEMENT data ((result+))>'.PHP_EOL;
															echo '<!ELEMENT result ((' . $remap_id . ', ' . $remap_markername . ', ' . $remap_basemap . ', ' . $remap_layers . ', ' . $remap_lat . ', ' . $remap_lon . ', ' . $remap_icon . ', ' . $remap_popuptext . ', ' . $remap_zoom . ', ' . $remap_openpopup . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_kml_timestamp . ', ' . $remap_address . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel . '))>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_id . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_markername . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_basemap . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_layers . ' ((' . $remap_layer . '+))>'.PHP_EOL;
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
															echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
															echo ']>'.PHP_EOL;
															echo '<mapsmarker>'.PHP_EOL;
															echo '<success>true</success>'.PHP_EOL;
															echo '<searchkey>' . $searchkey .'</searchkey>'.PHP_EOL;
															if ($searchvalue != NULL) {
																if ( ($searchkey == 'id') || ($searchkey == 'layer') || ($searchkey == 'zoom') || ($searchkey == 'openpopup') || ($searchkey == 'mapwidth') || ($searchkey == 'mapheight') || ($searchkey == 'panel') || ($searchkey == 'controlbox') || ($searchkey == 'overlays_custom') || ($searchkey == 'overlays_custom2') || ($searchkey == 'overlays_custom3') || ($searchkey == 'overlays_custom4') || ($searchkey == 'wms') || ($searchkey == 'wms2') || ($searchkey == 'wms3') || ($searchkey == 'wms4') || ($searchkey == 'wms5') || ($searchkey == 'wms6') || ($searchkey == 'wms7') || ($searchkey == 'wms8') || ($searchkey == 'wms9') || ($searchkey == 'wms10') || ($searchkey == 'gpx_panel') ) {
																	echo '<searchvalue>' . intval($searchvalue) .'</searchvalue>'.PHP_EOL;
																} else {
																	echo '<searchvalue>' . $searchvalue .'</searchvalue>'.PHP_EOL;
																}
															}
															if (($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') ) {
																echo '<date_from>' . date("Y-m-d H:i:s", $date_from) . '</date_from>'.PHP_EOL;
																echo '<date_to>' . date("Y-m-d H:i:s", $date_to) . '</date_to>'.PHP_EOL;
															}
															if ($searchkey == 'boundingbox') {
																echo '<lat_top_left>' . $lat_top_left . '</lat_top_left>'.PHP_EOL;
																echo '<lon_top_left>' . $lon_top_left . '</lon_top_left>'.PHP_EOL;
																echo '<lat_bottom_right>' . $lat_bottom_right . '</lat_bottom_right>'.PHP_EOL;
																echo '<lon_bottom_right>' . $lon_bottom_right . '</lon_bottom_right>'.PHP_EOL;
															}
															echo '<searchresults>'.count($query_result).'</searchresults>'.PHP_EOL;
															echo '<message>' . sprintf(esc_attr__('Search completed - showing %d results below','lmm'), count($query_result)) . '</message>'.PHP_EOL;
															echo '<data>'.PHP_EOL;
															foreach ($query_result as $row) {
																echo '<result>'.PHP_EOL;
																echo '<' . $remap_id . '>' . $row['id'] . '</' . $remap_id . '>'.PHP_EOL;
																echo '<' . $remap_markername . '><![CDATA[' . stripslashes(esc_js($row['markername'])) . ']]></' . $remap_markername . '>'.PHP_EOL;
																echo '<' . $remap_basemap . '>' . $row['basemap'] . '</' . $remap_basemap . '>'.PHP_EOL;
																echo '<' . $remap_layers . '>'.PHP_EOL;
																$assigned_layers = json_decode($row['layer']);
																foreach ($assigned_layers as $assigned_layer) {
																	echo '<' . $remap_layer . '>' . $assigned_layer . '</' . $remap_layer . '>'.PHP_EOL;
																}
																echo '</' . $remap_layers . '>'.PHP_EOL;
																echo '<' . $remap_lat . '>' . $row['lat'] . '</' . $remap_lat . '>'.PHP_EOL;
																echo '<' . $remap_lon . '>' . $row['lon'] . '</' . $remap_lon . '>'.PHP_EOL;
																echo '<' . $remap_icon . '><![CDATA[' . $row['icon'] . ']]></' . $remap_icon . '>'.PHP_EOL;
																echo '<' . $remap_popuptext . '><![CDATA[' . $mpopuptext . ']]></' . $remap_popuptext . '>'.PHP_EOL;
																echo '<' . $remap_zoom . '>' . $row['zoom'] . '</' . $remap_zoom . '>'.PHP_EOL;
																echo '<' . $remap_openpopup . '>' . $row['openpopup'] . '</' . $remap_openpopup . '>'.PHP_EOL;
																echo '<' . $remap_mapwidth . '>' . $row['mapwidth'] . '</' . $remap_mapwidth . '>'.PHP_EOL;
																echo '<' . $remap_mapwidthunit . '>' . $row['mapwidthunit'] . '</' . $remap_mapwidthunit . '>'.PHP_EOL;
																echo '<' . $remap_mapheight . '>' . $row['mapheight'] . '</' . $remap_mapheight . '>'.PHP_EOL;
																echo '<' . $remap_panel . '>' . $row['panel'] . '</' . $remap_panel . '>'.PHP_EOL;
																echo '<' . $remap_createdby . '><![CDATA[' . $row['createdby'] . ']]></' . $remap_createdby . '>'.PHP_EOL;
																echo '<' . $remap_createdon . '>' . $row['createdon'] . '</' . $remap_createdon . '>'.PHP_EOL;
																echo '<' . $remap_updatedby . '><![CDATA[' . $row['updatedby'] . ']]></' . $remap_updatedby . '>'.PHP_EOL;
																echo '<' . $remap_updatedon . '>' . $row['updatedon'] . '</' . $remap_updatedon . '>'.PHP_EOL;
																echo '<' . $remap_controlbox . '>' . $row['controlbox'] . '</' . $remap_controlbox . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom . '>' . $row['overlays_custom'] . '</' . $remap_overlays_custom . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom2 . '>' . $row['overlays_custom2'] . '</' . $remap_overlays_custom2 . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom3 . '>' . $row['overlays_custom3'] . '</' . $remap_overlays_custom3 . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom4 . '>' . $row['overlays_custom4'] . '</' . $remap_overlays_custom4 . '>'.PHP_EOL;
																echo '<' . $remap_wms . '>' . $row['wms'] . '</' . $remap_wms . '>'.PHP_EOL;
																echo '<' . $remap_wms2 . '>' . $row['wms2'] . '</' . $remap_wms2 . '>'.PHP_EOL;
																echo '<' . $remap_wms3 . '>' . $row['wms3'] . '</' . $remap_wms3 . '>'.PHP_EOL;
																echo '<' . $remap_wms4 . '>' . $row['wms4'] . '</' . $remap_wms4 . '>'.PHP_EOL;
																echo '<' . $remap_wms5 . '>' . $row['wms5'] . '</' . $remap_wms5 . '>'.PHP_EOL;
																echo '<' . $remap_wms6 . '>' . $row['wms6'] . '</' . $remap_wms6 . '>'.PHP_EOL;
																echo '<' . $remap_wms7 . '>' . $row['wms7'] . '</' . $remap_wms7 . '>'.PHP_EOL;
																echo '<' . $remap_wms8 . '>' . $row['wms8'] . '</' . $remap_wms8 . '>'.PHP_EOL;
																echo '<' . $remap_wms9 . '>' . $row['wms9'] . '</' . $remap_wms9 . '>'.PHP_EOL;
																echo '<' . $remap_wms10 . '>' . $row['wms10'] . '</' . $remap_wms10 . '>'.PHP_EOL;
																echo '<' . $remap_kml_timestamp . '>' . $row['kml_timestamp'] . '</' . $remap_kml_timestamp . '>'.PHP_EOL;
																echo '<' . $remap_address . '><![CDATA[' . $address . ']]></' . $remap_address . '>'.PHP_EOL;
																echo '<' . $remap_gpx_url . '><![CDATA[' . $row['gpx_url'] . ']]></' . $remap_gpx_url . '>'.PHP_EOL;
																echo '<' . $remap_gpx_panel . '><![CDATA[' . $row['gpx_panel'] . ']]></' . $remap_gpx_panel . '>'.PHP_EOL;
																echo '</result>'.PHP_EOL;
															}
															echo '</data>'.PHP_EOL;
															echo '</mapsmarker>';
														} //info: end display search results / marker
													} else {
														if ($format == 'json') {
															header('Content-type: application/json; charset=utf-8');
															if ($callback != NULL) { echo $callback . '('; }
															echo '{'.PHP_EOL;
															echo '"success":true,'.PHP_EOL;
															echo '"searchresults":0,'.PHP_EOL;
															echo '"message":"' . sprintf(esc_attr__('Search completed, no results found (searchkey: %s, searchvalue: %s)','lmm'), $searchkey, $searchvalue) . '",'.PHP_EOL;
															echo '"data": { }'.PHP_EOL;
															echo '}';
															if ($callback != NULL) { echo ');'; }
														} else if ($format == 'xml') {
															header('Content-type: application/xml; charset=utf-8');
															echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
															echo '<mapsmarker>'.PHP_EOL;
															echo '<success>true</success>'.PHP_EOL;
															echo '<searchresults>0</searchresults>'.PHP_EOL;
															echo '<message>' . sprintf(esc_attr__('Search completed, no results found (searchkey: %s, searchvalue: %s)','lmm'), $searchkey, $searchvalue) . '</message>'.PHP_EOL;
															echo '<data></data>'.PHP_EOL;
															echo '</mapsmarker>';
														}
													} //info: end count($query_result) marker
												} else if ($type == 'layer') {

													if ( ($searchkey == 'id') || ($searchkey == 'layerzoom') || ($searchkey == 'mapwidth') || ($searchkey == 'mapheight') || ($searchkey == 'panel') || ($searchkey == 'controlbox') || ($searchkey == 'overlays_custom') || ($searchkey == 'overlays_custom2') || ($searchkey == 'overlays_custom3') || ($searchkey == 'overlays_custom4') || ($searchkey == 'wms') || ($searchkey == 'wms2') || ($searchkey == 'wms3') || ($searchkey == 'wms4') || ($searchkey == 'wms5') || ($searchkey == 'wms6') || ($searchkey == 'wms7') || ($searchkey == 'wms8') || ($searchkey == 'wms9') || ($searchkey == 'wms10') || ($searchkey == 'listmarkers') || ($searchkey == 'multi_layer_map') || ($searchkey == 'clustering') || ($searchkey == 'gpx_panel') || ($searchkey == 'mlm_filter') ) {
														$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE `$searchkey` = %d AND `id` != 0", $searchvalue), ARRAY_A);
													} else if ( ($searchkey == 'createdon') || ($searchkey == 'updatedon') )   {
														$date_from = isset($_POST['date_from']) ? strtotime($_POST['date_from']) : (isset($_GET['date_from']) ? strtotime($_GET['date_from']) : '');
														$date_to = isset($_POST['date_to']) ? strtotime($_POST['date_to']) : (isset($_GET['date_to']) ? strtotime($_GET['date_to']) : '');
														if ( ($date_from != NULL) && ($date_to == NULL) ) {
															$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE `$searchkey` > FROM_UNIXTIME(%d) AND `id` != 0", $date_from), ARRAY_A);
														} else if ( ($date_from == NULL) && ($date_to != NULL) ) {
															$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE `$searchkey` < FROM_UNIXTIME(%d) AND `id` != 0", $date_to), ARRAY_A);
														} else if ( ($date_from != NULL) && ($date_to != NULL) ) {
															$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE `$searchkey` > FROM_UNIXTIME(%d) AND `$searchkey` < FROM_UNIXTIME(%d) AND `id` != 0", $date_from, $date_to), ARRAY_A);
														} else { //info: if ($date_from == NULL) && ($date_to == NULL)
															if ($format == 'json') {
																header('Content-type: application/json; charset=utf-8');
																if ($callback != NULL) { echo $callback . '('; }
																echo '{'.PHP_EOL;
																echo '"success":false,'.PHP_EOL;
																echo '"message":"' . esc_attr__('API parameter date_from or date_to has be set when searching date fields','lmm') . '",'.PHP_EOL;
																echo '"data": { }'.PHP_EOL;
																echo '}';
																if ($callback != NULL) { echo ');'; }
															} else if ($format == 'xml') {
																header('Content-type: application/xml; charset=utf-8');
																echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
																echo '<mapsmarker>'.PHP_EOL;
																echo '<success>false</success>'.PHP_EOL;
																echo '<message>' . esc_attr__('API parameter date_from or date_to has be set when searching date fields','lmm') . '</message>'.PHP_EOL;
																echo '<data></data>'.PHP_EOL;
																echo '</mapsmarker>';
															}
														}

													} else if ($searchkey == 'boundingbox') {
														$lat_top_left = isset($_POST['lat_top_left']) ? floatval(str_replace(",",".", $_POST['lat_top_left'])) : (isset($_GET['lat_top_left']) ? floatval(str_replace(",",".", $_GET['lat_top_left'])) : '');
														$lon_top_left = isset($_POST['lon_top_left']) ? floatval(str_replace(",",".", $_POST['lon_top_left'])) : (isset($_GET['lon_top_left']) ? floatval(str_replace(",",".", $_GET['lon_top_left'])) : '');
														$lat_bottom_right = isset($_POST['lat_bottom_right']) ? floatval(str_replace(",",".", $_POST['lat_bottom_right'])) : (isset($_GET['lat_bottom_right']) ? floatval(str_replace(",",".", $_GET['lat_bottom_right'])) : '');
														$lon_bottom_right = isset($_POST['lon_bottom_right']) ? floatval(str_replace(",",".", $_POST['lon_bottom_right'])) : (isset($_GET['lon_bottom_right']) ? floatval(str_replace(",",".", $_GET['lon_bottom_right'])) : '');
														if ( ($lat_top_left == NULL) || ($lon_top_left == NULL) || ($lat_bottom_right == NULL) || ($lon_bottom_right == NULL) ) {
															if ($format == 'json') {
																header('Content-type: application/json; charset=utf-8');
																if ($callback != NULL) { echo $callback . '('; }
																echo '{'.PHP_EOL;
																echo '"success":false,'.PHP_EOL;
																echo '"message":"' . esc_attr__('API parameters lat_top_left, lon_top_left, lat_bottom_right and lon_bottom_right have be set when performing a boundingbox search','lmm') . '",'.PHP_EOL;
																echo '"data": { }'.PHP_EOL;
																echo '}';
																if ($callback != NULL) { echo ');'; }
															} else if ($format == 'xml') {
																header('Content-type: application/xml; charset=utf-8');
																echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
																echo '<mapsmarker>'.PHP_EOL;
																echo '<success>false</success>'.PHP_EOL;
																echo '<message>' . esc_attr__('API parameters lat_top_left, lon_top_left, lat_bottom_right and lon_bottom_right have be set when performing a boundingbox search','lmm') . '</message>'.PHP_EOL;
																echo '<data></data>'.PHP_EOL;
																echo '</mapsmarker>';
															}
															exit();
														} else {
															$query_result = $wpdb->get_results( "SELECT * FROM `$table_name_layers` WHERE `layerviewlat` <= '$lat_top_left' AND `layerviewlon` >= '$lon_top_left' AND `layerviewlat` >= '$lat_bottom_right' AND `layerviewlon` <= '$lon_bottom_right' AND id != 0", ARRAY_A);
														}

													} else { //info: name, basemap, layerviewlat, layerviewlon, mapwidthunit, createdby, updatedby, multi_layer_map_list, address, gpx_url, mlm_filter_details
														$query_result = $wpdb->get_results( "SELECT * FROM $table_name_layers WHERE $searchkey LIKE '%$searchvalue%' AND id != 0", ARRAY_A);
													}
													if (count($query_result) >= 1) {
														if ($format == 'json') {
															header('Cache-Control: no-cache, must-revalidate');
															header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
															header('Content-type: application/json; charset=utf-8');
															if ($callback != NULL) { echo $callback . '('; }
															echo '{'.PHP_EOL;
															echo '"success":true,'.PHP_EOL;
															echo '"searchkey":"' . $searchkey .'",'.PHP_EOL;
															if ($searchvalue != NULL) {
																echo '"searchvalue":"' . $searchvalue .'",'.PHP_EOL;
															}
															if (($searchkey == 'createdon') || ($searchkey == 'updatedon') ) {
																echo '"date_from":"' . date("Y-m-d H:i:s", $date_from) . '",'.PHP_EOL;
																echo '"date_to":"' . date("Y-m-d H:i:s", $date_to) . '",'.PHP_EOL;
															}
															if ($searchkey == 'boundingbox') {
																echo '"lat_top_left":"' . $lat_top_left . '",'.PHP_EOL;
																echo '"lon_top_left":"' . $lon_top_left . '",'.PHP_EOL;
																echo '"lat_bottom_right":"' . $lat_bottom_right . '",'.PHP_EOL;
																echo '"lon_bottom_right":"' . $lon_bottom_right . '",'.PHP_EOL;
															}
															echo '"searchresults":'.count($query_result).','.PHP_EOL;
															echo '"message":"' . sprintf(esc_attr__('Search completed - showing %d results below','lmm'), count($query_result)) . '",'.PHP_EOL;
															echo '"data": ['.PHP_EOL;
																$result ='';
																foreach ($query_result as $row) {
																	$address = stripslashes(str_replace('"', '\'', $row['address']));
																	$result .= '{';
																		$result .= '"' . $remap_id . '":"' . $row['id'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_name . '":"' . stripslashes($row['name']) . '",'.PHP_EOL;
																		$result .= '"' . $remap_basemap . '":"' . $row['basemap'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_layerzoom . '":"' . $row['layerzoom'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mapwidth . '":"' . $row['mapwidth'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mapwidthunit . '":"' . $row['mapwidthunit'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mapheight . '":"' . $row['mapheight'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_panel . '":"' . $row['panel'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_layerviewlat . '":"' . $row['layerviewlat'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_layerviewlat . '":"' . $row['layerviewlon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_createdby . '":"' . $row['createdby'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_createdon . '":"' . $row['createdon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_updatedby . '":"' . $row['updatedby'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_updatedon . '":"' . $row['updatedon'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_controlbox . '":"'.$row['controlbox'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom . '":"' . $row['overlays_custom'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom2 . '":"' . $row['overlays_custom2'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom3 . '":"' . $row['overlays_custom3'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_overlays_custom4 . '":"' . $row['overlays_custom4'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms . '":"' . $row['wms'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms2 . '":"' . $row['wms2'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms3 . '":"' . $row['wms3'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms4 . '":"' . $row['wms4'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms5 . '":"' . $row['wms5'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms6 . '":"' . $row['wms6'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms7 . '":"' . $row['wms7'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms8 . '":"' . $row['wms8'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms9 . '":"' . $row['wms9'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_wms10 . '":"' . $row['wms10'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_listmarkers . '":"' . $row['listmarkers'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_multi_layer_map . '":"' . $row['multi_layer_map'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_multi_layer_map_list . '":"' . $row['multi_layer_map_list'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_address . '":"' . $address . '",'.PHP_EOL;
																		$result .= '"' . $remap_clustering . '":"' . $row['clustering'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_gpx_url . '":"' . $row['gpx_url'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_gpx_panel . '":"' . $row['gpx_panel'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mlm_filter . '":"' . $row['mlm_filter'] . '",'.PHP_EOL;
																		$result .= '"' . $remap_mlm_filter_details . '":"' . $row['mlm_filter_details'] . '"'.PHP_EOL;
																	$result .= '},';
																}
																echo $result = rtrim($result, ',');
															echo ']';//info: data
															echo '}';
															if ($callback != NULL) { echo ');'; }
														} else if ($format == 'xml') {
															header('Cache-Control: no-cache, must-revalidate');
															header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
															header('Content-type: application/xml; charset=utf-8');
															echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
															echo '<!DOCTYPE mapsmarker ['.PHP_EOL;
															if ($searchvalue != NULL) {
																echo '<!ELEMENT mapsmarker ((success, searchkey, searchvalue, searchresults, message, data))>'.PHP_EOL;
																echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchkey (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchvalue (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchresults (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
															} else if (($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') ) {
																echo '<!ELEMENT mapsmarker ((success, searchkey, date_from, date_to, searchresults, message, data))>'.PHP_EOL;
																echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchkey (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT date_from (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT date_to (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchresults (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
															} if ($searchkey == 'boundingbox') {
																echo '<!ELEMENT mapsmarker ((success, searchkey, lat_top_left, lon_top_left, lat_bottom_right, lon_bottom_right, searchresults, message, data))>'.PHP_EOL;
																echo '<!ELEMENT success (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchkey (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lat_top_left (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lon_top_left (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lat_bottom_right (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT lon_bottom_right (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT searchresults (#PCDATA)>'.PHP_EOL;
																echo '<!ELEMENT message (#PCDATA)>'.PHP_EOL;
															}
															echo '<!ATTLIST mapsmarker xmlns:xsi CDATA #FIXED "http://www.w3.org/2001/XMLSchema-instance" >'.PHP_EOL;
															echo '<!ELEMENT data ((result+))>'.PHP_EOL;
															echo '<!ELEMENT result ((' . $remap_id . ', ' . $remap_name . ', ' . $remap_basemap . ', ' . $remap_layerzoom . ', ' . $remap_mapwidth . ', ' . $remap_mapwidthunit . ', ' . $remap_mapheight . ', ' . $remap_panel . ', ' . $remap_layerviewlat . ', ' . $remap_layerviewlon . ', ' . $remap_createdby . ', ' . $remap_createdon . ', ' . $remap_updatedby . ', ' . $remap_updatedon . ', ' . $remap_controlbox . ', ' . $remap_overlays_custom . ', ' . $remap_overlays_custom2 . ', ' . $remap_overlays_custom3 . ', ' . $remap_overlays_custom4 . ', ' . $remap_wms . ', ' . $remap_wms2 . ', ' . $remap_wms3 . ', ' . $remap_wms4 . ', ' . $remap_wms5 . ', ' . $remap_wms6 . ', ' . $remap_wms7 . ', ' . $remap_wms8 . ', ' . $remap_wms9 . ', ' . $remap_wms10 . ', ' . $remap_listmarkers . ', ' . $remap_multi_layer_map . ', ' . $remap_multi_layer_map_list . ', ' . $remap_address . ', ' . $remap_clustering . ', ' . $remap_gpx_url . ', ' . $remap_gpx_panel .  ', '.$remap_mlm_filter.', '. $remap_mlm_filter_details .'))>'.PHP_EOL;
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
															echo '<!ELEMENT ' . $remap_clustering . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_gpx_url . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_gpx_panel . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_mlm_filter . ' (#PCDATA)>'.PHP_EOL;
															echo '<!ELEMENT ' . $remap_mlm_filter_details . ' (#PCDATA)>'.PHP_EOL;
															echo ']>'.PHP_EOL;
															echo '<mapsmarker>'.PHP_EOL;
															echo '<success>true</success>'.PHP_EOL;
															echo '<searchkey>' . $searchkey .'</searchkey>'.PHP_EOL;
															if ($searchvalue != NULL) {
																echo '<searchvalue>' . $searchvalue .'</searchvalue>'.PHP_EOL;
															}
															if (($searchkey == 'createdon') || ($searchkey == 'updatedon') || ($searchkey == 'kml_timestamp') ) {
																echo '<date_from>' . date("Y-m-d H:i:s", $date_from) . '</date_from>'.PHP_EOL;
																echo '<date_to>' . date("Y-m-d H:i:s", $date_to) . '</date_to>'.PHP_EOL;
															}
															if ($searchkey == 'boundingbox') {
																echo '<lat_top_left>' . $lat_top_left . '</lat_top_left>'.PHP_EOL;
																echo '<lon_top_left>' . $lon_top_left . '</lon_top_left>'.PHP_EOL;
																echo '<lat_bottom_right>' . $lat_bottom_right . '</lat_bottom_right>'.PHP_EOL;
																echo '<lon_bottom_right>' . $lon_bottom_right . '</lon_bottom_right>'.PHP_EOL;
															}
															echo '<searchresults>'.count($query_result).'</searchresults>'.PHP_EOL;
															echo '<message>' . sprintf(esc_attr__('Search completed - showing %d results below','lmm'), count($query_result)) . '</message>'.PHP_EOL;
															echo '<data>'.PHP_EOL;
															foreach ($query_result as $row) {
																echo '<result>'.PHP_EOL;
																echo '<' . $remap_id . '>' . $row['id'] . '</' . $remap_id . '>'.PHP_EOL;
																echo '<' . $remap_name . '><![CDATA[' . stripslashes($row['name']) . ']]></' . $remap_name . '>'.PHP_EOL;
																echo '<' . $remap_basemap . '>' . $row['basemap'] . '</' . $remap_basemap . '>'.PHP_EOL;
																echo '<' . $remap_layerzoom . '>' . $row['layerzoom'] . '</' . $remap_layerzoom . '>'.PHP_EOL;
																echo '<' . $remap_mapwidth . '>' . $row['mapwidth'] . '</' . $remap_mapwidth . '>'.PHP_EOL;
																echo '<' . $remap_mapwidthunit . '>' . $row['mapwidthunit'] . '</' . $remap_mapwidthunit . '>'.PHP_EOL;
																echo '<' . $remap_mapheight . '>' . $row['mapheight'] . '</' . $remap_mapheight . '>'.PHP_EOL;
																echo '<' . $remap_panel . '>' . $row['panel'] . '</' . $remap_panel . '>'.PHP_EOL;
																echo '<' . $remap_layerviewlat . '>' . $row['layerviewlat'] . '</' . $remap_layerviewlat . '>'.PHP_EOL;
																echo '<' . $remap_layerviewlon . '>' . $row['layerviewlon'] . '</' . $remap_layerviewlon . '>'.PHP_EOL;
																echo '<' . $remap_createdby . '><![CDATA[' . $row['createdby'] . ']]></' . $remap_createdby . '>'.PHP_EOL;
																echo '<' . $remap_createdon . '>' . $row['createdon'] . '</' . $remap_createdon . '>'.PHP_EOL;
																echo '<' . $remap_updatedby . '><![CDATA[' . $row['updatedby'] . ']]></' . $remap_updatedby . '>'.PHP_EOL;
																echo '<' . $remap_updatedon . '>' . $row['updatedon'] . '</' . $remap_updatedon . '>'.PHP_EOL;
																echo '<' . $remap_controlbox . '>' . $row['controlbox'] . '</' . $remap_controlbox . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom . '>' . $row['overlays_custom'] . '</' . $remap_overlays_custom . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom2 . '>' . $row['overlays_custom2'] . '</' . $remap_overlays_custom2 . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom3 . '>' . $row['overlays_custom3'] . '</' . $remap_overlays_custom3 . '>'.PHP_EOL;
																echo '<' . $remap_overlays_custom4 . '>' . $row['overlays_custom4'] . '</' . $remap_overlays_custom4 . '>'.PHP_EOL;
																echo '<' . $remap_wms . '>' . $row['wms'] . '</' . $remap_wms . '>'.PHP_EOL;
																echo '<' . $remap_wms2 . '>' . $row['wms2'] . '</' . $remap_wms2 . '>'.PHP_EOL;
																echo '<' . $remap_wms3 . '>' . $row['wms3'] . '</' . $remap_wms3 . '>'.PHP_EOL;
																echo '<' . $remap_wms4 . '>' . $row['wms4'] . '</' . $remap_wms4 . '>'.PHP_EOL;
																echo '<' . $remap_wms5 . '>' . $row['wms5'] . '</' . $remap_wms5 . '>'.PHP_EOL;
																echo '<' . $remap_wms6 . '>' . $row['wms6'] . '</' . $remap_wms6 . '>'.PHP_EOL;
																echo '<' . $remap_wms7 . '>' . $row['wms7'] . '</' . $remap_wms7 . '>'.PHP_EOL;
																echo '<' . $remap_wms8 . '>' . $row['wms8'] . '</' . $remap_wms8 . '>'.PHP_EOL;
																echo '<' . $remap_wms9 . '>' . $row['wms9'] . '</' . $remap_wms9 . '>'.PHP_EOL;
																echo '<' . $remap_wms10 . '>' . $row['wms10'] . '</' . $remap_wms10 . '>'.PHP_EOL;
																echo '<' . $remap_listmarkers . '>' . $row['listmarkers'] . '</' . $remap_listmarkers . '>'.PHP_EOL;
																echo '<' . $remap_multi_layer_map . '>' . $row['multi_layer_map'] . '</' . $remap_multi_layer_map . '>'.PHP_EOL;
																echo '<' . $remap_multi_layer_map_list . '>' . $row['multi_layer_map_list'] . '</' . $remap_multi_layer_map_list . '>'.PHP_EOL;
																echo '<' . $remap_address . '><![CDATA[' . $row['address'] . ']]></' . $remap_address . '>'.PHP_EOL;
																echo '<' . $remap_clustering . '>' . $row['clustering'] . '</' . $remap_clustering . '>'.PHP_EOL;
																echo '<' . $remap_gpx_url . '><![CDATA[' . $row['gpx_url'] . ']]></' . $remap_gpx_url . '>'.PHP_EOL;
																echo '<' . $remap_gpx_panel . '><![CDATA[' . $row['gpx_panel'] . ']]></' . $remap_gpx_panel . '>'.PHP_EOL;
																echo '<' . $remap_mlm_filter . '><![CDATA[' . $row['mlm_filter'] . ']]></' . $remap_mlm_filter . '>'.PHP_EOL;
																echo '<' . $remap_mlm_filter_details . '><![CDATA[' . $row['mlm_filter_details'] . ']]></' . $remap_mlm_filter_details . '>'.PHP_EOL;
																echo '</result>'.PHP_EOL;
															}
															echo '</data>'.PHP_EOL;
															echo '</mapsmarker>';
														} //info: end display search results / marker
													} else {
														if ($format == 'json') {
															header('Content-type: application/json; charset=utf-8');
															if ($callback != NULL) { echo $callback . '('; }
															echo '{'.PHP_EOL;
															echo '"success":true,'.PHP_EOL;
															echo '"searchresults":0,'.PHP_EOL;
															echo '"message":"' . sprintf(esc_attr__('Search completed, no results found (searchkey: %s, searchvalue: %s)','lmm'), $searchkey, $searchvalue) . '",'.PHP_EOL;
															echo '"data": { }'.PHP_EOL;
															echo '}';
															if ($callback != NULL) { echo ');'; }
														} else if ($format == 'xml') {
															header('Content-type: application/xml; charset=utf-8');
															echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
															echo '<mapsmarker>'.PHP_EOL;
															echo '<success>true</success>'.PHP_EOL;
															echo '<searchresults>0</searchresults>'.PHP_EOL;
															echo '<message>' . sprintf(esc_attr__('Search completed, no results found (searchkey: %s, searchvalue: %s)','lmm'), $searchkey, $searchvalue) . '</message>'.PHP_EOL;
															echo '<data></data>'.PHP_EOL;
															echo '</mapsmarker>';
														}
													} //info: end count($query_result)

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
												} //info: end type check / search
											} else if ($searchvalue == '') {
												if ($format == 'json') {
													header('Content-type: application/json; charset=utf-8');
													if ($callback != NULL) { echo $callback . '('; }
													echo '{'.PHP_EOL;
													echo '"success":false,'.PHP_EOL;
													echo '"message":"' . esc_attr__('API search parameter searchvalue has to be set','lmm') . '",'.PHP_EOL;
													echo '"data": { }'.PHP_EOL;
													echo '}';
													if ($callback != NULL) { echo ');'; }
												} else if ($format == 'xml') {
													header('Content-type: application/xml; charset=utf-8');
													echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
													echo '<mapsmarker>'.PHP_EOL;
													echo '<success>false</success>'.PHP_EOL;
													echo '<message>' . esc_attr__('API search parameter searchvalue has to be set','lmm') . '</message>'.PHP_EOL;
													echo '<data></data>'.PHP_EOL;
													echo '</mapsmarker>';
												}
											} //info: end searchvalue check
										} else if ($searchkey == '') {
											if ($format == 'json') {
												header('Content-type: application/json; charset=utf-8');
												if ($callback != NULL) { echo $callback . '('; }
												echo '{'.PHP_EOL;
												echo '"success":false,'.PHP_EOL;
												echo '"message":"' . esc_attr__('API search parameter searchkey has to be set','lmm') . '",'.PHP_EOL;
												echo '"data": { }'.PHP_EOL;
												echo '}';
												if ($callback != NULL) { echo ');'; }
											} else if ($format == 'xml') {
												header('Content-type: application/xml; charset=utf-8');
												echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
												echo '<mapsmarker>'.PHP_EOL;
												echo '<success>false</success>'.PHP_EOL;
												echo '<message>' . esc_attr__('API search parameter searchkey has to be set','lmm') . '</message>'.PHP_EOL;
												echo '<data></data>'.PHP_EOL;
												echo '</mapsmarker>';
											}
										} else {
											if ($format == 'json') {
												header('Content-type: application/json; charset=utf-8');
												if ($callback != NULL) { echo $callback . '('; }
												echo '{'.PHP_EOL;
												echo '"success":false,'.PHP_EOL;
												echo '"message":"' . esc_attr__('API search parameter searchkey is invalid','lmm') . '",'.PHP_EOL;
												echo '"data": { }'.PHP_EOL;
												echo '}';
												if ($callback != NULL) { echo ');'; }
											} else if ($format == 'xml') {
												header('Content-type: application/xml; charset=utf-8');
												echo '<?xml version="1.0" encoding="utf8"?>'.PHP_EOL;
												echo '<mapsmarker>'.PHP_EOL;
												echo '<success>false</success>'.PHP_EOL;
												echo '<message>' . esc_attr__('API search parameter searchkey is invalid','lmm') . '</message>'.PHP_EOL;
												echo '<data></data>'.PHP_EOL;
												echo '</mapsmarker>';
											}
										} //info: end searchkey check
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
									} //info: end permission check / search
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
