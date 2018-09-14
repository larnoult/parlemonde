<?php

function _cme_update_pp_usage() {
	static $updated;
	if ( ! empty($updated) ) { return true; }

	if ( ! current_user_can( 'pp_manage_settings' ) )
		return false;
	
	if ( ! empty( $_REQUEST['update_filtered_types']) ) {
		// update Press Permit "Filtered Post Types".  This determines whether type-specific capability definitions are forced
		$options = array( 'enabled_post_types', 'enabled_taxonomies' );
		
		foreach( $options as $option_basename ) {
			if ( ! isset( $_POST["{$option_basename}-options"] ) )
				continue;
		
			$unselected = array();
			$value = array();
		
			foreach( $_POST["{$option_basename}-options"] as $key ) {
				if ( empty( $_POST["{$option_basename}-$key"] ) )
					$unselected[$key] = true;
				else
					$value[$key] = true;
			}

			if ( $current = pp_get_option( $option_basename ) ) {
				if ( $current = array_diff_key( $current, $unselected ) )
					$value = array_merge( $current, $value );	// retain setting for any types which were previously enabled for filtering but are currently not registered
			}
			
			$value = stripslashes_deep($value);
			pp_update_option( $option_basename, $value );
			
			$updated = true;
		}
		
		if ( pp_wp_ver( '3.5' ) ) {
			pp_update_option( 'define_create_posts_cap', ! empty($_REQUEST['pp_define_create_posts_cap']) );
		}
	}
	
	if ( ! empty( $_REQUEST['SaveRole']) ) {
		if ( ! empty( $_REQUEST['role'] ) ) {
			$pp_only = (array) pp_get_option( 'supplemental_role_defs' );
			
			if ( empty($_REQUEST['pp_only_role']) )
				$pp_only = array_diff( $pp_only, array($_REQUEST['role']) );
			else
				$pp_only[]= $_REQUEST['role'];

			pp_update_option( 'supplemental_role_defs', array_unique($pp_only) );
			_cme_pp_default_pattern_role( $_REQUEST['role'] );
		}
	}
	
	if ( $updated ) {
		pp_refresh_options();
	}
	
	return $updated;
}

