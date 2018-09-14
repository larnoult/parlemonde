<?php

/*
*  fieldmaster_is_field_key
*
*  This function will return true or false for the given $field_key parameter
*
*  @type	function
*  @date	6/12/2013
*  @since	5.0.0
*
*  @param	$field_key (string)
*  @return	(boolean)
*/

function fieldmaster_is_field_key( $key = '' ) {
	
	// bail early if not string
	if( !is_string($key) ) return false;
	
	
	// bail early if is numeric (could be numeric string '123')
	if( is_numeric($key) ) return false;
	
	
	// default - starts with 'field_'
	if( substr($key, 0, 6) === 'field_' ) return true;
	
	
	// special - allow local field key to be any string
	if( fieldmaster_is_local_field_key($key) ) return true;
	
	
	// return
	return false;
	
}


/*
*  fieldmaster_get_valid_field
*
*  This function will fill in any missing keys to the $field array making it valid
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$field (array)
*  @return	$field (array)
*/

function fieldmaster_get_valid_field( $field = false ) {
	
	// $field must be an array
	if( !is_array($field) ) $field = array();
	
	
	// bail ealry if already valid
	if( !empty($field['_valid']) ) return $field;
	
	
	// defaults
	$field = wp_parse_args($field, array(
		'ID'				=> 0,
		'key'				=> '',
		'label'				=> '',
		'name'				=> '',
		'prefix'			=> '',
		'type'				=> 'text',
		'value'				=> null,
		'menu_order'		=> 0,
		'instructions'		=> '',
		'required'			=> 0,
		'id'				=> '',
		'class'				=> '',
		'conditional_logic'	=> 0,
		'parent'			=> 0,
		'wrapper'			=> array(),
		'_name'				=> '',
		'_prepare'			=> 0,
		'_valid'			=> 0,
	));
	
	$field['wrapper'] = wp_parse_args($field['wrapper'], array(
		'width'				=> '',
		'class'				=> '',
		'id'				=> ''
	));
	
	
	// _name
	$field['_name'] = $field['name'];
	
	
	// field is now valid
	$field['_valid'] = 1;
	
	
	// field specific defaults
	$field = apply_filters( "fieldmaster/validate_field", $field );
	$field = apply_filters( "fieldmaster/validate_field/type={$field['type']}", $field );
	
	
	// translate
	$field = fieldmaster_translate_field( $field );
	
	
	// return
	return $field;
	
}


/*
*  fieldmaster_translate_field
*
*  This function will translate field's settings
*
*  @type	function
*  @date	8/03/2016
*  @since	5.3.2
*
*  @param	$field (array)
*  @return	$field
*/

function fieldmaster_translate_field( $field ) {
	
	// vars
	$l10n = fieldmaster_get_setting('l10n');
	$l10n_textdomain = fieldmaster_get_setting('l10n_textdomain');
	
	
	// if
	if( $l10n && $l10n_textdomain ) {
		
		// translate
		$field['label'] = fieldmaster_translate( $field['label'] );
		$field['instructions'] = fieldmaster_translate( $field['instructions'] );
		
		
		// filters
		$field = apply_filters( "fieldmaster/translate_field", $field );
		$field = apply_filters( "fieldmaster/translate_field/type={$field['type']}", $field );
		
	}
	
	
	// return
	return $field;
	
}


/*
*  fieldmaster_clone_field
*
*  This function will allow customization to a field when it is cloned
*  Cloning a field is the act of mimicing another. Some settings may need to be altered
*
*  @type	function
*  @date	8/03/2016
*  @since	5.3.2
*
*  @param	$field (array)
*  @return	$field
*/

function fieldmaster_clone_field( $field, $clone_field ) {
	
	// add reference
	$field['_clone'] = $clone_field['key'];
	
	
	// filters
	$field = apply_filters( "fieldmaster/clone_field", $field, $clone_field );
	$field = apply_filters( "fieldmaster/clone_field/type={$field['type']}", $field, $clone_field );
	
	
	// return
	return $field;
	
}


/*
*  fieldmaster_prepare_field
*
*  This function will prepare the field for input
*
*  @type	function
*  @date	12/02/2014
*  @since	5.0.0
*
*  @param	$field (array)
*  @return	$field (array)
*/

function fieldmaster_prepare_field( $field ) {
	
	// bail early if already prepared
	if( $field['_prepare'] ) return $field;
	
	
	// key overrides name
	if( $field['key'] ) $field['name'] = $field['key'];

	
	// prefix
	if( $field['prefix'] ) $field['name'] = $field['prefix'] . '[' . $field['name'] . ']';
	
	
	// field is now prepared
	$field['_prepare'] = 1;
	
	
	// filter to 3rd party customization
	$field = apply_filters( "fieldmaster/prepare_field", $field );
	$field = apply_filters( "fieldmaster/prepare_field/type={$field['type']}", $field );
	$field = apply_filters( "fieldmaster/prepare_field/name={$field['_name']}", $field );
	$field = apply_filters( "fieldmaster/prepare_field/key={$field['key']}", $field );
	
	
	// bail ealry if no field
	if( !$field ) return false;
	
	
	// id attr is generated from name
	$field['id'] = str_replace(array('][', '[', ']'), array('-', '-', ''), $field['name']);
	
	
	// return
	return $field;
	
}


