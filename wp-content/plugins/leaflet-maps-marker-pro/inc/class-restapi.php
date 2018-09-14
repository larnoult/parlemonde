<?php
/**
 * Maps Marker Plugin - MMP REST API
 *
 * This class provides a front-facing JSON/XML API that makes it possible to
 * query data from the Maps Marker Pro.
 *
 * @package     MMP
 * @since       2.7
 */
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'class-restapi.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

class MMP_RESTAPI{
	/**
	 * Response data to return
	 *
	 * @var array
	 * @access private
	 * @since 2.7
	 */
	private $data = array();
	/**
	 * Pretty Print?
	 *
	 * @var bool
	 * @access private
	 * @since 2.7
	 */
	private $pretty_print = false;
	/**
	 * Setup the MMP API
	 *
	 * @since 2.7
	 */
	private $action = '';
	private $endpoint = '';
	public function __construct(){
		add_action( 'wp',   array( $this, 'process_query'    ), -1 );
		add_action( 'init',   array( $this, 'process_api_key'    ), -1 );
		add_action( 'admin_notices', array($this, 'show_api_messages'));

		// Determine if JSON_PRETTY_PRINT is available
		$this->pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;
	}
	
	/**
	 * Listens for the API and then processes the API requests
	 *
	 * @access public
	 * @global $wp_query
	 * @since 2.7
	 * @return void
	 */
	public function process_query(){

		global $wp_query;

		// Check for restapi var. Get out if not present
		if ( empty( $wp_query->query_vars['restapi'] ) ) {
			return;
		}

		// Authentication
		$this->authenticate();

		$api_query = $this->get_api_query();

		switch ($api_query['endpoint']) {
			case 'markers':
				$this->data = $this->process_markers (	$api_query['action']	);
				break;
			case 'layers':
				$this->data = $this->process_layers  (	$api_query['action']	);
				break;
		}

		// Send out data to the output function
		$this->output();
	}

	/**
	 * Authenticate the request.
	 *
	 * @access private
	 * @global $wp_query
	 * @since 2.7
	 * @return array response
	 */
	private function authenticate(){
		global $wp_query;

		$public_key = (isset($wp_query->query_vars['public_key']))?$wp_query->query_vars['public_key']:null;
		$secret_token = (isset($wp_query->query_vars['secret_token']))?$wp_query->query_vars['secret_token']:null;

		if(is_null($public_key)){
			$this->data['error'] = __('Authentication failed: REST API public key must be provided','lmm');
			$this->output();
		}
		if(is_null($secret_token)){
			$this->data['error'] = __('Authentication failed: REST API secret token must be provided','lmm');
			$this->output();
		}

		$user = get_users( array(
			'meta_key' => 'leafletmapsmarker_mmp_user_public_key',
			'meta_value' => $public_key,
			'fields'	=> 'ID',
		));
		//info if public key is invalid
		if(empty($user)){
			$this->data['error'] = __('Authentication failed: the REST API keys are not valid','lmm');
			$this->output();
		}

		//info: get the secret key
		$user_id = $user[0];
		$secret_key = get_user_meta($user_id, 'leafletmapsmarker_mmp_user_secret_key', true);

		//info: check the validity of both public key and the token
		if ( !hash_equals( md5( $secret_key . $public_key ), $secret_token ) ) {
			$this->data['error'] = __('Authentication failed: the REST API keys are not valid','lmm');
			$this->output();
		}
	}

