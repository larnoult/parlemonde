<?php

namespace threewp_broadcast\posts\actions;

/**
	@brief		A bulk post action that can be applied to the posts overview.
	@since		2014-10-31 13:26:54
**/
class action
	extends generic
{
	/**
		@brief		IN: Optional - Execute this action on a specific child blog.
		@details	If left at 0, will execute the action on all child blogs. This is the difference between a delete and a delete_all.
		@since		2014-11-03 08:01:00
	**/
	public $child_blog_id = 0;

	/**
		@brief		IN: The action slug.
		@details	For example: delete, trash, restore, etc.
		@since		2014-11-02 21:37:35
	**/
	public $action;

	/**
		@brief		Sets the action for this post action.
		@since		2014-11-02 21:34:30
	**/
	public function set_action( $action )
	{
		$this->action = $action;
		return $this;
	}

	/**
		@brief		Set the child blog id, on which to execute this action.
		@since		2014-11-03 08:02:22
	**/
	public function set_child_blog_id( $child_blog_id )
	{
		$this->child_blog_id = $child_blog_id;
		return $this;
	}
}
