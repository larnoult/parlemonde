<?php
/*
    Wikitude generator - Leaflet Maps Marker Plugin
*/
//info: construct path to wp-load.php
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
	global $wpdb, $allowedtags;
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$ar_wikitude_provider_name_sanitized = strtolower(preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), wp_kses($lmm_options[ 'ar_wikitude_provider_name' ], $allowedtags)));
	if (isset($_GET['layer'])) {
		$layer_prepared = esc_sql(strtolower($_GET['layer']));
		$layer = str_replace(array("b","c","d","e","f","g","h","i","j","k","m","n","o","p","q","r","s","t","u","v","w","x","y","z","$","%","#","-","_","'","\"","\\"," ","!","/","(",")"), "", $layer_prepared);
		if ($layer == NULL) { die(); }
		
		$maxNumberOfPois = isset($_GET['maxNumberOfPois']) ? intval($_GET['maxNumberOfPois']) : intval($lmm_options[ 'ar_wikitude_maxnumberpois' ]);

		if ($layer == '*' or $layer == 'all') {
			//info: no exact results, but better than getting no results on calling Wikitude ARML links which might confuse users
			$first_marker_lat = $wpdb->get_var('SELECT `lat` FROM `'.$table_name_markers.'`');
			$first_marker_lon = $wpdb->get_var('SELECT `lon` FROM `'.$table_name_markers.'`');
			$latUser = isset($_GET['latitude']) ? floatval($_GET['latitude']) : $first_marker_lat;
			$lonUser = isset($_GET['longitude']) ? floatval($_GET['longitude']) : $first_marker_lon;
		} else {
			$mlm_layers = explode(',', $layer);
			$mlm_checkedlayers = array();
			foreach ($mlm_layers as $mlm_clayer) {
				if (intval($mlm_clayer) > 0) {
					$mlm_checkedlayers[] = intval($mlm_clayer);
				}
			}
			if (count($mlm_checkedlayers) > 0) {
				$mlm_q = 'WHERE `id` IN ('.implode(',', $mlm_checkedlayers).')';
			}
			$layerviewlat = $wpdb->get_var('SELECT `layerviewlat` FROM `'.$table_name_layers.'` '.$mlm_q);
			$layerviewlon = $wpdb->get_var('SELECT `layerviewlon` FROM `'.$table_name_layers.'` '.$mlm_q);
			$latUser = isset($_GET['latitude']) ? floatval($_GET['latitude']) : $layerviewlat;
			$lonUser = isset($_GET['longitude']) ? floatval($_GET['longitude']) : $layerviewlon;
		}
		$radius = intval($lmm_options[ 'ar_wikitude_radius' ]);
		$distanceLLA = 0.01 * $radius / 1112;
		$boundingBoxLatitude1 = $latUser - $distanceLLA;
		$boundingBoxLatitude2 = $latUser + $distanceLLA;
		$boundingBoxLongitude1 = $lonUser - $distanceLLA;
		$boundingBoxLongitude2 = $lonUser + $distanceLLA;

		isset($_GET['searchterm']) ? $searchterm = esc_sql($_GET['searchterm']) : $searchterm = NULL;
		if ($searchterm != NULL) {
			$q = '';
			if ($layer == '*' or $layer == 'all') {
				$q = "WHERE m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . " AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%')";
			} else {
				$sql_mlm_check = 'SELECT `multi_layer_map` FROM `'.$table_name_layers.'` '.$mlm_q;
				$sql_mlm_check_list = 'SELECT `multi_layer_map_list` FROM `'.$table_name_layers.'` '.$mlm_q;
				$mlm_check = $wpdb->get_var($sql_mlm_check);
				$mlm_check_list = $wpdb->get_row($sql_mlm_check_list, ARRAY_A);
				if ($mlm_check == 0) {
					$layers = explode(',', $layer);
					$checkedlayers = array();
					foreach ($layers as $clayer) {
						if (intval($clayer) > 0) {
							$checkedlayers[] = intval($clayer);
						}
					}
					if (count($checkedlayers) > 0) {
						$q = "WHERE m.layer IN (".implode(",", $checkedlayers).") and m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . " AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%')";
					}
				} else if ( ($mlm_check == 1) && (!in_array('all',$mlm_check_list) ) ) {
					$clayer = 0;
					$q = "WHERE m.layer IN (".implode(",", $mlm_check_list).") and m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . " AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%')";
				} else if ( ($mlm_check == 1) && (in_array('all',$mlm_check_list) ) ) {
					$clayer = 0;
					$q = '';
				}
			}
			$sql = 'SELECT m.id as mid, m.layer as mlayer, m.markername as mmarkername, m.icon as micon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext, m.address as maddress FROM `'.$table_name_markers.'` AS m INNER JOIN `'.$table_name_layers.'` AS l ON m.layer=l.id '.$q.' GROUP BY m.id';;
			$markers = $wpdb->get_results($sql, ARRAY_A);
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
			echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0">'.PHP_EOL;
			echo '<Document>'.PHP_EOL;
			echo '<ar:provider id="' . $ar_wikitude_provider_name_sanitized . '">'.PHP_EOL;

			if ($layer == '*' || $layer == 'all') {
				if (get_bloginfo('name') != NULL) {
					$layername = get_bloginfo('name');
				} else {
					$layername = __('layer','lmm') . ' ID ' . $layer;
				}
			} else 	if ((count($checkedlayers) > 1) || ($mlm_check == 1)) {
				$layername = get_bloginfo('name') . ' - ' . __('layer','lmm') . ' ID ' . $layer;
			} else {
				$layername = $wpdb->get_var($wpdb->prepare("SELECT l.name FROM `$table_name_layers` as l WHERE l.id = %d", intval($layer)));
			}

			echo '<ar:name><![CDATA[' . $layername . ']]></ar:name>'.PHP_EOL;
			echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
			echo '<wikitude:providerUrl><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_provider_url' ], $allowedtags) . ']]></wikitude:providerUrl>'.PHP_EOL;
			echo '<wikitude:tags><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_tags' ], $allowedtags) . ']]></wikitude:tags>'.PHP_EOL;
			echo '<wikitude:shortName><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_shortname' ], $allowedtags) . ']]></wikitude:shortName>'.PHP_EOL;
			echo '<wikitude:promotionText><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiontext' ], $allowedtags) . ']]></wikitude:promotionText>'.PHP_EOL;
			echo '<wikitude:promotionGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiongraphic' ], $allowedtags) . ']]></wikitude:promotionGraphic>'.PHP_EOL;
			echo '<wikitude:featureGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_featuregraphic' ], $allowedtags) . ']]></wikitude:featureGraphic>'.PHP_EOL;
			echo '<wikitude:logo><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_logo' ], $allowedtags) . ']]></wikitude:logo>'.PHP_EOL;
			echo '<wikitude:icon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_icon' ], $allowedtags) . ']]></wikitude:icon>'.PHP_EOL;
			echo '<wikitude:hiResIcon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_hiresicon' ], $allowedtags) . ']]></wikitude:hiResIcon>'.PHP_EOL;
			echo '</ar:provider>'.PHP_EOL;

			foreach ($markers as $marker) {
				//info: get icon urls for each marker
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon'];
				}

				echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
				echo '<ar:provider><![CDATA[' . $ar_wikitude_provider_name_sanitized . ']]></ar:provider>'.PHP_EOL;
				echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
				//info: remove control chars & sanitize output
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
				$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, 
								stripslashes(
									str_replace("\\\\","/", 
									str_replace('"', '\'', 
									preg_replace('/[\x00-\x1F\x7F]/', '',
									preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $marker['mpopuptext'])
				)))));
				echo '<description><![CDATA[' . $popuptext_sanitized . ']]></description>'.PHP_EOL;
				echo '<wikitude:info>'.PHP_EOL;
				echo '<wikitude:markerIconUrl><![CDATA[' . $micon_url . ']]></wikitude:markerIconUrl>'.PHP_EOL;
				echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
				echo '<wikitude:phone><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_phone' ], $allowedtags) . ']]></wikitude:phone>'.PHP_EOL;
				//echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
				echo '<wikitude:email><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_email' ], $allowedtags) . ']]></wikitude:email>'.PHP_EOL;
				echo '<wikitude:address><![CDATA[' . $marker['maddress'] . ']]></wikitude:address>'.PHP_EOL;
				echo '<wikitude:attachment><![CDATA[' .wp_kses( $lmm_options[ 'ar_wikitude_attachment' ], $allowedtags) . ']]></wikitude:attachment>'.PHP_EOL;
				echo '</wikitude:info>'.PHP_EOL;
				echo '<Point>'.PHP_EOL;
				echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
				echo '</Point>'.PHP_EOL;
				echo '</Placemark>'.PHP_EOL;
			}
			echo '</Document>';
			echo '</kml>';
		} else  { //info: if no searchterm
			$q = '';
			if ($layer == '*' or $layer == 'all') {
				$q = "WHERE m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . "";//info: removed limit 5000
			} else {
				$sql_mlm_check = 'SELECT `multi_layer_map` FROM `'.$table_name_layers.'` '.$mlm_q;
				$sql_mlm_check_list = 'SELECT `multi_layer_map_list` FROM `'.$table_name_layers.'` '.$mlm_q;
				$mlm_check = $wpdb->get_var($sql_mlm_check);
				$mlm_check_list = $wpdb->get_row($sql_mlm_check_list, ARRAY_A);
				if ($mlm_check == 0) {
					$layers = explode(',', $layer);
					$checkedlayers = array();
					foreach ($layers as $clayer) {
						if (intval($clayer) > 0) {
							$checkedlayers[] = intval($clayer);
						}
					}
					if (count($checkedlayers) > 0) {
						$q = "WHERE layer IN (" . implode(",", $checkedlayers) . ") and m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . "";
					}
				} else if ( ($mlm_check == 1) && (!in_array('all',$mlm_check_list) ) ) {
					$clayer = 0;
					$q = "WHERE layer IN (" . implode(",", $mlm_check_list) . ") and m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . "";
				} else if ( ($mlm_check == 1) && (in_array('all',$mlm_check_list) ) ) {
					$clayer = 0;
					$q = '';
				}
			}
			$sql = 'SELECT m.id as mid, m.layer as mlayer, m.markername as mmarkername, m.icon as micon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext, m.address as maddress FROM `'.$table_name_markers.'` AS m INNER JOIN `'.$table_name_layers.'` AS l ON m.layer=l.id '.$q.' GROUP BY m.id';;
			$markers = $wpdb->get_results($sql, ARRAY_A);
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
			echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0">'.PHP_EOL;
			echo '<Document>'.PHP_EOL;
			echo '<ar:provider id="' . $ar_wikitude_provider_name_sanitized . '">'.PHP_EOL;

			if ($layer == '*' || $layer == 'all') {
				if (get_bloginfo('name') != NULL) {
					$layername = get_bloginfo('name');
				} else {
					$layername = __('layer','lmm') . ' ID ' . $layer;
				}
			} else 	if ((count($checkedlayers) > 1) || ($mlm_check == 1)) {
				$layername = get_bloginfo('name') . ' - ' . __('layer','lmm') . ' ID ' . $layer;
			} else {
				$layername = $wpdb->get_var($wpdb->prepare("SELECT l.name FROM `$table_name_layers` as l WHERE l.id = %d", intval($layer)));
			}

			echo '<ar:name><![CDATA[' . $layername . ']]></ar:name>'.PHP_EOL;
			echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
			echo '<wikitude:providerUrl><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_provider_url' ], $allowedtags) . ']]></wikitude:providerUrl>'.PHP_EOL;
			echo '<wikitude:tags><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_tags' ], $allowedtags) . ']]></wikitude:tags>'.PHP_EOL;
			echo '<wikitude:shortName><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_shortname' ], $allowedtags) . ']]></wikitude:shortName>'.PHP_EOL;
			echo '<wikitude:promotionText><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiontext' ], $allowedtags) . ']]></wikitude:promotionText>'.PHP_EOL;
			echo '<wikitude:promotionGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiongraphic' ], $allowedtags) . ']]></wikitude:promotionGraphic>'.PHP_EOL;
			echo '<wikitude:featureGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_featuregraphic' ], $allowedtags) . ']]></wikitude:featureGraphic>'.PHP_EOL;
			echo '<wikitude:logo><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_logo' ], $allowedtags) . ']]></wikitude:logo>'.PHP_EOL;
			echo '<wikitude:icon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_icon' ], $allowedtags) . ']]></wikitude:icon>'.PHP_EOL;
			echo '<wikitude:hiResIcon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_hiresicon' ], $allowedtags) . ']]></wikitude:hiResIcon>'.PHP_EOL;
			echo '</ar:provider>'.PHP_EOL;

			foreach ($markers as $marker) {
				//info: get icon urls for each marker
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon'];
				}

				echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
				echo '<ar:provider><![CDATA[' . $ar_wikitude_provider_name_sanitized . ']]></ar:provider>'.PHP_EOL;
				echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
				//info: remove control chars & sanitize output
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
				$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, 
								stripslashes(
									str_replace("\\\\","/", 
									str_replace('"', '\'', 
									preg_replace('/[\x00-\x1F\x7F]/', '',
									preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $marker['mpopuptext'])
				)))));
				echo '<description><![CDATA[' . $popuptext_sanitized . ']]></description>'.PHP_EOL;
				echo '<wikitude:info>'.PHP_EOL;
				echo '<wikitude:markerIconUrl><![CDATA[' . $micon_url . ']]></wikitude:markerIconUrl>'.PHP_EOL;
				echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
				echo '<wikitude:phone><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_phone' ], $allowedtags) . ']]></wikitude:phone>'.PHP_EOL;
				//echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
				echo '<wikitude:email><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_email' ], $allowedtags) . ']]></wikitude:email>'.PHP_EOL;
				echo '<wikitude:address><![CDATA[' . $marker['maddress'] . ']]></wikitude:address>'.PHP_EOL;
				echo '<wikitude:attachment><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_attachment' ], $allowedtags) . ']]></wikitude:attachment>'.PHP_EOL;
				echo '</wikitude:info>'.PHP_EOL;
				echo '<Point>'.PHP_EOL;
				echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
				echo '</Point>'.PHP_EOL;
				echo '</Placemark>'.PHP_EOL;
			}
			echo '</Document>';
			echo '</kml>';
		}
	} elseif (isset($_GET['marker'])) {
		$markerid_prepared = esc_sql(strtolower($_GET['marker']));
		$markerid = str_replace(array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","$","%","#","-","_","'","\"","\\"," ","!","/","*","(",")"), "", $markerid_prepared);
		if ($markerid == NULL) { die(); }
		
		$markers = explode(',', $markerid);
		$maxNumberOfPois = isset($_GET['maxNumberOfPois']) ? intval($_GET['maxNumberOfPois']) : intval($lmm_options[ 'ar_wikitude_maxnumberpois' ]);
		$markerlat = $wpdb->get_var('SELECT `lat` FROM `'.$table_name_markers.'` WHERE `id` IN ('.$markerid.')');
		$markerlon = $wpdb->get_var('SELECT `lon` FROM `'.$table_name_markers.'` WHERE `id` IN ('.$markerid.')');

		$latUser = isset($_GET['latitude']) ? floatval($_GET['latitude']) : $markerlat;
		$lonUser = isset($_GET['longitude']) ? floatval($_GET['longitude']) : $markerlon;

		$radius = intval($lmm_options[ 'ar_wikitude_radius' ]);
		$distanceLLA = 0.01 * $radius / 1112;
		$boundingBoxLatitude1 = $latUser - $distanceLLA;
		$boundingBoxLatitude2 = $latUser + $distanceLLA;
		$boundingBoxLongitude1 = $lonUser - $distanceLLA;
		$boundingBoxLongitude2 = $lonUser + $distanceLLA;
		isset($_GET['searchterm']) ? $searchterm = esc_sql($_GET['searchterm']) : $searchterm = NULL;
		if ($searchterm != NULL) {
			$checkedmarkers = array();
			foreach ($markers as $cmarker) {
				if (intval($cmarker) > 0) {
					$checkedmarkers[] = intval($cmarker);
				}
			}
			if (count($checkedmarkers) > 0) {
				$q = "WHERE m.id IN (" . implode(",", $checkedmarkers) . ") AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%')";
			} else {
				die();
			}
			//info: added left outer join to also show markers without a layer
			$sql = 'SELECT m.icon as micon, m.popuptext as mpopuptext, m.id as mid, m.markername as mmarkername, m.lat as mlat, m.lon as mlon, m.address as maddress FROM `'.$table_name_markers.'` AS m LEFT OUTER JOIN `'.$table_name_layers.'` AS l ON m.layer=l.id '.$q.' GROUP BY m.id';;
			$markers = $wpdb->get_results($sql, ARRAY_A);
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
			echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0" xmlns:wikitudeInternal="http://www.openarml.org/wikitudeInternal/1.0">'.PHP_EOL;
			echo '<Document>'.PHP_EOL;
			echo '<ar:provider id="' . $ar_wikitude_provider_name_sanitized . '">'.PHP_EOL;
			foreach ($markers as $marker) {
				$markername = ($marker[ 'mmarkername' ] != NULL) ? $marker[ 'mmarkername' ] : __('marker','lmm') . ' ID ' . $marker['mid'];
				echo '<ar:name><![CDATA[' . $markername . ']]></ar:name>'.PHP_EOL;
			}
			echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
			echo '<wikitude:providerUrl><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_provider_url' ], $allowedtags) . ']]></wikitude:providerUrl>'.PHP_EOL;
			echo '<wikitude:tags><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_tags' ], $allowedtags) . ']]></wikitude:tags>'.PHP_EOL;
			echo '<wikitude:shortName><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_shortname' ], $allowedtags) . ']]></wikitude:shortName>'.PHP_EOL;
			echo '<wikitude:promotionText><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiontext' ], $allowedtags) . ']]></wikitude:promotionText>'.PHP_EOL;
			echo '<wikitude:promotionGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiongraphic' ], $allowedtags) . ']]></wikitude:promotionGraphic>'.PHP_EOL;
			echo '<wikitude:featureGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_featuregraphic' ], $allowedtags) . ']]></wikitude:featureGraphic>'.PHP_EOL;
			echo '<wikitude:logo><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_logo' ], $allowedtags) . ']]></wikitude:logo>'.PHP_EOL;
			echo '<wikitude:icon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_icon' ], $allowedtags) . ']]></wikitude:icon>'.PHP_EOL;
			echo '<wikitude:hiResIcon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_hiresicon' ], $allowedtags) . ']]></wikitude:hiResIcon>'.PHP_EOL;
			echo '</ar:provider>'.PHP_EOL;

			foreach ($markers as $marker) {
				//info: get icon urls for each marker
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon'];
				}

				echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
				echo '<ar:provider><![CDATA[' . $ar_wikitude_provider_name_sanitized . ']]></ar:provider>'.PHP_EOL;
				echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
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
				$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, stripslashes(preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $marker['mpopuptext'])));
				echo '<description><![CDATA[' . $popuptext_sanitized . ']]></description>'.PHP_EOL;
				echo '<wikitude:info>'.PHP_EOL;

				foreach ($markers as $marker) {
					//info: get icon urls for each marker
					if ($marker['micon'] == null) {
						$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';
					} else {
						$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon'];
					}
					echo '<wikitude:markerIconUrl><![CDATA[' . $micon_url . ']]></wikitude:markerIconUrl>'.PHP_EOL;
					echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
				}

				echo '<wikitude:phone><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_phone' ], $allowedtags) . ']]></wikitude:phone>'.PHP_EOL;
				//echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
				echo '<wikitude:email><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_email' ], $allowedtags) . ']]></wikitude:email>'.PHP_EOL;
				echo '<wikitude:address><![CDATA[' . $marker['maddress'] . ']]></wikitude:address>'.PHP_EOL;
				echo '<wikitude:attachment><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_attachment' ], $allowedtags) . ']]></wikitude:attachment>'.PHP_EOL;
				echo '</wikitude:info>'.PHP_EOL;
				echo '<Point>'.PHP_EOL;
				echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
				echo '</Point>'.PHP_EOL;
				echo '</Placemark>'.PHP_EOL;
			}
			echo '</Document>';
			echo '</kml>';
		} else { //info: if no searchterm
			$checkedmarkers = array();
			foreach ($markers as $cmarker) {
				if (intval($cmarker) > 0) {
					$checkedmarkers[] = intval($cmarker);
				}
			}
			if (count($checkedmarkers) > 0) {
				$q = "WHERE m.id IN (" . implode(",", $checkedmarkers) . ")";
			} else {
				die();
			}
			//info: added left outer join to also show markers without a layer
			$sql = 'SELECT m.icon as micon, m.popuptext as mpopuptext, m.id as mid, m.markername as mmarkername, m.lat as mlat, m.lon as mlon, m.address as maddress FROM `'.$table_name_markers.'` AS m LEFT OUTER JOIN `'.$table_name_layers.'` AS l ON m.layer=l.id '.$q.' GROUP BY m.id';;
			$markers = $wpdb->get_results($sql, ARRAY_A);
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
			echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0" xmlns:wikitudeInternal="http://www.openarml.org/wikitudeInternal/1.0">'.PHP_EOL;
			echo '<Document>'.PHP_EOL;
			echo '<ar:provider id="' . $ar_wikitude_provider_name_sanitized . '">'.PHP_EOL;
			foreach ($markers as $marker) {
				$markername = ($marker[ 'mmarkername' ] != NULL) ? $marker[ 'mmarkername' ] : __('marker','lmm') . ' ID ' . $marker['mid'];
				echo '<ar:name><![CDATA[' . $markername . ']]></ar:name>'.PHP_EOL;
			}
			echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
			echo '<wikitude:providerUrl><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_provider_url' ], $allowedtags) . ']]></wikitude:providerUrl>'.PHP_EOL;
			echo '<wikitude:tags><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_tags' ], $allowedtags) . ']]></wikitude:tags>'.PHP_EOL;
			echo '<wikitude:shortName><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_shortname' ], $allowedtags) . ']]></wikitude:shortName>'.PHP_EOL;
			echo '<wikitude:promotionText><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiontext' ], $allowedtags) . ']]></wikitude:promotionText>'.PHP_EOL;
			echo '<wikitude:promotionGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_promotiongraphic' ], $allowedtags) . ']]></wikitude:promotionGraphic>'.PHP_EOL;
			echo '<wikitude:featureGraphic><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_featuregraphic' ], $allowedtags) . ']]></wikitude:featureGraphic>'.PHP_EOL;
			echo '<wikitude:logo><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_logo' ], $allowedtags) . ']]></wikitude:logo>'.PHP_EOL;
			echo '<wikitude:icon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_icon' ], $allowedtags) . ']]></wikitude:icon>'.PHP_EOL;
			echo '<wikitude:hiResIcon><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_hiresicon' ], $allowedtags) . ']]></wikitude:hiResIcon>'.PHP_EOL;
			echo '</ar:provider>'.PHP_EOL;

			foreach ($markers as $marker) {
				//info: get icon urls for each marker
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon'];
				}

				echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
				echo '<ar:provider><![CDATA[' . $ar_wikitude_provider_name_sanitized . ']]></ar:provider>'.PHP_EOL;
				echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
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
				$popuptext_sanitized = preg_replace($sanitize_popuptext_from, $sanitize_popuptext_to, stripslashes(preg_replace( '/(\015\012)|(\015)|(\012)/','<br />', $marker['mpopuptext'])));
				echo '<description><![CDATA[' . $popuptext_sanitized . ']]></description>'.PHP_EOL;
				echo '<wikitude:info>'.PHP_EOL;

				foreach ($markers as $marker) {
					//info: get icon urls for each marker
					if ($marker['micon'] == null) {
						$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';
					} else {
						$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon'];
					}
					echo '<wikitude:markerIconUrl><![CDATA[' . $micon_url . ']]></wikitude:markerIconUrl>'.PHP_EOL;
					echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
				}

				echo '<wikitude:phone><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_phone' ], $allowedtags) . ']]></wikitude:phone>'.PHP_EOL;
				//echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
				echo '<wikitude:email><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_email' ], $allowedtags) . ']]></wikitude:email>'.PHP_EOL;
				echo '<wikitude:address><![CDATA[' . $marker['maddress'] . ']]></wikitude:address>'.PHP_EOL;
				echo '<wikitude:attachment><![CDATA[' . wp_kses($lmm_options[ 'ar_wikitude_attachment' ], $allowedtags) . ']]></wikitude:attachment>'.PHP_EOL;
				echo '</wikitude:info>'.PHP_EOL;
				echo '<Point>'.PHP_EOL;
				echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
				echo '</Point>'.PHP_EOL;
				echo '</Placemark>'.PHP_EOL;
			}
			echo '</Document>';
			echo '</kml>';
		}
	}
} //info: end plugin active check
?>