<?php
/*
    List all markers - Maps Marker Pro
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-list-markers.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
global $wpdb, $current_user;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$lmm_base_url = MMP_Rewrite::get_base_url();
$lmm_slug = MMP_Rewrite::get_slug();
//info: set custom marker icon dir/url
if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
	$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
} else {
	$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
}
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';

$radius = 1;
$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
//info: security check if input variable is valid
$columnsort_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox','m.address');
$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_marker_listing_sort_order_by' ]);
$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_marker_listing_sort_order_by' ];

//info: security check if input variable is valid
$columnsortorder_values = array('asc','desc','ASC','DESC');
$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ]);
$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];

$offset = ($pagenum - 1) * intval($lmm_options[ 'markers_per_page' ]);
$searchtext = isset($_POST['searchtext']) ? '%' .esc_sql($_POST['searchtext']) . '%' : (isset($_GET['searchtext']) ? '%' . esc_sql($_GET['searchtext']) : '') . '%';
$markers_per_page_validated = intval($lmm_options[ 'markers_per_page' ]);
$capabilities_view = (isset($lmm_options['capabilities_view_others']))?$lmm_options['capabilities_view_others']:'edit_posts';
$createdby_query = (!current_user_can($capabilities_view))?" m.createdby = '". $current_user->user_login ."' ":'1=1';
$mcount = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name_markers` AS m WHERE (m.id LIKE '%s' OR m.markername LIKE '%s' OR m.popuptext LIKE '%s' OR m.address LIKE '%s') AND $createdby_query", $searchtext, $searchtext, $searchtext, $searchtext)));
if($columnsort == 'l.name'){
	$marklist = $wpdb->get_results( "SELECT m.id,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address,l.name AS layername,l.id as layerid FROM `$table_name_markers` AS m LEFT OUTER JOIN `$table_name_layers` AS l ON m.layer LIKE concat('%\"',l.id,'\"%')  WHERE $createdby_query  GROUP BY m.id order by $columnsort $columnsortorder LIMIT $markers_per_page_validated OFFSET $offset", ARRAY_A);
}else{
	$marklist = $wpdb->get_results( "SELECT m.id,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address FROM `$table_name_markers` AS m WHERE $createdby_query ORDER BY $columnsort $columnsortorder LIMIT $markers_per_page_validated OFFSET $offset", ARRAY_A);
}

//info:  get pagination
$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
$getorderby = isset($_GET['orderby']) ? '&orderby=' . htmlspecialchars($_GET['orderby']) : '';
if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
$pager = '';
if ($mcount > intval($lmm_options[ 'markers_per_page' ])) {
  $maxpage = intval(ceil($mcount / intval($lmm_options[ 'markers_per_page' ])));
  if ($maxpage > 1) {
    $pager .= '<div class="tablenav-pages">' . __('Markers per page','lmm') . ': ';
	if (current_user_can('activate_plugins')) {
		$pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-misc-list_all_markers" title="' . esc_attr__('Change number in settings','lmm') . '" style="background:none;padding:0;border:none;text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
	} else {
		$pager .= intval($lmm_options[ "markers_per_page" ]) . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
	}
	$pager .= '<form style="display:inline;" method="POST" action="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">' . __('page','lmm') . ' ';
	$pager .= '<input type="hidden" name="orderby" value="' . $columnsort . '" />';
	$pager .= '<input type="hidden" name="order" value="' . $columnsortorder . '" />';
    if ($pagenum > (2 + $radius * 2)) {
      foreach (range(1, 1 + $radius) as $num)
        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
      $pager .= '...';
      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    }
    else
      if ($pagenum > 1)
        foreach (range(1, $pagenum - 1) as $num)
          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    $pager .= '<a href="#" class="first-page current-page">' . $pagenum . '</a>';
    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
      $pager .= '...';
      foreach (range($maxpage - $radius, $maxpage) as $num)
        $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    }
    else
      if ($pagenum < $maxpage)
        foreach (range($pagenum + 1, $maxpage) as $num)
          $pager .= '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.$getorderby.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    $pager .= '</div></form>';
  }
}

include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php');
$duplicateselected = ( isset($_POST['bulkactions-markers']) && ($_POST['bulkactions-markers'] == 'duplicateselected') ) || ( isset($_GET['action']) && ($_GET['action'] == 'duplicate') ) ? '1' : '0';
$deleteselected = ( isset($_POST['bulkactions-markers']) && ($_POST['bulkactions-markers'] == 'deleteselected') ) || ( isset($_GET['action']) && ($_GET['action'] == 'delete') )  ? '1' : '0';
$assignselected = ( isset($_POST['bulkactions-markers']) && ($_POST['bulkactions-markers'] == 'assignselected') ) ? '1' : '0';
$massactionnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
if (isset($_POST['checkedmarkers'])) {
	$checkedmarkers = $_POST['checkedmarkers'];
} else if ( isset($_GET['action']) && isset($_GET['id']) ) {
	$checkedmarkers = array(intval($_GET['id']));
} else {
	$checkedmarkers = NULL;
}
if ( ($deleteselected == '1') && ($checkedmarkers != NULL) && current_user_can( $lmm_options[ 'capabilities_delete_others' ]) ) {
	if (! wp_verify_nonce($massactionnonce, 'massaction-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
	$checked_markers_prepared = implode(",", $checkedmarkers);
	$checked_markers = preg_replace('/[a-z|A-Z| |\=]/', '', $checked_markers_prepared);
	$wpdb->query( "DELETE FROM `$table_name_markers` WHERE `id` IN (" . htmlspecialchars($checked_markers) . ")");
	$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
	echo '<p><div class="notice notice-success is-dismissible" style="padding:10px;">' . __('The selected markers have been deleted','lmm') . ' (ID ' . htmlspecialchars($checked_markers) . ')</div>';
} else if ( ($assignselected == '1') && ($checkedmarkers != NULL) && current_user_can( $lmm_options[ 'capabilities_edit_others' ]) ) {
	if (! wp_verify_nonce($massactionnonce, 'massaction-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
	$checked_markers_prepared = implode(",", $checkedmarkers);
	$checked_markers = preg_replace('/[a-z|A-Z| |\=]/', '', $checked_markers_prepared);


	$prepared_layers = (is_array($_POST['layer']))?json_encode($_POST['layer']):json_encode(array($_POST['layer']));

	$wpdb->query( "UPDATE $table_name_markers SET layer = '" . $prepared_layers . "' where id IN (" . $checked_markers . ")");
	echo '<p><div class="notice notice-success is-dismissible" style="padding:10px;">' . __('The selected markers have been assigned to the selected layer','lmm') . ' (' . __('Marker','lmm') . ' ID ' . htmlspecialchars($checked_markers) . ', ' . __('Layer','lmm') . ' ID ' . htmlspecialchars(is_array($_POST['layer'])?implode(',', $_POST['layer']):$_POST['layer']) . ')</div>';
} else if ( ($duplicateselected == '1') && ($checkedmarkers != NULL) && current_user_can( $lmm_options[ 'capabilities_edit_others' ]) ) {
	if (! wp_verify_nonce($massactionnonce, 'massaction-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
	global $current_user;
	$new_marker_ids = array();
	foreach ($checkedmarkers as $row){
		$result = $wpdb->get_row( $wpdb->prepare('SELECT * FROM `'.$table_name_markers.'` WHERE `id`= %d',$row), ARRAY_A);
		if (isset($_POST['layer-duplicate'])) {
			if ($_POST['layer-duplicate'] == 'unchanged') {
				$layer_duplicate = $result['layer'];
			} else {
				$layer_duplicate = json_encode(array($_POST['layer-duplicate']));
			}
		} else { //info: single duplicate action link
			$layer_duplicate = $result['layer'];
		}
		if ($result['kml_timestamp'] == NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $result['markername'], $result['basemap'], $layer_duplicate, $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['address'], $result['gpx_url'], $result['gpx_panel'] );
		} else if ($result['kml_timestamp'] != NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO `$table_name_markers` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $result['markername'], $result['basemap'], $layer_duplicate, $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['kml_timestamp'], $result['address'], $result['gpx_url'], $result['gpx_panel'] );
		}
		$wpdb->query( $sql_duplicate );
		$new_marker_id = $wpdb->insert_id;
		if($ml_checked = MMP_Globals::check_multilingual()){
			//info: register marker texts for translation to support WPML
			MMP_Globals::register_single_string("Marker (ID {$new_marker_id}) name", $result['markername'], $ml_checked);
			MMP_Globals::register_single_string("Marker (ID {$new_marker_id}) popuptext", $result['popuptext'], $ml_checked);
			MMP_Globals::register_single_string("Marker (ID {$new_marker_id}) address", $result['address'], $ml_checked);
		}
		$new_marker_ids[] = $new_marker_id;
	}
	$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
	$checked_markers_prepared = implode(",", $checkedmarkers);
	$checked_markers = preg_replace('/[a-z|A-Z| |\=]/', '', $checked_markers_prepared);
	echo '<p><div class="notice notice-success is-dismissible" style="padding:10px;">' . __('The selected markers have been duplicated. New marker IDs:','lmm') . ' ';
	foreach ($new_marker_ids as $new_id){
		echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $new_id . '">' . $new_id . '</a> - ';
	}
	echo '</div>';
} else {
?>
<h1><?php _e('List all markers','lmm') ?></h1>
<div style="float:right;">
	<form method="POST" onkeypress="return event.keyCode != 13;"> <!--prevent search start with enter-->
		<?php $layerlist = $wpdb->get_results('SELECT * FROM `'.$table_name_layers.'` WHERE `id` > 0', ARRAY_A); ?>
		<select id="layerfilter" name="layerfilter" style="vertical-align:top;">
			<option value="0"><?php _e('All layers', 'lmm') ?></option>
			<?php
				foreach ($layerlist as $row) {
					if ($row['multi_layer_map'] == 1) {
						echo '<option value="' . $row['id'] . '"  disabled="disabled" title="' . esc_attr__('This is a multi-layer map - please select markers from included sublayers','lmm') . '">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . '/MLM - ' . __('layer IDs','lmm') . ': ' . substr($row['multi_layer_map_list'], 0, 70). ')</option>';
					} else {
						echo '<option value="' . $row['id'] . '">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . ')</option>';
					}
				}
			?>
		</select>
		<input type="text" placeholder="<?php _e('Search markers', 'lmm') ?>" id="searchtext" name="searchtext" value="<?php echo (isset($_POST['searchtext']) != NULL) ? htmlspecialchars(stripslashes($_POST['searchtext'])) : "" ?>"/>
	</form>
</div>

<div style="display:inline;">
	<p>
	<span id="exportlinkstext"><a style="text-decoration:none;cursor:pointer;"><?php _e('Show API links for all markers','lmm'); ?></a></span></div>
	</p>
<div id="exportlinks" style="display:none;">
<p>
<?php echo "<a href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/kml/layer/all/?markername=" . $lmm_options[ 'misc_kml' ]) . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-kml.png\" /></a> <a href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/kml/layer/all/?markername=" . $lmm_options[ 'misc_kml' ]) . "\" style=\"text-decoration:none;\">" . __('Export all markers as KML','lmm') . "</a> <a href=\"https://www.mapsmarker.com/kml\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/geojson/layer/all/?full=yes&full_icon_url=yes") . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-json.png\" /></a> <a target=\"_blank\" href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/geojson/layer/all/?full=yes&full_icon_url=yes") . "\" style=\"text-decoration:none;\">" . __('Export all markers as GeoJSON','lmm') . "</a> <a href=\"https://www.mapsmarker.com/geojson\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/georss/layer/all") . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-georss.png\" /></a> <a target=\"_blank\" href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/georss/layer/all") . "\" style=\"text-decoration:none;\">" . __('Subscribe to markers via GeoRSS','lmm') . "</a> <a href=\"https://www.mapsmarker.com/georss\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/wikitude/layer/all") . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-wikitude.png\" /></a> <a target=\"_blank\" href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/wikitude/layer/all") . "\" style=\"text-decoration:none;\">" . __('Export all markers as ARML for Wikitude','lmm') . "</a> <a href=\"https://www.mapsmarker.com/wikitude\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/geositemap/") . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-sitemap.png\" /></a> <a target=\"_blank\" href=\"" . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . "/geositemap/") . "\" style=\"text-decoration:none;\">" . __('Geo Sitemap','lmm') . "</a>&nbsp;<a href=\"https://www.mapsmarker.com/geo-sitemap\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to submit your Geo Sitemap to Google','lmm') . "\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"https://www.mapsmarker.com/mapsmarker-api\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-page.png\" /></a> <a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_apis\" title=\"" . esc_attr__('Click here for more information on how to use the MapsMarker API','lmm') . "\" style=\"text-decoration:none;\">Maps Marker API</a>"; ?>
</p>
</div>
<div class="tablenav top">
	<span id="search_title"><?php echo (isset($_POST['searchtext']) != NULL) ? __('Search result','lmm') : __('Total','lmm') ?></span>: <?php echo '<span id="totalmarkers">'.$mcount.'</span>'; echo ' ' . $mcount_singular_plural = ($mcount == 1) ? __('marker','lmm') : __('markers','lmm'); ?>
	<span id="search_reset_link" style="display:none;">(<a style="text-decoration:none;" href="javascript:void(0)"><?php echo __('reset','lmm'); ?></a>)</span>
	<?php echo $pager; ?>
</div>
<form method="POST" id="form-list-markers">
	<table cellspacing="0" id="list-markers" class="wp-list-table widefat fixed bookmarks" style="border-radius:5px;">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
				<th class="manage-column before_primary column-id sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.id&order=<?php echo $sortorder; ?>"><span>ID</span><span class="sorting-indicator"></span></a></th>
				<th class="manage-column column-primary column-markername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.markername&order=<?php echo $sortorder; ?>"><span><?php _e('Marker name','lmm') ?></span><span class="sorting-indicator"></span></a></th>
				<th class="manage-column column-icon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.icon&order=<?php echo $sortorder; ?>"><span><?php _e('Icon', 'lmm') ?></span><span class="sorting-indicator"></span></a></th>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) { ?>
				<th class="manage-column column-address sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.address&order=<?php echo $sortorder; ?>"><span><?php _e('Location','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
				<th class="manage-column column-popuptext sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.popuptext&order=<?php echo $sortorder; ?>"><span><?php _e('Popup text','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) { ?>
				<th class="manage-column column-layername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=l.name&order=<?php echo $sortorder; ?>"><span><?php _e('Layer', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
				<th class="manage-column column-openpopup"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.openpopup&order=<?php echo $sortorder; ?>"><span><?php _e('Popup status', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_panelstatus' ] == 1 )) { ?>
				<th class="manage-column column-openpopup"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.panel&order=<?php echo $sortorder; ?>"><span><?php _e('Panel status', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
				<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
				<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
				<th class="manage-column column-zoom" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.zoom&order=<?php echo $sortorder; ?>"><span><?php _e('Zoom', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
				<th class="manage-column column-basemap" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.basemap&order=<?php echo $sortorder; ?>"><span><?php _e('Basemap', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
				<th class="manage-column column-createdby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdby&order=<?php echo $sortorder; ?>"><span><?php _e('Created by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
				<th class="manage-column column-createdon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdon&order=<?php echo $sortorder; ?>"><span><?php _e('Created on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
				<th class="manage-column column-updatedby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedby&order=<?php echo $sortorder; ?>"><span><?php _e('Updated by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
				<th class="manage-column column-updatedon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedon&order=<?php echo $sortorder; ?>"><span><?php _e('Updated on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
				<th class="manage-column column-code" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.controlbox&order=<?php echo $sortorder; ?>"><span><?php _e('Controlbox status','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_used_in_content' ] == 1 )) { ?>
				<th class="manage-column column-usedincontent" scope="col"><?php _e('Used in content','lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) { ?>
				<th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) { ?>
				<th class="manage-column column-kml" scope="col">KML<a href="https://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) { ?>
				<th class="manage-column column-fullscreen" scope="col"><?php _e('Fullscreen', 'lmm') ?><span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) { ?>
				<th class="manage-column column-qr-code" scope="col"><?php _e('QR code', 'lmm') ?><span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) { ?>
				<th class="manage-column column-geojson" scope="col">GeoJSON<a href="https://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) { ?>
				<th class="manage-column column-georss" scope="col">GeoRSS<a href="https://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) { ?>
				<th class="manage-column column-wikitude" scope="col">Wikitude<a href="https://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
				<th class="manage-column before_primary column-id sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.id&order=<?php echo $sortorder; ?>"><span>ID</span><span class="sorting-indicator"></span></a></th>
				<th class="manage-column column-primary column-markername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.markername&order=<?php echo $sortorder; ?>"><span><?php _e('Marker name','lmm') ?></span><span class="sorting-indicator"></span></a></th>
				<th class="manage-column column-icon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.icon&order=<?php echo $sortorder; ?>"><span><?php _e('Icon', 'lmm') ?></span><span class="sorting-indicator"></span></a></th>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) { ?>
				<th class="manage-column column-address" scope="col"><?php _e('Location','lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
				<th class="manage-column column-popuptext sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.popuptext&order=<?php echo $sortorder; ?>"><span><?php _e('Popup text','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) { ?>
				<th class="manage-column column-layername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=l.name&order=<?php echo $sortorder; ?>"><span><?php _e('Layer', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
				<th class="manage-column column-openpopup"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.openpopup&order=<?php echo $sortorder; ?>"><span><?php _e('Popup status', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_panelstatus' ] == 1 )) { ?>
				<th class="manage-column column-openpopup"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.panel&order=<?php echo $sortorder; ?>"><span><?php _e('Panel status', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
				<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
				<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
				<th class="manage-column column-zoom" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.zoom&order=<?php echo $sortorder; ?>"><span><?php _e('Zoom', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
				<th class="manage-column column-basemap" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.basemap&order=<?php echo $sortorder; ?>"><span><?php _e('Basemap', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
				<th class="manage-column column-createdby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdby&order=<?php echo $sortorder; ?>"><span><?php _e('Created by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
				<th class="manage-column column-createdon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdon&order=<?php echo $sortorder; ?>"><span><?php _e('Created on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
				<th class="manage-column column-updatedby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedby&order=<?php echo $sortorder; ?>"><span><?php _e('Updated by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
				<th class="manage-column column-updatedon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedon&order=<?php echo $sortorder; ?>"><span><?php _e('Updated on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
				<th class="manage-column column-code" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.controlbox&order=<?php echo $sortorder; ?>"><span><?php _e('Controlbox status','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_used_in_content' ] == 1 )) { ?>
				<th class="manage-column column-usedincontent" scope="col"><?php _e('Used in content','lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) { ?>
				<th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'lmm') ?></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) { ?>
				<th class="manage-column column-kml" scope="col">KML<a href="https://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) { ?>
				<th class="manage-column column-fullscreen" scope="col"><?php _e('Fullscreen', 'lmm') ?><span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) { ?>
				<th class="manage-column column-qr-code" scope="col"><?php _e('QR code', 'lmm') ?><span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) { ?>
				<th class="manage-column column-geojson" scope="col">GeoJSON<a href="https://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) { ?>
				<th class="manage-column column-georss" scope="col">GeoRSS<a href="https://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) { ?>
				<th class="manage-column column-wikitude" scope="col">Wikitude<a href="https://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
$markernonce = wp_create_nonce('massaction-nonce'); //info: for delete-links
if (count($marklist) < 1) {
	echo '<tr><td colspan="9">'.__('No marker created yet', 'lmm').'</td></tr>';
} else {
$ml_checked = MMP_Globals::check_multilingual();
foreach ($marklist as $row) {
	if (MMP_Globals::check_capability('view_others', $row['createdby'])) {
		if (MMP_Globals::check_capability('delete', $row['createdby'])) {
			$delete_link_marker = '&nbsp;|&nbsp;<a style="color:red;" onclick="if ( confirm( \'' . esc_attr__('Do you really want to delete this marker?', 'lmm') . ' (' . $row['markername'] . ' - ID ' . $row['id'] . ')\' ) ) { return true;}return false;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&action=delete&id=' . $row['id'] . '&_wpnonce=' . $markernonce . '">' . __('delete','lmm') . '</a>';
		} else {
			$delete_link_marker = '';
		}
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$edit_link_marker = '<strong><a title="' . esc_attr__('edit marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('edit','lmm') . '</a>';
			$duplicate_link_marker = '&nbsp;|&nbsp;<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&id=' . $row['id'] . '&action=duplicate&_wpnonce=' . $markernonce . '">' . __('duplicate','lmm') . '</a>';
		} else {
			$edit_link_marker = '<a title="' . esc_attr__('View marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('View marker','lmm') . '</a>';
			$duplicate_link_marker = '';
		}
		if ($ml_checked == 'wpml') {
			$translate_url = admin_url('admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search='. urlencode($row['markername']));
		} elseif ($ml_checked == 'pll') {
			$translate_url = admin_url('admin.php?page=mlang_strings&s='. urlencode($row['markername']) . '&group=Maps+Marker+Pro');
		} else {
			$translate_url = 'https://www.mapsmarker.com/multilingual';
		}
		$translate_link = '&nbsp;|&nbsp;<a href="'. $translate_url .'">'.  __('translate', 'lmm')  .'</a>';

		/**
		* 2.4 get thelayers
		* @Waseem
		**/
		$rowlayername = '';
		$marker_layers = json_decode($row['layer'],true);

		if(!empty($marker_layers)) {
			$layers = $wpdb->get_results('SELECT id,name as layername FROM '.$table_name_layers.' WHERE id IN('.implode(',', $marker_layers).')',ARRAY_A);
			foreach($layers as $layer) {
				if (MMP_Globals::check_capability('edit', $row['createdby'])) {
					$rowlayername .= ($layer['id'] == 0) ? "" . __('unassigned','lmm') . "<br/>" : "<a title='" . esc_attr__('Edit layer ','lmm') . $layer['id'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $layer['id'] . "'>" . htmlspecialchars($layer['layername']) . " (ID " .$layer['id'] . ")</a><br/>";
				} else {
					$rowlayername .= ($layer['id'] == 0) ? "" . __('unassigned','lmm') . "<br/>" : "<a title='" . esc_attr__('view layer ','lmm') . $layer['id'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $layer['id'] . "'>" . htmlspecialchars($layer['layername']) . " (ID " .$layer['id'] . ")</a><br/>";
				}
			}
		}
		$openpopupstatus = ($row['openpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
		$openpanelstatus = ($row['panel'] == 1) ? __('visible','lmm') : __('hidden','lmm');
		 if ($row['controlbox'] == 0) {
			$controlboxstatus = __('hidden','lmm');
		} else if ($row['controlbox'] == 1) {
			$controlboxstatus = __('collapsed (except on mobiles)','lmm');
		} else if ($row['controlbox'] == 2) {
			$controlboxstatus = __('expanded','lmm');
		}
		$column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Location', 'lmm').'">' . stripslashes(htmlspecialchars($row['address'])) . '</td>' : '';
		$popuptextabstract = (strlen($row['popuptext']) >= 90) ? "...": "";
		//info: set column display variables - need for for-each
		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ? '<td class="lmm-border"><a title="' . esc_attr__('edit marker ', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
		} else {
			$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ? '<td class="lmm-border"><a title="' . esc_attr__('View marker', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
		}
		$column_layer = ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Layer', 'lmm').'">' . stripslashes($rowlayername) . '</td>' : '';
		$column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Popup status', 'lmm').'">' . $openpopupstatus . '</td>' : '';
		$column_panelstatus = ((isset($lmm_options[ 'misc_marker_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_panelstatus' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Panel status', 'lmm').'">' . $openpanelstatus . '</td>' : '';
		$column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Coordinates', 'lmm').'">Lat: ' . $row['lat'] . '<br/>Lon: ' . $row['lon'] . '</td>' : '';
		$column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Map Size', 'lmm').'">' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
		$column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Zoom', 'lmm').'">' . $row['zoom'] . '</td>' : '';
		$column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Controlbox status', 'lmm').'">' . $controlboxstatus . '</td>' : '';
		//info: workaround - select shortcode on input focus doesnt work on iOS
		global $wp_version;
		if ( version_compare( $wp_version, '3.4', '>=' ) ) {
			$is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
			$shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
		} else {
			$shortcode_select = '';
		}
		$column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Shortcode', 'lmm').'"><input ' . $shortcode_select . ' style="width:100%;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['id'] . '&quot;]"></td>' : '';
		$column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('KML', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['id'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" /><br/>KML</a></td>' : '';
		$column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Fullscreen', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
		$column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('QR Code', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><br/>' . __('QR code','lmm') . '</a></td>' : '';
		$column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoJSON', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['id'] . '/?callback=jsonp&full=yes&full_icon_url=yes') . '" target="_blank" title="' . esc_attr__('Export as GeoJSON','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '"><br/>GeoJSON</a></td>' : '';
		$column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoRSS', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as GeoRSS','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo" alt="' . esc_attr__('Export as GeoRSS','lmm') . '"><br/>GeoRSS</a></td>' : '';
		$column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Wikitude', 'lmm').'"><a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['id'] . '/') . '" target="_blank" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '"><br/>Wikitude</a></td>' : '';
		$column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Basemap', 'lmm').'">' . $row['basemap'] . '</td>' : '';
		$column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created by', 'lmm').'">' . esc_html($row['createdby']) . '</td>' : '';
		$column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created on', 'lmm').'">' . $row['createdon'] . '</td>' : '';
		$column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated by', 'lmm').'">' . esc_html($row['updatedby']) . '</td>' : '';
		$column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated on', 'lmm').'">' . $row['updatedon'] . '</td>' : '';

		if (MMP_Globals::check_capability('edit', $row['createdby'])) {
			$css_table_background = '';
		} else {
			$css_table_background = 'background:#f6f6f6;';
		}
		echo '<tr valign="middle" class="alternate" id="link-' . $row['id'] . '" style="' . $css_table_background . '">
		<th class="lmm-border check-column" scope="row"><input type="checkbox" value="' . $row['id'] . '" name="checkedmarkers[]"></th>
		<td class="lmm-border before_primary" data-colname="'.esc_attr__('ID', 'lmm').'">' . $row['id'] . '</td>
		<td class="lmm-border column-primary">' . $edit_link_marker . $duplicate_link_marker . $delete_link_marker . $translate_link . '</div>  <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>  </td>';
		echo '<td class="lmm-border" data-colname="'.esc_attr__('Icon', 'lmm').'">';
		if ($row['icon'] != null) {
			echo '<img src="' . $defaults_marker_icon_url . '/' . esc_html($row['icon']) . '" title="' . esc_html($row['icon']) . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" />';
		} else {
			echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" />';
		};
		echo '</td>';
		echo  $column_address . '
		  ' . $column_popuptext . '
		  ' . $column_layer . '
		  ' . $column_openpopup . '
		  ' . $column_panelstatus . '
		  ' . $column_coordinates . '
		  ' . $column_mapsize . '
		  ' . $column_zoom . '
		  ' . $column_basemap . '
		  ' . $column_createdby . '
		  ' . $column_createdon . '
		  ' . $column_updatedby . '
		  ' . $column_updatedon . '
		  ' . $column_controlbox;
		  echo ((isset($lmm_options[ 'misc_marker_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_used_in_content' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Used in content', 'lmm').'">' . MMP_Globals::get_map_shortcodes($row['id'], 'marker') . '</td>' : '';
		  echo $column_shortcode . '
		  ' . $column_kml . '
		  ' . $column_fullscreen . '
		  ' . $column_qr_code . '
		  ' . $column_geojson . '
		  ' . $column_georss . '
		  ' . $column_wikitude . '
		</tr>';
	} //info: end (MMP_Globals::check_capability('view_others', $row['createdby'])) check
}//info: end foreach $marklist as row
}
?>
		</tbody>
	</table>

	<?php
	if ( (current_user_can( $lmm_options[ 'capabilities_delete_others' ])) || (current_user_can( $lmm_options[ 'capabilities_edit_others' ])) ) {
		$delete_edit_others_actions_visibility = '';
		$delete_edit_others_infotext = '';
		wp_nonce_field('massaction-nonce');
	} else {
		$delete_edit_others_actions_visibility = 'disabled="disabled"';
		$delete_edit_others_infotext = __('Your user does not have the right to perform this action','lmm');
	}
	?>
	<table cellspacing="0" style="width:auto;margin-top:20px;border-radius:5px;" class="wp-list-table widefat fixed bookmarks">
	<tr><td>
	<p><b><?php _e('Bulk actions for selected markers','lmm') ?></b></p>
	<?php
	$layerlist = $wpdb->get_results('SELECT * FROM `'.$table_name_layers.'` WHERE `id` > 0 AND `multi_layer_map` = 0', ARRAY_A);
	?>
	<p><input <?php echo $delete_edit_others_actions_visibility; ?> type="radio" id="duplicateselected" name="bulkactions-markers" value="duplicateselected" /> <label for="duplicateselected" title="<?php echo $delete_edit_others_infotext; ?>"><?php _e('duplicate and assign to the following layer:','lmm') ?></label>
	<select id="layer-duplicate" name="layer-duplicate">
		<option <?php echo $delete_edit_others_actions_visibility; ?> value="unchanged"><?php _e('same layer(s) as original marker','lmm') ?></option>
		<option <?php echo $delete_edit_others_actions_visibility; ?> value="0"><?php _e('unassigned','lmm') ?></option>
		<?php
			foreach ($layerlist as $row)
			echo '<option ' . $delete_edit_others_actions_visibility . ' value="' . $row['id'] . '">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . ')</option>';
		?>
	</select></p>
	<?php
	if (current_user_can( $lmm_options[ 'capabilities_delete_others' ])) {
		$deleteselected_visibility = '';
		$deleteselected_infotext = '';
	} else {
		$deleteselected_visibility = 'disabled="disabled"';
		$deleteselected_infotext = __('Your user does not have the right to perform this action','lmm');
	} ?>
	<p><input <?php echo $deleteselected_visibility; ?> type="radio" id="deleteselected" name="bulkactions-markers" value="deleteselected" /> <label for="deleteselected" title="<?php echo $deleteselected_infotext; ?>"><?php _e('delete','lmm') ?></label></p>
	<?php
	if (current_user_can( $lmm_options[ 'capabilities_edit_others' ])) {
		$deleteassignselected_visibility = '';
		$deleteassignselected_infotext = '';
	} else {
		$deleteassignselected_visibility = 'disabled="disabled"';
		$deleteassignselected_infotext = __('Your user does not have the right to perform this action','lmm');
	} ?>
	<input <?php echo $deleteassignselected_visibility; ?> type="radio" id="assignselected" name="bulkactions-markers" value="assignselected" /> <label for="assignselected" title="<?php echo $deleteassignselected_infotext;?>"><?php _e('assign to the following layer(s):','lmm') ?></label>
	<select id="layer" name="layer[]" style="width:200px;" multiple="multiple">
	<option <?php echo $deleteassignselected_visibility; ?> value="0"><?php _e('unassigned','lmm') ?></option>
	<?php
		foreach ($layerlist as $row)
		echo '<option ' . $deleteassignselected_visibility . ' value="' . $row['id'] . '">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . ')</option>';
	?>
	</select><br/>
	<input id="bulk-actions-btn" class="button-secondary" type="submit" value="<?php _e('submit', 'lmm') ?>" style="margin: 0 0 5px 18px;" disabled="disabled" />
	</td></tr></table>
</form>
<div class="tablenav bottom"><div class="tablenav-pages"><?php echo $pager; ?></div></div>
<?php } //info: end delete/assign selected markers ?>

<script type="text/javascript">
(function($) {
	//info: enable submit button on bulk action selection
	$('#form-list-markers input[name="bulkactions-markers"]').click(function () {
		document.getElementById('bulk-actions-btn').disabled = false;
	});
	//info: show confirm alert for delete bulk action
	$('#form-list-markers').submit(function () {
		if($('input[name="bulkactions-markers"]:checked').val() == 'deleteselected')
		{
			if (confirm('<?php esc_attr_e('Do you really want to delete the selected marker(s)? (cannot be undone)','lmm') ?>')) {
				return true;
			} else {
				return false;
			}
		}
	});
	//info: show all API links on click on simplified editor
	$('#exportlinkstext').click(function(e) {
			$('#exportlinkstext').hide();
			$('#exportlinks').show();
	});
	$("#layer").select2({formatNoMatches: "<?php esc_attr_e('No matches found','lmm'); ?>"});
	$('#layer').on('change',function(e){
		var values = $(this).select2("val");
		if(values.length > 0){
			$('#assignselected').prop('checked', true);
		}else{
			$('#assignselected').prop('checked', false);
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
})(jQuery)
</script>
<!--defaults for list-markers-layers.js -->
<input type="hidden" id="lmm_list_maps_subaction" value="lmm_list_markers" />
<input type="hidden" id="defaults_texts_no_search_results" value="<?php echo __('No matches found','lmm'); ?>" />
<input type="hidden" id="defaults_texts_search_result" value="<?php echo __('Search result','lmm'); ?>" />
<input type="hidden" id="defaults_texts_total" value="<?php echo __('Total','lmm'); ?>" />
<?php include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php'); ?>
