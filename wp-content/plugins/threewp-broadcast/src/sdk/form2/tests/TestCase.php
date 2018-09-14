<?php

namespace plainview\sdk_broadcast\form2\tests;

/**
	@brief		TestCase for Form2 testing.

	@details

	@par		Changelog

	- 20130718	Initial version.

	@since		20130718
	@version	20130718
**/
class TestCase extends \plainview\sdk_broadcast\tests\TestCase
{
	/**
		@brief		Create a form.
		@return		\plainview\sdk_broadcast\form2\form	Newly-created form.
		@since		20130718
	**/
	public function form()
	{
		return new \plainview\sdk_broadcast\form2\form;
	}
}