/*
*  fieldmaster_is_sub_field
*
*  This function will return true if the field is a sub field
*
*  @type	function
*  @date	17/05/2014
*  @since	5.0.0
*
*  @param	$field (array)
*  @return	(boolean)
*/

function fieldmaster_is_sub_field( $field ) {
	
	// local field uses a field instead of ID
	if( fieldmaster_is_field_key($field['parent']) ) return true;
	
	
	// attempt to load parent field
	if( fieldmaster_get_field($field['parent']) ) return true;
	
	
	// return
	return false;
	
}


/*
*  fieldmaster_get_field_label
*
*  This function will return the field label with appropriate required label
*
*  @type	function
*  @date	4/11/2013
*  @since	5.0.0
*
*  @param	$field (array)
*  @return	$label (string)
*/

function fieldmaster_get_field_label( $field ) {
	
	// vars
	$label = $field['label'];
	
	
	if( $field['required'] ) {
		
		$label .= ' <span class="fieldmaster-required">*</span>';
		
	}
	
	
	// filter for 3rd party customization
	$label = apply_filters("fieldmaster/get_field_label", $label, $field);
	
	
	// return
	return $label;

}

function fieldmaster_the_field_label( $field ) {
	
	echo fieldmaster_get_field_label( $field );
	
}


/*
*  fieldmaster_render_fields
*
*  This function will render an array of fields for a given form.
*  Becasue the $field's values have not been loaded yet, this function will also load values
*
*  @type	function
*  @date	8/10/13
*  @since	5.0.0
*
*  @param	$post_id (int) the post to load values from
*  @param	$fields (array) the fields to render
*  @param	$el (string) the wrapping element type
*  @param	$instruction (int) the instructions position
*  @return	n/a
*/

function fieldmaster_render_fields( $post_id = 0, $fields, $el = 'div', $instruction = 'label' ) {
	
	// bail early if no fields
	if( empty($fields) ) return false;
	
		
	// remove corrupt fields
	$fields = array_filter($fields);
	
	
	// loop through fields
	foreach( $fields as $field ) {
		
		// load value
		if( $field['value'] === null ) {
			
			$field['value'] = fieldmaster_get_value( $post_id, $field );
			
		} 
		
		
		// render
		fieldmaster_render_field_wrap( $field, $el, $instruction );
		
	}
	
}


/*
*  fieldmaster_render_field
*
*  This function will render a field input
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$field (array)
*  @return	n/a
*/

function fieldmaster_render_field( $field = false ) {
	
	// get valid field
	$field = fieldmaster_get_valid_field( $field );
	
	
	// prepare field for input
	$field = fieldmaster_prepare_field( $field );
	
	
	// bail ealry if no field
	if( !$field ) return;
		
	
	// create field specific html
	do_action( "fieldmaster/render_field", $field );
	do_action( "fieldmaster/render_field/type={$field['type']}", $field );
	
}


/*
*  fieldmaster_render_field_wrap
*
*  This function will render the complete HTML wrap with label & field
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$field (array) must be a valid FieldMaster field array
*  @param	$el (string) modifys the rendered wrapping elements. Default to 'div', but can be 'tr', 'ul', 'ol', 'dt' or custom
*  @param	$instruction (string) specifys the placement of the instructions. Default to 'label', but can be 'field'
*  @param	$atts (array) an array of custom attributes to render on the $el
*  @return	N/A
*/

