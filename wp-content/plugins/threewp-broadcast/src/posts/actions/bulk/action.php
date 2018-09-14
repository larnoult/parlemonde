<?php

namespace threewp_broadcast\posts\actions\bulk;

/**
	@brief		A bulk post action that can be applied to the posts overview.
	@details	Since bulk actions are executed in javascript, a function will have to be provided.
	@since		2014-10-31 13:26:54
**/
class action
	extends \threewp_broadcast\posts\actions\generic
{
	/**
		@brief		Return the javascript function that is called when the submit button is pressedn.
		@since		2014-10-31 23:00:31
	**/
	public function get_javascript_function()
	{
		return "document.title = broadcast_post_bulk_actions.get_ids();";
	}
}
