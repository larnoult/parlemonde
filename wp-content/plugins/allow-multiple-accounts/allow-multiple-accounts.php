<?php
/**
 * Plugin Name: Allow Multiple Accounts
 * Version:     3.0.4
 * Plugin URI:  http://coffee2code.com/wp-plugins/allow-multiple-accounts/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: allow-multiple-accounts
 * Domain Path: /lang/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Allow multiple user accounts to be created, registered, and updated having the same email address.
 *
 * Compatible with WordPress 3.6 through 4.2+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/allow-multiple-accounts/
 *
 * @package Allow_Multiple_Accounts
 * @author  Scott Reilly
 * @version 3.0.4
 */

/*
 * NOTE FROM THE DEVELOPER
 *
 * WordPress really wants to enforce the uniqueness of email addresses for user
 * accounts and doesn't facilitate making re-use of email addresses easy to
 * accomplish. Though fairly straightforward to do pre-WP3.0, such an effort was
 * seriously hampered by changes made in WP3.0 in the merging of user creation
 * code/handling from WPMU. This merge negated the ability to simply use filters
 * to suppress errors generated from re-using email addresses. As such, hacky
 * solutions must be pursued.
 *
 * Ways to do this:
 *
 * - Override get_user_by() to intercept requests for users by 'email'. In
 *   strategic instances, check the permissability of multiple accounts for the email
 *   address and if allowed, return false, making it look like no user exists with
 *   that email address. This function is the basis for email_exists(), so
 *   detecting when such email_exists() calls are happening (unfortunately due to
 *   hooking nearby-but-unrelated hooks) this can be done.
 *   Con: If another plugin overrides get_user_by(), then this won't work. Or, this
 *        plugin would work and the other plugin would be negatively impacted.
 *   This is how pre-v3.0 of the plugin functioned.
 *
 * - Strategically define WP_IMPORTING in instances when a user account is being
 *   created/registered/updated and an email address is being reused but the
 *   plugin deems it is permissible. WP_IMPORTING prevents email_exists() from
 *   being called.
 *   Con: Potential (though unlikely conflicts) with other plugins that may check
 *        for WP_IMPORTING (they'd have to do so on page load after this plugin
 *        set it). (There is no current conflict with core.)
 *   Con: Not unit testable. The constant would be set and can't be unset.
 *
 * - Strategically replace the email address for an account being
 *   created/registered/updated with a functional, unique version (e.g.
 *   user+ama0@example.com) if the email address being used is non-unique but
 *   permitted by the plugin. Immediately after the account is created and saved,
 *   go back and restore the original, non-unique email address.
 *   This is the approach taken in v3.0.
 */

/*
 * TODO:
 * - Add caching, or at least memoize the count query
 * - Add more unit tests (as always)
 * - Handle large listings of users. (Separate admin page for listing? Omit accounts tied to email with only one account?)
 * - In Multisite, list blog(s) associated with each user?
 * - Support different limits for different email addressess?
 * - Review and update multisite support
 * - Review and update BuddyPress support
 * - Use WP_List_Table to construct table listing rather than the adapted old code.
 * - Add custom validation for 'emails' setting to ensure valid email addresses are defined on each line.
 */

