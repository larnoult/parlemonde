=== Import users from CSV with meta ===
Contributors: carazo, hornero
Donate link: http://paypal.me/codection
Tags: csv, import, importer, meta data, meta, user, users, user meta,  editor, profile, custom, fields, delimiter, update, insert
Requires at least: 3.4
Tested up to: 4.9.8
Stable tag: 1.11.3.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to import users using CSV files to WP database automatically including custom user meta

== Description ==

Clean and easy-to-use Import users plugin. It includes custom user meta to be included automatically from a CSV file and delimitation auto-detector. It also is able to send a mail to each user imported and all the meta data imported is ready to edit into user profile.

*	Import CSV file with users directly to your WordPress
*	Import thousends of users in only some seconds
*	You can also import meta-data like data from WooCommerce customers using the correct meta_keys
*	Send a mail to every new user
*	Use your own 
*	You can also update data of each user
*	Assing a role
*	Create a cron task to import users periodically
*	Edit the metadata (you will be able to edit the metadata imported using metakeys directly in the profile of each user)
*	Read our documentation
*	Ask anything in support forum, we try to give the best support

In Codection we have more plugins, please take a look to them.

*	[RedSys Gateway for WooCommerce Pro a plugin to connect your WooCommerce to RedSys](http://codection.com/producto/redsys-gateway-for-woocommerce) (premium)
*	[Ceca Gateway for WooCommerce Pro a plugin to connect your WooCommerce to Ceca](http://codection.com/producto/ceca-gateway-for-woocommerce-pro/) (premium)
*	[BBVA Bancomer for WooCommerce Pro a plugin to connect your WooCommerce to BBVA Bancomer](http://codection.com/producto/bbva-bancomer-mexico-gateway-for-woocommerce-pro/) (premium)
*	[RedSys Button for WordPress a plugin to receive payments using RedSys in WordPress without using WooCommerce](http://codection.com/producto/redsys-button-wordpress/) (premium)
*	[RedSys Gateway for WP Booking Calendar Pro a plugin to receive payments using RedSys in WordPress using WP Booking Calendar Pro](https://codection.com/producto/redsys-gateway-for-wp-booking-calendar-pro/) (premium)
*	[Clean Login a plugin to create your own register, log in, lost password and update profile forms](https://wordpress.org/plugins/clean-login/) (free)

## **Basics**

*   Import users from a CSV easily
*   And also extra profile information with the user meta data (included in the CSV with your custom fields)
*   Just upload the CSV file (one included as example)
*   All your users will be created/updated with the updated information, and of course including the user meta
*   Autodetect delimiter compatible with `comma , `, `semicolon ; ` and `bar | `

## **Usage**

Once the plugin is installed you can use it. Go to Tools menu and there, there will be a section called _Insert users from CSV_. Just choose your CSV file and go!

### **CSV generation**

You can generate CSV file with all users inside it, using a standar spreadsheet software like: Microsoft Excel, LibreOffice Calc, OpenOffice Calc or Gnumeric.

You have to create the file filled with information (or take it from another database) and you will only have to choose CSV file when you "Save as..." the file. As example, a CSV file is included with the plugin.

### **Some considerations**

Plugin will automatically detect:

* Charset and set it to **UTF-8** to prevent problems with non-ASCII characters.
* It also will **auto detect line-ending** to prevent problems with different OS.
* Finally, it will **detect the delimiter** being used in CSV file ("," or ";" or "|")

== Screenshots ==

1. Plugin link from dashboard
2. Plugin page
3. CSV file structure
4. Users imported
5. Extra profile information (user meta)

== Changelog ==

= 1.11.3.8.1 =
*	Fixed bug thanks to @xenator for discovering the bug (https://wordpress.org/support/topic/uncaught-error-while-importing-users/#post-10618130)

= 1.11.3.8 =
*	Fixed mail sending in frontend import
*	Now you can activate users with WP Members in frontend import
*	Some fixes and warnings added

= 1.11.3.7 =
*	Fixes and improvements thanks to @malcolm-oph

= 1.11.3.6 =
*	Role import working in cron jobs

= 1.11.3.5 =
*	SMTP tab hidden for user which are not using this option

= 1.11.3.4 =
*	Bug fixed: thanks to @oldfieldmike for reporting and fixing a bug present when BuddyPress was active (https://wordpress.org/support/topic/bp_xprofile_group/#post-10265833)

= 1.11.3.3 =
*	Added compatibility to import levels from Indeed Ultimate Membership Pro
*	Fixed role problems when importing

= 1.11.3.2 =
*	Patreon link included and some other improvements to make easier support this develop
*	Deprecated notices included about SMTP settings in this plugin

= 1.11.3.1 =
*	Thanks to Sebastian Mellmann(@xenator) a bug have been solved in password management in new users

= 1.11.3 =
*	Thanks to @xenator you can now import users with Allow Multiple Accounts with same Mail via cron

= 1.11.2 =
*	Problem with WordPress default emails fixed

= 1.11.1 =
*	Sidebar changed
*	Readme completed

= 1.11 =
*	You can now import users from the frontend using a shortcode thanks to Nelson Artz Group GmbH & Co. KG

= 1.10.13 =
*	You can now import User Groups (https://wordpress.org/plugins/user-groups/) and assign them to the users

= 1.10.12 =
*	You can now import WP User Groups (https://es.wordpress.org/plugins/wp-user-groups/) and assign them to the users thanks to the support of Arturas & Luis, Lda.

= 1.10.11.1 =
*	Debug notice shown fixed (thanks for submiting the bug @anieves (https://wordpress.org/support/topic/problem-using-wp-members-with-import-users-from-csv-2/#post-10035037)

= 1.10.11 =
*	Administrator are not deleted in cron task
*	Some hashed passwords was not being imported correctly because of wp_unslash() function into wp_insert_user(), issue fixed

= 1.10.10 =
*	Thanks to Attainable Adventure Cruising Ltd now the system to import passwords hashed directly from the CSV has been fixed
*	Thanks to Kevin Price-Ward and Peri Lane now the system does not include the default role when creating a new user
*	Plugin tested up to WordPress 4.9.4

= 1.10.9.1 =
*	Thanks to @lucile-agence-pulsi for reporting a bug (https://wordpress.org/support/topic/show-extra-profile-fields/) now this is solved

= 1.10.9 =
*	Thanks to the support of Studio MiliLand (http://www.mililand.com) we can now import data to Paid Membership Pro Plugin

= 1.10.8.2 =
*	Thanks to @Carlos Herrera we can now import date fields from BuddyPress

= 1.10.8.1 =
*	Bug fixed

= 1.10.8 =
*	New system for include addons
* 	You can now import data from WooCommerce Membership thanks to Lukas from Kousekmusic.cz
*	Tested up to WordPress 4.9

= 1.10.7.5 =
* 	Bug solved in cron import, now mails not being sent to user who are being updated unless you activate those mails

= 1.10.7.4 =
* 	Plugin now remember if user has selected or not mail sending when doing a manual import, to select by default this option next time

= 1.10.7.3 =
* 	Some of the plugins options are disabled by default to prevent unwanted mail sending

= 1.10.7.2 =
* 	Improve email notification disable

= 1.10.7.1 =
* 	Sending mail in standard import bug solved, thanks to @manverupl for the error report.

= 1.10.7 =
*	New feature thanks to Todd Zaroban (@tzarob) now you can choose if override or not current roles of each user when you are updating them
*	Problem solved in repeated email module thanks to @damienper (https://wordpress.org/support/topic/error-in-email_repeated-php/)
* 	Problem solved in mail sending with cron thanks to @khansadi (https://wordpress.org/support/topic/no-email-is-sent-to-new-users-when-created-via-corn-import/)

= 1.10.6.9 =
*	Thanks to Peri Lane from Apis Productions you can now import roles from CSV. Read documentation to see the way to work.

= 1.10.6.8.1 =
*	Thanks to @fiddla for debugging all this, as update_option with a value equals to true is saved as 1 in the database, we couldn't use the ==! or === operator to see if the option was active or not. Sorry for so many updates those days with this problems and thanks for the debugging

= 1.10.6.8 =
*	Bug fixed (now yes) when moving file including date and time thanks to @fiddla

= 1.10.6.7 =
*	Bug fixed when moving file including date and time

= 1.10.6.6 =
*	Bug fixed thanks to @ov3rfly (https://wordpress.org/support/topic/wrong-path-to-users-page-after-import/)
*	Documentation also included in home page of the plugins thanks to suggestions and threads in forum

= 1.10.6.5 =
*	If multisite is enabled it adds the user to the blog thanks to Rudolph Koegelenberg
*	Tested up to 4.8

= 1.10.6.4 =
*	Documentation fixed: if user id is present in the CSV but not in the database, it cannot be used to create a new user

= 1.10.6.3 =
*	New hook added do_action('post_acui_import_single_user', $headers, $data, $user_id );

= 1.10.6.2 =
*	Added documentation about locale and BuddyPress Extendend Profile
*	Header changed to avoid any problem about plugin header

= 1.10.6.1 =
*	Fix error in importer.php about delete users (https://wordpress.org/support/topic/wp_delete_user-undefined/#post-8925051)

= 1.10.6 =
*	Now you can hide the extra profile fields created with the plugin thanks to Steph O'Brien (Ruddy Good)

= 1.10.5 =
*	Now you can import list of elements using :: as separator and it can also be done in BuddyPress profile fields thanks to Jon Eiseman
*	Fixes in SMTP settings
*	SMTP settings now is a new tab

= 1.10.4 =
*	Now you can assign BuddyPress groups and assign roles in import thanks to TNTP (tntp.org)
* 	Import optimization
*	Readme fixed

= 1.10.3.1 =
*	Bug fixed in SMTP settings page

= 1.10.3 =
*	Plugin is now prepared for internacionalization using translate.wordpress.org

= 1.10.2.2 =
*	German translation fixed thanks to @mfgmicha
*	locale now is considered a data from WordPress user so it won't be shown in profiles

= 1.10.2.1 =
*	German translation fixed thanks to @barcelo
*	System compatibility updated

= 1.10.2 =
* 	New User Approve support fixed thanks to @stephanemartinw (https://wordpress.org/support/topic/new-user-approve-support/#post-8749012)

= 1.10.1 =
* 	Plugin can now import serialized data.
*	New filter added: $data[$i] = apply_filters( 'pre_acui_import_single_user_single_data', $data[$i], $headers[$i], $i); now you can manage each single data for each user, maybe easier to use than pre_acui_import_single_user_data


= 1.9.9.9 =
* 	Now you can automatically rename file after move it. Then you won't lost any file you have imported (thanks to @charlesgodwin)

= 1.9.9.8 =
* 	Password bug fixed. Now it works as it should (like it is explained in documentation)

= 1.9.9.7 =
* 	Bug fixed in importer now value 0 is not considered as empty thanks to @lafare (https://wordpress.org/support/topic/importing-values-equal-to-0/#post-8609191)

= 1.9.9.6 =
* 	From now we are going to keep old versions available in repository
*	We don't delete loaded columns (and fields) when you deactivate the plugin
*	Password is not auto generated when updating user in order to avoid problems (missing password column and update create new passwords and use to create problems)

= 1.9.9.5 =
*	Now you can set the email to empty in each row thanks to @sternhagel

= 1.9.9.4 =
*	German language added thanks to Wolfgang Kleinrath
*	Added conditional to avoid error on mail sending

= 1.9.9.3 =
*	Now you can choose if you want to not assign a role to users when you are making an import cron

= 1.9.9.2 =
*	Now you can choose if you want to assign to some user the posts of the users that can be deleted in cron task

= 1.9.9.1 =
*	French translation added thanks to @momo-fr

= 1.9.9 =
*	Plugin now is localized using i18n thanks to code provided by Toni Ginard @toniginard

= 1.9.8.1 =
*	Bug fixed in cron import, nonce conditional check, thanks to Ville Kokkala for showing the bug

= 1.9.8 =
*	Password reset url is now available to include in body email thanks to Mary Wheeler (https://wordpress.org/support/users/ransim/)

= 1.9.7 =
*	Thanks to Bruce MacPherson we can now choose if we don't want update users roles when importing data if user exist
*	Clearer English thanks to Bruce MacPherson

= 1.9.6 =
*	Thanks to Jason Lewis we can now choose if we don't want update users when importing data if user exist

= 1.9.5 =
*	Important security fixes added thanks to pluginvulnerabilities.com

= 1.9.4.6 =
*	New filter added, thanks to @burton-nerd

= 1.9.4.5 =
*	Renamed function to avoid collisions thanks to the message of Jason Lewis

= 1.9.4.4 =
*	Fix for the last one, we set true where it was false and vice versa

= 1.9.4.3 =
*	We try to make it clear to choose if mails (the one we talked in 1.9.4.2) are being sent or not

= 1.9.4.2 =
*	Automatic WordPress emails sending deactivated by default when user is created or updated, thanks to Peter Gariepy

= 1.9.4.1 =
*	wpautop added again

= 1.9.4 =
*	user_pass can be imported directly hashed thanks to Bad Yogi

= 1.9.3 =
*	Now you can move file after cron, thanks to Yme Brugts for supporting this new feature

= 1.9.2 =
*	New hook added, thanks to borkenkaefer

= 1.9.1 =
*	Fix new feature thanks to bixul ( https://wordpress.org/support/topic/problems-with-user-xxx-error-invalid-user-id?replies=3#post-8572766 )

= 1.9 =
*	New feature thanks to Ken Hagen - V3 Insurance Partners LLC, now you can import users directly with his ID or update it using his user ID, please read documentation tab for more information about it
*	New hooks added thank to the idea of borkenkaefer, in the future we will include more and better hooks (actions and filters)
*	Compatibility with New User Approve fixed

= 1.8.9 =
*	Lost password link included in the mail template thanks to alex@marckdesign.net

= 1.8.8 =
*	Checkbox included in order to avoid sending mail accidentally on password change or user updated.

= 1.8.7.4 =
*	Documentation updated.

= 1.8.7.3 =
*	Autoparagraph in email text to solve problem about all text in the same line.
*	Tested up to 4.5.1

= 1.8.7.2 =
*	Bug in delete_user_meta solved thanks for telling us lizzy2surge

= 1.8.7.1 =
*	Bug in HTML mails solved

= 1.8.7 =
*	You can choose between plugin mail settings or WordPress mail settings, thanks to Awaken Solutions web design (http://www.awakensolutions.com/)

= 1.8.6 =
*	Bug detected in mailer settings, thanks to Carlos (satrebil@gmail.com)

= 1.8.5 =
*	Include code changed, after BuddyPress adaptations we break the SMTP settings when activating

= 1.8.4 =
*	Labels for mail sending were creating some misunderstandings, we have changed it

= 1.8.3 =
*	Deleted var_dump message to debug left accidentally

= 1.8.2 =
*	BuddyPress fix in some installation to avoid a fatal error

= 1.8.1 =
*	Now you have to select at least a role, we want to prevent the problem of "No roles selected"
*	You can import now BuddyPress fields with this plugin thanks to André Ihlar

= 1.8 =
*	Email template has an own custom tab thanks to Amanda Ruggles
*	Email can be sent when you are doing a cron import thanks to Amanda Ruggles

= 1.7.9 =
*	Now you can choose if you want to send the email to all users or only to creted users (not to the updated one) thanks to Remy Medranda
*	Compatibility with New User Approve (https://es.wordpress.org/plugins/new-user-approve/) included thanks to Remy Medranda

= 1.7.8 =
*	Metadata can be sent in the mail thanks to Remy Medranda

= 1.7.7 =
*	Bad link fixed and new links added to the plugin

= 1.7.6 =
*	Capability changed from manage_options to create_users, this is a better capatibily to this plugin

= 1.7.5 =
*	Bug solved when opening tabs, it were opened in incorrect target
*	Documentation for WooCommerce integration included

= 1.7.4 =
*	Bug solved when saving path to file in Cron Import (thanks to Robert Zantow for reporting)
*	New tabs included: Shop and Need help
*	Banner background from WordPress.org updated

= 1.7.3 =
*	Users which are not administrator now can edit his extra fields thanks to downka (https://wordpress.org/support/topic/unable-to-edit-imported-custom-profile-fields?replies=1#post-7595520)

= 1.7.2 =
*	Plugin is now compatible with WordPress Access Areas plugin (https://wordpress.org/plugins/wp-access-areas/) thanks to Herbert (http://remark.no/)
*	Added some notes to clarify the proper working of the plugin.

= 1.7.1 =
*	Bug solved. Thanks for reporting this bug: https://wordpress.org/support/topic/version-17-just-doesnt-work?replies=3#post-7538427

= 1.7 =
*	New GUI based on tabs easier to use
*	Thanks to Michael Lancey ( Mckenzie Chase Management, Inc. ) we can now provide all this new features:	
*	File can now be refered using a path and not only uploading.
*	You can now create a scheduled event to import users regularly.

= 1.6.4 =
*	Bugs detected and solved thanks to a message from Periu Lane and others users, the problem was a var bad named.

= 1.6.3 =
*	Default action for empty values now is: leave old value, in this way we prevent unintentional deletions of meta data.
*	Included donate link in plugin.

= 1.6.2 =
*	Thanks to Carmine Morra (carminemorra.com) for reporting problems with <p> and <br/> tags in body of emails.

= 1.6.1 =
*	Thanks to Matthijs Mons: now this plugin is able to work with Allow Multiple Accounts (https://wordpress.org/plugins/allow-multiple-accounts/) and allow the possibility of register/update users with same email instead as using thme in this case as a secondary reference to the user as the username.

= 1.6 =
*	Now options that are only useful if some other plugin is activated, they will only show when those plugins were activated
* 	Thanks to Carmine Morra (carminemorra.com) for supporting the next two big features:
*	New role manager: instead of using a select list, you can choose roles now using checkboxes and you can choose more than one role per user
* 	SMTP server: you can send now from your WordPress directly or using a external SMTP server (almost all SMTP config and SMTP sending logic are based in the original one from WP Mail SMTP - https://wordpress.org/plugins/wp-mail-smtp/). When the plugin finish sending mail, reset the phpmailer to his previous state, so it won't break another SMTP mail plugin.
* 	And this little one, you can use **email** in mail body to send to users their email (as it existed before: **loginurl**, **username**, **password**)

= 1.5.2 =
* 	Thanks to idealien, if we use username to update users, the email can be updated as the rest of the data and metadata of the user and we silence the email changing message generated by core.

= 1.5.1 =
* 	Thanks to Mitch ( mitch AT themilkmob DOT org ) for reporting the bug, now headers do not appears twice.

= 1.5 =
* 	Thanks to Adam Hunkapiller ( of dreambridgepartners.com ) have supported all this new functionalities.
*	You can choose the mail from and the from name of the mail sent.
*	Mail from, from name, mail subject and mail body are now saved in the system and reused anytime you used the plugin in order to make the mail sent easier.
*	You can include all this fields in the mail: "user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim", "user_registered" if you used it in the CSV and you indicate it the mail body in this way **FIELD_NAME**, for example: **first_name**

= 1.4.2 =
* 	Due to some support threads, we have add a different background-color and color in rows that are problematic: the email was found in the system but the username is not the same

= 1.4.1 =
* 	Thanks to Peri Lane for supporting the new functionality which make possible to activate users at the same time they are being importing. Activate users as WP Members plugin (https://wordpress.org/plugins/wp-members/) consider a user is activated

= 1.4 =
* 	Thanks to Kristopher Hutchison we have add an option to choose what you want to do with empty cells: 1) delete the meta-data or 2) ignore it and do not update, previous to this version, the plugin update the value to empty string

= 1.3.9.4 =
* 	Previous version does not appear as updated in repository, with this version we try to fix it

= 1.3.9.3 =
* 	In WordPress Network, admins can now use the plugin and not only superadmins. Thanks to @jephperro

= 1.3.9.2 =
* 	Solved some typos. Thanks to Jonathan Lampe

= 1.3.9.1 =
* 	JS bug fixed, thanks to Jess C

= 1.3.9 =
* 	List of old CSV files created in order to prevent security problems.
* 	Created a button to delete this files directly in the plugin, you can delete one by one or you can do a bulk delete.

= 1.3.8 =
* 	Fixed a problem with iterator in columns count. Thanks to alysko for their message: https://wordpress.org/support/topic/3rd-colums-ignored?replies=1

= 1.3.7 =
* 	After upload, CSV file is deleted in order to prevent security issues.

= 1.3.6 =
* 	Thanks to idealien for telling us that we should check also if user exist using email (in addition to user login). Now we do this double check to prevent problems with users that exists but was registered using another user login. In the table we show this difference, the login is not changed, but all the rest of data is updated.

= 1.3.5 =
* 	Bug in image fixed
*	Title changed

= 1.3.4 =
* 	Warning with sends_mail parameter fixed
*	Button to donate included

= 1.3.3 =
* 	Screenshot updated, now it has the correct format. Thank to gmsb for telling us the problem with screenshout outdated

= 1.3.2 =
* 	Thanks to @jRausell for solving a bug with a count and an array

= 1.3.1 =
* 	WooCommerce fields integration into profile
*	Duplicate fields detection into profile
*	Thanks to @derwentx to give us the code to make possible to include this new features

= 1.3 =
*	This is the biggest update in the history of this plugin: mails and passwords generation have been added.
*	Thanks to @jRausell to give us code to start with mail sending functionality. We have improved it and now it is available for everyone.
*	Mails are customizable and you can choose 
*	Passwords are also generated, please read carefully the documentation in order to avoid passwords lost in user updates.

= 1.2.3 =
*	Extra format check done at the start of each row.

= 1.2.2 =
*	Thanks to twmoore3rd we have created a system to detect email collisions, username collision are not detected because plugin update metadata in this case

= 1.2.1 =
*	Thanks to Graham May we have fixed a problem when meta keys have a blank space and also we have improved plugin security using filter_input() and filter_input_array() functions instead of $_POSTs

= 1.2 =
*	From this version, plugin can both insert new users and update new ones. Thanks to Nick Gallop from Weston Graphics.

= 1.1.8 =
*	Donation button added.

= 1.1.7 =
*	Fixed problems with \n, \r and \n\r inside CSV fields. Thanks to Ted Stresen-Reuter for his help. We have changed our way to parse CSV files, now we use SplFileObject and we can solve this problem.

=======
= 1.2 =
*	From this version, plugin can both insert new users and update new ones. Thanks to Nick Gallop from Weston Graphics.

= 1.1.8 =
*	Donation button added.

= 1.1.7 =
*	Fixed problems with \n, \r and \n\r inside CSV fields. Thanks to Ted Stresen-Reuter for his help. We have changed our way to parse CSV files, now we use SplFileObject and we can solve this problem.

>>>>>>> .r1121403
= 1.1.6 =
*	You can import now user_registered but always in the correct format Y-m-d H:i:s

= 1.1.5 =
*	Now plugins is only shown to admins. Thanks to flegmatiq and his message https://wordpress.org/support/topic/the-plugin-name-apears-in-dashboard-menu-of-non-aministrators?replies=1#post-6126743

= 1.1.4 =
*	Problem solved appeared in 1.1.3: sometimes array was not correctly managed.

= 1.1.3 =
*	As fgetscsv() have problems with non UTF8 characters we changed it and now we had problems with commas inside fields, so we have rewritten it using str_getcsv() and declaring the function in case your current PHP version doesn't support it.

= 1.1.2 =
*	fgetscsv() have problems with non UTF8 characters, so we have changed it for fgetcsv() thanks to a hebrew user who had problems.

= 1.1.1 =
*	Some bugs found and solved managing custom columns after 1.1.0 upgrade.
*	If you have problems/bugs about custom headers, you should deactivate the plugin and then activate it and upload a CSV file with the correct headers again in order to solve some problems.

= 1.1.0 =
*	WordPress user profile default info is now saved correctly, the new fields are: "user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim" and "yim"
* 	New CSV example created.
*	Documentation adapted to new functionality.

= 1.0.9 =
*   Bug with some UTF-8 strings, fixed.

= 1.0.8 =
*   The list of roles is generated reading all the roles avaible in the system, instead of being the default always.

= 1.0.7 =
*   Issue: admin/super_admin change role when file is too large. Two checks done to avoid it.

= 1.0.6 =
*   Issue: Problems detecting extension solved (array('csv' => 'text/csv') added)

= 1.0.5 =
*   Issue: Existing users role change, fixed

= 1.0.0 =
*   First release

== Upgrade Notice ==

= 1.0 =
*   First installation

== Frequently Asked Questions ==

= Columns position =

You should fill the first two columns with the next values: Username, Email.

The next columns are totally customizable and you can use whatever you want. All rows must contains same columns. User profile will be adapted to the kind of data you have selected. If you want to disable the extra profile information, please deactivate this plugin after make the import.

= id column =

You can use a column called id in order to make inserts or updates of an user using the ID used by WordPress in the wp_users table. We have two different cases:

*	If id doesn't exist in your users table: WordPress core does not allow us insert it, so it will throw an error of kind: invalid_user_id
*	If id exists: plugin check if username is the same, if yes, it will update the data, if not, it ignores the cell to avoid problems

= Passwords =

We can use a column called "Password" to manage a string that contains user passwords. We have different options for this case:

*	If you don't create a column for passwords: passwords will be generated automatically
*	If you create a column for passwords: if cell is empty, password won't be updated; if cell has a value, it will be used

= Serialized data =

Plugin can import serialized data. You have to use the serialized string directly in the CSV cell in order the plugin will be able to understand it as an serialized data instead as any other string.

= Lists =

Plugin can import lists as an array. Use this separator: :: two colons, inside the cell in order to split the string in a list of items.

= WordPress default profile data =

You can use those labels if you want to set data adapted to the WordPress default user columns (the ones who use the function wp_update_user)

*	user_nicename: A string that contains a URL-friendly name for the user. The default is the user's username.
*	user_url: A string containing the user's URL for the user's web site.
*	display_name: A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through 	*	obscurity (that is if you don't use and delete the default admin user).
*	nickname: The user's nickname, defaults to the user's username.
* 	first_name: The user's first name.
*	last_name: The user's last name.
*	description: A string containing content about the user.
*	jabber: User's Jabber account.
*	aim: User's AOL IM account.
*	yim: User's Yahoo IM account.
*	user_registered: Using the WordPress format for this kind of data Y-m-d H:i:s.

= Multiple imports =

You can upload as many files as you want, but all must have the same columns. If you upload another file, the columns will change to the form of last file uploaded.

= Free and premium support =

You can get:

*	Free support [in WordPress forums](https://wordpress.org/support/plugin/import-users-from-csv-with-meta)
*	Premium support [writing directly to contacto@codection.com](mailto:contacto@codection.com).

= Customizations, addons, develops... =
[Write u directly to contacto@codection.com](mailto:contacto@codection.com).

== Installation ==

### **Installation**

*   Install **Import users from CSV with meta** automatically through the WordPress Dashboard or by uploading the ZIP file in the _plugins_ directory.
*   Then, after the package is uploaded and extracted, click&nbsp;_Activate Plugin_.

Now going through the points above, you should now see a new&nbsp;_Import users from CSV_&nbsp;menu item under Tool menu in the sidebar of the admin panel, see figure below of how it looks like.

[Plugin link from dashboard](http://ps.w.org/import-users-from-csv-with-meta/assets/screenshot-1.png)

If you get any error after following through the steps above please contact us through item support comments so we can get back to you with possible helps in installing the plugin and more.

Please read documentation before start using this plugin.
