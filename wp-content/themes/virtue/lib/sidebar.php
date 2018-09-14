<?php
/**
 * Determines whether or not to display the sidebar based on an array of conditional tags or page templates.
 * @return boolean True will display the sidebar, False will not
 *
 */
class Kadence_Sidebar {
	private $conditionals;
	private $templates;

	public $display = true;

	function __construct( $conditionals = array(), $templates = array() ) {
		$this->conditionals = $conditionals;
		$this->templates    = $templates;

		$conditionals = array_map( array( $this, 'check_conditional_tag'), $this->conditionals );
		$templates    = array_map( array( $this, 'check_page_template'), $this->templates );

		if (in_array(true, $conditionals) || in_array(true, $templates)) {
			$this->display = false;
		}
	}

	private function check_conditional_tag($conditional_tag) {
		if ( is_array( $conditional_tag ) ) {
			return call_user_func_array( $conditional_tag[0], $conditional_tag[1] );
		} else {
			return $conditional_tag();
		}
	}

	private function check_page_template( $page_template ) {
		return is_page_template( $page_template );
	}
}
function kadence_sidebar_id() {
	error_log( "The kadence_sidebar_id() function is deprecated since version 3.1.1. Please use virtue_sidebar_id() instead." );
	return virtue_sidebar_id();
}
function virtue_sidebar_id() {
    if ( is_front_page() ) {
      global $virtue;
        if (!empty($virtue['home_sidebar'])) {
          $sidebar = $virtue['home_sidebar'];
          }
        else  {
          $sidebar = 'sidebar-primary';
        } 
    } else if( class_exists('woocommerce') and (is_shop() || is_product_category() || is_product_tag())) {
        global $virtue;
        if (!empty($virtue['shop_sidebar'])) {
          $sidebar = $virtue['shop_sidebar'];
        } else {
          $sidebar = 'sidebar-primary';
        } 
    } elseif( class_exists('woocommerce') and (is_account_page())) {
            get_template_part('templates/account', 'sidebar');
            $sidebar = "";
    } elseif ( is_page_template( 'page-blog.php' ) || is_page_template('page-sidebar.php') || is_page_template('page-feature-sidebar.php') || 'post' === get_post_type() || 'page.php' === basename( get_page_template() ) ) {
		global $post;
		$sidebar_name = get_post_meta( $post->ID, '_kad_sidebar_choice', true );
		if ( ! empty( $sidebar_name ) ) {
			$sidebar = $sidebar_name;
		} else {
			$sidebar = 'sidebar-primary';
		}
    } else if (is_archive()) {
      $sidebar = 'sidebar-primary';
    } else if(is_category()) {
      $sidebar = 'sidebar-primary';
    } elseif (is_tag()) {
      $sidebar = 'sidebar-primary';
    } elseif (is_post_type_archive()) {
      $sidebar = 'sidebar-primary';
    } elseif (is_day()) {
       $sidebar = 'sidebar-primary';
    } elseif (is_month()) {
       $sidebar = 'sidebar-primary';
    } elseif (is_year()) {
       $sidebar = 'sidebar-primary';
    } elseif (is_author()) {
       $sidebar = 'sidebar-primary';
    } elseif (is_search()) {
      $sidebar = 'sidebar-primary';
    } else {
      $sidebar = 'sidebar-primary';
    }

    return apply_filters('kadence_sidebar_id', $sidebar);
}