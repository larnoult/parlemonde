<?php
/**
 * @version 1.0
 * @package Content
 * @category Menu
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-04-09
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

/** Replace:
 1.  LinkUser  -> LinkUser
 2.  LINK_USER -> LINK_USER
 3.  link_user -> link_user
 4.  Check in api-emails.php 'db_prefix_option' => '...' option,  have to be the same as OPSD_EMAIL_LINK_USER_PREFIX here
 5.  Configure Fields in init_settings_fields.
 */
                                                                                            
if ( ! defined( 'OPSD_EMAIL_LINK_USER_PREFIX' ) )   define( 'OPSD_EMAIL_LINK_USER_PREFIX',  'opsd_email_' ); // Its defined in api-emails.php file & its same for all emails, here its used only for easy coding...

if ( ! defined( 'OPSD_EMAIL_LINK_USER_ID' ) )       define( 'OPSD_EMAIL_LINK_USER_ID',      'link_user' );      /* Define Name of Email Template.   
                                                                                                                   Note. Prefix "opsd_email_" defined in api-emails.php file. 
                                                                                                                   Full name of option is - "opsd_email_link_user"
                                                                                                                   Other email templates names:
                                                                                                                                            - 'link_user'       - send email with download link to user
                                                                                                                                            - 'link_admin'      - send copy of email to admin with download link
                                                                                                                                            - 'download_admin'  - send email  about downloads happend    
                                                                                                                */

require_once( OPSD_PLUGIN_DIR . '/core/any/api-emails.php' );           // API


/** Email   F i e l d s  */
class OPSD_Emails_API_LinkUser extends OPSD_Emails_API  {                       // O v e r r i d i n g     "OPSD_Emails_API"     ClASS
    
