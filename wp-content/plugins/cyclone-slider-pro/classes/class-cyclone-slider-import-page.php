<?php
if(!class_exists('Cyclone_Slider_Import_Page') and class_exists('Codefleet_Admin_Sub_Page')):

	/**
	* Class for wrapping WP add_submenu_page.
	*/
	class Cyclone_Slider_Import_Page extends Codefleet_Admin_Sub_Page {
		protected $view;
		protected $importer;
		protected $cyclone_slider_data; // Holds cyclone slider data object
		
		public function __construct( $view, $importer, $cyclone_slider_data ) {		
			parent::__construct();
			$this->view = $view;
			$this->importer = $importer;
			$this->cyclone_slider_data = $cyclone_slider_data;
			
			add_action('init', array( $this, 'catch_posts') );
		}
		
		public function catch_posts(){
			// Verify nonce
			if( isset($_POST[$this->cyclone_slider_data->nonce_name]) ){
				$nonce = $_POST[$this->cyclone_slider_data->nonce_name];
				if ( wp_verify_nonce( $nonce, $this->cyclone_slider_data->nonce_action) ) {
					$uploads = wp_upload_dir(); // Get dir
					if( isset($_POST['cycloneslider_import_step']) ){
                        if( $_POST['cycloneslider_import_step'] == 1 ){
							$cyclone_import = array();
							
							// Success
							if( $this->importer->import( $_FILES['cycloneslider_import']['tmp_name'], $uploads['basedir'].'/cyclone-slider' ) ){
								$cyclone_import = $this->importer->get_results();
								update_option('cycloneslider_import', $cyclone_import);
								wp_redirect( get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import&step=2') );
								exit;
							} else { // Fail
								
							}
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
			$this->view->set_view_file( CYCLONE_PATH . 'views/import-step-1.php' );
            $vars = array();
            $vars['nonce_name'] = $this->cyclone_slider_data->nonce_name;
            $vars['nonce'] = wp_create_nonce( $this->cyclone_slider_data->nonce_action );
			$vars['form_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import' );
            $vars['export_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export' );
            $vars['import_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import' );
            $this->view->set_vars( $vars );
            $this->view->render();

		}
		public function step_2(){
			$log_results = get_option('cycloneslider_import');
            $defaults = array(
                'oks'=>array(),
                'errors'=>array()
            );
            $log_results = wp_parse_args($log_results, $defaults);
			delete_option('cycloneslider_import');
			
			$this->view->set_view_file( CYCLONE_PATH . 'views/import-step-2.php' );
            $vars = array();
			$vars['log_results'] = $log_results;
			$vars['export_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-export' );
            $vars['import_page_url'] = get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import' );
            $this->view->set_vars( $vars );
            $this->view->render();

		}
	
	} // end class
	
	
endif;