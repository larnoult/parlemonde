<?php
/*
Plugin Name: Media Vault
Plugin URI: http://wordpress.org/plugins/media-vault/
Description: Protect attachment files from direct access using powerful and flexible restrictions. Offer safe download links for any file in your uploads folder.
Network: true
Text Domain: media-vault
Domain Path: /languages
Version: 0.8.12
Author: Max GJ Panas
Author URI: http://maxpanas.com
License: GPLv3 or later

Copyright 2013 Maximilianos G J Panas (email : m@maxpanas.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 3, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



// define current plugin version constant
define( 'MGJP_MV_VERSION', '0.8.12' );


/**
 * The default Media Vault permissions array
 *
 * @since 0.4
 *
 * @global $mgjp_mv_permissions array Array of default Media Vault file access permissions
 */
global $mgjp_mv_permissions;
$mgjp_mv_permissions = array(
  'admin'     =>  array(
      'description'  => __( 'Admin users only', 'media-vault' ),
      'select'       => __( 'Admin users', 'media-vault' ),
      'logged_in'    => true,
      'run_in_admin' => true,
      'cb'           => 'mgjp_mv_check_admin_permission'
    ),
  'author'    =>  array(
      'description'  => __( 'The file\'s author', 'media-vault' ),
      'select'       => __( 'The file\'s author', 'media-vault' ),
      'logged_in'    => true,
      'run_in_admin' => true,
      'cb'           => 'mgjp_mv_check_author_permission'
    ),
  'logged-in' =>  array(
      'description'  => __( 'All logged-in users', 'media-vault' ),
      'select'       => __( 'Logged-in users', 'media-vault' ),
      'logged_in'    => true,
      'run_in_admin' => false,
      'cb'           => false
    ),
  'all'       =>  array(
      'description'  => __( 'Anyone', 'media-vault' ),
      'select'       => __( 'Anyone', 'media-vault' ),
      'logged_in'    => false,
      'run_in_admin' => false,
      'cb'           => false
    )
);


register_activation_hook( __FILE__, 'mgjp_mv_activate' );
register_deactivation_hook( __FILE__, 'mgjp_mv_deactivate' );

add_action( 'plugins_loaded', 'mgjp_mv_textdomain' );

add_action( 'init', 'mgjp_mv_check_version' );

add_action( 'load-plugins.php', 'mgjp_mv_on_deactivation_request' );

if ( get_site_option( 'mgjp_mv_enabled' ) ) {

  add_action( 'init', 'mgjp_mv_handle_file_request', 0 );
  add_action( 'init', 'mgjp_mv_register_shortcodes' );

  add_action( 'wp_enqueue_media', 'mgjp_mv_attachment_edit_fields_styles_and_scripts' );

  add_filter( 'mod_rewrite_rules', 'mgjp_mv_add_plugin_rewrite_rules' );

  add_filter( 'upload_dir', 'mgjp_mv_change_upload_directory', 999 );

  add_filter( 'user_has_cap', 'mgjp_mv_edit_capabilities', 999, 3 );

  add_filter( 'image_downsize', 'mgjp_mv_replace_protected_image', 999, 3 );

  if ( is_admin() ) {

    add_action( 'admin_init', 'mgjp_mv_ajax_actions_include', 0 );
    add_action( 'admin_init', 'mgjp_mv_media_vault_options_include' );
    add_action( 'admin_init', 'mgjp_mv_attachment_metabox_include' );

    add_action( 'load-media-new.php', 'mgjp_mv_media_new_options_include' );
    add_action( 'load-upload.php', 'mgjp_mv_media_library_options_include' );

    add_filter( 'admin_body_class', 'mgjp_add_mp6_admin_body_class' );

    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mgjp_mv_settings_link' );

  }

} else {

  include( plugin_dir_path( __FILE__ ) . 'mv-extra-activation-steps.php' );

}

if ( get_site_option( 'mgjp_mv_deactivation' ) )
  include( plugin_dir_path( __FILE__ ) . 'mv-extra-deactivation-steps.php' );


//-----------------------------------------------------------------------//
// MEDIA VAULT - PERMISSION CHECKING FUNCTIONS
//-----------------------------------------------------------------------//


/**
 * The 'admin' permission checking callback.
 *
 * @since 0.4
 */
function mgjp_mv_check_admin_permission() {
  if ( ! current_user_can( 'manage_options' ) )
    return new WP_Error( 'not_admin', __( 'You do not have sufficient permissions to view this file.', 'media-vault' ) );

  return true;
}

/**
 * The 'author' permission checking callback.
 *
 * @since 0.4
 */
function mgjp_mv_check_author_permission( $attachment_id ) {

  if ( current_user_can( 'manage_options' ) )
    return true;

  if ( ! isset( $attachment_id ) || empty( $attachment_id ) )
    return new WP_Error( 'no_id', __( 'There was an error determining this attachment\'s author. Please contact the website administrator.', 'media-vault' ) );

  if ( get_current_user_id() != get_post_field( 'post_author', $attachment_id, 'raw' ) )
    return new WP_Error( 'not_author', __( 'You do not have sufficient permissions to view this file.', 'media-vault' ) );

  return true;
}



