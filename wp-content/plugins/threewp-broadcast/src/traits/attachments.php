<?php

namespace threewp_broadcast\traits;

use threewp_broadcast\attachment_data;

use \Exception;

trait attachments
{
	/**
		@brief		Init the attachments trait.
		@since		2015-11-16 13:45:48
	**/
	public function attachments_init()
	{
		$this->add_action( 'threewp_broadcast_apply_existing_attachment_action', 100 );
		$this->add_action( 'threewp_broadcast_copy_attachment', 100 );
		$this->add_action( 'threewp_broadcast_get_existing_attachment_actions', 5 );
	}

	/**
		@brief		Copy the attachments from the parent to the child.
		@since		2016-07-22 16:50:54
	**/
	public function copy_attachments_to_child( $bcd )
	{
		if ( $bcd->copied_attachments_to_blog->has( get_current_blog_id() ) )
			return $this->debug( 'Already copied attachments to child blog.' );

		$bcd->copied_attachments_to_blog->set( get_current_blog_id(), true );

		$this->debug( 'Copying attachments to the child blog.' );

		$this->debug( 'Looking through %s attachments.', count( $bcd->attachment_data ) );
		foreach( $bcd->attachment_data as $key => $attachment )
		{
			$o = clone( $bcd );
			$o->attachment_data = clone( $attachment );
			$o->attachment_data->post = clone( $attachment->post );
			$this->debug( "The attachment's post parent is %s.", $o->attachment_data->post->post_parent );
			if ( $o->attachment_data->is_attached_to_parent() )
			{
				$this->debug( 'Assigning new post parent ID (%s) to attachment %s.', $bcd->new_post( 'ID' ), $o->attachment_data->post->ID );
				$o->attachment_data->post->post_parent = $bcd->new_post( 'ID' );
			}
			else
			{
				$this->debug( 'Resetting post parent for attachment %s.', $o->attachment_data->post->ID );
				$o->attachment_data->post->post_parent = 0;
			}
			$this->maybe_copy_attachment( $o );

			try
			{
				// Preserve all of the attachment data.
				$copied_attachment = get_post( $o->attachment_id );
				$copied_attachment->attachment_data = attachment_data::from_attachment_id( $copied_attachment );

				// Yepp, reverse reference in order to preserve the attachment data for future use.
				$attachment->post->attachment_data = $attachment;

				$bcd->copied_attachments()->add( $attachment->post, $copied_attachment );
				$this->debug( 'Copied attachment %s to %s', $attachment->post->ID, $o->attachment_id );
			}
			catch ( Exception $e )
			{
				$this->debug( 'Error while copying the attachment: %s', $e->getMessage() );
			}
		}
	}

	/**
		@brief		threewp_broadcast_apply_existing_attachment_action
		@since		2015-11-16 14:10:32
	**/
	public function threewp_broadcast_apply_existing_attachment_action( $action )
	{
		if ( $action->is_finished() )
			return;

		$bcd = $action->broadcasting_data;

		switch( $action->action )
		{
			case 'overwrite':
				// Delete the existing attachment
				$this->debug( 'Maybe copy attachment: Deleting current attachment %s', $action->target_attachment->ID );
				wp_delete_attachment( $action->target_attachment->ID, true );		// true = Don't go to trash
				// Tell BC to copy the attachment.
				$action->use = false;
				break;
			case 'randomize':
				$filename = $bcd->attachment_data->filename_base;
				$filename = preg_replace( '/(.*)\./', '\1_' . rand( 1000000, 9999999 ) .'.', $filename );
				$bcd->attachment_data->filename_base = $filename;
				$this->debug( 'Maybe copy attachment: Randomizing new attachment filename to %s.', $bcd->attachment_data->filename_base );
				// Tell BC to copy the attachment.
				$action->use = false;
				break;
		}
	}

