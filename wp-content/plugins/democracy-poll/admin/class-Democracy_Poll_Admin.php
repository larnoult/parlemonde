<?php

### ADMIN PART
class Democracy_Poll_Admin extends Democracy_Poll {

	function __construct(){
		parent::__construct();

		// add the management page to the admin nav bar
		if( $this->admin_access ){
			add_action('admin_menu', array( &$this, 'register_option_page') );

			// Cохранение настроек экрана
			add_filter('set-screen-option', function( $status, $option, $value ){
				return in_array( $option, array('dem_polls_per_page', 'dem_logs_per_page') ) ? (int) $value : $status;
			}, 10, 3 );

		}

		// ссылка на настойки
		add_filter('plugin_action_links', array( & $this, 'setting_page_link'), 10, 2 );

		// TinyMCE кнопка WP2.5+
		if( self::$opt['tinymce_button'] ){
			require_once DEMOC_PATH . 'admin/class-Dem_Tinymce.php';
			new Dem_Tinymce();
		}

		// метабокс
		if( ! self::$opt['post_metabox_off'] ){
			require_once DEMOC_PATH . 'admin/class-Democ_Post_Metabox.php';
			Democ_Post_Metabox::init();
		}

	}

	## Страница плагина
	function register_option_page(){
		if( ! $this->admin_access )
			return;

		$title = __('Democracy Poll','democracy-poll');
		$hook_name = add_options_page( $title, $title, 'edit_posts', basename(DEMOC_PATH), array( $this, 'admin_page_output') );
		// notice: `edit_posts` (role more then subscriber) because capability tests inside the `admin_page.php` and `admin_page_load()`

		add_action("load-$hook_name", array( $this, 'admin_page_load') );
	}

	## admin page html
	function admin_page_output(){
		if( isset($_GET['msg']) && $_GET['msg'] === 'created' )
			$this->msg[] = __('New Poll Added','democracy-poll');

		require DEMOC_PATH .'admin/admin_page.php';
	}

