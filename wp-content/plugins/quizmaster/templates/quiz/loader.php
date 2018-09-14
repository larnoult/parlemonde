<div class="qm-quiz-content" id="quizMaster_<?php echo $view->quiz->getId(); ?>">

  <?php

		$quizData = $view->showQuizBox( $view->question_count );

		// output quiz header
		quizmaster_get_template('quiz/header.php',
			array(
				'view'          => $view,
				'questionCount' => $view->question_count,
			)
		);

		// output quiz body
		quizmaster_get_template('quiz/body.php',
			array(
				'view'          => $view,
				'quiz'					=> $view->quiz,
				'questionCount' => $view->question_count,
			)
		);

		// output quiz footer
		quizmaster_get_template('quiz/footer.php',
			array(
				'view'          => $view,
				'quiz'					=> $view->quiz,
				'questionCount' => $view->question_count,
			)
		);

  ?>
</div>

<script type="text/javascript">
		jQuery(document).ready(function ($) {

			$('#quizMaster_<?php echo $view->quiz->getId(); ?>').quizmaster({

				quizId: <?php echo (int)$view->quiz->getId(); ?>,
				mode: <?php echo (int)$view->quiz->getQuizModus(); ?>,
				globalPoints: <?php echo (int)$quizData['globalPoints']; ?>,
				timeLimit: <?php echo (int)$view->quiz->getTimeLimit(); ?>,
				options: <?php echo $view->createOption(); ?>,
				qpp: <?php echo $view->quiz->getQuestionsPerPage(); ?>,
				catPoints: <?php echo json_encode($quizData['catPoints']); ?>,
				formPos: <?php echo (int)$view->quiz->getFormShowPosition(); ?>,
				showReviewBox: 0,
				lbn: <?php echo json_encode(($view->quiz->isShowReviewQuestion() && !$view->quiz->isQuizSummaryHide()) ? $view->_buttonNames['quiz_summary'] : $view->_buttonNames['finish_quiz']); ?>,
				json: <?php echo json_encode($quizData['json']); ?>

			});

		});
</script>
