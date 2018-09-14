<?php

## Root plugin class. Init plugin, split init to 'admin' and 'front'

class Democracy_Poll {

	public $ajax_url;

	public $admin_access; // only access to add/edit poll and so on...
	public $super_access; // full access to change settings and so on...

	public $msg = array();

	const OPT_NAME = 'democracy_options';

	// теги допустимые в вопросах и ответах. Добавляются к глобальной $allowedtags
	static $allowed_tags = array(
		'a'      => array( 'href'=>true, 'rel'=>true, 'name'=>true, 'target'=>true, ),
		'b'      => array(),
		'strong' => array(),
		'i'      => array(),
		'em'     => array(),
		'span'   => array( 'class'=>true ),
		'code'   => array(),
		'var'    => array(),
		'del'    => array( 'datetime'=>true, ),
		'img'    => array( 'src'=>true, 'alt'=>true, 'width'=>true, 'height'=>true, 'align'=>true ),
	);

	static $opt;
	static $i;

	static $pollid_meta_key = 'dem_poll_id'; // для Democ_Post_Metabox

	static function init(){
		if( ! is_null( self::$i ) ) return self::$i;

		# admin part
		if( ( is_admin() && ! (defined('DOING_AJAX') && DOING_AJAX) ) || isset($GLOBALS['democracy_activate_run']) )
			self::$i = new Democracy_Poll_Admin();
		# front-end
		else {
			self::$i = new self;
			self::$i->dem_front_init();
		}

		return self::$i;
	}

	function __construct(){
		if( ! is_null(self::$i) ) return self::$i;

		self::load_textdomain();

		## Для локализации внешней части и кастомной настройки перевода
		add_filter('gettext_with_context', array( __CLASS__, 'handle_front_l10n' ), 10, 4 );

		self::$allowed_tags = array_merge( $GLOBALS['allowedtags'], array_map('_wp_add_global_attributes', self::$allowed_tags) );

		$this->ajax_url = admin_url('admin-ajax.php');

		self::$opt = $this->get_options();

		// dem_init - main Democracy hooks init

		// set access
		$administrator = current_user_can('manage_options');
		$this->super_access = apply_filters('dem_super_access', $administrator ); // access to change settings...
		$this->admin_access = $administrator; // access to add/edit poll and so on...

		// open admin manage access for other roles
		if( ! $this->admin_access && ! empty(self::$opt['access_roles']) ){
			foreach( wp_get_current_user()->roles as $role ){
				if( in_array($role, self::$opt['access_roles'] ) ){
					$this->admin_access = true;
					break;
				}
			}
		}

		// меню в панели инструментов
		if( !empty(self::$opt['toolbar_menu']) && $this->admin_access ) add_action('admin_bar_menu', array( $this, 'toolbar'), 99);

		// hide duplicate content. For 5+ versions it's no need
		if( isset($_GET['dem_act']) || isset($_GET['dem_action']) || isset($_GET['dem_pid']) || isset($_GET['show_addanswerfield']) || isset($_GET['dem_add_user_answer']) ){
			add_action('wp', function(){ status_header( 404 ); });
			add_action('wp_head', function(){ echo "\n<!--by dem-->\n".'<meta name="robots" content="noindex,nofollow">'."\n"; });
		}

	}

	## подключаем файл перевода
	static function load_textdomain(){
		load_plugin_textdomain( 'democracy-poll', false, basename(DEMOC_PATH) . DEM_DOMAIN_PATH );
	}

	static function handle_front_l10n( $text_translated, $text = '', $context = '', $domain = '' ){
		static $l10n_opt;
		if( $l10n_opt === null || $text_translated === 'clear_cache' ) $l10n_opt = get_option('democracy_l10n');

		if( $domain === 'democracy-poll' && $context == 'front' && !empty($l10n_opt[$text]) ){
			return $l10n_opt[$text];
		}

		return $text_translated;
	}

