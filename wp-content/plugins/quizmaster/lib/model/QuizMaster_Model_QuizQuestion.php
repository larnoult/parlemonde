<?php

class QuizMaster_Model_QuizQuestion extends QuizMaster_Model_Model {

	protected $_quizId = 0;
	protected $_questionId = 0;

	public static function associate( $quiz, $question ) {

		if ( $quiz instanceof QuizMaster_Model_Quiz ) {
		  $quizId = $quiz->getId();
		} else {
			$quizId = $quiz;
		}

		if ( $question instanceof QuizMaster_Model_Question ) {
		  $questionId = $question->getId();
		} else {
			$questionId = $question;
		}

		// adds question to end of quiz
		$questions = get_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $quizId );
		$questions[] = $questionId;
		update_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $questions, $quizId );

		// adds quiz to list of selected quizzes associated from question editor
		$quizzes = get_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $questionId );
		$quizzes[] = $quizId;
		update_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $quizzes, $questionId );

	}

	public function clearAssociatedQuizzesFromQuestion( $questionId, $quizId = false ) {

		if( $quizId == false ) {
			update_field( QUIZMASTER_QUESTION_QUIZ_SELECTOR_FIELD, array(), $questionId );
		} else {
			// selective approach: remove one quiz
			$quizzes = get_field( QUIZMASTER_QUESTION_QUIZ_SELECTOR_FIELD, $questionId );

			if( empty( $quizzes )) {
				return;
			}

			if( ( $key = array_search( $quizId, $quizzes )) !== false ) {
				unset($quizzes[$key]);
			}
			update_field( QUIZMASTER_QUESTION_QUIZ_SELECTOR_FIELD, $quizzes, $questionId );
		}

	}

	/*
	 * Remove one or more questions from the given quiz
	 */
	public function clearAssociatedQuestionsFromQuiz( $quizId, $questionId = false ) {

		if( $questionId == false ) {
			update_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, array(), $quizId );
		} else {

			// selective approach: remove one quiz
			$questions = get_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $quizId );
			if( ( $key = array_search( $questionId, $questions )) !== false ) {
				unset($questions[$key]);
			}
			update_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $questions, $quizId );

		}

	}

	/*
   * Associate question from quiz
   * Quiz selected from quiz tab on question editor
	 */
	public static function associateQuestionFromQuiz( $quiz, $question ) {

		if ( $quiz instanceof QuizMaster_Model_Quiz ) {
		  $quizId = $quiz->getId();
		} else {
			$quizId = $quiz;
		}

		if ( $question instanceof QuizMaster_Model_Question ) {
		  $questionId = $question->getId();
		} else {
			$questionId = $question;
		}

		// adds question to quiz
		$questions = get_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $quizId );
		if( !in_array( $questionId, $questions )) {
			$questions[] = $questionId;
			update_field( QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD, $questions, $quizId );
		}

	}

	public static function associateQuizFromQuestion( $quiz, $question ) {

		if ( $quiz instanceof QuizMaster_Model_Quiz ) {
		  $quizId = $quiz->getId();
		} else {
			$quizId = $quiz;
		}

		if ( $question instanceof QuizMaster_Model_Question ) {
		  $questionId = $question->getId();
		} else {
			$questionId = $question;
		}

		// adds quiz to list of selected quizzes associated from question editor
		$quizzes = get_field( QUIZMASTER_QUESTION_QUIZ_SELECTOR_FIELD, $questionId );

		if( empty( $quizzes )) {
			return;
		}

		if( !in_array( $quizId, $quizzes )) {
			$quizzes[] = $quizId;
			update_field( QUIZMASTER_QUESTION_QUIZ_SELECTOR_FIELD, $quizzes, $questionId );
		}

	}

}
