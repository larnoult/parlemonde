<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add theme support
add_action( 'after_setup_theme', 'virtue_woocommerce_support' );
function virtue_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

add_action( 'init', 'virtue_woocommerce_core_hooks' );
function virtue_woocommerce_core_hooks() {
	// Only run this if woocommerce is active
	if ( class_exists('woocommerce') ) {
		// Add 3.0 gallery support.
		if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
			$virtue = virtue_get_options();
			if(isset($virtue['product_gallery_zoom']) && 1 == $virtue['product_gallery_zoom']) {
				add_theme_support( 'wc-product-gallery-zoom' );
			}
			if(isset($virtue['product_gallery_slider']) && 1 == $virtue['product_gallery_slider']) {
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
		// Don't use woo styles, the theme has styles
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
  
		// Disable WooCommerce Lightbox
		if (get_option( 'woocommerce_enable_lightbox' ) == true ) {
			update_option( 'woocommerce_enable_lightbox', false );
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
    }
}

// Add Cart Fragment for topbar cart option
add_action( 'after_setup_theme', 'virtue_woocommerce_cart_fragments_support' );
function virtue_woocommerce_cart_fragments_support() {
	// only run if woocommerce is runnig
	if ( class_exists( 'woocommerce' ) ) {
		if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
			add_filter('woocommerce_add_to_cart_fragments', 'virtue_woocommerce_header_add_to_cart_fragment');
		} else {
			add_filter('add_to_cart_fragments', 'virtue_woocommerce_header_add_to_cart_fragment');
		}
		function virtue_woocommerce_header_add_to_cart_fragment( $fragments ) {
			ob_start(); ?>
			<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'virtue' ); ?>">
				<i class="icon-shopping-cart" style="padding-right:5px;"></i>
				<?php esc_html_e('Your Cart', 'virtue');?>
				<span class="kad-cart-dash">-</span>
				<?php if ( WC()->cart->tax_display_cart == 'incl' ) {
					echo wp_kses_post( WC()->cart->get_cart_subtotal() ); 
				} else {
					echo wp_kses_post( WC()->cart->get_cart_total() );
				}
				?>
			</a>
			<?php
			$fragments['a.cart-contents'] = ob_get_clean();
			return $fragments;
		}
	}
}