	## предватирельная загрузка страницы настроек плагина, подключение стилей, скриптов, запросов и т.д.
	function admin_page_load(){
		// run upgrade
		if( $this->super_access ){
			// check and try forse upgrade
			if( isset($_POST['dem_forse_upgrade']) )
				update_option('democracy_version','0.1'); // hack

			dem_last_version_up();

			if( isset($_POST['dem_forse_upgrade']) ){
				wp_redirect( $_SERVER['REQUEST_URI'] );
				exit;
			}
		}

		//wp_enqueue_script('ace', DEMOC_URL .'admin/ace/src-min-noconflict/ace.js', array(), DEM_VER, true );

		// Iris Color Picker
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('wp-color-picker');

		// datepicker
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', DEMOC_URL . 'admin/css/jquery-ui.css', array(), DEM_VER ); //ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css

		// democracy
		wp_enqueue_script( 'democracy-scripts', DEMOC_URL . 'js/admin.js', array('jquery'/*,'ace'*/), DEM_VER, true );
		wp_enqueue_style( 'democracy-styles', DEMOC_URL . 'admin/css/admin.css', array(), DEM_VER );


		// handlers ------
		// adminform_verified
		if( isset($_REQUEST['_demnonce']) && wp_verify_nonce($_REQUEST['_demnonce'], 'dem_adminform') ){

			// options update
			if( $this->super_access ){
				$up = false;
				if( isset($_POST['dem_save_l10n']) || isset($_POST['dem_reset_l10n']) ){
					// обновляем произвольную локализацию
					if( isset($_POST['dem_save_l10n']) )  $up = $this->update_l10n();

					// сбрасываем произвольную локализацию
					if( isset($_POST['dem_reset_l10n']) ) $up = update_option('democracy_l10n', array() );

					// clear_cache
					self::handle_front_l10n('clear_cache');
				}

				// обновляем основные опции
				if( isset($_POST['dem_save_main_options']) )    $up = $this->update_options('main');
				// сбрасываем основные опции
				if( isset($_POST['dem_reset_main_options']) )   $up = $this->update_options('main_default');
				// обновляем опции дизайна
				if( isset($_POST['dem_save_design_options']) )  $up = $this->update_options('design');
				// сбрасываем опции дизайна
				if( isset($_POST['dem_reset_design_options']) ) $up = $this->update_options('design_default');

				// костыль, чтобы сразу применялся результат при отключении/включении тулбара
				if( $up ) self::$opt['toolbar_menu'] ? add_action('admin_bar_menu', array( & $this, 'toolbar'), 99) : remove_action('admin_bar_menu', array( & $this, 'toolbar'), 99);

				// запрос на создание страницы архива
				if( isset($_GET['dem_create_archive_page']) )   $this->dem_create_archive_page();

				// Clear logs
				if( isset($_GET['dem_clear_logs']) )            $this->clear_logs();
				if( isset($_GET['dem_del_closed_polls_logs']) ) $this->clear_closed_polls_logs();
				if( isset($_GET['dem_del_new_mark']) )          $this->clear_new_mark();
			}

			// make life easy
			$_poll_id = 0;
			$fn__setgetcheck = function( $name ) use ( & $_poll_id ){
				if( empty($_REQUEST[$name]) )
					return $_poll_id = 0;

				$_poll_id = intval($_REQUEST[$name]);

				return democr()->cuser_can_edit_poll($_poll_id) ? $_poll_id : 0;
			};

			// Add/update a poll
			if(	isset($_POST['dmc_create_poll']) || ( $fn__setgetcheck('dmc_update_poll') ) )
				$this->insert_poll_handler();

			// delete a poll
			if( $fn__setgetcheck('delete_poll') )
				$this->delete_poll( $_poll_id );

			// activates a poll
			if( $fn__setgetcheck('dmc_activate_poll')   ) $this->poll_activation( $_poll_id, true );         //echo "$_poll_id ";
			// deactivates a poll
			if( $fn__setgetcheck('dmc_deactivate_poll') ) $this->poll_activation( $_poll_id, false );        //echo "$_poll_id ";

			// open voting a poll
			if( $fn__setgetcheck('dmc_open_poll') )  $this->poll_opening( $_poll_id, 1 );                    //echo "$_poll_id ";
			// close voting a poll
			if( $fn__setgetcheck('dmc_close_poll') ) $this->poll_opening( $_poll_id, 0 );                    //echo "$_poll_id ";

		}

		// LOGS
		//if( isset($_GET['del_poll_logs']) && wp_verify_nonce($_GET['del_poll_logs'], 'del_poll_logs') )
		//	$this->del_poll_logs( $_GET['poll'] );

		// admin subpages (after handlers) ------
		$sp = & $_GET['subpage'];
		if(0){}
		elseif( $sp === 'general_settings' ){}
		elseif( $sp === 'l10n' ){}
		elseif( $sp === 'design' ){
			// CodeMirror
			if( function_exists('wp_enqueue_code_editor') ){
				add_action( 'admin_enqueue_scripts', function() {
					// подключаем редактор кода для HTML.
					$settings = wp_enqueue_code_editor( array( 'type'=>'text/css' ) );

					// инициализация
					wp_add_inline_script( 'code-editor', sprintf(
						'jQuery( function(){  wp.codeEditor.initialize( jQuery("textarea[name=additional_css]"), %s );  } );', wp_json_encode( $settings )
					));
				}, 99 );
			}
		}
		// add/edit poll
		elseif( $sp === 'add_new' || isset($_GET['edit_poll']) ){}
		// logs list
		elseif( $sp === 'logs' ){
			require_once DEMOC_PATH . 'admin/class-Dem_List_Table_Logs.php';
			$this->list_table = new Dem_List_Table_Logs();
		}
		// polls list
		else {
			require_once DEMOC_PATH . 'admin/class-Dem_List_Table_Polls.php';
			$this->list_table = new Dem_List_Table_Polls();
		}

	}

	### PLUGIN OPTIONS ----------

