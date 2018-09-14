<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	global $virtue_premium; 

?>
<div class="sliderclass home_sliderclass home-mobile-slider revslider_home_hidetop">
<?php  
	 echo do_shortcode( '[kadence_slider_pro id="'.$virtue_premium['mobile_ksp'].'"]' ); ?>
</div><!--sliderclass-->