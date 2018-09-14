<?php
/**
 * Cleaner menus
 *
 * @package Virtue Theme
 */

// Get Walkers.
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-virtue-nav-walker.php';
require_once trailingslashit( get_template_directory() ) . 'lib/classes/class-virtue-mobile-nav-walker.php';

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use Virtue_Nav_Walker by default
 *
 * @param array $args array of args.
 */
function virtue_nav_menu_args( $args = '' ) {
	$virtue_nav_menu_args = array();
	if ( ! isset( $args['container_class'] ) || empty( $args['container_class'] ) ) {
		$virtue_nav_menu_args['container'] = false;
	}

	if ( ! $args['items_wrap'] ) {
		$virtue_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
	}

	if ( ! $args['walker'] ) {
		$virtue_nav_menu_args['walker'] = new Virtue_Nav_Walker();
	}

	return array_merge( $args, $virtue_nav_menu_args );
}
add_filter( 'wp_nav_menu_args', 'virtue_nav_menu_args', 10 );

/**
 * Remove the id="" on nav menu items
 */
add_filter( 'nav_menu_item_id', '__return_null' );

/**
 * Deprecated class.
 *
 * @category class
 */
class kadence_Nav_Walker extends Virtue_Nav_Walker {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		error_log( 'The kadence_Nav_Walker class is deprecated since version 3.2.8. Please use Virtue_Nav_Walker instead.' );
		parent::start_lvl( $output, $depth, $args );
	}
}

/**
 * Deprecated class.
 *
 * @category class.
 */
class kadence_mobile_walker extends Virtue_Mobile_Nav_Walker {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		error_log( 'The kadence_mobile_walker class is deprecated since version 3.2.8. Please use Virtue_Mobile_Nav_Walker instead.' );
		parent::start_lvl( $output, $depth, $args );
	}
}
