<?php

namespace threewp_broadcast;

use \threewp_broadcast\broadcast_data\blog;

class ThreeWP_Broadcast
	extends \plainview\sdk_broadcast\wordpress\base
{
	use \plainview\sdk_broadcast\wordpress\traits\debug;

	use traits\actions;
	use traits\admin_menu;
	use traits\admin_scripts;
	use traits\attachments;
	use traits\broadcast_data;
	use traits\broadcasting;
	use traits\meta_boxes;
	use traits\misc;
	use traits\post_actions;
	use traits\terms_and_taxonomies;
	use traits\savings_calculator;

	/**
		@brief		Broadcasting stack.
		@details

		An array of broadcasting_data objects, the latest being at the end.

		@since		20131120
	**/
	public $broadcasting = [];

	/**
		@brief	Public property used during the broadcast process.
		@see	include/Broadcasting_Data.php
		@since	20130530
		@var	$broadcasting_data
	**/
	public $broadcasting_data = null;

	/**
		@brief		Display Broadcast completely, including menus and post overview columns.
		@since		20131015
		@var		$display_broadcast
	**/
	public $display_broadcast = true;

	/**
		@brief		Display the Broadcast columns in the post overview.
		@details	Disabling this will prevent the user from unlinking posts.
		@since		20131015
		@var		$display_broadcast_columns
	**/
	public $display_broadcast_columns = true;

	/**
		@brief		Display the Broadcast menu
		@since		20131015
		@var		$display_broadcast_menu
	**/
	public $display_broadcast_menu = true;

	/**
		@brief		Add the meta box in the post editor?
		@details	Standard is null, which means the plugin(s) should work it out first.
		@since		20131015
		@var		$display_broadcast_meta_box
	**/
	public $display_broadcast_meta_box = null;

	/**
		@brief	Display information in the menu about the premium pack?
		@see	threewp_broadcast_premium_pack_info()
		@since	20131004
		@var	$display_premium_pack_info
	**/
	public $display_premium_pack_info = true;

	/**
		@brief		An array of incompatible plugins that prevent Broadcast from working.
		@since		2017-01-16 17:14:35
	**/
	public static $incompatible_plugins = [
		'intuitive-custom-post-order/intuitive-custom-post-order.php',
		'post-type-switcher/post-type-switcher.php',
		'taxonomy-terms-order/taxonomy-terms-order.php',
		/**
			@brief		Breaks UBS by inserting things into the _POST during normal getting.
			@since		2018-01-22 16:02:22
		**/
		'tracking-code-manager/index.php',
	];

	/**
		@brief		The language domain to use.
		@since		2017-02-21 20:00:41
	**/
	public $language_domain = 'threewp_broadcast';

	/**
		@brief		Caches permalinks looked up during this page view.
		@see		post_link()
		@since		20130923
	**/
	public $permalink_cache;

	public $plugin_version = THREEWP_BROADCAST_VERSION;

	public function _construct()
	{
		if ( ! $this->is_network )
			return;

		$this->add_action( 'add_meta_boxes', 100 );

		if ( $this->get_site_option( 'override_child_permalinks' ) )
		{
			$this->add_filter( 'page_link', 'post_link', 10, 3 );
			$this->add_filter( 'post_link', 10, 3 );
			$this->add_filter( 'post_type_link', 'post_link', 10, 3 );
		}

		$this->attachments_init();
		$this->post_actions_init();
		$this->savings_calculator_init();
		$this->terms_and_taxonomies_init();

		$this->add_action( 'plugins_loaded' );

		$this->add_filter( 'threewp_broadcast_add_meta_box' );
		$this->add_filter( 'threewp_broadcast_admin_menu', 'add_post_row_actions_and_hooks', 100 );

		// This is a normal broadcast action, not a special action object. This is a holdover from the good old days from when Broadcast used normal actions.
		// Don't want to break anyone's plugins.
		$this->add_action( 'threewp_broadcast_broadcast_post' );

		$this->add_action( 'threewp_broadcast_each_linked_post' );
		$this->add_action( 'threewp_broadcast_get_user_writable_blogs', 100 );		// Allow other plugins to do this first.
		$this->add_filter( 'threewp_broadcast_get_post_types', 5 );					// Add our custom post types to the array of broadcastable post types.
		$this->add_action( 'threewp_broadcast_maybe_clear_post', 100 );
		$this->add_filter( 'threewp_broadcast_parse_content' );
		$this->add_action( 'threewp_broadcast_prepare_broadcasting_data' );
		$this->add_filter( 'threewp_broadcast_prepare_meta_box', 5 );
		$this->add_filter( 'threewp_broadcast_prepare_meta_box', 'threewp_broadcast_prepared_meta_box', 100 );
		$this->add_filter( 'threewp_broadcast_preparse_content' );


		if ( $this->get_site_option( 'canonical_url' ) )
			$this->add_action( 'wp_head', 1 );

		$this->admin_menu_trait_init();

		$this->permalink_cache = (object)[];
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Activate / Deactivate
	// --------------------------------------------------------------------------------------------

	public function activate()
	{
		if ( !$this->is_network )
			return;

		$db_ver = $this->get_site_option( 'database_version', 0 );

		// 2016-01-05 Always run the create if not exists.
		$this->query("CREATE TABLE IF NOT EXISTS `". $this->broadcast_data_table() . "` (
		  `blog_id` int(11) NOT NULL COMMENT 'Blog ID',
		  `post_id` int(11) NOT NULL COMMENT 'Post ID',
		  `data` longtext NOT NULL COMMENT 'Serialized BroadcastData',
		  KEY `blog_id` (`blog_id`,`post_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		");

		if ( $db_ver < 1 )
		{
			// Remove old options
			$this->delete_site_option( 'requirewhenbroadcasting' );

			// Removed 1.5
			$this->delete_site_option( 'activity_monitor_broadcasts' );
			$this->delete_site_option( 'activity_monitor_group_changes' );
			$this->delete_site_option( 'activity_monitor_unlinks' );

			// Cats and tags replaced by taxonomy support. Version 1.5
			$this->delete_site_option( 'role_categories' );
			$this->delete_site_option( 'role_categories_create' );
			$this->delete_site_option( 'role_tags' );
			$this->delete_site_option( 'role_tags_create' );
			$db_ver = 1;
		}

		if ( $db_ver < 2 )
		{
			// Convert the array site options to strings.
			foreach( [ 'custom_field_exceptions', 'post_types' ] as $key )
			{
				$value = $this->get_site_option( $key, '' );
				if ( is_array( $value ) )
				{
					$value = array_filter( $value );
					$value = implode( ' ', $value );
				}
				$this->update_site_option( $key, $value );
			}
			$db_ver = 2;
		}

		if ( $db_ver < 3 )
		{
			$this->delete_site_option( 'always_use_required_list' );
			$this->delete_site_option( 'blacklist' );
			$this->delete_site_option( 'requiredlist' );
			$this->delete_site_option( 'role_taxonomies_create' );
			$this->delete_site_option( 'role_groups' );
			$db_ver = 3;
		}

		if ( $db_ver < 4 )
		{
			$exceptions = $this->get_site_option( 'custom_field_exceptions', '' );
			$this->delete_site_option( 'custom_field_exceptions' );
			$whitelist = $this->get_site_option( 'custom_field_whitelist', $exceptions );
			$db_ver = 4;
		}

		// 2016-01-05 This used to be v5, but is now always run.
		$this->create_broadcast_data_id_column();

		if ( $db_ver < 6 )
		{
			$this->query("DROP TABLE IF EXISTS `".$this->wpdb->base_prefix."_3wp_broadcast`");
			$db_ver = 6;
		}

		if ( $db_ver < 7 )
		{
			foreach( [
				'role_broadcast',
				'role_link',
				'role_broadcast_as_draft',
				'role_broadcast_scheduled_posts',
				'role_taxonomies',
				'role_custom_fields',
			] as $old_role_option )
			{
				$old_value = $this->get_site_option( $old_role_option );
				if ( is_array( $old_value ) )
					continue;
				$new_value = static::convert_old_role( $old_value );
				$this->update_site_option( $old_role_option, $new_value );
			}
			$db_ver = 7;
		}

		if ( $db_ver < 8 )
		{
			// Make the table a longtext for those posts with many links.
			$this->query("ALTER TABLE `". $this->broadcast_data_table() . "` CHANGE `data` `data` LONGTEXT");
			$db_ver = 8;
		}

		$this->update_site_option( 'database_version', $db_ver );
	}

	public function uninstall()
	{
		$this->delete_site_option( 'broadcast_internal_custom_fields' );
		$query = sprintf( "DROP TABLE `%s`", $this->broadcast_data_table() );
		$this->query( $query );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Modify the plugin links in the plugins table.
		@since		2017-09-22 01:30:45
	**/
	public function plugin_action_links( $links, $plugin_name )
	{
		if ( $plugin_name != 'threewp-broadcast/ThreeWP_Broadcast.php' )
			return $links;
		if ( is_network_admin() )
			$url = network_admin_url( 'admin.php?page=threewp-broadcast' );
		else
			$url = admin_url( 'admin.php?page=threewp-broadcast' );
		$links []= sprintf( '<a href="%s">%s</a>',
			$url,
			__( 'Settings', 'threewp-broadcast' )
		);
		return $links;
	}

	/**
		@brief		Modify the plugin meta in the plugins table.
		@since		2017-09-22 01:50:49
	**/
	public function plugin_row_meta( $meta, $plugin_name )
	{
		if ( $plugin_name != 'threewp-broadcast/ThreeWP_Broadcast.php' )
			return $meta;
		if ( ! isset( $this->__plugin_pack ) )
		{
			if ( is_network_admin() )
				$url = network_admin_url( 'admin.php?page=threewp_broadcast_premium_pack_info' );
			else
				$url = admin_url( 'admin.php?page=threewp_broadcast_premium_pack_info' );
			$meta []= sprintf( '<a href="%s" title="%s">%s</a>',
				$url,
				__( 'View the add-ons available for Broadcast', 'threewp-broadcast' ),
				__( 'Add-ons', 'threewp-broadcast' )
			);
		}
		return $meta;
	}

	/**
		@brief		Broadcast is ready for broadcasting.
		@since		2015-10-29 12:22:53
	**/
	public function plugins_loaded()
	{
		$this->__loaded = true;
		$action = $this->new_action( 'loaded' );
		$action->execute();
	}

	public function post_link( $link, $post )
	{
		// Don't overwrite the permalink if we're in the editing window.
		// This allows the user to change the permalink.
		if ( $_SERVER[ 'SCRIPT_NAME' ] == '/wp-admin/post.php' )
			return $link;

		if ( isset( $this->_is_getting_permalink ) )
			return $link;

		$this->_is_getting_permalink = true;

		$blog_id = get_current_blog_id();

		// Pages return just the ID. Posts return a proper page object.
		if ( ! is_object( $post ) )
			$post = get_post( $post );

		$child_post = $post;

		// Have we already checked this post ID for a link?
		$key = 'b' . $blog_id . '_p' . $post->ID;
		if ( property_exists( $this->permalink_cache, $key ) )
		{
			unset( $this->_is_getting_permalink );
			return $this->permalink_cache->$key;
		}

		$broadcast_data = $this->get_post_broadcast_data( $blog_id, $post->ID );

		$linked_parent = $broadcast_data->get_linked_parent();

		if ( $linked_parent === false)
		{
			$this->permalink_cache->$key = $link;
			unset( $this->_is_getting_permalink );
			return $link;
		}

		switch_to_blog( $linked_parent[ 'blog_id' ] );
		$post = get_post( $linked_parent[ 'post_id' ] );
		$parent_permalink = get_permalink( $post );
		restore_current_blog();

		unset( $this->_is_getting_permalink );

		$action = new actions\override_child_permalink();
		$action->child_permalink = $link;
		$action->child_post = $child_post;
		$action->parent_permalink = $parent_permalink;
		$action->post = $post;
		$action->returned_permalink = $parent_permalink;
		$action->execute();

		$this->permalink_cache->$key = $action->returned_permalink;

		return $action->returned_permalink;
	}

	/**
		@brief		Execute callbacks on all posts linked to this specific post.
		@since		2015-05-02 21:33:55
	**/
	public function threewp_broadcast_each_linked_post( $action )
	{
		$prefix = 'Each Linked Post: ';

		// First, we need the broadcast data of the post.
		if ( $action->blog_id === null )
			$action->blog_id = get_current_blog_id();

		$this->debug( $prefix . 'Loading broadcast data of post %s on blog %s.', $action->post_id, $action->blog_id );

		$broadcast_data = $this->get_post_broadcast_data( $action->blog_id, $action->post_id );

		// Does this post have a parent?
		$parent = $broadcast_data->get_linked_parent();
		if ( $parent !== false )
		{
			if ( $action->on_parent )
			{
				$this->debug( $prefix . 'Executing callbacks on parent post %s on blog %s.', $parent[ 'post_id' ], $parent[ 'blog_id' ] );
				if ( $this->blog_exists( $parent[ 'blog_id' ] ) )
				{
					switch_to_blog( $parent[ 'blog_id' ] );
					$o = (object)[];
					$o->post_id = $parent[ 'post_id' ];
					$o->post = get_post( $o->post_id );
					$this->debug( $prefix . '' );
					foreach( $action->callbacks as $callback )
						$callback( $o );
					restore_current_blog();
				}
			}
			else
				$this->debug( $prefix . 'Not executing on parent.' );
			$broadcast_data = $this->get_post_broadcast_data( $parent[ 'blog_id' ], $parent[ 'post_id' ] );
		}
		else
			$this->debug( $prefix . 'No linked parent.' );

		if ( $action->on_children )
		{
			$this->debug( $prefix . 'Executing on children.' );
			foreach( $broadcast_data->get_linked_children() as $blog_id => $post_id )
			{
				// Do not bother eaching this child if we started here.
				if ( $blog_id == $action->blog_id )
					continue;
				if ( ! $this->blog_exists( $blog_id ) )
					continue;
				switch_to_blog( $blog_id );
				$o = (object)[];
				$o->post_id = $post_id;
				$o->post = get_post( $post_id );
				$this->debug( $prefix . 'Executing callbacks on child post %s on blog %s.', $post_id, $blog_id );
				foreach( $action->callbacks as $callback )
					$callback( $o );
				restore_current_blog();
			}
		}
		else
			$this->debug( $prefix . 'Not executing on children.' );
		$this->debug( $prefix . 'Finished.' );
	}

	/**
		@brief		Return a collection of blogs that the user is allowed to write to.
		@since		20131003
	**/
	public function threewp_broadcast_get_user_writable_blogs( $action )
	{
		if ( $action->is_finished() )
			return;

		$all_networks = $this->get_site_option( 'all_networks' );

		$network_id = get_network()->id;
		$blogs = get_blogs_of_user( $action->user_id );
		foreach( $blogs as $blog)
		{
			if ( ! $all_networks )
				// Filter out those blogs thare are not in our network.
				if ( $blog->site_id != $network_id )
					continue;
			$blog = blog::make( $blog );
			$blog->id = $blog->id;
			if ( ! $this->is_blog_user_writable( $action->user_id, $blog ) )
				continue;
			$action->blogs->set( $blog->id, $blog );
		}

		$action->blogs->sort_logically();
		$action->finish();
	}

	/**
		@brief		Convert the post_type site option to an array in the action.
		@since		2014-02-22 10:33:57
	**/
	public function threewp_broadcast_get_post_types( $action )
	{
		$post_types = $this->get_site_option( 'post_types' );
		$post_types = explode( ' ', $post_types );
		foreach( $post_types as $post_type )
			$action->post_types[ $post_type ] = $post_type;
	}

	/**
		@brief		Decide what to do with the POST.
		@since		2014-03-23 23:08:31
	**/
	public function threewp_broadcast_maybe_clear_post( $action )
	{
		if ( $action->is_finished() )
		{
			$this->debug( 'Not maybe clearing the POST.' );
			return;
		}

		$clear_post = $this->get_site_option( 'clear_post', true );
		if ( $clear_post )
		{

			$this->debug( 'Clearing the POST.' );
			$action->post = [];
		}
		else
			$this->debug( 'Not clearing the POST.' );
	}

	/**
		@brief		Use the correct canonical link.
	**/
	public function wp_head()
	{
		// Only override the canonical if we're looking at a single post.
		$override = false;
		$override |= is_single();
		$override |= is_page();
		if ( ! $override )
			return;

		global $post;
		global $blog_id;

		// Find the parent, if any.
		$broadcast_data = $this->get_post_broadcast_data( $blog_id, $post->ID );
		$linked_parent = $broadcast_data->get_linked_parent();
		if ( $linked_parent === false)
			return;

		// Post has a parent. Get the parent's permalink.
		switch_to_blog( $linked_parent[ 'blog_id' ] );
		$url = get_permalink( $linked_parent[ 'post_id' ] );
		restore_current_blog();

		echo sprintf( '<link rel="canonical" href="%s" />', $url );
		echo "\n";

		// Prevent Wordpress from outputting its own canonical.
		remove_action( 'wp_head', 'rel_canonical' );

		// Remove Canonical Link Added By Yoast WordPress SEO Plugin
		if ( class_exists( '\\WPSEO_Frontend' ) )
		{
			$this->add_filter( 'wpseo_canonical', 'wp_head_remove_wordpress_seo_canonical' );;
			$wpseo_frontend = \WPSEO_Frontend::get_instance();
			remove_action( 'wpseo_head', array( $wpseo_frontend, 'canonical' ), 20 );
		}
	}

	/**
		@brief		Remove Wordpress SEO canonical link so that it doesn't conflict with the parent link.
		@since		2014-01-16 00:36:15
	**/

	public function wp_head_remove_wordpress_seo_canonical()
	{
		// Tip seen here: http://wordpress.org/support/topic/plugin-wordpress-seo-by-yoast-remove-canonical-tags-in-header?replies=10
		return false;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Modify the debug class name, if necessary.
		@details	Due to trait problems, it is easier to just leave this function here rather than put it in the misc trait.
		@since		2017-10-28 18:11:53
	**/
	public function get_debug_class_name( $class_name )
	{
		$count = count( $this->broadcasting );
		if ( $count < 2 )
			return $class_name;
		return $class_name . ' (' . $count . ')';
	}
}
