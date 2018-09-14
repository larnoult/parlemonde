jQuery(document).ready(function ($) {
    /**
     * @memberOf $.fn
     */
    $.fn.quizMaster_preview = function () {
        var methods = {
            openPreview: function (obj) {
                window.open($(obj).attr('href'), 'quizMasterPreview', 'width=900,height=900');
            }
        };

        var init = function () {
            $('.quizMaster_prview').click(function (e) {
                methods.openPreview(this);
                e.preventDefault();
            });
        };

        init();
    };

    $.fn.quizMaster_quizOverall = function () {

        //function isEmpty(text) {
        //	text = $.trim(text);
        //
        //	return (!text || 0 === text.length);
        //};
        //
        //function ajaxPost(func, data, success) {
        //	var d = {
        //		action: 'quizmaster_admin_ajax',
        //		func: func,
        //		data: data
        //	};
        //
        //	$.post(ajaxurl, d, success, 'json');
        //};
        //
        //var methods = {
        //	changeExport: function(input) {
        //		$input = $(input);
        //		$export = $('.quizMaster_exportList');
        //		$export2 = $('.quizMaster_setQuizCategoryList');
        //		$ul = $export.find('ul').first();
        //		$ul2 = $export2.find('ul').first();
        //		$export.find('li').remove();
        //		$export2.find('li').remove();
        //
        //		$('input[name="exportItems"]').each(function() {
        //			$this = $(this);
        //
        //			if(this.checked) {
        //				var text = $this.parent().parent().find('.quizMaster_quizName a:eq(0)').text();
        //				$('<li>' + text + '</li>').appendTo($ul);
        //				$('<li>' + text + '</li>').appendTo($ul2);
        //			}
        //		});
        //	},
        //
        //	startExport: function() {
        //		$ele = $('input[name="exportItems"]:checked');
        //
        //		if($ele.length < 1) {
        //			alert(quizMasterLocalize.no_selected_quiz);
        //			return false;
        //		}
        //
        //		$hidden = $('#exportHidden');
        //
        //		$hidden.html('');
        //
        //		$('input[name="exportItems"]').each(function() {
        //			$this = $(this);
        //
        //			if(this.checked) {
        //				$('<input type="hidden" value="'+ this.value +'" name="exportIds[]">').appendTo($hidden);
        //			}
        //		});
        //
        //		return true;
        //	},
        //
        //	setCategoriesStart: function() {
        //		$ele = $('input[name="exportItems"]:checked');
        //
        //		if($ele.length < 1) {
        //			alert(quizMasterLocalize.no_selected_quiz);
        //			return false;
        //		}
        //
        //		var ids = [];
        //
        //		$('input[name="exportItems"]').each(function() {
        //			$this = $(this);
        //
        //			if(this.checked) {
        //				ids.push(this.value);
        //			}
        //		});
        //
        //		var categoryId = $('select[name="category"]').val();
        //
        //		var data = {
        //			categoryId: categoryId,
        //			quizIds: ids
        //		};
        //
        //		$('#ajaxLoad').show();
        //
        //		ajaxPost('setQuizMultipleCategories', data, function(json) {
        //			location.reload();
        //		});
        //
        //		return true;
        //	},
        //
        //	addCategory: function() {
        //		var name = $.trim($('input[name="categoryAdd"]').val());
        //
        //		if(isEmpty(name)) {
        //			return;
        //		}
        //
        //		var data = {
        //			categoryName: name,
        //			type: 'quiz'
        //		};
        //
        //		ajaxPost('categoryAdd', data, function(json) {
        //			if(json.err) {
        //				$('#categoryMsgBox').text(json.err).show('fast').delay(2000).hide('fast');
        //				return;
        //			}
        //
        //			var $option = $(document.createElement('option'))
        //				.val(json.categoryId)
        //				.text(json.categoryName)
        //				.attr('selected', 'selected');
        //
        //			$('select[name="category"]').append($option).change();
        //
        //		});
        //	}
        //};
        //
        //var init = function() {
        //	$('.quizMaster_delete').click(function(e) {
        //		var b = confirm(quizMasterLocalize.delete_msg);
        //
        //		if(!b) {
        //			e.preventDefault();
        //			return false;
        //		}
        //
        //		return true;
        //	});
        //
        //	$('.quizMaster_import').click(function(e) {
        //		e.preventDefault();
        //		$('.quizMaster_importList').show('fast');
        //
        //		$('.quizMaster_exportList, .quizMaster_setQuizCategoryList').hide();
        //		$('.quizMaster_exportCheck').hide();
        //
        //	});
        //
        //	$('.quizMaster_export').click(function(e) {
        //		e.preventDefault();
        //
        //		$('.quizMaster_exportList').show('fast');
        //		$('.quizMaster_exportCheck').show('fast');
        //		$('.quizMaster_importList, .quizMaster_setQuizCategoryList').hide();
        //	});
        //
        //	$('.quizMaster_setQuizCategory').click(function(e) {
        //		e.preventDefault();
        //
        //		$('.quizMaster_setQuizCategoryList').show('fast');
        //		$('.quizMaster_exportCheck').show('fast');
        //		$('.quizMaster_importList, .quizMaster_exportList').hide();
        //	});
        //
        //	$('input[name="exportItems"]').change(function() {
        //		methods.changeExport(this);
        //	});
        //
        //	$('input[name="exportItemsAll"]').change(function() {
        //		var $input = $('input[name="exportItems"]');
        //		if(this.checked)
        //			$input.attr('checked', true);
        //		else
        //			$input.attr('checked', false);
        //
        //		$input.change();
        //	});
        //
        //	$('#exportStart').click(function(e) {
        //
        //		if(!methods.startExport())
        //			e.preventDefault();
        //	});
        //
        //	$('#setCategoriesStart').click(function(e) {
        //
        //		if(!methods.setCategoriesStart())
        //			e.preventDefault();
        //	});
        //
        //	$('select[name="category"]').change(function() {
        //		var $this = $(this);
        //		var box = $('#categoryAddBox').hide();
        //
        //
        //		if($this.val() == "-1") {
        //			box.show();
        //		}
        //
        //	}).change();
        //
        //	$('#categoryAddBtn').click(function() {
        //		methods.addCategory();
        //	});
        //};
        //
        //init();
    };

//	$.fn.quizMaster_questionEdit = function() {
//
//		var setup = function() {
//			if($('input[name="answerType"][checked="checked"]').size() < 1) {
//				$('input[name="answerType"][value="single"]').attr({'checked': 'checked'});
//			}
//
//			$('input[name="answerType"]:checked').click();
//			$('#quizMaster_correctSameText').change();
//			$('#quizMaster_tip').change();
//			$('input[name="pointsPerAnswer"]').change();
//			$('input[name="answerPointsActivated"]').change();
//		};
//
//		var formListener = {
//			setup: function() {
//				$('input[name="answerType"]').click(function(e) {
//					$('.answer_felder').children().css('display', 'none');
//
//					switch (this.value) {
//					case 'single':
//						formListener.displaySingle('radio');
//						break;
//					case 'multiple':
//						formListener.displaySingle('checkbox');
//						break;
//					case 'free_answer':
//						formListener.displayFreeAnswer();
//						break;
//					case 'sort_answer':
//						formListener.displaySortAnswer();
//						break;
//					case 'matrix_sort_answer':
//						formListener.displayMatrixSortAnswer();
//						break;
//					case 'fill_blank':
//						formListener.displayClozeAnswer();
//						break;
//					}
//				});
//
//				$('.addAnswer').click(function(e) {
//					formListener.addAnswer(this);
//				});
//
//				$('.deleteAnswer').click(function(e) {
//					formListener.deleteAnswer(this);
//				});
//
//				$('#saveQuestion').click(function(e) {
//					return validate();
//				});
//
//				$('.sort_answer ul, .classic_answer ul, .matrix_sort_answer ul').sortable({
//					handle: '.quizMaster_move',
//					update: function(event, ui) {
//						formListener.setValueClassicAnswer();
//					}
//				});
//
//				$('#quizMaster_correctSameText').change(function() {
//					if(this.checked)
//						$('#quizMaster_incorrectMassageBox').hide();
//					else
//						$('#quizMaster_incorrectMassageBox').show();
//				});
//
//				$('#quizMaster_tip').change(function(e) {
//					if(this.checked)
//						$('#quizMaster_tipBox').show();
//					else
//						$('#quizMaster_tipBox').hide();
//				});
//
//				$('input[name="pointsPerAnswer"]').change(function() {
//					if(this.checked) {
//						$('#quizMaster_showPointsBox').show();
//					} else {
//						$('#quizMaster_showPointsBox').hide();
//					}
//				});
//
//				$('input[name="answerPointsActivated"]').change(function() {
//					if(this.checked) {
//						$('input[name="points"]').attr('disabled', 'disabled');
//						$('.quizMaster_answerPoints').show();
//						$('#quizMaster_showPointsBox').show();
//					} else {
//						$('input[name="points"]').removeAttr('disabled');
//						$('.quizMaster_answerPoints').hide();
//						$('#quizMaster_showPointsBox').hide();
//					}
//				});
//
//				$('.quizMaster_demoBox a').mouseover(function() {
//					$(this).next().show();
//				}).mouseout(function() {
//					$(this).next().hide();
//				}).click(function() {
//					return false;
//				});
//
//			},
//
//			displaySingle: function(type) {
//				$('.classic_answer').find('input[name="answerJson[classic_answer][correct][]"]').each(function() {
//					 $("<input type=" + type + " />").attr({ name: this.name, value: this.value, checked: this.checked}).insertBefore(this);
//				}).remove();
//
//				$('.classic_answer').css('display', 'block');
//			},
//
//			displayFreeAnswer: function() {
//				$('.free_answer').css('display', 'block');
//			},
//
//			displaySortAnswer: function() {
//				$('.sort_answer').css('display', 'block');
//			},
//
//			displayMatrixSortAnswer: function() {
//				$('.matrix_sort_answer').show();
//			},
//
//			displayClozeAnswer: function() {
//				$('.fill_blank').show();
//			},
//
//			addAnswer: function(obj) {
//				$(obj).siblings('ul').children().first()
//						.clone().css('display', 'block')
//						.appendTo($(obj).siblings('ul'));
//
//				formListener.setValueClassicAnswer();
//
//				$('.deleteAnswer').click(function(e) {
//					formListener.deleteAnswer(this);
//				});
//			},
//
//			deleteAnswer: function(obj) {
//				$(obj).parent().parent('li').remove();
//
//				formListener.setValueClassicAnswer();
//			},
//
//			setValueClassicAnswer: function() {
//
//				$('.classic_answer ul, .matrix_sort_answer ul, .sort_answer ul').children().each(function() {
//					var index = $(this).index();
//
//					$(this).find( 'input[name="answerJson[classic_answer][correct][]"], '
//								+ 'input[name="answerJson[classic_answer][html][]"], '
//								+ 'input[name="answerJson[answer_matrix_sort][answer_html][]"], '
//								+ 'input[name="answerJson[answer_matrix_sort][sort_string_html][]"], '
//								+ 'input[name="answerJson[answer_sort][html][]"]').val(index);
//				});
//			}
//		};
//
//		var validate = function () {
//
//			var question = '';
//			var type = $('input[name="answerType"]:checked');
//			var $points = $('input[name="points"]');
//
//			if(tinymce.editors.question != undefined && !tinymce.editors.question.isHidden()) {
//				question = tinymce.editors.question.getContent();
//			} else {
//				question = $('textarea[name="question"]').val();
//			}
//
//			if(isNaN($points.val()) || $points.val() < 1) {
//				alert(quizMasterLocalize.no_nummber_points);
//				$points.focus();
//				return false;
//			}
//
//			if(isEmpty(question)) {
//				alert(quizMasterLocalize.no_question_msg);
//				return false;
//			}
//
//
//			if(type.val() == 'single' || type.val() == 'multiple') {
//				var findChecked = true;
//				var findPoints = true;
//				if($('input[name="answerJson[classic_answer][correct][]"]:checked').each(function() {
//					if($.trim($(this).parent().parent().parent().parent().find('textarea').val()) != '') {
//						findChecked &= true;
//					} else {
//						findChecked = false;
//					}
//				})
//				.size() < 1) {
//					alert(quizMasterLocalize.no_correct_msg);
//					return false;
//				}
//
//				if($('input[name="answerPointsActivated"]:checked').length) {
//					$('input[name="answerJson[classic_answer][points][]"]').each(function() {
//						if($.trim($(this).parentsUntil('table').find('textarea').val()) != '') {
//							var points = $.trim($(this).val());
//
//							if(isNaN(points) || points == '' || points < 0) {
//								findPoints = false;
//							} else {
//								findPoints &= true;
//							}
//						}
//					});
//
//					if(!findPoints) {
//						alert(quizMasterLocalize.no_nummber_points_new);
//						return false;
//					}
//				}
//
//				if(!findChecked) {
//					alert(quizMasterLocalize.no_answer_msg);
//					return false;
//				}
//			} else if(type.val() == 'sort_answer') {
//				var findChecked = false;
//				var findPoints = true;
//
//				$('textarea[name="answerJson[answer_sort][answer][]"]').each(function() {
//					if(isEmpty($(this).val())) {
//						findChecked |= false;
//					} else {
//						findChecked = true;
//					}
//				});
//
//				if($('input[name="answerPointsActivated"]:checked').length) {
//					$('input[name="answerJson[answer_sort][points][]"]').each(function() {
//						if($.trim($(this).parentsUntil('table').find('textarea').val()) != '') {
//							var points = $.trim($(this).val());
//
//							if(isNaN(points) || points == '' || points < 0) {
//								findPoints = false;
//							} else {
//								findPoints &= true;
//							}
//						}
//					});
//
//					if(!findPoints) {
//						alert(quizMasterLocalize.no_nummber_points_new);
//						return false;
//					}
//				}
//
//				if(!findChecked) {
//					alert(quizMasterLocalize.no_answer_msg);
//					return false;
//				}
//			} else if(type.val() == 'matrix_sort_answer') {
//				var findChecked = false;
//				var findPoints = true;
//				$('textarea[name="answerJson[answer_matrix_sort][answer][]"]').each(function() {
//					if(isEmpty($(this).val())) {
//						findChecked |= false;
//					} else {
//
//						var $sortString = $(this).parent().parent().find('textarea[name="answerJson[answer_matrix_sort][sort_string][]"]');
//
//						if(isEmpty($sortString.val())) {
//							findChecked |= false;
//						} else {
//							findChecked = true;
//						}
//					}
//				});
//
//				if($('input[name="answerPointsActivated"]:checked').length) {
//					$('input[name="answerJson[answer_matrix_sort][points][]"]').each(function() {
//						if($.trim($(this).parentsUntil('table').find('textarea').val()) != '') {
//							var points = $.trim($(this).val());
//
//							if(isNaN(points) || points == '' || points < 0) {
//								findPoints = false;
//							} else {
//								findPoints &= true;
//							}
//						}
//					});
//
//					if(!findPoints) {
//						alert(quizMasterLocalize.no_nummber_points_new);
//						return false;
//					}
//				}
//
//				if(!findChecked) {
//					alert(quizMasterLocalize.no_answer_msg);
//					return false;
//				}
//			} else if(type.val() == 'fill_blank') {
//				var clozeText = '';
//
//				if(tinymce.editors.cloze != undefined && !tinymce.editors.cloze.isHidden()) {
//					clozeText = tinymce.editors.cloze.getContent();
//				} else {
//					clozeText = $('textarea[name="answerJson[answer_cloze][text]"]').val();
//				}
//
//				if(isEmpty(clozeText)) {
//					alert(quizMasterLocalize.no_answer_msg);
//					return false;
//				}
//			} else if(type.val() == 'free_answer') {
//				var freeText = $('textarea[name="answerJson[free_answer][correct]"]').val();
//
//				if(isEmpty(freeText)) {
//					alert(quizMasterLocalize.no_answer_msg);
//					return false;
//				}
//			}
//
//			return true;
//		};
//
//		var isEmpty = function(str) {
//			str = $.trim(str);
//			return (!str || 0 === str.length);
//		};
//
//		formListener.setup();
//		setup();
//	};

    //$.fn.quizMaster_questionOverall = function() {
    //
    //	var methode = {
    //		saveSort: function() {
    //
    //			var data = {
    //				action: 'quizmaster_update_sort',
    //				sort: methode.parseSortArray()
    //			};
    //
    //			var location = window.location.pathname + window.location.search;
    //			var url = location.replace('admin.php', 'admin-ajax.php') + '&action=save_sort';
    //
    //			$.post(url, data, function(response) {
    //				$('#sortMsg').show(400).delay(1000).hide(400);
    //			});
    //		},
    //
    //		parseSortArray: function() {
    //			var array = new Array();
    //
    //			$('tbody tr').each(function() {
    //				array.push(this.id.replace('quizMaster_questionId_', ''));
    //			});
    //
    //			return array;
    //		},
    //
    //		sortUpdate: function(e, ui) {
    //			$('.quizMaster_questionOverall tbody').children().each(function() {
    //				$t = $(this).children().first().text($(this).index() + 1);
    //			});
    //		},
    //
    //		loadQuestionCopy: function() {
    //			var list = $('#questionCopySelect');
    //			var location = window.location.pathname + window.location.search;
    //			var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_question';
    //			var data = {
    //				action: 'quizmaster_load_question',
    //				excludeId: 1
    //			};
    //
    //			list.hide();
    //			list.empty();
    //
    //			$('#loadDataImg').show();
    //
    //			$.post(
    //				url,
    //				data,
    //				function(json) {
    //					$.each(json, function(i, v) {
    //
    //						var group = $(document.createElement('optgroup'));
    //
    //						group.attr('label', v.name);
    //
    //						$.each(v.question, function(qi, qv) {
    //							$(document.createElement('option'))
    //								.val(qv.id)
    //								.text(qv.name)
    //								.appendTo(group);
    //
    //
    //						});
    //
    //						list.append(group);
    //
    //					});
    //
    //					$('#loadDataImg').hide();
    //					list.show();
    //				},
    //				'json'
    //			);
    //		}
    //	};
    //
    //	var init = function() {
    //		$('.wp-list-table tbody').sortable({ handle: '.quizMaster_move', update: methode.sortUpdate });
    //
    //		$('.quizMaster_delete').click(function(e) {
    //			var b = confirm(quizMasterLocalize.delete_msg);
    //
    //			if(!b) {
    //				e.preventDefault();
    //				return false;
    //			}
    //
    //			return true;
    //		});
    //
    //		$('#quizMaster_saveSort').click(function(e) {
    //			e.preventDefault();
    //			methode.saveSort();
    //		});
    //
    //		$('#quizMaster_questionCopy').click(function(e) {
    //			var $this = $('.quizMaster_questionCopy');
    //
    //			if($this.is(':visible')) {
    //				$this.hide();
    //			} else {
    //				$this.show();
    //				methode.loadQuestionCopy();
    //			}
    //
    //			e.preventDefault();
    //		});
    //	};
    //
    //	init();
    //};

    $.fn.quizMaster_quizEdit = function () {

        function ajaxPost(func, data, success) {
            var d = {
                action: 'quizmaster_admin_ajax',
                func: func,
                data: data
            };

            $.post(ajaxurl, d, success, 'json');
        };

        var methode = {
            addCategory: function () {
                var name = $.trim($('input[name="categoryAdd"]').val());

                if (isEmpty(name)) {
                    return;
                }

                var data = {
                    categoryName: name,
                    type: 'quiz'
                };

                ajaxPost('categoryAdd', data, function (json) {
                    if (json.err) {
                        $('#categoryMsgBox').text(json.err).show('fast').delay(2000).hide('fast');
                        return;
                    }

                    var $option = $(document.createElement('option'))
                        .val(json.categoryId)
                        .text(json.categoryName)
                        .attr('selected', 'selected');

                    $('select[name="category"]').append($option).change();

                });
            },

            addResult: function () {
                $('#resultList').children().each(function () {
                    if ($(this).css('display') == 'none') {
                        //TODO rework
                        var $this = $(this);
                        var $text = $this.find('textarea[name="resultTextGrade[text][]"]');
                        var id = $text.attr('id');
                        var hidden = true;

                        $this.find('input[name="resultTextGrade[prozent][]"]').val('0');
                        $this.find('input[name="resultTextGrade[activ][]"]').val('1').keyup();

                        if (tinymce.editors[id] != undefined && !tinymce.editors[id].isHidden()) {
                            hidden = false;
                        }

                        if (switchEditors != undefined && !hidden) {
                            switchEditors.go(id, 'toggle');
                            switchEditors.go(id, 'toggle');
                        }

                        if (tinymce.editors[id] != undefined) {
                            tinymce.editors[id].setContent('');
                        } else {
                            $text.val('');
                        }

                        if (tinymce.editors[id] != undefined && !hidden) {
                            tinyMCE.execCommand('mceRemoveControl', false, id);
                        }

                        $this.parent().children(':visible').last().after($this);

                        if (tinymce.editors[id] != undefined && !hidden) {
                            tinyMCE.execCommand('mceAddControl', false, id);
                        }

                        $(this).show();

                        if (switchEditors != undefined && !hidden) {
                            switchEditors.go(id, 'toggle');
                        }

                        return false;
                    }
                });
            },

            deleteResult: function (e) {
                $(e).parent().parent().hide();
                $(e).siblings('input[name="resultTextGrade[activ][]"]').val('0');
            },

            changeResult: function (e) {
                var $this = $(e);

                if (methode.validResultInput($this.val())) {
                    $this.siblings('.resultProzent').text($this.val());
                    $this.removeAttr('style');
                    return true;
                }

                $this.css('background-color', '#FF9696');

                return false;
            },

            validResultInput: function (input) {

                if (isEmpty(input))
                    return false;

                input = input.replace(/\,/, '.');

                if (!isNaN(input) && Number(input) <= 100 && Number(input) >= 0) {
                    if (input.match(/\./) != null)
                        return input.split('.')[1].length < 3;

                    return true;
                }

                return false;
            },

            validInput: function () {
                if (isEmpty($('#quizMaster_title').val())) {
                    alert(quizMasterLocalize.no_title_msg);
                    return false;
                }

                var text = '';

                if (tinymce.editors.text != undefined && !tinymce.editors.text.isHidden()) {
                    text = tinymce.editors.text.getContent();
                } else {
                    text = $('textarea[name="text"]').val();
                }

                if (isEmpty(text)) {
                    alert(quizMasterLocalize.no_quiz_start_msg);
                    return false;
                }

                if ($('#quizMaster_resultGradeEnabled:checked').length) {
                    var rCheck = true;

                    $('#resultList').children().each(function () {
                        if ($(this).is(':visible')) {
                            if (!methode.validResultInput($(this).find('input[name="resultTextGrade[prozent][]"]').val())) {
                                rCheck = false;
                                return false;
                            }
                        }
                    });

                    if (!rCheck) {
                        alert(quizMasterLocalize.fail_grade_result);
                        return false;
                    }
                }

                return true;
            },

            resetLock: function () {
                //var location = window.location.pathname + window.location.search;
                //var url = location.replace('admin.php', 'admin-ajax.php');
                //url = url.replace('action=edit', 'action=reset_lock');
                //
                //$.post(url, {
                //    action: 'quizmaster_reset_lock'
                //}, function (data) {
                //    $('#resetLockMsg').show('fast').delay(2000).hide('fast');
                //});

                ajaxPost('resetLock', {
                    quizId: $('input[name="ajax_quiz_id"]').val()
                }, function () {
                    $('#resetLockMsg').show('fast').delay(2000).hide('fast');
                });
            },

            generateFormIds: function () {
                var index = 0;

                $('#form_table tbody > tr').each(function () {
                    $(this).find('[name^="form[]"]').each(function () {
                        var newname = $(this).attr('name').substr(6);
                        $(this).attr('name', 'form[' + index + ']' + newname);
                    });

                    ++index;
                });
            },

            updateFormIds: function () {
                var index = -1;
                var selected = $('.emailFormVariables option:selected').val();
                var $formVariables = $('.formVariables').empty();
                var $emailFormVariables = $('.emailFormVariables').empty().append('<option value="-1"></option>');

                if ($('.emailFormVariables').data('default') > -1) {
                    selected = $('.emailFormVariables').data('default');
                    $('.emailFormVariables').data('default', -1);
                }

                $('#form_table tbody > tr').each(function () {
                    $(this).children().first().text(index);
                    var fieldName = $(this).find('.formFieldName').val();
                    var type = $(this).find('[name="form[][type]"] option:selected');
                    var name = $(this).find('[name="form[][fieldname]"]').val();

                    //is deleted?
                    if ($(this).find('input[name="form[][form_delete]"]').val() == 1)
                        return;

                    if (index >= 0 && !isEmpty(fieldName))
                        $formVariables.append($('<li><span>$form{' + index + '}</span> - ' + fieldName + '</li>'));

                    if (type.val() == 4)
                        $emailFormVariables.append($('<option value="' + index + '">' + name + '</option>'))

                    index++;
                });

                $('.emailFormVariables option[value="' + selected + '"]').prop('selected', true);
            }

        };

        var isEmpty = function (str) {
            str = $.trim(str);
            return (!str || 0 === str.length);
        };

        var init = function () {
            $('#statistics_on').change(function () {
                if (this.checked) {
                    $('#statistics_ip_lock_tr').show();
                } else {
                    $('#statistics_ip_lock_tr').hide();
                }
            });

            $('.addResult').click(function () {
                methode.addResult();
            });

            $('.deleteResult').click(function (e) {
                methode.deleteResult(this);
            });

            $('input[name="resultTextGrade[prozent][]"]').keyup(function (event) {
                methode.changeResult(this);
            }).keydown(function (event) {
                if (event.which == 13) {
                    event.preventDefault();
                }
            });

            $('#quizMaster_resultGradeEnabled').change(function () {
                if (this.checked) {
                    $('#resultGrade').show();
                    $('#resultNormal').hide();
                } else {
                    $('#resultGrade').hide();
                    $('#resultNormal').show();
                }
            });

            $('#quizMaster_save').click(function (e) {
                if (!methode.validInput())
                    e.preventDefault();
                else
                    methode.generateFormIds();

                $('select[name="prerequisiteList[]"] option').attr('selected', 'selected');
            });

            $('input[name="template"]').click(function (e) {
                if ($('select[name="templateSaveList"]').val() == '0') {
                    if (isEmpty($('input[name="templateName"]').val())) {
                        alert(quizMasterLocalize.temploate_no_name);

                        e.preventDefault();
                        return false;
                    }
                }

                methode.generateFormIds();
                $('select[name="prerequisiteList[]"] option').attr('selected', 'selected');
            });

            $('select[name="templateSaveList"]').change(function () {
                var $templateName = $('input[name="templateName"]');

                if ($(this).val() == '0') {
                    $templateName.show();
                } else {
                    $templateName.hide();
                }
            }).change();

            $('input[name="quizRunOnce"]').change(function (e) {
                if (this.checked) {
                    $('#quizMaster_quiz_run_once_type').show();
                    $('input[name="quizRunOnceType"]:checked').change();
                } else {
                    $('#quizMaster_quiz_run_once_type').hide();
                }
            });

            $('input[name="quizRunOnceType"]').change(function (e) {
                if (this.checked && (this.value == "1" || this.value == "3")) {
                    $('#quizMaster_quiz_run_once_cookie').show();
                } else {
                    $('#quizMaster_quiz_run_once_cookie').hide();
                }
            });

            $('input[name="resetQuizLock"]').click(function (e) {
                methode.resetLock();

                return false;
            });

            $('.quizMaster_demoBox a').mouseover(function (e) {
                var $this = $(this);
                var d = $('#poststuff').width();
                var img = $this.siblings().outerWidth(true);

                if (e.pageX + img > d) {
                    //var v = d + (e.pageX - (e.pageX + img + 30));
                    var v = jQuery(document).width() - $this.parent().offset().left - img - 30;
                    $(this).next().css('left', v + "px");
                }

                $(this).next().show();

            }).mouseout(function () {
                $(this).next().hide();
            }).click(function () {
                return false;
            });

            $('#btnPrerequisiteAdd').click(function () {
                $('select[name="quizList"] option:selected').removeAttr('selected').appendTo('select[name="prerequisiteList[]"]');
            });

            $('#btnPrerequisiteDelete').click(function () {
                $('select[name="prerequisiteList[]"] option:selected').removeAttr('selected').appendTo('select[name="quizList"]');
            });

            $('input[name="prerequisite"]').change(function () {
                if (this.checked)
                    $('#prerequisiteBox').show();
                else
                    $('#prerequisiteBox').hide();

            }).change();

            $('input[name="toplistDataAddMultiple"]').change(function () {
                if (this.checked)
                    $('#toplistDataAddBlockBox').show();
                else
                    $('#toplistDataAddBlockBox').hide();

            }).change();

            $('input[name="toplistActivated"]').change(function () {
                if (this.checked)
                    $('#toplistBox > tr:gt(0)').show();
                else
                    $('#toplistBox > tr:gt(0)').hide();

            }).change();

            $('input[name="showReviewQuestion"]').change(function () {
                if (this.checked) {
                    $('.quizMaster_reviewQuestionOptions').show();
                } else {
                    $('.quizMaster_reviewQuestionOptions').hide();
                }
            }).change();

            $('#statistics_on').change();
            $('#quizMaster_resultGradeEnabled').change();
            $('input[name="quizRunOnce"]').change();
            $('input[name="quizRunOnceType"]:checked').change();

            $('#form_add').click(function () {
                $('#form_table tbody > tr:eq(0)').clone(true).appendTo('#form_table tbody').show();
                methode.updateFormIds();
            });

            $('input[name="form_delete"]').click(function () {
                var con = $(this).parents('tr');

                if (con.find('input[name="form[][form_id]"]').val() != "0") {
                    con.find('input[name="form[][form_delete]"]').val(1);
                    con.hide();
                } else {
                    con.remove();
                }

                methode.updateFormIds();
            });

            $('#form_table tbody').sortable({
                handle: '.form_move',
                update: methode.updateFormIds
            });
            $('.form_move').click(function () {
                return false;
            });

            $('select[name="form[][type]"]').change(function () {
                switch (Number($(this).val())) {
                    case 7:
                    case 8:
                        $(this).siblings('.editDropDown').show();
                        break;
                    default:
                        $(this).siblings('.editDropDown, .dropDownEditBox').hide();
                        break;
                }

            }).change();

            $('.editDropDown').click(function () {
                $('.dropDownEditBox').not(
                    $(this).siblings('.dropDownEditBox').toggle())
                    .hide();

                return false;
            });

            $('.dropDownEditBox input').click(function () {
                $(this).parent().hide();
            });

            $('.formFieldName, select[name="form[][type]"]').change(function () {
                methode.updateFormIds();
            });

            $('select[name="category"]').change(function () {
                var $this = $(this);
                var box = $('#categoryAddBox').hide();

                if ($this.val() == "-1") {
                    box.show();
                }

            }).change();

            $('#categoryAddBtn').click(function () {
                methode.addCategory();
            });

            $('input[name="emailNotification"]').change(function () {
                var $tr = $('#adminEmailSettings tr:gt(0)');

                if ($('input[name="emailNotification"]:checked').val() > 0) {
                    $tr.show();
                } else {
                    $tr.hide();
                }
            }).change();

            $('input[name="userEmailNotification"]').change(function () {
                var $tr = $('#userEmailSettings tr:gt(0)');

                if ($('input[name="userEmailNotification"]:checked').val() > 0) {
                    $tr.show();
                } else {
                    $tr.hide();
                }
            }).change();

            methode.updateFormIds();

            $('input[name="email[html]"]').change(function () {
                if (switchEditors == undefined)
                    return false;

                if (this.checked) {
                    switchEditors.go('adminEmailEditor', 'tmce');
                } else {
                    switchEditors.go('adminEmailEditor', 'html');
                }

            });

            $('input[name="adminEmail[html]"]').change(function () {
                if (switchEditors == undefined)
                    return false;

                if (this.checked) {
                    switchEditors.go('adminEmailEditor', 'tmce');
                } else {
                    switchEditors.go('adminEmailEditor', 'html');
                }

            });

            $('input[name="userEmail[html]"]').change(function () {
                if (switchEditors == undefined)
                    return false;

                if (this.checked) {
                    switchEditors.go('userEmailEditor', 'tmce');
                } else {
                    switchEditors.go('userEmailEditor', 'html');
                }

            });

            setTimeout(function () {
                $('input[name="userEmail[html]"]').change();
                $('input[name="email[html]"]').change();
            }, 1000);
        };

        init();
    };

    $.fn.quizMaster_statistics = function () {
        var currectTab = 'quizMaster_typeAnonymeUser';
        var changePageNav = true;

        var methode = {
            loadStatistics: function (userId) {
                var location = window.location.pathname + window.location.search;
                var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_statistics';
                var data = {
                    action: 'quizmaster_load_statistics',
                    userId: userId
                };

                $('#quizMaster_loadData').show();
                $('#quizMaster_statistics_content, #quizMaster_statistics_overview').hide();

                $.post(
                    url,
                    data,
                    methode.setStatistics,
                    'json'
                );
            },

            setStatistics: function (json) {
                var $table = $('.quizMaster_statistics_table');
                var $tbody = $table.find('tbody');

                if (currectTab == 'quizMaster_typeOverview') {
                    return;
                }

                var setItem = function (i, j, r) {
                    i.find('.quizMaster_cCorrect').text(j.cCorrect + ' (' + j.pCorrect + '%)');
                    i.find('.quizMaster_cIncorrect').text(j.cIncorrect + ' (' + j.pIncorrect + '%)');
                    i.find('.quizMaster_cTip').text(j.cTip);
                    i.find('.quizMaster_cPoints').text(j.cPoints);

                    if (r == true) {
                        $table.find('.quizMaster_cResult').text(j.result + '%');
                    }
                };

                setItem($table, json.clear, false);

                $.each(json.items, function (i, v) {
                    setItem($tbody.find('#quizMaster_tr_' + v.id), v, false);
                });

                setItem($table.find('tfoot'), json.global, true);

                $('#quizMaster_loadData').hide();
                $('#quizMaster_statistics_content, .quizMaster_statistics_table').show();
            },

            loadOverview: function () {
                $('.quizMaster_statistics_table, #quizMaster_statistics_content, #quizMaster_statistics_overview').hide();
                $('#quizMaster_loadData').show();

                var location = window.location.pathname + window.location.search;
                var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_statistics';
                var data = {
                    action: 'quizmaster_load_statistics',
                    overview: true,
                    pageLimit: $('#quizMaster_pageLimit').val(),
                    onlyCompleted: Number($('#quizMaster_onlyCompleted').is(':checked')),
                    page: $('#quizMaster_currentPage').val(),
                    generatePageNav: Number(changePageNav)
                };

                $.post(
                    url,
                    data,
                    function (json) {
                        $('#quizMaster_statistics_overview_data').empty();

                        if (currectTab != 'quizMaster_typeOverview') {
                            return;
                        }

                        var item = $('<tr>'
                            + '<th><a href="#">---</a></th>'
                            + '<th class="quizMaster_points">---</th>'
                            + '<th class="quizMaster_cCorrect" style="color: green;">---</th>'
                            + '<th class="quizMaster_cIncorrect" style="color: red;">---</th>'
                            + '<th class="quizMaster_cTip">---</th>'
                            + '<th class="quizMaster_cResult" style="font-weight: bold;">---</th>'
                            + '</tr>'
                        );

                        $.each(json.items, function (i, v) {
                            var d = item.clone();

                            d.find('a').text(v.userName).data('userId', v.userId).click(function () {
                                $('#userSelect').val($(this).data('userId'));

                                $('#quizMaster_typeRegisteredUser').click();

                                return false;
                            });

                            if (v.completed) {
                                d.find('.quizMaster_points').text(v.cPoints);
                                d.find('.quizMaster_cCorrect').text(v.cCorrect + ' (' + v.pCorrect + '%)');
                                d.find('.quizMaster_cIncorrect').text(v.cIncorrect + ' (' + v.pIncorrect + '%)');
                                d.find('.quizMaster_cTip').text(v.cTip);
                                d.find('.quizMaster_cResult').text(v.result + '%');
                            } else {
                                d.find('th').removeAttr('style');
                            }

                            $('#quizMaster_statistics_overview_data').append(d);
                        });

                        if (json.page != undefined) {
                            methode.setPageNav(json.page);
                            changePageNav = false;
                        }

                        $('#quizMaster_loadData').hide();
                        $('#quizMaster_statistics_overview').show();
                    },
                    'json'
                );
            },

            loadFormOverview: function () {
                $('#quizMaster_tabFormOverview').show();
            },

            changeTab: function (id) {
                currectTab = id;

                if (id == 'quizMaster_typeRegisteredUser') {
                    methode.loadStatistics($('#userSelect').val());
                } else if (id == 'quizMaster_typeAnonymeUser') {
                    methode.loadStatistics(0);
                } else if (id == 'quizMaster_typeForm') {
                    methode.loadFormOverview();
                } else {
                    methode.loadOverview();
                }
            },

            resetStatistic: function (complete) {
                var userId = (currectTab == 'quizMaster_typeRegisteredUser') ? $('#userSelect').val() : 0;
                var location = window.location.pathname + window.location.search;
                var url = location.replace('admin.php', 'admin-ajax.php') + '&action=reset';
                var data = {
                    action: 'quizmaster_statistics',
                    userId: userId,
                    'complete': complete
                };

                $.post(url, data, function (e) {
                    methode.changeTab(currectTab);
                });
            },

            setPageNav: function (page) {
                page = Math.ceil(page / $('#quizMaster_pageLimit').val());
                $('#quizMaster_currentPage').empty();

                for (var i = 1; i <= page; i++) {
                    $(document.createElement('option'))
                        .val(i)
                        .text(i)
                        .appendTo($('#quizMaster_currentPage'));
                }

                $('#quizMaster_pageLeft, #quizMaster_pageRight').hide();

                if ($('#quizMaster_currentPage option').length > 1) {
                    $('#quizMaster_pageRight').show();

                }
            }
        };

        var init = function () {
            $('.quizMaster_tab').click(function (e) {
                var $this = $(this);

                if ($this.hasClass('button-primary')) {
                    return false;
                }

                if ($this.attr('id') == 'quizMaster_typeRegisteredUser') {
                    $('#quizMaster_userBox').show();
                } else {
                    $('#quizMaster_userBox').hide();
                }

                $('.quizMaster_tab').removeClass('button-primary').addClass('button-secondary');
                $this.removeClass('button-secondary').addClass('button-primary');

                methode.changeTab($this.attr('id'));

                return false;
            });

            $('#userSelect').change(function () {
                methode.changeTab('quizMaster_typeRegisteredUser');
            });

            $('.quizMaster_update').click(function () {
                methode.changeTab(currectTab);

                return false;
            });

            $('#quizMaster_reset').click(function () {

                var c = confirm(quizMasterLocalize.reset_statistics_msg);

                if (c) {
                    methode.resetStatistic(false);
                }

                return false;
            });

            $('.quizMaster_resetComplete').click(function () {

                var c = confirm(quizMasterLocalize.reset_statistics_msg);

                if (c) {
                    methode.resetStatistic(true);
                }

                return false;
            });

            $('#quizMaster_pageLimit, #quizMaster_onlyCompleted').change(function () {
                $('#quizMaster_currentPage').val(0);
                changePageNav = true;
                methode.changeTab(currectTab);

                return false;
            });

            $('#quizMaster_currentPage').change(function () {
                $('#quizMaster_pageLeft, #quizMaster_pageRight').hide();

                if ($('#quizMaster_currentPage option').length == 1) {

                } else if ($('#quizMaster_currentPage option:first-child:selected').length) {
                    $('#quizMaster_pageRight').show();
                } else if ($('#quizMaster_currentPage option:last-child:selected').length) {
                    $('#quizMaster_pageLeft').show();
                } else {
                    $('#quizMaster_pageLeft, #quizMaster_pageRight').show();
                }

                methode.changeTab(currectTab);
            });

            $('#quizMaster_pageRight').click(function () {
                $('#quizMaster_currentPage option:selected').next().attr('selected', 'selected');
                $('#quizMaster_currentPage').change();

                return false;
            });

            $('#quizMaster_pageLeft').click(function () {
                $('#quizMaster_currentPage option:selected').prev().attr('selected', 'selected');
                $('#quizMaster_currentPage').change();

                return false;
            });

            methode.changeTab('quizMaster_typeAnonymeUser');
        };

        init();
    };

    $.fn.quizMaster_toplist = function () {
        function ajaxPost(func, data, success) {
            var d = {
                action: 'quizmaster_admin_ajax',
                func: func,
                data: data
            };

            $.post(ajaxurl, d, success, 'json');
        }

        var elements = {
            sort: $('#quizMaster_sorting'),
            pageLimit: $('#quizMaster_pageLimit'),
            currentPage: $('#quizMaster_currentPage'),
            loadDataBox: $('#quizMaster_loadData'),
            pageLeft: $('#quizMaster_pageLeft'),
            pageRight: $('#quizMaster_pageRight'),
            dataBody: $('#quizMaster_toplistTable tbody'),
            rowClone: $('#quizMaster_toplistTable tbody tr:eq(0)').clone(),
            content: $('#qm-quiz-content')
        };

        var methods = {
            loadData: function (action) {
                //var location = window.location.pathname + window.location.search;
                //var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_toplist';
                var th = this;
                var data = {
                    //action: 'quizmaster_load_toplist',
                    sort: elements.sort.val(),
                    limit: elements.pageLimit.val(),
                    page: elements.currentPage.val(),
                    quizId: $('input[name="ajax_quiz_id"]').val()
                };

                if (action != undefined) {
                    $.extend(data, action);
                }

                elements.loadDataBox.show();
                elements.content.hide();

                //$.post(url, data, function (json) {
                //    //methods.handleDataRequest(json.data);
                //    th.handleDataRequest(json.data);
                //
                //    if (json.nav != undefined) {
                //        //methods.handleNav(json.nav);
                //        th.handleNav(json.nav);
                //    }
                //
                //    elements.loadDataBox.hide();
                //    elements.content.show();
                //}, 'json');

                ajaxPost('adminToplist', data, function (json) {
                    th.handleDataRequest(json.data);

                    if (json.nav != undefined) {
                        th.handleNav(json.nav);
                    }

                    elements.loadDataBox.hide();
                    elements.content.show();
                });
            },

            handleNav: function (nav) {
                elements.currentPage.empty();

                for (var i = 1; i <= nav.pages; i++) {
                    $(document.createElement('option'))
                        .val(i).text(i)
                        .appendTo(elements.currentPage);
                }

                this.checkNav();
            },

            handleDataRequest: function (json) {
                var methods = this;

                elements.dataBody.empty();

                $.each(json, function (i, v) {
                    var data = elements.rowClone.clone().children();

                    data.eq(0).children().val(v.id);
                    data.eq(1).find('strong').text(v.name);
                    data.eq(1).find('.inline_editUsername').val(v.name);
                    data.eq(2).find('.quizMaster_email').text(v.email);
                    data.eq(2).find('input').val(v.email);
                    data.eq(3).text(v.type);
                    data.eq(4).text(v.date);
                    data.eq(5).text(v.points);
                    data.eq(6).text(v.result);

                    data.parent().show().appendTo(elements.dataBody);
                });

                if (!json.length) {
                    $(document.createElement('td'))
                        .attr('colspan', '7')
                        .text(quizMasterLocalize.no_data_available)
                        .css({
                            'font-weight': 'bold',
                            'text-align': 'center',
                            'padding': '5px'
                        })
                        .appendTo(document.createElement('tr'))
                        .appendTo(elements.dataBody);
                }

                $('.quizMaster_delete').click(function () {
                    if (confirm(quizMasterLocalize.confirm_delete_entry)) {
                        var id = new Array($(this).closest('tr').find('input[name="checkedData[]"]').val());

                        methods.loadData({
                            a: 'delete',
                            toplistIds: id
                        });
                    }

                    return false;
                });

                $('.quizMaster_edit').click(function () {
                    var $contain = $(this).closest('tr');

                    $contain.find('.row-actions').hide();
                    $contain.find('.inline-edit').show();

                    $contain.find('.quizMaster_username, .quizMaster_email').hide();
                    $contain.find('.inline_editUsername, .inline_editEmail').show();

                    return false;
                });

                $('.inline_editSave').click(function () {
                    var $contain = $(this).closest('tr');
                    var username = $contain.find('.inline_editUsername').val();
                    var email = $contain.find('.inline_editEmail').val();

                    if (methods.isEmpty(username) || methods.isEmpty(email)) {
                        alert(quizMasterLocalize.not_all_fields_completed);

                        return false;
                    }

                    methods.loadData({
                        a: 'edit',
                        toplistId: $contain.find('input[name="checkedData[]"]').val(),
                        name: username,
                        email: email
                    });

                    return false;
                });

                $('.inline_editCancel').click(function () {
                    var $contain = $(this).closest('tr');

                    $contain.find('.row-actions').show();
                    $contain.find('.inline-edit').hide();

                    $contain.find('.quizMaster_username, .quizMaster_email').show();
                    $contain.find('.inline_editUsername, .inline_editEmail').hide();

                    $contain.find('.inline_editUsername').val($contain.find('.quizMaster_username').text());
                    $contain.find('.inline_editEmail').val($contain.find('.quizMaster_email').text());

                    return false;
                });
            },

            checkNav: function () {
                var n = elements.currentPage.val();

                if (n == 1) {
                    elements.pageLeft.hide();
                } else {
                    elements.pageLeft.show();
                }

                if (n == elements.currentPage.children().length) {
                    elements.pageRight.hide();
                } else {
                    elements.pageRight.show();
                }
            },

            isEmpty: function (text) {
                text = $.trim(text);

                return (!text || 0 === text.length);
            }
        };

        var init = function () {
            elements.sort.change(function () {
                methods.loadData();
            });

            elements.pageLimit.change(function () {
                methods.loadData({nav: 1});
            });

            elements.currentPage.change(function () {
                methods.checkNav();
                methods.loadData();
            });

            elements.pageLeft.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) - 1);
                methods.checkNav();
                methods.loadData();
            });

            elements.pageRight.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) + 1);
                methods.checkNav();
                methods.loadData();
            });

            $('#quizMaster_deleteAll').click(function () {
                methods.loadData({a: 'deleteAll'});
            });

            $('#quizMaster_action').click(function () {
                var name = $('#quizMaster_actionName').val();

                if (name != '0') {

                    var ids = $('input[name="checkedData[]"]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    methods.loadData({
                        a: name,
                        toplistIds: ids
                    });
                }
            });

            $('#quizMaster_checkedAll').change(function () {
                if (this.checked)
                    $('input[name="checkedData[]"]').attr('checked', 'checked');
                else
                    $('input[name="checkedData[]"]').removeAttr('checked', 'checked');
            });

            methods.loadData({nav: 1});
        };

        init();
    };

    if ($('.quizMaster_quizOverall').length)
        $('.quizMaster_quizOverall').quizMaster_preview();

    if ($('.quizMaster_quizOverall').length) {
        $('.quizMaster_quizOverall').quizMaster_quizOverall();
    }

    if ($('.quizMaster_quizEdit').length)
        $('.quizMaster_quizEdit').quizMaster_quizEdit();

