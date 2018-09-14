=== GD bbPress Attachments ===
Contributors: GDragoN
Donate link: https://plugins.dev4press.com/gd-bbpress-attachments/
Version: 3.0
Tags: dev4press, bbpress, attachments, upload, media library, forum, topic, reply, limit, meta
Requires at least: 4.4
Requires PHP: 5.5
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Implements attachments upload to the topics and replies in bbPress plugin through media library and add additional forum based controls.

== Description ==
GD bbPress Attachments is an easy to use plugin for WordPress and bbPress for implementing files upload for bbPress Forums topics and replies. You can control file sizes from the main plugin settings panel, or you can change some attachments settings for each forum individually. Currently included features:

* Attachments are handled through WordPress media library. 
* Limit the number of files to upload at once.
* Embed a list of attached files into topics and replies.
* Attachment icon in the topics list for topics with attachments.
* Attachments icons for file types in the attachments list.
* Option to control if visitors can see list of attachments.
* Display uploaded images as thumbnails.
* Control thumbnail size.
* Control thumbnail CLASS and REL attributes.
* Upload errors can be logged .
* Post uthor and administrators can see errors.
* Administration: attachments count for topics and replies.
* Administration: metabox for settings override for forums.
* Administration: metabox with attachments list and errors for topics and replies.

= bbPress Plugin Versions =
GD bbPress Attachments 3.0 supports bbPress 2.5 or newer. Older bbPress are no longer supported!

= BuddyPress Support =
GD bbPress Attachments 3.0 is tested with BuddyPress 3.0 using bbPress for Groups forums. Make sure you enable JavaScript and CSS Settings Always Include option in the plugin settings.

