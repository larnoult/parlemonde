<?php

/**
 * @property QuizMaster_Model_Quiz quiz
 * @property QuizMaster_Model_Question[] question
 * @property QuizMaster_Model_Category[] category
 */
class QuizMaster_View_FrontQuiz extends QuizMaster_View_View {

    public $_clozeTemp = array();
    public $_assessmetTemp = array();
    public $_buttonNames = array();

		public function renderExtensionQuizBoxes() {
			do_action( 'quizmaster_render_quiz_box', $this );
		}

    public function loadButtonNames() {

      if (!empty($this->_buttonNames)) {
        return;
      }

      $names = array(
        'start_quiz'      => __('Start quiz', 'quizmaster'),
        'restart_quiz'    => __('Restart quiz', 'quizmaster'),
        'quiz_summary'    => __('Quiz Summary', 'quizmaster'),
        'finish_quiz'     => __('Finish quiz', 'quizmaster'),
        'quiz_is_loading' => __('Quiz is loading...', 'quizmaster'),
        'lock_box_msg'    => __('You have already completed the quiz before and only 1 attempt is allowed.', 'quizmaster'),
        'only_registered_user_msg'  => __('You must sign in or sign up to start the quiz.', 'quizmaster'),
      );

      $this->_buttonNames = apply_filters('quizmaster_button_names', $names, $this);

    }

    /**
     * @param $data QuizMaster_Model_AnswerTypes
     *
     * @return array
     */
    public function getFreeCorrect( $data ) {

        $t = str_replace("\r\n", "\n", strtolower($data->getAnswer()));
        $t = str_replace("\r", "\n", $t);
        $t = explode("\n", $t);

        return array_values(array_filter(array_map('trim', $t), array($this, 'removeEmptyElements')));

    }

    public function removeEmptyElements( $v ) {
      return !empty($v) || $v === '0';
    }

    public function show() {

      $this->loadButtonNames();
      $this->question_count = count($this->question);
      $this->result = $this->quiz->getResultText();

      // handle graduations
      if ( $this->quiz->isResultGradeEnabled() ) {
        $this->result = array(
          'text' => array($this->result),
          'prozent' => array(0)
        );

        $this->resultsProzent = json_encode( $this->result['prozent'] );
      } else {
        $this->resultsProzent = array(0);
      }

      return quizmaster_parse_template( 'quiz/loader.php', array('view' => $this));

    }

    public function createOption() {
      $options = array(
				'isAnswerRandom' => (int)$this->quiz->isAnswerRandom(),
				'isQuestionRandom'  => (int)$this->quiz->isQuestionRandom(),
				'isDisabledAnswerMark' => (int)$this->quiz->isDisabledAnswerMark(),
				'isQuizRunOnce' => (int)$this->quiz->isQuizRunOnce(),
				'isStartOnlyRegisteredUser' => $this->quiz->isStartOnlyRegisteredUser(),
				'isCorsActivated' => (int)get_option('quizMaster_corsActivated'),
				'isShowReviewQuestion' => (int)$this->quiz->isShowReviewQuestion(),
				'isQuizSummaryHide' => (int)$this->quiz->isQuizSummaryHide(),
				'isShowSkipButton' => (int)$this->quiz->isShowSkipButton(),
				'isAutostart' => (int)$this->quiz->isAutostart(),
				'isForcingQuestionSolve' => (int)$this->quiz->isForcingQuestionSolve(),
				'isHideQuestionPositionOverview' => (int)$this->quiz->isHideQuestionPositionOverview(),
				'isFormActivated' => (int)$this->quiz->isFormActivated(),
				'isShowMaxQuestion' => (int)$this->quiz->isShowMaxQuestion(),
				'isSortCategories' => (int)$this->quiz->isSortCategories(),
				'isShowBackButton' => (int)$this->quiz->isShowBackButton(),
			);

			print json_encode( $options );
    }

    public function getQuizData() {

      ob_start();
      $this->loadButtonNames();
      $quizData = $this->showQuizBox(count($this->question));
      $quizData['content'] = ob_get_contents();
      ob_end_clean();

      return $quizData;

    }

    public function showQuizAnker()
    {
        ?>
        <div class="quizMaster_quizAnker" style="display: none;"></div>
        <?php
    }

    public function fetchAssessment($answerText, $quizId, $questionId)
    {
        preg_match_all('#\{(.*?)\}#im', $answerText, $matches);

        $this->_assessmetTemp = array();
        $data = array();

        for ($i = 0, $ci = count($matches[1]); $i < $ci; $i++) {
            $match = $matches[1][$i];

            preg_match_all('#\[([^\|\]]+)(?:\|(\d+))?\]#im', $match, $ms);

            $a = '';

            for ($j = 0, $cj = count($ms[1]); $j < $cj; $j++) {
                $v = $ms[1][$j];

                $a .= '<label>
					<input type="radio" value="' . ($j + 1) . '" name="question_' . $quizId . '_' . $questionId . '_' . $i . '" class="quizMaster_questionInput" data-index="' . $i . '">
					' . $v . '
				</label>';

            }

            $this->_assessmetTemp[] = $a;
        }

        $data['replace'] = preg_replace('#\{(.*?)\}#im', '@@quizMasterAssessment@@', $answerText);

        return $data;
    }

