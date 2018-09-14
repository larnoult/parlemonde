<?php

class QuizMaster_Model_ScoreQuestion extends QuizMaster_Model_Model {

  protected $_questionId = 0; // question current revision
  protected $_questionIdUnrevised = 0; // question parent id (not the current revision)
  protected $_correctCount = 0;
  protected $_incorrectCount = 0;
  protected $_hintCount = 0;
  protected $_points = 0;
  protected $_questionTime = 0;
  protected $_answerData = null;
  protected $_solvedCount = 0;

  public function outputArray() {
    return array(
      'questionId'          => $this->_questionId,
      'questionIdUnrevised' => $this->_questionIdUnrevised,
      'correctCount'        => $this->_correctCount,
      'incorrectCount'      => $this->_incorrectCount,
      'hintCount'           => $this->_hintCount,
      'points'              => $this->_points,
      'questionTime'        => $this->_questionTime,
      'answerData'          => $this->_answerData,
      'solvedCount'         => $this->_solvedCount,
    );
  }

  public function setQuestionId($_questionId) {
    $this->_questionId = (int)$_questionId;
  }

  public function getQuestionId() {
    return $this->_questionId;
  }

  public function setQuestionIdUnrevised( $_questionIdUnrevised ) {
    $this->_questionIdUnrevised = (int) $_questionIdUnrevised;
  }

  public function getQuestionIdUnrevised() {
    return $this->_questionIdUnrevised;
  }

  public function setCorrectCount($_correctCount) {
    $this->_correctCount = (int)$_correctCount;
  }

  public function getCorrectCount() {
    return $this->_correctCount;
  }

  public function setIncorrectCount($_incorrectCount) {
    $this->_incorrectCount = (int)$_incorrectCount;
  }

  public function getIncorrectCount() {
    return $this->_incorrectCount;
  }

  public function setHintCount($_hintCount) {
    $this->_hintCount = (int)$_hintCount;
  }

  public function getHintCount() {
    return $this->_hintCount;
  }

  public function setPoints($_points) {
    $this->_points = (int)$_points;
  }

  public function getPoints() {
    return $this->_points;
  }

  public function setQuestionTime($_questionTime) {
    $this->_questionTime = (int)$_questionTime;
  }

  public function getQuestionTime() {
    return $this->_questionTime;
  }

  public function setAnswerData($_answerData) {
    $this->_answerData = $_answerData;
  }

  public function getAnswerData() {
    return $this->_answerData;
  }

  public function setSolvedCount($_solvedCount) {
    $this->_solvedCount = (int)$_solvedCount;
  }

  public function getSolvedCount() {
    return $this->_solvedCount;
  }

  // returns the possible points for the question
  public function getPossiblePoints() {
    $question = QuizMaster_Model_QuestionMapper::fetch( $this->getQuestionId() );
    $points = $question->getPoints();
    $correctCount = $this->getCorrectCount();
    $incorrectCount = $this->getIncorrectCount();
    return $points * ( $correctCount + $incorrectCount );
  }

}
