<?php
/*
    Download file API - Maps Marker Pro
*/
//info redirect to permalink if file is being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-download.php') {
	while (!is_file('wp-load.php')) {
		if (is_dir('..' . DIRECTORY_SEPARATOR)) {
			chdir('..' . DIRECTORY_SEPARATOR);
		} else {
			die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
		}
	}
	require_once('wp-load.php');
	$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
	wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/download/' . $argv), 301);
	exit;
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
	echo sprintf(__('The plugin "Maps Marker Pro" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), antispambot(get_bloginfo('admin_email')) );
} else {
	global $wpdb;
	//info: prepare GET variables
	$format = isset($_GET['format']) ? sanitize_text_field($_GET['format']) : '';
	if ($format == 'gpx') {
		$map_db_column = 'gpx_url';
	}
	$map_type = isset($_GET['map_type']) ? sanitize_text_field($_GET['map_type']) : '';
	if ($map_type == 'marker') {
		$map_db_table = $wpdb->prefix.'leafletmapsmarker_markers';
	} else if ($map_type == 'layer') {
		$map_db_table = $wpdb->prefix.'leafletmapsmarker_layers';
	}
	$map_id = isset($_GET['map_id']) ? intval($_GET['map_id']) : '';
	$file_url = esc_url($wpdb->get_var($wpdb->prepare("SELECT $map_db_column FROM $map_db_table WHERE id = '%d'", $map_id)));

	if ($file_url === NULL) {
		echo sprintf(esc_attr__('Error: %1s download url could not be found - please check your input parameters!','lmm'), $format);
	} else {
		$file_content = wp_remote_get($file_url, array( 'sslverify' => false, 'timeout' => 30 ));
		if (!is_wp_error($file_content) && $file_content['response']['code'] != '404') {

			libxml_use_internal_errors(true);
			$xml_validity_check = simplexml_load_string($file_content['body']);
			if($xml_validity_check !== false) {

				//info: required for IE, http://davidwalsh.name/php-force-download
				if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}
		
				header('Pragma: public'); 	// required
				header('Expires: 0');		// no cache
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
				header('Cache-Control: private',false);
				if ($format == 'gpx') {
					header('Content-type: application/gpx+xml; charset=utf-8');
				}
				header('Content-Disposition: attachment; filename="' .  basename(esc_url($file_url)) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: close');
				echo str_replace("\xEF\xBB\xBF",'',$file_content['body']); //info: replace UTF8-BOM for Chrome - not sure if needed here

			} else {

				echo sprintf(__('Automatic GPX download cancelled due to security concerns - the file at %1$s does not seem to be a valid XML file!', 'lmm'), '<a style="text-decoration:none;" href="' . $file_url . '">' . $file_url . '</a>') . '<br/><br/>';
				foreach(libxml_get_errors() as $error) {
					echo 'Error parsing XML file ' . $file . ': ' . $error->message . '<br/>';
					error_log('Error parsing XML file ' . $file . ': ' . $error->message);
				}

			}

		} else {
			echo __('Error', 'lmm') . ' ' . $file_content['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), $file_url);
		}
	}
} //info: end plugin active check
