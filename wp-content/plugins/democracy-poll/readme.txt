=== Plugin Name ===
Stable tag: 5.5.6
Tested up to: 4.9.6
Requires at least: 3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Contributors: Tkama
Tags: democracy, poll, polls, create poll, do a poll, awesome poll, easy polls, user polls, online poll, opinion stage, opinionstage, poll plugin, poll widget, polling, polling System, post poll, opinion, questionnaire, vote, voting, voting polls, survey, research, usability, cache, wp poll, yop poll, quiz, rating, review


WordPress Polls plugin. Visitors can choose multiple and adds their own answers. Works with cache plugins like WP Super Cache. Has widget and shortcodes for posts.


== Description ==

The plugin adds a clever and convenient system to create various Polls with different features, such as:

* Single and Multiple voting. Сustomizable.
* Visitors can add new answers. Сustomizable.
* Ability to set poll's end date.
* Unregistered users can't vote. Сustomizable.
* Different design of a poll.
* And so on. See changelog.

Democracy Poll works with all cache plugins like: WP Total Cache, WP Super Cache, WordFence, Quick Cache etc.

I focus on easy-admin features and fast performance. So we have:

* Quick Edit button for Admin, right above a poll
* Plugin menu in toolbar
* Inline css & js incuding
* Css & js connection only where it's needed
* and so on. See changelog



### More Info ###

Democracy Poll is a reborn of once-has-been-famous plugin with the same name. Even if it hasn't been updated since far-far away 2006, it still has the great idea of adding users' own answers. So here's a completely new code. I have left only the idea and great name of the original DP by Andrew Sutherland.

What can it perform?

* adding new polls;
* works with cache plugins: wp total cache, wp super cache, etc...
* users may add their own answers (Democracy), the option may be disabled if necessary;
* multi-voting: users may multiple answers instead of a single one (may be disabled on demand);
* closing the poll after the date specified beforehand;
* showing a random poll when some are available;
* closing polls for still unregistered users;
* a comfortable editing of a selected poll: 'Edit' key for administrators;
* votes amount editing;
* a user can change his opinion when re-vote option is enabled;
* remember users by their IP, cookies, WP profiles (for authorized users). The vote history may be cleaned up;
* inserting new polls to any posts: the [demоcracy] (shortcode). A key in visual editor is available for this function;
* a widget (may be disabled);
* convenient polls editing: the plugin's Panel is carried out to the WordPress toolbar; (may be disabled);
* .css or .js files may be disabled or embedded to HTML code;
* showing a note under the poll: a short text with any notes to the poll or anything around it;
* changing the poll design (css themes);


Multisite: support from version 5.2.4


Requires PHP 5.3 or later.




== Usage ==

### Usage (Widget) ###
1. Go to `WP-Admin -> Appearance -> Widgets` and find `Democracy Poll` Widget.
2. Add this widget to one of existing sidebar.
3. Set Up added widget and press Save.
4. Done!


### Usage (Without Widget) ###
1. Open sidebar.php file of your theme: `wp-content/themes/<YOUR THEME NAME>/sidebar.php`
2. Add such code in the place you want Poll is appeared:

`
<?php if( function_exists('democracy_poll') ){ ?>
	<li>
		<h2>Polls</h2>
		<ul>
			<li><?php democracy_poll();?></li>
		</ul>
	</li>
<?php } ?>
`

* To show specific poll, use `<?php democracy_poll( 3 ); ?>` where 3 is your poll id.
* To embed a specific poll in your post, use `[democracy id="2"]` where 2 is your poll id.
* To embed a random poll in your post, use `[democracy]`


#### Display Archive ####
For display polls archive, use the function:

`<?php democracy_archives( $hide_active, $before_title, $after_title ); ?>`






== Frequently Asked Questions ==

### Does this plugin clear yourself after uninstall?

Yes it is! To completely uninstall the plugin, deactivate it and then press "delete" link in admin plugins page and ther plugin delete all it's options and so on...




