<?php

namespace threewp_broadcast\actions;

/**
	@brief		Maybe override the child permalink with the parent permalink.
	@details	This action is called when the Broadcast setting "use parent permalink" is enabled.

				The action provides a way for plugins to decide whether to override the specific child's permalink or not.

				For a usage example, see: https://broadcast.plainviewplugins.com/snippet/override-child-permalink/
	@since		2017-06-01 06:27:02
**/
class override_child_permalink
	extends action
{
	/**
		@brief		IN: The original, child permalink.
		@since		2017-06-01 12:54:34
	**/
	public $child_permalink;

	/**
		@brief		IN: The child post.
		@since		2018-07-25 22:40:26
	**/
	public $child_post;

	/**
		@brief		IN: The parent post in question.
		@since		2017-06-01 12:53:01
	**/
	public $post;

	/**
		@brief		IN: The permalink of the parent post.
		@since		2017-06-01 12:54:34
	**/
	public $parent_permalink;

	/**
		@brief		OUT: The permalink that is returned to be displayed to the user.
		@details	The default is the parent permalink.
		@since		2017-06-01 12:55:07
	**/
	public $returned_permalink;
}
