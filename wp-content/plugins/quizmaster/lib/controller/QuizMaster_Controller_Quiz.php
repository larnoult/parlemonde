<?php

class QuizMaster_Controller_Quiz extends QuizMaster_Controller_Controller
{
    public function route() {

    }

    public function isLockQuiz() {

        $quizId = (int)$this->_post['quizId'];
        $userId = get_current_user_id();
        $data = array();

        $lockMapper = new QuizMaster_Model_LockMapper();
        $quizMapper = new QuizMaster_Model_QuizMapper();

        $quiz = $quizMapper->fetch($this->_post['quizId']);

        if ($quiz === null || $quiz->getId() <= 0) {
            return null;
        }

        if ($this->isPreLockQuiz($quiz)) {
            $lockIp = $lockMapper->isLock($this->_post['quizId'], $this->getIp(), $userId,
                QuizMaster_Model_Lock::TYPE_QUIZ);
            $lockCookie = false;
            $cookieTime = $quiz->getQuizRunOnceTime();

            if (isset($this->_cookie['qm-locked']) && $userId == 0 && $quiz->isQuizRunOnceCookie()) {
                $cookieJson = json_decode($this->_cookie['qm-locked'], true);

                if ($cookieJson !== false) {
                    if (isset($cookieJson[$this->_post['quizId']]) && $cookieJson[$this->_post['quizId']] == $cookieTime) {
                        $lockCookie = true;
                    }
                }
            }

            $data['lock'] = array(
                'is' => ($lockIp || $lockCookie),
                'pre' => true
            );
        }

        // lock quiz if user not logged in
        if ($quiz->isStartOnlyRegisteredUser()) {
            $data['startUserLock'] = (int)!is_user_logged_in();
        }

				// enable extensions to filter quiz lock result
				$data = apply_filters( 'quizmaster_check_quiz_lock', $data, $quiz );

        return $data;

    }

    public function isPreLockQuiz(QuizMaster_Model_Quiz $quiz)
    {
        $userId = get_current_user_id();

        if ($quiz->isQuizRunOnce()) {
            switch ($quiz->getQuizRunOnceType()) {
                case QuizMaster_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ALL:
                    return true;
                case QuizMaster_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ONLY_USER:
                    return $userId > 0;
                case QuizMaster_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM:
                    return $userId == 0;
            }
        }

        return false;
    }

    private function getIp()
    {
        if (get_current_user_id() > 0) {
            return '0';
        } else {
            return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        }
    }

    public function htmlEmailContent()
    {
        return 'text/html';
    }

    private function setCategoryOverview($catArray, $categories)
    {
        $cats = array();

        foreach ($categories as $cat) {
            /* @var $cat QuizMaster_Model_Category */

            if (!$cat->getCategoryId()) {
                $cat->setCategoryName(__('Not categorized', 'quizmaster'));
            }

            $cats[$cat->getCategoryId()] = $cat->getCategoryName();
        }

        $a = __('Categories', 'quizmaster') . ":\n";

        foreach ($catArray as $id => $value) {
            if (!isset($cats[$id])) {
                continue;
            }

            $a .= '* ' . str_pad($cats[$id], 35, '.') . ((float)$value) . "%\n";
        }

        return $a;
    }

    public static function ajaxSetQuizMultipleCategories($data)
    {
        if (!current_user_can('quizMaster_edit_quiz')) {
            return json_encode(array());
        }

        $quizMapper = new QuizMaster_Model_QuizMapper();

        $quizMapper->setMultipeCategories($data['quizIds'], $data['categoryId']);

        return json_encode(array());
    }

    public static function ajaxLoadQuizData($data) {
        $quizId = (int)$data['quizId'];

        $quizMapper = new QuizMaster_Model_QuizMapper();
        $score = new QuizMaster_Controller_Score();

        $quiz = $quizMapper->fetch($quizId);
        $data = array();

        if ($quiz === null || $quiz->getId() <= 0) {
          return json_encode(array());
        }

        $data['averageResult'] = $score->getAverageResult($quizId);

        return json_encode($data);
    }

    public static function ajaxQuizCheckLock() {
      // workaround ...
      $_POST = $_POST['data'];
      $quizController = new QuizMaster_Controller_Quiz();
      return json_encode($quizController->isLockQuiz());
    }

