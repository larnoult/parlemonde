<?php

namespace threewp_broadcast;

/**
	@brief		Container for post linking data between parent and children.
	@since		2014-08-31 20:48:30
**/
class broadcast_data
{
	/**
		@brief		The ID of the database row.
		@since		20131105
	**/
	public $id;

	/**
		@brief		The ID of the blog this object belongs to.
		@since		20131105
	**/
	public $blog_id;

	/**
		@brief		The ID of the post this object belongs to.
		@since		20131105
	**/
	public $post_id;

	private $dataModified = false;

	private $data;

	/**
	 * Create the class with the specified array as the data.
	 */
	public function __construct( $data = [] )
	{
		$this->data = array_merge( self::get_default_data(), $data );
	}

	/**
		@brief		Construct a broadcast data object from an sql result.
		@since		20131105
	**/
	public static function sql( $result )
	{
		$result = (object) $result;
		$data = self::unserialize_data( $result->data );
		if ( ! $data )
			$data = [];
		$bcd = new BroadcastData( $data );
		$bcd->id = $result->id;
		$bcd->blog_id = $result->blog_id;
		$bcd->post_id = $result->post_id;
		return $bcd;
	}

	/**
	 * Returns the data array.
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
		@brief		Return the default data array.
		@since		20131107
	**/
	public static function get_default_data()
	{
		return [
			'version' => 2,
		];
	}

	/**
		@brief		Return the ID of the post on this blog, either parent or child.
		@since		2016-04-27 19:29:47
	**/
	public function get_linked_post_on_this_blog()
	{
		$blog_id = get_current_blog_id();

		// Can we offer the post of from which this BCD is built?
		if ( $this->blog_id == $blog_id )
			return $this->post_id;

		// Is there perhaps a child on the specified blog?
		$children = $this->get_linked_children();
		if ( isset( $children[ $blog_id ] ) )
			return $children[ $blog_id ];

		// How about the parent?
		$parent = $this->get_linked_parent();
		if ( $parent )
			if ( $parent[ 'blog_id' ] == $blog_id )
				return $parent[ 'post_id' ];

		// No matches at all. Return false.
		return false;
	}

	/**
	 * Does this post have any linked children?
	 */
	public function has_linked_children()
	{
		return isset($this->data[ 'linked_children' ]);
	}

	/**
	 * Does this post have children on the current blog?
	 *
	 * Used after switch_to_blog has been called.
	 */
	public function has_linked_child_on_this_blog( $blog__id = null )
	{
		global $blog_id;
		if ( $blog__id === null )
			$blog__id = $blog_id;
		return isset( $this->data[ 'linked_children' ][ $blog__id ] );
	}

	/**
	 * Return the post_id of the child post on the current blog.
	 */
	public function get_linked_child_on_this_blog()
	{
		global $blog_id;
		if ( $this->has_linked_child_on_this_blog() )
			return $this->data[ 'linked_children' ][$blog_id];
		else
			return null;
	}

	/**
	 * Returns an array of all the linked children.
	 *
	 * [blog_id] => [post_id]
	 */
	public function get_linked_children()
	{
		if (!$this->has_linked_children())
			return array();

		return $this->data[ 'linked_children' ];
	}

	/**
	 * Adds a linked child for this post.
	 * @param int $blog_id Blog ID
	 * @param int $post_id Post ID of child post
	 */
	public function add_linked_child( $blog_id, $post_id )
	{
		$this->data[ 'linked_children' ][$blog_id] = $post_id;
		$this->modified();
	}

	/**
	 * Removes a child from a blog.
	 */
	public function remove_linked_child( $blog_id )
	{
		unset($this->data[ 'linked_children' ][$blog_id]);
		if ( isset( $this->data[ 'linked_children' ] ) )
			if ( count($this->data[ 'linked_children' ]) < 1)
				unset( $this->data[ 'linked_children' ] );

		$this->modified();
	}

	/**
	 * Clears all the linked children.
	 */
	public function remove_linked_children()
	{
		unset($this->data[ 'linked_children' ]);
		$this->modified();
	}

	/**
	 * Remove linked parent
	 */
	public function get_linked_parent()
	{
		if (isset($this->data[ 'linked_parent' ]))
			return $this->data[ 'linked_parent' ];
		else
			return false;
	}

	/**
	 * Sets the parent post of this post.
	 * @param int $blog_id Blog ID
	 * @param int $post_id Post ID of child post
	 */
	public function set_linked_parent( $blog_id, $post_id )
	{
		$this->data[ 'linked_parent' ] = array('blog_id' => $blog_id, 'post_id' => $post_id);
		$this->modified();
	}

	/**
	 * Remove linked parent
	 */
	public function remove_linked_parent()
	{
		unset($this->data[ 'linked_parent' ]);
		$this->modified();
	}

	/**
	 * Flags the data as "modified".
	 */
	private function modified()
	{
		$this->dataModified = true;
	}

	/**
	 * Returns whether this broadcast data has been modified and needs to be saved.
	 */
	public function is_modified()
	{
		return $this->dataModified;
	}

	/**
	 * Returns whether the only data contained is worthless default data
	 */
	public function is_empty()
	{
		return (
			(count($this->data) == 1)
			&&
			( isset($this->data[ 'version' ]) )
		);
	}

	/**
		@brief		Try to unserialize the data from a string.
		@return		mixed		Either an array if all went well, or false.
		@since		20131105
	**/
	public static function unserialize_data( $data )
	{
		return @ unserialize( base64_decode( $data ) );
	}
}
