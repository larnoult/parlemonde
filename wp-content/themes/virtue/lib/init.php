<?php
/**
 * Virtue initial setup and constants
 *
 * @package Virtue Theme
 */

/**
 * Virtue initial setup
 */
function virtue_setup() {

	register_nav_menus(array(
		'primary_navigation'   => __( 'Primary Navigation', 'virtue' ),
		'secondary_navigation' => __( 'Secondary Navigation', 'virtue' ),
		'mobile_navigation'    => __( 'Mobile Navigation', 'virtue' ),
		'topbar_navigation'    => __( 'Topbar Navigation', 'virtue' ),
		'footer_navigation'    => __( 'Footer Navigation', 'virtue' ),
	));

	define( 'VIRTUE_VERSION', '3.2.8' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'widget-thumb', 80, 50, true );
	add_post_type_support( 'attachment', 'page-attributes' );
	add_theme_support( 'automatic-feed-links' );
	add_editor_style( '/assets/css/editor-style.css' );

	global $virtue;
	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Primary Color', 'virtue' ),
			'slug'  => 'virtue-primary',
			'color' => ( isset( $virtue['primary_color'] ) && ! empty( $virtue['primary_color'] ) ? $virtue['primary_color'] : '#2d5c88' ),
		),
		array(
			'name'  => __( 'Lighter Primary Color', 'virtue' ),
			'slug'  => 'virtue-primary-light',
			'color' => ( isset( $virtue['primary20_color'] ) && ! empty( $virtue['primary20_color'] ) ? $virtue['primary20_color'] : '#6c8dab' ),
		),
		array(
			'name'  => __( 'Very light gray', 'virtue' ),
			'slug'  => 'very-light-gray',
			'color' => '#eee',
		),
		array(
			'name'  => esc_html__( 'White', 'virtue' ),
			'slug'  => 'white',
			'color' => '#fff',
		),
		array(
			'name'  => __( 'Very dark gray', 'virtue' ),
			'slug'  => 'very-dark-gray',
			'color' => '#444',
		),
		array(
			'name'  => esc_html__( 'Black', 'virtue' ),
			'slug'  => 'black',
			'color' => '#000',
		),
	) );
	add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'virtue_setup' );

/**
 * Outputs Favicon.
 * Keep for fallback, only show if there is no site icon.
 */
function virtue_fav_output() {
	$site_icon_id = get_option( 'site_icon' );
	if ( empty( $site_icon_id ) ) {
		global $virtue;
		if ( isset( $virtue['virtue_custom_favicon']['url'] ) && ! empty( $virtue['virtue_custom_favicon']['url'] ) ) {
			echo '<link rel="shortcut icon" type="image/x-icon" href="' . esc_url( $virtue['virtue_custom_favicon']['url'] ) . '" />';
		}
	}
}
add_action( 'wp_head', 'virtue_fav_output', 5 );
