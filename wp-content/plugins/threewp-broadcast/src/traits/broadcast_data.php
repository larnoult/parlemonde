<?php

namespace threewp_broadcast\traits;

use threewp_broadcast\broadcast_data as data;			// Else if conflicts with the trait name. *sigh*

/**
	@brief		Methods related to broadcast data.
	@since		2014-10-19 15:44:39
**/
trait broadcast_data
{
	/**
		@brief		Returns the current broadcast_data cache object.
		@return		broadcast_data\\cache		A newly-created or old cache object.
		@since		201301009
	**/
	public function broadcast_data_cache()
	{
		$property = 'broadcast_data_cache';
		if ( ! property_exists( $this, 'broadcast_data_cache' ) )
			$this->$property = new \threewp_broadcast\broadcast_data\cache;
		return $this->$property;
	}

	/**
		@brief		Returns the name of the broadcast data table.
		@since		20131104
	**/
	public function broadcast_data_table()
	{
		return $this->wpdb->base_prefix . '_3wp_broadcast_broadcastdata';
	}

	/**
		Deletes the broadcast data completely of a post in a blog.
	*/
	public function delete_post_broadcast_data( $blog_id, $post_id)
	{
		$action = $this->new_action( 'delete_post_broadcast_data' );
		$action->blog_id = $blog_id;
		$action->post_id = $post_id;
		$action->execute();

		if ( $action->is_finished() )
			return;

		$this->broadcast_data_cache()->set_for( $blog_id, $post_id, new data );
		$this->sql_delete_broadcast_data( $blog_id, $post_id );
	}

	/**
		@brief		Convenience method to return the broadcast data of the linked parent (if any).
		@details	Will retrieve the broadcast data of this blogid / postid. If it is a linked child, will return the parent's broadcast data.

		Note that it is extra convenient by leaving the $post_id optional, meaning you can use the $blog_id as the $post_id, if the post is on this blog.
		@since		2016-07-12 19:58:52
	**/
	public function get_parent_post_broadcast_data( $blog_id, $post_id = null )
	{
		if ( $post_id === null )
		{
			$post_id = $blog_id;
			$blog_id = get_current_blog_id();
		}

		$bcd = $this->get_post_broadcast_data( $blog_id, $post_id );

		// Is this a child? Retrieve the parent's bcd.
		$parent = $bcd->get_linked_parent();
		if ( $parent !== false )
			$bcd = $this->get_post_broadcast_data( $parent[ 'blog_id' ], $parent[ 'post_id' ] );

		return $bcd;
	}

	/**
	 * Retrieves the BroadcastData for this post_id.
	 *
	 * Will return a fully functional BroadcastData class even if the post doesn't have BroadcastData.
	 *
	 * Use BroadcastData->is_empty() to check for that.
	 * @param int $post_id Post ID to retrieve data for.
	 */
	public function get_post_broadcast_data( $blog_id, $post_id )
	{
		$action = $this->new_action( 'get_post_broadcast_data' );
		$action->blog_id = $blog_id;
		$action->post_id = $post_id;
		$action->execute();

		if ( $action->is_finished() )
			return $action->broadcast_data;

		return $this->broadcast_data_cache()->get_for( $blog_id, $post_id );
	}

	/**
	 * Updates / removes the BroadcastData for a post.
	 *
	 * If the BroadcastData->is_empty() then the BroadcastData is removed completely.
	 *
	 * @param int $blog_id Blog ID to update
	 * @param int $post_id Post ID to update
	 * @param BroadcastData $broadcast_data BroadcastData file.
	 */
	public function set_post_broadcast_data( $blog_id, $post_id, $broadcast_data )
	{
		// Update the cache.
		$this->broadcast_data_cache()->set_for( $blog_id, $post_id, $broadcast_data );

		$action = $this->new_action( 'set_post_broadcast_data' );
		$action->blog_id = $blog_id;
		$action->post_id = $post_id;
		$action->broadcast_data = $broadcast_data;
		$action->execute();

		if ( $action->is_finished() )
			return;

		if ( $broadcast_data->is_modified() )
			if ( $broadcast_data->is_empty() )
				$this->sql_delete_broadcast_data( $blog_id, $post_id );
			else
				$this->sql_update_broadcast_data( $blog_id, $post_id, $broadcast_data );
	}

