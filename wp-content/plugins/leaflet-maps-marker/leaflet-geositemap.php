<?php
/*
    Geo Sitemap generator - Leaflet Maps Marker Plugin
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
	global $wpdb;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$lmm_options = get_option( 'leafletmapsmarker_options' );

	$sql = 'SELECT m.id as mid, m.createdon as mcreatedon, m.updatedon as mupdatedon FROM `'.$table_name_markers.'` AS m';
	$markers = $wpdb->get_results($sql, ARRAY_A);

	$sql2 = 'SELECT l.id as lid, l.createdon as lcreatedon, l.updatedon as lupdatedon FROM `'.$table_name_layers.'` AS l WHERE l.id != 0';
	$layers = $wpdb->get_results($sql2, ARRAY_A);

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-Type:text/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
	echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

	foreach ($markers as $marker) {
		if  ( ($marker['mupdatedon'] == NULL) || ($marker['mupdatedon'] == '0000-00-00 00:00:00') ){
			$date_kml =  strtotime($marker['mcreatedon']);
		} else {
			$date_kml =  strtotime($marker['mupdatedon']);
		}
		echo '<url>'.PHP_EOL;
		echo '<loc>'. LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $marker['mid'] . '</loc>'.PHP_EOL;
		echo '<lastmod>' . date("Y-m-d", $date_kml) . '</lastmod>'.PHP_EOL;
		echo '</url>'.PHP_EOL;
	}

	foreach ($layers as $layer) {
		if  ( ($layer['lupdatedon'] == NULL) || ($layer['lupdatedon'] == '0000-00-00 00:00:00') ){
			$date_kml =  strtotime($layer['lcreatedon']);
		} else {
			$date_kml =  strtotime($layer['lupdatedon']);
		}
		echo '<url>'.PHP_EOL;
		echo '<loc>'. LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $layer['lid'] . '</loc>'.PHP_EOL;
		echo '<lastmod>' . date("Y-m-d", $date_kml) . '</lastmod>'.PHP_EOL;
		echo '</url>'.PHP_EOL;
	}
	echo '</urlset>';
} //info: end plugin active check
?>