//-----------------------------------------------------------------------//
// MEDIA VAULT - MAIN HOOKED FUNCTIONS
//-----------------------------------------------------------------------//


/**
 * On plugin activation
 *
 * @since 0.1
 *
 * @uses _mgjp_mv_activate_local()
 */
function mgjp_mv_activate( $network_activating ) {

  global $is_apache;

  if ( $is_apache
      && ! is_multisite()
      && get_option( 'permalink_structure' )
      && got_mod_rewrite()
      && is_writable( get_home_path() . '.htaccess' ) ) {

    // register plugin enabled option
    update_site_option( 'mgjp_mv_enabled', true );

    // Flush rewrite rules to add Media Vault rewrite rules to the
    // site's .htaccess file on plugin activation
    add_filter( 'mod_rewrite_rules', 'mgjp_mv_add_plugin_rewrite_rules' );
    flush_rewrite_rules();
  }

  // register Media Vault's other network-wide options
  add_site_option( 'mgjp_mv_version', MGJP_MV_VERSION, '', 'yes' );
  delete_site_option( 'mgjp_mv_deactivation' );

  if ( ! is_multisite() ) {

    // run the activation function for the single site
    _mgjp_mv_activate_local();

  } else if ( ! wp_is_large_network() ) {
    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT `blog_id` FROM `$wpdb->blogs`" );

    // run the activation function for each site in the network
    foreach ( $blog_ids as $blog_id ) {

      switch_to_blog( $blog_id );
      _mgjp_mv_activate_local( $blog_id );
      restore_current_blog();

    }
  }
}


/**
 * On plugin deactivation
 *
 * @since 0.1
 *
 * @uses mgjp_mv_check_rewrite_rules()
 */
function mgjp_mv_deactivate( $network_deactivating ) {

  delete_site_option( 'mgjp_mv_deactivation' );
  delete_site_option( 'mgjp_mv_enabled' );

  // Flush rewrite rules to remove Media Vault rewrite rules from the
  // site's .htaccess file on plugin deactivation
  remove_filter( 'mod_rewrite_rules', 'mgjp_mv_add_plugin_rewrite_rules' );
  flush_rewrite_rules();

  if ( ! is_multisite() ) {

    // run the deactivation function for the single site
    _mgjp_mv_deactivate_local();

  } else if ( ! wp_is_large_network() ) {
    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT `blog_id` FROM `$wpdb->blogs`" );

    // run the deactivation function for each site in the network
    foreach ( $blog_ids as $blog_id ) {

      switch_to_blog( $blog_id );
      _mgjp_mv_deactivate_local( $blog_id );
      restore_current_blog();

    }
  }
}


/**
 * Load the plugin textdomain.
 *
 * @since 0.1
 */
function mgjp_mv_textdomain() {

  load_plugin_textdomain( 'media-vault', false, plugin_dir_path( __FILE__ ) . 'languages/' );

}


/**
 * Plugin update handling. Checks current version against
 * a version number stored in the database and performs any
 * necessary upgrades using the MGJP_MV_Update class
 *
 * @since 0.8
 *
 * @uses MGJP_MV_Update
 */
function mgjp_mv_check_version() {

  $option_key = 'mgjp_mv_version';

  $version_db = get_site_option( $option_key, '0' );

  if ( version_compare( $version_db, MGJP_MV_VERSION, 'eq' ) )
    return;

  if ( version_compare( $version_db, MGJP_MV_VERSION, 'gt' ) )
    return update_site_option( $option_key, MGJP_MV_VERSION );

  include( plugin_dir_path( __FILE__ ) . 'mv-class-update.php' );

  if ( class_exists( 'MGJP_MV_Update' ) )
    new MGJP_MV_Update( $version_db, MGJP_MV_VERSION, $option_key );

}


/**
 * Remove Media Vault from the plugins.php deactivation
 * actions if Media Vault needs extra steps in order
 * to deactivate
 *
 * @since 0.8.5
 *
 * @uses mgjp_mv_get_dirfile()
 * @uses mgjp_mv_is_deactivation_allowed()
 */