	/**
		@brief		Returns an array of SQL rows for these post_ids.
		@param		int		$blog_id		ID of blog for which to fetch the datas
		@param		mixed	$post_ids		An array of ints or a string signifying which datas to retrieve.
		@return		array					An array of database rows. Each row has a BroadcastData object in the data column.
		@since		20131009
	**/
	public function sql_get_broadcast_datas( $blog_id, $post_ids )
	{
		if ( ! is_array( $post_ids ) )
			$post_ids = [ $post_ids ];

		$query = sprintf( "SELECT * FROM `%s` WHERE `blog_id` = '%s' AND `post_id` IN ('%s') ORDER BY `id`",
			$this->broadcast_data_table(),
			$blog_id,
			implode( "', '", $post_ids )
		);
		$results = $this->query( $query );
		foreach( $results as $index => $result )
			$results[ $index ][ 'data' ] = data::sql( $result );
		return $results;
	}

	/**
		@brief		Delete broadcast data.
		@details	If $post_id is not used, then the $blog_id is assumed to be just the row ID.

		If $post_id is used, then $blog_id is the actual $blog_id.
		@since		20131105
	**/
	public function sql_delete_broadcast_data( $blog_id, $post_id = null )
	{
		if ( $post_id === null )
			$query = sprintf( "DELETE FROM `%s` WHERE `id` = '%s'",
				$this->broadcast_data_table(),
				$blog_id
			);
		else
			$query = sprintf( "DELETE FROM `%s` WHERE blog_id = '%s' AND post_id = '%s'",
				$this->broadcast_data_table(),
				$blog_id,
				$post_id
			);
		$this->query( $query );
	}

	public function sql_update_broadcast_data( $blog_id, $post_id, $bcd )
	{
		$data = serialize( $bcd->getData() );
		$data = base64_encode( $data );

		if ( $bcd->id > 0 )
		{
			$query = sprintf( "UPDATE `%s` SET `data` = '%s' WHERE `id` = '%s'",
				$this->broadcast_data_table(),
				$data,
				$bcd->id
			);
			$this->query( $query );
		}
		else
		{
			$query = sprintf( "INSERT INTO `%s` (blog_id, post_id, data) VALUES ( '%s', '%s', '%s' )",
				$this->broadcast_data_table(),
				$blog_id,
				$post_id,
				$data
			);
			$bcd->blog_id = $blog_id;
			$bcd->post_id = $post_id;
			$bcd->id = $this->query_insert_id( $query );
		}
	}

	/**
		@brief		Switch the broadcast data of two posts.
		@details	Broadcast will automatically figure out with which post to switch the data.
		@since		2017-05-27 20:40:32
	**/
	public function switch_broadcast_data( $blog_or_post_id, $post_id = null )
	{
		if ( ! $post_id )
		{
			$post_id = $blog_or_post_id;
			$blog_or_post_id = get_current_blog_id();
		}
		$blog_id = $blog_or_post_id;

		// Retrieve the bcd.
		$bcd = $this->get_post_broadcast_data( $blog_id, $post_id );

		// Is this a parent bcd?
		$parent = $bcd->get_linked_parent();
		if ( ! $parent )
		{
			$old_parent = [ $bcd->blog_id, $bcd->post_id ];
			// Use the first child.
			$old_child = [ key( $bcd->get_linked_children() ), reset( $bcd->get_linked_children() ) ];

		}
		else
		{
			$old_parent = [ $parent[ 'blog_id' ], $parent[ 'post_id' ] ];
			$old_child = [ $blog_id, $post_id ];
		}

		$this->debug( 'Switching broadcast data: %s with %s', $old_parent, $old_child );

		$this->delete_post_broadcast_data( $old_parent[ 0 ], $old_parent[ 1 ] );
		$this->delete_post_broadcast_data( $old_child[ 0 ], $old_child[ 1 ] );

		// Make the parent BCD.
		$new_bcd = $this->get_post_broadcast_data( $old_child[ 0 ], $old_child[ 1 ] );
		$new_bcd->add_linked_child( $old_parent[ 0 ], $old_parent[ 1 ] );
		$this->set_post_broadcast_data( $old_child[ 0 ], $old_child[ 1 ], $new_bcd );

		// And the child BCD.
		$new_bcd = $this->get_post_broadcast_data( $old_parent[ 0 ], $old_parent[ 1 ] );
		$new_bcd->set_linked_parent( $old_child[ 0 ], $old_child[ 1 ] );
		$this->set_post_broadcast_data( $old_parent[ 0 ], $old_parent[ 1 ], $new_bcd );
	}
}
