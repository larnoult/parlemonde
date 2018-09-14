<?php
 
/**
* @since 2.8.0
* A local endpoint to handle Google Places REST API
*/

class Google_Places_API_Endpoint{

	protected $autocomplete_endpoint = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input={query}';
	protected $details_endpoint = 'https://maps.googleapis.com/maps/api/place/details/json?placeid={place_id}';
	protected $address = '';
	protected $place_id = '';
	protected $lmm_options = array();

	/** Hook WordPress
	*	@return void
	*/
	public function __construct(){
		add_filter('query_vars', array($this, 'add_query_vars'), 0);
		add_action('parse_request', array($this, 'sniff_requests'), 0);
		add_action('init', array($this, 'add_endpoint'), 0);
	}	
	
	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars 
	*/
	public function add_query_vars($vars){
		$vars[] = 'mmp_address';
		$vars[] = 'mmp_place_id';
		$vars[] = '_wpnonce';
		return $vars;
	}
	
	/** Add API Endpoint
	*	This is where the magic happens - brush up on your regex skillz
	*	@return void
	*/
	public function add_endpoint(){
		add_rewrite_rule('^mmp-api/google_places/autocomplete/?([0-9]+)?/?','index.php?mmp_address=$matches[1]','top');
		add_rewrite_rule('^mmp-api/google_places/details/?([0-9]+)?/?','index.php?mmp_place_id=$matches[1]','top');
	}
	/**	Sniff Requests
	*	This is where we hijack all API requests
	*	@return die if API request
	*/
	public function sniff_requests(){
		global $wp;
		if(isset($wp->query_vars['mmp_address']) && trim($wp->query_vars['mmp_address']) != ''){
			$this->handle_request('autocomplete');
			exit;
		}
		if(isset($wp->query_vars['mmp_place_id']) && trim($wp->query_vars['mmp_place_id']) != ''){
			$this->handle_request('details');
			exit;
		}

	}
	
	/** Handle Requests
	*	@return void 
	*/
	protected function handle_request( $type ){
		global $wp;
		$_wpnonce = (isset($wp->query_vars['_wpnonce']))?$wp->query_vars['_wpnonce']:'';
		if(!wp_verify_nonce($_wpnonce, 'google-places-endpoint-nonce')){
			$this->send_response('Nonce validation was not successful.');
			exit;
		}

		$this->lmm_options = get_option( 'leafletmapsmarker_options' );

		if($type == 'autocomplete'){
			$this->address = $wp->query_vars['mmp_address'];
			if(!$this->address)
				$this->send_response('Please tell us the address.');
			$url = $this->prepare_api_url('autocomplete');
			$request_autocomplete = wp_remote_get($url, array('sslverify' => false, 'timeout' => 10));
			$request_autocomplete = json_decode($request_autocomplete['body'], true);
			$response = array();
			if ($request_autocomplete['status'] == 'OK') {
				foreach($request_autocomplete['predictions'] as $prediction){
					$data = array();
					$data['formatted_address'] = $prediction['description'];
					$data['place_id'] = $prediction['place_id'];
					$data['types'] = $prediction['types'];
					array_push($response, $data);
				}
				$this->send_response('OK', $response);
			} else if ($request_autocomplete['status'] == 'ZERO_RESULTS') {
				$response = array();
				$data['formatted_address'] = '';
				$data['place_id'] = '';
				$data['types'] = '';
				$this->send_response('ZERO_RESULTS', $response);
			} else { //info: custom error handling for geocoding.js
				$data['status'] = $request_autocomplete['status'];
				$data['error_message'] = $request_autocomplete['error_message'];
				array_push($response, $data);
				$this->send_response('GOOGLE-ERROR', $data);
			}
		}elseif($type == 'details'){
			$this->place_id = $wp->query_vars['mmp_place_id'];
			if(!$this->place_id)
				$this->send_response('Please tell us the place_id');
			$url = $this->prepare_api_url('details');
			$request_details_autocomplete = wp_remote_get($url, array('sslverify' => false, 'timeout' => 10));
			$request_details_autocomplete = json_decode($request_details_autocomplete['body'], true);
			if($request_details_autocomplete['status'] == 'OK'){
				$data = array();
				$data['formatted_address'] = $request_details_autocomplete['result']['formatted_address'];
				$data['geometry']['location']['lat'] = $request_details_autocomplete['result']['geometry']['location']['lat'];
				$data['geometry']['location']['lng'] = $request_details_autocomplete['result']['geometry']['location']['lng'];
				$data['types'] = $request_details_autocomplete['result']['types'];
				$this->send_response('OK', $data);
			} else { //info: custom error handling for geocoding.js
				$data['status'] = $request_autocomplete['status'];
				$data['error_message'] = $request_autocomplete['error_message'];
				array_push($response, $data);
				$this->send_response('GOOGLE-ERROR', $data);
			}
		}
	}
	
