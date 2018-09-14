<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data;

use \plainview\sdk_broadcast\collections\collection;
use \threewp_broadcast\BroadcastData;

/**
	@brief		Check the broadcast data table.
	@since		20131104
**/
class check
extends \threewp_broadcast\maintenance\checks\check
{
	use traits\post_bcd_caching;
	use traits\steps\step_results_fail_duplicate_bcd;
	use traits\steps\step_results_fail_broken_bcd;
	use traits\steps\step_results_fail_missing_post;
	use traits\steps\step_results_fail_missing_parents;
	use traits\steps\step_results_fail_missing_children;
	use traits\steps\step_results_fail_parent_is_unlinked;
	use traits\steps\step_results_fail_same_parent;
	use traits\steps\step_results_fail_child_is_unlinked;
	use traits\steps\step_results_fail_unnecessary_children;

	public $table;

	// ----------------------------- //
	//		MISC					 //
	// ----------------------------- //

	/**
		@brief		Return the blogname.
		@details	I wish switch_to_blog could tell me that the blog doesn't exist.
		@since		2015-08-19 21:47:53
	**/
	public function blogname( $blog_id )
	{
		if ( ThreeWP_Broadcast()->blog_exists( $blog_id ) )
		{
			switch_to_blog( $blog_id );
			$r = sprintf( '%s %s', get_bloginfo( 'name' ), $blog_id );
			restore_current_blog();
		}
		else
			$r = 'Unknown ' . $blog_id;
		return $r;
	}

	/**
		@brief		Return a string with the blog and post linked.
		@details	I wish switch_to_blog could tell me that the blog doesn't exist.
		@since		2015-08-19 21:47:53
	**/
	public function blogpost( $blog_id, $post_id )
	{
		if ( ThreeWP_Broadcast()->blog_exists( $blog_id ) )
		{
			switch_to_blog( $blog_id );
			$post = get_post( $post_id );
			$r = sprintf( '<a href="%s">%s</a> on %s (%s)',
				get_permalink( $post_id ),
				$post->post_title,
				get_bloginfo( 'name' ),
				$blog_id
			);
			restore_current_blog();
		}
		else
			// Unknown BLOG_ID
			$r = __( 'Unknown ', 'threewp-broadcast' ) . $blog_id;
		return $r;
	}

	public static function data()
	{
		return new data;
	}

	public function get_actions()
	{
		$r = [];

		// Add a "view help" link
		$url = $this->get_action_url( 'view_help' );
		$r [ 'view_help' ]= sprintf(
			'%s%s%s',
			'<a href="' . $url . '">',
			__( 'View help and troubleshooting guide', 'threewp-broadcast' ),
			'</a>'
		);

		return $r;
	}
	public function get_description()
	{
		return __( 'Checks and optionally repairs missing broadcast data, parents and children found in the database table.', 'threewp-broadcast' );
	}

	public function get_name()
	{
		// Name of maintenance check
		return __( 'Broadcast data', 'threewp-broadcast' );
	}

	public function step_check()
	{
		$r = '';
		$r .= $this->next_step( 'results ');
		return $r;
	}

	public function step_check_relations()
	{
		if ( count( $this->data->bcd_to_check ) < 1 )
		{
			// Status text for maintenance check
			$r = $this->broadcast()->p( __( 'Finished checking relations. Showing results.', 'threewp-broadcast' ) );
			$r .= $this->next_step( 'results' );
			return $r;
		}

		$max = count( $this->data->bcd_to_check );
		$counter = min( data::$rows_per_step, $max );

		$r = $this->broadcast()->p(
			// the next 500 of 1000 relations
			__( 'Checking the next %s of %s relations.', 'threewp-broadcast' ),
			$counter,
			$max
		);

		// Check that each relation exists.
		foreach( $this->data->bcd_to_check as $id => $bcd )
		{
			$counter--;
			if ( $counter < 0 )
				break;

			$this->data->bcd_to_check->forget( $id );

			// If this is a child:
			$parent = $bcd->get_linked_parent();
			if ( $parent )
			{
				$blog_id = $parent[ 'blog_id' ];
				$post_id = $parent[ 'post_id' ];

				if ( count( $bcd->get_linked_children() ) > 0 )
					$this->data->unnecessary_children->set( $id, $bcd );

				// The parent blog + post must exist.
				if ( ! $this->blog_and_post_exists( $blog_id, $post_id ) )
				{
					$this->data->missing_parents->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				switch_to_blog( $bcd->blog_id );
				$child_post = get_post( $bcd->post_id );
				restore_current_blog();

				// And the posts must be the same.
				switch_to_blog( $blog_id );
				$parent_post = get_post( $post_id );
				restore_current_blog();

				// If the parent doesn't match the child, then the parent is missing.
				if (
					( $child_post->post_type != $parent_post->post_type )
				)
				{
					$this->data->missing_parents->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				// Does the parent link back to this child?
				// First, does the parent have any BCD at all?
				$parent_bcd = $this->lookup_post_bcd( $blog_id, $post_id );
				if ( ! $parent_bcd )
				{
					$this->data->parent_is_unlinked->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				// Keep track of which parents are referenced by the children.
				if ( ! $this->data->same_parent->has( $blog_id ) )
					$this->data->same_parent->set( $blog_id, new collection() );

				$blog = $this->data->same_parent->get( $blog_id );

				if ( ! $blog->has( $post_id ) )
					$blog->set( $post_id, new collection() );
				$post = $blog->get( $post_id );

				$o = (object)[];
				$o->parent_bcd = $parent_bcd;
				$o->blog_id = $bcd->blog_id;
				$o->post_id = $bcd->post_id;

				$post->set( $id, $o );
			}

			// If this is a parent:
			$children = $bcd->get_linked_children();
			if ( count( $children ) > 0 )
			{
				switch_to_blog( $bcd->blog_id );
				$parent_post = get_post( $bcd->post_id );
				restore_current_blog();
			}
			foreach( $children as $blog_id => $post_id )
			{
				// The child blog + post must exist.
				if ( ! $this->blog_and_post_exists( $blog_id, $post_id ) )
				{
					$this->data->missing_children->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				// And the posts must be the same.
				switch_to_blog( $blog_id );
				$child_post = get_post( $post_id );
				restore_current_blog();

				// If the parent doesn't match the child, then the parent is missing.
				if (
					( $child_post->post_type != $parent_post->post_type )
				)
				{
					$this->data->missing_children->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				// Does the child link back to this parent?
				// First, does the child have any BCD at all?
				$child_bcd = $this->lookup_post_bcd( $blog_id, $post_id );
				if ( ! $child_bcd )
				{
					$this->data->child_is_unlinked->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				$linked_parent = $child_bcd->get_linked_parent();
				// The child does have a bcd. Does it have a parent link?
				if ( ! $linked_parent )
				{
					$this->data->child_is_unlinked->set( $id, [ $blog_id => $post_id ] );
					continue;
				}

				// Does the child's bcd link back to this blog+post?
				if ( ( $linked_parent[ 'blog_id' ] != $bcd->blog_id ) && ( $linked_parent[ 'post_id' ] != $bcd->post_id ) )
				{
					$this->data->child_is_unlinked->set( $id, [ $blog_id => $post_id ] );
					continue;
				}
			}
		}

		$r .= $this->next_step( 'check_relations' );
		return $r;
	}

	public function step_results()
	{
		if ( $this->data->is_error_free() )
			return $this->step_results_ok();
		else
			return $this->step_results_fail();
	}

	public function step_results_ok()
	{
		return $this->broadcast()->p( __( 'No problems found with the broadcast data. Click on the maintenance tab to return to the maintenance overview.', 'threewp-broadcast' ) );
	}

	public function step_results_fail()
	{
		$o = new \stdClass;
		$o->bc = $this->broadcast();
		$o->form = $this->broadcast()->form2();
		$o->r = '';

		if ( $o->form->is_posting() )
			$o->form->post();

		if ( isset( $this->data->id_column_missing ) )
		{
			$this->broadcast()->create_broadcast_data_id_column();
			$o->r .= $this->broadcast()->p( __( 'The broadcast data table is missing the ID column. It should now have been created. Please rerun the test. If the test fails, then something is wrong with the database.', 'threewp-broadcast' ) );
		}

		$o->r .= $o->form->open_tag();
		$this->step_results_fail_duplicate_bcd( $o );
		$this->step_results_fail_broken_bcd( $o );
		$this->step_results_fail_missing_post( $o );
		$this->step_results_fail_missing_parents( $o );
		$this->step_results_fail_missing_children( $o );
		$this->step_results_fail_child_is_unlinked( $o );
		$this->step_results_fail_same_parent( $o );
		$this->step_results_fail_parent_is_unlinked( $o );
		$this->step_results_fail_unnecessary_children( $o );
		$o->r .= $o->form->close_tag();

		return $o->r;
	}

	public function step_check_ids()
	{
		$r = '';

		$max = count( $this->data->ids_to_check );
		if ( $max < 1 )
		{
			$r .= $this->broadcast()->p( __( 'Finished checking database rows. Now checking the actual broadcast data relations.', 'threewp-broadcast' ) );
			$r .= $this->next_step( 'check_relations' );
			return $r;
		}


		$r .= $this->broadcast()->p(
			__( '%s rows left to check...', 'threewp-broadcast' ),
			count( $this->data->ids_to_check )
		);

		// Check the next ids
		$max = min( $max, data::$rows_per_step );
		$ids = [];
		for ( $counter = 0; $counter < $max; $counter++ )
		{
			$id = array_shift( $this->data->ids_to_check );
			$query = sprintf( "SELECT * FROM `%s` WHERE `id` = '%s'", $this->table, $id );
			$results = $this->broadcast()->query( $query );
			$result = reset( $results );

			$bcd = BroadcastData::sql( $result );

			// Save the broadcast data in a quick index.
			$this->data->broadcast_data->put( $id, $bcd );

			// 1. Is the broadcast data readable?
			if ( ! BroadcastData::unserialize_data( $result[ 'data' ] ) )
			{
				$this->data->broken_bcd->set( $id, $bcd );
				continue;
			}

			// Broadcast data is readable and unserializable.
			$blog_id = $result[ 'blog_id' ];
			$post_id = $result[ 'post_id' ];

			// Have we already seen BCD for this blog_post?
			$key = sprintf( '%s_%s', $blog_id, $post_id );
			if ( $this->data->seen_blog_post->has( $key ) )
			{
				$this->data->duplicate_bcd->put( $id, $bcd );
				continue;
			}
			else
				$this->data->seen_blog_post->put( $key, true );

			// 2. Does the linked post exist?

			if ( ! $this->blog_and_post_exists( $blog_id, $post_id ) )
			{
				$this->data->missing_post->put( $id, $bcd );
				continue;
			}

			$this->cache_post_bcd( $blog_id, $post_id, $bcd );

			// We have now checked the SQL row itself.
			// The relations we check later.
			$this->data->bcd_to_check->put( $id, $bcd );
		}

		$r .= $this->next_step( 'check_ids' );
		return $r;
	}

	public function step_start()
	{
		$this->table = $this->broadcast()->broadcast_data_table();

		// Check that the table has an ID column at all.
		$query = sprintf( 'EXPLAIN `%s`', $this->table );
		$results = $this->broadcast()->query( $query );
		$found = false;
		foreach( $results as $result )
		{
			if ( $result[ 'Field' ] == 'id' )
				$found = true;
		}
		if ( ! $found )
		{
			$this->data->id_column_missing = true;;
			$r = $this->broadcast()->p( __( 'The Broadcast Data table is missing the ID column.', 'threewp-broadcast' ) );
			$r .= $this->next_step( 'results' );
			return $r;
		}

		// Count the rows in the table.
		$query = sprintf( 'SELECT `id` FROM `%s`', $this->table );
		$results = $this->broadcast()->query( $query );

		$this->data->ids = [];
		foreach( $results as $result )
			$this->data->ids []= $result[ 'id' ];

		$this->data->ids_to_check = $this->data->ids;

		$r = $this->broadcast()->p(
			__( 'Beginning to check broadcast data. %s rows to check.', 'threewp-broadcast' ),
			count( $this->data->ids_to_check )
		);
		$r .= $this->next_step( 'check_ids' );
		return $r;
	}

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

	public function view_help()
	{
		$r = $this->broadcast()->html_css();
		$filename = dirname( $this->broadcast()->paths[ '__FILE__' ] );
		$r .= file_get_contents( $filename . '/html/maintenance_broadcast_data.html' );
		return $r;
	}
}