	/**
		@brief		Creates a new attachment.
		@details

		The $o object is an extension of Broadcasting_Data and must contain:
		- @i attachment_data An attachment_data object containing the attachment info.

		@param		object		$o		Options.
		@return		@i int The attachment's new post ID.
		@since		20130530
		@version	20131003
	*/
	public function threewp_broadcast_copy_attachment( $action )
	{
		if ( $action->is_finished() )
			return;

		$attachment_data = $action->attachment_data;
		$is_url = $attachment_data->is_url();			// Convenience. Shorter.
		$source = $attachment_data->filename_path;

		if ( $is_url )
		{
			$this->debug( 'Copy attachment: File "%s" is an external URL', $source );
		}
		else
		{
			if ( file_exists( $source ) )
				$this->debug( 'Copy attachment: File "%s" is on local file-system', $source );
			else
			{
				// File does not exist.
				$this->debug( 'Copy attachment: File "%s" does not exist!', $source );
				return false;
			}
		}

		// Copy the file to the blog's upload directory
		$upload_dir = wp_upload_dir();

		$target = $upload_dir[ 'path' ] . '/' . $attachment_data->filename_base;

		if( ! $attachment_data->is_url() )
		{
			// Only copy the file if it is local.
			$this->debug( 'Copy attachment: Copying from %s to %s', $source, $target );
			copy( $source, $target );
			$this->debug( 'Copy attachment: File sizes: %s %s ; %s %s', $source, filesize( $source ), $target, filesize( $target ) );
			$target_path = $target;
			$new_guid = $upload_dir[ 'url' ] . '/' . $attachment_data->filename_base;
		}
		else
		{
			// PW 24/04/2015 - for files with a remote source we will just create a reference in the media manager, no need to download.
			$target = $source;
			// PW 30/04/2015 - not accurate but required for wp_generate_attachment_metadata
			$target_path = $upload_dir[ 'path' ] . '/' . $attachment_data->filename_base;

			// In the case of URLs... use the existing URL.
			$new_guid = $attachment_data->post->guid;
		}

		// And now create the attachment stuff.
		// This is taken almost directly from http://codex.wordpress.org/Function_Reference/wp_insert_attachment
		$this->debug( 'Copy attachment: Checking filetype.' );
		$wp_filetype = wp_check_filetype( $target, null );
		$attachment = [
			'guid' => $new_guid,
			'menu_order' => $attachment_data->post->menu_order,
			'post_author' => $attachment_data->post->post_author,
			'post_excerpt' => $attachment_data->post->post_excerpt,
			'post_mime_type' => $wp_filetype[ 'type' ],
			'post_name' => $attachment_data->post->post_name,
			'post_title' => $attachment_data->post->post_title,
			'post_content' => $attachment_data->post->post_content,
			'post_status' => 'inherit',
		];
		$this->debug( 'Copy attachment: Inserting attachment: %s', $attachment );
		$new_attachment_id = wp_insert_attachment( $attachment, $target, $attachment_data->post->post_parent );
		$action->set_attachment_id( $new_attachment_id );

		// Now set the post name to what it should be.
		global $wpdb;
		$query = sprintf( "UPDATE `%s` SET `post_name` = '%s' WHERE `ID` = %s",
			$wpdb->posts,
			$attachment_data->post->post_name,
			$new_attachment_id
		);
		$this->debug( 'Renaming attachment to match original: %s', $query );
		$this->query( $query );

		// Now to maybe handle the metadata.
		if ( ! $is_url )
		{
			if ( $attachment_data->file_metadata )
			{
				$this->debug( 'Copy attachment: Handling metadata.' );
				// 1. Create new metadata for this attachment.
				$this->debug( 'Copy attachment: Requiring image.php.' );
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				$this->debug( 'Copy attachment: Generating metadata for %s.', $target );
				$attach_data = wp_generate_attachment_metadata( $action->attachment_id, $target_path );
				$this->debug( 'Copy attachment: Metadata is %s', $attach_data );

				// 2. Write the old metadata first.

				foreach( $attachment_data->post_custom as $key => $value )
				{
					if ( $key == '_wp_attached_file' )
						continue;
					$value = reset( $value );
					$value = maybe_unserialize( $value );
					update_post_meta( $action->attachment_id, $key, $value );
				}

				// 3. Overwrite the metadata that needs to be overwritten with fresh data.
				$this->debug( 'Copy attachment: Updating metadata.' );
				wp_update_attachment_metadata( $action->attachment_id, $attach_data );
			}
		}
		else
		{
			// Copy all of the metadata straight off.
			$this->debug( 'Copy attachment: Directly copying all metadata.' );
			foreach( $attachment_data->post_custom as $key => $value )
			{
				// Don't overwrite this key since it probably isn't uploaded to the same directory.
				if ( $key == '_wp_attached_file' )
					continue;
				$value = reset( $value );
				$value = maybe_unserialize( $value );
				update_post_meta( $action->attachment_id, $key, $value );
			}
		}

		if ( ! $is_url )
			$this->debug( 'Copy attachment: File sizes again: %s %s ; %s %s', $source, filesize( $source ), $target, filesize( $target ) );
		$action->finish();
	}

