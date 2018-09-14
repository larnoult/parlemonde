<?php

/**
 * bbPress Digest Admin Functions
 *
 * @package bbPress Digest
 * @subpackage Admin Functions
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Map bbPress Digest settings capability.
 *
 * @since 2.1
 *
 * @see BBP_Admin::map_settings_meta_caps()
 *
 * @param array $caps Capabilities for meta capability
 * @param string $cap Capability name
 * @param int $user_id User id
 * @param mixed $args Arguments
 * @return array Actual capabilities for meta capability
 */
function bbp_digest_map_settings_cap( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	if ( 'bbp_settings_digest' == $cap )
		$caps = array( bbpress()->admin->minimum_capability );

	return $caps;
}
add_filter( 'bbp_map_meta_caps', 'bbp_digest_map_settings_cap', 10, 4 );

/**
 * Add bbPress Digest settings section.
 *
 * @since 2.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 *
 * @param array $sections existing sections
 * @return array $sections new sections
 */
function bbp_digest_add_settings_section( $sections ) {
	/* Load translations */
	bbp_digest_load_textdomain();
	/* Append section to existing ones */
	$sections['bbp_settings_digest'] = array(
		'title'    => _x( 'bbPress Digest Settings', 'settings section title', 'bbp-digest' ),
		'callback' => 'bbp_digest_admin_setting_callback_section',
		'page'     => 'bbpress',
	);

	return $sections;
}
add_filter( 'bbp_admin_get_settings_sections', 'bbp_digest_add_settings_section' );

/**
 * Add bbPress Digest settings fields.
 *
 * @since 2.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 *
 * @param array $fields existing fields
 * @return array $fields new fields
 */
function bbp_digest_add_settings_fields( $fields ) {
	/* Load translations */
	bbp_digest_load_textdomain();
	/* Append fields to existing ones */
	$fields['bbp_settings_digest'] = array(
		/* One-click subscription setting */
		'_bbp_digest_show_one_click' => array(
			'title'             => __( 'Show one-click subscription', 'bbp-digest' ),
			'callback'          => 'bbp_digest_admin_setting_callback_one_click',
			'sanitize_callback' => 'intval',
			'args'              => array()
		),
		/* Weekly digest setting */
		'_bbp_digest_enable_weekly' => array(
			'title'             => __( 'Show weekly digest option', 'bbp-digest' ),
			'callback'          => 'bbp_digest_admin_setting_callback_weekly',
			'sanitize_callback' => 'intval',
			'args'              => array()
		),
	);

	return $fields;
}
add_filter( 'bbp_admin_get_settings_fields', 'bbp_digest_add_settings_fields' );

/**
 * bbPress Digest settings section description for the settings page
 *
 * @since 2.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 */
function bbp_digest_admin_setting_callback_section() {
	/* Load translations */
	bbp_digest_load_textdomain();
	?>
	<p><?php _e( 'bbPress Digest settings for enabling features', 'bbp-digest' ); ?></p>
	<?php
}

/**
 * One-click subscription setting field
 *
 * @since 2.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses checked() To display the checked attribute
 * @uses bbp_digest_is_it_active() To check if feature is enabled
 */
function bbp_digest_admin_setting_callback_one_click() {
	/* Load translations */
	bbp_digest_load_textdomain();
	?>
	<input id="_bbp_digest_show_one_click" name="_bbp_digest_show_one_click" type="checkbox" id="_bbp_digest_show_one_click" value="1" <?php checked( bbp_digest_is_it_active( '_bbp_digest_show_one_click' ) ); ?> />
	<label for="_bbp_digest_show_one_click"><?php _e( 'Allow users to include forum in a digest from a single forum page', 'bbp-digest' ); ?></label>
	<?php
}

/**
 * Weekly digest setting field
 *
 * @since 2.0
 *
 * @uses bbp_digest_load_textdomain() To load translation
 * @uses checked() To display the checked attribute
 * @uses bbp_digest_is_it_active() To check if feature is enabled
 */
function bbp_digest_admin_setting_callback_weekly() {
	/* Load translations */
	bbp_digest_load_textdomain();
	?>
	<input id="_bbp_digest_enable_weekly" name="_bbp_digest_enable_weekly" type="checkbox" id="_bbp_digest_enable_weekly" value="1" <?php checked( bbp_digest_is_it_active( '_bbp_digest_enable_weekly' ) ); ?> />
	<label for="_bbp_digest_enable_weekly"><?php _e( 'Allow users to chose do they want to receive digest once weekly instead of once daily', 'bbp-digest' ); ?></label>
	<?php
}