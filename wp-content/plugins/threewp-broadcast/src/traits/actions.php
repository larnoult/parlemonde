<?php

namespace threewp_broadcast\traits;

/**
	@brief		Provides methods related to actions.
	@since		2017-11-23 21:44:51
**/
trait actions
{
	/**
		@brief		Generate a new action.
		@details	Convenience method so that other plugins don't have to use the whole namespace for the class' actions.
		@since		2017-09-27 13:20:01
	**/
	public function new_action( $action_name )
	{
		$called_class = get_called_class();
		// Strip off the class name.
		$namespace = preg_replace( '/(.*)\\\\.*/', '\1', $called_class );
		$classname = $namespace  . '\\actions\\' . $action_name;
		return new $classname();
	}
}
