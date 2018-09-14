<?php

## Вывод и голосование отдельного опроса.
## Нуждается в классе плагина Dem

class DemPoll {

	public $id; // id опроса, 0 или 'last'
	public $poll;

	public $has_voted        = false;
	public $votedFor         = '';
	public $blockVoting      = false; // блокировать голосование
	public $blockForVisitor  = false; // только для зарегистрированных
	public $not_show_results = false; // не показывать результаты

	public $inArchive    = false; // в архивной странице

	public $cachegear_on = false; // проверка включен ли механихм кэширвоания
	public $for_cache    = false;

	public $cookey;           // Название ключа cookie

	function __construct( $id = 0 ){
		global $wpdb;

		if( ! $id )
			$poll = $wpdb->get_row("SELECT * FROM $wpdb->democracy_q WHERE active = 1 ORDER BY RAND() LIMIT 1");
		elseif( $id === 'last' )
			$poll = $wpdb->get_row("SELECT * FROM $wpdb->democracy_q WHERE open = 1 ORDER BY id DESC LIMIT 1");
		else
			$poll = self::get_poll( $id );

		if( ! $poll ) return print "<!-- democracy: there is no poll -->";

		// устанавливаем необходимые переменные
		$this->id = (int) $poll->id;

		if( ! $this->id ) return; // влияет на весь класс, важно!

		$this->cookey = 'demPoll_' . $this->id;
		$this->poll   = $poll;

		// отключим демокраси опцию
		if( democr()->opt('democracy_off') )
			$this->poll->democratic = false;
		// отключим опцию переголосования
		if( democr()->opt('revote_off') )
			$this->poll->revote = false;

		$this->cachegear_on = democr()->is_cachegear_on();

		$this->set_voted_data();
		$this->set_answers(); // установим свойство $this->poll->answers

		// закрываем опрос т.к. срок закончился
		if( $this->poll->end && $this->poll->open && ( current_time('timestamp') > $this->poll->end ) )
			$wpdb->update( $wpdb->democracy_q, array( 'open'=>0 ), array( 'id'=>$this->id ) );

		// только для зарегистрированных
		if( ( democr()->opt('only_for_users') || $this->poll->forusers ) && ! is_user_logged_in() )
			$this->blockForVisitor = true;

		// блокировка возможности голосовать
		if( $this->blockForVisitor || ! $this->poll->open || $this->has_voted )
			$this->blockVoting = true;

		if( (! $poll->show_results || democr()->opt('dont_show_results')) && $poll->open && ( ! is_admin() || defined('DOING_AJAX') ) )
			$this->not_show_results = true;

		return $this->id;
	}

	function __get( $var ){
		return isset( $this->poll->{$var} ) ? $this->poll->{$var} : null;
	}

	static function get_poll( $poll_id ){
		global $wpdb;

		$poll = $wpdb->get_row("SELECT * FROM $wpdb->democracy_q WHERE id = ". intval( $poll_id ) ." LIMIT 1");
		$poll = apply_filters( 'dem_get_poll', $poll, $poll_id );

		return $poll;
	}

