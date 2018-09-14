<div class="qm-review-box qm-hidden">

	<div class="quizMaster_reviewQuestion">
		<ol>
			<?php for ($xy = 1; $xy <= $questionCount; $xy++) { ?>
					<li>
						<?php echo $xy; ?>
					</li>
			<?php } ?>
		</ol>
	</div>

	<!-- Review Legend -->
	<div class="quizMaster_reviewLegend">
		<ol>
			<li>
				<span class="quizMaster_reviewColor" style="background-color: #6CA54C;"></span>
				<span class="quizMaster_reviewText"><?php _e('Answered', 'quizmaster'); ?></span>
			</li>
			<li>
				<span class="quizMaster_reviewColor" style="background-color: #FFB800;"></span>
				<span class="quizMaster_reviewText"><?php _e('Review', 'quizmaster'); ?></span>
			</li>
		</ol>
	</div>

	<!-- Question Review -->
	<div>
		<?php if ($view->quiz->getQuizModus() != QuizMaster_Model_Quiz::QUIZ_MODUS_SINGLE) { ?>
			<input type="button" name="review" value="<?php _e('Review question', 'quizmaster'); ?>" class="qm_button">
			<?php if (!$view->quiz->isQuizSummaryHide()) { ?>
				<input type="button" name="quizSummary" value="<?php echo $view->_buttonNames['quiz_summary']; ?>" class="qm-button">
			<?php } ?>
		<?php } ?>
	</div>

</div>
