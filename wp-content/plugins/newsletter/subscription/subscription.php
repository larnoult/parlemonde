<?php

if (!defined('ABSPATH'))
    exit;

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterSubscription extends NewsletterModule {

    const MESSAGE_CONFIRMED = 'confirmed';
    const OPTIN_DOUBLE = 0;
    const OPTIN_SINGLE = 1;

    static $instance;

    /**
     * @var array 
     */
    var $options_profile;

    /**
     * @var array 
     */
    var $options_lists;

    /**
     * @return NewsletterSubscription
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterSubscription();
        }
        return self::$instance;
    }

    function __construct() {

        parent::__construct('subscription', '2.1.7', null, array('lists', 'template', 'profile'));
        $this->options_profile = $this->get_options('profile');
        $this->options_lists = $this->get_options('lists');

        // Must be called after the Newsletter::hook_init, since some constants are defined
        // there.
        add_action('init', array($this, 'hook_init'), 90);
    }

    function hook_init() {
        add_action('wp_loaded', array($this, 'hook_wp_loaded'));
        if (is_admin()) {
            add_action('admin_init', array($this, 'hook_admin_init'));
        } else {
            add_action('wp_enqueue_scripts', array($this, 'hook_wp_enqueue_scripts'));
            add_shortcode('newsletter', array($this, 'shortcode_newsletter'));
            add_shortcode('newsletter_form', array($this, 'shortcode_newsletter_form'));
            add_shortcode('newsletter_field', array($this, 'shortcode_newsletter_field'));
        }
    }

    function hook_admin_init() {
        if (isset($_GET['page']) && $_GET['page'] === 'newsletter_subscription_forms') {
            header('X-XSS-Protection: 0');
        }
    }

    function hook_wp_enqueue_scripts() {

        wp_enqueue_script('newsletter-subscription', plugins_url('newsletter') . '/subscription/validate.js', array(), NEWSLETTER_VERSION, true);

        $options = $this->get_options('profile', $this->get_current_language());
        
        $data = array();
        $data['messages'] = array();
        if (isset($options['email_error'])) {
            $data['messages']['email_error'] = $this->options_profile['email_error'];
        }
        if (isset($options['name_error'])) {
            $data['messages']['name_error'] = $this->options_profile['name_error'];
        }
        if (isset($options['surname_error'])) {
            $data['messages']['surname_error'] = $this->options_profile['surname_error'];
        }
        if (isset($options['profile_error'])) {
            $data['messages']['profile_error'] = $this->options_profile['profile_error'];
        }
        if (isset($options['privacy_error'])) {
            $data['messages']['privacy_error'] = $this->options_profile['privacy_error'];
        }
        $data['profile_max'] = NEWSLETTER_PROFILE_MAX;
        wp_localize_script('newsletter-subscription', 'newsletter', $data);
    }

    function ip_match($ip, $range) {
        if (strpos($range, '/')) {
            list ($subnet, $bits) = explode('/', $range);
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
            return ($ip & $mask) == $subnet;
        } else {
            return strpos($range, $ip) === 0;
        }
    }

    function is_address_blacklisted($email) {
        if (empty($this->options['address_blacklist'])) {
            return false;
        }

        $this->logger->debug('Address blacklist check');
        $rev_email = strrev($email);
        foreach ($this->options['address_blacklist'] as $item) {
            if (strpos($rev_email, strrev($item)) === 0) {
                return true;
            }
        }
        return false;
    }

    function is_ip_blacklisted($ip) {
        if (empty($this->options['ip_blacklist'])) {
            return false;
        }
        $this->logger->debug('IP blacklist check');
        foreach ($this->options['ip_blacklist'] as $item) {
            if ($this->ip_match($ip, $item)) {
                return true;
            }
        }
        return false;
    }

    function is_missing_domain_mx($email) {
        // Actually not fully implemented
        return false;

        if (empty($this->options['domain_check'])) {
            return false;
        }

        $this->logger->debug('Domain MX check');
        list($local, $domain) = explode('@', $email);

        $hosts = array();
        if (!getmxrr($domain, $hosts)) {
            return true;
        }
        return false;
    }

    function is_flood($email, $ip) {
        global $wpdb;

        if (empty($this->options['antiflood'])) {
            return false;
        }

        $this->logger->debug('Antiflood check');

        $updated = $wpdb->get_var($wpdb->prepare("select updated from " . NEWSLETTER_USERS_TABLE . " where ip=%s or email=%s order by updated desc limit 1", $ip, $email));

        if ($updated && time() - $updated < $this->options['antiflood']) {
            return true;
        }

        return false;
    }

    function is_spam_text($text) {
        if (stripos($text, 'http://') !== false || stripos($text, 'https://') !== false) {
            return true;
        }
        if (stripos($text, 'www.') !== false) {
            return true;
        }
        return false;
    }

    function is_spam_by_akismet($email, $name, $ip, $agent, $referrer) {
        if (empty($this->options['akismet'])) {
            return false;
        }
        if (!class_exists('Akismet')) {
            return false;
        }

        $this->logger->debug('Akismet check');
        $request = 'blog=' . urlencode(home_url()) . '&referrer=' . urlencode($referrer) .
                '&user_agent=' . urlencode($agent) .
                '&comment_type=signup' .
                '&comment_author_email=' . urlencode($email) .
                '&user_ip=' . urlencode($ip);
        if (!empty($name)) {
            $request .= '&comment_author=' . urlencode($name);
        }

        $response = Akismet::http_post($request, 'comment-check');

        if ($response && $response[1] == 'true') {
            return true;
        }
        return false;
    }

    /**
     * 
     * @global wpdb $wpdb
     * @return mixed
     */
    function hook_wp_loaded() {
        global $wpdb;

        $newsletter = Newsletter::instance();

        switch ($newsletter->action) {
            case 'profile-change':
                if ($this->antibot_form_check()) {
                    $user = $this->get_user_from_request();
                    if (!$user || $user->status != 'C') {
                        die('Subscriber not found or not active.');
                    }

                    $email = $this->get_email_from_request();
                    if (!$email) {
                        die('Newsletter not found');
                    }

                    if (isset($_REQUEST['list'])) {
                        $list_id = (int) $_REQUEST['list'];

                        // Check if the list is public
                        $list = $this->get_list($list_id);
                        if (!$list || $list->status == 0) {
                            die('Private list.');
                        }

                        $url = $_REQUEST['redirect'];

                        $this->set_user_list($user, $list_id, $_REQUEST['value']);

                        $user = $this->get_user($user->id);
                        $this->add_user_log($user, 'cta');
                        NewsletterStatistics::instance()->add_click($url, $user->id, $email->id);
                        wp_redirect($url);
                        die();
                    }
                } else {
                    $this->request_to_antibot_form('Continue');
                }

                die();

            case 'm':
            case 'message':
                include dirname(__FILE__) . '/page.php';
                die();

            // normal subscription
            case 's':
            case 'subscribe':

                $ip = $this->get_remote_ip();
                $email = $this->normalize_email($_REQUEST['ne']);
                $first_name = '';
                if (isset($_REQUEST['nn']))
                    $first_name = $this->normalize_name($_REQUEST['nn']);

                $last_name = '';
                if (isset($_REQUEST['ns']))
                    $last_name = $this->normalize_name($_REQUEST['ns']);

                $full_name = trim($first_name . ' ' . $last_name);

                $antibot_logger = new NewsletterLogger('antibot');

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $antibot_logger->fatal($email . ' - ' . $ip . ' - HTTP method invalid');
                    die('Invalid');
                }

                $captcha = !empty($this->options['captcha']);

                if (!empty($this->options['antibot_disable']) || $this->antibot_form_check($captcha)) {


                    if ($this->is_spam_text($full_name)) {
                        $antibot_logger->fatal($email . ' - ' . $ip . ' - Name with http: ' . $full_name);
                        header("HTTP/1.0 404 Not Found");
                        die();
                    }

                    // Cannot check for administrator here, too early.
                    if (true) {

                        $this->logger->debug('Subscription of: ' . $email);

                        // 404 is returned to attempt to make the bot believe the url has been changed

                        if ($this->is_missing_domain_mx($email)) {
                            $antibot_logger->fatal($email . ' - ' . $ip . ' - MX check failed');
                            header("HTTP/1.0 404 Not Found");
                            die();
                        }

                        if ($this->is_ip_blacklisted($ip)) {
                            $antibot_logger->fatal($email . ' - ' . $ip . ' - IP blacklisted');
                            header("HTTP/1.0 404 Not Found");
                            die();
                        }

                        if ($this->is_address_blacklisted($email)) {
                            $antibot_logger->fatal($email . ' - ' . $ip . ' - Address blacklisted');
                            header("HTTP/1.0 404 Not Found");
                            die();
                        }

                        // Akismet check
                        if ($this->is_spam_by_akismet($email, $full_name, $ip, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'])) {
                            $antibot_logger->fatal($email . ' - ' . $ip . ' - Akismet blocked');
                            header("HTTP/1.0 404 Not Found");
                            die();
                        }

                        // Flood check
                        if ($this->is_flood($email, $ip)) {
                            $antibot_logger->fatal($email . ' - ' . $ip . ' - Antiflood triggered');
                            header("HTTP/1.0 404 Not Found");
                            die('Too quick');
                        }

                        $user = $this->subscribe();

                        if ($user->status == 'E')
                            $this->show_message('error', $user);
                        if ($user->status == 'C')
                            $this->show_message('confirmed', $user);
                        if ($user->status == 'A')
                            $this->show_message('already_confirmed', $user);
                        if ($user->status == 'S')
                            $this->show_message('confirmation', $user);
                    }
                } else {
                    // Temporary store data
                    //$data_key =  wp_generate_password(16, false, false);
                    //set_transient('newsletter_' . $data_key, $_REQUEST, 60);
                    //$this->antibot_redirect($data_key);
                    $this->request_to_antibot_form('Subscribe', $captcha);
                }
                die();

            // AJAX subscription
            case 'ajaxsub':
                $user = $this->subscribe();
                if ($user->status == 'E')
                    $key = 'error';
                if ($user->status == 'C')
                    $key = 'confirmed';
                if ($user->status == 'A')
                    $key = 'already_confirmed';
                if ($user->status == 'S')
                    $key = 'confirmation';
                $module = NewsletterSubscription::instance();
                $message = $newsletter->replace($module->options[$key . '_text'], $user);
                if (isset($module->options[$key . '_tracking'])) {
                    $message .= $module->options[$key . '_tracking'];
                }
                echo $message;
                die();

            case 'c':
            case 'confirm':
                if ($this->antibot_form_check()) {
                    $user = $this->confirm();
                    if ($user->status == 'E') {
                        $this->show_message('error', $user->id);
                    } else {
                        setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
                        $this->show_message('confirmed', $user);
                    }
                } else {
                    $this->request_to_antibot_form('Confirm');
                }
                die();
                break;

            default:
                return;
        }
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        $newsletter = Newsletter::instance();
        $lists_options = $this->get_options('lists');
        $profile_options = $this->get_options('profile');

        if (empty($lists_options)) {
            foreach ($profile_options as $key => $value) {
                if (strpos($key, 'list_') === 0) {
                    $lists_options[$key] = $value;
                }
            }
        }

        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            // Options migration to the new set
            if (!empty($profile_options['list_' . $i]) && empty($lists_options['list_' . $i])) {
                $lists_options['list_' . $i] = $profile_options['list_' . $i];
                $lists_options['list_' . $i . '_checked'] = $profile_options['list_' . $i . '_checked'];
                $lists_options['list_' . $i . '_forced'] = $profile_options['list_' . $i . '_forced'];
            }

            if (!isset($profile_options['list_' . $i . '_forced'])) {
                $profile_options['list_' . $i . '_forced'] = empty($this->options['preferences_' . $i]) ? 0 : 1;
                $lists_options['list_' . $i . '_forced'] = empty($this->options['preferences_' . $i]) ? 0 : 1;
            }
        }

        $this->save_options($profile_options, 'profile');
        $this->save_options($lists_options, 'lists');


        $default_options = $this->get_default_options();

        if (empty($this->options['error_text'])) {
            $this->options['error_text'] = $default_options['error_text'];
            $this->save_options($this->options);
        }

        if ($this->old_version < '2.0.0') {
            if (!isset($this->options['url']) && !empty($newsletter->options['url'])) {
                $this->options['url'] = $newsletter->options['url'];
                $this->save_options($this->options);
            }

            $options_template = $this->get_options('template');
            if (empty($options_template) && isset($this->options['template'])) {
                $options_template['enabled'] = isset($this->options['template_enabled']) ? 1 : 0;
                $options_template['template'] = $this->options['template'];
                add_option('newsletter_subscription_template', $options_template, null, 'no');
            }

            if (isset($this->options['template'])) {
                unset($this->options['template']);
                unset($this->options['template_enabled']);
                $this->save_options($this->options);
            }
        }

        $this->init_options('template', false);

        global $wpdb, $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');



        $sql = "CREATE TABLE `" . $wpdb->prefix . "newsletter_user_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL DEFAULT 0,
            `ip` varchar(50) NOT NULL DEFAULT '',
            `source` varchar(50) NOT NULL DEFAULT '',
            `data` longtext,
            `created` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
          ) $charset_collate;";

        dbDelta($sql);

        return true;
    }

    function first_install() {
        
    }

    function admin_menu() {
        $this->add_menu_page('options', 'List building');
        $this->add_menu_page('antibot', 'Security');
        $this->add_admin_page('profile', 'Subscription Form');
        $this->add_admin_page('forms', 'Forms');
        $this->add_admin_page('lists', 'Lists');
        $this->add_admin_page('lists-edit', 'List edit');
        $this->add_admin_page('template', 'Template');
    }

    /**
     * This method has been redefined for compatibility with the old options naming. It would
     * be better to change them instead. The subscription options should be named
     * "newsletter_subscription" while the form field options, actually named
     * "newsletter_profile", should be renamed "newsletter_subscription_profile" (since
     * they are retrived with get_options('profile')) or "newsletter_subscription_fields" or
     * "newsletter_subscription_form".
     *
     * @param array $options
     * @param string $sub
     */
    function save_options($options, $sub = '', $autoload = null, $language = '') {
        if (empty($sub) && empty($language)) {
            // For compatibility the options are wrongly named
            return update_option('newsletter', $options, $autoload);
        }

        if (empty($sub) && !empty($language)) {
            return update_option('newsletter_' . $language, $options, $autoload);
        }

        if ($sub == 'profile') {
            if (empty($language)) {
                $this->options_profile = $options;
                return update_option('newsletter_profile', $options, $autoload);
            } else {
                return update_option('newsletter_profile_' . $language, $options, $autoload);
            }
            // For compatibility the options are wrongly named
        }

        if ($sub == 'forms') {
            // For compatibility the options are wrongly named
            return update_option('newsletter_forms', $options, $autoload);
        }

        if ($sub == 'lists') {
            $this->options_lists = $options;
        }
        return parent::save_options($options, $sub, $autoload, $language);
    }

    function get_options($sub = '', $language = '') {
        if ($sub == '') {
            // For compatibility the options are wrongly named
            if ($language) {
                $options = get_option('newsletter_' . $language, array());
                $options = array_merge(get_option('newsletter', array()), $options);
            } else {
                $options = get_option('newsletter', array());
            }
            if (!is_array($options)) {
                $options = array();
            }

            return $options;
        }
        if ($sub == 'profile') {
            if ($language) {

                $options = get_option('newsletter_profile_' . $language, array());
                $options = array_merge(get_option('newsletter_profile', array()), $options);
            } else {
                $options = get_option('newsletter_profile', array());
            }
            // For compatibility the options are wrongly named
            return $options;
        }
        if ($sub == 'forms') {
            // For compatibility the options are wrongly named
            return get_option('newsletter_forms', array());
        }
        return parent::get_options($sub, $language);
    }

    function set_updated($user, $time = 0, $ip = '') {
        global $wpdb;
        if (!$time) {
            $time = time();
        }

        if (!$ip) {
            $ip = $this->get_remote_ip();
        }
        $ip = $this->process_ip($ip);

        if (is_object($user)) {
            $id = $user->id;
        } else if (is_array($user)) {
            $id = $user['id'];
        }

        $id = (int) $id;

        $wpdb->update(NEWSLETTER_USERS_TABLE, array('updated' => $time, 'ip' => $ip), array('id' => $id));
    }

    /**
     * Return the subscribed user.
     *
     * @param bool $registration If invoked from the registration process
     * @global Newsletter $newsletter
     */
    function subscribe($status = null, $emails = true) {

        $opt_in = (int) $this->options['noconfirmation']; // 0 - double, 1 - single
        if (!empty($this->options['optin_override']) && isset($_REQUEST['optin'])) {
            switch ($_REQUEST['optin']) {
                case 'single': $opt_in = self::OPTIN_SINGLE;
                    break;
                case 'double': $opt_in = self::OPTIN_DOUBLE;
                    break;
            }
        }

        if ($status != null) {
            // If a status is forced and it is requested to be "confirmed" is like a single opt in
            // $status here can only be confirmed or not confirmed 
            // TODO: Add a check on status values
            if ($status == Newsletter::STATUS_CONFIRMED) {
                $opt_in = self::OPTIN_SINGLE;
            } else {
                $opt_in = self::OPTIN_DOUBLE;
            }
        }

        $email = $this->normalize_email(stripslashes($_REQUEST['ne']));

        // Shound never reach this point without a valid email address
        if ($email == null) {
            die('Wrong email');
        }

        $user = $this->get_user($email);

        if ($user != null) {
            // Email already registered in our database
            $this->logger->info('Subscription of an address with status ' . $user->status);

            // Bounced
            // TODO: Manage other cases when added
            if ($user->status == 'B') {
                // Non persistent status to decide which message to show (error)
                $user->status = 'E';
                return $user;
            }

            // Is there any relevant data change? If so we can proceed otherwise if repeated subscriptions are disabled
            // show an already subscribed message

            if (empty($this->options['multiple'])) {
                $user->status = 'E';
                return $user;
            }

            if ($this->options['multiple'] == 2) {
                $lists_changed = false;
                if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
                    foreach ($_REQUEST['nl'] as $list_id) {
                        $list_id = (int) $list_id;
                        if ($list_id <= 0 || $list_id > NEWSLETTER_LIST_MAX)
                            continue;
                        $field = 'list_' . $list_id;
                        if ($user->$field == 0) {
                            $lists_changed = true;
                            break;
                        }
                    }
                }

                if (!$lists_changed) {
                    $user->status = 'E';
                    return $user;
                }
            }

            // If the subscriber is confirmed, we cannot change his data in double opt in mode, we need to
            // temporary store and wait for activation
            if ($user->status == Newsletter::STATUS_CONFIRMED && $opt_in == self::OPTIN_DOUBLE) {

                set_transient($this->get_user_key($user), $_REQUEST, 3600 * 48);

                // This status is *not* stored it indicate a temporary status to show the correct messages
                $user->status = 'S';

                $this->send_message('confirmation', $user);

                return $user;
            }
        }

        // Here we have a new subscription or we can process the subscription even with a pre-existant user for example
        // because it is not confirmed
        if ($user != null) {
            $this->logger->info("Email address subscribed but not confirmed");
            $user = array('id' => $user->id);
        } else {
            $this->logger->info("New email address");
            $user = array('email' => $email);
        }

        $user = $this->update_user_from_request($user);


        $user['token'] = $this->get_token();
        $ip = $this->get_remote_ip();
        $ip = $this->process_ip($ip);
        $user['ip'] = $ip;
        $user['status'] = $opt_in == self::OPTIN_SINGLE ? Newsletter::STATUS_CONFIRMED : Newsletter::STATUS_NOT_CONFIRMED;

        $user['updated'] = time();

        $user = apply_filters('newsletter_user_subscribe', $user);

        $user = $this->save_user($user);

        $this->add_user_log($user, 'subscribe');

        // Notification to admin (only for new confirmed subscriptions)
        if ($user->status == Newsletter::STATUS_CONFIRMED) {
            do_action('newsletter_user_confirmed', $user);
            $this->notify_admin($user, 'Newsletter subscription');
            setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
        }

        if ($emails) {
            $this->send_message(($user->status == Newsletter::STATUS_CONFIRMED) ? 'confirmed' : 'confirmation', $user);
        }

        return $user;
    }

    function add_microdata($message) {
        return $message . '<span itemscope itemtype="http://schema.org/EmailMessage"><span itemprop="description" content="Email address confirmation"></span><span itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction"><meta itemprop="name" content="Confirm Subscription"><span itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler"><meta itemprop="url" content="{subscription_confirm_url}"><link itemprop="method" href="http://schema.org/HttpRequestMethod/POST"></span></span></span>';
    }

    /**
     * Processes the request and fill in the *array* representing a subscriber with submitted values
     * (filtering when necessary).
     * 
     * @param array $user An array partially filled with subscriber data
     * @return array The filled array representing a subscriber
     */
    function update_user_from_request($user) {

        if (isset($_REQUEST['nn'])) {
            $user['name'] = $this->normalize_name(stripslashes($_REQUEST['nn']));
        }
        // TODO: required checking

        if (isset($_REQUEST['ns'])) {
            $user['surname'] = $this->normalize_name(stripslashes($_REQUEST['ns']));
        }
        // TODO: required checking

        if (!empty($_REQUEST['nx'])) {
            $user['sex'] = $this->normalize_sex($_REQUEST['nx'][0]);
        }
        // TODO: valid values check

        if (isset($_REQUEST['nr'])) {
            $user['referrer'] = strip_tags(trim($_REQUEST['nr']));
        }

        $language = '';
        if (!empty($_REQUEST['nlang'])) {
            $language = strtolower(strip_tags($_REQUEST['nlang']));
            // TODO: Check if it's an allowed language code
            $user['language'] = $language;
        }

        // From the antibot form
        if (isset($_REQUEST['nhr'])) {
            $user['http_referer'] = strip_tags(trim($_REQUEST['nhr']));
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $user['http_referer'] = strip_tags(trim($_SERVER['HTTP_REFERER']));
        }

        if (strlen($user['http_referer']) > 200) {
            $user['http_referer'] = mb_substr($user['http_referer'], 0, 200);
        }

        // New profiles
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // If the profile cannot be set by  subscriber, skip it.
            if ($this->options_profile['profile_' . $i . '_status'] == 0) {
                continue;
            }
            if (isset($_REQUEST['np' . $i])) {
                $user['profile_' . $i] = trim(stripslashes($_REQUEST['np' . $i]));
            }
        }

        // Preferences (field names are nl[] and values the list number so special forms with radio button can work)
        if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
            $lists = $this->get_lists_public();
            //$this->logger->debug($_REQUEST['nl']);
            foreach ($lists as $list) {
                if (in_array('' . $list->id, $_REQUEST['nl'])) {
                    $user['list_' . $list->id] = 1;
                }
            }
        } else {
            $this->logger->debug('No lists received');
        }

        // Forced lists (general or by language)
        $lists = $this->get_lists();
        foreach ($lists as $list) {
            if ($list->forced) {
                $user['list_' . $list->id] = 1;
            }
            if (in_array($language, $list->languages)) {
                $user['list_' . $list->id] = 1;
            }
        }

        // TODO: should be removed!!!
        if (defined('NEWSLETTER_FEED_VERSION')) {
            $options_feed = get_option('newsletter_feed', array());
            if ($options_feed['add_new'] == 1) {
                $user['feed'] = 1;
            }
        }
        return $user;
    }

    /**
     * Send emails during the subscription process. Emails are themes with email.php file.
     * @global type $newsletter
     * @return type
     */
    function mail($to, $subject, $message, $language = '') {
        $options_template = $this->get_options('template', $language);

        $template = trim($options_template['template']);
        if (empty($template) || strpos($template, '{message}') === false) {
            $template = '{message}';
        }
        $message = str_replace('{message}', $message, $template);

        $headers = array('Auto-Submitted' => 'auto-generated');

        // Replaces tags from the template
        $message = $this->replace($message);
        return Newsletter::instance()->mail($to, $subject, $message, $headers);
    }

    /**
     * Confirms a subscription changing the user status and, possibly, merging the
     * temporary data if present.
     * 
     * @param int $user_id Optional. If null the user is extracted from the request.
     * @return TNP_User
     */
    function confirm($user_id = null, $emails = true) {

        if ($user_id == null) {
            $user = $this->get_user_from_request(true);
            // Is there any temporary data from a subscription to be confirmed?
            $data = get_transient($this->get_user_key($user));
            if ($data !== false) {
                $_REQUEST = $data;
                // Update the user profile since it's now confirmed
                $user = $this->update_user_from_request((array) $user);
                $user = $this->save_user($user);
                delete_transient($this->get_user_key($user));
                // Forced a fake status so the welcome email is sent
                $user->status = Newsletter::STATUS_NOT_CONFIRMED;
            }
        } else {
            $user = $this->get_user($user_id);
            if ($user == null) {
                die('No subscriber found.');
            }
        }

        $this->update_user_last_activity($user);

        setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');

        if ($user->status == TNP_User::STATUS_CONFIRMED) {
            $this->add_user_log($user, 'activate');
            do_action('newsletter_user_confirmed', $user);
            return $user;
        }

        $this->set_user_status($user, TNP_User::STATUS_CONFIRMED);

        $user = $this->get_user($user);

        $this->add_user_log($user, 'activate');

        do_action('newsletter_user_confirmed', $user);
        $this->notify_admin($user, 'Newsletter subscription');

        if ($emails) {
            $this->send_message('confirmed', $user);
        }

        return $user;
    }

    /**
     * Sends a message (activation, welcome, cancellation, ...) with the correct template 
     * and checking if the message itself is disabled
     * 
     * @param string $type
     * @param TNP_User $user
     */
    function send_message($type, $user, $force = false) {
        if (!$force && !empty($this->options[$type . '_disabled'])) {
            return true;
        }

        $options = $this->get_options('', $this->get_user_language($user));
        $message = $options[$type . '_message'];
        if ($user->status == Newsletter::STATUS_NOT_CONFIRMED) {
            $message = $this->add_microdata($message);
        }
        $subject = $options[$type . '_subject'];

        return $this->mail($user->email, $this->replace($subject, $user), $this->replace($message, $user), $this->get_user_language($user));
    }

    /**
     * Saves the subscriber data.
     * 
     * @return type
     */
    function save_profile() {
        return NewsletterProfile::instance()->save_profile();
    }

    function is_double_optin() {
        return $this->options['noconfirmation'] == 0;
    }

    /**
     * Sends the activation email without conditions.
     * 
     * @param stdClass $user
     * @return bool
     */
    function send_activation_email($user) {
        return $this->send_message('confirmation', $user, true);
    }

    /**
     * Finds the right way to show the message identified by $key (welcome, unsubscription, ...) redirecting the user to the
     * WordPress page or loading the configured url or activating the standard page.
     */
    function show_message($key, $user, $alert = '', $email = null) {
        $url = '';

        if (isset($_REQUEST['ncu'])) {
            // Custom URL from the form
            $url = $_REQUEST['ncu'];
        } else {
            // Per message custom URL from configuration (language variants could not be supported)
            $options = $this->get_options('', $this->get_user_language($user));
            if (!empty($options[$key . '_url'])) {
                $url = $options[$key . '_url'];
            }
        }

        $url = Newsletter::instance()->build_message_url($url, $key, $user, $email, $alert);
        wp_redirect($url);

        die();
    }

    function get_message_key_from_request() {
        if (empty($_GET['nm'])) {
            return 'subscription';
        }
        $key = $_GET['nm'];
        switch ($key) {
            case 's': return 'confirmation';
            case 'c': return 'confirmed';
            case 'u': return 'unsubscription';
            case 'uc': return 'unsubscribed';
            case 'p':
            case 'pe':
                return 'profile';
            default: return $key;
        }
    }

    var $privacy_url = false;

    function get_privacy_url() {
        if (!$this->privacy_url) {
            if (!empty($this->options_profile['privacy_use_wp_url']) && function_exists('get_privacy_policy_url')) {
                $this->privacy_url = get_privacy_policy_url();
            } else {
                $this->privacy_url = $this->options_profile['privacy_url'];
            }
        }
        return $this->privacy_url;
    }

    function get_form_javascript() {

        $buffer = "\n\n";
        $buffer .= '<script type="text/javascript">' . "\n";
        $buffer .= '//<![CDATA[' . "\n";
        $buffer .= 'if (typeof newsletter_check !== "function") {' . "\n";
        $buffer .= 'window.newsletter_check = function (f) {' . "\n";
        $buffer .= '    var re = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-]{1,})+\.)+([a-zA-Z0-9]{2,})+$/;' . "\n";
        $buffer .= '    if (!re.test(f.elements["ne"].value)) {' . "\n";
        $buffer .= '        alert("' . addslashes($this->options_profile['email_error']) . '");' . "\n";
        $buffer .= '        return false;' . "\n";
        $buffer .= '    }' . "\n";
        if ($this->options_profile['name_status'] == 2 && $this->options_profile['name_rules'] == 1) {
            $buffer .= '    if (f.elements["nn"] && (f.elements["nn"].value == "" || f.elements["nn"].value == f.elements["nn"].defaultValue)) {' . "\n";
            $buffer .= '        alert("' . addslashes($this->options_profile['name_error']) . '");' . "\n";
            $buffer .= '        return false;' . "\n";
            $buffer .= '    }' . "\n";
        }
        if ($this->options_profile['surname_status'] == 2 && $this->options_profile['surname_rules'] == 1) {
            $buffer .= '    if (f.elements["ns"] && (f.elements["ns"].value == "" || f.elements["ns"].value == f.elements["ns"].defaultValue)) {' . "\n";
            $buffer .= '        alert("' . addslashes($this->options_profile['surname_error']) . '");' . "\n";
            $buffer .= '        return false;' . "\n";
            $buffer .= '    }' . "\n";
        }
        $buffer .= '    for (var i=1; i<' . NEWSLETTER_PROFILE_MAX . '; i++) {' . "\n";
        $buffer .= '    if (f.elements["np" + i] && f.elements["np" + i].required && f.elements["np" + i].value == "") {' . "\n";
        $buffer .= '        alert("' . addslashes($this->options_profile['profile_error']) . '");' . "\n";
        $buffer .= '        return false;' . "\n";
        $buffer .= '    }' . "\n";
        $buffer .= '    }' . "\n";

        $buffer .= '    if (f.elements["ny"] && !f.elements["ny"].checked) {' . "\n";
        $buffer .= '        alert("' . addslashes($this->options_profile['privacy_error']) . '");' . "\n";
        $buffer .= '        return false;' . "\n";
        $buffer .= '    }' . "\n";
        $buffer .= '    return true;' . "\n";
        $buffer .= '}' . "\n";
        $buffer .= '}' . "\n";
        $buffer .= '//]]>' . "\n";
        $buffer .= '</script>' . "\n\n";
        return $buffer;
    }

    function shortcode_subscription($attrs, $content) {
        if (!is_array($attrs)) {
            $attrs = array();
        }

        $attrs = array_merge(array('class' => 'newsletter', 'style' => ''), $attrs);

        $action = esc_attr($this->build_action_url('s'));
        $class = esc_attr($attrs['class']);
        $style = esc_attr($attrs['style']);
        $buffer = '<form method="post" action="' . $action . '" class="' . $class . '" style="' . $style . '">' . "\n";

        if (isset($attrs['referrer'])) {
            $buffer .= '<input type="hidden" name="nr" value="' . esc_attr($referrer) . '">' . "\n";
        }

        if (isset($attrs['confirmation_url'])) {
            $buffer .= "<input type='hidden' name='ncu' value='" . esc_attr($attrs['confirmation_url']) . "'>\n";
        }

        if (isset($attrs['list'])) {
            $arr = explode(',', $attrs['list']);
            foreach ($arr as $a) {
                $buffer .= "<input type='hidden' name='nl[]' value='" . esc_attr(trim($a)) . "'>\n";
            }
        }

        //$content = str_replace("\r\n", "", $content);
        $buffer .= do_shortcode($content);

        if (isset($attrs['button_label'])) {
            $label = $attrs['button_label'];
        } else {
            $label = $this->options_profile['subscribe'];
        }

        if (!empty($label)) {
            $buffer .= '<div class="tnp-field tnp-field-button">';
            if (strpos($label, 'http') === 0) {
                $buffer .= '<input class="tnp-button-image" type="image" src="' . $label . '">';
            } else {
                $buffer .= '<input class="tnp-button" type="submit" value="' . $label . '">';
            }
            $buffer .= '</div>';
        }

        $buffer .= '</form>';

        return $buffer;
    }

    function _shortcode_label($name, $attrs, $suffix = null) {
        if (!$suffix) {
            $suffix = $name;
        }
        $buffer = '<label for="tnp-' . $suffix . '">';
        if (isset($attrs['label'])) {
            if (empty($attrs['label'])) {
                return;
            } else {
                $buffer .= esc_html($attrs['label']);
            }
        } else {
            $buffer .= esc_html($this->options_profile[$name]);
        }
        $buffer .= "</label>\n";
        return $buffer;
    }

    function shortcode_newsletter_field($attrs, $content) {
        $name = $attrs['name'];

        $buffer = '';

        if ($name == 'email') {
            $buffer .= '<div class="tnp-field tnp-field-email">';
            $buffer .= $this->_shortcode_label('email', $attrs);

            $buffer .= '<input class="tnp-email" type="email" name="ne" value=""';
            if (isset($attrs['placeholder']))
                $buffer .= ' placeholder="' . esc_attr($attrs['placeholder']) . '"';
            $buffer .= 'required>';
            if (isset($attrs['button_label'])) {
                $label = $attrs['button_label'];
                if (strpos($label, 'http') === 0) {
                    $buffer .= ' <input class="tnp-submit-image" type="image" src="' . esc_attr(esc_url_raw($label)) . '">';
                } else {
                    $buffer .= ' <input class="tnp-submit" type="submit" value="' . esc_attr($label) . '" style="width: 29%">';
                }
            }
            $buffer .= "</div>\n";
            return $buffer;
        }

        if ($name == 'first_name' || $name == 'name') {
            $buffer .= '<div class="tnp-field tnp-field-firstname">';
            $buffer .= $this->_shortcode_label('name', $attrs);

            $buffer .= '<input class="tnp-name" type="text" name="nn" value=""';
            if (isset($attrs['placeholder']))
                $buffer .= ' placeholder="' . esc_attr($attrs['placeholder']) . '"';
            if ($this->options_profile['name_rules'] == 1) {
                $buffer .= ' required';
            }
            $buffer .= '>';
            $buffer .= "</div>\n";
            return $buffer;
        }

        if ($name == 'last_name' || $name == 'surname') {
            $buffer .= '<div class="tnp-field tnp-field-surname">';
            $buffer .= $this->_shortcode_label('surname', $attrs);

            $buffer .= '<input class="tnp-surname" type="text" name="ns" value=""';
            if (isset($attrs['placeholder']))
                $buffer .= ' placeholder="' . esc_attr($attrs['placeholder']) . '"';
            if ($this->options_profile['surname_rules'] == 1) {
                $buffer .= ' required';
            }
            $buffer .= '>';
            $buffer .= '</div>';
            return $buffer;
        }

        if ($name == 'preference' || $name == 'list') {
            $list = $this->get_list($attrs['number']);
            if (!$list || $list->status == 0 || $list->forced) {
                return;
            }
            if (isset($attrs['hidden'])) {
                return '<input type="hidden" name="nl[]" value="' . esc_attr($list->id) . '">';
            }
            $buffer .= '<div class="tnp-field tnp-field-checkbox tnp-field-list"><label for="nl' . esc_attr($list->id) . '">';
            $buffer .= '<input type="checkbox" id="nl' . esc_attr($list->id) . '" name="nl[]" value="' . esc_attr($list->id) . '"';
            if (isset($attrs['checked'])) {
                $buffer .= ' checked';
            }
            $buffer .= '>';
            if (isset($attrs['label'])) {
                if ($attrs['label'] != '')
                    $buffer .= '&nbsp;' . esc_html($attrs['label']) . '</label>';
            } else {
                $buffer .= '&nbsp;' . esc_html($list->name) . '</label>';
            }
            $buffer .= "</div>\n";

            return $buffer;
        }

        // All the lists
        if ($name == 'lists' || $name == 'preferences') {
            $tmp = '';
            $lists = $this->get_lists();
            foreach ($lists as $list) {
                if ($list->status != 2 || $list->forced) {
                    continue;
                }
                //die('ddd');
                $tmp .= '<div class="tnp-field tnp-field-checkbox tnp-field-list"><label for="nl' . $list->id . '">';
                $tmp .= '<input type="checkbox" id="nl' . $list->id . '" name="nl[]" value="' . $list->id . '"';
                if ($list->checked)
                    $tmp .= ' checked';
                $tmp .= '>&nbsp;' . esc_html($list->name) . '</label>';
                $tmp .= "</div>\n";
            }
            return $tmp;
        }

        // TODO: add the "not specified"
        if ($name == 'sex' || $name == 'gender') {
            $buffer .= '<div class="tnp-field tnp-field-gender">';
            if (isset($attrs['label'])) {
                if ($attrs['label'] != '')
                    $buffer .= '<label for="">' . esc_html($attrs['label']) . '</label>';
            } else {
                $buffer .= '<label for="">' . esc_html($this->options_profile['sex']) . '</label>';
            }

            $buffer .= '<select name="nx" class="tnp-gender">';
            $buffer .= '<option value="m">' . esc_html($this->options_profile['sex_male']) . '</option>';
            $buffer .= '<option value="f">' . esc_html($this->options_profile['sex_female']) . '</option>';
            $buffer .= '</select>';
            $buffer .= "</div>\n";
            return $buffer;
        }

        if ($name == 'profile' && isset($attrs['number'])) {
            $number = (int) $attrs['number'];
            $type = $this->options_profile['profile_' . $number . '_type'];
            $size = isset($attrs['size']) ? $attrs['size'] : '';
            $buffer .= '<div class="tnp-field tnp-field-profile">';
            if (isset($attrs['label'])) {
                if ($attrs['label'] != '') {
                    $buffer .= '<label>' . esc_html($attrs['label']) . '</label>';
                }
            } else {
                $buffer .= '<label>' . esc_html($this->options_profile['profile_' . $number]) . '</label>';
            }
            $placeholder = isset($attrs['placeholder']) ? $attrs['placeholder'] : $this->options_profile['profile_' . $number . '_placeholder'];

            $required = $this->options_profile['profile_' . $number . '_rules'] == 1;

            // Text field
            if ($type == 'text') {
                $buffer .= '<input class="tnp-profile tnp-profile-' . $number . '" type="text" size="' . esc_attr($size) . '" name="np' . $number . '" placeholder="' . esc_attr($placeholder) . '"';
                if ($required) {
                    $buffer .= ' required';
                }
                $buffer .= '>';
            }

            // Select field
            if ($type == 'select') {
                $buffer .= '<select class="tnp-profile tnp-profile-' . $number . '" name="np' . $number . '"';
                if ($required) {
                    $buffer .= ' required';
                }
                $buffer .= '>';
                if (!empty($placeholder)) {
                    $buffer .= '<option value="">' . esc_html($placeholder) . '</option>';
                }
                $opts = explode(',', $this->options_profile['profile_' . $number . '_options']);
                for ($j = 0; $j < count($opts); $j++) {
                    $buffer .= '<option>' . esc_html(trim($opts[$j])) . '</option>';
                }
                $buffer .= "</select>\n";
            }

            $buffer .= "</div>\n";

            return $buffer;
        }

        if (strpos($name, 'privacy') === 0) {

            if (!isset($attrs['url'])) {
                $attrs['url'] = $this->options_profile['privacy_url'];
            }

            if (!isset($attrs['label'])) {
                $attrs['label'] = $this->options_profile['privacy_label'];
            }

            $buffer .= '<div class="tnp-field tnp-field-checkbox tnp-field-privacy">';

            $buffer .= '<input type="checkbox" name="ny" required class="tnp-privacy" id="tnp-privacy"> ';
            $buffer .= '<label for="tnp-privacy">';
            if (!empty($attrs['url'])) {
                $buffer .= '<a target="_blank" href="' . esc_attr($attrs['url']) . '">';
            }
            $buffer .= $attrs['label'];
            if (!empty($attrs['url'])) {
                $buffer .= '</a>';
            }
            $buffer .= '</label>';
            $buffer .= '</div>';

            return $buffer;
        }
    }

    /**
     * Returns the form html code for subscription.
     *
     * @return string The html code of the subscription form
     */
    function get_subscription_form_html5($referrer = null, $action = null, $attrs = array()) {
        return $this->get_subscription_form($referrer, $action, $attrs);
    }

    function get_privacy_field() {
        $options_profile = $this->get_options('profile', $this->get_current_language());
        $privacy_status = (int) $options_profile['privacy_status'];
        $buffer = '<label>';
        if ($privacy_status === 1) {
            $buffer .= '<input type="checkbox" name="ny" required class="tnp-privacy">&nbsp;';
        }
        $url = $this->get_privacy_url();
        if (!empty($url)) {
            $buffer .= '<a target="_blank" href="' . esc_attr($url) . '">';
            $buffer .= esc_attr($options_profile['privacy']) . '</a>';
        } else {
            $buffer .= esc_html($options_profile['privacy']);
        }

        $buffer .= "</label>";

        return $buffer;
    }

    /**
     * The new standard form.
     * 
     * @param type $referrer
     * @param type $action
     * @param type $attrs
     * @return string
     */
    function get_subscription_form($referrer = null, $action = null, $attrs = array()) {
        $language = $this->get_current_language();
        $options_profile = $this->get_options('profile', $language);


        if (isset($attrs['action'])) {
            $action = $attrs['action'];
        }
        if (isset($attrs['referrer'])) {
            $referrer = $attrs['referrer'];
        }

        $buffer = '';



        if (empty($action)) {
            $action = $this->build_action_url('s');
        }

        if ($referrer != 'widget') {
            if (isset($attrs['class'])) {
                $buffer .= '<div class="tnp tnp-subscription ' . $attrs['class'] . '">' . "\n";
            } else {
                $buffer .= '<div class="tnp tnp-subscription">' . "\n";
            }
        }
        $buffer .= '<form method="post" action="' . esc_attr($action) . '" onsubmit="return newsletter_check(this)">' . "\n\n";

        $buffer .= '<input type="hidden" name="nlang" value="' . esc_attr($language) . '">' . "\n";

        if (!empty($referrer)) {
            $buffer .= '<input type="hidden" name="nr" value="' . esc_attr($referrer) . '">' . "\n";
        }
        if (isset($attrs['confirmation_url'])) {
            $buffer .= "<input type='hidden' name='ncu' value='" . esc_attr($attrs['confirmation_url']) . "'>\n";
        }

        // Compatibility
        if (isset($attrs['list'])) {
            $attrs['lists'] = $attrs['list'];
        }

        // Hidden lists
        if (isset($attrs['lists'])) {
            $arr = explode(',', $attrs['lists']);
            foreach ($arr as $a) {
                $buffer .= "<input type='hidden' name='nl[]' value='" . ((int) trim($a)) . "'>\n";
            }
        }

        if ($options_profile['name_status'] == 2) {
            $buffer .= '<div class="tnp-field tnp-field-firstname"><label>' . esc_html($options_profile['name']) . '</label>';
            $buffer .= '<input class="tnp-firstname" type="text" name="nn" ' . ($options_profile['name_rules'] == 1 ? 'required' : '') . '></div>';
            $buffer .= "\n";
        }

        if ($options_profile['surname_status'] == 2) {
            $buffer .= '<div class="tnp-field tnp-field-lastname"><label>' . esc_html($options_profile['surname']) . '</label>';
            $buffer .= '<input class="tnp-lastname" type="text" name="ns" ' . ($options_profile['surname_rules'] == 1 ? 'required' : '') . '></div>';
            $buffer .= "\n";
        }

        $buffer .= '<div class="tnp-field tnp-field-email"><label>' . esc_html($options_profile['email']) . '</label>';
        $buffer .= '<input class="tnp-email" type="email" name="ne" required></div>';
        $buffer .= "\n";

        if (isset($options_profile['sex_status']) && $options_profile['sex_status'] == 2) {
            $buffer .= '<div class="tnp-field tnp-field-gender"><label>' . esc_html($options_profile['sex']) . '</label>';
            $buffer .= '<select name="nx" class="tnp-gender"';
            if ($options_profile['sex_rules'] == 1) {
                $buffer .= ' required><option value=""></option>';
            } else {
                $buffer .= '><option value="n">' . esc_html($options_profile['sex_none']) . '</option>';
            }
            $buffer .= '<option value="m">' . esc_html($options_profile['sex_male']) . '</option>';
            $buffer .= '<option value="f">' . esc_html($options_profile['sex_female']) . '</option>';
            $buffer .= '</select></div>';
            $buffer .= "\n";
        }

        $tmp = '';
        $lists = $this->get_lists_for_subscription($language);
        if (!empty($attrs['lists_field_layout']) && $attrs['lists_field_layout'] == 'dropdown') {
            foreach ($lists as $list) {

                $tmp .= '<option value="' . $list->id . '"';
                if ($list->checked) {
                    $tmp .= ' selected';
                }
                $tmp .= '>' . esc_html($list->name) . '</option>';
                $tmp .= "\n";
            }
            if (!empty($attrs['lists_field_empty_label'])) {
                $tmp = '<option value="">' . $attrs['lists_field_empty_label'] . '</option>' . $tmp;
            }
            if (!empty($tmp)) {
                $tmp = '<select class="tnp-lists" name="nl[]" required>' . $tmp . '</select>';
            }
            if (!empty($tmp)) {
                $buffer .= '<div class="tnp-field tnp-lists">';
                if (!empty($attrs['lists_field_label'])) {
                    $buffer .= '<label>' . $attrs['lists_field_label'] . '</label>';
                }
                $buffer .= $tmp . '</div>';
            }
        } else {

            foreach ($lists as $list) {

                $tmp .= '<div class="tnp-field tnp-field-list"><label><input class="tnp-preference" type="checkbox" name="nl[]" value="' . $list->id . '"';
                if ($list->checked) {
                    $tmp .= ' checked';
                }
                $tmp .= '/>&nbsp;' . esc_html($list->name) . '</label></div>';
            }
            if (!empty($tmp)) {
                $buffer .= '<div class="tnp-lists">';
                if (!empty($attrs['lists_field_label'])) {
                    $buffer .= '<label>' . $attrs['lists_field_label'] . '</label>';
                }
                $buffer .= $tmp . '</div>';
            }
        }

        // Extra profile fields
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // Not for subscription form
            if ($options_profile['profile_' . $i . '_status'] != 2) {
                continue;
            }


            $buffer .= '<div class="tnp-field tnp-field-profile"><label>' .
                    esc_html($options_profile['profile_' . $i]) . '</label>';

            // Text field                
            if ($options_profile['profile_' . $i . '_type'] == 'text') {
                $buffer .= '<input class="tnp-profile tnp-profile-' . $i . '" type="text"' . ($options_profile['profile_' . $i . '_rules'] == 1 ? ' required' : '') . ' name="np' . $i . '">';
            }

            // Select field
            if ($options_profile['profile_' . $i . '_type'] == 'select') {
                $buffer .= '<select class="tnp-profile tnp-profile-' . $i . '" name="np' . $i . '" required>' . "\n";
                $buffer .= "<option></option>\n";
                $opts = explode(',', $options_profile['profile_' . $i . '_options']);
                for ($j = 0; $j < count($opts); $j++) {
                    $buffer .= "<option>" . esc_html(trim($opts[$j])) . "</option>\n";
                }
                $buffer .= "</select>\n";
            }
            $buffer .= '</div>';
        }

        $extra = apply_filters('newsletter_subscription_extra', array());
        foreach ($extra as $x) {
            $label = $x['label'];
            if (empty($label)) {
                $label = '&nbsp;';
            }
            $name = '';
            if (!empty($x['name'])) {
                $name = $x['name'];
            }
            $buffer .= '<div class="tnp-field tnp-field-' . $name . '"><label>' . $label . "</label>";
            $buffer .= $x['field'] . "</div>\n";
        }

        $privacy_status = (int) $options_profile['privacy_status'];

        if ($privacy_status === 1 || $privacy_status === 2) {
            $buffer .= '<div class="tnp-field tnp-field-privacy">';
            $buffer .= '<label>';
            if ($privacy_status === 1) {
                $buffer .= '<input type="checkbox" name="ny" required class="tnp-privacy">&nbsp;';
            }
            $url = $this->get_privacy_url();
            if (!empty($url)) {
                $buffer .= '<a target="_blank" href="' . esc_attr($url) . '">';
                $buffer .= esc_attr($options_profile['privacy']) . '</a>';
            } else {
                $buffer .= esc_html($options_profile['privacy']);
            }

            $buffer .= "</label></div>\n";
        }

        $buffer .= '<div class="tnp-field tnp-field-button">';

        if (strpos($options_profile['subscribe'], 'http') === 0) {
            $buffer .= '<input class="tnp-submit-image" type="image" src="' . esc_attr($options_profile['subscribe']) . '">' . "\n";
        } else {
            $buffer .= '<input class="tnp-submit" type="submit" value="' . esc_attr($options_profile['subscribe']) . '">' . "\n";
        }

        $buffer .= "</div>\n</form>\n";
        if ($referrer != 'widget') {
            $buffer .= "</div>\n";
        }
        return $buffer;
    }

    function get_profile_form($user) {
        return NewsletterProfile::instance()->get_profile_form($user);
    }

    function get_form($number) {
        $options = get_option('newsletter_forms');

        $form = $options['form_' . $number];

        $form = do_shortcode($form);

        $action = $this->build_action_url('s');

        if (stripos($form, '<form') === false) {
            $form = '<form method="post" action="' . $action . '">' . $form . '</form>';
        }

        // For compatibility
        $form = str_replace('{newsletter_url}', $action, $form);

        $form = $this->replace_lists($form);

        return $form;
    }

    /** Replaces on passed text the special tag {lists} that can be used to show the preferences as a list of checkbox.
     * They are called lists but on configuration panel they are named preferences!
     *
     * @param string $buffer
     * @return string
     */
    function replace_lists($buffer) {
        $lists = '';
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if ($this->options_profile['list_' . $i . '_status'] != 2)
                continue;
            $lists .= '<input type="checkbox" name="nl[]" value="' . $i . '"/>&nbsp;' . $this->options_profile['list_' . $i] . '<br />';
        }
        $buffer = str_replace('{lists}', $lists, $buffer);
        $buffer = str_replace('{preferences}', $lists, $buffer);
        return $buffer;
    }

    function notify_admin($user, $subject) {

        if (empty($this->options['notify'])) {
            return;
        }

        $message = "Subscriber details:\n\n" .
                "email: " . $user->email . "\n" .
                "first name: " . $user->name . "\n" .
                "last name: " . $user->surname . "\n" .
                "gender: " . $user->sex . "\n";

        for ($i = 0; $i < NEWSLETTER_LIST_MAX; $i++) {
            if (empty($this->options_profile['list_' . $i])) {
                continue;
            }
            $field = 'list_' . $i;
            $message .= $this->options_profile['list_' . $i] . ': ' . (empty($user->$field) ? "NO" : "YES") . "\n";
        }

        for ($i = 0; $i < NEWSLETTER_PROFILE_MAX; $i++) {
            if (empty($this->options_profile['profile_' . $i])) {
                continue;
            }
            $field = 'profile_' . $i;
            $message .= $this->options_profile['profile_' . $i] . ': ' . $user->$field . "\n";
        }



        $message .= "token: " . $user->token . "\n" .
                "status: " . $user->status . "\n";
        $email = trim($this->options['notify_email']);
        if (empty($email)) {
            $email = get_option('admin_email');
        }
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        Newsletter::instance()->mail($email, '[' . $blogname . '] ' . $subject, array('text' => $message));
    }

    function get_subscription_form_minimal($attrs) {
        $language = $this->get_current_language();
        if (!is_array($attrs)) {
            $attrs = array();
        }
        $options_profile = $this->get_options('profile', $language);
        $attrs = array_merge(array('class' => '', 'referrer' => 'minimal', 'button' => $options_profile['subscribe'], 'placeholder' => $options_profile['email']), $attrs);

        $form = '';
        $form .= '<div class="tnp tnp-subscription-minimal ' . $attrs['class'] . '">';
        $form .= '<form action="' . esc_attr($this->build_action_url('s')) . '" method="post">';
        if (isset($attrs['lists'])) {
            $arr = explode(',', $attrs['lists']);
            foreach ($arr as $a) {
                $form .= "<input type='hidden' name='nl[]' value='" . ((int) trim($a)) . "'>\n";
            }
        }
        $form .= '<input type="hidden" name="nr" value="' . esc_attr($attrs['referrer']) . '">';
        $form .= '<input type="hidden" name="nlang" value="' . esc_attr($language) . '">' . "\n";
        $form .= '<input class="tnp-email" type="email" required name="ne" value="" placeholder="' . esc_attr($attrs['placeholder']) . '">';
        $form .= '<input class="tnp-submit" type="submit" value="' . esc_attr($attrs['button']) . '">';
        if (!empty($this->options_profile['privacy_status'])) {
            $form .= '<div class="tnp-privacy-field">' . $this->get_privacy_field() . '</div>';
        }
        $form .= "</form></div>\n";

        return $form;
    }

    function shortcode_newsletter_form($attrs, $content) {

        if (isset($attrs['type']) && $attrs['type'] == 'minimal') {
            return NewsletterSubscription::instance()->get_subscription_form_minimal($attrs);
        }

        if (!empty($content)) {
            return NewsletterSubscription::instance()->shortcode_subscription($attrs, $content);
        }
        if (isset($attrs['form'])) {
            return NewsletterSubscription::instance()->get_form((int) $attrs['form']);
        } else if (isset($attrs['number'])) {
            return NewsletterSubscription::instance()->get_form((int) $attrs['number']);
        } else {
            if (isset($attrs['layout']) && $attrs['layout'] == 'table') {
                return NewsletterSubscription::instance()->get_subscription_form(null, null, $attrs);
            } else {
                return NewsletterSubscription::instance()->get_subscription_form_html5(null, null, $attrs);
            }
        }
    }

    /**
     *
     * @global wpdb $wpdb
     * @param array $attrs
     * @param string $content
     * @return string
     */
    function shortcode_newsletter($attrs, $content) {
        global $wpdb;

        $user = $this->get_user_from_request();
        $message_key = $this->get_message_key_from_request();

        $message = apply_filters('newsletter_page_text', '', $message_key, $user);

        $options = $this->get_options('', $this->get_user_language($user));

        if (empty($message)) {
            $message = $options[$message_key . '_text'];

            // TODO: the if can be removed
            if ($message_key == 'confirmed') {
                $message .= $options[$message_key . '_tracking'];
            }
        }


        // Now check what form must be added
        if ($message_key == 'subscription') {

            // Compatibility check
            if (stripos($message, '<form') !== false) {
                $message .= $this->get_form_javascript();
                $message = str_ireplace('<form', '<form method="post" action="' . esc_attr($this->get_subscribe_url()) . '" onsubmit="return newsletter_check(this)"', $message);
            } else {

                if (strpos($message, '{subscription_form') === false) {
                    $message .= '{subscription_form}';
                }

                if (isset($attrs['form'])) {
                    $message = str_replace('{subscription_form}', $this->get_form($attrs['form']), $message);
                } else {
                    $message = str_replace('{subscription_form}', $this->get_subscription_form('page'), $message);
                }
            }
        }

        $email = NewsletterSubscription::instance()->get_email_from_request();

        $message = $this->replace($message, $user, $email, 'page');

        $message = do_shortcode($message);

        if (isset($_REQUEST['alert'])) {
            // slashes are already added by wordpress!
            $message .= '<script>alert("' . strip_tags($_REQUEST['alert']) . '");</script>';
        }

        return $message;
    }

}

NewsletterSubscription::instance();

// Compatibility code

function newsletter_form($number = null) {
    if ($number != null) {
        echo NewsletterSubscription::instance()->get_form($number);
    } else {
        echo NewsletterSubscription::instance()->get_subscription_form();
    }
}
