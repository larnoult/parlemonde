<?php

class QuizMaster_View_Taxonomies extends QuizMaster_View_View
{

    public function show()
    {
        ?>

        <div class="wrap">

          <h3>Quiz Categories</h3>
          <a class="button-primary" href="<?php print admin_url('edit-tags.php?taxonomy=quizmaster_quiz_category') ?>">Quiz Categories</a>

          <h3>Quiz Tags</h3>
          <a class="button-primary" href="<?php print admin_url('edit-tags.php?taxonomy=quizmaster_quiz_tag') ?>">Quiz Tags</a>

          <h3>Question Categories</h3>
          <a class="button-primary" href="<?php print admin_url('edit-tags.php?taxonomy=quizmaster_question_category') ?>">Question Categories</a>

          <h3>Question Tags</h3>
          <a class="button-primary" href="<?php print admin_url('edit-tags.php?taxonomy=quizmaster_question_tag') ?>">Question Tags</a>

        </div>

        <?php
    }
}
