<?php 

// vars
$field = array(
	'label'		=> __('Select Field Groups', 'fieldmaster'),
	'type'		=> 'checkbox',
	'name'		=> 'fieldmaster_export_keys',
	'prefix'	=> false,
	'value'		=> false,
	'toggle'	=> true,
	'choices'	=> array(),
);

$field_groups = fieldmaster_get_field_groups();


// populate choices
if( $field_groups ) {
	
	foreach( $field_groups as $field_group ) {
		
		$field['choices'][ $field_group['key'] ] = $field_group['title'];
		
	}
	
}

?>
<div class="wrap fieldmaster-settings-wrap">
	
	<h1><?php _e('Tools', 'fieldmaster'); ?></h1>
	
	<div class="fieldmaster-box" id="fieldmaster-export-field-groups">
		<div class="title">
			<h3><?php _e('Export Field Groups', 'fieldmaster'); ?></h3>
		</div>
		<div class="inner">
			<p><?php _e('Select the field groups you would like to export and then select your export method. Use the download button to export to a .json file which you can then import to another FieldMaster installation. Use the generate button to export to PHP code which you can place in your theme.', 'fieldmaster'); ?></p>
			
			<form method="post" action="">
			<div class="fieldmaster-hidden">
				<input type="hidden" name="_fieldmasternonce" value="<?php echo wp_create_nonce( 'export' ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
	                <?php fieldmaster_render_field_wrap( $field, 'tr' ); ?>
					<tr>
						<th></th>
						<td>
							<input type="submit" name="download" class="button button-primary" value="<?php _e('Download export file', 'fieldmaster'); ?>" />
							<input type="submit" name="generate" class="button button-primary" value="<?php _e('Generate export code', 'fieldmaster'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
            
		</div>
	</div>

	
	<div class="fieldmaster-box">
		<div class="title">
			<h3><?php _e('Import Field Groups', 'fieldmaster'); ?></h3>
		</div>
		<div class="inner">
			<p><?php _e('Select the FieldMaster JSON file you would like to import. When you click the import button below, FieldMaster will import the field groups.', 'fieldmaster'); ?></p>
			
			<form method="post" action="" enctype="multipart/form-data">
			<div class="fieldmaster-hidden">
				<input type="hidden" name="_fieldmasternonce" value="<?php echo wp_create_nonce( 'import' ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label><?php _e('Select File', 'fieldmaster'); ?></label>
                    	</th>
						<td>
							<input type="file" name="fieldmaster_import_file">
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="submit" class="button button-primary" value="<?php _e('Import', 'fieldmaster'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			
		</div>
		
		
	</div>
	
</div>