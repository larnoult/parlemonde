<?php
/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage Core
 * @category Items
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.07.29
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


if ( ! class_exists( 'OPSD_Init' ) ) :

    
// General Init Class    
final class OPSD_Init {
        
    static private $instance = NULL;

    public $cron;
    public $notice;
    public $opsd_obj;    
    

    public $admin_menu;
    public $js;
    public $css;
    

/** Get Single Instance of this Class and Init Plugin */
public static function init() {
    
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof OPSD_Init ) ) {
            
                    global $opsd_settings;
                    $opsd_settings = array();
        
        self::$instance = new OPSD_Init;
        self::$instance->constants();
        self::$instance->includes();
        self::$instance->define_version();


        if ( class_exists( 'OPSD_ItemInstall' ) ) {                                 // Check if we need to run Install / Uninstal process.
            new OPSD_ItemInstall();
        }

        // TODO: Finish here
        //add_action('plugins_loaded', array(self::$instance, 'load_textdomain') );   // T r a n s l a t i o n
        
        
        $is_continue = self::$instance->start();                                // Make Ajax, Response or Define item ClASS

        make_opsd_action( 'opsd_calendar_started' );
        do_action( 'opsd_calendar_started' );
		
        //TODO:  NEW        
        if ( $is_continue ) {                                                   // Possible Load Admin or Front-End page
            
            self::$instance->js     = new OPSD_JS;
            self::$instance->css    = new OPSD_CSS;

            if( is_admin() ) {

                // Define Menu
                add_action( '_admin_menu',   array( self::$instance, 'define_admin_menu') );    // _admin_menu - Fires before the administration menu loads in the admin.

                add_action( 'admin_footer', 'opsd_print_js', 50 );              // Load my Queued JavaScript Code at  the footer of the Admin Panel page. Executed in ALL Admin Menu Pages
                
            } else {  
                
                if ( function_exists( 'opsd_br_cache' ) ) $br_cache = opsd_br_cache();  // Init item resources cache
                
                add_action( 'wp_enqueue_scripts', array(self::$instance->css, 'load'), 1000000001 );   // Load CSS at front-end side  // Enqueue Scripts to All Client pages 
                add_action( 'wp_enqueue_scripts', array(self::$instance->js,  'load'), 1000000001 );   // Load JavaScript files and define JS varibales at forn-end side
                add_action( 'wp_footer', 'opsd_print_js', 50 );                 // Load my Queued JavaScript Code at  the footer  of the page, if executed "wp_footer" hook at the Theme.
            }            
        }
                
    }
    return self::$instance;        
}


