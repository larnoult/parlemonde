=== MyPuzzle Word Search ===
Contributors: Thomas Seidel
Donate link: http://mypuzzle.org
Tags: word search, mypuzzle, puzzle, wordsearch, puzzle games, word search puzzles, wordsearch puzzles
Requires at least: 2.5
Tested up to: 1.3
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fast and easy integration of word search puzzles into your blogs. 

== Description ==

Major features in this version include: 

* UTF8 support for all language charsets
* Very easy and nice integration of online word search puzzles, direct playable
* The word search puzzles can be inserted everywhere: in your theme files, posts and pages.
* Twelve different levels from 6x6 upto 17x17, for kids or for cracks
* Simply enter a comma separated list of words and a playable online puzzle is ready for your visitors
* You can change appearance, size and visible features to fit your site and themes

For more details and examples visit the plugin page on <a href="http://mypuzzle.org/wordsearch/wordpress.html">Wordpress Word Search Plugin</a>

== Installation ==

1. Upload 'MyPuzzle Word Search' folder to the '/wp-content/plugins/' directory or use plugin install page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the MyPuzzle Word Search Options page under settings and save your prefered options.
4. Use the Wordsearch code "[wordsearch-mp]" to insert wordsearch puzzles into your posts/pages.

The <a href="http://mypuzzle.org/wordsearch/">Word Search Puzzles</a> are provided by mypuzzle.org.

== How to use ==

Use the wordpress post shortcut [wordsearch-mp] for default setup or with parameter for individualizing for each post.
Examples:
- [wordsearch-mp]
- [wordsearch-mp ws_size=300 ws_dimension=8 mywordlist='MyPuzzle,Wordsearch,Jigsaw,Sudoku,Sliding,Games,Kakuro,Solver']

All Parameter:
- ws_size, ws_dimension, mywordlist, ws_bgcolor, ws_fontcolor, ws_focuscolor, ws_foundcolor

Solve the puzzle by searching the hidden words and select them on the grid by clicking and dragging the mouse over all letters for the word.

Visit <a href="http://mypuzzle.org/wordsearch/wordpress.html">Wordpress Word Search Plugin</a>

== Screenshots ==

1. MyPuzzle.org Word Search display

== Changelog ==  

 = 1.0.0 =  
 * Initial release.

 = 1.1.0 =  
 * Fixed: different heights of grid an word container
 * Fixed: Letters not shown in IE, font-color will be set now correctly

 = 1.1.1 =  
 * Fixed: Wordlist alignment

 = 1.2.1 =  
 * Fixed: Width allocatioin between playgrid and wordlist
 * Added: Show Wordlist on Left, Right
 * Added: Disable Wordlist
 * Added: Restart Button

 = 1.3 =  
 * Added: UTF8 support to have all other languages beeing able to use the plugin!

 = 1.3.1 =  
 * Fixed: Success-Message has not beeing displayed after solving the puzzle.
