<?php

class QuizMaster_Controller_StudentReport {

  public function getCompletedQuizTable() {

    $currentUser = wp_get_current_user();
    $studentId = $currentUser->ID;

    $scoreMapper = new QuizMaster_Model_ScoreMapper;
    $scores = $scoreMapper->fetchByUser( $studentId );

    $viewScore = new QuizMaster_View_Score;
    return $viewScore->listTable( $scores );

  }

}
