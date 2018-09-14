<?php /**
 * @version 1.0
 * @package Secure Downloads 
 * @category Content of Add New Item
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-10-31
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class OPSD_Page_Files extends OPSD_Page_Structure {

	
    public function in_page() {
        return 'opsd-files';
    }

    public function tabs() {
        
        $tabs = array();
        $tabs[ 'add-files' ] = array(
                              'title' => __('Add New', 'secure-downloads')            // Title of TAB    
                            , 'hint' => __('Manage Files', 'secure-downloads')                      // Hint    
                            , 'page_title' => __('Manage Files', 'secure-downloads')                                // Title of Page    
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-plus'                 // CSS definition  of forn Icon
                            , 'default' => true                                 // Is this tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this tab disbaled: true || false. 
                            , 'hided'   =>  !true                                 // Is this tab hided: true || false. 
                            , 'subtabs' => array()            
        );
        
        return $tabs;        
    }


    public function content() {

        // Checking ////////////////////////////////////////////////////////////
        
        do_action( 'opsd_hook_settings_page_header', array( 'page' => $this->in_page() ) );					// Define Notices Section and show some static messages, if needed.
                    
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'opsd_products_csv_form';                           // Define form name
        $updated_data = '';        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {       // Check  if was clicked on Saved 

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'opsd_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $updated_data = $this->update();
            $updated_data = $updated_data['original_validated_data'];
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

				<?php opsd_open_meta_box_section( 'opsd_products_csv', __('Files List', 'secure-downloads') );  ?>

					<table class="form-table">
						<tbody>
							<tr class="opsd_tr_opsd_products_csv_text " valign="top">
								<td scope="row" colspan="2">
									<?php    

									$field_name = 'opsd_products_csv_text';    

									$field_value = get_opsd_option('opsd_products_csv' );
									//$field_value = json_encode( $field_value );										// array('a' => 1, 'b' => 2 ... ) => {"a":1,"b":2 ...}

									$place_holder = str_replace( '|', get_opsd_option( 'opsd_csv_separator' ), __( 'ID | Title | Version Number | Desciption | Path (URL)', 'secure-downloads' ) );
									$field = array(
													  'title' => ''
													, 'description' => ''
													, 'type' => 'textarea'
													, 'value' => empty( $updated_data ) ? $field_value : $updated_data
													, 'class' => ''
													, 'css' => 'width:100%;'
													, 'placeholder' => $place_holder
													, 'disabled' => false
													, 'rows' => 8
													, 'show_in_2_cols' => true
													, 'only_field' => true
												);                        
									OPSD_Settings_API::field_textarea_row_static( $field_name, $field );
									
									?>
								</td>								
							</tr>									
							<tr class="opsd_tr_opsd_products_csv_text " valign="top">
								<td scope="row" colspan="2" style="font-style: italic;">
								<?php 										
									echo __( 'Please use one product per line.', 'secure-downloads' ) . '<br/>';
									echo __( 'CSV product configuration structure', 'secure-downloads' ) . ': <b>' . $place_holder . '</b>' . '<br/>';
									echo sprintf( __( 'Or just use simple text at  row (without separators %s) for definition section.', 'secure-downloads' ), '<strong>' . get_opsd_option( 'opsd_csv_separator' ) . '</strong>' )  . '<br/>';
								?>
								</td>								
							</tr>
						</tbody>
					</table>                                       

				<?php opsd_close_meta_box_section(); ?>

                <div class="clear"></div>
				
				<?php		
				/////////////////////////////////////////////////////////////////////////
				// Add New Files button
				/////////////////////////////////////////////////////////////////////////
				?>
				<span class='wpdevelop' style="margin-right:15px;">
					<a class="button button-secondary opsd_btn_upload"  
					   style="font-weight: 600;"
					   <?php echo 'data-' . esc_attr( 'modal_title' )	. '="' . esc_attr( __( 'Choose files', 'secure-downloads' ) ) . '" '; ?>
					   <?php echo 'data-' . esc_attr( 'btn_title' )		. '="' . esc_attr( __( 'Insert', 'secure-downloads' ) ) . '" '; ?>						   
					   href="javascript:void(0)"  title="<?php _e('Add New Product' , 'secure-downloads') ?>"
					   ><span style="opsd_text_hide_mobile0"><?php echo __('Add New' , 'secure-downloads') . ' ' . __( 'Files', 'secure-downloads' ); ?>&nbsp;&nbsp;</span><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
				</span>					
				<?php 

				// Get OPSD_Upload obj. instance
				$opsd_upload = opsd_upload();	

				$opsd_upload->set_upload_button( '.opsd_btn_upload' );

				$opsd_upload->set_element_insert_url( '.opsd_file_urls' );

				?><input type="submit" value="<?php _e('Save Changes', 'secure-downloads'); ?>" class="button button-primary opsd_submit_button" /><?php 

									
				/////////////////////////////////////////////////////////////////////////
				// Help Notice
				/////////////////////////////////////////////////////////////////////////

				?><div class="clear"style="margin-top:25px;"></div><?php 
					
					$notice_id = 'opsd_upload_help_section';
					if ( ! opsd_section_is_dismissed( $notice_id ) ) {
						
						?><div  id="<?php echo $notice_id; ?>" 							           
								class="opsd_system_notice opsd_is_dismissible opsd_is_hideable notice-warning opsd_internal_notice"
								data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_opsdnonce' ); ?>"	
								data-user-id="<?php echo get_current_user_id(); ?>"
							><?php 

						opsd_x_dismiss_button();

						$field_options = array();					
						$field_options[] = '<strong>' . __( 'How to add new product ?', 'secure-downloads' ) . '</strong>';
						$field_options[] = '1. ' . sprintf( __( 'Click on %s"Add New"%s button and upload your files', 'secure-downloads' ), '<strong>', '</strong>' );
						$field_options[] = '2. ' . sprintf( __( 'Enter Title, Version Number and Description at %s"Attachment details"%s section', 'secure-downloads' ), '<strong>', '</strong>' ) . ' <em>(' . __( 'at right side of page', 'secure-downloads' ) . ')</em>';
						$field_options[] = '3. ' . sprintf( __( 'Select one or multiple files and click on insert button', 'secure-downloads' ) );
						$field_options[] = '4. ' . sprintf( __( 'Save the changes.', 'secure-downloads' ) );

						OPSD_Settings_API::field_help_row_static(
															'help_translation_section_after_legend_items'
															, array(   
																   'type'              => 'help'
																 , 'value'             => $field_options
																 , 'class'             => ''
																 , 'css'               => 'margin:0;padding:0;border:0;'
																 , 'description'       => ''
																 , 'cols'              => 2 
																 , 'group'             => 'help'
																 , 'tr_class'          => ''
																 , 'description_tag'   => 'p'
															)										
														);														
						?></div><?php
					}
					
				?>				
            </form>            
        </span>
        <?php 
        do_action( 'opsd_hook_settings_page_footer', 'csv_products' );    		
    }

 
	/**  Save Settings
	 * 
	 * @return array
	 */
    public function update() {
          
		$validated_option = OPSD_Settings_API::validate_text_post_static( 'opsd_products_csv_text' );
		
		update_opsd_option('opsd_products_csv', $validated_option );
		
		opsd_show_changes_saved_message();                                  
		
		return array (   'original_validated_data' => $validated_option );
		
		
		/** Standard Saving || Actions 
		 * 
        $post_action_key = 'opsd_action';
        
        $post_key        = 'opsd_products_csv_text'; 
        
        if (  isset( $_POST[ $post_action_key ] )  && ( $_POST[ $post_action_key ] == 'gogo2list' ) && ( isset( $_POST[ $post_key ] ) )   ) {
                        
            // Get Validated post
            $gogo_validated = OPSD_Settings_API::validate_text_post_static( $post_key );

            
            $show_debug_info_validated = OPSD_Settings_API::validate_checkbox_post_static( 'show_debug_info' );
            if ($show_debug_info_validated == 'On'){
                debuge( 'POST', $_POST );                            
                debuge('Validated data', $gogo_validated );            
            }
            
			
            update_opsd_option('opsd_products_csv', $gogo_validated );                 // Save to DB   
            
            opsd_show_changes_saved_message();     
			
            // opsd_show_message ( __('Done', 'secure-downloads'), 0 );                        // Show Message
            
            return array (   'original_validated_data' => $gogo_validated );                                             // Exit, for do  not parse 
        }
		*/
		
		/** Standard Bulk Saving of settings
		$validated_fields = $this->settings_api()->validate_post();														// Get Validated Settings fields in $_POST request.

		$validated_fields = apply_filters( 'opsd_settings_validate_fields_before_saving', $validated_fields );			//Hook for validated fields.
		
		unset($validated_fields['opsd_start_day_weeek']);																// Skip saving specific option, for example in Demo mode.

		$this->settings_api()->save_to_db( $validated_fields );															// Save fields to DB
		//opsd_show_changes_saved_message();
		opsd_show_message ( __('Done', 'secure-downloads'), 0 );																// Show Message
        */

		/** O L D   W A Y:   Saving Fields Data
		 * 		 
			update_opsd_option( 'opsd_is_delete_if_deactive'
							 , OPSD_Settings_API::validate_checkbox_post('opsd_is_delete_if_deactive') );  
			( (isset( $_POST['opsd_is_delete_if_deactive'] ))?'On':'Off') );
		*/
    }
	
}
add_action('opsd_menu_created', array( new OPSD_Page_Files() , '__construct') );    // Executed after creation of Menu