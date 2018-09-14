<?php

namespace threewp_broadcast;

class blog_collection
	extends \plainview\sdk_broadcast\collections\collection
{
	/**
		@brief		Sort with the root blog first, then the rest alphabetically.
	**/
	public function sort_logically()
	{
		$this->sortBy( function( $item )
		{
			if ( $item->id == 1 )
				return 'AAA';
			return $item->get_name();
		} );
		return $this;
	}
}
