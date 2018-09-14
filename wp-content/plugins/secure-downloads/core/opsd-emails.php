<?php
/**
 * @version 1.1
 * @package Secure Downloads 
 * @category Send Emails
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 15.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



////////////////////////////////////////////////////////////////////////////////
// Emails
////////////////////////////////////////////////////////////////////////////////

/**
 * Check email and format  it
 * 
 * @param string $emails
 * @return string
 */
function opsd_validate_emails( $emails ) {

    $emails = str_replace(';', ',', $emails);

    if ( !is_array( $emails ) )
            $emails = explode( ',', $emails );

    $emails_list = array();
    foreach ( (array) $emails as $recipient ) {

        // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
        $recipient_name = '';
        if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
            if ( count( $matches ) == 3 ) {
                $recipient_name = $matches[1];
                $recipient = $matches[2];                 
            }
        } else {                
            // Check about correct  format  of email
            if( preg_match( '/([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}/im', $recipient, $matches ) ) {
                $recipient = $matches[0];
            }             
        }

        $recipient_name = str_replace('"', '', $recipient_name);
        $recipient_name = trim( wp_specialchars_decode( esc_html( stripslashes( $recipient_name ) ), ENT_QUOTES ) );

        $emails_list[] =   ( empty( $recipient_name ) ? '' : $recipient_name . ' '  )
                           . '<' . sanitize_email( $recipient ) . '>';		
    }

    $emails_list = implode( ',', $emails_list );

    return $emails_list;
}    






function opsd_check_for_several_emails_in_form( $mail_recipients, $formdata, $bktype ) {  // FixIn: 6.0.1.9

    $possible_other_emails = explode('~',$formdata);
    $possible_other_emails = array_map("explode", array_fill(0,count($possible_other_emails),'^'), $possible_other_emails);
    $other_emails = array();
    foreach ( $possible_other_emails as $possible_emails ) {
        if (       ( $possible_emails[0] == 'email' ) 
                //&& ( $possible_emails[1] != 'email' . $bktype ) 
                && ( ! empty($possible_emails[2]) ) 
            )
                $other_emails[]=$possible_emails[2];
    }
    if ( count( $other_emails ) > 1 ) {
        $other_emails = implode(',',$other_emails);
        $mail_recipients =  $other_emails;
    }
    return $mail_recipients;
}


//  N E W  /////////////////////////////////////////////////////////////////////


/** Parse email and get Parts of Email - Name and Email
 * 
 * @param string $email
 * @return array [email] => beta@oplugins.com
                 [title] => item system
                 [original] => "Item system" 
                 [original_to_show] => "Item system" <beta@oplugins.com>
 */         
function opsd_get_email_parts( $email ) {
        
    $email_to_parse =  html_entity_decode( $email );                                 // Convert &quot; to " etc...
    
    $pure_name  = '';
    $pure_email = '';
    if( preg_match( '/(.*)<(.+)>/', $email_to_parse, $matches ) ) {
        if ( count( $matches ) == 3 ) {
            $pure_name = $matches[1];
            $pure_email = $matches[2];                 
        }
    } else {                                                                    // Check about correct  format  of email
        if( preg_match( '/([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}/im', $email_to_parse, $matches ) ) {
            $pure_email = $matches[0];
        }             
    }
    
    $pure_name = trim( wp_specialchars_decode( esc_html( stripslashes( $pure_name ) ), ENT_QUOTES ) , ' "');
    
    $return_email = array(
                            'email' => sanitize_email( $pure_email )
                            , 'title' => $pure_name
                            , 'original' => $email_to_parse
                            , 'original_to_show' => htmlentities( $email_to_parse )         // Convert " to  &quot;  etc...
                    );
    
    return $return_email;
}


