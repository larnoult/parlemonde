<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data\traits\steps;

trait step_results_fail_missing_parents
{
	public function step_results_fail_missing_parents( $o )
	{
		if ( count( $this->data->missing_parents ) < 1 )
			return;

		$button = $o->form->primary_button( 'missing_parents' )
			->value( 'Remove the links to the non-existent parents' );

		if ( $button->pressed() )
		{
			foreach( $this->data->missing_parents as $id => $ignore )
			{
				// Remove the link to this non-existent parent.
				$bcd = $this->data->broadcast_data->get( $id );
				$bcd->remove_linked_parent();
				$o->bc->set_post_broadcast_data( $bcd->blog_id, $bcd->post_id, $bcd );
				$this->data->missing_parents->forget( $id );
			}
			$o->bc->message( 'The missing parents have been removed from the broadcast data.' );
			return;
		}

		$o->r .= $o->bc->h3( 'Missing parents' );

		$o->r .= $o->bc->p( 'The following broadcast data list a non-existent post as the parent.' );
		$table = $o->bc->table();
		$row = $table->head()->row();
		$row->th()->text( 'Broadcast data row ID' );
		$row->th()->text( 'Belonging to post' );
		$row->th()->text( 'Missing parent' );

		foreach( $this->data->missing_parents as $id => $blog_post )
		{
			$bcd = $this->data->broadcast_data->get( $id );
			$parent_blog_id = key( $blog_post );
			$parent_post_id = reset( $blog_post );
			$row = $table->body()->row();
			$row->td()->text_( $id );
			$row->td()->text_( $this->blogpost( $bcd->blog_id, $bcd->post_id ) );
			$row->td()->text_( 'Post %s on %s',
				$parent_post_id,
				$this->blogname( $parent_blog_id )
			);
		}

		$o->r .= $table;
		$o->r .= $o->bc->p( $button->display_input() );
	}
}
