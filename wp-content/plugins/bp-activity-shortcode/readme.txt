=== BuddyPress Activity Shortcode ===
Contributors: buddydev, sbrajesh, raviousprime
Tags: buddypress, buddypress activity, sitewide activity, activity shortcode
Requires at least: 4.0
Tested up to: 4.9.7
Stable tag: 1.1.5

BuddyPress Activity shortcode plugin allows you to insert BuddyPress activity stream on any page/post using shortcode.

== Description ==
BuddyPress Activity shortcode plugin allows you to insert BuddyPress activity stream on any page/post using shortcode. It has a lot of flexibility built in the shortcode.
You can customize almost all aspects of the activity list, what should be listed, how many and everything using the shortcode.

This plugin does not include any css and utilizes your theme's css for displaying the activity. If you need any help, please ask on BuddyDev support forums. 
We are helpful people looking forward to assist you.

Features include:

 * List all activities
 * List activities for a user
 * List activities for a group
 * List activities of specific user role.
 * Allow users to post from the page( experimental, if does not work with your theme, please let us know)
 * All options supported by bp_has_activities are available
 * For details, please see [Documentation](https://buddydev.com/plugins/bp-activity-shortcode/ "Plugin page" )
The simple way to use it is by including this shortcode

[activity-stream ]

Please make sure to check the usage instructions on the [BuddyPress Activity shortcode plugin page](https://buddydev.com/plugins/bp-activity-shortcode/ "Plugin page" )

Free & paid supports are available via [BuddyDev Support Forum](https://buddydev.com/support/forums/ "BuddyDev support forums")

== Installation ==

The plugin is simple to install:

1. Download `bp-activity-shortcode.zip`
1. Unzip
1. Upload `bp-activity-shortcode` directory to your `/wp-content/plugins` directory
1. Go to the plugin management page and enable the plugin "BuddyPress Activity Shortcode"

Otherwise, Use the Plugin browser, upload it and activate, you are done.

*This plugin, specially the posting functionality, is not compatible with BP Nouveau.
 I have checked and it seems we won't be able to support posting in the Nouveau template.
 Listing will work with minor regression.*

== Frequently Asked Questions ==

= How to Use =
Add the shortcode [activity-stream ] in your post or page. For detailed usage instruction, please visit plugin page on BuddyDev.


== Changelog ==
= Version 1.1.5 =
 * Fix Pagination/Load more
 * Fix posting to group when there are no activities.

= Version 1.1.4 =
 * if object=groups allow_posting=1 and primary id is given, do not show dropdown in activity post form.
 * Introduce for_group=group-slug shortcode option. Object must be specified as 'groups'.
 * The follow scope now respects role.

= Version 1.1.3 =
 * Added support for load more activities in the current context. Thank you Shay for sponsoring it.
 * use load_more=1 for showing the load more button.

= Version 1.1.2 =
 * Added support for scope='following' when using BuddyPress follow 1.2.x branch too. Sponsored by Shay.
 * Also, scope='following' can be combined with  for ='logged', or for='displayed' or for='author' to display relevant activities.

= Version 1.1.1 =
 * Added actions to generate content before/after the actual shortcode. It allows developers to add extra messages if needed.

= Version 1.1.0 =
 * Added option to filter activity based on roles.
 * Use role='administrator' to list all activities of admin. You can use one or more role like role="administrator,editor"
 * Thank you "Arik Twena" for sponsoring the development.

= Version 1.0.9 =
 * Added option to list activity for Logged in user, Displayed user or post author.
 * Use for="logged" to display activities for logged in user. Other valid values are "displayed", "author".
 * for="displayed" lists activities for the displayed user(If you are on user profile section).
 * If you use for="author" in a page/post, It will list activities for the post author on single post/inside the post loop and on author page.


= Version 1.0.8 =
 * Introduce option to display the activity shortcode contents even on activities page  using hide_on_activity=0
 * Introduce container_class option to allow changing the shortcode output container class. It defaults to 'activity'.
  If you have hide_on_activity=0, we suggest you to change it to something else to avoid the filtering of the content via js.

= Version 1.0.7 =
 * Updated code
 * Tested with BuddyPress 2.7.0

= Version 1.0.5 =
 * Updated code
 * Added support for load more when no filters are used

= Version 1.0.5 =
 * Initial release on WordPress.org plugin repo

