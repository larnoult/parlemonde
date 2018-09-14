<?php

/**
 * View Own Post Media Only Library WordPress plugin library - common code
 * @Author: Vladimir Garagulya
 * @package: View Own Post Media Only Library
 * 
 */

class View_Own_Post_Media_Only_Library {

// general WP plugin related stuff

	private $options_id = '';
  private $options = array();
  
  public $log_to_file = false;  // set to true in order to record data about critical actions to log file
  
  private $log_file_name = '';  
  
  function __construct($option_name) {

    
    $this->init_options($option_name);

    add_action('admin_notices', array(&$this, 'show_message'));
    
  }
  // end of __construct()
  
// get current options for this plugin
	function init_options($options_id) {
		$this->options_id = $options_id;
		$this->options = get_option($options_id);    
	}
	
	
  public function show_message($message, $error_style=false) {
  
    if ($message) {
      if ($error_style) {
        echo '<div id="message" class="error" >';
      } else {
        echo '<div id="message" class="updated fade">';
      }
      echo $message . '</div>';
    }

  }
  // end of show_message()
  
  
  public function get_request_var($var_name, $request_type='request', $var_type='string') {

    $result = 0;
    if ($request_type == 'get') {
      if (isset($_GET[$var_name])) {
        $result = $_GET[$var_name];
      }
    } else if ($request_type == 'post') {
      if (isset($_POST[$var_name])) {
        if ($var_type!='checkbox') {
          $result = $_POST[$var_name];
        } else {
          $result = 1;
        }
      }
    } else {
      if (isset($_REQUEST[$var_name])) {
          $result = $_REQUEST[$var_name];
      }
    }

    if ($result) {
      if ($var_type == 'int' && !is_numeric($result)) {
        $result = 0;
      }
      if ($var_type!='int') {
        $result = esc_attr($result);
      }
    }
    

    return $result;
  }
  // end of get_request_var
 

  // returns option value for option with name in $option_name
  public function get_option($option_name, $default = false) {
    
    if (isset( $this->options[ $option_name ] ) ) {
      return $this->options[ $option_name ];
    } else {
      return $default;
    }
    
  }
  // end of get_option()

  
  // puts option value according to $option_name option name into options array property
  public function put_option($option_name, $option_value, $flush_options=false) {
    
    $this->options[$option_name] = $option_value;
    if ($flush_options) {
      $this->flush_options();
    }
    
  }
  // end of put_option()

  
  // saves options array into WordPress database wp_options table
  public function flush_options() {
    
    update_option($this->options_id, $this->options);
    
  }
  // end of flush_options()

	
	
	public function checked_html($checkbox_value) {
	
		if ($checkbox_value) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
		
		return $checked;
		
	}
	// end of checked_html()
  
	
}
// end of class View_Own_Post_Media_Only_Library

?>
