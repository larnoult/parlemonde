<?php

/*
  Plugin Name: Newsletter
  Plugin URI: https://www.thenewsletterplugin.com/plugins/newsletter
  Description: Newsletter is a cool plugin to create your own subscriber list, to send newsletters, to build your business. <strong>Before update give a look to <a href="https://www.thenewsletterplugin.com/category/release">this page</a> to know what's changed.</strong>
  Version: 5.6.7
  Author: Stefano Lissa & The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
  Text Domain: newsletter

  Copyright 2009-2018 The Newsletter Team (email: info@thenewsletterplugin.com, web: https://www.thenewsletterplugin.com)
 */

// Used as dummy parameter on css and js links
define('NEWSLETTER_VERSION', '5.6.7');

global $newsletter, $wpdb;

if (!defined('NEWSLETTER_EMAILS_TABLE'))
    define('NEWSLETTER_EMAILS_TABLE', $wpdb->prefix . 'newsletter_emails');

if (!defined('NEWSLETTER_USERS_TABLE'))
    define('NEWSLETTER_USERS_TABLE', $wpdb->prefix . 'newsletter');

if (!defined('NEWSLETTER_STATS_TABLE'))
    define('NEWSLETTER_STATS_TABLE', $wpdb->prefix . 'newsletter_stats');

if (!defined('NEWSLETTER_SENT_TABLE'))
    define('NEWSLETTER_SENT_TABLE', $wpdb->prefix . 'newsletter_sent');

// Do not use basename(dirname()) since on activation the plugin is sandboxed inside a function
define('NEWSLETTER_SLUG', 'newsletter');

define('NEWSLETTER_DIR', __DIR__);
define('NEWSLETTER_INCLUDES_DIR', __DIR__ . '/includes');

// Almost obsolete but the first two must be kept for compatibility with modules
define('NEWSLETTER_URL', WP_PLUGIN_URL . '/newsletter');

if (!defined('NEWSLETTER_LIST_MAX'))
    define('NEWSLETTER_LIST_MAX', 40);

if (!defined('NEWSLETTER_PROFILE_MAX'))
    define('NEWSLETTER_PROFILE_MAX', 20);

if (!defined('NEWSLETTER_FORMS_MAX'))
    define('NEWSLETTER_FORMS_MAX', 10);

if (!defined('NEWSLETTER_CRON_INTERVAL'))
    define('NEWSLETTER_CRON_INTERVAL', 300);

if (!defined('NEWSLETTER_HEADER'))
    define('NEWSLETTER_HEADER', true);

if (!defined('NEWSLETTER_DEBUG'))
    define('NEWSLETTER_DEBUG', false);

// Force the whole system log level to this value
//define('NEWSLETTER_LOG_LEVEL', 4);

require_once NEWSLETTER_INCLUDES_DIR . '/logger.php';
require_once NEWSLETTER_INCLUDES_DIR . '/store.php';
require_once NEWSLETTER_INCLUDES_DIR . '/module.php';
require_once NEWSLETTER_INCLUDES_DIR . '/themes.php';
require_once NEWSLETTER_INCLUDES_DIR . '/TNP.php';

class Newsletter extends NewsletterModule {

    // Limits to respect to avoid memory, time or provider limits
    var $time_start;
    var $time_limit;
    var $email_limit = 10; // Per run, every 5 minutes
    var $limits_set = false;
    var $max_emails = 20;

    /**
     * @var PHPMailer
     */
    var $mailer;
    // Message shown when the interaction is inside a WordPress page
    var $message;
    var $user;
    var $error;
    var $theme;
    // Theme autocomposer variables
    var $theme_max_posts;
    var $theme_excluded_categories; // comma separated ids (eventually negative to exclude)
    var $theme_posts; // WP_Query object
    // Secret key to create a unique log file name (and may be other)
    var $action = '';
    static $instance;

    const MAX_CRON_SAMPLES = 100;
    const STATUS_NOT_CONFIRMED = 'S';
    const STATUS_CONFIRMED = 'C';

