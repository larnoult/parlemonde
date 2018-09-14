=== QuizMaster ===
Contributors: goldhat
Donate link: http://wpquizmaster.com
Tags: quiz, test, answer, question, learning, assessment
Requires at least: 4.0
Tested up to: 4.8.1
Stable tag: 0.8.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The most extendable, feature-rich quiz system plugin for WordPress.

== Description ==

The most extendable, feature-rich quiz system plugin for WordPress. Developer-friendly, customizable and extension based with numerous extensions available to extend the feature set. Requires FieldMaster (Free) or ACF Pro. Forked in 2016 from WP Pro Quiz, a free quiz plugin developed by Julius Fischer (http://www.it-gecko.de/).

= Features =
* 5 Question Types: Single Choice, Multiple Choice, Sorting Choice, Free Choice, Fill in the Blank
* Unique slug/url for each quiz and each question
* Time limit setting for quizzes
* Randomize question display order
* Randomize answers within questions
* Correct or incorrect response message for all questions
* Different points for each answer
* Quiz score tracking and quiz history for students
* Hints button and tracking of hints used
* Email notification system
* Categorization and tagging for quizzes and questions
* Quiz summary displayed on quiz completion
* Decentralized question pool, allows a question to be placed in multiple quizzes
* Migration extension available for importing from WP Pro Quiz

= QuizMaster Official Plugin Site =
http://wpquizmaster.com

= Support =
https://wordpress.org/support/plugin/quizmaster or open an issue at GitHub https://github.com/goldhat/quizmaster

== Installation ==


Before installing QuizMaster check that you have ACF Pro installed and activated, or if you don't have it download and install FieldMaster, a free alternative from https://github.com/goldhat/fieldmaster. Make sure when unzipping FieldMaster that the main plugin directory is renamed to "fieldmaster", removing any version information that comes with GitHub downloads.

1. Search for "quizmaster" from your Add New Plugin page in the WP Admin.
2. Click to install, then activate.

OR

1. Upload the "quizmaster" folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.8.5 =

Replaces full templates with the_content filter loading for better them compatability

= 0.8.4 =

Fixes for max questions error and quiz/question association bug.

= 0.8.3 =

Fixes warnings regarding tab naming and other issues.

= 0.8.0 =

Adds bidirectional quiz/question management, the ability to add a question to a quiz from the question editor. Improved selection field for adding questions from the quiz editor.

= 0.7.3 =

Fixes randomization and marking/checking question issues present in previous release with new jQuery plugin file.

= 0.7.0 =

Contains a rebuilt front-end JS file with the newly developed QuizMaster jQuery plugin. This file features global event hooks that extension developers can leverage to adapt the quiz flow at run-time.

= 0.6.5 =
Includes support for translation, updated .pot file and fixes to translation functions.

== Upgrade Notice ==

N/A

== FAQ ==

= Why does the plugin require a fields API? =

We originally included ACF Pro because of it's great UX fields and because it allows us to offer great support for extending the plugin. Developers are able to easily add fields into the UX and there is great consistency in the interface by using ACF. However we were not able to strike a deal with the ACF developer to enable us to embed ACF Pro into the plugin, and QuizMaster already relied on certain pro features including repeater field and options pages. Thus we invented the new fields plugin FieldMaster which is a fork of ACF Pro, and available for free. We recommend using FieldMaster unless you already have ACF Pro installed, because we are bundling additional ACF integration plugins that provide an even better UX, such as collapsing repeater fields.

= How can I customize the front-end UX and style templates? =

QuizMaster has a sophisticated templating system similar to that found in most advanced WP plugins such as WooCommerce. Developers may override templates at the theme level simply by creating a directory named "quizmaster" and copying the original template from quizmaster using the same name and/or subfolder. These overrides give you the ability to edit template output. QuizMaster has over 20 of these templates and the vast majority of output comes from them (not from the view layer as in WP Pro Quiz). For other styling changes such as color, fonts etc. use your child theme CSS file or integrated theme style editor. We've eliminated the WP Pro Quiz usage of "!important" declarations so that the styles can be overwritten. Most QuizMaster CSS class names are prefixed with "qm-" but some inconsistency remains at this point in the naming of classes.

== Screenshots ==

1. Quiz List
2. Add New Quiz
3. Email List
4. Email Editor
5. Categories & Tags
6. Main Menu
7. Quiz Category Editor
8. Questions List
9. Quiz Scores List
10. Add New Question
11. Student Quiz Scores List

== Credits ==

This plugin would not be possible with the work of Julius Fischer, the developer of WP Pro Quiz.
