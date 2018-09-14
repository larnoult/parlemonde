<?php

defined('ABSPATH') || exit;

/**
 * @property int $id The list unique identifier
 * @property string $name The list name
 * @property bool $forced If the list must be added to every new subscriber
 * @property int $status When and how the list is visible to the subscriber - see constants
 * @property bool $checked If it must be pre-checked on subscription form
 * @property array $languages The list of language used to pre-assign this list
 * */
abstract class TNP_List {

    const STATUS_PRIVATE = 0;
    const STATUS_PUBLIC = 2;
    const STATUS_PROFILE_ONLY = 1;
    const STATUS_HIDDEN = 3; // Public but never show (can be set with a hidden form field)

}

/**
 * @property int $id The subscriber unique identifier
 * @property string $email The subscriber email
 * @property string $name The subscriber name or first name
 * @property string $surname The subscriber last name
 * @property string $status The subscriber status
 * @property string $language The subscriber language code 2 chars lowercase
 */
abstract class TNP_User {

    const STATUS_CONFIRMED = 'C';
    const STATUS_NOT_CONFIRMED = 'S';
    const STATUS_UNSUBSCRIBED = 'U';
    const STATUS_BOUNCED = 'B';

}

/**
 * @property int $id The subscriber unique identifier
 * @property string $subject The subscriber email
 * @property string $message The subscriber name or first name
 * @property string $track The subscriber last name
 * @property array $options The subscriber status
 * */
abstract class TNP_Email {
    
}

class NewsletterModule {

    /**
     * @var NewsletterLogger
     */
    var $logger;

    /**
     * @var NewsletterStore
     */
    var $store;

    /**
     * The main module options
     * @var array
     */
    var $options;

    /**
     * @var string The module name
     */
    var $module;

    /**
     * The module version
     * @var string
     */
    var $version;
    var $old_version;
    var $module_id;
    var $available_version;

    /**
     * Prefix for all options stored on WordPress options table.
     * @var string
     */
    var $prefix;

    /**
     * @var NewsletterThemes
     */
    var $themes;
    var $components;

