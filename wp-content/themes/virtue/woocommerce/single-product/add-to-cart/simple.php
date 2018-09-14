<?php
/**
 * Simple product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}
?>

<?php
	// Availability
	if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
		echo wc_get_stock_html( $product );
	} else {
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

		echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}

	if ( $product->is_in_stock() ) : ?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" enctype='multipart/form-data'>
	 	<?php 
	 		/**
			 * @since 2.1.0.
			 */
			do_action( 'woocommerce_before_add_to_cart_button' );

			/**
			 * @since 2.7.0.
			 */
			do_action( 'woocommerce_before_add_to_cart_quantity' );

 			woocommerce_quantity_input( array(
 				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
 			) );

	 		/**
			 * @since 2.7.0.
			 */
			do_action( 'woocommerce_after_add_to_cart_quantity' );
	 	?>
	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

	 	<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="kad_add_to_cart single_add_to_cart_button headerfont kad-btn kad-btn-primary button alt"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>