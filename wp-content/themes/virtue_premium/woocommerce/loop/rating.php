<?php
/**
 * Loop Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( 'no' == get_option( 'woocommerce_enable_review_rating' ) ) {
	return;
}
global $virtue_premium;
if ( '1' == $virtue_premium['shop_rating'] ) {
	if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
		$rating_html = wc_get_rating_html( $product->get_average_rating() );
	} else {
		$rating_html = $product->get_rating_html();
	}
	if ( $rating_html ) {
		?>
		<a href="<?php the_permalink(); ?>"><?php echo $rating_html; ?></a>
		<?php 
	} else {
		echo "<span class='notrated'>" . __( 'not rated', 'virtue' ) . "</span>";
	}
}
