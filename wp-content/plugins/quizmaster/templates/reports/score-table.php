<div class="quizmaster-wrap">

	<h2>Quiz Scores</h2>

	<table id="quizmaster_score_table" class="quizmaster-table display">
	  <thead>
	    <tr>
	      <th>Quiz</th>
	      <th>Taken At</th>
	      <th class="dt-center">Points</th>
	      <th class="dt-center">Correct</th>
	      <th>&nbsp;</th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach( $scores as $score ) : ?>
	      <tr>
	        <td><?php print $view->getQuizTitle( $score ); ?></td>
	        <td><?php print $score->getDate(); ?></td>
	        <td class="dt-center"><?php print $score->getPointsEarned(); ?></td>
	        <td class="dt-center"><?php print $score->getCorrectRatio() ?></td>
	        <td class="dt-right"><?php print $view->getLink( $score, "View Details"); ?></td>
	      </tr>
	    <?php endforeach; ?>
	  </tbody>
	</table>

</div>
