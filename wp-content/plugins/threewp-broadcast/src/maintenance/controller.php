<?php

namespace threewp_broadcast\maintenance;

class controller
{
	/**
		@brief		The ThreeWP Broadcast object.
		@since		20131101
	**/
	public $broadcast;

	public function __construct()
	{
		$this->broadcast = \threewp_broadcast\ThreeWP_Broadcast::instance();
	}

	public function __toString()
	{
		$this->data = data::load( $this );
		$r = '';

		if ( isset( $_GET[ 'check' ] ) )
		{
			$id = $_GET[ 'check' ];
			if ( ! $this->data->checks->has( $id ) )
				wp_die( 'Invalid check ID!' );

			$check = $this->data->checks->get( $id );

			if ( isset( $_GET[ 'action' ] ) )
			{
				$action = $_GET[ 'action' ];
				$actions = $check->get_actions();
				if ( ! isset( $actions[ $action ] ) )
					wp_die( 'Invalid check action!' );

				return call_user_func( [ $check, $action ] );
			}
		}
		if ( isset( $_GET[ 'do_check' ] ) )
		{
			$id = $_GET[ 'do_check' ];
			if ( $this->data->checks->has( $id ) )
			{
				$check = $this->data->checks->get( $id );
				if ( $check->step == 'start' )
					$r .= $check->init();
				$r .= $check->step();
				$this->data->save();
			}
			else
				wp_die( sprintf( 'Check %s does not exist!', $id ) );
		}
		else
			$r = $this->get_table();

		return $r;
	}

	/**
		@brief		Return the table showing all of the check types.
		@since		20131102
	**/
	public function get_table()
	{
		// Reset the data.
		$this->data = $this->data->reset();

		$form = $this->broadcast->form2();
		$r = '';
		$table = $this->broadcast->table();

		$row = $table->head()->row();
		// Table column header. Type of maintenance check.
		$row->th()->text( __( 'Check', 'threewp-broadcast' ) );
		// Table column header. Description of maintenance check.
		$row->th()->text( __( 'Description', 'threewp-broadcast' ) );
		// Table column header. Actions available for this maintenance check.
		$row->th()->text( __( 'Actions', 'threewp-broadcast' ) );

		foreach( $this->data->checks as $check )
		{
			$check->next_step( 'start' );
			$row = $table->body()->row();
			$name = sprintf( '<a href="%s">%s</a>',
				add_query_arg( [ 'do_check' => $check->get_id() ] ),
				$check->get_name()
			);
			$row->td()->text( $name );
			$row->td()->text( $check->get_description() );

			$actions = $check->get_actions();
			$text = [];
			foreach( $actions as $action )
				$text []= $action;
			$text = $this->broadcast->implode_html( $text );
			$row->td()->text( '<ul>' . $text . '</ul>' );
		}

		$this->data->save();

		$r .= $this->broadcast->p( __( 'This function allows the broadcast database to be checked and repaired. Make a backup of your Wordpress installation before using the repair functions.', 'threewp-broadcast' ) );

		$r .= $this->broadcast->p( __( 'Below is a table of available checks / tools. Click on the name of the check to use it.', 'threewp-broadcast' ) );

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $form->close_tag();

		return $r;
	}

	/**
		@brief		Return a URL for a check's action.
		@since		20131108
	**/
	public function get_action_url( $check, $action_name )
	{
		return add_query_arg( [
			'check' => $check->get_id(),
			'action' => $action_name,
		] );
	}
}
