<?php

/**
 * The bbPress Digest Plugin
 *
 * Send digests with forum's active topics.
 *
 * @package bbPress Digest
 * @subpackage Main
 */

/**
 * Plugin Name: bbPress Digest
 * Plugin URI:  http://blog.milandinic.com/wordpress/plugins/bbpress-digest/
 * Description: Send digests with forum's active topics.
 * Author:      Milan Dinić
 * Author URI:  http://blog.milandinic.com/
 * Version:     2.1
 * Text Domain: bbp-digest
 * Domain Path: /languages/
 * License:     GPL
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Schedule bbPress Digest event on activation
 *
 * @since 1.0
 *
 * @uses current_time() To get current UNIX time
 * @uses wp_clear_scheduled_hook() To remove scheduled event
 * @uses wp_schedule_event() To schedule event
 */
function bbp_digest_activation() {
	/* Get timestamp of the next full hour */
	$current_time = current_time( 'timestamp' );
	$timestamp = $current_time + ( 3600 - ( ( date( 'i', $current_time ) * 60 ) + date( 's', $current_time ) ) ); // Add passed seconds from full hour to the current time

	/* Clear the old recurring event and set up a new one */
	wp_clear_scheduled_hook( 'bbp_digest_event' );
	wp_schedule_event( $timestamp, 'hourly', 'bbp_digest_event' );
}
register_activation_hook( __FILE__, 'bbp_digest_activation' );

/**
 * Unschedule bbPress Digest event on deactivation
 *
 * @since 1.0
 *
 * @uses wp_next_scheduled() To get time of next event
 * @uses wp_unschedule_event() To unschedule event
 */
function bbp_digest_deactivation() {
	$timestamp = wp_next_scheduled( 'bbp_digest_event' );
	wp_unschedule_event( $timestamp, 'bbp_digest_event' );
}
register_deactivation_hook( __FILE__, 'bbp_digest_deactivation' );

/**
 * Remove options on uninstallation of plugin
 *
 * Based on delete_post_meta_by_key()
 *
 * @since 1.0
 * @deprecated 2.1 Use unistall.php instead.
 *
 * @uses delete_metadata() To delete all users meta data
 * @uses delete_option() To delete all site settings
 */
function bbp_digest_uninstall() {
	_deprecated_function( __FUNCTION__, '2.1' );
	include_once( dirname( __FILE__ ) . '/unistall.php' );
}

/**
 * Register actions on init hook
 *
 * @since 2.0
 *
 * @uses is_user_logged_in() To check if current visitor is logged in
 * @uses bbp_digest_is_it_active() To check if feature is enabled
 * @uses add_action() Hooks one-click templates & AJAX handler
 * @uses is_admin() To check if it's admin page
 */
function bbp_digest_init() {
	/* One-click subscription */
	if ( is_user_logged_in() && bbp_digest_is_it_active( '_bbp_digest_show_one_click' ) ) {
		/* Show one-click subscription */
		add_action( 'bbp_template_after_topics_loop'       , 'bbp_digest_one_click_subscription'   );

		/* Handle one-click noscript subscription */
		add_action( 'bbp_get_request_bbp_digest_add_sub'   , 'bbp_digest_one_click_ajax_handle', 1 );
		add_action( 'bbp_get_request_bbp_digest_remove_sub', 'bbp_digest_one_click_ajax_handle', 1 );

		/* Handle one-click AJAX subscription */
		add_action( 'bbp_ajax_bbp_digest_add_sub'          , 'bbp_digest_one_click_ajax_handle'    );
		add_action( 'bbp_ajax_bbp_digest_remove_sub'       , 'bbp_digest_one_click_ajax_handle'    );
	}

	/* On admin, load admin functions */
	if ( is_admin() ) {
		/* Load file */
		require_once( dirname( __FILE__ ) . '/inc/admin.php' );
	}
}
add_action( 'init', 'bbp_digest_init' );

/**
 * Load textdomain for internationalization
 *
 * @since 1.0
 *
 * @uses is_textdomain_loaded() To check if translation is loaded
 * @uses load_plugin_textdomain() To load translation file
 * @uses plugin_basename() To get plugin's file name
 */
