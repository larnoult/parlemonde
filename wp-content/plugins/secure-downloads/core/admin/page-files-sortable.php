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
class OPSD_Page_FilesSortable extends OPSD_Page_Structure {

	
    public function in_page() {
        return 'opsd-files';
    }

    public function tabs() {
        
        $tabs = array();
        $tabs[ 'files-sortable' ] = array(
                              'title' => __('Sortable List', 'secure-downloads')            // Title of TAB    
                            , 'hint' => __('Sortable List', 'secure-downloads')                      // Hint    
                            , 'page_title' => __('Manage List', 'secure-downloads')                                // Title of Page    
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-sort'                 // CSS definition  of forn Icon
                            , 'default' => !true                                 // Is this tab activated by default or not: true || false. 
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
        
        $submit_form_name = 'opsd_sortable_products_form';                           // Define form name
        $updated_data = '';        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {       // Check  if was clicked on Saved 

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'opsd_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $updated_data = $this->update();
            //$updated_data = $updated_data['original_validated_data'];
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

				<?php opsd_open_meta_box_section( 'opsd_sortable_products_list', __('Products List', 'secure-downloads') );  ?>
				
				<?php $this->show_soratble_table(); ?>
				
				<?php opsd_close_meta_box_section(); ?>

                <div class="clear"></div>
                <input type="submit" value="<?php _e('Save Changes', 'secure-downloads'); ?>" class="button button-primary opsd_submit_button" />  
								
            </form>            
        </span>
        <?php 
        do_action( 'opsd_hook_settings_page_footer', 'sortable_products' );    		
    }

 
	/**  Save Settings
	 * 
	 * @return array
	 */
    public function update() {

//debuge($_POST);

		if ( isset( $_POST['pro'] ) ) {
			
			// Get Exist List of products ////////////////////////////////////
			$products_csv = get_opsd_option('opsd_products_csv' ); 

			$products_obj = new OPSD_Products();

			$products_obj->define_products_from_csv( $products_csv );  

			$products = $products_obj->get_products();		 /**
				[id] => 206
				[title] => product.developer
				[version_num] => 'gdfgd'
				[description] => 'gdfgdf'
				[path] => /wp-content/uploads/opsd_lSJacOT1yVLFnrkqt2xR/2017/04/product.developer.zip
			 */

			// New products /////////////////////////////////////////////////
			$new_products_arr = array();
//debuge($_POST);
			foreach ( $_POST['pro'] as $post_index => $pro_id ) {

				$product_arr = array();
				
				$exist_product = $products_obj->get_product( $pro_id );
//debuge($pro_id, $exist_product)				;
				if ( ! empty( $exist_product ) ) {
					
					$post_key = 'pro';			// ID
					if ( isset( $_POST[ $post_key ] ) &&  isset( $_POST[ $post_key ][ $post_index ] ) ) {				
						$product_arr[ 'id' ] = OPSD_Settings_API::validate_text_post_static( $post_key , $post_index );		// Validate
					}				
					$post_key = 'pro_title';
					if ( isset( $_POST[ $post_key ] ) &&  isset( $_POST[ $post_key ][ $post_index ] ) ) {				
						$product_arr[ 'title' ] = OPSD_Settings_API::validate_text_post_static( $post_key , $post_index );	// Validate
					}
					$post_key = 'pro_version_num';
					if ( isset( $_POST[ $post_key ] ) &&  isset( $_POST[ $post_key ][ $post_index ] ) ) {				
						$product_arr[ 'version_num' ] = OPSD_Settings_API::validate_text_post_static( $post_key , $post_index );	// Validate
					}

					$post_key = 'pro_description';
					if ( isset( $_POST[ $post_key ] ) &&  isset( $_POST[ $post_key ][ $post_index ] ) ) {				
						$product_arr[ 'description' ] = OPSD_Settings_API::validate_text_post_static( $post_key , $post_index );	// Validate
					}

					//$product_arr[ 'description' ] = $exist_product['description'];

					$post_key = 'pro_path';
					if ( isset( $_POST[ $post_key ] ) &&  isset( $_POST[ $post_key ][ $post_index ] ) ) {				
						$product_arr[ 'path' ] = trim( str_replace( 
																	site_url()
																	, ''
																	,  OPSD_Settings_API::validate_text_post_static( $post_key , $post_index ) 
												) );	// Get local relative path
					}
				} else {
					
					// Product  does not exist so probabaly  its Group section  title.
					$post_key = 'pro_title';
					if ( isset( $_POST[ $post_key ] ) &&  isset( $_POST[ $post_key ][ $post_index ] ) ) {				
						$product_arr[ 'title' ] = OPSD_Settings_API::validate_text_post_static( $post_key , $post_index );	// Validate
					}
					
				}
				$new_products_arr[] = $product_arr;
			}
						
			// Create CSV 					
//debuge($new_products_arr);
			$products_csv = $products_obj->save_products( $new_products_arr );	
			opsd_show_changes_saved_message();                                  
		
			return array ( 'original_validated_data' => $products_csv );
		}
		
		
		
//		$validated_option = OPSD_Settings_API::validate_text_post_static( 'opsd_sortable_products_text' );
//		
//		update_opsd_option('opsd_sortable_products', $validated_option );
//		
//		opsd_show_changes_saved_message();                                  
//		
//		return array (   'original_validated_data' => $validated_option );
		
		
		/** Standard Saving || Actions 
		 * 
        $post_action_key = 'opsd_action';
        
        $post_key        = 'opsd_sortable_products_text'; 
        
        if (  isset( $_POST[ $post_action_key ] )  && ( $_POST[ $post_action_key ] == 'gogo2list' ) && ( isset( $_POST[ $post_key ] ) )   ) {
                        
            // Get Validated post
            $gogo_validated = OPSD_Settings_API::validate_text_post_static( $post_key );

            
            $show_debug_info_validated = OPSD_Settings_API::validate_checkbox_post_static( 'show_debug_info' );
            if ($show_debug_info_validated == 'On'){
                debuge( 'POST', $_POST );                            
                debuge('Validated data', $gogo_validated );            
            }
            
			
            update_opsd_option('opsd_sortable_products', $gogo_validated );                 // Save to DB   
            
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
	
		
	public function show_soratble_table() {
		
		// Get List of products ////////////////////////////////////
		$products_csv = get_opsd_option('opsd_products_csv' ); 

		$products_obj = new OPSD_Products();

		$products_obj->define_products_from_csv( $products_csv );   /** Array
																		(
																			[id] => 0
																			[order] => 0
																			[ip] => 0.0.0.0
																			[ipl] => 0
																			[expire] => +1440 seconds
																			[title] => Personal
																			[size] => 3.45
																			[path] => XXXXXXXXXX/wpbc/personal.zip
																		), ...
																	 */

		$products = $products_obj->get_products();
					
//debuge($products);die;					
/**
					[id] => 207
						[order] => 0
						[ip] => 0.0.0.0
						[expire] => +1 day
                    [title] => product.multisite
                    [version_num] => 
                    [description] => 
                    [path] => /wp-content/uploads/opsd_lSJacOT1yVLFnrkqt2xR/2017/04/product.multisite.zip
 */
        ?>
        <div class="opsd_sortable_table wpdevelop" >
            <table class="widefat opsd_input_table sortable table table-striped" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th class="sort">&nbsp;</th>
                        <th style="width:30px;"><?php _e('ID', 'secure-downloads' ) ?></th>
                        <th style="width:15%"><?php _e('Title', 'secure-downloads' ) ?></th>
                        <th style="width:10%"><?php _e('Version', 'secure-downloads' ) ?></th>
                        <th style="width:22%"><?php _e('Desciption', 'secure-downloads' ) ?></th>
                        <th><?php _e('Path', 'secure-downloads' ) ?></th>
						<th style="width:70px;"><?php _e('Actions', 'secure-downloads' ) ?></th>
                    </tr>
                </thead>
                <tbody class="accounts">
                    <?php
                    $i = -1;
                    if ( ! empty( $products ) ) {
                        
                        foreach ( $products as $product_id => $product ) {                                            
							$i++;
							
							if ( count($product) > 5 ) {	//Skip emty  rows.
								
								$product_id = $product[ 'id' ];
								echo 
								'<tr class="account" id="'. $product_id .'">'
									. '<td class="sort"><input type="hidden" value="' . esc_attr( wp_unslash( $product_id ) ) . '" name="pro[' . $i . ']" /></td>'
									. '<th style="vertical-align:middle;">' . wp_unslash( esc_js( $product_id ) ) . '</th>'
									. '<td><input style="font-weight:600;" type="text" value="' . wp_unslash( esc_js( $product['title'] ) ) . '" name="pro_title[' . $i . ']" /></td>'
									. '<td><input type="text" value="' . wp_unslash( esc_js( $product['version_num'] ) ) . '" name="pro_version_num[' . $i . ']" /></td>'
									. '<td><textarea name="pro_description[' . $i . ']" style="height:2em;width:100%;">'.wp_unslash( esc_js( $product['description'] ) ).'</textarea></td>'
									. '<td><input type="text" value="' . wp_unslash( esc_js( $product['path'] ) ) . '" name="pro_path[' . $i . ']" /></td>'
								    . '<td style="text-align:center;">'
										. '<a 	href="javascript:void(0)" 
												class="button-secondary button opsd_delete_row" 
												title="'. esc_js( __( 'Delete', 'secure-downloads' ) ) .'" 
												data-original-title="Completely "><i class="glyphicon glyphicon-remove"></i></a>'
									.'</td>'
								. '</tr>';
							} else {
								
								$product_id = time() + wp_rand( 1000 );
								echo '<tr class="account" id="'. $product_id .'">'
										. '<td class="sort"><input type="hidden" value="' . esc_attr( wp_unslash( $product_id ) ) . '" name="pro[' . $i . ']" /></td>'
										. '<td style="vertical-align:middle;" colspan="5">' 
										. '<input style="font-weight:600;" type="text" value="' . wp_unslash( esc_js( $product['title'] ) ) . '" name="pro_title[' . $i . ']" />'
										. '</td>'
										. '<td style="text-align:center;">'
											. '<a 	href="javascript:void(0)" 
													class="button-secondary button opsd_delete_row" 
													title="'. esc_js( __( 'Delete', 'secure-downloads' ) ) .'" 
													data-original-title="Completely "><i class="glyphicon glyphicon-remove"></i></a>'
										.'</td>'
									. '</tr>';
							}
                        }
                    }
                    ?>
                </tbody>
<?php /* ?>                
                <tfoot>
                    <tr>
                        <th colspan="7"><a href="#" class="add button"><?php _e( '+ Add Account' , 'secure-downloads' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected account(s)' , 'secure-downloads' ); ?></a></th>
                    </tr>
                </tfoot>
<?php /**/ ?>            
            </table>
            <script type="text/javascript">
				
                jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });
                    

					$('.opsd_input_table tbody th, .opsd_sortable_table tbody td').css('cursor','move');

					$('.opsd_input_table tbody td.sort').css('cursor','move');

					$('.opsd_input_table.sortable tbody').sortable({
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

					jQuery( '.opsd_sortable_table' ).on( 'click', '.opsd_delete_row', function ( event ) {				// This delegated event, can be run, when DOM element added after page loaded

						var jq_row = jQuery( this ).closest( 'tr' );

						jq_row.remove();						
					});
                    
					
					jQuery( '.opsd_sortable_table' ).on( 'focusin',  'textarea[name^="pro_description"]', function(){ jQuery( this ).css( 'height', '5em' );  } );
					jQuery( '.opsd_sortable_table' ).on( 'focusout', 'textarea[name^="pro_description"]', function(){ jQuery( this ).css( 'height', '2em' );  } );
					
					//jQuery( 'input[name^="pro_path"]' ).on( 'focus', function(){ jQuery( this ).css( 'height', '5em' );  } );
					//jQuery( 'input[name^="pro_path"]' ).on( 'blur', function(){ jQuery( this ).css( 'height', 'auto' );  } );
					/** Add new row
                    jQuery('.opsd_sortable_table').on( 'click', 'a.add', function(){

                        var size = jQuery('.opsd_sortable_table tbody .account').size();

                        jQuery('<tr class="account">\
                                    <td class="sort"></td>\
                                    <td><legend class="opsd_mobile_legend"><?php echo esc_js( '' ); ?>:</legend><input type="text" name="bank_transfer_account_name[' + size + ']" /></td>\
                                    <td><legend class="opsd_mobile_legend"><?php echo esc_js( '' ); ?>:</legend><input type="text" name="bank_transfer_account_number[' + size + ']" /></td>\
                                    <td><legend class="opsd_mobile_legend"><?php echo esc_js( '' ); ?>:</legend><input type="text" name="bank_transfer_bank_name[' + size + ']" /></td>\
                                    <td><legend class="opsd_mobile_legend"><?php echo esc_js( '' ); ?>:</legend><input type="text" name="bank_transfer_sort_code[' + size + ']" /></td>\
                                </tr>').appendTo('.opsd_sortable_table table tbody');

                        jQuery('.opsd_input_table tbody th, .opsd_sortable_table tbody td').css('cursor','move');
                        return false;
                    });
					*/
		
					/** Delete current row depend from Focus
                    $('.opsd_input_table .remove_rows').click(function() {
                            var $tbody = $(this).closest('.opsd_input_table').find('tbody');
                            if ( $tbody.find('tr.current').size() > 0 ) {
                                    $current = $tbody.find('tr.current');

                                    $current.each(function(){
                                            $(this).remove();
                                    });
                            }
                            return false;
                    });

                    var controlled = false;
                    var shifted = false;
                    var hasFocus = false;

                    $(document).bind('keyup keydown', function(e){ shifted = e.shiftKey; controlled = e.ctrlKey || e.metaKey } );

                    $('.opsd_input_table').on( 'focus click', 'input', function( e ) {

                            $this_table = $(this).closest('table');
                            $this_row   = $(this).closest('tr');

                            if ( ( e.type == 'focus' && hasFocus != $this_row.index() ) || ( e.type == 'click' && $(this).is(':focus') ) ) {

                                    hasFocus = $this_row.index();

                                    if ( ! shifted && ! controlled ) {
                                            $('tr', $this_table).removeClass('current').removeClass('last_selected');
                                            $this_row.addClass('current').addClass('last_selected');
                                    } else if ( shifted ) {
                                            $('tr', $this_table).removeClass('current');
                                            $this_row.addClass('selected_now').addClass('current');

                                            if ( $('tr.last_selected', $this_table).size() > 0 ) {
                                                    if ( $this_row.index() > $('tr.last_selected, $this_table').index() ) {
                                                            $('tr', $this_table).slice( $('tr.last_selected', $this_table).index(), $this_row.index() ).addClass('current');
                                                    } else {
                                                            $('tr', $this_table).slice( $this_row.index(), $('tr.last_selected', $this_table).index() + 1 ).addClass('current');
                                                    }
                                            }

                                            $('tr', $this_table).removeClass('last_selected');
                                            $this_row.addClass('last_selected');
                                    } else {
                                            $('tr', $this_table).removeClass('last_selected');
                                            if ( controlled && $(this).closest('tr').is('.current') ) {
                                                    $this_row.removeClass('current');
                                            } else {
                                                    $this_row.addClass('current').addClass('last_selected');
                                            }
                                    }

                                    $('tr', $this_table).removeClass('selected_now');

                            }
                    }).on( 'blur', 'input', function( e ) {
                            hasFocus = false;
                    });
					*/
                
				});
            </script>       
        </div>
        <?php

	}
	
}
add_action('opsd_menu_created', array( new OPSD_Page_FilesSortable() , '__construct') );    // Executed after creation of Menu