	/**
	 * Получает HTML опроса
	 * @param bool $show_screen Какой экран показывать: vote, voted, force_vote
	 * @return HTML
	 */
	function get_screen( $show_screen = 'vote', $before_title = '', $after_title = '' ){
		if( ! $this->id ) return false;

		$this->inArchive = ( @ $GLOBALS['post']->ID == democr()->opt('archive_page_id') ) && is_singular();

		if( $this->blockVoting && $show_screen != 'force_vote' ){
			$show_screen = 'voted';
		}

		$html  = '';
		$html .= democr()->add_css_once();

		$js_opts = array(
			'ajax_url'         => democr()->ajax_url,
			'pid'              => $this->id,
			'max_answs'        => ($this->poll->multiple > 1) ? $this->poll->multiple : 0,
			'answs_max_height' => democr()->opt('answs_max_height'),
			'anim_speed'       => democr()->opt('anim_speed'),
			'line_anim_speed'  => (int) democr()->opt('line_anim_speed'),
		);

		$html .= '<div id="democracy-'. $this->id .'" class="democracy" data-opts=\''. json_encode($js_opts) .'\' >';
			$html .=  ( $before_title ?: democr()->opt('before_title') ) . democr()->kses_html( $this->poll->question ) . ( $after_title  ?: democr()->opt('after_title') );

			// изменяемая часть
			$html .=  $this->get_screen_basis( $show_screen );
			// изменяемая часть

			$html .= $this->poll->note ? '<div class="dem-poll-note">'. wpautop( $this->poll->note ) .'</div>' : '';

			if( democr()->cuser_can_edit_poll($this->poll) ){
				$html .= '<a class="dem-edit-link" href="'. democr()->edit_poll_url($this->id) .'" title="'. __('Edit poll','democracy-poll') .'"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="1.5em" height="100%" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"><path d="M617.8,203.4l175.8,175.8l-445,445L172.9,648.4L617.8,203.4z M927,161l-78.4-78.4c-30.3-30.3-79.5-30.3-109.9,0l-75.1,75.1 l175.8,175.8l87.6-87.6C950.5,222.4,950.5,184.5,927,161z M80.9,895.5c-3.2,14.4,9.8,27.3,24.2,23.8L301,871.8L125.3,696L80.9,895.5z"/></svg></a>';
			}

		// copyright
		if( democr()->opt('show_copyright') && ( is_home() || is_front_page() ) ){
			$html .=  '<a class="dem-copyright" href="http://wp-kama.ru/?p=67" title="'. __('Download the Democracy Poll','democracy-poll') .'" onmouseenter="var $el = jQuery(this).find(\'span\'); $el.stop().animate({width:\'toggle\'},200); setTimeout(function(){ $el.stop().animate({width:\'toggle\'},200); }, 4000);"> © <span style="display:none;white-space:nowrap;">Kama</span></a>';
		}

		// loader
		if( democr()->opt('loader_fname') ){
			static $loader; // оптимизация, чтобы один раз выводился код на странице
			if( ! $loader ){
				$loader = '<div class="dem-loader"><div>'. file_get_contents( DEMOC_PATH .'styles/loaders/'. democr()->opt('loader_fname') ) .'</div></div>';
				$html .=  $loader;
			}
		}

		$html .=  "</div><!--democracy-->";


		// для КЭША
		if( $this->cachegear_on && ! $this->inArchive ){
			$html .= '
			<!--noindex-->
			<div class="dem-cache-screens" style="display:none;" data-opt_logs="'. democr()->opt('keep_logs') .'">';

			// запоминаем
			$votedFor = $this->votedFor;
			$this->votedFor = false;
			$this->for_cache = 1;

			$compress = function( $str ){
				return preg_replace("~[\n\r\t]~u", '', preg_replace('~\s+~u',' ',$str) );
			};

			if( ! $this->not_show_results )
				$html .= $compress( $this->get_screen_basis('voted') );  // voted_screen

			if( $this->poll->open )
				$html .= $compress( $this->get_screen_basis('force_vote') ); // vote_screen

			$this->for_cache = 0;
			$this->votedFor = $votedFor; // возвращаем

			$html .=	'
			</div>
			<!--/noindex-->';
		}

		if( ! democr()->opt('disable_js') )
			democr()->add_js_once(); // подключаем скрипты (один раз)

		return $html;
	}

	/**
	 * Получает сердце HTML опроса (изменяемую часть)
	 * @param bool $show_screen
	 * @return HTML
	 */
	function get_screen_basis( $show_screen = 'vote' ){
		$class_suffix = $this->for_cache ? '-cache' : '';

		if( $this->not_show_results )
			$show_screen = 'force_vote';

		$screen = ( $show_screen == 'vote' || $show_screen == 'force_vote' ) ? 'vote' : 'voted';

		$html = '<div class="dem-screen'. $class_suffix .' '. $screen  .'">';
		$html .= ( $screen == 'vote' ) ? $this->get_vote_screen() : $this->get_result_screen();
		$html .=  '</div>';

		if( ! $this->for_cache )
			$html .=  '<noscript>Poll Options are limited because JavaScript is disabled in your browser.</noscript>';

		return $html;
	}

