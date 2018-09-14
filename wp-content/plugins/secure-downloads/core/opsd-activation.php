<?php
/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage Activation / Deactivation
 * @category Functions
 * @author      wpdevelop
 *
 * @web-site    http://oplugins.com/
 * @email       info@oplugins.com 
 * @modified    2016-03-17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Activation  & Deactivation  of Secure Downloads  */
class OPSD_ItemInstall extends OPSD_Install {

    /** Overload Secure Downloads option names and some other parameters */
    public function get_init_option_names() {
        
        add_opsd_action( 'opsd_activate_user', array( $this, 'opsd_activate') );        // Hook  for MU User activation 
        
        return  array(
                  'option-version_num'                  => 'opsd_version_num'
                , 'option-is_delete_if_deactive'        => 'opsd_is_delete_if_deactive'
                , 'option-activation_process'           => 'opsd_activation_process'
                , 'transient-opsd_activation_redirect'  => '_opsd_activation_redirect'
                , 'message-delete_data'                 =>  '<strong>' . __('Warning!', 'secure-downloads') . '</strong> '
                                                            . __('All plugin data will be deleted when the plugin is deactivated.', 'secure-downloads') 
                                                            . '<br />'
                                                            . sprintf( __('If you want to save your plugin data, please uncheck the %s"Delete data"%s at the' , 'secure-downloads')
                                                                       , '<strong>', '</strong>') 
                                                            . '<a href="' . esc_url( admin_url( add_query_arg( array( 'page' => 'opsd-settings' ), 'admin.php' ) ) ) 
                                                                     . '#opsd_general_settings_uninstall_metabox"> ' .  __('settings page', 'secure-downloads') . '.' 
                                                            . ' </a>'
                , 'link_settings'                       => '<a href="' . esc_url( admin_url( add_query_arg( array( 'page' => 'opsd-settings' ), 'admin.php' ) ) ) 
                                                                       . '">'.__("Settings", 'secure-downloads').'</a>'
                , 'link_whats_new'                      => ''
        );                
        
    }
    
    /** Check if was updated from lower to  high version */
    public function is_update_from_lower_to_high_version() {
        
        $is_make_activation = false;
        
        // Check  conditions for different version about Upgrade
        if ( ( class_exists( 'opsd_personal' ) ) && ( ! opsd_is_table_exists( 'itemtypes' ) ) )
            $is_make_activation = true;

        return $is_make_activation;
    }

}






////////////////////////////////////////////////////////////////////////////////
//   A c t i v a t e    &    D e a c t i v a t e
////////////////////////////////////////////////////////////////////////////////

/** Activation */
function opsd_activate() {
    
				// Check for blank  data install
				$opsd_secret_key = get_opsd_option( 'opsd_secret_key' );
				if ( empty( $opsd_secret_key ) ) 
					$is_first_time_install = true;
				else
					$is_first_time_install = false;


    make_opsd_action( 'opsd_before_activation' );
    
    opsd_load_translation();
    
    $version = get_opsd_version();
    $is_demo = opsd_is_this_demo();

    ////////////////////////////////////////////////////////////////////////////
    // Options
    ////////////////////////////////////////////////////////////////////////////
    $default_options_to_add = opsd_get_default_options();
    
			
    foreach ( $default_options_to_add as $default_option_name => $default_option_value ) {
        
        add_opsd_option( $default_option_name, $default_option_value );
    }

				// Check for blank  data install
				if ( $is_first_time_install )
					opsd_create_blank_files();

    ////////////////////////////////////////////////////////////////////////////
    // DB Tables
    ////////////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////////////////////////////////////////////////
    // Other versions Activation
    ////////////////////////////////////////////////////////////////////////////
    make_opsd_action( 'opsd_other_versions_activation' );
          
    make_opsd_action( 'opsd_after_activation' );
}
add_opsd_action( 'opsd_activation',  'opsd_activate' );



// Deactivate
function opsd_deactivate() {

    ////////////////////////////////////////////////////////////////////////////
    // Options
    ////////////////////////////////////////////////////////////////////////////

    $default_options_to_add = opsd_get_default_options();
    foreach ( $default_options_to_add as $default_option_name => $default_option_value) {
        
        delete_opsd_option( $default_option_name );
    }   
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Widgets
    ////////////////////////////////////////////////////////////////////////////
    delete_opsd_option( 'opsd_activation_redirect_for_version' );
    
    
    ////////////////////////////////////////////////////////////////////////////
    // DB Tables
    ////////////////////////////////////////////////////////////////////////////
    global $wpdb;
    // $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}opsd" );
 
    // Delete all users item windows states   
    if ( false === $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%opsd_%'" ) ){    // All users data
        debuge_error('Error during deleting user meta at DB',__FILE__,__LINE__);
        die();
    }
     
    ////////////////////////////////////////////////////////////////////////////
    // Other versions Deactivation
    ////////////////////////////////////////////////////////////////////////////
    make_opsd_action('opsd_other_versions_deactivation');                         
}
add_opsd_action( 'opsd_deactivation',  'opsd_deactivate' );


/** Default Options 
 * 
 *  $option_name = '';
 *  $options_for_delete = opsd_get_default_options( $option_name, $is_get_multiuser_general_options );
 */
