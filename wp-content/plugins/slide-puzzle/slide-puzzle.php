<?php
/*
Plugin Name: Slide Puzzle
Plugin URI: http://www.colome.org
Description: Include a Sliding Puzzle in your blogs with just one shortcode. is Not flash!!, only html 5 and ccs 
Version: 1.0.0
Author: colome.disco@gmail.com
Author URI: http://www.colome.org/
Notes    :
*/


/*  Copyright 2013  colome.disco@gmail.com  

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

//include_once("sliding-plugin.php");

/**
 * Default Options
 */
function get_slide_options ($default = false){
	$shc_default = array(
            'pieces' => '4',
            'message' => 'Fantastic! you Win!',
			'myimage'=> 'http://puzle.colome.org/img001.jpg',
			'backcolor'=>'#000040',
			'endimage'=>'http://puzle.colome.org/img001.jpg'
                     );
	if ($default) {
		update_option('slide_conf', $shc_default);
		return $shc_default;
	}
	
	$options = get_option('slide_conf');
	if (isset($options))
		return $options;
	update_option('slide_conf', $shc_default);
	return $options;
}

/**
 * The Sortcode
 */
add_action('wp_enqueue_scripts', 'slide_puzzle_js');
add_shortcode('slide-puzzle', 'slide_p');


function slide_puzzle_js() {
    wp_enqueue_script( 'jquery' );
    
    //my jscripts
    wp_register_script('mp-sliding-js', plugins_url('/js/slide-puzzle.js', __FILE__));
    wp_enqueue_script('mp-sliding-js');
    
    //my styles
    wp_register_style( 'mp-sliding-style', plugins_url('/css/slide-puzzle.css', __FILE__) );
    wp_enqueue_style( 'mp-sliding-style' );
}    
 
function sliding_mp_testRange($int,$min,$max) {     
    return ($int>=$min && $int<=$max);
}

function slide_p($atts) {
	global $post;
	global $i;
	$i=$i+1;
	$options = get_slide_options();	
	
        //get options, or set default
		$pieces = $options['pieces'];
        $message = $options['message'];
        $myimage = $options['myimage'];
        $backcolor = $options['backcolor'];
        $endimage = $options['endimage'];
        

	extract(shortcode_atts(array(
                'pieces' => $pieces,
                'message' => $message,
                'myimage' => $myimage,
                'backcolor' => $backcolor,
                'endimage' => $endimage
	), $atts));
        
                
        $uploadDir = wp_upload_dir();

        //1. Abspath
        $absPath = ABSPATH;
        
		$urlar = parse_url($myimage);
		//echo('test: '.$urlar['host']);
		if ($urlar['host']=='') {
			$myimage = sliding_mp_clearpath($myimage); 
			$isurl = false;
		}else{
			$isurl = true;
		}  
       
        $siteurl = site_url();        
        
        
		if ($isurl)
			$myPic = $myimage;
		else
			$myPic = site_url() . '/'. $myimage;
		//----------------- end image -----------------
		$urlar = parse_url($endimage);
		//echo('test: '.$urlar['host']);
		if ($urlar['host']=='') {
			$endimage = sliding_mp_clearpath($endimage); 
			$isurl = false;
		}else{
			$isurl = true;
		}  
        
		$siteurl = site_url();        
		        
		if ($isurl)
			$myPice = $endimage;
		else
			$myPice = $siteurl . '/'. $endimage;
		if( $endimage=='')
		{
			$myPice=$myPic;
		}
        //------------------- end end image ----------------------
                
		$output = '<!-- sp Effect plugin by colome.com Starts-->';
		if ($i==1) $output .='<audio id="sound"> <source src="'.plugins_url('/slide-puzzle.mp3', __FILE__) .'" type="audio/mpeg">';
		if ($i==1) $output .='<source src="'.plugins_url('/slide-puzzle.ogg', __FILE__) .'" type="audio/ogg">    </audio>';	
		$output .='<div id="play" >';				
		$output .= '<img style="visibility:hidden" src="'.$myPic.'" name="sp_x"  id="sp_x_'.$i.'-'.$pieces.'">';
		$output .= '</a></div>';
		if ($i==1) $output .= '<script language=javascript> setTimeout(\'set_screen("'.plugins_url('/', __FILE__) .'")\',500);</script>';	
		$output .= '<div id="message'.($i-1).'" style="visibility:hidden;position:absolute">'.$message.'</div>';
		$output .= '<div id="backcolor'.($i-1).'" style="visibility:hidden;position:absolute">'.$backcolor.'</div>';
		$output .= '<img id="endimage'.($i-1).'" style="visibility:hidden;position:absolute" src="'.$myPice.'">';
		$output .= '<!-- sp Effect plugin by colome.com Ends-->';
        // $output = '<!-- sp Effect plugin by colome.com Starts-->';
		// if ($i==1) $output .='<audio id="sound"> <source src="'.plugins_url('/slide-puzzle.mp3', __FILE__) .'" type="audio/mpeg">';
		// if ($i==1) $output .='<source src="'.plugins_url('/slide-puzzle.ogg', __FILE__) .'" type="audio/ogg">    </audio>';	
		// $output .='<div id="play" >';				
		// $output .= '<img style="visibility:hidden" src="'.$myPic.'" name="sp_x"  id="sp_x_'.$i.'-'.$pieces.'">';
		// $output .= '</a></div>';
		// if ($i==1) $output .= '<script language=javascript> setTimeout(\'set_screen("'.plugins_url('/', __FILE__) .'")\',500);</script>';	
		// $output .= '<div id="message'.($i-1).'" style="visibility:hidden;position:absolute">'.$message.'</div>';
		// $output .= '<!-- sp Effect plugin by colome.com Ends-->';
        return($output);

}
function sliding_mp_clearpath($inputpath) {
    if (substr($inputpath, 0, 1)=='/') $inputpath = substr($inputpath, 1);
    if (substr($inputpath, strlen($inputpath)-1, 1)=='/') $inputpath = substr($inputpath, 0, strlen($inputpath)-1);
    return($inputpath);
}


