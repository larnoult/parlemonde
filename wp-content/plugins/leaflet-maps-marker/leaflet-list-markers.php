<?php
/*
    List all markers - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-list-markers.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
global $wpdb;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';

$radius = 1;
$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
//info: security check if input variable is valid
$columnsort_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox');
$columnsort_input = isset($_POST['orderby']) ? esc_sql($_POST['orderby']) : (isset($_GET['orderby']) ? esc_sql($_GET['orderby']) : $lmm_options[ 'misc_marker_listing_sort_order_by' ]);
$columnsort = (in_array($columnsort_input, $columnsort_values)) ? $columnsort_input : $lmm_options[ 'misc_marker_listing_sort_order_by' ];

//info: security check if input variable is valid
$columnsortorder_values = array('asc','desc','ASC','DESC');
$columnsortorder_input = isset($_POST['order']) ? esc_sql($_POST['order']) : (isset($_GET['order']) ? esc_sql($_GET['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ]);
$columnsortorder = (in_array($columnsortorder_input, $columnsortorder_values)) ? $columnsortorder_input : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];

$offset = ($pagenum - 1) * intval($lmm_options[ 'markers_per_page' ]);
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$searchtext = isset($_POST['searchtext']) ? '%' .esc_sql($_POST['searchtext']) . '%' : (isset($_GET['searchtext']) ? '%' . esc_sql($_GET['searchtext']) : '') . '%';
$markers_per_page_validated = intval($lmm_options[ 'markers_per_page' ]);
if ($action == 'search') {
	$markersearchnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : '';
	if (! wp_verify_nonce($markersearchnonce, 'markersearch-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
	$mcount = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table_name_markers` WHERE id like '%s' OR markername like '%s' OR popuptext like '%s' OR address like '%s'", $searchtext, $searchtext, $searchtext, $searchtext)));
	$marklist = $wpdb->get_results( $wpdb->prepare("SELECT m.id,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address,l.name AS layername,l.id as layerid FROM `$table_name_markers` AS m LEFT OUTER JOIN `$table_name_layers` AS l ON m.layer=l.id WHERE m.id like '%s' OR m.markername like '%s' OR m.popuptext like '%s' OR m.address like '%s' order by $columnsort $columnsortorder LIMIT $markers_per_page_validated OFFSET $offset", $searchtext, $searchtext, $searchtext, $searchtext), ARRAY_A);
} else {
	$mcount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
	$marker_per_page = intval($lmm_options[ 'markers_per_page' ]);
 	$marklist = $wpdb->get_results( "SELECT m.id,CONCAT(m.lat,',',m.lon) AS coords,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,m.address,l.name AS layername,l.id as layerid FROM `$table_name_markers` AS m LEFT OUTER JOIN $table_name_layers AS l ON m.layer=l.id order by $columnsort $columnsortorder LIMIT $marker_per_page OFFSET $offset", ARRAY_A);
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
    $pager .= '<span class="paging-input"><input type="text" size="2" value="'.$pagenum.'" name="paged" class="current-page"> <!--total pages <span class="total-pages">'.$maxpage.' </span>--></span>';
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
	$deleteselected = ( isset($_POST['bulkactions-markers']) && ($_POST['bulkactions-markers'] == 'deleteselected') ) ? '1' : '0';
	$assignselected = ( isset($_POST['bulkactions-markers']) && ($_POST['bulkactions-markers'] == 'assignselected') ) ? '1' : '0';
	$massactionnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if ( ($deleteselected == '1') && isset($_POST['checkedmarkers']) ) {
		if (! wp_verify_nonce($massactionnonce, 'massaction-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
		$checked_markers_prepared = implode(",", $_POST['checkedmarkers']);
		$checked_markers = preg_replace('/[a-z|A-Z| |\=]/', '', $checked_markers_prepared);
		$wpdb->query( "DELETE FROM `$table_name_markers` WHERE `id` IN (" . htmlspecialchars($checked_markers) . ")");
		$wpdb->query( "OPTIMIZE TABLE `$table_name_markers`" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The selected markers have been deleted','lmm') . ' (ID ' . htmlspecialchars($checked_markers) . ')</div>';
		echo '<p><a class=\'button-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('list all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new maker','lmm') . '</a></p>';
	} else if ( ($assignselected == '1') && isset($_POST['checkedmarkers']) ) {
		if (! wp_verify_nonce($massactionnonce, 'massaction-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according admin page!','lmm').'');
		$checked_markers_prepared = implode(",", $_POST['checkedmarkers']);
		$checked_markers = preg_replace('/[a-z|A-Z| |\=]/', '', $checked_markers_prepared);
		$wpdb->query( "UPDATE `$table_name_markers` SET `layer` = " . intval($_POST['layer']) . " where `id` IN (" . $checked_markers . ")");
		echo '<p><div class="updated" style="padding:10px;">' . __('The selected markers have been assigned to the selected layer','lmm') . ' (' . __('Marker','lmm') . ' ID ' . htmlspecialchars($checked_markers) . ', ' . __('Layer','lmm') . ' ID ' . htmlspecialchars($_POST['layer']) . ')</div>';
		echo '<p><a class=\'button-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('list all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new maker','lmm') . '</a></p>';
	} else {
	?>
	<h1><?php _e('List all markers','lmm') ?></h1>
	<div style="float:right;">
	<?php $nonce= wp_create_nonce  ('markersearch-nonce'); ?>
		<form method="post">
			<?php wp_nonce_field('markersearch-nonce'); ?>
			<input type="hidden" name="action" value="search" />
			<input type="text" id="searchtext" name="searchtext" value="<?php echo (isset($_POST['searchtext']) != NULL) ? htmlspecialchars(stripslashes($_POST['searchtext'])) : "" ?>"/>
			<input type="submit" class="button" name="searchsubmit" value="<?php _e('Search markers', 'lmm') ?>"/>
		</form>
		<?php echo $showall = (isset($_POST['searchtext']) != NULL) ? "<a style=\"text-decoration:none;\" href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_markers\">" . __('list all markers','lmm') . "</a>" : ""; ?>
	</div>

	<div style="display:inline;">
		<p>
		<span id="exportlinkstext"><a style="text-decoration:none;cursor:pointer;"><?php _e('Show API links for all markers','lmm'); ?></a></span></div>
		</p>
	<div id="exportlinks" style="display:none;">
	<p>
	<?php echo "<a href=\"" . LEAFLET_PLUGIN_URL . "leaflet-kml.php?layer=all&name=" . $lmm_options[ 'misc_kml' ] . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-kml.png\" /></a> <a href=\"" . LEAFLET_PLUGIN_URL . "leaflet-kml.php?layer=all&name=" . $lmm_options[ 'misc_kml' ] . "\" style=\"text-decoration:none;\">" . __('Export all markers as KML','lmm') . "</a> <a href=\"https://www.mapsmarker.com/kml\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geojson.php?layer=all&full=yes&full_icon_url=yes\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-json.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geojson.php?layer=all&full=yes&full_icon_url=yes\" style=\"text-decoration:none;\">" . __('Export all markers as GeoJSON','lmm') . "</a> <a href=\"https://www.mapsmarker.com/geojson\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-georss.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-georss.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-georss.php?layer=all\" style=\"text-decoration:none;\">" . __('Subscribe to markers via GeoRSS','lmm') . "</a> <a href=\"https://www.mapsmarker.com/georss\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"" . LEAFLET_PLUGIN_URL . "leaflet-wikitude.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-wikitude.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-wikitude.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as ARML for Wikitude','lmm') . "</a> <a href=\"https://www.mapsmarker.com/wikitude\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . "\"> <img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geositemap.php\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-sitemap.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geositemap.php\" style=\"text-decoration:none;\">" . __('Geo Sitemap','lmm') . "</a>&nbsp;<a href=\"https://www.mapsmarker.com/geo-sitemap\" target=\"_blank\" title=\"" . esc_attr__('Click here for more information on how to submit your Geo Sitemap to Google','lmm') . "\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-question-mark.png\" width=\"12\" height=\"12\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"https://www.mapsmarker.com/mapsmarker-api\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "inc/img/icon-menu-page.png\" /></a> <a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_apis\" title=\"" . esc_attr__('Click here for more information on how to use the MapsMarker API','lmm') . "\" style=\"text-decoration:none;\">MapsMarker API</a>"; ?>
	</p>
	</div>
	<div class="tablenav top">
		<?php echo (isset($_POST['searchtext']) != NULL) ? __('Search result','lmm') : __('Total','lmm') ?>: <?php echo $mcount; echo ' ' . $mcount_singular_plural = ($mcount == 1) ? __('marker','lmm') : __('markers','lmm'); echo $pager; ?>
	</div>
	<form method="POST">
		<table id="list-markers" cellspacing="0" class="wp-list-table widefat fixed bookmarks">
			<thead>
				<tr>
					<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
					<th class="manage-column before_primary column-id sortable before_primary <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.id&order=<?php echo $sortorder; ?>"><span>ID</span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-primary column-markername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.markername&order=<?php echo $sortorder; ?>"><span><?php _e('Marker name','lmm') ?></span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-icon sortable before_primary <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.icon&order=<?php echo $sortorder; ?>"><span><?php _e('Icon', 'lmm') ?></span><span class="sorting-indicator"></span></a></th>
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
			</thead>
			<tfoot>
				<tr>
					<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
					<th class="manage-column before_primary column-id sortable before_primary <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.id&order=<?php echo $sortorder; ?>"><span>ID</span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-primary column-markername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.markername&order=<?php echo $sortorder; ?>"><span><?php _e('Marker name','lmm') ?></span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-icon sortable before_primary <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.icon&order=<?php echo $sortorder; ?>"><span><?php _e('Icon', 'lmm') ?></span><span class="sorting-indicator"></span></a></th>
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
  $markernonce = wp_create_nonce('marker-nonce'); //info: for delete-links
  if (count($marklist) < 1)
    echo '<tr><td colspan="11">'.__('No marker created yet', 'lmm').'</td></tr>';
  else
    foreach ($marklist as $row)
	{
		if (current_user_can( $lmm_options[ 'capabilities_delete' ])) {
			$delete_link_marker = '<div style="float:right;"><a onclick="if ( confirm( \'' . esc_attr__('Do you really want to delete this marker?', 'lmm') . ' (' . $row['markername'] . ' - ID ' . $row['id'] . ')\' ) ) { return true;}return false;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=delete&id=' . $row['id'] . '&_wpnonce=' . $markernonce . '" class="submitdelete">' . __('delete','lmm') . '</a></div>';
		} else {
			$delete_link_marker = '';
		}
     $rowlayername = ($row['layerid'] == 0) ? "" . __('unassigned','lmm') . "<br>" : "<a title='" . esc_attr__('Edit layer ','lmm') . $row['layer'] . "' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $row['layer'] . "'>" . htmlspecialchars($row['layername']) . " (ID " .$row['layerid'] . ")</a>";
     $openpopupstatus = ($row['openpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
     $openpanelstatus = ($row['panel'] == 1) ? __('visible','lmm') : __('hidden','lmm');
	 if ($row['controlbox'] == 0) { $controlboxstatus = __('hidden','lmm'); } else if ($row['controlbox'] == 1) { $controlboxstatus = __('collapsed (except on mobiles)','lmm'); } else if ($row['controlbox'] == 2) { $controlboxstatus = __('expanded','lmm'); };

	 $column_address = ((isset($lmm_options[ 'misc_marker_listing_columns_address' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_address' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Location', 'lmm').'">' . stripslashes(htmlspecialchars($row['address'])) . '</td>' : '';
     $popuptextabstract = (strlen($row['popuptext']) >= 90) ? "...": "";
     //info: set column display variables - need for for-each
     $column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ?
'<td class="lmm-border"><a title="' . esc_attr__('edit marker', 'lmm') . ' ' . $row['id'] . '" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
     $column_layer = ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) ?
'<td class="lmm-border" data-colname="'.esc_attr__('Layer', 'lmm').'">' . stripslashes($rowlayername) . '</td>' : '';
     $column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ?
'<td class="lmm-border" data-colname="'.esc_attr__('Popup status', 'lmm').'">' . $openpopupstatus . '</td>' : '';
     $column_panelstatus = ((isset($lmm_options[ 'misc_marker_listing_columns_panelstatus' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_panelstatus' ] == 1 )) ?
'<td class="lmm-border" data-colname="'.esc_attr__('Panel status', 'lmm').'">' . $openpanelstatus . '</td>' : '';
     $column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td data-colname="'.esc_attr__('Coordinates', 'lmm').'">Lat: ' . $row['lat'] . '<br/>Lon: ' . $row['lon'] . '</td>' : '';
     $column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Map size', 'lmm').'">' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
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
     $column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Shortcode', 'lmm').'"><input ' . $shortcode_select . ' style="width:206px;background:#f3efef;" type="text" value="[' . htmlspecialchars($lmm_options[ 'shortcode' ]) . ' marker=&quot;' . $row['id'] . '&quot;]"></td>' : '';
     $column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('KML', 'lmm').'"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['id'] . '&name=' . $lmm_options[ 'misc_kml' ] . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /><br/>KML</a></td>' : '';
     $column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Fullscreen', 'lmm').'"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
     $column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('QR Code', 'lmm').'"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo"><br/>' . __('QR code','lmm') . '</a></td>' : '';
     $column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoJSON', 'lmm').'"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $row['id'] . '&callback=jsonp&full=yes&full_icon_url=yes" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-logo"><br/>GeoJSON</a></td>' : '';
     $column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('GeoRSS', 'lmm').'"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo"><br/>GeoRSS</a></td>' : '';
     $column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;" class="lmm-border centeralize" data-colname="'.esc_attr__('Wikitude', 'lmm').'"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-logo"><br/>Wikitude</a></td>' : '';
     $column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Basemap', 'lmm').'">' . $row['basemap'] . '</td>' : '';
     $column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created by', 'lmm').'">' . esc_html($row['createdby']) . '</td>' : '';
     $column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Created on', 'lmm').'">' . $row['createdon'] . '</td>' : '';
     $column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated by', 'lmm').'">' . esc_html($row['updatedby']) . '</td>' : '';
     $column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td class="lmm-border" data-colname="'.esc_attr__('Updated on', 'lmm').'">' . $row['updatedon'] . '</td>' : '';
	  echo '<tr valign="middle" class="alternate" id="link-' . $row['id'] . '">
      <th class="lmm-border check-column" scope="row"><input type="checkbox" value="' . $row['id'] . '" name="checkedmarkers[]"></th>
      <td class="lmm-border before_primary">' . $row['id'] . '</td>
      <td class="lmm-border column-primary"><strong><a title="' . esc_attr__('edit marker','lmm') . ' (' . $row['id'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></strong><br/><div class="row-actions"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('edit','lmm') . '</a><span style="margin-left:20px;color:#ccc;" title="' . esc_attr__('Feature available in pro version only','lmm') . '">' . __('duplicate','lmm') . '</span><span style="margin-left:20px;color:#ccc;" title="' . esc_attr__('Feature available in pro version only','lmm') . '">' . __('translate','lmm') . '</span>' . $delete_link_marker . '</div> <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button> </td>
      <td class="lmm-border" data-colname="'.esc_attr__('Icon', 'lmm').'">';
      if ($row['icon'] != null) {
         echo '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/' . $row['icon'] . '" title="' . $row['icon'] . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" />';
         } else {
         echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" />';
	  }
      echo '</td>
		  ' . $column_address . '
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
		   echo ((isset($lmm_options[ 'misc_marker_listing_columns_used_in_content' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_used_in_content' ] == 1 )) ? '<td class="lmm-border" style="text-align:center;"><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-feature.png" width="65" height="15" /></a></td>' : '';
		  echo $column_shortcode . '
		  ' . $column_kml . '
		  ' . $column_fullscreen . '
		  ' . $column_qr_code . '
		  ' . $column_geojson . '
		  ' . $column_georss . '
		  ' . $column_wikitude . '
		  </tr>';
	}//info: end foreach
	?>
			</tbody>
		</table>

		<table cellspacing="0" style="width:auto;margin-top:20px;" class="wp-list-table widefat fixed bookmarks">
		<tr><td>
		<p><b><?php _e('Bulk actions for selected markers','lmm') ?></b></p>
		<?php wp_nonce_field('massaction-nonce'); ?>
		<p>
		<table>
		<tr><td style="margin:0;padding:0;border:none;">
		<input type="radio" id="duplicateselected" name="bulkactions-markers" value="duplicateselected" disabled="disabled" /> <label for="duplicateselected"><?php _e('duplicate and assign to the following layer:','lmm') ?></label>
		<select id="layer-duplicate" name="layer-duplicate">
		<option value="unchanged"><?php _e('same layer(s) as original marker','lmm') ?></option>
		<option value="0"><?php _e('unassigned','lmm') ?></option>
		<?php
			$layerlist = $wpdb->get_results('SELECT * FROM `'.$table_name_layers.'` WHERE `id` > 0 AND `multi_layer_map` = 0', ARRAY_A);
			foreach ($layerlist as $row)
			echo '<option value="' . $row['id'] . '">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . ')</option>';
		?>
		</select></td>
		<td style="margin:0;padding:1px 0 0 5px;border:none;"><a href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_pro_upgrade" title="<?php esc_attr_e('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') ; ?>"><img src="<?php  echo LEAFLET_PLUGIN_URL ?>inc/img/help-pro-feature.png" width="70" height="15" /></a>
		</td></tr>
		</table>
		</p>
		<?php if (current_user_can( $lmm_options[ 'capabilities_delete' ])) { ?>
		<p><input type="radio" id="deleteselected" name="bulkactions-markers" value="deleteselected" /> <label for="deleteselected"><?php _e('delete','lmm') ?></label></p>
		<?php } ?>
		<input type="radio" id="assignselected"  name="bulkactions-markers" value="assignselected" /> <label for="assignselected"><?php _e('assign to the following layer:','lmm') ?></label>
		<select id="layer" name="layer">
		<option value="0"><?php _e('unassigned','lmm') ?></option>
		<?php
			foreach ($layerlist as $row)
			echo '<option value="' . $row['id'] . '">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . ')</option>';
		?>
		</select><br/>
		<input class="button-secondary" type="submit" value="<?php _e('submit', 'lmm') ?>" style="margin: 0 0 5px 18px;"/>
		</td></tr></table>

	</form>
<?php } //info: end delete/assign selected markers ?>

<div class="tablenav bottom"><div class="tablenav-pages"><?php echo $pager; ?></div></div>

<script type="text/javascript">
//info: show all API links on click on simplified editor
(function($) {
	$('#exportlinkstext').click(function(e) {
			$('#exportlinkstext').hide();
			$('#exportlinks').show();
	});
	$('.toggle-row').click(function(){
		$(this).parent().toggleClass('dynamic_border');
	});
})(jQuery)
</script>
<?php include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php'); ?>