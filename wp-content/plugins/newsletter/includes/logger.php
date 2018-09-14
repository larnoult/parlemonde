<?php
defined('ABSPATH') || exit;

if (!defined('NEWSLETTER_LOG_DIR')) {
    define('NEWSLETTER_LOG_DIR', WP_CONTENT_DIR . '/logs/newsletter/');
}

class NewsletterLogger {

    const NONE = 0;
    const FATAL = 1;
    const ERROR = 2;
    const INFO = 3;
    const DEBUG = 4;

    var $level;
    var $module;
    var $file;

    function __construct($module) {
        $this->module = $module;
        if (defined('NEWSLETTER_LOG_LEVEL')) $this->level = NEWSLETTER_LOG_LEVEL;
        else $this->level = get_option('newsletter_log_level', self::ERROR);

        $secret = get_option('newsletter_logger_secret');
        if (strlen($secret) < 8) {
            $secret = NewsletterModule::get_token(8);
            update_option('newsletter_logger_secret', $secret);
        }

        if (!wp_mkdir_p(NEWSLETTER_LOG_DIR)) {
            $this->level = self::NONE;
        }

        $this->file = NEWSLETTER_LOG_DIR . '/' . $module . '-' . date('Y-m') . '-' . $secret . '.txt';
    }

    /**
     * 
     * @param string|WP_Error|array|stdClass $text
     * @param int $level
     */
    function log($text, $level = self::ERROR) {
        global $current_user;
        
        if ($level != self::FATAL && $this->level < $level) return;
        
        if ($current_user) {
            $user_id = $current_user->ID;
        } else {
            $user_id = 0;
        }

        $time = date('d-m-Y H:i:s ');
        switch ($level) {
            case self::FATAL: $time .= '- FATAL';
                break;
            case self::ERROR: $time .= '- ERROR';
                break;
            case self::INFO: $time .= '- INFO ';
                break;
            case self::DEBUG: $time .= '- DEBUG';
                break;
        }
        if (is_wp_error($text)) {
            /* @var $text WP_Error */
            $text = $text->get_error_message() . ' (' . $text->get_error_code() . ') - ' . print_r($text->get_error_data(), true);
        } else {
            if (is_array($text) || is_object($text)) $text = print_r($text, true);
        }
        
        // The "logs" dir is created on Newsletter constructor.
        $res = @file_put_contents($this->file, $time . ' - m: ' . size_format(memory_get_usage(), 1) . ', u: ' . $user_id . ' - ' . $text . "\n", FILE_APPEND | FILE_TEXT);
        if ($res === false) {
            $this->level = self::NONE;
        }
    }

    function error($text) {
        self::log($text, self::ERROR);
    }

    function info($text) {
        $this->log($text, self::INFO);
    }

    function fatal($text) {
        $this->log($text, self::FATAL);
    }

    function debug($text) {
        $this->log($text, self::DEBUG);
    }

}