// Get Emails Help Shortcodes for Settings pages
function opsd_get_email_help_shortcodes( $skip_shortcodes = array() , $email_example = '') {
    
    $fields = array();
    $fields[] = '<strong>' . __('You can use following shortcodes in content of this template' , 'secure-downloads') . '</strong>';
    
    $fields[] = sprintf( __( '%s - ID of product', 'secure-downloads' )                , '<code>[product_id]</code>' );
    $fields[] = sprintf( __( '%s - title of product', 'secure-downloads' )             , '<code>[product_title]</code>' );
    $fields[] = sprintf( __( '%s - version number of product', 'secure-downloads' )			, '<code>[product_version]</code>' );
    $fields[] = sprintf( __( '%s - description of product', 'secure-downloads' )       , '<code>[product_description]</code>' );
    $fields[] = sprintf( __( '%s - secure URL for product download','secure-downloads'), '<code>[product_link]</code>' );
    $fields[] = sprintf( __( '%s - filename of the product', 'secure-downloads' )      , '<code>[product_filename]</code>' );
    
    $fields[] = sprintf( __( '%s - the download size in a friendly format such as %s or %s', 'secure-downloads' ),        '<code>[product_size]</code>',   '500 KB', '3.45 MB' );    
    $fields[] = sprintf( __( '%s - expiry time in friendly format (e.g. 24 hours or 2 days etc)', 'secure-downloads' ),   '<code>[product_expire_after]</code>' );    
    $fields[] = sprintf( __( '%s - exact expiry date and time  (e.g. 2017-03-21 18:30)', 'secure-downloads' ),            '<code>[product_expire_date]</code>' );    
    $fields[] = sprintf( __( '%s - complete product details including title, size, link etc', 'secure-downloads' ),       '<code>[product_summary]</code>' );
	$fields[] = '<hr/>';    
    $fields[] = sprintf( __( '%s - email, which was sent to secure link', 'secure-downloads' ),       '<code>[link_sent_to]</code>' );
//    $fields[] = sprintf( __( '%s - order', 'secure-downloads' ),       '<code>[order]</code>' );
    $fields[] = '<hr/>';    
    $fields[] = sprintf( __( '%s - website URL ', 'secure-downloads' ),                                    '<code>[siteurl]</code>' );
    $fields[] = sprintf( __( '%s - IP address of the user, who made this action ', 'secure-downloads' ),   '<code>[remote_ip]</code>' );
    $fields[] = sprintf( __( '%s - contents of the User-Agent header from the current request, if there is one ', 'secure-downloads' ), '<code>[user_agent]</code>' );
    $fields[] = sprintf( __( '%s - address of the page (if any), where visitor make this action ', 'secure-downloads' ), '<code>[request_url]</code>' );
    $fields[] = sprintf( __( '%s - date of this action ', 'secure-downloads' ),                          '<code>[current_date]</code>' );
    $fields[] = sprintf( __( '%s - time of this action ', 'secure-downloads' ),                          '<code>[current_time]</code>' );


    /*
    //$fields[] = __('HTML tags is accepted.' , 'secure-downloads');
//    $fields[] = '<hr/>';
//    // show_additional_translation_shortcode_help
//    $fields[] = '<strong>' . sprintf(__('Configuration in several languages' , 'secure-downloads') ) . '.</strong>';
//    $fields[] = sprintf(__('%s - start new translation section, where %s - locale of translation' , 'secure-downloads'),'<code>[lang=LOCALE]</code>','<code>LOCALE</code>');
//    $fields[] = sprintf(__('Example #1: %s - start French translation section' , 'secure-downloads'),'<code>[lang=fr_FR]</code>');
//    $fields[] = sprintf(__('Example #2: "%s" - English and French translation of some message' , 'secure-downloads'),'<code>Thank you for your item.[lang=fr_FR]Je vous remercie de votre reservation.</code>');
    */

    return $fields;           
}


/** Check  Email  subject  about Language sections
 * 
 * @param string $subject
 * @param string $email_id
 * @return string
 */
function opsd_email_api_get_subject_before( $subject, $email_id ) {
            
    $subject =  apply_opsd_filter('opsd_check_for_active_language', $subject );

    return  $subject;
}
add_filter( 'opsd_email_api_get_subject_before', 'opsd_email_api_get_subject_before', 10, 2 );    // Hook fire in api-email.php


/** Check  Email  sections content  about Language sections
 * 
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function opsd_email_api_get_content_before( $fields_values, $email_id , $email_type ) {
    
    if ( isset( $fields_values['content'] ) ) {
        $fields_values['content'] =  apply_opsd_filter('opsd_check_for_active_language', $fields_values['content'] );
        if ($email_type == 'html')
            $fields_values['content'] = make_clickable( $fields_values['content'] );
    }
    
    if ( isset( $fields_values['header_content'] ) )
        $fields_values['header_content'] =  apply_opsd_filter('opsd_check_for_active_language', $fields_values['header_content'] );
    
    if ( isset( $fields_values['footer_content'] ) )
        $fields_values['footer_content'] =  apply_opsd_filter('opsd_check_for_active_language', $fields_values['footer_content'] );
    
    return $fields_values;
}
add_filter( 'opsd_email_api_get_content_before', 'opsd_email_api_get_content_before', 10, 3 );    // Hook fire in api-email.php


/** Modify email  content,  if needed. - In HTML mail content,  make links clickable.
 * 
 * @param array $email_content - content of Email
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function opsd_email_api_get_content_after( $email_content, $email_id , $email_type ) {
    
    if (  ( $email_type == 'html' ) || ( $email_type == 'multipart' )  )
       $email_content = make_clickable( $email_content );
     
    return $email_content;
}
add_filter( 'opsd_email_api_get_content_after', 'opsd_email_api_get_content_after', 10, 3 );    // Hook fire in api-email.php


/** Check  Email  Headers  -  in New item Email (to admin) set Reply-To header to visitor email.
 * 
 * @param string $headers
 * @param string $email_id - Email ID
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param array $replace_array - list  of relpaced shortcodes
 * @return string
 */
