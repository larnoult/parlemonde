<?php
/*
 * Helper class that handles all of the TableMaster Admin settings.
*/
 
if (!class_exists('TableMaster_Settings'))  {
class TableMaster_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $options_name = 'tablemaster_general_options';
	private $settings_group = 'tablemaster_general_options_group';
	private $settings_page = 'general_options_page';
	private $settings_section = 'general_options_section';

	// If you add default settings for keywords here, you need to add them to the arrays in the validate function. tablemaster_plugin_validate_input
	private $plugin_default_settings = array(
			"class" 		=> null,
			'thead' 		=> 1,
			'tfoot' 		=> 0,
			'nohead' 		=> 0,
			'style' 		=> null,
			'css' 			=> null,
			'datatables' 	=> 0,
			'rows' 			=> 10,	//valid choices are 10, 25, 50, 100
			'buttons' 		=> 0,
			'button_list'	=> 'copy,csv,excel,pdf,print',	//valid choices are opy,csv,excel,pdf,print
			'new_window' 	=> 1,
			'default_sort'  => 1
		 );

	/**
	* Enforce Singleton Pattern
	*
	* @access public
	*/
	private static $instance;
	public function getInstance( $plugin_name, $plugin_version ) {
		if (null == self::$instance) {
			self::$instance = new TableMaster_Settings( $plugin_name, $plugin_version );
		}
		return self::$instance;
	}
	
	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $plugin_version ) {
	
		$this->plugin_name = $plugin_name;
		$this->version = $plugin_version;

		add_action( 'admin_menu', array( &$this, 'create_tablemaster_settings_menu_item' ) );
		add_action( 'admin_init', array( &$this, 'register_tablemaster_settings' ) );
	}

	function get_options_name() { 
		return $this->options_name; 
	}
	
	function activate() {
		update_option( $this->options_name, $this->plugin_default_settings );
	}

	function deactivate() {
		delete_option( $this->options_name, $this->plugin_default_settings );
 	}
	
	/**
	* This function introduces the plugin options into the 'Appearance' menu and into a top-level 
	* 'TableMaster Plugin' menu.
	*/
	function create_tablemaster_settings_menu_item() {

		add_options_page(
			'TableMaster User\'s Guide',		// The value used to populate the browser's title bar when the menu page is active
			'TableMaster',			// The text of the menu in the administrator's sidebar
			'manage_options',		// What roles are able to access the menu
			$this->settings_page,	// The ID used to bind submenu items to this menu 
			array( &$this, 'render_tablemaster_settings_page'	)	// The callback function used to render this menu
		);

	} // end create_tablemaster_plugin_menu

	/**
	* Renders a simple page to display for the plugin menu defined above.
	*/
	function render_tablemaster_settings_page( $active_tab = '' ) {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">
	
			<div id="icon-plugins" class="icon32"></div>
			<h2><?php //_e( 'TableMaster Plugin Options', 'tablemaster' ); ?></h2>
			<?php //settings_errors(); ?>
		
			<?php if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'users_guide' ) {
				$active_tab = 'users_guide';
			} else {
				$active_tab = 'general_options';
			} // end if/else ?>
		
			<h2 class="nav-tab-wrapper">
				<a href="?page=general_options_page&tab=users_guide" class="nav-tab <?php echo $active_tab == 'users_guide' ? 'nav-tab-active' : ''; ?>"><?php _e( 'User\'s Guide', 'tablemaster' ); ?></a>
				<a href="?page=general_options_page&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Options', 'tablemaster' ); ?></a>
			</h2>
		
			<form method="post" action="options.php">
			<?php
				if( $active_tab == 'general_options' ) {
				
					settings_fields( $this->settings_group );
					do_settings_sections( $this->settings_page );
					
					submit_button();
					
						
				} elseif( $active_tab == 'users_guide' ) {
				
					TableMaster_Users_Guide::print_users_guide();
				}
			?>
			</form>
			
			<?php TableMaster_Settings::print_donate_message(); ?>
			
		</div><!-- /.wrap -->
