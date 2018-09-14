<?php
/*
Plugin Name: MyPuzzle - Find The Pair | A Memory Game
Plugin URI: http://mypuzzle.org/find-the-pair/wordpress.html
Description: Include a mypuzzle.org Find the pair Puzzle in your blogs with just one shortcode. 
Version: 1.1.1
Author: tom@mypuzzle.org
Author URI: http://mypuzzle.org/
Notes    : Visible Copyrights and Hyperlink to mypuzzle.org required
*/


/*  Copyright 2012  tom@mypuzzle.org  (email : tom@mypuzzle.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Default Options
 */
function get_ftpair_mp_options ($default = false){
	$shc_default = array(
            'ftpair_width' => '500',
            'ftpair_height' => '500',
            'ftpair_pairs' => '8',
            'ftpair_bgcolor' => 'FFFFFF',
            'ftpair_cardcolor' => 'FEC',
            'ftpair_cardbordercolor' => 'F96',
            'ftpair_gallery' => 'wp-content/plugins/mypuzzle-find-the-pair-a-memory-game/gallery'
            );
	if ($default) {
		update_option('mp_ftpair_op', $shc_default);
		return $shc_default;
	}
	
	$options = get_option('mp_ftpair_op');
	if (isset($options))
		return $options;
	update_option('mp_ftpair_op', $shc_default);
	return $options;
}

/**
 * The Sortcode
 */
add_action('wp_enqueue_scripts', 'ftpair_mp_jscripts');
add_shortcode('ftpair-mp', 'ftpair_mp');


function ftpair_mp_jscripts() {
    wp_enqueue_script( 'jquery' );
    
    //my jscripts
    wp_register_script('mp-ftpair-js', plugins_url('/js/ftpair-mypuzzle.js', __FILE__));
    wp_enqueue_script('mp-ftpair-js');
    
    //my styles
    wp_register_style( 'mp-ftpair-style', plugins_url('/css/ftpair-mypuzzle.css', __FILE__) );
    wp_enqueue_style( 'mp-ftpair-style' );
} 

function ftpair_mp_testRange($int,$min,$max) {     
    return ($int>=$min && $int<=$max);
}

