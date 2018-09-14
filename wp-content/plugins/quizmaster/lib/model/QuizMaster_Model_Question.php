<?php

class QuizMaster_Model_Question extends QuizMaster_Model_Model {

    protected $_id = 0;
    protected $_quizId = 0;
    protected $_sort = 0;
    protected $_title = '';
    protected $_question = '';
    protected $_correctMsg = '';
    protected $_incorrectMsg = '';
    protected $_answerType = 'single';
    protected $_correctSameText = false;
    protected $_tipEnabled = false;
    protected $_tipMsg = '';
    protected $_points = 1;
    protected $_showPointsInBox = false;
    protected $_answerPointsActivated = false;
    protected $_answerData = null;
    protected $_categoryId = 0;
    protected $_categoryName = '';
    protected $_answerPointsDiffModusActivated = false;
    protected $_disableCorrect = false;
    protected $_matrixSortAnswerCriteriaWidth = 20;

    public function setId($_id) {
      $this->_id = (int)$_id;
    }

    public function getId() {
      return $this->_id;
    }

    public function setQuizId($_quizId) {
      $this->_quizId = (int)$_quizId;
    }

    public function getQuizId() {
      return $this->_quizId;
    }

    public function setSort($_sort) {
      $this->_sort = (int) $_sort;
    }

    public function getSort() {
      return $this->_sort;
    }

    public function setTitle( $_title ) {
      $this->_title = (string) $_title;
    }

    public function getTitle() {
      return $this->_title;
    }

    public function setQuestion( $_question ) {
      $this->_question = (string) $_question;
    }

    public function getQuestion() {
      return $this->_question;
    }

    public function setCorrectMsg($_correctMsg) {
      $this->_correctMsg = (string) $_correctMsg;
    }

    public function getCorrectMsg() {
      return $this->_correctMsg;
    }

    public function setIncorrectMsg($_incorrectMsg)
    {
        $this->_incorrectMsg = (string)$_incorrectMsg;

        return $this;
    }

    public function getIncorrectMsg()
    {
        return $this->_incorrectMsg;
    }

    public function setAnswerType($_answerType)
    {
        $this->_answerType = (string)$_answerType;

        return $this;
    }

    public function getAnswerType()
    {
        return $this->_answerType;
    }

    public function setCorrectSameText($_correctSameText)
    {
        $this->_correctSameText = (bool)$_correctSameText;

        return $this;
    }

    public function isCorrectSameText()
    {
        return $this->_correctSameText;
    }

    public function setTipEnabled($_tipEnabled)
    {
        $this->_tipEnabled = (bool)$_tipEnabled;

        return $this;
    }

    public function isTipEnabled()
    {
        return $this->_tipEnabled;
    }

    public function setTipMsg($_tipMsg)
    {
        $this->_tipMsg = (string)$_tipMsg;

        return $this;
    }

    public function getTipMsg()
    {
        return $this->_tipMsg;
    }

    public function setPoints($_points) {
      $this->_points = (int)$_points;
    }

    public function getPoints() {
      return $this->_points;
    }

    public function setShowPointsInBox($_showPointsInBox) {
      $this->_showPointsInBox = (bool)$_showPointsInBox;
      return $this;
    }

    public function isShowPointsInBox() {
      return $this->_showPointsInBox;
    }

    public function setAnswerPointsActivated($_answerPointsActivated) {
      $this->_answerPointsActivated = (bool)$_answerPointsActivated;
    }

    public function isAnswerPointsActivated() {
      return $this->_answerPointsActivated;
    }

    public function setAnswerData($_answerData) {
      $this->_answerData = $_answerData;
    }

    /**
     * @param bool|false $serialize
     * @return QuizMaster_Model_AnswerTypes[]|null|string
     */
    public function getAnswerData() {
      return $this->_answerData;
    }

