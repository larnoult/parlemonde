<?php
/**
 * WordPress session management.
 *
 * Standardizes WordPress session data and uses either database transients or in-memory caching
 * for storing user session information.
 *
 * @package WordPress
 * @subpackage Session
 * @since   3.7.0
 */

/**
 * Return the current cache expire setting.
 *
 * @return int
 */
function lmm_wp_session_cache_expire() {
	return LMM_WP_Session::get_instance()->cache_expiration();
}

/**
 * Alias of lmm_wp_session_write_close()
 */
function lmm_wp_session_commit() {
	lmm_wp_session_write_close();
}

/**
 * Load a JSON-encoded string into the current session.
 *
 * @param string $data
 * @return bool
 */
function lmm_wp_session_decode( $data ) {
	return LMM_WP_Session::get_instance()->json_in( $data );
}

/**
 * Encode the current session's data as a JSON string.
 *
 * @return string
 */
function lmm_wp_session_encode() {
	return LMM_WP_Session::get_instance()->json_out();
}

/**
 * Regenerate the session ID.
 *
 * @param bool $delete_old_session
 *
 * @return bool
 */
function lmm_wp_session_regenerate_id( $delete_old_session = false ) {
	LMM_WP_Session::get_instance()->regenerate_id( $delete_old_session );
	return true;
}

/**
 * Start new or resume existing session.
 *
 * Resumes an existing session based on a value sent by the _wp_session cookie.
 *
 * @return bool
 */
function lmm_wp_session_start() {
	$wp_session = LMM_WP_Session::get_instance();
	do_action( 'lmm_wp_session_start', $wp_session );

	return $wp_session->session_started();
}
// start session if using command line
if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
	add_action( 'plugins_loaded', 'lmm_wp_session_start' );
}

/**
 * Return the current session status.
 *
 * @return int
 */
function lmm_wp_session_status() {
	if ( LMM_WP_Session::get_instance()->session_started() ) {
		return PHP_SESSION_ACTIVE;
	}

	return PHP_SESSION_NONE;
}

/**
 * Unset all session variables.
 */
function lmm_wp_session_unset() {
	LMM_WP_Session::get_instance()->reset();
}

/**
 * Write session data and end session
 */
function lmm_wp_session_write_close() {
	LMM_WP_Session::get_instance()->write_data();
	do_action( 'lmm_wp_session_commit' );
}
// stop session if using command line
if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
	add_action( 'shutdown', 'lmm_wp_session_write_close' );
}

/**
 * Clean up expired sessions by removing data and their expiration entries from
 * the WordPress options table.
 *
 * This method should never be called directly and should instead be triggered as part
 * of a scheduled task or cron job.
 */
function lmm_wp_session_cleanup() {
	if ( defined( 'WP_SETUP_CONFIG' ) ) {
		return;
	}

	if ( ! defined( 'WP_INSTALLING' ) ) {
		/**
		 * Determine the size of each batch for deletion.
		 *
		 * @param int
		 */
		$batch_size = apply_filters( 'wp_session_delete_batch_size', 2000 );

		// Delete a batch of old sessions
		LMM_WP_Session_Utils::delete_old_sessions( $batch_size );
	}

	// Allow other plugins to hook in to the garbage collection process.
	do_action( 'lmm_wp_session_cleanup' );
}
add_action( 'lmm_wp_session_garbage_collection', 'lmm_wp_session_cleanup' );

/**
 * Register the garbage collector as a hourly daily event.
 */
function lmm_wp_session_register_garbage_collection() {
	if ( ! wp_next_scheduled( 'lmm_wp_session_garbage_collection' ) ) {
		wp_schedule_event( time(), 'hourly', 'lmm_wp_session_garbage_collection' );
	}
}
add_action( 'init', 'lmm_wp_session_register_garbage_collection' );