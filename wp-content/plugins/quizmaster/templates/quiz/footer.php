<div class="quizmaster-container qm-quiz-footer">
	<div class="quizmaster-row">
		<div class="quizmaster-col-6">

			<?php

				if ( $quiz->isShowSkipButton() ) {
				  print quizmaster_get_template( 'quiz/button-skip.php' );
				}

				print quizmaster_get_template( 'quiz/button-back.php' );

			?>

		</div>
		<div class="quizmaster-col-6 right">

			<?php print quizmaster_get_template( 'quiz/button-check.php' ); ?>
			<?php print quizmaster_get_template( 'quiz/button-next.php' ); ?>
			<?php print quizmaster_get_template( 'quiz/button-finish.php' ); ?>

		</div>
	</div>
</div>

<!-- Hint Modal -->
<div class="qm-hint-modal">
	<div class="qm-hint-close"></div>
	<div class="qm-hint-content"></div>
</div>
