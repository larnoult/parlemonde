<?php
/**
 * General WordPress related Functions Library
 *
 * @package WordPress
 * @package MGJP_Functions
 *
 * @version 1.0
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */


/**
 * !WIP: NOT FOR USE: convert a path to be relative to the wp uploads dir if
 * possible, otherwise return false. similar to _wp_relative_upload_path()
 *
 * @since 1.0
 *
 * @param $path string Path to check against WP upload dir
 * @returns string on success $path converted to relative of WP upload dir
 * @returns bool on failure if such conversion is impossible
 */
if ( ! function_exists( 'mgjp_wp_relative_upload_path' ) ) {
  function mgjp_wp_relative_upload_path( $path ) {
    $path = ltrim( $path, '/' );

    if ( ! path_is_absolute( $path ) )
      return $path;

    $upload_dir = wp_upload_dir();

    if ( 0 === strpos( $path, $upload_dir['basedir'] ) )
      $path = str_replace( $upload_dir['basedir'], '', $path );

    else if ( 0 === strpos( $path, $upload_dir['baseurl'] ) )
      $path = str_replace( $upload_dir['baseurl'], '', $path );

    else $path = false;

    if ( $path )
      $path = ltrim( $path, '/' );

    return $path;
  }
}


/**
 * move an attachment's files to another folder within
 * the WordPress uploads directory
 *
 * @since 1.0
 *
 * @param $attachment_id int the ID of the attachment whose files we want to move
 * @param $new_reldir string the new path to the attachment relative to the WP uploads directory
 * @return object | bool Returns WP_Error on failure and True on success
 */
