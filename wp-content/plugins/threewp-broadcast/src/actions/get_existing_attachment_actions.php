<?php

namespace threewp_broadcast\actions;

/**
	@brief		Return what actions are available for existing attachments.
	@see		apply_existing_attachment_action
	@since		2015-11-16 13:42:57
**/
class get_existing_attachment_actions
	extends action
{
	/**
		@brief		An array of [ key => description ] actions.
		@since		2015-11-16 13:44:42
	**/
	public $actions = [];

	/**
		@brief		Add an action.
		@since		2015-11-16 13:44:24
	**/
	public function add( $key, $description )
	{
		$this->actions[ $key ] = $description;
	}

	/**
		@brief		Return the actions array.
		@since		2015-11-16 13:43:16
	**/
	public function get_actions()
	{
		return $this->actions;
	}
}