== Screenshots ==

1. Single vote view.
2. Single result view.
3. Multiple vote view.
4. Admin polls list page.
5. Admin edit poll page.
6. Add poll admin page.
7. General settings.
8. Polls theme settings.
9. Poll's texts changes.




== TODO ==

* возможность подключать стили как файл!
* https://wordpress.org/support/topic/log-data-ip-restriction/#post-9083794
* ADD: Для каждого опроса своя высота разворачивания. Хотел сегодня прикрутить голосование помимо сайдбара ещё и в саму статью (там высота нужна была больше), не получилось. Она к сожалению фиксирована для всех опросов.
* ADD: option to set sort order for answers on results screen
* ADD: The ability to have a list of all active polls on one front end page would be nice.
* ADD: quick edit - https://wordpress.org/support/topic/suggestion-quick-edit/
* ADD: paging on archive page
* ADD: sorting on archive page
* ADD: cron: shadule polls opening & activation
* ADD: show link to post at the bottom of poll, if it attached to one post (has one in_posts ID)
* ADD: Collect cookies demPoll_N in one option array
* ADD: administrator can modify votes... put an option on poll creation to allow/disallow admin control over votes?
* ADD: Group polls
* ADD: Речь идёт о премодерации, чтобы пользователь предложил свой вариант, а публичным данный вариант станет после одобрения администратором.
* ADD: Фичареквест: добавить возможность "прикреплять" опрос к конкретному посту/странице вставкой шорткода не в тексте, а сделать метабокс (причем с нормальным выбором опроса из списка). Это позволит добавлять опрос в любое место на странице (согласно дизайну) и только для тех постов/страниц, где подключен опрос.





== Changelog ==

= 5.5.6 =
NEW: pagination links at the bottom of the archive page.
NEW: `[democracy_archives]` now can accept parameters: 'before_title', 'after_title', 'active', 'open', 'screen', 'per_page', 'add_from_posts'. `[democracy_archives screen="vote" active="1"]` will show only active poll with default vote screen.
NEW: function `get_dem_polls( $args )`

= 5.5.5 =
* CHANGE: ACE code editor to native WordPress CodeMirror.

= 5.5.4 =
* ADD: 'dem_get_ip' filter and cloudflare IP support.
* NEW: use float number in 'cookie_days' option.
* FIX: expire time now sets in UTC time zone.

= 5.5.3 =
* FIX: compatability with W3TC.
* FIX: multiple voting limit check on back-end (AJAX request) - no more answers than allowed...
* IMP: return WP_Error object on vote error and display it...

= 5.5.2 =
* ADD: wrapper function for use in themes 'get_democracy_poll_results( $poll_id )' - Gets poll results screen.
* ADD: allowed &lt;img&gt; tag in question and answers.

= 5.5.1 =
* IMP: now design setting admin page is more clear and beautiful :)

= 5.5.0 =
* ADD: post metabox to attach poll to a post. To show attached poll in theme use `get_post_poll_id()` on is_singular() page. Thanks to heggi@fhead.org for idea.
* ADD: voted screen progress line animation effect and option to set animation speed or disable animation...
* IMP: now "height collapsing" not work if it intend to hide less then 100px...
* FIX: now JS includes in_footer not right after poll. In some cases there was a bug - when poll added in content through shortcode.
* IMP: buttons and other design on 'design settings' admin screen.

= 5.4.9 =
* ADD: 'demadmin_sanitize_poll_data' filter second '$original_data' parameter
* ADD: posts where a poll is ebedded block at the bottom of each poll on polls archive page.

= 5.4.7 - 5.4.8 =
* FIX: 'expire' parameter works incorrectly with logs written to DB.
* FIX: 'wp_remote_get()' changed to 'file_get_contents()' bacause it works not correctly with geoplugin.net API.
* FIX: 'jquery-ui.css' fix and needed images added.

