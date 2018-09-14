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



class OPSD_Page_SettingsFields extends OPSD_Page_Structure {
            
    public function in_page() {
        return 'opsd-settings';
    }
    
    public function tabs() {
        
        $tabs = array();
        $tabs[ 'sub-toolbar' ] = array(
                              'title' =>  'Sub Toolbar Elements'                    // Title of TAB    
                            , 'hint' =>  'Customizaton of Form Fields'             // Hint    
                            , 'page_title' =>  'Form fields'       // Title of Page    
                            //, 'link' => ''                                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            //, 'position' => ''                                // 'left'  ||  'right'  ||  ''
                            //, 'css_classes' => ''                             // CSS class(es)
                            //, 'icon' => ''                                    // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-edit'         // CSS definition  of forn Icon
                            , 'default' => false                                // Is this tab activated by default or not: true || false. 
                            , 'subtabs' => array()
            
        );
//        $tabs[ 'upgrade' ] = array(
//                              'title' =>  'Upgrade'                 // Title of TAB    
//                            , 'hint' =>  'Upgrade to higher version'               // Hint    
//                            //, 'page_title' => 'Upgrade'         // Title of Page    
//                            , 'link' => 'http://server.com/'                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
//                            , 'position' => 'right'                             // 'left'  ||  'right'  ||  ''
//                            //, 'css_classes' => ''                             // CSS class(es)
//                            //, 'icon' => ''                                    // Icon - link to the real PNG img
//                            , 'font_icon' => 'glyphicon glyphicon-shopping-cart'// CSS definition  of forn Icon
//                            //, 'default' => false                              // Is this tab activated by default or not: true || false. 
//                            //, 'subtabs' => array()
//            
//        );
        
        $subtabs = array();
        
        $subtabs['form-html-section-1'] = array( 'type' => 'html' , 'html' => '<div style="float:left;">' );
        
        $subtabs['sub-toolbar'] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link'
                            , 'title' =>  'Form'                    // Title of TAB    
                            , 'page_title' =>  'Form Settings'                             // Title of Page    
                            , 'hint' =>  'Customization of Form Settings'                 // Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'https://www.paypalobjects.com/webstatic/icon/pp258.png'      // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-credit-card'                        // CSS definition of Font Icon
                            , 'default' =>  true                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  =>  false                             // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $subtabs['form-separator'] = array( 
                            'type' => 'separator'                               // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link'
                        );        
        $subtabs['form-goto'] = array( 
                            'type' => 'goto-link'                               // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link'
                            , 'title' =>ucwords(  'Form fields'  ) // Title of TAB    
                            , 'hint' => ''                                      // Hint    
                            , 'show_section' => 'id_of_show_section'            // ID of HTML element, for scroll to.
                        );
        
        $subtabs['form-html-section-2'] = array( 'type' => 'html' , 'html' => '</div>' );
        
                ob_start();
                $params = array(  
                          'label_for' => 'deny_reason1'                          // "For" parameter  of label element
                        //, 'label' =>  'Reason:'                    // Label above the input group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                            array(      
                                                'type' => 'addon' 
                                                , 'element' => 'radio'           // text | radio | checkbox
                                                , 'text' =>  'Reason of cancelation'                   // Simple plain text showing
                                                , 'id' => 'radios'                    // ID, if this radio | checkbox element
                                                , 'name' => 'radioss'                  // Name, if this radio | checkbox element
                                                , 'value' => ''                 // value, if this radio | checkbox element
                                                , 'selected' => false           // Selected, if this radio | checkbox element     
                                                , 'legend' => ''                // aria-label parameter , if this radio | checkbox element
                                                , 'class' => ''                 // Any CSS class here
                                                , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element 
                                            )  
                                            , array(    
                                                'type' => 'text'
                                                , 'id' => 'deny_reason'         // HTML ID  of element                                                          
                                                , 'value' => ''                 // Value of Text field
                                                , 'placeholder' =>  'Reason of Cancelation' 
                                                , 'style' => ''                 // CSS of select element
                                                , 'class' => ''                 // CSS Class of select element
                                                , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element 
                                                
                                            ) 
                                            , array( 
                                                'type' => 'button'
                                                , 'title' =>  'Delete' 
                                                , 'class' => 'button-secondary' 
                                                , 'font_icon' => 'glyphicon glyphicon-trash'
                                                , 'icon_position' => 'right'
                                                , 'action' => "jQuery('#opsd_filters_formID').submit();" ) 
                                            )
                  );     
        ?><span style="float:left;margin:0 5px;"><div class="control-group opsd-no-padding"><?php // col-sm-2 col-xs-12 " ><?php
                  opsd_bs_input_group( $params );                   
        ?></div></span><?php
        
        $html_element_data = ob_get_clean();
        
        $subtabs['form-selection'] = array( 
                            'type' => 'html'                                    // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link'
                            , 'html' => $html_element_data
                        );
        /**/
        $subtabs['form-save'] = array( 
                            'type' => 'button'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link'
                            , 'title' =>  'Save Changes'            // Title of TAB    
                            , 'form' => 'opsd_form'                             // Required for 'button'!  Name of Form  to submit    
                        );
        
        $tabs[ 'sub-toolbar' ][ 'subtabs' ] = $subtabs;
        
        return $tabs;
    }


    public function content() {
debuge( 'Content <strong>' . basename(__FILE__ ) . '</strong> <span style="font-size:9px;">' . __FILE__  . '</span>'); 



    opsd_bs_toolbar_sub_html_container_start();
        $params = array(  
                          'label_for' => 'deny_reason1'                          // "For" parameter  of label element
                        , 'label' =>  'Reason:'                    // Label above the input group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                            array(      
                                                'type' => 'addon' 
                                                , 'element' => 'radio'           // text | radio | checkbox
                                                , 'text' =>  'Reason of cancelation'                  // Simple plain text showing
                                                , 'id' => 'radios'                    // ID, if this radio | checkbox element
                                                , 'name' => 'radioss'                  // Name, if this radio | checkbox element
                                                , 'value' => ''                 // value, if this radio | checkbox element
                                                , 'selected' => false           // Selected, if this radio | checkbox element     
                                                , 'legend' => ''                // aria-label parameter , if this radio | checkbox element
                                                , 'class' => ''                 // Any CSS class here
                                                , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element 
                                            )  
                                            , array(    
                                                'type' => 'text'
                                                , 'id' => 'deny_reason'         // HTML ID  of element                                                          
                                                , 'value' => ''                 // Value of Text field
                                                , 'placeholder' =>  'Reason of Cancelation' 
                                                , 'style' => ''                 // CSS of select element
                                                , 'class' => ''                 // CSS Class of select element
                                                , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element 
                                                
                                            ) 
                                            , array( 
                                                'type' => 'button'
                                                , 'title' =>  'Delete' 
                                                , 'class' => 'button-secondary' 
                                                , 'font_icon' => 'glyphicon glyphicon-trash'
                                                , 'icon_position' => 'right'
                                                , 'action' => "jQuery('#opsd_filters_formID').submit();" ) 
                                            )
                  );     
        ?><span class="wpdevelop"><div class="control-group opsd-no-padding"><?php // col-sm-2 col-xs-12 " ><?php
                  opsd_bs_input_group( $params );                   
        ?></div></span><?php
    opsd_bs_toolbar_sub_html_container_end();

    }


    public function update() {
        
    }
}

add_action('opsd_menu_created', array( new OPSD_Page_SettingsFields() , '__construct') );    // Executed after creation of Menu