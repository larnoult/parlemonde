<?php
/**
 * Cyclone Slider
 *
 * Displays the slider on template files.
 *
 * @param string $slider_slug The slug of the slider.
 */
function cyclone_slider( $slider_slug ){
	global $cyclone_slider_plugin_instance;
	if(isset($cyclone_slider_plugin_instance)){
		echo $cyclone_slider_plugin_instance->cycloneslider_shortcode( array('id'=>$slider_slug) );
	}
}

/**
* Print with a twist
*/
function cyclone_slider_debug($out){
	return '<pre>'.print_r($out, true).'</pre>';
}

/**
 * Cyclone Slide Image URL
 *
 * Gets the url of the slide image
 *
 * @return string The url to the image. False on failure.
 */
function cyclone_slide_image_url( $original_attachment_id, $width, $height, $params ){
	$param_defaults = array(
		'fresh_resize' => false,
		'current_slide_settings' => array(),
		'slideshow_settings' => array(),
		'resize_option' => 'auto',
		'resize_quality' => 90
	);
	$params = wp_parse_args($params, $param_defaults);
	$params = apply_filters('cycloneslider_thumbnailer_params', $params);
	
	// Get url to full image, its width and height
	$image_dimensions = wp_get_attachment_image_src($original_attachment_id, 'full');
	if(!$image_dimensions){
		return false;
	}
	
	// If orig image width and height is the same as slideshow width and height, do not resize and return url
	list($image_url, $orig_width, $orig_height) = $image_dimensions;
	$image_url = apply_filters('cycloneslider_image_url', $image_url, $params['current_slide_settings']);
	if($orig_width == $width and $orig_height == $height){
		return $image_url;
	}
	
	//If resize is no, return url
	if( isset($params['slideshow_settings']['resize']) and !$params['slideshow_settings']['resize']){
		return $image_url;
	}
	
	//Check if we have GD library, if none, return url. Prevent fatal error on users without GD installed
	if(!function_exists('gd_info')){
		return $image_url;	
	}
	
	$dir = wp_upload_dir();
	
	// Get full path to the slide image
	$image_path = get_attached_file($original_attachment_id);
	$image_path = apply_filters('cycloneslider_image_path', $image_path, $params['current_slide_settings']);
	if(empty($image_path)){
		return false;
	}
	
	// Resize
	$info = pathinfo($image_path);
	$dirname = isset($info['dirname']) ? $info['dirname'] : ''; // Path to directory
	$ext = isset($info['extension']) ? $info['extension'] : ''; // File extension Eg. "jpg"
	$thumb = wp_basename($image_path, ".$ext")."-{$width}x{$height}.{$ext}"; // Thumbname. Eg. [imagename]-[width]x[height].[ext]
	
	// Check if thumb already exists. If it is, return its url, unless refresh is true
	if(file_exists($dirname.'/'.$thumb ) and !$params['fresh_resize']){
		return dirname($image_url).'/'.$thumb; //We used dirname() since we need the URL format not the path
	}
	
	$resizeObj = new Image_Resizer($image_path);
	$resizeObj -> resizeImage($width, $height, $params['resize_option']);
	$resizeObj -> saveImage($dirname.'/'.$thumb, $params['resize_quality']);
	
	return dirname($image_url).'/'.$thumb;
}

/**
 * Cycle Settings
 *
 * Prints out cycle2 data attributes from slideshow settings 
 *
 * @param array $slider_settings Slider settings array.
 * @param string $slider_id HTML ID of slideshow.
 * @return string Data attributes for cycle2.
 */
