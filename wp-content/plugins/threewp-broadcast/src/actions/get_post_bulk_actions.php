<?php

namespace threewp_broadcast\actions;

/**
	@brief		Return a collection of bulkable post actions.
	@since		2014-10-31 13:19:09
**/
class get_post_bulk_actions
	extends get_post_actions
{
	/**
		@brief		Adds a bulk action.
		@param		\threewp_broadcast\post\actions\bulk\action $action
		@since		2014-11-02 21:13:36
	**/
	public function add( $action )
	{
		$this->actions->append( $action );
	}

	/**
		@brief		Return the javascript necessary to add to the bulk actions select box.
		@since		2014-10-31 14:00:41
	**/
	public function get_js()
	{
		// TODO: < 1
		if ( count( $this->actions ) < -1 )
			return;

		// Sort them using the name.
		$this->actions->sort_by( function( $item )
		{
			return $item->get_name();
		} );

		$r = '<script type="text/javascript">';
		$r .= 'broadcast_bulk_post_actions = {';
		$array = [];
		foreach( $this->actions as $bulk_action )
		{
			$array[] = sprintf( '"%s" : { "name" : "%s", "callback" : function( broadcast_post_bulk_actions ){ %s } }',
				md5( $bulk_action->get_name() ),
				$bulk_action->get_name(),
				$bulk_action->get_javascript_function()
			);
		}
		$r .= implode( ',', $array );
		$r .= '};';
		$r .= '</script>';
		return $r;
	}
}