function opsd_get_default_options( $option_name = '' ) {

    $is_demo = opsd_is_this_demo();

	$default_options = array();	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// General Settings
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$default_options[ 'opsd_download_url_path' ] = '';
	$default_options[ 'opsd_protected_directory_name_level1' ] = 'opsd_' . wp_generate_password( 20, false, false );
	$default_options[ 'opsd_secret_key' ] = wp_generate_password( 30, false, false );
	$default_options[ 'opsd_defualt_expiration' ] = '+24 hours';
	$default_options[ 'opsd_defualt_iplock' ] = '';	
	// URLs
	$default_options[ 'opsd_url_wrong_hash' ] = '';
	$default_options[ 'opsd_url_download_expired' ] = '';
	$default_options[ 'opsd_url_ip_not_valied' ] = '';
	$default_options[ 'opsd_url_file_not_exist' ] = '';
	$default_options[ 'opsd_url_error_opening_file' ] = '';	
	// Miscellaneous
	$default_options[ 'opsd_csv_separator' ] = ',';
	$default_options[ 'opsd_date_format' ] = get_option( 'date_format' );
	$default_options[ 'opsd_time_format' ] = get_option( 'time_format' );	
	// Advanced
	$default_options[ 'opsd_is_not_load_bs_script_in_admin' ] = 'Off';	
	/**
	$default_options[ 'opsd_is_not_load_bs_script_in_client' ] = 'Off';	
	$default_options[ 'opsd_is_load_js_css_on_specific_pages' ] = 'Off';
	$default_options[ 'opsd_pages_for_load_js_css' ] = '';
	*/	
	// User permissions
	$default_options[ 'opsd_user_role_master' ] = ( $is_demo ) ? 'subscriber' : 'editor';
	$default_options[ 'opsd_user_role_addnew' ] = ( $is_demo ) ? 'subscriber' : 'editor';
	$default_options[ 'opsd_user_role_settings' ] = ( $is_demo ) ? 'subscriber' : 'editor';
	// Position
	$default_options[ 'opsd_menu_position' ] = ( $is_demo ) ? 'top' : 'top';
	// Uninstall
	$default_options[ 'opsd_is_delete_if_deactive' ] = ($is_demo) ? 'On' : 'Off';
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Emails 
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$user_info = array( 'name' => '' );
		if ( is_user_logged_in() ) {			
			$user_data         = get_userdata( get_current_user_id() );
			$user_info['name'] = ( $user_data ) ? $user_data->display_name : '';
		}

		$email_settings = array();
		$email_settings['enabled'] = 'On';
		$email_settings['copy_to_admin'] = 'On';
		$email_settings['from'] = get_option( 'admin_email' );
		$email_settings['from_name'] = $user_info['name'];
		$email_settings['subject'] = sprintf( __( 'Delivery of %s', 'secure-downloads'), '[product_title] [product_version]' );
		$email_settings['content'] = sprintf( __( 'Hello. %sThank you for requesting %s To download %s click the link below: %s Thank you, %s', 'secure-downloads' )
												, '<br/>'
												, '[product_title] [product_version]<br/><br/>'
												, '<strong>[product_description]</strong>'
												, '<br/> --- <br/> [product_summary] - [product_expire_date] <br/> --- <br/> <br/> '
												, '[siteurl]<br/> [current_date] [current_time]' );
		$email_settings['header_content'] = '';
		$email_settings['footer_content'] = '';
		$email_settings['template_file'] = 'plain';
		$email_settings['base_color'] = '#557da1';
		$email_settings['background_color'] = '#f5f5f5';
		$email_settings['body_color'] = '#fdfdfd';
		$email_settings['text_color'] = '#505050';
		$email_settings['email_content_type'] = 'html';
	$default_options[ 'opsd_email_link_user' ] = $email_settings;
	
		// Just  modify  some params in previos email
		$email_settings[ 'to' ] = $email_settings['from'];
		$email_settings[ 'to_name' ] = $email_settings['from_name'];
		$email_settings['subject'] = sprintf( __( 'Download notification of %s', 'secure-downloads'), '[product_title] [ [product_id] ]' );
		$email_settings['content'] = sprintf( __( 'Hi. %s The %s have been downloaded. %s ===== User Info ===== %s To: %s IP:  %s User agent: %s Request url: %s ====================== %s ===== Product Summary ===== %s Expire at %s ====================== %s', 'secure-downloads' )
												, '<br/><br/>'
												, '<strong>[product_title] [product_version]</strong> [ID=[product_id]]'
												, '<br/><br/>'
												, '<br/>'
												, '[link_sent_to]' . '<br/>'
												, '[remote_ip]' . '<br/>'
												, '[user_agent]' . '<br/>'
												, '[request_url]' . '<br/>'
												, '<br/><br/>'
												, '<br/>[product_summary] <br/>[product_description] <br/>'
												, '<strong>[product_expire_date]</strong> '. '<br/>'
												, '<br/> <br/><strong>[current_date] [current_time]</strong>'
											);
	$default_options[ 'opsd_email_download_notification' ] = $email_settings;
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// C S V  P R O D U C T S  -  F I L E S 
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$default_options[ 'opsd_products_csv' ] = '';
	
	
	if ( ! empty( $option_name ) ) {
		
		if ( isset( $default_options[ $option_name ] ) )
			return $default_options[ $option_name ];                        // Return 1 option
		else
			return  false;                                                  // Option does NOT exist
		
	} else {
		return $default_options;                                            // Return  ALL
	}
}
