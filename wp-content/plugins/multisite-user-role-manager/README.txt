=== Multisite User Role Manager ===
Contributors: ozthegreat
Donate link: https://wpartisan.me
Tags: multisite, wpmu, users, roles, management
Requires at least: 4.0
Tested up to: 4.8.3
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manage user roles for each blog from a single screen on multisite (WPMU) setups

== Description ==

For WordPress Multisite (WPMU) installs, allows Super Admins to easily manage each users roles and blogs from one
screen in the Network Admin menu.

You no longer have to go to each blog to change the user's role. It's also
much easier to see which sites a user is associated with.


== Installation ==

1. Upload `wpmu-user-role-manager.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Network Admin and edit any user. Click the `Manage Role` button.

== Frequently Asked Questions ==

= Can you order the table? =

Nope, coming in the pro version

== Screenshots ==

1. The manage user roles screen

== Changelog ==

= 1.0.7 =
* WordPress Version 4.8.3 compatibility

= 1.0.6 =
* WordPress Version 4.7.1 compatibility

= 1.0.5 =
* Readme corrections

= 1.0.4 =
* WordPress Version 4.6.1 compatibility

= 1.0.3 =
* Fix CSS for role selector
* Better comments for actions and filters
* Display the user's name in the model box
* Stricter translation escaping
* Make PHP >= 5.2 compatible

= 1.0.2 =
* Add filter for current user permission
* Add comments

= 1.0.1 =
* Conditionally load scripts better
* Decode entities on blogname

= 1.0 =
* Plugin released
