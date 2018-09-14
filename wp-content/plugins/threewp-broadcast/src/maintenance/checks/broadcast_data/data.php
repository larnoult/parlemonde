<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data;

use \plainview\sdk_broadcast\collections\collection;

/**
	@brief		Special data for broadcast data check.
	@since		20131104
**/
class data
extends \threewp_broadcast\maintenance\checks\data
{
	public static $rows_per_step = 500;

	public function __construct()
	{
		$this->bcd_to_check = new collection;
		$this->broadcast_data = new collection;
		$this->seen_blog_post = new collection;		// Collection of blog_post we've already seen.
		$this->post_bcd_cache = new collection;

		// These are collections of errors

		$this->duplicate_bcd = new collection;		// This blog_post already has broadcast data = Delete it.

		$this->broken_bcd = new collection;		// The BCD can not be read = Delete the BCD.
		$this->missing_post = new collection;		// The post specified in the SQL row does not exist = Delete the BCD.

		$this->missing_parents = new collection;	// The linked parent post does not exist = Remove the link to it.
		$this->parent_is_unlinked = new collection;	// Parent does not link to child = Create a link for the child post.

		$this->unnecessary_children = new collection;			// This child post has a parent and there are children in there also.

		$this->same_parent = new collection;			// Several posts on the same blog have the same parent.

		$this->missing_children = new collection;	// The linked child post does not exist = Remove the link to it.
		$this->child_is_unlinked = new collection;	// Child does not link back to parent = Create a link for the parent post.
	}

	/**
		@brief		Does this data contain any errors?
		@since		20131105
	**/
	public function is_error_free()
	{
		$count = 0;

		if ( isset( $this->id_column_missing ) )
			$count++;

		foreach( [
			'duplicate_bcd',
			'broken_bcd',
			'missing_post',
			'missing_parents',
			'missing_children',
			'parent_is_unlinked',
			'same_parent',
			'child_is_unlinked',
			'unnecessary_children',
		] as $type )
			$count += count( $this->$type );
		return $count === 0;
	}
}