	## adds admin bar menu item
	function toolbar( $toolbar ){
		$toolbar->add_node( array(
			'id'    => 'dem_settings',
			'title' => 'Democracy',
			'href'  => $this->admin_page_url(),
		) );

		$list = array();
		$list['']                 = __('Polls List','democracy-poll');
		$list['add_new']          = __('Add Poll','democracy-poll');
		$list['logs']             = __('Logs','democracy-poll');
		$list['general_settings'] = __('Settings','democracy-poll');
		$list['design']           = __('Theme Settings','democracy-poll');
		$list['l10n']             = __('Texts changes','democracy-poll');

		if( ! $this->super_access )
			unset( $list['general_settings'], $list['design'], $list['l10n'] );

		foreach( $list as $id => $title ){
			$toolbar->add_node( array(
				'parent' => 'dem_settings',
				'id'     => $id ?: 'dem_main',
				'title'  => $title,
				'href'   => add_query_arg( array('subpage'=>$id), $this->admin_page_url() ),
			) );
		}
	}

	## get single option from static $opt. It's wrapper function...
	function opt( $optname = '' ){
		if( ! $optname ) return self::$opt;

		if( isset(self::$opt[$optname]) ) return self::$opt[$optname];
	}

	## wp_kses value with democracy allowed tags. For esc outputing strings...
	function kses_html( $value ){
		return wp_kses( $value, self::$allowed_tags );
	}

	## adds options to `self::$opt`. Update options in DB if it's not set yet.
	function get_options(){
		if( empty(self::$opt) ) self::$opt = get_option( self::OPT_NAME );
		if( empty(self::$opt) && method_exists($this, 'update_options') ) $this->update_options('default');

		// append default values if need
		foreach( $this->default_options() as $part => $options ){
			foreach( $options as $key => $val )
				if( ! isset(self::$opt[$key]) ) self::$opt[$key] = $val;
		}

		return self::$opt;
	}

	/**
	 * Возвращает УРЛ на главную страницу настроек плагина. Кэширует.
	 * @return string URL
	 */
	function admin_page_url(){
		static $url; if( ! $url ) $url = admin_url('options-general.php?page='. basename( DEMOC_PATH ) );

		return $url;
	}

	/**
	 * Ссылка на редактирование опроса.
	 * @param  integer $poll_id ID опроса
	 * @return string URL
	 */
	function edit_poll_url( $poll_id ){
		return $this->admin_page_url() .'&edit_poll='. $poll_id;
	}

	/**
	 * Проверяет используется ли страничный плагин кэширования на сайте
	 * @return boolean
	 */
	function is_cachegear_on(){
		if( self::$opt['force_cachegear'] )
			return true;

		if( null !== ( $status = apply_filters( 'dem_cachegear_status', null ) ) )
			return $status;

		// wp total cache
		if( defined('W3TC') && \W3TC\Dispatcher::component('ModuleStatus') && \W3TC\Dispatcher::component('ModuleStatus')->is_enabled('pgcache') )
			return true;
		// wp super cache
		if( defined('WPCACHEHOME') && @ $GLOBALS['cache_enabled'] )
			return true;
		// WordFence
		if( defined('WORDFENCE_VERSION') && @ wfConfig::get('cacheType') == 'falcon' )
			return true;
		// WP Rocket
		if( class_exists('HyperCache')  )
			return true;
		// Quick Cache
		if( class_exists('quick_cache') && @ \quick_cache\plugin()->options['enable'] )
			return true;
		// wp-fastest-cache
		// aio-cache

		return false;
	}

	/**
	 * Очищает данные ответа
	 * @param  string/array $data Что очистить? Если передана строка, удалить из нее недопустимые HTML теги.
	 * @return string/array Чистые данные.
	 */
	function sanitize_answer_data( $data, $filter_type = '' ){
		$allowed_tags = $this->admin_access ? self::$allowed_tags : 'strip';

		if( is_string($data) ){
			$data = wp_kses( trim($data), $allowed_tags );
		}
		else {
			foreach( $data as $key => & $val ){
				if( is_string($val) ) $val = trim($val);

				if(0){}
				// допустимые теги
				elseif( $key == 'answer' )
					$val = wp_kses( $val, $allowed_tags );

				// числа
				elseif( in_array( $key, array('qid','aid','votes') ) )
					$val = (int) $val;

				// остальное
				else
					$val = wp_kses( $val, 'strip' );
			}
		}

		return apply_filters('dem_sanitize_answer_data', $data, $filter_type );
	}

