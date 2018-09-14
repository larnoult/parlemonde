<?php


/**
 * Migration from "WP Polls" plugin
 *
 * wp-poll version: 2.73
 *
 * wp-poll tables:
 *         wp_pollsq
 *             pollq_id
 *             pollq_question
 *             pollq_timestamp
 *             pollq_totalvotes
 *             pollq_active
 *             pollq_expiry
 *             pollq_multiple
 *             pollq_totalvoters
 *
 *         wp_pollsa
 *             polla_aid
 *             polla_qid
 *             polla_answers
 *             polla_votes
 *
 *         wp_pollsip
 *             pollip_id
 *             pollip_qid
 *             pollip_aid
 *             pollip_ip
 *             pollip_host
 *             pollip_timestamp
 *             pollip_user
 *             pollip_userid
 *
 * wp-poll options:
 *         poll_template_voteheader
 *         poll_template_votebody
 *         poll_template_votefooter
 *         poll_template_resultheader
 *         poll_template_resultbody
 *         poll_template_resultbody2
 *         poll_template_resultfooter
 *         poll_template_resultfooter2
 *         poll_template_disable
 *         poll_template_error
 *         poll_currentpoll
 *         poll_latestpoll
 *         poll_archive_perpage
 *         poll_ans_sortby
 *         poll_ans_sortorder
 *         poll_ans_result_sortby
 *         poll_ans_result_sortorder
 *         poll_logging_method
 *         poll_allowtovote
 *         poll_archive_show
 *         poll_archive_url
 *         poll_bar
 *         poll_close
 *         poll_ajax_style
 *         poll_template_pollarchivelink
 *         widget_polls
 *         poll_archive_displaypoll
 *         poll_template_pollarchiveheader
 *         poll_template_pollarchivefooter
 *         poll_cookielog_expiry
 *         widget_polls-widget
 */
function dem_WP_Polls_migration(){
	global $wpdb;

	$migrate_data = get_option('democracy_migrated');

	// выходим, если миграция уже была...
	if( isset($migrate_data['wp-polls']) )
		return;

	// get polls of WP Polls
	$wppolls = $wpdb->get_results("SELECT * FROM $wpdb->pollsq");

	if( ! $wppolls ){
		democr()->msg[] = 'No WP Polls polls found.';
		return;
	}

	$collation = array();

	foreach( $wppolls as $wppoll ){
		// poll
		$wpdb->insert( $wpdb->democracy_q, array(
			'question'      => $wppoll->pollq_question,
			'added'         => (int) $wppoll->pollq_timestamp,
			'users_voted'   => ($wppoll->pollq_totalvoters != $wppoll->pollq_totalvotes) ? $wppoll->pollq_totalvoters : $wppoll->pollq_totalvotes,
			'multiple'      => $wppoll->pollq_multiple,
			'open'          => $wppoll->pollq_active,
			'end'           => (int) $wppoll->pollq_expiry,
			//
			'added_user'    => get_current_user_id(),
			'active'        => 0,
			'democratic'    => 0,
			'revote'        => 0,
			'show_results'  => 1,
		) );

		$qid = $wpdb->insert_id;

		$branch = & $collation[ $wppoll->pollq_id ];

		$branch['new_poll_id'] = $qid;

		// answers
		$wpanswers = $wpdb->get_results("SELECT * FROM $wpdb->pollsa WHERE polla_qid = ". (int) $wppoll->pollq_id );

		foreach( $wpanswers as $wpansw ){
			$wpdb->insert( $wpdb->democracy_a, array(
				'qid'    => $qid,
				'answer' => $wpansw->polla_answers,
				'votes'  => $wpansw->polla_votes,
			) );

			$aid = $wpdb->insert_id;

			$branch['answers:old->new'][ $wpansw->polla_aid ] = $aid;
		}

		// logs
		// create logs after all answers was created
		foreach( $wpanswers as $wpansw ){
			// logs
			// Так как каждый ответ в мульти логах идет на отдельной строке, придется группировать, чтобы собрать множественные ID
			// GROUP_CONCAT(pollip_aid) - соберет ID ответов через запятую - что надо!
			$group_col_names = 'pollip_ip, pollip_timestamp, pollip_userid';
			$sql = "SELECT pollip_qid, GROUP_CONCAT(pollip_aid) as pollip_aid, $group_col_names FROM $wpdb->pollsip WHERE pollip_qid = ". $wppoll->pollq_id ." AND pollip_aid = ". $wpansw->polla_aid ." GROUP BY $group_col_names";
			$wpips = $wpdb->get_results( $sql );
			//$wpips = $wpdb->get_results("SELECT * FROM $wpdb->pollsip WHERE pollip_qid = ". (int) $wppoll->pollq_id );

			// только если логи найдены
			if( $wpips ){
				// заменим на текущие ID
				foreach( $wpips as $wpip ){
					$_aids = array();
					foreach( explode(',', $wpip->pollip_aid) as $pollip_aid )
						$_aids[] = $branch['answers:old->new'][ $pollip_aid ];

					$wpdb->insert( $wpdb->democracy_log, array(
						'ip'      => $wpip->pollip_ip, // строка - format
						'qid'     => $qid,
						'aids'    => implode(',', $_aids ),
						'userid'  => $wpip->pollip_userid,
						'date'    => date('Y-m-d H:i:s', $wpip->pollip_timestamp ), // datatime - format
						'expire'  => $wpip->pollip_timestamp + YEAR_IN_SECONDS,
						'ip_info' => '', // country, country code, city - format
					) );

					$logid = $wpdb->insert_id;

					$branch['logs_created'][] = $logid;
				}
			}

		}

	}

	if( $migrate_data )
		$migrate_data['wp-polls'] = $collation;
	else
		$migrate_data = array('wp-polls'=> $collation );

	update_option('democracy_migrated', $migrate_data, 'no' );

	// options
	// опции не мигрируют - лишняя работа...

}
