<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $woocommerce_loop, $virtue_premium;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

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

		$rpc = apply_filters('kt_related_products_columns', $rpc);

if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
	$related = wc_get_related_products($product->get_id(), $posts_per_page);
} else {
	$related = $product->get_related($posts_per_page);
}

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters('woocommerce_related_products_args', array(
	'post_type'				=> 'product',
	'ignore_sticky_posts'	=> 1,
	'no_found_rows' 		=> 1,
	'posts_per_page' 		=> $posts_per_page,
	'orderby' 				=> $orderby,
	'post__in' 				=> $related,
	'post__not_in'			=> array($product->get_id())
) );

if(!empty($virtue_premium['related_products_text'])) {
	$relatedtext = $virtue_premium['related_products_text'];
} else {
	$relatedtext = __( 'Related Products', 'virtue');
}

$products = new WP_Query( $args );


if ( $products->have_posts() ) : ?>

	<section class="related products carousel_outerrim">
		<h3><?php echo $relatedtext; ?></h3>
	<div class="fredcarousel">
		<div id="carouselcontainer" class="rowtight">
			<div id="related-product-carousel" class="products slick-slider product_related_carousel kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="400" data-slider-scroll="1" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($rpc['xxl']);?>" data-slider-xl="<?php echo esc_attr($rpc['xl']);?>" data-slider-md="<?php echo esc_attr($rpc['md']);?>" data-slider-sm="<?php echo esc_attr($rpc['sm']);?>" data-slider-xs="<?php echo esc_attr($rpc['xs']);?>" data-slider-ss="<?php echo esc_attr($rpc['ss']);?>">
			

			<?php while ( $products->have_posts() ) : $products->the_post();

				 wc_get_template_part( 'content', 'product' ); 

			endwhile; ?>

				</div>
			</div>
		</div>
	</section>
<?php endif;

wp_reset_postdata();
