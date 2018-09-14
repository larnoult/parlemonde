<?php

class QuizMaster_Controller_Front {

    /**
     * @var QuizMaster_Model_GlobalSettings
     */
    private $_settings = null;

    public function __construct() {

      add_action('wp_enqueue_scripts', array($this, 'loadDefaultScripts'));

      /* add shortcodes */
      add_shortcode('quizmaster', array($this, 'shortcode'));
      add_shortcode('quizmaster_student_report', array($this, 'studentReportShortcode'));

      // init controller email
      $emailCtr = new QuizMaster_Controller_Email();

    }

    public function getSetQuizId() {
      if( !empty(  $_GET['quiz'] )) {
        return $_GET['quiz'];
      }
      return false;
    }

    public function getSetRefId() {
      if( !empty(  $_GET['ref'] )) {
        return $_GET['ref'];
      }
      return false;
    }

    /* Student Report Shortcode */
    public function studentReportShortcode() {

      if( !is_user_logged_in() ) {
        return quizmaster_parse_template( 'reports/student-login.php');
      }
      $user = wp_get_current_user();

      // get params
      $quiz_id = $this->getSetQuizId();
      $ref_id  = $this->getSetRefId();

      // show completed quiz table
      if( !$quiz_id ) {
        $studentReportCtr = new QuizMaster_Controller_StudentReport;
        return $studentReportCtr->getCompletedQuizTable();
      }

      // show quiz review
      if( $quiz_id ) {
        $quizReview = new PQC_Quiz_Review_Controller;
        return $quizReview->getQuizReviewList( $quiz_id, $ref_id );
      }

    }

    public function loadDefaultScripts() {

			wp_enqueue_script('jquery');

      $data = array(
        'src' => plugins_url('css/quizmaster' . (QUIZMASTER_DEV ? '' : '.min') . '.css', QUIZMASTER_FILE),
        'deps' => array(),
        'ver' => QUIZMASTER_VERSION,
      );

      $data = apply_filters('quizmaster_style', $data);
      wp_enqueue_style('quizmaster_style', $data['src'], $data['deps'], $data['ver']);

      wp_enqueue_script('jquery-datatables',
        plugins_url('js/datatables/jquery.dataTables.min.js', QUIZMASTER_FILE),
        array(),
        QUIZMASTER_VERSION
      );

      wp_enqueue_style('jquery-datatables-style',
        plugins_url('js/datatables/jquery.dataTables.min.css', QUIZMASTER_FILE),
        array(),
        QUIZMASTER_VERSION
      );

      wp_enqueue_script('jquery-easy-pie-chart',
        plugins_url('js/jquery.easypiechart.min.js', QUIZMASTER_FILE),
        array(),
        QUIZMASTER_VERSION
      );

      $this->loadJsScripts(false, true);

    }

    private function loadJsScripts($footer = true, $quiz = true) {

      if ($quiz) {

        wp_enqueue_script(
            'quizmaster_javascript',
            plugins_url('js/quizmaster' . (QUIZMASTER_DEV ? '' : '.min') . '.js', QUIZMASTER_FILE),
            array('jquery-ui-sortable'),
            QUIZMASTER_VERSION,
            $footer
        );

        wp_localize_script('quizmaster_javascript', 'QuizMasterGlobal', array(
          'ajaxurl' => admin_url('admin-ajax.php'),
          'loadData' => __('Loading', 'quizmaster'),
          'questionNotSolved' => __('You must answer this question.', 'quizmaster'),
          'questionsNotSolved' => __('You must answer all questions before you can completed the quiz.', 'quizmaster'),
          'fieldsNotFilled' => __('All fields have to be filled.', 'quizmaster')
        ));

      }

    }

    public function shortcode( $attr ) {

			if( !array_key_exists( 'id', $attr )) {
				return;
			}

      $id = $attr['id'];
			if( $id == null ) {
				return;
			}

      $content = '';

      $this->loadJsScripts();

      if (is_numeric($id)) {
        $content = $this->handleShortCode( $id );
      }

      return $content;

    }

    public function handleShortCode( $id, $return = true ) {

        $content = '';

        $view = new QuizMaster_View_FrontQuiz();
        $view = apply_filters( 'quizmaster_view_load', $view, 'FrontQuiz' );
        $view = apply_filters( 'quizmaster_view_load_front_quiz', $view );

        $quizMapper = new QuizMaster_Model_QuizMapper();
        $questionMapper = new QuizMaster_Model_QuestionMapper();

        $quiz = $quizMapper->fetch( $id );
        $maxQuestion = false;

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
                $count = $questionMapper->count($id);

                $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll( $id, true, $value );
            $maxQuestion = true;

        } else {
          $question = $questionMapper->fetchAll( $id );
        }

        if (empty($quiz) || empty($question)) {
          if( $return ) {
            return $content;
          } else {
            print $content;
          }
        }

        $view->quiz = $quiz;

				// randomize question if question random set
				if( $quiz->isQuestionRandom() ) {
					shuffle( $question );
				}

        $view->question = $question;
        $view->category = $quiz->fetchQuestionCategoriesByQuiz();

        $view = apply_filters( 'quizmaster_view_before_render', $view );
        $content = $view->show();

        if( $return ) {
          return $content;
        } else {
          print $content;
        }

    }

    public static function ajaxQuizLoadData($data) {
        $id = $data['quizId'];

        $view = new QuizMaster_View_FrontQuiz();

        $quizMapper = new QuizMaster_Model_QuizMapper();
        $questionMapper = new QuizMaster_Model_QuestionMapper();

        $quiz = $quizMapper->fetch($id);

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
              $count = $questionMapper->count($id);
              $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll($id, true, $value);

        } else {
            $question = $questionMapper->fetchAll($id);
        }

        if (empty($quiz) || empty($question)) {
            return null;
        }

				// randomize question if question random set
				if( $quiz->isQuestionRandom() ) {
					shuffle( $question );
				}

        $view->quiz = $quiz;
        $view->question = $question;
        $view->category = $quiz->fetchQuestionCategoriesByQuiz();

        return json_encode($view->getQuizData());
    }
}
