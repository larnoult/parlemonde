 <?php 
 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $virtue_premium; 
	$height = get_post_meta( $post->ID, '_kad_posthead_height', true ); 
	if (!empty($height)){
	  	$slideheight = $height;
	} else {
	  	$slideheight = 400; 
	}
	$swidth = get_post_meta( $post->ID, '_kad_posthead_width', true ); 
	if (!empty($swidth)) { 
	  	$slidewidth = $swidth; 
	} else {
	  	$slidewidth = 1140;
	}
	if(isset($virtue_premium['slider_autoplay']) && 0 == $virtue_premium['slider_autoplay']) {
	  	$autoplay = 'false';
	} else {
	  	$autoplay = 'true';
	}	
  	if(isset($virtue_premium['slider_captions']) && 1 == $virtue_premium['slider_captions']) { 
  		$captions = 'true'; 
  	} else {
  		$captions = 'false';
  	}
   	if(isset($virtue_premium['slider_pausetime'])) {
   		$pausetime = $virtue_premium['slider_pausetime'];
	} else {
		$pausetime = '7000';
	}
	$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );

	
		echo '<section class="pagefeat carousel_outerrim"><div class="carousel_slider_outer" style="max-width:'.esc_attr($slidewidth).'px;">';
			virtue_build_slider($post->ID, $image_gallery, null, $slideheight, 'image', 'kt-slider-different-image-ratio carousel_slider','slider', $captions, $autoplay, $pausetime, 'true', 'false'); 
  		echo '</div></section>';
            