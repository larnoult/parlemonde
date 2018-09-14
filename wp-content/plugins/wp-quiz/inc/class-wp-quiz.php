<?php
/**
 * Generic WP_Quiz class. Extended by library specific classes.
 */
class WP_Quiz {

	/**
	 * quiz ID
	 */
	public $id = 0;

	/**
	 * quiz settings
	 */
	public $settings = array();

	/**
	 * quiz questions
	 */
	public $questions = array();

	/**
	 * quiz results
	 */
	public $results = array();

	/**
	 * quiz type
	 */
	public $type = '';

	/**
	 * unique identifier
	 */
	public $identifier = 0;

	/**
	 * Constructor
	 */
	public function __construct( $id ) {

		$this->id         = $id;
		$this->settings   = get_post_meta( $id, 'settings', true );
		$this->questions  = get_post_meta( $id, 'questions', true );
		$this->results    = get_post_meta( $id, 'results', true );
		$this->type       = get_post_meta( $id, 'quiz_type', true );
		$this->identifier = 'wp_quiz_' . $this->id;
	}

	/**
	 * @return string unique identifier for quiz
	 */
	protected function get_identifier() {
		return $this->identifier;
	}

	/**
	 * Output the HTML
	 *
	 * @return string HTML
	 */
	public function render_public_quiz() {

		$html[] = '<!-- wp quiz -->';
		$html[] = '<div class="wq_quizCtr ' . $this->settings['question_layout'] . ' ' . $this->type . '_quiz" ' . $this->get_data_attrs() . '>';
		$html[] = '   	<div class="wq_quizProgressBarCtr">';
		$html[] = '        ' . $this->get_html_progress_bar();
		$html[] = '   	</div>';
		$html[] = '   	<div class="wq_questionsCtr" >';
		$html[] = '        ' . $this->get_html_questions();
		$html[] = '   	</div>';
		$html[] = '   	<div class="wq_resultsCtr">';
		$html[] = '        ' . $this->get_html_results();
		$html[] = '   	</div>';
		$html[] = '   	<!-- promote link -->';
		$html[] = '        ' . $this->get_html_promote_link();
		$html[] = '   	<!--// promote link-->';
		$html[] = '   	<!-- retake button -->';
		$html[] = '        ' . $this->get_html_retake_button();
		$html[] = '   	<!--// retake button-->';
		$html[] = '</div>';
		$html[] = '<!--// wp quiz-->';

		$wp_quiz = implode( "\n", $html );

		$wp_quiz = apply_filters( 'wp_quiz_output', $wp_quiz, $this->id, $this->settings );

		return $wp_quiz;
	}

	public function get_data_attrs() {
		global $post;

		$data  = '';
		$data .= 'data-current-question="0" ';
		$data .= 'data-questions-answered="0" ';
		$data .= 'data-questions="' . count( $this->questions ) . '" ';
		$data .= 'data-transition_in="' . $this->settings['animation_in'] . '" ';
		$data .= 'data-transition_out="' . $this->settings['animation_out'] . '" ';
		$data .= 'data-correct-answered="0" ';
		$data .= 'data-quiz-pid="' . $this->id . '" ';
		$data .= 'data-share-url="' . get_permalink( $post->ID ) . '" ';
		$data .= 'data-post-title="' . get_the_title( $post->ID ) . '" ';
		$data .= 'data-retake-quiz="' . $this->settings['restart_questions'] . '" ';
		$data .= 'data-question-layout="' . $this->settings['question_layout'] . '" ';
		$data .= 'data-featured-image="' . wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ) . '" ';
		$data .= 'data-excerpt="' . get_post_field( 'post_excerpt', $this->id ) . '" ';
		$data .= 'data-ajax-url="' . admin_url( 'admin-ajax.php' ) . '" ';
		$data .= 'data-auto-scroll="' . $this->settings['auto_scroll'] . '" ';

		$data = apply_filters( 'wp_quiz_data_attrs', $data, $this->id, $this->settings );

