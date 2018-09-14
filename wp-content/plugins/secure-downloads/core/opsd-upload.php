<?php 
/**
 * @version 1.0
 * @package File Upload
 * @subpackage Support Upload Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2017-04-14
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Code usage in other places for Upload, Selection and Insertion of file URL
 * 
	<div>
		<input type="text" value="" wrap="off" placeholder="..." class="opsd_file_urls" name="opsd_file_urls" />
		<a href="javascript:void(0)" class="button opsd_btn_upload"
		   data-modal_title="<?php echo esc_attr( __( 'Choose file', 'secure-downloads' ) ); ?>" 
			 data-btn_title="<?php echo esc_attr( __( 'Insert file URL', 'secure-downloads' ) ); ?>" 						   
		><?php _e( 'Upload File', 'secure-downloads' ); ?></a>
	</div>
	<?php 

		$opsd_upload = opsd_upload();	// Get OPSD_Upload obj instance

		$opsd_upload->set_upload_button( '.opsd_btn_upload' );

		$opsd_upload->set_element_insert_url( '.opsd_file_urls' );
	?> 
 * 
 */

// General Init Class    
final class OPSD_Upload {

	public $settings = array(
							  'upload_button' => ''
							, 'element_insert_url' => ''
							, 'wp_media_uploader_params' => array( 'key' => 'opsd_type', 'value' => 'opsd_upload' )		// Required for setting OUR Dir for uploading and set it PROTECTED
				   );

	// Define only one instance of this class
    static private $instance = NULL;
	
	public static function init() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof OPSD_Upload ) ) {
			
			self::$instance = new OPSD_Upload;			
						
			add_filter( 'upload_dir', array( self::$instance, 'filter_upload_dir' ) );		
			
			add_action( 'admin_footer', array( self::$instance, 'js' ), 50 );              // Load JavaScript Code at  the footer of the Admin Panel page. Executed in ALL Admin Menu Pages
			
			//TODO: remove this
			// add_filter( 'posts_where', array( self::$instance, 'opsd_filter_posts_where' ) );
			// add_action('pre_get_posts', array( self::$instance, 'opsd_pre_get_posts' ) );
			
			self::$instance->protect_upload_dir();
		}
		return self::$instance;        			
	}
			
	
	/** Get Name of protected DIR name,  like opsd_XXXXX
	 * 
	 * @return string
	 */
	public function get_protected_dir_name() {
		
		$get_protected_dir_name = get_opsd_option( 'opsd_protected_directory_name_level1' );
		
		if ( empty( $get_protected_dir_name ) ) {
			$get_protected_dir_name = 'opsd_' . wp_generate_password( 20, false, false );
			update_opsd_option( 'opsd_protected_directory_name_level1', $get_protected_dir_name );
		}

		$get_protected_dir_name = untrailingslashit($get_protected_dir_name);
		
		return $get_protected_dir_name;
	}
	
	
	/** Get all settings or specific setting option
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get_settings( $key = '' ) {
		
		if ( '' === $key )
			return $this->settings;
		
		if ( isset( $this->settings[ $key ] ) )
			return $this->settings[ $key ];
		else 
			return false;
	}
	
	
//TODO: remove this	
/*	
	function opsd_pre_get_posts( $query ) {
debuge_log( $_POST );
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$discount = $query->get( 'discount' );

		if ( ! empty( $discount ) ) {

			// unset ref var from $wp_query
			$query->set( 'discount', null );

			global $wp;

			// unset ref var from $wp
			unset( $wp->query_vars[ 'discount' ] );

			// if in home (because $wp->query_vars is empty) and 'show_on_front' is page
			if ( empty( $wp->query_vars ) && get_option( 'show_on_front' ) === 'page' ) {

				// reset and re-parse query vars
				$wp->query_vars['page_id'] = get_option( 'page_on_front' );
				$query->parse_query( $wp->query_vars );

			}

		}
	
	}
*/

//TODO: remove this
	/**
	 * @param string $where Where clause
	 * @return string $where Modified where clause
	*/
