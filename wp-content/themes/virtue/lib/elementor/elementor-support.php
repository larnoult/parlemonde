<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action('after_setup_theme', 'virtue_elementor_support');
function virtue_elementor_support() {
	if ( Virtue_Plugin_Check::active_check( 'elementor/elementor.php' ) ){
		add_action( 'init', 'virtue_elementor_woo_editing_issue', 1 );
		add_filter( 'template_include', 'virtue_elementor_page_template_support', 20 );
		require_once( trailingslashit( get_template_directory() ) . 'lib/elementor/class-virtue-elementor-header-footer.php');
	}
}
function virtue_elementor_page_template_support( $template ) {
	if( is_singular() ) {
		$t_slug = get_page_template_slug();
		if ( $t_slug == 'elementor_header_footer' || $t_slug == 'elementor_canvas' ) {
			remove_filter( 'template_include', array( 'Kadence_Wrapping', 'wrap' ), 101 );
		}
	}
	return $template;
}
function virtue_elementor_woo_editing_issue() {
	add_action( 'admin_action_elementor', 'virtue_woo_archive_hooks_re_remove', 9 );
}
function virtue_woo_archive_hooks_re_remove() {
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );    
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
    remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
}