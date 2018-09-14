<?php
/**
* Class for copying WP images for import/export purposes and resizing slide images
*/
class CycloneSlider_ImageResizer {

	protected $image_sizes;
	protected $image_editor;
	protected $path;

	public function __construct( $image_sizes, $image_editor, $path ){
		$this->image_sizes = $image_sizes;
		$this->image_editor = $image_editor;
		$this->path = $path;
	}

	/**
	 * Resize Images
	 *
	 * API to resize slide images
	 *
	 * @param $slider_settings
	 * @param array $slides Slides array
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function resize_images( $slider_settings, $slides ){
		
		// Prevent fatal error on users without GD installed
		if(!function_exists('gd_info')){
			return false;	
		}

		$width =  $slider_settings['width'];
		$height =  $slider_settings['height'];
		$slider_settings['resize_quality'] = 100;
		
		if( is_array($slides) ){

			foreach($slides as $slide){
				
				// Get full path to the slide image
				$image_file = get_attached_file( $slide['id'] );
				
				if( true !== is_file( $image_file ) and $slide['id'] > 0 ) {
					throw new Exception( sprintf('Attachment ID %1$d not found.', $slide['id'] ) );
				}
				
				// Extract image path info
				$info = pathinfo($image_file);
				$dirname = isset($info['dirname']) ? $info['dirname'] : ''; // Path to directory
				
				// Create thumb filename-{width}x{height}.jpg
				$thumb_name = $this->generate_thumb_name( $image_file, $width, $height );
				
				// Save image to this file
				$image_file_dest = "{$dirname}/{$thumb_name}";

				// Main slide image
				if( isset($slider_settings['resize']) ){
					if( 1 == $slider_settings['resize'] ){
						
						// Resize if destination file does not exist OR if it exists but force resize is set to true
						if( ( false === is_file($image_file_dest) ) or ( is_file($image_file_dest) and $slider_settings['force_resize'] ) ){

							$this->resize_slide_image( $image_file, $image_file_dest, $width, $height, $slider_settings['resize_option'], $slider_settings['resize_quality'] );
						}
					}
				}
				
				// Additional slide images. Used mainly by templates. Eg. Thumbnail template's thumbnails
				$this->image_sizes = apply_filters('cycloneslider_image_sizes', $this->image_sizes);
				foreach($this->image_sizes as $size){
					
					// Create thumb filename-{width}x{height}.jpg
					$thumb_name = $this->generate_thumb_name( $image_file, $size['width'], $size['height'] );
					
					// Save image to this file
					$image_file_dest = "{$dirname}/{$thumb_name}";
					
					if( ( false === is_file($image_file_dest) ) or ( is_file($image_file_dest) and $slider_settings['force_resize'] ) ){
						
						$this->resize_slide_image( $image_file, $image_file_dest, $size['width'], $size['height'], $size['resize_option'], $slider_settings['resize_quality'] );
					
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Resize Slide Image
	 *
	 * @param string $image_file Full path to image file
	 * @param string $image_file_dest Full path to destination image
	 * @param int $width Image width in pixels
	 * @param int $height Image height in pixels
	 * @param string $resize_option
	 * @param int $resize_quality Quality of image. 0-100 where 0 is worst and 100 is best
	 * @return boolean True or false
	 */
	private function resize_slide_image( $image_file, $image_file_dest, $width, $height, $resize_option, $resize_quality){
		if(version_compare(PHP_VERSION, '5.3', '>=')){

			include $this->path.'src/code-5.3.php'; // Hack. This code is not placed here but in an external file to prevent PHP from parsing the code in versions below 5.3.0 despite the if..else check. See http://stackoverflow.com/questions/17275557/run-a-conditional-using-php-version

		} else {
			// Create
			$image = new CycloneSlider_ImageEditor($image_file);
			// Load
			if( $image->load() ){

				// Do resize
				$image->resize( $width, $height, $resize_option );
				$image->save( $image_file_dest, $resize_quality );


			}
			return false;
		}
		return true;
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