<?php

class QuizMaster_Controller_Score extends QuizMaster_Controller_Controller {

  protected $_score = '';

  public function getScore() {
    return $this->_score;
  }

  public function setScore( $score ) {
    $this->_score = $score;
  }

  public function save() {

    $quizId = $this->_post['quizId'];
    $results = $this->_post['results'];

    // load revision
    $quizRevisionPosts 	= wp_get_post_revisions( $quizId );
    $quizRevisionPost 	= reset( $quizRevisionPosts );
    $quizRevisionId 		= $quizRevisionPost->ID;

    $lockIp = $this->getIp();
    $userId = get_current_user_id();

    if ($lockIp === false) {
      return false;
    }

		// load quiz revision
    $quizMapper = new QuizMaster_Model_QuizMapper();
    $quizRevision = $quizMapper->fetch( $quizRevisionId );

		// is statistics off, we don't continue quiz score
    if ( !$quizRevision->isStatisticsOn() ) {
      return false;
    }

    $scores = $this->makeScoreList( $quizRevisionId, $results, $quizRevision->getQuizModus() );

    if ($scores === false) {
      return false;
    }

		// locks are applied to the quiz, not the revision
    if ( $quizRevision->getStatisticsIpLock() > 0 ) {

      $lockMapper = new QuizMaster_Model_LockMapper();
      $lockTime = $quiz->getStatisticsIpLock() * 60;

      $lockMapper->deleteOldLock($lockTime, $quiz->getId(), time(), QuizMaster_Model_Lock::TYPE_STATISTIC);

      if ($lockMapper->isLock($quizId, $lockIp, $userId, QuizMaster_Model_Lock::TYPE_STATISTIC)) {
        return false;
      }

      $lock = new QuizMaster_Model_Lock();
      $lock->setQuizId($quizId)
        ->setLockIp($lockIp)
        ->setUserId($userId)
        ->setLockType(QuizMaster_Model_Lock::TYPE_STATISTIC)
        ->setLockDate(time());

      $lockMapper->insert($lock);
    }

    // load score model
    $score = new QuizMaster_Model_Score();
    $score->setUserId( $userId );
    $score->setQuizId( $quizId );
		$score->setQuizRevisionId( $quizRevisionId );
    $score->setScores( $scores );

    $totals = $this->calcTotals( $scores );
    $score->setTotals( $totals );
    $score->save();

    $this->setScore( $score );
    return $this->getScore();
  }

  private function calcTotals( $scores ) {

    $totals = array(
      'qCount'          => 0,
      'qCorrect'        => 0,
      'qIncorrect'      => 0,
      'pointsPossible'  => 0,
      'pointsEarned'    => 0,
      'time'            => 0,
      'solved'          => 0,
      'hints'           => 0,
    );

    foreach( $scores as $score ) {
      $totals['qCorrect']         += $score->getCorrectCount();
      $totals['qIncorrect']       += $score->getIncorrectCount();
      $totals['pointsPossible']   += $score->getPossiblePoints();
      $totals['pointsEarned']     += $score->getPoints();
      $totals['time']             += $score->getQuestionTime();
      $totals['solved']           += $score->getSolvedCount();
      $totals['hints']            += $score->getHintCount();
    }

    $totals['qCount'] = $totals['qCorrect'] + $totals['qIncorrect'];
    return $totals;

  }

  private function makeScoreList($quizId, $results, $modus) {

    $questionMapper = new QuizMaster_Model_QuestionMapper();
    $questions = $questionMapper->fetchAll( $quizId );
    $ids = array();

    foreach ($questions as $q) {
      if (!isset( $results[ $q->getId() ]) ) {
        continue;
      }

      $ids[] = $q->getId();
      $v = $results[ $q->getId() ];

      if (!isset($v) || $v['points'] > $q->getPoints() || $v['points'] < 0) {
        return false;
      }
    }

    $avgTime = null;

    if ($modus == QuizMaster_Model_Quiz::QUIZ_MODUS_SINGLE) {
      $avgTime = ceil($results['comp']['quizTime'] / count($questions));
    }

    unset($results['comp']);

    $ak = array_keys($results);

    if (array_diff($ids, $ak) !== array_diff($ak, $ids)) {
      return false;
    }

    $values = array();

    foreach ($results as $id => $v) {

      // load revision
      $qRevisions = wp_get_post_revisions( $id );
      $qScoringRevision = reset( $qRevisions );
      $qRevisionId = $qScoringRevision->ID;

      $s = new QuizMaster_Model_ScoreQuestion();
      $s->setQuestionId( $qRevisionId );
      $s->setQuestionIdUnrevised( $id );
      $s->setHintCount(isset($v['tip']) ? 1 : 0);
      $s->setSolvedCount(isset($v['solved']) && $v['solved'] ? 1 : 0);
      $s->setCorrectCount($v['correct'] ? 1 : 0);
      $s->setIncorrectCount($v['correct'] ? 0 : 1);
      $s->setPoints($v['points']);
      $s->setQuestionTime( $v['time'] );
      $s->setAnswerData(isset($v['data']) ? $v['data'] : null);

      $values[] = $s;
    }

    return $values;
  }

  private function getIp() {
      if (get_current_user_id() > 0) {
          return '0';
      } else {
          return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
      }
  }

  public function getAverageResult($quizId) {
    $scoreMapper = new QuizMaster_Model_ScoreMapper();

    /*
    $result = $scoreMapper->fetchFrontAvg($quizId);

    if (isset($result['g_points']) && $result['g_points']) {
      return round(100 * $result['points'] / $result['g_points'], 2);
    }
    */

    return 0;
  }

  public static function loadById( $id ) {

    $scoreCtr = new QuizMaster_Controller_Score;

    $post = get_post( $id );
    $fields = get_fields( $id );

    $score = new QuizMaster_Model_Score( $id );
    $scoreCtr->setScore( $score );

    return $scoreCtr;

  }


}