	/**
	 * Обнолвяет опции. Если опция не передана, то на её место будет записано 0
	 * @param bool $type Какие опции обновлять: default, main_default, design_default, main, design
	 * @return none
	 */
	function update_options( $type ){
		$def_opt = $this->default_options();

		// reset all
		if( $type == 'default' ){
			$this->update_options('main_default');
			$this->update_options('design_default');
		}

		// reset main & design options
		if( $type == 'main_default' || $type == 'design_default' ){
			$_type = str_replace('_default', '', $type );
			foreach( $def_opt[$_type] as $k => $value ) self::$opt[$k] = $value;
		}

		// sanitize on POST request
		$POSTDATA = wp_unslash( $_POST );
		if( isset($POSTDATA['dem']) && ($type == 'main' || $type == 'design') ){
			foreach( $def_opt[$type] as $k => $v ){
				$value = isset($POSTDATA['dem'][$k]) ? $POSTDATA['dem'][$k] : 0; // именно 0/null, а не $v для checkbox

				if(0){}
				elseif( in_array( $k, array('before_title','after_title') ) ){
					$value = wp_kses( $value, 'post');
				}
				// only admin can change 'access_roles'
				elseif( $k === 'access_roles' ){
					if( $this->super_access )
						$value = array_map('sanitize_key', (array) $value ); // sanitize anyway
					else
						$value = (array) self::$opt[$k]; // leave as it was - only admin can change 'access_roles'
				}
				// all with sanitize_text_field
				else {
					$value = is_array($value) ? array_map('sanitize_text_field',$value) : sanitize_text_field($value);
				}

				self::$opt[$k] = $value;
			}
		}

		// update css styles option
		if( $type == 'design' || $type == 'design_default' ){
			$this->update_democracy_css();
		}

		// update all options
		$up = update_option( self::OPT_NAME, self::$opt );

		if( $up ) $this->msg[] = __('Updated','democracy-poll');
		else      $this->msg['notice'][] = __('Nothing was updated','democracy-poll');

		return $up;
	}

	## Обновляет произвольный текст перевода
	function update_l10n(){
		$new_l10n = stripslashes_deep( $_POST['l10n'] );

		foreach( $new_l10n as $key => & $val ){
			$val = trim( $val );

			if( __( $key,'democracy-poll') == $val )
				unset( $new_l10n[$key] ); // delete if no difference from original translations_api
			else
				$val = wp_kses( $val, Democracy_Poll::$allowed_tags ); // sanitize value: Thanks to //pluginvulnerabilities.com/?p=2967
		}

		update_option('democracy_l10n', $new_l10n );
	}

	/**
	 * Получает существующие полные css файлы из каталога плагина
	 * @return Возвращает массив имен (путей) к файлам
	 */
	function _get_styles_files(){
		$arr = array();

		foreach( glob( DEMOC_PATH . 'styles/*.css' ) as $file ){
			if( preg_match('~\.min~', basename( $file ) ) ) continue;

			$arr[] = $file;
		}

		return $arr;
	}

	## deletes specified poll
	function delete_poll( $poll_id ){
		global $wpdb;

		if( ! $poll_id = intval($poll_id) ) return;

		$wpdb->delete( $wpdb->democracy_q,   array('id'  => $poll_id ) );
		$wpdb->delete( $wpdb->democracy_a,   array('qid' => $poll_id ) );
		$wpdb->delete( $wpdb->democracy_log, array('qid' => $poll_id ) );

		$this->msg[] = __('Poll Deleted','democracy-poll') .': '. $poll_id;
	}

	/**
	 * Закрывает/открывает голосование
	 * @param int $poll_id ID опроса
	 * @param bool $open Что сделать, открыть или закрыть голосование?
	 */
	function poll_opening( $poll_id, $open ){
		global $wpdb;

		if( ! $poll = DemPoll::get_poll($poll_id) ) return;

		$open = $open ? 1 : 0;

		$new_data = array( 'open' => $open );

		// удаляем дату окончания при открытии голосования
		if( $open )
			$new_data['end'] = 0;
		// ставим дату при закрытии опроса и деактивируем опрос
		else {
			$new_data['end'] = current_time('timestamp') - 10;
			$this->poll_activation( $poll_id, false );
		}

		if( $wpdb->update( $wpdb->democracy_q, $new_data, array( 'id'=>$poll->id ) ) )
			$this->msg[] = $open ? __('Poll Opened','democracy-poll') : __('Voting is closed','democracy-poll');
	}

