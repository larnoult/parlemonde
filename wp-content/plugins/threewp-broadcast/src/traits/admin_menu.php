<?php

namespace threewp_broadcast\traits;

use \threewp_broadcast\maintenance;

/**
	@brief		Methods that handle the menu in the admin interface.
	@since		2014-10-19 14:21:16
**/
trait admin_menu
{
	public function admin_menu()
	{
		$action = $this->new_action( 'admin_menu' );
		$action->execute();

		$action = $this->new_action( 'menu' );
		$action->broadcast = $this;
		$action->menu_page = $this->menu_page();
		$action->execute();

		// Hook into save_post, no matter is the meta box is displayed or not.
		$this->hook_save_post();
	}

	public function admin_print_styles()
	{
		$this->enqueue_js();
		wp_enqueue_style( 'threewp_broadcast', $this->paths[ 'url' ] . '/css/css.css', '', $this->plugin_version  );
	}

	public function admin_menu_broadcast_info()
	{
		$r = $this->html_css();
		$r .= file_get_contents( __DIR__ . '/../../html/broadcast_info.html' );
		echo $r;
	}

	/**
		@brief		Show maintenance options.
		@since		20131107
	**/
	public function admin_menu_maintenance()
	{
		$maintenance = new maintenance\controller;
		echo $maintenance;
	}

	public function admin_menu_premium_pack_info()
	{
		$r = '';
		$r .= $this->html_css();
		$contents = file_get_contents( __DIR__ . '/../../html/premium_pack_info.html' );
		$r .= $this->wrap( $contents, $this->_( 'Broadcast add-on packs info' ) );
		echo $r;
	}

