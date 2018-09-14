<?php /**
 * @version 1.0
 * @package Secure Downloads 
 * @category Content of item Listing page
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class OPSD_Page_Main extends OPSD_Page_Structure {
        
    private $listing_table;
    
    public function in_page() {
        return 'opsd-files';
    }

    public function tabs() {
        
        $tabs = array();
        $tabs[ 'vm_toolbar' ] = array(
                              'title' => __('Item Listing', 'secure-downloads')            // Title of TAB    
                            , 'hint' => __('Item Listing', 'secure-downloads')                      // Hint    
                            , 'page_title' => __('Item Listing', 'secure-downloads')                                // Title of Page    
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-tasks'             // CSS definition  of forn Icon
                            , 'default' => ! true                                 // Is this tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this tab disbaled: true || false. 
                            , 'hided'   => !true                                 // Is this tab hided: true || false. 
                            , 'subtabs' => array()
            
        );
        
        // $subtabs = array();                
        // $tabs[ 'items' ][ 'subtabs' ] = $subtabs;
        
        return $tabs;        
    }


    public function content() {                
        
        opsd_check_request_paramters();                                         //Cleanup REQUEST parameters        //FixIn:6.2.1.4
        
        do_action( 'opsd_hook_opsd_page_header', array( 'page' => $this->in_page() ) );                // Define Notices Section and show some static messages, if needed.
                
        ?><span class="wpdevelop"><?php                                         // BS UI CSS Class
        
        make_opsd_action( 'opsd_write_content_for_modals' );                      // Content for modal windows
        
        opsd_js_for_items_page();                                            // JavaScript functions

        make_opsd_action( 'opsd_check_request_param__wh_opsd_type' );          // Setting $_REQUEST['wh_opsd_type'] - remove empty and duplicates ID of item resources in this list
        
        make_opsd_action( 'check_for_resources_of_notsuperadmin_in_opsd_listing' );    // If "Regular User",  then filter resources in $_REQUEST['wh_opsd_type'] to show only resources of this user

        
        //   T o o l b a r s   /////////////////////////////////////////////////
        opsd_items_toolbar();                                                

     
        ?><div class="clear" style="height:40px;"></div><?php

        $args = array( 'wh_opsd_type' => (isset( $_REQUEST[ 'wh_opsd_type' ] )) ? opsd_clean_parameter( $_REQUEST[ 'wh_opsd_type' ] ) : '' );
        echo '<textarea id="bk_request_params" style="display:none;">', serialize( $args ), '</textarea>';
        
        $items           = array(
									array(), array()
								);
        $opsd_types      = array();
        $items_count     = 100;
        $page_num        = 1;
        $page_items_count= 10;
        
        $this->listing_table = new OPSD_OPSD_Listing_Table( $items, $opsd_types );
        $this->listing_table->show();
        

        opsd_show_pagination($items_count, $page_num, $page_items_count);   // Show Pagination  

        opsd_show_opsd_footer();           
        
        ?></span><!-- wpdevelop class --><?php 
        
 

    }

}
add_action('opsd_menu_created', array( new OPSD_Page_Main() , '__construct') );    // Executed after creation of Menu



/** Trick here to  overload default REQUST parameters before page is loading */
function opsd_define_listing_page_parameters( $page_tag ) {
    
    // $page_tag - here can be all defined in plugin menu pages
    // So  we need to  check activated page. By default its inside of $_GET['page'], 
    
    // Execute it only  for item Listing  admin pages.
    //if (  ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'opsd' )  ) {                

    if ( opsd_is_master_page() ) {                                            // We are inside of this page. Menu item selected. 
        // Get saved filters set, (if its not set in request yet), like "tab"  & "view_mode" and overload $_REQUEST    
		//$_REQUEST['view_mode'] = $opsd_default_view_mode;                        // Set to REQUEST
    }
}
// We are set  9  to  execute early  than hook in OPSD_Admin_Menus
//add_action('opsd_define_nav_tabs', 'opsd_define_listing_page_parameters', 1  );             // This Hook fire in the class OPSD_Admin_Menus for showing page content of specific menu                