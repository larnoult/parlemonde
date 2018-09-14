<?php

namespace threewp_broadcast\posts\actions\bulk;

/**
	@brief		A post bulk action that uses Wordpress ajax.
	@since		2014-10-31 22:57:59
**/
class wp_ajax
	extends action
{
	/**
		@brief		The data array sent to Wordpress in the POST.
		@since		2014-10-31 22:58:24
	**/
	public $data = [];

	public function get_javascript_function()
	{
		$contents = file_get_contents( __DIR__ . '/wp_ajax.js' );
		$contents = sprintf( $contents, json_encode( $this->data ) );
		return $contents;
	}

	/**
		@brief		Sets the Wordpress ajax action.
		@see		set_data()
		@since		2014-10-31 22:58:41
	**/
	public function set_ajax_action( $action )
	{
		$this->set_data( 'action', $action );
		$this->set_nonce( $action );
	}

	/**
		@brief		Sets a data key / value pair.
		@see		$data
		@since		2014-11-01 18:56:13
	**/
	public function set_data( $key, $value )
	{
		$this->data[ $key ] = $value;
	}

	/**
		@brief		Convenience function to set the nonce in the data.
		@since		2014-11-01 19:20:25
	**/
	public function set_nonce( $action )
	{
		$this->set_data( 'nonce', wp_create_nonce( $action ) );
	}
}
