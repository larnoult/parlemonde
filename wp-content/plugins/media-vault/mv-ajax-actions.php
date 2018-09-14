<?php
/**
 * Media Vault Admin Ajax Handling.
 *
 * @package WordPress_Plugin
 * @package MediaVault
 *
 * @author Max G J Panas <m@maxpanas.com>
 * @license GPL-3.0+
 */


// forbid direct calls to this file without wp ajax constants
if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}


/**
 * Get the HTML image element of an attachment via AJAX
 *
 * @since 0.8
 *
 * @return string HTML image element of attachment file,
 *                if there is any, otherwise return 0
 */
function mgjp_mv_get_attachment_image() {

  $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : '';
  $size = isset( $_GET['size'] ) ? $_GET['size'] : 'thumbnail';
  $icon = isset( $_GET['icon'] ) ? ! ! $_GET['icon'] : false;
  $args = isset( $_GET['args'] ) ? $_GET['args'] : null;

  $html = wp_get_attachment_image( $id, $size, $icon, $args );
  if ( empty( $html ) )
    wp_die( -1 );

  wp_die( $html );
}
add_action( 'wp_ajax_mgjp_mv_get_attachment_image', 'mgjp_mv_get_attachment_image' );


/**
 * Attempt to restore the default placeholder image
 *
 * @since 0.8
 *
 * @return array [0] 
 *               [1] 
 */
function mgjp_mv_restore_default_placeholder_image() {

  if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) )
    wp_die( -1 );

  check_ajax_referer( 'mgjp_mv_ir_restore_default', 'nonce' );

  $size = isset( $_POST['size'] ) ? $_POST['size']: 'thumbnail';
  $args = isset( $_GET['args'] ) ? $_GET['args'] : null;

  $ir_id = mgjp_mv_load_placeholder_image( true );
  if ( ! $ir_id )
    wp_die( -1 );

  wp_die( json_encode( array(
    'id'  => $ir_id,
    'img' => wp_get_attachment_image( $ir_id, $size, false, $args )
  ) ) );
}
add_action( 'wp_ajax_mgjp_mv_restore_default_placeholder_image', 'mgjp_mv_restore_default_placeholder_image' );


/**
 * Render the Media Vault attachment edit fields in
 * the Media Upload modal
 *
 * @since 0.8.9
 *
 * @uses mgjp_mv_get_the_permissions()
 * @uses mgjp_mv_is_protected()
 * @return array if called in the Media Modal, adds a Media Vault field
 *               to the attachment fields to edit array
 */
function mgjp_mv_add_attachment_edit_fields( $form_fields, $post ) {

  // only add the field to the Media Upload Modal, and not the attachment
  // edit page, we have the Media Vault Protection Settings metabox for
  // that job
  if ( get_current_screen() !== null )
    return $form_fields;

  $permission = get_post_meta( $post->ID, '_mgjp_mv_permission', true );

  $permissions = mgjp_mv_get_the_permissions();

  if ( empty( $permission ) || ! isset( $permissions[$permission] ) )
    $permission = 'default';

  $default    = array(
    'default' => array(
      'select' => __( 'Use Default Setting', 'media-vault' )
    )
  );
  $permissions = $default + $permissions;

  ob_start(); ?>
  
    <tr id="mgjp_mv_attachment_fields" class="mgjp_mv_attachment_fields">

      <th><?php esc_html_e( 'Media Vault Protection Settings', 'media-vault'); ?></th>

      <td>

        <label for="attachments[<?php echo $post->ID; ?>][mgjp_mv_protection_toggle]" class="button">

          <input type="hidden" name="attachments[<?php echo $post->ID ?>][mgjp_mv_protection_toggle]" value="off">
          <input class="mgjp_mv_protection_toggle" type="checkbox" id="attachments[<?php echo $post->ID; ?>][mgjp_mv_protection_toggle]" name="attachments[<?php echo $post->ID; ?>][mgjp_mv_protection_toggle]" <?php checked( mgjp_mv_is_protected( $post->ID ) ); ?>>

          <?php esc_html_e( 'Protect files', 'media-vault' ); ?>

        </label>

        <p id="mgjp_mv_attachment_permissions_field">

          <label for="attachments[<?php echo $post->ID; ?>][mgjp_mv_permission_select]"><?php esc_html_e( 'File access permission:', 'media-vault' ); ?></label>

          <select class="mgjp_mv_permission_select" id="attachments[<?php echo $post->ID; ?>][mgjp_mv_permission_select]" name="attachments[<?php echo $post->ID; ?>][mgjp_mv_permission_select]">

            <?php foreach ( $permissions as $key => $data ) : ?>
              <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $permission, $key ); ?>>
                <?php echo esc_html( $data['select'] ); ?>
              </option>
            <?php endforeach; ?>

          </select>

        </p>

        <script>
          jQuery(function ($) {
            $('#mgjp_mv_attachment_fields').trigger('mgjpMvLoaded', <?php echo $post->ID; ?>);
          }(jQuery));
        </script>

      <td>

    </tr>

  <?php

  $form_fields['mgjp_mv_permission_fields']['tr'] = ob_get_clean();

  return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'mgjp_mv_add_attachment_edit_fields', 10, 2 );


/**
 * Saves changes to attachment fields in
 * the Media Upload/Library modal
 *
 * @since 0.8.9
 *
 * @uses mgjp_mv_move_attachment_from_protected()
 * @uses mgjp_mv_move_attachment_to_protected()
 * @uses mgjp_mv_get_the_permissions()
 */
function mgjp_mv_save_attachment_edit_fields( $post, $attachment ) {

  if ( ! isset( $attachment['mgjp_mv_protection_toggle'] ) )
    return $post;

  $attachment_id = $post['ID'];

  switch ( $attachment['mgjp_mv_protection_toggle'] ) {

    case 'off' :
      remove_action( 'edit_attachment', 'mgjp_mv_save_attachment_metabox_data' );

      $move = mgjp_mv_move_attachment_from_protected( $attachment_id );

      add_action( 'edit_attachment', 'mgjp_mv_save_attachment_metabox_data' );

      if ( is_wp_error( $move ) )
        return $post;

      delete_post_meta( $attachment_id, '_mgjp_mv_permission' );

      return $post;

    case 'on':
      remove_action( 'edit_attachment', 'mgjp_mv_save_attachment_metabox_data' );

      $move = mgjp_mv_move_attachment_to_protected( $attachment_id );

      add_action( 'edit_attachment', 'mgjp_mv_save_attachment_metabox_data' );

      if ( is_wp_error( $move ) )
        return $post;

      if ( ! isset( $attachment['mgjp_mv_permission_select'] ) || empty( $attachment['mgjp_mv_permission_select'] ) )
        return $post;

      $permissions = mgjp_mv_get_the_permissions();

      if ( 'default' == $attachment['mgjp_mv_permission_select'] || ! isset( $permissions[$attachment['mgjp_mv_permission_select']] ) )
        delete_post_meta( $attachment_id, '_mgjp_mv_permission' );
      else
        update_post_meta( $attachment_id, '_mgjp_mv_permission', $attachment['mgjp_mv_permission_select'] );

      return $post;

    default: return $post;
  }
}
add_filter( 'attachment_fields_to_save', 'mgjp_mv_save_attachment_edit_fields', 10, 2 );

?>