    public function assessmentCallback($t)
    {
        $a = array_shift($this->_assessmetTemp);

        return $a === null ? '' : $a;
    }

    public function showLockBox() {
			quizmaster_get_template('quiz/locked.php',
        array(
          'view' => $this,
        )
      );
    }

    public function showStartOnlyRegisteredUserBox() {
			quizmaster_get_template('quiz/registered.php',
        array(
          'view' => $this,
        )
      );
    }

    public function showCheckPageBox( $questionCount ) {
			quizmaster_get_template('quiz/check.php',
				array(
					'view' => $this,
					'questionCount' => $questionCount,
				)
			);
    }

    public function showInfoPageBox() {
			quizmaster_get_template('quiz/info.php',
        array(
          'view' => $this,
        )
      );
    }

    public function showStartQuizBox() {
			quizmaster_get_template('quiz/start.php',
        array(
          'view' => $this,
        )
      );
    }

    public function showTimeLimitBox() {
			quizmaster_get_template('quiz/time.php',
        array(
          'view' => $this,
        )
      );
    }

    public function showReviewBox($questionCount) {
			quizmaster_get_template('quiz/review.php',
        array(
          'view' => $this,
					'questionCount' => $questionCount,
        )
      );
    }

    public function showResultBox($result, $questionCount) {

			quizmaster_get_template('quiz/results-box.php',
        array(
          'view' => $this,
					'questionCount' => $questionCount,
					'result' => $result,
        )
      );

    }

    public function showQuizBox( $questionCount ) {

			$globalPoints = $this->setGlobalPoints( $this->question );
      $json = $this->setQuizJson( $this->question );
      return array('globalPoints' => $globalPoints, 'json' => $json, 'catPoints' => array());

    }

    public function setGlobalPoints( $questions ) {
      $globalPoints = 0;
      foreach ($questions as $question) {
        $answerArray = $question->getAnswerData();
        $globalPoints += $question->getPoints();
      }
      return $globalPoints;
    }

    public function setQuizJson( $questions ) {
      $json = array();
      foreach ($questions as $question) {
        $answerArray = $question->getAnswerData();

        $json[$question->getId()]['type'] = $question->getAnswerType();
        $json[$question->getId()]['id'] = (int)$question->getId();
        $json[$question->getId()]['catId'] = (int)$question->getCategoryId();
        if ($question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated() && $question->isDisableCorrect()) {
          $json[$question->getId()]['disCorrect'] = (int)$question->isDisableCorrect();
        }
        if (!$question->isAnswerPointsActivated()) {
          $json[$question->getId()]['points'] = $question->getPoints();
        }
        if ($question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated()) {
          $json[$question->getId()]['diffMode'] = 1;
        }

				if ($question->isShowPointsInBox() && $question->isAnswerPointsActivated()) {
					$json[$question->getId()]['showPoint'] = 1;
				}

				// pass messages
				$json[$question->getId()]['correctMessage'] = $question->getCorrectMsg();
				$json[$question->getId()]['incorrectMessage'] = $question->getIncorrectMsg();

        $answer_index = 0;
        foreach ($answerArray as $v) {
          if ($question->isAnswerPointsActivated()) {
            $json[$question->getId()]['points'][] = $v->getPoints();
          }

          // single or multiple
          if ($question->getAnswerType() === 'single' || $question->getAnswerType() === 'multiple') {
            $json[$question->getId()]['correct'][] = (int)$v->isCorrect();
            if ($question->getAnswerType() === 'sort_answer') {
              $json[$question->getId()]['correct'][] = (int)$answer_index;
            }
          }

          // free answer
          if ($question->getAnswerType() === 'free_answer') {
            $json[$question->getId()]['correct'] = $this->getFreeCorrect($v);
          }

          // matrix
          if ($question->getAnswerType() === 'matrix_sort_answer') {
            $json[$question->getId()]['correct'][] = (int)$answer_index;
          }

          // cloze
          if ($question->getAnswerType() === 'fill_blank') {
            $clozeData = $question->fetchCloze($v->getAnswer());
            $json[$question->getId()]['correct'] = $clozeData['correct'];
            if ($question->isAnswerPointsActivated()) {
              $json[$question->getId()]['points'] = $clozeData['points'];
            }
          }

          $answer_index++;
        }
      }
      return $json;
    }

    public function showLoadQuizBox() {
      quizmaster_get_template('quiz/load-box.php', array( 'view' => $this ));
    }

		public function showStaticHeaderMessage() {

			if( $this->quiz->getStaticHeaderMessage() !== '' ) {
				return true;
			}

			return false;

		}

}
