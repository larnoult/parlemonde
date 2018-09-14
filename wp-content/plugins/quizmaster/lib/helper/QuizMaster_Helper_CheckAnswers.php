<?php

class QuizMaster_Helper_CheckAnswers {

	private $correct = false;
	private $points = 0;

	public static function ajaxCheckAnswer() {

		$checkAnswers = new QuizMaster_Helper_CheckAnswers;
		$_POST = $_POST['data'];
		$quizId = $_POST['quizId'];
		$questionId = $_POST['questionId'];
		if( array_key_exists( 'userAnswerData', $_POST )) {
			$userAnswerData = $_POST['userAnswerData'];
		} else {
			$userAnswerData = array();
		}
		$questionMapper = new QuizMaster_Model_QuestionMapper();
		$question = $questionMapper->fetch( $questionId );
		$answerType = $question->getAnswerType();

		// run check function based on answer type
		switch( $answerType ) {
			case 'single':
				$checkAnswers->checkSingle( $question, $userAnswerData );
				break;
			case 'multiple':
				$checkAnswers->checkMultiple( $question, $userAnswerData );
				break;
			case 'free_answer':
				$checkAnswers->checkFree( $question, $userAnswerData );
				break;
			case 'fill_blank':
				$checkAnswers->checkFillBlank( $question, $userAnswerData );
				break;
			case 'sort_answer':
				$checkAnswers->checkSorting( $question, $userAnswerData );
				break;
		}

		// return json check result data
		print json_encode( array(
				'correct' => $checkAnswers->correct,
				'points' 	=> $checkAnswers->points
			)
		);

		die();

	}

	public function checkSingle( $question, $userAnswerData ) {

		if( empty( $userAnswerData )) {
			return;
		}

		foreach( $question->getAnswerData() as $answerIndex => $answerObj ) {

			if( $answerObj->isCorrect() ) {

				// check if the user answer index matches this answer index
				// even if answers are randomized in display the indexes will continue to match the order set in the question
				if( $userAnswerData['answerIndexes'][0] == $answerIndex ) {
					$this->correct = 1;
					$this->points  = $question->getPoints();
				}

			}

		}

	}

	public function checkMultiple( $question, $userAnswerData ) {

		if( empty( $userAnswerData )) {
			return;
		}

		foreach( $question->getAnswerData() as $answerIndex => $answerObj ) {

			if( $answerObj->isCorrect() ) {

				// check if the user answer index matches this answer index
				// even if answers are randomized in display the indexes will continue to match the order set in the question
				if( ! in_array( $answerIndex, $userAnswerData['answerIndexes'] )) {

					$this->correct = 0;
					$this->points  = 0;
					return;

				}

			}

		}

		// answer was correct
		$this->correct = 1;
		$this->points = $question->getPoints();

	}

	public function checkFree( $question, $userAnswerData ) {

		$answerData = $question->getAnswerData();
		$answer = $answerData[0]->getAnswer();

		if( $answer === $userAnswerData ) {

			$this->correct = 1;
			$this->points = $question->getPoints();

		}

	}

	public function checkSorting( $question, $userAnswerData ) {

		$answerData = $question->getAnswerData();

		foreach( $answerData as $answerIndex => $answer ) {
			if( $answer->getAnswerId() != $userAnswerData[ $answerIndex ] ) {

				$this->correct = 0;
				$this->points  = 0;
				return;

			}
		}

		$this->correct = 1;
		$this->points = $question->getPoints();

	}

	public function checkFillBlank( $question, $userAnswerData ) {

		$answerData = $question->getAnswerData();

		preg_match_all("/{([^}]+)}/", $answerData[0]->getAnswer(), $answers );
		$answers = $answers[1]; // matches without delimiters
		if( empty( $answers )) {

			$this->correct = 0;
			$this->points  = 0;
			return;

		}

		foreach( $answers as $answerIndex => $answer ) {
			if( $answer != $userAnswerData[$answerIndex] ) {
				$this->correct = 0;
				$this->points  = 0;
				return;
			}
		}

		$this->correct = 1;
		$this->points = $question->getPoints();

	}

}
