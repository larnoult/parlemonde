<?php

if (!defined('ABSPATH'))
    exit;

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterStatistics extends NewsletterModule {

    static $instance;
    
    const SENT_READ = 1;
    const SENT_CLICK = 2;

    /**
     * @return NewsletterStatistics
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterStatistics();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('statistics', '1.1.8');
        add_action('wp_loaded', array($this, 'hook_wp_loaded'));
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'hook_admin_enqueue_scripts'));
        }
    }

    function hook_admin_enqueue_scripts() {
        if (isset($_GET['page']) && (strpos($_GET['page'], 'newsletter_statistics') === 0 || strpos($_GET['page'], 'newsletter_reports') === 0)) {
            wp_enqueue_style('newsletter-admin-statistics', plugins_url('newsletter') . '/statistics/css/tnp-statistics.css', array('tnp-admin'), time());
        }
    }

    /**
     * 
     * @global wpdb $wpdb
     */
    function hook_wp_loaded() {
        global $wpdb;

        // Newsletter Link Tracking
        if (isset($_GET['nltr'])) {

            // Patch for links with ;
            $parts = explode(';', base64_decode($_GET['nltr']));
            $email_id = (int) array_shift($parts);
            $user_id = (int) array_shift($parts);
            $signature = array_pop($parts);
            $anchor = array_pop($parts); // No more used
            // The remaining elements are the url splitted when it contains
            $url = implode(';', $parts);

            if (empty($user_id) || empty($url)) {
                header("HTTP/1.0 404 Not Found");
                die('Invalid data');
            }

            $parts = parse_url($url);

            $verified = $signature == md5($email_id . ';' . $user_id . ';' . $url . ';' . $anchor . $this->options['key']);

            if (!$verified) {
                header("HTTP/1.0 404 Not Found");
                die('Url not verified');
            }

            $user = Newsletter::instance()->get_user($user_id);
            if (!$user) {
                header("HTTP/1.0 404 Not Found");
                die('Invalid subscriber');
            }

            // Test emails
            if (empty($email_id)) {
                header('Location: ' . esc_url_raw($url));
                die();
            }

            $email = $this->get_email($email_id);
            if (!$email) {
                header("HTTP/1.0 404 Not Found");
                die('Invalid newsletter');
            }

            setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');

            $is_action = strpos($url, '?na=');

            $ip = $this->get_remote_ip();
            $ip = $this->process_ip($ip);

            if (!$is_action) {
                $this->add_click($url, $user_id, $email_id, $ip);
                $this->update_open_value(self::SENT_CLICK, $user_id, $email_id, $ip);
            } else {
                // Track an action as an email read and not a click
                $this->update_open_value(self::SENT_READ, $user_id, $email_id, $ip);
            }

            $this->update_user_last_activity($user);

            header('Location: ' . apply_filters('newsletter_redirect_url', $url, $email, $user));
            die();
        }

        // Newsletter Open Traking Image
        if (isset($_GET['noti'])) {
            $this->logger->debug('Open tracking: ' . $_GET['noti']);

            list($email_id, $user_id, $signature) = explode(';', base64_decode($_GET['noti']), 3);

            $email = $this->get_email($email_id);
            if (!$email) {
                $this->logger->error('Open tracking request for unexistant email');
                die();
            }

            $user = $this->get_user($user_id);
            if (!$user) {
                $this->logger->error('Open tracking request for unexistant subscriber');
                die();
            }

            if ($email->token) {
                $this->logger->debug('Signature: ' . $signature);
                $s = md5($email_id . $user_id . $email->token);
                if ($s != $signature) {
                    $this->logger->error('Open tracking request with wrong signature. Email token: ' . $email->token);
                    die();
                }
            } else {
                $this->logger->info('Email with no token hence not signature to check');
            }

            $ip = $this->get_remote_ip();
            $ip = $this->process_ip($ip); 

            $this->add_click('', $user_id, $email_id, $ip);
            $this->update_open_value(self::SENT_READ, $user_id, $email_id, $ip);

            $this->update_user_last_activity($user);

            header('Content-Type: image/gif', true);
            echo base64_decode('_R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            die();
        }
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        $sql = "CREATE TABLE `" . $wpdb->prefix . "newsletter_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `email_id` varchar(10) NOT NULL DEFAULT '0',
  `link_id` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `country` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  KEY `user_id` (`user_id`)
) $charset_collate;";

        dbDelta($sql);

        if (empty($this->options['key'])) {
            $this->options['key'] = md5($_SERVER['REMOTE_ADDR'] . rand(100000, 999999) . time());
            $this->save_options($this->options);
        }
    }

    function admin_menu() {
        $this->add_admin_page('index', 'Statistics');
        $this->add_admin_page('view', 'Statistics');
        $this->add_admin_page('newsletters', 'Statistics');
        $this->add_admin_page('settings', 'Statistics');
        $this->add_admin_page('view_retarget', 'Statistics');
        $this->add_admin_page('view_urls', 'Statistics');
        $this->add_admin_page('view_users', 'Statistics');
    }

    function relink($text, $email_id, $user_id, $email_token = '') {
        $this->relink_email_id = $email_id;
        $this->relink_user_id = $user_id;
        $this->relink_email_token = $email_token;

        $this->logger->debug('Relink with token: ' . $email_token);
        $text = preg_replace_callback('/(<[aA][^>]+href[\s]*=[\s]*["\'])([^>"\']+)(["\'][^>]*>)(.*?)(<\/[Aa]>)/s', array($this, 'relink_callback'), $text);

        $signature = md5($email_id . $user_id . $email_token);
        $text = str_replace('</body>', '<img width="1" height="1" alt="" src="' . home_url('/') . '?noti=' . urlencode(base64_encode($email_id . ';' . $user_id . ';' . $signature)) . '"/></body>', $text);
        return $text;
    }

    function relink_callback($matches) {
        $href = trim(str_replace('&amp;', '&', $matches[2]));

        // Do not replace the tracking or subscription/unsubscription links.
        if (strpos($href, '/newsletter/') !== false) {
            return $matches[0];
        }

//        if (strpos($href, '?na=') !== false) {
//            return $matches[0];
//        }
        // Do not relink anchors
        if (substr($href, 0, 1) == '#') {
            return $matches[0];
        }
        // Do not relink mailto:
        if (substr($href, 0, 7) == 'mailto:') {
            return $matches[0];
        }

        // This is the link text which is added to the tracking data
        $anchor = '';
//        if ($this->options['anchor'] == 1) {
//            $anchor = trim(str_replace(';', ' ', $matches[4]));
//            // Keep images but not other tags
//            $anchor = strip_tags($anchor, '<img>');
//
//            // Truncate if needed to avoid to much long URLs
//            if (stripos($anchor, '<img') === false && strlen($anchor) > 100) {
//                $anchor = substr($anchor, 0, 100);
//            }
//        }
        $r = $this->relink_email_id . ';' . $this->relink_user_id . ';' . $href . ';' . $anchor;
        $r = $r . ';' . md5($r . $this->options['key']);
        $r = base64_encode($r);
        $r = urlencode($r);

        $url = home_url('/') . '?nltr=' . $r;

        return $matches[1] . $url . $matches[3] . $matches[4] . $matches[5];
    }

    function get_statistics_url($email_id) {
        $page = apply_filters('newsletter_statistics_view', 'newsletter_statistics_view');
        return 'admin.php?page=' . $page . '&amp;id=' . $email_id;
    }

    function maybe_fix_sent_stats($email) {
        global $wpdb;

        // Very old emails was missing the send_on
        if ($email->send_on == 0) {
            $this->query($wpdb->prepare("update " . NEWSLETTER_EMAILS_TABLE . " set send_on=unix_timestamp(created) where id=%d limit 1", $email->id));
            $email = $this->get_email($email->id);
        }

        if ($email->status == 'sending') {
            return;
        }

        if ($email->type == 'followup') {
            return;
        }

        $count = $wpdb->get_var($wpdb->prepare("select count(*) from " . NEWSLETTER_SENT_TABLE . " where email_id=%d", $email->id));

        if ($count) {
            return;
        }

        if (empty($email->query)) {
            $email->query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
        }

        $query = $email->query . " and unix_timestamp(created)<" . $email->send_on;

        $query = str_replace('*', 'id, ' . $email->id . ', ' . $email->send_on, $query);
        $this->query("insert ignore into " . NEWSLETTER_SENT_TABLE . " (user_id, email_id, time) " . $query);
    }

    function update_stats($email) {
        global $wpdb;

        $wpdb->query($wpdb->prepare("update " . $wpdb->prefix . "newsletter_sent s1 join " . $wpdb->prefix . "newsletter_stats s2 on s1.user_id=s2.user_id and s1.email_id=s2.email_id and s1.email_id=%d set s1.open=1, s1.ip=s2.ip", $email->id));
        $wpdb->query($wpdb->prepare("update " . $wpdb->prefix . "newsletter_sent s1 join " . $wpdb->prefix . "newsletter_stats s2 on s1.user_id=s2.user_id and s1.email_id=s2.email_id and s2.url<>'' and s1.email_id=%d set s1.open=2, s1.ip=s2.ip", $email->id));
    }

    function get_total_count($email_id) {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare("select count(*) from " . NEWSLETTER_SENT_TABLE . " where email_id=%d", $this->to_int_id($email_id)));
    }

    function get_open_count($email_id) {
        global $wpdb;

        return (int) $wpdb->get_var($wpdb->prepare("select count(*) from " . NEWSLETTER_SENT_TABLE . " where open>0 and email_id=%d", $this->to_int_id($email_id)));
    }

    function get_click_count($email_id) {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare("select count(*) from " . NEWSLETTER_SENT_TABLE . " where open>1 and email_id=%d", $this->to_int_id($email_id)));
    }

    function get_error_count($email_id) {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare("select count(*) from " . NEWSLETTER_SENT_TABLE . " where status>0 and email_id=%d", $this->to_int_id($email_id)));
    }

    function add_click($url, $user_id, $email_id, $ip = null) {
        global $wpdb;
        if (is_null($ip)) {
            $ip = $this->get_remote_ip();
        }
        
        $ip = $this->process_ip($ip);
        
        $this->insert(NEWSLETTER_STATS_TABLE, array(
            'email_id' => $email_id,
            'user_id' => $user_id,
            'url' => $url,
            'ip' => $ip
                )
        );
    }

    function update_open_value($value, $user_id, $email_id, $ip = null) {
        global $wpdb;
        if (is_null($ip)) {
            $ip = $this->get_remote_ip();
        }
        $ip = $this->process_ip($ip);
        $this->query($wpdb->prepare("update " . NEWSLETTER_SENT_TABLE . " set open=%d, ip=%s where email_id=%d and user_id=%d and open<%d limit 1", $value, $ip, $email_id, $user_id, $value));
    }

}

NewsletterStatistics::instance();

