<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data\traits\steps;

trait step_results_fail_child_is_unlinked
{
	public function step_results_fail_child_is_unlinked( $o )
	{
		if ( count( $this->data->child_is_unlinked ) < 1 )
			return;

		$button = $o->form->primary_button( 'child_is_unlinked' )
			->value( 'Link the child posts to their parent posts' );

		if ( $button->pressed() )
		{
			foreach( $this->data->child_is_unlinked as $id => $blog_post )
			{
				// Create a link from all of the child posts back to this parent.
				$bcd = $this->data->broadcast_data->get( $id );
				$child_blog_id = key( $blog_post );
				$child_post_id = reset( $blog_post );
				$child_bcd = $o->bc->get_post_broadcast_data( $child_blog_id, $child_post_id );
				$child_bcd->set_linked_parent( $bcd->blog_id, $bcd->post_id );
				$o->bc->set_post_broadcast_data( $child_blog_id, $child_post_id, $child_bcd );
				$this->data->child_is_unlinked->forget( $id );
			}
			$o->bc->message( 'The children now have links back to the parents.' );
			return;
		}

		$o->r .= $o->bc->h3( 'Unlinked children' );

		$o->r .= $o->bc->p( 'The following child posts are not linked back to the parent.' );
		$table = $o->bc->table();
		$row = $table->head()->row();
		$row->th()->text( 'Broadcast data row ID' );
		$row->th()->text( 'Belonging to post' );
		$row->th()->text( 'Child without link' );

		foreach( $this->data->child_is_unlinked as $id => $blog_post )
		{
			$bcd = $this->data->broadcast_data->get( $id );
			$child_blog_id = key( $blog_post );
			$child_post_id = reset( $blog_post );
			$row = $table->body()->row();
			$row->td()->text_( $id );
			$row->td()->text_( $this->blogpost( $bcd->blog_id, $bcd->post_id ) );
			$row->td()->text_( $this->blogpost( $child_blog_id, $child_post_id ) );
		}

		$o->r .= $table;
		$o->r .= $o->bc->p( $button->display_input() );
	}
}
