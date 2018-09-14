<?php
if(!class_exists('Codefleet_View')):
	
	/**
	* Class for conveniently fetching template files and attaching template variables
	*/
	class Codefleet_View {
        
		protected $view_file;
		protected $vars = array();
		
        /**
        * Constructor
        */  
        public function __construct( $view_file='' ){
			$this->view_file = $view_file;
		}
        
		/**
        * Magic functions
        */
		public function __get( $key ) {
			return $this->vars[$key];
		}
 
		public function __set( $key, $value ) {
			$this->vars[$key] = $value;
		}
		
		/**
        * Setters
        */
		public function set_view_file( $value ){
			$this->view_file = $value;
		}
		
		public function set_vars( $value ){
			$this->vars = $value;
		}
		
		/**
        * Getters
        */
		public function get_view_file(){
			return $this->view_file;
		}
		
		public function get_vars(){
			return $this->vars;
		}
		
        /**
        * Include the view file and extract the passed variables
        */
        public function render(){
			
            if(@file_exists($this->view_file)){
				extract($this->vars, EXTR_SKIP); // Extract variables
				
				include($this->view_file); //Include the view file
			} else {
				echo '<p>Not found '.$this->view_file.'</p>';
			}
        }
		
		/**
        * Get and return view_file contents as string
        */
        public function get_render(){
			ob_start();
			$this->render();
            return ob_get_clean();
		}
    }
    
endif;