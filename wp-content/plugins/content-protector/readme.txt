=== Passster ===
Contributors: patrickposner
Tags: protect, lock, CAPTCHA, password, hide, content, secret, AJAX, cookie, post, page, secure
Requires at least: 2.0.2
Tested up to: 4.9.4
Stable tag: 3.0
License: GPL2

Plugin to protect content on a Page or Post, where users require a password to access that content.

== Description ==
The Passster plugin allows users to password-protect a portion of a Page or Post.  This is done by adding a shortcode that you wrap
around the content you want to protect.  Your users are shown an access form in which to enter a password; if it's correct, the protected content
will get displayed.

Features

* Set up multiple protected sections on a single Post
* Set cookies so users won't need to re-enter the password on every visit, and share authorization with groups of protected sections.
* Apply custom CSS to your forms
* Choose from a variety of encryption methods for your passwords (depending on your server configuration)
* Set custom passwords to authorize your visitors

A TinyMCE dialog is included to help users build the shortcode. See the Screenshots tab for more info.

== Installation ==
**Note:** `XXX` refers to the current version release.
= Automatic method =
1. Click 'Add New' on the 'Plugins' page.
1. Upload `content-protector-XXX.zip` using the file uploader on the page

= Manual method =
1. Unzip `content-protector-XXX.zip` and upload the `content-protector` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

Coming soon.  In the meantime, check out the <a href="http://wordpress.org/support/plugin/content-protector">support forum</a> and ask away.

== Screenshots ==

1. The Form Instructions tab on the Passster Settings page.
2. The Form CSS tab on the Passster Settings page.
3. TinyMCE dialog for generating Passster shortcodes.
4. A Passster shortcode wrapped around some top-secret content.
5. The access form Passster creates for your authorized users to enter the password.
6. If the password is wrong, an error message is displayed, along with the access form so they can try again.
7. A correct password results in a success message being displayed, along with the unlocked content.
8. If you've set a cookie, the success message is only shown on initial authorization. This is how the unlocked content will be shown until the cookie expires.
9. A Passster access form that uses a CAPTCHA.  You can customize the image under Settings -> Passster.

== Changelog ==

= 2.2 =
* under new development
* compatibilty with WordPress 4.9+
* clean up and restructure whole plugin
* remove deprecated solutions for ajax and captcha
* removed date based selection of cookie expires

= 2.11 =
* Setting "Password Field Placeholder" now accessible through "Settings -> Passster -> Password/CAPTCHA Field"

= 2.10 =
* Form and CAPTCHA instructions moved to outside the form.
* `content_protector_unlocked_content` filter bug in AJAX mode fixed.
* CSS for `div.content-protector-form-instructions` fixed.
* New Setting "CAPTCHA Case Insensitive" - to allow users to enter CAPTCHAs w/o case-sensitivity.
* New action `content_protector_ajax_support` - for loading any extra files needed to support your protected content in AJAX mode.

= 2.9.0.1 =
* Fixed bug crashing `content_protector_unlocked_content` filter.
* Full AJAX support for `[caption]` built-in shortcode.

= 2.9 =
* Full AJAX support for `[embed]`, `[audio]`, and `[video]` built-in shortcodes.
* Added full support for `[playlist]` and `[gallery]` built-in shortcodes.
* Fixed Encrypted Passwords Storage setting message bug.
* `content_protector_content` filter now called `content_protector_unlocked_content`.
* `content_protector_unlocked_content` filter can now be customized from the Settings -> General tab.
* `the_content` filter now applied to form and CAPTCHA instructions.

= 2.8 =
* Partial AJAX support for `[embed]`, `[audio]`, and `[video]` built-in shortcodes. (experimental)
* Fixed AJAX error from code refactoring

= 2.7 =
* Displaying Form CSS on unlocked content is now a user option (on the Form CSS tab).
* When saving settings, the Settings page will now remember which tab you were on and load it automatically,
* Fixed potential cookie expiry bug for sessions meant to last until the browser closes (expiry time set explicitly to '0').
* Improved error checking for conflicting settings.
* Some code refactoring.

= 2.6.2 =
* Fixed output buffering bug for access form introduced in 2.6.1.

= 2.6.1 =
* Fixed AJAX security nonce bugs.

= 2.6 =
* jQuery UI theme updated to 1.11.4

= 2.5.0.1 =
* New setting to manage encrypted passwords transient storage.
* New settings for Password/CAPTCHA Fields character lengths.
* Improved option initialization and cleanup routines.
* `content-protector-ajax.js` now loads in the footer.
* WPML/Polylang compatibility (beta).
* New partial translation into Serbian (Latin); thanks to Andrijana Nikolic from WebHostingGeeks (Novi parcijalni prevod na Srpski ( latinski ); Hvala Andrijana Nikolic iz WebHostingGeeks)

= 2.5 =
* Skipped

= 2.4 =
* Skipped

