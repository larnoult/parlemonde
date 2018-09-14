<?php

/*
*  FieldMaster 3rd Party Compatibility Class
*
*  All the logic for 3rd party functionality
*
*  @class 		fieldmaster_third_party
*  @package		FieldMaster
*  @subpackage	Core
*/

if( ! class_exists('fieldmaster_third_party') ) :

class fieldmaster_third_party {
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// Tabify Edit Screen - http://wordpress.org/extend/plugins/tabify-edit-screen/
		add_action('admin_head-settings_page_tabify-edit-screen', array($this, 'admin_head_tabify'));
		
		
		// Post Type Switcher - http://wordpress.org/extend/plugins/post-type-switcher/
		add_filter('pts_allowed_pages', array($this, 'pts_allowed_pages'));
		
	}
	
	
	/*
	*  admin_head_tabify
	*
	*  description
	*
	*  @type	action (admin_head)
	*  @date	9/10/12
	*  @since	3.5.1
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_head_tabify() {
		
		// remove FieldMaster from the tabs
		add_filter('tabify_posttypes',			array($this, 'tabify_posttypes'));
		
		
		// add fieldmaster metaboxes to list
		add_action('tabify_add_meta_boxes',		array($this, 'tabify_add_meta_boxes'));
		
	}
	
	
	/*
	*  tabify_posttypes
	*
	*  This function removes FieldMaster post types from the tabify edit screen (post type selection sidebar)
	*
	*  @type	function
	*  @date	9/10/12
	*  @since	3.5.1
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function tabify_posttypes( $posttypes ) {
		
		// unset
		unset( $posttypes['fm-field-group'] );
		unset( $posttypes['fm-field'] );
		
		
		// return
		return $posttypes;
	}
	
	
	/*
	*  tabify_add_meta_boxes
	*
	*  This function creates dummy metaboxes on the tabify edit screen page
	*
	*  @type	function
	*  @date	9/10/12
	*  @since	3.5.1
	*
	*  @param	$post_type (string)
	*  @return	n/a
	*/
	
	function tabify_add_meta_boxes( $post_type ) {
		
		// get field groups
		$field_groups = fieldmaster_get_field_groups();
		
		
		if( !empty($field_groups) ) {
			
			foreach( $field_groups as $field_group ) {
				
				// vars
				$id = "fieldmaster-{$field_group['key']}";
				$title = 'FieldMaster: ' . $field_group['title'];

				
				
				// add meta box
				add_meta_box( $id, $title, '__return_true', $post_type );
				
			}
			
		}
		
	}
	
	
	/*
	*  pts_allowed_pages
	*
	*  This filter will prevent PTS from running on the field group page!
	*
	*  @type	function
	*  @date	25/09/2014
	*  @since	5.0.0
	*
	*  @param	$pages (array)
	*  @return	$pages
	*/
	
	function pts_allowed_pages( $pages ) {
		
		// vars
		$post_type = '';
		
		
		// check $_GET becuase it is too early to use functions / global vars
		if( !empty($_GET['post_type']) ) {
			
			$post_type = $_GET['post_type'];
			
		} elseif( !empty($_GET['post']) ) {
			
			$post_type = get_post_type( $_GET['post'] );
			
		}
				
		
		// check post type
		if( $post_type == 'fm-field-group' ) {
			
			$pages = array();
			
		}
		
		
		// return
		return $pages;
		
	}	
	
}

new fieldmaster_third_party();

endif;

?>