<?php /**
 * @version 1.0
 * @package Secure Downloads 
 * @category Toolbar. Data for UI Elements at Secure Downloads admin pages
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


////////////////////////////////////////////////////////////////////////////////
//   T o o l b a r s
////////////////////////////////////////////////////////////////////////////////

/** T o o l b a r   C o n t a i n e r   f o r   item Listing */
function opsd_items_toolbar() {

    opsd_clear_div();
	
	?><div style="height:20px;"></div><?php        
	
    opsd_toolbar_search_by_id_items();                                       // Search items by  ID - form  at the top  right side of the page
            
    opsd_toolbar_btn__view_mode();                                              //  Vertical Buttons 
        
    //  Toolbar ////////////////////////////////////////////////////////////////
    
    ?><div id="toolbar_opsd_listing" style="margin-left: 50px;position:relative;"><?php

        opsd_bs_toolbar_tabs_html_container_start();

            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >

            if ( ! isset( $_REQUEST['tab'] ) )  
				$_REQUEST['tab'] = 'filter';           
				
            $selected_tab = $_REQUEST['tab'];

			if ( ! in_array( $selected_tab, array( 'filter', 'actions' ) ) ) {
				$selected_tab = 'filter';
			}
			
            opsd_bs_display_tab(   array(
                                                'title'         => '&nbsp;' . __('Filters', 'secure-downloads')             
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#filter_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'glyphicon glyphicon-random' 
                                                , 'default'     => ( $selected_tab == 'filter' ) ? true : false
                                ) );
            opsd_bs_display_tab(   array(
                                                'title'         => __('Actions', 'secure-downloads')
                                                // , 'hint' => array( 'title' => __('Manage items' , 'secure-downloads') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#actions_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                
                                                , 'font_icon'   => 'glyphicon glyphicon-fire'
                                                , 'default'     => ( $selected_tab == 'actions' ) ? true : false

                                ) ); 

            opsd_bs_dropdown_menu_help();
        
            // </editor-fold>
            
        opsd_bs_toolbar_tabs_html_container_end();

        ////////////////////////////////////////////////////////////////////////
        
        opsd_bs_toolbar_sub_html_container_start();
        
        //  F i l t e r   T o o l b a r 
        
        ?><div id="filter_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'filter' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php 

            ?><form  name="opsd_filters_form" action="" method="post" id="opsd_filters_form"  class="form-inline">
                <input type="hidden" name="page_num" id ="page_num" value="1" /><?php  

                opsd_toolbar_btn__apply_reset();                                         

                opsd_toolbar_filter__approve_pending();                                  

                opsd_toolbar_filter__opsd_dates();                                     

                opsd_toolbar_filter__sort();                                             

                opsd_toolbar_filter__trash();                                           
                
                ?><span class="advanced_opsd_filter" style="display:none;"><?php 

                opsd_toolbar_filter__new_items();                                     

                opsd_toolbar_filter__creation_date();                                  

                if ( function_exists( 'opsd_filter_payment_status' ) )  opsd_filter_payment_status();

                if ( function_exists( 'opsd_filter_min_max_cost' ) )    opsd_filter_min_max_cost();

                if ( function_exists( 'opsd_filter_text_keyword' ) )    opsd_filter_text_keyword();

                ?></span><?php

                make_opsd_action( 'opsd_br_selection_for_listing' );

                if ( function_exists( 'opsd_filter_template_save_delete' ) ) opsd_filter_template_save_delete();                

            ?></form><?php
                
            opsd_clear_div();
            
            opsd_toolbar_expand_collapse_btn( 'advanced_opsd_filter' );   
        
        ?></div><?php
        
        
         
        
        ?><div id="actions_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'actions' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php 
            
            $user = wp_get_current_user(); 
            $user_opsd_id = $user->ID;

            opsd_toolbar_btn__approve_reject( $user_opsd_id );                            //   A p p r o v e   |   R e j e c t

            opsd_toolbar_btn__delete_reason( $user_opsd_id );                             //   D e l e t e


            if ( function_exists('opsd_toolbar_action_print_button') ) opsd_toolbar_action_print_button();

            make_opsd_action('opsd_extend_buttons_in_action_toolbar_opsd_listing' );

            if ( function_exists('opsd_toolbar_action_export_print_buttons') ) opsd_toolbar_action_export_print_buttons();
            
            opsd_clear_div();
            
        ?></div><?php    
        
        opsd_bs_toolbar_sub_html_container_end();
        
        opsd_toolbar_is_send_emails_btn();
        
    ?></div><?php
    
    opsd_clear_div();          
}



/** T o o l b a r   C o n t a i n e r   f o r   Add New item */
function opsd_add_new_opsd_toolbar() {

    opsd_clear_div();
                
    //  Toolbar ////////////////////////////////////////////////////////////////
    
    ?><div id="toolbar_opsd_listing" style="position:relative;"><?php

        opsd_bs_toolbar_tabs_html_container_start();
        
            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >
        
            if ( ! isset( $_REQUEST['toolbar'] ) )  $_REQUEST['toolbar'] = 'filter';           
            $selected_tab = $_REQUEST['toolbar'];

            opsd_bs_display_tab(   array(
                                                'title'         => __('Options', 'secure-downloads')
                                                // , 'hint' => array( 'title' => __('Manage items' , 'secure-downloads') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#filter_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'glyphicon glyphicon-fire'
                                                , 'default'     => ( $selected_tab == 'filter' ) ? true : false

                                ) ); 


            opsd_bs_dropdown_menu_help();

            // </editor-fold>
        
        opsd_bs_toolbar_tabs_html_container_end();

        ////////////////////////////////////////////////////////////////////////
        
        opsd_bs_toolbar_sub_html_container_start();
        
        //  T o o l b a r
        ?><div id="filter_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'filter' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php 
        
            if (    (  function_exists( 'opsd_toolbar_btn__resource_selection' ) ) 
                 && ( ! isset( $_GET['opsd_hash'] ) )  )                     //Do not show resource seleciton  if editing item.
                opsd_toolbar_btn__resource_selection();
            
            if (  function_exists( 'opsd_toolbar_btn__form_selection' ) )
                opsd_toolbar_btn__form_selection();
            
            ////////////////////////////////////////////////////////////////////
            ?><div class="clear-for-mobile"></div><?php 
            
            ?><div class="control-group opsd-no-padding" style="float:right;margin-right: 0;margin-left: 15px;"><?php 
            
                if ( function_exists( 'opsd_toolbar_btn__auto_fill' ) )
                    opsd_toolbar_btn__auto_fill();
                                
            
            ?></div><?php
            ////////////////////////////////////////////////////////////////////

            
            ?><span class="advanced_opsd_filter" style="display:none;"><div class="clear" style="width:100%;border-bottom:1px solid #ccc;height:10px;"></div><?php 
                
                // Get possible saved previous "Custom User Calednar data"
                $user_calendar_options = get_user_option( 'opsd_custom_' . 'add_opsd_calendar_options', get_opsd_current_user_id() );

                if ( $user_calendar_options === false ) {                       // Default, if no saved previously.
                    $user_calendar_options = array();       
                    $user_calendar_options['calendar_months_count'] = 1;
                    $user_calendar_options['calendar_months_num_in_1_row'] = 0 ;
                    $user_calendar_options['calendar_width'] = '';
                    $user_calendar_options['calendar_widthunits'] = 'px';      
                    $user_calendar_options['calendar_cell_height'] = '';      
                    $user_calendar_options['calendar_cell_heightunits'] = 'px';      
                } else {
                    $user_calendar_options = maybe_unserialize( $user_calendar_options );
                }
          
                opsd_toolbar_btn__calendar_months_number_selection( $user_calendar_options );
            
                opsd_toolbar_btn__calendar_months_num_in_1_row_selection( $user_calendar_options );

                opsd_toolbar_btn__calendar_width( $user_calendar_options );
                
                opsd_toolbar_btn__calendar_cell_height( $user_calendar_options );
                
                opsd_toolbar_btn__calendar_options_save();
                
            ?><div class="clear"></div></span><?php
            
            
            opsd_clear_div();
            
            opsd_toolbar_expand_collapse_btn( 'advanced_opsd_filter' );   
                             
        ?></div><?php

        
        opsd_bs_toolbar_sub_html_container_end();
        
        opsd_toolbar_is_send_emails_btn();
        
    ?></div><?php
    
    opsd_clear_div();
          
}


