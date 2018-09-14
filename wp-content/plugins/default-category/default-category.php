<?php
/**
 * @package default
 * @version 2.0
 */
/*
Plugin Name: Default Category
Plugin URI: http://wordpress.org/plugins/default-text/
Description: Auto select categories for new posts based on user or site-wide settings.
Author: Jason M. Kalawe
Version: 2.0
Author URI: http://makea.kalawe.com

*/

include_once dirname( __FILE__ ) . '/settings.php';
include_once dirname( __FILE__ ) . '/user-profile.php';

/*
 * Save the default category before it's rendered to the user
 */
function default_category_save($post_ID) {
  
  $post_categories = wp_get_post_categories( $post_ID);

  if(empty($post_categories)) {

    // Get current user
    $user = wp_get_current_user();
    // Get user field data for 'default_category_id_for_user'
    $default_category_id_for_user = get_user_meta( $user->ID, 'default_category_id_for_user', TRUE );

    // Check if field has any data
    if(is_array($default_category_id_for_user)){
      
      // Save categories to new post
      wp_set_post_categories( $post_ID, $default_category_id_for_user );
    }
    else { 
      if(get_option('default_category_id')) { 
        $default_category_id = get_option('default_category_id'); 
        wp_set_post_categories( $post_ID, $default_category_id['default_category_id'] );  
      }
    }
  }

}
add_action( 'save_post', 'default_category_save' );
