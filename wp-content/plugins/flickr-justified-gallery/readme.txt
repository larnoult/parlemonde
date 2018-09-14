=== Flickr Justified Gallery ===
Contributors: miro.mannino
Donate link: http://miromannino.com/
Tags: photography, gallery, photo, flickr, photostream, set, justified, grid
Requires at least: 3.0
Tested up to: 4.9.4
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt

Just your beautiful Flickr photos. In a Justified Grid.

== Description ==

Plugin that allows you to show your Flickr **photostream**, **photosets**, **galleries**, **group pools**, or **tags** in your blog, with a very elegant and awesome layout.

Create a gallery with the **same style of Flickr or Google+**! Awesome thumbnails disposition with a **justified grid**, calculated by a fast javascript algorithm called <a href="http://miromannino.github.io/Justified-Gallery" title="Justified Gallery">Justified Gallery</a>! You can **configure the height of the rows** to have a grid that can be *like the justified grid of Flickr or of Google+*. But, you can do more! For example you can *configure the margin between the images*, create rows with fixed height, or decide if you want to justify the last row or not!

You can also configure a gallery to show photos with a link to Flickr or with a **Lightbox** (Swipebox or Colorbox).

Always high quality thumbnails! The plugin chooses the **right resolution for the images**, using the "Flickr size suffixes", no small images are resized to be bigger and no big images are resized to be smaller! You can create gallery with very large thumbnails!

Remember that this plugin is not an official Flickr® plugin, any help will be greatly appreciated.

 = Features: = 

 * A gallery with the same layout of Flickr or Google+, configurable as you want.
 * Fast and light. Also uses a cache to load galleries instantly.
 * You can show photos from your Flickr photostream, from a **photoset**, from a **gallery**, or from a **group pool**.
 * You can show all the photos that has some **tags**.
 * You can create multiple galleries with different settings, also in the same page.
 * Customisable image sizes, always with a justified disposition.
 * Photo titles shown when the mouse is above.
 * Decide if use a lightbox (Colorbox or Swipebox) to show the original photo, or Flickr.
 * Customisable style, you need just to change a CSS.
 * Pagination with SEO friendly URLs. Decide if you want to show the newer photos or not.
 * Available in English and Italian

= Live Demo = 

