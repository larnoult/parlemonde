<?php global $virtue;
$slideheight = ( isset( $virtue['slider_size'] ) ? $virtue['slider_size'] : 400 );
$slidewidth = ( isset( $virtue['slider_size_width'] ) ? $virtue['slider_size_width'] : 1140 );
$captions = ( isset( $virtue['slider_captions'] ) ? $virtue['slider_captions'] : '' );
$slides = ( isset( $virtue['home_slider'] ) ? $virtue['home_slider'] : '' );
$autoplay = ( isset( $virtue['slider_autoplay'] ) && 0 == $virtue['slider_autoplay'] ? 'false' : 'true' );
$pausetime = ( isset( $virtue['slider_pausetime'] ) ? $virtue['slider_pausetime'] : '7000' );
?>
<div class="sliderclass carousel_outerrim kad-desktop-slider">
	<div id="imageslider">
		<div class="carousel_slider_outer fredcarousel" style=" overflow:hidden; max-width:<?php echo esc_attr( $slidewidth );?>px; height: <?php echo esc_attr( $slideheight );?>px; margin-left: auto; margin-right:auto;">
			<div class="carousel_slider slick-slider kt-slickslider kt-content-carousel kt-slider-different-image-ratio loading clearfix" data-slider-fade="false" data-slider-type="slider" data-slider-anim-speed="600" data-slider-scroll="1" data-slider-auto="<?php echo esc_attr( $autoplay ); ?>" data-slider-speed="<?php echo esc_attr( $pausetime ); ?>">
				<?php
				foreach ($slides as $slide) : 
					$target = ( ! empty( $slide['target'] ) && 1 == $slide['target'] ? '_blank' : '_self');
					$img = virtue_get_image_array(null, $slideheight, false, null, null, $slide['attachment_id'], false);
					echo '<div class="carousel_gallery_item" style="display: table; position: relative; text-align: center; margin: 0; width:100%; height:'.esc_attr($img['height']).'px;">';
						echo '<div class="carousel_gallery_item_inner" style="vertical-align: middle; display: table-cell;">';
						if ( ! empty( $slide['link'] ) )	{
							echo '<a href="'.esc_url($slide['link']).'" target="'.esc_attr($target).'">';
						}
							echo '<div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">';
							echo '<img src="'.esc_url( $img['src'] ).'" width="'.esc_attr( $img['width'] ).'" height="'.esc_attr( $img['height'] ).'" alt="'.esc_attr( $img['alt'] ).'" itemprop="contentUrl" '.wp_kses_post( $img['srcset'] ).'/>';
							echo '<meta itemprop="url" content="'.esc_url($img['src']).'">';
							echo '<meta itemprop="width" content="'.esc_attr($img['width']).'px">';
							echo '<meta itemprop="height" content="'.esc_attr($img['height']).'px">';
							echo '</div>';
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
						echo '</div>';
					echo '</div>';
				endforeach;
				?>
			</div>
		</div> <!--fredcarousel-->
	</div><!--Container-->
</div><!--sliderclass-->