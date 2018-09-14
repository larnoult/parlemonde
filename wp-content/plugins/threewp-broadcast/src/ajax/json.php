<?php

namespace threewp_broadcast\ajax;

/**
	@brief		JSON handling class containing methods to help integrate with debug mode.
	@since		2014-11-01 19:32:16
**/
class json
{
	/**
		@brief		Display the json output or return it?
		@since		2014-11-01 19:33:24
	**/
	public $__display = true;

	public function output()
	{
		$output = clone( $this );
		unset( $output->__display );

		$r = json_encode( $output );
		if ( $this->__display )
		{
			echo $r;
			die();
		}
		else
			return $r;
	}
}
