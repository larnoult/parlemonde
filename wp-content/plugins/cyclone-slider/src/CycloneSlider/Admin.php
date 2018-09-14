<?php
/**
* Class for displaying the admin screen
*/
class CycloneSlider_Admin {

	/**
     * @var int
     */
    public $slider_count;
	/**
     * @var int
     */
    protected $message_id;
	/**
     * @var CycloneSlider_AssetLoader
     */
    protected $asset_loader;
	/**
     * @var CycloneSlider_Data
     */
    protected $data;
	/**
     * @var boolean
     */
    protected $debug;
	/**
     * @var CycloneSlider_View
     */
    protected $view;
	/**
     * @var string
     */
    protected $nonce_name;
	/**
     * @var string
     */
    protected $nonce_action;
	/**
     * @var string
     */
    protected $url;

	/**
     * CycloneSlider_Admin constructor.
     *
     * @param $asset_loader
     * @param $data
     * @param $debug
     * @param $view
     * @param $nonce_name
     * @param $nonce_action
     * @param $url
     */
    public function __construct( $asset_loader, $data, $debug, $view, $nonce_name, $nonce_action, $url ){
        $this->asset_loader = $asset_loader;
        $this->data = $data;
        $this->debug = $debug;
        $this->view = $view;
        $this->nonce_name = $nonce_name;
        $this->nonce_action = $nonce_action;
        $this->url = $url;
    }


	/**
     * Auto run
     */
    public function run() {
        
        // Set defaults
        $this->slider_count = 0;

        // Register admin styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this->asset_loader, 'register_admin_scripts' ), 10);
        
        // Register frontend styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this->asset_loader, 'register_frontend_scripts_in_admin' ), 100 );
        
        // Add admin menus
        add_action( 'init', array( $this, 'create_post_types' ) );
        
        // Change admin menu icon
        add_action( 'admin_init', array( $this, 'change_admin_menu_icon' ) );
        
        // Update the messages for our custom post make it appropriate for slideshow
        add_filter('post_updated_messages', array( $this, 'post_updated_messages' ) );
        
        // Remove metaboxes
        add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
        
        // Add slider metaboxes
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        
        // Hacky way to change text in thickbox
        add_filter( 'gettext', array( $this, 'replace_text_in_thickbox' ), 10, 3 );
        
        // Modify html of image
        add_filter( 'image_send_to_editor', array( $this, 'image_send_to_editor'), 1, 8 );
        
        // Custom columns
        add_action( 'manage_cycloneslider_posts_custom_column', array( $this, 'custom_column' ), 10, 2);
        add_filter( 'manage_edit-cycloneslider_columns', array( $this, 'slideshow_columns') );
        
        // Add hook for admin footer
        add_action('admin_footer', array( $this, 'admin_footer') );
        
        // Add body css for custom styling when on our page
        add_filter('admin_body_class', array( $this, 'body_class' ) );

    
        // Add hook for ajax operations if logged in
        add_action( 'wp_ajax_cycloneslider_get_video', array( $this, 'cycloneslider_get_video' ) );
        
    }

    /**
     * Add js and css for WP media manager.
     *
     * @param $classes
     *
     * @return string
     */
    function body_class( $classes ) {
        if('cycloneslider' == get_post_type()){
            $classes .= 'cycloneslider';
        }
        return $classes;
    }
    
    /**
     * Create Post Types
     *
     * Create custom post for slideshows
     */
    public function create_post_types() {
        register_post_type( 'cycloneslider',
            array(
                'labels' => array(
                    'name' => __('Cyclone Slider', 'cycloneslider'),
                    'singular_name' => __('Slideshow', 'cycloneslider'),
                    'add_new' => __('Add Slideshow', 'cycloneslider'),
                    'add_new_item' => __('Add New Slideshow', 'cycloneslider'),
                    'edit_item' => __('Edit Slideshow', 'cycloneslider'),
                    'new_item' => __('New Slideshow', 'cycloneslider'),
                    'view_item' => __('View Slideshow', 'cycloneslider'),
                    'search_items' => __('Search Slideshows', 'cycloneslider'),
                    'not_found' => __('No slideshows found', 'cycloneslider'),
                    'not_found_in_trash' => __('No slideshows found in Trash', 'cycloneslider')
                ),
                'supports' => array('title'),
                'public' => false,
                'exclude_from_search' => true,
                'show_ui' => true,
                'menu_position' => 100,
                'can_export' => false // Exclude from export
            )
        );
    }
    