////////////////////////////////////////////////////////////////////////////////  
//   HTML elements for Toolbar
////////////////////////////////////////////////////////////////////////////////

/** Expand or Collapse Advanced Filter set
 * 
 * @param string $css_class_of_expand_element - CSS Class of element section  to  show or hide
 */
function opsd_toolbar_expand_collapse_btn( $css_class_of_expand_element ) {
    
      ?><span id="show_link_advanced_opsd_filter" class="tab-bottom tooltip_right" 
            title="<?php _e('Expand Advanced Toolbar' , 'secure-downloads'); ?>"  
            ><a href="javascript:void(0)" 
                onclick="javascript:jQuery('.<?php echo $css_class_of_expand_element; ?>').show();
                                    jQuery('#show_link_advanced_opsd_filter').hide();
                                    jQuery('#hide_link_advanced_opsd_filter').show();"><i 
                    class="glyphicon glyphicon-chevron-down"></i></a></span>
        <span id="hide_link_advanced_opsd_filter" class="tab-bottom tooltip_right" style="display:none;"                       
            title="<?php _e('Collapse Advanced Toolbar' , 'secure-downloads'); ?>" 
            ><a href="javascript:void(0)"  
                onclick="javascript:jQuery('.<?php echo $css_class_of_expand_element; ?>').hide(); 
                                    jQuery('#hide_link_advanced_opsd_filter').hide(); 
                                    jQuery('#show_link_advanced_opsd_filter').show();"><i 
                    class="glyphicon glyphicon-chevron-up"></i></a></span><?php 
     
}


/** Checkbox - sending emails or not */
function opsd_toolbar_is_send_emails_btn() {
    ?>
    <div class="btn-group" style="position:absolute;right:0px;margin-top:10px;">
        <fieldset>
            <label for="is_send_email_for_pending" style="display: inline-block;"    >
                <input style="margin:0 4px 2px;"
                    type="checkbox" checked="CHECKED" id="is_send_email_for_pending" name="is_send_email_for_pending" class="tooltip_top"  
                    title="<?php echo esc_js( __( 'Send email notification to customer after approval, cancellation or deletion of items', 'secure-downloads') ); ?>"
                /><?php _e( 'Emails sending', 'secure-downloads') ?>
            </label>
        </fieldset>
    </div>
    <?php
}


/** Search form  by item ID (at top right side of page)  */
function opsd_toolbar_search_by_id_items() {
    
    $bk_admin_url = opsd_get_params_in_url( opsd_get_master_url(), array( 'view_mode', 'wh_opsd_id', 'page_num' ) );
    
    ?> 
    <div style=" position: absolute; right: 20px; top: 10px;">
        <form name="opsd_filters_formID" action="<?php echo $bk_admin_url . '&view_mode=vm_listing' ; ?>" method="post" id="opsd_filters_formID" >
        <?php 
            
            if (isset($_REQUEST['wh_opsd_id']))  $wh_opsd_id = opsd_clean_digit_or_csd( $_REQUEST['wh_opsd_id'] );                  //  {'1', '2', .... }
            else                                    $wh_opsd_id = '';                    
                
                  
            $params = array(  'label_for' => 'wh_opsd_id'  
                                      , 'label' => ''//__('Keyword:', 'secure-downloads')
                                      , 'items' => array(
                                 array( 'type' => 'text', 'id' => 'wh_opsd_id', 'value' => $wh_opsd_id, 'placeholder' => __('Item ID', 'secure-downloads') ) 
                                , array( 
                                    'type' => 'button'
                                    , 'title' => __('Go', 'secure-downloads')
                                    , 'class' => 'button-secondary' 
                                    , 'font_icon' => 'glyphicon glyphicon-search'
                                    , 'icon_position' => 'right'
                                    , 'action' => "jQuery('#opsd_filters_formID').submit();" ) 
                                       )
                                );         
            ?><div class="control-group opsd-no-padding" ><?php
                      opsd_bs_input_group( $params );                   
            ?></div><?php
        ?>
        </form>
        <?php opsd_clear_div(); ?>
    </div>
    <?php 
}


////////////////////////////////////////////////////////////////////////////////
//   U I    E l e m e n t s
////////////////////////////////////////////////////////////////////////////////

/** Help   -   Drop Down Menu  -  T a b  */
function opsd_bs_dropdown_menu_help() {
    
    opsd_bs_dropdown_menu( array( 
                                        'title' => __( 'Help', 'secure-downloads') 
                                      , 'font_icon' => 'glyphicon glyphicon-question-sign'
                                      , 'position' => 'right'
                                      , 'items' => array( 
                                               array( 'type' => 'link', 'title' => __('About Secure Downloads', 'secure-downloads'), 'url' => esc_url( admin_url( add_query_arg( array( 'page' => 'opsd-about' ), 'index.php' ) ) ) )
                                             , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => __('Help', 'secure-downloads'), 'url' => 'http://oplugins.com/plugins/secure-downloads/#help' )
                                             , array( 'type' => 'link', 'title' => __('FAQ', 'secure-downloads'), 'url' => 'http://oplugins.com/plugins/secure-downloads/#faq' )
                                             , array( 'type' => 'link', 'title' => __('Technical Support', 'secure-downloads'), 'url' => 'mailto:support-opsd@oplugins.com' )
                                             , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => __('Upgrade Now', 'secure-downloads'), 'url' => opsd_up_link()
                                                                        , 'attr' => array(
                                                                              'target' => '_blank'
                                                                            , 'style' => 'font-weight: 600;font-size: 1em;'
                                                                        ) 
                                                    )
                                        )
                        ) );     
}


