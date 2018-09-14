<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://codehorsesoftware.com
 * @package    TableMaster
 * @subpackage TableMaster/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    TableMaster
 * @subpackage TableMaster/admin
 * @author     Valerie Mallder <horsenut@comcast.net>
 */
if (!class_exists('TableMaster_Admin'))  {
class TableMaster_Admin {

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

	/**
	* Enforce Singleton Pattern
	*
	* @access public
	*/
	private static $instance;
	public function getInstance( $plugin_name, $plugin_version ) {
		if (null == self::$instance) {
			self::$instance = new TableMaster_Admin( $plugin_name, $plugin_version );
		}
		return self::$instance;
	}

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $plugin_version ) {
	
		$this->plugin_name = $plugin_name;
		$this->version = $plugin_version;
		
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	}

	/**
	 * Runs when the admin screen is initialized
	 */
	function admin_init() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_footer_scripts' ), 11 ); 
	}
	
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    Tablemaster
 * @subpackage admin
 * @author     Valerie Mallder <horsenut@comcast.net>
 */
	function activate() {
	
		$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD );
		mysql_select_db( DB_NAME, $dbh );
		
		$sql = "DROP VIEW IF EXISTS `tm_example_view`;";
		$result = mysql_query( $sql, $dbh);
 
		$sql = "DROP TABLE IF EXISTS `tm_example_table`;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "CREATE TABLE IF NOT EXISTS `tm_example_table` (
			`Horse` varchar(16) DEFAULT NULL,
			`Breed` varchar(12) DEFAULT NULL,
			`Score` decimal(5,3) DEFAULT NULL,
			`Test` varchar(34) DEFAULT NULL,
			`Rider` varchar(13) DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "INSERT INTO `tm_example_table` (`Horse`, `Breed`, `Score`, `Test`, `Rider`) VALUES
			('Cirque du Soleil', 'Draft Cross', '60.000', 'Training Level, Test 2 2011', 'Adult Amateur'),
			('Zackary', 'Thoroughbred', '60.488', 'Third Level, Test 2 2011', 'Open'),
			('Lino', 'Friesian', '63.143', 'Second Level, Test 1 2011', 'Adult Amateur'),
			('Cinnamon', 'Oldenberg', '59.450', 'Introductory Walk-Trot Test A 2011', 'JR/YR'),
			('Zackary', 'Thoroughbred', '56.143', 'Fourth Level, Test 1 2011', 'Open'),
			('Lino', 'Friesian', '68.387', 'First Level, Test 3 2011', 'Adult Amateur'),
			('Stolichnaya', 'Hanoverian', '56.379', 'First Level, Test 1 2011', 'Adult Amateur');";
		$result = mysql_query( $sql, $dbh); 
			
		$sql = "CREATE VIEW tm_example_view AS SELECT Horse, Breed, Rider, Score FROM `tm_example_table`;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "DROP TABLE IF EXISTS `tm_example_long_table`;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "CREATE TABLE IF NOT EXISTS `tm_example_long_table` (
			`Horse` varchar(16) DEFAULT NULL,
			`Breed` varchar(12) DEFAULT NULL,
			`Score` decimal(5,3) DEFAULT NULL,
			`Test` varchar(34) DEFAULT NULL,
			`Rider` varchar(13) DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "INSERT INTO `tm_example_long_table` (`Horse`, `Breed`, `Score`, `Test`, `Rider`) VALUES
			('Cirque du Soleil', 'Draft Cross', '60.000', 'Training Level, Test 2 2011', 'Adult Amateur'),
			('Zackary', 'Thoroughbred', '60.488', 'Third Level, Test 2 2011', 'Open'),
			('Lino', 'Friesian', '63.143', 'Second Level, Test 1 2011', 'Adult Amateur'),
			('Cinnamon', 'Oldenberg', '59.450', 'Introductory Walk-Trot Test A 2011', 'JR/YR'),
			('Zackary', 'Thoroughbred', '56.143', 'Fourth Level, Test 1 2011', 'Open'),
			('Lino', 'Friesian', '68.387', 'First Level, Test 3 2011', 'Adult Amateur'),
			('Cirque du Soleil', 'Draft Cross', '60.000', 'Training Level, Test 2 2011', 'Adult Amateur'),
			('Zackary', 'Thoroughbred', '60.488', 'Third Level, Test 2 2011', 'Open'),
			('Lino', 'Friesian', '63.143', 'Second Level, Test 1 2011', 'Adult Amateur'),
			('Cinnamon', 'Oldenberg', '59.450', 'Introductory Walk-Trot Test A 2011', 'JR/YR'),
			('Zackary', 'Thoroughbred', '56.143', 'Fourth Level, Test 1 2011', 'Open'),
			('Lino', 'Friesian', '68.387', 'First Level, Test 3 2011', 'Adult Amateur'),
			('Cirque du Soleil', 'Draft Cross', '60.000', 'Training Level, Test 2 2011', 'Adult Amateur'),
			('Zackary', 'Thoroughbred', '60.488', 'Third Level, Test 2 2011', 'Open'),
			('Lino', 'Friesian', '63.143', 'Second Level, Test 1 2011', 'Adult Amateur'),
			('Cinnamon', 'Oldenberg', '59.450', 'Introductory Walk-Trot Test A 2011', 'JR/YR'),
			('Zackary', 'Thoroughbred', '56.143', 'Fourth Level, Test 1 2011', 'Open'),
			('Lino', 'Friesian', '68.387', 'First Level, Test 3 2011', 'Adult Amateur'),
			('Cirque du Soleil', 'Draft Cross', '60.000', 'Training Level, Test 2 2011', 'Adult Amateur'),
			('Zackary', 'Thoroughbred', '60.488', 'Third Level, Test 2 2011', 'Open'),
			('Lino', 'Friesian', '63.143', 'Second Level, Test 1 2011', 'Adult Amateur'),
			('Cinnamon', 'Oldenberg', '59.450', 'Introductory Walk-Trot Test A 2011', 'JR/YR'),
			('Zackary', 'Thoroughbred', '56.143', 'Fourth Level, Test 1 2011', 'Open'),
			('Lino', 'Friesian', '68.387', 'First Level, Test 3 2011', 'Adult Amateur'),
			('Cirque du Soleil', 'Draft Cross', '60.000', 'Training Level, Test 2 2011', 'Adult Amateur'),
			('Zackary', 'Thoroughbred', '60.488', 'Third Level, Test 2 2011', 'Open'),
			('Lino', 'Friesian', '63.143', 'Second Level, Test 1 2011', 'Adult Amateur'),
			('Cinnamon', 'Oldenberg', '59.450', 'Introductory Walk-Trot Test A 2011', 'JR/YR'),
			('Zackary', 'Thoroughbred', '56.143', 'Fourth Level, Test 1 2011', 'Open'),
			('Lino', 'Friesian', '68.387', 'First Level, Test 3 2011', 'Adult Amateur'),
			('Stolichnaya', 'Hanoverian', '56.379', 'First Level, Test 1 2011', 'Adult Amateur');";
		$result = mysql_query( $sql, $dbh); 

		$sql = "DROP TABLE IF EXISTS `tm_example_link_table`;";
		$result = mysql_query( $sql, $dbh); 
		
		$sql = "CREATE TABLE IF NOT EXISTS `tm_example_link_table` (
			`Column1` varchar(100) DEFAULT NULL,
			`Column2` varchar(100) DEFAULT NULL,
			`Column3` varchar(200) DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "INSERT INTO `tm_example_link_table` (`Column1`, `Column2`, `Column3`) VALUES
			('Wordpress.org', 'http://wordpress.org/', 'This link will take you to the Wordpress.org website.'),
			('Home', 'http://www.codehorsesoftware.com/', 'This link will take you to the codehorsesoftware.com .'),
			('TableMaster', 'http://www.codehorsesoftware.com/tablemaster-wordpress-plugin/', 'This link will take you to the User\'s Guide web page.');";
		$result = mysql_query( $sql, $dbh); 

	}
	
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package    Tablemaster
 * @subpackage Tablemaster/includes
 * @author     Valerie Mallder <horsenut@comcast.net>
 */
	function deactivate() {
	
		$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD );
		mysql_select_db( DB_NAME, $dbh );
		
		$sql = "DROP VIEW IF EXISTS `tm_example_view`;";
		$result = mysql_query( $sql, $dbh);
 
		$sql = "DROP TABLE IF EXISTS `tm_example_table`;";
		$result = mysql_query( $sql, $dbh); 

		$sql = "DROP TABLE IF EXISTS `tm_example_long_table`;";
		$result = mysql_query( $sql, $dbh); 

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugins_url() . 'css/tablemaster.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {

//		wp_enqueue_script( $this->plugin_name, plugins_url() . 'js/tablemaster-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function print_footer_scripts() {

//		wp_enqueue_script( $this->plugin_name, plugins_url() . 'js/tablemaster-admin.js', array( 'jquery' ), $this->version, false );

	}
}
}
?>