	/**
	 * Получает код для голосования
	 * @return HTML
	 */
	function get_vote_screen(){
		if( ! $this->id ) return false;

		$poll = $this->poll;

		$auto_vote_on_select = ( ! $poll->multiple && $poll->revote && democr()->opt('hide_vote_button') );

		$html = $dem_act = ''; // vars

		$html .= '<form method="POST" action="#democracy-'. $this->id .'">';
			$html .= '<ul class="dem-vote">';

				$type = $poll->multiple ? 'checkbox' : 'radio';

				foreach( $poll->answers as $answer ){
					$answer = apply_filters('dem_vote_screen_answer', $answer );

					$auto_vote = $auto_vote_on_select ? 'data-dem-act="vote"' : '';

					$checked = $disabled = '';
					if( $this->votedFor ){
						if( in_array( $answer->aid, explode(',', $this->votedFor ) ) )
							$checked = ' checked="checked"';

						$disabled = ' disabled="disabled"';
					}

					$html .= '
					<li data-aid="'. $answer->aid .'">
						<label class="dem__'. $type .'_label">
							<input class="dem__'. $type .'" '. $auto_vote .' type="'. $type .'" value="'. $answer->aid .'" name="answer_ids[]"'. $checked . $disabled .'><span class="dem__spot"></span> '. $answer->answer .'
						</label>
					</li>';
				}

				if( $poll->democratic && ! $this->blockVoting ){
					$html .= '<li class="dem-add-answer"><a href="javascript:void(0);" rel="nofollow" data-dem-act="newAnswer" class="dem-link">'. _x('Add your answer','front','democracy-poll') .'</a></li>';
				}
			$html .= "</ul>";

			$html .= '<div class="dem-bottom">';
				$html .= '<input type="hidden" name="dem_act" value="vote">';
				$html .= '<input type="hidden" name="dem_pid" value="'. $this->id .'">';

				$btnVoted  = '<div class="dem-voted-button"><input class="dem-button '. democr()->opt('btn_class') .'" type="submit" value="'. _x('Already voted...','front','democracy-poll') .'" disabled="disabled"></div>';
				$btnVote   = '<div class="dem-vote-button"><input class="dem-button '. democr()->opt('btn_class') .'" type="submit" value="'. _x('Vote','front','democracy-poll') .'" data-dem-act="vote"></div>';

				if( $auto_vote_on_select )
					$btnVote = '';

				$for_users_alert = $this->blockForVisitor ? '<div class="dem-only-users">'. self::registered_only_alert_text() .'</div>' : '';

				// для экша
				if( $this->for_cache ){
					$html .= self::_voted_notice();

					if( $for_users_alert )
						$html .= str_replace( array('<div', 'class="'), array('<div style="display:none;"', 'class="dem-notice '), $for_users_alert );

					if( $poll->revote )
						$html .= preg_replace('~(<[^>]+)~s', '$1 style="display:none;"', $this->_revote_btn(), 1 );
					else
						$html .= substr_replace( $btnVoted, '<div style="display:none;"', 0, 4 );
					$html .= $btnVote;
				}
				// не для кэша
				else {
					if( $for_users_alert ){
						$html .= $for_users_alert;
					}
					else{
						if( $this->has_voted )
							$html .= $poll->revote ? $this->_revote_btn() : $btnVoted;
						else
							$html .= $btnVote;
					}

				}

				if( ! $this->not_show_results && ! democr()->opt('dont_show_results_link') )
					$html .= '<a href="javascript:void(0);" class="dem-link dem-results-link" data-dem-act="view" rel="nofollow">'. _x('Results','front','democracy-poll') .'</a>';


			$html .= '</div>';

		$html .= '</form>';

		return apply_filters('dem_vote_screen', $html, $this );
	}