function mgjp_mv_on_deactivation_request() {

  if ( in_array( get_site_option( 'mgjp_mv_deactivation' ), array( 'allowed', 'temp' ) ) )
    return;

  $action = isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ?
    $_REQUEST['action'] :
    ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ?
      $_REQUEST['action2'] :
      false
    );

  if ( ! in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) )
    return;

  switch ( $action ) {
    case 'deactivate':
      if ( ! isset( $_REQUEST['plugin'] ) || mgjp_mv_get_dirfile() != $_REQUEST['plugin'] )
        return;

      if ( mgjp_mv_is_deactivation_allowed() )
        return;

      update_site_option( 'mgjp_mv_deactivation', 'disallowed' );

      $location = remove_query_arg( array( 'action', 'plugin', '_wpnonce' ), $_SERVER['REQUEST_URI'] );
      wp_redirect( $location );
      exit;
      break;
    case 'deactivate-selected':
      $plugin_dirfile = mgjp_mv_get_dirfile();

      if ( ! isset( $_POST['checked'] ) || ! in_array( $plugin_dirfile, (array) $_POST['checked'] ) )
        return;

      if ( mgjp_mv_is_deactivation_allowed() )
        return;

      update_site_option( 'mgjp_mv_deactivation', 'disallowed' );

      $_POST['checked'] = array_diff( $_POST['checked'], array( $plugin_dirfile ) );
      break;
  }
}


/**
 * Trigger protected media uploads file handling function
 * if 'file' GET parameter is set in URL on wp init
 *
 * @since 0.1
 *
 * @uses mgjp_mv_get_file()
 */
function mgjp_mv_handle_file_request() {

  if ( isset( $_GET['mgjp_mv_file'] ) && ! empty( $_GET['mgjp_mv_file'] ) ) {

    // used by @func mgjp_mv_check_rewrite_rules to verify rewrite rules are
    // set and working as intended
    if ( isset( $_GET['mgjp_mv_rewrite_test'] ) && $_GET['mgjp_mv_rewrite_test'] )
      die( 'pass' );

    require( plugin_dir_path( __FILE__ ) . 'mv-file-handler.php' );

    // Check if force download flag is set
    $force_download = isset( $_REQUEST['mgjp_mv_download'] ) ?
                        $_REQUEST['mgjp_mv_download'] :
                        '';

    if ( function_exists( 'mgjp_mv_get_file' ) ) {
      mgjp_mv_get_file( $_GET['mgjp_mv_file'], $force_download );
      exit; // This exit is important as all we want to do when a
            // media download is requested is to serve it and exit
            // If it is missing WP will continue serving the page
            // after the media file, thus breaking it
    }
  }
}


/**
 * Register Media Vault Shortcodes
 *
 * @since 0.5
 */
function mgjp_mv_register_shortcodes() {

  include( plugin_dir_path( __FILE__ ) . 'mv-shortcodes.php' );

  if ( function_exists( 'mgjp_mv_download_links_list_shortcode_handler' ) )
    add_shortcode( 'mv_dl_links', 'mgjp_mv_download_links_list_shortcode_handler' );

}


/**
 * Enqueue styles and scripts for Media Vault
 * attachment edit fields.
 *
 * @hook action 'wp_enqueue_media'
 *
 * @since 0.8.8
 */
function mgjp_mv_attachment_edit_fields_styles_and_scripts() {

  wp_enqueue_style( 'mgjp-mv-att-fields-css', plugins_url( 'css/mv-attachment-fields.css', __FILE__ ), 'all', null );
  wp_enqueue_script( 'mgjp-mv-att-fields-js', plugins_url( 'js/min/mv-attachment-fields.min.js', __FILE__ ), array( 'media-editor' ), null, true );

}


/**
 * Add the plugin rewrite rules to the WP rewrite
 * rules being written in the sitewide .htaccess file
 *
 * @since 0.8.5
 *
 * @uses mgjp_mv_get_the_rewrite_rules()
 * @param $rules string String containing all rewrite rules to be written in htaccess
 * @return string String containing all rewrite rules to be written in htaccess
 *                including Media Vault custom rewrite rules
 */
function mgjp_mv_add_plugin_rewrite_rules( $rules ) {

  $pattern = "RewriteRule ^index\.php$ - [L]\n";

  return str_replace( $pattern, "$pattern\n" . implode( "\n", mgjp_mv_get_the_rewrite_rules() ) . "\n\n", $rules );
}


/**
 * Change upload directory for media uploads to a protected
 * folder if the 'protected' post/get parameter has been set
 * during the upload process.
 *
 * @since 0.1
 *
 * @uses mgjp_mv_upload_dir()
 * @param $param array Array of path info for WP Upload Directory
 * @return array Array of path info for Media Vault protected directory
 */
function mgjp_mv_change_upload_directory( $param ) {

  if ( isset( $_POST['mgjp_mv_protected'] ) && 'on' == $_POST['mgjp_mv_protected'] ) {
    $param['subdir'] = mgjp_mv_upload_dir( $param['subdir'], true );
    $param['path']   = $param['basedir'] . $param['subdir'];
    $param['url']    = $param['baseurl'] . $param['subdir'];
  }

  return $param;
}


/**
 * Function for the 'user_has_cap' WP Core filter. Checks the permissions set
 * on an attachment before making it available to a user to edit/delete/read.
 *
 * @since 0.7
 *
 * @uses mgjp_mv_check_user_permitted()
 * @param $allcaps array Array of all user capabilities
 * @param $cap array  [0] string capability required
 * @param $args array [0] string capability requested
 *                    [1] int user ID
 *                    [2] int post ID
 * @return array @param $allcaps unchanged if user permitted to access post
 * @return array @param $allcaps with capability @param $cap[0] set to false
 */
