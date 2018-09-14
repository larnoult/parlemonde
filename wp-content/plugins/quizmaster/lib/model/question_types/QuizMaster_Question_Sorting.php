<?php

class QuizMaster_Question_Sorting extends QuizMaster_Model_Question {

  public function answerModelName() {
    return "QuizMaster_Answer_Sorting";
  }

  public function render( $quiz = false ) {
    quizmaster_get_template('question/sorting.php',
      array(
        'question' => $this,
				'quiz' => $quiz,
      )
    );
  }

}
