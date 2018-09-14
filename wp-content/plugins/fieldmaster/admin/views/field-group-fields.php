<?php 

// vars
$fields = false;
$parent = 0;


// use fields if passed in
extract( $args );

?>
<div class="fm-field-list-wrap">
	
	<ul class="fieldmaster-hl fieldmaster-thead">
		<li class="li-field-order"><?php _e('Order','fieldmaster'); ?></li>
		<li class="li-field-label"><?php _e('Label','fieldmaster'); ?></li>
		<li class="li-field-name"><?php _e('Name','fieldmaster'); ?></li>
		<li class="li-field-type"><?php _e('Type','fieldmaster'); ?></li>
	</ul>
	
	<div class="fm-field-list">
		
		<div class="no-fields-message" <?php if( $fields ){ echo 'style="display:none;"'; } ?>>
			<?php _e("No fields. Click the <strong>+ Add Field</strong> button to create your first field.",'fieldmaster'); ?>
		</div>
		
		<?php if( $fields ):
			
			foreach( $fields as $i => $field ):
				
				fieldmaster_get_view('field-group-field', array( 'field' => $field, 'i' => $i ));
				
			endforeach;
		
		endif; ?>
		
	</div>
	
	<ul class="fieldmaster-hl fieldmaster-tfoot">
		<li class="fieldmaster-fr">
			<a href="#" class="button button-primary button-large add-field"><?php _e('+ Add Field','fieldmaster'); ?></a>
		</li>
	</ul>
	
<?php if( !$parent ):
	
	// get clone
	$clone = fieldmaster_get_valid_field(array(
		'ID'		=> 'fieldmastercloneindex',
		'key'		=> 'fieldmastercloneindex',
		'label'		=> __('New Field','fieldmaster'),
		'name'		=> 'new_field',
		'type'		=> 'text'
	));
	
	?>
	<script type="text/html" id="tmpl-fm-field">
	<?php fieldmaster_get_view('field-group-field', array( 'field' => $clone )); ?>
	</script>
<?php endif;?>
	
</div>