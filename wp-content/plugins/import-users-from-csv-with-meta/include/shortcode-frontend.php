<?php
add_shortcode( 'import-users-from-csv-with-meta', 'acui_frontend' );
function acui_frontend() {
	ob_start();
	
	if( !current_user_can( 'create_users' ) )
		die( __( 'Only users who are able to create users can manage this form.', 'import-users-from-csv-with-meta' ) );

	if ( $_FILES && !empty( $_POST ) ) {
		$nonce = $_POST['acui_nonce'];
		if ( !isset( $nonce ) || !wp_verify_nonce( $nonce, 'codection-security' ) )
			die( 'Nonce check failed' );

	    foreach ( $_FILES as $file => $array ) {
	        $csv_file_id = acui_frontend_upload_file( $file );

	        $form_data = array();
			$form_data[ "path_to_file" ] = get_attached_file( $csv_file_id );
			$form_data[ "role" ] = get_option( "acui_frontend_role");
			$form_data[ "empty_cell_action" ] = "leave";

			acui_fileupload_process( $form_data, false, true );

	        wp_delete_attachment( $csv_file_id, true );

	        continue;
	    }
	}
	?>
	<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" class="acui_frontend_form">
		<label><?php _e( 'CSV file <span class="description">(required)</span>', 'import-users-from-csv-with-meta' ); ?></label></th>
		<input class="acui_frontend_file" type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" />
		<input class="acui_frontend_submit" type="submit" value="<?php _e( 'Upload and process', 'import-users-from-csv-with-meta' ); ?>"/>

		<?php wp_nonce_field( 'codection-security', 'acui_nonce' ); ?>
	</form>
	<?php
	return ob_get_clean();
}

function acui_frontend_upload_file( $file_handler ) {
    if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) {
        __return_false();
    }
    require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
    require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
    require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
    $attach_id = media_handle_upload( $file_handler, 0 );
    return $attach_id;
}
