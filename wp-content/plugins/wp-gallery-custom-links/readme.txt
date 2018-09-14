=== WP Gallery Custom Links ===
Contributors: johnogg
Tags: gallery links, gallery link, gallery
Requires at least: 3.3.1
Tested up to: 4.7.2
Stable tag: 1.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Specify custom links for WordPress gallery images (instead of attachment or file only).

== Description ==

= Overview =

If you've ever had a WordPress gallery of staff, product, or other images and needed
to link them to other pages but couldn't, this plugin is for you!

This plugin adds a "Gallery Link URL" field when editing images. If the image
is included in a gallery, the "Gallery Link URL" value will be used as the link on
the image instead of the raw file or the attachment post.  There are also several
additional options (see "Usage" below).

It's designed to work even if customizations have been made via the
post_gallery filter; instead of replacing the entire post_gallery function, it
calls the normal function and simply replaces the link hrefs in the generated
output. By default, any Lightbox or other onClick events on custom links
will be removed to allow them to function as regular links.

= Usage =

* See the custom fields added in the screenshots section at http://wordpress.org/extend/plugins/wp-gallery-custom-links/screenshots/.
* For each gallery image, you can specify a custom Gallery Link URL.
* Use "[none]" as the Gallery Link URL to remove the link for that gallery image.
* For each gallery image, you can select a Gallery Link Target ("Same Window" or "New Window").
* For each gallery image, you can select how to handle Lightbox and other onClick events ("Remove" or "Keep").
* For each gallery link, you can add additional css classes.
* Use `[gallery ignore_gallery_link_urls="true"]` to ignore the custom links on an entire gallery.
* Use `[gallery open_all_in_new_window="true"]` and `[gallery open_all_in_same_window="true"]` to open all images in an entire gallery in a new window/the same window, respectively.
* Use `[gallery preserve_click_events="true"]` to keep Lightbox or other onClick events on all custom-linked images in an entire gallery.
* Use `[gallery remove_links="true"]` to remove links on all images in an entire gallery.
* Use `[gallery rel="nofollow"]` to set a rel attribute with value "nofollow" on all links in an entire gallery.

= Hooks =

* Use "wpgcl_filter_raw_gallery_link_url" to filter the custom gallery link URLs as they come out of the database. Note that this may
include the value "[none]" if it has been entered to remove the link later on. Example:

`add_filter( 'wpgcl_filter_raw_gallery_link_url', 'my_gallery_link_url_filter', 10, 3 );
function my_gallery_link_url_filter( $link, $attachment_id, $post_id ) { return '/en/' . $link; }`

== Installation ==

1. Upload the 'wp-gallery-custom-links' folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You should now be able to see settings for each image like in the plugin screenshot when editing a gallery.

== Frequently Asked Questions ==

= #1) Will this plugin work with my theme's galleries? =

Possibly.  WP Gallery Custom Links plugin was designed for use with
1) WordPress's [gallery] shortcode and 2) images uploaded through the
WordPress media manager. Some themes use these features, and others
have their own proprietary way of saving gallery images and drawing out the gallery.
Provided your theme meets the criteria above, the plugin should work with it. You
might also want to see #6 below.

= #2) Will this plugin work with NextGen galleries? =

No, this plugin is not compatible with NextGen galleries.  WP Gallery Custom Links was
designed for use with 1) WordPress's [gallery] shortcode and 2) images uploaded through the
WordPress media manager.  NextGen galleries uses its own [nggallery] etc. shortcodes
that function outside of the WordPress [gallery] shortcode.

= #3) When I enable the plugin, the styling on my gallery changes. Why? =

The way the plugin works requires the gallery generation code to be run twice.  This
may result in it being labeled as "#gallery-2" instead of "#gallery-1."
Check your HTML and CSS for these changes and adjust accordingly.

= #4) I'd like to use the custom link in my own gallery code or in a different custom layout.  How can I get the custom link? =

The custom links are stored as meta values for images, and can be accessed with the following:

`$custom_url = get_post_meta( $attachment_id, '_gallery_link_url', true );`

Please note that "$attachment_id" is a variable for the post ID of the image - you will need to have already defined and set this variable
in your own code and use your variable in this spot.  "$attachment_id" is just an example of what it could be named.

= #5) I've set my gallery to remove Lightbox effects, but they are still coming up, possibly with nothing in them. Why? =

