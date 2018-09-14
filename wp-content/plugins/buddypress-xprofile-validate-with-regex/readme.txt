=== BuddyPress XProfile Validate with RegEx ===
Contributors: tometzky
Tags: buddypress, profile, anti-spam
Requires at least: 3.8
Tested up to: 4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress - Validate XProfile data with PCRE regular expressions.

== Description ==

With this plugin you can define a PCRE regular expression against which data entered
in XProfile fields will be matched.

For example:

* check *webpage* field for a correct(-ish) URL or domain name: `%^(https?://)?([^ :/]{1,63}\.)+[^ :/.]{2,63}%iu`
* check *age* field for a sane(-ish) value (0-199): `/^1?[0-9]?[0-9]$/`
* check *phone* field for a sane(-ish) value: `/^+?[0-9 -]{7,45}$/`
* check any field for a minimum and maksimum length: `/^.{10,100}$/u`

You can also configure a message to show when a field data does not validate.

== Changelog ==

= TODO =
* Checking for a valid regular expression while saving field configuration.
* Support validating standard Name field
* Support validating field data while editing profile - currently data is validated only during registering.

= 0.1.2 =
* Fixed a bug: configuration wasn't saved during creating a new xprofile
  field - only during editing an existing field

= 0.1.1 =
* Added plugin site
* Change license to GPLv2 or later - interaction of AGPLv3 with other
  licenses is too confusing

= 0.1.0 =
* Initial release
