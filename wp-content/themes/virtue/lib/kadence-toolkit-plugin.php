<?php
/**
 * Add notice for toolkit.
 * Include the TGM_Plugin_Activation class.
 * Register the required plugins for this theme.
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Add Notice for toolkit if not installed
 */
function virtue_kadence_toolkit_notice() {
	if ( class_exists( 'virtue_toolkit_welcome' ) || get_transient( 'virtue_theme_toolkit_plugin_notice' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$installed_plugins = get_plugins();
	if ( ! isset( $installed_plugins['virtue-toolkit/virtue_toolkit.php'] ) ) {
		$button_label = esc_html__( 'Install Kadence Toolkit', 'virtue' );
		$data_action  = 'install';
	} elseif ( ! Virtue_Plugin_Check::active_check( 'virtue-toolkit/virtue_toolkit.php' ) ) {
		$button_label = esc_html__( 'Activate Kadence Toolkit', 'virtue' );
		$data_action  = 'activate';
	} else {
		return;
	}
	$install_link    = wp_nonce_url(
		add_query_arg(
			array(
				'action' => 'install-plugin',
				'plugin' => 'virtue-toolkit',
			),
			network_admin_url( 'update.php' )
		),
		'install-plugin_virtue-toolkit'
	);
	$activate_nonce  = wp_create_nonce( 'activate-plugin_virtue-toolkit/virtue_toolkit.php' );
	$activation_link = self_admin_url( 'plugins.php?_wpnonce=' . $activate_nonce . '&action=activate&plugin=virtue-toolkit%2Fvirtue_toolkit.php' );
	?>
	<div id="message" class="updated kt-plugin-install-notice-wrapper">
		<h3 class="kt-notice-title"><?php echo esc_html__( 'Thanks for choosing the Virtue Theme', 'virtue' ); ?></h3>
		<p class="kt-notice-description"><?php /* translators: %s: <strong> */ printf( esc_html__( 'To take full advantage of the Virtue Theme please install the %1$sKadence Toolkit%2$s, this adds extra settings and features.', 'virtue' ), '<strong>', '</strong>' ); ?></p>
		<p class="submit">
			<a class="button button-primary kt-install-toolkit-btn" data-redirect-url="<?php echo esc_url( admin_url( 'themes.php?page=kadence_welcome_page' ) ); ?>" data-activating-label="<?php echo esc_attr__( 'Activating...', 'virtue' ); ?>" data-activated-label="<?php echo esc_attr__( 'Activated', 'virtue' ); ?>" data-installing-label="<?php echo esc_attr__( 'Installing...', 'virtue' ); ?>" data-installed-label="<?php echo esc_attr__( 'Installed', 'virtue' ); ?>" data-action="<?php echo esc_attr( $data_action ); ?>" data-install-url="<?php echo esc_attr( $install_link ); ?>" data-activate-url="<?php echo esc_attr( $activation_link ); ?>"><?php echo esc_html( $button_label ); ?></a>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'virtue-kadence-toolkit-plugin-notice', 'install' ), 'virtue_toolkit_hide_notices_nonce', '_notice_nonce' ) ); ?>" class="notice-dismiss kt-close-notice"><span class="screen-reader-text"><?php esc_html_e( 'Skip', 'virtue' ); ?></span></a>
		</p>
	</div>
	<?php
	wp_enqueue_script( 'kadence-toolkit-install' );
}
add_action( 'admin_notices', 'virtue_kadence_toolkit_notice' );

/**
 * Hide Notice
 */
function virtue_hide_toolkit_plugin_notice() {
	if ( isset( $_GET['virtue-kadence-toolkit-plugin-notice'] ) && isset( $_GET['_notice_nonce'] ) ) {
		if ( ! wp_verify_nonce( wp_unslash( sanitize_key( $_GET['_notice_nonce'] ) ), 'virtue_toolkit_hide_notices_nonce' ) ) {
			wp_die( esc_html__( 'Authorization failed. Please refresh the page and try again.', 'virtue' ) );
		}
		set_transient( 'virtue_theme_toolkit_plugin_notice', 1, 4 * YEAR_IN_SECONDS );
	}
}
add_action( 'wp_loaded', 'virtue_hide_toolkit_plugin_notice' );

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
/**
 * Register the required plugins for this theme.
 */
function virtue_register_required_plugins() {

	$plugins = array(
		array(
			'name'               => 'Kadence Toolkit',
			'slug'               => 'virtue-toolkit',
			'required'           => false,
			'version'            => '4.8',
			'force_activation'   => false,
			'force_deactivation' => false,
		),
	);

	$theme_text_domain = 'virtue';

	$config = array(
		'domain'       => 'virtue', // Text domain - likely want to be the same as your theme.
		'default_path' => '', // Default absolute path to pre-packaged plugins
		'menu'         => 'install-required-plugins', // Menu slug
		'has_notices'  => false, // Show admin notices or not
		'is_automatic' => false, // Automatically activate plugins after installation or not
		'message'      => '', // Message to output right before the plugins table.
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'virtue' ),
			'menu_title'                      => __( 'Install Plugins', 'virtue' ),
			/* translators: %s: plugin name */
			'installing'                      => __( 'Installing Plugin: %s', 'virtue' ), // %1$s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugins, they add extra features to the theme options and content controls: %1$s. enjoy:)', 'This theme recommends the following plugins, they add extra features to the theme options and content controls: %1$s. enjoy:)', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'virtue' ),
			/* translators: %1$s: plugin name */
			'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'virtue' ),
			'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'virtue' ),
			'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'virtue' ),
			'return'                          => __( 'Return to Required Plugins Installer', 'virtue' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'virtue' ),
			/* translators: %1$s: dashboard link */
			'complete'                        => __( 'All plugins installed and activated successfully. %s', 'virtue' ),
			'nag_type'                        => 'updated',
		),
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'virtue_register_required_plugins' );
