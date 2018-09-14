<?php

namespace threewp_broadcast\actions;

/**
	@brief		Maybe clear the POST data before broadcasting.
	@since		2014-03-23 23:07:02
**/
class maybe_clear_post
	extends action
{
	/**
		@brief		The _POST variable to manipulate before broadcasting.
		@since		2014-03-23 23:07:19
	**/
	public $post;
}
