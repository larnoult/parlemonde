=== Capability Manager Enhanced===
Contributors: txanny, kevinB
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=JWZVFUDLLYQBA
Tags: roles, capabilities, manager, editor, rights, role, capability, types, taxonomies, network, multisite, default
Requires at least: 3.1
Tested up to: 4.9.7
Stable tag: 1.5.9

A simple way to manage WordPress roles and capabilities.

== Description ==

Capability Manager Enhanced provides a simple way to manage WordPress role definitions (Subscriber, Editor, etc.). View or change the capabilities of any role, add new roles, copy existing roles into new ones, and add new capabilities to existing roles. Now supports capability negation and role networking.

= Features: =

* Create roles
* Manage role capabilities
* Supports negation: set any capability to granted, not granted, or blocked
* Copy any role all network sites
* Mark any role for auto-copy to future network sites
* Backup and restore Roles and Capabilities to revert your last changes.
* Revert Roles and Capabilities to WordPress defaults. 
 
Role management can also be delegated:

* Only users with 'manage_capabilities' can manage them. This capability is created at install time and assigned to Administrators.
* Administrator role cannot be deleted.
* Non-administrators can only manage roles or users with same or lower capabilities.

Enhanced and supported by <a href="http://agapetry.net">Kevin Behrens</a> since July 2012. The original Capability Manager author, Jordi Canals, has not updated the plugin since early 2010. Since he was unreachable by web or email, I decided to take on the project myself.

The main change from the original plugin is an improved UI which organizes capabilities:

* by post type
* by operation (read/edit/delete)
* by origin (WP core or plugin)

Capability Manager Enhanced also adds <a href="http://wordpress.org/plugins/press-permit-core">Press Permit</a> plugin integration:

* easily specify which post types require type-specific capability definitions
* show capabilities which Press Permit adds to the role by supplemental type-specific role assignment
  
= Languages included: =

* English
* Catalan
* Spanish
* Belorussian *by <a href="http://antsar.info/" rel="nofollow">Ilyuha</a>*
* German *by <a href="http://great-solution.de/" rel="nofollow">Carsten Tauber</a>*
* Italian *by <a href="http://gidibao.net" rel="nofollow">Gianni Diurno</a>*
* Russian *by <a href="http://www.fatcow.com" rel="nofollow">Marcis Gasuns</a>*
* Swedish *by <a href="http://jenswedin.com/" rel="nofollow">Jens Wedin</a>
* POT file for easy translation to other languages included.

== Installation ==

= System Requirements =

* **Requires PHP 5.2**. Older versions of PHP are obsolete and expose your site to security risks.
* Verify the plugin is compatible with your WordPress Version. If not, plugin will not load.

= Installing the plugin =

1. Unzip the plugin archive.
1. Upload the plugin's folder to the WordPress plugins directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Manage the capabilities on the 'Capabilities' page on Users menu.
1. Enjoy your plugin!

== Screenshots ==

1. Users Menu.
2. View or Modify capabilities for a role.
3. Network: copy role to existing or future sites.
4. Actions on roles.
5. Permissions Menu (Press Permit integration).
6. View or Modify capabilities for a role (with Press Permit Pro).
7. Force type-specific capabilities (Press Permit integration).
8. Backup/Restore tool.

== Frequently Asked Questions ==

= How can I grant capabilities for a custom post type =
The custom post type must be defined to impose type-specific capability requirements.  This is normally done by setting the "capability type" property equal to the post type name.

= I have configured a role to edit a custom post type. Why do the users still see "You are not allowed the edit this post?" when they try to save/submit a new post? =

Probably because your custom post type definition not having map_meta_cap set true. If you are calling register_post_type manually, just add this property to the options array. Unfortunately, none of the free CPT plugins deal with this important detail. 

= Even after I added capabilities, WordPress is not working the way I want =

Keep in mind that this plugin's purpose is to conveniently view and modify the capabilities array stored for each WordPress role.  It is not responsible for the implementation of those capabilities by the WordPress core or other plugins.

= Where can I find more information about this plugin, usage and support ? =

* If you need help, <a href="http://wordpress.org/tags/capsman-enhanced">ask in the Support forum</a>.  If your issue pertains to the enforcement of assigned capabilities, I am not the primary support avenue.  In many cases, I will offer a suggestion, but please don't give me negative feedback for not providing free consulting.

