<?php

namespace threewp_broadcast\actions;

/**
	@brief		Sets the broadcast data of a post.
	@details	Finishing this action will prevent Broadcast from writing the data to the database.
	@see		broadcast_data action for INs.
	@since		2015-07-23 13:07:51
**/
class set_post_broadcast_data
	extends broadcast_data
{
	/**
		@brief		IN: The broadcast data of this blog + post.
		@since		2015-07-23 13:08:34
	**/
	public $broadcast_data;
}
