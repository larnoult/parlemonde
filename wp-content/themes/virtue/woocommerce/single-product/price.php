<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
  * @version 	3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>
<div class="pricebox">
	<p class="product_price price headerfont"><?php echo $product->get_price_html(); ?></p>
</div>