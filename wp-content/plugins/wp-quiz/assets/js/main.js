/*
 * WP Quiz Plugin by MyThemeShop
 * https://mythemeshop.com/plugins/wp-quiz/
*/

/* global wq_l10n */
;(function( $ ) {

	'use strict';

	// Move or create fb-root
	function moveFbRoot() {
		var fbRoot = $( '#fb-root' );
		if ( fbRoot.length > 0 ) {
			$( 'body' ).prepend( fbRoot );
		} else {
			$( 'body' ).prepend( '<div id="fb-root"/>' );
		}
	}

	moveFbRoot();

	if ( 'function' === typeof $.fn.flip ) {
		$( '.wq_IsFlip .card' ).flip();
	}

	$( document ).on( 'click', '.wq_btn-continue', function( event ) {

		event.preventDefault();

		var quizElem			= $( this ).closest( '.wq_quizCtr' ),
			curQ				= parseInt( quizElem.data( 'current-question' ) ),
			curQElem			= quizElem.find( '.wq_questionsCtr > .wq_singleQuestionWrapper' ).eq( curQ ),
			totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) ),
			questionsAnswered	= parseInt( quizElem.data( 'questions-answered' ) ),
			questionPercent		= parseInt( ( questionsAnswered / totalQuestionsNum ) * 100 );

		if ( ! curQElem.next().length ) {
			return;
		}

		curQElem.transition({
			animation: quizElem.data( 'transition_out' ),
			onComplete: function() {

				curQElem.hide();

				curQElem.next().transition({
					animation: quizElem.data( 'transition_in' )
				});

				quizElem.data( 'current-question', curQ + 1 );
			}
		});

		quizElem.find( '.wq_quizProgressValue' ).animate({
			width: questionPercent + '%'
		}).text( questionPercent + '%' );

		if ( 1 === parseInt( quizElem.data( 'auto-scroll' ) ) ) {
			$( 'html, body' ).animate({
				scrollTop: quizElem.offset().top - 35
			}, 750 );
		}
	});

	$( document ).on( 'click', '.wq_retakeQuizBtn', function( event ) {

		event.preventDefault();

		var quizElem		= $( this ).closest( '.wq_quizCtr' ),
			animationIn		= quizElem.data( 'transition_in' ),
			animationOut	= quizElem.data( 'transition_out' ),
			questionLayout	= quizElem.data( 'question-layout' );

		// Reset Quiz
		quizElem.data( 'current-question', 0 );
		quizElem.data( 'questions-answered', 0 );
		quizElem.data( 'correct-answered', 0 );
		quizElem.find( '.wq_quizProgressValue' ).animate({
			width: '0%'
		}).text( '' );

		// Reset All Questions
		quizElem.find( '.wq_questionsCtr > .wq_singleQuestionWrapper' ).each(function() {

			var $this = $( this );

			$this.find( '.wq_triviaQuestionExplanation' ).removeClass( 'transition visible' );
			$this.find( '.wq_triviaQuestionExplanation' ).hide();
			$this.find( '.wq_singleAnswerCtr' ).removeClass( 'wq_incorrectAnswer wq_correctAnswer chosen wq_answerSelected' );
			$this.find( '.wq_ExplanationHead' ).removeClass( 'wq_correctExplanationHead wq_wrongExplanationHead' );
			$this.data( 'question-answered', 1 );
			$this.removeClass( 'wq_questionAnswered' );
		});

		// Reset Results
		quizElem.find( '.wq_singleResultWrapper, .wq_singleResultRow' ).data( 'points', 0 );

		// Hide results and show first question
		quizElem.find( '.wq_questionsCtr' ).show();
		quizElem.find( '.wq_singleResultWrapper.transition.visible' ).transition({
			animation: animationOut,
			onComplete: function() {
				quizElem.find( '.wq_singleResultWrapper' ).hide();
				if ( 'multiple' === questionLayout ) {
					quizElem.find( '.wq_questionsCtr > .wq_singleQuestionWrapper:last' ).transition({
						animation: animationOut,
						onComplete: function() {
							quizElem.find( '.wq_questionsCtr > .wq_singleQuestionWrapper:eq(0)' ).transition({
								animation: animationIn
							});
						}
					});
				}

				// Reset Results
				quizElem.find( '.wq_singleResultWrapper, .wq_resultsTable, .wq_shareResultCtr, .wq_resultsCtr, .wq_quizEmailCtr, .wq_quizForceShareCtr, .wq_retakeQuizBtn, .wq_retakeQuizCtr' ).removeClass( 'transition hidden visible' );
				quizElem.find( '.wq_resultExplanation, .wq_quizEmailCtr, .wq_quizForceShareCtr, .wq_retakeQuizBtn' ).hide();
			}
		});

		$( 'html, body' ).animate({
			scrollTop: quizElem.offset().top - 35
		}, 750 );

		$( this ).removeClass( 'transition visible' ).hide();
	});

	// Share - Facebook
	$( document ).on( 'click', '.wq_shareFB', function( event ) {

		event.preventDefault();

		var quizElem	= $( this ).closest( '.wq_quizCtr' ),
			shareURL	= quizElem.data( 'share-url' ) ? quizElem.data( 'share-url' ) : document.referrer,
			resultElem	= quizElem.find( '.wq_singleResultWrapper:visible' ),
			description	= resultElem.find( '.wq_resultDesc' ).text(),
			picture		= resultElem.find( '.wq_resultImg' ).attr( 'src' ),
			shareText;

		if (  resultElem.hasClass( 'wq_IsTrivia' ) ) {
			var correctAnswered		= parseInt( quizElem.data( 'correct-answered' ) ),
				totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) );

			shareText = wq_l10n.captionTriviaFB.replace( '%%score%%', correctAnswered ).replace( '%%total%%', totalQuestionsNum );

		} else if ( resultElem.hasClass( 'wq_IsPersonality' )  ) {
			shareText = resultElem.find( '.wq_resultTitle' ).data( 'title' );
		} else {
			shareText = quizElem.data( 'post-title' );
		}

		var base64 = {
			id: parseInt( quizElem.data( 'quiz-pid' ) ),
			rid: parseInt( resultElem.data( 'id' ) ),
			pic: picture ? 'r' : 'f',
			text: shareText,
			desc: description ? 'r' : 'e',
			ts: Date.now()
		};

		base64 = $.param( base64 );

		FB.ui({
			method: 'share',
			href: shareURL + '?fbs=1&' + base64
		}, function() {});
	});

	// Share - Twitter
	$( document ).on( 'click', '.wq_shareTwitter', function( event ) {

		event.preventDefault();

		var quizElem	= $( this ).closest( '.wq_quizCtr' ),
			shareURL	= quizElem.data( 'share-url' ) ? quizElem.data( 'share-url' ) : document.referrer,
			resultElem	= quizElem.find( '.wq_singleResultWrapper:visible' ),
			description	= resultElem.find( '.wq_resultDesc' ).text(),
			picture		= resultElem.find( '.wq_resultImg' ).attr( 'src' ),
			shareText = '';

		if ( resultElem.hasClass( 'wq_IsTrivia' ) ) {
			var correctAnswered		= parseInt( quizElem.data( 'correct-answered' ) ),
				totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) );

			shareText = wq_l10n.captionTriviaFB.replace( '%%score%%', correctAnswered ).replace( '%%total%%', totalQuestionsNum );
		} else if ( resultElem.hasClass( 'wq_IsPersonality' ) ) {
			shareText = resultElem.find( '.wq_resultTitle' ).data( 'title' );
		} else {
			shareText = quizElem.data( 'post-title' );
		}

		var base64 = {
			id: parseInt( quizElem.data( 'quiz-pid' ) ),
			rid: parseInt( resultElem.data( 'id' ) ),
			pic: picture ? 'r' : 'f',
			desc: description ? 'r' : 'e',
			text: shareText
		};

		base64 = $.param( base64 );

		window.open(
			'https://twitter.com/share?url=' + encodeURIComponent( shareURL + '?fbs=1&' + base64 ),
			'_blank',
			'width=500, height=300'
		);
	});

	// Share - Google+
	$( document ).on( 'click', '.wq_shareGP', function( event ) {

		event.preventDefault();

		var quizElem	= $( this ).closest( '.wq_quizCtr' ),
			shareURL	= quizElem.data( 'share-url' ) ? quizElem.data( 'share-url' ) : document.referrer,
			resultElem	= quizElem.find( '.wq_singleResultWrapper:visible' ),
			description	= resultElem.find( '.wq_resultDesc' ).text(),
			picture		= resultElem.find( '.wq_resultImg' ).attr( 'src' ),
			shareText = '';

		if ( resultElem.hasClass( 'wq_IsTrivia' ) ) {
			var correctAnswered		= parseInt( quizElem.data( 'correct-answered' ) ),
				totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) );

			shareText = wq_l10n.captionTriviaFB.replace( '%%score%%', correctAnswered ).replace( '%%total%%', totalQuestionsNum );
		} else if ( resultElem.hasClass( 'wq_IsPersonality' ) ) {
			shareText = resultElem.find( '.wq_resultTitle' ).data( 'title' );
		} else {
			shareText = quizElem.data( 'post-title' );
		}

		var base64 = {
			id: parseInt( quizElem.data( 'quiz-pid' ) ),
			rid: parseInt( resultElem.data( 'id' ) ),
			pic: picture ? 'r' : 'f',
			desc: description ? 'r' : 'e',
			text: shareText
		};

		base64 = $.param( base64 );

		window.open(
			'https://plus.google.com/share?url=' + encodeURIComponent( shareURL + '?fbs=1&' + base64 ),
			'_blank',
			'width=500, height=300'
		);
	});

	// Share - VK
	$( document ).on( 'click', '.wq_shareVK', function( event ) {

		event.preventDefault();

		var quizElem = $( this ).closest( '.wq_quizCtr' ),
			shareURL = quizElem.data( 'share-url' ) ? quizElem.data( 'share-url' ) : document.referrer;

		window.open(
			'http://vk.com/share.php?url=' + shareURL,
			'_blank',
			'width=500, height=300'
		);
	});

	// Document Ready
	$( document ).ready(function() {

		function flipDescResizeAndReady( event ) {

			$( '.wq_IsFlip .back' ).each(function() {

				var $this = $( this ),
					bImg = $this.find( 'img' ).attr( 'src' ),
					titleH = $( this ).siblings( '.item_top' ).height();

				$this.css( 'top', titleH + 'px' );

				if ( '' === bImg ) {

					$this.siblings( '.front' ).find( 'img' ).on( 'load', function() {
						$this.find( '.desc' ).height( $( this ).height() );
					});

					if ( 'resize' === event.type ) {
						var imgHeight = $this.siblings( '.front' ).find( 'img' ).height();
						$this.find( '.desc' ).height( imgHeight );
					}
				}
			});
		}

		$( document ).on( 'ready', flipDescResizeAndReady );
		$( window ).on( 'resize', flipDescResizeAndReady );
		$( window ).trigger( 'resize' );
	});

	// Trivia
	function processResults( quizElem ) {
		var animationIn			= quizElem.data( 'transition_in' ),
			correctAnswered		= parseInt( quizElem.data( 'correct-answered' ) ),
			totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) ),
			isRetakeable		= parseInt( quizElem.data( 'retake-quiz' ) );

		quizElem.find( '.wq_continue' ).hide();

		var resultFound = false;

		quizElem.find( '.wq_singleResultWrapper' ).each(function() {
			var $result = $( this ),
				min		= parseInt( $result.data( 'min' ) ),
				max		= parseInt( $result.data( 'max' ) );

			if ( correctAnswered >= min && correctAnswered <= max && ! resultFound ) {
				resultFound	= true;
				var title = wq_l10n.captionTrivia.replace( '%%score%%', correctAnswered ).replace( '%%total%%', totalQuestionsNum );
				$result.find( '.wq_resultScoreCtr' ).text( title );
				$result.transition({ animation: animationIn });
				return;
			}
		});

		if ( isRetakeable ) {
			quizElem.find( '.wq_retakeQuizBtn' ).transition({ animation: animationIn });
		}
	}

	$( document ).on( 'click', '.wq_singleQuestionWrapper:not(.wq_questionAnswered) .wq_singleAnswerCtr.wq_IsTrivia', function( event ) {

		event.preventDefault();

		var $this				= $( this ),
			questionElem		= $this.closest( '.wq_singleQuestionWrapper' ),
			quizElem			= $this.closest( '.wq_quizCtr' ),
			totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) ),
			questionsAnswered	= parseInt( quizElem.data( 'questions-answered' ) ) + 1,
			correctAnswered		= parseInt( quizElem.data( 'correct-answered' ) ),
			curQ				= parseInt( quizElem.data( 'current-question' ) ),
			questionLayout		= quizElem.data( 'question-layout' );

		questionElem.addClass( 'wq_questionAnswered' );

		// Process Correct Answer
		if ( 1 === parseInt( $this.data( 'crt' ) ) ) {

			correctAnswered++;
			$this.addClass( 'wq_correctAnswer chosen' );
			questionElem.find( '.wq_triviaQuestionExplanation .wq_ExplanationHead' ).text( wq_l10n.correct ).addClass( 'wq_correctExplanationHead' );
			quizElem.data( 'correct-answered', correctAnswered );

		} else {
			questionElem.find( '.wq_singleAnswerCtr' ).each(function() {
				if ( 1 === $( this ).data( 'crt' ) ) {
					$( this ).addClass( 'wq_correctAnswer' );
				}
			});
			$this.addClass( 'wq_incorrectAnswer chosen' );
			questionElem.find( '.wq_triviaQuestionExplanation .wq_ExplanationHead' ).text( wq_l10n.wrong ).addClass( 'wq_wrongExplanationHead' );
		}

		if ( 'single' === questionLayout ) {
			curQ = parseInt( quizElem.data( 'current-question' ) );
			quizElem.data( 'current-question', curQ + 1 );
		} else {
			questionElem.find( '.wq_continue' ).show();
		}

		quizElem.data( 'questions-answered', questionsAnswered );
		questionElem.find( '.wq_triviaQuestionExplanation' ).show();

		if ( 1 === parseInt( quizElem.data( 'auto-scroll' ) ) ) {
			$( 'html, body' ).animate({
				scrollTop: questionElem.find( '.wq_triviaQuestionExplanation' ).offset().top - 35
			}, 750 );
		}

		if ( totalQuestionsNum === questionsAnswered ) {
			quizElem.find( '.wq_quizProgressValue' ).animate({ width: '100%' }).text( '100%' );
			processResults( quizElem );
			return;
		}
	});

	// Personality
	$( document ).on( 'click', '.wq_singleQuestionWrapper:not(.wq_questionAnswered) .wq_singleAnswerCtr.wq_IsPersonality', function( event ) {

		event.preventDefault();

		var $this				= $( this ),
			quizElem			= $this.closest( '.wq_quizCtr' ),
			resultsInfo			= JSON.parse( $this.find( '.wq_singleAnswerResultCtr' ).val() ),
			curQElem			= $this.closest( '.wq_singleQuestionWrapper' ),
			questionsAnswered	= parseInt( quizElem.data( 'questions-answered' ) ),
			totalQuestionsNum	= parseInt( quizElem.data( 'questions' ) ),
			animationIn			= quizElem.data( 'transition_in' ),
			isRetakeable		= parseInt( quizElem.data( 'retake-quiz' ) ),
			questionLayout		= quizElem.data( 'question-layout' ),
			autoScroll			= parseInt( quizElem.data( 'auto-scroll' ) );

		curQElem.addClass( 'wq_questionAnswered' );

		// Remove Any Points from Previous Selected Result if Any
		curQElem.find( '.wq_singleAnswerCtr.wq_answerSelected' ).each(function() {

			var oldResInfo = JSON.parse( $( this ).find( '.wq_singleAnswerResultCtr' ).val() );

			if ( '' !== oldResInfo )	{
				oldResInfo.forEach(function( ele, ind, arr ) {

					var resultElem		= quizElem.find( '.wq_singleResultWrapper[data-rid="' + ind + '"]' ),
						resultPoints	= parseInt( resultElem.data( 'points' ) ) - parseInt( ele.points );

					resultElem.data( 'points', resultPoints );
				});
			}
		});

		// Add new Points
		if ( '' !== resultsInfo ) {
			resultsInfo.forEach(function( ele, ind, arr ) {

				var resultElem		= quizElem.find( '.wq_singleResultWrapper[data-rid="' + ind + '"]' ),
					resultPoints	= parseInt( resultElem.data( 'points' ) ) + parseInt( ele.points );

				resultElem.data( 'points', resultPoints );
			});
		}

		// Increment Questions Answered
		if ( 1 === parseInt( curQElem.data( 'question-answered' ) ) ) {

			questionsAnswered++;
			curQElem.data( 'question-answered', 2 );
			quizElem.data( 'questions-answered', questionsAnswered );
		}

		$this.addClass( 'wq_answerSelected' );

		if ( 'single' === questionLayout ) {

			var curQ = parseInt( quizElem.data( 'current-question' ) );

			quizElem.data( 'current-question', curQ + 1 );

			if ( curQElem.next().length && 1 === autoScroll ) {
				$( 'html, body' ).animate({
					scrollTop: curQElem.next().offset().top - 75
				}, 750 );
			}
		} else {
			curQElem.find( '.wq_btn-continue' ).trigger( 'click' );
		}

		if ( totalQuestionsNum !== questionsAnswered ) {
			return;
		}

		quizElem.find( '.wq_quizProgressValue' ).animate({ width: '100%' }).text( '100%' );
		$( 'html, body' ).animate({
			scrollTop: quizElem.find( '.wq_resultsCtr' ).offset().top - 75
		}, 750 );

		var resultElem	= null,
			maxPoints	= 0;

		quizElem.find( '.wq_singleResultWrapper' ).each(function() {

			var resultPoints = parseInt( $( this ).data( 'points' ) );

			if ( resultPoints > maxPoints ) {
				maxPoints	= resultPoints;
				resultElem	= $( this );
			}
		});

		resultElem.transition({ animation: animationIn });

		if (  isRetakeable ) {
			quizElem.find( '.wq_retakeQuizBtn' ).transition({ animation: animationIn });
		}
	});

})( jQuery );
