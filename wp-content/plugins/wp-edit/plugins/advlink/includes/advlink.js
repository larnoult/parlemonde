/**
 *
 *
 * @author Josh Lobe
 * http://wpeditpro.com
 */
 
jQuery(document).ready(function($) {
	
	// Declare window variable
	var this_advlink_window = top.tinymce.activeEditor;
	
	// Check if node is an a element.. and if so, populate any exisiting elements into popup window
	// EDIT MODE
	get_nodename = this_advlink_window.selection.getNode().nodeName;
	if(get_nodename == 'A' || get_nodename == 'a') {
		
		// Get active node
		get_node = this_advlink_window.selection.getNode();
		// jQuery-ify it
		jq_node = $(get_node);
		// Extract attributes
		jq_link = jq_node.attr('href');
		jq_title = jq_node.attr('title');
		jq_id = jq_node.attr('id');
		jq_classes = jq_node.attr('class');
		jq_style = jq_node.attr('style');
		jq_target = jq_node.attr('target');
		jq_nofollow = jq_node.attr('rel');
		
		// Populate attributes
		if(jq_link != 'undefined') {
			$('#advlink_link').val(jq_link);
		}
		if(jq_title != 'undefined') {
			$('#advlink_title').val(jq_title);
		}
		if(jq_id != 'undefined') {
			$('#advlink_id').val(jq_id);
		}
		if(jq_classes != 'undefined') {
			$('#advlink_classes').val(jq_classes);
		}
		if(jq_style != 'undefined') {
			$('#advlink_style').val(jq_style);
		}
		if(jq_target != 'undefined') {
			$('#advlink_target').val(jq_target);
		}
		if(jq_nofollow == 'nofollow') {
			$('#advlink_nofollow').prop('checked', true);
			$('#advlink_nofollow_label').html('On');
		}
	}
	
	
	// Action buttons
	$('#advlink_cancel').click(function() {
		
		this_advlink_window.windowManager.close();
	});
	$('#advlink_insert').click(function() {
		
		// Get values from window
		this_link = $('#advlink_link').val();
		this_title = $('#advlink_title').val();
		this_id = $('#advlink_id').val();
		this_classes = $('#advlink_classes').val();
		this_style = $('#advlink_style').val();
		this_target = $('#advlink_target').val();
		
		// Get checkbox values
		this_nofollow = $('#advlink_nofollow').is(':checked');
		
		// Get active selection
		var get_selection = this_advlink_window.selection.getContent({format : 'text'});
		
		// Add appropriate options if user selected
		if(this_link == '' && this_title == '' && this_id == '' && this_classes == '' && this_style == '' && this_target == 'select' && this_nofollow == false) {
			alert('Nothing has been changed, so nothing will be modified in the content editor.');
			return false;
		}
		
		
		// Start link building
		final_link = '<a';
		
		// Check link url
		if(this_link != '') {
			final_link += ' href="'+this_link+'"';
		}
		// Check Title
		if(this_title != '') {
			final_link += ' title="'+this_title+'"';
		}
		// Check ID
		if(this_id != '') {
			final_link += ' id="'+this_id+'"';
		}
		// Check Classes
		if(this_classes != '') {
			final_link += ' class="'+this_classes+'"';
		}
		// Check Style
		if(this_style != '') {
			final_link += ' style="'+this_style+'"';
		}
		// Check target
		if(this_target != 'select') {
			final_link += ' target="'+this_target+'"';
		}
		// Check NoFollow
		if(this_nofollow == true) {
			final_link += ' rel="nofollow"';
		}
		
		// Add closing tag
		final_link += '>';
		
		
		// If selection is empty, we have to get node inner content to pass back to editor
		if(get_selection == '') {
			// Get node html
			get_innerhtml = this_advlink_window.selection.getNode().innerHTML;
			
			orig_node = this_advlink_window.selection.getNode();
			this_advlink_window.dom.remove(orig_node);
		}
		// Else get active selection
		else {
			get_innerhtml = get_selection;
		}
		// Add html to final link
		final_link += get_innerhtml;
		
		// Build link closing tag
		final_link += '</a>';
	
		// Insert content into editor
		this_advlink_window.execCommand('mceInsertContent', !1, final_link); 
		// Close window
		this_advlink_window.windowManager.close(); 
	});
	
	
	// Style checkboxes with jquery UI button
	//$( "#advlink_nofollow" ).button();
	
	// Adjust button text based on click state
	$( "#advlink_nofollow" ).click(function() {
		
		isset_advlink = $(this).is(':checked');
		if(isset_advlink == true) {
			$('#advlink_nofollow_label').html('On');
		}
		else {
			$('#advlink_nofollow_label').html('Off');
		}
	});
	
});