= More free Dev4Press plugins for bbPress =
* [GD bbPress Tools](https://wordpress.org/plugins/gd-bbpress-tools/) - various expansion tools for forums
* [GD Topic Polls](https://wordpress.org/plugins/gd-topic-polls/) - add polls to the bbPress topics

= Upgrade to GD bbPress Toolbox Pro =
Pro version contains many more great features:

* Enhanced attachments features
* Limit file types attachments uplod
* BBCodes editor toolbar
* Report topics and replies
* Say thanks to forum members
* Various SEO features
* Various privacy features
* Enable TinyMCE editor
* Private topics and replies
* Auto closing of inactive topics
* Notification email control
* Show user stats in topics and replies
* Track new and unread topics
* Great new responsive admin UI
* Setup Wizard
* Forum based settings overrides
* Improved BuddyPress support
* 40 BBCodes (including Hide and Spoiler)
* 19 Topics Views
* 8 additional widgets
* Many great tweaks
* And much, much more

With more features on the roadmap exclusively for Pro version.

* More information about [GD bbPress Toolbox Pro](https://plugins.dev4press.com/gd-bbpress-toolbox/)
* Compare [Free vs. Pro Plugin](https://plugins.dev4press.com/gd-bbpress-toolbox/articles/toolbox-pro-vs-free-plugins/)

= Premium dev4Press.com plugins for bbPress =
* [GD bbPress Toolbox Pro](https://plugins.dev4press.com/gd-bbpress-toolbox/) - collection of features for bbPress
* [GD Topic Polls Pro](https://plugins.dev4press.com/gd-topic-polls/) - add polls to the bbPress topics
* [GD Topic Prefix Pro](https://plugins.dev4press.com/gd-topic-prefix/) - add customizable bbPress topic prefixes
* [GD Content Tools Pro](https://plugins.dev4press.com/gd-content-tools/) - meta box for the topic and reply form

== Installation ==
= General Requirements =
* PHP: 5.5 or newer

= WordPress Requirements =
* WordPress: 4.4 or newer

= bbPress Requirements =
* bbPress Plugin: 2.5 or newer

= Basic Installation =
* Plugin folder in the WordPress plugins folder must be `gd-bbpress-attachments`
* Upload folder `gd-bbpress-attachments` to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Where can I configure the plugin? =
Open the Forums menu, and you will see Attachments item there. This will open a panel with global plugin settings.

= Why is Media Library required? =
All attachments uploads are handled by the WordPress Media Library, and plugin uses native WordPress upload functions. When the file is uploaded it will be available through Media Library. Consult WordPress documentation about Media Library requirements.

= Will this plugin work with standalone bbPress installation? =
No. This plugin requires the plugin versions of bbPress 2.5 or higher.

= Does this plugin work with bbPress plugin used as sitewide forums for BuddyPress plugin? =
Yes. But, make sure to enable 'Always Include' option for JavaScript and CSS.

= What are the common problems that can prevent upload to work? =
In some cases, it can happen that jQuery is not included on the page, even so, the bbPress requires it to be loaded. That can happen if something else is unloading jQuery. If the jQuery is not present, the upload will not work.
Another common issue is that WordPress Media Library upload is not working. If that is not set up, attachments upload can't work.

== Translations ==
* English
* Serbian
* Swedish: https://profiles.wordpress.org/lakrisgubben
* Russian: Pavel Kuznetsov - https://profiles.wordpress.org/pavel-kuznetsov
* German: David Decker - http://deckerweb.de/
* Slovak: Branco Radenovich - http://webhostinggeeks.com/blog/
* French: Marie Bodson - http://mariebodson.com/
* Polish:Daniel Napora - http://www.hinok.net/
* Dutch: Wouter van Vliet - http://www.interpotential.com/
* Spanish: Jhonathan Arroyo - http://www.siswer.com/
* Persian: Ramin Firooz - http://shayverd.com/
* Portuguese
* Italian

== Upgrade Notice ==
= 3.0 =
New plugin interface. New Images and Advanced settings panels. Various other updates and improvements.

== Changelog ==
= 3.0 (2018.07.26) =
* New interface for the plugin settings panel
* New panel with advanced settings
* New panel with images settings
* New support for thumbnails for PDF and SVG file types
* Updated settings form with proper field types
* Updated toolbar icon to use bbPress dashicon

= 2.6 (2018.04.27) =
* Updated plugin requirements
* Sanitize file name stored for the upload errors
* Escape the file name displayed for upload errors
* Fixed potential stored XSS vulnerability (thanks to [Luigi Gubello](https://www.gubello.me/blog/) for reporting)
* Fixed few typos and missing translation strings

= 2.5 =
* Updated JS and CSS files are by default always loaded
* Updated WordPress minimal requirement to 4.2
* Updated several broken URL's
* Updated and improved readme file

= 2.4 =
* Added download attribute to attached files links
* Updated sanitation of the plugin settings on save
* Updated PHP minimal requirement to 5.3
* Updated WordPress minimal requirement to 4.0
* Updated several broken URL's
* Updated several missing translation strings

= 2.3.2 =
* Added Swedish translation
* Updated readme file

= 2.3.1 =
* Added Russian translation
* Updated readme file

= 2.3 =
* Updated several Dev4Press links
* Fixed XSS and LFI security issues with unsanitized input
* Fixed order of displayed attachments to match upload order
* Fixed inline image alignment when there is no image caption

= 2.2 =
* Fixed problem with uploading video or audio files in some cases

= 2.1 =
* Improved default styling for the list of attachments
* Removed support for bbPress 2.2.x
* Fixed posts deletion problem caused by attachments module

= 2.0 =
* Improved default styling for the list of attachments
* Removed obsolete hooks and functions
* Removed support for bbPress 2.1.x
* Fixed method for adding some of the plugin hooks
* Fixed issue with attachments DIV not closed properly
* Fixed few typos and missing translation strings

= 1.9.2 =
* Added Slovak translation
* Changed upload field location to end of the form
* Dropped support for bbPress 2.0
* Dropped support for WordPress 3.2
* Fixed problem with saving some settings

= 1.9.1 =
* Fixed detection of bbPress 2.2
* Fixed missing function fatal error

= 1.9 =
* Added support for dynamic roles from bbPress 2.2
* Added class to attachments elements in the topic/reply forms
* Using enqueue scripts and styles to load files on frontend
* Admin menu now uses 'activate_plugins' capability by default
* Screenshots removed from plugin and added into assets directory
* Fixed problem with some themes and embedding of JavaScript
* Fixed issues with some themes and displaying attachments

= 1.8.4 =
* Additional settings information
* BuddyPress with site wide bbPress supported
* Expanded list of FAQ entries
* Panel for upgrade to GD bbPress Toolbox
* Fixed duplicated registration for reply embed filter

= 1.8.3 =
* Added Italian translation
* Updated several translations

= 1.8.2 =
* Added Portuguese translation

= 1.8.1 =
* Adding meta field to identify file as attachment
* Few minor issues with plugin settings

= 1.8 =
* Added option to display thumbnails in line
* Added Persian translation
* Improvements for the bbPress 2.1 compatibility
* Several embedding styling improvements
* Fixed some loading issues for admin side

= 1.7.6 =
* Changes to readme.txt file
* Improvements to the shared code

= 1.7.5 =
* Additional loading optimization
* Added French language
* Updated some translations
* Fixed minor issues with saving settings

= 1.7.2 =
* Missing license.txt file

= 1.7.1 =
* Added option for improved embedding of JS and CSS code
* Minor changes to the plugins admin interface panels
* Updated and expanded plugin FAQ and requirements

= 1.7 =
* Loading optimization with separate admin and front end code
* Added options for deleting and detaching attachments
* Added several new filters for additional plugin control
* Added option for error logging visibility for moderators
* Fixed logging of multiple upload errors
* Fixed several issues with displaying upload errors

= 1.6 =
* Added hide attachments from visitors option
* Added option to hook in topic and reply deletion
* Added Polish translation
* Improved adding of plugin styling and JavaScript
* Fixed visibility of meta settings for non admins

= 1.5.3 =
* Context Help for WordPress 3.3

= 1.5.2 =
* Rel attribute allows use of topic or reply ID
* Admin topic and reply editor list of errors
* Updated German and Serbian translations
* Updated readme file with error logging information

= 1.5.1 =
* Fixed logging of empty error messages

= 1.5 =
* Improved tabbed admin interface
* Image attachments display and styling
* Error logging displayed to admin and author
* Fixed upload from edit topic and reply
* Fixed including of jQuery into header
* Fixed bbPress detection for edit pages

= 1.2.4 =
* Improved Dutch Translation
* Updated Frequently Asked Questions

= 1.2.3 =
* Minor change to user roles detection
* Fixed problem with displaying attachments to visitors

= 1.2.2 =
* Spanish Translation

= 1.2.1 =
* German Translation
* Check for the bbPress to add JavaScript and CSS

= 1.2.0 =
* Disable attachments for individual forums
* Improved admin side topic and reply editor integration

= 1.1.0 =
* Attachments icons in the attachment lists

= 1.0.4 =
* Attachment icon of forums

= 1.0.3 =
* Serbian Translation
* Dutch Translation

= 1.0.2 =
* Improvements to the main settings panel
* Fixed missing variable for topic attachments saving
* Fixed ignoring selected roles to display upload form elements
* Fixed upgrading plugin settings process
* Fixed few more undefined variables warnings

== Screenshots ==
1. Main plugins settings panel
2. Images settings panel
3. Advanced settings panel
4. Reply with attachments and file type icons
5. Attachments upload elements in the form
6. Single forum meta box with settings
7. Icons for the forums with attachments
8. Thumbnails displayed in line
9. Attachments with delete and detach actions
10. Image attachments with upload errors
