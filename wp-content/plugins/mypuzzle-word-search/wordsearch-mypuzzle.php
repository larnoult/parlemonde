<?php
/*
Plugin Name: MyPuzzle - Word Search
Plugin URI: http://mypuzzle.org/wordsearch/wordpress.html
Description: Include a mypuzzle.org Word Search Puzzle in your blogs with just one shortcode. 
Version: 1.3.1
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

//include_once("wordsearch-plugin.php");

/**
 * Default Options
 */
function get_wordsearch_mp_options ($default = false){
	$shc_default = array(
            'ws_size' => '500',
            'ws_dimension' => '8',
            'ws_wordlistmenu' => '1',
            'ws_wordlistside' => 'right',
            'mywordlist' => '',
            'ws_bgcolor' => '#ffffff',
            'ws_fontcolor' => '#000000',
            'ws_focuscolor' => '#e9e9e9',
            'ws_foundcolor' => '#caebcc',
            'ws_showlink' => '0'
            );
	if ($default) {
		update_option('wordsearch_mp_set', $shc_default);
		return $shc_default;
	}
	
	$options = get_option('wordsearch_mp_set');
	if (isset($options))
		return $options;
	update_option('wordsearch_mp_set', $shc_default);
	return $options;
}

/**
 * The Shortcode
 */
add_action('wp_enqueue_scripts', 'wordsearch_mp_jscripts');
add_shortcode('wordsearch-mp', 'wordsearch_mp');


function wordsearch_mp_jscripts() {
    wp_enqueue_script( 'jquery' );
    //my jscripts
    wp_register_script('mp-wordsearch-js', plugins_url('/js/wordsearch-plugin.js', __FILE__));
    wp_enqueue_script('mp-wordsearch-js');
    wp_register_script('mp-wordsearch-pop', plugins_url('/js/jquery.bpopup-0.7.0.min.js', __FILE__));
    wp_enqueue_script('mp-wordsearch-pop');
    //my styles
    wp_register_style( 'mp-wordsearch-style', plugins_url('/css/wordsearch-plugin.css', __FILE__) );
    wp_enqueue_style( 'mp-wordsearch-style' );
}    
 
function wordsearch_mp_testRange($int,$min,$max) {     
    return ($int>=$min && $int<=$max);
}

