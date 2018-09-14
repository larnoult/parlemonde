<?php
/**
 * The Template for displaying products in a product category. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/woocommerce/taxonomy-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$cat_term_id = get_queried_object()->term_id;
$meta = get_option('product_cat_slider');
if (empty($meta)) $meta = array();
if (!is_array($meta)) $meta = (array) $meta;
$meta = isset($meta[$cat_term_id]) ? $meta[$cat_term_id] : array();

if(isset($meta['cat_short_slider'])) { echo '<div class="sliderclass kad_cat_slider">'. do_shortcode($meta['cat_short_slider']). '</div>';  }
wc_get_template( 'archive-product.php' );