<?php
/**
 * Page Hooks
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Page comments
 */
function virtue_page_comments() {
	global $virtue;
	if ( isset( $virtue['page_comments'] ) && 1 == $virtue['page_comments'] ) {
		comments_template( '/templates/comments.php' );
	}
}
add_action( 'virtue_page_footer', 'virtue_page_comments', 20 );

/**
 * Page Title
 */
function virtue_page_title() {
	global $virtue, $post;
	if ( is_home() ) {
		$homeid = get_option( 'page_for_posts' );
		$pagetitle = get_post_meta( $homeid, '_kad_show_page_title', true );
	} elseif ( is_404() ) {
		$pagetitle = 'default';
	} elseif ( is_page() ) {
		$pagetitle = get_post_meta( $post->ID, '_kad_show_page_title', true );
	}
	if ( empty( $pagetitle ) || 'default' === $pagetitle ) {
		if ( ! isset( $virtue_premium['page_title_show'] ) || 1 == $virtue_premium['page_title_show'] ) {
			get_template_part( '/templates/page', 'header' );
		}
	} elseif ( 'show' === $pagetitle ) {
		get_template_part( '/templates/page', 'header' );
	}
}
add_action( 'virtue_page_title_container', 'virtue_page_title', 20 );

/**
 * Page pagination
 */
function virtue_pagination() {

	$args['mid_size']  = 3;
	$args['end_size']  = 1;
	$args['prev_text'] = '«';
	$args['next_text'] = '»';

	echo '<div class="wp-pagenavi">';
		the_posts_pagination( $args );
	echo '</div>';
}
add_action( 'virtue_pagination', 'virtue_pagination', 10 );

/**
 * Virtue Header
 */
function virtue_header_markup() {
	get_template_part( 'templates/header' );
}
add_action( 'virtue_header', 'virtue_header_markup', 10 );

/**
 * Virtue Footer
 */
function virtue_footer_markup() {
	get_template_part( 'templates/footer' );
}
add_action( 'virtue_footer', 'virtue_footer_markup', 10 );

/**
 * Virtue Sidebar
 */
function virtue_sidebar_markup() {
	if ( virtue_display_sidebar() ) {
		get_sidebar();
	}
}
add_action( 'virtue_sidebar', 'virtue_sidebar_markup', 10 );
