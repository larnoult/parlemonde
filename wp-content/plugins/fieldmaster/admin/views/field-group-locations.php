<?php 

// vars
$rule_types = apply_filters('fieldmaster/location/rule_types', array(
	__("Post",'fieldmaster') => array(
		'post_type'		=>	__("Post Type",'fieldmaster'),
		'post_template'	=>	__("Post Template",'fieldmaster'),
		'post_status'	=>	__("Post Status",'fieldmaster'),
		'post_format'	=>	__("Post Format",'fieldmaster'),
		'post_category'	=>	__("Post Category",'fieldmaster'),
		'post_taxonomy'	=>	__("Post Taxonomy",'fieldmaster'),
		'post'			=>	__("Post",'fieldmaster')
	),
	__("Page",'fieldmaster') => array(
		'page_template'	=>	__("Page Template",'fieldmaster'),
		'page_type'		=>	__("Page Type",'fieldmaster'),
		'page_parent'	=>	__("Page Parent",'fieldmaster'),
		'page'			=>	__("Page",'fieldmaster')
	),
	__("User",'fieldmaster') => array(
		'current_user'		=>	__("Current User",'fieldmaster'),
		'current_user_role'	=>	__("Current User Role",'fieldmaster'),
		'user_form'			=>	__("User Form",'fieldmaster'),
		'user_role'			=>	__("User Role",'fieldmaster')
	),
	__("Forms",'fieldmaster') => array(
		'attachment'	=>	__("Attachment",'fieldmaster'),
		'taxonomy'		=>	__("Taxonomy Term",'fieldmaster'),
		'comment'		=>	__("Comment",'fieldmaster'),
		'widget'		=>	__("Widget",'fieldmaster')
	)
));


// WP < 4.7
if( fieldmaster_version_compare('wp', '<', '4.7') ) {
	
	unset( $rule_types[ __("Post",'fieldmaster') ]['post_template'] );
	
}

$rule_operators = apply_filters( 'fieldmaster/location/rule_operators', array(
	'=='	=>	__("is equal to",'fieldmaster'),
	'!='	=>	__("is not equal to",'fieldmaster'),
));
						
?>
<div class="fm-field">
	<div class="fieldmaster-label">
		<label><?php _e("Rules",'fieldmaster'); ?></label>
		<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these advanced custom fields",'fieldmaster'); ?></p>
	</div>
	<div class="fieldmaster-input">
		<div class="rule-groups">
			
			<?php foreach( $field_group['location'] as $group_id => $group ): 
				
				// validate
				if( empty($group) ) {
				
					continue;
					
				}
				
				
				// $group_id must be completely different to $rule_id to avoid JS issues
				$group_id = "group_{$group_id}";
				$h4 = ($group_id == "group_0") ? __("Show this field group if",'fieldmaster') : __("or",'fieldmaster');
				
				?>
			
				<div class="rule-group" data-id="<?php echo $group_id; ?>">
				
					<h4><?php echo $h4; ?></h4>
					
					<table class="fieldmaster-table -clear">
						<tbody>
							<?php foreach( $group as $rule_id => $rule ): 
								
								// valid rule
								$rule = wp_parse_args( $rule, array(
									'field'		=>	'',
									'operator'	=>	'==',
									'value'		=>	'',
								));
								
															
								// $group_id must be completely different to $rule_id to avoid JS issues
								$rule_id = "rule_{$rule_id}";
								
								?>
								<tr data-id="<?php echo $rule_id; ?>">
								<td class="param"><?php 
									
									// create field
									fieldmaster_render_field(array(
										'type'		=> 'select',
										'prefix'	=> "fieldmaster_field_group[location][{$group_id}][{$rule_id}]",
										'name'		=> 'param',
										'value'		=> $rule['param'],
										'choices'	=> $rule_types,
										'class'		=> 'location-rule-param'
									));
		
								?></td>
								<td class="operator"><?php 	
									
									// create field
									fieldmaster_render_field(array(
										'type'		=> 'select',
										'prefix'	=> "fieldmaster_field_group[location][{$group_id}][{$rule_id}]",
										'name'		=> 'operator',
										'value'		=> $rule['operator'],
										'choices' 	=> $rule_operators,
										'class'		=> 'location-rule-operator'
									)); 	
									
								?></td>
								<td class="value"><?php 
									
									$this->render_location_value(array(
										'group_id'	=> $group_id,
										'rule_id'	=> $rule_id,
										'value'		=> $rule['value'],
										'param'		=> $rule['param'],
										'class'		=> 'location-rule-value'
									)); 
									
								?></td>
								<td class="add">
									<a href="#" class="button add-location-rule"><?php _e("and",'fieldmaster'); ?></a>
								</td>
								<td class="remove">
									<a href="#" class="fieldmaster-icon -minus remove-location-rule"></a>
								</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
				</div>
			<?php endforeach; ?>
			
			<h4><?php _e("or",'fieldmaster'); ?></h4>
			
			<a href="#" class="button add-location-group"><?php _e("Add rule group",'fieldmaster'); ?></a>
			
		</div>
	</div>
</div>
<script type="text/javascript">
if( typeof fieldmaster !== 'undefined' ) {
		
	fieldmaster.postbox.render({
		'id': 'fm-field-group-locations',
		'label': 'left'
	});	

}
</script>