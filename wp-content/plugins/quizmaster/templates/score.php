<?php

/**
 * Template for displaying quiz scores
 *
 * @package QuizMaster
 * @since 1.0
 * @version 1.0
 *
 *
 * Thanks to Easy Pie Chart, https://github.com/rendro/easy-pie-chart
 * https://rendro.github.io/easy-pie-chart/
 *
 *
 */

$scoreCtr = QuizMaster_Controller_Score::loadById( $post->ID );
$scoreView = new QuizMaster_View_Score;
$scoreModel = $scoreCtr->getScore();
$scoreView->setScoreQuestions( $scoreModel->getScores() );

?>

<div id="main" class="site-main" role="main">

	<h1>Quiz Score</h1>

	<div class="quizmaster-score-summary">

		<table class="quizmaster-table quizmaster-info-table display info">

			<thead>
				<tr>
					<th></th>
					<th></th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td>Quiz</td>
					<td><?php print $scoreModel->getQuizName(); ?></td>
				</tr>
				<tr>
					<td>User</td>
					<td><?php print $scoreModel->getUserName(); ?></td>
				</tr>
				<tr>
					<td>Overall Score</td>
					<td><?php print $scoreModel->getScoreResult(); ?>%</td>
				</tr>
				<tr>
					<td>Correct Questions</td>
					<td><?php print $scoreModel->getCorrectRatio(); ?></td>
				</tr>
				<tr>
					<td>Questions Solved</td>
					<td><?php print $scoreModel->getSolvedPercentage(); ?>%</td>
				</tr>
				<tr>
					<td>Questions Correct</td>
					<td><?php print $scoreModel->getQuestionsCorrectPercentage(); ?>%</td>
				</tr>
				<tr>
					<td>Questions Incorrect</td>
					<td><?php print $scoreModel->getQuestionsIncorrectPercentage(); ?>%</td>
				</tr>
				<tr>
					<td>Completion Time</td>
					<td><?php print $scoreModel->getTotalTime(); ?>%</td>
				</tr>
				<tr>
					<td>Total Solved</td>
					<td><?php print $scoreModel->getTotalSolved(); ?></td>
				</tr>
				<tr>
					<td>Hints Used</td>
					<td><?php print $scoreModel->getTotalHints(); ?></td>
				</tr>

			</tbody>
		</table>

	<!-- Return Link -->
	<?php
		$user = wp_get_current_user();
		$adminLink = false;
		if ( in_array( 'administrator', (array) $user->roles ) ) {
			$adminLink = true;
		}

		if( $adminLink ) {
			$returnUrl = admin_url( 'edit.php?post_type=quizmaster_score' );
		} else {
			$returnUrl = home_url('student-report');
		}
	?>
	<div class="quizmaster-score-return-button">
		<button class="quizmaster-score-return-button">
			<a class="quizmaster-score-return-link" href="<?php print $returnUrl; ?>">Return to Scores List</a>
		</button>
	</div>


</main><!-- #main -->
</div><!-- #primary -->

<script>
jQuery(".main-pie").easyPieChart({
		trackColor: "#000",
		scaleColor: "#999",
		barColor: "#999",
		lineWidth: 2,
		lineCap: "butt",
		size: 150
});
jQuery(".sub-pie-1").easyPieChart({
		trackColor: "rgba(255,255,255,0.2)",
		scaleColor: "rgba(255,255,255,0)",
		barColor: "rgba(255,255,255,0.7)",
		lineWidth: 2,
		lineCap: "butt",
		size: 90
});
jQuery(".sub-pie-2").easyPieChart({
		trackColor: "rgba(255,255,255,0.2)",
		scaleColor: "rgba(255,255,255,0)",
		barColor: "rgba(255,255,255,0.7)",
		lineWidth: 2,
		lineCap: "butt",
		size: 90
});

</script>

<style>

.quizmaster-wrap .quizmaster-score-summary h2 {
	margin: 6px 0;
	padding: 0;
}

.easyPieChart {
    position: relative;
    text-align: center;
}

.easyPieChart canvas {
    position: absolute;
    top: 0;
    left: 0;
}


</style>