if ( ! function_exists( 'mgjp_move_attachment_files' ) ) {
  function mgjp_move_attachment_files( $attachment_id, $new_reldir ) {

    // basic sanity checks
    if ( 'attachment' != get_post_type( $attachment_id ) )
      return new WP_Error( 'not_attachment', sprintf(
        __( 'The post with ID: %d is not an attachment post type.', 'media-vault' ),
        $attachment_id
      ) );

    if ( path_is_absolute( $new_reldir ) )
      return new WP_Error( 'new_reldir_not_relative', sprintf(
        __( 'The new path provided: %s is absolute. The new path must be a path relative to the WP uploads directory.', 'media-vault' ),
        $new_relpath
      ) );


    // Get all file related attachment meta data
    $meta = wp_get_attachment_metadata( $attachment_id );                             // meta_key => '_wp_attachment_metadata'

    $file = get_post_meta( $attachment_id, '_wp_attached_file', true );               // meta_key => '_wp_attached_file'

    $backups = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );  // meta_key => '_wp_attachment_backup_sizes'


    // Determine the full paths to the directory where the file
    // currently is and to the directory we want to put the file in.
    $upload_dir = wp_upload_dir();

    $old_reldir = dirname( $file );
    if ( in_array( $old_reldir, array( '\\', '/', '.' ), true ) )
      $old_reldir = '';

    // If the files are already in the new directory, we don't need
    // to do anything further.
    if ( $new_reldir === $old_reldir )
      return 'sfdfdsfsdd';

    $old_fulldir = path_join( $upload_dir['basedir'], $old_reldir );
    $new_fulldir = path_join( $upload_dir['basedir'], $new_reldir );


    // Make sure the directory we want to put the files into exists
    // otherwise create it, while setting appropriate permissions.
    if ( ! wp_mkdir_p( $new_fulldir ) )
      return new WP_Error( 'wp_mkdir_p_error', sprintf(
        __( 'There was an error making or verifying the directory at: %s', 'media-vault' ),
        $new_fulldir
      ) );


    // Get all filenames for all attached files
    $intermediate_sizes = array();
    if ( is_array( $meta['sizes'] ) ) {
      foreach ( $meta['sizes'] as $size ) {
        $intermediate_sizes[] = $size['file'];
      }
    }

    $backup_sizes = array();
    if ( is_array( $backups ) ) {
      foreach ( $backups as $size ) {
        $backup_sizes[] = $size['file'];
      }
    }

    $old_basenames = $new_basenames = array_merge(
      array( basename( $file ) ),
      $intermediate_sizes,
      $backup_sizes
    );


    // Determine the original filename, to be used to update the guid
    // and if we need to change the filenames because there is already
    // a file with the same name in the destination directory
    $orig_basename = basename( $file );
    if ( is_array( $backups ) && isset( $backups['full-orig'] ) )
      $orig_basename = $backups['full-orig']['file'];


    // Make sure we are not overwriting any existing files in the
    // destination folder. Add numerical increment to filename until
    // there are no conflicts.

    // prep for filename conflict script
    $orig_filename = pathinfo( $orig_basename );
    $orig_filename = $orig_filename['filename'];
    $conflict = true;
    $number = 1;
    $separator = '#';
    $med_filename = $orig_filename;

    while ( $conflict ) {

      $conflict = false;
      foreach ( $new_basenames as $basename ) {
        if ( is_file( path_join( $new_fulldir, $basename ) ) ) {
          $conflict = true;
          break;
        }
      }

      // filename conflict script
      if ( $conflict ) {
        $new_filename = "$orig_filename$number";
        $number++;
        $pattern = "$separator$med_filename";
        $replace = "$separator$new_filename";
        $new_basenames = explode(
          $separator,
          ltrim(
            str_replace( $pattern, $replace, $separator . implode( $separator, $new_basenames ) ),
            $separator
          )
        );
        $med_filename = $new_filename;
      }
    }


    // php rename() all filepaths in old directory to new path

    // remove duplicate basenames to prevent uneccessary renames
    // from happening
    $unique_old_basenames = array_values( array_unique( $old_basenames ) );
    $unique_new_basenames = array_values( array_unique( $new_basenames ) );

    $i = count( $unique_old_basenames );
    while ( $i-- ) {

      $old_fullpath = path_join( $old_fulldir, $unique_old_basenames[$i] );
      $new_fullpath = path_join( $new_fulldir, $unique_new_basenames[$i] );

      rename( $old_fullpath, $new_fullpath );

      if ( ! is_file( $new_fullpath ) )
        return new WP_Error(
          'rename_failed',
          sprintf(
            __( 'Rename failed when trying to move file from: %s, to: %s', 'media-vault' ),
            $old_fullpath,
            $new_fullpath
          )
        );
    }


    // Update all attachment filepaths in database to point to the new location

    // $new_basenames[0] should always be the basename of the file
    // from '_wp_attached_media' with the new conflict free filename
    $meta['file'] = path_join( $new_reldir, $new_basenames[0] );
    update_post_meta( $attachment_id, '_wp_attached_file', $meta['file'] );

    // if $new_basenames != $old_basenames we must update the
    // original basename used in the guid as well as the metadata
    // of the intermediate and backup sizes to reflect the
    // filename changes
    if ( $new_basenames[0] != $old_basenames[0] ) {

      // if $new_basenames != $old_basenames that means the 
      // filename conflict script has run and therefore
      // $pattern & $replace are defined
      $orig_basename = ltrim (
        str_replace( $pattern, $replace, $separator . $orig_basename ),
        $separator
      );

      if ( is_array( $meta['sizes'] ) ) {
        $i = 0;

        foreach ( $meta['sizes'] as $size => $data ) {
          $meta['sizes'][$size]['file'] = $new_basenames[++$i];
        }
      }

      if ( is_array( $backups ) ) {
        $i = 0;
        $l = count( $backups );
        $new_backup_sizes = array_slice( $new_basenames, -$l, $l );

        foreach ( $backups as $size => $data ) {
          $backups[$size]['file'] = $new_backup_sizes[$i++];
        }
        update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backups );
      }

    }

    update_post_meta( $attachment_id, '_wp_attachment_metadata', $meta );

    $guid = path_join( $new_fulldir, $orig_basename );                                // should I be updating the GUID? the Codex says I should
    // just in case someone wants to disable updating the guid:                       // for attachments.
    if ( apply_filters( 'mgjp_update_guid_on_attachment_files_move', true ) )
      wp_update_post( array( 'ID' => $attachment_id, 'guid' => $guid ) );


    // NOT IMPLEMENTED YET: If $rewrite_whole_db flag is set, sanely search through database for instances of 
    // old filepath and replace them with new filepath
      // database tables to look through: -> ?


    return true;
  }
}

?>