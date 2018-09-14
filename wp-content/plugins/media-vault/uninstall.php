<?php
/**
 * Functions to completely uninstall all Media Vault
 * settings, options, meta and loaded files
 *
 * @package WordPress_Plugins
 * @package MediaVault
 *
 * @author Max G J Panas <http://maxpanas.com/>
 * @license GPL-3.0+
 */


/** Make sure this file is not being called directly **/
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! current_user_can( 'delete_plugins' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}



/**
 * Media Vault internal Uninstall function for a single
 * blog install or for each blog site
 * in network activation mode
 *
 * @since 0.8.5
 */
function _mgjp_mv_uninstall_local( $blog_id = 0 ) {

  // Delete the default Media Vault placeholder image if it
  // still exists
  $ir['default'] = get_option( 'mgjp_mv_ir' );
  if ( $ir['default'] && wp_attachment_is_image( $ir['default'] ) )
    wp_delete_attachment( $ir['default'], true );

  // Delete all Media Vault local options from the local options table
  delete_option( 'mgjp_mv_version' );
  delete_option( 'mgjp_mv_default_permission' );
  delete_option( 'mgjp_mv_options' );
  delete_option( 'mgjp_mv_ir' );

  // Delete all Media Vault attachment metadata from the local postmeta table
  delete_post_meta_by_key( '_mgjp_mv_permission' );
  delete_post_meta_by_key( 'mgjp_mv_meta' );
}



// Flush rewrite rules to remove all Media Vault rewrite rules from
// the site's .htaccess file
remove_filter( 'mod_rewrite_rules', 'mgjp_mv_add_plugin_rewrite_rules' );
flush_rewrite_rules();


// Delete all Media Vault network-wide options from the options table
delete_site_option( 'mgjp_mv_version' );
delete_site_option( 'mgjp_mv_enabled' );
delete_site_option( 'mgjp_mv_deactivation' );


if ( ! is_multisite() ) {

  // run the uninstall function for the single site
  _mgjp_mv_uninstall_local();

} else if ( ! wp_is_large_network() ) {
  global $wpdb;

  $blog_ids = $wpdb->get_col( "SELECT `blog_id` FROM `$wpdb->blogs`" );

  // run the uninstall function for each site in the network
  foreach ( $blog_ids as $blog_id ) {

    switch_to_blog( $blog_id );
    _mgjp_mv_uninstall_local( $blog_id );
    restore_current_blog();

  }
}