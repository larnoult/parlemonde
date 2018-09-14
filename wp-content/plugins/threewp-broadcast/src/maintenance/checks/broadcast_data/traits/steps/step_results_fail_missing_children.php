<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data\traits\steps;

trait step_results_fail_missing_children
{
	public function step_results_fail_missing_children( $o )
	{
		if ( count( $this->data->missing_children ) < 1 )
			return;

		$button = $o->form->primary_button( 'missing_children' )
			->value( 'Remove the links to the non-existent children' );

		if ( $button->pressed() )
		{
			foreach( $this->data->missing_children as $id => $blog_post )
			{
				$child_blog_id = key( $blog_post );
				// Remove the link to this non-existent child.
				$bcd = $this->data->broadcast_data->get( $id );
				$bcd->remove_linked_child( $child_blog_id );
				$o->bc->set_post_broadcast_data( $bcd->blog_id, $bcd->post_id, $bcd );
				$this->data->missing_children->forget( $id );
			}
			$o->bc->message( 'The missing children have been removed from the broadcast data.' );
			return;
		}

		$o->r .= $o->bc->h3( 'Missing children' );

		$o->r .= $o->bc->p( 'The following broadcast data list a non-existent post as a child.' );
		$table = $o->bc->table();
		$row = $table->head()->row();
		$row->th()->text( 'Broadcast data row ID' );
		$row->th()->text( 'Belonging to post' );
		$row->th()->text( 'Missing child' );

		foreach( $this->data->missing_children as $id => $blog_post )
		{
			$bcd = $this->data->broadcast_data->get( $id );
			$child_blog_id = key( $blog_post );
			$child_post_id = reset( $blog_post );
			$row = $table->body()->row();
			$row->td()->text_( $id );
			$row->td()->text_( $this->blogpost( $bcd->blog_id, $bcd->post_id ) );
			$row->td()->text_( 'Post %s on %s',
				$child_post_id,
				$this->blogname( $child_blog_id )
			);
		}

		$o->r .= $table;
		$o->r .= $o->bc->p( $button->display_input() );
	}
}
