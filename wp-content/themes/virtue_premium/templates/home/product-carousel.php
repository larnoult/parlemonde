<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

	global $virtue_premium, $woocommerce_loop;


		$product_title = $virtue_premium['product_title'];
		if(!empty($product_title)) {
				$ptitle = $product_title;
		} else {
			$ptitle = __('Featured Products', 'virtue');
		}
		if(!empty($virtue_premium['home_product_feat_column'])) {
			$product_tcolumn = $virtue_premium['home_product_feat_column'];
		} else {
			$product_tcolumn = '4';
		}
		if(!empty($virtue_premium['home_product_count'])) {
			$hp_procount = $virtue_premium['home_product_count'];
		} else {
			$hp_procount = '6';
		}
		if(!empty($virtue_premium['home_product_feat_speed'])) {
			$hp_featspeed = $virtue_premium['home_product_feat_speed'].'000';
		} else {
			$hp_featspeed = '9000';
		} 
		if(isset($virtue_premium['home_product_feat_scroll']) && $virtue_premium['home_product_feat_scroll'] == 'all' ) {
			$hp_featscroll = '';
		} else {
			$hp_featscroll = '1';
		}?>
	<div class="home-product home-margin carousel_outerrim home-padding kad-animation" data-animation="fade-in" data-delay="0">
		<div class="clearfix">
			<h3 class="hometitle">
				<?php echo $ptitle; ?>
			</h3>
		</div>
		<?php 
		virtue_build_post_content_carousel('featured', $product_tcolumn, 'product', null, $hp_procount, 'menu_order', 'ASC', 'products', null, 'true', $hp_featspeed, $hp_featscroll, 'true', '400', 'featured' ); 
		?>
	</div>