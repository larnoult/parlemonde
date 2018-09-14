<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data\traits\steps;

trait step_results_fail_missing_post
{
	public function step_results_fail_missing_post( $o )
	{
		if ( count( $this->data->missing_post ) < 1 )
			return;

		$button = $o->form->primary_button( 'missing_post' )
			->value( 'Delete the broadcast data objects in the database' );

		if ( $button->pressed() )
		{
			// Delete all of the missing post broadcast datas.
			foreach( $this->data->missing_post as $id => $bcd )
			{
				$o->bc->sql_delete_broadcast_data( $bcd->id );
				$this->data->missing_post->forget( $bcd->id );
			}
			$o->bc->message( 'The broadcast data objects without existing posts have been deleted.' );
			return;
		}

		$o->r .= $o->bc->h3( 'Missing posts' );

		$o->r .= $o->bc->p( 'The following broadcast data objects belong to posts that no longer exist.' );
		$table = $o->bc->table();
		$row = $table->head()->row();
		$row->th()->text( 'Broadcast data row ID' );
		$row->th()->text( 'Belonging to post' );

		foreach( $this->data->missing_post as $id => $bcd )
		{
			$row = $table->body()->row();
			$row->td()->text_( $id );
			$row->td()->text_( 'Post %s on %s',
				$bcd->post_id,
				$this->blogname( $bcd->blog_id )
			);
		}

		$o->r .= $table;
		$o->r .= $o->bc->p( $button->display_input() );
	}
}