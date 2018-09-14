<?php

/*
*  FieldMaster Image Field Class
*
*  All the logic for this field type
*
*  @class 		fieldmaster_field_image
*  @extends		fieldmaster_field
*  @package		FieldMaster
*  @subpackage	Fields
*/

if( ! class_exists('fieldmaster_field_image') ) :

class fieldmaster_field_image extends fieldmaster_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->name = 'image';
		$this->label = __("Image",'fieldmaster');
		$this->category = 'content';
		$this->defaults = array(
			'return_format'	=> 'array',
			'preview_size'	=> 'thumbnail',
			'library'		=> 'all',
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> ''
		);
		$this->l10n = array(
			'select'		=> __("Select Image",'fieldmaster'),
			'edit'			=> __("Edit Image",'fieldmaster'),
			'update'		=> __("Update Image",'fieldmaster'),
			'uploadedTo'	=> __("Uploaded to this post",'fieldmaster'),
			'all'			=> __("All images",'fieldmaster'),
		);
		
		
		// filters
		add_filter('get_media_item_args',				array($this, 'get_media_item_args'));
		add_filter('wp_prepare_attachment_for_js',		array($this, 'wp_prepare_attachment_for_js'), 10, 3);
		
		
		// do not delete!
    	parent::__construct();
    
    }
    
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$uploader = fieldmaster_get_setting('uploader');
		
		
		// enqueue
		if( $uploader == 'wp' ) {
			
			fieldmaster_enqueue_uploader();
			
		}
		
		
		// vars
		$url = '';
		$alt = '';
		$div = array(
			'class'					=> 'fieldmaster-image-uploader fieldmaster-cf',
			'data-preview_size'		=> $field['preview_size'],
			'data-library'			=> $field['library'],
			'data-mime_types'		=> $field['mime_types'],
			'data-uploader'			=> $uploader
		);
		
		
		// has value?
		if( $field['value'] ) {
			
			// update vars
			$url = wp_get_attachment_image_src($field['value'], $field['preview_size']);
			$alt = get_post_meta($field['value'], '_wp_attachment_image_alt', true);
			
			
			// url exists
			if( $url ) $url = $url[0];
			
			
			// url exists
			if( $url ) {
				
				$div['class'] .= ' has-value';
			
			}
						
		}
		
		
		// get size of preview value
		$size = fieldmaster_get_image_size($field['preview_size']);
		
?>
<div <?php fieldmaster_esc_attr_e( $div ); ?>>
	<?php fieldmaster_hidden_input(array( 'name' => $field['name'], 'value' => $field['value'] )); ?>
	<div class="view show-if-value fieldmaster-soh" <?php if( $size['width'] ) echo 'style="max-width: '.$size['width'].'px"'; ?>>
		<img data-name="image" src="<?php echo $url; ?>" alt="<?php echo $alt; ?>"/>
		<ul class="fieldmaster-hl fieldmaster-soh-target">
			<?php if( $uploader != 'basic' ): ?>
				<li><a class="fieldmaster-icon -pencil dark" data-name="edit" href="#" title="<?php _e('Edit', 'fieldmaster'); ?>"></a></li>
			<?php endif; ?>
			<li><a class="fieldmaster-icon -cancel dark" data-name="remove" href="#" title="<?php _e('Remove', 'fieldmaster'); ?>"></a></li>
		</ul>
	</div>
	<div class="view hide-if-value">
		<?php if( $uploader == 'basic' ): ?>
			
			<?php if( $field['value'] && !is_numeric($field['value']) ): ?>
				<div class="fieldmaster-error-message"><p><?php echo $field['value']; ?></p></div>
			<?php endif; ?>
			
			<label class="fieldmaster-basic-uploader">
				<input type="file" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" />
			</label>
			
		<?php else: ?>
			
			<p style="margin:0;"><?php _e('No image selected','fieldmaster'); ?> <a data-name="add" class="fieldmaster-button button" href="#"><?php _e('Add Image','fieldmaster'); ?></a></p>
			
		<?php endif; ?>
	</div>