	/**
	 * Активирует/деактивирует опрос
	 * @param int $poll_id ID опроса
	 * @param bool $activation Что сделать, активировать (true) или деактивировать?
	 */
	function poll_activation( $poll_id, $activation = true ){
		global $wpdb;

		if( ! $poll = DemPoll::get_poll($poll_id) ) return;

		$active = (int) $activation;

		if( ! $poll->open && $active ){
			$this->msg['error'][] = __('You can not activate closed poll...','democracy-poll');
			return false;
		}

		$done = $wpdb->update( $wpdb->democracy_q, array( 'active'=>$active ), array( 'id'=>$poll->id ) );

		if( $done )
			$this->msg[] = $active ? __('Poll Activated','democracy-poll') : __('Poll Deactivated','democracy-poll');
	}

	function insert_poll_handler(){
		$data = array();

		// collect all fields which start with 'dmc_'
		foreach( (array) $_POST as $key => $val )  if( 'dmc_' == substr( $key, 0, 4 ) )  $data[ substr( $key, 4 ) ] = $val;

		$data = wp_unslash( $data );

		$this->insert_poll( $data );
	}

	/**
	 * Add || update poll. Expect unslashed data.
	 * @param  array $data Data of added poll. If set 'qid' key poll wil be updated.
	 * @return boolean    true when added updated, false otherwise
	 */
	function insert_poll( $data ){
		global $wpdb;

		$orig_data = $data;

		$poll_id = intval( @ $data['qid'] );
		$update  = !! $poll_id;

		// sanitize
		$data = (object) $this->sanitize_poll_data( $data );

		if( ! $data->question ){
			$this->msg[] = 'error: question not set';
			return false;
		}

		// awnswers
		$old_answers = (array) @ $data->old_answers;
		$new_answers = (array) @ $data->new_answers;

		// add data if insert new poll
		if( ! $update ){
			if( ! $new_answers ){
				$this->msg[] = 'Error: Poll must have at least one answer';
				return false;
			}

			$data->added      = current_time('timestamp');
			$data->added_user = get_current_user_id();
			$data->open       = 1; // poll is open by default
		}

		// Удалим недопустимые для таблицы поля
		$q_fields = wp_list_pluck( $wpdb->get_results("SHOW COLUMNS FROM $wpdb->democracy_q"), 'Field' );
		$q_data   = array_intersect_key( (array) $data, array_flip($q_fields) );

		do_action_ref_array( 'dem_before_insert_quest_data', array(& $q_data, & $old_answers, & $new_answers, $update) );

		// UPDATE POLL
		if( $update ){
			$wpdb->update( $wpdb->democracy_q, $q_data, array('id'=>$poll_id) );

			// upadate answers
			if( $old_answers || $new_answers ){
				$ids = array();

				// Обновим старые ответы
				foreach( $old_answers as $aid => $anws ){
					$answ_row = $wpdb->get_row("SELECT * FROM $wpdb->democracy_a WHERE aid = ". intval($aid) );

					$added_by = $this->is_new_answer($answ_row) ? str_replace('-new', '', $answ_row->added_by) : $answ_row->added_by; // удалим метку NEW
					$order = $anws['aorder'];

					$wpdb->update(
						$wpdb->democracy_a,
						array('answer'=>$anws['answer'], 'votes'=>$anws['votes'], 'aorder'=>$order, 'added_by'=>$added_by ),
						array('qid'=>$poll_id, 'aid'=>$aid )
					);

					// собираем ID, которые остались. Для исключения из удаления
					$ids[] = $aid;
					$max_order_num = ! isset($max_order_num) ? $order : ( $max_order_num < $order ? $order : $max_order_num );
				}

				// Удаляем удаленные ответы, которые есть в БД но нет в запросе
				if( count($ids) > 0 ){
					$ids = array_map('absint', $ids );
					$del_ids = $wpdb->get_col("SELECT aid FROM $wpdb->democracy_a WHERE qid = $poll_id AND aid NOT IN (". implode(',', $ids ) .")");
					if( $del_ids ){
						// delete answers
						$deleted = $wpdb->query("DELETE FROM $wpdb->democracy_a WHERE aid IN (". implode(',', $del_ids ) .")");

						// delete answers logs
						if(1){
							// delete logs
							$user_voted_minus = $wpdb->query("DELETE FROM $wpdb->democracy_log WHERE qid = $poll_id AND aids IN (". implode(',', $del_ids ) .")");
							// обновим значение 'users_voted' в бд
							if( $user_voted_minus )
								$wpdb->query( self::users_voted_minus_sql($user_voted_minus, $poll_id) );

							// Обновим мульти логи, где по несколько ответов: '321,654'
							$up_logs = $wpdb->get_results("SELECT logid, aids FROM $wpdb->democracy_log WHERE qid = $poll_id AND aids RLIKE '(". implode('|', $del_ids ) .")'");
							foreach( $up_logs as $log ){
								$_ids_patt = implode('|', $del_ids ); // pattern part
								$new_aids = preg_replace("~^(?:$_ids_patt),|,(?:$_ids_patt)(?=,)|,(?:$_ids_patt)\$~", '', $log->aids );
								$wpdb->query( $wpdb->prepare("UPDATE $wpdb->democracy_log SET aids = %s WHERE logid = $log->logid", $new_aids) );
							}
						}

						if( $deleted )
							do_action('dem_answers_deleted', $del_ids, $poll_id );
					}
				}

				// Добавим новые ответы
				foreach( $new_answers as $anws ){
					$anws = trim( $anws );

					if( ! empty( $anws ) ){
						$order = $max_order_num ? $max_order_num++ : 0;
						$wpdb->insert( $wpdb->democracy_a, array( 'answer'=>$anws, 'aorder'=>$order, 'qid'=>$poll_id ) );
					}
				}

			}

			$this->msg[] = __('Poll Updated','democracy-poll');

			// collect answers users votes count
			// обновим 'users_voted' в questions после того как логи были обновлены, зависит от логов
			if(1){
				$users_voted = 0;
				// соберем из логов
				if( $data->multiple && ! $data->users_voted )
					$users_voted = $wpdb->get_var("SELECT count(*) FROM $wpdb->democracy_log WHERE qid = ". (int) $poll_id );
				// равно количеству голосов
				if( ! $data->multiple )
					$users_voted = $wpdb->get_var("SELECT SUM(votes) FROM $wpdb->democracy_a WHERE qid = ". (int) $poll_id );
					//$users_voted = array_sum( wp_list_pluck($old_answers, 'votes') );

				if( $users_voted )
					$wpdb->update( $wpdb->democracy_q, array('users_voted'=>$users_voted), array('id' => $poll_id ) );
			}
		}
		// ADD POLL
		else {
			$wpdb->insert( $wpdb->democracy_q, $q_data );

			if( ! $poll_id = $wpdb->insert_id ){
				$this->msg[] = 'error: sql error when adding poll data';
				return false;
			}

			foreach( $new_answers as $answer ){
				$answer = trim( $answer );

				if( ! empty( $answer ) )
					$wpdb->insert( $wpdb->democracy_a, array( 'answer'=>$answer, 'qid'=>$poll_id ) );
			}

			wp_redirect( add_query_arg( array('msg'=>'created'), $this->edit_poll_url($poll_id) ) );
		}

		do_action('dem_poll_inserted', $poll_id, $update );

		return true;
	}