function fieldmaster_render_field_wrap( $field, $el = 'div', $instruction = 'label' ) {
	
	// get valid field
	$field = fieldmaster_get_valid_field( $field );
	
	
	// prepare field for input
	$field = fieldmaster_prepare_field( $field );
	
	
	// bail ealry if no field
	if( !$field ) return;
	
	
	// el
	$elements = apply_filters('fieldmaster/render_field_wrap/elements', array(
		'div'	=> 'div',
		'tr'	=> 'td',
		'ul'	=> 'li',
		'ol'	=> 'li',
		'dl'	=> 'dt',
		'td'	=> 'div' // special case for sub field!
	));
	
	
	// validate $el
	if( !array_key_exists($el, $elements) ) {
		
		$el = 'div';
	
	}
	
	
	// wrapper
	$wrapper = array(
		'id'		=> '',
		'class'		=> 'fm-field',
		'width'		=> '',
		'style'		=> '',
		'data-name'	=> $field['_name'],
		'data-type'	=> $field['type'],
		'data-key'	=> '',
	);
	
	
	// add required
	if( $field['required'] ) {
		
		$wrapper['data-required'] = 1;
		
	}
	
	
	// add type
	$wrapper['class'] .= " fm-field-{$field['type']}";
	
	
	// add key
	if( $field['key'] ) {
		
		$wrapper['class'] .= " fm-field-{$field['key']}";
		$wrapper['data-key'] = $field['key'];
		
	}
	
	
	// replace
	$wrapper['class'] = str_replace('_', '-', $wrapper['class']);
	$wrapper['class'] = str_replace('field-field-', 'field-', $wrapper['class']);
	
	
	// wrap classes have changed (5.2.7)
	if( fieldmaster_get_compatibility('field_wrapper_class') ) {
		
		$wrapper['class'] .= " field_type-{$field['type']}";
		
		if( $field['key'] ) {
			
			$wrapper['class'] .= " field_key-{$field['key']}";
			
		}
		
	}
	
	
	// merge in atts
	$wrapper = fieldmaster_merge_atts( $wrapper, $field['wrapper'] );
	
	
	// add width
	$width = (int) fieldmaster_extract_var( $wrapper, 'width' );
	
	if( $el == 'tr' || $el == 'td' ) {
		
		$width = 0;
		
	} elseif( $width > 0 && $width < 100 ) {
		
		$wrapper['data-width'] = $width;
		$wrapper['style'] .= " width:{$width}%;";
		
	}
	
	
	// remove empty attributes
	foreach( $wrapper as $k => $v ) {
		
		if( $v == '' ) {
			
			unset($wrapper[$k]);
			
		}
		
	}
	
	
	// vars
	$show_label = ($el !== 'td') ? true : false;
	
	
?><<?php echo $el; ?> <?php echo fieldmaster_esc_attr($wrapper); ?>>
<?php if( $show_label ): ?>
	<<?php echo $elements[ $el ]; ?> class="fieldmaster-label">
		<label for="<?php echo $field['id']; ?>"><?php echo fieldmaster_get_field_label($field); ?></label>
<?php if( $instruction == 'label' && $field['instructions'] ): ?>
		<p class="description"><?php echo $field['instructions']; ?></p>
<?php endif; ?>
	</<?php echo $elements[ $el ]; ?>>
<?php endif; ?>
	<<?php echo $elements[ $el ]; ?> class="fieldmaster-input">
		<?php fieldmaster_render_field( $field ); ?>
<?php if( $instruction == 'field' && $field['instructions'] ): ?>
		<p class="description"><?php echo $field['instructions']; ?></p>
<?php endif; ?>
	</<?php echo $elements[ $el ]; ?>>
<?php if( !empty($field['conditional_logic'])): ?>
	<script type="text/javascript">
		if(typeof fieldmaster !== 'undefined'){ fieldmaster.conditional_logic.add( '<?php echo $field['key']; ?>', <?php echo json_encode($field['conditional_logic']); ?>); }
	</script>
<?php endif; ?>
</<?php echo $el; ?>>
<?php
	
}


/*
*  fieldmaster_render_field_setting
*
*  This function will render a tr element containing a label and field cell, but also setting the tr data attribute for AJAX 
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$field (array) the origional field being edited
*  @param	$setting (array) the settings field to create
*  @return	n/a
*/

function fieldmaster_render_field_setting( $field, $setting, $global = false ) {
	
	// validate
	$setting = fieldmaster_get_valid_field( $setting );
	
	
	// specific
	if( !$global ) {
		
		$setting['wrapper']['data-setting'] = $field['type'];
		
	}
	
	
	// class
	$setting['wrapper']['class'] .= ' fm-field-setting-' . $setting['name'];
	
	
	// copy across prefix
	$setting['prefix'] = $field['prefix'];
		
	
	// attempt find value
	if( $setting['value'] === null ) {
		
		// name
		if( isset($field[ $setting['name'] ]) ) {
			
			$setting['value'] = $field[ $setting['name'] ];
		
		// default
		} elseif( isset($setting['default_value']) ) {
			
			$setting['value'] = $setting['default_value'];
			
		}
		
	}
	
	
	// append (used by JS to join settings)
	if( isset($setting['_append']) ) {
		
		$setting['wrapper']['data-append'] = $setting['_append'];
		
	}
	
	
	// render
	fieldmaster_render_field_wrap( $setting, 'tr', 'label' );
	
}


/*
*  fieldmaster_get_fields
*
*  This function will return an array of fields for the given $parent
*
*  @type	function
*  @date	30/09/13
*  @since	5.0.0
*
*  @param	$parent (array) a field or field group
*  @return	(array)
*/

function fieldmaster_get_fields( $parent = false ) {
	
	// allow $parent to be a field group ID
	if( !is_array($parent) ) {
		
		$parent = fieldmaster_get_field_group( $parent );
	
	}
	
	
	// bail early if no parent
	if( !$parent ) return false;
	
	
	// vars
	$fields = array();
	
	
	// try JSON before DB to save query time
	if( fieldmaster_have_local_fields( $parent['key'] ) ) {
		
		$fields = fieldmaster_get_local_fields( $parent['key'] );
		
	} else {
		
		$fields = fieldmaster_get_fields_by_id( $parent['ID'] );
	
	}
	
	
	// filter
	$fields = apply_filters('fieldmaster/get_fields', $fields, $parent);
	
	
	// return
	return $fields;
	
}


