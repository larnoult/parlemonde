<?php
/**
 * Plugin Name: Force Admin Color Scheme
 * Version:     1.1.1
 * Plugin URI:  http://coffee2code.com/wp-plugins/force-admin-color-scheme/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: force-admin-color-scheme
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Force a single admin color scheme for all users of the site.
 *
 * Compatible with WordPress 4.1 through 4.9+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/force-admin-color-scheme/
 *
 * @package Force_Admin_Color_Scheme
 * @author  Scott Reilly
 * @version 1.1.1
 */

/*
 * TODO:
 * - Support use of a constant that configures the admin color scheme,
 *   in doing so disables the profile checkbox added by this plugin.
 *   For admin users, provide a notice somewhere (in lieu of the color scheme
 *   picker?) that indicates the constant is in use for force a given scheme.
 * - Validate the color scheme that is configured (in the event a color scheme
 *   is no longer available, ignore it and show notice to admin user).
 * - Record and report (to other admins) the name (and possibly datetime) of
 *   the user who forced the admin color scheme.
 */

/*
	Copyright (c) 2014-2018 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_ForceAdminColorScheme' ) ) :

class c2c_ForceAdminColorScheme {

	/**
	 * Name of plugin's setting.
	 *
	 * @access private
	 * @var string
	 */
	private static $setting = 'c2c_forced_admin_color';

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.0
	 */
	public static function version() {
		return '1.1.1';
	}

	/**
	 * Hooks actions and filters.
	 *
	 * @since 1.0
	 */
	public static function init() {
		if ( ! is_admin() ) {
			return;
		}

		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		add_action( 'admin_init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * @since 1.1
	 */
	public static function uninstall() {
		delete_option( self::get_setting_name() );
	}

	/**
	 * Performs initializations on the 'init' action.
	 *
	 * @since 1.0
	 */
	public static function do_init() {
		// Load textdomain.
		load_plugin_textdomain( 'force-admin-color-scheme' );

		/*
		 * Register hooks
		 */

		// Override the user's admin color scheme.
		add_filter( 'get_user_option_admin_color', array( __CLASS__, 'force_admin_color'           ) );

		// Add checked for setting the forced admin color scheme.
		add_action( 'admin_color_scheme_picker',   array( __CLASS__, 'add_checkbox'                ), 20 );

		// Save the checkbox value for forcing admin color scheme.
		add_action( 'personal_options_update',     array( __CLASS__, 'save_setting'                ) );

		// Hide the Admin Color Scheme field from users who can't set a forced color scheme.
		add_action( 'admin_color_scheme_picker',   array( __CLASS__, 'hide_admin_color_input'      ), 8 );

		// Output CSS.
		add_action( 'load-profile.php',            array( __CLASS__, 'register_css'                ) );

		/*
		 * Note: not bothering with preventing users from being able to save a value for admin_color
		 * since it's not really a big deal if they do.
		 */
	}

	/**
	 * Returns the name of the setting that stores the forced admin color scheme.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public static function get_setting_name() {
		return self::$setting;
	}

	/**
	 * Returns the forced admin color scheme.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public static function get_forced_admin_color() {
		return get_option( self::get_setting_name() );
	}

	/**
	 * Sets the forced admin color scheme.
	 *
	 * NOTE: Does not currently verify if the specified color scheme is valid.
	 * NOTE: Does not perform any capability checks.
	 *
	 * @since 1.1
	 *
	 * @param  string $color_scheme The color scheme name.
	 * @return string The color scheme.
	 */
	public static function set_forced_admin_color( $color_scheme ) {
		if ( ! $color_scheme ) {
			delete_option( self::get_setting_name() );
		} else {
			update_option( self::get_setting_name(), $color_scheme );
		}

		return $color_scheme;
	}

	/**
	 * Overrides the user's admin color scheme with the forced admin color
	 * scheme, if set.
	 *
	 * @since 1.0
	 *
	 * @param  string $admin_color_scheme The admin color scheme.
	 * @return string
	 */
	public static function force_admin_color( $admin_color_scheme ) {
		// If a forced admin color has been configured, use it.
		if ( $forced = self::get_forced_admin_color() ) {
			$admin_color_scheme = $forced;
		}

		return $admin_color_scheme;
	}

	/**
	 * Outputs the checkbox for forcing the admin color scheme.
	 *
	 * @since 1.0
	 */
	public static function add_checkbox() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$forced_admin_color = self::get_forced_admin_color();
		$setting = self::get_setting_name();

		printf(
			'<label for="%s"><input name="%s" type="checkbox" id="%s" value="true" %s/> %s %s</label>',
			esc_attr( $setting ),
			esc_attr( $setting ),
			esc_attr( $setting ),
			checked( ! empty( $forced_admin_color ), true, false ),
			__( 'Force this admin color scheme on all users?', 'force-admin-color-scheme' ),
			(
				( $c = self::get_forced_admin_color() ) ?
					'<em>' . sprintf( __( 'Currently forced admin color: %s', 'force-admin-color-scheme' ), '<strong>' . ucfirst( $c ) . '</strong>' ) . '</em>' :
					''
			)
		);
	}

	/**
	 * Saves the admin user's admin color scheme as the forced admin color
	 * scheme if the checkbox is checked.
	 *
	 * @since 1.0
	 *
	 * @param  $user_id The user ID.
	 */
	public static function save_setting( $user_id ) {
		if ( current_user_can( 'manage_options' ) ) {
			// Unset the forced admin color if the checkbox is unchecked or no color was
			// specified.
			$new_color = empty( $_POST[ self::get_setting_name() ] ) || empty( $_POST['admin_color'] ) ?
				'' :
				$_POST['admin_color'];
			self::set_forced_admin_color( $new_color );
		}
	}

	/**
	 * Hides the Admin Color Scheme input and label when appropriate.
	 *
	 * The input is hidden for users who do not have the capability to set the
	 * forced admin color scheme *and* when an admin color scheme hasn't been
	 * set yet (so that user's can still choose until a forced admin color
	 * scheme is chosen).
	 *
	 * @since 1.1
	 */
	public static function hide_admin_color_input() {
		if ( ! current_user_can( 'manage_options' ) && self::get_forced_admin_color() ) {
			remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
		}
	}

	/**
	 * Registers hook for outputting CSS on the profile page.
	 *
	 * @since 1.0
	 */
	public static function register_css() {
		add_action( 'admin_head', array( __CLASS__, 'output_css' ) );
	}

	/**
	 * Outputs the CSS for hiding the label associated with the admin color picker.
	 *
	 * @since 1.0
	 */
	public static function output_css() {
		$css = '';

		// Admins need CSS to align checkbox with admin color picker.
		if ( current_user_can( 'manage_options' ) ) {
			$css = 'label[for="' . esc_attr( self::get_setting_name() ) . '"] { display: block; padding-left: 15px; margin-top: 10px; }';
		}
		// Non-admins need CSS to hide admin color label if a color is being forced.
		elseif ( self::get_forced_admin_color() ) {
			$css = '.user-admin-color-wrap { display: none; }';
		}

		if ( $css ) {
			echo "<style>{$css}</style>\n";
		}
	}

} // end c2c_ForceAdminColorScheme

c2c_ForceAdminColorScheme::init();

endif; // end if !class_exists()
