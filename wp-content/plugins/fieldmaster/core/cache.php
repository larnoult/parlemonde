<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('fieldmaster_cache') ) :

class fieldmaster_cache {
	
	// vars
	var $reference = array();
		
		
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// prevent FieldMaster from persistent cache
		wp_cache_add_non_persistent_groups('fieldmaster');
		
	}
	
	
	/*
	*  get_key
	*
	*  This function will check for references and modify the key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	$key
	*/
	
	function get_key( $key = '' ) {
		
		// check for reference
		if( isset($this->reference[ $key ]) ) {
			
			$key = $this->reference[ $key ];
				
		}
		
		
		// return
		return $key;
		
	}
	
	
	
	/*
	*  isset_cache
	*
	*  This function will return true if a cached data exists for the given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	(boolean)
	*/
	
	function isset_cache( $key = '' ) {
		
		// vars
		$key = $this->get_key($key);
		$found = false;
		
		
		// get cache
		$cache = wp_cache_get($key, 'fieldmaster', false, $found);
		
		
		// return
		return $found;
		
	}
	
	
	/*
	*  get_cache
	*
	*  This function will return cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	(mixed)
	*/
	
	function get_cache( $key = '' ) {
		
		// vars
		$key = $this->get_key($key);
		$found = false;
		
		
		// get cache
		$cache = wp_cache_get($key, 'fieldmaster', false, $found);
		
		
		// return
		return $cache;
		
	}
	
	
	/*
	*  set_cache
	*
	*  This function will set cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @param	$data (mixed)
	*  @return	n/a
	*/
	
	function set_cache( $key = '', $data = '' ) {
		
		wp_cache_set($key, $data, 'fieldmaster');
		
		return $key;
		
	}
	
	
	/*
	*  set_cache_reference
	*
	*  This function will set a reference to cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @param	$reference (string)
	*  @return	n/a
	*/
	
	function set_cache_reference( $key = '', $reference = '' ) {
		
		$this->reference[ $key ] = $reference;	
		
		return $key;
		
	}
	
	
	/*
	*  delete_cache
	*
	*  This function will delete cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	n/a
	*/
	
	function delete_cache( $key = '' ) {
		
		return wp_cache_delete( $key, 'fieldmaster' );
		
	}
	
}


// initialize
fieldmaster()->cache = new fieldmaster_cache();

endif; // class_exists check



/*
*  fieldmaster_isset_cache
*
*  alias of fieldmaster()->cache->isset_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function fieldmaster_isset_cache( $key = '' ) {
	
	return fieldmaster()->cache->isset_cache( $key );
	
}


/*
*  fieldmaster_get_cache
*
*  alias of fieldmaster()->cache->get_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function fieldmaster_get_cache( $key = '' ) {
	
	return fieldmaster()->cache->get_cache( $key );
	
}


/*
*  fieldmaster_set_cache
*
*  alias of fieldmaster()->cache->set_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function fieldmaster_set_cache( $key = '', $data ) {
	
	return fieldmaster()->cache->set_cache( $key, $data );
	
}


/*
*  fieldmaster_set_cache_reference
*
*  alias of fieldmaster()->cache->set_cache_reference()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function fieldmaster_set_cache_reference( $key = '', $reference = '' ) {
	
	return fieldmaster()->cache->set_cache_reference( $key, $reference );
	
}


/*
*  fieldmaster_delete_cache
*
*  alias of fieldmaster()->cache->delete_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function fieldmaster_delete_cache( $key = '' ) {
	
	return fieldmaster()->cache->delete_cache( $key );
	
}

?>