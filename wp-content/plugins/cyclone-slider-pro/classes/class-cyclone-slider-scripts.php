<?php
if(!class_exists('Cyclone_Slider_Scripts')):
    
    /**
    * Class for handling styles and scripts
    */
    class Cyclone_Slider_Scripts {
        
        private $templates_manager; // Holds templates manager object
        private $cyclone_settings_data; // Holds cyclone settings array
        
        /**
         * Initialize
         */
        public function __construct( $templates_manager, $cyclone_settings_data ) {
            
            // Inject dependencies
            $this->templates_manager = $templates_manager;
            $this->cyclone_settings_data = $cyclone_settings_data;
            
        } // end constructor
        
        /**
         * Scripts and styles for slider admin area
         */ 
        public function register_admin_scripts( $hook ) {

            if( 'cycloneslider' == get_post_type() || $hook == 'cycloneslider_page_cycloneslider-settings' || $hook == 'cycloneslider_page_cycloneslider-export' ||$hook == 'cycloneslider_page_cycloneslider-import' ){ // Limit loading to certain admin pages
                
                // Required media files for new media manager. Since WP 3.5+
                wp_enqueue_media();
                
                // Fontawesome style
                wp_enqueue_style( 'font-awesome', CYCLONE_URL.'libs/font-awesome/css/font-awesome.min.css', array(), CYCLONE_VERSION );
                
                // Main style
                wp_enqueue_style( 'cycloneslider-admin-styles', CYCLONE_URL.'css/admin.css', array(), CYCLONE_VERSION  );
                
                // Disable autosave
                wp_dequeue_script( 'autosave' );
                
                // For sortable elements
                wp_enqueue_script('jquery-ui-sortable');
                
                // For localstorage
                wp_enqueue_script( 'store', CYCLONE_URL.'js/store-json2.min.js', array('jquery'), CYCLONE_VERSION );
                
                // Allow translation to script texts
                wp_register_script( 'cycloneslider-admin-script', CYCLONE_URL.'js/admin.js', array('jquery'), CYCLONE_VERSION  );
                wp_localize_script( 'cycloneslider-admin-script', 'cycloneslider_admin_vars',
                    array(
                        'title'     => __( 'Select an image', 'cycloneslider' ), // This will be used as the default title
                        'title2'     => __( 'Select Images - Use Ctrl + Click or Shift + Click', 'cycloneslider' ),
                        'button'    => __( 'Add to Slide', 'cycloneslider' ), // This will be used as the default button text
                        'button2'    => __( 'Add Images as Slides', 'cycloneslider' ),
                        'youtube_url_error'    => __( 'Error. Make sure its a valid YouTube URL.', 'cycloneslider' ) 
                    )
                );
                wp_enqueue_script( 'cycloneslider-admin-script');
                
            }
        }
        
        /**
         * Scripts and styles for slider to run in admin preview. Must be hook to either admin_enqueue_scripts or wp_enqueue_scripts
         *
         * @param string $hook Hook name passed by WP
         * @return void
         */
        public function register_frontend_scripts_in_admin( $hook ) {
            if( get_post_type() == 'cycloneslider' || 'cycloneslider_page_cycloneslider-settings' == $hook || 'cycloneslider_page_cycloneslider-export' == $hook || 'cycloneslider_page_cycloneslider-import' == $hook ){ // Limit loading to certain admin pages
                $this->register_frontend_scripts( $hook );
            }
        }
        
        /**
         * Scripts and styles for slider to run. Must be hook to either admin_enqueue_scripts or wp_enqueue_scripts
         *
         * @param string $hook Hook name passed by WP
         * @return void
         */
        public function register_frontend_scripts( $hook ) {
 
            $in_footer = true;
            if($this->cyclone_settings_data['load_scripts_in'] == 'header'){
                $in_footer = false;
            }
            
            /*** Magnific Popup Style ***/
            if($this->cyclone_settings_data['load_magnific'] == 1){
                wp_enqueue_style( 'jquery-magnific-popup', CYCLONE_URL.'libs/magnific-popup/magnific-popup.css', array(), CYCLONE_VERSION );
            }
            
            /*** Templates Styles ***/
            $this->enqueue_templates_css();
            
            /*****************************/
            
            /*** Core Cycle2 Scripts ***/
            if($this->cyclone_settings_data['load_cycle2'] == 1){
                wp_enqueue_script( 'jquery-cycle2', CYCLONE_URL.'libs/cycle2/jquery.cycle2.min.js', array('jquery'), CYCLONE_VERSION, $in_footer );
            }
            if($this->cyclone_settings_data['load_cycle2_carousel'] == 1){
                wp_enqueue_script( 'jquery-cycle2-carousel', CYCLONE_URL.'libs/cycle2/jquery.cycle2.carousel.min.js', array('jquery', 'jquery-cycle2'), CYCLONE_VERSION, $in_footer );
            }
            if($this->cyclone_settings_data['load_cycle2_swipe'] == 1){
                wp_enqueue_script( 'jquery-cycle2-swipe', CYCLONE_URL.'libs/cycle2/jquery.cycle2.swipe.min.js', array('jquery', 'jquery-cycle2'), CYCLONE_VERSION, $in_footer );
            }
            if($this->cyclone_settings_data['load_cycle2_tile'] == 1){
                wp_enqueue_script( 'jquery-cycle2-tile', CYCLONE_URL.'libs/cycle2/jquery.cycle2.tile.min.js', array('jquery', 'jquery-cycle2'), CYCLONE_VERSION, $in_footer );
            }
            if($this->cyclone_settings_data['load_cycle2_video'] == 1){
                wp_enqueue_script( 'jquery-cycle2-video', CYCLONE_URL.'libs/cycle2/jquery.cycle2.video.min.js', array('jquery', 'jquery-cycle2'), CYCLONE_VERSION, $in_footer );
            }
            
            /*** Easing Script***/
            if($this->cyclone_settings_data['load_easing'] == 1){
                wp_enqueue_script( 'jquery-easing', CYCLONE_URL.'libs/jquery-easing/jquery.easing.1.3.1.min.js', array('jquery'), CYCLONE_VERSION, $in_footer );
            }
            
            /*** Magnific Popup Scripts ***/
            if($this->cyclone_settings_data['load_magnific'] == 1){
                wp_enqueue_script( 'jquery-magnific-popup', CYCLONE_URL.'libs/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), CYCLONE_VERSION, $in_footer );
            }
            
            /*** Templates Scripts ***/
            $this->enqueue_templates_scripts();
            
            /*** Client Script ***/
            wp_enqueue_script( 'cyclone-client', CYCLONE_URL.'js/client.js', array('jquery'), CYCLONE_VERSION, $in_footer );

        }
        
        public function register_yt_iframe_api_scripts(){
            if( get_post_type() == 'cycloneslider' ){ // Limit loading to certain slider admin pages
                ?>
                <script>
                    jQuery(document).ready(function($){
                        // Loads the IFrame Player API code asynchronously.
                        (function(d, t) {
                            var g = d.createElement(t),
                                s = d.getElementsByTagName(t)[0];
                            g.src = 'https://www.youtube.com/iframe_api';
                            s.parentNode.insertBefore(g, s);
                        }(document, 'script'));
                    });
                    
                    window.cyclone = {
                        players:{}
                    };
                    window.onYouTubeIframeAPIReady = function() {
                        var iframes = document.getElementsByTagName('iframe'),
                            id='';
                            
                        for (var i = 0; i < iframes.length; i++) {
                            console.log('loop '+iframes[i].id);
                            id = iframes[i].id;
                            window.cyclone.players[id] = new YT.Player(id, {
                                events: {
                                    // The API will call this function when the video player is ready.
                                    'onReady': function( event ) {
                                        console.log('onPlayerReady ' + event.target.a.id);
                                        
                                        //event.target.playVideo();
                                    },
                                    'onStateChange': function(event) {
                                        if (event.data == YT.PlayerState.BUFFERING) {
                                            console.log('buffering '+event.target.a.id)
                                        }
                                        if (event.data == YT.PlayerState.PAUSED) {
                                            console.log('paused '+event.target.a.id)
                                            jQuery('#'+event.target.a.id).parents('.cycloneslider-slides').cycle('resume');
                                        }
                                        if (event.data == YT.PlayerState.PLAYING) {
                                            console.log('played '+event.target.a.id);
                                            jQuery('#'+event.target.a.id).parents('.cycloneslider-slides').cycle('pause');
                                        }
                                    }
                                }
                            });
                        }
                    }
                </script>
                <?php
            }
        }
        
        /**
         * Enqueues templates styles.
         */
        private function enqueue_templates_css(){
            $ds = DIRECTORY_SEPARATOR;
             
            $template_folders = $this->templates_manager->get_all_templates();
            $active_templates = $this->templates_manager->get_active_templates( $this->cyclone_settings_data );
            
            foreach($template_folders as $name=>$folder){
                
                if( 1 == $active_templates[$name] ){
                    $file = $folder['path']."/style.css"; // Path to file
                    
                    if( file_exists( $file ) ){ // Check existence
                        wp_enqueue_style( 'cyclone-template-style-'.sanitize_title($name), $folder['url'].'/style.css', array(), CYCLONE_VERSION );
                    }
                }
            }
        }
        
        /**
         * Enqueues templates scripts.
         */
        private function enqueue_templates_scripts(){
            $ds = DIRECTORY_SEPARATOR;

            $in_footer = true;
            if($this->cyclone_settings_data['load_scripts_in'] == 'header'){
                $in_footer = false;
            }
            
            $template_folders = $this->templates_manager->get_all_templates();
            $active_templates = $this->templates_manager->get_active_templates( $this->cyclone_settings_data );
            
            foreach($template_folders as $name=>$folder){
                
                if( 1 == $active_templates[$name] ){
                    $file = $folder['path']."/script.js"; // Path to file
                    
                    if( file_exists( $file ) ){ // Check existence
                        wp_enqueue_script( 'cyclone-template-script-'.sanitize_title($name), $folder['url'].'/script.js', array(), CYCLONE_VERSION, $in_footer );
                    }
                }
            }
        }
        
    }
    
endif;