/*
	Copyright (c) 2008-2015 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_AllowMultipleAccounts' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

class c2c_AllowMultipleAccounts extends C2C_Plugin_039 {

	/**
	 * The one true instance.
	 *
	 * @var c2c_AllowMultipleAccounts
	 */
	private static $instance;


	protected $allow_multiple_accounts = false;  // Used internally; not a setting!
	protected $exceeded_limit          = false;
	protected $retrieve_password_for   = '';

	// Used for hacks.
	protected $hack_user               = null;
	protected $hack_remapped_emails    = array();

	/**
	 * Get singleton instance.
	 *
	 * @since 3.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '3.0.4', 'allow-multiple-accounts', 'c2c', __FILE__, array( 'settings_page' => 'users' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 2.5
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * @since 2.5
	 */
	public static function uninstall() {
		delete_option( 'c2c_allow_multiple_accounts' );
	}

	/**
	 * Initializes the plugin's config data array.
	 */
	public function load_config() {
		$this->name      = __( 'Allow Multiple Accounts', $this->textdomain );
		$this->menu_name = __( 'Multiple Accounts', $this->textdomain );

		$this->config = array(
			'allow_for_everyone' => array(
				'input'            => 'checkbox',
				'default'          => true,
				'label'            => __( 'Allow multiple accounts for everyone?', $this->textdomain ),
				'help'             => __( 'If not checked, only the email addresses listed below can have multiple accounts.', $this->textdomain )
			),
			'account_limit'      => array(
				'input'            => 'int',
				'default'          => '',
				'label'            => __( 'Account limit', $this->textdomain ),
				'help'             => __( 'The maximum number of accounts that can be associated with a single email address. Leave blank to indicate no limit.', $this->textdomain )
			),
			'emails'             => array(
				'input'            => 'inline_textarea',
				'datatype'         => 'array',
				'default'          => '',
				'input_attributes' => 'style="width:98%;" rows="6"',
				'label'            => __( 'Multi-account email addresses', $this->textdomain ),
				'help'             => __( 'If the checkbox above is unchecked, then only the email addresses listed here will be allowed to have multiple accounts. Define one per line.', $this->textdomain )
			),
		);
	}

	/**
	 * Override plugin framework's register_filters() to register actions and filters.
	 */
	public function register_filters() {
		if ( is_multisite() ) {
			add_action( 'network_admin_menu',     array( $this, 'admin_menu' ) );
			remove_action( 'admin_menu',          array( $this, 'admin_menu' ) );
		}

		add_action( 'register_post',              array( $this, 'register_post' ), 1, 3 );
		add_filter( 'registration_errors',        array( $this, 'registration_errors' ), 1 );
		add_action( 'retrieve_password',          array( $this, 'retrieve_password' ) );
		add_filter( 'retrieve_password_message',  array( $this, 'retrieve_password_message' ) );
		add_action( 'user_profile_update_errors', array( $this, 'user_profile_update_errors' ), 1, 3 );
		add_filter( 'wpmu_validate_user_signup',  array( $this, 'bp_members_validate_user_signup' ) );

		// Hacks due to unfortunate changes made in WP 3.0 (and still present in WP 4.1).
		add_filter( 'pre_user_email',             array( $this, 'hack_pre_user_email' ), 100 );
		add_filter( 'pre_user_login',             array( $this, 'hack_pre_user_login' ), 100 );
		add_action( 'profile_update',             array( $this, 'hack_restore_remapped_email_address' ), 0 );
		add_action( 'user_register',              array( $this, 'hack_restore_remapped_email_address' ), 0 );

		add_action( $this->get_hook( 'after_settings_form' ), array( $this, 'list_multiple_accounts' ) );
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text Optional. Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		$options = $this->get_options();

		parent::options_page_description( __( 'Allow Multiple Accounts Settings', $this->textdomain ) );
		echo '<p>' . __( 'Allow multiple user accounts to be created from the same email address.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'By default, WordPress only allows a single user account to be associated to a specific email address. This plugin removes that restriction. A setting is also provided to allow only certain email addresses to be used by multiple accounts. You may also specify a limit to the number of accounts an email address can have.', $this->textdomain ) . '</p>';
		echo '<p><a href="#multiaccount_list">' . __( 'View a list of user accounts grouped by email address.', $this->textdomain ) . '</a></p>';
	}

	/**
	 * Fools wp_insert_user() into permitting an email address to be used more
	 * than once, if the plugin allows it.
	 *
	 * WHAT?
	 * This is a hack. But lighter weight than employed in pre-3.0 (of the plugin).
	 *
	 * WHY?
	 * WP 3.0 merged WPMU code for user registration, duplicating the
	 * email_exists() checks, one is in register_new_user() and one is in
	 * wp_insert_user(), the errors generated in the latter of which cannot be
	 * suppressed.
	 *
	 * HOW?
	 * In wp_insert_user(), the 'pre_user_email' filter fires before the
	 * unfilterable and almost uncircumnavigatable email_exists() check in
	 * wp_insert_user(). WP will only skip the email_exists() if WP_IMPORTING is
	 * defined (which is a technique I considered exploiting; see NOTE FROM THE
	 * DEVELOPER at top of file). However, in cases where the user is using an
	 * email address that can be used multiple times, the plugin can temporarily
	 * change the email address to something unique (but still functional for the
	 * user, e.g. user@example.org becomes user+ama0@example.org). Doing so ensures
	 * that the email address is unique. Once the user account is properly created
	 * or updated, the plugin updates the user's email address directly in the
	 * database.
	 *
	 * In v2.6 of the plugin, it took the approach of overriding get_user_by() (if
	 * it could) since that is used by email_exists(). Via some object variables
	 * set on just-earlier-firing actions, the custom get_user_by() knew to
	 * not return a user in order to fool email_exists() into thinking an email
	 * address wasn't in use.
	 *
	 * @since 3.0
	 *
	 * @param string $email The email address being saved.
	 * @return string
	 */
	public function hack_pre_user_email( $email ) {
		if ( ! is_email( $email ) ) {
			return;
		}

		if ( ! $this->has_exceeded_limit( $email, $this->hack_user ) ) {
			/*
			 * Setting WP_IMPORTING at this point would fool wp_insert_user() into not
			 * performing the email_exists() check, allowing the plugin to work as
			 * expected. But that is a hack, could have conflicts with other plugins that
			 * may check WP_IMPORTING, and isn't unit testable. It remains here,
			 * commented out, until it is certain it need not be considered any longer.
			 */
			//if ( ! defined( 'WP_IMPORTING' ) && email_exists( $email ) ) {
			//	define( 'WP_IMPORTING', true );
			//}
			//return;

			/*
			 * Temporarily modify the email address to ensure it is unique in order to
			 * bypass WP's email_exists() check. The real email address will be swapped
			 * back in later via hooking 'profile_update' and 'user_register'.
			 *
			 * This active approach is probably just as hacky as setting WP_IMPORTING,
			 * but at least it's self-contained and unit testable. Perhaps just as
			 * importantly, it allows calls to wp_(create|insert)_user() to be properly
			 * handled by the plugin.
			 */
			$old_email = $email;

			// No need to remap if user is updating their profile without an email change.
			$skip_remap = ( $this->hack_user && $this->hack_user->user_email === $email );

			// Find a unique, but functional, variation of the original email address
			// (just in case it gets changed here but fails to get restored soon
			// afterwards).
			for ( $i = 0; ! $skip_remap && email_exists( $email ); $i++ ) {
				$email = str_replace( '@', "+ama{$i}@", $old_email );
			}

			// Don't store the email address to remapping unless it actually got remapped.
			if ( $email !== $old_email ) {
				$this->hack_remapped_emails[ $email ] = $old_email;
			}
		}

		return $email;
	}

	/**
	 * Stores the user_id of the login being updated.
	 *
	 * This is a hack because there is no hook in the wp_create_user()/
	 * wp_insert_user() sequence that contains all userdata or user object or user
	 * id of the potential user being updated until the very end, which is too late
	 * to bypass any email uniqueness checks.
	 *
	 * @see hack_pre_user_email()
	 *
	 * @since 3.0
	 *
	 * @param string $user_login The user login.
	 * @return string
	 */
	public function hack_pre_user_login( $user_login ) {
		// Don't bother storing user_id if this is happening during registration or
		// if the specified username does not exist.
		if ( $user_login && ! did_action( 'register_post' ) && username_exists( $user_login ) ) {
			$this->hack_user = get_user_by( 'login', $user_login );
		}

		return $user_login;
	}

	/**
	 * Restores a potentially remapped email address.
	 *
	 * The remapping is part of a hack to bypass difficult to bypass WP checks for
	 * email address uniqueness.
	 *
	 * @since 3.0
	 *
	 * @param int $user_id The id of the user just registered or updated.
	 */
	public function hack_restore_remapped_email_address( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		if ( ! $user instanceof WP_User ) {
			return;
		}

		$email = $user->user_email;

		if ( $email && isset( $this->hack_remapped_emails[ $email ] ) ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->users,
				array( 'user_email' => $this->hack_remapped_emails[ $email ] ),
				array( 'ID' => $user_id )
			);

			unset( $this->hack_remapped_emails[ $email ] );
			clean_user_cache( $user_id );
		}
	}

	/**
	 * Outputs list of all user email addresses and their associated accounts.
	 */
	public function list_multiple_accounts() {
		global $wpdb;

		$users = get_users( array( 'fields' => array( 'ID', 'user_email' ), 'blog_id' => '' ) );
		$by_email = array();
		foreach ( $users as $user ) {
			$by_email[ $user->user_email ][] = $user;
		}
		$emails = array_keys( $by_email );
		sort( $emails );
		$style = '';

		echo <<<END
			</div>
			<style type="text/css">
				.emailrow {
					background-color: #ffffef;
				}
				.check-column {
					display: none;
				}
				.column-username img {
					margin-left: 20px;
				}
			</style>
			<div class='wrap'><a name='multiaccount_list'></a>
				<h2>

END;
		echo __( 'Email Addresses with Multiple User Accounts', $this->textdomain );
		echo <<<END
				</h2>
				<table class="widefat">
				<thead>
				<tr class="thead">

END;
		echo '<th>' . __( 'Username', $this->textdomain ) . '</th>' .
			 '<th>' . __( 'Name', $this->textdomain ) . '</th>' .
			 '<th>' . __( 'Email', $this->textdomain ) . '</th>' .
			 '<th>' . __( 'Role', $this->textdomain ) . '</th>';
// .
//			 '<th class="num">' . __( 'Posts', $this->textdomain ) . '</th>';
		echo <<<END
				</tr>
				</thead>
				<tbody id="users" class="list:user user-list">

END;

		foreach ( $emails as $email ) {
			$email_users = $by_email[ $email ];
			$count = count( $by_email[ $email ] );
			// Omit listing user accounts uniquely associated with an email address.
			if ( $count <= 1 ) {
				continue;
			}
			echo '<tr class="emailrow"><td colspan="6">';
			printf( _n( '%1$s &#8212; %2$d account', '%1$s &#8212; %2$d accounts', $count, $this->textdomain ), $email, $count );
			echo '</td></tr>';
			foreach ( $by_email[ $email ] as $euser ) {
				$user_object = new WP_User( $euser->ID );
				$roles = $user_object->roles;
				$role = array_shift( $roles );
				$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
				echo "\n\t" . $this->user_row( $user_object, $style, $role );
			}
		}

		echo <<<END
				</tbody>
				</table>
			</div>

END;
	}

	/**
	 * Indicates if the specified email address has exceeded its allowable number of accounts.
	 *
	 * To be clear: this is checking if the potential *additional* use of the specified email
	 * address would cause the use of that email address to exceed plugin-enforced limits.
	 *
	 * @since 3.0 Largely refactored.
	 *
	 * @param string      $email   Email address.
	 * @param int|WP_User $user_id Optional. User object or ID of existing user, if updating a user.
	 * @return boolean    True if the email address has exceeded its allowable number of accounts; false otherwise.
	 */
	public function has_exceeded_limit( $email, $user_id = null ) {
		$has     = false;
		$options = $this->get_options();
		$count   = $this->count_multiple_accounts( $email, $user_id );

		// If an explicit limit is set, use it.
		if ( $options['account_limit'] ) {
			$limit = (int) $options['account_limit'];
		}
		// Else assume no limit for now.
		else {
			$limit = 0;
		}

		// If multiple email address use is enabled for everyone.
		if ( $options['allow_for_everyone'] ) {
			// If no limit has been defined, ensure the limit isn't exceeded.
			if ( ! $limit ) {
				$count = -1;
			}
			// Else the explicitly set limit will be honored.
		}
		// Else only certain email addresses can be used multiple times.
		else {
			// If email address is on the whitelist, ensure the limit isn't exceeded.
			if ( $options['emails'] && in_array( $email, (array) $options['emails'] ) ) {
				$count = -1;
			}
			// Else email address can't be used more than once.
			else {
				$limit = 1;
			}
		}

		// Note: Certain conditions above set $count to -1 to ensure this is always
		// true in those situations.
		return $count >= $limit;
	}

	/**
	 * Returns count of the number of users associated with a given email address.
	 *
	 * @param string      $email   The email address.
	 * @param int|WP_User $user_id Optional. User object or ID of existing user, if updating a user.
	 * @return int        The number of users associated with the given email.
	 */
	public function count_multiple_accounts( $email, $user_id = null ) {
		global $wpdb;

		if ( $user_id && is_object( $user_id ) ) {
			$user_id = $user_id->ID;
		}

		$sql = "SELECT COUNT(*) AS count FROM $wpdb->users WHERE user_email = %s";
		if ( $user_id ) {
			$sql .= ' AND ID != %d';
		}
		$count = (int) $wpdb->get_var( $wpdb->prepare( $sql, $email, $user_id ) );

		return $count;
	}

	/**
	 * Returns the users associated with the given email address.
	 *
	 * @param string $email The email account.
	 * @return array All of the users associated with the given email.
	 */
	public function get_users_by_email( $email ) {
		return get_users( array( 'search' => $email, 'blog_id' => '' ) );
	}

	/**
	 * Returns a boolean indicating if the given email address is associated with
	 * more than one user account.
	 *
	 * @param string $email The email address.
	 * @return bool  True if email address is associated with more than one user account.
	 */
	public function has_multiple_accounts( $email ) {
		return $this->count_multiple_accounts( $email ) > 1 ? true : false;
	}

	/**
	 * Handler for 'register_post' action. Intercepts potential 'email_exists'
	 * error and sets flags for later use, pertaining to if multiple accounts are
	 * authorized for the email address and/or if the email has exceeded its
	 * allocated number of accounts.
	 *
	 * @param string   $user_login User login.
	 * @param string   $user_email User email address.
	 * @param WP_Error $errors     Error object.
	 * @param int      $user_id    Optional. ID of existing user, if updating a user.
	 */
	public function register_post( $user_login, $user_email, $errors, $user_id = null ) {
		$options = $this->get_options();

		$allow_multiple_accounts = $options['allow_for_everyone'] || in_array( $user_email, $options['emails'] );
		if ( $errors->get_error_message( 'email_exists' ) && $allow_multiple_accounts ) {
			if ( $this->has_exceeded_limit( $user_email, $user_id ) ) {
				$this->exceeded_limit = true;
			} else {
				$this->allow_multiple_accounts = true;
			}
		}
	}

	/**
	 * Handler for 'registration_errors' action to add and/or remove registration
	 * errors as needed.
	 *
	 * @param WP_Error  $errors Error object.
	 * @return WP_Error The potentially modified error object.
	 */
	public function registration_errors( $errors ) {
		if ( $this->exceeded_limit ) {
			$errors->add( 'exceeded_limit', __( '<strong>ERROR</strong>: Too many accounts are associated with this email address, please choose another one.', $this->textdomain ) );
		}

		if ( $this->allow_multiple_accounts || $this->exceeded_limit ) {
			if ( method_exists( $errors, 'remove' ) ) {
				$errors->remove( 'email_exists' );
			} else { // Pre-WP4.1 compatibility
				unset( $errors->errors['email_exists'] );
				unset( $errors->error_data['email_exists'] );
			}
		}

		return $errors;
	}

	/**
	 * Roundabout way of determining what user account a password retrieval is
	 * being requested for since some of the actions/filters don't specify.
	 *
	 * @param string  $user_login User login.
	 * @return string The same value as passed to the function.
	 */
	public function retrieve_password( $user_login ) {
		$this->retrieve_password_for = $user_login;

		return $user_login;
	}

	/**
	 * Appends text at the end of a 'retrieve password' email to remind users what
	 * accounts they have associated with their email address.
	 *
	 * @param string  $message The original email message.
	 * @return string Potentially modified email message.
	 */
	public function retrieve_password_message( $message ) {
		$user = get_user_by( 'login', $this->retrieve_password_for );

		if ( $this->has_multiple_accounts( $user->user_email ) ) {
			$message .= "\r\n\r\n";
			$message .= __( 'For your information, your email address is also associated with the following accounts:', $this->textdomain ) . "\r\n\r\n";
			foreach ( $this->get_users_by_email( $user->user_email ) as $user ) {
				$message .= "\t" . $user->user_login . "\r\n";
			}
			$message .= "\r\n";
			$message .= __( 'In order to reset the password for any of these (if you aren\'t already successfully in the middle of doing so already), you should specify the login when requesting a password reset rather than using your email.', $this->textdomain ) . "\r\n\r\n";
		}

		return $message;
	}

	/**
	 * Intercept possible email_exists errors during user updating, and also possibly add errors.
	 *
	 * @param WP_Error $errors Error object.
	 * @param boolean  $update Is this being invoked due to a user being updated?.
	 * @param WP_User  $user   User object.
	 */
	public function user_profile_update_errors( $errors, $update, $user ) {
		$user_id = $update ? $user->ID : null;
		$this->register_post( $user->user_login, $user->user_email, $errors, $user_id );
		$errors = $this->registration_errors( $errors );
	}

	/**
	 * Check user_email for exceeding allowed use under BuddyPress
	 *
	 * Like WP of yore (pre-3.0), BP allows for all registration errors to be
	 * intercepted after detection but before handling by WP. That allow this
	 * function to detect an error raised by due to email_exists() and ignore
	 * or modify it as appropriate according to this plugin.
	 *
	 * Note: This function is hooked against the 'wpmu_validate_user_signup'
	 * filter because it is consistently present across more BP versions,
	 * whereas its own 'bp_core_validate_user_signup' is slated to be renamed
	 * 'bp_core_validate_user_signup' in BP1.3.
	 *
	 * @since 2.5
	 *
	 * @param array  $result BP signup validation result array consisting of 'user_name', 'user_email', and 'errors' elements.
	 * @return array The possibly modified results array.
	 */
	public function bp_members_validate_user_signup( $result ) {
		if ( $result['errors'] ) {
			$errors = $result['errors']->get_error_messages( 'user_email' );
			if ( ! empty( $errors ) ) {
				$new_errors = array();
				$bp_msg = __( 'Sorry, that email address is already used!', 'buddypress' );
				foreach ( $errors as $e ) {
					if ( $e == $bp_msg ) {
						if ( $this->has_exceeded_limit( $result['user_email'] ) ) {
							// Only indicate "Too many accounts" if the account was allowed more than one. Otherwise use BP default.
							if ( $this->has_multiple_accounts( $result['user_email'] ) ) {
								$e = __( 'Too many accounts are associated with this email address, please choose another one.', $this->textdomain );
							} else {
								$e = $bp_msg;
							}
						} else {
							$e = null;
						}
					}
					if ( $e ) {
						$new_errors[] = $e;
					}
				}

				$result['errors']->remove( 'user_email' );
				foreach ( $new_errors as $new_error ) {
					$result['errors']->add( 'user_email', $new_error );
				}
			}
		}

		return $result;
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 *
	 * Slightly adapted version of function last seen in WP 3.0.6
	 *
	 * @since 2.5
	 * (since WP 2.1)
	 *
	 * @param object $user_object User object.
	 * @param string $style       Optional. Attributes added to the TR element. Must be sanitized.
	 * @param string $role        Key for the $wp_roles array.
	 * @param int    $numposts    Optional. Post count to display for this user. Defaults to zero, as in, a new user has made zero posts.
	 * @return string
	 */
	public function user_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		global $wp_roles;

		if ( !( is_object( $user_object) && is_a( $user_object, 'WP_User' ) ) )
			$user_object = new WP_User( (int) $user_object );
		if ( property_exists( $user_object, 'filter' ) )
			$user_object->filter = 'display';
		else // pre-WP 3.3
			$user_object = sanitize_user_object($user_object, 'display');
		$email = $user_object->user_email;
		$url = $user_object->user_url;
		$short_url = str_replace( 'http://', '', $url );
		$short_url = str_replace( 'www.', '', $short_url );
		if ('/' == substr( $short_url, -1 ))
			$short_url = substr( $short_url, 0, -1 );
		if ( strlen( $short_url ) > 35 )
			$short_url = substr( $short_url, 0, 32 ).'...';
		$checkbox = '';
		// Check if the user for this row is editable
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link
			// TODO: make profile/user-edit determination a separate function
			if ( get_current_user_id() == $user_object->ID) {
				$edit_link = 'profile.php';
			} else {
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=$user_object->ID" ) );
			}
			$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";

			// Set up the hover actions for this user
			$actions = array();

			if ( current_user_can('edit_user',  $user_object->ID) ) {
				$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
			} else {
				$edit = "<strong>$user_object->user_login</strong><br />";
			}

			if ( !is_multisite() && get_current_user_id() != $user_object->ID && current_user_can('delete_user', $user_object->ID) ) {
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("users.php?action=delete&amp;user=$user_object->ID", 'bulk-users') . "'>" . __('Delete') . "</a>";
			}
			if ( is_multisite() && get_current_user_id() != $user_object->ID && current_user_can('remove_user', $user_object->ID) ) {
				$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url("users.php?action=remove&amp;user=$user_object->ID", 'bulk-users') . "'>" . __('Remove') . "</a>";
			}
			$actions = apply_filters('user_row_actions', $actions, $user_object);
			$action_count = count($actions);
			$i = 0;
			$edit .= '<div class="row-actions">';
			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				$edit .= "<span class='$action'>$link$sep</span>";
			}
			$edit .= '</div>';

			// Set up the checkbox (because the user is editable, otherwise its empty)
			$checkbox = "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' />";

		} else {
			$edit = '<strong>' . $user_object->user_login . '</strong>';
		}
		$role_name = isset( $wp_roles->role_names[ $role ] ) ? translate_user_role( $wp_roles->role_names[ $role ] ) : __('None');
		$r = "<tr id='user-$user_object->ID'$style>";

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'username' => __('Username'),
			'name' => __('Name'),
			'email' => __('E-mail'),
			'role' => __('Role')
		);

		$avatar = get_avatar( $user_object->ID, 32 );
		foreach ( $columns as $column_name => $column_display_name ) {
			$attributes = "class=\"$column_name column-$column_name\"";

			switch ($column_name) {
				case 'cb':
					$r .= "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'username':
					$r .= "<td $attributes>$avatar $edit</td>";
					break;
				case 'name':
					$r .= "<td $attributes>$user_object->first_name $user_object->last_name</td>";
					break;
				case 'email':
					$r .= "<td $attributes><a href='mailto:$email' title='" . sprintf( __('Email: %s' ), $email ) . "'>$email</a></td>";
					break;
				case 'role':
					$r .= "<td $attributes>$role_name</td>";
					break;
				case 'posts':
					$attributes = 'class="posts column-posts num"' . $style;
					$r .= "<td $attributes>";
					if ( $numposts > 0 ) {
						$r .= "<a href='edit.php?author=$user_object->ID' title='" . __( 'View posts by this author' ) . "' class='edit'>";
						$r .= $numposts;
						$r .= '</a>';
					} else {
						$r .= 0;
					}
					$r .= "</td>";
					break;
				default:
					$r .= "<td $attributes>";
					$r .= apply_filters('manage_users_custom_column', '', $column_name, $user_object->ID);
					$r .= "</td>";
			}
		}
		$r .= '</tr>';

		return $r;
	}

} // end c2c_AllowMultipleAccounts

