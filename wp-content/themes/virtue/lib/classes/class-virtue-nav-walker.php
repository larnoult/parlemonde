<?php
/**
 * Walker for wp_nav_menu()
 *
 * @package Virtue Theme
 */

/**
 * Walker Class for wp_nav_menu()
 *
 * @category class
 */
class Virtue_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Check for the current classes
	 *
	 * @param array $classes the classes for the menu item.
	 */
	public function check_current( $classes ) {
		return preg_match( '/(current[-_])|active|dropdown/', $classes );
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default classes.
		$classes = array( 'sub-menu', 'sf-dropdown-menu', 'dropdown' );

		$class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= "{$n}{$indent}<ul$class_names>{$n}";
	}

	/**
	 * Starts the element output.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$slug = sanitize_title( $item->title );
		$id   = 'menu-' . $slug;

		$class_names   = '';
		$value         = '';
		$li_attributes = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = array_filter( $classes, array( &$this, 'check_current' ) );

		$classes[] = 'menu-item-' . $item->ID;

		$custom_classes = get_post_meta( $item->ID, '_menu_item_classes', true );
		if ( $custom_classes ) {
			foreach ( $custom_classes as $custom_class ) {
				if ( strpos( $custom_class, 'icon' ) !== false || strpos( $custom_class, 'kt-icon' ) !== false ) {
					$icon_class = $custom_class;
				} else {
					$classes[] = $custom_class;
				}
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $id ) . ' ' . esc_attr( $class_names ) . '"' : ' class="' . esc_attr( $id ) . '"';

		$output .= $indent . '<li ' . $class_names . '>';

		$atts                 = array();
		$atts['title']        = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target']       = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']          = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']         = ! empty( $item->url ) ? $item->url : '';
		$atts['aria-current'] = $item->current ? 'page' : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		if ( ! isset( $args->link_before ) || ( isset( $args->link_before ) && empty( $args->link_before ) ) ) {
			$args->link_before = '<span>';
			$args->link_after  = '</span>';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$description = ! empty( $item->description ) ? '<span class="sf-description">' . esc_attr( $item->description ) . '</span>' : '';
		$icon        = ! empty( $icon_class ) ? '<i class="' . esc_attr( $icon_class ) . '"></i>' : '';

		// No Description for submenu items.
		if ( 0 !== $depth ) {
			$description = '';
		}

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>' . $icon;
		$item_output .= $args->link_before . $title . $description . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing (passed by reference).
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Used to append additional content (passed by reference).
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$element->is_dropdown = ( ( ! empty( $children_elements[ $element->ID ] ) && ( ( $depth + 1 ) < $max_depth || ( 0 === $max_depth ) ) ) );

		if ( $element->is_dropdown ) {
			if ( 0 === $depth ) {
				$element->classes[] = 'sf-dropdown';
			} elseif ( 1 === $depth ) {
				$element->classes[] = 'sf-dropdown-submenu';
			}
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
