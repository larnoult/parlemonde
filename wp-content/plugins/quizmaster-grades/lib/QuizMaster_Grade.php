<?php

class QuizMaster_Grade {

	public $requirement;
	public $title;
	public $description;
	public $achievementMessage;

	public function __construct( $args ) {

		$this->requirement 				= $args['requirement'];
		$this->title 							= $args['title'];
		$this->description				= $args['description'];
		$this->achievementMessage	= $args['achievementMessage'];

	}



}
