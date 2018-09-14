<?php

namespace threewp_broadcast\actions;

/**
	@brief		This taxonomy has just been synced.
	@since		2017-11-20 15:08:05
**/
class synced_taxonomy
	extends action
{
	/**
		@brief		IN: The broadcasting data object.
		@since		2017-11-20 15:08:41
	**/
	public $broadcasting_data;

	/**
		@brief		IN: The name of the taxonomy we just synced.
		@since		2017-11-20 15:08:54
	**/
	public $taxonomy;
}
