<?php
/**
* Handles all rewrite functionality for API endpoints
*
* @used in: leaflet-core.php
*/
class MMP_Rewrite {
	/**
	 * Adds additional query vars to WP_Query
	 *
	 * @since  3.0
	 * @access public
	 * @static
	 *
	 * @return array
	 */
	public static function add_query_vars($vars) {
		//info: General vars
		$vars[] = 'endpoint';
		$vars[] = 'marker';
		$vars[] = 'layer';
		//info: Google Places vars
		$vars[] = 'mmp_address';
		$vars[] = 'mmp_place_id';
		$vars[] = '_wpnonce';
		//info: REST-API vars
		$vars[] = 'restapi';
		$vars[] = 'format';
		//info: Authentication vars
		$vars[] = 'public_key';
		$vars[] = 'secret_token';
		//info: Markers vars
		$vars[] = 'marker_id';
		$vars[] = 'markername';
		$vars[] = 'popuptext';
		$vars[] = 'basemap';
		$vars[] = 'lat';
		$vars[] = 'lon';
		$vars[] = 'icon';
		$vars[] = 'zoom';
		$vars[] = 'openpopup';
		$vars[] = 'mapwidth';
		$vars[] = 'mapwidthunit';
		$vars[] = 'mapheight';
		$vars[] = 'panel';
		$vars[] = 'createdby';
		$vars[] = 'createdon';
		$vars[] = 'updatedby';
		$vars[] = 'updatedon';
		$vars[] = 'controlbox';
		$vars[] = 'overlays_custom';
		$vars[] = 'overlays_custom2';
		$vars[] = 'overlays_custom3';
		$vars[] = 'overlays_custom4';
		$vars[] = 'wms';
		$vars[] = 'wms2';
		$vars[] = 'wms3';
		$vars[] = 'wms4';
		$vars[] = 'wms5';
		$vars[] = 'wms6';
		$vars[] = 'wms7';
		$vars[] = 'wms8';
		$vars[] = 'wms9';
		$vars[] = 'wms10';
		$vars[] = 'kml_timestamp';
		$vars[] = 'address';
		$vars[] = 'gpx_url';
		$vars[] = 'gpx_panel';
		$vars[] = 'geocode';
		//info: Layer vars
		$vars[] = 'layer_id';
		$vars[] = 'layerzoom';
		$vars[] = 'layerviewlat';
		$vars[] = 'layerviewlon';
		$vars[] = 'listmarkers';
		$vars[] = 'multi_layer_map';
		$vars[] = 'multi_layer_map_list';
		$vars[] = 'clustering';
		$vars[] = 'mlm_filter';
		$vars[] = 'mlm_filter_details';
		//info: Bulk markers var
		$vars[] = 'markers_data';
		//info: Bulk layers var
		$vars[] = 'layers_data';
		//info: Update marker vars
		$vars[] = 'id';
		//info: Delete bulk markers vars
		$vars[] = 'markers_ids';
		//info: Delete bulk layers vars
		$vars[] = 'layers_ids';
		//info: Search vars
		$vars[] = 'searchkey';
		$vars[] = 'searchvalue';
		return $vars;
	}

	/**
	 * Returns base URL
	 *
	 * @since  3.0.1
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_base_url() {
		$lmm_options = get_option('leafletmapsmarker_options');
		$url = (isset($lmm_options['rewrite_baseurl']) && !empty($lmm_options['rewrite_baseurl'])) ? trailingslashit(esc_url($lmm_options['rewrite_baseurl'])) : trailingslashit(get_site_url());
		return $url;
	}

	/**
	 * Returns currently set rewrite slug
	 *
	 * @since  3.0
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_slug() {
		$lmm_options = get_option('leafletmapsmarker_options');
		$slug = (isset($lmm_options['rewrite_slug'])) ? esc_html($lmm_options['rewrite_slug']) : 'maps';
		return $slug;
	}

	/**
	 * Sets rewrite rules for API endpoints
	 *
	 * @since  3.0
	 * @access public
	 * @static
	 */
	public static function set_rewrite_rules() {
		add_rewrite_rule(
			'^' . self::get_slug() . '/(geositemap|download|webapi|import-export|proxy|changelog|upload)/?$',
			'index.php?endpoint=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^' . self::get_slug() . '/(fullscreen|geojson|kml|georss|wikitude|qr)/(marker|layer)/(.+)/?',
			'index.php?endpoint=$matches[1]&$matches[2]=$matches[3]',
			'top'
		);
		add_rewrite_rule(
			'^' . self::get_slug() . '/restapi/(.+)/?',
			'index.php?endpoint=restapi&restapi=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^' . self::get_slug() . '/google-places/autocomplete/?([0-9]+)?/?',
			'index.php?mmp_address=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^' . self::get_slug() . '/google-places/details/?([0-9]+)?/?',
			'index.php?mmp_place_id=$matches[1]',
			'top'
		);
	}

	/**
	 * Sets rewrite rules for API endpoints when Polylang is active
	 *
	 * @since  3.0
	 * @access public
	 * @static
	 */
	public static function set_pll_rewrite_rules($wp_rewrite) {
		$rules[self::get_slug() . '/(geositemap|download|webapi|import-export|proxy|changelog|upload)/?$'] =
			'index.php?endpoint=$matches[1]';
		$rules[self::get_slug() . '/(fullscreen|geojson|kml|georss|wikitude|qr)/(marker|layer)/(.+)/?'] =
			'index.php?endpoint=$matches[1]&$matches[2]=$matches[3]';
		$rules[self::get_slug() . '/restapi/(.+)/?'] =
			'index.php?endpoint=restapi&restapi=$matches[1]';
		$rules[self::get_slug() . '/google-places/autocomplete/?([0-9]+)?/?'] =
			'index.php?mmp_address=$matches[1]';
		$rules[self::get_slug() . '/google-places/details/?([0-9]+)?/?'] =
			'index.php?mmp_place_id=$matches[1]';
		$rules = apply_filters('set_pll_rewrite_rules', $rules);
		$wp_rewrite->rules = array_merge($rules, $wp_rewrite->rules);
	}

	/**
	 * Filter for Polylang that adds language slugs to rewrite rules
	 *
	 * @since  3.0
	 * @access public
	 * @static
	 *
	 *@return array
	 */
	public static function pll_filter_rewrite_rules($rules) {
		$rules[] = 'set_pll'; // Name of rewrite filter without '_rewrite_rules'
		return $rules;
	}

	/**
	* Redirects to respective endpoint based on query var
	*
	* @since  3.0
	* @access public
	* @static
	*/
	public static function redirect_to_endpoint() {
		switch (get_query_var('endpoint', false)) {
			case 'fullscreen':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-fullscreen.php';
				exit;
			case 'geojson':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-geojson.php';
				exit;
			case 'kml':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-kml.php';
				exit;
			case 'georss':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-georss.php';
				exit;
			case 'wikitude':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-wikitude.php';
				exit;
			case 'qr':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-qr.php';
				exit;
			case 'geositemap':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-geositemap.php';
				exit;
			case 'download':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-download.php';
				exit;
			case 'webapi':
				require_once LEAFLET_PLUGIN_DIR . 'leaflet-api.php';
				exit;
			case 'import-export':
				require_once LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'import-export' . DIRECTORY_SEPARATOR . 'start.php';
				exit;
			case 'proxy':
				require_once LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'proxy.php';
				exit;
			case 'changelog':
				require_once LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'changelog.php';
				exit;
			case 'upload':
				require_once LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'icon-upload.php';
				exit;
			default:
				break;
		}
	}
}
