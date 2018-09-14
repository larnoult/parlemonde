<?php
/*
    GeoRSS generator - Maps Marker Pro
*/
//info redirect to permalink if file is being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-georss.php') {
	while (!is_file('wp-load.php')) {
		if (is_dir('..' . DIRECTORY_SEPARATOR)) {
			chdir('..' . DIRECTORY_SEPARATOR);
		} else {
			die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
		}
	}
	require_once('wp-load.php');
	if (isset($_GET['layer'])) {
		$layer = $_GET['layer'];
		unset($_GET['layer']);
		$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
		wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/georss/layer/' . $layer . '/' . $argv), 301);
	} elseif (isset($_GET['marker'])) {
		$marker = $_GET['marker'];
		unset($_GET['marker']);
		$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
		wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/georss/marker/' . $marker . '/' . $argv), 301);
	}
	exit;
}

$format = (isset($_GET['format'])) ? $_GET['format'] : '';
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
	global $wpdb, $allowedposttags;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$lmm_base_url = MMP_Rewrite::get_base_url();
	$lmm_slug = MMP_Rewrite::get_slug();

	if (get_query_var('layer', false)) {
		$layer_prepared = esc_sql(strtolower(get_query_var('layer')));
		$layer = str_replace(array("b","c","d","e","f","g","h","i","j","k","m","n","o","p","q","r","s","t","u","v","w","x","y","z","$","%","#","-","_","'","\"","\\","(",")"), "", $layer_prepared);

		$q = '';
		if (($layer_prepared == 'all') || ($layer_prepared == '*')) {
			$q = '';
		} else {
			$mlm_layers = explode(',', $layer);
			$mlm_checkedlayers = array();
			foreach ($mlm_layers as $mlm_clayer) {
				if (intval($mlm_clayer) > 0) {
					$mlm_checkedlayers[] = intval($mlm_clayer);
				}
			}
			if (count($mlm_checkedlayers) > 0) {
				$mlm_q = 'WHERE id IN ('.implode(',', $mlm_checkedlayers).')';

				$sql_mlm_check = 'SELECT `multi_layer_map` FROM `'.$table_name_layers.'` '.$mlm_q;
				$sql_mlm_check_list = 'SELECT `multi_layer_map_list` FROM `'.$table_name_layers.'` '.$mlm_q;
				$mlm_check = $wpdb->get_var($sql_mlm_check);
				$mlm_check_list = $wpdb->get_row($sql_mlm_check_list, ARRAY_A);

				if ($mlm_check == 0) {
					$layers = explode(',', $layer);
					$checkedlayers = array();
					foreach ($layers as $clayer) {
						if (intval($clayer) > 0)
							$checkedlayers[] = intval($clayer);
					}
					if (count($checkedlayers) > 0) {
						$q .= ' WHERE ';

						foreach( $checkedlayers as $checked_layer){
							if(end($checkedlayers) == $checked_layer){
								$q .= " layer LIKE '%\"".$checked_layer."\"%' ";
							}else{
								$q .= " layer LIKE '%\"".$checked_layer."\"%' OR ";
							}
						}
					}
				} else if ( ($mlm_check == 1) && (!in_array('all',$mlm_check_list) ) ){
					$q .= ' WHERE ';
						$mlm_check_list_for_query = explode(',', $mlm_check_list['multi_layer_map_list']);
						foreach( $mlm_check_list_for_query as $mlm_checked_layer){
							if(end($mlm_check_list_for_query) == $mlm_checked_layer){
								$q .= " layer LIKE '%\"".$mlm_checked_layer."\"%' ";
							}else{
								$q .= " layer LIKE '%\"".$mlm_checked_layer."\"%' OR ";
							}
					}

				} else if ( ($mlm_check == 1) && (in_array('all',$mlm_check_list) ) ){
					  $q = '';
				}
			} else {
				die('Error: a layer with that ID does not exist!');
			}
		}
		$sql = 'SELECT m.id as mid, m.markername as mmarkername, m.layer as mlayer, m.icon as micon, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext, m.address as maddress, l.id as lid, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon, l.name AS lname FROM `'.$table_name_markers.'` AS m INNER JOIN `'.$table_name_layers.'` AS l ON m.layer LIKE concat(\'%"\',l.id,\'"%\') '.$q.' GROUP BY m.id';
		$markers = $wpdb->get_results($sql, ARRAY_A);
		//info: prepare WPML supported strings
		if ($ml_checked = MMP_Globals::check_multilingual()) {
			foreach ($markers as $key => $marker) {
				$markers[$key]['mmarkername'] = MMP_Globals::translate_single_string($marker['mmarkername'], "Marker (ID {$marker['mid']}) name", $ml_checked);
				$markers[$key]['mpopuptext'] = MMP_Globals::translate_single_string($marker['mpopuptext'], "Marker (ID {$marker['mid']}) popuptext", $ml_checked);
			}
		}
		//info: output as atom - part 1
		if ($format == 'atom') {
			$offset_kml = date('H:i',get_option('gmt_offset')*3600);
			if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
			/*info: not used yet, as do not know which are right srsnames
			if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) { $srsname = 'EPSG3395'; }
			*/
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/atom+xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
			echo '<feed xmlns:atom="http://www.w3.org/2005/Atom" xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml">'.PHP_EOL;
			echo '<atom:link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $layer_prepared . '/') . '%26format%3Datom" rel="self" type="application/rss+xml" />'.PHP_EOL;
			if (($layer_prepared == 'all') || ($layer_prepared == '*')) {
				$layercreatedon = $wpdb->get_var("SELECT max(`createdon`) FROM `$table_name_layers`");
				$date_kml =  strtotime($layercreatedon);
				$time_kml =  strtotime($layercreatedon);
				echo '<title>' . get_bloginfo('name') . ' - ' . __('maps','lmm') . '</title>'.PHP_EOL;
				echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
				echo '<link href="' . home_url() . '"/>'.PHP_EOL;
				echo '<id>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/all/') . '</id>'.PHP_EOL;
			} else {
				$layername = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `$table_name_layers` WHERE `id` = %d", intval(get_query_var('layer'))));
				$layername = MMP_Globals::translate_single_string($layername, 'Layer (ID ' . intval(get_query_var('layer')) . ') name');
				$layercreatedby = $wpdb->get_var($wpdb->prepare("SELECT `createdby` FROM `$table_name_layers` WHERE `id` = %d", intval(get_query_var('layer'))));
				$layercreatedon = $wpdb->get_var($wpdb->prepare("SELECT `createdon` FROM `$table_name_layers` WHERE `id` = %d", intval(get_query_var('layer'))));
				$date_kml =  strtotime($layercreatedon);
				$time_kml =  strtotime($layercreatedon);
				echo '<title>' . get_bloginfo('name') . ' - ' . htmlspecialchars($layername) . '</title>'.PHP_EOL;
				echo '<author>'.PHP_EOL;
				echo '<name>' . stripslashes($layercreatedby) . '</name>'.PHP_EOL;
				echo '</author>'.PHP_EOL;
				echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
				echo '<link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . intval(get_query_var('layer')) . '/') . '"/>'.PHP_EOL;
				echo '<id>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . intval(get_query_var('layer')) . '/') . '</id>'.PHP_EOL;
			}
			echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
			echo '<subtitle>GeoRSS-feed created with Maps Marker Pro (www.mapsmarker.com)</subtitle>'.PHP_EOL;

			foreach ($markers as $marker) {
				echo '<entry>'.PHP_EOL;
				echo '<title>' . htmlspecialchars(stripslashes($marker['mmarkername'])) . '</title>'.PHP_EOL;
				echo '<link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $marker['mid'] . '/') . '"/>'.PHP_EOL;
				echo '<id>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $marker['mid'] . '/') . '</id>'.PHP_EOL;
				echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
				echo '<author><name>' . stripslashes($marker['mcreatedby']) . '</name></author>'.PHP_EOL;
				echo '<content><![CDATA[' . stripslashes(wp_kses($marker['mpopuptext'], $allowedposttags)) . ']]></content>'.PHP_EOL;
				echo '<georss:where>'.PHP_EOL;
				//info: add if srsnames are verified - <gml:Point srsName="' . $srsname . '">
				echo '<gml:Point>'.PHP_EOL;
				echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
				echo '</gml:Point>'.PHP_EOL;
				echo '</georss:where>'.PHP_EOL;
				echo '</entry>'.PHP_EOL;
			}
			echo '</feed>';
			//info: end output as atom
		} else if ($format != 'atom') { //info: output as RSS 2.0
			$offset_kml = date('Hi',get_option('gmt_offset')*3600);
			if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
			/*info: not used yet, as do not know which are right srsnames
			if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) { $srsname = 'EPSG3395'; }
			*/
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/rss+xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
			echo '<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;
			echo '<channel>'.PHP_EOL;
			echo '<atom:link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/layer/' . $layer_prepared . '/') . '" rel="self" type="application/rss+xml" />'.PHP_EOL;
			echo '<link>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . intval(get_query_var('layer')) . '/') . '</link>'.PHP_EOL;
			if (($layer_prepared == 'all') || ($layer_prepared == '*')) {
				$newest_marker_createdon = strtotime($wpdb->get_var('SELECT max(createdon) FROM '.$table_name_markers.''));
				echo '<title>' . get_bloginfo('name') . ' - ' . __('maps','lmm') . '</title>'.PHP_EOL;
				echo '<lastBuildDate>' . date("D, d M Y", $newest_marker_createdon) . ' ' . date("h:m:s", $newest_marker_createdon) . ' ' . $plus_minus . $offset_kml . '</lastBuildDate>'.PHP_EOL;
			} else {
				$layername = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `$table_name_layers` WHERE `id` = %d", intval(get_query_var('layer'))));
				$layername = MMP_Globals::translate_single_string($layername, 'Layer (ID ' . intval(get_query_var('layer')) . ') name');
				$layercreatedby = $wpdb->get_var($wpdb->prepare("SELECT `createdby` FROM `$table_name_layers` WHERE `id` = %d", intval(get_query_var('layer'))));
				$layercreatedon = $wpdb->get_var($wpdb->prepare("SELECT `createdon` FROM `$table_name_layers` WHERE `id` = %d", intval(get_query_var('layer'))));
				$date_kml_layer =  strtotime($layercreatedon);
				$time_kml_layer =  strtotime($layercreatedon);
				echo '<title>' . get_bloginfo('name') . ' - ' . htmlspecialchars($layername) . '</title>'.PHP_EOL;
				echo '<lastBuildDate>' . date("D, d M Y", $date_kml_layer) . ' ' . date("h:m:s", $time_kml_layer) . ' ' . $plus_minus . $offset_kml . '</lastBuildDate>'.PHP_EOL;
			}
			echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
			echo '<description>GeoRSS-feed created with Maps Marker Pro (www.mapsmarker.com)</description>'.PHP_EOL;

			foreach ($markers as $marker) {
				$date_kml_marker =  strtotime($marker['mcreatedon']);
				$time_kml_marker =  strtotime($marker['mcreatedon']);
				echo '<item>'.PHP_EOL;
				echo '<title>' . htmlspecialchars(stripslashes($marker['mmarkername'])) . '</title>'.PHP_EOL;
				echo '<link>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $marker['mid'] . '/') . '</link>'.PHP_EOL;
				echo '<guid isPermaLink="false">' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-layer-' . $marker['lid'] . '-marker-' . $marker['mid'] . '</guid>'.PHP_EOL;
				echo '<pubDate>' . date("D, d M Y", $date_kml_marker) . ' ' . date("h:m:s", $time_kml_marker) . ' ' . $plus_minus . $offset_kml . '</pubDate>'.PHP_EOL;
				echo '<dc:creator>' . $marker['mcreatedby'] . '</dc:creator>'.PHP_EOL;
				$popuptext_sanitized = MMP_Globals::sanitize_popuptext($marker['mpopuptext']);
				echo '<description><![CDATA[' . $popuptext_sanitized . ']]></description>'.PHP_EOL;
				echo '<source url="' . home_url() . '">' . home_url() . '</source>'.PHP_EOL;
				echo '<georss:where>'.PHP_EOL;
				echo '<gml:Point>'.PHP_EOL;
				echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
				echo '</gml:Point>'.PHP_EOL;
				echo '</georss:where>'.PHP_EOL;
				echo '</item>'.PHP_EOL;
			}
			echo '</channel>'.PHP_EOL;
			echo '</rss>';
		} //info: end output as RSS 2.0
	} //info: end get_query_var('layer')
	elseif (get_query_var('marker', false)) {
		$markerid_prepared = esc_sql(strtolower(get_query_var('marker')));
		$markerid = str_replace(array("b","c","d","e","f","g","h","i","j","k","m","n","o","p","q","r","s","t","u","v","w","x","y","z","$","%","#","-","_","'","\"","\\","(",")"), "", $markerid_prepared);

		if (($markerid_prepared == 'all') || ($markerid_prepared == '*')) {
			$q = '';
		} else {
			$markers = explode(',', $markerid);
			$checkedmarkers = array();
			foreach ($markers as $cmarker) {
				if (intval($cmarker) > 0) {
					$checkedmarkers[] = intval($cmarker);
				}
			}
			if (count($checkedmarkers) > 0) {
				$q = 'WHERE m.id IN ('.implode(',', $checkedmarkers).')';
			} else {
				die('Error: a marker with that ID does not exist!');
			}
		}
		//info: added left outer join to also show markers without a layer
		$sql = 'SELECT m.layer as mlayer,m.icon as micon,m.popuptext as mpopuptext,m.id as mid,m.markername as mmarkername,m.createdby as mcreatedby, m.createdon as mcreatedon, m.lat as mlat, m.lon as mlon, m.address as maddress FROM `'.$table_name_markers.'` AS m LEFT OUTER JOIN `'.$table_name_layers.'` AS l ON m.layer LIKE concat(\'%"\',l.id,\'"%\') '.$q.' GROUP BY m.id';
		$markers = $wpdb->get_results($sql, ARRAY_A);
		//info: prepare WPML supported strings
		if ($ml_checked = MMP_Globals::check_multilingual()) {
			foreach ($markers as $key => $marker) {
				$markers[$key]['mmarkername'] = MMP_Globals::translate_single_string($marker['mmarkername'], "Marker (ID {$marker['mid']}) name", $ml_checked);
				$markers[$key]['mpopuptext'] = MMP_Globals::translate_single_string($marker['mpopuptext'], "Marker (ID {$marker['mid']}) popuptext", $ml_checked);
			}
		}
		//info: output as atom - part 1
		if ($format == 'atom') {
			$offset_kml = date('H:i',get_option('gmt_offset')*3600);
			if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
			/*info: not used yet, as do not know which are right srsnames
			if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) {	$srsname = 'EPSG3395';
			}*/
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/atom+xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
			echo '<feed xmlns:atom="http://www.w3.org/2005/Atom" xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml">'.PHP_EOL;
			echo '<atom:link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $markerid_prepared . '/') . '%26format%3Datom" rel="self" type="application/rss+xml" />'.PHP_EOL;

			foreach ($markers as $marker) {
				$date_kml =  strtotime($marker['mcreatedon']);
				$time_kml =  strtotime($marker['mcreatedon']);
				echo '<title>' . get_bloginfo('name') . ' - ' . htmlspecialchars(stripslashes($marker['mmarkername'])) . '</title>'.PHP_EOL;
				echo '<author>'.PHP_EOL;
				echo '<name>' . stripslashes($marker['mcreatedby']) . '</name>'.PHP_EOL;
				echo '</author>'.PHP_EOL;
				echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
			}
			echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
			echo '<subtitle>GeoRSS-feed created with Maps Marker Pro (www.mapsmarker.com)</subtitle>'.PHP_EOL;
			if (($markerid_prepared != 'all') || ($markerid_prepared != '*')) {
				echo '<link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . intval(get_query_var('marker')) . '/') . '"/>'.PHP_EOL;
			}
			foreach ($markers as $marker) {
				echo '<id>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . intval(get_query_var('marker')) . '/') . '</id>'.PHP_EOL;
				echo '<entry>'.PHP_EOL;
				echo '<title>' . htmlspecialchars(stripslashes($marker['mmarkername'])) . '</title>'.PHP_EOL;
				if (($markerid_prepared != 'all') || ($markerid_prepared != '*')) {
					echo '<link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . intval(get_query_var('marker')) . '/') . '"/>'.PHP_EOL;
					echo '<id>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . intval(get_query_var('marker')) . '/') . '</id>'.PHP_EOL;
				}
				echo '<author>'.PHP_EOL;
				echo '<name>' . stripslashes($marker['mcreatedby']) . '</name>'.PHP_EOL;
				echo '</author>'.PHP_EOL;
				echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
				echo '<content><![CDATA[' . stripslashes(wp_kses($marker['mpopuptext'], $allowedposttags)) . ']]></content>'.PHP_EOL;
				echo '<georss:where>'.PHP_EOL;
				//info: add if srsnames are verified - <gml:Point srsName="' . $srsname . '">
				echo '<gml:Point>'.PHP_EOL;
				echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
				echo '</gml:Point>'.PHP_EOL;
				echo '</georss:where>'.PHP_EOL;
				echo '</entry>'.PHP_EOL;
			}
			echo '</feed>';
			//info: end output as atom
		} else if ($format != 'atom') { //info: output as RSS 2.0
			$offset_kml = date('Hi',get_option('gmt_offset')*3600);
			if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
			/*info: not used yet, as do not know which are right srsnames
			if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; }
			else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) {	$srsname = 'EPSG3395';
			}*/
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/rss+xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
			echo '<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;
			echo '<channel>'.PHP_EOL;
			echo '<atom:link href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $markerid_prepared . '/') . '" rel="self" type="application/rss+xml" />'.PHP_EOL;
			if (($markerid_prepared != 'all') || ($markerid_prepared != '*')) {
				echo '<link>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . intval(get_query_var('marker')) . '/') . '</link>'.PHP_EOL;
				echo '<title>' . get_bloginfo('name') . ' - ' . __('marker','lmm') . ' ' . intval(get_query_var('marker')) . '</title>'.PHP_EOL;
			} else {
				echo '<title>' . get_bloginfo('name') . '</title>'.PHP_EOL;
			}
			echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
			echo '<description>GeoRSS-feed created with Maps Marker Pro (www.mapsmarker.com)</description>'.PHP_EOL;

			foreach ($markers as $marker) {
				$date_kml_marker =  strtotime($marker['mcreatedon']);
				$time_kml_marker =  strtotime($marker['mcreatedon']);
				echo '<item>'.PHP_EOL;
				echo '<title>' . htmlspecialchars(stripslashes($marker['mmarkername'])) . '</title>'.PHP_EOL;
				echo '<link>' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $marker['mid'] . '/') . '</link>'.PHP_EOL;
				echo '<guid isPermaLink="false">' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-marker-' . $marker['mid'] . '</guid>'.PHP_EOL;
				echo '<pubDate>' . date("D, d M Y", $date_kml_marker) . ' ' . date("h:m:s", $time_kml_marker) . ' ' . $plus_minus . $offset_kml . '</pubDate>'.PHP_EOL;
				echo '<dc:creator>' . stripslashes($marker['mcreatedby']) . '</dc:creator>'.PHP_EOL;
				$popuptext_sanitized = MMP_Globals::sanitize_popuptext($marker['mpopuptext']);
				echo '<description><![CDATA[' . $popuptext_sanitized . ']]></description>'.PHP_EOL;
				echo '<source url="' . home_url() . '">' . home_url() . '</source>'.PHP_EOL;
				echo '<georss:where>'.PHP_EOL;
				echo '<gml:Point>'.PHP_EOL;
				echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
				echo '</gml:Point>'.PHP_EOL;
				echo '</georss:where>'.PHP_EOL;
				echo '</item>'.PHP_EOL;
			}
			echo '</channel>'.PHP_EOL;
			echo '</rss>';
		} //info: end output as RSS 2.0
	} //info: end get_query_var('marker', false)
} //info: end plugin active check
