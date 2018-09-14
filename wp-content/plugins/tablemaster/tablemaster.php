<?php
/*
 * Plugin Name:       TableMaster
 * Plugin URI:        https://wordpress.org/plugins/tablemaster/
 * Description:       Prints your tables using the DataTables for JQuery Plugin using a simple `[tablemaster]` shortcode. 
 * Version:           0.0.3
 * Author:            Valerie Mallder
 * Author URI:        http://codehorsesoftware.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/
define(TABLEMASTER_PLUGIN_VERSION, '0.0.3');

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

	/**
	 * Constants used by _register_stylesheets_and_scripts
	 */

	//define(JQUERY_VERSION, '1.11.1'); 
	define(JQUERY_DATATABLES_VERSION, '1.10.10'); 
	define(DATATABLES_BUTTONS_VERSION, '1.1.0');
	define(DATATABLES_RESPONSIVE_VERSION, '2.0.0');  
	define(JSZIP_VERSION, '2.5.0-15');
	define(PDFMAKE_VERSION, '0.1.20');

	$datatables_externals = plugin_dir_url(__FILE__).'external/datatables.net';
	
	//define( JQUERY_JS, 'http://code.jquery.com/jquery-'.JQUERY_VERSION.'.min.js');
	define( JQUERY_DATATABLES_JS, $datatables_externals.'/js/jquery.dataTables.min.js');
	define( DATATABLES_RESPONSIVE_JS, $datatables_externals.'/js/dataTables.responsive.min.js');
	define( DATATABLES_BUTTONS_JS, $datatables_externals.'/js/dataTables.buttons.min.js');
	define( DATATABLES_BUTTONS_FLASH_JS, $datatables_externals.'/js/buttons.flash.min.js');
	define( DATATABLES_BUTTONS_HTML5_JS, $datatables_externals.'/js/buttons.html5.min.js');
	define( DATATABLES_BUTTONS_PRINT_JS, $datatables_externals.'/js/buttons.print.min.js');
	
	define( JQUERY_DATATABLES_CSS, $datatables_externals.'/css/jquery.dataTables.min.css');
	define( DATATABLES_BUTTONS_CSS, $datatables_externals.'/css/buttons.dataTables.min.css');
	define( DATATABLES_RESPONSIVE_CSS, $datatables_externals.'/css/responsive.dataTables.min.css');

	define( JSZIP_JS, $datatables_externals.'/js/jszip.min.js');
	define( PDFMAKE_JS, $datatables_externals.'/js/pdfmake.min.js');
	define( VFS_FONTS_JS, $datatables_externals.'/js/vfs_fonts.js');

	define( TABLEMASTER_CSS, plugin_dir_url( __FILE__ ) . 'css/tablemaster.css');
	define( TABLEMASTER_USER_CSS_FILE, get_stylesheet_directory() . '/css/tablemaster.css');
	define( TABLEMASTER_USER_CSS_URI, get_stylesheet_directory_uri() . '/css/tablemaster.css');
	
	require( dirname(__FILE__) . '/admin/class-tablemaster-settings.php');
	require( dirname(__FILE__) . '/admin/class-tablemaster-users-guide.php');
	require( dirname(__FILE__) . '/admin/class-tablemaster-admin.php');

//file_exists(dirname(__FILE__) . '/tablemaster-pro.php') AND include dirname(__FILE__) . '/tablemaster-pro.php';


