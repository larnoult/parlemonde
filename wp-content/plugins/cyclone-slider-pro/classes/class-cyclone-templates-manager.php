<?php
if(!class_exists('Cyclone_Templates_Manager')):
    
    /**
    * In charge in getting templates from various template locations.
    */
    class Cyclone_Templates_Manager {
        
        protected $template_locations;
        
        /**
         * Initializes class
         */
        public function __construct() {
            $this->template_locations = array();
        }
        
        /**
         * Add a directory to read templates from
         *
         * @param string $location - The full path to a directory
         */
        public function add_template_location( $location ){
            $this->template_locations[] = $location;
        }
        
        /**
         * Get all templates in array format
         */
        public function get_all_templates(){
            if(is_array($this->template_locations) and !empty($this->template_locations)){
                $template_folders = array();
                foreach($this->template_locations as $location){
                    if($files = @scandir($location['path'])){
                        $c = 0;
                        foreach($files as $name){
                            if($name!='.' and $name!='..' and is_dir($location['path'].$name) and @file_exists($location['path'].$name.DIRECTORY_SEPARATOR.'slider.php') ){ // Check if its a directory
                                $ini_array['slide_type'] = array('image');// Default
                                if(@file_exists($location['path'].$name.DIRECTORY_SEPARATOR.'config.txt')){
                                    $ini_array = parse_ini_file($location['path'].$name.DIRECTORY_SEPARATOR.'config.txt'); //Parse ini to get slide types supported
                                }
								
                                $name = sanitize_title($name);// Change space to dash and all lowercase
                                $template_folders[$name] = array( // Here we override template of the same names. If there is a template with the same name in plugin and theme directory, the one in theme will take over
                                    'path'=>$location['path'].$name,
                                    'url'=>$location['url'].$name,
                                    'supports' => $ini_array['slide_type'],
									'location_name' => $location['location_name']
                                );
                            }
                        }
                    }
                }
                return $template_folders;
            }
            
        }
        
        /**
         * Get Active Templates
         *
         * Get templates that are enabled in settings page
         *
         * @return array Template locations
         */
        public function get_active_templates( $settings_data ){
            
			$templates = $this->get_all_templates();

			foreach($templates as $name=>$template){
				
				if( !isset($settings_data['load_templates'][$name]) ){
					$settings_data['load_templates'][$name] = 1;
				}
			}
			return $settings_data['load_templates'];
        }
        
        /**
         * Get Template Locations
         *
         * @return array Template locations
         */
        public function get_template_locations(){
            return $this->template_locations;
        }
    }
    
endif;