<?php
/**
 * Determines whether or not to display the sidebar based on an array of conditional tags or page templates.
 *
 * If any of the is_* conditional tags or is_page_template(template_file) checks return true, the sidebar will NOT be displayed.
 *
 * @param array list of conditional tags (http://codex.wordpress.org/Conditional_Tags)
 * @param array list of page templates. These will be checked via is_page_template()
 *
 * @return boolean True will display the sidebar, False will not
 *
 */
class Kadence_Sidebar {
  private $conditionals;
  private $templates;

  public $display = true;

  function __construct($conditionals = array(), $templates = array()) {
    $this->conditionals = $conditionals;
    $this->templates    = $templates;

    $conditionals = array_map(array($this, 'check_conditional_tag'), $this->conditionals);
    $templates    = array_map(array($this, 'check_page_template'), $this->templates);

    if (in_array(true, $conditionals) || in_array(true, $templates)) {
      $this->display = false;
    }
  }

  private function check_conditional_tag($conditional_tag) {
    if (is_array($conditional_tag)) {
      return call_user_func_array($conditional_tag[0], $conditional_tag[1]);
    } else {
      return $conditional_tag();
    }
  }

  private function check_page_template($page_template) {
    return is_page_template($page_template);
  }
}
function kadence_sidebar_id() {
	error_log( "The kadence_sidebar_id() function is deprecated since version 4.3.5. Please use virtue_sidebar_id() instead." );
	return virtue_sidebar_id();
}
function virtue_sidebar_id() {
    if(is_front_page()) {
      global $virtue_premium;
        if (!empty($virtue_premium['home_sidebar'])) {
          $sidebar = $virtue_premium['home_sidebar'];
        } else {
          $sidebar = 'sidebar-primary';
        } 
    } elseif (is_search()) {
      global $virtue_premium; 
        if(isset($virtue_premium['search_sidebar'])) {
          $sidebar = $virtue_premium['search_sidebar'];
        } else  {
          $sidebar = 'sidebar-primary';
        } 
    } elseif( class_exists('woocommerce') and (is_shop())) {
      global $virtue_premium;
        if (!empty($virtue_premium['shop_sidebar'])) {
          $sidebar = $virtue_premium['shop_sidebar'];
        } else {
          $sidebar = 'sidebar-primary';
        } 
    } elseif( class_exists('woocommerce') and (is_product_category() || is_product_tag())) {
        global $virtue_premium;
        if (!empty($virtue_premium['shop_cat_sidebar'])) {
          $sidebar = $virtue_premium['shop_cat_sidebar'];
        } else {
          $sidebar = 'sidebar-primary';
        } 
    } elseif (class_exists('woocommerce') and is_product()) {
      global $post;
        $sidebar_name = get_post_meta( $post->ID, '_kad_sidebar_choice', true ); 
        if (empty($sidebar_name) || $sidebar_name == 'default') {
          global $virtue_premium;
          if(!empty($virtue_premium['product_sidebar_default_sidebar'])) {
            $sidebar = $virtue_premium['product_sidebar_default_sidebar'];
          } else {
            $sidebar = 'sidebar-primary';
          }
        } else if(!empty($sidebar_name)) {
          $sidebar = $sidebar_name;
        } else {
          $sidebar = 'sidebar-primary';
        }
    } elseif (is_singular('post') ) {
      global $post;
        $sidebar_name = get_post_meta( $post->ID, '_kad_sidebar_choice', true ); 
        if (empty($sidebar_name) || $sidebar_name == 'default') {
          global $virtue_premium;
          if(!empty($virtue_premium['blogpost_sidebar_id_default'])) {
            $sidebar = $virtue_premium['blogpost_sidebar_id_default'];
          } else {
            $sidebar = 'sidebar-primary';
          }
        } else if(!empty($sidebar_name)) {
          $sidebar = $sidebar_name;
        } else {
          $sidebar = 'sidebar-primary';
        }
    } elseif( is_page_template('page-blog.php') || is_page_template('page-blog-grid.php') || is_page_template('page-sidebar.php') || is_page_template('page-feature-sidebar.php') || is_single() || is_singular('staff') ) {
      global $post;
        $sidebar_name = get_post_meta( $post->ID, '_kad_sidebar_choice', true );
        if (!empty($sidebar_name) && $sidebar_name == 'default') {
        	 global $virtue_premium;
	          if(!empty($virtue_premium['blogpost_sidebar_id_default'])) {
	            $sidebar = $virtue_premium['blogpost_sidebar_id_default'];
	          } else {
	            $sidebar = 'sidebar-primary';
	          }
        } else if (!empty($sidebar_name)) {
            $sidebar = $sidebar_name;
        } else {
          $sidebar = 'sidebar-primary';
        } 
    } elseif (is_archive()) {
      global $virtue_premium; 
        if(isset($virtue_premium['blog_cat_sidebar'])) {
          $sidebar = $virtue_premium['blog_cat_sidebar'];
        } else  {
          $sidebar = 'sidebar-primary';
        } 
    }
    elseif(is_category()) {
      global $virtue_premium; 
        if(isset($virtue_premium['blog_cat_sidebar'])) {
          $sidebar = $virtue_premium['blog_cat_sidebar'];
        } else  {
          $sidebar = 'sidebar-primary';
        } 
    }
    elseif (is_tag()) {
      $sidebar = 'sidebar-primary';
    }
    elseif (is_post_type_archive()) {
      $sidebar = 'sidebar-primary';
    }
     elseif (is_day()) {
       $sidebar = 'sidebar-primary';
     }
     elseif (is_month()) {
       $sidebar = 'sidebar-primary';
     }
     elseif (is_year()) {
       $sidebar = 'sidebar-primary';
     }
     elseif (is_author()) {
       $sidebar = 'sidebar-primary';
    }  else {
      $sidebar = 'sidebar-primary';
    }

    return apply_filters('kadence_sidebar_id', $sidebar);
}