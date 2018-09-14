<?php
/*
Plugin Name:	Import users from CSV with meta
Plugin URI:		https://www.codection.com
Description:	This plugins allows to import users using CSV files to WP database automatically
Version:		1.11.3.8.1
Author:			codection
Author URI: 	https://codection.com
License:     	GPL2
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: import-users-from-csv-with-meta
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

$url_plugin = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );
$wp_users_fields = array( "id", "user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim", "user_registered", "password", "user_pass", "locale" );
$wp_min_fields = array("Username", "Email");
$acui_fields = array( "bp_group", "bp_group_role", "role" );
$acui_restricted_fields = array_merge( $wp_users_fields, $wp_min_fields, $acui_fields );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

require_once( "smtp.php" );
require_once( "email-repeated.php" );

if( is_plugin_active( 'buddypress/bp-loader.php' ) ){
	if ( defined( 'BP_VERSION' ) )
		acui_loader();
	else
		add_action( 'bp_init', 'acui_loader' );
}
else
	acui_loader();

function acui_loader(){
	register_activation_hook( __FILE__,'acui_init' ); 
	register_deactivation_hook( __FILE__, 'acui_deactivate' );
	add_action( "plugins_loaded", "acui_init" );
	add_action( "admin_menu", "acui_menu" );
	add_action( 'admin_enqueue_scripts', 'acui_admin_enqueue_scripts' );
	add_filter( 'plugin_row_meta', 'acui_plugin_row_meta', 10, 2 );
	add_action( 'admin_init', 'acui_modify_user_edit_admin' );
	add_action( 'wp_ajax_acui_delete_attachment', 'acui_delete_attachment' );
	add_action( 'wp_ajax_acui_bulk_delete_attachment', 'acui_bulk_delete_attachment' );
	add_action( 'acui_cron_process', 'acui_cron_process' );

	if( is_plugin_active( 'buddypress/bp-loader.php' ) && file_exists( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' ) ){
		require_once( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' );	
	}

	if( get_option( 'acui_show_profile_fields' ) == true ){
		add_action( "show_user_profile", "acui_extra_user_profile_fields" );
		add_action( "edit_user_profile", "acui_extra_user_profile_fields" );
		add_action( "personal_options_update", "acui_save_extra_user_profile_fields" );
		add_action( "edit_user_profile_update", "acui_save_extra_user_profile_fields" );
	}

	// includes
	foreach ( glob( plugin_dir_path( __FILE__ ) . "include/*.php" ) as $file ) {
	    include_once( $file );
	}

	// addons
	foreach ( glob( plugin_dir_path( __FILE__ ) . "addons/*.php" ) as $file ) {
	    include_once( $file );
	}
	
	require_once( "importer.php" );
}

function acui_init(){
	load_plugin_textdomain( 'import-users-from-csv-with-meta', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	acui_activate();
}

function acui_activate(){
	global $acui_smtp_options;
	
	add_option( "acui_columns" );
	
	add_option( "acui_mail_subject", __('Welcome to', 'import-users-from-csv-with-meta') . ' ' . get_bloginfo("name"), '', false );
	add_option( "acui_mail_body", __('Welcome,', 'import-users-from-csv-with-meta') . '<br/>' . __('Your data to login in this site is:', 'import-users-from-csv-with-meta') . '<br/><ul><li>' . __('URL to login', 'import-users-from-csv-with-meta') . ': **loginurl**</li><li>' . __( 'Username', 'import-users-from-csv-with-meta') . '= **username**</li><li>Password = **password**</li></ul>', '', false );
	
	add_option( "acui_cron_activated", false );
	add_option( "acui_cron_send_mail", false );
	add_option( "acui_cron_send_mail_updated", false );
	add_option( "acui_cron_delete_users", false );
	add_option( "acui_cron_path_to_file" );
	add_option( "acui_cron_path_to_move" );
	add_option( "acui_cron_path_to_move_auto_rename" );
	add_option( "acui_cron_period" );
	add_option( "acui_cron_role" );
	add_option( "acui_cron_update_roles_existing_users" );
	add_option( "acui_cron_log" );
	add_option( "acui_cron_allow_multiple_accounts", "not_allowed" );
	
	add_option( "acui_frontend_send_mail", false );
	add_option( "acui_frontend_send_mail_updated", false );
	add_option( "acui_frontend_role" );

	add_option( "acui_manually_send_mail", false );
	add_option( "acui_manually_send_mail_updated", false );

	add_option( "acui_automattic_wordpress_email" );
	add_option( "acui_show_profile_fields" );

	// smtp
	foreach ( $acui_smtp_options as $name => $val ) {
		add_option( $name, $val );
	}
}

function acui_deactivate(){
	wp_clear_scheduled_hook( 'acui_cron' );
}

function acui_admin_enqueue_scripts() {
	wp_enqueue_style( 'acui_css', plugin_dir_url( __FILE__ ) . '/assets/style.css', false, '1.0.0' );
}

function acui_delete_options(){
	global $acui_smtp_options;

	delete_option( "acui_columns" );
	
	delete_option( "acui_mail_subject" );
	delete_option( "acui_mail_body" );

	delete_option( "acui_cron_activated" );
	delete_option( "acui_cron_send_mail" );
	delete_option( "acui_cron_send_mail_updated" );
	delete_option( "acui_cron_delete_users" );
	delete_option( "acui_cron_path_to_file" );
	delete_option( "acui_cron_path_to_move" );
	delete_option( "acui_cron_path_to_move_auto_rename" );
	delete_option( "acui_cron_period" );
	delete_option( "acui_cron_role" );
	delete_option( "acui_cron_update_roles_existing_users" );
	delete_option( "acui_cron_log" );
	delete_option( "acui_cron_allow_multiple_accounts" );

	delete_option( "acui_frontend_send_mail" );
	delete_option( "acui_frontend_send_mail_updated" );
	delete_option( "acui_frontend_role" );

	delete_option( "acui_manually_send_mail" );
	delete_option( "acui_manually_send_mail_updated" );

	delete_option( "acui_automattic_wordpress_email" );
	delete_option( "acui_show_profile_fields" );

	foreach ( $acui_smtp_options as $name => $val ) {
		delete_option( $name );
	}
}

function acui_get_restricted_fields(){
	global $acui_restricted_fields;
	return apply_filters( 'acui_restricted_fields', $acui_restricted_fields );
}

function acui_menu() {
	add_submenu_page( 'tools.php', __( 'Insert users massively (CSV)', 'import-users-from-csv-with-meta' ), __( 'Import users from CSV', 'import-users-from-csv-with-meta' ), 'create_users', 'acui', 'acui_options' );
}

function acui_plugin_row_meta( $links, $file ){
	if ( strpos( $file, basename( __FILE__ ) ) !== false ) {
		$new_links = array(
					'<a href="https://www.paypal.me/imalrod" target="_blank">' . __( 'Donate', 'import-users-from-csv-with-meta' ) . '</a>',
					'<a href="mailto:contacto@codection.com" target="_blank">' . __( 'Premium support', 'import-users-from-csv-with-meta' ) . '</a>',
					'<a href="http://codection.com/tienda" target="_blank">' . __( 'Premium plugins', 'import-users-from-csv-with-meta' ) . '</a>',
				);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

function acui_detect_delimiter($file){
	$handle = @fopen($file, "r");
	$sumComma = 0;
	$sumSemiColon = 0;
	$sumBar = 0; 

    if($handle){
    	while (($data = fgets($handle, 4096)) !== FALSE):
	        $sumComma += substr_count($data, ",");
	    	$sumSemiColon += substr_count($data, ";");
	    	$sumBar += substr_count($data, "|");
	    endwhile;
    }
    fclose($handle);
    
    if(($sumComma > $sumSemiColon) && ($sumComma > $sumBar))
    	return ",";
    else if(($sumSemiColon > $sumComma) && ($sumSemiColon > $sumBar))
    	return ";";
    else 
    	return "|";
}

function acui_string_conversion( $string ){
	if(!preg_match('%(?:
    [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
    |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
    |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
    |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
    |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
    |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
    |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
    )+%xs', $string)){
		return utf8_encode($string);
    }
	else
		return $string;
}

function acui_mail_from(){
	return get_option( "acui_mail_from" );
}

function acui_mail_from_name(){
	return get_option( "acui_mail_from_name" );
}

function acui_user_id_exists( $user_id ){
	if ( get_userdata( $user_id ) === false )
	    return false;
	else
	    return true;
}

function acui_get_roles($user_id){
	$roles = array();
	$user = new WP_User( $user_id );

	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			$roles[] = $role;
	}

	return $roles;
}

function acui_get_editable_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);
    $list_editable_roles = array();

    foreach ($editable_roles as $key => $editable_role)
		$list_editable_roles[$key] = $editable_role["name"];
	
    return $list_editable_roles;
}

function acui_check_options(){
	if( get_option( "acui_mail_body" ) == "" )
		update_option( "acui_mail_body", __( 'Welcome,', 'import-users-from-csv-with-meta' ) . '<br/>' . __( 'Your data to login in this site is:', 'import-users-from-csv-with-meta' ) . '<br/><ul><li>' . __( 'URL to login', 'import-users-from-csv-with-meta' ) . ': **loginurl**</li><li>' . __( 'Username', 'import-users-from-csv-with-meta' ) . ' = **username**</li><li>' . __( 'Password', 'import-users-from-csv-with-meta' ) . ' = **password**</li></ul>' );

	if( get_option( "acui_mail_subject" ) == "" )
		update_option( "acui_mail_subject", __('Welcome to','import-users-from-csv-with-meta') . ' ' . get_bloginfo("name") );
}

function acui_admin_tabs( $current = 'homepage' ) {
    $tabs = array( 
    		'homepage' => __( 'Import', 'import-users-from-csv-with-meta' ), 
    		'frontend' => __( 'Frontend', 'import-users-from-csv-with-meta' ), 
    		'columns' => __( 'Extra profile fields', 'import-users-from-csv-with-meta' ), 
    		'mail-options' => __( 'Mail options', 'import-users-from-csv-with-meta' ), 
    		'smtp-settings' => __( 'SMTP settings (deprecated)', 'import-users-from-csv-with-meta' ), 
    		'doc' => __( 'Documentation', 'import-users-from-csv-with-meta' ), 
    		'cron' => __( 'Cron import', 'import-users-from-csv-with-meta' ), 
    		'donate' => __( 'Donate/Patreon', 'import-users-from-csv-with-meta' ), 
    		'shop' => __( 'Shop', 'import-users-from-csv-with-meta' ), 
    		'help' => __( 'Hire an expert', 'import-users-from-csv-with-meta' )
    );

    if( get_option( "acui_settings" ) == "wordpress"  )
    	unset( $tabs['smtp-settings'] );

    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
       	$class = ( $tab == $current ) ? ' nav-tab-active' : '';

        if( $tab == "shop"  ){
			$href = "http://codection.com/tienda/";	
			$target = "_blank";
        }
		else{
			$href = "?page=acui&tab=$tab";
			$target = "_self";
		}

		echo "<a class='nav-tab$class' href='$href' target='$target'>$name</a>";

    }
    echo '</h2>';
}

function acui_fileupload_process( $form_data, $is_cron = false, $is_frontend  = false ) {
  if ( !( $is_cron || $is_frontend ) && ( ! isset( $_POST['acui-nonce'] ) || ! wp_verify_nonce( $_POST['acui-nonce'], 'acui-import' ) ) ){
	wp_die( 'Nonce problem' );
  }

  $path_to_file = $form_data["path_to_file"];
  $role = $form_data["role"];
  $uploadfiles = $_FILES['uploadfiles'];

  if( empty( $uploadfiles["name"][0] ) ):
  	
  	  if( !file_exists ( $path_to_file ) )
  			wp_die( __( 'Error, we cannot find the file', 'import-users-from-csv-with-meta' ) . ": $path_to_file" );

  	acui_import_users( $path_to_file, $form_data, 0, $is_cron, $is_frontend );

  else:
  	 
	  if ( is_array($uploadfiles) ) {

		foreach ( $uploadfiles['name'] as $key => $value ) {

		  // look only for uploded files
		  if ($uploadfiles['error'][$key] == 0) {
			$filetmp = $uploadfiles['tmp_name'][$key];

			//clean filename and extract extension
			$filename = $uploadfiles['name'][$key];

			// get file info
			// @fixme: wp checks the file extension....
			$filetype = wp_check_filetype( basename( $filename ), array('csv' => 'text/csv') );
			$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
			$filename = $filetitle . '.' . $filetype['ext'];
			$upload_dir = wp_upload_dir();
			
			if ($filetype['ext'] != "csv") {
			  wp_die('File must be a CSV');
			  return;
			}

			/**
			 * Check if the filename already exist in the directory and rename the
			 * file if necessary
			 */
			$i = 0;
			while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
			  $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
			  $i++;
			}
			$filedest = $upload_dir['path'] . '/' . $filename;

			/**
			 * Check write permissions
			 */
			if ( !is_writeable( $upload_dir['path'] ) ) {
			  wp_die( __( 'Unable to write to directory. Is this directory writable by the server?', 'import-users-from-csv-with-meta' ));
			  return;
			}

			/**
			 * Save temporary file to uploads dir
			 */
			if ( !@move_uploaded_file($filetmp, $filedest) ){
			  wp_die( __( 'Error, the file', 'import-users-from-csv-with-meta' ) . " $filetmp " . __( 'could not moved to', 'import-users-from-csv-with-meta' ) . " : $filedest");
			  continue;
			}

			$attachment = array(
			  'post_mime_type' => $filetype['type'],
			  'post_title' => $filetitle,
			  'post_content' => '',
			  'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $filedest );
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			
			acui_import_users( $filedest, $form_data, $attach_id, $is_cron, $is_frontend );
		  }
		}
	  }
  endif;
}

