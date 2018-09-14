<?php
/**
 * Plugins related functions and classes.
 *
 * @version		$Rev: 203758 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Abtract class to be used as a plugin template.
 * Must be implemented before using this class and it's recommended to prefix the class to prevent collissions.
 * There are some special functions that have to be declared in implementations to perform main actions:
 * 		- pluginActivate (Protected) Actions to run when activating the plugin.
 * 		- pluginUpdate (Protected) Actions to update the plugin to a new version. (Updating version on DB is done after this).
 * 						Takes plugin running version as a parameter.
 *
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link		http://wiki.alkivia.org/framework/classes/plugin
 */
abstract class akPluginAbstract
{
	/**
	 * Module ID. Is the module internal short name.
	 * Filled in constructor (as a constructor param). Used for translations textdomain.
	 *
	 * @var string
	 */
	public $ID;

	/**
	 * Module version number.
	 *
	 * @since 0.8
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Full path to module main file.
	 * Main file is 'style.css' for themes and the php file with data header for plugins and components.
	 *
	 * @var string
	 */
	protected $mod_file;

	/**
	 * URL to the module folder.
	 *
	 * @var string
	 */
	protected $mod_url;

	/**
	 * Flag to see if module needs to be updated.
	 *
	 * @var boolean
	 */
	protected $needs_update = false;

    /**
	 * Class constructor.
	 * Calls the implementated method 'startUp' if it exists. This is done at plugins loading time.
	 * Prepares admin menus by seting an action for the implemented method '_adminMenus' if it exists.
	 *
	 * @param string $mod_file	Full main plugin's filename (absolute to root).
	 * @param string $ID  Plugin short name (known as plugin ID).
	 * @return spostsPlugin|false	The plugin object or false if not compatible.
	 */
	public function __construct( $mod_file, $ID = '' )
	{
		$this->mod_file = trim($mod_file);

        $this->loadModuleData($ID);
		
		add_action('plugins_loaded', array($this, 'pluginsInit'));

		//if ( ! apply_filters('ak_' . $this->ID . '_disable_admin', $this->getOption('disable-admin-page')) ) {
			add_action('admin_menu', array($this, 'adminMenus'), 5);  // execute prior to PP, to use menu hook
		//}

		// Load styles
		add_action('admin_print_styles', array($this, 'adminStyles'));

		$this->moduleLoad();

		// Activation and deactivation hooks.
		register_activation_hook($this->mod_file, array($this, 'activate'));

		add_action('plugins_loaded', array($this, 'init'));
	}

	/**
	 * Fires on plugin activation.
	 * @return void
	 */
	protected function pluginActivate () {}

	/**
	 * Updates the plugin to a new version.
	 * @param string $version Old plugin version.
	 * @return void
	 */
	protected function pluginUpdate ( $version ) {}

	/**
	 * Activates the plugin. Only runs on first activation.
	 * Saves the plugin version in DB, and calls the 'pluginActivate' method.
	 *
	 * @uses do_action() Calls 'ak_activate_<modID>_plugin' action hook.
	 * @hook register_activation_hook
	 * @access private
	 * @return void
	 */
	final function activate()
	{
        $this->pluginActivate();

        // Save options and version
		add_option($this->ID . '_version', $this->version);

        // Do activated hook.
		do_action('ak_activate_' . $this->ID . '_plugin');
	}

	/**
	 * Init the plugin (In action 'plugins_loaded')
	 * Here whe call the 'pluginUpdate' method.
	 * Also the plugin version and settings are updated here.
	 *
	 * @hook action plugins_loaded
	 * @uses do_action() Calls the 'ak_<modID>_updated' action hook.
	 *
	 * @access private
	 * @return void
	 */
	final function init()
	{
		// First, check if the plugin needs to be updated.
		if ( $this->needs_update ) {
			$version = get_option($this->ID . '_version');
			$this->pluginUpdate($version);

			update_option($this->ID . '_version', $this->version);

			do_action('ak_' . $this->ID . '_updated');
		}
	}

	/**
     * Functions to execute after loading plugins.
     *
     * @return void
     */
    final function pluginsInit ()
    {
		load_plugin_textdomain('capsman-enhanced', false, basename(dirname($this->mod_file)) . '/lang');
    }

    /**
     * Enqueues additional administration styles.
     * Send the framework admin.css file and additionally any other admin.css file
     * found on the module direcotry.
     *
     * @hook action 'admin_print_styles'
     * @uses apply_filters() Calls the 'ak_framework_style_admin' filter on the framework style url.
     * @uses apply_filters() Calls the 'ak_<Mod_ID>_style_admin' filter on the style url.
     * @access private
     *
     * @return void
     */
    final function adminStyles()
    {
		if ( empty( $_REQUEST['page'] ) || ! in_array( $_REQUEST['page'], array( 'capsman', 'capsman-tool' ) ) )
			return;
	
		// FRAMEWORK admin styles.
		$url = apply_filters('ak_framework_style_admin', AK_STYLES_URL . '/admin.css');
		if ( ! empty($url) ) {
   			wp_register_style('ak_framework_admin', $url, false, get_option('ak_framework_version'));
   			wp_enqueue_style('ak_framework_admin');
    	}

        // MODULE admin styles.
		if ( file_exists(dirname($this->mod_file) . '/admin.css') ) {
		    $url = $this->mod_url . '/admin.css';
		} else {
		    $url = '';
		}

		$url = apply_filters('ak_' . $this->ID . '_style_admin', $url);
		if ( ! empty($url) ) {
   			wp_register_style('ak_' . $this->ID . '_admin', $url, array('ak_framework_admin'), $this->version);
   			wp_enqueue_style('ak_' . $this->ID . '_admin');
    	}
		
		if ( file_exists(dirname($this->mod_file) . '/admin.js') ) {
			$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
			$url = $this->mod_url . "/admin{$suffix}.js";
			wp_enqueue_script( 'cme_admin', $url, array('jquery'), CAPSMAN_VERSION, true );
			wp_localize_script( 'cme_admin', 'cmeAdmin', array( 
				'negationCaption' => __( 'Explicity negate this capability by storing as disabled', 'capsman-enhanced' ),
				'typeCapsNegationCaption' => __( 'Explicitly negate these capabilities by storing as disabled', 'capsman-enhanced' ),
				'typeCapUnregistered' => __( 'Post type registration does not define this capability distinctly', 'capsman-enhanced' ),
				'capNegated' => __( 'This capability is explicitly negated. Click to add/remove normally.', 'capsman-enhanced' ), 
				'chkCaption' => __( 'Add or remove this capability from the WordPress role', 'capsman-enhanced' ), 
				'switchableCaption' => __( 'Add or remove capability from the role normally', 'capsman-enhanced' ) ) 
			);
		}
		
    }

	/**
	 * Loads module data and settings.
	 * Data is loaded from the module file headers. Settings from Database and alkivia.ini.
	 *
	 * @return void
	 */
	final private function loadModuleData ( $id )
	{
        $this->mod_url = plugins_url( '', CME_FILE );
	    
		if ( ! isset($this->ID) )
			$this->ID = ( empty($id) ) ? strtolower(basename($this->mod_file, '.php')) : trim($id) ;

   		$old_version = get_option($this->ID . '_version');
		if ( version_compare($old_version, $this->version, 'ne') ) {
			$this->needs_update = true;
		}
	}
	
	/**
	 * Executes as soon as module class is loaded.
	 *
	 * @return void
	 */
	protected function moduleLoad() {}

    /**
     * Fires at 'admin_menus' action hook.
     *
     * @return void
     */
    public function adminMenus () {}
}