= 5.4.6 =
* FIX: Error with "load_textdomain" because of which it was impossible to activate the plugin

= 5.4.5 =
* FIX: "Edit poll" link from front-end for users with access to edit single poll.
* FIX: not correct use of $this for PHP 5.3 in class.Democracy_Poll_Admin.php

= 5.4.4 =
* CHG: prepare to move all localisation to translate.wordpress.org in next release...
* FIX: notice on MU activation - change `wp_get_sites()` to new from WP 4.6 `get_sites()`. Same fix on plugin Uninstall...
* ADD: Hungarian translation (hu_HU). Thanks to Lesbat.

= 5.4.3 =
* ADD: disable user capability to edit poll of another user, when there is democracy admin access to other roles...
* ADD: spain (es_ES) localisation file added.
* IMP: improve accessibility protection in different parts of admin area for additional roles (edit,delete poll)...
* IMP: hide & block any global plugin options updates for roles with not 'super_access' access level...

= 5.4.2 =
* FIX: Some minor changes that do not change the plugin logic at all: change function names; block direct access to files with "active" PHP code.
* CHG: Add `jquery-ui.css` to plugin files and now it loaded from inside it.
* FIX: "wp total cache" support
* ADD: second parametr to 'dem_sanitize_answer_data' filter - $filter_type
* ADD: second parametr to 'dem_set_answers' filter - $poll
* FIX: tinymce translation fix
* CHG: rename main class `Dem` to `Democracy_Poll` for future no conflict. And rename some other internal functions/method names

= 5.4.1 =
* CHG: improve logic to work correctly with activate_plugin() function outside of wp-admin area (in front end). Thanks to J.D.Grimes

= 5.4 =
* FIX: XSS Vulnerability. In some extraordinary case it could be possible to hack your site. Read here: http://pluginvulnerabilities.com/?p=2967
* ADD: For additional protect I add nonce check for all requests in admin area.
* CHG: move back Democracy_Poll_Admin::update_options() to its place - it's not good decision - I'm looking for a better one

= 5.3.6 =
* FIX: delete `esc_sql()` from code, for protection. Thanks to J.D. Grimes
* FIX: multi run of Democracy_Poll_Admin trigger error... (J.D. Grimes)
* CHG: move Democracy_Poll_Admin::update_options() method to Democracy_Poll::update_options(), for possibility to activate plugin not only from admin area.

= 5.3.5 =
* FIX: now user IP detects only with REMOTE_ADDR server variable to don't give possibility to cheat voice. You can change behavior in settings.

= 5.3.4.6 =
* FIX: add 'dem_add_user_answer' query var param to set noindex for no duplicate content
* ADD: actions `dem_voted` and `dem_vote_deleted`

= 5.3.4.5 =
* ADD: filters `dem_vote_screen` and `dem_result_screen`

= 5.3.4 =
* ADD: poll creation date change capability on edit poll page.
* ADD: animation speed option on design settings.
* ADD: "dont show results link" global option.
* ADD: 'show last poll' option in widget
* FIX: bug user cant add onw answer when vote button is hidden for not multiple poll
* CHG: move the "dem__collapser" styles to all styles. Change the styles: now arrow has 150% font-size. Now you can set your own arrow simbols by changing it's style. EX:
	```
	.dem__collapser.collapsed .arr:before{ content:"down"; }
	.dem__collapser.expanded  .arr:before{ content:"up"; }
	```

= 5.3.3.2 =
* FIX: stability for adding "dem__collapser" style into document.

= 5.3.3.1 =
* ADD: answers sort in admin by two fields - votes and then by ID - it's for no suffle new answers...

= 5.3.3 =
* FIX: minor: when work with cache plugin: now vote & revote buttons completely removes from DOM

= 5.3.2 =
* FIX: minor: cookie stability fix when plugin works with page caching plugin