	/**
	 * Process all the markers requests
	 *
	 * @access private
	 * @global $wp_query
	 * @since 2.7
	 * @return array response
	 */
	private function process_markers( $action ){
		global $wp_query;

		switch ($action) {
			case 'get':
				$marker_id = (isset($wp_query->query_vars['marker_id']))?$wp_query->query_vars['marker_id']:0;
				$marker = MMPAPI::get_marker( $marker_id );
				if($marker === false){
					$data['error'] = __('Marker does not exist!', 'lmm');
				}else{
					$data = $marker;
				}
				break;
			case 'add':
				$marker_data = $wp_query->query;
				$marker = MMPAPI::add_marker( $marker_data );
				if(is_wp_error($marker)){
					$data['error'] = $marker->get_error_message();
				}
				elseif($marker === false){
					$data['error'] = __('Error: marker could not be added', 'lmm');
				}else{
					$data['marker_id'] = $marker;
				}
				break;
			case 'add_bulk':
				if(isset($_REQUEST['markers_data'])){
					$markers_data = $_REQUEST['markers_data'];
					$markers = MMPAPI::add_markers( $markers_data );
					if(is_wp_error($markers)){
						$data['error'] = $markers->get_error_message();
					}else{
						$data['marker_ids'] = $markers;
					}
				}else{
					$data['error'] = sprintf(__('Error: parameter %1$s is required', 'lmm'), 'markers_data');
				}
				break;
			case 'update':
				$marker_data = $wp_query->query;
				$marker = MMPAPI::update_marker( $marker_data );
				if(is_wp_error($marker)){
					$data['error'] = $marker->get_error_message();
				}
				elseif($marker === false){
					$data['error'] = __('Error: marker could not be updated', 'lmm');
				}else{
					$data['updated'] = true;
				}
				break;
			case 'update_bulk':
				if(isset($_REQUEST['markers_data'])){
					$markers_data = $_REQUEST['markers_data'];
					$markers = MMPAPI::update_markers( $markers_data );

					if(is_wp_error($markers)){
						$data['error'] = $markers->get_error_message();
					}else{
						$output_markers = $markers;
						if($wp_query->query_vars['format'] === 'xml'){
							$output_markers = array();
							foreach ($markers as $key => $value) {
								$output_markers[$key]['@attributes'] = array('marker_id' => $key);
								$output_markers[$key]['@value'] = $value;
							}

						}
						$data['update_results'] = $output_markers;
					}
				}else{
					$data['error'] = sprintf(__('Error: parameter %1$s is required', 'lmm'), 'markers_data');
				}
				break;
			case 'delete':
				$marker_id = (isset($wp_query->query_vars['marker_id']))?$wp_query->query_vars['marker_id']:0;
				$marker = MMPAPI::delete_marker( $marker_id );
				if(is_wp_error($marker)){
					$data['error'] = $marker->get_error_message();
				}else{
					$data['deleted'] = $marker;
				}
				break;
			case 'delete_bulk':
				if(isset($wp_query->query['markers_ids'])){
					$markers_ids = $wp_query->query_vars['markers_ids'];
					$markers = MMPAPI::delete_markers( $markers_ids );
					if(is_wp_error($markers)){
						$data['error'] = $markers->get_error_message();
					}else{
						$data['deleted'] = $markers;
					}
				}else{
					$data['error'] = sprintf(__('Error: parameter %1$s is required', 'lmm'), 'markers_ids');
				}
				break;
			case 'search':
				$search_args = array(
					'searchkey'	=>	(isset($wp_query->query_vars['searchkey']))?$wp_query->query_vars['searchkey']:'',
					'searchvalue'	=>	(isset($wp_query->query_vars['searchvalue']))?$wp_query->query_vars['searchvalue']:'',
				);
				$markers = MMPAPI::search_markers( $search_args );
				if(is_wp_error($markers)){
					$data['error'] = $markers->get_error_message();
				}else{
					$data['results'] = (!empty($markers))?$markers:false;
				}
				break;
			case 'count':
				$data['markers_count'] = MMPAPI::count_markers();
				break;
		}
		return $data;
	}

