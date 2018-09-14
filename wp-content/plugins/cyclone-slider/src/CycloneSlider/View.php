<?php

/**
* Simple class for fetching template files and attaching template variables
*/

class CycloneSlider_View {
	
	protected $view_folder; // Folder containing view files
	
	/**
	* Constructor
	*/  
	public function __construct( $view_folder='' ){
		$this->view_folder = $view_folder;
	}
	
	/**
	* Setters
	*/
	public function set_view_folder( $value ){
		$this->view_folder = $value;
	}
	
	/**
	* Getters
	*/
	public function get_view_folder(){
		return $this->view_folder;
	}
	
	/**
	* Include the view file and extract the passed variables
	* 
	* @param string $file File name of the template
	* @param array $vars Template variables passed to the template
	* @return void on success string "Not found $view_file" on fail
	*/
	public function render($file, $vars = array()){
		$vars = apply_filters('cycloneslider_pre_render_view_vars', $vars);
		$view_file = $this->right_sep($this->view_folder).$file; // Add directory separator if needed
		$view_file = apply_filters('cycloneslider_pre_render_view_file', $view_file);
		if(@file_exists($view_file)){
			if(!empty($vars)){
				extract($vars, EXTR_SKIP); // Extract variables
			}

			include $view_file; //Include the view file
		} else {
			echo '<p>Not found '.$view_file.'</p>';
		}
	}
	
	/**
	* Get and return view_file contents as string
	*
	* @param string $file File name of the template
	* @param array $vars Template variables passed to the template
	* @return string String of template file
	*/
	public function get_render($file, $vars = array()){
		ob_start();
		$this->render($file, $vars);
		return ob_get_clean();
	}
	
	/*
	 * Add directory separator if its missing. Can be \ or / depending on OS.
	 *
	 * @param string $string
	 * @return string $string
	 */
	protected function right_sep( $string ){
		$c = substr($string, -1);
		if($c !== '/' and $c !== '\\'){
			return $string.DIRECTORY_SEPARATOR;
		}
		return $string;
	}
}
