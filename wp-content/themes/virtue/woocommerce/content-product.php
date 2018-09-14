<?php
/**
 * The template for displaying product content within loops
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 	WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop, $virtue, $post;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

if ($woocommerce_loop['columns'] == '3'){ 
	$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
} else {
	$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();

$classes[] = 'grid_item';
$classes[] = 'product_item';
$classes[] = 'clearfix';
?>
<div class="<?php echo esc_attr( $itemsize );?> kad_product">
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
			 	* @hooked virtue_woocommerce_archive_content_wrap_start - 5
			 	* @hooked virtue_woocommerce_archive_title_wrap_start - 6
			 	* @hooked virtue_woocommerce_archive_title_link_start - 7
			 	*
			 	* @hooked woocommerce_template_loop_product_title - 10 (UNHOOKED BY THEME)
			 	* @hooked virtue_woocommerce_template_loop_product_title - 10
				*
			 	* @hooked woocommerce_template_loop_product_title - 10
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