Version 1.9 (hopefully) resolves most of these issues, but if you're still having this problem,
see #5 in the old version's readme file here: http://plugins.svn.wordpress.org/wp-gallery-custom-links/tags/1.8.0/readme.txt.

= #6) When I enable the plugin, nothing in my gallery changes, even though I have custom links set. Why? =

Thing to try #0.5: make sure your gallery is using the [gallery] shortcode (i.e. you've created the gallery by clicking
the "Add Media" button and then "Create Gallery" on the side of the media pop-up window). If you don't see a [gallery] shortcode
in your content and/or you've created the gallery by using a custom form or shortcode generated by your theme (e.g. "I selected
'Gallery' as a page template option in a dropdown" or "I'm seeing [somethemename_gallery] in my content"),
chances are this plugin will not work with that custom code - you would need to contact the author of that custom code to request
they add their own version of custom links that will work with their code.

Thing to try #1: make sure your gallery is set to use either attachment or file links. If the gallery is set to link to "none" there
will be no links to match on, thus this plugin won't be able to swap in custom values.

Thing to try #2: make sure you have the onclick effect set to "remove" if you continue to have undesired lightbox/carousel popups.

Thing to try #3: make sure the hook that this plugin uses (the "post_gallery" filter) is being called.
Some themes and gallery plugins have code that replaces the default WordPress gallery code, and the post_gallery
filter gets left out, which means this plugin never gets called to do anything.  If you do a "View Source" on your gallery page
and see a javascript file named "wp-gallery-custom-links.js" being included, but items you know have custom links are not
using the custom links, try looking around in your theme/gallery plugin to see if the gallery shortcode is
being replaced, and if that function doesn't contain a reference to post_gallery, try adding this near the top of
the function (assumes the attributes variable passed to the shortcode function is named "$attr"):

`$output = apply_filters('post_gallery', '', $attr);
if ( $output != '' )
    return $output;`

You may want to see http://wordpress.org/support/topic/wont-work-syntax-error for an example of adding this code.

This thing to try is a bit on the programmy side, so if you're having trouble, my suggestion would be to contact your theme author
and ask that they support the "post_gallery" filter in their gallery shortcode function.  This would not only fix it for your theme most thoroughly,
but would also fix it for any future users also using that same theme.  Otherwise, any WordPress developer should be able to help you
with the code changes to customize your theme to support the post_gallery filter like WordPress core (not something I consider in the realm of free support, sorry).

= #7) The custom links are working fine, but I need help changing the formatting/styling on my gallery, such as spacing between images, aligning images, or changing image size. =

This plugin just changes links, plus a bit of auxiliary functionality to help with changing the links.  It
doesn't alter layout or styling - that's something you'd need to change in your theme or whatever
plugin you may be using to display the gallery.  Note: if you're using [none] to remove links from gallery
images, it may affect the styling, depending on whether your stylesheet is expecting all gallery images
 to have `<a>` tags around them, in which case you would need to modify your stylesheet to also apply
the same styles to `<img>` tags without a link around them.

= #8) I have a lightbox/carousel set up when a user clicks an image, and I would like to make the image in the lightbox/carousel window link to the custom link.  How can I accomplish this? =

Unfortunately those images are placed in those locations via your particular lightbox/carousel javascript library,
which this plugin isn't able to hook into to modify.  You would need to modify your javascript library or theme to
accomplish this, which is outside the scope of this plugin.

== Screenshots ==

1. The additional WP Gallery Custom Link fields.

== Changelog ==

= 1.12 =
* Fixed issue with links not showing when using WP_Tiles in later versions of Wordpress.
* Tested with WordPress 4.7.2

= 1.11 =
* By popular demand, added the ability to set a "rel" property on all images in a gallery (e.g. nofollow)
* Tested with WordPress 4.4

= 1.10.5 =
* Changed translation text domain from a variable to strings, because apparently a variable doesn't universally work no matter how smart it makes me feel.

= 1.10.4 =
* Updated some text domain settings to be in accordance with the translate.wordpress.org translation system.
* Polished up some of the help message styles to make them easier to read
* Tested with WordPress 4.3

= 1.10.3 =
* Added a "Do Not Change" default target option to improve performance by reducing the number of regexes to apply "_self" on every gallery item. If your theme opens all gallery items in a new window by default and you prefer to keep them in the same window, you will need to add open_all_in_same_window="true" to your gallery shortcode.

