<div class="quizmaster-container qm-results-box">
	<div class="quizmaster-row">
		<div class="quizmaster-col-12 center">

			<div class="qm-results-box-inner">

					<h4 class="quizMaster_header"><?php _e('Quiz Results', 'quizmaster'); ?></h4>
					<?php if (!$view->quiz->isHideResultCorrectQuestion()) { ?>
							<p>
									<?php printf(__('%s of %s questions answered correctly', 'quizmaster'),
											'<span class="quizMaster_correct_answer">0</span>', '<span>' . $questionCount . '</span>'); ?>
							</p>
					<?php }
					if (!$view->quiz->isHideResultQuizTime()) { ?>
							<p class="quizMaster_quiz_time">
									<?php _e('Your time: <span></span>', 'quizmaster'); ?>
							</p>
					<?php } ?>
					<p class="qm-time-limit_expired" style="display: none;">
							<?php _e('Time has elapsed', 'quizmaster'); ?>
					</p>
					<?php if (!$view->quiz->isHideResultPoints()) { ?>
							<p class="quizMaster_points">
									<?php printf(__('You have reached %s of %s points, (%s)', 'quizmaster'), '<span>0</span>',
											'<span>0</span>', '<span>0</span>'); ?>
							</p>
					<?php } ?>
					<?php if ($view->quiz->isShowAverageResult()) { ?>
							<div class="quizMaster_resultTable">
									<table>
											<tbody>
											<tr>
													<td class="quizMaster_resultName"><?php _e('Average score', 'quizmaster'); ?></td>
													<td class="quizMaster_resultValue">
															<div style="background-color: #6CA54C;">&nbsp;</div>
															<span>&nbsp;</span>
													</td>
											</tr>
											<tr>
													<td class="quizMaster_resultName"><?php _e('Your score', 'quizmaster'); ?></td>
													<td class="quizMaster_resultValue">
															<div style="background-color: #F79646;">&nbsp;</div>
															<span>&nbsp;</span>
													</td>
											</tr>
											</tbody>
									</table>
							</div>
					<?php } ?>
					<div class="quizMaster_catOverview" <?php $view->isDisplayNone($view->quiz->isShowCategoryScore()); ?>>
							<h4><?php _e('Categories', 'quizmaster'); ?></h4>

							<div style="margin-top: 10px;">
									<ol>

										<li data-category_id="0">
											<span class="quizMaster_catName"><?php print __('Uncategorized', 'quizmaster') ?></span>
											<span class="quizMaster_catPercent">0%</span>
										</li>

										<?php
											if( !empty( $view->category )) :
												foreach ( $view->category as $catId ) { ?>
													<li data-category_id="<?php echo $catId; ?>">
														<span class="quizMaster_catName"><?php echo get_term( $catId )->name; ?></span>
														<span class="quizMaster_catPercent">0%</span>
													</li>
										<?php } endif; ?>
									</ol>
							</div>
					</div>
					<div>
							<ul class="qm-results-boxList">
								<?php if(!empty( $result['text'] )): foreach ($result['text'] as $resultText) { ?>
										<li style="display: none;">
												<div>
														<?php echo do_shortcode(apply_filters('comment_text', $resultText)); ?>
												</div>
										</li>
								<?php } endif; ?>
							</ul>
					</div>

					<?php do_action('quizmaster_results_before_render_buttons', $view ); ?>

					<!-- Navigate with restart or review questions -->
					<div class="results-box-nav">
							<?php if (!$view->quiz->isBtnRestartQuizHidden()) { ?>
									<input class="qm-button qm-restart-quiz-button" type="button" name="restartQuiz"
												 value="<?php echo $view->_buttonNames['restart_quiz']; ?>">
							<?php }
							if (!$view->quiz->isBtnViewQuestionHidden()) { ?>
									<input class="qm-button qm-question-review-button" type="button" name="reShowQuestion"
												 value="<?php _e('View questions', 'quizmaster'); ?>">
							<?php } ?>
					</div>

					<?php do_action('quizmaster_results_after_render_buttons', $view ); ?>

			</div>

<!-- End grid -->
		</div>
	</div>
</div>
