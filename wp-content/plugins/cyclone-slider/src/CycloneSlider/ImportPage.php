<?php
/**
* Class for import page
*/
class CycloneSlider_ImportPage extends CycloneSlider_WpAdminSubPage {
	
	protected $data;
	protected $view;
	protected $nonce_name;
	protected $nonce_action;
	protected $importer;
	protected $cyclone_slider_dir;
	protected $wp_content_dir;
	protected $wp_content_url;
	protected $export_page_url;
	protected $import_page_url;
	protected $nextgen_page_url;
	
	public function __construct( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $data, $view, $nonce_name, $nonce_action, $importer, $cyclone_slider_dir, $wp_content_dir, $wp_content_url, $export_page_url, $import_page_url, $nextgen_page_url ){
		parent::__construct(
            $parent_slug,
            $page_title,
            $menu_title,
            $capability,
            $menu_slug
        );
		
		$this->data = $data;
		$this->view = $view;
		$this->nonce_name = $nonce_name;
		$this->nonce_action = $nonce_action;
		$this->importer = $importer;
		$this->cyclone_slider_dir = $cyclone_slider_dir;
		$this->wp_content_dir = $wp_content_dir;
		$this->wp_content_url = $wp_content_url;
		$this->export_page_url = $export_page_url;
		$this->import_page_url = $import_page_url;
		$this->nextgen_page_url = $nextgen_page_url;
	}
	
	public function run() {
        
        parent::run();
        
        // Post
        add_action('init', array( $this, 'catch_posts') );
    }
	
	/**
	* Render page. This function should output the HTML of the page.
	*/
	public function render_page(){
		$current_step = isset($_GET['step']) ? (int) $_GET['step'] : 1;
		
		$vars = array();
		$vars['nonce_name'] = $this->nonce_name;
		$vars['nonce'] = wp_create_nonce( $this->nonce_action );
		$vars['export_page_url'] = $this->export_page_url;
		$vars['import_page_url'] = $this->import_page_url;
		$vars['nextgen_page_url'] = $this->nextgen_page_url;
		$vars['error'] = get_transient( 'cycloneslider_error_import');
		if(!class_exists('ZipArchive')){
			$vars['error'] = __( 'ZipArchive not supported. ZipArchive is needed for Import and Export to work.', 'cycloneslider' );
		}
		delete_transient( 'cycloneslider_error_import');
		
		$vars['tabs'] = array(
			array(
				'title' => __('Export', 'cycloneslider'),
				'url' => $this->export_page_url,
				'classes' => 'nav-tab'
			),
			array(
				'title' => __('Import', 'cycloneslider'),
				'url' => $this->import_page_url,
				'classes' => 'nav-tab nav-tab-active'
			)
		);
		
		switch ( $current_step ) {
			case 1:
				$this->step_1( $vars );
				break;
			case 2:
				$this->step_2( $vars );
				break;
			case 3:
				$this->step_3( $vars );
				break;
		}
		
	}
	
	private function step_1( $vars ){
		if( is_dir( $this->cyclone_slider_dir.'/imports' ) ){
			$this->rmdir_recursive( $this->cyclone_slider_dir.'/imports' );
		}
		
		$this->view->render('import-step-1.php', $vars);

	}
	
	private function step_2( $vars ){
		
		
		
		$this->view->render('import-step-2.php', $vars);

	}
	
	private function step_3( $vars ){
		if( is_dir( $this->cyclone_slider_dir.'/imports' ) ){
			$this->rmdir_recursive( $this->cyclone_slider_dir.'/imports' );
		}
		
		$vars['ok'] = __('Import operation success!', 'cycloneslider' );
		
		$this->view->render('import-step-3.php', $vars);

	}
	
	public function catch_posts(){
		// Verify nonce
		if( isset($_POST[ $this->nonce_name ]) ){
			$nonce = $_POST[ $this->nonce_name ];
			if ( wp_verify_nonce( $nonce, $this->nonce_action) ) {
				$uploads = wp_upload_dir(); // Get dir
				if( isset( $_POST['cycloneslider_import_step'] ) ){
					switch ( $_POST['cycloneslider_import_step'] ) {
						case 1:
							$this->catch_post_step_1( $_POST, $_FILES );
							break;
						case 2:
							
							break;
					}
				}
			}
		}
	}
	
	private function catch_post_step_1( $post, $files ){
		
		try {
			$this->importer->import( $files['cycloneslider_import']['tmp_name'] );
		} catch (Exception $e ){
			set_transient( 'cycloneslider_error_import', $e->getMessage(), 60 );
			return false;
		}
		
		wp_redirect( get_admin_url( get_current_blog_id(), 'edit.php?post_type=cycloneslider&page=cycloneslider-import&step=3') );
		exit;
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