if (!class_exists('TableMaster'))  {
class TableMaster {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name = "tablemaster";

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version = TABLEMASTER_PLUGIN_VERSION;

	protected $tables = array();
	protected $next_idx = 0; 	// next table index, init to 0, increment after the shortcode is processed
	protected $dbh; 			// handle to the database
	
	protected $valid_keywords = array(
		"table",
		"view",
		"sql",
		"class",
		"thead",
		"nohead",
		"tfoot",
		"style",
		"css",
		"columns",
		"nowrap",
		"col_widths",
		"link_labels",
		"link_targets",
		"datatables",
		"buttons",
		"rows",
		"button_list",
		"new_window",
		"pre_table_filter",
		"post_table_filter",
		'default_sort'		
	);

	protected $valid_buttons = array(
		"copy",
		"csv",
		"excel",
		"pdf",
		"print"
	);
		

	/**
	* Enforce Singleton Pattern
	*/
	private static $instance;
	public function getInstance() {
		if (null == self::$instance) {
			self::$instance = new TableMaster;
		}
		return self::$instance;
	}
	
	/*
	 * Constructor
	 */
	function __construct() {
	
		TableMaster_Admin::getInstance( $plugin_name, $plugin_version );		// Instantiate to initialize
		TableMaster_Settings::getInstance( $plugin_name, $plugin_version ); 	// Instantiate to initialize

		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		
		add_action( 'init', array( &$this, 'init' ) );
	}
	
	/*
	 * Runs when the plugin is activated
	 */
	function activate() {
		TableMaster_Admin::getInstance( $plugin_name, $plugin_version )->activate();
		TableMaster_Settings::getInstance( $plugin_name, $plugin_version )->activate();
	}

	/*
	 * Runs when the plugin is deactivated
	 */
	function deactivate() {
		TableMaster_Admin::getInstance( $plugin_name, $plugin_version )->deactivate();
		TableMaster_Settings::getInstance( $plugin_name, $plugin_version )->deactivate();
	}
	
	/**
	 * Runs when the plugin is initialized
	 */
	function init() {
	
		// Register the shortcode 
		add_shortcode( 'tablemaster', array( &$this, 'generate_table' ) );
		add_shortcode( 'tablemaster_users_guide', array( 'TableMaster_Users_Guide', 'print_users_guide' ) );
		add_shortcode( 'tablemaster_donate_message', array( 'TableMaster_Settings', 'print_donate_message' ) );
	
		// Enqueue the style sheets and java scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylesheets_and_scripts' ), 11 ); 
		
		// Print the javascript code in the footer
		add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ), 11 ); 
		
		// Connect to the database host
		$this->dbh = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD );
		
		// Select the database
		mysql_select_db( DB_NAME, $this->dbh );
		
		// Add filters to test the filter keywords
		add_filter( 'my_filter_name', array( &$this, 'print_my_logo'));
 	}

	/**
	 * Helper to print a formatted error message
	 */
	function _error_msg( $error_msg ) {
		$output = '<p style="color:red;font-weight:bold;">' . $error_msg . '</p>' ;
		return $output;
	}

	/**
	 * Helper to make sure the user provides valid keywords
	 */
	function validate_keywords( $keywords ) {
	
		foreach ( $keywords as $key ) {
			if( !in_array($key, $this->valid_keywords) ) {
			    echo '<p style="color:red;font-weight:bold;">Unrecognized keyword [' . $key . ']</p>' ;
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Helper to make sure the user provides valid buttons in the button list.
	 */
	function validate_buttons( $button_list ) {
	
		$buttons = explode(",", $button_list );
		foreach ( $buttons as $button ) {
			if( !in_array($button, $this->valid_buttons) ) {
			    echo '<p style="color:red;font-weight:bold;">Unrecognized button [' . $button . ']</p>' ;
				return false;
			}
		}
		return true;
	}	
	
	/**
	 * Initializes all the settings for this table based onb default saved settings and
	 * keywords provided in the shortcode.
	 */
	function initialize_table_settings( $table_id, $atts ) {
	
		/*
		 * Make sure all the keywords are valid before proceeding
		 */
		if( !$this->validate_keywords( array_keys( $atts ) ) ) {
			return "One or more of the keywords specified are not valid keywords. Please check the documentation";
		}
/*
[tablemaster use_datatables="false" show_rows="50" use_thead="yes" class="table-striped" columns="Date,Show,Prizelist,Ride Times,Day Sheets,Stabling,Show Results" link_labels="Prizelist,Ride Times,Day Sheets,Stabling,Show Results" link_targets="PrizelistLink,RideTimesLink,DaySheetsLink,StablingLink,ShowResultsLink" sql="SELECT pvda_shows.ShowDate as 'Date', pvda_shows.ShowName as 'Show', pvda_shows_meta.PrizelistLabel as Prizelist, pvda_shows_meta.PrizelistLink, pvda_shows_meta.RideTimesLabel as 'Ride Times', pvda_shows_meta.RideTimesLink, pvda_shows_meta.DaySheetsLabel as 'Day Sheets', pvda_shows_meta.DaySheetsLink, pvda_shows_meta.StablingLabel as Stabling, pvda_shows_meta.StablingLink, pvda_shows_meta.ShowResultsLabel as 'Show Results', pvda_shows_meta.ShowResultsLink FROM pvda_shows LEFT JOIN pvda_shows_meta ON pvda_shows.ShowID = pvda_shows_meta.ShowID WHERE RIGHT(pvda_shows.ShowDate,4) = '2015' ORDER BY pvda_shows.ShowDate ASC"]
*/
		/*
		 * Initialize with default settings first, then modify settings based on keywords provided
		 */
		$this->tables[$table_id]['table_id'] = $table_id;
		$this->tables[$table_id]['settings'] = get_option( TableMaster_Settings::getInstance( $this->plugin_name, $this->plugin_version )->get_options_name() );
		$this->tables[$table_id]['div_tag_id'] = 'tablemaster_wrapper_' . $table_id;
		$this->tables[$table_id]['table_tag_id'] = 'tablemaster_table_' . $table_id;
		
		/* 
		 * Check the table keyword
		 */
		if ( array_key_exists( 'table', $atts ) && !empty( $atts['table'] ) ) {
			if ( array_key_exists( 'sql', $atts ) && !empty( $atts['sql'] ) ) {
				return "Cannot use table and sql keywords at the same time.";
			}
			if ( array_key_exists( 'view', $atts ) && !empty( $atts['view'] ) ) {
				return "Cannot use table and view keywords at the same time.";
			}
			$this->tables[$table_id]['settings']['table'] = $atts['table'];
			
			/* 
			 * Check the columns keyword 
			 */
//			if ( array_key_exists('columns', $atts ) && !empty( $atts['columns'] )) {
//				$this->tables[$table_id]['settings']['columns'] = $atts['columns'];
//				$this->tables[$table_id]['sql'] = "SELECT " . $atts['columns'] . " FROM " . $atts['table'];
//			} else {
				$this->tables[$table_id]['sql'] = "SELECT * FROM " . $atts['table'];
//			}
			
		}
		
		/* 
		 * Check the view keyword
		 */
		if ( array_key_exists( 'view', $atts ) && !empty( $atts['view'] ) ) {
			if ( array_key_exists( 'sql', $atts ) && !empty( $atts['sql'] ) ) {
				return "Cannot use view and sql keywords at the same time.";
			}
			if ( array_key_exists( 'table', $atts ) && !empty( $atts['table'] ) ) {
				return "Cannot use view and table keywords at the same time.";
			}
			$this->tables[$table_id]['settings']['view'] = $atts['view'];		
			/* 
			 * Check the columns keyword 
			 */
//			if ( array_key_exists('columns', $atts ) && !empty( $atts['columns'] )) {
//				$this->tables[$table_id]['settings']['columns'] = $atts['columns'];
//				$this->tables[$table_id]['sql'] = "SELECT " . $atts['columns'] . " FROM " . $atts['view'];
//			} else {
				$this->tables[$table_id]['sql'] = "SELECT * FROM " . $atts['view'];
//			}
		}

		/* 
		 * Check the sql keyword
		 */
		if ( array_key_exists( 'sql', $atts ) && !empty( $atts['sql'] ) ) {
			if ( array_key_exists( 'view', $atts ) && !empty( $atts['view'] ) ) {
				return "Cannot use sql and view keywords at the same time.";
			}
			if ( array_key_exists( 'table', $atts ) && !empty( $atts['table'] ) ) {
				return "Cannot use sql and table keywords at the same time.";
			}
			$this->tables[$table_id]['sql'] = $atts['sql'];		
		}

		/* 
		 * Check the columns keyword
		 */
		if ( array_key_exists( 'columns', $atts ) && !empty( $atts['columns'] ) ) {
			$this->tables[$table_id]['settings']['columns'] = $atts['columns'];		
		}

		/* 
		 * Check the class keyword
		 */
		if ( array_key_exists( 'class', $atts ) && !empty( $atts['class'] ) ) {
			$this->tables[$table_id]['settings']['class'] = $atts['class'];		
		}

		/* 
		 * Check the thead keyword
		 */
		if ( array_key_exists('thead', $atts ) && "true" == $atts['thead'] ) {
			$this->tables[$table_id]['settings']['thead'] = true;	
	
		} else if ( array_key_exists('thead', $atts ) && "false" == $atts['thead'] ) {
			$this->tables[$table_id]['settings']['thead'] = false;		
		}

		/* 
		 * Check the nohead keyword
		 */
		if ( array_key_exists('nohead', $atts ) && "true" == $atts['nohead'] ) {
			$this->tables[$table_id]['settings']['nohead'] = true;	
	
		} else if ( array_key_exists('nohead', $atts ) && "false" == $atts['nohead'] ) {
			$this->tables[$table_id]['settings']['nohead'] = false;		
		}
		
		/* 
		 * Check the tfoot keyword
		 */
		if ( array_key_exists('tfoot', $atts ) && "true" == $atts['tfoot'] ) {
			$this->tables[$table_id]['settings']['tfoot'] = true;
		
		} else if ( array_key_exists('tfoot', $atts ) && "false" == $atts['tfoot'] ) {
			$this->tables[$table_id]['settings']['tfoot'] = false;		
		}
		
		/* 
		 * Check the style keyword
		 */
		if ( array_key_exists( 'style', $atts ) && !empty( $atts['style'] ) ) {
			$this->tables[$table_id]['settings']['style'] = $atts['style'];		
		}

		/* 
		 * Check the css keyword
		 */
		if ( array_key_exists( 'css', $atts ) && !empty( $atts['css'] ) ) {
			$this->tables[$table_id]['settings']['css'] = $atts['css'];		
		}

		/* 
		 * Check the datatables keyword (requires thead = true)
		 */
		if ( array_key_exists('datatables', $atts ) && "true" == $atts['datatables'] ) {
			if (( array_key_exists('thead', $atts ) && "false" == $atts['thead'] ) || ( false == $this->tables[$table_id]['settings']['thead'] )) {
				return "thead must also be set to true in order to use the Jquery Datatables.";
			}
			$this->tables[$table_id]['settings']['datatables'] = true;
	
		} else if ( array_key_exists('datatables', $atts ) && "false" == $atts['datatables'] ) {
			$this->tables[$table_id]['settings']['datatables'] = false;		
		}
		
		/* 
		 * Check the datatables rows (requires datatables = true)
		 */
		if ( array_key_exists('rows', $atts ) && !empty( $atts['rows'] )) {
			if (( array_key_exists('datatables', $atts ) && "false" == $atts['datatables'] ) || ( false == $this->tables[$table_id]['settings']['datatables'] )) {
				return "Datatables must also be enabled in order to adjust the number of rows.";
			}
			$this->tables[$table_id]['settings']['rows'] = $atts['rows'];	
		}

		/* 
		 * Check the buttons keyword (requires datatables = true)
		 */
		if ( array_key_exists('buttons', $atts ) && "true" == $atts['buttons'] ) {
			if (( array_key_exists('datatables', $atts ) && "false" == $atts['datatables'] ) || ( false == $this->tables[$table_id]['settings']['datatables'] )) {
				return "Datatables must also be enabled in order to add the buttons.";
			}
			$this->tables[$table_id]['settings']['buttons'] = true;	
			
		} else if ( array_key_exists('buttons', $atts ) && "false" == $atts['buttons'] ) {
			$this->tables[$table_id]['settings']['buttons'] = false;		
		}
		

		/* 
		 * Check the button_list keyword (requires buttons = true and valid buttons)
		 */
		if ( array_key_exists('button_list', $atts ) && !empty( $atts['button_list'] )) {
		
			if (( !array_key_exists('buttons', $atts ) || ( array_key_exists('buttons', $atts ) && "false" == $atts['buttons'] )) ||
				( false == $this->tables[$table_id]['settings']['buttons'] )) {
				return "Datatables must also be enabled in order to add the buttons.";
			}
			
			if ( !$this->validate_buttons( $atts['button_list'] ) ) {
				return "One or more of the buttons you specified are invalid. Valid buttons are: copy, csv, excel, pdf, print.";
			}
			$this->tables[$table_id]['settings']['button_list'] = $atts['button_list'];
		}

		/* 
		 * Check the default_sort keyword (requires datatables = true, datatables could be set to true by a keyword or set to true in the default settings.)
		 */
		if ( array_key_exists('default_sort', $atts ) ) { 
		
			if (( array_key_exists('datatables', $atts ) && "false" == $atts['datatables'] ) || ( false == $this->tables[$table_id]['settings']['datatables'] )) {
				return "The \'default_sort\' keyword has no effect unless \'datatables\' is enabled.";
			} 
			
			if ( "true" == $atts['default_sort'] ) {
				$this->tables[$table_id]['settings']['default_sort'] = true;
		
			} else if ( "false" == $atts['default_sort'] ) {
				$this->tables[$table_id]['settings']['default_sort'] = false;
			}
		}
		
		/* 
		 * Check the nowrap keyword 
		 */
		if ( array_key_exists( 'nowrap', $atts ) && !empty( $atts['nowrap'] )) {
			$this->tables[$table_id]['settings']['nowrap'] = $atts['nowrap'];		
		}
		
		/* 
		 * Check the new_window keyword 
		 */
		 
		if ( array_key_exists('new_window', $atts ) && "true" == $atts['new_window'] ) {
			$this->tables[$table_id]['settings']['new_window'] = true;		
			
		} else if ( array_key_exists('new_window', $atts ) && "false" == $atts['new_window'] ) {
			$this->tables[$table_id]['settings']['new_window'] = false;		
		}
		
		/* 
		 * Check the col_widths keyword 
		 */
		if ( array_key_exists('col_widths', $atts ) && !empty( $atts['col_widths'] )) {
			$this->tables[$table_id]['settings']['col_widths'] = $atts['col_widths'];		
		}

		/*
		 * Check the link_labels and link_targets (both must be present to be valid
		 */
		if ( array_key_exists('link_labels', $atts )  || array_key_exists('link_targets', $atts ) ) {
		
			if (( array_key_exists( 'link_labels', $atts ) && !empty( $atts['link_labels'] ) ) && 
				( !array_key_exists('link_targets', $atts ) || ( array_key_exists( 'link_targets', $atts ) && empty( $atts['link_targets'] ) ))) {
				return "You must specify link_targets if you are going to specify link_labels.";
			}
			
			if (( array_key_exists( 'link_targets', $atts ) && !empty( $atts['link_targets'] ) ) && 
				( !array_key_exists('link_labels', $atts ) || ( array_key_exists( 'link_labels', $atts ) && empty( $atts['link_labels'] ) ))) {
				return "You must specify link_labels if you are going to specify link_targets.";
			}
			
			$this->tables[$table_id]['settings']['link_labels'] = $atts['link_labels'];		
			$this->tables[$table_id]['settings']['link_targets'] = $atts['link_targets'];		
		}

		/*
		 * Check the sql for special strings that need to be converted back to
         * valid comparison operators.
		 */
		if ( !empty( $this->tables[$table_id]['sql'] ))  {
// I have no idea why the call to html_entity_decode used to work (or, have no effect on the value) but it doesn't now.			
//			$this->tables[$table_id]['sql'] = html_entity_decode($atts['sql']);
			$this->tables[$table_id]['sql'] = str_replace("__LT__", "<", $this->tables[$table_id]['sql'] );
			$this->tables[$table_id]['sql'] = str_replace("__LE__", "<=", $this->tables[$table_id]['sql'] );
			$this->tables[$table_id]['sql'] = str_replace("__GT__", ">", $this->tables[$table_id]['sql'] );
			$this->tables[$table_id]['sql'] = str_replace("__GE__", ">=", $this->tables[$table_id]['sql'] );
			$this->tables[$table_id]['sql'] = str_replace("__EQ__", "=", $this->tables[$table_id]['sql'] );
			$this->tables[$table_id]['sql'] = str_replace("__NE__", "<>", $this->tables[$table_id]['sql'] );
			
			//$this->tables[$table_id]['sql'] = htmlspecialchars( $this->tables[$table_id]['sql'] );
			//$this->tables[$table_id]['sql'] = mysql_real_escape_string( $this->tables[$table_id]['sql'] );
		}
//_logMaster( print_r( $this->tables[$table_id], true ));
		return null;
	}
	
	/**
	 * Processess the shortcode and builds the html output for the table
	 */
	function generate_table($atts, $content, $tag) {
		
		// Get the current table id and increment the next index
		$table_id = $this->next_idx; 
		$this->next_idx++;
		
		$err_msg = $this->initialize_table_settings( $table_id, $atts );
		if( !empty( $err_msg) ) {
			return $this->_error_msg( $err_msg );
		}
		
		/*
		 * PRE TABLE FILTER
		 */	
		if( !empty($atts['pre_table_filter'] )) {
			$output .= apply_filters( $atts['pre_table_filter'], ''); 
		}
		
		/* 
		 * Do the query to get the data for the table
		 */
		$sql = $this->tables[$table_id]['sql'];
		$result = mysql_query( $sql, $this->dbh ); 
		if ( !$result ) {
			$output = "No results returned for [" . $sql . "] error = " . mysql_error();
			return $output;
		}

		$num_rows = mysql_num_rows( $result );
		if ( $num_rows <= 0 ) {
			$output = "No rows returned for query = [" . $sql . "]";
			return $output;
		}
		
		$num_fields = mysql_num_fields($result);

		 /*
		  * Get the column names from the query results.
		  */
		$query_columns =  array();
		$num_query_columns = mysql_num_fields($result);
		for( $i=0;$i<$num_query_columns;$i++) {
			array_push($query_columns, mysql_field_name($result, $i) );
		}
// This is the column names as a result of the query. It could be the names of the columns, or custom names for the columns.  If link labels are used, it needs to contain both the link labels and link targets columns.
// it is recommeded that the columns keyword be used if link labels are link targets are used so that you can control whether the link target column is printed.
// The column names specified in the columns array must match the column names in the query results.			
		/*
		 * Get the list of columns that should be printed. 
		 */
		$print_columns = array();
		if ( isset( $this->tables[$table_id]['settings']['columns'] )) {
			$print_columns = explode(',', $this->tables[$table_id]['settings']['columns'] );
		} else {
			$print_columns = $query_columns;
		}

		/*
		 * Get the list of columns that should not wrap
		 */
		$nowrap_columns = array();
		if ( isset( $this->tables[$table_id]['settings']['nowrap'] )) {
			$nowrap_columns = explode(',', $this->tables[$table_id]['settings']['nowrap'] );
		 } 

		/*
		 * Get the list of column widths. 
		 */
		$column_widths = array();
		if ( isset( $this->tables[$table_id]['settings']['col_widths'] )) {
			$column_widths = explode(',', $this->tables[$table_id]['settings']['col_widths']);
		}
		 
/* 
 * link labels =  comma separated list of column names that contain the label to use for a link. The column names must be same as what is returned from mysql_field_name($result, $i) );
 * link targets =  comma separated list of column names that contain the html for a link. The column names must be same as what is returned from mysql_field_name($result, $i) ); This is usually going to be the same as what is in the database because you won't want the column names printed
*/
		/* 
		 * Check if link labels and link targets were specified. Both must be a comma
		 * separated list with no spaces after the comma, and no quote marks around items
		 * with 2 names like 'show results'.  'link_labels' must be list of the column
		 * names of the columns that contain the link labels. If the columns command was
		 * specified, the column names in this list must match the column names listed
		 * in the columns command, if any were listed. 'link_targets' must be a list
		 * of the column names that contain the links associated with the link labels. 
		 * The label list and link list must be in the same order. 
		 */

		$link_labels = array();
		if ( isset( $this->tables[$table_id]['settings']['link_labels'] )) {
			$link_labels = explode(',', $this->tables[$table_id]['settings']['link_labels']);
		}

		$link_targets = array();
		if ( isset( $this->tables[$table_id]['settings']['link_targets'] )) {
			$link_targets = explode(',', $this->tables[$table_id]['settings']['link_targets']);
		}

		$links = array();
		if ( isset( $link_labels ) && isset( $link_targets ) ) {
	
			// this sets the keys each with the array of labels
			$links = array_fill_keys($link_labels, $link_targets);
			$count = count($link_labels);
			for($i = 0; $i < $count; $i++)  {
				$links[$link_labels[$i]] = $link_targets[$i];
			}
		}

		// Start with the CSS statements if they were specified.
		if ( isset( $this->tables[$table_id]['settings']['css'] )) {
			$css = $this->tables[$table_id]['settings']['css'];
			$output .= '<style>' . $css . '</style>';
		}
		
		// Start the table container wrapper
		$output .= '<div id="' . $this->tables[$table_id]['div_tag_id'] . '">'; // required, need to enclose whole table in a div
		

		// Start the table
		$output .= '<table id="' . $this->tables[$table_id]['table_tag_id'] . '"';
		
		$output .= ' class="table ';
		if ( true == $this->tables[$table_id]['settings']['datatables'] ) {
			$output .= 'dataTable ';
		}
		
		// Insert css class name if a class was provided
		if ( isset( $this->tables[$table_id]['settings']['class'] )) {
			$output .= $this->tables[$table_id]['settings']['class'];
		}
		// Close the class attribute
		$output .= '"';
		
		// Insert style elements in the style attribute if any were provided
		if ( isset( $this->tables[$table_id]['settings']['style'] )) {
			$output .= ' style="' . $this->tables[$table_id]['settings']['style'] . '"';
		}
		
		// Close the table tag.
		$output .= '>';

		/*
		 * Start the table header or the table body depending on the setting for thead
		 */
		if ( ! (true == $this->tables[$table_id]['settings']['nohead'] )) {
		 
			if( true == $this->tables[$table_id]['settings']['thead'] ) {
				$output .= '<thead>';
			} else {
				$output .= '<tbody>';
			}
		
			// Start the header
			$output .= '<tr>';
			
			// Print the column headers, and set the column width for each column.
			$i = 0;
			foreach($print_columns as $n)	{
				$style = "";
				if (null != $column_widths[$i] ) {
					$style = 'style="width:' . $column_widths[$i] . '%;"';
				}

				if( true == $this->tables[$table_id]['settings']['thead'] ) {
					$output .= '<th ' . $style . '>' . $n . '</th>';
				}
				else {
					$output .= '<td ' . $style . '>' . $n . '</td>';
				}
				$i++;
			}
			// Close the header row
			$output .= '</tr>';
	
			if( true == $this->tables[$table_id]['settings']['thead'] ) {
				// close the header with the thead tag
				$output .= '</thead>';
			
				// start the body
				$output .= '<tbody>';
			}
		}
		else {
			$output .= '<tbody>';
		}


		// Get the body rows
		$row_num = 1;
		while ($row = mysql_fetch_assoc($result)) {
		
			// Check to see if this is the last row and the last row should be printed as a footer
			if( $num_rows == $row_num  && true == $this->tables[$table_id]['settings']['tfoot'] ) {
				// Close the body
				$output .= '</tbody>';
			
				// Start footer
				$output .= '<tfoot>';
			}

			// Start a row (this will be a body row or a footer row) 
			$output .= '<tr>';
			foreach($print_columns as $n)	{
		
				// If this is one of the columns that should not wrap, add the styling to make it not wrap
				if ( !empty($nowrap_columns) && in_array( $n, $nowrap_columns )) {
					$td = '<td data-title="' . $n . '" style="white-space:nowrap;">';
				} else {
					$td = '<td data-title="' . $n . '">';
				}
				
				// If the column name is the name of a column that contains a link, it needs special formatting
				if (null != $link_labels && in_array( $n, $link_labels )) {
					// If the link field is not empty, format the label name as a link
					if ( null != $row[$links[$n]] ) {
						if( true == $this->tables[$table_id]['settings']['new_window'] ) {
							$output .= $td.'<a target="_blank" style="color:blue;text-decoration:underline;" href="' . $row[$links[$n]] . '">' . $row[$n] . '</a></td>';
						} else {
							$output .= $td.'<a style="color:blue;text-decoration:underline;" href="' . $row[$links[$n]] . '">' . $row[$n] . '</a></td>';
						}
					}
					// else the link field is empty
					else {
						$output .= $td.'</td>';
					}
				// else print the row as a normal row
				} else {
					$output .= $td . $row[$n] . '</td>';
				}
			}
			$output .= '</tr>';
			$row_num =  $row_num + 1;
		}
		
		if( true == $this->tables[$table_id]['settings']['tfoot'] )  {
			$output .= '</tfoot>';
		} else {
			$output .= '</tbody>';
		}
		$output .= '</table>';
		$output .= '</div>'; // required, need to enclose whole table in a div
		
		/*
		 * POST TABLE FILTER
		 */	
		if( !empty($atts['post_table_filter'] )) {
			$output .= apply_filters( $atts['post_table_filter'], ''); 
		}
	
		return $output;
	}
  
	/**
	 * Builds the javascript commands necessary to activate the datatables.
	 */
	function wp_print_footer_scripts() {
		$num_tables = count($this->tables);
		if ( $num_tables == 0 ) {
			return;
		}

		$use_javascript = false;
		$js_commands = array();

		foreach ( $this->tables as $t ) {
			if( true == $t['settings']['datatables'] ) {
				$use_javascript = true;
			
				//Default  parameters for jquery datatables
				$datatable_options = array(
					'pageLength' 	=> '"pageLength":' . $t['settings']['rows'],  
					'lengthChange' 	=> '"lengthChange":true',
					'responsive' 	=> '"responsive":true'
				);

				// By default, DataTables will sort the data alphabetically by column 1. If the user
				// does not want the data sorted that way, they must set default_sort="false". 
				if ( ( false == $t['settings']['default_sort'] ) ) {
					$datatable_options['aaSorting'] = '"aaSorting":[]'; // disables default sort by column 1
				} 
				
				if( true == $t['settings']['buttons'] ) {
					/* Need to set the button list based on what was entered witih the shortcode */
					$buttons_options = array(
						'dom' => '"dom":"Blfrtip"',
						'buttons' => '"buttons":[\'copy\', \'csv\', \'excel\', \'pdf\', \'print\']',
					);
					if (!empty( $t['settings']['button_list']  )) {
						$button_names = explode(",", $t['settings']['button_list']  );
						$button_list = "[";
						foreach( $button_names as $button ) {
							$button_list .= "'".$button."', ";
						}
						
						$button_list = rtrim($button_list);	
						$button_list = substr($button_list, 0, -1)."]";	
						$buttons_options['buttons'] = '"buttons":'.$button_list;
					}
					$datatable_options = array_merge($datatable_options,$buttons_options);
				}
				$parameters = implode( ',', $datatable_options );
				$parameters = ( ! empty( $parameters ) ) ? '{' . $parameters . '}' : '';
				$html_id = str_replace( '-', '_', $t['table_tag_id'] );
				$div_id = str_replace( '-', '_', $t['div_tag_id'] );
				$command = "$('#{$html_id}').DataTable({$parameters});";
				if ( ! empty( $command ) ) {
					array_push($js_commands, $command);
				}
			}
		}
		
		if( $use_javascript == true ) {
			$js_commands = implode( "\n", $js_commands );

// echo DataTables strings and JS calls
echo <<<JS
<script type="text/javascript">
var a = jQuery.noConflict();
a(document).ready( function ($) {
{$js_commands}
});
</script>
JS;
		}
	}


	/**
	 * Registers and enqueues stylesheets and scripts
	 */
	function enqueue_stylesheets_and_scripts() {

		// TableMaster CSS
		wp_register_style( 'tablemaster-style', TABLEMASTER_CSS, array(), false, 'all'	);
		wp_enqueue_style( 'tablemaster-style' ) ;
		if ( file_exists( TABLEMASTER_USER_CSS_FILE ) ) {
			wp_register_style( 'tablemaster-user-style', TABLEMASTER_USER_CSS_URI, array(), false, 'all'	);
			wp_enqueue_style( 'tablemaster-user-style' ) ;
		}
		
		// DataTables CSS
		wp_register_style( 'datatables-style', JQUERY_DATATABLES_CSS, array(), false, 'all'	);
		wp_enqueue_style( 'datatables-style' ) ;
		
		// DataTables Buttons CSS
		wp_register_style( 'datatables-buttons-style', DATATABLES_BUTTONS_CSS, array(), false, 'all'	);
		wp_enqueue_style( 'datatables-buttons-style' ) ;
		
		// DataTables Responsive CSS
		wp_register_style( 'datatables-responsive-style', DATATABLES_RESPONSIVE_CSS, array(), false, 'all'	);
		wp_enqueue_style( 'datatables-responsive-style' ) ;
		
		// JQuery
//		wp_register_script( 'jquery-js', JQUERY_JS, array(), JQUERY_VERSION, true);
//		wp_enqueue_script( 'jquery-js' ) ;

		// DataTables
		wp_register_script( 'datatables-js', JQUERY_DATATABLES_JS, array(), JQUERY_DATATABLES_VERSION, true);
		wp_enqueue_script( 'datatables-js' ) ;

		// DataTables Buttons
		wp_register_script( 'buttons-js', DATATABLES_BUTTONS_JS, array(), DATATABLES_BUTTONS_VERSION, true);
		wp_enqueue_script( 'buttons-js' ) ;

		wp_register_script( 'buttons-flash-js', DATATABLES_BUTTONS_FLASH_JS, array(), DATATABLES_BUTTONS_VERSION, true);
		wp_enqueue_script( 'buttons-flash-js' ) ;

		wp_register_script( 'buttons-print-js', DATATABLES_BUTTONS_PRINT_JS, array(), DATATABLES_BUTTONS_VERSION, true);
		wp_enqueue_script( 'buttons-print-js' ) ;

		wp_register_script( 'buttons-html5-js', DATATABLES_BUTTONS_HTML5_JS, array(), DATATABLES_BUTTONS_VERSION, true);
		wp_enqueue_script( 'buttons-html5-js' ) ;

		wp_register_script( 'jszip-js', JSZIP_JS, array(), JSZIP_VERSION, true);
		wp_enqueue_script( 'jszip-js' ) ;

		wp_register_script( 'pdfmake-js', PDFMAKE_JS, array(), PDFMAKE_VERSION, true);
		wp_enqueue_script( 'pdfmake-js' ) ;

		wp_register_script( 'vfs-fonts-js', VFS_FONTS_JS, array(), PDFMAKE_VERSION, true);
		wp_enqueue_script( 'vfs-fonts-js' ) ;

		wp_register_script( 'responsive-js', DATATABLES_RESPONSIVE_JS, array(), DATATABLES_RESPONSIVE_VERSION, true);
		wp_enqueue_script( 'responsive-js' ) ;

		return true;
	} 
		
	function print_my_logo($arg) {
		$arg = '<div ><img src="' . plugin_dir_url(__FILE__) . 'admin/logo-val-brown-265x65.png' . '"></div>';
		return $arg;
	}
	
	
} // end class

// Instantiate TableMaster
TableMaster::getInstance();

} // end if ( !class_exists() )
?>
