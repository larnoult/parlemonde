<?php

if ( is_object( $quiz ) && $quiz->isShowPoints() ) { ?>

  <span style="font-weight: bold; float: right;"><?php printf(__('%d points', 'quizmaster'),
    $question->getPoints()); ?></span>

<?php } ?>
