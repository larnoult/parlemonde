<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	global $virtue_premium; 

	if(isset($virtue_premium['slider_size_width'])) {$slidewidth = $virtue_premium['slider_size_width'];} else { $slidewidth = 1140; }
?>
<div class="sliderclass">
	<div id="imageslider" class="container">
			<div class="videofit" style="max-width:<?php echo esc_attr($slidewidth);?>px; margin-left: auto; margin-right:auto;">
                <?php if(!empty($virtue_premium['video_embed'])) echo $virtue_premium['video_embed'];?>
            </div>
	</div><!--Container-->
</div><!--feat-->