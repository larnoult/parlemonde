<?php

/**
 * bbPress Digest Profile Saving Functions
 *
 * @package bbPress Digest
 * @subpackage Profile Saving Functions
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle submission from users profile.
 *
 * @since 1.0
 *
 * @uses current_user_can() To check if the current user can edit user
 * @uses get_user_meta() To get user's digest settings
 * @uses add_user_meta() To save user's setting
 * @uses update_user_meta() To update user's setting
 * @uses delete_user_meta() To delete user's setting
 *
 * @param int $user_id ID of a user
 */
function bbp_digest_do_save_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Prepare submitted values */
	$bbp_digest_time = ( isset( $_POST['bbp-digest-time'] ) && in_array( $_POST['bbp-digest-time'], range( 00, 23 ) ) ) ? $_POST['bbp-digest-time'] : '';
	$bbp_digest_day = ( isset( $_POST['bbp-digest-day'] ) && in_array( $_POST['bbp-digest-day'], range( 0, 6 ) ) ) ? $_POST['bbp-digest-day'] : '';
	$bbp_digest_forums = '';

	if ( isset( $_POST['bbp-digest-forums'] ) && is_array( $_POST['bbp-digest-forums'] ) ) {
		$bbp_digest_forums = array();
		foreach ( $_POST['bbp-digest-forums'] as $forum_id ) {
			if ( is_numeric( $forum_id ) )
				$bbp_digest_forums[] = $forum_id;
		}
	}

	$meta = array(
		'bbp_digest_time' => $bbp_digest_time,
		'bbp_digest_forums' => $bbp_digest_forums,
		'bbp_digest_day' => $bbp_digest_day,
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the current meta value of the key. */
		$meta_value = get_user_meta( $user_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( isset( $new_meta_value ) && ( is_array( $new_meta_value ) || strlen( $new_meta_value ) > 0 ) && '' == $meta_value )
			add_user_meta( $user_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( isset( $new_meta_value ) && ( is_array( $new_meta_value ) || strlen( $new_meta_value ) > 0 ) && $new_meta_value != $meta_value )
			update_user_meta( $user_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && isset( $meta_value ) && ( is_array( $meta_value ) || strlen( $meta_value ) > 0 ) )
			delete_user_meta( $user_id, $meta_key, $meta_value );
	}
	/* Workaround when 0 is POSTed by tom {@link http://www.php.net/manual/en/types.comparisons.php#53926} */
}