<?php

class Democ_Post_Metabox {

	static $pid_metakey;

	static function init() {
		self::$pid_metakey = Democracy_Poll::$pollid_meta_key;

		add_action('add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );
	}

	static function add_meta_box(){
		// публичные типы записей
		$post_types = get_post_types(array( 'publicly_queryable'=>true )) + array('page'=>'page');
		unset( $post_types['attachment'] );

		// WP 4.4+
		if( function_exists('get_term_meta') ){
			add_meta_box( 'democracy-metabox', __('Attach a poll to the post','democracy-poll'), array( __CLASS__, 'meta_box' ), $post_types, 'side' );
		}
		// OLD WP
		else {
			foreach( $post_types as $ptype )
				add_meta_box( 'democracy-metabox', __('Attach a poll to the post','democracy-poll'), array( __CLASS__, 'meta_box' ), $ptype, 'side' );
		}

	}

	static function meta_box( $post ) {
		global $wpdb;

		$poll_id = get_post_meta( $post->ID, self::$pid_metakey, true );

		$polls = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->democracy_q WHERE ( open = 1 OR id = %d ) ORDER BY id DESC", $poll_id )  );

		$options = array('<option value="0">- '. __('random active poll','democracy-poll') .' -</option>');
		foreach( $polls as $poll ) {
			$options[] = '
			<option value="'. (int) $poll->id .'" '. selected($poll->id, $poll_id, 0) .'>
				'. esc_html( $poll->question ) .' '.( $poll->active ? ' [active]' : '' ) . ( $poll->open ? '' : ' [closed]' ).'
			</option>';
		}

		echo '
		<select name="democ_metabox['. self::$pid_metakey .']" style="width:100%">
			'. implode('', $options ) .'
		</select>
		<p>
			'. sprintf( __( '%s - shortcode','democracy-poll' ), '[democracy id="current"]' ) .'
		</p>';

	}

	static function save_post( $post_id, $post ) {
		if(
			! isset( $_POST['democ_metabox'] )                                                         || // нет данных
			( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  )                                           || // выходим, если это автосохр.
			! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_'. $post_id )                          || // nonce проверка
			! current_user_can( get_post_type_object( $post->post_type )->cap->edit_post, $post_id )      // нет права редакт. запись
		)
			return;

		$pollid = intval( $_POST['democ_metabox'][ self::$pid_metakey ] );

		if( $pollid )
			update_post_meta( $post_id, self::$pid_metakey, $pollid );
		else
			delete_post_meta( $post_id, self::$pid_metakey );
	}


}