function mgjp_mv_edit_capabilities( $allcaps, $cap, $args ) {

  $disallowed_caps = array(
    'edit_post',
    'delete_post',
    'read_post'
  );

  if ( ! in_array( $args[0], $disallowed_caps ) )
    return $allcaps;

  if ( ! isset( $args[2] ) )
    return $allcaps;

  // check if user is permitted to access the post
  if ( mgjp_mv_check_user_permitted( $args[2] ) )
    return $allcaps;

  $allcaps[$cap[0]] = false;

  return $allcaps;
}


/**
 * Replace requested image with a Media Vault place-holder
 * if the user is not permitted to view them
 *
 * @since 0.8
 *
 * @param $img mixed false based on wp-includes/media.php or array if other filter has affected it
 * @param $attachment_id int ID of the attachment whose image is being requested
 * @param $size string name-id of the dimensions of the requested image
 * @return mixed return the $url if the image is not protected
 * @return array [0] string URL to the Media Vault replacement image of the requested size
 *               [1] string width of the Media Vault replacement image
 *               [2] string height of the Media Vault replacement image
 *               [3] bool whether the url is for a resized image or not
 */
function mgjp_mv_replace_protected_image( $img, $attachment_id, $size ) {

  $ir = get_option( 'mgjp_mv_ir' );

  if ( ! isset( $ir['is_on'] ) || ! $ir['is_on'] )
    return $img;

  $upload_dir = wp_upload_dir();

  if ( isset( $img[0] ) && 0 !== strpos( ltrim( $img[0], $upload_dir['baseurl'] ), mgjp_mv_upload_dir( '/', true ) ) )
    return $img;

  if ( mgjp_mv_check_user_permitted( $attachment_id ) )
    return $img;

  if ( isset( $ir['id'] ) && ! mgjp_mv_is_protected( $ir['id'] ) ) {

    remove_filter( 'image_downsize', 'mgjp_mv_replace_protected_image', 999, 3 );
    $placeholder = wp_get_attachment_image_src( $ir['id'], $size );
    add_filter( 'image_downsize', 'mgjp_mv_replace_protected_image', 999, 3 );

    return $placeholder;

  } else {

    list( $width, $height ) = image_constrain_size_for_editor( 1024, 1024, $size );

    return array(
      plugins_url( 'imgs/media-vault-ir.jpg', __FILE__ ),
      $width,
      $height,
      false
    );

  }
}


/**
 * Include the Media Vault custom AJAX actions
 *
 * @since 0.8
 */
function mgjp_mv_ajax_actions_include() {

  if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
    include( plugin_dir_path( __FILE__ ) . 'mv-ajax-actions.php' );

}


/**
 * Include the plugin's general settings
 *
 * @since 0.4
 */
function mgjp_mv_media_vault_options_include() {

  include( plugin_dir_path( __FILE__ ) . 'mv-options-media-vault.php' );

}


/**
 * Include the custom attachment metabox functions
 *
 * @since 0.7.1
 */
function mgjp_mv_attachment_metabox_include() {

  include( plugin_dir_path( __FILE__ ) . 'mv-metaboxes.php' );

}


/**
 * Include the options for protected media uploads
 * on the 'media-new.php' admin page
 *
 * @since 0.2
 */
function mgjp_mv_media_new_options_include() {

  include( plugin_dir_path( __FILE__ ) . 'mv-options-media-new.php' );

}


/**
 * Include the options for protected media uploads
 * on the 'upload.php' (Media Library) admin page
 *
 * @since 0.3
 */
function mgjp_mv_media_library_options_include() {

  include( plugin_dir_path( __FILE__ ) . 'mv-options-media-library.php' );

}


/**
 * Add Media Vault flag to enable 
 * Media Vault mp6 styles for WP 3.8+
 *
 * @since 0.8.7
 *
 * @param $classes string admin body classes
 * @return string admin body classes
 */
if ( ! function_exists( 'mgjp_add_mp6_admin_body_class' ) ) {
  function mgjp_add_mp6_admin_body_class( $classes ) {

    global $wp_version;

    if ( version_compare( '3.7.5', $wp_version, '>' ) )
      return $classes;

    return $classes . ' mgjp_mp6 ';
  }
}


/**
 * Add Media Vault settings link on plugins manager page
 *
 * @since 0.8
 *
 * @param $links array Array of links associated with plugin
 * @return array Array of links associated with plugin plus settings link
 */
function mgjp_mv_settings_link( $links ) {

  $settings_link = '<a href="options-media.php#mgjp_mv_settings_section">'
    . esc_html__( 'Settings', 'media-vault' )
    . '</a>';

  array_push( $links, $settings_link );

  return $links;
}



