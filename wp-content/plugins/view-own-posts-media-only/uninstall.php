<?php

/**
 * Plugin uninstallation script
 * 
 * @author Vladimir Garagulya
 * @package edit-post-expire
 * @subpackage uninstall
 * 
 */

if( defined( 'ABSPATH' ) && defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	// Remove plugin's settings 
	delete_option( 'view_own_posts_media_only' );

}

?>