function ftpair_mp($atts) {
	global $post;
	$options = get_ftpair_mp_options();	
	
	$ftpair_width = $options['ftpair_width'];
        if (!is_numeric($ftpair_width) || !ftpair_mp_testRange(intval($ftpair_width),100,1000)) {$ftpair_width=300;}
        $ftpair_height = $options['ftpair_height'];
        if (!is_numeric($ftpair_height) || !ftpair_mp_testRange(intval($ftpair_height),100,1000)) {$ftpair_height=300;}
        
        $ftpair_pairs = $options['ftpair_pairs'];
        if (!is_numeric($ftpair_pairs) || !ftpair_mp_testRange(intval($ftpair_pairs),6,21)) {$ftpair_pairs=8;}
        
        $ftpair_bgcolor = $options['ftpair_bgcolor'];
        $ftpair_bgcolor = str_replace('#', '', $ftpair_bgcolor);
        if (!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i', '#'.$ftpair_bgcolor)) $ftpair_bgcolor = 'FFFFFF';
        
        $ftpair_cardcolor = $options['ftpair_cardcolor'];
        $ftpair_cardcolor = str_replace('#', '', $ftpair_cardcolor);
        if (!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i', '#'.$ftpair_cardcolor)) $ftpair_cardcolor = 'FEC';
        
        $ftpair_cardbordercolor = $options['ftpair_cardbordercolor'];
        $ftpair_cardbordercolor = str_replace('#', '', $ftpair_cardbordercolor);
        if (!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i', '#'.$ftpair_cardbordercolor)) $ftpair_cardbordercolor = 'F96';
        
        $ftpair_gallery = $options['ftpair_gallery'];
        if (!$ftpair_gallery || $ftpair_gallery=='') {
            $ftpair_gallery = 'wp-content/plugins/mypuzzle-find-the-pair-a-memory-game/gallery';
        } else {
            $ftpair_gallery = ftpair_mp_clearpath($ftpair_gallery);
        }
        
        extract(shortcode_atts(array(
                'ftpair_width' => $ftpair_width,
                'ftpair_height' => $ftpair_height,
                'ftpair_pairs' => $ftpair_pairs,
                'ftpair_bgcolor' => $ftpair_bgcolor,
                'ftpair_cardcolor' => $ftpair_cardcolor,
                'ftpair_cardbordercolor' => $ftpair_cardbordercolor,
		'ftpair_gallery' => $ftpair_gallery
	), $atts));
        $galleryDir = ABSPATH . $ftpair_gallery;
        $galleryGetImagesPHP = plugins_url('ftpair-getCardImages.php', __FILE__);
        
        $siteurl = site_url();
        
        $output = "<style>\r";
        $output .= "#mem-grid {float:left;cursor: pointer;margin:5px;border:0px solid #e0e0e0;background-color:#".$ftpair_bgcolor.";width:".$ftpair_width."px;}";
        $output .= ".memCard {border: 1px solid #".$ftpair_cardbordercolor."; border-radius: 10px;float: left;margin: 0px;background-color: #".$ftpair_cardcolor."; overflow: hidden;position: relative; cursor: pointer;}";
        $output .= ".memImage {border-radius:10px; margin:2px;border:#".$ftpair_cardbordercolor." 1px solid; vertical-align: top;alignment-adjust: central;}";
        $output .= ".memCard.selected {border-color: #".$ftpair_bgcolor."; background-color: #".$ftpair_bgcolor.";}";
        $output .= ".memCard.selected img {display: block;}";
        $output .= ".memCard img {display: none; position: absolute;}";
        $output .= ".memCard.empty {border-color: #".$ftpair_bgcolor."; background: #".$ftpair_bgcolor."; cursor: default;}";
        
        $output .= "</style>";
        
        $output .= "<div style='background-color:#".$ftpair_bgcolor.";width:".$ftpair_width."px'>";
        $output .= "  <div id='mem-grid'></div>";
        $output .= "  <div style='float: right;font-size:12px;'></div>";
        $output .= "  <div style='width:".intval($ftpair_width/2)."px;float: left;font-size:12px;'><a id='aRestart' href=''>Recommencez</a></div>";
        $output .= "</div>";
        
        //add diff for the image wrapper template
        $output .= "<div id='imgWrapTemplate' class='memCard' style='visibility:hidden;'>\r"; //
        $output .= "    <img src='' class='memImage'/>\r";
        $output .= "</div>\r";

        //add invisible variables for jquery access
        $output .= "<div id='var_ftpair_width' style='visibility:hidden;position:absolute'>".$ftpair_width."</div>\r";
        $output .= "<div id='var_ftpair_height' style='visibility:hidden;position:absolute'>".$ftpair_height."</div>\r";
        $output .= "<div id='var_ftpair_pairs' style='visibility:hidden;position:absolute'>".$ftpair_pairs."</div>\r";
        $output .= "<div id='var_galleryDir' style='visibility:hidden;position:absolute'>".$galleryDir."</div>\r";
        $output .= "<div id='var_galleryPath' style='visibility:hidden;position:absolute'>".$ftpair_gallery."</div>\r";
        $output .= "<div id='var_galleryGetPHP' style='visibility:hidden;position:absolute'>".$galleryGetImagesPHP."</div>\r";
        //add jscript to start gallery from flash
        $output .= "<script language='javascript'>\r";
        $output .= "jQuery('#aRestart').click(function(event) {event.preventDefault();ftpair_mp_memory(".$ftpair_pairs.");});";
        $output .= "ftpair_mp_memory(".$ftpair_pairs.");\r";
        $output .= "</script>\r";
        
        return($output);

}
function ftpair_mp_clearpath($inputpath) {
    if (substr($inputpath, 0, 1)=='/') $inputpath = substr($inputpath, 1);
    if (substr($inputpath, strlen($inputpath)-1, 1)=='/') $inputpath = substr($inputpath, 0, strlen($inputpath)-1);
    return($inputpath);
}

function ftpair_mp_getrndanchor()
{
    $asKW = array('Puzzle','Puzzle','Puzzle','Puzzle'
        ,'Puzzle','Puzzle', 'Puzzles', 'Puzzles'
        , 'Puzzles', 'Puzzles', 'Puzzles', 'Puzzles'
        , 'Puzzle', 'Puzzle', 'Puzzles', 'Puzzles');
    $asHC = array('a', 'b', 'c', 'd', 'e', 'f', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');        
    $md5Str = strtolower(substr(strval(md5(strtolower($_SERVER['HTTP_HOST']))), 0, 1));    
    $idx = array_search($md5Str, $asHC);
    return($asKW[$idx]);
}
/**
 * Settings
 */  

add_action('admin_menu', 'ftpair_mp_set');

function ftpair_mp_set() {
	$plugin_page = add_options_page('MyPuzzle Find The Pair | A Memory Game', 'MyPuzzle Find The Pair', 'administrator', 'sudoku-ftpair', 'ftpair_mp_options_page');		
 }

function ftpair_mp_options_page() {

	$options = get_ftpair_mp_options();
	
    if(isset($_POST['Restore_Default']))	$options = get_ftpair_mp_options(true);	?>

	<div class="wrap">   
	
	<h2><?php _e("MyPuzzle - Find The Pair Puzzle Settings") ?></h2>
	
	<?php 

	if(isset($_POST['Submit'])){
                $newoptions['ftpair_width'] = isset($_POST['ftpair_width'])?$_POST['ftpair_width']:$options['ftpair_width'];
                $newoptions['ftpair_height'] = isset($_POST['ftpair_height'])?$_POST['ftpair_height']:$options['ftpair_height'];
                $newoptions['ftpair_pairs'] = isset($_POST['ftpair_pairs'])?$_POST['ftpair_pairs']:$options['ftpair_pairs'];
                
                $newoptions['ftpair_bgcolor'] = isset($_POST['ftpair_bgcolor'])?$_POST['ftpair_bgcolor']:$options['ftpair_bgcolor'];
                $newoptions['ftpair_cardcolor'] = isset($_POST['ftpair_cardcolor'])?$_POST['ftpair_cardcolor']:$options['ftpair_cardcolor'];
                $newoptions['ftpair_cardbordercolor'] = isset($_POST['ftpair_cardbordercolor'])?$_POST['ftpair_cardbordercolor']:$options['ftpair_cardbordercolor'];
                $newoptions['ftpair_gallery'] = isset($_POST['ftpair_gallery'])?$_POST['ftpair_gallery']:$options['ftpair_gallery'];
                
                if ( $options != $newoptions ) {
                        $options = $newoptions;
                        update_option('mp_ftpair_op', $options);
                }

 	} 

	if(isset($_POST['Use_Default'])){
            update_option('mp_ftpair_op', $options);
        }
        
        $ftpair_width = $options['ftpair_width'];
        if (!is_numeric($ftpair_width) || !ftpair_mp_testRange(intval($ftpair_width),100,1500)) {$ftpair_width=450;} //to be checked
	$ftpair_height = $options['ftpair_height'];
        if (!is_numeric($ftpair_height) || !ftpair_mp_testRange(intval($ftpair_height),100,1500)) {$ftpair_height=450;} //to be checked
	$ftpair_pairs = $options['ftpair_pairs'];
        if (!is_numeric($ftpair_pairs) || !ftpair_mp_testRange(intval($ftpair_pairs),2,20)) {$ftpair_pairs=4;}
        
        $ftpair_bgcolor = $options['ftpair_bgcolor'];
        $ftpair_bgcolor = str_replace('#', '', $ftpair_bgcolor);
        if (!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i', '#'.$ftpair_bgcolor)) $ftpair_bgcolor = 'FFFFFF';
        
        $ftpair_cardcolor = $options['ftpair_cardcolor'];
        $ftpair_cardcolor = str_replace('#', '', $ftpair_cardcolor);
        if (!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i', '#'.$ftpair_cardcolor)) $ftpair_cardcolor = 'FEC';
        
        $ftpair_cardbordercolor = $options['ftpair_cardbordercolor'];
        $ftpair_cardbordercolor = str_replace('#', '', $ftpair_cardbordercolor);
        if (!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i', '#'.$ftpair_cardbordercolor)) $ftpair_cardbordercolor = 'F96';
        
        $ftpair_gallery = $options['ftpair_gallery'];
        if (!$ftpair_gallery || $ftpair_gallery=='') {
            $ftpair_gallery = 'wp-content/plugins/mypuzzle-find-the-pair-a-memory-game/gallery';
        } else {
            $ftpair_gallery = ftpair_mp_clearpath($ftpair_gallery);
        }
        
        
	?>
        <form method="POST" name="options" target="_self" enctype="multipart/form-data">
	<h3><?php _e("Find The Pair Puzzle Parameters") ?></h3>
	
        <table width="" border="0" cellspacing="10" cellpadding="0">
            <tr>
                <td width="100">
                    Puzzle width in px
                </td>
                <td>
                    <input style="width: 150px" type="text" name="ftpair_width" value="<?php echo ($ftpair_width); ?>">
                    
                </td>
                <td width="500">
                    500 - equates to 500 pixel width for the puzzle
                </td>
            </tr>
            <tr>
                <td width="100">
                    Puzzle height in px
                </td>
                <td>
                    <input style="width: 150px" type="text" name="ftpair_height" value="<?php echo ($ftpair_height); ?>">
                    
                </td>
                <td width="500">
                    300 - equates to 300 pixel height for the puzzle
                </td>
            </tr>
            <tr>
                <td width="50">
                    Pair count
                </td>
                <td>
                    <select name="ftpair_pairs" id="ftpair_pairs" style="width: 150px">
                            <option value="6"<?php echo ($ftpair_pairs == 6 ? " selected" : "") ?>><?php echo _e("6 Pairs") ?></option>
                            <option value="8"<?php echo ($ftpair_pairs == 8 ? " selected" : "") ?>><?php echo _e("8 Pairs") ?></option>
                            <option value="10"<?php echo ($ftpair_pairs == 10 ? " selected" : "") ?>><?php echo _e("10 Pairs") ?></option>
                            <option value="12"<?php echo ($ftpair_pairs == 12 ? " selected" : "") ?>><?php echo _e("12 Pairs") ?></option>
                            <option value="13"<?php echo ($ftpair_pairs == 13 ? " selected" : "") ?>><?php echo _e("13 Pairs") ?></option>
                            <option value="15"<?php echo ($ftpair_pairs == 15 ? " selected" : "") ?>><?php echo _e("15 Pairs") ?></option>
                            <option value="18"<?php echo ($ftpair_pairs == 18 ? " selected" : "") ?>><?php echo _e("18 Pairs") ?></option>
                            <option value="21"<?php echo ($ftpair_pairs == 21 ? " selected" : "") ?>><?php echo _e("21 Pairs") ?></option>
                    </select>
                </td>
                <td width="200">
                    This configures the complexity and amount of overall pairs.
                </td>
            </tr>
            <tr>
                <td width="100">
                    Background color
                </td>
                <td>
                    <input style="width: 150px" type="text" name="ftpair_bgcolor" value="<?php echo ($ftpair_bgcolor); ?>">
                </td>
                <td width="200">Like #FFFFFF for white.</td>
            </tr>
            <tr>
                <td width="100">
                    Card color
                </td>
                <td>
                    <input style="width: 150px" type="text" name="ftpair_cardcolor" value="<?php echo ($ftpair_cardcolor); ?>">
                </td>
                <td width="200">Like #FEC for orange.</td>
            </tr>
            <tr>
                <td width="100">
                    Card border color
                </td>
                <td>
                    <input style="width: 150px" type="text" name="ftpair_cardbordercolor" value="<?php echo ($ftpair_cardbordercolor); ?>">
                </td>
                <td width="200">Like #F96 for dark orange.</td>
            </tr>
            <tr>
                <td width="100">
                    Path to Gallery
                </td>
                <td>
                    <input style="width: 200px" type="text" name="ftpair_gallery" value="<?php echo ($ftpair_gallery); ?>">
                </td>
                <td width="700">
                    Point to your own image directory or leave blank for MyPuzzle Images Gallery. 
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="Update" class="button-primary" />
        </p>
        </form>
    </div>


<?php } 

