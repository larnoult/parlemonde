<?php
/**
 * Enqueue scripts and stylesheets
 *
 * @package Virtue Theme
 */

/**
 * Enqueue scripts and stylesheets
 */
function virtue_scripts() {
	global $virtue;
	wp_enqueue_style( 'virtue_theme', get_template_directory_uri() . '/assets/css/virtue.css', false, VIRTUE_VERSION );
	if ( isset( $virtue['skin_stylesheet'] ) || ! empty( $virtue['skin_stylesheet'] ) ) {
		$skin = $virtue['skin_stylesheet'];
	} else {
		$skin = 'default.css';
	}
	wp_enqueue_style( 'virtue_skin', get_template_directory_uri() . '/assets/css/skins/' . $skin, false, VIRTUE_VERSION );
	if ( is_rtl() ) {
		wp_enqueue_style( 'kadence_rtl', get_template_directory_uri() . '/assets/css/rtl.css', false, VIRTUE_VERSION );
	}

	if ( is_child_theme() ) {
		$child_theme   = wp_get_theme();
		$child_version = $child_theme->get( 'Version' );
		wp_enqueue_style( 'virtue_child', get_stylesheet_uri(), false, $child_version );
	}

	if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/js/min/bootstrap-min.js', array( 'jquery' ), VIRTUE_VERSION, true );
	wp_enqueue_script( 'virtue_plugins', get_template_directory_uri() . '/assets/js/min/plugins-min.js', array( 'jquery', 'hoverIntent', 'masonry' ), VIRTUE_VERSION, true );
	wp_enqueue_script( 'virtue_main', get_template_directory_uri() . '/assets/js/min/main-min.js', array( 'jquery', 'hoverIntent', 'masonry' ), VIRTUE_VERSION, true );

	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_script( 'kt-wc-add-to-cart-variation', get_template_directory_uri() . '/assets/js/min/kt-add-to-cart-variation-min.js', array( 'jquery' ), false, VIRTUE_VERSION, true );

		if ( isset( $virtue['product_quantity_input'] ) && 1 == $virtue['product_quantity_input'] ) {
			wp_enqueue_script( 'wcqi-js', get_template_directory_uri() . '/assets/js/min/wc-quantity-increment-min.js', array( 'jquery' ), false, VIRTUE_VERSION, true );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'virtue_scripts', 100 );

/**
 * Add Respond.js for IE8 support of media queries
 */
function virtue_ie_support_scripts() {
	wp_enqueue_script( 'virtue-respond', get_template_directory_uri() . '/assets/js/vendor/respond.min.js' );
	wp_script_add_data( 'virtue-respond', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'virtue_ie_support_scripts' );
