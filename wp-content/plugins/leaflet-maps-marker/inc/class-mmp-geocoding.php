<?php
/**
* Handling all Geocoding functionality.
* @since 2.8.0
*
*/
if (basename($_SERVER['SCRIPT_FILENAME']) == 'class-mmp-geocoding.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'globals.php' );
require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR . 'Throttle' . DIRECTORY_SEPARATOR . 'ThrottleInterface.php' );
require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR . 'Throttle' . DIRECTORY_SEPARATOR . 'LeakyBucket.php' );
require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR. 'Storage' . DIRECTORY_SEPARATOR . 'StorageInterface.php' );
require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . 'LockWaitTimeoutException.php' );
if ( function_exists('apc_store') && (apc_sma_info() !== FALSE) ) { //info: initialize APC storage
    require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . 'Apc.php' );
} else if ( function_exists('apcu_store') && (apcu_sma_info() !== FALSE) ) { //info: initialize APCu storage
    require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . 'Apcu.php' );
} else { //info: use WordPress session storage
    require_once( LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle' . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . 'Session.php' );
}

class MMP_Geocoding{
	/**
	* Geocode an address
	*
	* @param object $row The object of the marker
	* @return latitude+longitude+address value
	* @used in: leaflet-api.php, /inc/import-export/start.php, class-mmpapi.php
	*/
	public static function getLatLng( $address, $provider = ''){
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		$address_to_geocode = lmm_accent_folding($address);
		$provider = ($provider != '')? $provider: $lmm_options['geocoding_provider'];
        $throttle = new LMM_LeakyBucket();

		switch ( $provider ) {
			case 'mapzen-search':
				$throttle->throttle($provider, 6, 1000);
				return self::mapzen_geocode( $address_to_geocode );
				break;
			case 'algolia-places':
				$throttle->throttle($provider, 15, 1000);
				return self::algolia_geocode( $address_to_geocode );
				break;
			case 'photon':
				$throttle->throttle($provider, 10, 1000);
				return self::photon_geocode( $address_to_geocode );
				break;
			case 'mapquest-geocoding':
				$throttle->throttle($provider, 10, 1000);
				return self::mapquest_geocode( $address_to_geocode );
				break;
			case 'google-geocoding':
				$throttle->throttle($provider, 50, 1000);
				return self::google_geocode( $address_to_geocode );
				break;
			default:
				$throttle->throttle('algolia-places', 15, 1000);
				return self::mapzen_geocode( $address_to_geocode );
				break;
		}
	}

    /**
     * Geocode an address by Mapzen Search
     *
     * @param string $address The address text to geocode
     * @return latitude+longitude+address value
     */
    public static function mapzen_geocode( $address_to_geocode ){
        global $locale;
        $lmm_options = get_option( 'leafletmapsmarker_options' );
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

        $parameters = array(
            'text'				=>	$address_to_geocode,
            'size'				=>	1,
        );
        if(isset($lmm_options["geocoding_mapzen_search_api_key"])){
            $parameters['api_key'] = esc_js(trim($lmm_options["geocoding_mapzen_search_api_key"]));
        }
        if(isset($lmm_options["geocoding_mapzen_search_focus_lat"]) && $lmm_options["geocoding_mapzen_search_focus_lat"]!=''){
            $parameters['focus.point.lat'] = str_replace(',', '.',floatval($lmm_options["geocoding_mapzen_search_focus_lat"]));
        }
        if(isset($lmm_options["geocoding_mapzen_search_focus_lon"]) && $lmm_options["geocoding_mapzen_search_focus_lon"]!=''){
            $parameters['focus.point.lon'] = floatval($lmm_options["geocoding_mapzen_search_focus_lon"]);
        }
        if($mapzen_sources){
            $parameters['sources'] = $mapzen_sources;
        }
        if(isset($lmm_options["geocoding_mapzen_search_layer"]) && $lmm_options["geocoding_mapzen_search_layer"]!="none"){
            $parameters['layers'] = $lmm_options["geocoding_mapzen_search_layer"];
        }
        if($lmm_options['geocoding_mapzen_search_narrow_search'] == 'rectangle'){
            $parameters['boundary.rect.min_lat'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapzen_search_narrow_rect_lat_min']));
            $parameters['boundary.rect.min_lon'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapzen_search_narrow_rect_lon_min']));
            $parameters['boundary.rect.max_lat'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapzen_search_narrow_rect_lat_max']));
            $parameters['boundary.rect.max_lon'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapzen_search_narrow_rect_lon_max']));
        }elseif ($lmm_options['geocoding_mapzen_search_narrow_search'] == 'circle') {
            $parameters['boundary.circle.lat'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapzen_search_narrow_circle_lat']));
            $parameters['boundary.circle.lon'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapzen_search_narrow_circle_lon']));
            $parameters['boundary.circle.radius'] = floatval($lmm_options['geocoding_mapzen_search_narrow_circle_radius']);
        }
        $api_url = 	'https://search.mapzen.com/v1/search?'. http_build_query($parameters);
        $response = wp_remote_get($api_url, array('sslverify' => false, 'timeout' => 10));
        //info: If request successed the response must be an array and the response status 200
        if( is_array($response) && $response['response']['code'] == 200) {
            $response_body = json_decode($response['body'], true);
            if(isset($response_body['features'])){
                $rate_limit = (isset($lmm_options['geocoding_mapzen_search_api_key']) && $lmm_options['geocoding_mapzen_search_api_key'] != '')?30000:1000;
                $rate_limit_left = (isset($response['headers']['x-apiaxleproxy-qpd-left']))?sprintf(__('Rate Limit: %1$s out of %2$s/day', 'lmm'), $rate_limit - $response['headers']['x-apiaxleproxy-qpd-left'], $rate_limit): sprintf(__('Rate Limit: %s/day', 'lmm'), $rate_limit);
                return array(
                    'success'		=> true,
                    'lat'			=> $response_body['features'][0]['geometry']['coordinates'][1],
                    'lon'			=> $response_body['features'][0]['geometry']['coordinates'][0],
                    'address'		=> self::format_address('mapzen', $response_body['features'][0]),
                    'rate_limit'	=> $rate_limit_left
                );
            }
        }else{
            //info: if the request failed due network errors.
            if(is_wp_error($response)){
                return array(
                    'success'	=>	false,
                    'message'	=> $response->get_error_message()
                );
                //info: if the request failed due algolia API endpoint response.
            }else{
                $response_body = json_decode($response['body'], true);
                return array(
                    'success'	=>	false,
                    'message'	=> (isset($response_body['geocoding']['errors'][0]))?$response_body['geocoding']['errors'][0]:''
                );
            }
        }
    }

    /**
     * Geocode an address by Algolia Places
     *
     * @param string $address The address text to geocode
     * @return latitude+longitude+address value
     */
    public static function algolia_geocode( $address_to_geocode ){
        global $locale;
        $lmm_options = get_option( 'leafletmapsmarker_options' );
        if (isset($lmm_options["geocoding_algolia_language"]) && $lmm_options["geocoding_algolia_language"]!="") {
            $algolia_language = esc_js(trim($lmm_options["geocoding_algolia_language"]));
        } else {
            $algolia_language = substr($locale, 0, 2);
        }

        $parameters = array(
            'query'				=>	$address_to_geocode,
            'language'			=>	$algolia_language,
            'countries'			=>	(isset($lmm_options["geocoding_algolia_countries"]))?esc_js(trim($lmm_options["geocoding_algolia_countries"])):"",
            'aroundLatLngViaIP'	=> 	(isset($lmm_options["geocoding_algolia_aroundLatLngViaIP"]))?$lmm_options["geocoding_algolia_aroundLatLngViaIP"]:"true",
            'aroundLatLng'		=> 	(isset($lmm_options["geocoding_algolia_aroundLatLng"]))?esc_js(trim($lmm_options["geocoding_algolia_aroundLatLng"])):"",
        );
        $header = array(
            'X-Algolia-Application-Id'	=>	(isset($lmm_options["geocoding_algolia_appId"]))?esc_js(trim($lmm_options["geocoding_algolia_appId"])):"",
            'X-Algolia-API-Key'	=>	(isset($lmm_options["geocoding_algolia_apiKey"]))?esc_js(trim($lmm_options["geocoding_algolia_apiKey"])):"",
        );
        $api_url = 	'https://places-dsn.algolia.net/1/places/query?'. http_build_query($parameters) . '&hitsPerPage=1';
        $response = wp_remote_get($api_url, array('sslverify' => false, 'timeout' => 10, 'headers'	=>	$header));
        //info: If request successed the response must be an array and the response status 200
        if( is_array($response) && $response['response']['code'] == 200) {
            $response_body = json_decode($response['body'], true);
            if(isset($response_body['nbHits']) && $response_body['nbHits'] > 0){
                return array(
                    'success'	=> true,
                    'lat'	=> $response_body['hits'][0]['_geoloc']['lat'],
                    'lon'	=> $response_body['hits'][0]['_geoloc']['lng'],
                    'address'	=>	self::format_address('algolia', $response_body['hits'][0]),
                    'rate_limit'	=> sprintf(__('Rate Limit: %s/day', 'lmm'), 1000)
                );
            }
        }else{
            //info: if the request failed due network errors.
            if(is_wp_error($response)){
                return array(
                    'success'	=>	false,
                    'message'	=> $response->get_error_message()
                );
                //info: if the request failed due algolia API endpoint response.
            }else{
                $response_body = json_decode($response['body'], true);
                return array(
                    'success'	=>	false,
                    'message'	=> $response_body['message']
                );
            }
        }
    }

    /**
     * Geocode an address by Photon@MapsMarker
     *
     * @param string $address The address text to geocode
     * @return latitude+longitude+address value
     */
    public static function photon_geocode( $address_to_geocode ){
        global $locale;
        $lmm_options = get_option( 'leafletmapsmarker_options' );
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
        $parameters = array(
            'q'			=>	$address_to_geocode,
            'limit'		=>	1,
            'lang'		=>	$photon_language,
            'lat'		=>	(isset($lmm_options["geocoding_photon_location_bias_lat"]) && $lmm_options["geocoding_photon_location_bias_lat"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_photon_location_bias_lat"])):"",
            'lon'		=> 	(isset($lmm_options["geocoding_photon_location_bias_lon"]) && $lmm_options["geocoding_photon_location_bias_lon"]!="")?str_replace(',', '.', floatval($lmm_options["geocoding_photon_location_bias_lon"])):"",
            'osm_tag'	=> 	(isset($lmm_options["geocoding_photon_filter_results"]) && $lmm_options["geocoding_photon_filter_results"]!="")?esc_js(trim($lmm_options["geocoding_photon_filter_results"])):"",
        );

        $api_url = 	'https://photon.mapsmarker.com/pro/api?'. http_build_query($parameters);
        $response = wp_remote_get($api_url, array('sslverify' => false, 'timeout' => 10));
        //info: If request successed the response must be an array and the response status 200
        if( is_array($response) && $response['response']['code'] == 200) {
            $response_body = json_decode($response['body'], true);
            if(isset($response_body['features'][0]['geometry']['coordinates'][1])){ //info: as empty 'features' is returned on no results
                return array(
                    'success'		=> true,
                    'lat'			=> $response_body['features'][0]['geometry']['coordinates'][1],
                    'lon'			=> $response_body['features'][0]['geometry']['coordinates'][0],
                    'address'		=> self::format_address('photon', $response_body['features'][0]),
                    'rate_limit'	=> sprintf(__('Rate Limit: %1$s out of %2$s/day', 'lmm'), $response['headers']['x-ratelimit-remaining-day'], $response['headers']['x-ratelimit-limit-day'])
                );
            }
        }else{
            //info: if the request failed due network errors.
            if(is_wp_error($response)){
                return array(
                    'success'	=>	false,
                    'message'	=> $response->get_error_message()
                );
                //info: if the request failed due algolia API endpoint response.
            }else{
                $response_body = json_decode($response['body'], true);
                //info: API limit exceeds
               	if($response['response']['code'] == 429){ //info: prepared for custom message
					return array(
                   		'success'	=>	false,
                    	'message'	=> $response_body['message']
                	);
               	}else{
               		return array(
                   		'success'	=>	false,
                    	'message'	=> $response_body['message']
                	);
               	}
            }
        }
    }

    /**
     * Geocode an address by MapQuest Search
     *
     * @param string $address The address text to geocode
     * @return latitude+longitude+address value
     */
    public static function mapquest_geocode( $address_to_geocode ){
        global $locale;
        $lmm_options = get_option( 'leafletmapsmarker_options' );
        $parameters = array(
            'location'			=>	$address_to_geocode,
            'maxResults'		=>	1,
        );
        if($lmm_options['geocoding_mapquest_geocoding_bounds_status'] == 'enabled'){
            $parameters['boundingBox'] = str_replace(',', '.', floatval($lmm_options['geocoding_mapquest_geocoding_bounds_lat1'])).','.str_replace(',', '.', floatval($lmm_options['geocoding_mapquest_geocoding_bounds_lon1'])).','.str_replace(',', '.', floatval($lmm_options['geocoding_mapquest_geocoding_bounds_lat2'])).','.str_replace(',', '.', floatval($lmm_options['geocoding_mapquest_geocoding_bounds_lon2']));
        }
        if(isset($lmm_options['geocoding_mapquest_geocoding_api_key']) && $lmm_options['geocoding_mapquest_geocoding_api_key']!=''){
            $parameters['key'] = $lmm_options['geocoding_mapquest_geocoding_api_key'];
        }
        $api_url = 	'https://www.mapquestapi.com/geocoding/v1/address?'. http_build_query($parameters);
        $response = wp_remote_get($api_url, array('sslverify' => false, 'timeout' => 10));
        $response_body = json_decode($response['body'], true);
        //info: If request successed the response must be an array and the response status 200
        if( is_array($response) && $response_body['info']['statuscode'] === 0) {
            if(isset($response_body['results'])){
                return array(
                    'success'		=> true,
                    'lat'			=> $response_body['results'][0]['locations'][0]['latLng']['lat'],
                    'lon'			=> $response_body['results'][0]['locations'][0]['latLng']['lng'],
                    'address'		=> self::format_address('mapquest', $response_body['results'][0]['locations'][0]),
                    'rate_limit'	=> sprintf(__('Rate Limit: %s/month', 'lmm'), 15000)
                );
            }
        }else{
            //info: if the request failed due network errors.
            if(is_wp_error($response)){
                return array(
                    'success'	=>	false,
                    'message'	=> $response->get_error_message()
                );
            //info: if the request failed due MapQuest Geocoding API endpoint response.
            }else{
                return array(
                    'success'	=>	false,
                    'message'   =>  wp_remote_retrieve_body($response)
                );
            }
        }
    }

	/**
	* Geocode an address by Google Geocoding
	*
	* @param string $address The address text to geocode
	* @return latitude+longitude+address value
	*/
	public static function google_geocode( $address_to_geocode ){
		global $locale;
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		//info: Google Maps API key parameters
		if ($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') {
			$google_api_key = '&key=' . trim($lmm_options['geocoding_google_geocoding_api_key']);
			$gmapsbusiness_client = '';
			$gmapsbusiness_signature = '';
			$gmapsbusiness_channel = '';
		} else if ($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') {
			$google_api_key = '';
			$gmapsbusiness_client = '&client=' . urlencode(trim($lmm_options["geocoding_google_geocoding_premium_client"]));
			$gmapsbusiness_signature = '&signature=' . urlencode(trim($lmm_options["geocoding_google_geocoding_premium_signature"]));
			$gmapsbusiness_channel = '&channel=' . urlencode(trim($lmm_options["geocoding_google_geocoding_premium_channel"]));
		}
		$url = 'https://maps.googleapis.com/maps/api/geocode/xml?address=' . urlencode($address_to_geocode) . $google_api_key . $gmapsbusiness_client . $gmapsbusiness_signature . $gmapsbusiness_channel;
		$xml_raw = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 10 ) );
		$xml = simplexml_load_string($xml_raw['body']);

		$response = array();
		$statusCode = $xml->status;
		$error_message = $xml->error_message;

		if ( ($statusCode != false) && ($statusCode != NULL) ) {
			if ($statusCode == 'OK') {
				$latDom = $xml->result[0]->geometry->location->lat;
				$lonDom = $xml->result[0]->geometry->location->lng;
				$addressDom = $xml->result[0]->formatted_address;
				if ($latDom != NULL) {
					$response = array (
						'success' 	=> true,
						'lat' 		=> $latDom,
						'lon' 		=> $lonDom,
						'address'	=> $addressDom,
  						'rate_limit'	=> sprintf(__('Rate Limit: %s/day', 'lmm'), 2500)
					);
					return $response;
				}
			} else if($statusCode == 'OVER_QUERY_LIMIT'){
                $response = array (
                    'success' => false,
                    'message' => $statusCode . ' - ' . $error_message
                );
                return $response;
            } else if($statusCode == 'REQUEST_DENIED'){
                $response = array (
                    'success' => false,
                    'message' => $statusCode . ' - ' . $error_message
                );
                return $response;
            } else if($statusCode == 'INVALID_REQUEST'){
                $response = array (
                    'success' => false,
                    'message' => $statusCode . ' - ' . $error_message
                );
                return $response;
            } else if($statusCode == 'UNKNOWN_ERROR'){
                $response = array (
                    'success' => false,
                    'message' => $statusCode . ' - ' . $error_message
                );
                return $response;
            } else {
                $response = array (
                    'success' => false,
                    'message' => $statusCode . ' - ' . $error_message
                );
                return $response;
            }
		}
		$response = array (
			'success' => false,
			'message' => $statusCode . ' - ' . $error_message
		);
		return $response;
	}

	/**
	* Format the address which is returned from Geocoding providers
	*
	* @param string $provider The provider name
	* @param array $response_object The response data
	* @return string formatted address
	*/
	public static function format_address( $provider, $response_object ){
		global $locale;
		$lmm_options = get_option( 'leafletmapsmarker_options' );

		switch ($provider) {

			case 'algolia':
				if (isset($lmm_options["geocoding_algolia_language"]) && $lmm_options["geocoding_algolia_language"]!="") {
					$language = esc_js(trim($lmm_options["geocoding_algolia_language"]));
				} else {
					$language = substr($locale, 0, 2);
				}
				$administrative = $response_object['administrative'];
		        $city = $response_object['city'];
		        $country = $response_object['country'];
		        $hit = $response_object;
		        if(isset($hit['_highlightResult']['locale_names'][0])){
		        	$name = $hit['_highlightResult']['locale_names'][0]['value'].',';
		        }elseif(isset($hit['_highlightResult']['locale_names'][$language][0])){
		        	$name = $hit['_highlightResult']['locale_names'][$language][0]['value'].',';
		        }else{
		        	$name = '';
		        }
		        $city = ($city) ? $hit['_highlightResult']['city'][0]['value'] : null;
		        $administrative = ($administrative && isset($hit['_highlightResult']['administrative']))? $hit['_highlightResult']['administrative'][0]['value'] : null;
		        $country = ($country)? $hit['_highlightResult']['country']['value'] : null;
		        return strip_tags($name) .' '. ($administrative ? $administrative . ',' : '') . ' ' . ($country ? '' . $country : '');
				break;

			case 'photon':
			case 'mapzen':
				$country 	= (isset($response_object['properties']['country']))?$response_object['properties']['country']:null;
				$city 		= (isset($response_object['properties']['city']))?$response_object['properties']['city']:null;
				$postcode 	= (isset($response_object['properties']['postcode']))?$response_object['properties']['postcode']:null;
				$state 		= (isset($response_object['properties']['state']))?$response_object['properties']['state']:null;
				$name 		= (isset($response_object['properties']['name']))?$response_object['properties']['name'].',':null;
				$address 	= $name .' '. ($state ? $state . ', ' : '') . ($country ? '' . $country : '');
				return $address;
				break;
			case 'mapquest':
				$address = '';
				$address .= (isset($response_object['adminArea5']) && $response_object['adminArea5'] != '')?$response_object['adminArea5'].', ':'';
				$address .= (isset($response_object['adminArea4']) && $response_object['adminArea4'] != '')?$response_object['adminArea4'].', ':'';
				$address .= (isset($response_object['adminArea3']) && $response_object['adminArea3'] != '')?$response_object['adminArea3'].', ':'';
				$address .= (isset($response_object['adminArea2']) && $response_object['adminArea2'] != '')?$response_object['adminArea2'].', ':'';
				$address .= (isset($response_object['adminArea1']) && $response_object['adminArea1'] != '')?$response_object['adminArea1']:'';
				return $address;
				break;
			default:
				return '';
				break;
		}
	}
}