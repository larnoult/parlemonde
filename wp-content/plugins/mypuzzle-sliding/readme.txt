=== MyPuzzle Sliding ===
Contributors: Thomas Seidel
Donate link: http://mypuzzle.org
Tags: Sliding, mypuzzle, puzzle, sliding puzzle, puzzle games, slide puzzle
Requires at least: 2.5
Tested up to: 3.4.1
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fast and easy integration of sliding puzzles into your blogs. 

== Description ==

Major features in this version include: 

* New! Use the included MyPuzzle Image Gallery and random startup images
* New! Link your own Image Library
* The sliding puzzles can be inserted everywhere: in your theme files, posts and pages.
* Use your own image url and automatically get sliding puzzles for them
* Three different levels 3x3, 4x4 and 5x5 are available 
* You can change size specifically 
* Change background color to fit your themes and layouts
* Enable or disable hints to give some help

For more details and examples visit the plugin page on <a href="http://mypuzzle.org/sliding/wordpress.html">Wordpress Sliding Plugin</a>

== Installation ==

1. Upload 'Sliding Mypuzzle' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the MyPuzzle Sliding Options page under settings and save your prefered options.
4. Use the Sliding code "[sliding-mp]" to insert sliding puzzles into your posts/pages.

The Sliding <a href="http://mypuzzle.org/">Puzzles Games</a> are provided by mypuzzle.org.

== How to use ==

Use the wordpress post shortcut [sliding-mp] for default setup or with parameter for individualizing for each post.
Examples:
- [sliding-mp]
- [sliding-mp size=400 pieces=4 showhints=1 bgcolor='#ffffff' myimage='' showlink=1]
- [sliding-mp size=460 pieces=3 showhints=0 bgcolor='#f4f4f4' myimage='http://mypuzzle.org/jigsaw/img/animals-bunny.jpg']
- [sliding-mp size=460 pieces=4 showhints=0 bgcolor='#f4f4f4' gallery='wp-content/uploads/myimages']

When using the resize option is not possible you have to resize the images yourself.
The best size for the puzzle is about maximum height of 400 and maximum width of 400.
So depending on the height/width ration you have to resize your image to fit one side the maximum value while beeing less on the other.

Visit <a href="http://mypuzzle.org/sliding/wordpress.html">Wordpress Sliding Plugin</a>

== Screenshots ==

1. MyPuzzle Sliding Screenshot

== Changelog ==  

 = 1.0.0 =  
 * Initial release.

 = 1.1.0 =  
 * Added Gallery Feature for MyPuzzle Image Gallery
 * Added Gallery Feature for Custom Image Path

 = 1.1.1 =
 * Fixed jQuery Wordpress default
 * Fixed custom path handling

 = 1.1.2 =
 * Fixed resize image function for new php version
 * Fixed remove folders from gallery

 = 1.1.3 =
 * Fixed Image Url not working without resize