function bbp_digest_load_textdomain() {
	/* If translation isn't loaded, load it */
	if ( ! is_textdomain_loaded( 'bbp-digest' ) )
		load_plugin_textdomain( 'bbp-digest', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Add action links to plugins page
 *
 * Thanks to Dion Hulse for guide
 * and Adminize plugin for implementation
 *
 * @link http://dd32.id.au/wordpress-plugins/?configure-link
 * @link http://bueltge.de/wordpress-admin-theme-adminimize/674/
 *
 * @since 1.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses plugin_basename() To get plugin's file name
 *
 * @param array $links Default links of plugin
 * @param string $file Name of plugin's file
 * @return array $links New & old links of plugin
 */
function bbp_digest_filter_plugin_actions( $links, $file ) {
	/* Load translations */
	bbp_digest_load_textdomain();

	static $this_plugin;

	if ( ! $this_plugin )
		$this_plugin = plugin_basename( __FILE__ );

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . add_query_arg( array( 'page' => 'bbpress' ), admin_url( 'options-general.php' ) ) . '">' . _x( 'Settings', 'plugin actions link', 'bbp-digest' ) . '</a>';
		$donate_link = '<a href="http://blog.milandinic.com/donate/">' . __( 'Donate', 'bbp-digest' ) . '</a>';
		$links = array_merge( array( $donate_link, $settings_link ), $links ); // Before other links
	}

	return $links;
}
add_filter( 'plugin_action_links', 'bbp_digest_filter_plugin_actions', 10, 2 );

/**
 * Send digest emails on schedule
 *
 * @since 1.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses bbp_digest_do_event() To do process
 */
function bbp_digest_event() {
	/* Load translations */
	bbp_digest_load_textdomain();
	/* Load file with event function */
	require_once( dirname( __FILE__ ) . '/inc/event.php' );
	/* Do event */
	bbp_digest_do_event();
}
add_action( 'bbp_digest_event', 'bbp_digest_event' );

/**
 * Show settings on user profile page
 *
 * @since 1.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses bbp_digest_display_profile_fields To display fields
 *
 * @param object $user Viewed user's data
 */
function bbp_digest_profile_fields( $user ) {
	/* Don't show on bbPress user edit page, we have special function */
	if ( bbp_is_single_user_edit() )
		return;

	/* Load translations */
	bbp_digest_load_textdomain();
	/* Load file with forum list generator */
	require_once( dirname( __FILE__ ) . '/inc/forums-list.php' );
	/* Load file with settings form */
	require_once( dirname( __FILE__ ) . '/inc/wp-profile.php' );
	/* Display form */
	bbp_digest_display_profile_fields( $user );
}
add_action( 'show_user_profile', 'bbp_digest_profile_fields' );
add_action( 'edit_user_profile', 'bbp_digest_profile_fields' );

/**
 * Handle submission from users profile.
 *
 * @since 1.0
 *
 * @uses bbp_digest_do_save_profile_fields() To handle submission
 *
 * @param int $user_id ID of a user
 */
function bbp_digest_save_profile_fields( $user_id ) {
	/* Load file with function for saving */
	require_once( dirname( __FILE__ ) . '/inc/save-profile.php' );
	/* Do event */
	bbp_digest_do_save_profile_fields( $user_id );
}
add_action( 'personal_options_update', 'bbp_digest_save_profile_fields' );
add_action( 'edit_user_profile_update', 'bbp_digest_save_profile_fields' );

/**
 * Show settings on user's bbPress profile page
 *
 * @since 1.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses bbp_digest_display_bbp_profile_fields() To display fields
 */
function bbp_digest_bbp_profile_fields() {
	/* Load translations */
	bbp_digest_load_textdomain();
	/* Load file with forum list generator */
	require_once( dirname( __FILE__ ) . '/inc/forums-list.php' );
	/* Load file with settings form */
	require_once( dirname( __FILE__ ) . '/inc/bbp-profile.php' );
	/* Display form */
	bbp_digest_display_bbp_profile_fields();
}
add_action( 'bbp_user_edit_after', 'bbp_digest_bbp_profile_fields' );

/**
 * Show one-click subscription on a single forum
 *
 * @since 2.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses bbp_digest_display_one_click_subscription() To display link
 */
function bbp_digest_one_click_subscription() {
	/* Load translations */
	bbp_digest_load_textdomain();
	/* Load file with template function */
	require_once( dirname( __FILE__ ) . '/inc/one-click-template.php' );
	/* Display template */
	bbp_digest_display_one_click_subscription();
}

/**
 * Handle one-click subscription submission
 *
 * @since 2.0
 *
 * @uses bbp_digest_do_one_click_ajax_handle() To handle request
 */
function bbp_digest_one_click_ajax_handle() {
	/* Load file with function for saving */
	require_once( dirname( __FILE__ ) . '/inc/one-click-handle.php' );
	/* Do handling */
	bbp_digest_do_one_click_ajax_handle();
}

/**
 * Checks if feature is enabled.
 *
 * @since 2.0
 *
 * @uses get_option() To get the requested option
 *
 * @param string $option Name of the option
 * @return bool Is feature enabled or not
 */
function bbp_digest_is_it_active( $option ) {
	return (bool) get_option( $option );
}

/**
 * Show Javascript in a head of a page
 *
 * @since 2.0
 *
 * @uses admin_url To get URL of AJAX handler
 */
function bbp_digest_head_scripts() {
	?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		/* ]]> */
	</script>
	<?php
}