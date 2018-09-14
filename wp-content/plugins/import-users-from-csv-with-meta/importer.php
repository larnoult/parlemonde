<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

function acui_import_users( $file, $form_data, $attach_id = 0, $is_cron = false, $is_frontend = false ){?>
	<div class="wrap">
		<h2><?php _e('Importing users','import-users-from-csv-with-meta'); ?></h2>
		<?php
			set_time_limit(0);
			
			do_action( 'before_acui_import_users' );

			global $wpdb;
			global $wp_users_fields;
			global $wp_min_fields;
			global $acui_fields;
			$acui_restricted_fields = acui_get_restricted_fields();

			if( is_plugin_active( 'wp-access-areas/wp-access-areas.php' ) ){
				$wpaa_labels = WPAA_AccessArea::get_available_userlabels(); 
			}

			$buddypress_fields = array();

			if( is_plugin_active( 'buddypress/bp-loader.php' ) ){
				$profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true	) );

				if ( !empty( $profile_groups ) ) {
					 foreach ( $profile_groups as $profile_group ) {
						if ( !empty( $profile_group->fields ) ) {				
							foreach ( $profile_group->fields as $field ) {
								$buddypress_fields[] = $field->name;
							}
						}
					}
				}
			}

			$users_registered = array();
			$headers = array();
			$headers_filtered = array();	
			$update_existing_users = isset( $form_data["update_existing_users"] ) ? $form_data["update_existing_users"] : '';
			$role_default = isset( $form_data["role"] ) ? $form_data["role"] : '';
			$update_roles_existing_users = isset( $form_data["update_roles_existing_users"] ) ? $form_data["update_roles_existing_users"] : '';
			$empty_cell_action = isset( $form_data["empty_cell_action"] ) ? $form_data["empty_cell_action"] : '';

			if( $is_frontend ){
				$activate_users_wp_members = get_option( "acui_frontend_activate_users_wp_members" );
			}
			else{
				if( !isset( $form_data["activate_users_wp_members"] ) || empty( $form_data["activate_users_wp_members"] ) )
					$activate_users_wp_members = "no_activate";
				else
					$activate_users_wp_members = $form_data["activate_users_wp_members"];
			}
			
	
			if( $is_cron ){
				if( get_option( "acui_cron_allow_multiple_accounts" ) == "allowed" ){
					$allow_multiple_accounts = "allowed";						
				}
				else {
					$allow_multiple_accounts = "not_allowed";
				}
			}
			else {
				if( empty( $form_data["allow_multiple_accounts"] ) )
					$allow_multiple_accounts = "not_allowed";	
				else
					$allow_multiple_accounts = $form_data["allow_multiple_accounts"];
			}
	
			if( empty( $form_data["approve_users_new_user_appove"] ) )
				$approve_users_new_user_appove = "no_approve";
			else
				$approve_users_new_user_appove = $form_data["approve_users_new_user_appove"];

			// save mail sending preferences
			if( isset( $form_data["sends_email"] ) && $form_data["sends_email"] == 'yes' )
  				update_option( "acui_manually_send_mail", true );
  			else
				update_option( "acui_manually_send_mail", false );

			if( isset( $form_data["send_email_updated"] ) && $form_data["send_email_updated"] == 'yes' )
  				update_option( "acui_manually_send_mail_updated", true );
  			else
				update_option( "acui_manually_send_mail_updated", false );

			// disable WordPress default emails if this must be disabled
			if( !get_option('acui_automattic_wordpress_email') ){
				add_filter( 'send_email_change_email', 'acui_return_false', 999 );
				add_filter( 'send_password_change_email', 'acui_return_false', 999 );
			}

			// action
			echo "<h3>" . __('Ready to registers','import-users-from-csv-with-meta') . "</h3>";
			echo "<p>" . __('First row represents the form of sheet','import-users-from-csv-with-meta') . "</p>";
			$row = 0;
			$positions = array();

			ini_set('auto_detect_line_endings',TRUE);

			$delimiter = acui_detect_delimiter( $file );

			$manager = new SplFileObject( $file );
			while ( $data = $manager->fgetcsv( $delimiter ) ):
				if( empty($data[0]) )
					continue;

				if( count( $data ) == 1 )
					$data = $data[0];
				
				foreach ( $data as $key => $value ){
					$data[ $key ] = trim( $value );
				}

				for( $i = 0; $i < count($data); $i++ ){
					$data[$i] = acui_string_conversion( $data[$i] );

					if( is_serialized( $data[$i] ) ) // serialized
						$data[$i] = maybe_unserialize( $data[$i] );
					elseif( strpos( $data[$i], "::" ) !== false  ) // list of items
						$data[$i] = explode( "::", $data[$i] );
				}
				
				if( $row == 0 ):
					// check min columns username - email
					if(count( $data ) < 2){
						echo "<div id='message' class='error'>" . __( 'File must contain at least 2 columns: username and email', 'import-users-from-csv-with-meta' ) . "</div>";
						break;
					}

					$i = 0;
					$password_position = false;
					$id_position = false;
					
					foreach ( $acui_restricted_fields as $acui_restricted_field ) {
						$positions[ $acui_restricted_field ] = false;
					}

					foreach( $data as $element ){
						$headers[] = $element;

						if( in_array( strtolower( $element ) , $acui_restricted_fields ) )
							$positions[ strtolower( $element ) ] = $i;

						if( !in_array( strtolower( $element ), $acui_restricted_fields ) && !in_array( $element, $buddypress_fields ) )
							$headers_filtered[] = $element;

						$i++;
					}

					$columns = count( $data );

					update_option( "acui_columns", $headers_filtered );
					?>
					<h3><?php _e( 'Inserting and updating data', 'import-users-from-csv-with-meta' ); ?></h3>
					<table>
						<tr><th><?php _e( 'Row', 'import-users-from-csv-with-meta' ); ?></th><?php foreach( $headers as $element ) echo "<th>" . $element . "</th>"; ?></tr>
					<?php
					$row++;
				else:
					if( count( $data ) != $columns ): // if number of columns is not the same that columns in header
						echo '<script>alert("' . __( 'Row number', 'import-users-from-csv-with-meta' ) . " $row " . __( 'does not have the same columns than the header, we are going to skip', 'import-users-from-csv-with-meta') . '");</script>';
						continue;
					endif;

					do_action('pre_acui_import_single_user', $headers, $data );
					$data = apply_filters('pre_acui_import_single_user_data', $data, $headers);

					$username = $data[0];
					$email = $data[1];
					$user_id = 0;
					$problematic_row = false;
					$password_position = $positions["password"];
					$password = "";
					$role_position = $positions["role"];
					$role = "";
					$id_position = $positions["id"];
					
					if ( !empty( $id_position ) )
						$id = $data[ $id_position ];
					else
						$id = "";

					$created = true;

					if( $password_position === false )
						$password = wp_generate_password();
					else
						$password = $data[ $password_position ];					

					if( $role_position === false )
						$role = $role_default;
					else{
						$roles_cells = explode( ',', $data[ $role_position ] );
						array_walk( $roles_cells, 'trim' );
						$role = $roles_cells;
					}

					if( !empty( $id ) ){ // if user have used id
						if( acui_user_id_exists( $id ) ){
							if( $update_existing_users == 'no' ){
								continue;
							}

							// we check if username is the same than in row
							$user = get_user_by( 'ID', $id );

							if( $user->user_login == $username ){
								$user_id = $id;
								
								if( $password !== "" )
									wp_set_password( $password, $user_id );

								if( !empty( $email ) ) {
									$updateEmailArgs = array(
										'ID'         => $user_id,
										'user_email' => $email
									);
									wp_update_user( $updateEmailArgs );
								}

								$created = false;
							}
							else{
								echo '<script>alert("' . __( 'Problems with ID', 'import-users-from-csv-with-meta' ) . ": $id , " . __( 'username is not the same in the CSV and in database, we are going to skip.', 'import-users-from-csv-with-meta' ) . '");</script>';
								continue;
							}

						}
						else{
							$userdata = array(
								'ID'		  =>  $id,
							    'user_login'  =>  $username,
							    'user_email'  =>  $email,
							    'user_pass'   =>  $password
							);

							$user_id = wp_insert_user( $userdata );

							$created = true;
						}
					}
					elseif( !empty( $email ) && ( ( sanitize_email( $email ) == '' ) ) ){ // if email is invalid
						$problematic_row = true;
						$data[0] = __('Invalid EMail','import-users-from-csv-with-meta')." ($email)";
					}
					elseif( username_exists( $username ) ){ // if user exists, we take his ID by login, we will update his mail if it has changed
						if( $update_existing_users == 'no' ){
							continue;
						}

						$user_object = get_user_by( "login", $username );
						$user_id = $user_object->ID;

						if( $password !== "" )
							wp_set_password( $password, $user_id );

						if( !empty( $email ) ) {
							$updateEmailArgs = array(
								'ID'         => $user_id,
								'user_email' => $email
							);
							wp_update_user( $updateEmailArgs );
						}

						$created = false;
					}
					elseif( email_exists( $email ) && $allow_multiple_accounts == "not_allowed" ){ // if the email is registered, we take the user from this and we don't allow repeated emails
						if( $update_existing_users == 'no' ){
							continue;
						}

	                    $user_object = get_user_by( "email", $email );
	                    $user_id = $user_object->ID;
	                    
	                    $data[0] = __( 'User already exists as:', 'import-users-from-csv-with-meta' ) . $user_object->user_login . '<br/>' . __( '(in this CSV file is called:', 'import-users-from-csv-with-meta' ) . $username . ")";
	                    $problematic_row = true;

	                    if( $password !== "" )
	                        wp_set_password( $password, $user_id );

	                    $created = false;
					}
					elseif( email_exists( $email ) && $allow_multiple_accounts == "allowed" ){ // if the email is registered and repeated emails are allowed
						// if user is new, but the password in csv is empty, generate a password for this user
						if( $password === "" ) {
							$password = wp_generate_password();
						}
						
						$hacked_email = acui_hack_email( $email );
						$user_id = wp_create_user( $username, $password, $hacked_email );
						acui_hack_restore_remapped_email_address( $user_id, $email );
					}
					else{
						// if user is new, but the password in csv is empty, generate a password for this user
						if( $password === "" ) {
							$password = wp_generate_password();
						}
						
						$user_id = wp_create_user( $username, $password, $email );
					}
						
					if( is_wp_error( $user_id ) ){ // in case the user is generating errors after this checks
						$error_string = $user_id->get_error_message();
						echo '<script>alert("' . __( 'Problems with user:', 'import-users-from-csv-with-meta' ) . $username . __( ', we are going to skip. \r\nError: ', 'import-users-from-csv-with-meta') . $error_string . '");</script>';
						continue;
					}

					$users_registered[] = $user_id;
					$user_object = new WP_User( $user_id );

					if( $created || $update_roles_existing_users != 'no' ){
						if(!( in_array("administrator", acui_get_roles($user_id), FALSE) || is_multisite() && is_super_admin( $user_id ) )){
							
							if( $update_roles_existing_users == 'yes' || $created ){
								$default_roles = $user_object->roles;
								foreach ( $default_roles as $default_role ) {
									$user_object->remove_role( $default_role );
								}

								if( !empty( $role ) ){
									if( is_array( $role ) ){
										foreach ($role as $single_role) {
											$user_object->add_role( $single_role );
										}	
									}
									else{
										$user_object->add_role( $role );
									}
								}

								$invalid_roles = array();
								if( !empty( $role ) ){
									if( !is_array( $role ) ){
										$role_tmp = $role;
										$role = array();
										$role[] = $role_tmp;
									}
									
									foreach ($role as $single_role) {
										$single_role = strtolower($single_role);
										if( get_role( $single_role ) ){
											$user_object->add_role( $single_role );
										}
										else{
											$invalid_roles[] = trim( $single_role );
										}
									}	
								}

								if ( !empty( $invalid_roles ) ){
									$problematic_row = true;
									if( count( $invalid_roles ) == 1 )
										$data[0] = __('Invalid role','import-users-from-csv-with-meta').' (' . reset( $invalid_roles ) . ')';
									else
										$data[0] = __('Invalid roles','import-users-from-csv-with-meta').' (' . implode( ', ', $invalid_roles ) . ')';
								}
							}
						}
					}

					// Multisite add user to current blog
					if( is_multisite() ){
						if( !empty( $role ) ){
							if( is_array( $role ) ){
								foreach ($role as $single_role) {
									add_user_to_blog( get_current_blog_id(), $user_id, $single_role );
								}	
							}
							else{
								add_user_to_blog( get_current_blog_id(), $user_id, $role );
							}
						}
					}

					// WP Members activation
					if( $activate_users_wp_members == "activate" )
						update_user_meta( $user_id, "active", true );

					// New User Approve
					if( $approve_users_new_user_appove == "approve" )
						update_user_meta( $user_id, "pw_user_status", "approved" );
					else
						update_user_meta( $user_id, "pending", true );
						
					if( $columns > 2 ){
						for( $i = 2 ; $i < $columns; $i++ ):
							$data[$i] = apply_filters( 'pre_acui_import_single_user_single_data', $data[$i], $headers[$i], $i);

							if( !empty( $data ) ){
								if( strtolower( $headers[ $i ] ) == "password" ){ // passwords -> continue
									continue;
								}
								elseif( strtolower( $headers[ $i ] ) == "user_pass" ){ // hashed pass
									$wpdb->update( $wpdb->users, array( 'user_pass' => wp_slash( $data[ $i ] ) ), array( 'ID' => $user_id ) );
									wp_cache_delete( $user_id, 'users' );
							        continue;
								}
								elseif( in_array( $headers[ $i ], $wp_users_fields ) ){ // wp_user data									
									if( empty( $data[ $i ] ) && $empty_cell_action == "leave" ){
										continue;
									}
									else{
										wp_update_user( array( 'ID' => $user_id, $headers[ $i ] => $data[ $i ] ) );
										continue;
									}										
								}
								elseif( strtolower( $headers[ $i ] ) == "wp-access-areas" && is_plugin_active( 'wp-access-areas/wp-access-areas.php' ) ){ // wp-access-areas
									$active_labels = array_map( 'trim', explode( "#", $data[ $i ] ) );

									foreach( $wpaa_labels as $wpa_label ){
										if( in_array( $wpa_label->cap_title , $active_labels )){
											acui_set_cap_for_user( $wpa_label->capability , $user_object , true );
										}
										else{
											acui_set_cap_for_user( $wpa_label->capability , $user_object , false );
										}
									}

									continue;
								}
								elseif( ( $bpf_pos = array_search( $headers[ $i ], $buddypress_fields ) ) !== false ){ // buddypress
                                    switch( $buddypress_types[ $bpf_pos ] ){
                                        case 'datebox':
                                            $date = $data[$i];
                                            switch( true ){
                                                case is_numeric( $date ):
                                                    $UNIX_DATE = ($date - 25569) * 86400;
                                                    $datebox = gmdate("Y-m-d H:i:s", $UNIX_DATE);break;
                                                case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-]([4567890]{1}\d{1})/',$date,$match):
                                                    $match[3]='19'.$match[3];
                                                case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](20[4567890]{1}\d{1})/',$date,$match):
                                                case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](19[4567890]{1}\d{1})/',$date,$match):
                                                    $datebox= ($match[3].'-'.$match[2].'-'.$match[1]);
                                                    break;

                                                default:
                                                    $datebox = $date;
                                            }

                                            $datebox = strtotime( $datebox );
                                            xprofile_set_field_data( $headers[$i], $user_id, date( 'Y-m-d H:i:s', $datebox ) );
                                            unset( $datebox );
                                            break;
                                        default:
                                            xprofile_set_field_data( $headers[$i], $user_id, $data[$i] );
                                    }

									continue;
								}
								elseif( $headers[ $i ] == 'bp_group' ){ // buddypress group
									$groups = explode( ',', $data[ $i ] );
									$groups_role = explode( ',', $data[ $positions[ 'bp_group_role' ] ] );

								    for( $j = 0; $j < count( $groups ); $j++ ){
								    	$group_id = BP_Groups_Group::group_exists( $groups[ $j ] );

								    	if( !empty( $group_id ) ){
								    		groups_join_group( $group_id, $user_id );

								    		if( $groups_role[ $j ] == 'Moderator' ){
								    			groups_promote_member( $user_id, $group_id, 'mod' );
								    		}
								    		elseif( $groups_role[ $j ] == 'Administrator' ){
								    			groups_promote_member( $user_id, $group_id, 'admin' );
								    		}
								    	}
								    }
								    	
								    continue;							    
								}
								elseif( $headers[ $i ] == 'bp_group_role' ){
									continue;
								}
								else{ // wp_usermeta data
									
									if( $data[ $i ] === '' ){
										if( $empty_cell_action == "delete" )
											delete_user_meta( $user_id, $headers[ $i ] );
										else
											continue;	
									}
									else{
										update_user_meta( $user_id, $headers[ $i ], $data[ $i ] );
										continue;
									}
								}

							}
						endfor;
					}

					do_action('post_acui_import_single_user', $headers, $data, $user_id );

					$styles = "";
					if( $problematic_row )
						$styles = "background-color:red; color:white;";

					echo "<tr style='$styles' ><td>" . ($row - 1) . "</td>";
					foreach ($data as $element){
						if( is_array( $element ) )
							$element = implode ( ',' , $element );

						echo "<td>$element</td>";
					}

					echo "</tr>\n";

					flush();

					$mail_for_this_user = false;
					if( $is_cron ){
						if( get_option( "acui_cron_send_mail" ) ){
							if( $created || ( !$created && get_option( "acui_cron_send_mail_updated" ) ) ){
								$mail_for_this_user = true;
							}							
						}
					}
					elseif( $is_frontend ){
						if( get_option( "acui_frontend_send_mail" ) ){
							if( $created || ( !$created && get_option( "acui_frontend_send_mail_updated" ) ) ){
								$mail_for_this_user = true;
							}							
						}
					}
					else{
						if( isset( $form_data["sends_email"] ) && $form_data["sends_email"] ){
							if( $created || ( !$created && ( isset( $form_data["send_email_updated"] ) && $form_data["send_email_updated"] ) ) )
								$mail_for_this_user = true;
						}
					}
						
					// send mail
					if( isset( $mail_for_this_user ) && $mail_for_this_user ):
						$key = get_password_reset_key( $user_object );
						$user_login= $user_object->user_login;
						
						$body_mail = get_option( "acui_mail_body" );
						$subject = get_option( "acui_mail_subject" );
												
						$body_mail = str_replace( "**loginurl**", "<a href='" . home_url() . "/wp-login.php" . "'>" . home_url() . "/wp-login.php" . "</a>", $body_mail );
						$body_mail = str_replace( "**username**", $user_login, $body_mail );
						$body_mail = str_replace( "**lostpasswordurl**", wp_lostpassword_url(), $body_mail );
						
						if( !is_wp_error( $key ) )
							$body_mail = str_replace( "**passwordreseturl**", network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ), 'login' ), $body_mail );
						
						if( empty( $password ) && !$created ) 
							$password = __( 'Password has not been changed', 'import-users-from-csv-with-meta' );

						$body_mail = str_replace("**password**", $password, $body_mail);
						$body_mail = str_replace("**email**", $email, $body_mail);

						foreach ( $wp_users_fields as $wp_users_field ) {								
							if( $positions[ $wp_users_field ] != false && $wp_users_field != "password" ){
								$body_mail = str_replace("**" . $wp_users_field .  "**", $data[ $positions[ $wp_users_field ] ] , $body_mail);							
							}
						}

						for( $i = 0 ; $i < count( $headers ); $i++ ) {
							$body_mail = str_replace("**" . $headers[ $i ] .  "**", $data[ $i ] , $body_mail);							
						}

						$body_mail = wpautop( $body_mail );

						add_filter( 'wp_mail_content_type', 'cod_set_html_content_type' );

						if( get_option( "acui_settings" ) == "plugin" ){
							add_action( 'phpmailer_init', 'acui_mailer_init' );
							add_filter( 'wp_mail_from', 'acui_mail_from' );
							add_filter( 'wp_mail_from_name', 'acui_mail_from_name' );
							
							wp_mail( $email, $subject, $body_mail );

							remove_filter( 'wp_mail_from', 'acui_mail_from' );
							remove_filter( 'wp_mail_from_name', 'acui_mail_from_name' );
							remove_action( 'phpmailer_init', 'acui_mailer_init' );
						}
						else
							wp_mail( $email, $subject, $body_mail );

						remove_filter( 'wp_mail_content_type', 'cod_set_html_content_type' );						
					endif;

				endif;

				$row++;						
			endwhile;

			// let the filter of default WordPress emails as it were before deactivating them
			if( !get_option('acui_automattic_wordpress_email') ){
				remove_filter( 'send_email_change_email', 'acui_return_false', 999 );
				remove_filter( 'send_password_change_email', 'acui_return_false', 999 );
			}


			if( $attach_id != 0 )
				wp_delete_attachment( $attach_id );

			// delete all users that have not been imported
			if( $is_cron && get_option( "acui_cron_delete_users" ) ):
				require_once( ABSPATH . 'wp-admin/includes/user.php');	

				$all_users = get_users( array( 
					'fields' => array( 'ID' ),
					'role__not_in' => array( 'administrator' )
				) );
				$cron_delete_users_assign_posts = get_option( "acui_cron_delete_users_assign_posts");

				foreach ( $all_users as $user ) {
					if( !in_array( $user->ID, $users_registered ) ){
						if( !empty( $cron_delete_users_assign_posts ) && get_userdata( $cron_delete_users_assign_posts ) !== false ){
							wp_delete_user( $user->ID, $cron_delete_users_assign_posts );
						}
						else{
							wp_delete_user( $user->ID );
						}						
					}
				}
			endif;

			?>
			</table>

			<?php if( !$is_frontend ): ?>
				<br/>
				<p><?php _e( 'Process finished you can go', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo get_admin_url( null, 'users.php' ); ?>"><?php _e( 'here to see results', 'import-users-from-csv-with-meta' ); ?></a></p>
			<?php endif; ?>
			
			<?php
			ini_set('auto_detect_line_endings',FALSE);
			
			do_action( 'after_acui_import_users' );
		?>
	</div>
<?php
}

