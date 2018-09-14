=== User Shortcodes Plus ===
Contributors: kbjohnson90
Tags: user, shortcodes, meta
Donate link: http://kylebjohnson.me/plugins
Requires at least: 4.5
Tested up to: 4.7.3
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add simple user shortcodes to WordPress for displaying information, including custom meta and avatars, for any user.

== Description ==
Add simple user shortcodes to WordPress for displaying information, including custom meta and avatars, for any user.

Available Shortcodes:
- [user_id]
- [user_login]
- [user_email]
- [user_firstname]
- [user_lastname]
- [user_nicename]
- [user_display]
- [user_display_name] (alias)
- [user_registered]
- [user_avatar] (image)
- [user_avatar_url]
- [user_url]
- [user_website] (alias)
- [user_description]
- [user_bio] (alias)

Displaying userdata for another user:

ex. [user_email id=2]

== Installation ==
This section describes how to install the plugin and get it working.

1. Upload the `user-shortcodes-plus` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add shortcodes to the content of any post type.

== Frequently Asked Questions ==
What shortcodes are supported?

- [user_id]
- [user_login]
- [user_email]
- [user_firstname]
- [user_lastname]
- [user_nicename]
- [user_display]
- [user_display_name] (alias)
- [user_registered]
- [user_avatar] (image)
- [user_avatar_url]
- [user_url]
- [user_website] (alias)
- [user_description]
- [user_bio] (alias)

== Screenshots ==

1. Shortcodes can be added to any post type via the content editor.
2. Each shortcode is replaced with the user's data, when available.
3. Shortcodes can be inserted using the 'Add User Shortcode' button on the TinyMCE editor.
4. A specific user cna be specified by adding the 'id' attribute to the shortcode.
5. Any user metadata can be displayed using the [user_meta] shortcode with a specified 'key' attribute.

== Changelog ==

= 2.0.1 =
* Fixed a bug with showing the TinyMCE Button on new posts/pages.

= 2.0.0 =
* Add support for [user_url] and [user_website] shortcodes.
* Add support for [user_description] and [user_bio] shortcodes.
* Add TinyMCE editor button for easily adding shortcodes.
* Restructure plugin for extendability.

= 1.0.1 =
* Add support for [user_avatar] and [user_avatar_url].

= 1.0.0 =
* Initial Commit

== Upgrade Notice ==

= 2.0.0 =
Added new shortcodes for the user's website and bio. Also added a TinyMCE button.

= 1.0.1 =
Added new shortcodes for the user's avatar.