= 1.10.2 =
* Added a translation for Portuguese, courtesy of Carlos Jordão (thanks!)
* Added the U ungreedy modifier to regular expressions to attempt to resolve occasional not-easily-reproduced blank page issues
* Tested with WordPress 4.2

= 1.10.1 =
* Changed javascript to queue in wp_enqueue_scripts hook instead of the all-encompassing init.
* Added a translation for Spanish, courtesy of Andrew Kurtis of WebHostingHub (thanks!)

= 1.10.0 =
* By popular demand, added the ability to add additional css classes to each image link in the gallery.
* Added a translation for German, courtesy of Martin Stehle (thanks!)

= 1.9.0 =
* By popular demand, moved the help notes under each field into tooltips so the form won't be so tall.
* The javascript that attempts to disable lightboxes now runs in window.onload in addition to document.ready.
Hopefully this will cut down on some of the issues where lightboxes keep popping up without having to mess
with javascript dependencies at the code level. Any other window.onload function should be preserved.
* Added a javascript function detect for jQuery's off() function, since it only came into existence in 1.7.
If off() isn't defined, unbind() is called instead.
* Resolved an issue where making an image have no link occasionally resulted in all previous images in the gallery
disappearing from the display.
* Added a translation for Polish, courtesy of Przemyslaw Trawicki (thanks!)

= 1.8.0 =
* By popular demand, added a new filter on each link value: wpgcl_filter_raw_gallery_link_url

= 1.7.1 =
* A few performance increases

= 1.7.0 =
* By popular demand, added support for the "open_all_in_new_window" and "open_all_in_same_window"
gallery shortcode attributes to set all images in a gallery to open in a new/the same window, respectively.
* By popular demand, made it so "Same Window" will set the target to "_self", thus
forcing the same window, instead of doing whatever the theme does by default.

= 1.6.1 =
* Fixed an issue where items with the same custom link were not having lightbox
removed properly
* Added support for the "ids" attribute added in WP 3.5
* Updated help text for the Gallery Link URL field

= 1.6.0 =
* By popular demand, added the ability to remove links from individual images
or an entire gallery.

= 1.5.1 =
* Fixed a possible error with an undefined "preserve_click" variable.

= 1.5.0 =
* By popular demand, added support for Jetpack tiled galleries (and its use
of the Photon CDN for URLs).

= 1.4.0 =
* By popular demand, added an option to remove or keep Lightbox and other OnClick
events ("remove" by default).
* Added support for the "preserve_click_events" gallery shortcode attribute to
set all custom-linked images in a gallery to "preserve" its OnClick events.

= 1.3.0 =
* Added support for the "ignore_gallery_link_urls" gallery shortcode attribute to
ignore custom links on a gallery and use the normal file/attachment setting.
* Added support for IDs in the "include" gallery shortcode attribute that aren't
directly attached to the post.

= 1.2.2 =
* Moved javascript to a separate file so jquery could be required as a dependency.

= 1.2.1 =
* Fixed a bug where javascript hover effects were not working properly on images.

= 1.2.0 =
* By popular demand, added an option to open gallery image links in a new window.

= 1.1.2 =
* Added a check to prevent javascript from showing up in feeds.

= 1.1.1 =
* Fixed an error that occurred when an images were small enough to only have one size
* Tested with WordPress 3.4

= 1.1.0 =
* Added support for replacing links to all sizes of an uploaded image instead of the full version only
* Replaced lightbox removal with a more advanced javascript method

= 1.0.5 =
* Moving the $post_id code above first_call to avoid messing that up if a return does occur due to a missing post_id

= 1.0.4 =
* The "id" attribute of the gallery shortcode is now supported

= 1.0.3 =
* Added a check to return a simple space in the event $post is undefined

= 1.0.2 =
* Fixed an issue with two undefined variables

= 1.0.1 =
* Changed priority on post_gallery filter from 10 to 999 to help ensure it runs after anything else

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.11 =
* By popular demand, added the ability to set a "rel" property on all images in a gallery (e.g. nofollow)
* Tested with WordPress 4.4

= 1.10.5 =
* Changed translation text domain from a variable to strings, because apparently a variable doesn't universally work no matter how smart it makes me feel.

