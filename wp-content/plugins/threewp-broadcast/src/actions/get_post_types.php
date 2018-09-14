<?php

namespace threewp_broadcast\actions;

/**
	@brief		Allow plugins to return an array of post types that Broadcast may broadcast.
	@since		2014-02-22 10:32:19
**/
class get_post_types
	extends action
{
	/**
		@brief		An array of post types that Broadcast may broadcast.
		@since		2014-02-22 10:31:55
	**/
	public $post_types = [];

	/**
		@brief		Convenience function to add a post type.
		@since		2015-10-10 20:45:23
	**/
	public function add_type( $type )
	{
		$this->post_types[ $type ] = $type;
	}

	/**
		@brief		Convenience function to add several types at once.
		@since		2015-10-10 20:46:12
	**/
	public function add_types()
	{
		$types = func_get_args();
		foreach( $types as $type )
			$this->add_type( $type );
	}
}