c2c_AllowMultipleAccounts::get_instance();

endif; // end if !class_exists()


	/*
	 * *******************
	 * TEMPLATE FUNCTIONS
	 *
	 * Functions suitable for use in other themes and plugins
	 * *******************
	 */

	/**
	 * Returns a count of the number of users associated with a given email address.
	 *
	 * @since 2.0
	 *
	 * @param string $email The email address.
	 * @return int   The number of users associated with the given email.
	 */
	if ( ! function_exists( 'c2c_count_multiple_accounts' ) ) {
		function c2c_count_multiple_accounts( $email ) {
			return c2c_AllowMultipleAccounts::get_instance()->count_multiple_accounts( $email );
		}
		add_action( 'c2c_count_multiple_accounts', 'c2c_count_multiple_accounts' );
	}

	/**
	 * Returns the users associated with the given email address.
	 *
	 * @since 2.0
	 *
	 * @param string $email The email address.
	 * @return array All of the users associated with the given email address.
	 */
	if ( ! function_exists( 'c2c_get_users_by_email' ) ) {
		function c2c_get_users_by_email( $email ) {
			return c2c_AllowMultipleAccounts::get_instance()->get_users_by_email( $email );
		}
		add_action( 'c2c_get_users_by_email', 'c2c_get_users_by_email' );
	}

	/**
	 * Returns a boolean indicating if the given email address is associated with more
	 * than one user account.
	 *
	 * @since 2.0
	 *
	 * @param string $email The email address.
	 * @return bool  True if the email address is associated with more than one user account.
	 */
	if ( ! function_exists( 'c2c_has_multiple_accounts' ) ) {
		function c2c_has_multiple_accounts( $email ) {
			return c2c_AllowMultipleAccounts::get_instance()->has_multiple_accounts( $email );
		}
		add_action( 'c2c_has_multiple_accounts', 'c2c_has_multiple_accounts' );
	}
