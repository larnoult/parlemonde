<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	global $virtue_premium; 

?>
<div class="sliderclass shop-sliderclass revslider_home_hidetop">
<?php  
	echo do_shortcode( '[kadence_slider_pro id="'.$virtue_premium['shop_ksp'].'"]' ); ?>
</div><!--sliderclass-->