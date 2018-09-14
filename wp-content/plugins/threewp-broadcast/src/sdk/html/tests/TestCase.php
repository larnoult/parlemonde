<?php

namespace plainview\sdk_broadcast\html\tests;

/**
	@brief		TestCase for HTML testing.

	@details

	@par		Changelog

	- 20130718	Initial version.

	@since		20130718
	@version	20130718
**/
class TestCase extends \plainview\sdk_broadcast\tests\TestCase
{
	/**
		@brief		Create a div.
		@return		\plainview\sdk_broadcast\html\div		Newly-created div.
		@since		20130718
	**/
	public function div()
	{
		return new \plainview\sdk_broadcast\html\div;
	}
}
