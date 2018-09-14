<?php /**
 * @version 1.0
 * @package Secure Downloads 
 * @category Content of Settings page 
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class OPSD_Page_SettingsGeneral extends OPSD_Page_Structure {
    
    private $settings_api = false;
     
    public function in_page() {
        
        return 'opsd-settings';
    }        
    
    /** Get Settings API class - define, show, update "Fields".
     * 
     * @return object Settings API
     */    
    public function settings_api(){
        
        if ( $this->settings_api === false )             
             $this->settings_api = new OPSD_Settings_API_General(); 
        
        return $this->settings_api;
    }    
    
    public function tabs() {
       
        $tabs = array();
                
        $tabs[ 'general' ] = array(
									  'title'			 => __( 'General', 'secure-downloads' )				// Title of TAB    
									, 'page_title'		 => __( 'General Settings', 'secure-downloads' )		// Title of Page    
									, 'hint'			 => __( 'General Settings', 'secure-downloads' )		// Hint    
									, 'link'			 => ''											// Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
									, 'position'		 => ''											// 'left'  ||  'right'  ||  ''
									, 'css_classes'		 => ''											// CSS class(es)
									, 'icon'			 => ''											// Icon - link to the real PNG img
									, 'font_icon'		 => 'glyphicon glyphicon-cog'					// CSS definition  of forn Icon
									, 'default'			 => true										// Is this tab activated by default or not: true || false. 
								);

		$subtabs = array();
        
        $subtabs['opsd-settings-url'] = array(   'type' => 'goto-link' 
                                                    , 'title' => __('URLs', 'secure-downloads') 
                                                    , 'show_section' => 'opsd_warning_url_settings_calendar_metabox'
                                                );
        
        
        $subtabs['opsd-settings-listing'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Misc', 'secure-downloads')            
                                                    , 'show_section' => 'opsd_general_settings_opsd_misc_metabox'
                                                );
        
        $subtabs['opsd-settings-menu-access'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Plugin Menu', 'secure-downloads')            
                                                    , 'show_section' => 'opsd_general_settings_permissions_metabox'
                                                );
                
        $subtabs['opsd-settings-uninstall'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Uninstall', 'secure-downloads')            
                                                    , 'show_section' => 'opsd_general_settings_uninstall_metabox'
                                                );
		
        $subtabs['opsd-settings-advanced'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Advanced', 'secure-downloads')            
                                                    , 'show_section' => 'opsd_general_settings_advanced_metabox'
                                                );
        
                
        
        $subtabs['form-save'] = array( 
                                        'type' => 'button'                                  
                                        , 'title' => __('Save Changes', 'secure-downloads')        
                                        , 'form' => 'opsd_general_settings_form'                
                                    );
                        
        
        $tabs[ 'general' ][ 'subtabs' ] = $subtabs;
        
        $tabs[ 'upgrade' ] = array(
									  'title'			 => __( 'Addons', 'secure-downloads' )				// Title of TAB    
									, 'page_title'		 => __( 'Extend functionaity', 'secure-downloads' )		// Title of Page    
									, 'hint'			 => __( 'Extend functionaity with premium addons', 'secure-downloads' )		// Hint    
									, 'link'			 => 'http://oplugins.com/plugins/secure-downloads/#premium'											// Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
									, 'position'		 => 'right'											// 'left'  ||  'right'  ||  ''
									, 'css_classes'		 => ''											// CSS class(es)
									, 'icon'			 => ''											// Icon - link to the real PNG img
									, 'font_icon'		 => 'glyphicon glyphicon-shopping-cart'					// CSS definition  of forn Icon
									, 'default'			 => false										// Is this tab activated by default or not: true || false. 
								);
		
        return $tabs;
    }


    public function content() {
                
        // Checking ////////////////////////////////////////////////////////////
        
        do_action( 'opsd_hook_settings_page_header', array( 'page' => $this->in_page() ) );					// Define Notices Section and show some static messages, if needed.
                    
        $is_can = apply_opsd_filter('recheck_version', true); if ( ! $is_can ) { ?><script type="text/javascript"> jQuery(document).ready(function(){ jQuery( '.wpdvlp-sub-tabs').remove(); }); </script><?php return; }
        
        
        // Init Settings API & Get Data from DB ////////////////////////////////
        $this->settings_api();                                                  // Define all fields and get values from DB
        
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'opsd_general_settings_form';                       // Define form name
                
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'opsd_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        //$opsd_user_role_master   = get_opsd_option( 'opsd_user_role_master' );    // O L D   W A Y:   Get Fields Data
        
        
        // JavaScript: Tooltips, Popover, Datepick (js & css) //////////////////
        echo '<span class="wpdevelop">';
        opsd_js_for_items_page();                                        
        echo '</span>';

              
        
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:10px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'opsd_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />

                <div class="opsd_settings_row opsd_settings_row_left" >

                    <?php opsd_open_meta_box_section( 'opsd_general_settings_calendar', __('General', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'general' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>
					
                    <?php opsd_open_meta_box_section( 'opsd_warning_url_settings_calendar', __('URL to pages after some errors', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'warning_url' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>
					
					
					
					
					
<?php /* ?>

                    <?php opsd_open_meta_box_section( 'opsd_general_settings_calendar', __('Calendar', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'calendar' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>


                    <?php opsd_open_meta_box_section( 'opsd_general_settings_availability', __('Availability', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'availability' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>

                    
                    <?php opsd_open_meta_box_section( 'opsd_general_settings_form', __('Form', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'form' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>

<?php */ ?>
                    <?php opsd_open_meta_box_section( 'opsd_general_settings_opsd_misc', __('Miscellaneous', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'opsd_listing' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>
                    
                     
                    
                </div>  
                <div class="opsd_settings_row opsd_settings_row_right">

                    <?php opsd_open_meta_box_section( 'opsd_general_settings_information', __('Information', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'information' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>                    


                    <?php opsd_open_meta_box_section( 'opsd_general_settings_permissions', __('Plugin Menu', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'permissions' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>                    

                    
                    <?php opsd_open_meta_box_section( 'opsd_general_settings_uninstall', __('Uninstall / deactivation', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'uninstall' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>                    

                                        
                    <?php opsd_open_meta_box_section( 'opsd_general_settings_advanced', __('Advanced', 'secure-downloads') );  ?>

                    <?php $this->settings_api()->show( 'advanced' ); ?>                                      
                    
                    <?php opsd_close_meta_box_section(); ?>
                    
                </div>                
                <div class="clear"></div>
                <input type="submit" value="<?php _e('Save Changes', 'secure-downloads'); ?>" class="button button-primary opsd_submit_button" />  
            </form>
            <?php if ( ( isset( $_GET['system_info'] ) ) && ( $_GET['system_info'] == 'show' ) ) { ?>
                
                <div class="clear" style="height:30px;"></div>
                
                <?php opsd_open_meta_box_section( 'opsd_general_settings_system_info', 'System Info' );  ?>

                <?php opsd_system_info(); ?>

                <?php opsd_close_meta_box_section(); ?>                    

            <?php } ?>
            
        </span>
    <?php 

    
    
        do_action( 'opsd_hook_settings_page_footer', 'general_settings' );
    
//debuge( 'Content <strong>' . basename(__FILE__ ) . '</strong> <span style="font-size:9px;">' . __FILE__  . '</span>');                  
    }


    public function update() {
//debuge($_POST);
        $validated_fields = $this->settings_api()->validate_post();             // Get Validated Settings fields in $_POST request.
        
        $validated_fields = apply_filters( 'opsd_settings_validate_fields_before_saving', $validated_fields );   //Hook for validated fields.
//debuge($validated_fields);
        // Skip saving specific option, for example in Demo mode.
        // unset($validated_fields['opsd_start_day_weeek']);

        $this->settings_api()->save_to_db( $validated_fields );                 // Save fields to DB
        opsd_show_changes_saved_message();
        
//debuge( basename(__FILE__), 'UPDATE',  $_POST, $validated_fields);          
                
        // O L D   W A Y:   Saving Fields Data
        //      update_opsd_option( 'opsd_is_delete_if_deactive'
        //                       , OPSD_Settings_API::validate_checkbox_post('opsd_is_delete_if_deactive') );  
        //      ( (isset( $_POST['opsd_is_delete_if_deactive'] ))?'On':'Off') );

    }
}



//if ( $is_other_tab ) {  
//    
//    if (  ( ! isset( $_GET['tab'] ) ) || ( $_GET['tab'] == 'general' )  ) {     // If tab  was not selected or selected default,  then  redirect  it to the "form" tab.            
//        $_GET['tab'] = 'form';
//    }
//} else {
//    add_action('opsd_menu_created', array( new OPSD_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
//}

add_action('opsd_menu_created', array( new OPSD_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
 