    /**
     * Change Icon
     */
    public function change_admin_menu_icon() {
        
        global $menu, $wp_version;

        if(!isset($menu) and !is_array($menu)) {
            return false; // Abort
        }

        foreach( $menu as $key => $value ) {
            if( 'edit.php?post_type=cycloneslider' == $value[2] ) {
                if ( version_compare( $wp_version, '3.9', '<' ) ) { // WP 3.8 and below
                    $menu[$key][4] = str_replace('menu-icon-post', 'menu-icon-media', $menu[$key][4]);
                } else { // WP 3.9+
                    $menu[$key][6] = 'dashicons-format-gallery';
                }

            }
        }
    }

    /**
     * Add custom messages
     *
     * @param $messages
     *
     * @return array Messages for cyclone
     */
    public function post_updated_messages($messages){
        global $post, $post_ID;
        $messages['cycloneslider'] = array(
            0  => '',
            1  => __( 'Slideshow updated.', 'cycloneslider' ),
            2  => __( 'Custom field updated.', 'cycloneslider' ),
            3  => __( 'Custom field deleted.', 'cycloneslider' ),
            4  => __( 'Slideshow updated.', 'cycloneslider' ),
            5  => __( 'Slideshow updated.', 'cycloneslider' ),
            6  => __( 'Slideshow published.', 'cycloneslider' ),
            7  => __( 'Slideshow saved.', 'cycloneslider' ),
            8  => __( 'Slideshow updated.', 'cycloneslider' ),
            9  => __( 'Slideshow updated.', 'cycloneslider' ),
            10 => __( 'Slideshow updated.', 'cycloneslider' )
        );
        return $messages;
    }

    /**
     * Show custom messages
     *
     * @param $location
     *
     * @return array The array of locations containing path and url
     */
    public function throw_message($location) {
        $location = add_query_arg( 'message', $this->message_id, $location );
        $this->message_id = 0;
        return $location;
    }
    
    /**
     * Remove Meta Boxes
     *
     * Remove built-in metaboxes from our custom post type
     */
    public function remove_meta_boxes(){
        remove_meta_box('slugdiv', 'cycloneslider', 'normal');
    }
    
    /**
     * Add Meta Boxes
     *
     * Add custom metaboxes to our custom post type
     */
    public function add_meta_boxes(){
        
        add_meta_box(
            'cyclone-slides-metabox',
            __('Slides', 'cycloneslider'),
            array( $this, 'render_slides_meta_box' ),
            'cycloneslider' ,
            'normal',
            'high'
        );
        
        add_meta_box(
            'cyclone-slider-preview-metabox',
            __('Slider Preview', 'cycloneslider'),
            array( $this, 'render_slider_preview_meta_box' ),
            'cycloneslider' ,
            'side',
            'high'
        );
        
        add_meta_box(
            'cyclone-slider-codes',
            __('Get Slider Codes', 'cycloneslider'),
            array( $this, 'render_slider_codes' ),
            'cycloneslider' ,
            'side',
            'low'
        );
        
        add_meta_box(
            'cyclone-slider-properties-metabox',
            __('Basic Settings', 'cycloneslider'),
            array( $this, 'render_slider_properties_meta_box' ),
            'cycloneslider' ,
            'side',
            'low'
        );
        
        add_meta_box(
            'cyclone-slider-advanced-settings-metabox',
            __('Advanced Settings', 'cycloneslider'),
            array( $this, 'render_slider_advanced_settings_meta_box' ),
            'cycloneslider' ,
            'side',
            'low'
        );
        
        add_meta_box(
            'cyclone-slider-templates-metabox',
            __('Templates', 'cycloneslider'),
            array( $this, 'render_slider_templates_meta_box' ),
            'cycloneslider' ,
            'normal',
            'low'
        );
        
        add_meta_box(
            'cyclone-slider-id',
            __('Slideshow ID', 'cycloneslider'),
            array( $this, 'render_slider_id' ),
            'cycloneslider' ,
            'normal',
            'low'
        );
    }