	/**
	 * Получает опции по умолчанию
	 * @return Массив
	 */
	function default_options(){
		return array(
			'main' => array(
				'inline_js_css'     => 1, // встараивать стили и скрипты в HTML
				'keep_logs'         => 1, // вести лог в БД
				'before_title'      => '<strong class="dem-poll-title">',
				'after_title'       => '</strong>',
				'force_cachegear'   => 0,
				'archive_page_id'   => 0,
				'order_answers'     => 'by_winner',
				'use_widget'        => 1,
				'hide_vote_button'  => 0, // прятать кнопку голосования где это можно, тогда голосование будет происходить по клику на ответ
				'toolbar_menu'      => 1,
				'tinymce_button'    => 1,
				'show_copyright'    => 1,
				'only_for_users'    => 0,
				'dont_show_results' => 0,      // глобальная опция - не показывать результаты опроса. До закрития голосования
				'dont_show_results_link' => 0, // глобальная опция - не показывать только ссылку на результаты. Результаты будут видын после голосования
				'democracy_off'     => 0,      // глобальная опция democracy
				'revote_off'        => 0,      // глобальная опция переголосование
				'cookie_days'       => 365,
				'access_roles'      => array(),
				'soft_ip_detect'    => 0, // определять IP не только через REMOTE_ADDR
				'post_metabox_off'  => 0, // выключить ли метабокс для записей?
				'disable_js'        => 0, // Дебаг: отключает JS
			),
			'design' => array(
				'loader_fname'     => 'css-roller.css3',
				'css_file_name'    => 'alternate.css', // название файла стилей который будет использоваться для опроса.
				'css_button'       => 'flat.css',
				'loader_fill'      => '',  // как заполнять шкалу прогресса
				'graph_from_total' => 1,
				'answs_max_height' => 500, // px
				'anim_speed'       => 400, // msec
				// radio checkbox
				'checkradio_fname' => '',
				// progress
				'line_bg'         => '',
				'line_fill'       => '',
				'line_height'     => '',
				'line_fill_voted' => '',
				'line_anim_speed' => 1500,
				// button
				'btn_bg_color'         => '',
				'btn_color'            => '',
				'btn_border_color'     => '',
				'btn_hov_bg'           => '',
				'btn_hov_color'        => '',
				'btn_hov_border_color' => '',
				'btn_class'            => '',
			)
		);
	}

	/**
	 * Check if current or specified user can edit specified poll
	 * @param object $poll Poll object
	 * @param object $user User object. default - current user
	 */
	function cuser_can_edit_poll( $poll ){
		if( $this->super_access ) return true;

		if( ! $this->admin_access ) return false;

		if( is_numeric($poll) ) $poll = DemPoll::get_poll( $poll ); // get poll object

		if( $poll && $poll->added_user == wp_get_current_user()->ID ) return true;

		return false;
	}

	## FRONT --------------------------------------
	function dem_front_init(){
		# шоткод [democracy]
		add_shortcode( 'democracy',          array( $this, 'poll_shortcode') );
		add_shortcode( 'democracy_archives', array( $this, 'archives_shortcode') );

		//if( ! self::$opt['inline_js_css'] ) $this->add_css_once(); // подключаем стили как файл, если не инлайн

		# для работы функции без AJAX
		if( !isset($_POST['action']) || $_POST['action'] !== 'dem_ajax' ) $this->not_ajax_request_handler();

		# ajax request во frontend_init нельзя, потому что срабатывает только как is_admin()
		add_action('wp_ajax_dem_ajax',        array( $this, 'ajax_request_handler') );
		add_action('wp_ajax_nopriv_dem_ajax', array( $this, 'ajax_request_handler') );
	}

