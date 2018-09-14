=== View Own Posts Media Only ===
Contributors: shinephp
Donate link: http://www.shinephp.com/donate/
Tags: post, media, view, own, only
Requires at least: 3.2
Tested up to: 4.3
Stable tag: trunk

Limits posts and media library items available for contributors and authors by their own (added, uploaded, attached) only.

== Description ==

This plugin allows to restrict user with Author and Contributor roles to view their own Posts and Media Library items only at admin back-end.
Plugin offers option automatically select "Uploaded to this post" item from drop-down list at Insert Media - Media Library dialog and option to hide this drop-down menu for all users except Administrator and Editor, user with 'edit_other_posts' capability.

== Installation ==

Installing procedure:

1. Deactivate plugin if you have the previous version installed. (It is important requirement for switching to this version from a previous one.)
2. Extract "view-own-posts-media-only.zip" archive content to the "/wp-content/plugins/view-own-posts-media-only" directory.
3. Activate "View Own Post Media Only" plugin via 'Plugins' menu in WordPress admin menu. 
4. Plugin has no any settings and works just from the box - activate and forget.

== Screenshots ==
1. screenshot-1.png No other authors items available at Media Library
2. screenshot-2.png No attachment type selection menu at Insert Media window
3. screenshot-3.png Plugin options page

= Translations =
* Russian: [Vladimir Garagulya](http://shinephp.com)

== Frequently Asked Questions ==
- Just ask.


== Changelog ==

= 1.3 =
* 18.10.2014
* Restriction applied to the selected admin pages only (Posts and Media Library) to exclude compatibility issues with other plugins.
* CSS updated for WordPress 4.0.

= 1.2 =
* 09.07.2013 
* Added option to turn on/off ability to hide comments to the posts of other authors. Admin sees all comments now.
* Added option to exclude from plugin action selected custom post type. If you use plugin "Contact Form 7", do not forget to exclude its 'wpcf7_contact_form' post type from this plugin action.

= 1.1 =
* 13.05.2013 
* Enhance compatibility with other plugins:
* Does not hide custom type posts created by Contact Form 7 plugin; 
* Support for 'supress_filters' query variable is added. If you need to make a query without it being filtered by "View Own Post Media Only", use  $wp_query->set ("suppress_filters", true); at your plugin or theme.

= 1.0 =
* 06.01.2013
* 1st release

== Others ==

You can find more information about "View Own Posts Media Only" plugin at this page
http://www.shinephp.com/view-own-posts-media-only-wordpress-plugin/

I am ready to answer on your questions about plugin usage. Use ShinePHP forum at
http://shinephp.com/forums/forum/view-own-posts-media-only
plugin page comments or site contact form for that please.
