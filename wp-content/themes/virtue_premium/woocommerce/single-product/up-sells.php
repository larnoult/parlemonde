<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce, $woocommerce_loop, $virtue_premium;
if(!empty($virtue_premium['related_item_column'])) {
	$product_related_column = $virtue_premium['related_item_column'];
} else {
	$product_related_column = '4';
}
$woocommerce_loop['columns'] = $product_related_column;

    $rpc = array();
	if ($product_related_column == '2') {
		$rpc['xxl'] = 2; 
		$rpc['xl'] = 2; 
		$rpc['md'] = 2; 
		$rpc['sm'] = 2; 
		$rpc['xs'] = 1;
		$rpc['ss'] = 1; 
	} else if ($product_related_column == '3'){
		$rpc['xxl'] = 3; 
		$rpc['xl'] = 3; 
		$rpc['md'] = 3; 
		$rpc['sm'] = 3; 
		$rpc['xs'] = 2;
		$rpc['ss'] = 1; 
	} else if ($product_related_column == '6'){
		$rpc['xxl'] = 6; 
		$rpc['xl'] = 6; 
		$rpc['md'] = 6; 
		$rpc['sm'] = 4; 
		$rpc['xs'] = 3;
		$rpc['ss'] = 2; 
	} else if ($product_related_column == '5'){
		$rpc['xxl'] = 5; 
		$rpc['xl'] = 5; 
		$rpc['md'] = 5; 
		$rpc['sm'] = 4; 
		$rpc['xs'] = 3;
		$rpc['ss'] = 2; 
	} else {
		$rpc['xxl'] = 4; 
		$rpc['xl'] = 4; 
		$rpc['md'] = 4; 
		$rpc['sm'] = 3; 
		$rpc['xs'] = 2;
		$rpc['ss'] = 1; 
	} 

	$rpc = apply_filters('kt_upsell_products_columns', $rpc);

if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
	$upsells = $product->get_upsell_ids();
} else {
	$upsells = $product->get_upsells();
}


if ( sizeof( $upsells ) === 0 ) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => 8,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->get_id() ),
	'meta_query'          => $meta_query
);
if ( ! empty($virtue_premium['wc_upsell_products_text'] ) ) {
	$upsell_text = $virtue_premium['wc_upsell_products_text'];
} else {
	$upsell_text = __( 'You may also like&hellip;', 'virtue');
}

$products = new WP_Query( $args );

if ( $products->have_posts() ) : ?>

	<div class="upsells products carousel_outerrim">
		<h3><?php echo $upsell_text; ?></h3>
	<div class="fredcarousel">
		<div id="carouselcontainer-upsell" class="rowtight">
			<div id="upsale-product-carousel" class="products slick-slider product_upsell_carousel kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="400" data-slider-scroll="1" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($rpc['xxl']);?>" data-slider-xl="<?php echo esc_attr($rpc['xl']);?>" data-slider-md="<?php echo esc_attr($rpc['md']);?>" data-slider-sm="<?php echo esc_attr($rpc['sm']);?>" data-slider-xs="<?php echo esc_attr($rpc['xs']);?>" data-slider-ss="<?php echo esc_attr($rpc['ss']);?>">

					<?php while ( $products->have_posts() ) : $products->the_post(); 

						wc_get_template_part( 'content', 'product' ); 

					endwhile;?>

				</div>
			</div>
		</div>
	</div>

<?php endif;

wp_reset_postdata();
