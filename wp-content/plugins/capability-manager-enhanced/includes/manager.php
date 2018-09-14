<?php
/**
 * Capability Manager.
 * Plugin to create and manage roles and capabilities.
 *
 * @version		$Rev: 199485 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals; Copyright (C) 2012-2014 Kevin Behrens
 * @license		GNU General Public License version 2
 * @link		http://agapetry.net
 *

	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
	Modifications Copyright 2012-2018 Kevin Behrens <kevin@agapetry.net>

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

include_once ( AK_CLASSES . '/abstract/plugin.php' );

add_action( 'init', 'cme_update_pp_usage' );  // update early so resulting post type cap changes are applied for this request's UI construction

function cme_update_pp_usage() {
	if ( defined( 'PP_ACTIVE' ) && ( ! empty($_REQUEST['update_filtered_types']) || ! empty($_REQUEST['SaveRole']) ) ) {
		require_once( dirname(__FILE__).'/pp-handler.php' );
		return _cme_update_pp_usage();
	}
}


/**
 * Class cmanCapsManager.
 * Sets the main environment for all Capability Manager components.
 *
 * @author		Jordi Canals, Kevin Behrens
 * @link		http://agapetry.net
 */
class CapabilityManager extends akPluginAbstract
{
	/**
	 * Array with all capabilities to be managed. (Depends on user caps).
	 * The array keys are the capability, the value is its screen name.
	 * @var array
	 */
	var $capabilities = array();

	/**
	 * Array with roles that can be managed. (Depends on user roles).
	 * The array keys are the role name, the value is its translated name.
	 * @var array
	 */
	var $roles = array();

	/**
	 * Current role we are managing
	 * @var string
	 */
	var $current;

	/**
	 * Maximum level current manager can assign to a user.
	 * @var int
	 */
	private $max_level;

	private $log_db_role_objects = array();
	
	var $message;
	
	function __construct( $mod_file, $ID = '' ) {
		$this->ID = 'capsman';
		
		parent::__construct( $mod_file, $ID );
	}
	
	/**
	 * Creates some filters at module load time.
	 *
	 * @see akPluginAbstract#moduleLoad()
	 *
	 * @return void
	 */
    protected function moduleLoad ()
    {
        // Only roles that a user can administer can be assigned to others.
        add_filter('editable_roles', array($this, 'filterEditRoles'));

        // Users with roles that cannot be managed, are not allowed to be edited.
        add_filter('map_meta_cap', array(&$this, 'filterUserEdit'), 10, 4);
		
		// ensure storage, retrieval of db-stored customizations to bbPress dynamic roles
		global $wpdb;
		$role_key = $wpdb->prefix . 'user_roles';
		add_filter( 'option_' . $role_key, array( &$this, 'log_db_roles' ), 0 );
		add_filter( 'option_' . $role_key, array( &$this, 'reinstate_db_roles' ), 50 );
		
		add_filter( 'plugins_loaded', array( &$this, 'processRoleUpdate' ) );
    }
	
	function log_db_roles( $passthru_roles ) {
		global $wp_roles;

		if ( isset($wp_roles) )
			$this->log_db_role_objects = $wp_roles->role_objects;

		return $passthru_roles;
	}
	
	// note: this is only applied when accessing the cme role edit form
	function reinstate_db_roles( $passthru_roles = array() ) {
		global $wp_roles;

		if ( $this->log_db_role_objects ) {
			$intersect = array_intersect_key( $wp_roles->role_objects, $this->log_db_role_objects );
			foreach( array_keys( $intersect ) as $key ) {
				if ( ! empty( $this->log_db_role_objects[$key]->capabilities ) )
					$wp_roles->role_objects[$key]->capabilities = $this->log_db_role_objects[$key]->capabilities;
			}
		}
		
		return $passthru_roles;
	}

	/**
	 * Activates the plugin and sets the new capability 'Manage Capabilities'
	 *
	 * @return void
	 */
	protected function pluginActivate ()
	{
		$this->setAdminCapability();
	}

	/**
	 * Updates Capability Manager to a new version
	 *
	 * @return void
	 */
	protected function pluginUpdate ( $version )
	{
		$backup = get_option($this->ID . '_backup');
		if ( false === $backup ) {		// No previous backup found. Save it!
			global $wpdb;
			$roles = get_option($wpdb->prefix . 'user_roles');
			update_option($this->ID . '_backup', $roles);
		}
	}

	/**
	 * Adds admin panel menus. (At plugins loading time. This is before plugins_loaded).
	 * User needs to have 'manage_capabilities' to access this menus.
	 * This is set as an action in the parent class constructor.
	 *
	 * @hook action admin_menu
	 * @return void
	 */
	public function adminMenus ()
	{
		// First we check if user is administrator and can 'manage_capabilities'.
		if ( current_user_can('administrator') && ! current_user_can('manage_capabilities') ) {
			$this->setAdminCapability();
		}

		add_action( 'admin_menu', array( &$this, 'cme_menu' ), 20 );
	}