/** View Mode   -   B u t t o n */
function opsd_toolbar_btn__view_mode() {
    
	if ( ! empty( $_REQUEST['view_mode'] ) )
		$selected_view_mode = $_REQUEST['view_mode'];
	else
		$selected_view_mode = 'vm_listing';		// vm_calendar | vm_listing

    $bk_admin_url = opsd_get_params_in_url( opsd_get_master_url( false ), array('view_mode', 'wh_opsd_id', 'page_num' ) );

    $params = array();
    $params['btn_vm_listing'] = array(
                                  'title' => ''
                                , 'hint' => array( 'title' => __('Item Listing' , 'secure-downloads') , 'position' => 'top' )
                                , 'selected' => ( $selected_view_mode == 'vm_listing' ) ? true : false
                                , 'link' => $bk_admin_url . '&view_mode=vm_listing'
                                , 'icon' => ''
                                , 'font_icon' => 'glyphicon glyphicon-align-justify'            
                            );
    
    
    $bk_admin_url = opsd_get_params_in_url( opsd_get_master_url( false ) , array()              // Exclude Value of this parameter
                                            , array( 'page', 'tab', 'tab_cvm', 'wh_opsd_type', 'scroll_start_date', 'scroll_month', 'view_days_num'  
                                                     , 'wh_trash'               //FixIn: 6.1.1.10                                                
                                                ) // Only  this parameters
                                           );    
    $params['btn_vm_calendar'] = array(
                                  'title' => ''
                                , 'hint' => array( 'title' => __('Calendar Overview' , 'secure-downloads') , 'position' => 'bottom' )
                                , 'selected' => ( $selected_view_mode == 'vm_calendar' ) ? true : false
                                , 'link' => $bk_admin_url . '&view_mode=vm_calendar'
                                , 'icon' => ''
                                , 'font_icon' => 'glyphicon glyphicon-calendar'
                            );

    ?><div style="position:absolute;"><?php
    
        opsd_bs_vertical_buttons_group( $params );
    
    ?></div><?php
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar   Filter    B u t t o n s 
////////////////////////////////////////////////////////////////////////////////

/** Apply | Reset   -   B u t t o n s */
function opsd_toolbar_btn__apply_reset(){

    $params = array(  
                      'label_for' => 'opsd_refresh'                                 // "For" parameter  of button group element
                    , 'label' => ''//&nbsp;'//__('Refresh listing', 'secure-downloads')      // Label above the button group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                        array(                                                 
                                              'type' => 'button' 
                                            , 'title' => __('Apply', 'secure-downloads')     // Title of the button
                                            , 'hint' => array( 'title' => __('Refresh item listing' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => 'opsd_filters_form.submit();'                // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-primary'           // button-secondary  | button-primary
                                            , 'style' => ''                         // Any CSS class here
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-refresh'  // glyphicon-white
                                            , 'icon_position' => 'right'            // Position  of icon relative to Text: left | right
                                            , 'attr' => array()
                                            , 'mobile_show_text' => true            // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        )
                                        , array( 
                                            'type' => 'button'
                                            , 'title' => ''
                                            , 'hint' => array( 'title' => __('Reset filter to default values' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                            , 'link' => opsd_get_master_url() . '&view_mode=vm_listing'
                                            , 'action' => ''  
                                            , 'class' => '' 
                                            , 'style' => ''  //
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-remove'
                                            , 'icon_position' => 'left'
                                            , 'attr' => array()
                                            , 'mobile_show_text' => false        // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        )
                                    )
    );             
    opsd_bs_button_group( $params );      
}


/** Approved | Pending   -   F i l t e r */
function opsd_toolbar_filter__approve_pending(){

    $params = array(                                                    
                    'id' => 'wh_approved'
                    , 'options' => array (
                                            __('Pending', 'secure-downloads') => '0',
                                            __('Approved', 'secure-downloads') => '1',
                                            'divider0' => 'divider',
                                            __('Any', 'secure-downloads') => ''
                                         )
                    , 'default' => ( isset( $_REQUEST[ 'wh_approved' ] ) ) ? esc_attr( $_REQUEST[ 'wh_approved' ] ) : ''
                    , 'label' => ''//__('Status', 'secure-downloads') . ':'
                    , 'title' => __('Items', 'secure-downloads')     
                );            

    opsd_bs_dropdown_list( $params );                                     
}


/** Dates   -   F i l t e r */
function opsd_toolbar_filter__opsd_dates(){

    $dates_interval = array(  
                                1 => '1' . ' ' . __('day' , 'secure-downloads')
                              , 2 => '2' . ' ' . __('days' , 'secure-downloads')
                              , 3 => '3' . ' ' . __('days' , 'secure-downloads')
                              , 4 => '4' . ' ' . __('days' , 'secure-downloads')
                              , 5 => '5' . ' ' . __('days' , 'secure-downloads')
                              , 6 => '6' . ' ' . __('days' , 'secure-downloads')
                              , 7 => '1' . ' ' . __('week' , 'secure-downloads')
                              , 14 => '2' . ' ' . __('weeks' , 'secure-downloads')
                              , 30 => '1' . ' ' . __('month' , 'secure-downloads')
                              , 60 => '2' . ' ' . __('months' , 'secure-downloads')
                              , 90 => '3' . ' ' . __('months' , 'secure-downloads')
                              , 183 => '6' . ' ' . __('months' , 'secure-downloads')
                              , 365 => '1' . ' ' . __('Year' , 'secure-downloads')
                        );
    $params = array(                                                    
                      'id'  => 'wh_opsd_date'
                    , 'id2' => 'wh_opsd_date2'
                    , 'default' =>  ( isset( $_REQUEST[ 'wh_opsd_date' ] ) ) ? esc_attr( $_REQUEST[ 'wh_opsd_date' ] ) : ''
                    , 'default2' => ( isset( $_REQUEST[ 'wh_opsd_date2' ] ) ) ? esc_attr( $_REQUEST[ 'wh_opsd_date2' ] ) : ''
                    , 'hint' => array( 'title' => __('Filter items by item dates' , 'secure-downloads') , 'position' => 'top' )
                    , 'label' => ''//__('Dates', 'secure-downloads') . ':'
                    , 'title' => __('Dates', 'secure-downloads')                                                              
                    , 'options' => array (
                                              __('Current dates' , 'secure-downloads')    => '0'
                                            , __('Today' , 'secure-downloads')            => '1'
                                            , __('Previous dates' , 'secure-downloads')   => '2'
                                            , __('All dates' , 'secure-downloads')        => '3'
                                            , 'divider1' => 'divider'
                                            , __('Today check in/out' , 'secure-downloads')   => '9'
                                            , __('Check In - Tomorrow' , 'secure-downloads')  => '7'
                                            , __('Check Out - Tomorrow' , 'secure-downloads') => '8'
                                            , 'divider2' => 'divider'                                
                                            , 'next' => array(  
                                                                array(
                                                                        'type' => 'radio'                                                        
                                                                      , 'label' => __('Next' , 'secure-downloads')
                                                                      , 'id' => 'wh_opsd_datedays_interval1' 
                                                                      , 'name' => 'wh_opsd_datedays_interval_Radios'                                                                                            
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element                                                                                
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                                                      , 'legend' => ''                    // aria-label parameter 
                                                                      , 'value' => '4'                     // Some Value from optins array that selected by default                                      
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_opsd_datedays_interval_Radios'] ) 
                                                                                        && ( $_REQUEST[ 'wh_opsd_datedays_interval_Radios'] == '4' ) ) ? true : false
                                                                      )
                                                                , array( 
                                                                        'type' => 'select'
                                                                      , 'attr' => array() 
                                                                      , 'name' => 'wh_opsd_datenext'
                                                                      , 'id' => 'wh_opsd_datenext'                                                                                                                                                                                                                                                                                
                                                                      , 'options' => $dates_interval
                                                                      , 'value' => isset( $_REQUEST[ 'wh_opsd_datenext'] ) ? esc_attr( $_REQUEST[ 'wh_opsd_datenext'] ) : ''
                                                                      )                                                        
                                                             ) 
                                            , 'prior' => array( 
                                                                array( 
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Prior' , 'secure-downloads')
                                                                      , 'id' => 'wh_opsd_datedays_interval2' 
                                                                      , 'name' => 'wh_opsd_datedays_interval_Radios'                                                                                            
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element                                                                                
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                                                      , 'legend' => ''                    // aria-label parameter 
                                                                      , 'value' => '5'                     // Some Value from optins array that selected by default                                      
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_opsd_datedays_interval_Radios'] ) 
                                                                                        && ( $_REQUEST[ 'wh_opsd_datedays_interval_Radios'] == '5' ) ) ? true : false
                                                                      )
                                                                , array( 
                                                                        'type' => 'select'
                                                                      , 'attr' => array() 
                                                                      , 'name' => 'wh_opsd_dateprior'
                                                                      , 'id' => 'wh_opsd_dateprior'                                                                                                                                                                                                                                                                                
                                                                      , 'options' => $dates_interval
                                                                      , 'value' => isset( $_REQUEST[ 'wh_opsd_dateprior'] ) ? esc_attr( $_REQUEST[ 'wh_opsd_dateprior'] ) : ''
                                                                      )                                                        
                                                             )                                        
                                            , 'fixed' => array( array(  'type' => 'group', 'class' => 'input-group text-group'), 
                                                                array( 
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Dates' , 'secure-downloads')
                                                                      , 'id' => 'wh_opsd_datedays_interval3' 
                                                                      , 'name' => 'wh_opsd_datedays_interval_Radios'                                                                                            
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element                                                                                
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                                                      , 'legend' => ''                    // aria-label parameter 
                                                                      , 'value' => '6'                     // Some Value from optins array that selected by default                                      
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_opsd_datedays_interval_Radios'] ) 
                                                                                        && ( $_REQUEST[ 'wh_opsd_datedays_interval_Radios'] == '6' ) ) ? true : false
                                                                      )
                                                                , array(                                                                                 
                                                                        'type'          => 'text' 
                                                                        , 'id'          => 'wh_opsd_datefixeddates'  
                                                                        , 'name'        => 'wh_opsd_datefixeddates'  
                                                                        , 'label'       => __('Check-in' , 'secure-downloads') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'opsd-filters-section-calendar'           // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )                                                                                                                                   
                                                                        , 'attr'        => array()    
                                                                        , 'value' => isset( $_REQUEST[ 'wh_opsd_datefixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_opsd_datefixeddates'] ) : ''
                                                                      )                                                        
                                                                , array(                                                                                 
                                                                        'type'          => 'text' 
                                                                        , 'id'          => 'wh_opsd_date2fixeddates'  
                                                                        , 'name'        => 'wh_opsd_date2fixeddates'  
                                                                        , 'label'       => __('Check-out' , 'secure-downloads') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'opsd-filters-section-calendar'                  // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )                                                                                                                                   
                                                                        , 'attr'        => array()       
                                                                        , 'value' => isset( $_REQUEST[ 'wh_opsd_date2fixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_opsd_date2fixeddates'] ) : ''
                                                                      )                                                        
                                                             )                                                     
                                            , 'divider3' => 'divider'  
                                            , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ), 
                                                                array( 
                                                                          'type' => 'button' 
                                                                        , 'title' => __('Apply' , 'secure-downloads') // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' , 'secure-downloads') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        , 'action' => "opsd_show_selected_in_dropdown__radio_select_option("
                                                                                            . "  'wh_opsd_date'"
                                                                                            . ", 'wh_opsd_date2'"
                                                                                            . ", 'wh_opsd_datedays_interval_Radios' "
                                                                                        . ");"
                                                                        , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()

                                                                      )
                                                                , array( 
                                                                          'type' => 'button' 
                                                                        , 'title' => __('Close' , 'secure-downloads')                     // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' , 'secure-downloads') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                        , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()                                                                            
                                                                      )
                                                                )
                                        )
                );            

    opsd_bs_dropdown_list( $params );    

}


