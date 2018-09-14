<?php

namespace threewp_broadcast\premium_pack;

/**
	@brief		This is the class that manages all of the Broadcast packs.
	@details	The reason for it being in this namespace is due to being moved from the premium pack and not wanting to break backward compatability.
	@since		2015-10-28 15:11:13
**/
class ThreeWP_Broadcast_Plugin_Pack
	extends \plainview\sdk_broadcast\wordpress\plugin_pack\plugin_pack
{
	/**
		@brief		Is the pack ready to load the plugins?
		@details	If we don't wait until all plugin packs are ready, then some activated plugins won't be found and will be deactivated.
		@since		2015-10-28 23:44:11
	**/
	public $plugins_ready = false;

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_loaded', 100 );
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'threewp_broadcast_broadcasting_started', 'dump_pack_info' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin menu
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Plugin Pack menu.
		@since		2014-05-08 15:16:03
	**/
	public function admin_menu_plugins()
	{
		$r = '';

		$r .= $this->p( __( 'This table shows all Broadcast add-ons that are available via the activated Broadcast add-on packs.', 'threewp-broadcast' ) );

		$r .= $this->get_plugins_table();

		$r .= $this->p( sprintf(
			__( 'The developer can be contacted at: %s', 'threewp-broadcast' ),
			'<a href="mailto:info@plainviewplugins.com">info@plainviewplugins.com</a>'
		) );

		echo $r;
	}

	/**
		@brief		Menu tabs.
		@since		2014-05-10 09:00:29
	**/
	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		$tabs->tab( 'plugins' )
			->callback_this( 'admin_menu_plugins' )
			// Add-ons tab name
			->name( __( 'Available add-ons', 'threewp-broadcast' ) )
			->sort_order( 25 );

		$action = ThreeWP_Broadcast()->new_action( 'plugin_pack_tabs' );
		$action->tabs = $tabs;
		$action->execute();

		$tabs->tab( 'uninstall' )
			->callback_this( 'admin_uninstall' )
			// Uninstall tab name
			->name( __( 'Uninstall', 'threewp-broadcast' ) )
			->sort_order( 75 );

		echo $tabs->render();
	}

	/**
		@brief		The uninstall form.
		@since		2015-10-28 18:45:14
	**/
	public function admin_uninstall()
	{
		$r = '';
		$form = $this->form2();
		$form->prefix( get_class( $this ) );

		$form->markup( 'uninstall_info_1' )
			->p( __( 'This button will remove the database settings for the add-on packs themselves: list of activated plugins, license keys.', 'threewp-broadcast' ) );

		$form->markup( 'uninstall_info_2' )
			->p( __( 'To uninstall the settings for each pack add-on, use the add-on list bulk action.', 'threewp-broadcast' ) );

		$uninstall = $form->primary_button( 'uninstall' )
			->value( __( 'Uninstall the pack settings', 'threewp-broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			if ( $uninstall->pressed() )
			{
				$this->uninstall_internal();
				$this->deactivate_me();
				if( is_network_admin() )
					$url ='ms-admin.php';
				else
					$url ='index.php';
				$this->message(
					__( 'The plugin and all associated settings and database tables have been removed. Please %sfollow this link to complete the uninstallation procedure%s.', 'threewp-broadcast' ),
					sprintf( '<a href="%s" title="%s">', $url, $this->_( 'This link will take you to the index page' ) ),
					'</a>' );
				return;
			}
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		After all of the BC plugin packs have loaded (including their classloaders), we can now load our plugins.
		@since		2015-10-28 14:54:58
	**/
	public function threewp_broadcast_loaded()
	{
		$this->plugins_ready = true;
		$this->plugins();
	}

	/**
		@brief		Hide the premium pack info.
		@since		20131030
	**/
	public function threewp_broadcast_menu( $action )
	{
		$this->remove_premium_pack_info_menu();

		if ( ! is_super_admin() )
			return;

		$action->menu_page
			->submenu( 'bc_pp' )
			->callback_this( 'admin_menu_tabs' )
			// Menu item for menu
			->menu_title( __( 'Add-ons', 'threewp-broadcast' ) )
			// Page title for menu
			->page_title( __( 'Add-ons', 'threewp-broadcast' ) )
			->sort_order( 25 );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Tell the dump about ourself.
		@since		2015-11-20 14:09:13
	**/
	public function dump_pack_info( $action )
	{
		$constants = get_defined_constants();

		foreach( ThreeWP_Broadcast()->get_addon_packs_info() as $pack )
		{
			$define = $pack->get( 'version_define' );
			if ( isset( $constants[ $define ] ) )
				ThreeWP_Broadcast()->debug( '%s version: %s', $define, $constants[ $define ] );
		}

		$enabled_plugins = [];
		foreach( $this->get_site_option( 'enabled_plugins', [] ) as $plugin )
			$enabled_plugins[] = preg_replace( '/.*\\\\/', '', $plugin );
		ThreeWP_Broadcast()->debug( 'Active pack plugins: %s', implode( ', ', $enabled_plugins ) );
	}

	public function edd_get_url()
	{
		return 'https://broadcast.plainviewplugins.com/';
	}

	/**
		@brief		Plugins are provided by the various plugin packs.
		@since		2015-10-29 09:46:49
	**/
	public function get_plugin_classes()
	{
		return [];
	}

	/**
		@brief		Override the plugins method, checking whether the pack is ready to load them.
		@see		$plugins_ready
		@since		2015-10-29 09:28:41
	**/
	public function plugins()
	{
		if ( ! $this->plugins_ready )
			return false;
		return parent::plugins();
	}

	public function remove_premium_pack_info_menu()
	{
		ThreeWP_Broadcast()->menu_page()->forget( 'threewp_broadcast_premium_pack_info' );
	}

	public function site_options()
	{
		return array_merge( [
			'enabled_plugins' => [],
		], parent::site_options() );
	}

	/**
		@brief		Send out an uninstall action to all plugin packs.
		@since		2015-10-28 18:51:43
	**/
	public function uninstall()
	{
		$action = ThreeWP_Broadcast()->new_action( 'plugin_pack_uninstall' );
		$action->execute();
	}
}
