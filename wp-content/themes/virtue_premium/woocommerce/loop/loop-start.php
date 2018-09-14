<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */
global $woocommerce, $woocommerce_loop, $virtue_premium;
if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 1) {$animate = 1;} else {$animate = 0;}
if(isset($virtue_premium['product_fitrows']) && $virtue_premium['product_fitrows'] == 1) {$style = 'fitRows';} else {$style = 'masonry';}
 if ( empty( $woocommerce_loop['columns'] ) ) {
 	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
 }
 $woocommerce_loop['rand'] = $woocommerce_loop['columns'];

if ( virtue_display_sidebar() ) {
	$columns = "shopcolumn".$woocommerce_loop['columns']." shopsidebarwidth"; 
} else {
	$columns = "shopcolumn".$woocommerce_loop['columns']." shopfullwidth"; 
}
if(isset($virtue_premium['product_img_resize']) && $virtue_premium['product_img_resize'] == 0) { 
	$isoclass = 'init-isotope';
} else { 
	$isoclass = 'init-isotope-intrinsic';
}
if ( isset( $virtue_premium['infinitescroll'] ) && '1' == $virtue_premium['infinitescroll'] ) {
	wp_enqueue_script( 'virtue-infinite-scroll' );
	$infinit = 'data-nextselector=".woocommerce-pagination a.next" data-navselector=".woocommerce-pagination" data-itemselector=".kad_product" data-itemloadselector=".kad_product_fade_in" data-infiniteloader="'.get_template_directory_uri() . '/assets/img/loader.gif"';
	$scrollclass = 'init-infinit';
} else {
	$infinit = '';
	$scrollclass = '';
}
?>
<div id="product_wrapper<?php echo $woocommerce_loop['rand'];?>" class="products kad_product_wrapper rowtight <?php echo esc_attr($columns); ?> <?php echo esc_attr($isoclass); ?> <?php echo esc_attr($scrollclass); ?> reinit-isotope" data-fade-in="<?php echo esc_attr($animate);?>" <?php echo $infinit;?>  data-iso-selector=".kad_product" data-iso-style="<?php echo esc_attr($style);?>" data-iso-filter="true">