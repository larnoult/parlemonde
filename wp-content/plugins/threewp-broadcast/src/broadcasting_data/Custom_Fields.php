<?php

namespace threewp_broadcast\broadcasting_data;

/**
	@brief		A collection of custom field helper methods.
	@details	Created mainly as a convenience class for get and has lookups.
	@since		2015-06-06 09:01:46
**/
class Custom_Fields
	extends \threewp_broadcast\collection
{
	/**
		@brief		The broadcasting data object.
		@since		2015-06-06 09:02:08
	**/
	public $broadcasting_data;

	/**
		@brief		Constructor.
		@since		2015-06-06 09:01:58
	**/
	public function __construct( $broadcasting_data )
	{
		$this->broadcasting_data = $broadcasting_data;
		$this->items = & $this->broadcasting_data->custom_fields->original;
	}

	/**
		@brief		Checks whether a name exists in the blacklist.
		@since		2015-06-06 09:09:26
	**/
	public function blacklist_has( $field_name )
	{
		return $this->list_has( 'blacklist', $field_name );
	}

	/**
		@brief		Return the child fields object.
		@since		2015-08-02 14:56:14
	**/
	public function child_fields()
	{
		return new custom_fields\Child_Fields( $this->broadcasting_data );
	}

	/**
		@brief		Return the first value of a custom field.
		@since		2015-08-02 09:35:14
	**/
	public function get_single( $key )
	{
		if ( ! $this->has( $key ) )
			return null;
		return reset( $this->broadcasting_data->custom_fields->original[ $key ] );
	}

	/**
		@brief		Checks whether a specific custom field is covered in a custom field list.
		@since		2015-06-06 09:08:18
	**/
	public function list_has( $list_type, $field_name )
	{
		foreach( $this->broadcasting_data->custom_fields->$list_type as $entry )
		{
			// No wildcard = straight match
			if ( strpos( $entry, '*' ) === false )
			{
				if ( $entry == $field_name )
					return true;
			}
			else
			{
				$preg = str_replace( '*', '.*', $entry );
				$preg = sprintf( '/%s/', $preg );
				preg_match( $preg, $field_name, $matches );
				if ( ( count( $matches ) == 1 ) && $matches[ 0 ] == $field_name )
					return true;
			}

		}
		return false;
	}

	/**
		@brief		Checks whether a name exists in the protectlist.
		@since		2015-06-06 09:09:26
	**/
	public function protectlist_has( $field_name )
	{
		return $this->list_has( 'protectlist', $field_name );
	}

	/**
		@brief		Checks whether a name exists in the whitelist.
		@since		2015-06-06 09:09:26
	**/
	public function whitelist_has( $field_name )
	{
		return $this->list_has( 'whitelist', $field_name );
	}
}