    /**  Overrided functions - define Email Fields & Values  */
    public function init_settings_fields() {
        
        $this->fields = array();

        $this->fields['enabled'] = array(   
                                      'type'        => 'checkbox'
                                    , 'default'     => 'On'            
                                    , 'title'       => __('Enable / Disable', 'secure-downloads')
                                    , 'label'       => __('Enable this email notification', 'secure-downloads')   
                                    , 'description' => ''
                                    , 'group'       => 'general'

                                );

        $this->fields['copy_to_admin'] = array(   
                                      'type'        => 'checkbox'
                                    , 'default'     => 'On'            
                                    , 'title'       => __('Copy to admin', 'secure-downloads')
                                    , 'label'       => __('Enable / disable sending copy of this email notification to admin', 'secure-downloads')
                                    , 'description' => ''
                                    , 'group'       => 'general'

                                );
        
        $this->fields['enabled_hr'] = array( 'type' => 'hr' );    
		
		$user_info = array( 'name' => '' );
		if ( is_user_logged_in() ) {			
			$user_data         = get_userdata( get_current_user_id() );
			$user_info['name'] = ( $user_data ) ? $user_data->display_name : '';
		}
		
/*
        $this->fields['to_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '<tr valign="top">
                                                        <th scope="row">
                                                            <label class="opsd-form-email" for="' 
                                                                             . esc_attr( 'link_user_to' ) 
                                                            . '">' . wp_kses_post(  __('To' , 'secure-downloads') ) 
                                                            . '</label>
                                                        </th>
                                                        <td><fieldset style="float:left;width:50%;margin-right:5%;">'
                                );        
        $this->fields['to'] = array(  
                                      'type'        => 'text'               // We are using here 'text'  and not 'email',  for ability to  save several comma seperated emails.
                                    , 'default'     => get_option( 'admin_email' )
                                    //, 'placeholder' => ''
                                    , 'title'       => '' 
                                    , 'description' => __('Email Address', 'secure-downloads') . '. ' . __('Required', 'secure-downloads') . '.'
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field'  => true
                                    , 'validate_as' => array( 'required' )
                                );            
        $this->fields['to_html_middle'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '</fieldset><fieldset style="float:left;width:45%;">'
                                );                
        $this->fields['to_name'] = array(  
                                      'type'        => 'text'
                                    , 'default'     => ''  // 		$user_info['name']
                                    //, 'placeholder' => ''
                                    , 'title'       => '' 
                                    , 'description' => __('Title', 'secure-downloads') . '  (' . __('optional', 'secure-downloads') . ').' //. ' ' . __('If empty then title defined as WordPress', 'secure-downloads') 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                );
        $this->fields['to_html_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'general'
                                , 'html'        => '    </fieldset>
                                                        </td>
                                                    </tr>'            
                        );        
*/


        $this->fields['from_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '<tr valign="top">
                                                        <th scope="row">
                                                            <label class="opsd-form-email" for="' 
                                                                             . esc_attr( 'link_user_from' ) 
                                                            . '">' . wp_kses_post(  __('From' , 'secure-downloads') ) 
                                                            . '</label>
                                                        </th>
                                                        <td><fieldset style="float:left;width:50%;margin-right:5%;">'
                                );        
        $this->fields['from'] = array(  
                                      'type'        => 'email'              // Its can  be only 1 email,  so check  it as Email  field.
                                    , 'default'     => get_option( 'admin_email' )
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Email Address', 'secure-downloads') . '. ' . __('Required', 'secure-downloads') . '.' 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                    , 'validate_as' => array( 'required' )
                                );            
        $this->fields['from_html_middle'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '</fieldset><fieldset style="float:left;width:45%;">'
                                );                
        $this->fields['from_name'] = array(  
                                      'type'        => 'text'
                                    , 'default'     => $user_info['name']
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Title', 'secure-downloads') . '  (' . __('optional', 'secure-downloads') . ').' //. ' ' . __('If empty then title defined as WordPress', 'secure-downloads') 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                );
        $this->fields['from_html_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'general'
                                , 'html'        => '    </fieldset>
                                                        </td>
                                                    </tr>'            
                        );                    

        $this->fields['from_hr'] = array( 'type' => 'hr' );            


        $this->fields['subject'] = array(   
                                      'type'        => 'text'
//                                    , 'default'     => sprintf( __( 'Update of %s', 'secure-downloads'), '[product_title]' )
									, 'default'     => sprintf( __( 'Delivery of %s', 'secure-downloads'), '[product_title] [product_version]' )
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Subject', 'secure-downloads')
                                    , 'description' => sprintf(__('Type your email %ssubject%s.' , 'secure-downloads'),'<b>','</b>') . ' ' . __('Required', 'secure-downloads') . '.'
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'validate_as' => array( 'required' )
                            );

        $blg_title = get_option( 'blogname' );
        $blg_title = str_replace( array( '"', "'" ), '', $blg_title );
        
        $this->fields['content'] = array(   
                                      'type'        => 'wp_textarea'
//                                    , 'default'     => sprintf( __( 'Hello.%sTo download %s click the link below:%s (%s) ~ Download link will expire in %sThank you, %s', 'secure-downloads')
//                                                                , '<br/><br/>', '[product_title]', '<br/>[product_link]', '[product_size]', '[product_expire_after]<br/><br/>', '[site_title]<br>[siteurl]' )
                                    , 'default'     => sprintf( __( 'Hello. %sThank you for requesting %s To download %s click the link below: %s Thank you, %s', 'secure-downloads' )
                                                                , '<br/>'
																, '[product_title] [product_version]<br/><br/>'
																, '<strong>[product_description]</strong>'
																, '<br/> --- <br/> [product_summary] - [product_expire_date] <br/> --- <br/> <br/> '
																, '[siteurl]<br/> [current_date] [current_time]' )
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Content', 'secure-downloads')
                                    , 'description' => __('Type your email message content.', 'secure-downloads') 
                                    , 'description_tag' => ''
                                    , 'css'         => ''
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'rows'        => 10
                                    , 'show_in_2_cols' => true
                            );
//        $this->fields['content'] = htmlspecialchars( $this->fields['content'] );// Convert > to &gt;
//        $this->fields['content'] = html_entity_decode( $this->fields['content'] );// Convert &gt; to >
        


        ////////////////////////////////////////////////////////////////////
        // Style
        ////////////////////////////////////////////////////////////////////


        $this->fields['header_content'] = array(   
                                    'type' => 'textarea'
                                    , 'default' => ''
                                    , 'title' => __('Email Heading', 'secure-downloads')
                                    , 'description'  => __('Enter main heading contained within the email notification.', 'secure-downloads') 
                                    //, 'placeholder' => ''
                                    , 'rows'  => 2
                                    , 'css' => "width:100%;"
                                    , 'group' => 'parts'                        
                            );
        $this->fields['footer_content'] = array(   
                                    'type' => 'textarea'
                                    , 'default' => ''
                                    , 'title' => __('Email Footer Text', 'secure-downloads')
                                    , 'description'  => __('Enter text contained within footer of the email notification', 'secure-downloads') 
                                    //, 'placeholder' => ''
                                    , 'rows'  => 2
                                    , 'css' => 'width:100%;'
                                    , 'group' => 'parts'                        
                            );

        $this->fields['template_file'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'plain'
                                    , 'title' => __('Email template', 'secure-downloads')
                                    , 'description' => __('Choose email template.', 'secure-downloads')  
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => array(
                                                            'plain'     => __('Plain (without styles)', 'secure-downloads')  
                                                          , 'standard'  => __('Standard 1 column', 'secure-downloads')                                                              
                                                    )      
                                    , 'group' => 'style'
                            );

        $this->fields['template_file_help'] = array(   
                                    'type' => 'help'                                        
                                    , 'value' => array( sprintf( __('You can override this email template in this folder %s', 'secure-downloads')                                                
                                                                , '<code>' . realpath( dirname(__FILE__) . '/../any/emails_tpl/' ) . '</code>' ) 
                                                      )
                                    , 'cols' => 2
                                    , 'group' => 'style'
                            );

        $this->fields['base_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#557da1'
                                    , 'title'   => __('Base Color', 'secure-downloads')
                                    , 'description'  => __('The base color for email templates.', 'secure-downloads') 
                                                        . ' ' . __('Default color', 'secure-downloads') .': <code>#557da1</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );                
        $this->fields['background_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#f5f5f5'
                                    , 'title'   => __('Background Color', 'secure-downloads')
                                    , 'description' => __('The background color for email templates.', 'secure-downloads') 
                                                       . ' ' . __('Default color', 'secure-downloads') .': <code>#f5f5f5</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );
        $this->fields['body_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#fdfdfd'
                                    , 'title'   => __('Email Body Background Color', 'secure-downloads')
                                    , 'description' =>  __('The main body background color for email templates.', 'secure-downloads') 
                                                        . ' ' . __('Default color', 'secure-downloads') .': <code>#fdfdfd</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );
        $this->fields['text_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#505050'
                                    , 'title'   => __('Email Body Text Colour', 'secure-downloads')
                                    , 'description' =>  __('The main body text color for email templates.', 'secure-downloads') 
                                                        . ' ' . __('Default color', 'secure-downloads') .': <code>#505050</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );


        ////////////////////////////////////////////////////////////////////
        // Email format: Plain, HTML, MultiPart
        ////////////////////////////////////////////////////////////////////


        $this->fields['email_content_type'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'plain'
                                    , 'title' => __('Email format', 'secure-downloads')
                                    , 'description' => __('Choose which format of email to send.', 'secure-downloads')  
                                    , 'description_tag' => 'p'
                                    , 'css' => 'width:100%;'
                                    , 'options' => array(
                                                            'plain' => __('Plain text', 'secure-downloads')  
                                                        //  , 'html' => __('HTML', 'secure-downloads')  
                                                        //  , 'multipart' => __('Multipart', 'secure-downloads')  
                                                    )      
                                    , 'group' => 'email_content_type'
                            );
        if ( class_exists( 'DOMDocument' ) ) {
            $this->fields['email_content_type']['options']['html']        = __('HTML', 'secure-downloads');
            $this->fields['email_content_type']['options']['multipart']   = __('Multipart', 'secure-downloads');

            $this->fields['email_content_type']['default'] = 'html';
        }



        ////////////////////////////////////////////////////////////////////
        // Help
        ////////////////////////////////////////////////////////////////////

        $this->fields['content_help'] = array(   
                                    'type' => 'help'                                        
                                    , 'value' => array()
                                    , 'cols' => 2
                                    , 'group' => 'help'
                            );

        $skip_shortcodes = array(
                                'denyreason'
                              , 'paymentreason'
                              , 'visitorediturl'
                              , 'visitorcancelurl'
                              , 'visitorpayurl'
                          );
        $email_example = sprintf(__('For example: "You have a new reservation %s on the following date(s): %s Contact information: %s You can approve or cancel this item at: %s Thank you, Reservation service."' , 'secure-downloads'),'','[dates]&lt;br/&gt;&lt;br/&gt;','&lt;br/&gt; [content]&lt;br/&gt;&lt;br/&gt;', htmlentities( ' <a href="[moderatelink]">'.__('here' , 'secure-downloads').'</a> ') . '&lt;br/&gt;&lt;br/&gt; ');

        $help_fields = opsd_get_email_help_shortcodes( $skip_shortcodes, $email_example );

        foreach ( $help_fields as $help_fields_key => $help_fields_value ) {
            $this->fields['content_help']['value'][] = $help_fields_value;
        }
            
    }    
        
}



/** Settings Emails   P a g e  */
class OPSD_Settings_Page_Email_LinkUser extends OPSD_Page_Structure {

		// Addon Fix
		public function __construct() {
			$is_show = true;
			$is_show = apply_filters( 'opsd_is_show_email_link_user_page', $is_show );
			if ( $is_show ) 
				parent::__construct();
		}
	
    public $email_settings_api = false;
    
    
    /** Define interface for  Email API
     * 
     * @param string $selected_email_name - name of Email template
     * @param array $init_fields_values - array of init form  fields data
     * @return object Email API
     */
    public function mail_api( $selected_email_name ='',  $init_fields_values = array() ){
        
        if ( $this->email_settings_api === false ) {
            $this->email_settings_api = new OPSD_Emails_API_LinkUser( $selected_email_name , $init_fields_values );    
        }
        
        return $this->email_settings_api;
    }
    
    
    public function in_page() {                                                 // P a g e    t a g
        return 'opsd-settings';
    }
    
    
    public function tabs() {                                                    // T a b s      A r r a y
        
        $tabs = array();
                
        $tabs[ 'email' ] = array(
                              'title'     => __( 'Emails', 'secure-downloads')               // Title of TAB    
                            , 'page_title'=> __( 'Emails Settings', 'secure-downloads')      // Title of Page    
                            , 'hint'      => __( 'Emails Settings', 'secure-downloads')      // Hint                
                            //, 'link'      => ''                                   // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            //, 'position'  => ''                                   // 'left'  ||  'right'  ||  ''
                            //, 'css_classes'=> ''                                  // CSS class(es)
                            //, 'icon'      => ''                                   // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-envelope'         // CSS definition  of forn Icon
                            //, 'default'   => false                                // Is this tab activated by default or not: true || false. 
                            //, 'disabled'  => false                                // Is this tab disbaled: true || false. 
                            //, 'hided'     => false                                // Is this tab hided: true || false. 
                            , 'subtabs'   => array()   
                    );

        $subtabs = array();
        

        $is_data_exist = get_opsd_option( OPSD_EMAIL_LINK_USER_PREFIX . OPSD_EMAIL_LINK_USER_ID );           // ''opsd_email_' - defined in api-emails.php  file.
        if (  ( ! empty( $is_data_exist ) ) && ( isset( $is_data_exist['enabled'] ) ) && ( $is_data_exist['enabled'] == 'On' )  )     
            $icon = '<i class="menu_icon icon-1x glyphicon glyphicon-check"></i> &nbsp; ';
        else 
            $icon = '<i class="menu_icon icon-1x glyphicon glyphicon-unchecked"></i> &nbsp; ';
		
        if (  ( ! empty( $is_data_exist ) ) && ( isset( $is_data_exist['copy_to_admin'] ) ) && ( $is_data_exist['copy_to_admin'] == 'On' )  )
            $sufix = '<sup> 2</sup>';
        else 
            $sufix = '';
        
        $subtabs['link-user'] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' =>  $icon . __('To User' , 'secure-downloads') . $sufix     // Title of TAB    
                            , 'page_title' => __('Emails Settings', 'secure-downloads')  // Title of Page   
                            , 'hint' => __('Email with download link, which is sending to user' , 'secure-downloads')   // Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  true                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  => false                              // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $tabs[ 'email' ]['subtabs'] = $subtabs;
                        
        return $tabs;
    }
    
    
    /** Show Content of Settings page */
    public function content() {
//debuge( 'OPSD_EMAIL_LINK_USER_PREFIX . OPSD_EMAIL_LINK_USER_ID, get_opsd_option( OPSD_EMAIL_LINK_USER_PREFIX . OPSD_EMAIL_LINK_USER_ID )', OPSD_EMAIL_LINK_USER_PREFIX . OPSD_EMAIL_LINK_USER_ID, get_opsd_option( OPSD_EMAIL_LINK_USER_PREFIX . OPSD_EMAIL_LINK_USER_ID ) );

        $this->css();
        
        ////////////////////////////////////////////////////////////////////////
        // Checking 
        ////////////////////////////////////////////////////////////////////////
        
        do_action( 'opsd_hook_settings_page_header', array( 'page' => $this->in_page(), 'subpage' => 'emails_settings' ) );	// Define Notices Section and show some static messages, if needed.
        

        
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////
        
        /**             Its will  load DATA from DB,  during creattion mail_api CLASS
         *              during initial activation  of the API  its try  to get option  from DB
         *              We need to define this API before checking POST, to know all available fields
         *              Define Email Name & define field values from DB, if not exist, then default values. 
            Array ( 
                    [opsd_email_link_user] => Array
                                                (
                                                    [enabled] => On
                                                    [to] => beta@oplugins.com
                                                    [to_name] => 'Some name'
                                                    [from] => admin@oplugins.com
                                                    [from_name] => 
                                                    [subject] => New item
                                                    [content] => You need to approve [shortcodetype] for: [dates]...
                                                    [header_content] => 
                                                    [footer_content] => 
                                                    [template_file] => plain
                                                    [base_color] => #557da1
                                                    [background_color] => #f5f5f5
                                                    [body_color] => #fdfdfd
                                                    [text_color] => #505050
                                                    [email_content_type] => html
                                                )
        )

        // $mail_api->save_to_db( $fields_values );
        */    
        $init_fields_values = array();

        $this->mail_api( OPSD_EMAIL_LINK_USER_ID, $init_fields_values );
        
        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Actions  -  S e n d   
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name_action = 'opsd_form_action';                                      // Define form name
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name_action ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'opsd_settings_page_' . $submit_form_name_action );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update_actions();
        }                        
        ?>
        <form  name="<?php echo $submit_form_name_action; ?>" id="<?php echo $submit_form_name_action; ?>" action="" method="post" autocomplete="off">
           <?php 
              // N o n c e   field, and key for checking   S u b m i t 
              wp_nonce_field( 'opsd_settings_page_' . $submit_form_name_action );
           ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name_action; ?>" id="is_form_sbmitted_<?php echo $submit_form_name_action; ?>" value="1" />
             <input type="hidden" name="form_action" id="form_action" value="" />
        </form>
        <?php

        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form  
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name = 'opsd_emails_template';                             // Define form name
        
        $this->mail_api()->validated_form_id = $submit_form_name;               // Define ID of Form for ability to  validate fields before submit.
        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'opsd_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        
        
        ////////////////////////////////////////////////////////////////////////
        // JavaScript: Tooltips, Popover, Datepick (js & css) 
        ////////////////////////////////////////////////////////////////////////
        
        echo '<span class="wpdevelop">';
        
        opsd_js_for_items_page();                                        
        
        echo '</span>';

        
        ////////////////////////////////////////////////////////////////////////
        // Content
        ////////////////////////////////////////////////////////////////////////
        ?>         
        <div class="clear" style="margin-bottom:10px;"></div>                        
                
        <span class="metabox-holder">
            
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" autocomplete="off">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'opsd_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />


                <div class="clear"></div>    
                <div class="metabox-holder">

                    <div class="opsd_settings_row opsd_settings_row_left" >
                    <?php 
                            
                        opsd_open_meta_box_section( $submit_form_name . 'general', __('Email with download link, which is sending to user', 'secure-downloads')   );
                            
                            $this->mail_api()->show( 'general' ); 
                            
                        opsd_close_meta_box_section(); 
                            
                        
                        opsd_open_meta_box_section( $submit_form_name . 'parts' , __('Header / Footer', 'secure-downloads') ); 
                            
                            $this->mail_api()->show( 'parts' );
                        
                        opsd_close_meta_box_section();
                            
                        
                        opsd_open_meta_box_section( $submit_form_name . 'style' , __('Email Styles', 'secure-downloads') ); 
                            
                            $this->mail_api()->show( 'style' );
                        
                        opsd_close_meta_box_section();
                        
                    ?>    
                    </div>

                    <div class="opsd_settings_row opsd_settings_row_right">
                    <?php 
                    
                        opsd_open_meta_box_section( $submit_form_name . 'actions', __('Actions', 'secure-downloads') ); 

                            ?><a class="button button-secondary" style="margin:0 0 5px;" href="javascript:void(0)" 
                                 onclick="javascript: jQuery('#form_action').val('test_send'); jQuery('form#<?php echo $submit_form_name_action; ?>').submit();"
                                ><?php _e('Send Test Email', 'secure-downloads'); ?></a><?php  
                                
                            ?><input type="submit" value="<?php _e('Save Changes', 'secure-downloads'); ?>" class="button button-primary right" style="margin:0 0 5px 5px;" /><?php 
                            
                            /* ?>
                            <a class="button button-secondary" href="javascript:void(0)" ><?php _e('Preview Email', 'secure-downloads'); ?></a>
                            <hr />
                            <a  class="button button-secondary right"   
                                href="javascript:void(0)" 
                                onclick="javascript: if ( opsd_are_you_sure('<?php echo esc_js(__('Do you really want to delete this item?', 'secure-downloads')); ?>') ){ 
                                                         jQuery('#form_action').val('delete');
                                                         jQuery('form#<?php echo $submit_form_name_action; ?>').submit();
                                                     }"
                                ><?php _e('Delete Email', 'secure-downloads'); ?></a>
                             <?php */ 
                            
                            ?><div class="clear"></div><?php   
                        
                        opsd_close_meta_box_section(); 
                        
                        opsd_open_meta_box_section( $submit_form_name . 'email_content_type', __('Type', 'secure-downloads') );
                            
                            $this->mail_api()->show( 'email_content_type' );
                            
                        opsd_close_meta_box_section(); 
                        
                        
                        opsd_open_meta_box_section( $submit_form_name . 'help', __('Help', 'secure-downloads') );
                            
                            $this->mail_api()->show( 'help' );
                            
                        opsd_close_meta_box_section(); 
                        
                    ?>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <input type="submit" value="<?php _e('Save Changes', 'secure-downloads'); ?>" class="button button-primary" />  
            </form>
        </span>
        <?php
        
        $this->enqueue_js();
    }
    
    
    /**
     * Update form  from Toolbar - create / delete/ load email templates
     * 
     * @return boolean
     */
    public function update_actions(  ) {
    
             
        if ( $_POST['form_action'] == 'test_send' ) {                           // Sending test  email
            
            /*
            $this->email_settings_api = false;    
            $selected_email_name = 'standard';    
            $email_fields = get_opsd_option( 'opsd_email_' . $selected_email_name );
            $this->mail_api( $selected_email_name, $email_fields );                
            */

            
            //$to = sanitize_email( $this->mail_api()->fields_values['to'] );
			
            $replace = array();
			$replace[ 'product_id' ] = '<strong>99</strong>';
			$replace[ 'product_title' ] = '<strong>Product ZZZ</strong>';
			$replace[ 'product_version' ] = '<strong>1.0</strong>';
			$replace[ 'product_description' ] = 'Product ZZZ Info';
			$replace[ 'product_filename' ] = 'zzz_product.zip';
			$replace[ 'product_link' ] = home_url();
			$replace[ 'product_size' ] = '3 Mb';
			$replace[ 'product_expire_after' ] = '1 day';
			$replace[ 'product_expire_date' ] = date_i18n( get_opsd_option( 'opsd_date_format' ) . ' ' . get_opsd_option( 'opsd_time_format' ), strtotime( '+1 day' ) );
			$replace[ 'product_summary' ] = '<a href="">' . $replace[ 'product_filename' ] . '</a> (' . $replace[ 'product_size' ] . ')  ~ expire in ' . $replace[ 'product_expire_after' ];

			$replace[ 'link_sent_to' ] = $this->mail_api()->get_from__email_address();

			$replace[ 'siteurl' ] = htmlspecialchars_decode( '<a href="' . home_url() . '">' . home_url() . '</a>' );
			$replace[ 'remote_ip' ] = opsd_get_user_ip();												// The IP address from which the user is viewing the current page. 
			$replace[ 'user_agent' ] = $_SERVER[ 'HTTP_USER_AGENT' ];									// Contents of the User-Agent: header from the current request, if there is one. 
			$replace[ 'request_url' ] = $_SERVER[ 'HTTP_REFERER' ];										// The address of the page (if any) where action was occured. Because we are sending it in Ajax request, we need to use the REFERER HTTP
			$replace[ 'current_date' ] = date_i18n( get_opsd_option( 'opsd_date_format' ) );
			$replace[ 'current_time' ] = date_i18n( get_opsd_option( 'opsd_time_format' ) );



			$to = $this->mail_api()->get_from__email_address();
            $to_name = $this->mail_api()->get_from__name();
            $to = trim(  $to_name ) . ' <' .  $to . '> ';
        
            $email_result = $this->mail_api()->send( $to , $replace );

            if ( $email_result ) 
                opsd_show_message ( __('Email sent to ', 'secure-downloads') . ' ' . $this->mail_api()->get_from__email_address() , 5 );             
            else 
                opsd_show_message ( __('Email had not sent. Some error occuered.', 'secure-downloads'), 5 ,'error' );    
        }

        /*
        if ( $_POST['form_action'] == 'create' ) {                              // Create
            
            $email_title = esc_attr( $_POST['create_email_template'] );            
            $email_name = opsd_get_slug_format_4_option_name( $email_title );
            
            $opsd_email_tpl_names = get_opsd_option( 'opsd_email_tpl_names' );
            if ( empty( $opsd_email_tpl_names ) )  $opsd_email_tpl_names = array();
            
            
            if ( empty($email_name) || isset( $opsd_email_tpl_names[ $email_name ] ) ) {      // Error
                opsd_show_message ( __('Email template has not added.', 'secure-downloads'), 5 , 'error' );  
                return false;                
            }
            
            $opsd_email_tpl_names[ $email_name ]= stripslashes( $email_title );
            
            update_opsd_option( 'opsd_email_tpl_names', $opsd_email_tpl_names );
            
            opsd_show_message ( __('Email template added successfully', 'secure-downloads'), 5 );                                               // Show Save message
            
            $redir = esc_url( add_query_arg( array('email_template' => $email_name ), html_entity_decode( $this->getUrl() ) ) );       
            
            opsd_reload_page_by_js( $redir );
            
            return true;            
        }
        
        if ( $_POST['form_action'] == 'delete' ) {                              // Delete
            $email_name = esc_attr( $_POST['select_email_template'] );
            
            $opsd_email_tpl_names = get_opsd_option( 'opsd_email_tpl_names' );
            if ( empty( $opsd_email_tpl_names ) )  $opsd_email_tpl_names = array();
            
            if ( ! isset( $opsd_email_tpl_names[ $email_name ] ) ) {            // Error
                opsd_show_message ( __('Email template does not exist.', 'secure-downloads'), 5 , 'error' );  
                return false;                
            } 
            
            unset($opsd_email_tpl_names[ $email_name ]);                        // Remove Email  name from list of email names
            update_opsd_option( 'opsd_email_tpl_names', $opsd_email_tpl_names );
            
            delete_opsd_option( 'opsd_email_' . $email_name );                  // Delete Email Template
            
            opsd_show_message ( __('Email template deleted successfully', 'secure-downloads'), 5 );                                     // Show Save message
            
                        
            $redir = esc_url( remove_query_arg( array( 'email_template' ), html_entity_decode( $this->getUrl() ) ) );       // Load standard email template
            
            opsd_reload_page_by_js( $redir );
            
            return true;            
            
        }
        
        if ( $_POST['form_action'] == 'load' ) {                                // Load
            $email_name = $_POST['select_email_template'];
            
            $opsd_email_tpl_names = get_opsd_option( 'opsd_email_tpl_names' );
            if ( empty( $opsd_email_tpl_names ) )  $opsd_email_tpl_names = array();
            
            if ( ! isset( $opsd_email_tpl_names[ $email_name ] ) ) {             // Error
                opsd_show_message ( __('Email template does not exist.', 'secure-downloads'), 5 , 'error' );  
                return false;                
            }
            
        }
        */
    }
    
    
    /** Update Email template to DB */
    public function update() {

        // Get Validated Email fields
        $validated_fields = $this->mail_api()->validate_post();

		// Remove <p> at begining and </p> at END of email template.
		if (
				( substr( $validated_fields['content'], 0, 3) === '<p>' ) 
			&&  ( substr( $validated_fields['content'], -4 ) === '</p>' ) 
			) {
			$validated_fields['content'] = substr ( $validated_fields['content'], 3, ( strlen ( $validated_fields['content'] ) - 7 ) );
		}
		
        $this->mail_api()->save_to_db( $validated_fields );
                
        opsd_show_message ( __('Settings saved.', 'secure-downloads'), 5 );              // Show Save message
    }

    // <editor-fold     defaultstate="collapsed"                        desc=" CSS & JS  "  >
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css">  
            .opsd-help-message {
                border:none;
                margin:0 !important;
                padding:0 !important;
            }
            
            @media (max-width: 399px) {
            }
        </style>
        <?php
    }
    
    
        
    /**     Add Custon JavaScript - for some specific settings options
     *      Executed After post content, after initial definition of settings,  and possible definition after POST request.
     * 
     * @param type $menu_slug
     * 
     */
    private function enqueue_js(){                                               // $page_tag, $active_page_tab, $active_page_subtab ) {

    
            
        // Check if this correct  page /////////////////////////////////////////////

//        if ( !(
//                   ( $page_tag == 'opsd-settings')                              // Load only at 'opsd-settings' menu
//                && ( $_GET['tab'] == 'email' )                                  // At ''general' tab
//                && (  ( ! isset( $_GET['subtab'] ) ) || ( $_GET['subtab'] == 'new-admin' )  )                                               
//              )
//          ) return;

        // JavaScript //////////////////////////////////////////////////////////////
        
        $js_script = '';
        //Show or hide colors section  in settings page depend form  selected email  template.
        $js_script .= " jQuery('select[name=\"link_user_template_file\"]').on( 'change', function(){    
                                if ( jQuery('select[name=\"link_user_template_file\"] option:selected').val() == 'plain' ) {   
                                    jQuery('.template_colors').hide();                                    
                                } else {
                                    jQuery('.template_colors').show();                                    
                                }
                            } ); ";    
        $js_script .= "\n";                                                     //New Line
        $js_script .= " if ( jQuery('select[name=\"link_user_template_file\"] option:selected').val() == 'plain' ) {   
                            jQuery('.template_colors').hide();                                    
                        } ";    
        
