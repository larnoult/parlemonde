<?php

namespace threewp_broadcast\maintenance\checks\view_broadcast_data;

use \threewp_broadcast\BroadcastData;

/**
	@brief		View individual broadcast data objects
	@since		20131107
**/
class check
extends \threewp_broadcast\maintenance\checks\check
{
	public function get_description()
	{
		// Maintenance check description
		return 'View individual broadcast data objects. This tool does not modify the database.';
	}

	public function get_name()
	{
		// Maintenance check name
		return __( 'View broadcast data', 'threewp-broadcast' );
	}

	public function step_start()
	{
		$o = new \stdClass;
		$o->inputs = new \stdClass;
		$o->form = $this->broadcast()->form2();
		$o->r = ThreeWP_Broadcast()->p( __( 'Use the form below to look up the broadcast data (linking) either by specifying the ID of the row in the database or the combination of blog ID and post ID. Leave the row input empty to look up using blog and post IDs.', 'threewp-broadcast' ) );

		$fs = $o->form->fieldset( 'fs_by_id' );
		// Fieldset label - broadcast data is linking data
		$fs->legend->label( __( 'Find broadcast data by ID', 'threewp-broadcast' ) );

		$o->inputs->row_id = $fs->number( 'id' )
			// Input title
			->description( __( 'The ID of the row in the database table.', 'threewp-broadcast' ) )
			// Input label
			->label( 'ID' );

		$fs = $o->form->fieldset( 'fs_by_blog_and_post' );
		// Fieldset label - broadcast data is linking data
		$fs->legend->label( __( 'Find broadcast data by blog and post ID', 'threewp-broadcast' ) );

		$o->inputs->blog_id = $fs->number( 'blog_id' )
			->description( __( 'The ID of the blog. The current blog is the default.', 'threewp-broadcast' ) )
			->label( __( 'Blog ID', 'threewp-broadcast' ) )
			->value( get_current_blog_id() );

		$o->inputs->post_id = $fs->number( 'post_id' )
			->description( __( 'The ID of the post.', 'threewp-broadcast' ) )
			->label( __( 'Post ID', 'threewp-broadcast' ) )
			->value( '' );

		$button = $o->form->primary_button( 'dump' )
			// Button - broadcast data is link data
			->value( __( 'Find and display the broadcast data', 'threewp-broadcast' ) );

		if ( $o->form->is_posting() )
		{
			$o->form->post()->use_post_value();

			if ( $o->inputs->row_id->get_post_value() != '' )
				$this->handle_row_id( $o );
			else
				$this->handle_blog_and_post_id( $o );
		}

		$o->r .= $o->form->open_tag();
		$o->r .= $o->form->display_form_table();
		$o->r .= $o->form->close_tag();
		return $o->r;
	}

	public function handle_row_id( $o )
	{
		$row_id = $o->inputs->row_id->get_value();
		if ( $row_id < 1 )
			return;

		$table = $this->broadcast()->broadcast_data_table();
		$query = sprintf( "SELECT * FROM `%s` WHERE `id` = '%s'", $table, $row_id );
		$o->results = $this->broadcast()->query( $query );
		if ( count( $o->results ) !== 1 )
		{
			$o->r .= $this->broadcast()->error_message_box()->_( __( 'Row %s in the broadcast data table was not found!', 'threewp-broadcast' ), $row_id );
			return;
		}

		// Try to unserialize the object.
		$result = reset( $o->results );
		$bcd = BroadcastData::sql( $result );

		$text = sprintf( '<pre>%s</pre>', var_export( $bcd, true ) );
		$o->r .= $this->broadcast()->info_message_box()->_( $text );
	}

	public function handle_blog_and_post_id( $o )
	{
		$blog_id = intval( $o->inputs->blog_id->get_value() );
		$post_id = intval( $o->inputs->post_id->get_value() );

		if ( $blog_id < 1 )
			$blog_id = get_current_blog_id();

		$bcd = ThreeWP_Broadcast()->get_post_broadcast_data( $blog_id, $post_id );
		$text = sprintf( '<pre>%s</pre>', var_export( $bcd, true ) );
		$o->r .= $this->broadcast()->info_message_box()->_( $text );
	}
}
