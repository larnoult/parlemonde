<?php
/**
 * Variable product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 3.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attribute_keys = array_keys( $attributes );
?>

<?php do_action('woocommerce_before_add_to_cart_form'); ?>


<form class="variations_form cart" method="post" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( $available_variations ) ) ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'virtue' ); ?></p>
	<?php else : ?>
	<table class="variations" cellspacing="0">
		<tbody>
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<tr>
					
					<td class="product_label label"><label for="<?php echo sanitize_title($attribute_name); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></td>
					<td class="product_value value">
					<?php
								$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
								wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected, 'class'=>'kad-select') );
								echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear selection', 'virtue' ) . '</a>' ) : '';
							?>

					</td>
				</tr>
	        <?php endforeach;?>
		</tbody>
	</table>

	<?php if ( version_compare( WC_VERSION, '3.4', '<' ) ) {
		do_action( 'woocommerce_before_add_to_cart_button' ); 
	} ?>

	<div class="single_variation_wrap_kad single_variation_wrap" style="display:block;">
		<?php do_action( 'woocommerce_before_single_variation' ); 
		/**
		* woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
		* @since 2.4.0
		* @hooked woocommerce_single_variation - 10 Empty div for variation data.
		* @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
		*/
		do_action( 'woocommerce_single_variation' );

		do_action( 'woocommerce_after_single_variation' ); ?>

	</div>

	<?php if ( version_compare( WC_VERSION, '3.4', '<' ) ) {
		do_action( 'woocommerce_after_add_to_cart_button' ); 
	} ?>

	<?php endif; ?>
	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action('woocommerce_after_add_to_cart_form'); ?>
