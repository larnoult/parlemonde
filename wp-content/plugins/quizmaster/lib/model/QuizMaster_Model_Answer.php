<?php

class QuizMaster_Model_Answer extends QuizMaster_Model_Model {

		protected $_question_id = '';
    protected $_answer  = '';
    protected $_points  = 1;
    protected $_correct = false;

		public function setQuestionId( $_question_id ) {
			$this->_question_id = (int) $_question_id;
		}

		public function getQuestionId() {
			return $this->_question_id;
		}

    public function setAnswer( $_answer ) {
      $this->_answer = (string) $_answer;
    }

    public function getAnswer() {
      return $this->_answer;
    }

    public function setPoints( $_points ) {
      $this->_points = (int) $_points;
      return $this;
    }

    public function getPoints() {
      return $this->_points;
    }

    public function setCorrect( $_correct ) {
      $this->_correct = (bool) $_correct;
      return $this;
    }

    public function isCorrect() {
      return $this->_correct;
    }

		// child answer type class should extend
		public function save( $answers ) {}

}
