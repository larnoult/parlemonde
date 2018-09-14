<?php


class QuizMaster_Answer_Fill_Blank extends QuizMaster_Model_Answer {

  public function getKey() {
    return 'fill_blank';
  }

  public function getName() {
    return 'Fill in the Blank';
  }

  public function load( $data ) {

    $fieldAnswerData = $data['qmqe_sorting_choice_answers'];
    $answerData = array();
    $fieldAnswer = $fieldAnswerData[0];

    // correct answer
    $rep = 'qmqe_sorting_correct_answer_repeater';
    $field = 'qmqe_sorting_correct_answer';
    $answer['answer'] = $fieldAnswer[ $rep ][0][ $field ];
    $answer['correct'] = true;
    $answerData[] = new self( $answer );

    // incorrect answers
    $rep = 'qmqe_sorting_incorrect_answer_repeater';
    $field = 'qmqe_sorting_incorrect_answer';

    foreach( $fieldAnswer[ $rep ] as $ia ) {

      $answer['answer'] = $ia[ $field ];
      $answer['correct'] = false;
      $answerData[] = new self( $answer );

    }

    return $answerData;

  }

}
