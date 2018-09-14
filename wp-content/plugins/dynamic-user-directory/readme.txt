=== Dynamic User Directory ===

Contributors: Sarah_Dev
Donate link: http://sgcustomwebsolutions.com/wordpress-plugin-development/
Tags: user directory, BuddyPress, Cimy User Extra Fields, user registration, memberpress, user meta fields, profile fields, member directory, website directory, directory, user listing, users, members, user profile, user profiles
Requires at least: 3.0.1
Tested up to: 4.9.8
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Highly configurable front-end user directory based on user profile and meta fields.

== Description ==

This lightweight yet powerful and feature-rich plugin creates an alphabetically sorted user directory displaying the user meta information you specify. It can show avatars, social icons, mailing address, email address, website, phone, or any other user meta information you wish. It is also fully compatible with BuddyPress, S2Member, and Cimy User Extra Fields plugins.


= Current Features =

The best thing about Dynamic User Directory is the high degree of control you have over the content, formatting, and style. This allows you to create a highly customized directory and integrate it seamlessly into your WordPress theme. The intuitive backend settings interface is designed to help you get your directory up and running quickly. Features include: 

1. Full compatibility with BuddyPress Extended Profile, S2Member Custom Fields, Cimy User Extra Fields, and many other membership plugins.
2. Sort by user last name or user display name 
3. Specify which user meta fields to display (up to 10)
4. Hide users with specified user roles
5. Include or exclude specific users
6. Optionally hyperlink the user name and avatar to their WP author page or BuddyPress profile page
7. Enjoy a fully responsive display for smaller screen sizes
8. Optionally show a search box to quickly locate a user
9. Optionally show pagination to reduce page load times
10. Search by any user meta field with the Meta Fields Search add-on
11. Create multiple directory instances with the Multiple Directories add-on
12. Hide the directory until a search is run with the Hide Directory Before Search add-on
13. Show directory listings in a table format with the Horizontal Layout add-on 
14. Easily format the display in the following ways:

* Show/hide avatars
* Set avatar style (circle, rounded edges, or standard)
* Show/hide listing border
* Set listing border style, color, length, and thickness
* Control font size of all text displayed
* Set the display order of each field
* Control space between alphabet letter links
* Control space between each directory listing
* Choose between showing all users or filtering by selected alphabet letter
* Hyperlink almost any user meta field
* Choose from a variety of field display formats, including phone number, comma delimited lists, & dates
* Display social media link icons (choose from two different icon styles)
* Display address fields as a formatted mailing address

= Add-Ons and Customization =

