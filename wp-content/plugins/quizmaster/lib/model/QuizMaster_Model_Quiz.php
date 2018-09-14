<?php

class QuizMaster_Model_Quiz extends QuizMaster_Model_Model {

    const QUIZ_RUN_ONCE_TYPE_ALL = 1;
    const QUIZ_RUN_ONCE_TYPE_ONLY_USER = 2;
    const QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM = 3;
    const QUIZ_MODUS_NORMAL = 0;
    const QUIZ_MODUS_BACK_BUTTON = 1;
    const QUIZ_MODUS_CHECK = 2;
    const QUIZ_MODUS_SINGLE = 3;
    const QUIZ_FORM_POSITION_START = 0;
    const QUIZ_FORM_POSITION_END = 1;

    protected $_id = 0;
    protected $_name = '';
    protected $_text = '';
    protected $_resultText;
    protected $_titleHidden = false;
    protected $_btnRestartQuizHidden = false;
    protected $_btnViewQuestionHidden = false;
    protected $_questionRandom = false;
    protected $_answerRandom = false;
    protected $_timeLimit = 0;
    protected $_statisticsOn = false;
    protected $_statisticsIpLock = 1440;
    protected $_resultGradeEnabled = false;
    protected $_showPoints = false;
    protected $_quizRunOnce = false;
    protected $_quizRunOnceType = 0;
    protected $_quizRunOnceCookie = false;
    protected $_quizRunOnceTime = 0;
    protected $_numberedAnswer = false;
    protected $_hideAnswerMessageBox = false;
    protected $_disabledAnswerMark = false;
    protected $_showMaxQuestion = false;
    protected $_showMaxQuestionValue = 1;
    protected $_showMaxQuestionPercent = false;
    protected $_showAverageResult = false;
    protected $_quizModus = 0;
    protected $_showReviewQuestion = false;
    protected $_quizSummaryHide = false;
    protected $_skipQuestionDisabled = false;
    protected $_showCategoryScore = false;
    protected $_hideResultCorrectQuestion = false;
    protected $_hideResultQuizTime = false;
    protected $_hideResultPoints = false;
    protected $_autostart = false;
    protected $_forcingQuestionSolve = false;
    protected $_hideQuestionPositionOverview = false;
    protected $_hideQuestionNumbering = false;
    protected $_startOnlyRegisteredUser = false;
    protected $_questionsPerPage = 0;
    protected $_sortCategories = false;
    protected $_showCategory = false;
    protected $_categoryId = 0;
    protected $_categoryName = '';
		protected $_staticHeaderMessage;
		protected $_showSkipButton;
		protected $_showBackButton;

