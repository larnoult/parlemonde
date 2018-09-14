<?php

class QuizMaster_Question_FillBlank extends QuizMaster_Model_Question {

  public function answerModelName() {
    return "QuizMaster_Answer_FillBlank";
  }

  public function render( $quiz = false ) {
    quizmaster_get_template('question/fill_blank.php',
      array(
        'question' => $this,
        'quiz' => $quiz,
      )
    );
  }

  public function fetchCloze($answer_text) {

      preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $answer_text, $matches, PREG_SET_ORDER);

      $data = array();

      foreach ($matches as $k => $v) {
        $text = $v[1];
        $points = !empty($v[2]) ? (int)$v[2] : 1;
        $rowText = $multiTextData = array();
        $len = array();

        if (preg_match_all('#\[(.*?)\]#im', $text, $multiTextMatches)) {
          foreach ($multiTextMatches[1] as $multiText) {
            $x = mb_strtolower(trim(html_entity_decode($multiText, ENT_QUOTES)));

            $len[] = strlen($x);
            $multiTextData[] = $x;
            $rowText[] = $multiText;
          }
        } else {
          $x = mb_strtolower(trim(html_entity_decode($text, ENT_QUOTES)));

          $len[] = strlen($x);
          $multiTextData[] = $x;
          $rowText[] = $text;
        }

        $a = '<span class="quizMaster_cloze"><input data-wordlen="' . max($len) . '" type="text" value=""> ';
        $a .= '<span class="quizMaster_clozeCorrect" style="display: none;">(' . implode(', ',
                $rowText) . ')</span></span>';

        $data['correct'][] = $multiTextData;
        $data['points'][] = $points;
        $data['data'][] = $a;
      }

      $data['replace'] = preg_replace('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', '@@quizMasterCloze@@', $answer_text);

      quizmaster_log( $data );

      return $data;
  }

  public function clozeCallback( $t ) {
    $a = array_shift($this->_clozeTemp);
    return $a === null ? '' : $a;
  }

}
