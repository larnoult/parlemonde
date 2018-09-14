<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $woocommerce_loop, $virtue_premium;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ){
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$product_column = $woocommerce_loop['columns'];
if ($product_column == '1') {
	$itemsize = 'tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12';
} else if ($product_column == '2') {
	$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
} else if ($product_column == '3'){ 
	$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
} else if ($product_column == '6'){ 
	$itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
} else if ($product_column == '5'){ 
	$itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
} else { 
	$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
}


// Extra post classes
$classes = array();
if( isset( $virtue_premium['shop_hide_action'] ) && 1 == $virtue_premium['shop_hide_action'] ) {
	$classes[] = 'hidetheaction';
}
$classes[] = 'grid_item';
$classes[] = 'product_item';
$classes[] = 'clearfix';
$classes[] = 'kad_product_fade_in';
$classes[] = 'kt_item_fade_in';

?>
<div class="<?php echo esc_attr( $itemsize );?> <?php echo esc_attr( virtue_get_product_iso_terms_class( $post->ID, 'product_cat' ) ); ?> kad_product">
	<div <?php post_class( $classes ); ?>>

		<?php
		/**
		 * woocommerce_before_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10 (UNHOOKED BY THEME)
		 */
		do_action( 'woocommerce_before_shop_loop_item' ); 

			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked kad_woocommerce_image_link_open - 2
			 * @hooked woocommerce_show_product_loop_sale_flash - 10 
			 * @hooked woocommerce_template_loop_product_thumbnail - 10 (UNHOOKED BY THEME)
			 * @hooked kt_woocommerce_template_loop_product_thumbnail - 10
			 * @hooked kad_woocommerce_image_link_close - 50
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' ); ?> 

					<?php 
					/**
					* woocommerce_shop_loop_item_title hook
					*
					* @hooked virtue_woocommerce_archive_content_wrap_start - 5
					* @hooked virtue_woocommerce_archive_title_wrap_start - 6
					* @hooked virtue_woocommerce_archive_title_link_start - 7
				 	*
				 	* @hooked woocommerce_template_loop_product_title - 10 (UNHOOKED BY THEME)
				 	* @hooked virtue_woocommerce_template_loop_product_title - 10
					*
				 	* @hooked virtue_woocommerce_archive_title_link_end - 15
					* @hooked virtue_woocommerce_archive_excerpt - 20
					* @hooked virtue_woocommerce_archive_title_wrap_end - 50
				 	*/
					do_action( 'woocommerce_shop_loop_item_title' );
					?>
			
			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>


		<?php 
		/**
	 	* woocommerce_after_shop_loop_item hook.
	 	*
		* @hooked woocommerce_template_loop_product_link_close - 5 (UNHOOKED BY THEME)
		* @hooked woocommerce_template_loop_add_to_cart - 10
	 	* @hooked virtue_after_shop_loop_wrap_end - 50
	 	*/
	 	do_action( 'woocommerce_after_shop_loop_item' ); ?>

	</div>
</div>