	/** Response Handler
	*	This sends a JSON response to the browser
	*/
	protected function send_response($msg, $data = ''){
		$response['status'] = $msg;
		if($data)
			$response['results'] = $data;
		header('content-type: application/json; charset=utf-8');
	    echo json_encode($response)."\n";
	    exit;
	}

	protected function prepare_api_url( $type ){
		global $locale;
		if($type == 'autocomplete'){
			$url = str_replace('{query}',  urlencode($this->address), $this->autocomplete_endpoint);
		}else if($type == 'details'){
			$url = str_replace('{place_id}',  urlencode($this->place_id), $this->details_endpoint);
		}

		if ($this->lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') {
			$url = $url . '&key=' . $this->lmm_options['geocoding_google_geocoding_api_key'];
		}elseif ($this->lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') {
			$gmapsbusiness_client = '&client=' . urlencode(trim($this->lmm_options["geocoding_google_geocoding_premium_client"]));
			$gmapsbusiness_signature = '&signature=' . urlencode(trim($this->lmm_options["geocoding_google_geocoding_premium_signature"]));
			$gmapsbusiness_channel = '&channel=' . urlencode(trim($this->lmm_options["geocoding_google_geocoding_premium_channel"]));
			$url = $url . $gmapsbusiness_client . $gmapsbusiness_signature . $gmapsbusiness_channel;
		}
		if(trim($this->lmm_options['geocoding_google_geocoding_location']) != ''){
			$url .= '&location=' . trim($this->lmm_options['geocoding_google_geocoding_location']);
		}
		if(trim($this->lmm_options['geocoding_google_geocoding_radius']) != ''){
			$url .= '&radius=' . trim($this->lmm_options['geocoding_google_geocoding_radius']);
		}
		//info: Google language localization
		if ( isset($this->lmm_options["geocoding_google_geocoding_language"]) && ($this->lmm_options["geocoding_google_geocoding_language"]!="") ) {
			$google_language = esc_js(trim($this->lmm_options["geocoding_google_geocoding_language"]));
		} else if ( ($this->lmm_options['google_maps_language_localization'] != 'browser_setting') && ($this->lmm_options['google_maps_language_localization'] != 'wordpress_setting') ) {
			//info: if custom language is used for maps, use it for geocoding too
			$google_language = $this->lmm_options['google_maps_language_localization'];
		} else if ( $locale != NULL ) {
			$google_language = substr($locale, 0, 2);
		} else {
			$google_language =  ''; //info: language retrieved by Google based on server IP
		}
		if($google_language != ''){
			$url .= '&language=' . $google_language;
		}
		if(trim($this->lmm_options['geocoding_min_chars_search_autostart']) != ''){
			$url .= '&offset=' . trim($this->lmm_options['geocoding_min_chars_search_autostart']);
		}		
		if(trim($this->lmm_options['geocoding_google_geocoding_region']) != ''){
			$url .= '&region=' . trim($this->lmm_options['geocoding_google_geocoding_region']);
		}
		if(trim($this->lmm_options['geocoding_google_geocoding_components']) != ''){
			$url .= '&components=' . trim($this->lmm_options['geocoding_google_geocoding_components']);
		}
		return $url;
	}
}

add_action('init', 'lmm_initiate_google_places_endpoint');
function lmm_initiate_google_places_endpoint(){
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	if 
	(
		(isset($lmm_options['capabilities_edit'])) //info: to prevent PHP warnings - different from pro! capabilities_view_others
		&&
		(
		(current_user_can('activate_plugins'))
		|| 
			(
				(isset($lmm_options['capabilities_edit'])) //info: different from pro! capabilities_view_others
				&& 
				(current_user_can($lmm_options[ 'capabilities_edit' ])) //info: different from pro! capabilities_view_others
			)
		)
	)
	{
		if 
			(
				(isset($lmm_options['geocoding_google_geocoding_auth_method']))
				&&
				(
					(
						($lmm_options['geocoding_google_geocoding_auth_method'] == 'api-key') 
						&& 
						($lmm_options['geocoding_google_geocoding_api_key'] != NULL)
					)
					|| 
					(
						($lmm_options['geocoding_google_geocoding_auth_method'] == 'clientid-signature') 
						&& 
						(
							($lmm_options['geocoding_google_geocoding_premium_client'] != NULL) 
							&& 
							($lmm_options['geocoding_google_geocoding_premium_signature'] != NULL)
						) 
					)
				)
			)
		{
		new Google_Places_API_Endpoint();
		}
	}
}