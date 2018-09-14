<?php

namespace threewp_broadcast\cache;

/**
	@brief		Cache for posts
	@since		20131010
**/
abstract class posts_cache
	extends \plainview\sdk_broadcast\collections\collection
{

	/**
		@brief		Cache the lookup.
		@details	Searches the lookups for this blog + post combo and stores whatever is found. If anything.
		@since		20131010
	**/
	public function cache_lookups( $blog_id, $post_id, $lookups )
	{
		$data = null;

		// In the array of data, find the data that matches this blog and post.
		foreach( $lookups as $index => $lookup )
		{
			$lookup_post_id = $this->extract_post_id( $lookup );
			if ( $lookup_post_id == $post_id )
			{
				// Found it!
				$lookups->forget( $index );
				$data = $this->extract_data( $lookup );
				break;
			}
		}

		// Didn't find any result  that matches this blog and post. What do we do with it?
		if ( ! $data )
		{
			$this->cache_no_data( $blog_id, $post_id );
			return;
		}

		$key = $this->key( $blog_id, $post_id );
		$this->set( $key, $data );
	}

	/**
		@brief		We are trying to cache a null value. What do we do?
		@details	Subclasses might want to store an empty object.
		@since		20131010
	**/
	public function cache_no_data( $blog_id, $post_id )
	{
		return null;
	}

	/**
		@brief		Extract the data from the lookup.
		@since		20131010
	**/
	public function extract_data( $lookup )
	{
		return $lookup[ 'data' ];
	}

	/**
		@brief		Prep the cache for the following post IDs in the the specified blog.
		@since		20131010
	**/
	public function expect( $blog_id, $post_ids )
	{
		if ( ! is_array( $post_ids ) )
			$post_ids = [ $post_ids ];

		$missing_post_ids = [];
		foreach( $post_ids as $post_id )
		{
			$key = $this->key( $blog_id, $post_id );
			if ( $this->has( $key ) )
				continue;
			$missing_post_ids []= $post_id;
		}

		// Are we missing anything?
		if ( count( $missing_post_ids ) < 1 )
			return;

		// Fetch them!
		$lookups = $this->lookup( $blog_id, $missing_post_ids );
		$lookups = new \plainview\sdk_broadcast\collections\collection( $lookups );

		// Since not all requested post IDs have broadcast data, foreach the missing post ids, not the results, and add them to the cache.
		foreach( $missing_post_ids as $post_id )
			$this->cache_lookups( $blog_id, $post_id, $lookups );
		return $this;
	}

	/**
		@brief		From the global $wp_query object, expect the post IDs.
		@since		20131010
	**/
	public function expect_from_wp_query()
	{
		global $wp_query;
		$blog_id = get_current_blog_id();
		$post_ids = [];
		foreach( $wp_query->posts as $post )
			$post_ids []= $post->ID;
		$this->expect( $blog_id, $post_ids );
		return $this;
	}

	/**
		@brief		Return the post ID of this lookup row.
		@since		20131010
	**/
	public function extract_post_id( $lookup )
	{
		return $lookup [ 'post_id' ];
	}

	/**
		@brief		Do an SQL query for these post IDs in this blog.
		@since		20131010
	**/
	public abstract function lookup( $blog_id, $post_ids );

	/**
		@brief		Returns the data for this this blog and post combo.
		@details	Returns false if no data stored. Or if the stored data is null.
		@since		20131010
	**/
	public function get_for( $blog_id, $post_id )
	{
		$key = $this->key( $blog_id, $post_id );
		return $this->get( $key, null );
	}

	/**
		@brief		Returns the cache key for this blog and post combo.
		@since		20131010
	**/
	public function key( $blog_id, $post_id )
	{
		return sprintf( '%s_%s', $blog_id, $post_id );
	}

	/**
		@brief		Set the data for this blog and post combo.
		@since		20131010
	**/
	public function set_for( $blog_id, $post_id, $data )
	{
		$key = $this->key( $blog_id, $post_id );
		$this->set( $key, $data );
	}

}
