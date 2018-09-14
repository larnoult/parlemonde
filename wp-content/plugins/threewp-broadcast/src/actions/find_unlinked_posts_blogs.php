<?php

namespace threewp_broadcast\actions;

/**
	@brief		Return which blogs to search for unlinked children.
	@see		https://broadcast.plainviewplugins.com/snippet/find-unlinked-children-only-on-some-blogs/
	@since		2017-04-21 12:20:30
**/
class find_unlinked_posts_blogs
	extends action
{
	/**
		@brief		A blog_collection containing the blogs on which to search for unlinked children.
		@since		2017-04-21 12:21:32
	**/
	public $blogs;
}
