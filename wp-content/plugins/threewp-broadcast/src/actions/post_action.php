<?php

namespace threewp_broadcast\actions;

/**
	@brief		Execute an action on a post.
	@details	The action could be delete, trash, unlink, etc.
	@since		2014-11-02 16:25:57
**/
class post_action
	extends action
{
	/**
		@brief		IN: The action to execute: delete, untrash, etc.
		@since		2014-11-02 16:29:59
	**/
	public $action;

	/**
		@brief		IN: The ID of the post on which to execute this action.
		@since		2014-11-02 16:28:00
	**/
	public $post_id;
}
