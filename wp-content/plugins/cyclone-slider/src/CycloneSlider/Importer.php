<?php

/**
* Class for importing cyclone-slider export zip
*/
class CycloneSlider_Importer {
	
	protected $data;
	protected $imports_dir;
	protected $wp_upload_dir;
	protected $zip_archive;
	protected $zip_name;
	protected $imports_extracts_dir;
	protected $export_json_file;
	
	public function __construct( $data, $imports_dir, $wp_upload_dir, $zip_archive, $zip_name, $imports_extracts_dir, $export_json_file ){
		$this->data = $data;
		$this->imports_dir = $imports_dir;
		$this->wp_upload_dir = $wp_upload_dir;
		$this->zip_archive = $zip_archive;
		$this->zip_name = $zip_name;
		$this->imports_extracts_dir = $imports_extracts_dir;
		$this->export_json_file = $export_json_file;
	}
	
	/*
	 * Wrapper function for various import operations. Uses other functions in correct order.
	 *
	 * @param string $uploaded_zip The zip file uploaded via HTTP POST. Must be the tmp_name of $_FILES, eg: $_FILES['cycloneslider_import']['tmp_name'].
	 * @return bool|Exception True on success. On error, throw an Exception.
	 */
	public function import( $uploaded_zip ){
		// Check zip support
		if( !class_exists('ZipArchive') ){
			throw new Exception( __('Could not read zip files. ZipArchive not supported.', 'cycloneslider'), 1);
		}
		
		// Check zip
		if( !is_file( $uploaded_zip ) ){
			throw new Exception( __('No zip file found.', 'cycloneslider'), 1);
		}
		
		// Create imports dir
		if( is_dir( $this->imports_dir ) == false ){
			if( ! mkdir( $this->imports_dir, 0777, true ) ){
				throw new Exception( __('Error creating imports directory.', 'cycloneslider'), 2);
			}
		}
		
		// Move uploaded zip and rename it
		$zip_file = $this->imports_dir.'/'.$this->zip_name;
		if ( ! move_uploaded_file( $uploaded_zip, $zip_file ) ){
			throw new Exception( __('Error moving uploaded zip.', 'cycloneslider'), 3);
		}
		
		// Open zip and perform checks
		$zip = new ZipArchive();
		$zip_result = $zip->open( $zip_file, ZipArchive::CHECKCONS);
		if( true !== $zip_result ){
			throw new Exception( sprintf( __('Error opening zip: %s', 'cycloneslider'), $this->get_zip_error( $zip_result ) ), 4);
		}

		// Security checks
		$export_json_found = false;
		for( $i = 0; $i < $zip->numFiles; $i++ ){
			$status = $zip->statIndex( $i );
			$name = $status['name'];
			$entry = $zip->getFromIndex( $i );
			if (false !== $entry) {
				if($name === 'export.json') {
					$export_json_found = true;
					if( null === json_decode($entry) ){  // Not a valid JSON
						throw new Exception( sprintf( __('Security error. Invalid %s file.', 'cycloneslider'), $name ) );
					}
				} else {
					$im = @imagecreatefromstring( $entry );
					if(false !== $im){
						imagedestroy($im);
					} else { // Not an image
						throw new Exception( sprintf( __('Security error. File %s is not an image.', 'cycloneslider'), $name ) );
					}
				}
			}
		}
		if(!$export_json_found) {
			throw new Exception( sprintf( __('Security error. Missing %s file.', 'cycloneslider'), 'export.json' ) );
		}

		// Extract zip to extraction dir
		$extraction_dir = $this->imports_extracts_dir;
		if( $zip->extractTo( $extraction_dir ) === false ){
			throw new Exception( __('Error extracting zip.', 'cycloneslider'), 5);
		}
		$zip->close();
		
		// Read export file from extraction dir
		$json_file = $extraction_dir.'/'.$this->export_json_file;
		if( ($export_string = file_get_contents( $json_file )) === false ){
			throw new Exception( __('Failed to read export JSON.', 'cycloneslider'), 6);
		}
		
		// Decode JSON
		if( ($export_data = json_decode($export_string, true)) == false ) {
			throw new Exception( __('Failed to decode JSON.', 'cycloneslider'), 7);
		}

		// Add images to wp uploads dir and add as attachment
		$images_list = $this->add_images_to_wp( $export_data['images'], $extraction_dir, $this->wp_upload_dir );
		
		// Add sliders
		$this->add_sliders( $export_data['sliders'], $images_list );
	}
	
	
	/**
	 * Add images to WP media library
	 * 
	 * @param array $images_array Array of image from export data
	 * @param string $src_folder Folder to get images from
	 * @param string $target_folder WP upload dir: uploads/2015/03
	 * @return array Array of WP attachment IDs
	 */
	private function add_images_to_wp( array $images_array, $src_folder, $target_folder ){
		
		$images_list = array();
		foreach($images_array as $slider_name=>$slider){
			
			foreach($slider as $i=>$image_name){
				$image_id = 0;
				if(!empty($image_name)){ // Check for slides without images ie. custom or youtube slide
					
					$new_name = $this->unique_name($image_name, $target_folder); // Generates name that avoids conflict
					$src_image_file = $src_folder.'/'.$image_name; // Full path to source image. Note the use of forward slash
					$dest_image_file = $target_folder.'/'.$new_name; // Full path to destination image
					$this->copy_image( $src_image_file, $dest_image_file ); // Move image to wp uploads dir
					$image_id =  $this->add_media_image( $dest_image_file ); // Add image as media attachment
					
				}
				$images_list[$slider_name][] = $image_id;
			}
		}
		return $images_list;
	}
	
