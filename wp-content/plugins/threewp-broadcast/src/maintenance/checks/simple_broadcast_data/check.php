<?php

namespace threewp_broadcast\maintenance\checks\simple_broadcast_data;

use \plainview\sdk_broadcast\collections\collection;
use \threewp_broadcast\BroadcastData;

/**
	@brief		Simplified Broadcast Data check for large databases.
	@since		2018-07-19 08:58:31
**/
class check
	extends \threewp_broadcast\maintenance\checks\check
{
	/**
		@brief		Check that this blog and post combo exists.
		@since		2018-07-19 09:48:57
	**/
	public function blog_and_post_exists( $blog_id, $post_id )
	{
		if ( ! ThreeWP_Broadcast()->blog_exists( $blog_id ) )
			return false;

		switch_to_blog( $blog_id );
		$post = get_post( $post_id );
		$ok = ( $post !== null );
		restore_current_blog();

		return $ok;
	}

	/**
		@brief		The check's description.
		@since		2018-07-19 09:49:06
	**/
	public function get_description()
	{
		return __( 'Lighter, simpler version of the Broadcast Data check for very large installs.', 'threewp-broadcast' );
	}

	/**
		@brief		The check's name.
		@since		2018-07-19 09:49:06
	**/
	public function get_name()
	{
		// Name of maintenance check
		return __( 'Broadcast data (simple)', 'threewp-broadcast' );
	}

	/**
		@brief		Check each SQL row.
		@since		2018-07-19 09:49:21
	**/
	public function step_check_rows()
	{
		$r = '';

		global $wpdb;
		$table = $this->broadcast()->broadcast_data_table();
		$query = sprintf( 'SELECT * FROM `%s` LIMIT %d OFFSET %d',
			$table,
			$this->data->per_page,
			$this->data->counter
		);
		$rows = $wpdb->get_results( $query );

		$r .= wpautop( sprintf( 'Checking from row %d.', $this->data->counter ) );

		foreach( $rows as $row )
		{
			$delete_query = sprintf( "DELETE FROM `%s` WHERE `id` = %d", $table, $row->id );

			if ( ! $this->blog_and_post_exists( $row->blog_id, $row->post_id ) )
			{
				$r .= wpautop( sprintf( "Row %d: Blog %d post %d no longer exists. Deleting row.", $row->id, $row->blog_id, $row->post_id ) );
				$wpdb->get_results( $delete_query );
				continue;
			}

			// Check the broadcast data.
			$r .= wpautop( sprintf( "Row %d: Checking BCD for post %d / %d.", $row->id, $row->blog_id, $row->post_id ) );
			$bcd = ThreeWP_Broadcast()->get_post_broadcast_data( $row->blog_id, $row->post_id );
			if ( $bcd->has_linked_children() )
			{
				foreach( $bcd->get_linked_children() as $blog_id => $post_id )
				{
					if ( ! $this->blog_and_post_exists( $blog_id, $post_id ) )
					{
						$r .= wpautop( sprintf( "Row %d: Child post %d / %d no longer exists. Updating BCD.", $row->id, $blog_id, $post_id ) );
						$bcd->remove_linked_child( $blog_id );
					}
				}
				ThreeWP_Broadcast()->set_post_broadcast_data( $row->blog_id, $row->post_id, $bcd );
			}
			else
			{
				$parent = $bcd->get_linked_parent();
				if ( ! $this->blog_and_post_exists( $parent[ 'blog_id' ], $parent[ 'post_id' ] ) )
				{
					$r .= wpautop( sprintf( "Row %d: Parent post %d / %d no longer exists. Deleting row.", $row->id, $parent[ 'blog_id' ], $parent[ 'post_id' ] ) );
					$wpdb->get_results( $delete_query );
				}
			}
		}

		if ( $this->data->counter > $this->data->count )
			$r .= wpautop( sprintf( 'Finished checking %d rows.', $this->data->counter ) );
		else
		{
			$this->data->counter += $this->data->per_page;
			$r .= $this->next_step( 'check_rows' );
		}

		return $r;
	}

	/**
		@brief		Initialize.
		@since		2018-07-19 09:19:50
	**/
	public function step_start()
	{
		global $wpdb;
		$table = $this->broadcast()->broadcast_data_table();
		$query = sprintf( 'SELECT COUNT(*) as row_count FROM `%s`', $table );
		$count = $wpdb->get_var( $query );

		$this->data->counter = 0;
		$this->data->count = $count;
		$this->data->per_page = 100;

		$r = $this->broadcast()->p(
			__( 'Beginning to check broadcast data. %s SQL rows to check.', 'threewp-broadcast' ),
			$count
		);
		$r .= $this->next_step( 'check_rows' );
		return $r;
	}
}