= 5.3.1 =
* ADD: filter: 'dem_poll_screen_choose'
* FIX: now before do anything, js checks - is there any democracy element on page. It needs to prevent js errors.
* CHG: now main js init action run on document.ready, but not on load. So democracy action begin to work earlier...

= 5.3.0 =
* CHG: All plugin code translated to english! Now there is NO russian text for unknown localisation strings.

= 5.2.9 =
* FIX: add poll PHP syntax bug...

= 5.2.8 =
* ADD: new red button - pinterest style. default button styles changed. Some ugly buttons (3d, glass) was deleted.
* ADD: filters: 'dem_vote_screen_answer', 'dem_result_screen_answer', 'demadmin_after_question', 'demadmin_after_answer', 'dem_sanitize_answer_data', 'demadmin_sanitize_poll_data'

= 5.2.7 =
* FIX: global option 'dont show results' not work properly
* FIX: some little fix in code

= 5.2.6 =
* FIX: bug when new answer added: now "NEW" mark adds correctly

= 5.2.5 =
* FIX: wp_json_encode() function was replaced, in order to support WP lower then 4.1
* CHG: usability improvements
* CHG: set 'max+1' order num for users added answers, if answers has order

= 5.2.4 =
* ADD: multisite support
* ADD: migration from 'WP Polls' plugin mechanism
* FIX: bug - was allowed set 1 answer for multiple answers
* CHG: IP save to DB: now it saves as it is without ip2long()
* CHG: EN translation is updated.

= 5.2.3 =
* ADD: on admin edit poll screen, posts list where poll shortcode uses
* ADD: ability to set poll buttons css class on design settings page
* ADD: filters: 'dem_super_access' (removed filter 'dem_admin_access'), 'dem_get_poll', 'dem_set_answers'
* FIX: 'reset order' bug fix - button not work, when answers are ordered in edit poll screen and you wanted to reset the order - I missed one letter in the code during refactoring :)
* FIX: 'additional css' update bug fix: you can't empty it...
* FIX: some other minor fixes...
* CHG: EN translation is updated.

= 5.2.2 =
* FIX: when click on 'close', 'open', 'activate', 'deactivate' buttons at polls list table, the action was applied not immediately
* FIX: radio, checkbox styles fix

= 5.2.1 =
* ADD: 'in posts' column in admin polls list. In which posts the poll shortcode used.

= 5.2.0 =
* ADD: hooks: 'dem_poll_inserted', 'dem_before_insert_quest_data'
* ADD: two variants to delete logs: only logs and logs with votes.
* ADD: possibiliti to delete single answer log.
* ADD: "all voters" at the bottom of a poll if the poll is multiple.
* ADD: delete answer logs on answer deleting.
* ADD: button to delete all logs of closed polls.
* ADD: not show logs link in polls list table, when the poll don't have any log records.
* ADD: collapse extremely height polls under 'max height' option. All answers expands when user click on answers area.
* ADD: css themes for 'radio' and 'checkboks' inputs. Added special css classes and span after input element into the poll HTML code.
* ADD: now you can set access to add, edit polls and logs to other wordpress roles (editor, author etc.).
* ADD: mark 'NEW' for newely added answers by any user, except poll creator.
* ADD: 'NEW' mark filter and 'NEW' mark clear button in plugin logs table.
* ADD: country name and flag in logs table, parsed from voter IP.
* ADD: ability to sort answers (set order) in edit/add poll admin page. In this case answers will showen by the order.
* ADD: one more option to sort answers by random on display its in poll.
* ADD: sort option for single poll. It will overtake global sort option.
* FIX: fix admin css bug in firefox on design screen...
* CHG: EN translation is updated.

= 5.1.1 =
* SEO Fix: Now sets 404 response and "noindex" head tag for duplicate pages with: $_GET['dem_act'] or $_GET['dem_pid'] or $_GET['show_addanswerfield']

= 5.1.0 =
* Fix: Change DB ip field from int(11) to bigint(20). Because of this some IP was writen wrong. Also, change some other DB fields types, but it's no so important.

