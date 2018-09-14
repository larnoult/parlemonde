// JavaScript Document
jQuery(document).ready(function($) {
	
	tinymce.PluginManager.add( 'columnShortcodes', function( editor, url ) {
		
		// Register button
		editor.addButton( 'columnShortcodes', {
			
			icon: 'schedule',
			tooltip: 'Column Shortcodes',
			onclick: columnShortcodeWindow
		});
		
		
		
		// Create function for window manager
		function columnShortcodeWindow() {
			
			editor.windowManager.open({
					
				title: 'Column Shortcodes',
				width: 900,
				height: 400,
				url: url+'/columnShortcodes.htm'
			})
		}
	});
});