= 1.10.4 =
* Updated some text domain settings to be in accordance with the translate.wordpress.org translation system.
* Polished up some of the help message styles to make them easier to read
* Tested with WordPress 4.3

= 1.10.3 =
* Added a "Do Not Change" default target option to improve performance by reducing the number of regexes to apply "_self" on every gallery item. If your theme opens all gallery items in a new window by default and you prefer to keep them in the same window, you will need to add open_all_in_same_window="true" to your gallery shortcode.

= 1.10.2 =
* Added a translation for Portuguese, courtesy of Carlos Jordão (thanks!)
* Added the U ungreedy modifier to regular expressions to attempt to resolve occasional not-easily-reproduced blank page issues
* Tested with WordPress 4.2

= 1.10.1 =
* Changed javascript to queue in wp_enqueue_scripts hook instead of the all-encompassing init.
* Added a translation for Spanish, courtesy of Andrew Kurtis of WebHostingHub (thanks!)

= 1.10.0 =
* By popular demand, added the ability to add additional css classes to each image link in the gallery.
* Added a translation for German, courtesy of Martin Stehle (thanks!)

= 1.9.0 =
* By popular demand, moved the help notes under each field into tooltips so the form won't be so tall.
* The javascript that attempts to disable lightboxes now runs in window.onload in addition to document.ready.
Hopefully this will cut down on some of the issues where lightboxes keep popping up without having to mess
with javascript dependencies at the code level. Any other window.onload function should be preserved.
* Added a javascript function detect for jQuery's off() function, since it only came into existence in 1.7.
If off() isn't defined, unbind() is called instead.
* Resolved an issue where making an image have no link occasionally resulted in all previous images in the gallery
disappearing from the display.
* Added a translation for Polish, courtesy of Przemyslaw Trawicki (thanks!)

= 1.8.0 =
* By popular demand, added a new filter on each link value: wpgcl_filter_raw_gallery_link_url

= 1.7.1 =
* A few performance increases

= 1.7.0 =
* By popular demand, added support for the "open_all_in_new_window" and "open_all_in_same_window"
gallery shortcode attributes to set all images in a gallery to open in a new/the same window, respectively.
* By popular demand, made it so "Same Window" will set the target to "_self", thus
forcing the same window, instead of doing whatever the theme does by default.

= 1.6.1 =
* Fixed an issue where multiple items with the same custom links were not having lightbox
removed properly
* Added support for the "ids" attribute added in WP 3.5
* Updated help text for the Gallery Link URL field

= 1.6.0 =
* By popular demand, added the ability to remove links from individual images
or an entire gallery.

= 1.5.1 =
* Fixed a possible error with an undefined "preserve_click" variable.

= 1.5.0 =
* By popular demand, added support for Jetpack tiled galleries (and its use
of the Photon CDN for URLs).

= 1.4.0 =
* By popular demand, added an option to remove or keep Lightbox and other OnClick
events ("remove" by default).
* Added support for the "preserve_click_events" gallery shortcode attribute to
set all custom-linked images in a gallery to "preserve" its OnClick events.

= 1.3.0 =
* Added support for the "ignore_gallery_link_urls" gallery shortcode attribute to
ignore custom links on a gallery and use the normal file/attachment setting.
* Added support for IDs in the "include" gallery shortcode attribute that aren't
directly attached to the post.

= 1.2.2 =
* Moved javascript to a separate file so jquery could be required as a dependency.

= 1.2.1 =
* Fixed a bug where javascript hover effects were not working properly on images.

= 1.2.0 =
* By popular demand, added an option to open gallery image links in a new window.

= 1.1.2 =
* Added a check to prevent javascript from showing up in feeds.

= 1.1.1 =
* Fixed an error that occurred when an images were small enough to only have one size
* Tested with WordPress 3.4

= 1.1.0 =
* Added support for replacing links to all sizes of an uploaded image instead of the full version only
* Replaced lightbox removal with a more advanced javascript method

= 1.0.5 =
* Moving the $post_id code above first_call to avoid messing that up if a return does occur due to a missing post_id

= 1.0.4 =
* The "id" attribute of the gallery shortcode is now supported

= 1.0.3 =
* Added a check to return a simple space in the event $post is undefined

= 1.0.2 =
* Fixed an issue with two undefined variables

= 1.0.1 =
* Changed priority on post_gallery filter from 10 to 999 to help ensure it runs after anything else

= 1.0.0 =
* Initial release
