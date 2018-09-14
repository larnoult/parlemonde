=== Author Category ===
Contributors: bainternet
Donate link:http://en.bainternet.info/donations
Tags: author category, limit author to category, author posts to category
Requires at least: 3.0
Tested up to: 4.7.0
Stable tag: 0.8

simple lightweight plugin limit authors to post just in one category.

== Description ==

This Plugin allows you to select specific category per user and all of that users posts will be posted in that category only.


**Main Features:**

*   Only admin can set categories for users.
*   Only users with a specified category will be limited to that category, other will still have full control.
*   Removes category metabox for selected users.
*   Removed categories from quick edit for selected users.
*   Option to clear selection.(new)
*   multiple categories per user.(new)

French traslation (since 0.8) thanks to jyd44



Any feedback or suggestions are welcome.

Also check out my <a href=\"http://en.bainternet.info/category/plugins\">other plugins</a>

 

== Installation ==

1.  Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation.
2.  Then activate the Plugin from Plugins page.
3.  Done!
== Frequently Asked Questions ==

= I have Found a Bug, Now what? =

Simply use the <a href=\"http://wordpress.org/tags/author-category?forum_id=10\">Support Forum</a> and thanks a head for doing that.

= How To Use =

Simply login as the admin user and under each user >> profile select the category for that user.

== Screenshots ==
1. User category selection under user profile.
2. Author category metabox.

== Changelog ==
 = 0.8 = 
Added POT file for translations.
Added french translation.
Fixed translation loading to an earlest time to allow panel translation.

 = 0.7 =
updated simple panel version.
added textdomain to plugin and to option panel.
wrapped checkboxes with labels
categoires are now ordered by name.

 = 0.6 =
Fixed xmlrpc posting issue.
Added an option panel to allow configuration of multiple categories.
added An action hook `in_author_category_metabox`

 = 0.5 = 
Added post by mail category limitation.

 = 0.4 = 
Added support for multiple categories per user.
added option to remove user selected category.

 = 0.3 =  
added plugin links,
added XMLRPC and Quickpress support
changed category save function from save_post to default input_tax field.
added a function to overwrite default category option per user.

 = 0.2 = 
Fixed admin profile update issue.

 = 0.1 = 
initial release