function acui_manage_frontend_process( $form_data ){
	if( isset( $form_data["send-mail-frontend"] ) && $form_data["send-mail-frontend"] == "yes" )
		update_option( "acui_frontend_send_mail", true );
	else
		update_option( "acui_frontend_send_mail", false );

	if( isset( $form_data["send-mail-updated-frontend"] ) && $form_data["send-mail-updated-frontend"] == "yes" )
		update_option( "acui_frontend_send_mail_updated", true );
	else
		update_option( "acui_frontend_send_mail_updated", false );

	update_option( "acui_frontend_activate_users_wp_members", $form_data["activate_users_wp_members"] );
	update_option( "acui_frontend_role", $form_data["acui_frontend_role"] );
	?>
	<div class="updated">
       <p><?php _e( 'Settings updated correctly', 'import-users-from-csv-with-meta' ) ?></p>
    </div>
    <?php
}


function acui_manage_extra_profile_fields( $form_data ){
	if( isset( $form_data["show-profile-fields"] ) && $form_data["show-profile-fields"] == "yes" ){
		update_option( "acui_show_profile_fields", true );
	}
	else{
		update_option( "acui_show_profile_fields", false );
	}
}

function acui_save_mail_template( $form_data ){
	update_option( "acui_automattic_wordpress_email", stripslashes( $form_data["automattic_wordpress_email"] ) );
	update_option( "acui_mail_body", stripslashes( $form_data["body_mail"] ) );
	update_option( "acui_mail_subject", stripslashes( $form_data["subject_mail"] ) );
	?>
	<div class="updated">
       <p><?php _e( 'Mail template updated correctly', 'import-users-from-csv-with-meta' )?></p>
    </div>
    <?php
}

