<?php

class QuizMaster_Controller_Question extends QuizMaster_Controller_Controller {

    protected $_question;

    public function load( $qId ) {

     $qMapper = new QuizMaster_Model_QuestionMapper();
     $this->_question = $qMapper->fetch( $qId );

    }

    public function render() {
      $this->_question->render();
    }

}
