<?php

class QuizMaster_Controller_Support extends QuizMaster_Controller_Controller {

  public function route() {
    $this->showView();
  }

  private function showView() {
    $view = new QuizMaster_View_Support();
    $view->show();
  }

}
