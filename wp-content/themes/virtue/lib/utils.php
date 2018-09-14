<?php
/**
 * Theme wrapper
 *
 * @link http://scribu.net/wordpress/theme-wrappers.html
 */
function kadence_template_path() {
	return Kadence_Wrapping::$main_template;
}

function kadence_sidebar_path() {
	return Kadence_Wrapping::sidebar();
}

class Kadence_Wrapping {

	// Stores the full path to the main template file
	static $main_template;

	// Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
	static $base;

	static function wrap( $template ) {

		if ( is_embed() ) {
			return $template;
		}
		self::$main_template = $template;

		self::$base = substr(basename(self::$main_template), 0, -4);

		if (self::$base === 'index') {
			self::$base = false;
		}

		$templates = array('base.php');

		if (self::$base) {
			array_unshift( $templates, sprintf( 'base-%s.php', self::$base ) );
		}

		return locate_template($templates);
	}

	static function sidebar() {
		$templates = array('templates/sidebar.php');

		if (self::$base) {
			array_unshift( $templates, sprintf('templates/sidebar-%s.php', self::$base ) );
		}

		return locate_template($templates);
	}
}
add_filter('template_include', array( 'Kadence_Wrapping', 'wrap'), 101 );

add_action( 'init', 'virtue_toolset_layout_support' );
function virtue_toolset_layout_support() {
	// Add tool layout Support
	if ( class_exists('WPDDL_Templates_Settings') ) {
		// SET in the default template path.
		add_filter('template_include', array('Kadence_Wrapping', 'wrap'), 10);
	}
}

/**
 * Page titles
 */
function virtue_title() {
  if ( is_home() ) {
    	if ( get_option( 'page_for_posts', true ) ) {
      		$title = get_the_title( get_option( 'page_for_posts', true ) );
    	} else {
     		$title = __( 'Latest Posts', 'virtue' );
    	}
  	} elseif ( is_archive() ) {
  		$title = get_the_archive_title();
  	} elseif ( is_search() ) {
  		/* translators: %s: search term */
    	$title = sprintf( __( 'Search Results for %s', 'virtue' ), get_search_query() );
  	} elseif ( is_404() ) {
    	$title = __( 'Not Found', 'virtue' );
  	} else {
    	$title = get_the_title();
  	}
  	return apply_filters('virtue_title', $title);
}
add_filter('get_the_archive_title', 'virtue_filter_archive_title');
function virtue_filter_archive_title( $title ){
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif (is_author()) {
		/* translators: %s: Author Name */
		$title = sprintf( __( 'Author Archives: %s', 'virtue'), get_the_author() );
	} else if ($term) {
		$title = $term->name;
	} elseif (is_day()) {
		/* translators: %s: Date */
		$title = sprintf( __( 'Daily Archives: %s', 'virtue' ), get_the_date() );
	} elseif (is_month()) {
		/* translators: %s: Date showing year and month */
		$title = sprintf( __( 'Monthly Archives: %s', 'virtue' ), get_the_date( 'F Y' ) );
	} elseif (is_year()) {
		/* translators: %s: Date showing year only */
		$title = sprintf( __( 'Yearly Archives: %s', 'virtue' ), get_the_date( 'Y' ) );
	} 
	return $title;
}