/**
 * Settings
 */  

add_action('admin_menu', 'slide_conf');

function slide_conf() {
	$plugin_page = add_options_page('Slide Puzzle', 'Slide Puzzle', 'administrator', 'sudoku-sliding', 'sliding_mp_options_page');		
 }

function sliding_mp_options_page() {

	$options = get_slide_options();
	
    if(isset($_POST['Restore_Default']))	$options = get_slide_options(true);	?>

	<div class="wrap">   
	
	<h2><?php _e("Slide Puzzle Default Settings") ?></h2>
	
	<?php 

	if(isset($_POST['Submit'])){
				$newoptions['pieces'] = isset($_POST['pieces'])?$_POST['pieces']:$options['pieces'];
                $newoptions['myimage'] = isset($_POST['myimage'])?$_POST['myimage']:$options['myimage'];
                $newoptions['message'] = isset($_POST['message'])?$_POST['message']:$options['message'];
                $newoptions['backcolor'] = isset($_POST['backcolor'])?$_POST['backcolor']:$options['backcolor'];
                $newoptions['endimage'] = isset($_POST['endimage'])?$_POST['endimage']:$options['endimage'];
                
                if ( $options != $newoptions ) {
                        $options = $newoptions;
                        update_option('slide_conf', $options);			
                }

 	} 

	if(isset($_POST['Use_Default'])){
            update_option('slide_conf', $options);
        }
       
		$pieces = $options['pieces'];
        $myimage = $options['myimage'];
        $message = $options['message'];
        $backcolor = $options['backcolor'];
        $endimage = $options['endimage'];
        
	?>
        <form method="POST" name="options" target="_self" enctype="multipart/form-data">
	<h3><?php _e("Slide Puzzle Parameters") ?></h3>
	
        <table width="" border="0" cellspacing="10" cellpadding="0">
            
            <tr>
                <td width="100">
                    Pieces count
                </td>
                <td>
                    <input style="width: 100px" type="text" name="pieces" value="<?php echo ($pieces); ?>">                                   
                    
                <td width="200"></td>
            </tr>
            
            <tr>
                <td width="100">
                    Image Url/Path
                </td>
                <td>
                    <input style="width: 400px" type="text" name="myimage" value="<?php echo ($myimage); ?>">
                </td>
                <td width="700">
                    
                </td>
            </tr>
            
            <tr>
                <td width="100">
                    Message when finish
                </td>
                <td>
                    <input style="width: 300px" type="text" name="message" value="<?php echo ($message); ?>">
                </td>
                <td width="700">
                    
                </td>
            </tr>
			<tr>
                <td width="100">
                    Back Color (#rrggbb)
                </td>
                <td>
                    <input style="width: 100px" type="text" name="backcolor" value="<?php echo ($backcolor); ?>">
                </td>
                <td width="200">
                    
                </td>
            </tr>
			<tr>
                <td width="100">
                    Image to show when finish
                </td>
                <td>
                    <input style="width: 400px" type="text" name="endimage" value="<?php echo ($endimage); ?>">
                </td>
                <td width="700">
                    
                </td>
            </tr>
            
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="Update" class="button-primary" />
        </p>
        </form>
    </div>


<?php } 