/** Sort   -   F i l t e r */
function opsd_toolbar_filter__sort(){

        $selectors = array(
                            __('ID' , 'secure-downloads') . '&nbsp;<i class="glyphicon glyphicon-arrow-up "></i>' => '',
                            __('Dates' , 'secure-downloads') . '&nbsp;<i class="glyphicon glyphicon-arrow-up "></i>' => 'sort_date',
                            'divider0'=>'divider',
                            __('ID' , 'secure-downloads') . '&nbsp;<i class="glyphicon glyphicon-arrow-down "></i>' => 'opsd_id_asc',
                            __('Dates' , 'secure-downloads') . '&nbsp;<i class="glyphicon glyphicon-arrow-down "></i>' => 'sort_date_asc'
                        );

        $selectors = apply_opsd_filter('bk_filter_sort_options', $selectors);
        $default_value = get_opsd_option( 'opsd_sort_order');

        $params = array(                                                        // Pending, Active, Suspended, Terminated, Cancelled, Fraud 
                        'id' => 'or_sort'
                        , 'options' => $selectors
                        , 'default' => ( isset( $_REQUEST[ 'or_sort' ] ) ) ? esc_attr( $_REQUEST[ 'or_sort' ] ) : $default_value
                        , 'label' => ''//__('Status', 'secure-downloads') . ':'
                        , 'title' => __('Order by', 'secure-downloads')                                              
                    );            

        opsd_bs_dropdown_list( $params );                         
}


/** Trash   -   F i l t e r */
function opsd_toolbar_filter__trash(){                                          //FixIn: 6.1.1.10


    $params = array(                                                    
                    'id' => 'wh_trash'
                    , 'options' => array (
                                            __('Exist', 'secure-downloads') => '0',
                                            __('In Trash', 'secure-downloads') => 'trash',
                                            'divider0' => 'divider',
                                            __('Any', 'secure-downloads') => 'any'
                                         )
                    , 'default' => ( isset( $_REQUEST[ 'wh_trash' ] ) ) ? esc_attr( $_REQUEST[ 'wh_trash' ] ) : ''
                    , 'label' => ''//__('Status', 'secure-downloads') . ':'
                    , 'title' => __('Items', 'secure-downloads')     
                );            

    opsd_bs_dropdown_list( $params );                              
                    
}


/** New items   -   F i l t e r */
function opsd_toolbar_filter__new_items() {
    
    $params = array(                                                    
                    'id' => 'wh_is_new'
                    , 'options' => array (
                                            __('All items', 'secure-downloads') => '',
                                            __('New items', 'secure-downloads') => '1'
                                         )
                    , 'default' => ( isset( $_REQUEST[ 'wh_is_new' ] ) ) ? esc_attr( $_REQUEST[ 'wh_is_new' ] ) : ''
                    , 'label' => ''//__('Status', 'secure-downloads') . ':'
                    , 'title' => __('Show', 'secure-downloads')     
                );            

    opsd_bs_dropdown_list( $params );                             
}


