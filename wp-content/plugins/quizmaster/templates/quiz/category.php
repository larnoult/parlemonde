<?php if( $question->getCategoryId() ) { ?>
  <div class="quiz-category">
      <?php printf(__('Category: %s', 'quizmaster'),
          esc_html( $question->getCategoryName() )); ?>
  </div>
<?php } ?>