        // Show Warning messages if Title (optional) is empty - title of email  will be "WordPress
        $js_script .= " jQuery(document).ready(function(){ ";
        $js_script .= "     if (  jQuery('#link_user_to_name').val() == ''  ) {";
        $js_script .= "         jQuery('#link_user_to_name').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('If empty then title defined as WordPress', 'secure-downloads'))."</div>')";
        $js_script .= "     }";
        $js_script .= "     if (  jQuery('#link_user_from_name').val() == ''  ) {";
        $js_script .= "         jQuery('#link_user_from_name').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('If empty then title defined as WordPress', 'secure-downloads'))."</div>')";
        $js_script .= "     }";
        $js_script .= "  }); ";
          // Show Warning messages if "From" Email DNS different from current website DNS
        $js_script .= " jQuery(document).ready(function(){ ";
        
        $js_script .= "     var opsd_email_from = jQuery('#link_user_from').val();";    // from@oplugins.com 
        $js_script .= "     opsd_email_from = opsd_email_from.split('@');";             // ['from', 'oplugins.com']
        $js_script .= "     opsd_email_from.shift();";                                  // ['oplugins.com']
        $js_script .= "     opsd_email_from = opsd_email_from.join('');";              // 'oplugins.com'        

        $js_script .= "     var opsd_website_dns = jQuery(location).attr('hostname');"; // server.com
        $js_script .= "     if ( opsd_email_from != opsd_website_dns ) {";
        $js_script .= "         jQuery('#link_user_from').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('Email different from website DNS, its can be a reason of not delivery emails. Please use the email withing the same domain as your website!', 'secure-downloads'))."</div>')";
        $js_script .= "     }";