/*
*  fieldmaster_get_fields_by_id
*
*  This function will get all fields for the given parent
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$fields (array)
*/

function fieldmaster_get_fields_by_id( $parent_id = 0 ) {
	
	// bail early if no ID
	if( !$parent_id ) return false;
	
	
	// vars
	$fields = array();
	$post_ids = array();
	$cache_key = "get_fields/ID={$parent_id}";
	
	
	// check cache for child ids
	if( fieldmaster_isset_cache($cache_key) ) {
		
		$post_ids = fieldmaster_get_cache($cache_key);
	
	// query DB for child ids
	} else {
		
		// query
		$posts = get_posts(array(
			'posts_per_page'			=> -1,
			'post_type'					=> 'fm-field',
			'orderby'					=> 'menu_order',
			'order'						=> 'ASC',
			'suppress_filters'			=> true, // DO NOT allow WPML to modify the query
			'post_parent'				=> $parent_id,
			'post_status'				=> 'publish, trash', // 'any' won't get trashed fields
			'update_post_meta_cache'	=> false
		));
		
		
		// loop
		if( $posts ) {
			
			foreach( $posts as $post ) {
				
				$post_ids[] = $post->ID;
				
			}
				
		}
		
		
		// update cache
		fieldmaster_set_cache($cache_key, $post_ids);
		
	}
	
	
	// bail early if no children
	if( empty($post_ids) ) return false;
	
	
	// load fields
	foreach( $post_ids as $post_id ) {
		
		$fields[] = fieldmaster_get_field( $post_id );
		
	}
	
	
	// return
	return $fields;
	
}


/*
*  fieldmaster_get_field
*
*  This function will return a field for the given selector. 
*
*  @type	function
*  @date	30/09/13
*  @since	5.0.0
*
*  @param	$selector (mixed) identifyer of field. Can be an ID, key, name or post object
*  @param	$db_only (boolean) return $field in it's raw form without filters or cache
*  @return	$field (array)
*/

function fieldmaster_get_field( $selector = null, $db_only = false ) {
	
	// vars
	$field = false;
	$type = 'ID';
	
	
	// ID
	if( is_numeric($selector) ) {
		
		// do nothing
	
	// object
	} elseif( is_object($selector) ) {
		
		$selector = $selector->ID;
	
	// string
	} elseif( is_string($selector) ) {
		
		$type = fieldmaster_is_field_key($selector) ? 'key' : 'name';
	
	// other
	} else {
		
		return false;
		
	}
	
	
	// return early if cache is found
	$cache_key = "get_field/{$type}={$selector}";
	
	if( !$db_only && fieldmaster_isset_cache($cache_key) ) {
		
		return fieldmaster_get_cache($cache_key);
		
	}
	
	
	// ID
	if( $type == 'ID' ) {
		
		$field = _fieldmaster_get_field_by_id( $selector, $db_only );
	
	// key	
	} elseif( $type == 'key' ) {
		
		$field = _fieldmaster_get_field_by_key( $selector, $db_only );
	
	// name (rare case)
	} else {
		
		$field = _fieldmaster_get_field_by_name( $selector, $db_only );
		
	}
	
	
	// bail early if no field
	if( !$field ) return false;
	
	
	// validate
	$field = fieldmaster_get_valid_field( $field );
	
	
	// set prefix (fieldmaster fields save with prefix 'fieldmaster')
	$field['prefix'] = 'fieldmaster';
	
	
	// bail early if db only value (no need to update cache)
	if( $db_only ) return $field;
	
	
	// filter for 3rd party customization
	$field = apply_filters( "fieldmaster/load_field", $field);
	$field = apply_filters( "fieldmaster/load_field/type={$field['type']}", $field );
	$field = apply_filters( "fieldmaster/load_field/name={$field['name']}", $field );
	$field = apply_filters( "fieldmaster/load_field/key={$field['key']}", $field );
	
	
	// update cache
	// - Use key instead of ID for best compatibility (not all fields exist in the DB)
	$cache_key = fieldmaster_set_cache("get_field/key={$field['key']}", $field);
	
	
	// update cache reference
	// - allow cache to return if using an ID selector
	fieldmaster_set_cache_reference("get_field/ID={$field['ID']}", $cache_key);

	
	// return
	return $field;
	
}


/*
*  _fieldmaster_get_field_by_id
*
*  This function will get a field via its ID
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$field (array)
*/