    /**
     * @return Newsletter
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new Newsletter();
        }
        return self::$instance;
    }

    function __construct() {
        // Grab it before a plugin decides to remove it.
        if (isset($_GET['na'])) {
            $this->action = $_GET['na'];
        }
        if (isset($_POST['na'])) {
            $this->action = $_POST['na'];
        }

        if (!empty($this->action)) {
            // For old versions of wp super cache
            $_GET['preview'] = 'true';
        }

        $this->time_start = time();

        // Here because the upgrade is called by the parent constructor and uses the scheduler
        add_filter('cron_schedules', array($this, 'hook_cron_schedules'), 1000);
        parent::__construct('main', '1.5.0', null, array('info', 'smtp'));

        $max = $this->options['scheduler_max'];
        if (!is_numeric($max)) {
            $max = 100;
        }
        $this->max_emails = max(floor($max / 12), 1);

        add_action('init', array($this, 'hook_init'));
        add_action('newsletter', array($this, 'hook_newsletter'), 1);
        add_action('newsletter_extension_versions', array($this, 'hook_newsletter_extension_versions'), 1);
        add_action('plugins_loaded', array($this, 'hook_plugins_loaded'));

        // This specific event is created by "Feed by mail" panel on configuration
        add_action('shutdown', array($this, 'hook_shutdown'));

        $this->update_cron_stats();

        register_activation_hook(__FILE__, array($this, 'hook_activate'));
        register_deactivation_hook(__FILE__, array($this, 'hook_deactivate'));

        add_action('admin_init', array($this, 'hook_admin_init'));

        if (is_admin()) {
            add_action('admin_head', array($this, 'hook_admin_head'));

            // Protection against strange schedule removal on some installations
            if (!wp_next_scheduled('newsletter') && (!defined('WP_INSTALLING') || !WP_INSTALLING)) {
                wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
            }

            add_action('admin_menu', array($this, 'add_extensions_menu'), 90);
        }
    }

    function update_cron_stats() {
        if (defined('DOING_CRON') && DOING_CRON) {
            $calls = get_option('newsletter_diagnostic_cron_calls', array());
            $calls[] = time();
            if (count($calls) > self::MAX_CRON_SAMPLES) {
                array_shift($calls);
            }
            update_option('newsletter_diagnostic_cron_calls', $calls, false);

            if (count($calls) > 50) {
                $mean = 0;
                $max = 0;
                $min = 0;
                for ($i = 1; $i < count($calls); $i++) {
                    $diff = $calls[$i] - $calls[$i - 1];
                    $mean += $diff;
                    if ($min == 0 || $min > $diff) {
                        $min = $diff;
                    }
                    if ($max < $diff) {
                        $max = $diff;
                    }
                }
                $mean = $mean / count($calls) - 1;
                update_option('newsletter_diagnostic_cron_data', array('mean' => $mean, 'max' => $max, 'min' => $min), false);
            } else {
                update_option('newsletter_diagnostic_cron_data', '', false);
            }
        }
    }

    function hook_activate() {
        // Ok, why? When the plugin is not active WordPress may remove the scheduled "newsletter" action because
        // the every-five-minutes schedule named "newsletter" is not present.
        // Since the activation does not forces an upgrade, that schedule must be reactivated here. It is activated on
        // the upgrade method as well for the user which upgrade the plugin without deactivte it (many).
        if (!wp_next_scheduled('newsletter')) {
            wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
        }

        $install_time = get_option('newsletter_install_time');
        if (!$install_time) {
            update_option('newsletter_install_time', time(), false);
        }

        Newsletter::instance()->upgrade();
        NewsletterUsers::instance()->upgrade();
        NewsletterEmails::instance()->upgrade();
        NewsletterSubscription::instance()->upgrade();
        NewsletterStatistics::instance()->upgrade();
        NewsletterProfile::instance()->upgrade();
    }

    function first_install() {
        parent::first_install();
        update_option('newsletter_show_welcome', '1', false);
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        parent::upgrade();


        $sql = "CREATE TABLE `" . $wpdb->prefix . "newsletter_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(10) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` longtext,
  `subject2` varchar(255) NOT NULL DEFAULT '',
  `message2` longtext,
  `name2` varchar(255) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','sending','sent','paused') NOT NULL DEFAULT 'new',
  `total` int(11) NOT NULL DEFAULT '0',
  `last_id` int(11) NOT NULL DEFAULT '0',
  `sent` int(11) NOT NULL DEFAULT '0',
  `track` int(11) NOT NULL DEFAULT '0',
  `list` int(11) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '',
  `query` longtext,
  `editor` tinyint(4) NOT NULL DEFAULT '0',
  `sex` varchar(20) NOT NULL DEFAULT '',
  `theme` varchar(50) NOT NULL DEFAULT '',
  `message_text` longtext,
  `preferences` longtext,
  `send_on` int(11) NOT NULL DEFAULT '0',
  `token` varchar(10) NOT NULL DEFAULT '',
  `options` longtext,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0',
  `version` varchar(10) NOT NULL DEFAULT '',
  `open_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $charset_collate;";

        dbDelta($sql);

        // WP does not manage composite primary key when it tries to upgrade a table...
        $suppress_errors = $wpdb->suppress_errors(true);

        dbDelta("CREATE TABLE " . $wpdb->prefix . "newsletter_sent (
            email_id int(10) unsigned NOT NULL DEFAULT '0',
            user_id int(10) unsigned NOT NULL DEFAULT '0',
            status tinyint(1) unsigned NOT NULL DEFAULT '0',
            open tinyint(1) unsigned NOT NULL DEFAULT '0',
            time int(10) unsigned NOT NULL DEFAULT '0',
            error varchar(100) NOT NULL DEFAULT '',
	    ip varchar(100) NOT NULL DEFAULT '',
            PRIMARY KEY (email_id,user_id),
            KEY user_id (user_id),
            KEY email_id (email_id)
          ) $charset_collate;");
        $wpdb->suppress_errors($suppress_errors);

//        if ('utf8mb4' === $wpdb->charset) {
//            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//            if (function_exists('maybe_convert_table_to_utf8mb4')) {
//                maybe_convert_table_to_utf8mb4(NEWSLETTER_EMAILS_TABLE);
//            }
//        }
        // Some setting check to avoid the common support request for mis-configurations
        $options = $this->get_options();

        if (empty($options['scheduler_max']) || !is_numeric($options['scheduler_max'])) {
            $options['scheduler_max'] = 100;
            $this->save_options($options);
        }

        wp_clear_scheduled_hook('newsletter');
        wp_schedule_event(time() + 30, 'newsletter', 'newsletter');

        wp_clear_scheduled_hook('newsletter_extension_versions');
        wp_schedule_event(time() + 30, 'daily', 'newsletter_extension_versions');

        $subscription_options = get_option('newsletter', array());

        // Settings migration
        if (empty($this->options['page'])) {
            if (isset($subscription_options['page']))
                $this->options['page'] = $subscription_options['page'];
            $this->save_options($this->options);
        }

        if (empty($this->options['css']) && !empty($subscription_options['css'])) {
            $this->options['css'] = $subscription_options['css'];
            $this->save_options($this->options);
        }

        // Migration of "info" options
        $info_options = $this->get_options('info');
        if (!empty($this->options['header_logo']) && empty($info_options['header_logo'])) {
            $info_options = $this->options;
            $this->save_options($info_options, 'info');
        }

        return true;
    }

    function admin_menu() {
        // This adds the main menu page
        add_menu_page('Newsletter', 'Newsletter', ($this->options['editor'] == 1) ? 'manage_categories' : 'manage_options', 'newsletter_main_index', '', plugins_url('newsletter') . '/images/menu-icon.png', '30.333');

        $this->add_menu_page('index', 'Dashboard');
        $this->add_menu_page('welcome', 'Welcome');
        $this->add_menu_page('main', 'Settings and More', 'manage_options');


        $this->add_admin_page('smtp', 'SMTP');
        $this->add_admin_page('status', 'Status', 'manage_options');
        $this->add_admin_page('info', 'Company info');
        //$this->add_admin_page('diagnostic', 'Diagnostic');
        //$this->add_admin_page('startup', 'Quick Startup');
    }

    function add_extensions_menu() {
        $this->add_menu_page('extensions', '<span style="color:#27AE60; font-weight: bold;">Extensions</span>');
    }

    /**
     * Returns a set of warnings about this installtion the suser should be aware of. Return an empty string
     * if there are no warnings.
     */
    function warnings() {
       
    }
    
