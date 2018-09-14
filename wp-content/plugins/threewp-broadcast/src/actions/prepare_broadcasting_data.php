<?php

namespace threewp_broadcast\actions;

/**
	@brief		Requests that the broadcasting data object be modified / prepared.
	@details	This should be used when validating the user's $_POST input, also checking the user's access roles, etc.

	If the above is not necessary, and you're broadcasting via the API, you can skip this action.

	@since		20131028
**/
class prepare_broadcasting_data
	extends action
{
	/**
		@brief		The broadcasting data.
		@since		20131028
	**/
	public $broadcasting_data;
}
