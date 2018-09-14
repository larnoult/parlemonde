<?php


class Dem_List_Table_Logs extends WP_List_Table {
	static $cache;

	public $poll_id;

	function __construct(){
		parent::__construct(array(
			'singular' => 'demlog',
			'plural'   => 'demlogs',
			'ajax'     => false,
		));

		$this->bulk_action_handler();

		// screen option
		add_screen_option( 'per_page', array(
			'label'   => 'Показывать на странице',
			'default' => 20,
			'option'  => 'dem_logs_per_page',
		) );

		// the logs of poll
		$this->poll_id = (int) @ $_GET['poll'];

		$this->prepare_items();
	}

	private function bulk_action_handler(){
		$nonce = & $_POST['_wpnonce'];
		if ( ! $nonce || ! ($action = $this->current_action()) )
			return;

		if( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) )
			wp_die('nonce error');

		if( ! $log_ids = array_filter( array_map('intval', $_POST['logids']) ) ){
			democr()->msg['error'][] = __('Nothing was selected.', 'democracy-poll');
			return;
		}

		// delete
		if( $action == 'delete_only_logs' )
			democr()->del_only_logs( $log_ids );

		// delete with votes
		if( $action == 'delete_logs_votes' )
			democr()->del_logs_and_votes( $log_ids );

	}

	function prepare_items(){
		global $wpdb;

		$per_page = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;

		//$this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns() );

		// Строим запрос
		// where --- filters ----
		$where   = 'WHERE 1';
		if( $this->poll_id )                    $where .= ' AND qid = ' . $this->poll_id;
		if( $userid = (int) @ $_GET['userid'] ) $where .= ' AND userid = ' . intval($userid);
		if( $ip = @ $_GET['ip'] )               $where .= $wpdb->prepare(' AND ip = %s', $ip );

		// новые ответы
		if( @ $_GET['filter'] === 'new_answers' ){
			// ID new ответов
			if( $aqids = $wpdb->get_results("SELECT DISTINCT aid, qid FROM $wpdb->democracy_a WHERE added_by LIKE '%-new'") )
				$where .= " AND qid IN (". implode(',', wp_list_pluck($aqids,'qid')) .") AND ( aids RLIKE '(^|,)(". implode('|', wp_list_pluck($aqids,'aid')) .")(,|$)' )";
			else
				$where .= ' AND 0 ';
		}

		// пагинация
		$this->set_pagination_args( array(
			'total_items' => $wpdb->get_var("SELECT count(*) FROM $wpdb->democracy_log $where"),
			'per_page'    => $per_page,
		) );
		$cur_page = (int) $this->get_pagenum(); // после set_pagination_args()

		// orderby offset
		$OFFSET   = 'LIMIT '. ( ($cur_page-1) * $per_page .','. $per_page );
		$order    = (@ strtolower($_GET['order'])=='asc') ? 'ASC' : 'DESC';
		$orderby  = @ $_GET['orderby'] ?: 'date';
		$ORDER_BY = $orderby ? sprintf("ORDER BY %s %s", sanitize_key($orderby), $order ) : '';

		// выполняем запрос
		$sql = "SELECT * FROM $wpdb->democracy_log $where $ORDER_BY $OFFSET";

		$this->items = $wpdb->get_results( $sql );
	}

	function get_columns(){
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'ip'      => 'IP',
			'ip_info' => __('IP info','democracy-poll'),
			'qid'     => __('Poll','democracy-poll'),
			'aids'    => __('Answer','democracy-poll'),
			'userid'  => __('User','democracy-poll'),
			'date'    => __('Date','democracy-poll'),
			'expire'  => __('Expire','democracy-poll'),
		);

		if( $this->poll_id )
			unset( $columns['qid'] );

		return $columns;
	}

	function get_hidden_columns(){
		return array();
	}

	function get_sortable_columns(){
		return array(
			'ip'      => array('ip','asc'),
			'ip_info' => array('ip_info','asc'),
			'qid'     => array('qid','desc'),
			'userid'  => array('userid','asc'),
			'date'    => array('date','desc'),
		);
	}

	protected function get_bulk_actions() {
		return array(
			'delete_only_logs' => __('Delete logs only', 'democracy-poll'),
			'delete_logs_votes' => __('Delete logs and votes', 'democracy-poll'),
		);
	}

	function table_title(){
		if( $this->poll_id ){
			if( ! $poll = $this->cache('polls', $this->poll_id) )
				$poll = $this->cache('polls', $this->poll_id, DemPoll::get_poll($this->poll_id) );

			echo '<h2><small>'. __('Poll\'s logs: ','democracy-poll') .'</small>'. democr()->kses_html( $poll->question ) .' <small><a href="'. democr()->edit_poll_url($this->poll_id) .'">'. __('Edit poll','democracy-poll') .'</a></small></h2>';
		}
	}

	## Extra controls to be displayed between bulk actions and pagination
	function extra_tablenav( $which ){
		if( $which == 'top' ){
			$newfilter = @ $_GET['filter'] === 'new_answers';

			echo '
			<div class="alignleft actions" style="margin-top:.3em;">
				'. ( democr()->opt('democracy_off') ? '' :
					'<a class="button button-small" href="'. esc_url( add_query_arg(array('filter'=>$newfilter?null:'new_answers')) ) .'">'.
						($newfilter?' &#215; ':'') . __('NEW answers logs','democracy-poll')
					.'</a>'
				) .'
			</div>';
		}
	}

	## если указать $val кэш будет устанавливаться
	function cache( $type, $key, $val = null ){
		$cache = & self::$cache[ $type ][ $key ];

		if( ! isset( $cache ) && $val !== null )
			$cache = $val;

		return $cache;
	}

	## Заполнения для колонок
	function column_default( $log, $col ){
		global $wpdb;

		// вывод
		if(0){}
		elseif( $col == 'ip' ){
			return '<a title="'. __('Search by IP', 'democracy-poll') .'" href="'. esc_url( add_query_arg( array('ip'=>$log->ip, 'poll'=>null) ) ) .'">'. esc_html($log->ip) .'</a>';
		}
		elseif( $col == 'ip_info' ){
			// обновим данные IP если их нет и прошло больше суток с последней попытки
			if( $log->ip ){
				if( ! $log->ip_info || ( is_numeric($log->ip_info) && (time()-DAY_IN_SECONDS) > $log->ip_info ) ){
					$log->ip_info = Democracy_Poll::ip_info_format( $log->ip );

					$wpdb->update( $wpdb->democracy_log, array('ip_info'=>$log->ip_info), array('logid'=>$log->logid) );
				}

				if( $log->ip_info && ! is_numeric($log->ip_info) ){
					list( $country_name, $county_code, $city ) = explode(',', $log->ip_info);

					// css background position
					if( ! $flagcss = $this->cache('flagcss', 'flagcss' ) )
						$flagcss = $this->cache('flagcss', 'flagcss', file_get_contents(DEMOC_PATH .'admin/country_flags/flags.css') );
					preg_match("~flag-". strtolower($county_code) ." \{([^}]+)\}~", $flagcss, $mm );
					$bg_pos = @ $mm[1] ?: '';

					$country_img = $bg_pos ? '<span title="'. $country_name . ($city?", $city":'') .'" style="cursor:help; display:inline-block; width:16px; height:11px; background:url('. DEMOC_URL .'admin/country_flags/flags.png) no-repeat; '. $bg_pos .'"></span> ' : '';
				}
			}

			return @ $country_img ? $country_img .' <span style="opacity:0.7">'. $country_name . ($city?", $city":'') .'</span>' : '';
		}
		elseif( $col == 'qid' ){
			if( ! $poll = $this->cache('polls', $log->qid ) )
				$poll = $this->cache('polls', $log->qid, DemPoll::get_poll( $log->qid ) );

			$actions = '';
			if( democr()->cuser_can_edit_poll($poll) )
				$actions = '
				<div class="row-actions">
					<span class="edit"><a href="'. democr()->edit_poll_url($poll->id) .'">'. __('Edit poll','democracy-poll') .'</a> | </span>
					<span class="edit"><a href="'. esc_url( add_query_arg( array('ip'=>null, 'poll'=>$log->qid) ) ) .'">'. __('Poll logs','democracy-poll') .'</a></span>
				</div>';

			return democr()->kses_html( $poll->question ) . $actions;
		}
		elseif( $col == 'userid' ){
			if( ! $user = $this->cache('users', $log->userid ) )
				$user = $this->cache('users', $log->userid, $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = ". (int) $log->userid ) );

			return esc_html( @ $user->user_nicename );
		}
		elseif( $col == 'expire' ){
			return date('Y-m-d H:i:s', $log->expire + (get_option('gmt_offset') * HOUR_IN_SECONDS) );
		}
		elseif( $col == 'aids' ){
			$out = array();
			foreach( explode(',', $log->aids ) as $aid ){
				if( ! $answ = $this->cache('answs', $aid ) )
					$answ = $this->cache('answs', $aid, $wpdb->get_row("SELECT * FROM $wpdb->democracy_a WHERE aid = ". (int) $aid ) );

				$new = democr()->is_new_answer($answ) ? ' <a href="'. democr()->edit_poll_url($log->qid) .'"><span style="color:red;">NEW</span></a>' : '';

				$out[] = '- '. esc_html( $answ->answer ) . $new;
			}

			return implode('<br>', $out );
		}
		else
			return isset( $log->$col ) ? $log->$col : print_r( $log, true );
	}

	function column_cb( $item ){
		echo '<label><input id="cb-select-'. @ $item->logid .'" type="checkbox" name="logids[]" value="'. @ $item->logid .'" /></label>';
	}

}
