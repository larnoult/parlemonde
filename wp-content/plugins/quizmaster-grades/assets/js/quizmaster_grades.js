(function($) {


	$(document).on( 'quizmasterQuizCompleted', quizCompleted );

	var gradesBox = $('.qm-grades-box');

	function quizCompleted( e ) {

		// get data
		var results = e.values.results;
		var gradesData = getGradesData();
		var resultPercentage = results.comp.result

		// set pass or fail
		var passed = passingGrade( resultPercentage, gradesData.passingGrade );
		if( passed == true ) {
			$('.qm-grades-pass-message').html('Congratulations, You Passed the Quiz')
			gradesBox.addClass('qm-grades-passed')
		} else {
			$('.qm-grades-pass-message').html('Sorry, You Failed the Quiz')
			gradesBox.addClass('qm-grades-failed')
		}

		// set grade
		var gradeAwarded = awardGrade( resultPercentage, gradesData.grades );

		if( gradeAwarded != false ) {
			$('.qm-grades-title').html( gradeAwarded.title );
			$('.qm-grades-achievement-message').html( gradeAwarded.achievementMessage );
		}

	}

	// award grade
	function awardGrade( resultPercentage, grades ) {

		var gradeAwarded = false;

		$.each( grades, function( index, value ) {

			// award grade if score qualifies, and no grade yet awarded, or this grade is better
			if( resultPercentage >= value.requirement && gradeAwarded == false || gradeAwarded.requirement <= value.requirement ) {
				gradeAwarded = value;
			}
		});

		return gradeAwarded;

	}

	// check if quiz taker passed
	function passingGrade( resultPercentage, passingGrade ) {

		if( resultPercentage >= passingGrade ) {
			return true
		}
		return false;

	}

	function getGradesData() {

		var gradeSettings = $('.qm-grade-settings');
		var gradesList = $('.qm-grade');
		var grades = {};

		gradesList.each( function( index, element ){

			grades[index] = {}
			grades[index].requirement = $( this ).data( 'requirement' );
			grades[index].title = $( this ).data( 'title' );
			grades[index].description = $( this ).data( 'description' );
			grades[index].achievementMessage = $( this ).data( 'achievement-message' );

		});

		var gradeData = {
			gradesEnabled: gradeSettings.data('grades-enabled'),
			passingGrade: gradeSettings.data('passing-grade'),
			grades: grades,
		}

		return gradeData;

	}

})( jQuery );
