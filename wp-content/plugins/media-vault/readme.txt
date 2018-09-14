=== Media Vault ===
Contributors: Max GJP
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6KFT65LQXEHFQ
Tags: media, security, protection, attachments, downloads, download links, powerful, shortcode, flexible, simple, uploads, images, multisite, files, links, private, documents
Requires at least: 3.5.0
Tested up to: 3.8.1
Stable tag: 0.8.12
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Protect attachment files from direct access using powerful and flexible restrictions. Offer safe download links for any file in your uploads folder.

== Description ==

= Protected Attachment Files =

Media Vault cordons off a section of your WordPress uploads folder and secures it, protecting all files within by passing requests for them through a *powerful, flexible and completely customizable* set of permission checks.

After activating the plugin, to protect attachment files with Media Vault you can:

* use the *Media Uploader admin page* to upload new protected attachments,
* use the *Media Vault metabox* to toggle file protection on the 'Edit Media' admin page,
* use the the *Media Vault Protection Settings* fields in the new Media Modal, or, 
* using *bulk actions* in your Media Library page, you can change file protection on multiple pre-existing attachments at once.

By default the only permission check that the plugin does on media files is that the user requesting them be logged in. You can change this *default* behavior from the 'Media Settings' page in the 'Settings' menu of the WordPress Admin. You can also change the restrictions set on attachments on an individual basis by means of either the Media Vault metabox on the 'Edit Media' page or the Media Vault Protection Settings fields in the new Media Modal.

