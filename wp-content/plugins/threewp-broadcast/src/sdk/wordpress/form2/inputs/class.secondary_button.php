<?php

namespace plainview\sdk_broadcast\wordpress\form2\inputs;

class secondary_button
	extends button
{
	public function _construct()
	{
		$this->css_class( 'button-secondary' );
	}
}