	## sanitize all poll fields before save in db
	function sanitize_poll_data( $data ){
		$original_data = $data;

		foreach( $data as $key => & $val ){
			if( is_string($val) ) $val = trim($val);

			if(0){}
			// valid tags
			elseif( $key === 'question' || $key === 'note' ){
				$val = wp_kses( $val, self::$allowed_tags );
			}
			// date
			elseif( $key === 'end' || $key === 'added' ){
				if( preg_match('~[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}~', $val ) )
					$val = strtotime( $val );
				else
					$val = 0;
			}
			// fix multiple
			elseif( $key == 'multiple' && $val == 1 ){
				$val = 2;
			}
			// numbers
			elseif( in_array( $key, array('qid','democratic','active','multiple','forusers','revote') ) ){
				$val = (int) $val;
			}
			// answers
			elseif( $key == 'old_answers' || $key == 'new_answers' ){
				if( is_string($val) )
					$val = $this->sanitize_answer_data( $val );
				else
					foreach( $val as & $_val )
						$_val = $this->sanitize_answer_data( $_val );
			}
			// remove tags
			else
				$val = wp_kses( $val, 'strip' );
		}
		unset($val);

		return apply_filters('demadmin_sanitize_poll_data', $data, $original_data );
	}


	#### CSS ------------
	## Обновляет опцию "democracy_css"
	function update_democracy_css(){
		$additional = !empty($_POST['additional_css']) ? strip_tags( stripslashes($_POST['additional_css']) ) : '';

		$this->regenerate_democracy_css( $additional );
	}

