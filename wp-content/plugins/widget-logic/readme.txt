=== Widget Logic ===
Contributors: wpchefgadget, alanft
Donate link: http://www.justgiving.com/widgetlogic_cancerresearchuk
Tags: widget, admin, conditional tags, filter, context
Requires at least: 3.0
Tested up to: 4.9
Stable tag: 5.9.0
License: GPLv2 or later

Widget Logic lets you control on which pages widgets appear using WP's conditional tags. It also adds a 'widget_content' filter.

== Description ==
This plugin gives every widget an extra control field called "Widget logic" that lets you control the pages that the widget will appear on. The text field lets you use WP's [Conditional Tags](http://codex.wordpress.org/Conditional_Tags), or any general PHP code.

PLEASE NOTE The widget logic you introduce is EVAL'd directly. Anyone who has access to edit widget appearance will have the right to add any code, including malicious and possibly destructive functions. There is an optional filter 'widget_logic_eval_override' which you can use to bypass the EVAL with your own code if needed. (See [Other Notes](other_notes/)).

There is also an option to add a wordpress 'widget_content' filter -- this lets you tweak any widget's HTML to suit your theme without editing plugins and core code.

= Donations =

If you like and use Widget Logic you could consider a small donation to Cancer Research UK. I have a [JustGiving.com donation link](http://www.justgiving.com/widgetlogic_cancerresearchuk). As of February 2017 we have raised 1,048.50 UKP.

== Installation ==

1. Upload `widget-logic.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it. The configuring and options are in the usual widget admin interface.

= Configuration =

Aside from logic against your widgets, there are three options added to the foot of the widget admin page (see screenshots).

* Add 'widget_content' filter -- This allows you to modify the text output in all widgets. You need to know how to write a WP filter, though some basics are covered in [Other Notes](../other_notes/).

* Use 'wp_reset_query' fix -- Many features of WP, as well as the many themes and plugins out there, can mess with the conditional tags, such that is_home is NOT true on the home page. This can often be fixed with a quick wp_reset_query() statement just before the widgets are called, and this option puts that in for you rather than having to resort to code editing

* Load logic -- This option allows you to set the point in the page load at which your widget logic if first checked. Pre v.50 it was when the 'wp_head' trigger happened, ie during the creation of the HTML's HEAD block. Many themes didn't call wp_head, which was a problem. From v.50 it happens, by default, as early as possible, which is as soon as the plugin loads. You can now specify these 'late load' points (in chronological order):
	* after the theme loads (after_setup_theme trigger)
	* when all PHP loaded (wp_loaded trigger)
	* after query variables set (parse_query) – this is the default
	* during page header (wp_head trigger)

	You may need to delay the load if your logic depends on functions defined, eg in the theme functions.php file. Conversely you may want the load early so that the widget count is calculated correctly, eg to show an alternative layour or content when a sidebar has no widgets.

*  Don't cache widget logic results -- From v .58 the widget logic code should only execute once, but that might cause unexpected results with some themes, so this option is here to turn that behaviour off. (The truth/false of the code will be evaluated every time the sidebars_widgets filter is called.

== Frequently Asked Questions ==

= I upgraded to Version 5.7.0 and my site's widgets now behave differently =

There was an important change to how your Widget Logic code is evaluated. There is a new default 'Load logic' point of 'after query variables set'. For most people this should be better, but you could try reverting to the old default 'when plugin starts'.

= What can I try if it's not working? =

* Switch to the default theme. If the problem goes away, your theme may be interfering with the WP conditional tags or how widgets work
* Try the `wp_reset_query` option. If your theme performs custom queries before calling the dynamic sidebar this might help.
* Try a different 'Load logic' point. Most wordpress conditional tags only work 'after query variables set', but some plugins may require evaluation earlier or later.
* The 'Evaluate widget logic more than once' option may be needed if you have to use an early 'Load logic' point.


= I'm getting errors that read like "PHP Parse error… … eval()'d code on line 1" =

You have a PHP syntax error in one of your widget's Widget Logic fields. Review them for errors. You might find it easiest to check by using 'Export options' and reading the code there (Though be aware that single and double quotes are escaped with multiple backslash characters.)

If you are having trouble finding the syntax error, a simple troubleshooting method is to use 'Export options' to keep a copy and then blank each Widget Logic field in turn until the problem goes. Once you've identified the problematic code, you can restore the rest with 'Import options'.

= It's causing problems with Woo Commerce / other popular plugin =

This is often, not always, fixed by trying the different 'Load Logic' options. The 'after query variables set' option looks like it might be a better default, try it.

= What's this stuff in my sidebar when there are no widgets? =

Since v .50 the widget logic code runs such that when dynamic_sidebar is called in a theme's code it will 'return false' if no widgets are present. In such cases many themes are coded to put in some default sidebar text in place of widgets, which is what you are seeing.

Your options, if you want this default sidebar content gone, are to either edit the theme, or as a work around, add an empty text widget (no title, no content) to the end of the sidebar's widget list.

= How do I get widget X on just my 'home' page? (Or on every page except that.) =

There is some confusion between the [Main Page and the front page](http://codex.wordpress.org/Conditional_Tags#The_Main_Page). If you want a widget on your 'front page' whether that is a static page or a set of posts, use is_front_page(). If it is a page using is_page(x) does not work. If your 'front page' is a page and not a series of posts, you can still use is_home() to get widgets on that main posts page (as defined in Admin > Settings > Reading).

= Logic using is_page() doesn't work =

I believe this is fixed in 5.7.0. Let me know if that is not the case.

If your theme calls the sidebar after the loop you should find that the wp_reset_query option fixes things. This problem is explained on the [is_page codex page](http://codex.wordpress.org/Function_Reference/is_page#Cannot_Be_Used_Inside_The_Loop).

= How do I get a widget to appear both on a category page and on single posts within that category? =
Take care with your conditional tags. There is both an `in_category` and `is_category` tag. One is used to tell if the 'current' post is IN a category, and the other is used to tell if the page showing IS for that category (same goes for tags etc). What you want is the case when:

`(this page IS category X) OR (this is a single post AND this post is IN category X)`
which in proper PHP is:

`is_category(X) || (is_single() && in_category(X))`


= How do I get a widget to appear when X, Y and Z? =
Have a go at it yourself first. Check out the 'Writing Logic Code' section under [Other Notes](../other_notes/).

= Why is Widget Logic so unfriendly, you have to be a code demon to use it? =
This is sort of deliberate. I originally wrote it to be as flexible as possible with the thought of writing a drag'n'drop UI at some point. I never got round to it, because (I'm lazy and) I couldn't make it both look nice and let you fall back to 'pure code' (for the possibilities harder to cater for in a UI).

The plugin [Widget Context](http://wordpress.org/extend/plugins/widget-context/) presents a nice UI and has a neat 'URL matching' function too.

= Widgets appear when they shouldn't =

It might be that your theme performs custom queries before calling the sidebar. Try the `wp_reset_query` option.

Alternatively you may have not defined your logic tightly enough. For example when the sidebar is being processed, in_category('cheese') will be true if the last post on an archive page is in the 'cheese' category.

Tighten up your definitions with PHPs 'logical AND' &&, for example:

`is_single() && in_category('cheese')`


== Screenshots ==

1. The 'Widget logic' field at work in standard widgets.
2. The plugin options are at the foot of the usual widget admin page… `widget_content` filter, `wp_reset_query` option, 'load logic point' and 'evaluate more than once'. You can also export and import your site's WL options as a plain text file for a quick backup/restore and to help troubleshoot issues.

== Changelog ==

= 5.9.0 =

wp_reset_query works better under certain conditions.

= 5.8.2 =
The code has been adapted to work on the servers with restricted <?=

Fixed support for the wp_register_sidebar_widget widgets.

Some content was prepared for translation.

= 5.8.1 =
Fixed the issue of displaying errors under certain conditions.

= 5.8.0 =
Added full support for WP customizer.

In case of a fatal error in logic, the widget will not be displayed.

= 5.7.4 =
Fixed the "Warning: Attempt to assign property of non-object" bug.
https://wordpress.org/support/topic/latest-update-seems-break-my-installation/

= 5.7.3 =
Fixed the issue when in some cases the plugin displayed user logic errors in the Widgets section and this didn't allow to save the widgets.
https://wordpress.org/support/topic/an-error-has-occurred-please-reload-the-page-and-try-again-3/

Fixed ini_set() related warnings for some rare hosting configurations.
https://wordpress.org/support/topic/ini_set-diabled-warning/

= 5.7.2 =
Removed conflicts with outdated WP versions.

= 5.7.1 =
Fixed the settings form not being saved settings under some circumstances.

Added a setting to show logic code errors for admins (turned off by default).

Fixed the issue with quotes in error messages on some WP installations.

= 5.7.0 =
Fixed PHP 7 compatibility issue.

Fixed a conflict with the latest WPML plugin.

A new default load logic point attached to the action 'parse_query'. By default the widget logic is only evaluated once.

Translation added: Ukrainian by Roman Sulym

= 0.57 =
Small fixes to satisfy some define('WP_DEBUG', true) errors

= 0.56 =
Small fix to the original WP3.5 fix in 0.54 that had the side effect of failing to save logic text on newly added widgets.

= 0.55 =
Restored a striplashes that vanished in 0.54 causing much grief.

Translation: Spanish by Eduardo Larequi http://wordpress.org/support/profile/elarequi

= 0.54 =
Removed a WP 3.1+ function call, hopefully making it 2.8 compatible again.

A little 'trim' of WL code to stop "syntax error, unexpected ')'" errors, which could occur if your WL was just a single space. Thanks to https://twitter.com/chrisjean for pointing this out.

Translation support! Thanks to Foe Services Labs http://wordpress.org/support/profile/cfoellmann for the work on this and the German Social Translation

Added a 'widget_logic_eval_override' filter. This allows advanced users to bypass EVAL with a function of their own.

= 0.53 =
Accidentally released code with a terrible bug in it :-(

= 0.52 =
Two new features: optional delayed loading of logic (see Configuration under [Installation](../installation/)), and the ability to save out and reload all your site's widget logic into a config file

= 0.51 =
One important bug fix (fairly major and fairly stupid of me too)

= 0.50 =
For the first time since this started on WP 2.3, I've rewritten how the core widget logic function works, so there may be 'bumps ahead'.

It now uses the 'sidebars_widgets' filter (as it should have done when that was
introduced in WP2.8 by the look of it). The upshot is that is_active_sidebar should behave properly.

Widget callbacks only get intercepted if the 'widget_content' filter is activated, and much more briefly. (A widget's 'callback' is rewired within the 'dynamic_sidebar' function just before the widget is called, by the 'dynamic_sidebar_param' filter, and is restored when the callback function is invoked.)

= 0.48 =
Kill some poor coding practices that throws debug notices - thanks to John James Jacoby.

= 0.47 =
FINALLY tracked down the elusive 'wp_reset_query' option resetting bug.

= 0.46 =
Fix to work with new WP2.8 admin ajax. With bonus fixes.

= 0.44 =
Officially works with 2.7 now. Documentation changes and minor bug fixes.

= 0.43 =
simple bug fix (form data was being lost when 'Cancel'ing widgets)

= 0.42 =
WP 2.5+ only now. WP's widget admin has changed so much and I was getting tied up in knots trying to make it work with them both.

= 0.4 =
Brings WP 2.5 compatibility. I am trying to make it back compatible. If you have trouble using WL with WP 2.1--2.3 let me know the issue. Thanks to Kjetil Flekkoy for reporting and helping to diagnose errors in this version

= 0.31 =
Last WP 2.3 only version

== Upgrade Notice ==

= 0.58 =
Important change to the default of when your Widget Logic is evaluated. It is now on the "parse_query" action, and just once. Those defaults can be overridden.

= 0.46 =
Required with WP2.8 cos of changes in Widget admin AJAX

= 0.44 =
Updated for WP2.7 with extra bug fixes


== Writing Logic Code ==

The text in the 'Widget logic' field can be full PHP code and should return 'true' when you need the widget to appear. If there is no 'return' in the text, an implicit 'return' is added to the start and a ';' is added on the end. (This is just to make single statements like is_home() more convenient.)

= The Basics =
Make good use of [WP's own conditional tags](http://codex.wordpress.org/Conditional_Tags). You can vary and combine code using:

* `!` (NOT) to **reverse** the logic, eg `!is_home()` is TRUE when this is NOT the home page.
* `||` (OR) to **combine** conditions. `X OR Y` is TRUE when either X is true or Y is true.
* `&&` (AND) to make conditions **more specific**. `X AND Y` is TRUE when both X is true and Y is true.

There are lots of great code examples on the WP forums, and on WP sites across the net. But the WP Codex is also full of good examples to adapt, such as [Test if post is in a descendent category](http://codex.wordpress.org/Template_Tags/in_category#Testing_if_a_post_is_in_a_descendant_category).

= Examples =

*	`is_home()` -- just the main blog page
*	`!is_page('about')` -- everywhere EXCEPT this specific WP 'page'
*	`!is_user_logged_in()` -- shown when a user is not logged in
*	`is_category(array(5,9,10,11))` -- category page of one of the given category IDs
*	`is_single() && in_category('baked-goods')` -- single post that's in the category with this slug
*	`current_user_can('level_10')` -- admin only widget
* 	`strpos($_SERVER['HTTP_REFERER'], "google.com")!=false` -- widget to show when clicked through from a google search
*	`is_category() && in_array($cat, get_term_children( 5, 'category'))` -- category page that's a descendent of category 5
*	`global $post; return (in_array(77,get_post_ancestors($post)));` -- WP page that is a child of page 77
*	`global $post; return (is_page('home') || ($post->post_parent=="13"));` -- home page OR the page that's a child of page 13

Note the extra ';' on the end where there is an explicit 'return'.

== The 'widget_logic_eval_override' filter ==
Before the Widget Logic code is evaluated for each widget, the text of the Widget Logic code is passed through this filter. If the filter returns a BOOLEAN result, this is used instead to determine if the widget is visible. Return TRUE for visible.

== The 'widget_content' filter ==

When this option is active (tick the option tickbox at the foot of the widget admin page) you can modify the text displayed by ANY widget from your own theme's functions.php file. Hook into the filter with:

`add_filter('widget_content', 'your_filter_function', [priority], 2);`

where `[priority]` is the optional priority parameter for the [add_filter](http://codex.wordpress.org/Function_Reference/add_filter) function. The filter function can take a second parameter (if you provde that last parameter '2') like this:

`function your_filter_function($content='', $widget_id='')`

The second parameter ($widget_id) can be used to target specific widgets if needed.

A [Wordpress filter function](http://codex.wordpress.org/Plugin_API#Filters) 'takes as input the unmodified data, and returns modified data' which means that widget_content filters are provided with the raw HTML output by the widget, and you are then free to return something else entirely:

= Example filters =

`add_filter('widget_content', 'basic_widget_content_filter');
function basic_widget_content_filter($content='')
{	return $content."<PRE>THIS APPEARS AFTER EVERY WIDGET</PRE>";
}`

I was motivated to make this filter in order to render all widget titles with the excellent [ttftitles plugin](http://templature.com/2007/10/18/ttftitles-wordpress-plugin/) like this:

`add_filter('widget_content', 'ttftext_widget_title');
function ttftext_widget_title($content='')
{	preg_match("/<h2[^>]*>([^<]+)/",$content, $matches);
	$heading=$matches[1];
	$insert_img=the_ttftext( $heading, false );
	$content=preg_replace("/(<h2[^>]*>)[^<]+/","$1$insert_img",$content,1);
	return $content;
}`

People often ask for a way to give widgets alternating styles. This filter inserts widget_style_a/widget_style_b into the class="widget ..." text usually found in a widget's main definition:

`add_filter('widget_content', 'make_alternating_widget_styles');
function make_alternating_widget_styles($content='')
{	global $wl_make_alt_ws;
	$wl_make_alt_ws=($wl_make_alt_ws=="style_a")?"style_b":"style_a";
	return preg_replace('/(class="widget )/', "$1 widget_${wl_make_alt_ws} ", $content);
}`
