<?php

namespace threewp_broadcast\traits;

/**
	@brief		Misc functions that look better being placed here.
	@since		2018-01-24 15:25:10
**/
trait misc
{
	/**
		@brief		Return the API class.
		@since		2015-06-16 22:21:22
	**/
	public function api()
	{
		return new \threewp_broadcast\api();
	}

	/**
		@brief		Checks whether a blog exists.
		@details	Yes, Wordpress' switch_to_blog() doesn't do that check and ALWAYS RETURNS TRUE (!!!!).
		@since		2017-01-18 20:10:26
	**/
	public function blog_exists( $blog_id )
	{
		return get_blog_status( $blog_id, 'blog_id' ) == $blog_id;
	}

	/**
		@brief		Convenience function to return a Plainview SDK Collection.
		@since		2014-10-31 13:21:06
	**/
	public static function collection( $items = [] )
	{
		return new \plainview\sdk_broadcast\collections\Collection( $items );
	}

	/**
		@brief		Convert old role to array of roles.
		@details	Used to convert 'editor' to [ 'editor', 'author', 'contribuor', 'subscriber' ], for example.
		@since		2015-03-17 18:09:27
	**/
	public static function convert_old_role( $role )
	{
		$old_roles = [ 'super_admin', 'administrator', 'editor', 'author', 'contributor', 'subscriber' ];
		foreach( $old_roles as $index => $old_role )
		{
			if ( $old_role != $role )
				continue;
			// The new roles are the rest of the array.
			return array_slice( $old_roles, $index );
		}
		// Didn't find anything? Return the same role, but in an array.
		return [ $role ];
	}

	/**
		@brief		Creates the ID column in the broadcast data table.
		@since		2014-04-20 20:19:45
	**/
	public function create_broadcast_data_id_column()
	{
		$query = sprintf( "ALTER TABLE `%s` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'ID of row' FIRST;",
			$this->broadcast_data_table()
		);
		$this->query( $query );
	}

	/**
		@brief		Enqueue the JS file.
		@since		20131007
	**/
	public function enqueue_js()
	{
		if ( isset( $this->_js_enqueued ) )
			return;
		wp_enqueue_script( 'threewp_broadcast', $this->paths[ 'url' ] . '/js/js.js', '', $this->plugin_version );
		$this->_js_enqueued = true;
	}

	/**
		@brief		Find shortcodes in a string.
		@details	Runs a preg_match_all on a string looking for specific shortcodes.
					Overrides Wordpress' get_shortcode_regex without own shortcode(s).
		@since		2014-02-26 22:05:09
	**/
	public function find_shortcodes( $string, $shortcodes )
	{
		// Make the shortcodes an array
		if ( ! is_array( $shortcodes ) )
			$shortcodes = [ $shortcodes ];

		// We use Wordpress' own function to find shortcodes.

		global $shortcode_tags;
		// Save the old global
		$old_shortcode_tags = $shortcode_tags;
		// Replace the shortcode tags with just our own.
		$shortcode_tags = array_flip( $shortcodes );
		$rx = get_shortcode_regex();
		$shortcode_tags = $old_shortcode_tags;

		// Run the preg_match_all
		$matches = '';
		preg_match_all( '/' . $rx . '/', $string, $matches );

		return $matches;
	}

	/**
		@brief		Return a collection of add-on pack info.
		@since		2016-12-05 14:50:20
	**/
	public function get_addon_packs_info()
	{
		$r = $this->collection();

		$pack = $r->collection( '3rdparty' );
		$pack->set( 'name', '3rd party' );
		$pack->set( 'version_define', 'BROADCAST_3RD_PARTY_PACK_VERSION' );

		$pack = $r->collection( 'control' );
		$pack->set( 'name', 'Control' );
		$pack->set( 'version_define', 'BROADCAST_CONTROL_PACK_VERSION' );

		$pack = $r->collection( 'efficiency' );
		$pack->set( 'name', 'Efficiency' );
		$pack->set( 'version_define', 'BROADCAST_EFFICIENCY_PACK_VERSION' );

		$pack = $r->collection( 'premium' );
		$pack->set( 'name', 'Premium' );
		$pack->set( 'version_define', 'BROADCAST_PREMIUM_PACK_VERSION' );

		$pack = $r->collection( 'utilities' );
		$pack->set( 'name', 'Utilities' );
		$pack->set( 'version_define', 'BROADCAST_UTILITIES_PACK_VERSION' );

		return $r;
	}