	/**
	 * Process all the layers requests
	 *
	 * @access private
	 * @global $wp_query
	 * @since 2.7
	 * @return array response
	 */
	private function process_layers( $action ){
		global $wp_query;

		switch ($action) {
			case 'get':
				$layer_id = (isset($wp_query->query_vars['layer_id']))?$wp_query->query_vars['layer_id']:0;
				$layer = MMPAPI::get_layer( $layer_id );
				if($layer === false){
					$data['error'] = __('Error: layer does not exist', 'lmm');
				}else{
					$data = $layer;
				}
				break;
			case 'add':
				$layer_data = $wp_query->query;
				$layer = MMPAPI::add_layer( $layer_data );
				if(is_wp_error($layer)){
					$data['error'] = $layer->get_error_message();
				}
				elseif($layer === false){
					$data['error'] = __('Error: layer could not be added', 'lmm');
				}else{
					$data['layer_id'] = $layer;
				}
				break;
			case 'add_bulk':
				if(isset($_REQUEST['layers_data'])){
					$layers_data = $_REQUEST['layers_data'];
					$layers = MMPAPI::add_layers( $layers_data );
					if(is_wp_error($layers)){
						$data['error'] = $layers->get_error_message();
					}else{
						$data['layer_ids'] = $layers;
					}
				}else{
					$data['error'] = sprintf(__('Error: parameter %1$s is required', 'lmm'), 'layers_data');
				}
				break;
			case 'update':
				$layer_data = $wp_query->query;
				$layer = MMPAPI::update_layer( $layer_data );
				if(is_wp_error($layer)){
					$data['error'] = $layer->get_error_message();
				}
				elseif($layer === false){
					$data['error'] = __('Error: layer could not be updated', 'lmm');
				}else{
					$data['updated'] = true;
				}
				break;
			case 'update_bulk':
				if(isset($_REQUEST['layers_data'])){
					$layers_data = $_REQUEST['layers_data'];
					$layers = MMPAPI::update_layers( $layers_data );
					if(is_wp_error($layers)){
						$data['error'] = $layers->get_error_message();
					}else{
						$data['update_results'] = $layers;
					}
				}else{
					$data['error'] = sprintf(__('Error: parameter %1$s is required', 'lmm'), 'layers_data');
				}
				break;
			case 'delete':
				$layer_id = (isset($wp_query->query_vars['layer_id']))?$wp_query->query_vars['layer_id']:0;
				$layer = MMPAPI::delete_layer( $layer_id );
				if(is_wp_error($layer)){
					$data['error'] = $layer->get_error_message();
				}else{
					$data['deleted'] = $layer;
				}
				break;
			case 'delete_bulk':
				if(isset($wp_query->query['layers_ids'])){
					$layers_ids = $wp_query->query_vars['layers_ids'];
					$layers = MMPAPI::delete_layers( $layers_ids );
					if(is_wp_error($layers)){
						$data['error'] = $layers->get_error_message();
					}else{
						$data['deleted'] = $layers;
					}
				}else{
					$data['error'] = sprintf(__('Error: parameter %1$s is required', 'lmm'), 'layers_ids');
				}
				break;
			case 'search':
				$search_args = array(
					'searchkey'	=>	(isset($wp_query->query_vars['searchkey']))?$wp_query->query_vars['searchkey']:'',
					'searchvalue'	=>	(isset($wp_query->query_vars['searchvalue']))?$wp_query->query_vars['searchvalue']:'',
				);
				$layers = MMPAPI::search_layers( $search_args );
				if(is_wp_error($layers)){
					$data['error'] = $layers->get_error_message();
				}else{
					$data['results'] = (!empty($layers))?$layers:false;
				}
				break;
			case 'count':
				$data['layers_count'] = MMPAPI::count_layers();
				break;
		}
		return $data;
	}


	private function get_api_query(){
		global $wp_query;

		$query = isset( $wp_query->query_vars['restapi'] ) ? $wp_query->query_vars['restapi'] : null;
		if(!is_null($query)){
			$query = explode('/', $query);
			$endpoint 	= (isset($query[0]))?$query[0]:null;
			$action 	= (isset($query[1]))?$query[1]:null;
		}
		$accepted_endpoints = array( 'markers', 'layers' );
		$accepted_actions = array(
							'get',
							'add',
							'add_bulk',
							'update',
							'update_bulk',
							'delete',
							'delete_bulk',
							'search',
							'count',
						);
		// Make sure our query is valid
		if ( ! in_array( $endpoint, $accepted_endpoints ) ) {
			$error['error'] = __('Error: invalid query', 'lmm');
			$this->data = $error;
			$this->output();
		}
		// Make sure our query is valid
		if ( ! in_array( $action, $accepted_actions ) ) {
			$error['error'] = __('Error: invalid query', 'lmm');
			$this->data = $error;
			$this->output();
		}
		$this->action = $action;
		$this->endpoint = $action;
		return array(
			'endpoint'=> $endpoint,
			'action'=>	 $action
		);
	}

	/**
	 * Retrieve the output format
	 *
	 * Determines whether results should be displayed in XML or JSON
	 *
	 * @since 2.7
	 *
	 * @return mixed|void
	 */
	public function get_output_format() {
		global $wp_query;
		$format = isset( $wp_query->query_vars['format'] ) ? $wp_query->query_vars['format'] : 'json';

		return $format;
	}

