<?php

class QuizMaster_Question_Free extends QuizMaster_Model_Question {

  public function answerModelName() {
    return "QuizMaster_Answer_Free";
  }

  public function render( $quiz = false ) {
    quizmaster_get_template('question/free.php',
      array(
        'question' => $this,
				'quiz' => $quiz,
      )
    );
  }

}
