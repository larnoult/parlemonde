<?php

namespace threewp_broadcast\actions;

/**
	@brief		General class for handling broadcast data.
	@since		2015-07-23 13:06:45
**/
class broadcast_data
	extends action
{
	/**
		@brief		IN: The blog ID for the requested broadcast data.
		@since		2015-07-23 13:07:08
	**/
	public $blog_id;

	/**
		@brief		IN: The post ID for the requested broadcast data.
		@since		2015-07-23 13:07:08
	**/
	public $post_id;
}
