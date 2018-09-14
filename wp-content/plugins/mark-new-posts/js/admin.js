var MarkNewPostsAdminForm = function ($, scriptOptions) {
	var MESSAGE_TIME = 3000;
	var ANIMATION_TIME = 300;
	var ui;
	var messageTimeout;

	$(document).ready(function() {
		initUi();

		initForm();

		var initialOptions = getOptionsFromForm();

		ui.saveOptionsBtn.click(function() {
			var options = getOptionsFromForm();
			if (!validateOptions(options)) {
				return;
			}
			var data = $.extend({}, options, {
				action: 'mark_new_posts_save_options'
			});
			clearMessage();
			setFormDisabled(true);
			$.post(ajaxurl, data, function (response) {
				var success = response.success;
				if (success) {
					initialOptions = options;
				}
				showMessage(success, response.message);
				setFormDisabled(false);
			});
		});

		ui.resetOptionsBtn.click(function() {
			ui.markerPlacement.val(initialOptions.markerPlacement);
			ui.markerType.val(initialOptions.markerType);
			ui.imageUrl.val(initialOptions.imageUrl);
			ui.openToRead.prop('checked', initialOptions.openToRead);
			ui.checkMarkup.prop('checked', initialOptions.checkMarkup);
			initForm();
			clearMessage();
		});

		ui.markerType.change(onMarkerTypeChange);
		ui.postStaysNew.change(onPostStaysNewChange);
	});

	var initUi = function() {
		ui = {
			markerPlacement: $('#mnp-show-marker-placement'),
			markerType: $('#mnp-marker-type'),
			imageRow: $('#mnp-image-row'),
			imageUrl: $('#mnp-image-url'),
			postStaysNew: $('#mnp-post-stays-new'),
			postStaysNewDays: $('#mnp-post-stays-new-days'),
			allNewForNewVisitor: $('#mnp-all-new-for-new-visitor'),
			checkMarkup: $('#mnp-check-markup'),
			saveOptionsBtn: $('#mnp-save-options-btn'),
			resetOptionsBtn: $('#mnp-reset-options-btn'),
			message: $('#mnp-message')
		};
	};

	var initForm = function() {
		onMarkerTypeChange();
		onPostStaysNewChange();
	}

	var getOptionsFromForm = function() {
		return {
			markerPlacement: ui.markerPlacement.val(),
			markerType: ui.markerType.val(),
			imageUrl: ui.imageUrl.val().trim(),
			markAfter: $('[name=mnp-mark-after]:checked').val(),
			postStaysNewDays: +ui.postStaysNewDays.val(),
			allNewForNewVisitor: ui.allNewForNewVisitor.is(':checked'),
			checkMarkup: ui.checkMarkup.is(':checked')
		};
	};

	var validateOptions = function(options) {
		var error = {
			field: null,
			message: null
		};
		if (options.markerType === scriptOptions.markerTypes.imageCustom && !options.imageUrl) {
			error.field = 'imageUrl';
			error.message = 'imageUrl';
		} else if (ui.postStaysNew.is(':checked') && !(options.postStaysNewDays > 0)) {
			error.field = 'postStaysNewDays';
			error.message = 'postStaysNewDays';
		}
		var noError = true;
		if (error.field) {
			noError = false;
			ui[error.field].focus();
			showMessage(false, scriptOptions.messages[error.message]);
		}
		return noError;
	};

	var onMarkerTypeChange = function(e) {
		toggle(ui.imageRow, ui.markerType.val() === scriptOptions.markerTypes.imageCustom, !e);
	};

	var onPostStaysNewChange = function() {
		var checked = ui.postStaysNew.is(':checked');
		ui.postStaysNewDays.prop('disabled', !checked);
		if (!checked) {
			ui.postStaysNewDays.val('');
		} else if (!ui.postStaysNewDays.val()) {
			ui.postStaysNewDays.val(1);
		}
	};

	var toggle = function(el, show, quick) {
		el[show ? 'show' : 'hide'](quick ? 0 : ANIMATION_TIME);
	};

	var showMessage = function(success, text) {
		var CLASS_SUCCESS = 'mnp-success';
		var CLASS_ERROR = 'mnp-error';
		ui.message
			.removeClass(CLASS_SUCCESS + ' ' + CLASS_ERROR)
			.addClass(success ? CLASS_SUCCESS : CLASS_ERROR)
			.text(text)
			.show();
		clearTimeout(messageTimeout);
		if (success) {
			messageTimeout = setTimeout(function() {
				ui.message.hide();
			}, MESSAGE_TIME);
		}
	};

	var clearMessage = function() {
		clearTimeout(messageTimeout);
		ui.message.hide();
	};

	var setFormDisabled = function(value) {
		$.each(ui, function(i, el) {
			$(el).prop('disabled', value);
		});
	};
};