function acui_manage_cron_process( $form_data ){
	$next_timestamp = wp_next_scheduled( 'acui_cron_process' );

	if( isset( $form_data["cron-activated"] ) && $form_data["cron-activated"] == "yes" ){
		update_option( "acui_cron_activated", true );

			if( !$next_timestamp ) {
				wp_schedule_event( time(), $form_data[ "period" ], 'acui_cron_process' );
			}
	}
	else{
		update_option( "acui_cron_activated", false );
		wp_unschedule_event( $next_timestamp, 'acui_cron_process');		
	}
	
	if( isset( $form_data["send-mail-cron"] ) && $form_data["send-mail-cron"] == "yes" )
		update_option( "acui_cron_send_mail", true );
	else
		update_option( "acui_cron_send_mail", false );

	if( isset( $form_data["send-mail-updated"] ) && $form_data["send-mail-updated"] == "yes" )
		update_option( "acui_cron_send_mail_updated", true );
	else
		update_option( "acui_cron_send_mail_updated", false );

	if( isset( $form_data["cron-delete-users"] ) && $form_data["cron-delete-users"] == "yes" )
		update_option( "acui_cron_delete_users", true );
	else
		update_option( "acui_cron_delete_users", false );

	if( isset( $form_data["move-file-cron"] ) && $form_data["move-file-cron"] == "yes" )
		update_option( "acui_move_file_cron", true );
	else
		update_option( "acui_move_file_cron", false );

	if( isset( $form_data["path_to_move_auto_rename"] ) && $form_data["path_to_move_auto_rename"] == "yes" )
		update_option( "acui_cron_path_to_move_auto_rename", true );
	else
		update_option( "acui_cron_path_to_move_auto_rename", false );
	
	if ( isset ( $form_data["allow_multiple_accounts"] ) && $form_data["allow_multiple_accounts"] == "yes" )
		update_option( "acui_cron_allow_multiple_accounts", "allowed" );
	else
		update_option( "acui_cron_allow_multiple_accounts", "not_allowed" );
	
	update_option( "acui_cron_path_to_file", $form_data["path_to_file"] );
	update_option( "acui_cron_path_to_move", $form_data["path_to_move"] );
	update_option( "acui_cron_period", $form_data["period"] );
	update_option( "acui_cron_role", $form_data["role"] );
	update_option( "acui_cron_update_roles_existing_users", $form_data["update-roles-existing-users"] );
	update_option( "acui_cron_delete_users_assign_posts", $form_data["cron-delete-users-assign-posts"] );
	?>
	<div class="updated">
       <p><?php _e( 'Settings updated correctly', 'import-users-from-csv-with-meta' ) ?></p>
    </div>
    <?php
}