function cyclone_settings($slider_settings, $slider_id='', $cycle2_settings=array()){
	$defaults = array();
	$defaults['data-cycle-slides'] = '&gt; div';
	$defaults['data-cycle-auto-height'] = $slider_settings['width'].':'.$slider_settings['height'];
	$defaults['data-cycle-fx'] = $slider_settings['fx'];
	$defaults['data-cycle-speed'] = $slider_settings['speed'];
	$defaults['data-cycle-timeout'] = $slider_settings['timeout'];
	$defaults['data-cycle-pause-on-hover'] = $slider_settings['hover_pause'];
	$defaults['data-cycle-pager'] = '#cycloneslider-'.$slider_id.' .cycloneslider-pager';
	$defaults['data-cycle-prev'] = '#cycloneslider-'.$slider_id.' .cycloneslider-prev';
    $defaults['data-cycle-next'] = '#cycloneslider-'.$slider_id.' .cycloneslider-next';
	$defaults['data-cycle-tile-count'] = $slider_settings['tile_count'];
	$defaults['data-cycle-tile-delay'] = $slider_settings['tile_delay'];
	$defaults['data-cycle-tile-vertical'] = $slider_settings['tile_vertical'];
	$defaults['data-cycle-log'] = 'false';
	$cycle2_settings = wp_parse_args($cycle2_settings, $defaults);
	
	$cycle2_settings = apply_filters('cyclone_cycle2_settings_array', $cycle2_settings);
	
	$out = '';
	foreach($cycle2_settings as $data_attr=>$value){ //Array to html string
		$out .= ' '.$data_attr.'="'.$value.'" ';
	}
	return $out;
}

/**
 * Cyclone Slide Settings
 *
 * Prints out cycle2 per slide settings as data attributes
 *
 *
 * @param array $slider_meta Slide settings array.
 * @param array $slider_settings Slider settings array.
 * @param string $slider_id HTML ID of slideshow.
 * @param int $slider_count Current slideshow count.
 * @return string Data attributes for slide.
 */
function cyclone_slide_settings($slider_meta, $slider_settings=array(), $slider_id='', $slider_count=1){
	$cycle2_settings = array();
	if(!empty($slider_meta['enable_slide_effects'])){
		if($slider_meta['fx']!='default') {
			$cycle2_settings['data-cycle-fx'] = $slider_meta['fx'];
		}
		if(!empty($slider_meta['speed'])) {
			$cycle2_settings['data-cycle-speed'] = $slider_meta['speed'];
		}
		if(!empty($slider_meta['timeout'])) {
			$cycle2_settings['data-cycle-timeout'] = $slider_meta['timeout'];
		}
		if($slider_meta['fx']=='tileBlind' or $slider_meta['fx']=='tileSlide'){
			if(!empty($slider_meta['tile_count'])) {
				$cycle2_settings['data-cycle-tile-count'] = $slider_meta['tile_count'];
			}
			if(!empty($slider_meta['tile_delay'])) {
				$cycle2_settings['data-cycle-tile-delay'] = $slider_meta['tile_delay'];
			}
			$cycle2_settings['data-cycle-tile-vertical'] = $slider_meta['tile_vertical'];
		}
		
	}
	$cycle2_settings = apply_filters('cyclone_cycle2_slide_settings_array', $cycle2_settings, $slider_meta, $slider_settings);
	
	$out = '';
	foreach($cycle2_settings as $data_attr=>$value){ //Array to html string
		$out .= ' '.$data_attr.'="'.$value.'" ';
	}
	return $out;
}


/*** Deprecated functions as of 2.3.0 ***/
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
function cycloneslider_thumb( $original_attachment_id, $width, $height, $refresh = false, $slide_meta = array(), $option="auto" ){
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

	// If image width and height is the same as slideshow, do not resize
	$image_dimensions = wp_get_attachment_image_src($original_attachment_id, 'full');
	if($image_dimensions[1] == $width and $image_dimensions[2] == $height){
		return $image_url;
	}
	
	// Resize
	$info = pathinfo($image_path);
	$dirname = isset($info['dirname']) ? $info['dirname'] : ''; // Path to directory
	$ext = isset($info['extension']) ? $info['extension'] : ''; // File extension Eg. "jpg"
	$thumb = wp_basename($image_path, ".$ext")."-{$width}x{$height}.{$ext}"; // Thumbname. Eg. [imagename]-[width]x[height].hpg
	
	// Check if thumb already exists. If it is, return its url, unless refresh is true
	if(file_exists($dirname.'/'.$thumb ) and !$refresh){
		return dirname($image_url).'/'.$thumb; //We used dirname() since we need the URL format not the path
	}
	
	$resizeObj = new Image_Resizer($image_path);
	$resizeObj -> resizeImage($width, $height, $option);
	$resizeObj -> saveImage($dirname.'/'.$thumb, 90);
	
	return dirname($image_url).'/'.$thumb;
}

