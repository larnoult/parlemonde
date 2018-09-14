<?php
if(!class_exists('Cyclone_Slider_Importer')):

	/**
	* Class for importing cyclone-slider.zip
	*/
	class Cyclone_Slider_Importer {
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
		* Import zip file
		*
		* @param filename $zip_file. Full path and filename to zip file
		* @param string $dir. Path to folder to extract the zip
		*/
		public function import( $zip_file, $target_dir ) {
			// Check zip existence
			if( !file_exists($zip_file) ){
				$this->add_error('Zip file not found.');
				return false;
			}
			
			// Check zip support
			if( !class_exists('ZipArchive') ){
				$this->add_error('ZipArchive not supported.');
				return false;
			}
			
			// Check zip
			if( !$this->check_integrity( $zip_file ) ){
				return false;
			}
			
			// Remove old tmp folder if present
			if ( file_exists($target_dir) and is_dir($target_dir)) {
				if( $this->rmdir_recursive($target_dir)=== false ){
					$this->add_error('Failed to remove the temporary extract folder.');
					return false;
				} else {
					$this->add_ok('Removed the temporary extract folder.');
				}
			}
			
			// Create tmp folder to hold extracted files
			if(mkdir($target_dir)===false){
				$this->add_error('Failed to create the temporary extract folder.');
				return false;
			} else {
				$this->add_ok('Created the temporary extract folder.');
			}
			
			// Extract zip
			$zip = new ZipArchive;
			$zip->open($zip_file);
			if( $zip->extractTo($target_dir) === false ){
				$this->add_error('Failed to extract zip contents.');
				return false;
			} else {
				$this->add_ok('Extracted zip contents.');
			}
			$zip->close();
			
			// Read export JSON
			if( ($export_string = file_get_contents($target_dir.'/export.json')) === false ){
				$this->add_error('Failed to read export JSON.');
				return false;
			} else {
				$this->add_ok('Success reading export file.');
			}
			
			// Decode JSON
			if( ($export_data = json_decode($export_string, true)) == false ) {
				$this->add_error('Failed to decode JSON');
				return false;
			}
			$this->add_ok('Success decoding JSON');

			// Add images
			if( ($images_list = $this->add_images( $export_data['images'] )) === false){
				$this->add_error('Failed to add images');
				return false;
			}
			$this->add_ok('Success importing images');
			
			// Add sliders
			if( ($sliders_list = $this->add_sliders( $export_data['sliders'], $images_list ) ) === false){
				$this->add_error('Failed to add sliders');
				return false;
			}
			$this->add_ok('Success importing sliders');
			
			return true;
		}
		
		private function add_sliders( array $sliders, array $images_list ){
			
			foreach($sliders as $slider_index=>$slider){
				$slider = (array) $slider;
				foreach($slider['slides'] as $slide_index=>$slide){
					$sliders[$slider_index]['slides'][$slide_index]['id'] = $images_list[$slider['name']][$slide_index];// Update image ID
				}
				$this->cyclone_slider_data->add_slider($slider['title'], $slider['slider_settings'], $sliders[$slider_index]['slides'] );
			}
			return $sliders;
		}
		
		/**
		* Check zip file for consistency
		*
		* @param ZipArchive $zip. Instance
		*/
		public function check_integrity( $zip_file ){
			$zip = new ZipArchive();
			
			// ZipArchive::CHECKCONS will enforce additional consistency checks
			$res = $zip->open($zip_file, ZipArchive::CHECKCONS);

			if ( $res === true){ // OK
				return true;
			}
			
			$this->add_error($this->get_zip_error($res));
			return false;

		}
		
		/**
		 * @param 
		 */
		private function add_images( array $sliders ){
			$dir = wp_upload_dir();
			$target_folder = $dir['path'].'/';
			$src_folder = $dir['basedir'].'/cyclone-slider/';
			
			$images_list = array();
			foreach($sliders as $slider_name=>$slider){
				
				foreach($slider as $i=>$image){
					$image_id = 0;
					if(!empty($image)){ // Check for slides without images ie. custom or youtube slide
						$image_id = $this->copy_image( $src_folder.$image, $target_folder);
					}
					$images_list[$slider_name][] = $image_id;
				}
				//$this->cyclone_slider_data->add_slider($slider->title, $slider_settings, $slides );
			}
			return $images_list;
		}
		
		/**
        * Copy image to WP upload directory, create attachment and return attachment ID.
        *
        * @param string $image_file
        * @param string $target_folder
        * @return int|false Attachment ID on success false on fail
        */
        public function copy_image( $image_file, $target_folder ){
            if( !file_exists($image_file) ){
                return false;
            }
            $info = pathinfo($image_file);
            
            if( !isset($info['dirname']) and !isset($info['filename']) and !isset($info['extension']) ){
                return false;
            }
            $dirname = $info['dirname']; // Path to directory
            $filename = $info['filename']; // Filename without extension Eg. "image-1"
            $ext = $info['extension']; // File extension Eg. "jpg"
            
            $target_folder_files = scandir($target_folder);
            if($target_folder_files===false){
                return false;
            }
            
            $new_name = $this->increment_name( wp_basename( $image_file ), $target_folder_files); // Append numbers if file exist
            if(copy($image_file, $target_folder.$new_name)===false){
                return false;
            }
            
            return $this->add_media_image( $target_folder.$new_name ); // Add image as media attachment
        }
        
        /**
        * Add image as media attachment
        *
        * @param array $properties Media properties see $defaults for example
        * @param string $file Full path to file to add
        * @return int|false Attachment ID on success false on fail
        */
        private function add_media_image( $file, array $properties = array() ){
            $wp_upload_dir = wp_upload_dir();
            
            $filename = wp_basename( $file ); // Filename with extension. Example: image.jpg
            $wp_filetype = wp_check_filetype( $filename ); // Get mime type and extension
            $file_ext = $wp_filetype['ext']; // Extension without ".". Example: jpg
            
            $defaults = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => $this->slug_to_title( wp_basename( $file, ".$file_ext" ) ),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            $properties = wp_parse_args( $properties, $defaults ); // Apply defaults
            
            $attach_id = wp_insert_attachment( $properties, $file ); // Location of the file on the server. The file MUST be on the WP uploads directory
            
            if(!function_exists('wp_generate_attachment_metadata')) require_once( ABSPATH . 'wp-admin/includes/image.php' );// Required for wp_generate_attachment_metadata
            
            $attachment_metadata = wp_generate_attachment_metadata( $attach_id, $file ); // Generate different thumbnails
            wp_update_attachment_metadata( $attach_id, $attachment_metadata );
            return $attach_id;
        }
        
        /**
        * Replace - and _ with spaces and capitalize, for a readable post title
        *
        * @param string $string To manipulate
        */
        private function slug_to_title( $string ){
            $string = sanitize_title( $string );
            $patterns = array();
            $patterns[0] = '/-/';
            $patterns[1] = '/_/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = ' ';
            return ucwords(preg_replace($patterns, $replacements, $string));    
        }
        
        /**
        * Remove directory and its contents
        *
        * @param string $dir Directory to delete
        * @return true|false True on success false on fail
        */
        public function rmdir_recursive( $dir ) {
            foreach(scandir($dir) as $file) {
                if ('.' === $file || '..' === $file) continue;
                if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
                else unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        
        /**
		* Append "-{n}" to name if filename already exist. Eg.: image.jpg becomes image-1.jpg
		*
		* @param string $file File to rename
		* @param array $other_filenames Filenames (not full path, only names) to look for. Similar to array returned by scandir.
		*/
		private function increment_name( $file, array $other_filenames ){
			$pathinfo = pathinfo( $file );
		
			$dirname = $pathinfo['dirname'];
			$basename = $pathinfo['basename'];
			$extension = isset($pathinfo['extension']) ? ".".$pathinfo['extension'] : '';
			$filename = $pathinfo['filename'];
		
			$counter = 0;
			$new_name = $basename;
		
			while( $this->in_array_str_i( $new_name, $other_filenames ) ){
				$new_name = $filename.'-'.++$counter.$extension;
			}
			
			return $new_name;
		}
		
		/**
		* Case insensitive string lookup in an array. Similar usage to in_array
		*/
		private function in_array_str_i($key, array $array){
			foreach($array as $element){
				if(strcasecmp($key, $element)==0){ // Found it
					return true;
				}
			}
			return false;
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
		
		/**
		* Get zip error description from code
		*
		* @param int $error_code. ZipArchive error code
		*/
		public function get_zip_error( $error_code ){
			if($error_code == ZIPARCHIVE::ER_MULTIDISK){
				return 'Multi-disk zip archives not supported.';

			} else if($error_code == ZIPARCHIVE::ER_RENAME){
				return 'Renaming temporary file failed.';
				
			} else if($error_code == ZIPARCHIVE::ER_CLOSE){
				return 'Closing zip archive failed.';
				
			} else if($error_code == ZIPARCHIVE::ER_SEEK){
				return 'Seek error.';
				
			} else if($error_code == ZIPARCHIVE::ER_READ){
				return 'Read error.';
				
			} else if($error_code == ZIPARCHIVE::ER_WRITE){
				return 'Write error.';
				
			} else if($error_code == ZIPARCHIVE::ER_CRC){
				return 'CRC error.';
				
			} else if($error_code == ZIPARCHIVE::ER_ZIPCLOSED){
				return 'Containing zip archive was closed.';
				
			} else if($error_code == ZIPARCHIVE::ER_NOENT){
				return 'No such file.';
				
			} else if($error_code == ZIPARCHIVE::ER_EXISTS){
				return 'File already exists.';
				
			} else if($error_code == ZIPARCHIVE::ER_OPEN){
				return 'Cannot open file.';
				
			} else if($error_code == ZIPARCHIVE::ER_TMPOPEN){
				return 'Failure to create temporary file.';
				
			} else if($error_code == ZIPARCHIVE::ER_ZLIB){
				return 'Zlib error.';
				
			} else if($error_code == ZIPARCHIVE::ER_MEMORY){
				return 'Memory allocation failure.';
				
			} else if($error_code == ZIPARCHIVE::ER_CHANGED){
				return 'Entry has been changed.';
				
			} else if($error_code == ZIPARCHIVE::ER_COMPNOTSUPP){
				return 'Compression method not supported.';
				
			} else if($error_code == ZIPARCHIVE::ER_EOF){
				return 'Premature EOF.';
				
			} else if($error_code == ZIPARCHIVE::ER_INVAL){
				return 'Invalid argument.';
				
			} else if($error_code == ZIPARCHIVE::ER_NOZIP){
				return 'Not a zip archive.';
				
			} else if($error_code == ZIPARCHIVE::ER_INTERNAL){
				return 'Internal error.';
				
			} else if($error_code == ZIPARCHIVE::ER_INCONS){
				return 'Zip archive inconsistent.';
				
			} else if($error_code == ZIPARCHIVE::ER_REMOVE){
				return 'Cannot remove file.';
				
			} else if($error_code == ZIPARCHIVE::ER_DELETED){
				return 'Entry has been deleted.';
				
			}
			return 'Unknown error.';
		}
	}

endif;