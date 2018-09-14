<?php

function democracy_activate(){
	$GLOBALS['democracy_activate_run'] = 1; // in order to activation works - activate_plugin() function works

	// PHP 5.3+ check
	if( is_admin() && version_compare(PHP_VERSION, '5.3', '<') ) {
		deactivate_plugins( plugin_basename( DEM_MAIN_FILE ) );

		wp_die('Democracy Poll needs PHP 5.3 or higher.');
	}

	// multisite
	if( is_multisite() && count( $sites = (function_exists('get_sites') ? get_sites() : wp_get_sites()) ) > 0 ){

		foreach( $sites as $site ){
			switch_to_blog( is_array($site) ? $site['blog_id'] : $site->blog_id ); // get_sites of WP 4.6+ return objects ...

			_democracy_activate();
		}

		restore_current_blog();
	}
	else
		_democracy_activate();
}

/**
 * Создает таблицы, настройки и апгрейдит если надо.
 */
function _democracy_activate(){
	global $wpdb;

	Democracy_Poll::load_textdomain();

	dem_set_dbtables(); // переопределим названия таблиц для мультисайта

	// create tables
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( dem_get_db_schema() );
	//wp_die();

	// Poll example
	if( ! $wpdb->get_row("SELECT * FROM $wpdb->democracy_q LIMIT 1") ){
		$wpdb->insert( $wpdb->democracy_q, array(
			'question'   => __('What does "money" mean to you?','democracy-poll'),
			'added'      => current_time('timestamp'),
			'added_user' => get_current_user_id(),
			'democratic' => 1,
			'active'     => 1,
			'open'       => 1,
			'revote'     => 1,
		) );

		$qid = $wpdb->insert_id;

		$answers = array(
			__(' It is a universal product for exchange.','democracy-poll'),
			__('Money - is paper... Money is not the key to happiness...','democracy-poll'),
			__('Source to achieve the goal. ','democracy-poll'),
			__('Pieces of Evil :)','democracy-poll'),
			__('The authority, the  "power", the happiness...','democracy-poll'),
		);

		// create votes
		$allvotes = 0;
		foreach( $answers as $answr ){
			$allvotes += $votes = rand(0, 100);
			$wpdb->insert( $wpdb->democracy_a, array('votes'=> $votes, 'qid'=> $qid, 'answer'=> $answr ) );
		}

		// 'users_voted' update
		$wpdb->update( $wpdb->democracy_q, array('users_voted'=>$allvotes ), array('id'=>$qid) );
	}

	// add options, if needed
	if( ! get_option(Democracy_Poll::OPT_NAME) )
		Democracy_Poll::init()->update_options('default');

	// upgrade
	dem_last_version_up();
}

/**
 * Схема таблиц плагина
 * @return string схема
 */
function dem_get_db_schema(){
	global $wpdb;

	$charset_collate = '';

	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";

	return "
		CREATE TABLE $wpdb->democracy_q (
			id            bigint(20) unsigned NOT NULL auto_increment,
			question      text                NOT NULL default '',
			added         int(10)    unsigned NOT NULL default 0,
			added_user    bigint(20) unsigned NOT NULL default 0,
			end           int(10)    unsigned NOT NULL default 0,
			users_voted   bigint(20) unsigned NOT NULL default 0,
			democratic    tinyint(1) unsigned NOT NULL default 0,
			active        tinyint(1) unsigned NOT NULL default 0,
			open          tinyint(1) unsigned NOT NULL default 0,
			multiple      tinyint(5) unsigned NOT NULL default 0,
			forusers      tinyint(1) unsigned NOT NULL default 0,
			revote        tinyint(1) unsigned NOT NULL default 0,
			show_results  tinyint(1) unsigned NOT NULL default 0,
			answers_order varchar(50)         NOT NULL default '',
			in_posts      text                NOT NULL default '',
			note          text                NOT NULL default '',
			PRIMARY KEY  (id),
			KEY active (active)
		) $charset_collate;

		CREATE TABLE $wpdb->democracy_a (
			aid      bigint(20) unsigned NOT NULL auto_increment,
			qid      bigint(20) unsigned NOT NULL default 0,
			answer   text                NOT NULL default '',
			votes    int(10)    unsigned NOT NULL default 0,
			aorder   int(5)     unsigned NOT NULL default 0,
			added_by varchar(100)        NOT NULL default '',
			PRIMARY KEY  (aid),
			KEY qid (qid)
		) $charset_collate;

		CREATE TABLE $wpdb->democracy_log (
			logid    bigint(20)   unsigned NOT NULL auto_increment,
			ip       varchar(100)          NOT NULL default '',
			qid      bigint(20)   unsigned NOT NULL default 0,
			aids     text                  NOT NULL default '',
			userid   bigint(20)   unsigned NOT NULL default 0,
			date     DATETIME              NOT NULL default '0000-00-00 00:00:00',
			expire   bigint(20)   unsigned NOT NULL default 0,
			ip_info  text                  NOT NULL default '',
			PRIMARY KEY  (logid),
			KEY ip (ip,qid),
			KEY qid (qid),
			KEY userid (userid)
		) $charset_collate;
	";
}

