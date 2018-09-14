<?php

namespace threewp_broadcast\posts\actions;

/**
	@brief		A base, generic post action.
	@since		2014-11-02 21:33:57
**/
class generic
{
	/**
		@brief		The more-or-less unique ID for this action.
		@since		2015-02-02 00:22:05
	**/
	public $id;

	/**
		@brief		A short name / verb that describes the action.
		@since		2014-10-31 14:14:15
	**/
	public $name;

	/**
		@brief		Return the unique ID for this action.
		@since		2015-02-02 00:22:52
	**/
	public function get_id()
	{
		return $this->id;
	}

	/**
		@brief		Set the unique ID of this action.
		@since		2015-02-02 00:22:27
	**/
	public function set_id( $id )
	{
		$this->id = $id;
	}

	/**
		@brief		Get the action name.
		@see		$name
		@since		2014-10-31 14:14:31
	**/
	public function get_name()
	{
		return $this->name;
	}

	/**
		@brief		Set the action name.
		@see		$name
		@since		2014-10-31 14:14:34
	**/
	public function set_name( $name )
	{
		$this->name = $name;
	}
}