function _fieldmaster_get_field_by_id( $post_id = 0, $db_only = false ) {
	
	// get post
	$post = get_post( $post_id );
	
	
	// bail early if no post, or is not a field
	if( empty($post) || $post->post_type != 'fm-field' ) return false;
	
	
	// unserialize
	$field = maybe_unserialize( $post->post_content );
	
	
	// update attributes
	$field['ID'] = $post->ID;
	$field['key'] = $post->post_name;
	$field['label'] = $post->post_title;
	$field['name'] = $post->post_excerpt;
	$field['menu_order'] = $post->menu_order;
	$field['parent'] = $post->post_parent;


	// override with JSON
	if( !$db_only && fieldmaster_is_local_field($field['key']) ) {
		
		// load JSON field
		$local = fieldmaster_get_local_field( $field['key'] );
		
		
		// override IDs
		$local['ID'] = $field['ID'];
		$local['parent'] = $field['parent'];
		
		
		// return
		return $local;
		
	}
	
	
	// return
	return $field;
	
}


/*
*  _fieldmaster_get_field_by_key
*
*  This function will get a field via its key
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$key (string)
*  @return	$field (array)
*/

function _fieldmaster_get_field_by_key( $key = '', $db_only = false ) {
	
	// try JSON before DB to save query time
	if( !$db_only && fieldmaster_is_local_field( $key ) ) {
		
		return fieldmaster_get_local_field( $key );
		
	}
	
	
	// vars
	$post_id = fieldmaster_get_field_id( $key );
	
	
	// bail early if no post_id
	if( !$post_id ) return false;
		
	
	// return
	return _fieldmaster_get_field_by_id( $post_id, $db_only );
	
}


/*
*  _fieldmaster_get_field_by_name
*
*  This function will get a field via its name
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$key (string)
*  @return	$field (array)
*/

function _fieldmaster_get_field_by_name( $name = '', $db_only = false ) {
	
	// try JSON before DB to save query time
	if( !$db_only && fieldmaster_is_local_field( $name ) ) {
		
		return fieldmaster_get_local_field( $name );
		
	}
	
	
	// vars
	$args = array(
		'posts_per_page'	=> 1,
		'post_type'			=> 'fm-field',
		'orderby' 			=> 'menu_order title',
		'order'				=> 'ASC',
		'suppress_filters'	=> false,
		'fieldmaster_field_name'	=> $name
	);
	
	
	// load posts
	$posts = get_posts( $args );
	
	
	// bail early if no posts
	if( empty($posts) ) return false;
	
	
	// return
	return _fieldmaster_get_field_by_id( $posts[0]->ID, $db_only );
	
}


/*
*  fieldmaster_maybe_get_field
*
*  This function will return a field for the given selector.
*  It will also review the field_reference to ensure the correct field is returned which makes it useful for the template API
*
*  @type	function
*  @date	4/08/2015
*  @since	5.2.3
*
*  @param	$selector (mixed) identifyer of field. Can be an ID, key, name or post object
*  @param	$post_id (mixed) the post_id of which the value is saved against
*  @param	$strict (boolean) if true, return a field only when a field key is found.
*  @return	$field (array)
*/

function fieldmaster_maybe_get_field( $selector, $post_id = false, $strict = true ) {
	
	// init
	fieldmaster_init();
	
	
	// bail early if is field key
	if( fieldmaster_is_field_key($selector) ) {
		
		return fieldmaster_get_field( $selector );
		
	}
	
	
	// save selector as field_name (could be sub field name 'images_0_image')
	$field_name = $selector;
	
	
	// get valid post_id
	$post_id = fieldmaster_get_valid_post_id( $post_id );
	
	
	// get reference
	$field_key = fieldmaster_get_field_reference( $selector, $post_id );
	
	
	// update selector
	if( $field_key ) {
		
		$selector = $field_key;
	
	// bail early if no reference	
	} elseif( $strict ) {
		
		return false;
		
	}
	
	
	// get field
	$field = fieldmaster_get_field( $selector );
	
	
	// update name
	if( $field ) $field['name'] = $field_name;
	
	
	// return
	return $field;
	
}


/*
*  fieldmaster_get_field_id
*
*  This function will lookup a field's ID from the DB
*  Useful for local fields to find DB sibling
*
*  @type	function
*  @date	25/06/2015
*  @since	5.2.3
*
*  @param	$key (string)
*  @return	$post_id (int)
*/

function fieldmaster_get_field_id( $key = '' ) {
	
	// vars
	$args = array(
		'posts_per_page'	=> 1,
		'post_type'			=> 'fm-field',
		'orderby' 			=> 'menu_order title',
		'order'				=> 'ASC',
		'suppress_filters'	=> false,
		'fieldmaster_field_key'		=> $key
	);
	
	
	// load posts
	$posts = get_posts( $args );
	
	
	// validate
	if( empty($posts) ) return 0;
	
	
	// return
	return $posts[0]->ID;
	
}


/*
*  fieldmaster_update_field
*
*  This function will update a field into the DB.
*  The returned field will always contain an ID
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	$field (array)
*  @return	$field (array)
*/

