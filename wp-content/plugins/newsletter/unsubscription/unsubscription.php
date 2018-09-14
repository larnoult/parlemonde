<?php

defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterUnsubscription extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterUnsubscription
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterUnsubscription();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('unsubscription', '1.0.0');
        add_action('init', array($this, 'hook_init'));
        add_action('wp_loaded', array($this, 'hook_wp_loaded'));
    }

    function hook_init() {
        add_filter('newsletter_replace', array($this, 'hook_newsletter_replace'), 10, 3);
        add_filter('newsletter_page_text', array($this, 'hook_newsletter_page_text'), 10, 3);
    }

    function hook_wp_loaded() {
        global $wpdb;
        //error_reporting(E_STRICT | E_ALL | E_NOTICE);

        switch (Newsletter::instance()->action) {
            case 'u':
                $user = $this->get_user_from_request();
                $email = $this->get_email_from_request();
                if ($user == null) {
                    $url = $this->build_message_url(null, 'error_text', $user);
                    wp_redirect($url);
                } else {
                    $url = $this->build_message_url(null, 'unsubscribe', $user);
                    wp_redirect($url);
                }
                die();
                break;
            case 'uc':
                if ($this->antibot_form_check()) {
                    $user = $this->unsubscribe();
                    if ($user->status == 'E') {
                        $url = $this->build_message_url(null, 'unsubscription_error', $user);
                        wp_redirect($url);
                    } else {
                        $url = $this->build_message_url(null, 'unsubscribed', $user);
                        wp_redirect($url);
                    }
                    return;
                } else {
                    $this->request_to_antibot_form('Unsubscribe');
                }
                die();
                break;

            case 'reactivate':
                if ($this->antibot_form_check()) {
                    $user = $this->reactivate();
                    $url = $this->build_message_url(null, 'reactivated', $user);
                    wp_redirect($url);
                } else {
                    $this->request_to_antibot_form('Reactivate');
                }
                die();

                break;
        }
    }

    /**
     * Unsubscribes the subscriber from the request. Die on subscriber extraction failure.
     *
     * @return TNP_User
     */
    function unsubscribe() {
        $user = $this->get_user_from_request(true);

        if ($user->status == TNP_User::STATUS_UNSUBSCRIBED) {
            return $user;
        }

        $user = $this->refresh_user_token($user);
        $user = $this->set_user_status($user, TNP_User::STATUS_UNSUBSCRIBED);

        $this->add_user_log($user, 'unsubscribe');

        do_action('newsletter_unsubscribed', $user);

        global $wpdb;

        $email = $this->get_email_from_request();
        if ($email) {
            $wpdb->update(NEWSLETTER_USERS_TABLE, array('unsub_email_id' => (int) $email_id, 'unsub_time' => time()), array('id' => $user->id));
        }

        $this->send_unsubscribed_email($user);

        NewsletterSubscription::instance()->notify_admin($user, 'Newsletter unsubscription');

        return $user;
    }

    function send_unsubscribed_email($user, $force = false) {
        $options = $this->get_options('', $this->get_user_language($user));
        if (!$force && !empty($options['unsubscribed_disabled'])) {
            return true;
        }

        $message = $options['unsubscribed_message'];
        $subject = $options['unsubscribed_subject'];

        return NewsletterSubscription::instance()->mail($user->email, $this->replace($subject, $user), $this->replace($message, $user));
    }

    /**
     * Reactivate the subscriber extracted from the request setting his status 
     * to confirmed and logging. No email are sent. Dies on subscriber extraction failure.
     * 
     * @return TNP_User
     */
    function reactivate() {
        $user = $this->get_user_from_request(true);

        $user = $this->set_user_status($user, TNP_User::STATUS_CONFIRMED);
        $this->add_user_log($user, 'reactivate');

        return $user;
    }

    function hook_newsletter_replace($text, $user, $email) {

        if (!$user) {
            return $text;
        }

        $text = $this->replace_url($text, 'UNSUBSCRIPTION_CONFIRM_URL', $this->build_action_url('uc', $user, $email));
        $text = $this->replace_url($text, 'UNSUBSCRIPTION_URL', $this->build_action_url('u', $user, $email));
        $text = $this->replace_url($text, 'REACTIVATE_URL', $this->build_action_url('reactivate', $user, $email));

        return $text;
    }

    function hook_newsletter_page_text($text, $key, $user = null) {

        $options = $this->get_options('', $this->get_current_language($user));
        if ($key == 'unsubscribe') {
            if (!$user) {
                return 'Subscriber not found.';
            }
            return $options['unsubscribe_text'];
        }
        if ($key == 'unsubscribed') {
            if (!$user) {
                return $options['error_text'];
            }
            return $options['unsubscribed_text'];
        }
        if ($key == 'reactivated') {
            if (!$user) {
                return $options['error_text'];
            }
            return $options['reactivated_text'];
        }
        if ($key == 'unsubscription_error') {
            return $options['error_text'];
        }
        return $text;
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        // Migration code
        if (empty($this->options) || empty($this->options['unsubscribe_text'])) {
            // Options of the subscription module (worng name, I know)
            $options = get_option('newsletter');
            $this->options['unsubscribe_text'] = $options['unsubscription_text'];

            $this->options['reactivated_text'] = $options['reactivated_text'];

            $this->options['unsubscribed_text'] = $options['unsubscribed_text'];
            $this->options['unsubscribed_message'] = $options['unsubscribed_message'];
            $this->options['unsubscribed_subject'] = $options['unsubscribed_subject'];

            $this->save_options($this->options);
        }
    }

    function admin_menu() {
        $this->add_admin_page('index', 'Unsubscribe');
    }

}

NewsletterUnsubscription::instance();
