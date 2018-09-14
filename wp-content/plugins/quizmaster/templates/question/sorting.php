<!-- Question Category -->
<?php
  print quizmaster_get_template( 'quiz/category.php', array( 'question' => $question ));
?>

<!-- Question Points -->
<?php
  print quizmaster_get_template( 'quiz/question-points.php', array( 'question' => $question, 'quiz' => $quiz ));
?>

<div class="qm-question">

  <div class="qm-question-text">
    <?php echo do_shortcode(apply_filters('comment_text', $question->getQuestion())); ?>
  </div>

  <ul class="qm-question-list qm-sortable" data-question_id="<?php echo $question->getId(); ?>"
    data-type="<?php echo $question->getAnswerType(); ?>">

    <?php
      $answer_index = 0;
			$sortingAnswers = $question->getAnswerData();
			shuffle( $sortingAnswers );
      foreach ( $sortingAnswers as $v ) :
        $answer_text = $v->getAnswer();
    ?>

      <li class="qm-question-list-item" id="<?php print $v->getAnswerId(); ?>" data-pos="<?php echo $answer_index; ?>">

				<?php echo $answer_text; ?>

      </li>

      <?php $answer_index++; endforeach;?>

  </ul>

  <?php print quizmaster_get_template('quiz/question-response.php', array('question' => $question)); ?>

</div>