There are several Dynamic User Directory add-ons available [here](http://sgcustomwebsolutions.com/wordpress-plugin-development/) to enhance and extend your directory. Don't see the functionality you need? You can also hire me to customize the plugin according to your site's specific needs.

= Your Feedback is Valuable! =

If this plugin benefits your website, please take a moment to say thanks by leaving a positive rating and/or review. Did you find a bug? Let me know and I'll fix it ASAP. Have suggestions for improvement? Don't hesitate to email me with your thoughts. Thanks so much! 


== Installation ==

1. Copy the whole dynamic-user-directory subdir into your plugin directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Dynamic User Directory screen to configure the plugin


== Frequently Asked Questions ==

**Q: Does this plugin let me create custom fields on the user profile?** 

A: Dynamic User Directory strictly displays pre-existing user profile information in a searchable directory format. To create custom fields on the user registration/profile page, you’ll need a plugin such as BuddyPress or UserRegistration. Once your custom fields have been added to the user registration/profile page, simply enter the corresponding Meta Key names on the DUD settings page to show them in the directory.

**Q: Why are the avatars misaligned in my directory?**  

A: The Dynamic User Directory plugin should play well with most themes. However, on occasion there may be CSS conflicts with a particular theme that skew the appearance. Currently the only known theme with this problem is the Basic theme. If you are using Basic or any other theme that skews the appearance, contact me and I will work with you to resolve the issue.

**Q: Why is my selected avatar shape not working? ** 

A: Some themes enforce a certain avatar shape. For example, the WordPress Twenty Fifteen and Twenty Sixteen themes do this. The Dynamic User Directory plugin intentionally does not interfere with the sitewide avatar settings, so the theme-enforced avatar shape will take precedence in your user directory display.

**Q: Will this plugin support non-English languages?** 

A: Currently this plugin only supports English. However, there are plans to internationalize it in the near future!

**Q: How do I contact you with questions or suggestions?** 

A: If you have a support question please create a thread under the Support tab of the plugin page. I will be glad to help you resolve the problem ASAP. You can also reach me at sarah@sgcustomwebsolutions.com. I usually reply in under 24 hours.  


== Screenshots ==

1. Directory style example #1
2. Directory style example #2
3. Directory style example #3
4. Directory style example #4
5. Actual Site #1
6. Actual Site #2
7. Actual Site #3
8. 1 of 5: Plugin Settings Page
9. 2 of 5: Plugin Settings Page
10. 3 of 5: Plugin Settings Page
11. 4 of 5: Plugin Settings Page
12. 5 of 5: Plugin Settings Page


== Changelog ==

= 1.0.0 =
- First public release.

= 1.0.1 =
- Updated readme.txt.

= 1.0.2 =
- Added default plugin settings.
- Corrected a spacing issue related to the directory listing display.

= 1.0.3 =
- Security update: Added SQL injection protection.
- Fixed: Display issue related to show/hide user role feature.

= 1.0.4 = 
- Fixed: Spacing issue when a directory listing showed an avatar next to three or less lines of text.
- Fixed: An extra underline was appearing in the empty space next to each letter link for themes that underline hyperlinks.
- Fixed: The city and state of the address fields did not display if there was no zip code.
- New Feature: A fifth meta field was added.
- New Feature: An "Include/Exclude User" setting was added to provide a more customized directory.

= 1.1.0 =
- New Feature: "Space between listings" setting added for greater formatting control
- Fixed: Directory was not displaying results when using the include/exclude or hide user roles feature and sorting by display name
- Fixed: Directory would not work if the default WordPress table name prefix had been changed (thanks, Jaya P!)
- Fixed: Responsive display at very small screen sizes was not properly formatting the avatars

= 1.1.1 =
- Successfully tested on WordPress 4.6 
- New Feature: Added 5 new meta fields for a total of 10 available meta fields (not including address fields).
- New Feature: Added the User Meta Fields dropdown on the settings page so you can select the exact number of fields you need.
- New Feature: Added the Address Fields checkbox so you can hide that section if you do not need it.

= 1.1.2 =
- New Feature: Added a "link to author page" checkbox on the settings page that will hyperlink the user name and avatar to the user's WP Author Page.
- Code cleanup and reorganization 

= 1.1.3 =
- Internal change to code generating alpha links to eliminate potential display issues
- New Feature: Added "Debug Mode" setting that will display a set of debug statements for Admins *ONLY* when turned on. This will help me debug site-specific issues more quickly. 

= 1.1.4 =
- New Feature: Added "Directory Type" dropdown on the settings page. You may select the "all users on one page" option to display the entire directory on one screen. 
- Code enhancement: Minified all CSS files for faster load time.

= 1.1.5 =
- Code successfully tested on WordPress 4.7

= 1.1.6 =
- New Feature: Added "Show search box" checkbox on the settings page that will show a search box at the top of the directory. You may search by user last name or display name, depending on the sort field. 
- Fixed: A message incorrectly stating that there are "no users in the directory" was being displayed when viewing the directory with the following settings: 1) the "Single Page Directory" option was selected, 2) The Sort Field was set to "Display Name," and 3) users were selected for exclusion. 

= 1.1.7 =
- Enhancement: Added five filter hooks to allow developers to extend this plugin
- Fixed: The city/state/zip portion of the address field was not showing if there was no state meta field. It will now show any portion of the city/state/zip address fields that is present. 
- Fixed: Search box was case sensitive, so that you could not search using all lowercase letters. You can now search using upper, lower, or mixed case.

= 1.1.8 =
- Fixed: internal change in the id field of the letter dividers.

= 1.1.9 =
- Fixed: Admin settings page did not set a default value for the letter divider font and fill colors, 
resulting in an error message if you submitted the page without choosing those colors.
- Changed: Removed the Cimy User Extra Fields notification from the settings page for those who do not have that plugin loaded.