	public function cme_menu() {
		$cap_name = ( is_super_admin() ) ? 'manage_capabilities' : 'restore_roles';
		add_management_page(__('Capability Manager', 'capsman-enhanced'),  __('Capability Manager', 'capsman-enhanced'), $cap_name, $this->ID . '-tool', array($this, 'backupTool'));
		
		if ( did_action( 'pp_admin_menu' ) ) { // Put Capabilities link on Permissions menu if Press Permit is active and user has access to it
			global $pp_admin;
			$menu_caption = ( defined('WPLANG') && WPLANG && ( 'en_EN' != WPLANG ) ) ? __('Capabilities', 'capsman-enhanced') : 'Role Capabilities';
			add_submenu_page( $pp_admin->get_menu('options'), __('Capability Manager', 'capsman-enhanced'),  $menu_caption, 'manage_capabilities', $this->ID, array($this, 'generalManager') );
		} else {
			add_users_page( __('Capability Manager', 'capsman-enhanced'),  __('Capabilities', 'capsman-enhanced'), 'manage_capabilities', $this->ID, array($this, 'generalManager'));
		}	
	}
	
	/**
	 * Sets the 'manage_capabilities' cap to the administrator role.
	 *
	 * @return void
	 */
	public function setAdminCapability ()
	{
		$admin = get_role('administrator');
		$admin->add_cap('manage_capabilities');
	}

	/**
	 * Filters roles that can be shown in roles list.
	 * This is mainly used to prevent an user admin to create other users with
	 * higher capabilities.
	 *
	 * @hook 'editable_roles' filter.
	 *
	 * @param $roles List of roles to check.
	 * @return array Restircted roles list
	 */
	function filterEditRoles ( $roles )
	{
	    $this->generateNames();
        $valid = array_keys($this->roles);

        foreach ( $roles as $role => $caps ) {
            if ( ! in_array($role, $valid) ) {
                unset($roles[$role]);
            }
        }

        return $roles;
	}

	/**
	 * Checks if a user can be edited or not by current administrator.
	 * Returns array('do_not_allow') if user cannot be edited.
	 *
	 * @hook 'map_meta_cap' filter
	 *
	 * @param array $caps Current user capabilities
	 * @param string $cap Capability to check
	 * @param int $user_id Current user ID
	 * @param array $args For our purpose, we receive edited user id at $args[0]
	 * @return array Allowed capabilities.
	 */
	function filterUserEdit ( $caps, $cap, $user_id, $args )
	{
	    if ( ! in_array( $cap, array( 'edit_user', 'delete_user', 'promote_user', 'remove_user' ) ) || ( ! isset($args[0]) ) || $user_id == (int) $args[0] ) {
	        return $caps;
	    }
		
		$user = new WP_User( (int) $args[0] );
		
		$this->generateNames();
		
		if ( defined( 'CME_LEGACY_USER_EDIT_FILTER' ) && CME_LEGACY_USER_EDIT_FILTER ) {
			$valid = array_keys($this->roles);
			
			foreach ( $user->roles as $role ) {
				if ( ! in_array($role, $valid) ) {
					$caps = array('do_not_allow');
					break;
				}
			}
		} else {
			global $wp_roles;

			foreach ( $user->roles as $role ) {
				$r = get_role( $role );
    			$level = ak_caps2level($r->capabilities);
				
				if ( ( ! $level ) && ( 'administrator' == $role ) )
					$level = 10;
				
	    		if ( $level > $this->max_level ) {
		    		$caps = array('do_not_allow');
					break;
			    }
    		}
			
		}

		return $caps;
	}

