<?php

namespace threewp_broadcast;

/**
	@brief		API methods, to make life easier for developers.
	@details

This is read much easier if you run doxygen...

<h2>Using the API</h2>

Retrieve the API object by asking Broadcast for it.

<code>$api = ThreeWP_Broadcast()->api();</code>

Or call an API method directly.

<code>ThreeWP_Broadcast()->api()->broadcast_children( 123, [ 10, 11, 12 ] );</code>

<h2>Broadcasting</h2>

The methods below refer to the post ID on the current blog.

To broadcast a post to one or more blogs, see broadcast_children(). The posts will be linked as children.

To rebroadcast (update) the post, see update_children(). You can also, optionally, specified an array of new blog IDs which to add the post. Linking is parent <-> child.

Broadcasting is done like so:

<ul>
	<li>A broadcasting_data (bcd) object is created with the specified post_id as the source.</li>
	<li>The bcd will examine the post and prepare itself, setting its internal properties.</li>
	<li>The bcd constructor will create and prepare the meta_box_data object, which allows plugins to do things like force broadcast to blogs or similar.</li>
	<li>Optionally, the prepare_broadcasting_data action is executed in order to parse the meta box, to see which options and blogs are selected. This is only done when input from the user needs to be parsed. This is not run when using the API.</li>
	<li>The threewp_broadcast_broadcast_post action is executed, which will broadcast the post. The queue plugin overrides this hook and stores the bcd for later.</li>
	<li>The bcd object is returned.</li>
</ul>

If you wish to not use the default broadcasting_data values (perhaps you don't want taxonomies broadcasted), then you should create the bcd object yourself, modify it, and then run action.

<h2>Linking</h2>

Scheduled for future versions of the API, which will allow not only easy linking but also the ability to link as siblings, parents and siblings, etc. All through the ingenious use of hooks.

<h2>Future plans</h2>

* Unlinking, deleting children.

	@see		\\threewp_broadcast\\ThreeWP_Broadcast::api()
	@since		2015-06-15 22:12:21
**/
class api
{
	/**
		@brief		API version, as the date.
		@since		2015-06-25 16:41:57
	**/
	public static $version = 20150625;

	/**
		@brief		Broadcast a post to one or more blogs. Link the posts as children.
		@param		int		$post_id		ID of post on this blog to broadcast.
		@param		array	$blogs			Array of blog IDs to which to broadcast.
		@see		\\threewp_broadcast\\broadcasting_data for default values, which is to link, broadcast custom fields and taxonomies, etc.
		@since		2015-06-15 22:50:24
	**/
	public function broadcast_children( $post_id, $blogs )
	{
		$bcd = \threewp_broadcast\broadcasting_data::make( $post_id, $blogs );
		$bcd->high_priority = true;
		apply_filters( 'threewp_broadcast_broadcast_post', $bcd );
		return $bcd;
	}

	/**
		@brief		Delete the specified children.
		@param		int		$post_id		ID of post on this blog to use as the parent.
		@param		array	$blogs			Array of blog IDs from which to delete the child posts.
		@since		2018-03-02 18:40:09
	**/
	public function delete_children( $post_id, $blogs )
	{
		if ( ! is_array( $blogs ) )
			$blogs = [ $blogs ];
		$broadcast_data = ThreeWP_Broadcast()->get_post_broadcast_data( get_current_blog_id(), $post_id );
		foreach( $broadcast_data->get_linked_children() as $child_blog_id => $child_post_id )
		{
			if ( ! in_array( $child_blog_id, $blogs ) )
				continue;
			switch_to_blog( $child_blog_id );
			// Don't even trash them. Go away!
			wp_delete_post( $child_post_id, true );
			restore_current_blog();
		}
	}

	/**
		@brief		Rebroadcasts a parent post to its existing children.
		@details	Optionally adds children on new blogs.
		@param		int		$post_id	The ID of the post to rebroadcast.
		@param		array	$new_blogs	Optional array of blog IDs to which to add new children.
		@since		2015-06-24 18:44:46
	**/
	public function update_children( $post_id, $new_blogs = [] )
	{
		$bcd = \threewp_broadcast\broadcasting_data::make( $post_id );
		$bcd->high_priority = true;
		foreach( $this->_get_post_children( $post_id ) as $blog_id )
			$bcd->broadcast_to( $blog_id );

		foreach( $new_blogs as $blog_id )
			$bcd->broadcast_to( $blog_id );

		apply_filters( 'threewp_broadcast_broadcast_post', $bcd );
		return $bcd;
	}

	/**
		@brief		Convenience method to return an array of blog IDs on which the post has children.
		@since		2015-06-25 16:32:56
	**/
	public function _get_post_children( $post_id )
	{
		$r = [];

		// Retrieve the broadcast_data of this post.
		$broadcast_data = ThreeWP_Broadcast()->get_post_broadcast_data( get_current_blog_id(), $post_id );

		if ( ! $broadcast_data->has_linked_children() )
			return $r;

		foreach( $broadcast_data->get_linked_children() as $child_blog_id => $child_post_id )
			$r[] = $child_blog_id;

		return $r;
	}
}