	/**
		@brief		threewp_broadcast_get_attachment_actions
		@since		2015-11-16 13:46:23
	**/
	public function threewp_broadcast_get_existing_attachment_actions( $action )
	{
		// Existing attachment action.
		$s = __( 'Use the existing attachment on the child blog', 'threewp-broadcast' );
		$action->add( 'use', $s );

		// Existing attachment action.
		$s = __( 'Delete and then recopy the attachment', 'threewp-broadcast' );
		$action->add( 'overwrite', $s );

		// Existing attachment action.
		$s = __( 'Create a new attachment with a randomized suffix', 'threewp-broadcast' );
		$action->add( 'randomize', $s );
	}

	/**
		@brief		Will only copy the attachment if it doesn't already exist on the target blog.
		@details	The return value is an object, with the most important property being ->attachment_id.

		@param		object		$options		See the parameter for copy_attachment.
		@return		object		$options		The attachment_id property should be > 0.
	**/
	public function maybe_copy_attachment( $options )
	{
		$attachment_data = $options->attachment_data;		// Convenience.

		$key = get_current_blog_id();

		// Start by assuming no attachments.
		$attachment_posts = [];

		global $wpdb;
		// The post_name is the important part.
		$query = sprintf( "SELECT `ID` FROM `%s` WHERE `post_type` = 'attachment' AND `post_name` = '%s'",
			$wpdb->posts,
			$attachment_data->post->post_name
		);
		$this->debug( 'Maybe copy attachment: Searching for attachment posts with the name %s: %s', $attachment_data->post->post_name, $query );
		$results = $this->query( $query );
		$this->debug( 'Maybe copy attachment: Found %s attachment posts.', count( $results ) );
		if ( count( $results ) > 0 )
			foreach( $results as $result )
				$attachment_posts[] = get_post( $result[ 'ID' ] );

		// Is there an existing media file?
		// Try to find the filename in the GUID.
		foreach( $attachment_posts as $attachment_post )
		{
			if ( $attachment_post->post_name !== $attachment_data->post->post_name )
			{
				$this->debug( "The attachment post name is %s, and we are looking for %s. Ignoring attachment.", $attachment_post->post_name, $attachment_data->post->post_name );
				continue;
			}
			$this->debug( "Found attachment %s and we are looking for %s.", $attachment_post->post_name, $attachment_data->post->post_name );
			// We've found an existing attachment. What to do with it...
			$existing_action = $this->get_site_option( 'existing_attachments', 'use' );
			$this->debug( 'Maybe copy attachment: The action for existing attachments is to %s.', $existing_action );

			$apply_existing_attachment_action = $this->new_action( 'apply_existing_attachment_action' );
			$apply_existing_attachment_action->action = $existing_action;
			$apply_existing_attachment_action->broadcasting_data = $options;
			$apply_existing_attachment_action->source_attachment = $attachment_data;
			$apply_existing_attachment_action->target_attachment = $attachment_post;
			$apply_existing_attachment_action->execute();

			if ( $apply_existing_attachment_action->use )
			{
				// The ID is the important part.
				$options->attachment_id = $attachment_post->ID;
				$this->debug( 'Maybe copy attachment: Using existing attachment %s.', $attachment_post->ID );
				return $options;
			}
		}

		// Since it doesn't exist, copy it.
		$this->debug( 'Maybe copy attachment: Really copying attachment.' );
		$copy_attachment_action = $this->new_action( 'copy_attachment' );
		$copy_attachment_action->attachment_data = $attachment_data;
		$copy_attachment_action->execute();
		$options->attachment_id = $copy_attachment_action->attachment_id;
	}