    function __construct($module, $version, $module_id = null, $components = array()) {
        $this->module = $module;
        $this->version = $version;
        $this->module_id = $module_id;
        $this->prefix = 'newsletter_' . $module;
        array_unshift($components, '');
        $this->components = $components;


        $this->logger = new NewsletterLogger($module);
        $this->options = $this->get_options();
        $this->store = NewsletterStore::singleton();

        //$this->logger->debug($module . ' constructed');
        // Version check
        if (is_admin()) {
            $this->old_version = get_option($this->prefix . '_version', '0.0.0');

            if ($this->old_version == '0.0.0') {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                $this->first_install();
                update_option($this->prefix . "_first_install_time", time(), FALSE);
            }

            if (strcmp($this->old_version, $this->version) != 0) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                $this->logger->info('Version changed from ' . $this->old_version . ' to ' . $this->version);
                // Do all the stuff for this version change
                $this->upgrade();
                update_option($this->prefix . '_version', $this->version);
            }

            add_action('admin_menu', array($this, 'admin_menu'));
            $this->available_version = get_option($this->prefix . '_available_version');
        }
    }

    /**
     * 
     * @global wpdb $wpdb
     * @param string $query
     */
    function query($query) {
        global $wpdb;

        $this->logger->debug($query);
        $r = $wpdb->query($query);
        if ($r === false) {
            $this->logger->fatal($wpdb->last_error);
        }
    }

    /**
     * 
     * @global wpdb $wpdb
     * @param string $table
     * @param array $data
     */
    function insert($table, $data) {
        global $wpdb;
        $this->logger->debug("inserting into table $table");
        $r = $wpdb->insert($table, $data);
        if ($r === false) {
            $this->logger->fatal($wpdb->last_error);
        }
    }

    function first_install() {
        $this->logger->debug('First install');
    }

    /**
     * Does a basic upgrade work, checking if the options is already present and if not (first
     * installation), recovering the defaults, saving them on database and initializing the
     * internal $options.
     */
    function upgrade() {
        foreach ($this->components as $component) {
            $this->logger->debug('Upgrading component ' . $component);
            $this->init_options($component);
        }
    }

    function init_options($component = '', $autoload = true) {
        global $wpdb;
        $default_options = $this->get_default_options($component);
        $options = $this->get_options($component);
        $options = array_merge($default_options, $options);
        $this->save_options($options, $component, $autoload);
    }

    function upgrade_query($query) {
        global $wpdb, $charset_collate;

        $this->logger->info('upgrade_query> Executing ' . $query);
        $suppress_errors = $wpdb->suppress_errors(true);
        $wpdb->query($query);
        if ($wpdb->last_error) {
            $this->logger->debug($wpdb->last_error);
        }
        $wpdb->suppress_errors($suppress_errors);
    }

    /**
     * Kept for compatibility.
     */
    static function get_available_version($module_id, $force = false) {
        return '';
    }

    /**
     * Kept for compatibility.
     */
    function new_version_available($force = false) {
        return false;
    }

    /** Returns a prefix to be used for option names and other things which need to be uniquely named. The parameter
     * "sub" should be used when a sub name is needed for another set of options or like.
     *
     * @param string $sub
     * @return string The prefix for names
     */
    function get_prefix($sub = '', $language = '') {
        return $this->prefix . (!empty($sub) ? '_' : '') . $sub . (!empty($language) ? '_' : '') . $language;
    }

    /**
     * Returns the options of a module, if not found an empty array.
     */
    function get_options($sub = '', $language = '') {
        $options = get_option($this->get_prefix($sub, $language), array());
        // Protection against scarmled database...
        if (!is_array($options)) {
            $options = array();
        }
        if ($language) {
            $main_options = get_option($this->get_prefix($sub));
            // Protection against scarmled database...
            if (!is_array($main_options))
                $main_options = array();
            //$options = array_merge($main_options, array_filter($options));
            $options = array_merge($main_options, $options);
        }
        return $options;
    }

    function get_default_options($sub = '') {
        if (!empty($sub)) {
            $sub = '-' . $sub;
        }
        $file = NEWSLETTER_DIR . '/' . $this->module . '/defaults' . $sub . '.php';
        if (file_exists($file)) {
            @include $file;
        }

        if (!isset($options) || !is_array($options)) {
            return array();
        }
        return $options;
    }

    function reset_options($sub = '') {
        $this->save_options(array_merge($this->get_options($sub), $this->get_default_options($sub)), $sub);
        return $this->get_options($sub);
    }

    /**
     * Saves the module options (or eventually a subset names as per parameter $sub). $options
     * should be an array (even if it can work with non array options.
     * The internal module options variable IS initialized with those new options only for the main
     * options (empty $sub parameter).
     * If the options contain a "theme" value, the theme-related options contained are saved as well
     * (used by some modules).
     *
     * @param array $options
     * @param string $sub
     */
    function save_options($options, $sub = '', $autoload = null, $language = '') {
        update_option($this->get_prefix($sub, $language), $options, $autoload);
        if (empty($sub) && empty($language)) {
            $this->options = $options;
            if (isset($this->themes) && isset($options['theme'])) {
                $this->themes->save_options($options['theme'], $options);
            }
        }
    }

    function delete_options($sub = '') {
        delete_option($this->get_prefix($sub));
        if (empty($sub)) {
            $this->options = array();
        }
    }

    function merge_options($options, $sub = '', $language = '') {
        if (!is_array($options))
            $options = array();
        $old_options = $this->get_options($sub, $language);
        $this->save_options(array_merge($old_options, $options), $sub, null, $language);
    }

    function backup_options($sub) {
        $options = $this->get_options($sub);
        update_option($this->get_prefix($sub) . '_backup', $options, false);
    }

    function get_last_run($sub = '') {
        return get_option($this->get_prefix($sub) . '_last_run', 0);
    }

    /**
     * Save the module last run value. Used to store a timestamp for some modules,
     * for example the Feed by Mail module.
     *
     * @param int $time Unix timestamp (as returned by time() for example)
     * @param string $sub Sub module name (default empty)
     */
    function save_last_run($time, $sub = '') {
        update_option($this->get_prefix($sub) . '_last_run', $time);
    }

    /**
     * Sums $delta seconds to the last run time.
     * @param int $delta Seconds
     * @param string $sub Sub module name (default empty)
     */
    function add_to_last_run($delta, $sub = '') {
        $time = $this->get_last_run($sub);
        $this->save_last_run($time + $delta, $sub);
    }

    /**
     * Checks if the semaphore of that name (for this module) is still red. If it is active the method
     * returns false. If it is not active, it will be activated for $time seconds.
     *
     * Since this method activate the semaphore when called, it's name is a bit confusing.
     *
     * @param string $name Sempahore name (local to this module)
     * @param int $time Max time in second this semaphore should stay red
     * @return boolean False if the semaphore is red and you should not proceed, true is it was not active and has been activated.
     */
    function check_transient($name, $time) {
        if ($time < 60)
            $time = 60;
        //usleep(rand(0, 1000000));
        if (($value = get_transient($this->get_prefix() . '_' . $name)) !== false) {
            $this->logger->error('Blocked by transient ' . $this->get_prefix() . '_' . $name . ' set ' . (time() - $value) . ' seconds ago');
            return false;
        }
        set_transient($this->get_prefix() . '_' . $name, time(), $time);
        return true;
    }

    function delete_transient($name = '') {
        delete_transient($this->get_prefix() . '_' . $name);
    }

    /** Returns a random token of the specified size (or 10 characters if size is not specified).
     *
     * @param int $size
     * @return string
     */
    static function get_token($size = 10) {
        return substr(md5(rand()), 0, $size);
    }

    /**
     * Adds query string parameters to an URL checing id there are already other parameters.
     *
     * @param string $url
     * @param string $qs The part of query-string to add (param1=value1&param2=value2...)
     * @param boolean $amp If the method must use the &amp; instead of the plain & (default true)
     * @return string
     */
    static function add_qs($url, $qs, $amp = true) {
        if (strpos($url, '?') !== false) {
            if ($amp)
                return $url . '&amp;' . $qs;
            else
                return $url . '&' . $qs;
        } else
            return $url . '?' . $qs;
    }

    /**
     * Returns the email address normalized, lowercase with no spaces. If it's not a valid email
     * returns false.
     */
    static function normalize_email($email) {
        if (!is_string($email))
            return false;
        $email = strtolower(trim($email));
        if (!is_email($email)) {
            return false;
        }
        //$email = apply_filters('newsletter_normalize_email', $email);
        return $email;
    }

    static function normalize_name($name) {
        $name = str_replace(';', ' ', $name);
        $name = strip_tags($name);
        return $name;
    }

    static function normalize_sex($sex) {
        $sex = trim(strtolower($sex));
        if ($sex != 'f' && $sex != 'm') {
            $sex = 'n';
        }
        return $sex;
    }

    static function is_email($email, $empty_ok = false) {

        if (!is_string($email))
            return false;
        $email = strtolower(trim($email));

        if ($email == '') {
            return $empty_ok;
        }

        if (!is_email($email)) {
            return false;
        }

        // TODO: To be moved on the subscription module and make configurable
        if (strpos($email, 'mailinator.com') !== false) {
            return false;
        }
        if (strpos($email, 'guerrillamailblock.com') !== false) {
            return false;
        }
        if (strpos($email, 'emailtemporanea.net') !== false) {
            return false;
        }
        return true;
    }

    /**
     * Converts a GMT date from mysql (see the posts table columns) into a timestamp.
     *
     * @param string $s GMT date with format yyyy-mm-dd hh:mm:ss
     * @return int A timestamp
     */
    static function m2t($s) {

        // TODO: use the wordpress function I don't remeber the name
        $s = explode(' ', $s);
        $d = explode('-', $s[0]);
        $t = explode(':', $s[1]);
        return gmmktime((int) $t[0], (int) $t[1], (int) $t[2], (int) $d[1], (int) $d[2], (int) $d[0]);
    }

    static function format_date($time) {
        if (empty($time)) {
            return '-';
        }
        return gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);
    }

    static function format_time_delta($delta) {
        $days = floor($delta / (3600 * 24));
        $hours = floor(($delta % (3600 * 24)) / 3600);
        $minutes = floor(($delta % 3600) / 60);
        $seconds = floor(($delta % 60));
        $buffer = $days . ' days, ' . $hours . ' hours, ' . $minutes . ' minutes, ' . $seconds . ' seconds';
        return $buffer;
    }

    /**
     * Formats a scheduler returned "next execution" time, managing negative or false values. Many times
     * used in conjuction with "last run".
     *
     * @param string $name The scheduler name
     * @return string
     */
    static function format_scheduler_time($name) {
        $time = wp_next_scheduled($name);
        if ($time === false) {
            return 'No next run scheduled';
        }
        $delta = $time - time();
        // If less 10 minutes late it can be a cron problem but now it is working
        if ($delta < 0 && $delta > -600) {
            return 'Probably running now';
        } else if ($delta <= -600) {
            return 'It seems the cron system is not working. Reload the page to see if this message change.';
        }
        return 'Runs in ' . self::format_time_delta($delta);
    }

    static function date($time = null, $now = false, $left = false) {
        if (is_null($time)) {
            $time = time();
        }
        if ($time == false) {
            $buffer = 'none';
        } else {
            $buffer = gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);
        }
        if ($now) {
            $buffer .= ' (now: ' . gmdate(get_option('date_format') . ' ' .
                            get_option('time_format'), time() + get_option('gmt_offset') * 3600);
            $buffer .= ')';
        }
        if ($left) {
            $buffer .= ', ' . gmdate('H:i:s', $time - time()) . ' left';
        }
        return $buffer;
    }

    /**
     * Return an array of array with on first element the array of recent post and on second element the array
     * of old posts.
     *
     * @param array $posts
     * @param int $time
     */
    static function split_posts(&$posts, $time = 0) {
        if ($time < 0) {
            return array_chunk($posts, ceil(count($posts) / 2));
        }

        $result = array(array(), array());

        if (empty($posts))
            return $result;

        foreach ($posts as &$post) {
            if (self::is_post_old($post, $time))
                $result[1][] = $post;
            else
                $result[0][] = $post;
        }
        return $result;
    }

    static function is_post_old(&$post, $time = 0) {
        return self::m2t($post->post_date_gmt) <= $time;
    }

    static function get_post_image($post_id = null, $size = 'thumbnail', $alternative = null) {
        global $post;

        if (empty($post_id))
            $post_id = $post->ID;
        if (empty($post_id))
            return $alternative;

        $image_id = function_exists('get_post_thumbnail_id') ? get_post_thumbnail_id($post_id) : false;
        if ($image_id) {
            $image = wp_get_attachment_image_src($image_id, $size);
            return $image[0];
        } else {
            $attachments = get_children(array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));

            if (empty($attachments)) {
                return $alternative;
            }

            foreach ($attachments as $id => $attachment) {
                $image = wp_get_attachment_image_src($id, $size);
                return $image[0];
            }
        }
    }

    /**
     * Cleans up a text containing url tags with appended the absolute URL (due to
     * the editor behavior) moving back them to the simple form.
     */
    static function clean_url_tags($text) {
        $text = str_replace('%7B', '{', $text);
        $text = str_replace('%7D', '}', $text);

        // Only tags which are {*_url}
        $text = preg_replace("/[\"']http[^\"']+(\\{[^\\}]+_url\\})[\"']/i", "\"\\1\"", $text);
        return $text;
    }

    function get_styles() {

        $list = array('' => 'none');

        $dir = NEWSLETTER_DIR . '/' . $this->module . '/styles';
        $handle = @opendir($dir);

        if ($handle !== false) {
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..')
                    continue;
                if (substr($file, -4) != '.css')
                    continue;
                $list[$file] = substr($file, 0, strlen($file) - 4);
            }
            closedir($handle);
        }

        $dir = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/styles';
        if (is_dir($dir)) {
            $handle = @opendir($dir);

            if ($handle !== false) {
                while ($file = readdir($handle)) {
                    if ($file == '.' || $file == '..')
                        continue;
                    if (isset($list[$file]))
                        continue;
                    if (substr($file, -4) != '.css')
                        continue;
                    $list[$file] = substr($file, 0, strlen($file) - 4);
                }
                closedir($handle);
            }
        }
        return $list;
    }

    function get_style_url($style) {
        if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/styles/' . $style))
            return WP_CONTENT_URL . '/extensions/newsletter/' . $this->module . '/styles/' . $style;
        else
            return plugins_url('newsletter') . '/' . $this->module . '/styles/' . $style;
    }

    function admin_menu() {
        
    }

    function add_menu_page($page, $title, $capability = '') {
        global $newsletter;
        $name = 'newsletter_' . $this->module . '_' . $page;
        if (empty($capability)) {
            $capability = ($newsletter->options['editor'] == 1) ? 'manage_categories' : 'manage_options';
        }
        add_submenu_page('newsletter_main_index', $title, $title, $capability, $name, array($this, 'menu_page'));
    }

    function add_admin_page($page, $title) {
        global $newsletter;
        $name = 'newsletter_' . $this->module . '_' . $page;
        $name = apply_filters('newsletter_admin_page', $name);
        add_submenu_page(null, $title, $title, ($newsletter->options['editor'] == 1) ? 'manage_categories' : 'manage_options', $name, array($this, 'menu_page'));
    }

    function sanitize_file_name($name) {
        return preg_replace('/[^a-z_\\-]/i', '', $name);
    }

    function menu_page() {
        global $plugin_page, $newsletter, $wpdb;

        $parts = explode('_', $plugin_page, 3);
        $module = $this->sanitize_file_name($parts[1]);
        $page = $this->sanitize_file_name($parts[2]);
        $page = str_replace('_', '-', $page);

        $file = WP_CONTENT_DIR . '/extensions/newsletter/' . $module . '/' . $page . '.php';
        if (!is_file($file)) {
            $file = NEWSLETTER_DIR . '/' . $module . '/' . $page . '.php';
        }
        require $file;
    }

    function get_admin_page_url($page) {
        return admin_url('admin.php') . '?page=newsletter_' . $this->module . '_' . $page;
    }

    /** Returns all the emails of the give type (message, feed, followup, ...) and in the given format
     * (default as objects). Return false on error or at least an empty array. Errors should never
     * occur.
     *
     * @global wpdb $wpdb
     * @param string $type
     * @return boolean|array
     */
    function get_emails($type = null, $format = OBJECT) {
        global $wpdb;
        if ($type == null) {
            $list = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " order by id desc", $format);
        } else {
            $type = (string) $type;
            $list = $wpdb->get_results($wpdb->prepare("select * from " . NEWSLETTER_EMAILS_TABLE . " where type=%s order by id desc", $type), $format);
        }
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        if (empty($list)) {
            return array();
        }
        return $list;
    }

    /**
     * Retrieves an email from DB and unserialize the options.
     * 
     * @param mixed $id
     * @param string $format
     */
    function get_email($id, $format = OBJECT) {
        $email = $this->store->get_single(NEWSLETTER_EMAILS_TABLE, $id, $format);
        if (!$email) {
            return null;
        }
        if ($format == OBJECT) {
            $email->options = maybe_unserialize($email->options);
            if (!is_array($email->options)) {
                $email->options = array();
            }
            if (empty($email->query)) {
                $email->query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
            }
        } else if ($format == ARRAY_A) {
            $email['options'] = maybe_unserialize($email['options']);
            if (!is_array($email['options'])) {
                $email['options'] = array();
            }
            if (empty($email['query'])) {
                $email['query'] = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
            }
        }
        return $email;
    }

    /**
     * Save an email and provide serialization, if needed, of $email['options']. 
     */
    function save_email($email, $return_format = OBJECT) {
        if (is_object($email)) {
            $email = (array) $email;
        }

        if (isset($email['subject'])) {
            if (mb_strlen($email['subject'], 'UTF-8') > 250) {
                $email['subject'] = mb_substr($email['subject'], 0, 250, 'UTF-8');
            }
        }
        if (isset($email['options']) && is_array($email['options'])) {
            $email['options'] = serialize($email['options']);
        }
        $email = $this->store->save(NEWSLETTER_EMAILS_TABLE, $email, $return_format);
        if ($return_format == OBJECT) {
            $email->options = maybe_unserialize($email->options);
            if (!is_array($email->options))
                $email->options = array();
        } else if ($return_format == ARRAY_A) {
            $email['options'] = maybe_unserialize($email['options']);
            if (!is_array($email['options']))
                $email['options'] = array();
        }
        return $email;
    }

    function get_email_from_request() {

        if (isset($_REQUEST['nek'])) {
            list($id, $token) = @explode('-', $_REQUEST['nek'], 2);
        } else {
            return null;
        }

        $email = $this->get_email($id);

        return $email;
    }

    function delete_email($id) {
        global $wpdb;
        $r = $this->store->delete(NEWSLETTER_EMAILS_TABLE, $id);
        if ($r !== false) {
            $wpdb->delete(NEWSLETTER_STATS_TABLE, array('email_id' => $id));
            $wpdb->delete(NEWSLETTER_SENT_TABLE, array('email_id' => $id));
        }
        return $r;
    }

    function get_email_field($id, $field_name) {
        return $this->store->get_field(NEWSLETTER_EMAILS_TABLE, $id, $field_name);
    }

    function get_email_status_label($email) {
        switch ($email->status) {
            case 'sending':
                if ($email->send_on > time()) {
                    return __('Scheduled', 'newsletter');
                } else {
                    return __('Sending', 'newsletter');
                }

            case 'sent':
                return __('Sent', 'newsletter');
            case 'paused':
                return __('Paused', 'newsletter');
            case 'new':
                return __('Draft', 'newsletter');
            default:
                return ucfirst($email->status);
        }
    }

    function get_email_type_label($type) {

        // Is an email?
        if (is_object($type))
            $type = $type->type;

        $label = apply_filters('newsletter_email_type', '', $type);

        if (!empty($label))
            return $label;

        switch ($type) {
            case 'followup':
                return 'Followup';
            case 'message':
                return 'Standard Newsletter';
            case 'feed':
                return 'Feed by Mail';
        }

        if (strpos($type, 'automated') === 0) {
            list($a, $id) = explode('_', $type);
            return 'Automated Channel ' . $id;
        }

        return ucfirst($type);
    }

    function get_email_progress_label($email) {
        if ($email->status == 'sent' || $email->status == 'sending') {
            return $email->sent . ' ' . __('of', 'newsletter') . ' ' . $email->total;
        }
        return '-';
    }

    /**
     * Returns the email unique key
     * @param TNP_User $user
     * @return string
     */
    function get_email_key($email) {
        return $email->id . '-' . $email->token;
    }

    /** Searches for a user using the nk parameter or the ni and nt parameters. Tries even with the newsletter cookie.
     * If found, the user object is returned or null.
     * The user is returned without regards to his status that should be checked by caller.
     *
     * @return TNP_User
     */
    function check_user() {
        global $wpdb;

        $user = null;

        if (isset($_REQUEST['nk'])) {
            list($id, $token) = @explode('-', $_REQUEST['nk'], 2);
        } else if (isset($_COOKIE['newsletter'])) {
            list ($id, $token) = @explode('-', $_COOKIE['newsletter'], 2);
        }

        if (isset($id)) {
            $user = $this->get_user($id);
            if ($token != $user->token) {
                $user = null;
            }
        }

        if ($user == null && is_user_logged_in()) {
            $user = $this->get_user_by_wp_user_id(get_current_user_id());
        }
        return $user;
    }

    /** Returns the user identify by an id or an email. If $id_or_email is an object or an array, it is assumed it contains
     * the "id" attribute or key and that is used to load the user.
     *
     * @global type $wpdb
     * @param string|int|object|array $id_or_email
     * @param string $format
     * @return TNP_User
     */
    function get_user($id_or_email, $format = OBJECT) {
        global $wpdb;

        if (empty($id_or_email))
            return null;

        // To simplify the reaload of a user passing the user it self.
        if (is_object($id_or_email)) {
            $id_or_email = $id_or_email->id;
        } else if (is_array($id_or_email)) {
            $id_or_email = $id_or_email['id'];
        }

        $id_or_email = strtolower(trim($id_or_email));

        if (is_numeric($id_or_email)) {
            $r = $wpdb->get_row($wpdb->prepare("select * from " . NEWSLETTER_USERS_TABLE . " where id=%d limit 1", $id_or_email), $format);
        } else {
            $r = $wpdb->get_row($wpdb->prepare("select * from " . NEWSLETTER_USERS_TABLE . " where email=%s limit 1", $id_or_email), $format);
        }

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return null;
        }
        return $r;
    }

    /**
     * Returns the user unique key
     * @param TNP_User $user
     * @return string
     */
    function get_user_key($user) {
        return $user->id . '-' . $user->token;
    }

    /**
     * Return the user identified by the "nk" parameter (POST or GET).
     * If no user can be found or the token is not matching, returns null. 
     * If die_on_fail is true it dies instead of return null.
     * 
     * @param bool $die_on_fail
     * @return TNP_User
     */
    function get_user_from_request($die_on_fail = false) {
        $id = 0;
        if (isset($_REQUEST['nk'])) {
            list($id, $token) = @explode('-', $_REQUEST['nk'], 2);
        }
        $user = $this->get_user($id);

        if ($user == null || $token != $user->token) {
            if ($die_on_fail) {
                die(__('No subscriber found.', 'newsletter'));
            } else {
                return null;
            }
        }
        return $user;
    }

    /**
     * @param string $language The language for the list labels (it does not affect the lists returned)
     * @return TNP_List[]
     */
    function get_lists($language = '') {
        static $lists = array();
        if (isset($lists[$language])) {
            return $lists[$language];
        }

        $lists[$language] = array();
        $data = NewsletterSubscription::instance()->get_options('lists', $language);
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($data['list_' . $i])) {
                continue;
            }
            $list = new stdClass();
            $list->name = $data['list_' . $i];
            $list->id = $i;
            $list->forced = !empty($data['list_' . $i . '_forced']);
            $list->status = (int) $data['list_' . $i . '_status'];
            $list->checked = !empty($data['list_' . $i . '_checked']);
            if (empty($data['list_' . $i . '_languages'])) {
                $list->languages = array();
            } else {
                $list->languages = $data['list_' . $i . '_languages'];
            }
            $lists[$language][] = $list;
        }
        return $lists[$language];
    }

    /**
     * 
     * @return TNP_List[]
     */
    function get_lists_public($language = '') {
        static $lists = array();
        if (isset($lists[$language])) {
            return $lists[$language];
        }

        $lists[$language] = array();
        $all = $this->get_lists($language);
        foreach ($all as $list) {
            if ($list->status == TNP_List::STATUS_PRIVATE) {
                continue;
            }
            $lists[$language][] = $list;
        }
        return $lists[$language];
    }

    /**
     * Lists to be shown on subscription form.
     * 
     * @return TNP_List[]
     */
    function get_lists_for_subscription($language = '') {
        static $lists = array();
        if (isset($lists[$language])) {
            return $lists[$language];
        }

        $lists[$language] = array();
        $all = $this->get_lists($language);
        foreach ($all as $list) {
            if ($list->status != TNP_List::STATUS_PUBLIC || $list->forced) {
                continue;
            }
            $lists[$language][] = $list;
        }
        return $lists[$language];
    }

    /**
     * Returns the lists to be shown in the profile page.
     * 
     * @return TNP_List[]
     */
    function get_lists_for_profile($language = '') {
        static $lists = array();
        if (isset($lists[$language])) {
            return $lists[$language];
        }

        $lists[$language] = array();
        $all = $this->get_lists($language);
        foreach ($all as $list) {
            if ($list->status == TNP_List::STATUS_PRIVATE || $list->status == TNP_List::STATUS_HIDDEN) {
                continue;
            }
            $lists[$language][] = $list;
        }
        return $lists[$language];
    }

    /**
     * Returns a list as an object (with the same signature of TNP_List)
     * 
     * @param int $id
     * @return TNP_List
     */
    function get_list($id, $language = '') {
        $id = (int) $id;
        if (!$id) {
            return null;
        }
        $data = NewsletterSubscription::instance()->get_options('lists', $language);
        $list = new stdClass();
        $list->name = $data['list_' . $id];
        $list->id = $id;
        $list->forced = !empty($data['list_' . $id . '_forced']);
        $list->status = (int) $data['list_' . $id . '_status'];
        $list->checked = !empty($data['list_' . $id . '_checked']);
        return $list;
    }

    /**
     * NEVER CHANGE THIS METHOD SIGNATURE, USER BY THIRD PARTY PLUGINS.
     *
     * Saves a new user on the database. Return false if the email (that must be unique) is already
     * there. For a new users set the token and creation time if not passed.
     *
     * @param array $user
     * @return TNP_User|array|boolean Returns the subscriber reloaded from DB in the specified format. Flase on failure (duplicate email).
     */
    function save_user($user, $return_format = OBJECT) {
        if (is_object($user)) {
            $user = (array) $user;
        }
        if (empty($user['id'])) {
            $existing = $this->get_user($user['email']);
            if ($existing != null) {
                return false;
            }
            if (empty($user['token'])) {
                $user['token'] = NewsletterModule::get_token();
            }
        }
        // Due to the unique index on email field, this can fail.
        return $this->store->save(NEWSLETTER_USERS_TABLE, $user, $return_format);
    }

    /**
     * Updates the user last activity timestamp.
     * 
     * @global wpdb $wpdb
     * @param TNP_User $user
     */
    function update_user_last_activity($user) {
        global $wpdb;
        $this->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set last_activity=%d where id=%d limit 1", time(), $user->id));
    }

    /**
     * Finds single style blocks and adds a style attribute to every HTML tag with a class exactly matching the rules in the style
     * block. HTML tags can use the attribute "inline-class" to exact match a style rules if they need a composite class definition.
     * 
     * @param string $content
     * @param boolean $strip_style_blocks
     * @return string
     */
    function inline_css($content, $strip_style_blocks = false) {
        // CSS
        $matches = array();
        // "s" skips line breaks
        $styles = preg_match('|<style>(.*?)</style>|s', $content, $matches);
        if (isset($matches[1])) {
            $style = str_replace(array("\n", "\r"), '', $matches[1]);
            $rules = array();
            preg_match_all('|\s*\.(.*?)\{(.*?)\}\s*|s', $style, $rules);
            //print_r($rules);
            for ($i = 0; $i < count($rules[1]); $i++) {
                $class = trim($rules[1][$i]);
                $value = trim($rules[2][$i]);
                $value = preg_replace('|\s+|', ' ', $value);
                $content = str_replace('class="' . $class . '"', 'class="' . $class . '" style="' . $value . '"', $content);
                $content = str_replace('inline-class="' . $class . '"', 'style="' . $value . '"', $content);
            }
        }

        if ($strip_style_blocks) {
            return trim(preg_replace('|<style>.*?</style>|s', '', $content));
        } else {
            return $content;
        }
    }

    /**
     * Returns a list of users marked as "test user".
     * @return TNP_User[]
     */
    function get_test_users() {
        return $this->store->get_all(NEWSLETTER_USERS_TABLE, "where test=1");
    }

    /**
     * Deletes a subscriber and cleans up all the stats table with his correlated data.
     * 
     * @global wpdb $wpdb
     * @param int $id
     */
    function delete_user($id) {
        global $wpdb;
        $user = $this->get_user($id);
        if ($user) {
            $r = $this->store->delete(NEWSLETTER_USERS_TABLE, $id);
            do_action('newsletter_user_deleted', $user);
        }
        // Anyway try a table clean up, nothing bad happens
        $wpdb->delete(NEWSLETTER_STATS_TABLE, array('user_id' => $id));
        $wpdb->delete(NEWSLETTER_SENT_TABLE, array('user_id' => $id));
    }

    /**
     * Add to a destination url the parameters to identify the user, the email and to show
     * an alert message, if required. The parameters and them managed by the [newsletter] shortcode.
     * 
     * @param string $url If empty the standard newsletter page URL is used (usually it is empty, but sometime a custom URL has been specified)
     * @param string $message_key The message identifier
     * @param TNP_User|int $user
     * @param TNP_Email|int $email
     * @param string $alert An optional alter message to be shown. Does not work with custom URLs
     * @return string The final URL with parameters
     */
    function build_message_url($url = '', $message_key = '', $user = null, $email = null, $alert = '') {
        $params = 'nm=' . urlencode($message_key);
        $language = '';
        if ($user) {
            if (!is_object($user)) {
                $user = $this->get_user($user);
            }
            $params .= '&nk=' . urlencode($this->get_user_key($user));
            $language = $this->get_user_language($user);
        }

        if ($email) {
            if (!is_object($email)) {
                $email = $this->get_email($email);
            }
            $params .= '&nek=' . urlencode($this->get_email_key($email));
        }

        if ($alert) {
            $params .= '&alert=' . urlencode($alert);
        }

        if (empty($url)) {
            $url = Newsletter::instance()->get_newsletter_page_url($language);
        }

        return self::add_qs($url, $params, false);
    }

    /**
     * Builds a standard Newsletter action URL for the specified action.
     * 
     * @param string $action
     * @param TNP_User $user
     * @param TNP_Email $email
     * @return string
     */
    function build_action_url($action, $user = null, $email = null) {
        $url = $this->add_qs($this->get_home_url(), 'na=' . urlencode($action));
        if ($user) {
            $url .= '&nk=' . urlencode($this->get_user_key($user));
        }
        if ($email) {
            $url .= '&nek=' . urlencode($this->get_email_key($email));
        }
        return $url;
    }
    
    function get_subscribe_url() {
        return $this->build_action_url('s');
    }

    function clean_stats_table() {
        global $wpdb;
        $this->logger->info('Cleaning up stats table');
        $this->query("delete s from `{$wpdb->prefix}newsletter_stats` s left join `{$wpdb->prefix}newsletter` u on s.user_id=u.id where u.id is null");
        $this->query("delete s from `{$wpdb->prefix}newsletter_stats` s left join `{$wpdb->prefix}newsletter_emails` e on s.email_id=e.id where e.id is null");
    }

    function clean_sent_table() {
        global $wpdb;
        $this->logger->info('Cleaning up sent table');
        $this->query("delete s from `{$wpdb->prefix}newsletter_sent` s left join `{$wpdb->prefix}newsletter` u on s.user_id=u.id where u.id is null");
        $this->query("delete s from `{$wpdb->prefix}newsletter_sent` s left join `{$wpdb->prefix}newsletter_emails` e on s.email_id=e.id where e.id is null");
    }

    function clean_user_logs_table() {
        //global $wpdb;
    }

    function clean_tables() {
        $this->clean_sent_table();
        $this->clean_stats_table();
        $this->clean_user_logs_table();
    }

    function anonymize_ip($ip) {
        if (empty($ip)) {
            return $ip;
        }
        $parts = explode('.', $ip);
        array_pop($parts);
        return implode('.', $parts) . '.0';
    }

    function process_ip($ip) {
        $option = Newsletter::instance()->options['ip'];
        if (empty($option)) {
            return $ip;
        }
        if ($option == 'anonymize') {
            return $this->anonymize_ip($ip);
        }
        return '';
    }

    function anonymize_user($id) {
        global $wpdb;
        $user = $this->get_user($id);
        if (!$user) {
            return null;
        }

        $user->name = '';
        $user->surname = '';
        $user->ip = $this->anonymize_ip($user->ip);

        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            $field = 'profile_' . $i;
            $user->$field = '';
        }

        // [TODO] Status?
        $user->status = TNP_User::STATUS_UNSUBSCRIBED;
        $user->email = $user->id . '@anonymi.zed';

        $user = $this->save_user($user);

        return $user;
    }

    /**
     * Changes a user status. Accept a user object, user id or user email.
     * 
     * @param TNP_User $user
     * @param string $status
     * @return TNP_User
     */
    function set_user_status($user, $status) {
        global $wpdb;

        $this->logger->debug('Status change to ' . $status . ' of subscriber ' . $user->id . ' from ' . $_SERVER['REQUEST_URI']);

        $this->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set status=%s where id=%d limit 1", $status, $user->id));

        return $this->get_user($user);
    }
    
    /**
     * 
     * @global wpdb $wpdb
     * @param TNP_User $user
     * @return TNP_User
     */
    function refresh_user_token($user) {
        global $wpdb;

        $token = $this->get_token();
        
        $this->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set token=%s where id=%d limit 1", $token, $user->id));

        return $this->get_user($user);
    }    

    /**
     * Create a log entry with the meaningful user data. 
     * 
     * @global wpdb $wpdb
     * @param TNP_User $user
     * @param string $source
     * @return type
     */
    function add_user_log($user, $source = '') {
        global $wpdb;

        $lists = $this->get_lists_public();
        foreach ($lists as $list) {
            $field_name = 'list_' . $list->id;
            $data[$field_name] = $user->$field_name;
        }
        $data['status'] = $user->status;
        $ip = $this->get_remote_ip();
        $ip = $this->process_ip($ip);
        $this->store->save($wpdb->prefix . 'newsletter_user_logs', array('ip' => $ip, 'user_id' => $user->id, 'source' => $source, 'created' => time(), 'data' => json_encode($data)));
    }

    /**
     * 
     * @global wpdb $wpdb
     * @param TNP_User $user
     * @param int $list
     * @param type $value
     */
    function set_user_list($user, $list, $value) {
        global $wpdb;

        $list = (int) $list;
        $value = $value ? 1 : 0;
        $r = $wpdb->update(NEWSLETTER_USERS_TABLE, array('list_' . $list => $value), array('id' => $user->id));
    }

    function set_user_field($id, $field, $value) {
        $this->store->set_field(NEWSLETTER_USERS_TABLE, $id, $field, $value);
    }

    function set_user_wp_user_id($user_id, $wp_user_id) {
        $this->store->set_field(NEWSLETTER_USERS_TABLE, $user_id, 'wp_user_id', $wp_user_id);
    }

    /**
     * 
     * @param int $wp_user_id
     * @param string $format
     * @return TNP_User
     */
    function get_user_by_wp_user_id($wp_user_id, $format = OBJECT) {
        return $this->store->get_single_by_field(NEWSLETTER_USERS_TABLE, 'wp_user_id', $wp_user_id, $format);
    }
    
    /**
     * Returns the user language IF there is a supported mutilanguage plugin installed.
     * @param TNP_User $user
     * @return string Language code or empty
     */
    function get_user_language($user) {
        if ($user && $this->is_multilanguage()) {
            return $user->language;
        }
        return '';
    }

    /**
     * Replaces every possible Newsletter tag ({...}) in a piece of text or HTML.
     * 
     * @global wpdb $wpdb
     * @param string $text
     * @param mixed $user Can be an object, associative array or id
     * @param mixed $email Can be an object, associative array or id
     * @param type $referrer
     * @return type
     */
    function replace($text, $user = null, $email = null, $referrer = null) {
        global $wpdb;

        static $home_url = false;

        if (!$home_url) {
            $home_url = home_url('/');
        }

        //$this->logger->debug('Replace start');
        if ($user !== null && !is_object($user)) {
            if (is_array($user)) {
                $user = (object) $user; //$this->get_user($user['id']);
            } else if (is_numeric($user)) {
                $user = $this->get_user($user);
            } else {
                $user = null;
            }
        }

        if ($email !== null && !is_object($email)) {
            if (is_array($email)) {
                $email = (object) $email; //$this->get_user($user['id']);
            } else if (is_numeric($email)) {
                $email = $this->get_email($email);
            } else {
                $email = null;
            }
        }


        $text = apply_filters('newsletter_replace', $text, $user, $email);

        $text = $this->replace_url($text, 'BLOG_URL', $home_url);
        $text = $this->replace_url($text, 'HOME_URL', $home_url);

        $text = str_replace('{blog_title}', get_option('blogname'), $text);
        $text = str_replace('{blog_description}', get_option('blogdescription'), $text);

        $text = $this->replace_date($text);

        if ($user) {
            $nk = $this->get_user_key($user);
            $options_profile = get_option('newsletter_profile');

            $text = str_replace('{email}', $user->email, $text);
            $name = apply_filters('newsletter_replace_name', $user->name, $user);
            if (empty($name)) {
                $text = str_replace(' {name}', '', $text);
                $text = str_replace('{name}', '', $text);
            } else {
                $text = str_replace('{name}', $name, $text);
            }

            switch ($user->sex) {
                case 'm': $text = str_replace('{title}', $options_profile['title_male'], $text);
                    break;
                case 'f': $text = str_replace('{title}', $options_profile['title_female'], $text);
                    break;
                case 'n': $text = str_replace('{title}', $options_profile['title_none'], $text);
                    break;
                default:
                    $text = str_replace('{title}', '', $text);
            }


            $text = str_replace('{surname}', $user->surname, $text);
            $text = str_replace('{last_name}', $user->surname, $text);

            $full_name = trim($user->name . ' ' . $user->surname);
            if (empty($full_name)) {
                $text = str_replace(' {full_name}', '', $text);
                $text = str_replace('{full_name}', '', $text);
            } else {
                $text = str_replace('{full_name}', $full_name, $text);
            }

            $text = str_replace('{token}', $user->token, $text);
            $text = str_replace('%7Btoken%7D', $user->token, $text);
            $text = str_replace('{id}', $user->id, $text);
            $text = str_replace('%7Bid%7D', $user->id, $text);
            $text = str_replace('{ip}', $user->ip, $text);
            $text = str_replace('{key}', $nk, $text);
            $text = str_replace('%7Bkey%7D', $nk, $text);

            for ($i = 1; $i < NEWSLETTER_PROFILE_MAX; $i++) {
                $p = 'profile_' . $i;
                $text = str_replace('{profile_' . $i . '}', $user->$p, $text);
            }

            $base = (empty($this->options_main['url']) ? get_option('home') : $this->options_main['url']);
            $id_token = '&amp;ni=' . $user->id . '&amp;nt=' . $user->token;


            $options_subscription = NewsletterSubscription::instance()->options;



            $nek = false;
            if ($email) {
                $nek = $email->id . '-' . $email->token;
                $text = str_replace('{email_id}', $email->id, $text);
                $text = str_replace('{email_key}', $nek, $text);
                $text = str_replace('{email_subject}', $email->subject, $text);
                $text = $this->replace_url($text, 'EMAIL_URL', $this->build_action_url('v', $user) . '&id=' . $email->id);
            }


            //$text = str_replace('{activation_link}', '<a href="{activation_url}">' . $options_subscription['confirmation_label'] . '</a>', $text);


            $text = $this->replace_url($text, 'SUBSCRIPTION_CONFIRM_URL', $this->build_action_url('c', $user));
            $text = $this->replace_url($text, 'ACTIVATION_URL', $this->build_action_url('v', $user));

            // Obsolete.
            $text = $this->replace_url($text, 'FOLLOWUP_SUBSCRIPTION_URL', self::add_qs($base, 'nm=fs' . $id_token));
            $text = $this->replace_url($text, 'FOLLOWUP_UNSUBSCRIPTION_URL', self::add_qs($base, 'nm=fu' . $id_token));
            $text = $this->replace_url($text, 'FEED_SUBSCRIPTION_URL', self::add_qs($base, 'nm=es' . $id_token));
            $text = $this->replace_url($text, 'FEED_UNSUBSCRIPTION_URL', self::add_qs($base, 'nm=eu' . $id_token));

            $text = $this->replace_url($text, 'UNLOCK_URL', $this->build_action_url('ul', $user));
        } else {
            $text = $this->replace_url($text, 'SUBSCRIPTION_CONFIRM_URL', '#');
            $text = $this->replace_url($text, 'ACTIVATION_URL', '#');
            $text = $this->replace_url($text, 'UNSUBSCRIPTION_CONFIRM_URL', '#');
            $text = $this->replace_url($text, 'UNSUBSCRIPTION_URL', '#');
        }

        if (strpos($text, '{subscription_form}') !== false) {
            $text = str_replace('{subscription_form}', NewsletterSubscription::instance()->get_subscription_form($referrer), $text);
        } else {
            for ($i = 1; $i <= 10; $i++) {
                if (strpos($text, "{subscription_form_$i}") !== false) {
                    $text = str_replace("{subscription_form_$i}", NewsletterSubscription::instance()->get_form($i), $text);
                    break;
                }
            }
        }

        // Company info
        // TODO: Move to another module
        $options = Newsletter::instance()->options;
        $text = str_replace('{company_address}', $options['footer_contact'], $text);
        $text = str_replace('{company_name}', $options['footer_title'], $text);


        //$this->logger->debug('Replace end');
        return $text;
    }

    function replace_date($text) {
        $text = str_replace('{date}', date_i18n(get_option('date_format')), $text);

        // Date processing
        $x = 0;
        while (($x = strpos($text, '{date_', $x)) !== false) {
            $y = strpos($text, '}', $x);
            if ($y === false)
                continue;
            $f = substr($text, $x + 6, $y - $x - 6);
            $text = substr($text, 0, $x) . date_i18n($f) . substr($text, $y + 1);
        }
        return $text;
    }

    function replace_url($text, $tag, $url) {
        static $home = false;
        if (!$home) {
            $home = trailingslashit(home_url());
        }
        $tag_lower = strtolower($tag);
        $text = str_replace('http://{' . $tag_lower . '}', $url, $text);
        $text = str_replace('https://{' . $tag_lower . '}', $url, $text);
        $text = str_replace($home . '{' . $tag_lower . '}', $url, $text);
        $text = str_replace($home . '%7B' . $tag_lower . '%7D', $url, $text);
        $text = str_replace('{' . $tag_lower . '}', $url, $text);
        $text = str_replace('%7B' . $tag_lower . '%7D', $url, $text);

        $url_encoded = urlencode($url);
        $text = str_replace('%7B' . $tag_lower . '_encoded%7D', $url_encoded, $text);
        $text = str_replace('{' . $tag_lower . '_encoded}', $url_encoded, $text);

        // for compatibility
        $text = str_replace($home . $tag, $url, $text);

        return $text;
    }

    public static function antibot_form_check($captcha = false) {
        if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
            return false;
        }

        if (!isset($_POST['ts']) || time() - $_POST['ts'] > 60) {
            return false;
        }
        if ($captcha) {
            $n1 = (int) $_POST['n1'];
            if (empty($n1)) {
                return false;
            }
            $n2 = (int) $_POST['n2'];
            if (empty($n2)) {
                return false;
            }
            $n3 = (int) $_POST['n3'];
            if ($n1 + $n2 != $n3) {
                return false;
            }
        }

        return true;
    }

    public static function request_to_antibot_form($submit_label = 'Continue...', $captcha = false) {
        header('Content-Type: text/html;charset=UTF-8');
        header('X-Robots-Tag: noindex,nofollow,noarchive');
        header('Cache-Control: no-cache,no-store,private');
        echo "<!DOCTYPE html>\n";
        echo '<html><head>'
        . '<style type="text/css">'
        . '.tnp-captcha {text-align: center; margin: 200px auto 0 auto !important; max-width: 300px !important; padding: 10px !important; font-family: "Open Sans", sans-serif; background: #ECF0F1; border-radius: 5px; padding: 50px !important; border: none !important;}'
        . 'p {text-align: center; padding: 10px; color: #7F8C8D;}'
        . 'input[type=text] {width: 50px; padding: 10px 10px; border: none; border-radius: 2px; margin: 0px 5px;}'
        . 'input[type=submit] {text-align: center; border: none; padding: 10px 15px; font-family: "Open Sans", sans-serif; background-color: #27AE60; color: white; cursor: pointer;}'
        . '</style>'
        . '</head><body>';
        echo '<form method="post" action="https://www.domain.tld" id="form">';
        echo '<div style="width: 1px; height: 1px; overflow: hidden">';
        foreach ($_REQUEST as $name => $value) {
            if ($name == 'submit')
                continue;
            if (is_array($value)) {
                foreach ($value as $element) {
                    echo '<input type="text" name="';
                    echo esc_attr($name);
                    echo '[]" value="';
                    echo esc_attr(stripslashes($element));
                    echo '">';
                }
            } else {
                echo '<input type="hidden" name="', esc_attr($name), '" value="', esc_attr(stripslashes($value)), '">';
            }
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            echo '<input type="hidden" name="nhr" value="' . esc_attr($_SERVER['HTTP_REFERER']) . '">';
        }
        echo '<input type="hidden" name="ts" value="' . time() . '">';
        echo '</div>';

        if ($captcha) {
            echo '<div class="tnp-captcha">';
            echo '<p>', __('Math question', 'newsletter'), '</p>';
            echo '<input type="text" name="n1" value="', rand(1, 9), '" readonly style="width: 50px">';
            echo '+';
            echo '<input type="text" name="n2" value="', rand(1, 9), '" readonly style="width: 50px">';
            echo '=';
            echo '<input type="text" name="n3" value="?" style="width: 50px">';
            echo '<br><br>';
            echo '<input type="submit" value="', esc_attr($submit_label), '">';
            echo '</div>';
        }
        echo '<noscript><input type="submit" value="';
        echo esc_attr($submit_label);
        echo '"></noscript></form>';
        echo '<script>';
        echo 'document.getElementById("form").action="' . home_url('/') . '";';
        if (!$captcha) {
            echo 'document.getElementById("form").submit();';
        }
        echo '</script>';
        echo '</body></html>';
        die();
    }

    static function extract_body($html) {
        $x = stripos($html, '<body');
        if ($x !== false) {
            $x = strpos($html, '>', $x);
            $y = strpos($html, '</body>');
            return substr($html, $x + 1, $y - $x - 1);
        } else {
            return $html;
        }
    }

    /** Returns a percentage as string */
    static function percent($value, $total) {
        if ($total == 0)
            return '-';
        return sprintf("%.2f", $value / $total * 100) . '%';
    }

    /** Returns a percentage as integer value */
    static function percentValue($value, $total) {
        if ($total == 0)
            return 0;
        return round($value / $total * 100);
    }

    static function to_int_id($var) {
        if (is_object($var)) {
            return (int) $var->id;
        }
        if (is_array($var)) {
            return (int) $var['id'];
        }
        return (int) $var;
    }

    static function sanitize_ip($ip) {
        if (empty($ip))
            return '';
        return preg_replace('/[^0-9a-fA-F:., ]/', '', $ip);
    }

    static function get_remote_ip() {
        return self::sanitize_ip($_SERVER['REMOTE_ADDR']);
    }

    static function get_signature($text) {
        $key = NewsletterStatistics::instance()->options['key'];
        return md5($text . $key);
    }

    static function check_signature($text, $signature) {
        if (empty($signature))
            return false;
        $key = NewsletterStatistics::instance()->options['key'];
        return md5($text . $key) === $signature;
    }

    static function get_home_url() {
        static $url = false;
        if (!$url) {
            $url = home_url('/');
        }
        return $url;
    }

    /**
     * Return the current language code. Optionally, if a user is passed and it has a language
     * the user language is returned.
     * If there is no language available, an empty string is returned.
     * 
     * @param TNP_User $user
     * @return string The language code
     */
    function get_current_language($user = null) {
        // TODO: Check if the blog is multilanguage?
        
        if ($user && $user->language) {
            return $user->language;
        }
        
        if (class_exists('SitePress')) {
            $current_language = apply_filters('wpml_current_language', '');
            if ($current_language == 'all') {
                $current_language = '';
            }
            return $current_language;
        }
        if (function_exists('pll_current_language')) {
            return pll_current_language();
        }
        return '';
    }

    function get_default_language() {
        if (class_exists('SitePress')) {
            return $current_language = apply_filters('wpml_current_language', '');
        }
        if (function_exists('pll_default_language')) {
            return pll_default_language();
        }
        return '';
    }

    function is_all_languages() {
        return $this->get_current_language() == '';
    }

    function is_default_language() {
        return $this->get_current_language() == $this->get_default_language();
    }
    
    /**
     * Returns an array od languages with key the language code and value the language name.
     * An empty array is returned if no language is available.
     */
    function get_languages() {
        $language_options = array();
        if (class_exists('SitePress')) {
            $languages = apply_filters('wpml_active_languages', null);
            foreach ($languages as $language) {
                $language_options[$language['language_code']] = $language['translated_name'];
            }
        } else if (function_exists('icl_get_languages')) {
            $languages = icl_get_languages();
            foreach ($languages as $code=>$language) {
                $language_options[$code] = $language['native_name'];
            }
        }
        
        return $language_options;
    }
    
    function get_language_label($language) {
        $languages = $this->get_languages();
        if (isset($languages[$language])) return $languages[$language];
        return '';
        
    }

    function switch_language($language) {
        if (class_exists('SitePress')) {
            if (empty($language)) $language = 'all';
            do_action('wpml_switch_language', $language);
            return;
        }
    }

    function is_multilanguage() {
        return class_exists('SitePress') || function_exists('pll_default_language');
    }

    function get_posts($filters = array(), $language = '') {
        $current_language = $this->get_current_language();

        // Language switch for WPML
        if ($language) {
            if (class_exists('SitePress')) {
                $this->switch_language($language);
                $filters['suppress_filters'] = false;
            }
            if (class_exists('Polylang')) {
                $filters['lang'] = $language;
            }
        }
        $posts = get_posts($filters);
        if ($language) {
            if (class_exists('SitePress')) {
                $this->switch_language($current_language);
            }
        }
        return $posts;
    }

}

/**
 * Kept for compatibility.
 *
 * @param type $post_id
 * @param type $size
 * @param type $alternative
 * @return type
 */
function nt_post_image($post_id = null, $size = 'thumbnail', $alternative = null) {
    return NewsletterModule::get_post_image($post_id, $size, $alternative);
}

function newsletter_get_post_image($post_id = null, $size = 'thumbnail', $alternative = null) {
    echo NewsletterModule::get_post_image($post_id, $size, $alternative);
}

/**
 * Accepts a post or a post ID.
 * 
 * @param WP_Post $post
 */
function newsletter_the_excerpt($post, $words = 30) {
    $post = get_post($post);
    $excerpt = $post->post_excerpt;
    if (empty($excerpt)) {
        $excerpt = $post->post_content;
        $excerpt = strip_shortcodes($excerpt);
        $excerpt = wp_strip_all_tags($excerpt, true);
    }
    echo '<p>' . wp_trim_words($excerpt, $words) . '</p>';
}
