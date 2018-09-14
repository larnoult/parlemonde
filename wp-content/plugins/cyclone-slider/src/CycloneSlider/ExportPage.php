<?php
/**
* Class for export page
*/
class CycloneSlider_ExportPage extends CycloneSlider_WpAdminSubPage{
	
    protected $sliders;
	protected $view;
	protected $exporter;
	protected $wp_content_dir;
	protected $wp_content_url;
	protected $transient_name;
	protected $nonce_name;
	protected $nonce_action;
	protected $export_page_url;
	protected $import_page_url;
	protected $nextgen_page_url;
	
	public function __construct( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $sliders, $view, $exporter, $wp_content_dir, $wp_content_url, $transient_name, $nonce_name, $nonce_action, $export_page_url, $import_page_url, $nextgen_page_url ){
		parent::__construct(
            $parent_slug,
            $page_title,
            $menu_title,
            $capability,
            $menu_slug
        );
		
		$this->sliders = $sliders;
		$this->view = $view;
		$this->exporter = $exporter;
		$this->wp_content_dir = $wp_content_dir;
		$this->wp_content_url = $wp_content_url;
		$this->transient_name = $transient_name;
		$this->nonce_name = $nonce_name;
		$this->nonce_action = $nonce_action;
		$this->export_page_url = $export_page_url;
		$this->import_page_url = $import_page_url;
		$this->nextgen_page_url = $nextgen_page_url;
	}
	
	public function run() {
        
        parent::run();
		
        // Catch Post
        add_action('init', array( $this, 'catch_posts') );
    }
	
	/**
    * Render page. This function should output the HTML of the page.
    */
    public function render_page(){
        $current_step = isset($_GET['step']) ? (int) $_GET['step'] : 1;
		
		$vars = array();
		$vars['transient_name'] = $this->transient_name;
		$vars['nonce_name'] = $this->nonce_name;
		$vars['nonce'] = wp_create_nonce( $this->nonce_action );
		$vars['export_page_url'] = $this->export_page_url;
        $vars['import_page_url'] = $this->import_page_url;
		$vars['nextgen_page_url'] = $this->nextgen_page_url;
		$vars['tabs'] = array(
			array(
				'title' => __('Export', 'cycloneslider'),
				'url' => $this->export_page_url,
				'classes' => 'nav-tab nav-tab-active'
			),
			array(
				'title' => __('Import', 'cycloneslider'),
				'url' => $this->import_page_url,
				'classes' => 'nav-tab'
			)
		);
		$vars['page_data'] = $this->get_page_data();
	    $vars['error'] = get_transient( 'cycloneslider_error_export');
	    if(!class_exists('ZipArchive')){
		    $vars['error'] = __( 'ZipArchive not supported. ZipArchive is needed for Import and Export to work.', 'cycloneslider' );
	    }
	    delete_transient( 'cycloneslider_error_export');

		switch ( $current_step ) {
			case 1:
				$this->render_step_1( $vars );
				break;
			case 2:
				$this->render_step_2( $vars );
				break;
			case 3:
				$this->render_step_3( $vars );
				break;
		}
    }
    
    private function render_step_1( $vars ){
        
        $vars['sliders'] = $this->sliders;

        $this->view->render( 'export-step-1.php', $vars );

    }
    private function render_step_2( $vars ){

        $this->view->render( 'export-step-2.php', $vars );
    }
	
    private function render_step_3( $vars ){
        
		// Make this configurable
        $vars['zip_url'] = $this->wp_content_url.'/cyclone-slider/exports/'.$vars['page_data']['file_name'];
		
        $zip_file = $this->wp_content_dir.'/cyclone-slider/exports/'.$vars['page_data']['file_name'];
        
		$vars['ok'] = __('Your export file is ready. Click Download.', 'cycloneslider');
		try {
			// Create exports dir
			if( is_dir( $this->wp_content_dir.'/cyclone-slider/exports' ) == false ){
				if( ! mkdir( $this->wp_content_dir.'/cyclone-slider/exports', 0777, true ) ){
					throw new Exception( __('Error creating exports directory.', 'cycloneslider'));
				}
			}
		
			$this->exporter->export( $zip_file, $vars['page_data']['sliders'] );
		} catch (Exception $e ){
			set_transient( 'cycloneslider_error_export', $e->getMessage(), 60 );
			$vars['ok'] = '';
		}
		
        
        $vars['log_results'] = $this->exporter->get_results();
        
		$vars['error'] = get_transient( 'cycloneslider_error_export');
		delete_transient( 'cycloneslider_error_export');
        $this->view->render( 'export-step-3.php', $vars );
    }
	
	public function catch_posts(){
		// Verify nonce
		if( isset($_POST[ $this->nonce_name ]) ){
			
			if ( wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action ) ) {
				
				$current_step = isset($_POST['cycloneslider_export_step']) ? (int) $_POST['cycloneslider_export_step'] : 1;
				
				switch ( $current_step ) {
					case 1:
						$this->catch_post_step_1( $_POST );
						break;
					case 2:
						$this->catch_post_step_2( $_POST );
						break;
				}
			}
		}
	}
	
	private function catch_post_step_1( $post ){
		
		if(isset($post['reset'])){
			delete_transient( $this->transient_name );
			if(is_dir($this->wp_content_dir.'/cyclone-slider/exports')){
				$this->rmdir_recursive($this->wp_content_dir.'/cyclone-slider/exports');
			}
			return false;
		}
		
		if( empty( $post[$this->transient_name]['sliders']) ){
			set_transient( 'cycloneslider_error_export', __('No slider selected.', 'cycloneslider'), 60 );
			return false;
		}
		
		if( empty( $post[$this->transient_name]['file_name'] ) ){
			set_transient( 'cycloneslider_error_export', __('Please choose a file name.', 'cycloneslider'), 60 );
			return false;
		}
		
		$page_data = $this->get_page_data();
		$page_data['all_sliders'] = $post[$this->transient_name]['all_sliders'];
		$page_data['sliders'] = $post[$this->transient_name]['sliders'];
		$page_data['file_name'] = $post[$this->transient_name]['file_name'];
		set_transient( $this->transient_name, $page_data, 3600 );
		wp_redirect( $this->export_page_url.'&step=2' );
		exit;
		
	}
	
	private function catch_post_step_2( $post ){
		
		
		wp_redirect( $this->export_page_url.'&step=3' );
		exit;
	}
	
    
	
	public function get_page_data(){
		return wp_parse_args(get_transient( $this->transient_name ), $this->get_default_page_data());
	}
	
	public function get_default_page_data(){
		return array(
            'all_sliders' => 0,
            'sliders' => array(),
			'file_name' => 'cyclone-slider-'.date('Y-m-d').'.zip'
        );
	}
	
	public function rmdir_recursive( $path ) {
		
		foreach(scandir($path) as $item) {
			if ('.' === $item || '..' === $item) continue;
			if ( is_dir("$path/$item") ) {
				$this->rmdir_recursive("$path/$item");
			} else {
				unlink("$path/$item");
			}
		}
		return rmdir($path);
	}
}