	/**
	 * Output Query in either JSON/XML. The query data is outputted as JSON
	 * by default
	 *
	 * @since 2.7
	 * @global $wp_query
	 *
	 * @param int $status_code
	 */
	public function output( $status_code = 200 ) {
		global $wp_query;

		$format = $this->get_output_format();

		status_header( $status_code );

		switch ( $format ) :

			case 'xml' :
				require_once LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'array2xml.php';

				$xml = Array2XML::createXML( 'mmp', json_decode(json_encode($this->data), true) );
				echo $xml->saveXML();
				break;
			case 'json' :
				header( 'Content-Type: application/json' );
				if ( ! empty( $this->pretty_print ) )
					echo json_encode( $this->data, $this->pretty_print );
				else
					echo json_encode( $this->data );
				break;
			default:
				header( 'Content-Type: application/json' );
				if ( ! empty( $this->pretty_print ) )
					echo json_encode( $this->data, $this->pretty_print );
				else
					echo json_encode( $this->data );
				break;
			break;
		endswitch;

		exit;
	}

	/**
	 * Process an API key generation/revocation
	 *
	 * @access public
	 * @since 2.7
	 * @param array $args
	 * @return void
	 */
	public function process_api_key(  ) {
		$args = $_REQUEST;
		if(!isset($args['mmp_action'])){
			return;
		}
		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mmp-api-nonce' ) ) {
			wp_die( __('Error: nonce verification failed', 'lmm'), __('Error', 'lmm'), array( 'response' => 403 ) );

		}
		if( is_numeric( $args['user_id'] ) ) {
			$user_id    = isset( $args['user_id'] ) ? absint( $args['user_id'] ) : get_current_user_id();
		} else {
			$userdata   = get_user_by( 'login', $args['user_id'] );
			$user_id    = $userdata->ID;
		}
		$process    = isset( $args['mmp_api_process'] ) ? strtolower( $args['mmp_api_process'] ) : false;

		if( $user_id == get_current_user_id() && ! get_option( 'mmp_allow_user_api_keys' ) && ! current_user_can( 'activate_plugins' ) ) {
			wp_die( sprintf( __('Error: you do not have permission to %s API keys for this user', 'lmm'), $process ), __('Error', 'lmm'), array( 'response' => 403 ) );
		} elseif( ! current_user_can( 'activate_plugins' ) ) {
			wp_die( sprintf( __('Error: you do not have permission to %s API keys for this user', 'lmm'), $process ), __('Error', 'lmm'), array( 'response' => 403 ) );
		}

