<?php
if(!class_exists('Codefleet_Plugin_Updater')):

	/**
	* Manage updates for self hosted plugins
	*/
	class Codefleet_Plugin_Updater {
		
		// Array containing list of plugins to manage
		protected $plugins;
	
		/**
		* Initialize properties
		*/
		public function __construct() {
			$this->plugins = array();
		}
		
		/**
		* Add plugin to list
		*
		* @param string $slug - Format"plugin-folder/plugin-filename.php"
		* @param string $remote_url - URL for the remote file that returns our plugin info
		* @param string $current_version - Current version of installed plugin
		*/
		public function add_plugin($slug, $remote_url, $current_version){
			$obj = new stdClass();
			$obj->slug = $slug;  
			$obj->remote_url = $remote_url;
			$obj->current_version = $current_version;
			
			$this->plugins[$slug] = $obj;
		}
		
		/**
		* Filter WP data and insert custom data
		*/
		public function check_updates(){
			// Insert custom plugins info to 'update_plugins" site transient
			add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
			
			// Define the alternative response for information checking
			add_filter('plugins_api', array($this, 'check_info'), 10, 3);
		}
		
		/**
		* Check update for each plugin in the list
		*
		* @param object $transient - The site transient containing plugin info
		*/
		public function check_update($transient) {
			
			// Return we already checked
			if (empty($transient->checked)) {
				return $transient;
			}
			
			if(!empty($this->plugins)){
				
				foreach($this->plugins as $plugin_slug=>$plugin){
					
					// Get the remote version
					if( $remote_info = $this->get_remote_plugin_info($plugin->remote_url, $plugin_slug) ) {
					
						// If a newer version is available, add the update info
						if ( version_compare($plugin->current_version, $remote_info['version'], '<') ) {
							
							// Prepare needed info for transient using objects
							$obj = new stdClass();
							
							$obj->slug = $plugin_slug;
							$obj->new_version = $remote_info['version'];
							$obj->url = $remote_info['url'];
							$obj->package = $remote_info['package'];
							
							$transient->response[$plugin_slug] = $obj;
							
						}
						
					}
				}
			}
			
			return $transient;
		}
		
		
	
		/**
		* Add our self-hosted description to the filter
		*/
		public function check_info($false, $action, $arg)
		{
			if(!empty($this->plugins)){
				
				foreach($this->plugins as $plugin_slug=>$plugin){
					if ( isset($arg->slug) and $plugin_slug === $arg->slug) {
						
						// Get the remote version
						$remote_info = $this->get_remote_plugin_info($plugin->remote_url, $plugin_slug);
						
						// Build needed info
						$information = new stdClass();
						$information->slug = $plugin_slug;
						$information->version = $remote_info['version'];
						$information->author = $remote_info['author'];
						$information->homepage = $remote_info['homepage'];
						$information->requires = $remote_info['requires'];  
						$information->tested = $remote_info['tested'];  
						$information->downloaded = $remote_info['downloaded'];  
						$information->last_updated = $remote_info['last_updated'];  
						$information->sections = $remote_info['sections'];
						$information->download_link = $remote_info['package'];
		
						return $information;
					}
				}
				
			}
			return $false;
		}
		
		/**
		* Fetch plugin info from remote url
		*
		* @param url $remote_url - URL to webpage that returns serialize data of plugin info
		* @param string $plugin - Plugin slug in this format: "[plugin-folder/plugin-name.php]"
		*/
		public function get_remote_plugin_info($remote_url, $plugin) {
			$raw_response = wp_remote_post(
				$remote_url,
				array(
					'body' => array(
						'plugin' => $plugin
					)
				)
			);
			
			if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
				return false;
			
			$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
			
			return $response;
		}
	
	}

endif;
