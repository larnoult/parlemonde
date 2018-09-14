<?php

namespace threewp_broadcast\actions;

use \threewp_broadcast\blog_collection;

class get_user_writable_blogs
	extends action
{
	/**
		@brief		OUT: A collection of blogs the user has access to.
		@var		$blogs
		@since		20131003
	**/
	public $blogs;

	/**
		@brief		IN: ID of user to query.
		@var		$user_id
		@since		20131003
	**/
	public $user_id;

	public function _construct( $user_id = null )
	{
		$this->blogs = new blog_collection;

		if ( ! $user_id )
			$user_id = ThreeWP_Broadcast()->user_id();

		$this->user_id = $user_id;
	}
}
