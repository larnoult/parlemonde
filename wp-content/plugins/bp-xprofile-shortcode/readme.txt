=== BP XProfile Shortcode ===
Contributors: tylerdigital, croixhaug
Tags: buddypress, xprofile, shortcode, users, user meta
Requires at least: 3.5
Tested up to: 3.9.2
Stable tag: 1.0.1

Adds Shortcode for BuddyPress XProfile data

== Description ==
Adds Shortcode for BuddyPress XProfile data

For quick reference, here is a list of example shortcodes:

Reference field by ID in case name changes:
**[xprofile field=12]**

Output city using default user detection (currently displayed BP profile, fallback to author of current page/post, fallback to currently logged in user):
**[xprofile field="City"]**

Output city for a specific user by ID or username:
**[xprofile field="City" user=20]**
**[xprofile field="City" user="someusername"]**

Override the default user detection by specifying method:
Output city for the currently logged in user (blank if no user is logged in):
**[xprofile field="City" user=current]**

Output city for the author of the current page/post being viewed:
**[xprofile field="City" user=author]**

Output city for the currently displayed BuddyPress profile:
**[xprofile field="City" user=displayed]**

[Learn more about BP XProfile Shortcode](http://tylerdigital.com/products/bp-xprofile-shortcode-plugin/)



== Changelog ==
= v1.0.1 =
* Fix fatal error from deprecated function in WP 3.6.0

== Installation ==
Install and activate, there are no settings in the UI
Documentation of shortcode available at http://tylerdigital.com/products/bp-xprofile-shortcode-plugin/