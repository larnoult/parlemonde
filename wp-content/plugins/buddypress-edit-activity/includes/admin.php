<?php
/**
 * @package WordPress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'BuddyBoss_Edit_Activity_Admin' ) ):
	
/**
 *
 * BuddyBoss Edit Activity Admin
 * ********************
 *
 *
 */
class BuddyBoss_Edit_Activity_Admin{
	/**
	 * Plugin options
	 *
	 * @var array
	 */
	public $options = array();
	
	private $network_activated = false,
			$plugin_slug = 'buddyboss-editactivity',
			$menu_hook = 'admin_menu',
			$settings_page = 'options-general.php',
			$capability = 'manage_options',
			$form_action = 'options.php',
			$plugin_settings_url;
	
	/**
	 * Empty constructor function to ensure a single instance
	 */
	public function __construct(){
		// ... leave empty, see Singleton below
	}


	/* Singleton
	 * ===================================================================
	 */

	/**
	 * Admin singleton
	 *
	 * @param  array  $options [description]
	 *
	 * @return object Admin class
	 */
	public static function instance(){
		static $instance = null;

		if ( null === $instance )
		{
			$instance = new BuddyBoss_Edit_Activity_Admin();
			$instance->setup();
		}

		return $instance;
	}
	
	/**
	 * Get option
	 *
	 * @param  string $key Option key
	 *
	 * @return mixed      Option value
	 */
	public function option( $key ){
		$value = buddyboss_edit_activity()->option( $key );
		return $value;
	}
	
	/**
	 * Setup admin class
	 */
	public function setup(){
		if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ){
			return;
		}
		
		$this->plugin_settings_url = admin_url('options-general.php?page=' . $this->plugin_slug);

		$this->network_activated = $this->is_network_activated();

		//if the plugin is activated network wide in multisite, we need to override few variables
		if ( $this->network_activated ) {
			// Main settings page - menu hook
			$this->menu_hook = 'network_admin_menu';

			// Main settings page - parent page
			$this->settings_page = 'settings.php';

			// Main settings page - Capability
			$this->capability = 'manage_network_options';

			// Settins page - form's action attribute
			$this->form_action = 'edit.php?action=' . $this->plugin_slug;

			// Plugin settings page url
			$this->plugin_settings_url = network_admin_url('settings.php?page=' . $this->plugin_slug);
		}

		//if the plugin is activated network wide in multisite, we need to process settings form submit ourselves
		if ( $this->network_activated ) {
			add_action('network_admin_edit_' . $this->plugin_slug, array( $this, 'save_network_settings_page' ));
		}

		add_action('admin_init', array( $this, 'admin_init' ));
		add_action($this->menu_hook, array( $this, 'admin_menu' ));