== License ==

Copyright 2009, 2010 Jordi Canals
Copyright 2013-2018, Kevin Behrens

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.

== Changelog ==

= 1.5.9 =
  * Fixed : Potential vulnerability in wp-admin (but exposure was only to users with role editing capability)

= 1.5.8 =
  * Fixed : PHP warning for deprecated function WP_Roles::reinit
  * Change : Don't allow non-Administrator to edit Administrators, even if Administrator role level is set to 0
  
= 1.5.7 =
  * Change : Revert menu captions to previous behavior ("Permissions > Role Capabilities" if Press Permit Core is active, otherwise "Users > Capabilities")

= 1.5.6 =
  * Fixed : Correct some irregularities in CME admin menu item display

= 1.5.5 =
  * Fixed : User editing was improperly blocked in some cases

= 1.5.4 =
  * Fixed : Non-administrators' user editing capabilities were blocked if Press Permit Core was also active
  * Fixed : Non-administrators could not edit other users with their role (define constant CME_LEGACY_USER_EDIT_FILTER to retain previous behavior)
  * Fixed : Non-administrators could not assign their role to other users (define constant CME_LEGACY_USER_EDIT_FILTER to retain previous behavior)
  * Lang : Changed text domain for language pack conformance

= 1.5.3 =
  * Fixed : On single-site installations, non-Administrators with delete_users capability could give new users an Administrator role (since 1.5.2) 
  * Fixed : Deletion of a third party plugin role could cause users to be demoted to Subscriber inappropriately
  * Compat : Press Permit Core - Permission Group refresh was not triggered if Press Permit Core is inactive when CME deletes a role definition
  * Compat : Support third party display of available capabilities via capsman_get_capabilities or members_get_capabilities filter
  * Change : If user_level of Administrator role was cleared, non-Administrators with user editing capabilities could create/edit/delete Administrators.  Administrator role is now implicitly treated as level 10.
  * Fixed : CSS caused formatting issues around wp-admin Update button on some installations
  * Perf : Don't output wp-admin CSS on non-CME screens
  * Lang : Fixed erroneous text_domain argument for numerous strings
  * Lang : Updated .pot and .po files
  
= 1.5.2 =
  * Fixed : Network Super Administrators without an Administrator role on a particular site could not assign an Administrator role to other users of that site

= 1.5.1 =
  * Fixed : Non-administrators with user editing capabilities could give new users a role with a higher level than their own (including Administrator)

= 1.5 =
  * Feature : Support negative capabilities (storage to wp_roles array with false value)
  * Feature : Multisite - Copy a role definition to all current sites on a network
  * Feature : Multisite - Copy a role definition to new (future) sites on a network
  * Feature : Backup / Restore tool requires "restore_roles" capability or super admin status
  * Fixed : Role reset to WP defaults did not work, caused a PHP error / white screen
  * Change : Clarified English captions on Backup Tool screen
  * Fixed : Term deletion capability was not included in taxonomies grid even if defined
  * Fixed : jQuery notices for deprecated methods on Edit Role screen
  * Compat : Press Permit - if a role is marked as hidden, also default it for use by PP Pro as a Pattern Role (when PP Collaborative Editing is activated and Advanced Settings enabled)
  * Change : Press Permit promotional message includes link to display further info
  
= 1.4.10 =
  * Perf :  Eliminated unused framework code (reduced typical wp-admin memory usage by 0.6 MB)
  * Fixed : Failure to save capability changes, on some versions of PHP
  * Compat : Press Permit - PHP Warning on role save
  * Compat : Press Permit - PHP Warning on "Force Type-Specific Capabilities" settings update
  * Compat : Press Permit - "supplemental only" option stored redundant entries
  * Compat : Press Permit - green background around capabilities which 
  * Compat : Press Permit - PHP Warning on "Force Type-Specific Capabilities" settings update
  * Maint  : Stop using $GLOBALS superglobal
  * Change : Reduced download size by moving screenshots to assets folder of project folder

