=== BP Limit Activity Length ===
Contributors: Mike_Cowobo
Donate link: http://trenvo.com/
Tags: buddypress, activity, twitter, length
Requires at least: WP3.5, BP1.6
Tested up to:  WP3.5.1, BP1.6.3
Stable tag: 0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Limit the length of your BuddyPress activity updates like Twitter

== Description ==

BP Limit Activity Length is a simple plugin that forces users to keep the length of their activity updates limited by amount of characters or words.

The plugin adds a character/word countdown next to the Post Update button, and prevents users from typing further than the specified limit. *Only new activities will be truncated to the set amount of characters or words*

The amount of characters is a simple setting in the BuddyPress administration screens.

== Installation ==

Install the plugin the usual way, then set the character limit (defaults to 140) in the BuddyPress settings screen (WP-Admin -> Settings -> BuddyPress -> Settings).

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. The plugin in action on the default activity form
1. The setting

== Changelog ==

=0.4=

* Add (filterable) whitelist for activity types that need to adhere to the limit
* Added German translation (thanks to Thorsten Wollenhöfer!)

=0.3.5=

* Made the truncation count characters not bytes, so multibyte characters are counted as 1 (thanks to Jonas Knupp!)

=0.3.4=

* Compatibility with BP Reshare

=0.3.3=

* Added limit to activity comments

=0.3.2=

* Added Dutch and Italian translations (thanks [Luca](https://github.com/luccame)!)

=0.3.1=

* Updated .pot file

=0.3=

* Initial release to WP Plugin Repository

=0.2=

* Added word limit as alternative

=0.1=

* Initial release to GitHub