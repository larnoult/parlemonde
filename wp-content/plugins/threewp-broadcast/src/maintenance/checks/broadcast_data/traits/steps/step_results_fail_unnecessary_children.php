<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data\traits\steps;

trait step_results_fail_unnecessary_children
{
	public function step_results_fail_unnecessary_children( $o )
	{
		if ( count( $this->data->unnecessary_children ) < 1 )
			return;

		$button = $o->form->primary_button( 'unnecessary_children' )
			->value( 'Remove the unnecessary children' );

		if ( $button->pressed() )
		{
			// Delete all of the missing post broadcast datas.
			foreach( $this->data->unnecessary_children as $id => $bcd )
			{
				$bcd->remove_linked_children();
				$o->bc->set_post_broadcast_data( $bcd->blog_id, $bcd->post_id, $bcd );
				$this->data->unnecessary_children->forget( $id );
			}
			$o->bc->message( 'The broadcast data has been cleared of unnecessary children.' );
			return;
		}

		$o->r .= $o->bc->h3( 'Unneccessary Children' );

		$o->r .= $o->bc->p( 'The following broadcast data objects belong to children but has other children linked unnecessarily.' );
		$table = $o->bc->table();
		$row = $table->head()->row();
		$row->th()->text( 'Broadcast data row ID' );
		$row->th()->text( 'Belonging to post' );

		foreach( $this->data->unnecessary_children as $id => $bcd )
		{
			$row = $table->body()->row();
			$row->td()->text_( $id );
			$row->td()->text_( 'Post %s on %s has %s extra children.',
				$bcd->post_id,
				$this->blogname( $bcd->blog_id ),
				count( $bcd->get_linked_children() )
			);
		}

		$o->r .= $table;
		$o->r .= $o->bc->p( $button->display_input() );
	}
}
