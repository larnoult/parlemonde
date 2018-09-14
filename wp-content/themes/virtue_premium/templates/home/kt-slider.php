<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	global $virtue_premium;

?>
<div class="sliderclass clearfix ktslider_home_hidetop">
<?php echo do_shortcode( '[kadence_slider id="'.$virtue_premium['kt_slider'].'"]' ); 

	if(isset($virtue_premium['above_header_slider_arrow']) && $virtue_premium['above_header_slider_arrow'] == 1) {
        	echo '<div class="kad_fullslider_arrow"><a href="#home_slider_bottom"><i class="icon-arrow-down"></i></a></div>';
    }
	?>
</div><!--sliderclass-->
<div id="home_slider_bottom"></div>