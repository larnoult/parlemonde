<?php 
global $virtue; 

?>
<div class="sliderclass kad-mobile-slider">
	<div id="imageslider" class="container">
		<div class="videofit">
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

				echo do_shortcode( wp_kses( $virtue['mobile_video_embed'], $allowed_tags ) ); ?>
        </div>
    </div><!--Container-->
</div><!--feat-->