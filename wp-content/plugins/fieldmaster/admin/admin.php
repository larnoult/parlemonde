<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fieldmaster_admin') ) :

class fieldmaster_admin {
	
	// vars
	var $notices = array();
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
	
		// actions
		add_action('admin_menu', 			array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts',	array($this, 'admin_enqueue_scripts'), 0);
		add_action('admin_notices', 		array($this, 'admin_notices'));
		
	}
	
	
	/*
	*  add_notice
	*
	*  This function will add the notice data to a setting in the fieldmaster object for the admin_notices action to use
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	$text (string)
	*  @param	$class (string)
	*  @param	wrap (string)
	*  @return	n/a
	*/
	
	function add_notice( $text = '', $class = '', $wrap = 'p' ) {
		
		// append
		$this->notices[] = array(
			'text'	=> $text,
			'class'	=> 'updated ' . $class,
			'wrap'	=> $wrap
		);
		
	}
	
	
	/*
	*  get_notices
	*
	*  This function will return an array of admin notices
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	(array)
	*/
	
	function get_notices() {
		
		// bail early if no notices
		if( empty($this->notices) ) return false;
		
		
		// return
		return $this->notices;
		
	}
	
	
	/*
	*  admin_menu
	*
	*  This function will add the FieldMaster menu item to the WP admin
	*
	*  @type	action (admin_menu)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_menu() {
		
		// bail early if no show_admin
		if( !fieldmaster_get_setting('show_admin') ) return;
		
		
		// vars
		$slug = 'edit.php?post_type=fm-field-group';
		$cap = fieldmaster_get_setting('capability');
		
		
		// add parent
		add_menu_page(__("FieldMaster",'fieldmaster'), __("FieldMaster",'fieldmaster'), $cap, $slug, false, 'dashicons-welcome-widgets-menus', '80.025');
		
		
		// add children
		add_submenu_page($slug, __('Field Groups','fieldmaster'), __('Field Groups','fieldmaster'), $cap, $slug );
		add_submenu_page($slug, __('Add New','fieldmaster'), __('Add New','fieldmaster'), $cap, 'post-new.php?post_type=fm-field-group' );
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This function will add the already registered css
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {
		
		wp_enqueue_style( 'fieldmaster-global' );
		
	}
	
	
	/*
	*  admin_notices
	*
	*  This function will render any admin notices
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_notices() {
		
		// vars
		$notices = $this->get_notices();
		
		
		// bail early if no notices
		if( !$notices ) return;
		
		
		// loop
		foreach( $notices as $notice ) {
			
			$open = '';
			$close = '';
				
			if( $notice['wrap'] ) {
				
				$open = "<{$notice['wrap']}>";
				$close = "</{$notice['wrap']}>";
				
			}
				
			?>
			<div class="notice is-dismissible <?php echo $notice['class']; ?>"><?php echo $open . $notice['text'] . $close; ?></div>
			<?php
				
		}
		
	}
	
}

// initialize
fieldmaster()->admin = new fieldmaster_admin();

endif; // class_exists check


/*
*  fieldmaster_add_admin_notice
*
*  This function will add the notice data to a setting in the fieldmaster object for the admin_notices action to use
*
*  @type	function
*  @date	17/10/13
*  @since	5.0.0
*
*  @param	$text (string)
*  @param	$class (string)
*  @return	(int) message ID (array position)
*/

function fieldmaster_add_admin_notice( $text, $class = '', $wrap = 'p' ) {
	
	return fieldmaster()->admin->add_notice($text, $class, $wrap);
	
}


/*
*  fieldmaster_get_admin_notices
*
*  This function will return an array containing any admin notices
*
*  @type	function
*  @date	17/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array)
*/

function fieldmaster_get_admin_notices() {
	
	return fieldmaster()->admin->get_notices();
	
}

?>