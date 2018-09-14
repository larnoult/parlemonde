<?php

/**
 * Wrap functions to use in the theme.
 */


/**
 * Get poll object
 * @param  integer $poll_id ID of poll
 * @return object Poll object
 */
function democracy_get_poll( $poll_id ){
	return DemPoll::get_poll( $poll_id );
}

/**
 * Get poll attached to current post.
 * @param  integer [$post_id = 0] ID or object of post, attached poll of which you want to get.
 * @return string  Poll HTML code.
 */
function get_post_poll_id( $post_id = 0 ){
	$post_id = ( is_numeric($post_id) && $post_id ) ? intval($post_id) : get_post( $post_id )->ID; // current post

	return $poll_id = (int) get_post_meta( $post_id, Democracy_Poll::$pollid_meta_key, 1 );
}

/**
 * Display specified democracy poll.
 *
 * @see get_democracy_poll()
 */
function democracy_poll( $id = 0, $before_title = '', $after_title = '', $from_post = 0 ){
	echo get_democracy_poll( $id, $before_title, $after_title, $from_post );
}

/**
 * Get specified democracy poll.
 * @param  integer  [$poll_id = 0]       Poll ID
 * @param  string   [$before_title = ''] HTML/text before poll title.
 * @param  string   [$after_title = '']  HTML/text after poll title.
 * @param  integer  [$from_post = 0]     Post ID from which the poll was called - to which the poll must be attached.
 * @return string   Poll HTML code.
 */
function get_democracy_poll( $poll_id = 0, $before_title = '', $after_title = '', $from_post = 0 ){
	$poll = new DemPoll( $poll_id );

	if( ! $poll ) return 'Poll not found';

	// обновим ID записи с которой вызван опрос, если такого ID нет в данных
	$from_post = is_object($from_post) ? $from_post->ID : intval($from_post);
	if( $from_post && ( ! $poll->in_posts || ! preg_match('~(?:^|,)'. $from_post .'(?:,|$)~', $poll->in_posts) ) ){
		global $wpdb;

		$new_in_posts = $poll->in_posts ? "$poll->in_posts,$from_post" : $from_post;
		$new_in_posts = trim( $new_in_posts, ','); // на всякий...
		$wpdb->update( $wpdb->democracy_q, array('in_posts'=>$new_in_posts), array('id'=>$poll_id) );
	}

	$show_screen = dem__query_poll_screen_choose( $poll );

	return $poll->get_screen( $show_screen, $before_title, $after_title );
}

/**
 * Gets poll results screen.
 * @param  integer  [$poll_id = 0]       Poll ID
 * @param  string   [$before_title = ''] HTML/text before poll title.
 * @param  string   [$after_title = '']  HTML/text after poll title.
 * @return string   Poll HTML code.
 */
function get_democracy_poll_results( $poll_id = 0, $before_title = '', $after_title = '' ){
	if( ! $poll = new DemPoll( $poll_id ) ) return '';

	if( $poll->open && ! $poll->show_results ) return __('Poll results hidden for now...','democracy-poll');

	return $poll->get_screen( 'voted', $before_title, $after_title );
}

/**
 * Show archives.
 *
 * @see get_democracy_archives()
 *
 * @param bool $hide_active Do not show active polls?
 * @return HTML
 */
function democracy_archives( $args = array() ){
	// backward compatibility
	if( func_num_args() > 1 ){
		$args = array(
			'active'       => ($hide_active = func_get_arg(0)) ? 0 : null,
			'before_title' => func_get_arg(1),
			'after_title'  => func_get_arg(2),
		);
	}

	echo get_democracy_archives( $args );
}

function get_democracy_archives( $args = array() ){

	// backward compatibility
	if( func_num_args() > 1 ){
		$args = array(
			'active'       => ($hide_active = func_get_arg(0)) ? 0 : null,
			'before_title' => func_get_arg(1),
			'after_title'  => func_get_arg(2),
		);
	}

	$args = wp_parse_args( $args, array(
		'before_title' => '',
		'after_title'  => '',
		'active'       => null,    // 1 (active), 0 (not active) or null (param not set).
		'open'         => null,    // 1 (opened), 0 (closed) or null (param not set) polls.
		'screen'       => 'voted',
		'per_page'     => 10,
		'add_from_posts' => true,    // add From posts: html block
		// internal
		'paged'        => (int) @ $_GET['dem_paged'],       // pagination page when 'limit' parameter is set
		'wrap'         => '<div class="dem-archives">%s</div>',
		'return'       => 'html',
	) );

	$html = get_dem_polls( $args );

	// pagination
	if( $GLOBALS['get_dem_polls_found_rows'] ){
		$pagination = paginate_links( array(
			'base'    => esc_url( remove_query_arg('dem_paged', $_SERVER['REQUEST_URI']) ) . '%_%',
			'format'  => '?dem_paged=%#%',
			'current' => max( 1, (int) @ $_GET['dem_paged'] ),
			'total'   => floor( $GLOBALS['get_dem_polls_found_rows'] / (int) $args['per_page'] ),
		) );

		$html .= '<div class="dem-paging">'. $pagination .'</div>';
	}

	return $html;
}