/**
 * Plugin Upgrade
 * Need initiated Democracy_Poll class.
 * Нужно вызывать на странице настроек плагина, чтобы не грузить лишний раз сервер.
 */
function dem_last_version_up(){
	$old_ver = get_option('democracy_version');

	if( $old_ver == DEM_VER || ! $old_ver ) return;

	// обновим css
	democr()->regenerate_democracy_css();

	update_option('democracy_version', DEM_VER );

	global $wpdb;

	// обнволение структуры таблиц
	//require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	//$doe = dbDelta( dem_get_db_schema() );
	//wp_die(print_r($doe));

	###
	### изменение данных таблиц
	$cols_q   = $wpdb->get_results("SHOW COLUMNS FROM $wpdb->democracy_q", OBJECT_K );
	$fields_q = array_keys( $cols_q );

	$cols_a   = $wpdb->get_results("SHOW COLUMNS FROM $wpdb->democracy_a", OBJECT_K );
	$fields_a = array_keys( $cols_a );

	$cols_log   = $wpdb->get_results("SHOW COLUMNS FROM $wpdb->democracy_log", OBJECT_K );
	$fields_log = array_keys( $cols_log );

	// 3.1.3
	if( ! in_array('end', $fields_q ) )
		$wpdb->query("ALTER TABLE $wpdb->democracy_q ADD `end` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `added`;");

	if( ! in_array('note', $fields_q ) )
		$wpdb->query("ALTER TABLE $wpdb->democracy_q ADD `note` text NOT NULL;");

	if( in_array('current', $fields_q ) ){
		$wpdb->query("ALTER TABLE $wpdb->democracy_q CHANGE `current` `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0;");
		$wpdb->query("ALTER TABLE $wpdb->democracy_q CHANGE `active` `open`    tinyint(1) UNSIGNED NOT NULL DEFAULT 0;");
	}

	// 4.1
	if( ! in_array('aids', $fields_log ) ){
		// если нет поля aids, создаем 2 поля и индексы
		$wpdb->query("ALTER TABLE $wpdb->democracy_log ADD `aids`   text NOT NULL;");
		$wpdb->query("ALTER TABLE $wpdb->democracy_log ADD `userid` bigint(20) UNSIGNED NOT NULL DEFAULT 0;");
		$wpdb->query("ALTER TABLE $wpdb->democracy_log ADD KEY userid (userid)");
		$wpdb->query("ALTER TABLE $wpdb->democracy_log ADD KEY qid (qid)");
	}

	// 4.2
	if( in_array('allowusers', $fields_q ) )
		$wpdb->query("ALTER TABLE $wpdb->democracy_q CHANGE `allowusers` `democratic` tinyint(1) UNSIGNED NOT NULL DEFAULT '0';");

	if( ! in_array('forusers', $fields_q ) ){
		$wpdb->query("ALTER TABLE $wpdb->democracy_q ADD `forusers` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `multiple`;");
		$wpdb->query("ALTER TABLE $wpdb->democracy_q ADD `revote`   tinyint(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `multiple`;");
	}

	// 4.5.6
	if( ! in_array('expire', $fields_log ) )
		$wpdb->query("ALTER TABLE $wpdb->democracy_log ADD `expire` bigint(20) UNSIGNED NOT NULL default 0 AFTER `userid`;");

	// 4.7.5
	// конвертируем в кодировку utf8mb4
	if( $wpdb->charset === 'utf8mb4' ){
		foreach( array( $wpdb->democracy_q, $wpdb->democracy_a, $wpdb->democracy_log ) as $table ){
			$alter = false;
			if( ! $results = $wpdb->get_results( "SHOW FULL COLUMNS FROM `$table`" ) )
				continue;

			foreach( $results as $column ){
				if ( ! $column->Collation ) continue;

				list( $charset ) = explode( '_', $column->Collation );

				if( strtolower( $charset ) != 'utf8mb4' ){
					$alter = true;
					break;
				}
			}

			if( $alter )
				$wpdb->query("ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		}

	}

	// 4.9
	if( ! in_array('date', $fields_log ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_log` ADD `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `userid`;");

	// 4.9.3
	if( version_compare( $old_ver, '4.9.3', '<') ){
		$wpdb->query("ALTER TABLE `$wpdb->democracy_log` CHANGE `date` `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';");

		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` CHANGE `multiple` `multiple` tinyint(5) UNSIGNED NOT NULL DEFAULT 0;");

		$wpdb->query("ALTER TABLE `$wpdb->democracy_a` CHANGE `added_by` `added_by` varchar(100) NOT NULL default '';");
		$wpdb->query("UPDATE `$wpdb->democracy_a` SET added_by = '' WHERE added_by = '0'");
	}
	if( ! in_array('added_user', $fields_q ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` ADD `added_user` bigint(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `added`;");
	if( ! in_array('show_results', $fields_q ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` ADD `show_results` tinyint(1) UNSIGNED NOT NULL default 1 AFTER `revote`;");

	// 5.0.4
	if( version_compare( $old_ver, '5.0.4', '<') ){
		//$wpdb->query("ALTER TABLE $wpdb->democracy_log CHANGE `ip` `ip` bigint(11) UNSIGNED NOT NULL DEFAULT '0';"); // ниже изменяется...
		$wpdb->query("ALTER TABLE $wpdb->democracy_log CHANGE `qid` `qid` bigint(20) UNSIGNED NOT NULL DEFAULT '0';");

		$wpdb->query("ALTER TABLE `$wpdb->democracy_a` CHANGE `aid` `aid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
		$wpdb->query("ALTER TABLE `$wpdb->democracy_a` CHANGE `qid` `qid` bigint(20) UNSIGNED NOT NULL DEFAULT '0';");

		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` CHANGE `id` `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
	}

	// 5.2.0
	if( ! in_array('logid', $fields_log ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_log` ADD `logid` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;");

	if( ! in_array('ip_info', $fields_log ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_log` ADD `ip_info` text NOT NULL default '' AFTER `expire`;");

	if( ! in_array('aorder', $fields_a ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_a` ADD `aorder` int(5) unsigned NOT NULL default 0 AFTER `votes`;");

	if( ! in_array('answers_order', $fields_q ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` ADD `answers_order` varchar(50) NOT NULL default '' AFTER `show_results`;");

	if( ! in_array('users_voted', $fields_q ) ){
		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` ADD `users_voted` bigint(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `end`;");
		// заполним данными из лога
		$wpdb->query("UPDATE $wpdb->democracy_q SET users_voted = (SELECT count(*) FROM $wpdb->democracy_log WHERE qid = id) WHERE multiple > 0");
		$wpdb->query("UPDATE $wpdb->democracy_q SET users_voted = (SELECT SUM(votes) FROM $wpdb->democracy_a WHERE qid = id) WHERE multiple = 0");
	}

	// 5.2.1
	if( ! in_array('in_posts', $fields_q ) )
		$wpdb->query("ALTER TABLE `$wpdb->democracy_q` ADD `in_posts` text NOT NULL default '' AFTER `answers_order`;");

	// 5.2.4
	if( $cols_log['ip']->Type != 'varchar(100)' ){
		$wpdb->query("ALTER TABLE $wpdb->democracy_log CHANGE `ip` `ip` varchar(100) NOT NULL default '';");
		$wpdb->query("UPDATE $wpdb->democracy_log SET ip = INET_NTOA(ip);");

	}

}