/** Define Admin Menu items */
public function define_admin_menu(){
    
    $update_count = opsd_get_number_new_items();

    $title = 'Secure Downloads';				//'&#223;<span style="font-size:0.75em;">&#920;&#920;</span>&kgreen;&imath;&eng;';   

	if ( $update_count > 0 ){
        $update_count_title = "<span class='update-plugins count-$update_count' title=''><span class='update-count bk-update-count'>" . number_format_i18n($update_count) . "</span></span>" ;
        $title .= $update_count_title;
    }
    
    
    //global $menu;
    //if ( current_user_can(  ) ) {
    //$menu[] = array( '', 'read', 'separator-opsd', '', 'wp-menu-separator opsd' );
    //}
    // debuge($menu); 
    
    $opsd_menu_position = get_opsd_option( 'opsd_menu_position' );
    switch ( $opsd_menu_position ) {
        case 'top':
            $opsd_menu_position = 3.13;
            break;
        case 'middle':    
            global $_wp_last_object_menu;                                       // The index of the last top-level menu in the object menu group
            $_wp_last_object_menu++;
            $opsd_menu_position = $_wp_last_object_menu; // 58.9;
            break;
        case 'bottom':
            $opsd_menu_position = 99.919;
            break;
        default:
            $opsd_menu_position = 3.13;
            break;
    }
    
    
    self::$instance->admin_menu['master'] = new OPSD_Admin_Menus( 
                                                    'opsd' , array (
                                                    'in_menu' => 'root'                                                               
                                                  , 'mune_icon_url' => '/assets/img/icon-16x16.png'      
                                                  , 'menu_title' => $title
                                                  , 'menu_title_second' => __('Secure Links', 'secure-downloads')
                                                  , 'page_header' => __('Secure Downlods - Generate and Send Secure Links', 'secure-downloads')
                                                  , 'browser_header' =>  __('Secure Downlods - Links', 'secure-downloads') 
                                                  , 'user_role' => get_opsd_option( 'opsd_user_role_master' )
                                                  , 'position' => $opsd_menu_position // 3.3 - top           //( 58.9 )  // - middle
                                                                                /*  
                                                                                (Optional). Positions for Core Menu Items
                                                                                    2 Dashboard
                                                                                    4 Separator
                                                                                    5 Posts
                                                                                    10 Media
                                                                                    15 Links
                                                                                    20 Pages
                                                                                    25 Comments
                                                                                    59 Separator
                                                                                    60 Appearance
                                                                                    65 Plugins
                                                                                    70 Users
                                                                                    75 Tools
                                                                                    80 Settings
                                                                                    99 Separator
                                                                                     */
                                                                            )
                                                  
                                                );
   
    self::$instance->admin_menu['files']    = new OPSD_Admin_Menus( 
                                                    'opsd-files' , array (
                                                    'in_menu' => 'opsd'     
                                                  , 'menu_title'    => ucwords( __('Files', 'secure-downloads') )
                                                  , 'page_header'   => ucwords( __('Add New', 'secure-downloads') )
                                                  , 'browser_header'=> ucwords( __('Files', 'secure-downloads') ) 
                                                  , 'user_role' => get_opsd_option( 'opsd_user_role_addnew' )  
                                                                            )
                                                );


    self::$instance->admin_menu['settings'] = new OPSD_Admin_Menus( 
                                                    'opsd-settings' , array (
                                                    'in_menu' => 'opsd'                                                                         
                                                  , 'menu_title'    => __('Settings', 'secure-downloads')
                                                  , 'page_header'   => __('General Settings', 'secure-downloads')
                                                  , 'browser_header'=> __('Settings', 'secure-downloads') 
                                                  , 'user_role' => get_opsd_option( 'opsd_user_role_settings' )
                                                                            )
                                                );        
    
}

    
    
    /** Get Menu Object
     * 
     * @param type  - menu type
     * @return boolean
     */
    public function get_menu_object( $type ) {

        if ( isset( self::$instance->admin_menu[ $type ] ) )
            return self::$instance->admin_menu[ $type ];
        else 
            return false;
    }


    // Define constants
    private function constants() {
        require_once OPSD_PLUGIN_DIR . '/core/opsd-constants.php' ; 
    }
    
    
    // Include Files
    private function includes() {
        require_once OPSD_PLUGIN_DIR . '/core/opsd-include.php' ; 
    }
    
        
    private function define_version() {
        
        // GET VERSION NUMBER
        $plugin_data = get_file_data_wpdev(  OPSD_FILE , array( 'Name' => 'Plugin Name', 'PluginURI' => 'Plugin URI', 'Version' => 'Version', 'Description' => 'Description', 'Author' => 'Author', 'AuthorURI' => 'Author URI', 'TextDomain' => 'Text Domain', 'DomainPath' => 'Domain Path' ) , 'plugin' );
        if (!defined('OPSD_VERSION'))    define('OPSD_VERSION',   $plugin_data['Version'] );
    }

    
    /**
     * Load Plugin Locale.
     * Look firstly in Global folder: /wp-content/languages/plugin_name
     *         then in Local  folder: /wp-content/plugins/plugin_name/languages/
     * and afterwards load default  : load_plugin_textdomain( ...
     */
    public function load_textdomain() {
        // Set filter for plugin's languages directory
        $plugin_lang_dir = OPSD_PLUGIN_DIR . '/languages/';
        $plugin_lang_dir = apply_filters( 'opsd~languages_directory', $plugin_lang_dir );

        // Plugin locale filter
        $locale        = apply_filters( 'plugin_locale',  get_locale() , 'secure-downloads');
        $mofile        = sprintf( '%1$s-%2$s.mo', 'secure-downloads', $locale );

        // Setup paths to current locale file
        $mofile_local  = $plugin_lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . '/secure-downloads/' . $mofile;

        if ( file_exists( $mofile_global ) ) {                      
            // Look in global /wp-content/languages/plugin_name folder
            load_textdomain( 'secure-downloads', $mofile_global );                       
            
        } elseif ( file_exists( $mofile_local ) ) {                
            // Look in local /wp-content/plugins/plugin_name/languages/ folder
            load_textdomain( 'secure-downloads', $mofile_local );                        
            
        } else {                
            // Load the default language files
            load_plugin_textdomain( 'secure-downloads', false, $plugin_lang_dir );       
        }
    }    
    
    
    // Cloning instances of the class is forbidden
    public function __clone() {

        _doing_it_wrong( __FUNCTION__, __( 'Action is not allowed!' ), '1.0' );
    }

    
    // Unserializing instances of the class is forbidden
    public function __wakeup() {

        _doing_it_wrong( __FUNCTION__, __( 'Action is not allowed!' ), '1.0' );
    }

    
    // Initialization
    private function start(){
        
        if (  ( defined( 'DOING_AJAX' ) )  && ( DOING_AJAX )  ){                        // New A J A X    R e s p o n d e r

            

            require_once OPSD_PLUGIN_DIR . '/core/opsd-ajax.php';                        // Ajax 
            
            return false;
        } else {                                                                        // Usual Loading of plugin

            // We are having Response, its executed in other file: opsd-response.php
            if ( OPSD_RESPONSE )
                return false;
			
            ////////////////////////////////////////////////////////////////////
        }
        return true;
    }
    
}