		return $data;
	}

	public function get_html_progress_bar() {

		$display = 'single' === $this->settings['question_layout'] ? 'none' : 'block';
		$display = 'swiper' === $this->type ? 'none' : $display;
		$html[]  = '<!-- progress bar -->';
		$html[]  = '<div class="wq_quizProgressBarCtr" style="display:' . $display . '">';
		$html[]  = '<div class="wq_quizProgressBar">';
		$html[]  = '<span style="background-color:' . $this->settings['bar_color'] . '" class="wq_quizProgressValue"></span>';
		$html[]  = '</div>';
		$html[]  = '</div>';
		$html[]  = '<!--// progress bar-->';

		$progress_bar = implode( "\n", $html );

		return $progress_bar;
	}

	public function get_html_share() {

		$html[] = '<!-- social share -->';
		$html[] = '<div class="wq_shareCtr">';
		if ( isset( $this->settings['share_buttons'] ) ) {
			$share_buttons = $this->settings['share_buttons'];
			$html[]        = '<p style="font-size:14px;">' . esc_html__( 'Share your Results :', 'wp-quiz-pro' ) . '</p>';

			if ( in_array( 'fb', $share_buttons ) ) {
				$html[] = '<button class="wq_shareFB"><i class="sprite sprite-facebook"></i><span>' . esc_html__( 'Facebook', 'wp-quiz-pro' ) . '</span></button>';
			}
			if ( in_array( 'tw', $share_buttons ) ) {
				$html[] = '<button class="wq_shareTwitter"><i class="sprite sprite-twitter"></i><span>' . esc_html__( 'Twitter', 'wp-quiz-pro' ) . '</span></button>';
			}
			if ( in_array( 'g+', $share_buttons ) ) {
				$html[] = '<button class="wq_shareGP"><i class="sprite sprite-google-plus"></i><span>' . esc_html__( 'Google+', 'wp-quiz-pro' ) . '</span></button>';
			}
			if ( in_array( 'vk', $share_buttons ) ) {
				$html[] = '<button class="wq_shareVK"><i class="sprite sprite-vk"></i><span>' . esc_html__( 'VK', 'wp-quiz-pro' ) . '</span></button>';
			}
		}
		$html[] = '</div>';
		$html[] = '<!--// social share-->';

		$social_shares = implode( "\n", $html );
		$social_shares = apply_filters( 'wp_quiz_shares', $social_shares, $this->id, $this->settings );

		return $social_shares;

	}

	public function get_html_promote_link() {

		$html           = array();
		$promote_plugin = $this->settings['promote_plugin'];

		if ( $promote_plugin ) {
			$html[] = '<div style="width:100%;text-align:right;" class="wq_promoteQuizCtr" >';
			$html[] = '<span style="font-size:11px;">' . esc_html__( 'Powered by ', 'wp-quiz' ) . '<a href="https://mythemeshop.com/plugins/wp-quiz/" target="_blank">' . __( 'WordPress Quiz Plugin', 'wp-quiz' ) . '</a></span>';
			$html[] = '</div>';
		}

		$promote_link = implode( "\n", $html );
		$promote_link = apply_filters( 'wp_quiz_promote_plugin', $promote_link, $this->id, $this->settings );

		return $promote_link;
	}

	public function get_html_retake_button() {

		$html[] = '<div class="wq_retakeQuizCtr" >';
		$html[] = '<button style="display:none;" class="wq_retakeQuizBtn"><i class="fa fa-undo"></i>&nbsp;' . esc_html__( 'PLAY AGAIN !', 'wp-quiz' ) . '</button>';
		$html[] = '</div>';

		$retake_button = implode( "\n", $html );

		$retake_button = apply_filters( 'wp_quiz_capture_email', $retake_button, $this->id, $this->settings );

		return $retake_button;
	}

	/**
	 * Include quiz assets
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'wp_quiz-front-js', wp_quiz()->plugin_url() . 'assets/js/main.min.js', array( 'jquery', 'semantic-transition-js', 'semantic-embed-js', 'flip-js' ), wp_quiz()->version, true );
		wp_enqueue_script( 'semantic-transition-js', wp_quiz()->plugin_url() . 'assets/js/transition.min.js', array( 'jquery' ), wp_quiz()->version, true );
		wp_enqueue_script( 'semantic-embed-js', wp_quiz()->plugin_url() . 'assets/js/embed.min.js', array( 'jquery' ), wp_quiz()->version, true );
		wp_enqueue_script( 'flip-js', wp_quiz()->plugin_url() . 'assets/js/jquery.flip.min.js', array( 'jquery' ), wp_quiz()->version, true );

		wp_localize_script( 'wp_quiz-front-js', 'wq_l10n', array(
			'correct'         => esc_html__( 'Correct !', 'wp-quiz' ),
			'wrong'           => esc_html__( 'Wrong !', 'wp-quiz' ),
			'captionTrivia'   => esc_html__( 'You got %%score%% out of %%total%%', 'wp-quiz' ),
			'captionTriviaFB' => esc_html__( 'I got %%score%% out of %%total%%, and you?', 'wp-quiz' ),
		));

		// This will be added to the bottom of the page as <head> has already been processed by WordPress sorry.
		wp_enqueue_style( 'semantic-transition-css', wp_quiz()->plugin_url() . 'assets/css/transition.min.css', array(), wp_quiz()->version );
		wp_enqueue_style( 'semantic-embed-css', wp_quiz()->plugin_url() . 'assets/css/embed.min.css', array(), wp_quiz()->version );
		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', false, wp_quiz()->version );
		wp_enqueue_style( 'wp_quiz-front-css', wp_quiz()->plugin_url() . 'assets/css/main.css', false, wp_quiz()->version );
		wp_enqueue_style( 'traditional-skin-css', wp_quiz()->plugin_url() . 'assets/css/traditional-skin.css', array(), wp_quiz()->version );

		do_action( 'wp_quiz_register_public_styles' );
	}
}