    public static function ajaxResetLock($data)
    {
        if (!current_user_can('quizMaster_edit_quiz')) {
            return json_encode(array());
        }

        $quizId = (int)$data['quizId'];

        $lm = new QuizMaster_Model_LockMapper();
        $qm = new QuizMaster_Model_QuizMapper();

        $q = $qm->fetch($quizId);

        if ($q->getId() > 0) {
            $q->setQuizRunOnceTime(time());

            $qm->save($q);

            $lm->deleteByQuizId($quizId, QuizMaster_Model_Lock::TYPE_QUIZ);
        }

        return json_encode(array());
    }

    public static function ajaxCompletedQuiz( $data ) {
        // workaround ...
        $_POST = $_POST['data'];
        $ctr = new QuizMaster_Controller_Quiz();

        $lockMapper = new QuizMaster_Model_LockMapper();
        $quizMapper = new QuizMaster_Model_QuizMapper();

        $is100P = $data['results']['comp']['result'] == 100;

        $quiz = $quizMapper->fetch($data['quizId']);

        if ($quiz === null || $quiz->getId() <= 0) {
          return json_encode(array());
        }

        $ctr->setResultCookie($quiz);

        if (!$ctr->isPreLockQuiz($quiz)) {

          $score = new QuizMaster_Controller_Score();
          $score->save($quiz);
          do_action( 'quizmaster_completed_quiz', $quiz, $score->getScore() );

          if ($is100P) {
            do_action('quizmaster_completed_quiz_100_percent', $quiz, $score->getScore() );
          }

          return json_encode(array());
        }

        $lockMapper->deleteOldLock(60 * 60 * 24 * 7, $data['quizId'], time(), QuizMaster_Model_Lock::TYPE_QUIZ,
            0);

        $lockIp = $lockMapper->isLock($data['quizId'], $ctr->getIp(), get_current_user_id(),
            QuizMaster_Model_Lock::TYPE_QUIZ);
        $lockCookie = false;
        $cookieTime = $quiz->getQuizRunOnceTime();
        $cookieJson = null;

        if (isset($ctr->_cookie['qm-locked']) && get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
          $cookieJson = json_decode($ctr->_cookie['qm-locked'], true);

          if ($cookieJson !== false) {
            if (isset($cookieJson[$data['quizId']]) && $cookieJson[$data['quizId']] == $cookieTime) {
              $lockCookie = true;
            }
          }
        }

        if (!$lockIp && !$lockCookie) {

          $score = new QuizMaster_Controller_Score();
          $score->save($quiz);

          do_action('quizmaster_completed_quiz', $quiz, $score->getScore() );

          if ($is100P) {
              do_action('quizmaster_completed_quiz_100_percent', $quiz, $score->getScore() );
          }

          if (get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
              $cookieData = array();

              if ($cookieJson !== null || $cookieJson !== false) {
                  $cookieData = $cookieJson;
              }

              $cookieData[$data['quizId']] = $quiz->getQuizRunOnceTime();
              $url = parse_url(get_bloginfo('url'));

              setcookie('qm-locked', json_encode($cookieData), time() + 60 * 60 * 24 * 60,
                  empty($url['path']) ? '/' : $url['path']);
          }

          $lock = new QuizMaster_Model_Lock();

          $lock->setUserId(get_current_user_id());
          $lock->setQuizId($data['quizId']);
          $lock->setLockDate(time());
          $lock->setLockIp($ctr->getIp());
          $lock->setLockType(QuizMaster_Model_Lock::TYPE_QUIZ);

          $lockMapper->insert($lock);
        }

        return json_encode(array());
    }

    private function setResultCookie(QuizMaster_Model_Quiz $quiz) {
        if (get_current_user_id() == 0) {
            $cookieData = array();
            if (isset($this->_cookie['quizMaster_result'])) {
                $d = json_decode($this->_cookie['quizMaster_result'], true);
                if ($d !== null && is_array($d)) {
                    $cookieData = $d;
                }
            }
            $cookieData[$quiz->getId()] = 1;
            $url = parse_url(get_bloginfo('url'));
            setcookie('quizMaster_result', json_encode($cookieData), time() + 60 * 60 * 24 * 300,
                empty($url['path']) ? '/' : $url['path']);
        }
    }


}
