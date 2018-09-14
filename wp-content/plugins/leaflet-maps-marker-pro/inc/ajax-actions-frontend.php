<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'ajax-actions-frontend.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
//info: sanitize AJAX response from unwanted output (https://wp-dreams.com/articles/2014/10/removing-unwanted-output-from-ajax-responses/)
$left_delimiter = "!!LMM-AJAX-START!!";
$right_delimiter = "!!LMM-AJAX-END!!";

$ajax_results = array();

if( !isset( $_POST['lmm_ajax_nonce'] ) || !wp_verify_nonce($_POST['lmm_ajax_nonce'], 'lmm-ajax-nonce') ) {
	$ajax_results['status-class'] = 'error';
	$ajax_results['status-text'] = __('Permissions check failed or WordPress nonce has expired - please reload the page to try again!','lmm');
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
}

global $wpdb;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';

//info: global settings
$ajax_subaction = $_POST['lmm_ajax_subaction'];

/**********************************************/
if($ajax_subaction == 'lmm_list_markers'){
	$lmm_options = get_option('leafletmapsmarker_options');
	$lmm_base_url = MMP_Rewrite::get_base_url();
	$lmm_slug = MMP_Rewrite::get_slug();
	$mapid = (isset($_POST['mapid']))?$_POST['mapid']:'';
	$search_text = (isset($_POST['search_text']) && (trim($_POST['search_text']!='')))?esc_sql(strip_tags($_POST['search_text'])):'';
	//info: security check if input variable is valid
	$orderby_values = array('m.id','m.icon','m.markername','m.popuptext','l.name','m.openpopup','m.panel','m.zoom','m.basemap','m.createdon','m.createdby','m.updatedon','m.updatedby','m.controlbox','m.layer','m.address','m.kml_timestamp','distance_layer_center','distance_current_position');
	$order_by = (isset($_POST['order_by']) && (trim($_POST['order_by']!='')))?esc_sql(strip_tags($_POST['order_by'])):$lmm_options[ 'defaults_layer_listmarkers_order_by' ];
	$order_by = (in_array($order_by, $orderby_values))? $order_by : $lmm_options[ 'defaults_layer_listmarkers_order_by' ];
	//info: security check if input variable is valid
	$order_values = array('asc','desc','ASC','DESC');
	$order = (isset($_POST['order']) && (trim($_POST['order']!='')))?esc_sql(strip_tags($_POST['order'])):$lmm_options[ 'defaults_layer_listmarkers_sort_order' ];
	$order = (in_array($order, $order_values))? $order : $lmm_options[ 'defaults_layer_listmarkers_sort_order' ];

	$mapname = (isset($_POST['mapname']) && (trim($_POST['mapname']!='')))?esc_sql(strip_tags($_POST['mapname'])):'';
	$layerlat = (isset($_POST['layerlat']))?round(esc_sql($_POST['layerlat']), 4):'l.layerviewlat';
	$layerlon = (isset($_POST['layerlon']))?round(esc_sql($_POST['layerlon']), 4):'l.layerviewlon';

	$pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
	$per_page = (isset($_POST['per_page']) && intval($_POST['per_page']) != 0)?intval($_POST['per_page']):intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]);
	$offset = ($pagenum - 1) * $per_page;

	$multi_layer_map = isset($_POST['multi_layer_map']) ? intval($_POST['multi_layer_map']) : (isset($_GET['multi_layer_map']) ? intval($_GET['multi_layer_map']) : 1);
	$multi_layer_map_list = isset($_POST['multi_layer_map_list']) ? ($_POST['multi_layer_map_list']) : (isset($_GET['multi_layer_map_list']) ? ($_GET['multi_layer_map_list']) : '');
	$multi_layer_map_list_exploded = explode(',', $multi_layer_map_list);

	$id = (isset($_POST['id']) && intval($_POST['id']) != 0)?intval($_POST['id']):0;

	//info: set custom marker icon dir/url
	if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
		$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
	} else {
		$defaults_marker_icon_url = esc_url($lmm_options['defaults_marker_icon_url']);
	}
	$search_query = '';
	if(trim($search_text) != ''){
		//info: the search text is already escaped.
		$search_query .= " AND (m.markername LIKE '%$search_text%' ";
		$search_query .= " OR m.address LIKE '%$search_text%' ";
		$search_query .= " OR m.popuptext LIKE '%$search_text%' ) ";
	}
	//info: adding info if more markers are available than listed in markers list
    $markercount = 0;
    if ($multi_layer_map == 0) {
		$markercount = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') '. $search_query .' WHERE l.id='.intval($id));
    } else if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == 'all' ) ) {
		$markercount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
    } else if ( ($multi_layer_map == 1) && ( $multi_layer_map_list != NULL ) && ($multi_layer_map_list != 'all') ) {
		foreach ($multi_layer_map_list_exploded as $mlmrowcount){
			$mlm_count_temp{$mlmrowcount} = $wpdb->get_var('SELECT count(*) FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') '. $search_query .' WHERE l.id='.intval($mlmrowcount));
		}
		$markercount = array_sum($mlm_count_temp);
    } else if ( ($multi_layer_map == 1) && ( $multi_layer_map_list == NULL ) ) {
		$markercount = 0;
    }
	//info: sqls for singe and multi-layer-maps
	if ($multi_layer_map == 0) {
		if($order_by == 'distance_layer_center'){
			$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
			if(isset($lmm_options[ 'defaults_layer_listmarkers_show_distance' ] ) && $order_by != 'distance_layer_center'){
				$layer_marker_list = $wpdb->get_results('SELECT  '. $distance_query .' l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id.' '.$search_query.' ORDER BY '. $order_by .' ' . $order . ' LIMIT ' . $per_page ." OFFSET $offset", ARRAY_A);
			}else{
				$layer_marker_list = $wpdb->get_results('SELECT  '. $distance_query .' l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id.' '.$search_query.' ORDER BY distance ' . $order . ' LIMIT ' . $per_page ." OFFSET $offset", ARRAY_A);
			}
		}else if( $order_by == 'distance_current_position' ){
			$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( ".$layerlat." ) ) * cos( radians( ".$layerlon." ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(".$layerlat.")) ) ) AS distance,";
			if(isset($lmm_options[ 'defaults_layer_listmarkers_show_distance' ] ) && $order_by != 'distance_current_position'){
				$layer_marker_list = $wpdb->get_results('SELECT '. $distance_query .' l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id.' '.$search_query.' ORDER BY '. $order_by .' ' . $order . ' LIMIT ' . $per_page ." OFFSET $offset", ARRAY_A);
			}else{
				$layer_marker_list = $wpdb->get_results('SELECT '. $distance_query .' l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id.' '.$search_query.' ORDER BY distance ' . $order . ' LIMIT ' . $per_page ." OFFSET $offset", ARRAY_A);
			}
		}else{
			if( isset($lmm_options[ 'defaults_layer_listmarkers_show_distance' ]) && $lmm_options[ 'defaults_layer_listmarkers_show_distance' ] == 1){
				$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
				$layer_marker_list = $wpdb->get_results('SELECT '. $distance_query .' l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id.' '.$search_query.' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT ' . $per_page ." OFFSET $offset", ARRAY_A);
			}else{
				$layer_marker_list = $wpdb->get_results('SELECT l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp, m.zoom as mzoom FROM `'.$table_name_layers.'` as l INNER JOIN `'.$table_name_markers.'` AS m ON m.layer LIKE concat(\'%"\',l.id,\'"%\') WHERE l.id='.$id.' '.$search_query.' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT ' . $per_page ." OFFSET $offset", ARRAY_A);
			}
		}
	} else if ($multi_layer_map == 1) {
			$distance_query = '';
			if( $order_by == 'distance_current_position' || $order_by == 'distance_layer_center' || isset($lmm_options[ 'defaults_layer_listmarkers_show_distance' ] )){
				if( $order_by == 'distance_current_position'){
					$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( ".$layerlat." ) ) * cos( radians( ".$layerlon." ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(".$layerlat.")) ) ) AS distance,";
				}else if($order_by == 'distance_layer_center' ){
					$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
				}else{
					$distance_query = " ( 6371 * acos( cos( radians(m.lat) ) * cos( radians( l.layerviewlat ) ) * cos( radians( l.layerviewlon ) - radians(m.lon) ) + sin( radians(m.lat) ) * sin(radians(l.layerviewlat)) ) ) AS distance,";
				}
			}
			//info: set sort order for multi-layer-maps based on list-marker-setting
			if ( $order_by == 'm.id') {
				$sort_order_mlm = 'markerid';
			} else if ( $order_by == 'm.markername') {
				$sort_order_mlm = 'markername';
			} else if ( $order_by == 'm.popuptext') {
				$sort_order_mlm = 'mpopuptext';
			} else if ( $order_by == 'm.icon') {
				$sort_order_mlm = 'micon';
			} else if ( $order_by == 'm.createdby') {
				$sort_order_mlm = 'mcreatedby';
			} else if ( $order_by == 'm.createdon') {
				$sort_order_mlm = 'mcreatedon';
			} else if ( $order_by == 'm.updatedby') {
				$sort_order_mlm = 'mupdatedby';
			} else if ( $order_by == 'm.updatedon') {
				$sort_order_mlm = 'mupdatedon';
			} else if ( $order_by == 'm.layer') {
				$sort_order_mlm = 'mlayer';
			} else if ( $order_by == 'm.address') {
				$sort_order_mlm = 'maddress';
			} else if ( $order_by == 'm.kml_timestamp') {
				$sort_order_mlm = 'mkml_timestamp';
			} else if ( $order_by == 'distance_current_position') {
				$sort_order_mlm = 'distance';
			} else if ( $order_by == 'distance_layer_center') {
				$sort_order_mlm = 'distance';
			}
			if ( (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') && ($multi_layer_map_list != NULL) ) { //info: only 1 layer selected
				$mlm_query = "SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') ". $search_query ." WHERE l.id='" . $multi_layer_map_list . "'  ORDER BY " . $sort_order_mlm . " " . $order . " LIMIT " . $per_page ." OFFSET $offset";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
			} //info: end (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') && ($multi_layer_map_list != NULL)
			else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all') ) {
				$first_mlm_id = $multi_layer_map_list_exploded[0];
				$other_mlm_ids = array_slice($multi_layer_map_list_exploded,1);
				$mlm_query = "(SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') ".$search_query." WHERE l.id='" . $first_mlm_id . "'  )";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') ".$search_query." WHERE l.id='" . $row . "' )";
				}
				$mlm_query .= " ORDER BY " . $sort_order_mlm . " " . $order . " LIMIT " . $per_page . " OFFSET $offset";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
			} //info: end else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all')
			else if ($multi_layer_map_list == 'all') {
				$first_mlm_id = '0';
				$mlm_all_layers = $wpdb->get_results( "SELECT id FROM $table_name_layers", ARRAY_A );
				$other_mlm_ids = array_slice($mlm_all_layers,1);
				$mlm_query = "(SELECT  ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') ".$search_query." WHERE l.id='" . $first_mlm_id . "' )";
				foreach ($other_mlm_ids as $row) {
					$mlm_query .= " UNION (SELECT ". $distance_query ." l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, l.address as laddress, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.address as maddress, m.layer as mlayer, m.kml_timestamp as mkml_timestamp FROM `" . $table_name_layers . "` as l INNER JOIN `" . $table_name_markers . "` AS m ON m.layer LIKE concat('%\"',l.id,'\"%') ".$search_query." WHERE l.id='" . $row['id'] . "'  )";
				}
				$mlm_query .=  " ORDER BY " . $sort_order_mlm . " " . $order . " LIMIT " . $per_page . " OFFSET $offset";
				$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
			} //info: end else if ($multi_layer_map_list == 'all')
			else { //info: if ($multi_layer_map == 1) but no layers selected
				$layer_marker_list = array();
			}
	} //info: end main - else if ($multi_layer_map == 1)
 	$ajax_results['mcount'] = $markercount;
 	$ajax_results['rows'] = '';
	if($lmm_options['defaults_layer_listmarkers_action_bar'] != 'hide'){
		$ajax_results['rows'] .= '<tr id="search_markers_row_'.$mapid.'">'.PHP_EOL;
		$ajax_results['rows'] .= '	<td colspan="2" class="lmm-search-markers-row">'.PHP_EOL;
		if($lmm_options['defaults_layer_listmarkers_action_bar'] != 'show-sort-order-selection-only'){
			$defaults_layer_listmarkers_searchtext = ($lmm_options['defaults_layer_listmarkers_searchtext'] == NULL) ? __('Search markers','lmm') : esc_attr(strip_tags($lmm_options['defaults_layer_listmarkers_searchtext']));
			$defaults_layer_listmarkers_searchtext_hover = ($lmm_options['defaults_layer_listmarkers_searchtext_hover'] == NULL) ? __('start typing to find marker entries based on markername or popuptext','lmm') : esc_attr(strip_tags( $lmm_options['defaults_layer_listmarkers_searchtext_hover']));
			$ajax_results['rows'] .= '<input id="search_markers_'.$mapid.'" class="lmm-search-markers" type="text" value="'.esc_attr($search_text).'" data-mapid="'.$mapid.'" placeholder="'.$defaults_layer_listmarkers_searchtext.'" title="'. $defaults_layer_listmarkers_searchtext_hover .'" />'.PHP_EOL;
		}
		if($lmm_options['defaults_layer_listmarkers_action_bar'] == 'show-sort-order-selection-only' || $lmm_options['defaults_layer_listmarkers_action_bar'] == 'show-full'){
			$order_class = ($order == 'asc')?'up':'down';
			$order_hover_text = ($order_class == 'up')?__('sort order ascending','lmm'):__('sort order descending','lmm');
			$order_value_hover_text = ($order_class == 'down')?__('ascending','lmm'):__('descending','lmm');
			$order_text = MMP_Globals::get_order_text($order_by);
			$ajax_results['rows'] .= '<div id="dropdown_'.$mapid.'" class="dropdown '.$order_class.'" title="' . esc_attr__('sort order','lmm') . '" data-sortby="'.$order_by.'">'.PHP_EOL;
			$ajax_results['rows'] .= '  <button class="dropbtn '. $order_class .'" title="'.$order_hover_text.'">'. $order_text .'</button>'.PHP_EOL;
			$ajax_results['rows'] .= '  <div class="dropdown-content" data-mapid="'.$mapid.'">'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_id']) && $lmm_options['defaults_layer_listmarkers_sort_id'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), 'ID', $order_value_hover_text) . '" data-sortby="m.id" class="lmm-sort-by ' . ($order_by == 'm.id'?$order_class:'') .'">ID</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_markername']) && $lmm_options['defaults_layer_listmarkers_sort_markername'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('marker name','lmm'), $order_value_hover_text) . '" data-sortby="m.markername" class="lmm-sort-by ' . ($order_by == 'm.markername'?$order_class:'') .'">'.__('marker name','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_popuptext']) && $lmm_options['defaults_layer_listmarkers_sort_popuptext'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('popuptext','lmm'), $order_value_hover_text) . '" data-sortby="m.popuptext" class="lmm-sort-by ' . ($order_by == 'm.popuptext'?$order_class:'') .'">'.__('popuptext','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_icon']) && $lmm_options['defaults_layer_listmarkers_sort_icon'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('icon','lmm'), $order_value_hover_text) . '" data-sortby="m.icon" class="lmm-sort-by ' . ($order_by == 'm.icon'?$order_class:'') .'">'.__('icon','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_created_by']) && $lmm_options['defaults_layer_listmarkers_sort_created_by'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('created by','lmm'), $order_value_hover_text) . '" data-sortby="m.createdby" class="lmm-sort-by ' . ($order_by == 'm.createdby'?$order_class:'') .'">'.__('created by','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_created_on']) && $lmm_options['defaults_layer_listmarkers_sort_created_on'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('created on','lmm'), $order_value_hover_text) . '" data-sortby="m.createdon" class="lmm-sort-by ' . ($order_by == 'm.createdon'?$order_class:'') .'">'.__('created on','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_updated_by']) && $lmm_options['defaults_layer_listmarkers_sort_updated_by'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('updated by','lmm'), $order_value_hover_text) . '" data-sortby="m.updatedby" class="lmm-sort-by ' . ($order_by == 'm.updatedby'?$order_class:'') .'">'.__('updated by','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_updated_on']) && $lmm_options['defaults_layer_listmarkers_sort_updated_on'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('updated on','lmm'), $order_value_hover_text) . '" data-sortby="m.updatedon" class="lmm-sort-by ' . ($order_by == 'm.updatedon'?$order_class:'') .'">'.__('updated on','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_layer_id']) && $lmm_options['defaults_layer_listmarkers_sort_layer_id'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('layer ID','lmm'), $order_value_hover_text) . '" data-sortby="m.layer" class="lmm-sort-by ' . ($order_by == 'm.layer'?$order_class:'') .'">'.__('layer ID','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_address']) && $lmm_options['defaults_layer_listmarkers_sort_address'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('address','lmm'), $order_value_hover_text) . '" data-sortby="m.address" class="lmm-sort-by ' . ($order_by == 'm.address'?$order_class:'') .'">'.__('address','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_kml_timestamp']) && $lmm_options['defaults_layer_listmarkers_sort_kml_timestamp'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('KML timestamp','lmm'), $order_value_hover_text) . '" data-sortby="m.kml_timestamp" class="lmm-sort-by ' . ($order_by == 'm.kml_timestamp'?$order_class:'') .'">'.__('KML timestamp','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_distance_layer_center']) && $lmm_options['defaults_layer_listmarkers_sort_distance_layer_center'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('distance from layer center','lmm'), $order_value_hover_text) . '" data-sortby="distance_layer_center" class="lmm-sort-by ' . ($order_by == 'distance_layer_center'?$order_class:'') .'">'.__('distance from layer center','lmm').'</a>'.PHP_EOL;
			if(isset($lmm_options['defaults_layer_listmarkers_sort_distance_current_pos']) && $lmm_options['defaults_layer_listmarkers_sort_distance_current_pos'] == 1)
				$ajax_results['rows'] .= '<a href="javascript:void(0);" title="' . sprintf(esc_attr__('click to sort list by %1$s %2$s','lmm'), __('distance from current position','lmm'), $order_value_hover_text) . '" data-sortby="distance_current_position"  class="lmm-sort-by ' . ($order_by == 'distance_current_position'?$order_class:'') .'">'.__('distance from current position','lmm').'</a>'.PHP_EOL;
			$ajax_results['rows'] .= '  </div>'.PHP_EOL;
			$ajax_results['rows'] .= '</div>'.PHP_EOL;
		}
		$ajax_results['rows'] .= '	</td>'.PHP_EOL;
		$ajax_results['rows'] .= '</tr>'.PHP_EOL;
	}
	//info: prepare WPML supported strings
	if ($ml_checked = MMP_Globals::check_multilingual()) {
		foreach ($layer_marker_list as $key => $row) {
			$layer_marker_list[$key]['mmarkername'] = MMP_Globals::translate_single_string($row['markername'], "Marker (ID {$row['markerid']}) name", $ml_checked);
			$layer_marker_list[$key]['maddress'] = MMP_Globals::translate_single_string($row['maddress'], "Marker (ID {$row['markerid']}) address", $ml_checked);
			$layer_marker_list[$key]['mpopuptext'] = MMP_Globals::translate_single_string($row['mpopuptext'], "Marker (ID {$row['markerid']}) popuptext", $ml_checked);
		}
	}
	foreach($layer_marker_list as $row){
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_icon' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_icon' ] == 1 ) ) {
			$ajax_results['rows'] .= '<tr id="marker_'.$row['markerid'].'"><td class="lmm-listmarkers-icon">';
			if ($lmm_options['defaults_layer_listmarkers_link_action'] != 'disabled') {
				$listmarkers_href_a = '<a href="javascript:void(0);" onclick="javascript:listmarkers_openpopup_' . $mapname . '(' . $row['markerid'] . ')">';
				$listmarkers_href_b = '</a>';
			} else {
				$listmarkers_href_a = '';
				$listmarkers_href_b = '';
			}
			if ($lmm_options['defaults_marker_popups_add_markername'] == 'true') {
				$markername_on_hover = 'title="' . stripslashes(htmlspecialchars($row['markername'])) . '"';
			} else {
				$markername_on_hover = '';
			}
			if ($row['micon'] != null) {
				$ajax_results['rows'] .= $listmarkers_href_a . '<img style="border-radius:0;box-shadow:none;" width="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . '" height="' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '" alt="marker icon" src="' . $defaults_marker_icon_url . '/'.$row['micon'].'" ' . $markername_on_hover . ' />' . $listmarkers_href_b;
			} else {
				$ajax_results['rows'] .= $listmarkers_href_a . '<img style="border-radius:0;box-shadow:none;" alt="marker icon" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" ' . $markername_on_hover . ' />' . $listmarkers_href_b;
			};
		} else {
			$ajax_results['rows'] .= '<tr><td>';
		};
		$ajax_results['rows'] .= '</td><td class="lmm-listmarkers-popuptext"><div class="lmm-listmarkers-panel-icons">';

		$edit_link = (current_user_can( $lmm_options[ 'capabilities_edit_others' ]))?'<a title="' . esc_attr__('Edit marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'"><img class="lmm-panel-api-images" style="margin-right:3px !important;" src="' . LEAFLET_PLUGIN_URL . '/inc/img/icon-map-edit.png" width="16" height="16" alt="' . esc_attr__('Edit marker','lmm') . ' ID ' . $row['markerid'] . '"></a>':'';
		$ajax_results['rows'] .= $edit_link;

		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_directions' ] == 1 ) ) {
			if ($lmm_options['directions_provider'] == 'googlemaps') {
				if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = htmlspecialchars($lmm_options['google_maps_base_domain_custom']); }
				if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $directions_transport_type_icon = 'icon-walk.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
				if ( $row['maddress'] != NULL ) { $google_from = urlencode($row['maddress']); } else { $google_from = $row['mlat'] . ',' . $row['mlon']; }
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
					$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
				}
				$ajax_results['rows'] .= '<a href="https://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&amp;t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&amp;layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&amp;doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&amp;om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img alt="' . esc_attr__('Get directions','lmm') . '" src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'yours') {
				if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $directions_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $directions_transport_type_icon = 'icon-walk.png'; }
				$ajax_results['rows'] .= '<a href="http://www.yournavigation.org/?tlat=' . $row['mlat'] . '&amp;tlon=' . $row['mlon'] . '&amp;v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&amp;fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&amp;layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
			} else if ($lmm_options['directions_provider'] == 'ors') {
				if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Pedestrian') { $directions_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_routeOpt' ] == 'Bicycle') { $directions_transport_type_icon = 'icon-bicycle.png'; } else { $directions_transport_type_icon = 'icon-car.png'; }
				$ajax_results['rows'] .= '<a href="http://openrouteservice.org/?pos=' . $row['mlon'] . ',' . $row['mlat'] . '&amp;wp=' . $row['mlon'] . ',' . $row['mlat'] . '&amp;zoom=' . $row['mzoom'] . '&amp;routeOpt=' . $lmm_options[ 'directions_ors_routeOpt' ] . '&amp;layer=' . $lmm_options[ 'directions_ors_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $directions_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
			} else if ($lmm_options['directions_provider'] == 'bingmaps') {
				if ( $row['maddress'] != NULL ) { $bing_to = '_' . urlencode($row['maddress']); } else { $bing_to = ''; }
				$ajax_results['rows'] .= '<a href="https://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $row['mlat'] . '_' . $row['mlon'] . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" alt="' . esc_attr__('Get directions','lmm') . '" /></a>';
			}
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] == 1 ) ) {
			$ajax_results['rows'] .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/fullscreen/marker/' . $row['markerid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_kml' ] == 1 ) ) {
			$ajax_results['rows'] .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/kml/marker/' . $row['markerid'] . '/?markername=' . $lmm_options[ 'misc_kml' ]) . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] == 1 ) ) {
			$ajax_results['rows'] .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/qr/marker/' . $row['markerid'] . '/') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" rel="nofollow"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] == 1 ) ) {
			$ajax_results['rows'] .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/geojson/marker/' . $row['markerid'] . '?callback=jsonp&amp;full=yes&amp;full_icon_url=yes') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="' . esc_attr__('Export as GeoJSON','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_georss' ] == 1 ) ) {
			$ajax_results['rows'] .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/georss/marker/' . $row['markerid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="' . esc_attr__('Export as GeoRSS','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] == 1 ) ) {
			$ajax_results['rows'] .= '&nbsp;<a href="' . MMP_Globals::translate_permalink($lmm_base_url . $lmm_slug . '/wikitude/marker/' . $row['markerid'] . '/') . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_distance' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_show_distance' ] == 1 ) && ($order_by == 'distance_current_position' || $order_by == 'distance_layer_center')) {
			if ($lmm_options['defaults_layer_listmarkers_show_distance_unit'] == 'km') {
				$ajax_results['rows'] .= '<br/><br/><span class="lmm-distance" title="' . esc_attr__('calculated from map center','lmm') . '">' . __('distance', 'lmm').': ' . round($row['distance'], intval($lmm_options[ 'defaults_layer_listmarkers_show_distance_precision' ])) . ' ' . __('km','lmm') . '</span>';
			} else if($lmm_options['defaults_layer_listmarkers_show_distance_unit'] == 'mile') {
				$ajax_results['rows'] .= '<br/><br/><span class="lmm-distance" title="' . esc_attr__('calculated from map center','lmm') . '">' . __('distance', 'lmm').': ' . round($row['distance'], intval($lmm_options[ 'defaults_layer_listmarkers_show_distance_precision' ])) . ' ' . __('miles','lmm') . '</span>';
			}
		}
		$ajax_results['rows'] .= '</div>';
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_markername' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_markername' ] == 1 ) ) {
			if ($lmm_options['defaults_layer_listmarkers_link_action'] != 'disabled') {
				$ajax_results['rows'] .= '<span class="lmm-listmarkers-markername"><a title="' . esc_attr__('show marker on map','lmm') . '" href="javascript:void(0);" onclick="javascript:listmarkers_openpopup_' . $mapname . '(' . $row['markerid'] . ')">' . stripslashes(htmlspecialchars($row['markername'])) . '</a></span> ';
			} else {
				$ajax_results['rows'] .= '<span class="lmm-listmarkers-markername">' . stripslashes(htmlspecialchars($row['markername'])) . '</span> ';
			}
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ] == 1 ) ) {
			$popuptext_sanitized = MMP_Globals::sanitize_popuptext($row['mpopuptext']);
			$ajax_results['rows'] .= '<br/><span class="lmm-listmarkers-popuptext-only">' . do_shortcode($popuptext_sanitized) . '</span>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_address' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_address' ] == 1 ) ) {
			if ( $row['mpopuptext'] == NULL ) {
				$ajax_results['rows'] .= stripslashes(htmlspecialchars($row['maddress']));
			} else if ( ($row['mpopuptext'] != NULL) && ($row['maddress'] != NULL) ) {
				$ajax_results['rows'] .= '<div class="lmm-listmarkers-hr">' . stripslashes(htmlspecialchars($row['maddress'])) . '</div>';
			}
		}
		$ajax_results['rows'] .= '</td></tr>';
	} //info: end foreach
	//info:  get pagination
	$pager = '<input type="hidden" id="'.$mapid.'_orderby" name="orderby" value="' . $order_by . '" />';
	$pager .= '<input type="hidden" id="'.$mapid.'_order" name="order" value="' . $order . '" />';
	$pager .= '<input type="hidden" id="'.$mapid.'_multi_layer_map" name="multi_layer_map" value="' . $multi_layer_map . '" />';
	$pager .= '<input type="hidden" id="'.$mapid.'_multi_layer_map_list" name="multi_layer_map_list" value="' . $multi_layer_map_list . '" />';
	$pager .= '<input type="hidden" id="'.$mapid.'_markercount" name="markercount" value="' . $markercount . '" />';
	$pager .= '<input type="hidden" id="'.$mapid.'_id" name="id" value="' . $id . '" />';
	$pager .= '<span class="markercount_'.$mapid.'">'. $markercount.'</span> '.__('markers','lmm');
	$radius = 1;
	$getorder = isset($_POST['order']) ? htmlspecialchars($_POST['order']) : $lmm_options[ 'misc_marker_listing_sort_sort_order' ];
	$getorderby = isset($_POST['orderby']) ? '&orderby=' . htmlspecialchars($_POST['orderby']) : '';
	if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
	if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
	if ($markercount >  $per_page ) {
	  $maxpage = intval(ceil($markercount / $per_page));
	  if ($maxpage > 1) {
		$pager .= '<div class="lmm-per-page">';
		$pager .= '<input type="text" id="markers_per_page_'.$mapid.'" class="lmm-per-page-input" value="'.$per_page.'" data-mapid="'.$mapid.'" />';
		$pager .= ' '.__('per page','lmm');
		$pager .= '</div>';
	   	$pager .= '<div class="lmm-pages">';
		$pager .= '<form style="display:inline;" method="POST" action="">';
	    $pager .= ' '.__('page','lmm').' ';
	    if ($pagenum > (2 + $radius * 2)) {
	      foreach (range(1, 1 + $radius) as $num)
	        $pager .= '<a href="#" class="first-page" data-mapid="'.$mapid.'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
	        $pager .= '<a href="#" class="first-page" data-mapid="'.$mapid.'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum > 1)
	        foreach (range(1, $pagenum - 1) as $num)
	          $pager .= '<a href="#" class="first-page" data-mapid="'.$mapid.'">'.$num.'</a>';
	    $pager .= '<a href="#" class="first-page current-page" data-mapid="'.$mapid.'">' . $pagenum . '</a>';
	    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
	      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
	        $pager .= '<a href="#" class="first-page" data-mapid="'.$mapid.'">'.$num.'</a>';
	      $pager .= '...';
	      foreach (range($maxpage - $radius, $maxpage) as $num)
	        $pager .= '<a href="#" class="first-page" data-mapid="'.$mapid.'">'.$num.'</a>';
	    }
	    else
	      if ($pagenum < $maxpage)
	        foreach (range($pagenum + 1, $maxpage) as $num)
	          $pager .= '<a href="#" class="first-page" data-mapid="'.$mapid.'">'.$num.'</a>';
	  }
	  $ajax_results['no_pagination'] = false;
	}else{
		$ajax_results['no_pagination'] = true;
	}
	$pager .= '</form></div>';
	$ajax_results['pager'] = $pager;
	echo $left_delimiter . json_encode($ajax_results) . $right_delimiter;
	die();
/* } else if ($ajax_subaction == '.....') {

	die();

*******************************************/
}
die();
