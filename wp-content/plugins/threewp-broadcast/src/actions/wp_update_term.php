<?php

namespace threewp_broadcast\actions;

/**
	@brief		[Maybe] update a taxonomy term.
	@since		2014-04-10 14:17:24
**/
class wp_update_term
	extends action
{
	/**
		@brief		IN: The broadcasting_data object.
		@since		2016-07-22 16:38:11
	**/
	public $broadcasting_data;

	/**
		@brief		IN: The new term object with new values.
		@see		set_new_term()
		@since		2014-04-08 15:32:24
	**/
	public $new_term;

	/**
		@brief		[IN]: The old term as an object, if available.
		@see		set_old_term()
		@since		2014-04-10 14:24:13
	**/
	public $old_term;

	/**
		@brief		IN: The taxonomy name.
		@since		2014-04-08 15:34:26
	**/
	public $taxonomy;

	/**
		@brief		OUT: Was an actual wp_update_term called?
		@since		2014-04-10 14:25:11
	**/
	public $updated = false;

	/**
		@brief		Conv: Does this action have an old term specified?
		@since		2014-04-10 14:27:54
	**/
	public function has_old_term()
	{
		return $this->old_term !== null;
	}

	/**
		@brief		Sets the new term.
		@since		2015-01-15 23:16:39
	**/
	public function set_new_term( $term )
	{
		$this->new_term = clone( $term );
	}

	/**
		@brief		Sets the old term.
		@since		2015-01-15 23:16:39
	**/
	public function set_old_term( $term )
	{
		$this->old_term = clone( $term );
	}

	/**
		@brief		Switch the data between the terms.
		@since		2014-04-10 15:27:47
	**/
	public function switch_data( $key = '' )
	{
		if ( $key !== '' )
			$keys = [ $key ];
		else
			$keys = [
				'count',
				'parent',
				'term_group',
				'term_id',
				'term_taxonomy_id',
			];

		foreach( $keys as $key )
		{
			$temp = $this->old_term->$key;
			$this->old_term->$key = $this->new_term->$key;
			$this->new_term->$key = $temp;
		}
	}
}
