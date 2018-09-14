<?php
/**
 * Handler class for plugin ajax request
 *
 * @package bp-activity-shortcode
 */

// Exit if accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BPAS_Ajax_Handler
 */
class BPAS_Ajax_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Setup Callbacks
	 */
	public function setup() {
		add_action( 'wp_ajax_bpas_load_activities', array( $this, 'load_activities' ) );
		add_action( 'wp_ajax_nopriv_bpas_load_activities', array( $this, 'load_activities' ) );
	}

	/**
	 * Load activities
	 */
	public function load_activities() {

		check_ajax_referer( 'bpas_load_activities' );

		unset( $_POST['_wpnonce'] );
		unset( $_POST['action'] );

		//unset( $_POST['user_id'] );

		// we do not allow tinkering with hide_sitewide while loading via ajax.
		unset( $_POST['hide_sitewide'] );

		$args = wp_parse_args( $_POST, array(
			'display_comments' => 'threaded',
			'include'          => false,     // pass an activity_id or string of IDs comma-separated
			'exclude'          => false,     // pass an activity_id or string of IDs comma-separated
			'in'               => false,     // comma-separated list or array of activity IDs among which to search
			'sort'             => 'DESC',    // sort DESC or ASC
			'page'             => 1,         // which page to load
			'per_page'         => 5,         // how many per page.
			'max'              => false,     // max number to return.
			'count_total'      => true,

			// Scope - pre-built activity filters for a user (friends/groups/favorites/mentions).
			'scope'            => false,

			// Filtering
			'user_id'          => false,    // user_id to filter on
			'object'           => false,    // object to filter on e.g. groups, profile, status, friends
			'action'           => false,    // action to filter on e.g. activity_update, new_forum_post, profile_updated
			'primary_id'       => false,    // object ID to filter on e.g. a group_id or forum_id or blog_id etc.
			'secondary_id'     => false,    // secondary object ID to filter on e.g. a post_id.

			// Searching
			'search_terms'     => false,         // specify terms to search on.
			'hide_on_activity' => 1,// hide on user and group activity pages.
			'for'              => '', // 'logged','displayed','author'.
			'role'             => '', // use one or more role here(e.g administrator,editor etc).
            'for_group'        => '', // Not using 'group' as bp may use it in future.
		) );

		$activity_for = $args['for'];

		if ( ! empty( $activity_for ) ) {
			unset( $args['for'] );
			$args['user_id'] = BPAS_Shortcode_Util::get_user_id_for_context( $activity_for );

			if ( empty( $args['user_id'] ) ) {
				$args['user_id'] = array(0, 0 ); //invalid.
			}
		}


		$user_ids = array();
		$has_role = false;
		// Fetch users for role and use their activity.
		if ( ! empty( $args['role'] ) ) {
			$has_role = true;
			$user_ids        = BPAS_Shortcode_Util::get_user_ids_by_roles( $args['role'] );
			$args['user_id'] = $user_ids;
		}

		if ( ! empty( $args['scope'] ) && 'following' === $args['scope'] ) {
			// Compatibility for 1.2.2, Not needed when using the 1.3 branch of bp followers.
			$user_id = BPAS_Shortcode_Util::get_user_id_for_context( $activity_for );
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			$following_ids = array();

			if ( $user_id ) {
				$following_ids = BPAS_Shortcode_Util::get_following_user_ids( $user_id );
			}
			// limit to common users.
			if ( $has_role ) {
				$following_ids = array_intersect( $user_ids, $following_ids );
			}

			// if not following anyone, empty/invalid.
			if ( empty( $following_ids ) ) {
				$args['user_id'] = array( 0, 0 );// invalid.
			} else {
				$args['user_id'] = $following_ids;
			}
		}

		$for_group = $args['for_group'];
		if ( 'groups' === $args['object'] && ! empty( $for_group ) && bp_is_active( 'groups' ) ) {
			$args['primary_id'] = BP_Groups_Group::get_id_from_slug( $for_group );

			if ( empty( $args['primary_id'] ) ) {
				$args['user_id'] = array( 0, 0 );// no result.
			}
		}

			if ( ! empty( $_POST['bpas_action'] ) ) {
			$args['action'] = $_POST['bpas_action'];
		}

		if ( bp_has_activities( $args ) ) {

			ob_start();

		?>
			<?php while ( bp_activities() ) : bp_the_activity(); ?>

				<?php bp_get_template_part( 'activity/entry' ); ?>

			<?php endwhile; ?>

			<?php if ( bp_activity_has_more_items() ) : ?>

				<li class="load-more">
					<a href="#more"><?php _e( 'Load More', 'bp-magic' ); ?></a>
				</li>

			<?php endif; ?>
		<?php
			$content = ob_get_clean();

			wp_send_json_success( $content );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Sorry, there was no activity found.', 'bp-activity-shortcode' ),
			) );
		}
	}
}

new BPAS_Ajax_Handler();
