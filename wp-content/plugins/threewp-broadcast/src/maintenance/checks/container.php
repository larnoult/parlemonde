<?php

namespace threewp_broadcast\maintenance\checks;

class container
extends \plainview\sdk_broadcast\collections\collection
{
	/**
		@brief		Link back to the controller.
		@since		20131108
	**/
	public $controller;

	/**
		@brief		The maintenance data object.
		@since		20131104
	**/
	public $maintenance_data;

	/**
		@brief		Adds a check to the container.
		@details	Uses the check's ID as the key.
		@since		20131102
	**/
	public function add_check( $check )
	{
		$check->controller = $this->controller;
		$check->maintenance_data = $this->maintenance_data;
		$this->set( $check->get_id(), $check );
		return $this;
	}
}
