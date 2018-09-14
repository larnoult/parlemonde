<?php 
/* Thumbnail Flex Slider */
global $virtue; 
// Get slider settings
$slideheight = ( isset( $virtue['slider_size'] ) ? $virtue['slider_size'] : 400 );
$slidewidth = ( isset( $virtue['slider_size_width'] ) ? $virtue['slider_size_width'] : 1140 );
$captions = ( isset( $virtue['slider_captions'] ) ? $virtue['slider_captions'] : '' );
$slides = ( isset( $virtue['home_slider'] ) ? $virtue['home_slider'] : '' );
$autoplay = ( isset( $virtue['slider_autoplay'] ) && 0 == $virtue['slider_autoplay'] ? 'false' : 'true' );
$pausetime = ( isset( $virtue['slider_pausetime'] ) ? $virtue['slider_pausetime'] : '7000' );
$transtype = ( isset( $virtue['trans_type'] ) ? $virtue['trans_type'] : 'slide' );
$transtime = ( isset( $virtue['slider_transtime'] ) ? $virtue['slider_transtime'] : '300' );
?>
<div class="sliderclass kad-desktop-slider">
	<div id="imageslider" class="container">
		<div id="flex" class="flexslider kt-flexslider-thumb loading" style="max-width:<?php echo esc_attr( $slidewidth );?>px; margin-left: auto; margin-right:auto;" data-flex-speed="<?php echo esc_attr( $pausetime );?>" data-flex-anim-speed="<?php echo esc_attr( $transtime );?>" data-flex-animation="<?php echo esc_attr( $transtype ); ?>" data-flex-auto="<?php echo esc_attr( $autoplay );?>">
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
				endforeach;
				?>
			</ul>
		</div> <!--Flex Slides-->
		<div id="thumbnails" class="flexslider" style="max-width:<?php echo esc_attr($slidewidth);?>px; margin-left: auto; margin-right:auto;">
			<ul class="slides">
			<?php foreach ($slides as $slide) :?>
				<li> 
					<?php echo virtue_get_full_image_output(180, 100, true, null, null, $slide['attachment_id'] ); ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</div><!--Flex thumb-->
	</div><!--Container-->
</div><!--feat-->