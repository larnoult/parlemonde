<?php
/**
 * Class for updating plugin
 */
class CycloneSlider_Updater {

	protected $api_endpoint;
	protected $license;
	protected $secret_key;
	protected $wp_slug;
	protected $version;

	public function __construct( $api_endpoint, $license, $secret, $wp_slug, $version ) {

		$this->api_endpoint = $api_endpoint;
		$this->license = $license;
		$this->secret_key = $secret;
		$this->wp_slug = $wp_slug;
		$this->version = $version;

	}

	public function run() {

		// Uncomment to force check updates
//		delete_option( '_site_transient_update_plugins' ); // Force check

		// Allow testing on localhost. Comment this out on production for security reason!
//		add_filter( 'http_request_host_is_external', '__return_true' );

		// Insert updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		// Define the alternative response for information checking
		add_filter( 'plugins_api', array( $this, 'check_info' ), 10, 3 );

	}

	public function check_update($transient) {

		// Return we already checked
		if (empty($transient->checked)) {
			return $transient;
		}

		// Get the remote plugin info
		$latest_plugin = $this->get_plugin_info();

		// Latest plugin check
		if( $latest_plugin ) {

			// If a newer version is available, add the update info
			if ( version_compare( $this->version, $latest_plugin->version, '<') ) {

				// Prepare needed info for transient using objects
				$obj = new stdClass();

				$obj->slug = $this->derive_simple_slug( $latest_plugin->slug ); // Make update ajax work
				$obj->plugin = $latest_plugin->slug;
				$obj->new_version = $latest_plugin->version;
				$obj->url = $latest_plugin->homepage;
				$obj->package = $latest_plugin->download_link;

				$transient->response[ $obj->plugin ] = $obj;
			}

		}

		return $transient;
	}

	public function check_info($false, $action, $arg) {

		if ( isset($arg->slug) and $arg->slug === $this->derive_simple_slug($this->wp_slug) ) { // Plugin slug format: {folder-name}/{main-file.php}

			// Get the remote plugin info
			$latest_plugin = $this->get_plugin_info();

			// Latest plugin check
			if( $latest_plugin ) {

				// Build needed info
				$information                 = new stdClass();
				$information->name           = $latest_plugin->name;
				$information->slug           = $this->derive_simple_slug( $latest_plugin->slug ); // TODO: Maybe return a simple slug and a plugin slug
				$information->version        = $latest_plugin->version;
				$information->author         = $latest_plugin->author;
				$information->homepage       = $latest_plugin->homepage;
				$information->requires       = $latest_plugin->requires;
				$information->tested         = $latest_plugin->tested;
				$information->last_updated   = $latest_plugin->last_updated;
				$information->sections       = array(
					'description'  => $latest_plugin->description,
					'installation' => $latest_plugin->installation,
					'changelog'    => $this->nice_changelog($latest_plugin->changelog, $latest_plugin->version)
				);
				$information->download_link  = $latest_plugin->download_link;
				$information->banners['low'] = $latest_plugin->banner; // TODO: Banner low and high

				return $information;
			}
		}

		return $false;
	}

	protected function nice_changelog( $changelog, $version ) {
		$changelog_array = preg_split("/\r\n|\n|\r/", $changelog);
		$output = '<h4>'.$version.'</h4>';
		$output .= '<ul>';
		foreach($changelog_array as $log){
			$output .= '<li>'.$log.'</li>';
		}
		return $output.'</ul>';
	}

	protected function get_plugin_info(){
		// Create client
		$restClient = new CycloneSlider_Crispin_RestClientWp(
			$this->license,
			$this->secret_key,
			true
		);

		// Get the remote version
		$query = http_build_query(array(
			'plugin' => $this->derive_simple_slug( $this->wp_slug ),
			'version' => 'latest',
			'action' => 'info'
		));
		$response = $restClient->get($this->api_endpoint.'?'.$query);

		if( is_array($response) and $response['response']['code'] >= 200 and $response['response']['code'] <= 299 ){
			return json_decode($response['body']);
		}
		return '';
	}

	protected function derive_simple_slug( $plugin_wp_slug ){

		$slashPos = strpos($plugin_wp_slug, '/');
		if( false === $slashPos ){ // No slash found, assume php file name. Eg. format: foo-bar.php
			return basename($plugin_wp_slug,'.php'); // Remove .php extension and returns "foo-bar"
		}
		// Slash found, use it as ending position for substr()
		return substr($plugin_wp_slug, 0, $slashPos); // Eg. "plugin-folder/foo-bar.php" returns "plugin-folder"
	}
}