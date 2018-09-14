<?php

namespace threewp_broadcast\actions;

/**
	@brief		Tells Broadcast to parse a content string with the use of the Broadcasting data.
	@details	Will currently replace image attachments in captions and image guids.

	For an example of prepase and parse in action, see the two methods in the broadcasting trait where they handle galleries.

	@see		preparse_content
	@since		2016-03-30 17:47:15
**/
class parse_content
	extends action
{
	/**
		@brief		IN: The broadcasting_data object to use.
		@since		2016-03-30 17:48:10
	**/
	public $broadcasting_data;

	/**
		@brief		IN/OUT: The string to parse.
		@since		2016-03-30 17:48:21
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
