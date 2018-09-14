<?php
if(!class_exists('Nextgen_Integration')):

	/**
	* Class for NextGEN integration. Allows import of images from a gallery as new slides.
	*/
	class Nextgen_Integration {
		
		private $cyclone_slider_data; // Holds cyclone slider data object
		
		/**
		* Initialize 
		*/
		function __construct( $cyclone_slider_data ) {
			
			$this->cyclone_slider_data = $cyclone_slider_data;
			
			// Add metaboxes
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 100 );
			
			// Append nextgen slides
			add_filter('cycloneslider_slides', array($this, 'nextgen_slides'));
			
		}
		
		/**
		* Add metabox 
		*/
		function add_meta_boxes(){
			global $nggdb;
			if(!isset($nggdb)){//Show only if nextgen plugin is available
				return false;
			}
			
			add_meta_box(
				'cyclone-nextgen-metabox',
				__('NextGEN Integration', 'cycloneslider'),
				array( $this, 'render_nextgen_meta_box' ),
				'cycloneslider' ,
				'normal',
				'low'
			);
		}
		
		/**
		* Render metabox
		*/
		function render_nextgen_meta_box($post){
			global $nggdb;
			
			?>
			<div class="cycloneslider-field last">
				<label for="cycloneslider_nextgen_gallery"><?php _e('Choose a NextGEN Gallery', 'cycloneslider'); ?></label>
				<?php
				$galleries = $nggdb->find_all_galleries();
				?>
				<select id="cycloneslider_nextgen_gallery" name="cycloneslider_settings[nextgen_gallery]">
					<option selected="selected" value="0"></option>
					<?php foreach($galleries as $gallery): ?>
					<option value="<?php echo $gallery->gid; ?>"><?php echo $gallery->title; ?></option>
					<?php endforeach; ?>
				</select>
				<input type="submit" name="cycloneslider_settings[nextgen]" value="<?php _e('Import', 'cycloneslider'); ?>" class="button-secondary" />
				<br />
				<span class="note"><?php _e('Select a gallery to import images from. Images will be added as new slides.', 'cycloneslider'); ?></span>
				<div class="clear"></div>
			</div>
			<?php
		}
		
		/**
		* Append 1-x to name if filename already exist
		*/
		function increment_name($filename, $extension, $folder_files){
			$name = $filename.'.'.$extension;
			if($this->in_array_str_i($name, $folder_files)){
				$existing = true;
				$counter = 0;
				while($existing){
					$counter++;
					$name = $filename.$counter.'.'.$extension;
					if(!$this->in_array_str_i($name, $folder_files)){
						return $name;
					}
				}
			}
			return $name;
		}
		
		/**
		* Case insensitive string lookup in an array
		*/
		function in_array_str_i($key, $array){
			foreach($array as $element){
				if(strcasecmp($key, $element)==0){
					return true;
				}
			}
			return false;
		}
		
		/**
		* Replace certain characters with spaces in order to be a readable post title
		*/
		function slug_to_title($string){
			$patterns = array();
			$patterns[0] = '/-/';
			$patterns[1] = '/_/';
			$replacements = array();
			$replacements[0] = ' ';
			$replacements[1] = ' ';
			return ucwords(preg_replace($patterns, $replacements, $string));	
		}
		
		/**
		* Copy image to WP upload directory, create attachment and return attachment ID.
		*/
		function copy_image($image_file){
			$dir = wp_upload_dir();
			$target_folder = $dir['path'].'/';
			
			$info = pathinfo($image_file);
			$dirname = isset($info['dirname']) ? $info['dirname'] : ''; // Path to directory
			$filename = isset($info['filename']) ? $info['filename'] : ''; // Filename without extension Eg. "image-1"
			$ext = isset($info['extension']) ? $info['extension'] : ''; // File extension Eg. "jpg"
			
			if($target_folder_files = scandir($target_folder)){
				if(is_array($target_folder_files)){
					$new_name = $this->increment_name($filename, $ext, $target_folder_files); //Append numbers if file exist
					if(copy($image_file, $target_folder.$new_name)){
						
						$size = getimagesize($target_folder.$new_name);// Get mime type
						// Build attachment details
						$attachment = array(
							'post_mime_type' => $size['mime'],
							'post_title' => $this->slug_to_title(basename($new_name, '.'.$ext)),
							'post_content' => '',
							'post_status' => 'inherit'
						);
									
						$attach_id = wp_insert_attachment( $attachment, $target_folder.$new_name );
						
						if(!function_exists('wp_generate_attachment_metadata')) include( ABSPATH . 'wp-admin/includes/image.php' );// Required for wp_generate_attachment_metadata
							
						$attach_data = wp_generate_attachment_metadata( $attach_id, $target_folder.$new_name ); // Generate different thumbnails
						wp_update_attachment_metadata( $attach_id, $attach_data );
						return $attach_id;
					}
				}
			}
			return false;
		}
		
		
		
		/**
		* Add images from nextgen as new slides. Images are copied from nextgen folders into WP uploads dir and added as attachments.
		*/
		function nextgen_slides($slides){
			global $nggdb;
			if(!isset($nggdb)){
				return $slides;
			}
			
			if(isset($_POST['cycloneslider_settings']['nextgen']) and isset($_POST['cycloneslider_settings']['nextgen_gallery']) ){
				
				$nextgen_gallery = $nggdb->get_gallery((int) $_POST['cycloneslider_settings']['nextgen_gallery']);
				
				if(!empty($nextgen_gallery) and is_array($nextgen_gallery)){
					
					foreach($nextgen_gallery as $image){
						if($attach_id = $this->copy_image($image->imagePath)){ //Copy success!
							$slides[] = wp_parse_args(array('id' => $attach_id), $this->cyclone_slider_data->get_slide_defaults() ); //Add the slide ID and fill in default values
						}
					}
				}
			}

			return $slides;
		}
		
	} // end class
	
endif;