	## обрабатывает запрос AJAX
	function ajax_request_handler(){
		$vars = (object) $this->_sanitize_request_vars();

		if( ! $vars->act ) wp_die('error: no parameters have been sent or it is unavailable');
		if( ! $vars->pid ) wp_die('error: id unknown');

		// Вывод
		$poll = new DemPoll( $vars->pid );

		// switch
		// голосуем и выводим результаты
		if( $vars->act === 'vote' && $vars->aids ){
			// если пользователь голосует с другого браузера и он уже голосовал, ставим куки
			//if( $poll->cachegear_on && $poll->votedFor ) $poll->set_cookie();

			$voted = $poll->vote( $vars->aids );

			if( is_wp_error($voted) ){
				echo $poll::_voted_notice( $voted->get_error_message() );
				echo $poll->get_vote_screen();
			}
			else {
				if( $poll->not_show_results )
					echo $poll->get_vote_screen();
				else
					echo $poll->get_result_screen();
			}

		}
		// удаляем результаты
		elseif( $vars->act === 'delVoted' ){
			$poll->delete_vote();
			echo $poll->get_vote_screen();
		}
		// смотрим результаты
		elseif( $vars->act === 'view' ){
			if( $poll->not_show_results )
				echo $poll->get_vote_screen();
			else
				echo $poll->get_result_screen();
		}
		// вернуться к голосованию
		elseif( $vars->act === 'vote_screen' ){
			echo $poll->get_vote_screen();
		}
		elseif( $vars->act === 'getVotedIds' ){
			if( $poll->votedFor ){
				$poll->set_cookie(); // установим куки, т.к. этот запрос делается только если куки не установлены
				echo $poll->votedFor;
			}
			elseif( $poll->blockForVisitor ){
				echo 'blockForVisitor'; // чтобы вывести заметку
			}
			else {
				// если не голосовал ставим куки на пол дня, чтобы не делать эту првоерку каждый раз
				$poll->set_cookie('notVote', (time() + (DAY_IN_SECONDS/2)) );
			}
		}

		wp_die();
	}

	## для работы функции без AJAX
	function not_ajax_request_handler(){
		$vars = (object) $this->_sanitize_request_vars();

		if( ! $vars->act || ! $vars->pid || ! isset($_SERVER['HTTP_REFERER']) ) return;

		$poll = new DemPoll( $vars->pid );

		if( $vars->act == 'vote' && $vars->aids ){
			$poll->vote( $vars->aids );
			wp_redirect( remove_query_arg( array('dem_act','dem_pid'), $_SERVER['HTTP_REFERER'] ) );
			exit;
		}
		elseif( $vars->act == 'delVoted' ){
			$poll->delete_vote();
			wp_redirect( remove_query_arg( array('dem_act','dem_pid'), $_SERVER['HTTP_REFERER'] ) );
			exit;
		}
	}

	## Делает предваритеьную проверку передавемых переменных запроса
	function _sanitize_request_vars(){
		return array(
			'act'  => isset($_POST['dem_act']) ? $_POST['dem_act'] : false,
			'pid'  => isset($_POST['dem_pid'])  ? absint( $_POST['dem_pid'] ) : false,
			'aids' => isset($_POST['answer_ids']) ? wp_unslash( $_POST['answer_ids'] ) : false,
		);
	}

	## шоткод архива опросов
	function archives_shortcode( $args ){
		$args = shortcode_atts( array(
			'before_title' => '',
			'after_title'  => '',
			'active'       => null,    // 1 (active), 0 (not active) or null (param not set).
			'open'         => null,    // 1 (opened), 0 (closed) or null (param not set) polls.
			'screen'       => 'voted',
			'per_page'     => 10,
			'add_from_posts' => true,    // add From posts: html block
		), $args );

		return '<div class="dem-archives-shortcode">'. get_democracy_archives( $args ) .'</div>';
	}

	## шоткод опроса
	function poll_shortcode( $atts ){
		$atts = shortcode_atts( array(
			'id'  => '', // number or 'current', 'last'
		), $atts, 'democracy' );

		// для опредления к какой записи относиться опрос. проверка, если шорткод вызван не из контента...
		$post_id = ( is_singular() && is_main_query() ) ? $GLOBALS['post'] : 0;

		if( $atts['id'] === 'current' )
			$atts['id'] = (int) get_post_meta( $post_id, Democracy_Poll::$pollid_meta_key, 1 );

		return '<div class="dem-poll-shortcode">'. get_democracy_poll( $atts['id'], '', '', $post_id ) .'</div>';
	}

