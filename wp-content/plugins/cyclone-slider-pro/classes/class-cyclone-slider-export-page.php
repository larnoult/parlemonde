<?php
if(!class_exists('Cyclone_Slider_Export_Page') and class_exists('Codefleet_Admin_Sub_Page')):

    /**
    * Class for wrapping WP add_submenu_page.
    */
    class Cyclone_Slider_Export_Page extends Codefleet_Admin_Sub_Page {
        protected $view;
        protected $exporter;
        protected $cyclone_slider_data; // Holds cyclone slider data object
        
        public function __construct( $view, $exporter, $cyclone_slider_data ) {     
            parent::__construct();
            $this->view = $view;
            $this->exporter = $exporter;
            $this->cyclone_slider_data = $cyclone_slider_data;
            
            add_action('init', array( $this, 'catch_posts') );
        }
        
        public function catch_posts(){
            // Verify nonce
            if( isset($_POST[$this->cyclone_slider_data->nonce_name]) ){
                $nonce = $_POST[$this->cyclone_slider_data->nonce_name];
                if ( wp_verify_nonce( $nonce, $this->cyclone_slider_data->nonce_action) ) {
                    
                    if( isset($_POST['cycloneslider_export_step']) ){
                        if( $_POST['cycloneslider_export_step'] == 1 ){
                            $cyclone_export = array();
                            if( isset($_POST['cycloneslider_export']) ){
                                if(!empty($_POST['cycloneslider_export'])){
                                    $cyclone_export = $_POST['cycloneslider_export'];
                                    update_option('cycloneslider_export', $cyclone_export);
                                    wp_redirect( get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export&step=2') );
                                    exit;
                                }
                            }
                            update_option('cycloneslider_export', $cyclone_export);
                        }
                    }
                }
            }
        }
        
        /**
        * Render page. This function should output the HTML of the page.
        */
        public function render_page( $post ){
            $current_step = isset($_GET['step']) ? (int) $_GET['step'] : 1;
            if($current_step == 2){
                $this->step_2();
            } else {
                $this->step_1();
            }
        }
        
        public function step_1(){
            $cycloneslider_export = get_option('cycloneslider_export');
            $defaults = array(
                'all' => 0,
                'sliders' => array()
            );
            $cycloneslider_export = wp_parse_args($cycloneslider_export, $defaults);
            
            $this->view->set_view_file( CYCLONE_PATH . 'views/export-step-1.php' );
            $vars = array();
            $vars['sliders'] = $this->cyclone_slider_data->get_sliders();
            $vars['nonce_name'] = $this->cyclone_slider_data->nonce_name;
            $vars['nonce'] = wp_create_nonce( $this->cyclone_slider_data->nonce_action );
            $vars['cycloneslider_export'] = $cycloneslider_export;
            $vars['form_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export' );
            $vars['export_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export' );
            $vars['import_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import' );
            $this->view->set_vars( $vars );
            $this->view->render();

        }
        
        public function step_2(){
            $cycloneslider_export = get_option('cycloneslider_export');
            $defaults = array(
                'all' => 0,
                'sliders' => array()
            );
            $cycloneslider_export = wp_parse_args($cycloneslider_export, $defaults);
            
            $uploads = wp_upload_dir();
            $zip_file = $uploads['basedir'].'/cyclone-slider.zip';
            
            $this->exporter->export( $zip_file, $cycloneslider_export['sliders'] );
            
            $this->view->set_view_file( CYCLONE_PATH . 'views/export-step-2.php' );
            $vars = array();
            $vars['nonce_name'] = $this->cyclone_slider_data->nonce_name;
            $vars['nonce'] = wp_create_nonce( $this->cyclone_slider_data->nonce_action );
            $vars['form_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export&step=3' );
            $vars['export_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export' );
            $vars['import_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import' );
            $vars['zip_url'] = $uploads['baseurl'].'/cyclone-slider.zip';
            $vars['log_results'] = $this->exporter->get_results();
            $this->view->set_vars( $vars );
            $this->view->render();
        }
    } // end class
    
endif;