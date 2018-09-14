<?php


namespace threewp_broadcast\actions;

/**
	@brief		Prepare the data in the meta box.
**/
class prepare_meta_box
	extends action
{
	/**
		@brief		IN/OUT: The meta box data object, ready to be modified.
		@var		$meta_box_data
		@since		20131010
	**/
	public $meta_box_data;

	/**
		@brief		Convenience method to return whether this post is a parent.
		@since		2017-09-19 08:46:40
	**/
	public function is_parent_post()
	{
		return ( $this->meta_box_data->broadcast_data->get_linked_parent() === false);
	}
}