    function hook_init() {
        global $cache_stop, $hyper_cache_stop, $wpdb;

        if (isset($this->options['debug']) && $this->options['debug'] == 1) {
            ini_set('log_errors', 1);
            ini_set('error_log', WP_CONTENT_DIR . '/logs/newsletter/php-' . date('Y-m') . '-' . get_option('newsletter_logger_secret') . '.txt');
        }

        add_shortcode('newsletter_replace', array($this, 'shortcode_newsletter_replace'));

        if (is_admin()) {
            add_action('admin_notices', array($this, 'hook_admin_notices'));

            if ($this->is_admin_page()) {
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-ui-tooltip');
                wp_enqueue_media();
                wp_enqueue_style('tnp-admin', plugins_url('newsletter') . '/admin.css', array(), filemtime(NEWSLETTER_DIR . '/admin.css'));
                wp_enqueue_script('tnp-admin', plugins_url('newsletter') . '/admin.js', array('jquery'), time());

                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');

                wp_enqueue_style('tnp-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
                wp_enqueue_script('tnp-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js');
                wp_enqueue_script('tnp-jquery-vmap', 'https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js', array('jquery'));
                wp_enqueue_script('tnp-jquery-vmap-world', 'https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.world.js', array('tnp-jquery-vmap'));
                wp_enqueue_style('tnp-jquery-vmap', 'https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css');

                wp_register_script('tnp-chart', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js', array('jquery'));

                $dismissed = get_option('newsletter_dismissed', array());

                if (isset($_GET['dismiss'])) {
                    $dismissed[$_GET['dismiss']] = 1;
                    update_option('newsletter_dismissed', $dismissed);
                    wp_redirect($_SERVER['HTTP_REFERER']);
                    exit();
                }
            }
        } else {
            add_action('wp_enqueue_scripts', array($this, 'hook_wp_enqueue_scripts'));
        }
        
        //add_filter('site_transient_update_plugins', array($this, 'hook_site_transient_update_plugins'));

        if (empty($this->action)) {
            return;
        }

        // TODO: Remove!
        $cache_stop = true;
        $hyper_cache_stop = true;

        if ($this->action == 'fu') {
            $user = $this->check_user();
            if ($user == null) {
                die('No user');
            }
            $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set followup=2 where id=" . $user->id);
            $options_followup = get_option('newsletter_followup');
            $this->message = $options_followup['unsubscribed_text'];
            return;
        }

        if ($this->action == 'test') {
            echo 'ok';
            die();
        }
    }
    
//    function hook_site_transient_update_plugins($value) {
//        //return null;
//        //var_dump($value);
//        //die('xxx');
//        if (!isset($value->response['newsletter/plugin.php'])) {
//         $value->response['newsletter/plugin.php'] = $value->no_update['newsletter/plugin.php'];   
//        }
//        
//        // Add caching
//        $response = wp_remote_get('http://plugins.svn.wordpress.org/newsletter/');
//        $body = wp_remote_retrieve_body($response);
//        
//        $x = strpos($body, 'Revision');
//        $x += 9;
//        $y = strpos($body, ':', $x);
//        $version = substr($body, $x, $y-$x);
//        $value->checked['newsletter/plugin.php'] = $version;
//        $value->response['newsletter/plugin.php']->new_version = $version;
//        $value->response['newsletter/plugin.php']->package = 'https://downloads.wordpress.org/plugin/newsletter.zip';
//        
//        return $value;
//
//    }

    function hook_admin_notices() {
        // Check of Newsletter dedicated page
        if (!empty($this->options['page'])) {
            if (get_post_status($this->options['page']) !== 'publish') {
                echo '<div class="notice notice-error"><p>The Newsletter dedicated page is not published. <a href="', site_url('/wp-admin/post.php') . '?post=', $this->options['page'], '&action=edit"><strong>Edit the page</strong></a> or <a href="admin.php?page=newsletter_main_main"><strong>review the main settings</strong></a>.</p></div>';
            } else {
                $content = get_post_field('post_content', $this->options['page']);
                // With and without attributes
                if (strpos($content, '[newsletter]') === false && strpos($content, '[newsletter ') === false) {
                    echo '<div class="notice notice-error"><p>The Newsletter dedicated page does not contain the [newsletter] shortcode. <a href="', site_url('/wp-admin/post.php'), '?post=', $this->options['page'], '&action=edit"><strong>Edit the page</strong></a> or <a href="admin.php?page=newsletter_main_main"><strong>review the main settings</strong></a>.</p></div>';
                }
            }
        }
        
        if (isset($this->options['debug']) && $this->options['debug'] == 1) {
            echo '<div class="notice notice-warning"><p>The Newsletter plugin is in <strong>debug mode</strong>. When done change it on Newsletter <a href="admin.php?page=newsletter_main_main"><strong>main settings</strong></a>. Do not keep the debug mode active on production sites.</p></div>';
        }
        
        $x = wp_next_scheduled('newsletter');
        if ($x === false) {
            echo '<div class="notice notice-error"><p>The Newsletter delivery engine is off (it should never be off). Deactivate and reactivate the Newsletter plugin.</p></div>';
        } else if (time() - $x > 900) {
            echo '<div class="notice notice-error"><p>The WP scheduler doesn\'t seem to be running correctly for Newsletter. <a href="https://www.thenewsletterplugin.com/documentation/newsletter-delivery-engine#cron" target="_blank"><strong>Read this page to solve the problem</strong></a>.</p></div>';
        } else {
//            if (empty($this->options['disable_cron_notice'])) {
//                $cron_data = get_option('newsletter_diagnostic_cron_data');
//                if ($cron_data && $cron_data['mean'] > 500) {
//                    echo '<div class="notice notice-error"><p>The WP scheduler doesn\'t seem to be triggered enough often for Newsletter. <a href="https://www.thenewsletterplugin.com/documentation/newsletter-delivery-engine#cron" target="_blank"><strong>Read this page to solve the problem</strong></a> or disable this notice on <a href="admin.php?page=newsletter_main_main"><strong>main settings</strong></a>.</p></div>';
//                }
//            }
        }
    }

    function hook_wp_enqueue_scripts() {
        if (empty($this->options['css_disabled']) && apply_filters('newsletter_enqueue_style', true)) {
            wp_enqueue_style('newsletter', plugins_url('newsletter') . '/style.css', array(), NEWSLETTER_VERSION);
            if (!empty($this->options['css'])) {
                wp_add_inline_style('newsletter', $this->options['css']);
            }
        }
    }

    function shortcode_newsletter_replace($attrs, $content) {
        $content = do_shortcode($content);
        $content = $this->replace($content, $this->get_user_from_request(), $this->get_email_from_request());
        return $content;
    }

    function is_admin_page() {
        if (!isset($_GET['page'])) {
            return false;
        }
        $page = $_GET['page'];
        return strpos($page, 'newsletter_') === 0;
    }

    function hook_admin_init() {
        // Verificare il contesto
        if (isset($_GET['page']) && $_GET['page'] === 'newsletter_main_welcome')
            return;
        if (get_option('newsletter_show_welcome')) {
            delete_option('newsletter_show_welcome');
            wp_redirect(admin_url('admin.php?page=newsletter_main_welcome'));
        }

        // https://developer.wordpress.org/plugins/privacy/suggesting-text-for-the-site-privacy-policy/
        // https://make.wordpress.org/core/2018/05/17/4-9-6-update-guide/
        if (function_exists('wp_add_privacy_policy_content')) {
            //wp_add_privacy_policy_content('Newsletter', wp_kses_post( wpautop( $content, false )));
        }
    }

    function hook_admin_head() {
        // Small global rule for sidebar menu entries
        echo '<style>';
        echo '.tnp-side-menu { color: #E67E22!important; }';
        echo '</style>';
    }

    function relink($text, $email_id, $user_id, $email_token = '') {
        return NewsletterStatistics::instance()->relink($text, $email_id, $user_id, $email_token);
    }

    /**
     * Runs every 5 minutes and look for emails that need to be processed.
     */
    function hook_newsletter() {
        global $wpdb;

        $this->logger->debug('hook_newsletter> Start');

        // Do not accept job activation before at least 4 minutes are elapsed from the last run.
        if (!$this->check_transient('engine', NEWSLETTER_CRON_INTERVAL)) {
            return;
        }

        // Retrieve all emails in "sending" status
        $emails = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where status='sending' and send_on<" . time() . " order by id asc");
        $this->logger->debug('hook_newsletter> Emails found in sending status: ' . count($emails));
        foreach ($emails as $email) {
            $this->logger->debug('hook_newsletter> Sending email ' . $email->id);
            $this->send($email);
        }
        // Remove the semaphore so the delivery engine can be activated again
        $this->delete_transient('engine');

        $this->logger->debug('hook_newsletter> End');
    }

    /**
     * Sends an email to targeted users or to given users. If a list of users is given (usually a list of test users)
     * the query inside the email to retrieve users is not used.
     *
     * @global wpdb $wpdb
     * @global type $newsletter_feed
     * @param type $email
     * @param array $users
     * @return boolean True if the proccess completed, false if limits was reached. On false the caller should no continue to call it with other emails.
     */
    function send($email, $users = null) {
        global $wpdb;

        ignore_user_abort(true);

        if (is_array($email)) {
            $email = (object) $email;
        }

        // Could be a test
        if (empty($email->id)) {
            $email->id = 0;
        }

        $this->logger->debug('send> Email ID: ' . $email->id);

        // This stops the update of last_id and sent fields since it's not a scheduled delivery but a test or something else (like an autoresponder)
        $test = $users != null;

        if ($users == null) {

            $skip_this_run = apply_filters('newsletter_send_skip', false, $email);
            if ($skip_this_run) {
                return false;
            }

            if (empty($email->query)) {
                $email->query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
            }

            $email->options = maybe_unserialize($email->options);
            $max_emails = apply_filters('newsletter_send_max_emails', $this->max_emails, $email);

            $this->logger->debug('send> Max emails per run: ' . $max_emails);

            if (empty($max_emails)) {
                $this->logger->error('send> Max emails empty after the filter');
                $max_emails = $this->max_emails;
            }

            //$query = apply_filters('newsletter_send_query', $email->query, $email);
            $query = $email->query;
            $query .= " and id>" . $email->last_id . " order by id limit " . $max_emails;

            $this->logger->debug('send> Query: ' . $query);

            $users = $wpdb->get_results($query);

            $this->logger->debug('send> Loaded users: ' . count($users));

            // If there was a database error, do nothing
            if ($wpdb->last_error) {
                $this->logger->fatal($wpdb->last_error);
                $this->logger->fatal($wpdb->last_query);
                return;
            }

            if (empty($users)) {
                $this->logger->info('send> No more users, set as sent');
                $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set status='sent', total=sent where id=" . $email->id . " limit 1");
                return true;
            }

            //$users = apply_filters('newsletter_send_users', $users, $email);
        }

        $start_time = microtime(true);
        $count = 0;
        $result = true;

        foreach ($users as $user) {
            $this->logger->debug('send> Processing user ID: ' . $user->id);

            // Before try to send, check the limits.
            if (!$test && $this->limits_exceeded()) {
                $result = false;
                break;
            }

            $headers = array('List-Unsubscribe' => '<' . $this->build_action_url('u', $user, $email) . '>');
            $headers['Precedence'] = 'bulk';
            $headers['X-Newsletter-Email-Id'] = $email->id;


            if (!$test) {
                $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set sent=sent+1, last_id=" . $user->id . " where id=" . $email->id . " limit 1");
            }

            $m = $this->replace($email->message, $user, $email->id);
            $mt = $this->replace($email->message_text, $user, $email->id);

            $m = apply_filters('newsletter_message_html', $m, $email, $user);

            if ($email->track == 1) {
                $m = $this->relink($m, $email->id, $user->id, $email->token);
            }

            $s = $this->replace($email->subject, $user);
            $s = apply_filters('newsletter_message_subject', $s, $email, $user);

            if (!empty($user->wp_user_id)) {
                $this->logger->debug('send> Has wp_user_id: ' . $user->wp_user_id);
                // TODO: possibly name extraction
                $wp_user_email = $wpdb->get_var($wpdb->prepare("select user_email from $wpdb->users where id=%d limit 1", $user->wp_user_id));
                if (!empty($wp_user_email)) {
                    $user->email = $wp_user_email;
                    $this->logger->debug('send> Email replaced with: ' . $user->email);
                } else {
                    $this->logger->debug('send> This WP user has not an email');
                }
            }

            $r = $this->mail($user->email, $s, array('html' => $m, 'text' => $mt), $headers, true);

            $status = $r ? 0 : 1;

            $this->save_sent($user, $email);

            $this->email_limit--;
            $count++;
        }
        
        
        if ($this->the_mailer != null) {
            $this->logger->debug('Flushing and checking errors');
            $this->the_mailer->flush();
            if (method_exists($this->the_mailer, 'get_errors')) {
                $errors = $this->the_mailer->get_errors();
                foreach ($errors as $error) {
                    $this->logger->debug('Error on email ' . $error['email']);
                    $this->save_sent($error['email'], $email, 1, $error['message']);
                }
            
            }
            
            $this->logger->debug('Clearing errors');
            if (method_exists($this->the_mailer, 'get_errors')) {
                $this->the_mailer->clear_errors();
            }
        }

        $end_time = microtime(true);

        if ($count > 0) {
            $send_calls = get_option('newsletter_diagnostic_send_calls', array());
            $send_calls[] = array($start_time, $end_time, $count, $result);

            if (count($send_calls) > self::MAX_CRON_SAMPLES)
                array_shift($send_calls);

            update_option('newsletter_diagnostic_send_calls', $send_calls, false);
        }
        return $result;
    }

    function save_sent($user, $email, $status = 0, $error = '') {
        global $wpdb;
        //$this->logger->debug('Saving sent data');
        $user_id = 0;
        if (is_numeric($user)) {
            $user_id = $user;
        } else if (is_array($user) && isset($user['id'])) {
            $user_id = $user['id'];
        } else if (is_object($user) && isset($user->id)) {
            $user_id = $user->id;
        } else if (is_string($user)) {
            // is an email
            $user = $this->get_user($user);
            if ($user) {
                $user_id = $user->id;
            }
        }

        $email_id = $this->to_int_id($email);

        if (!$user_id) {
            return;
        }
        //$this->logger->debug('Query');
        $wpdb->query($wpdb->prepare("insert into " . $wpdb->prefix . 'newsletter_sent (user_id, email_id, time, status, error) values (%d, %d, %d, %d, %s) on duplicate key update time=%d, status=%d, error=%s', $user_id, $email_id, time(), $status, $error, time(), $status, $error));
    }

    /**
     * This function checks is, during processing, we are getting to near to system limits and should stop any further
     * work (when returns true).
     */
    function limits_exceeded() {
        global $wpdb;

        if (!$this->limits_set) {
            $this->logger->debug('limits_exceeded> Setting the limits for the first time');

            @set_time_limit(NEWSLETTER_CRON_INTERVAL + 30);

            $max_time = (int) (@ini_get('max_execution_time') * 0.95);
            if ($max_time == 0 || $max_time > NEWSLETTER_CRON_INTERVAL) {
                $max_time = (int) (NEWSLETTER_CRON_INTERVAL * 0.95);
            }

            $this->time_limit = $this->time_start + $max_time;

            $this->logger->info('limits_exceeded> Max time set to ' . $max_time);

            $max = (int) $this->options['scheduler_max'];
            if (!$max) {
                $max = 100;
            }
            $this->email_limit = max(floor($max / 12), 1);
            $this->logger->debug('limits_exceeded> Max number of emails can send: ' . $this->email_limit);

            $wpdb->query("set session wait_timeout=300");
            // From default-constants.php
            if (function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < 128 )) {
                @ini_set('memory_limit', '256M');
            }

            $this->limits_set = true;
        }

        // The time limit is set on constructor, since it has to be set as early as possible
        if (time() > $this->time_limit) {
            $this->logger->info('limits_exceeded> Max execution time limit reached');
            return true;
        }

        if ($this->email_limit <= 0) {
            $this->logger->info('limits_exceeded> Max emails limit reached');
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $to
     * @param string $subject
     * @param string|array $message
     * @param type $headers
     * @return boolean
     */
    var $mail_method = null;

    function register_mail_method($callable) {
        $this->mail_method = $callable;
    }

    var $the_mailer = null;

    function register_mailer($mailer) {
        $this->the_mailer = $mailer;
    }

    var $mail_last_error = '';

    function mail($to, $subject, $message, $headers = null, $enqueue = false) {
        $this->mail_last_error = '';
        //$this->logger->debug('mail> To: ' . $to);
        //$this->logger->debug('mail> Subject: ' . $subject);
        if (empty($subject)) {
            $this->logger->error('mail> Subject empty, skipped');
            return true;
        }

        if (!$headers) {
            $headers = array();
        }

        $headers['X-Auto-Response-Suppress'] = 'OOF, AutoReply';

        // Message carrige returns and line feeds clean up
        if (!is_array($message)) {
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("\n", "\r\n", $message);
        } else {
            if (!empty($message['text'])) {
                $message['text'] = str_replace("\r\n", "\n", $message['text']);
                $message['text'] = str_replace("\r", "\n", $message['text']);
                $message['text'] = str_replace("\n", "\r\n", $message['text']);
            }

            if (!empty($message['html'])) {
                $message['html'] = str_replace("\r\n", "\n", $message['html']);
                $message['html'] = str_replace("\r", "\n", $message['html']);
                $message['html'] = str_replace("\n", "\r\n", $message['html']);
            }
        }

        if ($this->the_mailer != null) {
            $r = $this->the_mailer->mail($to, $subject, $message, $headers, $enqueue);
            if (is_wp_error($r)) {
                /* @var $r WP_Error */
                $this->mail_last_error = $r->get_error_message();
                return false;
            }
            return true;
        }


        if ($this->mail_method != null) {
            //$this->logger->debug('mail> alternative mail method found');
            return call_user_func($this->mail_method, $to, $subject, $message, $headers);
        }

        if ($this->mailer == null) {
            $this->mailer_init();
        }

        if ($this->mailer == null) {
            // If still null, we need to use wp_mail()...

            $wp_mail_headers = array();

            $wp_mail_headers[] = 'From: ' . $this->options['sender_name'] . ' <' . $this->options['sender_email'] . '>';

            if (!empty($this->options['return_path'])) {
                $wp_mail_headers[] = 'Return-Path: ' . $this->options['return_path'];
            }
            if (!empty($this->options['reply_to'])) {
                $wp_mail_headers[] = 'Reply-To: ' . $this->options['reply_to'];
            }

            if (!is_array($message)) {
                $wp_mail_headers[] = 'Content-Type: text/html;charset=UTF-8';
                $body = $message;
            } else {
                // Only html is present?
                if (!empty($message['html'])) {
                    $wp_mail_headers[] = 'Content-Type: text/html;charset=UTF-8';

                    $body = $message['html'];
                } else if (!empty($message['text'])) {
                    $wp_mail_headers[] = 'Content-Type: text/plain;charset=UTF-8';
                    //$this->mailer->IsHTML(false);
                    $body = $message['text'];
                }
            }

            if (is_array($headers)) {
                foreach ($headers as $key => $value) {
                    $wp_mail_headers[] = $key . ': ' . $value;
                }
            }

            $r = wp_mail($to, $subject, $body, $wp_mail_headers);
            if (!$r) {
                $last_error = error_get_last();
                if (is_array($last_error)) {
                    $this->mail_last_error = $last_error['message'];
                }
            }
            return $r;
        }

        // Simple message is asumed to be html
        if (!is_array($message)) {
            $this->mailer->IsHTML(true);
            $this->mailer->Body = $message;
        } else {
            // Only html is present?
            if (empty($message['text'])) {
                $this->mailer->IsHTML(true);
                $this->mailer->Body = $message['html'];
            }
            // Only text is present?
            else if (empty($message['html'])) {
                $this->mailer->IsHTML(false);
                $this->mailer->Body = $message['text'];
            } else {
                $this->mailer->IsHTML(true);
                $this->mailer->Body = $message['html'];
                $this->mailer->AltBody = $message['text'];
            }
        }

        $this->mailer->Subject = $subject;

        $this->mailer->ClearCustomHeaders();
        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $this->mailer->AddCustomHeader($key . ': ' . $value);
            }
        }

        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($to);
        $this->mailer->Send();

        if ($this->mailer->IsError()) {
            $this->mail_last_error = $this->mailer->ErrorInfo;
            $this->logger->error('mail> ' . $this->mailer->ErrorInfo);
            // If the error is due to SMTP connection, the mailer cannot be reused since it does not clean up the connection
            // on error.
            $this->mailer = null;
            return false;
        }
        return true;
    }

    /**
     * Returns the SMTP options filtered so extensions can change them.
     */
    function get_smtp_options() {
        $smtp_options = $this->get_options('smtp');
        $smtp_options = apply_filters('newsletter_smtp', $smtp_options);
        return $smtp_options;
    }

    function mailer_init() {
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';

        $smtp_options = $this->get_smtp_options();


        if ($smtp_options['enabled'] == 1) {
            $this->mailer = new PHPMailer();
            $this->mailer->IsSMTP();
            $this->mailer->Host = $smtp_options['host'];
            if (!empty($smtp_options['port']))
                $this->mailer->Port = (int) $smtp_options['port'];

            if (!empty($smtp_options['user'])) {
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $smtp_options['user'];
                $this->mailer->Password = $smtp_options['pass'];
            }
            $this->mailer->SMTPKeepAlive = true;
            $this->mailer->SMTPSecure = $smtp_options['secure'];
            $this->mailer->SMTPAutoTLS = false;

            if ($smtp_options['ssl_insecure'] == 1) {
                $this->mailer->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            }
        } else {
            if ($this->options['phpmailer'] == 1) {
                $this->mailer = new PHPMailer();
                $this->mailer->IsMail();
            } else {
                $this->mailer = null;
                return;
            }
        }

        if (!empty($this->options['content_transfer_encoding'])) {
            $this->mailer->Encoding = $this->options['content_transfer_encoding'];
        } else {
            $this->mailer->Encoding = 'base64';
        }

        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->From = $this->options['sender_email'];

        $return_path = $this->options['return_path'];
        if (!empty($return_path)) {
            $this->mailer->Sender = $return_path;
        }
        if (!empty($this->options['reply_to'])) {
            $this->mailer->AddReplyTo($this->options['reply_to']);
        }

        $this->mailer->FromName = $this->options['sender_name'];
    }

    function hook_deactivate() {
        wp_clear_scheduled_hook('newsletter');
    }

    function hook_cron_schedules($schedules) {
        $schedules['newsletter'] = array(
            'interval' => NEWSLETTER_CRON_INTERVAL, // seconds
            'display' => 'Newsletter'
        );
        return $schedules;
    }

    function shortcode_newsletter_form($attrs, $content) {
        return $this->form($attrs['form']);
    }

    function form($number = null) {
        if ($number == null)
            return $this->subscription_form();
        $options = get_option('newsletter_forms');

        $form = $options['form_' . $number];

        if (stripos($form, '<form') !== false) {
            $form = str_replace('{newsletter_url}', plugins_url('newsletter/do/subscribe.php'), $form);
        } else {
            $form = '<form method="post" action="' . plugins_url('newsletter/do/subscribe.php') . '" onsubmit="return newsletter_check(this)">' .
                    $form . '</form>';
        }

        $form = $this->replace_lists($form);

        return $form;
    }

    function find_file($file1, $file2) {
        if (is_file($file1))
            return $file1;
        return $file2;
    }

    function hook_shutdown() {
        if ($this->mailer != null)
            $this->mailer->SmtpClose();
    }

    /**
     * Called weekly if at least one extension is active.
     */
    function hook_newsletter_extension_versions($force = false) {
        if (!$force && !defined('NEWSLETTER_EXTENSION')) {
            return;
        }
        $response = wp_remote_get('http://www.thenewsletterplugin.com/wp-content/versions/all.txt?ts=' . time());
        if (is_wp_error($response)) {
            $this->logger->error($response);
            return;
        }

        $versions = json_decode(wp_remote_retrieve_body($response));
        update_option('newsletter_extension_versions', $versions, false);
    }

    function get_extension_version($extension_id) {
        $versions = get_option('newsletter_extension_versions');
        if (!is_array($versions)) {
            return null;
        }
        foreach ($versions as $data) {
            if ($data->id == $extension_id) {
                return $data->version;
            }
        }

        return null;
    }

    /**
     * Completes the WordPress plugin update data with the extension data. 
     * $value is the data WordPress is saving
     * $extension is an instance of an extension
     */
    function set_extension_update_data($value, $extension) {

        // See the wp_update_plugins function
        if (!is_object($value)) {
            return $value;
        }

        // If someone registered our extension name on wordpress.org... get rid of it otherwise
        // our extenions will be overwritten!
        unset($value->response[$extension->plugin]);
        unset($value->no_update[$extension->plugin]);

        if (defined('NEWSLETTER_EXTENSION_UPDATE') && !NEWSLETTER_EXTENSION_UPDATE) {
            return $value;
        }

        if (!function_exists('get_plugin_data')) {
            return $value;
        }

        $new_version = $this->get_extension_version($extension->id);

        if (empty($new_version)) {
            return $value;
        }

        if (function_exists('get_plugin_data')) {
            if (file_exists(WP_PLUGIN_DIR . '/' . $extension->plugin)) {
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $extension->plugin, false, false);
            } else if (file_exists(WPMU_PLUGIN_DIR . '/' . $extension->plugin)) {
                $plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . '/' . $extension->plugin, false, false);
            }
        }