    /**
     * Metabox for slides
     *
     * @param $post
     */
    public function render_slides_meta_box($post){
        
        try {
            $slider = $this->data->get_slider($post->ID);

            if($slider === NULL){
                $this->view->render('slides.php', array('error'=> sprintf(__('Slider "%s" not found.','cycloneslider'), $post->ID) ));
            } else {
                $slides_html = '';
                foreach($slider['slides'] as $i=>$slide) {
                    
                    $image_url = $this->get_slide_img_thumb($slide['id']);
                    $image_url = apply_filters('cycloneslider_preview_url', $image_url, $slide);
                    $box_title = __('Slide', 'cycloneslider').' '.($i+1);
                    if( '' != trim($slide['title']) and 'image' == $slide['type'] ){
                        $box_title = $box_title. ' - '.$slide['title'];
                    }
                    if( '1' == $slide['hidden'] ){
                        $box_title = $box_title. ' - '.__('[Hidden]', 'cycloneslider');
                    }
                    $box_title = apply_filters('cycloneslider_box_title', $box_title);
                    
                    $vars = array();
                    $vars['i'] = $i;
                    $vars['slider'] = $slider;
                    $vars['slide'] = $slide;
                    $vars['image_url'] = $image_url;
                    $vars['full_image_url'] = wp_get_attachment_url($slide['id']);
                    $vars['testimonial_img_url'] = $this->get_slide_img_thumb($slide['testimonial_img']);
                    $vars['full_testimonial_img_url'] = wp_get_attachment_url($slide['testimonial_img']);
                    $vars['box_title'] = $box_title;
                    $vars['debug'] = ($this->debug) ? cyclone_slider_debug($slide) : '';
                    $vars['effects'] = $this->data->get_slide_effects();
                    
                    $slides_html .= $this->view->get_render('slide-edit.php', $vars);
                }
                
                $vars = array();
                $vars['error'] = '';
                $vars['slides'] = $slides_html;
                $vars['post_id'] = $post->ID;
                $vars['nonce_name'] = $this->nonce_name;
                $vars['nonce'] = wp_create_nonce( $this->nonce_action );
            
                $this->view->render('slides.php', $vars);
            }
        } catch (Exception $e) {
            $this->view->render('slides.php', array('error'=> $e->getMessage()));
        }
    }

    /**
     * Metabox for slider codes
     *
     * @param $post
     */
    public function render_slider_codes( $post ){
        
        $vars = array();
        $vars['post'] = $post;
        if(empty($post->post_name)){
            $vars['shortcode'] = '';
            $vars['template_code'] = '';
        } else {
            $vars['shortcode'] = '[cycloneslider id="'.$post->post_name.'"]';
            $vars['template_code'] = '<?php if( function_exists(\'cyclone_slider\') ) cyclone_slider(\''.$post->post_name.'\'); ?>';
        }
        
        $this->view->render('slider-codes.php', $vars);

    }

    /**
     * Metabox for basic settings
     *
     * @param $post
     */
    public function render_slider_properties_meta_box( $post ){
        $slider_settings = $this->data->get_slider_settings( $post->ID );
        
        $vars = array();
        $vars['slider_settings'] = $slider_settings;
        $vars['effects'] = $this->data->get_slide_effects();
        $vars['debug'] = ($this->debug) ? cyclone_slider_debug($slider_settings) : '';
        
        $this->view->render('slider-settings.php', $vars);

    }

    /**
     * Metabox for advanced settings
     *
     * @param $post
     */
    public function render_slider_advanced_settings_meta_box( $post ){
        $slider_settings = $this->data->get_slider_settings( $post->ID );
        
        $vars = array();
        $vars['slider_settings'] = $slider_settings;
        $vars['easing_options'] = $this->data->get_jquery_easing_options();
        $vars['resize_options'] = $this->data->get_resize_options();
        $vars['debug'] = ($this->debug) ? cyclone_slider_debug($slider_settings) : '';
        
        $this->view->render( 'slider-advanced-settings.php', $vars );

    }


