<?php
/*
    Edit marker - Maps Marker Pro
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-marker.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php');
require_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php' );
WP_Filesystem();
global $wpdb, $current_user, $wp_version, $allowedtags, $locale, $is_chrome, $is_safari, $wp_filesystem;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();

//info: set custom marker icon dir/url
if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
	$defaults_marker_icon_dir = LEAFLET_PLUGIN_ICONS_DIR;
	$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
} else {
	$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
	$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
}
//info: set marker shadow url
if ( $lmm_options['defaults_marker_icon_shadow_url_status'] == 'default' ) {
	if ( $lmm_options['defaults_marker_icon_shadow_url'] == NULL ) {
		$marker_shadow_url = '';
	} else {
		$marker_shadow_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png';
	}
} else {
	$marker_shadow_url = esc_url($lmm_options['defaults_marker_icon_shadow_url']);
}

$current_editor = get_option( 'leafletmapsmarker_editor' );
$current_editor_css = ($current_editor == 'simplified') ? 'display:none;' : 'display:block';
$current_editor_css_inline = ($current_editor == 'simplified') ? 'display:none;' : 'display:inline';
$current_editor_css_audit = ($current_editor == 'simplified') ? 'display:none;' : '';

//info: workaround - select shortcode on input focus doesnt work on iOS
$is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
if ( version_compare( $wp_version, '3.4', '>=' ) ) {
	 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
} else {
	 $shortcode_select = '';
}
//info: workaround for datetime-local issue on iOS
if ($is_ios) {
	$datetime_ios_workaround = 'text';
} else {
	$datetime_ios_workaround = 'datetime-local';
}
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$addtoLayer = isset($_GET['addtoLayer']) ? intval($_GET['addtoLayer']) : (isset($_POST['layer']) ? intval($_POST['layer']) : '');
$lat_from_layer = isset($_GET['lat'])? floatval($_GET['lat']): '';
$lon_from_layer = isset($_GET['lon'])? floatval($_GET['lon']): '';
$zoom_from_layer = isset($_GET['zoom'])? intval($_GET['zoom']): '';

//info: get icons list
$iconlist = array();
$dir = opendir($defaults_marker_icon_dir);
while ($file = readdir($dir)) {
	if ($file === false) {
		break;
	}
	if ($file != "." and $file != "..") {
		if (!is_dir($dir.$file) && ((substr($file, count($file)-5, 4) == '.png') || (substr($file, count($file)-5, 4) == '.jpg') || (substr($file, count($file)-5, 4) == '.gif'))) {
			$iconlist[] = $file;
		}
	}
}
closedir($dir);
sort($iconlist);

//info: get layers list
$layerlist = $wpdb->get_results('SELECT * FROM `'.$table_name_layers.'` WHERE `id`>0 ORDER BY id ASC', ARRAY_A);

$isedit = isset($_GET['id']);
if (!$isedit) {
	//info: prepare translate URL
	if(MMP_Globals::check_multilingual() == 'wpml'){
		$translate_url_markername = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php');
		$translate_url_markeraddress = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php');
		$translate_url_markerpopuptext = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php');
	}elseif(MMP_Globals::check_multilingual() == 'pll'){
		$translate_url_markername = admin_url('admin.php?page=mlang_strings');
		$translate_url_markeraddress = admin_url('admin.php?page=mlang_strings');
		$translate_url_markerpopuptext = admin_url('admin.php?page=mlang_strings');
	}
	else{
		$translate_url_markername = 'https://www.mapsmarker.com/multilingual';
		$translate_url_markeraddress = 'https://www.mapsmarker.com/multilingual';
		$translate_url_markerpopuptext = 'https://www.mapsmarker.com/multilingual';
	}
	//info: prepare marker vars
	$id = '';
	$markername = '';

	$basemap = $lmm_options[ 'standard_basemap' ];
	//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
	if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
	} else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
	}
	//info: GoogleMutant fallback for unsupported browsers (automatically switch to OSM)
	if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($lmm_options['google_maps_plugin'] == 'google_mutant') ) {
		if ( MMP_Globals::check_google_mutant_fallback() === TRUE ) {
			$google_mutant_fallback = TRUE;
			if (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) {
				$basemap = 'osm_mapnik';
			}
		} else {
			$google_mutant_fallback = FALSE;
		}
	} else {
		$google_mutant_fallback = FALSE; //info: to avoid PHP undefined warnings
	}

	$layer = ($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '' : explode(',', $lmm_options[ 'defaults_marker_default_layer' ]);
	if ($lat_from_layer == '') {
		$lat = str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lat' ]));
	} else {
		$lat = $lat_from_layer;
	}
	if ($lon_from_layer == '') {
		$lon = str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lon' ]));
	} else {
		$lon = $lon_from_layer;
	}
	$icon = ($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : esc_html($lmm_options[ 'defaults_marker_icon' ]);
	$popuptext = '';
	if ($zoom_from_layer == '') {
		$zoom = intval($lmm_options[ 'defaults_marker_zoom' ]);
	} else {
		$zoom = $zoom_from_layer;
	}
	$openpopup = $lmm_options[ 'defaults_marker_openpopup' ];
	$mapwidth = intval($lmm_options[ 'defaults_marker_mapwidth' ]);
	$mapwidthunit = $lmm_options[ 'defaults_marker_mapwidthunit' ];
	$mapheight = intval($lmm_options[ 'defaults_marker_mapheight' ]);
	$panel = $lmm_options[ 'defaults_marker_panel' ];
	$mcreatedby = $current_user->user_login;
	$mcreatedon = current_time('mysql',0);
	$mupdatedby = $current_user->user_login;
	$mupdatedon = current_time('mysql',0);
	$controlbox = $lmm_options[ 'defaults_marker_controlbox' ];
	$overlays_custom = ( (isset($lmm_options[ 'defaults_marker_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
	$overlays_custom2 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
	$overlays_custom3 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
	$overlays_custom4 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
	$wms = ( (isset($lmm_options[ 'defaults_marker_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms_active' ] == 1 ) ) ? '1' : '0';
	$wms2 = ( (isset($lmm_options[ 'defaults_marker_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms2_active' ] == 1 ) ) ? '1' : '0';
	$wms3 = ( (isset($lmm_options[ 'defaults_marker_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms3_active' ] == 1 ) ) ? '1' : '0';
	$wms4 = ( (isset($lmm_options[ 'defaults_marker_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms4_active' ] == 1 ) ) ? '1' : '0';
	$wms5 = ( (isset($lmm_options[ 'defaults_marker_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms5_active' ] == 1 ) ) ? '1' : '0';
	$wms6 = ( (isset($lmm_options[ 'defaults_marker_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms6_active' ] == 1 ) ) ? '1' : '0';
	$wms7 = ( (isset($lmm_options[ 'defaults_marker_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms7_active' ] == 1 ) ) ? '1' : '0';
	$wms8 = ( (isset($lmm_options[ 'defaults_marker_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms8_active' ] == 1 ) ) ? '1' : '0';
	$wms9 = ( (isset($lmm_options[ 'defaults_marker_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms9_active' ] == 1 ) ) ? '1' : '0';
	$wms10 = ( (isset($lmm_options[ 'defaults_marker_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms10_active' ] == 1 ) ) ? '1' : '0';
	$kml_timestamp = '';
	$address = '';
	$gpx_url = '';
	$gpx_panel = 0;
} else {
	$id = intval($_GET['id']);
	$row = $wpdb->get_row('SELECT `markername`,`basemap`,`layer`,`lat`,`lon`,`icon`,`popuptext`,`zoom`,`openpopup`,`mapwidth`,`mapwidthunit`,`mapheight`,`panel`,`createdby`,`createdon`,`updatedby`,`updatedon`,`controlbox`,`overlays_custom`,`overlays_custom2`,`overlays_custom3`,`overlays_custom4`,`wms`,`wms2`,`wms3`,`wms4`,`wms5`,`wms6`,`wms7`,`wms8`,`wms9`,`wms10`,`kml_timestamp`,`address`,`gpx_url`,`gpx_panel` FROM `'.$table_name_markers.'` WHERE `id`='.$id, ARRAY_A);
	$markername = esc_js(htmlspecialchars($row['markername']));

	$basemap = $row['basemap'];
	//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
	if (($lmm_options['google_maps_api_status'] == 'disabled') && (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) ) {
		$basemap = 'osm_mapnik';
	} else if (($lmm_options['mapquest_api_key'] == NULL) && (($basemap == 'mapquest_osm') || ($basemap == 'mapquest_aerial') || ($basemap == 'mapquest_hybrid')) ) {
		$basemap = 'osm_mapnik';
	}
	//info: GoogleMutant fallback for unsupported browsers (automatically switch to OSM)
	if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($lmm_options['google_maps_plugin'] == 'google_mutant') ) {
		if ( MMP_Globals::check_google_mutant_fallback() === TRUE ) {
			$google_mutant_fallback = TRUE;
			if (($basemap == 'googleLayer_roadmap') || ($basemap == 'googleLayer_satellite') || ($basemap == 'googleLayer_hybrid') || ($basemap == 'googleLayer_terrain')) {
				$basemap = 'osm_mapnik';
			}
		} else {
			$google_mutant_fallback = FALSE;
		}
	} else {
		$google_mutant_fallback = FALSE; //info: to avoid PHP undefined warnings
	}

	$layer = json_decode($row['layer'], true);
	$lat = $row['lat'];
	$lon = $row['lon'];
	$icon = esc_html($row['icon']);
	$popuptext = $row['popuptext'];
	$zoom = $row['zoom'];
	$openpopup = $row['openpopup'];
	$mapwidth = $row['mapwidth'];
	$mapwidthunit = $row['mapwidthunit'];
	$mapheight = $row['mapheight'];
	$panel = $row['panel'];
	$mcreatedby = $row['createdby'];
	$mcreatedon = $row['createdon'];
	$mupdatedby = $row['updatedby'];
	$mupdatedon = $row['updatedon'];
	$controlbox = $row['controlbox'];
	$overlays_custom = $row['overlays_custom'];
	$overlays_custom2 = $row['overlays_custom2'];
	$overlays_custom3 = $row['overlays_custom3'];
	$overlays_custom4 = $row['overlays_custom4'];
	$wms = $row['wms'];
	$wms2 = $row['wms2'];
	$wms3 = $row['wms3'];
	$wms4 = $row['wms4'];
	$wms5 = $row['wms5'];
	$wms6 = $row['wms6'];
	$wms7 = $row['wms7'];
	$wms8 = $row['wms8'];
	$wms9 = $row['wms9'];
	$wms10 = $row['wms10'];
	//info: for datetime check in Firefox
	if ($row['kml_timestamp'] == NULL) {
		$kml_timestamp = '';
	} else {
		$kml_timestamp = date('Y-m-d\TH:i:s', strtotime($row['kml_timestamp']));
	}
	$address = htmlspecialchars($row['address']);
	$gpx_url = esc_url($row['gpx_url']);
	$gpx_panel = $row['gpx_panel'];
	//info: prepare translate URL
	if(MMP_Globals::check_multilingual() == 'wpml'){
		$translate_url_markername = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($markername));
		$translate_url_markeraddress = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($address));
		$translate_url_markerpopuptext = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($popuptext));
	}elseif(MMP_Globals::check_multilingual() == 'pll'){
		$translate_url_markername = admin_url('admin.php?page=mlang_strings&s='. urlencode($markername) .'&group=Maps+Marker+Pro');
		$translate_url_markeraddress = admin_url('admin.php?page=mlang_strings&s='. urlencode($address) .'&group=Maps+Marker+Pro');
		$translate_url_markerpopuptext = admin_url('admin.php?page=mlang_strings&s='. urlencode($popuptext) .'&group=Maps+Marker+Pro');
	}
	else{
		$translate_url_markername = 'https://www.mapsmarker.com/multilingual';
		$translate_url_markeraddress = 'https://www.mapsmarker.com/multilingual';
		$translate_url_markerpopuptext = 'https://www.mapsmarker.com/multilingual';
	}
}

//info: check if user is allowed to view marker - part 1
if (!MMP_Globals::check_capability('view_others', $mcreatedby)) {
	echo '<div class="notice notice-error" style="padding: 10px;">' . __('Error: your user does not have the permission to view markers from other users!','lmm') . '</div>';
} else {

//info: check if marker exists - part 1
if ($lat === NULL) {
	$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), htmlspecialchars($_GET['id']));
	echo '<p><div class="notice notice-error" style="padding:10px;">' . $error_marker_not_exists . '</div></p>';
	echo '<script type="text/javascript">
			jQuery(function($) {
				$(document).ready(function(){
					$("#lmm-header-button2").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker");
				});
			});
			</script>';
} else {
?>

<?php $nonce= wp_create_nonce('marker-nonce'); ?>
<form id="marker-add-edit" method="post" action="javascript:void(0);">
<input type="hidden" id="id" name="id" value="<?php echo $id ?>" />
<input type="hidden" id="action-marker-add-edit" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $overlays_custom ?>" />
<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $overlays_custom2 ?>" />
<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $overlays_custom3 ?>" />
<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $overlays_custom4 ?>" />
<input type="hidden" id="active_editor" name="active_editor" value="<?php echo $current_editor ?>" />
<input id="icon_hidden" type="hidden" name="icon_hidden" value="<?php echo $icon; ?>" /> <!-- //info: IE11 fix -->

<div id="lmm_ajax_results_top" class="notice notice-success" style="padding:10px;display:none;"></div>

<div id="div-marker-editor-hide-on-ajax-delete" style="clear:both;">

<?php
if ($current_editor == 'simplified') {
	echo '<div id="switch-link-visible" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-advanced-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to advanced editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
	echo '<div id="switch-link-hidden" class="switch-link-rtl" style="display:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-simplified-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to simplified editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
} else {
	echo '<div id="switch-link-visible" class="switch-link-rtl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-simplified-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to simplified editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
	echo '<div id="switch-link-hidden" class="switch-link-rtl" style="display:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float:right;"><a style="text-decoration:none;cursor:pointer;" id="editor-switch-link-to-advanced-href"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="' . esc_attr__('switch to advanced editor','lmm') . '" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
}
?>

<h1 style="margin:10px 0 10px 0;"><span id="marker-heading"><?php ($isedit === true) ? _e('Edit marker','lmm') : _e('Add new marker','lmm') ?>
<?php
	if ($isedit === true) {	echo ' "' . stripslashes($markername) . '" (ID '.$id.')'; }
	echo '</span>';
	if (MMP_Globals::check_capability('edit', $mcreatedby)) {
		if ($isedit === true) { $button_text = __('update','lmm'); } else { $button_text = __('publish','lmm'); }
		echo '<input id="submit_top" style="font-weight:bold;margin-left:10px;" type="submit" name="marker" class="button button-primary" value="' . $button_text . '" disabled="disabled" />';
		echo '<img src="' . admin_url('/images/wpspin_light.gif') . '" class="waiting" id="lmm_ajax_loading_top" style="margin-left:5px;display:none;"/>';
	} else {
		if ($isedit === true) {
			echo '<span style="font-size:13px;margin-left:20px;font-weight:normal;">' . __('Your user does not have the permission to update this marker!','lmm') . '</span>';
		} else {
			echo '<span style="font-size:13px;margin-left:20px;font-weight:normal;">' . __('Your user does not have the permission to add a new marker!','lmm') . '</span>';
		}
	}
	//info: duplicate button
	$duplicate_delete_button_visibility = ($isedit === true) ? 'display:inline;' : 'display:none;';
	if (MMP_Globals::check_capability('edit', $mcreatedby)) {
		echo '<span id="duplicate_span_top" style="margin:0 0 0 50px;' . $duplicate_delete_button_visibility . '">';
		echo '<a id="duplicate_button_top" href="javascript:void(0);" class="button button-secondary" style="font-size:13px;text-decoration:none;" disabled="disabled">' . __('duplicate', 'lmm') . '</a>';
		echo '</span>';
	} else {
		if ($isedit === true) {
			echo '<span id="duplicate_span_top" style="font-size:13px;margin-left:20px;font-weight:normal;">' . __('Your user does not have the permission to duplicate this marker!','lmm') . '</span>';
		}
	}
	//info: delete button
	if (MMP_Globals::check_capability('delete', $mcreatedby)) {
		echo '<span id="delete_span_top" style="margin:0 0 0 50px;' . $duplicate_delete_button_visibility . '">';
		echo '<a id="delete_button_top" href="javascript:void(0);" id="marker-delete" class="button button-secondary" style="font-size:13px;text-decoration:none;color:#FF0000;" disabled="disabled">' . __('delete', 'lmm') . '</a>';
		echo '</span>';
	} else {
		if ($isedit === true) {
			echo '<span id="delete_span_top" style="font-size:13px;margin-left:20px;font-weight:normal;">' . __('Your user does not have the permission to delete this marker!','lmm') . '</span>';
		}
	}
?>
</h1>
<p style="margin-top:0px;">
<?php echo sprintf(__('Add a new marker to display a single location. To display multiple locations at the same time, <a href="%1$s">create a layer</a> first (e.g. "company locations") and then add markers (e.g. "headquarters", "store A", "store B") assigned to that layer.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer'); ?>
</p>
<table class="widefat" style="border-radius:5px;">
	<?php if ($isedit === true) { $shortcode_visibility = 'table-row'; } else { $shortcode_visibility = 'none'; }?>
	<tr id="tr-shortcode" style="display:<?php echo $shortcode_visibility; ?>;">
		<td style="width:230px;" class="lmm-border"><label for="shortcode"><strong><?php _e('Shortcode and API links','lmm') ?></strong></label></td>
		<td class="lmm-border"><input id="shortcode" style="width:206px;background:#f3efef;" type="text" value="[<?php echo htmlspecialchars($lmm_options[ 'shortcode' ]); ?> marker=&quot;<?php echo $id?>&quot;]" <?php echo $shortcode_select; ?>>
		<?php
			if ($current_editor == 'simplified') {
				echo '<div id="apilinkstext" style="display:inline;"><a tabindex="123" style="cursor:pointer;">' . __('show API links','lmm') . '</a></div>';
			}
			echo '<span id="apilinks" style="' . $current_editor_css_inline . '">';
			echo '<a id="shortcode-link-kml" tabindex="125" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $id . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /> KML</a> <a tabindex="126" href="https://www.mapsmarker.com/kml" target="_blank" title="' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '"/></a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-fullscreen" tabindex="127" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" /> ' . __('Fullscreen','lmm') . '</a> <span title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"/></span>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-qr" tabindex="128" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" /> ' . __('QR code','lmm') . '</a> <span title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"/></span>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-geojson" tabindex="129" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $id . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" /> GeoJSON</a> <a tabindex="130" href="https://www.mapsmarker.com/geojson" target="_blank" title="' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '"/></a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-georss" tabindex="131" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" /> GeoRSS</a> <a tabindex="132" href="https://www.mapsmarker.com/georss" target="_blank" title="' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '"/></a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="shortcode-link-wikitude" tabindex="133" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" /> Wikitude</a> <a tabindex="134" href="https://www.mapsmarker.com/wikitude" target="_blank" title="' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '"> <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0" alt="' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '"/></a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="134" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_apis"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-page.png" width="16" height="16" alt="' . esc_attr__('Settings','lmm') . '" /> Maps Marker API</a>';
			echo '</span>';
		?>
		<br><small><?php _e('Use this shortcode in posts or pages on your website or one of the API links for embedding in external websites or apps','lmm') ?></small>
			</td>
	</tr>
	<?php if ($isedit === true) { $used_in_content_visibility = 'table-row'; } else { $used_in_content_visibility = 'none'; }?>
	<tr id="tr-usedincontent" style="display:<?php echo $used_in_content_visibility; ?>;">
		<td style="width:230px;" class="lmm-border"><strong><?php _e('Used in content','lmm') ?></strong></td>
		<td class="lmm-border"><?php echo MMP_Globals::get_map_shortcodes($id, 'marker'); ?></td>
	</tr>
	<tr>
	<tr>
		<td style="width:230px;" class="lmm-border"><label for="markername"><strong><?php _e('Marker name','lmm') ?></strong></label></td>
		<td class="lmm-border"><input autofocus style="width:640px;" type="text" id="markername" name="markername" value="<?php echo stripslashes($markername) ?>" /> <?php if ($isedit === true) { ?>  <span class="wpml-markertranslatelink wpml-markername">(<a target="_blank" href="<?php echo $translate_url_markername; ?>"><?php _e('translate', 'lmm'); ?></a>) </span> <?php }else{  ?> <span class="wpml-markertranslatelink wpml-markername">  (<a target="_blank" href="<?php echo $translate_url_markername; ?>"><?php _e('translate', 'lmm'); ?></a>) </span>  <?php } ?> </td>
	</tr>
	<tr>
		<td class="lmm-border">
			<label for="address"><strong><?php _e('Location','lmm') ?></strong></label>
			<br/>
			<?php
			//info: to prevent PHP warnings
			$geocoding_provider_selected_mapzen = '';
			$geocoding_provider_selected_algolia = '';
			$geocoding_provider_selected_photon = '';
			$geocoding_provider_selected_mapquest = '';
			$geocoding_provider_selected_google = '';

			if ($lmm_options['geocoding_provider'] == 'mapzen-search') {
				$geocoding_provider_settings_internal_link = '#lmm-geocoding-mapzen';
				$geocoding_provider_selected_mapzen = 'selected';
				$mapzen_rate_limit = '30.000';
				$mapzen_rate_limit_period = __('day','lmm');
				$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': <span id="mapzen-rate-limit-static" style="display:inline;">' . sprintf(__('%1$s requests/%2$s','lmm'), $mapzen_rate_limit, $mapzen_rate_limit_period) . '</span><span id="mapzen-rate-limit-dynamic" style="display:none;">' . sprintf(__('%1$s out of %2$s requests/%3$s','lmm'), '<span id="mapzen-rate-limit-consumed"></span>', $mapzen_rate_limit, $mapzen_rate_limit_period) . '</span>';

			} else if ($lmm_options['geocoding_provider'] == 'algolia-places') {
				$geocoding_provider_settings_internal_link = '#lmm-geocoding-algolia';
				$geocoding_provider_selected_algolia = 'selected';
				if ( ($lmm_options['geocoding_algolia_appId'] != NULL) && ($lmm_options['geocoding_algolia_apiKey'] != NULL) ){
					$algolia_rate_limit = '100.000';
					$algolia_rate_limit_period = __('month','lmm');
				} else {
					$algolia_rate_limit = '1.000';
					$algolia_rate_limit_period = __('day','lmm');
				}
				$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/domain/%2$s','lmm'), $algolia_rate_limit, $algolia_rate_limit_period);

			} else if ($lmm_options['geocoding_provider'] == 'photon') {
				$geocoding_provider_settings_internal_link = '#lmm-geocoding-photon';
				$geocoding_provider_selected_photon = 'selected';
				$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': <span id="photon-rate-limit-static" style="display:inline;">' . sprintf(__('%1$s requests/domain/%2$s','lmm'), '2.500', __('day','lmm')) . '</span><span id="photon-rate-limit-dynamic" style="display:none;">' . sprintf(__('%1$s out of %2$s requests/%3$s','lmm'), '<span id="photon-rate-limit-consumed"></span>', '<span id="photon-rate-limit"></span>', __('day','lmm')) . '</span>';

			} else if ($lmm_options['geocoding_provider'] == 'mapquest-geocoding') {
				$geocoding_provider_settings_internal_link = '#lmm-geocoding-mapquest';
				$geocoding_provider_selected_mapquest = 'selected';
				$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '15.000', __('month','lmm'));

			} else if ($lmm_options['geocoding_provider'] == 'google-geocoding') {
				$geocoding_provider_settings_internal_link = '#lmm-geocoding-google';
				$geocoding_provider_selected_google = 'selected';
				$geocoding_provider_rate_limit_details = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '1.000', __('day','lmm'));
			}
			//info: Get all the limits for each provider
			$geocoding_limits = array();

			$mapzen_rate_limit = '30.000';
			$mapzen_rate_limit_period = __('day','lmm');
			$geocoding_limits['mapzen'] = __('Rate limit','lmm') . ': <span id="mapzen-rate-limit-static" style="display:inline;">' . sprintf(__('%1$s requests/%2$s','lmm'), $mapzen_rate_limit, $mapzen_rate_limit_period) . '</span><span id="mapzen-rate-limit-dynamic" style="display:none;">' . sprintf(__('%1$s out of %2$s requests/%3$s','lmm'), '<span id="mapzen-rate-limit-consumed"></span>', $mapzen_rate_limit, __('day','lmm')) . '</span>';

			if ( ($lmm_options['geocoding_algolia_appId'] != NULL) && ($lmm_options['geocoding_algolia_apiKey'] != NULL) ){
				$algolia_rate_limit = '100.000';
				$algolia_rate_limit_period = __('month','lmm');
				$geocoding_limits['algolia']  = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), $algolia_rate_limit, $algolia_rate_limit_period);
			} else {
				$algolia_rate_limit = '1.000';
				$algolia_rate_limit_period = __('day','lmm');
				$geocoding_limits['algolia']  = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/domain/%2$s','lmm'), $algolia_rate_limit, $algolia_rate_limit_period);
			}
			$geocoding_limits['photon'] = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s out of %2$s requests/domain/%3$s','lmm'), '<span id="photon-rate-limit-consumed">?</span>', '<span id="photon-rate-limit">2.500</span>', __('day','lmm'));
			$geocoding_limits['mapquest'] = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '15.000', __('month','lmm'));
			$geocoding_limits['google'] = __('Rate limit','lmm') . ': ' . sprintf(__('%1$s requests/%2$s','lmm'), '1.000', __('day','lmm'));

			//info: check if Mapzen Search API key is set
			$geocoding_provider_mapzen_disabled = '';
			if ($lmm_options['geocoding_mapzen_search_api_key'] == NULL) {
				$option_mapzen_inactive = '<optgroup label="' . esc_attr__('Inactive (API key required)','lmm') . '"><option value="mapzen_geocoding" ' . $geocoding_provider_selected_mapzen . ' disabled="disabled">Mapzen Search (' . __('recommended','lmm') . ')</option></optgroup>';
				$option_mapzen_active = '';
			} else {
				$option_mapzen_active = '<option value="mapzen_geocoding" ' . $geocoding_provider_selected_mapzen . '>Mapzen Search</option>';
				$option_mapzen_inactive = '';
			}

			//info: check if MapQuest API key is set
			$geocoding_provider_mapquest_disabled = '';
			if ($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL) {
				$option_mapquest_inactive = '<optgroup label="' . esc_attr__('Inactive (API key required)','lmm') . '"><option value="mapquest_geocoding" ' . $geocoding_provider_selected_mapquest . ' disabled="disabled">MapQuest Geocoding</option></optgroup>';
				$option_mapquest_active = '';
			} else {
				$option_mapquest_active = '<option value="mapquest_geocoding" ' . $geocoding_provider_selected_mapquest . '>MapQuest Geocoding</option>';
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
				$option_google_inactive = '<optgroup label="' . esc_attr__('Inactive (API key required)','lmm') . '"><option value="google_geocoding" ' . $geocoding_provider_selected_google . ' disabled="disabled">Google Geocoding</option></optgroup>';
				$option_google_active = '';
			} else {
				$option_google_active = '<option value="google_geocoding" ' . $geocoding_provider_selected_google . '>Google Geocoding</option>';
				$option_google_inactive = '';
			}

			echo '<small><a tabindex="136" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#geocoding" title="' . esc_attr__('click to change default geocoding provider','lmm') . '">' . __('Geocoding provided by','lmm') . '</a>:';
			if (current_user_can('activate_plugins')) {
				echo '<div style="float:right;display:inline-block;"><a id="geocoding-provider-settings-link" title="' . esc_attr__('click to change current geocoding provider settings','lmm') . '" tabindex="136" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings' . $geocoding_provider_settings_internal_link . '">' . __('Settings','lmm') . '</a></div>';
			}
			echo '</small>
			<select tabindex="122" id="geocoding-provider-select" style="width: 100%">
			<optgroup label="' . esc_attr__('Available providers','lmm') . '">
				<option value="algolia_places" ' . $geocoding_provider_selected_algolia . '>Algolia Places</option>
				<option value="photon" ' . $geocoding_provider_selected_photon . '>Photon@MapsMarker</option>
				' . $option_mapzen_active  . '
				' . $option_mapquest_active  . '
				' . $option_google_active  . '
			</optgroup>
			' . $option_mapzen_inactive  . '
			' . $option_mapquest_inactive  . '
			' . $option_google_inactive  . '
			</select>';

			echo '<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery("#geocoding-provider-select").select2({
						minimumResultsForSearch: Infinity
					});
					jQuery("#geocoding-provider-select").on("change", function (e) {
						var new_geocoding_provider = jQuery("#geocoding-provider-select").val();
						jQuery("#address").places_autocomplete("destroy");
						jQuery("#address").off("autocomplete:selected");
						jQuery("head").find("link[rel=preconnect]").remove();
						if (new_geocoding_provider == "mapzen_search") {
							jQuery("#geocoding-provider-status").html("&nbsp;");
							jQuery("head").append("<link rel=\"preconnect\" href=\"https://search.mapzen.com\" crossorigin />");
							jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapzen");';
							if ($isedit) {
								echo "var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}else{
								echo "var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}
							echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['mapzen'].'\');';
							echo 'mapzen_search.init();
						} else if (new_geocoding_provider == "algolia_places") {
							jQuery("#geocoding-provider-status").html("&nbsp;");
							jQuery("head").append("<link rel=\"preconnect\" href=\"https://places-dsn.algolia.net\" crossorigin />");
							jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-algolia");';
							if ($isedit) {
								echo "var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}else{
								echo "var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}
							echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['algolia'].'\');';
							echo 'algolia_places.init();
						} else if (new_geocoding_provider == "photon") {
							jQuery("#geocoding-provider-status").html("&nbsp;");
							jQuery("head").append("<link rel=\"preconnect\" href=\"https://photon.mapsmarker.com\" crossorigin />");
							jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-photon");';
							if ($isedit) {
								echo "var photon = new MMP_Geocoding('photon', geocoding_options, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}else{
								echo "var photon = new MMP_Geocoding('photon', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}
							echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['photon'].'\');';
							echo 'photon.init();
						} else if (new_geocoding_provider == "mapquest_geocoding") {
							jQuery("#geocoding-provider-status").html("<a href=\"https://www.mapsmarker.com/mapquest-api-key\" tabindex=\"150\" target=\"_blank\">' . __('Please note that MapQuest basemaps should also be used if MapQuest geocoding is selected!','lmm') . '</a>");
							jQuery("head").append("<link rel=\"preconnect\" href=\"https://www.mapquestapi.com\" crossorigin />");
							jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapquest");';
							if ($isedit) {
								echo "var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}else{
								echo "var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}
							echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['mapquest'].'\');';
							echo 'mapquest_geocoding.init();
						} else if (new_geocoding_provider == "google_geocoding") {
							jQuery("#geocoding-provider-status").html("&nbsp;");
							jQuery("head").append("<link rel=\"preconnect\" href=\"https://maps.googleapis.com\" crossorigin />");
							jQuery("#geocoding-provider-settings-link").attr("href", "' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-google");';
							if ($isedit) {
								echo "var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, true, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}else{
								echo "var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, false, " . intval($lmm_options["geocoding_typing_delay"]) . ", " . intval($lmm_options["geocoding_min_chars_search_autostart"]) . ");";
							}
							echo 'jQuery("#geocoding-rate-limit-details").html(\''.$geocoding_limits['google'].'\');';
							echo 'google_geocoding.init();
						}
					});'.PHP_EOL;

					//info: preconnect geocoding providers (DNS, TCP, TLS)
					echo 'jQuery("head").find("link[rel=preconnect]").remove();'.PHP_EOL;
					if ($lmm_options['geocoding_provider'] == 'mapzen-search') {
						echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://search.mapzen.com\" crossorigin />");';
					} else if ($lmm_options['geocoding_provider'] == 'algolia-places') {
						echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://places-dsn.algolia.net\" crossorigin />");';
					} else if ($lmm_options['geocoding_provider'] == 'photon') {
						echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://photon.mapsmarker.com\" crossorigin />");';
					} else if ($lmm_options['geocoding_provider'] == 'mapquest-geocoding') {
						echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://www.mapquestapi.com\" crossorigin />");';
					} else if ($lmm_options['geocoding_provider'] == 'google-geocoding') {
						echo 'jQuery("head").append("<link rel=\"preconnect\" href=\"https://maps.googleapis.com\" crossorigin />");';
					}
				echo'});
			</script>';
			echo '<small><span id="geocoding-rate-limit-details">' . $geocoding_provider_rate_limit_details . '</span></small>';
			?>
		</td>
		<td class="lmm-border">
			<?php
			if
			(
				( ($lmm_options['geocoding_provider'] == 'google-geocoding') || ($lmm_options['geocoding_provider_fallback'] == 'google-geocoding')	)
				&&
				(
					( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] == NULL) )
					||
					( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] == NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] == NULL)) )
				)
			) {
				$geocoding_provider_status_text = sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-google', 'Google Geocoding', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
				$location_input_css = 'background:#ff9999;';
			} else if (($lmm_options['geocoding_mapquest_geocoding_api_key'] == NULL)  && ($lmm_options['geocoding_provider'] == 'mapquest-geocoding')) {
				$geocoding_provider_status_text = sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapquest', 'MapQuest Geocoding', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
				$location_input_css = 'background:#ff9999;';
			} else if (($lmm_options['geocoding_mapquest_geocoding_api_key'] != NULL)  && ($lmm_options['geocoding_provider'] == 'mapquest-geocoding')) {
				$geocoding_provider_status_text = '<a href="https://www.mapsmarker.com/mapquest-api-key" tabindex="150" target="_blank">' . __('Please note that MapQuest basemaps should also be used if MapQuest geocoding is selected!','lmm') . '</a>';
				$location_input_css = '';
			} else if (($lmm_options['geocoding_mapzen_search_api_key'] == NULL)  && ($lmm_options['geocoding_provider'] == 'mapzen-search')) {
				$geocoding_provider_status_text = sprintf(__('Error: please <a href="%1$s">enter your %2$s-API key</a> or <a href="%3$s">select an alternative geocoding provider</a>!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding-mapzen', 'Mapzen Search', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
				$location_input_css = 'background:#ff9999;';
			} else if ($lmm_options['geocoding_provider'] == $lmm_options['geocoding_provider_fallback']) {
				$geocoding_provider_status_text = sprintf(__('Warning: you did not configure an <a href="%1$s">alternative geocoding provider</a> - no fallback will be available if main geocoding provider is unavailable!','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-geocoding');
				$location_input_css = 'background:yellow;';
			} else {
				$geocoding_provider_status_text = '&nbsp;';
				$location_input_css = '';
			}
			echo '<div id="geocoding-provider-status" style="font-size:11px;">' . $geocoding_provider_status_text . '</div>';
			$placeholder_text = ( (intval($lmm_options['geocoding_min_chars_search_autostart']) == 0) || (intval($lmm_options['geocoding_min_chars_search_autostart']) == 1) ) ? esc_attr__('Please enter a location','lmm') : sprintf(esc_attr__('Please enter a location (%1$s characters minimum to start typeahead suggestions)','lmm'), intval($lmm_options['geocoding_min_chars_search_autostart']));
			echo '<input style="margin:0px;width:640px;' . $location_input_css . '" type="text" id="address" name="address" value="' . stripslashes(htmlspecialchars($address)) . '" placeholder="' .  $placeholder_text . '" />';
			?>
			<?php if ($isedit === true) { ?>  <span class="wpml-markertranslatelink wpml-markeraddress">(<a target="_blank" href="<?php echo $translate_url_markeraddress; ?>"><?php _e('translate', 'lmm'); ?></a>) </span> <?php }else{  ?> <span class="wpml-markertranslatelink wpml-markeraddress">  (<a target="_blank" href="<?php echo $translate_url_markeraddress; ?>"><?php _e('translate', 'lmm'); ?></a>) </span>  <?php } ?>
			<div id="toggle-coordinates" style="clear:both;margin-top:5px;<?php echo $current_editor_css; ?>">
			<?php echo __('or paste coordinates here','lmm') . ' - '; ?>
			<?php _e('latitude','lmm') ?>: <input style="width: 100px;" type="text" id="lat" name="lat" value="<?php echo $lat; ?>" />
			<?php _e('longitude','lmm') ?>: <input style="width: 100px;" type="text" id="lon" name="lon" value="<?php echo $lon; ?>" />
			</div>
		</td>
	</tr>
	<tr>
		<td class="lmm-border"><p style="margin-bottom:0;"><strong><?php _e('Map size','lmm') ?></strong><br/>
			<label for="mapwidth"><?php _e('Width','lmm') ?>:</label>
			<input size="3" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo $mapwidth ?>" style="margin-left:5px;" />
			<input id="mapwidthunit_px" type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>><label for="mapwidthunit_px" title="<?php esc_attr_e('pixel','lmm'); ?>">px</label>&nbsp;&nbsp;&nbsp;
			<input id="mapwidthunit_percent" type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>><label for="mapwidthunit_percent">%</label><br/>
			<label for="mapheight"><?php _e('Height','lmm') ?>:</label>
			<input size="3" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" /> <span title="<?php esc_attr_e('pixel','lmm'); ?>">px</span>

			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

			<label for="zoom"><strong><?php _e('Zoom','lmm') ?></strong> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('You can also change zoom level by clicking on + or - on preview map or using your mouse wheel'); ?>" width="12" height="12" border="0"/></label>&nbsp;<input style="width:40px;" type="text" id="zoom" name="zoom" value="<?php echo $zoom ?>" />
			<small>
			<?php
			echo '<span id="toogle-global-maximum-zoom-level" style="' . $current_editor_css_inline . '"><br>' . __('Global maximum zoom level','lmm') . ': ';
			if (current_user_can('activate_plugins')) {
				echo '<a title="' . esc_attr__('If the native maximum zoom level of a basemap is lower, tiles will be upscaled automatically.','lmm') . '" tabindex="111" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-default_basemaps">' . intval($lmm_options['global_maxzoom_level']) . '</a>';
			} else {
				echo intval($lmm_options['global_maxzoom_level']);
			}
			?>
			</span>
			</small>

			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

			<label for="layer"><strong><?php _e('Layer(s)','lmm') ?></strong></label>
			<select id="layer" name="layer[]" style="width:230px;" multiple="multiple">
				<option value="0">
				<?php _e('not assigned to a layer','lmm') ?>
				</option>
				<?php
					foreach ($layerlist as $row) {
						$layername_abstract = (strlen($row['name']) >= 28) ? '...': '';
						$layername_abstract_mlm = (strlen($row['name']) >= 21) ? '...': '';
						if ($row['multi_layer_map'] == 0) {
							echo '<option value="' . $row['id'] . '"' . ( ((is_array($layer) && @in_array($row['id'], $layer)) || ($row['id'] == $addtoLayer)) ? ' selected="selected"' : '') . ' title="' . stripslashes(htmlspecialchars($row['name'])) . '">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 28) . $layername_abstract . ' (ID ' . $row['id'] . ')</option>';
						} else {
							echo '<option title="' . stripslashes(htmlspecialchars($row['name'])) . ' (' . esc_attr__('This is a multi-layer map - markers cannot be assigned to this layer directly','lmm') . ')" value="' . $row['id'] . '"' . ( (($row['id'] == $layer) || ($row['id'] == $addtoLayer)) ? ' selected="selected"' : '') . ' disabled="disabled">' . mb_substr(stripslashes(htmlspecialchars($row['name'])), 0, 21) . $layername_abstract_mlm . ' (ID ' . $row['id'] . '/MLM)</option>';
						}
					}
				?>
			</select>
			<br>
			<small>
			<?php
			$is_unassigned = '';
			if ( (is_array($layer)) || (isset($_GET['addtoLayer'])) ) {
					$layereditlink = '<span id="layereditlink" style="display:inline;">';
					if(is_array($layer)){
						$is_unassigned = (count($layer) == 1 && in_array("0", $layer))?'display:none;':'';
					}
					$layereditlink .= '<span class="layereditlink_wrap" style="'.$is_unassigned.'">'.__('edit layer','lmm'). ' (ID </span>';
					$layereditlink .= '<span id="multilayeredit">';
					if(is_array($layer)){
						if(!in_array("0", $layer)){
							foreach($layer as $l){

								$layereditlink .= '<a id="layereditlink-href" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . $l . '">'  . '  <span id="layereditlink-id">' . $l . '</span></a>';
								if(end($layer) != $l){
									$layereditlink .= ', ';
								}
							}
						}
					}
					if(isset($_GET['addtoLayer'])){
						if(!empty($layer)){ $layereditlink.= ', '; }
						$layereditlink .= '<a id="layereditlink-href" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id=' . intval($_GET['addtoLayer']) . '">'  . '  <span id="layereditlink-id">' . intval($_GET['addtoLayer']) . '</span></a>';
					}
					$layereditlink .= '</span>';
					$layereditlink .= '<span class="layereditlink_wrap" style="'.$is_unassigned.'">'.') '.__('or','lmm') . '</span>';
					$layereditlink .= '</span>';
			} else {
					$layereditlink = '<span id="layereditlink" style="display:none;">' .  __('edit layer','lmm').' (ID ';
						$layereditlink .= '<span id="multilayeredit">';
							$layereditlink .= '<a id="layereditlink-href" ><span id="layereditlink-id"></span>)</a>';
						$layereditlink .= '</span>';
					$layereditlink .= '<span class="layereditlink_wrap" style="'.$is_unassigned.'">'.') '.__('or','lmm') . '</span>';

			}
			echo $layereditlink . ' </span><a id="layeraddlink" tabindex="121" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer">' . __('add new layer','lmm') . '</a>';
			?>
			</small>
			<?php
				$preview_display = 'none';
				if(is_array($layer)){
					if(!in_array("0", $layer)){
						$preview_display = 'inline';
			  	 	}
				} else if (isset($_GET['addtoLayer']) && (intval($_GET['addtoLayer']) != 0) ) {
					$preview_display = 'inline';
				}
			?>
			<br/>
			<a id="preview_layers" style="display:<?php echo $preview_display; ?>;" href="javascript:void(0);"><img id="preview_layers_icon" src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-eye-show.png" width="16" height="16" alt="preview icon" style="float:left;margin:2px 3px 0 0;" /> <span id="preview_layers_text"><?php _e('preview all markers from assigned layer(s)','lmm'); ?></span></a>

			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

			<div style="float:right;"><label for="gpx_panel"><?php _e('display panel','lmm') ?></label>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="gpx_panel" id="gpx_panel" <?php checked($gpx_panel, 1 ); ?>></div>
			<label for="gpx_url"><strong><?php _e('URL to GPX track','lmm') ?></strong></label><br/>
			<?php
				if ($gpx_url != NULL) {

					//info: load gpx_content
					$gpx_content_array = wp_remote_get( $gpx_url, array( 'sslverify' => false, 'timeout' => 30 ) );

					if (is_wp_error($gpx_content_array)) {
						echo '<div class="notice notice-error" style="padding:10px;">' . sprintf(__('The GPX file could not be loaded due to the following error:<br/>%s!','lmm'), $gpx_content_array->get_error_message()) . '</div>';
					} else if ($gpx_content_array['response']['code'] == '404') {
							echo '<div class="gpx-error-notice">' . __('Error', 'lmm') . ' ' . $gpx_content_array['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), '<a href="' . $gpx_url . '" target="_blank">' . $gpx_url . '</a>') . '</div>';
							echo '<div class="notice notice-error" style="padding:10px;">' . __('Error', 'lmm') . ' ' . $gpx_content_array['response']['code'] . ': ' . sprintf(__('The GPX file at %s could not be found!','lmm'), '<a href="' . $gpx_url . '" target="_blank">' . $gpx_url . '</a>') . '</div>';
					}
				}
			?>
			<input style="width:229px;" type="text" id="gpx_url" name="gpx_url" value="<?php echo $gpx_url ?>" /><br/>
			<?php if (current_user_can('upload_files')) { echo '<small><span style="color:#21759B;cursor:pointer;" onMouseOver="this.style.color=\'#D54E21\'" onMouseOut="this.style.color=\'#21759B\'" id="upload_gpx_file">' . __('add','lmm') . '</span> |'; } ?>
			<a tabindex="117" href="https://www.mapsmarker.com/gpx-convert" target="_blank" title="<?php esc_attr_e('Click here for a tutorial on how to convert a non-GPX-track file into a GPX track file','lmm'); ?>"><?php _e('convert','lmm'); ?></a> |
			<a tabindex="118" href="https://www.mapsmarker.com/gpx-merge" target="_blank" title="<?php esc_attr_e('Click here for a tutorial on how to merge multiple GPX-track files into one GPX track file','lmm'); ?>"><?php _e('merge','lmm'); ?></a>
			<?php if (current_user_can('activate_plugins')) { echo ' | <a tabindex="116" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-gpx">' . __('settings','lmm') . '</a>'; } ?>
			<?php if ($gpx_url != NULL) { $fitbounds_css = 'display:inline;'; } else { $fitbounds_css = 'display:none;'; }
			echo '<span id="gpx_fitbounds_link" style="color:#21759B;cursor:pointer;' . $fitbounds_css . '" onMouseOver="this.style.color=\'#D54E21\'" onMouseOut="this.style.color=\'#21759B\'" class="gpxfitbounds"> | ' . __('fit bounds','lmm') . ' <img title="' . esc_attr__('Attention: when you save the map, the position of the marker will be used for the map center!','lmm') . '" src="' .  LEAFLET_PLUGIN_URL . 'inc/img/icon-exclamation.png" width="14" height="14" border="0"/></small></span>'; ?>
			</p>
			<div id="toggle-controlbox-panel-kmltimestamp-backlinks-minimaps" style="<?php echo $current_editor_css; ?>">
			<p>
			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
			<strong><?php _e('Controlbox for basemaps/overlays','lmm') ?>:</strong></label><br/>
			<input style="margin-top:1px;" id="controlbox_hidden" type="radio" name="controlbox" value="0" <?php checked($controlbox, 0); ?>><label for="controlbox_hidden"><?php _e('hidden','lmm') ?></label><br/>
			<input style="margin-top:1px;" id="controlbox_collapsed" type="radio" name="controlbox" value="1" <?php checked($controlbox, 1); ?>><label for="controlbox_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
			<input style="margin-top:1px;" id="controlbox_expanded" type="radio" name="controlbox" value="2" <?php checked($controlbox, 2); ?>><label for="controlbox_expanded"><?php _e('expanded','lmm') ?></label>

			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

			<strong><label for="panel"><?php _e('Display panel','lmm') ?></label></strong>&nbsp;&nbsp;<input style="margin-top:1px;" type="checkbox" name="panel" id="panel" <?php checked($panel, 1 ); ?>><br/>
			<small><?php _e('If checked, panel on top of map is displayed','lmm') ?></small>

			</p>
			</div>
		</td>
		<td style="padding-bottom:5px;" class="lmm-border">
			<?php
			echo '<div id="lmm" class="lmm-rtl" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
			//info: panel for marker name and API URLs
			$panel_state = ($panel == 1) ? 'block' : 'none';
			echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])) . ';">'.PHP_EOL;
			echo '<div class="lmm-panel-api">';
				if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
						//info: Google language localization (directions)
						if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
							$google_language = '';
						} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
							if ( $locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
						} else {
							$google_language = '&hl=' . esc_html($lmm_options['google_maps_language_localization']);
						}
						//info: build directions provider links
						if ($lmm_options['directions_provider'] == 'googlemaps') {
							if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
							if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
							if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
							$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
							$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
							$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
							$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
							echo '<a id="panel-link-directions" tabindex="105" href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . esc_html($lmm_options[ 'directions_googlemaps_map_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_googlemaps_traffic' ]) . '&doflg=' . esc_html($lmm_options[ 'directions_googlemaps_distance_units' ]) . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . intval($lmm_options[ 'directions_googlemaps_overview_map' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
						} else if ($lmm_options['directions_provider'] == 'yours') {
							if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
							echo '<a id="panel-link-directions" tabindex="105" href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . esc_html($lmm_options[ 'directions_yours_type_of_transport' ]) . '&fast=' . intval($lmm_options[ 'directions_yours_route_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_yours_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
						} else if ($lmm_options['directions_provider'] == 'ors') {
							if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
							echo '<a id="panel-link-directions" tabindex="105" href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&wp=' . $lon . ',' . $lat . '&zoom=' . $zoom . '&routeWeigh=' . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . '&routeOpt=' . esc_html($lmm_options[ 'directions_ors_routeOpt' ]) . '&layer=' . esc_html($lmm_options[ 'directions_ors_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
						} else if ($lmm_options['directions_provider'] == 'bingmaps') {
							if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
							echo '<a id="panel-link-directions" tabindex="105" href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
						}
				}
				if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
				echo '<a id="panel-link-kml" tabindex="106" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $id . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" class="lmm-panel-api-images" /></a>';
				}
				if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
				echo '<a id="panel-link-fullscreen" tabindex="107" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
				}
				if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
				echo '<a id="panel-link-qr" tabindex="108" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $id . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
				}
				if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
				echo '<a id="panel-link-geojson" tabindex="109" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $id . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" class="lmm-panel-api-images" /></a>';
				}
				if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
				echo '<a id="panel-link-georss" tabindex="110" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" class="lmm-panel-api-images" /></a>';
				}
				if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
				echo '<a id="panel-link-wikitude" tabindex="111" href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $id . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" class="lmm-panel-api-images" /></a>';
				}
			echo '</div>'.PHP_EOL;
			echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ])) . '">' . (($markername == NULL) ? __('if set, markername will be displayed here','lmm') : stripslashes($markername)) . '</div>'.PHP_EOL;
			?>
			</div> <!--end lmm-panel-->
			<div id="selectlayer" style="height:<?php echo $mapheight; ?>px;"></div>
			<?php $gpx_panel_state = ($gpx_panel == 1) ? 'block' : 'none'; ?>
			<div id="gpx-panel-selectlayer" class="gpx-panel" style="display:<?php echo $gpx_panel_state; ?>; background: <?php echo htmlspecialchars(addslashes($lmm_options[ 'defaults_marker_panel_background_color' ])); ?>;">
			<?php
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') { $gpx_unit_distance = 'km'; $gpx_unit_elevation = 'm'; } else { $gpx_unit_distance = 'mi'; $gpx_unit_elevation = 'ft'; }
			if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
				$gpx_metadata_name = __('Track name','lmm') . ': <span class="gpx-name"></span>';
			} else { $gpx_metadata_name = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
				$gpx_metadata_start = __('Start','lmm') . ': <span class="gpx-start"></span>';
			} else { $gpx_metadata_start = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
				$gpx_metadata_end = __('End','lmm') . ': <span class="gpx-end"></span>';
			} else { $gpx_metadata_end = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
				$gpx_metadata_distance = __('Distance','lmm') . ': <span class="gpx-distance"></span> ' . $gpx_unit_distance;
			} else { $gpx_metadata_distance = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
				$gpx_metadata_duration_moving = __('Moving time','lmm') . ': <span class="gpx-duration-moving"></span> ';
			} else { $gpx_metadata_duration_moving = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
				$gpx_metadata_duration_total = __('Duration','lmm') . ': <span class="gpx-duration-total"></span> ';
			} else { $gpx_metadata_duration_total = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
				$gpx_metadata_avpace = '&#216;&nbsp;' . __('Pace','lmm') . ': <span class="gpx-avpace"></span>/' . $gpx_unit_distance;
			} else { $gpx_metadata_avpace = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
				$gpx_metadata_avhr = '&#216;&nbsp;' . __('Heart rate','lmm') . ': <span class="gpx-avghr"></span>';
			} else { $gpx_metadata_avhr = NULL; }
			if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
				$gpx_metadata_elevation_title = __('Elevation','lmm') . ':';
			} else { $gpx_metadata_elevation_title = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
				$gpx_metadata_elev_gain = '+<span class="gpx-elevation-gain"></span>' . $gpx_unit_elevation;
			} else { $gpx_metadata_elev_gain = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
				$gpx_metadata_elev_loss = '-<span class="gpx-elevation-loss"></span>' . $gpx_unit_elevation;
			} else { $gpx_metadata_elev_loss = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
				$gpx_metadata_elev_net = '(' . __('net','lmm') . ': <span class="gpx-elevation-net"></span>' . $gpx_unit_elevation . ')';
			} else { $gpx_metadata_elev_net = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
				$gpx_metadata_elev_full = '<br/>' . __('Full elevation data','lmm') . ':<br/><span class="gpx-elevation-full"></span>';
			} else { $gpx_metadata_elev_full = NULL; }
			if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
				$gpx_metadata_hr_full = '<br/>' . __('Full heart rate data','lmm') . ':<br/><span class="gpx-heartrate-full"></span>';
			} else { $gpx_metadata_hr_full = NULL; }
			$gpx_metadata_elevation_array = array($gpx_metadata_elevation_title, $gpx_metadata_elev_gain, $gpx_metadata_elev_loss, $gpx_metadata_elev_net);
			$gpx_metadata_elevation = implode(' ',$gpx_metadata_elevation_array);
			if ($gpx_metadata_elevation == '   ') { $gpx_metadata_elevation = NULL; } //info: for no trailing |
			$gpx_metadata_array_all = array($gpx_metadata_name, $gpx_metadata_start, $gpx_metadata_end, $gpx_metadata_distance, $gpx_metadata_duration_moving, $gpx_metadata_duration_total, $gpx_metadata_avpace, $gpx_metadata_avhr, $gpx_metadata_elevation, $gpx_metadata_elev_full, $gpx_metadata_hr_full);

			$gpx_metadata_array_not_null = array();
			foreach ($gpx_metadata_array_all as $key => $value) {
				if (is_null($value) === false) {
					$gpx_metadata_array_not_null[$key] = $value;
				}
			}
			$gpx_metadata = implode(' | ',$gpx_metadata_array_not_null);
			echo $gpx_metadata;
			if ( (isset($lmm_options[ 'gpx_metadata_gpx_download' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_gpx_download' ] == 1 ) ) {
				echo ' | <a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/download/?map_type=marker&map_id=' . $id . '&format=gpx') . '" title="' . esc_attr__('download GPX file','lmm') . '">' . esc_attr__('download GPX file','lmm') . ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-download-gpx.png" width="10" height="10" alt="' . esc_attr__('download GPX file','lmm') . '" class="lmm-icon-download-gpx"></a>';
			}
			?>
			</div>

			</div><!--end mapsmarker div-->
			<?php
			if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
				if ($markername != NULL) {
					$markername_popup_hidden = '<div class="popup-markername"  style="border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;">' . stripslashes(strip_tags(htmlspecialchars_decode($markername))) . '</div>';
				} else {
					$markername_popup_hidden = '';
				}
			} else {
				$markername_popup_hidden = '';
			}
			//info: sanitize popuptext (line breaks for HTML lists)
			$popuptext_sanitized = MMP_Globals::sanitize_popuptext($popuptext);
			?>

			<span style="display:none;" id="selectlayer-popuptext-hidden"><?php echo "<img id=\"popup-loading-".$id."\" style=\"display: none; margin: 20px auto;\" src=\"".LEAFLET_PLUGIN_URL."inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-".$id."\">".$markername_popup_hidden.do_shortcode($popuptext_sanitized); ?></div></span>
			<?php
			if ($lmm_options['directions_popuptext_panel'] == 'yes') {
				//info: Google language localization (directions)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
				} else {
					$google_language = '&hl=' . esc_html($lmm_options['google_maps_language_localization']);
				}
				if ($lmm_options['directions_provider'] == 'googlemaps') {
					if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
					if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
					$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
					$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
					$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
					$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
					$directionslink = "http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ];
				} else if ($lmm_options['directions_provider'] == 'yours') {
					$directionslink = "http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . intval($lmm_options[ 'directions_yours_route_type' ]) . "&layer=" . esc_html($lmm_options[ 'directions_yours_layer' ]);
				} else if ($lmm_options['directions_provider'] == 'ors') {
					$directionslink = "http://www.openrouteservice.org/?pos=" . $lon . "," . $lat . "&wp=" . $lon . "," . $lat . "&zoom=" . $zoom . "&routeWeigh=" . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . "&routeOpt=" . $lmm_options[ 'directions_ors_routeOpt' ] . "&layer=" . esc_html($lmm_options[ 'directions_ors_layer' ]);
				} else if ($lmm_options['directions_provider'] == 'bingmaps') {
					if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
					$directionslink = "https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to;
				}
				$mpopuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
				$directions_settings_link = (current_user_can("activate_plugins")) ? "<span id='toggle-popup-directions-settings' style='" . $current_editor_css_inline . "'> (<a tabindex='103' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_settings#directions' title='" . esc_attr__("change directions settings","lmm") . "'>" . __("Settings","lmm") . "</a>)</span>" : "";
				if ($address == NULL) {
					$google_from = $lat . ',' . $lon;
					echo '<span id="selectlayer-popuptext-dlink-hidden" style="display:none;"><div class="popup-directions" style="' . $mpopuptext_css . '"><span id="popup-address">' . esc_attr__('if set, address will be displayed here','lmm') . '</span> <a id="popup-directions" href="' . $directionslink . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">(' . __('Directions','lmm') . ')</a>' . $directions_settings_link . '</div></span>';
				} else {
					echo '<span id="selectlayer-popuptext-dlink-hidden" style="display:none;"><div class="popup-directions" style="' . $mpopuptext_css . '"><span id="popup-address">' . stripslashes(htmlspecialchars($address)) . '</span> <a id="popup-directions" href="' . $directionslink . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">(' . __('Directions','lmm') . ')</a>' . $directions_settings_link . '</div></span>';
				}
			} else {
				//info: outside next condition to prevent PHP warnings if disabled
				$directionslink = '';
				$directions_settings_link = '';
				//info: needed for geocoding.js, line 426
				if ($address == NULL) {
					echo '<span id="popup-address" style="display:none;">' . esc_attr__('if set, address will be displayed here','lmm') . '</span>';
				} else {
					echo '<span id="popup-address" style="display:none;">' . stripslashes(htmlspecialchars($address)) . '</span>';
				}
			} ?>
			<!--//info: preload area for CSS background images (home button etc)-->
			<div class="lmm-preload-area"></div>
		</td>
	</tr>
	<tr>
		<td class="lmm-border"><label for="default_icon"><strong><?php _e('Icon', 'lmm') ?></strong></label>
			<br/>
			<div id="mapiconscollection" style="<?php echo $current_editor_css; ?>">
			<a tabindex="122" title="Maps Icons Collection - https://mapicons.mapsmarker.com" href="https://mapicons.mapsmarker.com" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/logo-mapicons.png" width="88" heigh="31" style="<?php echo $css_whitelabel = ($lmm_options['misc_whitelabel_backend'] == 'enabled') ? 'display:none' : 'float:left;margin-right:10px;' ?>" /></a>
			<small>
			<?php
			$mapicons_admin = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and upload the new icons to the directory %2$s/','lmm'), '<a tabindex="112" href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a>', $defaults_marker_icon_url);
			$mapicons_user = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and ask your WordPress admin to upload the new icons to the directory %2$s/','lmm'), '<a tabindex="113" href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a>', $defaults_marker_icon_url);
			$upload_icons_button_text = '<br/>' . __('You can also upload the icons by clicking the button "upload new icon"','lmm');
			if (current_user_can('activate_plugins')) { echo $mapicons_admin . $upload_icons_button_text; } else { echo $mapicons_user . $upload_icons_button_text; }
			if (is_multisite()) {
			$mapicons_directory = sprintf( __('As you are running your blog within a WordPress Multisite installation, please use this icon directory on your server: %2$s/','lmm'), '<a tabindex="114" href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a>', $defaults_marker_icon_dir);
			echo '<br/><br/>' . $mapicons_directory;
			}
			?>
			</div>
			</small>
		</td>
		<td class="lmm-border"><?php
			if ($current_editor == 'simplified') {
				echo '<div id="toogle-icons-advanced" style="display:none;">';
				if ($icon == NULL) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
				echo '<div class="div-marker-icon div-marker-icon-default" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input class="marker-icon-radio-button" id="default_icon" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
				foreach ($iconlist as $row) {
					$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
					if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
					$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
					if ($icon_data == NULL) { //info: workaround #1 due to support request
						$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
					}
					if ($icon_data == NULL) { //info: workaround #2 due to support request
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
						$icon_data = curl_exec($ch);
						curl_close($ch);
					}
					$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
					echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';
				}
				echo '<input title="' . esc_attr__('start typing to filter icons by filename','lmm') . '" type="text" class="icon-search" placeholder="' . esc_attr__('filter icons','lmm') . '" />';
				if (current_user_can('upload_files')) {
					echo '<a id="upload-new-icon" class="browser button button-hero" href="#">' . __('upload new icon','lmm') . '</a>';
				}
				echo '</div>';

				echo '<div id="toogle-icons-simplified" style="display:inline;">';
				if ($icon == NULL) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
				echo '<div class="div-marker-icon div-marker-icon-default" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input class="marker-icon-radio-button" id="default_icon" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
				if ($icon != NULL) {
					$filename_without_dots = str_replace('.','_',$icon); //info: dots in not allowed in selectors
					echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="background:#5e5d5d;"><label for="' . $icon . '"><img src="' . $defaults_marker_icon_url . '/' . $icon . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_attr__('filename','lmm') . ': ' . $icon . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . $icon .'" alt="' . $icon . '" /></label><br/><input class="marker-icon-radio-button" id="' . $icon . '" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="' . $icon . '" checked="" /></div>';
					echo '<div id="moreiconslink" style="display:block;padding:7px 0 0 70px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
					echo '<div id="moreicons" style="display:none;">';
					foreach ($iconlist as $row) {
						$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
						if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
						$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						if ($icon_data == NULL) { //info: workaround #1 due to support request
							$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						}
						if ($icon_data == NULL) { //info: workaround #2 due to support request
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
							$icon_data = curl_exec($ch);
							curl_close($ch);
						}
						$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
						echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"/></div>';
					}
				} else {
					echo '<div id="moreiconslink" style="display:block;padding:7px 0 0 70px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
					echo '<div id="moreicons" style="display:none;">';
					foreach ($iconlist as $row) {
						$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
						if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
						$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						if ($icon_data == NULL) { //info: workaround #1 due to support request
							$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						}
						if ($icon_data == NULL) { //info: workaround #2 due to support request
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
							$icon_data = curl_exec($ch);
							curl_close($ch);
						}
						$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
						echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"/></div>';
					}
				}
				echo '<div style="text-align:center;float:left;line-height:0;padding:17px 5px 0 5px;" id="showlessicons"><a style="cursor:pointer;">' . __('show fewer icons','lmm') . '</a></div>';
				echo '<input title="' . esc_attr__('start typing to filter icons by filename','lmm') . '" type="text" class="icon-search" placeholder="' . esc_attr__('filter icons','lmm') . '" />';
				if (current_user_can('upload_files')) {
					echo '<a id="upload-new-icon" class="browser button button-hero" href="#">' . __('upload new icon','lmm') . '</a>';
				}
				echo '</div></div>';

			} else if ($current_editor == 'advanced') {

				echo '<div id="toogle-icons-simplified" style="display:none;">';
				if ($icon == NULL) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
				echo '<div class="div-marker-icon div-marker-icon-default" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" width="32" height="37" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input class="marker-icon-radio-button" id="default_icon" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
				if ($icon != NULL) {
					$filename_without_dots = str_replace('.','_',$icon); //info: dots in not allowed in selectors
					echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="background:#5e5d5d;"><label for="' . $icon . '"><img src="' . $defaults_marker_icon_url . '/' . $icon . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_attr__('filename','lmm') . ': ' . $icon . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . $icon .'" alt="' . $icon . '" /></label><br/><input class="marker-icon-radio-button" id="' . $icon . '" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="' . $icon . '" checked="" /></div>';
					echo '<div id="moreiconslink" style="display:block;padding:7px 0 0 70px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
					echo '<div id="moreicons" style="display:none;">';
					foreach ($iconlist as $row) {
						$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
						if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
						$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						if ($icon_data == NULL) { //info: workaround #1 due to support request
							$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						}
						if ($icon_data == NULL) { //info: workaround #2 due to support request
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
							$icon_data = curl_exec($ch);
							curl_close($ch);
						}
						$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
						echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"/></div>';
					}
				} else {
					echo '<div id="moreiconslink" style="display:block;padding:7px 0 0 70px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
					echo '<div id="moreicons" style="display:none;">';
					foreach ($iconlist as $row) {
						$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
						if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
						$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						if ($icon_data == NULL) { //info: workaround #1 due to support request
							$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
						}
						if ($icon_data == NULL) { //info: workaround #2 due to support request
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
							$icon_data = curl_exec($ch);
							curl_close($ch);
						}
						$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
						echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="' . $icon_base64 . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'/></div>';
					}
				}
				echo '<div style="text-align:center;float:left;line-height:0;padding:17px 5px 0 5px;" id="showlessicons"><a style="cursor:pointer;">' . __('show fewer icons','lmm') . '</a></div>';
				echo '<input title="' . esc_attr__('start typing to filter icons by filename','lmm') . '" type="text" class="icon-search" placeholder="' . esc_attr__('filter icons','lmm') . '" />';
				if (current_user_can('upload_files')) {
					echo '<a id="upload-new-icon" class="browser button button-hero" href="#">' . __('upload new icon','lmm') . '</a>';
				}
				echo '</div></div>';

				echo '<div id="toogle-icons-advanced" style="display:inline;">';
				if ($icon == NULL) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
				echo '<div class="div-marker-icon div-marker-icon-default" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input class="marker-icon-radio-button" id="default_icon" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
				foreach ($iconlist as $row) {
					$filename_without_dots = str_replace('.','_',$row); //info: dots in not allowed in selectors
					if ($row == $icon) { $opacity = '1'; $background = '#5e5d5d'; } else { $opacity = '0.4'; $background = 'none'; }
					$icon_data = $wp_filesystem->get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
					if ($icon_data == NULL) { //info: workaround #1 due to support request
						$icon_data = file_get_contents(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . $row);
					}
					if ($icon_data == NULL) { //info: workaround #2 due to support request
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, LEAFLET_PLUGIN_ICONS_URL . '/' . $row);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
						$icon_data = curl_exec($ch);
						curl_close($ch);
					}
					$icon_base64 = 'data:image/png;base64,' . base64_encode($icon_data);
					echo '<div class="div-marker-icon div-marker-icon-' . $filename_without_dots . '" style="opacity:' . $opacity . ';background:' . $background . ';"><label for="'.$row.'"><img src="'. $icon_base64 .'" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" /></label><br/><input class="marker-icon-radio-button" id="'.$row.'" style="margin:1px 0 0 1px;display:none;" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';
				}
				echo '<input title="' . esc_attr__('start typing to filter icons by filename','lmm') . '" type="text" class="icon-search" placeholder="' . esc_attr__('filter icons','lmm') . '" />';
				if (current_user_can('upload_files')) {
					echo '<a id="upload-new-icon" class="browser button button-hero" href="#">' . __('upload new icon','lmm') . '</a>';
				}
				echo '</div>';
			}
			$noncelink_uploadicon = wp_create_nonce('icon-upload-nonce');
			?>
			<script type="text/javascript">
			jQuery(function($) {
				$(document).ready(function(){
					$(document).on('click', '#upload-new-icon', function(){
						tb_show('<?php esc_attr_e('Upload new icon','lmm'); ?>', '<?php echo MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/upload/?_wpnonceicon=' . $noncelink_uploadicon); ?>&TB_iframe&width=450&height=200');
						<?php
						if ( version_compare( $wp_version, '3.9', '<' ) ) {
							echo "jQuery('#TB_overlay').css('z-index','100050');".PHP_EOL;
							echo "jQuery('#TB_window').css('z-index','100050');".PHP_EOL;
						} ?>
							return false;
						});
					});
				});
			</script></div>
		</td>
	</tr>
	<tr>
		<td class="lmm-border"><label for="popuptext"><strong><?php _e('Popup text','lmm') ?></strong></label>
		<br /><br />
		<label for="openpopup"><?php _e('open popup','lmm') ?></label>&nbsp;&nbsp;<input type="checkbox" name="openpopup" id="openpopup" <?php checked($openpopup, 1 ); ?>>
		<br/><small>
		<?php _e('If unchecked, the popup will only be visible after clicking on the marker on marker- or layer-maps.','lmm') ?>
		</small>
		<br /><br />
		<label for="add-markername"><?php _e('add markername','lmm') ?></label>&nbsp;&nbsp;<?php if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') { echo '<input type="checkbox" name="add-markername" id="add-markername" checked="checked" disabled="disabled">'; } else { echo '<input type="checkbox" name="add-markername" id="add-markername" disabled="disabled">'; } ?>
		<br/><small><a tabindex="119" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-marker_popups"><?php _e('Please visit Settings to change this globally','lmm'); ?></a></small>
		<br /><br />
		<label for="add-directions"><?php _e('add directions link','lmm') ?></label>&nbsp;&nbsp;<?php if ($lmm_options['directions_popuptext_panel'] == 'yes') { echo '<input type="checkbox" name="add-directions" id="add-directions" checked="checked" disabled="disabled">'; } else { echo '<input type="checkbox" name="add-directions" id="add-directions" disabled="disabled">'; } ?>
		<br/><small><a tabindex="119" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-directions"><?php _e('Please visit Settings to change this globally','lmm'); ?></a></small>
		</p>
		</td>
		<td class="lmm-border">
		<script type="text/javascript">var unsaved = false;</script>
		<?php
			if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
				$popup_markername = '<div class=\"popup-markername\"  style=\"border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;\">"+document.getElementById("markername").value+"</div>';
			} else {
				$popup_markername = '';
			}
			if ($lmm_options['directions_popuptext_panel'] == 'yes') {
				$popup_panel = '+popup_panel';
			} else {
				$popup_panel = '';
			}
			if ( version_compare( $wp_version, '3.9-alpha', '>=' ) ) {

				$settings = array(
					'wpautop' => true,
					'tinymce' => array(
						'height' => '250',
						'content_style' => 'img {' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '} a {text-decoration:none;} a:hover {text-decoration:underline;}',
						'setup' => 'function(ed) {
										ed.on("keyup", function(ed,e) {
											var popup_markername = "' . $popup_markername . '";
											var popup_panel = "<div style=\"border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;\">"+document.getElementById("address").value+" <a href=\"' . $directionslink . '\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a>' . $directions_settings_link . '</div>";
											if (marker.getPopup() === null) { marker.bindPopup(); } //info: needed for markers with empty popuptext (& no direction link added)
											marker._popup.setContent(popup_markername+tinyMCE.activeEditor.getContent()' . $popup_panel . ');
											unsaved = true;
										});
										//info: remove disabled attribute for HTML editor mode only - text mode see line 2600
										ed.on("LoadContent", function(ed, e){
										    jQuery("#submit_top").removeAttr("disabled");
										    jQuery("#submit_bottom").removeAttr("disabled");
										    jQuery("#duplicate_button_top").removeAttr("disabled");
										    jQuery("#duplicate_button_bottom").removeAttr("disabled");
										    jQuery("#delete_button_top").removeAttr("disabled");
										    jQuery("#delete_button_bottom").removeAttr("disabled");
										});
								}'
					 ),
				'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,code,close'));
			} else {
				$settings = array(
					'wpautop' => true,
					'tinymce' => array(
						'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,link,unlink,|,ltr,rtl',
						'theme' => 'advanced',
						'height' => '250',
						'content_style' => 'html .mcecontentbody {font:12px/1.4 "Helvetica Neue",Arial,Helvetica,sans-serif; max-width:' . intval($lmm_options['defaults_marker_popups_maxwidth']) + 1 . 'px; /* Default + 1 fix */ word-wrap:break-word;} .mcecontentbody a {text-decoration:none;} .mcecontentbody a:hover {text-decoration:underline;} .mcecontentbody img {' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '}',
						'theme_advanced_statusbar_location' => 'bottom',
						'setup' => 'function(ed) {
							ed.onKeyUp.add(function(ed, e) {
								var popup_markername = "' . $popup_markername . '";
								var popup_panel = "<div style=\"border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;\">"+document.getElementById("address").value+" <a href=\"' . $directionslink . '\" target=\"_blank\" title=\"' . esc_attr__('Get directions','lmm') . '\">(' . __('Directions','lmm') . ')</a>' . $directions_settings_link . '</div>";
								if (marker.getPopup() === null) { marker.bindPopup(); } //info: needed for markers with empty popuptext (& no direction link added)
								marker._popup.setContent(popup_markername+ed.getContent()' . $popup_panel . ');
								unsaved = true;
							});
							ed.onInit.add(function(ed) {
								jQuery("#submit_top").removeAttr("disabled");
								jQuery("#submit_bottom").removeAttr("disabled");
								jQuery("#duplicate_button_top").removeAttr("disabled");
								jQuery("#duplicate_button_bottom").removeAttr("disabled");
								jQuery("#delete_button_top").removeAttr("disabled");
								jQuery("#delete_button_bottom").removeAttr("disabled");
							});
						}'
					),
				'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,code,close'));
		}
		wp_editor( $popuptext_sanitized, 'popuptext', $settings);
		?>
		<?php if ($isedit === true) { ?>  <span class="wpml-markertranslatelink wpml-markerpopuptext">(<a target="_blank" href="<?php echo $translate_url_markerpopuptext; ?>"><?php _e('translate', 'lmm'); ?></a>) </span> <?php }else{  ?> <span class="wpml-markertranslatelink wpml-markerpopuptext"> (<a target="_blank" href="<?php echo $translate_url_markerpopuptext; ?>"><?php _e('translate', 'lmm'); ?></a>)</span><?php } ?>
		<br />
		<small>
			<?php
			echo '<span id="popup-image-css-info" style="' . $current_editor_css_audit . '">' . sprintf( esc_attr__('Note: if you add an image, the following CSS definition will be applied: %1$s','lmm'), '<code>' . htmlspecialchars($lmm_options['defaults_marker_popups_image_css']) . '</code>');
			if (current_user_can('activate_plugins')) { echo ' <a tabindex="102" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-marker_popups" title="' . esc_attr__('can be changed at section "Default values for marker popups"','lmm') . '">(' . __('Settings','lmm') . ')</a>'; }
			?>
			</span>
			</small>
		</td>
	</tr>
	<tr id="toggle-advanced-settings" style="<?php echo $current_editor_css_audit; ?>">
		<td class="lmm-border"><strong><?php _e('Advanced settings','lmm') ?></strong></td>
		<td class="lmm-border">
			<p><strong><?php _e('WMS layers','lmm') ?></strong> <?php if (current_user_can('activate_plugins')) { echo '<a tabindex="101" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#wms">(' . __('Settings','lmm') . ')</a>'; } ?></p>
			<?php
			//info: define available wms layers (for markers and layers)
			if ( (isset($lmm_options[ 'wms_wms_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms" name="wms"';
				if ($wms == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms">' . strip_tags($lmm_options[ 'wms_wms_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 1 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms1"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms2_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms2_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms2" name="wms2"';
				if ($wms2 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms2">' . strip_tags($lmm_options[ 'wms_wms2_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 2 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms2"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms3_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms3_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms3" name="wms3"';
				if ($wms3 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms3">' . strip_tags($lmm_options[ 'wms_wms3_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 3 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms3"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms4_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms4_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms4" name="wms4"';
				if ($wms4 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms4">' . strip_tags($lmm_options[ 'wms_wms4_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 4 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms4"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms5_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms5_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms5" name="wms5"';
				if ($wms5 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms5">' . strip_tags($lmm_options[ 'wms_wms5_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 5 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms5"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms6_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms6_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms6" name="wms6"';
				if ($wms6 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms6">' . strip_tags($lmm_options[ 'wms_wms6_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 6 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms6"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms7_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms7_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms7" name="wms7"';
				if ($wms7 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms7">' . strip_tags($lmm_options[ 'wms_wms7_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 7 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms7"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms8_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms8_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms8" name="wms8"';
				if ($wms8 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms8">' . strip_tags($lmm_options[ 'wms_wms8_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 8 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms8"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms9_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms9_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms9" name="wms9"';
				if ($wms9 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms9">' . strip_tags($lmm_options[ 'wms_wms9_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 9 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms9"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/>';
			}
			if ( (isset($lmm_options[ 'wms_wms10_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms10_available' ] == 1 ) ) {
				echo '<input type="checkbox" id="wms10" name="wms10"';
				if ($wms10 == 1) { echo ' checked="checked"'; }
				echo '/>&nbsp;<label for="wms10">' . strip_tags($lmm_options[ 'wms_wms10_name' ]) . ' </label> <a title="' . esc_attr__('WMS layer 10 settings','lmm') . '" tabindex="104" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-wms-wms10"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>';
			}
			?>

			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">

			<label for="kml_timestamp"><strong><?php _e('Timestamp for KML animation','lmm') ?>:</strong></label> <a tabindex="104" href="https://www.mapsmarker.com/kml-timestamp" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('Click here for more information on animations in KML/Google Earth','lmm'); ?>" width="12" height="12" border="0"/></a><br/>
			<input type="<?php echo $datetime_ios_workaround; ?>" id="kml_timestamp" name="kml_timestamp" value="<?php echo $kml_timestamp; ?>" /><br/>
			<small><?php _e('If empty, marker creation date will be used','lmm') ?></small>
			<?php
				if (current_user_can('activate_plugins')) {
					if ( $lmm_options['misc_backlinks'] == 'show' ) {
						echo '<hr style="border:none;color:#edecec;background:#edecec;height:1px;"><strong>' . __('Hide MapsMarker.com backlinks','lmm') .'</strong>: ';
						echo '<a tabindex="110" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc">' . __('Please visit Settings / Misc to disable MapsMarker.com backlinks','lmm') . '</a>';
					}
				}
			?>
			<?php if (current_user_can('activate_plugins')) { ?>
			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
			<strong><?php _e('Minimap settings','lmm'); ?> </strong>
			<a tabindex="110" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-minimap"><?php _e('Please visit Settings / Maps / Minimap settings','lmm'); ?></a>
			<hr style="border:none;color:#edecec;background:#edecec;height:1px;">
			<strong><?php _e('Geolocate settings','lmm'); ?> </strong>
			<a tabindex="111" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-geolocate"><?php _e('Please visit Settings / Maps / Geolocate settings','lmm'); ?></a>
			<?php } ?>
		</td>
	</tr>

	<tr id="toggle-audit" style="<?php echo $current_editor_css_audit; ?>">
		<td class="lmm-border"><small><strong><?php _e('Audit','lmm') ?></strong></small></td>
		<td class="lmm-border"><small>
			<?php
			echo __('Marker added by','lmm') . ' ';
			if (current_user_can('activate_plugins')) {
				echo '<input title="' . esc_attr__('Please use valid WordPress usernames as otherwise non-admins might not be able to access this map on backend (depending on your access settings)','lmm') . '" type="text" id="createdby" name="createdby" value="' . esc_html($mcreatedby) . '" style="font-size:small;width:110px;height:24px;" />';
				echo '<input type="' . $datetime_ios_workaround . '" id="createdon" name="createdon" value="' . date('Y-m-d\TH:i:s', strtotime($mcreatedon)) . '" style="font-size:small;height:24px;" /> ';
				if ($mupdatedon == $mcreatedon) { $audit_visibility = 'none'; } else { $audit_visibility = 'inline'; }
				echo '<span id="audit_visibility" style="display:' . $audit_visibility . ';">' . __('last update by','lmm');
				echo ' <input type="text" id="updatedby" name="updatedby" value="' . esc_html($mupdatedby) . '" style="font-size:small;width:110px;height:24px;" disabled="disabled" />';
				echo '<input type="' . $datetime_ios_workaround . '" id="updatedon" name="updatedon" value="' . date('Y-m-d\TH:i:s', strtotime($mupdatedon)) . '" style="font-size:small;height:24px;" disabled="disabled" /> </span>';
				echo __('next update by','lmm');
				echo ' <input title="' . esc_attr__('Please use valid WordPress usernames as otherwise non-admins might not be able to access this map on backend (depending on your access settings)','lmm') . '" type="text" id="updatedby_next" name="updatedby_next" value="' . $current_user->user_login . '" style="font-size:small;width:110px;height:24px;" />';
				echo '<input type="' . $datetime_ios_workaround . '" id="updatedon_next" name="updatedon_next" value="' . date('Y-m-d\TH:i:s', strtotime(current_time('mysql',0))) . '" style="font-size:small;height:24px;" />';
			} else {
				echo '<input type="hidden" id="createdby" name="createdby" value="' . esc_html($mcreatedby) . '" />';
				echo '<input type="hidden" id="createdon" name="createdon" value="' . $mcreatedon . '" />';
				echo '<input type="hidden" id="updatedby_next" name="updatedby_next" value="' . $current_user->user_login . '" />';
				echo '<input type="hidden" id="updatedon_next" name="updatedon_next" value="' . current_time('mysql',0) . '" />';
				echo esc_html($mcreatedby) . ' - ' . $mcreatedon;
				if ($mupdatedon != $mcreatedon) {
					echo ', ' . __('last update by','lmm');
					echo ' ' . esc_html($mupdatedby) . ' - ' . $mupdatedon . ', ';
				}
				echo ' ' . __('next update by','lmm');
				echo ' ' . $current_user->user_login . ' - ' . current_time('mysql',0);
			}
			?>
			</small></td>
	</tr>
</table>

<table style="margin:15px 0;"><tr><td>
<?php
	if (MMP_Globals::check_capability('edit', $mcreatedby)) {
		if ($isedit === true) { $button_text = __('update','lmm'); } else { $button_text = __('publish','lmm'); }
		echo '<input id="submit_bottom" disabled="disabled" style="font-weight:bold;" type="submit" name="marker" class="button button-primary" value="' . $button_text . '" disabled="disabled" />';
		echo '<img src="' . admin_url('/images/wpspin_light.gif') . '" class="waiting" id="lmm_ajax_loading_bottom" style="margin-left:5px;display:none;"/>';
	} else {
		if ($isedit === true) {
			echo '<span style="font-size:13px;margin-left:20px;font-weight:normal;">' . __('Your user does not have the permission to update this marker!','lmm') . '</span>';
		} else {
			echo '<span style="font-size:13px;margin-left:20px;font-weight:normal;">' . __('Your user does not have the permission to add a new marker!','lmm') . '</span>';
		}
	}
?>
</td>
<?php
	//info: duplicate button bottom
	echo '<td>';
		if (MMP_Globals::check_capability('edit', $mcreatedby)) {
			echo '<span id="duplicate_span_bottom" style="margin:0 0 0 50px;' . $duplicate_delete_button_visibility . '">';
			echo '<a id="duplicate_button_bottom" href="javascript:void(0);" class="button button-secondary" style="font-size:13px;text-decoration:none;" disabled="disabled">' . __('duplicate', 'lmm') . '</a>';
			echo '</span>';
		} else {
			if ($isedit === true) {
				echo '<span id="duplicate_span_bottom" style="font-size:13px;margin-left:20px;">' . __('Your user does not have the permission to duplicate this marker!','lmm') . '</span>';
			}
		}
	echo '</td><td>';
	//info: delete button bottom
	if (MMP_Globals::check_capability('delete', $mcreatedby)) {
		echo '<span id="delete_span_bottom" style="margin:0 0 0 50px;' . $duplicate_delete_button_visibility . '">';
		echo '<a id="delete_button_bottom" href="javascript:void(0);" id="marker-delete" class="button button-secondary" style="font-size:13px;text-decoration:none;color:#FF0000;" disabled="disabled">' . __('delete', 'lmm') . '</a>';
		echo '</span>';
	} else {
		if ($isedit === true) {
			echo '<span id="delete_span_bottom" style="font-size:13px;margin-left:20px;">' . __('Your user does not have the permission to delete this marker!','lmm') . '</span>';
		}
	}
	echo '</td>';
?>

</div>
</tr></table>
</form>

<div style="height:30px;padding:0 0 15px 0;">
	<div id="lmm_ajax_results_bottom" style="padding:10px;display:none;"></div>
</div>

<script type="text/javascript">
/* //<![CDATA[ */
var marker,selectlayer,googleLayer_roadmap,googleLayer_satellite,googleLayer_hybrid,googleLayer_terrain,bingaerial,bingaerialwithlabels,bingroad,osm_mapnik,stamen_terrain,stamen_toner,stamen_watercolor,mapquest_osm,mapquest_aerial,mapquest_hybrid,ogdwien_basemap,ogdwien_satellite,mapbox,mapbox2,mapbox3,custom_basemap,custom_basemap2,custom_basemap3,empty_basemap,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl;
(function($) {
	<?php
	$maxzoom = intval($lmm_options['global_maxzoom_level']);
	$dragging_setting = ($lmm_options['misc_map_dragging'] == 'false-touch') ? '!L.Browser.mobile' : esc_js($lmm_options['misc_map_dragging']);
	 //info: true for leaflet-fullscreen.php only
	if ($lmm_options['misc_map_scrollwheelzoom'] == 'true') {
		$scrollwheelzoom_setting = 'true';
	} else if ( ($lmm_options['misc_map_scrollwheelzoom'] == 'true-fullscreen-only') || ($lmm_options['misc_map_scrollwheelzoom'] == 'false') ){
		$scrollwheelzoom_setting = 'false';
	}
	?>
	selectlayer = new L.Map("selectlayer", { dragging: <?php echo $dragging_setting ?>, touchZoom: <?php echo esc_js($lmm_options['misc_map_touchzoom']); ?>, scrollWheelZoom: <?php echo $scrollwheelzoom_setting ?>, doubleClickZoom: <?php echo esc_js($lmm_options['misc_map_doubleclickzoom']); ?>, boxzoom: <?php echo esc_js($lmm_options['map_interaction_options_boxzoom']); ?>, trackResize: <?php echo esc_js($lmm_options['misc_map_trackresize']); ?>, worldCopyJump: <?php echo esc_js($lmm_options['map_interaction_options_worldcopyjump']); ?>, closePopupOnClick: <?php echo esc_js($lmm_options['misc_map_closepopuponclick']); ?>, keyboard: <?php echo esc_js($lmm_options['map_keyboard_navigation_options_keyboard']); ?>, keyboardPanDelta: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardpandelta']) ?>, inertia: <?php echo esc_js($lmm_options['map_panning_inertia_options_inertia']); ?>, inertiaDeceleration: <?php echo intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) ?>, inertiaMaxSpeed: <?php echo intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) ?>, zoomControl: <?php echo $lmm_options['misc_map_zoomcontrol']; ?>, crs: <?php echo esc_js($lmm_options['misc_projections']); ?>, fullscreenControl: <?php echo esc_js($lmm_options['map_fullscreen_button']); ?>, tap: <?php echo esc_js($lmm_options['map_interaction_options_tap']); ?>, tapTolerance: <?php echo intval($lmm_options['map_interaction_options_taptolerance']) ?>, bounceAtZoomLimits: <?php echo esc_js($lmm_options['map_interaction_options_bounceatzoomlimits']); ?> });

	//info: workaround for #230/#377 ("Uncaught Map has no maxZoom specified") (Google Mutant)
	selectlayer._layersMaxZoom = <?php echo $maxzoom; ?>;

	<?php
	if ( $lmm_options['misc_backlinks'] == 'show' ) {
		$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
		$attrib_prefix = '<a tabindex=\"115\" href=\"https://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Maps Marker Pro - #1 mapping plugin for WordPress','lmm') . '\">MapsMarker.com</a> (<a tabindex=\"116\" href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s is based on Leaflet.js maintained by Vladimir Agafonkin','lmm'), 'Maps Marker Pro') . '\">Leaflet</a>/<a tabindex=\"117\" href=\"https://mapicons.mapsmarker.com\" target=\"_blank\" title=\"' . sprintf(esc_attr__('%1$s uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm'), 'Maps Marker Pro') . '\">icons</a>)';
	} else {
		$attrib_prefix = '';
	}
	$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a tabindex=\"119\" href=\"https://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
	$attrib_stamen = '<a target=\"_blank\" href=\"http://maps.stamen.com/\">' . esc_attr__('Map tiles','lmm') . '</a>: <a target=\"_blank\" href=\"http://stamen.com\">Stamen Design</a>, <a target=\"_blank\" href=\"https://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>, ' . esc_attr__('Data','lmm') . ' &copy <a target=\"blank\" href=\"https://www.openstreetmap.org/copyright\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	$attrib_basemapat = __("Map",'lmm').': <a href=\"https://www.basemap.at\" target=\"_blank\" style=\"\">basemap.at</a>';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap_attribution' ], $allowedtags));
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap2_attribution' ], $allowedtags));
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes(wp_kses($lmm_options[ 'custom_basemap3_attribution' ], $allowedtags));
	?>
	selectlayer.attributionControl.setPrefix("<?php echo $attrib_prefix; ?>");

	<?php
	//info: osm_mapnik js name+db entries not renamed due to backward compatibility reasons!
	$osm_attrib_general = __("Map",'lmm').': &copy; <a tabindex=\"123\" href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>';
	if ($lmm_options['openstreetmap_variants'] == 'osm-mapnik') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 19;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-blackandwhite') {
		$osm_tile_url = 'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 18;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-de') {
		$osm_tile_url = 'http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 19;
		$osm_attribution = $osm_attrib_general . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-france') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = __("Map",'lmm').': &copy; <a tabindex=\"123\" href=\"https://www.openstreetmap.fr\" target=\"_blank\">Openstreetmap France</a> & <a tabindex=\"123\" href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
	} else if ($lmm_options['openstreetmap_variants'] == 'osm-hot') {
		$osm_tile_url = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
		$osm_maxNativeZoom = 20;
		$osm_attribution = $osm_attrib_general . ', ' . __("Tiles courtesy of","lmm") . ' <a  tabindex=\"123\" href=\"https://hotosm.org/\" target=\"_blank\">Humanitarian OpenStreetMap Team</a>' . $osm_editlink;
	}
	//info: define edgeBuffertiles
	if ($lmm_options['map_interaction_options_bounceatzoomlimits'] != '0') {
		$edgebuffertiles = ', edgeBufferTiles: ' . floatval(str_replace(",",".", $lmm_options['edgeBufferTiles']));
	} else {
		$edgebuffertiles = '';
	}
	$error_tile_url = $lmm_options['basemaps_nowrap_enabled'] == 'true' ? '' : LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png';
	?>
	osm_mapnik = new L.TileLayer("<?php echo $osm_tile_url; ?>", {mmid: 'osm_mapnik', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo $osm_maxNativeZoom; ?>, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $osm_attribution; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	stamen_terrain = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/<?php echo esc_html($lmm_options[ 'stamen_terrain_flavor' ]); ?>/{z}/{x}/{y}.png", {mmid: 'stamen_terrain', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	stamen_toner = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/<?php echo esc_html($lmm_options[ 'stamen_toner_flavor' ]); ?>/{z}/{x}/{y}.png", {mmid: 'stamen_toner', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 20, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	stamen_watercolor = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {mmid: 'stamen_watercolor', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", attribution: "<?php echo $attrib_stamen; ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});

	<?php
	if ($lmm_options['mapquest_api_key'] != NULL) {
		echo 'if (typeof MQ !== "undefined") {';
		echo 'mapquest_osm = new MQ.mapLayer({mmid: "mapquest_osm"});';
		echo 'mapquest_aerial = new MQ.satelliteLayer({mmid: "mapquest_aerial"});';
		echo 'mapquest_hybrid = new MQ.hybridLayer({mmid: "mapquest_hybrid"});';
		echo '} else { alert("' . sprintf(esc_attr__('An issue with your MapQuest API key %1$s occured - please check the support forum at %2$s for more details','lmm'), esc_js(trim($lmm_options['mapquest_api_key'])), 'https://developer.mapquest.com/forum') . '"); }';
	}

	if ($lmm_options['google_maps_api_status'] == 'enabled') {

		if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === TRUE) ) {
			$lmmjs_out .= 'if (window.console) { console.log("' . esc_attr__('You are using an outdated browser therefore maps automatically switched to OpenStreetMap. An update to a current browser is recommended.','lmm') . '"); }'.PHP_EOL;
		} elseif ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

			if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js' . $google_maps_api_key . $google_language;
				echo 'var deferred_google_layers = {
								roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
								satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
								hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
								terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }}
						};
						var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
						var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
						var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
						var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
			} else { //info: undeferred loading
				echo 'var googleLayer_roadmap = new L.gridLayer.googleMutant({type: "roadmap", mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				echo 'var googleLayer_satellite = new L.gridLayer.googleMutant({type: "satellite", mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				echo 'var googleLayer_hybrid = new L.gridLayer.googleMutant({type: "hybrid", mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				echo 'var googleLayer_terrain = new L.gridLayer.googleMutant({type: "terrain", mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
			}

		} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {

			if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '&key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js?callback=L.Google.asyncInitialize' . $google_maps_api_key . $google_language;
				echo 'var deferred_google_layers = {
								roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
								satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
								hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
								terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }}
						};
						var googleLayer_roadmap = new L.DeferredLayer(deferred_google_layers.roadmap);
						var googleLayer_satellite = new L.DeferredLayer(deferred_google_layers.satellite);
						var googleLayer_hybrid = new L.DeferredLayer(deferred_google_layers.hybrid);
						var googleLayer_terrain = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
			} else { //info: undeferred loading
				echo 'var googleLayer_roadmap = new L.Google("ROADMAP", {mmid: "googleLayer_roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				echo 'var googleLayer_satellite = new L.Google("SATELLITE", {mmid: "googleLayer_satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				echo 'var googleLayer_hybrid = new L.Google("HYBRID", {mmid: "googleLayer_hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				echo 'var googleLayer_terrain = new L.Google("TERRAIN", {mmid: "googleLayer_terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
			}
		}
	}
	?>

	<?php if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) { ?>
		bingaerial = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingaerial', type: 'Aerial', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		bingaerialwithlabels = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingaerialwithlabels', type: 'AerialWithLabels', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
		bingroad = new L.BingLayer("<?php echo htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])); ?>", {mmid: 'bingroad', type: 'Road', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "<?php echo $error_tile_url ?>", detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	<?php }; ?>
	ogdwien_basemap = new L.TileLayer("https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png", {mmid: 'ogdwien_basemap', errorTileUrl: "<?php echo $error_tile_url ?>", maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, attribution: "<?php echo $attrib_basemapat; ?>", subdomains: ['maps1', 'maps2', 'maps3', 'maps4'], detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	ogdwien_satellite = new L.TileLayer("https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_satellite', errorTileUrl: "<?php echo $error_tile_url ?>", maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, attribution: "<?php echo $attrib_basemapat; ?>", subdomains: ['maps1', 'maps2', 'maps3', 'maps4'], detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	//info: MapBox basemaps
	<?php
	if ($lmm_options[ 'mapbox_access_token' ] != NULL) {
		echo 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox_access_token' ])) . '&secure=1", {mmid: "mapbox", minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
	} else {  //info: v3 fallback for default maps
		echo 'var mapbox = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox_map' ])) . '/{z}/{x}/{y}.png", {mmid: "mapbox", minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
	}
	if ($lmm_options[ 'mapbox2_access_token' ] != NULL) {
		echo 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox2_access_token' ])) . '&secure=1", {mmid: "mapbox2", minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
	} else {
		echo 'var mapbox2 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox2_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox2_map' ])) . '/{z}/{x}/{y}.png", {mmid: "mapbox2", minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox2_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
	}
	if ($lmm_options[ 'mapbox3_access_token' ] != NULL) {
		echo 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v4/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png?access_token=' . esc_js(trim($lmm_options[ 'mapbox3_access_token' ])) . '&secure=1", {mmid: "mapbox3", minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
	} else {
		echo 'var mapbox3 = new L.TileLayer("https://{s}.tiles.mapbox.com/v3/' . htmlspecialchars(trim($lmm_options[ 'mapbox3_user' ])) . '.' . htmlspecialchars(trim($lmm_options[ 'mapbox3_map' ])) . '/{z}/{x}/{y}.png", {mmid: "mapbox3", minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . $error_tile_url . '", attribution: "' . addslashes(wp_kses($lmm_options[ 'mapbox3_attribution' ], $allowedtags)) . '", subdomains: ["a","b","c","d"], detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
	}
	?>
	//info: check if subdomains are set for custom basemaps
	<?php
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'custom_basemap3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	?>
	var custom_basemap = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap_tileurl' ]) ?>", {mmid: 'custom_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, tms: <?php echo esc_js($lmm_options[ 'custom_basemap_tms' ]); ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap_continuousworld_enabled' ]; ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap_nowrap_enabled' ]; ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>});
	var custom_basemap2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap2_tileurl' ]) ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, tms: <?php echo esc_js($lmm_options[ 'custom_basemap2_tms' ]); ?>, <?php echo $error_tile_url_custom_basemap2; ?>attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap2_continuousworld_enabled' ]; ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap2_nowrap_enabled' ]; ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>});
	var custom_basemap3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'custom_basemap3_tileurl' ]) ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, tms: <?php echo esc_js($lmm_options[ 'custom_basemap3_tms' ]); ?>, <?php echo $error_tile_url_custom_basemap3; ?>attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap3_continuousworld_enabled' ]; ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap3_nowrap_enabled' ]; ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>});
	var empty_basemap = new L.TileLayer("", {mmid: 'empty_basemap'});

	//info: check if subdomains are set for custom overlays
	<?php
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'overlays_custom4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
	?>

	overlays_custom = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom_tileurl' ]) ?>", {olid: 'overlays_custom', tms: <?php echo $lmm_options[ 'overlays_custom_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom_minzoom' ]) ?><?php echo $overlays_custom_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	overlays_custom2 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom2_tileurl' ]) ?>", {olid: 'overlays_custom2', tms: <?php echo $lmm_options[ 'overlays_custom2_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom2; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom2_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom2_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom2_minzoom' ]) ?><?php echo $overlays_custom2_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	overlays_custom3 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom3_tileurl' ]) ?>", {olid: 'overlays_custom3', tms: <?php echo $lmm_options[ 'overlays_custom3_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom3; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom3_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom3_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom3_minzoom' ]) ?><?php echo $overlays_custom3_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	overlays_custom4 = new L.TileLayer("<?php echo str_replace('"','&quot;',$lmm_options[ 'overlays_custom4_tileurl' ]) ?>", {olid: 'overlays_custom4', tms: <?php echo $lmm_options[ 'overlays_custom4_tms' ]; ?>, <?php echo $error_tile_url_overlays_custom4; ?>attribution: "<?php echo addslashes(wp_kses($lmm_options[ 'overlays_custom4_attribution' ], $allowedtags)) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom4_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom4_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom4_minzoom' ]) ?><?php echo $overlays_custom4_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});

	//info: check if subdomains are set for wms layers
	<?php
	$wms_subdomains = ((isset($lmm_options[ 'wms_wms_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms2_subdomains = ((isset($lmm_options[ 'wms_wms2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms2_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms3_subdomains = ((isset($lmm_options[ 'wms_wms3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms3_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms4_subdomains = ((isset($lmm_options[ 'wms_wms4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms4_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms5_subdomains = ((isset($lmm_options[ 'wms_wms5_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms5_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms5_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms6_subdomains = ((isset($lmm_options[ 'wms_wms6_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms6_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms6_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms7_subdomains = ((isset($lmm_options[ 'wms_wms7_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms7_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms7_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms8_subdomains = ((isset($lmm_options[ 'wms_wms8_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms8_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms8_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms9_subdomains = ((isset($lmm_options[ 'wms_wms9_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms9_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms9_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";
	$wms10_subdomains = ((isset($lmm_options[ 'wms_wms10_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms10_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode(wp_kses($lmm_options[ 'wms_wms10_subdomains_names' ], $allowedtags), ENT_QUOTES) . "]" :  "";

	//info: define wms legends
	$wms_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms2_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms2_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms2_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms2_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms3_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms3_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms3_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms3_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms4_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms4_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms4_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms4_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms5_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms5_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms5_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms5_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms6_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms6_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms6_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms6_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms7_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms7_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms7_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms7_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms8_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms8_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms8_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms8_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms9_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms9_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms9_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms9_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	$wms10_attribution = addslashes(wp_kses($lmm_options[ 'wms_wms10_attribution' ], $allowedtags)) . ( (($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) && ($lmm_options[ 'wms_wms10_legend' ] != NULL )) ? ' (<a href="' . wp_kses($lmm_options[ 'wms_wms10_legend' ], $allowedtags) . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
	?>

	//info: define wms layers
	wms = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms_baseurl' ]) ?>", {wmsid: 'wms', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_format' ]))?>', attribution: '<?php echo $wms_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms_version' ]))?>'<?php echo $wms_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms2 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms2_baseurl' ]) ?>", {wmsid: 'wms2', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_format' ]))?>', attribution: '<?php echo $wms2_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms2_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms2_version' ]))?>'<?php echo $wms2_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms3 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms3_baseurl' ]) ?>", {wmsid: 'wms3', layers: '<?php echo htmlspecialchars(htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_layers' ])))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_format' ]))?>', attribution: '<?php echo $wms3_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms3_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms3_version' ]))?>'<?php echo $wms3_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms4 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms4_baseurl' ]) ?>", {wmsid: 'wms4', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_format' ]))?>', attribution: '<?php echo $wms4_attribution ?>', transparent: '<?php echo $lmm_options[ 'wms_wms4_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms4_version' ]))?>'<?php echo $wms4_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms5 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms5_baseurl' ]) ?>", {wmsid: 'wms5', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_format' ]))?>', attribution: '<?php echo $wms5_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms5_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms5_version' ]))?>'<?php echo $wms5_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms6 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms6_baseurl' ]) ?>", {wmsid: 'wms6', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_format' ]))?>', attribution: '<?php echo $wms6_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms6_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms6_version' ]))?>'<?php echo $wms6_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms7 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms7_baseurl' ]) ?>", {wmsid: 'wms7', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_format' ]))?>', attribution: '<?php echo $wms7_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms7_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms7_version' ]))?>'<?php echo $wms7_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms8 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms8_baseurl' ]) ?>", {wmsid: 'wms8', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_format' ]))?>', attribution: '<?php echo $wms8_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms8_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms8_version' ]))?>'<?php echo $wms8_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms9 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms9_baseurl' ]) ?>", {wmsid: 'wms9', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_format' ]))?>', attribution: '<?php echo $wms9_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms9_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms9_version' ]))?>'<?php echo $wms9_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	wms10 = new L.TileLayer.WMS("<?php echo htmlspecialchars($lmm_options[ 'wms_wms10_baseurl' ]) ?>", {wmsid: 'wms10', layers: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_layers' ]))?>', styles: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_styles' ]))?>', format: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_format' ]))?>', attribution: '<?php echo $wms10_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms10_transparent' ]; ?>', errorTileUrl: "<?php echo $error_tile_url ?>", version: '<?php echo htmlspecialchars(addslashes($lmm_options[ 'wms_wms10_version' ]))?>'<?php echo $wms10_subdomains ?>, detectRetina: <?php echo esc_js($lmm_options['map_retina_detection']) . $edgebuffertiles; ?>, noWrap: <?php echo esc_js($lmm_options['basemaps_nowrap_enabled']) ?>});
	//info: controlbox - define basemaps

	layersControl = new L.Control.Layers(
	{
	<?php
		$basemaps_available = "";
		if ( (isset($lmm_options[ 'controlbox_osm_mapnik' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_mapnik' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_osm_mapnik' ])) . "': osm_mapnik,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_terrain' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_terrain' ])) . "': stamen_terrain,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_toner' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_toner' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_toner' ])) . "': stamen_toner,";
		}
		if ( (isset($lmm_options[ 'controlbox_stamen_watercolor' ]) == TRUE ) && ($lmm_options[ 'controlbox_stamen_watercolor' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_stamen_watercolor' ])) . "': stamen_watercolor,";
		}
		if ($lmm_options[ 'mapquest_api_key' ] != NULL) {
			if ( (isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_osm' ])) . "': mapquest_osm,";
			}
			if ( (isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_aerial' ])) . "': mapquest_aerial,";
			}
			if ( (isset($lmm_options[ 'controlbox_mapquest_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_hybrid' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_mapquest_hybrid' ])) . "': mapquest_hybrid,";
			}
		}
		if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) ) {
			if ( (isset($lmm_options[ 'controlbox_googleLayer_roadmap' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_roadmap' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_roadmap' ])) . "': googleLayer_roadmap,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_satellite' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_satellite' ])) . "': googleLayer_satellite,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_hybrid' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_hybrid' ])) . "': googleLayer_hybrid,";
			}
			if ( (isset($lmm_options[ 'controlbox_googleLayer_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_terrain' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_googleLayer_terrain' ])) . "': googleLayer_terrain,";
			}
		}
		if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
			if ( (isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingaerial' ])) . "': bingaerial,";
			}
			if ( (isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingaerialwithlabels' ])) . "': bingaerialwithlabels,";
			}
			if ( (isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1) ) {
				$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_bingroad' ])) . "': bingroad,";
			}
		};
		if ( (isset($lmm_options[ 'controlbox_ogdwien_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_basemap' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_ogdwien_basemap' ])) . "': ogdwien_basemap,";
		}
		if ( (isset($lmm_options[ 'controlbox_ogdwien_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_satellite' ] == 1) ) {
			$basemaps_available .= "'" . htmlspecialchars(addslashes($lmm_options[ 'default_basemap_name_ogdwien_satellite' ])) . "': ogdwien_satellite,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox_name' ]))."': mapbox,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox2' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox2' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox2_name' ]))."': mapbox2,";
		}
		if ( (isset($lmm_options[ 'controlbox_mapbox3' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox3' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'mapbox3_name' ]))."': mapbox3,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap_name' ]))."': custom_basemap,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap2' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap2' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap2_name' ]))."': custom_basemap2,";
		}
		if ( (isset($lmm_options[ 'controlbox_custom_basemap3' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap3' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'custom_basemap3_name' ]))."': custom_basemap3,";
		}
		if ( (isset($lmm_options[ 'controlbox_empty_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_empty_basemap' ] == 1) ) {
			$basemaps_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'empty_basemap_name' ]))."': empty_basemap,";
		}
		//info: needed for IE7 compatibility
		echo substr($basemaps_available, 0, -1);
	?>
	},

	//info: controlbox - add available overlays
	{
	<?php
		$overlays_custom_available = '';
		if ( ((isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 )) || ($overlays_custom == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom_name' ]))."': overlays_custom,";
		if ( ((isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 )) || ($overlays_custom2 == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom2_name' ]))."': overlays_custom2,";
		if ( ((isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 )) || ($overlays_custom3 == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom3_name' ]))."': overlays_custom3,";
		if ( ((isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 )) || ($overlays_custom4 == 1) )
			$overlays_custom_available .= "'".htmlspecialchars(addslashes($lmm_options[ 'overlays_custom4_name' ]))."': overlays_custom4,";
		//info: needed for IE7 compatibility
		echo substr($overlays_custom_available, 0, -1);
	?>
	},

	{
	//info: set controlbox visibility 1/2
	collapsed: true
	});
	selectlayer.setView(new L.LatLng(<?php echo $lat . ', ' . $lon; ?>), <?php echo $zoom ?>);

	selectlayer.addControl(layersControl);
	selectlayer.addLayer(<?php echo $basemap; ?>);

	//info: controlbox - add active overlays on marker level
	<?php
		if ( $wms == 1 )
			echo "selectlayer.addLayer(wms);".PHP_EOL;
		if ( $wms2 == 1 )
			echo "selectlayer.addLayer(wms2);".PHP_EOL;
		if ( $wms3 == 1 )
			echo "selectlayer.addLayer(wms3);".PHP_EOL;
		if ( $wms4 == 1 )
			echo "selectlayer.addLayer(wms4);".PHP_EOL;
		if ( $wms5 == 1 )
			echo "selectlayer.addLayer(wms5);".PHP_EOL;
		if ( $wms6 == 1 )
			echo "selectlayer.addLayer(wms6);";
		if ( $wms7 == 1 )
			echo "selectlayer.addLayer(wms7);".PHP_EOL;
		if ( $wms8 == 1 )
			echo "selectlayer.addLayer(wms8);".PHP_EOL;
		if ( $wms9 == 1 )
			echo "selectlayer.addLayer(wms9);".PHP_EOL;
		if ( $wms10 == 1 )
			echo "selectlayer.addLayer(wms10);".PHP_EOL;
	?>
	//info: controlbox - add active overlays on marker level
	<?php
		if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
			echo "selectlayer.addLayer(overlays_custom);".PHP_EOL;
		if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
			echo "selectlayer.addLayer(overlays_custom2);".PHP_EOL;
		if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
			echo "selectlayer.addLayer(overlays_custom3);".PHP_EOL;
		if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
			echo "selectlayer.addLayer(overlays_custom4);".PHP_EOL;
	?>
	<?php //info: add minimap
	if ($lmm_options['minimap_status'] != 'hidden') {
		echo 'var osm_mapnik_minimap = new L.TileLayer("' . $osm_tile_url . '", {maxZoom: ' . $maxzoom . ', maxNativeZoom: ' . $osm_maxNativeZoom . ', minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		echo 'var stamen_terrain_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_terrain_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		echo 'var stamen_toner_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/' .  esc_html($lmm_options[ 'stamen_toner_flavor' ]) . '/{z}/{x}/{y}.png", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 20, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		echo 'var stamen_watercolor_minimap = new L.TileLayer("https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg", {maxZoom: ' .  $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . $error_tile_url . '", detectRetina: ' .  esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
		//info: MapQuest minimap
		if ($lmm_options['mapquest_api_key'] != NULL) {
			echo 'if (typeof MQ !== "undefined") {';
			echo 'mapquest_osm_minimap = new MQ.mapLayer();';
			echo 'mapquest_aerial_minimap = new MQ.satelliteLayer();';
			echo 'mapquest_hybrid_minimap = new MQ.hybridLayer();';
			echo '}';
		}
		//info: google maps minimap
		if ($lmm_options['google_maps_api_status'] == 'enabled') {

			if ( ($lmm_options['google_maps_plugin'] == 'google_mutant') && ($google_mutant_fallback === FALSE) ) {

				if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
					//info: Google language localization (JSON API)
					if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
						$google_language = '';
					} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
						if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
					} else {
						$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
					}
					//info: Google API key
					if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '?key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
					$google_js_url = 'https://maps.googleapis.com/maps/api/js' . $google_maps_api_key . $google_language;
					echo 'var deferred_google_layers = {
									roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
									satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
									hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }},
									terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 }); }}
							};
							var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers.roadmap);
							var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers.satellite);
							var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers.hybrid);
							var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					echo 'var googleLayer_roadmap_minimap = new L.gridLayer.googleMutant({type: "roadmap", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					echo 'var googleLayer_satellite_minimap = new L.gridLayer.googleMutant({type: "satellite", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					echo 'var googleLayer_hybrid_minimap = new L.gridLayer.googleMutant({type: "hybrid", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
					echo 'var googleLayer_terrain_minimap = new L.gridLayer.googleMutant({type: "terrain", detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . ', edgeBufferTiles: 0 });'.PHP_EOL;
				}

			} else if ($lmm_options['google_maps_plugin'] == 'google_legacy') {

				if ($lmm_options['google_maps_api_deferred_loading'] == 'enabled') {
				//info: Google language localization (JSON API)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( $locale != NULL ) { $google_language = "&language=" . substr($locale, 0, 2); } else { $google_language =  '&language=en'; }
				} else {
					$google_language = "&language=" . esc_html($lmm_options['google_maps_language_localization']);
				}
				//info: Google API key
				if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = '&key=' . esc_js(trim($lmm_options['google_maps_api_key'])); } else { $google_maps_api_key = ''; }
				$google_js_url = 'https://maps.googleapis.com/maps/api/js?callback=L.Google.asyncInitialize' . $google_maps_api_key . $google_language;
				echo 'var deferred_google_layers = {
								roadmap: { name: "roadmap", js: ["' . $google_js_url . '"], init: function() {return new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
								satellite: { name: "satellite", js: ["' . $google_js_url . '"], init: function() {return new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
								hybrid: { name: "hybrid", js: ["' . $google_js_url . '"], init: function() {return new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }},
								terrain: { name: "terrain", js: ["' . $google_js_url . '"], init: function() {return new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '}); }}
						};
						var googleLayer_roadmap_minimap = new L.DeferredLayer(deferred_google_layers.roadmap);
						var googleLayer_satellite_minimap = new L.DeferredLayer(deferred_google_layers.satellite);
						var googleLayer_hybrid_minimap = new L.DeferredLayer(deferred_google_layers.hybrid);
						var googleLayer_terrain_minimap = new L.DeferredLayer(deferred_google_layers.terrain);'.PHP_EOL;
				} else { //info: undeferred loading
					echo 'var googleLayer_roadmap_minimap = new L.Google("ROADMAP", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					echo 'var googleLayer_satellite_minimap = new L.Google("SATELLITE", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					echo 'var googleLayer_hybrid_minimap = new L.Google("HYBRID", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
					echo 'var googleLayer_terrain_minimap = new L.Google("TERRAIN", {detectRetina: ' . esc_js($lmm_options['map_retina_detection']) . '});'.PHP_EOL;
				}
			}
		}
		//info: bing minimaps
		if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
			echo 'var bingaerial_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			echo 'var bingaerialwithlabels_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			echo 'var bingroad_minimap = new L.BingLayer("' . htmlspecialchars(trim($lmm_options[ 'bingmaps_api_key' ])) . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
		};
		if ($lmm_options['minimap_zoomLevelFixed'] != NULL) { $zoomlevelfixed =  'zoomLevelFixed: ' . intval($lmm_options['minimap_zoomLevelFixed']) . ','; } else { $zoomlevelfixed = ''; }
		if ($lmm_options['minimap_basemap'] == 'automatic') {
			if ($basemap == 'osm_mapnik') {
				$minimap_basemap = 'osm_mapnik_minimap';
			} else if ($basemap == 'stamen_terrain') {
				$minimap_basemap = 'stamen_terrain_minimap';
			} else if ($basemap == 'stamen_toner') {
				$minimap_basemap = 'stamen_toner_minimap';
			} else if ($basemap == 'stamen_watercolor') {
				$minimap_basemap = 'stamen_watercolor_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_osm')){
				$minimap_basemap = 'mapquest_osm_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_aerial')){
				$minimap_basemap = 'mapquest_aerial_minimap';
			} else if ( (isset($lmm_options['mapquest_api_key']) && ($lmm_options['mapquest_api_key'] != NULL )) && ($basemap == 'mapquest_hybrid')){
				$minimap_basemap = 'mapquest_hybrid_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_roadmap') ) {
				$minimap_basemap = 'googleLayer_roadmap_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_satellite') ) {
				$minimap_basemap = 'googleLayer_satellite_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_hybrid') ) {
				$minimap_basemap = 'googleLayer_hybrid_minimap';
			} else if ( ($lmm_options['google_maps_api_status'] == 'enabled') && ($google_mutant_fallback === FALSE) && ($basemap == 'googleLayer_terrain') ) {
				$minimap_basemap = 'googleLayer_terrain_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerial')){
				$minimap_basemap = 'bingaerial_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerialwithlabels')){
				$minimap_basemap = 'bingaerialwithlabels_minimap';
			} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingroad')){
				$minimap_basemap = 'bingroad_minimap';
			} else {
				$minimap_basemap = 'osm_mapnik_minimap';
			}
		} else {
			$minimap_basemap = $lmm_options['minimap_basemap'];
			//info: fallback for existing maps if Google API is disabled or MapQuest API key is not set
			if (($lmm_options['google_maps_api_status'] == 'disabled') && (($minimap_basemap == 'googleLayer_roadmap') || ($minimap_basemap == 'googleLayer_satellite') || ($minimap_basemap == 'googleLayer_hybrid') || ($minimap_basemap == 'googleLayer_terrain')) ) {
				$minimap_basemap = 'osm_mapnik_minimap';
			} else if (($lmm_options['mapquest_api_key'] == NULL) && (($minimap_basemap == 'mapquest_osm') || ($minimap_basemap == 'mapquest_aerial') || ($minimap_basemap == 'mapquest_hybrid')) ) {
				$minimap_basemap = 'osm_mapnik_minimap';
			}
		}
		$minimap_minimized = ($lmm_options['minimap_status'] == 'collapsed') ? 'true' : 'false';
		echo "var miniMap = new L.Control.MiniMap(" . $minimap_basemap . ", {position: '" . esc_js($lmm_options['minimap_position']) . "', width: " . intval($lmm_options['minimap_width']) . ", height: " . intval($lmm_options['minimap_height']) . ", collapsedWidth: " . intval($lmm_options['minimap_collapsedWidth']) . ", collapsedHeight: " . intval($lmm_options['minimap_collapsedHeight']) . ", zoomLevelOffset: " . intval($lmm_options['minimap_zoomLevelOffset']) . ", " . $zoomlevelfixed . " zoomAnimation: " . esc_js($lmm_options['minimap_zoomAnimation']) . ", toggleDisplay: " . esc_js($lmm_options['minimap_toggleDisplay']) . ", autoToggleDisplay: " . esc_js($lmm_options['minimap_autoToggleDisplay']) . ", minimized: " . $minimap_minimized . "}).addTo(selectlayer);".PHP_EOL;
	} ?>

	//info: gpx tracks
	<?php if ($gpx_url != NULL) {
		$gpx_track_color = '#' . str_replace('#', '', esc_js($lmm_options['gpx_track_color']));
		$gpx_startIconUrl = ($lmm_options['gpx_startIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png' : esc_url($lmm_options['gpx_startIconUrl']);
		$gpx_endIconUrl = ($lmm_options['gpx_endIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png' : esc_url($lmm_options['gpx_endIconUrl']);
		$gpx_shadowUrl = ($lmm_options['gpx_shadowUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png' : esc_url($lmm_options['gpx_shadowUrl']);
		if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
				$gpx_metadata_name_js = 'if (gpx.get_name() != undefined) { _c("gpx-name").innerHTML = gpx.get_name(); } else { _c("gpx-name").innerHTML = "n/a"; }';
		} else { $gpx_metadata_name_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
			$gpx_metadata_start_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-start").innerHTML = gpx.get_start_time().toDateString() + ", " + gpx.get_start_time().toLocaleTimeString(); } else { _c("gpx-start").innerHTML = "n/a"; }';
		} else { $gpx_metadata_start_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
			$gpx_metadata_end_js = 'if (gpx.get_end_time() != undefined) { _c("gpx-end").innerHTML = gpx.get_end_time().toDateString() + ", " + gpx.get_end_time().toLocaleTimeString(); } else { _c("gpx-end").innerHTML = "n/a"; }';
		} else { $gpx_metadata_end_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = (gpx.get_distance()/1000).toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			} else {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = gpx.get_distance_imp().toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_distance_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
			$gpx_metadata_duration_moving_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-moving").innerHTML = gpx.get_duration_string(gpx.get_moving_time()); } else { _c("gpx-duration-moving").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_moving_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
			$gpx_metadata_duration_total_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-total").innerHTML = gpx.get_duration_string(gpx.get_total_time()); } else { _c("gpx-duration-total").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_total_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			} else {
			$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace_imp(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_avpace_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
			$gpx_metadata_avhr_js = 'if (isNaN(gpx.get_average_hr())) { _c("gpx-avghr").innerHTML = "n/a"; } else { _c("gpx-avghr").innerHTML = gpx.get_average_hr() + "bpm"; }';
		} else { $gpx_metadata_avhr_js = ''; }
		if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
			$gpx_metadata_elevation_title_js = '';
		} else { $gpx_metadata_elevation_title_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.get_elevation_gain().toFixed(0);';
			} else {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_gain_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_loss_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.get_elevation_gain().toFixed(0) - gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.to_ft(gpx.get_elevation_gain() - gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_net_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data();';
			} else {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data_imp();';
			}
		} else { $gpx_metadata_elev_full_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data();';
			} else {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data_imp();';
			}
		} else { $gpx_metadata_hr_full_js = ''; }

		//info: do not load GPX if error on wp_remote_get occured
		if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
			$gpx_content = esc_js(str_replace("\xEF\xBB\xBF",'',$gpx_content_array['body'])); //info: replace UTF8-BOM for Chrome
			//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
			if ( (strrpos($gpx_content, '&lt;?xml') !== FALSE) && (strrpos($gpx_content, '&lt;?xml') != 0) ){
				$position = strrpos($gpx_content, '&lt;?xml');
				$gpx_content = substr($gpx_content, $position);
			}
		} else {
			$gpx_content = '';
		}
		echo 'function display_gpx_selectlayer() {
					var gpx_panel = document.getElementById("gpx-panel-selectlayer");
					var gpx_url = "'.$gpx_url.'";

					function _c(c) { return gpx_panel.querySelectorAll("."+c)[0]; }

				   var gpx_track = new L.GPX(gpx_url, {
					gpx_content: "'.$gpx_content.'",
					async: true,
					max_point_interval: ' . intval($lmm_options['gpx_max_point_interval']) . ',
					marker_options: {
						startIconUrl: "' . $gpx_startIconUrl . '",
						endIconUrl: "' . $gpx_endIconUrl . '",
						shadowUrl: "' . $gpx_shadowUrl . '",
						iconSize: [' . intval($lmm_options['gpx_iconSize_x']) . ', ' . intval($lmm_options['gpx_iconSize_y']) . '],
						shadowSize: [' . intval($lmm_options['gpx_shadowSize_x']) . ', ' . intval($lmm_options['gpx_shadowSize_y']) . '],
						iconAnchor: [' . intval($lmm_options['gpx_iconAnchor_x']) . ', ' . intval($lmm_options['gpx_iconAnchor_y']) . '],
					shadowAnchor: [' . intval($lmm_options['gpx_shadowAnchor_x']) . ', ' . intval($lmm_options['gpx_shadowAnchor_y']) . '],
					className: "lmm_gpx_icons"
					},
					polyline_options: {
						color: "' . $gpx_track_color . '",
						weight: ' . intval($lmm_options['gpx_track_weight']) . ',
						opacity: "' . str_replace(',', '.', floatval($lmm_options['gpx_track_opacity'])) . '",
						smoothFactor: "' . str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])) . '",
						interactive: ' . esc_js($lmm_options['gpx_track_clickable']) . ',
						noClip: ' . esc_js($lmm_options['gpx_track_noClip']) . '
					}
					}).addTo(selectlayer);
					gpx_track.on("gpx_loaded", function(e) {
							var gpx = e.target;
							' . $gpx_metadata_name_js . '
							' . $gpx_metadata_start_js . '
							' . $gpx_metadata_end_js . '
							' . $gpx_metadata_distance_js . '
							' . $gpx_metadata_duration_moving_js . '
							' . $gpx_metadata_duration_total_js . '
							' . $gpx_metadata_avpace_js . '
							' . $gpx_metadata_avhr_js . '
							' . $gpx_metadata_elev_gain_js . '
							' . $gpx_metadata_elev_loss_js . '
							' . $gpx_metadata_elev_net_js . '
							' . $gpx_metadata_elev_full_js . '
							' . $gpx_metadata_hr_full_js . '
					});
			}'.PHP_EOL;
		//info: to prevent console XML parsing errors
		if (!is_wp_error($gpx_content_array) && $gpx_content_array['response']['code'] != '404') {
			echo 'display_gpx_selectlayer();'.PHP_EOL;
		}
	}
	?>

	//info: add scale control
	<?php if ( $lmm_options['map_scale_control'] == 'enabled' ) { ?>
	L.control.scale({position:'<?php echo esc_js($lmm_options['map_scale_control_position']); ?>', maxWidth: <?php echo intval($lmm_options['map_scale_control_maxwidth']) ?>, metric: <?php echo esc_js($lmm_options['map_scale_control_metric']); ?>, imperial: <?php echo esc_js($lmm_options['map_scale_control_imperial']); ?>, updateWhenIdle: <?php echo esc_js($lmm_options['map_scale_control_updatewhenidle']); ?>}).addTo(selectlayer);
	<?php }; ?>

	//info: add geolocate control
	<?php
	if ($lmm_options['geolocate_status'] == 'true') {
		if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) { $onlocationerror = ', onLocationError: function () {}'; } else { $onlocationerror = ''; }
		//info: prepare geolocate setView
		if ($lmm_options[ 'geolocate_setView' ] == 'false') {
			$geolocate_setview = "false";
		} else {
			$geolocate_setview = "'" . esc_js($lmm_options[ 'geolocate_setView' ]) . "'";
		}
		echo "var locatecontrol_selectlayer = L.control.locate({
				position: '" . esc_js($lmm_options[ 'geolocate_position' ]) . "',
				drawCircle: " . esc_js($lmm_options[ 'geolocate_drawCircle' ]) . ",
				drawMarker: " . esc_js($lmm_options[ 'geolocate_drawMarker' ]) . ",
				setView: " . $geolocate_setview . ",
				keepCurrentZoomLevel: " . esc_js($lmm_options[ 'geolocate_keepCurrentZoomLevel' ]) . ",
				clickBehavior: {
					inView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_inView' ]) . "',
					outOfView: '" . esc_js($lmm_options[ 'geolocate_clickBehavior_outOfView' ]) . "'
				},
				circleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_circleStyle' ])) . "},
				markerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_markerStyle' ])) . "},
				followCircleStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followCircleStyle' ])) . "},
				followMarkerStyle: {" . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_followMarkerStyle' ])) . "},
				icon: '" . esc_js($lmm_options[ 'geolocate_icon' ]) . "',
				circlePadding: " . esc_js($lmm_options[ 'geolocate_circlePadding' ]) . ",
				metric: " . esc_js($lmm_options[ 'geolocate_units' ]) . ",
				showPopup: " . esc_js($lmm_options[ 'geolocate_showPopup' ]) . ",
				strings: {
					title: '" . __('Show me where I am','lmm') . "',
					metersUnit: '" . __('meters','lmm') . "',
					feetUnit: '" . __('feet','lmm') . "',
					popup: '" . sprintf(__('You are within %1$s %2$s from this point','lmm'), '{distance}', '{unit}') . "',
					outsideMapBoundsMsg: '" . __('You seem located outside the boundaries of the map','lmm') . "'
				},
				locateOptions: { " . str_replace("'", "\"", htmlspecialchars($lmm_options[ 'geolocate_locateOptions' ])) . " }" . $onlocationerror . "
			}).addTo(selectlayer);".PHP_EOL;
		if ( $lmm_options['geolocate_autostart'] == 'true' ) {
			echo "locatecontrol_selectlayer.start();";
		}
	}
	?>

	marker = new L.Marker(new L.LatLng(<?php echo $lat . ", " . $lon; ?>), {
	<?php
	if ($isedit) {
		echo 'markerid: ' . $id . ',';
	}
	$markername_title = strip_tags(htmlspecialchars_decode($markername));
	if ( ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') && ($lmm_options[ 'marker_tooltip_status' ] == 'disabled') ) {
		echo "title: '$markername_title', ";
	} ?>alt: '<?php echo $markername_title; ?>', opacity: <?php echo floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) ?>, draggable: true});
	<?php
	if ($icon == NULL) {
		echo "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
		} else {
		echo "marker.options.icon = new L.Icon({iconUrl: '" . $defaults_marker_icon_url . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
	}

	//info: marker tooltips
	if ($lmm_options[ 'marker_tooltip_status' ] == 'enabled') {
		if ($markername_title != NULL) {
			echo "marker.bindTooltip('" . $markername_title . "', {offset: L.point(" . intval($lmm_options[ 'marker_tooltip_offset_x' ]) . "," . intval($lmm_options[ 'marker_tooltip_offset_y' ]) . "), direction: '" . esc_js($lmm_options[ 'marker_tooltip_direction' ]) . "', permanent: " . esc_js($lmm_options[ 'marker_tooltip_permanent' ]) . ", sticky: " . esc_js($lmm_options[ 'marker_tooltip_sticky' ]) . ", interactive: " . esc_js($lmm_options[ 'marker_tooltip_interactive' ]) . ", opacity: " . str_replace(',', '.', floatval($lmm_options[ 'marker_tooltip_opacity' ])) . "});".PHP_EOL;
		}
	}

	//info: open popup on mouse hover
	if($lmm_options['defaults_marker_popups_rise_on_hover'] == 'true'){ ?>
				marker.on("mouseover", function (e) {
		   			this.openPopup();
		 	    });
	<?php  } ?>
	selectlayer.addLayer(marker);

	<?php
	//info: set controlbox visibility 2/2
	if ($controlbox == '0') {
		echo "$('.leaflet-control-layers').hide();";
	} else if ($controlbox == '2') {
		echo "layersControl._expand();";
	}

	if ($lmm_options['directions_popuptext_panel'] == 'yes') {
	  $directions_settings_link = current_user_can('activate_plugins') ? '<span id="toggle-popup-directions-settings" style="' . $current_editor_css_inline . '"> (<a tabindex="103" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-directions" title="' . esc_attr__('change directions settings','lmm') . '">' . __('Settings','lmm') . '</a>)</span>' : '';
	 if ($address == NULL) {
		$google_from = $lat . ',' . $lon;
		$address = esc_attr__('if set, address will be displayed here','lmm');
	} else {
		$google_from = urlencode($address);
	}
	 $address = (($address == NULL) ? esc_attr__('if set, address will be displayed here','lmm') : $address);
	 $popuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
	 $popuptext = $popuptext . '<div style="' . $popuptext_css . '"><span id="popup-address">' . $address . '</span> ';

	if ($lmm_options['directions_provider'] == 'googlemaps') {
		if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
		$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
		$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
		$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
		$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
		//info: Google language localization (directions)
		if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
			$google_language = '';
		} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
			if ( $locale != NULL ) { $google_language = '&hl=' . substr($locale, 0, 2); } else { $google_language =  '&hl=en'; }
		} else {
		$google_language = '&hl=' . esc_html($lmm_options['google_maps_language_localization']);
		}
		$popuptext = $popuptext . '(<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . esc_html($lmm_options[ 'directions_googlemaps_map_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_googlemaps_traffic' ]) . '&doflg=' . esc_html($lmm_options[ 'directions_googlemaps_distance_units' ]) . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . intval($lmm_options[ 'directions_googlemaps_overview_map' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
	} else if ($lmm_options['directions_provider'] == 'yours') {
			$popuptext = $popuptext . '(<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . esc_html($lmm_options[ 'directions_yours_type_of_transport' ]) . '&fast=' . intval($lmm_options[ 'directions_yours_route_type' ]) . '&layer=' . esc_html($lmm_options[ 'directions_yours_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>';
	} else if ($lmm_options['directions_provider'] == 'ors') {
		$popuptext = $popuptext . '(<a href="http://www.openrouteservice.org/?pos=' . $lon . ',' . $lat . '&wp=' . $lon . ',' . $lat . '&zoom=' . $zoom . '&routeWeigh=' . esc_html($lmm_options[ 'directions_ors_routeWeigh' ]) . '&routeOpt=' . esc_html($lmm_options[ 'directions_ors_routeOpt' ]) . '&layer=' . esc_html($lmm_options[ 'directions_ors_layer' ]) . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
	} else if ($lmm_options['directions_provider'] == 'bingmaps') {
	if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
		$popuptext = $popuptext . '(<a href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
	}
	$popuptext = $popuptext . $directions_settings_link . '</div>';
	}
	?>

	<?php
	if ($lmm_options['directions_popuptext_panel'] == 'yes') {
		echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML+document.getElementById("selectlayer-popuptext-dlink-hidden").innerHTML,';
	} else {
		echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML,';
	}?>
		{ maxWidth: <?php echo intval($lmm_options['defaults_marker_popups_maxwidth']) ?>, minWidth: <?php echo intval($lmm_options['defaults_marker_popups_minwidth']) ?>, maxHeight: <?php echo intval($lmm_options['defaults_marker_popups_maxheight']) ?>, autoPan: <?php echo esc_js($lmm_options['defaults_marker_popups_autopan']); ?>, closeButton: <?php echo esc_js($lmm_options['defaults_marker_popups_closebutton']); ?>, autoPanPadding: new L.Point(<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']) ?>, <?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']) ?>)})<?php  if ($openpopup == 1) { echo '.openPopup()'; } ?>;
	//info: load wms layer when checkbox gets checked
	$('#toggle-advanced-settings input:checkbox').click(function(el) {
		if(el.target.checked) {
			selectlayer.addLayer(window[el.target.id]);
		} else {
			selectlayer.removeLayer(window[el.target.id]);
		}

	});
	//info: update basemap when chosing from control box
	selectlayer.on('layeradd', function(e) {
	if (e.layer.options != undefined) { //needed for gpx
		if(e.layer.options.mmid) {
			selectlayer.attributionControl._attributions = [];
			$('#basemap').val(e.layer.options.mmid);
		}
	}
	});
	//info: when custom overlay gets checked from control box update hidden field
	selectlayer.on('layeradd', function(e) {
	if (e.layer.options != undefined) { //needed for gpx
		if(e.layer.options.olid) {
			$('#'+e.layer.options.olid).attr('value', '1');
		}
	}
	});
	//info: when custom overlay gets unchecked from control box update hidden field
	selectlayer.on('layerremove', function(e) {
		if(e.layer.options.olid) {
			$('#'+e.layer.options.olid).attr('value', '0');
		}
	});
	selectlayer.on('moveend', function(e) { document.getElementById('zoom').value = selectlayer.getZoom();});
	selectlayer.on('click', function(e) {
		selectlayer.setView(e.latlng,selectlayer.getZoom());
		document.getElementById('lat').value = e.latlng.lat.toFixed(6);
		document.getElementById('lon').value = e.latlng.lng.toFixed(6);
		marker.setLatLng(e.latlng);
	<?php
	if ($popuptext != NULL) {
		if ($lmm_options['directions_popuptext_panel'] == 'yes') {
			echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML+document.getElementById("selectlayer-popuptext-dlink-hidden").innerHTML,';
		} else {
			echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML,';
		}
		echo '{maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . esc_js($lmm_options['defaults_marker_popups_autopan']) . ', closeButton: ' . esc_js($lmm_options['defaults_marker_popups_closebutton']) . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')});';
	}
	?>
		if($('input:checkbox[name=openpopup]').is(':checked')) {
			marker.openPopup();
		}
	});
	//info: set new coordinates on marker drag
	marker.on('dragend', function(e) {
		var newlocation = marker.getLatLng();
		var newlat = newlocation['lat'];
		var newlon = newlocation['lng'];
		document.getElementById('lat').value = newlat.toFixed(6);
		document.getElementById('lon').value = newlon.toFixed(6);
		selectlayer.setView(newlocation,selectlayer.getZoom());
		if($('input:checkbox[name=openpopup]').is(':checked')) {
			marker.openPopup();
		}
	});
	//info: set popup checkbox status on click
	marker.on('popupopen', function(e) {
		$('input:checkbox[name=openpopup]').prop('checked', true );
	});
	marker.on('popupclose', function(e) {
		$('input:checkbox[name=openpopup]').prop('checked', false);
	});

	<?php if ($lmm_options['defaults_marker_popups_center_map'] == 'true') {
		echo "//info: center map on popup instead of marker".PHP_EOL;
		echo "selectlayer.on('popupopen', function(e) {".PHP_EOL;
		echo "	var px = selectlayer.project(e.popup._latlng);".PHP_EOL;
		echo "	px.y -= e.popup._container.clientHeight/2".PHP_EOL;
		echo "	selectlayer.panTo(selectlayer.unproject(px),{animate: true});".PHP_EOL;
		echo "});".PHP_EOL;
	}?>

	//info: define variables
	var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), popupText = $('#popuptext'), lat = $('#lat'), lon = $('#lon'), panel = $('#lmm-panel'), gpxpanel = $('#gpx-panel-selectlayer'), gpxpanelcheckbox = $('#gpx_panel'), lmm = $('#lmm'), gpx_fitbounds_link = $('#gpx_fitbounds_link'), markername = $('#markername'), address = $('#address'), zoom = $('#zoom');
	//info: change zoom level when changing form field
	zoom.on('change', function(e) {
		if(isNaN(zoom.val())) {
				alert('<?php esc_attr_e('Invalid format! Please only use numbers!','lmm') ?>');
		} else {
		selectlayer.setZoom(zoom.val());
		}
	});

	markername.on('change', function(e) {
		var popup_markername = "<?php echo $popup_markername; ?>";
		var popup_panel = '<div style="border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;">'+document.getElementById('address').value+' <a href="<?php echo $directionslink; ?>" target="_blank" title="<?php esc_attr_e('Get directions','lmm'); ?>">(<?php _e('Directions','lmm'); ?>)</a><?php echo $directions_settings_link; ?></div>';
		if ($('#wp-popuptext-wrap').hasClass('tmce-active')) {
			if(tinyMCE.activeEditor.getContent() != "") {
				marker._popup.setContent(popup_markername+tinyMCE.activeEditor.getContent()<?php echo $popup_panel; ?>);
			}
		}
		if( markername.val() ){
			$('#lmm-panel-text').text(markername.val());
		} else {
			$('#lmm-panel-text').text('&nbsp;');
		}
	});
	address.on('change', function() {
		markername.trigger('change');
	});
	mapWidth.change(function() {
		if(!isNaN(mapWidth.val())) {
			lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
			selectlayer.invalidateSize();
		}
	});
	$('input:radio[name=mapwidthunit]').click(function() {
			lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
			selectlayer.invalidateSize();
	});
	mapHeight.change(function() {
		if(!isNaN(mapHeight.val())) {
			mapElement.css("height",mapHeight.val()+"px");
			selectlayer.invalidateSize();
		}
	});
	//info: show/hide panel for markername & API URLs
	$('input:checkbox[name=panel]').click(function() {
		if($('input:checkbox[name=panel]').is(':checked')) {
			panel.css("display",'block');
		} else {
			panel.css("display",'none');
		}
	});
	<?php //info: reset the map
	if($lmm_options['map_home_button'] == 'true-ondemand'){
		echo 'selectlayer.on("moveend",function(e){'.PHP_EOL;
		echo '		jQuery("#leaflet-control-zoomhome-admin").show();'.PHP_EOL;
		echo '});'.PHP_EOL;
	}	?>
	<?php //info: reset control
	if ($isedit) { //info: only show on edits of existing maps, as otherwise reset uses default lat/lon values
		if($lmm_options['map_home_button'] != 'false'){
			$zoomhome_ondemand = ($lmm_options['map_home_button'] == 'true-ondemand')?'true':'false';
			echo  'var reset_control = L.Control.zoomHome({position: "'. $lmm_options['map_home_button_position'] .'", zoomHomeIcon: "'.LEAFLET_PLUGIN_URL.'leaflet-dist/images/icon-home.png", mapId: "admin", ondemand: '.$zoomhome_ondemand.', zoomHomeTitle:"'.esc_attr__('reset map view','lmm').'"  });'.PHP_EOL;
			echo  'reset_control.addTo(selectlayer);'.PHP_EOL;
		}
	}
	?>
	<?php //info: upload to media library
	$gpx_track_color = '#' . str_replace('#', '', esc_js($lmm_options['gpx_track_color']));
	$gpx_startIconUrl = ($lmm_options['gpx_startIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png' : esc_url($lmm_options['gpx_startIconUrl']);
	$gpx_endIconUrl = ($lmm_options['gpx_endIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png' : esc_url($lmm_options['gpx_endIconUrl']);
	$gpx_shadowUrl = ($lmm_options['gpx_shadowUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png' : esc_url($lmm_options['gpx_shadowUrl']);
	if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
			$gpx_metadata_name_js = 'if (gpx.get_name() != undefined) { _c("gpx-name").innerHTML = gpx.get_name(); } else { _c("gpx-name").innerHTML = "n/a"; }';
	} else { $gpx_metadata_name_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
		$gpx_metadata_start_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-start").innerHTML = gpx.get_start_time().toDateString() + ", " + gpx.get_start_time().toLocaleTimeString(); } else { _c("gpx-start").innerHTML = "n/a"; }';
	} else { $gpx_metadata_start_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
		$gpx_metadata_end_js = 'if (gpx.get_end_time() != undefined) { _c("gpx-end").innerHTML = gpx.get_end_time().toDateString() + ", " + gpx.get_end_time().toLocaleTimeString(); } else { _c("gpx-end").innerHTML = "n/a"; }';
	} else { $gpx_metadata_end_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = (gpx.get_distance()/1000).toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
		} else {
			$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = gpx.get_distance_imp().toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
		}
	} else { $gpx_metadata_distance_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
		$gpx_metadata_duration_moving_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-moving").innerHTML = gpx.get_duration_string(gpx.get_moving_time()); } else { _c("gpx-duration-moving").innerHTML = "n/a"; }';
	} else { $gpx_metadata_duration_moving_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
		$gpx_metadata_duration_total_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-total").innerHTML = gpx.get_duration_string(gpx.get_total_time()); } else { _c("gpx-duration-total").innerHTML = "n/a"; }';
	} else { $gpx_metadata_duration_total_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
		} else {
		$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace_imp(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
		}
	} else { $gpx_metadata_avpace_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
		$gpx_metadata_avhr_js = 'if (isNaN(gpx.get_average_hr())) { _c("gpx-avghr").innerHTML = "n/a"; } else { _c("gpx-avghr").innerHTML = gpx.get_average_hr() + "bpm"; }';
	} else { $gpx_metadata_avhr_js = ''; }
	if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
		$gpx_metadata_elevation_title_js = '';
	} else { $gpx_metadata_elevation_title_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.get_elevation_gain().toFixed(0);';
		} else {
			$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);';
		}
	} else { $gpx_metadata_elev_gain_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.get_elevation_loss().toFixed(0);';
		} else {
			$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);';
		}
	} else { $gpx_metadata_elev_loss_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.get_elevation_gain().toFixed(0) - gpx.get_elevation_loss().toFixed(0);';
		} else {
			$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.to_ft(gpx.get_elevation_gain() - gpx.get_elevation_loss()).toFixed(0);';
		}
	} else { $gpx_metadata_elev_net_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data();';
		} else {
			$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data_imp();';
		}
	} else { $gpx_metadata_elev_full_js = ''; }
	if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
		if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
			$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data();';
		} else {
			$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data_imp();';
		}
	} else { $gpx_metadata_hr_full_js = ''; }

	//info: create nonce for gpx proxy
	$gpx_proxy_nonce = wp_create_nonce('gpx-proxy-nonce');

	if ( version_compare( $wp_version, '3.5', '>=' ) ) {
		echo "var custom_uploader;
		$('#upload_gpx_file').click(function(e) {
			e.preventDefault();
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: '" . esc_attr__('Upload GPX track','lmm') . "',
				frame: 'select',
				library: { type: 'application/gpx+xml' },
				button: {
					text: '" . esc_attr__('Insert GPX track','lmm') . "'
				},
				multiple: false
			});
			//info: when a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				$('#gpx_url').val(attachment.url);
				gpxpanelcheckbox.attr('checked','checked');
				gpxpanel.css('display','block');
				gpx_fitbounds_link.css('display','inline');

				$.ajax({
					url: '" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/proxy/?url='+attachment.url+'&nonce=" . $gpx_proxy_nonce) . "',
					dataType: 'text',
					type: 'POST'
				}).done(function(data) {
						//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
						if (data.indexOf('<?xml') != 0) {
							var position = data.indexOf('<?xml');
							if (position === -1) {
								if (data.indexOf('<gpx') != 0) { //info: for non-standard GPX files
									var position = data.indexOf('<gpx');
									var data = data.slice(position);
								}
							} else {
								var data = data.slice(position);
							}
						}
						//info: search data for <gpx tag (IIS7.0 issue)
						try {
							if (window.addEventListener) { //info: indexof only available in IE9+
								if (data.toLowerCase().indexOf('<gpx') >= 0) { if (window.console) { console.log('GPX file seems to be ok'); } } else { jquery.error; };
							}
						} catch (err) {
							alert('" . esc_attr__('GPX file could not be parsed - please check your browser console for more information!','lmm') . "');
							if (window.console) console.log(data);
						}
						var gpx_panel = document.getElementById('gpx-panel-selectlayer');
							function _c(c) { return gpx_panel.querySelectorAll('.'+c)[0]; }
						var gpx_track = new L.GPX(attachment.url, {
							gpx_content: data,
							async: true,
							max_point_interval: " .  intval($lmm_options['gpx_max_point_interval']) . ",
							marker_options: {
								startIconUrl: '" . $gpx_startIconUrl . "',
								endIconUrl: '" . $gpx_endIconUrl . "',
								shadowUrl: '" . $gpx_shadowUrl . "',
								iconSize: [" .  intval($lmm_options['gpx_iconSize_x']) . ", " .  intval($lmm_options['gpx_iconSize_y']) . "],
								shadowSize: [" .  intval($lmm_options['gpx_shadowSize_x']) . ", " .  intval($lmm_options['gpx_shadowSize_y']) . "],
								iconAnchor: [" .  intval($lmm_options['gpx_iconAnchor_x']) . ", " .  intval($lmm_options['gpx_iconAnchor_y']) . "],
								shadowAnchor: [" .  intval($lmm_options['gpx_shadowAnchor_x']) . ", " .  intval($lmm_options['gpx_shadowAnchor_y']) . "],
								className: 'lmm_gpx_icons'
							},
							polyline_options: {
								color: '" . $gpx_track_color . "',
								weight: " . intval($lmm_options['gpx_track_weight']) . ",
								opacity: '" . str_replace(',', '.', floatval($lmm_options['gpx_track_opacity'])) . "',
								smoothFactor: '" . str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])) . "',
								interactive: " . esc_js($lmm_options['gpx_track_clickable']) . ",
								noClip: " . esc_js($lmm_options['gpx_track_noClip']) . "
							}
							}).addTo(selectlayer);
						gpx_track.on('gpx_loaded', function(e) {
								var gpx = e.target;
								selectlayer.fitBounds(e.target.getBounds(), { padding: [25,25] });
								" . $gpx_metadata_name_js . "
								" . $gpx_metadata_start_js . "
								" . $gpx_metadata_end_js . "
								" . $gpx_metadata_distance_js . "
								" . $gpx_metadata_duration_moving_js . "
								" . $gpx_metadata_duration_total_js . "
								" . $gpx_metadata_avpace_js . "
								" . $gpx_metadata_avhr_js . "
								" . $gpx_metadata_elev_gain_js . "
								" . $gpx_metadata_elev_loss_js . "
								" . $gpx_metadata_elev_net_js . "
								" . $gpx_metadata_elev_full_js . "
								" . $gpx_metadata_hr_full_js . "
						});
					});
			});
			custom_uploader.open();
		});";
	} else { //info: WP <3.5
		echo "jQuery(document).ready(function() {
			jQuery('#upload_gpx_file').click(function() {
				formfield = jQuery('#gpx_url').attr('name');
				tb_show('', 'media-upload.php?tab=library&post_mime_type=text%2Fgpx&amp;TB_iframe=true');
				jQuery('#TB_overlay').css('z-index','1000');
				jQuery('#TB_window').css('z-index','10000');
				return false;
			});
			window.send_to_editor = function(html) {
				gpxurl = jQuery(html).attr('href');
				jQuery('#gpx_url').val(gpxurl);
				tb_remove();
			}
			 });";
	} ?>

	//info: show/hide gpx panel
	$('input:checkbox[name=gpx_panel]').click(function() {
		if($('input:checkbox[name=gpx_panel]').is(':checked')) {
			gpxpanel.css("display",'block');
		} else {
			gpxpanel.css("display",'none');
		}
	});
	//info: show fitbounds link on focus
	$('#gpx_url').focus(function() {
		gpx_fitbounds_link.css("display",'inline');
	});
	//info: fit gpx map bounds on click
	$('.gpxfitbounds').click(function(e){
		var current_gpx_url = $('#gpx_url').val();
		$.ajax({
			url: '<?php echo MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug); ?>/proxy/?url='+current_gpx_url+'&nonce=<?php echo $gpx_proxy_nonce; ?>',
			dataType: 'text',
			type: 'POST'
		}).done(function(data) {
			//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
			if (data.indexOf('<\?xml') != 0) {
				var position = data.indexOf('<\?xml');
				if (position === -1) {
					if (data.indexOf('<gpx') != 0) { //info: for non-standard GPX files
						var position = data.indexOf('<gpx');
						var data = data.slice(position);
					}
				} else {
					var data = data.slice(position);
				}
			}
			//info: search data for <gpx tag (IIS7.0 issue)
			try {
				if (window.addEventListener) { //info: indexof only available in IE9+
					if (data.toLowerCase().indexOf("<gpx") >= 0) { if (window.console) { console.log("GPX file seems to be ok"); } } else { jquery.error; }
				}
			} catch (err) {
				alert("<?php echo esc_attr__('GPX file could not be parsed - please check your browser console for more information!','lmm'); ?>");
				if (window.console) console.log(data);
			}
			var gpx_panel = document.getElementById('gpx-panel-selectlayer');
			function _c(c) { return gpx_panel.querySelectorAll('.'+c)[0]; }
			var gpx_track = new L.GPX(gpx_url, {
				gpx_content: data,
				async: true,
				max_point_interval: <?php echo intval($lmm_options['gpx_max_point_interval']); ?>,
				marker_options: {
					startIconUrl: "<?php echo $gpx_startIconUrl; ?>",
					endIconUrl: "<?php echo $gpx_endIconUrl; ?>",
					shadowUrl: "<?php echo $gpx_shadowUrl; ?>",
					iconSize: [<?php echo intval($lmm_options['gpx_iconSize_x']); ?>, <?php echo intval($lmm_options['gpx_iconSize_y']); ?>],
					shadowSize: [<?php echo intval($lmm_options['gpx_shadowSize_x']); ?>, <?php echo intval($lmm_options['gpx_shadowSize_y']); ?>],
					iconAnchor: [<?php echo intval($lmm_options['gpx_iconAnchor_x']); ?>, <?php echo intval($lmm_options['gpx_iconAnchor_y']); ?>],
					shadowAnchor: [<?php echo intval($lmm_options['gpx_shadowAnchor_x']); ?>, <?php echo intval($lmm_options['gpx_shadowAnchor_y']); ?>],
					className: 'lmm_gpx_icons'
				},
				polyline_options: {
					color: "<?php echo $gpx_track_color; ?>",
					weight: <?php echo intval($lmm_options['gpx_track_weight']); ?>,
					opacity: "<?php echo str_replace(',', '.', floatval($lmm_options['gpx_track_opacity'])); ?>",
					smoothFactor: "<?php echo str_replace(',', '.', floatval($lmm_options['gpx_track_smoothFactor'])); ?>",
					interactive: <?php echo esc_js($lmm_options['gpx_track_clickable']); ?>,
					noClip: <?php echo esc_js($lmm_options['gpx_track_noClip']); ?>
				}
				}).addTo(selectlayer);

				gpx_track.on('gpx_loaded', function(e) {
					var gpx = e.target;
					selectlayer.fitBounds(e.target.getBounds(), { padding: [25,25] } );
					<?php echo $gpx_metadata_name_js; ?>
					<?php echo $gpx_metadata_start_js; ?>
					<?php echo $gpx_metadata_end_js; ?>
					<?php echo $gpx_metadata_distance_js; ?>
					<?php echo $gpx_metadata_duration_moving_js; ?>
					<?php echo $gpx_metadata_duration_total_js; ?>
					<?php echo $gpx_metadata_avpace_js; ?>
					<?php echo $gpx_metadata_avhr_js; ?>
					<?php echo $gpx_metadata_elev_gain_js; ?>
					<?php echo $gpx_metadata_elev_loss_js; ?>
					<?php echo $gpx_metadata_elev_net_js; ?>
					<?php echo $gpx_metadata_elev_full_js; ?>
					<?php echo $gpx_metadata_hr_full_js; ?>
				});
		});
	});
	//info: open/close popup
	$('input:checkbox[name=openpopup]').click(function() {
		if($('input:checkbox[name=openpopup]').is(':checked')) {
			marker.openPopup();
		} else {
			marker.closePopup();
		}
	});
	//info: check if lat is a number
	$('input:text[name=lat]').change(function(e) {
		if(isNaN(lat.val())) {
				alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: check if lon is a number
	$('input:text[name=lon]').change(function(e) {
		if(isNaN(lon.val())) {
				alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: dynamic update of control box status
	$('input:radio[name=controlbox]').click(function() {
		if($('input:radio[name=controlbox]:checked').val() == 0) {
			$('.leaflet-control-layers').hide();
		}
		if($('input:radio[name=controlbox]:checked').val() == 1) {
			$('.leaflet-control-layers').show();
			layersControl._collapse();
		}
		if($('input:radio[name=controlbox]:checked').val() == 2) {
			$('.leaflet-control-layers').show();
			layersControl._expand();
		}
	});
	//info: show all API links on click on simplified editor
	$('#apilinkstext').click(function(e) {
		$('#apilinkstext').hide();
		$('#apilinks').show('fast');
	});
	//info: show all icons on click on simplified editor
	$('#moreiconslink').click(function(e) {
		$('#moreiconslink').hide();
		$('#moreicons').show('fast');
		$('#mapiconscollection').show('fast');
	});
	//info: show less icons on click on simplified editor
	$('#showlessicons').click(function(e) {
		$('#moreicons').hide();
		$('#mapiconscollection').hide();
		$('#moreiconslink').show('fast');
	});
	//info: sets map center to new marker position when entering lat/lon manually
	$('input:text[name=lat],input:text[name=lon]').change(function(e) {
		var markerLocation = new L.LatLng(lat.val(),lon.val());
		marker.closePopup();
		marker.setLatLng(markerLocation);
		selectlayer.setView(markerLocation, selectlayer.getZoom());
		if($('input:radio[name=openpopup]:checked').val() == 1) {
			marker.openPopup();
		}
	});
	//info: update marker icon upon click
	$('.div-marker-icon').click(function(e) {
		var newicon = $(this).children('.marker-icon-radio-button').attr('value');
		document.getElementById('icon_hidden').value = newicon; //info: IE11 fix
		$('.div-marker-icon').css('background','none');
		$('.div-marker-icon').css('opacity','0.4');
		if (newicon) {
			marker.setIcon(new L.Icon({iconUrl: '<?php echo $defaults_marker_icon_url . '/' ?>' + newicon,iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],shadowUrl: '<?php echo $marker_shadow_url; ?>',shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],className: 'lmm_marker_icon_default'}));
			var newicon_without_dots = newicon.replace('.', '_'); //info: dots in not allowed in selectors
			$('.div-marker-icon-'+newicon_without_dots).css('opacity','1');
			$('.div-marker-icon-'+newicon_without_dots).css('background','#5e5d5d');
		} else {
			marker.setIcon(new L.Icon({iconUrl: '<?php echo LEAFLET_PLUGIN_URL . '/leaflet-dist/images/marker.png' ?>',iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],shadowUrl: '<?php echo $marker_shadow_url; ?>',shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],className: 'lmm_marker_icon_<?php echo substr($icon, 0, -4); ?>'}));
			$('.div-marker-icon-default').css('opacity','1');
			$('.div-marker-icon-default').css('background','#5e5d5d');
		}
	});
	//info: search within markers
	$(document).ready(function(){
		$(".icon-search").keyup(function(){
			var filter = $(this).val();
			$(".div-marker-icon").each(function(){
				var filename = $(this).children('.marker-icon-radio-button').attr("value");
				var filename_without_dots = filename.replace('.', '_'); //info: dots in not allowed in selectors
				var div_marker_icon = 'div-marker-icon-'+filename_without_dots;
				if (filename.search(new RegExp(filter, "i")) < 0) {
					$(this).fadeOut();
				} else {
					$(this).show();
				}
			});
		});
	});
	//info: select2 selector for layer list
	$(document).ready(function(){
		$("#layer").select2({formatNoMatches: "<?php esc_attr_e('No matches found','lmm'); ?>", placeholder: "<?php esc_attr_e('click to assign marker to layer(s)','lmm'); ?>"});
		$(".select2-results").click( function(e){
			alert("<?php esc_attr_e('Error: markers cannot be assigned to multi-layer-maps directly! Please either untick the multi-layer-map-checkbox on the according (multi-)layer map first or assign the marker to a sub-layer of the multi-layer-map in question.','lmm'); ?>");
		});
		$("#layer").on("change", function(e){
			var values = $(this).select2("val");
			if(values.length > 0 && values.indexOf("0")===-1){
				$('#preview_layers').show();
			}else{
				$('#preview_layers').hide();
			}
		});
		$("#layer").on("select2-selecting", function(e){
				var values = $(this).select2("val");
				if(values.length > 0){
						if(e.val === "0"){
							$(this).select2("val", {});
						}else{
							if(values.indexOf("0")!==-1){
								$(this).select2("val", {});

							}
						}
				}
		});
	});
	//info: reset map position after popup is closed
	marker.on('popupclose', function(e) {
		selectlayer.setView(marker.getLatLng(),selectlayer.getZoom());
	});
	//info: warn on unsaved changes when leaving page
	$(":input, textarea").change(function(){
		unsaved = true;
	});
	selectlayer.on('zoomend click', function(e) {
		unsaved = true;
	});
	marker.on('dragend', function(e) {
		unsaved = true;
	});
	$('#submit_top, #submit_bottom, #delete').click(function() {
		unsaved = false;
	});
	function unloadPage(){
		if(unsaved){
			return "<?php esc_attr_e('You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?','lmm'); ?>";
		}
	}
	window.onbeforeunload = unloadPage;
	//info: change submenu URL to prevent reloading & supporting AJAX when adding new markers
	$(document).ready(function(){
		$('.wp-menu-open.menu-top.toplevel_page_leafletmapsmarker_markers.menu-top-last ul.wp-submenu.wp-submenu-wrap li.current a.current').attr('href', $('#defaults_add_new_marker_link').val());
	});

	//info: select markername, address, mapwith, mapheight & zoom input field on focus
	$("#markername, #address, #mapwidth, #mapheight, #zoom").focus(function() {
		$(this).select();
	});
	//info: fix for autofocus in Chrome
	$(document).ready(function(){
		$('input[autofocus="autofocus"]').focus();
	});
	//info: remove disabled attribute for publish/update button if text editor is used
	$(document).ready(function(){
		if($('#wp-popuptext-wrap').hasClass('html-active')){
			$('#submit_top').removeAttr('disabled');
		    $('#submit_bottom').removeAttr('disabled');
		    $('#duplicate_button_top').removeAttr('disabled');
		    $('#duplicate_button_bottom').removeAttr('disabled');
		    $('#delete_button_top').removeAttr('disabled');
		    $('#delete_button_bottom').removeAttr('disabled');
		}
	});
	//info: workaround for conflicts with other plugins also using TinyMCE-LoadContent event, preventing enabling of buttons
	$(document).ready(function(){
		function lmm_initialize_popuptext_editor(){
			if(typeof tinymce !== "undefined" && typeof tinymce.editors !== "undefined" && typeof tinymce.editors.popuptext !== "undefined"){
				tinymce.editors.popuptext.on("LoadContent", function(ed, e){
						jQuery("#submit_top").removeAttr("disabled");
						jQuery("#submit_bottom").removeAttr("disabled");
						jQuery("#duplicate_button_top").removeAttr("disabled");
						jQuery("#duplicate_button_bottom").removeAttr("disabled");
						jQuery("#delete_button_top").removeAttr("disabled");
						jQuery("#delete_button_bottom").removeAttr("disabled");
					});
			}else if(!$('#wp-popuptext-wrap').hasClass('html-active')){ //info: do not run if TinyMCE text mode is enabled
				setTimeout(function(){
					lmm_initialize_popuptext_editor();
				},250);
			}
		}
		lmm_initialize_popuptext_editor();
	});
})(jQuery)

	<?php //info: show alternative error on gelocation fail for Google Chrome
	if ( (($is_chrome === TRUE) || ($is_safari === TRUE)) && (is_ssl() === FALSE) ) {
		echo 'selectlayer.on("locationerror",function(e){'.PHP_EOL;
		echo '	alert("' . sprintf(esc_attr__('Geolocation failed: your current location can only be retrieved if the map is accessed securely using https - see %1$s for more details!','lmm'), 'https://www.mapsmarker.com/geolocation-https-only') . '");'.PHP_EOL;
		echo '});'.PHP_EOL;
	}

	//info: show loading indicator if popup contains images and update popup after images have loaded to prevent broken popups
	echo PHP_EOL."selectlayer.on('popupopen', function(e) {".PHP_EOL;
	echo '  var popup_markerid = e.popup._source.options.markerid;'.PHP_EOL;
	echo "  var popup_images = jQuery('.leaflet-popup-content-wrapper #popup-content-'+popup_markerid+' img');".PHP_EOL;
	echo '  if (popup_images.length > 0) {'.PHP_EOL;
	echo '    var image_counter = 0;'.PHP_EOL;
	echo "    jQuery('#popup-content-'+popup_markerid).css('display', 'none');".PHP_EOL;
	echo "    jQuery('#popup-loading-'+popup_markerid).css('display', 'block');".PHP_EOL;
	echo '    jQuery(popup_images).each(function() {'.PHP_EOL;
	echo "      jQuery(this).on('load', function() {".PHP_EOL;
	echo '        image_counter++;'.PHP_EOL;
	echo '        if (image_counter == popup_images.length) {'.PHP_EOL;
	echo "          jQuery('#popup-loading-'+popup_markerid).css('display', 'none');".PHP_EOL;
	echo "          jQuery('#popup-content-'+popup_markerid).css('display', 'block');".PHP_EOL;
	echo '          e.popup.update();'.PHP_EOL;
	echo '        }'.PHP_EOL;
	echo '      });'.PHP_EOL;
	echo '    });'.PHP_EOL;
	echo '  }'.PHP_EOL;
	echo '});'.PHP_EOL;

	//info: prepare Mapzen Search options
	$mapzen_sources = array();
	if(isset($lmm_options['mapzen_search_sources_osm'])){
		array_push($mapzen_sources, 'osm');
	}
	if(isset($lmm_options['mapzen_search_sources_oa'])){
		array_push($mapzen_sources, 'oa');
	}
	if(isset($lmm_options['mapzen_search_sources_geonames'])){
		array_push($mapzen_sources, 'geonames');
	}
	if(isset($lmm_options['mapzen_search_sources_wof'])){
		array_push($mapzen_sources, 'wof');
	}
	$mapzen_sources = (!empty($mapzen_sources))?implode(',', $mapzen_sources):'';

	//info: prepare Algolia Places options
	if (isset($lmm_options["geocoding_algolia_language"]) && $lmm_options["geocoding_algolia_language"]!="") {
		$algolia_language = esc_js(trim($lmm_options["geocoding_algolia_language"]));
	} else {
		$algolia_language = substr($locale, 0, 2);
	}

	//info: prepare Photon options
	if ($lmm_options["geocoding_photon_language"]=="automatic") {
		$locale_for_photon = strtolower(substr($locale, 0,2));
		if ($locale_for_photon = 'de') {
			$photon_language = 'de';
		} else if ($locale_for_photon = 'fr') {
			$photon_language = 'fr';
		} else if ($locale_for_photon = 'it') {
			$photon_language = 'it';
		} else {
			$photon_language = 'en';
		}
	} else {
		$photon_language = $lmm_options["geocoding_photon_language"];
	}
	?>
	var geocoding_options = {
		mapzen_search: {
			api_key: '<?php echo (isset($lmm_options["geocoding_mapzen_search_api_key"]))?esc_js(trim($lmm_options["geocoding_mapzen_search_api_key"])):""; ?>',
			focuspointlat: '<?php echo (isset($lmm_options["geocoding_mapzen_search_focus_lat"]) && $lmm_options["geocoding_mapzen_search_focus_lat"]!="")?str_replace(',', '.',floatval($lmm_options["geocoding_mapzen_search_focus_lat"])):"none"; ?>',
			focuspointlon: '<?php echo (isset($lmm_options["geocoding_mapzen_search_focus_lon"]) && $lmm_options["geocoding_mapzen_search_focus_lon"]!="")?str_replace(',', '.',floatval($lmm_options["geocoding_mapzen_search_focus_lon"])):"none"; ?>',
			sources:'<?php echo $mapzen_sources; ?>',
			layers: '<?php echo (isset($lmm_options["geocoding_mapzen_search_layer"]) && $lmm_options["geocoding_mapzen_search_layer"]!="none")?$lmm_options["geocoding_mapzen_search_layer"]:"none"; ?>',
			country: '<?php echo (isset($lmm_options["geocoding_mapzen_search_country"]) && $lmm_options["geocoding_mapzen_search_country"]!="")?esc_js(trim($lmm_options["geocoding_mapzen_search_country"])):""; ?>',
			narrow_search: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_search"]) && $lmm_options["geocoding_mapzen_search_narrow_search"]!="")?esc_js(trim($lmm_options["geocoding_mapzen_search_narrow_search"])):""; ?>',
			rect_lat_min: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lat_min"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lat_min"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lat_min"])):""; ?>',
			rect_lon_min: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lon_min"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lon_min"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lon_min"])):""; ?>',
			rect_lat_max: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lat_max"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lat_max"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lat_max"])):""; ?>',
			rect_lon_max: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_rect_lon_max"]) && $lmm_options["geocoding_mapzen_search_narrow_rect_lon_max"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_rect_lon_max"])):""; ?>',
			circle_lat: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_circle_lat"]) && $lmm_options["geocoding_mapzen_search_narrow_circle_lat"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_circle_lat"])):""; ?>',
			circle_lon: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_circle_lon"]) && $lmm_options["geocoding_mapzen_search_narrow_circle_lon"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_mapzen_search_narrow_circle_lon"])):""; ?>',
			circle_radius: '<?php echo (isset($lmm_options["geocoding_mapzen_search_narrow_circle_radius"]) && $lmm_options["geocoding_mapzen_search_narrow_circle_radius"]!="")?floatval($lmm_options["geocoding_mapzen_search_narrow_circle_radius"]):""; ?>'
		},
		algolia_places: {
			appId: '<?php echo (isset($lmm_options["geocoding_algolia_appId"]))?esc_js(trim($lmm_options["geocoding_algolia_appId"])):""; ?>',
			apiKey: '<?php echo (isset($lmm_options["geocoding_algolia_apiKey"]))?esc_js(trim($lmm_options["geocoding_algolia_apiKey"])):""; ?>',
			language: '<?php echo $algolia_language; ?>',
			countries: '<?php echo (isset($lmm_options["geocoding_algolia_countries"]))?esc_js(trim($lmm_options["geocoding_algolia_countries"])):""; ?>',
			aroundLatLngViaIP: '<?php echo (isset($lmm_options["geocoding_algolia_aroundLatLngViaIP"]))?$lmm_options["geocoding_algolia_aroundLatLngViaIP"]:"true"; ?>',
			aroundLatLng: '<?php echo (isset($lmm_options["geocoding_algolia_aroundLatLng"]))?esc_js(trim($lmm_options["geocoding_algolia_aroundLatLng"])):""; ?>',
			referrer: '<?php echo (isset($lmm_options["geocoding_algolia_referrer"]))?esc_js(trim($lmm_options["geocoding_algolia_referrer"])):get_home_url(); ?>',
		},
		photon: {
			language: '<?php echo $photon_language; ?>',
			locationbiaslat: '<?php echo (isset($lmm_options["geocoding_photon_location_bias_lat"]) && $lmm_options["geocoding_photon_location_bias_lat"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_photon_location_bias_lat"])):"none"; ?>',
			locationbiaslon: '<?php echo (isset($lmm_options["geocoding_photon_location_bias_lon"]) && $lmm_options["geocoding_photon_location_bias_lon"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_photon_location_bias_lon"])):"none"; ?>',
			filter: '<?php echo (isset($lmm_options["geocoding_photon_filter_results"]) && $lmm_options["geocoding_photon_filter_results"]!="")?esc_js(trim($lmm_options["geocoding_photon_filter_results"])):"none"; ?>'
		},
		mapquest_geocoding: {
			api_key: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_api_key"]))?esc_js(trim($lmm_options["geocoding_mapquest_geocoding_api_key"])):""; ?>',
			boundingBox: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_status"]))?esc_js(trim($lmm_options["geocoding_mapquest_geocoding_bounds_status"])):"disabled"; ?>',
			lat1: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lat1"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lat1"])):""; ?>',
			lon1: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lon1"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lon1"])):""; ?>',
			lat2: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lat2"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lat2"])):""; ?>',
			lon2: '<?php echo (isset($lmm_options["geocoding_mapquest_geocoding_bounds_lon2"]))?str_replace(',', '.', floatval($lmm_options["geocoding_mapquest_geocoding_bounds_lon2"])):""; ?>'
		},
		google_geocoding: {
			<?php $google_places_endpoint_nonce = wp_create_nonce('google-places-endpoint-nonce'); ?>
			nonce: '<?php echo $google_places_endpoint_nonce ?>'
		},
		fallback: '<?php echo str_replace("-", "_", $lmm_options["geocoding_provider_fallback"]); ?>'
	};
	<?php
	if ($lmm_options['geocoding_provider'] == 'mapzen-search')  { ?>
		jQuery(document).ready(function(){
			<?php if ($isedit) { ?>
				var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var mapzen_search = new MMP_Geocoding('mapzen_search', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			mapzen_search.init();
		});
	<?php
	} else if ($lmm_options['geocoding_provider'] == 'algolia-places')  {  ?>
		jQuery(document).ready(function(){
			<?php if ($isedit) { ?>
				var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var algolia_places = new MMP_Geocoding('algolia_places', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			algolia_places.init();
		});
	<?php
	} else if ($lmm_options['geocoding_provider'] == 'photon')  {  ?>
		jQuery(document).ready(function(){
			<?php if ($isedit) { ?>
				var photon = new MMP_Geocoding('photon', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var photon = new MMP_Geocoding('photon', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			photon.init();
		});
	<?php
	} else if ( ($lmm_options['geocoding_provider'] == 'mapquest-geocoding' ) && ($lmm_options['geocoding_mapquest_geocoding_api_key'] != NULL) ) { ?>
		jQuery(document).ready(function(){
			<?php if ($isedit) { ?>
				var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php }else{ ?>
				var mapquest_geocoding = new MMP_Geocoding('mapquest_geocoding', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
			<?php } ?>
			mapquest_geocoding.init();
		});
	<?php
	} else if
		(
			($lmm_options['geocoding_provider'] == 'google-geocoding')
			&&
			(
				( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') && ($lmm_options['geocoding_google_geocoding_api_key'] != NULL) )
				||
				( ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') && (($lmm_options['geocoding_google_geocoding_premium_client'] != NULL) || ($lmm_options['geocoding_google_geocoding_premium_signature'] != NULL)) )
			)
		)  {
	?>
	jQuery(document).ready(function(){
		<?php if ($isedit) { ?>
			var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, true, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php }else{ ?>
			var google_geocoding = new MMP_Geocoding('google_geocoding', geocoding_options, false, <?php echo intval($lmm_options["geocoding_typing_delay"]); ?>, <?php echo intval($lmm_options["geocoding_min_chars_search_autostart"]); ?>);
		<?php } ?>
			google_geocoding.init();
	});
	<?php } ?>

	<?php
	if ($lmm_options['google_maps_api_status'] == 'enabled') {
		echo '//info: detect if Google Maps JavaScript API returns an error
		if(window.console){
			console.error = function(message){
				if(message.indexOf("Google") != -1){ //info: only execute on Google console errors
					jQuery("#google-maps-api-status-info ").hide(); //info: hide admin notice visible for fresh pro installs and upgrades from free <3.11
					var message_stripped = message.replace(/(?:https?|ftp):\/\/[\n\S]+/g, "");
					jQuery("#google-api-error-admin-header").html(\'<strong>\'+message_stripped+\'</strong> (<a href="https://developers.google.com/maps/documentation/javascript/error-messages#no-api-keys" target="_blank">' . esc_attr__('error message details on google.com','lmm') . '</a>)<br/>\');
					jQuery("#google-api-error-admin-header").append(\'<hr noshade size="1"/><strong>' . __('Background','lmm') . '</strong>: ' . sprintf(__( 'Since June 22nd 2016 <a href="%1$s" target="_blank">Google requires a Google Maps API key</a> when using any Google Map service on your website.','lmm'), 'https://googlegeodevelopers.blogspot.co.at/2016/06/building-for-scale-updates-to-google.html') . ' ' . sprintf(__('Your personal API key can be obtained from the <a href="%1$s" target="_blank">Google API Console</a>.', 'lmm'), 'https://console.developers.google.com/apis/') . '<br/>' . sprintf(__('For a tutorial including screenshots on how to register a Google Maps API key <a href="%1$s" target="_blank">please click here</a>.', 'lmm'), 'https://www.mapsmarker.com/google-maps-javascript-api') . '<br/>\');
					jQuery("#google-api-error-admin-header").append(\'<hr noshade size="1"/><strong>' . __('Solution','lmm') . '</strong>: ' . sprintf(__('please add or verify your Google Maps JavaScript API key at <a href="%1$s">Settings / Basemaps / Google Maps JavaScript API</a>','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-basemaps-google_js_api') . '\');
					jQuery("#google-api-error-admin-header").css("display", "block");
				}
			}
		}';
	}
	?>

	<?php //info: unbindPopup to avoid empty popup on icon click (bindPopup added on popuptext change again in TinyMCE)
	if ( ($popuptext == NULL) && ($lmm_options['directions_popuptext_panel'] == 'no') ) { ?>
		marker.unbindPopup();
	<?php }?>
	/* //]]> */
	</script>

<!--default & current values for AJAX-->
<input type="hidden" id="defaults_add_new_marker_link" value="<?php echo LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker#'; ?>" />
<input type="hidden" id="defaults_icon" value="<?php echo esc_html($icon); ?>" />
<input type="hidden" id="defaults_layer" value="<?php echo ($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '0' : ($lmm_options[ 'defaults_marker_default_layer' ]); ?>" />
<input type="hidden" id="defaults_lat" value="<?php echo str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lat' ])); ?>" />
<input type="hidden" id="defaults_lon" value="<?php echo str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lon' ])); ?>" />
<input type="hidden" id="defaults_zoom" value="<?php echo intval($lmm_options[ 'defaults_marker_zoom' ]); ?>" />
<input type="hidden" id="defaults_openpopup" value="<?php echo $lmm_options[ 'defaults_marker_openpopup' ]; ?>" />
<input type="hidden" id="defaults_mapwidth" value="<?php echo intval($lmm_options[ 'defaults_marker_mapwidth' ]); ?>" />
<input type="hidden" id="defaults_mapwidthunit" value="<?php echo esc_html($lmm_options[ 'defaults_marker_mapwidthunit' ]); ?>" />
<input type="hidden" id="defaults_mapheight" value="<?php echo intval($lmm_options[ 'defaults_marker_mapheight' ]); ?>" />
<input type="hidden" id="defaults_panel" value="<?php echo $lmm_options[ 'defaults_marker_panel' ]; ?>" />
<input type="hidden" id="defaults_controlbox" value="<?php echo $lmm_options[ 'defaults_marker_controlbox' ]; ?>" />
<input type="hidden" id="defaults_overlays_custom" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_overlays_custom2" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_overlays2_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom2_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_overlays_custom3" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_overlays3_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom3_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_overlays_custom4" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_overlays4_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom4_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms2" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms2_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms3" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms3_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms4" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms4_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms5" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms5_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms6" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms6_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms7" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms7_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms8" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms8_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms9" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms9_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<input type="hidden" id="defaults_wms10" value="<?php echo ( (isset($lmm_options[ 'defaults_marker_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms10_active' ] == 1 ) ) ? '1' : '0'; ?>" />
<!--defaults for marker icons-->
<?php
if ($icon == NULL) {
	echo '<input type="hidden" id="defaults_marker_icon_url" value="' . LEAFLET_PLUGIN_URL . '/leaflet-dist/images/marker.png" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_marker_icon_url" value="' . $defaults_marker_icon_url . '/' . $icon .'" />'.PHP_EOL;
}
?>
<input type="hidden" id="defaults_marker_icon_iconsize_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_iconsize_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_iconanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_iconanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_popupanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_popupanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadow_url" value="<?php echo $marker_shadow_url;?>" />
<input type="hidden" id="defaults_marker_icon_shadowsize_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadowsize_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadowanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadowanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]);?>" />
<?php
if ($icon == NULL) {
	echo '<input type="hidden" id="defaults_icon_className" value="lmm_marker_icon_default" />'.PHP_EOL;
	echo '<input type="hidden" id="defaults_icon_opacity_selector" value=".div-marker-icon-default" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_icon_className" value="lmm_marker_icon_' . substr($icon, 0, -4) .'" />'.PHP_EOL;
	$icon_without_dots = str_replace('.', '_', $icon);
	echo '<input type="hidden" id="defaults_icon_opacity_selector" value=".div-marker-icon-' . $icon_without_dots . '" />'.PHP_EOL;
}?>
<!--defaults for google directions link-->
<input type="hidden" id="defaults_directions_directions_provider" value="<?php echo $lmm_options['directions_provider'];?>" />
<input type="hidden" id="defaults_directions_directions_googlemaps_map_type" value="<?php echo $lmm_options['directions_googlemaps_map_type'];?>" />
<input type="hidden" id="defaults_directions_directions_googlemaps_traffic" value="<?php echo $lmm_options['directions_googlemaps_traffic'];?>" />
<input type="hidden" id="defaults_directions_directions_googlemaps_distance_units" value="<?php echo $lmm_options['directions_googlemaps_distance_units'];?>" />
<input type="hidden" id="defaults_directions_directions_directions_googlemaps_overview_map" value="<?php echo $lmm_options['directions_googlemaps_overview_map'];?>" />
<?php
if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) {
	echo '<input type="hidden" id="defaults_directions_gmaps_base_domain_directions" value="' . $lmm_options['google_maps_base_domain'] . '" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_directions_gmaps_base_domain_directions" value="' . htmlspecialchars($lmm_options['google_maps_base_domain_custom']) . '" />'.PHP_EOL;
}
if ( (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ) {
	echo '<input type="hidden" id="defaults_directions_google_avoidhighways" value="&dirflg=h" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_directions_google_avoidhighways" value="" />'.PHP_EOL;
}
if ( (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ) {
	echo '<input type="hidden" id="defaults_directions_google_avoidtolls" value="&dirflg=t" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_directions_google_avoidtolls" value="" />'.PHP_EOL;
}
if ( (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ) {
	echo '<input type="hidden" id="defaults_directions_google_publictransport" value="&dirflg=r" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_directions_google_publictransport" value="" />'.PHP_EOL;
}
if ( (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ) {
	echo '<input type="hidden" id="defaults_directions_google_walking" value="&dirflg=w" />'.PHP_EOL;
} else {
	echo '<input type="hidden" id="defaults_directions_google_walking" value="" />'.PHP_EOL;
}
if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
	echo '<input type="hidden" id="defaults_directions_google_language" value="" />'.PHP_EOL;
} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
	if ( $locale != NULL ) {
		echo '<input type="hidden" id="defaults_directions_google_language" value="&hl=' . substr($locale, 0, 2) .'" />'.PHP_EOL;
	} else {
		echo '<input type="hidden" id="defaults_directions_google_language" value="&hl=en" />'.PHP_EOL;
	}
} else {
	echo '<input type="hidden" id="defaults_directions_google_language" value="&hl=' . $lmm_options['google_maps_language_localization'] . '" />'.PHP_EOL;
}
?>
<!--defaults for other direction link providers -->
<input type="hidden" id="defaults_directions_directions_yours_type_of_transport" value="<?php echo $lmm_options['directions_yours_type_of_transport'];?>" />
<input type="hidden" id="defaults_directions_directions_yours_route_type" value="<?php echo $lmm_options['directions_yours_route_type'];?>" />
<input type="hidden" id="defaults_directions_directions_yours_layer" value="<?php echo $lmm_options['directions_yours_layer'];?>" />
<input type="hidden" id="defaults_directions_directions_ors_routeWeigh" value="<?php echo $lmm_options['directions_ors_routeWeigh'];?>" />
<input type="hidden" id="defaults_directions_directions_ors_routeOpt" value="<?php echo $lmm_options['directions_ors_routeOpt'];?>" />
<input type="hidden" id="defaults_directions_directions_ors_layer" value="<?php echo $lmm_options['directions_ors_layer'];?>" />
<!--defaults needed for mlm layer preview ajax loading-->
<input type="hidden" id="defaults_marker_icon_iconsize_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_iconsize_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_iconanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_iconanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_popupanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_popupanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadow_url" value="<?php echo $marker_shadow_url;?>" />
<input type="hidden" id="defaults_marker_icon_shadowsize_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadowsize_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadowanchor_x" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]);?>" />
<input type="hidden" id="defaults_marker_icon_shadowanchor_y" value="<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]);?>" />
<input type="hidden" id="defaults_marker_popups_maxWidth" value="<?php echo intval($lmm_options['defaults_marker_popups_maxwidth']);?>" />
<input type="hidden" id="defaults_marker_popups_minWidth" value="<?php echo intval($lmm_options['defaults_marker_popups_minwidth']);?>" />
<input type="hidden" id="defaults_marker_popups_maxHeight" value="<?php echo intval($lmm_options['defaults_marker_popups_maxheight']);?>" />
<input type="hidden" id="defaults_marker_popups_autoPan" value="<?php echo esc_js($lmm_options['defaults_marker_popups_autopan']);?>" />
<input type="hidden" id="defaults_marker_popups_closeButton" value="<?php echo esc_js($lmm_options['defaults_marker_popups_closebutton']);?>" />
<input type="hidden" id="defaults_marker_popups_autopanpadding_x" value="<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']);?>" />
<input type="hidden" id="defaults_marker_popups_autopanpadding_y" value="<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']);?>" />
<input type="hidden" id="defaults_marker_popups_add_markername" value="<?php echo $lmm_options['defaults_marker_popups_add_markername'];?>" />
<input type="hidden" id="defaults_directions_popuptext_panel" value="<?php echo $lmm_options['directions_popuptext_panel'];?>" />
<!-- default texts for AJAX-->
<input type="hidden" id="defaults_texts_add_new_marker" value="<?php echo __('Add new marker','lmm'); ?>" />
<input type="hidden" id="defaults_texts_publish" value="<?php echo __('publish','lmm'); ?>" />
<input type="hidden" id="defaults_texts_update" value="<?php echo __('update','lmm'); ?>" />
<input type="hidden" id="defaults_texts_panel_text" value="<?php echo __('if set, markername will be displayed here','lmm'); ?>" />
<input type="hidden" id="defaults_texts_directions_link_new_marker" value="<?php echo esc_attr__('if set, address will be displayed here','lmm') . esc_attr__($directions_settings_link); ?>" />
<input type="hidden" id="defaults_texts_hide_preview" value="<?php echo esc_attr__('hide all markers from assigned layer(s)','lmm'); ?>" />
<input type="hidden" id="defaults_texts_hide_preview_icon" value="<?php echo LEAFLET_PLUGIN_URL . 'inc/img/icon-eye-show.png'?>" />
<input type="hidden" id="defaults_texts_show_preview" value="<?php echo esc_attr__('preview all markers from assigned layer(s)','lmm'); ?>" />
<input type="hidden" id="defaults_texts_show_preview_icon" value="<?php echo LEAFLET_PLUGIN_URL . 'inc/img/icon-eye-hide.png'?>" />
<input type="hidden" id="defaults_directions_link" value="<?php echo $directionslink; ?>" />
<span style="display:none;" id="directions_settings_link"><?php echo addslashes($directions_settings_link); ?></span>
<!--default texts for geocoding.js-->
<span style="display:none;" id="defaults_texts_geocoding_mapzen_api_key_needed"><?php echo sprintf(__('Fallback geocoding provider was activated as Mapzen Search requires an API key since 04/2017.<br/>We recommend <a href="%1$s" target="_blank">registering a free Mapzen Search API key</a> which allows up to %2$s requests/%3$s and a maximum of %4$s requests/%5$s.','lmm'), 'https://www.mapsmarker.com/mapzen-search#tutorial', '30.000', __('day','lmm'), '6', __('second','lmm')); ?></span>
<span style="display:none;" id="defaults_texts_mapquest_key_issue"><?php echo sprintf(__('MapQuest Geocoding error - please contact support at <a href="%1$s">support@mapquest.com</a>'), 'mailto:support@mapquest.com?subject=Issue with API key ' . esc_js(trim($lmm_options['geocoding_mapquest_geocoding_api_key']))); ?></span>
<input type="hidden" id="defaults_texts_geocoding_fallback_info" value="<?php echo __('Automatically switched to fallback provider','lmm'); ?>" />
<input type="hidden" id="defaults_texts_geocoding_results_header" value="<?php echo __('To select a location, please click on a result or press','lmm'); ?>" />
<span style="display:none;" id="defaults_texts_geocoding_footer_tips"><div id="geocoding-footer-tips" style="float:left;margin:4px 0 0 0;"><a href="https://www.mapsmarker.com/geocoding-optimization" target="_blank" style="text-decoration:underline;" title="<?php echo esc_attr__('show tutorial at mapsmarker.com','lmm'); ?>"><?php echo __('Tip: adjust geocoding settings for more targeted search results','lmm'); ?></a></div></span>
</div><!--end div-marker-editor-hide-on-ajax-delete-->
<?php
echo '<input type="hidden" id="defaults_wpml_status" value="' . MMP_Globals::check_multilingual() . '" />';
} //info: check if marker exists - part 2
} //info: check if user is allowed to view marker - part 2
include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');
