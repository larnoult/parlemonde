<?php

class QuizMaster_Controller_Taxonomies extends QuizMaster_Controller_Controller {

  public function route() {
    $this->showView();
  }

  private function showView() {
    $view = new QuizMaster_View_Taxonomies();
    $view->show();
  }

}
