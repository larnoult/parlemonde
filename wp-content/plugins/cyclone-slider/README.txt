=== Cyclone Slider ===
Contributors: kosinix
Donate link: http://www.codefleet.net/donate/
Tags: slider, slideshow, drag-and-drop, wordpress-slider, wordpress-slideshow, cycle 2, jquery, responsive, translation-ready, custom-post, cyclone-slider
Requires at least: 3.5
Tested up to: 4.8
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

An easy-to-use and customizable slideshow plugin. For both casual users and expert developers.

== Description ==

Cyclone Slider is an easy-to-use slideshow plugin with an intuitive user interface. It's simple yet extensible.

= Why Use It? =
* Simplified workflow: 1.) Add slides 2.) Set slideshow properties 3.) Choose a template 4.) Publish! You can choose between a shortcode or a php function (for themes) to display.
* Supports 5 different slide types: image, YouTube, Vimeo, custom HTML, and testimonial slides.
* Translation ready and RTL support. Ideal for languages other than English.
* Comes with 4 core templates: Dark, Default, Standard and Thumbnails.
* Advance template system. Not happy with the core templates? The template system allows developers to customize the slideshow appearance and behavior. Perfect for every client projects. [More info on templating here](http://docs.codefleet.net/cyclone-slider/templating/).
* Selective loading. Load only the scripts and styles that you need.
* Import/export selected slideshows. Moving your slideshow to a different website? No problemo.
* It's FREE! Cyclone Slider does not impose the pro version to free users.

= More Features =
* Ability to add per-slide transition effects.
* Customizable tile transition effects.
* Unlimited sliders.
* Unique settings for each slider.
* Supports random slide order.
* Shortcode for displaying sliders anywhere in your site.
* Ability to import images from NextGEN (NextGEN must be installed and active).
* Ability to use qTranslate quick tags for slide title and descriptions (qTranslate must be installed and active).
* Allows title and alt to be specified for each slide images.
* Comes with a widget to display your slider easily in widget areas.
* Ability to fine tune the script settings. You can choose what scripts to load and where to load them.

= Cyclone Slider Pro =

Cyclone Slider Pro offers even more features:

* Allow wrap. Slideshow wraps to beginning slide if it reaches the end slide.
* Dynamic height. For slides with varying height.
* Delay. Delay start of slideshow
* Easing. Some cool transition effects.
* Swipe. Swipe gesture support for touch devices.
* 6 resize options: Fit, Fill, Crop, Exact, Exact Width, Exact Height
* Ability to change the image quality: Low, Medium, High, Very High, Max
* And additional templates: Text, Galleria, Yelp and Twitter


= Demos =
* View some [screenshots](http://wordpress.org/plugins/cyclone-slider/screenshots/).
* Checkout the [Cyclone Slider homepage](https://www.codefleet.net/cyclone-slider/) for a live demo.

= Credits =
* Cyclone Slider was based on [Cycle 2](http://jquery.malsup.com/cycle2/) by [Mike Alsup](http://jquery.malsup.com/).

= Translation Credits =
* Aubin BERTHE for the French translation.
* maxgx for the Italian translation.
* [Hassan](http://wordpress.org/support/profile/hassanhamm) for the Arabic translation.
* Javad for the Persian translation.
* [Borisa Djuraskovic](http://www.webhostinghub.com/) for the Serbo-Croatian translation.
* [Gabriel Gil](http://gabrielgil.es/) and [Digital03](http://digital03.net/) for the Spanish translation.

Do you want to translate Cyclone Slider manually into your language? Check this [tutorial](http://docs.codefleet.net/cyclone-slider/translation/).

= License =
GPLv3 - http://www.gnu.org/licenses/gpl-3.0.html

== Installation ==

= Install via WordPress Admin =
1. Ready the zip file of the plugin
1. Go to Admin > Plugins > Add New
1. On the upper portion click the Upload link
1. Using the file upload field, upload the plugin zip file here and activate the plugin

= Install via FTP =
1. First unzip the plugin file
1. Using FTP go to your server's wp-content/plugins directory
1. Upload the unzipped plugin here
1. Once finished login into your WP Admin and go to Admin > Plugins
1. Look for Cyclone Slider and activate it

= Usage =
1. Start adding sliders in 'Cyclone Slider' menu in WordPress
1. The shortcodes and php code are generated automatically by the plugin. Just copy and paste it.


== Frequently Asked Questions ==

= Why is my slider not working? =
Check for javascript errors in your page. This is the most common cause of the slider not running. See [diagnosing javascript errors](http://codex.wordpress.org/Using_Your_Browser_to_Diagnose_JavaScript_Errors). Fix the javascript errors and the slider will run.

Also check if you are using jQuery Cycle 1 script by viewing your page source. jQuery Cycle2 won't work if both are present.

= How do I pause an auto running slider when I play a YouTube or Vimeo video? =
Sorry but its not currently supported as it requires loading the YouTube API which is an extra overhead. A solution would be to disable auto transition.

= Why is there is an extra slide that I didn't add? = 
Most probably its wordpress adding paragpraphs on line breaks next to the slides therefore adding a blank `<p>` slide. You can try adding this to functions.php:
`remove_filter('the_content', 'wpautop');`

= Where do I add my own templates? =
See: [http://docs.codefleet.net/cyclone-slider/creating-your-own-template/](http://docs.codefleet.net/cyclone-slider/creating-your-own-template/)

== Screenshots ==

1. All Slideshow Screen
2. Slideshow Editing Screen
3. Slideshow in Action
4. Slideshow Widget
5. Slideshow Settings

== Changelog ==

= 3.2.0 - 2017-07-04 =
* Fix issue with font awesome not loading when used by other plugins.
* Used SVG for icons.

= 3.1.3 - 2017-06-12 =
* Fix language files not loaded.
* Updated Japanese language files.

= 3.1.2 - 2017-05-04 =
* Fix "Slideshow not found" error when using numeric slideshow slugs. Eg. "011".

= 3.1.1 - 2017-04-29 =
* Fix slide type image edit area not showing when adding slide for the first time.

= 3.1.0 - 2017-04-28 =
* Add image to testimonial slide type. Make it work on Dark and Standard template.
* Fix dark template.
* Fix standard template.
* Fix vimeo slide type. 
* Use the new vimeo player js library.
* Add templates Twitter and Yelp reviews to [Pro](https://www.codefleet.net/cyclone-slider/templates/).

= 3.0.0 - 2017-04-12 =
* Codebase merge with Cyclone Slider 2. See [detailed post](https://www.codefleet.net/cyclone-slider-reborn/).
* Add "Legacy Mode" to restore full Cyclone Slider 1 functionality if needed.
* Add Slider Properties pane. You can now set per-slide settings on all slide types. Before it was only available for Image slides.
* Move Hidden option to Slider Properties pane.
* Sorting can now be toggled to prevent accidentally dragging slides.
* Add minimized/maximized button for it to be more obvious vs just toggling with the slide header.
* Slicker slide types dropdown.
* Cosmetic changes to Templates and Slide boxes.
* Update Cycle2 scripts to latest version.

= 1.3.4 - 2013-09-20 =
* Can now use WP 3.5 media gallery when using WP 3.5 or greater. Older WP versions will use the old media gallery.
* Clicking slide box title will now expand/collapse the box.
* Slide box can now be drag anywhere in the title area.

= 1.3.3 - 2012-12-18 = 
* Bug fix. Preserve PNG transparency on resize.

= 1.3.2 - 2012-12-11 = 
* Added gettext calls for qtranslate to work in title and description fields. You can now place [:en]English Text[:de]German Text in these fields.

= 1.3.1 - 2012-12-03 = 
* Removed width and height attributes from slide images in responsive template

= 1.3.0 - 2012-11-28 =
Code now based from Cyclone Slider 2 codebase. The improvements are:

* Cleaner and faster user interface that works well even in IE7
* Ability to import images from NextGEN
* Option to pause slide on hover
* Option to open slide links in new tab
* Improved function cycloneslider_thumb
* Improved codes in templates. Please check your custom templates to match the changes in the template system. Old templates will still work but may not benefit from the newly added options
* Renamed jquery.cookie.js to jquery-cookie.js to prevent the bug in some servers where a file named jquery.cookie.js is blocked and not loaded

= 1.2.2 - 2012-10-05 = 
* Fix error for CSS not loading for WPMU when there is already GET var in the url. Eg. http://www.url.com?lang=en

= 1.2.1 - 2012-09-25 = 
* Added check for undefined jquery cookie plugin

= 1.2.0 - 2012-09-05 = 
* Template selection via admin
* Child theme support. You can now add the `cycloneslider` templates folder inside a child theme. tnx Geoff
* Bug fix for template url/path missing a slash. tnx Chris
* German translation
* Remove unwanted whitespaces on templates at runtime to remove unwanted `<p>` tags from being added by wp

= 1.1.1 - 2012-09-02 = 
* Fix bug on function cycloneslider_thumb
* Added improved thumbnails template

= 1.1.0 - 2012-08-31 = 
* New templates
* New and improved template system

= 1.0.6 - 2012-08-26 = 
* Bug fix for titles and descriptions out of sync after deleting a slide

= 1.0.5 - 2012-08-24 = 
* Caching for thumbnails
* Autodetect "cycloneslider" folder inside current active theme 

= 1.0.4 - 2012-08-18 = 
* Added default values when adding a new slideshow to help users.
* Added visual cues when adding new slides. 
* Hide preview in admin img when its src is blank to hide the img not found on IE and other browsers. Show only when src is given.

= 1.0.3 =
* Bug fix if shortcode attributes are set to zero eg. timeout="0". Change use of empty() to === to differentiate null from integer zero or blank string

= 1.0.2 =
* Prefixed meta keys with underscore _ to hide from wp custom field metabox. Existing slider data will be silently migrated into this new meta keys.
* Thumbnail function added. 

== Upgrade Notice ==

See changelog
