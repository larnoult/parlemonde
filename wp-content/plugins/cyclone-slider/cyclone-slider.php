<?php
/*
Plugin Name: Cyclone Slider
Plugin URI: http://www.codefleet.net/cyclone-slider/
Description: Create and manage sliders with ease. Built for both casual users and developers.
Version: 3.2.0
Author: Nico Amarilla
Author URI: http://www.codefleet.net/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: cycloneslider
*/

// Legacy mode or not
$option = get_option('cyclone_option_name');

$get = $_GET;
if(isset($get['cs_legacy_mode']) and $get['cs_legacy_mode']='off'){
    $option['legacy'] = 0;
    update_option('cyclone_option_name', $option);
}
if ( isset($option['legacy']) and $option['legacy'] ) {
	require_once 'src/legacy/cyclone-slider.php';
} else {
	require_once 'src/autoloader.php';
	require_once 'src/plugin.php';
}