/**
 * Cycle Settings Printer
 *
 * Prints out cycle slideshow settings in templates
 *
 *
 * @param array $slider_settings Slider settings array.
 * @param string $slider_id HTML ID of slideshow.
 * @param int $slider_count Current slideshow count.
 * @return string Data attributes for slideshow.
 */
function cycloneslider_settings($slider_settings, $slider_id='', $slider_count=1){
	$out = ' data-cycle-slides="&gt; div"';
	$out .= ' data-cycle-auto-height="'.$slider_settings['width'].':'.$slider_settings['height'].'"';
	$out .= ' data-cycle-fx="'.$slider_settings['fx'].'"';
	$out .= ' data-cycle-speed="'.$slider_settings['speed'].'"';
	$out .= ' data-cycle-timeout="'.$slider_settings['timeout'].'"';
	$out .= ' data-cycle-pause-on-hover="'.$slider_settings['hover_pause'].'"';
	$out .= ' data-cycle-pager="#cycloneslider-'.$slider_id.' .cycloneslider-pager"';
	$out .= ' data-cycle-prev="#cycloneslider-'.$slider_id.' .cycloneslider-prev"';
    $out .= ' data-cycle-next="#cycloneslider-'.$slider_id.' .cycloneslider-next"';
	$out .= ' data-cycle-tile-count="'.$slider_settings['tile_count'].'"';
	$out .= ' data-cycle-tile-delay="'.$slider_settings['tile_delay'].'"';
	$out .= ' data-cycle-tile-vertical="'.$slider_settings['tile_vertical'].'"';
	$out .= ' data-cycle-log="false"';
	$out = apply_filters('cycloneslider_cycle_settings', $out);
	return $out;
}

/**
 * Cycle Slide Settings Printer
 *
 * Prints out cycle slide settings in templates
 *
 *
 * @param array $slider_meta Slide settings array.
 * @param array $slider_settings Slider settings array.
 * @param string $slider_id HTML ID of slideshow.
 * @param int $slider_count Current slideshow count.
 * @return string Data attributes for slide.
 */
function cycloneslider_slide_settings($slider_meta, $slider_settings=array(), $slider_id='', $slider_count=1){
	$out = '';
	if(empty($slider_meta['enable_slide_effects'])){
		return $out;
	}
	if($slider_meta['fx']!='default') {
		$out .= ' data-cycle-fx="'.$slider_meta['fx'].'"';
	}
	if(!empty($slider_meta['speed'])) {
		$out .= ' data-cycle-speed="'.$slider_meta['speed'].'"';
	}
	if(!empty($slider_meta['timeout'])) {
		$out .= ' data-cycle-timeout="'.$slider_meta['timeout'].'"';
	}
	if($slider_meta['fx']=='tileBlind' or $slider_meta['fx']=='tileSlide'){
		if(!empty($slider_meta['tile_count'])) {
			$out .= ' data-cycle-tile-count="'.$slider_meta['tile_count'].'"';
		}
		if(!empty($slider_meta['tile_delay'])) {
			$out .= ' data-cycle-tile-delay="'.$slider_meta['tile_delay'].'"';
		}
		$out .= ' data-cycle-tile-vertical="'.$slider_meta['tile_vertical'].'"';
	}
	$out = apply_filters('cycloneslider_cycle_slide_settings', $out);
	return $out;
}