    /**
     * Metabox for preview
     *
     * @param $post
     */
    public function render_slider_preview_meta_box($post){
        
        $vars = array();
        $vars['post'] = $post;
        if(empty($post->post_name)){
            $vars['shortcode'] = '';
            $vars['template_code'] = '';
        } else {
            $vars['shortcode'] = '[cycloneslider id="'.$post->post_name.'"]';
            $vars['template_code'] = '<?php if( function_exists(\'cyclone_slider\') ) cyclone_slider(\''.$post->post_name.'\'); ?>';
        }
        
        $this->view->render('slider-preview.php', $vars);
    }

    /**
     * Metabox for templates
     *
     * @param $post
     */
    public function render_slider_templates_meta_box($post){

        $slider_settings = $this->data->get_slider_settings($post->ID);
        $templates = $this->data->get_all_templates();

        $settings_data = $this->data->get_settings_page_data(); // Get checked templates
		$active_templates = $this->data->get_enabled_templates($settings_data, $templates);
        
        ksort ( $templates ); // Sort assoc array alphabetically
        foreach($templates as $name=>$template){
            $templates[$name]['name'] = $template['name'];
            if( $name == $slider_settings['template'] ){
                $templates[$name]['selected'] = true;
            } else {
                $templates[$name]['selected'] = false;
            }
            
            if( file_exists($template['path'].'/screenshot.jpg') ) {
                $templates[$name]['screenshot'] = $template['url'].'/screenshot.jpg';
            } else {
                $templates[$name]['screenshot'] = $this->url.'images/screenshot.png';
            }
            
            $templates[$name]['warning'] = '';
        
            if( $template['location_name'] == 'core' ){
                $templates[$name]['location_name'] = __('Core', 'cycloneslider');
                $templates[$name]['location_details'] = sprintf( __('You should not make changes to templates in this location. All your changes will be gone when the plugin is updated. <br />Location: <strong>%s</strong>', 'cycloneslider' ), $template['path']);
            }
            if( $template['location_name'] == 'active-theme' ){
                $templates[$name]['location_name'] = __('Active Theme', 'cycloneslider');
                $templates[$name]['location_details'] = sprintf( __("Location: <strong>%s</strong>", 'cycloneslider' ), $template['path']);
                $templates[$name]['warning'] = sprintf( __('Your template is in danger of being overwritten when you upgrade your theme. Please consider creating a WordPress plugin Cyclone Slider template.', 'cycloneslider' ) );
            }
            if( $template['location_name'] == 'wp-content' ){
                $templates[$name]['location_name'] = __('WP Content', 'cycloneslider');
                $templates[$name]['location_details'] = sprintf( __("Location: <strong>%s</strong>", 'cycloneslider'), $template['path'] );
            }
            if( $template['location_name'] == 'plugin' ){
                $templates[$name]['location_name'] = __('Plugin', 'cycloneslider');
                $templates[$name]['location_details'] = sprintf( __("Location: <strong>%s</strong>", 'cycloneslider'), $template['path'] );
            }
            // Remove inactive templates
            if($active_templates[$name]==0){
                unset($templates[$name]);
            }
        }
        
        $vars = array();
        $vars['slider_settings'] = $slider_settings;
        $vars['templates'] = $templates;
        $vars['debug'] = ($this->debug) ? cyclone_slider_debug($templates) : '';
        
        $this->view->render('template-selection.php', $vars);
    }

    /**
     * Metabox for slider ID
     *
     * @param $post
     */
    public function render_slider_id( $post ){
        
        $vars = array();
        $vars['post_name'] = $post->post_name;
        
        $this->view->render('slider-id.php', $vars);

    }
    
    /**
     * Hook to admin footer
     */
    public function admin_footer() {
        // JS skeleton for adding a slide
        if(get_post_type()=='cycloneslider'){
            // Empty Slide
            $vars = array();
            $vars['box_title'] = __('Slide *', 'cycloneslider');
            $vars['image_url'] = '';
            $vars['i'] = '{id}';
            $vars['slide'] = $this->data->get_slide_defaults();
            foreach($vars['slide'] as $key=>$value){
                $vars['slide'][$key] = '';
            }
            $vars['slide']['type'] = 'image';
            $vars['effects'] = $this->data->get_slide_effects();
            $vars['debug'] = ($this->debug) ? cyclone_slider_debug($vars['slide']) : '';
            
            $vars['full_image_url'] = '';
            $vars['testimonial_img_url'] = '';
            $vars['full_testimonial_img_url'] = '';
            $vars['type'] = 'image';

            $empty_slide = $this->view->get_render('slide-edit.php', $vars);
            
            // Main skeleton container
            $vars = array();
            $vars['empty_slide'] = $empty_slide;
            
            $this->view->render('slides-skeleton.php', $vars);
        }
    }


