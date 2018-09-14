<?php
/**
 * MGJP_MV_Update class handles the necessary
 * functions to update the plugin from previous
 * versions.
 *
 * @package WordPress_Plugins
 * @package MediaVault
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */


/**
 * MGJP_MV_Update class handles the necessary
 * functions to update the plugin from previous
 * versions.
 *
 * @since 0.8
 *
 * @param $version_db string the plugin version as stored in the db
 * @param $version_cur string the current plugin version
 * @param $option_key string the option_key for the option row where the 
 *                           version string is saved in the database
 */
class MGJP_MV_Update {

  /**
   * associative array of version numbers below which
   * an update is required
   * array key = version number less than which this update callback must run
   * array value = the update callback to run
   *
   * @since 0.8
   */
  var $updates = array(
    '0.8'   => 'update_08',
    '0.8.5' => 'update_085'
  );

  /**
   * Boolean holds whether the page should reload
   * after all the update scripts have run
   *
   * @since 0.8.5
   */
  var $force_reload = false;

  /**
   * Compare the current version with the version updated
   * from, determine the update functions that are required
   * to run and then run them sequentially 
   *
   * @since 0.8
   *
   * @param $version_db string the plugin version as stored in the db
   * @param $version_cur string the current plugin version
   * @param $option_key string the option_key for the option row where the 
   *                           version string is saved in the database
   */
  function __construct( $version_db, $version_cur, $option_key ) {

    $versions = array_keys( $this->updates );

    foreach ( $versions as $version ) {
      if ( version_compare( $version_db, $version, 'lt' ) ) {
        $updates_start = $version;
        break;
      }
    }

    if ( ! isset( $updates_start ) )
      return update_site_option( $option_key, $version_cur );

    $updates_todo = array_slice(
      $this->updates,
      array_search(
        $updates_start,
        $versions
    ) );

    foreach ( $updates_todo as $update ) {
      if ( is_callable( array( &$this, $update ) ) )
        call_user_func( array( &$this, $update ) );
    }

    update_site_option( $option_key, $version_cur );

    if ( ! $this->force_reload )
      return;

    $current_url = esc_url_raw( '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

    if ( headers_sent() )
      echo '<meta http-equiv="refresh" content="' . esc_attr( "0;url=$current_url" ) . '" />';
    else
      wp_redirect( $current_url );
    exit;
  }

  /**
   * Updates plugin data from pre-version 0.8 to version 0.8
   * Loads the Media Vault place-holder image
   * which is used from 0.8 onwards to replace
   * images a user does not have permission to view.
   * Modifies the db replacing all references to the deprecated
   * `mgjp_mv_meta` post meta key with the new `_mgjp_mv_permission`
   * post meta key.
   *
   * @since 0.8
   *
   * @uses mgjp_mv_load_placeholder_image()
   */
  function update_08() {

    // edit existing options
    $default_permission = get_option( 'mgjp_mv_default_permission', 'logged-in' );
    delete_option( 'mgjp_mv_default_permission' );
    add_option( 'mgjp_mv_default_permission', $default_permission, '', 'yes' );

    // add options
    add_option( 'mgjp_mv_ir', array( 'is_on' => true ), '', 'no' );

    // load place-holder image into Media Library
    mgjp_mv_load_placeholder_image();

    /** correctly replace old `mgjp_mv_meta` meta with new `_mgjp_mv_permission` meta **/
    global $wpdb;

    // get all posts with the old 'mgjp_mv_meta' meta key
    $old = $wpdb->get_results(
      $wpdb->prepare(
        "
        SELECT      meta_id, meta_value
        FROM        $wpdb->postmeta
        WHERE       meta_key = %s
        ",
        'mgjp_mv_meta'
      ), ARRAY_A
    );

    if ( empty( $old ) )
      return;

    // distill the meta values which have custom
    // permissions set on them
    foreach ( $old as $columns ) {
      $meta = unserialize( $columns['meta_value'] );
      if ( ! isset( $meta['permission'] ) )
        continue;
      $ids[] = $columns['meta_id'];
      $data[$meta['permission']][] = $columns['meta_id'];
    }

    if ( ! isset( $data ) )
      return delete_post_meta_by_key( 'mgjp_mv_meta' );

    // build the sql update query to convert the old meta system
    // using 'mgjp_mv_meta' to the new meta system using '_mgjp_mv_permission'
    $sql_update[] = $wpdb->prepare( "UPDATE `$wpdb->postmeta` SET `meta_key` = %s, `meta_value` = CASE", '_mgjp_mv_permission' );
    foreach ( $data as $meta_value => $meta_ids ) {
      if ( isset( $meta_ids[1] ) )
        $sql_update[] = $wpdb->prepare( "WHEN `meta_id` IN (" . implode( ', ', array_fill( 0, count( $meta_ids ), '%d' ) ) . ") THEN %s", array_merge( $meta_ids, array( $meta_value ) ) );
      else
        $sql_update[] = $wpdb->prepare( "WHEN `meta_id` = %d THEN %s", $meta_ids[0], $meta_value );
    }
    $sql_update[] = $wpdb->prepare( "ELSE %s END WHERE `meta_id` IN (" . implode( ', ', array_fill( 0, count( $ids ), '%d' ) ) . ")", array_merge( array( 'logged-in' ), $ids ) );

    // run the update query
    $wpdb->query( implode( ' ', $sql_update ) );

    // and delete all other references to 'mgjp_mv_meta'
    delete_post_meta_by_key( 'mgjp_mv_meta' );
  }

  /**
   * Verifies that the rewrite rules required for Media Vault
   * to function correctly are set and functioning. If they are
   * it enables all the plugin's functionality and sets the
   * force_reload flag for the update class
   *
   * @since 0.8.5
   *
   * @uses mgjp_mv_check_rewrite_rules()
   */
  function update_085() {

    if ( ! mgjp_mv_check_rewrite_rules() )
      return;

    update_site_option( 'mgjp_mv_enabled', true );

    $this->force_reload = true;

  }

} // END of class MGJP_MV_Update


?>