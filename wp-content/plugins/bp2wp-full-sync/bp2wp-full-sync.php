<?php

/*
Plugin Name: BuddyPress to WordPress Full Sync
Plugin URI: http://wordpress.org/plugins/bp2wp-full-sync/
Description: BuddyPress to WordPress Full Sync lets BuddyPress users to synchronize various users fields with WordPress user fields and vice versa.
Author: Envire Web Solutions
Version: 0.3.3
Author URI: https://www.envire.it/
Text Domain: bp2wp-full-sync
Domain Path: /languages/
License: GPL v3
*/

register_activation_hook(__FILE__			, array('BP2WP_Full_Sync_Loader', 'install_plugin'));		// Registering plugin activation hook.
register_deactivation_hook( __FILE__		, array('BP2WP_Full_Sync_Loader', 'uninstall_plugin'));		// Registering plugin deactivation hook.

/**
 * Load BP2WP Full Sync Plugin
 *
 * @since 0.1
 */
class BP2WP_Full_Sync_Loader {
	/**
	 * Uniquely identify plugin version
	 * Bust caches based on this value
	 *
	 * @since 0.1
	 * @var string
	 */
	const VERSION = '0.3';
	
	/**
	 * List of WordPress User Fields that can be synchronized with BuddyPress.
	 *
	 * @since 0.1
	 *
	 * @link http://codex.wordpress.org/Function_Reference/wp_update_user WordPress Update User Fields
	 * @var array {
	 *     Supported WordPress User Fields.
	 *
	 *     @type string WordPress User Field
	 *     @type string WordPress User Field associated Filter
	 * }
	 */
	public static $wpfields = array(
		'first_name'	=> 'pre_user_first_name',
		'last_name'		=> 'pre_user_last_name',
		'description'	=> 'pre_user_description',
		'user_url'		=> 'pre_user_url',
		'locale'		=> 'pre_user_locale'
	);

	/**
	 * Whether values from this field are already synced.
	 *
	 * @since 0.2
	 * @var bool
	 */
	public $synced = false;
	
	/**
	 * Store the current user.
	 *
	 * @since 0.2
	 * @var bool
	 */
	public $bp_user_id = 0;
		
	/**
	 * Let's get it started
	 *
	 * @since 0.1
	 */
	public function __construct() {
		// load plugin files relative to this directory
		$this->plugin_directory = dirname(__FILE__) . '/';
		
		// load plugin files relative to this url
		$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );

		// Load the textdomain for translations
		load_plugin_textdomain( 'bp2wp-full-sync', true, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		if ( is_admin() ) {
			$this->admin_init();
		} else {
			add_action( 'wp', array( &$this, 'public_init' ) );
		}
	}

	/**
	 * Handles actions for the plugin activation
	 *
	 * @since 0.1
	 */
	static function install_plugin() {
	}
	
	/**
	 * Handles actions for the plugin deactivation
	 *
	 * @since 0.1
	 */
	static function uninstall_plugin() {
	}
	
	/**
	 * Intialize the public, front end views
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function public_init() {
		// Since here must be a BuddyPress page
		if ( ! is_buddypress() ) 
			return;

		// Add required Filters & Actions
		add_action( 'user_register', array( &$this, 'bp2wp_user_register' ), 10, 1 );
		add_filter( 'bp_xprofile_set_field_data_pre_validate', array( &$this, 'bp2wp_update_sync_field' ), 1, 3 );
	}

	/**
	 * Initialize the backend
	 *
	 * @since 0.1
	 */
	public function admin_init() {
		// Add required hooks
		add_filter( 'updated_user_meta', array( &$this, 'bp2wp_update_sync_user_metadata' ), 10, 4 );
		add_action( 'xprofile_updated_profile', array( &$this, 'bp2wp_updated_profile_sync' ), 10, 5 );
		add_action( 'xprofile_field_after_submitbox', array( &$this, 'bp2wp_add_sync_options' ) );
		add_action( 'xprofile_field_after_save', array( &$this, 'bp2wp_save_sync_options' ) );
	}

	/**
	 * Intercept the user_register action, storing the new user id WordPress field.
	 *
	 * @since BP2WP Full Sync 0.1
	 */	
	public function bp2wp_user_register( $user_id ) {
		$this->bp_user_id = $user_id;
	}

	/**
	 * Intercept the update_user_metadata action, syncing the WordPress user metadata with the associated BuddyPress field (WP2BP).
	 *
	 * @since BP2WP Full Sync 0.2
	 */	
	public function bp2wp_update_sync_user_metadata( $meta_id, $object_id, $meta_key, $_meta_value ) {
		// Return if last_update
		if ( $meta_key == 'last_update' )
			return;

		// Check if already synced
		if ( $this->synced )
			return;
		
		$parent_profile_id = intval( self::bp2wp_get_parent_fields( $meta_key, 'id' ) );
		xprofile_set_field_data( $parent_profile_id, $object_id, $_meta_value );
	}