    public function setId($_id)
    {
        $this->_id = (int)$_id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setName($_name)
    {
        $this->_name = (string)$_name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setQuizDescription( $_description ) {
      $this->_text = (string)$_description;
    }

    public function getQuizDescription() {
      return $this->_text;
    }

    public function setText($_text) {
      $this->_text = (string)$_text;
    }

    public function getText() {
      return $this->_text;
    }

    public function setResultText($_resultText)
    {
        $this->_resultText = $_resultText;

        return $this;
    }

    public function getResultText() {
      return $this->_resultText;
    }

    public function setTitleHidden($_titleHidden)
    {
        $this->_titleHidden = (bool)$_titleHidden;

        return $this;
    }

    public function isTitleHidden()
    {
        return $this->_titleHidden;
    }

    public function setQuestionRandom($_questionRandom)
    {
        $this->_questionRandom = (bool)$_questionRandom;

        return $this;
    }

    public function isQuestionRandom()
    {
        return $this->_questionRandom;
    }

    public function setAnswerRandom($_answerRandom)
    {
        $this->_answerRandom = (bool)$_answerRandom;

        return $this;
    }

    public function isAnswerRandom()
    {
        return $this->_answerRandom;
    }

    public function setTimeLimit($_timeLimit)
    {
        $this->_timeLimit = (int)$_timeLimit;

        return $this;
    }

    public function getTimeLimit()
    {
        return $this->_timeLimit;
    }

    public function setStatisticsOn($_statisticsOn)
    {
        $this->_statisticsOn = (bool)$_statisticsOn;

        return $this;
    }

    public function isStatisticsOn()
    {
        return $this->_statisticsOn;
    }

    public function setStatisticsIpLock($_statisticsIpLock)
    {
        $this->_statisticsIpLock = (int)$_statisticsIpLock;

        return $this;
    }

    public function getStatisticsIpLock()
    {
        return $this->_statisticsIpLock;
    }

    public function setResultGradeEnabled( $_resultGradeEnabled ) {
      $this->_resultGradeEnabled = (bool)$_resultGradeEnabled;
    }

    public function isResultGradeEnabled() {
      return $this->_resultGradeEnabled;
    }

    public function setShowPoints($_showPoints)
    {
        $this->_showPoints = (bool)$_showPoints;

        return $this;
    }

    public function isShowPoints()
    {
        return $this->_showPoints;
    }

    public function fetchSumQuestionPoints()
    {
        $m = new QuizMaster_Model_QuizMapper();

        return $m->sumQuestionPoints($this->_id);
    }

    public function setBtnRestartQuizHidden($_btnRestartQuizHidden)
    {
        $this->_btnRestartQuizHidden = (bool)$_btnRestartQuizHidden;

        return $this;
    }

    public function isBtnRestartQuizHidden()
    {
        return $this->_btnRestartQuizHidden;
    }

    public function setBtnViewQuestionHidden($_btnViewQuestionHidden)
    {
        $this->_btnViewQuestionHidden = (bool)$_btnViewQuestionHidden;

        return $this;
    }

    public function isBtnViewQuestionHidden()
    {
        return $this->_btnViewQuestionHidden;
    }

    public function setQuizRunOnce($_quizRunOnce)
    {
        $this->_quizRunOnce = (bool)$_quizRunOnce;

        return $this;
    }

    public function isQuizRunOnce()
    {
        return $this->_quizRunOnce;
    }

    public function setQuizRunOnceCookie($_quizRunOnceCookie)
    {
        $this->_quizRunOnceCookie = (bool)$_quizRunOnceCookie;

        return $this;
    }

    public function isQuizRunOnceCookie()
    {
        return $this->_quizRunOnceCookie;
    }

    public function setQuizRunOnceType($_quizRunOnceType)
    {
        $this->_quizRunOnceType = (int)$_quizRunOnceType;

        return $this;
    }

    public function getQuizRunOnceType()
    {
        return $this->_quizRunOnceType;
    }

    public function setQuizRunOnceTime($_quizRunOnceTime)
    {
        $this->_quizRunOnceTime = (int)$_quizRunOnceTime;

        return $this;
    }

    public function getQuizRunOnceTime()
    {
        return $this->_quizRunOnceTime;
    }

    public function setNumberedAnswer($_numberedAnswer)
    {
        $this->_numberedAnswer = (bool)$_numberedAnswer;

        return $this;
    }

    public function isNumberedAnswer()
    {
        return $this->_numberedAnswer;
    }

    public function setHideAnswerMessageBox($_hideAnswerMessageBox)
    {
        $this->_hideAnswerMessageBox = (bool)$_hideAnswerMessageBox;

        return $this;
    }

    public function isHideAnswerMessageBox()
    {
        return $this->_hideAnswerMessageBox;
    }

    public function setDisabledAnswerMark($_disabledAnswerMark)
    {
        $this->_disabledAnswerMark = (bool)$_disabledAnswerMark;

        return $this;
    }

    public function isDisabledAnswerMark()
    {
        return $this->_disabledAnswerMark;
    }

    public function setShowMaxQuestion($_showMaxQuestion)
    {
        $this->_showMaxQuestion = (bool)$_showMaxQuestion;

        return $this;
    }

    public function isShowMaxQuestion()
    {
        return $this->_showMaxQuestion;
    }

    public function setShowMaxQuestionValue($_showMaxQuestionValue)
    {
        $this->_showMaxQuestionValue = (int)$_showMaxQuestionValue;

        return $this;
    }

    public function getShowMaxQuestionValue()
    {
        return $this->_showMaxQuestionValue;
    }

    public function setShowMaxQuestionPercent($_showMaxQuestionPercent)
    {
        $this->_showMaxQuestionPercent = (bool)$_showMaxQuestionPercent;

        return $this;
    }

    public function isShowMaxQuestionPercent()
    {
        return $this->_showMaxQuestionPercent;
    }

    public function setQuizModus($_quizModus) {
      $this->_quizModus = (int)$_quizModus;
    }

    public function getQuizModus()
    {
        return $this->_quizModus;
    }

    public function setShowReviewQuestion($_showReviewQuestion)
    {
        $this->_showReviewQuestion = (bool)$_showReviewQuestion;

        return $this;
    }

    public function isShowReviewQuestion()
    {
        return $this->_showReviewQuestion;
    }

    public function setQuizSummaryHide($_quizSummaryHide)
    {
        $this->_quizSummaryHide = (bool)$_quizSummaryHide;

        return $this;
    }

    public function isQuizSummaryHide()
    {
        return $this->_quizSummaryHide;
    }

    public function setShowCategoryScore($_showCategoryScore)
    {
        $this->_showCategoryScore = (bool)$_showCategoryScore;

        return $this;
    }

    public function isShowCategoryScore()
    {
        return $this->_showCategoryScore;
    }

    public function setHideResultCorrectQuestion($_hideResultCorrectQuestion)
    {
        $this->_hideResultCorrectQuestion = (bool)$_hideResultCorrectQuestion;

        return $this;
    }

    public function isHideResultCorrectQuestion()
    {
        return $this->_hideResultCorrectQuestion;
    }

    public function setHideResultQuizTime($_hideResultQuizTime)
    {
        $this->_hideResultQuizTime = (bool)$_hideResultQuizTime;

        return $this;
    }

    public function isHideResultQuizTime()
    {
        return $this->_hideResultQuizTime;
    }

    public function setHideResultPoints($_hideResultPoints)
    {
        $this->_hideResultPoints = (bool)$_hideResultPoints;

        return $this;
    }

    public function isHideResultPoints()
    {
        return $this->_hideResultPoints;
    }

    public function setAutostart($_autostart)
    {
        $this->_autostart = (bool)$_autostart;

        return $this;
    }

    public function isAutostart()
    {
        return $this->_autostart;
    }

    public function setForcingQuestionSolve($_forcingQuestionSolve)
    {
        $this->_forcingQuestionSolve = (bool)$_forcingQuestionSolve;

        return $this;
    }

    public function isForcingQuestionSolve()
    {
        return $this->_forcingQuestionSolve;
    }

    public function setHideQuestionPositionOverview($_hideQuestionPositionOverview)
    {
        $this->_hideQuestionPositionOverview = (bool)$_hideQuestionPositionOverview;

        return $this;
    }

    public function isHideQuestionPositionOverview()
    {
        return $this->_hideQuestionPositionOverview;
    }

    public function setHideQuestionNumbering($_hideQuestionNumbering)
    {
        $this->_hideQuestionNumbering = (bool)$_hideQuestionNumbering;

        return $this;
    }

    public function isHideQuestionNumbering()
    {
        return $this->_hideQuestionNumbering;
    }

    public function setStartOnlyRegisteredUser($_startOnlyRegisteredUser) {
      $this->_startOnlyRegisteredUser = (bool)$_startOnlyRegisteredUser;
    }

    public function isStartOnlyRegisteredUser() {
      return $this->_startOnlyRegisteredUser;
    }

    public function setQuestionsPerPage($_questionsPerPage) {
      $this->_questionsPerPage = (int)$_questionsPerPage;
    }

    public function getQuestionsPerPage() {
      return $this->_questionsPerPage;
    }

    public function setSortCategories($_sortCategories) {
      $this->_sortCategories = (bool)$_sortCategories;
    }

    public function isSortCategories() {
      return $this->_sortCategories;
    }

    public function setShowCategory($_showCategory) {
      $this->_showCategory = (bool)$_showCategory;
    }

    public function isShowCategory() {
      return $this->_showCategory;
    }

    public function setCategoryId($_categoryId) {
        $this->_categoryId = (int)$_categoryId;
        return $this;
    }

    public function getCategoryId() {
      return $this->_categoryId;
    }

    public function setCategoryName($_categoryName) {
      $this->_categoryName = (string)$_categoryName;
    }

    public function getCategoryName() {
      return $this->_categoryName;
    }

    public function getLink() {
      return '<a class="quizmaster-quiz-link" href="' . get_permalink( $this->getId() ) . '">' . $this->getName() . '</a>';
    }

    public function getFieldPrefix() {
      return 'qmqu_';
    }

    public function processFieldsDuringModelSet( $fields ) {

      $fields['name'] = get_post( $this->getId() )->post_title;

      return $fields;
    }

    /**
     * Save the current quiz object. Must be fully-formed, with certain required properties set
     * @return QuizMaster_Model_Quiz saved quiz object
     */
    public function save() {

      // if new quiz, save quiz post
      if( !$this->getId() ) {
         $createPostResult = $this->createPost();
         if( is_wp_error( $createPostResult || $createPostResult == 0 )) {
           return; // failed post create
         }
         $this->setId( $createPostResult );
      }

      // save meta
      $this->saveMeta();

    }

    /*
     * Update quiz meta
     */
    public function saveMeta() {
      $fieldGroup = $this->getFieldGroup();
      foreach( $fieldGroup['fields'] as $field ) {
        $this->saveField( $field );
      }
    }

    public function saveField( $field ) {

      // skip tabs
      if( $field['type'] == 'tab' ) {
        return;
      }

      // get method name
      $methodName = $this->fieldMethodNameGet( $field['name'] );

      if( $methodName ) {
        update_field( $field['name'], $this->$methodName(), $this->getId() );
      }

    }

    /*
     * Create quiz post
     */
    public function createPost() {
      $quizId = wp_insert_post(
        array(
          'post_type'     => 'quizmaster_quiz',
          'post_title'    => $this->getName(),
          'post_status'   => 'publish',
          'post_author'   => 1,
        )
      );
      return $quizId;
    }

    public function fetchQuestionCategoriesByQuiz() {

      $categories = array();

      // get all questions
      $questionMapper = new QuizMaster_Model_QuestionMapper();
      $questions = $questionMapper->fetchAll( $this->getId() );

      // return early if no questions
      if( empty( $questions )) {
        return $categories;
      }

      // get all the terms for each question
      foreach( $questions as $q ) {
        $categories[] = $q->getCategoryId();
      }

      // remove duplicates and reindex array
      $categories = array_unique( $categories );
      $categories = array_values( $categories );

      return $categories;
    }

    public function fetchQuestionCategoryPoints() {

      $categoryPoints = array();

      // get all questions
      $questionMapper = new QuizMaster_Model_QuestionMapper();
      $questions = $questionMapper->fetchAll( $this->getId() );

      // return early if no questions
      if( empty( $questions )) {
        return $categoryPoints;
      }

      // get all the terms for each question
      foreach( $questions as $q ) {
        $catId = $q->getCategoryId();
        if( array_key_exists( $catId, $categoryPoints )) {
          $categoryPoints[ $catId ] += $q->getPoints();
        } else {
          $categoryPoints[ $catId ] = $q->getPoints();
        }

      }

      return $categoryPoints;
    }

		public function setStaticHeaderMessage( $_staticHeaderMessage ) {
			$this->_staticHeaderMessage = $_staticHeaderMessage;
		}

		public function getStaticHeaderMessage() {
			return $this->_staticHeaderMessage;
		}

		public function setShowSkipButton( $_showSkipButton ) {
			$this->_showSkipButton = $_showSkipButton;
		}

		public function isShowSkipButton() {
			return $this->_showSkipButton;
		}

		public function setShowBackButton( $_showBackButton ) {
			$this->_showBackButton = $_showBackButton;
		}

		public function isShowBackButton() {
			return $this->_showBackButton;
		}

    public function fieldGroupKey() {
      return 'quiz';
    }

}
