<?php
/**
 * Product loop sale flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $virtue_premium;
	if(isset($virtue_premium['outofstocktag']) && $virtue_premium['outofstocktag'] == 1) {

		if (! $product->is_in_stock() ) : 
	 		if(!empty($virtue_premium['sold_placeholder_text'])) {
	 			$sold_text = $virtue_premium['sold_placeholder_text'];
	 		} else {
	 			$sold_text = __( 'Sold', 'virtue');
	 		} 
	    	echo apply_filters('kt_woocommerce_soldout_flash', '<span class="onsale headerfont kad-out-of-stock">' . $sold_text . '</span>', $post, $product);

	    elseif ($product->is_on_sale()) : 
	        if(!empty($virtue_premium['sale_placeholder_text'])) {
	        	$sale_text = $virtue_premium['sale_placeholder_text'];
	        } else {
	        	$sale_text = __( 'Sale!', 'virtue');
	        } 
	      	echo apply_filters('woocommerce_sale_flash', '<span class="onsale bg_primary headerfont">'.$sale_text.'</span>', $post, $product); 
	    endif; 

	} elseif ($product->is_on_sale()) { 
  		if(!empty($virtue_premium['sale_placeholder_text'])) {
  			$sale_text = $virtue_premium['sale_placeholder_text'];
  		} else {
  			$sale_text = __( 'Sale!', 'virtue');
  		}
  		echo apply_filters('woocommerce_sale_flash', '<span class="onsale bg_primary headerfont">'.$sale_text.'</span>', $post, $product); 
  	} ?>