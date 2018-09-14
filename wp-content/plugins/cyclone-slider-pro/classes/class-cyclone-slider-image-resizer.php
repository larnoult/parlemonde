<?php
if(!class_exists('Cyclone_Slider_Image_Resizer')):
    
    /**
    * Class for copying WP images for import/export purposes and resizing slide images
    */
    class Cyclone_Slider_Image_Resizer {
		
		/**
         * Resize Images
         * 
         * API to resize slide images
         *
         * @param int $slider_id Slider post ID
         * @param array $slides Slides array
         * @return void
         */
        public function resize_images( $slider_settings, $slides ){
            
            // Prevent fatal error on users without GD installed
            if(!function_exists('gd_info')){
                return false;	
            }
            
            $slider_settings['resize_quality'] = 90;
            $width =  $slider_settings['width'];
            $height =  $slider_settings['height'];
            
            if( is_array($slides) ){

                foreach($slides as $slide){
                    // Get full path to the slide image
                    $image_file = get_attached_file( $slide['id'] );

					// Extract image path info
					$info = pathinfo($image_file);
					$dirname = isset($info['dirname']) ? $info['dirname'] : ''; // Path to directory
					
					// Create thumb filename-{width}x{height}.jpg
					$thumb_name = $this->generate_thumb_name( $image_file, $width, $height );
					
					// Save image to this file
					$image_file_dest = "{$dirname}/{$thumb_name}";
					
					if( ! is_file( $image_file_dest ) ) { // Destination file does not exist
						//echo "fresh {$image_file_dest} <br>";
						$this->resize( $image_file, $image_file_dest, $width, $height, $slider_settings );
						
					} else if( is_file( $image_file_dest ) and $slider_settings['force_resize'] ) { // Exist but force resize so we resave it
						//echo "REfreshed {$image_file_dest} <br>";
						$this->resize( $image_file, $image_file_dest, $width, $height, $slider_settings );
						
					}
                }
                //exit;
            }
        }
		
		/**
         * Resize Image
         *
         * @param string $image_file
         * @param string $image_file_dest
         * @param int $width
         * @param int $height
         * @param array $slider_settings
         * @return boolean True or false
         */
		private function resize($image_file, $image_file_dest, $width, $height, $slider_settings){
			// Create
			$image = new Cyclone_Slider_Image_Editor( $image_file );
			
			// Load
			if( $image->load() ){
				
				// Do resize
				$image->resize( $width, $height, $slider_settings['resize_option'] );
				$image->save( $image_file_dest, $slider_settings['resize_quality']);
				
				return true;
			}
			return false;
		}
		
		/**
         * Generate Thumb Name
         *
         * @param string $image_file
         * @param int $width
         * @param int $height
         * @return string 
         */
		public function generate_thumb_name( $image_file, $width, $height ){
            
            // Get image path info and create file name
            $info = pathinfo( $image_file ); // Eg: d:/uploads/image-1.jpg
            if( !isset($info['extension']) or !isset($info['filename'])){
                return false;
            }

            $ext = $info['extension']; // File extension Eg. "jpg"
            $filename = $info['filename']; // Filename Eg. "image-1"
            return "{$filename}-{$width}x{$height}.{$ext}"; // Thumbname. Eg. "image-1-600x300.jpg"
            
        }
    }

endif;