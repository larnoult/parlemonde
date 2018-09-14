<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'install-and-updates.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
global $wpdb;
//info: options not managed by Settings API
add_option('leafletmapsmarker_version', 'init');
add_option('leafletmapsmarker_version_before_update', '0'); //info: if = 0 -> fresh pro install or upgrade from free initial install
add_option('leafletmapsmarker_redirect', 'true'); //redirect to marker creation page page after first activation only

//info: pro options not managed by Settings API
add_option('leafletmapsmarker_version_pro', 'init');
add_option('leafletmapsmarker_version_pro_before_update', '0');

//info: check and update db-structure for markers and layers table - not done in 'init' as needed for switches between free and pro edition
require_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php');
//info: create/update marker table
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$sql_markers_table = "CREATE TABLE " . $table_name_markers . " (
	id int(6) unsigned NOT NULL AUTO_INCREMENT,
	markername varchar(255) NOT NULL,
	basemap varchar(25) NOT NULL,
	layer varchar(4000) NOT NULL,
	lat decimal(9,6) NOT NULL,
	lon decimal(9,6) NOT NULL,
	icon varchar(255) NOT NULL,
	popuptext text NOT NULL,
	zoom int(2) NOT NULL,
	openpopup tinyint(1) NOT NULL,
	mapwidth int(4) NOT NULL,
	mapwidthunit varchar(2) NOT NULL,
	mapheight int(4) NOT NULL,
	panel tinyint(1) NOT NULL,
	createdby varchar(60) NOT NULL,
	createdon datetime NOT NULL,
	updatedby varchar(60) DEFAULT NULL,
	updatedon datetime DEFAULT NULL,
	controlbox int(1) NOT NULL,
	overlays_custom int(1) NOT NULL,
	overlays_custom2 int(1) NOT NULL,
	overlays_custom3 int(1) NOT NULL,
	overlays_custom4 int(1) NOT NULL,
	wms tinyint(1) NOT NULL,
	wms2 tinyint(1) NOT NULL,
	wms3 tinyint(1) NOT NULL,
	wms4 tinyint(1) NOT NULL,
	wms5 tinyint(1) NOT NULL,
	wms6 tinyint(1) NOT NULL,
	wms7 tinyint(1) NOT NULL,
	wms8 tinyint(1) NOT NULL,
	wms9 tinyint(1) NOT NULL,
	wms10 tinyint(1) NOT NULL,
	kml_timestamp datetime DEFAULT NULL,
	address varchar(255) NOT NULL,
	gpx_url varchar(2083) NOT NULL,
	gpx_panel tinyint(1) NOT NULL,
	PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
dbDelta($sql_markers_table);

//info: create/update layer table
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$sql_layers_table = "CREATE TABLE " . $table_name_layers . " (
	id int(6) unsigned NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	basemap varchar(25) NOT NULL,
	layerzoom int(2) NOT NULL,
	mapwidth int(4) NOT NULL,
	mapwidthunit varchar(2) NOT NULL,
	mapheight int(4) NOT NULL,
	panel tinyint(1) NOT NULL,
	layerviewlat decimal(9,6) NOT NULL,
	layerviewlon decimal(9,6) NOT NULL,
	createdby varchar(60) NOT NULL,
	createdon datetime NOT NULL,
	updatedby varchar(60) DEFAULT NULL,
	updatedon datetime DEFAULT NULL,
	controlbox int(1) NOT NULL,
	overlays_custom int(1) NOT NULL,
	overlays_custom2 int(1) NOT NULL,
	overlays_custom3 int(1) NOT NULL,
	overlays_custom4 int(1) NOT NULL,
	wms tinyint(1) NOT NULL,
	wms2 tinyint(1) NOT NULL,
	wms3 tinyint(1) NOT NULL,
	wms4 tinyint(1) NOT NULL,
	wms5 tinyint(1) NOT NULL,
	wms6 tinyint(1) NOT NULL,
	wms7 tinyint(1) NOT NULL,
	wms8 tinyint(1) NOT NULL,
	wms9 tinyint(1) NOT NULL,
	wms10 tinyint(1) NOT NULL,
	listmarkers tinyint(1) NOT NULL,
	multi_layer_map tinyint(1) NOT NULL,
	multi_layer_map_list varchar(4000) DEFAULT NULL,
	address varchar(255) NOT NULL,
	`clustering` tinyint(1) unsigned NOT NULL,
	gpx_url varchar(2083) NOT NULL,
	gpx_panel tinyint(1) NOT NULL,
	mlm_filter int(1) NOT NULL,
	mlm_filter_details varchar(65535) DEFAULT NULL,
	PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
