<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
    global $virtue_premium;

        if(isset($virtue_premium['slider_size'])) {$slideheight = $virtue_premium['slider_size'];} else { $slideheight = 400; }
        if(isset($virtue_premium['slider_size_width'])) {$slidewidth = $virtue_premium['slider_size_width'];} else { $slidewidth = 1140; }
        if(isset($virtue_premium['slider_captions']) && 1 == $virtue_premium['slider_captions']) { $captions = 'true'; } else {$captions = 'false';}
        if(isset($virtue_premium['home_slider'])) {$slides = $virtue_premium['home_slider']; } else {$slides = '';}
        if(isset($virtue_premium['slider_autoplay']) && 0 == $virtue_premium['slider_autoplay']) {$autoplay = 'false';} else {$autoplay = 'true';}
        if(isset($virtue_premium['slider_pausetime'])) {$pausetime = $virtue_premium['slider_pausetime'];} else {$pausetime = '7000';}
        if(isset($virtue_premium['trans_type']) && 'fade' == $virtue_premium['trans_type']) {$transtype = 'true';} else { $transtype = 'false';}
        if(isset($virtue_premium['slider_transtime'])) {$transtime = $virtue_premium['slider_transtime'];} else {$transtime = '300';}
                
	echo '<div class="sliderclass carousel_outerrim">';
 		echo '<div id="imageslider">';
  			virtue_build_slider_home($slides, $slidewidth, $slideheight, 'kt-slider-different-image-ratio carousel_slider', 'kt_slider_home', 'different-ratio', $captions, $autoplay, $pausetime, 'true', $transtype, $transtime); 
  		echo '</div><!--imageslider-->';
  	echo '</div>';

?>                  
            