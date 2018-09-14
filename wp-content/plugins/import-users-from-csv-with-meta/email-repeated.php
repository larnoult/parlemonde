<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

function acui_hack_email( $email ) {
	if ( ! is_email( $email ) ) {
		return;
	}

	$old_email = $email;

	for ( $i = 0; ! $skip_remap && email_exists( $email ); $i++ ) {
		$email = str_replace( '@', "+ama{$i}@", $old_email );
	}

	return $email;
}

function acui_hack_restore_remapped_email_address( $user_id, $email ) {
	global $wpdb;

	$wpdb->update(
		$wpdb->users,
		array( 'user_email' => $email ),
		array( 'ID' => $user_id )
	);

	clean_user_cache( $user_id );	
}