	/**
	 * Filter the raw submitted profile field value, checking if there is an associate WordPress Field.
	 *
	 * @since BP2WP Full Sync 0.1
	 *
	 * @return mixed
	 */	
	public function bp2wp_update_sync_field( $value, $field, $field_type_obj ) {
		if ( $field ) {

			$childs = $field->get_children();
			if (isset($childs) && $childs && count($childs) > 0 
				&& is_object($childs[0]) && $childs[0]->type == 'bp2wp_sync') {

				if ( null !== bp_displayed_user_id() && bp_displayed_user_id() > 0 )
					$this->bp_user_id = bp_displayed_user_id();
				
				$this->synced = true;
				update_user_meta( $this->bp_user_id, $childs[0]->name, $value );
			}
		}
		return $value;
	}

	/**
	 * Filter the BuddyPress profile modified fields from the backend syncing them with WordPress.
	 *
	 * @since BP2WP Full Sync 0.2
	 *
	 * @return mixed
	 */	
	public function bp2wp_updated_profile_sync( $user_id, $posted_field_ids, $errors, $old_values, $new_values ) {
		foreach ( $posted_field_ids as $field_id ) {
			if ( $new_values[ $field_id ]['value'] !=  $old_values[ $field_id ]['value'] ) {
				$field = xprofile_get_field( $field_id );
				$childs = $field->get_children();
				if (isset($childs) && $childs && count($childs) > 0 
					&& is_object($childs[0]) && $childs[0]->type == 'bp2wp_sync') {

					$this->synced = true;
					//wp_update_user( array( 'ID' => $this->bp_user_id, $childs[0]->name => $new_values[ $field_id ]['value'] ) );
					update_user_meta( $user_id, $childs[0]->name, $new_values[ $field_id ]['value'] );
				}
			}
		}
	}
	
	public function bp2wp_add_sync_options( $field ) {
		$bp2wp_sync = false;
		if ($field) {
			$childs = $field->get_children();
			if (isset($childs) && $childs && count($childs) > 0 
				&& is_object($childs[0]) && $childs[0]->type == 'bp2wp_sync') {
				$bp2wp_sync = $childs[0]->name;
			}
		}
		?>

		<div class="postbox">
			<h2><?php _e( 'Sync to WordPress user field', 'bp2wp-full-sync' ); ?></h2>
			<div class="inside">
				<p class="description">
					<?php _e( 'Please select the WordPress user meta you want to sync with this BuddyPress field.', 'bp2wp-full-sync' ); ?>
				</p>
				<p>
					<label class="screen-reader-text" for="bp2wp_sync"><?php _e( 'Sync to WordPress user field', 'bp2wp-full-sync' ); ?></label>
					<select name="bp2wp_sync" id="bp2wp_sync" style="width: 90%">
						<option value="" <?php selected( $bp2wp_sync, '' ); ?>><?php _e( 'None', 'bp2wp-full-sync' ); ?></option>
						<?php foreach( self::$wpfields as $wpfield => $filter ) { ?>

							<option value="<?php echo $wpfield; ?>"<?php selected( $bp2wp_sync, $wpfield ); ?>><?php echo $wpfield; ?></option>
						
						<?php } ?>
					</select>
				</p>
			</div>
		</div>
			
		<?php
	}
	
	public function bp2wp_save_sync_options( $field ) {
		global $bp, $wpdb;
		
		$bp2wp_sync_option = !empty( $_POST['bp2wp_sync']) ? $_POST['bp2wp_sync'] : '';

		if ( $bp2wp_sync_option ) {
			if ( !empty( $field->id ) ) {
				$field_id = $field->id;
			} else {
				$field_id = $wpdb->insert_id;
			}
			if ( !$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->profile->table_name_fields} (group_id, parent_id, type, name, description, is_required, option_order, is_default_option) VALUES (%d, %d, 'bp2wp_sync', %s, '', 0, %d, %d)", $field->group_id, $field_id, $bp2wp_sync_option, 1, 0 ) ) ) {
				return false;
			}
		}
	}

	/**
	 * Get BuddyPress corresponding field id.
	 *
	 * @since 0.2
	 *
	 * @global object $wpdb
	 *
	 * @param string $meta_key The WordPress usermeta key.
	 * @return array
	 */
	public function bp2wp_get_parent_fields( $meta_key, $id_or_object = 'object' ) {
		global $wpdb;

		$bp  = buddypress();
		$sql = $wpdb->prepare( "SELECT parent.* FROM {$bp->profile->table_name_fields} AS parent JOIN {$bp->profile->table_name_fields} AS child ON parent.id = child.parent_id WHERE child.type = 'bp2wp_sync' AND child.name = %s", $meta_key );

		$parent_fields = $wpdb->get_results( $sql );
		if ( count( $parent_fields ) === 0 )
			return false;
		
		if ( $id_or_object == 'id' )
			return $parent_fields[0]->id;
		
		return $parent_fields[0];
	}
}

/**
 * Load plugin function during the WordPress init action
 *
 * @since 0.1
 */
function BP2WP_Full_Sync_Loader_init() {
	global $BP2WP_Full_Sync_loader;

	$BP2WP_Full_Sync_loader = new BP2WP_Full_Sync_Loader();
}
add_action( 'init', 'BP2WP_Full_Sync_Loader_init', 0 ); // load before widgets_init at 1