        if (!isset($plugin_data)) {
            return $value;
        }

        if (version_compare($new_version, $plugin_data['Version']) <= 0) {
            return $value;
        }

        $plugin = new stdClass();
        $plugin->id = $extension->id;
        $plugin->slug = $extension->slug;
        $plugin->plugin = $extension->plugin;
        $plugin->new_version = $new_version;
        $plugin->url = '';
        $value->response[$extension->plugin] = $plugin;

        if (defined('NEWSLETTER_LICENSE_KEY')) {
            $value->response[$extension->plugin]->package = 'http://www.thenewsletterplugin.com/wp-content/plugins/file-commerce-pro/get.php?f=' . $extension->id .
                    '&k=' . NEWSLETTER_LICENSE_KEY;
        } else {
            $value->response[$extension->plugin]->package = 'http://www.thenewsletterplugin.com/wp-content/plugins/file-commerce-pro/get.php?f=' . $extension->id .
                    '&k=' . Newsletter::instance()->options['contract_key'];
        }

        return $value;
    }

    /**
     * Retrieve the extensions form the tnp site
     * @return array 
     */
    function getTnpExtensions() {

        $extensions_json = get_transient('tnp_extensions_json');

        if (false === $extensions_json) {
            $url = "http://www.thenewsletterplugin.com/wp-content/extensions.json";
            if (!empty($this->options['contract_key'])) {
                $url = "http://www.thenewsletterplugin.com/wp-content/plugins/file-commerce-pro/extensions.php?k=" . $this->options['contract_key'];
            }

            $extensions_response = wp_remote_get($url);
            $extensions_json = wp_remote_retrieve_body($extensions_response);
            if (!empty($extensions_json)) {
                set_transient('tnp_extensions_json', $extensions_json, 24 * 60 * 60);
            }
        }

        $extensions = json_decode($extensions_json);

        return $extensions;
    }

    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     */
    function hook_plugins_loaded() {
        if (function_exists('load_plugin_textdomain')) {
            load_plugin_textdomain('newsletter', false, plugin_basename(dirname(__FILE__)) . '/languages');
        }
    }

    var $panels = array();

    function add_panel($key, $panel) {
        if (!isset($this->panels[$key]))
            $this->panels[$key] = array();
        if (!isset($panel['id']))
            $panel['id'] = sanitize_key($panel['label']);
        $this->panels[$key][] = $panel;
    }

    function has_license() {
        return !empty($this->options['contract_key']);
    }

    /**
     * Returns the Newsletter dedicated page URL or an alternative URL if that page if not
     * configured or not available.
     * 
     * @staticvar string $url
     * @return string
     */
    var $newsletter_page_url = false;
    function get_newsletter_page_url($language = '') {
        
        // TODO: Reintroduce the cache
        //if (!$this->newsletter_page_url) {
            if (!empty($this->options['page'])) {
                $this->newsletter_page_url = get_permalink($this->options['page']);
                if ($language && $this->newsletter_page_url) {
                    if (class_exists('SitePress')) {
                        $this->newsletter_page_url = apply_filters('wpml_permalink', $this->newsletter_page_url , $language);
                    }
                    if (function_exists('pll_get_post')) {
                        $this->newsletter_page_url = get_permalink(pll_get_post($this->options['page']));
                    }
                }
            }
            if (!$this->newsletter_page_url) {
                $this->newsletter_page_url = $this->build_action_url('m');
            }
        //}
        return $this->newsletter_page_url;
    }

}

$newsletter = Newsletter::instance();

require_once NEWSLETTER_DIR . '/subscription/subscription.php';
require_once NEWSLETTER_DIR . '/unsubscription/unsubscription.php';
require_once NEWSLETTER_DIR . '/profile/profile.php';
require_once NEWSLETTER_DIR . '/emails/emails.php';
require_once NEWSLETTER_DIR . '/users/users.php';
require_once NEWSLETTER_DIR . '/statistics/statistics.php';
require_once NEWSLETTER_DIR . '/widget/standard.php';
require_once NEWSLETTER_DIR . '/widget/minimal.php';
