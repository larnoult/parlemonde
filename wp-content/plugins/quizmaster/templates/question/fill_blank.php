<!-- Question Category -->
<?php
  print quizmaster_get_template( 'quiz/category.php', array( 'question' => $question ));
?>

<!-- Question Points -->
<?php
  print quizmaster_get_template( 'quiz/question-points.php', array( 'question' => $question, 'quiz' => $quiz ));
?>

<div class="quizMaster_question">

  <div class="qm-question-text">
    <?php print $question->getQuestion(); ?>
  </div>

  <ul class="qm-question-list" data-question_id="<?php echo $question->getId(); ?>"
    data-type="<?php echo $question->getAnswerType(); ?>">

    <li class="qm-question-list-item" data-pos="0">

      <?php
        $answer_index = 0;
        foreach ($question->getAnswerData() as $v) {
          $answer_text = $v->isHtml() ? $v->getAnswer() : esc_html($v->getAnswer());

          if ($answer_text == '') {
            continue;
          }

          $clozeData = $question->fetchCloze( $answer_text );

          $question->_clozeTemp = $clozeData['data'];
          $cloze = do_shortcode(apply_filters('comment_text',
              $clozeData['replace']));
          $cloze = $clozeData['replace'];

          echo preg_replace_callback('#@@quizMasterCloze@@#im', array($question, 'clozeCallback'), $cloze);

        }

      ?>

    </li>

  </ul>

  <?php print quizmaster_get_template('quiz/question-response.php', array('question' => $question)); ?>

</div>
