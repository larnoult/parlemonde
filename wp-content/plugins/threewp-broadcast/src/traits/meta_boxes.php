<?php

namespace threewp_broadcast\traits;

use \threewp_broadcast\meta_box;

/**
	@brief		Methods related to the broadcast meta box.
	@since		2014-10-19 15:44:39
**/
trait meta_boxes
{
	public function add_meta_boxes()
	{
		// Display broadcast at all?
		if ( ! $this->display_broadcast )
			return false;
		// Display the meta box?
		if ( $this->display_broadcast_meta_box === false )
			return;

		// If it's true, then show it to all post types!
		if ( $this->display_broadcast_meta_box === true )
		{
			$action = $this->new_action( 'get_post_types' );
			$action->execute();
			foreach( $action->post_types as $post_type )
				add_meta_box(
					'threewp_broadcast',
					// Meta box title
					__( 'Broadcast', 'threewp-broadcast' ),
					[ $this, 'threewp_broadcast_add_meta_box' ],
					$post_type,
					'side',
					'low'
				);
			return;
		}

		// No decision yet. Decide.
		$this->display_broadcast_meta_box |= is_super_admin();
		$this->display_broadcast_meta_box |= static::user_has_roles( $this->get_site_option( 'role_broadcast' ) );

		// No access to any other blogs = no point in displaying it.
		$filter = $this->new_action( 'get_user_writable_blogs' );
		$filter->user_id = $this->user_id();
		$blogs = $filter->execute()->blogs;
		if ( count( $blogs ) <= 1 )
		{
			// If the user is debugging, show the box anyway.
			if ( ! $this->debugging() )
				$this->display_broadcast_meta_box = false;
		}

		// Convert to a bool value
		$this->display_broadcast_meta_box = ( $this->display_broadcast_meta_box == true );

		if ( $this->display_broadcast_meta_box == true )
			return $this->add_meta_boxes();
	}

	/**
		@brief		Create a meta box for this post.
		@since		20131015
	**/
	public function create_meta_box( $post )
	{
		$meta_box_data = new meta_box\data;
		$meta_box_data->blog_id = get_current_blog_id();
		$meta_box_data->broadcast_data = $this->get_post_broadcast_data( $meta_box_data->blog_id, $post->ID );
		$meta_box_data->form = $this->form2();
		$meta_box_data->post = $post;
		$meta_box_data->post_id = $post->ID;
		return $meta_box_data;
	}

	/**
		@brief		Prepare and display the meta box data.
		@since		20131003
	**/
	public function threewp_broadcast_add_meta_box( $post )
	{
		$meta_box_data = $this->create_meta_box( $post );

		// Allow plugins to modify the meta box with their own info.
		$action = $this->new_action( 'prepare_meta_box' );
		$action->meta_box_data = $meta_box_data;
		$action->execute();

		foreach( $meta_box_data->css as $key => $value )
			wp_enqueue_style( $key, $value, '', $this->plugin_version );
		foreach( $meta_box_data->js as $key => $value )
			wp_enqueue_script( $key, $value, '', $this->plugin_version );

		echo $meta_box_data->html->render();
	}

