<?php

/**
 * bbPress Digest AJAX Functions
 *
 * @package bbPress Digest
 * @subpackage AJAX Functions
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle one-click subscription
 *
 * @since 2.0
 *
 * @uses bbp_get_current_user_id() To get ID of current user
 * @uses current_user_can() To check if the current user can edit user
 * @uses bbp_digest_one_click_add_error() To display error
 * @uses bbp_get_forum() To get forum's data
 * @uses check_ajax_referer() To check nonce
 * @uses get_user_meta() To get user's digest settings
 * @uses current_time() To get current UNIX time
 * @uses add_user_meta() To save user's setting
 * @uses update_user_meta() To update user's setting
 * @uses delete_user_meta() To delete user's setting
 */
function bbp_digest_do_one_click_ajax_handle() {
	/* Check if one click submission and bail if not */
	$action = $_REQUEST['action'];
	if ( ! in_array( $action, array( 'bbp_digest_add_sub', 'bbp_digest_remove_sub' ) ) )
		return;

	/* Get current user's ID */
	$user_id  = bbp_get_current_user_id();

	/* Get forum's ID */
	$forum_id = ! empty( $_REQUEST['forum_id'] ) ? intval( $_REQUEST['forum_id'] ) : 0;

	/* Bail if user can't edit itself */
	if ( ! current_user_can( 'edit_user', $user_id ) )
		return bbp_digest_one_click_add_error();

	/* Get forum object */
	$forum = bbp_get_forum( $forum_id );

	/* Bail if no forum */
	if ( empty( $forum ) )
		return bbp_digest_one_click_add_error();

	/* Check nonce */
	if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'toggle-bbp-digest-sub_' . $forum->ID ) )
		return bbp_digest_one_click_add_error();

	/* Get user's settings */
	$bbp_digest_time   = get_user_meta( $user_id, 'bbp_digest_time'  , true );
	$bbp_digest_forums = get_user_meta( $user_id, 'bbp_digest_forums', true );

	/* If not receiving digest, setup hour */
	if ( ! $bbp_digest_time )
		$new_bbp_digest_time = date( 'H', current_time( 'timestamp' ) );
	else
		$new_bbp_digest_time = $bbp_digest_time;

	/* If no forums included, setup array */
	if ( ! is_array( $bbp_digest_forums ) )
		$bbp_digest_forums = array();

	/* Check if we're adding or removing forum */
	if ( 'bbp_digest_remove_sub' == $action ) {
		$new_action = 'bbp_digest_add_sub';
		$new_bbp_digest_forums = array();

		/* Setup counters to see if we've removed all forums */
		$_total = $_removed = 0;
		foreach ( $bbp_digest_forums as $_forum ) {
			$_total++;
			if ( $_forum == $forum->ID )
				$_removed++;
			else
				$new_bbp_digest_forums[] = $_forum;
		}

		/* If we've removed all forums, stop sending digest */
		if ( $_total == $_removed )
			$new_bbp_digest_time = $new_bbp_digest_forums = '';
	} else {
		$new_action              = 'bbp_digest_remove_sub';
		$new_bbp_digest_forums   = $bbp_digest_forums;
		$new_bbp_digest_forums[] = $forum->ID;
	}

	/* Save data to the database */
	$meta = array(
		'bbp_digest_time'   => $new_bbp_digest_time,
		'bbp_digest_forums' => $new_bbp_digest_forums,
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {
		/* Get the current meta value of the key. */
		$meta_value = get_user_meta( $user_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_user_meta( $user_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_user_meta( $user_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_user_meta( $user_id, $meta_key, $meta_value );
	}

	/* If it's AJAX request, return new one-click subscription link */
	if ( bbp_is_ajax() ) {
		/* Load template file since it's probably not included */
		if ( ! function_exists( 'bbp_digest_get_one_click_link' ) )
			require_once( dirname( __FILE__ ) . '/one-click-template.php' );

		bbp_ajax_response( true, bbp_digest_get_one_click_link( $forum->ID, $new_action ) );
	} else {
		/* Otherwise redirect to forum's page */
		wp_safe_redirect( bbp_get_forum_permalink( $forum->ID ) );

		/* For good measure */
		exit();
	}
}

/**
 * Display error for one-click subscription.
 *
 * @since 2.1
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses bbp_is_ajax() To check if it's bbPress AJAX request
 * @uses bbp_ajax_response() To return error via bbPress AJAX response
 * @uses bbp_add_error() To show error via bbPress error template
 *
 * @param bool $success Was request successful or not
 * @param string $content Error content
 */
function bbp_digest_one_click_add_error( $success = false, $content = '' ) {
	/* If no error content, use default text */
	if ( ! $content ) {
		/* Load translations */
		bbp_digest_load_textdomain();

		$content = __( 'The request was unsuccessful. Please try again.', 'bbp-digest' );
	}

	/* Display error depending of type of request */
	if ( bbp_is_ajax() )
		bbp_ajax_response( $success, $content );
	else
		return bbp_add_error( 'bbp_digest_oneclick_error', $content );
}