/** Creation Date   -   F i l t e r */
function opsd_toolbar_filter__creation_date(){
    
    $dates_interval = array(  
                                1 => '1' . ' ' . __('day' , 'secure-downloads')
                              , 2 => '2' . ' ' . __('days' , 'secure-downloads')
                              , 3 => '3' . ' ' . __('days' , 'secure-downloads')
                              , 4 => '4' . ' ' . __('days' , 'secure-downloads')
                              , 5 => '5' . ' ' . __('days' , 'secure-downloads')
                              , 6 => '6' . ' ' . __('days' , 'secure-downloads')
                              , 7 => '1' . ' ' . __('week' , 'secure-downloads')
                              , 14 => '2' . ' ' . __('weeks' , 'secure-downloads')
                              , 30 => '1' . ' ' . __('month' , 'secure-downloads')
                              , 60 => '2' . ' ' . __('months' , 'secure-downloads')
                              , 90 => '3' . ' ' . __('months' , 'secure-downloads')
                              , 183 => '6' . ' ' . __('months' , 'secure-downloads')
                              , 365 => '1' . ' ' . __('Year' , 'secure-downloads')
                        );
    
    $params = array(                                                    
                      'id'  => 'wh_modification_date'
                    , 'id2' => 'wh_modification_date2'
                    , 'default' =>  ( isset( $_REQUEST[ 'wh_modification_date' ] ) )  ? esc_attr( $_REQUEST[ 'wh_modification_date' ] ) : '3'
                    , 'default2' => ( isset( $_REQUEST[ 'wh_modification_date2' ] ) ) ? esc_attr( $_REQUEST[ 'wh_modification_date2' ] ) : ''
                    , 'hint' => array( 'title' => __('Filter items by item dates' , 'secure-downloads') , 'position' => 'top' )
                    , 'label' => ''//__('Item Creation Date', 'secure-downloads') . ':'
                    , 'title' => __('Creation', 'secure-downloads')                                                              
                    , 'options' => array (
                                              __('Today' , 'secure-downloads')            => '1'
                                            , __('All dates' , 'secure-downloads')        => '3'
                                            , 'divider1' => 'divider'
                                            , 'prior' => array( 
                                                                array( 
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Prior' , 'secure-downloads')
                                                                      , 'id' => 'wh_modification_datedays_interval2' 
                                                                      , 'name' => 'wh_modification_datedays_interval_Radios'                                                                                            
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element                                                                                
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                                                      , 'legend' => ''                    // aria-label parameter 
                                                                      , 'value' => '5'                     // Some Value from optins array that selected by default                                      
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_modification_datedays_interval_Radios'] ) 
                                                                                        && ( $_REQUEST[ 'wh_modification_datedays_interval_Radios'] == '5' ) ) ? true : false
                                                                      )
                                                                , array( 
                                                                        'type' => 'select'
                                                                      , 'attr' => array() 
                                                                      , 'name' => 'wh_modification_dateprior'
                                                                      , 'id' => 'wh_modification_dateprior'                                                                                                                                                                                                                                                                                
                                                                      , 'options' => $dates_interval
                                                                      , 'value' => isset( $_REQUEST[ 'wh_modification_dateprior'] ) ? esc_attr( $_REQUEST[ 'wh_modification_dateprior'] ) : ''
                                                                      )                                                        
                                                             )                                        
                                            , 'fixed' => array( array(  'type' => 'group', 'class' => 'input-group text-group'), 
                                                                array( 
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Dates' , 'secure-downloads')
                                                                      , 'id' => 'wh_modification_datedays_interval3' 
                                                                      , 'name' => 'wh_modification_datedays_interval_Radios'                                                                                            
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element                                                                                
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                                                      , 'legend' => ''                    // aria-label parameter 
                                                                      , 'value' => '6'                     // Some Value from optins array that selected by default                                      
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_modification_datedays_interval_Radios'] ) 
                                                                                        && ( $_REQUEST[ 'wh_modification_datedays_interval_Radios'] == '6' ) ) ? true : false
                                                                      )
                                                                , array(                                                                                 
                                                                        'type'          => 'text' 
                                                                        , 'id'          => 'wh_modification_datefixeddates'  
                                                                        , 'name'        => 'wh_modification_datefixeddates'  
                                                                        , 'label'       => __('Check-in' , 'secure-downloads') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'opsd-filters-section-calendar'           // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )                                                                                                                                   
                                                                        , 'attr'        => array()    
                                                                        , 'value' => isset( $_REQUEST[ 'wh_modification_datefixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_modification_datefixeddates'] ) : ''
                                                                      )                                                        
                                                                , array(                                                                                 
                                                                        'type'          => 'text' 
                                                                        , 'id'          => 'wh_modification_date2fixeddates'  
                                                                        , 'name'        => 'wh_modification_date2fixeddates'  
                                                                        , 'label'       => __('Check-out' , 'secure-downloads') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'opsd-filters-section-calendar'                  // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )                                                                                                                                   
                                                                        , 'attr'        => array()       
                                                                        , 'value' => isset( $_REQUEST[ 'wh_modification_date2fixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_modification_date2fixeddates'] ) : ''
                                                                      )                                                        
                                                             )                                                     
                                            , 'divider3' => 'divider'  
                                            , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ), 
                                                                array( 
                                                                          'type' => 'button' 
                                                                        , 'title' => __('Apply' , 'secure-downloads') // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' , 'secure-downloads') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        , 'action' => "opsd_show_selected_in_dropdown__radio_select_option("
                                                                                            . "  'wh_modification_date'"
                                                                                            . ", 'wh_modification_date2'"
                                                                                            . ", 'wh_modification_datedays_interval_Radios' "
                                                                                        . ");"
                                                                        , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()

                                                                      )
                                                                , array( 
                                                                          'type' => 'button' 
                                                                        , 'title' => __('Close' , 'secure-downloads')                     // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' , 'secure-downloads') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                        , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()                                                                            
                                                                      )
                                                                )
                                        )
                );            

    opsd_bs_dropdown_list( $params );                         
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar   Actions    B u t t o n s 
////////////////////////////////////////////////////////////////////////////////