	/**
		@brief		Return an array of post types available on this blog.
		@details	Excludes the nav menu item post type.
		@since		2014-11-16 23:10:09
	**/
	public function get_blog_post_types()
	{
		$r = get_post_types();
		unset( $r[ 'nav_menu_item' ] );
		$r = array_keys( $r );
		return $r;
	}

	/**
		@brief		Return an array of all callbacks of a hook.
		@since		2014-04-30 00:11:30
	**/
	public function get_hooks( $hook )
	{
		global $wp_filter;
		$filters = $wp_filter[ $hook ];
		if ( is_object( $filters ) )
			$filters = $filters->callbacks;
		ksort( $filters );
		$hook_callbacks = [];
		//$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
		foreach( $filters as $priority => $callbacks )
		{
			foreach( $callbacks as $callback )
			{
				$function = $callback[ 'function' ];
				if ( is_string( $function ) )
					$function_name = $function;
				if ( is_array( $function ) )
				{
					$function_name = $function[ 0 ];
					if ( is_object( $function_name ) )
						$function_name = sprintf( '%s::%s', get_class( $function_name ), $function[ 1 ] );
					else
						$function_name = sprintf( '%s::%s', $function_name, $function[ 1 ] );
				}
				if ( is_a( $function, 'Closure' ) )
					$function_name = '[Anonymous function]';
				$hook_callbacks[] = $function_name;
			}
		}
		return $hook_callbacks;
	}

	/**
		@brief		Get some standardizing CSS styles.
		@return		string		A string containing the CSS <style> data, including the tags.
		@since		20131031
	**/
	public function html_css()
	{
		return file_get_contents( __DIR__ . '/../../html/style.css' );
	}

	/**
		@brief		Return the plugin pack instance.
		@since		2015-10-28 14:42:18
	**/
	public function plugin_pack()
	{
		if ( ! isset( $this->__plugin_pack ) )
		{
			$this->__plugin_pack = new \threewp_broadcast\premium_pack\ThreeWP_Broadcast_Plugin_Pack();
			if ( $this->__loaded )
				$this->__plugin_pack->plugins_ready = true;
		}
		return $this->__plugin_pack;
	}

	/**
		@brief		Return a table containing the info of each plugin.
		@since		2016-07-19 13:46:46
	**/
	public function get_plugin_info_array( $plugins )
	{
		$r = [];
		if ( function_exists( 'get_plugin_data' ) )
			foreach( $plugins as $plugin_filename )
			{
				$s = [];

				$plugin_filepath = WP_PLUGIN_DIR . '/' . $plugin_filename;
				if ( !file_exists($plugin_filepath) )
					continue;
				$plugin_data = get_plugin_data( $plugin_filepath );

				$plugin_data = (object)$plugin_data;
				$s []= $plugin_filename;
				$s []= $plugin_data->Name;
				$s []= $plugin_data->Version;
				$s = implode( ', ', $s );
				$r []= $s;
			}
		return $r;
	}

