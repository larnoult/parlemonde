=== BP xProfile Location ===
Contributors: shanebp
Donate link: http://www.philopress.com/donate/
Tags: buddypress, members, profile, xprofile, location, address, geocode, map
Author URI: http://philopress.com/contact/
Plugin URI: http://philopress.com/products/
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 1.6
License: GPLv2 or later

A BuddyPress plugin that creates a xProfile Location field type that use the Google Places API

== Description ==

Creates an xProfile Location field type that use the Google Places API to populate and validate the address fields.
The result will be *uniform* and *searchable* addresses with a *single* input field.

You can create multiple Location fields via *wp-admin > Users > Profile Fields > Add New Field*

The xprofile field for each member will be populated as a searchable string.

A latitude / longitude 'geocode' will be saved as a separate field, if that option was selected when the field was created.

You can then use the geocode in your preferred Member Map solution.

Or you may be interested in this BuddyPress **Member Map** solution: [BP Maps for Members](http://www.philopress.com/products/bp-maps-for-members "BP Maps for Members")

For BuddyPress **Group Maps**, please see: [BP Maps for Groups](http://www.philopress.com/products/bp-maps-for-groups "BP Maps for Groups")


For more information about this plugin, please visit [BP xProfile Location](http://www.philopress.com/products/bp-xprofile-location/ "BP xProfile Location")


== Installation ==

1. If you have not entered a Google Maps API Key for one of your other PhiloPress plugins - see the FAQ 

2. Upload the zip on the Plugins > Add screen in wp-admin

3. Activate the plugin through the 'Plugins' menu in WordPress

4. Go to wp-admin > Users > Profile Fields > Add New Field and Create a profile field of Type = Location.




== Frequently Asked Questions ==

= Do I need a Google Maps API Key? =
Yes. If you need help, read this tutorial: [Google Maps API Key](http://www.philopress.com/google-maps-api-key/ "Google Maps API Key")

= I have a Google Maps API Key. Where do I put it? =
* You will need to unzip the plugin. 
* Then open this file in a text editor: <em>bp-xprofile-location\inc\pp-field-type-location.php</em>
* And paste your Key where it says 'Paste Your Key Here'. Don't delete the single quotes. Save the file. Re-zip the plugin. 

= Other questions? =
* Multi-site support - Yes
* Works with [BP Profile Search](https://wordpress.org/plugins/bp-profile-search/ "BP Profile Search plugin")
* Maps are not included. For maps, please see: [BP Maps for Members](http://www.philopress.com/products/bp-maps-for-members "BP Maps for Members")




== Screenshots ==
1. The Edit screen on a member profile with Address Auto-Complete
2. Creating a Profile Field of Type > Location
3. GeoCode option when creating a Profile Field


== Changelog ==

= 1.6 =
* Add 'Description' field output in class-pp-field-type-location.php

= 1.5 =
* Prevent saving of field data and geocode if the value is an empty serialized array, a:0:{}

= 1.4 =
* Changed the method for supporting the BP Profile Search plugin. 

= 1.3 =
* Added check for enabled BuddyPress Extended Profiles component
* Tested with WP 4.7 and BP 2.7.2

= 1.2 =
* Added requirement for Google Maps API Key. 

= 1.1 =
* Fix autolink issue

= 1.0 =
* Initial release.



== Upgrade Notice ==

= 1.5 =
* Prevent saving of field data and geocode if the value is an empty serialized array, a:0:{}

= 1.4 =
* Changed the method for supporting the BP Profile Search plugin.  You must have at least version 4.7.8 of the BP Profile Search plugin in order to be able to search on any profile fields that you add using the BP xProfile Location plugin. 

= 1.3 =
* Added check for enabled BuddyPress Extended Profiles component
* Tested with WP 4.7 and BP 2.7.2

= 1.2 =
* Added requirement for Google Maps API Key. If you were using this plugin prior to June 22, 2016, you don't need this update. If you decide to update anyway, you will need a Key - please see the FAQ for more info

= 1.1 =
* Fix autolink issue

= 1.0 =


