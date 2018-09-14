=== WP Edit ===
Contributors: josh401
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A9E5VNRBMVBCS
Tags: wpedit, wp edit, editor, buttons, button, add, font, font style, font select, table, tables, visual editor, search, replace, colors, color, anchor, advance, advanced, links, link, popup, javascript, upgrade, update, admin, image, images, citations, preview, html, custom css, borders, pages, posts, colorful, php, php widget, shortcode, shortcodes, style, styles, plugin, login, excerpt, id, post, page, youtube, tinymce
Requires at least: 3.9
Tested up to: 4.9.6
Stable tag: 4.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Take complete control over the WordPress content editor.

== Description ==

= Welcome =
Welcome to WP Edit. Finally, take control of the default WordPress editor and unlock the power of additional editor tools. Arrange buttons into toolbars the way you want; to ease your workflow. WP Edit adds dozens of additional custom options to the WordPress editor.

= NEW Custom Buttons API =
WP Edit now uses a custom buttons API which allows other plugin/theme developers to add their editor buttons into the WP Edit button configuration; allowing a WP Edit user to place the plugin/theme buttons into any desired location.

Refer your favorite plugin/theme developers to the [WP Edit Custom Buttons API](http://learn.wpeditpro.com/custom-buttons-api/) documentation to get your favorite buttons added to WP Edit.

= Introduction =
For a riveting video introduction into the possibilities available with WP Edit; please visit [Jupiter Jim's Marketing Team](http://jupiterjim.club/wordpress/tutorials/change-font-family-font-size-wordpress-4-4-1/).

= Description =

WP Edit is built around three years of custom WordPress development. WP Edit adds extensive, additional editing functionality to the default WordPress editor. Begin creating content like the pros; without knowing a single bit of HTML or CSS.

[Subscribe to our Feedblitz List](http://www.feedblitz.com/f/?Sub=950320), and receive news, update notices and more.
[<img title="Subscribe to get updates by email and more!" border="0" src="http://assets.feedblitz.com/chicklets/email/i/25/950320.bmp">](http://www.feedblitz.com/f/?Sub=950320)

= Most Powerful Features =
WP Edit will provide new buttons, additional options, and extended formatting abilities to the exisiting content editor.

* Easily insert images, media, YouTube videos, and clip art.
* Create tables via a graphical interface.
* Adjust table cell border and background colors.
* No need to learn HTML and CSS (although the basics can certainly help); use buttons with visual interfaces instead!
* Easily access all shortcodes available to your WordPress environment; and insert them into the content editor.
* Use shortcodes to insert columns.. similar to "magazine" style layouts, in your content areas.

= Why should you use this plugin? =
Because WP Edit is the culmination of three years development in the WordPress content editor. You can begin creating content (with advanced layouts); easily insert all types of external media (YouTube, Vimeo, etc.); adjust fonts, styles, colors, and sizes; and much more!

= What is included in the free version? =
* Drag and drop functionality for custom creation of the top row of editor buttons.
* Adds additional editor buttons such as subscript, superscript, insert media, emoticons, search and replace, html editor, preview.. and many more.
* Add your custom editor to excerpt areas and profile descriptions.
* Allow shortcodes in excerpt and widget areas.
* Highlight admin posts/pages based on status (green = published, yellow = draft, etc.)
* Easily import/export plugin options.

= Why should you upgrade to WP Edit Pro? =
* Drag and drop functionality for custom creation of all rows of editor buttons.
* Powerful network installation functionality; WP Network Ready.
* User roles for custom button arrangements; allow different user roles access to different editor buttons.
* Extreme Custom Widget Builder - create custom widgets just like posts or pages.. and insert them into any widget area or the content editor.

= Translations =
* Spanish - Provided by Andrew Kurtis with ["WebHostingHub"](http://www.webhostinghub.com).

= Notes =
* This plugin is provided "as-is"; within the scope of WordPress.  We will update this plugin to remain secure, and to follow WP coding standards.
* If you prefer more "dedicated" support, with more advanced and powerful plugin features, please consider upgrading to ["WP Edit Pro"](http://wpeditpro.com). 

= Resources =
* ["Complete Guide to WP Edit Buttons"](http://learn.wpeditpro.com/wp-edit-buttons-guide/)


== Installation ==

* From the WP admin panel, click "Plugins" -> "Add new".
* In the browser input box, type "WP Edit".
* Select the "WP Edit" plugin (authored by "josh401"), and click "Install".
* Activate the plugin.

OR...

* Download the plugin from this page.
* Save the .zip file to a location on your computer.
* Open the WP admin panel, and click "Plugins" -> "Add new".
* Click "upload".. then browse to the .zip file downloaded from this page.
* Click "Install".. and then "Activate plugin".

OR...

* Download the plugin from this page.
* Extract the .zip file to a location on your computer.
* Use either FTP or your hosts cPanel to gain access to your website file directories.
* Browse to the `wp-content/plugins` directory.
* Upload the extracted `wp_edit` folder to this directory location.
* Open the WP admin panel.. click the "Plugins" page.. and click "Activate" under the newly added "WP Edit" plugin.



== Frequently asked questions ==

* Nothing at the moment.

== Screenshots ==

1. Create custom button arrangements from a friendly drag and drop interface.
2. The custom button arrangement will be loaded in the content editor.
3. Eight tabs packed with options.

== Changelog ==

= 4.0.3 =
* Updated readme.
* Ensured plugin not generating any errors/warnings/notices with current version of WordPress.
* WordPress 5.0 will include "Gutenberg" (please research); and I am working on a compatible WP Edit version.

= 4.0.2 =
* 11/17/2017
* Tested to ensure WP 4.9 compatibility.
* Fixed icon to insert date/time.
* Fixed advanced link nofollow checkbox.


= 4.0.1 =
* 04/20/2017
* Fixed warnings when plugin is network activated (pertaining to buttons options).
* Altered readme file to better display in new WordPress plugins repository design.

= 4.0 =
* 10/03/2016
* Added Custom Buttons API; other plugins/themes can add buttons to WP Edit.
* Added dismissable admin notice for Custom Buttons API (help spread the word!).
* Added plugin rating statistics to sidebar (Please rate and review).
* Added nonce fields for every form submission used to save database options.
* Moved plugins.php page styles to properly enqueue (used for notices).

= 3.9 =
* 09/05/2016
* Added functionality to enable visual editor on BBPress forums (Editor tab).
* Fixed strict standards error on wp_widget_rss_output() function (final fix will be done upstream when WordPress 4.7 is released).
* Adjusted plugin css file.

= 3.8.1 =
* 05/11/2016
* Removed a stray var_dump() function.

= 3.8 =
* 05/11/2016
* Added support for WP Edit toolbars in custom post types excerpt areas.
* Fixed deprecated function.  (htmledit_pre changed to format_for_editor) (main.php ~line 115).
* Updated introduction video link.
* Minor changes to ensure WordPress 4.6 compatibility.
* Increased stable tag version.

= 3.7 =
* 01/11/2016

* Fixed Feedblitz image loading insecure over https.
* Fixed WP_PLUGIN_URL constant; switched to using plugins_url() function.
* Fixed profile biography editor.
* Updated compatibility version.

= 3.6 = 
* 12/16/2015
* Update to be stable with WordPress 4.4.

== Upgrade Notice ==

Nothing at the moment.