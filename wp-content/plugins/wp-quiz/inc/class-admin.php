<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class WP_Quiz_Admin {

	/**
	 * The Constructor
	 */
	public function __construct() {

		// Common
		add_action( 'admin_menu', array( $this, 'register_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// List
		add_filter( 'manage_edit-wp_quiz_columns', array( $this, 'wp_quiz_columns' ) );
		add_action( 'manage_wp_quiz_posts_custom_column', array( $this, 'manage_wp_quiz_columns' ), 10, 2 );
		add_filter( 'screen_layout_columns', array( $this, 'screen_layout_columns' ), 10, 2 );

		// Edit
		add_action( 'enter_title_here', array( $this, 'enter_title_here' ) );
		add_action( 'edit_form_after_title', array( $this, 'add_shortcode_before_editor' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'admin_post_wp_quiz', array( $this, 'save_post_form' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * [register_pages description]
	 * @return [type] [description]
	 */
	public function register_pages() {

		$parent = 'edit.php?post_type=wp_quiz';

		// General Settings
		$page_hook = add_submenu_page(
			$parent,
			esc_html__( 'General Settings', 'wp-quiz' ),
			esc_html__( 'General Settings', 'wp-quiz' ),
			'manage_options',
			'wp_quiz_config',
			array( 'WP_Quiz_Page_Config', 'page' )
		);
		add_action( 'load-' . $page_hook, array( 'WP_Quiz_Page_Config', 'load' ) );
		add_action( 'admin_print_styles-' . $page_hook, array( 'WP_Quiz_Page_Config', 'admin_print_styles' ) );

		// Import/Export
		$page_hook = add_submenu_page(
			$parent,
			esc_html__( 'Import/Export', 'wp-quiz' ),
			esc_html__( 'Import/Export', 'wp-quiz' ),
			'manage_options',
			'wp_quiz_ie',
			array( 'WP_Quiz_Page_Import_Export', 'page' )
		);
		add_action( 'load-' . $page_hook, array( 'WP_Quiz_Page_Import_Export', 'load' ) );
		add_action( 'admin_print_styles-' . $page_hook, array( 'WP_Quiz_Page_Import_Export', 'admin_print_styles' ) );

		$page_hook = add_submenu_page(
			$parent,
			esc_html__( 'Get Support for WP Quiz', 'wp-quiz' ),
			esc_html__( 'Support', 'wp-quiz' ),
			'manage_options',
			'wp_quiz_support',
			array( 'WP_Quiz_Page_Support', 'page' )
		);
		add_action( 'load-' . $page_hook, array( 'WP_Quiz_Page_Support', 'load' ) );
		add_action( 'admin_print_styles-' . $page_hook, array( 'WP_Quiz_Page_Support', 'admin_print_styles' ) );
	}

	/**
	 * Register admin JavaScript
	 *
	 * @param  [type] $hook [description]
	 * @return [type]       [description]
	 */
	public function enqueue_scripts( $hook ) {

		global $typenow;

		if ( 'wp_quiz' !== $typenow ) {
			return;
		}

		if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wp_quiz', wp_quiz()->plugin_url() . 'assets/css/new-quiz.css', array(), wp_quiz()->version );
			wp_enqueue_style( 'semantic-checkbox-css', wp_quiz()->plugin_url() . 'assets/css/checkbox.min.css', array(), wp_quiz()->version );
			wp_enqueue_style( 'chosen-css', wp_quiz()->plugin_url() . 'assets/css/chosen.min.css', array(), wp_quiz()->version );
			wp_enqueue_style( 'semantic-embed-css', wp_quiz()->plugin_url() . 'assets/css/embed.min.css', array(), wp_quiz()->version );

			wp_enqueue_script( 'wp_quiz-react', wp_quiz()->plugin_url() . 'assets/js/content.min.js', array( 'jquery', 'semantic-checkbox-js', 'chosen-js', 'wp-color-picker' ), wp_quiz()->version, true );
			wp_enqueue_script( 'wp_quiz-bootstrap', wp_quiz()->plugin_url() . 'assets/js/bootstrap.min.js', array( 'jquery' ), wp_quiz()->version );
			wp_enqueue_script( 'semantic-checkbox-js', wp_quiz()->plugin_url() . 'assets/js/checkbox.min.js', array( 'jquery' ), wp_quiz()->version );
			wp_enqueue_script( 'chosen-js', wp_quiz()->plugin_url() . 'assets/js/chosen.jquery.min.js', array( 'jquery' ), wp_quiz()->version );
			wp_enqueue_script( 'semantic-embed-js', wp_quiz()->plugin_url() . 'assets/js/embed.min.js', array( 'jquery' ), wp_quiz()->version );

			wp_localize_script( 'wp_quiz-react', 'wq_l10n', array(
				'labelSelectType' 		 => esc_html__( 'Select Quiz Type', 'wp-quiz' ),
				'content'				 => esc_html__( 'Content', 'wp-quiz' ),
				'styling'				 => esc_html__( 'Styling', 'wp-quiz' ),
				'settings'				 => esc_html__( 'Settings', 'wp-quiz' ),
				'questions'				 => esc_html__( 'Questions', 'wp-quiz' ),
				'questionSingle'		 => esc_html__( 'Question', 'wp-quiz' ),
				'addQuestion'			 => esc_html__( 'Add Question', 'wp-quiz' ),
				'editQuestion'			 => esc_html__( 'Edit Question', 'wp-quiz' ),
				'results'				 => esc_html__( 'Results', 'wp-quiz' ),
				'addResult' 			 => esc_html__( 'Add Result', 'wp-quiz' ),
				'editResult' 			 => esc_html__( 'Edit Result', 'wp-quiz' ),
				'addAnswer' 			 => esc_html__( 'Add Answer', 'wp-quiz' ),
				'editAnswer'			 => esc_html__( 'Edit Answer', 'wp-quiz' ),
				'addExplanation' 		 => esc_html__( 'Add Explanation', 'wp-quiz' ),
				'editExplanation'		 => esc_html__( 'Edit Explanation', 'wp-quiz' ),
				'edit'					 => esc_html__( 'Edit', 'wp-quiz' ),
				'delete' 				 => esc_html__( 'Delete', 'wp-quiz' ),
				'question'				 => esc_html_x( 'Question Title', 'input label', 'wp-quiz' ),
				'result' 				 => esc_html_x( 'Result Title', 'input label', 'wp-quiz' ),
				'answer' 				 => esc_html_x( 'Answer Title', 'input label', 'wp-quiz' ),
				'explanation'			 => esc_html_x( 'Explanation', 'input label', 'wp-quiz' ),
				'image'					 => esc_html_x( 'Image', 'input label', 'wp-quiz' ),
				'frontImage'			 => esc_html_x( 'Front Image', 'input label', 'wp-quiz' ),
				'backImage'				 => esc_html_x( 'Back Image', 'input label', 'wp-quiz' ),
				'backImageDesc'			 => esc_html_x( 'Back Image Description', 'input label', 'wp-quiz' ),
				'votesUp'				 => esc_html_x( 'Votes Up', 'input label', 'wp-quiz' ),
				'votesDown'				 => esc_html_x( 'Votes Down', 'input label', 'wp-quiz' ),
				'imageCredit'			 => esc_html_x( 'Image Credit', 'input label', 'wp-quiz' ),
				'mediaType' 			 => esc_html_x( 'Media Type', 'input label', 'wp-quiz' ),
				'videoUrl'				 => esc_html_x( 'Youtube/Vimeo/Custom URL', 'input label', 'wp-quiz' ),
				'placeholderImage' 		 => esc_html_x( 'Image Placeholder', 'input label', 'wp-quiz' ),
				'isCorrect' 			 => esc_html_x( 'Is Correct Answer', 'input label', 'wp-quiz' ),
				'minCorrect' 			 => esc_html_x( 'Minimum Correct', 'input label', 'wp-quiz' ),
				'maxCorrect'			 => esc_html_x( 'Maximum Correct', 'input label', 'wp-quiz' ),
				'minScore'				 => esc_html_x( 'Minimum Score', 'input label', 'wp-quiz' ),
				'maxScore'				 => esc_html_x( 'Maximum Score', 'input label', 'wp-quiz' ),
				'desc'					 => esc_html_x( 'Description', 'input label', 'wp-quiz' ),
				'shtDesc'				 => esc_html_x( 'Short Description', 'input label', 'wp-quiz' ),
				'pointsResult'			 => esc_html_x( 'Result Points', 'input label', 'wp-quiz' ),
				'pointsExplain'			 => esc_html__( '(Association: 0-no, 1-normal, 2-strong)', 'wp-quiz' ),
				'lngDesc'				 => esc_html_x( 'Long Description', 'input label', 'wp-quiz' ),
				'cancel' 				 => esc_html__( 'Cancel', 'wp-quiz' ),
				'saveChanges' 			 => esc_html__( 'Save Changes', 'wp-quiz' ),
				'videoEmbed' 			 => esc_html__( 'Video/Custom Embed', 'wp-quiz' ),
				'noMedia'				 => esc_html__( 'No Media', 'wp-quiz' ),
				'generalSettings'		 => esc_html__( 'General Settings', 'wp-quiz' ),
				'randomizeQuestions'	 => esc_html__( 'Randomize Questions', 'wp-quiz' ),
				'randomizeAnswers'		 => esc_html__( 'Randomize Answers', 'wp-quiz' ),
				'restartQuestions'		 => esc_html__( 'Restart Questions', 'wp-quiz' ),
				'promote'				 => esc_html__( 'Promote the plugin', 'wp-quiz' ),
				'embedToggle'			 => esc_html__( 'Show embed code toggle', 'wp-quiz' ),
				'shareButtons'			 => esc_html__( 'Share buttons', 'wp-quiz' ),
				'countDown'				 => esc_html__( 'Countdown timer [Seconds/question]', 'wp-quiz' ),
				'multipleExplain'		 => esc_html__( '(applies to multiple page layout)', 'wp-quiz' ),
				'autoScroll'			 => esc_html__( 'Auto scroll to next question', 'wp-quiz' ),
				'endAnswers'			 => esc_html__( 'Show right/wrong answers at the end of quiz', 'wp-quiz' ),
				'singleExplain'			 => esc_html__( '(applies to single page layout)', 'wp-quiz' ),
				'forceAction'			 => esc_html__( 'Force action to see results', 'wp-quiz' ),
				'forceAction0'			 => esc_html__( 'No Action', 'wp-quiz' ),
				'forceAction1'			 => esc_html__( 'Capture Email', 'wp-quiz' ),
				'forceAction2'			 => esc_html__( 'Facebook Share', 'wp-quiz' ),
				'showAds'				 => esc_html__( 'Show Ads', 'wp-quiz' ),
				'adsAfterN'				 => esc_html__( 'Ads after every nth question', 'wp-quiz' ),
				'repeatAds'				 => esc_html__( 'Repeat Ads', 'wp-quiz' ),
				'adCodes'				 => esc_html__( 'Ad Codes', 'wp-quiz' ),
				'adCodesDesc'			 => esc_html__( 'comma separated codes', 'wp-quiz' ),
				'customizeLayout'		 => esc_html__( 'Customize Layout and Colors', 'wp-quiz' ),
				'questionsLayout'		 => esc_html__( 'Questions layout', 'wp-quiz' ),
				'showAll'				 => esc_html__( 'Show all', 'wp-quiz' ),
				'mutiplePages'			 => esc_html__( 'Mutiple pages', 'wp-quiz' ),
				'chooseSkin'			 => esc_html__( 'Choose skin', 'wp-quiz' ),
				'traditionalSkin'		 => esc_html__( 'Traditional Skin', 'wp-quiz' ),
				'flatSkin'				 => esc_html__( 'Modern Flat Skin', 'wp-quiz' ),
				'progressColor'			 => esc_html__( 'Progress bar color', 'wp-quiz' ),
				'questionColor'			 => esc_html__( 'Question font color', 'wp-quiz' ),
				'questionBgColor'		 => esc_html__( 'Question background color', 'wp-quiz' ),
				'titleColor'			 => esc_html__( 'Result title color', 'wp-quiz' ),
				'titleSize'				 => esc_html__( 'Result title font size', 'wp-quiz' ),
				'titleFont'				 => esc_html__( 'Result title font', 'wp-quiz' ),
				'chooseProfile'			 => esc_html__( 'Select Profile', 'wp-quiz' ),
				'userProfile'			 => esc_html__( 'User Profile Image', 'wp-quiz' ),
				'friendProfile'			 => esc_html__( 'Friend Profile Image', 'wp-quiz' ),
				'animationIn'			 => esc_html__( 'Animation In', 'wp-quiz' ),
				'animationOut'			 => esc_html__( 'Animation Out', 'wp-quiz' ),
				'quizSize'				 => esc_html__( 'Quiz Size', 'wp-quiz' ),
				'custom'				 => esc_html__( 'Custom', 'wp-quiz' ),
				'customSize'			 => esc_html__( 'Custom Size', 'wp-quiz' ),
				'width'					 => esc_html__( 'Width:' , 'wp-quiz' ),
				'height'				 => esc_html__( 'Height:' , 'wp-quiz' ),
				'customExplain'			 => esc_html__( 'set width and height in px', 'wp-quiz' ),
				'fullWidth'				 => esc_html__( 'Full Width (responsive)', 'wp-quiz' ),
				'answers'				 => esc_html__( 'Answers', 'wp-quiz' ),
				'upload'				 => esc_html__( 'Upload', 'wp-quiz' ),
				'uploadImage'			 => esc_html__( 'Upload Image', 'wp-quiz' ),
				'preview'				 => esc_html__( 'Preview', 'wp-quiz' ),
				'previewImage'			 => esc_html__( 'Preview Image', 'wp-quiz' ),
				'previewMedia'			 => esc_html__( 'Preview Video/Media', 'wp-quiz' ),
				'PrePosition'			 => esc_html__( 'Preview/Position', 'wp-quiz' ),
				'prePositionImage'		 => esc_html__( 'Preview Image and set profile position', 'wp-quiz' ),
				'sliderTitle'			 => esc_html__( 'image border radius', 'wp-quiz' ),
				'ajax_url'				 => esc_url( admin_url( 'admin-ajax.php' ) ),
				'proText'				 =>	esc_html__( 'Pro feature', 'wp-quiz' ),
				'buyPro'				 =>	esc_html__( 'Buy WP Quiz Pro', 'wp-quiz' ),
				'proTitle'				 =>	esc_html__( 'Buy WP Quiz Pro', 'wp-quiz' ),
				'proNoticeHeader'		 =>	esc_html__( 'Like WP Quiz Plugin? You will LOVE WP Quiz Pro!', 'wp-quiz' ),
				'proNotice'				 => esc_html__( 'New Quiz type Swiper, Show Ads in the quizzes, Countdown Timer, Open graph integration, Player tracking, Force users to Subscribe to see the results and much more.', 'wp-quiz' ),
				'personalityNotice'		 => esc_html__( 'Please add the Results and save the draft before adding questions', 'wp-quiz' ),
				'fbnameNotice'			 => esc_html__( 'Possible name substiution (%%userfirstname%% = user first name, %%userlastname%% = user last name, %%friendfirstname%% = friend first name, %%friendlastname%% = friend last name)', 'wp-quiz' ),
				'fbprofileNotice'		 => esc_html__( 'Friend profile image will only work if the current quiz player/user has Facebook friends that has also authorized your app id to read their friends list.', 'wp-quiz' ),
			) );
		}
		add_thickbox();
	}

	/**
	 * [enter_title_here description]
	 * @param  [type] $text [description]
	 * @return [type]       [description]
	 */
	public function enter_title_here( $text ) {

		global $typenow;

		if ( 'wp_quiz' !== $typenow ) {
			return $text;
		}

		return esc_html_x( 'Quiz Title', 'new quiz title placeholder', 'wp-quiz' );
	}

	/**
	 * [add_shortcode_before_editor description]
	 */
	public function add_shortcode_before_editor() {

		global $typenow;

		if ( 'wp_quiz' === $typenow && isset( $_GET['post'] ) ) {
			echo '<div class="inside"><strong style="padding: 0 10px;">' . esc_html__( 'Shortcode:', 'wp-quiz' ) . '</strong> <input type="text" value=\'[wp_quiz id="' . trim( $_GET['post'] ) . '"]\' readonly="readonly" /></div>';
		}
	}

	/**
	 * [add_meta_boxes description]
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'quiz-content',
			esc_html_x( 'Quiz', 'metabox title', 'wp-quiz' ),
			array( $this, 'render_meta_box' ),
			'wp_quiz',
			'normal',
			'high'
		);
	}

	/**
	 * [render_meta_box description]
	 * @return [type] [description]
	 */
	public function render_meta_box() {

		$quiz_type = get_post_meta( get_the_ID(), 'quiz_type', true );

		$quiz_types = array(
			'trivia' 		=> esc_html__( 'Trivia', 'wp-quiz' ),
			'personality'	=> esc_html__( 'Personality', 'wp-quiz' ),
			'flip'			=> esc_html__( 'Flip Cards', 'wp-quiz' ),
		);

		$animations = array(
			'fade',
			'scale',
			'fade up',
			'fade down',
			'fade left',
			'fade right',
			'horizontal flip',
			'vertical flip',
			'drop',
			'fly left',
			'fly right',
			'fly up',
			'fly down',
			'swing left',
			'swing right',
			'swing up',
			'swing down',
			'browse',
			'browse right',
			'slide down',
			'slide up',
			'slide left',
			'slide right',
		);

		$share_buttons = array(
			'fb' => esc_html__( 'Facebook', 'wp-quiz' ),
			'tw' => esc_html__( 'Twitter', 'wp-quiz' ),
			'g+' => esc_html__( 'Google +', 'wp-quiz' ),
			'vk' => esc_html__( 'VK', 'wp-quiz' ),
		);

		$defaults = get_option( 'wp_quiz_default_settings' );
		unset( $defaults['share_meta'] );

		foreach ( $defaults as $key => $value ) {

			$defaults[ $key ]['question_layout'] 	 = 'single';
			$defaults[ $key ]['skin'] 			     = 'flat';
			$defaults[ $key ]['bar_color']		     = '#00c479';
			$defaults[ $key ]['font_color']		     = '#444';
			$defaults[ $key ]['background_color']	 = '';
			$defaults[ $key ]['animation_in'] 	     = 'fade';
			$defaults[ $key ]['animation_out']	     = 'fade';
			$defaults[ $key ]['size']				 = 'full';
			$defaults[ $key ]['custom_width']		 = '338';
			$defaults[ $key ]['custom_height']	     = '468';
		}

		wp_localize_script( 'wp_quiz-react', 'quiz', array(
			'types' 			 => $quiz_types,
			'typeSelected' 		 => '' === $quiz_type ? 'trivia' : $quiz_type,
			'nonce' 			 => wp_create_nonce( 'quiz-content' ),
			'questions' 		 => get_post_meta( get_the_ID(), 'questions', true ),
			'results' 			 => get_post_meta( get_the_ID(), 'results', true ),
			'settings' 			 => get_post_meta( get_the_ID(), 'settings', true ),
			'defaultSettings' 	 => $defaults['defaults'],
			'animations'		 => $animations,
			'shareButtons'		 => $share_buttons,
			'defaultSkins'       => array( 'trad' => '#f2f2f2' ),
			'proImage'           => wp_quiz()->plugin_url() . 'assets/image/wp-quiz-pro-small.jpg',
			'proBanner'          => wp_quiz()->plugin_url() . 'assets/image/wp-quiz-pro.jpg',
			'proLink'            => 'https://mythemeshop.com/plugins/wp-quiz-pro/',
		) );
		?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$('#tabs').tab();
				});
			</script>
		<?php
	}

	/**
	 * [save_post description]
	 * @param  [type] $post_id [description]
	 * @param  [type] $post    [description]
	 * @return [type]          [description]
	 */
	public function save_post( $post_id, $post ) {

		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'quiz_nonce' ), 'quiz-content' ) ) {
			return $post_id;
		}

		$post_type = get_post_type_object( $post->post_type );
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		$quiz_type = get_post_meta( $post_id, 'quiz_type', true );
		$new_quiz_type = filter_input( INPUT_POST, 'quiz_type', FILTER_SANITIZE_STRING );
		if ( $new_quiz_type && ( '' === $quiz_type || $quiz_type !== $new_quiz_type ) ) {
			update_post_meta( $post_id, 'quiz_type', $new_quiz_type );
		}

		// @TODO: sanitize all inputs
		$settings = $this->sanitize_checkboxes( $_POST['settings'] );
		$questions = '';
		if ( isset( $_POST['questions'] ) ) {
			$questions = array_values( $_POST['questions'] );
			foreach ( $questions as $key => $question ) {
				if ( isset( $questions[ $key ]['answers'] ) ) {
					$questions[ $key ]['answers']  = array_values( $question['answers'] );
				}
			}
		}

		update_post_meta( $post_id, 'questions', $questions );
		update_post_meta( $post_id, 'settings', $settings );

		if ( isset( $_POST['results'] ) && ! empty( $_POST['results'] ) ) {
			update_post_meta( $post_id, 'results', $_POST['results'] );
		}
	}

	/**
	 * [sanitize_checkboxes description]
	 * @param  [type] $post [description]
	 * @return [type]       [description]
	 */
	public function sanitize_checkboxes( $post ) {

		$settings_key = array(
			'rand_questions',
			'rand_answers',
			'restart_questions',
			'promote_plugin',
			'embed_toggle',
			'show_ads',
			'show_countdown',
			'timer',
			'auto_scroll',
			'repeat_ads',
		);

		foreach (  $settings_key as  $key ) {
			if ( isset( $post[ $key ] ) && '1' === $post[ $key ] ) {
				$post[ $key ] = 1;
			} else {
				$post[ $key ] = 0;
			}
		}

		return $post;
	}

	/**
	 * [wp_quiz_columns description]
	 *
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	public function wp_quiz_columns( $columns ) {

		$new_columns['cb']        = '<input type="checkbox" />';
		$new_columns['title']     = esc_html__( 'Quiz Name', 'wp-quiz' );
		$new_columns['shortcode'] = esc_html__( 'Shortcode', 'wp-quiz' );
		$new_columns['type'] = esc_html__( 'Quiz type', 'wp-quiz' );
		$new_columns['date'] = esc_html__( 'Date', 'wp-quiz' );

		return $new_columns;
	}

	/**
	 * [manage_wp_quiz_columns description]
	 *
	 * @param  [type] $column_name [description]
	 * @param  [type] $id          [description]
	 * @return [type]              [description]
	 */
	public function manage_wp_quiz_columns( $column_name, $id ) {

		global $wpdb;
		$type = get_post_meta( $id, 'quiz_type', true );

		switch ( $column_name ) {

			case 'shortcode':
				echo '<div class="field"><input type="text" readonly value="[wp_quiz id=&quot;' . $id . '&quot;]" onClick="this.select();" style="width:100%;"></div>';
				break;
			case 'type':
				if ( $type ) {
					echo ucfirst( str_replace( '_', ' ', $type ) );
				}
				break;
		}
	}

	/**
	 * [screen_layout_columns description]
	 * @param  [type] $columns   [description]
	 * @param  [type] $screen_id [description]
	 * @return [type]            [description]
	 */
	public function screen_layout_columns( $columns, $screen_id ) {

		if ( 'wp_quiz_page_wp_quiz_config' === $screen_id ) {
			$columns['wp_quiz_page_wp_quiz_config'] = 2;
		} else if ( 'wp_quiz_page_wp_quiz_ie' === $screen_id ) {
			$columns['wp_quiz_page_wp_quiz_ie'] = 2;
		}

		return $columns;
	}

	/**
	 * [save_post_form description]
	 * @return [type] [description]
	 */
	public function save_post_form() {

		// Allowed Pages
		if ( ! in_array( $_POST['page'], array( 'wp_quiz_config' ) ) ) {
			wp_die( esc_html__( 'Cheating, huh?', 'wp-quiz' ) );
		}

		// Check nonce
		check_admin_referer( $_POST['page'] . '_page' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Cheating, huh?', 'wp-quiz' ) );
		}

		// Call method to save data
		$location = '';
		if ( 'wp_quiz_config' === $_POST['page'] ) {
			WP_Quiz_Page_Config::save_post_form();
			$location = admin_url() . 'edit.php?post_type=wp_quiz&page=wp_quiz_config';
		}

		// Back to topic
		$location = add_query_arg( 'message', 3, $location );
		wp_safe_redirect( $location );

		exit;
	}
}