function wordsearch_mp($atts) {
	global $post;
	$options = get_wordsearch_mp_options();	
	
        //get options, or set default
	$ws_size = $options['ws_size'];
        if (!is_numeric($ws_size) || !wordsearch_mp_testRange(intval($ws_size),100,1500)) {$ws_size=460;}
        $ws_dimension = $options['ws_dimension'];
        if (!is_numeric($ws_dimension) || !wordsearch_mp_testRange(intval($ws_dimension),6,17)) {$ws_dimension=6;}
        $wordlistmenu = $options['ws_wordlistmenu'];
        if (!is_numeric($wordlistmenu) || !wordsearch_mp_testRange(intval($wordlistmenu),0,1)) {$wordlistmenu=1;}
        $wordlistside = $options['ws_wordlistside'];
        if (strtolower($wordlistside!="left")) 
            $wordlistside="right";
        $mywordlist = $options['mywordlist'];
        if (!$mywordlist || $mywordlist == '')
            $mywordlist = "one,two,three,four,five,six,seven,eight,nine,ten";
            
        $ws_bgcolor = $options['ws_bgcolor'];
        $ws_bgcolor = str_replace('#', '', $ws_bgcolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_bgcolor)) $ws_bgcolor = 'FFFFFF';
        $ws_fontcolor = $options['ws_fontcolor'];
        $ws_fontcolor = str_replace('#', '', $ws_fontcolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_fontcolor)) $ws_fontcolor = '000000';
        $ws_focuscolor = $options['ws_focuscolor'];
        $ws_focuscolor = str_replace('#', '', $ws_focuscolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_focuscolor)) $ws_focuscolor = '000000';
        $ws_foundcolor = $options['ws_foundcolor'];
        $ws_foundcolor = str_replace('#', '', $ws_foundcolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_foundcolor)) $ws_foundcolor = '000000';
        
        $ws_showlink = $options['ws_showlink'];        

	extract(shortcode_atts(array(
                'ws_size' => $ws_size,
                'ws_dimension' => $ws_dimension,
                'ws_wordlistmenu' => $wordlistmenu,
                'ws_wordlistside' => $wordlistside,
                'mywordlist' => $mywordlist,
                'ws_bgcolor' => $ws_bgcolor,
                'ws_fontcolor' => $ws_fontcolor,
                'ws_focuscolor' => $ws_focuscolor,
                'ws_foundcolor' => $ws_foundcolor,
                'ws_showlink' => $ws_showlink                
	), $atts));
        $mywordlist = str_replace (' ', '', $mywordlist);
        $ws_bgcolor = str_replace('#', '', $ws_bgcolor);
        $ws_fontcolor = str_replace('#', '', $ws_fontcolor);
        $ws_focuscolor = str_replace('#', '', $ws_focuscolor);
        $ws_foundcolor = str_replace('#', '', $ws_foundcolor);
        
        $get_ws_grid = plugins_url('get_ws_grid.php', __FILE__);
        $closebuton = plugins_url('img/close_button.png', __FILE__);
        
        if ($mywordlist != "one,two,three,four,five,six,seven,eight,nine,ten")
            $ws_showlink = '1';
        
        if (strtolower($wordlistside) == "left") {
            $wsgrid_float = "right";
            $wsside_float = "left";
        } else {
            $wsgrid_float = "left";
            $wsside_float = "right";
        }
        
        if ($wordlistmenu == 1) {
            $ws_grid_size = "width: 70%";
            $tdWidth = intval($ws_size * 0.7 / $ws_dimension);
            $tdHeight = $tdWidth;
            $tdFontsize = intval($tdHeight / 2);
            $wsside_width = intval($ws_size * 0.25);
            $wlFontws_size = intval($ws_size / 40);
        } else {
            $ws_grid_size = "width: 100%";
            $tdWidth = intval($ws_size / $ws_dimension);
            $tdHeight = $tdWidth;
            $tdFontsize = intval($tdHeight / 2);
            $wsside_width = 0;
            $wlFontws_size = intval($ws_size / 40);
        }
        
        $output = "<style>table td.ws_size {width:".$tdWidth."px;height:".$tdHeight."px;margin:0;padding:0 0 0 0;border: 0;vertical-align:middle;text-align: center;font-size: ".$tdFontsize."px;}\r";
        $output .= "#ws_wrapper {float: left}";
        
        $output .= "#ws-grid {width:".$ws_grid_size.";height:".$ws_grid_size.";font-size: ".$tdFontsize."px;color:#".$ws_fontcolor.";background-color:#".$ws_bgcolor.";float: ".$wsgrid_float."}";
        $output .= "#ws-side {height:".$ws_grid_size.";padding-left:10px;width:".$wsside_width."px;font-size: ".$wlFontws_size."px;color:#".$ws_fontcolor.";background-color:#".$ws_bgcolor.";float: ".$wsside_float."}";
        
        $output .= "table td {background-color:#".$ws_bgcolor.";color:#".$ws_fontcolor.";}";
        $output .= "table td.focused {background-color:#".$ws_focuscolor.";}";
        $output .= "table td.found {background-color:#".$ws_foundcolor.";}";
        $output .= "table td.startend {background-color:#709ffe;}";
        $output .= "A:hover {color: #".$ws_fontcolor."; text-decoration: underline; }";
        $output .= "</style>";

        //wordsearch game grid
        $output .= "<div id='ws_wrapper'>";
        
        if (strtolower($wordlistside) == "right")
            $output .= "    <div id='ws-grid' class='ws_grid_size'></div>";
        
        if ($wordlistmenu == 1) {
            $output .= "    <div id='ws-side'>";
            $output .= "        <p><strong>Hidden Words</strong></p>";
            $output .= "        <div id='wordlist'></div>";
            $output .= "    </div>";
        }
        if (strtolower($wordlistside) == "left")
            $output .= "    <div id='ws-grid'</div>";
        
        $output .= "<div style='background-color:#".$ftpair_bgcolor.";width:".$ftpair_width."px'>";
        $output .= "  <div id='mem-grid'></div>";
        $output .= "  <div style='text-align: right;width:".intval($ws_size/2)."px;float: right;font-size:12px;'><a href='http://mypuzzle.org/'>".wordsearch_mp_getrndanchor()."</a> by mypuzzle.org</div>";
        $output .= "  <div style='width:".intval($ws_size/2)."px;float: left;font-size:12px;'><a id='aRestart' href=''>Restart</a></div>";
        $output .= "</div>";
        
        $output .= "</div>";
        
        $output .= "<div id='ws_popup' style='z-index:1;'>\r";
        $output .= "    <span class='ws_button bClose'><img src='".$closebuton."' /></span>\r";
        $output .= "    <div class='ws_popcontent'></div>\r";
        $output .= "</div>\r";
        
        
        $output .= "<div id='var_mp_ws_getscript' style='visibility:hidden;position:absolute'>".$get_ws_grid."</div>\r";
        $output .= "<div id='var_mp_ws_width' style='visibility:hidden;position:absolute'>".$ws_size."</div>\r";
        //$output .= "<div id='var_mp_ws_showlink' style='visibility:hidden;position:absolute'>".$ws_showlink."</div>\r";
        //$output .= "<div id='var_mp_ws_domain' style='visibility:hidden;position:absolute'>".wordsearch_mp_getdomainmd5()."</div>\r";
        //restart functionality
        $output .= "<script language='javascript'>\r";
        $output .= "jQuery('#aRestart').click(function(event) {event.preventDefault();init_grid('".$mywordlist."', '".$ws_dimension."');";
        $output .= "ftpair_mp_memory(".$ftpair_pairs.");\r";
        $output .= "</script>\r";
        
        $output .= "<script language='javascript'>";
        $output .= "init_grid('".$mywordlist."', '".$ws_dimension."');";
        $output .= "</script>\r";
        
        
        return($output);
}
function wordsearch_mp_getdomainmd5()
{
    $md5Str = strtolower(substr(strval(md5(strtolower($_SERVER['HTTP_HOST']))), 0, 1));
    return($md5Str);
}
function wordsearch_mp_getrndanchor()
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

