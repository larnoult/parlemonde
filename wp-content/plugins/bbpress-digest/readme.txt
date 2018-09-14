=== bbPress Digest ===
Contributors: dimadin
Donate link: http://blog.milandinic.com/donate/
Tags: bbPress, digest, notification, notifications
Requires at least: 3.1
Tested up to: 3.8
Stable tag: 2.1

Send digests with forum's active topics.

== Description ==

[Plugin homepage](http://blog.milandinic.com/wordpress/plugins/bbpress-digest/) | [Plugin author](http://blog.milandinic.com/) | [Donate](http://blog.milandinic.com/donate/)

This plugin enables sending of a digests with a list of topics active on a bbPress-powered forum in the last 24 hours or 7 days.

Users are able to choose on their profile edit pages (both built-in and from bbPress) whether or not they want to receive digest, at what time of the day, at which day of the week (if they receive weekly digest), and should digest include topics from all forums or only selected ones.

It requires that cron runs regularly at least once an hour.

bbPress Digest is a very lightweight, it loads necessary files with functions only when needed.

If you are translator, you can translate it to your language and send translations to plugin's author.

== Installation ==

1. Upload `bbpress-digest` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can change default options on 'bbPress Settings' page

== Screenshots ==

1. Example of email sent to user with list of active topics
2. User settings with subscription deselected
3. User settings with subscription selected, which includes all forums
4. User settings with subscription selected, which includes only forums chosen by user
5. User settings with subscription selected, with weekly interval enabled
6. One-click forum subscription
7. General settings

== Changelog ==

= 2.1 =
* Released on 15th December 2013
* Mapped bbPress Digest settings capability. Fixed missing settings in new bbPress version.
* Removed profile settings made for core from bbPress user edit page. Fixed double profile settings on bbPress user edit page.
* Improved profile settings saving. Fixed issue where forums wouldn't be unselected if all forums or unsubsciption is chosen later.
* Fixed notice received for forum list selector.
* Improved documentation for forum list functions.
* Replaced one-click Javascript handler with new one based on new bbPress code. Fixed handler that didn't work.
* Added noscript one-click handler.
* Improved sending of email so that it sends only when there are topics.
* Moved sending of emails to separate method to allow better customization.
* Moved uninstall function to uninstall.php for better performance.

= 2.0 =
* Released on 11th August 2012
* Introduced optional weekly digest.
* Introduced optional one-click subscription from forums pages.
* Added settings for introduced features.
* Moved event to a class to be able to handle both periods.
* Moved to getting IDs instead of whole data when querying active topics for better performance.
* Improved profile fields behavior.
* Improved documentation.
* Added partial Italian translation (thanks Davide Vecchini).

= 1.0 =
* Released on 29th February 2012
* Initial release