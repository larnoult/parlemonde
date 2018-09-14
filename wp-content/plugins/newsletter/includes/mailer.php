<?php

abstract class TNP_Mailer {

    var $queue = array();
    var $errors = array();
    var $name = '';

    public function __construct($name) {
        $this->name = $name;
    }
    
    abstract public function mail($to, $subject, $message, $headers = null, $enqueue = false, $from = false);

    /**
     * @return WP_Error[] A list of errors associated to flushed messages
     */
    public function flush() {
        $this->queue = array();
    }

    function get_errors() {
        return $this->errors;
    }

    function clear_errors() {
        $this->errors = array();
    }

    /**
     * @return NewsletterLogger
     */
    function get_logger() {
        static $logger = null;
        if (is_null($logger)) {
            $logger = new NewsletterLogger($this->name);
        }

        return $logger;
    }

}
