=== Mark New Posts ===
Contributors: tssoft
Tags: new posts, unread posts, title, highlight, easy
Requires at least: 3.3
Tested Up To: 4.6.1
Stable tag: 6.9.28
License: MIT


== Description ==

Highlight unread WordPress posts.

Features:

 * Works right out of the box
 * 4 different types of markers for highlighting posts: an orange circle, a text label, a picture (default or custom) or a flag icon
 * A post gets marked as read...
 * after viewing the post's page
 * or after viewing the post on any page
 * or after opening any page of the blog
 * 2 functions for WordPress developers: to check if a post is unread and to get the total number of unread posts


== Frequently Asked Questions ==

= 1. How can I see that the plugin works? =

1. Install and activate the plugin.
2. Open your blog's main page.
3. Add a new post to your blog.
4. Open the main page once again. An orange circle should appear to the left of the new post's title.

= 2. The plugin is exploding my page's markup. How to fix it? =

Try to enable the option "Check page markup before displaying a marker" (plugin options, advanced settings).

= 3. What do I need the mnp_is_new_post() and mnp_new_posts_count() functions for? =

These two functions could be useful for developing WordPress themes.
~~~~
mnp_is_new_post($post);
~~~~
Returns true if specific post is unread, otherwise false.
Parameters: $post (optional) - post ID or object.
~~~~
mnp_new_posts_count($query);
~~~~
Returns the total number of unread posts.
Parameters: $query (optional) - WP_Query query string.
Example:
~~~~
echo mnp_new_posts_count('cat=1');
~~~~
This will show the number of unread posts in category with id = 1.

== Screenshots ==

1. Settings page
2. Marker type: Circle
3. Marker type: "New" text
4. Marker type: Picture (default)

== Changelog ==

= 6.9.28 =
 * New translation: Russian
 * New option: mark posts as read after opening any page of the blog
 * New option: posts stay marked as new only for a certain amount of days after publishing
 * New option: mark all existing posts as new to new visitors

= 6.9.26 =
 * Fixed notices in debug mode

= 6.9.22 =
 * mnp_new_posts_count() speed up

= 6.5.24 =
 * Fixed minor bug: 2nd argument might not be passed to the_title filter in some themes

= 6.5.12 =
 * Fixed blank screen when not running on Apache

= 6.5.10 =
 * Unicode flag marker replaced with an image (because of Unicode issues in Firefox)
icon by [Vectors Market](http://www.flaticon.com/authors/vectors-market) from [www.flaticon.com](http://www.flaticon.com), [CC BY 3.0](https://creativecommons.org/licenses/by/3.0/) license
 * Code refactoring and optimization
 * Better way of markup checking
 * Settings page redesign

= 6.5.6 =
 * Detect prefetching

= 6.5.5 =
 * New marker placement: before and after post title
 * Incorrect markup check is disabled by default to use less memory

= 6.5.4 =
 * Fixed: incorrect markup when the_title() is being called from an attribute value

= 6.3.18 =
 * "Mark posts as read only after opening" option now works for post excerpts too

= 5.6.4 =
 * New marker type: flag (unicode character)
 * New option: the marker can be placed before or after the title of a post
 * New marker type: custom image
 * Fixed bug: after opening a post's preview it's getting marked as read
 * Fixed bug: sometimes the marker falls on another line
 * Fixed: marker gets wrapped on new line in post's navigation block

= 5.5.12 =
 * i18n
 * Added "Mark post as read only after opening" option
 * New marker type: image. "Label New Blue" icon by [Jack Cai](http://www.doublejdesign.co.uk/), [CC BY-ND 3.0](https://creativecommons.org/licenses/by-nd/3.0/) license

= 5.5.8 =
 * This plugin is based upon [KB New Posts 0.1](http://adambrown.info/b/widgets/tag/kb-new-posts/) by [Adam R. Brown](http://adambrown.info/)
 * New functions for using in WordPress themes: *mnp_is_new_post* and *mnp_new_posts_count*
 * 2 new ways of highlighting unread posts