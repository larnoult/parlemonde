=== BuddyPress Edit Activity ===
Contributors: buddyboss
Donate link: https://www.buddyboss.com/donate/
Tags: buddypress, social networking, activity, profiles, messaging, friends, groups, forums, notifications, settings, social, community, networks, networking
Requires at least: 3.8
Tested up to: 4.9.1
Stable tag: 1.0.9
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

BuddyPress Edit Activity allows your members to edit their activity posts on the front-end of your BuddyPress-powered site.

== Description ==

Let your BuddyPress members edit their activity posts and replies on the front-end of the site. You can even set a time limit for how long activity posts should remain editable.

Just activate the plugin, and every activity post and reply will become editable, styled automatically by BuddyPress to fit with your theme.

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'
2. Search for 'BuddyPress Edit Activity'
3. Activate BuddyPress Edit Activity from your Plugins page.

= From WordPress.org =

1. Download BuddyPress Edit Activity.
2. Upload the 'buddypress-edit-activity' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, etc...)
3. Activate BuddyPress Edit Activity from your Plugins page.

= Configuration =

1. Visit 'Settings > BP Edit Activity' to configure plugin options.
2. Adjust the CSS of your theme as needed, to make everything pretty.

== Frequently Asked Questions ==

= Where can I find documentation and tutorials? =

For help setting up and configuring any BuddyBoss plugin please refer to our [tutorials](https://www.buddyboss.com/tutorials/).

= Does this plugin require BuddyPress? =

Yes, it requires [BuddyPress](https://wordpress.org/plugins/buddypress/) to work.

= Will it work with my theme? =

Yes, BuddyPress Edit Activity should work with any theme, and will adopt your BuddyPress styling for activity editing. It may require some styling to make it match perfectly, depending on your theme.

= Where can I request customizations? =

For BuddyPress customizations, submit your request at [BuddyBoss](https://www.buddyboss.com/buddypress-developers/).

== Screenshots ==

1. **Post Editing** - Editing an activity post from the front-end
2. **Admin** - Configuring plugin options

== Changelog ==

= 1.0.9 =
* Tweak - Added warning dialog to warn the user if they try to edit multiple activity at once
* Tweak - Added warning dialog to warn the user if they try to edit activity comment and reply on the same activity at once
* Fix - Fatal error cannot redeclare b_e_a_inline_styles function
* Fix - Editing activity does not work if activity has only photos and no activity content
* Fix - Activity comment image disappear after editing

= 1.0.8 =
* Fix - BuddyBoss Media: photoswipe not working for logged out users
* French translations added, credits to Jean-Pierre Michaud
* German translations added, Credits to Zimmerhofer Alexander

= 1.0.7 =
* Ability to edit BuddyBoss Media photo uploads
* Hide Edit button if activity does not have editable content

= 1.0.6 =
* Added Russian translation files - credits to SirAlex
* Admin notice added to install and activate BuddyPress first
* Edit activity box, more scalable
* Patch for XSS Vulnerability
* PHP notice fix

= 1.0.5 =
* Added French translation files - credits to Jean-Pierre Michaud
* Added Italian translation files - credits to Massimiliano Napoli
* Fixed translation string for "Edit" text
* Added 100% width for edit textarea

= 1.0.4 =
* Multisite compatibility, no longer requires network activation
* Editing activity now shows a "Cancel" button
* Fix for correctly rendering utf-8 characters (Greek, Arabic, etc.)
* Modified script selectors to allow for editing other activity types

= 1.0.3 =
* Remove wrapping <p> tag in edit activity content
* Added Persian translation files - credits to Mahdiar Amani

= 1.0.2 =
* Added Settings option: "Exclude admins from time limit."

= 1.0.1 =
* Updated documentation

= 1.0.0 =
* Initial public release

= 0.0.2 =
* Bug fixes

= 0.0.1 =
* Initial beta version
