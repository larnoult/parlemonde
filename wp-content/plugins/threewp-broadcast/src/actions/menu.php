<?php

namespace threewp_broadcast\actions;

class menu
	extends action
{
	/**
		@brief		IN: The broadcast object.
		@details	Deprecated since 2015126. Use $menu_page instead.
		@since		20131006
	**/
	public $broadcast;

	/**
		@brief		IN: The menu page object.
		@since		2015-12-26 20:45:20
	**/
	public $menu_page;
}
