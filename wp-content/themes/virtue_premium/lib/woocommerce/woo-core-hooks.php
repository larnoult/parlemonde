<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Add theme support
add_action( 'after_setup_theme', 'virtue_woocommerce_support' );
function virtue_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
// Core hooks
add_action( 'init', 'virtue_woocommerce_core_hooks' );
function virtue_woocommerce_core_hooks() {
	// Only run this if woocommerce is active
    if ( class_exists( 'woocommerce' ) ) {
    	// Don't use woo styles, the theme has styles
        add_filter( 'woocommerce_enqueue_styles', '__return_false' );

        // Add 3.0 gallery support.
		if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
			$virtue_premium = virtue_premium_get_options();
			if( isset( $virtue_premium['product_gallery_zoom'] ) && 1 == $virtue_premium['product_gallery_zoom'] ) {
				add_theme_support( 'wc-product-gallery-zoom' );
			}
			if( isset( $virtue_premium['product_gallery_slider'] ) && 1 == $virtue_premium['product_gallery_slider'] ) {
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
		if ( isset( $virtue_premium['kadence_lightbox'] ) && $virtue_premium['kadence_lightbox'] == 1 ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}

        // Makes the product finder plugin work.
        remove_action( 'template_redirect' , array( 'WooCommerce_Product_finder' , 'load_template' ) );
        // Makes the product Voucher plugin work.
        if ( class_exists( 'WC_PDF_Product_Vouchers' ) ) {
			add_filter('template_include', 'kt_wc_voucher_override', 20);
			function kt_wc_voucher_override($template) {
				$cpt = get_post_type();
				if ($cpt == 'wc_voucher') {
					remove_filter('template_include', array('Kadence_Wrapping', 'wrap'), 101);
				}
				return $template;
			}
		}
		// Add woo messages to normal pages.
		add_action('kt_afterheader', 'virtue_wc_print_notices');
		function virtue_wc_print_notices() {
			if ( ! is_shop() and ! is_woocommerce() and ! is_cart() and ! is_checkout() and ! is_account_page() ) {
				echo '<div class="container virtue-woomessages-container">';
					echo do_shortcode( '[woocommerce_messages]' );
				echo '</div>';
			}
		}
		// Topbar Cart widget ajax refreash
		if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
			add_filter('woocommerce_add_to_cart_fragments', 'kad_woocommerce_header_add_to_cart_fragment');
		} else {
			add_filter('add_to_cart_fragments', 'kad_woocommerce_header_add_to_cart_fragment');
		}
		function kad_woocommerce_header_add_to_cart_fragment( $fragments ) {
			global $woocommerce, $virtue_premium;
			ob_start(); ?>
				<a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php _e('View your shopping cart', 'virtue'); ?>">
					<i class="icon-basket" style="padding-right:5px;"></i> 
					<?php if ( ! empty( $virtue_premium['cart_placeholder_text'] ) ) {
						echo $virtue_premium['cart_placeholder_text'];
					} else {
						echo __('Your Cart', 'virtue');
					}  ?> 
					<span class="kad-cart-dash">-</span>
					<?php if ( WC()->cart->tax_display_cart == 'incl' ) {
						echo WC()->cart->get_cart_subtotal(); 
					} else {
						echo WC()->cart->get_cart_total();
					}
			  		?>
				</a>
				<?php
			$fragments['a.cart-contents'] = ob_get_clean();
			return $fragments;
		}
    }
    // Sensei Support
	if ( class_exists('Sensei_Main' ) ) {
		add_filter('template_include', 'virtue_wc_sensei_override', 1);
		function virtue_wc_sensei_override( $template ) {
			$cpt = get_post_type();
			if ( ! is_search() && ( 'course' == $cpt || 'lesson' == $cpt ) ) {
				remove_filter('template_include', array('Kadence_Wrapping', 'wrap'), 101);
			}
			return $template;
		}
		global $woothemes_sensei;
		remove_action( 'sensei_sidebar', array( $woothemes_sensei->frontend, 'sensei_get_sidebar'), 10);
		remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
		remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );
		add_action( 'sensei_before_main_content', 'virtue_sensei_output_content_wrapper', 10 );
		add_action( 'sensei_after_main_content', 'virtue_sensei_output_content_wrapper_end', 10 );

		function virtue_sensei_output_content_wrapper() { ?>
			<div class="wrap clearfix contentclass hfeed" role="document">
			<div id="content" class="container">
			<div class="row">
			<div class="main <?php echo virtue_main_class(); ?>" role="main">
			<?php 
		}

		function virtue_sensei_output_content_wrapper_end() { ?>
			</div>
			<?php if (virtue_display_sidebar()) : ?>
				<aside id="ktsidebar" class="<?php echo esc_attr(virtue_sidebar_class()); ?> kad-sidebar" role="complementary">
					<div class="sidebar">
						<?php include kadence_sidebar_path(); ?>
					</div><!-- /.sidebar -->
				</aside><!-- /aside -->
			<?php endif; ?>
			</div><!-- /.row-->
			<?php do_action('kt_after_content'); ?>
			</div><!-- /.content -->
			</div><!-- /.wrap -->
			<?php
		}
	}
}