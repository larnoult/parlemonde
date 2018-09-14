<div class="quizmaster-container qm-quiz-body">

	<div class="quizmaster-row center">
		<div class="quizmaster-col-12">

			<?php

				// output quiz boxes
				$view->showTimeLimitBox();
				$view->showCheckPageBox($view->question_count);
				$view->showInfoPageBox();
				$view->showStartQuizBox();
				$view->showLockBox();
				$view->showLoadQuizBox();
				$view->showStartOnlyRegisteredUserBox();
				$view->showReviewBox($view->question_count);
				$view->showQuizAnker();

				$view->showResultBox( $view->result, $view->question_count );

				// enables quizmaster extension to load quiz boxes via action hook
				$view->renderExtensionQuizBoxes();

				// output quiz questions
				quizmaster_get_template('quiz/question-item.php',
					array(
						'view'          => $view,
						'questionCount' => $view->question_count,
					)
				);

			?>

		</div>

	</div>
</div>
