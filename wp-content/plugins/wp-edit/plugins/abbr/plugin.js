/**
 *
 *
 * @author Josh Lobe
 * https://wpeditpro.com
 */
 
jQuery(document).ready(function($) {
	
	tinymce.PluginManager.add('abbr', function(ed, url) {
		
		ed.addButton('abbr', {
			
			image: url + '/abbr.png',
			tooltip: 'Abbreviation',
			onclick: abbr
		});
		
		function abbr() {
				
			// Set variables
			var get_sel_content = tinymce.activeEditor.selection.getContent();  // Get selected content
			var node = tinymce.activeEditor.selection.getNode();  // Get active node
			var node_content = '';
			var node_title = '';
			
			// If node is an abbr node... get it's innerHTML and title
			if(node.nodeName === 'ABBR') {
				
				node_content = node.innerHTML;  // abbreviation
				node_title = node.title;  // title
				
				ed.windowManager.open({
				
					title: 'Edit Abbreviation',
					body: [{
						
						type: 'textbox',
						name: 'abbr_title',
						size: 40,
						label: 'ABBR Title',
						value: node_title
					},{
						
						type: 'textbox',
						name: 'abbr',
						size: 40,
						label: 'Abbreviation',
						value: node_content
					}],
					onsubmit: function(t) { 
						
						// Clear current abbr node
						tinymce.activeEditor.selection.getNode().remove();
						ed.execCommand('mceInsertContent', !1, '<abbr title="'+t.data.abbr_title+'">'+t.data.abbr+'</abbr>');
					}
				})
			}
			// Else this is NOT an abbr node
			else {
			
				ed.windowManager.open({
					
					title: 'Abbreviation',
					body: [{
						
						type: 'textbox',
						name: 'abbr_selection',
						label: 'Selection',
						size: 40,
						value: get_sel_content
					},{
						
						type: 'textbox',
						name: 'abbr',
						size: 40,
						label: 'Abbreviation',
						value: node_content
					}],
					onsubmit: function(t) { 
					
						ed.execCommand('mceInsertContent', !1, '<abbr title="'+t.data.abbr_selection+'">'+t.data.abbr+'</abbr>');
					}
				})
			}
		}
	});
});