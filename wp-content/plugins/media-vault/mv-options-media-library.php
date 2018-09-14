<?php
/**
 * Functions for Media Vault WP Media Library (wp-admin/upload.php) additions.
 * Including Bulk action handling and custom Media Library columns
 *
 * @package WordPress_Plugin
 * @package MediaVault
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */



/**
 * Remove WP List Table row actions in the Media Library List Table
 * if the attachment is protected and the user is not permitted to access it
 *
 * @since 0.7
 *
 * @uses mgjp_mv_check_user_permitted() returns true if user is permitted to access
 *                                            specified attachment
 * @param $actions array Array of row actions available for specific attachment
 * @param $post object WP_Post object of currently rendering attachment
 * @return array Return row actions untouched if user permitted to access attachment
 * @return array Empty array if no access permitted
 */
function mgjp_mv_modify_media_library_row_actions( $actions, $post ) {

  // check if current user is permitted to access the post
  if ( ! mgjp_mv_check_user_permitted( $post->ID ) )
    return array( esc_html__( 'You do not have permission to access this attachment', 'media-vault' ) );

  return $actions;
}
add_filter( 'media_row_actions', 'mgjp_mv_modify_media_library_row_actions', 10, 2 );


/**
 * Register Media Vault custom column to WP Media Library (wp-admin/upload.php)
 * list table.
 *
 * @since 0.4
 *
 * @param array $columns array of columns for WP Media List Table
 * @return array Array of columns, including custom column, for WP Media List Table
 */
function mgjp_mv_register_media_library_custom_column( $columns ) {

  $columns['mgjp_mv_info'] = 'Media Vault';

  return $columns;
}
add_filter( 'manage_upload_columns', 'mgjp_mv_register_media_library_custom_column' );


/**
 * Render function for Media Vault custom column in WP Media Library list table.
 *
 * @since 0.4
 *
 * @uses mgjp_mv_get_the_permissions()
 * @param $column_name string name-id of current column
 * @param $post_id int ID of post being evaluated
 */
function mgjp_mv_render_media_library_custom_column( $column_name, $post_id ) {

  if ( 'mgjp_mv_info' != $column_name )
    return;

  if ( ! $permission = mgjp_mv_get_the_permission( $post_id ) )
    return;

  $permissions = mgjp_mv_get_the_permissions();

  $permission = isset( $permissions[$permission] ) ? $permissions[$permission] : '';

  $description = isset( $permission['description'] ) && ! empty( $permission['description'] ) ?
                  esc_html( $permission['description'] ) :
                  '<span class="mgjp-mv-error">'
                  . esc_html__( 'Undetermined! Permissions have been misconfigured for this attachment!', 'media-vault' )
                  . '</span>'; ?>

    <em><?php esc_html_e( 'Protected Media', 'media-vault' ); ?></em>

    <p>

      <div><?php esc_html_e( 'Files accessible to:', 'media-vault' ); ?></div>

      <em><?php echo $description; ?></em>

    </p>

  <?php
}
add_action( 'manage_media_custom_column', 'mgjp_mv_render_media_library_custom_column', 10, 2 );


/**
 * Add some simple styles to the WP Media Library page to prettify
 * the Media Vault custom column
 *
 * @since 0.4
 */
function mgjp_mv_media_library_custom_column_styles() { ?>
  
  <style type="text/css">
    .column-mgjp_mv_info {
      width: 120px;
    }
  </style>

  <?php
}
add_action( 'admin_head-upload.php', 'mgjp_mv_media_library_custom_column_styles' );


/**
 * Add Media Vault bulk actions to WP Media Library list table
 * using javascript
 *
 * @since 0.3
 */
function mgjp_mv_add_media_library_bulk_actions_js() {

  if ( ! current_user_can( 'edit_posts' ) )
    return;

  $bulk_actions = array();
  if ( ! isset( $_GET['mgjp-mv-show-protected'] ) ) // hide action on 'Show Protected' media library page
    $bulk_actions['mgjp-mv-protect'] = esc_html__( 'Add to Protected', 'media-vault' );
  if ( ! isset( $_GET['mgjp-mv-show-unprotected'] ) ) // hide action on 'Show Unprotected' media library page
    $bulk_actions['mgjp-mv-unprotect'] = esc_html__( 'Remove from Protected', 'media-vault' );

  ?>

  <script type="text/javascript">
    jQuery(function($) {

      $.each(<?php echo json_encode( $bulk_actions ); ?>, function(index, value) {
        $('<option>')
          .val(index)
          .text(value)
          .appendTo('select[name="action"]')
          .clone()
          .appendTo('select[name="action2"]');
      });

    }(jQuery));
  </script>

  <?php
}
add_action( 'admin_footer-upload.php', 'mgjp_mv_add_media_library_bulk_actions_js' );

