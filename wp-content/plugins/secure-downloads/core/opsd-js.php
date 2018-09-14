<?php /**
 * @version 1.0
 * @package Secure Downloads 
 * @category JavaScript files and varibales
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 19.10.2015
 */

class OPSD_JS extends OPSD_JS_CSS {
    
    public function define() {
        
        $this->setType('js');
        
        /*
        $this->add( array(
                            'handle' => 'opsd-datepick',
                            'src' => opsd_plugin_url( '/js/datepick/jquery.datepick.js'), 
                            'deps' => array( 'opsd-global-vars' ),
                            'version' => '1.1',
                            'where_to_load' => array( 'admin', 'client' ),                //Usage: array( 'admin', 'client' )
                            'condition' => false    
                  ) );        
        */
    }

    /** Enqueue Files and Varibales. 
     *  Useful in case, if we use get_options and current user functions...
     * 
     * @param type $where_to_load
     */
    public function enqueue( $where_to_load ) {
        
        opsd_js_load_vars(  $where_to_load );
        
        // Define JavaScript varibales in all other files
        do_action( 'opsd_define_js_vars', $where_to_load );                         

        opsd_js_load_libs(  $where_to_load );
        opsd_js_load_files( $where_to_load );
        
        if ( opsd_is_new_opsd_page() )   
            $where_to_load = 'both';
        
        // Load JavaScript files in all other versions
        do_action( 'opsd_enqueue_js_files', $where_to_load );                     
    }

    /** Deregister  some conflict  scripts from  other plugins.
     * 
     * @param type $where_to_load
     */
    public function remove_conflicts( $where_to_load ) {
        
        if ( opsd_is_master_page() ) {
            if (function_exists('wp_dequeue_script')) {
                
                //wp_dequeue_script( 'jquery.cookie' );
                //wp_dequeue_script( 'jquery-interdependencies' );
                wp_dequeue_script( 'chosen' );
                wp_dequeue_script( 'cs-framework' );
                wp_dequeue_script( 'cgmp-jquery-tools-tooltip' );                               // Remove this script jquery.tools.tooltip.min.js, which is load by the "Comprehensive Google Map Plugin"
            }
        }        
                        
    }
}



/** Define JavaScript Varibales */
function opsd_js_load_vars( $where_to_load ) {
    
    ////////////////////////////////////////////////////////////////////////////
    // JavaScripts Variables               
    ////////////////////////////////////////////////////////////////////////////
      
    wp_enqueue_script( 'opsd-global-vars', opsd_plugin_url( '/js/opsd_vars.js' ), array( 'jquery' ), '1.1' );        // Blank JS File 
        
    wp_localize_script( 'opsd-global-vars'
                      , 'opsd_global1', array(
          'opsd_ajaxurl'                        => admin_url( 'admin-ajax.php' )
        , 'opsd_plugin_url'                     => plugins_url( '' , OPSD_FILE )                                                     
        , 'opsd_today'       => '['     . intval(date_i18n('Y'))            //FixIn:6.1
                                        .','. intval(date_i18n('m')) 
                                        .','. intval(date_i18n('d'))
                                        .','. intval(date_i18n('H'))
                                        .','. intval(date_i18n('i'))
                                    .']'
        , 'opsd_plugin_filename'            => OPSD_PLUGIN_FILENAME 
        , 'message_verif_requred'               => esc_js(__('This field is required' , 'secure-downloads'))
        , 'message_verif_requred_for_check_box' => esc_js(__('This checkbox must be checked' , 'secure-downloads'))
        , 'message_verif_requred_for_radio_box' => esc_js(__('At least one option must be selected' , 'secure-downloads'))
        , 'message_verif_emeil'                 => esc_js(__('Incorrect email field' , 'secure-downloads'))
        , 'message_verif_same_emeil'            => esc_js(__('Your emails do not match' , 'secure-downloads'))          // Email Addresses Do Not Match
                          
        , 'opsd_active_locale'                  => opsd_get_locale()  
        , 'opsd_message_processing'             => esc_js( __('Processing' , 'secure-downloads') )
        , 'opsd_message_deleting'               => esc_js( __('Deleting' , 'secure-downloads') )
        , 'opsd_message_updating'               => esc_js( __('Updating' , 'secure-downloads') )
        , 'opsd_message_saving'                 => esc_js( __('Saving' , 'secure-downloads') )
    ));
        
}


