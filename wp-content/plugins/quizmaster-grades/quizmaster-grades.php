<?php
/*
Plugin Name: QuizMaster Grades
Plugin URI: http://wordpress.org/extend/plugins/quizmaster-grades
Description: Provides grading support for QuizMaster including passing grade, and ability to setup multiple grades with different achievement messages.
Version: 0.0.1
Author: GoldHat Group
Author URI: https://goldhat.ca
Copyright: GoldHat Group
Text Domain: quizmaster-grades
*/

define( 'QUIZMASTER_GRADES_VERSION', '0.0.1' );
define( 'QUIZMASTER_GRADES_TEMPLATES_PATH', plugin_dir_path( __FILE__ ) . "templates/" );
define( 'QUIZMASTER_GRADES_ASSETS_PATH', plugin_dir_path( __FILE__ ) . "assets/" );
define( 'QUIZMASTER_GRADES_ASSETS_URL', plugin_dir_url( __FILE__ ) . "assets/" );

class QuizMaster_Grades_Extension {

	public function __construct() {
		$this->init();
	}

	public function init() {

		add_filter('quizmaster_extension_registry', array( $this, 'registerExtension' ));
		add_action('init', array( $this, 'load'), 12);
		add_filter('quizmaster_add_fields_after_qmqu_show_category', array( $this, 'addQuizFields' ));
		add_filter('quizmaster_add_fields_after_qm_student_report_page', array( $this, 'addSettingsFields' ));
		add_action('quizmaster_results_before_render_buttons', array( $this, 'renderGradeMessageBox' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'addScripts' ));

	}

	public function load() {

		require_once( plugin_dir_path( __FILE__ ) . "lib/QuizMaster_Grades_Api.php" );
		require_once( plugin_dir_path( __FILE__ ) . "lib/QuizMaster_Grade.php" );

	}

	public function renderGradeMessageBox( $view ) {

		if( QuizMaster_Grades_Api::isGradesEnabled( $view->quiz )) {

			$grades = new QuizMaster_Grades_Api();
			$grades->setQuiz( $view->quiz );
			$grades->loadGrades();
			$grades->renderGradeMessageBox();

		}

	}

	public function addSettingsFields( $fields ) {

		$fields[] = array (
			'name' => '',
			'type' => 'tab',
			'placement' => 'left',
			'key' => 'field_5885c9ee832k',
			'label' => 'Grades',
		);

		$fields[] = array (
			'default_value' => 1,
			'message' => '',
			'key' => 'field_5933412a72h4',
			'label' => 'Grades Enabled by Default',
			'name' => 'qm_grades_default_enabled',
			'type' => 'true_false',
			'instructions' => 'Enables grades by default on all quizzes.',
		);

		$fields[] = array (
			'key' => 'field_59334140d992j',
			'label' => 'Default Passing Grade',
			'name' => 'qm_default_passing_grade',
			'type' => 'number',
			'instructions' => 'Default passing grade set for each quiz.',
			'default_value' => 75,
			'append' => '%',
			'min' => 1,
			'max' => 100,
		);

		$fields[] = array (
			'key' => 'field_5933429a45t91',
			'label' => ' Default Passing Grade Message',
			'name' => 'qmqu_grade_default_pass_message',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		);

		$fields[] = array (
			'key' => 'field_5933429a411kd',
			'label' => ' Default Failing Grade Message',
			'name' => 'qmqu_grade_default_fail_message',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		);

		return $fields;

	}

