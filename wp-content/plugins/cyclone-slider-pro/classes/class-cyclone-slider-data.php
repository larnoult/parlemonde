<?php
if(!class_exists('Cyclone_Slider_Data')):

    /**
     * Class for saving and getting slider data  
     */
    class Cyclone_Slider_Data {
        
        public $nonce_name;
        public $nonce_action;
        private $cyclone_slider_image_resizer;
        
        /**
         * Initializes the class
         */
        public function __construct( $cyclone_slider_image_resizer ){
            $this->nonce_name = 'cyclone_slider_builder_nonce'; // Must match with the one in class-cyclone-slider-admin.php
            $this->nonce_action = 'cyclone-slider-save'; // Must match with the one in class-cyclone-slider-admin.php
            
            $this->cyclone_slider_image_resizer = $cyclone_slider_image_resizer;
            
            // Save slides
            add_action( 'save_post', array( $this, 'save_slider_post' ) );
        }
        
        /**
         * Save post hook
         */
        public function save_slider_post( $post_id ){
            global $cyclone_slider_saved_done;
            
            // Stop! We have already saved..
            if($cyclone_slider_saved_done){
                return $post_id;
            }
            
            // Verify nonce
            $nonce_name = $this->nonce_name;
            if (!empty($_POST[$nonce_name])) {
                if (!wp_verify_nonce($_POST[$nonce_name], $this->nonce_action)) {
                    return $post_id;
                }
            } else {
                return $post_id; // Make sure we cancel on missing nonce!
            }
            
            // Check autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            
            // Resize images if needed
            if($_POST['cycloneslider_settings']['resize'] == 1){
                $this->cyclone_slider_image_resizer->resize_images( $_POST['cycloneslider_settings'], $_POST['cycloneslider_metas'] );

            }
            
            // Save slides
            $this->add_slider_slides( $post_id, $_POST['cycloneslider_metas'] );
            
            // Save slider settings
            $this->add_slider_settings( $post_id, $_POST['cycloneslider_settings']);
            
            // Marked as done
            $cyclone_slider_saved_done = true;
        }
        
        /**
         * API to add slider
         */
        public function add_slider( $post_title, $slider_settings, $slides ){
            global $cyclone_slider_saved_done;
            
            $cyclone_slider_saved_done= true; // Prevent double whammy!

            $post_data = array(
                'post_type' => 'cycloneslider',
                'post_title' => $post_title,
                'post_content' => '',
                'post_status' => 'publish'
            );

            if( $slider_id = wp_insert_post( $post_data ) ){
                
                // Resize images if needed
                if( $slider_settings['resize'] == 1){
                    $this->cyclone_slider_image_resizer->resize_images( $slider_settings, $slides );
                }
                
                // Save slides
                $this->add_slider_slides( $slider_id, $slides );
                
                // Save slider settings
                $this->add_slider_settings( $slider_id, $slider_settings );
            }
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
        public function add_slider_settings( $slider_id, $settings ){
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
            $settings_to_save['easing'] = sanitize_text_field( $settings['easing'] );
            $settings_to_save['resize_option'] = sanitize_text_field( $settings['resize_option'] );
            $settings_to_save['allow_wrap'] = sanitize_text_field( $settings['allow_wrap'] );
            $settings_to_save['dynamic_height'] = sanitize_text_field( $settings['dynamic_height'] );
            $settings_to_save['delay'] = (int) ( $settings['delay'] );
            $settings_to_save['swipe'] = sanitize_text_field( $settings['swipe'] );
            
            $settings_to_save = apply_filters('cycloneslider_settings', $settings_to_save, $slider_id); // Apply filters before saving
            
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
         * @return void
         */
        public function add_slider_slides( $slider_id, $slides ){
            
            $slides_to_save = array();
            
            if( is_array($slides) ){

                $i=0;//always start from 0
                foreach($slides as $slide){
                    $slide = wp_parse_args(
                        $slide,
                        $this->get_slide_defaults()
                    );
                    $slides_to_save[$i]['id'] = (int) ($slide['id']);
                    $slides_to_save[$i]['type'] = sanitize_text_field($slide['type']);
                    
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
                    
                    $slides_to_save[$i]['youtube_url'] = $slide['youtube_url'];
                    $slides_to_save[$i]['youtube_related'] = $slide['youtube_related'];
                    
                    $slides_to_save[$i]['vimeo_url'] = $slide['vimeo_url'];
                
                    $i++;
                }
                
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
        * Get Sliders
        *
        * Get all sliders and their accompanying meta data
        * 
        * @return array|false The array of sliders post and their meta data or false on fail
        */
        public function get_sliders( $args=array() ){
            $defaults = array(
                'post_type' => 'cycloneslider',
                'numberposts' => -1 // Get all
            );
            $args = wp_parse_args($args, $defaults);
            
            $slider_posts = get_posts( $args ); // Use get_posts to avoid filters
            $sliders = array(); // Store it here
            if( !empty($slider_posts) and is_array($slider_posts) ){
                foreach($slider_posts as $index=>$slider_post){
                    $sliders[$index] = (array) $slider_post;
                    $sliders[$index]['slider_settings'] = $this->get_slider_settings( $slider_post->ID );
                    $sliders[$index]['slides'] = $this->get_slider_slides( $slider_post->ID );
                }
                return $sliders;
            } else {
                return false;
            }
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
            $meta = get_post_custom( $slider_id );
            $slider_settings = array();
            if(isset($meta['_cycloneslider_settings'][0]) and !empty($meta['_cycloneslider_settings'][0])){
                $slider_settings = maybe_unserialize($meta['_cycloneslider_settings'][0]);
            }
            $slider_settings = wp_parse_args($slider_settings, $this->get_slider_defaults() );
            return apply_filters('cycloneslider_get_slider_settings', $slider_settings);
        }
        
        /**
        * Get Slider Slides
        *
        * @param int $slider_id Post ID of the slider custom post.
        * @return array The array of slides settings
        */
        public function get_slider_slides( $slider_id ){
            $meta = get_post_custom( $slider_id );
            
            if(isset($meta['_cycloneslider_metas'][0]) and !empty($meta['_cycloneslider_metas'][0])){
                $slides = maybe_unserialize($meta['_cycloneslider_metas'][0]);
                $defaults = $this->get_slide_defaults();
                
                foreach($slides as $i=>$slide){
                    $slides[$i] = wp_parse_args($slide, $defaults);
                }
                
                return apply_filters('cycloneslider_get_slider_slides', $slides);
            }
            return false;
        }
        
        /**
         * Get Slider by Slug
         *
         * @param string $slug Post slug of the slider custom post.
         * @return array|false The array of slider post and associated meta or false if none found
         */
        public function get_slider_by_slug( $slug ) {
            // Get slider by id
            $args = array(
                'post_type' => 'cycloneslider',
                'numberposts' => 1,
                'name'=> $slug
            );

            $slider_posts = get_posts( $args ); // Use get_posts to avoid filters

            $sliders = array(); // Store it here
            if( !empty($slider_posts) and is_array($slider_posts) ){
                foreach($slider_posts as $index=>$slider_post){
                    $sliders[$index] = (array) $slider_post;
                    $sliders[$index]['slider_settings'] = $this->get_slider_settings( $slider_post->ID );
                    $sliders[$index]['slides'] = $this->get_slider_slides( $slider_post->ID );
                }
                return $sliders[0];
            }
            
            return false;
        }
        
        /**
        * Gets the number of slides of slideshow
        *
        * @param int $slider_id Post ID of slider custom post
        * @return int|0 Total images or zero
        */
        public function get_slide_count( $slider_id ){
            $meta = get_post_custom( $slider_id );
            
            if(isset($meta['_cycloneslider_metas'][0]) and !empty($meta['_cycloneslider_metas'][0])){
                $slides = maybe_unserialize($meta['_cycloneslider_metas'][0]);
                
                return count($slides);
            }
            return 0;
        }
        
        /**
        * Get Slide Image URL
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
                'resize_option' => 'auto',
                'easing' => '',
                'allow_wrap' => 'true',
                'dynamic_height' => 'off',
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
                
                'vimeo_url' => ''
            );
        }
        
        /**
        * Get Resize Options
        *
        * @return array The array of resize options in a key-value pair
        */
        public function get_resize_options(){
            return array(
                'auto' => 'Auto',
                'crop' => 'Crop',
                'exact' => 'Exact',
                'landscape' => 'Landscape',
				'portrait' => 'Portrait'
            );
        }
        
        /**
        * Gets the slide effects. 
        *
        * @return array The array of supported slide effects
        */
        public function get_slide_effects(){
            return array(
                'fade'=>'Fade',
                'fadeout'=>'Fade Out',
                'none'=>'None',
                'scrollHorz'=>'Scroll Horizontally',
                'tileBlind'=>'Tile Blind',
                'tileSlide'=>'Tile Slide'
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
                    'text' => 'Default',
                    'value' => ''
                ),
                array(
                    'text' => 'Swing',
                    'value' => 'swing'
                ),
                array(
                    'text' => 'Ease-In Quad',
                    'value' => 'easeInQuad'
                ),
                array(
                    'text' => 'Ease-Out Quad',
                    'value' => 'easeOutQuad'
                ),
                array(
                    'text' => 'Ease-In OutQuad',
                    'value' => 'easeInOutQuad'
                ),
                array(
                    'text' => 'Ease-In Cubic',
                    'value' => 'easeInCubic'
                ),
                array(
                    'text' => 'Ease-Out Cubic',
                    'value' => 'easeOutCubic'
                ),
                array(
                    'text' => 'Ease-In OutCubic',
                    'value' => 'easeInOutCubic'
                ),
                array(
                    'text' => 'Ease-In Quart',
                    'value' => 'easeInQuart'
                ),
                array(
                    'text' => 'Ease-Out Quart',
                    'value' => 'easeOutQuart'
                ),
                array(
                    'text' => 'Ease-In OutQuart',
                    'value' => 'easeInOutQuart'
                ),
                array(
                    'text' => 'Ease-In Quint',
                    'value' => 'easeInQuint'
                ),
                array(
                    'text' => 'Ease-Out Quint',
                    'value' => 'easeOutQuint'
                ),
                array(
                    'text' => 'Ease-In OutQuint',
                    'value' => 'easeInOutQuint'
                ),
                array(
                    'text' => 'Ease-In Sine',
                    'value' => 'easeInSine'
                ),
                array(
                    'text' => 'Ease-Out Sine',
                    'value' => 'easeOutSine'
                ),
                array(
                    'text' => 'Ease-In OutSine',
                    'value' => 'easeInOutSine'
                ),
                array(
                    'text' => 'Ease-In Expo',
                    'value' => 'easeInExpo'
                ),
                array(
                    'text' => 'Ease-Out Expo',
                    'value' => 'easeOutExpo'
                ),
                array(
                    'text' => 'Ease-In OutExpo',
                    'value' => 'easeInOutExpo'
                ),
                array(
                    'text' => 'Ease-In Circ',
                    'value' => 'easeInCirc'
                ),
                array(
                    'text' => 'Ease-Out Circ',
                    'value' => 'easeOutCirc'
                ),
                array(
                    'text' => 'Ease-In OutCirc',
                    'value' => 'easeInOutCirc'
                ),
                array(
                    'text' => 'Ease-In Elastic',
                    'value' => 'easeInElastic'
                ),
                array(
                    'text' => 'Ease-Out Elastic',
                    'value' => 'easeOutElastic'
                ),
                array(
                    'text' => 'Ease-In OutElastic',
                    'value' => 'easeInOutElastic'
                ),
                array(
                    'text' => 'Ease-In Back',
                    'value' => 'easeInBack'
                ),
                array(
                    'text' => 'Ease-Out Back',
                    'value' => 'easeOutBack'
                ),
                array(
                    'text' => 'Ease-In OutBack',
                    'value' => 'easeInOutBack'
                ),
                array(
                    'text' => 'Ease-In Bounce',
                    'value' => 'easeInBounce'
                ),
                array(
                    'text' => 'Ease-Out Bounce',
                    'value' => 'easeOutBounce'
                ),
                array(
                    'text' => 'Ease-In OutBounce',
                    'value' => 'easeInOutBounce'
                )
            );
        }
    }
    
endif;
