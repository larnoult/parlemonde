<?php


class QuizMaster_Answer_Free extends QuizMaster_Model_Answer {

  public function getKey() {
    return 'free';
  }

  public function getName() {
    return 'Free Answer';
  }

  public function load( $data ) {

    $fieldAnswerData = array(
      'answer' => $data['qmqe_free_choice_answers']
    );
    $answerData[] = new QuizMaster_Model_AnswerTypes( $fieldAnswerData );
    return $answerData;

  }

}