//-----------------------------------------------------------------------//
// MEDIA VAULT - GENERAL FUNCTIONS
//-----------------------------------------------------------------------//


/**
 * Media Vault internal Activation function for a single
 * blog install or for each blog site
 * in network activation mode
 *
 * @since 0.8.5
 *
 * @uses mgjp_mv_default_options()
 * @uses mgjp_mv_load_placeholder_image()
 */
function _mgjp_mv_activate_local( $blog_id = 0 ) {

  // register Media Vault options to the local options table
  add_option( 'mgjp_mv_default_permission', 'logged-in', '', 'yes' );

  add_option( 'mgjp_mv_options', mgjp_mv_default_options(), '', 'no' );
  add_option( 'mgjp_mv_ir', array( 'is_on' => true ), '', 'no' );

  mgjp_mv_load_placeholder_image();

  do_action( 'mgjp_mv_activated_local', $blog_id );
}


/**
 * Checks whether Media Vault requires extra
 * deactivation steps before it can be correctly
 * deactivated
 *
 * @since 0.8.5
 *
 * @return bool true
 *              false
 */
function mgjp_mv_is_deactivation_allowed() {

  if ( 'temp' === get_site_option( 'mgjp_mv_deactivation' ) )
    return true;

  global $is_apache;
  if ( $is_apache
        && ! is_multisite()
        && get_option( 'permalink_structure' )
        && is_writable( get_home_path() . '.htaccess' ) )
    return true;

  if ( ! mgjp_mv_check_rewrite_rules( true ) )
    return true;

  return false;
}


/**
 * Media Vault internal Deactivation function for a single
 * blog install or for each blog site
 * in network activation mode
 *
 * @since 0.8.5
 */
function _mgjp_mv_deactivate_local( $blog_id = 0 ) {

  // unload default placeholder image if it exists
  $ir = get_option( 'mgjp_mv_ir' );
  if ( isset( $ir['default'] ) && wp_attachment_is_image( $ir['default'] ) )
    wp_delete_attachment( $ir['default'], true );

  do_action( 'mgjp_mv_deactivated_local', $blog_id );
}


/**
 * Return the relative "Path to plugin file with plugin data"
 *
 * @since 0.8.5
 *
 * @return string
 */
function mgjp_mv_get_dirfile() {

  $plugin_dir  = explode( '/', plugin_basename( __FILE__ ) );
  $plugin_dir  = $plugin_dir[0];

  $plugin_file = array_keys( get_plugins( "/$plugin_dir" ) );
  $plugin_file = $plugin_file[0];

  return "$plugin_dir/$plugin_file";
}


/**
 * Return the Media Vault protected upload folder
 *
 * @since 0.1
 *
 * @param $path string path to attach to the end of protected folder dirname
 * @param $in_url bool set to true if slash before protected folder dirname is desired
 * @return string Media Vault protected upload folder relative to WP uploads folder
 */
function mgjp_mv_upload_dir( $path = '', $in_url = false ) {

  $dirpath = $in_url ? '/' : '';
  $dirpath .= '_mediavault';
  $dirpath .= $path;

  return $dirpath;

}


/**
 * Generate the rewrite rules to reroute requests for
 * media uploads within protected folders and requests 
 * for media uploads with the `safeforce` download flag
 * set, to the file-handling script. Even supporting
 * WP Multisite.
 *
 * @since 0.8.5
 *
 * @uses mgjp_mv_upload_dir()
 * @return array Array of strings of each line of the
 *               plugin's custom rewrite rules.
 */
function mgjp_mv_get_the_rewrite_rules() {

  $upload       = wp_upload_dir();
  $uploads_path = str_replace( site_url( '/' ), '', $upload['baseurl'] );

  // if is multisite add allowance for '/sites/ID' folders in uploads path
  if ( is_multisite() )
    $uploads_path .= '(?:/sites/[0-9]+)?';

  // if multisite is on sub-directory mode add allowance for the site's 
  // sub-directory in the rewrite regex
  if ( is_multisite() && ! is_subdomain_install() )
    $uploads_path = '(?:[_0-9a-zA-Z-]+/)?' . $uploads_path;

  $old_path_protected = $uploads_path . '(' . mgjp_mv_upload_dir( '/.*\.\w+)$', true );
  $old_path_downloads = $uploads_path . '(/.*\.\w+)$';

  $rewrite_rules = array(
    '# Media Vault Rewrite Rules',
    'RewriteRule ^' . $old_path_protected . ' index.php?mgjp_mv_file=$1 [QSA,L]',
    'RewriteCond %{QUERY_STRING} ^(?:.*&)?mgjp_mv_download=safeforce(?:&.*)?$',
    'RewriteRule ^' . $old_path_downloads . ' index.php?mgjp_mv_file=$1 [QSA,L]',
    '# Media Vault Rewrite Rules End'
  );

  // if pretty permalinks not enabled then produce the code necessary for the user to manually
  // add the rules to .htaccess
  if ( ! is_multisite() && ! get_option( 'permalink_structure' ) ) {
    $home_root = parse_url( home_url() );
    if ( isset( $home_root['path'] ) )
      $home_root = trailingslashit( $home_root['path'] );
    else
      $home_root = '/';

    array_splice( $rewrite_rules, 1, 0, array(
      '<ifModule mod_rewrite.c>',
      'RewriteEngine On',
      'RewriteBase ' . $home_root
    ) );
    array_splice( $rewrite_rules, -1, 0, array(
      '</ifModule>'
    ) );
  }

  return apply_filters( 'mgjp_mv_get_rewrite_rules', $rewrite_rules );

}