= 5.0.3 =
* Fix: Some bugs with variables and antivirus check.

= 5.0.2 =
* FIX: not correctly set answers on cache mode, because couldn't detect current screen correctly.

= 5.0.1 =
* ADD: expand answers list on Polls list page by click on the block.

= 5.0 =
* FIX: replace VOTE button with REVOTE. On cache mode, after user voting he see backVOTE button (on result screen), but not "revote" or "nothing" (depence on poll options).
* HUGE ADD: Don't show results until vote is closed. You can choose this option for single poll or for all polls (on settings page).
* ADD: edit & view links on admin logs page.
* ADD: Search poll field on admin polls list page.
* ADD: All answers (not just win) in "Winner" column on polls list page. For usability answers are folds.
* ADD: Poll shordcode on edit poll page. Auto select on its click.
* CHG: sort answers by votes on edit poll page.

= 4.9.4 =
* FIX: change default DB tables charset from utf8mb4 to utf8. Thanks to Nanotraktor

= 4.9.3 =
* ADD: single poll option that allow set limit for max answers if there is multiple answers option.
* ADD: global option that allow hide vote button on polls with no multiple answers and revote possibility. Users will vote by clicking on answer itself.
* fix: disable cache on archive page.

= 4.9.2 =
* FIX: bootstrap .label class conflict. Rename .label to .dem-label. If you discribe .label class in 'additional css' rename it to .dem-label please.
* ADD: Now on new version css regenerated automaticaly when you enter any democracy admin page.

= 4.9.1 =
* FIX: Polls admin table column order

= 4.9.0 =
* ADD: Logs table in admin and capability to remove only logs of specific poll.
* ADD: 'date' field to the democracy_log table.

= 4.8 =
* Complatelly change polls list table output. Now it work under WP_List_Table and have sortable colums, pagination, search (in future) etc.

= 4.7.8 =
* ADD: en_US l10n if no l10n file.

= 4.7.7 =
* ADD: de_DE localisation. Thanks to Matthias Siebler

= 4.7.6 =
* DELETED: possibility to work without javascript. Now poll works only with enabled javascript in your browser. It's better because you don't have any additional URL with GET parametrs. It's no-need-URL in 99% cases..

= 4.7.5 =
* CHG: Convert tables from utf8 to utf8mb4 charset. For emoji uses in polls

= 4.7.4 =
* CHG: Some css styles in admin

= 4.7.3 =
* ADD: Custom front-end localisation - as single settings page. Now you can translate all phrases of Poll theme as you like.

= 4.7.2 =
* CHG: in main js cache result/vote view was setted with animation. Now it sets without animation & so the view change invisible for users. Also, fix with democracy wrap block height set, now it's sets on "load" action, but not "document.ready".
* CHG: "block.css" theme improvements for better design.

= 4.7.1 =
* ADD: "on general options page": global "revote" and "democratic" functionality disabling ability
* ADD: localisation POT file & english transtation

= 4.7.0 =
* CHG: "progress fill type" & "answers order" options now on "Design option page"
* FIX: english localisation

= 4.6.9 =
* CHG: delete "add new answer" button on Add new poll and now field for new answerr adds when you focus on last field.

= 4.6.8 =
* FIX: options bug appers in 4.6.7

= 4.6.7 =
* ADD: check for current user has an capability to edit polls. Now toolbar doesn't shown if user logged in but not have capability

= 4.6.6 =
* FIX: Huge bug about checking is user already vote or not. This is must have release!
* CHG: a little changes in js code
* 'notVote' cookie check set to 1 hour

= 4.6.5 =
* ADD: New theme "block.css"
* ADD: Preset theme (_preset.css) now visible and you can set it and wtite additional css styles to customize theme

= 4.6.4 =
* FIX: when user send democratic answer, new answer couldn't have comma

= 4.6.3 =
* FIX: Widget showed screens uncorrectly because of some previous changes in code.
* Improve: English localisation

