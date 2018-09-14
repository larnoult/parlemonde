=== Force Admin Color Scheme ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin colors, color scheme, admin, staging, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.1
Tested up to: 4.9
Stable tag: 1.1.1

Force a single admin color scheme for all users of the site.

== Description ==

Though it is typically an individually configurable aspect of WordPress, there are times when forcing a single admin color scheme upon all users of a site can be warranted, such as to:

* Provide a unique backend color scheme for multiple sites used by the same set of users to reinforce the difference between the sites.
* Clearly denote backend differences between a production and staging/test instance of a site. Especially given that in this situation with the same plugins active and often the same data present, it can be easy to get mixed up about what site you're actually on.
* Force a site branding appropriate color scheme.
* Crush the expression of individuality under your iron fist.

Additionally, the plugin removes the "Admin Color Scheme" profile setting from users who don't have the capability to set the admin color scheme globally since being able to set its value gives them the false impression that it may actually apply.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/force-admin-color-scheme/) | [Plugin Directory Page](https://wordpress.org/plugins/force-admin-color-scheme/) | [GitHub](https://github.com/coffee2code/force-admin-color-scheme/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `force-admin-color-scheme.zip` inside the plugins directory for your site (typically `/wp-content/plugins/`). Or install via the built-in WordPress plugin installer)
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. As an admin, edit your own profile (Users -> Your Profile) and choose the Admin Color Scheme you want to apply to all users by setting the color scheme for yourself.
4. Check the "Force this admin color scheme on all users?" checkbox and then save the update to your profile.


== Screenshots ==

1. A screenshot of the profile page for an administrative user who has the checkbox to force an admin color scheme on users.


== Frequently Asked Questions ==

= Why isn't everyone seeing the same admin color scheme after activating this plugin? =

Have you followed all of the installation instructions? You must configure the forced admin color scheme by setting the color scheme for yourself while also checking the "Force this admin color scheme?" checkbox.

= How do I resume letting users pick their own color schemes? =

Uncheck the "Force this admin color scheme?" when updating an administrative profile, or deactivate the plugin.

= Does this plugin include unit tests? =

Yes.


== Changelog ==

= 1.1.1. (2017-12-22) =
* Fix: Add missing underscore to function call; `_()` should have been `__()`
* New: Add README.md
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: In unit tests, fire `do_init()` manually instead of triggering 'admin_init' to avoid a PHP warning
* Fix: Fix typo in readme
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Update copyright date (2018)
* New: Add a list of ideas for future consideration

= 1.1 (2016-03-09) =
* New: Add `get_setting_name()` as a getter for plugin's setting name and use it everywhere internally instead of referencing private class variable.
* New: Add `set_forced_admin_color()` as a setter for forced admin color. Deletes setting if value is falsey.
* New: Delete plugin setting on uninstall.
* New: Add unit tests.
* Change: Reimplement how the color picker is hidden from non-administrative users.
    * Rewrite `hide_admin_color_input()`.
    * Remove `restore_wp_admin_css_colors()`.
    * Remove private static variable `$_wp_admin_css_colors`.
* Change: When the checkbox is submitted unchecked, delete the forced admin color value.
* Change: When a forced admin color is set, have the checkbox checked.
* Change: Hook 'admin_init' rather than 'init' for initialization.
* Change: Escape use of setting name in markup attributes as an extra precaution.
* Change: Allow class to be defined even when loaded outside the admin.
* Change: Add left padding to input label so the input aligns with color picker colors.
* Change: Remove extra help text associated with checkbox as it was no longer necessary.
* Change: Add support for language packs:
    * Change textdomain from 'c2c-facs' to 'force-admin-color-scheme'.
    * Don't load plugin translations from file.
    * Remove 'Domain Path' from plugin header.
* Change: Add inline docs for class variable.
* Change: Minor code and inline documentation reformatting (spacing).
* New: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Drop support for versions of WP older than 4.1.
* Change: Note compatibility through WP 4.4+.
* Change: Update copyright date (2016).

= 1.0 (2014-09-26) =
* Initial release


== Upgrade Notice ==

= 1.1.1 =
Trivial update: updated unit test bootstrap; noted compatibility through WP 4.9+; added README.md; added GitHub link to readme; updated copyright date (2018)

= 1.1 =
Recommended update.

= 1.0 =
Initial release.
