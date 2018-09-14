<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
  global $virtue_premium;

if ( isset( $virtue_premium[ 'slider_size' ] ) ) {
	$slideheight = $virtue_premium[ 'slider_size' ];
} else {
	$slideheight = 400;
}
if ( isset( $virtue_premium[ 'home_slider' ] ) ) {
	$slides = $virtue_premium[ 'home_slider' ];
} else {
	$slides = '';
}
if ( isset( $virtue_premium[ 'slider_autoplay' ] ) && 1 == $virtue_premium[ 'slider_autoplay' ] ) {
	$autoplay ='true';
} else {
	$autoplay = 'false';
}
if ( isset( $virtue_premium[ 'slider_pausetime' ] ) ) {
	$pausetime = $virtue_premium[ 'slider_pausetime' ];
} else {
	$pausetime = '7000';
}
if ( isset( $virtue_premium[ 'slider_captions' ] ) && 1 == $virtue_premium[ 'slider_captions' ] ) { 
	$captions = 'true'; 
} else {
	$captions = 'false';
}
$items = count($slides);
if( 11 < $items ) {
	$show_slides = 10;
} else {
	$show_slides = $items - 1;
}
echo '<div class="sliderclass carousel_outerrim loading">';
	echo '<div class="slick-slider kt-slickslider kt-image-carousel loading" data-slider-center-mode="true" data-slider-speed="'.esc_attr( $pausetime ).'" data-slider-anim-speed="400" data-slider-fade="false" data-slider-type="carousel" data-slider-auto="'.esc_attr( $autoplay ).'" data-slides-to-show="'.esc_attr($show_slides).'" data-slider-arrows="true">';
		foreach ($slides as $slide) {
		if ( ! empty( $slide[ 'target' ] ) && $slide[ 'target' ] == 1 ) {
		    $target = '_blank';
		} else {
		    $target = '_self';
		}
		$img = virtue_get_image_array( null, $slideheight, true, null, null, $slide['attachment_id'], false );

			echo '<div class="kt-slick-slide gallery_item">';
				if ( ! empty( $slide[ 'link' ] ) ) {
					echo '<a href="'.esc_url( $slide[ 'link' ] ).'" target="'.esc_attr( $target ).'">';
				}
					echo '<img src='.esc_url( $img[ 'src' ] ).' width="'.esc_attr( $img[ 'width' ] ).'" height="'.esc_attr( $img[ 'height' ] ).'" alt="'.esc_attr( $img[ 'alt' ] ).'" data-caption="'.esc_attr( get_post_field( 'post_excerpt', $attachment ) ).'" '.wp_kses_post( $img[ 'srcset' ] ).' />';
			            if ($captions == 'true') {
			            	echo '<div class="ic-caption">';
			              	if ( ! empty( $slide[ 'title' ] ) ) {
			                	echo '<div class="captiontitle headerfont">'.esc_html( $slide[ 'title' ] ).'</div>'; 
			              	}
			              	if ( ! empty( $slide[ 'description' ] ) ) {
			                	echo '<div><div class="captiontext headerfont"><p>'.wp_kses_post( $slide[ 'description' ] ).'</p></div></div>';
			              	}
			              	echo '</div>';
			      		}
			    if ( ! empty( $slide[ 'link' ] ) ) {
			    	echo '</a>';
			    }
			echo '</div>';
			}
	echo '</div>';
echo '</div>';