</div>
<?php
		
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// clear numeric settings
		$clear = array(
			'min_width',
			'min_height',
			'min_size',
			'max_width',
			'max_height',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) {
				
				$field[$k] = '';
				
			}
			
		}
		
		
		// return_format
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Return Value','fieldmaster'),
			'instructions'	=> __('Specify the returned value on front end','fieldmaster'),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'array'			=> __("Image Array",'fieldmaster'),
				'url'			=> __("Image URL",'fieldmaster'),
				'id'			=> __("Image ID",'fieldmaster')
			)
		));
		
		
		// preview_size
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Preview Size','fieldmaster'),
			'instructions'	=> __('Shown when entering data','fieldmaster'),
			'type'			=> 'select',
			'name'			=> 'preview_size',
			'choices'		=> fieldmaster_get_image_sizes()
		));
		
		
		// library
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Library','fieldmaster'),
			'instructions'	=> __('Limit the media library choice','fieldmaster'),
			'type'			=> 'radio',
			'name'			=> 'library',
			'layout'		=> 'horizontal',
			'choices' 		=> array(
				'all'			=> __('All', 'fieldmaster'),
				'uploadedTo'	=> __('Uploaded to post', 'fieldmaster')
			)
		));
		
		
		// min
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Minimum','fieldmaster'),
			'instructions'	=> __('Restrict which images can be uploaded','fieldmaster'),
			'type'			=> 'text',
			'name'			=> 'min_width',
			'prepend'		=> __('Width', 'fieldmaster'),
			'append'		=> 'px',
		));
		
		fieldmaster_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_height',
			'prepend'		=> __('Height', 'fieldmaster'),
			'append'		=> 'px',
			'_append' 		=> 'min_width'
		));
		
		fieldmaster_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_size',
			'prepend'		=> __('File size', 'fieldmaster'),
			'append'		=> 'MB',
			'_append' 		=> 'min_width'
		));	
		
		
		// max
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Maximum','fieldmaster'),
			'instructions'	=> __('Restrict which images can be uploaded','fieldmaster'),
			'type'			=> 'text',
			'name'			=> 'max_width',
			'prepend'		=> __('Width', 'fieldmaster'),
			'append'		=> 'px',
		));
		
		fieldmaster_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_height',
			'prepend'		=> __('Height', 'fieldmaster'),
			'append'		=> 'px',
			'_append' 		=> 'max_width'
		));
		
		fieldmaster_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_size',
			'prepend'		=> __('File size', 'fieldmaster'),
			'append'		=> 'MB',
			'_append' 		=> 'max_width'
		));	
		
		
		// allowed type
		fieldmaster_render_field_setting( $field, array(
			'label'			=> __('Allowed file types','fieldmaster'),
			'instructions'	=> __('Comma separated list. Leave blank for all types','fieldmaster'),
			'type'			=> 'text',
			'name'			=> 'mime_types',
		));
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) return false;
		
		
		// bail early if not numeric (error message)
		if( !is_numeric($value) ) return false;
		
		
		// convert to int
		$value = intval($value);
		
		
		// format
		if( $field['return_format'] == 'url' ) {
		
			return wp_get_attachment_url( $value );
			
		} elseif( $field['return_format'] == 'array' ) {
			
			return fieldmaster_get_attachment( $value );
			
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  get_media_item_args
	*
	*  description
	*
	*  @type	function
	*  @date	27/01/13
	*  @since	3.6.0
	*
	*  @param	$vars (array)
	*  @return	$vars
	*/
	
	function get_media_item_args( $vars ) {
	
	    $vars['send'] = true;
	    return($vars);
	    
	}
		
	
	/*
	*  wp_prepare_attachment_for_js
	*
	*  this filter allows FieldMaster to add in extra data to an attachment JS object
	*  This sneaky hook adds the missing sizes to each attachment in the 3.5 uploader. 
	*  It would be a lot easier to add all the sizes to the 'image_size_names_choose' filter but 
	*  then it will show up on the normal the_content editor
	*
	*  @type	function
	*  @since:	3.5.7
	*  @date	13/01/13
	*
	*  @param	{int}	$post_id
	*  @return	{int}	$post_id
	*/
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
		
		// only for image
		if( $response['type'] != 'image' ) {
		
			return $response;
			
		}
		
		
		// make sure sizes exist. Perhaps they dont?
		if( !isset($meta['sizes']) ) {
		
			return $response;
			
		}
		
		
		$attachment_url = $response['url'];
		$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
		
		if( isset($meta['sizes']) && is_array($meta['sizes']) ) {
		
			foreach( $meta['sizes'] as $k => $v ) {
			
				if( !isset($response['sizes'][ $k ]) ) {
				
					$response['sizes'][ $k ] = array(
						'height'      => $v['height'],
						'width'       => $v['width'],
						'url'         => $base_url .  $v['file'],
						'orientation' => $v['height'] > $v['width'] ? 'portrait' : 'landscape',
					);
				}
				
			}
			
		}

		return $response;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		return fieldmaster_get_field_type('file')->update_value( $value, $post_id, $field );
		
	}
	
	
	/*
	*  validate_value
	*
	*  This function will validate a basic file input
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		return fieldmaster_get_field_type('file')->validate_value( $valid, $value, $field, $input );
		
	}
	
}


// initialize
fieldmaster_register_field_type( new fieldmaster_field_image() );

endif; // class_exists check

?>