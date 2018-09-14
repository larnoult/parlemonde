
=== Post Volume Stats ===
Contributors: shortdark
Donate link: https://www.paypal.me/shortdark
Tags: posts, stats, graphs, charts, categories, tags, admin, year, month, day, hour, widget, author, taxonomy, csv
Requires at least: 3.5
Tested up to: 4.9.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows stats for the volume of posts per year, month, day-of-the-month, day-of-the-week, hour-of-the-day, words per post, days between posts, author, categories, tags and custom taxonomy.

== Description ==

This plugin looks at the volume of posts in each category, tag, the volume of posts per year, month, day-of-the-month, day-of-the-week, hour, author, number of words-per-post and the number of days between posts. You can specify a year and/or an author to just look at the post volume stats for that year/author. The bar and pie charts can be added to a sidebar with Post Volume Stats widget. Lists and line graphs can be exported to a new post to show the change in category, tag and custom taxonomy posts over the years. You can also export the "Compare Years" data into a CSV spreadsheet. The latest feature is the "Date Range" page which applies a date range to the data on the main page.

Please let me know if you like this plugin by [leaving a review](https://wordpress.org/plugins/post-volume-stats/).

Go to the [Post Volume Stats website](https://www.postvolumestats.com/) for more information.

= Translations =

You can translate Post Volume Stats on [__translate.wordpress.org__](https://translate.wordpress.org/projects/wp-plugins/post-volume-stats).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress
plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. The menu item "Post Volume Stats" should now be in your admin menu and the "Post Volume Stats" widget should also be available to use.

== Screenshots ==

Here are the latest screenshots...

1. Shows the main page of "Post Volume Stats".
2. Shows the "compare years" chart, which is a preview of the CSV download.
3. You can view the stats for each year individually. This shows the same blog with stats for one year selected.
4. The "Category", "Tag" and "Custom Taxonomy" pages allow the user to filter results and export the data into an HTML list and a line graph.
5. The export button adds the HTML list and line graph into a new blog post.
6. The results of exporting the line graph and list into a new post.
7. Shows the widget in the "live preview" area of the admin.
8. Settings page allows customization.
9. New "Date Range" page which applies a date range to the main page only, not "Category", "Tag" or "Custom Taxonomy" pages.

== Changelog ==

= 3.1.17 =

* "Date Range" now working for all bar charts, pie charts and lists on main page. The "Years" bar chart, "Compare Years" and "Export Compare Years CSV" remain unaffected. 
* CSV exports can now be the stats for a single user.
* Fixed bug where a selected user meant that it wasn't recognizing the selected year on "Days Between Posts".
* Made the "Show Data" and "Compare Years" titles more precise.
* Tidied up code, removed WordPress notices and general bugfix.

= 3.1.16 =

* Setting added to make max. interval between posts 30 to 80 days for "Days Between Posts". Longer intervals take longer to load in "Compare Years" table.
* Compare years added for "Days Between Posts".
* CSV export added for "Days Between Posts".
* "Words per Post" and "Days Between Posts" added to the widget.
* Fixed Custom Taxonomy page so that changing year no longer breaks the checkbox list.
* Fixed the color issue with the Custom Taxonomy pages.
* Fixed export bug with the Custom Taxonomy.

= 3.1.14 =

* Changed "Days Between Posts" to look at calendar dates only, not the time that posts were made.
* "Date Range" must be activated in the settings.
* Modified some wording.

= 3.1.12 =

* Tidied Date Range page. 
* Added explanation to Date Range: this page is experimental, please use the year select on the other pages.

= 3.1.11 =

* Added "Days Between Posts" to the main page.
* Fixed bug where sometimes the volume of posts can be a non integer.
* Added link to the Settings page from the main WordPress Plugins page.
* More Date Range fields added (beta).

= 3.1.10 =

* Fixed with line graph axes (tags, categories and custom taxonomy).
* Added "Date Range" page.

= 3.1.09 =

* Problems related to the version number.

= 3.1.08 =

* Added CSV downloads to category, tag and custom taxonomies.

= 3.1.07 =

* Multiple custom taxonomies are now able to be shown at the same time.
* Trimmed empty columns from the end of "words per post".
* Fixed "year" bug on the widget.
* Added option to have white text on the widget.

= 3.1.06 =

* Security fix made beta CSV download stop working, so this is a different fix.

= 3.1.05 =

* Security fix on beta CSV downloads.

= 3.1.04 =

* Updated PVS version number on the pages.

= 3.1.03 =

* "Words per post" should give a better distribution of the posts.
* Beta for CSV export added.

= 3.1.02 =

* Bug fixes on words per post.

= 3.1.01 =

* Minor bug fixes on the author and years bar charts.

= 3.1.00 =

* You can now compare years for some of the bar charts - shows data for all years in one table that can be copy/pasted into a spreadsheet.
* Words per post bar chart added.
* Custom Taxonomy bar chart added to the main page (must be selected on the Settings page).
* "Authors" data is now "Authors" or higher, "Contributors" are not included in the Authors data.
* Optional link in the Admin Toolbar, activated on the Settings page.
* Version number added to page footers.
* Bug fixing and streamlining.

= 3.0.29 =

* Custom taxonomy page added (must be selected on the Settings page).

= 3.0.28 =

* Updated description and POT file.

= 3.0.27 =

* You can now click a bar of the "Authors" barchart to filter the stats to that author.
* Settings page: turn off authors bar chart, turn off rainbow lists and week starts on.

= 3.0.26 =

* Updated description and POT file.

= 3.0.25 =

* Added more summary text stats to the bottom of the main page.
* Highlighted weekends on the "posts per day-of-the-week" bar chart.
* Added "Authors" bar chart.

= 3.0.24 =

* Added pie charts to Widget.
* Added links to the line graphs.

= 3.0.23 =

* Fixed bug on line graph for blogs with only one year of posts.
* Tidied and simplified tag/category pages.
* You can now choose whether to export line graph, list or both.

= 3.0.22 =

* Added plugin link to bottom of exported results.

= 3.0.21 =

* Line graphs improved and also able to be exported with the lists.
* Matching color applied to the export lists.
* Re-structured tag/category pages and removed the pie charts.

= 3.0.20 =

* Admin notices added.
* Line graph added to tags/categories pages.

= 3.0.19 =

* Improved the colors in the pie charts.

= 3.0.18 =

* Reverted back to having the preview, then from the preview you can "Export" into post.

= 3.0.17 =

* Changed from "Show HTML" to "Export" into post.
* One more debug notice fixed.

= 3.0.16 =

* Tidied debug notices.

= 3.0.15 =

* Fixed bug on exports.
* Added "load_plugin_textdomain".

= 3.0.14 =

* Tidied "export" method to reduce script time elapsed.

= 3.0.13 =

* Updated readme.txt with "translations" info.
* Widget screenshot.
* Minor changes.

= 3.0.12 =

* Wording fixed.
* Duplicate methods merged.
* Updated version of WordPress.

= 3.0.11 =

* Bug-fix.

= 3.0.10 =

* Widget added.

= 3.0.09 =

* Updated version numbers to re-load scripts and bug-fix.

= 3.0.08 =

* Export "Categories" data to HTML.

= 3.0.07 =

* Export "Tags" data to HTML.

= 3.0.06 =

* Bug fix - allowed top line of bar chart if it is on the boundary of the chart.
* I18n improvements.
* Added "Category" and "Tag" admin subpages.
* Tidied.

= 3.0.05 =

* Bug fixes.

= 3.0.04 =

* Added lines and legends to the bar charts.

= 3.0.03 =

* Brought the lists back for the bar charts.
* Tidied code.
* Cosmetic changes.

= 3.0.02 =

* Removed submit button from year dropdown used 'onchange' to submit instead.
* You can now also select a year by clicking a bar of the 'Years' graph.

= 3.0.01 =

* Updated the version number because some older versions were not updating.

= 2.3.05 =

* Made sure categories should be working correctly.
* Prevented direct access to class files.

= 2.3.04 =

* Fixed bug with yearly tags.

= 2.3.03 =

* When a year is selected it applies to all stats now, including tags and categories.
* Changed pie chart opacity rules.

= 2.3.02 =

* The year option setting moved from it's own page to the main plugin page.

= 2.3.01 =

* Settings page added for users to chose the year for all time-based stats.

= 2.2.6 =

* Modified the pie chart coloring
* Preparation for UI
* Modified layout

= 2.2.5 =

* Added posts per day info.

= 2.2.4 =

* Timed the script.

= 2.2.3 =

* Loaded external jQuery UI draggable the proper way using script-loader.php
* Limited the height of the lists to smaller than the height of the window
* Fixed the number of years bug on the years list

= 2.2.2 =

* Added JQuery UI.
* AJAX DIVs are now draggable.
* Updated readme.txt description.
* More text changed to translatable strings.

= 2.2.1 =

* "lists" moved out of the page and into AJAX DIVs with loading animation
* CSS loaded as a .css file, instead of in-line
* Months and days-of-the-month added.

= 2.1.8 =

* Removed jddayofweek completely as it was not working properly.

= 2.1.7 =

* Removed PHP function jddayofweek for PHP versions below 5.3 as was not working on 5.2.17

= 2.1.6 =

* Removed the Day of the Week section for PHP vesions below 5.3 as that part was not working on a 5.2 version of PHP.

= 2.1.5 =

* Removed the magic variable __DIR__ that limited the plugin to PHP versions 5.3 and above.

= 2.1.4 =

* Changed the way the info is gathered, meaning that the year. Hour and day-of-week data should now be correct, whereas before it was incorrect.

= 2.1.3 =

* Re-ordered the data in the pie charts into size order.

= 2.1.2 =

* Added bar charts for day-of-the-week and hour-of-the-day.
* Simplified the CSS to allow for easy additional columns.
* Calculated the "requires at least" from the Wordpress functions used.

= 2.1.1 =

* Added the total number of posts in yearly column.

= 2.1.0 =

* More security.
* More OOP classes and split up into different files.
* Changed admin page type to "read" as it does not have any need for user input and does not do anything.

= 2.09 =

* Started changing to OOP.
* Tags added.
