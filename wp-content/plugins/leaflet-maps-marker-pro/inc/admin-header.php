<?php
/*
	Admin Header - Maps Marker Pro Plugin
*/
//info: prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'admin-header.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . "pluggable.php");
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();

//info: workaround for vertical scrolling on mobiles #276
if (function_exists('wp_is_mobile')) {
	if (wp_is_mobile()) {
		$wrap_style = 'overflow:scroll;clear:both;';
	} else {
		$wrap_style='clear:both;'; //info: otherwise scrollbar gets hidden on desktop
	}
} else {
	$wrap_style='clear:both;';
}
echo '<div class="wrap" style="' . $wrap_style . '">';
//info: make to menu buttons active depended on page you´re on
$page = (isset($_GET['page']) ? $_GET['page'] : '');
$oid = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : '');
if ($page == 'leafletmapsmarker_markers') {
	$buttonclass1 = 'button button-primary lmm-nav-primary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_marker') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	 if ( ($oid == NULL) && ($page == 'leafletmapsmarker_marker') ) {
		$buttonclass2 = 'button button-primary lmm-nav-primary';
	} else if ( ($oid != NULL) && ($page == 'leafletmapsmarker_marker') ) {
		$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	} else {
		$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	}
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_import_export') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-primary lmm-nav-primary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_layers') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-primary lmm-nav-primary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_layer') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	if ( ($oid == NULL) && ($page == 'leafletmapsmarker_layer') ) {
		$buttonclass4 = 'button button-primary lmm-nav-primary';
	} else if ( ($oid != NULL) && ($page == 'leafletmapsmarker_layer') ) {
		$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	} else {
		$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	}
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_tools') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-primary lmm-nav-primary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_settings') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-primary lmm-nav-primary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_apis') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-primary lmm-nav-primary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_help') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-primary lmm-nav-primary';
	$buttonclass9 = 'button button-secondary lmm-nav-secondary';
} else if ($page == 'leafletmapsmarker_license') {
	$buttonclass1 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2 = 'button button-secondary lmm-nav-secondary';
	$buttonclass2b = 'button button-secondary lmm-nav-secondary';
	$buttonclass3 = 'button button-secondary lmm-nav-secondary';
	$buttonclass4 = 'button button-secondary lmm-nav-secondary';
	$buttonclass5 = 'button button-secondary lmm-nav-secondary';
	$buttonclass6 = 'button button-secondary lmm-nav-secondary';
	$buttonclass7 = 'button button-secondary lmm-nav-secondary';
	$buttonclass8 = 'button button-secondary lmm-nav-secondary';
	$buttonclass9 = 'button button-primary lmm-nav-primary';
}

if ($lmm_options['misc_whitelabel_backend'] == 'enabled') {
	$capabilities_whitelabel = 'activate_plugins';
} else {
	$capabilities_whitelabel = $lmm_options[ 'capabilities_edit' ];
}
$admin_quicklink_tools_buttons = ( current_user_can( "activate_plugins" ) ) ? "<a id='lmm-header-button5' class='" . $buttonclass5 ."' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_tools'><img src='" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-tools.png' width='10' height='10' /> ".__('Tools','lmm')."</a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" : "";
$admin_quicklink_settings_buttons = ( current_user_can( "activate_plugins" ) ) ? "<a id='lmm-header-button6' class='" . $buttonclass6 ."' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_settings'><img src='" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-settings.png' width='10' height='10' /> ".__('Settings','lmm')."</a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" : "";
$admin_quicklink_apis_buttons = ( current_user_can( "activate_plugins" ) ) ? "<a id='lmm-header-button7' class='" . $buttonclass7 ."' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_apis'><img src='" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-api.png' width='10' height='10' /> ".__('Maps Marker APIs','lmm')."</a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" : "";
$admin_quicklink_support_buttons = ( current_user_can( $capabilities_whitelabel ) ) ? "<a id='lmm-header-button8' class='" . $buttonclass8 ."' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_help'><img src='" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-help.png' width='10' height='10' /> ".__('Support', 'lmm')."</a>&nbsp;&nbsp;&nbsp;" : "";
$admin_quicklink_license_buttons = ( current_user_can( $capabilities_whitelabel ) ) ? "<a id='lmm-header-button9' class='" . $buttonclass9 ."' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_license'><img src='" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-settings.png' width='10' height='10' /> ".__('License settings', 'lmm')."</a>" : "";