		add_filter('plugin_action_links', array( $this, 'add_action_links' ), 10, 2);
		add_filter('network_admin_plugin_action_links', array( $this, 'add_action_links' ), 10, 2);
	}
	
	/**
	 * Check if the plugin is activated network wide(in multisite).
	 * 
	 * @return boolean
	 */
	private function is_network_activated() {
	   $network_activated = false;
	   if ( is_multisite() ) {
		   if ( !function_exists('is_plugin_active_for_network') )
			   require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		   if ( is_plugin_active_for_network('buddypress-edit-activity/buddypress-edit-activity.php') ) {
			   $network_activated = true;
		   }
	   }
	   return $network_activated;
	}
	
	/**
	 * Register admin settings
	 */
	public function admin_init(){
		register_setting( 'b_e_a_plugin_options', 'b_e_a_plugin_options', array( $this, 'plugin_options_validate' ) );
		add_settings_section( 'general_section', __( 'Front-end Editing Settings', 'buddypress-edit-activity' ), array( $this, 'section_general' ), __FILE__ );

		add_settings_field( 'user_access', __( 'Who can edit activity', 'buddypress-edit-activity' ), array( $this, 'setting_user_access' ), __FILE__, 'general_section');
		add_settings_field( 'editable_types', __( 'Editable on front-end', 'buddypress-edit-activity' ), array( $this, 'setting_editable_types' ), __FILE__, 'general_section');
		add_settings_field( 'editable_timeout', __( 'Disallow editing after', 'buddypress-edit-activity' ), array( $this, 'setting_editable_timeout' ), __FILE__, 'general_section');
		add_settings_field( 'exclude_admins', '', array( $this, 'setting_exclude_admins' ), __FILE__, 'general_section');
	}
	
	/**
	 * General settings section
	 */
	public function section_general(){

	}
	
	/**
	 * Add plugin settings page
	 */
	public function admin_menu(){
		//add_options_page( 'BP Edit Activity', 'BP Edit Activity', 'manage_options', __FILE__, array( $this, 'options_page' ) );
		add_submenu_page(
			$this->settings_page, 'BP Edit Activity', 'BP Edit Activity', $this->capability, $this->plugin_slug, array( $this, 'options_page' )
		);
	}
	
	/**
	 * Render settings page
	 */
	public function options_page(){
		?>
		<div class="wrap">
			<h2><?php _e( 'Buddypress Edit Activity' , 'buddypress-edit-activity' ) ; ?></h2>
			<div class="updated fade">
				<p><?php _e( 'Need BuddyPress customizations?', 'buddypress-edit-activity' ); ?>  &nbsp;<a href="http://buddyboss.com/buddypress-developers/" target="_blank"><?php _e( 'Say hello.', 'buddypress-edit-activity' ); ?></a></p>
			</div>
			<form method="post" action="<?php echo $this->form_action; ?>">

				<?php
				if ( $this->network_activated && isset($_GET['updated']) ) {
					echo "<div class='updated'><p>" . __('Settings updated.', 'buddypress-edit-activity') . "</p></div>";
				}
				?>
				
				<?php settings_fields( 'b_e_a_plugin_options' ); ?>
				<?php do_settings_sections( __FILE__ ); ?>

				<p class="submit">
					<input name="bboss_e_a_settings_submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'buddypress-edit-activity' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Validate plugin option
	 */
	public function plugin_options_validate( $input ){
		$editable_timeout = (int)sanitize_text_field( $input['editable_timeout'] );
		$input['editable_timeout'] = $editable_timeout;
		
		if( !isset( $input['exclude_admins'] ) || !$input['exclude_admins'] )
			$input['exclude_admins'] = 'no';

		return $input; // return validated input
	}
	
	/**
	 * Setting > user_access
	 */
	public function setting_user_access(){
		$user_access = $this->option( 'user_access' );
		if( !$user_access ){
			$user_access = 'author';
		}
		
		$options = array(
			'admin'		=> __( 'Admin only', 'buddypress-edit-activity' ),
			'author'	=> __( 'Admin and user who created the post', 'buddypress-edit-activity' )
		);
		foreach( $options as $option=>$label ){
			$checked = $user_access == $option ? ' checked' : '';
			echo '<label><input type="radio" name="b_e_a_plugin_options[user_access]" value="'. $option . '" '. $checked . '>' . $label . '</label>&nbsp;&nbsp;';
		}
	}
	
	/**
	 * Setting > editable_types
	 */
	public function setting_editable_types(){
		$editable_types = $this->option( 'editable_types' );
		if( !$editable_types ){
			$editable_types = array( 'activity_update' );
		}
		
		$options = array(
			'activity_update'	=> __( 'Activity Posts', 'buddypress-edit-activity' ),
			'activity_comment'	=> __( 'Activity Replies', 'buddypress-edit-activity' )
		);
		foreach( $options as $option=>$label ){
			$checked = in_array( $option, $editable_types ) ? ' checked' : '';
			echo '<label><input type="checkbox" name="b_e_a_plugin_options[editable_types][]" value="'. $option . '" '. $checked . '>' . $label . '</label>&nbsp;&nbsp;';
		}
	}
	
	/**
	 * Setting > editable_timeout
	 */
	public function setting_editable_timeout(){
		$editable_timeout = $this->option( 'editable_timeout' );

		echo "<input id='editable_timeout' name='b_e_a_plugin_options[editable_timeout]' type='text' class='small-text' value='" . esc_attr( $editable_timeout ) . "' />";
		echo '<label for="b_e_a_plugin_options[editable_timeout]">' . __( ' minutes', 'buddypress-edit-activity' ) . '</label>';
		echo '<p class="description">' . __( 'Leave at 0 to set no time limit ', 'buddypress-edit-activity' ) . '</p>';
	}
	
	/**
	 * Setting > exclude_admins
	 */
	public function setting_exclude_admins(){
		$exclude_admins = $this->option( 'exclude_admins' );
		$checked = $exclude_admins=='yes' ? ' checked' : '';
		echo '<label><input type="checkbox" name="b_e_a_plugin_options[exclude_admins]" value="yes" '. $checked . '>' . __( 'Exclude admins from time limit.', 'buddypress-edit-activity' ) . '</label>';
	}
	
	public function add_action_links( $links, $file ) {
		// Return normal links if not this plugin
		if ( plugin_basename(basename(constant('BUDDYBOSS_EDIT_ACTIVITY_PLUGIN_DIR')) . '/buddypress-edit-activity.php') != $file ) {
			return $links;
		}

		$mylinks = array(
			'<a href="' . esc_url($this->plugin_settings_url) . '">' . __("Settings", "buddypress-edit-activity") . '</a>',
		);
		return array_merge($links, $mylinks);
	}
	
	public function save_network_settings_page() {
		if ( !check_admin_referer('b_e_a_plugin_options-options') )
			return;

		if ( !current_user_can($this->capability) )
			die('Access denied!');

		if ( isset($_POST['bboss_e_a_settings_submit']) ) {
			$submitted = stripslashes_deep($_POST['b_e_a_plugin_options']);
			$submitted = $this->plugin_options_validate($submitted);

			update_site_option('b_e_a_plugin_options', $submitted);
		}

		// Where are we redirecting to?
		$base_url = trailingslashit(network_admin_url()) . 'settings.php';
		$redirect_url = esc_url_raw(add_query_arg(array( 'page' => $this->plugin_slug, 'updated' => 'true' ), $base_url));

		// Redirect
		wp_redirect($redirect_url);
		die();
	}
}

endif;