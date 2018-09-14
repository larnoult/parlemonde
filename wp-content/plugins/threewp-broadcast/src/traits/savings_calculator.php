<?php

namespace threewp_broadcast\traits;

/**
	@brief		Keep track of the time saved by using Broadcast.
	@details	None of this needs to be translated, since it's not critical.
	@since		2015-10-27 21:43:06
**/
trait savings_calculator
{
	/**
		@brief		admin_menu_savings
		@since		2015-10-27 21:46:06
	**/
	public function admin_menu_savings()
	{
		$data = $this->savings_data();
		$defaults = $data->get_defaults();

		$form = $this->form();
		$form->css_class( 'plainview_form_auto_tabs' );

		$r = '';

		$fs = $form->fieldset( 'fs_new_post' );
		// Fieldset legend for time savings. Timings for new posts.
		$fs->legend->label( __( 'New post time', 'threewp-broadcast' ) );

		$new_post_basic_setup = $fs->number( 'new_post_basic_setup' )
			// Desc for time savings
			->description( sprintf(
				__( "How many seconds it takes to switch blogs, start a new post and paste text into the title and content boxes. This varies depending on how efficient you are and how fast your webhost is. %s seconds is the default value.", 'threewp-broadcast' ),
				$defaults[ 'new_post_basic_setup' ]
				)
			)
			// Label for time savings
			->label( __( 'Basic setup', 'threewp-broadcast' ) )
			->min( 0 )
			->required()
			->size( 2, 5 )
			->value( $data->get_or_default( 'new_post_basic_setup' ) );

		$time_per_attachment = $fs->number( 'time_per_attachment' )
			// Desc for time savings
			->description( sprintf(
				__( "How many seconds to add for each image that is attached to the post. %s seconds is the default value to upload the image, caption it and then insert it into the post.", 'threewp-broadcast' ),
				$defaults[ 'time_per_attachment' ]
				)
			)
			// Label for time savings
			->label( __( 'Time per attachment', 'threewp-broadcast' ) )
			->min( 0 )
			->required()
			->size( 2, 5 )
			->value( $data->get_or_default( 'time_per_attachment' ) );

		$fs = $form->fieldset( 'fs_post_updates' );
		$fs->legend->label( __( 'Post update time', 'threewp-broadcast' ) );

		$updated_post_discount = $fs->number( 'updated_post_discount' )
			// Desc for time savings
			->description( sprintf(
				__( "How long, in percent, it takes to update a post, compared to created a brand new post. %s%% is a nice, round, number.", 'threewp-broadcast' ),
				$defaults[ 'updated_post_discount' ]
				)
			)
			// Label for time savings
			->label( __( 'Update time', 'threewp-broadcast' ) )
			->min( 0 )
			->max( 100 )
			->required()
			->size( 2, 3 )
			->value( $data->get_or_default( 'updated_post_discount' ) );

		$fs = $form->fieldset( 'fs_wage' );
		// Fieldset legend for time savings
		$fs->legend->label( __( 'Hourly wage', 'threewp-broadcast' ) );

		$hourly_wage = $fs->number( 'hourly_wage' )
			// Desc for time savings
			->description( __( "How much money per hour do you earn?", 'threewp-broadcast' ) )
			// Label for time savings
			->label( __( 'Hourly wage', 'threewp-broadcast' ) )
			->min( 0 )
			->required()
			->size( 5, 10 )
			->value( $data->get_or_default( 'hourly_wage' ) );

		$fs = $form->fieldset( 'fs_reset_savings' );
		// Fieldset legend for time savings
		$fs->legend->label( __( 'Reset time saved', 'threewp-broadcast' ) );

		$reset_savings = $fs->secondary_button( 'reset_savings' )
			// Button text
			->value( __( 'Reset the savings', 'threewp-broadcast' ) );

		$save = $form->primary_button( 'save' )
			// Button text
			->value( __( 'Save settings', 'threewp-broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			if ( $save->pressed() )
			{
				foreach( [
					'hourly_wage',
					'new_post_basic_setup',
					'time_per_attachment',
					'updated_post_discount',
				] as $key )
					$data->set( $key, $$key->get_filtered_post_value() );
				$data->save();
				// Message after updating savings calculator.
				$this->message( __( 'The values have been updated.', 'threewp-broadcast' ) );
			}

			if ( $reset_savings->pressed() )
			{
				$data->reset_stats();
				// Message after resetting savings calculator.
				$this->message( __( 'The savings have been reset.', 'threewp-broadcast' ) );
			}
		}

		$r .= $this->p( __( 'This time savings tracker guesses roughly how much time you have saved by using Broadcast instead of manually copying out posts.', 'threewp-broadcast' ) );

		$r .= $data->get_savings_table();

		$r .= $this->p( __( 'Use the form below to help Broadcast more correctly guess how much time you have saved.', 'threewp-broadcast' ) );

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Calculate how much time was spent writing this post.
		@since		2015-10-27 22:31:27
	**/
	public function savings_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;
		$data = $this->savings_data();

		// Make sure a value is saved.
		$since = $data->get( 'since', time() );
		$data->set( 'since', $since );

		$time = $data->get_or_default( 'new_post_basic_setup' );
		$this->debug( 'Calculator. Basic setup: %s seconds', $time );

		$attachments = $bcd->copied_attachments()->count();
		$time_per_attachment = $data->get_or_default( 'time_per_attachment' );
		$this->debug( 'Calculator. %s attachments @ %s seconds = %s seconds', $attachments, $time_per_attachment, ( $attachments * $time_per_attachment ) );
		$time += ( $time_per_attachment * $attachments  );

		// Was this an update?
		if ( $bcd->new_child_created )
		{
			$data->set( 'posts_created', $data->get( 'posts_created' ) + 1 );
		}
		else
		{
			// The child was updated. It gets the update discount.
			$discount = $data->get_or_default( 'updated_post_discount' );
			$this->debug( 'Calculator. Post update only costs %s %%.', $discount );
			$discount = $discount / 100;
			$time = ceil( $time * $discount );

			$data->set( 'posts_updated', $data->get( 'posts_updated' ) + 1 );
		}

		// Add it to the total time saved.
		$data->set( 'time_saved', $data->get( 'time_saved' ) + $time );
		$this->debug( 'Calculator. Time saved for this post: %s seconds', $time );

		$data->save();
	}

	/**
		@brief		Init the calculator.
		@since		2015-10-27 21:43:13
	**/
	public function savings_calculator_init()
	{
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog', 'savings_broadcasting_before_restore_current_blog' );
	}

	/**
		@brief		Add ourself to the tabs.
		@since		2015-10-28 10:37:50
	**/
	public function savings_calculator_tabs( $tabs )
	{
		$tabs->tab( 'savings' )
			->callback_this( 'admin_menu_savings' )
			->name( __( 'Time savings', 'threewp-broadcast' ) );
	}

	/**
		@brief		Load the savings calculator data.
		@since		2015-10-27 21:52:32
	**/
	public function savings_data()
	{
		return \threewp_broadcast\savings_calculator\Data::load();
	}
}