	## Регенерирует стили в настройках, на оснвое настроек. не трогает дополнительные стили
	function regenerate_democracy_css( $additional = null ){

		// чтобы при обновлении плагина, доп. стили не слетали
		if( $additional === null ){
			$css = get_option('democracy_css');
			$additional = isset($css['additional_css']) ? $css['additional_css'] : '';
		}

		$base = $this->collect_base_css(); // если нет, то тема отключена

		$newdata = array(
			'base_css'       => $base,
			'additional_css' => $additional,
			'minify'         => $this->cssmin( $base . $additional ),
		);

		update_option('democracy_css', $newdata );
	}

	## Собирает базовые стили.
	## @return css код стилей или '', если шаблон отключен.
	function collect_base_css(){
		$tpl = self::$opt['css_file_name'];

		if( ! $tpl )
			return ''; // выходим если не указан шаблон

		$button = self::$opt['css_button'];
		$loader = self::$opt['loader_fill'];

		$radios = self::$opt['checkradio_fname'];

		$out = '';
		$stylepath = DEMOC_PATH . 'styles/';

		$out .= $this->parce_cssimport( $stylepath . $tpl );

		$out .= $radios ? "\n".file_get_contents( $stylepath .'checkbox-radio/'. $radios ) : '';

		$out .= $button ? "\n".file_get_contents( $stylepath .'buttons/'. $button ) : '';

		if( $loader ){
			$out .= "\n.dem-loader .fill{ fill: $loader !important; }\n";
			$out .= ".dem-loader .css-fill{ background-color: $loader !important; }\n";
			$out .= ".dem-loader .stroke{ stroke: $loader !important; }\n";
		}

		// progress line
		$d_bg       = self::$opt['line_bg'];
		$d_fill     = self::$opt['line_fill'];
		$d_height   = self::$opt['line_height'];
		$d_fillThis = self::$opt['line_fill_voted'];

		if( $d_bg )       $out .= "\n.dem-graph{ background: $d_bg !important; }\n";
		if( $d_fill )     $out .= "\n.dem-fill{ background-color: $d_fill !important; }\n";
		if( $d_fillThis ) $out .= ".dem-voted-this .dem-fill{ background-color:$d_fillThis !important; }\n";
		if( $d_height )   $out .= ".dem-graph{ height:{$d_height}px; line-height:{$d_height}px; }\n";

		if( $button ){
			// button
			$bbackground = self::$opt['btn_bg_color'];
			$bcolor      = self::$opt['btn_color'];
			$bbcolor     = self::$opt['btn_border_color'];
			// hover
			$bh_bg     = self::$opt['btn_hov_bg'];
			$bh_color  = self::$opt['btn_hov_color'];
			$bh_bcolor = self::$opt['btn_hov_border_color'];

			if( $bbackground ) $out .= "\n.dem-button{ background-color:$bbackground !important; }\n";
			if( $bcolor )      $out .= ".dem-button{ color:$bcolor !important; }\n";
			if( $bbcolor )     $out .= ".dem-button{ border-color:$bbcolor !important; }\n";

			if( $bh_bg )     $out .= "\n.dem-button:hover{ background-color:$bh_bg !important; }\n";
			if( $bh_color )  $out .= ".dem-button:hover{ color:$bh_color !important; }\n";
			if( $bh_bcolor ) $out .= ".dem-button:hover{ border-color:$bh_bcolor !important; }\n";
		}

		return $out;
	}