	public function admin_menu_settings()
	{
		$this->enqueue_js();
		$form = $this->form();
		$form->id( 'broadcast_settings' );
		$form->css_class( 'plainview_form_auto_tabs' );
		$r = '';

		// Check that the version of BC and the packs mostly match.
		$constants = get_defined_constants();
		$bc_major_version = THREEWP_BROADCAST_VERSION;
		$bc_major_version = preg_replace( '/\..*/', '', $bc_major_version );
		foreach( $this->get_addon_packs_info() as $pack )
		{
			$define = $pack->get( 'version_define' );
			if ( ! isset( $constants[ $define ] ) )
				continue;

			// Extract the major version.
			$pack_version = $constants[ $define ];
			$pack_version = preg_replace( '/\..*/', '', $pack_version );

			if ( $pack_version != $bc_major_version )
			{
				$message = $this->p(
					__( 'Network admin! To ensure compatibility, please upgrade your version of the Broadcast %s add-on pack (%s) to match the major version of Broadcast itself: %s', 'threewp-broadcast' ),
					$pack->get( 'name' ),
					$constants[ $define ],
					$bc_major_version
				);

				$r .= sprintf( '<div class="inline error notice">%s</div>', $message );
			}
		}

		// --CUSTOM FIELDS------------------------------------------------------------------------------------------

		$fs = $form->fieldset( 'custom_field_handling' );
		// Legend for custom field handling fieldset
		$fs->legend->label( __( 'Custom field handling', 'threewp-broadcast' ) );

		$fs->markup( 'm_custom_field_handling1' )
			->p( __( 'All custom fields are passed through the blacklist and then the whitelist. If the field exists in the blacklist, it will not be broadcast - unless it is specified in the whitelist.', 'threewp-broadcast' ) );

		$fs->markup( 'm_custom_field_handling2' )
			->p( sprintf(
				__( 'You can use wildcards: %s will match all fields that start with %s and end with %s. If you wish to match all fields except a few, use %s in the blacklist and then the exceptions in the whitelist.', 'threewp-broadcast' ),
				'<code>field_*123</code>',
				'<code>field_</code>',
				'<code>123</code>',
				'<code>*</code>'
				)
			);

		$fs->markup( 'm_custom_field_handling3' )
			->markup(
				sprintf( __( 'For more detailed documentation, see the %scustom field documentation page%s', 'threewp-broadcast' ),
				'<a href="https://broadcast.plainviewplugins.com/doc/custom-fields/">',
				'</a>'
			) );

		$custom_field_blacklist = $this->get_site_option( 'custom_field_blacklist' );
		$custom_field_blacklist = str_replace( ' ', "\n", $custom_field_blacklist );
		$custom_field_blacklist = $fs->textarea( 'custom_field_blacklist' )
			->cols( 40, 10 )
			// Custom field blacklist.
			->description( __( 'Do not broadcast these custom fields.', 'threewp-broadcast' ) )
			->label( __( 'Custom field blacklist', 'threewp-broadcast' ) )
			->trim()
			->value( $custom_field_blacklist );

		$custom_field_whitelist = $this->get_site_option( 'custom_field_whitelist' );
		$custom_field_whitelist = str_replace( ' ', "\n", $custom_field_whitelist );
		$custom_field_whitelist = $fs->textarea( 'custom_field_whitelist' )
			->cols( 40, 10 )
			// Custom field whitelist.
			->description( __( 'Exceptions to the blacklist.', 'threewp-broadcast' ) )
			->label( __( 'Custom field whitelist', 'threewp-broadcast' ) )
			->trim()
			->value( $custom_field_whitelist );

		$custom_field_protectlist = $this->get_site_option( 'custom_field_protectlist' );
		$custom_field_protectlist = str_replace( ' ', "\n", $custom_field_protectlist );
		$custom_field_protectlist = $fs->textarea( 'custom_field_protectlist' )
			->cols( 40, 10 )
			// Custom field protectlist.
			->description( __( 'Do not overwrite the following fields on the child blogs if they exist.', 'threewp-broadcast' ) )
			->label( __( 'Custom field protectlist', 'threewp-broadcast' ) )
			->trim()
			->value( $custom_field_protectlist );

		// --CUSTOM POST TYPES--------------------------------------------------------------------------------------

		$fs = $form->fieldset( 'fs_post_types' );
		// Label for fieldset
		$fs->legend->label( __( 'Custom post types', 'threewp-broadcast' ) );

		$post_types = $this->get_site_option( 'post_types' );
		$post_types = str_replace( ' ', "\n", $post_types );

		$post_types_input = $fs->textarea( 'post_types' )
			->cols( 20, 10 )
			->label( __( 'Custom post types to broadcast', 'threewp-broadcast' ) )
			->value( $post_types );
		$label = sprintf( __( 'A list of custom post types that have broadcasting enabled. The default value is %s.', 'threewp-broadcast' ), '<code>post<br/>page</code>' );
		$post_types_input->description->set_unfiltered_label( $label );

		$blog_post_types = $this->get_blog_post_types();

		$fs->markup( 'cpt_m1' )
			->p( __( 'Custom post types must be specified using their internal Wordpress names on a new line each. It is not possible to automatically make a list of available post types on the whole network because of a limitation within Wordpress (the current blog knows only of its own custom post types).', 'threewp-broadcast' ) );
		$fs->markup( 'cpt_m2' )
			->p( sprintf(
				__( 'The custom post types registered on <em>this</em> blog are: %s', 'threewp-broadcast' ),
				'<code>' . implode( ', ', $blog_post_types ) . '</code>' )
			);

		// --DEBUG--------------------------------------------------------------------------------------------------

		$this->add_debug_settings_to_form( $form );

		// --MISC---------------------------------------------------------------------------------------------------

		$fs = $form->fieldset( 'misc' );
		// Label for fieldset
		$fs->legend->label( __( 'Miscellaneous', 'threewp-broadcast' ) );

		$clear_post = $fs->checkbox( 'clear_post' )
			->description( __( 'The POST PHP variable is data sent when updating posts. Most plugins are fine if the POST is cleared before broadcasting, while others require that the data remains intact. Uncheck this setting if you notice that child posts are not being treated the same on the child blogs as they are on the parent blog.', 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'Clear POST', 'threewp-broadcast' ) )
			->checked( $this->get_site_option( 'clear_post' ) );

		$all_networks = $fs->checkbox( 'all_networks' )
			->description( __( 'In the Broadcast meta box, include all the sites on all networks available to a user, or just the sites on the current network.', 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'Include all networks', 'threewp-broadcast' ) )
			->checked( $this->get_site_option( 'all_networks' ) );

		$keep_attachments = $fs->checkbox( 'keep_attachments' )
			->description( __( 'Normally Broadcast will delete all existing attachments from an existing child post before copying the attachments anew. This option will prevent the deletion / housekeeping during broadcasting.', 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'Keep attachments', 'threewp-broadcast' ) )
			->checked( $this->get_site_option( 'keep_attachments' ) );

		$save_post_decoys = $fs->number( 'save_post_decoys' )
			->description( __( "How many save_post hook decoys to insert before the real Broadcast save_post hook. This value should be raised if you notice that Broadcast isn't doing anything. This is due to a bug in Wordpress when other plugins call remove_action on the save_post hook.", 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'save_post decoys', 'threewp-broadcast' ) )
			->min( 0 )
			->required()
			->size( 2, 2 )
			->value( $this->get_site_option( 'save_post_decoys' ) );

		$save_post_priority = $fs->number( 'save_post_priority' )
			->description( __( 'The priority for the save_post hook. Should be after all other plugins have finished modifying the post. Default is 640.', 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'save_post priority', 'threewp-broadcast' ) )
			->min( 1 )
			->required()
			->size( 5, 5 )
			->value( $this->get_site_option( 'save_post_priority' ) );

		$blogs_to_hide = $fs->number( 'blogs_to_hide' )
			->description( __( 'In the broadcast meta box, after how many blogs the list should be auto-hidden.', 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'Blogs to hide', 'threewp-broadcast' ) )
			->min( 1 )
			->required()
			->size( 3, 3 )
			->value( $this->get_site_option( 'blogs_to_hide' ) );

		$blogs_hide_overview = $fs->number( 'blogs_hide_overview' )
			->description( __( 'How many children to display in the overview before making the list into a summary.', 'threewp-broadcast' ) )
			// Input label.
			->label( __( 'Display in overview', 'threewp-broadcast' ) )
			->min( 1 )
			->required()
			->size( 3, 3 )
			->value( $this->get_site_option( 'blogs_hide_overview' ) );

		$get_existing_attachment_actions = $this->new_action( 'get_existing_attachment_actions' );
		$get_existing_attachment_actions->execute();
		$actions = $get_existing_attachment_actions->get_actions();
		$actions = array_flip( $actions );
		ksort( $actions );
		$actions = array_flip( $actions );

		$existing_attachments = $fs->select( 'existing_attachments' )
			->description( __( 'Action to take when attachments with the same filename already exist on the child blog.', 'threewp-broadcast' ) )
			// What to do with existing attachments when broadcasting?
			->label( __( 'Existing attachments', 'threewp-broadcast' ) )
			// Array flip because we till be getting [ key => description ]
			->opts( $actions )
			->required()
			->value( $this->get_site_option( 'existing_attachments', 'use' ) );

		// --ROLES--------------------------------------------------------------------------------------------------

		$fs = $form->fieldset( 'roles' );
		// Label for fieldset
		$fs->legend->label( __( 'Roles', 'threewp-broadcast' ) );

		$roles = $this->roles_as_options();

		$fs->markup( 'm_roles' )
			->p( __( 'Multiple roles may be selected. Each role must be individually selected, since there is no automatic hierarchy where, for example, authors automatically include the editor role. Note that only the roles on this blog can be shown.', 'threewp-broadcast' ) );

		$role_broadcast = $fs->select( 'role_broadcast' )
			->value( $this->get_site_option( 'role_broadcast' ) )
			->description( __( 'The broadcast access role is the user role required to use the broadcast function at all.', 'threewp-broadcast' ) )
			// Role needed to...
			->label( __( 'Broadcast', 'threewp-broadcast' ) )
			->multiple()
			->opts( $roles );

		$role_link = $fs->select( 'role_link' )
			->value( $this->get_site_option( 'role_link' ) )
			->description( __( 'When a post is linked with broadcasted posts, the child posts are updated / deleted when the parent is updated.', 'threewp-broadcast' ) )
			// Role needed to...
			->label( __( 'Link to child posts', 'threewp-broadcast' ) )
			->multiple()
			->opts( $roles );

		$role_custom_fields = $fs->select( 'role_custom_fields' )
			->value( $this->get_site_option( 'role_custom_fields' ) )
			->description( __( 'Which role is needed to allow custom field broadcasting?', 'threewp-broadcast' ) )
			// Role needed to...
			->label( __( 'Broadcast custom fields', 'threewp-broadcast' ) )
			->multiple()
			->opts( $roles );

		$role_taxonomies = $fs->select( 'role_taxonomies' )
			->value( $this->get_site_option( 'role_taxonomies' ) )
			->description( __( 'Which role is needed to allow taxonomy broadcasting? The taxonomies must have the same slug on all blogs.', 'threewp-broadcast' ) )
			// Role needed to...
			->label( __( 'Broadcast taxonomies', 'threewp-broadcast' ) )
			->multiple()
			->opts( $roles );

		$role_broadcast_as_draft = $fs->select( 'role_broadcast_as_draft' )
			->value( $this->get_site_option( 'role_broadcast_as_draft' ) )
			->description( __( 'Which role is needed to broadcast drafts?', 'threewp-broadcast' ) )
			// Role needed to...
			->label( __( 'Broadcast as draft', 'threewp-broadcast' ) )
			->multiple()
			->opts( $roles );

		$role_broadcast_scheduled_posts = $fs->select( 'role_broadcast_scheduled_posts' )
			->value( $this->get_site_option( 'role_broadcast_scheduled_posts' ) )
			->description( __( 'Which role is needed to broadcast scheduled (future) posts?', 'threewp-broadcast' ) )
			// Role needed to...
			->label( __( 'Broadcast scheduled posts', 'threewp-broadcast' ) )
			->multiple()
			->opts( $roles );

		// --SEO----------------------------------------------------------------------------------------------------

		$fs = $form->fieldset( 'seo' );
		$fs->legend->label( __( 'SEO', 'threewp-broadcast' ) );

		$override_child_permalinks = $fs->checkbox( 'override_child_permalinks' )
			->checked( $this->get_site_option( 'override_child_permalinks' ) )
			->description( __( "Use the parent post's permalink for the children. If checked, child posts will link back to the parent post.", 'threewp-broadcast' ) )
			// SEO setting
			->label( __( "Use parent permalink", 'threewp-broadcast' ) );

		$canonical_url = $fs->checkbox( 'canonical_url' )
			->checked( $this->get_site_option( 'canonical_url' ) )
			->description( __( "Child posts have their canonical URLs pointed to the URL of the parent post. This automatically disables the canonical URL from Yoast's Wordpress SEO plugin.", 'threewp-broadcast' ) )
			// SEO setting
			->label( __( 'Canonical URL', 'threewp-broadcast' ) );

		// --TAXONOMIES------------------------------------------------------------------------------------------

		$fs = $form->fieldset( 'taxonomy_handling' );
		// Legend for taxonomy handling fieldset
		$fs->legend->label( __( 'Taxonomy handling', 'threewp-broadcast' ) );

		$fs->markup( 'm_taxonomy_handling' )
			->markup( __( 'Taxonomy term meta is passed through the two lists below. If the meta key exists in the blacklist, it will not be broadcast. If a term has an existing meta key set, the value will not be overwritten if specified in the protect list.', 'threewp-broadcast' ) );

		$fs->markup( 'm_taxonomy_handling2' )
			->markup( __( 'The format for both lists is the same: Each line must contain TAXONOMYSLUG TERMSLUG METAKEY', 'threewp-broadcast' ) );

		$fs->markup( 'm_taxonomy_handling3' )
			->markup( __( 'More than one METAKEY can be specified per line. Wildcards can be used for all columns.', 'threewp-broadcast' ) );

		$fs->markup( 'm_taxonomy_handling4' )
			->markup(
				sprintf( __( 'For more detailed documentation, see the %staxonomy handling documentation page%s', 'threewp-broadcast' ),
				'<a href="https://broadcast.plainviewplugins.com/doc/taxonomy-handling/">',
				'</a>'
			) );

		$taxonomy_term_blacklist = $this->get_site_option( 'taxonomy_term_blacklist' );
		$taxonomy_term_blacklist = $fs->textarea( 'taxonomy_term_blacklist' )
			->cols( 40, 10 )
			// Taxonomy term blacklist.
			->description( __( 'Do not broadcast these taxonomy term meta keys.', 'threewp-broadcast' ) )
			->label( __( 'Taxonomy term blacklist', 'threewp-broadcast' ) )
			->placeholder( 'category great_cars term_icon term_name' )
			->trim()
			->value( $taxonomy_term_blacklist );

		$taxonomy_term_protectlist = $this->get_site_option( 'taxonomy_term_protectlist' );
		$taxonomy_term_protectlist = $fs->textarea( 'taxonomy_term_protectlist' )
			->cols( 40, 10 )
			// Taxonomy term protectlist.
			->description( __( 'Do not overwrite the following term meta keys on the child blogs if they exist.', 'threewp-broadcast' ) )
			->label( __( 'Taxonomy term protectlist', 'threewp-broadcast' ) )
			->placeholder( 'post_* camera-rev* icon*' )
			->trim()
			->value( $taxonomy_term_protectlist );

		// ---------------------------------------------------------------------------------------------------------

		$save = $form->primary_button( 'save' )
			->value( __( 'Save settings', 'threewp-broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$this->update_site_option( 'role_broadcast', $role_broadcast->get_post_value() );
			$this->update_site_option( 'role_link', $role_link->get_post_value() );
			$this->update_site_option( 'role_taxonomies', $role_taxonomies->get_post_value() );
			$this->update_site_option( 'role_custom_fields', $role_custom_fields->get_post_value() );
			$this->update_site_option( 'role_broadcast_as_draft', $role_broadcast_as_draft->get_post_value() );
			$this->update_site_option( 'role_broadcast_scheduled_posts', $role_broadcast_scheduled_posts->get_post_value() );

			$form->post()->use_post_values();
			$post_types = $form->input( 'post_types' )->get_value();
			$post_types = $this->lines_to_string( $post_types );
			$this->update_site_option( 'post_types', $post_types);

			$this->update_site_option( 'override_child_permalinks', $override_child_permalinks->is_checked() );
			$this->update_site_option( 'canonical_url', $canonical_url->is_checked() );

			$custom_field_blacklist = $custom_field_blacklist->get_post_value();
			$custom_field_blacklist = $this->lines_to_string( $custom_field_blacklist );
			$this->update_site_option( 'custom_field_blacklist', $custom_field_blacklist );

			$custom_field_whitelist = $custom_field_whitelist->get_post_value();
			$custom_field_whitelist = $this->lines_to_string( $custom_field_whitelist );
			$this->update_site_option( 'custom_field_whitelist', $custom_field_whitelist );

			$custom_field_protectlist = $custom_field_protectlist->get_post_value();
			$custom_field_protectlist = $this->lines_to_string( $custom_field_protectlist );
			$this->update_site_option( 'custom_field_protectlist', $custom_field_protectlist );

			$this->update_site_option( 'all_networks', $all_networks->is_checked() );
			$this->update_site_option( 'clear_post', $clear_post->is_checked() );
			$this->update_site_option( 'keep_attachments', $keep_attachments->is_checked() );
			$this->update_site_option( 'save_post_priority', $save_post_priority->get_post_value() );
			$this->update_site_option( 'save_post_decoys', $save_post_decoys->get_post_value() );
			$this->update_site_option( 'blogs_to_hide', $blogs_to_hide->get_post_value() );
			$this->update_site_option( 'blogs_hide_overview', $blogs_hide_overview->get_post_value() );
			$this->update_site_option( 'existing_attachments', $existing_attachments->get_post_value() );

			$taxonomy_term_blacklist = $taxonomy_term_blacklist->get_post_value();
			$this->update_site_option( 'taxonomy_term_blacklist', $taxonomy_term_blacklist );

			$taxonomy_term_protectlist = $taxonomy_term_protectlist->get_post_value();
			$this->update_site_option( 'taxonomy_term_protectlist', $taxonomy_term_protectlist );

			$this->save_debug_settings_from_form( $form );

			$r .= $this->info_message_box()->_( __( 'Settings saved!', 'threewp-broadcast' ) );

			echo $r;
			$_POST = [];
			$function = __FUNCTION__;
			echo $this->$function();
			return;
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show system info.
		@since		2015-07-14 21:17:39
	**/
	public function admin_menu_system_info()
	{
		$table = $this->get_system_info_table();
		echo $table;
	}

	public function admin_menu_tabs()
	{
		$tabs = $this->tabs();

		$tabs->tab( 'settings' )
			->callback_this( 'admin_menu_settings' )
			// Name of tab
			->name( __( 'Settings', 'threewp-broadcast' ) )
			->sort_order( 25 );		// Always first.

		$tabs->tab( 'maintenance' )
			->callback_this( 'admin_menu_maintenance' )
			// Name of tab
			->name( __( 'Maintenance', 'threewp-broadcast' ) );

		$tabs->tab( 'system_info' )
			->callback_this( 'admin_menu_system_info' )
			// Name of tab
			->name( __( 'System info', 'threewp-broadcast' ) );

		$this->savings_calculator_tabs( $tabs );

		$tabs->tab( 'uninstall' )
			->callback_this( 'admin_uninstall' )
			// Name of tab
			->name( __( 'Uninstall', 'threewp-broadcast' ) )
			->sort_order( 90 );		// Always last.

		echo $tabs->render();
	}

	/**
		@brief		Init the admin menu trait.
		@since		2017-01-31 15:08:51
	**/
	public function admin_menu_trait_init()
	{
		$this->add_action( 'admin_menu' );
		$this->add_action( 'admin_print_styles' );
		$this->add_action( 'network_admin_menu', 'admin_menu' );

		// The plugin table.
		$this->add_filter( 'network_admin_plugin_action_links', 'plugin_action_links', 10, 4 );
		$this->add_filter( 'plugin_action_links', 'plugin_action_links', 10, 4 );
		$this->add_filter( 'plugin_row_meta', 10, 2 );

		$this->add_action( 'threewp_broadcast_menu', 5 );
		$this->add_action( 'threewp_broadcast_menu', 'threewp_broadcast_menu_final', 100 );
	}

	/**
		@brief		Allow tabs to be shown when deleting / trashing / whatever a post from the post overview.
		@since		2014-10-19 14:22:54
	**/
	public function broadcast_menu_tabs()
	{
		$tabs = $this->tabs()
			->default_tab( 'admin_menu_broadcast_info' )
			->get_key( 'action' );

		if ( isset( $_GET[ 'action' ] ) )
		{
			switch( $_GET[ 'action' ] )
			{
				case 'user_delete':
					$tabs->tab( 'user_delete' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Delete the child post', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Delete child', 'threewp-broadcast' ) );
					break;
				case 'user_delete_all':
					$tabs->tab( 'user_delete_all' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Delete all child posts', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Delete all children', 'threewp-broadcast' ) );
					break;
				case 'user_find_orphans':
					$tabs->tab( 'user_find_orphans' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Find orphans', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Find orphans', 'threewp-broadcast' ) );
					break;
				case 'user_restore':
					$tabs->tab( 'user_restore' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Restore the child post from the trash', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Restore child', 'threewp-broadcast' ) );
					break;
				case 'user_restore_all':
					$tabs->tab( 'user_restore_all' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Restore all of the child posts from the trash', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Restore all', 'threewp-broadcast' ) );
					break;
				case 'user_trash':
					$tabs->tab( 'user_trash' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Trash the child post', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Trash child', 'threewp-broadcast' ) );
					break;
				case 'user_trash_all':
					$tabs->tab( 'user_trash_all' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Trash all child posts', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Trash all children', 'threewp-broadcast' ) );
					break;
				case 'user_unlink':
					$tabs->tab( 'user_unlink' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Unlink the child post', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Unlink child', 'threewp-broadcast' ) );
					break;
				case 'user_unlink_all':
					$tab = $tabs->tab( 'user_unlink_all' )
						->callback_this( 'user_unlink' )
						// Tab heading for non-admins when doing post actions.
						->heading( __( 'Unlink all child posts', 'threewp-broadcast' ) )
						// Tab name for non-admins when doing post actions.
						->name( __( 'Unlink all children', 'threewp-broadcast' ) );
					break;
			}
		}

		$tabs->tab( 'admin_menu_broadcast_info' )
			->callback_this( 'admin_menu_broadcast_info' )
			// Tab name for non-admins
			->name( __( 'Broadcast information', 'threewp-broadcast' ) );

		$action = $this->new_action( 'broadcast_menu_tabs' );
		$action->tabs = $tabs;
		$action->execute();

		echo $tabs->render();
	}

	/**
		@brief		plugin_row_meta
		@since		2017-01-31 15:10:07
	**/
	public function plugin_row_meta( $input, $file )
	{
		// We only care about ourself.
		if ( strpos( $file, $this->paths( 'filename' ) ) === false )
			return $input;

		$links = [];

		if ( ThreeWP_Broadcast()->menu_page()->has( 'threewp_broadcast_premium_pack_info' ) )
			$links []= '<a href="https://broadcast.plainviewplugins.com" title="View the available Broadcast add-on packs">Add-ons</a>';

		$input = array_merge( $input, $links );

		return $input;
	}

	/**
		@brief		Adds to the broadcast menu.
		@param		threewp_broadcast		$threewp_broadcast		The broadcast object.
		@since		20130927
	**/
	public function threewp_broadcast_menu( $action )
	{
		if ( $this->display_premium_pack_info && is_super_admin() )
		$this->add_submenu_page(
				'threewp_broadcast',
				// Page heading
				__( 'Add-on packs info', 'threewp-broadcast' ),
				// Menu item name
				__( 'Add-on packs', 'threewp-broadcast' ),
				'edit_posts',
				'threewp_broadcast_premium_pack_info',
				[ &$this, 'admin_menu_premium_pack_info' ]
			);
	}

	/**
		@brief		Adds to the broadcast menu.
		@param		threewp_broadcast		$threewp_broadcast		The broadcast object.
		@since		20130927
	**/
	public function threewp_broadcast_menu_final( $action )
	{
		if ( ! $this->display_broadcast_menu )
			return;

		if ( ! static::user_has_roles( $this->get_site_option( 'role_broadcast' ) ) )
			return;

		if ( is_super_admin() )
			$target = 'admin_menu_tabs';
		else
			$target = 'broadcast_menu_tabs';

		$this->menu_page()
			->callback_this( $target )
			->capability( 'edit_posts' )
			->menu_slug( 'threewp-broadcast' )
			// Menu title
			->menu_title( __( 'Broadcast', 'threewp-broadcast' ) )
			// Page title
			->page_title( __( 'Broadcast', 'threewp-broadcast' ) )
			->icon_url( 'dashicons-rss' );

		$this->menu_page()
			->add_all();
	}

}
