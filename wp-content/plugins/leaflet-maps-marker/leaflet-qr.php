<?php
/*
    QR code generator - Leaflet Maps Marker Plugin
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
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	if (isset($_GET['layer'])) {
			$url = LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . htmlspecialchars($_GET['layer']);
	} else if (isset($_GET['marker'])) {
			$url = LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . htmlspecialchars($_GET['marker']);
	}
	//info: visualead settings
	if ($lmm_options['qrcode_provider'] == 'visualead') {
		$api_url = 'https://api.visualead.com/v3/generate_from_project?api_key=22ecaee1-101a-4ee8-1bc0-0000584d2591&project_id=94819&qr_x=4&qr_y=5&qr_size=124&qr_rotation=0&output_type=1&action=url&content[url]='.$url.'&cells_type=1&markers_type=1';
		$output = wp_remote_get( $api_url, array( 'sslverify' => false, 'timeout' => 10 ) );
		$results = json_decode($output['body']);
		
		//$results = json_decode($output);
		if($results->response ==1){
			$image_decoded= base64_decode($results->image);
			echo '<span title="' . sprintf(esc_attr__('QR code image for link to full screen map (%s)','lmm'),$url) . '"><img src="data:image/png;base64,' . $results->image . '" alt="QR-Code"/></span>';
			echo '<br/><a href="https://www.visualead.com/api/?pricing_mapsmarker" target="_blank" title="' . esc_attr__('QR code powered by visualead.com','lmm') . '"><img style="margin:10px 0 0 35px;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/logo-visualead.png"></a>';
		} else {
			echo __('QR code could not be generated!','lmm') . '<br/>';
			echo 'Error ID: ' . $results->error_id . ' (' . $results->error . ')<br/>';
			echo sprintf(__('Please contact %1$s for more details','lmm'), '<a href="mailto:api@visualead.com?subject=QR error ID '.$results->error_id.'">api@visualead.com</a>');
		}
	//info: Google QR settings
	} else if ($lmm_options['qrcode_provider'] == 'google') {
		$google_qr_link = 'https://chart.googleapis.com/chart?chs=' . intval($lmm_options[ 'misc_qrcode_size' ]) . 'x' . intval($lmm_options[ 'misc_qrcode_size' ]) . '&cht=qr&chl=' . $url;
		echo '<script type="text/javascript">window.location.href = "' . $google_qr_link . '";</script>  ';
	}
} //info: end plugin active check
?>