<?php
	} // end render_tablemaster_settings_page

	static function print_donate_message() {
	?>
		<p style="margin-top:40px;">If you find this plugin useful, please consider making a donation to help support development of future plugin enhancements.</p>
		<div style="text-align:center;margin-right:auto;margin-left:auto;">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="9ET8HUEG7THKS">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div> 
	<?php
	}
	
	/* ------------------------------------------------------------------------ *
	* Setting Registration
	* ------------------------------------------------------------------------ */ 

	/**
	* Initializes the plugin's options page by registering the Sections,
	* Fields, and Settings.
	*
	* This function is registered with the 'admin_init' hook.
	*/ 
	function register_tablemaster_settings() {

		// First, we register the fields with WordPress
		register_setting(
			$this->settings_group,
			$this->options_name,
			array( &$this, 'tablemaster_plugin_validate_input')	
		);

		// get_option returns false if option doesn't exist.
		// add_option returns true if the option was added, false otherwise.

		// If the plugin options don't exist, create them. They should exist though, since they are updated
		// when the plugin is activated.
		if( false === get_option( $this->options_name ) ) {	
			add_option( $this->options_name, $this->plugin_default_settings );
		} 
		
		// In version 0.0.3, 'default_sort' was added.  But, if someone is upgrading for  when I added the 'default_sort' option. Want to set the default value for the new setting
		// in memory but, we don't want to reset all of the default values because the user might have already set some
		// settings values that we don't want to override.
		
		// Lets check to see what value the new option has by default (null, 0, etc?)
		$current_options = get_option( $this->options_name );
		if( !array_key_exists('default_sort', $current_options ) ){
			$current_options['default_sort'] = $this->plugin_default_settings['default_sort'];
			update_option( $this->options_name, $current_options );
		}

		// Second, we register a section. This is necessary since all future options must belong to a section.
		add_settings_section(
			$this->settings_section,					// ID used to identify this section and with which to register options
			__( 'General Options', 'tablemaster' ),		// Title to be displayed on the administration page
			array( &$this, 'tablemaster_general_options_callback'),	// Callback used to render the description of the section
			$this->settings_page					// Page on which to add this section of options
		);
	
		add_settings_field(	
			'class',						
			__( 'class Keyword', 'tablemaster' ),							
			array( &$this, 'tablemaster_render_text_input'),	
			$this->settings_page,	
			$this->settings_section,			
			array( 'class',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Enter a CSS class, or multiple classes separated by spaces, to apply to all tables.', 'tablemaster' )
			)
		);

		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'thead',						// ID used to identify the field throughout the plugin
			__( 'thead Keyword', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'thead',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box to use the \'thead\' HTML tag for the first row of the table.', 'tablemaster' )
			)
		);

		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'tfoot',						// ID used to identify the field throughout the plugin
			__( 'tfoot Keyword', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'tfoot',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box to use the \'tfoot\' HTML tag for the last row of the table.', 'tablemaster' )
			)
		);
		
		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'nohead',						// ID used to identify the field throughout the plugin
			__( 'nohead Keyword', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'nohead',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box if you do not want a header row on your tables.', 'tablemaster')
			)
		);
	
		add_settings_field(	
			'style',						
			__( 'style', 'tablemaster' ),							
			array( &$this, 'tablemaster_render_text_input'),	
			$this->settings_page,	
			$this->settings_section,			
			array( 'style',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Enter text to be used for the \'style\' attribute for all the tables.', 'tablemaster' )
			)
		);
	
		add_settings_field(	
			'css',						
			__( 'css', 'tablemaster' ),							
			array( &$this, 'tablemaster_render_text_input'),	
			$this->settings_page,	
			$this->settings_section,			
			array( 'css',								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Enter CSS style statements to apply to the all the tables.', 'tablemaster' )
			)	
		);
	
		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'new_window',						// ID used to identify the field throughout the plugin
			__( 'new_window', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'new_window',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box if you want any links within your tables to open in a new window.', 'tablemaster')
			)
		);

		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'datatables',						// ID used to identify the field throughout the plugin
			__( 'datatables', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'datatables',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box if you want to use the DataTables table plugin for jQuery on all tables.', 'tablemaster' )
			)
		);

	
		add_settings_field(	
			'rows',						
			__( 'rows', 'tablemaster' ),							
			array( &$this, 'tablemaster_render_text_input'),	
			$this->settings_page,	
			$this->settings_section,			
			array( 'rows',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Enter the number of rows to display for each page of your tables. Valid values are 10, 25, 50, 100.', 'tablemaster' )
			)
		);
	
		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'buttons',						// ID used to identify the field throughout the plugin
			__( 'buttons', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'buttons',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box if you want to enable the DataTables \'Buttons\' extension on all tables', 'tablemaster' )
			)
		);

		add_settings_field(	
			'button_list',						
			__( 'button_list', 'tablemaster' ),							
			array( &$this, 'tablemaster_render_text_input'),	
			$this->settings_page,	
			$this->settings_section,			
			array( 'button_list',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Enter a comma separated list of the buttons you want to use with the \'Buttons\' extension. Valid valus are copy, csv, excel, pdf, print.','tablemaster')
			)
		);
	
		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(	
			'default_sort',						// ID used to identify the field throughout the plugin
			__( 'default_sort', 'tablemaster' ),							// The label to the left of the option interface element
			array( &$this, 'tablemaster_render_checkbox'),	// The name of the function responsible for rendering the option interface
			$this->settings_page,	
			$this->settings_section,			
			array( 'default_sort',								// The array of arguments to pass to the callback. In this case, just a description.
				__( 'Check this box if you want the DataTables jQuery plugin to determine the default sort order for your table. Note, if you use the \'sql\' keyword and your MySQL command contains an \'ORDER BY\' clause, you should uncheck this checkbox.', 'tablemaster')
			)
		);

	} // end register_tablemaster_settings

	/**
	* This function provides a simple description for the General Options page. 
	*
	* It's called from the 'tablemaster_initialize_plugin_options' function by being passed as a parameter
	* in the add_settings_section function.
	*/
	function tablemaster_general_options_callback() {
		echo '<p>' . __( 'This page allows you to set default values for certain keywords. These default values will be applied to all TableMaster tables. You can override these settings for a table by specifying the keywords directly in the shortcode with a different value. Be sure to read the User\'s Guide for a description of all keywords that are available.' , 'tablemaster' ) . '</p>';
	} // end tablemaster_general_options_callback


	/* ------------------------------------------------------------------------ *
	* Field Callbacks
	* ------------------------------------------------------------------------ */ 

	/**
	* This function renders the checkbox fields
	* 
	* It accepts an array or arguments and expects the first element in the array to be the description
	* to be displayed next to the checkbox.
	*/
	function tablemaster_render_checkbox($args) {
	
		// First, we read the options collection
		$options = get_option($this->options_name);
		$keyword = $args[0];
		$description = $args[1];
	
		// Next, we update the name attribute to access this element's ID in the context of the display options array
		// We also access the show_header element of the options collection in the call to the checked() helper function
		$html = '<input type="checkbox" id="'.$keyword.'" name="' .$this->options_name.'['.$keyword.']" value="1" ' . checked( 1, isset( $options[$keyword] ) ? $options[$keyword] : 0, false ) . '/>'; 

		// Here, we'll take the first argument of the array and add it to a label next to the checkbox
		$html .= '<label for="'.$keyword.'"> '  . $description . '</label>'; 
	
		echo $html;
	
	} // end tablemaster_toggle_thead_callback


	function tablemaster_render_text_input($args) {
	
		// First, we read the options collection
		$options = get_option($this->options_name);
		$keyword = $args[0];
		$description = $args[1];
		
		$input_type = ($keyword == 'style' || $keyword == 'css' ) ? 'textarea' : 'text';
	
		// Render the output
		$html = '<input type="text" id="'.$keyword.'" name="' .$this->options_name.'['.$keyword.']" value="' . $options[$keyword] . '" />';
	
		// Here, we'll take the first argument of the array and add it to a label next to the checkbox
		$html .= '<label for="'.$keyword.'"><br />'  . $description. '</label>'; 
		echo $html;
	
	} // end tablemaster_render_input

	
	/* ------------------------------------------------------------------------ *
	* Setting Callbacks
	* ------------------------------------------------------------------------ */ 
	function tablemaster_plugin_validate_input( $input ) {

		$checkbox_array = array( 'thead', 'tfoot', 'nohead', 'datatables', 'buttons', 'new_window', 'default_sort' );
		$text_input_array = array( 'class', 'style', 'css', 'button_list', 'rows' );
		
		// Create our array for storing the validated options, start with the current settings.
		$new_settings = get_option( $this->options_name );
		
		// Loop through each of the keywords and see if they are set in the input. Problem is,
		// I can't loop through the value returned from 
		
		foreach( $new_settings as $key => $value ) {
/*		
		$debug = "Current Settings  " . $key;
		$debug .= " empty=[".empty($new_settings[$key])."]";
		$debug .= ", isset=[".isset($new_settings[$key])."]";
		$debug .= ", is_null=[".is_null($new_settings[$key])."]";
		$debug .= ", false==[".(false==$new_settings[$key])."]";
		$debug .= ", false===[".(false===$new_settings[$key])."]";
		$debug .= ", true==[".(true==$new_settings[$key])."]";
		
_logMaster( $debug);

		$debug = "Received in input " . $key;
		$debug .= " empty=[".empty($input[$key])."]";
		$debug .= ", isset=[".isset($input[$key])."]";
		$debug .= ", is_null=[".is_null($input[$key])."]";
		$debug .= ", false==[".(false==$input[$key])."]";
		$debug .= ", false===[".(false===$input[$key])."]";
		$debug .= ", true==[".(true==$input[$key])."]";
		$debug .= ", array_key_exists[".(array_key_exists( $key, $input ))."]";
		
_logMaster( $debug);
_logMaster( "-------------------------------");
*/
			if ( in_array( $key, $checkbox_array ) ) {
			
				// Process like a checkbox
				if ( array_key_exists( $key, $input ) && !empty( $input[$key] ) ) {
					$new_settings[$key] = 1;
				} else {
					$new_settings[$key] = 0;
				}
					
			} else  {
			
				if( array_key_exists( $key, $input ) && !empty( $input[$key] )) { // not empty is needed to catch the checkboxes

					// Strip all HTML and PHP tags and properly handle quoted strings
					$new_settings[$key] = strip_tags( stripslashes( $input[ $key ] ) );
					
				//} else if ( !array_key_exists($key, $input) || ( array_key_exists($key, $input) && empty($input[$key] )) ) {
				} else if (  ( array_key_exists($key, $input) && empty($input[$key] )) ) {
				
					$new_settings[$key] = null;
					if ($key == 'rows' ) {
						$new_settings[$key] = '10'; // rows needs to have a valid default value.
					} 
					
					if ($key == 'button_list' ) {
						$new_settings[$key] = 'copy,csv,excel,pdf,print'; // buttons need a default list of buttons
					} 
				}
			} 
		
		} // end foreach

		return  $new_settings;
		
	} // end tablemaster_plugin_validate_input
	
	
} // End class
} // End if class exists

?>