jQuery(document).ready(function( $ ) {

	// QuizMaster jQuery plugin
	$.fn.quizmaster = function( options = false ) {

		var quizmaster = this;

		quizmaster.config = {},
		quizmaster.status = {};
		quizmaster.restarted = false,
		quizmaster.finish = false;

		quizmaster.data = {
			results: new Object(),
			catResults: new Object(),
			currentQuestion: false,
			currentQuestionId: 0,
			quizSolved: [],
			lastButtonValue: "",
			inViewQuestions: false,
			currentPage: 1,
			isQuizStarted: false,
		};

		quizmaster.elements = {
			checkButtonClass: '.qm-button-check',
			nextButtonClass: '.qm-button-next',
			finishButtonClass: '.qm-button-finish',
			skipButtonClass: '.qm-skip-button',
			singlePageLeft: 'input[name="quizMaster_pageLeft"]',
			singlePageRight: 'input[name="quizMaster_pageRight"]',
			startButton: quizmaster.find('.qm-start-button'),
			backButtonClass: '.qm-back-button',
			backButton: quizmaster.find('.qm-back-button'),
			nextButton: quizmaster.find('.qm-button-next'),
			finishButton: quizmaster.find('.qm-button-finish'),
			skipButton: quizmaster.find('.qm-skip-button'),
			checkButton: quizmaster.find('.qm-button-check'),
			restartButtonClass: '.qm-restart-quiz-button',
			restartButton: quizmaster.find('.qm-restart-quiz-button'),
			questionReviewButton: quizmaster.find('.qm-question-review-button'),
			quiz: quizmaster.find('.quizMaster_quiz'),
			questionListClass: '.qm-question-list',
			questionList: quizmaster.find('.quizMaster_list'),
			resultsBox: quizmaster.find('.qm-results-box'),
			reviewBox: quizmaster.find('.qm-review-box'),
			questionCheck: quizmaster.find('.qm-check-answer-box'),
			startPage: quizmaster.find('.qm-quiz-start-box'),
			timeLimitBox: quizmaster.find('.qm-time-limit'),
			hintTrigger: quizmaster.find('.qm-hint-trigger'),
			listItems: $()
		};

		quizmaster.startPageShow = function() {

			quizmaster.elements.startPage.show();

			// hide hint button
			quizmaster.hint.buttonHide();

			// hide next button
			quizmaster.elements.nextButton.hide();

			// hide next button
			quizmaster.elements.finishButton.hide();

		};

		quizmaster.startPageHide = function() {
			quizmaster.elements.startPage.hide();
		};

		/*
  		* Moves quiz to next question
		 */
		quizmaster.nextQuestion = function () {

			if( !quizmaster.data.currentQuestion.next().length ) {
				return; // no next question (end of quiz)
			}

			quizmaster.showQuestionObject( quizmaster.data.currentQuestion.next() );

			// question show event
			quizmaster.trigger({
				type: 'quizmaster.nextQuestion',
				nextQuestion: quizmaster.data.currentQuestion.next(),
				currentQuestion: quizmaster.data.currentQuestion,
			});

		};

		quizmaster.prevQuestion = function () {
			quizmaster.showQuestionObject( quizmaster.data.currentQuestion.prev() );

			quizmaster.fireChangeScreenEvent('question')
		};

		quizmaster.getCurrentQuestion = function () {
			return quizmaster.data.currentQuestion;
		}

		quizmaster.isLastQuestion = function( $question ) {

			if( $question == undefined ) {
				$question = quizmaster.getCurrentQuestion()
			}

			if( quizmaster.questionCount() == $question.index() +1 ) {
				return true;
			}

			return false;

		}

		quizmaster.isFirstQuestion = function( $questionId ) {
			if( 0 == quizmaster.data.currentQuestion.index() ) {
				return true;
			}

			return false;
		}

		quizmaster.getCurrentQuestionId = function () {
			return quizmaster.data.currentQuestionId;
		}

		quizmaster.showQuestionObject = function ( obj ) {

			if( obj == 'current' ) {
				obj = quizmaster.data.currentQuestion;
			}

			// hide current question, show new and set storage of current question
			quizmaster.data.currentQuestion.hide();
			obj.show();
			quizmaster.setCurrentQuestion( obj );

			// scroll to quiz area
			quizmaster.scrollTo( quizmaster.elements.quiz );

			// question show event
			quizmaster.trigger({
				type: 'quizmaster.questionShow',
				question: quizmaster.data.currentQuestion,
				questionIndex: quizmaster.data.currentQuestion.index()
			});

			// last question load event
			if( quizmaster.questionCount() == quizmaster.data.currentQuestion.index() +1 ) {

				quizmaster.trigger({
					type: 'quizmaster.lastQuestionLoaded',
					question: quizmaster.data.currentQuestion,
					questionIndex: quizmaster.data.currentQuestion.index()
				});

			}

			quizmaster.timer.question.start( quizmaster.getCurrentQuestionId() );

		};

		quizmaster.fireChangeScreenEvent = function( $screen ) {

			// change event
			quizmaster.trigger({
				type: 'quizmaster.changeScreen',
				screen: $screen
			});

		}

		quizmaster.checkButtonInit = function() {

			quizmaster.elements.checkButton.click( function () {

				if (quizmaster.config.options.forcingQuestionSolve && !quizmaster.data.quizSolved[ quizmaster.data.currentQuestion.index() ]
					&& (quizmaster.config.options.quizSummeryHide || !quizmaster.config.options.reviewQustion)) {

					return false;
				}

				quizmaster.fireQuestionAnsweredEvent()

			});

		};

		quizmaster.userAnswerData = {

			singleMulti: function( $questionId, $questionElement ) {

				var userAnswerData = {
					answerIndexes: []
				};

				var input = $questionElement.find('.quizMaster_questionInput')

				$questionElement.find('.qm-question-list-item').each(function (i) {

					var $item = $(this);
					var index = $item.data('pos');
					var checked = input.eq(i).is(':checked');

					if( checked ) {
						userAnswerData.answerIndexes.push( index )
					}

				});

				return userAnswerData;

			},

			free: function( $questionId, $questionElement ) {

				return $questionElement.find('.quizMaster_questionInput').val();

			},

			fillBlank: function( $questionId, $questionElement ) {

				var answers = [];

				$questionElement.find('.quizMaster_cloze input').each(function (i, v) {
					answers.push( $(this).val() );
				});

				return answers;

			},

			sorting: function( $questionId, $questionElement ) {

				var answerOrder = $questionElement.find('.qm-sortable').sortable('toArray');
				return answerOrder;

			},

		};

		/*
 		 * Get Question Input
 		 * Usually checkbox, radio button element
 		 * Defaults to input from current question
		 */
		quizmaster.getQuestionInput = function( $question ) {

			if( $question == undefined ) {
				$question = quizmaster.getCurrentQuestion();
			}

			 return $question.find('.quizMaster_questionInput');

		}

		/*
 		 * Get Question Data
 		 * Question data stored in json array
 		 * Key is the question id, pass question id to load specific question data
 		 * Default return is current question loaded into quiz
		 */
		quizmaster.getQuestionData = function( $questionId ) {

			if( $questionId == undefined ) {
				$questionId = quizmaster.getCurrentQuestionId();
			}

			return quizmaster.config.json[ $questionId ];

		}

		quizmaster.checker = function ( $questionId, $questionElement ) {

			var questionData = quizmaster.config.json[ quizmaster.getCurrentQuestionId() ];

			switch( questionData.type ) {

				case 'single':
				case 'multiple':
					var userAnswerData = quizmaster.userAnswerData.singleMulti( $questionId, $questionElement )
				break;

				case 'free_answer':
					var userAnswerData = quizmaster.userAnswerData.free( $questionId, $questionElement )
				break;

				case 'fill_blank':
					var userAnswerData = quizmaster.userAnswerData.fillBlank( $questionId, $questionElement )
				break;

				case 'sort_answer':
					var userAnswerData = quizmaster.userAnswerData.sorting( $questionId, $questionElement )
				break;

			}

			quizmaster.ajax({
					action: 'quizmaster_admin_ajax',
					func: 'checkAnswer',
					data: {

						answerType: questionData.type,
						quizId: quizmaster.config.quizId,
						userAnswerData: userAnswerData,
						questionId: $questionId,


					}
			}, function (json) {

				// organize result from checking answer
				quizmaster.data.results[ $questionId ].points = json.points;
				quizmaster.data.results[ $questionId ].correct = json.correct;
				quizmaster.data.results['comp'].points += json.points;

				if( json.correct ) {
					quizmaster.data.results['comp'].correctQuestions += 1;
				}

				quizmaster.data.catResults[ questionData.catId ] += json.points;
				quizmaster.getCurrentQuestion().data('check', true);

				// answerCheckComplete event
				quizmaster.trigger({
					type: 'quizmaster.answerCheckComplete',
					question: quizmaster.getCurrentQuestion(),
					isCorrect: json.correct,
					pointsEarned: json.points,
				});

			});

		};

		quizmaster.setCheckMessagePoints = function( $pointsEarned ) {
			$('.qm-check-question-points span').text( $pointsEarned );
		}

		quizmaster.setCheckMessage = function ( $isCorrect, $pointsEarned ) {

			$questionData = quizmaster.getQuestionData();

			// points
			quizmaster.setCheckMessagePoints( $pointsEarned )

			// messages
			if ( $isCorrect ) {
				// correct answer

				$('.qm-check-message').html( $questionData.correctMessage )
				$('.qm-check-message').removeClass('qm-check-answer-incorrect')
				$('.qm-check-message').addClass('qm-check-answer-correct')

	    } else {
				$('.qm-check-message').html( $questionData.incorrectMessage )
				$('.qm-check-message').removeClass('qm-check-answer-correct')
				$('.qm-check-message').addClass('qm-check-answer-incorrect')
			}

			quizmaster.checkMessageBoxShow()

		};

		quizmaster.checkMessageBoxShow = function() {
			// show check message
			$('.qm-check-answer-box').show()
			$('.qm-check-message').show()
			$('.qm-check-result').show()
		}

		quizmaster.checkMessageBoxHide = function() {
			$('.qm-check-answer-box').hide()
			$('.qm-check-message').hide()
			$('.qm-check-result').hide()
		}

		quizmaster.getQuestions = function() {
			return quizmaster.elements.questionList.children();
		}

		/*
     * Checks multiple questions
     * Used for single page (stacked mode) quizzes where all answers submitted at once
		 */
		quizmaster.checkQuestionMultiple = function() {

			quizmaster.setStatus('check_question_multiple')

			// get all questions
			var $questionList = quizmaster.getQuestions();

			$questionList.each( function( index, element ){

				$question = $(this);
				quizmaster.setCurrentQuestion( $question );
				quizmaster.checker( quizmaster.getCurrentQuestionId(), quizmaster.getCurrentQuestion() );

				// after last question checked do finishQuiz()
				if( quizmaster.isLastQuestion( $question ) ) {

					quizmaster.finish = true;
					quizmaster.on( 'quizmaster.answerCheckComplete', function( e ) {

						$question = e.question;
						if( quizmaster.isLastQuestion( $question ) ) {
							quizmaster.finishQuiz();
						}

					});
				}

			});

			// questions.each quizmaster.checkQuestion()

		}

		/*
     * Checks a single question
		 */
		quizmaster.checkQuestion = function() {

			// move this so the function can be used by multiple check
			quizmaster.setStatus('check_question')

			// answer already checked
			if ( quizmaster.getCurrentQuestion().data('check') ) {
				return true;
			}

			// run checker to check answer
			quizmaster.checker( quizmaster.getCurrentQuestionId(), quizmaster.getCurrentQuestion() );

			// end check trigger
			quizmaster.trigger({
				type: 'quizmaster.questionChecked',
				values: {
					item: quizmaster.data.currentQuestion,
					index: quizmaster.data.currentQuestion.index(),
					solved: true,
					fake: true
				}
			});

		};

		quizmaster.questionSolved = function (e) {

			quizmaster.data.quizSolved[ e.values.index ] = e.values.solved;
			var data = quizmaster.config.json[ quizmaster.getCurrentQuestionId() ];

			quizmaster.data.results[data.id].solved = Number(e.values.fake ? quizmaster.data.results[data.id].solved : e.values.solved);

				// record as answered, solved/skipped
				if( e.values.fake ) {
					quizmaster.data.results.comp.answered++
					if( quizmaster.data.results[data.id].solved ) {
						quizmaster.data.results.comp.solved++
					} else {
						quizmaster.data.results.comp.skipped++
					}
				}
		};

		quizmaster.ajax = function (data, success, dataType) {
				dataType = dataType || 'json';

				if (quizmaster.config.options.cors) {
						jQuery.support.cors = true;
				}

				$.post(QuizMasterGlobal.ajaxurl, data, success, dataType);

				if (quizmaster.config.options.cors) {
						jQuery.support.cors = false;
				}
		};

		quizmaster.startButtonInit = function() {

			quizmaster.elements.startButton.click( function () {
				quizmaster.startQuiz();
			});

		};

		/*
     * Initialize Next Button
		 */
		quizmaster.nextButtonInit = function() {

			quizmaster.elements.nextButton.click(function () {

				if ( quizmaster.config.options.forcingQuestionSolve && !quizmaster.data.quizSolved[ quizmaster.getCurrentQuestion().index() ]
					&& ( quizmaster.config.options.quizSummeryHide || !quizmaster.config.options.reviewQustion )) {
					return false;
				}

				// question answered event
				quizmaster.fireQuestionAnsweredEvent()

			});

		};

		quizmaster.nextButtonInitCheckContinueMode = function() {

			quizmaster.elements.nextButton.click(function () {

				if ( quizmaster.config.options.forcingQuestionSolve && !quizmaster.data.quizSolved[ quizmaster.getCurrentQuestion().index() ]
					&& ( quizmaster.config.options.quizSummeryHide || !quizmaster.config.options.reviewQustion )) {
					return false;
				}

				// question answered event
				quizmaster.nextQuestion()

			});

		};

		/*
     * Initialize Finish Button
		 */
		quizmaster.finishButtonInit = function() {

			quizmaster.elements.finishButton.click(function () {

				quizmaster.finish = true;
				quizmaster.fireQuestionAnsweredEvent()

			});

		};

		quizmaster.finishButtonInitFinishQuiz = function() {

			quizmaster.elements.finishButton.click(function () {

				quizmaster.finishQuiz()

			});

		};

		quizmaster.fireQuestionAnsweredEvent = function() {

			// question answered event
			quizmaster.trigger({
				type: 'quizmaster.questionAnswered',
				question: quizmaster.getCurrentQuestion(),
			});

		};

		quizmaster.backButtonInit = function() {

			quizmaster.elements.backButton.click( function () {
				quizmaster.prevQuestion();
			});

		}

		quizmaster.startQuiz = function() {

			quizmaster.startPageHide();

			var $listItem = quizmaster.elements.questionList.children();
			quizmaster.elements.listItems = $('.quizMaster_list > li');

			// start time limit
			quizmaster.timer.limit.start();

			quizmaster.data.quizSolved = [];
			quizmaster.data.results = {
				comp: {
					points: 0,
					correctQuestions: 0,
					quizTime: 0,
					answered: 0,
					skipped: 0,
					solved: 0,
				}
			};

			$('.qm-question-list').each(function () {

					var questionId = $(this).data('question_id');

					quizmaster.data.results[ questionId ] = {
						time: 0,
						solved: 0
					};

			});

			quizmaster.data.catResults = {};
			$.each( quizmaster.config.options.catPoints, function (i, v) {
				quizmaster.data.catResults[i] = 0;
			});

			quizmaster.elements.quiz.show();

			// maybe show reviewBox
			if( quizmaster.config.options.isShowReviewQuestion ) {
				quizmaster.elements.reviewBox.show();
			}

			// maybe show skip button
			if ( quizmaster.config.options.isShowSkipButton || quizmaster.config.options.isShowReviewQuestion ) {
				quizmaster.elements.skipButton.show();
			}

			// maybe show back button
			if ( quizmaster.config.options.isShowBackButton ) {
				quizmaster.elements.backButton.show();
			}

			// start timer
			quizmaster.timer.quiz.start();

			// determine if this is a restart
			var restart = false;
			if( quizmaster.getStatus() == 'restart' ) {
				restart = true;
			}

			// quiz start event
			quizmaster.trigger({
				type: 'quizmaster.startQuiz',
				mode: quizmaster.config.mode,
				restart: restart,
			});

			// change status
			quizmaster.setStatus('started');

		};

		quizmaster.showSinglePage = function (page) {
				$listItem = quizmaster.elements.questionList.children().hide();

				if (!quizmaster.config.qpp) {
						$listItem.show();

						return;
				}

				page = page ? +page : 1;
				var maxPage = Math.ceil(quizmaster.find('.quizMaster_list > li').length / quizmaster.config.qpp);

				if (page > maxPage)
						return;

				var pl = quizmaster.find(quizmaster.elements.singlePageLeft).hide();
				var pr = quizmaster.find(quizmaster.elements.singlePageRight).hide();
				var cs = quizmaster.find('input[name="checkSingle"]').hide();

				if (page > 1) {
						pl.val(pl.data('text').replace(/%d/, page - 1)).show();
				}

				if (page == maxPage) {
					cs.show();
				} else {
					pr.val(pr.data('text').replace(/%d/, page + 1)).show();
				}

				currentPage = page;
				var start = config.qpp * (page - 1);

				$listItem.slice(start, start + config.qpp).show();
				quizmaster.scrollTo( quizmaster.elements.quiz );
		};

		quizmaster.setCurrentQuestion = function( $question ) {

			quizmaster.data.currentQuestion = $question;
			quizmaster.data.currentQuestionId = $question.find(quizmaster.elements.questionListClass).data('question_id');

		};

		quizmaster.questionCount = function () {
			return quizmaster.find('.quizMaster_listItem').length;
		};

		quizmaster.finishQuiz = function (timeover) {

			// when quiz mode is single page and not set to finish ready state, do check question multiple
			// after checkQuestionMultiple() checks all the questions quizmaster.finish is set to true
			if( quizmaster.config.mode == 2 && quizmaster.finish == false ) {
				quizmaster.checkQuestionMultiple()
				return;
			}

			// hide finish button
			quizmaster.elements.finishButton.hide();

			// deactivate hint trigger
			quizmaster.hintDisable();

			quizmaster.timer.question.stop();
			quizmaster.timer.quiz.stop();
			quizmaster.timer.limit.stop();

			var time = (+new Date() - quizmaster.timer.quizStartTime);
			time = (quizmaster.config.timeLimit && time > quizmaster.config.timeLimit) ? quizmaster.config.timeLimit : time;

			quizmaster.find('.quizMaster_quiz_time span').text( quizmaster.timer.parseTime(time) );

			if (timeover) {
				quizmaster.elements.resultsBox.find('.qm-time-limit_expired').show();
			}

			// average result
			quizmaster.data.results.comp.result = Math.round(quizmaster.data.results.comp.points / quizmaster.config.globalPoints * 100 * 100) / 100;

			quizmaster.setAverageResult(quizmaster.data.results.comp.result, false);
			quizmaster.setCategoryOverview();
			quizmaster.sendCompletedQuiz();

			/* global trigger */
			quizmaster.trigger({
				type: 'quizmaster.quizCompleted',
				questionCount: quizmaster.questionCount(),
				results: quizmaster.data.results,
			});

		};

		quizmaster.afterQuizFinish = function() {

			quizmaster.elements.reviewBox.hide();
			quizmaster.elements.quiz.hide();

			// show the correct answer count
			var correctAnswerEl = quizmaster.find('.quizMaster_correct_answer');
			correctAnswerEl.text( quizmaster.data.results.comp.correctQuestions )

			var $pointFields = quizmaster.find('.quizMaster_points span');

			$pointFields.eq(0).text(quizmaster.data.results.comp.points);
			$pointFields.eq(1).text(quizmaster.config.globalPoints);
			$pointFields.eq(2).text(quizmaster.data.results.comp.result + '%');

			// hide buttons and elements
			quizmaster.elements.nextButton.hide()
			quizmaster.elements.hintTrigger.hide()
			quizmaster.elements.checkButton.hide();
			quizmaster.elements.skipButton.hide();
			quizmaster.elements.finishButton.hide();
			quizmaster.elements.reviewBox.hide();
			quizmaster.find('.qm-check-page, .qm-info-page').hide();
			quizmaster.elements.quiz.hide();
			quizmaster.elements.resultsBox.show();
			quizmaster.scrollTo(quizmaster.elements.resultsBox);

		}

		/*
     * ScrollTo
		 */
		 quizmaster.scrollTo = function (e, h) {
       var x = e.offset().top - 100;

       if (h || (window.pageYOffset || document.body.scrollTop) > x) {
         $('html,body').animate({scrollTop: x}, 300);
       }
     }

		/*
     * Hint Handler Functions
		 */

		quizmaster.hint = {

			buttonHide: function() {
				quizmaster.elements.hintTrigger.hide();
			},

			buttonShow: function() {
				quizmaster.elements.hintTrigger.show();
			},

		};

		 quizmaster.hintInit = function() {

 			quizmaster.on('quizmaster.questionShow', function() {

				var $hint = quizmaster.getCurrentQuestion().find('.quizMaster_tipp')
				if( ! $hint.length ) {
					quizmaster.hintDisable();
				} else {
					quizmaster.hint.buttonShow();
					quizmaster.hintEnable();
				}

 			});
 		};

		 quizmaster.hintDisable = function () {

 			$tipModal = $('.qm-hint-modal');
 			$tipModal.hide();
			quizmaster.elements.hintTrigger.hide()
 			quizmaster.elements.hintTrigger.removeClass('qm-hint-enabled')
 			quizmaster.elements.hintTrigger.addClass('qm-hint-disabled')
 			quizmaster.elements.hintTrigger.off( 'click', quizmaster.hintHide )
 			quizmaster.elements.hintTrigger.off( 'click', quizmaster.hintShow )

 		};

 		quizmaster.hintEnable = function () {

 			quizmaster.elements.hintTrigger.removeClass('qm-hint-disabled')
 			quizmaster.elements.hintTrigger.addClass('qm-hint-enabled')
 			quizmaster.elements.hintTrigger.off( 'click', quizmaster.hintHide )
 			quizmaster.elements.hintTrigger.on( 'click', quizmaster.hintShow )

 		};

 		quizmaster.hintHide = function ( event ) {

 			$tipModal = $('.qm-hint-modal');
 			$tipModal.hide();
 			quizmaster.elements.hintTrigger.off( 'click', quizmaster.hintHide )
 			quizmaster.elements.hintTrigger.on( 'click', quizmaster.hintShow )

 		};

 		quizmaster.hintShow = function ( event ) {

 			var $this = $(this);
 			var id = quizmaster.getCurrentQuestionId();

 			// get tip div
 			var $hint = quizmaster.data.currentQuestion.find('.quizMaster_tipp')
 			var $tip = $hint.html();
 			$tipModal = $('.qm-hint-modal');
 			$tipModalContents = $('.qm-hint-modal .qm-hint-content');

 			// populate modal with current question tip
 			$tipModalContents.html( $tip )

 			// adjust modal position
 			$tipModal.css({
 				position: "absolute",
 				left: $this.position().left + "px",
 				top: ($this.position().top + $this.outerHeight()) + "px",
 				display: "block",
 			});

 			quizmaster.elements.hintTrigger.on( 'click', quizmaster.hintHide )
 			quizmaster.elements.hintTrigger.off( 'click', quizmaster.hintShow )

 			// record use of tip
 			quizmaster.data.results[id].tip = 1;

 		};

		/*
     * Timer Class
		 */
		quizmaster.timer = {

			questionStartTime: 0,
			quizStartTime: 0,

			limit: {

				intervalId: 0,

				stop: function () {
					if ( quizmaster.config.timeLimit ) {
						window.clearInterval( quizmaster.timer.limit.intervalId );
						quizmaster.elements.timeLimitBox.hide();
					}
				},

				start: function () {

					if (! quizmaster.config.timeLimit )
						return;

					var $timeText = quizmaster.elements.timeLimitBox.find('span').text( quizmaster.timer.parseTime( quizmaster.config.timeLimit ) );
					var $timeDiv = quizmaster.elements.timeLimitBox.find('.qm-progress-box');

					quizmaster.elements.timeLimitBox.show();

					var beforeTime = +new Date();

					quizmaster.timer.limit.intervalId = window.setInterval(function () {

						var diff = (+new Date() - beforeTime);
						var elapsedTime = (quizmaster.config.timeLimit) - diff;

						if (diff >= 500) {
							$timeText.text( quizmaster.timer.parseTime(Math.ceil(elapsedTime)) );
						}

						$timeDiv.css('width', (elapsedTime / quizmaster.config.timeLimit * 100) + '%');

						if (elapsedTime <= 0) {
							quizmaster.timer.limit.stop();
							quizmaster.finishQuiz( true );
						}

					});
				},

			},

			question: {

				start: function ( questionId ) {
					if ( quizmaster.data.currentQuestionId != 0 )
						quizmaster.stop();

					quizmaster.data.currentQuestionId = questionId;
					quizmaster.timer.questionStartTime = +new Date();

				},

				stop: function () {

					if ( quizmaster.getCurrentQuestionId() == 0 )
							return;

					quizmaster.data.results[ quizmaster.getCurrentQuestionId() ].time += Math.round((new Date() - quizmaster.timer.questionStartTime));

				},

			},

			quiz: {

				start: function () {

					quizmaster.timer.quizStartTime = +new Date();
					quizmaster.data.isQuizStarted = true;

				},

				stop: function () {

					if ( !quizmaster.data.isQuizStarted ) {
						return;
					}

					quizmaster.data.results['comp'].quizTime += new Date() - quizmaster.timer.quizStartTime;
					quizmaster.data.isQuizStarted = false;

				},

			},

			convertTimeLimitMs: function() {
				if( quizmaster.config.timeLimit ) {
					quizmaster.config.timeLimit = quizmaster.config.timeLimit * 1000;
				}
			},

			parseTime: function (ms) {

				x = ms / 1000
				seconds = parseInt( x % 60 )
				x /= 60
				minutes = parseInt( x % 60 )
				x /= 60
				hours = parseInt( x % 24 )



				seconds = (seconds > 9 ? '' : '0') + seconds;
				minutes = (minutes > 9 ? '' : '0') + minutes;
				hours = (hours > 9 ? '' : '0') + hours;

				return hours + ':' + minutes + ':' + seconds;
			},

		};

		quizmaster.setAverageResult = function (p, g) {
			var v = quizmaster.find('.quizMaster_resultValue:eq(' + (g ? 0 : 1) + ') > * ');
			v.eq(1).text(p + '%');
			v.eq(0).css('width', (240 * p / 100) + 'px');
		};

		quizmaster.setCategoryOverview = function () {

				quizmaster.data.results.comp.cats = {};

				quizmaster.find('.quizMaster_catOverview li').each(function () {

					var $this = $(this);
					var catId = $this.data('category_id');

					if (quizmaster.config.catPoints[catId] === undefined) {
							$this.hide();
							return true;
					}

					var r = Math.round(quizmaster.data.catResults[catId] / quizmaster.config.catPoints[catId] * 100 * 100) / 100;

					quizmaster.data.results.comp.cats[catId] = r;

					$this.find('.quizMaster_catPercent').text(r + '%');

					$this.show();
				});

		};

		quizmaster.sendCompletedQuiz = function () {

			quizmaster.fetchAllAnswerData( quizmaster.data.results );

			quizmaster.ajax({
				action: 'quizmaster_admin_ajax',
				func: 'completedQuiz',
				data: {
					quizId: quizmaster.config.quizId,
					results: quizmaster.data.results,
				}
			});

		};

		quizmaster.fetchAllAnswerData = function (resultData) {

				quizmaster.find('.quizMaster_question-list').each(function () {
						var $this = $(this);
						var questionId = $this.data('question_id');
						var type = $this.data('type');
						var data = {};

						if (type == 'single' || type == 'multiple') {
								$this.find('.qm-question-list-item').each(function () {
									data[$(this).data('pos')] = +$(this).find('.quizMaster_questionInput').is(':checked');
								});
						} else if (type == 'free_answer') {
								data[0] = $this.find('.quizMaster_questionInput').val();
						} else if (type == 'sort_answer') {
								return true;
						} else if (type == 'matrix_sort_answer') {
								return true;
						} else if (type == 'fill_blank') {
								var i = 0;
								$this.find('.quizMaster_cloze input').each(function () {
										data[i++] = $(this).val();
								});
						}

						quizmaster.data.resultData[questionId]['data'] = data;

				});
		};

		/*
     * Question Review
		 */
		quizmaster.questionReviewButtonInit = function() {

			quizmaster.elements.questionReviewButton.on( 'click', function () {
				quizmaster.showQuestionList();
			});

		};

		quizmaster.showQuestionList = function () {

				quizmaster.elements.quiz.toggle();
				quizmaster.find('.quizMaster_QuestionButton').hide();
				quizmaster.elements.questionList.children().show();

				if( quizmaster.config.showReviewBox ) {
					quizmaster.elements.reviewBox.toggle();
				}

				quizmaster.find('.quizMaster_question_page').hide();

		};

		/*
     * Restart quiz
		 */
		quizmaster.restartButtonInit = function() {

			quizmaster.elements.restartButton.click(function () {
					quizmaster.restartQuiz();
			});

		};

		quizmaster.restartQuiz = function () {

			// flag that the quiz has been restarted
			quizmaster.restarted = true;

			// reset current question
			var $questionList = quizmaster.elements.questionList.children();
			quizmaster.setCurrentQuestion( $questionList.eq(0) );

			quizmaster.elements.resultsBox.hide();
			quizmaster.elements.startPage.show();
			quizmaster.elements.questionList.children().hide();
			quizmaster.elements.reviewBox.hide();

			quizmaster.find('.quizMaster_questionInput, .quizMaster_cloze input').removeAttr('disabled').removeAttr('checked')
					.css('background-color', '');

			quizmaster.find('.quizMaster_questionListItem input[type="text"]').val('');

			quizmaster.find('.quizMaster_answerCorrect, .quizMaster_answerIncorrect').removeClass('quizMaster_answerCorrect quizMaster_answerIncorrect');

			quizmaster.find('.quizMaster_listItem').data('check', false);

			// quizmaster.find('.qm-check-answer-box').hide().children().hide();
			quizmaster.find('.qm-check-answer-box').hide();
			quizmaster.find('.quizMaster_clozeCorrect, .quizMaster_QuestionButton, .qm-results-boxList > li').hide();

			quizmaster.find('.quizMaster_question_page, input[name="tip"]').show();
			quizmaster.find('.quizMaster_resultForm').text('').hide();

			quizmaster.elements.resultsBox.find('.qm-time-limit_expired').hide();

			// reset finish tracker
			quizmaster.finish = false;

			// set status
			quizmaster.setStatus('restart')

		};

		/*
     * Important utility functions
		 */

		quizmaster.loadQuizData = function () {

			quizmaster.ajax({
					action: 'quizmaster_admin_ajax',
					func: 'quizLoadData',
					data: {
						quizId: quizmaster.config.quizId
					}
			}, function (json) {

				quizmaster.config.globalPoints = json.globalPoints;
				quizmaster.config.catPoints = json.catPoints;
				quizmaster.config.json = json.json;
				quizmaster.find('.quizMaster_quizAnker').after(json.content);

				// quiz data loaded event
				quizmaster.trigger({
					type: 'quizmaster.quizDataLoaded',
				});

			});
		};

		quizmaster.modeHandler = function( e ) {

			var restart = e.restart;

			// mode handling
			switch (quizmaster.config.mode) {

				// single page mode
				case 2:

					quizmaster.elements.finishButton.show();
					quizmaster.find('.quizMaster_question_page').hide();
					var $questionList = quizmaster.elements.questionList.children();
					quizmaster.setCurrentQuestion( $questionList.last() );
					quizmaster.showSinglePage(0);
					quizmaster.finishButtonInitFinishQuiz();
					quizmaster.nextButtonInit();

					break;

				// check/continue mode
				case 1:

					// show check button at start
					quizmaster.elements.checkButton.show();
					quizmaster.elements.finishButton.hide();
					quizmaster.elements.nextButton.hide();

					// handle buttons on questionCheck
					if( !restart ) {

						quizmaster.on( 'quizmaster.questionChecked', function() {

							if( quizmaster.isLastQuestion() ) {
								quizmaster.elements.finishButton.show()
								quizmaster.elements.checkButton.hide()
							} else {
								quizmaster.elements.nextButton.show()
								quizmaster.elements.checkButton.hide()
							}

						});

						// handle buttons on nextQuestion
						quizmaster.on( 'quizmaster.nextQuestion', function() {

							quizmaster.elements.checkButton.show()
							quizmaster.elements.nextButton.hide()

							quizmaster.checkMessageBoxHide()

						});

						quizmaster.finishButtonInitFinishQuiz();
						quizmaster.nextButtonInitCheckContinueMode();

					}

					// answer check completed
					quizmaster.on( 'quizmaster.answerCheckComplete', function( e ) {

						// get check results from event
						var $pointsEarned = e.pointsEarned;
						var $isCorrect = e.isCorrect;

						quizmaster.setCheckMessage( $isCorrect, $pointsEarned );

					});

					break;

				// default standard mode
				case 0:

					quizmaster.elements.nextButton.show();

					if( !restart ) {

						quizmaster.finishButtonInit();
						quizmaster.nextButtonInit();

						// answer check completed
						quizmaster.on( 'quizmaster.answerCheckComplete', function() {

							if( quizmaster.isLastQuestion() ) {
								quizmaster.finishQuiz()
							} else {
								quizmaster.nextQuestion();
							}

						});

					}

					break;
			}

			// maybe hide question position overview
			if ( quizmaster.config.options.hideQuestionPositionOverview ) {
				quizmaster.find('.quizMaster_question_page').hide();
			}

			// start timer
			quizmaster.timer.question.start( quizmaster.getCurrentQuestionId() )

		};

		quizmaster.startQuizShowQuestion = function() {

			if( quizmaster.config.mode != 2 ) {

				// get first question object and show
				var $questionList = quizmaster.elements.questionList.children();
				quizmaster.setCurrentQuestion( $questionList.eq(0) );
				quizmaster.showQuestionObject( 'current' );

			}

		};

		quizmaster.sortableInit = function () {

			quizmaster.find('.qm-sortable').sortable({
				update: function (event, ui) {
					var $p = $(this).parents('.quizMaster_listItem');

					quizmaster.trigger({
							type: 'quizmaster.questionSolved',
							values: {
								item: $p,
								index: $p.index(),
								solved: true
							}
					});
				}
			}).disableSelection();

		}

		quizmaster.setStatus = function ( statusCode ) {
			quizmaster.status = statusCode;

			// status change event
			quizmaster.trigger({
				type: 'quizmaster.statusChange',
				status: statusCode,
			});
		}

		quizmaster.marker = function ( $question, $isCorrect ) {

			$questionInput = quizmaster.getQuestionInput( $question );
			$questionInput.each( function( index ) {

				$answerChoice = $( this );
				var checked =  $questionInput.eq(index).is(':checked');

				if( checked ) {
					// mark input label
					if( $isCorrect ) {
						$answerChoice.parent().addClass('quizMaster_answerCorrect')
					} else {
						$answerChoice.parent('label').addClass('quizMaster_answerIncorrect')
					}
				}

			})

			// mark entire question correct/incorrect
			if( $isCorrect ) {
				//$question.addClass('quizMaster_answerCorrect')
			} else {
				// $question.addClass('quizMaster_answerIncorrect')
			}

		}

		quizmaster.getStatus = function () {
			return quizmaster.status;
		}

		quizmaster.init = function( options ) {

			// parse options to quizmaster.config
			quizmaster.config = $.extend({

				// default settings
	      bitOptions: {
					cors: true
				},
	      options: {
					catPoints: []
				}

	    }, options );

			// convert the time limit set in seconds to ms
			quizmaster.timer.convertTimeLimitMs();

			quizmaster.loadQuizData()
			quizmaster.checkButtonInit();
			quizmaster.backButtonInit();
			quizmaster.startButtonInit();
			quizmaster.restartButtonInit();
			quizmaster.questionReviewButtonInit();
			quizmaster.hintInit();
			quizmaster.sortableInit();

			/*
  		 * Maybe start quiz or show start page
			 */
			if( quizmaster.config.options.isAutostart ) {
				quizmaster.on( 'quizmaster.quizDataLoaded', quizmaster.startQuiz )
			} else {
				quizmaster.startPageShow();
			}

			// quiz setup functions
			quizmaster.on( 'quizmaster.startQuiz', quizmaster.modeHandler );
			quizmaster.on( 'quizmaster.startQuiz', quizmaster.startQuizShowQuestion );

			/*
   		 * Event Handlers
			 */

			quizmaster.on( 'quizmaster.questionAnswered', function() {
				quizmaster.getQuestionInput().attr('disabled', 'disabled')
			});

			// mark questions on answer check completion
			if ( !quizmaster.config.options.disabledAnswerMark ) {

				quizmaster.on( 'quizmaster.answerCheckComplete', function( e ) {

					$question = e.question;
					$isCorrect = e.isCorrect;
					quizmaster.marker( $question, $isCorrect );

				});

			}


			// stop timer on question_check status change
			quizmaster.on( 'quizmaster.statusChange', function( e ) {

				var status = e.status;

				if( status == 'check_question' || status == 'check_question_multiple' ) {
					quizmaster.timer.question.stop();
				}

			});


			// bind questionSolved to questionCheck
			quizmaster.on( 'quizmaster.questionChecked', quizmaster.questionSolved );

			// bind to quizCompleted event
			quizmaster.on( 'quizmaster.quizCompleted', function() {
				quizmaster.afterQuizFinish();
			});

			quizmaster.on( 'quizmaster.lastQuestionLoaded', function() {

				if( quizmaster.config.mode == 0 ) {
					quizmaster.elements.finishButton.show();
					quizmaster.elements.checkButton.hide();
					quizmaster.elements.nextButton.hide();
				}

			});

			// bind to questionAnswered event
			quizmaster.on( 'quizmaster.questionAnswered', function() {
				quizmaster.checkQuestion()
			});

			/*
 			 * Set initial status
			 */
			quizmaster.setStatus('initialized')

    };

		/*
     * Initialize or return
		 */

 		if( !options ) {
			// return current instance
 			return quizmaster;
 		} else {
			// do init
			quizmaster.init( options );
		}


  }; // end quizmaster jQuery plugin

});