= 1.4.9 =
  * Fixed : Role capabilities were not updated / refreshed properly on multisite installations
  * Feature : If create_posts capabilities are defined, organize checkboxes into a column alongside edit_posts
  * Feature : "Use create_posts capability" checkbox in sidebar auto-defines create_posts capabilities (requires Press Permit)
  * Compat : bbPress + Press Permit - Modified bbPress role capabilities were not redisplayed following save, required reload
  * Compat : bbPress + Press Permit - Adding a capability via the "Add Cap" textbox caused the checkbox to be available but not selected
  * Compat : Press Permit - "supplemental only" option was always enabled for newly created and copied roles, regardless of checkbox setting near Create/Copy button
  
= 1.4.8 =
  * Compat : bbPress + Press Permit - "Add Capability" form failed when used on a bbPress role, caused creation of an invalid role

= 1.4.7 =
  * Compat : Press Permit - flagging of roles as "supplemental assignment only" was not saved

= 1.4.6 =
  * Compat : bbPress 2.2 (supports customization of dynamic forum role capabilities)
  * Compat : Press Permit + bbPress - customized role capabilities were not properly maintained on bbPress activation / deactivation, in some scenarios
  * Fixed : Role update and copy failed if currently stored capability array is corrupted
 
= 1.4.5 =
  * Fixed : Capabilities were needlessly re-saved on role load
  * Fixed : Capability labels in "Other WordPress" section did not toggle checkbox selection
  * Press Permit integration: If capability is granted by the role's Permit Group, highlight it as green with a descriptive caption title, but leave checkbox enabled for display/editing of role defintion setting (previous behavior caused capability to be stripped out of WP role definition under some PP configurations)
  
= 1.4.4 =
  * Fixed : On translated sites, roles could not be edited
  * Fixed : Menu item change to "Role Capabilities" broke existing translations

= 1.4.3 =
  * Fixed : Separate checkbox was displayed for cap->edit_published_posts even if it was defined to the be same as cap->edit_posts
  * Press Permit integration: automatically store a backup copy of each role's last saved capability set so they can be reinstated if necessary (currently for bbPress)

= 1.4.2 =
  * Language: updated .pot file
  * Press Permit integration: roles can be marked for supplemental assignment only (and suppressed from WP role assignment dropdown, requires PP 1.0-beta1.4)

= 1.4.1 =
  * https compatibility: use content_url(), plugins_url()
  * Press Permit integration: if role definitions are reset to WP defaults, also repopulate PP capabilities (pp_manage_settings, etc.)

= 1.4 =
  * Organized capabilities UI by post type and operation
  * Editing UI separates WP core capabilities and 3rd party capabilities
  * Clarified sidebar captions
  * Don't allow a non-Administrator to add or remove a capability they don't have
  * Fixed : PHP Warnings for unchecked capabilities
  * Press Permit integration: externally (dis)enable Post Types, Taxonomies for PP filtering (which forces type-specific capability definitions)
  * Show capabilities which Press Permit adds to the role by supplemental type-specific role assignment
  * Reduce memory usage by loading framework and plugin code only when needed
  
= 1.3.2 = 
  * Added Swedish translation.

= 1.3.1 =
  * Fixed a bug where administrators could not create or manage other administrators.
  
= 1.3 =
  * Cannot edit users with more capabilities than current user.
  * Cannot assign to users a role with more capabilities than current user.
  * Solved an incompatibility with Chameleon theme.
  * Migrated to the new Alkivia Framework.
  * Changed license to GPL version 2.

= 1.2.5 =
  * Tested up to WP 2.9.1.

= 1.2.4 =
  * Added Italian translation.

= 1.2.3 =
  * Added German and Belorussian translations.

= 1.2.2 =
  * Added Russian translation.

= 1.2.1 =
  * Coding Standards.
  * Corrected internal links.
  * Updated Framework.

= 1.2 =
  * Added backup/restore tool.

= 1.1 =
  * Role deletion added.

= 1.0.1 =
  * Some code improvements.
  * Updated Alkivia Framework.

= 1.0 =
  * First public version.

== Upgrade Notice ==

= 1.5.1 =
Fixed : Non-administrators with user editing capabilities could add new Administrators

= 1.3.2 = 
Only Swedish translation.

= 1.3.1 =
Bug fixes.
  
= 1.3 =
Improved security esiting users. You can now create real user managers. 
