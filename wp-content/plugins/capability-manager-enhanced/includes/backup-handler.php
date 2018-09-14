<?php

class Capsman_BackupHandler
{
	var $cm;

	function __construct( $manager_obj ) {
		if ( ! is_super_admin() && ! current_user_can( 'restore_roles' ) )
			wp_die( __( 'You do not have permission to restore roles.', 'capsman-enhanced' ) );
	
		$this->cm = $manager_obj;
	}
	
	/**
	 * Processes backups and restores.
	 *
	 * @return void
	 */
	function processBackupTool ()
	{
		if ( isset($_POST['Perform']) ) {
			check_admin_referer('capsman-backup-tool');
		
			global $wpdb;
			$wp_roles = $wpdb->prefix . 'user_roles';
			$cm_roles = 'capsman_backup';

			switch ( $_POST['action'] ) {
				case 'backup':
					$roles = get_option($wp_roles);
					update_option($cm_roles, $roles);
					ak_admin_notify(__('New backup saved.', 'capsman-enhanced'));
					break;
				case 'restore':
					$roles = get_option($cm_roles);
					if ( $roles ) {
						update_option($wp_roles, $roles);
						ak_admin_notify(__('Roles and Capabilities restored from last backup.', 'capsman-enhanced'));
					} else {
						ak_admin_error(__('Restore failed. No backup found.', 'capsman-enhanced'));
					}
					break;
			}
		}
	}
	
	/**
	 * Resets roles to WordPress defaults.
	 *
	 * @return void
	 */
	function backupToolReset ()
	{
		check_admin_referer('capsman-reset-defaults');
	
		require_once(ABSPATH . 'wp-admin/includes/schema.php');

		if ( ! function_exists('populate_roles') ) {
			ak_admin_error(__('Needed function to create default roles not found!', 'capsman-enhanced'));
			return;
		}

		$roles = array_keys( ak_get_roles(true) );

		foreach ( $roles as $role) {
			remove_role($role);
		}

		populate_roles();
		$this->cm->setAdminCapability();

		$msg = __('Roles and Capabilities reset to WordPress defaults', 'capsman-enhanced');
		
		if ( function_exists( 'pp_populate_roles' ) ) {
			pp_populate_roles();
		} else {
			// force PP to repopulate roles
			$pp_ver = get_option( 'pp_c_version', true );
			if ( $pp_ver && is_array($pp_ver) ) {
				$pp_ver['version'] = ( preg_match( "/dev|alpha|beta|rc/i", $pp_ver['version'] ) ) ? '0.1-beta' : 0.1;
			} else {
				$pp_ver = array( 'version' => '0.1', 'db_version' => '1.0' );
			}

			update_option( 'pp_c_version', $pp_ver );
			delete_option( 'ppperm_added_role_caps_10beta' );
		}
		
		ak_admin_notify($msg);
	}
}