	## добавляет стили в WP head
	function add_css_once(){
		static $once; if( $once ) return; $once=1; // выполняем один раз!

		$demcss = get_option('democracy_css');
		$minify = @ $demcss['minify'];

		if( ! $minify ) return;

		// пробуем подключить сжатые версии файлов
//		$css_name = rtrim( $css_name, '.css');
//		$css      = 'styles/' . $css_name;
//		$cssurl   = DEMOC_URL  . "$css.min.css";
//		$csspath  = DEMOC_PATH . "$css.min.css";
//
//		if( ! file_exists( $csspath ) ){
//			$cssurl   = DEMOC_URL  . "$css.css";
//			$csspath  = DEMOC_PATH . "$css.css";
//		}

		// inline HTML
//		if( self::$opt['inline_js_css'] )
			return "\n<!--democracy-->\n" .'<style type="text/css">'. $minify .'</style>'."\n";

//		else{
//			add_action('wp_enqueue_scripts', function() use ($cssurl){ wp_enqueue_style('democracy', $cssurl, array(), DEM_VER ); } );
//		}
	}

	## добавляет скрипты в подвал
	function add_js_once(){
		static $once; if( $once ) return; $once=1; // выполняем один раз!

		// inline HTML
		if( self::$opt['inline_js_css'] ){
			wp_enqueue_script('jquery');
			add_action( ( is_admin() ? 'admin_footer' : 'wp_footer' ), array( __CLASS__, '_add_js_wp_footer'), 0 );
			// подключаем через фильтр, потому что иногда вылазиет баг, когда опрос добавляется прямо в контент...
			//return "\n" .'<script type="text/javascript">'. file_get_contents( DEMOC_PATH .'js/democracy.min.js' ) .'</script>'."\n";
		}
		else
			wp_enqueue_script('democracy', DEMOC_URL  .'js/democracy.min.js', array(), DEM_VER, true );
	}

	static function _add_js_wp_footer(){
		echo "\n<!--democracy-->\n" .'<script type="text/javascript">'. file_get_contents( DEMOC_PATH .'js/democracy.min.js' ) .'</script>'."\n";
	}

	## Сортировка массива объектов. Передаете в $array массив объектов, указываете в $args параметры сортировки и получаете отсортированный массив объектов.
	static function objects_array_sort( $array, $args = array('votes'=>'desc') ){
		usort( $array, function( $a, $b ) use ( $args ){
			$res = 0;

			if( is_array($a) ){
				$a = (object) $a;
				$b = (object) $b;
			}

			foreach( $args as $k => $v ){
				if( $a->$k == $b->$k ) continue;

				$res = ( $a->$k < $b->$k ) ? -1 : 1;
				if( $v=='desc' ) $res= -$res;
				break;
			}

			return $res;
		} );

		return $array;
	}

	/**
	 * Добавляет сообщение в массив
	 * @param string $msg                Сообщение
	 * @param string [$type = 'updated'] Тип: updated, notice, error
	 */
	static function add_msg( $msg, $type = 'updated' ){
		democr()->msg[ $type ][] = $msg;
	}

	/**
	 * Получает HTML код всех сообщений находящихся в массиве Democracy_Poll::msg
	 */
	function msgs_html(){
		if( ! $this->msg ) return '';
		$out = '';

		if( isset($this->msg['error']) )
			foreach( $this->msg['error'] as $msg )
				$out .= Democracy_Poll::msg_html( $msg, 'error' );

		if( isset($this->msg['notice']) )
			foreach( $this->msg['notice'] as $msg )
				$out .= Democracy_Poll::msg_html( $msg, 'notice' );

		if( isset($this->msg['updated']) )
			foreach( $this->msg['updated'] as $msg )
				$out .= Democracy_Poll::msg_html( $msg, 'updated' );


		foreach( $this->msg as $k => $msg ){
			if( in_array( $k, array('error', 'notice', 'updated'), true ) ) // $k = 0 не работает. поэтому $strict = true
				continue; // === because (0 == 'foo') = true

			$out .= Democracy_Poll::msg_html( $msg, 'updated' );
		}

		return $out;
	}

	static function msg_html( $msg, $type = 'updated' ){
		return '<div class="'. $type .' notice is-dismissible"><p>'. $msg .'</p></div>';
	}

	static function admin_notices( $msg, $type = '' ){
		add_action('admin_notices', function() use ($msg, $type){ echo Democracy_Poll::msg_html( $msg, $type ); } );
	}