function acui_options(){
	global $url_plugin;

	if ( !current_user_can('create_users') ) {
		wp_die( __( 'You are not allowed to see this content.', 'import-users-from-csv-with-meta' ));
	}

	if ( isset ( $_GET['tab'] ) ) 
		$tab = $_GET['tab'];
   	else 
   		$tab = 'homepage';


	if( isset( $_POST ) && !empty( $_POST ) ):
		switch ( $tab ){
      		case 'homepage':
      			acui_fileupload_process( $_POST, false );
      			return;
      		break;

      		case 'frontend':
      			acui_manage_frontend_process( $_POST );
      		break;

      		case 'columns':
      			acui_manage_extra_profile_fields( $_POST );
      		break;

      		case 'mail-options':
      			acui_save_mail_template( $_POST );
      		break;

      		case 'cron':
      			acui_manage_cron_process( $_POST );
      		break;

      	}
      	
	endif;
	
	if ( isset ( $_GET['tab'] ) ) 
		acui_admin_tabs( $_GET['tab'] ); 
	else
		acui_admin_tabs('homepage');
	
  	switch ( $tab ){
      case 'homepage' :
		
	$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
	$old_csv_files = new WP_Query( $args_old_csv );

	acui_check_options();
?>
	<div class="wrap acui">	

		<?php if( $old_csv_files->found_posts > 0 ): ?>
		<div class="postbox">
		    <div title="<?php _e( 'Click to open/close', 'import-users-from-csv-with-meta' ); ?>" class="handlediv">
		      <br>
		    </div>

		    <h3 class="hndle"><span>&nbsp;&nbsp;&nbsp;<?php _e( 'Old CSV files uploaded', 'import-users-from-csv-with-meta' ); ?></span></h3>

		    <div class="inside" style="display: block;">
		    	<p><?php _e( 'For security reasons you should delete this files, probably they would be visible in the Internet if a bot or someone discover the URL. You can delete each file or maybe you want delete all CSV files you have uploaded:', 'import-users-from-csv-with-meta' ); ?></p>
		    	<input type="button" value="<?php _e( 'Delete all CSV files uploaded', 'import-users-from-csv-with-meta' ); ?>" id="bulk_delete_attachment" style="float:right;" />
		    	<ul>
		    		<?php while($old_csv_files->have_posts()) : 
		    			$old_csv_files->the_post(); 

		    			if( get_the_date() == "" )
		    				$date = "undefined";
		    			else
		    				$date = get_the_date();
		    		?>
		    		<li><a href="<?php echo wp_get_attachment_url( get_the_ID() ); ?>"><?php the_title(); ?></a> <?php _e( 'uploaded on', 'import-users-from-csv-with-meta' ) . ' ' . $date; ?> <input type="button" value="<?php _e( 'Delete', 'import-users-from-csv-with-meta' ); ?>" class="delete_attachment" attach_id="<?php the_ID(); ?>" /></li>
		    		<?php endwhile; ?>
		    		<?php wp_reset_postdata(); ?>
		    	</ul>
		        <div style="clear:both;"></div>
		    </div>
		</div>
		<?php endif; ?>	

		<div id='message' class='updated'><?php _e( 'File must contain at least <strong>2 columns: username and email</strong>. These should be the first two columns and it should be placed <strong>in this order: username and email</strong>. If there are more columns, this plugin will manage it automatically.', 'import-users-from-csv-with-meta' ); ?></div>
		<div id='message-password' class='error'><?php _e( 'Please, read carefully how <strong>passwords are managed</strong> and also take note about capitalization, this plugin is <strong>case sensitive</strong>.', 'import-users-from-csv-with-meta' ); ?></div>

		<div>
			<h2><?php _e( 'Import users from CSV','import-users-from-csv-with-meta' ); ?></h2>
		</div>

		<div style="clear:both;"></div>

		<div class="main_bar">
			<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" onsubmit="return check();">
			<table class="form-table">
				<tbody>
				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Update existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="update_existing_users">
							<option value="yes"><?php _e( 'Yes', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="no"><?php _e( 'No', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>

				<tr class="form-field">
					<th scope="row"><label for="role"><?php _e( 'Default role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
					<?php 
						$list_roles = acui_get_editable_roles(); 
						
						foreach ($list_roles as $key => $value) {
							if($key == "subscriber")
								echo "<label style='margin-right:5px;'><input name='role[]' type='checkbox' checked='checked' value='$key'/>$value</label>";
							else
								echo "<label style='margin-right:5px;'><input name='role[]' type='checkbox' value='$key'/>$value</label>";
						}
					?>

					<p class="description"><?php _e( 'You can also import roles from a CSV column. Please read documentation tab to see how it can be done. If you choose more than one role, the roles would be assigned correctly but you should use some plugin like <a href="https://wordpress.org/plugins/user-role-editor/">User Role Editor</a> to manage them.', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Update roles for existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="update_roles_existing_users">
							<option value="no"><?php _e( 'No', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="yes"><?php _e( 'Yes, update and override existing roles', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="yes_no_override"><?php _e( 'Yes, add new roles and not override existing ones', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'CSV file <span class="description">(required)</span></label>', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<div id="upload_file">
							<input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" />
							<?php _e( '<em>or you can choose directly a file from your host,', 'import-users-from-csv-with-meta' ) ?> <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'import-users-from-csv-with-meta' ) ?></a>.</em>
						</div>
						<div id="introduce_path" style="display:none;">
							<input placeholder="<?php _e( 'You have to introduce the path to file, i.e.:' ,'import-users-from-csv-with-meta' ); ?><?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv" type="text" name="path_to_file" id="path_to_file" value="<?php echo dirname( __FILE__ ); ?>/test.csv" style="width:70%;" />
							<em><?php _e( 'or you can upload it directly from your PC', 'import-users-from-csv-with-meta' ); ?>, <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'import-users-from-csv-with-meta' ); ?></a>.</em>
						</div>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'What should the plugin do with empty cells?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="empty_cell_action">
							<option value="leave"><?php _e( 'Leave the old value for this metadata', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="delete"><?php _e( 'Delete the metadata', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>

				<?php if( is_plugin_active( 'buddypress/bp-loader.php' ) ):

					if( !class_exists( "BP_XProfile_Group" ) ){
						require_once( WP_PLUGIN_DIR . "/buddypress/bp-xprofile/classes/class-bp-xprofile-group.php" );
					}

					$buddypress_fields = array();
					$buddypress_types=array();
					$profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true	) );

					if ( !empty( $profile_groups ) ) {
						 foreach ( $profile_groups as $profile_group ) {
							if ( !empty( $profile_group->fields ) ) {				
								foreach ( $profile_group->fields as $field ) {
									$buddypress_fields[] = $field->name;
									$buddypress_types[] = $field->type;
								}
							}
						}
					}
				?>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'BuddyPress users', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can insert any profile from BuddyPress using his name as header. Plugin will check, before import, which fields are defined in BuddyPress and will assign it in the update. You can use this fields:', 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none;margin-left:2em;">
						<?php foreach ($buddypress_fields as $buddypress_field ): ?><li><?php echo $buddypress_field; ?></li><?php endforeach; ?>
					</ul>
					<?php _e( 'Remember that all date fields have to be imported using a format like this: 2016-01-01 00:00:00', 'import-users-from-csv-with-meta' ); ?>

					<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/buddypress/">BuddyPress</a> <?php _e( 'users', 'import-users-from-csv-with-meta' ); ?></strong>.)</p>
					</td>					
				</tr>

				<?php endif; ?>

				<?php if( is_plugin_active( 'wp-members/wp-members.php' ) ): ?>

				<tr class="form-field form-required">
					<th scope="row"><label>Activate user when they are being imported?</label></th>
					<td>
						<select name="activate_users_wp_members">
							<option value="no_activate"><?php _e( 'Do not activate users', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="activate"><?php _e( 'Activate users when they are being imported', 'import-users-from-csv-with-meta' ); ?></option>
						</select>

						<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/wp-members/"><?php _e( 'WP Members', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta' ); ?>)</strong>.</p>
					</td>
					
				</tr>

				<?php endif; ?>

				<?php if( is_plugin_active( 'new-user-approve/new-user-approve.php' ) ): ?>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Approve users at the same time is being created', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="approve_users_new_user_appove">
							<option value="no_approve"><?php _e( 'Do not approve users', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="approve"><?php _e( 'Approve users when they are being imported', 'import-users-from-csv-with-meta' ); ?></option>
						</select>

						<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://es.wordpress.org/plugins/new-user-approve/"><?php _e( 'New User Approve', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta' ); ?></strong>.</p>
					</td>
					
				</tr>

				<?php endif; ?>

				<?php if( is_plugin_active( 'allow-multiple-accounts/allow-multiple-accounts.php' ) ): ?>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Repeated email in different users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="allow_multiple_accounts">
							<option value="not_allowed"><?php _e( 'Not allowed', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="allowed"><?php _e( 'Allowed', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
						<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/allow-multiple-accounts/"><?php _e( 'Allow Multiple Accounts', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta'); ?>)</strong>. <?php _e('Allow multiple user accounts to be created having the same email address.','import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<?php endif; ?>

				<?php if( is_plugin_active( 'wp-access-areas/wp-access-areas.php' ) ): ?>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e('WordPress Access Areas is activated','import-users-from-csv-with-meta'); ?></label></th>
					<td>
						<p class="description"><?php _e('As user of','import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/wp-access-areas/"><?php _e( 'WordPress Access Areas', 'import-users-from-csv-with-meta' )?></a> <?php _e( 'you can use the Access Areas created', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo admin_url( 'users.php?page=user_labels' ); ?>"><?php _e( 'here', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'and use this areas in your own CSV file. Please use the column name <strong>wp-access-areas</strong> and in each row use <strong>the name that you have used', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo admin_url( 'users.php?page=user_labels' ); ?>"><?php _e( 'here', 'import-users-from-csv-with-meta' ); ?></a></strong><?php _e( ', like this ones:', 'import-users-from-csv-with-meta' ); ?></p>
						<ol>
							<?php 
								$data = WPAA_AccessArea::get_available_userlabels( '0,5' , NULL ); 								
								foreach ( $data as $access_area_object ): ?>
									<li><?php echo $access_area_object->cap_title; ?></li>
							<?php endforeach; ?>

						</ol>
						<p class="description"><?php _e( "If you leave this cell empty for some user or the access area indicated doesn't exist, user won't be assigned to any access area. You can choose more than one area for each user using pads between them in the same row, i.e.: ", 'import-users-from-csv-with-meta' ) ?>access_area1#accces_area2</p>
					</td>
				</tr>

				<?php endif; ?>

				<tr class="form-field">
					<th scope="row"><label for="user_login"><?php _e( 'Send mail', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<p>
							<?php _e( 'Do you wish to send a mail with credentials and other data?', 'import-users-from-csv-with-meta' ); ?> 
							<input type="checkbox" name="sends_email" value="yes" <?php if( get_option( 'acui_manually_send_mail' ) ): ?> checked="checked" <?php endif; ?>>
						</p>
						<p>
							<?php _e( 'Do you wish to send this mail also to users that are being updated? (not only to the one which are being created)', 'import-users-from-csv-with-meta' ); ?>
							<input type="checkbox" name="send_email_updated" value="yes" <?php if( get_option( 'acui_manually_send_mail_updated' ) ): ?> checked="checked" <?php endif; ?>>
						</p>
					</td>
				</tr>
				</tbody>
			</table>

			<?php wp_nonce_field( 'acui-import', 'acui-nonce' ); ?>

			<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php _e( 'Start importing', 'import-users-from-csv-with-meta' ); ?>"/>
			</form>
		</div>

		<div class="sidebar">
			<div class="sidebar_section become_patreon">
		    	<a class="patreon" color="primary" type="button" name="become-a-patron" data-tag="become-patron-button" href="https://www.patreon.com/bePatron?c=1741454" role="button">
		    		<div><span><?php _e( 'Become a patron', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

		    <?php if( substr( get_locale(), 0, 2 ) == 'es' ): ?>
			<div class="sidebar_section" id="webempresa">
				<h3>Tu web más rápida con Webempresa</h3>
				<ul>
					<li><label>Además ahora un <a href="https://codection.com/25-de-descuento-en-el-mejor-hosting-en-espanol-con-webempresa/">25% de descuento</a>.</label></li>
				</ul>
				<a href="https://codection.com/25-de-descuento-en-el-mejor-hosting-en-espanol-con-webempresa/" target="_blank">
					<img src="<?php echo plugin_dir_url( __FILE__ ); ?>assets/webempresa_logo.png">
				</a>
			</div>
			<?php else: ?>
			<div class="sidebar_section" style="padding:0 !important;border:none !important;background:none !important;">
				<a href="https://codection.com/how-to-transfer-your-website-to-inmotion-hosting/" target="_blank">
					<img src="<?php echo plugin_dir_url( __FILE__ ); ?>assets/codection-inmotion.png">
				</a>
			</div>
			<?php endif; ?>
			
			<div class="sidebar_section" id="vote_us">
				<h3>Rate Us</h3>
				<ul>
					<li><label>If you like it, Please vote and support us.</label></li>
				</ul>
			</div>
			<div class="sidebar_section">
				<h3>Having Issues?</h3>
				<ul>
					<li><label>You can create a ticket</label> <a target="_blank" href="http://wordpress.org/support/plugin/import-users-from-csv-with-meta"><label>WordPress support forum</label></a></li>
					<li><label>You can ask for premium support</label> <a target="_blank" href="mailto:contacto@codection.com"><label>contacto@codection.com</label></a></li>
				</ul>
			</div>
			<div class="sidebar_section">
				<h3>Donate</h3>
				<ul>
					<li><label>If you appreciate our work and you want to help us to continue developing it and giving the best support</label> <a target="_blank" href="https://paypal.me/imalrod"><label>donate</label></a></li>
				</ul>
			</div>
		</div>

	</div>
	<script type="text/javascript">
	function check(){
		if(document.getElementById("uploadfiles").value == "" && jQuery( "#upload_file" ).is(":visible") ) {
		   alert("<?php _e( 'Please choose a file', 'import-users-from-csv-with-meta' ); ?>");
		   return false;
		}

		if( jQuery( "#path_to_file" ).val() == "" && jQuery( "#introduce_path" ).is(":visible") ) {
		   alert("<?php _e( 'Please enter a path to the file', 'import-users-from-csv-with-meta' ); ?>");
		   return false;
		}

		if( jQuery("[name=role\\[\\]]input:checkbox:checked").length == 0 ){
			alert("<?php _e( 'Please select a role', 'import-users-from-csv-with-meta'); ?>");
		   	return false;	
		}
	}

	jQuery( document ).ready( function( $ ){
		$( ".delete_attachment" ).click( function(){
			var answer = confirm( "<?php _e( 'Are you sure to delete this file?', 'import-users-from-csv-with-meta' ); ?>" );
			if( answer ){
				var data = {
					'action': 'acui_delete_attachment',
					'attach_id': $( this ).attr( "attach_id" )
				};

				$.post(ajaxurl, data, function(response) {
					if( response != 1 )
						alert( "<?php _e( 'There were problems deleting the file, please check file permissions', 'import-users-from-csv-with-meta' ); ?>" );
					else{
						alert( "<?php _e( 'File successfully deleted', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			}
		});

		$( "#bulk_delete_attachment" ).click( function(){
			var answer = confirm( "<?php _e( 'Are you sure to delete ALL CSV files uploaded? There can be CSV files from other plugins.', 'import-users-from-csv-with-meta' ); ?>" );
			if( answer ){
				var data = {
					'action': 'acui_bulk_delete_attachment',
				};

				$.post(ajaxurl, data, function(response) {
					if( response != 1 )
						alert( "<?php _e( 'There were problems deleting the files, please check files permissions', 'import-users-from-csv-with-meta' ); ?>" );
					else{
						alert( "<?php _e( 'Files successfully deleted', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			}
		});

		$( ".toggle_upload_path" ).click( function( e ){
			e.preventDefault();

			$("#upload_file,#introduce_path").toggle();
		} );

		$("#vote_us").click(function(){
			var win=window.open("http://wordpress.org/support/view/plugin-reviews/import-users-from-csv-with-meta?free-counter?rate=5#postform", '_blank');
			win.focus();
		});

	} );
	</script>

	<?php 

	break;

	case 'frontend':

	$send_mail_frontend = get_option( "acui_frontend_send_mail");
	$send_mail_updated_frontend = get_option( "acui_frontend_send_mail_updated");
	$role = get_option( "acui_frontend_role");
	$activate_users_wp_members = get_option( "acui_frontend_activate_users_wp_members");

	if( empty( $send_mail_frontend ) )
		$send_mail_frontend = false;

	if( empty( $send_mail_updated_frontend ) )
		$send_mail_updated_frontend = false;
	?>
		<h3><?php _e( "Execute an import of users in the frontend", 'import-users-from-csv-with-meta' ); ?></h3>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
			<table class="form-table">
				<tbody>
				<tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Use this shortcode in any page or post', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<pre>[import-users-from-csv-with-meta]</pre>
						<input class="button-primary" type="button" id="copy_to_clipboard" value="<?php _e( 'Copy to clipboard', 'import-users-from-csv-with-meta'); ?>"/>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="send-mail-frontend"><?php _e( 'Send mail when using frontend import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="send-mail-frontend" value="yes" <?php if( $send_mail_frontend == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="send-mail-updated-frontend"><?php _e( 'Send mail also to users that are being updated?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="send-mail-updated-frontend" value="yes" <?php if( $send_mail_updated_frontend == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="role"><?php _e( 'Role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select id="role-frontend" name="role-frontend">
							<?php 
								if( $role == '' )
									echo "<option selected='selected' value=''>" . __( 'Disable role assignement in frontend import', 'import-users-from-csv-with-meta' )  . "</option>";
								else
									echo "<option value=''>" . __( 'Disable role assignement in frontend import', 'import-users-from-csv-with-meta' )  . "</option>";

								$list_roles = acui_get_editable_roles();								
								foreach ($list_roles as $key => $value) {
									if($key == $role)
										echo "<option selected='selected' value='$key'>$value</option>";
									else
										echo "<option value='$key'>$value</option>";
								}
							?>
						</select>
						<p class="description"><?php _e( 'Which role would be used to import users?', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<?php if( is_plugin_active( 'wp-members/wp-members.php' ) ): ?>

				<tr class="form-field form-required">
					<th scope="row"><label>Activate user when they are being imported?</label></th>
					<td>
						<select name="activate_users_wp_members">
							<option value="no_activate" <?php selected( $activate_users_wp_members,'no_activate', true ); ?>><?php _e( 'Do not activate users', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="activate"  <?php selected( $activate_users_wp_members,'activate', true ); ?>><?php _e( 'Activate users when they are being imported', 'import-users-from-csv-with-meta' ); ?></option>
						</select>

						<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/wp-members/"><?php _e( 'WP Members', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta' ); ?>)</strong>.</p>
					</td>
				</tr>

				<?php endif; ?>
				</tbody>
			</table>
			<input class="button-primary" type="submit" value="<?php _e( 'Save frontend import options', 'import-users-from-csv-with-meta'); ?>"/>
		</form>

		<script>
		jQuery( document ).ready( function( $ ){
			$( '#copy_to_clipboard' ).click( function(){
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val( '[import-users-from-csv-with-meta]' ).select();
				document.execCommand("copy");
				$temp.remove();
			} );
		});
		</script>
	<?php break;

	case 'columns':
		$show_profile_fields = get_option( "acui_show_profile_fields");
		$headers = get_option("acui_columns"); 
	?>

		<h3><?php _e( 'Extra profile fields', 'import-users-from-csv-with-meta' ); ?></h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Show fields in profile?', 'import-users-from-csv-with-meta' ); ?></th>
				<td>
					<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
						<input type="checkbox" name="show-profile-fields" value="yes" <?php if( $show_profile_fields == true ) echo "checked='checked'"; ?>>
						<input type="hidden" name="show-profile-fields-action" value="update"/>
						<input class="button-primary" type="submit" value="<?php _e( 'Save option', 'import-users-from-csv-with-meta'); ?>"/>
					</form>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Extra profile fields loadad in previous files', 'import-users-from-csv-with-meta' ); ?></th>
				<td><small><em><?php _e( '(if you load another CSV with different columns, the new ones will replace this list)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<ol>
						<?php 
						if( is_array( $headers ) && count( $headers ) > 0 ):
							foreach ($headers as $column): ?>
							<li><?php echo $column; ?></li>
						<?php endforeach;  ?>
						
						<?php else: ?>
							<li><?php _e( 'There is no columns loaded yet', 'import-users-from-csv-with-meta' ); ?></li>
						<?php endif; ?>
					</ol>
				</td>
			</tr>
		</tbody></table>

		<?php 

		break;

		case 'doc':

		?>

		<h3><?php _e( 'Documentation', 'import-users-from-csv-with-meta' ); ?></h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Columns position', 'import-users-from-csv-with-meta' ); ?></th>
				<td><small><em><?php _e( '(Documents should look like the one presented into screenshot. Remember you should fill the first two columns with the next values)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<ol>
						<li><?php _e( 'Username', 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( 'Email', 'import-users-from-csv-with-meta' ); ?></li>
					</ol>						
					<small><em><?php _e( '(The next columns are totally customizable and you can use whatever you want. All rows must contains same columns)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<small><em><?php _e( '(User profile will be adapted to the kind of data you have selected)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<small><em><?php _e( '(If you want to disable the extra profile information, please deactivate this plugin after make the import)', 'import-users-from-csv-with-meta' ); ?></em></small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'id', 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( 'You can use a column called id in order to make inserts or updates of an user using the ID used by WordPress in the wp_users table. We have two different cases:', 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "If id <strong>doesn't exist in your users table</strong>: WordPress core does not allow us insert it, so it will throw an error of kind: invalid_user_id", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If id <strong>exists</strong>: plugin check if username is the same, if yes, it will update the data, if not, it ignores the cell to avoid problems", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Passwords", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "A string that contains user passwords. We have different options for this case:", 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "If you <strong>don't create a column for passwords</strong>: passwords will be generated automatically", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If you <strong>create a column for passwords</strong>: if cell is empty, password won't be updated; if cell has a value, it will be used", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Roles", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can import roles from the CSV. This is how it works:", 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "If you <strong>don't create a column for roles</strong>: roles would be chosen from the 'Default role' field in import screen.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If you <strong>create a column called 'role'</strong>: if cell is empty, roles would be chosen from 'Default role' field in import screen; if cell has a value, it will be used as role, if this role doesn't exist the default one would be used", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "Multiple roles can be imported creating <strong>a list of roles</strong> using commas to separate values.", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Serialized data", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can now import serialized data. You have to use the serialized string directly in the CSV cell in order the plugin will be able to understand it as an serialized data instead as any other string.", 'import-users-from-csv-with-meta' ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Lists", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can now import lists an array. Use this separator:", 'import-users-from-csv-with-meta'); ?> <strong>::</strong> <?php _e("two colons, inside the cell in order to split the string in a list of items.", 'import-users-from-csv-with-meta' ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'WordPress default profile data', 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "You can use those labels if you want to set data adapted to the WordPress default user columns (the ones who use the function", 'import-users-from-csv-with-meta' ); ?> <a href="http://codex.wordpress.org/Function_Reference/wp_update_user">wp_update_user</a>)
					<ol>
						<li><strong>user_nicename</strong>: <?php _e( "A string that contains a URL-friendly name for the user. The default is the user's username.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>user_url</strong>: <?php _e( "A string containing the user's URL for the user's web site.", 'import-users-from-csv-with-meta' ); ?>	</li>
						<li><strong>display_name</strong>: <?php _e( "A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through obscurity (that is if you don't use and delete the default admin user).", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>nickname</strong>: <?php _e( "The user's nickname, defaults to the user's username.", 'import-users-from-csv-with-meta' ); ?>	</li>
						<li><strong>first_name</strong>: <?php _e( "The user's first name.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>last_name</strong>: <?php _e("The user's last name.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>description</strong>: <?php _e("A string containing content about the user.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>jabber</strong>: <?php _e("User's Jabber account.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>aim</strong>: <?php _e("User's AOL IM account.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>yim</strong>: <?php _e("User's Yahoo IM account.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>user_registered</strong>: <?php _e( "Using the WordPress format for this kind of data Y-m-d H:i:s.", "import-users-from-csv-with-meta "); ?></li>
					</ol>
				</td>
			</tr>

			<?php if( is_plugin_active( 'woocommerce/woocommerce.php' ) ): ?>

				<tr valign="top">
					<th scope="row"><?php _e( "WooCommerce is activated", 'import-users-from-csv-with-meta' ); ?></th>
					<td><?php _e( "You can use those labels if you want to set data adapted to the WooCommerce default user columns", 'import-users-from-csv-with-meta' ); ?>
					<ol>
						<li>billing_first_name</li>
						<li>billing_last_name</li>
						<li>billing_company</li>
						<li>billing_address_1</li>
						<li>billing_address_2</li>
						<li>billing_city</li>
						<li>billing_postcode</li>
						<li>billing_country</li>
						<li>billing_state</li>
						<li>billing_phone</li>
						<li>billing_email</li>
						<li>shipping_first_name</li>
						<li>shipping_last_name</li>
						<li>shipping_company</li>
						<li>shipping_address_1</li>
						<li>shipping_address_2</li>
						<li>shipping_city</li>
						<li>shipping_postcode</li>
						<li>shipping_country</li>
						<li>shipping_state</li>
					</ol>
				</td>
				</tr>
			<?php endif; ?>

			<?php if( is_plugin_active( 'buddypress/bp-loader.php' ) ): ?>

				<tr valign="top">
					<th scope="row"><?php _e( "BuddyPress is activated", 'import-users-from-csv-with-meta' ); ?></th>
					<td><?php _e( "You can use the <strong>profile fields</strong> you have created and also you can set one or more groups for each user. For example:", 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "If you want to assign an user to a group you have to create a column 'bp_group' and a column 'bp_group_role'", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "Then in each cell you have to fill with the BuddyPress <strong>group slug</strong>", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "And the role assigned in this group: <em>Administrator, Moderator or Member</em>", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "You can do it with multiple groups at the same time using commas to separate different groups, in bp_group column, i.e.: <em>group_1, group_2, group_3</em>", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "But you will have to assign a role for each group: <em>Moderator,Moderator,Member,Member</em>", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If you get some error of this kind:", 'import-users-from-csv-with-meta' ); ?> <code>Fatal error: Class 'BP_XProfile_Group'</code> <?php _e( "please enable Buddypress Extended Profile then import the csv file. You can then disable this afterwards", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
				</td>
				</tr>

			<?php endif; ?>

			<?php do_action( 'acui_documentation_after_plugins_activated' ); ?>

			<tr valign="top">
				<th scope="row"><?php _e( "Important notice", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "You can upload as many files as you want, but all must have the same columns. If you upload another file, the columns will change to the form of last file uploaded.", 'import-users-from-csv-with-meta' ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Any question about it", 'import-users-from-csv-with-meta' ); ?></th>
				<td>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( 'Free support (in WordPress forums):', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/support/plugin/import-users-from-csv-with-meta">https://wordpress.org/support/plugin/import-users-from-csv-with-meta</a>.</li>
						<li><?php _e( 'Premium support (with a quote):', 'import-users-from-csv-with-meta' ); ?> <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Example', 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( 'Download this', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo plugins_url() . "/import-users-from-csv-with-meta/test.csv"; ?>">.csv <?php _e('file','import-users-from-csv-with-meta'); ?></a> <?php _e( 'to test', 'import-users-from-csv-with-meta' ); ?></td>
			</tr>
		</tbody>
		</table>
		<br/>
		<div style="width:775px;margin:0 auto"><img src="<?php echo plugins_url() . "/import-users-from-csv-with-meta/csv_example.png"; ?>"/></div>
	<?php break; ?>

	<?php case 'mail-options':
		$from_email = get_option( "acui_mail_from" );
		$from_name = get_option( "acui_mail_from_name" );
		$body_mail = get_option( "acui_mail_body" );
		$subject_mail = get_option( "acui_mail_subject" );
		$automattic_wordpress_email = get_option( "acui_automattic_wordpress_email" );
	?>
		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
		<h3><?php _e('Mail options','import-users-from-csv-with-meta'); ?></h3>

		<p class="description"><?php _e( 'You can set your own SMTP and other mail details', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo admin_url( 'tools.php?page=acui-smtp' ); ?>" target="_blank"><?php _e( 'here', 'import-users-from-csv-with-meta' ); ?></a>.
		
		<table class="optiontable form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'WordPress automatic emails users updated', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e( 'Send automattic WordPress emails?', 'import-users-from-csv-with-meta' ); ?></span>
							</legend>
							<label for="automattic_wordpress_email">
								<select name="automattic_wordpress_email" id="automattic_wordpress_email">
									<option <?php if( $automattic_wordpress_email == 'false' ) echo "selected='selected'"; ?> value="false"><?php _e( "Deactivate WordPress automattic email when an user is updated or his password is changed", 'import-users-from-csv-with-meta' ) ;?></option>
									<option <?php if( $automattic_wordpress_email == 'true' ) echo "selected='selected'"; ?> value="true"><?php _e( 'Activate WordPress automattic email when an user is updated or his password is changed', 'import-users-from-csv-with-meta' ); ?></option>
								</select>
								<span class="description"><? _e( "When you update an user or change his password, WordPress prepare and send automattic email, you can deactivate it here.", 'import-users-from-csv-with-meta' ); ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>	

		<h3><?php _e( 'Customize the email that can be sent when importing users', 'import-users-from-csv-with-meta' ); ?></h3>
		
		<p><?php _e( 'Mail subject :', 'import-users-from-csv-with-meta' ); ?><input name="subject_mail" size="100" value="<?php echo $subject_mail; ?>" id="title" autocomplete="off" type="text"></p>
		<?php wp_editor( $body_mail , 'body_mail'); ?>

		<br/>
		<input class="button-primary" type="submit" value="Save mail template"/>
		
		<p>You can use:</p>
		<ul style="list-style-type:disc; margin-left:2em;">
			<li>**username** = <?php _e( 'username to login', 'import-users-from-csv-with-meta' ); ?></li>
			<li>**password** = <?php _e( 'user password', 'import-users-from-csv-with-meta' ); ?></li>
			<li>**loginurl** = <?php _e( 'current site login url', 'import-users-from-csv-with-meta' ); ?></li>
			<li>**lostpasswordurl** = <?php _e( 'lost password url', 'import-users-from-csv-with-meta' ); ?></li>
			<li>**passwordreseturl** = <?php _e( 'password reset url', 'import-users-from-csv-with-meta' ); ?></li>
			<li>**email** = <?php _e( 'user email', 'import-users-from-csv-with-meta' ); ?></li>
			<li><?php _e( "You can also use any WordPress user standard field or an own metadata, if you have used it in your CSV. For example, if you have a first_name column, you could use **first_name** or any other meta_data like **my_custom_meta**", 'import-users-from-csv-with-meta' ) ;?></li>
		</ul>

		</form>

	<?php break; ?>

	<?php case 'smtp-settings': ?>

	<?php acui_smtp(); ?>

	<?php break; ?>

	<?php case 'cron':

	$cron_activated = get_option( "acui_cron_activated");
	$send_mail_cron = get_option( "acui_cron_send_mail");
	$send_mail_updated = get_option( "acui_cron_send_mail_updated");
	$cron_delete_users = get_option( "acui_cron_delete_users");
	$cron_delete_users_assign_posts = get_option( "acui_cron_delete_users_assign_posts");
	$path_to_file = get_option( "acui_cron_path_to_file");
	$period = get_option( "acui_cron_period");
	$role = get_option( "acui_cron_role");
	$update_roles_existing_users = get_option( "acui_cron_update_roles_existing_users");
	$move_file_cron = get_option( "acui_move_file_cron");
	$path_to_move = get_option( "acui_cron_path_to_move");
	$path_to_move_auto_rename = get_option( "acui_cron_path_to_move_auto_rename");
	$log = get_option( "acui_cron_log");
	$allow_multiple_accounts = get_option("acui_cron_allow_multiple_accounts");

	if( empty( $cron_activated ) )
		$cron_activated = false;

	if( empty( $send_mail_cron ) )
		$send_mail_cron = false;

	if( empty( $send_mail_updated ) )
		$send_mail_updated = false;

	if( empty( $cron_delete_users ) )
		$cron_delete_users = false;

	if( empty( $update_roles_existing_users) )
		$update_roles_existing_users = false;

	if( empty( $cron_delete_users_assign_posts ) )
		$cron_delete_users_assign_posts = '';

	if( empty( $path_to_file ) )
		$path_to_file = dirname( __FILE__ ) . '/test.csv';

	if( empty( $period ) )
		$period = 'hourly';

	if( empty( $move_file_cron ) )
		$move_file_cron = false;

	if( empty( $path_to_move ) )
		$path_to_move = dirname( __FILE__ ) . '/move.csv';

	if( empty( $path_to_move_auto_rename ) )
		$path_to_move_auto_rename = false;

	if( empty( $log ) )
		$log = "No tasks done yet.";
	
	if( empty( $allow_multiple_accounts ) )
		$allow_multiple_accounts = "not_allowed";

	?>
		<h3><?php _e( "Execute an import of users periodically", 'import-users-from-csv-with-meta' ); ?></h3>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
			<table class="form-table">
				<tbody>
				<tr class="form-field">
					<th scope="row"><label for="path_to_file"><?php _e( "Path of file that are going to be imported", 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input placeholder="<?php _e('Insert complete path to the file', 'import-users-from-csv-with-meta' ) ?>" type="text" name="path_to_file" id="path_to_file" value="<?php echo $path_to_file; ?>" style="width:70%;" />
						<p class="description"><?php _e( 'You have to introduce the path to file, i.e.:', 'import-users-from-csv-with-meta' ); ?> <?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv</p>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="period"><?php _e( 'Period', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>	
						<select id="period" name="period">
							<option <?php if( $period == 'hourly' ) echo "selected='selected'"; ?> value="hourly"><?php _e( 'Hourly', 'import-users-from-csv-with-meta' ); ?></option>
							<option <?php if( $period == 'twicedaily' ) echo "selected='selected'"; ?> value="twicedaily"><?php _e( 'Twicedaily', 'import-users-from-csv-with-meta' ); ?></option>
							<option <?php if( $period == 'daily' ) echo "selected='selected'"; ?> value="daily"><?php _e( 'Daily', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
						<p class="description"><?php _e( 'How often the event should reoccur?', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="cron-activated"><?php _e( 'Activate periodical import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="cron-activated" value="yes" <?php if( $cron_activated == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="send-mail-cron"><?php _e( 'Send mail when using periodical import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="send-mail-cron" value="yes" <?php if( $send_mail_cron == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="send-mail-updated"><?php _e( 'Send mail also to users that are being updated?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="send-mail-updated" value="yes" <?php if( $send_mail_updated == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>
				
				<?php if( is_plugin_active( 'allow-multiple-accounts/allow-multiple-accounts.php' ) ): ?>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Repeated email in different users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="allow_multiple_accounts" value="yes" <?php if( $allow_multiple_accounts == "allowed" ) echo "checked='checked'"; ?>/>
						<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/allow-multiple-accounts/"><?php _e( 'Allow Multiple Accounts', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta'); ?>)</strong>. <?php _e('Allow multiple user accounts to be created having the same email address.','import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<?php endif; ?>
				
				<tr class="form-field form-required">
					<th scope="row"><label for="cron-delete-users"><?php _e( 'Delete users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left;">
							<input type="checkbox" name="cron-delete-users" value="yes" <?php if( $cron_delete_users == true ) echo "checked='checked'"; ?>/>
						</div>
						<div style="margin-left:25px;">
							<select id="cron-delete-users-assign-posts" name="cron-delete-users-assign-posts">
								<?php
									if( $cron_delete_users_assign_posts == '' )
										echo "<option selected='selected' value=''>" . __( 'Delete posts of deled users without assing to any user', 'import-users-from-csv-with-meta' ) . "</option>";
									else
										echo "<option value=''>" . __( 'Delete posts of deled users without assing to any user', 'import-users-from-csv-with-meta' ) . "</option>";

									$blogusers = get_users();
									
									foreach ( $blogusers as $bloguser ) {
										if( $bloguser->ID == $cron_delete_users_assign_posts )
											echo "<option selected='selected' value='{$bloguser->ID}'>{$bloguser->display_name}</option>";
										else
											echo "<option value='{$bloguser->ID}'>{$bloguser->display_name}</option>";
									}
								?>
							</select>
							<p class="description"><?php _e( 'After delete users, we can choose if we want to assign their posts to another user. Please do not delete them or posts will be deleted.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="role"><?php _e( 'Role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select id="role" name="role">
							<?php 
								if( $role == '' )
									echo "<option selected='selected' value=''>" . __( 'Disable role assignement in cron import', 'import-users-from-csv-with-meta' )  . "</option>";
								else
									echo "<option value=''>" . __( 'Disable role assignement in cron import', 'import-users-from-csv-with-meta' )  . "</option>";

								$list_roles = acui_get_editable_roles();								
								foreach ($list_roles as $key => $value) {
									if($key == $role)
										echo "<option selected='selected' value='$key'>$value</option>";
									else
										echo "<option value='$key'>$value</option>";
								}
							?>
						</select>
						<p class="description"><?php _e( 'Which role would be used to import users?', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="update-roles-existing-users"><?php _e( 'Update roles for existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="update-roles-existing-users" value="yes" <?php if( $update_roles_existing_users == 'yes' ) echo "checked='checked'"; ?>/>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="move-file-cron"><?php _e( 'Move file after import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left;">
							<input type="checkbox" name="move-file-cron" value="yes" <?php if( $move_file_cron == true ) echo "checked='checked'"; ?>/>
						</div>

						<div class="move-file-cron-cell" style="margin-left:25px;">
							<input placeholder="<?php _e( 'Insert complete path to the file', 'import-users-from-csv-with-meta'); ?>" type="text" name="path_to_move" id="path_to_move" value="<?php echo $path_to_move; ?>" style="width:70%;" />
							<p class="description"><?php _e( 'You have to introduce the path to file, i.e.:', 'import-users-from-csv-with-meta'); ?> <?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/move.csv</p>
						</div>
					</td>
				</tr>
				<tr class="form-field form-required move-file-cron-cell">
					<th scope="row"><label for="move-file-cron"><?php _e( 'Auto rename after move?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left;">
							<input type="checkbox" name="path_to_move_auto_rename" value="yes" <?php if( $path_to_move_auto_rename == true ) echo "checked='checked'"; ?>/>
						</div>

						<div style="margin-left:25px;">
							<p class="description"><?php _e( 'Your file will be renamed after moved, so you will not lost any version of it. The way to rename will be append the time stamp using this date format: YmdHis.', 'import-users-from-csv-with-meta'); ?></p>
						</div>
					</td>
				</tr>			
				<tr class="form-field form-required">
					<th scope="row"><label for="log"><?php _e( 'Last actions of schedule task', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<pre><?php echo $log; ?></pre>
					</td>
				</tr>
				</tbody>
			</table>
			<input class="button-primary" type="submit" value="<?php _e( 'Save schedule options', 'import-users-from-csv-with-meta'); ?>"/>
		</form>

		<script>
		jQuery( document ).ready( function( $ ){
			$( "[name='cron-delete-users']" ).change(function() {
		        if( $(this).is( ":checked" ) ) {
		            var returnVal = confirm("<?php _e( 'Are you sure to delete all users that are not present in the CSV? This action cannot be undone.', 'import-users-from-csv-with-meta' ); ?>");
		            $(this).attr("checked", returnVal);

		            if( returnVal )
		            	$( '#cron-delete-users-assign-posts' ).show();
		        }
		        else{
	       	        $( '#cron-delete-users-assign-posts' ).hide();     	        
		        }
		    });

		    $( "[name='move-file-cron']" ).change(function() {
		        if( $(this).is( ":checked" ) ){
		        	$( '.move-file-cron-cell' ).show();
		        }
		        else{
		        	$( '.move-file-cron-cell' ).hide();
		        }
		    });

		    <?php if( $cron_delete_users == '' ): ?>
		    $( '#cron-delete-users-assign-posts' ).hide();
		    <?php endif; ?>

		    <?php if( !$move_file_cron ): ?>
		    $( '.move-file-cron-cell' ).hide();
		    <?php endif; ?>
		});
		</script>
	<?php break; ?>

	<?php case 'donate': ?>

	<div class="postbox">
	    <h3 class="hndle"><span>&nbsp;<?php _e( 'Do you like it?', 'import-users-from-csv-with-meta' ); ?></span></h3>

	    <div class="inside" style="display: block;">
	        <img src="<?php echo $url_plugin; ?>icon_coffee.png" alt="<?php _e( 'buy me a coffee', 'import-users-from-csv-with-meta' ); ?>" style=" margin: 5px; float:left;">
	        <p><?php _e( 'Hi! we are', 'import-users-from-csv-with-meta'); ?> <a href="https://twitter.com/fjcarazo" target="_blank" title="Javier Carazo">Javier Carazo</a> <?php _e( 'and all the team of', 'import-users-from-csv-with-meta' ); ?> <a href="http://codection.com">Codection</a>, <?php _e("developers of this plugin.", 'import-users-from-csv-with-meta' ); ?></p>
	        <p><?php _e( 'We have been spending many hours to develop this plugin and answering questions in the forum to give you the best support. <br>If you like and use this plugin, you can <strong>buy us a cup of coffee</strong>.', 'import-users-from-csv-with-meta' ); ?></p>
	        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="QPYVWKJG4HDGG">
				<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e('PayPal – The safer, easier way to pay online.', 'import-users-from-csv-with-meta' ); ?>">
				<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
			</form>
	        <div style="clear:both;"></div>
	    </div>

	    <h3 class="hndle"><span>&nbsp;<?php _e( 'Or if you prefer, you can also help us becoming a Patreon:', 'import-users-from-csv-with-meta' ); ?></span></h3>

	    <div class="inside acui" style="display: block;">
	    	<a class="patreon" color="primary" type="button" name="become-a-patron" data-tag="become-patron-button" href="https://www.patreon.com/bePatron?c=1741454" role="button">
	    		<div class="oosjif-1 jFPfxp"><span>Become a patron</span></div>
	    	</a>
	    </div>
	</div>

	<?php break; ?>

	<?php case 'help': ?>

	<div class="postbox">
	    <h3 class="hndle"><span>&nbsp;<?php _e( 'Need help with WordPress or WooCommerce?', 'import-users-from-csv-with-meta' ); ?></span></h3>

	    <div class="inside" style="display: block;">
	        <p><?php _e( 'Hi! we are', 'import-users-from-csv-with-meta' ); ?> <a href="https://twitter.com/fjcarazo" target="_blank" title="Javier Carazo">Javier Carazo</a><?php _e( 'and', 'import-users-from-csv-with-meta' ) ?> <a href="https://twitter.com/ahornero" target="_blank" title="Alberto Hornero">Alberto Hornero</a>  <?php _e( 'from', 'import-users-from-csv-with-meta' ); ?> <a href="http://codection.com">Codection</a>, <?php _e( 'developers of this plugin.', 'import-users-from-csv-with-meta' ); ?></p>
	        <p><?php _e( 'We work everyday with WordPress and WooCommerce, if you need help hire us, send us a message to', 'import-users-from-csv-with-meta' ); ?> <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</p>
	        <div style="clear:both;"></div>
	    </div>
	</div>

	<?php break; ?>
<?php
	}
}