function fieldmaster_update_field( $field = false, $specific = false ) {
	
	// $field must be an array
	if( !is_array($field) ) return false;
	
	
	// validate
	$field = fieldmaster_get_valid_field( $field );
	
	
	// may have been posted. Remove slashes
	$field = wp_unslash( $field );
	
	
	// parse types (converts string '0' to int 0)
	$field = fieldmaster_parse_types( $field );
	
	
	// clean up conditional logic keys
	if( !empty($field['conditional_logic']) ) {
		
		// extract groups
		$groups = fieldmaster_extract_var( $field, 'conditional_logic' );
		
		
		// clean array
		$groups = array_filter($groups);
		$groups = array_values($groups);
		
		
		// clean rules
		foreach( array_keys($groups) as $i ) {
			
			$groups[ $i ] = array_filter($groups[ $i ]);
			$groups[ $i ] = array_values($groups[ $i ]);
			
		}
		
		
		// reset conditional logic
		$field['conditional_logic'] = $groups;
		
	}
	
	
	// parent may be a field key
	// - lookup parent ID
	if( fieldmaster_is_field_key($field['parent']) ) {
		
		$field['parent'] = fieldmaster_get_field_id( $field['parent'] );
		
	}
	
	
	// filter for 3rd party customization
	$field = apply_filters( "fieldmaster/update_field", $field);
	$field = apply_filters( "fieldmaster/update_field/type={$field['type']}", $field );
	$field = apply_filters( "fieldmaster/update_field/name={$field['name']}", $field );
	$field = apply_filters( "fieldmaster/update_field/key={$field['key']}", $field );
	
	
	// store origional field for return
	$data = $field;
	
	
	// extract some args
	$extract = fieldmaster_extract_vars($data, array(
		'ID',
		'key',
		'label',
		'name',
		'prefix',
		'value',
		'menu_order',
		'id',
		'class',
		'parent',
		'_name',
		'_prepare',
		'_valid',
	));
	
	
	// serialize for DB
	$data = maybe_serialize( $data );
    
    
    // save
    $save = array(
    	'ID'			=> $extract['ID'],
    	'post_status'	=> 'publish',
    	'post_type'		=> 'fm-field',
    	'post_title'	=> $extract['label'],
    	'post_name'		=> $extract['key'],
    	'post_excerpt'	=> $extract['name'],
    	'post_content'	=> $data,
    	'post_parent'	=> $extract['parent'],
    	'menu_order'	=> $extract['menu_order'],
    );
    
    
    // $specific
    if( !empty($specific) ) {
	    
	    // prepend ID
    	array_unshift( $specific, 'ID' );
    	
    	
	    // vars
	    $_save = $save;
	    
	    
	    // reset
	    $save = array();
	    
    	
    	// appen data
    	foreach( $specific as $key ) {
	    	
	    	$save[ $key ] = $_save[ $key ];
	    	
    	}
    	
    }
    
    
    // allow fields to contain the same name
	add_filter( 'wp_unique_post_slug', 'fieldmaster_update_field_wp_unique_post_slug', 100, 6 ); 
	
	
    // update the field and update the ID
    if( $field['ID'] ) {
	    
	    wp_update_post( $save );
	    
    } else  {
	    
	    $field['ID'] = wp_insert_post( $save );
	    
    }
	
    
    // clear cache
    fieldmaster_delete_cache("get_field/key={$field['key']}");
	
	
    // return
    return $field;
	
}

function fieldmaster_update_field_wp_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
		
	if( $post_type == 'fm-field' ) {
	
		$slug = $original_slug;
	
	}
	
	// return
	return $slug;
	
}


/*
*  fieldmaster_duplicate_fields
*
*  This function will duplicate an array of fields and update conditional logic references
*
*  @type	function
*  @date	16/06/2014
*  @since	5.0.0
*
*  @param	$fields (array)
*  @param	$new_parent (int)
*  @return	n/a
*/

function fieldmaster_duplicate_fields( $fields, $new_parent = 0 ) {
	
	// bail early if no fields
	if( empty($fields) ) return;
	
	
	// create new field keys (for conditional logic fixes)
	foreach( $fields as $field ) {
		
		// ensure a delay for unique ID
		usleep(1);
		
		fieldmaster_update_setting( 'duplicate_key_' . $field['key'] , uniqid('field_') );
		
	}
	
	
	// duplicate fields
	foreach( $fields as $field ) {
	
		// duplicate
		fieldmaster_duplicate_field( $field['ID'], $new_parent );
		
	}
	
}


/*
*  fieldmaster_duplicate_field
*
*  This function will duplicate a field and attach it to the given field group ID
*
*  @type	function
*  @date	17/10/13
*  @since	5.0.0
*
*  @param	$selector (int)
*  @param	$new_parent (int)
*  @return	$field (array) the new field
*/