	/**
		@brief		Prepare and display the meta box data.
		@since		20131010
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;	// Convenience.

		// Check for incompatible plugins. This is thanks to Post Type Switcher which has now caused me enough headache.
		$plugins = array_keys( get_site_option( 'active_sitewide_plugins' ) );
		$plugins = array_merge( $plugins, get_option( 'active_plugins' ) );
		$incompatible_plugins = array_intersect( $plugins, static::$incompatible_plugins );
		if ( count( $incompatible_plugins ) > 0 )
		{
			$meta_box_data->html->put( 'incompatible_plugins1',
				$this->p( __( 'Please disable the following incompatible plugins before using Broadcasting:' ), 'threewp-broadcast' )
			);
			$incompatible_plugins = $this->get_plugin_info_array( $incompatible_plugins );
			// Extract only the middle part.
			foreach( $incompatible_plugins as $index => $string )
			{
				$parts = explode( ',', $string );
				$string = trim( $parts[ 1 ] );
				$incompatible_plugins[ $index ] = $string;
			}
			$meta_box_data->html->put( 'incompatible_plugins2', implode( "<br>\n", $incompatible_plugins ) );
		}

		// Add translation strings
		$meta_box_data->html->put( 'broadcast_strings', '
			<script type="text/javascript">
				var broadcast_strings = {
					hide_all : "' . $this->_( 'hide all' ) . '",
					invert_selection : "' . $this->_( 'Invert selection' ) . '",
					select_deselect_all : "' . $this->_( 'Select / deselect all' ) . '",
					show_all : "' . $this->_( 'show all' ) . '"
				};
			</script>
		' );

		if ( $this->debugging() )
			$meta_box_data->html->put( 'debug',
				$this->p( __( 'Broadcast is in debug mode. More information than usual will be shown.' , 'threewp-broadcast' ) )
			);

		if ( $action->is_finished() )
		{
			if ( $this->debugging() )
				$meta_box_data->html->put( 'debug_applied',
					$this->p( __( 'Broadcast is not preparing the meta box because it has already been applied.', 'threewp-broadcast' ) )
				);
			return;
		}

		$linked_parent = $meta_box_data->broadcast_data->get_linked_parent();
		if ( $linked_parent !== false)
		{
			switch_to_blog( $linked_parent[ 'blog_id' ] );
			// http://broadcast2.it.ed/wp-admin/post.php?post=1435&action=edit
			$edit_url = get_edit_post_link( $linked_parent[ 'post_id' ] );
			restore_current_blog();
			$meta_box_data->html->put( 'already_broadcasted',  sprintf( '<p>%s</p>',
				sprintf(
					// broadcasted is linked.
					__( 'This post is a %sbroadcasted%s child post. It cannot be broadcasted further.', 'threewp-broadcast' ),
					'<a href="' . $edit_url . '">',
					'</a>'
					)
			) );
			$action->finish();
			return;
		}

		$form = $meta_box_data->form;		// Convenience
		$form->prefix( 'broadcast' );		// Create all inputs with this prefix.

		$published = $meta_box_data->post->post_status == 'publish';

		$has_linked_children = $meta_box_data->broadcast_data->has_linked_children();

		$meta_box_data->last_used_settings = $this->load_last_used_settings( $this->user_id() );

		$post_type = $meta_box_data->post->post_type;
		$post_type_supports_thumbnails = post_type_supports( $post_type, 'thumbnail' );

		$post_type_object = get_post_type_object( $post_type );
		// Yepp. Some post types don't return proper info.
		$post_type_is_hierarchical = @ ( $post_type_object->hierarchical === true );

		if ( is_super_admin() OR static::user_has_roles( $this->get_site_option( 'role_link' ) ) )
		{
			// Link checkbox should always be on.
			$link_input = $form->checkbox( 'link' )
				->checked( true )
				// Input label for meta box
				->label( __( 'Link this post to its children', 'threewp-broadcast' ) )
				// Input title for meta box
				->title( __( 'Create a link to the children, which will be updated when this post is updated, trashed when this post is trashed, etc.', 'threewp-broadcast' ) );
			$meta_box_data->convert_form_input_later( 'link' );
		}

		// 20140327 Because so many plugins create broken post types, assume that all post types support custom fields.
		// $post_type_supports_custom_fields = post_type_supports( $post_type, 'custom-fields' );
		$post_type_supports_custom_fields = true;

		if (
			( $post_type_supports_custom_fields OR $post_type_supports_thumbnails )
			AND
			( is_super_admin() OR static::user_has_roles( $this->get_site_option( 'role_custom_fields' ) ) )
		)
		{
			$custom_fields_input = $form->checkbox( 'custom_fields' )
				->checked( isset( $meta_box_data->last_used_settings[ 'custom_fields' ] ) )
				// Input label for meta box
				->label( __( 'Custom fields', 'threewp-broadcast' ) )
				// Input title for meta box
				->title( 'Broadcast all the custom fields and the featured image?' );
			$meta_box_data->convert_form_input_later( 'custom_fields' );
		}

		if ( is_super_admin() OR static::user_has_roles( $this->get_site_option( 'role_taxonomies' ) ) )
		{
			$taxonomies_input = $form->checkbox( 'taxonomies' )
				->checked( isset( $meta_box_data->last_used_settings[ 'taxonomies' ] ) )
				// Input label for meta box
				->label( __( 'Taxonomies', 'threewp-broadcast' ) )
				// Input title for meta box
				->title( 'The taxonomies must have the same name (slug) on the selected blogs.' );
			$meta_box_data->convert_form_input_later( 'taxonomies' );
		}

		$filter = $this->new_action( 'get_user_writable_blogs' );
		$filter->user_id = $this->user_id();
		$blogs = $filter->execute()->blogs;

		$blogs_input = $form->checkboxes( 'blogs' )
			->css_class( 'blogs checkboxes' )
			// Input label for meta box
			->label( __( 'Broadcast to', 'threewp-broadcast' ) )
			->prefix( 'blogs' );

		// Preselect those children that this post has.
		$linked_children = $meta_box_data->broadcast_data->get_linked_children();
		foreach( $linked_children as $blog_id => $ignore )
		{
			$blog = $blogs->get( $blog_id );
			if ( ! $blog )
				continue;
			$blog->linked()->selected();
		}

		foreach( $blogs as $blog )
		{
			$label = $form::unfilter_text( $blog->get_name() );
			if ( $label == '' )
				$label = $blog->domain;

			$blogs_input->option( $label, $blog->id );
			$input_name = 'blogs_' . $blog->id;
			$option = $blogs_input->input( $input_name );
			$option->get_label()->content = htmlspecialchars( $label );
			$option->css_class( 'blog ' . $blog->id );
			$option->title( 'ID: %s', $blog->id );
			if ( $blog->is_disabled() )
				$option->disabled()->css_class( 'disabled' );
			if ( $blog->is_linked() )
				$option->css_class( 'linked' );
			if ( $blog->is_required() )
				// Input title for required blogs.
				$option->css_class( 'required' )->title( __( 'This blog is required', 'threewp-broadcast' ) );
			if ( $blog->is_selected() )
				$option->checked( true );
			// The current blog should be "selectable", for the sake of other plugins that modify the meta box. But hidden from users.
			if ( $blog->id == $meta_box_data->blog_id )
				$option->hidden();
		}

		$meta_box_data->convert_form_input_later( 'blogs' );

		$unchecked_child_blogs = $form->select( 'unchecked_child_blogs' )
			->css_class( 'blogs checkboxes' )
			// Input title
			->title( __( 'What to do with unchecked, linked child blogs', 'threewp-broadcast' ) )
			// Input label
			->label( __( 'With the unchecked child blogs', 'threewp-broadcast' ) )
			// With the unchecked child blogs:
			->option( __( 'Do not update', 'threewp-broadcast' ), '' )
			// With the unchecked child blogs:
			->option( __( 'Delete the child post', 'threewp-broadcast' ), 'delete' )
			// With the unchecked child blogs:
			->option( __( 'Trash the child post', 'threewp-broadcast' ), 'trash' )
			// With the unchecked child blogs:
			->option( __( 'Unlink the child post', 'threewp-broadcast' ), 'unlink' );
		$meta_box_data->convert_form_input_later( 'unchecked_child_blogs' );

		$js = sprintf( '<script type="text/javascript">var broadcast_blogs_to_hide = %s;</script>', $this->get_site_option( 'blogs_to_hide', 5 ) );
		$meta_box_data->html->put( 'blogs_js', $js );

		// We require some js.
		$meta_box_data->js->put( 'threewp_broadcast', $this->paths[ 'url' ] . '/js/js.js' );
		// And some CSS
		$meta_box_data->css->put( 'threewp_broadcast', $this->paths[ 'url' ] . '/css/css.css'  );

		if ( $this->debugging() )
		{
			$meta_box_data->html->put( 'debug_info_1', sprintf( '
				<h3>Debug info</h3>
				<ul>
				<li>High enough role to link: %s</li>
				<li>Post supports custom fields: %s</li>
				<li>Post supports thumbnails: %s</li>
				<li>High enough role to broadcast custom fields: %s</li>
				<li>High enough role to broadcast taxonomies: %s</li>
				<li>Blogs available to user: %s</li>
				</ul>',
					( static::user_has_roles( $this->get_site_option( 'role_link' ) ) ? 'yes' : 'no' ),
					( $post_type_supports_custom_fields ? 'yes' : 'no' ),
					( $post_type_supports_thumbnails ? 'yes' : 'no' ),
					( static::user_has_roles( $this->get_site_option( 'role_custom_fields' ) ) ? 'yes' : 'no' ),
					( static::user_has_roles( $this->get_site_option( 'role_taxonomies' ) ) ? 'yes' : 'no' ),
					count( $blogs )
				)
			);

			// Display a list of actions that have hooked into save_post
			$save_post_callbacks = $this->get_hooks( 'save_post' );
			$meta_box_data->html->put( 'debug_save_post_callbacks', sprintf( '%s%s',
				$this->p( __( 'Plugins that have hooked into save_post:', 'threewp-broadcast' ) ),
				$this->implode_html( $save_post_callbacks )
			) );
		}

		$action->finish();
	}

	/**
		@brief		Fix up the inputs.
		@since		20131010
	**/
	public function threewp_broadcast_prepared_meta_box( $action )
	{
		$action->meta_box_data->convert_form_inputs_now();
	}

}