/** Default JavaScripts Libraries */
function opsd_js_load_libs( $where_to_load ) {
    
    // jQuery  
    wp_enqueue_script( 'jquery' );

    $version = opsd_get_registered_jquery_version();

    
    

    if ( $version !== false ) {
        
        if ( version_compare( $version, '1.9.1', '<' ) ) {                      // load jQuery 1.7.1, if "Theme" load older jQuery      //FixIn: 7.0.1.3 
            wp_deregister_script('jquery');
            wp_register_script( 'jquery', 'http://code.jquery.com/jquery-1.9.1.min.js', false, '1.9.1' );                   //FixIn: 7.0.1.3 
            // wp_register_script('jquery', 'http://code.jquery.com/jquery-latest.min.js', false, false);                
            wp_enqueue_script('jquery');
            
            wp_register_script('jquery-migrate', 'http://code.jquery.com/jquery-migrate-1.0.0.js', false, '1.0.0' );       //FixIn: 7.0.1.3  
            wp_enqueue_script( 'jquery-migrate' );                                                                         //FixIn: 7.0.1.3  
        }

        ////////////////////////////////////////////////////////////////////////
        // jQuery Migrate 
        if ( version_compare( $version, '1.9', '>=' ) ) {                       // if the jQuery newer then 1.9
            wp_register_script('jquery-migrate', 'http://code.jquery.com/jquery-migrate-1.0.0.js', false, '1.0.0' );
            wp_enqueue_script( 'jquery-migrate' );
        }
    }

    
    // Default Admin Libs 
    if (     ( $where_to_load == 'admin' ) 
         // || (  is_admin() && ( defined( 'DOING_AJAX' ) ) && ( DOING_AJAX )  )
        ) {
        
		wp_enqueue_media();
 
		wp_enqueue_script('thickbox');
        // Load thickbox CSS
        wp_enqueue_style('thickbox');
		
        wp_enqueue_style(  'wp-color-picker' );                                 // Color Picker
        wp_enqueue_script( 'wp-color-picker' ); 
        wp_enqueue_script( 'jquery-ui-sortable' );                              // UI Sortable
//        if ( opsd_is_master_page()  )
//            wp_enqueue_script( 'jquery-ui-dialog' );                            // UI Dialog -  for payment request dialog                                     
    }   
    
}


/** Load JavaScript Files */
function opsd_js_load_files( $where_to_load ) {
    
    // Bootstrap 
    if (     (  (   is_admin() ) && ( get_opsd_option( 'opsd_is_not_load_bs_script_in_admin' )  !== 'On')  ) 
         // ||  (  ( ! is_admin() ) && ( get_opsd_option( 'opsd_is_not_load_bs_script_in_client' ) !== 'On' )  )
       ) {
        wp_enqueue_script( 'wpdevelop-bootstrap', opsd_plugin_url( '/assets/libs/bootstrap/js/bootstrap.js' ), array( 'opsd-global-vars' ), '3.3.5.1');
    }
     
    // Datepicker    
    // wp_enqueue_script( 'opsd-datepick', opsd_plugin_url( '/js/datepick/jquery.datepick.js'), array( 'opsd-global-vars' ), '1.1');

    // Localization
    // $calendar_localization_url = opsd_get_calendar_localization_url();
    // if ( ! empty( $calendar_localization_url ) )
    //    wp_enqueue_script( 'opsd-datepick-localize', $calendar_localization_url, array( 'opsd-datepick' ), '1.1');
    //opsd_load_calendar_localization_file();
                
    if (  ( $where_to_load == 'client' ) || ( opsd_is_new_opsd_page()  )   ) {
        
        // Client
        // wp_enqueue_script( 'opsd-main-client', opsd_plugin_url( '/js/client.js'), array( 'opsd-datepick' ), '1.1');
    }
    
    if ( $where_to_load == 'admin' ) {
        
        // Admin
        wp_enqueue_script( 'opsd-admin-main',    opsd_plugin_url( '/js/admin.js'), array( 'opsd-global-vars' ), '1.1');
        wp_enqueue_script( 'opsd-admin-support', opsd_plugin_url( '/core/any/js/admin-support.js'), array( 'opsd-global-vars' ), '1.1');
    
        // Chosen Library    
        wp_enqueue_script( 'opsd-chosen', opsd_plugin_url( '/assets/libs/chosen/chosen.jquery.min.js'), array( 'opsd-global-vars' ), '1.1' );    
    }    
        
}



////////////////////////////////////////////////////////////////////////////////
//  Support JavaScript functions
////////////////////////////////////////////////////////////////////////////////