add_action('admin_menu', 'wordsearch_mp_set');

function wordsearch_mp_set() {
	$plugin_page = add_options_page('MyPuzzle Word Search', 'MyPuzzle Word Search', 'administrator', 'mypuzzle-wordsearch', 'wordsearch_mp_options_page');		
 }

function wordsearch_mp_options_page() {

	$options = get_wordsearch_mp_options();
	
    if(isset($_POST['Restore_Default']))	$options = get_wordsearch_mp_options(true);	?>

	<div class="wrap">   
	
	<h2><?php _e("MyPuzzle - Word Search Puzzle Settings") ?></h2>
	
	<?php 

	if(isset($_POST['Submit'])){
                $newoptions['ws_showlink'] = isset($_POST['ws_showlink'])?$_POST['ws_showlink']:$options['ws_showlink'];
     		$newoptions['ws_size'] = isset($_POST['ws_size'])?$_POST['ws_size']:$options['ws_size'];
     		$newoptions['ws_dimension'] = isset($_POST['ws_dimension'])?$_POST['ws_dimension']:$options['ws_dimension'];
                $newoptions['ws_wordlistmenu'] = isset($_POST['ws_wordlistmenu'])?$_POST['ws_wordlistmenu']:$options['ws_wordlistmenu'];
                $newoptions['ws_wordlistside'] = isset($_POST['ws_wordlistside'])?$_POST['ws_wordlistside']:$options['ws_wordlistside'];
                $newoptions['mywordlist'] = isset($_POST['mywordlist'])?$_POST['mywordlist']:$options['mywordlist'];
                $newoptions['ws_bgcolor'] = isset($_POST['ws_bgcolor'])?$_POST['ws_bgcolor']:$options['ws_bgcolor'];
                $newoptions['ws_fontcolor'] = isset($_POST['ws_fontcolor'])?$_POST['ws_fontcolor']:$options['ws_fontcolor'];
                $newoptions['ws_focuscolor'] = isset($_POST['ws_focuscolor'])?$_POST['ws_focuscolor']:$options['ws_focuscolor'];
                $newoptions['ws_foundcolor'] = isset($_POST['ws_foundcolor'])?$_POST['ws_foundcolor']:$options['ws_foundcolor'];
                
                if ( $options != $newoptions ) {
                        $options = $newoptions;
                        update_option('wordsearch_mp_set', $options);			
                }

 	} 

	if(isset($_POST['Use_Default'])){
            update_option('wordsearch_mp_set', $options);
        }
        $ws_showlink = $options['ws_showlink'];
        if (!is_numeric($ws_showlink) || !wordsearch_mp_testRange(intval($ws_showlink),0,1)) {$ws_showlink=0;}
	$ws_size = $options['ws_size'];
        if (!is_numeric($ws_size) || !wordsearch_mp_testRange(intval($ws_size),100,1500)) {$ws_size=460;}
	$ws_dimension = $options['ws_dimension'];
        if (!is_numeric($ws_dimension) || !wordsearch_mp_testRange(intval($ws_dimension),6,17)) {$ws_dimension=8;}
        $wordlistmenu = $options['ws_wordlistmenu'];
        if (!is_numeric($wordlistmenu) || !wordsearch_mp_testRange(intval($wordlistmenu),0,1)) {$wordlistmenu=1;}
        $wordlistside = $options['ws_wordlistside'];
        if (strtolower($wordlistside!="left")) 
            $wordlistside="right";
        $mywordlist = $options['mywordlist'];
        $ws_bgcolor = $options['ws_bgcolor'];
        $ws_bgcolor = str_replace('#', '', $ws_bgcolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_bgcolor)) $ws_bgcolor = 'FFFFFF';
        
        $ws_fontcolor = $options['ws_fontcolor'];
        $ws_fontcolor = str_replace('#', '', $ws_fontcolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_fontcolor)) $ws_fontcolor = '000000';
        
	$ws_focuscolor = $options['ws_focuscolor'];
        $ws_focuscolor = str_replace('#', '', $ws_focuscolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_focuscolor)) $ws_focuscolor = 'e9e9e9';
        
	$ws_foundcolor = $options['ws_foundcolor'];
        $ws_foundcolor = str_replace('#', '', $ws_foundcolor);
        if (!preg_match('/^[a-f0-9]{6}$/i', $ws_foundcolor)) $ws_foundcolor = 'caebcc';
        
	?>
        <form method="POST" name="options" target="_self" enctype="multipart/form-data">
	<h3><?php _e("Word Search Puzzle Parameters") ?></h3>
	
        <table width="" border="0" cellspacing="10" cellpadding="0">
            <tr>
                <td width="100">
                    Grid-Size in px
                </td>
                <td>
                    <input style="width: 100px" type="text" name="ws_size" value="<?php echo ($ws_size); ?>">
                    
                </td>
                <td width="500">
                    400 - equates to 400 pixel width for the puzzle
                </td>
            </tr>
            <tr>
                <td width="50">
                    Dimension
                </td>
                <td>
                    <select name="ws_dimension" id="ws_dimension" style="width: 100px">
                            <option value="6"<?php echo ($ws_dimension == 6 ? " selected" : "") ?>><?php echo _e("6x6") ?></option>
                            <option value="7"<?php echo ($ws_dimension == 7 ? " selected" : "") ?>><?php echo _e("7x7") ?></option>
                            <option value="8"<?php echo ($ws_dimension == 8 ? " selected" : "") ?>><?php echo _e("8x8") ?></option>
                            <option value="9"<?php echo ($ws_dimension == 9 ? " selected" : "") ?>><?php echo _e("9x9") ?></option>
                            <option value="10"<?php echo ($ws_dimension == 10 ? " selected" : "") ?>><?php echo _e("10x10") ?></option>
                            <option value="11"<?php echo ($ws_dimension == 11 ? " selected" : "") ?>><?php echo _e("11x11") ?></option>
                            <option value="12"<?php echo ($ws_dimension == 12 ? " selected" : "") ?>><?php echo _e("12x12") ?></option>
                            <option value="13"<?php echo ($ws_dimension == 13 ? " selected" : "") ?>><?php echo _e("13x13") ?></option>
                            <option value="14"<?php echo ($ws_dimension == 14 ? " selected" : "") ?>><?php echo _e("14x14") ?></option>
                            <option value="15"<?php echo ($ws_dimension == 15 ? " selected" : "") ?>><?php echo _e("15x15") ?></option>
                            <option value="16"<?php echo ($ws_dimension == 16 ? " selected" : "") ?>><?php echo _e("16x16") ?></option>
                            <option value="17"<?php echo ($ws_dimension == 17 ? " selected" : "") ?>><?php echo _e("17x17") ?></option>
                            
                    </select>
                </td>
                <td width="200"></td>
            </tr>
            <tr>
                <td width="100">
                    Background Color
                </td>
                <td>
                    <input style="width: 100px" type="text" name="ws_bgcolor" value="<?php echo ($ws_bgcolor); ?>">
                </td>
                <td width="200">e.g. "FFFFFF" for white background</td>
            </tr>
            <tr>
                <td width="100">
                    Text Color
                </td>
                <td>
                    <input style="width: 100px" type="text" name="ws_fontcolor" value="<?php echo ($ws_fontcolor); ?>">
                </td>
                <td width="200">e.g. "000000" for black text color</td>
            </tr>
            <tr>
                <td width="100">
                    Marker Color
                </td>
                <td>
                    <input style="width: 100px" type="text" name="ws_focuscolor" value="<?php echo ($ws_focuscolor); ?>">
                </td>
                <td width="200"></td>
            </tr>
            <tr>
                <td width="100">
                    Found Word Color
                </td>
                <td>
                    <input style="width: 100px" type="text" name="ws_foundcolor" value="<?php echo ($ws_foundcolor); ?>">
                </td>
                <td width="200"></td>
            </tr>
            <tr>
                <td width="100">
                    Show Wordlist
                </td>
                <td>
                    <select name="ws_wordlistmenu" id="ws_wordlistmenu" style="width: 100px">
                            <option value="1"<?php echo ($wordlistmenu == 1 ? " selected" : "") ?>><?php echo _e("Yes") ?></option>
                            <option value="0"<?php echo ($wordlistmenu == 0 ? " selected" : "") ?>><?php echo _e("No") ?></option>
                    </select>
                </td>
                <td width="500">
                    Displays a list of remaining words to be found.
                </td>
            </tr>
            <tr>
                <td width="100">
                    Wordlist appearance
                </td>
                <td>
                    <select name="ws_wordlistside" id="ws_wordlistside" style="width: 100px">
                            <option value="right"<?php echo ($wordlistside == 'right' ? " selected" : "") ?>><?php echo _e("Right") ?></option>
                            <option value="left"<?php echo ($wordlistside == 'left' ? " selected" : "") ?>><?php echo _e("Left") ?></option>
                    </select>
                </td>
                <td width="500">
                    Displays this list right or left to the play-grid.
                </td>
            </tr>
            <tr>
                <td width="100">
                    My Wordlist
                </td>
                <td>
                    <input style="width: 200px" type="text" name="mywordlist" value="<?php echo ($mywordlist); ?>">
                </td>
                <td width="700">
                    Enter a list of words that will build into a puzzle. Use comma's to separate words like 'Peter,Monica,John'
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="Update" class="button-primary" />
        </p>
        </form>
    </div>


<?php } 

