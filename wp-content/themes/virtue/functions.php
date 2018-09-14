<?php
/**
 * Add functions files
 *
 * @package Virtue Theme
 */

/**
 * Language setup
 */
function virtue_lang_setup() {
	load_theme_textdomain( 'virtue', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'virtue_lang_setup' );

/*
 * Init Theme Options
 */
require_once trailingslashit( get_template_directory() ) . 'themeoptions/framework.php';                 // Options framework.
require_once trailingslashit( get_template_directory() ) . 'themeoptions/options.php';                   // Options settings.
require_once trailingslashit( get_template_directory() ) . 'themeoptions/options/virtue_extension.php';  // Options framework extension.

/*
 * Init Theme Startup/Core utilities/classes
 */
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-virtue-plugin-check.php';          // Check plugin class.
require_once trailingslashit( get_template_directory() ) . 'lib/utils.php';                                      // Utility functions.
require_once trailingslashit( get_template_directory() ) . 'lib/init.php';                                       // Initial theme setup and constants
require_once trailingslashit( get_template_directory() ) . 'lib/sidebar.php';                                    // Sidebar class
require_once trailingslashit( get_template_directory() ) . 'lib/config.php';                                     // Configuration
require_once trailingslashit( get_template_directory() ) . 'lib/cleanup.php';                                    // Cleanup
require_once trailingslashit( get_template_directory() ) . 'lib/elementor/elementor-support.php';                // Elementor Support
require_once trailingslashit( get_template_directory() ) . 'lib/nav.php';                                        // Custom nav modifications
require_once trailingslashit( get_template_directory() ) . 'lib/metaboxes.php';                           // Custom metaboxes
require_once trailingslashit( get_template_directory() ) . 'lib/comments.php';                            // Custom comments modifications
require_once trailingslashit( get_template_directory() ) . 'lib/image_functions.php';                     // Image functions
require_once trailingslashit( get_template_directory() ) . 'lib/class-virtue-get-image.php';              // Image Class
require_once trailingslashit( get_template_directory() ) . 'lib/custom.php';                              // Custom functions
require_once trailingslashit( get_template_directory() ) . 'lib/kadence-toolkit-plugin.php';              // Plugin Activation.

/*
* Woomcommerce Support
*/
require_once trailingslashit( get_template_directory() ) . 'lib/woocommerce/woo-core-hooks.php';           // Woocommerce Core functions
require_once trailingslashit( get_template_directory() ) . 'lib/woocommerce/woo-archive-hooks.php';        // Woocommerce Archive functions
require_once trailingslashit( get_template_directory() ) . 'lib/woocommerce/woo-single-product-hooks.php'; // Woocommerce single product functions
require_once trailingslashit( get_template_directory() ) . 'lib/woo-account.php';                          // Woocommerce account functions.

/*
 * Template Hooks
 */
require_once trailingslashit( get_template_directory() ) . 'lib/authorbox.php';                             // Author box
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/portfolio_hooks.php';        // Portfolio Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/post_hooks.php';             // Post Template Hooks
require_once trailingslashit( get_template_directory() ) . 'lib/template_hooks/page_hooks.php';             // Post Template Hooks.

/*
 * Init Widgets
 */
require_once trailingslashit( get_template_directory() ) . 'lib/widgets.php';                               // Sidebars and widgets.

/*
 * Load Scripts
 */
require_once trailingslashit( get_template_directory() ) . 'lib/admin-scripts.php';                         // Admin Scripts
require_once trailingslashit( get_template_directory() ) . 'lib/scripts.php';                               // Scripts and stylesheets
require_once trailingslashit( get_template_directory() ) . 'lib/custom-css.php';                            // Fontend Custom CSS.

/**
 * Note: Do not add any custom code here. Please use a custom plugin or child theme so that your customizations aren't lost during updates.
 * https://www.kadencethemes.com/child-themes/
 */
