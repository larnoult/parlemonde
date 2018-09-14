<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


add_action( 'after_setup_theme', 'virtue_elementor_support' );
function virtue_elementor_support() {

	if ( Virtue_Plugin_Check::active_check( 'elementor/elementor.php' ) ) {
		add_action( 'init', 'virtue_elementor_init', 1 );
		add_filter( 'template_include', 'virtue_elementor_page_template_support', 20 );
		require_once( trailingslashit( get_template_directory() ) . 'lib/elementor/class-virtue-elementor-header-footer.php');
	}
}
function virtue_ele_init_editor() {
	add_action( 'elementor/editor/before_enqueue_scripts', 'virtue_add_widget_support_elementor' );
}
function virtue_add_widget_support_elementor() {
	wp_enqueue_style('kad-shortcode-css', get_template_directory_uri() . '/lib/kad_shortcodes/css/kad-short-pop.css'); 
	wp_enqueue_style('kad_adminstyles', get_template_directory_uri() . '/assets/css/kad_adminstyles.css', false, VIRTUE_VERSION);
	add_action( 'wp_print_footer_scripts','virtue_shortcode_content' );
	wp_enqueue_script('virtue_elementor_admin_scripts', get_template_directory_uri() . '/assets/js/virtue_elementor_admin_scripts.js', array( 'wp-color-picker', 'jquery' ) );
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
function virtue_elementor_init() {
	add_action( 'admin_action_elementor', 'virtue_ele_init_editor', 6 );
	add_action( 'admin_action_elementor', 'virtue_woo_archive_hooks_re_remove', 9 );
}
function virtue_woo_archive_hooks_re_remove() {
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );    
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
    remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );

    remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
	remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
    remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
    remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
}