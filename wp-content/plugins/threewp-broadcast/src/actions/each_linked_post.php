<?php

namespace threewp_broadcast\actions;

/**
	@brief		Execute a callback on each linked post.

	@details	Given the ID of a post, will find all linked posts (parent and children) and execute callbacks on the posts.

	The sequence is:

	- action is created
	- action is given the post_id at the minimum.
	- execute()
	- the broadcast data of the post is loaded
	- if a child, the broadcast data of the parent is loaded
	- the loop begins and the callback(s) are executed on all linked posts

	@since		2015-05-02 21:21:10
**/
class each_linked_post
	extends action
{
	/**
		@brief		IN: The array of callbacks to be called on each child.

		@details

		Use the add_callback() method to more easily add callbacks.

		The callback is passed an object with the following info:

		- the post object being worked on

		@see		add_callback()
		@since		2015-05-02 21:22:55
	**/
	public $callbacks = [];

	/**
		@brief		[IN]: The blog ID on which the post belongs.
		@details	If left empty will use the current blog.
		@since		2015-05-02 21:38:04
	**/
	public $blog_id = null;

	/**
		@brief		[IN]: Execute the callback(s) on the child posts?
		@since		2015-05-02 21:29:48
	**/
	public $on_children = true;

	/**
		@brief		[IN]: Execute the callback(s) on the parent post?
		@since		2015-05-02 21:28:17
	**/
	public $on_parent = true;

	/**
		@brief		IN: The ID of the post.
		@since		2015-05-02 21:40:01
	**/
	public $post_id;

	/**
		@brief		Convenience method to add a callback to the callback array.
		@since		2015-05-02 21:30:56
	**/
	public function add_callback( $callback )
	{
		$this->callbacks []= $callback;
	}
}
