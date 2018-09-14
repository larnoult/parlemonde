<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'ajax-actions-backend.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
//info: sanitize AJAX response from unwanted output (https://wp-dreams.com/articles/2014/10/removing-unwanted-output-from-ajax-responses/)
$left_delimiter = "!!LMM-AJAX-START!!";
$right_delimiter = "!!LMM-AJAX-END!!";

$ajax_results = array();

global $wpdb, $current_user, $wp_version;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();

if( (!current_user_can($lmm_options[ 'capabilities_view_others' ])) || !isset( $_POST['lmm_ajax_nonce'] ) || !wp_verify_nonce($_POST['lmm_ajax_nonce'], 'lmm-ajax-nonce') ) {
	$ajax_results['status-class'] = 'notice notice-error';
	$ajax_results['status-text'] = __('Permissions check failed or WordPress nonce has expired - please reload the page to try again!','lmm');
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}

$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';

//info: global settings
$ajax_subaction = $_POST['lmm_ajax_subaction'];
$oid = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : '');

//info: individual marker / layer settings
if (($ajax_subaction == 'marker-add') || ($ajax_subaction == 'marker-edit')) {
	$lat_check = isset($_POST['lat']) ? $_POST['lat'] : (isset($_GET['lat']) ? $_GET['lat'] : '');
	$lon_check = isset($_POST['lon']) ? $_POST['lon'] : (isset($_GET['lon']) ? $_GET['lon'] : '');
	$layer = ($_POST['layer']!="") ? json_encode($_POST['layer']) : json_encode(array("0"));
} else if (($ajax_subaction == 'layer-add') || ($ajax_subaction == 'layer-edit')) {
	$lat_check = isset($_POST['layerviewlat']) ? $_POST['layerviewlat'] : (isset($_GET['layerviewlat']) ? $_GET['layerviewlat'] : '');
	$lon_check = isset($_POST['layerviewlon']) ? $_POST['layerviewlon'] : (isset($_GET['layerviewlon']) ? $_GET['layerviewlon'] : '');
}
/**********************************************/
if ($ajax_subaction == 'marker-add') {
	if (MMP_Globals::check_capability('add', $current_user->user_login)) {
		if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
			$markername_quotes = str_replace("\\\\","/", str_replace("\"","'", sanitize_text_field($_POST['markername']))); //info: geojson validity fixes
			$popuptext = preg_replace("/\t/", " ", str_replace("\\\\","/", $_POST['popuptext'])); //info: geojson validity fixes
			$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", sanitize_text_field($_POST['address']))); //info: geojson validity fixes
			//info: for datetime check in Firefox
			$createdon = (MMP_Globals::validate_date($_POST['createdon']) == TRUE) ? $_POST['createdon'] : current_time('mysql',0);
			$updatedon = (MMP_Globals::validate_date($_POST['updatedon']) == TRUE) ? $_POST['updatedon'] : current_time('mysql',0);
			if ($_POST['kml_timestamp'] == NULL) {
				$result = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername_quotes, sanitize_text_field($_POST['basemap']), $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), sanitize_text_field($_POST['icon_hidden']), $popuptext, $_POST['zoom'], $_POST['openpopup'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $_POST['panel'], esc_html($_POST['createdby']), $createdon, esc_html($_POST['updatedby']), $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $_POST['wms'], $_POST['wms2'], $_POST['wms3'], $_POST['wms4'], $_POST['wms5'], $_POST['wms6'], $_POST['wms7'], $_POST['wms8'], $_POST['wms9'], $_POST['wms10'], $address, trim(esc_url_raw($_POST['gpx_url'])), $_POST['gpx_panel'] );
			} else if ($_POST['kml_timestamp'] != NULL) {
				//info: for datetime check in Firefox
				$kml_timestamp = (MMP_Globals::validate_date($_POST['kml_timestamp']) == TRUE) ? $_POST['kml_timestamp'] : current_time('mysql',0);
				$result = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername_quotes, sanitize_text_field($_POST['basemap']), $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), sanitize_text_field($_POST['icon_hidden']), $popuptext, $_POST['zoom'], $_POST['openpopup'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $_POST['panel'], esc_html($_POST['createdby']), $createdon, esc_html($_POST['updatedby']), $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $_POST['wms'], $_POST['wms2'], $_POST['wms3'], $_POST['wms4'], $_POST['wms5'], $_POST['wms6'], $_POST['wms7'], $_POST['wms8'], $_POST['wms9'], $_POST['wms10'], $kml_timestamp, $address, trim(esc_url_raw($_POST['gpx_url'])), $_POST['gpx_panel'] );
			}
			$wpdb->query( $result );
			$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
			$ajax_results['newmarkerid'] = $wpdb->insert_id;
			//info: register marker texts for translation to support WPML
			MMP_Globals::register_single_string("Marker (ID {$ajax_results['newmarkerid']}) name", $markername_quotes);
			MMP_Globals::register_single_string("Marker (ID {$ajax_results['newmarkerid']}) popuptext", $popuptext);
			MMP_Globals::register_single_string("Marker (ID {$ajax_results['newmarkerid']}) address", $address);
			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = sprintf(__('The marker with the ID %1$s has been successfully published','lmm'), $wpdb->insert_id);
			$ajax_results['layerid'] = implode(',', json_decode($layer));
			$ajax_results['markername'] = __('Edit marker','lmm') . ' "' . stripslashes(strip_tags($_POST['markername'])) . '"';
		} else {
			$ajax_results['status-class'] = 'notice notice-error';
			$ajax_results['status-text'] = __('Error: coordinates cannot be empty!','lmm');
		}
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Your user does not have the permission to add a new marker!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();

/**********************************************/
} else if ($ajax_subaction == 'marker-edit') {
	$createdby_check = $wpdb->get_var( 'SELECT `createdby` FROM `'.$table_name_markers.'` WHERE id='.$oid );
	if (MMP_Globals::check_capability('edit', $createdby_check)) {
		if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
			$markername_quotes = str_replace("\\\\","/", str_replace("\"","'", sanitize_text_field($_POST['markername']))); //info: geojson validity fixes
			$popuptext = preg_replace("/\t/", " ", str_replace("\\\\","/", $_POST['popuptext'])); //info: geojson validity fixes
			$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", sanitize_text_field($_POST['address']))); //info: geojson validity fixes
			//info: for datetime check in Firefox
			$createdon = (MMP_Globals::validate_date($_POST['createdon']) == TRUE) ? $_POST['createdon'] : current_time('mysql',0);
			$updatedon = (MMP_Globals::validate_date($_POST['updatedon']) == TRUE) ? $_POST['updatedon'] : current_time('mysql',0);
			if ($_POST['kml_timestamp'] == NULL) {
				$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `markername` = %s, `basemap` = %s, `layer` = %s, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, sanitize_text_field($_POST['basemap']), $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), sanitize_text_field($_POST['icon_hidden']), $popuptext, $_POST['zoom'], $_POST['openpopup'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $_POST['panel'], esc_html($_POST['createdby']), $createdon, esc_html($_POST['updatedby']), $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $_POST['wms'], $_POST['wms2'], $_POST['wms3'], $_POST['wms4'], $_POST['wms5'], $_POST['wms6'], $_POST['wms7'], $_POST['wms8'], $_POST['wms9'], $_POST['wms10'], $address, trim(esc_url_raw($_POST['gpx_url'])), $_POST['gpx_panel'], $oid );
			} else if ($_POST['kml_timestamp'] != NULL) {
				//info: for datetime check in Firefox
				$kml_timestamp = (MMP_Globals::validate_date($_POST['kml_timestamp']) == TRUE) ? $_POST['kml_timestamp'] : current_time('mysql',0);
				$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `markername` = %s, `basemap` = %s, `layer` = %s, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `kml_timestamp` = %s, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, sanitize_text_field($_POST['basemap']), $layer, str_replace(',', '.', floatval($_POST['lat'])), str_replace(',', '.', floatval($_POST['lon'])), sanitize_text_field($_POST['icon_hidden']), $popuptext, $_POST['zoom'], $_POST['openpopup'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $_POST['panel'], esc_html($_POST['createdby']), $createdon, esc_html($_POST['updatedby']), $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $_POST['wms'], $_POST['wms2'], $_POST['wms3'], $_POST['wms4'], $_POST['wms5'], $_POST['wms6'], $_POST['wms7'], $_POST['wms8'], $_POST['wms9'], $_POST['wms10'], $kml_timestamp, $address, trim(esc_url_raw($_POST['gpx_url'])), $_POST['gpx_panel'], $oid );
			}

			$wpdb->query( $result );
			$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
			//info: register marker texts for translation to support WPML
			MMP_Globals::register_single_string("Marker (ID {$oid}) name", $markername_quotes);
			MMP_Globals::register_single_string("Marker (ID {$oid}) popuptext", $popuptext);
			MMP_Globals::register_single_string("Marker (ID {$oid}) address", $address);
			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = sprintf(__('The marker with the ID %1$s has been successfully updated','lmm'), intval($_POST['id']));
			$ajax_results['markerid'] = $oid;
			$ajax_results['layerid'] = implode(',', json_decode($layer));
			$ajax_results['markername'] = __('Edit marker','lmm') . ' "' . stripslashes(strip_tags($_POST['markername'])) . '"';
			$ajax_results['updatedby_saved'] = strip_tags($_POST['updatedby']);
			$ajax_results['updatedon_saved'] = strip_tags($_POST['updatedon']);
			$ajax_results['updatedby_next'] = $current_user->user_login;
			$ajax_results['updatedon_next'] = current_time('mysql',0);
		} else {
			$ajax_results['status-class'] = 'notice notice-error';
			$ajax_results['status-text'] = __('Error: coordinates cannot be empty!','lmm');
		}
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Error: your user does not have the permission to edit markers from other users!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();

/**********************************************/
} else if ($ajax_subaction == 'marker-delete') {
	$createdby_check = $wpdb->get_var( 'SELECT `createdby` FROM `'.$table_name_markers.'` WHERE id='.$oid );
	if (MMP_Globals::check_capability('edit', $createdby_check)) {
		if (!empty($oid)) {
			$result = $wpdb->prepare( "DELETE FROM `$table_name_markers` WHERE `id` = %d", $oid );
			$wpdb->query( $result );
			$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
			//info: delete qr code cache image
			if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $oid . '.png') ) {
				unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $oid . '.png');
			}
			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = sprintf(__('The marker with the ID %1$s has been successfully deleted','lmm'), $oid);
		}
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Error: your user does not have the permission to delete markers from other users!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();

