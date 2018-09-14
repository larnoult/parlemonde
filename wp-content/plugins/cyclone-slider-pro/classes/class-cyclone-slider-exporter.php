<?php
if(!class_exists('Cyclone_Slider_Exporter')):
    
    /**
    * Class for exporting cyclone-slider.zip
    */
    class Cyclone_Slider_Exporter {
        protected $log_results; // Hold results of import operations
        protected $log_count;
        protected $cyclone_slider_data;
        
        public function __construct( $cyclone_slider_data ) {
            $this->log_results = array(
                'oks'=>array(),
                'errors'=>array()
            );
            $this->log_count = 0;
            $this->cyclone_slider_data = $cyclone_slider_data;
        }
        
        /**
        * Get log results
        *
        */
        public function get_results(){
            return $this->log_results;
        }

        /**
        * Export
        *
        * Export main operation
        *
        * @param string $zip_file Full path and filename to zip file
        * @param array $sliders_slugs_array Array of slider slugs to export
        * @return void|false Do export or abort and return false on fail
        */
        public function export( $zip_file, array $sliders_slugs_array ) {
            
            // Check selected sliders
            if( empty($sliders_slugs_array) ){
                $this->add_error( 'Error no sliders selected' );
                return false;
            }
            
            // Generate sliders export data
            if( ($sliders_export_data = $this->generate_sliders_export_data( $sliders_slugs_array )) === false ){
                $this->add_error( 'Error generating sliders export data' );
                return false;
            }
			
			// Generate images lists
            if( ($images_list = $this->generate_images_list( $sliders_slugs_array )) === false ){
                $this->add_error( 'Error generating images list' );
                return false;
            }

			// Generate images export data
            if( ($images_export_data = $this->generate_images_export_data( $sliders_slugs_array )) === false ){
                $this->add_error( 'Error generating images export data' );
                return false;
            }
			
			// Combine
			$export_data = array(
				'sliders' => $sliders_export_data,
				'images' => $images_export_data
			);
			$this->add_ok( 'Success generating export data' );
			
            // Generate JSON
            if( ($export_json = $this->generate_json_data( $export_data )) === false ){
                $this->add_error( 'Error generating JSON data' );
                return false;
            }
            $this->add_ok( 'Success converting export data to JSON' );
            
            // Generate Zip
            if( $this->generate_export_zip( $zip_file, $images_list, $export_json ) === false ){
                $this->add_error( 'Error generating zip file' );
                return false;
            }
            $this->add_ok( 'Success generating zip file' );
            
            return true;
        }
        
		/**
        * Generate Sliders Export Data
        *
        * Generate export data array for selected sliders. Include slider settings and slides
        *
        * @param array $sliders_slugs_array Array of slider slugs to export
        * @return array|false Export data array or false on fail
        */
        private function generate_sliders_export_data( array $sliders_slugs_array ) {
            
            if( !empty( $sliders_slugs_array ) ){
                $sliders_export_data = array();
                foreach( $sliders_slugs_array as $i=>$slider_slug){

                    $slider = $this->cyclone_slider_data->get_slider_by_slug( $slider_slug );
                    
                    if($slider){
                        $sliders_export_data[$i] = array(
                            'title' => $slider['post_title'],
                            'name' => $slider['post_name'],
                            'slider_settings' => $slider['slider_settings'],
                            'slides' => $slider['slides']
                        );
                        $this->add_ok( sprintf('Exporting data for slider "%s"', $slider_slug) );
                    } else {
                        $this->add_error( sprintf('Slider "%s" not found', $slider_slug) );
                    }
                    
                }
                return $sliders_export_data;
            }
            
            return false;
        }
		
		/**
        * Generate Images List
        *
        * Generate image array for slider images
        *
        * @param array $sliders_slugs_array Array of slider slugs to export
        * @return array|false Export data array or false on fail
        */
        private function generate_images_list( array $sliders_slugs_array ) {
            
            if( !empty( $sliders_slugs_array ) ){
				
                $images_list = array();
				
                foreach( $sliders_slugs_array as $slider_slug){
                    $slider = $this->cyclone_slider_data->get_slider_by_slug( $slider_slug );
                    if($slider){
						if(isset($slider['slides'])){
							foreach($slider['slides'] as $i=>$slide){
								$images_list[$slider['post_name']][$i] = get_attached_file( $slide['id'] ); // Filename of image
							}
						}
                    }
                }
				
                return $images_list;
            }
            
            return false;
        }
		
		/**
        * Generate Images Export Data
        *
        * Generate export data array for slider images
        *
        * @param array $sliders_slugs_array Array of slider slugs to export
        * @return array|false Export data array or false on fail
        */
        private function generate_images_export_data( array $sliders_slugs_array ) {
            
            if( !empty( $sliders_slugs_array ) ){
                $images_export_data = array();
                foreach( $sliders_slugs_array as $slider_slug){

                    $slider = $this->cyclone_slider_data->get_slider_by_slug( $slider_slug );
                    
                    if($slider){
						if(isset($slider['slides'])){
							foreach($slider['slides'] as $i=>$slide){
								$images_export_data[$slider['post_name']][$i] = sanitize_file_name( wp_basename( get_attached_file( $slide['id'] ) ) ); // Filename of image
							}
						}
					}
                }
                return $images_export_data;
            }
            
            return false;
        }
		        
        /**
        * Generate JSON Data
        *
        * Generate json data
        *
        * @param array $export_data Array of export data
        * @return string|false JSON data or false on fail
        */
        private function generate_json_data( array $export_data ) {
            
            if($export_data){
                // JSON encode
                if( $export_json = json_encode($export_data) ){
                    return $export_json;
                }
            }
            
            return false;
        }
        
        /**
        * Generate Export Zip
        *
        * Generate export zip. Add images and export.json
        *
        * @param string $zip_file Zip file to save
        * @param array $images_list Array of image file paths to include in the zip
        * @param string $export_json JSON string to save
        * @return string|false JSON data or false on fail
        */
        private function generate_export_zip( $zip_file, array $images_list, $export_json ) {     

            if( !class_exists('ZipArchive') ) {
                $this->add_error( 'ZipArchive not supported' );
                return false;
            }
            $zip = new ZipArchive();

            if ($zip->open($zip_file, ZIPARCHIVE::OVERWRITE)!==TRUE) {
                $this->add_error( 'Error opening zip file' );
                return false;
            }
           
            // Add slide images
			foreach($images_list as $a=>$sliders) {
				foreach($sliders as $b=>$image_file){
					if(!empty($image_file)){ // Non image slides
						$filename = sanitize_file_name( wp_basename( $image_file ) );
						if( $zip->addFile( $image_file, $filename ) === false ){
							$this->add_error( sprintf( 'Error adding file %s to zip', $filename ) );
						}
					}
				}
			}
            
            // Add json file
            $zip->addFromString("export.json", $export_json );

            $zip->close();
            return $zip_file;
        }
        
		
		
        /**
        * Add Ok
        *
        * @param string $message Message to add
        * @return void
        */
        private function add_ok( $message ){
            $this->log_results['oks'][$this->log_count++] = $message;
        }
        
        /**
        * Add Error
        *
        * @param string $message Message to add
        * @return void
        */
        private function add_error( $message ){
            $this->log_results['errors'][$this->log_count++] = $message;
        }
    }

endif;