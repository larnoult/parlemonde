<div class="qm-quiz-start-box">

	<div class="qm-quiz-text">
		<?php echo do_shortcode(apply_filters('comment_text', $view->quiz->getText())); ?>
	</div>

	<div class="qm-start-button-wrap">
		<input class="qm-button qm-start-button" type="button" value="<?php echo $view->_buttonNames['start_quiz']; ?>" name="startQuiz">
	</div>

</div>