= 4.6.2 =
* FIX: great changes about polls themes and css structure.
* ADD: "Ace" css editor. Now you can easely write your own themes by editing css in admin.

= 4.6.1 =
* FIX: some little changes about themes settings, translate, css.
* ADD: screenshots to WP directory.

= 4.6.0 =
* ADD: Poll themes management
* FIX: some JS and CSS bugs
* FIX: Unactivate pool when closing poll

= 4.5.9 =
* FIX: CSS fixes, prepare to 4.6.0 version update
* ADD: Cache working. Wright/check cookie "notVote" for cache gear optimisation

= 4.5.8 =
* ADD: AJAX loader images SVG & css3 collection
* ADD: Sets close date when closing poll

= 4.5.7 =
* FIX: revote button didn't minus votes if "keep-logs" option was disabled

= 4.5.6 =
* ADD: right working with cache plugins. Auto unable/dasable with wp total cache, wp super cache, WordFence, WP Rocket, Quick Cache. If you use the other plugin you can foorce enable this option.
* ADD: add link to selected css file in settings page, to conviniently copy or view the css code
* ADD: php 5.3+ needed check & notice if php unsuitable
* Changed: archive page ID in option, but not link to the archive page
* FIX: in_archive check... to not show archive link on archive page
* FIX: many code improvements & some bug fix (hide archive page link if 0 set as ID, errors on activation, etc.)

= 4.5.5 =
* CHG: Archive link detection by ID not by url

= 4.5.4 =
* FIX: js code. Now All with jQuery
* FIX: Separate js and css connections: css connect on all pages into the head, but js connected into the bottom just for page where it need

= 4.5.3 =
* FIX: code fix, about $_POST[*] vars

= 4.5.2 =
* FIX: Remove colling wp-load.php files directly on AJAX request. Now it works with wordpress environment - it's much more stable.
* FIX: fixes about safe SQL calls. Correct escaping of passing variables. Now work with $wpdb->* functions where it posible
* FIX: admin messages

= 4.5.1 =
* FIX: Localisation bug on activation.

= 4.5 =
* ADD: css style themes support.
* ADD: new flat (flat.css) theme.
* FIX: Some bugs in code.

= 4.4 =
* ADD: All plugin functionality when javascript is disabled in browser.
* FIX: Some bug.

= 4.3.1 =
* ADD: "add user answer text" field close button when on multiple vote. Now it's much more convenient.
* FIX: Some bug.

= 4.3 =
* ADD: TinyMCE button.
* FIX: Some bug.

= 4.2 =
* ADD: Revote functionality.

= 4.1 =
* ADD: "only registered users can vote" functionality.
* ADD: Minified versions of CSS (*.min.css) and .js (*.min.js) is loaded if they exists.
* ADD: js/css inline including: Adding code of .css and .js files right into HTML. This must improve performance a little.
* ADD: .js and .css files (or theirs code) loads only on the pages where polls is shown.
* ADD: Toolbar menu for fast access. It help easily manage polls. The menu can be disabled.

= 4.0 =
* ADD: Multiple voting functionality.
* ADD: Opportunity to change answers votes in DataBase.
* ADD: "Random show one of many active polls" functionality.
* ADD: Poll expiration date functionality.
* ADD: Poll expiration datepicker on jQuery.
* ADD: Open/close polls functionality.
* ADD: Localisation functionality. Translation to English.
* ADD: Change {democracy}/{democracy:*} shortcode to standart WP [democracy]/[democracy id=*].
* ADD: jQuery support and many features because of this.
* ADD: Edit button for each poll (look at right top corner) to convenient edit poll when logged in.
* ADD: Clear logs button.
* ADD: Smart "create archive page" button on plugin's settings page.
* FIX: Improve about 80% of plugin code and logic in order to easily expand the plugin functionality in the future.
* FIX: Improve css output. Now it's more adaptive for different designs.


