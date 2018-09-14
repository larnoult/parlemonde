=== BuddyPress Admin Only Profile Fields ===
Contributors: A5hleyRich, garrett-eclipse
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=S6KBW2ZSVZ8RE
Tags: buddypress, admin, hidden, profile, field, visibility
Requires at least: 4.3.1
Tested up to: 4.3.1
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily set the visibility of BuddyPress profile fields to hidden, allowing only admin users to edit and view them.

== Description ==

Easily set the visibility of BuddyPress profile fields to hidden, allowing only admin users to edit and view them.

**GitHub**

If you would like to contribute to the plugin, you can do so on [GitHub](https://github.com/A5hleyRich/BuddyPress-Admin-Only-Profile-Fields).

== Installation ==

1. Upload `bp-admin-only-profile-fields` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How do I hide a profile field? =

In the WordPress admin area, go to *Users > Profile Fields* and click *Edit* on the desired profile field. Under the *Default Visibility* panel select *Hidden* as the value and click *Save*.

The profile field is now hidden from all users except Administrators.

= How do I change who can view and edit the hidden field? =

Add the following filter to your theme’s functions.php file, substituting *edit_others_posts* with the desired capability:
`function custom_profile_fields_visibility() {
	return 'edit_others_posts'; // Editors
}
add_filter( 'bp_admin_only_profile_fields_cap', 'custom_profile_fields_visibility' );`

== Screenshots ==

1. Edit field BuddyPress screen.

== Changelog ==

= 1.2 =

* New: Added 'Everyone (Admin Editable)' field visibility level
* New: Added 'Only Me (Admin Editable)' field visibility level
* Bug fix: Issues with JS due to admin visibility settings change from checkboxes to selects
* Bug fix: Issue with JS where visibility settings disappear when 'Hidden' selected
* Bug fix: Issue with breaking standard BuddyPress Visibility options

= 1.1.1 =

* Fix fatal error on activation
* Adhere to WordPress coding standards

= 1.1 =

* Hide the _Per-Member Visibility_ options when the _Default Visibility_ is set to _Hidden_

= 1.0 =

* Initial release

== Upgrade Notice ==

= 1.2 =

* New visibility options
* Bug fixes

= 1.1.1 =

* Bug fixes

= 1.1 =

* General improvements

= 1.0 =

* Initial release