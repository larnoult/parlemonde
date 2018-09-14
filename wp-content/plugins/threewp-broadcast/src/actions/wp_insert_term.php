<?php

namespace threewp_broadcast\actions;

/**
	@brief
	@since		2014-04-08 15:09:59
**/
class wp_insert_term
	extends action
{
	/**
		@brief		The broadcasting data.
		@since		2017-11-24 18:48:47
	**/
	public $broadcasting_data;

	/**
		@brief		OUT: The newly-created term object. Or a WP_Error or false if the term was not created.
		@since		2014-04-08 15:32:24
	**/
	public $new_term = false;

	/**
		@brief		IN: The name of the taxonomy in which to create the term.
		@since		2014-04-08 15:34:26
	**/
	public $taxonomy;

	/**
		@brief		IN: The term object to create, taken from the source blog.
		@since		2014-04-08 15:30:19
	**/
	public $term;
}