function opsd_email_api_get_headers_after( $mail_headers, $email_id , $fields_values , $replace_array, $additional_params = array() ) {
       
/*
// Default in api-emails.php:
//        $mail_headers  = 'From: ' . $this->get_from__name() . ' <' .  $this->get_from__email_address() . '> ' . "\r\n" ;
//        $mail_headers .= 'Content-Type: ' . $this->get_content_type() . "\r\n" ;
//        
//            $mail_headers = "From: $mail_sender\n";
//            preg_match('/<(.*)>/', $mail_sender, $simple_email_matches );
//            $reply_to_email = ( count( $simple_email_matches ) > 1 ) ? $simple_email_matches[1] : $mail_sender;
//            $mail_headers .= 'Reply-To: ' . $reply_to_email . "\n";        
//            $mail_headers .= 'X-Sender: ' . $reply_to_email . "\n";
//            $mail_headers .= 'Return-Path: ' . $reply_to_email . "\n";
*/

//debuge($mail_headers, $email_id , $fields_values , $replace_array);    
    if (
        ( $email_id == 'new_admin' )                                            // Only  for email: "New item to Admin"
       || ( isset( $additional_params['reply'] ) )  
    ) {
        if ( isset( $replace_array['email'] ) ) {                                // Get email from  the item form.
           
            $reply_to_email = sanitize_email( $replace_array['email'] );
            if ( ! empty( $reply_to_email ) )
                $mail_headers .= 'Reply-To: '    . $reply_to_email  . "\r\n" ;
            
           // $mail_headers .= 'X-Sender: '    . $reply_to_email  . "\r\n" ;
           // $mail_headers .= 'Return-Path: ' . $reply_to_email  . "\r\n" ;           
        }
    }

    return  $mail_headers;
}
add_filter( 'opsd_email_api_get_headers_after', 'opsd_email_api_get_headers_after', 10, 5 );    // Hook fire in api-email.php


/** Check if we can send Email - block  sending in live demos
 * 
 * @param bool $is_send_email 
 * @param string $email_id
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @return bool
 */
function opsd_email_api_is_allow_send( $is_send_email, $email_id, $fields_values ) {
//debuge($fields_values);    
    if ( opsd_is_this_demo() )   
        $is_send_email = false;

    return  $is_send_email;
}
add_filter( 'opsd_email_api_is_allow_send', 'opsd_email_api_is_allow_send', 100, 3 );    // Hook fire in api-email.php
add_filter( 'opsd_email_api_is_allow_send_copy' , 'opsd_email_api_is_allow_send' , 100, 3);

/** Show warning about not sending emails,  and reason about this.
 * 
 * @param object $wp_error_object     - WP Error object
 * @param string $error_description   - Description
 */
function opsd_email_sending_error( $wp_error_object, $error_description = '' ) {
    
    if ( empty( $error_description ) ) {
//        $error_description = __( 'Unknown exception', 'secure-downloads') . '.';        // Overwrite to  show error, if no description ???    
    }
    
    if ( ! empty( $error_description ) ) {

        $error_description = '' . __('Error', 'secure-downloads')  . '! ' . __('Email had not sent. Some error occuered.', 'secure-downloads') .  ' ' . $error_description;
        
        // Admin side
        if (  function_exists( 'opsd_show_message' ) ) {
            opsd_show_message ( $error_description , 15 , 'error');     

        }
        
        // Front-end
        ?>   
        <script type="text/javascript">  
            if (typeof( opsd_show_message_under_element ) == 'function') {
                opsd_show_message_under_element( '.opsd_form' , '<?php echo esc_js( $error_description ) ; ?>', '');
            }
        </script>    
        <?php    
    } else {
        
        // Error that have no description. Its can be Empty Object like this: WP_Error Object(  'errors' => array(), 'error_data' => array() ),  or NOT
        // debuge( $wp_error_object );        
    }
}
add_action('opsd_email_sending_error', 'opsd_email_sending_error', 10, 2);