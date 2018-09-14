<?php

class QuizMaster_Grades_Api {

	protected $gradesEnabled;
	protected $passingGrade;
	protected $quiz;

	public function setQuiz( $quiz ) {

		$this->quiz = $quiz;

	}

	public function isGradesDefaultEnabled() {

		return get_field( 'qm_grades_default_enabled', 'option' );

	}

	public static function isGradesEnabled( $quiz ) {

		$grades = new QuizMaster_Grades_Api;

		if( $quiz->gradesEnabled ) {
			return true;
		}

		if( $grades->isGradesDefaultEnabled() ) {
			return true;
		}

		return false;

	}

	public function loadGrades() {

		$this->loadGradesEnabled();
		$this->loadPassingGrade();
		$this->loadGradesByQuiz();

	}

	public function loadGradesByQuiz() {

		$this->grades = array();

		$quizGrades = $this->quiz->grades;
		if( empty($quizGrades)) {

			$this->grades = false;
			return $this->grades;

		}

		foreach( $quizGrades as $quizGrade ) {

			$args = array(
				'requirement' 				=> $quizGrade['qmqu_grade_requirement'],
				'title' 							=> $quizGrade['qmqu_grade_title'],
				'description' 				=> $quizGrade['qmqu_grade_description'],
				'achievementMessage' 	=> $quizGrade['qmqu_grade_achievement_message'],
			);

			$this->grades[] = new QuizMaster_Grade( $args );

		}

		return $this->grades;

	}

	public function loadGradesEnabled() {

		$this->gradesEnabled = false;

		if( $this->quiz->gradesEnabled ) {
			$this->gradesEnabled = true;
		}

		if( $this->isGradesDefaultEnabled() ) {
			$this->gradesEnabled = true;
		}

		return $this->gradesEnabled;

	}

	public function loadPassingGrade() {

		$this->passingGrade = false;

		$passingGrade = $this->quiz->passingGrade;

		if( $passingGrade ) {
			$this->passingGrade = $passingGrade;
		}

		$defaultPassingGrade = get_field( 'qm_default_passing_grade', 'option' );

		if( $defaultPassingGrade ) {
			$this->passingGrade = $defaultPassingGrade;
		}

		return $this->passingGrade;

	}

	public function renderGradeMessageBox() {

		quizmaster_get_template( 'quiz-grade-message.php',
			array(
				'passingGrade' => $this->passingGrade,
				'gradesEnabled' => $this->gradesEnabled,
				'quiz' => $this->quiz,
				'grades' => $this->grades,
			),
			QUIZMASTER_GRADES_TEMPLATES_PATH,
			QUIZMASTER_GRADES_TEMPLATES_PATH
		);

	}

}
