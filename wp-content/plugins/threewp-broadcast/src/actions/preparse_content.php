<?php

namespace threewp_broadcast\actions;

/**
	@brief		Informs all add-ons and plugins of content that will have to be parsed during broadcast.
	@details	This action is called just before broadcasting starts so that plugins can collect whatever local inforation is necessary.

	This is to unnecessary blog switching during a child broadcast.

	For an example of prepase and parse in action, see the two methods in the broadcasting trait where they handle galleries.

	@see		parse_content
	@since		2016-04-22 12:49:45
**/
class preparse_content
	extends action
{
	/**
		@brief		IN: The broadcasting_data object to use.
		@since		2016-04-22 12:49:45
	**/
	public $broadcasting_data;

	/**
		@brief		IN/OUT: The string to preparse.
		@since		2016-04-22 12:49:45
	**/
	public $content;

	/**
		@brief		IN: The content identifier.
		@details	This is purely to help plugins organise the content data. For example, the id could be "post_content" or perhaps "acf_field_123".

		Otherwise the plugins will have to go through all of the contents to see which one is being manipulated.

		@since		2016-04-22 12:51:11
	**/
	public $id = '';
}