function fieldmaster_duplicate_field( $selector = 0, $new_parent = 0 ){
	
	// disable filters to ensure FieldMaster loads raw data from DB
	fieldmaster_disable_filters();
	
	
	// load the origional field
	$field = fieldmaster_get_field( $selector );
	
	
	// bail early if field did not load correctly
	if( empty($field) ) {
	
		return false;
		
	}
	
	
	// update ID
	$field['ID'] = false;
	
	
	// try duplicate keys
	$field['key'] = fieldmaster_get_setting( 'duplicate_key_' . $field['key'] );
	
	
	// default key
	if( empty($field['key']) ) {
		
		$field['key'] = uniqid('field_');
			
	}
	
	
	// update parent
	if( $new_parent ) {
	
		$field['parent'] = $new_parent;
		
	}
	
	
	// update conditional logic references (because field keys have changed)
	if( !empty($field['conditional_logic']) ) {
	
		// extract groups
		$groups = fieldmaster_extract_var( $field, 'conditional_logic' );
		
		
		// loop over groups
		foreach( array_keys($groups) as $g ) {
			
			// extract group
			$group = fieldmaster_extract_var( $groups, $g );
			
			
			// bail early if empty
			if( empty($group) ) {
				
				continue;
				
			}
			
			
			// loop over rules
			foreach( array_keys($group) as $r ) {
				
				// extract rule
				$rule = fieldmaster_extract_var( $group, $r );
				
				
				// vars
				$new_key = fieldmaster_get_setting( 'duplicate_key_' . $rule['field'] );
				
				
				// update rule with new key
				if( $new_key ) {
					
					$rule['field'] = $new_key;
					
				}
				
				
				// append to group
				$group[ $r ] = $rule;
				
			}
			
			
			// append to groups
			$groups[ $g ] = $group;
			
		}
		
		
		// update conditional logic
		$field['conditional_logic'] = $groups;
		
		
	}
	
	
	// filter for 3rd party customization
	$field = apply_filters( "fieldmaster/duplicate_field", $field);
	$field = apply_filters( "fieldmaster/duplicate_field/type={$field['type']}", $field );
	
	
	// save
	return fieldmaster_update_field( $field );
	
}


/*
*  fieldmaster_delete_field
*
*  This function will delete a field from the databse
*
*  @type	function
*  @date	2/10/13
*  @since	5.0.0
*
*  @param	$id (int)
*  @return	(boolean)
*/

function fieldmaster_delete_field( $selector = 0 ) {
	
	// disable filters to ensure FieldMaster loads raw data from DB
	fieldmaster_disable_filters();
	
	
	// load the origional field gorup
	$field = fieldmaster_get_field( $selector );
	
	
	// bail early if field did not load correctly
	if( empty($field) ) return false;
	
	
	// delete field
	wp_delete_post( $field['ID'], true );
	
	
	// action for 3rd party customisation
	do_action( "fieldmaster/delete_field", $field);
	do_action( "fieldmaster/delete_field/type={$field['type']}", $field );
	
	
	// clear cache
	fieldmaster_delete_cache("get_field/key={$field['key']}");
	
	
	// return
	return true;
	
}


/*
*  fieldmaster_trash_field
*
*  This function will trash a field from the databse
*
*  @type	function
*  @date	2/10/13
*  @since	5.0.0
*
*  @param	$id (int)
*  @return	(boolean)
*/

function fieldmaster_trash_field( $selector = 0 ) {
	
	// disable filters to ensure FieldMaster loads raw data from DB
	fieldmaster_disable_filters();
	
	
	// load the origional field gorup
	$field = fieldmaster_get_field( $selector );
	
	
	// bail early if field did not load correctly
	if( empty($field) ) return false;
	
	
	// delete field
	wp_trash_post( $field['ID'] );
	
	
	// action for 3rd party customisation
	do_action( 'fieldmaster/trash_field', $field );
	
	
	// return
	return true;
	
}


/*
*  fieldmaster_untrash_field
*
*  This function will restore a field from the trash
*
*  @type	function
*  @date	2/10/13
*  @since	5.0.0
*
*  @param	$id (int)
*  @return	(boolean)
*/

function fieldmaster_untrash_field( $selector = 0 ) {
	
	// disable filters to ensure FieldMaster loads raw data from DB
	fieldmaster_disable_filters();
	
	
	// load the origional field gorup
	$field = fieldmaster_get_field( $selector );
	
	
	// bail early if field did not load correctly
	if( empty($field) ) return false;
	
	
	// delete field
	wp_untrash_post( $field['ID'] );
	
	
	// action for 3rd party customisation
	do_action( 'fieldmaster/untrash_field', $field );
	
	
	// return
	return true;
}