function acui_cron_process(){
	$message = __('Import cron task starts at', 'import-users-from-csv-with-meta' ) . ' ' . date("Y-m-d H:i:s") . '<br/>';

	$form_data = array();
	$form_data[ "path_to_file" ] = get_option( "acui_cron_path_to_file");
	$form_data[ "role" ] = get_option( "acui_cron_role");
	$form_data[ "update_roles_existing_users" ] = get_option( "acui_cron_update_roles_existing_users");
	$form_data[ "empty_cell_action" ] = "leave";

	ob_start();
	acui_fileupload_process( $form_data, true );
	$message .= "<br/>" . ob_get_contents() . "<br/>";
	ob_end_clean();

	$move_file_cron = get_option( "acui_move_file_cron");
	
	if( $move_file_cron ){
		$path_to_file = get_option( "acui_cron_path_to_file");
		$path_to_move = get_option( "acui_cron_path_to_move");

		rename( $path_to_file, $path_to_move );

		acui_cron_process_auto_rename(); // optionally rename with date and time included
	}

	$message .= __( '--Finished at', 'import-users-from-csv-with-meta' ) . ' ' . date("Y-m-d H:i:s") . '<br/><br/>';

	update_option( "acui_cron_log", $message );
}

function acui_cron_process_auto_rename () {
  if( get_option( "acui_cron_path_to_move_auto_rename" ) != true )
  	return;

  $movefile  = get_option( "acui_cron_path_to_move");
  if ($movefile && file_exists($movefile)) {
    $parts = pathinfo($movefile);
    $filename = $parts['filename'];
    if ($filename){
      $date = date('YmdHis'); 
      $newfile = $parts['dirname'] . '/' . $filename .'_' . $date . '.' . $parts['extension'];
      rename($movefile , $newfile);
    } 
  }
}

