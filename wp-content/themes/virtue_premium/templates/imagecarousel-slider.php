<?php 
/**
 * Image Carousel Template
 *
 * @version 3.2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post; 
$height = get_post_meta( $post->ID, '_kad_posthead_height', true ); 
$slideheight = ( !empty( $height ) ? $height : 400 );

$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
if ( !empty( $image_gallery ) ) {
	$attachments = array_filter( explode( ',', $image_gallery ) );
} else {
	$attach_args = array('order'=> 'ASC','post_type'=> 'attachment','post_parent'=> $post->ID,'post_mime_type' => 'image','post_status'=> null,'orderby'=> 'menu_order','numberposts'=> -1);
	$attachments_posts = get_posts($attach_args);
	$attachments = array();
	foreach ($attachments_posts as $val) {
		$attachments[] = $val->ID;
	}
}
if ( $attachments ) {
	$items = count($attachments);
	if( 11 < $items ) {
		$show_slides = 10;
	} else {
		$show_slides = $items - 1;
	}
} else {
	$show_slides = 5;
}
?>
<section class="pagefeat carousel_outerrim">
	<div id="post-carousel-gallery" class="slick-slider kad-light-wp-gallery kt-slickslider kt-image-carousel loading" data-slider-center-mode="true" data-slider-speed="7000" data-slider-anim-speed="400" data-slider-fade="false" data-slider-type="carousel" data-slider-auto="true" data-slider-arrows="true" data-slides-to-show="<?php echo esc_attr( $show_slides ); ?>">
		<?php 
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$img_args = array(
					'width' 		=> null,
					'height' 		=> $slideheight,
					'crop'			=> true,
					'class'			=> null,
					'alt'			=> null,
					'id'			=> $attachment,
					'placeholder'	=> false,
				);
				$img = virtue_get_processed_image_array( $img_args );
				$img['schema'] = true;
				$img['lazy'] = false;
				$img['extras'] = esc_attr( get_post_field('post_excerpt', $attachment ) );
				echo '<div class="kt-slick-slide gallery_item">';
					echo '<a href="'.esc_url( $img['full'] ).'" data-rel="lightbox">';
						virtue_print_image_output( $img );
					echo '</a>';
				echo '</div>';
			}
		}
		?>
	</div> <!--post gallery carousel-->
</section>