		switch( $process ) {
			case 'generate':
				if( $this->generate_api_key( $user_id ) ) {
					delete_transient( 'mmp-total-api-keys' );
					wp_redirect( add_query_arg( 'mmp-message', 'api-key-generated', 'admin.php?page=leafletmapsmarker_apis' ) ); exit();
				} else {
					wp_redirect( add_query_arg( 'mmp-message', 'api-key-failed', 'admin.php?page=leafletmapsmarker_apis' ) ); exit();
				}
				break;
			case 'regenerate':
				$this->generate_api_key( $user_id, true );
				delete_transient( 'mmp-total-api-keys' );
				wp_redirect( add_query_arg( 'mmp-message', 'api-key-regenerated', 'admin.php?page=leafletmapsmarker_apis' ) ); exit();
				break;
			case 'revoke':
				$this->revoke_api_key( $user_id );
				delete_transient( 'mmp-total-api-keys' );
				wp_redirect( add_query_arg( 'mmp-message', 'api-key-revoked', 'admin.php?page=leafletmapsmarker_apis' ) ); exit();
				break;
			default;
				break;
		}
	}

	/**
	 * Generate new API keys for a user
	 *
	 * @access public
	 * @since 2.7
	 * @param int $user_id User ID the key is being generated for
	 * @param boolean $regenerate Regenerate the key for the user
	 * @return boolean True if (re)generated succesfully, false otherwise.
	 */
	public function generate_api_key( $user_id = 0, $regenerate = false ) {

		if( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if( ! $user ) {
			return false;
		}
		$public_key = $this->get_user_public_key( $user_id );
		$secret_key = $this->get_user_secret_key( $user_id );

		if ( empty( $public_key ) || $regenerate == true ) {
			$new_public_key = $this->generate_public_key( $user->user_email );
			$new_secret_key = $this->generate_private_key( $user->ID );
		} else {
			return false;
		}

		if ( $regenerate == true ) {
			$this->revoke_api_key( $user->ID );
		}
		update_user_meta( $user_id, 'leafletmapsmarker_mmp_user_public_key', $new_public_key );
		update_user_meta( $user_id, 'leafletmapsmarker_mmp_user_secret_key', $new_secret_key );
		return true;
	}

	/**
	 * Get the user's secret key
	 *
	 * @access private
	 * @since 2.7
	 * @param int user ID
	 * @return string
	 */
	public static function get_user_secret_key( $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return '';
		}

		$cache_key       = md5( 'leafletmapsmarker_mmp_api_user_secret_key' . $user_id );
		$user_secret_key = get_transient( $cache_key );

		if ( empty( $user_secret_key ) ) {
			$user_secret_key = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'leafletmapsmarker_mmp_user_secret_key' AND user_id = %d", $user_id ) );
			set_transient( $cache_key, $user_secret_key, HOUR_IN_SECONDS );
		}

		return $user_secret_key;
	}

	/**
	 * Get the user's public key
	 *
	 * @access private
	 * @since 2.7
	 * @param int user ID
	 * @return string
	 */
	public static function get_user_public_key( $user_id ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return '';
		}

		$cache_key       = md5( 'leafletmapsmarker_mmp_api_user_public_key' . $user_id );
		$user_public_key = get_transient( $cache_key );

		if ( empty( $user_public_key ) ) {
			$user_public_key = get_user_meta($user_id, 'leafletmapsmarker_mmp_user_public_key', true); //$wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'mmp_user_public_key' AND user_id = %d", $user_id ) );
			set_transient( $cache_key, $user_public_key, HOUR_IN_SECONDS );
		}
		return $user_public_key;

	}

	/**
	 * Get the user's token
	 *
	 * @access private
	 * @since 2.7
	 * @param int user ID
	 * @return string
	 */
	public static function get_token( $user_id = 0 ) {
		return hash( 'md5', self::get_user_secret_key( $user_id ) . self::get_user_public_key( $user_id ) );
	}
	/**
	 * Generate the public key for a user
	 *
	 * @access private
	 * @since 2.7
	 * @param string $user_email
	 * @return string
	 */
	public static function generate_public_key( $user_email = '' ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );
		return $public;
	}
	/**
	 * Generate the secret key for a user
	 *
	 * @access private
	 * @since 1.7
	 * @param int $user_id
	 * @return string
	 */
	public static function generate_private_key( $user_id = 0 ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );
		return $secret;
	}

	/**
	 * Revoke a users API keys
	 *
	 * @access public
	 * @since 2.7
	 * @param int $user_id User ID of user to revoke key for
	 * @return string
	 */
	public function revoke_api_key( $user_id = 0 ) {

		if( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if( ! $user ) {
			return false;
		}

		$public_key = $this->get_user_public_key( $user_id );
		$secret_key = $this->get_user_secret_key( $user_id );
		if ( ! empty( $public_key ) ) {
			delete_transient( md5('leafletmapsmarker_mmp_api_user_' . $public_key ) );
			delete_transient( md5('leafletmapsmarker_mmp_api_user_public_key' . $user_id ) );
			delete_transient( md5('leafletmapsmarker_mmp_api_user_secret_key' . $user_id ) );
			delete_user_meta( $user_id, 'leafletmapsmarker_mmp_user_public_key' );
			delete_user_meta( $user_id, 'leafletmapsmarker_mmp_user_secret_key' );
		} else {
			return false;
		}

		return true;
	}

	public function show_api_messages(){
		if ( isset( $_GET['mmp-message'] ) && $_GET['mmp-message'] == 'api-key-generated'  && current_user_can( 'activate_plugins' ) ) {
			add_settings_error( 'mmp-notices', 'mmp-api-key-generated', __('API keys have been successfully generated.', 'lmm'), 'updated' );
		}

		if ( isset( $_GET['mmp-message'] ) && $_GET['mmp-message'] == 'api-key-failed' && current_user_can( 'activate_plugins' ) ) {
			add_settings_error( 'mmp-notices', 'mmp-api-key-failed', __('The specified user already has API keys or the specified user does not exist.', 'lmm'), 'error' );
		}

		if ( isset( $_GET['mmp-message'] ) &&  $_GET['mmp-message'] == 'api-key-regenerated' && current_user_can( 'activate_plugins' ) ) {
			add_settings_error( 'mmp-notices', 'mmp-api-key-regenerated', __('API keys have been successfully regenerated.', 'lmm'), 'updated' );
		}

		if ( isset( $_GET['mmp-message'] ) &&  $_GET['mmp-message'] == 'api-key-revoked' && current_user_can( 'activate_plugins' ) ) {
			add_settings_error( 'mmp-notices', 'mmp-api-key-revoked', __('API keys have been successfully revoked.', 'lmm'), 'updated' );
		}
	}
}
$_GLOBALS['mmp_restapi'] =	new MMP_RESTAPI();