dbDelta($sql_layers_table);

//info: begin update routine based on plugin version
if (get_option('leafletmapsmarker_version') == 'init') {
	//info: copy map icons to wp-content/uploads
	WP_Filesystem();
	$target = LEAFLET_PLUGIN_ICONS_DIR;
	if (!is_dir($target)) //info: check for multisite installations not to extract files again if already installed on 1 site
	{
		wp_mkdir_p( $target );
		$source = LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'mapicons';
		copy_dir($source, $target, $skip_list = array() );
		$zipfile = LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'mapicons.zip';
		unzip_file( $zipfile, $target );
		//info: fallback for hosts where copying zipfile to LEAFLET_PLUGIN_ICON_DIR doesnt work
		if ( !file_exists(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'information.png') ) {
			if (class_exists('ZipArchive')) {
				$zip = new ZipArchive;
				$res = $zip->open( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'mapicons' . DIRECTORY_SEPARATOR . 'mapicons.zip');
			    if ($res === TRUE) {
					$zip->extractTo(LEAFLET_PLUGIN_ICONS_DIR);
					$zip->close();
				}
			}
		}
	}
	//info: insert layer row 0 for markers without assigned layer
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$sql = "SET SESSION sql_mode=NO_AUTO_VALUE_ON_ZERO;";
	$wpdb->query($sql);
	$sql2 = "INSERT INTO `".$table_name_layers."` ( `id`, `name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `layerviewlat`, `layerviewlon` ) VALUES (0, 'markers not assigned to a layer', 'osm_mapnik', '11', '640', 'px', '480', '', '');";
	$wpdb->query($sql2);
	$sql3 = "SET SESSION sql_mode='';";
	$wpdb->query($sql3);
	update_option('leafletmapsmarker_version_before_update', '0'); //info: dynamic changelog for free version removed
	update_option('leafletmapsmarker_version', '1.0');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.0','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.2','=')) {
	update_option('leafletmapsmarker_version', '1.2.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.2.1','=')) {
	update_option('leafletmapsmarker_version', '1.2.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.2.2','=')) {
	update_option('leafletmapsmarker_version', '1.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.4','=')) {
	update_option('leafletmapsmarker_version', '1.4.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.4.1','=')) {
	update_option('leafletmapsmarker_version', '1.4.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.4.2','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.4.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.4.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.5','=')) {
	update_option('leafletmapsmarker_version', '1.5.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.5.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.6','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.7');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.7','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.8');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.8','=')) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update19_1 = "UPDATE `" . $table_name_markers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'osm_osmarender';";
	$wpdb->query($update19_1);
	$update19_2 = "UPDATE `" . $table_name_layers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'osm_osmarender';";
	$wpdb->query($update19_2);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '1.9');
}
if (version_compare(get_option('leafletmapsmarker_version'),'1.9','=')) {
	update_option('leafletmapsmarker_version', '2.0');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.0','=')) {
	add_option('leafletmapsmarker_update_info', 'show'); //info: 1st time initialization
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.2','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.4','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.5','=')) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update26_1 = "UPDATE `" . $table_name_markers . "` SET `basemap` = 'googleLayer_satellite' WHERE `basemap` = 'googleLayer_satellit';";
	$wpdb->query($update26_1);
	$update26_2 = "UPDATE `" . $table_name_layers . "` SET `basemap` = 'googleLayer_satellite' WHERE `basemap` = 'googleLayer_satellit';";
	$wpdb->query($update26_2);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.6','=')) {
	update_option('leafletmapsmarker_version', '2.6.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.6.1','=')) {
	update_option('leafletmapsmarker_version', '2.7');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.7','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.7.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.7.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.8');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.8','=')) {
	update_option('leafletmapsmarker_version', '2.8.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.8.1','=')) {
	update_option('leafletmapsmarker_version', '2.8.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.8.2','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '2.9');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.9','=')) {
	update_option('leafletmapsmarker_version', '2.9.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.9.1','=')) {
	update_option('leafletmapsmarker_version', '2.9.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'2.9.2','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.0');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.0','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.1','=')) {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$editor_to_use = ( isset($lmm_options['misc_map_editor']) && ($lmm_options['misc_map_editor'] == 'advanced') ) ? 'advanced' : 'simplified';
	update_option('leafletmapsmarker_editor', $editor_to_use);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.2','=')) {
	update_option('leafletmapsmarker_version', '3.2.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.2.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.2.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.2.2','=')) {
	update_option('leafletmapsmarker_version', '3.2.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.2.3','=')) {
	update_option('leafletmapsmarker_version', '3.2.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.2.4','=')) {
	update_option('leafletmapsmarker_version', '3.2.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.2.5','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.4','=')) {
	update_option('leafletmapsmarker_version', '3.4.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.4.1','=')) {
	update_option('leafletmapsmarker_version', '3.4.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.4.2','=')) {
	update_option('leafletmapsmarker_version', '3.4.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.4.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.5','=')) {
	update_option('leafletmapsmarker_version', '3.5.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.5.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.5.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.5.2','=')) {
	update_option('leafletmapsmarker_version', '3.5.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.5.3','=')) {
	update_option('leafletmapsmarker_version', '3.5.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.5.4','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6','=')) {
	update_option('leafletmapsmarker_version', '3.6.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6.1','=')) {
	update_option('leafletmapsmarker_version', '3.6.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6.2','=')) {
	update_option('leafletmapsmarker_version', '3.6.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.6.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6.4','=')) {
	update_option('leafletmapsmarker_version', '3.6.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6.5','=')) {
	update_option('leafletmapsmarker_version', '3.6.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.6.6','=')) {
	update_option('leafletmapsmarker_version', '3.7');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.7','=')) {
	update_option('leafletmapsmarker_version', '3.8');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8','=')) {
	update_option('leafletmapsmarker_version', '3.8.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.1','=')) {
	update_option('leafletmapsmarker_version', '3.8.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.2','=')) {
	update_option('leafletmapsmarker_version', '3.8.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.3','=')) {
	update_option('leafletmapsmarker_version', '3.8.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.4','=')) {
	update_option('leafletmapsmarker_version', '3.8.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.5','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.8.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.6','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();

	//info: as cloudmade retired its free tiling service
	$cloudmade_update_1 = "UPDATE `" . $table_name_markers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'cloudmade';";
	$wpdb->query($cloudmade_update_1);
	$cloudmade_update_2 = "UPDATE `" . $table_name_layers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'cloudmade';";
	$wpdb->query($cloudmade_update_2);
	$cloudmade_update_3 = "UPDATE `" . $table_name_markers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'cloudmade2';";
	$wpdb->query($cloudmade_update_3);
	$cloudmade_update_4 = "UPDATE `" . $table_name_layers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'cloudmade2';";
	$wpdb->query($cloudmade_update_4);
	$cloudmade_update_5 = "UPDATE `" . $table_name_markers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'cloudmade3';";
	$wpdb->query($cloudmade_update_5);
	$cloudmade_update_6 = "UPDATE `" . $table_name_layers . "` SET `basemap` = 'osm_mapnik' WHERE `basemap` = 'cloudmade3';";
	$wpdb->query($cloudmade_update_6);

	update_option('leafletmapsmarker_version', '3.8.7');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.7','=')) {
	update_option('leafletmapsmarker_version', '3.8.8');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.8','=')) {
	update_option('leafletmapsmarker_version', '3.8.9');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.9','=')) {
	update_option('leafletmapsmarker_version', '3.8.10');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.8.10','=')) {
	update_option('leafletmapsmarker_version', '3.9');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9','=')) {
	update_option('leafletmapsmarker_version', '3.9.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.1','=')) {
	update_option('leafletmapsmarker_version', '3.9.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.2','=')) {
	update_option('leafletmapsmarker_version', '3.9.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.3','=')) {
	update_option('leafletmapsmarker_version', '3.9.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.4','=')) {
	update_option('leafletmapsmarker_version', '3.9.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.5','=')) {
	update_option('leafletmapsmarker_version', '3.9.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.6','=')) {
	update_option('leafletmapsmarker_version', '3.9.7');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.7','=')) {
	update_option('leafletmapsmarker_version', '3.9.8');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.8','=')) {
	update_option('leafletmapsmarker_version', '3.9.9');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.9','=')) {
	update_option('leafletmapsmarker_version', '3.9.10');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.9.10','=')) {
	update_option('leafletmapsmarker_version', '3.10');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.10.1');
	//info: update directions provider osrm to yours
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	if ($lmm_options['directions_provider'] == 'osrm') {
		$lmm_options['directions_provider'] = 'yours';
		update_option('leafletmapsmarker_options', $lmm_options);
	}
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10.1','=')) {
	update_option('leafletmapsmarker_version', '3.10.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10.2','=')) {
	update_option('leafletmapsmarker_version', '3.10.3');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10.3','=')) {
	update_option('leafletmapsmarker_version', '3.10.4');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10.4','=')) {
	update_option('leafletmapsmarker_version', '3.10.5');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10.5','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.10.6');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.10.6','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.11');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.11','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.11.1');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.11.1','=')) {
	update_option('leafletmapsmarker_version', '3.11.2');
}
if (version_compare(get_option('leafletmapsmarker_version'),'3.11.2','=')) {
	update_option('leafletmapsmarker_version', '3.12');

	//info: for v3.12 update routine only (change default geocoding provider to Algolia Places if Mapzen without API key is used)
	$options_current_geocoding_provider = get_option( 'leafletmapsmarker_options' );
	if ( ($options_current_geocoding_provider['geocoding_provider'] = 'mapzen-search') && ($options_current_geocoding_provider['geocoding_mapzen_search_api_key'] == NULL) ) {
		$overwrite_default_geocoding_provider = array('geocoding_provider' => 'algolia-places');
		$options_updated_geocoding_provider = array_merge($options_current_geocoding_provider, $overwrite_default_geocoding_provider);
		update_option( 'leafletmapsmarker_options', $options_updated_geocoding_provider );
	}	

	//info: for v3.12 update routine only (change fallback geocoding provider to Photon if Algolia is set - to prevent warnings that fallback provider is same as default geocoding provider)
	$options_current_geocoding_provider_fallback = get_option( 'leafletmapsmarker_options' );
	if ( ($options_current_geocoding_provider_fallback['geocoding_provider_fallback'] = 'algolia-places') && ($options_current_geocoding_provider_fallback['geocoding_mapzen_search_api_key'] == NULL) ) {
		$overwrite_default_geocoding_provider_fallback = array('geocoding_provider_fallback' => 'photon');
		$options_updated_geocoding_provider_fallback = array_merge($options_current_geocoding_provider_fallback, $overwrite_default_geocoding_provider_fallback);
		update_option( 'leafletmapsmarker_options', $options_updated_geocoding_provider_fallback );
	}

}
if (version_compare(get_option('leafletmapsmarker_version'),'3.12','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', '3.12.1');
}
/* template for free plugin updates - UPDATE ON EACH NEW FREE RELEASE!
if (version_compare(get_option('leafletmapsmarker_version'),'x.x','=')) {
	//2do - mandatory if new options in class-leaflet-options.php were added & update /inc/class-leaflet-options.php update routine
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	update_option('leafletmapsmarker_version', 'x.x+1');
}
*/

