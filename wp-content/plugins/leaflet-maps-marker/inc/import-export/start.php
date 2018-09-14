<?php
/*
    Import/Export Standalone - Leaflet Maps Marker Plugin
	Check cell data type: http://phpexcel.codeplex.com/discussions/403466
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
	$import_export_standalone_nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
	if (! wp_verify_nonce($import_export_standalone_nonce, 'import-export-standalone-nonce') ) die("".__('Security check failed - please call this function from the according admin page!','lmm')."");

	/**
	 * Sanitizes first character of string fields to prevent command injections
	 *
	 * @since  3.12.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	function sanitize_excel($string) {
		$filter = array('=', '+', '-', '@');
		$first_char = substr($string, 0, 1);
		$string = substr($string, 1);
		if (in_array($first_char, $filter)) {
			$first_char = "'" . $first_char . "'";
		}
		return $first_char . $string;
	}

	global $wpdb, $current_user;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$action_iframe = isset($_GET['action_iframe']) ? $_GET['action_iframe'] : '';
	$action_standalone  = isset($_POST['action_standalone']) ? $_POST['action_standalone'] : '';

	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PHPExcel.php';
	if ($action_standalone == NULL) {
		echo '<!DOCTYPE html>
				<head>
				<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
				<title>Import/Export for Maps Marker Pro</title>
				<style type="text/css">
					body { font-family: sans-serif;	padding:0 0 0 5px; margin:0px; font-size: 12px;	line-height: 1.4em; }
					a {	color: #21759B;	text-decoration: none; }
					a:hover, a:active, a:focus { color: #D54E21; }
					td {padding:5px 5px 5px 0;}
					error { font-weight:bold;color:red; }
				</style>
				</head>
				<body><p style="margin:0.5em 0 0 0;">';

		//info: get available caching methods for import/export prepare forms
		if (version_compare(phpversion(),"5.6.29","!=")){ //info: https://github.com/PHPOffice/PHPExcel/issues/1085
			if ( function_exists('sqlite_open') ){ //info: SQLite2
				$caching_sqlite2_disabled = '';
				$caching_sqlite2_disabled_css = '';
			} else {
				$caching_sqlite2_disabled = 'disabled="disabled"';
				$caching_sqlite2_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
			}
			if ( class_exists('SQLite3',FALSE) === TRUE ) { //info:SQLite3
				$caching_sqlite3_disabled = '';
				$caching_sqlite3_disabled_css = '';
			} else {
				$caching_sqlite3_disabled = 'disabled="disabled"';
				$caching_sqlite3_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
			}
		} else {
			$caching_sqlite2_disabled = 'disabled="disabled"';
			$caching_sqlite2_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
			$caching_sqlite3_disabled = 'disabled="disabled"';
			$caching_sqlite3_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_apc) === TRUE ) { //info: APC
			$caching_apc_disabled = '';
			$caching_apc_disabled_css = '';
		} else {
			$caching_apc_disabled = 'disabled="disabled"';
			$caching_apc_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( function_exists('wincache_ucache_add') ) { //info: Wincache
			$caching_wincache_disabled = '';
			$caching_wincache_disabled_css = '';
		} else {
			$caching_wincache_disabled = 'disabled="disabled"';
			$caching_wincache_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip) === TRUE ) { //info: MemoryGZip
			$caching_memorygzip_disabled = '';
			$caching_memorygzip_disabled_css = '';
		} else {
			$caching_memorygzip_disabled = 'disabled="disabled"';
			$caching_memorygzip_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_discISAM) === TRUE ) { //info: DiscISAM
			$caching_discisam_disabled = '';
			$caching_discisam_disabled_css = '';
		} else {
			$caching_discisam_disabled = 'disabled="disabled"';
			$caching_discisam_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp) === TRUE ) { //info: PHPTemp
			$caching_phptemp_disabled = '';
			$caching_phptemp_disabled_css = '';
		} else {
			$caching_phptemp_disabled = 'disabled="disabled"';
			$caching_phptemp_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( function_exists('igbinary_serialize') ) { //info: Igbinary
			$caching_igbinary_disabled = '';
			$caching_igbinary_disabled_css = '';
		} else {
			$caching_igbinary_disabled = 'disabled="disabled"';
			$caching_igbinary_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized) === TRUE ) { //info: MemorySerialized
			$caching_memoryserialized_disabled = '';
			$caching_memoryserialized_disabled_css = '';
		} else {
			$caching_memoryserialized_disabled = 'disabled="disabled"';
			$caching_memoryserialized_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}

		//info: disable geocoding if no API key is set for Mapzen Search, Google Geocoding and Mapzen Search
		if ($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL && $lmm_options['geocoding_provider'] == 'mapquest-geocoding') {
			$geocoding_radio_button_on = ' disabled="disabled"';
			$geocoding_radio_button_off = ' checked="checked"';
			$geocoding_provider_api_key_warning_mapquest = '<br/><strong>' . sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapquest', 'MapQuest Geocoding', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding') . '</strong>';
		} else if
			(
				( ($lmm_options['geocoding_provider'] == 'google-geocoding') || ($lmm_options['geocoding_provider_fallback'] == 'google-geocoding')	)
				&&
				(
					( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] == NULL) )
					||
					( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] == NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] == NULL)) )
				)
			)
		{
			$geocoding_radio_button_on = ' disabled="disabled"';
			$geocoding_radio_button_off = ' checked="checked"';
			$geocoding_provider_api_key_warning_google = '<br/><span style="font-weight:bold;">' . sprintf(__('Warning: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-google', 'Google Geocoding', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding') . '</span><br/>';
		} else if ( (($lmm_options['geocoding_provider'] == 'mapzen-search') || ($lmm_options['geocoding_provider_fallback'] == 'mapzen-search')) && ($lmm_options['geocoding_mapzen_search_api_key'] == NULL) ) {
			$geocoding_radio_button_on = ' disabled="disabled"';
			$geocoding_radio_button_off = ' checked="checked"';
			$geocoding_provider_api_key_warning_mapzen = '<br/><strong>' . sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapzen', 'Mapzen Search', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding') . '</strong>';
		} else {
			$geocoding_radio_button_on = ' checked="checked" disabled="disabled"';
			$geocoding_radio_button_off = '';
			$geocoding_provider_api_key_warning = '';
		}

		//info: prepare rate limit infos
		if ( ($lmm_options['geocoding_algolia_appId'] != NULL) && ($lmm_options['geocoding_algolia_apiKey'] != NULL) ){
			$algolia_rate_limit = sprintf(__('Rate limit: %1$s requests/%2$s and a maximum of %3$s requests/%4$s','lmm'), '100.000', __('month','lmm'), '15', __('second','lmm'));
		} else {
			$algolia_rate_limit = sprintf(__('Rate limit: %1$s requests/domain/%2$s and a maximum of %3$s requests/%4$s','lmm'), '1.000', __('day','lmm'), '15', __('second','lmm'));
		}
		$mapzen_rate_limit = sprintf(__('Rate limit: %1$s requests/%2$s and a maximum of %3$s requests/%4$s','lmm'), '30.000', __('month','lmm'), '6', __('second','lmm'));

		//info: check if Mapzen Search API key is set
		$geocoding_provider_mapzen_disabled = '';
		if ($lmm_options['geocoding_mapzen_search_api_key'] == NULL) {
			$option_mapzen_inactive = '<tr><td colspan="2"><strong>' . esc_attr__('Inactive (API key required)','lmm') . '</strong></td></tr><tr><td><input id="mapzen" type="radio" name="geocoding-provider" value="mapzen-search" ' . checked($lmm_options['geocoding_provider'], 'mapzen-search', false) . ' disabled="disabled"/><label for="mapzen">Mapzen Search (' . __('recommended','lmm') . ')</label></td><td>'. sprintf(__('Rate limit: %1$s transactions/month and a maximum of %2$s requests/%3$s','lmm'), '30.000', '6', __('second','lmm')) . $geocoding_provider_api_key_warning_mapzen . '</td></tr>';
			$option_mapzen_active = '';
		} else {
			$option_mapzen_active = '<tr><td><input id="mapzen" type="radio" name="geocoding-provider" value="mapzen-geocoding" disabled="disabled" ' . checked($lmm_options['geocoding_provider'], 'mapzen-search', false)  . ' /><label for="mapzen">Mapzen Search</label></td><td>'. sprintf(__('Rate limit: %1$s transactions/month and a maximum of %2$s requests/%3$s','lmm'), '30.000', '6', __('second','lmm')) . '</td></tr>';
			$option_mapzen_inactive = '';
		}		
		
		//info: check if MapQuest API key is set
		$geocoding_provider_mapquest_disabled = '';
		if ($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL) {
			$option_mapquest_inactive = '<tr><td colspan="2"><strong>' . esc_attr__('Inactive (API key required)','lmm') . '</strong></td></tr><tr><td><input id="mapquest" type="radio" name="geocoding-provider" value="mapquest-geocoding" ' . checked($lmm_options['geocoding_provider'], 'mapquest-geocoding', false) . ' disabled="disabled"/><label for="mapquest">MapQuest Geocoding</label></td><td>'. sprintf(__('Rate limit: %1$s transactions/month and a maximum of %2$s requests/%3$s','lmm'), '15.000', '10', __('second','lmm')) . $geocoding_provider_api_key_warning_mapquest . '</td></tr>';
			$option_mapquest_active = '';
		} else {
			$option_mapquest_active = '<tr><td><input id="mapquest" type="radio" name="geocoding-provider" value="mapquest-geocoding" disabled="disabled" ' . checked($lmm_options['geocoding_provider'], 'mapquest-geocoding', false)  . ' /><label for="mapquest">MapQuest Geocoding</label></td><td>'. sprintf(__('Rate limit: %1$s transactions/month and a maximum of %2$s requests/%3$s','lmm'), '15.000', '10', __('second','lmm')) . '</td></tr>';
			$option_mapquest_inactive = '';
		}
		//info: check if Google Geocoding API key is set
		$geocoding_provider_google_disabled = '';
		if
		(
				( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] == NULL) )
				||
				( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] == NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] == NULL)) )
		) {
			$option_google_inactive = '<tr><td colspan="2"><strong>' . esc_attr__('Inactive (API key required)','lmm') . '</strong></td></tr><tr><td><input id="google" type="radio" name="geocoding-provider" value="google-geocoding" ' . checked($lmm_options['geocoding_provider'], 'google-geocoding', false) . 'disabled="disabled" /><label for="google">Google Geocoding</label></td><td>'.sprintf(__('Rate limit: %1$s requests/%2$s and a maximum of %3$s requests/%4$s','lmm'), '2.500', __('day','lmm'), '50', __('second','lmm')). $geocoding_provider_api_key_warning_google . '</td></tr>';
			$option_google_active = '';
		} else {
			$option_google_active = '<tr><td><input id="google" type="radio" name="geocoding-provider" value="google-geocoding" disabled="disabled" ' . checked($lmm_options['geocoding_provider'], 'google-geocoding', false ) . '/><label for="google">Google Geocoding</label></td><td>'. sprintf(__('Rate limit: %1$s requests/%2$s and a maximum of %3$s requests/%4$s','lmm'), '2.500', __('day','lmm'), '50', __('second','lmm')) .'</td></tr>';
			$option_google_inactive = '';
		}

		$geocoding_provider = '
		<table style="margin-left:23px;background:#ccc;border-radius:5px;">
			<tr>
				<td colspan="2">&nbsp;&nbsp;&nbsp;<strong>'. esc_attr__('Available geocoding providers','lmm') . '</strong> (<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#geocoding" title="' . esc_attr__('click to change geocoding provider','lmm') . '" target="_top">' . __('Settings','lmm') . '</a>)</td>
			</tr>
			<tr>
				<td style="width:150px;"><input id="algolia" type="radio" name="geocoding-provider" disabled="disabled" '. checked($lmm_options['geocoding_provider'], 'algolia-places', false ) . ' value="algolia-places" /><label for="algolia">Algolia Places</a></td>
				<td>'.$algolia_rate_limit.'</td>
			</tr>
			<tr>
				<td style="width:150px;"><input id="photon" type="radio" name="geocoding-provider" disabled="disabled" '. checked($lmm_options['geocoding_provider'], 'photon', false ) .' value="photon" /><label for="photon">Photon@MapsMarker</label></td>
				<td>'.sprintf(__('Rate limit: %1$s requests/domain/%2$s and a maximum of %3$s requests/%4$s','lmm'), '2.500', __('day','lmm'), '10', __('second','lmm')).'</td>
			</tr>
			'. $option_mapzen_active .'
			'. $option_mapquest_active .'
			'. $option_google_active .'
			'. $option_mapzen_inactive .'
			'. $option_mapquest_inactive .'
			'. $option_google_inactive .'
		</table>';

		if ($action_iframe == 'import') {
			/**********************************
			*         import form             *
			**********************************/
			echo '<table><tr><td><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-import.png" width="32" height="32" alt="import"></td>';
			echo '<td><h1 style="font-size:20px;margin:0px;"> ' . __('prepare import','lmm') . '</h1></td></tr></table>';
			echo '
			<script>
			function lmm_check_file_extension()	{
				str=document.getElementById("import-file").value.toUpperCase();
				suffix=".CSV";
				suffix2=".XLS";
				suffix3=".XLSX";
				suffix4=".ODS";
				if(!(str.indexOf(suffix, str.length - suffix.length) !== -1
							|| str.indexOf(suffix2, str.length - suffix2.length) !== -1
							|| str.indexOf(suffix3, str.length - suffix3.length) !== -1
							|| str.indexOf(suffix4, str.length - suffix4.length) !== -1)
					){
					alert("' . sprintf(esc_attr__('Error: file type not allowed - allowed file types: %1$s','lmm'), 'csv, xls, xlsx, ods') . '");
					document.getElementById("import-file").value="";
				}
			}
			</script>

			<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" target="_top">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>

			<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action_standalone" value="import" />
			<table>
				<tr>
					<td colspan="2">
						' . sprintf(__('For details and tutorials about imports and exports, please visit %1s','lmm'), '<a href="http://www.mapsmarker.com/import-export" target="_blank" style="text-decoration:none;">www.mapsmarker.com/import-export</a>') . '
						<ul>
							<li>' . __('Download import template files','lmm') . ': ';
							if (extension_loaded('zip')) {
								echo '<a href="http://www.mapsmarker.com/import-template-xlsx" target="_blank">.xlsx (Excel2007)</a>, <a href="http://www.mapsmarker.com/import-template-xls" target="_blank">.xls (Excel5)</a>, <a href="http://www.mapsmarker.com/import-template-ods" target="_blank">.ods (OpenOffice/LibreOffice)</a>, <a href="http://www.mapsmarker.com/import-template-csv" target="_blank">.csv</a><br/>';
							} else {
								echo '<a href="http://www.mapsmarker.com/import-template-xls" target="_blank">.xls (Excel5)</a>, <a href="http://www.mapsmarker.com/import-template-csv" target="_blank">.csv</a><br/>';
							}
							echo '</li>
							<li>' . __('If you want to bulk update existing markers, please make an export first!','lmm') . '</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Please select import file','lmm') . '</td>
					<td>
						<input id="import-file" name="import-file" type="file" size="50" onchange="lmm_check_file_extension()" disabled="disabled" /><br/>';
						if (extension_loaded('zip')) {
							echo sprintf(__('supported formats: %1$s','lmm'), 'xlsx, xls, ods, csv') . ' ' . __('(with semicolons as delimiters)','lmm');
						} else {
							echo sprintf(__('supported formats: %1$s','lmm'), 'xls, csv') . ' ' . __('(with semicolons as delimiters)','lmm') . '<br/>';
							echo ' <span style="background:yellow;padding:2px;">' . __('The PHP extension php_zip is not enabled on your server - this means that .xlsx or .ods files cannot be handled. Please contact your admin for more details.','lmm') . '</span>';
						}
					echo '</td>
				</tr>
				<tr>
					<td valign="top">' . __('Import mode','lmm') . '</td>
					<td>
						<input id="import-mode-add" type="radio" name="import-mode" value="import-mode-add" checked="checked" disabled="disabled" /> <label for="import-mode-add"> ' . __('bulk additions (add new markers)','lmm') . '</label><br/>
						<input id="import-mode-update" type="radio" name="import-mode" value="import-mode-update" disabled="disabled" /> <label for="import-mode-update"> ' . __('bulk updates (update existing markers)','lmm') . '</label><br/>
						<p style="margin:5px 0 0 24px;">' . __('Please note: values in the column ID from import file will be ignored on bulk additions but are needed on bulk updates!','lmm') . '</p>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which geocoding option should be used?','lmm') . '</td>
					<td>
						<input id="geocoding-on" type="radio" name="geocoding-option" value="geocoding-on" selected="selected" ' . $geocoding_radio_button_on . ' disabled="disabled"/> <label for="geocoding-on"> ' . __('use address for geocoding (latitude and longitude values will get overwritten by geocoding results)','lmm') . '</label>' . $geocoding_provider . '
						<input id="geocoding-off" type="radio" name="geocoding-option" value="geocoding-off" ' . $geocoding_radio_button_off . ' disabled="disabled"/> <label for="geocoding-off"> ' . __('do not use address for geocoding (address, latitude and longitude values will be imported as given)','lmm') . '</label><br/>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which audit option should be used?','lmm') . '</td>
					<td>
						<input id="audit-on" type="radio" name="audit-option" value="audit-on" checked="checked" disabled="disabled" /> <label for="audit-on"> ' . sprintf(__('use current userlogin (%1$s) and current timestamp for createdby/createdon on new markers and updatedby/updatedon on marker updates','lmm'), $current_user->user_login) . '</label><br/>
						<input id="audit-off" type="radio" name="audit-option" value="audit-off" disabled="disabled" /> <label for="audit-off"> ' . __('import values for createdby/createdon and updatedby/updatedon as given (no changes will be made to import file)','lmm') . '</label>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which caching method should be used?','lmm') . '</td>
					<td>
						<input id="caching-auto" type="radio" name="caching-method" value="auto" checked="checked" disabled="disabled" /> <label for="caching-auto">' . __('automatic','lmm') . '</label>

						<a href="#" id="show-more-link" onclick="document.getElementById(\'caching-options-more\').style.display = \'block\';document.getElementById(\'show-more-link\').style.display = \'none\';"> - ' . __('show more options','lmm') . '</a>
						<div id="caching-options-more" style="display:none;">
						<span ' . $caching_sqlite2_disabled_css . '><input id="caching-sqlite2" type="radio" name="caching-method" value="sqlite2" ' . $caching_sqlite2_disabled . ' /> <label for="caching-sqlite2">SQLite2 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('low','lmm')) . ')</label></span><br/>

						<span ' . $caching_sqlite3_disabled_css . '><input id="caching-sqlite3" type="radio" name="caching-method" value="sqlite3" ' . $caching_sqlite3_disabled . ' /> <label for="caching-sqlite3">SQLite3 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('very low','lmm')) . ')</label></span><br/>
						<span ' . $caching_apc_disabled_css . '><input id="caching-apc" type="radio" name="caching-method" value="apc" ' . $caching_apc_disabled . ' /> <label for="caching-apc">APC <a href="http://pecl.php.net/package/APC" title="http://pecl.php.net/package/APC" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-apc-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-apc-timeout" type="input" name="caching-apc-timeout" value="600" style="width:30px;" ' . $caching_apc_disabled . ' /></label></span><br/>

						<span ' . $caching_wincache_disabled_css . '><input id="caching-wincache" type="radio" name="caching-method" value="wincache" ' . $caching_wincache_disabled . ' /> <label for="caching-wincache">Wincache <a href="http://sourceforge.net/projects/wincache/" title="http://sourceforge.net/projects/wincache/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-wincache-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-wincache-timeout" type="input" name="caching-wincache-timeout" value="600" style="width:31px;" ' . $caching_wincache_disabled . ' /></label></span><br/>

						<span ' . $caching_memorygzip_disabled_css . '><input id="caching-memorygzip" type="radio" name="caching-method" value="memorygzip" ' . $caching_memorygzip_disabled . ' /> <label for="caching-memorygzip">MemoryGZIP (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label></span><br/>

						<span ' . $caching_discisam_disabled_css . '><input id="caching-discisam" type="radio" name="caching-method" value="discisam" ' . $caching_discisam_disabled . ' /> <label for="caching-discisam">DiscISAM (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')' . $caching_discisam_disabled . '</label><br/>
						<label for="caching-discisam-directory" style="margin-left:24px;">' . __('optional - use the following custom directory for temp files','lmm') . '</label>:<br/>
						<input style="margin-left:24px;width:300px;" id="caching-discisam-directory" type="input" name="caching-discisam-directory" value="" ' . $caching_discisam_disabled . ' /></label></span><br/>

						<span ' . $caching_phptemp_disabled_css . '><input id="caching-phptemp" type="radio" name="caching-method" value="phptemp" ' . $caching_phptemp_disabled . ' /> <label for="caching-phptemp">phpTemp ' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label><br/>
						<label for="caching-phptemp-filesize" style="margin-left:24px;">' . __('maximum temporary file size in MB','lmm') . ' </label> <input id="caching-phptemp-filesize" type="input" name="caching-phptemp-filesize" value="8" style="width:30px;" ' . $caching_phptemp_disabled . ' /></label></span><br/>

						<span ' . $caching_igbinary_disabled_css . '><input id="caching-igbinary" type="radio" name="caching-method" value="igbinary" ' . $caching_igbinary_disabled . ' /> <label for="caching-igbinary">igbinary <a href="http://pecl.php.net/package/igbinary" title="http://pecl.php.net/package/igbinary" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('high','lmm')) . ')</label></span><br/>

						<span ' . $caching_memoryserialized_disabled_css . '><input id="caching-memoryserialized" type="radio" name="caching-method" value="memoryserialized" ' . $caching_memoryserialized_disabled . ' /> <label for="caching-memoryserialized">Memory serialized (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('high','lmm'), __('high','lmm')) . ')' . $caching_memoryserialized_disabled . '</label></span><br/>

						<input id="caching-memory" type="radio" name="caching-method" value="memory" /> <label for="caching-memory">Memory <a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" title="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very high','lmm'), __('very high','lmm')) . ')</label><br/>

						<input type="checkbox" name="setReadDataOnly" id="setReadDataOnly"/> <label for="setReadDataOnly"> ' . __('further reduce memory usage for xlsx/xls/ods input files by only importing linktext for hyperlinks','lmm') . '</a>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Test mode','lmm') . '</td>
					<td>
						<input id="test-mode-on" type="radio" name="test-mode" value="test-mode-on" checked="checked" disabled="disabled" /> <label for="test-mode-on"> ' . __('on (check import file only - no changes will be made to database)','lmm') . '</label><br/>
						<input id="test-mode-off" type="radio" name="test-mode" value="test-mode-off" disabled="disabled" /> <label for="test-mode-off"> ' . __('off (save changes to database)','lmm') . '</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input style="font-weight:bold;" type="submit" name="submit" class="submit button-primary" value="' . esc_attr__('start import','lmm') . '" disabled="disabled" />
						<br/><br/>
						<a href="javascript:history.back();">' . __('or back to overview','lmm') . '</a>	
					</td>
				</tr>
			</table>
			</form>';
		//info: end ($action_iframe == 'import') markers
		} else if ($action_iframe == 'import-layers') {
			/**********************************
			*      import form layers        *
			**********************************/
			echo '<table><tr><td><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-import.png" width="32" height="32" alt="import"></td>';
			echo '<td><h1 style="font-size:20px;margin:0px;"> ' . __('prepare import','lmm') . ' (' . __('layers','lmm') . ')</h1></td></tr></table>';
			echo '
			<script>
			function lmm_check_file_extension()	{
				str=document.getElementById("import-file").value.toUpperCase();
				suffix=".CSV";
				suffix2=".XLS";
				suffix3=".XLSX";
				suffix4=".ODS";
				if(!(str.indexOf(suffix, str.length - suffix.length) !== -1
							|| str.indexOf(suffix2, str.length - suffix2.length) !== -1
							|| str.indexOf(suffix3, str.length - suffix3.length) !== -1
							|| str.indexOf(suffix4, str.length - suffix4.length) !== -1)
					){
					alert("' . sprintf(esc_attr__('Error: file type not allowed - allowed file types: %1$s','lmm'), 'csv, xls, xlsx, ods') . '");
					document.getElementById("import-file").value="";
				}
			}
			</script>
			
			<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" target="_top">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>

			<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action_standalone" value="import-layers" />
			<table>
				<tr>
					<td colspan="2">
						' . sprintf(__('For details and tutorials about imports and exports, please visit %1s','lmm'), '<a href="http://www.mapsmarker.com/import-export" target="_blank" style="text-decoration:none;">www.mapsmarker.com/import-export</a>') . '
						<ul>
							<li>' . __('Download import template files','lmm') . ': ';
							if (extension_loaded('zip')) {
								echo '<a href="http://www.mapsmarker.com/import-template-layers-xlsx" target="_blank">.xlsx (Excel2007)</a>, <a href="http://www.mapsmarker.com/import-template-layers-xls" target="_blank">.xls (Excel5)</a>, <a href="http://www.mapsmarker.com/import-template-layers-ods" target="_blank">.ods (OpenOffice/LibreOffice)</a>, <a href="http://www.mapsmarker.com/import-template-layers-csv" target="_blank">.csv</a><br/>';
							} else {
								echo '<a href="http://www.mapsmarker.com/import-template-layers-xls" target="_blank">.xls (Excel5)</a>, <a href="http://www.mapsmarker.com/import-template-layers-csv" target="_blank">.csv</a>';
							}
							echo '</li>
							<li>' . __('If you want to bulk update existing layers, please make an export first!','lmm') . '</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Please select import file','lmm') . '</td>
					<td>
						<input id="import-file" name="import-file" type="file" size="50" onchange="lmm_check_file_extension()" disabled="disabled" /><br/>';
						if (extension_loaded('zip')) {
							echo sprintf(__('supported formats: %1$s','lmm'), 'xlsx, xls, ods, csv') . ' ' . __('(with semicolons as delimiters)','lmm');
						} else {
							echo sprintf(__('supported formats: %1$s','lmm'), 'xls, csv') . ' ' . __('(with semicolons as delimiters)','lmm') . '<br/>';
							echo ' <span style="background:yellow;padding:2px;">' . __('The PHP extension php_zip is not enabled on your server - this means that .xlsx or .ods files cannot be handled. Please contact your admin for more details.','lmm') . '</span>';
						}
					echo '</td>
				</tr>
				<tr>
					<td valign="top">' . __('Import mode','lmm') . '</td>
					<td>
						<input id="import-mode-add" type="radio" name="import-mode" value="import-mode-add" checked="checked" disabled="disabled" /> <label for="import-mode-add"> ' . __('bulk additions (add new markers)','lmm') . '</label><br/>
						<input id="import-mode-update" type="radio" name="import-mode" value="import-mode-update" disabled="disabled" /> <label for="import-mode-update"> ' . __('bulk updates (update existing markers)','lmm') . '</label><br/>
						<p style="margin:5px 0 0 24px;">' . __('Please note: values in the column ID from import file will be ignored on bulk additions but are needed on bulk updates!','lmm') . '</p>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which geocoding option should be used?','lmm') . '</td>
					<td>
						<input id="geocoding-on" type="radio" name="geocoding-option" value="geocoding-on" selected="selected" ' . $geocoding_radio_button_on . ' disabled="disabled"/> <label for="geocoding-on"> ' . __('use address for geocoding (latitude and longitude values will get overwritten by geocoding results)','lmm') . '</label>' . $geocoding_provider . '
						<input id="geocoding-off" type="radio" name="geocoding-option" value="geocoding-off" ' . $geocoding_radio_button_off . ' disabled="disabled"/> <label for="geocoding-off"> ' . __('do not use address for geocoding (address, latitude and longitude values will be imported as given)','lmm') . '</label><br/>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which audit option should be used?','lmm') . '</td>
					<td>
						<input id="audit-on" type="radio" name="audit-option" value="audit-on" checked="checked" disabled="disabled" /> <label for="audit-on"> ' . sprintf(__('use current userlogin (%1$s) and current timestamp for createdby/createdon on new layers and updatedby/updatedon on layer updates','lmm'), $current_user->user_login) . '</label><br/>
						<input id="audit-off" type="radio" name="audit-option" value="audit-off" disabled="disabled" /> <label for="audit-off"> ' . __('import values for createdby/createdon and updatedby/updatedon as given (no changes will be made to import file)','lmm') . '</label>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which caching method should be used?','lmm') . '</td>
					<td>
						<input id="caching-auto" type="radio" name="caching-method" value="auto" checked="checked" disabled="disabled" /> <label for="caching-auto">' . __('automatic','lmm') . '</label>

						<a href="#" id="show-more-link" onclick="document.getElementById(\'caching-options-more\').style.display = \'block\';document.getElementById(\'show-more-link\').style.display = \'none\';"> - ' . __('show more options','lmm') . '</a>
						<div id="caching-options-more" style="display:none;">
						<span ' . $caching_sqlite2_disabled_css . '><input id="caching-sqlite2" type="radio" name="caching-method" value="sqlite2" ' . $caching_sqlite2_disabled . ' disabled="disabled" /> <label for="caching-sqlite2">SQLite2 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('low','lmm')) . ')</label></span><br/>

						<span ' . $caching_sqlite3_disabled_css . '><input id="caching-sqlite3" type="radio" name="caching-method" value="sqlite3" ' . $caching_sqlite3_disabled . ' disabled="disabled" /> <label for="caching-sqlite3">SQLite3 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('very low','lmm')) . ')</label></span><br/>
						<span ' . $caching_apc_disabled_css . '><input id="caching-apc" type="radio" name="caching-method" value="apc" ' . $caching_apc_disabled . ' disabled="disabled" /> <label for="caching-apc">APC <a href="http://pecl.php.net/package/APC" title="http://pecl.php.net/package/APC" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-apc-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-apc-timeout" type="input" name="caching-apc-timeout" value="600" style="width:30px;" ' . $caching_apc_disabled . ' disabled="disabled" /></label></span><br/>

						<span ' . $caching_wincache_disabled_css . '><input id="caching-wincache" type="radio" name="caching-method" value="wincache" ' . $caching_wincache_disabled . ' disabled="disabled" /> <label for="caching-wincache">Wincache <a href="http://sourceforge.net/projects/wincache/" title="http://sourceforge.net/projects/wincache/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-wincache-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-wincache-timeout" type="input" name="caching-wincache-timeout" value="600" style="width:31px;" ' . $caching_wincache_disabled . ' disabled="disabled" /></label></span><br/>

						<span ' . $caching_memorygzip_disabled_css . '><input id="caching-memorygzip" type="radio" name="caching-method" value="memorygzip" ' . $caching_memorygzip_disabled . ' disabled="disabled" /> <label for="caching-memorygzip">MemoryGZIP (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label></span><br/>

						<span ' . $caching_discisam_disabled_css . '><input id="caching-discisam" type="radio" name="caching-method" value="discisam" ' . $caching_discisam_disabled . ' disabled="disabled" /> <label for="caching-discisam">DiscISAM (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')' . $caching_discisam_disabled . '</label><br/>
						<label for="caching-discisam-directory" style="margin-left:24px;">' . __('optional - use the following custom directory for temp files','lmm') . '</label>:<br/>
						<input style="margin-left:24px;width:300px;" id="caching-discisam-directory" type="input" name="caching-discisam-directory" value="" ' . $caching_discisam_disabled . ' disabled="disabled" /></label></span><br/>

						<span ' . $caching_phptemp_disabled_css . '><input id="caching-phptemp" type="radio" name="caching-method" value="phptemp" ' . $caching_phptemp_disabled . ' disabled="disabled" /> <label for="caching-phptemp">phpTemp ' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label><br/>
						<label for="caching-phptemp-filesize" style="margin-left:24px;">' . __('maximum temporary file size in MB','lmm') . ' </label> <input id="caching-phptemp-filesize" type="input" name="caching-phptemp-filesize" value="8" style="width:30px;" ' . $caching_phptemp_disabled . ' disabled="disabled" /></label></span><br/>

						<span ' . $caching_igbinary_disabled_css . '><input id="caching-igbinary" type="radio" name="caching-method" value="igbinary" ' . $caching_igbinary_disabled . ' disabled="disabled" /> <label for="caching-igbinary">igbinary <a href="http://pecl.php.net/package/igbinary" title="http://pecl.php.net/package/igbinary" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('high','lmm')) . ')</label></span><br/>

						<span ' . $caching_memoryserialized_disabled_css . '><input id="caching-memoryserialized" type="radio" name="caching-method" value="memoryserialized" ' . $caching_memoryserialized_disabled . ' disabled="disabled" /> <label for="caching-memoryserialized">Memory serialized (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('high','lmm'), __('high','lmm')) . ')' . $caching_memoryserialized_disabled . '</label></span><br/>

						<input id="caching-memory" type="radio" name="caching-method" value="memory" disabled="disabled" /> <label for="caching-memory">Memory <a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" title="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very high','lmm'), __('very high','lmm')) . ')</label><br/>

						<input type="checkbox" name="setReadDataOnly" id="setReadDataOnly" disabled="disabled" /> <label for="setReadDataOnly"> ' . __('further reduce memory usage for xlsx/xls/ods input files by only importing linktext for hyperlinks','lmm') . '</a>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Test mode','lmm') . '</td>
					<td>
						<input id="test-mode-on" type="radio" name="test-mode" value="test-mode-on" checked="checked" disabled="disabled" /> <label for="test-mode-on"> ' . __('on (check import file only - no changes will be made to database)','lmm') . '</label><br/>
						<input id="test-mode-off" type="radio" name="test-mode" value="test-mode-off" disabled="disabled" /> <label for="test-mode-off"> ' . __('off (save changes to database)','lmm') . '</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input style="font-weight:bold;" type="submit" name="submit" class="submit button-primary" value="' . esc_attr__('start import','lmm') . '" disabled="disabled" />
						<br/><br/>
						<a href="javascript:history.back();">' . __('or back to overview','lmm') . '</a>
					</td>
				</tr>
			</table>
			</form>';
		//info: end ($action_iframe == 'import-layers')	
		} else if ($action_iframe == 'export') {
			/**********************************
			*         export form             *
			**********************************/
			echo '<table><tr><td><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-export.png" width="32" height="32" alt="export"></td>';
			echo '<td><h1 style="font-size:20px;margin:0px;"> ' . __('prepare export','lmm') . '</h1></td></tr></table>';
			$layerlist = $wpdb->get_results('SELECT `id`,`name`,`multi_layer_map` FROM '.$table_name_layers, ARRAY_A);
			$markercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_markers.'');
			$iconlist = $wpdb->get_results('SELECT distinct(icon) FROM '.$table_name_markers, ARRAY_A);

			if (extension_loaded('zip')) {
				$export_disabled = '';
				$export_disabled_info = '';
			} else {
				$export_disabled = 'disabled="disabled"';
				$export_disabled_info = ' <span style="background:yellow;padding:2px;">' . __('The PHP extension php_zip is not enabled on your server - this means that .xlsx or .ods files cannot be handled. Please contact your admin for more details.','lmm') . '</span>';
			}
			echo '
			<form method="post">
			<input type="hidden" name="action_standalone" value="export" />
			<table>
				<tr>
					<td>' . __('Which markers should be selected?','lmm') . '</td>
					<td>
						<select id="filter-layer" name="filter-layer">
						<option value="select-all">' . sprintf(__('all %1$s markers','lmm'), $markercount_all) . '</option>';
						foreach ($layerlist as $row) {
							$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON l.id=m.layer WHERE l.id='.$row['id']);
							if ($row['multi_layer_map'] == 0) {
								echo '<option value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . '>' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('layer','lmm') . ' ID ' . $row['id'] . ' - ' . sprintf(__('%1$s markers','lmm'), $markercount) . ')</option>';
							} else {
								echo '<option title="' . esc_attr__('This is a multi-layer map - markers cannot be exported from this layer directly','lmm') . '" value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . ' disabled="disabled">' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('layer','lmm') . ' ID ' . $row['id'] . '/MLM)</option>';
							}
						}
						echo '
						</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>' . __('How many markers should be exported?','lmm') . '</td>
					<td>
						' . sprintf(__('Please select range - from %1$s to %2$s markers','lmm'), '<input type="text" id="limit-from" name="limit-from" value="0" style="width:50px;" />', '<input type="text" id="limit-to" name="limit-to" value="100" style="width:50px;" />') . '
					</td>
				</tr>
				<tr>
					<td>' . __('Optional 1 - selected markers must have:','lmm') . '</td>
					<td>
						<p style="margin:0;">' . sprintf(__('%1$s in the marker name','lmm'), '<input type="text" id="filter-markername" name="filter-markername" style="width:200px;" />') . '<input id="filter-operator1-and" type="radio" name="filter-operator1" value="AND" checked="checked"/>
						<label for="filter-operator1-and">' . __('and','lmm') . '</label> <input id="filter-operator1-or" type="radio" name="filter-operator1" value="OR" /> <label for="filter-operator1-or">' . __('or','lmm') . '</label>
						' . sprintf(__('%1$s in the popup text','lmm'), ' <input type="text" id="filter-popuptext" name="filter-popuptext" style="width:200px;" />') . '
					</td>
				</tr>
				<tr>
					<td>' . __('Optional 2 - selected markers must NOT have:','lmm') . '</td>
					<td>
						<p style="margin:0;">' . sprintf(__('%1$s in the marker name','lmm'), '<input type="text" id="filter-exclude-markername" name="filter-exclude-markername" style="width:200px;" />') . '<input id="filter-operator2-and" type="radio" name="filter-operator2" value="AND" checked="checked"/>
						<label for="filter-operator2-and">' . __('and','lmm') . '</label> <input id="filter-operator2-or" type="radio" name="filter-operator2" value="OR" /> <label for="filter-operator2-or">' . __('or','lmm') . '</label>
						' . sprintf(__('%1$s in the popup text','lmm'), ' <input type="text" id="filter-exclude-popuptext" name="filter-exclude-popuptext" style="width:200px;" />') . '
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Optional 3 - selected markers must have the following icon:','lmm') . '</td>
					<td>
						<input id="filter-any-icon" type="radio" name="filter-icon" value="icon-any" checked="checked" /> <label for="filter-any-icon">' . __('export markers with any icon','lmm') . '</label> <a href="#" id="show-icons-link" onclick="document.getElementById(\'more-icons\').style.display = \'block\';document.getElementById(\'show-icons-link\').style.display = \'none\';"> - ' . __('show used icons','lmm') . '</a>
						<div id="more-icons" style="display:none;">';
						foreach ($iconlist as $row) {
							if ($row['icon'] == NULL) {
								echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" width="32" height="37" title="' . esc_attr__('filename','lmm') . ': marker.png" alt="default.png" /></label><br/><input id="default_icon" style="margin:1px 0 0 1px;" type="radio" name="filter-icon" value="" /></div>';
							} else {
								echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="'.$row['icon'].'"><img src="' . $defaults_marker_icon_url . '/' . $row['icon'] . '" title="' . esc_attr__('filename','lmm') . ': ' . $row['icon'] . '" alt="' . $row['icon'] . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" /></label><br/><input id="'.$row['icon'].'" style="margin:1px 0 0 1px;" type="radio" name="filter-icon" value="'.$row['icon'].'"/></div>';
							}
						}
					echo '</div>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which export format should be used?','lmm') . '</td>
					<td>';
						//info: needed if excel2007 is not supported
						if ($export_disabled == NULL) {
							$default_export_format_exel2007 = 'checked="checked"';
							$default_export_format_exel5 = '';
						} else {
							$default_export_format_exel2007 = '';
							$default_export_format_exel5 = 'checked="checked"';
						}
					echo '<input id="export-exel2007" type="radio" name="export-format" value="exel2007" ' . $export_disabled . ' ' . $default_export_format_exel2007 . ' /> <label for="export-exel2007">Excel2007 (.xlsx) - ' . sprintf(__('compatible with OpenOffice %1$s and LibreOffice %2$s','lmm'), '3.0+', '3.6+') . '</label> ' . $export_disabled_info . '<br/>
						<input id="export-excel5" type="radio" name="export-format" value="excel5" ' . $default_export_format_exel5 . ' /> <label for="export-excel5">Excel5 (.xls)</label><br/>
						<input id="export-ods" type="radio" name="export-format" value="ods" ' . $export_disabled . ' /> <label for="export-ods">' . __('OpenDocument Spreadsheet','lmm') . ' (.ods)</label><br/>
						<input id="export-csv" type="radio" name="export-format" value="csv" /> <label for="export-csv">CSV (.csv)</label>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which caching method should be used?','lmm') . '</td>
					<td>
						<input id="caching-auto" type="radio" name="caching-method" value="auto" checked="checked" /> <label for="caching-auto">' . __('automatic','lmm') . '</label>

						<a href="#" id="show-more-link" onclick="document.getElementById(\'caching-options-more\').style.display = \'block\';document.getElementById(\'show-more-link\').style.display = \'none\';"> - ' . __('show more options','lmm') . '</a>
						<div id="caching-options-more" style="display:none;">
						<span ' . $caching_sqlite2_disabled_css . '><input id="caching-sqlite2" type="radio" name="caching-method" value="sqlite2" ' . $caching_sqlite2_disabled . ' /> <label for="caching-sqlite2">SQLite2 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('low','lmm')) . ')</label></span><br/>

						<span ' . $caching_sqlite3_disabled_css . '><input id="caching-sqlite3" type="radio" name="caching-method" value="sqlite3" ' . $caching_sqlite3_disabled . ' /> <label for="caching-sqlite3">SQLite3 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('very low','lmm')) . ')</label></span><br/>
						<span ' . $caching_apc_disabled_css . '><input id="caching-apc" type="radio" name="caching-method" value="apc" ' . $caching_apc_disabled . ' /> <label for="caching-apc">APC <a href="http://pecl.php.net/package/APC" title="http://pecl.php.net/package/APC" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-apc-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-apc-timeout" type="input" name="caching-apc-timeout" value="600" style="width:30px;" ' . $caching_apc_disabled . ' /></label></span><br/>

						<span ' . $caching_wincache_disabled_css . '><input id="caching-wincache" type="radio" name="caching-method" value="wincache" ' . $caching_wincache_disabled . ' /> <label for="caching-wincache">Wincache <a href="http://sourceforge.net/projects/wincache/" title="http://sourceforge.net/projects/wincache/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-wincache-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-wincache-timeout" type="input" name="caching-wincache-timeout" value="600" style="width:31px;" ' . $caching_wincache_disabled . ' /></label></span><br/>

						<span ' . $caching_memorygzip_disabled_css . '><input id="caching-memorygzip" type="radio" name="caching-method" value="memorygzip" ' . $caching_memorygzip_disabled . ' /> <label for="caching-memorygzip">MemoryGZIP (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label></span><br/>

						<span ' . $caching_discisam_disabled_css . '><input id="caching-discisam" type="radio" name="caching-method" value="discisam" ' . $caching_discisam_disabled . ' /> <label for="caching-discisam">DiscISAM (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')' . $caching_discisam_disabled . '</label><br/>
						<label for="caching-discisam-directory" style="margin-left:24px;">' . __('optional - use the following custom directory for temp files','lmm') . '</label>:<br/>
						<input style="margin-left:24px;width:300px;" id="caching-discisam-directory" type="input" name="caching-discisam-directory" value="" ' . $caching_discisam_disabled . ' /></label></span><br/>

						<span ' . $caching_phptemp_disabled_css . '><input id="caching-phptemp" type="radio" name="caching-method" value="phptemp" ' . $caching_phptemp_disabled . ' /> <label for="caching-phptemp">phpTemp ' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label><br/>
						<label for="caching-phptemp-filesize" style="margin-left:24px;">' . __('maximum temporary file size in MB','lmm') . ' </label> <input id="caching-phptemp-filesize" type="input" name="caching-phptemp-filesize" value="8" style="width:30px;" ' . $caching_phptemp_disabled . ' /></label></span><br/>

						<span ' . $caching_igbinary_disabled_css . '><input id="caching-igbinary" type="radio" name="caching-method" value="igbinary" ' . $caching_igbinary_disabled . ' /> <label for="caching-igbinary">igbinary <a href="http://pecl.php.net/package/igbinary" title="http://pecl.php.net/package/igbinary" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('high','lmm')) . ')</label></span><br/>

						<span ' . $caching_memoryserialized_disabled_css . '><input id="caching-memoryserialized" type="radio" name="caching-method" value="memoryserialized" ' . $caching_memoryserialized_disabled . ' /> <label for="caching-memoryserialized">Memory serialized (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('high','lmm'), __('high','lmm')) . ')' . $caching_memoryserialized_disabled . '</label></span><br/>

						<input id="caching-memory" type="radio" name="caching-method" value="memory" /> <label for="caching-memory">Memory <a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" title="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very high','lmm'), __('very high','lmm')) . ')</label>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input style="font-weight:bold;" type="submit" name="submit" class="submit button-primary" value="' . esc_attr__('start export','lmm') . '" />
						<br/><br/>
						<a href="javascript:history.back();">' . __('or back to overview','lmm') . '
					</td>
				</tr>
			</table>
			</form>';
		//info: ($action_iframe == 'import')
		} else if ($action_iframe == 'export-layers') {
			/**********************************
			*      export form layers        *
			**********************************/
			echo '<table><tr><td><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-export.png" width="32" height="32" alt="export"></td>';
			echo '<td><h1 style="font-size:20px;margin:0px;"> ' . __('prepare export','lmm') . ' (' . __('layers','lmm') . ')</h1></td></tr></table>';
			$layerlist = $wpdb->get_results('SELECT `id`,`name`,`multi_layer_map` FROM '.$table_name_layers, ARRAY_A);
			$layercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.'') - 1;
			
			if (extension_loaded('zip')) {
				$export_disabled = '';
				$export_disabled_info = '';
			} else {
				$export_disabled = 'disabled="disabled"';
				$export_disabled_info = ' <span style="background:yellow;padding:2px;">' . __('The PHP extension php_zip is not enabled on your server - this means that .xlsx or .ods files cannot be handled. Please contact your admin for more details.','lmm') . '</span>';
			}
			echo '<p>' . __('Please keep in mind that you can only export layer maps here - if you also want to export the assigned markers, please also use the function "export markers"!','lmm') . '</p>';
			echo '
			<form method="post">
			<input type="hidden" name="action_standalone" value="export-layers" />
			<table>
				<tr>
					<td>' . __('Which layers should be selected?','lmm') . '</td>
					<td>
						<select id="filter-layer" name="filter-layer">
						<option value="select-all">' . sprintf(__('all %1$s layers','lmm'), $layercount_all) . '</option>';
						foreach ($layerlist as $row) {
							if ($row['id'] != 0) {
								echo '<option value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . '>' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('layer','lmm') . ' ID ' . $row['id'] . ')</option>';
							}
						}
						echo '
						</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>' . __('How many layers should be exported?','lmm') . '</td>
					<td><input type="text" id="limit-to" name="limit-to" value="' . $layercount_all . '" style="width:31px;" /> ' . __('layers','lmm') . '</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which export format should be used?','lmm') . '</td>
					<td>';
						//info: needed if excel2007 is not supported
						if ($export_disabled == NULL) {
							$default_export_format_exel2007 = 'checked="checked"';
							$default_export_format_exel5 = '';
						} else {
							$default_export_format_exel2007 = '';
							$default_export_format_exel5 = 'checked="checked"';
						}
					echo '<input id="export-exel2007" type="radio" name="export-format" value="exel2007" ' . $export_disabled . ' ' . $default_export_format_exel2007 . ' /> <label for="export-exel2007">Excel2007 (.xlsx) - ' . sprintf(__('compatible with OpenOffice %1$s and LibreOffice %2$s','lmm'), '3.0+', '3.6+') . '</label> ' . $export_disabled_info . '<br/>
						<input id="export-excel5" type="radio" name="export-format" value="excel5" ' . $default_export_format_exel5 . ' /> <label for="export-excel5">Excel5 (.xls)</label><br/>
						<input id="export-ods" type="radio" name="export-format" value="ods" ' . $export_disabled . ' /> <label for="export-ods">' . __('OpenDocument Spreadsheet','lmm') . ' (.ods)</label><br/>
						<input id="export-csv" type="radio" name="export-format" value="csv" /> <label for="export-csv">CSV (.csv)</label>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which caching method should be used?','lmm') . '</td>
					<td>
						<input id="caching-auto" type="radio" name="caching-method" value="auto" checked="checked" /> <label for="caching-auto">' . __('automatic','lmm') . '</label>

						<a href="#" id="show-more-link" onclick="document.getElementById(\'caching-options-more\').style.display = \'block\';document.getElementById(\'show-more-link\').style.display = \'none\';"> - ' . __('show more options','lmm') . '</a>
						<div id="caching-options-more" style="display:none;">
						<span ' . $caching_sqlite2_disabled_css . '><input id="caching-sqlite2" type="radio" name="caching-method" value="sqlite2" ' . $caching_sqlite2_disabled . ' /> <label for="caching-sqlite2">SQLite2 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('low','lmm')) . ')</label></span><br/>

						<span ' . $caching_sqlite3_disabled_css . '><input id="caching-sqlite3" type="radio" name="caching-method" value="sqlite3" ' . $caching_sqlite3_disabled . ' /> <label for="caching-sqlite3">SQLite3 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('very low','lmm')) . ')</label></span><br/>
						<span ' . $caching_apc_disabled_css . '><input id="caching-apc" type="radio" name="caching-method" value="apc" ' . $caching_apc_disabled . ' /> <label for="caching-apc">APC <a href="http://pecl.php.net/package/APC" title="http://pecl.php.net/package/APC" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-apc-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-apc-timeout" type="input" name="caching-apc-timeout" value="600" style="width:30px;" ' . $caching_apc_disabled . ' /></label></span><br/>

						<span ' . $caching_wincache_disabled_css . '><input id="caching-wincache" type="radio" name="caching-method" value="wincache" ' . $caching_wincache_disabled . ' /> <label for="caching-wincache">Wincache <a href="http://sourceforge.net/projects/wincache/" title="http://sourceforge.net/projects/wincache/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-wincache-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-wincache-timeout" type="input" name="caching-wincache-timeout" value="600" style="width:31px;" ' . $caching_wincache_disabled . ' /></label></span><br/>

						<span ' . $caching_memorygzip_disabled_css . '><input id="caching-memorygzip" type="radio" name="caching-method" value="memorygzip" ' . $caching_memorygzip_disabled . ' /> <label for="caching-memorygzip">MemoryGZIP (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label></span><br/>

						<span ' . $caching_discisam_disabled_css . '><input id="caching-discisam" type="radio" name="caching-method" value="discisam" ' . $caching_discisam_disabled . ' /> <label for="caching-discisam">DiscISAM (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')' . $caching_discisam_disabled . '</label><br/>
						<label for="caching-discisam-directory" style="margin-left:24px;">' . __('optional - use the following custom directory for temp files','lmm') . '</label>:<br/>
						<input style="margin-left:24px;width:300px;" id="caching-discisam-directory" type="input" name="caching-discisam-directory" value="" ' . $caching_discisam_disabled . ' /></label></span><br/>

						<span ' . $caching_phptemp_disabled_css . '><input id="caching-phptemp" type="radio" name="caching-method" value="phptemp" ' . $caching_phptemp_disabled . ' /> <label for="caching-phptemp">phpTemp ' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label><br/>
						<label for="caching-phptemp-filesize" style="margin-left:24px;">' . __('maximum temporary file size in MB','lmm') . ' </label> <input id="caching-phptemp-filesize" type="input" name="caching-phptemp-filesize" value="8" style="width:30px;" ' . $caching_phptemp_disabled . ' /></label></span><br/>

						<span ' . $caching_igbinary_disabled_css . '><input id="caching-igbinary" type="radio" name="caching-method" value="igbinary" ' . $caching_igbinary_disabled . ' /> <label for="caching-igbinary">igbinary <a href="http://pecl.php.net/package/igbinary" title="http://pecl.php.net/package/igbinary" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('high','lmm')) . ')</label></span><br/>

						<span ' . $caching_memoryserialized_disabled_css . '><input id="caching-memoryserialized" type="radio" name="caching-method" value="memoryserialized" ' . $caching_memoryserialized_disabled . ' /> <label for="caching-memoryserialized">Memory serialized (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('high','lmm'), __('high','lmm')) . ')' . $caching_memoryserialized_disabled . '</label></span><br/>

						<input id="caching-memory" type="radio" name="caching-method" value="memory" /> <label for="caching-memory">Memory <a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" title="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very high','lmm'), __('very high','lmm')) . ')</label>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input style="font-weight:bold;" type="submit" name="submit" class="submit button-primary" value="' . esc_attr__('start export','lmm') . '" />
						<br/><br/>
						<a href="javascript:history.back();">' . __('or back to overview','lmm') . '
					</td>
				</tr>
			</table>
			</form>';
		} //info: ($action_iframe == 'export-layers') 
		echo '</p></body></html>';

	//info: end ($action_standalone == NULL)
	} else {
		/**********************************
		*         start action            *
		**********************************/
		//info: start PHPExcel - shared settings for import and export
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		date_default_timezone_set('Europe/London');

		//info: prepare caching - http://phpexcel.codeplex.com/discussions/234150
		$user_cache = $_POST['caching-method'];
		if ($user_cache == 'auto') {
			if (version_compare(phpversion(),"5.6.29","!=")){ //info: https://github.com/PHPOffice/PHPExcel/issues/1085
				if ( function_exists('sqlite_open') ){ //info: SQLite2
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (SQLite2)';
				} else if ( class_exists('SQLite3',FALSE) === TRUE ) { //info:SQLite3
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (SQLite3)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_apc) === TRUE ) { //info: APC
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_apc;
					$cacheSettings = array( 'cacheTime' => 600 );
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$cache_method_for_log = 'automatic (APC)';
				} else if ( function_exists('wincache_ucache_add') ) { //info: Wincache
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_wincache;
					$cacheSettings = array( 'cacheTime' => 600 );
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$cache_method_for_log = 'automatic (Wincache)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip) === TRUE ) { //info: MemoryGZip
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (MemoryGZip)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_discISAM) === TRUE ) { //info: DiscISAM
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (DiscISAM)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp) === TRUE ) { //info: PHPTemp
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
					$cacheSettings = array( 'memoryCacheSize'  => '8MB' );
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$cache_method_for_log = 'automatic (PHPTemp)';
				} else if ( function_exists('igbinary_serialize') ) { //info: Igbinary
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_igbinary;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (Igbinary)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized) === TRUE ) { //info: MemorySerialized
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (MemorySerialized)';
				} else { //info: Cache in Memory
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (Memory)';
				}

			} else { //info: is PHP 5.6.29 = no SQLite2 & SQLite3

				if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_apc) === TRUE ) { //info: APC
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_apc;
					$cacheSettings = array( 'cacheTime' => 600 );
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$cache_method_for_log = 'automatic (APC)';
				} else if ( function_exists('wincache_ucache_add') ) { //info: Wincache
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_wincache;
					$cacheSettings = array( 'cacheTime' => 600 );
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$cache_method_for_log = 'automatic (Wincache)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip) === TRUE ) { //info: MemoryGZip
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (MemoryGZip)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_discISAM) === TRUE ) { //info: DiscISAM
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (DiscISAM)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp) === TRUE ) { //info: PHPTemp
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
					$cacheSettings = array( 'memoryCacheSize'  => '8MB' );
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$cache_method_for_log = 'automatic (PHPTemp)';
				} else if ( function_exists('igbinary_serialize') ) { //info: Igbinary
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_igbinary;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (Igbinary)';
				} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized) === TRUE ) { //info: MemorySerialized
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (MemorySerialized)';
				} else { //info: Cache in Memory
					$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
					$cache_method_for_log = 'automatic (Memory)';
				}
			}
		//info: perpare custom cache selection
		} else if ($user_cache == 'sqlite2') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'SQLite2';
		} else if ($user_cache == 'sqlite3') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'SQLite3';
		} else if ($user_cache == 'apc') {
			$caching_apc_timeout = intval($_POST['caching-apc-timeout']);
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_apc;
			$cacheSettings = array( 'cacheTime' => $caching_apc_timeout );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'APC';
		} else if ($user_cache == 'wincache') {
			$caching_wincache_timeout = intval($_POST['caching-wincache-timeout']);
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_wincache;
			$cacheSettings = array( 'cacheTime' => $caching_wincache_timeout );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'Wincache';
		} else if ($user_cache == 'memorygzip') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'MemoryGZip';
		} else if ($user_cache == 'discisam') {
			$caching_discisam_directory = trim($_POST['caching-discisam-directory']);
			if ($caching_discisam_directory == NULL) {
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			} else {
				$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
				$cacheSettings = array( 'dir'  => $caching_discisam_directory );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			}
			$cache_method_for_log = 'DiscISAM';
		} else if ($user_cache == 'phptemp') {
			$caching_phptemp_filesize = intval($_POST['caching-phptemp-filesize']) . 'MB';
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			$cacheSettings = array( 'memoryCacheSize'  => $caching_phptemp_filesize );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'PHPTemp';
		} else if ($user_cache == 'igbinary') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_igbinary;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'Igbinary';
		} else if ($user_cache == 'memoryserialized') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'MemorySerialized';
		} else if ($user_cache == 'memory') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
			$cache_method_for_log = 'Memory';
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
		}
		$objPHPExcel = new PHPExcel();

		if ($action_standalone == 'export') {
			/**********************************
			*       export action             *
			**********************************/
			//info: prepare sql for layer filter
			if ( $_POST['filter-layer'] == 'select-all' ) {
				$filter_layer_sql = '(1=1)';
			} else {
				$filter_layer_sql = '`layer` = ' . intval($_POST['filter-layer']);
			}
			//info: prepare sql for optional 1
			$filter_option1_sql = '(';
			if ( $_POST['filter-markername'] == NULL ) {
				$filter_option1_sql .= '(1=1)';
			} else {
				$filter_option1_sql .= 'markername LIKE "%' . esc_sql($_POST['filter-markername']) . '%"';
			}
			if ( ($_POST['filter-markername'] == NULL) || ($_POST['filter-popuptext'] == NULL) ) {
					$filter_option1_sql .= ' AND '; //info: otherwise search for popuptext only returns all results
			} else {
					$filter_option1_sql .= ' ' . esc_sql($_POST['filter-operator1']) . ' ';
			}
			if ( $_POST['filter-popuptext'] == NULL ) {
				$filter_option1_sql .= '(1=1)';
			} else {
				$filter_option1_sql .= '`popuptext` LIKE "%' . esc_sql($_POST['filter-popuptext']) . '%"';
			}
			$filter_option1_sql .= ')';
			//info: prepare sql for optional 2
			$filter_option2_sql = '(';
			if ( $_POST['filter-exclude-markername'] == NULL ) {
				$filter_option2_sql .= '(1=1)';
			} else {
				$filter_option2_sql .= '`markername` NOT LIKE "%' . esc_sql($_POST['filter-exclude-markername']) . '%"';
			}
			if ( ($_POST['filter-exclude-markername'] == NULL) || ($_POST['filter-exclude-popuptext'] == NULL) ) {
					$filter_option2_sql .= ' AND '; //info: otherwise search for popuptext only returns all results
			} else {
					$filter_option2_sql .= ' ' . esc_sql($_POST['filter-operator2']) . ' ';
			}
			if ( $_POST['filter-exclude-popuptext'] == NULL ) {
				$filter_option2_sql .= '(1=1)';
			} else {
				$filter_option2_sql .= '`popuptext` NOT LIKE "%' . esc_sql($_POST['filter-exclude-popuptext']) . '%"';
			}
			$filter_option2_sql .= ')';
			//info: filter for marker icons
			if ( $_POST['filter-icon'] == 'icon-any' ) {
				$filter_icons_sql = '(1=1)';
			} else if ( $_POST['filter-icon'] == 'icon-any' ) {
				$filter_icons_sql = '(`icon` = "")';
			} else {
				$filter_icons_sql = '(`icon` = "' . esc_sql($_POST['filter-icon']) . '")';
			}
			$filter_limit_from = intval($_POST['limit-from']);
			$filter_limit_to = intval($_POST['limit-to']);
			$export_rows = $wpdb->get_results("SELECT * FROM `$table_name_markers` WHERE $filter_layer_sql AND $filter_option1_sql AND $filter_option2_sql AND $filter_icons_sql LIMIT $filter_limit_from, $filter_limit_to", ARRAY_A);

			//info: set document properties
			global $user;
			$objPHPExcel->getProperties()->setCreator("$current_user->user_login")
								 ->setLastModifiedBy("$current_user->user_login")
								 ->setTitle("MapsMarker Export")
								 ->setDescription("Marker export created with MapsMarker (http://www.mapsmarker.com), using PHPExcel (http://phpexcel.codeplex.com)")
								 ->setKeywords("MapsMarker PHPExcel");

			 //info: rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('MapsMarker-Export');
			//info: set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			//info: activate autofilter
			$objPHPExcel->getActiveSheet()->setAutoFilter('A1:AK1');

			//info: add header data
			$headings = array('id','markername','popuptext','openpopup','address','lat','lon','layer','zoom','icon','mapwidth','mapwidthunit','mapheight','basemap','panel','controlbox','createdby','createdon','updatedby','updatedon','kml_timestamp','overlays_custom','overlays_custom2','overlays_custom3','overlays_custom4','wms','wms2','wms3','wms4','wms5','wms6','wms7','wms8','wms9','wms10','gpx_url','gpx_panel');
			$rowNumber = 1;
			$col = 'A';
			foreach($headings as $heading) {
			   $objPHPExcel->getActiveSheet()->setCellValue($col.$rowNumber,$heading);
			   $col++;
			}
			$rowNumber = 2;
			$array_count_total = count($export_rows);
			$array_count_current = 0;

			$export_format = $_POST['export-format'];

			while ($array_count_current < $array_count_total) {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNumber,intval($export_rows[$array_count_current]['id']));
				if ($export_format != 'csv') {
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber)->getAlignment()->setWrapText(true);
				}
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber,stripslashes(sanitize_excel($export_rows[$array_count_current]['markername'])));
				if ($export_format == 'csv') {
					$popuptext_prepare_escape1 = preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$export_rows[$array_count_current]['popuptext']);
					$popuptext_prepare_escape2 = str_replace("'", "'", $popuptext_prepare_escape1);
					$popuptext_prepare_escape3 = str_replace('"', '\'', $popuptext_prepare_escape2);
					$popuptext_escaped = $popuptext_prepare_escape3;
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber,sanitize_excel($popuptext_escaped));
				} else {
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rowNumber)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber,stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',sanitize_excel($export_rows[$array_count_current]['popuptext']))));
				}
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['openpopup']));
				if ($export_format == 'csv') {
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['address']));
				} else {
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rowNumber)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber,stripslashes(sanitize_excel($export_rows[$array_count_current]['address'])));
				}
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowNumber,floatval($export_rows[$array_count_current]['lat']));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rowNumber,floatval($export_rows[$array_count_current]['lon']));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$rowNumber,intval($export_rows[$array_count_current]['layer']));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$rowNumber,intval($export_rows[$array_count_current]['zoom']));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['icon']));
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rowNumber,intval($export_rows[$array_count_current]['mapwidth']));
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['mapwidthunit']));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber,intval($export_rows[$array_count_current]['mapheight']));
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['basemap']));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$rowNumber,intval($export_rows[$array_count_current]['panel']));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$rowNumber,intval($export_rows[$array_count_current]['controlbox']));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['createdby']));
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['createdon']));
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['updatedby']));
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['updatedon']));
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['kml_timestamp']));
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom']));
				$objPHPExcel->getActiveSheet()->setCellValue('W'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom2']));
				$objPHPExcel->getActiveSheet()->setCellValue('X'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom3']));
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom4']));
				$objPHPExcel->getActiveSheet()->setCellValue('Z'.$rowNumber,intval($export_rows[$array_count_current]['wms']));
				$objPHPExcel->getActiveSheet()->setCellValue('AA'.$rowNumber,intval($export_rows[$array_count_current]['wms2']));
				$objPHPExcel->getActiveSheet()->setCellValue('AB'.$rowNumber,intval($export_rows[$array_count_current]['wms3']));
				$objPHPExcel->getActiveSheet()->setCellValue('AC'.$rowNumber,intval($export_rows[$array_count_current]['wms4']));
				$objPHPExcel->getActiveSheet()->setCellValue('AD'.$rowNumber,intval($export_rows[$array_count_current]['wms5']));
				$objPHPExcel->getActiveSheet()->setCellValue('AE'.$rowNumber,intval($export_rows[$array_count_current]['wms6']));
				$objPHPExcel->getActiveSheet()->setCellValue('AF'.$rowNumber,intval($export_rows[$array_count_current]['wms7']));
				$objPHPExcel->getActiveSheet()->setCellValue('AG'.$rowNumber,intval($export_rows[$array_count_current]['wms8']));
				$objPHPExcel->getActiveSheet()->setCellValue('AH'.$rowNumber,intval($export_rows[$array_count_current]['wms9']));
				$objPHPExcel->getActiveSheet()->setCellValue('AI'.$rowNumber,intval($export_rows[$array_count_current]['wms10']));
				$objPHPExcel->getActiveSheet()->setCellValue('AJ'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['gpx_url']));
				$objPHPExcel->getActiveSheet()->setCellValue('AK'.$rowNumber,intval($export_rows[$array_count_current]['gpx_panel']));
				$rowNumber++;
				$array_count_current++;
			}

			//info: freeze pane so that the heading line will not scroll
			$objPHPExcel->getActiveSheet()->freezePane('A2');

			//info: set column widths
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(13);

			//info: prepare output file
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/download");
			header("Content-Type: application/octet-stream");
			$filename = 'MapsMarkerPro-Export-' . date("Y-m-d_H-i");

			$export_format = $_POST['export-format'];
			if ($export_format == 'csv') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
				$objWriter->setDelimiter(';');
				header("Content-Type: text/csv");
				header("Content-Disposition: attachment;filename=" . $filename . ".csv");
				header("Content-Transfer-Encoding: binary ");
			} else if ($export_format == 'excel5') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				header("Content-Transfer-Encoding: binary ");
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment;filename=" . $filename . ".xls");
			} else if ($export_format == 'exel2007') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header("Content-Disposition: attachment;filename=" . $filename . ".xlsx");
				header("Content-Transfer-Encoding: binary ");
			} else if ($export_format == 'ods') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
				header("Content-Type: application/vnd.oasis.opendocument.spreadsheet");
				header("Content-Disposition: attachment;filename=" . $filename . ".ods");
				header("Content-Transfer-Encoding: binary ");
			}
			$objWriter->save('php://output');

			//info: cleanup to free memory
			unset($export_rows);
			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);
		//info: end (action_standalone == export)
		} else if ($action_standalone == 'export-layers') {
			/**********************************
			*      export action layer        *
			**********************************/
			//info: prepare sql for layer filter
			if ( $_POST['filter-layer'] == 'select-all' ) {
				$filter_layer_sql = '(1=1)';
			} else {
				$filter_layer_sql = '`id` = ' . intval($_POST['filter-layer']);
			}
			$filter_limit_to = intval($_POST['limit-to']);
			$export_rows = $wpdb->get_results("SELECT * FROM `$table_name_layers` WHERE $filter_layer_sql AND `id` != 0 LIMIT 0, $filter_limit_to", ARRAY_A);

			//info: set document properties
			global $user;
			$objPHPExcel->getProperties()->setCreator("$current_user->user_login")
								 ->setLastModifiedBy("$current_user->user_login")
								 ->setTitle("MapsMarker Export Layers")
								 ->setDescription("Marker export created with MapsMarkerPro (http://www.mapsmarker.com), using PHPExcel (http://phpexcel.codeplex.com)")
								 ->setKeywords("MapsMarker PHPExcel");

			 //info: rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('MapsMarker-Export-Layers');
			//info: set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			//info: activate autofilter
			$objPHPExcel->getActiveSheet()->setAutoFilter('A1:AJ1');

			//info: add header data
			$headings = array('id','name','address','layerviewlat','layerviewlon','layerzoom','mapwidth','mapwidthunit','mapheight','basemap','panel','clustering','listmarkers','multi_layer_map','multi_layer_map_list','controlbox','createdby','createdon','updatedby','updatedon','overlays_custom','overlays_custom2','overlays_custom3','overlays_custom4','wms','wms2','wms3','wms4','wms5','wms6','wms7','wms8','wms9','wms10','gpx_url','gpx_panel');
			$rowNumber = 1;
			$col = 'A';
			foreach($headings as $heading) {
			   $objPHPExcel->getActiveSheet()->setCellValue($col.$rowNumber,$heading);
			   $col++;
			}
			$rowNumber = 2;
			$array_count_total = count($export_rows);
			$array_count_current = 0;

			$export_format = $_POST['export-format'];

			while ($array_count_current < $array_count_total) {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNumber,intval($export_rows[$array_count_current]['id']));
				if ($export_format != 'csv') {
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber)->getAlignment()->setWrapText(true);
				}
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber,stripslashes(sanitize_excel($export_rows[$array_count_current]['name'])));
				if ($export_format == 'csv') {
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['address']));
				} else {
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rowNumber)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber,stripslashes(sanitize_excel($export_rows[$array_count_current]['address'])));
				}
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rowNumber,floatval($export_rows[$array_count_current]['layerviewlat']));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber,floatval($export_rows[$array_count_current]['layerviewlon']));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowNumber,intval($export_rows[$array_count_current]['layerzoom']));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rowNumber,intval($export_rows[$array_count_current]['mapwidth']));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['mapwidthunit']));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$rowNumber,intval($export_rows[$array_count_current]['mapheight']));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['basemap']));
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rowNumber,intval($export_rows[$array_count_current]['panel']));
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$rowNumber,intval($export_rows[$array_count_current]['clustering']));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber,intval($export_rows[$array_count_current]['listmarkers']));
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$rowNumber,intval($export_rows[$array_count_current]['multi_layer_map']));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['multi_layer_map_list']));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$rowNumber,intval($export_rows[$array_count_current]['controlbox']));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['createdby']));
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['createdon']));
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['updatedby']));
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['updatedon']));
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom']));
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom2']));
				$objPHPExcel->getActiveSheet()->setCellValue('W'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom3']));
				$objPHPExcel->getActiveSheet()->setCellValue('X'.$rowNumber,intval($export_rows[$array_count_current]['overlays_custom4']));
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.$rowNumber,intval($export_rows[$array_count_current]['wms']));
				$objPHPExcel->getActiveSheet()->setCellValue('Z'.$rowNumber,intval($export_rows[$array_count_current]['wms2']));
				$objPHPExcel->getActiveSheet()->setCellValue('AA'.$rowNumber,intval($export_rows[$array_count_current]['wms3']));
				$objPHPExcel->getActiveSheet()->setCellValue('AB'.$rowNumber,intval($export_rows[$array_count_current]['wms4']));
				$objPHPExcel->getActiveSheet()->setCellValue('AC'.$rowNumber,intval($export_rows[$array_count_current]['wms5']));
				$objPHPExcel->getActiveSheet()->setCellValue('AD'.$rowNumber,intval($export_rows[$array_count_current]['wms6']));
				$objPHPExcel->getActiveSheet()->setCellValue('AE'.$rowNumber,intval($export_rows[$array_count_current]['wms7']));
				$objPHPExcel->getActiveSheet()->setCellValue('AF'.$rowNumber,intval($export_rows[$array_count_current]['wms8']));
				$objPHPExcel->getActiveSheet()->setCellValue('AG'.$rowNumber,intval($export_rows[$array_count_current]['wms9']));
				$objPHPExcel->getActiveSheet()->setCellValue('AH'.$rowNumber,intval($export_rows[$array_count_current]['wms10']));
				$objPHPExcel->getActiveSheet()->setCellValue('AI'.$rowNumber,sanitize_excel($export_rows[$array_count_current]['gpx_url']));
				$objPHPExcel->getActiveSheet()->setCellValue('AJ'.$rowNumber,intval($export_rows[$array_count_current]['gpx_panel']));
				$rowNumber++;
				$array_count_current++;
			}

			//info: freeze pane so that the heading line will not scroll
			$objPHPExcel->getActiveSheet()->freezePane('A2');

			//info: set column widths
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(13);

			//info: prepare output file
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/download");
			header("Content-Type: application/octet-stream");
			$filename = 'MapsMarkerPro-Export-Layers-' . date("Y-m-d_H-i");

			$export_format = $_POST['export-format'];
			if ($export_format == 'csv') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
				$objWriter->setDelimiter(';');
				header("Content-Type: text/csv");
				header("Content-Disposition: attachment;filename=" . $filename . ".csv");
				header("Content-Transfer-Encoding: binary ");
			} else if ($export_format == 'excel5') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				header("Content-Transfer-Encoding: binary ");
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment;filename=" . $filename . ".xls");
			} else if ($export_format == 'exel2007') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header("Content-Disposition: attachment;filename=" . $filename . ".xlsx");
				header("Content-Transfer-Encoding: binary ");
			} else if ($export_format == 'ods') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
				header("Content-Type: application/vnd.oasis.opendocument.spreadsheet");
				header("Content-Disposition: attachment;filename=" . $filename . ".ods");
				header("Content-Transfer-Encoding: binary ");
			}
			$objWriter->save('php://output');

			//info: cleanup to free memory
			unset($export_rows);
			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);
		} //info: end (action_standalone == export-layers)
	} //info: end (action_standalone != NULL) - shared code for import/export
} //info: end plugin active check
?>