/**
 * Check if request rewrite rules have been configured correctly
 * Don't call on every init! - it makes **at least** two http
 * requests every time it is called!
 *
 * @since 0.8.5
 *
 * @uses mgjp_mv_upload_dir()
 * @return bool
 */
function mgjp_mv_check_rewrite_rules( $deactivation = false ) {

  $upload_dir = wp_upload_dir();

  $protected_test = mgjp_mv_upload_dir( '/mgjp_mv_rewrite_test.txt?mgjp_mv_rewrite_test=1', true );
  $downloads_test = '/mgjp_mv_rewrite_test.txt?mgjp_mv_download=safeforce&mgjp_mv_rewrite_test=1';

  $checks = array(
    $upload_dir['baseurl'] . $protected_test,
    $upload_dir['baseurl'] . $downloads_test
  );

  $checks = apply_filters( 'mgjp_mv_rewrite_rule_check_urls', $checks );

  $checks_passed = true;
  foreach ( $checks as $check_url ) {

    $check = wp_remote_get( $check_url );

    if ( is_wp_error( $check )
         || ! isset( $check['response']['code'] ) || 200 != $check['response']['code']
         || ! isset( $check['body'] ) || 'pass' != $check['body'] )
      $checks_passed = false;
    else
      $checks_passed = true;

    if ( ( ! $deactivation && ! $checks_passed ) || ( $deactivation && $checks_passed ) )
      break;
  }

  return $checks_passed;

}


/**
 * Adds the default Media Vault place-holder image to the 
 * Media Library and saves the id of the attachment created
 * in the 'mgjp_mv_ir' option in the options table
 *
 * @since 0.8
 *
 * @return int the ID of the attachment that was created
 * @return bool true on success
 * @return bool false on failure to load image
 */
function mgjp_mv_load_placeholder_image( $restore_orig = false ) {

  $ir = get_option( 'mgjp_mv_ir' );

  // if placeholder image already exists return its attachment ID
  if ( isset( $ir['id'] ) && wp_attachment_is_image( $ir['id'] ) && ! $restore_orig )
    return $ir['id'];

  // if original placeholder is loaded no need to
  // reload it. Set it as placeholder and return its ID
  if ( isset( $ir['default'] ) && wp_attachment_is_image( $ir['default'] ) ) {
    $ir['id'] = $ir['default'];
    update_option( 'mgjp_mv_ir', $ir );
    return $ir['id'];
  }

  require_once( ABSPATH . 'wp-admin/includes/file.php' );
  require_once( ABSPATH . 'wp-admin/includes/media.php' );
  require_once( ABSPATH . 'wp-admin/includes/image.php' );

  $tmp = download_url( plugins_url( 'imgs/media-vault-ir.jpg', __FILE__ ) );

  if ( is_wp_error( $tmp ) ) {
    @ unlink( $tmp );
    return false;
  }

  $file_array = array(
    'name'     => 'media-vault-ir.jpg',
    'tmp_name' => $tmp
  );

  $post_data['post_date_gmt'] = $post_data['post_date'] = '1988-01-31 12:00:00';

  $id = media_handle_sideload(
    $file_array,
    0,
    __( 'Do Not Delete, Media Vault Place-holder Image' , 'media-vault' ),
    $post_data
  );

  if ( is_wp_error( $id ) ) {
    @ unlink( $tmp );
    return false;
  }

  $ir['default'] = $id;
  $ir['id'] = $id;

  update_option( 'mgjp_mv_ir', $ir );

  return $id;
}


/**
 * Check if an attachment is protected with Media Vault.
 *
 * A file is protected by media vault if and only if 
 * it is in the Media Vault Protected Directory within 
 * the WordPress Uploads Directory.
 * ( eg: wp-content/uploads/_mediavault/../filename.ext )
 *
 * If a file is in the protected directory and no permission
 * meta is detected for the file, the default permission is 
 * used to check if the user is allowed access.
 *
 * So to check if an attachment is protected by Media Vault we
 * need to check whether its files are within the Media Vault
 * Protected Directory.
 *
 * @since 0.8
 *
 * @uses mgjp_mv_upload_dir()
 * @param $attachment_id int the id of the attachment we want to check
 */
