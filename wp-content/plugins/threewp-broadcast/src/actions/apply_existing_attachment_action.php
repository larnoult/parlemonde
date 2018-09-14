<?php

namespace threewp_broadcast\actions;

/**
	@brief		Apply the selected action to an existing attachment upon broadcasting.
	@details	The default action is to use the existing attachment, see $use.

	If you want to do anything else, that requires the attachment being copied by Broadcast, set $use to false.

	@see		get_existing_attachment_actions
	@since		2015-11-16 13:55:07
**/
class apply_existing_attachment_action
	extends action
{
	/**
		@brief		IN: The action for existing attachments in the settings.
		@details	This could be 'use', 'randomize', 'overwrite', etc.
		@see		get_existing_attachment_actions
		@since		2015-11-16 13:58:40
	**/
	public $action;

	/**
		@brief		The broadcasting data.
		@details	Also contains the attachment_data object.
		@since		2015-11-16 14:09:00
	**/
	public $broadcasting_data;

	/**
		@brief		IN: The source attachment as an attachment_data object.
		@since		2015-11-16 13:55:36
	**/
	public $source_attachment;

	/**
		@brief		IN: The target attachment as a WP_Post object.
		@since		2015-11-16 13:55:53
	**/
	public $target_attachment;

	/**
		@brief		[OUT]: Do we use this existing attachment?
		@since		2015-11-16 14:05:27
	**/
	public $use = true;
}
