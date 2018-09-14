<?php

namespace threewp_broadcast\premium_pack;

class ajax_data
{
	/**
		@brief		Should to_json display and then die, or just return the string?
		@since		20131217
	**/
	public $display = true;

	use \plainview\sdk_broadcast\traits\method_chaining;

	public function display( $display )
	{
		return $this->set_key( 'display', $display );
	}

	public function html( $html )
	{
		return $this->set_key( 'html', $html );
	}

	public function to_json()
	{
		$output = clone( $this );
		unset( $output->display );

		$r = json_encode( $output );
		if ( $this->display )
		{
			echo $r;
			die();
		}
		else
			return $r;
	}
}