= 1.2.0 =
- New Feature: Added Name Display Format on the settings page that will allow you to display name as "First Last" or "Last, First." 
- Enhancement: Expanded the width of the key names listing and sorted it alphabetically for ease of use.
- Enhancement: Added link to the Dynamic User Directory add-ons page. 

= 1.2.1 =
- Fixed: the code variable "$this" was causing fatal error in php 7.1. Changed variable name to correct problem.

= 1.2.2 =
- Fixed: User meta fields that contained arrays would not display properly (e.g. multiple checkbox or radio button values stored in an array). It will now show a list of array items vertically, with one item per line.
- Code enhancement: now storing all settings page options as an array in a single options setting. This will improve performance since every "get_option" call requires a database read.
- New Feature: You can now choose to show Author Page links for all users rather than only for those with posts. This accomodates those who have a custom author.php page that should be shown regardless of the post count.

= 1.2.3 =
- Fixed: Code was generating incorrect Letter Link URLs for certain intranet website confirgurations and for the WordPress "Plain" permalink setting. It will now generate the links correctly. 
- Enhancement: Added code to accommodate the new Meta Fields Search add-on.

= 1.2.4 =
- Fixed: Corrected a null error warning: "Warning: in_array() expects parameter 2 to be array, null given" which may occur for those who do not have the Cimy plugin.

= 1.2.5 =
- Fixed: Corrected a missing </pre> statement when the debug mode is turned on.

= 1.2.6 =
- Enhancement: Added new code to accomodate the new Meta Fields Search add-on.
- Fixed: Search box width was too long. Set new width to 45%.

= 1.2.7 =
- New feature: Added the ability to hyperlink any meta field.
- Enhancement: Added new code to accomodate the new Meta Fields Search add-on.
- Internal code reorganization on the admin settings page. 

= 1.2.8 =
- IMPORTANT: If you have the Meta Fields Search or Alpha Links Scroll add-ons, you should see an update available for each of these on the plugins page. If you do not see these updates, contact me and I will resolve the issue. These should be run in tandem with the Dynamic User Directory update to 1.2.8. 
- Enhancement: Added new code to accomodate the new Multiple Directories add-on.
- Fixed: when showing a dividing border and a letter divider on a single page directory, a dividing border was being displayed just before the letter divider of the single page directory.
- Tweak: set the height of the default user search box to 40px.

= 1.2.9 =
- Fixed: When the Meta Fields Search add-on is installed, and an invalid search value is entered, a PHP notice "Warning: Missing argument 2 for dud_build_srch_form_custom()" appears at the top of the page.

= 1.3.0 =
- Fixed: Alpha links were not always properly created when the site uses a custom permalink structure, resulting in a 404 error.

= 1.3.1 =
- Successfully tested against WP 4.8
- Fixed: Letter divider was showing up on the Single Page Directory even when "No letter divider" was selected.
- New Feature: You can now link the user name and avatar to their BuddyPress profile page in addition to the WP Author Page. 

= 1.3.2 =
- Code clean-up: properly initialized all variables to eliminate the PHP warning notices that were being shown for this plugin when DEBUG = true in the wp_config.php file.

= 1.3.3 =
- New Feature: DUD is now fully compatible with BuddyPress Extended Profile fields
- New Feature: DUD is now fully compatible with S2Member Custom fields

= 1.3.4 =
- Internal code tweak that allows developers to show only the search box and hide the directory unless a search is run.
- Added two filters, dud_set_avatar_link and dud_set_user_profile_link, so that developers can manually set the links to the user profile/author page if needed.  

= 1.3.5 =
- Fixed: When user roles with a space in the name are selected for hiding, DUD did not hide those roles. It will now hide all selected roles properly.
- Enhancement: Added two new filters, dud_search_err and dud_no_users_msg, so that developers can customize the plugin error messages shown to the viewer
- Multiple Directories code cleanup: Internal reorganization to handle loading a selected directory instance more efficiently in core.php 

= 1.3.6 =
- Released 8/29/17
- Enhancement: Redesigned and reorganized the admin settings page for improved aesthetics, readability, and ease of use.
- Fixed: When text with an apostrophe is entered on the BuddyPress profile, a slash was being shown in the directory next to the apostrophe. The text is now shown correctly without the extra slash.