	/**
	 * Сжимает css YUICompressor
	 * $minicss = democr()->cssmin( file_get_contents( DEMOC_URL . 'styles/'. democr()->opt('css_file_name') ) );
	 * @param str $input_css КОД css
	 * @return str min css.
	 */
	function cssmin( $input_css ){
		require_once DEMOC_PATH . 'admin/class-CSSmin.php';

		$compressor = new CSSmin();

		// Override any PHP configuration options before calling run() (optional)
		// $compressor->set_memory_limit('256M');
		// $compressor->set_max_execution_time(120);

		return $compressor->run( $input_css );
	}

	## Импортирует @import в css
	function parce_cssimport( $css_filepath ){
		$filecode = file_get_contents( $css_filepath );

		$filecode = preg_replace_callback('~@import [\'"](.*?)[\'"];~', function( $m ) use ( $css_filepath ){
			return file_get_contents( dirname( $css_filepath ) . '/' . $m[1] );
		}, $filecode );

		return $filecode;
	}

	## Ссылка на настройки со страницы плагинов
	function setting_page_link( $actions, $plugin_file ){
		if( false === strpos( $plugin_file, basename( DEMOC_PATH ) ) ) return $actions;

		$settings_link = '<a href="'. $this->admin_page_url() .'">'. __('Settings','democracy-poll') .'</a>';
		array_unshift( $actions, $settings_link );

		return $actions;
	}

	## Создает страницу архива. Сохраняет УРЛ созданой страницы в опции плагина. Перед созданием проверят нет ли уже такой страницы.
	## @return  УРЛ созданной страницы или false
	function dem_create_archive_page(){
		global $wpdb;

		// Пробуем найти страницу с архивом
		if( $page = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_content LIKE '[democracy_archives]' AND post_status = 'publish' LIMIT 1") ){
			$page_id = $page->ID;
		}
		// Создаем новую страницу
		else {
			$page_id = wp_insert_post( array(
				'post_title'   => __('Polls Archive','democracy-poll'),
				'post_content' => '[democracy_archives]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_name'    => 'democracy-archives',
			) );

			if( ! $page_id ) return false;
		}

		// обновляем опцию плагина
		$newopt = democr()->opt();
		$newopt['archive_page_id'] = $page_id;
		update_option( Democracy_Poll::OPT_NAME, $newopt );

		wp_redirect( remove_query_arg('dem_create_archive_page') );
	}

	## Clear all log table
	protected function clear_logs(){
		global $wpdb;
		$wpdb->query("TRUNCATE TABLE $wpdb->democracy_log");
		wp_redirect( remove_query_arg('dem_clear_logs') );
		exit;
	}

	protected function clear_closed_polls_logs(){
		global $wpdb;
		$wpdb->query("DELETE FROM $wpdb->democracy_log WHERE qid IN (SELECT id FROM $wpdb->democracy_q WHERE open = 0)");
		wp_redirect( remove_query_arg('dem_del_closed_polls_logs') );
		exit;
	}

	protected function clear_new_mark(){
		global $wpdb;
		$wpdb->query("UPDATE $wpdb->democracy_a SET added_by = REPLACE( added_by, '-new', '')");
		wp_redirect( remove_query_arg('dem_del_new_mark') );
		exit;
	}

	/**
	 * Удаляет логи опроса
	 * @param integer $poll_id ID опроса
	function del_poll_logs( $poll_id ){
		global $wpdb;

		$done = $wpdb->query("DELETE FROM $wpdb->democracy_log WHERE qid = ". intval($poll_id) );
	}
	*/

