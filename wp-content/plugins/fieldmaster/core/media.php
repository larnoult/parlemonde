<?php 

class fieldmaster_media {
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct() {
		
		// actions
		add_action('fieldmaster/save_post', 				array($this, 'save_files'), 5, 1);
		add_action('fieldmaster/input/admin_footer', 		array($this, 'admin_footer'));
		
		
		// filters
		add_filter('wp_handle_upload_prefilter', 	array($this, 'handle_upload_prefilter'), 10, 1);
		add_filter('fieldmaster/input/admin_l10n',			array($this, 'fieldmaster_input_admin_l10n'), 10, 1);
		
		
		// ajax
		add_action( 'wp_ajax_query-attachments',	array($this, 'wp_ajax_query_attachments'), -1);
		
	}
	
	
	/*
	*  fieldmaster_input_admin_l10n
	*
	*  This function will append l10n strings for JS use
	*
	*  @type	function
	*  @date	11/04/2016
	*  @since	5.3.8
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function fieldmaster_input_admin_l10n( $l10n ) {
		
		// append
		$l10n['media'] = array(
			'select'		=> _x('Select', 'verb', 'fieldmaster'),
			'edit'			=> _x('Edit', 'verb', 'fieldmaster'),
			'update'		=> _x('Update', 'verb', 'fieldmaster'),
			'uploadedTo'	=> __("Uploaded to this post",'fieldmaster'),
			'default_icon'	=> wp_mime_type_icon()
		);
		
		
		// return
		return $l10n;
		
	}
	
		
	/*
	*  handle_upload_prefilter
	*
	*  description
	*
	*  @type	function
	*  @date	16/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function handle_upload_prefilter( $file ) {
		
		// bail early if no fieldmaster field
		if( empty($_POST['_fieldmasteruploader']) ) {
		
			return $file;
			
		}
		
		
		// load field
		$field = fieldmaster_get_field( $_POST['_fieldmasteruploader'] );
		
		if( !$field ) {
		
			return $file;
			
		}
		
		
		// get errors
		$errors = fieldmaster_validate_attachment( $file, $field, 'upload' );
		
		
		// filter for 3rd party customization
		$errors = apply_filters("fieldmaster/upload_prefilter", $errors, $file, $field);
		$errors = apply_filters("fieldmaster/upload_prefilter/type={$field['type']}", $errors, $file, $field );
		$errors = apply_filters("fieldmaster/upload_prefilter/name={$field['name']}", $errors, $file, $field );
		$errors = apply_filters("fieldmaster/upload_prefilter/key={$field['key']}", $errors, $file, $field );
		
		
		// append error
		if( !empty($errors) ) {
			
			$file['error'] = implode("\n", $errors);
			
		}
		
		
		// return
		return $file;
		
	}

	
	/*
	*  save_files
	*
	*  This function will save the $_FILES data
	*
	*  @type	function
	*  @date	24/10/2014
	*  @since	5.0.9
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_files( $post_id = 0 ) {
		
		// bail early if no $_FILES data
		if( empty($_FILES['fieldmaster']['name']) ) {
			
			return;
			
		}
		
		
		// upload files
		fieldmaster_upload_files();
	
	}
	
	
	/*
	*  admin_footer
	*
	*  description
	*
	*  @type	function
	*  @date	19/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_footer() {
		
?>
<script type="text/javascript">
	if( fieldmaster ) fieldmaster.media.mime_types = <?php echo json_encode( get_allowed_mime_types() ); ?>;
</script>
<?php
		
	}
	
	
	/*
	*  wp_ajax_query_attachments
	*
	*  description
	*
	*  @type	function
	*  @date	26/06/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function wp_ajax_query_attachments() {
		
		add_filter('wp_prepare_attachment_for_js', 	array($this, 'wp_prepare_attachment_for_js'), 10, 3);
		
	}
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
		
		// append attribute
		$response['fieldmaster_errors'] = false;
		
		
		// bail early if no fieldmaster field
		if( empty($_POST['query']['_fieldmasteruploader']) ) {
		
			return $response;
			
		}
		
		
		// load field
		$field = fieldmaster_get_field( $_POST['query']['_fieldmasteruploader'] );
		
		if( !$field ) {
		
			return $response;
			
		}
		
		
		// get errors
		$errors = fieldmaster_validate_attachment( $response, $field, 'prepare' );
		
		
		// append errors
		if( !empty($errors) ) {
			
			$response['fieldmaster_errors'] = implode('<br />', $errors);
			
		}
		
		
		// return
		return $response;
		
	}
	
}


// initialize
new fieldmaster_media();

?>