/**********************************************/
}  else if ($ajax_subaction == 'markers-bulk-delete') {
	if (MMP_Globals::check_capability('delete', false)) {
		$checkedmarkers = (isset($_POST['checkedmarkers']))?$_POST['checkedmarkers']:array();
		$checked_markers_prepared = implode(",", $checkedmarkers);
		$checked_markers = preg_replace('/[a-z|A-Z| |\=]/', '', $checked_markers_prepared);
		$wpdb->query( "DELETE FROM `$table_name_markers` WHERE `id` IN (" . htmlspecialchars($checked_markers) . ")");
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		$ajax_results['status-class'] = 'notice notice-success';
		$ajax_results['status-text'] =  __('The selected markers have been deleted','lmm') . ' (ID ' . htmlspecialchars($checked_markers) . ')';
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Your user does not have the permission to delete this marker!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
/**********************************************/
} else if ($ajax_subaction == 'marker-duplicate') {
	if (MMP_Globals::check_capability('add', $current_user->user_login)) {
		$result = $wpdb->get_row( $wpdb->prepare('SELECT * FROM `'.$table_name_markers.'` WHERE `id` = %d', $oid), ARRAY_A);
		if ($result['kml_timestamp'] == NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $result['markername'], $result['basemap'], $result['layer'], $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['address'], esc_url_raw($result['gpx_url']), $result['gpx_panel'] );
		} else if ($result['kml_timestamp'] != NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $result['markername'], $result['basemap'], $result['layer'], $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['kml_timestamp'], $result['address'], esc_url_raw($result['gpx_url']), $result['gpx_panel'] );
		}
		$wpdb->query( $sql_duplicate );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		$ajax_results['status-class'] = 'notice notice-success';
		$ajax_results['status-text'] = sprintf(__('The marker has been successfully duplicated - new ID: %1$s','lmm'), $wpdb->insert_id);
		$ajax_results['markername'] = __('Edit marker','lmm') . ' "' . stripslashes($result['markername']) . '"';
		$ajax_results['newmarkerid'] = $wpdb->insert_id;
		//info: register marker texts for translation to support WPML
		MMP_Globals::register_single_string("Marker (ID {$ajax_results['newmarkerid']}) name", $result['markername']);
		MMP_Globals::register_single_string("Marker (ID {$ajax_results['newmarkerid']}) popuptext", $result['popuptext']);
		MMP_Globals::register_single_string("Marker (ID {$ajax_results['newmarkerid']}) address", $result['address']);
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Your user does not have the permission to duplicate this marker!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();

/**********************************************/
} else if ($ajax_subaction == 'layer-add') {
	if (MMP_Globals::check_capability('add', $current_user->user_login)) {
		if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
			$layerlist = $wpdb->get_results('SELECT l.id as lid,l.name as lname FROM `'.$table_name_layers.'` as l WHERE l.multi_layer_map = 0 and l.id != 0', ARRAY_A);
			global $current_user, $allowedtags;
			//info: set values for wms checkboxes status
			$wms_checkbox = (isset($_POST['wms']) && $_POST['wms']!=0) ? '1' : '0';
			$wms2_checkbox = (isset($_POST['wms2']) && $_POST['wms2']!=0) ? '1' : '0';
			$wms3_checkbox = (isset($_POST['wms3']) && $_POST['wms3']!=0) ? '1' : '0';
			$wms4_checkbox = (isset($_POST['wms4']) && $_POST['wms4']!=0) ? '1' : '0';
			$wms5_checkbox = (isset($_POST['wms5']) && $_POST['wms5']!=0) ? '1' : '0';
			$wms6_checkbox = (isset($_POST['wms6']) && $_POST['wms6']!=0) ? '1' : '0';
			$wms7_checkbox = (isset($_POST['wms7']) && $_POST['wms7']!=0) ? '1' : '0';
			$wms8_checkbox = (isset($_POST['wms8']) && $_POST['wms8']!=0) ? '1' : '0';
			$wms9_checkbox = (isset($_POST['wms9']) && $_POST['wms9']!=0) ? '1' : '0';
			$wms10_checkbox = (isset($_POST['wms10']) && $_POST['wms10']!=0) ? '1' : '0';

			$clustering_checkbox = (isset($_POST['clustering']) && $_POST['clustering']!=0) ? '1' : '0';
			$listmarkers_checkbox = (isset($_POST['listmarkers']) && $_POST['listmarkers']!=0) ? '1' : '0';
			$panel_checkbox = (isset($_POST['panel']) && $_POST['panel']!=0) ? '1' : '0';
			$layername_quotes = str_replace("\\\\","/", str_replace("\"","'", sanitize_text_field($_POST['name']))); //info: backslash and double quotes break geojson
			$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", sanitize_text_field($_POST['address']))); //info: tabs break geojson
			$multi_layer_map_checkbox = (isset($_POST['multi_layer_map']) && $_POST['multi_layer_map']=='1') ? '1' : '0';
			$mlm_checked_imploded = (isset($_POST['mlmall']) && $_POST['mlmall']=='1') ? 'all' : '';
			$gpx_panel_checkbox = (isset($_POST['gpx_panel']) && $_POST['gpx_panel']=='1')? '1' : '0';
			//info: for datetime check in Firefox
			$createdon = (MMP_Globals::validate_date($_POST['createdon']) == TRUE) ? $_POST['createdon'] : current_time('mysql',0);
			$updatedon = (MMP_Globals::validate_date($_POST['updatedon']) == TRUE) ? $_POST['updatedon'] : current_time('mysql',0);

			$mlmlayers = explode(',', $_POST['mlmlayers']);
			$filter_details = array();
			if ($mlm_checked_imploded != 'all') {
				$mlm_checked_temp = '';
				foreach ($layerlist as $mlmrow){
					$mlm_checked{$mlmrow['lid']} = (in_array('mlm-'.$mlmrow['lid'], $mlmlayers)) ? $mlmrow['lid'].',' : '';
					$mlm_checked_temp .= $mlm_checked{$mlmrow['lid']};
					if(isset($_POST['mlm_filter_status_'.$mlmrow['lid']])){
						$layer_filter_name =  (isset($_POST['mlm_filter_name_' . $mlmrow['lid'] ]) && trim($_POST['mlm_filter_name_' . $mlmrow['lid'] ])!='')?$_POST['mlm_filter_name_' . $mlmrow['lid'] ]:'';
						$layer_filter_icon =  (isset($_POST['mlm_filter_icon_' . $mlmrow['lid'] ]) && trim($_POST['mlm_filter_icon_' . $mlmrow['lid'] ])!='')?$_POST['mlm_filter_icon_' . $mlmrow['lid'] ]:'';
							if($_POST['mlm_filter_status_'.$mlmrow['lid']] == '2'){
								// inactive layer
								$filter_details[$mlmrow['lid']] = array( 'status'   => 'inactive',
																		 'name' => wp_kses($layer_filter_name, $allowedtags),
																		 'icon' => wp_kses($layer_filter_icon, $allowedtags));
							}elseif($_POST['mlm_filter_status_'.$mlmrow['lid']] == '1'){
								// active layer
								$filter_details[$mlmrow['lid']] = array(  'status'   => 'active',
																		  'name' => wp_kses($layer_filter_name, $allowedtags),
																		  'icon' => wp_kses($layer_filter_icon, $allowedtags));
							}
					}
				}
				$mlm_checked_imploded = substr($mlm_checked_temp, 0, -1);
			}
			if( empty($filter_details)  ){
				$result = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter`, `mlm_filter_details` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d, NULL)", $layername_quotes, sanitize_text_field($_POST['basemap']), $_POST['layerzoom'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $panel_checkbox, str_replace(',', '.', floatval($_POST['layerviewlat'])), str_replace(',', '.', floatval($_POST['layerviewlon'])), $current_user->user_login, $createdon, $current_user->user_login, $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $listmarkers_checkbox, $multi_layer_map_checkbox, $mlm_checked_imploded, $address, $clustering_checkbox, esc_url_raw($_POST['gpx_url']), $gpx_panel_checkbox, $_POST['controlbox_mlm_filter'] );
			}else{
				$filter_details = json_encode($filter_details);
				$result = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter`, `mlm_filter_details` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d, %s)", $layername_quotes, sanitize_text_field($_POST['basemap']), $_POST['layerzoom'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $panel_checkbox, str_replace(',', '.', floatval($_POST['layerviewlat'])), str_replace(',', '.', floatval($_POST['layerviewlon'])), $current_user->user_login, $createdon, $current_user->user_login, $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $listmarkers_checkbox, $multi_layer_map_checkbox, $mlm_checked_imploded, $address, $clustering_checkbox, esc_url_raw($_POST['gpx_url']), $gpx_panel_checkbox, $_POST['controlbox_mlm_filter'], $filter_details );
			}
			$wpdb->query( $result );
			$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = sprintf(__('The layer with the ID %1$s has been successfully published','lmm'), $wpdb->insert_id);
			$ajax_results['newlayerid'] = $wpdb->insert_id;
			$ajax_results['layername'] = __('Edit layer','lmm') . ' "' . stripslashes($_POST['name']) . '"';
			//info: register layer texts for translation to support WPML
			MMP_Globals::register_single_string("Layer (ID {$ajax_results['newlayerid']}) name", $layername_quotes);
			MMP_Globals::register_single_string("Layer (ID {$ajax_results['newlayerid']}) address", $address);

			if ($multi_layer_map_checkbox == '0') {
				$ajax_results['listmarker-table-heading'] = sprintf(__('Markers assigned to layer "%1s" (ID %2s)','lmm'), stripslashes($_POST['name']), $wpdb->insert_id);
			} else if ($multi_layer_map_checkbox == '1') {
				$ajax_results['listmarker-table-heading'] = sprintf(__('Markers assigned to multi layer map "%1s" (ID %2s)','lmm'), stripslashes($_POST['name']), $wpdb->insert_id);
			}
		} else {
			$ajax_results['status-class'] = 'notice notice-error';
			$ajax_results['status-text'] = __('Error: coordinates cannot be empty!','lmm');
		}
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Your user does not have the permission to add a new layer!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
/**********************************************/
} else if ($ajax_subaction == 'layer-edit') {
	$createdby_check = $wpdb->get_var( 'SELECT `createdby` FROM `'.$table_name_layers.'` WHERE id='.$oid );
	if (MMP_Globals::check_capability('edit', $createdby_check)) {
		if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
			$layerlist = $wpdb->get_results('SELECT l.id as lid,l.name as lname FROM `'.$table_name_layers.'` as l WHERE l.multi_layer_map = 0 and l.id != 0', ARRAY_A);
			//info: set values for wms checkboxes status
			$wms_checkbox = (isset($_POST['wms']) && $_POST['wms']!=0) ? '1' : '0';
			$wms2_checkbox = (isset($_POST['wms2']) && $_POST['wms2']!=0) ? '1' : '0';
			$wms3_checkbox = (isset($_POST['wms3']) && $_POST['wms3']!=0) ? '1' : '0';
			$wms4_checkbox = (isset($_POST['wms4']) && $_POST['wms4']!=0) ? '1' : '0';
			$wms5_checkbox = (isset($_POST['wms5']) && $_POST['wms5']!=0) ? '1' : '0';
			$wms6_checkbox = (isset($_POST['wms6']) && $_POST['wms6']!=0) ? '1' : '0';
			$wms7_checkbox = (isset($_POST['wms7']) && $_POST['wms7']!=0) ? '1' : '0';
			$wms8_checkbox = (isset($_POST['wms8']) && $_POST['wms8']!=0) ? '1' : '0';
			$wms9_checkbox = (isset($_POST['wms9']) && $_POST['wms9']!=0) ? '1' : '0';
			$wms10_checkbox = (isset($_POST['wms10']) && $_POST['wms10']!=0) ? '1' : '0';

			$clustering_checkbox = (isset($_POST['clustering']) && $_POST['clustering'] == '1') ? '1' : '0';
			$listmarkers_checkbox = (isset($_POST['listmarkers']) && $_POST['listmarkers']=='1') ? '1' : '0';
			$panel_checkbox = (isset($_POST['panel']) && $_POST['panel'] == '1') ? '1' : '0';
			$layername_quotes = str_replace("\\\\","/", str_replace("\"","'", sanitize_text_field($_POST['name']))); //info: backslash and double quotes break geojson
			$address = preg_replace("/(\\\\)(?!')/","/", preg_replace("/\t/", " ", sanitize_text_field($_POST['address']))); //info: tabs break geojson
			$multi_layer_map_checkbox = (isset($_POST['multi_layer_map']) && $_POST['multi_layer_map'] === '1') ? '1' : '0';
			$mlm_checked_imploded = (isset($_POST['mlmall']) && $_POST['mlmall']=='1') ? 'all' : '';
			$gpx_panel_checkbox = (isset($_POST['gpx_panel']) && $_POST['gpx_panel']==='1') ? '1' : '0';
			//info: for datetime check in Firefox
			$createdon = (MMP_Globals::validate_date($_POST['createdon']) == TRUE) ? $_POST['createdon'] : current_time('mysql',0);
			$updatedon = (MMP_Globals::validate_date($_POST['updatedon']) == TRUE) ? $_POST['updatedon'] : current_time('mysql',0);

			$mlmlayers = explode(',', $_POST['mlmlayers']);
			$filter_details = array();
			if ($mlm_checked_imploded != 'all' && $multi_layer_map_checkbox === '1') {
				$mlm_checked_temp = '';
				foreach ($layerlist as $mlmrow){
					$mlm_checked{$mlmrow['lid']} = (in_array('mlm-'.$mlmrow['lid'], $mlmlayers)) ? $mlmrow['lid'].',' : '';
					$mlm_checked_temp .= $mlm_checked{$mlmrow['lid']};
					if(isset($_POST['mlm_filter_status_'.$mlmrow['lid']])){
							$layer_filter_name =  (isset($_POST['mlm_filter_name_' . $mlmrow['lid'] ]) && trim($_POST['mlm_filter_name_' . $mlmrow['lid'] ])!='')?$_POST['mlm_filter_name_' . $mlmrow['lid'] ]:'';
							$layer_filter_icon =  (isset($_POST['mlm_filter_icon_' . $mlmrow['lid'] ]) && trim($_POST['mlm_filter_icon_' . $mlmrow['lid'] ])!='')?$_POST['mlm_filter_icon_' . $mlmrow['lid'] ]:'';
							if($_POST['mlm_filter_status_'.$mlmrow['lid']] == '2'){
								// inactive layer
								$filter_details[$mlmrow['lid']] = array( 'status'   => 'inactive',
																		 'name' => wp_kses($layer_filter_name, $allowedtags),
																		 'icon' => wp_kses($layer_filter_icon, $allowedtags));
							}elseif($_POST['mlm_filter_status_'.$mlmrow['lid']] == '1'){
								// active layer
								$filter_details[$mlmrow['lid']] = array(  'status'   => 'active',
																		  'name' => wp_kses($layer_filter_name, $allowedtags),
																		  'icon' => wp_kses($layer_filter_icon, $allowedtags));
							}
					}
				}
				$mlm_checked_imploded = substr($mlm_checked_temp, 0, -1);
			}
			if(empty($filter_details)){
				$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d, `mlm_filter` = %d, `mlm_filter_details` = NULL WHERE `id` = %d", $layername_quotes, sanitize_text_field($_POST['basemap']), $_POST['layerzoom'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $panel_checkbox, str_replace(',', '.', floatval($_POST['layerviewlat'])), str_replace(',', '.', floatval($_POST['layerviewlon'])), esc_html($_POST['createdby']), $createdon, $current_user->user_login, $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $listmarkers_checkbox, $multi_layer_map_checkbox, $mlm_checked_imploded, $address, $clustering_checkbox, esc_url_raw($_POST['gpx_url']), $gpx_panel_checkbox, $_POST['controlbox_mlm_filter'], $oid );
			}else{
				$filter_details =json_encode($filter_details);
				$result = $wpdb->prepare( "UPDATE `$table_name_layers` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d, `mlm_filter` = %d, `mlm_filter_details` = %s WHERE `id` = %d", $layername_quotes, sanitize_text_field($_POST['basemap']), $_POST['layerzoom'], $_POST['mapwidth'], sanitize_text_field($_POST['mapwidthunit']), $_POST['mapheight'], $panel_checkbox, str_replace(',', '.', floatval($_POST['layerviewlat'])), str_replace(',', '.', floatval($_POST['layerviewlon'])), esc_html($_POST['createdby']), $createdon, $current_user->user_login, $updatedon, $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $listmarkers_checkbox, $multi_layer_map_checkbox, $mlm_checked_imploded, $address, $clustering_checkbox, esc_url_raw($_POST['gpx_url']), $gpx_panel_checkbox, $_POST['controlbox_mlm_filter'], $filter_details, $oid );
			}
			$wpdb->query( $result );
			$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
			//info: register layer texts for translation to support WPML
			MMP_Globals::register_single_string("Layer (ID {$oid}) name", $layername_quotes);
			MMP_Globals::register_single_string("Layer (ID {$oid}) address", $address);

			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = sprintf(__('The layer with the ID %1$s has been successfully updated','lmm'), intval($_POST['id']));
			$ajax_results['layerid'] = $oid;
			$ajax_results['layername'] = __('Edit layer','lmm') . ' "' . stripslashes($_POST['name']) . '"';
			if ($multi_layer_map_checkbox == '0') {
				$ajax_results['listmarker-table-heading'] = sprintf(__('Markers assigned to layer "%1s" (ID %2s)','lmm'), stripslashes($_POST['name']), $oid);
			} else if ($multi_layer_map_checkbox == '1') {
				$ajax_results['listmarker-table-heading'] = sprintf(__('Markers assigned to multi layer map "%1s" (ID %2s)','lmm'), stripslashes($_POST['name']), $oid);
			}
			$ajax_results['updatedby_saved'] = strip_tags($_POST['updatedby']);
			$ajax_results['updatedon_saved'] = strip_tags($_POST['updatedon']);
			$ajax_results['updatedby_next'] = $current_user->user_login;
			$ajax_results['updatedon_next'] = current_time('mysql',0);
		} else {
			$ajax_results['status-class'] = 'notice notice-error';
			$ajax_results['status-text'] = __('Error: coordinates cannot be empty!','lmm');
		}
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Error: your user does not have the permission to edit layers from other users!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
/**********************************************/
} else if ($ajax_subaction == 'layer-deleteboth') {
	//info: 2do
/**********************************************/
} else if ($ajax_subaction == 'layer-delete') {
	$createdby_check = $wpdb->get_var( 'SELECT `createdby` FROM `'.$table_name_layers.'` WHERE `id`='.$oid );
	if (MMP_Globals::check_capability('delete', $createdby_check)) {
		//info: delete qr code cache image for layer
		if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $oid . '.png') ) {
			unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $oid . '.png');
		}
		$markers_of_layer = $wpdb->get_results(" SELECT id,layer FROM  `$table_name_markers` WHERE layer LIKE '%\"".$oid."\"%' ");
		if(!empty($markers_of_layer)){
			foreach( $markers_of_layer as $marker ){
				$marker_layers = json_decode($marker->layer,true);
				if(count($marker_layers) == 1){
					$new_layer = json_encode(array("0"));
				}else{
					$layer_key = array_search($oid, $marker_layers);
					unset($marker_layers[$layer_key]);
					$new_layer = json_encode($marker_layers);
				}
				$result = $wpdb->prepare( "UPDATE `$table_name_markers` SET `layer` = '".$new_layer."' WHERE `id` = %d", $marker->id );
				$wpdb->query( $result );
			}
		}
		$result2 = $wpdb->prepare( "DELETE FROM `$table_name_layers` WHERE `id` = %d", $oid );
		$wpdb->query( $result2 );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		$ajax_results['status-class'] = 'notice notice-success';
		$ajax_results['status-text'] = sprintf(__('The layer with the ID %1$s has been successfully deleted','lmm'), $oid);
	} else {
			$ajax_results['status-class'] = 'notice notice-error';
			$ajax_results['status-text'] = __('Error: your user does not have the permission to delete layers from other users!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
/**********************************************/
} else if ($ajax_subaction == 'layer-duplicate') {
	if (MMP_Globals::check_capability('add', $current_user->user_login)) {
		$result = $wpdb->get_row( $wpdb->prepare('SELECT * FROM `'.$table_name_layers.'` WHERE `id` = %d',$oid), ARRAY_A);
		$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_layers` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter`, `mlm_filter_details`) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d, %s)", $result['name'], $result['basemap'], $result['layerzoom'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $result['layerviewlat'], $result['layerviewlon'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['listmarkers'], $result['multi_layer_map'], $result['multi_layer_map_list'], $result['address'], $result['clustering'], esc_url_raw($result['gpx_url']), $result['gpx_panel'], $result['mlm_filter'], $result['mlm_filter_details'] );
		$wpdb->query( $sql_duplicate );
		$wpdb->query( "OPTIMIZE TABLE `$table_name_layers`" );
		$ajax_results['status-class'] = 'notice notice-success';
		$ajax_results['status-text'] = sprintf(__('The layer has been successfully duplicated - new ID: %1$s','lmm'), $wpdb->insert_id);
		$ajax_results['layername'] = __('Edit layer','lmm') . ' "' . stripslashes($result['name']) . '"';
		if ($result['multi_layer_map'] == '0') {
			$ajax_results['listmarker-table-heading'] = sprintf(__('Markers assigned to layer "%1s" (ID %2s)','lmm'), $result['name'], $wpdb->insert_id);
		} else {
			$ajax_results['listmarker-table-heading'] = sprintf(__('Markers assigned to multi layer map "%1s" (ID %2s)','lmm'), $result['name'], $wpdb->insert_id);
		}
		$ajax_results['newlayerid'] = $wpdb->insert_id;
		MMP_Globals::register_single_string("Layer (ID {$result['newlayerid']}) name", $result['name']);
		MMP_Globals::register_single_string("Layer (ID {$result['newlayerid']}) address", $result['address']);
		$ajax_results['layerviewlat'] = $result['layerviewlat'];
		$ajax_results['layerviewlon'] = $result['layerviewlon'];
		$ajax_results['layerzoom'] = $result['layerzoom'];
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = __('Your user does not have the permission to duplicate this layer!','lmm');
	}
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
/**********************************************/
} else if ($ajax_subaction == 'editor-switchlink') {
	if ( ($_POST['active_editor'] == 'simplified') || ($_POST['active_editor'] == 'advanced') ) {
		update_option( 'leafletmapsmarker_editor', $_POST['active_editor'] );
		if ($_POST['active_editor'] == 'advanced') {
			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = __('Settings updated - you successfully switched to the advanced editor!','lmm');
		} else {
			$ajax_results['status-class'] = 'notice notice-success';
			$ajax_results['status-text'] = __('Settings updated - you successfully switched to the simplified editor!','lmm');
		}
		echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	} else {
		$ajax_results['status-class'] = 'notice notice-error';
		$ajax_results['status-text'] = sprintf(__('Error - active_editor value cannot be set to %1$s!','lmm'), esc_html($_POST['active_editor']));
		echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	}
	die();
}else if($ajax_subaction == 'lmm_list_markers'){
	$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links
	//info: set custom marker icon dir/url
	if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
		$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
	} else {
		$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
	}
	$layer_id = isset($_POST['layer_id']) ? intval($_POST['layer_id']) : (isset($_GET['layer_id']) ? intval($_GET['layer_id']) : '');
	$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
	$offset = ($pagenum - 1) * intval($lmm_options[ 'markers_per_page' ]);
	$layerfilter = isset($_POST['layerfilter']) ? '%"' .esc_sql($_POST['layerfilter']) . '"%' : (isset($_GET['layerfilter']) ? '%"' . esc_sql($_GET['layerfilter']) . '"%': '%');
	$layerfilter = $layerfilter == '%"0"%' ? $layerfilter = '%' : $layerfilter;
	$searchtext = isset($_POST['searchtext']) ? '%' .esc_sql($_POST['searchtext']) . '%' : (isset($_GET['searchtext']) ? '%' . esc_sql($_GET['searchtext']) : '') . '%';
	//info: security check if input variable is valid
	$columnsort_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox','m.address');
	$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_marker_listing_sort_order_by' ]);
	$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_marker_listing_sort_order_by' ];
	//info: security check if input variable is valid
	$columnsortorder_values = array('asc','desc','ASC','DESC');
	$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ]);
	$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$capabilities_view = (isset($lmm_options['capabilities_view_others']))?$lmm_options['capabilities_view_others']:'edit_posts';
	$createdby_query = (!current_user_can($capabilities_view))?" m.createdby = '". $current_user->user_login ."' ":'1=1';
	$markers_per_page_validated = intval($lmm_options[ 'markers_per_page' ]);
	$mcount = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name_markers` AS m WHERE m.layer LIKE '%s' AND (m.id LIKE '%s' OR m.markername LIKE '%s' OR m.popuptext LIKE '%s' OR m.address LIKE '%s') AND $createdby_query", $layerfilter, $searchtext, $searchtext, $searchtext, $searchtext)));
	$marker_per_page = intval($lmm_options[ 'markers_per_page' ]);
	if($layer_id != ''){
		$backend_class = '-backend';
		$mcount = intval($_POST['totalmarkers']);
		if(isset($_POST['multi_layer_map_list']) && $_POST['multi_layer_map_list']!=''){
			$multi_layer_map_list_exploded = explode(',', $_POST['multi_layer_map_list']);
			$first_mlm_id = $multi_layer_map_list_exploded[0];
			$other_mlm_ids = array_slice($multi_layer_map_list_exploded,1);
			$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $first_mlm_id . "') ";
			foreach ($other_mlm_ids as $row) {
				$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON  m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $row . "')";
			}
			$mlm_query .= " ORDER BY markerid " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['markers_per_page']) . " OFFSET $offset";
			$marklist = $wpdb->get_results($mlm_query, ARRAY_A);
		}else{
			$marklist = $wpdb->get_results( "SELECT m.id,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address,l.name AS layername,l.id as layerid FROM `$table_name_markers` AS m LEFT OUTER JOIN `$table_name_layers` AS l ON m.layer LIKE concat('%\"',". $layer_id .",'\"%') $createdby_query  GROUP BY m.id order by $columnsort $columnsortorder LIMIT $markers_per_page_validated OFFSET $offset", ARRAY_A);
		}
	}else{
		$backend_class = '';
		$marklist = $wpdb->get_results( $wpdb->prepare("SELECT m.id,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup AS openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address,l.name AS layername,l.id as layerid FROM `$table_name_markers` AS m LEFT OUTER JOIN `$table_name_layers` AS l ON m.layer=l.id WHERE m.layer like '%s' AND (m.id like '%s' OR m.markername like '%s' OR m.popuptext like '%s' OR m.address like '%s') AND $createdby_query ORDER BY $columnsort $columnsortorder LIMIT $markers_per_page_validated OFFSET $offset", $layerfilter, $searchtext, $searchtext, $searchtext, $searchtext), ARRAY_A);
	}

 	$ajax_results['mcount'] = $mcount;
 	$ajax_results['rows'] = '';
	$ml_checked = MMP_Globals::check_multilingual();
	foreach ($marklist as $row) {
		if (MMP_Globals::check_capability('delete', $row['createdby'])) {
			$delete_link_marker = '&nbsp;|&nbsp;<a style="color:red;" onclick="if ( confirm( \'' . esc_attr__('Do you really want to delete this marker?', 'lmm') . ' (' . $row['markername'] . ' - ID ' . $row['id'] . ')\' ) ) { return true;}return false;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&action=delete&id=' . $row['id'] . '&_wpnonce=' . $markernonce . '">' . __('delete','lmm') . '</a>';
		} else {
			$delete_link_marker = '';
		}
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$edit_link_marker = '<strong><a title="' . esc_attr__('edit marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('edit','lmm') . '</a>';
			$duplicate_link_marker = '&nbsp;|&nbsp;<a style="" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&id=' . $row['id'] . '&action=duplicate&_wpnonce=' . $markernonce . '">' . __('duplicate','lmm') . '</a>';
		} else {
			$edit_link_marker = '&nbsp;|&nbsp;<a title="' . esc_attr__('View marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('View marker','lmm') . '</a>';
			$duplicate_link_marker = '';
		}
		if ($ml_checked == 'wpml') {
			$translate_url = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($row['markername']));
		} elseif ($ml_checked == 'pll') {
			$translate_url = admin_url('admin.php?page=mlang_strings&s='. urlencode($row['markername']) . '&group=Maps+Marker+Pro');
		} else {
			$translate_url = 'https://www.mapsmarker.com/multilingual';
		}
		$translate_link = '&nbsp;|&nbsp;<a href="'. $translate_url .'">'.  __('translate', 'lmm')  .'</a>';
		//info: since 2.4 get thelayers @Waseem
		$rowlayername = '';
		$marker_layers = json_decode($row['layer'],true);

		if(!empty($marker_layers)) {
			$layers = $wpdb->get_results('SELECT id,name as layername FROM '.$table_name_layers.' WHERE id IN('.implode(',', $marker_layers).')',ARRAY_A);
			foreach($layers as $layer) {
				if (MMP_Globals::check_capability('edit', $row['createdby'])) {
					$rowlayername .= ($layer['id'] == 0) ? "" . __('unassigned','lmm') . "<br/>" : "<a title='" . esc_attr__('Edit layer ','lmm') . $layer['id'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $layer['id'] . "'>" . htmlspecialchars($layer['layername']) . " (ID " .$layer['id'] . ")</a><br/>";
				} else {
					$rowlayername .= ($layer['id'] == 0) ? "" . __('unassigned','lmm') . "<br/>" : "<a title='" . esc_attr__('view layer ','lmm') . $layer['id'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $layer['id'] . "'>" . htmlspecialchars($layer['layername']) . " (ID " .$layer['id'] . ")</a><br/>";
				}
			}
		}
		$openpopupstatus = ($row['openpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
		$openpanelstatus = ($row['panel'] == 1) ? __('visible','lmm') : __('hidden','lmm');
		 if ($row['controlbox'] == 0) {
			$controlboxstatus = __('hidden','lmm');
		} else if ($row['controlbox'] == 1) {
			$controlboxstatus = __('collapsed (except on mobiles)','lmm');
		} else if ($row['controlbox'] == 2) {
			$controlboxstatus = __('expanded','lmm');
		}
		$column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border"  data-colname="'.esc_attr__('Location', 'lmm').'">' . stripslashes(htmlspecialchars($row['address'])) . '</td>' : '';
		 $popuptextabstract = (strlen($row['popuptext']) >= 90) ? "...": "";
		 //info: set column display variables - need for for-each
		 if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ? '<td class="lmm-border"><a title="' . esc_attr__('edit marker ', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
		} else {
			$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ? '<td class="lmm-border"><a title="' . esc_attr__('View marker', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
		}
		 $column_layer = ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Layer', 'lmm').'">' . stripslashes($rowlayername) . '</td>' : '';
		 $column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Popup status', 'lmm').'">' . $openpopupstatus . '</td>' : '';
		 $column_panelstatus = ((isset($lmm_options[ 'misc_marker_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_panelstatus' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Panel status', 'lmm').'">' . $openpanelstatus . '</td>' : '';
		 $column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Coordinates', 'lmm').'">Lat: ' . $row['lat'] . '<br/>Lon: ' . $row['lon'] . '</td>' : '';
		 $column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Map Size', 'lmm').'">' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
		 $column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Zoom', 'lmm').'">' . $row['zoom'] . '</td>' : '';
		 $column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Controlbox status', 'lmm').'">' . $controlboxstatus . '</td>' : '';
		 //info: workaround - select shortcode on input focus doesnt work on iOS
		 if ( version_compare( $wp_version, '3.4', '>=' ) ) {
			 $is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
			 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
		 } else {
			 $shortcode_select = '';
		 }
		 $column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Shortcode', 'lmm').'"><input ' . $shortcode_select . ' style="width:100%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['id'] . '&quot;]"></td>' : '';
		 $column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('KML', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['id'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
		 $column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Fullscreen', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
		 $column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('QR Code', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
		 $column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoJSON', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['id'] . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
		 $column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoRSS', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
		 $column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Wikitude', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
		 $column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Basemap', 'lmm').'">' . $row['basemap'] . '</td>' : '';
		 $column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created by', 'lmm').'">' . $row['createdby'] . '</td>' : '';
		 $column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created on', 'lmm').'">' . $row['createdon'] . '</td>' : '';
		 $column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated by', 'lmm').'">' . $row['updatedby'] . '</td>' : '';
		 $column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated on', 'lmm').'">' . $row['updatedon'] . '</td>' : '';
		 $column_usedincontent = ((isset($lmm_options[ 'misc_marker_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_used_in_content' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Used in content', 'lmm').'">' . MMP_Globals::get_map_shortcodes($row['id'], 'marker') . '</td>' : '';
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$css_table_background = '';
		} else {
			$css_table_background = 'background:#f6f6f6;';
		}
		$ajax_results['rows'] .= '<tr valign="middle" class="alternate" id="link-' . $row['id'] . '" style="' . $css_table_background . '">
		  <th class="check-column" scope="row" style="border-bottom:1px solid #DFDFDF;"><input type="checkbox" value="' . $row['id'] . '" name="checkedmarkers[]"></th>
		  <td class="lmm-border before_primary" data-colname="'.esc_attr__('ID', 'lmm').'">' . $row['id'] . '</td>
		  <td class="lmm-border column-primary">' . $edit_link_marker . $duplicate_link_marker . $delete_link_marker . $translate_link . '</div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
		  <td class="lmm-border" data-colname="'.esc_attr__('Icon', 'lmm').'">';

		  if ($row['icon'] != null) {
			 $ajax_results['rows'] .= '<img src="' . $defaults_marker_icon_url . '/' . esc_html($row['icon']) . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_html($row['icon']) . '" />';
			 } else {
			 $ajax_results['rows'] .= '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';};
		  $ajax_results['rows'] .= '</td>
			  ' . $column_address . '
			  ' . $column_popuptext . '
			  ' . $column_layer . '
			  ' . $column_openpopup . '
			  ' . $column_panelstatus . '
			  ' . $column_coordinates . '
			  ' . $column_mapsize . '
			  ' . $column_zoom . '
			  ' . $column_basemap . '
			  ' . $column_createdby . '
			  ' . $column_createdon . '
			  ' . $column_updatedby . '
			  ' . $column_updatedon . '
			  ' . $column_controlbox . '
			  ' . $column_usedincontent . '
			  ' . $column_shortcode . '
			  ' . $column_kml . '
			  ' . $column_fullscreen . '
			  ' . $column_qr_code . '
			  ' . $column_geojson . '
			  ' . $column_georss . '
			  ' . $column_wikitude . '
			  </tr>';
	}//info: end foreach
	//info:  get pagination
	$radius = 1;
	$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
	if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
	if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
	$pager = '';
	if ($mcount > intval($lmm_options[ 'markers_per_page' ])) {
	  $maxpage = intval(ceil($mcount / intval($lmm_options[ 'markers_per_page' ])));
	  if ($maxpage > 1) {
	    $pager .= '' . __('Markers per page','lmm') . ': ';
		if (current_user_can('activate_plugins')) {
			$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_markers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		} else {
			$pager .= intval($lmm_options[ "markers_per_page" ]) . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		}
		$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">' . __('page','lmm') . ' ';
		$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
		$pager .= '<input type="hidden" name="order" value="' . $columnsortorder . '" />';
	    if($layer_id != ''){
			$pager .= '<input type="hidden" name="layer_id" value="' . $layer_id . '" />';
			$pager .= '<input type="hidden" name="multi_layer_map_list" value="' . $_POST['multi_layer_map_list'] . '" />';
			$pager .= '<input type="hidden" name="totalmarkers" value="' . $mcount . '" />';
	    }
	    if ($pagenum > (2 + $radius * 2)) {
	      foreach (range(1, 1 + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum > 1)
	        foreach (range(1, $pagenum - 1) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '<a href="#" class="first-page'. $backend_class .' current-page">' . $pagenum . '</a>';
	    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
	      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($maxpage - $radius, $maxpage) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum < $maxpage)
	        foreach (range($pagenum + 1, $maxpage) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '</form>';
	  }
	}
	$ajax_results['pager'] = $pager;
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}else if($ajax_subaction == 'lmm_list_layers'){
	$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links


	$searchtext = isset($_POST['searchtext']) ? '%' .esc_sql($_POST['searchtext']) . '%' : (isset($_GET['searchtext']) ? '%' . esc_sql($_GET['searchtext']) : '') . '%';
	$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
	$offset = ($pagenum - 1) * intval($lmm_options[ 'layers_per_page' ]);

	//info: security check if input variable is valid
	$columnsort_values = array('id','multi_layer_map','name','m.panel','zoom','basemap','createdon','createdby','updatedon','updatedby','controlbox','address');
	$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_layer_listing_sort_order_by' ]);
	$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_layer_listing_sort_order_by' ];
	//info: security check if input variable is valid
	$columnsortorder_values = array('asc','desc','ASC','DESC');
	$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_layer_listing_sort_sort_order' ]);
	$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_layer_listing_sort_sort_order' ];

	$capabilities_view = (isset($lmm_options['capabilities_view_others']))?$lmm_options['capabilities_view_others']:'edit_posts';
	$createdby_query = (!current_user_can($capabilities_view))?" createdby = '". $current_user->user_login ."' ":'1=1';
	$layers_per_page_validated = intval($lmm_options[ 'layers_per_page' ]);
	$layer_per_page = intval($lmm_options[ 'layers_per_page' ]);

	$backend_class = '';
	$lcount = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name_layers` WHERE (`id` LIKE '%s' OR `name` LIKE '%s' OR `address` LIKE '%s') AND $createdby_query AND `id`>0", $searchtext, $searchtext, $searchtext)));
	$layerlist = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE (`id` LIKE '%s' OR `name` LIKE '%s' OR `address` like '%s') AND $createdby_query AND `id`>0 ORDER BY `$columnsort` $columnsortorder LIMIT $layers_per_page_validated OFFSET $offset", $searchtext, $searchtext, $searchtext), ARRAY_A );

 	$ajax_results['mcount'] = $lcount;
 	$ajax_results['rows'] = '';
	$ml_checked = MMP_Globals::check_multilingual();
 	foreach ($layerlist as $row)
		{
		$markercount = 0; //info: needed for multi-layer-map count-bug
		if (MMP_Globals::check_capability('delete', $row['createdby'])) {
			$delete_link_layer  = '&nbsp;|&nbsp;<a style="color:red;" onclick="lmm_delete_layer( '.$row['id'].' )" href="javascript:void(0);" class="delete">' . __('delete layer','lmm') . '</a>';
		} else {
			$delete_link_layer = '';
		}
		if ($ml_checked == 'wpml') {
			$translate_url = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($row['name']));
		} elseif ($ml_checked == 'pll') {
			$translate_url = admin_url('admin.php?page=mlang_strings&s='. urlencode($row['name']) . '&group=Maps+Marker+Pro');
		} else {
			$translate_url = 'https://www.mapsmarker.com/multilingual';
		}
		$translate_link = '&nbsp;|&nbsp;<a href="'. $translate_url .'">'.  __('translate', 'lmm')  .'</a>';
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$edit_link_layer = '<a title="' . esc_attr__('Edit', 'lmm') . ' &laquo;' . htmlspecialchars($row['name']) . '&raquo;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['name'])) . '</a></strong><br><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '">' . __('edit layer','lmm') . '</a>';
		} else {
			$edit_link_layer = '<a title="' . esc_attr__('view', 'lmm') . ' &laquo;' . htmlspecialchars($row['name']) . '&raquo;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['name'])) . '</a></strong><br><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '">' . __('view layer','lmm') . '</a>';
		}
		$column_address = ((isset($lmm_options[ 'misc_layer_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_address' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Location','lmm').'">' . $row['address'] . '</td>' : '';
		if ($row['multi_layer_map'] == 0) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$row['id']);
		} else 	if ( ($row['multi_layer_map'] == 1) && ( $row['multi_layer_map_list'] == 'all' ) ) {
			$markercount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
		} else 	if ( ($row['multi_layer_map'] == 1) && ( $row['multi_layer_map_list'] != NULL ) && ($row['multi_layer_map_list'] != 'all') ) {
			$multi_layer_map_list_exploded = explode(",", $wpdb->get_var('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id='.$row['id']));
			foreach ($multi_layer_map_list_exploded as $mlmrowcount){
				$mlm_count_temp{$mlmrowcount} = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$mlmrowcount);
				$markercount = $markercount + $mlm_count_temp{$mlmrowcount};
			}
		} else 	if ( ($row['multi_layer_map'] == 1) && ( $row['multi_layer_map_list'] == NULL ) ) {
			$markercount = 0;
		}
		$multi_layer_map_type = ($row['multi_layer_map'] == 0) ? '&nbsp;&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-layer.png" width="16" height="16" title="' . esc_attr__('single layer map','lmm') . '" />' : '&nbsp;&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-multi_layer_map.png" width="16" height="16" title="' . esc_attr__('multi layer map','lmm') . '" />';
		$openpanelstatus = ($row['panel'] == 1) ? __('visible','lmm') : __('hidden','lmm');
		if ($row['controlbox'] == 0) { $controlboxstatus = __('hidden','lmm'); } else if ($row['controlbox'] == 1) { $controlboxstatus = __('collapsed (except on mobiles)','lmm'); } else if ($row['controlbox'] == 2) { $controlboxstatus = __('expanded','lmm'); };

		 //info: set column display variables - need for for-each
		 $column_layercenter = ((isset($lmm_options[ 'misc_layer_listing_columns_layercenter' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_layercenter' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Layer Center', 'lmm').'">Lat: ' . $row['layerviewlat'] . '<br/>Lon: ' . $row['layerviewlon'] . '</td>' : '';
		 $column_mapsize = ((isset($lmm_options[ 'misc_layer_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Width', 'lmm').'">' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
		 $column_panelstatus = ((isset($lmm_options[ 'misc_layer_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_panelstatus' ] == 1 )) ?
		'<td class="lmm-border" data-colname="'.esc_attr__('Panel Status', 'lmm').'">' . $openpanelstatus . '</td>' : '';
		 $column_zoom = ((isset($lmm_options[ 'misc_layer_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Zoom', 'lmm').'">' . $row['layerzoom'] . '</td>' : '';
		 $column_controlbox = ((isset($lmm_options[ 'misc_layer_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Control box status', 'lmm').'">' . $controlboxstatus . '</td>' : '';
		 //info: workaround - select shortcode on input focus doesnt work on iOS
		 if ( version_compare( $wp_version, '3.4', '>=' ) ) {
			 $is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
			 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
		 } else {
			 $shortcode_select = '';
		 }
		 $column_shortcode = ((isset($lmm_options[ 'misc_layer_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="Shortcode"><input ' . $shortcode_select . ' style="width:95%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' layer=&quot;' . $row['id'] . '&quot;]"></td>' : '';
		 $column_kml = ((isset($lmm_options[ 'misc_layer_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('KML', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['id'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
		 $column_fullscreen = ((isset($lmm_options[ 'misc_layer_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Fullscreen', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
		 $column_qr_code = ((isset($lmm_options[ 'misc_layer_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('QR Code', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
		if ($row['multi_layer_map_list'] == NULL ) { $geojson_api_link = $row['id']; } else { $geojson_api_link = $row['multi_layer_map_list']; }
		$column_geojson = ((isset($lmm_options[ 'misc_layer_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('GeoJSON', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $geojson_api_link . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
		 $column_georss = ((isset($lmm_options[ 'misc_layer_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('GeoRSS', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
		 $column_wikitude = ((isset($lmm_options[ 'misc_layer_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('Wikitude', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
		 $column_basemap = ((isset($lmm_options[ 'misc_layer_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border"  data-colname="'.esc_attr__('Basemap', 'lmm').'">' . $row['basemap'] . '</td>' : '';
		 $column_createdby = ((isset($lmm_options[ 'misc_layer_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created By', 'lmm').'">' . $row['createdby'] . '</td>' : '';
		 $column_createdon = ((isset($lmm_options[ 'misc_layer_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created On', 'lmm').'">' . $row['createdon'] . '</td>' : '';
		 $column_updatedby = ((isset($lmm_options[ 'misc_layer_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated By', 'lmm').'">' . $row['updatedby'] . '</td>' : '';
		 $column_updatedon = ((isset($lmm_options[ 'misc_layer_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated On', 'lmm').'">' . $row['updatedon'] . '</td>' : '';
		 $add_new_marker_to_layer = ( $row['multi_layer_map'] == 0 ) ? '&nbsp;|&nbsp;<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $row['id'] . '&lat=' . $row['layerviewlat'] . '&lon=' . $row['layerviewlon'] . '&zoom=' . $row['layerzoom'] . '" style="text-decoration:none;">' . __('add new marker to this layer','lmm') . '</a>' : '';
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$css_table_background = '';
		} else {
			$css_table_background = 'background:#efefef;';
		}
		$layer_type = ($row['multi_layer_map'] == 0)?'layer':'mlm';
		$ajax_results['rows'] .= '<tr valign="middle" class="alternate iedit author-self hentry" id="link-' . $row['id'] . '" style="' . $css_table_background . '">
		<th class="lmm-border check-column " scope="row"><input type="checkbox" value="' . $row['id'] . '" name="checkedlayers[]" data-layertype="'. $layer_type .'"></th>

		<td class="lmm-border before_primary" data-colname="'.esc_attr__('ID', 'lmm').'">'.$row['id'].' </td>
		<td class="lmm-border before_primary" data-colname="'.esc_attr__('Type', 'lmm').'">'.$multi_layer_map_type.'</td>
		<td class="lmm-border column-primary has-row-actions" data-colname="'.esc_attr__('Name', 'lmm').'"><strong>' . $edit_link_layer . $add_new_marker_to_layer . $delete_link_layer . $translate_link . '</span></div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button> </td>
		  ' . $column_address . '
		<td class="lmm-border centeralize" style="text-align:center;" data-colname="'.esc_attr__('# Markers', 'lmm').'"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '#assigned_markers" title="' . esc_attr__('show markers assigned to this layer','lmm') . '">'.$markercount.'</a></td>
		  ' . $column_layercenter . '
		  ' . $column_mapsize . '
		  ' . $column_panelstatus . '
		  ' . $column_zoom . '
		  ' . $column_basemap . '
		  ' . $column_createdby . '
		  ' . $column_createdon . '
		  ' . $column_updatedby . '
		  ' . $column_updatedon . '
		  ' . $column_controlbox;
		  $ajax_results['rows'] .= ((isset($lmm_options[ 'misc_layer_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_used_in_content' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Used in content', 'lmm').'">' . MMP_Globals::get_map_shortcodes($row['id'], 'layer') . '</td>' : '';
	      $ajax_results['rows'] .= $column_shortcode . '
		  ' . $column_kml . '
		  ' . $column_fullscreen . '
		  ' . $column_qr_code . '
		  ' . $column_geojson . '
		  ' . $column_georss . '
		  ' . $column_wikitude . '
		</tr>';
				}

	//info:  get pagination
	$radius = 1;
	$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_layer_listing_sort_sort_order' ];
	$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
	if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
	if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
	$pager = '';
	if ($lcount > intval($lmm_options[ 'layers_per_page' ])) {
	  $maxpage = intval(ceil($lcount / intval($lmm_options[ 'layers_per_page' ])));
	  if ($maxpage > 1) {
	    $pager .= '' . __('Layers per page','lmm') . ': ';
		if (isset($lmm_options[ "layers_per_page" ])) {
		$layers_per_page = intval($lmm_options[ "layers_per_page" ]);
		} else {
			$layers_per_page = 30; //info: for beta tester updating from 2.8-beta1 to 2.8
		}
		if (current_user_can('activate_plugins')) {
			$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_layers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . $layers_per_page . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		} else {
			$pager .= $layers_per_page . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		}
		$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers">' . __('page','lmm') . ' ';
		$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
		$pager .= '<input type="hidden" name="order" value="' . $columnsortorder . '" />';
	    if ($pagenum > (2 + $radius * 2)) {
	      foreach (range(1, 1 + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum > 1)
	        foreach (range(1, $pagenum - 1) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '<a href="#" class="first-page'. $backend_class .' current-page">' . $pagenum . '</a>';
	    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
	      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($maxpage - $radius, $maxpage) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum < $maxpage)
	        foreach (range($pagenum + 1, $maxpage) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '</form>';
	  }
	}
	$ajax_results['pager'] = $pager;
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}else if($ajax_subaction == 'lmm_list_markers_search'){
	$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links
	//info: set custom marker icon dir/url
	if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
		$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
	} else {
		$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
	}
	$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
	$offset =0;
	$layerfilter = isset($_POST['layerfilter']) ? '%"' .esc_sql($_POST['layerfilter']) . '"%' : (isset($_GET['layerfilter']) ? '%"' . esc_sql($_GET['layerfilter']) . '"%': '%');
	$layerfilter = $layerfilter == '%"0"%' ? $layerfilter = '%' : $layerfilter;
	$searchtext = isset($_POST['searchtext']) ? '%' .esc_sql($_POST['searchtext']) . '%' : (isset($_GET['searchtext']) ? '%' . esc_sql($_GET['searchtext']) : '') . '%';
	//info: security check if input variable is valid
	$columnsort_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox','m.address');
	$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_marker_listing_sort_order_by' ]);
	$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_marker_listing_sort_order_by' ];
	//info: security check if input variable is valid
	$columnsortorder_values = array('asc','desc','ASC','DESC');
	$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ]);
	$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$markers_per_page_validated = intval($lmm_options[ 'markers_per_page' ]);
	$capabilities_view = (isset($lmm_options['capabilities_view_others']))?$lmm_options['capabilities_view_others']:'edit_posts';
	$createdby_query = (!current_user_can($capabilities_view))?" m.createdby = '". $current_user->user_login ."' ":'1=1';
	$mcount = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name_markers` AS m WHERE m.layer LIKE '%s' AND (m.id LIKE '%s' OR m.markername LIKE '%s' OR m.popuptext LIKE '%s' OR m.address LIKE '%s') AND $createdby_query", $layerfilter, $searchtext, $searchtext, $searchtext, $searchtext)));
	$marklist = $wpdb->get_results( $wpdb->prepare("SELECT m.id,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup AS openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address,l.name AS layername,l.id as layerid FROM `$table_name_markers` AS m LEFT OUTER JOIN `$table_name_layers` AS l ON m.layer=l.id WHERE m.layer like '%s' AND (m.id like '%s' OR m.markername like '%s' OR m.popuptext like '%s' OR m.address like '%s') AND $createdby_query ORDER BY $columnsort $columnsortorder LIMIT $markers_per_page_validated OFFSET $offset", $layerfilter, $searchtext, $searchtext, $searchtext, $searchtext), ARRAY_A);
	$ajax_results['mcount'] = $mcount;
	$ajax_results['rows'] = '';
	foreach ($marklist as $row) {
		if (MMP_Globals::check_capability('delete', $row['createdby'])) {
			$delete_link_marker = '<div style="float:right;"><a style="color:red;" onclick="if ( confirm( \'' . esc_attr__('Do you really want to delete this marker?', 'lmm') . ' (' . $row['markername'] . ' - ID ' . $row['id'] . ')\' ) ) { return true;}return false;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&action=delete&id=' . $row['id'] . '&_wpnonce=' . $markernonce . '">' . __('delete','lmm') . '</a></div>';
		} else {
			$delete_link_marker = '';
		}
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$edit_link_marker = '<strong><a title="' . esc_attr__('edit marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('edit','lmm') . '</a>';
			$duplicate_link_marker = '<a style="margin-left:20px;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&id=' . $row['id'] . '&action=duplicate&_wpnonce=' . $markernonce . '">' . __('duplicate','lmm') . '</a>';
		} else {
			$edit_link_marker = '<a title="' . esc_attr__('View marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('View marker','lmm') . '</a>';
			$duplicate_link_marker = '';
		}
		//info: since 2.4 get thelayers @Waseem
		$rowlayername = '';
		$marker_layers = json_decode($row['layer'],true);

		if(!empty($marker_layers)) {
			$layers = $wpdb->get_results('SELECT id,name as layername FROM '.$table_name_layers.' WHERE id IN('.implode(',', $marker_layers).')',ARRAY_A);
			foreach($layers as $layer) {
				if (MMP_Globals::check_capability('edit', $row['createdby'])) {
					$rowlayername .= ($layer['id'] == 0) ? "" . __('unassigned','lmm') . "<br/>" : "<a title='" . esc_attr__('Edit layer ','lmm') . $layer['id'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $layer['id'] . "'>" . htmlspecialchars($layer['layername']) . " (ID " .$layer['id'] . ")</a><br/>";
				} else {
					$rowlayername .= ($layer['id'] == 0) ? "" . __('unassigned','lmm') . "<br/>" : "<a title='" . esc_attr__('view layer ','lmm') . $layer['id'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $layer['id'] . "'>" . htmlspecialchars($layer['layername']) . " (ID " .$layer['id'] . ")</a><br/>";
				}
			}
		}
		$openpopupstatus = ($row['openpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
		$openpanelstatus = ($row['panel'] == 1) ? __('visible','lmm') : __('hidden','lmm');
		 if ($row['controlbox'] == 0) {
			$controlboxstatus = __('hidden','lmm');
		} else if ($row['controlbox'] == 1) {
			$controlboxstatus = __('collapsed (except on mobiles)','lmm');
		} else if ($row['controlbox'] == 2) {
			$controlboxstatus = __('expanded','lmm');
		}

		$column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border"  data-colname="'.esc_attr__('Location', 'lmm').'">' . stripslashes(htmlspecialchars($row['address'])) . '</td>' : '';
		 $popuptextabstract = (strlen($row['popuptext']) >= 90) ? "...": "";
		 //info: set column display variables - need for for-each
		 if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ? '<td class="lmm-border"><a title="' . esc_attr__('edit marker ', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
		} else {
			$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ? '<td class="lmm-border"><a title="' . esc_attr__('View marker', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
		}
		 $column_layer = ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Layer', 'lmm').'">' . stripslashes($rowlayername) . '</td>' : '';
		 $column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Popup status', 'lmm').'">' . $openpopupstatus . '</td>' : '';
		 $column_panelstatus = ((isset($lmm_options[ 'misc_marker_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_panelstatus' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Panel status', 'lmm').'">' . $openpanelstatus . '</td>' : '';
		 $column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Coordinates', 'lmm').'">Lat: ' . $row['lat'] . '<br/>Lon: ' . $row['lon'] . '</td>' : '';
		 $column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Map Size', 'lmm').'">' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
		 $column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Zoom', 'lmm').'">' . $row['zoom'] . '</td>' : '';
		 $column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Controlbox status', 'lmm').'">' . $controlboxstatus . '</td>' : '';
		 //info: workaround - select shortcode on input focus doesnt work on iOS
		 if ( version_compare( $wp_version, '3.4', '>=' ) ) {
			 $is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
			 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
		 } else {
			 $shortcode_select = '';
		 }
		 $column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Shortcode', 'lmm').'"><input ' . $shortcode_select . ' style="width:100%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['id'] . '&quot;]"></td>' : '';
		 $column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('KML', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['id'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
		 $column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Fullscreen', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
		 $column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('QR Code', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
		 $column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoJSON', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['id'] . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
		 $column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoRSS', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
		 $column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Wikitude', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
		 $column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Basemap', 'lmm').'">' . $row['basemap'] . '</td>' : '';
		 $column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created by', 'lmm').'">' . $row['createdby'] . '</td>' : '';
		 $column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created on', 'lmm').'">' . $row['createdon'] . '</td>' : '';
		 $column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated by', 'lmm').'">' . $row['updatedby'] . '</td>' : '';
		 $column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated on', 'lmm').'">' . $row['updatedon'] . '</td>' : '';
		 $column_usedincontent = ((isset($lmm_options[ 'misc_marker_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_used_in_content' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Used in content', 'lmm').'">' . MMP_Globals::get_map_shortcodes($row['id'], 'marker') . '</td>' : '';
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$css_table_background = '';
		} else {
			$css_table_background = 'background:#f6f6f6;';
		}
		$ajax_results['rows'] .= '<tr valign="middle" class="alternate" id="link-' . $row['id'] . '" style="' . $css_table_background . '">
		  <th class="check-column" scope="row" style="border-bottom:1px solid #DFDFDF;"><input type="checkbox" value="' . $row['id'] . '" name="checkedmarkers[]"></th>
		  <td class="lmm-border before_primary" data-colname="'.esc_attr__('ID', 'lmm').'">' . $row['id'] . '</td>
		  <td class="lmm-border column-primary">' . $edit_link_marker . $duplicate_link_marker . $delete_link_marker . '</div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
		  <td class="lmm-border" data-colname="'.esc_attr__('Icon', 'lmm').'">';

		  if ($row['icon'] != null) {
			 $ajax_results['rows'] .= '<img src="' . $defaults_marker_icon_url . '/' . esc_html($row['icon']) . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_html($row['icon']) . '" />';
			 } else {
			 $ajax_results['rows'] .= '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';};
		  $ajax_results['rows'] .= '</td>
			  ' . $column_address . '
			  ' . $column_popuptext . '
			  ' . $column_layer . '
			  ' . $column_openpopup . '
			  ' . $column_panelstatus . '
			  ' . $column_coordinates . '
			  ' . $column_mapsize . '
			  ' . $column_zoom . '
			  ' . $column_basemap . '
			  ' . $column_createdby . '
			  ' . $column_createdon . '
			  ' . $column_updatedby . '
			  ' . $column_updatedon . '
			  ' . $column_controlbox . '
			  ' . $column_usedincontent . '
			  ' . $column_shortcode . '
			  ' . $column_kml . '
			  ' . $column_fullscreen . '
			  ' . $column_qr_code . '
			  ' . $column_geojson . '
			  ' . $column_georss . '
			  ' . $column_wikitude . '
			  </tr>';
	}//info: end foreach
	//info:  get pagination
	$radius = 1;
	$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
	if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
	if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
	$pager = '';
	if ($mcount > intval($lmm_options[ 'markers_per_page' ])) {
	  $maxpage = intval(ceil($mcount / intval($lmm_options[ 'markers_per_page' ])));
	  if ($maxpage > 1) {
	    $pager .= '' . __('Markers per page','lmm') . ': ';
		if (current_user_can('activate_plugins')) {
			$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_markers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		} else {
			$pager .= intval($lmm_options[ "markers_per_page" ]) . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		}
		$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">' . __('page','lmm') . ' ';
		$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
		$pager .= '<input type="hidden" name="order" value="' . $columnsortorder . '" />';
	    if($layer_id != ''){
			$pager .= '<input type="hidden" name="layer_id" value="' . $layer_id . '" />';
			$pager .= '<input type="hidden" name="multi_layer_map_list" value="' . $_POST['multi_layer_map_list'] . '" />';
			$pager .= '<input type="hidden" name="totalmarkers" value="' . $mcount . '" />';
	    }
	    if ($pagenum > (2 + $radius * 2)) {
	      foreach (range(1, 1 + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum > 1)
	        foreach (range(1, $pagenum - 1) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '<a href="#" class="first-page'. $backend_class .' current-page">' . $pagenum . '</a>';
	    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
	      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($maxpage - $radius, $maxpage) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum < $maxpage)
	        foreach (range($pagenum + 1, $maxpage) as $num) {
				$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
			}
	    $pager .= '</form>';
	  }
	}
	$ajax_results['pager'] = $pager;
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}else if($ajax_subaction == 'lmm_list_layers_search'){
	$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links
	$searchtext = isset($_POST['searchtext']) ? '%' .esc_sql($_POST['searchtext']) . '%' : (isset($_GET['searchtext']) ? '%' . esc_sql($_GET['searchtext']) : '') . '%';
	$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
	$offset = ($pagenum - 1) * intval($lmm_options[ 'markers_per_page' ]);

	//info: security check if input variable is valid
	$columnsort_values = array('id','multi_layer_map','name','m.panel','zoom','basemap','createdon','createdby','updatedon','updatedby','controlbox','address');
	$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_layer_listing_sort_order_by' ]);
	$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_layer_listing_sort_order_by' ];
	//info: security check if input variable is valid
	$columnsortorder_values = array('asc','desc','ASC','DESC');
	$columnsortorder_input = isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_layer_listing_sort_sort_order' ];
	$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_layer_listing_sort_sort_order' ];

	$capabilities_view = (isset($lmm_options['capabilities_view_others']))?$lmm_options['capabilities_view_others']:'edit_posts';
	$createdby_query = (!current_user_can($capabilities_view))?" createdby = '". $current_user->user_login ."' ":'1=1';
	$markers_per_page_validated = intval($lmm_options[ 'markers_per_page' ]);
	$marker_per_page = intval($lmm_options[ 'markers_per_page' ]);

	$backend_class = '';
	$lcount = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name_layers` WHERE (`id` LIKE '%s' OR `name` LIKE '%s' OR `address` LIKE '%s') AND `id`>0", $searchtext, $searchtext, $searchtext)));
	$layerlist = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `$table_name_layers` WHERE (`id` LIKE '%s' OR `name` LIKE '%s' OR `address` like '%s') AND `id`>0 ORDER BY $columnsort $columnsortorder", $searchtext, $searchtext, $searchtext), ARRAY_A);

 	$ajax_results['mcount'] = $lcount;
 	$ajax_results['rows'] = '';
 	foreach ($layerlist as $row)
		{
		$markercount = 0; //info: needed for multi-layer-map count-bug
		if (MMP_Globals::check_capability('delete', $row['createdby'])) {
			$delete_link_layer  = '&nbsp;|&nbsp;<a style="color:red;" onclick="lmm_delete_layer( '.$row['id'].' )" href="javascript:void(0);" class="delete">' . __('delete layer','lmm') . '</a>';
		} else {
			$delete_link_layer = '';
		}

		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$edit_link_layer = '<a title="' . esc_attr__('Edit', 'lmm') . ' &laquo;' . htmlspecialchars($row['name']) . '&raquo;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['name'])) . '</a></strong><br><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '">' . __('edit layer','lmm') . '</a>';
		} else {
			$edit_link_layer = '<a title="' . esc_attr__('view', 'lmm') . ' &laquo;' . htmlspecialchars($row['name']) . '&raquo;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['name'])) . '</a></strong><br><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '">' . __('view layer','lmm') . '</a>';
		}
		$column_address = ((isset($lmm_options[ 'misc_layer_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_address' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Location','lmm').'">' . $row['address'] . '</td>' : '';
		if ($row['multi_layer_map'] == 0) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$row['id']);
		} else 	if ( ($row['multi_layer_map'] == 1) && ( $row['multi_layer_map_list'] == 'all' ) ) {
			$markercount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
		} else 	if ( ($row['multi_layer_map'] == 1) && ( $row['multi_layer_map_list'] != NULL ) && ($row['multi_layer_map_list'] != 'all') ) {
			$multi_layer_map_list_exploded = explode(",", $wpdb->get_var('SELECT l.multi_layer_map_list FROM `'.$table_name_layers.'` as l WHERE l.id='.$row['id']));
			foreach ($multi_layer_map_list_exploded as $mlmrowcount){
				$mlm_count_temp{$mlmrowcount} = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$mlmrowcount);
				$markercount = $markercount + $mlm_count_temp{$mlmrowcount};
			}
		} else 	if ( ($row['multi_layer_map'] == 1) && ( $row['multi_layer_map_list'] == NULL ) ) {
			$markercount = 0;
		}
		$multi_layer_map_type = ($row['multi_layer_map'] == 0) ? '&nbsp;&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-layer.png" width="16" height="16" title="' . esc_attr__('single layer map','lmm') . '" />' : '&nbsp;&nbsp;<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-multi_layer_map.png" width="16" height="16" title="' . esc_attr__('multi layer map','lmm') . '" />';
		$openpanelstatus = ($row['panel'] == 1) ? __('visible','lmm') : __('hidden','lmm');
		if ($row['controlbox'] == 0) { $controlboxstatus = __('hidden','lmm'); } else if ($row['controlbox'] == 1) { $controlboxstatus = __('collapsed (except on mobiles)','lmm'); } else if ($row['controlbox'] == 2) { $controlboxstatus = __('expanded','lmm'); };

		 //info: set column display variables - need for for-each
		 $column_layercenter = ((isset($lmm_options[ 'misc_layer_listing_columns_layercenter' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_layercenter' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Layer Center', 'lmm').'">Lat: ' . $row['layerviewlat'] . '<br/>Lon: ' . $row['layerviewlon'] . '</td>' : '';
		 $column_mapsize = ((isset($lmm_options[ 'misc_layer_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Width', 'lmm').'">' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
		 $column_panelstatus = ((isset($lmm_options[ 'misc_layer_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_panelstatus' ] == 1 )) ?
		'<td class="lmm-border" data-colname="'.esc_attr__('Panel Status', 'lmm').'">' . $openpanelstatus . '</td>' : '';
		 $column_zoom = ((isset($lmm_options[ 'misc_layer_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Zoom', 'lmm').'">' . $row['layerzoom'] . '</td>' : '';
		 $column_controlbox = ((isset($lmm_options[ 'misc_layer_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Control box status', 'lmm').'">' . $controlboxstatus . '</td>' : '';
		 //info: workaround - select shortcode on input focus doesnt work on iOS
		 if ( version_compare( $wp_version, '3.4', '>=' ) ) {
			 $is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
			 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
		 } else {
			 $shortcode_select = '';
		 }
		 $column_shortcode = ((isset($lmm_options[ 'misc_layer_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="Shortcode"><input ' . $shortcode_select . ' style="width:95%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' layer=&quot;' . $row['id'] . '&quot;]"></td>' : '';
		 $column_kml = ((isset($lmm_options[ 'misc_layer_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('KML', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['id'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
		 $column_fullscreen = ((isset($lmm_options[ 'misc_layer_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Fullscreen', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
		 $column_qr_code = ((isset($lmm_options[ 'misc_layer_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('QR Code', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
		if ($row['multi_layer_map'] == 0) {
			 $column_geojson = ((isset($lmm_options[ 'misc_layer_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('GeoJSON', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/layer/' . $row['id'] . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
		} else if ($row['multi_layer_map'] == 1) {
			$column_geojson = ((isset($lmm_options[ 'misc_layer_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_geojson' ] == 1 )) ? '<td class="lmm-border"  data-colname="'.esc_attr__('GeoJSON', 'lmm').'"></td>' : '';
		}
		 $column_georss = ((isset($lmm_options[ 'misc_layer_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('GeoRSS', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
		 $column_wikitude = ((isset($lmm_options[ 'misc_layer_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize"  data-colname="'.esc_attr__('Wikitude', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/layer/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
		 $column_basemap = ((isset($lmm_options[ 'misc_layer_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border"  data-colname="'.esc_attr__('Basemap', 'lmm').'">' . $row['basemap'] . '</td>' : '';
		 $column_createdby = ((isset($lmm_options[ 'misc_layer_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created By', 'lmm').'">' . $row['createdby'] . '</td>' : '';
		 $column_createdon = ((isset($lmm_options[ 'misc_layer_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created On', 'lmm').'">' . $row['createdon'] . '</td>' : '';
		 $column_updatedby = ((isset($lmm_options[ 'misc_layer_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated By', 'lmm').'">' . $row['updatedby'] . '</td>' : '';
		 $column_updatedon = ((isset($lmm_options[ 'misc_layer_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated On', 'lmm').'">' . $row['updatedon'] . '</td>' : '';
		 $add_new_marker_to_layer = ( $row['multi_layer_map'] == 0 ) ? '&nbsp;|&nbsp;<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $row['id'] . '&lat=' . $row['layerviewlat'] . '&lon=' . $row['layerviewlon'] . '&zoom=' . $row['layerzoom'] . '" style="text-decoration:none;">' . __('add new marker to this layer','lmm') . '</a>' : '';
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$css_table_background = '';
		} else {
			$css_table_background = 'background:#efefef;';
		}
		$layer_type = ($row['multi_layer_map'] == 0)?'layer':'mlm';
		$ajax_results['rows'] .= '<tr valign="middle" class="alternate iedit author-self hentry" id="link-' . $row['id'] . '" style="' . $css_table_background . '">
		<th class="lmm-border check-column " scope="row"><input type="checkbox" value="' . $row['id'] . '" name="checkedlayers[]" data-layertype="'. $layer_type .'"></th>

		<td class="lmm-border before_primary" data-colname="'.esc_attr__('ID', 'lmm').'">'.$row['id'].' </td>
		<td class="lmm-border before_primary" data-colname="'.esc_attr__('Type', 'lmm').'">'.$multi_layer_map_type.'</td>
		<td class="lmm-border column-primary has-row-actions" data-colname="'.esc_attr__('Name', 'lmm').'"><strong>' . $edit_link_layer . $add_new_marker_to_layer . $delete_link_layer . '</span></div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button> </td>
		  ' . $column_address . '
		<td class="lmm-border centeralize" style="text-align:center;" data-colname="'.esc_attr__('# Markers', 'lmm').'"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $row['id'] . '#assigned_markers" title="' . esc_attr__('show markers assigned to this layer','lmm') . '">'.$markercount.'</a></td>
		  ' . $column_layercenter . '
		  ' . $column_mapsize . '
		  ' . $column_panelstatus . '
		  ' . $column_zoom . '
		  ' . $column_basemap . '
		  ' . $column_createdby . '
		  ' . $column_createdon . '
		  ' . $column_updatedby . '
		  ' . $column_updatedon . '
		  ' . $column_controlbox;
		  $ajax_results['rows'] .= ((isset($lmm_options[ 'misc_layer_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_layer_listing_columns_used_in_content' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Used in content', 'lmm').'">' . MMP_Globals::get_map_shortcodes($row['id'], 'layer') . '</td>' : '';
	      $ajax_results['rows'] .= $column_shortcode . '
		  ' . $column_kml . '
		  ' . $column_fullscreen . '
		  ' . $column_qr_code . '
		  ' . $column_geojson . '
		  ' . $column_georss . '
		  ' . $column_wikitude . '
		</tr>';
				}

	//info:  get pagination
	$radius = 1;
	$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_layer_listing_sort_sort_order' ];
	$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
	if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
	if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
	$pager = '';
	if ($lcount > intval($lmm_options[ 'markers_per_page' ])) {
	  $maxpage = intval(ceil($lcount / intval($lmm_options[ 'markers_per_page' ])));
	  if ($maxpage > 1) {
	    $pager .= '' . __('Layers per page','lmm') . ': ';
		if (isset($lmm_options[ "layers_per_page" ])) {
			$layers_per_page = intval($lmm_options[ "layers_per_page" ]);
		} else {
			$layers_per_page = 30; //info: for beta tester updating from 2.8-beta1 to 2.8
		}
		if (current_user_can('activate_plugins')) {
			$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_layers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . $layers_per_page . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		} else {
			$pager .= $layers_per_page . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		}
		$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers">' . __('page','lmm') . ' ';
		$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
		$pager .= '<input type="hidden" name="order" value="' . $columnsortorder . '" />';
	    if ($pagenum > (2 + $radius * 2)) {
	      foreach (range(1, 1 + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum > 1)
	        foreach (range(1, $pagenum - 1) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '<a href="#" class="first-page'. $backend_class .' current-page">' . $pagenum . '</a>';
	    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
	      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($maxpage - $radius, $maxpage) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum < $maxpage)
	        foreach (range($pagenum + 1, $maxpage) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '</form>';
	  }
	}
	$ajax_results['pager'] = $pager;
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}elseif ($ajax_subaction == 'lmm_list_markers_for_edit_page') {
	$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links
	//info: set custom marker icon dir/url
	if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
		$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
	} else {
		$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
	}
	$layer_id = isset($_POST['layer_id']) ? intval($_POST['layer_id']) : (isset($_GET['layer_id']) ? intval($_GET['layer_id']) : '');
	$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
	$offset = ($pagenum - 1) * intval($lmm_options[ 'markers_per_page' ]);
	//info: security check if input variable is valid
	$columnsort_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox','m.address');
	$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_marker_listing_sort_order_by' ]);
	$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_marker_listing_sort_order_by' ];
	//info: security check if input variable is valid
	$columnsortorder_values = array('asc','desc','ASC','DESC');
	$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ]);
	$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$capabilities_view = (isset($lmm_options['capabilities_view_others']))?$lmm_options['capabilities_view_others']:'edit_posts';
	$createdby_query = (!current_user_can($capabilities_view))?" WHERE m.createdby = '". $current_user->user_login ."' ":'';
	$markers_per_page_validated = intval($lmm_options[ 'markers_per_page' ]);
	$mcount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers.$createdby_query));
	$marker_per_page = intval($lmm_options[ 'markers_per_page' ]);
	if($layer_id != ''){
		$backend_class = '-backend';
		$mcount = intval($_POST['totalmarkers']);
		if(isset($_POST['multi_layer_map_list']) && $_POST['multi_layer_map_list']!=''){
			$multi_layer_map_list_exploded = explode(',', $_POST['multi_layer_map_list']);
			$first_mlm_id = $multi_layer_map_list_exploded[0];
			$other_mlm_ids = array_slice($multi_layer_map_list_exploded,1);
			$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $first_mlm_id . "') ";
			foreach ($other_mlm_ids as $row) {
				$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON  m.layer LIKE concat('%\"',l.id,'\"%') WHERE l.id='" . $row . "')";
			}
			$mlm_query .= " ORDER BY markerid " . $lmm_options['defaults_layer_listmarkers_sort_order'] . " LIMIT " . intval($lmm_options['markers_per_page']) . " OFFSET $offset";
			$marklist = $wpdb->get_results($mlm_query, ARRAY_A);
		}else{
			$sql_where = 'WHERE l.id=' . $layer_id;
			$marklist = $wpdb->get_results('SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') ' . $sql_where . ' ORDER BY m.id ASC LIMIT ' . intval($lmm_options[ 'markers_per_page' ])." OFFSET $offset", ARRAY_A);
		}
	}
 	$ajax_results['mcount'] = $mcount;
 	$ajax_results['rows'] = '';
	$ml_checked = MMP_Globals::check_multilingual();
	foreach ($marklist as $row) {
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
		$translate_link = '&nbsp;|&nbsp;<a href="'. $translate_url .'" target="_blank">'.  __('translate', 'lmm')  .'</a>';
		//info: set column display variables - need for for-each
		$column_layer_name = '<td class="lmm-border" data-colname="'.__('Layer name','lmm').'">' . $row['lname'] . '</td>';
		$column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Location','lmm').'">' . stripslashes(htmlspecialchars($row['maddress'])) . '</td>' : '';
		$column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Popup status','lmm').'">' . $row['mopenpopup'] . '</td>' : '';
		$column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Coordinates','lmm').'">Lat: ' . $row['mlat'] . '<br/>Lon: ' . $row['mlon'] . '</td>' : '';
		$column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Map size','lmm').'">' . __('Width','lmm') . ': '.$row['mmapwidth'].$row['mmapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mmapheight'].'px</td>' : '';
		$column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Zoom','lmm').'">' . $row['mzoom'] . '</td>' : '';
		$column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Controlbox status','lmm').'">'.$row['mcontrolbox'].'</td>' : '';
		$column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Shortcode','lmm').'"><input ' . $shortcode_select . ' style="width:90%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['markerid'] . '&quot;]" readonly></td>' : '';
		$column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('KML','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['id'] . '/?markername=' . $lmm_options[ 'markerid' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
		$column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Fullscreen','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
		$column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('QR code','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
		$column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('GeoJSON','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['markerid'] . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
		$column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('GeoRSS','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
		$column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border" data-colname="'.__('Wikitude','lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
		$column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Basemap','lmm').'">' . $row['mbasemap'] . '</td>' : '';
		$column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Created by','lmm').'">' . $row['mcreatedby'] . '</td>' : '';
		$column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Created on','lmm').'">' . $row['mcreatedon'] . '</td>' : '';
		$column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.__('Updated by','lmm').'">' . $row['mupdatedby'] . '</td>' : '';
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
		$ajax_results['rows'] .= '
		<tr valign="middle" class="alternate" id="link-'.$row['markerid'].'" style="' . $css_table_background . '">
			<th class="lmm-border check-column" scope="row"><input value="'.$row['markerid'].'" name="checkedmarkers[]" data-layertype="marker" type="checkbox"></th>
			<td class="lmm-border before_primary" data-colname="'.__('ID','lmm').'">'.$row['markerid'].'</td>
			<td class="lmm-border column-primary">' . $edit_link_marker . $delete_link_marker . $translate_link . '</div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
			$ajax_results['rows'] .= '<td class="lmm-border" data-colname="'.__('Icon','lmm').'">';
			if ($row['micon'] != null) {
				$ajax_results['rows'] .= '<img src="' . $defaults_marker_icon_url . '/'.$row['micon'].'" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="'.$row['micon'].'" />';
			} else {
				$ajax_results['rows'] .= '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';
			};
		$ajax_results['rows'] .= '</td>';
		$ajax_results['rows'] .= '
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
	//info:  get pagination
	$radius = 1;
	$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
	if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
	if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
	if ($mcount > intval($lmm_options[ 'markers_per_page' ])) {
	  $pager = '<div class="tablenav">';
	  $maxpage = intval(ceil($mcount / intval($lmm_options[ 'markers_per_page' ])));
	  if ($maxpage > 1) {
	    $pager .= '' . __('Markers per page','lmm') . ': ';
		if (current_user_can('activate_plugins')) {
			$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_markers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		} else {
			$pager .= intval($lmm_options[ "markers_per_page" ]) . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		}
		$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">' . __('page','lmm') . ' ';
		$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
		$pager .= '<input type="hidden" name="order" value="' . $columnsortorder . '" />';
	    if($layer_id != ''){
			$pager .= '<input type="hidden" name="layer_id" value="' . $layer_id . '" />';
			$pager .= '<input type="hidden" name="multi_layer_map_list" value="' . $_POST['multi_layer_map_list'] . '" />';
			$pager .= '<input type="hidden" name="totalmarkers" value="' . $mcount . '" />';
	    }
	    if ($pagenum > (2 + $radius * 2)) {
	      foreach (range(1, 1 + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum > 1)
	        foreach (range(1, $pagenum - 1) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '<a href="#" class="first-page'. $backend_class .' current-page">' . $pagenum . '</a>';
	    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
	      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($maxpage - $radius, $maxpage) as $num)
	        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum < $maxpage)
	        foreach (range($pagenum + 1, $maxpage) as $num)
	          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page'. $backend_class .'">'.$num.'</a>';
	    $pager .= '</form>';
	  }
	   $pager .= '</div>';
	}
	$ajax_results['pager'] = $pager;
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}
die();
