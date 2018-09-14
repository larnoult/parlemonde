<?php
/*
Plugin Name: Cyclone Slider
Plugin URI: http://www.codefleet.net/cyclone-slider/
Description: Create amazing slideshows with ease. Built for both developers and non-developers.
Version: 1.3.4
Author: Nico Amarilla
Author URI: http://www.codefleet.net/
License:

  Copyright 2012 (kosinix@codefleet.net)

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
require_once('inc/class-cyclone-slider.php');
require_once('inc/class-image-resizer.php');
require_once('inc/class-nextgen-integration.php');

if(!defined('CYCLONE_VERSION')){
    define('CYCLONE_VERSION', '1.3.4' );
}
if(!defined('CYCLONE_PATH')){
	define('CYCLONE_PATH', realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR );
}
if(!defined('CYCLONE_URL')){
	define('CYCLONE_URL', plugin_dir_url(__FILE__));
}

if(class_exists('Cyclone_Slider')):
	$cyclone_slider_1 = new Cyclone_Slider();
endif;

/**
 * Thumbnailer
 *
 * Creates thumbnail of the slide image using the specified attachment ID, width and height
 *
 *
 * @param int $original_attachment_id Attachment ID.
 * @param int $width Width of thumbnail in pixels.
 * @param int $height Height of thumbnail in pixels.
 * @param bool $refresh Recreate thumbnail if it already exists if set to true. Default to false, will not recreate thumbnails if it already exist.
 * @return string The url to the thumbnail. False on failure.
 */
// 
function cycloneslider_thumb( $original_attachment_id, $width, $height, $refresh = false, $slide_meta = array() ){
	$dir = wp_upload_dir();
	
	// Get full path to the slide image
	$image_path = get_attached_file($original_attachment_id);
	$image_path = apply_filters('cycloneslider_image_path', $image_path, $slide_meta);
	if(empty($image_path)){
		return false;
	}
	
	// Full url to the slide image
	$image_url = wp_get_attachment_url($original_attachment_id);
	$image_url = apply_filters('cycloneslider_image_url', $image_url, $slide_meta);
	if(empty($image_url)){
		return false;
	}

	$info = pathinfo($image_path);
	$dirname = isset($info['dirname']) ? $info['dirname'] : ''; // Path to directory
	$ext = isset($info['extension']) ? $info['extension'] : ''; // File extension Eg. "jpg"
	$thumb = wp_basename($image_path, ".$ext")."-{$width}x{$height}.{$ext}"; // Thumbname. Eg. [imagename]-[width]x[height].hpg
	
	// Check if thumb already exists. If it is, return its url, unless refresh is true
	if(file_exists($dirname.'/'.$thumb ) and !$refresh){
		return dirname($image_url).'/'.$thumb; //We used dirname() since we need the URL format not the path
	}
	
	$resizeObj = new Image_Resizer($image_path);
	$resizeObj -> resizeImage($width, $height);
	$resizeObj -> saveImage($dirname.'/'.$thumb, 80);
	
	return dirname($image_url).'/'.$thumb;
}

/**
 * Transparent GIF Generator
 *
 * Creates trasnparent gif for use by the responsive template passing the width and height
 *
 * @since 1.1.0
 *
 * @param int $width Width in pixels.
 * @param int $height Height in pixels.
 * @param bool $refresh Recreate if it already exists if set to true. Default to false, will not recreate if it already exist.
 * @return string The url to the image. False on failure.
 */
// 
function cycloneslider_trans( $width=1, $height=1, $refresh = false ){
	$dir = wp_upload_dir();
	
	// Check if thumb already exists. Return its url
	$thumb = "cycloneslider_trans-{$width}x{$height}.gif";
	if(file_exists($dir['path'].'/'.$thumb ) and !$refresh){
		return $dir['url'].'/'.$thumb;
	}
	
	// Create it
	if(function_exists('imagecreate')){ //check for gd lib
		$image = imagecreate( $width, $height );
		$background = imagecolorallocate( $image,  255, 255, 255);
		
		imagecolortransparent($image, $background);
		
		if(!imagegif($image, $dir['path'].'/'.$thumb)){
			return false; //error
		}
		imagedestroy( $image );
	} else {
		return false;
	}
	// Get full url to the image 
	return $dir['url'].'/'.$thumb;
}


// Add settings page
add_action( 'admin_menu', 'cs_add_menu_and_page');
	
function cs_add_menu_and_page(){

	// Use built-in WP function
	add_submenu_page(
		'edit.php?post_type=cycloneslider',
		__('Cyclone Slider Settings', 'cycloneslider'),
		__('Settings', 'cycloneslider'),
		'manage_options',
		'cycloneslider-settings',
		'cs_add_menu_and_page_render_page'
	);
}
function cs_add_menu_and_page_render_page(){
	?><div class="wrap"><h1>Settings</h1>
	<p><a class="button-secondary" href="<?php echo get_admin_url(); ?>?cs_legacy_mode=off">Turn off Legacy Mode</a></p>
	</div><?php
}