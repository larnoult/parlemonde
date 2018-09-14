<?php
/**
 * Virtue init
 *
 * @package Virtue Theme
 */

/**
 * Virtue initial setup and constants
 */
function virtue_setup() {
	global $pagenow, $virtue_premium;
	register_nav_menus(array(
		'primary_navigation'   => __( 'Primary Navigation', 'virtue' ),
		'secondary_navigation' => __( 'Secondary Navigation', 'virtue' ),
		'mobile_navigation'    => __( 'Mobile Navigation', 'virtue' ),
		'topbar_navigation'    => __( 'Topbar Navigation', 'virtue' ),
		'footer_navigation'    => __( 'Footer Navigation', 'virtue' ),
	));
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'widget-thumb', 80, 50, true );
	add_post_type_support( 'attachment', 'page-attributes' );
	add_theme_support( 'automatic-feed-links' );
	add_editor_style( '/assets/css/editor-style-virtue.css' );

	if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
		wp_redirect( admin_url( 'themes.php?page=kt_api_manager_dashboard' ) );
		exit();
	}
	define( 'VIRTUE_VERSION', '4.7.6' );

	// This needs to be moved.
	add_filter( 'eventorganiser_theme_compatability_templates', 'virtue_event_organizer_archive_template_issue' );

	// Gutenberg Support.
	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Primary Color', 'virtue' ),
			'slug'  => 'virtue-primary',
			'color' => ( isset( $virtue_premium['primary_color'] ) && ! empty( $virtue_premium['primary_color'] ) ? $virtue_premium['primary_color'] : '#2d5c88' ),
		),
		array(
			'name'  => __( 'Lighter Primary Color', 'virtue' ),
			'slug'  => 'virtue-primary-light',
			'color' => ( isset( $virtue_premium['primary20_color'] ) && ! empty( $virtue_premium['primary20_color'] ) ? $virtue_premium['primary20_color'] : '#6c8dab' ),
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
 * Virtue event organizer filter
 *
 * @param string $template archive template.
 */
function virtue_event_organizer_archive_template_issue( $template ) {
	if ( is_archive() ) {
		return get_template_part( 'archive', 'events' );
	}
	return $template;
}

if ( ! function_exists( '_wp_render_title_tag' ) ) :
	/**
	 * Virtue render Meta title.
	 */
	function virtue_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'virtue_render_title' );
endif;


/**
 * Virtue SEO
 */
function virtue_seo_switch() {
	global $virtue_premium;
	if ( isset( $virtue_premium['seo_switch'] ) ) {
		if ( '1' == $virtue_premium['seo_switch'] ) {
			$useseo = true;
		} else {
			$useseo = false;
		}
	} else {
		$useseo = true;
	}
	return $useseo;
}

/**
 * Virtue SEO Title
 *
 * @param string $title the meta title.
 */
function virtue_wp_title( $title ) {
	if ( virtue_seo_switch() ) {
		global $virtue_premium, $post;
		if ( get_post_meta( get_the_ID(), '_kad_seo_title', true ) ) {
			$new_title = get_post_meta( get_the_ID(), '_kad_seo_title', true );
		}
		if ( ! empty( $new_title ) ) {
			$title = $new_title;
		} elseif ( ! empty( $virtue_premium['seo_sitetitle'] ) ) {
			$title = $virtue_premium['seo_sitetitle'];
		}
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'virtue_wp_title', 10 );

/**
 * Virtue Fallback Fav Icon
 */
function virtue_fav_output_seo() {
	// Keep for fallback.
	global $virtue_premium, $post;
	if ( virtue_seo_switch() ) {
		if ( get_post_meta( get_the_ID(), '_kad_seo_description', true ) ) {
			echo '<meta name="description" content="' . esc_attr( get_post_meta( get_the_ID(), '_kad_seo_description', true ) ) . '">';
		} elseif ( ! empty( $virtue_premium['seo_sitedescription'] ) ) {
			echo '<meta name="description" content="' . esc_attr( $virtue_premium['seo_sitedescription'] ) . '">';
		}
	}
	$site_icon_id = get_option( 'site_icon' );
	if ( empty( $site_icon_id ) ) {
		if ( isset( $virtue_premium['virtue_custom_favicon']['url'] ) && ! empty( $virtue_premium['virtue_custom_favicon']['url'] ) ) {
			echo '<link rel="shortcut icon" type="image/x-icon" href="' . esc_url( $virtue_premium['virtue_custom_favicon']['url'] ) . '" />';
		}
	}
}
add_action( 'wp_head', 'virtue_fav_output_seo', 5 );
