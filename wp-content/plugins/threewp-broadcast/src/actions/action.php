<?php

namespace threewp_broadcast\actions;

class action
	extends \plainview\sdk_broadcast\wordpress\actions\action
{
	public function get_prefix()
	{
		return 'threewp_broadcast_';
	}
}