//////////////////////////////////////////////////////
// info: admin notices which only show on LMM pages //
//////////////////////////////////////////////////////
if ( isset($lmm_options['misc_global_admin_notices']) && ($lmm_options['misc_global_admin_notices'] == 'show') ){
	//info: check if custom shadow image and custom marker icon directory exists
	function checkUrlExists($url) {
		$result = wp_remote_get(
			$url,
			array(
				'sslverify' => false,
				'body' => $url
			)
		);
		if (is_wp_error($result)) { return false; }
		if ((integer)$result['response']['code']!=200) { return false; }
		return true;
	}
	if ( $lmm_options['defaults_marker_icon_shadow_url_status'] == 'custom') {
		$custom_shadow_icon_url = esc_url($lmm_options['defaults_marker_icon_shadow_url']);
		$custom_shadow_icon_url_exists = checkUrlExists($custom_shadow_icon_url);
		if ( ($custom_shadow_icon_url != NULL) && (!$custom_shadow_icon_url_exists) ) {
			echo '<div class="notice notice-error" style="padding:10px;margin:10px 0;"><strong>' . sprintf(__('Warning: the setting for the marker shadow url (%1s) seems to be invalid. This can happen when you moved your WordPress installation from one server to another one.<br/>Please navigate to <a href="%2s">Settings / Map Defaults / "Default values for marker icons"</a> and update the option "Shadow URL". If you do not know which values to enter, please <a href="%3s">reset all plugins options to their defaults</a>', 'lmm'), $shadow_icon_url, LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-marker_icons', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-reset-reset_settings') . '</strong></div>';
		}
	}
	//info: check if custom marker icon dir/url is available
	if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'yes') {
		$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
		$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
		$defaults_marker_icon_url_exists = checkUrlExists($defaults_marker_icon_url . '/readme-icons.txt');
		if ( ! $defaults_marker_icon_url_exists ) {
			echo '<div class="notice notice-error" style="padding:10px;margin:10px 0;"><strong>' . sprintf(__('Warning: the setting for your custom marker icon url (%1s) seems to be invalid. <br/>Please navigate to <a href="%2s">Settings / Map Defaults / "Default values for marker icons"</a> and update the option "Custom icons URL".', 'lmm'), $defaults_marker_icon_url, LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-marker_icons') . '<br/>' . __('Please note that the file readme-icons.txt within this directory is used for this check, so please make sure, that this file is available!','lmm') . '</strong></div>';
		}
		if ( ! file_exists($defaults_marker_icon_dir . DIRECTORY_SEPARATOR . 'readme-icons.txt') ) {
			echo '<div class="notice notice-error" style="padding:10px;margin:10px 0;"><strong>' . sprintf(__('Warning: the setting for your custom marker icon directory (%1s) seems to be invalid. <br/>Please navigate to <a href="%2s">Settings / Map Defaults / "Default values for marker icons"</a> and update the option "Custom icons directory".', 'lmm'), $defaults_marker_icon_dir, LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-marker_icons') . '<br/>' . __('Please note that the file readme-icons.txt within this directory is used for this check, so please make sure, that this file is available!','lmm') . '</strong></div>';
		}
	}
	if ( $lmm_options['misc_betatest'] == 'enabled') {
		echo '<div class="notice notice-info" style="padding:10px;margin:10px 0;">' . sprintf(__('<strong>Beta testing is enabled - updates will be downloaded from the beta release channel</strong><br/>Beta versions may be used on production sites on your own risk only as they might be unstable!<br/>Important: please <a href="%1s">save the settings manually</a> after each plugin update and <a href="%2s" target="_blank">use the helpdesk</a> for feedback.', 'lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings', 'https://www.mapsmarker.com/helpdesk') . '</div>';
	}
}//info: end misc_global_admin_notices check (which can be disabled)

//info: show free update recommendation if free <3.6 is still available on server (dont delete on multisite as it might be used on other subsites)
if ( lmm_pro_version_info() ) {
	$lmm_free_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker' . DIRECTORY_SEPARATOR . 'readme.txt';
	require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'plugin.php');
	if (file_exists($lmm_free_readme)) {
		$free_version_metadata = get_plugin_data(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker' . DIRECTORY_SEPARATOR . 'leaflet-maps-marker.php');
	}
	if (!is_multisite()) {
		if ( (file_exists($lmm_free_readme)) && (lmm_validate_license_info($release_date_info=false, $license_only_info=true)===true) ) {
				if (version_compare($free_version_metadata['Version'],"3.6","<")){
					echo '<p><div class="notice notice-warning" style="padding:10px;margin:10px 0;">' . __('<strong>Thanks for purchasing a valid license key for Maps Marker Pro!</strong><br/>To finish the installation please FIRST update the inactive plugin "Leaflet Maps Marker" to the latest version and THEN delete the plugin in order to avoid plugin conflicts!<br/><h3>Deleting the plugin "Leaflet Maps Marker" before updating to the latest version will result in data loss!</h3> This message will disappear as soon as the free plugin has been updated and deleted.','lmm') . '<br/>' . __('For you information: maps created with the free version and custom settings will NOT be deleted when uninstalling the free plugin!','lmm') . '</div></p>';
				}
		}
	} else {
		if ( (file_exists($lmm_free_readme)) && (lmm_validate_license_info($release_date_info=false, $license_only_info=true)===true) ) {
			if (version_compare($free_version_metadata['Version'],"3.6","<")){
				echo '<p><div class="notice notice-warning" style="padding:10px;margin:10px 0;">' . __('<strong>Important information for multisite installations:</strong><br/><h3>Please update the plugin "Leaflet Maps Marker" to the latest version (to avoid data loss when the plugin "Leaflet Maps Marker" gets deleted)!</h3>This message will disappear as soon as the free plugin has been updated.','lmm') . '</div></p>';
			}
		}
	}
}

//info: check if newer plugin version is available
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
if ( $error_message == null ) { //info: dont show if get error
	if ( lmm_validate_license_info() ) {
		$plugin_updates = get_site_transient( 'update_plugins' );
		if (isset($plugin_updates->response['leaflet-maps-marker-pro/leaflet-maps-marker.php']->new_version)) {
			$plugin_updates_lmm_installed = get_option("leafletmapsmarker_version_pro");
			$plugin_updates_lmm_new_version = $plugin_updates->response['leaflet-maps-marker-pro/leaflet-maps-marker.php']->new_version;
			echo '<p><div class="notice notice-warning" style="padding:10px;margin:10px 0;"><strong>' . __('Maps Marker Pro - plugin update available!','lmm') . '</strong><br/>' . sprintf(__('You are currently using v%1s and the plugin author highly recommends updating to v%2s for new features, bugfixes and updated translations (please see <a href="http://mapsmarker.com/v%3s" target="_blank">this blog post</a> for more details about the latest release).','lmm'), $plugin_updates_lmm_installed, $plugin_updates_lmm_new_version, $plugin_updates_lmm_new_version . 'p') . '<br/>';
			if ( current_user_can( 'update_plugins' ) ) {
				echo sprintf(__('Update instruction: please start the update from the <a href="%1s">Updates-page</a>.','lmm'), get_admin_url() . 'update-core.php' ) . '</div></p>';
			} else {
				echo sprintf(__('Update instruction: as your user does not have the right to update plugins, please contact your <a href="mailto:%1$s?subject=Please update plugin -Maps Marker Pro- on %2$s">administrator</a>','lmm'), get_option('admin_email'), site_url() ) . '</div></p>';
			}
		}
	} else if ( (lmm_validate_license_info($release_date_info=false, $license_only_info=true)===true) && !lmm_validate_license_info() ) {
		$plugin_version = get_option('leafletmapsmarker_version_pro');
		$page = (isset($_GET['page']) ? $_GET['page'] : '');
		$latest_pro_version_transient = get_transient( 'leafletmapsmarker_latest_pro_version' );
		if ( $latest_pro_version_transient === FALSE ) {
			$latest_pro_version_array = wp_remote_get('https://www.mapsmarker.com/version.json', array( 'sslverify' => true, 'timeout' => 10 ) );
			if ( is_wp_error($latest_pro_version_array) || (isset($latest_pro_version_array['response']['code']) && (integer)$latest_pro_version_array['response']['code']!=200) ) {
				$latest_pro_version_array = wp_remote_get('https://www.mapsmarker.com/version.json',
					array( 'sslverify' => false, 'timeout' => 10 )
				);
			}
			if (!is_wp_error($latest_pro_version_array)) {
				$latest_pro_version_prepare = json_decode($latest_pro_version_array['body']);
				if ($latest_pro_version_prepare->version != NULL) {
					$latest_pro_version = $latest_pro_version_prepare->version;
				} else { //info: fallback if error
					$latest_pro_version = $plugin_version;
				}
				update_option('leafletmapsmarker_version_pro_latest', $latest_pro_version);
			}
			set_transient( 'leafletmapsmarker_latest_pro_version', 'daily check if new maps marker pro version exists', 60*60*24 );
		}
		$latest_pro_version = get_option('leafletmapsmarker_version_pro_latest');
		if ($page != 'leafletmapsmarker_license') {
			echo "<div id='message' class='error' style='padding:10px;margin:10px 0;'><strong>" . __('Warning: your access to updates and support for Maps Marker Pro has expired!','lmm') . "</strong><br/>";
			if (version_compare($latest_pro_version,$plugin_version,">")){
				echo __('Latest available version:','lmm') . " <a href='https://www.mapsmarker.com/v" . $latest_pro_version . "p' target='_blank' title='" . esc_attr__('click to show release notes','lmm') . "'>" . $latest_pro_version . "</a> " . "(<a href='www.mapsmarker.com/changelog/pro/' target='_blank'>" . __('show all available changelogs','lmm') . "</a>)<br/>";
			}
			echo sprintf(__('You can continue using version %s without any limitations. Nevertheless you will not be able to get updates including bugfixes, new features and optimizations as well as access to our support system. ','lmm'), $plugin_version) . "<br/>" . sprintf(__('<a href="%s">Please renew your access to updates and support to keep your plugin up-to-date and safe</a>.','lmm'), LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_license") . "</div>";
		}
	}
}

//info: check if LAYER column is JSON-encoded (needed if user switches from pro to free to pro as dbdelta() from update routine is only run once) - do not run in compatibility-checks.php to save db queries
global $wpdb;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$layer_json_check = $wpdb->get_var( 'SELECT `layer` FROM `'.$table_name_markers.'` LIMIT 0,1' ); //info: only check 1 row due to performance
if (isset($layer_json_check)) { //info: check if marker exists
	if (is_numeric($layer_json_check)) { //info: check if not JSON encoded
		$toolnonce = wp_create_nonce('tool-nonce');
		echo '<p><div id="database-upgrade" class="notice notice-warning" style="padding:10px;margin:10px 0;">' . sprintf(__('Warning: database tables for Maps Marker Pro need to be updated! This is usually needed when you switched back and forth between "Leaflet Maps Marker" and "Maps Marker Pro" multiple times.<br/><a href="%1s">Please click here to start the database upgrade</a>','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools&action=database_upgrade&_wpnonce=' . $toolnonce) . '</div></p>';
	}
}

//info: show info about Google Maps API key only for fresh pro installs and upgrades from free <3.11
if ( ($page == 'leafletmapsmarker_marker') || ($page == 'leafletmapsmarker_layer') || ($page == 'leafletmapsmarker_license') ) {
	if (get_option('leafletmapsmarkerpro_license_key') != NULL) { //info: only check if license key is given
		if( current_user_can( 'activate_plugins' ) ) { //info: do not show for non-admins
			if ( ($lmm_options['google_maps_api_key'] == NULL) || ($lmm_options['google_maps_api_status'] == 'disabled') ) {
				if (
					(version_compare(get_option('leafletmapsmarker_version_pro_before_update'),'0','=')) //info: check if fresh pro install [also 0 on free upgrades]
					&&
					(version_compare(get_option('leafletmapsmarker_version_before_update'),'3.10.6','<')) //info: check if upgrade from free version (<3.11) [also 0 for fresh free or pro install]
					) {
						echo '<p><div id="google-maps-api-status-info" class="notice notice-info" style="padding:5px;margin:10px 0;"><strong>' . __('Installation finished - you can now start creating maps!','lmm') . '</strong> ' . sprintf(__('We recommend using OpenStreetMap - anyway if you also want to use Google Maps, you need to register a "<a href="%1$s" target="_blank">Google Maps Javascript API key</a>".','lmm'), 'https://www.mapsmarker.com/google-maps-javascript-api') . '<br/><small>' . sprintf(__('This notice will be removed if the option <a href="%1$s">"Google Maps JavaScript API" has been enabled and a "Google Maps Javascript API key" has been set</a> or automatically after the next plugin update.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-google_js_api') . '</small></div></p>';
				}
			}
		}
	}
}

//info: display update info with current release notes
$update_info_action = isset($_POST['update_info_action']) ? $_POST['update_info_action'] : '';
$first_run = (isset($_GET['first_run']) ? 'true' : 'false');

if ( ($update_info_action == 'hide') && ($first_run == 'false') ) {
	update_option('leafletmapsmarker_update_info', 'hide');
}
if (get_option('leafletmapsmarker_update_info') == 'show') {
	$lmm_version_old = get_option( 'leafletmapsmarker_version_pro_before_update' );
	$lmm_version_new = get_option( 'leafletmapsmarker_version_pro' );
	echo '<div style="border-radius:5px;border-color:#E6DB55;background-color:#FFFFE0;margin:10px 0 5px;padding:0 0.6em;border-style:solid;border-width:1px;">';
	if (version_compare(phpversion(),"5.6","<")){
		echo '<p><div style="border:2px solid red;background-color:#ffde00;padding:5px;">' . sprintf(__('Warning: your server uses the outdated PHP version %1$s, which is not updated anymore and potentially insecure!<br/>PHP version %2$s is now the recommended version for WordPress (but it does not hurt to get updated to PHP %3$s or higher already).<br/>Read more information about how you can update at %4$s','lmm'), phpversion(), '5.6', '7.0', '<a href="http://www.wpupdatephp.com/update/" target="_blank" style="text-decoration:none;">http://www.wpupdatephp.com/</a>') . '</div></p>';
	}
	if ($lmm_version_old == 0) {
		echo '<p style="margin:5px; 0;"><span style="font-weight:bold;font-size:125%;">' . sprintf(__('Maps Marker Pro has been successfully updated to version %1s!','lmm'), $lmm_version_new) . '</span></p>';
	} else {
		echo '<p style="margin:5px; 0;"><span style="font-weight:bold;font-size:125%;">' . sprintf(__('Maps Marker Pro has been successfully updated from version %1s to %2s!','lmm'), $lmm_version_old, $lmm_version_new) . '</span></p>';
	}
	echo '<iframe name="changelog" src="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/changelog/') . '" width="98%" height="205" marginwidth="0" marginheight="0" style="border:thin dashed #E6DB55;"></iframe>'.PHP_EOL;
	echo '<form method="post" style="padding:2px 0 6px 0;">
			<input type="hidden" name="update_info_action" value="hide" />
			<input class="button-secondary" type="submit" value="' . esc_attr__('hide changelog', 'lmm') . '"/></form></div>';
}
?>

<h1><!--placeholder for correct positioning of admin notices above header table--></h1>

<div id="google-api-error-admin-header" class="notice notice-error" style="padding:10px;margin:10px 0;display:none;"><!--placeholder--></div>

<table cellpadding="5" cellspacing="0" class="widefat fixed" style="border-radius:5px;">
  <tr>
	<td style="padding:0 0 1px 5px;"><div class="logo-rtl" style="<?php echo $css_whitelabel = ($lmm_options['misc_whitelabel_backend'] == 'enabled') ? 'display:none' : '' ?>"><a href="https://www.mapsmarker.com/go" target="_blank" title="www.mapsmarker.com"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/logo-mapsmarker-pro.png" width="65" height="65" alt="www.mapsmarker.com" /></a></div>
<?php
if ($lmm_options['misc_whitelabel_backend'] == 'disabled') {
	$pro_version = get_plugin_data(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'leaflet-maps-marker.php');
	echo '<div style="font-size:1.5em;margin-top:7px;"><span style="font-weight:bold;">Maps Marker Pro<sup style="font-size:75%;">&reg;</sup> <a href="https://www.mapsmarker.com/v' . $pro_version['Version'] . 'p" target="_blank" title="' . esc_attr__('view blogpost for current version','lmm') . '">v' . $pro_version['Version'] . '</a></span>';
	echo '</div><p style="margin:8px 0 0 0;line-height:32px;">';
} else {
	echo '<div style="font-size:25px;margin-bottom:5px;padding:8px 0 0 0;font-weight:bold;">' . __('Maps','lmm');
	echo '</div><p style="margin:8px 0 3px 0;line-height:32px;">';
}
?>
  <a id="lmm-header-button1" class="<?php echo $buttonclass1; ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_markers"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-list.png" width="10" height="10" /> <?php _e("List all markers", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <?php if ($page == 'leafletmapsmarker_marker') { $page_leafletmapsmarker_marker = 'javascript:void(0);'; } else { $page_leafletmapsmarker_marker = LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker'; } ?>
  <a id="lmm-header-button2" class="<?php echo $buttonclass2; ?>" href="<?php echo $page_leafletmapsmarker_marker; ?>" title="<?php esc_attr_e('Use markers to display a single location (for multiple locations, create a layer first).','lmm'); ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-add.png" width="10" height="10" /> <?php _e("Add new marker", "lmm"); ?></a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
  <a id="lmm-header-button3" class="<?php echo $buttonclass3; ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layers"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-list.png" width="10" height="10" /> <?php _e("List all layers", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a id="lmm-header-button4" class="<?php echo $buttonclass4; ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer" title="<?php esc_attr_e('Use layers to display multiple locations at the same time.','lmm'); ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-add.png" width="10" height="10" /> <?php _e("Add new layer", "lmm"); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <?php echo $admin_quicklink_support_buttons; ?>

  <span id="show-advanced-menu-items-link" style="display:inline;"><a href="javascript:void(0);" style="cursor:pointer;" title="<?php esc_attr_e('show advanced menu items','lmm'); ?>">>>></a></span>
  <span id="hide-advanced-menu-items-link" style="display:none;"><a href="javascript:void(0);" style="cursor:pointer;" title="<?php esc_attr_e('hide advanced menu items','lmm'); ?>"><<<</a></span>
  <span id="advanced-menu-items" style="display:none;">
  &nbsp;&nbsp;
    <a id="lmm-header-button2b" class="<?php echo $buttonclass2b; ?>" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_import_export"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-import-export.png" width="10" height="10" /> <?php _e("Import/Export", "lmm"); ?></a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
  <?php
	echo $admin_quicklink_tools_buttons;
	echo $admin_quicklink_settings_buttons;
	echo $admin_quicklink_apis_buttons;
	echo $admin_quicklink_license_buttons;
  ?>
  </span>
  </p>
</td></tr></table>