function acui_extra_user_profile_fields( $user ) {
	$acui_restricted_fields = acui_get_restricted_fields();
	$headers = get_option("acui_columns");
	if( is_array( $headers ) && !empty( $headers ) ):
?>
	<h3>Extra profile information</h3>
	
	<table class="form-table"><?php

	foreach ( $headers as $column ):
		if( in_array( $column, $acui_restricted_fields ) )
			continue;
	?>
		<tr>
			<th><label for="<?php echo $column; ?>"><?php echo $column; ?></label></th>
			<td><input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" value="<?php echo esc_attr(get_the_author_meta($column, $user->ID )); ?>" class="regular-text" /></td>
		</tr>
		<?php
	endforeach;
	?>
	</table><?php
	endif;
}

function acui_save_extra_user_profile_fields( $user_id ){
	$headers = get_option("acui_columns");
	$acui_restricted_fields = acui_get_restricted_fields();

	$post_filtered = filter_input_array( INPUT_POST );

	if( is_array( $headers ) && count( $headers ) > 0 ):
		foreach ( $headers as $column ){
			if( in_array( $column, $acui_restricted_fields ) )
				continue;

			$column_sanitized = str_replace(" ", "_", $column);
			update_user_meta( $user_id, $column, $post_filtered[$column_sanitized] );
		}
	endif;
}