	/**
	 * Получает код результатов голосования
	 * @return HTML
	 */
	function get_result_screen(){
		if( ! $this->id ) return false;
		$poll = $this->poll;

		// отсортируем по голосам
		$answers = Democracy_Poll::objects_array_sort( $poll->answers, array('votes'=>'desc') );

		$html = '';

		$max = $total = 0;

		foreach( $answers as $answer ){
			$total += $answer->votes;
			if( $max < $answer->votes )
				$max = $answer->votes;
		}

		$voted_class = 'dem-voted-this';
		$voted_txt   = _x('This is Your vote.','front','democracy-poll');
		$html .= '<ul class="dem-answers" data-voted-class="'. $voted_class .'" data-voted-txt="'. $voted_txt .'">';
			foreach( $answers as $answer ){
				// склонение голосов
				$__sclonenie = function( $number, $titles, $nonum = false ){
					$titles = explode(',', $titles);
					$cases = array (2, 0, 1, 1, 1, 2);
					return ($nonum ? '' : "$number "). $titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];
				};

				$answer = apply_filters('dem_result_screen_answer', $answer );

				$votes         = (int) $answer->votes;
				$is_voted_this = ( $this->has_voted && in_array( $answer->aid, explode(',', $this->votedFor) ) );
				$is_winner     = ( $max == $votes );

				$novoted_class = ( $votes == 0 ) ? ' dem-novoted' : '';
				$li_class      = ' class="'. ( $is_winner ? 'dem-winner':'' ) . ( $is_voted_this ? " $voted_class":'' ) . $novoted_class .'"';
				$sup           = $answer->added_by ? '<sup class="dem-star" title="'. _x('The answer was added by a visitor','front','democracy-poll') .'">*</sup>' : '';
				$percent       = ( $votes > 0 ) ? round($votes / $total * 100) : 0;

				$percent_txt = sprintf( _x('%s - %s%% of all votes','front','democracy-poll'), $__sclonenie( $votes, _x('vote,votes,votes','front','democracy-poll') ), $percent );
				$title       = ( $is_voted_this ? $voted_txt : '' ) . ' '. $percent_txt;
				$title       = " title='$title'";

				$votes_txt = $votes .' '. '<span class="votxt">'. $__sclonenie( $votes, _x('vote,votes,votes','front','democracy-poll'), 'nonum' ) .'</span>';

				$html .= '<li'. $li_class . $title .' data-aid="'. $answer->aid .'">';
					$label_perc_txt = ' <span class="dem-label-percent-txt">'. $percent .'%, '. $votes_txt .'</span>';
					$percent_txt    = '<div class="dem-percent-txt">'. $percent_txt .'</div>';
					$votes_txt      = '<div class="dem-votes-txt">
						<span class="dem-votes-txt-votes">'. $votes_txt .'</span>
						'. ( ( $percent > 0 ) ? ' <span class="dem-votes-txt-percent">'. $percent .'%</span>' : '' ) . '
						</div>';

					$html .= '<div class="dem-label">'. $answer->answer . $sup . $label_perc_txt .'</div>';

					// css процент
					$graph_percent = ( ( ! democr()->opt('graph_from_total') && $percent != 0 ) ? round( $votes / $max * 100 ) : $percent ) . '%';
					if( $graph_percent == 0 ) $graph_percent = '1px';

					$html .= '<div class="dem-graph">';
						$html .= '<div class="dem-fill" '.( democr()->opt('line_anim_speed') ? 'data-width="' : 'style="width:' ). $graph_percent .'"></div>';
						$html .= $votes_txt;
						$html .= $percent_txt;
					$html .= "</div>";
				$html .= "</li>";
			}
		$html .= '</ul>';

		// dem-bottom
		$html .= '<div class="dem-bottom">';
			$html .= '<div class="dem-poll-info">';
				$html .= '<div class="dem-total-votes">'. sprintf( _x('Total Votes: %s','front','democracy-poll'), $total ) .'</div>';
				$html .= ($poll->multiple  ? '<div class="dem-users-voted">'. sprintf( _x('Voters: %s','front','democracy-poll'), $poll->users_voted ) .'</div>' : '');
				$html .= '
				<div class="dem-date" title="'. _x('Begin','front','democracy-poll') .'">
					<span class="dem-begin-date">'. date_i18n( get_option('date_format'), $poll->added ) .'</span>
					'.( $poll->end ? ' - <span class="dem-end-date" title="'. _x('End','front','democracy-poll') .'">'. date_i18n( get_option('date_format'), $poll->end ) .'</span>' : '' ).'
				</div>';
				$html .= $answer->added_by ? '<div class="dem-added-by-user"><span class="dem-star">*</span>'. _x(' - added by visitor','front','democracy-poll') .'</div>' : '';
				$html .= ! $poll->open     ? '<div>'. _x('Voting is closed','front','democracy-poll') .'</div>' : '';
				if( ! $this->inArchive && democr()->opt('archive_page_id') )
					$html .= '<a class="dem-archive-link dem-link" href="'. get_permalink( democr()->opt('archive_page_id') ) .'" rel="nofollow">'. _x('Polls Archive','front','democracy-poll') .'</a>';
			$html .= '</div>';

