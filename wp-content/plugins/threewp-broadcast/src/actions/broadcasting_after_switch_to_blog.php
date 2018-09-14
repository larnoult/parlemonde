<?php

namespace threewp_broadcast\actions;

class broadcasting_after_switch_to_blog
	extends action
{
	/**
		@brief		The broadcasting data.
		@since		20131005
	**/
	public $broadcasting_data;

	/**
		@brief		Broadcast to this blog?
		@details	Allow plugins to skip broadcasting to this specific blog on the fly.
		@since		2014-10-07 09:15:46
	**/
	public $broadcast_here = true;
}