	/**
		@brief		Modify the attachment IDs and URLs in this content.
		@since		2016-03-29 09:09:18
	**/
	public function update_attachment_ids( $bcd, $content )
	{
		foreach( $bcd->copied_attachments() as $a )
		{
			// Replace all of the guids in this array.
			$guids = [ $a->old->guid => $a->new->guid ];

			// If the file has different sizes, replace them all.
			if ( @ isset( $a->old->attachment_data->file_metadata[ 'sizes' ] ) )		// Instead of having 30 checks, we just block the warning on this one.
			{
				$old_dirname = dirname( $a->old->guid );
				$new_dirname = dirname( $a->new->guid );
				foreach( $a->old->attachment_data->file_metadata[ 'sizes' ] as $size => $size_data )
				{
					$file = $size_data[ 'file' ];
					$guids [ $old_dirname . '/' . $file ] = $new_dirname . '/' . $file;
				}
			}

			// Modify the captions.
			$content = str_replace( '[caption id="attachment_' . $a->old->ID . '"', '[caption id="attachment_' . $a->new->ID . '"', $content, $count );
			if ( $count > 0 )
				$this->debug( 'Modified caption ID: %s times', $count );

			foreach( $guids as $old_guid => $new_guid )
			{
				$count = 0;

				// We are going to be pregging things in order to ensure that as an exact, local match as possible is replaced with new values
				// This minimizes the risk of similar texts being replaced in the straight content.
				// The old_match = new_match lines are for preg blocks which chain the modifications.

				// Modify anchors
				preg_match_all( '/<a [^>]+>/', $content, $matches );
				foreach( $matches[ 0 ] as $index => $old_match )
				{
					// Replace the GUID with the new one.
					$new_match = str_replace( $old_guid, $new_guid, $old_match, $count );
					if ( $count > 0 )
					{
						$this->debug( 'Modified attachment guid in link: %s times', $count );
						$content = str_replace( $old_match, $new_match, $content );
						$old_match = $new_match;
					}

					// Modify the wp-att-XXX value.
					$new_match = str_replace( 'wp-att-' . $a->old->ID, 'wp-att-' . $a->new->ID, $old_match, $count );
					if ( $count > 0 )
					{
						$this->debug( 'Modified wp-att: %s times', $count );
						$content = str_replace( $old_match, $new_match, $content );
						$old_match = $new_match;
					}
				}

				// Modify the image tags.
				preg_match_all( '/<img [^>]+>/', $content, $matches );
				foreach( $matches[ 0 ] as $index => $old_match )
				{
					// And replace the IDs present in any image captions.
					$new_match = str_replace( 'id="attachment_' . $a->old->ID . '"', 'id="attachment_' . $a->new->ID . '"', $old_match, $count );
					if ( $count > 0 )
					{
						$this->debug( 'Modified attachment ID: %s times', $count );
						$content = str_replace( $old_match, $new_match, $content );
						$old_match = $new_match;
					}

					// Modify the wp-image-XXX value.
					$new_match = str_replace( 'wp-image-' . $a->old->ID, 'wp-image-' . $a->new->ID, $old_match, $count );
					if ( $count > 0 )
					{
						$this->debug( 'Modified wp-image ID class: %s times', $count );
						$content = str_replace( $old_match, $new_match, $content );
						$old_match = $new_match;
					}
				}

				// Replace whatever is left.
				$content = str_replace( $old_guid, $new_guid, $content, $count );
			}
		}
		return $content;
	}
}