/*//////////////////////////////////////////////////////////////////
/ Pro version install and update routine starting here
//////////////////////////////////////////////////////////////////*/

if (get_option('leafletmapsmarker_version_pro') == 'init' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '0');
	}
	update_option('leafletmapsmarker_version_pro', '1.0');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.0','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.0');
	}
	update_option('leafletmapsmarker_version_pro', '1.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.1','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.1.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.1.1','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.1.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.1.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.1.2','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.1.2');
	}
	update_option('leafletmapsmarker_version_pro', '1.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.2','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.2');
	}
	update_option('leafletmapsmarker_version_pro', '1.2.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.2.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.2.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.3');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.3');
	}
	update_option('leafletmapsmarker_version_pro', '1.3.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.3.1','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.3.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.4');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.4','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.4');
	}
	update_option('leafletmapsmarker_version_pro', '1.5');
	//info: create QR code cache directory in wp-content/uploads
	WP_Filesystem();
	$target = LEAFLET_PLUGIN_QR_DIR;
	if (!is_dir($target)) //info: check for multisite installations not to extract files again if already installed on 1 site
	{
		wp_mkdir_p( $target );
		$source = LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'qr-codes';
		copy_dir($source, $target, $skip_list = array() );
		$zipfile = LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'readme-qr-codes.zip';
		unzip_file( $zipfile, $target );
		//info: fallback for hosts where copying zipfile to LEAFLET_PLUGIN_QR_DIR doesnt work
		if ( !file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'readme-qr-codes.txt') ) {
			if (class_exists('ZipArchive')) {
				$zip = new ZipArchive;
				$res = $zip->open( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'qr-codes' . DIRECTORY_SEPARATOR . 'readme-qr-codes.zip');
			    if ($res === TRUE) {
					$zip->extractTo(LEAFLET_PLUGIN_QR_DIR);
					$zip->close();
				}
			}
		}
		//info: delete zipped qr code readme.txt
		if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'readme-qr-codes.zip') ) {
			unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'readme-qr-codes.zip');
		}
	}
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.1','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.2','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.2');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.3');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.3','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.3');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.4');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.4','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.4');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.5');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.5','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.5');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.6');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.6','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.6');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.7');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.7','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.7');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.8');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.8','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.8');
	}
	update_option('leafletmapsmarker_version_pro', '1.5.9');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.5.9','=')) {
	delete_transient('leafletmapsmarkerpro_transient_proxy');
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.5.9');
	}
	update_option('leafletmapsmarker_version_pro', '1.6');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.6','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.6');
	}
	update_option('leafletmapsmarker_version_pro', '1.7');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.7','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.7');
	}
	update_option('leafletmapsmarker_version_pro', '1.8');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.8','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.8');
	}
	update_option('leafletmapsmarker_version_pro', '1.8.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.8.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.8.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.9');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.9','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.9');
	}
	update_option('leafletmapsmarker_version_pro', '1.9.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.9.1','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.9.1');
	}
	update_option('leafletmapsmarker_version_pro', '1.9.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'1.9.2','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '1.9.2');
	}
	update_option('leafletmapsmarker_version_pro', '2.0');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.0','=')) {
	//info: delete SimplePie MD5 cache file with random name to avoid WAF wrong positives
	$lmm_upload_dir = wp_upload_dir();
	$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
	if (is_dir($icons_directory)) {
		foreach(glob($icons_directory.'*.spc') as $v){
			unlink($v);
		}
	}
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.0');
	}
	update_option('leafletmapsmarker_version_pro', '2.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.1','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.1');
	}
	update_option('leafletmapsmarker_version_pro', '2.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.2','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.2');
	}
	update_option('leafletmapsmarker_version_pro', '2.3');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.3','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.3');
	}
	update_option('leafletmapsmarker_version_pro', '2.3.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.3.1','=')) {
	//info: convert LAYER column to JSON
	$markers = $wpdb->get_results('SELECT id,layer FROM ' . $wpdb->prefix . 'leafletmapsmarker_markers');
	foreach($markers as $marker) {
		if(is_numeric($marker->layer)){ //just convert the int value of the field
			$layer = json_encode(array(strval($marker->layer)));
			$wpdb->update( $wpdb->prefix . 'leafletmapsmarker_markers',
				array('layer'=> $layer),
				array('id'=>$marker->id),
				array('%s'), //format of the layer field
				array('%d')  //format of the where clause
				);
			unset($layer);
		}
	}
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.3.1');
	}
	update_option('leafletmapsmarker_version_pro', '2.4');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.4','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.4');
	}
	update_option('leafletmapsmarker_version_pro', '2.5');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.5','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.5');
	}
	update_option('leafletmapsmarker_version_pro', '2.6');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.6','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.6');
	}
	update_option('leafletmapsmarker_version_pro', '2.6.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.6.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.6.1');
	}
	update_option('leafletmapsmarker_version_pro', '2.6.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.6.2','=')) {

	//info: delete obsolete transients
 	delete_transient('leafletmapsmarker_tinymce_custom_css');
	delete_transient('leafletmapsmarkerpro_proxy_access');

	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.6.2');
	}
	update_option('leafletmapsmarker_version_pro', '2.7');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.7','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.7');
	}
	update_option('leafletmapsmarker_version_pro', '2.7.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.7.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.7.1');
	}
	update_option('leafletmapsmarker_version_pro', '2.7.2');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.7.2','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.7.2');
	}
	update_option('leafletmapsmarker_version_pro', '2.7.3');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.7.3','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.7.3');
	}
	update_option('leafletmapsmarker_version_pro', '2.8');

	//info: re-enable Google Maps API status if no fresh pro install or upgrade from free <3.11; only run once during upgrade from 2.7.3 to 2.8!
	if (
		(version_compare(get_option('leafletmapsmarker_version_pro_before_update'),'0','!=')) //info: check if no fresh pro install but pro upgrade
		||
			(
				(version_compare(get_option('leafletmapsmarker_version_pro_before_update'),'0','=')) //info: check if fresh pro install [or free upgrade]
				&&
				(version_compare(get_option('leafletmapsmarker_version_before_update'),'0','>')) //info: check if no fresh free install [and no fresh pro install]
				&&
				(version_compare(get_option('leafletmapsmarker_version_before_update'),'3.10.6','<')) //info: check if upgrade from free version (<3.11)
			)
		) {
			$overwrite_option_google_api_status = array('google_maps_api_status' => 'enabled');
			$options_current = get_option( 'leafletmapsmarker_options' );
			$options_updated = array_merge($options_current, $overwrite_option_google_api_status);
			update_option( 'leafletmapsmarker_options', $options_updated );
	}

}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.8','=')) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.8');
	}
	update_option('leafletmapsmarker_version_pro', '2.8.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.8.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.8.1');
	}
	update_option('leafletmapsmarker_version_pro', '2.9');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'2.9','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '2.9');
	}
	update_option('leafletmapsmarker_version_pro', '3.0');

	//info: for v3.0 update routine only (flush rewrite rules are otherwise only processed via plugin activation hook)
	MMP_Rewrite::set_rewrite_rules();
	flush_rewrite_rules(true);

	//info: code for updating default geocoding provider to Algolia Places if no Mapzen API key is used can be found in update-routine for free v3.12!
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'3.0','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '3.0');
	}
	update_option('leafletmapsmarker_version_pro', '3.0.1');
}
if (version_compare(get_option('leafletmapsmarker_version_pro'),'3.0.1','=')) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '3.0.1');
	}
	update_option('leafletmapsmarker_version_pro', '3.1');
	
	//info: set correct mime-type for GPX files
	$wpdb->query("UPDATE `{$wpdb->prefix}posts` SET `post_mime_type` = 'application/gpx+xml' WHERE LCASE(RIGHT(guid, 4)) = '.gpx' AND `post_type` = 'attachment' AND (`post_mime_type` = 'text/gpx' OR `post_mime_type` = 'application/octet-stream')");

	//info: redirect to create marker page only on first plugin activation, otherwise redirect is also done on bulk plugin activations
	if (get_option('leafletmapsmarker_redirect') == 'true')
	{
		update_option('leafletmapsmarker_redirect', 'false');
		wp_redirect(LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&first_run=true');
	} else {
		update_option('leafletmapsmarker_update_info', 'show');
	}
	//info: hide changelog for new installations
	$version_before_update = get_option('leafletmapsmarker_version_pro_before_update');
	if ($version_before_update == '0') {
			update_option('leafletmapsmarker_update_info', 'hide');
	}
	//info: delete all install+update transients (free+pro) at once to save db queries
	$table_options = $wpdb->prefix.'options';
	$delete_transient_query_1 = "DELETE FROM `" . $table_options . "` WHERE `" . $table_options . "`.`option_name` LIKE '_transient_leafletmapsmarker_install_update_cache%';";
	$wpdb->query($delete_transient_query_1);
	$delete_transient_query_2 = "DELETE FROM `" . $table_options . "` WHERE `" . $table_options . "`.`option_name` LIKE '_transient_timeout_leafletmapsmarker_install_update_cache%';";
	$wpdb->query($delete_transient_query_2);
	//info: re-add latest install-update-transient so routine is not run twice - update on new releases!!!
	set_transient( 'leafletmapsmarker_install_update_cache_vp31', 'execute install and update-routine only once a day', 60*60*24 );
}

/* template for pro plugin updates
if (version_compare(get_option('leafletmapsmarker_version_pro'),'3.xbefore','=')) {
	//2do - mandatory if new options in class-leaflet-options.php were added & update /inc/class-leaflet-options.php update routine
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_pro_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_pro_before_update', 'MapsMarkerPro-transient-for-dynamic-changelog', 60 );
		update_option('leafletmapsmarker_version_pro_before_update', '3.xbefore'); //2do - update to version before update
	}
	update_option('leafletmapsmarker_version_pro', '3.xneu');
	//2do - optional: add code for sql updates (no ddl - done by dbdelta!)
	//2do - mandatory: move code for redirect-on-first-activation-check and "hide changelog for new installations" to here
	//2do - mandatory: change install-and-update-transient to current version
	//2do - mandatory: set $current_version in leaflet-core.php / function lmm_install_and_updates()
	//2do - mandatory: set $current_version in uninstall.php
	//2do - update free version install-and-update-code too
}
*/
