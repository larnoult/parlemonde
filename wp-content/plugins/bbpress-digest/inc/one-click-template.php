<?php

/**
 * bbPress Digest One-click Template
 *
 * @package bbPress Digest
 * @subpackage One-click Template
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display link for one-click subscription
 *
 * @since 2.0
 *
 * @uses bbp_is_single_forum() To check if it's single forum
 * @uses is_user_logged_in() To check if current visitor is logged in
 * @uses bbp_get_current_user_id() To get ID of current user
 * @uses get_user_meta() To get user's digest settings
 * @uses bbp_get_forum_id() To get current forum's ID
 * @uses bbp_get_ajax_url() To get bbPress AJAX handler URL
 * @uses wp_create_nonce() To create nonce for AJAX request
 * @uses wp_print_scripts() To load jQuery file
 * @uses bbp_digest_get_one_click_link() To get one-click subscription link
 */
function bbp_digest_display_one_click_subscription() {
	/* Bail if not viewing a single forum or not logged in */
	if ( ! bbp_is_single_forum() || ! is_user_logged_in() )
		return;

	/* Get current user's ID */
	$user_id = bbp_get_current_user_id();

	/* Get user's settings */
	$bbp_digest_time   = get_user_meta( $user_id, 'bbp_digest_time'  , true );
	$bbp_digest_forums = get_user_meta( $user_id, 'bbp_digest_forums', true );

	/* Bail if user subscribed to all */
	if ( $bbp_digest_time && ! $bbp_digest_forums )
		return;

	/* Get this forum's ID */
	$forum_id = bbp_get_forum_id();

	/* Check user's subcription status*/
	$is_sub = in_array( $forum_id, (array) $bbp_digest_forums ) ? 1 : 0;

	/* Prepare Javascript variables */
	$localizations = array(
		'forum_id'           => $forum_id,
		'bbp_ajaxurl'        => bbp_get_ajax_url(),
		'generic_ajax_error' => __( 'The request was unsuccessful. Please try again.', 'bbp-digest' ),
		'_wpnonce'           => wp_create_nonce( 'toggle-bbp-digest-sub_' . $forum_id )
	);

	/* Prepare script with code taken from WP_Scripts::localize */
	foreach ( (array) $localizations as $key => $value ) {
		if ( ! is_scalar( $value ) )
			continue;

		$scripts[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
	}

	$script = 'var bbpDigestJS = ' . json_encode( $scripts ) . ';';

	/* Load necessary scripts */
	wp_print_scripts( 'jquery' );

	/* Print Javascript code */
	echo "<script type='text/javascript'>\n"; // CDATA and type='text/javascript' is not needed for HTML 5
	echo "/* <![CDATA[ */\n";
	echo "$script\n";
	echo "/* ]]> */\n";
	echo "</script>\n";

	/* Javascript that handles link clicks, taken from bbPress'	topic.js */
	?>
	<script type="text/javascript">
	jQuery( document ).ready( function ( $ ) {
		/* Fire when clicking one-click subscription action link */
		$( '#bbp-digest-sub-toggle' ).on( 'click', 'span a.bbp-digest-sub-toggle', function( e ) {
			e.preventDefault();

			/* Get subscription status from parent span class */
			var bbpDigestAction = $( this ).parent().hasClass( 'is-subscribed' ) ? 'bbp_digest_remove_sub' : 'bbp_digest_add_sub';

			/* Prepare POST data */
			var $data = {
				action   : bbpDigestAction,
				forum_id : bbpDigestJS.forum_id,
				_wpnonce : bbpDigestJS._wpnonce
			};

			/* Make AJAX request */
			$.post( bbpDigestJS.bbp_ajaxurl, $data, function ( response ) {
				/* On successful request display response instead of current link */
				if ( response.success ) {
					$( '#bbp-digest-sub-toggle' ).html( response.content );
				} else {
					/* Otherwise display response or genereic error */
					if ( !response.content ) {
						response.content = bbpDigestJS.generic_ajax_error;
					}
					alert( response.content );
				}
			} );
		} );
	} );
	</script>
	<?php
	/* Setup variables based on subscription status */
	if ( 1 == $is_sub )
		$action = 'bbp_digest_remove_sub';
	else
		$action = 'bbp_digest_add_sub';

	echo bbp_digest_get_one_click_link( $forum_id, $action );
}

/**
 * Get link for one-click subscription
 *
 * @since 2.1
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses bbp_get_user_profile_edit_url() To get URL of user's settings
 * @uses bbp_get_current_user_id() To get current user's ID
 * @uses bbp_get_forum_permalink() To get URL of a forum
 * @uses esc_url() To escape URL
 * @uses wp_nonce_url() To add nonce to the URL
 * @uses add_query_arg() To add query arguments to the URL
 *
 * @param int $forum_id ID of a forum
 * @param string $action Action that link should perform
 * @return string $html Link for one-click subscription
 */
function bbp_digest_get_one_click_link( $forum_id, $action ) {
	/* Load translations */
	bbp_digest_load_textdomain();

	/* Get link to bbPress Digest section at profile page */
	$profile_url = bbp_get_user_profile_edit_url( bbp_get_current_user_id() ) . '#bbp-digest-check-row';

	/* Setup texts */
	$sub_text = __( '<a href="%1$s" class="%2$s">Include topics from this forum to the digest emails</a> (<a href="%3$s">edit settings</a>)', 'bbp-digest' );
	$unsub_text = __( 'Topics from this forum are included in digest emails (<a href="%1$s" class="%2$s">remove </a> | <a href="%3$s">edit settings</a>)', 'bbp-digest' );

	/* Setup variables based on subscription status */
	if ( 'bbp_digest_remove_sub' == $action ) {
		$text = $unsub_text;
		$favs = array( 'action' => 'bbp_digest_remove_sub', 'forum_id' => $forum_id );
	} else {
		$text = $sub_text;
		$favs = array( 'action' => 'bbp_digest_add_sub', 'forum_id' => $forum_id );
	}

	/* Get link to the forum's page */
	$permalink = bbp_get_forum_permalink( $forum_id );

	/* Setup subelements */
	$url    = esc_url( wp_nonce_url( add_query_arg( $favs, $permalink ), 'toggle-bbp-digest-sub_' . $forum_id ) );
	$is_sub_class = ( 'bbp_digest_remove_sub' == $action ) ? 'is-subscribed' : 'not-subscribed';
	$a_class = 'bbp-digest-sub-toggle';

	/* Prepare elements with subelements */
	$_pre = '<span id="bbp-digest-sub-toggle"><span id="bbp-digest-sub-' . $forum_id . '" class="' . $is_sub_class . '">';
	$_mid = sprintf( $text, $url, $a_class, $profile_url );
	$_post = '</span></span>';

	/* Create and return final element */
	$html = $_pre . $_mid . $_post;

	return $html;
}