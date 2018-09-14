<!-- Grades Quiz Box -->
<div class="qm-grades-box">
	<div class="qm-grades-pass-message"></div>
	<div class="qm-grades-title"></div>
	<div class="qm-grades-achievement-message"></div>
</div>

<!-- Grades Data -->
<div class="qm-grades-data">

	<div class="qm-grade-settings"
		data-grades-enabled="<?php print $gradesEnabled; ?>"
		data-passing-grade="<?php print $passingGrade; ?>"
		data-passing-grade-message="<?php print $passingGradeMessage; ?>"
		data-failing-grade-message="<?php print $failingGradeMessage; ?>"
		>
	</div>

	<?php

		if( !empty( $grades )) {
			foreach( $grades as $grade ) {

	?>

		<div class="qm-grade"
			data-requirement="<?php print $grade->requirement; ?>"
			data-title="<?php print $grade->title; ?>"
			data-description="<?php print $grade->description; ?>"
			data-achievement-message="<?php print $grade->achievementMessage; ?>"
		>
		</div>

	<?php

			}
		}

	?>

</div>