else:   // Its seems that  some instance of Secure Downloads still activted!!!
    

    function opsd_show_activation_error() {

        $message_type = 'error';
        $title        = __( 'Error' , 'secure-downloads') . '!';
        $message      = __( 'Please deactivate previous old version of' , 'secure-downloads') . ' ' . 'Item Calendar';
        
        $opsd_version_num = get_option( 'opsd_version_num');        
        if ( ! empty( $opsd_version_num ) )
            $message .= ' <strong>' . $opsd_version_num . '</strong>'; 
        
        
        $is_delete_if_deactive =  get_opsd_option( 'opsd_is_delete_if_deactive' ); // check

        if ( $is_delete_if_deactive == 'On' ) { 
            
            $message .= '<br/><br/> <strong>Warning!</strong> ' . 'All plugin data will be deleted when plugin had deactivated.' . ' '
                . sprintf( 'If you want to save your plugin data, please uncheck the %s"Delete plugin data"%s at the', '<strong>', '</strong>') . ' ' . __( 'Settings' , 'secure-downloads') . '.';
        }
        
        $message_content = '';

        $message_content .= '<div class="clear"></div>';

        $message_content .= '<div class="updated opsd-settings-notice notice-' . $message_type . ' ' . $message_type . '" style="text-align:left;padding:10px;">';

        if ( ! empty( $title ) )
        $message_content .=  '<strong>' . esc_js( $title ) . '</strong> ';

        $message_content .= html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;

        $message_content .= '</div>';

        $message_content .= '<div class="clear"></div>';
        
        echo $message_content;
    }    
    
    add_action('admin_notices', 'opsd_show_activation_error');    
    
    return;         // Exit

endif;


/**
 * The main function responsible for returning the one true Instance to functions everywhere.
 *
 * Example: <?php $opsd = OPSD(); ?>
 */
function OPSD() {
    return OPSD_Init::init();
}



// Start
OPSD();



//if (  ! defined( 'SAVEQUERIES') ) define('SAVEQUERIES', true);

 //add_action( 'admin_footer', 'opsd_show_debug_info', 130 ); 
function opsd_show_debug_info() {
    
    $request_uri = $_SERVER['REQUEST_URI'];                                 // FixIn:5.4.1
    if ( strpos( $request_uri, 'page=opsd') === false ) {
        return;
    }
    echo '<div style="width:800px;margin:10px auto;"><style type="text/css"> a:link{background: inherit !important; } pre { white-space: pre-wrap; }</style>'; 
    
phpinfo();  echo '</div>'; return;
    
    ?><div style="width:auto;margin:0 0 0 215px;font-size:11px;    "><?php 

// SYSTEM  INFO SHOWING ////////////////////////////////////////////////////////
    
    //Note firstly  need to  define this in functions.php file:   define('SAVEQUERIES', true);
    global $wpdb;
    echo '<div class="clear"></div>START SYSTEM<pre>';
        $qq_kk = 0;
        $total_time = 0;
        $total_num = 0;
        foreach ( $wpdb->queries as $qq_k => $qq ) {
            if ( 
                       ( strpos( $qq[0], 'secure-downloads') !== false ) 

                ) {
                if ( $qq[1] > 0.002 ) { echo '<div style="color:#A77;font-weight:bold;">'; }
                debuge($qq_kk++, $qq);
                $total_time += $qq[1];
                $total_num++;
                if ( $qq[1] > 0.002 ) { echo '</div>'; }
            }
        }

        echo '<div><pre class="prettyprint linenums" style="font-size:18px;">[' . $total_num . '/' . $total_time . '] OPSD Requests TOTAL TIME</pre></div>';
    
        echo '<div class="clear"></div>'; 

        echo '<div><pre class="prettyprint linenums" style="font-size:18px;">' . get_num_queries(). '/'  . timer_stop(0, 3) . 'qps</pre></div>';
        
        echo '<div class="clear"></div>'; 
            
    echo "</pre>";
    ?><br/><br/><br/><br/><br/><br/><?php
    echo '<div class="clear"></div>'; 

////////////////////////////////////////////////////////////////////////////////
    ?></div><?php
    
    echo '</div>';
}