/** Approve | Reject   -   B u t t o n s */
function opsd_toolbar_btn__approve_reject( $user_opsd_id ) {
    
    $params = array(  
                      'label_for' => 'actions'                              // "For" parameter  of button group element
                    , 'label' => '' //__('Actions:', 'secure-downloads')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(                                                 
                                              'type' => 'button' 
                                            , 'title' => __('Approve', 'secure-downloads') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Approve selected items' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "console.log( get_selected_items_id_in_opsd_listing(), 1, " . 
                                                            $user_opsd_id . ", '" . opsd_get_locale() . "' , 1);"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-primary'                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-ok-circle glyphicon-white'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(                                                 
                                              'type' => 'button' 
                                            , 'title' => __('Reject', 'secure-downloads') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Set selected items as pending' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "if ( opsd_are_you_sure('" . esc_js(__('Do you really want to set item as pending ?' , 'secure-downloads')) . "') )
                                                            console.log( get_selected_items_id_in_opsd_listing() ,
                                                                    0, " . $user_opsd_id . ", '" . opsd_get_locale() . "' , 1);"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-ban-circle'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                    )
    );             
    opsd_bs_button_group( $params );
}


/** Delete | Reason   -   B u t t o n s */
function opsd_toolbar_btn__delete_reason( $user_opsd_id ) {
    
    $params = array(  
                  'label_for' => 'denyreason'                               // "For" parameter  of label element
                , 'label' => ''                                             // Label above the input group
                , 'style' => ''                                             // CSS Style of entire div element
                , 'items' => array(
                                    array(                                      //FixIn: 6.1.1.10                                                
                                          'type' => 'button' 
                                        , 'title' => __('Trash', 'secure-downloads') . '&nbsp;&nbsp;'    // Title of the button
                                        , 'hint' => array( 'title' => __('Move selected items to trash' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                        , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                        , 'action' => "if ( opsd_are_you_sure('" . esc_js( __('Do you really want to do this ?' , 'secure-downloads') ) . "') )
                                                         console.log( 1, get_selected_items_id_in_opsd_listing() , " 
                                                        . $user_opsd_id . ", '" 
                                                        . opsd_get_locale() . "' , 1  );"                // Some JavaScript to execure, for example run  the function
                                        , 'class' => ''                 // button-secondary  | button-primary
                                        , 'icon' => ''
                                        , 'font_icon' => 'glyphicon glyphicon-trash'
                                        , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                        , 'style' => ''                 // Any CSS class here
                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'attr' => array()
                                    )
                                    , array(                                                 
                                          'type' => 'button' 
                                        , 'title' => __('Restore', 'secure-downloads') . '&nbsp;&nbsp;'    // Title of the button
                                        , 'hint' => array( 'title' => __('Restore selected items' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                        , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                        , 'action' => "if ( opsd_are_you_sure('" . esc_js( __('Do you really want to do this ?' , 'secure-downloads') ) . "') )
                                                         console.log( 0, get_selected_items_id_in_opsd_listing() , " 
                                                        . $user_opsd_id . ", '" 
                                                        . opsd_get_locale() . "' , 1  );"                // Some JavaScript to execure, for example run  the function
                                        , 'class' => ''                 // button-secondary  | button-primary
                                        , 'icon' => ''
                                        , 'font_icon' => 'glyphicon glyphicon-repeat'
                                        , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                        , 'style' => ''                 // Any CSS class here
                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'attr' => array()
                                    )
                                    , array(                                                 
                                          'type' => 'button' 
                                        , 'title' => __('Delete', 'secure-downloads') . '&nbsp;&nbsp;'    // Title of the button
                                        , 'hint' => array( 'title' => __('Delete selected items' , 'secure-downloads') , 'position' => 'top' ) // Hint
                                        , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                        , 'action' => "if ( opsd_are_you_sure('" . esc_js( __('Do you really want to delete selected item(s) ?' , 'secure-downloads') ) . "') )
                                                        console.log( get_selected_items_id_in_opsd_listing() , " 
                                                        . $user_opsd_id . ", '" 
                                                        . opsd_get_locale() . "' , 1  );"                // Some JavaScript to execure, for example run  the function
                                        , 'class' => ''                 // button-secondary  | button-primary
                                        , 'icon' => ''
                                        , 'font_icon' => 'glyphicon glyphicon-remove'
                                        , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                        , 'style' => ''                 // Any CSS class here
                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'attr' => array()
                                    )
                                    , array(    
                                        'type' => 'text'
                                        , 'id' => 'denyreason'            // HTML ID  of element                                                          
                                        , 'value' => ''                 // Value of Text field
                                        , 'placeholder' => __('Reason of cancellation', 'secure-downloads')
                                        , 'style' => ''                 // CSS of select element
                                        , 'class' => ''                 // CSS Class of select element
                                        , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element 
                                    ) 
                )
          );     
    ?><div class="control-group opsd-no-padding opsd-sm-100" ><?php 
            opsd_bs_input_group( $params );                   
    ?></div><?php    
}




////////////////////////////////////////////////////////////////////////////////
// Toolbar   Options    B u t t o n s   -   Add New item   //////////////////
////////////////////////////////////////////////////////////////////////////////

/** Genereate URL based on GET parameters */
function opsd_get_new_opsd_url__base( $skip_parameters = array() ) {
        
    $link_base = opsd_get_new_opsd_url();
    
    $link_params = array();
    if ( ( isset( $_GET['opsd_type'] ) ) && ( $_GET['opsd_type'] > 0 ) )      $link_params['opsd_type'] = $_GET['opsd_type'];    
    if ( isset( $_GET['opsd_hash'] ) )                   $link_params['opsd_hash'] = $_GET['opsd_hash'];    
    if ( isset( $_GET['parent_res'] ) )                     $link_params['parent_res'] = $_GET['parent_res'];    
    if ( isset( $_GET['opsd_form'] ) )                   $link_params['opsd_form'] = $_GET['opsd_form'];
    if ( isset( $_GET['calendar_months_count'] ) )          $link_params['calendar_months_count'] = intval( $_GET['calendar_months_count'] );
    if ( isset( $_GET['calendar_months_num_in_1_row'] ) )   $link_params['calendar_months_num_in_1_row'] = intval( $_GET['calendar_months_num_in_1_row'] );

    
    foreach ( $link_params as $key => $value ) {

        if ( ! in_array( $key, $skip_parameters) ) {
            $link_base .= '&' . $key . '=' . $value;
        }
    }
    
    return $link_base;
}

/** Selection Number of visible months */
function opsd_toolbar_btn__calendar_months_number_selection( $user_calendar_options = array() ) {
            
    $text_label = __('Visible months' , 'secure-downloads') .':' ;

    $form_options = array(  1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12 );

    $parameter_name = 'calendar_months_count';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval ( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = 1;

    $link_base = opsd_get_new_opsd_url__base( array( $parameter_name ) ) . '&' . $parameter_name . '=' ;        

    $on_change = '';    //'location.href=\'' . $link_base . '\' + this.value;';


    $params = array(  
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(      
                                        'type' => 'addon' 
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )  
                                    , array(    
                                          'type' => 'select'  
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'options' => $form_options                // Associated array  of titles and values 
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default                                      
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                        , 'onchange' => $on_change                
                                    )
                    )
              );     
    ?><div class="control-group opsd-no-padding" style="width:auto;"><?php 
            opsd_bs_input_group( $params );                   
    ?></div><?php    
}


/** Selection Number of calendar months in one row */
function opsd_toolbar_btn__calendar_months_num_in_1_row_selection( $user_calendar_options = array() ) {

    $text_label = __('Number of months in one row' , 'secure-downloads') . ':';
    $form_options = array( 0 => __('All', 'secure-downloads'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12 );
                       
    $parameter_name = 'calendar_months_num_in_1_row';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval ( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = 0;
    
    $link_base = opsd_get_new_opsd_url__base( array( $parameter_name ) ) . '&' . $parameter_name . '=' ;        

    $on_change = ''; // 'location.href=\'' . $link_base . '\' + this.value;';


    $params = array(  
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(      
                                        'type' => 'addon' 
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )  
                                    , array(    
                                          'type' => 'select'  
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'options' => $form_options                // Associated array  of titles and values 
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default                                      
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                        , 'onchange' => $on_change                
                                    )
                    )
              );     
    ?><div class="control-group opsd-no-padding"><?php 
            opsd_bs_input_group( $params );                   
    ?></div><?php    
}


function opsd_toolbar_btn__calendar_width( $user_calendar_options = array() ){
    
    $text_label     = __('Calendar width' , 'secure-downloads') . ':';
    $parameter_name = 'calendar_width';
    
    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = '';
            
    if ( isset( $user_calendar_options[$parameter_name . 'units'] ) )    $selected_value_units = esc_attr( $user_calendar_options[ $parameter_name . 'units' ]  );
    else                                                                 $selected_value_units = '';
            
    $params = array(  
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(      
                                        'type' => 'addon' 
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )  
                                    , array(    
                                          'type' => 'text'  
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default                                      
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'placeholder' => '100%'
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                    )
                                    , array(    
                                          'type' => 'select'  
                                        , 'id' =>      $parameter_name . 'units'                // HTML ID  of element
                                        , 'options' => array( 'px' => 'px', 'percent' => '%' )  // Associated array  of titles and values 
                                        , 'value' =>   $selected_value_units              // Some Value from optins array that selected by default                                      
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                    )                        
                    )
              );     
    ?><div class="control-group opsd-no-padding"><?php 
            opsd_bs_input_group( $params );                   
    ?></div><?php        
}
                
function opsd_toolbar_btn__calendar_cell_height( $user_calendar_options = array() ){
    
    $text_label     = __('Calendar cell height' , 'secure-downloads') . ':';
    $parameter_name = 'calendar_cell_height';
    
    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = '';

    if ( isset( $user_calendar_options[$parameter_name . 'units'] ) )    $selected_value_units = esc_attr( $user_calendar_options[ $parameter_name . 'units' ]  );
    else                                                                 $selected_value_units = '';
    
    $params = array(  
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(      
                                        'type' => 'addon' 
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )  
                                    , array(    
                                          'type' => 'text'  
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default                                      
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'placeholder' => '39px'
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                    )
                                    , array(    
                                          'type' => 'select'  
                                        , 'id' =>      $parameter_name . 'units'                // HTML ID  of element
                                        , 'options' => array( 'px' => 'px', 'percent' => '%' )  // Associated array  of titles and values 
                                        , 'value' =>   $selected_value_units              // Some Value from optins array that selected by default                                      
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                    )                        
                        
                    )
              );     
    ?><div class="control-group opsd-no-padding"><?php 
            opsd_bs_input_group( $params );                   
    ?></div><?php        
}


/** Add New item   Button*/
function opsd_toolbar_btn__calendar_options_save() {
    
    ?><div class="control-group opsd-no-padding"><?php 
    ?><a                 
             class="button button-primary " 
             href="javascript:void(0)"
             onclick="javascript:var data_params = {};
			data_params.calendar_months_count = jQuery('#calendar_months_count').val();
			data_params.calendar_months_num_in_1_row = jQuery('#calendar_months_num_in_1_row').val();
			data_params.calendar_width = jQuery('#calendar_width').val();
			data_params.calendar_widthunits = jQuery('#calendar_widthunits').val();
			data_params.calendar_cell_height = jQuery('#calendar_cell_height').val();
			data_params.calendar_cell_heightunits = jQuery('#calendar_cell_heightunits').val();
			var ajax_data_params = jQuery.param( data_params );
                        opsd_save_custom_user_data(<?php echo get_opsd_current_user_id(); ?>
                                                , '<?php echo 'add_opsd_calendar_options'; ?>'
                                                , ajax_data_params
                                                , 1        
                                                );" 
             ><?php _e('Save Changes' , 'secure-downloads') ?></a><?php           
    ?></div><?php           
}




/** Checkbox - sending emails or not - duplicated button, usually at the bottom of page*/
function opsd_toolbar_is_send_emails_btn_duplicated() {
    
    ?>
    <div class="btn-group" style="color:#888;">
        <fieldset>
            <label for="is_send_email_for_new_item" style="display: inline-block;"  >
                <input  onchange="javascript:document.getElementById('is_send_email_for_pending').checked = this.checked;"
                        type="checkbox" 
                        checked="CHECKED" 
                        id="is_send_email_for_new_item" 
                        name="is_send_email_for_new_item" 
                        class="tooltip_top"  
                        style="margin:0 4px 2px;"
                        title="<?php echo esc_js( __( 'Send email notification to customer about this operation', 'secure-downloads') ); ?>"
                /><?php _e( 'Send email notification to customer about this operation', 'secure-downloads') ?>
            </label>
        </fieldset>
    </div>
    <script type="text/javascript">
        jQuery( '#is_send_email_for_pending' ).change(function() {                
          if ( jQuery('#is_send_email_for_pending').attr('checked') !== undefined ) {
              document.getElementById('is_send_email_for_new_item').checked = true;
          } else {
              document.getElementById('is_send_email_for_new_item').checked = false;
          }              
        });            
    </script>
    <?php    
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar   Other UI elements - General
////////////////////////////////////////////////////////////////////////////////
    
/** Selection elements in toolbar UI selectbox
 * 
 * @param array $params
 * 
 * Exmaple:
            opsd_toolbar_btn__selection_element( array(
                                                            'name' => 'resources_count'
                                                          , 'title' => __('Resources count' , 'secure-downloads') . ':'
                                                          , 'selected' => 1  
                                                          , 'options' => array_combine( range(1, 201) ,range(1, 201) ) 
                                            ) ) ;   

 */    
function opsd_toolbar_btn__selection_element( $params ) {
    
    $defaults = array( 
                          'name'        => 'random_' . rand( 1000, 10000 )
                        , 'title'       => __('Total', 'secure-downloads') . ':'
                        , 'on_change'   => ''                                    //'location.href=\'' . $link_base . '\' + this.value;';    //$link_base = opsd_get_new_opsd_url__base( array( $params['name'] ) ) . '&' . $params['name'] . '=' ;        
                        , 'options'     => array()
                        , 'selected'    => 0
                    );
    $params = wp_parse_args( $params, $defaults );
    
    
                        

    for ( $i = 1; $i < 201; $i++ ) {
        $form_options[ $i ] = $i;
    }

    $params = array(  
                      'label_for' => $params['name']                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(      
                                        'type' => 'addon' 
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text'  => $params['title']
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'             // CSS Style of entire div element
                                    )  
                                    , array(    
                                          'type' => 'select'  
                                        , 'id'   =>      $params['name']              // HTML ID  of element
                                        , 'name' =>      $params['name']              // HTML ID  of element
                                        , 'options' => $params['options']           // Associated array  of titles and values 
                                        , 'value' =>   $params['selected']          // Some Value from optins array that selected by default                                      
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element 
                                        , 'onchange' => $params['on_change']
                                    )
                    )
              );     
    ?><div class="control-group opsd-no-padding"><?php 
            opsd_bs_input_group( $params );                   
    ?></div><?php    
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar     S e a r c h    F o r m     at    Top  Right side of Settings page
////////////////////////////////////////////////////////////////////////////////

/** Real Search item data  by ID | Title (at top right side of page)  
 * 
 * @param array $params - array of parameters
 * Exmaple:
                opsd_toolbar_search_by_id__top_form( array( 
                                                            'search_form_id' => 'opsd_seasonfilters_search_form'
                                                          , 'search_get_key' => 'wh_search_id'
                                                          , 'is_pseudo'      => false
                                                    ) );

 */
function opsd_toolbar_search_by_id__top_form( $params ) {
    
    $defaults = array( 
                          'search_form_id'  => 'opsd_seasonfilters_search_form'
                        , 'search_get_key'  => 'wh_search_id'
                        , 'is_pseudo'       => false                                    //'location.href=\'' . $link_base . '\' + this.value;';    //$link_base = opsd_get_new_opsd_url__base( array( $params['name'] ) ) . '&' . $params['name'] . '=' ;        
                    );
    $params = wp_parse_args( $params, $defaults );
        
    
    $exclude_params         = array();                                          //array('page_num', 'orderby', 'order');  - if using "only_these_parameters",  then this parameter does NOT require
    $only_these_parameters  = array( 'page', 'tab', $params[ 'search_get_key' ] );
    $opsd_admin_url = opsd_get_params_in_url( opsd_get_master_url( false ), $exclude_params, $only_these_parameters );
    
    
    $search_form_value = '';    
    if ( isset( $_REQUEST[ $params[ 'search_get_key' ] ] ) ) {
        $wh_resource_id    = opsd_clean_digit_or_csd( $_REQUEST[ $params[ 'search_get_key' ] ] );          // '12,0,45,9' or '10' 
        $wh_resource_title = opsd_clean_string_for_form( $_REQUEST[ $params[ 'search_get_key' ] ] );       // Clean string
        if ( ! empty( $wh_resource_id ) ) {
            $search_form_value = $wh_resource_id;
        } else {
            $search_form_value = $wh_resource_title;
        }                 
    } 
    
    
    opsd_clear_div();
    
    ?>
    <span class="wpdevelop">
           
    <?php if ( ! $params['is_pseudo'] ) { ?>
        <div style="position: absolute; right: 20px; top: 10px;">
            <form action="<?php echo $opsd_admin_url; ?>" method="post" id="<?php echo $params[ 'search_form_id' ]; ?>"  name="<?php echo $params[ 'search_form_id' ]; ?>"  >
            <?php 
    } else {
      ?><div style="float:right;" id="<?php echo $params['search_form_id'] . '_pseudo'; ?>"><?php
    }
                
                $params_for_element = array(  'label_for' => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
                                          , 'label' => ''//__('Keyword:', 'secure-downloads')
                                          , 'items' => array(
                                                                array(   'type' => 'text'
                                                                       , 'id' => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
                                                                       , 'value' => $search_form_value
                                                                       , 'placeholder' => __('ID or Title', 'secure-downloads') 
                                                                    ) 
                                                                , array( 
                                                                    'type' => 'button'
                                                                    , 'title' => __('Go', 'secure-downloads')
                                                                    , 'class' => 'button-secondary' 
                                                                    , 'font_icon' => 'glyphicon glyphicon-search'
                                                                    , 'icon_position' => 'right'
                                                                    , 'action' => ( ( ! $params['is_pseudo'] ) ? "jQuery('#". $params[ 'search_form_id' ] ."').submit();" 
                                                                                                             : "jQuery('#" . $params[ 'search_get_key' ] . "').val( jQuery('#" . $params[ 'search_get_key' ] . "_pseudo').val() ); jQuery('#". $params[ 'search_form_id' ] ."').submit();" )           //Submit real form  at the top of page.
                                                                    ) 
                                                        )
                                    );       
                
                ?><div class="control-group opsd-no-padding" ><?php
                          opsd_bs_input_group( $params_for_element );                   
                ?></div><?php
                
            if ( ! $params['is_pseudo'] ) { ?>
            </form>
            <?php } ?>
            <?php opsd_clear_div(); ?>
            
            <?php 
                if ( $params['is_pseudo'] ) { 
                    // Required for opening specific page NUM during saving ////////
                    ?><input type="hidden" value="<?php echo $search_form_value; ?>" name="<?php echo $params[ 'search_get_key' ]; ?>" /><?php 
                    ?><div class="clear" style="height:20px;"></div><?php
                }
            ?>
        </div>
    </span>
    <?php 
    
    if ( $params['is_pseudo'] ) { 
        
        // Hide pseudo form, if real  search  form does not exist 
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                if ( jQuery('#<?php echo $params[ 'search_form_id' ]; ?>').length == 0 ) {
                    jQuery('#<?php echo $params['search_form_id'] . '_pseudo'; ?>').hide();
                }            
            });
        </script>
        <?php 
    }
}


////////////////////////////////////////////////////////////////////////////////    
//  M o d a l s
////////////////////////////////////////////////////////////////////////////////

/** Start Loyouts - Modal Window structure */    
function opsd_write_content_for_modals_start_here() {
    
    ?><span id="opsd_content_for_modals"></span><?php
}
add_opsd_action( 'opsd_write_content_for_modals', 'opsd_write_content_for_modals_start_here');    


////////////////////////////////////////////////////////////////////////////////
// JS & CSS
////////////////////////////////////////////////////////////////////////////////

/** Load suport JavaScript for "Items" page*/
function opsd_js_for_items_page() {
    
    $is_use_hints = get_opsd_option( 'opsd_is_use_hints_at_admin_panel'  );
    if ( $is_use_hints == 'On' )
      opsd_bs_javascript_tooltips();                                            // JS Tooltips

    opsd_bs_javascript_popover();                                               // JS Popover        
    
    //opsd_datepicker_js();                                                       // JS  Datepicker
    opsd_datepicker_css();                                                      // CSS DatePicker
}


/** Datepicker activation JavaScript */
function opsd_datepicker_js() {
    
    ?><script type="text/javascript">
        jQuery(document).ready( function(){

            function applyCSStoDays( date ){
                return [true, 'date_available']; 
            }
            jQuery('input.opsd-filters-section-calendar').datepick(
                {   beforeShowDay: applyCSStoDays,
                    showOn: 'focus',
                    multiSelect: 0,
                    numberOfMonths: 1,
                    stepMonths: 1,
                    prevText: '&laquo;',
                    nextText: '&raquo;',
                    dateFormat: 'yy-mm-dd',
                    changeMonth: false,
                    changeYear: false,
                    minDate: null, 
                    maxDate: null, //'1Y',
                    showStatus: false,
                    multiSeparator: ', ',
                    closeAtTop: false,
                    // firstDay:<?php //echo get_opsd_option( 'opsd_start_day_weeek' ); ?>,
                    gotoCurrent: false,
                    hideIfNoPrevNext:true,
                    useThemeRoller :false,
                    mandatory: true
                }
            );
        });
        </script><?php 
}


/** Support CSS - datepick,  etc... */
function opsd_datepicker_css(){
    ?>
    <style type="text/css">
        #datepick-div .datepick-header {
               width: 172px !important;
        }
        #datepick-div {
            -border-radius: 3px;
            -box-shadow: 0 0 2px #888888;
            -webkit-border-radius: 3px;
            -webkit-box-shadow: 0 0 2px #888888;
            -moz-border-radius: 3px;
            -moz-box-shadow: 0 0 2px #888888;
            width: 172px !important;
        }
        #datepick-div .datepick .datepick-days-cell a{
            font-size: 12px;
        }
        #datepick-div table.datepick tr td {
            border-top: 0 none !important;
            line-height: 24px;
            padding: 0 !important;
            width: 24px;
        }
        #datepick-div .datepick-control {
            font-size: 10px;
            text-align: center;
        }
        #datepick-div .datepick-one-month {
            height: auto;
        }
    </style>
    <?php
}            


/** Sortable Table JavaScript */
function opsd_sortable_js() {
    ?>
    <script type="text/javascript">        
        // Activate Sortable Functionality    
        jQuery( document ).ready(function(){

            jQuery('.opsd_input_table tbody th').css('cursor','move');

            jQuery('.opsd_input_table tbody td.sort').css('cursor','move');

            jQuery('.opsd_input_table.sortable tbody').sortable({
                    items:'tr',
                    cursor:'move',
                    axis:'y',
                    scrollSensitivity:40,
                    forcePlaceholderSize: true,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: '.opsd_sortable_table .sort',
                    start:function(event,ui){
                            ui.item.css('background-color','#f6f6f6');
                    },
                    stop:function(event,ui){
                            ui.item.removeAttr('style');
                    }
            });
        });
    </script>
    <?php
    
}