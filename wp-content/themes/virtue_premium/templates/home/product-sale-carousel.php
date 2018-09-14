<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	global $virtue_premium; 
	

	if(!empty($virtue_premium['product_sale_title'])) {$product_sale_title = $virtue_premium['product_sale_title'];} else {$product_sale_title = __('Products on Sale', 'virtue');}
	if(!empty($virtue_premium['home_product_sale_column'])) {$product_tcolumn = $virtue_premium['home_product_sale_column'];} else {$product_tcolumn = '4';}
	if(!empty($virtue_premium['home_product_sale_count'])) {$hp_proscount = $virtue_premium['home_product_sale_count'];} else {$hp_proscount = '6';}
	if(!empty($virtue_premium['home_product_sale_speed'])) {$hp_salespeed = $virtue_premium['home_product_sale_speed'].'000';} else {$hp_salespeed = '9000';} 
	if(isset($virtue_premium['home_product_sale_scroll']) && $virtue_premium['home_product_sale_scroll'] == 'all' ) {$hp_salescroll = '';} else {$hp_salescroll = '1';}?>
	<div class="home-product home-margin carousel_outerrim home-padding kad-animation" data-animation="fade-in" data-delay="0">
		<div class="clearfix">
			<h3 class="hometitle">
				<?php echo $product_sale_title; ?>
			</h3>
		</div>
		<?php 
		virtue_build_post_content_carousel('sale', $product_tcolumn, 'product', null, $hp_proscount, 'menu_order', 'ASC', 'products', null, 'true', $hp_salespeed, $hp_salescroll, 'true', '400', 'sale' ); 
		?>
	</div>