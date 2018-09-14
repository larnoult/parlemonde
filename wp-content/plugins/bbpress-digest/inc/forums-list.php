<?php

/**
 * bbPress Digest Forums List Checkboxes Functions
 *
 * @package bbPress Digest
 * @subpackage Forums List Checkboxes Functions
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Create HTML checkboxes list of bbPress forums.
 *
 * @see BBP_Walker_Dropdown()
 *
 * @since 1.0
 *
 * @uses Walker
 */
class BBP_Digest_Walker_Checkboxes extends Walker {
	/**
	 * @see Walker::$tree_type
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	var $tree_type;

	/**
	 * @see Walker::$db_fields
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );

	/**
	 * Set the tree_type
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses bbp_get_forum_post_type() To get forum post type name
	 */
	public function BBP_Digest_Walker_Checkboxes() {
		$this->tree_type = bbp_get_forum_post_type();
	}

	/**
	 * @see Walker::start_el()
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses bbp_get_forum_post_type() To get forum post type name
	 * @uses bbp_is_forum_category() To check if the forum is a category
	 * @uses current_user_can() To check if the current user can post in
	 *                           closed forums
	 * @uses bbp_is_forum_closed() To check if the forum is closed
	 * @uses apply_filters() Calls 'bbp_digest_walker_checkboxes_post_title'
	 *                         with the title, output, post, depth and args
	 *
	 * @param string $output Passed by reference. Used to append additional
	 *                        content.
	 * @param object $post Post data object.
	 * @param int $depth Depth of post in reference to parent posts. Used
	 *                    for padding.
	 * @param int $current_object_id
	 * @param array $args Uses 'selected_forums' argument for selected forum to set
	 *                     checked HTML attribute for checkbox.
	 */
	public function start_el( &$output, $post, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$pad     = str_repeat( '&nbsp;', $depth * 3 );
		$output .= $pad . "\t<label for=\"bbp-digest-forum-checkbox-{$post->ID}\"><input type=\"checkbox\" class=\"level-$depth\" name=\"bbp-digest-forums[]\"";

		// Disable the <option> if we're told to do so, the post type is bbp_forum and the forum is a category or is closed
		if ( true == $args['disable_categories'] && $post->post_type == bbp_get_forum_post_type() && ( bbp_is_forum_category( $post->ID ) || ( !current_user_can( 'edit_forum', $post->ID ) && bbp_is_forum_closed( $post->ID ) ) ) )
			$output .= ' disabled="disabled" value=""';
		else
			$output .= ' value="' .$post->ID .'"' . checked( in_array( $post->ID, $args['selected_forums'] ), true, false );

		$output .= ' />';
		$title   = esc_html( $post->post_title );
		$title   = apply_filters( 'bbp_digest_walker_checkboxes_post_title', $post->post_title, $output, $post, $depth, $args );
		$output .= '&nbsp;' . $title . '</label><br />';
		$output .= "\n";
	}
}
/**
 * Output a checkboxes allowing to pick which forum
 * is selected for digest.
 *
 * @see bbp_get_dropdown()
 *
 * @since 1.0
 *
 * @uses BBP_Digest_Walker_Checkboxes() As the default walker to generate the
 *                              checkboxes
 * @uses current_user_can() To check if the current user can read
 *                           private forums
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses bbp_get_tab_index() To get tab index of element
 * @uses bbp_get_public_status_id() To get name of public post status
 * @uses bbp_get_private_status_id() To get name of private post status
 * @uses bbp_get_hidden_status_id() To get name of hidden post status
 * @uses get_posts() To get posts for list
 * @uses walk_page_dropdown_tree() To generate the checkboxes using the
 *                                  walker
 * @uses apply_filters() Calls 'bbp_digest_get_dropdown' with the checkboxes
 *                        and args
 *
 * @param mixed $args The function supports these args:
 *  - post_type: Post type, defaults to bbp_get_forum_post_type() (bbp_forum)
 *  - selected: Selected ID, to not have any value as selected, pass
 *               anything smaller than 0 (due to the nature of select
 *               box, the first value would of course be selected -
 *               though you can have that as none (pass 'show_none' arg))
 *  - sort_column: Sort by? Defaults to 'menu_order, post_title'
 *  - child_of: Child of. Defaults to 0
 *  - post_status: Which all post_statuses to find in? Can be an array
 *                  or CSV of publish, category, closed, private, spam,
 *                  trash (based on post type) - if not set, these are
 *                  automatically determined based on the post_type
 *  - posts_per_page: Retrieve all forums/topics. Defaults to -1 to get
 *                     all posts
 *  - walker: Which walker to use? Defaults to
 *             {@link BBP_Digest_Walker_Checkboxes}
 *  - select_id: ID of the select box. Defaults to 'bbp_forum_id'
 *  - tab: Tabindex value. False or integer
 *  - options_only: Show only <options>? No <select>?
 *  - show_none: False or something like __( '(No Forum)', 'bbpress' ),
 *                will have value=""
 *  - none_found: False or something like
 *                 __( 'No forums to post to!', 'bbpress' )
 *  - disable_categories: Disable forum categories and closed forums?
 *                         Defaults to true. Only for forums and when
 *                         the category option is displayed.
 * @return string The checkboxes
 */
