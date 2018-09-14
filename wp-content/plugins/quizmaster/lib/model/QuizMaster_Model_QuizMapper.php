<?php

class QuizMaster_Model_QuizMapper extends QuizMaster_Model_Mapper {

  /**
   * @param $id
   * @return QuizMaster_Model_Quiz
   */
  public function fetch( $id ) {
    $quiz = new QuizMaster_Model_Quiz( $id );
    return $quiz;
  }

  /**
   * @return QuizMaster_Model_Quiz[]
   */
  public function fetchAll() {
    $quizzes = array();
    $args = array(
      'post_type' => 'quizmaster_quiz',
      'orderby' => 'ASC',
      'posts_per_page'=> -1
    );
    $query = new WP_Query($args);
    if( !$query->have_posts() ) {
      return false;
    }
    foreach( $query->posts as $post ) {
      $quizzes[] = $this->fetch( $post->ID );
    }
    return $quizzes;
  }

}
