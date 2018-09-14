=== Newsletter ===
Tags: newsletter,email,subscription,mass mail,list build,email marketing,direct mailing,automation,automated,mailing list
Requires at least: 3.4.0
Tested up to: 4.9.8
Stable tag: 5.6.7
Contributors: satollo,webagile,michael-travan

Add a real newsletter system to your blog. For free. With unlimited newsletters and subscribers.

== Description ==

Newsletter is a **real newsletter system** for your WordPress blog: perfect for list building, you can easily create,
send and track e-mails, headache-free. It just works out of box!

= Main Features =

* **Unlimited subscribers** with statistics 
* **Unlimited newsletter** with tracking
* **Subscription spam check** with domain/ip black lists, Akismet, captcha
* **Delivery speed** fine control (from 12 emails per hour to as much as your blog can manage)
* [WPML ready](https://www.thenewsletterplugin.com/documentation/multilanguage)
* [GDPR ready](https://www.thenewsletterplugin.com/documentation/gdpr-compliancy) 
* **Multi-list targeting** with list combinations like all in, at least one, not in and so on
* **Drag and drop composer** with responsive email layout
* Customizable **subscription widget**, **page** or **custom form**
* Wordpress User Registration **seamless integration**
* **Single** And **Double Opt-In** plus privacy checkbox for EU laws compliance
* **Subscribers lists** to fine-target your campaigns
* PHP API and REST API for coders and integrations
* SMTP-Ready 
* Customizable Themes
* All messages are **fully translatable** from administration panels (no .po/.mo file to edit)
* **Status panel** to check your blog mailing capability and configuration
* **Compatible with every SMTP plugin**: Postman, WP Mail SMTP, Easy WP SMTP, Easy SMTP Mail, WP Mail Bank, ...
* **Subscribers import** from file
* Newsletter with Html and Text message versions 

= GDPR =

The Newsletter Plugin provides all the technical tools needed to achieve GDPR compliancy and 
we're continuously working to improve them and to give support even for specific 
use cases.

The plugin does not collect users' own subscribers data, nor it has any access to those data: 
hence, we are not a data processor, so a data processing agreement is not needed.

Anyway if you configure the plugin to use external services (usually an external mail
delivery service) you should check with that service if some sort of agreement is required.

= Integration with WordPress registration =

* Newsletter subscription check box on standard WordPress registration form
* Auto confirmation on first login
* Imports already registered users

= Free Extensions =

Find and install them from the Extensions panel in your blog.

* [WP Registration Integration](https://www.thenewsletterplugin.com/documentation/wpusers-extension) - connects the WordPress standard and custom registration with Newsletter subscription. Optionally imports all registered users as subscribers.
* [Archive Extension](https://www.thenewsletterplugin.com/documentation/archive-extension) - creates a simple blog page which lists all your sent newsletters
* [Locked Content Extension](https://www.thenewsletterplugin.com/documentation/locked-content-extension) - open up your premium content only after subscription

= Professional Extensions =

Need *more power*? Feel *something's missing*? The Newsletter Plugin features can be easily extended through 
our **premium, professional Extensions**! Let us introduce just two of them : )

* [Reports Extension](https://www.thenewsletterplugin.com/reports) - improves the internal statistics collection system and provides better reports of data collected for each sent email. Neat.
* [Automated Extension](https://www.thenewsletterplugin.com/automated) - generates and sends your newsletters using your blog last posts, even custom ones like events or products. Just sit and watch!
* [WooCommerce Extension](https://www.thenewsletterplugin.com/woocommerce) - subscribe customers to a mailing list and generate product newletters.
* [Amazon SES and other providers integration](https://www.thenewsletterplugin.com/integrations) - seamlessly integrate Amazon SES and other email service providers with The Newsletter Plugin. Hassle-free.
* [Contact Form 7 Extension](https://www.thenewsletterplugin.com/documentation/contact-form-7-extension) - integrate the subscription on Contact Form 7 forms
* [Google Analytics Extension](https://www.thenewsletterplugin.com/google-analytics) - track newsletter links with Google UTM tracking paramaters

= Support =

We provide support for our plugin on [Wordpress.org forums](https://wordpress.org/support/plugin/newsletter) and through our [official forum](https://www.thenewsletterplugin.com/forums).

Premium Users with an active license have access to one-to-one support via our [ticketing system](https://www.thenewsletterplugin.com/support-ticket). 

= Follow Us =

* **Our Official Website** - https://www.thenewsletterplugin.com/ 
* **Our Facebook Page** - https://www.facebook.com/thenewsletterplugin 
* **Our Twitter Account** - https://twitter.com/newsletterwp 

== Installation ==

1. Put the plug-in folder into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: go to the options page and configure the plugin

== Frequently Asked Questions ==

See the [Newsletter FAQ](https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-faq) or the
[Newsletter Forum](https://www.thenewsletterplugin.com/forums) to ask for help.

For documentation start from [Newsletter documentation](https://www.thenewsletterplugin.com/documentation).

Thank you, The Newsletter Team

== Screenshots ==

1. The plugin dashboard
2. The responsive email Drag & Drop composer
3. The Reports extension

== Changelog ==

= 5.6.7 =

* Fixed multilanguage support for service messages template

= 5.6.6 =

* Fixed unsubscription two-steps process message

= 5.6.5 =

* Fixed email validation message

= 5.6.4 =

* Fixed reactivation message display

= 5.6.3 =

* Removed unused files
* Fixed few links with permalink disabled and WMPL language as parameter
* Fixed the minimal form with WPML

= 5.6.2 =

* readme.txt improvements
* Fixed subscribe URL for blogs with WPML and permalinks disabled

= 5.6.1 =

* Fix debug notice on profile URL when no privacy page is set on WP
* Fixed export list filter

= 5.6.0 =

* Lists pre-assignment by language (no more need to customize the newsletter shortcode or duplicate the widgets)
* Improved Polylang support (still not fully tested)

= 5.5.9 =

* Fixed a possible debug notice on subscription without a dedicated page configured
* Fixed minimal widget with WPML language selector without permalink

= 5.5.8 =

* Fixed multilanguage text on profile page
* Fix the image resizer for small images
* Page message url based on subscriber language if available

= 5.5.7 =

* Fixed the unsubscription multilanguage messages using even the user language
* Fixed the goodby message

= 5.5.5 =

* Minimal form fix with WPML
* Privacy label fix with WPML

= 5.5.4 =

* WPML integration. [Read our integration page](https://www.thenewsletterplugin.com/documentation/multilanguage)

= 5.5.3 =

* Updated tinyMCE for Edge compatibility
* Debug mode notification
* Fix debug notice on profile page

= 5.5.2 =

* Fixed the multiple dedicated page creation on Welcome screen

= 5.5.1 =

* Fixed few debug notices
* API unsubscription messages fix

= 5.5.0 =

* Added IP storage control
* Fixed a warning and a debug notice
* Aggregated warnings on admin side

= 5.4.9 =

* Lists management in APIs
* Code cleanup
* New subscribers data export controls
* New global check and notice if the dedicated page is misconfigured
* Fix privacy note display on profile page even without a privacy url set

= 5.4.8 =

* Fixed the (duplicated) style.css reference

= 5.4.7 =

* Fixed pre-assigned lists

= 5.4.6 =

* Fixed few debug notices
* Added more translatable texts
* Improved performances
* Profile saving used as confirmation
* Fixed the captcha layout and style
* Fix initialization default messages template on first install

= 5.4.5 =

* Fixed tested up version value in readme.txt
* Added support for the WP privacy url
* Added initialization values for company info on first installation
* Fixed few debug notices
* Added button in lists panel to dissociate the list from every subscriber (list clean up)
* Fix of messages on profile editing panel

= 5.4.4 =

* Fixed warning on default option init 

= 5.4.3 =

* Improved the profile editing page and the email change check with activation id in double opt-in mode
* New profile editing panel configuration
* Privacy notice optionally even on profile panel
* New list change logging with source
* Removed old tabled-layout on profile editing page
* Clean up procedure for statistics and logs tables
* Removed old widget layout
* New options on list management panel
* Forced lists option removed from the subscription panel
* Dedicated page moved to main settings panel
* Tracking default value on main settings panel
* Removed old translations
* Added default option files
* Service message template no more on PHP file, the configurable template must be used
* Reactivation after cancellation feature
* Revised and simplified all texts for easy translation by the community
* The messages alternative page (/extensions/newsletter/subscription/page.php) is now deprecated and will be removed
* New [cancellation documentation page published](/extensions/newsletter/subscription/page.php)
* Integrated SMTP is now deprecated (soon will be replaced with a **free extension**) 
* {home_url} tag is now deprecated, use {blog_url} instead
* Introduced tags {company_name} and {company_address} replaced by info in the company info configuration
* Default template for messages has been improved with company contacts
* Repeated subscriptions management
* Generally improved the performances with caching and code clean up
* General CSS moved to the main settings panel
* Option to disable the default CSS
* Profile export fix

= 5.4.2 =

* SVN Deleted files fix

= 5.4.1 =

* Fixed debug notice in the standard widget
* Gender label fix
* Fixed the global variable conflict on widget (rare case)
* CSS fix on widget list field

= 5.4.0 =

* Fix lists as dropdown in the widget

= 5.3.9 =

* Version number fix

= 5.3.8 =

* Fixed failed insert on ip null

= 5.3.7 =

* Fixed the newsletter deletion with clean up of log tables

= 5.3.6 =

* Fixed composer block background editing
* Fixed API functions
* Minor fixes

= 5.3.5 =

* Fixed error notice on profile.php

= 5.3.4 =

* GDPR ready
* Maintenance option to add all subscriber without a list to a specified list
* Dismissed the tabled subscription form
* Fixed privacy checkbox label for field shortcode
* Logs of lists change
* Last activity tracking
* Retargeting/deletion of inactive subscribers
* Privacy checkbox without the checkbox (option)
* Personal data export
* Improved subscriber deletion with cleanup of log tables

= 5.3.3 =

* Added GIPHY composer block
* Added raw HTML composer block
* API: Newsletters and subscribers lists

= 5.3.2 =

* Security panel reorganized
* Added Akismet spam check 

= 5.3.1 = 

* Name and last name check for spam
* 404 responses on error condition
* jQuery fix
* Email cleanup on admin edit panel
* Name check for spam on subscription

= 5.3.0 =

* CAPTCHA system
* IP black list
* Email address black list

= 5.2.8 =

* Redirect fix

= 5.2.7 =

* Improved block layout
* Added filter on profile url
* Removed old obsolete query
* Improved the antibot
* Antiflood configurable to 30 minutes

= 5.2.6 =

* Fixed url attributes on privacy field shortcode
* Fixed few debug notices
* (NEW) PHP API for coders (and companion REST API with the free Newsletter API extension)

= 5.2.4 =

* readme.txt fix
* Improved extension version checking
* Changed the database timeout check on status panel
* Added support for pixel perfect thumb nails of media library images

= 5.2.3 =

* Newsletter subject ideas popup

= 5.2.2 =

* Removed create_function from widgets (compatibility PHP 7.2)
* Fixed the list exclusion condition
* Added [options to Newsletter shortcodes](https://www.thenewsletterplugin.com/documentation/subscription-form-shortcodes) to show the lists as dropdown

= 5.2.1 =

* Commit fix

= 5.2.0 =

* Fixed email_url tag (broken to fix Automated in previous version)

= 5.1.9 =

* Fixed debug notice on test email from Automated Extension

= 5.1.8 =

* Newsletter page creation fix

= 5.1.7 =

* NEW! Welcome wizard

= 5.1.6 =

* Fix list selection on first save

= 5.1.5 =

* Re-confirmation is now allows for unsubscribed and bounced 
* Fixed to minimal widget and minimal css
* Fixed the approx. subscriber count on newsletter creation (was showing encoded data)

= 5.1.4 =

* Fixed notices on email edit panel
* Added microdata to the call to action block
* Added filter on name field while sending. [See this post](https://www.thenewsletterplugin.com/?p=54292)
* Improved the online [viewability rules](https://www.thenewsletterplugin.com/documentation/newsletters-module#view-online) 
* Fixed theme editor bad behavior
* Fixed the min size of thumbnails on default theme
* Removed references to font awesome where not used

= 5.1.3 =

* Fixed newsletter duplication which was loosing the editor type
* Fixed gender saving on targeting

= 5.1.2 = 

* Improved the speed report on status panel
* Removed the obsolete diagnostic panel
* Removed obsolete code
* Removed the locked content menu entry (please install the free content lock extension)
* Fixed validation call on widget minimal
* Added more translatable strings
* Fixed the editor CSS when a theme has its own
* Confirmation is now activation
* CSS clean up

= 5.1.1 =

* Fix on email check

= 5.1.0 =

* "ncu" parameter can be used for alternative welcome page as well, not only confirmation, when single opt in is selected
* Removed the old "email alternative" to create custom subscription messages templates (has no effects)
* Added the antiflood system

= 5.0.9 =

* Removed wp users integration and locked content now available as FREE optional extensions (to make the plugin smaller)
* Removed obsolete code loading old-style extensions

= 5.0.8 =

* Improved select 2 layout
* Support for conditional comments in the editor

= 5.0.7 =

* Fix a database table field size

= 5.0.6 =

* Improved performance with new db indexes
* Fixed a bug in social URLs
* Home URL check on status panel
* Fixed a not removable notice

= 5.0.5 =

* Fixed an administration notice removal

= 5.0.4 =

* Fix media selector for blog without absolute URLs
* Notice to install the wp users integration
* Added workaround for XSS protection in chrome on custom form when they contains JS code

= 5.0.3 =

* Added {email_url_encoded} tag
* Changed https to http for compatibility with old servers

= 5.0.2 =

* Fixed a notice on theme selection panel
* Fixed a block initialization error notice for woocommerce
* Improved the emoji support on newsletter subject

= 5.0.1 =

* Fixed and improved the heading block
* Fixed the can spam block (not showing default texts is not configured)
* Fixed the footer block changed the unsubscribe url to the profile url

= 5.0.0 =

* Added wp user identifier on subscriber details panel
* Fixed a notice on subscriber list panel
* Added the Auto-Submit header
* Added the X-Auto-Response-Suppress header
* Added microdata markup for native confirmation button on email clients
* Fixed url checking with ending spaces
* Fix for sites with forced relative content url
* Fix debug notices on vimeo theme

= 4.9.9 =

* Fixed action URL for hand written forms in the subscription text
* Fixed rewrite not working with specific html formatting
* Change the composer text editor height

= 4.9.8 =

* Fixed the antibot option

= 4.9.7 =

* Fixed setup script throwing a debug notice
* Changed chart library

= 4.9.6 =

* Extension version check improved
* HTML5 form source code on profile panel

= 4.9.5 =

* Fixed table creation with dbDelta

= 4.9.4 =

* Admin css fix
* Log fix

= 4.9.3 =

* Replacing fix

= 4.9.2 =

* Style fix

= 4.9.1 =

* Small code fixes
* Chart js conflict fix
* Curl SSL version on status panel

= 4.9.0 =

* Fixed logo editing when not set in the Company Info
* Fix few layout problem on the user statistics panel
* Composer layout improvements
* Added select2 support
* Fixed syntax error on status panel auto call check
* Fixed notice for theme without the text part
* Added chart.js from cdn
* Improved stats collection and aggregation

= 4.8.9 =

* Package problems on WP.org 

= 4.8.8 =

* WP Users Integration and Locked Content Extensions readiness
* Improved the SSL management on admin side
* Fix privacy checkbox layout when the link is used
* Standard form CSS improvements

= 4.8.7 =

* Empty excerpt fix
* Fixed the list of blocks not appearing on few PHP installations
* Fixed the composer editor

= 4.8.6 =

* Warnings on SMTP panel when configured but not yet activated
* Warning management in controls
* Font family selector in the editor
* Font size selector in the editor
* Removed embedded tiny
* Removed embedded ace
* HTTPS on external links
* Fixed tag replacement on subject on test confirmation and welcome emails

= 4.8.5 =

* Added HTML editor plugin to tiny
* Fixed a couple of debug notices on widget

= 4.8.4 =

* Fixed posts block background editing
* Composer js improvements
* Fixed a couple of debug notices
* CSS fix

= 4.8.3 =

* Fixed debug notices on widgets
* Fixed Vimeo icon on social block
* Fixed javascript in the editor

= 4.8.2 =

* Changed few labels
* CSS fix on admin panels  

= 4.8.1 =

* Lists selection on widget
* CSS fix for submit button on widget
* Fixed the status panel on action call check
* Fixed the dedicated page creation
* New editor for service messages' template with desktop and mobile preview
* New raw html editor for newsletter with with desktop and mobile preview
* Improved status panel scheduler check
* Removed the old tiny mce 3

= 4.8.0 =

* New media selection on newsletter editor with size picker
* Fixed the required attribute on profile form
* Support for Analytics extension

= 4.7.9 =

* Removed the TGMPA library
* Some CSS fixes
* Fix few debug notices

= 4.7.8 =

* Old TGMPA library compatibility

= 4.7.7 =

* New extensions panel
* Minor fixes and enhancements
* New media selection on newsletter edit panel
* Removed enqueuing of no more used scripts
* Fixed the subscriber count on targeting panel

= 4.7.6 =

* New status panel

= 4.7.5 =

* Removed references to old css
* Fixed the relative URLs problem in the composer

= 4.7.4 =

* Improved widget CSS
* Added Instagram to social icons
* Little style improvements

= 4.7.3 =

* Added custom CSS field to customize the forms appearance
* Added codemirror for CSS edit
* Added plugin version to style link
* Added plugin version to script link
* Corrected a tag in newsletter widget minimal
* Added line height on form fields
* Changed CSS class prefixes to tnp
* Fixed a divide by zero on diagnostic panel
* Improved the collection of emails sending speed data
* Fixed a bug when adding a new subscriber from the admin panel

= 4.7.2 =

* Fixed a debug notice

= 4.7.1 =

* Fixed missing files in the package

= 4.7.0 =

* Added the selection for the Newsletter messages dedicated page
* New forms CSS and validator
* New widget for a minimal form
* Use shortcode attribute "layout" to revert to old style table forms, using layout=table
* Added reset button for diagnostic scheduler statistics

= 4.6.8 =

* Fixed the image browser/upload URL error in themes

= 4.6.7 =

* Fixed blank page without a newsletter dedicated page

= 4.6.6 =

* Fixed the blocks reload

= 4.6.5 =

* Fixed the style stripped when switching back and forth betwenn the visual and raw editor (from version 4.6.4)

= 4.6.4 =

* Fixed the profile link in admin panels
* Fixed the composer editor
* Cleanup of old code
* Force the composer editor to keep the absolute urls

= 4.6.3 =

* Fixed the profile save with new action url

= 4.6.2 =

* Fixed the import option "override status" not working in update mode
* Fixed the missing http when the couldflare plugin is installed (!)
* Improved controls and security on open tracking link
* Added few new diagnostic parameters

= 4.6.1 =

* Fixed a security issue on admin side only exploitable by logged in admins

= 4.6.0 =

* Fixed debug notices on composer post blocks
* Improved image styles on composer hero block
* Added support for WP_Error in the logger
* Improved the license checking

= 4.5.9 =

* Fixed a PHP syntax error on composer panel

= 4.5.8 =

* Edit image alt text in the composer
* Fixed german characters problem on visual composer
* Added new data to diagnostic panel
* Change the export to be more compatible with specific blog installations
* Added translations to export panel

= 4.5.7 =

* Fixed the total sent email in dashboard
* Fixed the total sent number on newsletter list when an already sent newsletter is edited
* Removed the save button on sent newsletter
* Code cleanup
* Service message for stats panel of draft newsletters

= 4.5.6 =

* New unified themes and composer selection screen
* Fixed the WP integration panel (incompatibility with WP 4.5.3)
* Removed few notices
* More warning fix
* Fix "isHTML" error
* jQuery conflict fix
* Escape fix
* Warning fix
* Fixed the mime header

= 4.5.0 =

* New responsive email Drag & Drop composer, see [the guide](https://www.thenewsletterplugin.com/plugins/newsletter/composer).

= 4.2.4 =

* Fixed the notices display in Newsletter admin pages
* Administrative emails sent now with the Newsletter engine
* New {email_id} and {email_subject} placeholders

= 4.2.3 =

* Improvements in database error management
* Added hook for Reports Extension

= 4.2.2 =

* The lock feature is disabled for editors and administrators
* Fixed the newsletter sent table

= 4.2.1 =

* Fixed the progress indicator on newsletter list

= 4.2.0 =

* New statistics dashboard
* Newsletter Reports 4 support

= 4.1.3 =

* Fixed the notice dismiss not working on every page
* Fixed debug notice on WP 4.5
* Added unverified SSL connection option on SMTP panel

= 4.1.2 =

* Minor security fix (on admin side with admin access)

= 4.2.0 =

* Added support for Reports Extension 4
* Improved license check

= 4.1.1 =

* Added compatibility with SMTP plugins (Newsletter now sends with wp_mail if not otherwise configured)
* Small CSS fixes

= 4.1.0 =

* Fix statistics link in the dashboard newsletter list

= 4.0.9 =

* Updated jQuery UI
* Updated compatibility notice
* Fixed tabs on subscriber stats panel
* Fixed documentation on profile page

= 4.0.8 =

* Dashboard style fix for WordPress >= 4.4
* Minor improvements

= 4.0.7 =

* New check to prevent not correct message template settings to block emails
* Improved confirmation email test
* Added welcome email test
* Resubscription in single opt-in does not send the confirmation email anymore
* Fixed second subscription with Facebook extension

= 4.0.6 =

* Fixed forced lists on subscription panel
* Fixed few i18n tags
* Fixed background color on theme selector

= 4.0.5 =

* Fixed an error on content lock
* Fixed CSS for extensions
* Fixed few debug notices
* Improved the content lock user recognition
* Subscriber search by list 

= 4.0.4 =

* Fixed and improved the email template for service messages

= 4.0.3 =

* Fixed the welcome email enable/disable setting 

= 4.0.2 =

* Fixed main settings saving
* Fixed unsubscription settings saving

= 4.0.0 =

* Shiny new look
* New top menu
* Locked content feature moved into the subscription module
* Separated SMTP configuration panel
* Newsletter users import transfer first and last name
* Various bug fixes and improvements

= 3.9.5 =

* Corrected the call to maybe_maybe_convert_table_to_utf8mb4

= 3.9.4 =

* CSV export fix
* Delete button on newsletter list fixed (wrong since version 3.9.3)

= 3.9.3 =

* Added the blog charset to the dagnostic panel
* Fixed the read count (was due to antispam filter changes)
* Language domain fixes

= 3.9.2 =

* Fixed the tab separator in CSV export
* Removed the already subscribed option
* Removed obsolete code for anchor tracking
* Added the sent newsletters to the subscriber editing panel
* Added a sent-to table

= 3.9.1 =

* Enable the tracking key edit
* Removed the antibot option, active by default
* First round of changes for translate.wordpress.org
* Fixed few notices
* Added deprecation notices for the email.php and email-alternative.php files
* Fixed the export separator
* Removed reference to plugin files in URLs to avoid spam filter
* Removed the selection of Newsletter action URL
* Removed obsolete files

= 3.9.0 =

* Fixed the new excerpt extraction

= 3.8.9 =

* Fixed few debug notices
* Improved support for browser without javascript
* Adding multi subscription support
* Improved antibot
* Fixed some debug/deprecated noticies
* Added support for new beta Report Extension features

= 3.8.8 =

* Unsubscription works now only with JavaScript enabled browser to block bots
* New way to extract excerpts to avoid third party filters
* Fixed the image selector for header logo
* Added preview form subscription message template
* Added WordPress like metadata on themes
* Fixed the default theme
* Changed few theme screeshots
* Added attribute "layout" to shortcode with value "html5" for tableless form

= 3.8.7 =

* Fixed the widget constructor

= 3.8.6 =

* Improved checks on tracking links which could be used for spamming links (open redirect)

= 3.8.5 =

* Changed the widget constructor
* Fixed the newsletter_form shortcode
* Added shortcodes for in page form building (beta): see the [plugin documentation](https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-documentation).

= 3.8.4 =

* Fixed the unsubscription problem

= 3.8.3 =

* Fixed the editor for unsubscription messages
* Added the unsubscription error message
* Fixed the email change from admin panels
* Fixed the profile field check when set as optionals

= 3.8.2 =

* Improved the profile editing page (with confirmation for email change)
* Added new sync between confirmed email and linked wp user email
* Improved check and messages on subscriber edit panel
* Changed the confirmation behavior for already confirmed users (welcome page reshown)
* Added the subscription delete option when a WordPress user si deleted
* Unsubscribe action for already unsubscribed subscribers reshow the unsubscription message
* Better management of emoji (creating sometime a database error)

= 3.8.1 =

* Corrected open tracking with new tracking URL schema

= 3.8.0 =

* CSV import fix

= 3.7.9 =

* Added subject empty check when sending tests
* Added option to import subscribers as confirmed or not confirmed 
* Added import subscribers from CSV file
* Updated the WP compatibility version number
* Fixed the save button on sent emails
* Fixed the List-Unsubscribe header with new URL schema

= 3.7.8 =

* Fixed the online email view with the new URL schema

= 3.7.7 =

* Fixed the editor role
* Fixed the unsubscription url with the new action url schema
* Fixed the readme.txt
* Tested with WP 4.2

= 3.7.6 =

* Fixed the new action URL schema
* Added a notice for blank page on newsletter creation
* Few notices removed
* Added more html require attributes
* Fixed the alternative confirmation redirect

= 3.7.5 =

* Fixed the referrer attribute on shortcodes

= 3.7.4 =

* Added control to avoid the wp super cache caching on newsletter services
* Added the new action URL schema (see the subscription steps panel)
* Added confirmation_url attribute to the form short code
* Added referrer attribute to the form short code
* Newsletters now start with empty subject and it's require to write it
* Fixed the API add service key check
* Fixed a couple of PHP noticies on widget (when not configured)

= 3.7.3 =

* Fixed extra profile field rules and placeholder

= 3.7.2 =

* Fixed the editor issue on subscription steps panel

= 3.7.1 =

* Attempt to fix the home url retrieval on some custom installations
* Removed some unused code
* Fixed the rate request notice
* Added the new URL tracking option (beta)
* Added the new URL tracking option notice
* Added file owner checking on diagnostic panel
* Added action files call checking on diagnostic panel
* Added dimensions on read-tracking image
* Added the html tag to the message templates
* Changed the template generation method to avoid conflicts with themes

= 3.7.0 =

* Bugfix

= 3.6.9 =

* Little fix

= 3.6.8 =

* Fixed the subject of the administrative notification
* Cleaned up obsolete code
* Added support for extension versions check
* Fixed typo in text only themes
* Fixed wrong unsubscribe code in German Welcome Email

= 3.6.7 =

* New Blog Info configuration panel
* New Default Theme
* Minor layout changes
* Fix subscription email link
* Added notices when filters are active on subscriber management panel
* Few fixes on statistic panel
* Fixed undefined index noticies on subscription page
* Several fixes
* A TNT team member quitted smoking, so the plugin become smoking free

= 3.6.6 =

* Added a cron monitor
* Added a xmas theme
* Fixed the opt-in mode for wordpress registsred users
* Fixed the noticies
* Fixed somes styles
* Added the direct newsletter edit for themes without options
* Header changed
* Fixed all links to refer the new site www.thenewsletterplugin.com
* Fixed the newsletter editor default style

= 3.6.5 =

* Added parameter "plugin url" on diagnostic panel
* Added custom post types to the linear theme
* Added custom post types to the vimeo-like theme
* Fixed the feed by mail placeholder panel
* Fixed the antibot option with preferences

= 3.6.4 =

* Support for greek (and others) characters without the entity encoding
* Fixed a debug notice in the widget code
* Added gender on import
* Added support for the constant NEWSLETTER_LOG_DIR in wp-config.php to define the loggin folder
* Fixed the domain removal on subscription steps messages

= 3.6.3 =

* Fixed the feed by mail test function

= 3.6.2 =

* Added the separator selection on CSV export
* Added the UTF-8 BOM to the export
* Fixed some debug noticies

= 3.6.1 =

* Fixed the widget when field names contain double quotes

= 3.6.0 =

* Removed the extension list from welcome panel
* Added the and operator in the newsletter recipients selector
* Fixed the select_group(...) in NewsletterControls class

= 3.5.9 =

* Added a possible antibot to the subscription flow

= 3.5.8 =

* Added soundcloud for social icon on default theme
* Fixed the welcome screen (should)

= 3.5.7 =

* Added the private flag on newsletters
* Fixed old extension version checking/reporting

= 3.5.6 =

* Added custom header for newsletter tagging with mandrill
* Added internally used html 5 subscription form

= 3.5.5 =

* Added the license key field for special installations

= 3.5.4 =

* Fixed the web preview charset

= 3.5.3 =

* Added support for extensions as plugins

= 3.5.2 =

* Fixed the {title} tag replacement for old subscriber list with the gender not set
* Added the upgrade from old versions button on diagnostic panel

= 3.5.1 =

* Support for the SendGrid extension

= 3.5.0 =

* Fixed the subscriber list panel
* Interface reviewed
* Fixed the image chooser for WP 3.8.1
* Fixed the export for editors
* Patch for anonymous users create by woocommerce
* Madrill API adapter
* Header separation between this plugin and the extensions
* Default to base 64 encoding of outgoing email to solve the long lines problem

= 3.4.9 =

* Fixed some warnings in debug mode
* Fixed the disabling setting of the social icons (on default newsletter themes)
* Added filters on widget for WPML
* Added filter for single line feeds refused by some mail servers

= 3.4.8 =

* Added a javascript protection against auto confirmation from bot
* Fixed a warning with debug active on site login

= 3.4.7 =

* Fixed the subscription panel where some panels where no more visible.

= 3.4.6 =

* Added the full_name tag
* Added the "simple" theme
* Added indexes to the statistic table to improve the reports extension response time
* Fixed some noticies in debug mode

= 3.4.5 =

* Revisited the theme chooser and the theme configuration
* Fixed a double field on the locked content configuration
* Improved the delivery engine

= 3.4.4 =

* Improved error messages
* Fixed the last tab store (jquery changes)
* Added some new controls for the pop up extensions

= 3.4.3 =

* Added the precendence bulk header (https://support.google.com/mail/answer/81126)
* Added filter on messages to avoid wrong URLs when the blog change domain or folder
* Added the alt attribute to the tracking image
* New option to set the PHP max execution time
* Fixed some text on main configuration panel

= 3.4.2 =

* Refined the subscription for already subscribed emails

= 3.4.1 =

* Fixed the delivery engine warning message
* Fixed the version check

= 3.4.0 =

* Changed newsletter copy to copy even the editor and traking status
* Fixed the subscribers search list
* Added some more buttons on Newsletter editor
* Added the subscription form menu voice (I cannot answer anymore the same request about subscribe button translation :-)
* Suppressed warning on log function

= 3.3.9 =

* Fixed activation in debug mode
* Fixed some notices
* Added defaults for subscriber titles (Mr, Mrs, ...)

= 3.3.8 =

* Internal code fixes
* Fixed the "editor" access control

= 3.3.7 =

* Fixed the feed by mail field on widget
* Fixed tab names to avoid mod_security interference
* Fixed the "name" form field rules
* Added (undocumented/untested) way to change the table names

= 3.3.6 =

* Fixed a caching blocking on short code
* New way to create a newsletter

= 3.3.5 =

* Fixed the mailto rewriting
* Added tags and categories to default theme
* Added post type on default theme
* Fixed some administrative CSS
* Revisited the theme selection and configuration

= 3.3.4 =

* Fixed the module version check

= 3.3.3 =

* Fixed the IP tracking on opening

= 3.3.2 =

* Disabled the save button on composer when the newsletter is "sending" or "sent"
* Added ip field on statistics
* Reviewed the subscriber statistics panel
* Fixed some links on welcome panel
* Added extensions version check
* Added the Mandrill Extension support
* Fixed the banner options on default theme
* New "new newsletter" panel (hope simpler to use)

= 3.3.1 =

* Fixed a bug in an administrative query

= 3.3.0 =

* Fixed a replacement on online email version
* Fixed a missing privacy check box configuration
* Improved the split posts
* Added post_type control
* Re-enabled the subscription for addresses not confirmed
* Fixed the welcome and ocnfirmaiton email when sent from subscribers list panel (were not using the theme)
* Added the "pre-checked" option to preferences configuration

= 3.2.9 =

* Fixed a possible loop on widget (when using extended fields in combobox format)

= 3.2.8 =

* Fixed the newsletter_replace filter
* Added the person title for salutation
* Changed the profile field panel
* Fixed the massive deletion of unsubscribed users

= 3.2.7 =

* Added a controls for the Reports module version 1.0.4
* Changed opening tracking and removed 1x1 GIF
* Added support for popup on subscription form
* Fixed the link to the reports module

= 3.2.6 =

* Fixed the forced preferences on subscription panel

= 3.2.5 =

* Fixed the home_url and blog_url replacements
* Added the cleans up of tags used in href attributes
* Fixed the cleans up of URL tags
* Added module version checking support
* Added the welcome email option to disable it
* Fixed the new subscriber notification missing under some specific conditions

= 3.2.4 =

* Added target _blank on theme links so they open on a new windows for the online version
* Changed to the plugins_url() function
* Added clean up of url tags on composer

= 3.2.3 =

* Added schedule list on Diagnostic panel
* Removed the enable/disable resubscription option
* Added a check for the delivery engine shutdown on some particular situations
* Revisited the WordPress registration integration
* Revisited the WordPress user import and moved on subscriber massive action panel
* Added links to new documentation chapter
* Removed a survived reference to an old table
* Reactivated the replacement of the {blog_url} tag
* Fixed the tracking code injection
* Fixed a default query generation for compatibility with 2.5 version
* Fixed the tag replacements when using the old forms

= 3.2.2 =

* Fixed the subscription options change problem during the upgrade
* English corrections by Rita Vaccaro
* Added the Feed by Mail demo module
* Added support for the Facebook module

= 3.2.1 =

* Fixed fatal error with old form formats

= 3.2.0 =

* Added hint() method to NewsletterControls
* Fixed the Newsletter::replace_date() to replace even the {date} tag without a format
* Added NewsletterModule::format_time_delta()
* Added NewsletterModule::format_scheduler_time
* Improved the diagnostic panel
* Fixed an error on subscription with old forms
* Fixed the unsubscription with old formats
* Fixed the confirmation for multiple calls
* Fixed user saving on new installation (column missing for followup module)
* Added compatibility code with domain remaping plugin
* Added a setting to let unsubscribed users to subscribe again
* Added the re-subscription option

= 3.1.9 =

* Added the NEWSLETTER_MAX_EXECUTION_TIME
* Added the NEWSLETTER_CRON_INTERVAL
* Improved the delivery engine performances
* Improved the newsletter list panel
* Change the subscription in case of unsubscribed, bounced or confirmed address with a configurable error message
* Some CSS review
* Fixed the unsubscription procedure with a check on user status
* Added Pint theme

= 3.1.7 =

* Added better support for Follow Up for Newsletter
* Fixed integration with Feed by Mail for Newsletter
* Fixed a bug on profile save
* Fixed a message about log folder on diagnostic panel
* Fixed the sex field on user creation

= 3.1.6 =

* Fixed the subscription form absent on some configurations

= 3.1.5 =

* Content locking deactivated if a user is logged in
* Added a button to create a newsletter dedicated page
* Added top message is the newsletter dedicated page is not configured
* Fixed the subscription process with the old "na" action
* Added a new option with wp registration integration
* Added the opt-in mode to wp registration integration

= 3.1.4 =

* Fixed a bug on post/page preview

= 3.1.3 =

* Added support for SendGrid Module
* Fixed a fatal error on new installations on emails.php

= 3.1.2 =

* Fixed the access control for editors
* Improved to the log system to block it when the log folder cannot be created
* Moved all menu voices to the new format
* Improved the diagnostic panel
* Added ability to send and email to not confirmed subscribers
* Fixed a problem with internal module versions

= 3.1.1 =

* Fixed the copy and delete buttons on newsletter list
* Removed the old trigger button on newsletter list
* Fixed the edit button on old user search
* Improved the module version checking
* Added the "unconfirm" button on massive subscriber management panel

= 3.1.0 =

* Added link to change preferences/sex from emails
* Added tag reference on email composer
* Added "negative" preference selection on email targeting
* Improved the subscription during WordPress user registration
* Fixed the preference saving from profile page
* Fixed the default value for the gender field to "n"
* Added loading of the Feed by Mail module
* Added loading of the Follow Up module
* Added loading of the MailJet module
* Changed the administrative page header
* Changed the subscriber list and search panel
* Improved the locked content feature
* Fixed the good bye email not using the standard email template
* Changed the diagnostics panel with module versions checking
* Fixed some code on NewsletterModule

= 3.0.9 =

* Fixed an important bug

= 3.0.8 =

* Fixed the charset on some pages and previews for umlaut characters

= 3.0.7 =

* Fixed a warning in WP 3.5
* Fixed the visual editor on/off on composer panel

= 3.0.6 =

* Added file permissions check on diagnostic panel
* Fixed the default value for "sex" on email at database level
* Fixed the checking of required surname
* Fixed a warning on subscription panel
* Improved the subscription management for bounced or unsubscribed addresses
* Removed the simple theme of tinymce to reduce the number of files
* Added neutral style for subscription form

= 3.0.5 =

* Added styling for widget
* Fixed the widget html
* Fixed the reset button on subscription panels
* Fixed the language initialization on first installation
* Fixed save button on profile page (now it can be an image)
* Fixed email listing showing the planned status

= 3.0.4 =

* Fixed the alternative email template for subscription messages
* Added user statistics by referrer (field nr passed during subscription)
* Added user statistics by http referer (one r missing according to the http protocol)
* Fixed the preview for themes without textual version
* Fixed the subscription redirect for blogs without permalink
* Fixed the "sex" column on database so email configuration is correctly stored
* Fixed the wp user integration

= 3.0.3 =

* Fixed documentation on subscription panel and on subscription/page.php file
* Fixed the statistics module URL rewriting
* Fixed a "echo" on module.php datetime method
* Fixed the multi-delete on newsletter list
* Fixed eval() usage on add_menu_page and add_admin_page function
* Fixed a number of ob_end_clean() called wht not required and interfering with other output buffering
* Fixed the editor access level

= 3.0.2 =

* Documented how to customize the subscription/email.php file (see inside the file) for subscription messages
* Fixed the confirmation message lost (only for who do not already save the subscription options...)

= 3.0.1 =

* Fixed an extra character on head when including the form css
* Fixed the double privacy check on subscription widget
* Fixed the charset of subscription/page.php
* Fixed the theme preview with wp_nonce_url
* Added compatibility code for forms directly coded inside the subscription message
* Added link to composer when the javascript redirect fails on creation of a new newsletter
* Fixed the old email list and conversion

= 3.0.0 =

* Release

= 2.6.2 =

* Added the user massive management panel

= 2.5.3.3 =

* Updated to 20 lists instead of 9
* Max lists can be set on wp-config.php with define('NEWSLETTER_LIST_MAX', [number])
* Default preferences ocnfigurable on subscription panel

= 2.5.3.2 =

* fixed the profile fields generation on subscription form

= 2.5.3.1 =

* fixed javascript email check
* fixed rewrite of link that are anchors
* possible patch to increase concurrency detection while sending
* fixed warning message on email composer panel

= 2.5.3 =

* changed the confirmation and cancellation URLs to a direct call to Newsletter Pro to avoid double emails
* mail opening now tracked
* fixed the add api
* feed by mail settings added: categories and max posts
* feed by mail themes change to use the new settings
* unsubscribed users are marked as unsubscribed and not removed
* api now respect follow up and feed by mail subscription options
* fixed the profile form to add the user id and token
* subscribers' panel changed
* optimizations
* main url fixed everywhere
* small changes to the email composer
* small changes to the blank theme

= 2.5.2.3 =

* subscribers panel now show the profile data
* search can be ordered by profile data
* result limit on search can be specified
* {unlock_url} fixed (it was not pointing to the right configured url)

= 2.5.2.2 =

* fixed the concurrent email sending problem
* added WordPress media gallery integration inside email composer

= 2.5.2.1 =

* added the add_user method
* fixed the API (was not working) and added multilist on API (thankyou betting-tips-uk.com)
* fixed privacy check box on widget

= 2.5.2 =

* added compatibility with lite cache
* fixed the list checkboxes on user edit panel
* removed the 100 users limit on search panel
* category an max posts selection on email composer

= 2.5.1.5 =

* improved the url tag replacement for some particular blog installation
* fixed the unsubscription administrator notification
* replaced sex with gender in notification emails
* fixed the confirm/unconfirm button on user list
* fixed some labels
* subscription form table HTML

= 2.5.1.4 =

* added {date} tag and {date_'format'} tag, where 'format' can be any of the PHP date formats
* added {blog_description} tag
* fixed the feed reset button
* added one day back button to the feed
* updated custom forms documentation
* fixed the trigger button on emails panel
* changed both feed by mail themes (check them if you create your own theme)
* fixed the custom profile field generation (important!)
* fixed documentation about custom forms

Version 2.5.1.3
- fix the feed email test id (not important, it only generates PHP error logs)
- feed by mail send now now force the sending if in a non sending day
- changed the way feed by mail themes extract the posts: solves the sticky posts problem
- added the feed last check time reset button
- fixed the confirm and cancel buttons on user list
- fixed the welcome email when using a custom thank you page
- added images to theme 1
- added button to trigger the delivery engine
- fixed the widget mail check
- reintroduced style.css for themes
- updated theme documentation
- added CDATA on JavaScript
- fixed theme 1 which was not adding the images
- added theme 3

Version 2.5.1.2
- fixed the old profile fields saving

Version 2.5.1.1
- new fr_FR file
- fixed test of SMTP configuration which was sending to test address 2 instead of test address 1
- bounced voice remove on search filter
- added action "of" which return only the subscription form and fire a subcription of type "os"
- added action "os" that subscribe the user and show only the welcome/confirmation required message
- fixed issue with main page url configuration

Version 2.5.1
- Fixed the widget that was not using the extended fields
- Fixed the widget that was not using the lists
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the widget form
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the main subscription form
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the profile form
- Added the classes "newsletter-email", "newsletter-firstname", "newsletter-surname" to the respective fields on every form
- Removed email theme option on subscription panel (was not used)
- Fixed the welcome email on double opt in process
- Subscription notifications to admin only for confirmed subscription
- Fixed subscription process panel for double opt in (layout problems)
- Improved subscription process panel


Version 2.5.0.1
- Fix unsubscription process not working

Version 2.5.0
- Official first release