	public function addQuizFields( $fields ) {

		$fields[] = array (
			'name' => '',
			'type' => 'tab',
			'placement' => 'left',
			'key' => 'field_5885c9ee192u4',
			'label' => 'Grades',
		);

		$fields[] = array (
			'default_value' => 1,
			'message' => '',
			'key' => 'field_5933412ade4de',
			'label' => 'Enable Grades',
			'name' => 'qmqu_grades_enabled',
			'type' => 'true_false',
			'instructions' => 'Enables grades, uncheck to disable grades.',
		);

		$fields[] = array (
			'key' => 'field_59334140de4df',
			'label' => 'Passing Grade',
			'name' => 'qmqu_passing_grade',
			'type' => 'number',
			'instructions' => 'Minimum grade to pass quiz.',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5933412ade4de',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'default_value' => 75,
			'append' => '%',
			'min' => 1,
			'max' => 100,
		);

		$fields[] = array (
			'default_value' => 0,
			'message' => '',
			'key' => 'field_5933412adh32p',
			'label' => 'Enable Custom Passing Grade Message',
			'name' => 'qmqu_grade_has_custom_pass_message',
			'type' => 'true_false',
			'instructions' => 'Override the default message shown when user passes the quiz.',
		);

		$fields[] = array (
			'key' => 'field_5933429a439h1',
			'label' => 'Passing Grade Message',
			'name' => 'qmqu_grade_pass_message',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5933412adh32p',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
			'wrapper' => array (
				'width' => '100',
			),
		);

		$fields[] = array (
			'default_value' => 0,
			'message' => '',
			'key' => 'field_5933412adk44b',
			'label' => 'Enable Custom Failing Grade Message',
			'name' => 'qmqu_grade_has_custom_fail_message',
			'type' => 'true_false',
			'instructions' => 'Override the default message shown when user fails the quiz..',
		);

		$fields[] = array (
			'key' => 'field_5933429a488b6',
			'label' => 'Failing Grade Message',
			'name' => 'qmqu_grade_fail_message',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5933412adk44b',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
			'wrapper' => array (
				'width' => '100',
			),
		);

		$fields[] = array (
			'key' => 'field_5933424b4ffdc',
			'label' => 'Grades',
			'name' => 'qmqu_grades',
			'type' => 'repeater',
			'layout' => 'row',
			'button_label' => 'Add Grade',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5933412ade4de',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'sub_fields' => array (
				array (
					'key' => 'field_593342b34ffe0',
					'label' => 'Score Required',
					'name' => 'qmqu_grade_requirement',
					'type' => 'number',
					'required' => 1,
					'append' => '%',
					'min' => 1,
					'max' => 100,
					'wrapper' => array (
						'width' => '50',
					),
				),
				array (
					'key' => 'field_593342654ffdd',
					'label' => 'Grade Title',
					'name' => 'qmqu_grade_title',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'wrapper' => array (
						'width' => '50',
					),
				),
				array (
					'key' => 'field_5933427a4ffde',
					'label' => 'Grade Description',
					'name' => 'qmqu_grade_description',
					'type' => 'textarea',
					'conditional_logic' => 0,
					'rows' => 4,
					'new_lines' => 'wpautop',
					'wrapper' => array (
						'width' => '100',
					),
				),
				array (
					'key' => 'field_5933429a4ffdf',
					'label' => 'Achievement Message',
					'name' => 'qmqu_grade_achievement_message',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
					'wrapper' => array (
						'width' => '100',
					),
				),
			)
		);

		return $fields;

	}

	public function registerExtension( $registeredExtensions ) {

		$registeredExtensions['grades'] = array(
			'type' => 'ext',
			'name' => 'Grades',
		);

	}

	public function addScripts() {
		wp_register_script( 'quizmaster-grades-script', plugins_url('assets/js/quizmaster_grades.js', __FILE__), array('jquery'), QUIZMASTER_GRADES_VERSION, true );
		wp_enqueue_script( 'quizmaster-grades-script' );

		wp_register_style( 'quizmaster-grades-style', plugins_url('assets/css/quizmaster_grades.css', __FILE__), array(), QUIZMASTER_GRADES_VERSION );
		wp_enqueue_style( 'quizmaster-grades-style' );
	}

}

new QuizMaster_Grades_Extension();
