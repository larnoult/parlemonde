<?php 

// vars
$field = false;
$i = 0;


// extract args
extract( $args );


// add prefix
$field['prefix'] = "fieldmaster_fields[{$field['ID']}]";


// vars
$atts = array(
	'class' => "fm-field-object fm-field-object-{$field['type']}",
	'data-id'	=> $field['ID'],
	'data-key'	=> $field['key'],
	'data-type'	=> $field['type'],
);

$meta = array(
	'ID'			=> $field['ID'],
	'key'			=> $field['key'],
	'parent'		=> $field['parent'],
	'menu_order'	=> $field['menu_order'],
	'save'			=> '',
);


// replace
$atts['class'] = str_replace('_', '-', $atts['class']);

?>
<div <?php echo fieldmaster_esc_attr( $atts ); ?>>
	
	<div class="meta">
		<?php foreach( $meta as $k => $v ):
			
			fieldmaster_hidden_input(array( 'class' => "input-{$k}", 'name' => "{$field['prefix']}[{$k}]", 'value' => $v ));
				
		endforeach; ?>
	</div>
	
	<div class="handle">
		<ul class="fieldmaster-hl fieldmaster-tbody">
			<li class="li-field-order">
				<span class="fieldmaster-icon fieldmaster-sortable-handle" title="<?php _e('Drag to reorder','fieldmaster'); ?>"><?php echo ($i + 1); ?></span>
				<pre class="pre-field-key"><?php echo $field['key']; ?></pre>
			</li>
			<li class="li-field-label">
				<strong>
					<a class="edit-field" title="<?php _e("Edit field",'fieldmaster'); ?>" href="#"><?php echo fieldmaster_get_field_label($field); ?></a>
				</strong>
				<div class="row-options">
					<a class="edit-field" title="<?php _e("Edit field",'fieldmaster'); ?>" href="#"><?php _e("Edit",'fieldmaster'); ?></a>
					<a class="duplicate-field" title="<?php _e("Duplicate field",'fieldmaster'); ?>" href="#"><?php _e("Duplicate",'fieldmaster'); ?></a>
					<a class="move-field" title="<?php _e("Move field to another group",'fieldmaster'); ?>" href="#"><?php _e("Move",'fieldmaster'); ?></a>
					<a class="delete-field" title="<?php _e("Delete field",'fieldmaster'); ?>" href="#"><?php _e("Delete",'fieldmaster'); ?></a>
				</div>
			</li>
			<li class="li-field-name"><?php echo $field['name']; ?></li>
			<li class="li-field-type">
				<?php if( fieldmaster_field_type_exists($field['type']) ): ?>
					<?php echo fieldmaster_get_field_type_label($field['type']); ?>
				<?php else: ?>
					<b><?php _e('Error', 'fieldmaster'); ?></b> <?php _e('Field type does not exist', 'fieldmaster'); ?>
				<?php endif; ?>
			</li>	
		</ul>
	</div>
	
	<div class="settings">			
		<table class="fieldmaster-table">
			<tbody>
				<?php 
				
				// label
				fieldmaster_render_field_setting($field, array(
					'label'			=> __('Field Label','fieldmaster'),
					'instructions'	=> __('This is the name which will appear on the EDIT page','fieldmaster'),
					'name'			=> 'label',
					'type'			=> 'text',
					'required'		=> 1,
					'class'			=> 'field-label'
				), true);
				
				
				// name
				fieldmaster_render_field_setting($field, array(
					'label'			=> __('Field Name','fieldmaster'),
					'instructions'	=> __('Single word, no spaces. Underscores and dashes allowed','fieldmaster'),
					'name'			=> 'name',
					'type'			=> 'text',
					'required'		=> 1,
					'class'			=> 'field-name'
				), true);
				
				
				// type
				fieldmaster_render_field_setting($field, array(
					'label'			=> __('Field Type','fieldmaster'),
					'instructions'	=> '',
					'required'		=> 1,
					'type'			=> 'select',
					'name'			=> 'type',
					'choices' 		=> fieldmaster_get_grouped_field_types(),
					'class'			=> 'field-type'
				), true);
				
				
				// instructions
				fieldmaster_render_field_setting($field, array(
					'label'			=> __('Instructions','fieldmaster'),
					'instructions'	=> __('Instructions for authors. Shown when submitting data','fieldmaster'),
					'type'			=> 'textarea',
					'name'			=> 'instructions',
					'rows'			=> 5
				), true);
				
				
				// required
				fieldmaster_render_field_setting($field, array(
					'label'			=> __('Required?','fieldmaster'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'required',
					'ui'			=> 1,
					'class'			=> 'field-required'
				), true);
				
				
				// 3rd party settings
				do_action('fieldmaster/render_field_settings', $field);
				
				
				// type specific settings
				do_action("fieldmaster/render_field_settings/type={$field['type']}", $field);
				
				
				// conditional logic
				fieldmaster_get_view('field-group-field-conditional-logic', array( 'field' => $field ));
				
				
				// wrapper
				fieldmaster_render_field_wrap(array(
					'label'			=> __('Wrapper Attributes','fieldmaster'),
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'width',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['width'],
					'prepend'		=> __('width', 'fieldmaster'),
					'append'		=> '%',
					'wrapper'		=> array(
						'data-name' => 'wrapper',
						'class' => 'fm-field-setting-wrapper'
					)
				), 'tr');
				
				fieldmaster_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'class',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['class'],
					'prepend'		=> __('class', 'fieldmaster'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				fieldmaster_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'id',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['id'],
					'prepend'		=> __('id', 'fieldmaster'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				?>
				<tr class="fm-field fm-field-save">
					<td class="fieldmaster-label"></td>
					<td class="fieldmaster-input">
						<ul class="fieldmaster-hl">
							<li>
								<a class="button edit-field" title="<?php _e("Close Field",'fieldmaster'); ?>" href="#"><?php _e("Close Field",'fieldmaster'); ?></a>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div>