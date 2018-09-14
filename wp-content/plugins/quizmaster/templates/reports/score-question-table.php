<?php

// category handling
$cats = array();
foreach( $scoreView->getScoreQuestions() as $scoreQuestion ) {

	$question = QuizMaster_Model_QuestionMapper::fetch( $scoreQuestion->getQuestionId() );
	$catId = $question->getCategoryId();
	if( !$catId ) {
		$catId = 0;
	}
	$cats[ $catId ][] = array(
		'question' => $question,
		'score' => $scoreQuestion,
	);

}

?>

<!-- Categorized Scores Table -->
<table>
  <tr>
    <th>Question</th>
    <th>Points</th>
    <th>Correct</th>
    <th>Hint</th>
    <th>Solved</th>
    <th>Time</th>
  </tr>

  <?php foreach( $cats as $cat ) : ?>

    <tr>
      <th colspan="6">Category: <?php print $cat[0]['question']->getCategoryName(); ?></th>
    </tr>

	  <?php foreach( $scoreView->getScoreQuestions() as $scoreQuestion ) :

	    $scoreView->setActiveScoreQuestion( $scoreQuestion );

	  ?>

    <tr>
      <td><?php print $scoreView->getQuestion(); ?></td>
      <td><?php print $scoreView->getPoints() . '/' . $scoreView->getPossiblePoints(); ?></td>
			<td><?php $scoreView->renderCorrect(); ?></td>
      <td><?php $scoreView->renderHint(); ?></td>
      <td><?php $scoreView->renderSolved(); ?></td>
      <td><?php $scoreView->renderQuestionTime(); ?></td>
    </tr>

  <?php endforeach; ?>

  <!-- Subotal Row -->
  <tr>
    <th><?php _e('Subtotal', 'quizmaster'); ?></th>
    <th><?php print $scoreView->getPoints() . '/' . $scoreView->getPossiblePoints(); ?></th>
    <th><?php print $scoreView->getScoreTotal( 'correctCount' ) . '/' . $scoreView->getScoreTotal( 'totalQuestionCount' ); ?></th>
    <th><?php print $scoreView->getScoreTotal( 'hintCount' ); ?></th>
    <th><?php print $scoreView->getScoreTotal( 'solvedCount' ); ?></th>
    <th><?php $scoreView->renderTotalQuestionTime(); ?></th>
  </tr>

  <?php endforeach; ?>

  <tfoot>

  	<!-- Totals Row -->
    <tr>
      <th><?php _e('Total', 'quizmaster'); ?></th>
      <th><?php print $scoreModel->getTotalPointsEarned() . '/' . $scoreModel->getTotalPointsPossible(); ?></th>
      <th><?php print $scoreModel->getCorrectRatio(); ?></th>
      <th><?php print $scoreModel->getTotalHints(); ?></th>
      <th><?php print $scoreModel->getTotalSolved(); ?></th>
      <th><?php print gmdate( "H:i:s", $scoreModel->getTotalTime() ); ?></th>
    </tr>

  </tfoot>

</table>
