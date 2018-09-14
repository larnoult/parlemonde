<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fieldmaster_json') ) :

class fieldmaster_json {
	
	function __construct() {
		
		// update setting
		fieldmaster_update_setting('save_json', get_stylesheet_directory() . '/fieldmaster-json');
		fieldmaster_append_setting('load_json', get_stylesheet_directory() . '/fieldmaster-json');
		
		
		// actions
		add_action('fieldmaster/update_field_group',		array($this, 'update_field_group'), 10, 1);
		add_action('fieldmaster/duplicate_field_group',		array($this, 'update_field_group'), 10, 1);
		add_action('fieldmaster/untrash_field_group',		array($this, 'update_field_group'), 10, 1);
		add_action('fieldmaster/trash_field_group',			array($this, 'delete_field_group'), 10, 1);
		add_action('fieldmaster/delete_field_group',		array($this, 'delete_field_group'), 10, 1);
		add_action('fieldmaster/include_fields', 			array($this, 'include_json_folders'), 10, 0);
		
	}
	
	
	/*
	*  update_field_group
	*
	*  This function is hooked into the fieldmaster/update_field_group action and will save all field group data to a .json file 
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$field_group (array)
	*  @return	n/a
	*/
	
	function update_field_group( $field_group ) {
		
		// validate
		if( !fieldmaster_get_setting('json') ) return;
		
		
		// get fields
		$field_group['fields'] = fieldmaster_get_fields( $field_group );
		
		
		// save file
		fieldmaster_write_json_field_group( $field_group );
			
	}
	
	
	/*
	*  delete_field_group
	*
	*  This function will remove the field group .json file
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$field_group (array)
	*  @return	n/a
	*/
	
	function delete_field_group( $field_group ) {
		
		// validate
		if( !fieldmaster_get_setting('json') ) return;
		
		
		// WP appends '__trashed' to end of 'key' (post_name) 
		$field_group['key'] = str_replace('__trashed', '', $field_group['key']);
		
		
		// delete
		fieldmaster_delete_json_field_group( $field_group['key'] );
		
	}
		
	
	/*
	*  include_json_folders
	*
	*  This function will include all registered .json files
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function include_json_folders() {
		
		// validate
		if( !fieldmaster_get_setting('json') ) return;
		
		
		// vars
		$paths = fieldmaster_get_setting('load_json');
		
		
		// loop through and add to cache
		foreach( $paths as $path ) {
			
			$this->include_json_folder( $path );
		    
		}
		
	}
	
	
	/*
	*  include_json_folder
	*
	*  This function will include all .json files within a folder
	*
	*  @type	function
	*  @date	1/5/17
	*  @since	5.5.13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function include_json_folder( $path = '' ) {
		
		// remove trailing slash
		$path = untrailingslashit( $path );
		
		
		// bail early if path does not exist
		if( !is_dir($path) ) return false;
		
		
		// open
		$dir = opendir( $path );
    
		
		// loop over files
	    while(false !== ( $file = readdir($dir)) ) {
	    	
	    	// validate type
			if( pathinfo($file, PATHINFO_EXTENSION) !== 'json' ) continue;
	    	
	    	
	    	// read json
	    	$json = file_get_contents("{$path}/{$file}");
	    	
	    	
	    	// validate json
	    	if( empty($json) ) continue;
	    	
	    	
	    	// decode
	    	$json = json_decode($json, true);
	    	
	    	
	    	// add local
	    	$json['local'] = 'json';
	    	
	    	
	    	// add field group
	    	fieldmaster_add_local_field_group( $json );
	        
	    }
	    
	    
	    // return
	    return true;
	    
	}
	
}


// initialize
fieldmaster()->json = new fieldmaster_json();

endif; // class_exists check


/*
*  fieldmaster_write_json_field_group
*
*  This function will save a field group to a json file within the current theme
*
*  @type	function
*  @date	5/12/2014
*  @since	5.1.5
*
*  @param	$field_group (array)
*  @return	(boolean)
*/

function fieldmaster_write_json_field_group( $field_group ) {
	
	// vars
	$path = fieldmaster_get_setting('save_json');
	$file = $field_group['key'] . '.json';
	
	
	// remove trailing slash
	$path = untrailingslashit( $path );
	
	
	// bail early if dir does not exist
	if( !is_writable($path) ) return false;
	
	
	// prepare for export
	$id = fieldmaster_extract_var( $field_group, 'ID' );
	$field_group = fieldmaster_prepare_field_group_for_export( $field_group );
	

	// add modified time
	$field_group['modified'] = get_post_modified_time('U', true, $id, true);
	
	
	// write file
	$f = fopen("{$path}/{$file}", 'w');
	fwrite($f, fieldmaster_json_encode( $field_group ));
	fclose($f);
	
	
	// return
	return true;
	
}


/*
*  fieldmaster_delete_json_field_group
*
*  This function will delete a json field group file
*
*  @type	function
*  @date	5/12/2014
*  @since	5.1.5
*
*  @param	$key (string)
*  @return	(boolean)
*/

function fieldmaster_delete_json_field_group( $key ) {
	
	// vars
	$path = fieldmaster_get_setting('save_json');
	$file = $key . '.json';
	
	
	// remove trailing slash
	$path = untrailingslashit( $path );
	
	
	// bail early if file does not exist
	if( !is_readable("{$path}/{$file}") ) {
	
		return false;
		
	}
	
		
	// remove file
	unlink("{$path}/{$file}");
	
	
	// return
	return true;
	
}


?>