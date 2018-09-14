<?php
/*
    QR code generator - Maps Marker Pro
*/
//info redirect to permalink if file is being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-qr.php') {
	while (!is_file('wp-load.php')) {
		if (is_dir('..' . DIRECTORY_SEPARATOR)) {
			chdir('..' . DIRECTORY_SEPARATOR);
		} else {
			die('Error: Could not construct path to wp-load.php - please check <a href="https://www.mapsmarker.com/path-error">https://www.mapsmarker.com/path-error</a> for more details');
		}
	}
	require_once('wp-load.php');
	if (isset($_GET['layer'])) {
		$layer = intval($_GET['layer']);
		unset($_GET['layer']);
		$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
		wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/qr/layer/' . $layer . '/' . $argv), 301);
	} elseif (isset($_GET['marker'])) {
		$marker = intval($_GET['marker']);
		unset($_GET['marker']);
		$argv = (!empty($_GET)) ? '?' . http_build_query($_GET) : '';
		wp_redirect(MMP_Globals::translate_permalink(MMP_Rewrite::get_base_url() . MMP_Rewrite::get_slug() . '/qr/marker/' . $marker . '/' . $argv), 301);
	}
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
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$lmm_base_url = MMP_Rewrite::get_base_url();
	$lmm_slug = MMP_Rewrite::get_slug();
	if (get_query_var('layer', false)) {
		$filename = 'layer-' . intval(get_query_var('layer')) . '.png';
		$url = MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/layer/' . intval(get_query_var('layer')) . '/');
	} else if (get_query_var('marker', false)) {
		$filename = 'marker-' . intval(get_query_var('marker')) . '.png';
		$url = MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . intval(get_query_var('marker')) . '/');
	}
	//info: visualead settings
	if ($lmm_options['qrcode_provider'] == 'visualead') {
		if ( ! file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . $filename) ) {

			$api_key = ($lmm_options['qrcode_visualead_api_key'] == NULL) ? '22ecaee1-101a-4ee8-1bc0-0000584d2591' : htmlspecialchars($lmm_options['qrcode_visualead_api_key']);
			$project_id = ($lmm_options['qrcode_visualead_project_id'] == NULL) ? '94819' : intval($lmm_options['qrcode_visualead_project_id']);

			if ($lmm_options['qrcode_visualead_qr_cell_size'] == NULL) {
				$qr_cell_size = '';
			} else {
				$qr_cell_size = '&qr_cell_size=' . intval($lmm_options['qrcode_visualead_qr_cell_size']); //info: due to Error ID: 501 (Invalid type for qr_cell_size , parameter must be numeric)
			}
			if ($lmm_options['qrcode_visualead_qr_gravity'] == NULL) {
				$qr_gravity = '';
			} else {
				$qr_gravity = htmlspecialchars($lmm_options['qrcode_visualead_qr_gravity']);
			}
			if ($lmm_options['qrcode_visualead_output_image_width'] == NULL) {
				$output_image_width = '';
			} else if ( ($lmm_options['qrcode_visualead_output_image_width'] != NULL) && (intval($lmm_options['qrcode_visualead_output_image_width']) < 124) ) {
				$output_image_width = '&output_image_width=124';
			} else if ( ($lmm_options['qrcode_visualead_output_image_width'] != NULL) && (intval($lmm_options['qrcode_visualead_output_image_width']) >= 124) ) {
				$output_image_width = '&output_image_width=' . intval($lmm_options['qrcode_visualead_output_image_width']);  //info: due to Error ID: 501 (Invalid type for output_image_width , parameter must be numeric)
			}

			//info: prepare different api urls
			if ($lmm_options['qrcode_visualead_api_key'] == NULL) { //info: generate_from_project defaults
				$api_url = 'https://api.visualead.com/v3/generate_from_project?api_key='.$api_key.'&project_id='.$project_id.'&qr_x='.intval($lmm_options['qrcode_visualead_qr_x']).'&qr_y='.intval($lmm_options['qrcode_visualead_qr_y']).'&qr_size='.intval($lmm_options['qrcode_visualead_qr_size']).$qr_cell_size.'&qr_rotation='.intval($lmm_options['qrcode_visualead_qr_rotation']).'&qr_gravity='.$qr_gravity.$output_image_width.'&output_type=1&action=url&content[url]='.$url.'&cells_type='.intval($lmm_options['qrcode_visualead_cells_type']).'&markers_type='.intval($lmm_options['qrcode_visualead_markers_type']);
			} else {
				if ( ($lmm_options['qrcode_visualead_image_url'] != NULL) && (($lmm_options['qrcode_visualead_project_id'] == NULL)) ) { //info: generate method (image url but no project_id)
					$image_url = urlencode(esc_url($lmm_options['qrcode_visualead_image_url']));
					$api_url = 'https://api.visualead.com/v3/generate?api_key='.$api_key.'&image='.$image_url.'&qr_x='.intval($lmm_options['qrcode_visualead_qr_x']).'&qr_y='.intval($lmm_options['qrcode_visualead_qr_y']).'&qr_size='.intval($lmm_options['qrcode_visualead_qr_size']).$qr_cell_size.'&qr_rotation='.intval($lmm_options['qrcode_visualead_qr_rotation']).'&qr_gravity='.$qr_gravity.$output_image_width.'&output_type=1&action=url&content[url]='.$url.'&cells_type='.intval($lmm_options['qrcode_visualead_cells_type']).'&markers_type='.intval($lmm_options['qrcode_visualead_markers_type']);
				} else if ($lmm_options['qrcode_visualead_project_id'] != NULL) { //infos: if project_id is given use same as generate_from_project defaults with custom API key
					$api_url = 'https://api.visualead.com/v3/generate_from_project?api_key='.$api_key.'&project_id='.$project_id.'&qr_x='.intval($lmm_options['qrcode_visualead_qr_x']).'&qr_y='.intval($lmm_options['qrcode_visualead_qr_y']).'&qr_size='.intval($lmm_options['qrcode_visualead_qr_size']).$qr_cell_size.'&qr_rotation='.intval($lmm_options['qrcode_visualead_qr_rotation']).'&qr_gravity='.$qr_gravity.$output_image_width.'&output_type=1&action=url&content[url]='.$url.'&cells_type='.intval($lmm_options['qrcode_visualead_cells_type']).'&markers_type='.intval($lmm_options['qrcode_visualead_markers_type']);
				} else if ($lmm_options['qrcode_visualead_image_url'] == NULL) {
					$api_url = 'https://api.visualead.com/v3/generate_from_project?api_key='.$api_key.'&project_id='.$project_id.'&qr_x='.intval($lmm_options['qrcode_visualead_qr_x']).'&qr_y='.intval($lmm_options['qrcode_visualead_qr_y']).'&qr_size='.intval($lmm_options['qrcode_visualead_qr_size']).$qr_cell_size.'&qr_rotation='.intval($lmm_options['qrcode_visualead_qr_rotation']).'&qr_gravity='.$qr_gravity.$output_image_width.'&output_type=1&action=url&content[url]='.$url.'&cells_type='.intval($lmm_options['qrcode_visualead_cells_type']).'&markers_type='.intval($lmm_options['qrcode_visualead_markers_type']);
				}
			}

			$output = wp_remote_get( $api_url, array( 'sslverify' => false, 'timeout' => 10 ) );
			$results = json_decode($output['body']);

			if ($results->response == 1) {
				$image_decoded = base64_decode($results->image);
				if ( (is_writable(LEAFLET_PLUGIN_QR_DIR)) && ($lmm_options['qrcode_visualead_caching'] == 'enabled') ) {
					include_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php' );
					WP_Filesystem();
					global $wp_filesystem;
					$wp_filesystem->put_contents(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . $filename, $image_decoded, FS_CHMOD_FILE);
				}
				echo '<a href="data:image/png;base64,' . $results->image . '" title="' . sprintf(esc_attr__('QR code image for link to full screen map (%s)','lmm'),$url) . '"><img src="data:image/png;base64,' . $results->image . '" alt="QR-Code"/></a>';
			} else {
				echo __('QR code could not be generated!','lmm') . '<br/>';
				echo 'Error ID: ' . $results->error_id . ' (' . $results->error . ')<br/>';
				echo sprintf(__('Please contact %1$s for more details','lmm'), '<a href="mailto:api@visualead.com?subject=QR error ID '.$results->error_id.' with API-key '.$api_key.'">api@visualead.com</a>');
			}
		} else { //info: load cached QR code
			echo '<a href="' . LEAFLET_PLUGIN_QR_URL . DIRECTORY_SEPARATOR . $filename . '" title="' . sprintf(esc_attr__('QR code image for link to full screen map (%s)','lmm'),$url) . '"><img src="' . LEAFLET_PLUGIN_QR_URL . DIRECTORY_SEPARATOR . $filename . '" alt="QR-Code"/></a>';
		}
	//info: Google QR settings
	} else if ($lmm_options['qrcode_provider'] == 'google') {
		$google_qr_link = 'https://chart.googleapis.com/chart?chs=' . intval($lmm_options[ 'misc_qrcode_size' ]) . 'x' . intval($lmm_options[ 'misc_qrcode_size' ]) . '&cht=qr&chl=' . $url;
		echo '<script type="text/javascript">window.location.href = "' . $google_qr_link . '";</script>  ';
	}
} //info: end plugin active check
