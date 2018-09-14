<?php
global $virtue;
$slideheight = ( isset( $virtue['mobile_slider_size'] ) ? $virtue['mobile_slider_size'] : 400 );
$slidewidth = ( isset( $virtue['mobile_slider_size_width'] ) ? $virtue['mobile_slider_size_width'] : 1140 );
$captions = ( isset( $virtue['mobile_slider_captions'] ) ? $virtue['mobile_slider_captions'] : '' );
$slides = ( isset( $virtue['home_mobile_slider'] ) ? $virtue['home_mobile_slider'] : '' );
$autoplay = ( isset( $virtue['mobile_slider_autoplay'] ) && 0 == $virtue['mobile_slider_autoplay'] ? 'false' : 'true' );
$pausetime = ( isset( $virtue['mobile_slider_pausetime'] ) ? $virtue['mobile_slider_pausetime'] : '7000' );
$transtype = ( isset( $virtue['mobile_trans_type'] ) ? $virtue['mobile_trans_type'] : 'slide' );
$transtime = ( isset( $virtue['mobile_slider_transtime'] ) ? $virtue['mobile_slider_transtime'] : '300' );
?>
<div class="sliderclass kad-mobile-slider">
	<div id="imageslider" class="container">
		<div id="mflex" class="flexslider kt-flexslider loading" style="max-width:<?php echo esc_attr( $slidewidth );?>px; margin-left: auto; margin-right:auto;" data-flex-speed="<?php echo esc_attr( $pausetime );?>" data-flex-anim-speed="<?php echo esc_attr( $transtime );?>" data-flex-animation="<?php echo esc_attr( $transtype ); ?>" data-flex-auto="<?php echo esc_attr( $autoplay );?>">
			<ul class="slides">
				<?php 
				foreach ($slides as $slide) : 
					$target = ( ! empty( $slide['target'] ) && 1 == $slide['target'] ? '_blank' : '_self');
					echo '<li>'; 
						if ( ! empty( $slide['link'] ) ){
							echo '<a href="'.esc_url( $slide['link'] ).'" target="'.esc_attr( $target ).'">';
						}
						echo virtue_get_full_image_output($slidewidth, $slideheight, true, null, null, $slide['attachment_id'] );
						if ($captions == '1') {
							echo '<div class="flex-caption">';
								if ( ! empty( $slide['title']) ) {
									echo '<div class="captiontitle headerfont">'.esc_html( $slide['title'] ).'</div>'; 
								}
								if ( ! empty( $slide['description'] ) ) {
									echo '<div><div class="captiontext headerfont"><p>'.wp_kses_post( $slide['description'] ).'</p></div></div>';
								}
							echo '</div>'; 
						} 
						
						if( ! empty( $slide['link'] ) ) {
							echo '</a>';
						} 
					echo '</li>';
                endforeach; ?>
			</ul>
		</div> <!--Flex Slides-->
	</div><!--Container-->
</div><!--feat-->