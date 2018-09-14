<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'proxy.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

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
	global $current_user;
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$referer_marker = LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker';
	$referer_layer = LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer';
	$gpx_proxy_nonce = (isset($_GET['nonce']) ? $_GET['nonce'] : '');

	if ( (current_user_can($lmm_options[ 'capabilities_view_others' ])) && (wp_verify_nonce($gpx_proxy_nonce, 'gpx-proxy-nonce')) && ((strpos($_SERVER['HTTP_REFERER'], $referer_marker) === 0) || (strpos($_SERVER['HTTP_REFERER'], $referer_layer) === 0)) ) {
		if (isset($_GET['url'])) {
			$gpx_content_raw = wp_remote_get( esc_url_raw($_GET['url']), array( 'sslverify' => false, 'timeout' => 30 ) );
			$gpx_content = str_replace("\xEF\xBB\xBF",'',$gpx_content_raw['body']);  //info: replace UTF8-BOM for Chrome - not sure if needed here
			header( 'Content-type: text/xml' );
			echo $gpx_content;
		}
	} else {
		die("".__('Security check failed - please call this function from the according admin page!','lmm')."");
	}
}