See a Live Demo in [Miro Mannino's Blog](http://miromannino.com/my-photos)


== Installation ==

1. Upload the folder `flickr-justified-gallery` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings through the 'Settings > Flickr Justified Gallery' page.
4. Remember to set also the API key and the default User ID
5. Create a page with the shortcode `[flickr_photostream]` to show the specified user photostream (you can add attributes in the shortcode, to show set, gallery or simply to have settings that are different than the default).
6. (optional) If you want to use Colorbox, install a Colorbox plugin (i.e. [JQuery Colorbox](http://www.techotronic.de/plugins/jquery-colorbox/)). Then, check the settings the lightbox option.

== Frequently Asked Questions ==

= Can I have in the same blog two photostream of different Flickr's users?  =

Yes, use the shortcode attributes called `user_id`. For example the shortcode `[flickr_photostream user_id="67681714@N03"]` displays the photostream of the specified user, no matter what is the default user ID specified in the settings.

= Are the photos synchronised with Flickr? =

Yes, of course. But remember that the cache (and also Flickr) doesn't allow to see the changes immediately.

= Why I can’t see the last photos I uploaded? =

* Probably because they aren’t public: Flickr Justified Gallery can’t show private photos.
* Remember that Flickr Justified Gallery uses a cache to speed-up your site, and the new photos are not immediately available, but only after few hours.

= API Key is not valid? =

If you are sure that the API key is valid, there could be many other reasons, related to communication
problems with Flickr.

* Try an alternative version of phpFlickr, choosing it from the settings page
* Be sure that CURL is installed in your server
* Verify that the CURL certificate is properly installed in your server to communicate via https with CURL
* Be sure that your server can perform CURL calls to Flickr (the server may be configured to work only )
* Update CURL to the latest version

= The web service endpoint returned a "HTTP/1.1 403 Forbidden" response =

This question is another effect of the same problems related to the previous question

* Be sure that CURL is installed in your server
* Be sure that **php5-curl** is installed and active in php.ini

= Why don't Colorbox or Swipebox work? =

You have to provide Colorbox or Swipebox with other plugins. For example:

* [Responsive Lightbox](https://wordpress.org/plugins/responsive-lightbox/)
* [jQuery Colorbox](http://wordpress.org/extend/plugins/jquery-colorbox/)
* [Lightbox Plus Colorbox](http://wordpress.org/extend/plugins/lightbox-plus/)

= Why aren't the set, gallery or photostream ordered as I would? =

The photos are showed in the same order they are returned via Flickr. Verify the order using the
official Flickr's API page:

* Photostream: [flickr.people.getPublicPhotos](https://www.flickr.com/services/api/flickr.people.getPublicPhotos.html)
* Sets: [flickr.photosets.getPhotos](https://www.flickr.com/services/api/flickr.photosets.getPhotos.html)
* Galleries: [flickr.galleries.getPhotos](https://www.flickr.com/services/api/flickr.galleries.getPhotos.html)
* Tags: [flickr.photos.search](https://www.flickr.com/services/api/flickr.photos.search.html)
* Groups: [flickr.groups.pools.getPhotos](https://www.flickr.com/services/api/flickr.groups.pools.getPhotos.html)

= The images are opened in the Lightbox and immediately the page is redirected =

If you have installed WP Slimstat, you need to exclude the images of the gallery to the WP Slimstat settings, filtering the class `jg-img`.

== Screenshots ==

1. A typical photostream
2. A photostream with more pages
3. The captions


== Changelog ==

= 3.5 =

* Changed the way scripts are added to the page using the standard Wordpress functions wp_enqueue_style and wp_add_inline_script. This solves errors in case jquery is added at the footer, unless another plugin (or the theme) adds it again in a wrong way. 

= 3.4.2 = 

* Fixed problems with the new versions of jQuery and Swipebox
* Updated Swipebox to 1.4.4
* Updated Colorbox to 1.6.4

= 3.4.1 = 

* Fixed error when the right click is blocked
* Fixed error with Colorbox that showed "undefined"
* Colorbox updated to 1.6.3
* Swipebox updated to 1.4.1

= 3.4.0 =

* Scripts on bottom (for performance and compatibility)
* Settings help update
* Justified Gallery 3.6.0
* Fixed XSS security vulnerability

= 3.3.6 =

* Fixed the provide Colorbox/Swipbox bugs in settings

= 3.3.4 = 

* Fixed links for sets
* Fixed problems with settings that may generate some errors.
* Justified Gallery 3.5.4

= 3.3.2 = 

* Fixed problems with lightboxes
* Fixed problems with large thumbnails for user that uploaded in Flickr high quality images.

= 3.3 = 

* Possibility to have descriptions
* Justified Gallery 3.5.1
* Fixed some errors in the small tutorial in the setting page

= 3.2.4 =

* Possibility to provide the lightbox libraries
* Fixed some problems with lightboxes

= 3.2 =

* Justified Gallery 3.5
* Removed Swipebox to allow users to use the provided version
* Prevent that multiple jQuery inclusions broke the gallery
* Possibility to choose alternative wrappers for the Flickr's API in case of problems
* Name changed to avoid 

= 3.1.6 =

* Try to fix errors in phpFlickr

= 3.1.5 = 

* Now is possible to use the `tags` option in the [flickr_group]
* Fixed the errors with WP_DEBUG enabled in Wordpress
* Improved the option that disables the context menu
* Revert phpFlickr to the nextend version put in 3.1.2
* Spanish translation

= 3.1.4 = 

* new option to disable the right click 
* phpFlickr 3.1.1

= 3.1.2 =

* nextend version of phpFlickr to solve the keys problems 
* Fixed 'No photos' with tags
* Fixed links to Flickr for sets
* Justified gallery 3.2

= 3.0 =

* Justified gallery 3.0
	* Less images crops 
	* Faster load (rows appear when are ready)
	* No more white spaces when the images are loading
* Randomize order
* Capability to show the original images in the lightbox

= 2.3.2 =

* pagination style workaround for themes that use 'pre' tags
* workaround for a swipe box bug, when there are more than one justified gallery in one page
* pagination settings error (changed the behaviour for those people that founded usage problems)
* changed the available size behavior. Some Flickr images is very very huge! Now it try to show the large size image in the lightbox, it this is not available try to show the original image, and if this is not available show the medium size. Unfortunately, Flickr doesn't store very large sizes (only the original). 
* fixed some bugs with tags

= 2.2 =

* removed the setting 'use large thumbnails': founded a way to determine it automatically
* fixed the links with original photos

= 2.1 = 

* now it works with Photon.
* fixed errors for those that don't have large image in flickr (added the 'use large thumbnails' option).
* now the links display the original pictures and not the large ones, this improve the quality and the compatibility.

= 2.0 =

* Group pools
* Tags
* New shortcodes, due to the number of functionalities: photostream, group, tags, set, and galleries.
* Now one can use the Colorbox or the Swipebox lightbox
* Standard Wordpress pagination
* Pagination with prev and next links, or with page numbers
* Performance improvements, reduced the numbers of calls to the Flickr server.
* Justified Gallery updated to version 2.0

= 1.6 =

* Sets 
* Galleries
* Some performance improvements
* New settings UI style
* Some bugs fixed, thanks to nammourdesigns.
* New error detection system, now it's easier to find the wrong settings
* pagination settings has been changed, to be more understandable
* Justified Gallery updated to version 1.0.4

= 1.5 =
* updated Justified Gallery to version 1.0.2

= 1.4 =
* Now the plugin uses the [Justified Gallery](http://miromannino.github.io/Justified-Gallery) JQuery plugin to build the justified layout.
* Corrected some bugs in the default settings

= 1.3 =
* Algorithm improved, faster and now Internet Explorer compatible
* Added captions
* Now, you can add multiple instance on the same page
* Now, the CSS rules force the scrollbar to be always visible, this to prevent loops
* Fixed some errors
* Usability improved

= 1.2 =
* Deleted the custom Lightbox. Now, to use a lightbox, you need to use a plugin that enable colorbox.
* Added error message if the plugin doesn't find a plugin that enable colorbox.
* Added a loading phase to show the images directly in a justified grid.
* The images fade-in only when they are completely loaded.
* Simplified the settings page.
* Fixed an issue of the "IE8 or lower error message" in case of multiple gallery per page.

= 1.1 =
* Optional Lightbox
* Option to use or not the pages
* Support for multiple gallery instances
* All options is now "default options", every instance can have different options
* Now, you can have different instances that show different user photostreams

= 1.0.1 =
* Justified grid algorithm disabled for IE8 or lower
* Error message for IE8 or lower
* Fixed some css issues
* Speed improvements to the images loading

= 1.0 =
* First version


== Upgrade Notice ==

= 3.3 =

* Descriptions
* Justified Gallery 3.5.1

= 3.2.1 =

* Possibility to provide the lightbox libraries
* Fixed some problems with colorbox

= 1.0 =
* First version.