function bbp_digest_get_dropdown( $args = '' ) {

	/** Arguments *********************************************************/

	$defaults = array (
		'post_type'          => bbp_get_forum_post_type(),
		'selected'           => 0,
		'sort_column'        => 'menu_order',
		'child_of'           => '0',
		'numberposts'        => -1,
		'orderby'            => 'menu_order',
		'order'              => 'ASC',
		'walker'             => '',

		// Output-related
		'select_id'          => 'bbp_forum_id',
		'tab'                => bbp_get_tab_index(),
		'options_only'       => false,
		'show_none'          => false,
		'none_found'         => false,
		'disable_categories' => true
	);

	$r = wp_parse_args( $args, $defaults );

	if ( empty( $r['walker'] ) ) {
		$r['walker']            = new BBP_Digest_Walker_Checkboxes();
		$r['walker']->tree_type = $r['post_type'];
	}

	// Force 0
	if ( is_numeric( $r['selected'] ) && $r['selected'] < 0 )
		$r['selected'] = 0;

	extract( $r );

	// Unset the args not needed for WP_Query to avoid any possible conflicts.
	// Note: walker and disable_categories are not unset
	unset( $r['select_id'], $r['tab'], $r['options_only'], $r['show_none'], $r['none_found'] );

	/** Post Status *******************************************************/

	// Public
	$post_stati[] = bbp_get_public_status_id();

	// Forums
	if ( bbp_get_forum_post_type() == $post_type ) {

		// Private forums
		if ( current_user_can( 'read_private_forums' ) )
			$post_stati[] = bbp_get_private_status_id();

		// Hidden forums
		if ( current_user_can( 'read_hidden_forums' ) )
			$post_stati[] = bbp_get_hidden_status_id();
	}

	// Setup the post statuses
	$r['post_status'] = implode( ',', $post_stati );

	/** Setup variables ***************************************************/

	$name      = esc_attr( $select_id );
	$select_id = $name;
	$tab       = (int) $tab;
	$retval    = '';
	$posts     = get_posts( $r );

	/** Drop Down *********************************************************/

	// Items found
	if ( !empty( $posts ) ) {

		$retval .= !empty( $show_none ) ? "\t<option value=\"\" class=\"level-0\">" . $show_none . '</option>' : '';
		$retval .= walk_page_dropdown_tree( $posts, 0, $r );

	// No items found - Display feedback if no custom message was passed
	} elseif ( empty( $none_found ) ) {

		// Switch the response based on post type
		switch ( $post_type ) {

			// Topics
			case bbp_get_topic_post_type() :
				$retval = __( 'No topics available', 'bbp-digest' );
				break;

			// Forums
			case bbp_get_forum_post_type() :
				$retval = __( 'No forums available', 'bbp-digest' );
				break;

			// Any other
			default :
				$retval = __( 'None available', 'bbp-digest' );
				break;
		}
	}

	return apply_filters( 'bbp_digest_get_dropdown', $retval, $args );
}