function mgjp_mv_is_protected( $attachment_id ) {

  // Get the base file path relative to the WordPress Uploads Directory
  $file = get_post_meta( $attachment_id, '_wp_attached_file', true );

  // Check if the path begins with the Media Vault Protected Directory
  // Therefore check if the attachment's files are in the protected directory
  if ( 0 === stripos( $file, mgjp_mv_upload_dir( '/' ) ) )
    return true;

  return false;
}





/**
 * Adds a permission to the Media Vault permissions array
 *
 * @since 0.6
 *
 * @uses $mgjp_mv_permissions
 * @param $name string Name-id of the new permission, must be unique
 * @param $args array Array of arguments for permission must include:
 *                    [description] string Human readable short description of permission
 *                    [select] string Human readable very consice description of permission, used in option of select element
 *                    [logged_in] bool Whether the user must be at least logged in
 *                    [run_in_admin] bool Whether to run the permission check in WP Admin
 *                    [cb] string Function name to be called to evaluate file access permissions, false if no callback desired
 *                                Function MUST return TRUE if access permitted to file and FALSE or WP_Error if access denied
 * @return bool false on failure, true on success
 */
function mgjp_mv_add_permission( $name, $args ) {

  $allowed_keys = array( 'description', 'select', 'logged_in', 'run_in_admin', 'cb' );

  $safe_args = array();
  foreach ( $allowed_keys as $key ) {
    if ( isset( $args[$key] ) )
      $safe_args[$key] = $args[$key];
  }

  if ( count( $allowed_keys ) !== count( $safe_args ) )
    return false;

  global $mgjp_mv_permissions;
  if ( isset( $mgjp_mv_permissions[$name] ) )
    return false;

  $mgjp_mv_permissions[$name] = $safe_args;

  return true;
}

/**
 * Returns the array of permission array objects
 *
 * @since 0.4
 *
 * @uses apply_filters() to provide hook to change default permissions, or
 *                       add / remove custom permission objects
 * @uses $mgjp_mv_permissions
 * @return array Array of Media Vault file access permissions
 */
function mgjp_mv_get_the_permissions() {

  global $mgjp_mv_permissions;

  return apply_filters( 'mgjp_mv_edit_permissions', $mgjp_mv_permissions );
}

/**
 * Returns the Media Vault file access permission for an attachment
 * or false if the attachment's files are not marked as protected
 *
 * @since 0.7
 *
 * @uses mgjp_mv_is_protected()
 * @return bool false if attachment is not protected
 * @return string the permission name-id set for this attachment if it is protected
 */
function mgjp_mv_get_the_permission( $attachment_id, $meta_only = false ) {

  if ( ! mgjp_mv_is_protected( $attachment_id ) )
    return false;

  $permission = get_post_meta( $attachment_id, '_mgjp_mv_permission', true );

  return empty( $permission ) && ! $meta_only ?
          get_option( 'mgjp_mv_default_permission', 'logged-in' ) :
          $permission;
}

/**
 * Check if the current user is permitted to access an attachment of a
 * specified ID
 *
 * @since 0.7
 *
 * @uses mgjp_mv_get_the_permission()
 * @uses mgjp_mv_get_the_permissions()
 * @param $attachment_id int The id of the attachment to check against
 * @return bool True if current user access permitted
 * @return bool False if current user access denied
 */
function mgjp_mv_check_user_permitted( $attachment_id ) {

  // check if attachment has protection and permissions set on it
  if ( ! $permission = mgjp_mv_get_the_permission( $attachment_id ) )
    return true;

  $permissions = mgjp_mv_get_the_permissions();

  // check if permission set on attachment is valid
  if ( ! isset( $permissions[$permission] ) )
    return false; // it is better to fail safely than to reveal something we should not

  // check if permission check is set to need not run in admin
  if ( is_admin() && isset( $permissions[$permission]['run_in_admin'] ) && ! $permissions[$permission]['run_in_admin'] )
    return true;

  // check if permission check is set to need the user to be logged in. if it is check if he is logged in
  if ( ! isset( $permissions[$permission]['logged_in'] ) || ( $permissions[$permission]['logged_in'] && ! is_user_logged_in() ) )
    return false;

  // check if permission callback is set to false
  if ( isset( $permissions[$permission]['cb'] ) && false === $permissions[$permission]['cb'] )
    return true;

  // if not false (above), check if permission callback is valid, fail safely if it is not
  if ( ! is_callable( $permissions[$permission]['cb'] ) )
    return false;

  // perform the defined permission check callback on the user for this attachment
  // function MUST return true if the user is allowed access
  $permission_check = call_user_func( $permissions[$permission]['cb'], $attachment_id );

  // if there are no errors permit access
  if ( true === $permission_check )
    return true;

  return false;
}


/**
 * Moves attachment files to Media Vault protected
 * directory in the WP uploads folder
 *
 * @since 0.8
 *
 * @uses mgjp_move_attachment_files()
 * @param $attachment_id int the id of the attachment whose files we want to move
 * @return object WP_Error with error txt from mgjp_move_attachment_files() on failure
 * @return bool true on success
 */
