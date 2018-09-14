<?php

global	$wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

update_option( 'wp_quiz_version' , '1.0.6' );

// Create Settings
$quiz_settings = get_option( 'wp_quiz_default_settings' );
if ( false === $quiz_settings ) {

	$mts_username = '';
	// MTS Connect plugin username
	$mts_connect_data = get_option( 'mts_connect_data' );
	if ( false !== $mts_connect_data ) {
		$mts_username = $mts_connect_data['username'];
	}

	// Create Options
	$quiz_settings = array(
		'defaults'	=> array(
			'promote_plugin' 	=> 0,
			'mts_username'		=> $mts_username,
			'fb_app_id'			=> '0',
			'auto_scroll'		=> 1,
			'share_buttons'		=> array( 'fb', 'tw', 'g+', 'vk' ),
			'share_meta' 		=> 1,
		),
	);

	update_option( 'wp_quiz_default_settings', $quiz_settings );
}

// Create Import/Export Directory
$wq_upload_dir = wp_upload_dir();
wp_mkdir_p( $wq_upload_dir['basedir'] . '/wp_quiz-import/' );

chmod( $wq_upload_dir['basedir'], 0755 );
chmod( $wq_upload_dir['basedir'] . '/wp_quiz-import/', 0755 );

flush_rewrite_rules();