/**
 * Add admin notices to media library page to show when actions are successful / or not.
 *
 * @since 0.3
 */
function mgjp_mv_add_media_library_admin_notices() {

  $screen = get_current_screen();
  if ( 'upload' === $screen->id ) {

    if ( isset( $_REQUEST['mgjp-mv-protected'] ) && (int) $_REQUEST['mgjp-mv-protected'] ) {
      $message = sprintf(
        _n(
          'Media file is now protected.',       //singular
          '%s media files are now protected.',  //plural
          $_REQUEST['mgjp-mv-protected'],
          'media-vault'
        ), number_format_i18n( $_REQUEST['mgjp-mv-protected'] )
      );
      echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
      $_SERVER['REQUEST_URI'] = remove_query_arg( 'mgjp-mv-protected', $_SERVER['REQUEST_URI'] );
    }

    if ( isset( $_REQUEST['mgjp-mv-unprotected'] ) && (int) $_REQUEST['mgjp-mv-unprotected'] ) {
      $message = sprintf(
        _n(
          'Removed file protection on media file.',       //singular
          'Removed file protection on %s media files.',   //plural
          $_REQUEST['mgjp-mv-unprotected'],
          'media-vault'
        ), number_format_i18n( $_REQUEST['mgjp-mv-unprotected'] )
      );
      echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
      $_SERVER['REQUEST_URI'] = remove_query_arg( 'mgjp-mv-unprotected', $_SERVER['REQUEST_URI'] );
    }

  }
}
add_action( 'admin_notices', 'mgjp_mv_add_media_library_admin_notices' );



/** Handle Media Vault bulk actions **/
$wp_list_table = _get_list_table( 'WP_Media_List_Table' );
$action = $wp_list_table->current_action();

$allowed_actions = array(
  'mgjp-mv-protect',
  'mgjp-mv-unprotect'
);
if ( ! in_array( $action, $allowed_actions ) ) return;

check_admin_referer( 'bulk-media' );

if ( isset( $_REQUEST['media'] ) )
  $media_ids = array_map( 'intval', $_REQUEST['media'] );

if ( empty( $media_ids ) ) return;

$location = 'upload.php';
if ( $referer = wp_get_referer() ) {
  if ( false !== strpos( $referer, 'upload.php' ) )
    $location = remove_query_arg(
      array( 'mgjp-mv-protected', 'mgjp-mv-unprotected', 'trashed', 'untrashed', 'deleted', 'message', 'ids', 'posted' ),
      $referer
    );
}

$pagenum = $wp_list_table->get_pagenum();
if ( $pagenum > 1 )
  $location = add_query_arg( 'paged', $pagenum, $location );

require_once( plugin_dir_path( __FILE__ ) . 'includes/mgjp-functions.php' );

switch( $action ) {

  case 'mgjp-mv-protect':
    if ( ! current_user_can( 'edit_posts' ) )
      wp_die( __( 'You are not allowed to add attachments to the protected directory.', 'media-vault' ) );

    $protected = 0;
    foreach ( (array) $media_ids as $media_id ) {

      if ( ! current_user_can( 'edit_post', $media_id ) )
        continue;

      if ( mgjp_mv_is_protected( $media_id ) )
        continue;

      $move = mgjp_mv_move_attachment_to_protected( $media_id );

      if ( is_wp_error( $move ) )
        wp_die( __( 'There was an error moving the files to the protected directory.', 'media-vault' ) . '<br/>' .  $move->get_error_message() );

      $protected++;
    }

    $location = add_query_arg( array(
      'mgjp-mv-protected' => $protected,
      'ids'               => join( ',', $media_ids )
    ), $location );
    break;

  case 'mgjp-mv-unprotect':
    if ( ! current_user_can( 'edit_posts' ) )
      wp_die( __( 'You are not allowed to remove attachments from the protected directory.', 'media-vault' ) );

    $unprotected = 0;
    foreach ( (array) $media_ids as $media_id ) {

      if ( ! current_user_can( 'edit_post', $media_id ) )
        continue;

      if ( ! mgjp_mv_is_protected( $media_id ) )
        continue;

      $move = mgjp_mv_move_attachment_from_protected( $media_id );

      if ( is_wp_error( $move ) )
        wp_die( __( 'There was an error moving the files from the protected directory.', 'media-vault' ) . '<br/>' .  $move->get_error_message() );

      delete_post_meta( $media_id, '_mgjp_mv_permission' );

      $unprotected++;
    }

    $location = add_query_arg( array(
      'mgjp-mv-unprotected' => $unprotected,
      'ids'                 => join( ',', $media_ids )
    ), $location );
    break;

  default: return;
}

$location = remove_query_arg( array( 'action', 'action2', 'media' ), $location );

wp_redirect( $location );
exit();