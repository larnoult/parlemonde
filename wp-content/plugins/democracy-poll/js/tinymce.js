jQuery(document).ready(function($){
	tinymce.PluginManager.add('demTiny', function(editor) {

		editor.addCommand('demTinyInsert', function() {
			var pID = $.trim( prompt(tinymce.translate('Insert Poll ID')) );
			while( isNaN( pID ) ){
				pID = $.trim( prompt( tinymce.translate('Error: ID is a integer. Enter ID again, please.') ));
			}
			if( pID >= -1 && pID != null && pID != "" ){
				editor.insertContent('[democracy id="' + pID + '"]');
			}
		});

		editor.addButton('demTiny', {
			text: false,
			tooltip: tinymce.translate('Insert Poll of Democracy'),
			icon: 'dem dashicons-before dashicons-megaphone',
			onclick: function(){
				tinyMCE.activeEditor.execCommand('demTinyInsert')
			}
		});
	});
});
