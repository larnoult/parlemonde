<?php
/**
 * Utility class.
 */

defined( 'ABSPATH' ) || exit( 0 );

/**
 * Collection of utility functions.
 */
class BPAS_Shortcode_Util {

	/**
	 * Get user ids belonging to a specific role.
	 *
	 * @param string|array $roles list of roles.
	 *
	 * @return array
	 */
	public static function get_user_ids_by_roles( $roles ) {

		$invalid_ids = array( 0, 0 );
		if ( empty( $roles ) ) {
			return $invalid_ids;// invalid ids.
		}

		if ( ! is_array( $roles ) ) {
			$roles = explode( ',', $roles );
		}

		// trim space etc.
		$roles = array_map( 'trim', $roles );

		$user_query = new WP_User_Query( array(
			'role__in' => $roles,
			'fields'   => 'ID',
		) );

		$ids = $user_query->get_results();

		if ( empty( $ids ) ) {
			$ids = $invalid_ids;
		}

		return $ids;
	}

	/**
	 * Get the ids of user followed by the $user_id.
	 *
	 * @param int $user_id user id.
	 *
	 * @return array
	 */
	public static function get_following_user_ids( $user_id ) {
		if ( ! function_exists( 'bp_follow_get_following' ) ) {
			return array();
		}

		return bp_follow_get_following( array(
			'user_id' => $user_id,
		) );
	}

	/**
	 * Get the user id for the given context.
	 *
	 * @param string $context 'logged', 'displayed', 'author'.
	 *
	 * @return string
	 */
	public static function get_user_id_for_context( $context ) {

		$user_id = false;
		switch ( $context ) {

			case 'logged':
				$user_id = bp_loggedin_user_id();
				break;

			case 'displayed':
				$user_id = bp_displayed_user_id();
				break;

			case 'author':
				if ( is_singular() || in_the_loop() ) {
					$user_id = get_the_author_meta( 'ID' );
				} elseif ( is_author() ) {
					$user_id = get_queried_object_id();
				}

				break;
		}

		return $user_id;
	}
}