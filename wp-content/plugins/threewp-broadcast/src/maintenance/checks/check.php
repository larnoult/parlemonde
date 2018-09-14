<?php

namespace threewp_broadcast\maintenance\checks;

/**
	@brief		A maintenance check.
	@since		20131101
**/
class check
{
	/**
		@brief		Has this check been processed?
		@since		20131101
	**/
	public $checked = false;

	/**
		@brief		The data object for this check.
		@since		20131104
	**/
	public $data;

	/**
		@brief		The maintenance data object.
		@since		20131104
	**/
	public $maintenance_data;

	/**
		@brief		Which step the check is on.
		@details	Is the name of the function to call: step_$STEP()
		@since		20131104
	**/
	public $step = 'start';

	public function __construct()
	{
		$this->data = $this->data();
	}

	/**
		@brief		Return the Broadcast instance.
		@since		20131101
	**/
	public static function broadcast()
	{
		return \threewp_broadcast\ThreeWP_Broadcast::instance();
	}

	/**
		@brief		Create a new local data object in which to store our data.
		@since		20131104
	**/
	public static function data()
	{
		return new data;
	}

	/**
		@brief		Return an array of action links
		@since		20131108
	**/
	public function get_actions()
	{
		return [];
	}

	/**
		@brief		Return a URL for a check's action.
		@since		20131108
	**/
	public function get_action_url( $action_name )
	{
		return $this->controller->get_action_url( $this, $action_name );
	}

	/**
		@brief		Describe this check.
		@since		20131101
	**/
	public function get_description()
	{
		return 'Description';
	}

	/**
		@brief		The ID of the check.
		@details	Just a few unique characters.
		@since		20131101
	**/
	public function get_id()
	{
		$id = md5( $this->get_name() );
		$id = substr( $id, 0, 4 );
		return $id;
	}

	/**
		@brief		The name of the check.
		@since		20131101
	**/
	public function get_name()
	{
		return 'Name';
	}

	public function init()
	{
		$r = file_get_contents( __DIR__ . '/js.js' );
		return $r;
	}

	/**
		@brief		Make the check handle itself: display the UI, handle the _GET, etc.
		@since		20131102
	**/
	public function step()
	{
		$old_step = $this->step;
		$function = 'step_' . $this->step;
		$args = func_get_args();
		array_shift( $args );
		$r = sprintf( '<div class="threewp_broadcast_check step_%s">', $this->step );
		$r .= call_user_func_array( [ $this, $function ], $args );
		$r .= '</div>';
		return $r;
	}

	public function next_step( $step )
	{
		// Set the new step.
		$this->step = $step;

		// Reload the current page.
		$r = file_get_contents( __DIR__ . '/js.js' );
		$r .= sprintf( '<div class="next_step_link"><a href="%s">Continue to next step.</a></div>', remove_query_arg( 'ignore' ) );

		return $r;
	}
}