/*	
	function opsd_filter_posts_where( $where = '' ) {

debuge_log( $_POST  );
return $where;
//debuge( maybe_unserialize( 'a:3:{s:6:"action";s:17:"query-attachments";s:7:"post_id";s:1:"0";s:5:"query";a:4:{s:7:"orderby";s:4:"date";s:5:"order";s:4:"DESC";s:14:"posts_per_page";s:2:"40";s:5:"paged";s:1:"1";}}' ));

		$media_uploader_params = $this->get_settings( 'wp_media_uploader_params' );
		if (       ( isset( $_POST['query'] ) )
				&& ( isset( $_POST['query'][ $media_uploader_params[ 'key' ] ] ) )
				&& ( $media_uploader_params[ 'value' ] === $_POST['query'][ $media_uploader_params[ 'key' ] ] )
			) {	
			
			global $wpdb;
			$where .= " AND guid LIKE '%".$wpdb->esc_like( untrailingslashit(  get_opsd_option( 'opsd_protected_directory_name_level1' ) ) )."%'";
		}
		return $where;
	}
*/
	
	/** Filters the uploads directory array,  
	 *  after CLICKING on our Upload Button and USE our wp.media thanks to 'wp_media_uploader_params'
	 *
	 * @param array $uploads Array of upload directory data:
															array (
																[path]		=> Z:\home\new\www/wp-content/uploads/opsd_lSJacOT1yVLFnrkqt2xR/2017/04
																[url]		=> http://new/wp-content/uploads/opsd_lSJacOT1yVLFnrkqt2xR/2017/04
															 
																[subdir]	=> /opsd_lSJacOT1yVLFnrkqt2xR/2017/04
																[basedir]	=> Z:\home\new\www/wp-content/uploads
															 
																[baseurl]	=> http://new/wp-content/uploads
																[error]		=> 
															)
	 
															*$uploads = apply_filters( 'upload_dir', $cache[ $key ] );
	 * 
	 * @param type $param
	 */
	public function filter_upload_dir( $param ) {
		
		//TODO: here we can  create own TAGs and Versioning directory  structure in some way.
		
		$media_uploader_params = $this->get_settings( 'wp_media_uploader_params' );
		
		if ( isset( $_POST[ $media_uploader_params[ 'key' ] ] ) &&  $media_uploader_params[ 'value' ] === $_POST[ $media_uploader_params[ 'key' ] ] ) {

			$protected_dir_name = $this->get_protected_dir_name();

			if ( empty( $param['subdir'] ) ) {
				
				$param['path']   = $param['path'] . '/' . $protected_dir_name;
				$param['url']    = $param['url']  . '/' . $protected_dir_name;
				$param['subdir'] =					'/' . $protected_dir_name;
				
			} else {
				$new_subdir = '/' . $protected_dir_name . $param['subdir'];

				$param['path']   = str_replace( $param['subdir'], $new_subdir, $param['path'] );
				$param['url']    = str_replace( $param['subdir'], $new_subdir, $param['url'] );
				$param['subdir'] = str_replace( $param['subdir'], $new_subdir, $param['subdir'] );
			}
		}

		return $param;		
	}

	
	/** Get path  to protected dir.
	 * 
	 * @return type
	 */
	public function get_protected_dir() {

		// Protected secret name LEVEL 1
		$dir_level1 = $this->get_protected_dir_name();
		
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();

		return $upload_dir['basedir'] . '/' . $dir_level1;
	}
	
	
	/** Check and Protect upload folder each  time
	 * 
		*  May be we need to have 2 folders,  like /opsd_xxxxx/XXXXXXXXXXXXX
		*  for prevent of dir listing at previous stage /opsd_xxxxx with .htaccess file
		* 
		*	Typical Directory structure
		*	/wp-content/uploads/
		*						   /opsd_xxxxx					{main dir}
		*						   /.htaccess					(Deny access and deny dir listing) 
		*						   /.index.php					(Silence is golden)
		*									   /XXXXXXXXXXXXX	(Secret dir for store files)
	 */
	function protect_upload_dir() {

		// Protected secret name LEVEL 1
		$dir_level1 = $this->get_protected_dir_name();
		
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();

		$files = array(
			array(
				'base'    => $upload_dir['basedir'] . '/' . $dir_level1,
				'file'    => '.htaccess',
				'content' =>  'Options -Indexes' . "\n"
							. 'deny from all'
			)
			, array(
				'base'    => $upload_dir['basedir'] . '/' . $dir_level1,
				'file'    => 'index.php',
				'content' => '<?php ' . "\n"
						   . '// Silence is golden.'

			)
		);

		foreach ( $files as $file ) {

			if (   ( wp_mkdir_p( $file['base'] ) )												// Recursive directory creation based on full path.
				&& ( ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) )		// If file not exist
			) {

				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	
	
	/** Define element for opening wp media after clicking
	 * 
	 * @param string $jq_selector
	 */
	public function set_upload_button( $jq_selector ) {		
		$this->settings['upload_button'] = $jq_selector;
	}
	
	
	/** Define element for inserting URL of file from wp media
	 * 
	 * @param type $jq_selector
	 */
	public function set_element_insert_url( $jq_selector ) {		
		$this->settings['element_insert_url'] = $jq_selector;
	}
	
		
	public function js() {
		
		//set   JavaScript only  if we set upload button
		$jq_sel_upload_button = $this->get_settings( 'upload_button' );

		if ( empty( $jq_sel_upload_button ) )
			return;
		

	?>
	<!-- OPSD JavaScript -->
	<script type="text/javascript">
		var opsd_file_frame;
		(function($){
			//'use strict';
			<?php $media_uploader_params = $this->get_settings( 'wp_media_uploader_params' ); ?>
			// Our wp media frame
			

			jQuery( '<?php echo $this->get_settings('upload_button'); ?>' ).on( 'click', function( event ) {

				var j_btn = jQuery( this );
				var is_multi_selection = true;

				var insert_field_separator = '<?php $opsd_csv_separator = get_opsd_option( 'opsd_csv_separator' ); echo empty( $opsd_csv_separator ) ? ',' : $opsd_csv_separator; ?>';
				
				// Stop the anchor's default behavior
				event.preventDefault();

				// If frame exist close it
				if ( opsd_file_frame ) {
					opsd_file_frame.close();
				}
					
					
				///////////////////////////////////////////////////////////////////////
				// Create Media Frame
				///////////////////////////////////////////////////////////////////////
				opsd_file_frame = wp.media.frames.opsd_upload_file_frame = wp.media( {									// Check  here ../wp-includes/js/media-views.js	
					// Set the title of the modal.
					title: j_btn.data( 'modal_title' ),
					library: {
						type: ''
					},
					button: {
						text: j_btn.data( 'btn_title' ),
					},
					multiple: is_multi_selection,
					states: [
								new wp.media.controller.Library( {
									       <?php  
										   // Add to  this libaray custom post  parameter: $_POST['query'][ $media_uploader_params['key'] ] = $media_uploader_params['value']
										   // We are checking in functon opsd_filter_posts_where media files that  only  relative to  this medi Frame opening
										   // And filtering posts (in WHERE) relative custom path to  our files.
										    // echo '{' . $media_uploader_params['key'] . ": '" . $media_uploader_params['value'] . "' }"; 
										   ?>
											library: wp.media.query(), 
											multiple: is_multi_selection,
											title:	j_btn.data( 'modal_title' ),
											priority: 15,
											filterable: 'uploaded',
												//idealColumnWidth: 125
									} )
							]
				} );
				
				
				///////////////////////////////////////////////////////////////////////
				// Set  custom parameters for uploader	->  $_POST['opsd_type'] - checking in "upload_dir",  when filter_upload_dir
				///////////////////////////////////////////////////////////////////////
				opsd_file_frame.on( 'ready', function () {
					opsd_file_frame.uploader.options.uploader.params = {
						type: 'opsd_download',
						<?php
						echo $media_uploader_params['key'] . ": '" . $media_uploader_params['value'] . "'";
						?>									
					};
				} );


				///////////////////////////////////////////////////////////////////////
				// When File have selected, do this
				///////////////////////////////////////////////////////////////////////
				opsd_file_frame.on( 'select', function () {

					if ( ! is_multi_selection ) { // Single file
						
						var attachment = opsd_file_frame.state().get('selection').first().toJSON();

						// Put URL of file to text field
						j_btn.parent().find('<?php echo $this->get_settings('element_insert_url'); ?>').val( attachment.url );

					} else { // Multiple files.
						
						var file_paths = '';
						var csv_data_line = '';
						opsd_file_frame.state().get('selection').map( function ( attachment ) {

// Request  new data
//attachment.fetch().then(function (data) {
//	console.log(data);
//  // preloading finished
//  // after this you can use your attachment normally
//  //wp.media.attachment( attachment.id ).get('url');
//});

							attachment = attachment.toJSON();
//console.log( attachment );

							if ( attachment.url ) {
								// Insert info from selected files
								csv_data_line = attachment.id + insert_field_separator + attachment.title  + insert_field_separator + attachment.opsd_version_num  + insert_field_separator + attachment.description + insert_field_separator + attachment.url
								file_paths = file_paths ? file_paths + "\n" + csv_data_line : csv_data_line;
							}
							
							// file_paths = file_paths ? file_paths + "\n" + attachment.url : attachment.url;
						});

						//j_btn.parent().find('<?php echo $this->get_settings('element_insert_url'); ?>').val( file_paths );
						jQuery( '#opsd_products_csv_text' ).val( file_paths + "\n\n" + jQuery( '#opsd_products_csv_text' ).val() );
					}
					
				} );
				
if (0) {							
				/** Remove Dom element of Media element from Media browser,  if the URL not from  our settings.
				* 
				* @param {type} my_model_obj
				* @returns {undefined}				 
				*/
				function opsd_remove_media_element_from_container( my_model_obj , delay_time ) {
				
					/** Attributes:
						'id': 112
						'title': __71
						'filename': 71.zip 
						'url': http://server.com/wp-content/uploads/opsd_lSJacOT1yVLFnrkqt2xR/2017/04/71.zip' 
						'link': http://server.com/__71/' 
						'alt': 
						'author': 1 
						'description': 
						'caption': 
						'name': __71 
						'status': inherit 
						'uploadedTo': 0 
						'date': Mon Apr 17 2017 14:30:32
						'modified': Mon Apr 17 2017 14:30:32
						'menuOrder': 0 
						'mime': application/zip 
						'type': application 
						'subtype': zip  
						'icon': http://server.com/wp-includes/images/media/archive.png
						'dateFormatted': April 17, 2017 
						'nonces': [object Object] 
						'editLink': http://server.com/wp-admin/post.php?post=112&action=edit
						'meta': false 
						'authorName': admin_name 
						'filesizeInBytes': 324104 
						'filesizeHumanReadable': 317 KB 
						'compat': [object Object]
					 */
							
					// Sometimes  need some delay
					_.delay( function() {
							
						if ( my_model_obj.attributes.url != undefined ) {
							if ( my_model_obj.attributes.url.indexOf('/<?php echo $this->get_protected_dir_name(); ?>/') === -1 ) {
								//console.log( my_model_obj.attributes.url );
								//wp.media.model.Attachment.get("collection").collection.remove( my_model_obj );								
								
								jQuery( "li[data-id='" + my_model_obj.attributes.id + "']" ).remove();
							}
						}					
					
					}, delay_time ); 					
					
				}
	
				wp.media.model.Attachment.get("collection").collection.on( 'change', function( my_model_obj ) {
					opsd_remove_media_element_from_container( my_model_obj , 1);
				});
				
				// Fires,  when  Content redraw
				opsd_file_frame.on( 'content:activate:browse', function(){
					
						var opsd_models = wp.media.model.Attachment.get("collection").collection.models;

						_.each( opsd_models, function( my_model_obj, ind) { 

							opsd_remove_media_element_from_container( my_model_obj , 1 );
						});																			
				});
}				

/*
	// Fires when a state activates.
	opsd_file_frame.on( 'activate', function() { alert('activate'); } );

	// Fires after the frame markup has been built, but not appended to the DOM.
	// @see wp.media.view.Modal.attach()
	opsd_file_frame.on( 'ready', function() { alert('ready'); } );

	// Fires when the frame's $el is appended to its DOM container.
	// @see media.view.Modal.attach()
	opsd_file_frame.on( 'attach', function() { alert('attach'); } );

	// Fires when the modal opens (becomes visible).
	// @see media.view.Modal.open()
	opsd_file_frame.on( 'open', function() { alert('open'); } );

	// Fires when the modal closes via the escape key.
	// @see media.view.Modal.close()
	opsd_file_frame.on( 'escape', function() { alert('escape'); } );

	// Fires when the modal closes.
	// @see media.view.Modal.close()
	opsd_file_frame.on( 'close', function() { alert('close'); } );

	// Fires when a user has selected attachment(s) and clicked the select button.
	// @see media.view.MediaFrame.Post.mainInsertToolbar()
	opsd_file_frame.on( 'select', function() {
		var selectionCollection = opsd_file_frame.state().get('select');
	} );

	// Fires when a mode is deactivated on a region { 'menu' | title | content | toolbar | router }
	opsd_file_frame.on( 'content:deactivate', function() { alert('{region}:deactivate'); } );
	// and a more specific event including the mode.
	opsd_file_frame.on( 'content:deactivate:{mode}', function() { alert('{region}:deactivate{mode}'); } );

	// Fires when a region is ready for its view to be created.
	opsd_file_frame.on( 'content:create', function() { alert('{region}:create'); } );
	// and a more specific event including the mode.
	opsd_file_frame.on( 'content:create:{mode}', function() { alert('{region}:create{mode}'); } );

	// Fires when a region is ready for its view to be rendered.
	opsd_file_frame.on( 'content:render', function() { alert('{region}:render'); } );
	// and a more specific event including the mode.
	opsd_file_frame.on( 'content:render:{mode}', function() { alert('{region}:render{mode}'); } );

	// Fires when a new mode is activated (after it has been rendered) on a region.
	opsd_file_frame.on( 'content:activate', function() { alert('{region}:activate'); } );
	// and a more specific event including the mode.
	opsd_file_frame.on( 'content:activate:{mode}', function() { alert('{region}:activate{mode}'); } );

	// Get an object representing the current state.
	//opsd_file_frame.state();

	// Get an object representing the previous state.
	//opsd_file_frame.lastState(); 
*/
				
if(0) {
// Debuge all events from  media Frame!
opsd_file_frame.on("all", function(eventName) {
	console.log('Frame Event: ' + eventName);
 });

// Debuge all events from  media Frame!
wp.media.model.Attachment.get("collection").collection.on("all", function(eventName) {
	console.log('[Collection] Event: ' + eventName);
 }); 
wp.media.model.Attachment.get("models").collection.on( "all", function(eventName) {
	console.log('[models] Event: ' + eventName);
});					
wp.media.model.Attachment.get("views").collection.on( "all", function(eventName) {
	console.log('[views] Event: ' + eventName);
});	
}
				// Open the modal.
				opsd_file_frame.open();				
			});
				
		})(jQuery);
		 						 
	</script>
	<!-- End OPSD JavaScript -->
	<?php
	
	}
}


/**
 * The main function responsible for returning the one true Instance to functions everywhere.
 *
 * Example: <?php $opsd_upload = opsd_upload(); ?>
 */
function opsd_upload() {
    return OPSD_Upload::init();
}
opsd_upload();		// Start


/** Add Version Number Field to Attachment Section of Media Window
 * 
 * @param array $form_fields
 * @param obj $post
 * @return array
 */
function opsd_add_attachment_version_num_field( $form_fields, $post ) {
    
	$field_value = get_post_meta( $post->ID, 'opsd_version_num', true );

	$form_fields[ 'opsd_version_num' ] = array(
												'value'	 => $field_value ? $field_value : '',
												'label'	 => __( 'Vesion' ),
											//'helps' => __( 'Set version number' )
	);
	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'opsd_add_attachment_version_num_field', 10, 2 );


/** Save Version Number as meta key  relative specific attachment.
 * 
 * @param type $attachment_id
 */
function opsd_save_attachment_version_num( $attachment_id ) {
	
    if ( isset( $_REQUEST[ 'attachments' ][ $attachment_id ][ 'opsd_version_num' ] ) ) {
		$version_num = wp_kses_post( trim( stripslashes( $_REQUEST[ 'attachments' ][ $attachment_id ][ 'opsd_version_num' ] ) ) );
		update_post_meta( $attachment_id, 'opsd_version_num', $version_num );
	}
}
add_action( 'edit_attachment', 'opsd_save_attachment_version_num' );


/** Get Meta key "Version Number"  e.g. 'opsd_version_num' when fetching data about attachment
 * 
 * Filters the attachment data prepared for JavaScript.
 *
 * @since 3.5.0
 *
 * @param array      $response   Array of prepared attachment data.
 * @param int|object $attachment Attachment ID or object.
 * @param array      $meta       Array of attachment meta data.
 */
function opsd_wp_prepare_attachment_for_js( $response, $attachment, $meta ){
	
	if ( is_object( $attachment ) )
		$attachment_id = $attachment->ID;
	else
		$attachment_id = $attachment;

	$field_value = get_post_meta( $attachment_id, 'opsd_version_num', true );

	$response[ 'opsd_version_num' ] = $field_value;

	return $response;
}
add_filter('wp_prepare_attachment_for_js', 'opsd_wp_prepare_attachment_for_js' , 10, 3 );

	




/*  we have in WP function
    sanitize_file_name( $filename )
 * 
    filename altering:
 * 
 *  so product.bl.zip  become product.bl_.zip
 * 
 * 
 *
	 * Loop over any intermediate extensions. Postfix them with a trailing underscore
	 * if they are a 2 - 5 character long alpha string not in the extension whitelist.
	 
	foreach ( (array) $parts as $part) {
		$filename .= '.' . $part;

		if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!^(' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( !$allowed )
				$filename .= '_';
		}
	}
 */

function opsd_sanitize_file_name( $filename, $filename_raw ) {
	
/*  $filename, $filename_raw, $_REQUEST
    [0] => product.bl_.zip
    [1] => secure-downloads.bl.zip
    [2] => Array
        (
            [name] => secure-downloads.bl.zip
            [post_id] => 0
            [_wpnonce] => 90049ce803
            [type] => 
            [tab] => 
            [short] => 1
        )
 */
	return $filename;
}
// add_filter( 'sanitize_file_name', 'opsd_sanitize_file_name', 10, 2 );