## gets polls by parametrs
function get_dem_polls( $args = array() ){

	global $wpdb;

	$args = (object) wp_parse_args( $args, array(
		'wrap'           => '<div class="dem-polls">%s</div>', // html block wrap
		'before_title'   => '',      // for single poll title
		'after_title'    => '',      // for single poll title
		'screen'         => 'vote',  // vote, voted
		'active'         => null,    // 1 (active), 0 (not active) or null (param not set).
		'open'           => null,    // 1 (opened), 0 (closed) or null (param not set) polls.
		'add_from_posts' => false,    // add From posts: html block
		'return'         => 'html',  // html, objects
		'paged'          => 1,       // pagination page when 'limit' parameter is set
		'per_page'       => 0,       // limit. 0 or -1 - no limit
	) );

	$WHERE = array();
	if( isset($args->active) )
		$WHERE[] = $wpdb->prepare( 'WHERE active = %d', intval($args->active) );
	if( isset($args->open) )
		$WHERE[] = $wpdb->prepare( 'WHERE open = %d', intval($args->open) );

	$LIMIT = '';
	$SET_FOUND_ROWS = false;
	if( $args->per_page > 0 ){
		$SET_FOUND_ROWS = true;
		$offset = $args->paged > 1 ? intval($args->paged) * $args->per_page : 0;
		$LIMIT = $wpdb->prepare( 'LIMIT %d, %d', $offset, $args->per_page );
	}

	$ORDER_BY = 'ORDER BY active DESC, open DESC, id DESC';

	$sql = "SELECT id FROM $wpdb->democracy_q ". implode(' AND ', $WHERE);
	$poll_ids = $wpdb->get_col( "$sql $ORDER_BY $LIMIT" );

	$GLOBALS['get_dem_polls_found_rows'] = $SET_FOUND_ROWS ? $wpdb->get_var( str_replace('SELECT id','SELECT count(*)',$sql) ) : null;

	$out = array();

	foreach( $poll_ids as $poll_id ){

		$DemPoll = new DemPoll( $poll_id );
		$poll    = $DemPoll->poll;

		if( $args->return === 'objects' ){
			$out[] = $DemPoll;
			continue;
		}

		// if return html is set
		$screen = isset($_REQUEST['dem_act']) ? dem__query_poll_screen_choose( $DemPoll ) : $args->screen;

		$elm_html = $DemPoll->get_screen( $screen, $args->before_title, $args->after_title );

		// in posts
		if( $args->add_from_posts && $posts = democr()->get_in_posts_posts($poll) ){
			$links = array();
			foreach( $posts as $post )
				$links[] = '<a href="'. get_permalink($post) .'">'. esc_html($post->post_title) .'</a>';

			$elm_html .= '
			<div class="dem-moreinfo">
				<b>'. __('From posts:','democracy-poll') .'</b>
				<ul>
					<li>'. implode("</li>\n<li>", $links) .'</li>
				</ul>
			</div>';
		}

		$out[] = '<div class="dem-elem-wrap">'. $elm_html .'</div>';
	}

	if( $args->return === 'objects' )
		return $out;
	else
		return sprintf( $args->wrap, implode( "\n", $out ) );
}

## Какой экран показать, на основе переданных запросов: 'voted' или 'vote'
function dem__query_poll_screen_choose( $poll ){
	if( $poll->open && ! $poll->show_results )
		return 'vote'; // view results is closed in options

	$screen = ( isset($_REQUEST['dem_act']) && isset($_REQUEST['dem_pid']) && $_REQUEST['dem_act'] == 'view' && $_REQUEST['dem_pid'] == $poll->id ) ? 'voted' : 'vote';

	return apply_filters('dem_poll_screen_choose', $screen, $poll );
}

