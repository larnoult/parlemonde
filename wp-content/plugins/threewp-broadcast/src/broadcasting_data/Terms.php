<?php

namespace threewp_broadcast\broadcasting_data;

/**
	@brief		A helper to more easily find the equivalent term ID on this blog.
	@since		2015-07-29 15:16:01
**/
class Terms
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
	}

	/**
		@brief		Return the equivalent term ID.
		@since		2015-07-29 15:16:23
	**/
	public function get( $old_term_id )
	{
		$blog_id = get_current_blog_id();

		foreach( $this->broadcasting_data->parent_blog_taxonomies as $taxonomy_name => $data )
		{
			if ( ! isset( $data[ 'equivalent_terms' ][ $blog_id ][ $old_term_id ] ) )
				continue;
			return $data[ 'equivalent_terms' ][ $blog_id ][ $old_term_id ];
		}
		return false;
	}
}