	/**
	 * Удаляет только указанный лог
	 * @param array/int $logids Log IDs array or single log ID
	 */
	function del_only_logs( $logids ){
		$logids = array_filter( (array) $logids );
		if( ! $logids )
			return false;

		global $wpdb;

		$res = $wpdb->query("DELETE FROM $wpdb->democracy_log WHERE logid IN (". implode(',', array_map('intval', $logids)) .")");

		$this->msg[] = $res ? sprintf( __('Lines deleted:%s','democracy-poll'), $res ) : __('Failed to delete','democracy-poll');

		do_action('dem_delete_only_logs', $logids, $res );

		return $res;
	}

	/**
	 * Удаляет указанный лог и связанные голоса
	 * @param array/int $logids Log IDs array or single log ID
	 */
	function del_logs_and_votes( $logids ){
		$logids = array_filter( (array) $logids );
		if( ! $logids )
			return false;

		global $wpdb;

		// Соберем все ID вопросов, которые нужно минусануть
		$log_data = $wpdb->get_results("SELECT qid, aids FROM $wpdb->democracy_log WHERE logid IN (". implode(',', array_map('intval', $logids)) .")");
		$aids = wp_list_pluck($log_data, 'aids');
		$qids = wp_list_pluck($log_data, 'qid');

		// update answers table 'votes' field
		if(1){
			// collect count how much to minus from every answer
			$minus_data = array();
			foreach( $aids as $_aids )
				foreach( explode(',', $_aids) as $aid )
					$minus_data[$aid] = empty($minus_data[$aid]) ? 1 : ($minus_data[$aid]+1);

			// minus sql for answer 'votes' field
			$minus_answ_sum = 0;
			foreach( $minus_data as $aid => $minus_num ){
				// IF( (votes<=%d), 0, (votes-%d) ) - for case when minus number bigger than votes. Votes can't be negative
				$sql = $wpdb->prepare("UPDATE $wpdb->democracy_a SET votes = IF( (votes<=%d), 0, (votes-%d) ) WHERE aid = %d", $minus_num, $minus_num, $aid );
				if( $wpdb->query( $sql ) )
					$minus_answ_sum += $minus_num;
			}
		}

		// update question table 'users_voted' field
		if(1){
			// collect count how much to minus from every question 'users_voted' field
			$minus_data = array();
			foreach( $qids as $qid )
				$minus_data[$qid] = empty($minus_data[$qid]) ? 1 : ($minus_data[$qid]+1);

			// minus sql for question 'users_voted' field
			$minus_users_sum = 0;
			foreach( $minus_data as $qid => $minus_num ){
				if( $wpdb->query( self::users_voted_minus_sql($minus_num, $qid) ) )
					$minus_users_sum += $minus_num;
			}
		}

		// now, delete logs itself
		$res = $wpdb->query("DELETE FROM $wpdb->democracy_log WHERE logid IN (". implode(',', array_map('intval', $logids)) .")");

		$this->msg[] = $res
			? sprintf( __('Removed logs:%d. Taken away answers:%d. Taken away users %d.','democracy-poll'), $res, $minus_answ_sum, $minus_users_sum )
			: __('Failed to delete','democracy-poll');

		do_action('dem_delete_logs_and_votes', $logids, $res, $minus_answ_sum, $minus_users_sum );
	}

	static function users_voted_minus_sql( $minus_num, $qid ){
		global $wpdb;
		return $wpdb->prepare("UPDATE $wpdb->democracy_q SET users_voted = IF( (users_voted<=%d), 0, (users_voted-%d) ) WHERE id = %d", $minus_num, $minus_num, $qid );
	}

	/**
	 * Проверяет является ли переданный ответ новым ответом - NEW
	 * @param  object $answer Объект ответа
	 * @return boolean Новый или нет
	 */
	function is_new_answer( $answer ){
		return preg_match('~-new$~', $answer->added_by );
	}

}