/** Load Datepicker Localization JS File */
/*
function opsd_load_calendar_localization_file() {
    
    // Datepicker Localization - translation for calendar.                      Example:    $locale = 'fr_FR';   
    $locale = opsd_get_locale();                                              
    if ( ! empty( $locale ) ) {

        $locale_lang    = substr( $locale, 0, 2 ); 
        $locale_country = substr( $locale, 3 );

        if (   ( $locale_lang !== 'en') && ( opsd_is_file_exist( '/js/datepick/jquery.datepick-' . $locale_lang . '.js' ) )   ) { 
            
                wp_enqueue_script( 'opsd-datepick-localize', opsd_plugin_url( '/js/datepick/jquery.datepick-'. $locale_lang . '.js' ), array( 'opsd-datepick' ), '1.1');

        } else if (   ( ! in_array( $locale, array( 'en_US', 'en_CA', 'en_GB', 'en_AU' ) )   )                                      // English Exceptions 
                   && ( opsd_is_file_exist( '/js/datepick/jquery.datepick-'. $locale_country . '.js' ) ) 
        ) { 

                wp_enqueue_script( 'opsd-datepick-localize', opsd_plugin_url( '/js/datepick/jquery.datepick-'. $locale_country . '.js' ), array( 'opsd-datepick' ), '1.1');                
        }          
    }
}*/


/** Get URL Datepicker Localization JS File 
 * 
 * @return string - URL to  calendar skin
 */
/*
function opsd_get_calendar_localization_url() {
    // Datepicker Localization - translation for calendar.                      Example:    $locale = 'fr_FR';   
    $locale = opsd_get_locale();                                              
    
    $calendar_localization_url = false;
    
    if ( ! empty( $locale ) ) {

        $locale_lang    = substr( $locale, 0, 2 ); 
        $locale_country = substr( $locale, 3 );

        if (   ( $locale_lang !== 'en') && ( opsd_is_file_exist( '/js/datepick/jquery.datepick-' . $locale_lang . '.js' ) )   ) { 
            
                $calendar_localization_url = opsd_plugin_url( '/js/datepick/jquery.datepick-'. $locale_lang . '.js' );

        } else if (   ( ! in_array( $locale, array( 'en_US', 'en_CA', 'en_GB', 'en_AU' ) )   )                                      // English Exceptions 
                   && ( opsd_is_file_exist( '/js/datepick/jquery.datepick-'. $locale_country . '.js' ) ) 
        ) { 

                $calendar_localization_url = opsd_plugin_url( '/js/datepick/jquery.datepick-'. $locale_country . '.js' );                
        }          
    } 
    
    return $calendar_localization_url;
}
*/

/** Get Registred jQuery version
 * 
 * @global type $wp_scripts
 * @return string - jQuery version
 */
function opsd_get_registered_jquery_version() {
    global $wp_scripts;
    
    $version = false;
    
    if (  is_a( $wp_scripts, 'WP_Scripts' ) ) 
        if (isset( $wp_scripts->registered['jquery'] )) 
            $version = $wp_scripts->registered['jquery']->ver;
    return $version;
}


/** Check if we activated loading of JS/CSS only on specific pages and then load or no it
 * 
 * @param boolean $is_load_scripts  - Default: true
 * @return boolean                  - true | false
 */
function opsd_is_load_css_js_on_client_page( $is_load_scripts ) {

return true;

    if ( ! is_admin() ) {           // Check  on Client side only
        
        $opsd_is_load_js_css_on_specific_pages = get_opsd_option( 'opsd_is_load_js_css_on_specific_pages'  );
        if ( $opsd_is_load_js_css_on_specific_pages == 'On' ) {
            
            $opsd_pages_for_load_js_css = get_opsd_option( 'opsd_pages_for_load_js_css' );

            $opsd_pages_for_load_js_css = preg_split('/[\r\n]+/', $opsd_pages_for_load_js_css, -1, PREG_SPLIT_NO_EMPTY);

            $request_uri = $_SERVER['REQUEST_URI'];                                 // FixIn:5.4.1
            if ( strpos( $request_uri, 'opsd_hash=') !== false ) {
                $request_uri = parse_url($request_uri);
                if (  ( ! empty($request_uri ) ) && ( isset($request_uri['path'] ) )  ){
                    $request_uri = $request_uri['path'];
                } else {
                    $request_uri = $_SERVER['REQUEST_URI'];
                }
            }

            if (  ( ! empty($opsd_pages_for_load_js_css ) ) && ( ! in_array( $request_uri, $opsd_pages_for_load_js_css ) )  )
                    return false;
        }
    }
    return true;
}
add_filter( 'opsd_is_load_script_on_this_page', 'opsd_is_load_css_js_on_client_page' );
