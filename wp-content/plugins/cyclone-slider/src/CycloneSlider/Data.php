<?php
/**
 * Class for saving and getting slider data  
 */
class CycloneSlider_Data {

    /**
     * @var string
     */
    protected $nonce_name;
    /**
     * @var string
     */
    protected $nonce_action;
    /**
     * @var CycloneSlider_ImageResizer
     */
    protected $image_resizer;
    /**
     * @var array
     */
    protected $template_locations;
    /**
     * @var array
     */
    protected $settings_page_properties;

    public function __construct( $nonce_name, $nonce_action, $image_resizer, $template_locations, $settings_page_properties ){
        $this->nonce_name = $nonce_name;
		$this->nonce_action = $nonce_action;
		$this->image_resizer = $image_resizer;
		$this->template_locations = $template_locations;
        $this->settings_page_properties = $settings_page_properties;
    }
    
    public function run(){
        global $wp_version;
		
        // Add save hook
	    $this->_add_save_hook( $wp_version );
    }
    
    /**
     * Save post hook
     */
    public function save_post_hook( $post_id ){
        global $wp_version;

        // Use local variable
        $post = $_POST;
        
        // Verify nonce
        $nonce_name = $this->nonce_name;
        if (!empty($post[$nonce_name])) {
            if (!wp_verify_nonce($post[$nonce_name], $this->nonce_action)) {
                return $post_id;
            }
        } else {
            return $post_id; // Make sure we cancel on missing nonce!
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // TODO: Comprehensive array key checks
        $slider = array(
            'id' => $post_id,
			'name' => $post['post_name'],
			'title' => $post['post_title'],
			'status' => $post['post_status'],
			'slider_settings' => isset($post['cycloneslider_settings']) ? $post['cycloneslider_settings'] : array(),
			'slides' => isset($post['cycloneslider_metas']) ? $post['cycloneslider_metas'] : array(),
        );
        // Remove temporarily to avoid infinite loop
	    $this->_remove_save_hook( $wp_version );
	    // Update slider info
	    $this->update_slider($slider);
		// Add save hook
	    $this->_add_save_hook( $wp_version );
    }
    
    /**
	 * @param array $slider
	 *
	 * @return int|\WP_Error
	 */
	public function add_slider( $slider ){

		$slider['id'] = 0; // Set to zero to insert instead of update
		return $this->update_slider( $slider );
	}

    /**
	 * @param array $slider
	 *
	 * @return int|\WP_Error
	 */
	public function update_slider( $slider ){

		$post_data = array(
			'ID' => (int) $slider['id'],
			'post_name' => wp_strip_all_tags($slider['name'], true),
			'post_title' => wp_strip_all_tags($slider['title'], true),
			'post_type' => 'cycloneslider',
			'post_status' => $slider['status'],
			'post_content' => ''
		);

		if( $slider_id = wp_insert_post( $post_data ) ){

			// Resize images if needed
            if( $slider['slider_settings']['resize'] == 1){
				
                $this->image_resizer->resize_images( $slider['slider_settings'], $slider['slides'] );
            }
            
            // Save slides
            $this->add_slider_slides( $slider_id, $slider['slides'] );
            
            // Save slider settings
            $this->add_slider_settings( $slider_id, $slider['slider_settings'] );

		}

		return $slider_id;
	}

    /**
     * Add Slide Settings
     * 
     * API to add slider settings to slider post meta
     *
     * @param int $slider_id Slider post ID
     * @param array $settings Slider settings array
     * @return void
     */
    public function add_slider_settings( $slider_id, array $settings ){
        $settings = wp_parse_args(
            $settings,
            $this->get_slider_defaults()
        );
        
        $settings_to_save['template'] = sanitize_text_field( $settings['template'] );
        $settings_to_save['fx'] = sanitize_text_field( $settings['fx'] );
        $settings_to_save['timeout'] = (int) ( $settings['timeout'] );
        $settings_to_save['speed'] = (int) ( $settings['speed'] );
        $settings_to_save['width'] = (int) ( $settings['width'] );
        $settings_to_save['height'] = (int) ( $settings['height'] );
        $settings_to_save['hover_pause'] = sanitize_text_field( $settings['hover_pause'] );
        $settings_to_save['show_prev_next'] = (int) ( $settings['show_prev_next'] );
        $settings_to_save['show_nav'] = (int) ( $settings['show_nav'] );
        $settings_to_save['tile_count'] = (int) ( $settings['tile_count'] );
        $settings_to_save['tile_delay'] = (int) ( $settings['tile_delay'] );
        $settings_to_save['tile_vertical'] = sanitize_text_field( $settings['tile_vertical'] );
        $settings_to_save['random'] = (int) ( $settings['random'] );
        $settings_to_save['resize'] = (int) ( $settings['resize'] );
        $settings_to_save['width_management'] = sanitize_text_field( $settings['width_management'] );

        $settings_to_save = apply_filters('cycloneslider_settings', $settings_to_save, $settings, $slider_id); // Apply filters before saving
        
        delete_post_meta($slider_id, '_cycloneslider_settings');
        update_post_meta($slider_id, '_cycloneslider_settings', $settings_to_save);
    }
    
    /**
     * Add Slider Slides
     * 
     * API to add slides 
     *
     * @param int $slider_id Slider post ID
     * @param array $slides Slides array
     */
    public function add_slider_slides( $slider_id, array $slides ){
        
        $slides_to_save = array();
        
        $i=0;//always start from 0
        foreach($slides as $slide){
            $slide = wp_parse_args(
                $slide,
                $this->get_slide_defaults()
            );
            $slides_to_save[$i]['id'] = (int) ($slide['id']);
            $slides_to_save[$i]['type'] = sanitize_text_field($slide['type']);
            $slides_to_save[$i]['hidden'] = (int) ($slide['hidden']);
            
            $slides_to_save[$i]['link'] = esc_url_raw($slide['link']);
            $slides_to_save[$i]['title'] = wp_kses_post($slide['title']);
            $slides_to_save[$i]['description'] = wp_kses_post($slide['description']);
            $slides_to_save[$i]['link_target'] = sanitize_text_field($slide['link_target']);
            
            $slides_to_save[$i]['img_alt'] = sanitize_text_field($slide['img_alt']);
            $slides_to_save[$i]['img_title'] = sanitize_text_field($slide['img_title']);
            
            $slides_to_save[$i]['enable_slide_effects'] = (int) ($slide['enable_slide_effects']);
            $slides_to_save[$i]['fx'] = sanitize_text_field($slide['fx']);
            $slides_to_save[$i]['speed'] = sanitize_text_field($slide['speed']);
            $slides_to_save[$i]['timeout'] = sanitize_text_field($slide['timeout']);
            $slides_to_save[$i]['tile_count'] = sanitize_text_field($slide['tile_count']);
            $slides_to_save[$i]['tile_delay'] = sanitize_text_field($slide['tile_delay']);
            $slides_to_save[$i]['tile_vertical'] = sanitize_text_field($slide['tile_vertical']);
            
            $slides_to_save[$i]['video_thumb'] = esc_url_raw($slide['video_thumb']);
            $slides_to_save[$i]['video_url'] = esc_url_raw($slide['video_url']);
            $slides_to_save[$i]['video'] = $slide['video'];
            
            $slides_to_save[$i]['custom'] = $slide['custom'];
            
            $slides_to_save[$i]['youtube_url'] = esc_url_raw($slide['youtube_url']);
            $slides_to_save[$i]['youtube_related'] = sanitize_text_field($slide['youtube_related']);
            
            $slides_to_save[$i]['vimeo_url'] = esc_url_raw($slide['vimeo_url']);
            
            $slides_to_save[$i]['testimonial'] = wp_kses_post($slide['testimonial']);
            $slides_to_save[$i]['testimonial_author'] = sanitize_text_field($slide['testimonial_author']);
            $slides_to_save[$i]['testimonial_link'] = esc_url_raw($slide['testimonial_link']);
            $slides_to_save[$i]['testimonial_link_target'] = sanitize_text_field($slide['testimonial_link_target']);
            $slides_to_save[$i]['testimonial_img'] = (int) $slide['testimonial_img'];
        
            $i++;
        }
            
        $slides_to_save = apply_filters('cycloneslider_slides', $slides_to_save); //do filter before saving
        
        delete_post_meta($slider_id, '_cycloneslider_metas');
        update_post_meta($slider_id, '_cycloneslider_metas', $slides_to_save);
    }
    
    
    
    public function get_thumb_name( $image_file, $width, $height ){
        
        // Get image path info and create file name
        $info = pathinfo( $image_file ); // Eg: d:/uploads/image-1.jpg
        if( !isset($info['extension']) or !isset($info['filename'])){
            return false;
        }

        $ext = $info['extension']; // File extension Eg. "jpg"
        $filename = $info['filename']; // Filename Eg. "image-1"
        return "{$filename}-{$width}x{$height}.{$ext}"; // Thumbname. Eg. "image-1-600x300.jpg"
        
    }
    
    /**
	 * @param $slug Post slug
	 *
	 * @return array|NULL
	 */
    public function get_slider_by_slug( $slug ){
        global $wp_version;

		$args = array(
			'numberposts' => 1 // 1 only
		);

        $args['name'] = $slug;
        if ( version_compare( $wp_version, '4.4', '>=' ) ) { // post_name__in avail only in 4.4
            $args['post_name__in'] = array( $slug ); // Workaround: Using "post_name__in" not "name" as WP returns a post instead of nothing when name is ''.
        }
		
		$sliders = $this->get_sliders( $args );
		return array_pop($sliders); // Return the lone slideshow or NULL if empty
    }

    /**
	 * @param $id_or_slug
	 *
	 * @return array|NULL
     * @throws Exception on invalid parameter
	 */
	public function get_slider( $id_or_slug ) {
		global $wp_version;

		$args = array(
			'numberposts' => 1 // 1 only
		);

		if ( is_numeric( $id_or_slug ) ) {
			$args['post__in'] = array( $id_or_slug ); // Workaround: Using "post_in" not "p" as WP inserts an ID when p <= 0.
		} else if ( is_string( $id_or_slug ) ) {
			$args['name'] = $id_or_slug;
			if ( version_compare( $wp_version, '4.4', '>=' ) ) { // post_name__in avail only in 4.4
				$args['post_name__in'] = array( $id_or_slug ); // Workaround: Using "post_name__in" not "name" as WP returns a post instead of nothing when name is ''.
			}
		} else {
			throw new Exception( sprintf(__( 'Invalid format for get_slider %s parameter.', 'cycloneslider' ), '$id_or_slug') );
		}

		$sliders = $this->get_sliders( $args );
		return array_pop($sliders); // Return the lone slideshow or NULL if empty
	}

    /**
    * Get Sliders
    *
    * Get all sliders and their accompanying meta data
    * 
    * @return array Returns an array of sliders
    */
    public function get_sliders( $args=array() ){
        $defaults = array(
            'post_type' => 'cycloneslider',
            'post_status' => array('any', 'auto-draft'), // As long as it exist, get it
			'numberposts' => -1 // Get all
        );
        $args = wp_parse_args($args, $defaults);
        
        $sliders = array(); // Store it here
        foreach($this->get_posts( $args ) as $index=>$slider_post){
            $sliders[$index] = array(
                'id' => $slider_post['ID'],
                'name' => $slider_post['post_name'],
                'title' => $slider_post['post_title'],
                'status' => $slider_post['post_status']
            );
            $sliders[$index]['slider_settings'] = $this->get_slider_settings( $slider_post['ID'] );
            $sliders[$index]['slides'] = $this->get_slider_slides( $slider_post['ID'] );
        }
        return $sliders;
    }
    
    /**
    * Get Slider Settings
    *
    * Get slider settings by $slider_id
    *
    * @paramt int $slider_id ID of slider post
    * @return array The array of slider settings
    */
    public function get_slider_settings( $slider_id ) {
        $slider_settings = $this->get_post_meta( $slider_id, '_cycloneslider_settings' );
        $slider_settings = wp_parse_args($slider_settings, $this->get_slider_defaults() );
        return apply_filters('cycloneslider_get_slider_settings', $slider_settings);
    }
    
    /**
    * Get Slider Slides
    *
    * @param int $slider_id Post ID of the slider custom post.
    * @return array The array of slides or empty array
    */
    public function get_slider_slides( $slider_id ){
        $slides = $this->get_post_meta( $slider_id, '_cycloneslider_metas' );
        
        $defaults = $this->get_slide_defaults();
        foreach($slides as $i=>$slide){
            $slides[$i] = wp_parse_args($slide, $defaults);
        }
        
        return apply_filters('cycloneslider_get_slider_slides', $slides);
    }
    
    /**
    * Gets the number of slides of slideshow
    *
    * @param int $slider_id Post ID of slider custom post
    * @return int Total slides
    */
    public function get_slide_count( $slider_id ){
        $slides = $this->get_post_meta( $slider_id, '_cycloneslider_metas' );
        
        return count($slides);
    }
    
    /**
    * Get Slide Image URL. 
    */
    public function get_slide_image_url( $slide_image_id, $slider_settings ){
        $width = $slider_settings['width'];
        $height = $slider_settings['height'];
        
        // Get url to full image, its width and height
        $image_dimensions = wp_get_attachment_image_src($slide_image_id, 'full');
        if(!$image_dimensions){
            return false;
        }
        
        // Assign variables
        list($image_url, $orig_width, $orig_height) = $image_dimensions;
        
        // If orig image width and height is the same as slideshow width and height, do not resize and return url
        if($orig_width == $width and $orig_height == $height){
            return $image_url;
        }
        
        //If resize is no, return url
        if( isset( $slider_settings['resize'] ) and $slider_settings['resize'] == 0 ){
            return $image_url;
        }
        
        // Get full path to the slide image
        $image_file = get_attached_file($slide_image_id);
        if(empty($image_file)){
            return false;
        }
        
        $thumb_name = $this->get_thumb_name( $image_file, $width, $height );
        $thumb_url = dirname($image_url).'/'.$thumb_name; // URL to thumbnail
        
        return $thumb_url; 
    }
    
    /**
    * Get Slide Thumbnail URL. 
    */
    public function get_slide_thumbnail_url( $slide_image_id, $width, $height, $resize){
        
        // Get url to full image, its width and height
        $image_dimensions = wp_get_attachment_image_src($slide_image_id, 'full');
        if(!$image_dimensions){
            return false;
        }
        
        // Assign variables
        list($image_url, $orig_width, $orig_height) = $image_dimensions;
        
        // If orig image width and height is the same as width and height, do not resize and return url
        if($orig_width == $width and $orig_height == $height){
            return $image_url;
        }
        
        //If resize is no, return url
        if( $resize == 0 ){
            return $image_url;
        }
        
        // Get full path to the slide image
        $image_file = get_attached_file($slide_image_id);
        if(empty($image_file)){
            return false;
        }
        
        $thumb_name = $this->get_thumb_name( $image_file, $width, $height );
        $thumb_url = dirname($image_url).'/'.$thumb_name; // URL to thumbnail
        
        return $thumb_url; 
    }
    
    /**
     * Get View File
     *
     * Get slider view file from theme or plugin or wp-content location
     *
     * @param string $template_name Name of slider template
     * @return string|false Slider view filepath or false
     */
    public function get_view_file( $template_name ){

        $templates = $this->get_all_templates();
        if(isset($templates[$template_name])){
            $view_file = $templates[ $template_name ]['path'] . '/slider.php';
            if(@is_file($view_file)){
                return $view_file;
            }
        }

        return false;
    }
    
    /**
	 * Get all templates in array format
	 */
	public function get_all_templates(){

        $templates = array();
		if( is_array( $this->template_locations ) ){
			foreach( $this->template_locations as $location ){
				if( is_dir( $location['path'] ) ) {
					if( $files = scandir( $location['path'] ) ){
						foreach( $files as $name ){
							if($name!='.' and $name!='..' and is_dir($location['path'].$name) and @file_exists($location['path'].$name.DIRECTORY_SEPARATOR.'slider.php') ){ // Check if its a directory
								$supported_slide_types = array('image');// Default
								if ( $config = $this->parse_config_json( $location['path'].$name.DIRECTORY_SEPARATOR.'config.json' ) ) {
									$supported_slide_types = $config->slide_types;
								} else if ( @file_exists($location['path'].$name.DIRECTORY_SEPARATOR.'config.txt') ) { // Older templates use ini format
									$ini_array = parse_ini_file($location['path'].$name.DIRECTORY_SEPARATOR.'config.txt'); //Parse ini to get slide types supported
									if($ini_array){
										$supported_slide_types = $ini_array['slide_type'];
									}
								}

                                $name = sanitize_title($name); // Change space to dash and all lowercase

                                // Old templates (pre 2.11.0) can only have 1 css file and 1 js file

                                // Check if script.js exists
                                $scripts = array();
                                if ( @file_exists( $location['path'] . $name . DIRECTORY_SEPARATOR . 'script.js' ) ) {
                                    $scripts = array(
                                        'script.js'
                                    );
                                }

                                // Check if style.css exists
                                $styles = array();
                                if ( @file_exists( $location['path'] . $name . DIRECTORY_SEPARATOR . 'style.css' ) ) {
                                    $styles = array(
                                        'style.css'
                                    );
                                }

                                // Create and add template object to our template list
                                $templates[ $name ] = array(
                                    'name' => ucwords(str_replace('-',' ',$name)),
                                    'path' => $location['path'].$name,
                                    'url' => $location['url'].$name,
                                    'supports' => $supported_slide_types,
                                    'location_name' => $location['location_name'],
                                    'scripts' => $scripts,
                                    'styles' => $styles
                                );
							}
						}
					}
				}
			}
		}
        return apply_filters('cycloneslider_template_list', $templates);
	}
    
    /**
	 * Get Active Templates
	 *
	 * Get templates that are enabled in settings page
	 *
	 * @param array $settings_data Settings page data
	 * @param array $templates List of all templates
	 * @return array Template locations
	 */
	public function get_enabled_templates( $settings_data, $templates ){
		
		foreach($templates as $name=>$template){
			if( !isset($settings_data['load_templates'][$name]) ){
				$settings_data['load_templates'][$name] = 1;
			}
		}
		return $settings_data['load_templates'];
	}
    
    /**
	 * Get template config data from file
	 *
	 * @param string $file Full path to config file
	 * @return object $config_data or false on fail
	 */
	public function parse_config_json( $file ){
		if( @file_exists($file) ){
			$config = file_get_contents($file); //Get template info
			if($config){
				$config_data = json_decode($config);
				if($config_data){
					return $config_data;
				}
			}
		}
		return false;
	}
    
    /**
	* Get settings data. If there is no data from database, use default values
	*/
	public function get_settings_page_data(){
		$option = get_option( $this->settings_page_properties['option_name'], array() );
        return wp_parse_args($option, $this->get_default_settings_page_data());
	}
    
    /**
	* Apply default values
	*/
	public function get_default_settings_page_data() {
		$defaults = array();

		$defaults['legacy'] = 0;

		$defaults['load_scripts_in'] = 'footer';
		
		$defaults['load_cycle2'] = 1;
		$defaults['load_cycle2_carousel'] = 1;
		$defaults['load_cycle2_swipe'] = 1;
		$defaults['load_cycle2_tile'] = 1;
		$defaults['load_cycle2_video'] = 1;

		$defaults['load_easing'] = 0;
		
		$defaults['load_magnific'] = 0;
		
		$defaults['load_templates'] = array();
		
		$defaults['script_priority'] = 100;
		
		$defaults['license_id'] = '';
		$defaults['license_key'] = '';
		
		return $defaults;
	}
    
    /**
    * Cyclone Slide Settings
    *
    * Prints out cycle2 per slide settings as data attributes
    *
    *
    * @param array $slide Slide settings array.
    * @param array $slider Slider array.
    *
    * @return string data-* attributes for slide.
    */
    public function slide_data_attributes($slide, $slider){
        $data_attrib = array();
        if($slide['fx'] != 'default'){
            $data_attrib['data-cycle-fx'] = $slide['fx'];
        }
        if($slide['speed'] !== '') {
            $data_attrib['data-cycle-speed'] = $slide['speed'];
        }
        if($slide['timeout'] !== '') {
            $data_attrib['data-cycle-timeout'] = $slide['timeout'];
        }
        if($slide['fx']=='tileBlind' or $slide['fx']=='tileSlide'){
            if($slide['tile_count'] !== '') {
                $data_attrib['data-cycle-tile-count'] = $slide['tile_count'];
            }
            if($slide['tile_delay'] !== '') {
                $data_attrib['data-cycle-tile-delay'] = $slide['tile_delay'];
            }
            $data_attrib['data-cycle-tile-vertical'] = $slide['tile_vertical'];
        }
        
        $data_attrib = apply_filters('cycloneslider_slide_data_attributes', $data_attrib, $slide, $slider);
        
        $out = '';
        foreach($data_attrib as $data_attr=>$value){ // Array to html string
            $out .= ' '.$data_attr.'="'.esc_attr($value).'" ';
        }
        return $out;
    }

    /**
     * Trim White Spaces
     *
     */
    public function trim_white_spaces($buffer, $off=false){
        if($off){
            return $buffer;
        }
        $search = array(
            '/\>[^\S ]+/s', //strip whitespaces after tags, except space
            '/[^\S ]+\</s', //strip whitespaces before tags, except space
            '/(\s)+/s'  // shorten multiple whitespace sequences
        );
        $replace = array(
            '>',
            '<',
            '\\1'
        );
        return preg_replace($search, $replace, $buffer);
    }
    
    
    /*
     * Combine admin and shortcode settings
     */
    public function combine_slider_settings($admin_settings, $shortcode_settings ){
        // Use shortcode settings if present and override admin settings
        if( null !== $shortcode_settings['fx'] ){
            $admin_settings['fx'] = $shortcode_settings['fx'];
        }
        if( null !== $shortcode_settings['timeout'] ){
            $admin_settings['timeout'] = $shortcode_settings['timeout'];
        }
        if( null !== $shortcode_settings['speed'] ){
            $admin_settings['speed'] = $shortcode_settings['speed'];
        }
        if( null !== $shortcode_settings['width'] ){
            $admin_settings['width'] = $shortcode_settings['width'];
        }
        if( null !== $shortcode_settings['height'] ){
            $admin_settings['height'] = $shortcode_settings['height'];
        }
        if( null !== $shortcode_settings['hover_pause'] ){
            $admin_settings['hover_pause'] = $shortcode_settings['hover_pause'];
        }
        if( null !== $shortcode_settings['show_prev_next'] ){
            $admin_settings['show_prev_next'] = $shortcode_settings['show_prev_next'];
        }
        if( null !== $shortcode_settings['show_nav'] ){
            $admin_settings['show_nav'] = $shortcode_settings['show_nav'];
        }
        if( null !== $shortcode_settings['tile_count'] ){
            $admin_settings['tile_count'] = $shortcode_settings['tile_count'];
        }
        if( null !== $shortcode_settings['tile_delay'] ){
            $admin_settings['tile_delay'] = $shortcode_settings['tile_delay'];
        }
        if( null !== $shortcode_settings['tile_vertical'] ){
            $admin_settings['tile_vertical'] = $shortcode_settings['tile_vertical'];
        }
        if( null !== $shortcode_settings['random'] ){
            $admin_settings['random'] = $shortcode_settings['random'];
        }
        if( null !== $shortcode_settings['resize'] ){
            $admin_settings['resize'] = $shortcode_settings['resize'];
        }
        if( null !== $shortcode_settings['resize_option'] ){
            $admin_settings['resize_option'] = $shortcode_settings['resize_option'];
        }
        if( null !== $shortcode_settings['easing'] ){
            $admin_settings['easing'] = $shortcode_settings['easing'];
        }
        if( null !== $shortcode_settings['allow_wrap'] ){
            $admin_settings['allow_wrap'] = $shortcode_settings['allow_wrap'];
        }
        if( null !== $shortcode_settings['dynamic_height'] ){
            $admin_settings['dynamic_height'] = $shortcode_settings['dynamic_height'];
        }
        if( null !== $shortcode_settings['delay'] ){
            $admin_settings['delay'] = $shortcode_settings['delay'];
        }
        if( null !== $shortcode_settings['swipe'] ){
            $admin_settings['swipe'] = $shortcode_settings['swipe'];
        }
        if( null !== $shortcode_settings['width_management'] ){
            $admin_settings['width_management'] = $shortcode_settings['width_management'];
        }
        return $admin_settings;
    }
    
    /**
    * Gets the slider default settings. 
    *
    * @return array The array of slider defaults
    */
    public function get_slider_defaults(){
        return array(
            'template' => 'standard',
            'fx' => 'fade',
            'timeout' => '4000',
            'speed' => '1000',
            'width' => '960',
            'height' => '600',
            'hover_pause' => 'true',
            'show_prev_next' => '1',
            'show_nav' => '1',
            'tile_count' => '7',
            'tile_delay' => '100',
            'tile_vertical' => 'true',
            'random' => 0,
            'resize' => 1,
            'width_management' => 'responsive',
            'resize_option' => 'fit',
            'resize_quality' => 100,
            'easing' => '',
            'allow_wrap' => 'true',
            'dynamic_height' => 'off',
            'dynamic_height_speed' => 250,
            'delay' => 0,
            'swipe' => 'false'
        );
    }
    
    /**
    * Gets the slide default settings. 
    *
    * @return array The array of slide defaults
    */
    public function get_slide_defaults(){
        return array(
            'enable_slide_effects'=>0,
            'type' => 'image',
            'hidden' => 0,
            'id' => '',
            'link' => '',
            'title' => '',
            'description' => '',
            'link_target' => '_self',
            'fx' => 'default',
            'speed' => '',
            'timeout' => '',
            'tile_count' => '7',
            'tile_delay' => '100',
            'tile_vertical' => 'true',
            'img_alt' => '',
            'img_title' => '',
            
            'video_thumb' => '',
            'video_url' => '',
            'video' => '',
            
            'custom' => '',
            
            'youtube_url' => '',
            'youtube_related' => 'false',
            
            'vimeo_url' => '',
            
            'testimonial' => '',
            'testimonial_author' => '',
            'testimonial_link' => '',
            'testimonial_link_target' => '_self',
            'testimonial_img' => ''
        );
    }
    
    /**
    * Get Resize Options
    *
    * @return array The array of resize options in a key-value pair
    */
    public function get_resize_options(){
        if(version_compare(PHP_VERSION, '5.3', '>=')) { // 5.3+
            return array(
                'fit'      => __('Fit', 'cycloneslider'),
                'fill'     => __('Fill', 'cycloneslider'),
                'crop'     => __('Crop', 'cycloneslider'),
                'exact'      => __('Exact', 'cycloneslider'),
                'exactWidth' => __('Exact Width', 'cycloneslider'),
                'exactHeight'  => __('Exact Height', 'cycloneslider')
            );
        } else { // 5.2
            return array(
                'auto'      => __('Auto', 'cycloneslider'),
                'crop'      => __('Crop', 'cycloneslider'),
                'exact'     => __('Exact', 'cycloneslider'),
                'landscape' => __('Landscape', 'cycloneslider'),
                'portrait'  => __('Portrait', 'cycloneslider')
            );
        }
    }
    
    /**
    * Gets the slide effects. 
    *
    * @return array The array of supported slide effects
    */
    public function get_slide_effects(){
        return array(
            'fade'=>__('Fade', 'cycloneslider'),
            'fadeout'=>__('Fade Out', 'cycloneslider'),
            'none'=>__('None', 'cycloneslider'),
            'scrollHorz'=>__('Scroll Horizontally', 'cycloneslider'),
            'tileBlind'=>__('Tile Blind', 'cycloneslider'),
            'tileSlide'=>__('Tile Slide', 'cycloneslider')
        );
    }
    
    /**
     * Get array of jquery easing options
     *
     * @return array Easing options
     */
    public function get_jquery_easing_options(){
        return array(
            array(
                'text' => __('Default', 'cycloneslider'),
                'value' => ''
            ),
            array(
                'text' => __('Swing', 'cycloneslider'),
                'value' => 'swing'
            ),
            array(
                'text' => __('Ease-In Quad', 'cycloneslider'),
                'value' => 'easeInQuad'
            ),
            array(
                'text' => __('Ease-Out Quad', 'cycloneslider'),
                'value' => 'easeOutQuad'
            ),
            array(
                'text' => __('Ease-In OutQuad', 'cycloneslider'),
                'value' => 'easeInOutQuad'
            ),
            array(
                'text' => __('Ease-In Cubic', 'cycloneslider'),
                'value' => 'easeInCubic'
            ),
            array(
                'text' => __('Ease-Out Cubic', 'cycloneslider'),
                'value' => 'easeOutCubic'
            ),
            array(
                'text' => __('Ease-In OutCubic', 'cycloneslider'),
                'value' => 'easeInOutCubic'
            ),
            array(
                'text' => __('Ease-In Quart', 'cycloneslider'),
                'value' => 'easeInQuart'
            ),
            array(
                'text' => __('Ease-Out Quart', 'cycloneslider'),
                'value' => 'easeOutQuart'
            ),
            array(
                'text' => __('Ease-In OutQuart', 'cycloneslider'),
                'value' => 'easeInOutQuart'
            ),
            array(
                'text' => __('Ease-In Quint', 'cycloneslider'),
                'value' => 'easeInQuint'
            ),
            array(
                'text' => __('Ease-Out Quint', 'cycloneslider'),
                'value' => 'easeOutQuint'
            ),
            array(
                'text' => __('Ease-In OutQuint', 'cycloneslider'),
                'value' => 'easeInOutQuint'
            ),
            array(
                'text' => __('Ease-In Sine', 'cycloneslider'),
                'value' => 'easeInSine'
            ),
            array(
                'text' => __('Ease-Out Sine', 'cycloneslider'),
                'value' => 'easeOutSine'
            ),
            array(
                'text' => __('Ease-In OutSine', 'cycloneslider'),
                'value' => 'easeInOutSine'
            ),
            array(
                'text' => __('Ease-In Expo', 'cycloneslider'),
                'value' => 'easeInExpo'
            ),
            array(
                'text' => __('Ease-Out Expo', 'cycloneslider'),
                'value' => 'easeOutExpo'
            ),
            array(
                'text' => __('Ease-In OutExpo', 'cycloneslider'),
                'value' => 'easeInOutExpo'
            ),
            array(
                'text' => __('Ease-In Circ', 'cycloneslider'),
                'value' => 'easeInCirc'
            ),
            array(
                'text' => __('Ease-Out Circ', 'cycloneslider'),
                'value' => 'easeOutCirc'
            ),
            array(
                'text' => __('Ease-In OutCirc', 'cycloneslider'),
                'value' => 'easeInOutCirc'
            ),
            array(
                'text' => __('Ease-In Elastic', 'cycloneslider'),
                'value' => 'easeInElastic'
            ),
            array(
                'text' => __('Ease-Out Elastic', 'cycloneslider'),
                'value' => 'easeOutElastic'
            ),
            array(
                'text' => __('Ease-In OutElastic', 'cycloneslider'),
                'value' => 'easeInOutElastic'
            ),
            array(
                'text' => __('Ease-In Back', 'cycloneslider'),
                'value' => 'easeInBack'
            ),
            array(
                'text' => __('Ease-Out Back', 'cycloneslider'),
                'value' => 'easeOutBack'
            ),
            array(
                'text' => __('Ease-In OutBack', 'cycloneslider'),
                'value' => 'easeInOutBack'
            ),
            array(
                'text' => __('Ease-In Bounce', 'cycloneslider'),
                'value' => 'easeInBounce'
            ),
            array(
                'text' => __('Ease-Out Bounce', 'cycloneslider'),
                'value' => 'easeOutBounce'
            ),
            array(
                'text' => __('Ease-In OutBounce', 'cycloneslider'),
                'value' => 'easeInOutBounce'
            )
        );
    }

    /**
	 * Wrapper for WP get_posts.
	 *
	 * @param array $args The same as WP get_posts
	 *
	 * @return array An assoc array of posts or empty array
	 */
	public function get_posts( array $args ) {
		$posts   = get_posts( $args ); // Returns array
		$results = array(); // Store it here
		if ( ! empty( $posts ) and is_array( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$results[ $index ] = (array) $post; // Obj to assoc array
			}
		}
		return $results;
	}

    /**
	 * Wrapper for WP get_post_custom that automatically unserialize data.
	 *
	 * @param int $post_id ID of post
	 * @param string $key Meta key name
	 *
	 * @return array Array of data or empty array
	 */
	public function get_post_meta( $post_id, $key ) {
		$meta = get_post_custom( $post_id );
		if ( isset( $meta[ $key ][0] ) and ! empty( $meta[ $key ][0] ) ) {
			return maybe_unserialize( $meta[ $key ][0] );
		}
        return array();
	}

    private function _add_save_hook($wp_version){
		// Use better hook if available
		if ( version_compare( $wp_version, '3.7', '>=' ) ) {
			add_action( "save_post_cycloneslider", array( $this, 'save_post_hook' ) );
		} else {
			add_action( 'save_post', array( $this, 'save_post_hook' ) );
		}
	}

	private function _remove_save_hook($wp_version){
		// Use better hook if available
		if ( version_compare( $wp_version, '3.7', '>=' ) ) {
			remove_action( "save_post_cycloneslider", array( $this, 'save_post_hook' ) );
		} else {
			remove_action( 'save_post', array( $this, 'save_post_hook' ) );
		}
	}
}

