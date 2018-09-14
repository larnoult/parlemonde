<div style="display: none;" class="quizMaster_quiz">

  <ol class="quizMaster_list">
    <?php
      $index = 0;
      foreach ($view->question as $question) :
        $index++;
    ?>

    <li class="quizMaster_listItem" style="display: none;" data-pos="<?php print $index; ?>">

        <div class="quizMaster_question_page" <?php $view->isDisplayNone($view->quiz->getQuizModus() != QuizMaster_Model_Quiz::QUIZ_MODUS_SINGLE && !$view->quiz->isHideQuestionPositionOverview()); ?> >
            <?php printf(__('Question %s of %s', 'quizmaster'), '<span>' . $index . '</span>',
                '<span>' . $questionCount . '</span>'); ?>
        </div>
        <h5 style="<?php echo $view->quiz->isHideQuestionNumbering() ? 'display: none;' : 'display: inline-block;' ?>"
            class="quizMaster_header">
            <span><?php echo $index; ?></span>. <?php _e('Question', 'quizmaster'); ?>
        </h5>

        <?php
          // render question
          // @TODO support: $view->quiz->isHideAnswerMessageBox()
          $question->render( $view->quiz );
        ?>

        <?php
          // question buttons
          print quizmaster_get_template('quiz/question-buttons.php',
            array(
              'question' => $question,
              'quiz' => $view->quiz,
            )
          );
        ?>

    </li>

    <?php endforeach; ?>
  </ol>

</div>