//	if($('.quizMaster_questionEdit').length)
//		$('.quizMaster_questionEdit').quizMaster_questionEdit();

    //if($('.quizMaster_questionOverall').length)
    //	$('.quizMaster_questionOverall').quizMaster_questionOverall();

//	if($('.quizMaster_statistics').length)
//		$('.quizMaster_statistics').quizMaster_statistics();

    if ($('.quizMaster_toplist').length)
        $('.quizMaster_toplist').quizMaster_toplist();

    /**
     * NEW
     */
    /**
     * @memberOf QuizMaster_Admin
     */
    function QuizMaster_Admin() {
        var global = this;

        global = {
            displayChecked: function (t, box, neg, disabled) {
                var c = neg ? !t.checked : t.checked;

                if (disabled)
                    c ? box.attr('disabled', 'disabled') : box.removeAttr('disabled');
                else
                    c ? box.show() : box.hide();
            },

            isEmpty: function (text) {
                text = $.trim(text);

                return (!text || 0 === text.length);
            },

            isNumber: function (number) {
                number = $.trim(number);
                return !global.isEmpty(number) && !isNaN(number);
            },

            getMceContent: function (id) {
                var editor = tinymce.editors[id];

                if (editor != undefined && !editor.isHidden()) {
                    return editor.getContent();
                }

                return $('#' + id).val();
            },

            ajaxPost: function (func, data, success) {
                var d = {
                    action: 'quizmaster_admin_ajax',
                    func: func,
                    data: data
                };

                $.post(ajaxurl, d, success, 'json');
            }
        };

        var tabWrapper = function () {
            $('.quizMaster_tab_wrapper a').click(function () {
                var $this = $(this);
                var tabId = $this.data('tab');
                var currentTab = $this.siblings('.button-primary').removeClass('button-primary').addClass('button-secondary');

                $this.removeClass('button-secondary').addClass('button-primary');

                $(currentTab.data('tab')).hide('fast');
                $(tabId).show('fast');

                $(document).trigger({
                    type: 'changeTab',
                    tabId: tabId
                });

                return false;
            });
        };

        var module = {
            /**
             * @memberOf QuizMaster_admin.module
             */

            gobalSettings: function () {
                var methode = {
                    categoryDelete: function (id, type) {
                        var data = {
                            categoryId: id
                        };

                        global.ajaxPost('categoryDelete', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            $('select[name="category' + type + '"] option[value="' + id + '"]').remove();
                            $('select[name="category' + type + '"]').change();
                        });
                    },

                    categoryEdit: function (id, name, type) {
                        var data = {
                            categoryId: id,
                            categoryName: $.trim(name)
                        };

                        if (global.isEmpty(name)) {
                            alert(quizMasterLocalize.category_no_name);
                            return;
                        }

                        global.ajaxPost('categoryEdit', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            $('select[name="category' + type + '"] option[value="' + id + '"]').text(data.categoryName);
                            $('select[name="category' + type + '"]').change();
                        });
                    },

                    changeTimeFormat: function (inputName, $select) {
                        if ($select.val() != "0")
                            $('input[name="' + inputName + '"]').val($select.val());
                    },

                    templateDelete: function (id, type) {
                        var data = {
                            templateId: id,
                            type: type
                        };

                        global.ajaxPost('templateDelete', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            if (!type) {
                                $('select[name="templateQuiz"] option[value="' + id + '"]').remove();
                                $('select[name="templateQuiz"]').change();
                            } else {
                                $('select[name="templateQuestion"] option[value="' + id + '"]').remove();
                                $('select[name="templateQuestion"]').change();
                            }
                        });
                    },

                    templateEdit: function (id, name, type) {

                        if (global.isEmpty(name)) {
                            alert(quizMasterLocalize.category_no_name);
                            return;
                        }

                        var data = {
                            templateId: id,
                            name: $.trim(name),
                            type: type
                        };

                        global.ajaxPost('templateEdit', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            if (!type) {
                                $('select[name="templateQuiz"] option[value="' + id + '"]').text(data.name);
                                $('select[name="templateQuiz"]').change();
                            } else {
                                $('select[name="templateQuestion"] option[value="' + id + '"]').text(data.name);
                                $('select[name="templateQuestion"]').change();
                            }
                        });
                    }
                };

                var init = function () {
//					$('.quizMaster_tab').click(function() {
//						var $this = $(this);
//
//						$('.quizMaster_tab').removeClass('button-primary').addClass('button-secondary');
//						$this.removeClass('button-secondary').addClass('button-primary');
//
//						$('#problemInfo, #problemContent, #globalContent').hide('fast');
//
//						if($this.attr('id') == 'globalTab') {
//							$('#globalContent').show('fast');
//						} else {
//							$('#problemInfo, #problemContent').show('fast');
//						}
//					});

                    $('select[name="category"]').change(function () {
                        $('input[name="categoryEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('input[name="categoryDelete"]').click(function () {
                        var id = $('select[name="category"] option:selected').val();

                        methode.categoryDelete(id, '');
                    });

                    $('input[name="categoryEdit"]').click(function () {
                        var id = $('select[name="category"] option:selected').val();
                        var text = $('input[name="categoryEditText"]').val();

                        methode.categoryEdit(id, text, '');
                    });

                    $('select[name="categoryQuiz"]').change(function () {
                        $('input[name="categoryQuizEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('input[name="categoryQuizDelete"]').click(function () {
                        var id = $('select[name="categoryQuiz"] option:selected').val();

                        methode.categoryDelete(id, 'Quiz');
                    });

                    $('input[name="categoryQuizEdit"]').click(function () {
                        var id = $('select[name="categoryQuiz"] option:selected').val();
                        var text = $('input[name="categoryQuizEditText"]').val();

                        methode.categoryEdit(id, text, 'Quiz');
                    });

                    $('#statistic_time_format_select').change(function () {
                        methode.changeTimeFormat('statisticTimeFormat', $(this));
                    });

                    $(document).bind('changeTab', function (data) {
                        $('#problemInfo').hide('fast');

                        switch (data.tabId) {
                            case '#problemContent':
                                $('#problemInfo').show('fast');
                                break;
                            case '#emailSettingsTab':
                                break;
                        }
                    });

                    $('input[name="email[html]"]').change(function () {
                        if (switchEditors == undefined)
                            return false;

                        if (this.checked) {
                            switchEditors.go('adminEmailEditor', 'tmce');
                        } else {
                            switchEditors.go('adminEmailEditor', 'html');
                        }

                    }).change();

                    $('input[name="userEmail[html]"]').change(function () {
                        if (switchEditors == undefined)
                            return false;

                        if (this.checked) {
                            switchEditors.go('userEmailEditor', 'tmce');
                        } else {
                            switchEditors.go('userEmailEditor', 'html');
                        }

                    }).change();

                    $('select[name="templateQuiz"]').change(function () {
                        $('input[name="templateQuizEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('select[name="templateQuestion"]').change(function () {
                        $('input[name="templateQuestionEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('input[name="templateQuizDelete"]').click(function () {
                        var id = $('select[name="templateQuiz"] option:selected').val();

                        methode.templateDelete(id, 0);
                    });

                    $('input[name="templateQuestionDelete"]').click(function () {
                        var id = $('select[name="templateQuestion"] option:selected').val();

                        methode.templateDelete(id, 1);
                    });

                    $('input[name="templateQuizEdit"]').click(function () {
                        var id = $('select[name="templateQuiz"] option:selected').val();
                        var text = $('input[name="templateQuizEditText"]').val();

                        methode.templateEdit(id, text, 0);
                    });

                    $('input[name="templateQuestionEdit"]').click(function () {
                        var id = $('select[name="templateQuestion"] option:selected').val();
                        var text = $('input[name="templateQuestionEditText"]').val();

                        methode.templateEdit(id, text, 1);
                    });
                };

                init();
            },

            questionEdit: function () {
                var methode = this;
                var filter = $.noop();

                var elements = {
                    answerChildren: $('.answer_felder > div'),
                    pointsModus: $('input[name="answerPointsActivated"]'),
                    gPoints: $('input[name="points"]')
                };

                methode = {
                    generateArrayIndex: function () {
                        var type = $('input[name="answerType"]:checked').val();
                        type = (type == 'single' || type == 'multiple') ? 'classic_answer' : type;

                        $('.answerList').each(function () {
                            var currentType = $(this).parent().attr('class');

                            $(this).children().each(function (i, v) {
                                $(this).find('[name^="answerData"]').each(function () {
                                    var name = this.name;
                                    var x = name.search(/\](\[\w+\])+$/);
                                    var n = (type == currentType) ? i : 'none';

                                    if (x > 0) {
                                        this.name = 'answerData[' + n + name.substring(x, name.length);

                                    }
                                });
                            });
                        });
                    },

                    globalValidate: function () {
                        if (global.isEmpty(global.getMceContent('question'))) {
                            alert(quizMasterLocalize.no_question_msg);

                            return false;
                        }

                        if (!elements.pointsModus.is(':checked')) {
                            var p = elements.gPoints.val();

                            if (!global.isNumber(p) || p < 1) {
                                alert(quizMasterLocalize.no_nummber_points);

                                return false;
                            }
                        } else {
                            if ($('input[name="answerType"]:checked').val() == 'free_answer') {
                                alert(quizMasterLocalize.dif_points);
                                return false;
                            }
                        }

                        if (filter() === false)
                            return false;

                        return true;
                    },

                    answerRemove: function () {
                        var li = $(this).parent();

                        if (li.parent().children().length < 2)
                            return false;

                        li.remove();

                        return false;
                    },

                    addCategory: function () {
                        var name = $.trim($('input[name="categoryAdd"]').val());

                        if (global.isEmpty(name)) {
                            return;
                        }

                        var data = {
                            categoryName: name
                        };

                        global.ajaxPost('categoryAdd', data, function (json) {
                            if (json.err) {
                                $('#categoryMsgBox').text(json.err).show('fast').delay(2000).hide('fast');
                                return;
                            }

                            var $option = $(document.createElement('option'))
                                .val(json.categoryId)
                                .text(json.categoryName)
                                .attr('selected', 'selected');

                            $('select[name="category"]').append($option).change();

                        });
                    },

                    addMediaClick: function () {
                        if (typeof tb_show != "function")
                            return false;

                        var closest = $(this).closest('li');
                        var htmlCheck = closest.find('input[name="answerData[][html]"]:eq(0)');
                        var field = closest.find('.qm-start-box:eq(0)');

                        window.org_send_to_editor = window.send_to_editor;
                        var org_tb_remove = tb_remove;

                        window.send_to_editor = function (html) {
                            var img = $('img', html)[0].outerHTML;

                            field.val(field.val() + img);
                            htmlCheck.attr('checked', true);

                            tb_remove();

                            window.send_to_editor = window.org_send_to_editor;
                        };

                        window.tb_remove = function () {
                            window.send_to_editor = window.org_send_to_editor;
                            tb_remove = org_tb_remove;

                            tb_remove();
                        };

                        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                    }
                };

                var validate = {
                    classic_answer: function () {
                        var findText = 0;
                        var findCorrect = 0;
                        var findPoints = 0;

                        $('.classic_answer .answerList').children().each(function () {
                            var t = $(this);

                            if (!global.isEmpty(t.find('textarea[name="answerData[][answer]"]').val())) {
                                findText++;

                                if (t.find('input[name="answerData[][correct]"]:checked').length) {
                                    findCorrect++;
                                }

                                var p = t.find('input[name="answerData[][points]"]').val();

                                if (global.isNumber(p) && p >= 0) {
                                    findPoints++;
                                }
                            }
                        });

                        if (!findText) {
                            alert(quizMasterLocalize.no_answer_msg);
                            return false;
                        }

                        if (!findCorrect && !($('input[name="disableCorrect"]').is(':checked')
                            && $('input[name="answerPointsDiffModusActivated"]').is(':checked')
                            && $('input[name="answerPointsActivated"]').is(':checked')
                            && $('input[name="answerType"]:checked').val() == 'single')) {
                            alert(quizMasterLocalize.no_correct_msg);
                            return false;
                        }

                        if (findPoints != findText && elements.pointsModus.is(':checked')) {
                            alert(quizMasterLocalize.no_nummber_points_new);
                            return false;
                        }

                        return true;
                    },

                    free_answer: function () {
                        if (global.isEmpty($('.free_answer textarea[name="answerData[][answer]"]').val())) {
                            alert(quizMasterLocalize.no_answer_msg);
                            return false;
                        }

                        return true;
                    },

                    fill_blank: function () {
                        if (global.isEmpty(global.getMceContent('cloze'))) {
                            alert(quizMasterLocalize.no_answer_msg);
                            return false;
                        }

                        return true;
                    },

                    sort_answer: function () {
                        var findText = 0;
                        var findPoints = 0;

                        $('.sort_answer .answerList').children().each(function () {
                            var t = $(this);

                            if (!global.isEmpty(t.find('textarea[name="answerData[][answer]"]').val())) {
                                findText++;

                                var p = t.find('input[name="answerData[][points]"]').val();

                                if (global.isNumber(p) && p >= 0) {
                                    findPoints++;
                                }
                            }
                        });

                        if (!findText) {
                            alert(quizMasterLocalize.no_answer_msg);
                            return false;
                        }

                        if (findPoints != findText && elements.pointsModus.is(':checked')) {
                            alert(quizMasterLocalize.no_nummber_points_new);
                            return false;
                        }

                        return true;
                    },

                    matrix_sort_answer: function () {
                        var findText = 0;
                        var findPoints = 0;
                        var sortString = true;
                        var menge = 0;

                        $('.matrix_sort_answer .answerList').children().each(function () {
                            var t = $(this);
                            var p = t.find('input[name="answerData[][points]"]').val();

                            if (!global.isEmpty(t.find('textarea[name="answerData[][answer]"]').val())) {
                                findText++;
                                menge++;

                                if (global.isEmpty(t.find('textarea[name="answerData[][sort_string]"]').val())) {
                                    sortString = false;
                                }

                                if (global.isNumber(p) && p >= 0) {
                                    findPoints++;
                                }
                            } else {
                                if (!global.isEmpty(t.find('textarea[name="answerData[][sort_string]"]').val())) {
                                    menge++;

                                    if (global.isNumber(p) && p >= 0) {
                                        findPoints++;
                                    }
                                }
                            }
                        });

                        if (!findText) {
                            alert(quizMasterLocalize.no_answer_msg);
                            return false;
                        }

                        if (!sortString) {
                            alert(quizMasterLocalize.no_sort_element_criterion);
                            return false;
                        }

                        if (findPoints != menge && elements.pointsModus.is(':checked')) {
                            alert(quizMasterLocalize.no_nummber_points_new);
                            return false;
                        }

                        return true;
                    },

                    assessment_answer: function () {
                        if (global.isEmpty(global.getMceContent('assessment'))) {
                            alert(quizMasterLocalize.no_answer_msg);
                            return false;
                        }

                        return true;
                    }
                };

                var formListener = function () {
                    $('#quizMaster_tip').change(function () {
                        global.displayChecked(this, $('#quizMaster_tipBox'));
                    }).change();

                    $('#quizMaster_correctSameText').change(function () {
                        global.displayChecked(this, $('#quizMaster_incorrectMassageBox'), true);
                    }).change();

                    $('input[name="answerType"]').click(function () {
                        elements.answerChildren.hide();
                        var v = this.value;

                        if (v == 'single') {
                            $('#singleChoiceOptions').show();
                            $('input[name="disableCorrect"]').change();
                        } else {
                            $('#singleChoiceOptions').hide();
                            $('.classic_answer .quizMaster_classCorrect').parent().parent().show();
                        }

                        if (v == 'single' || v == 'multiple') {
                            var type = (v == 'single') ? 'radio' : 'checkbox';
                            v = 'classic_answer';

                            $('.quizMaster_classCorrect').each(function () {
                                $("<input type=" + type + " />")
                                    .attr({
                                        name: this.name,
                                        value: this.value,
                                        checked: this.checked
                                    })
                                    .addClass('quizMaster_classCorrect quizMaster_checkbox')
                                    .insertBefore(this);
                            }).remove();
                        }

                        filter = (validate[v] != undefined) ? validate[v] : $.noop();

                        $('.' + v).show();
                    });

                    $('input[name="answerType"]:checked').click();

                    $('.deleteAnswer').click(methode.answerRemove);

                    $('.addAnswer').click(function () {
                        var ul = $(this).siblings('ul');
                        var clone = ul.find('li:eq(0)').clone();

                        clone.find('.quizMaster_checkbox').removeAttr('checked');
                        clone.find('.qm-start-box').val('');
                        clone.find('.quizMaster_points').val(1);
                        clone.find('.deleteAnswer').click(methode.answerRemove);
                        clone.find('.addMedia').click(methode.addMediaClick);

                        clone.appendTo(ul);

                        return false;
                    });

                    $('.sort_answer ul, .classic_answer ul, .matrix_sort_answer ul').sortable({
                        handle: '.quizMaster_move'
                    });

                    $('#saveQuestion').click(function () {
                        if (!methode.globalValidate()) {
                            return false;
                        }

                        methode.generateArrayIndex();

                        return true;
                    });

                    $(elements.pointsModus).change(function () {
                        global.displayChecked(this, $('.quizMaster_answerPoints'));
                        global.displayChecked(this, $('#quizMaster_showPointsBox'));
                        global.displayChecked(this, elements.gPoints, false, true);
                        global.displayChecked(this, $('input[name="answerPointsDiffModusActivated"]'), true, true);

                        if (this.checked) {
                            $('input[name="answerPointsDiffModusActivated"]').change();
                            $('input[name="disableCorrect"]').change();
                        } else {
                            $('.classic_answer .quizMaster_classCorrect').parent().parent().show();
                            $('input[name="disableCorrect"]').attr('disabled', 'disabled');
                        }
                    }).change();

                    $('select[name="category"]').change(function () {
                        var $this = $(this);
                        var box = $('#categoryAddBox').hide();

                        if ($this.val() == "-1") {
                            box.show();
                        }

                    }).change();

                    $('#categoryAddBtn').click(function () {
                        methode.addCategory();
                    });

                    $('.addMedia').click(methode.addMediaClick);

                    $('input[name="answerPointsDiffModusActivated"]').change(function () {
                        global.displayChecked(this, $('input[name="disableCorrect"]'), true, true);

                        if (this.checked)
                            $('input[name="disableCorrect"]').change();
                        else
                            $('.classic_answer .quizMaster_classCorrect').parent().parent().show();
                    }).change();

                    $('input[name="disableCorrect"]').change(function () {
                        global.displayChecked(this, $('.classic_answer .quizMaster_classCorrect').parent().parent(), true);
                    }).change();

                    $('#clickPointDia').click(function () {
                        $('.pointDia').toggle('fast');

                        return false;
                    });

                    $('input[name="template"]').click(function (e) {
                        if ($('select[name="templateSaveList"]').val() == '0') {
                            if (global.isEmpty($('input[name="templateName"]').val())) {
                                alert(quizMasterLocalize.temploate_no_name);

                                e.preventDefault();
                                return false;
                            }
                        }

                        methode.generateArrayIndex();
                    });

                    $('select[name="templateSaveList"]').change(function () {
                        var $templateName = $('input[name="templateName"]');

                        if ($(this).val() == '0') {
                            $templateName.show();
                        } else {
                            $templateName.hide();
                        }
                    }).change();
                };

                var init = function () {
                    elements.answerChildren.hide();

                    formListener();
                };

                init();
            },

            statistic: function () {

                var methode = this;

                var quizId = $('#quizId').val();

                var currentTab = 'users';

                var elements = {
                    currentPage: $('#quizMaster_currentPage'),
                    pageLeft: $('#quizMaster_pageLeft'),
                    pageRight: $('#quizMaster_pageRight'),
                    testSelect: $('#testSelect')

                };

                methode = {
                    loadStatistic: function (userId, callback) {
                        var data = {
                            userId: userId
                        };

                        global.ajaxPost('statisticLoad', data, function (json) {

                        });
                    },

                    loadUsersStatistic: function () {
                        //var userId = $('#userSelect').val();
                        //
                        //var data = {
                        //    userId: userId,
                        //    quizId: quizId,
                        //    testId: $('#testSelect').val()
                        //};
                        //
                        //methode.toggleLoadBox(false);
                        //
                        //global.ajaxPost('statisticLoad', data, function (json) {
                        //    $.each(json.question, function () {
                        //        var $tr = $('#quizMaster_tr_' + this.questionId);
                        //
                        //        methode.setStatisticData($tr, this);
                        //    });
                        //
                        //    $.each(json.category, function (i, v) {
                        //        var $tr = $('#quizMaster_ctr_' + i);
                        //
                        //        methode.setStatisticData($tr, v);
                        //    });
                        //
                        //    $('#testSelect option:gt(0)').remove();
                        //    var $testSelect = $('#testSelect');
                        //
                        //    $.each(json.tests, function () {
                        //        var $option = $(document.createElement('option'));
                        //
                        //        $option.val(this.id);
                        //        $option.text(this.date);
                        //
                        //        if (json.testId == this.id)
                        //            $option.attr('selected', true);
                        //
                        //        $testSelect.append($option);
                        //    });
                        //
                        //    methode.parseFormData(json.formData);
                        //
                        //    methode.toggleLoadBox(true);
                        //});
                    },

                    loadUsersStatistic_: function (userId, testId) {

                        var data = {
                            userId: userId,
                            quizId: quizId,
                            testId: testId
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticLoad', data, function (json) {
                            $.each(json.question, function () {
                                var $tr = $('#quizMaster_tr_' + this.questionId);

                                methode.setStatisticData($tr, this);
                            });

                            $.each(json.category, function (i, v) {
                                var $tr = $('#quizMaster_ctr_' + i);

                                methode.setStatisticData($tr, v);
                            });

                            $('#testSelect option:gt(0)').remove();
                            var $testSelect = $('#testSelect');

                            $.each(json.tests, function () {
                                var $option = $(document.createElement('option'));

                                $option.val(this.id);
                                $option.text(this.date);

                                if (json.testId == this.id)
                                    $option.attr('selected', true);

                                $testSelect.append($option);
                            });

                            methode.parseFormData(json.formData);

                            $('#userSelect').val(userId);
                            $('#testSelect').val(testId);

                            methode.toggleLoadBox(true);
                        });
                    },

                    parseFormData: function (data) {
                        var $formBox = $('#quizMaster_form_box');

                        if (data == null) {
                            $formBox.hide();
                            return;
                        }

                        $.each(data, function (i, v) {
                            $('#form_id_' + i).text(v);
                        });

                        $formBox.show();
                    },

                    setStatisticData: function ($o, v) {
                        $o.find('.quizMaster_cCorrect').text(v.correct);
                        $o.find('.quizMaster_cIncorrect').text(v.incorrect);
                        $o.find('.quizMaster_cTip').text(v.hint);
                        $o.find('.quizMaster_cPoints').text(v.points);
                        $o.find('.quizMaster_cResult').text(v.result);
                        $o.find('.quizMaster_cTime').text(v.questionTime);
                        $o.find('.quizMaster_cCreateTime').text(v.date);
                    },

                    toggleLoadBox: function (show) {
                        var $loadBox = $('#quizMaster_loadData');
                        var $content = $('#qm-quiz-content');

                        if (show) {
                            $loadBox.hide();
                            $content.show();
                        } else {
                            $content.hide();
                            $loadBox.show();
                        }
                    },

                    reset: function (type) {
                        var userId = $('#userSelect').val();

                        if (!confirm(quizMasterLocalize.reset_statistics_msg)) {
                            return;
                        }

                        var data = {
                            quizId: quizId,
                            userId: userId,
                            testId: elements.testSelect.val(),
                            type: type
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticReset', data, function () {
                            methode.loadUsersStatistic();
                        });
                    },

                    loadStatisticOverview: function (nav) {

                        var data = {
                            quizId: quizId,
                            pageLimit: $('#quizMaster_pageLimit').val(),
                            onlyCompleted: Number($('#quizMaster_onlyCompleted').is(':checked')),
                            page: elements.currentPage.val(),
                            nav: Number(nav)
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticLoadOverview', data, function (json) {
                            var $body = $('#quizMaster_statistics_overview_data');
                            var $tr = $body.children();
                            var $c = $tr.first().clone();

                            $tr.slice(1).remove();

                            $.each(json.items, function () {
                                var clone = $c.clone();

                                methode.setStatisticData(clone, this);

                                clone.find('a').text(this.userName).data('userId', this.userId).click(function () {
                                    $('#userSelect').val($(this).data('userId'));

                                    $('#quizMaster_typeUser').click();

                                    return false;
                                });

                                clone.show().appendTo($body);
                            });

                            $c.remove();

                            methode.toggleLoadBox(true);

                            if (json.page != undefined)
                                methode.handleNav(json.page);
                        });

                    },

                    handleNav: function (nav) {
                        var $p = $('#quizMaster_currentPage').empty();

                        for (var i = 1; i <= nav; i++) {
                            $(document.createElement('option'))
                                .val(i)
                                .text(i)
                                .appendTo($p);
                        }

                        methode.checkNavBar();
                    },

                    checkNavBar: function () {
                        var n = elements.currentPage.val();

                        if (n == 1) {
                            elements.pageLeft.hide();
                        } else {
                            elements.pageLeft.show();
                        }

                        if (n == elements.currentPage.children().length) {
                            elements.pageRight.hide();
                        } else {
                            elements.pageRight.show();
                        }
                    },

                    refresh: function () {
                        if (currentTab == 'users') {
                            methode.loadUsersStatistic();
                        } else if (currentTab == 'formOverview') {
                            methode.loadFormsOverview(true);
                        } else {
                            methode.loadStatisticOverview(true);
                        }
                    },

                    loadFormsOverview: function (nav) {
                        var data = {
                            quizId: quizId,
                            pageLimit: $('#quizMaster_fromPageLimit').val(),
                            onlyUser: $('#quizMaster_formUser').val(),
                            page: $('#quizMaster_formCurrentPage').val(),
                            nav: Number(nav)
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticLoadFormOverview', data, function (json) {
                            var $body = $('#quizMaster_statistics_form_data');
                            var $tr = $body.children();
                            var $c = $tr.first().clone();

                            $tr.slice(1).remove();

                            $.each(json.items, function () {
                                var clone = $c.clone();

                                methode.setStatisticData(clone, this);

                                clone.find('a').text(this.userName).data('userId', this.userId).data('testId', this.testId).click(function () {
                                    methode.switchTabOnLoad('users');
                                    methode.loadUsersStatistic_($(this).data('userId'), $(this).data('testId'));

                                    return false;
                                });

                                clone.show().appendTo($body);
                            });

                            $c.remove();

                            methode.toggleLoadBox(true);

                            if (json.page != undefined)
                                methode.handleFormNav(json.page);
                        });
                    },

                    handleFormNav: function (nav) {
                        var $p = $('#quizMaster_formCurrentPage').empty();

                        for (var i = 1; i <= nav; i++) {
                            $(document.createElement('option'))
                                .val(i)
                                .text(i)
                                .appendTo($p);
                        }

                        methode.checkFormNavBar();
                    },

                    checkFormNavBar: function () {
                        var n = $('#quizMaster_formCurrentPage').val();

                        if (n == 1) {
                            $('#quizMaster_formPageLeft').hide();
                        } else {
                            $('#quizMaster_formPageLeft').show();
                        }

                        if (n == $('#quizMaster_formCurrentPage').children().length) {
                            $('#quizMaster_formPageRight').hide();
                        } else {
                            $('#quizMaster_formPageRight').show();
                        }
                    },

                    switchTabOnLoad: function (name) {
                        $('.quizMaster_tab').removeClass('button-primary').addClass('button-secondary');
                        $('.quizMaster_tabContent').hide();

                        var $this = $('#quizMaster_typeOverview');

                        if (name == 'users') {
                            currentTab = 'users';
                            $('#quizMaster_tabUsers').show();
                            $this = $('#quizMaster_typeUser');
                        } else if (name == 'formOverview') {
                            currentTab = 'formOverview';
                            $('#quizMaster_tabFormOverview').show();
                            $this = $('#quizMaster_typeForm');
                        } else {
                            currentTab = 'overview';
                            $('#quizMaster_tabOverview').show();
                        }

                        $this.removeClass('button-secondary').addClass('button-primary');
                    }
                };

                var init = function () {

                    $('#userSelect, #testSelect').change(function () {
                        methode.loadUsersStatistic();
                    });

                    $('.quizMaster_update').click(function () {
                        methode.refresh();
                    });

                    $('#quizMaster_reset').click(function () {
                        methode.reset(0);
                    });

                    $('#quizMaster_resetUser').click(function () {
                        methode.reset(1);
                    });

                    $('.quizMaster_resetComplete').click(function () {
                        methode.reset(2);
                    });

                    $('.quizMaster_tab').click(function () {
                        var $this = $(this);

                        $('.quizMaster_tab').removeClass('button-primary').addClass('button-secondary');
                        $this.removeClass('button-secondary').addClass('button-primary');
                        $('.quizMaster_tabContent').hide();

                        if ($this.attr('id') == 'quizMaster_typeUser') {
                            currentTab = 'users';
                            $('#quizMaster_tabUsers').show();
                            methode.loadUsersStatistic();
                        } else if ($this.attr('id') == 'quizMaster_typeForm') {
                            currentTab = 'formOverview';
                            $('#quizMaster_tabFormOverview').show();
                            methode.loadFormsOverview(true);
                        } else {
                            currentTab = 'overview';
                            $('#quizMaster_tabOverview').show();
                            methode.loadStatisticOverview(true);
                        }

                        return false;
                    });

                    $('#quizMaster_onlyCompleted').change(function () {
                        elements.currentPage.val(1);
                        methode.loadStatisticOverview(true);
                    });

                    $('#quizMaster_pageLimit').change(function () {
                        elements.currentPage.val(1);
                        methode.loadStatisticOverview(true);
                    });

                    elements.pageLeft.click(function () {
                        elements.currentPage.val(Number(elements.currentPage.val()) - 1);
                        methode.loadStatisticOverview(false);
                        methode.checkNavBar();
                    });

                    elements.pageRight.click(function () {
                        elements.currentPage.val(Number(elements.currentPage.val()) + 1);
                        methode.loadStatisticOverview(false);
                        methode.checkNavBar();
                    });

                    elements.currentPage.change(function () {
                        methode.loadStatisticOverview(false);
                        methode.checkNavBar();
                    });

                    $('#quizMaster_formUser, #quizMaster_fromPageLimit').change(function () {
                        $('#quizMaster_formCurrentPage').val(1);
                        methode.loadFormsOverview(true);
                    });

                    $('#quizMaster_formPageLeft').click(function () {
                        $('#quizMaster_formCurrentPage').val(Number(elements.currentPage.val()) - 1);
                        methode.loadFormsOverview(false);
                        methode.checkFormNavBar();
                    });

                    $('#quizMaster_formPageRight').click(function () {
                        $('#quizMaster_formCurrentPage').val(Number(elements.currentPage.val()) + 1);
                        methode.loadFormsOverview(false);
                        methode.checkFormNavBar();
                    });

                    $('#quizMaster_formCurrentPage').change(function () {
                        methode.loadFormsOverview(false);
                        methode.checkFormNavBar();
                    });

                    methode.loadUsersStatistic();
                };

                init();
            },

            statisticNew: function () {
                var quizId = $('#quizId').val();
                var historyNavigator = null;
                var overviewNavigator = null;

                var historyFilter = {
                    data: {
                        quizId: quizId,
                        users: -1,
                        pageLimit: 100,
                        dateFrom: 0,
                        dateTo: 0,
                        generateNav: 0
                    },

                    changeFilter: function () {
                        var getTime = function (p) {
                            var date = p.datepicker('getDate');

                            return date === null ? 0 : date.getTime() / 1000;
                        };

                        $.extend(this.data, {
                            users: $('#quizMaster_historyUser').val(),
                            pageLimit: $('#quizMaster_historyPageLimit').val(),
                            dateFrom: getTime($('#datepickerFrom')),
                            dateTo: getTime($('#datepickerTo')),
                            generateNav: 1
                        });

                        return this.data;
                    }
                };

                var overviewFilter = {
                    data: {
                        pageLimit: 100,
                        onlyCompleted: 0,
                        generateNav: 0,
                        quizId: quizId
                    },

                    changeFilter: function () {
                        $.extend(this.data, {
                            pageLimit: $('#quizMaster_overviewPageLimit').val(),
                            onlyCompleted: Number($('#quizMaster_overviewOnlyCompleted').is(':checked')),
                            generateNav: 1
                        });
                    }
                };

                var deleteMethode = {
                    deleteUserStatistic: function (refId, userId) {
                        if (!confirm(quizMasterLocalize.reset_statistics_msg))
                            return false;

                        var data = {
                            refId: refId,
                            userId: userId,
                            quizId: quizId,
                            type: 0
                        };

                        global.ajaxPost('statisticResetNew', data, function () {
                            $('#quizMaster_user_overlay').hide();

                            historyFilter.changeFilter();
                            methode.loadHistoryAjax();

                            overviewFilter.changeFilter();
                            methode.loadOverviewAjax();

                        });
                    },

                    deleteAll: function () {
                        if (!confirm(quizMasterLocalize.reset_statistics_msg))
                            return false;

                        var data = {
                            quizId: quizId,
                            type: 1
                        };

                        global.ajaxPost('statisticResetNew', data, function () {
                            historyFilter.changeFilter();
                            methode.loadHistoryAjax();

                            overviewFilter.changeFilter();
                            methode.loadOverviewAjax();
                        });
                    }
                };

                var methode = {
                    loadHistoryAjax: function () {

                        var data = $.extend({
                            page: historyFilter.data.generateNav ? 1 : historyNavigator.getCurrentPage()
                        }, historyFilter.data);

                        methode.loadBox(true);
                        var content = $('#quizMaster_historyLoadContext').hide();

                        global.ajaxPost('statisticLoadHistory', data, function (json) {
                            content.html(json.html).show();

                            if (json.navi)
                                historyNavigator.setNumPage(json.navi);

                            historyFilter.data.generateNav = 0;

                            content.find('.user_statistic').click(function () {
                                methode.loadUserAjax(0, $(this).data('ref_id'), false);

                                return false;
                            });

                            content.find('.quizMaster_delete').click(function () {
                                deleteMethode.deleteUserStatistic($(this).parents('tr').find('.user_statistic').data('ref_id'), 0);

                                return false;
                            });

                            methode.loadBox(false);
                        });

                    },

                    loadUserAjax: function (userId, refId, avg) {
                        $('#quizMaster_user_overlay, #quizMaster_loadUserData').show();

                        var content = $('#quizMaster_user_content').hide();

                        var data = {
                            quizId: quizId,
                            userId: userId,
                            refId: refId,
                            avg: Number(avg)
                        };

                        global.ajaxPost('statisticLoadUser', data, function (json) {
                            content.html(json.html);

                            content.find('.quizMaster_update').click(function () {
                                methode.loadUserAjax(userId, refId, avg);

                                return false;
                            });

                            content.find('#quizMaster_resetUserStatistic').click(function () {
                                deleteMethode.deleteUserStatistic(refId, userId);
                            });

                            content.find('.statistic_data').click(function () {
                                $(this).parents('tr').next().toggle('fast');

                                return false;
                            });

                            $('#quizMaster_loadUserData').hide();
                            content.show();
                        });

                    },

                    loadBox: function (show, contain) {
                        if (show)
                            $('#quizMaster_loadDataHistory').show();
                        else
                            $('#quizMaster_loadDataHistory').hide();

                    },

                    loadOverviewAjax: function () {
                        var data = $.extend({
                            page: overviewFilter.data.generateNav ? 1 : overviewNavigator.getCurrentPage()
                        }, overviewFilter.data);

                        $('#quizMaster_loadDataOverview').show();

                        var content = $('#quizMaster_overviewLoadContext').hide();

                        global.ajaxPost('statisticLoadOverviewNew', data, function (json) {
                            content.html(json.html).show();

                            if (json.navi)
                                overviewNavigator.setNumPage(json.navi);

                            overviewFilter.data.generateNav = 0;

                            content.find('.user_statistic').click(function () {
                                methode.loadUserAjax($(this).data('user_id'), 0, true);

                                return false;
                            });

                            content.find('.quizMaster_delete').click(function () {
                                deleteMethode.deleteUserStatistic(0, $(this).parents('tr').find('.user_statistic').data('user_id'));

                                return false;
                            });

                            $('#quizMaster_loadDataOverview').hide();
                        });
                    }
                };

                var init = function () {
                    historyNavigator = new Navigator($('#historyNavigation'), {
                        onChange: function () {
                            methode.loadHistoryAjax();
                        }
                    });

                    overviewNavigator = new Navigator($('#overviewNavigation'), {
                        onChange: function () {
                            methode.loadOverviewAjax();
                        }
                    });

                    $('#datepickerFrom').datepicker({
                        closeText: quizMasterLocalize.closeText,
                        currentText: quizMasterLocalize.currentText,
                        monthNames: quizMasterLocalize.monthNames,
                        monthNamesShort: quizMasterLocalize.monthNamesShort,
                        dayNames: quizMasterLocalize.dayNames,
                        dayNamesShort: quizMasterLocalize.dayNamesShort,
                        dayNamesMin: quizMasterLocalize.dayNamesMin,
                        dateFormat: quizMasterLocalize.dateFormat,
                        firstDay: quizMasterLocalize.firstDay,

                        changeMonth: true,
                        onClose: function (selectedDate) {
                            $('#datepickerTo').datepicker('option', 'minDate', selectedDate);
                        }
                    });

                    $('#datepickerTo').datepicker({
                        closeText: quizMasterLocalize.closeText,
                        currentText: quizMasterLocalize.currentText,
                        monthNames: quizMasterLocalize.monthNames,
                        monthNamesShort: quizMasterLocalize.monthNamesShort,
                        dayNames: quizMasterLocalize.dayNames,
                        dayNamesShort: quizMasterLocalize.dayNamesShort,
                        dayNamesMin: quizMasterLocalize.dayNamesMin,
                        dateFormat: quizMasterLocalize.dateFormat,
                        firstDay: quizMasterLocalize.firstDay,

                        changeMonth: true,
                        onClose: function (selectedDate) {
                            $('#datepickerFrom').datepicker('option', 'maxDate', selectedDate);
                        }
                    });

                    $('#filter').click(function () {
                        historyFilter.changeFilter();
                        methode.loadHistoryAjax();
                    });

                    $('#quizMaster_overlay_close').click(function () {
                        $('#quizMaster_user_overlay').hide();
                    });

                    $('#quizMaster_tabHistory .quizMaster_update').click(function () {
                        historyFilter.changeFilter();
                        methode.loadHistoryAjax();

                        return false;
                    });

                    $('#quizMaster_tabOverview .quizMaster_update').click(function () {
                        overviewFilter.changeFilter();
                        methode.loadOverviewAjax();

                        return false;
                    });

                    $('.quizMaster_resetComplete').click(function () {
                        deleteMethode.deleteAll();

                        return false;
                    });

                    $('#overviewFilter').click(function () {
                        overviewFilter.changeFilter();
                        methode.loadOverviewAjax();
                    });

                    historyFilter.changeFilter();
                    methode.loadHistoryAjax();

                    overviewFilter.changeFilter();
                    methode.loadOverviewAjax();
                };

                init();
            }
        };

        var init = function () {
            tabWrapper();

            var m = $.noop;

            if ($('.quizMaster_questionEdit').length) {
                m = module.questionEdit;
            } else if ($('.quizMaster_globalSettings').length) {
                m = module.gobalSettings;
            } else if ($('.quizMaster_statistics').length) {
                m = module.statistic;
            } else if ($('.quizMaster_statisticsNew').length) {
                m = module.statisticNew;
            }

            m();

            $('.quizMaster_demoImgBox a').mouseover(function (e) {
                var $this = $(this);
                var d = $(document).width();
                var img = $this.siblings().outerWidth(true);

                if (e.pageX + img > d) {
                    var v = d - (e.pageX + img + 30);
                    $(this).next().css('left', v + "px");
                }

                $(this).next().show();

            }).mouseout(function () {
                $(this).next().hide();
            }).click(function () {
                return false;
            });
        };

        init();
    }

    QuizMaster_Admin();

    function Navigator(obj, option) {
        var defaultOption = {
            onChange: null
        };

        var elements = {
            contain: null,
            pageLeft: null,
            pageRight: null,
            currentPage: null
        };

        var checkNavBar = function () {
            var num = elements.currentPage.children().length;
            var cur = Number(elements.currentPage.val());

            elements.pageLeft.hide();
            elements.pageRight.hide();

            if (cur > 1)
                elements.pageLeft.show();

            if ((cur + 1) <= num)
                elements.pageRight.show();
        };

        var init = function () {
            $.extend(elements, {
                contain: obj,
                pageLeft: obj.find('.navigationLeft'),
                pageRight: obj.find('.navigationRight'),
                currentPage: obj.find('.navigationCurrentPage')
            });

            $.extend(defaultOption, option);

            elements.pageLeft.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) - 1);
                checkNavBar();

                if (defaultOption.onChange)
                    defaultOption.onChange(elements.currentPage.val());
            });

            elements.pageRight.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) + 1);
                checkNavBar();

                if (defaultOption.onChange)
                    defaultOption.onChange(elements.currentPage.val());
            });

            elements.currentPage.change(function () {
                checkNavBar();

                if (defaultOption.onChange)
                    defaultOption.onChange(elements.currentPage.val());
            });
        };

        this.getCurrentPage = function () {
            return elements.currentPage.val();
        }

        this.setNumPage = function (num) {
            elements.currentPage.empty();

            for (var i = 1; i <= num; i++) {
                $(document.createElement('option'))
                    .val(i)
                    .text(i)
                    .appendTo(elements.currentPage);
            }

            checkNavBar();
        }

        init();
    }
});
