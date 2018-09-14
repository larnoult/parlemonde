<?php
/**
 * Override parent 'WP_Quiz' class with personality quiz specific markup,
 */

class WP_Quiz_Personality_Quiz extends WP_Quiz {

	public function get_html_questions() {

		$questions_html 	= '';

		if ( ! empty( $this->questions ) ) {

			if ( 'single' === $this->settings['question_layout'] ) {
				$ad_display = 'block';
				$display_continue = 'none';
			} else {
				$ad_display = 'none';
				$display_continue = 'block';
			}

			foreach ( $this->questions as $key => $question ) {
				$media_html = '';
				if ( 'image' === $question['mediaType'] ) {
					if ( ! empty( $question['image'] ) ) {
						$media_html = '<div class="wq_questionImage"><img src="' . $question['image'] . '" /><span>' . $question['imageCredit'] . '</span></div>';
					}
				} else if ( 'video' === $question['mediaType'] ) {
					if ( ! empty( $question['video'] ) ) {
						if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $question['video'], $match ) ) {
							if ( ! empty( $match[1] ) ) {
								$media_html = '<div class="ui embed media-' . $key . '" data-source="youtube" data-id="' . $match[1] . '"  data-placeholder="' . $question['imagePlaceholder'] . '" data-icon="video play"></div>';
							}
						} else if ( preg_match( '#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $question['video'], $match ) ) {
							if ( ! empty( $match[1] ) ) {
								$media_html = '<div class="ui embed media-' . $key . '" data-source="vimeo" data-id="' . $match[1] . '"  data-placeholder="' . $question['imagePlaceholder'] . '" data-icon="video play"></div>';
							}
						} else {
							$media_html = '<div class="ui embed media-' . $key . '" data-url="' . $question['video'] . '" data-placeholder="' . $question['imagePlaceholder'] . '" data-icon="video play"></div>';
						}
						$autoplay = ! empty( $question['imagePlaceholder'] ) ? 'true':'false';
						$media_html .= '<script>jQuery(document).ready(function($) {$(".ui.media-' . $key . '").embed({"autoplay":' . $autoplay . '});});</script>';
					}
				}

				$answers_html = '';
				if ( isset( $question['answers'] ) ) {

					if ( $this->settings['rand_answers'] ) {
						shuffle( $question['answers'] );
					}

					$answers_html = '<div class="wq_answersWrapper">';
					$answers_has_image = false;

					foreach ( $question['answers'] as $answer ) {

						if ( ! empty( $answer['image'] ) ) {
							$answers_has_image = true;
							$answers_html = '';
							$answers_html = '<div class="wq_answersWrapper">';
							break;
						}
						$answers_html .= '
							<div class="wq_singleAnswerCtr wq_IsPersonality" style="background-color:' . $this->settings['background_color'] . '; color:' . $this->settings['font_color'] . ';">
								<textarea style="display:none;" class="wq_singleAnswerResultCtr" >' . json_encode( isset( $answer['results'] ) ? $answer['results'] : '' ) . '</textarea>
								<label class="wq_answerTxtCtr">' . $answer['title'] . '</label>
							</div>
						';
					}

					if ( $answers_has_image ) {

						$cols = apply_filters( 'wp_quiz_img_answer_cols', 3 ); // 2 columns if anything else is passed

						if ( 3 === $cols ) {
							$col_class = 'col-md-wq-4';
						} else {
							$col_class = 'col-md-wq-6';
							$cols = 2;
						}

						$i = 0;
						$answers_html .= '<div class="row">';

						foreach ( $question['answers'] as $answer ) {
							$answer_img_html = '';
							$answer_title = '';
							$answer_img_html = '<div class="wq_answerImgCtr"><img src="' . $answer['image'] . '"></div>';
							$answer_title = empty( $answer['title'] ) ? '&nbsp;' : $answer['title'];
							$answers_html .= '
								<div class="' . $col_class . '">
									<div class="wq_singleAnswerCtr wq_IsPersonality wq_hasImage" style="background-color:' . $this->settings['background_color'] . '; color:' . $this->settings['font_color'] . ';">
										<textarea style="display:none;" class="wq_singleAnswerResultCtr" >' . json_encode( isset( $answer['results'] ) ? $answer['results'] : '' ) . '</textarea>
										' . $answer_img_html . '
										<label class="wq_answerTxtCtr">' . $answer_title . '</label>
									</div>
								</div>
							';

							$i++;
							if ( 0 === $i % $cols ) {
								$answers_html .= '</div><div class="row">';
							}
						}
						$answers_html .= '</div>';
					}
					$answers_html .= '</div>';
				}

				$display = 0 === $key ? 'block' : 'none';

				if ( 'single' === $this->settings['question_layout'] ) {
					$display = 'block';
				}
				$questions_html .= '
					<div class="wq_singleQuestionWrapper wq_IsPersonality" data-question-answered="1" style="display:' . $display . ';">
						<div class="wq_singleQuestionCtr">
							<div class="wq_questionTextWrapper quiz-clearfix">
								<div class="wq_questionTextCtr" style="background-color:' . $this->settings['background_color'] . '; color:' . $this->settings['font_color'] . ';">
									<h4>' . $question['title'] . '</h4>
								</div>
							</div>
							<div class="wq_questionMediaCtr">
								' . $media_html . '
							</div>
							<div class="wq_questionAnswersCtr">
								' . $answers_html . '
							</div>
						</div>
						<div class="wq_continue" style="display:none;">
							<button class="wq_btn-continue">' . __( 'Continue &gt;&gt;', 'wp-quiz' ) . '</button>
						</div>
					</div>
				';
			}
		}
		return $questions_html;
	}

	public function get_html_results() {

		$results_html = '';
		$share_html = $this->get_html_share();
		if ( ! empty( $this->results ) ) {
			for ( $i = 0; $i < count( $this->results ); $i++ ) {
				$result_img_html = '';
				if ( ! empty( $this->results[ $i ]['image'] ) ) {
					$result_img_html = '<p><img class="wq_resultImg" src="' . $this->results[ $i ]['image'] . '"/></p>';
				}
				$results_html .= '
					<div style="display:none;" class="wq_singleResultWrapper wq_IsPersonality" data-points="0" data-id="' . $i . '" data-rid="' . $i . '">
						<span class="wq_quizTitle">' . get_the_title( $this->id ) . '</span>
						<div class="wq_resultTitle" data-title="' . urlencode( $this->results[ $i ]['title'] ) . '">' . wp_kses_post( $this->results[ $i ]['title'] ) . '</div>
						' . $result_img_html . '
						<div class="wq_resultDesc">' . wp_kses_post( $this->results[ $i ]['desc'] ) . '</div>
						' . $share_html . '
					</div>
				';
			}
		}

		return $results_html;
	}
}