	function processRoleUpdate() {
		$this->current = get_option('default_role');	// By default we manage the default role.
		
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ( ! empty($_REQUEST['SaveRole']) || ! empty($_REQUEST['AddCap']) ) ) {
			if ( ! current_user_can('manage_capabilities') && ! current_user_can('administrator') ) {
				// TODO: Implement exceptions.
				wp_die('<strong>' .__('What do you think you\'re doing?!?', 'capsman-enhanced') . '</strong>');
			}

			//$this->current = get_option('default_role');	// By default we manage the default role.

			check_admin_referer('capsman-general-manager');
			$this->processAdminGeneral();
		}
	}
	
	/**
	 * Manages global settings admin.
	 *
	 * @hook add_submenu_page
	 * @return void
	 */
	function generalManager () {
		if ( ! current_user_can('manage_capabilities') && ! current_user_can('administrator') ) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('What do you think you\'re doing?!?', 'capsman-enhanced') . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			if ( empty($_REQUEST['SaveRole']) && empty($_REQUEST['AddCap']) ) {
				check_admin_referer('capsman-general-manager');
				$this->processAdminGeneral();
			} elseif ( ! empty($_REQUEST['SaveRole']) ) {
				ak_admin_notify( $this->message );  // moved update operation to earlier action to avoid UI refresh issues.  But outputting notification there breaks styling.
			} elseif ( ! empty($_REQUEST['AddCap']) ) {
				ak_admin_notify( $this->message );
			}
		}

		$this->generateNames();
		$roles = array_keys($this->roles);

		if ( isset($_GET['action']) && 'delete' == $_GET['action']) {
			require_once( dirname(__FILE__).'/handler.php' );
			$capsman_modify = new CapsmanHandler( $this );
			$capsman_modify->adminDeleteRole();
		}

		if ( ! in_array($this->current, $roles) ) {    // Current role has been deleted.
			$this->current = array_shift($roles);
		}

		include ( AK_CMAN_LIB . '/admin.php' );
	}

	/**
	 * Processes and saves the changes in the general capabilities form.
	 *
	 * @return void
	 */
	private function processAdminGeneral ()
	{
		if (! isset($_POST['action']) || 'update' != $_POST['action'] ) {
		    // TODO: Implement exceptions. This must be a fatal error.
			ak_admin_error(__('Bad form Received', 'capsman-enhanced'));
			return;
		}

		$post = stripslashes_deep($_POST);
		if ( empty ($post['caps']) ) {
		    $post['caps'] = array();
		}

		$this->current = $post['current'];
		
		// Select a new role.
		if ( ! empty($post['LoadRole']) ) {
			$this->current = $post['role'];
		} else {
			require_once( dirname(__FILE__).'/handler.php' );
			$capsman_modify = new CapsmanHandler( $this );
			$capsman_modify->processAdminGeneral( $post );
		}
	}
	
	/**
	 * Callback function to create names.
	 * Replaces underscores by spaces and uppercases the first letter.
	 *
	 * @access private
	 * @param string $cap Capability name.
	 * @return string	The generated name.
	 */
	function _capNamesCB ( $cap )
	{
		$cap = str_replace('_', ' ', $cap);
		//$cap = ucfirst($cap);

		return $cap;
	}

	/**
	 * Generates an array with the system capability names.
	 * The key is the capability and the value the created screen name.
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	function generateSysNames ()
	{
		$this->max_level = 10;
		$this->roles = ak_get_roles(true);
		$caps = array();

		foreach ( array_keys($this->roles) as $role ) {
			$role_caps = get_role($role);
			$caps = array_merge( $caps, (array) $role_caps->capabilities );  // user reported PHP 5.3.3 error without array cast
		}

		$keys = array_keys($caps);
		$names = array_map(array($this, '_capNamesCB'), $keys);
		$this->capabilities = array_combine($keys, $names);

		asort($this->capabilities);
	}

	/**
	 * Generates an array with the user capability names.
	 * If user has 'administrator' role, system roles are generated.
	 * The key is the capability and the value the created screen name.
	 * A user cannot manage more capabilities that has himself (Except for administrators).
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	function generateNames ()
	{
		if ( current_user_can('administrator') || ( is_multisite() && is_super_admin() ) ) {
			$this->generateSysNames();
		} else {
		    global $user_ID;
		    $user = new WP_User($user_ID);
		    $this->max_level = ak_caps2level($user->allcaps);
			
		    $keys = array_keys($user->allcaps);
    		$names = array_map(array($this, '_capNamesCB'), $keys);
			
	    	$this->capabilities = ( $keys ) ? array_combine($keys, $names) : array();

		    $roles = ak_get_roles(true);
    		unset($roles['administrator']);

			if ( ( defined( 'CME_LEGACY_USER_EDIT_FILTER' ) && CME_LEGACY_USER_EDIT_FILTER ) || ( ! empty( $_REQUEST['page'] ) && 'capsman' == $_REQUEST['page'] ) ) {
				foreach ( $user->roles as $role ) {			// Unset the roles from capability list.
					unset ( $this->capabilities[$role] );
					unset ( $roles[$role]);					// User cannot manage his roles.
				}
			}
			
	    	asort($this->capabilities);

		    foreach ( array_keys($roles) as $role ) {
			    $r = get_role($role);
    			$level = ak_caps2level($r->capabilities);

	    		if ( $level > $this->max_level ) {
		    		unset($roles[$role]);
			    }
    		}

	    	$this->roles = $roles;
		}
	}

	/**
	 * Manages backup, restore and resset roles and capabilities
	 *
	 * @hook add_management_page
	 * @return void
	 */
	function backupTool ()
	{
		if ( ! current_user_can('restore_roles') && ! is_super_admin() ) {
		    // TODO: Implement exceptions.
			wp_die('<strong>' .__('What do you think you\'re doing?!?', 'capsman-enhanced') . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			require_once( dirname(__FILE__).'/backup-handler.php' );
			$cme_backup_handler = new Capsman_BackupHandler( $this );
			$cme_backup_handler->processBackupTool();
		}

		if ( isset($_GET['action']) && 'reset-defaults' == $_GET['action']) {
			require_once( dirname(__FILE__).'/backup-handler.php' );
			$cme_backup_handler = new Capsman_BackupHandler( $this );
			$cme_backup_handler->backupToolReset();
		}

		include ( AK_CMAN_LIB . '/backup.php' );
	}
}
