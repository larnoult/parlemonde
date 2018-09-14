jQuery(document).ready(function($) {
	
	
	/*
	****************************************************************
	Color picker for input fields
	****************************************************************
	*/
	$('.color_field').wpColorPicker();
	
	/*
	****************************************************************
	Sortable buttons
	****************************************************************
	*/
	// Buttons draggable/sortable
	$("#toolbar1, #toolbar2, #tmce_container").sortable({
		revert: true,
		tolerance: 'pointer',
		cursor: 'move',
		opacity: 0.6,
		distance: 10,
		placeholder: "sortable_placeholder",
		connectWith: "#toolbar1, #toolbar2, #tmce_container",
		cursorAt: {
            right: 40,
            bottom: 40
        },
		helper: function(event, item) {
			
			if(!item.hasClass("ui-state-active-button")) {
				item.addClass("ui-state-active-button").siblings().removeClass("ui-state-active-button");
			}
			var helper = $('<div id="sortable_multiselect_container"><div class="sortable_multiselect"></div></div>');
			var selected = item.parent().parent().children().children(".ui-state-active-button");
			var cloned = selected.clone();
			helper.find("div.sortable_multiselect").append(cloned);
			selected.hide();
			
			item.data("multi-sortable", cloned);
			
			return helper;
		},
		start: function() {
		},
		stop: function(e, ui) {
			
			var cloned = ui.item.data("multi-sortable");
			ui.item.removeData("multi-sortable");
			
			ui.item.after(cloned);
			ui.item.parent().parent().children().children(":hidden").remove();
			ui.item.remove();
			
			// Get each sorted string
			sorted_tmce1 = $( "#toolbar1" ).sortable( "toArray" );
			sorted_tmce2 = $( "#toolbar2" ).sortable( "toArray" );
			sorted_tmce_cont = $( "#tmce_container" ).sortable( "toArray" );
			
			// Build huge string of all buttons and containers
			sorted_final = '*toolbar1:'+sorted_tmce1+'*toolbar2:'+sorted_tmce2+'*tmce_container:'+sorted_tmce_cont;
			
			// Populate hidden input field
			$('.get_sorted_array').val(sorted_final);
			
			// Remove active state from selection
			$(this).parent().children().children().removeClass("ui-state-active-button");
		}
	});
	
	// Populate hidden array div with default values
	sorted_tmce1 = $( "#toolbar1" ).sortable( "toArray" );
	sorted_tmce2 = $( "#toolbar2" ).sortable( "toArray" );
	sorted_tmce_cont = $( "#tmce_container" ).sortable( "toArray" );
	// Build huge string of all buttons and containers
	sorted_final = '*toolbar1:'+sorted_tmce1+'*toolbar2:'+sorted_tmce2+'*tmce_container:'+sorted_tmce_cont;
	// Populate hidden input field
	$('.get_sorted_array').val(sorted_final);
	
	// Toggle button active class
	$(document).on('click', '.draggable', function() {
		
		$(this).toggleClass('ui-state-active-button');
	});
	
	// Document click function to remove active button selection
	$(document).click(function(e) {
		
		if(!$(e.target).parents().hasClass('wpep_act_button_area')) {
			
			$('#inside_button_hover').children('div').children('div').removeClass('ui-state-active-button');
		}
	});
	
	
	// Button tooltips
	$( ".sortable" ).tooltip({
		items: "div.draggable",
		hide: false
	});
	
	// Get tooltip data from html data attribute
	$(document).on('mouseover', 'div.draggable', function() {
		
		get_data = $(this).context.getAttribute('data-tooltip');
		$( ".sortable" ).tooltip( "option", "content", get_data );
	});
	
	
	
	// Reset Buttons
	$('.reset_dd_buttons').click(function() {
		
		OkDialog('Reset Buttons?', 'Clicking "Okay" will reset all buttons to their original default values.', '.wpep_reset_buttons');
	});
	
	// Buttons help tabs
	$('#button_help_tabs').tabs();
	
	
	// Reset plugin options
	$('.reset_db_values_confirm').click(function() {
		
		OkDialog('Reset Plugin Options?', 'Clicking "Okay" will reset all plugin options to their original default values.', '.reset_db_values');
	});
	
	
	/*
	****************************************************************
	jQuery modal dialog functions
	****************************************************************
	*/
	function OkDialog(title, message, action) {
		
		var $dialog = $('<div title="'+title+'"></div>').html(message).dialog({
			
			modal: true,
			width: 600,
			height: 200,
			closeOnEscape: true,
			buttons: {
				
				Ok: function() {
					
					$(action).click();
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
		$dialog.dialog("open");
	}
	
	
	/*
	****************************************************************
	Get HTML version
	****************************************************************
	*/
	function getHtmlVer(){
		
		var CName  = navigator.appCodeName;
		var UAgent = navigator.userAgent;
		var HtmlVer= 0.0;
		
		// Remove start of string in UAgent upto CName or end of string if not found.
		UAgent = UAgent.substring((UAgent+CName).toLowerCase().indexOf(CName.toLowerCase()));
		
		// Remove CName from start of string. (Eg. '/5.0 (Windows; U...)
		UAgent = UAgent.substring(CName.length);
		
		// Remove any spaves or '/' from start of string.
		while(UAgent.substring(0,1)==" " || UAgent.substring(0,1)=="/") {
			UAgent = UAgent.substring(1);
		}
		
		// Remove the end of the string from first characrer that is not a number or point etc.
		var pointer = 0;
		while("0123456789.+-".indexOf((UAgent+"?").substring(pointer,pointer+1))>=0) {
			pointer = pointer+1;
		}
		UAgent = UAgent.substring(0,pointer);
		
		if(!isNaN(UAgent)) {
			if(UAgent>0) {
				HtmlVer=UAgent;
			}
		}
		return HtmlVer;
	}
	
	// Add html version to 'About' tab
	$('.wpep_html_version').html(getHtmlVer());
});