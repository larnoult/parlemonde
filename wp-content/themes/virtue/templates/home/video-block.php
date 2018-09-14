<div class="sliderclass kad-desktop-slider">
<div id="imageslider" class="container">
	<?php global $virtue;
		if(isset($virtue['slider_size_width'])) {
			$slidewidth = $virtue['slider_size_width'];
		} else {
			$slidewidth = 1140;
		} ?>
			<div class="videofit" style="max-width:<?php echo esc_attr($slidewidth);?>px; margin-left: auto; margin-right:auto;">
				<?php 
				$allowed_tags = wp_kses_allowed_html('post');
				$allowed_tags['iframe'] = array(
					'src'             => true,
					'height'          => true,
					'width'           => true,
					'frameborder'     => true,
					'allowfullscreen' => true,
					'name' 			  => true,
					'id' 			  => true,
					'class' 		  => true,
					'style' 		  => true,
				);

				echo do_shortcode( wp_kses( $virtue['video_embed'], $allowed_tags ) ); ?>
            </div>
	</div><!--Container-->
</div><!--feat-->