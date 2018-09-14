/**
 *
 *
 * @author Josh Lobe
 * http://ultimatetinymcepro.com
 */

jQuery(document).ready(function($) {

	tinymce.PluginManager.add('acheck', function(editor, url) {
      

		// Register clker button
		editor.addButton('acheck', {
			
			image : url + '/img/acheck.png',
			tooltip : 'Accessibility Checker',
			onclick : acheck
		});
		  
		  
		// Run windowmanager function
		function acheck() {
			
			get_content = tinyMCE.activeEditor.getContent({format : 'raw'});
			
			var theCode = '<html><body onLoad="document.accessform.submit();"> \n';
			theCode += '<h1>Submitting Code for Accessibility Checking.....</h1>\n';
			theCode += '<form action="http://achecker.ca/checker/index.php" name="accessform" method="post"> \n';
			theCode += '<input type="hidden" name="gid[]" value="8" /> \n';
			theCode += '<textarea name="validate_content">' + get_content + '</textarea>\n';
			theCode += '<input type="submit" /></form> \n';  
			theCode += '</body></html> \n';
			accessWin = window.open('', 'accessWin',  '');
			accessWin.document.writeln(theCode);
			accessWin.document.close();
		}
	
	});
});