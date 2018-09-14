<?php

namespace threewp_broadcast\actions;

/**
	@brief		Copy an attachment from one blog to another.
	@since		2014-12-24 12:40:46
**/
class copy_attachment
	extends action
{
	/**
		@brief		IN: The attachment_data object to be copied.
		@since		2014-12-25 10:16:28
	**/
	public $attachment_data;

	/**
		@brief		OUT: The ID of the new attachment.
		@since		2014-12-25 10:14:43
	**/
	public $attachment_id;

	/**
		@brief		Sets the attachment data for the attachment to be copied.
		@since		2014-12-25 10:16:08
	**/
	public function set_attachment_data()
	{
	}

	/**
		@brief		Set the ID of the new attachment.
		@since		2014-12-25 10:14:28
	**/
	public function set_attachment_id( $attachment_id )
	{
		$this->attachment_id = $attachment_id;
	}
}