= 2.3 =
* Settings admin page now limited to users with `manage_options` permission (i.e., admin users only).
* Fixed bug where when using AJAX and CAPTCHA together, CAPTCHA image didn't reload on incorrect password.
* New settings: use either a text or password field for entering passwords/CAPTCHAs, and set placeholder text for those fields.
* Added `autocomplete="off"` to the access form.
* Streamlined i18n for date/time pickers (Use values available in Wordpress settings and `$wp_locale` when available, combined *-i18n.js files into one).

= 2.2.1 =
* Fixed AJAX bug where shortcode couldn't be found if already enclosed in another shortcode.
* Clarified error message if AJAX method cannot find shortcode.
* Changed calls from `die()` to `wp_die()`.

= 2.2 =
* Removed `content-protector-admin-tinymce.js` (No need anymore; required JS variables now hooked directly into editor). Fixes incompatibility with OptimizePress.

= 2.1.1 =
* Added custom filter `content_protector_content` to emulate `apply_filter( 'the_content', ... )` functionality for form and CAPTCHA instructions.

= 2.1 =
* Rich text editors for form and CAPTCHA instructions.
* NEW Template/Conditional Tag: `content_protector_is_logged_in()` (See Usage for details).
* Performance improvements via Transients API.

= 2.0 =
* New CAPTCHA feature! Check out the CAPTCHA tab on Settings -> Content Protector for details.
* Improved i18n.
* Various minor bug fixes.

= 1.4.1 =
* Dashicons support for WP 3.8 + added. Support for old-style icons in Admin/TinyMCE is deprecated.
* Unified dashicons among all of my plugins.

= 1.4 =
* Added "Display Success Message" option.

= 1.3 =
* Added "Shared Authorization" feature.
* Renamed "Password Settings" to "General Settings".

= 1.2.2 =
* Added support for Contact Form 7 when using AJAX.

= 1.2.1 =
* Fixed label repetition on "Cookie expires after" drop-down menu.

= 1.2 =
* Various CSS settings now controllable from the admin panel.
* Palettes on Settings color controls are now loaded from colors read from the active Theme's stylesheet.  This
should help in choosing colors that fit in with the active Theme.
* Spinner image now preloaded.
* Some language strings changed.

= 1.1 =
* AJAX loading message now customizable.

= 1.0.1 =
* Added required images for jQuery UI theme.
* Fixed some i18n strings.

= 1.0 =
* Initial release.

== Upgrade Notice ==
= 2.8 =
New features and bug fixes. Please upgrade.

= 2.6.1 =
New bug fixes. Please upgrade.

= 2.3 =
New features and bug fixes. Please upgrade.

= 2.1.1 =
Added custom filter `content_protector_content` to emulate `apply_filter( 'the_content', ... )` functionality for form and CAPTCHA instructions. Please upgrade.

= 2.1 =
New features. Please upgrade.

= 2.0 =
New features and bug fixes. Please upgrade.

= 1.2.1 =
Fixed label repetition on "Cookie expires after" drop-down menu. Please upgrade.

= 1.0.1 =
Added required images for JQuery UI theme and fixed some i18n strings.

= 1.0 =
Initial release.

== Usage ==

NOTE: The shortcode can be built using the built-in TinyMCE dialog.  When in doubt, use the dialog to create correctly formed shortcodes.

= Shortcode =

`[content_protector password="{string}" identifier="{string}" cookie_expires="{string|int}"]...[/content_protector]`

* `password` - Specifies the password that unlocks the protected content. Upper- and lower-case Latin alphabet letters (A-Z and a-z), numbers (0-9), and "." and "/" only.  Set `password` to "CAPTCHA" to add a CAPTCHA to your access form.
* `identifier` <em>(Optional)</em> - Used to differentiate between multiple instances of protected content
* `cookie_expires` <em>(Optional)</em> - If set, put a cookie on the user's computer so the user doesn't need to re-enter the password when revisiting the page.
= Template/Conditional Tag =

`content_protector_is_logged_in( $password = "", $identifier = "", $post_id = "", $cookie_expires = "" )`

* `$password`, `$cookie_expires`, and `$identifier` are defined the same as their analogous attributes above. `$post_id` is the Post ID.
* Returns `true` if the user is currently authorized to access the content protected by a Passster shortcode matching those parameters.
* All arguments are <strong>required</strong>.

= Notes =

1. `cookie_expires` can be either a string or an integer. If it's an integer, it's processed as the number of seconds before the cookie expires; set it to "0" to make the cookie
expire when the browser is closed.  If it's a string, it can be either a duration (e.g., "2 weeks")
2. While the use of `identifier` is optional, you *must* set it if you want to apply custom CSS with a specific access form, or to use Shared Authorization.
3. While you don't need to set `identifier` if you want to want to set a cookie for specific protected content, editing that content in the future will invalidate any
cookies set for it (this may actually be desired behaviour, depending on what you're trying to do).
4. Basically, when in doubt, set the `identifier` attribute.  You'll thank yourself later.
5. When you set an identifier for protected content, the identifier gets appended onto the existing DOM IDs in its access form.  For example if you set `identifier="Bob"`
in a shortcode, the ID for that form element will be `#content-protector-access-form-Bob`
6. Any identifiers you set on shortcodes you use in a specific post should be unique to that post (see Note 5).