    /**
     * Get slide image thumb from id. False on fail
     *
     * @param $attachment_id
     *
     * @return array|bool|false|string
     */
    private function get_slide_img_thumb($attachment_id){
        $attachment_id = (int) $attachment_id;
        if($attachment_id > 0){
            $image_url = wp_get_attachment_image_src( $attachment_id, 'medium', true );
            $image_url = (is_array($image_url)) ? $image_url[0] : '';
            return $image_url;
        }
        return false;
    }

    /**
     * Replace text in media button for WP < 3.5
     *
     * @param $translation
     * @param $text
     * @param $domain
     *
     * @return string
     */
    public function replace_text_in_thickbox($translation, $text, $domain ) {
        $http_referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $req_referrer = isset($_REQUEST['referer']) ? $_REQUEST['referer'] : '';
        if(strpos($http_referrer, 'cycloneslider')!==false or $req_referrer=='cycloneslider') {
            if ( 'default' == $domain and 'Insert into Post' == $text )
            {
                return 'Add to Slide';
            }
        }
        return $translation;
    }

	/**
     * Add attachment ID as html5 data attr in thickbox
     * @param $html
     * @param $id
     * @param $caption
     * @param $title
     * @param $align
     * @param $url
     * @param $size
     * @param string $alt
     *
     * @return mixed
     */
    public function image_send_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt = '' ){
        if(strpos($html, '<img data-id="')===false){
            $html = str_replace('<img', '<img data-id="'.$id.'" ', $html);
        }
        return $html;
    }

	/**
     * Modify columns
     *
     * @param $columns
     *
     * @return array
     */
    public function slideshow_columns( $columns ) {
        unset($columns['date']);
        $columns['title']= __('Slideshow Name', 'cycloneslider');
        $columns['template']= __('Template', 'cycloneslider');
        $columns['images']= __('No. of Slides', 'cycloneslider');
        $columns['id']= __('Slideshow ID', 'cycloneslider');
        $columns['shortcode']= __('Shortcode', 'cycloneslider');
        return $columns;
    }

	/**
     * Add content to custom columns
     * @param $column_name
     * @param $post_id
     */
    public function custom_column( $column_name, $post_id ){
        if ($column_name == 'template') {
            $settings = $this->data->get_slider_settings($post_id);
            echo ucwords($settings['template']);
        }
        if ($column_name == 'images') {
            echo '<div style="text-align:center; max-width:40px;">' . $this->data->get_slide_count( $post_id ) . '</div>';
        }
        if ($column_name == 'id') {
            $post = get_post($post_id);
            echo $post->post_name;
        }
        if ($column_name == 'shortcode') {  
            $post = get_post($post_id);
            echo '[cycloneslider id="'.$post->post_name.'"]';
        }  
    }

	/**
     * Compare the value from admin and shortcode. If shortcode value is present and not empty, use it, otherwise return admin value
     *
     * @param $admin_val
     * @param $shortcode_val
     *
     * @return mixed
     */
    public function get_comp_slider_setting($admin_val, $shortcode_val){
        if($shortcode_val!==null){//make sure its really null and not just int zero 0
            return $shortcode_val;
        }
        return $admin_val;
    }

	/**
     * Return array of slide urls from meta
     *
     * @param $slider_metas
     *
     * @return array
     */
    public function get_slides_from_meta($slider_metas){
        $slides = array();
        if(is_array($slider_metas)){
            foreach($slider_metas as $slider_meta){
                $attachment_id = (int) $slider_meta['id'];
                $image_url = wp_get_attachment_url($attachment_id);
                $image_url = ($image_url===false) ? '' : $image_url;
                $slides[] = $image_url;
            }
        }
        return $slides;
    }
    
    
    /**
     * YOUTUBE & VIMEO
     */
    
    /**
     * Ajax for getting videos
     */
    public function cycloneslider_get_video(){
        $url = $_POST['url'];
        
        $retval = array(
            'success' => false
        );
        
        if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {

            if( $video_id = $this->get_youtube_id($url) ){ //If youtube url
                if( $embed = wp_oembed_get($url) ){ //Get embed, false on fail
                    $retval = array(
                        'success' => true,
                        'url' => $this->get_youtube_thumb($video_id),
                        'embed' => $embed
                    );
                }
                
            } else if( $video_id = $this->get_vimeo_id($url) ){ //If vimeo url
                if( $embed = wp_oembed_get($url) ){ //Get embed, false on fail
                    $retval = array(
                        'success' => true,
                        'url' => $this->get_vimeo_thumb($video_id),
                        'embed' => $embed
                    );
                }
            }
        }
        
        echo json_encode($retval);
        die();
    }

    /**
     * Get video thumb url
     *
     * @param string $url A valid youtube or vimeo url
     *
     * @return bool|string
     */
    public function get_video_thumb_from_url($url){
        $url = esc_url_raw($url);
            
        if ( $video_id = $this->get_youtube_id($url) ) { // A youtube url

            return $this->get_youtube_thumb($video_id);
            
        } else if( $video_id = $this->get_vimeo_id($url) ){ // A vimeo url
            
            return $this->get_vimeo_thumb($video_id);
        }
        
        return false;
    }

    /**
     * Return vimeo video id
     *
     * @param $url
     *
     * @return bool|string
     */
    public function get_vimeo_id($url){
        
        $parsed_url = parse_url($url);
        if ($parsed_url['host'] == 'vimeo.com'){
            $vimeo_id = ltrim( $parsed_url['path'], '/');
            if (is_numeric($vimeo_id)) {
                return $vimeo_id;
            }
        }
        return false;
    }
    
    /**
     * Get vimeo video thumbnail image
     *
     * @param int $video_id Vimeo ID.
     * @param string $size Size can be: thumbnail_small, thumbnail_medium, thumbnail_large.
     *
     * @return string URL of thumbnail image.
     */
    public function get_vimeo_thumb($video_id, $size = 'thumbnail_large'){
        $vimeo = unserialize( file_get_contents('http://vimeo.com/api/v2/video/'.$video_id.'.php') );
        if( isset($vimeo[0][$size]) ){
            return $vimeo[0][$size];
        }
        return '';
    }

    /**
     * Get youtube video thumbnail image
     *
     * @param int $video_id Youtube ID.
     *
     * @return string URL of thumbnail image.
     */
    public function get_youtube_thumb($video_id){
        return 'http://img.youtube.com/vi/'.$video_id.'/0.jpg';
    }
    
    /**
     * Get youtube ID from different url formats
     *
     * @param string $url Youtube url
     * @return string Youtube URL or boolean false on fail
     */
    public function get_youtube_id($url){
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            return false;
        }
        $parsed_url = parse_url($url);
       
        if(strpos($parsed_url['host'], 'youtube.com')!==false){
            if(strpos($parsed_url['path'], '/watch')!==false){ // Regular url Eg. http://www.youtube.com/watch?v=9bZkp7q19f0
                parse_str($parsed_url['query'], $parsed_str);
                if(isset($parsed_str['v']) and !empty($parsed_str['v'])){
                    return $parsed_str['v'];
                }
            } else if(strpos($parsed_url['path'], '/v/')!==false){ // "v" URL http://www.youtube.com/v/9bZkp7q19f0?version=3&autohide=1
                $id = str_replace('/v/','',$parsed_url['path']);
                if( !empty($id) ){
                    return $id;
                }
            } else if(strpos($parsed_url['path'], '/embed/')!==false){ // Embed URL: http://www.youtube.com/embed/9bZkp7q19f0
                return str_replace('/embed/','',$parsed_url['path']);
            }
        } else if(strpos($parsed_url['host'], 'youtu.be')!==false){ // Shortened URL: http://youtu.be/9bZkp7q19f0
            return str_replace('/','',$parsed_url['path']);
        }
        
        return false;
    }
}