function acui_modify_user_edit_admin(){
	global $pagenow;

	if(in_array($pagenow, array("user-edit.php", "profile.php"))){
    	$acui_columns = get_option("acui_columns");
    	
    	if(is_array($acui_columns) && !empty($acui_columns)){
        	$new_columns = array();
        	$core_fields = array(
	            'username',
	            'user_email',
	            'first_name',
	            'role',
	            'last_name',
	            'nickname',
	            'display_name',
	            'description',
	            'billing_first_name',
	            'billing_last_name',
	            'billing_company',
	            'billing_address_1',
	            'billing_address_2',
	            'billing_city',
	            'billing_postcode',
	            'billing_country',
	            'billing_state',
	            'billing_phone',
	            'billing_email',
	            'shipping_first_name',
	            'shipping_last_name',
	            'shipping_company',
	            'shipping_address_1',
	            'shipping_address_2',
	            'shipping_city',
	            'shipping_postcode',
	            'shipping_country',
	            'shipping_state'
        	);
        
        	foreach ($acui_columns as $key => $column) {
            	
            	if(in_array($column, $core_fields)) {
                	// error_log('removing column because core '.$column);
                	continue;
            	}
            	if(in_array($column, $new_columns)) {
                	// error_log('removing column because not unique '.$column);
                	continue;
                }
            	
            	array_push($new_columns, $column);
        	}
        	
        	update_option("acui_columns", $new_columns);
 		}
 	}
}

function acui_delete_attachment() {
	if( ! current_user_can( 'manage_options' ) )
		wp_die( __('You are not an adminstrator', 'import-users-from-csv-with-meta' ) );

	$attach_id = intval( $_POST['attach_id'] );

	$result = wp_delete_attachment( $attach_id, true );

	if( $result === false )
		echo 0;
	else
		echo 1;

	wp_die();
}

function acui_bulk_delete_attachment(){
	$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
	$old_csv_files = new WP_Query( $args_old_csv );
	$result = 1;

	while($old_csv_files->have_posts()) : 
		$old_csv_files->the_post(); 

		if( wp_delete_attachment( get_the_ID(), true ) === false )
			$result = 0;
	endwhile;
	
	wp_reset_postdata();

	echo $result;

	wp_die();
}

// wp-access-areas functions
 function acui_set_cap_for_user( $capability , &$user , $add ) {
	$has_cap = $user->has_cap( $capability );
	$is_change = ($add && ! $has_cap) || (!$add && $has_cap);
	if ( $is_change ) {
		if ( $add ) {
			$user->add_cap( $capability , true );
			do_action( 'wpaa_grant_access' , $user , $capability );
			do_action( "wpaa_grant_{$capability}" , $user );
		} else if ( ! $add ) {
			$user->remove_cap( $capability );
			do_action( 'wpaa_revoke_access' , $user , $capability );
			do_action( "wpaa_revoke_{$capability}" , $user );
		}
	}
}

// misc
if (!function_exists('str_getcsv')) { 
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') { 
        if (is_string($input) && !empty($input)) { 
            $output = array(); 
            $tmp    = preg_split("/".$eol."/",$input); 
            if (is_array($tmp) && !empty($tmp)) { 
                while (list($line_num, $line) = each($tmp)) { 
                    if (preg_match("/".$escape.$enclosure."/",$line)) { 
                        while ($strlen = strlen($line)) { 
                            $pos_delimiter       = strpos($line,$delimiter); 
                            $pos_enclosure_start = strpos($line,$enclosure); 
                            if ( 
                                is_int($pos_delimiter) && is_int($pos_enclosure_start) 
                                && ($pos_enclosure_start < $pos_delimiter) 
                                ) { 
                                $enclosed_str = substr($line,1); 
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure); 
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end); 
                                $output[$line_num][] = $enclosed_str; 
                                $offset = $pos_enclosure_end+3; 
                            } else { 
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) { 
                                    $output[$line_num][] = substr($line,0); 
                                    $offset = strlen($line); 
                                } else { 
                                    $output[$line_num][] = substr($line,0,$pos_delimiter); 
                                    $offset = ( 
                                                !empty($pos_enclosure_start) 
                                                && ($pos_enclosure_start < $pos_delimiter) 
                                                ) 
                                                ?$pos_enclosure_start 
                                                :$pos_delimiter+1; 
                                } 
                            } 
                            $line = substr($line,$offset); 
                        } 
                    } else { 
                        $line = preg_split("/".$delimiter."/",$line); 

                        /* 
                         * Validating against pesky extra line breaks creating false rows. 
                         */ 
                        if (is_array($line) && !empty($line[0])) { 
                            $output[$line_num] = $line; 
                        }  
                    } 
                } 
                return $output; 
            } else { 
                return false; 
            } 
        } else { 
            return false; 
        } 
    } 
} 

function cod_set_html_content_type() {
	return 'text/html';
}

function acui_return_false(){
	return false;
}