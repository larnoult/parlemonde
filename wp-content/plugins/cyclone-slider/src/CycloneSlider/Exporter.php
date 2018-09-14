<?php
/**
* Class for exporting cyclone-slider.zip
*/
class CycloneSlider_Exporter {
	protected $log_results; // Hold results of import operations
	protected $log_count;
	
	protected $data;
	protected $zip_archive;
	protected $export_json_file;
	
	public function __construct( $data, $zip_archive, $export_json_file ){
		$this->data = $data;
		$this->zip_archive = $zip_archive;
		$this->export_json_file = $export_json_file;
	}
	
	public function run() {
		
		$this->log_results = array(
			'oks'=>array(),
			'errors'=>array()
		);
		$this->log_count = 0;
		
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
	 *
	 * @return Exception|void Do export or throw exception on failure
	 * @throws Exception
	 */
	public function export( $zip_file, array $sliders_slugs_array ) {
		
		// Check selected sliders
		if( empty($sliders_slugs_array) ){
			throw new Exception( __('Error no sliders selected.', 'cycloneslider'), 1);
		}
		
		// Generate sliders export data
		$sliders_export_data = $this->generate_sliders_export_data( $sliders_slugs_array );
		
		// Generate images lists
		$images_list = $this->generate_images_list( $sliders_export_data );

		// Generate images export data
		$images_export_data = $this->generate_images_export_data( $images_list );
		
		// Combine
		$export_data = array(
			'sliders' => $sliders_export_data,
			'images' => $images_export_data
		);
		
		// Generate JSON
		$export_json = json_encode( $export_data );
		if( false === $export_json ){
			throw new Exception( __('Error encoding data to JSON.', 'cycloneslider'), 1);
		}
		
		// Generate Zip
		$this->generate_export_zip( $zip_file, $images_list, $export_json );
		
		$this->add_ok( sprintf( __('Success generating zip %s.', 'cycloneslider'), wp_basename($zip_file) ) );
		
		return true;
	}

	/**
	 * Generate Sliders Export Data
	 *
	 * Generate export data array for selected sliders. Include slider settings and slides
	 *
	 * @param array $sliders_slugs_array Array of slider slugs to export
	 *
	 * @return array|false Export data array or false on fail
	 * @throws Exception
	 */
	private function generate_sliders_export_data( array $sliders_slugs_array ) {
		
		$sliders_export_data = array();
		
		foreach( $sliders_slugs_array as $i=>$slider_slug){

			$slider = $this->data->get_slider_by_slug( $slider_slug );
			
			if($slider){
				$sliders_export_data[$i] = array(
					'title' => $slider['title'],
					'name' => $slider['name'],
					'slider_settings' => $slider['slider_settings'],
					'slides' => $slider['slides']
				);
				$this->add_ok( sprintf( __('Exporting data for slider "%s".', 'cycloneslider'), $slider_slug) );
			} else {
				throw new Exception( sprintf( __('Slider "%s" not found.', 'cycloneslider'), $slider_slug), 3);
			}
		}
		return $sliders_export_data;
	}

	/**
	 *
	 * Generate image list array containing full file path to images
	 *
	 * @param array $sliders_export_data Array of slider slugs to export
	 *
	 * @return array|Exception Image list array or throws Exception on error
	 * @throws Exception
	 */
	private function generate_images_list( array $sliders_export_data ) {

		$images_list = array();
		
		foreach( $sliders_export_data as $slider){
			
			foreach( $slider['slides'] as $i => $slide ){
				if( $slide['id'] > 0 ){
					$file = get_attached_file( $slide['id'] ); // Filename of image
					if( is_file( $file ) ){ // Check existence
						$images_list[ $slider['name'] ][ $i ] = $file;
					} else {
						throw new Exception( sprintf( __('Image %1$d was not found on slide %2$d of slider %3$s. Path to image is %4$s.', 'cycloneslider' ), $slide['id'], (int)$i+1, $slider['name'], $file ), 4 );
					}
				}
			}
			
		}
		
		return $images_list;
		
	}
	
	/**
	* Generate export data array for slider images
	*
	* @param array $images_list Array of slider images
	* @return array Export data of images 
	*/
	private function generate_images_export_data( array $images_list ) {
		
		$images_export_data = array();
		foreach( $images_list as $slider_name => $slider ){
			foreach($slider as $i => $slide_image ){
				$images_export_data[ $slider_name ][ $i ] = wp_basename( $slide_image ); // Remove full path and retain only the file name
			}
		}
		return $images_export_data;
		
	}

	/**
	 * Generate Export Zip
	 *
	 * Generate export zip. Add images and export.json
	 *
	 * @param string $zip_file Zip file to save
	 * @param array $images_list Array of image file paths to include in the zip
	 * @param string $export_json JSON string to save
	 *
	 * @return Exception|string Path to zip file or Exception on fail
	 * @throws Exception
	 */
	private function generate_export_zip( $zip_file, array $images_list, $export_json ) {     

		if( !class_exists('ZipArchive') ) {
			throw new Exception( __( 'ZipArchive not supported.', 'cycloneslider' ) );
		}
		$zip_archive_class_name = $this->zip_archive;
		$zip = new $zip_archive_class_name();
		$result = $zip->open( $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE );
		if ( true !== $result ) {
			throw new Exception( sprintf( __( 'Error opening zip file %s. Code: %s', 'cycloneslider' ), $zip_file, $result ) );
		}
		
		// Add slide images
		foreach($images_list as $a=>$sliders) {
			foreach($sliders as $b=>$image_file){
				if(!empty($image_file)){ // Non image slides
					$filename = sanitize_file_name( wp_basename( $image_file ) );
					if( $zip->addFile( $image_file, $filename ) === false ){
						throw new Exception( sprintf( __( 'Error adding file %s to zip.', 'cycloneslider' ), $image_file ) );
					} else {
						$this->add_ok( sprintf( __('File %s added to zip.', 'cycloneslider'), wp_basename($image_file) ) );
					}
				}
			}
		}
		
		// Add json file
		$zip->addFromString($this->export_json_file, $export_json );
		$this->add_ok( sprintf( __('File %s added to zip.', 'cycloneslider' ), $this->export_json_file) );
		
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