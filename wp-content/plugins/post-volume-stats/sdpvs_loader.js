jQuery(document).ready(function($) {
	
	$("#sdpvs-notice > .notice-dismiss").click(function(e) {
		var data = {
			action : "sdpvs_admin_notice",
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
		});
		return false;
	});
	
	$('#select-all').click(function(e) {
			// Iterate each checkbox
			$(':checkbox').each(function() {
				this.checked = true;
			});
	});
	
	$('#deselect-all').click(function(e) {
			// Iterate each checkbox
			$(':checkbox').each(function() {
				this.checked = false;
			});
	});

	$(".sdpvs_catselect").submit(function(e) {
		$("#sdpvs_loading").show();
		$(".sdpvs_preview").attr('disabled', true);
		
		// Serialize the form data
		var sdpvs_checkboxdata = $(this).serialize();

		var data = {
			action : "sdpvs_select_cats",
			whichcats : sdpvs_checkboxdata,
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
			$('#sdpvs_ajax_lists').html(response);
			$("#sdpvs_loading").hide();
			$(".sdpvs_preview").attr('disabled', false);
		});
		return false;
	});
	
	$(".sdpvs_tagselect").submit(function(e) {
		$("#sdpvs_loading").show();
		$(".sdpvs_preview").attr('disabled', true);

		// Serialize the form data
		var sdpvs_checkboxdata = $(this).serialize();

		var data = {
			action : "sdpvs_select_tags",
			whichtags : sdpvs_checkboxdata,
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
			$('#sdpvs_ajax_lists').html(response);
			$("#sdpvs_loading").hide();
			$(".sdpvs_preview").attr('disabled', false);
		});
		return false;
	});
	
	
	$(".sdpvs_customselect").submit(function(e) {
		$("#sdpvs_loading").show();
		$(".sdpvs_preview").attr('disabled', true);

		// Serialize the form data
		var sdpvs_checkboxdata = $(this).serialize();

		var data = {
			action : "sdpvs_select_custom",
			whichcustom : sdpvs_checkboxdata,
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
			$('#sdpvs_ajax_lists').html(response);
			$("#sdpvs_loading").hide();
			$(".sdpvs_preview").attr('disabled', false);
		});
		return false;
	});
	
	

	$(".sdpvs_form").submit(function(e) {
		$("#sdpvs_loading").show();
		$(".sdpvs_load_content").attr('disabled', true);

		// Serialize the form data
		var sdpvs_buttondata = $(this).serialize();

		var data = {
			action : "sdpvs_get_results",
			whichdata : sdpvs_buttondata,
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
			$('#sdpvs_listcontent').html(response);
			$("#sdpvs_listcontent").show();
			$("#sdpvs_loading").hide();
			$(".sdpvs_load_content").attr('disabled', false);
		});
		return false;
	});
	
	$(".sdpvs_compare").submit(function(e) {
		$("#sdpvs_loading").show();
		$(".sdpvs_load_content").attr('disabled', true);

		// Serialize the form data
		var sdpvs_buttondata = $(this).serialize();

		var data = {
			action : "sdpvs_compare_years",
			comparedata : sdpvs_buttondata,
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
			$('#sdpvs_listcompare').html(response);
			$("#sdpvs_listcompare").show();
			$("#sdpvs_loading").hide();
			$(".sdpvs_load_content").attr('disabled', false);
		});
		return false;
	});
	
	$(document).mouseup(function(e) {
		var container = $("#sdpvs_listcontent");

		// if the target of the click isn't the container nor a descendant of the container
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.hide();
		}
	});
	
	$(document).mouseup(function(e) {
		var container = $("#sdpvs_listcompare");

		// if the target of the click isn't the container nor a descendant of the container
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.hide();
		}
	});

	// Simple way to make the box draggable using jQuery UI...
	$(function() {
		$("#sdpvs_listcontent").draggable();
	});

	

});
