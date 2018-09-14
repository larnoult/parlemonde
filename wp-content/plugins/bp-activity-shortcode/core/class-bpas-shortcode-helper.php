<?php
/**
 * Helper class for plugin ShortCodes
 *
 * @package bp-activity-shortcode
 */

// Exit if accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BPAS_ShortCode_Helper
 */
class BPAS_ShortCode_Helper {

	/**
	 * Class instance
	 *
	 * @var BPAS_ShortCode_Helper
	 */
	private static $instance;

	/**
	 * Keep track if currently inside shortcode content generation.
	 *
	 * @var bool
	 */
	private $doing_shortcode = false;

	/**
	 * The constructor.
	 */
	private function __construct() {
		$this->register_shortcodes();
	}

	/**
	 * Get Instance
	 *
	 * @return BPAS_ShortCode_Helper
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register ShortCode
	 *
	 * @example [activity-stream display_comments=threaded|none title=somethimg per_page=something]
	 */
	private function register_shortcodes() {
		add_shortcode( 'activity-stream', array( $this, 'generate_activity_stream' ) );
	}

	/**
	 * Generate activity content.
	 *
	 * @param array  $atts shortcode atts.
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function generate_activity_stream( $atts, $content = null ) {

		// Hide if BuddyPress is not active.
		if ( ! function_exists( 'buddypress' ) ) {
			return '';
		}

		// allow to use all those args awesome!
		$atts = shortcode_atts( array(
			'title'            => 'Latest Activity',// title of the section.
			'pagination'       => 1,// show or not.
			'load_more'        => 0,
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
			'use_compat'       => bp_use_theme_compat_with_current_theme(),
			'allow_posting'    => false,    // experimental, some of the themes may not support it.
			'container_class'  => 'activity',// default container,
			'hide_on_activity' => 1,// hide on user and group activity pages.
			'for'              => '', // 'logged','displayed','author'.
			'role'             => '', // use one or more role here(e.g administrator,editor etc).
            'for_group'        => '',// group slug.
		), $atts );

		// hide on user activity, activity directory and group activity.
		if ( $atts['hide_on_activity'] &&
		     ( function_exists( 'bp_is_activity_component' ) &&
		       bp_is_activity_component() ||
		       function_exists( 'bp_is_group_home' ) &&
		       bp_is_group_home() )
		) {
			return '';
		}

		$activity_for = $atts['for'];

		if ( ! empty( $activity_for ) ) {
			unset( $atts['for'] );
			$atts['user_id'] =BPAS_Shortcode_Util::get_user_id_for_context( $activity_for );

			if ( empty( $atts['user_id'] ) ) {
				return '';
			}
		}

		$user_ids = array();
		$has_role = false;
		// Fetch users for role and use their activity.
		if ( ! empty( $atts['role'] ) ) {
		    $has_role = true;
			$user_ids        = BPAS_Shortcode_Util::get_user_ids_by_roles( $atts['role'] );
			$atts['user_id'] = $user_ids;
		}

		if ( ! empty( $atts['scope'] ) && 'following' === $atts['scope'] ) {
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
				$atts['user_id'] = array( 0, 0 );// invalid.
			} else {
				$atts['user_id'] = $following_ids;
			}
		}

		$for_group = $atts['for_group'];
		if ( 'groups' === $atts['object'] && ! empty( $for_group ) && bp_is_active( 'groups' ) ) {
			unset( $atts['for_group'] );
			$atts['primary_id'] = BP_Groups_Group::get_id_from_slug( $for_group );

			if ( empty( $atts['primary_id'] ) ) {
				$atts['user_id'] = array( 0, 0 );// no result.
			}
		}


		$this->doing_shortcode = true;
		$is_nouveau = function_exists( 'bp_nouveau' );


		// start buffering.
		ob_start();
		do_action( 'bp_activity_stream_shortcode_before_generate_content', $atts );

		?>

		<?php if ( $atts['use_compat'] ) : ?>
			<div id="buddypress">
		<?php endif; ?>

		<?php if ( $atts['title'] ) : ?>
			<h3 class="activity-shortcode-title"><?php echo $atts['title']; ?></h3>
		<?php endif; ?>

		<?php do_action( 'bp_before_activity_loop' ); ?>

		<?php if ( $atts['allow_posting'] && is_user_logged_in() ) : ?>
        <div class="bpas-post-form-wrapper">
	        <?php bp_locate_template( array( 'activity/post-form.php' ), true ); ?>

        </div>
		<?php endif; ?>

		<?php if ( bp_has_activities( $atts ) ) : ?>

            <div class="bpas-shortcode-activities <?php echo esc_attr( $atts['container_class'] ); ?> <?php if ( ! $atts['display_comments'] ) : ?> hide-activity-comments<?php endif; ?> shortcode-activity-stream">

                <ul id="activity-stream" class="activity-list item-list <?php echo  $is_nouveau ? 'bp-list': '';?> ">

					<?php while ( bp_activities() ) : bp_the_activity(); ?>
						<?php bp_get_template_part( 'activity/entry' ); ?>
					<?php endwhile; ?>

					<?php if ( $atts['load_more'] && bp_activity_has_more_items() ) : ?>
                        <li class="load-more">
                            <a href="<?php bp_activity_load_more_link() ?>"><?php _e( 'Load More', 'buddypress' ); ?></a>
                        </li>
					<?php endif; ?>
                </ul>

				<?php if ( $atts['pagination'] && ! $atts['load_more'] ) : ?>
                    <div class="pagination">
                        <div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
                        <div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
                    </div>
				<?php endif; ?>

            </div>

        <?php else : ?>
            <div id="message" class="info">
                <p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></p>
            </div>
        <?php endif; ?>

		<?php do_action( 'bp_after_activity_loop' ); ?>
        <form name="bpas-activities-args" class="bpas-activities-args">
            <input type="hidden"  class="bpas_input_display_comments" name="display_comments" value="<?php echo esc_attr( $atts['display_comments'] ) ?>">
            <input type="hidden" class="bpas_input_include" name="include" value="<?php echo esc_attr( $atts['include'] ) ?>">
            <input type="hidden" class="bpas_input_exclude" name="exclude" value="<?php echo esc_attr( $atts['exclude'] ) ?>">
            <input type="hidden" class="bpas_input_int" name="in" value="<?php echo esc_attr( $atts['in'] ) ?>">
            <input type="hidden" class="bpas_input_sort" name="sort" value="<?php echo esc_attr( $atts['sort'] ) ?>">
            <input type="hidden" class="bpas_input_page bps-input-current-page" name="page" value="<?php echo esc_attr( $atts['page'] + 1 ) ?>">
            <input type="hidden" class="bpas_input_per_page" name="per_page" value="<?php echo esc_attr( $atts['per_page'] ) ?>">
            <input type="hidden" class="bpas_input_max" name="max" value="<?php echo esc_attr( $atts['max'] ) ?>">
            <input type="hidden" class="bpas_input_count_total" name="count_total" value="<?php echo esc_attr( $atts['count_total'] ) ?>">
            <input type="hidden" class="bpas_input_scope" name="scope" value="<?php echo esc_attr( $atts['scope'] ) ?>">

            <input type="hidden" class="bpas_input_user_id" name="user_id" value="<?php echo esc_attr( $atts['user_id'] ) ?>">
            <input type="hidden" class="bpas_input_object" name="object" value="<?php echo esc_attr( $atts['object'] ) ?>">
            <input type="hidden" class="bpas_input_bpas_action" name="bpas_action" value="<?php echo esc_attr( $atts['action'] ) ?>">
            <input type="hidden" class="bpas_input_primary_id" name="primary_id" value="<?php echo esc_attr( $atts['primary_id'] ) ?>">
            <input type="hidden" class="bpas_input_secondary_id" name="secondary_id" value="<?php echo esc_attr( $atts['secondary_id'] ) ?>">
            <input type="hidden" class="bpas_input_search_terms" name="search_terms" value="<?php echo esc_attr( $atts['search_terms'] ) ?>">
            <input type="hidden" class="bpas_input_for" name="for" value="<?php echo esc_attr( $activity_for ) ?>">
            <input type="hidden" class="bpas_input_role" name="role" value="<?php echo esc_attr( $atts['role'] ) ?>">
            <input type="hidden" class="bpas_input_for_group" name="for_group" value="<?php echo esc_attr( $for_group ); ?>">
            <input type="hidden" class="bpas_input_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'bpas_load_activities' ) ?>">
            <!--<input type="hidden" name="action" value="bpas_load_activities">-->
        </form>

		<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
			<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>
		</form>

		<?php if ( $atts['use_compat'] ) : ?>
			</div>
		<?php endif; ?>

		<?php

		$output = ob_get_clean();

		$this->doing_shortcode = false;
		do_action( 'bp_activity_stream_shortcode_after_generate_content', $atts );

		return $output;
	}

	/**
	 * Check if we doing shortcode?
	 *
	 * @return bool
	 */
	public function doing_shortcode() {
		return $this->doing_shortcode;
	}
}

BPAS_ShortCode_Helper::get_instance();
