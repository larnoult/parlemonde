<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	global $virtue_premium;

	if(!empty($virtue_premium['product_best_title'])) {
		$product_best_title = $virtue_premium['product_best_title'];
	} else {
		$product_best_title = __('Best Selling Products', 'virtue');
	}
	if(!empty($virtue_premium['home_product_best_column'])) {
		$product_tcolumn = $virtue_premium['home_product_best_column'];
	} else {
		$product_tcolumn = '4';
	}
	
	if(!empty($virtue_premium['home_product_best_count'])) {
		$hp_probcount = $virtue_premium['home_product_best_count'];
	} else {
		$hp_probcount = '6';
	}
	if(!empty($virtue_premium['home_product_best_speed'])) {
		$hp_bestspeed = $virtue_premium['home_product_best_speed'].'000';
	} else {
		$hp_bestspeed = '9000';
	}
	if(isset($virtue_premium['home_product_best_scroll']) && $virtue_premium['home_product_best_scroll'] == 'all' ) {
		$hp_bestscroll = '';
	} else {
		$hp_bestscroll = '1';
	}?>
	<div class="home-product home-margin carousel_outerrim home-padding kad-animation" data-animation="fade-in" data-delay="0">
		<div class="clearfix">
			<h3 class="hometitle">
				<?php echo $product_best_title; ?>
			</h3>
		</div>
		
		<?php 
		virtue_build_post_content_carousel('best', $product_tcolumn, 'product', null, $hp_probcount, 'menu_order', 'DESC', 'products', null, 'true', $hp_bestspeed, $hp_bestscroll, 'true', '400', 'best' ); 
		?>
	</div>