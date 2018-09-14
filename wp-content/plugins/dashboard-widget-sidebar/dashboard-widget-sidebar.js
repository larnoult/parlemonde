jQuery(document).ready(function($) {
	
	function dws_change_select_value(element, value) {
		element.find("option").each(function() {
			
			if($(this).text().toLowerCase() == $.trim(value))
				$(this).attr('selected', 'selected');
			else
				$(this).removeAttr('selected');
		});
	}
	
	function dws_add_settings() {
		$('#dws-sidebar .widget').each(function() {
			var widgetContent = $(this).find('.widget-content').first();

			if(widgetContent.length > 0 ) {

				if(widgetContent.find('.dws-settings').length == 0) {
					widgetContent.append('<div class="dws-settings"><h4>Dashboard Widget Sidebar - Settings:</h4><label>Context: <select class="dws-setting dws-context"><option selected="selected">Normal</option><option>Side</option></select></label><label>Priority: <select class="dws-setting dws-priority"><option>High</option><option>Core</option><option selected="selected">Default</option><option>Low</option></select></label></div>');
					var widgetID = widgetContent.parent().find('.widget-id').val();
					
					if(dwsWidgetSettings[widgetID] != undefined) {
						//Priority
						dws_change_select_value(widgetContent.find('.dws-setting.dws-priority'), dwsWidgetSettings[widgetID][0]);
						//Context
						dws_change_select_value(widgetContent.find('.dws-setting.dws-context'), dwsWidgetSettings[widgetID][1]);
					}
				}
				
			}			
		});
	}
	
	setInterval(function(){dws_add_settings()},1000);
	
	//
	$('.dws-setting').live('change', function() {
		var widgetForm = $(this).parent().parent().parent().parent();
		var widgetID = widgetForm.find('.widget-id').val();
		
		var data = {
			action: 'dws_ajax_update',
			widget_id: widgetID,
			priority: widgetForm.find('.dws-setting.dws-priority').val(),
			context: widgetForm.find('.dws-setting.dws-context').val()
		};
		
		// Change settings in local array
		if(dwsWidgetSettings[widgetID] == undefined) {
			dwsWidgetSettings[widgetID] = new Array();
		}
		
		//Priority
		dwsWidgetSettings[widgetID][0] = widgetForm.find('.dws-setting.dws-priority').val().toLowerCase();
		//Context
		dwsWidgetSettings[widgetID][1] = widgetForm.find('.dws-setting.dws-context').val().toLowerCase();

		//Save settings
		$.post(ajaxurl, data, function(response) {
			if(response != '1')
				alert('Error: Something went wrong.');
		});
	});
});