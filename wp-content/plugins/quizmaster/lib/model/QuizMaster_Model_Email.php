<?php

class QuizMaster_Model_Email extends QuizMaster_Model_Model {

    protected $_id = 0;
    protected $_from = '';
    protected $_html = false;
    protected $_message = '';
    protected $_headers = '';
    protected $_key = '';
    protected $_enabled = '';
    protected $_trigger = '';
    protected $_recipients = '';
    protected $_subject = '';
    protected $_type = 'html';

    public function getFieldPrefix() {
      return 'qm_email_';
    }

    public function getKey() {
      return $this->_key;
    }

    public function setId($_id) {
      $this->_id = (int)$_id;
      return $this;
    }

    public function getId() {
      return $this->_id;
    }

    public function getHeaders() {
      return $this->_headers;
    }

    public function setHeaders() {

      // set content-type
      if( $this->getType() == 'plain' ) {
        $contentType = 'Content-Type: text; charset=UTF-8';
      } else {
        $contentType = 'Content-Type: text/html; charset="UTF-8"';
      }

      $headers = array(
        'From: "' . get_bloginfo('name') . '" <'. $this->getFrom() .'>' ,
        'Reply-To: "' . get_bloginfo('name') . '" <' . $this->getFrom() . '>' ,
        'X-Mailer: PHP/' . phpversion() ,
        'MIME-Version: 1.0' ,
        $contentType ,
      );

      $headers = implode( "\r\n" , $headers );
      $this->_headers = $headers;

    }

    public function setFrom($_from) {
      $this->_from = (string)$_from;
      return $this;
    }

    public function getFrom() {
      return $this->_from;
    }

    public function getSubject() {
      return $this->_subject;
    }

    public function setHtml($_html) {
      $this->_html = (bool)$_html;
      return $this;
    }

    public function isHtml() {
      return $this->_html;
    }

    public function setMessage($_message) {
      $this->_message = (string)$_message;
      return $this;
    }

    public function setKey( $_key ) {
      $this->_key = (string)$_key;
      return $this;
    }

    public function setEnabled( $_enabled ) {
      $this->_enabled = (bool)$_enabled;
      return $this;
    }

    public function setTrigger( $_trigger ) {
      $this->_trigger = (string)$_trigger;
      return $this;
    }

    public function setRecipients( $_recipients ) {
      $this->_recipients = (string)$_recipients;
      return $this;
    }

    public function getRecipients() {
      // parse any shortcodes that return recipients
      $recipients = do_shortcode( $this->_recipients );
      return $this->validateEmailList( $recipients );
    }

    // validates each email in a list and returns a list only of valid
    private function validateEmailList( $emailList ) {
      $validEmails = '';
      $emailList = str_replace(' ', '', $emailList);
      $emailsArray = explode( ',', $emailList );
      foreach( $emailsArray as $e ) {
        if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
          $validEmails .= $e . ',';
        }
      }
      return substr( $validEmails, 0, -1);
    }

    public function setSubject( $_subject ) {
      $this->_subject = (string)$_subject;
      return $this;
    }

    public function setType( $_type ) {
      $this->_type = (string)$_type;
    }

    public function getType() {
      return $this->_type;
    }

    public function getMessage() {
      return $this->_message;
    }

    /*
     * After model loaded
     */
    public function afterSetModel() {
      $this->setHeaders();
    }

}