    public function setCategoryId($_categoryId) {
      $this->_categoryId = (int)$_categoryId;
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

    public function setAnswerPointsDiffModusActivated($_answerPointsDiffModusActivated)
    {
        $this->_answerPointsDiffModusActivated = (bool)$_answerPointsDiffModusActivated;

        return $this;
    }

    public function isAnswerPointsDiffModusActivated()
    {
        return $this->_answerPointsDiffModusActivated;
    }

    public function setDisableCorrect($_disableCorrect)
    {
        $this->_disableCorrect = (bool)$_disableCorrect;

        return $this;
    }

    public function isDisableCorrect()
    {
        return $this->_disableCorrect;
    }

    public function setMatrixSortAnswerCriteriaWidth($_matrixSortAnswerCriteriaWidth)
    {
        $this->_matrixSortAnswerCriteriaWidth = (int)$_matrixSortAnswerCriteriaWidth;

        return $this;
    }

    public function getMatrixSortAnswerCriteriaWidth()
    {
        return $this->_matrixSortAnswerCriteriaWidth;
    }

    public function loadAnswerDataAssessment( $fields ) {
      $fieldAnswerData['answer'] = $fields['qmqe_assessment_answers'];
      $answerData[] = new QuizMaster_Model_AnswerTypes( $fieldAnswerData );
      return $answerData;
    }

    public function loadAnswerDataCloze( $fields ) {
      $fieldAnswerData['answer'] = $fields['qmqe_fill_blanks'];
      $answerData[] = new QuizMaster_Model_AnswerTypes( $fieldAnswerData );
      return $answerData;
    }

    public function loadAnswerDataSortingChoice( $data ) {

      // load answer model
      $aModelName = $this->answerModelName();
      $aModel = new $aModelName();
      return $aModel->load( $data );

    }

    public function loadAnswerDataMultipleChoice( $data ) {

      // load answer model
      $aModelName = $this->answerModelName();
      $aModel = new $aModelName();
      return $aModel->load( $data );

    }

    public function loadAnswerDataSingleChoice( $data ) {

      // load answer model
      $aModelName = $this->answerModelName();
      $aModel = new $aModelName();
      return $aModel->load( $data );

    }

    public function loadAnswerDataFreeChoice( $data ) {

      // load answer model
      $aModelName = $this->answerModelName();
      $aModel = new $aModelName();
      return $aModel->load( $data );

    }

    private function loadAnswerData() {

      $fields = get_fields( $this->getId() );
      $answer_type = $fields[ QUIZMASTER_ANSWER_TYPE_FIELD ];

      // set answer data
      switch( $answer_type ) {
        case 'single':
          $answerData = $this->loadAnswerDataSingleChoice( $fields );
          break;
        case 'multiple':
          $answerData = $this->loadAnswerDataMultipleChoice( $fields );
          break;
        case 'free_answer':
          $answerData = $this->loadAnswerDataFreeChoice( $fields );
          break;
        case 'sort_answer':
          $answerData = $this->loadAnswerDataSortingChoice( $fields );
          break;
        case 'fill_blank':
          $answerData = $this->loadAnswerDataCloze( $fields );
          break;
      }

      $this->setAnswerData( $answerData );

    }

    public function getFieldPrefix() {
      return 'qmqe_';
    }

    public function processFieldsDuringModelSet( $fields ) {

      // load the answer data based on answer type
      $this->loadAnswerData();

      $scoreCategoryID = $fields[ 'score_category' ];
      $scoreCategory = get_term( $scoreCategoryID );

      if( !is_wp_error( $scoreCategory )) {

        $fields['category_id'] = $scoreCategoryID;
        $fields['category_name'] = $scoreCategory->name;

      }

      return $fields;

    }

    public function save() {

      // if new q, save q post
      if( !$this->getId() ) {
         $createPostResult = $this->createPost();
         if( is_wp_error( $createPostResult || $createPostResult == 0 )) {
           return; // failed post create
         }
         $this->setId( $createPostResult );
      }

      // save meta
      $this->saveMeta();

			// save answer data
			$this->saveAnswers();

    }

    /*
     * Create quiz post
     */
    public function createPost() {
      $questionId = wp_insert_post(
        array(
          'post_type'     => 'quizmaster_question',
          'post_title'    => $this->getTitle(),
          'post_status'   => 'publish',
          'post_author'   => 1,
        )
      );
      return $questionId;
    }

    /*
     * Update question meta
     */
    public function saveMeta() {

      $fieldGroup = $this->getFieldGroup();
      foreach( $fieldGroup['fields'] as $field ) {
        $this->saveField( $field );
      }

    }

		public function getAnswerModel() {

			switch( $this->getAnswerType() ) {
	      case 'single':
	        return new QuizMaster_Answer_SingleChoice;
	        break;
	      case 'multiple':
	        return new QuizMaster_Answer_MultipleChoice;
	        break;
	      case 'free_answer':
	        return  new QuizMaster_Answer_Free;
	      case 'sort_answer':
	        return new QuizMaster_Answer_Sorting;
	      case 'fill_blank':
	      	return  new QuizMaster_Answer_Fill_Blank;
	    }

			return false;

		}

		/*
     * Update question meta
     */
    public function saveAnswers() {

			$answerModel = $this->getAnswerModel();
			$answerModel->save( $this->getAnswerData() );

		}

		public static function isAssociatedQuiz( $quizId ) {

			$quiz = new QuizMaster_Model_Quiz( $quizId );
			$questions = $questionMapper->fetchAll( $quizId );
			if( in_array( $this->getId(), $questions )) {
				return true;
			}
			return false;

		}

    public function fieldGroupKey() {
      return 'question';
    }

}
