<?php

// active
fieldmaster_render_field_wrap(array(
	'label'			=> __('Active','fieldmaster'),
	'instructions'	=> '',
	'type'			=> 'true_false',
	'name'			=> 'active',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['active'],
	'ui'			=> 1,
	//'ui_on_text'	=> __('Active', 'fieldmaster'),
	//'ui_off_text'	=> __('Inactive', 'fieldmaster'),
));


// style
fieldmaster_render_field_wrap(array(
	'label'			=> __('Style','fieldmaster'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'style',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['style'],
	'choices' 		=> array(
		'default'			=>	__("Standard (WP metabox)",'fieldmaster'),
		'seamless'			=>	__("Seamless (no metabox)",'fieldmaster'),
	)
));


// position
fieldmaster_render_field_wrap(array(
	'label'			=> __('Position','fieldmaster'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'position',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['position'],
	'choices' 		=> array(
		'fieldmaster_after_title'	=> __("High (after title)",'fieldmaster'),
		'normal'			=> __("Normal (after content)",'fieldmaster'),
		'side' 				=> __("Side",'fieldmaster'),
	),
	'default_value'	=> 'normal'
));


// label_placement
fieldmaster_render_field_wrap(array(
	'label'			=> __('Label placement','fieldmaster'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'label_placement',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['label_placement'],
	'choices' 		=> array(
		'top'			=>	__("Top aligned",'fieldmaster'),
		'left'			=>	__("Left Aligned",'fieldmaster'),
	)
));


// instruction_placement
fieldmaster_render_field_wrap(array(
	'label'			=> __('Instruction placement','fieldmaster'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'instruction_placement',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['instruction_placement'],
	'choices' 		=> array(
		'label'		=>	__("Below labels",'fieldmaster'),
		'field'		=>	__("Below fields",'fieldmaster'),
	)
));


// menu_order
fieldmaster_render_field_wrap(array(
	'label'			=> __('Order No.','fieldmaster'),
	'instructions'	=> __('Field groups with a lower order will appear first','fieldmaster'),
	'type'			=> 'number',
	'name'			=> 'menu_order',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['menu_order'],
));


// description
fieldmaster_render_field_wrap(array(
	'label'			=> __('Description','fieldmaster'),
	'instructions'	=> __('Shown in field group list','fieldmaster'),
	'type'			=> 'text',
	'name'			=> 'description',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['description'],
));


// hide on screen
fieldmaster_render_field_wrap(array(
	'label'			=> __('Hide on screen','fieldmaster'),
	'instructions'	=> __('<b>Select</b> items to <b>hide</b> them from the edit screen.','fieldmaster') . '<br /><br />' . __("If multiple field groups appear on an edit screen, the first field group's options will be used (the one with the lowest order number)",'fieldmaster'),
	'type'			=> 'checkbox',
	'name'			=> 'hide_on_screen',
	'prefix'		=> 'fieldmaster_field_group',
	'value'			=> $field_group['hide_on_screen'],
	'toggle'		=> true,
	'choices' => array(
		'permalink'			=>	__("Permalink", 'fieldmaster'),
		'the_content'		=>	__("Content Editor",'fieldmaster'),
		'excerpt'			=>	__("Excerpt", 'fieldmaster'),
		'custom_fields'		=>	__("FieldMaster", 'fieldmaster'),
		'discussion'		=>	__("Discussion", 'fieldmaster'),
		'comments'			=>	__("Comments", 'fieldmaster'),
		'revisions'			=>	__("Revisions", 'fieldmaster'),
		'slug'				=>	__("Slug", 'fieldmaster'),
		'author'			=>	__("Author", 'fieldmaster'),
		'format'			=>	__("Format", 'fieldmaster'),
		'page_attributes'	=>	__("Page Attributes", 'fieldmaster'),
		'featured_image'	=>	__("Featured Image", 'fieldmaster'),
		'categories'		=>	__("Categories", 'fieldmaster'),
		'tags'				=>	__("Tags", 'fieldmaster'),
		'send-trackbacks'	=>	__("Send Trackbacks", 'fieldmaster'),
	)
));


// 3rd party settings
do_action('fieldmaster/render_field_group_settings', $field_group);
		
?>
<div class="fieldmaster-hidden">
	<input type="hidden" name="fieldmaster_field_group[key]" value="<?php echo $field_group['key']; ?>" />
</div>
<script type="text/javascript">
if( typeof fieldmaster !== 'undefined' ) {
		
	fieldmaster.postbox.render({
		'id': 'fm-field-group-options',
		'label': 'left'
	});	

}
</script>