/*
*  fieldmaster_prepare_fields_for_export
*
*  description
*
*  @type	function
*  @date	11/03/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fieldmaster_prepare_fields_for_export( $fields = false ) {
	
	// validate
	if( empty($fields) ) return $fields;
	
	
	// format
	foreach( array_keys($fields) as $i ) {
		
		// prepare
		$fields[ $i ] = fieldmaster_prepare_field_for_export( $fields[ $i ] );
				
	}
	
	
	// return
	return $fields;
	
}


/*
*  fieldmaster_prepare_field_for_export
*
*  description
*
*  @type	function
*  @date	11/03/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fieldmaster_prepare_field_for_export( $field ) {
	
	// extract some args
	$extract = fieldmaster_extract_vars($field, array(
		'ID',
		'prefix',
		'value',
		'menu_order',
		'id',
		'class',
		'parent',
		'_name',
		'_prepare',
		'_valid',
	));
	
	
	// filter for 3rd party customization
	$field = apply_filters( "fieldmaster/prepare_field_for_export", $field );
	$field = apply_filters( "fieldmaster/prepare_field_for_export/type={$field['type']}", $field );
	
	
	// return
	return $field;
}


/*
*  fieldmaster_prepare_fields_for_import
*
*  description
*
*  @type	function
*  @date	11/03/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fieldmaster_prepare_fields_for_import( $fields = false ) {
	
	// validate
	if( empty($fields) ) return array();
	
	
	// re-index array
	$fields = array_values($fields);
	
	
	// vars
	$i = 0;
	
	
	// format
	while( $i < count($fields) ) {
		
		// prepare field
		$field = fieldmaster_prepare_field_for_import( $fields[ $i ] );
		
		
		// allow multiple fields to be returned ($field + $sub_fields)
		if( fieldmaster_is_sequential_array($field) ) {
			
			// merge in $field (1 or more fields)
			array_splice($fields, $i, 1, $field);
			
		}
				
		
		// $i
		$i++;
		
	}
	
	
	// filter for 3rd party customization
	$fields = apply_filters('fieldmaster/prepare_fields_for_import', $fields);
	
	
	// return
	return $fields;
	
}


/*
*  fieldmaster_prepare_field_for_import
*
*  description
*
*  @type	function
*  @date	11/03/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fieldmaster_prepare_field_for_import( $field ) {
	
	// extract some args
	$extract = fieldmaster_extract_vars($field, array(
		'value',
		'id',
		'class',
		'_name',
		'_prepare',
		'_valid',
	));
	
	
	// filter for 3rd party customization
	$field = apply_filters( "fieldmaster/prepare_field_for_import", $field );
	$field = apply_filters( "fieldmaster/prepare_field_for_import/type={$field['type']}", $field );
	
	
	// return
	return $field;
}


/*
*  fieldmaster_get_sub_field
*
*  This function will return a field for the given selector, and $field (parent). 
*
*  @type	function
*  @date	30/09/13
*  @since	5.0.0
*
*  @param	$selector (string)
*  @param	$field (mixed)
*  @return	$field (array)
*/

function fieldmaster_get_sub_field( $selector, $field ) {
	
	// vars
	$sub_field = false;
	
	
	// filter for 3rd party customization
	$sub_field = apply_filters( "fieldmaster/get_sub_field", $sub_field, $selector, $field );
	$sub_field = apply_filters( "fieldmaster/get_sub_field/type={$field['type']}", $sub_field, $selector, $field );
	
	
	// return
	return $sub_field;
	
}


/*
*  fieldmaster_get_field_ancestors
*
*  This function will return an array of all ancestor fields
*
*  @type	function
*  @date	22/06/2016
*  @since	5.3.8
*
*  @param	$field (array)
*  @return	(array)
*/

function fieldmaster_get_field_ancestors( $field ) {
	
	// get field
	$ancestors = array();
	
	
	// loop
	while( $field && fieldmaster_is_field_key($field['parent']) ) {
		
		$ancestors[] = $field['parent'];
		$field = fieldmaster_get_field($field['parent']);
		
	}
	
	
	// return
	return $ancestors;
	
}


/*
*  fieldmaster_maybe_get_sub_field
*
*  This function will attempt to find a sub field
*
*  @type	function
*  @date	3/10/2016
*  @since	5.4.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function fieldmaster_maybe_get_sub_field( $selectors, $post_id = false, $strict = true ) {
	
	// bail ealry if not enough selectors
	if( !is_array($selectors) || count($selectors) < 3 ) return false;
	
	
	// vars
	$offset = fieldmaster_get_setting('row_index_offset');
	$selector = fieldmaster_extract_var( $selectors, 0 );
	$selectors = array_values( $selectors ); // reset keys
	
	
	// attempt get field
	$field = fieldmaster_maybe_get_field( $selector, $post_id, $strict );
	
	
	// bail early if no field
	if( !$field ) return false;
	
	
	// loop
	for( $j = 0; $j < count($selectors); $j+=2 ) {
		
		// vars
		$sub_i = $selectors[ $j ];
		$sub_s = $selectors[ $j+1 ];
		$field_name = $field['name'];
		
		
		// find sub field
		$field = fieldmaster_get_sub_field( $sub_s, $field );
		
		
		// bail early if no sub field
		if( !$field ) return false;
					
		
		// add to name
		$field['name'] = $field_name . '_' . ($sub_i-$offset) . '_' . $field['name'];
		
	}
	
	
	// return
	return $field;
	
	
}


?>
