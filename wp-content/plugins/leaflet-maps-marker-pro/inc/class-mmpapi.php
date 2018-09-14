<?php
/**
 * Maps Marker Plugin - MMPAPI
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'class-mmpapi.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

class MMPAPI {
	/**
    * Returns all marker objects
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @return mixed The marker objects or false
    */
	public static function list_markers( $args = NULL ){
		global $wpdb;
		if (isset($args)) {
			//info:  to make sure that the args is an array
			if(!is_array($args)){
				 return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: list_markers method only accepts arrays. Input was %s', 'lmm' ), gettype($args)), $args );
			}

			$allowed_keys = self::get_allowed_marker_orderkeys();
			if(isset($args['orderkey']) && !in_array($args['orderkey'], $allowed_keys )){
				 return new WP_Error( 'invalid_parameter', sprintf(__( 'MMPAPI error: orderkey is not valid, it must be one of the following keys: %s', 'lmm' ), implode(', ',$allowed_keys)), $args );
			}
			extract($args);
		}
		$orderkey = isset($orderkey) ? $orderkey : 'id';
		$orderdir = ( isset($orderdir) && ( $orderdir == 'ASC' || $orderdir == 'DESC' ) ) ? $orderdir : 'ASC';
		$orderoff = isset($orderoff) ? abs(intval($orderoff)) : 0;
		$orderlimit = isset($orderlimit) ? abs(intval($orderlimit)) : 999999;
		//info:  query the database
		$marker_list = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `".self::table_name_markers()."` ORDER BY `".$orderkey."` ".$orderdir." LIMIT %d, %d", $orderoff, $orderlimit));
		//info:  to make sure that markers exist in the database
		if($marker_list === null) return false;
		return $marker_list;
	}

	/**
    * Returns the marker object for a given Marker ID
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param int $marker_id The ID of the Marker
    *
    * @return mixed The marker object or false
    */
	public static function get_marker( $marker_id ){
		global $wpdb;
		//info:  to make sure that the marker_id is a number
		$marker_id = intval( $marker_id );
		if($marker_id === 0) return false;
		//info:  query the database
		$marker_result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `".self::table_name_markers()."` WHERE `id` = %d", $marker_id));
		//info:  to make sure that the marker_id exists in the database
		if($marker_result === null) return false;
		return $marker_result;
	}

	/**
    * Returns the marker objects for given Marker IDs
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param int $marker_ids The IDs of the Markers
    *
    * @return mixed The marker objects or false
    */
	public static function get_markers( $marker_ids ){
		global $wpdb;
		//info:  to make sure that the marker_ids is an array
		if(!is_array($marker_ids)) return false;
		//info:  query the database
		$marker_ids = implode(',', array_map('intval', $marker_ids));
		$markers_result = $wpdb->get_results( "SELECT * FROM `".self::table_name_markers()."` WHERE `id` IN (".$marker_ids .")");
		//info:  to make sure that the marker_ids exists in the database
		if($markers_result === null) return false;
		return $markers_result;
	}

	/**
    * Add a marker based on given data array
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $marker_data Data of the marker
    *
    * @return mixed int|WP_Error
    */
	public static function add_marker( $marker_data ){
		require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'class-mmp-geocoding.php' );
		if(!is_array($marker_data)){
			return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: add_marker() method only accepts arrays. Input was %s', 'lmm' ), gettype($marker_data)), $marker_data );
		}
		global $wpdb;
		$lmm_options = self::lmm_options();
		$markername = isset($marker_data['markername']) ? $marker_data['markername'] :  '';

		$markername_quotes = str_replace("\\\\","/", str_replace("\"", "'", sanitize_text_field($markername)));
		$mpopuptext = isset($marker_data['popuptext']) ? str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker_data['popuptext'])) : '';

		$basemap = isset($marker_data['basemap']) && in_array($marker_data['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $marker_data['basemap'] :  $lmm_options[ 'standard_basemap' ];
		$layer = isset($marker_data['layer']) ? $marker_data['layer'] : (($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '0' : $lmm_options[ 'defaults_marker_default_layer' ]);
		//info:  convert the layer id to json and add the option to assign multiple layers.
		$layer =  array_map('intval', explode (',', $layer));
		$layer = json_encode( array_map('strval', $layer ) );

		$lat = isset($marker_data['lat']) ? floatval($marker_data['lat']) :  str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lat' ]));
		$lon = isset($marker_data['lon']) ? floatval($marker_data['lon']) :  str_replace(',', '.', floatval($lmm_options[ 'defaults_marker_lon' ]));
		$icon = isset($marker_data['icon']) ? $marker_data['icon']  : (($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : esc_html($lmm_options[ 'defaults_marker_icon' ]));
		$popuptext = $mpopuptext;
		$zoom = isset($marker_data['zoom']) ? intval($marker_data['zoom'])  : intval($lmm_options[ 'defaults_marker_zoom' ]);
		$openpopup = ( isset($marker_data['openpopup']) && ( ($marker_data['openpopup'] == '0') || ($marker_data['openpopup'] == '1')) ) ? $marker_data['openpopup']  : $lmm_options[ 'defaults_marker_openpopup' ];
		$mapwidth = isset($marker_data['mapwidth']) ? $marker_data['mapwidth'] : intval($lmm_options[ 'defaults_marker_mapwidth' ]);
		$mapwidthunit = ( isset($marker_data['mapwidthunit']) && ( ($marker_data['mapwidthunit'] == 'px') || ($marker_data['mapwidthunit'] == '%') ) ) ? $marker_data['mapwidthunit']  : $lmm_options[ 'defaults_marker_mapwidthunit' ];
		$mapheight = isset($marker_data['mapheight']) ? $marker_data['mapheight'] : intval($lmm_options[ 'defaults_marker_mapheight' ]);
		$panel = ( isset($marker_data['panel']) && ( ($marker_data['panel'] == '0') || ($marker_data['panel'] == '1')) ) ? $marker_data['panel'] : $lmm_options[ 'defaults_marker_panel' ];
		$createdby = isset($marker_data['createdby']) ? esc_html($marker_data['createdby']): 'MapsMarker API';
		$createdon = isset($marker_data['createdon']) && ( $marker_data['createdon'] == date('Y-m-d H:i:s',strtotime($marker_data['createdon'])) ) ? $marker_data['createdon'] : current_time('mysql',0);
		$updatedby = isset($marker_data['updatedby']) ? esc_html($marker_data['updatedby']) : 'MapsMarker API';
		$updatedon = isset($marker_data['updatedon']) && ( $marker_data['createdon'] == date('Y-m-d H:i:s',strtotime($marker_data['createdon'])) ) ? $marker_data['updatedon'] : current_time('mysql',0);
		$controlbox = ( isset($marker_data['controlbox']) && ( ($marker_data['controlbox'] == '0') || ($marker_data['controlbox'] == '1') || ($marker_data['controlbox'] == '2')) ) ? $marker_data['controlbox'] : $lmm_options[ 'defaults_marker_controlbox' ];
		$overlays_custom = ( isset($marker_data['overlays_custom']) && ( ($marker_data['overlays_custom'] == '0') || ($marker_data['overlays_custom'] == '1')) ) ? $marker_data['overlays_custom'] : ( isset($lmm_options[ 'defaults_layer_overlays_custom_active' ]) && $lmm_options[ 'defaults_layer_overlays_custom_active' ] != null) ? '1' : '0';
		$overlays_custom2 = ( isset($marker_data['overlays_custom2']) && ( ($marker_data['overlays_custom2'] == '0') || ($marker_data['overlays_custom2'] == '1')) ) ? $marker_data['overlays_custom2'] : ( isset($lmm_options[ 'defaults_layer_overlays_custom2_active' ]) && $lmm_options[ 'defaults_layer_overlays_custom2_active' ] != null) ? '1' : '0';
		$overlays_custom3 = ( isset($marker_data['overlays_custom3']) && ( ($marker_data['overlays_custom3'] == '0') || ($marker_data['overlays_custom3'] == '1')) ) ? $marker_data['overlays_custom3'] : ( isset($lmm_options[ 'defaults_layer_overlays_custom3_active' ]) && $lmm_options[ 'defaults_layer_overlays_custom3_active' ] != null) ? '1' : '0';
		$overlays_custom4 = ( isset($marker_data['overlays_custom4']) && ( ($marker_data['overlays_custom4'] == '0') || ($marker_data['overlays_custom4'] == '1')) ) ? $marker_data['overlays_custom4'] : ( isset($lmm_options[ 'defaults_layer_overlays_custom4_active' ]) && $lmm_options[ 'defaults_layer_overlays_custom4_active' ] != null) ? '1' : '0';
		$wms = ( isset($marker_data['wms']) && ( ($marker_data['wms'] == '0') || ($marker_data['wms'] == '1')) ) ? $marker_data['wms'] : (  isset($lmm_options['defaults_layer_wms_active']) && $lmm_options[ 'defaults_layer_wms_active'] !==0 ) ? '1' : '0';
		$wms2 = ( isset($marker_data['wms2']) && ( ($marker_data['wms2'] == '0') || ($marker_data['wms2'] == '1')) ) ? $marker_data['wms2'] : ( isset($lmm_options['defaults_layer_wms2_active']) && $lmm_options[ 'defaults_layer_wms2_active'] !=null) ? '1' : '0';
		$wms3 = ( isset($marker_data['wms3']) && ( ($marker_data['wms3'] == '0') || ($marker_data['wms3'] == '1')) ) ? $marker_data['wms3'] : ( isset($lmm_options['defaults_layer_wms3_active']) && $lmm_options[ 'defaults_layer_wms3_active'] !=null ) ? '1' : '0';
		$wms4 = ( isset($marker_data['wms4']) && ( ($marker_data['wms4'] == '0') || ($marker_data['wms4'] == '1')) ) ? $marker_data['wms4'] : ( isset($lmm_options['defaults_layer_wms4_active']) && $lmm_options[ 'defaults_layer_wms4_active'] !=null ) ? '1' : '0';
		$wms5 = ( isset($marker_data['wms5']) && ( ($marker_data['wms5'] == '0') || ($marker_data['wms5'] == '1')) ) ? $marker_data['wms5'] : ( isset($lmm_options['defaults_layer_wms5_active']) && $lmm_options[ 'defaults_layer_wms5_active'] !=null ) ? '1' : '0';
		$wms6 = ( isset($marker_data['wms6']) && ( ($marker_data['wms6'] == '0') || ($marker_data['wms6'] == '1')) ) ? $marker_data['wms6'] : ( isset($lmm_options['defaults_layer_wms6_active']) && $lmm_options[ 'defaults_layer_wms6_active'] !=null ) ? '1' : '0';
		$wms7 = ( isset($marker_data['wms7']) && ( ($marker_data['wms7'] == '0') || ($marker_data['wms7'] == '1')) ) ? $marker_data['wms7'] : ( isset($lmm_options['defaults_layer_wms7_active']) && $lmm_options[ 'defaults_layer_wms7_active'] !=null ) ? '1' : '0';
		$wms8 = ( isset($marker_data['wms8']) && ( ($marker_data['wms8'] == '0') || ($marker_data['wms8'] == '1')) ) ? $marker_data['wms8'] : ( isset($lmm_options['defaults_layer_wms8_active']) && $lmm_options[ 'defaults_layer_wms8_active'] !=null ) ? '1' : '0';
		$wms9 = ( isset($marker_data['wms9']) && ( ($marker_data['wms9'] == '0') || ($marker_data['wms9'] == '1')) ) ? $marker_data['wms9'] : ( isset($lmm_options['defaults_layer_wms9_active']) && $lmm_options[ 'defaults_layer_wms9_active'] !=null ) ? '1' : '0';
		$wms10 = ( isset($marker_data['wms10']) && ( ($marker_data['wms10'] == '0') || ($marker_data['wms10'] == '1')) ) ? $marker_data['wms10'] : (isset($lmm_options['defaults_layer_wms10_active']) && $lmm_options[ 'defaults_layer_wms10_active'] !==0 ) ? '1' : '0';

		$kml_timestamp = isset($marker_data['kml_timestamp']) && ( $marker_data['kml_timestamp'] == date('Y-m-d H:i:s',strtotime($marker_data['kml_timestamp'])) ) ? $marker_data['kml_timestamp'] : '';
		$address = isset($marker_data['address']) ? sanitize_text_field($marker_data['address']) : '';
		$gpx_url = isset($marker_data['gpx_url']) ? esc_url_raw($marker_data['gpx_url']) : '';
		$gpx_panel = ( isset($marker_data['gpx_panel']) && ( ($marker_data['gpx_panel'] == '0') || ($marker_data['gpx_panel'] == '1')) ) ? $marker_data['gpx_panel'] : '0';
		$geocode = isset($marker_data['geocode'])? $marker_data['geocode']: '';
		if ($geocode != '') {
				$do_geocoding = MMP_Geocoding::getLatLng($geocode);
			if ($do_geocoding['success'] == true) {
				$lat = $do_geocoding['lat'];
				$lon = $do_geocoding['lon'];
				$address = $do_geocoding['address'];
			}else{
				return new WP_Error( 'geocode_failed', sprintf( __( 'MMPAPI error: geocoding error: %1s', 'lmm' ), $do_geocoding['message'] ), $marker_data );
			}
		}
		if ($kml_timestamp == NULL) {
			$query_add = $wpdb->prepare( "INSERT INTO `".self::table_name_markers()."` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel );
		} else {
			$query_add = $wpdb->prepare( "INSERT INTO `".self::table_name_markers()."` (`markername`, `basemap`, `layer`, `lat`, `lon`, `icon`, `popuptext`, `zoom`, `openpopup`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `kml_timestamp`, `address`, `gpx_url`, `gpx_panel`) VALUES (%s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel );
		}
		$result_add = $wpdb->query( $query_add );
		return ($result_add === 1)?$wpdb->insert_id:FALSE;
	}

	/**
    * Add markers based on given data object
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $marker_data Data of the marker
    *
    * @return array|WP_Error IDs of added markers or an error object.
    */
	public static function add_markers( $markers_data ){

		if(! $markers_data || !is_array($markers_data)){
			return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: invalid marker objects. Input was %s', 'lmm' ), gettype($markers_data)), $markers_data );
		}
        $marker_ids = array();
        foreach ( $markers_data as $marker ) {
          $result = self::add_marker( $marker );
          if ( is_wp_error( $result ) ) {
              return $result;
          }
          $marker_ids[] = $result;
        }
        return $marker_ids;
	}

	/**
    * Update a marker based on given data object
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $marker_data Data of the marker
    *
    * @return boolean
    */
	public static function update_marker( $marker_data ){
			require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'class-mmp-geocoding.php' );
			$lmm_options = self::lmm_options();
			global $wpdb;
			$id = isset($marker_data['id']) ? $marker_data['id'] : '';
			$query_view = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `". self::table_name_markers() ."` WHERE `id` = %d", $id), ARRAY_A);
			if (count($query_view) >= 1) {
				$mpopuptext = stripslashes(str_replace('"', '\'', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$query_view['popuptext'])));
				$address = stripslashes(str_replace('"', '\'', $query_view['address']));
				$markername = isset($marker_data['markername']) ? $marker_data['markername']  : $query_view['markername'];
				$markername_quotes = str_replace("\\\\","/", str_replace("\"", "'", sanitize_text_field($markername))); //info: backslash breaks GeoJSON
				$basemap = isset($marker_data['basemap']) && in_array($marker_data['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $marker_data['basemap'] : $query_view['basemap'];

				$layer = isset($marker_data['layer']) ? $marker_data['layer'] : $query_view['layer'];
				if($layer !== $query_view['layer']){
					//info:  convert the layer id to json and add the option to assign multiple layers.
					$layer =  array_map('intval', explode (',', $layer));
					$layer = json_encode( array_map('strval', $layer ) );
				}
				$lat = isset($marker_data['lat']) ? floatval($marker_data['lat']): $query_view['lat'];
				$lon = isset($marker_data['lon']) ? floatval($marker_data['lon']): $query_view['lon'];
				$icon = isset($marker_data['icon']) ? esc_html($marker_data['icon'])  : (($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : $query_view['icon']);
				$popuptext = isset($marker_data['popuptext']) ? $marker_data['popuptext'] : $mpopuptext;
				$zoom = isset($marker_data['zoom']) ? intval($marker_data['zoom']) : $query_view['zoom'];
				$openpopup = ( isset($marker_data['openpopup']) && ( ($marker_data['openpopup'] == '0') || ($marker_data['openpopup'] == '1')) ) ? $marker_data['openpopup'] : $query_view['openpopup'];
				$mapwidth = isset($marker_data['mapwidth']) ? $marker_data['mapwidth'] :  $query_view['mapwidth'];
				$mapwidthunit = ( isset($marker_data['mapwidthunit']) && ( ($marker_data['mapwidthunit'] == 'px') || ($marker_data['mapwidthunit'] == '%') ) ) ? $marker_data['mapwidthunit'] : $query_view['mapwidthunit'];
				$mapheight = isset($marker_data['mapheight']) ? $marker_data['mapheight'] : $query_view['mapheight'];
				$panel = ( isset($marker_data['panel']) && ( ($marker_data['panel'] == '0') || ($marker_data['panel'] == '1')) ) ? $marker_data['panel'] :  $query_view['panel'];
				$createdby = isset($marker_data['createdby']) ? esc_html($marker_data['createdby']) : esc_html($query_view['createdby']);
				$createdon = isset($marker_data['createdon']) && ( $marker_data['createdon'] == date('Y-m-d H:i:s',strtotime($marker_data['createdon'])) ) ? $marker_data['createdon'] : $query_view['createdon'];
				$updatedby = isset($marker_data['updatedby']) ? esc_html($marker_data['updatedby']) : esc_html($query_view['updatedby']);
				$updatedon = isset($marker_data['updatedon']) && ( isset($marker_data['createdon']) && $marker_data['createdon'] == date('Y-m-d H:i:s',strtotime($marker_data['createdon'])) ) ? $marker_data['updatedon'] : current_time('mysql',0);
				$controlbox = ( isset($marker_data['controlbox']) && ( ($marker_data['controlbox'] == '0') || ($marker_data['controlbox'] == '1') || ($marker_data['controlbox'] == '2')) ) ? $marker_data['controlbox'] : $query_view['controlbox'];
				$overlays_custom = ( isset($marker_data['overlays_custom']) && ( ($marker_data['overlays_custom'] == '0') || ($marker_data['overlays_custom'] == '1')) ) ? $marker_data['overlays_custom'] : $query_view['overlays_custom'];
				$overlays_custom2 = ( isset($marker_data['overlays_custom2']) && ( ($marker_data['overlays_custom2'] == '0') || ($marker_data['overlays_custom2'] == '1')) ) ? $marker_data['overlays_custom2'] : $query_view['overlays_custom2'];
				$overlays_custom3 = ( isset($marker_data['overlays_custom3']) && ( ($marker_data['overlays_custom3'] == '0') || ($marker_data['overlays_custom3'] == '1')) ) ? $marker_data['overlays_custom3'] : $query_view['overlays_custom3'];
				$overlays_custom4 = ( isset($marker_data['overlays_custom4']) && ( ($marker_data['overlays_custom4'] == '0') || ($marker_data['overlays_custom4'] == '1')) ) ? $marker_data['overlays_custom4'] : $query_view['overlays_custom4'];
				$wms = ( isset($marker_data['wms']) && ( ($marker_data['wms'] == '0') || ($marker_data['wms'] == '1')) ) ? $marker_data['wms'] : $query_view['wms'];
				$wms2 = ( isset($marker_data['wms2']) && ( ($marker_data['wms2'] == '0') || ($marker_data['wms2'] == '1')) ) ? $marker_data['wms2'] : $query_view['wms2'];
				$wms3 = ( isset($marker_data['wms3']) && ( ($marker_data['wms3'] == '0') || ($marker_data['wms3'] == '1')) ) ? $marker_data['wms3'] : $query_view['wms3'];
				$wms4 = ( isset($marker_data['wms4']) && ( ($marker_data['wms4'] == '0') || ($marker_data['wms4'] == '1')) ) ? $marker_data['wms4'] : $query_view['wms4'];
				$wms5 = ( isset($marker_data['wms5']) && ( ($marker_data['wms5'] == '0') || ($marker_data['wms5'] == '1')) ) ? $marker_data['wms5'] : $query_view['wms5'];
				$wms6 = ( isset($marker_data['wms6']) && ( ($marker_data['wms6'] == '0') || ($marker_data['wms6'] == '1')) ) ? $marker_data['wms6'] : $query_view['wms6'];
				$wms7 = ( isset($marker_data['wms7']) && ( ($marker_data['wms7'] == '0') || ($marker_data['wms7'] == '1')) ) ? $marker_data['wms7'] : $query_view['wms7'];
				$wms8 = ( isset($marker_data['wms8']) && ( ($marker_data['wms8'] == '0') || ($marker_data['wms8'] == '1')) ) ? $marker_data['wms8'] : $query_view['wms8'];
				$wms9 = ( isset($marker_data['wms9']) && ( ($marker_data['wms9'] == '0') || ($marker_data['wms9'] == '1')) ) ? $marker_data['wms9'] : $query_view['wms9'];
				$wms10 = ( isset($marker_data['wms10']) && ( ($marker_data['wms10'] == '0') || ($marker_data['wms10'] == '1')) ) ? $marker_data['wms10'] : $query_view['wms10'];
				$kml_timestamp = isset($marker_data['kml_timestamp']) && ( $marker_data['kml_timestamp'] == date('Y-m-d H:i:s',strtotime($marker_data['kml_timestamp'])) ) ? $marker_data['kml_timestamp'] : $query_view['kml_timestamp'];
				$address = isset($marker_data['address']) ? sanitize_text_field($marker_data['address']) : $address;
				$gpx_url = isset($marker_data['gpx_url']) ? esc_url_raw($marker_data['gpx_url']) : esc_url_raw($query_view['gpx_url']);
				$gpx_panel = ( isset($marker_data['gpx_panel']) && ( ($marker_data['gpx_panel'] == '0') || ($marker_data['gpx_panel'] == '1')) ) ? $marker_data['gpx_panel'] : $query_view['gpx_panel'];
				$geocode = isset($marker_data['geocode'])? $marker_data['geocode']: '';
				if ($geocode != NULL) {
					$do_geocoding = MMP_Geocoding::getLatLng($geocode);

					if ($do_geocoding['success'] == true) {
						$lat = $do_geocoding['lat'];
						$lon = $do_geocoding['lon'];
						$address = $do_geocoding['address'];
					} else {
						return new WP_Error( 'geocode_failed', sprintf( __( 'MMPAPI error: geocoding error: %1s', 'lmm' ), $do_geocoding['message'] ), $marker_data );
					}
				}
				if ($kml_timestamp == NULL) {
					$query_update = $wpdb->prepare( "UPDATE `". self::table_name_markers() ."` SET `markername` = %s, `basemap` = %s, `layer` = %s, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel, $id );
				} else {
					$query_update = $wpdb->prepare( "UPDATE `". self::table_name_markers() ."` SET `markername` = %s, `basemap` = %s, `layer` = %s, `lat` = %s, `lon` = %s, `icon` = %s, `popuptext` = %s, `zoom` = %d, `openpopup` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %s, `overlays_custom2` = %s, `overlays_custom3` = %s, `overlays_custom4` = %s, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `kml_timestamp` = %s, `address` = %s, `gpx_url` = %s, `gpx_panel` = %d WHERE `id` = %d", $markername_quotes, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel, $id );
				}
				$result_update = $wpdb->query( $query_update );
				return ($result_update === 1)?TRUE:FALSE;
			}else{
				return new WP_Error( 'not_found', sprintf( __( 'MMPAPI error: marker ID %1s was not found', 'lmm' ), $id ), $marker_data );
			}
	}

	/**
    * Update markers based on given data object
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $marker_data Data of the marker
    *
    * @return array|WP_Error IDs of updated markers or an error object.
    */
	public static function update_markers(  $markers_data  ){
		if(! $markers_data || !is_array($markers_data)){
			return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: invalid marker objects. Input was %s', 'lmm' ), gettype($markers_data)), $markers_data );
		}
        $marker_ids = array();
        foreach ( $markers_data as $marker ) {
          $result = self::update_marker( $marker );
          if ( is_wp_error( $result ) ) {
              return $result;
          }
          $marker_ids[$marker['id']] = $result;
        }
        return $marker_ids;
	}

	/**
    * Deletes the marker for a given Marker ID
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param int $marker_id The ID of the Marker
    *
    * @return mixed TRUE if the marker deleted successfully, WP_Error object if not
    */
	public static function delete_marker( $marker_id ){
		global $wpdb;
		//info:  to make sure that the marker_id is a number
		$marker_id = intval( $marker_id );
		if($marker_id === 0) return new WP_Error( 'invalid_parameter',  __( 'MMPAPI error: delete_marker method only accepts integers', 'lmm' ), $marker_id );
		//info:  query the database
		$marker_result = $wpdb->query( $wpdb->prepare("DELETE  FROM `".self::table_name_markers()."` WHERE `id` = %d", $marker_id));
		//info:  it will return false if the number of rows affected is zero, and it will return true otherwise.
		if($marker_result === 0){
			return new WP_Error( 'not_found', sprintf( __( 'MMPAPI error: marker with ID %s not found', 'lmm' ), $marker_id ), $marker_id );
		}else{
			return true;
		}

	}

	/**
    * Deletes the markers for given Markers IDs
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $marker_ids The IDs of the Markers
    *
    * @return boolean true if the markers deleted successfully, WP_Error object if not
    */
	public static function delete_markers( $marker_ids = array() ){
		global $wpdb;
		//info:  to make sure that the marker_id is an array
		if(!is_array($marker_ids)){
			 return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: delete_markers method only accepts arrays. Input was %s', 'lmm' ), gettype($marker_ids)), $marker_ids );
		}
		//info:  to make sure that the marker_ids is not empty
		if(empty($marker_ids)) return new WP_Error( 'empty_parameter', __( 'MMPAPI error: delete_markers method does not accept empty arrays.', 'lmm' ), $marker_ids );
		//info:  in case of one or more elements of the array were not integers.
		$marker_ids = array_map('intval', $marker_ids);
		if(in_array(0, $marker_ids)){
			 return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: all the elements of marker_ids array must be integers', 'lmm' ), $marker_ids );
		}
		//info:  query the database
		$marker_ids = implode(',', $marker_ids);

		$marker_result = $wpdb->query( "DELETE  FROM `".self::table_name_markers()."` WHERE `id` IN (".$marker_ids.")" );
		return ($marker_result !== 0);
	}

	/**
    * Returns all layer objects
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @return mixed The layer objects or false
    */
	public static function list_layers( $args = NULL ){
		global $wpdb;
		if (isset($args)) {
			//info:  to make sure that the args is an array
			if(!is_array($args)){
				 return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: list_layers method only accepts arrays. Input was %s', 'lmm' ), gettype($args)), $args );
			}

			$allowed_keys = self::get_allowed_layer_orderkeys();
			if(isset($args['orderkey']) && !in_array($args['orderkey'], $allowed_keys )){
				 return new WP_Error( 'invalid_parameter', sprintf(__( 'MMPAPI error: orderkey is not valid, it must be one of the following keys: %s', 'lmm' ), implode(', ',$allowed_keys)), $args );
			}
			extract($args);
		}
		$orderkey = isset($orderkey) ? $orderkey : 'id';
		$orderdir = ( isset($orderdir) && ( $orderdir == 'ASC' || $orderdir == 'DESC' ) ) ? $orderdir : 'ASC';
		$orderoff = isset($orderoff) ? abs(intval($orderoff)) : 0;
		$orderlimit = isset($orderlimit) ? abs(intval($orderlimit)) : 999999;
		//info:  query the database
		$layer_list = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `".self::table_name_layers()."` WHERE `id` != 0 ORDER BY `".$orderkey."` ".$orderdir." LIMIT %d, %d", $orderoff, $orderlimit));
		//info:  to make sure that layers exist in the database
		if($layer_list === null) return false;
		return $layer_list;
	}

	/**
    * Returns the layer object for a given Layer ID
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param int $layer_id The ID of the Layer
    *
    * @return mixed The layer object or false
    */
	public static function get_layer( $layer_id ){
		global $wpdb;
		//info:  to make sure that the layer_id is a number
		$layer_id = intval( $layer_id );
		if($layer_id === 0) return false;
		//info:  query the database
		$layer_result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `".self::table_name_layers()."` WHERE `id` = %d", $layer_id));
		//info:  to make sure that the layer_id exists in the database
		if($layer_result === null) return false;
		return $layer_result;
	}

	/**
    * Returns the layer objects for given Layer IDs
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param int $layer_ids The IDs of the Layers
    *
    * @return mixed The layer objects or false
    */
	public static function get_layers( $layer_ids ){
		global $wpdb;
		//info:  to make sure that the layer_ids is an array
		if(!is_array($layer_ids)) return false;
		//info:  query the database
		$layer_ids = implode(',', array_map('intval', $layer_ids));
		$layers_result = $wpdb->get_results( "SELECT * FROM `".self::table_name_layers()."` WHERE `id` IN (".$layer_ids .") AND `id` != 0");
		//info:  to make sure that the layer_ids exists in the database
		if($layers_result === null) return false;
		return $layers_result;
	}

	/**
    * Add a layer based on given data array
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $layer_data Data of the layer
    *
    * @return mixed int|WP_Error
    */
	public static function add_layer( $layer_data ){
		require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'class-mmp-geocoding.php' );
		global $wpdb;
		$lmm_options = self::lmm_options();
		$name = isset($layer_data['name']) ? $layer_data['name'] : '';
		$name_quotes = str_replace("\\\\","/", str_replace("\"", "'", sanitize_text_field($name)));
		$basemap = isset($layer_data['basemap']) && in_array($layer_data['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $layer_data['basemap'] : $lmm_options[ 'standard_basemap' ];
		$layerzoom = isset($layer_data['layerzoom']) ? intval($layer_data['layerzoom']) :  intval($lmm_options[ 'defaults_layer_zoom' ]);
		$mapwidth = isset($layer_data['mapwidth']) ? $layer_data['mapwidth'] : intval($lmm_options[ 'defaults_layer_mapwidth' ]);
		$mapwidthunit = ( isset($layer_data['mapwidthunit']) && ( ($layer_data['mapwidthunit'] == 'px') || ($layer_data['mapwidthunit'] == '%') ) ) ? $layer_data['mapwidthunit'] : $lmm_options[ 'defaults_layer_mapwidthunit' ];
		$mapheight = isset($layer_data['mapheight']) ? $layer_data['mapheight'] : intval($lmm_options[ 'defaults_layer_mapheight' ]);
		$panel = ( isset($layer_data['panel']) && ( ($layer_data['panel'] == '0') || ($layer_data['panel'] == '1')) ) ? $layer_data['panel'] :  $lmm_options[ 'defaults_layer_panel' ];
		$layerviewlat = isset($layer_data['layerviewlat']) ? floatval($layer_data['layerviewlat']) :  str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lat' ]));
		$layerviewlon = isset($layer_data['layerviewlon']) ? floatval($layer_data['layerviewlon']) : str_replace(',', '.', floatval($lmm_options[ 'defaults_layer_lon' ]));
		$createdby = isset($layer_data['createdby']) ? esc_html($layer_data['createdby']) : 'MapsMarker API';
		$createdon = isset($layer_data['createdon']) && ( $layer_data['createdon'] == date('Y-m-d H:i:s',strtotime($layer_data['createdon'])) ) ? $layer_data['createdon'] : current_time('mysql',0);
		$updatedby = isset($layer_data['updatedby']) ? esc_html($layer_data['updatedby']) : 'MapsMarker API';
		$updatedon = isset($layer_data['updatedon']) && ( $layer_data['createdon'] == date('Y-m-d H:i:s',strtotime($layer_data['createdon'])) ) ? $layer_data['updatedon'] :  current_time('mysql',0);
		$controlbox = ( isset($layer_data['controlbox']) && ( ($layer_data['controlbox'] == '0') || ($layer_data['controlbox'] == '1') || ($layer_data['controlbox'] == '2')) ) ? $layer_data['controlbox'] :  $lmm_options[ 'defaults_layer_controlbox' ];
		$overlays_custom = ( isset($layer_data['overlays_custom']) && ( ($layer_data['overlays_custom'] == '0') || ($layer_data['overlays_custom'] == '1')) ) ? $layer_data['overlays_custom'] : ($lmm_options[ 'defaults_layer_overlays_custom_active' ] !== 0) ? '1' : '0';
		$overlays_custom2 = ( isset($layer_data['overlays_custom2']) && ( ($layer_data['overlays_custom2'] == '0') || ($layer_data['overlays_custom2'] == '1')) ) ? $layer_data['overlays_custom2'] : ($lmm_options[ 'defaults_layer_overlays_custom2_active' ] !== 0) ? '1' : '0';
		$overlays_custom3 = ( isset($layer_data['overlays_custom3']) && ( ($layer_data['overlays_custom3'] == '0') || ($layer_data['overlays_custom3'] == '1')) ) ? $layer_data['overlays_custom3'] : ($lmm_options[ 'defaults_layer_overlays_custom3_active' ] !== 0) ? '1' : '0';
		$overlays_custom4 = ( isset($layer_data['overlays_custom4']) && ( ($layer_data['overlays_custom4'] == '0') || ($layer_data['overlays_custom4'] == '1')) ) ? $layer_data['overlays_custom4'] : ($lmm_options[ 'defaults_layer_overlays_custom4_active' ] !== 0) ? '1' : '0';
		$wms = ( isset($layer_data['wms']) && ( ($layer_data['wms'] == '0') || ($layer_data['wms'] == '1')) ) ? $layer_data['wms'] : ($lmm_options[ 'defaults_layer_wms_active'] !==0 ) ? '1' : '0';
		$wms2 = ( isset($layer_data['wms2']) && ( ($layer_data['wms2'] == '0') || ($layer_data['wms2'] == '1')) ) ? $layer_data['wms2'] : ($lmm_options[ 'defaults_layer_wms2_active'] !==0) ? '1' : '0';
		$wms3 = ( isset($layer_data['wms3']) && ( ($layer_data['wms3'] == '0') || ($layer_data['wms3'] == '1')) ) ? $layer_data['wms3'] : ($lmm_options[ 'defaults_layer_wms3_active'] !==0 ) ? '1' : '0';
		$wms4 = ( isset($layer_data['wms4']) && ( ($layer_data['wms4'] == '0') || ($layer_data['wms4'] == '1')) ) ? $layer_data['wms4'] : ($lmm_options[ 'defaults_layer_wms4_active'] !==0 ) ? '1' : '0';
		$wms5 = ( isset($layer_data['wms5']) && ( ($layer_data['wms5'] == '0') || ($layer_data['wms5'] == '1')) ) ? $layer_data['wms5'] : ($lmm_options[ 'defaults_layer_wms5_active'] !==0 ) ? '1' : '0';
		$wms6 = ( isset($layer_data['wms6']) && ( ($layer_data['wms6'] == '0') || ($layer_data['wms6'] == '1')) ) ? $layer_data['wms6'] : ($lmm_options[ 'defaults_layer_wms6_active'] !==0 ) ? '1' : '0';
		$wms7 = ( isset($layer_data['wms7']) && ( ($layer_data['wms7'] == '0') || ($layer_data['wms7'] == '1')) ) ? $layer_data['wms7'] : ($lmm_options[ 'defaults_layer_wms7_active'] !==0 ) ? '1' : '0';
		$wms8 = ( isset($layer_data['wms8']) && ( ($layer_data['wms8'] == '0') || ($layer_data['wms8'] == '1')) ) ? $layer_data['wms8'] : ($lmm_options[ 'defaults_layer_wms8_active'] !==0 ) ? '1' : '0';
		$wms9 = ( isset($layer_data['wms9']) && ( ($layer_data['wms9'] == '0') || ($layer_data['wms9'] == '1')) ) ? $layer_data['wms9'] : ($lmm_options[ 'defaults_layer_wms9_active'] !==0 ) ? '1' : '0';
		$wms10 = ( isset($layer_data['wms10']) && ( ($layer_data['wms10'] == '0') || ($layer_data['wms10'] == '1')) ) ? $layer_data['wms10'] : ($lmm_options[ 'defaults_layer_wms10_active'] !==0 ) ? '1' : '0';
		$listmarkers = ( isset($layer_data['listmarkers']) && ( ($layer_data['listmarkers'] == '0') || ($layer_data['listmarkers'] == '1')) ) ? $layer_data['listmarkers'] : (isset($lmm_options[ 'defaults_layer_listmarkers' ]) ? '1' : '0');
		$multi_layer_map = ( isset($layer_data['multi_layer_map']) && ( ($layer_data['multi_layer_map'] == '0') || ($layer_data['multi_layer_map'] == '1')) ) ? $layer_data['multi_layer_map'] : '0';
		$multi_layer_map_list = isset($layer_data['multi_layer_map_list']) ? $layer_data['multi_layer_map_list'] : '';
		$address = isset($layer_data['address']) ? sanitize_text_field($layer_data['address']) : '';
		$clustering = ( isset($layer_data['clustering']) && ( ($layer_data['clustering'] == '0') || ($layer_data['clustering'] == '1')) ) ? $layer_data['clustering'] : ($lmm_options[ 'defaults_layer_clustering' ] == 'enabled') ? '1' : '0';
		$gpx_url = isset($layer_data['gpx_url']) ? esc_url_raw($layer_data['gpx_url']) : '';
		$gpx_panel = ( isset($layer_data['gpx_panel']) && ( ($layer_data['gpx_panel'] == '0') || ($layer_data['gpx_panel'] == '1')) ) ? $layer_data['gpx_panel'] :  '0';
		$mlm_filter = ( isset($layer_data['mlm_filter']) && ( ($layer_data['mlm_filter'] == '0') || ($layer_data['mlm_filter'] == '1')) ) ? $layer_data['mlm_filter'] :  '0';
		$mlm_filter_details_input = isset($layer_data['mlm_filter_details']) ? stripslashes($layer_data['mlm_filter_details']) : '';
		//info: make sure input is valid JSON
		$mlm_filter_details_input_decoded = json_decode($mlm_filter_details_input);
		if($mlm_filter_details_input_decoded === NULL) {
			$mlm_filter_details = '';
		} else {
			$mlm_filter_details = $mlm_filter_details_input;
		}

		$geocode = isset($layer_data['geocode'])? $layer_data['geocode']: '';
		if ($geocode != '') {
				$do_geocoding = MMP_Geocoding::getLatLng($geocode);
			if ($do_geocoding['success'] == true) {
				$layerviewlat = $do_geocoding['lat'];
				$layerviewlon = $do_geocoding['lon'];
				$address = $do_geocoding['address'];
			}else{
				return new WP_Error( 'geocode_failed', sprintf( __( 'MMPAPI error: geocoding error: %1s', 'lmm' ), $do_geocoding['message'] ), $layer_data );
			}
		}
		if($mlm_filter_details_input_decoded == NULL){
			$query_add = $wpdb->prepare( "INSERT INTO `". self::table_name_layers() ."` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d)", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter );
		}else{
			$query_add = $wpdb->prepare( "INSERT INTO `". self::table_name_layers() ."` (`name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `panel`, `layerviewlat`, `layerviewlon`, `createdby`, `createdon`, `updatedby`, `updatedon`, `controlbox`, `overlays_custom`, `overlays_custom2`, `overlays_custom3`, `overlays_custom4`, `wms`, `wms2`, `wms3`, `wms4`, `wms5`, `wms6`, `wms7`, `wms8`, `wms9`, `wms10`, `listmarkers`, `multi_layer_map`, `multi_layer_map_list`, `address`, `clustering`, `gpx_url`, `gpx_panel`, `mlm_filter`, `mlm_filter_details` ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d, %d, %s)", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter, $mlm_filter_details );
		}

		$result_add = $wpdb->query( $query_add );

		return ($result_add === 1)?$wpdb->insert_id:FALSE;

	}

	/**
    * Add layers based on given data object
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $layers_data Data of the marker
    *
    * @return array int|WP_Error
    */
	public static function add_layers( $layers_data ){
		if(! $layers_data || !is_array($layers_data)){
			return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: invalid marker objects. Input was %s', 'lmm' ), gettype($layers_data)), $layers_data );
		}
        $layer_ids = array();
        foreach ( $layers_data as $layer ) {
          $result = self::add_layer( $layer );
          if ( is_wp_error( $result ) ) {
              return $result;
          }
          $layer_ids[] = $result;
        }
        return $layer_ids;
	}

	/**
    * Update a layer based on given data object
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $layer_data Data of the layer
    *
    * @return boolean
    */
	public static function update_layer(  $layer_data ){
		require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'class-mmp-geocoding.php' );
		$lmm_options = self::lmm_options();
		global $wpdb;
		$id = isset($layer_data['id']) ? $layer_data['id'] : '';
		$query_view = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `". self::table_name_layers() ."` WHERE `id` = %d", $id), ARRAY_A);
		if (count($query_view) >= 1) {
			$name = isset($layer_data['name']) ? $layer_data['name'] :  $query_view['name'];
			$name_quotes = str_replace("\\\\","/", str_replace("\"", "'", sanitize_text_field($name)));
			$basemap = isset($layer_data['basemap']) && in_array($layer_data['basemap'], array('osm_mapnik','stamen_terrain','stamen_toner','stamen_watercolor','mapquest_osm','mapquest_aerial','mapquest_hybrid','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap')) ? $layer_data['basemap'] :  $query_view['basemap'];
			$layerzoom = isset($layer_data['layerzoom']) ? intval($layer_data['layerzoom']) : $query_view['layerzoom'];
			$mapwidth = isset($layer_data['mapwidth']) ? $layer_data['mapwidth'] : $query_view['mapwidth'];
			$mapwidthunit = ( isset($layer_data['mapwidthunit']) && ( ($layer_data['mapwidthunit'] == 'px') || ($layer_data['mapwidthunit'] == '%') ) ) ? $layer_data['mapwidthunit'] :  $query_view['mapwidthunit'];
			$mapheight = isset($layer_data['mapheight']) ? $layer_data['mapheight'] : $query_view['mapheight'];
			$panel = ( isset($layer_data['panel']) && ( ($layer_data['panel'] == '0') || ($layer_data['panel'] == '1')) ) ? $layer_data['panel'] : $query_view['panel'];
			$layerviewlat = isset($layer_data['layerviewlat']) ? floatval($layer_data['layerviewlat']) : $query_view['layerviewlat'];
			$layerviewlon = isset($layer_data['layerviewlon']) ? floatval($layer_data['layerviewlon']) : $query_view['layerviewlon'];
			$createdby = isset($layer_data['createdby']) ? esc_html($layer_data['createdby']) : esc_html($query_view['createdby']);
			$createdon = isset($layer_data['createdon']) && ( $layer_data['createdon'] == date('Y-m-d H:i:s',strtotime($layer_data['createdon'])) ) ? $layer_data['createdon'] : $query_view['createdon'];
			$updatedby = isset($layer_data['updatedby']) ? esc_html($layer_data['updatedby'])  : esc_html($query_view['updatedby']);
			$updatedon = isset($layer_data['updatedon']) && ( $layer_data['createdon'] == date('Y-m-d H:i:s',strtotime($layer_data['createdon'])) ) ? $layer_data['updatedon'] : current_time('mysql',0);
			$controlbox = ( isset($layer_data['controlbox']) && ( ($layer_data['controlbox'] == '0') || ($layer_data['controlbox'] == '1') || ($layer_data['controlbox'] == '2')) ) ? $layer_data['controlbox'] :  $query_view['controlbox'];
			$overlays_custom = ( isset($layer_data['overlays_custom']) && ( ($layer_data['overlays_custom'] == '0') || ($layer_data['overlays_custom'] == '1')) ) ? $layer_data['overlays_custom'] : $query_view['overlays_custom'];
			$overlays_custom2 = ( isset($layer_data['overlays_custom2']) && ( ($layer_data['overlays_custom2'] == '0') || ($layer_data['overlays_custom2'] == '1')) ) ? $layer_data['overlays_custom2'] : $query_view['overlays_custom2'];
			$overlays_custom3 = ( isset($layer_data['overlays_custom3']) && ( ($layer_data['overlays_custom3'] == '0') || ($layer_data['overlays_custom3'] == '1')) ) ? $layer_data['overlays_custom3'] : $query_view['overlays_custom3'];
			$overlays_custom4 = ( isset($layer_data['overlays_custom4']) && ( ($layer_data['overlays_custom4'] == '0') || ($layer_data['overlays_custom4'] == '1')) ) ? $layer_data['overlays_custom4'] : $query_view['overlays_custom4'];
			$wms = ( isset($layer_data['wms']) && ( ($layer_data['wms'] == '0') || ($layer_data['wms'] == '1')) ) ? $layer_data['wms'] : $query_view['wms'];
			$wms2 = ( isset($layer_data['wms2']) && ( ($layer_data['wms2'] == '0') || ($layer_data['wms2'] == '1')) ) ? $layer_data['wms2'] : $query_view['wms2'];
			$wms3 = ( isset($layer_data['wms3']) && ( ($layer_data['wms3'] == '0') || ($layer_data['wms3'] == '1')) ) ? $layer_data['wms3'] : $query_view['wms3'];
			$wms4 = ( isset($layer_data['wms4']) && ( ($layer_data['wms4'] == '0') || ($layer_data['wms4'] == '1')) ) ? $layer_data['wms4'] : $query_view['wms4'];
			$wms5 = ( isset($layer_data['wms5']) && ( ($layer_data['wms5'] == '0') || ($layer_data['wms5'] == '1')) ) ? $layer_data['wms5'] : $query_view['wms5'];
			$wms6 = ( isset($layer_data['wms6']) && ( ($layer_data['wms6'] == '0') || ($layer_data['wms6'] == '1')) ) ? $layer_data['wms6'] : $query_view['wms6'];
			$wms7 = ( isset($layer_data['wms7']) && ( ($layer_data['wms7'] == '0') || ($layer_data['wms7'] == '1')) ) ? $layer_data['wms7'] : $query_view['wms7'];
			$wms8 = ( isset($layer_data['wms8']) && ( ($layer_data['wms8'] == '0') || ($layer_data['wms8'] == '1')) ) ? $layer_data['wms8'] : $query_view['wms8'];
			$wms9 = ( isset($layer_data['wms9']) && ( ($layer_data['wms9'] == '0') || ($layer_data['wms9'] == '1')) ) ? $layer_data['wms9'] : $query_view['wms9'];
			$wms10 = ( isset($layer_data['wms10']) && ( ($layer_data['wms10'] == '0') || ($layer_data['wms10'] == '1')) ) ? $layer_data['wms10'] : $query_view['wms10'];
			$listmarkers = ( isset($layer_data['listmarkers']) && ( ($layer_data['listmarkers'] == '0') || ($layer_data['listmarkers'] == '1')) ) ? $layer_data['listmarkers'] :  $query_view['listmarkers'];
			$multi_layer_map = ( isset($layer_data['multi_layer_map']) && ( ($layer_data['multi_layer_map'] == '0') || ($layer_data['multi_layer_map'] == '1')) ) ? $layer_data['multi_layer_map'] : $query_view['multi_layer_map'];
			$multi_layer_map_list = isset($layer_data['multi_layer_map_list']) ? $layer_data['multi_layer_map_list'] : $query_view['multi_layer_map_list'];
			$address = isset($layer_data['address']) ? sanitize_text_field($layer_data['address']) : $query_view['address'];
			$clustering = ( isset($layer_data['clustering']) && ( ($layer_data['clustering'] == '0') || ($layer_data['clustering'] == '1')) ) ? $layer_data['clustering'] :  $query_view['clustering'];
			$gpx_url = isset($layer_data['gpx_url']) ? esc_url_raw($layer_data['gpx_url']) : esc_url_raw($query_view['gpx_url']);
			$gpx_panel = ( isset($layer_data['gpx_panel']) && ( ($layer_data['gpx_panel'] == '0') || ($layer_data['gpx_panel'] == '1')) ) ? $layer_data['gpx_panel'] : $query_view['gpx_panel'];
			$mlm_filter = ( isset($layer_data['mlm_filter']) && ( ($layer_data['mlm_filter'] == '0') || ($layer_data['mlm_filter'] == '1')) ) ? $layer_data['mlm_filter'] :  '0';
			$mlm_filter_details_input = isset($layer_data['mlm_filter_details']) ? stripslashes($layer_data['mlm_filter_details']) : '';
			//info: make sure input is valid JSON
			$mlm_filter_details_input_decoded = json_decode($mlm_filter_details_input);
			if($mlm_filter_details_input_decoded === NULL) {
				$mlm_filter_details = '';
			} else {
				$mlm_filter_details = $mlm_filter_details_input;
			}

			$geocode = isset($layer_data['geocode'])? $layer_data['geocode']: '';
			if ($geocode != NULL) {
				$do_geocoding = MMP_Geocoding::getLatLng($geocode);
				if ($do_geocoding['success'] == true) {
					$layerviewlat = $do_geocoding['lat'];
					$layerviewlon = $do_geocoding['lon'];
					$address = $do_geocoding['address'];
				} else {
					return new WP_Error( 'geocode_failed', sprintf( __( 'Geocoding error: %1s', 'lmm' ), $do_geocoding['message'] ), $layer_data );
				}
			}
			if($mlm_filter_details_input_decoded == NULL){
				$query_update = $wpdb->prepare( "UPDATE `". self::table_name_layers() ."` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d, `mlm_filter` = %d WHERE `id` = %d", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel,  $mlm_filter, $id );
			}else{
				$query_update = $wpdb->prepare( "UPDATE `". self::table_name_layers() ."` SET `name` = %s, `basemap` = %s, `layerzoom` = %d, `mapwidth` = %d, `mapwidthunit` = %s, `mapheight` = %d, `panel` = %d, `layerviewlat` = %s, `layerviewlon` = %s, `createdby` = %s, `createdon` = %s, `updatedby` = %s, `updatedon` = %s, `controlbox` = %d, `overlays_custom` = %d, `overlays_custom2` = %d, `overlays_custom3` = %d, `overlays_custom4` = %d, `wms` = %d, `wms2` = %d, `wms3` = %d, `wms4` = %d, `wms5` = %d, `wms6` = %d, `wms7` = %d, `wms8` = %d, `wms9` = %d, `wms10` = %d, `listmarkers` = %d, `multi_layer_map` = %d, `multi_layer_map_list` = %s, `address` = %s, `clustering` = %d, `gpx_url` = %s, `gpx_panel` = %d, `mlm_filter` = %d, `mlm_filter_details` = %s WHERE `id` = %d", $name_quotes, $basemap, $layerzoom, $mapwidth, $mapwidthunit, $mapheight, $panel, str_replace(',', '.', $layerviewlat), str_replace(',', '.', $layerviewlon), $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $listmarkers, $multi_layer_map, $multi_layer_map_list, $address, $clustering, $gpx_url, $gpx_panel, $mlm_filter, $mlm_filter_details, $id );
			}

			$result_update = $wpdb->query( $query_update );
			return ($result_update === 1)?TRUE:FALSE;
		}else{
				return new WP_Error( 'not_found', sprintf( __( 'MMPAPI error: marker ID %1s was not found', 'lmm' ), $id ), $layer_data );
		}
	}

	/**
    * Update layers based on given data object
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $layers_data Data of the layers
    *
    * @return array|WP_Error IDs of updated layers or an error object.
    */
	public static function update_layers(  $layers_data  ){
		if(! $layers_data || !is_array($layers_data)){
			return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: invalid layer objects. Input was %s', 'lmm' ), gettype($layers_data)), $layers_data );
		}
        $layers_ids = array();
        foreach ( $layers_data as $layer ) {
          $result = self::update_layer( $layer );
          if ( is_wp_error( $result ) ) {
              return $result;
          }
          $layers_ids[$layer['id']] = $result;
        }
        return $layers_ids;
	}

	/**
    * Deletes the layer for a given layer ID
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param int $layer_id The ID of the layer
    *
    * @return mixed TRUE if the layer deleted successfully, WP_Error object if not
    */
	public static function delete_layer( $layer_id, $delete_markers = false ){
		global $wpdb;
		//info:  to make sure that the layer_id is a number
		$layer_id = intval( $layer_id );
		if($layer_id === 0) return new WP_Error( 'invalid_parameter',  __( 'MMPAPI error: delete_layer method only accepts integers', 'lmm' ), $layer_id );

		if($delete_markers === true){
			//info: delete qr code cache images for assigned markers
			$layer_marker_list_qr = $wpdb->get_results('SELECT m.id as markerid,m.layer as mlayer,l.id as lid FROM `'.self::table_name_layers().'` as l INNER JOIN `'.self::table_name_markers().'` AS m ON l.id=m.layer WHERE l.id=' . $layer_id, ARRAY_A);
			foreach ($layer_marker_list_qr as $row){
				if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png') ) {
					unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png');
				}
			}
			//info: delete qr code cache image for layer
			if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $layer_id . '.png') ) {
				unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $layer_id . '.png');
			}
			$multi_layer_map_list_exploded = $wpdb->get_var($wpdb->prepare('SELECT l.multi_layer_map_list FROM `'.self::table_name_layers().'` as l WHERE l.id=%d',$layer_id));
			if(!is_null($multi_layer_map_list_exploded)){
				$multi_layer_map_list_exploded = explode(",", $multi_layer_map_list_exploded);
			}
			if(is_null($multi_layer_map_list_exploded)){
				$markers_to_delete = $wpdb->get_results($wpdb->prepare("SELECT id,layer FROM ". self::table_name_markers() ." WHERE `layer` LIKE %s ",'%"'.$layer_id.'"%'));
			}
			else {
				//info: delete markers of mlm layers
				$markers_to_delete = array();
				foreach($multi_layer_map_list_exploded as $lid){
					$markers = $wpdb->get_results("SELECT id,layer FROM ". self::table_name_markers() ." WHERE `layer` LIKE '%\"".$lid."\"%'");
					if(!is_null($markers)){
						$markers_to_delete = array_merge($markers_to_delete,$markers);
					}
				}
			}
			foreach($markers_to_delete as $row){
				$layer = json_decode($row->layer);
				if(count($layer) === 1){
					$wpdb->query("DELETE FROM ". self::table_name_markers() ." WHERE `id` =".$row->id);
				} else {
					if(!is_null($multi_layer_map_list_exploded)){
						foreach($multi_layer_map_list_exploded as $mlm_layer){
							$key = array_search($mlm_layer, $layer);
							if($key !== FALSE)
								unset( $layer[$key] );
						}
					}
					$key = array_search($layer_id, $layer);
					if($key !== FALSE)
						unset($layer[$key]);
					$layer = array_values( $layer );
					$wpdb->update(self::table_name_markers(), array('layer' => json_encode($layer) ), array('id' => $row->id));
				}
			}
			if(!is_null($multi_layer_map_list_exploded)){
				$wpdb->query( "DELETE FROM ". self::table_name_layers() ." WHERE `id` IN (" . htmlspecialchars(implode(',',$multi_layer_map_list_exploded)) . ")");
			}
		}else{
			$associated_markers = $wpdb->get_results($wpdb->prepare("SELECT id,layer FROM `". self::table_name_markers() ."` WHERE layer LIKE '%%\"%d\"%%' ", $layer_id) );
			foreach($associated_markers as $marker){
				$layer = json_decode($marker->layer, true);
				$marker_key = array_search($layer_id, $layer);
				unset($layer[$marker_key]);
				//info: if not no layers left, make the marker unassigned.
				if(count($layer) == 0){
					$layer[] = "0";
					$layer = array_values($layer);
				}
				$wpdb->update(self::table_name_markers(),
							  array('layer'=> json_encode($layer)),
							  array('id' => $marker->id)
							  );
			}
		}
		//info:  deleting the layer
		$layer_result = $wpdb->query( $wpdb->prepare("DELETE  FROM `".self::table_name_layers()."` WHERE `id` = %d", $layer_id));
		//info:  it will return false if the number of rows affected is zero, and it will return true otherwise.
		if($layer_result === 0){
			return new WP_Error( 'not_found', sprintf( __( 'MMPAPI error: layer with ID %s not found', 'lmm' ), $layer_id ), $layer_id );
		}else{
			return true;
		}
	}

	/**
    * Deletes the layers for given Layers IDs
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $layer_ids The IDs of the Layers
    *
    * @return boolean true if the markers deleted successfully, WP_Error object if not
    */
	public static function delete_layers( $layer_ids = array(), $delete_markers = false ){
		global $wpdb;
		//info:  to make sure that the marker_id is an array
		if(!is_array($layer_ids)){
			 return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: delete_layers method only accepts arrays. Input was %s', 'lmm' ), gettype($layer_ids)), $layer_ids );
		}
		//info:  to make sure that the layer_ids is not empty
		if(empty($layer_ids)) return new WP_Error( 'empty_parameter', __( 'MMPAPI error: delete_markers method does not accept empty arrays.', 'lmm' ), $layer_ids );
		//info:  in case of one or more elements of the array were not integers.
		$layer_ids = array_map('intval', $layer_ids);
		if(in_array(0, $layer_ids)){
			 return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: all the elements of layer_ids array must be integers', 'lmm' ), $layer_ids );
		}

		foreach($layer_ids as $layer_id){


			if($delete_markers === true){
					//info: delete qr code cache images for assigned markers
					$layer_marker_list_qr = $wpdb->get_results('SELECT m.id as markerid,m.layer as mlayer,l.id as lid FROM `'.self::table_name_layers().'` as l INNER JOIN `'.self::table_name_markers().'` AS m ON l.id=m.layer WHERE l.id=' . $layer_id, ARRAY_A);
					foreach ($layer_marker_list_qr as $row){
						if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png') ) {
							unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $row['markerid'] . '.png');
						}
					}
					//info: delete qr code cache image for layer
					if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $layer_id . '.png') ) {
						unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'layer-' . $layer_id . '.png');
					}
					$multi_layer_map_list_exploded = $wpdb->get_var($wpdb->prepare('SELECT l.multi_layer_map_list FROM `'.self::table_name_layers().'` as l WHERE l.id=%d',$layer_id));
					if(!is_null($multi_layer_map_list_exploded)){
						$multi_layer_map_list_exploded = explode(",", $multi_layer_map_list_exploded);
					}
					if(is_null($multi_layer_map_list_exploded)){
						$markers_to_delete = $wpdb->get_results($wpdb->prepare("SELECT id,layer FROM ". self::table_name_markers() ." WHERE `layer` LIKE %s ",'%"'.$layer_id.'"%'));
					}
					else {
						//info: delete markers of mlm layers
						$markers_to_delete = array();
						foreach($multi_layer_map_list_exploded as $lid){
							$markers = $wpdb->get_results("SELECT id,layer FROM ". self::table_name_markers() ." WHERE `layer` LIKE '%\"".$lid."\"%'");
							if(!is_null($markers)){
								$markers_to_delete = array_merge($markers_to_delete,$markers);
							}
						}
					}
					foreach($markers_to_delete as $row){
						$layer = json_decode($row->layer);
						if(count($layer) === 1){
							$wpdb->query("DELETE FROM ". self::table_name_markers() ." WHERE `id` =".$row->id);
						} else {
							if(!is_null($multi_layer_map_list_exploded)){
								foreach($multi_layer_map_list_exploded as $mlm_layer){
									$key = array_search($mlm_layer, $layer);
									if($key !== FALSE)
										unset( $layer[$key] );
								}
							}
							$key = array_search($layer_id, $layer);
							if($key !== FALSE)
								unset($layer[$key]);
							$layer = array_values( $layer );
							$wpdb->update(self::table_name_markers(), array('layer' => json_encode($layer) ), array('id' => $row->id));
						}
					}
					if(!is_null($multi_layer_map_list_exploded)){
						$wpdb->query( "DELETE FROM ". self::table_name_layers() ." WHERE `id` IN (" . htmlspecialchars(implode(',',$multi_layer_map_list_exploded)) . ")");
					}
			}else{
				$associated_markers = $wpdb->get_results($wpdb->prepare("SELECT id,layer FROM `". self::table_name_markers() ."` WHERE layer LIKE '%%\"%d\"%%' ", $layer_id) );
				foreach($associated_markers as $marker){
					$layer = json_decode($marker->layer, true);
					$marker_key = array_search($layer_id, $layer);
					unset($layer[$marker_key]);
					//info: if not no layers left, make the marker unassigned.
					if(count($layer) == 0){
						$layer[] = "0";
						$layer = array_values($layer);
					}
					$wpdb->update(self::table_name_markers(),
								  array('layer'=> json_encode($layer)),
								  array('id' => $marker->id)
								  );
				}
			}

		}

		//info:  delete the layers
		$layer_ids = implode(',', $layer_ids);
		$layer_result = $wpdb->query( "DELETE  FROM `".self::table_name_layers()."` WHERE `id` IN (".$layer_ids.")" );
		return ($layer_result !== 0);
	}

	/**
    * returns the number of markers available
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @return int The markers count
    */
	public static function count_markers( ){
		global $wpdb;
		$count_result = $wpdb->get_row( 'SELECT COUNT(*) as markers_count FROM '.self::table_name_markers() );
		return (int)$count_result->markers_count;
	}

	/**
    * returns the number of layers available
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @return int The layers count
    */
	public static function count_layers( ){
		global $wpdb;
		$count_result = $wpdb->get_row( 'SELECT COUNT(*) as layers_count FROM ' .self::table_name_layers(). ' WHERE id <> 0' );
		return (int)$count_result->layers_count;
	}

	/**
    * Search markers object for a given args.
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $args The parameters of the search
    *
    * @return array The marker object or false
    */
	public static function search_markers( $args ){
		global $wpdb;
		//info:  to make sure that the args is an array
		if(!is_array($args)){
			 return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: search_markers method only accepts arrays. Input was %s', 'lmm' ), gettype($args)), $args );
		}

		if(!isset($args['searchkey'])){
			 return new WP_Error( 'invalid_parameter',  __( 'MMPAPI error: searchkey parameter is required.', 'lmm' ), $args );
		}
		if(!isset($args['searchvalue'])){
			 return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: searchvalue parameter is required.', 'lmm' ), $args );
		}
		$allowed_keys = self::get_allowed_marker_searchkeys();
		if(!in_array($args['searchkey'], $allowed_keys )){
			 return new WP_Error( 'invalid_parameter', sprintf(__( 'MMPAPI error: searchkey is not valid, it must be one of the following keys: %s', 'lmm' ), implode(', ',$allowed_keys)), $args );
		}
		extract($args);

		switch ($searchkey) {
			case 'layer':
				$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_markers() ."` WHERE layer LIKE %s", '%"' . intval($searchvalue) . '"%'));
				break;
			case 'id':
			case 'zoom':
			case 'openpopup':
			case 'mapwidth':
			case 'mapheight':
			case 'panel':
			case 'controlbox':
			case 'overlays_custom':
			case 'overlays_custom2':
			case 'overlays_custom3':
			case 'overlays_custom4':
			case 'wms':
			case 'wms2':
			case 'wms3':
			case 'wms4':
			case 'wms5':
			case 'wms6':
			case 'wms7':
			case 'wms8':
			case 'wms9':
			case 'wms10':
			case 'gpx_panel':
				$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_markers() ."` WHERE `$searchkey` = %d",  $searchvalue ));
				break;
			case 'createdon':
			case 'updatedon':
			case 'kml_timestamp':
				$date_from = isset($date_from) ? strtotime($date_from) : '';
				$date_to = isset($date_to) ? strtotime($date_to) : '';
				if ( ($date_from != NULL) && ($date_to == NULL) ) {
					$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_markers() ."` WHERE `$searchkey` > FROM_UNIXTIME(%d)", $date_from));
				} else if ( ($date_from == NULL) && ($date_to != NULL) ) {
					$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_markers() ."` WHERE `$searchkey` < FROM_UNIXTIME(%d)", $date_to));
				} else if ( ($date_from != NULL) && ($date_to != NULL) ) {
					$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_markers() ."` WHERE `$searchkey` > FROM_UNIXTIME(%d) AND `$searchkey` < FROM_UNIXTIME(%d)", $date_from, $date_to));
				} else { //info: if ($date_from == NULL) && ($date_to == NULL)
					return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: parameter date_from or date_to has be set when searching date fields', 'lmm' ), $args );
				}
				break;
			case 'boundingbox':
				$lat_top_left = isset($lat_top_left) ? floatval(str_replace(",",".", $lat_top_left)) :  '';
				$lon_top_left = isset($lon_top_left) ? floatval(str_replace(",",".", $lon_top_left)) :  '';
				$lat_bottom_right = isset($lat_bottom_right) ? floatval(str_replace(",",".", $lat_bottom_right)) :  '';
				$lon_bottom_right = isset($lon_bottom_right) ? floatval(str_replace(",",".", $lon_bottom_right)) :	'';
				if ( ($lat_top_left == NULL) || ($lon_top_left == NULL) || ($lat_bottom_right == NULL) || ($lon_bottom_right == NULL) ) {
					return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: API parameters lat_top_left, lon_top_left, lat_bottom_right and lon_bottom_right have be set when performing a boundingbox search', 'lmm' ), $args );
				}
				$query_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `". self::table_name_markers() ."` WHERE `lat` <= %s AND `lon` >= %s AND `lat` >= %s AND `lon` <= %s", $lat_top_left, $lon_top_left, $lat_bottom_right, $lon_bottom_right ) );
				break;
			default:
				$query_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `". self::table_name_markers() ."` WHERE $searchkey LIKE %s", '%'.$searchvalue.'%' ) );
				break;
		}
		return $query_result;
	}

	/**
    * Search layers object for a given args.
    *
    * @since  2.5
    * @access public
    * @static
    *
    * @param array $args The parameters of the search
    *
    * @return array The layer objects.
    */
	public static function search_layers( $args ){
		global $wpdb;
		//info:  to make sure that the args is an array
		if(!is_array($args)){
			 return new WP_Error( 'invalid_parameter', sprintf( __( 'MMPAPI error: search_layers method only accepts arrays. Input was %s', 'lmm' ), gettype($args)), $args );
		}

		if(!isset($args['searchkey'])){
			 return new WP_Error( 'invalid_parameter',  __( 'MMPAPI error: searchkey parameter is required.', 'lmm' ), $args );
		}
		if(!isset($args['searchvalue'])){
			 return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: searchvalue parameter is required.', 'lmm' ), $args );
		}
		$allowed_keys = self::get_allowed_layer_searchkeys();
		if(!in_array($args['searchkey'], $allowed_keys )){
			 return new WP_Error( 'invalid_parameter', sprintf(__( 'MMPAPI error: searchkey is not valid, it must be one of the following keys: %s', 'lmm' ), implode(', ',$allowed_keys)), $args );
		}
		extract($args);

		switch ($searchkey) {
			case 'id':
			case 'layerzoom':
			case 'mapwidth':
			case 'mapheight':
			case 'panel':
			case 'controlbox':
			case 'overlays_custom':
			case 'overlays_custom2':
			case 'overlays_custom3':
			case 'overlays_custom4':
			case 'wms':
			case 'wms2':
			case 'wms3':
			case 'wms4':
			case 'wms5':
			case 'wms6':
			case 'wms7':
			case 'wms8':
			case 'wms9':
			case 'wms10':
			case 'listmarkers':
			case 'multi_layer_map':
			case 'clustering':
			case 'gpx_panel':
				$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_layers() ."` WHERE `$searchkey` = %d AND `id` != 0", $searchvalue));
				break;
			case 'updatedon':
			case 'createdon':
				$date_from = isset($date_from) ? strtotime($date_from) : '';
				$date_to = isset($date_to) ? strtotime($date_to) : '';
				if ( ($date_from != NULL) && ($date_to == NULL) ) {
					$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_layers() ."` WHERE `$searchkey` > FROM_UNIXTIME(%d) AND `id` != 0", $date_from));
				} else if ( ($date_from == NULL) && ($date_to != NULL) ) {
					$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_layers() ."` WHERE `$searchkey` < FROM_UNIXTIME(%d) AND `id` != 0", $date_to));
				} else if ( ($date_from != NULL) && ($date_to != NULL) ) {
					$query_result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM `". self::table_name_layers() ."` WHERE `$searchkey` > FROM_UNIXTIME(%d) AND `$searchkey` < FROM_UNIXTIME(%d) AND `id` != 0", $date_from, $date_to));
				} else { //info: if ($date_from == NULL) && ($date_to == NULL)
					return new WP_Error( 'invalid_parameter', __( 'MMPAPI error: API parameter date_from or date_to has be set when searching date fields', 'lmm' ), $args );
				}
				break;
			case 'boundingbox':
				$lat_top_left = isset($lat_top_left) ? floatval(str_replace(",",".", $lat_top_left)) :  '';
				$lon_top_left = isset($lon_top_left) ? floatval(str_replace(",",".", $lon_top_left)) :  '';
				$lat_bottom_right = isset($lat_bottom_right) ? floatval(str_replace(",",".", $lat_bottom_right)) :  '';
				$lon_bottom_right = isset($lon_bottom_right) ? floatval(str_replace(",",".", $lon_bottom_right)) :	'';
				if ( ($lat_top_left == NULL) || ($lon_top_left == NULL) || ($lat_bottom_right == NULL) || ($lon_bottom_right == NULL) ) {
					return new WP_Error( 'invalid_parameter', __( 'API parameters lat_top_left, lon_top_left, lat_bottom_right and lon_bottom_right have be set when performing a boundingbox search', 'lmm' ), $args );
				}
				$query_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `". self::table_name_layers() ."` WHERE `layerviewlat` <= %s AND `layerviewlon` >= %s AND `layerviewlat` >= %s AND `layerviewlon` <= %s AND id != 0", $lat_top_left, $lon_top_left, $lat_bottom_right, $lon_bottom_right) );
				break;
			default:
				$query_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `". self::table_name_layers() ."` WHERE $searchkey LIKE %s AND id != 0", '%'.$searchvalue.'%'));
				break;
		}
		return $query_result;
	}

	// HELPERS ----------------------------------------------------

	/**
    * Returns the markers' table name
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return string The markers' table name.
    */
	private static function table_name_markers(){
		global $wpdb;
		return $wpdb->prefix.'leafletmapsmarker_markers';
	}

	/**
    * Returns the layer markers' table name
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return string The layers' table name.
    */
	private static function table_name_layers(){
		global $wpdb;
		return $wpdb->prefix.'leafletmapsmarker_layers';
	}

	/**
    * Returns the options of Maps Marker Pro
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return array The options of the plugin.
    */
	private static function lmm_options(){
		return get_option( 'leafletmapsmarker_options' );
	}

	/**
    * Returns the allowed orders keys for markers
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return array The order keys
    */
	private static function get_allowed_marker_orderkeys(){

		return array(
				'id',
				'createdon',
				'updatedon',
				'kml_timestamp',
				'markername',
				'basemap',
				'lat',
				'lon',
				'createdby',
				'updatedby',
				'address'
			);
	}

	/**
    * Returns the allowed order keys for layers
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return array The order keys
    */
	private static function get_allowed_layer_orderkeys(){

		return array(
				'id',
				'updatedon',
				'createdon',
				'name',
				'basemap',
				'layerviewlat',
				'layerviewlon',
				'createdby',
				'updatedby',
				'address'
			);
	}

	/**
    * Returns the allowed search keys for markers
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return array The search keys
    */
	private static function get_allowed_marker_searchkeys(){

		return array(
				'id',
				'zoom',
				'layer',
				'openpopup',
				'mapwidth',
				'mapheight',
				'panel',
				'controlbox',
				'overlays_custom',
				'overlays_custom2',
				'overlays_custom3',
				'overlays_custom4',
				'wms',
				'wms2',
				'wms3',
				'wms4',
				'wms5',
				'wms6',
				'wms7',
				'wms8',
				'wms9',
				'wms10',
				'gpx_panel',
				'createdon',
				'updatedon',
				'kml_timestamp',
				'boundingbox',
				'markername',
				'basemap',
				'lat',
				'lon',
				'icon',
				'popuptext',
				'mapwidthunit',
				'createdby',
				'updatedby',
				'address',
				'gpx_url'
			);
	}

	/**
    * Returns the allowed search keys for layers
    *
    * @since  2.5
    * @access private
    * @static
	*
    * @return array The search keys
    */
	private static function get_allowed_layer_searchkeys(){

		return array(
				'id',
				'layerzoom',
				'mapwidth',
				'mapheight',
				'panel',
				'controlbox',
				'overlays_custom',
				'overlays_custom2',
				'overlays_custom3',
				'overlays_custom4',
				'wms',
				'wms2',
				'wms3',
				'wms4',
				'wms5',
				'wms6',
				'wms7',
				'wms8',
				'wms9',
				'wms10',
				'listmarkers',
				'multi_layer_map',
				'clustering',
				'gpx_panel',
				'updatedon',
				'createdon',
				'boundingbox',
				'name',
				'basemap',
				'layerviewlat',
				'layerviewlon',
				'mapwidthunit',
				'createdby',
				'updatedby',
				'multi_layer_map_list',
				'address',
				'gpx_url'
			);
	}

	/**
	* Checks the permissions for the current user. Returns true if the current user has any of the specified capabilities.
	* IMPORTANT: Call this before calling any of the other API Functions as permission checks are not performed at lower levels.
	*
	* @since  2.5
	* @access public
	* @static
	*
	* @param array|string $capabilities An array of capabilities, or a single capability
	*
	* @return bool Returns true if the current user has any of the specified capabilities
	*/
	public static function current_user_can_any( $capabilities ) {
	 	if ( ! is_array( $caps ) ) {
			$has_cap = current_user_can( $caps ) || current_user_can( 'administrator' );
			return $has_cap;
		}
		foreach ( $caps as $cap ) {
			if ( current_user_can( $cap ) ) {
				return true;
			}
		}
		$has_full_access = current_user_can( 'administrator' );
		return $has_full_access;
	}

 }//info: END class MMPAPI
