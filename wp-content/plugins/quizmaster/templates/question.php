<?php

$qCtr = new QuizMaster_Controller_Question();
$qCtr->load( $post->ID );
$qCtr->render();