        $js_script .= "  }); ";
        
        
        
        // Eneque JS to  the footer of the page
        opsd_enqueue_js( $js_script );                
    }

    
    // </editor-fold>    
}
add_action('opsd_menu_created',  array( new OPSD_Settings_Page_Email_LinkUser() , '__construct') );    // Executed after creation of Menu



// <editor-fold     defaultstate="collapsed"                        desc=" Emails Sending After New item "  >

/** Send email to customer with  link for downloading file.
 * 
 * @param array $replace					- Array  with  replace parameters for email.
 * @param string $email_to					- Email address
 * @param string $send_copy_to_admin		- On|Off
 * @param array $other_params				- Optional. Array  of validated parameters from  submit form  at Send page
 * @return \OPSD_Emails_API_LinkUser|boolean
 */
function opsd_send_email_to_user_notification( $replace = array(), $email_to = '', $send_copy_to_admin = 'Off', $other_params = array() ) {
    
	$is_continue = true;
	
    $is_continue = apply_filters( 'opsd_send_email_to_user_notification_filter', $is_continue, $replace, $email_to, $send_copy_to_admin, $other_params);
	
	if ( $is_continue !== true )
		return $is_continue;
	
	
    ////////////////////////////////////////////////////////////////////////
    // Load Data 
    ////////////////////////////////////////////////////////////////////////

    /* Check if New Email Template   Exist or NOT
     * Exist     -  return  empty array in format: array( OPTION_NAME => array() ) 
     *              Its will  load DATA from DB,  during creattion mail_api CLASS
     *              during initial activation  of the API  its try  to get option  from DB
     *              We need to define this API before checking POST, to know all available fields
     *              Define Email Name & define field values from DB, if not exist, then default values. 
     * Not Exist -  import Old Data from DB
     *              or get "default" data from settings and return array with  this data
     *              This data its initial  parameters for definition fields in mail_api CLASS 
     * 
     */

    $init_fields_values = array();//opsd_import6_email__link_user__get_fields_array_for_activation();

    // Get Value of first element - array of default or imported OLD data,  because need only  array  of values without key - name of options for wp_options table
    //$init_fields_values = array_shift( array_values( $init_fields_values ) );               

    $mail_api = new OPSD_Emails_API_LinkUser( OPSD_EMAIL_LINK_USER_ID, $init_fields_values );

    ////////////////////////////////////////////////////////////////////////////
    
    if ( $mail_api->fields_values['enabled'] == 'Off' )     return false;       // Email  template deactivated - exit.

	add_filter( 'opsd_email_api_is_allow_send_copy' , 'opsd_email_api_is_allow_send_copy_block' , 10, 3);
	
	
    if ( ! empty( $replace['to'] ) )        
        $valid_email = sanitize_email( $replace['to'] );
    
    if ( ! empty( $email_to ) )
        $valid_email = sanitize_email( $email_to );
    
    if ( empty( $valid_email ) ) return $mail_api;        //return false;       
    
    if ( ! empty( $replace['to_name'] ) )        
        $email_to_name = trim( wp_specialchars_decode( esc_html( stripslashes( $replace['to_name'] ) ), ENT_QUOTES ) );
    else 
        $email_to_name = '';
    
    $to = $email_to_name . ' <' .  $valid_email . '> ';
    
    $email_result = $mail_api->send( $to , $replace );
    
    // Send copy  of email  to  admin  also to  "From" email address
    if ( $send_copy_to_admin == 'On') {
        $subject = $mail_api->get_field_value('subject');
        $mail_api->set_field_value('subject', __('Email copy to', 'secure-downloads') . ': ' . $valid_email . ' ' . $subject );
        $email_result = $mail_api->send( $mail_api->get_from__email_address() , $replace );
        $mail_api->set_field_value('subject', $subject );
    }
    
//debuge( (int) $email_result, $to , $replace);
    
    return $mail_api;    
    
}


/** Block  Sending copy of email to  Admin,  based on OPSD_Emails_API interface,  instead of that  we will sent it manually  from opsd_send_email_to_user_notification function
 * 
 * @param boolean $is_send_email
 * @param type $id
 * @param type $fields_values
 * @return boolean
 */
function opsd_email_api_is_allow_send_copy_block( $is_send_email, $id, $fields_values ) {
	$is_send_email = false;
	return $is_send_email;
}
// </editor-fold>