	/**
		@brief		Return a table object containing the system info.
		@since		2016-05-04 21:06:33
	**/
	public function get_system_info_table()
	{
		$table = $this->table();
		// Caption for the blog / PHP information table
		$table->caption()->text_( 'Information' );

		$row = $table->head()->row();
		$row->th()->text_( 'Key' );
		$row->th()->text_( 'Value' );

		if ( $this->debugging() )
		{
			$row = $table->body()->row();
			$row->td()->text_( 'Debugging' );
			$row->td()->text_( 'Enabled' );
		}

		$row = $table->body()->row();
		$row->td()->text_( 'Broadcast version' );
		$row->td()->text( $this->plugin_version );

		global $wp_version;
		$row = $table->body()->row();
		$row->td()->text_( 'Wordpress version' );
		$row->td()->text( $wp_version );

		$row = $table->body()->row();
		$row->td()->text_( 'PHP version' );
		$row->td()->text( phpversion() );

		$row = $table->body()->row();
		$row->td()->text_( 'Wordpress upload directory array' );
		$row->td()->text( '<pre>' . var_export( wp_upload_dir(), true ) . '</pre>' );

		$this->paths[ 'ABSPATH' ] = ABSPATH;
		$this->paths[ 'WP_PLUGIN_DIR' ] = WP_PLUGIN_DIR;
		$row = $table->body()->row();
		$row->td()->text_( 'Plugin paths' );
		$row->td()->text( '<pre>' . var_export( $this->paths(), true ) . '</pre>' );

		$row = $table->body()->row();
		$row->td()->text_( 'PHP maximum execution time' );
		$count = ini_get ( 'max_execution_time' );
		$text = $this->p( _n( '%d second', '%d seconds', $count, 'threewp-broadcast' ), $count );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'PHP memory limit' );
		$text = ini_get( 'memory_limit' );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'Wordpress memory limit' );
		$text = wpautop( sprintf( WP_MEMORY_LIMIT . "

%s

<code>define('WP_MEMORY_LIMIT', '512M');</code>
",		$this->_( 'This can be increased by adding the following to your wp-config.php:' ) ) );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'Debug code' );
		$text = WP_MEMORY_LIMIT;
		$text = wpautop( sprintf( "%s

<code>ini_set('display_errors','On');</code>
<code>define('WP_DEBUG', true);</code>
",		$this->p( __( 'Add the following lines to your wp-config.php to help find out why errors or blank screens are occurring:' ) ), 'threewp-broadcast' ) );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'Hooked into save_post' );
		$hooks = $this->get_hooks( 'save_post' );
		$row->td()->text( implode( "<br>\n", $hooks ) );

		$row = $table->body()->row();
		$row->td()->text_( 'Plugins active on blog' );
		$plugins = $this->get_plugin_info_array( get_option( 'active_plugins' ) );
		$row->td()->text( implode( "<br>\n", $plugins ) );

		$row = $table->body()->row();
		$row->td()->text_( 'Plugins active on network' );
		$plugins = get_site_option( 'active_sitewide_plugins' );
		$plugins = $this->get_plugin_info_array( array_keys( $plugins ) );
		$row->td()->text( implode( "<br>\n", $plugins ) );

		foreach( $this->site_options() as $key => $value )
		{
			$row = $table->body()->row();
			$row->td()->text_( 'Broadcast option %s', $key );
			$value = $this->get_site_option( $key );
			$row->td()->text( json_encode( $value ) );
		}

		return $table;
	}

	/**
		@brief		Insert hook into save post action.
		@since		2015-02-10 20:38:22
	**/
	public function hook_save_post()
	{
		$priority = intval( $this->get_site_option( 'save_post_priority' ) );
		$decoys = intval( $this->get_site_option( 'save_post_decoys' ) );
		// See nop() for why this even exists.
		for ( $counter = 0; $counter < $decoys; $counter++ )
			$this->add_action( 'save_post', 'nop', $priority - 1 - $counter );
		$this->add_action( 'save_post', $priority );
	}

	public function is_blog_user_writable( $user_id, $blog )
	{
		// Check that the user has write access.
		switch_to_blog( $blog->id );

		global $current_user;
		wp_get_current_user();
		$r = current_user_can( 'edit_posts' );

		restore_current_blog();

		return $r;
	}

	/**
		@brief		Converts a textarea of lines to a single line of space separated words.
		@param		string		$lines		Multiline string.
		@return		string					All of the lines on one line, minus the empty lines.
		@since		20131004
	**/
	public function lines_to_string( $lines )
	{
		$lines = explode( "\n", $lines );
		$r = [];
		foreach( $lines as $line )
			if ( trim( $line ) != '' )
				$r[] = trim( $line );
		return implode( ' ', $r );
	}

	/**
		@brief		Load the user's last used settings from the user meta table.
		@since		2014-10-09 06:27:32
	**/
	public function load_last_used_settings( $user_id )
	{
		$settings = get_user_meta( $user_id, 'broadcast_last_used_settings', true );
		if ( ! is_array( $settings ) )
			// Suggest some settings.
			$settings = [
				'custom_fields' => 'on',
				'link' => 'on',
				'taxonomies' => 'on',
			];
		return $settings;
	}

	/**
		@brief		Maybe match this subject to a pattern.
		@details	Accepts a plaintext $pattern, or a regexp if the pattern starts and ends with a forward slash.
		@since		2017-09-15 18:27:08
	**/
	public static function maybe_preg_match( $pattern, $subject )
	{
		$is_regexp = false;
		// A straight up regexp starts and ends with a forward slash.
		if ( ( $pattern[ 0 ] == '/' ) AND ( $pattern[ strlen( $pattern ) - 1 ] == '/' ) )
			$is_regexp = true;
		else
		{
			// An asterisk is accepted as a regexp.
			if ( strpos( $pattern, '*' ) !== false )
			{
				// But it needs to be modified to be a real regexp.
				$pattern = '/' . str_replace( '*', '.*', $pattern ) . '/';
				$is_regexp = true;
			}
		}

		if ( ! $is_regexp )
			return $pattern == $subject;

		preg_match( $pattern, $subject, $matches );
		return ( count( $matches ) > 0 );
	}

	/**
		@brief		Do nothing.
		@details	Used as a workaround for plugins that might remove_action in the save_post before us.
					This is a bug in how Wordpress handles filters and actions: https://core.trac.wordpress.org/ticket/17817
		@since		2015-08-26 21:09:28
	**/
	public function nop()
	{
	}

	/**
		@brief		Forces changes to the post dates.
		@details	Accepts all four post date columns.
		@since		2017-02-07 14:57:41
	**/
	public function set_post_date( $post_data )
	{
		$sets = [];
		foreach( [ 'post_modified', 'post_modified_gmt', 'post_date', 'post_date_gmt' ] as $key )
			if ( isset( $post_data->$key ) )
				$sets[ $key ] = $post_data->$key;
		if ( count( $sets ) < 1 )
			return;
		global $wpdb;
		$wpdb->update( $wpdb->posts, $sets, [ 'ID' => $post_data->ID ] );
	}

	/**
		@brief		Save the user's last used settings.
		@details	Since v8 the data is stored in the user's meta.
		@since		2014-10-09 06:19:53
	**/
	public function save_last_used_settings( $user_id, $settings )
	{
		update_user_meta( $user_id, 'broadcast_last_used_settings', $settings );
	}

	/**
		@brief		The site options we store.
		@since		2018-01-25 21:07:47
	**/
	public function site_options()
	{
		return array_merge( [
			/**
				@brief		Include sites from all networks.
				@since		2018-01-17 16:20:31
			**/
			'all_networks' => false,
			'blogs_to_hide' => 5,								// How many blogs to auto-hide
			'blogs_hide_overview' => 5,							// Use a summary in the overview if more than this amount of children / siblings.
			'canonical_url' => true,							// Override the canonical URLs with the parent post's.
			'clear_post' => true,								// Clear the post before broadcasting.
			'custom_field_blacklist' => '',						// Internal custom fields that should not be broadcasted.
			'custom_field_protectlist' => '',					// Internal custom fields that should not be overwritten on broadcast
			'custom_field_whitelist' => '',						// Internal custom fields that should be broadcasted in spite of being blacklisted.
			'database_version' => 0,							// Version of database and settings
			'debug' => false,									// Display debug information?
			'debug_ips' => '',									// List of IP addresses that can see debug information, when debug is enabled.
			'debug_to_browser' => false,						// Display debug info in the browser?
			'debug_to_file' => false,							// Save debug info to a file.
			'keep_attachments' => false,						// Stop attachments from being deleted.
			'save_post_decoys' => 1,							// How many nop() hooks to insert into the save_post action before Broadcast itself.
			'save_post_priority' => 640,						// Priority of save_post action. Higher = lets other plugins do their stuff first
			'override_child_permalinks' => false,				// Make the child's permalinks link back to the parent item?
			'post_types' => 'post page',						// Custom post types which use broadcasting
			'existing_attachments' => 'use',					// What to do with existing attachments: use, overwrite, randomize
			'role_broadcast' => [ 'super_admin' ],					// Role required to use broadcast function
			'role_link' => [ 'super_admin' ],						// Role required to use the link function
			'role_broadcast_as_draft' => [ 'super_admin' ],			// Role required to broadcast posts as templates
			'role_broadcast_scheduled_posts' => [ 'super_admin' ],	// Role required to broadcast scheduled, future posts
			'role_taxonomies' => [ 'super_admin' ],					// Role required to broadcast the taxonomies
			'role_custom_fields' => [ 'super_admin' ],				// Role required to broadcast the custom fields
			'savings_calculator_data' => '',						// Data for the savings calculator.
			/**
				@brief		List of taxonomy + term slugs to not broadcast.
				@since		2017-07-10 16:16:28
			**/
			'taxonomy_term_blacklist' => '',
			/**
				@brief		List of taxonomy + term slugs to be protected during broadcast.
				@since		2017-07-10 16:16:28
			**/
			'taxonomy_term_protectlist' => '',
		], parent::site_options() );
	}
}
