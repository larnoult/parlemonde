<?php
/**
 * External product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

defined( 'ABSPATH' ) || exit;

if ( version_compare( WC_VERSION, '3.4', '<' ) ) {

	do_action('woocommerce_before_add_to_cart_button');
?>
<p class="cart">
	<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="kad_add_to_cart single_add_to_cart_button headerfont kad-btn kad-btn-primary button alt"><?php echo $button_text; ?></a>
</p>

<?php  do_action('woocommerce_after_add_to_cart_button'); 

} else {
	
do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" action="<?php echo esc_url( $product_url ); ?>" method="get">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?> 

	<button type="submit" name="add-to-cart" class="kad_add_to_cart single_add_to_cart_button headerfont kad-btn kad-btn-primary button alt"><?php echo esc_html( $button_text ); ?></button>

	<?php wc_query_string_form_fields( $product_url ); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); 

}?>
