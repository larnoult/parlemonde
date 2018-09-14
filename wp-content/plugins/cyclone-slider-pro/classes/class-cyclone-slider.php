<?php
if(!class_exists('Cyclone_Slider')):

    class Cyclone_Slider {
        public $slider_count;
        private $cyclone_slider_templates; // Holds templates manager object
        private $cyclone_slider_scripts; // Holds slider scripts object
        private $cyclone_slider_data; // Holds cyclone slider data object
        private $cyclone_slider_youtube; // Holds youtube class
        private $cyclone_slider_vimeo; // Holds vimeo class
        private $view; // Holds cyclone slider view object
        
        /**
         * Initializes the plugin by setting localization, filters, and administration functions.
         */
        public function __construct( $cyclone_slider_scripts, $cyclone_slider_data, $view, $cyclone_slider_templates, $cyclone_slider_youtube, $cyclone_slider_vimeo ) {
            
            // Inject dependencies
            $this->cyclone_slider_scripts = $cyclone_slider_scripts;
            $this->cyclone_slider_data = $cyclone_slider_data;
            $this->cyclone_slider_templates = $cyclone_slider_templates;
            $this->cyclone_slider_youtube = $cyclone_slider_youtube;
            $this->cyclone_slider_vimeo = $cyclone_slider_vimeo;
            $this->view = $view;
            
            // Set defaults
            $this->slider_count = 0;
            
            // Register frontend styles and scripts
            add_action( 'wp_enqueue_scripts', array( $this->cyclone_slider_scripts, 'register_frontend_scripts' ), 100 );
            
            // Our shortcode
            add_shortcode('cycloneslider', array( $this, 'cycloneslider_shortcode') );
            
        } // end constructor
  
        /**
         * Cycloneslider Shortcode
         *
         * Displays shortcode on pages
         *
         * @param array $shortcode_settings Array of shortcode parameters
         * @return string Slider HTML
         */
        public function cycloneslider_shortcode( $shortcode_settings ) {
            
            $shortcode_settings = shortcode_atts(
                array(
                    'id' => 0,
                    'easing' => null,
                    'fx' => null,
                    'timeout' => null,
                    'speed' => null,
                    'width' => null,
                    'height' => null,
                    'hover_pause' => null,
                    'show_prev_next' => null,
                    'show_nav' => null,
                    'tile_count' => null,
                    'tile_delay' => null,
                    'tile_vertical' => null,
                    'random' => null,
                    'resize' => null,
                    'resize_option' => null,
                    'easing' => null,
                    'allow_wrap' => null,
                    'dynamic_height' => null,
                    'delay' => null,
                    'swipe' => null,
                    'width_management' => null
                ),
                $shortcode_settings,
                'cycloneslider'
            );
            
            $slider_slug = $shortcode_settings['id']; // Slideshow slug passed from shortcode
            
            $slider_count = ++$this->slider_count; // Make each call to shortcode unique
            
            $slider_html_id = 'cycloneslider-'.$slider_slug.'-'.$slider_count; // UID
            
            $slider = $this->cyclone_slider_data->get_slider_by_slug( $slider_slug ); // Get slider by slug
            
            // Abort if slider not found!
            if( $slider === false ){
                return sprintf(__('[Slideshow "%s" not found]', 'cycloneslider'), $slider_slug); 
            }
            
            // Assign important variables
            $slider_settings = $slider['slider_settings']; // Assign slider settings
            $slides = $slider['slides']; // Assign slides
            
            $template_name = $slider_settings['template'];
            $view_file = $this->get_view_file( $template_name );
            
            // Abort if template not found!
            if( $view_file === false ){
                return sprintf(__('[Template "%s" not found]', 'cycloneslider'), $template_name);
            }
            
            // Use shortcode settings if present and override admin settings
            if( null !== $shortcode_settings['fx'] ){
                $slider_settings['fx'] = $shortcode_settings['fx'];
            }
            if( null !== $shortcode_settings['timeout'] ){
                $slider_settings['timeout'] = $shortcode_settings['timeout'];
            }
            if( null !== $shortcode_settings['speed'] ){
                $slider_settings['speed'] = $shortcode_settings['speed'];
            }
            if( null !== $shortcode_settings['width'] ){
                $slider_settings['width'] = $shortcode_settings['width'];
            }
            if( null !== $shortcode_settings['height'] ){
                $slider_settings['height'] = $shortcode_settings['height'];
            }
            if( null !== $shortcode_settings['hover_pause'] ){
                $slider_settings['hover_pause'] = $shortcode_settings['hover_pause'];
            }
            if( null !== $shortcode_settings['show_prev_next'] ){
                $slider_settings['show_prev_next'] = $shortcode_settings['show_prev_next'];
            }
            if( null !== $shortcode_settings['show_nav'] ){
                $slider_settings['show_nav'] = $shortcode_settings['show_nav'];
            }
            if( null !== $shortcode_settings['tile_count'] ){
                $slider_settings['tile_count'] = $shortcode_settings['tile_count'];
            }
            if( null !== $shortcode_settings['tile_delay'] ){
                $slider_settings['tile_delay'] = $shortcode_settings['tile_delay'];
            }
            if( null !== $shortcode_settings['tile_vertical'] ){
                $slider_settings['tile_vertical'] = $shortcode_settings['tile_vertical'];
            }
            if( null !== $shortcode_settings['random'] ){
                $slider_settings['random'] = $shortcode_settings['random'];
            }
            if( null !== $shortcode_settings['resize'] ){
                $slider_settings['resize'] = $shortcode_settings['resize'];
            }
            if( null !== $shortcode_settings['resize_option'] ){
                $slider_settings['resize_option'] = $shortcode_settings['resize_option'];
            }
            if( null !== $shortcode_settings['easing'] ){
                $slider_settings['easing'] = $shortcode_settings['easing'];
            }
            if( null !== $shortcode_settings['allow_wrap'] ){
                $slider_settings['allow_wrap'] = $shortcode_settings['allow_wrap'];
            }
            if( null !== $shortcode_settings['dynamic_height'] ){
                $slider_settings['dynamic_height'] = $shortcode_settings['dynamic_height'];
            }
            if( null !== $shortcode_settings['delay'] ){
                $slider_settings['delay'] = $shortcode_settings['delay'];
            }
            if( null !== $shortcode_settings['swipe'] ){
                $slider_settings['swipe'] = $shortcode_settings['swipe'];
            }
            if( null !== $shortcode_settings['width_management'] ){
                $slider_settings['width_management'] = $shortcode_settings['width_management'];
            }
            
            $image_count = 0; // Number of image slides
            $video_count = 0; // Number of video slides
            $custom_count = 0; // Number of custom slides
            $youtube_count = 0; // Number of youtube slides
            $vimeo_count = 0; // Number of Vimeo slides
            
            // Do some last minute logic
            // Translations and counters
            foreach($slides as $i=>$slide){
                $slides[$i]['title'] = __($slide['title']);
                $slides[$i]['description'] = __($slide['description']);
                if($slides[$i]['type']=='image'){
                    
                    list($full_image_url, $orig_width, $orig_height) = wp_get_attachment_image_src($slide['id'], 'full');
                    
                    $slides[$i]['full_image_url'] = $full_image_url;
                    $slides[$i]['image_url'] = $this->cyclone_slider_data->get_slide_image_url( $slide['id'], $slider_settings );
                    
                    $image_count++;
                } else if($slides[$i]['type']=='video'){
                    $video_count++;
                } else if($slides[$i]['type']=='custom'){
                    $custom_count++;
                } else if($slides[$i]['type']=='youtube'){
                    $youtube_count++;
                    $youtube_id = $this->cyclone_slider_youtube->get_youtube_id($slides[$i]['youtube_url']);
                    
                    $youtube_related = '';
                    if( 'true' == $slides[$i]['youtube_related'] ) {
                        $youtube_related = '&rel=0';
                    }
                    
                    $slides[$i]['youtube_embed_code'] = '<iframe id="'.$slider_html_id.'-iframe-'.$i.'" width="'.$slider_settings['width'].'" height="'.$slider_settings['height'].'" src="//www.youtube.com/embed/'.$youtube_id.'?wmode=transparent'.$youtube_related.'" frameborder="0" allowfullscreen></iframe>';
                    $slides[$i]['youtube_id'] = $youtube_id;
                    $slides[$i]['thumbnail_small'] = $this->cyclone_slider_youtube->get_youtube_thumb($youtube_id);
                    
                } else if($slides[$i]['type']=='vimeo'){
                    $vimeo_count++;
                    $vimeo_id = $this->cyclone_slider_vimeo->get_vimeo_id($slides[$i]['vimeo_url']);
                    
                    $slides[$i]['vimeo_embed_code'] = '<iframe id="'.$slider_html_id.'-iframe-'.$i.'" width="'.$slider_settings['width'].'" height="'.$slider_settings['height'].'" src="http://player.vimeo.com/video/'.$vimeo_id.'?api=1&wmode=transparent" frameborder="0"  webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                    $slides[$i]['vimeo_id'] = $vimeo_id;
                    $slides[$i]['thumbnail_small'] = $this->cyclone_slider_vimeo->get_vimeo_thumb($vimeo_id);
                }
            }
            
            // Randomize slides
            if($slider_settings['random']){
                shuffle($slides);
            }
            
            // Assign view file
            $this->view->set_view_file( $view_file );
           
            // Hardcoded for now
            $slider_settings['hide_non_active'] = "true";
            $slider_settings['auto_height'] = "{$slider_settings['width']}:{$slider_settings['height']}"; // Use ratio for backward compat
            if( 'on' == $slider_settings['dynamic_height'] ) {
                $slider_settings['auto_height'] = 0; // Disable autoheight when dynamic height is on. To prevent slider returning to wrong (ratio height) height when browser is resized.
            }
            if( ($youtube_count+$vimeo_count) > 0 or  'on' == $slider_settings['dynamic_height'] ){ 
                $slider_settings['hide_non_active'] = "false"; // Do not hide non active slides to prevent reloading of videos and for getBoundingClientRect() to not return 0.
            }
            $slider_settings['auto_height_speed'] = 250; // Will be editable in admin in the future
            $slider_settings['auto_height_easing'] = "null"; // Will be editable in admin in the future
            
            // Pass this vars to template
            $vars = array();
            $vars['slider_html_id'] = $slider_html_id; // The unique HTML ID for slider
            $vars['slider_count'] = $slider_count;
            $vars['slides'] = $slides;
            $vars['image_count'] = $image_count;
            $vars['video_count'] = $video_count;
            $vars['custom_count'] = $custom_count;
            $vars['youtube_count'] = $youtube_count;
            $vars['slider_id'] = $slider_slug; // (Deprecated since 2.6.0, use $slider_html_id instead) Unique string to identify the slideshow.
            $vars['slider_metas'] = $slides; // (Deprecated since 2.5.5, use $slides instead) An array containing slides properties.
            $vars['slider_settings'] = $slider_settings;
            
            $this->view->set_vars( $vars );
            $slider_html = $this->view->get_render();
            
            // Remove whitespace to prevent WP from adding rogue paragraphs
            $slider_html = $this->trim_white_spaces( $slider_html );
            
            // Return HTML
            return $slider_html;
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
        
        /**
         * Get View File
         *
         * Get slider view file from theme or plugin location
         *
         * @param string $template_name Name of slider template
         * @return string|false Slider view filepath or false
         */
        public function get_view_file( $template_name ){
            
            $template_locations = $this->cyclone_slider_templates->get_template_locations();
            $template_locations = array_reverse($template_locations); // Last added template locations are checked first
            foreach($template_locations as $template_location){
                $view_file = $template_location['path']."{$template_name}/slider.php";
                if(@is_file($view_file)){
                    return $view_file;
                }
            }

            return false;
        }
    } // end class
    
endif;