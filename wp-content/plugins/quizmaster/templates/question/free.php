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
		<?php do_action('quizmaster_question_text_before'); ?>
    <?php print $question->getQuestion(); ?>
		<?php do_action('quizmaster_question_text_after'); ?>
  </div>

  <ul class="qm-question-list" data-question_id="<?php echo $question->getId(); ?>"
    data-type="<?php echo $question->getAnswerType(); ?>">

    <?php
      $answer_index = 0;
      foreach ($question->getAnswerData() as $v) {
        $answer_text = $v->isHtml() ? $v->getAnswer() : esc_html($v->getAnswer());
        if ($answer_text == '') {
          continue;
        }
      }
    ?>

    <li class="qm-question-list-item" data-pos="<?php echo $answer_index; ?>">
      <label>
        <input class="quizMaster_questionInput" type="text"
          name="question_<?php echo $question->getId(); ?>"
          style="width: 300px;">
      </label>
    </li>

  </ul>

  <?php print quizmaster_get_template('quiz/question-response.php', array('question' => $question)); ?>

</div>