	/**
	 * Add sliders to cyclone slider using the add_slider API. Replaces the attachment ID from the export file with the true ID 
	 *
	 * @param array $sliders
	 * @param array $images_list
	 *
	 * @return array Sliders
	 */
	private function add_sliders( array $sliders, array $images_list ){
		
		foreach($sliders as $slider_index=>$slider){
			$slider = (array) $slider;
			foreach($slider['slides'] as $slide_index=>$slide){
				$sliders[$slider_index]['slides'][$slide_index]['id'] = $images_list[$slider['name']][$slide_index];// Update image ID. The image ID is different on the machine being imported to 
			}
			$this->data->add_slider($slider['title'], $slider['slider_settings'], $sliders[$slider_index]['slides'] );
		}
		return $sliders;
	}
	
	/*
	 * Create a name that avoids name collision with existing images in the target folder.
	 *
	 * @param string $image_name
	 * @param string $target_folder
	 * @return string Image name. Will throw exception on error.
	 */
	private function unique_name( $image_name, $target_folder){
		$target_folder_files = scandir($target_folder);
		if( false === $target_folder_files ){
			throw new Exception( sprintf( __('scandir failed on %s', 'cycloneslider'), $target_folder), 8 );
		}
		
		return $this->increment_name( $image_name, $target_folder_files); // Append numbers if file exist
		
	}
	
	/**
	* Copy image to WP upload directory
	*
	* @param string $image_file
	* @param string $target_folder
	* @return void Will throw exception on error
	*/
	private function copy_image( $src_image_file, $dest_image_file ){
		if( ! file_exists($src_image_file) ){
			throw new Exception( sprintf( __('Source image %s not found.', 'cycloneslider'), $src_image_file ), 9);
		}
		
		if( ! copy($src_image_file, $dest_image_file) ){
			throw new Exception( __('Copy error.', 'cycloneslider'), 10);
		}
	}
	
	/**
	* Add image as media attachment
	*
	* @param array $properties Media properties see $defaults for example
	* @param string $file Full path to file to add
	* @return int Attachment ID on success false on fail
	*/
	private function add_media_image( $file, array $properties = array() ){
		
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
	* Append "-{n}" to name if filename already exist. Eg.: image.jpg becomes image-1.jpg
	*
	* @param string $file File to rename. Does not need to be a full path.
	* @param array $other_filenames Filenames (not full path, only names) to look for. Similar to array returned by scandir.
	*/
	private function increment_name( $file, array $other_filenames ){
		$pathinfo = pathinfo( $file );
		
		$name_with_ext = $pathinfo['basename'];
		$name_without_ext = $pathinfo['filename'];
		$extension = isset($pathinfo['extension']) ? ".".$pathinfo['extension'] : '';
		
		$counter = 0;
		$new_name = $name_with_ext;
	
		while( $this->in_array_str_i( $new_name, $other_filenames ) ){
			$new_name = $name_without_ext.'-'.++$counter.$extension;
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
