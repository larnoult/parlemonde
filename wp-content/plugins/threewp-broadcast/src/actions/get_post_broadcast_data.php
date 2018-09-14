<?php

namespace threewp_broadcast\actions;

/**
	@brief		Gets the broadcast data of a post.
	@details	Finishing this action will prevent Broadcast from returning the [cached] data from the database.
	@see		broadcast_data action for INs.
	@since		2015-07-23 13:07:51
**/
class get_post_broadcast_data
	extends broadcast_data
{
	/**
		@brief		OUT: The broadcast data of this blog + post.
		@since		2015-07-23 13:08:34
	**/
	public $broadcast_data;
}
