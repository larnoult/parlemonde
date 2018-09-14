<?php
if(!class_exists('Cyclone_Slider_Image_Editor')):
    
    /**
    * Class for editing images. Each instance handles a single file.
    */
	class Cyclone_Slider_Image_Editor {
		protected $file = ''; // Image file
		protected $quality = 90; // JPEG quality
		protected $image = false; // GD resource of image
		protected $width; // Image width
		protected $height; // Image height
		protected $size; // Array holding width and height of image
		
		// Resize constants
        const RESIZE_EXACT = 'exact';
		const RESIZE_PORTRAIT = 'portrait';
		const RESIZE_LANDSCAPE = 'landscape';
		const RESIZE_CROP = 'crop';
		
		/**
		 * Assign file
		 */
		public function __construct( $file ) {
			
			$this->file = $file;
			
		}
		
		public function __destruct() {
			if ( $this->image ) {
				// Free memory
				imagedestroy( $this->image );
			}
		}
	
		private function _get_file_ext( $file ){
			// Get extension
			$path_parts = pathinfo( $file );
			
			if( ! isset( $path_parts['extension'] ) )
				return false;
			
			if( $path_parts['extension'] == '' )
				return false;
			
			return strtolower( $path_parts['extension'] );
		}
		
		/**
		 * Loads image resource from file
		 *
		 * @return resource|boolean GD resource on success; False on fail
		 */
		public function load(){
			
			if ( ! is_file( $this->file ) )
				return false;
		
			// Get extension
			$extension = $this->_get_file_ext( $this->file );
			
			if( ! $extension )
				return false;
			
			if( $extension == 'jpg' or $extension == 'jpeg' ){
				$this->image = @imagecreatefromjpeg( $this->file );
				
			} else if( $extension == 'gif' ){
				$this->image = @imagecreatefromgif( $this->file );
				
			} else if( $extension == 'png' ){
				$this->image = @imagecreatefrompng( $this->file );
				
			}
			
			if ( ! is_resource( $this->image ) )
				return false;
			
			$this->update_size();
			
			return true;
		}
		
		/**
		* Sets or updates current image size.
		*
		* @param int $width
		* @param int $height
		*/
		protected function update_size( $width = false, $height = false ) {
			if ( ! $width )
				$width = imagesx( $this->image );
	
			if ( ! $height )
				$height = imagesy( $this->image );
	
			$this->size = array(
				'width' => (int) $width,
				'height' => (int) $height
			);
			return true;
		}
	
		/**
		 * Resize
		 *
		 * @param int $new_width 
		 * @param int $new_height
		 * @param string $resize_option 
		 * @return void
		 */
		public function resize( $new_width, $new_height, $resize_option="auto") {
			// Get current width and height
			list($orig_width, $orig_height) = array_values($this->size);
			
			// Get optimal width and height - based on $resize_option
			list($optimal_width, $optimal_height) = $this->get_optimal_dimension( $orig_width, $orig_height, $new_width, $new_height, $resize_option );

			// Resample - create image canvas of x, y size
			$image_resized = imagecreatetruecolor($optimal_width, $optimal_height);
			// Preserve PNG transparency start
			imagealphablending($image_resized, false); 
			imagesavealpha($image_resized, true);
			$transparent = imagecolorallocatealpha($image_resized, 255, 255, 255, 127);
			imagefilledrectangle($image_resized, 0, 0, $optimal_width, $optimal_height, $transparent);
			// Preserve PNG transparency end
			imagecopyresampled($image_resized, $this->image, 0, 0, 0, 0, $optimal_width, $optimal_height, $orig_width, $orig_height);
			
			// Free memory
			imagedestroy( $this->image );
			
			// Assign
			$this->image = $image_resized;
			$this->update_size();
			
			// If option is 'crop', then crop too
			if ($resize_option == self::RESIZE_CROP) {
				$this->crop($optimal_width, $optimal_height, $new_width, $new_height);
			}
		}
		
		/**
		 * Crop
		 *
		 * @param int $optimal_width 
		 * @param int $optimal_height
		 * @param int $new_width 
		 * @param int $new_height
		 * @param string $resize_option 
		 * @return void
		 */
		public function crop($optimal_width, $optimal_height, $new_width, $new_height) {
			// Find center - this will be used for the crop
			$crop_start_x = ( $optimal_width / 2) - ( $new_width /2 );
			$crop_start_y = ( $optimal_height/ 2) - ( $new_height/2 );
			
			// Now crop from center to exact requested size
			$image_resized = imagecreatetruecolor($new_width , $new_height);
			imagecopyresampled($image_resized, $this->image , 0, 0, $crop_start_x, $crop_start_y, $new_width, $new_height , $new_width, $new_height);
			
			// Free memory
			imagedestroy( $this->image );
			
			// Assign
			$this->image = $image_resized;
			$this->update_size();
		}
		
		/**
		 * Save
		 */
		public function save($filename, $image_quality="100") {
			// Get extension
			$extension = $this->_get_file_ext( $filename );

			switch($extension) {
				case 'jpg':
				case 'jpeg':
					if (imagetypes() & IMG_JPG) {
						imagejpeg($this->image, $filename, $image_quality);
					}
					break;

				case 'gif':
					if (imagetypes() & IMG_GIF) {
						imagegif($this->image, $filename);
					}
					break;

				case 'png':
					// Scale quality from 0-100 to 0-9
					$scale_quality = round(($image_quality/100) * 9);

					// Invert quality setting as 0 is best, not 9
					$invert_scale_quality = 9 - $scale_quality;

					if (imagetypes() & IMG_PNG) {
						imagepng($this->image, $filename, $invert_scale_quality);
					}
					break;

				default:
					// No extension - No save.
					break;
			}
		}
		
		/**
		* Get Optimal Dimension
		*
		* Computes the best possible width and height when resizing an image
		*
		* @param int $orig_width
		* @param int $orig_height
		* @param int $new_width
		* @param int $new_height
		* @param string $resize_option
		*
		* @return array Array containing width and height
		*/
		public function get_optimal_dimension($orig_width, $orig_height, $new_width, $new_height, $resize_option='auto') {

			if($resize_option == self::RESIZE_EXACT){
				
				$optimal_width = $new_width;
				$optimal_height = $new_height;
				
			} else if($resize_option== self::RESIZE_PORTRAIT ){ // Prefer height, compute width

				$ratio = $orig_width / $orig_height;
				
				$optimal_width = $new_height * $ratio;
				$optimal_height= $new_height;
				
			} else if($resize_option== self::RESIZE_LANDSCAPE){ // Prefer width
				
				$ratio = $orig_height / $orig_width;
				
				$optimal_width = $new_width;
				$optimal_height = $new_width * $ratio;

			} else if($resize_option== self::RESIZE_CROP){ // Crop
				
				$height_ratio = $orig_height / $new_height;
				$width_ratio  = $orig_width /  $new_width;
	
				if ($height_ratio < $width_ratio) {
					$optimal_ratio = $height_ratio;
				} else {
					$optimal_ratio = $width_ratio;
				}
				
				$optimal_width  = $orig_width  / $optimal_ratio;
				$optimal_height = $orig_height / $optimal_ratio;
				

			} else { // Auto
				if ($orig_height < $orig_width) { // Image to be resized is wider (landscape)
					
					list($optimal_width, $optimal_height) = $this->get_optimal_dimension($orig_width, $orig_height, $new_width, $new_height, 'landscape');
					
				} elseif ($orig_height > $orig_width) { // Image to be resized is taller (portrait)
					
					list($optimal_width, $optimal_height) = $this->get_optimal_dimension($orig_width, $orig_height, $new_width, $new_height, 'portrait');
					
				} else { // Image to be resized as a square
					if ($new_height < $new_width) { // Image to be resized is wider (landscape)
						
						list($optimal_width, $optimal_height) = $this->get_optimal_dimension($orig_width, $orig_height, $new_width, $new_height, 'landscape');

					} else if ($new_height > $new_width) {
						
						list($optimal_width, $optimal_height) = $this->get_optimal_dimension($orig_width, $orig_height, $new_width, $new_height, 'portrait');

					} else { // Square being resized to a square
						$optimal_width = $new_width;
						$optimal_height= $new_height;
					}
				}
			}

			return array( $optimal_width, $optimal_height );
		}
	}

endif;