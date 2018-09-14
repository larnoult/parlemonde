<?php

class QuizMaster_Model_ScoreMapper {

  public function fetchByUser( $userId ) {
    $objects = array();
    $args = array(
      'post_type' => 'quizmaster_score',
      'orderby' => array(
        'date' => 'DESC',
      ),
      'meta_query' => array(
        'key' => 'qmsc_user',
        'value' => $userId
      ),
      'posts_per_page'=> -1
    );
    $query = new WP_Query( $args );
    if( !$query->have_posts() ) {
      return false;
    }
    foreach( $query->posts as $post ) {
      $objects[] = $this->fetch( $post->ID );
    }
    return $objects;
  }

  /**
   * @return QuizMaster_Model_Score[]
   */
  public function fetchAll() {
    $objects = array();
    $args = array(
      'post_type' => 'quizmaster_score',
      'orderby' => array(
        'date' => 'DESC',
      ),
      'posts_per_page'=> -1
    );
    $query = new WP_Query( $args );
    if( !$query->have_posts() ) {
      return false;
    }
    foreach( $query->posts as $post ) {
      $objects[] = $this->fetch( $post->ID );
    }
    return $objects;
  }

  public function fetch( $id ) {
    return new QuizMaster_Model_Score( $id );
  }

	public function fetchByUserQuiz( $userId, $quizId ) {
    $objects = array();
    $args = array(
      'post_type' => 'quizmaster_score',
      'orderby' => array(
        'date' => 'DESC',
      ),
      'meta_query' => array(
				array(
					'key' 	=> 'qmsc_user',
	        'value' => $userId
				),
				array(
					'key' 	=> 'qmsc_quiz',
	        'value' => $quizId
				),
      ),
      'posts_per_page'=> -1
    );
    $query = new WP_Query( $args );
    if( !$query->have_posts() ) {
      return false;
    }
    foreach( $query->posts as $post ) {
      $objects[] = $this->fetch( $post->ID );
    }
    return $objects;
  }

}
