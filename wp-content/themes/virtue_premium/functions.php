<?php
/**
 * Get all required functions files
 *
 * @package Virtue Theme
 */

define( 'OPTIONS_SLUG', 'virtue_premium' );
define( 'LANGUAGE_SLUG', 'virtue' );
load_theme_textdomain( 'virtue', get_template_directory() . '/languages' );

/*
 * Init Theme Options
 */
require_once trailingslashit( get_template_directory() ) . 'themeoptions/framework.php'; // Options framework
require_once trailingslashit( get_template_directory() ) . 'themeoptions/options.php'; // Options framework
require_once trailingslashit( get_template_directory() ) . 'themeoptions/options/virtue_extension.php'; // Options framework extension.
require_once trailingslashit( get_template_directory() ) . 'kt-framework/extensions.php'; // Remove options from the admin.

/*
 * Init Theme Startup/Core utilities
 */
require_once trailingslashit( get_template_directory() ) . 'lib/utils.php'; // Utility functions.
require_once trailingslashit( get_template_directory() ) . 'lib/init.php'; // Initial theme setup and constants.
require_once trailingslashit( get_template_directory() ) . 'lib/sidebar.php'; // Sidebar class.
require_once trailingslashit( get_template_directory() ) . 'lib/config.php'; // Configuration.
require_once trailingslashit( get_template_directory() ) . 'lib/cleanup.php';        									// Cleanup
require_once trailingslashit( get_template_directory() ) . 'lib/custom-nav.php';        								// Nav Options
require_once trailingslashit( get_template_directory() ) . 'lib/nav.php';            									// Custom nav modifications
require_once trailingslashit( get_template_directory() ) . 'lib/cmb2/init.php';     									// Custom metaboxes
require_once trailingslashit( get_template_directory() ) . 'lib/metaboxes/virtue-cmb-extensions.php';     			// Custom metaboxes
require_once trailingslashit( get_template_directory() ) . 'lib/aq_resizer.php';      								// Resize on the fly
require_once trailingslashit( get_template_directory() ) . 'lib/elementor/elementor-support.php';      				// elementor support
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-kadence-image-processing.php';    				// Image processing
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-virtue-get-image.php';      					// Resize images
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-virtue-lazy-load.php';      					// Lazy Load for images
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-virtue-plugin-check.php';      					// Lazy Load for images
require_once trailingslashit( get_template_directory() ) . 'lib/image_functions.php';      							// Image Functions
require_once trailingslashit( get_template_directory() ) . 'lib/taxonomy-meta-class.php';   							// Taxonomy meta boxes
require_once trailingslashit( get_template_directory() ) . 'lib/taxonomy-meta.php';         							// Taxonomy meta boxes
require_once trailingslashit( get_template_directory() ) . 'lib/comments.php'; // Custom comments modifications.
require_once trailingslashit( get_template_directory() ) . 'lib/post-types.php'; // Post Types.
require_once trailingslashit( get_template_directory() ) . 'lib/Mobile_Detect.php'; // Mobile Detect.
require_once trailingslashit( get_template_directory() ) . 'lib/build_slider.php'; // Post Types.
require_once trailingslashit( get_template_directory() ) . 'lib/admin/virtue-plugins-activate.php'; // Plugin Activation.
require_once trailingslashit( get_template_directory() ) . 'kt-framework/status.php'; // System status.
require_once trailingslashit( get_template_directory() ) . 'kt-framework/gfont-downloader.php'; // System status.

/*
 * Init Shortcodes
 */
require_once locate_template('/lib/kad_shortcodes/shortcodes.php');      					// Shortcodes
require_once locate_template('/lib/kad_shortcodes/carousel_shortcodes.php');   				// Carousel Shortcodes
require_once locate_template('/lib/kad_shortcodes/custom_carousel_shortcodes.php');   		// Carousel Shortcodes
require_once locate_template('/lib/kad_shortcodes/testimonial_shortcodes.php');   			// Carousel Shortcodes
require_once locate_template('/lib/kad_shortcodes/testimonial_form_shortcode.php');   		// Carousel Shortcodes
require_once locate_template('/lib/kad_shortcodes/blog_shortcodes.php');   					// Blog Shortcodes
require_once locate_template('/lib/kad_shortcodes/image_menu_shortcodes.php'); 				// image menu Shortcodes
require_once locate_template('/lib/kad_shortcodes/portfolio_shortcodes.php'); 				// Portfolio Shortcodes
require_once locate_template('/lib/kad_shortcodes/portfolio_type_shortcodes.php'); 			// Portfolio Shortcodes
require_once locate_template('/lib/kad_shortcodes/staff_shortcodes.php'); 					// Staff Shortcodes
require_once locate_template('/lib/kad_shortcodes/gallery.php');      						// Gallery Shortcode

/*
 * Init Widgets
 */
require_once trailingslashit( get_template_directory() ) . '/lib/premium_widgets.php'; 					// Premium Widgets
require_once trailingslashit( get_template_directory() ) . '/lib/widgets.php';         					// Sidebars and widgets main

/*
 * Template Hooks
 */
require_once trailingslashit( get_template_directory() ) . 'lib/pagebuilder/pagebuilder.php';          					// Pagebuilder functions
require_once trailingslashit( get_template_directory() ) . 'lib/pagebuilder/animations.php';          					// pagebuilder animations
require_once trailingslashit( get_template_directory() ) . 'lib/pagebuilder/prebuilt_layouts.php';          				// pagebuilder layouts
require_once trailingslashit( get_template_directory() ) . 'lib/custom.php';          					// Custom functions
require_once trailingslashit( get_template_directory() ) . 'lib/breadcrumbs.php';         				// Breadcrumbs
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks.php'; 					// Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/posts.php'; 				// Posts Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/portfolio.php'; 			// Portfolio Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/page.php'; 				// Page Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/posts_list.php'; 		// Post List Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/woocommerce/woo-core-hooks.php'; 				// Woocommerce core functions
require_once trailingslashit( get_template_directory() ) . 'lib/woocommerce/woo-archive-hooks.php'; 				// Woocommerce archive functions
require_once trailingslashit( get_template_directory() ) . 'lib/woocommerce/woo-single-product-hooks.php'; 		// Woocommerce single_product functions
require_once trailingslashit( get_template_directory() ) . 'lib/woo-account.php'; 								// Woocommerce account page functions

/*
 * Load Scripts
 */
require_once trailingslashit( get_template_directory() ) . 'lib/admin-scripts.php';    				// Admin Scripts
require_once trailingslashit( get_template_directory() ) . 'lib/scripts.php';        					// Scripts and stylesheets
require_once trailingslashit( get_template_directory() ) . 'lib/custom_css.php'; 						// Fontend Custom CSS

/*
 * Updater
 */
require_once trailingslashit( get_template_directory() ) . 'kt-framework/kadence-api-manager/kadence-api-manager.php'; // Load API.
require_once trailingslashit( get_template_directory() ) . 'lib/admin/virtue-dashboard.php'; // Load Dashboard.
require_once trailingslashit( get_template_directory() ) . 'kt-framework/kt-theme-updates.php'; // Load Update class.

/*
 * Admin Shortcode Btn
 */
function virtue_shortcode_init() {
	if ( is_admin() ) {
		if ( kad_is_edit_page() ) {
			require_once locate_template( '/lib/kad_shortcodes.php' );
		}
	}
}
add_action( 'init', 'virtue_shortcode_init' );