		if( $poll->open ){
			// заметка для незарегистрированных пользователей
			$for_users_alert = $this->blockForVisitor ? '<div class="dem-only-users">'. self::registered_only_alert_text() .'</div>' : '';

			// вернуться к голосованию
			$vote_btn = '<button type="button" class="dem-button dem-vote-link '. democr()->opt('btn_class') .'" data-dem-act="vote_screen">'. _x('Vote','front','democracy-poll') .'</button>';

			// для кэша
			if( $this->for_cache ){
				$html .= self::_voted_notice();

				if( $for_users_alert )
					$html .= str_replace( array('<div', 'class="'), array('<div style="display:none;"', 'class="dem-notice '), $for_users_alert );

				if( $poll->revote )
					$html .= $this->_revote_btn();
				else
					$html .= $vote_btn;
			}
			// не для кэша
			else {
				if( $for_users_alert ){
					$html .= $for_users_alert;
				}
				else {
					if( $this->has_voted ){
						if( $poll->revote )
							$html .= $this->_revote_btn();
					}
					else
						$html .= $vote_btn;
				}

			}
		}

		$html .= '</div>'; // / dem-bottom


		return apply_filters('dem_result_screen', $html, $this );
	}

	static function registered_only_alert_text(){
		return str_replace(
			'<a',
			'<a href="'. esc_url( wp_login_url( $_SERVER['REQUEST_URI'] ) ) .'" rel="nofollow"',
			_x('Only registered users can vote. <a>Login</a> to vote.','front','democracy-poll')
		);
	}

	function _revote_btn(){
		return '
		<span class="dem-revote-button-wrap">
		<form action="#democracy-'. $this->id .'" method="POST">
			<input type="hidden" name="dem_act" value="delVoted">
			<input type="hidden" name="dem_pid" value="'. $this->id .'">
			<input type="submit" value="'. _x('Revote','front','democracy-poll') .'" class="dem-revote-link dem-revote-button dem-button '. democr()->opt('btn_class') .'" data-dem-act="delVoted" data-confirm-text="'. _x('Are you sure you want cancel the votes?','front','democracy-poll') .'">
		</form>
		</span>';
	}

	/**
	 * заметка: вы уже голосовали
	 * @return string Текст заметки
	 */
	static function _voted_notice( $msg = '' ){
		if( ! $msg ){
			return '
			<div class="dem-notice dem-youarevote" style="display:none;">
				<div class="dem-notice-close" onclick="jQuery(this).parent().fadeOut();">&times;</div>
				'. _x('You or your IP had already vote.','front','democracy-poll') .'
			</div>';
		}
		else {
			return '
			<div class="dem-notice">
				<div class="dem-notice-close" onclick="jQuery(this).parent().fadeOut();">&times;</div>
				'. $msg .'
			</div>';
		}
	}

	/**
	 * Добавляет голос
	 * @param  str   $aids ID ответов через запятую. Там может быть строка, тогда она будет добавлена, как ответ пользователя.
	 * @return WP_Error/string $aids IDs , separated
	 */
	function vote( $aids ){
		if( ! $this->id ) return new WP_Error('vote_err', 'ERROR: no id');

		if( $this->has_voted && ($_COOKIE[ $this->cookey ] === 'notVote') )
			$this->set_cookie(); // установим куки повторно, был баг...

		// must run after "$this->has_voted" check, because if $this->has_voted then $this->blockVoting always true
		if( $this->blockVoting ) return new WP_Error('vote_err', 'ERROR: voting is blocked...');

		global $wpdb;

		if( ! is_array( $aids ) ){
			$aids = trim( $aids );
			$aids = explode('~', $aids );
		}

		$aids = array_map('trim', $aids ); // could have string (free answer)
		$aids = array_filter( $aids );

		// проверим кол-во
		if( $this->poll->multiple > 1 && count($aids) > $this->poll->multiple )
			return new WP_Error('vote_err', __('ERROR: You select more number of answers than it is allowed...','democracy-poll') );

		// Add user free answer
		// Checks values of $aids array, trying to find string, if has - it's free answer
		if( $this->poll->democratic ){
			$new_free_answer = false;

			foreach( $aids as $k => $id ){
				if( ! is_numeric($id) ){
					$new_free_answer = $id;
					unset( $aids[$k] ); // удалим из общего массива, чтобы дельше ответа не было

					// clear array because multiple voting is blocked
					if( ! $this->poll->multiple ) $aids = array();

					//break; !!!!NO
				}
			}

			// есть ответ пользователя, добавляем и голосуем
			if( $new_free_answer && ($aid = $this->_add_democratic_answer($new_free_answer)) ){
				$aids[] = $aid;
			}
		}

		// AND clause
		$AND = '';

		// collect $ids into string for cookie. There are only ints
		$aids = array_filter($aids);

		if( ! $aids ) return new WP_Error('vote_err', 'ERROR: internal - no ids. Contact developer...' );

		// один ответ
		if( count($aids) == 1 ){
			$aids = reset($aids); // $aids must be defined
			$AND = $wpdb->prepare(" AND aid = %d LIMIT 1", $aids );
		}
		// несколко ответов (multiple)
		elseif( $this->poll->multiple ){
			$aids = array_map('intval', $aids);

			// не больше чем разрешено...
			if( count($aids) > (int) $this->poll->multiple )
				$aids = array_slice( $aids, 0, $this->poll->multiple );

			$aids = implode(',', $aids ); // нужна отдельно!
			$AND = ' AND aid IN ('. $aids .')';
		}

		if( ! $AND ) return new WP_Error('vote_err', 'ERROR: internal - no $AND. Contact developer...' );

		// update in DB
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->democracy_a SET votes = (votes+1) WHERE qid = %d $AND", $this->id ) );
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->democracy_q SET users_voted = (users_voted+1) WHERE id = %d", $this->id ) );

		$this->poll->users_voted++;

		$this->blockVoting = true;
		$this->has_voted   = true;
		$this->votedFor    = $aids;

		$this->set_answers(); // переустановим ответы

		$this->set_cookie(); // установим куки

		if( democr()->opt('keep_logs') )
			$this->add_logs();

		do_action_ref_array( 'dem_voted', array( $this->votedFor, $this->poll, & $this ) );

		return $this->votedFor;
	}

	/**
	 * Удаляет данные пользователя о голосовании.
	 * Отменяет установленные $this->has_voted и $this->votedFor
	 * Должна вызываться до вывода данных на экран
	 */
	function delete_vote(){
		if ( ! $this->id ) return false;
		if ( ! $this->poll->revote ) return false;

		// если опция логов не включена, то отнимаем по кукам,
		// тут голоса можно откручивать назад, потому что разные браузеры проверить не получится
		if( ! democr()->opt('keep_logs') )
			$this->minus_vote();

		// Прежде чем удалять, проверим включена ли опция ведения логов и есть ли записи о голосовании в БД,
		// так как куки могут удалить и тогда, данные о голосовании пойдут в минус
		if( democr()->opt('keep_logs') && $this->get_vote_log() ){
			$this->minus_vote();
			$this->delete_vote_from_log(); // чистим логи
		}

		$this->unset_cookie();

		$this->has_voted   = false;
		$this->votedFor    = false;
		$this->blockVoting = $this->poll->open ? false : true; // тут еще нужно учесть открыт опрос или нет...

		$this->set_answers(); // переустановим ответы, если вдруг добавленный ответ был удален

		do_action_ref_array('dem_vote_deleted', array( $this->poll, & $this ) );
	}

	private function _add_democratic_answer( $answer ){
		global $wpdb;

		$new_answer = democr()->sanitize_answer_data( $answer, 'democratic_answer' );
		$new_answer = wp_unslash( $new_answer );

		// проверим нет ли уже такого ответа
		if( $wpdb->query( $wpdb->prepare("SELECT aid FROM $wpdb->democracy_a WHERE answer = '%s' AND qid = $this->id", $new_answer ) ) )
			return;

		$cuser_id = get_current_user_id();

		// добавлен из фронта - демократический вариант ответа не важно какой юзер!
		$added_by = $cuser_id ?: self::get_ip();
		$added_by .= (! $cuser_id || $this->poll->added_user != $cuser_id ) ? '-new' : '';

		// если есть порядок, ставим 'max+1'
		$aorder = $this->poll->answers[0]->aorder > 0 ? max(wp_list_pluck($this->poll->answers, 'aorder')) +1 : 0;

		$inserted = $wpdb->insert( $wpdb->democracy_a, array( 'qid'=>$this->id, 'answer'=>$new_answer, 'votes'=>0, 'added_by'=>$added_by, 'aorder'=>$aorder ) );

		return $inserted ? $wpdb->insert_id : 0;
	}

	/**
	 * Устанавливает глобальные переменные $this->has_voted и $this->votedFor
	 */
	protected function set_voted_data(){
		if( ! $this->id )
			return false;

		// база приоритетнее куков, потому что в одном браузере можно отменить голосование, а куки в другом будут показывать что голосовал...
		// ЗАМЕТКА: обновим куки, если не совпадают. Потому что в разных браузерах могут быть разыне. Не работает,
		// потому что куки нужно устанавливать перед выводом данных и вообще так делать не нужно, потмоу что проверка
		// по кукам становится не нужной в целом...
		if( democr()->opt('keep_logs') && ($res = $this->get_vote_log()) ){
			$this->has_voted = true;
			$this->votedFor = $res->aids;
		}
		// проверяем куки
		elseif( isset($_COOKIE[ $this->cookey ]) && ($_COOKIE[ $this->cookey ] != 'notVote') ){
			$this->has_voted = true;
			$this->votedFor = preg_replace('/[^0-9, ]/', '', $_COOKIE[ $this->cookey ] ); // чистим
		}

	}

	## отнимает голоса в БД и удаляет ответ, если надо
	protected function minus_vote(){
		global $wpdb;

		$INaids = implode(',', $this->get_answ_aids_from_str( $this->votedFor ) ); // чистит для БД!

		if( ! $INaids ) return false;

		// сначала удалим добавленные пользователем ответы, если они есть и у них 0 или 1 голос
		$r1 = $wpdb->query("DELETE FROM $wpdb->democracy_a WHERE added_by != '' AND votes IN (0,1) AND aid IN ($INaids) ORDER BY aid DESC LIMIT 1");

		// отнимаем голоса
		$r2 = $wpdb->query("UPDATE $wpdb->democracy_a SET votes = IF( votes>0, votes-1, 0 ) WHERE aid IN ($INaids)");
		// отнимаем кол голосовавших
		$r3 = $wpdb->query("UPDATE $wpdb->democracy_q SET users_voted = IF( users_voted>0, users_voted-1, 0 ) WHERE id = ". (int) $this->id );

		return ($r1 || $r2);
	}

	/**
	 * Получает массив ID ответов из переданной строки, где id разделены запятой.
	 * Чистит для БД!
	 * @param  string $str Строка с ID ответов
	 * @return array  ID ответов
	 */
	protected function get_answ_aids_from_str( $str ){
		$arr = explode(',', $str);
		$arr = array_map('trim', $arr );
		$arr = array_map('intval', $arr );
		$arr = array_filter( $arr ); // удалим пустые
		return $arr;
	}

	## время до которого логи будут жить
	function get_expire_time(){
		return current_time('timestamp', $utc = 1) + intval( floatval( democr()->opt('cookie_days') ) * DAY_IN_SECONDS );
	}

	/**
	 * Устанавливает куки для текущего опроса
	 * @param str $value Значение куки, по умолчанию текущие голоса.
	 * @param int $expire Время окончания кики.
	 * @return none.
	 */
	function set_cookie( $value = '', $expire = false ){
		$expire = $expire ?: $this->get_expire_time();
		$value  = $value  ?: $this->votedFor;

		setcookie( $this->cookey, $value, $expire, COOKIEPATH );

		$_COOKIE[ $this->cookey ] = $value;
	}

	function unset_cookie(){
		setcookie( $this->cookey, null, strtotime('-1 day'), COOKIEPATH );
		$_COOKIE[ $this->cookey ] = '';
	}

	/**
	 * Устанавливает ответы в $this->poll->answers и сортирует их в нужном порядке.
	 * @return array Массив объектов
	 */
	protected function set_answers(){
		global $wpdb;

		$answers = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->democracy_a WHERE qid = %d", $this->id ) ) ;

		// если не установлен порядок
		if( $answers[0]->aorder == 0  ){
			$ord = $this->poll->answers_order ?: democr()->opt('order_answers');

			if( $ord == 'by_winner' || $ord == 1 )
				$answers = Democracy_Poll::objects_array_sort( $answers, array('votes'=>'desc') );
			elseif( $ord == 'mix' )
				shuffle( $answers );
			elseif( $ord == 'by_id' ){}
		}
		// по порядку
		else
			$answers = Democracy_Poll::objects_array_sort( $answers, array('aorder'=>'asc') );

		$answers = apply_filters('dem_set_answers', $answers, $this->poll );

		return $this->poll->answers = $answers;
	}

	/**
	 * Получает строку логов по ID или IP пользователя
	 * @return object/null democracy_log table row
	 */
	function get_vote_log(){
		global $wpdb;

		$user_ip = self::get_ip();
		$AND = $wpdb->prepare('AND ip = %s', $user_ip );

		// нужно проверять юзера и IP отдельно! Иначе, если юзер не авторизован его id=0 и он будет совпадать с другими пользователями
		if( $user_id = get_current_user_id() ){
			// только для юзеров, IP не учитывается - если вы голосовали как посетитель, а потом залогинились, то можно голосовать еще раз
			$AND = $wpdb->prepare('AND userid = %d', $user_id );
			//$AND = $wpdb->prepare('AND (userid = %d OR ip = %s)', $user_id, $user_ip );
		}

		$AND .= $wpdb->prepare(' AND expire > %d', time() );

		// получаем первую строку найденого лога по IP или ID юзера
		$sql = $wpdb->prepare("SELECT * FROM $wpdb->democracy_log WHERE qid = %d $AND ORDER BY logid DESC LIMIT 1", $this->id );

		return $wpdb->get_row( $sql );
	}

	/**
	 * Удаляет записи о голосовании в логах.
	 * @return bool
	 */
	protected function delete_vote_from_log(){
		global $wpdb;

		$user_ip = self::get_ip();

		// Ищем пользвоателя или IP в логах
		$sql = $wpdb->prepare("DELETE FROM $wpdb->democracy_log WHERE qid = %d AND (ip = %s OR userid = %d)", $this->id, $user_ip, get_current_user_id() );
		return $wpdb->query( $sql );
	}

	protected function add_logs(){
		if( ! $this->id ) return false;

		global $wpdb;

		$ip = self::get_ip();

		return $wpdb->insert( $wpdb->democracy_log, array(
			'ip'      => $ip,
			'qid'     => $this->id,
			'aids'    => $this->votedFor,
			'userid'  => (int) get_current_user_id(),
			'date'    => current_time('mysql'),
			'expire'  => $this->get_expire_time(),
			'ip_info' => Democracy_Poll::ip_info_format( $ip ),
		) );

	}

	static function shortcode_html( $poll_id ){
		if( ! $poll_id )
			return '';
		return '<span style="cursor:pointer;padding:0 2px;background:#fff;" onclick="var sel = window.getSelection(), range = document.createRange(); range.selectNodeContents(this); sel.removeAllRanges(); sel.addRange(range);">[democracy id="'. $poll_id .'"]</span>';
	}

	static function get_ip(){
		if( democr()->opt('soft_ip_detect') ){
			// cloudflare IP support
			$ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '';
			if( ! filter_var($ip, FILTER_VALIDATE_IP) ) $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
			if( ! filter_var($ip, FILTER_VALIDATE_IP) ) $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
			if( ! filter_var($ip, FILTER_VALIDATE_IP) )	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}
		else {
			$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}

		$ip = apply_filters( 'dem_get_ip', $ip );

		if( ! filter_var($ip, FILTER_VALIDATE_IP) )	$ip = 'no_IP__'. rand(1,999999);

		return $ip;
	}

}