You can also write your own custom restrictions using the `mgjp_mv_add_permission()` function. See [this support question](http://wordpress.org/support/topic/restrict-only-for-subscribers?replies=5) for more details.

= Safe Download Links =

Creating a cross-browser compatible download link for a file is a harder task than might be expected. Media Vault handles this for you, and it does so while preserving all the file security features discussed earlier like blocking downloads to people who should not have access to the file.

The download links are available through a simple shortcode that you can use in your post/page editor screen:

	[mv_dl_links ids="1,2,3"]

where 'ids' are the comma separated list of attachment ids you would like to make available for download in the list.


*Note:* Plugin comes with styles ready for WordPress 3.8+!

*Note:*  **Now supports WordPress MultiSite!**

== Installation ==

= Install Through your Blog's Admin =
*This sometimes does not to work on `localhost`, so if you're running your site off your own computer it's simpler to use the second method.*

1. Go to the 'Plugins' menu in WordPress and select 'Add New'.
1. Type 'Media Vault' in the Search Box and press the 'Search' button.
1. When you find 'Media Vault', click 'Install Now' and after reading it, click 'ok' on the little alert that pops up.
1. When the plugin finishes installing, simply click 'Activate Now'.

= Downloading from WordPress.org =

1. Clicking the big 'Download' button on the right on this page (wordpress.org/plugins/media-vault/) will download the plugin's `zip` folder (`mediavault.zip`).
1. Upload this `zip` folder to your server; to the `/wp-content/plugins/` directory of the site you wish to install the plugin on.
1. Extract the contents of the `zip`. Once it is done you can delete the `mediavault.zip` file if you wish.
1. Activate the plugin through the 'Plugins' menu in WordPress.


Once you have Media Vault activated and fully enabled don't forget to go and check out the plugin's settings on the 'Media Settings' page under the admin 'Settings' menu.

== Frequently Asked Questions ==

= How do I toggle File Protection on an existing Attachment? =

You have two options. If you only want to toggle File Protection on **a single attachment**, you can do it directly from the attachment's Edit page. In the 'Media Vault Settings' metabox in the right column, you can toggle protection by clicking the button that will either say 'Add to Protected' or 'Remove from Protected'. Remember to click 'Update' to save the changes you have made.

If you want to toggle File Protection on **multiple attachments**, the plugin comes with two bulk actions that can be performed in the Media Library page in the WordPress Admin. On the Media Library page select the attachment or attachments you would like to manipulate by ticking the box next to their title. Then from the 'bulk options' dropdown select either the 'Add to Protected' or 'Remove from Protected' option and click the 'Apply' button next to the dropdown.

You can verify that the action took effect by looking at the Media Vault column in the Media Library list table. It will display when an attachment's files are protected as well as the permissions set on the particular attachment.

= Can files uploaded from the front-end be automatically protected? =

Yes they can, see [this support question](http://wordpress.org/support/topic/default-upload-protection-from-front-end?replies=5) for more details!

= How are unprotected files handled? How does this plugin work? =

This question was recently asked and answered in [this support thread](https://wordpress.org/support/topic/how-the-unprotected-files-are-handeled?replies=3), check it out!

== Screenshots ==

1. The WordPress Media Upload page with Media Vault file protection activated.
2. An example of the access denied prompt produced by a custom file access restriction implemented very simply using Media Vault.
3. The WordPress Media Upload page with Media Vault file protection activated (in WP mp6 & WP 3.8+)

== Changelog ==

= 0.8.12 =
fixed bug in `mv-file-handler.php` causing php Notice and corrupted files. Big thanks to user [ikivanov](http://profiles.wordpress.org/ikivanov) for pointing it out and providing the solution!

= 0.8.11 =

* fixed bug in `mv-metaboxes.php` causing php Notice. Thank you user [ikivanov](http://profiles.wordpress.org/ikivanov) for pointing it out!
* fixed bug in `mv-metaboxes.php` causing metabox stylesheet not to be served

= 0.8.10 =
Fixed typo causing php error in `mv-extra-activation-steps.php`. Thank you user [wwn2013](http://profiles.wordpress.org/wwn2013) for pointing it out!

= 0.8.9 =

* Added Attachment Edit fields to the new Media Modal to make it easier to manage which files are protected with Media Vault and what permissions are set on each protected file.
* Fixed visual bug with IE8 and the general sibling selector not showing permissions in the Media Vault Metabox on the attachment edit admin page.
* Organized minified js code into seperate folder

= 0.8.8 =
fixed bug in `mv-file-handler.php` that allowed files to be viewed in the protected folder when 'Save uploads in year/month folders' was *not* selected. Thanks to [WayneHarris](http://profiles.wordpress.org/wayneharris) for pointing the issue out.

= 0.8.7 =
added a body class to the WP admin to let Media Vault know to use the new 3.8+ styles

= 0.8.6 =
fixed code that required php 5.4 and above, to be compatible with older versions of php

= 0.8.5 =

* Now the plugin is not fully enabled if the rewrite rules are detected to not be fully functioning as required
* Added flag to indicate Media Vault can **only** be network activated on WordPress Multisite installs
* Added return to homepage link in standard access denied message on protected media
* Added Media Vault Activation/Deactivation Helper (MVADH) to support setups where Media Vault cannot automatically configure all components it needs to function, particularly the rewrite rules. Currently, MVADH supports single & multisite WordPress installs on Apache + mod_rewrite. Support for more server technologies coming soon. *MVADH not supporting a particular server technology **does not** mean Media Vault cannot work with that technology*, just that you may need to figure some of how to make the rewrite rules work by yourself.
* Added **much** better support for WP multisite: better activation support, better deactivation support, better uninstallation support, better rewrite rule support, better file-handling support, better plugin update support.
* Added MVADH rewrite rule support for ugly permalink setups
* Made some performance tweaks & minor bugfixing

= 0.8 =

* added functionality to allow a place-holder image to replace a requested protected image.
* refactored permission resolving functions to be more thorough and efficient.
* modified `mgjp_mv_admin_check_user_permitted()` function to handle non admin checking and renamed it to `mgjp_mv_check_user_permitted()` to reflect this.
* added plugin update handling class to manage per update required changes fluidly.
* created an `uninstall.php` file and moved all settings removal actions there so that settings are now saved when a user only deactivates and does not remove the plugin.
* added a link to the Media Vault settings to the Plugins page.
* fixed bug with the Media Vault metabox not being able to set the default permission on the attachment.
* fixed bug with the `mgjp_mv_get_the_permission()` function returning the wrong permission.

= 0.7.1 =
The Metabox - added a Media Vault metabox to the attachment editor screen to manage protection meta + bugfixing on the bulk actions script

= 0.7 =
*Minor remastering of permission checking code to address protected attachment access from within the WordPress backend. Highly recommended to immediately update.*

* Rewrote default permissions to return rather than using `wp_die` directly. They now MUST either return `true` upon determining the current user is permitted to access a particular attachment; or if access is denied: `false` or a [`WP_Error`](http://codex.wordpress.org/Class_Reference/WP_Error) object with an error message included. 
* Added `mgjp_mv_admin_check_user_permitted()` function to use permission functions to change access to attachments while within the WP Admin.
* Hooked into the 'user_has_cap' and 'media_row_actions' filters to restrict what users could see and manipulate in the backend for the specific attachments they did not have the permission to access.
* Rewrote the custom permission checking function handling section of the file-handling script `mv-file-handler.php` to accommodate the changes to the way custom permission functions now return values.

= 0.6 =
Initial Release.

== Upgrade Notice ==

= 0.8.12 =
fixed bug in `mv-file-handler.php` causing php Notice and corrupted files. Big thanks to user [ikivanov](http://profiles.wordpress.org/ikivanov) for pointing it out and providing the solution!

= 0.8.11 =
fixed bug in `mv-metaboxes.php` causing php Notice. Thank you user [ikivanov](http://profiles.wordpress.org/ikivanov) for pointing it out!

= 0.8.10 =
fixed typo causing php error in `mv-extra-activation-steps.php`. Thank you user [wwn2013](http://profiles.wordpress.org/wwn2013) for pointing it out!

= 0.8.9 =
Added Attachment Edit Fields to the new Media Modal and fixed visual bug with IE8

= 0.8.8 =
fixed bug in `mv-file-handler.php` that allowed files to be viewed in the protected folder when 'Save uploads in year/month folders' was *not* selected. Thanks to [WayneHarris](http://profiles.wordpress.org/wayneharris) for pointing the issue out.

= 0.8.7 =
added a body class to the WP admin to let Media Vault know to use the new 3.8+ styles

= 0.8.6 =
fixed code that required php 5.4 and above, to be compatible with older versions of php

= 0.8.5 =
The WPMU update - more organized code, now 90% more optimized to run fine both on single-site installs as well as multisite installs.

= 0.8 =
The Update update - good amount of bugfixing, and streamlining of code. Added a class to handle fluid plugin updates and some functions to allow for image placeholders to appear in the place of restricted images.

= 0.7.1 =
The Metabox - added a Media Vault metabox to the attachment editor screen to manage protection meta + bugfixing

= 0.7 =
Version 0.7 includes minor remastering of the permission checking code to address protected attachment access from within the WordPress backend. It is strongly recommended that you upgrade from version 0.6.

= 0.6 =
This is the original release version.