<?php

class QuizMaster_Controller_Email {

  private $email  = false;
  private $quiz   = false;
  private $score  = false;

  const QUIZMASTER_EMAIL_TRIGGER_FIELD = 'qm_email_trigger';
  const QUIZMASTER_EMAIL_ENABLED_FIELD = 'qm_email_enabled';

  public function __construct() {
    $this->addEmailTriggers();
    $this->addShortcodes();
    add_filter( quizmaster_get_fields_prefix() . '/load_field/name=qm_email_trigger', array( $this, 'loadEmailTriggerList'));
  }

  public function addShortcodes() {
    add_shortcode('quizdata', array($this, 'quizDataShortcode'));
    add_shortcode('quiztaker_email', array($this, 'quizTakerEmailShortcode'));
  }

  public function quizTakerEmailShortcode() {
    $user = wp_get_current_user();
    return $user->user_email;
  }

  public function quizDataShortcode( $atts ) {

    $content = '';

    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $args = shortcode_atts([
      'data' => '',
    ], $atts, 'quizdata');

    $data = $args['data'];

    switch( $data ) {
      case "quiztitle":
        $content = $this->quiz->getName();
        break;
      case "quizlink":
        $content = '<a href="' . $this->quiz->getPermalink() . '">';
        $content .= $this->quiz->getName();
        $content .= '</a>';
        break;
      case "scorelink":
        if( is_object( $this->score )) {
          $content = '<a href="' . $this->score->getPermalink() . '">';
          $content .= 'Your Quiz Score';
          $content .= '</a>';
        }
        break;
    }

    return $content;
  }

  public function addEmailTriggers() {

    $defaultTriggers = $this->getDefaultEmailTriggers();
    $registeredTriggers = apply_filters('quizmaster_email_triggers', $defaultTriggers);

    foreach( $registeredTriggers as $trigger => $label ) {
      $triggerKey = quizmaster_simplify_key( $trigger );
      $triggerKey = quizmaster_camelize( $triggerKey );
      $callback = array( $this, 'sendEmail' . $triggerKey );
      $callback = apply_filters('quizmaster_email_trigger_callback', $callback, $trigger );
      add_action( $trigger, $callback, 10, 2);
    }

  }

  public function loadEmailTriggerList( $field ) {
    $defaultTriggers = $this->getDefaultEmailTriggers();
    $registeredTriggers = apply_filters('quizmaster_email_triggers', $defaultTriggers);
    foreach( $registeredTriggers as $trigger => $label ) {
      $field['choices'][ $trigger ] = $label;
    }
    return $field;
  }

  private function getDefaultEmailTriggers() {

    return array(
      'quizmaster_completed_quiz' => 'Quiz Completion',
      'quizmaster_completed_quiz_100_percent' => 'Quiz Completion with 100% Score',
    );

  }

  public function send() {

    quizmaster_log($this->email);

    $send = wp_mail(
      $this->email->getRecipients(),
      $this->email->getSubject(),
      $this->email->getMessage(),
      $this->email->getHeaders()
    );

  }

  public function setMessage() {

    $msg = $this->parseTemplate();
    $this->email->setMessage( $msg );

  }

  public function parseTemplate() {
    $templateName = str_replace( '_', '-', $this->email->getKey() );

    if( $this->email->getType() == 'plain' ) {
      $templateName = 'plain/' . $templateName;
    }

    $template = 'emails/' . $templateName;
    $content = quizmaster_parse_template( $template . '.php' );
    return do_shortcode( $content );
  }

  public function sendEmailCompletedQuiz( $quiz, $score ) {

    $trigger = 'quizmaster_completed_quiz';
    $emailPosts = $this->getEmailsByTrigger( $trigger );

    // log send details
    quizmaster_log( array(
      'class'   => 'QuizMaster_Controller_Email',
      'method'  => 'sendEmailCompletedQuiz',
      'line'    => 124,
      'score'   => $score
    ));

    foreach( $emailPosts as $emailPost ) {
      $this->email  = new QuizMaster_Model_Email( $emailPost->ID );
      $this->quiz   = $quiz;
      $this->score  = $score;
      $this->setMessage();
      $this->send();
    }

  }

  public function sendEmailCompletedQuiz100Percent( $quiz, $score ) {

    $trigger = 'quizmaster_completed_quiz_100_percent';
    $emailPosts = $this->getEmailsByTrigger( $trigger );

    foreach( $emailPosts as $emailPost ) {
      $this->email  = new QuizMaster_Model_Email( $emailPost->ID );
      $this->quiz   = $quiz;
      $this->score  = $score;
      $this->setMessage();
      $this->send();
    }

  }

  public function getEmailsByTrigger( $trigger ) {
    $posts = get_posts(array(
    	'numberposts'	=> -1,
    	'post_type'		=> 'quizmaster_email',
    	'meta_query'	=> array(
    		'relation'		=> 'AND',
    		array(
    			'key'	 	    => self::QUIZMASTER_EMAIL_TRIGGER_FIELD,
    			'value'	  	=> $trigger,
    			'compare' 	=> '=',
    		),
    		array(
    			'key'	  	  => self::QUIZMASTER_EMAIL_ENABLED_FIELD,
    			'value'	  	=> '1',
    			'compare' 	=> '=',
    		),
    	),
    ));
    return $posts;
  }

  public function getEmailsByKey( $key ) {
    $posts = get_posts(array(
    	'numberposts'	=> -1,
    	'post_type'		=> 'quizmaster_email',
    	'meta_query'	=> array(
    		'relation'		=> 'AND',
    		array(
    			'key'	 	    => 'qm_email_key',
    			'value'	  	=> $key,
    			'compare' 	=> '=',
    		),
    	),
    ));
    return $posts;
  }

  /**
   * Check if an email post exists for the given email key
   * @param  string $key Email key as defined in email settings
   * @return boolean true if the email exist, or false if it does not
   */
  public function emailExists( $key ) {
    $emails = $this->getEmailsByKey( $key );
    if( !empty( $emails )) {
      return true;
    }
    return false;
  }

}
