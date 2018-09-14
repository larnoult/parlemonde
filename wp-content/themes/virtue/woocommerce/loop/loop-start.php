<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

	if ( version_compare( WC_VERSION, '3.3', '>' ) ) {
		$product_columns =  wc_get_loop_prop( 'columns' );
	} else {
		global $woocommerce_loop;
		if ( empty( $woocommerce_loop['columns'] ) ) {
		 	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
		}
		$product_columns = $woocommerce_loop['columns'];
	}
	if ( virtue_display_sidebar() ) {
		$columns = "shopcolumn".$product_columns." shopsidebarwidth"; 
	} else {
		$columns = "shopcolumn".$product_columns." shopfullwidth"; 
	}
	?>
<div id="product_wrapper" class="products kt-masonry-init rowtight <?php echo esc_attr($columns); ?>" data-masonry-selector=".kad_product">