<?php

namespace threewp_broadcast;

/**
	@brief		Storage class for equivalent posts on various blogs.
	@since		2014-09-21 11:53:45
**/
class equivalent_posts
{
	/**
		@brief		An array of parent blogs IDs => parent post IDs => child blog IDs => child post IDs.
		@since		2014-09-21 11:55:15
	**/
	public $equivalents;

	/**
		@brief		Constructor.
		@since		2014-09-21 11:55:03
	**/
	public function __construct()
	{
		$this->equivalents = [];
	}

	public function broadcast()
	{
		return \threewp_broadcast\ThreeWP_Broadcast::instance();
	}

	/**
		@brief		Retrieve the equivalent post on a blog for a specific parent blog/post.
		@since		2014-09-21 11:54:04
	**/
	public function get( $parent_blog, $parent_post, $child_blog )
	{
		if ( ! isset( $this->equivalents[ $parent_blog ][ $parent_post ][ $child_blog ] ) )
		{
			$broadcast_data = $this->broadcast()->get_parent_post_broadcast_data( $parent_blog, $parent_post );
			$child = $broadcast_data->get_linked_post_on_this_blog();
			$this->set( $parent_blog, $parent_post, $child_blog, $child );
		}
		return $this->equivalents[ $parent_blog ][ $parent_post ][ $child_blog ];
	}

	/**
		@brief		Either retrieve the existing equivalent, or broadcast the post making a new equivalent.
		@since		2017-10-25 16:44:53
	**/
	public function get_or_broadcast( $parent_blog, $parent_post, $child_blog )
	{
		$child_post = $this->get( $parent_blog, $parent_post, $child_blog );
		if ( ! $child_post )
		{
			$this->broadcast()->debug( 'Equivalent child of %s / %s on %s not found. Broadcasting.', $parent_blog, $parent_post, $child_blog );
			switch_to_blog( $parent_blog );
			$new_bcd = ThreeWP_Broadcast()->api()
				->broadcast_children( $parent_post, [ $child_blog ] );
			restore_current_blog();
			$child_post = $new_bcd->new_post( 'ID' );
			$this->broadcast()->debug( 'Equivalent child of %s / %s is now %s / %s.', $parent_blog, $parent_post, $child_blog, $child_post );
			$this->equivalents[ $parent_blog ][ $parent_post ][ $child_blog ] = $child_post;
		}
		return $child_post;
	}

	/**
		@brief		Set the equivalent post on a blog.
		@since		2014-09-21 11:54:04
	**/
	public function set( $parent_blog, $parent_post, $child_blog, $child_post )
	{
		if ( ! isset( $this->equivalents[ $parent_blog ] ) )
			$this->equivalents[ $parent_blog ] = [];
		if ( ! isset( $this->equivalents[ $parent_blog ][ $parent_post ] ) )
			$this->equivalents[ $parent_blog ][ $parent_post ] = [];
		if ( ! isset( $this->equivalents[ $parent_blog ][ $parent_post ][ $child_blog ] ) )
			$this->equivalents[ $parent_blog ][ $parent_post ][ $child_blog ] = $child_post;
	}
}

