<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'virtue_header', 'virtue_header_markup', 10 );
function virtue_header_markup() {
	global $virtue_premium;
	if ( isset( $virtue_premium[ 'header_style' ] ) ) {
		$header_style = $virtue_premium[ 'header_style' ];
	} else {
		$header_style = 'standard';
	}
	if( 'center' == $header_style ) {
		if( isset( $virtue_premium[ 'shrink_center_header' ] ) && 1 == $virtue_premium[ 'shrink_center_header' ] ) {
			get_template_part( 'templates/header-style-two-shrink' );
		} else {
			get_template_part( 'templates/header-style-two' );
		}
	} else if ( 'shrink' == $header_style ) {
		get_template_part('templates/header-style-three');
	} else {
		get_template_part('templates/header');
	}
}

add_action( 'virtue_footer', 'virtue_footer_markup', 10 );
function virtue_footer_markup() {
	get_template_part( 'templates/footer' );
}

add_action( 'virtue_sidebar', 'virtue_sidebar_markup', 10 );
function virtue_sidebar_markup() {
	if ( virtue_display_sidebar() ) {
		get_sidebar();
	}
}

add_action( 'kt_header_after', 'kt_mobile_header', 20 );
function kt_mobile_header() {
	global $virtue_premium;
	if(isset($virtue_premium['mobile_header']) && $virtue_premium['mobile_header'] == '1') {
		get_template_part('templates/mobile', 'header'); 
	}
}

add_action( 'kt_header_after', 'kt_shortcode_after_header', 40 );
function kt_shortcode_after_header() {
	global $virtue_premium;
	if(isset($virtue_premium['sitewide_after_header_shortcode_input']) && !empty($virtue_premium['sitewide_after_header_shortcode_input']) ) {
		echo do_shortcode($virtue_premium['sitewide_after_header_shortcode_input']); 
	}
}

add_action( 'kadence_page_footer', 'virtue_page_comments', 20 );
function virtue_page_comments() {
	global $virtue_premium;
	if(isset($virtue_premium['page_comments']) && $virtue_premium['page_comments'] == '1') {
		comments_template('/templates/comments.php');
	}
}

add_action( 'kadence_page_title_container', 'virtue_page_title', 20 );
function virtue_page_title() {
	global $virtue_premium, $post;
	if ( is_home() ) {
		$homeid = get_option( 'page_for_posts' );
		$pagetitle = get_post_meta( $homeid, '_kad_show_page_title', true );
	} elseif ( is_archive() ) {
		if ( get_queried_object() ) {
			$cat_term_id = get_queried_object_id();
			$meta = get_option('kad_cat_title');
			if (empty($meta)) $meta = array();
			if (!is_array($meta)) $meta = (array) $meta;
			$meta = isset($meta[$cat_term_id]) ? $meta[$cat_term_id] : array();
			if(isset($meta['archive_show_title'])) {
				$pagetitle = $meta['archive_show_title'];
			} else {
				$pagetitle = 'default';
			}
		}
	} elseif ( is_404() ) {
		$pagetitle = 'default';
	} elseif ( is_page() ) {
		$pagetitle = get_post_meta( $post->ID, '_kad_show_page_title', true );
	}
	if(empty($pagetitle) || $pagetitle == 'default') {
		if(isset($virtue_premium['page_title_show']) && $virtue_premium['page_title_show'] == '0') {
			// Do nothing
		} else {
			get_template_part('/templates/page', 'header'); 
		}
	} elseif($pagetitle == 'show') {
		get_template_part('/templates/page', 'header'); 
	} else {
		// do nothing
	}
}
if ( ! function_exists( 'virtue_body_data' ) ) {
	function virtue_body_data() {
		global $virtue_premium;
		if ( isset( $virtue_premium[ 'smooth_scrolling' ] ) && 1 == $virtue_premium[ 'smooth_scrolling' ] ) {
			$scrolling = '1';
		} else if ( isset( $virtue_premium[ 'smooth_scrolling' ] ) && 2 == $virtue_premium[ 'smooth_scrolling' ] ) {
			$scrolling = '2';
		} else {
			$scrolling = '0';
		}
		if ( isset( $virtue_premium[ 'smooth_scrolling_hide' ] ) && 1 == $virtue_premium[ 'smooth_scrolling_hide' ] ) {
			$scrolling_hide = '1';
		} else {
			$scrolling_hide = '0';
		} 
		if ( isset( $virtue_premium[ 'virtue_animate_in' ] ) && 1 == $virtue_premium[ 'virtue_animate_in' ] ) {
			$animate = '1';
		} else {
			$animate = '0';
		}
		if ( isset( $virtue_premium[ 'sticky_header' ] ) && 1 == $virtue_premium[ 'sticky_header' ]) {
			$sticky = '1';
		} else {
			$sticky = '0';
		}
		if ( isset( $virtue_premium[ 'product_tabs_scroll' ] ) && 1 == $virtue_premium[ 'product_tabs_scroll' ] ) {
			$pscroll = '1';
		} else {
			$pscroll = '0';
		}
		if( isset( $virtue_premium[ 'select2_select' ] ) ) {
			$select2_select = $virtue_premium[ 'select2_select' ];
		} else {
			$select2_select = '1';
		}

		return 'data-smooth-scrolling="'.esc_attr( $scrolling ).'" data-smooth-scrolling-hide="'.esc_attr( $scrolling_hide ).'" data-jsselect="'.esc_attr( $select2_select ).'" data-product-tab-scroll="'.esc_attr( $pscroll ).'" data-animate="'.esc_attr( $animate ).'" data-sticky="'.esc_attr( $sticky ).'"';
	}
}
add_action( 'virtue_pagination', 'virtue_pagination_markup', 20 );
function virtue_pagination_markup() {
	global $wp_query;
	if ($wp_query->max_num_pages > 1) : 
		virtue_wp_pagenav();
	endif; 
}