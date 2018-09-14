<?php

/**
 * Configuration values
 */
function virtue_get_options() {
    $options = get_option( 'virtue' );
	if ( isset( $_REQUEST['wp_customize'] ) ) {
    	$options = apply_filters('virtue_theme_options_filter', $options);
    }

    return $options;
}

define('POST_EXCERPT_LENGTH', 40);

/**
 * .main classes
 */
function virtue_main_class() {
  if (virtue_display_sidebar()) {
    // Classes on pages with the sidebar
    $class = 'col-lg-9 col-md-8';
  } else {
    // Classes on full width pages
    $class = 'col-md-12';
  }

  return $class;
}

/**
 * .sidebar classes
 */
function virtue_sidebar_class() {
  return 'col-lg-3 col-md-4';
}

/**
 * Virtue Container classes
 */
function virtue_container_class() {
	global $post;
	$page_content_width = get_post_meta( $post->ID, '_kad_page_content_width', true );
	if ( isset( $page_content_width ) && 'full' === $page_content_width ) {
		$content_class = 'container-fullwidth';
	} elseif ( isset( $page_content_width ) && 'contained' === $page_content_width ) {
		$content_class = 'container-contained';
	} else {
		global $virtue;
		if ( isset( $virtue['default_page_content_width'] ) && 'full' === $virtue['default_page_content_width'] ) {
			$content_class = 'container-fullwidth';
		} else {
			$content_class = 'container-contained';
		}
	}
	return apply_filters( 'virtue_page_content_class', $content_class );
}

/* Depreciated */
function kadence_sidebar_class() {
	error_log( "The kadence_sidebar_class() function is deprecated since version 3.0.7. Please use virtue_sidebar_class() instead." );
	return virtue_sidebar_class();
}
function kadence_main_class() {
	error_log( "The kadence_main_class() function is deprecated since version 3.0.7. Please use virtue_main_class() instead." );
	return virtue_main_class();
}
function kadence_display_sidebar() {
	error_log( "The kadence_display_sidebar() function is deprecated since version 3.1.1. Please use virtue_display_sidebar() instead." );
	return virtue_display_sidebar();
}
/**
 * Define which pages shouldn't have the sidebar
 *
 * See lib/sidebar.php for more details
 */
function virtue_display_sidebar() {
	if ( class_exists( 'woocommerce' ) )  {
		$sidebar_config = new Kadence_Sidebar(
		array('kadence_sidebar_on_shop_page','kadence_sidebar_on_blog_post','kadence_sidebar_on_blog_page', 'virtue_sidebar_on_default_page','is_404','kadence_sidebar_on_home_page','is_cart','is_product','is_checkout','kadence_sidebar_on_myaccount_page',array('is_singular', array('portfolio')),array('is_singular', array('kbe_knowledgebase')), array('is_tax', array('portfolio-type'))
        ),
		array('page-fullwidth.php','page-feature.php','page-portfolio.php','page-staff-grid.php','page-testimonial-grid.php','page-contact.php', 'elementor_canvas', 'elementor_header_footer',)
      );
	} else {
		$sidebar_config = new Kadence_Sidebar(
		array('kadence_sidebar_on_blog_post','kadence_sidebar_on_blog_page','virtue_sidebar_on_default_page','is_404','kadence_sidebar_on_home_page', array('is_singular', array('portfolio')),array('is_singular', array('kbe_knowledgebase')), array('is_tax', array('portfolio-type'))
		),
		array( 'page-fullwidth.php','page-feature.php','page-portfolio.php','page-staff-grid.php','page-testimonial-grid.php','elementor_header_footer','elementor_canvas','page-contact.php' ) );
	}

	return apply_filters('kadence_display_sidebar', $sidebar_config->display);
}
function kadence_sidebar_on_shop_page() {
  global $virtue; 
    if(isset($virtue['shop_layout']) && $virtue['shop_layout'] == 'sidebar') {
      if( is_shop() || is_product_category() || is_product_tag()) {
        return false;
      }
    } else {
      if( is_shop() || is_product_category() || is_product_tag()) {
        return true;
    }
  }
}

/**
 * Check defualt page template
 */
function virtue_sidebar_on_default_page() {
	if ( is_page() && 'page.php' === basename( get_page_template() ) ) {
		global $post;
		$postsidebar = get_post_meta( $post->ID, '_kad_page_sidebar', true );
		if ( isset( $postsidebar ) && 'no' === $postsidebar ) {
			return true;
		} elseif ( isset( $postsidebar ) && 'default' === $postsidebar || empty( $postsidebar ) ) {
			global $virtue;
			if ( isset( $virtue['default_page_show_sidebar'] ) && 'no' == $virtue['default_page_show_sidebar'] ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

function kadence_sidebar_on_blog_post() {
  if(is_single()) {
    global $post;
    $postsidebar = get_post_meta( $post->ID, '_kad_post_sidebar', true );
      if(isset($postsidebar) && $postsidebar == 'no') {
        return true;
        } else {
          return false;
        }
      }
}
function kadence_sidebar_on_home_page() {
  if(is_front_page()) {
      global $virtue; 
      if(isset($virtue['home_sidebar_layout']) && $virtue['home_sidebar_layout'] == 'sidebar') {
        return false;
        } else {
          return true;
        }
   }
}
function kadence_sidebar_on_blog_page() {
  if(is_page_template('page-blog.php')) {
    global $post;
    $pagesidebar = get_post_meta( $post->ID, '_kad_page_sidebar', true );
      if(isset($pagesidebar) && $pagesidebar == 'no') {
        return true;
        } else {
          return false;
        }
      }
}
function kadence_sidebar_on_myaccount_page() {
  if(is_account_page()) {
      if(virtue_is_wc_version_gte_2_6() == 1) {
           return true;
        } else {
            $current_user = wp_get_current_user();
              if ( 0 == $current_user->ID ) {
                  return true;
              } else { 
                  return false;
              }
        }
   }
}
function kadence_display_topbar() {
  global $virtue;
   if(isset($virtue['topbar'])) {
  if($virtue['topbar'] == 1 ) {$topbar = true;} else { $topbar = false;}
} else {$topbar = true;}
  return $topbar;
  }
function kadence_display_topbar_icons() {
  global $virtue;
 if(isset($virtue['topbar_icons'])) {
  if($virtue['topbar_icons'] == 1 ) {$topbaricons = true;} else { $topbaricons = false;}
} else {$topbaricons = false;}
  return $topbaricons;
  }
  function kadence_display_top_search() {
  global $virtue;
 if(isset($virtue['topbar_search'])) {
  if($virtue['topbar_search'] == 1 ) {$topsearch = true;} else { $topsearch = false;}
} else {$topsearch = true;}
  return $topsearch;
  }
function kadence_display_topbar_widget() {
  global $virtue;
 if(isset($virtue['topbar_widget'])) {
  if($virtue['topbar_widget'] == 1 ) {$topbarwidget = true;} else { $topbarwidget = false;}
} else {$topbarwidget = false;}
  return $topbarwidget;
  }

// Add body class for wide or boxed layout
add_filter('body_class','kadence_layout_class_names');
function kadence_layout_class_names($classes) {
  global $virtue;
  // add 'class-name' to the $classes array
  if(isset($virtue['boxed_layout'])) {
    $layoutstyle = $virtue['boxed_layout'];
  } else {
    $layoutstyle = 'wide';
  }

if ($layoutstyle == "boxed") {
  $classes[] = 'boxed';
}
else {
  $classes[] = 'wide';
}
  // return the $classes array
  return $classes;
}

if (!isset($content_width)) { $content_width = 1140; }
