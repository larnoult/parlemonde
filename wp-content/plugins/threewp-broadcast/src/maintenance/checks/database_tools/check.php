<?php

namespace threewp_broadcast\maintenance\checks\database_tools;

use \threewp_broadcast\BroadcastData;

/**
	@brief		Various database tools.
	@since		2017-10-18 09:09:07
**/
class check
	extends \threewp_broadcast\maintenance\checks\check
{
	public function get_description()
	{
		// Maintenance check description
		return __( 'Various tools for manipulating the database.', 'threewp-broadcast' );
	}

	public function get_name()
	{
		// Maintenance check name
		return __( 'Database tools', 'threewp-broadcast' );
	}

	public function step_start()
	{
		$o = new \stdClass;
		$o->inputs = new \stdClass;
		$o->form = $this->broadcast()->form2();
		$o->r = '';

		global $wpdb;

		$table = ThreeWP_Broadcast()->table();
		$row = $table->head()->row();

		// Table head column
		$th = $row->th( 'key' )->text( __( 'Key', 'threewp-broadcast' ) );
		// Table head column
		$th = $row->th( 'value' )->text( __( 'Value', 'threewp-broadcast' ) );

		$row = $table->head()->row();
		$row->td( 'key' )->text( __( 'Auto drafts', 'threewp-broadcast' ) );
		$query = sprintf( "SELECT COUNT( * ) FROM `%s` WHERE `post_status` = 'auto-draft'", $wpdb->posts );
		$value = $wpdb->get_var( $query );
		$row->td( 'value' )->text( $value );

		$row = $table->head()->row();
		$row->td( 'key' )->text( __( 'Extra postmeta data', 'threewp-broadcast' ) );
		$query = sprintf( "SELECT COUNT( * ) FROM `%s` WHERE `post_id` NOT IN ( SELECT `ID` FROM `%s` )", $wpdb->postmeta, $wpdb->posts );
		$value = $wpdb->get_var( $query );
		$row->td( 'value' )->text( $value );

		$row = $table->head()->row();
		$row->td( 'key' )->text( __( 'Revisions', 'threewp-broadcast' ) );
		$query = sprintf( "SELECT COUNT( * ) FROM `%s` WHERE `post_type` = 'revision'", $wpdb->posts );
		$value = $wpdb->get_var( $query );
		$row->td( 'value' )->text( $value );

		$o->r .= $table;

		$o->r .= $o->form->open_tag();

		$delete_auto_drafts = $o->form->secondary_button( 'delete_auto_drafts' )
			// Button
			->value( __( 'Delete auto drafts', 'threewp-broadcast' ) );

		$delete_extra_postmeta = $o->form->secondary_button( 'delete_extra_postmeta' )
			// Button
			->value( __( 'Delete extra postmeta data', 'threewp-broadcast' ) );

		$delete_revisions = $o->form->secondary_button( 'delete_revisions' )
			// Button
			->value( __( 'Delete all revisions', 'threewp-broadcast' ) );

		if ( $o->form->is_posting() )
		{
			$o->form->post()->use_post_value();

			if ( $delete_extra_postmeta->pressed() )
			{
				$query = sprintf( "DELETE FROM `%s` WHERE `post_id` NOT IN ( SELECT `ID` FROM `%s` )",
					$wpdb->postmeta,
					$wpdb->posts
				);
				$this->broadcast()->debug( $query );
				$wpdb->query( $query );
				$o->r .= $this->broadcast()->info_message_box()
					->_( __( 'The postmeta table has been cleaned up.', 'threewp-broadcast' ) );
			}

			if ( $delete_auto_drafts->pressed() )
			{
				$query = sprintf( "DELETE FROM `%s` WHERE `post_status` = 'auto-draft'", $wpdb->posts );
				$this->broadcast()->debug( $query );
				$wpdb->query( $query );
				$o->r .= $this->broadcast()->info_message_box()
					->_( __( 'All auto drafts have been deleted.', 'threewp-broadcast' ) );
			}

			if ( $delete_revisions->pressed() )
			{
				$query = sprintf( "DELETE FROM `%s` WHERE `post_type` = 'revision'", $wpdb->posts );
				$this->broadcast()->debug( $query );
				$wpdb->query( $query );
				$o->r .= $this->broadcast()->info_message_box()
					->_( __( 'All revision posts have been deleted.', 'threewp-broadcast' ) );
			}
		}

		$o->r .= $o->form->open_tag();
		$o->r .= $o->form->display_form_table();
		$o->r .= $o->form->close_tag();
		return $o->r;
	}
}
