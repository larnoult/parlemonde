<?php

namespace threewp_broadcast;

use \Exception;

/**
	@brief	Data of an attached file.

	@par	Changelog

	- 20130530		from_attachment_id uses the whole WP_Post, not just the ID.
	- @b 2013-02-21 Added post excerpt, guid, id
	- @b 2013-02-14 Added extra attachment data from werk@haha.nl: post_title and menu_order
 */

class attachment_data
{
	use \plainview\sdk_broadcast\traits\method_chaining;

	public $filename_base;					// img.jpg
	public $filename_path;					// /var/www/wordpress/image.jpg
	public $file_metadata;					// Wordpress' metadata for the attached image.
	public $id;								// ID of attachment.
	public $guid;							// Old guid of image.
	public $post_custom;					// Array of post meta keys and values.
	public $_wp_attachment_image_alt;		// The alt value for this image, if any.

	public function __construct( $options = array() )
	{
		foreach($options as $key => $value)
			$this->$key = $value;
	}

	/**
		@brief		Create an attachment_data object from a post or ID.
		@since		2015-11-16 15:39:18
	**/
	public static function from_attachment_id( $attachment )
	{
		$r = new attachment_data;

		if ( is_object( $attachment ) )
			$r->id = $attachment->ID;
		else
			$r->id = $attachment;

		$r->post = get_post( $r->id );

		if ( ! $r->post )
			throw new Exception( sprintf( 'The attachment ID %s does not have an associated post.', $r->id ) );

		$metadata = wp_get_attachment_metadata( $r->id );
		// Does the file have metadata?
		if ( $metadata )
			$r->file_metadata = $metadata;

		$r->filename_path = get_attached_file( $r->id );
		$r->filename_base = basename( $r->filename_path );

		if ( $r->filename_path == '' )
			throw new Exception( sprintf( 'The attachment ID %s does not have a filename.', $r->id ) );

		// Copy all of the custom data for this post.
		$r->post_custom = get_post_custom( $r->id );

		return $r;
	}

	/**
		@brief		Is this attachment attached to a parent post?
		@since		2014-08-01 13:11:04
	**/
	public function is_attached_to_parent()
	{
		if ( ! isset( $this->attached_to_parent ) )
			return false;
		return $this->attached_to_parent;
	}

	/**
		@brief		Is this attachment a URL?
		@since		2015-06-04 18:38:31
	**/
	public function is_url()
	{
		return strpos( $this->filename_path, '://' ) !== false;
	}

	/**
		@brief		Set the "attached to parent" status.
		@since		2014-08-01 13:09:09
	**/
	public function set_attached_to_parent( $post, $attached = null )
	{
		if ( $attached === null )
			$attached = ( $this->post->post_parent == $post->ID );

		$this->attached_to_parent = $attached;
	}
}
