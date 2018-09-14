=== TableMaster ===
Contributors: vmallder
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9ET8HUEG7THKS
Tags: table, tables, database, jquery, datatables
Requires at least: 4.3
Tested up to: 4.3.1
Stable tag: 0.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds styling and DataTables (table plugin for jQuery) with Buttons and Responsive extensions to your tables.

== Description ==
TableMaster is a simple and easy to use plugin that will print styled and responsive tables (containing your own dynamic data) from any Wordpress database table to your page or post.  In addition, TableMaster integrates the [DataTables table plugin for jQuery with the Buttons and Responsive extensions](http://datatables.net) to provide a feature-rich user experience. TableMaster is currently the only plugin of its kind. (The most popular Wordpress table plugin does not allow you to print dynamic data directly from your Wordpress database.) 

With TableMaster you can create custom styled tables with search, filter, pagination, copy, print and download (to CSV, Excel and PDF format) features with just a simple shortcode. There are many shorcode keywords available for customizing your tables. The complete TableMaster User's Guide is included with the plugin. However, if you would like to preview all the keywords and see the examples tables prior to installing the plugin please visit the [TableMaster Page](http://www.codehorsesoftware.com/tablemaster-wordpress-plugin) at the Codehorse Software website.

TableMaster Features Include:

* Tables are fully responsive
* No knowledge of HTML or CSS is required
* Prints dynamic or static data from your MySQL database
* Integrates the Datatables table plugin for jQuery with the Buttons and Responsive Extensions
* Enables custom styling of your table
* Includes a complete user's guide
* Comes with friendly and responsive support
* Several example styles included

==Installation==

* Upload the `tablemaster` directory to your wp-content/plugins/ directory.
* Login to your WordPress Admin menu, go to Plugins, and activate it.
* You can find a complete user's guide by selecting the TableMaster option on your WordPress Dashboard.

==Screenshots==

1. The full TableMaster User's Guide available in the admin area.

2. Example table with default theme style

3. Example table using a user custom style

4. Example table using default theme style with JQuery Datatables enabled

5. Example table using default theme style with JQuery Datatables and Buttons enabled

6. Example table with user's custom style and JQuery Datatables and Buttons enabled

== Frequently Asked Questions ==

This is the third release of TableMaster! And, still, there are no frequently asked questions. I hope this means that the TableMaster User's Guide has been very helpful to everyone. However, questions will be added here when they are received.

== Upgrade Notice ==

= 0.0.3 =

This release fixes an issue reported by megadrive16 where the DataTables jQuery plugin default column sorting was overriding the "ORDER BY" clause that was specified by the user with the 'sql' keyword.  With this release, you can use the 'default_sort' keyword to disable the default column sorting used by the DataTables jQuery plugin. 

= 0.0.2 =

This release adds new features for the user and completes the development of the baseline plugin. There are no known backward compatibility issues.

== Changelog ==

= 0.0.3 =

* Added default_sort keyword to turn off the default column sorting by DataTables.
* Added plugin name and plugin version to Settings and Admin constructors.
* Added font awesome icons to user's guide when not viewed in Wordpress admin. 

= 0.0.2 =

* Added new keywords: link_labels, link_targets, new_window, pre_table_filter, post_table_filter. 
* Added a General Options page that is available from the Admin Settings menu.
* Updated and fixed typos in User's Guide.
* Refactored the code into Admin, Settings, and User's Guide classes.

= 0.0.1 =

First release.