function mgjp_mv_move_attachment_to_protected( $attachment_id ) {

  $file = get_post_meta( $attachment_id, '_wp_attached_file', true );

  // check if files are already in the Media Vault protected folder
  if ( 0 === stripos( $file, mgjp_mv_upload_dir( '/' ) ) )
    return true;

  $reldir = dirname( $file );
  if ( in_array( $reldir, array( '\\', '/', '.' ), true ) )
    $reldir = '';

  $new_reldir = path_join( mgjp_mv_upload_dir(), $reldir );

  require_once( plugin_dir_path( __FILE__ ) . 'includes/mgjp-functions.php' );

  return mgjp_move_attachment_files( $attachment_id, $new_reldir );
}

/**
 * Moves attachment files out of Media Vault protected
 * directory in the WP uploads folder
 *
 * @since 0.8
 *
 * @uses mgjp_move_attachment_files()
 * @param $attachment_id int the id of the attachment whose files we want to move
 * @return object WP_Error with error txt from mgjp_mv_move_attachment_files() on failure
 * @return bool true on move success
 */
function mgjp_mv_move_attachment_from_protected( $attachment_id ) {

  $file = get_post_meta( $attachment_id, '_wp_attached_file', true );

  // check if files are already not in the Media Vault protected folder
  if ( 0 !== stripos( $file, mgjp_mv_upload_dir( '/' ) ) )
    return true;

  $new_reldir = ltrim( dirname( $file ), mgjp_mv_upload_dir( '/' ) );

  require_once( plugin_dir_path( __FILE__ ) . 'includes/mgjp-functions.php' );

  return mgjp_move_attachment_files( $attachment_id, $new_reldir );
}


/**
 * Return attachment file download url
 *
 * @since 0.5
 *
 * @param $attachment_id int ID of attachment whose file download url we want
 * @param $size string optional name-id of size of file if attachment is of type 'image'
 * @return object WP_Error with error txt if file is not attachment
 * @return string full filepath to attachment file of specified size with Media Vault force download
 *                query parameter set
 */
function mgjp_mv_get_attachment_download_url( $attachment_id, $size = null ) {

  if ( 'attachment' !== get_post_type( $attachment_id ) )
    return new WP_Error( 'not_attachment', sprintf( __( 'The post type of the post with ID %d, is not %s.', 'media-vault' ), $attachment_id, '\'attachment\'' ) );

  $query_arg = array( 'mgjp_mv_download' => 'safeforce' );

  if ( ! wp_attachment_is_image( $attachment_id ) || ! isset( $size ) )
    return add_query_arg( $query_arg, wp_get_attachment_url( $attachment_id ) );

  $image = wp_get_attachment_image_src( $attachment_id, $size );

  return add_query_arg( $query_arg, $image[0] );
}


/**
 * Return plugin default options
 *
 * @since 0.4
 *
 * @uses apply_filters() provides hook to modify default plugin options
 * @return array Array of Media Vault options
 */
function mgjp_mv_default_options() {

  $options = array(
    'default_upload_protection' => 'off' // possible values 'on' && 'off'
  );

  return apply_filters( 'mgjp_mv_default_options', $options );

}


/**
 * Echo Media Vault Custom
 * admin notice
 *
 * @since 0.8.5
 *
 * @param $desc string Notice text, can contain 'em' & 'strong' html tags
 * @param $link array (optional) [link] link
 *                               [text] link text
 */
function mgjp_mv_admin_notice( $desc, $links = null ) {

  wp_enqueue_style( 'mgjp-mv-admin-notice', plugins_url( 'css/mv-admin-notice.css', __FILE__ ), 'all', null );

  ?>

    <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
      <div class="mgjp-mv-admin-notice">

        <div class="mgjp-mv-admin-notice-logo">
          <img alt="Media Vault logo" src="<?php echo plugins_url( 'imgs/media-vault-logo.png', __FILE__ ); ?>">
        </div>

        <?php if ( isset( $links ) ) : ?>
          <div class="mgjp-mv-admin-notice-btn-box">

            <?php $links = isset( $links['link'] ) ? array( $links ) : $links; ?>
            <?php foreach ( $links as $link ) : ?>

              <a class="mgjp-mv-admin-notice-button" href="<?php echo esc_url( $link['link'] ); ?>">
                <?php echo esc_html( $link['text'] ); ?>
              </a>

            <?php endforeach; ?>

          </div>
        <?php endif; ?>

        <div class="mgjp-mv-valign-outer">
          <div class="mgjp-mv-valign-mid">
            <div class="mgjp-mv-valign-inner mgjp-mv-admin-notice-desc">
              <?php echo wp_kses( $desc, array( 'em' => array(), 'strong' => array() ), false ); ?>
            </div>
          </div>
        </div>

      </div>
    </div>

  <?php
}

?>