= 1.3.7 =
- Released 11/12/17
- New Feature: Added four new DUD filters: dud_modify_letters, dud_format_key_val_array, dud_srch_fld_placeholder_txt, and dud_modify_address_flds
- New Feature: Added one new add-on filter: dud_hide_dir_before_srch
- New Feature: Added the ability to control the avatar size.
- New Feature: Added new letter divider options: Letter Only, Letter with Bottom Border, and Letter with Top and Bottom Border
- Fixed: The CSS for the directory search box was shrinking the box's height in some themes. This has been corrected.
- Internal code reorganization to streamline certain actions

= 1.3.8 =
- Released 1/7/18
- Successfully tested against WP 4.9.1
- New Feature: Added a Social Meta Fields section that will format your social media links as a row of icons.
- New Feature: Added three new DUD filters: dud_set_user_email, dud_set_user_email_display, and dud_modify_social_flds
- Fixed: When accessing the S2Member meta field name that holds all custom fields, the "wp_" prefix was hard coded. This has been changed to pull the prefix dynamically from the config file in case it has been changed. 
- Fixed: The DUD settings page was calling the deprecated function "screen_icon()," which generates an error notice when WP Debug is turned on. This call has been removed.

= 1.3.9 =
- Released 1/22/18
- Successfully tested against WP 4.9.2
- Fixed: Adjusted the new dud_modify_social_flds filter to send all necessary parameters.
- Fixed: Removed the <BR> that pushes the value below the label for meta fields containing arrays with only one item.

= 1.4.0 =
- Released 2/7/18
- Successfully test against WP 4.9.4
- Fixed: changed the sql for loading the "user include/exclude" listbox on the settings page when there are 1000+ users, to prevent the page from hanging.
- Fixed: eliminated the "undefined index" warning notices appearing on some sites for the new Social meta fields when wp_debug is set to "true".
- Enhancement: updated the users include/exclude and user roles exclude listboxes to multi-selectable dropdowns with search capability for ease of use.
- Enhancement: added a "country" field to the Address meta fields section.

= 1.4.1 =
- Released 2/28/18
- Fixed: Corrected the problem with the Multiple Directories add-on where you couldn't add, delete, or modify dirctory instances on the settings page using the Safari browser.
- Fixed: Corrected the problem on some sites where user profile pics were being hidden for smaller screen sizes on the vertical directory. 

= 1.4.2 =
- Released 4/24/18
- Successfully test against WP 4.9.5
- Fixed: Eliminated an "undefined index" warning notice that appeared on the DUD Settings page for some users for the ud_table_cell_padding
and ud_show_table_stripes fields of the horizontal directory when wp_debug is set to "true".
- Enhancement: Expanded the dud_after_load_letters filter parameter list for greater flexibility.
- New Feature: Added the new DUD setting "Format Meta Field As" dropdown with options to format the field as a hyperlink (new tab or same window),
muliple value list (comma delimited or bulleted), or phone number.

= 1.4.3 =
- Released 6/20/18
- Fixed: Corrected the problem where fields with multiple checkboxes stored as key-value pairs were not displaying in the directory. The problem was reported by several sites using the MemberPress plugin.
- Enhancement: Added new format options to the "Format Meta Field As" drop down on the settings page: 
1) Multiple checkboxes => Show label only
2) Single checkbox => Show label only
3) Several Date/time field format options

= 1.4.4 =
- Released 8/15/18
- Successfully tested against WP 4.9.8
- New Feature: Pagination has been added and may be configured under the new "Alphabet and Pagination Link Settings" section. This affects three DUD add-ons: Alpha Links Scroll, Meta Fields Search, and Horizontal Layout. These add-ons must be updated to the latest versions for pagination to work properly when using them.
- New Feature: Ability to change the selected alphabet letter link color. This may be configured under the new "Alphabet and Pagination Link Settings" section.
- Enhancement: Ability to link to a user's BuddyPress profile page as opposed to the BP member activity page.
- Fixed: Corrected problem with some themes skewing the avatar when the avatar display size is set in DUD.
- Fixed: Corrected problem where "undefined index" warning notices were being displayed for var_1 and var_2 when wp_debug is turned on.
- Fixed: Changed the default "Last Name" search box width from 45% to 350px to eliminate the possibility of the field being too long in some themes.
- Reorganized SQL code and added other infrastucture in preparation for the Custom Sort Field add-on