	/**
	 * Получает данные локации переданного IP.
	 * @param  string [$ip = NULL]            IP для проверки. По умолчанию текущий IP.
	 * @param  string [$purpose = "location"] Какие данные нужно получить. Может быть: location address city state region country countrycode
	 * @return array/string Данные в виде массива или строки. Массив при $purpose = "location" в остальных случаях вернется строка.
	 */
	static function get_ip_info( $ip = NULL, $purpose = "location" ){
		$output = NULL;

		if( filter_var($ip, FILTER_VALIDATE_IP) === FALSE )
			$ip = DemPoll::get_ip();

		$purpose    = str_replace( array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)) );
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			'AF' => 'Africa',
			'AN' => 'Antarctica',
			'AS' => 'Asia',
			'EU' => 'Europe',
			'OC' => 'Australia (Oceania)',
			'NA' => 'North America',
			'SA' => 'South Americ',
		);

		if( filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support) ){
			//$ipdat = json_decode( wp_remote_retrieve_body( wp_remote_get("http://www.geoplugin.net/json.gp?ip=$ip") ) ); // wp_remote_get отдает forbiden 403 !!!
			$ipdat = json_decode( @ file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip") );

			if( $ipdat && @ strlen(trim($ipdat->geoplugin_countryCode)) == 2 ){
				switch( $purpose ){
					case "location":
						$output = array(
							'city'           => @ $ipdat->geoplugin_city,
							'state'          => @ $ipdat->geoplugin_regionName,
							'country'        => @ $ipdat->geoplugin_countryName,
							'country_code'   => @ $ipdat->geoplugin_countryCode,
							'continent'      => @ $continents[strtoupper($ipdat->geoplugin_continentCode)],
							'continent_code' => @ $ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if(@ strlen($ipdat->geoplugin_regionName) >= 1) $address[] = $ipdat->geoplugin_regionName;
						if(@ strlen($ipdat->geoplugin_city) >= 1)       $address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @ $ipdat->geoplugin_city;
						break;
					case "state":
						$output = @ $ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @ $ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @ $ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @ $ipdat->geoplugin_countryCode;
						break;
				}
			}
		}

		return $output;
	}

	/**
	 * Получает строку: Формат ip_info для таблицы логов
	 * @param  array/string    $ip_info IP или уже полученные данные IP в массиве
	 * @return string Формат: 'название_страны,код_страны,город' или 'текущее время UNIX'
	 * Зависит от метода Democracy_Poll::get_ip_info()
	 */
	static function ip_info_format( $ip_info ){
		// если передан IP
		if( filter_var($ip_info, FILTER_VALIDATE_IP) ){
			if( $ip_info === '127.0.0.1' )
				$format = time() + YEAR_IN_SECONDS * 10;
			else
				$ip_info = self::get_ip_info( $ip_info );
		}

		if( empty($format) ){
			/*Array(
				[city] =>
				[state] =>
				[country] => Uzbekistan
				[country_code] => UZ
				[continent] => Asia
				[continent_code] => AS
			)*/
			if( @ $ip_info['country'] && @  $ip_info['country_code'] )
				$format = $ip_info['country'] .','. $ip_info['country_code'] .','. $ip_info['city'];
			else
				$format = time();
		}

		return $format;
	}


	/**
	 * Получает объекты записей к которым прикреплен опрос (где испльзуется шорткод).
	 * @param  object  $poll Объект текущего опроса.
	 * @return array/false Массив объектов записей
	 */
	function get_in_posts_posts( $poll ){
		if( empty($poll->in_posts) || empty($poll->id) )
			return false;

		global $wpdb;

		$pids = explode(',', $poll->in_posts );

		$posts = array();
		$delete_pids = array(); // удалим ID записей которых теперь уже нет...

		foreach( $pids as $post_id ){
			if( $post = get_post($post_id) )
				$posts[] = $post;
			else
				$delete_pids[] = $post_id;
		}

		if( $delete_pids ){
			$new_in_posts = array_diff( $pids, $delete_pids );
			$wpdb->update( $wpdb->democracy_q, array('in_posts'=> implode(',', $new_in_posts) ), array('id'=>$poll->id) );
		}

		return $posts;
	}

}

