<?php

namespace threewp_broadcast\actions;

/**
	@brief		Return a collection of bulkable post actions.
	@since		2014-10-31 13:19:09
**/
class get_post_actions
	extends action
{
	/**
		@brief		OUT: A collection of post actions.
		@since		2014-10-31 13:19:48
	**/
	public $actions;

	/**
		@brief		Constructor.
		@since		2014-10-31 13:20:10
	**/
	public function _construct()
	{
		$this->actions = ThreeWP_Broadcast()->collection();
	}

	/**
		@brief		Add a post action.
		@since		2014-10-31 14:13:19
	**/
	/**
		@brief		Adds an action.
		@param		\threewp_broadcast\post\actions\action $action
		@since		2014-11-02 21:13:36
	**/
	public function add( $action )
	{
		$this->actions->append( $action );
	}
}
