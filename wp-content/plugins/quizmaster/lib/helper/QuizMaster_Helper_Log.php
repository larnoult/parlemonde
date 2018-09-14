<?php

class QuizMaster_Helper_Log {

  const LOGGING_ACTIVE_FIELD = "qm_log_activate";
  const LOGGING_MESSAGES_FIELD = "qm_log_messages";
  const LOGGING_MESSAGES_ENTRY = "qm_log_entry";

  public function isActive() {
    return get_field( self::LOGGING_ACTIVE_FIELD, 'option' );
  }

  public function log( $message ) {

    $msg = '';

    if( !$this->isActive() ) {
      return; // logging deactivated
    }

    if( is_array( $message ) || is_object( $message )) {
      ob_start();
      var_dump( $message );
      $msg .= "\r" . ob_get_clean();
    } else {
      $msg .= "\r" . $message;
    }

    // save message
    add_row( self::LOGGING_MESSAGES_FIELD, array( self::LOGGING_MESSAGES_ENTRY => $msg ), 'option' );

  }


}
