<?php
/**
 * Configuration values
 */
function virtue_premium_get_options() {
	$options = get_option( 'virtue_premium' );
	if ( isset( $_REQUEST[ 'wp_customize' ] ) ) {
		$options = apply_filters( 'virtue_premium_theme_options_filter', $options );
	}

    return $options;
}
function virtue_animate() {
	global $virtue_premium;
	if ( isset( $virtue_premium[ 'virtue_animate_in' ] ) && '1' == $virtue_premium[ 'virtue_animate_in' ] ) {
		$animate = 1;
	} else {
		$animate = 0;
	}
	return $animate;
}

function virtue_init_define_values(){
	global $virtue_premium; 
	if ( isset( $virtue_premium[ 'post_word_count' ] ) ) {
		$excerptlength = $virtue_premium[ 'post_word_count' ];
	} else {
		$excerptlength = '40'; 
	}

	define( 'POST_EXCERPT_LENGTH', $excerptlength );
}

add_action('init', 'virtue_init_define_values');


/**
 * .main classes
 */
function virtue_main_class() {
	if ( virtue_display_sidebar() ) {
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
		global $virtue_premium;
		if ( isset( $virtue_premium['default_page_content_width'] ) && 'full' === $virtue_premium['default_page_content_width'] ) {
			$content_class = 'container-fullwidth';
		} else {
			$content_class = 'container-contained';
		}
	}
	return apply_filters( 'ascend_page_content_class', $content_class );
}

/**
 * Depreciated 
 */
function kadence_sidebar_class() {
	error_log( "The kadence_sidebar_class() function is deprecated since version 4.3.5. Please use virtue_sidebar_class() instead." );
	return virtue_sidebar_class();
}
function kadence_main_class() {
	error_log( "The kadence_main_class() function is deprecated since version 4.3.5. Please use virtue_main_class() instead." );
	return virtue_main_class();
}
function kadence_display_sidebar() {
	error_log( "The kadence_display_sidebar() function is deprecated since version 4.3.5. Please use virtue_display_sidebar() instead." );
	return virtue_display_sidebar();
}

/**
 * Define which pages shouldn't have the sidebar
 *
 * See lib/sidebar.php for more details
 */
function virtue_display_sidebar() {
	if ( class_exists( 'woocommerce' ) ) {
		$sidebar_config = new Kadence_Sidebar(
		array('kadence_sidebar_on_archive_page','kadence_sidebar_on_shop_page', 'virtue_sidebar_on_default_page', 'kadence_sidebar_on_shop_cat_page','kadence_sidebar_on_blog_post','kadence_sidebar_on_blog_page','kadence_sidebar_on_product_post', 'kadence_sidebar_on_staff_post','is_404','kadence_sidebar_on_home_page','kadence_sidebar_on_myaccount_page','is_cart','is_checkout',array('is_singular', array('portfolio')), array('is_singular', array('attachment')), array('is_singular', array('kbe_knowledgebase')), array('is_tax', array('portfolio-type')), array('is_tax', array('portfolio-tag'))
		),
		array('page-fullwidth.php','page-feature.php','page-portfolio.php','page-landing.php','page-staff-grid.php','page-testimonial-grid.php','page-contact.php','elementor_header_footer','elementor_canvas', 'page-portfolio-category.php')
		);
	} else {
		$sidebar_config = new Kadence_Sidebar(
		array('kadence_sidebar_on_blog_post', 'virtue_sidebar_on_default_page', 'kadence_sidebar_on_archive_page_no_woo','kadence_sidebar_on_staff_post','kadence_sidebar_on_blog_page','is_404','kadence_sidebar_on_home_page', array('is_singular', array('portfolio')), array('is_singular', array('attachment')),array('is_singular', array('kbe_knowledgebase')), array('is_tax', array('portfolio-type'))
		),
		array('page-fullwidth.php','page-feature.php','page-portfolio.php','page-landing.php','page-staff-grid.php','page-testimonial-grid.php','page-contact.php','elementor_header_footer','elementor_canvas', 'page-portfolio-category.php' )
		);
	}

	return apply_filters( 'kadence_display_sidebar', $sidebar_config->display );
}

function kadence_sidebar_on_shop_page() {
	if ( is_shop() ) {
		global $virtue_premium; 
		if ( isset( $virtue_premium[ 'shop_layout' ] ) && 'sidebar' == $virtue_premium[ 'shop_layout' ] ) {
			return false;
		} else {
			return true;
		}
	}
}
add_filter('kadence_display_sidebar', 'kadence_sidebar_on_search_page', 5);
function kadence_sidebar_on_search_page( $sidebar ) {
	global $virtue_premium, $wp_query;
	if ( isset( $virtue_premium[ 'enable_custom_404' ] ) && 1 == $virtue_premium[ 'enable_custom_404' ] ) {
		if ( isset( $virtue_premium[ 'custom_404_page' ] ) && ! empty( $virtue_premium[ 'custom_404_page' ] ) ) {
			$page_template = get_page_template_slug();
			if ( ( in_array( 'default', array( 'page-fullwidth.php','page-feature.php','page-landing.php','page-contact.php' ), true ) && ! $page_template ) || in_array( $page_template,  array( 'page-fullwidth.php','page-feature.php','page-landing.php','page-contact.php' ), true ) ) {
				$sidebar = false;
	        }
		}
	}
	if( is_search() ) {
		$sidebar = true;
	}
	return $sidebar;
}
function kadence_sidebar_on_shop_cat_page() {
	if ( is_product_category() || is_product_tag() ) {
		global $virtue_premium; 
		if ( isset( $virtue_premium[ 'shop_cat_layout' ] ) && 'sidebar' == $virtue_premium[ 'shop_cat_layout' ] ) {
			return false;
		} else {
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
			global $virtue_premium;
			if ( isset( $virtue_premium['default_page_show_sidebar'] ) && 'no' == $virtue_premium['default_page_show_sidebar'] ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

function kadence_sidebar_on_product_post() {
	if ( is_product() ) {
		global $post;
		$postsidebar = get_post_meta( $post->ID, '_kad_post_sidebar', true );
		if ( isset( $postsidebar ) && 'yes' == $postsidebar ) {
			return false;
		} else if ( empty( $postsidebar ) || ! isset( $postsidebar ) || 'default' == $postsidebar ){
			global $virtue_premium; 
			if ( isset( $virtue_premium[ 'product_sidebar_default' ] ) && 'yes' == $virtue_premium[ 'product_sidebar_default' ] ) {
				return false;
			} else  {
				return true;
			}
		} else {
			return true;
		}
	}
}
function kadence_sidebar_on_blog_post() {
  if(is_single() && !is_singular('product') ) {
    global $post;
    $postsidebar = get_post_meta( $post->ID, '_kad_post_sidebar', true );
      if(isset($postsidebar) && $postsidebar == 'no') {
        return true;
        } else if(empty($postsidebar) || $postsidebar == 'default') {
          global $virtue_premium;
          if(isset($virtue_premium['blogpost_sidebar_default']) && $virtue_premium['blogpost_sidebar_default'] == 'no') {
            return true;
          } else {
            return false;
          }
        } else {
          return false;
        }
      }
}
function kadence_sidebar_on_staff_post() {
  if(is_singular('staff')) {
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
      global $virtue_premium; 
      if(isset($virtue_premium['home_sidebar_layout']) && $virtue_premium['home_sidebar_layout'] == 'sidebar') {
        return false;
        } else {
          return true;
        }
   }
}
function kadence_sidebar_on_myaccount_page() {
  if(is_account_page()) {
    if(kad_is_wc_version_gte_2_6() == 1) {
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
function kadence_sidebar_on_archive_page() {
  if(is_archive() && !is_shop() && !is_product_category() && !is_product_tag()) {
      global $virtue_premium; 
      if(isset($virtue_premium['blog_cat_layout']) && $virtue_premium['blog_cat_layout'] == 'sidebar') {
        return false;
        } else {
          return true;
        }
   }
}
function kadence_sidebar_on_archive_page_no_woo() {
  if(is_archive()) {
      global $virtue_premium; 
      if(isset($virtue_premium['blog_cat_layout']) && $virtue_premium['blog_cat_layout'] == 'sidebar') {
        return false;
        } else {
          return true;
        }
   }
}
function kadence_sidebar_on_blog_page() {
  if(is_page_template('page-blog.php') || is_page_template('page-blog-grid.php') ) {
    global $post;
    $pagesidebar = get_post_meta( $post->ID, '_kad_page_sidebar', true );
      if(isset($pagesidebar) && $pagesidebar == 'no') {
        return true;
        } else {
          return false;
        }
      }
}
function kadence_shop_layout_css() {
  global $virtue_premium;
  if(virtue_display_sidebar()) {
          if(isset($virtue_premium['product_shop_layout'])) {
            $columns = "shopcolumn".$virtue_premium['product_shop_layout']." shopsidebarwidth"; 
          } else {$columns = "shopcolumn4 shopsidebarwidth"; }
      } else {
         if(isset($virtue_premium['product_shop_layout'])) { $columns = "shopcolumn".$virtue_premium['product_shop_layout']." shopfullwidth"; 
          } else { $columns = "shopcolumn4 shopfullwidth";  }
      }

  return $columns;
}
function kadence_category_layout_css() {
  global $virtue_premium;
  if(virtue_display_sidebar()) {
          if(isset($virtue_premium['product_shop_layout']) && $virtue_premium['product_shop_layout'] == "single") {
            $columns = "s-threecolumn"; 
          } else {
            $columns = "s-threecolumn"; 
          }
    } else {
        if(isset($virtue_premium['product_shop_layout']) && $virtue_premium['product_shop_layout'] == "single") {
            $columns = "fourcolumn"; 
          } else {
            $columns = "fourcolumn";
          }
        }

  return $columns;
}

function kadence_display_topbar() {
  global $virtue_premium;
   if(isset($virtue_premium['topbar'])) {
  if($virtue_premium['topbar'] == 1 ) {$topbar = true;} else { $topbar = false;}
} else {$topbar = true;}
  return $topbar;
  }
function kadence_display_topbar_icons() {
  global $virtue_premium;
 if(isset($virtue_premium['topbar_icons'])) {
  if($virtue_premium['topbar_icons'] == 1 ) {$topbaricons = true;} else { $topbaricons = false;}
} else {$topbaricons = false;}
  return $topbaricons;
  }
  function kadence_display_top_search() {
  global $virtue_premium;
 if(isset($virtue_premium['topbar_search'])) {
  if($virtue_premium['topbar_search'] == 1 ) {$topsearch = true;} else { $topsearch = false;}
} else {$topsearch = true;}
  return $topsearch;
  }
function kadence_display_topbar_widget() {
  global $virtue_premium;
 if(isset($virtue_premium['topbar_widget'])) {
  if($virtue_premium['topbar_widget'] == 1 ) {$topbarwidget = true;} else { $topbarwidget = false;}
} else {$topbarwidget = false;}
  return $topbarwidget;
  }

// All added body classes
add_filter( 'body_class', 'virtue_added_body_classes' );
function virtue_added_body_classes( $classes ) {
	global $virtue_premium;
	if ( isset( $virtue_premium['kadence_lightbox'] ) && $virtue_premium['kadence_lightbox'] == 1 ) {
		$classes[] = 'kt-turnoff-lightbox';
	}
	if ( isset( $virtue_premium['show_subindicator'] ) && $virtue_premium['show_subindicator'] == 1 ) {
		$classes[] = 'kt-showsub-indicator';
	}
	if ( isset( $virtue_premium['sticky_header'] ) && $virtue_premium['sticky_header'] == 1 ) {
		$classes[] = 'stickyheader';
	} else {
		$classes[] = 'notsticky';
	}
	if ( isset( $virtue_premium['skin_stylesheet'] ) && ! empty( $virtue_premium['skin_stylesheet'] ) ) {
		$classes[] = 'virtue-skin-'. str_replace('.css', '', $virtue_premium['skin_stylesheet']);
	}
	if( isset( $virtue_premium['boxed_layout'] ) && $virtue_premium['boxed_layout'] == 'boxed' ) {
		$classes[] = 'boxed';
	} else {
		$classes[] = 'wide';
	}

	return $classes;
}
/*  Browser detection body_class() output
/* ------------------------------------ */ 
function virtue_browser_body_class( $classes ) {
    global $is_IE;
    if($is_IE) {
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $browser = substr( "$browser", 25, 8);
        if ($browser == "MSIE 7.0"  ) {
            $classes[] = 'ie7';
            $classes[] = 'ie';
        } elseif ($browser == "MSIE 6.0" ) {
            $classes[] = 'ie6';
            $classes[] = 'ie';
        } elseif ($browser == "MSIE 8.0" ) {
            $classes[] = 'ie8';
            $classes[] = 'ie';
        } elseif ($browser == "MSIE 9.0" ) {
            $classes[] = 'ie9';
            $classes[] = 'ie';
        } else {
            $classes[] = 'ie';
        }
    }
    else $classes[] = 'not_ie';

    return $classes;
}
add_filter( 'body_class', 'virtue_browser_body_class' );

function kad_icon_list() {
$icons = array('icon-home' => 'icon-home','icon-home2' => 'icon-home2','icon-office' => 'icon-office','icon-newspaper' => 'icon-newspaper','icon-pencil' => 'icon-pencil','icon-pencil2' => 'icon-pencil2','icon-pencil3' => 'icon-pencil3','icon-pencil4' => 'icon-pencil4','icon-quill' => 'icon-quill','icon-quill2' => 'icon-quill2','icon-pen' => 'icon-pen','icon-pen2' => 'icon-pen2','icon-home3' => 'icon-home3','icon-marker' => 'icon-marker','icon-brush' => 'icon-brush','icon-palette' => 'icon-palette','icon-palette2' => 'icon-palette2','icon-droplet' => 'icon-droplet','icon-droplet2' => 'icon-droplet2','icon-paint-format' => 'icon-paint-format','icon-images' => 'icon-images','icon-image' => 'icon-image','icon-image2' => 'icon-image2','icon-images2' => 'icon-images2','icon-camera' => 'icon-camera','icon-camera2' => 'icon-camera2','icon-camera3' => 'icon-camera3', 'icon-camera4' => 'icon-camera4','icon-camera5' => 'icon-camera5','icon-music' => 'icon-music','icon-music2' => 'icon-music2','icon-piano' => 'icon-piano','icon-guitar' => 'icon-guitar','icon-headphones' => 'icon-headphones','icon-play' => 'icon-play','icon-movie' => 'icon-movie','icon-film' => 'icon-film','icon-gamepad' => 'icon-gamepad','icon-pacman' => 'icon-pacman','icon-bullhorn' => 'icon-bullhorn','icon-megaphone' => 'icon-megaphone','icon-connection' => 'icon-connection','icon-radio' => 'icon-radio','icon-mic' => 'icon-mic','icon-book' => 'icon-book','icon-books' => 'icon-books','icon-library' => 'icon-library','icon-file' => 'icon-file','icon-profile' => 'icon-profile','icon-file2' => 'icon-file2','icon-copy' => 'icon-copy','icon-paste' => 'icon-paste','icon-folder' => 'icon-folder','icon-folder-open' => 'icon-folder-open','icon-certificate' => 'icon-certificate','icon-cc' => 'icon-cc','icon-tag' => 'icon-tag','icon-tag2' => 'icon-tag2','icon-tags' => 'icon-tags','icon-cart' => 'icon-cart','icon-cart2' => 'icon-cart2','icon-cart3' => 'icon-cart3','icon-cart4' => 'icon-cart4','icon-basket' => 'icon-basket','icon-basket2' => 'icon-basket2','icon-bag' => 'icon-bag','icon-bag2' => 'icon-bag2','icon-bag3' => 'icon-bag3','icon-coin' => 'icon-coin','icon-credit' => 'icon-credit','icon-support' => 'icon-support','icon-phone' => 'icon-phone','icon-address-book' => 'icon-address-book','icon-envelope' => 'icon-envelope','icon-mail-send' => 'icon-mail-send','icon-envelope2' => 'icon-envelope2','icon-pushpin' => 'icon-pushpin','icon-location' => 'icon-location','icon-location2' => 'icon-location2','icon-compass' => 'icon-compass','icon-compass2' => 'icon-compass2','icon-map' => 'icon-map','icon-map2' => 'icon-map2','icon-direction' => 'icon-direction','icon-clock' => 'icon-clock','icon-clock2' => 'icon-clock2','icon-watch' => 'icon-watch','icon-alarm' => 'icon-alarm','icon-bell' => 'icon-bell','icon-stopwatch' => 'icon-stopwatch','icon-calendar' => 'icon-calendar','icon-calendar2' => 'icon-calendar2','icon-print' => 'icon-print','icon-mouse' => 'icon-mouse','icon-screen' => 'icon-screen','icon-screen2' => 'icon-screen2','icon-laptop' => 'icon-laptop','icon-mobile' => 'icon-mobile','icon-mobile2' => 'icon-mobile2','icon-tablet' => 'icon-tablet','icon-cabinet' => 'icon-cabinet','icon-drawer' => 'icon-drawer','icon-drawer2' => 'icon-drawer2','icon-box' => 'icon-box','icon-box-add' => 'icon-box-add','icon-box-remove' => 'icon-box-remove','icon-cd' => 'icon-cd','icon-storage' => 'icon-storage','icon-undo' => 'icon-undo','icon-redo' => 'icon-redo','icon-rotate' => 'icon-rotate','icon-rotate2' => 'icon-rotate2','icon-undo2' => 'icon-undo2','icon-redo2' => 'icon-redo2','icon-forward' => 'icon-forward','icon-reply' => 'icon-reply','icon-bubble' => 'icon-bubble','icon-bubbles' => 'icon-bubbles','icon-bubbles2' => 'icon-bubbles2','icon-bubbles3' => 'icon-bubbles3','icon-user2' => 'icon-user2','icon-users' => 'icon-users','icon-users2' => 'icon-users2','icon-users3' => 'icon-users3','icon-vcard' => 'icon-vcard','icon-tshirt' => 'icon-tshirt','icon-bubble-notification' => 'icon-bubble-notification','icon-bubble2' => 'icon-bubble2','icon-bubble3' => 'icon-bubble3','icon-quotes-left' => 'icon-quotes-left','icon-quotes-right' => 'icon-quotes-right','icon-busy' => 'icon-busy','icon-spinner' => 'icon-spinner','icon-spinner2' => 'icon-spinner2','icon-search' => 'icon-search','icon-search2' => 'icon-search2','icon-zoom-in' => 'icon-zoom-in','icon-zoom-out' => 'icon-zoom-out','icon-expand' => 'icon-expand','icon-contract' => 'icon-contract','icon-key2' => 'icon-key2','icon-key22' => 'icon-key22','icon-keyhole' => 'icon-keyhole','icon-lock' => 'icon-lock','icon-wrench' => 'icon-wrench','icon-settings' => 'icon-settings','icon-equalizer' => 'icon-equalizer','icon-equalizer2' => 'icon-equalizer2','icon-equalizer3' => 'icon-equalizer3','icon-cog' => 'icon-cog','icon-cogs' => 'icon-cogs','icon-tools' => 'icon-tools','icon-screwdriver' => 'icon-screwdriver','icon-wand' => 'icon-wand','icon-aid' => 'icon-aid','icon-bug' => 'icon-bug','icon-inject' => 'icon-inject','icon-construction' => 'icon-construction','icon-pie' => 'icon-pie','icon-stats' => 'icon-stats','icon-stats2' => 'icon-stats2','icon-stats3' => 'icon-stats3','icon-bars' => 'icon-bars','icon-bars2' => 'icon-bars2','icon-bars3' => 'icon-bars3','icon-gift' => 'icon-gift','icon-balloon' => 'icon-balloon','icon-stats-up' => 'icon-stats-up','icon-gift2' => 'icon-gift2','icon-medal' => 'icon-medal','icon-crown' => 'icon-crown','icon-trophy' => 'icon-trophy','icon-glass' => 'icon-glass','icon-glass2' => 'icon-glass2','icon-bottle' => 'icon-bottle','icon-food' => 'icon-food','icon-food2' => 'icon-food2','icon-cup' => 'icon-cup','icon-leaf' => 'icon-leaf','icon-leaf2' => 'icon-leaf2','icon-apple-fruit' => 'icon-apple-fruit','icon-tree' => 'icon-tree','icon-paw' => 'icon-paw','icon-steps' => 'icon-steps','icon-flower' => 'icon-flower','icon-rocket' => 'icon-rocket','icon-meter' => 'icon-meter','icon-meter-fast' => 'icon-meter-fast','icon-mug' => 'icon-mug','icon-dashboard' => 'icon-dashboard','icon-hammer' => 'icon-hammer','icon-fire' => 'icon-fire','icon-bomb' => 'icon-bomb','icon-lab' => 'icon-lab','icon-atom' => 'icon-atom','icon-magnet' => 'icon-magnet','icon-dumbbell' => 'icon-dumbbell','icon-lamp' => 'icon-lamp','icon-lamp2' => 'icon-lamp2','icon-lamp3' => 'icon-lamp3','icon-lamp4' => 'icon-lamp4','icon-remove' => 'icon-remove','icon-remove2' => 'icon-remove2','icon-remove3' => 'icon-remove3','icon-briefcase' => 'icon-briefcase','icon-briefcase2' => 'icon-briefcase2','icon-briefcase3' => 'icon-briefcase3','icon-airplane' => 'icon-airplane','icon-airplane2' => 'icon-airplane2','icon-paper-plane' => 'icon-paper-plane','icon-car' => 'icon-car','icon-gas-pump' => 'icon-gas-pump','icon-bus' => 'icon-bus','icon-truck' => 'icon-truck','icon-bike' => 'icon-bike','icon-train' => 'icon-train','icon-boat' => 'icon-boat','icon-cube' => 'icon-cube','icon-cube4' => 'icon-cube4','icon-cylinder' => 'icon-cylinder','icon-puzzle' => 'icon-puzzle','icon-puzzle2' => 'icon-puzzle2','icon-glasses' => 'icon-glasses','icon-glasses2' => 'icon-glasses2','icon-sun-glasses' => 'icon-sun-glasses','icon-accessibility' => 'icon-accessibility','icon-accessibility2' => 'icon-accessibility2','icon-brain' => 'icon-brain','icon-target' => 'icon-target','icon-shield' => 'icon-shield','icon-shield2' => 'icon-shield2','icon-soccer' => 'icon-soccer','icon-football' => 'icon-football','icon-baseball' => 'icon-baseball','icon-basketball' => 'icon-basketball','icon-golf' => 'icon-golf','icon-hockey' => 'icon-hockey','icon-racing' => 'icon-racing','icon-eight-ball' => 'icon-eight-ball','icon-bowling-ball' => 'icon-bowling-ball','icon-bowling' => 'icon-bowling','icon-lightning' => 'icon-lightning','icon-power' => 'icon-power','icon-switch' => 'icon-switch','icon-power-cord' => 'icon-power-cord','icon-clipboard' => 'icon-clipboard','icon-signup' => 'icon-signup','icon-clipboard2' => 'icon-clipboard2','icon-clipboard3' => 'icon-clipboard3','icon-grid' => 'icon-grid','icon-grid2' => 'icon-grid2','icon-grid3' => 'icon-grid3','icon-grid4' => 'icon-grid4','icon-grid5' => 'icon-grid5','icon-menu' => 'icon-menu','icon-menu2' => 'icon-menu2','icon-menu3' => 'icon-menu3','icon-menu4' => 'icon-menu4','icon-menu5' => 'icon-menu5','icon-menu6' => 'icon-menu6','icon-cloud' => 'icon-cloud','icon-download' => 'icon-download','icon-upload' => 'icon-upload','icon-globe' => 'icon-globe','icon-earth' => 'icon-earth','icon-network' => 'icon-network','icon-link' => 'icon-link','icon-link2' => 'icon-link2','icon-link3' => 'icon-link3','icon-cloud-download' => 'icon-cloud-download','icon-cloud-upload' => 'icon-cloud-upload','icon-link4' => 'icon-link4','icon-anchor' => 'icon-anchor','icon-flag' => 'icon-flag','icon-flag2' => 'icon-flag2','icon-flag3' => 'icon-flag3','icon-attachment' => 'icon-attachment','icon-attachment2' => 'icon-attachment2','icon-eye' => 'icon-eye','icon-bookmark' => 'icon-bookmark','icon-bookmarks' => 'icon-bookmarks','icon-spotlight' => 'icon-spotlight','icon-temperature' => 'icon-temperature','icon-weather-lightning' => 'icon-weather-lightning','icon-weather-rain' => 'icon-weather-rain','icon-weather-snow' => 'icon-weather-snow','icon-windy' => 'icon-windy','icon-snowflake' => 'icon-snowflake','icon-sun' => 'icon-sun','icon-eye-blocked' => 'icon-eye-blocked','icon-moon' => 'icon-moon','icon-bed' => 'icon-bed','icon-star' => 'icon-star','icon-star2' => 'icon-star2','icon-star3' => 'icon-star3','icon-heart' => 'icon-heart','icon-heart2' => 'icon-heart2','icon-heart-broken' => 'icon-heart-broken','icon-thumbs-up' => 'icon-thumbs-up','icon-thumbs-down' => 'icon-thumbs-down','icon-man' => 'icon-man','icon-woman' => 'icon-woman','icon-people' => 'icon-people','icon-happy' => 'icon-happy','icon-happy2' => 'icon-happy2','icon-smiley' => 'icon-smiley','icon-smiley2' => 'icon-smiley2','icon-tongue' => 'icon-tongue','icon-tongue2' => 'icon-tongue2','icon-sad' => 'icon-sad','icon-sad2' => 'icon-sad2','icon-wink' => 'icon-wink','icon-wink2' => 'icon-wink2','icon-grin' => 'icon-grin','icon-grin2' => 'icon-grin2','icon-cool' => 'icon-cool','icon-cool2' => 'icon-cool2','icon-shocked' => 'icon-shocked','icon-shocked2' => 'icon-shocked2','icon-neutral' => 'icon-neutral','icon-neutral2' => 'icon-neutral2','icon-hand' => 'icon-hand','icon-stack-picture' => 'icon-stack-picture','icon-stack-list' => 'icon-stack-list','icon-stack-clubs' => 'icon-stack-clubs','icon-stack-spades' => 'icon-stack-spades','icon-stack-hearts' => 'icon-stack-hearts','icon-stack-diamonds' => 'icon-stack-diamonds','icon-stack-user' => 'icon-stack-user','icon-stack-music' => 'icon-stack-music','icon-angry' => 'icon-angry','icon-angry2' => 'icon-angry2','icon-evil' => 'icon-evil','icon-evil2' => 'icon-evil2','icon-confused' => 'icon-confused','icon-confused2' => 'icon-confused2','icon-wondering' => 'icon-wondering','icon-wondering2' => 'icon-wondering2','icon-cursor' => 'icon-cursor','icon-move' => 'icon-move','icon-warning' => 'icon-warning','icon-warning2' => 'icon-warning2','icon-notification' => 'icon-notification','icon-notification2' => 'icon-notification2','icon-question' => 'icon-question','icon-question2' => 'icon-question2','icon-question3' => 'icon-question3','icon-plus-circle' => 'icon-plus-circle','icon-plus-circle2' => 'icon-plus-circle2','icon-minus-circle' => 'icon-minus-circle','icon-minus-circle2' => 'icon-minus-circle2','icon-info' => 'icon-info','icon-info2' => 'icon-info2','icon-cancel-circle' => 'icon-cancel-circle','icon-cancel-circle2' => 'icon-cancel-circle2','icon-checkmark-circle' => 'icon-checkmark-circle','icon-checkmark-circle2' => 'icon-checkmark-circle2','icon-close' => 'icon-close','icon-close2' => 'icon-close2','icon-checkmark' => 'icon-checkmark','icon-checkmark2' => 'icon-checkmark2','icon-checkmark3' => 'icon-checkmark3','icon-checkmark4' => 'icon-checkmark4','icon-minus' => 'icon-minus','icon-plus' => 'icon-plus','icon-bed2' => 'icon-bed2','icon-fan' => 'icon-fan','icon-umbrella' => 'icon-umbrella','icon-play2' => 'icon-play2','icon-pause' => 'icon-pause','icon-stop' => 'icon-stop','icon-backward' => 'icon-backward','icon-forward2' => 'icon-forward2','icon-play3' => 'icon-play3','icon-pause2' => 'icon-pause2','icon-stop2' => 'icon-stop2','icon-backward2' => 'icon-backward2','icon-forward3' => 'icon-forward3','icon-first' => 'icon-first','icon-last' => 'icon-last','icon-previous' => 'icon-previous','icon-next' => 'icon-next','icon-eject' => 'icon-eject','icon-volume-high' => 'icon-volume-high','icon-volume-medium' => 'icon-volume-medium','icon-volume-low' => 'icon-volume-low','icon-volume-mute' => 'icon-volume-mute','icon-arrow-up' => 'icon-arrow-up','icon-arrow-right' => 'icon-arrow-right','icon-arrow-down' => 'icon-arrow-down','icon-arrow-left' => 'icon-arrow-left','icon-arrow-up2' => 'icon-arrow-up2','icon-arrow-right2' => 'icon-arrow-right2','icon-arrow-down2' => 'icon-arrow-down2','icon-arrow-left2' => 'icon-arrow-left2','icon-arrow-up-left' => 'icon-arrow-up-left','icon-arrow-up3' => 'icon-arrow-up3','icon-arrow-up-right' => 'icon-arrow-up-right','icon-arrow-right3' => 'icon-arrow-right3','icon-arrow-down-right' => 'icon-arrow-down-right','icon-arrow-down3' => 'icon-arrow-down3','icon-arrow-down-left' => 'icon-arrow-down-left','icon-arrow-left3' => 'icon-arrow-left3','icon-arrow-up4' => 'icon-arrow-up4','icon-arrow-right4' => 'icon-arrow-right4','icon-arrow-down4' => 'icon-arrow-down4','icon-arrow-left4' => 'icon-arrow-left4','icon-arrow-up5' => 'icon-arrow-up5','icon-arrow-right5' => 'icon-arrow-right5','icon-arrow-bottom' => 'icon-arrow-bottom','icon-arrow-left5' => 'icon-arrow-left5','icon-arrow-up6' => 'icon-arrow-up6','icon-arrow-right6' => 'icon-arrow-right6','icon-arrow-down5' => 'icon-arrow-down5','icon-arrow-left6' => 'icon-arrow-left6','icon-transmission' => 'icon-transmission','icon-sort' => 'icon-sort','icon-loop' => 'icon-loop','icon-loop2' => 'icon-loop2','icon-checkbox-checked' => 'icon-checkbox-checked','icon-checkbox' => 'icon-checkbox','icon-checkbox-checked2' => 'icon-checkbox-checked2','icon-crop' => 'icon-crop','icon-vector' => 'icon-vector','icon-rulers' => 'icon-rulers','icon-scissors' => 'icon-scissors','icon-filter' => 'icon-filter','icon-font' => 'icon-font','icon-font-size' => 'icon-font-size','icon-text-height' => 'icon-text-height','icon-text-width' => 'icon-text-width','icon-height' => 'icon-height','icon-width' => 'icon-width','icon-page-break' => 'icon-page-break','icon-page-break2' => 'icon-page-break2','icon-new-tab' => 'icon-new-tab','icon-new-tab2' => 'icon-new-tab2','icon-embed' => 'icon-embed','icon-code' => 'icon-code','icon-mail' => 'icon-mail','icon-mail2' => 'icon-mail2','icon-mail3' => 'icon-mail3','icon-table' => 'icon-table','icon-google-plus' => 'icon-google-plus','icon-google-plus2' => 'icon-google-plus2','icon-google-plus3' => 'icon-google-plus3', 'icon-google-plus4' => 'icon-google-plus4', 'icon-google-plus-square' => 'icon-google-plus-square', 'icon-google' => 'icon-google', 'icon-facebook' => 'icon-facebook','icon-facebook2' => 'icon-facebook2','icon-facebook3' => 'icon-facebook3','icon-instagram' => 'icon-instagram','icon-twitter' => 'icon-twitter','icon-twitter2' => 'icon-twitter2','icon-feed' => 'icon-feed','icon-feed2' => 'icon-feed2','icon-youtube' => 'icon-youtube','icon-youtube2' => 'icon-youtube2','icon-vimeo' => 'icon-vimeo','icon-vimeo2' => 'icon-vimeo2','icon-lanyrd' => 'icon-lanyrd','icon-flickr' => 'icon-flickr','icon-flickr2' => 'icon-flickr2','icon-picassa' => 'icon-picassa','icon-picassa2' => 'icon-picassa2','icon-dribbble' => 'icon-dribbble','icon-dribbble2' => 'icon-dribbble2','icon-forrst' => 'icon-forrst','icon-forrst2' => 'icon-forrst2','icon-deviantart' => 'icon-deviantart','icon-deviantart2' => 'icon-deviantart2','icon-github' => 'icon-github','icon-github2' => 'icon-github2','icon-github3' => 'icon-github3','icon-wordpress' => 'icon-wordpress','icon-blogger' => 'icon-blogger','icon-blogger2' => 'icon-blogger2','icon-tumblr' => 'icon-tumblr','icon-tumblr2' => 'icon-tumblr2','icon-apple' => 'icon-apple','icon-android' => 'icon-android','icon-windows' => 'icon-windows','icon-windows8' => 'icon-windows8','icon-skype' => 'icon-skype','icon-linkedin' => 'icon-linkedin','icon-lastfm' => 'icon-lastfm','icon-lastfm2' => 'icon-lastfm2','icon-stumbleupon' => 'icon-stumbleupon','icon-stumbleupon2' => 'icon-stumbleupon2','icon-pinterest' => 'icon-pinterest','icon-pinterest2' => 'icon-pinterest2','icon-xing2' => 'icon-xing2','icon-paypal' => 'icon-paypal','icon-html5' => 'icon-html5','icon-css3' => 'icon-css3','icon-file-zip' => 'icon-file-zip','icon-file-xml' => 'icon-file-xml','icon-file-pdf' => 'icon-file-pdf','icon-file-word' => 'icon-file-word','icon-file-excel' => 'icon-file-excel','icon-king' => 'icon-king','icon-queen' => 'icon-queen','icon-rock' => 'icon-rock','icon-knight' => 'icon-knight','icon-github4' => 'icon-github4','icon-steam' => 'icon-steam','icon-steam2' => 'icon-steam2','icon-yahoo' => 'icon-yahoo','icon-flickr3' => 'icon-flickr3','icon-google-drive' => 'icon-google-drive','icon-chrome' => 'icon-chrome','icon-firefox' => 'icon-firefox','icon-IE' => 'icon-IE','icon-opera' => 'icon-opera','icon-safari' => 'icon-safari','icon-IcoMoon' => 'icon-IcoMoon','icon-html52' => 'icon-html52', 'icon-xing' => 'icon-xing', 'icon-vk' => 'icon-vk', 'icon-viadeo' => 'icon-viadeo', 'icon-user' => 'icon-user', 'icon-key' => 'icon-key', 'icon-gears' => 'icon-gears', 'icon-group' => 'icon-group', 'icon-euro' => 'icon-euro', 'icon-gbp' => 'icon-gbp', 'icon-dollar' => 'icon-dollar', 'icon-rupee' => 'icon-rupee', 'icon-cny' => 'icon-cny', 'icon-ruble' => 'icon-ruble', 'icon-won' => 'icon-won', 'icon-bitcoin' => 'icon-bitcoin','icon-enter' => 'icon-enter', 'icon-exit' => 'icon-exit', 'icon-sign-out' => 'icon-sign-out', 'icon-sign-in' => 'icon-sign-in', 'icon-soundcloud' => 'icon-soundcloud', 'icon-soundcloud2' => 'icon-soundcloud2','icon-yelp' => 'icon-yelp','icon-amazon' => 'icon-amazon','icon-500px' => 'icon-500px','icon-500px2' => 'icon-500px2','icon-500px-with-circle' => 'icon-500px-with-circle','icon-px' => 'icon-px','icon-cc-stripe' => 'icon-cc-stripe','icon-cc-paypal' => 'icon-cc-paypal','icon-cc-amex' => 'icon-cc-amex','icon-cc-discover' => 'icon-cc-discover','icon-cc-mastercard' => 'icon-cc-mastercard','icon-cc-visa' => 'icon-cc-visa','icon-deviantart3' => 'icon-deviantart3','icon-etsy' => 'icon-etsy','icon-tripadvisor' => 'icon-tripadvisor','icon-hand-grab-o' => 'icon-hand-grab-o','icon-hand-rock-o' => 'icon-hand-rock-o','icon-hand-paper-o' => 'icon-hand-paper-o','icon-hand-stop-o' => 'icon-hand-stop-o','icon-hand-scissors-o' => 'icon-hand-scissors-o','icon-hand-lizard-o' => 'icon-hand-lizard-o','icon-hand-spock-o' => 'icon-hand-spock-o','icon-hand-pointer-o' => 'icon-hand-pointer-o','icon-hand-peace-o' => 'icon-hand-peace-o','icon-male' => 'icon-male','icon-female' => 'icon-female','icon-behance' => 'icon-behance','icon-behance2' => 'icon-behance2','icon-fa-snapchat' => 'icon-fa-snapchat','icon-iconPeriscope' => 'icon-iconPeriscope');

return apply_filters( 'kadence_icon_list', $icons );

}
if (!isset($content_width)) { $content_width = 1140; }

function virtue_carousel_columns( $columns, $sidebar = false, $maxwidth = '1170' ) {
    if( empty( $columns ) ) {
        $columns = 4;
    }
    $cc = array();
    if( $columns == 6 ) {
        $cc['md'] = 6; 
        $cc['sm'] = 5; 
        $cc['xs'] = 4;
        $cc['ss'] = 3;
    } else if($columns == 5) {
        $cc['md'] = 5; 
        $cc['sm'] = 4; 
        $cc['xs'] = 3;
        $cc['ss'] = 2;
    }  else if($columns == 4) {
        $cc['md'] = 4; 
        $cc['sm'] = 3; 
        $cc['xs'] = 2;
        $cc['ss'] = 1;
    } else if($columns == 3) {
        $cc['md'] = 3; 
        $cc['sm'] = 2; 
        $cc['xs'] = 2;
        $cc['ss'] = 1;
    } else if($columns == 2) {
        $cc['md'] = 2; 
        $cc['sm'] = 2; 
        $cc['xs'] = 1;
        $cc['ss'] = 1;
    } else {
        $cc['md'] = 1; 
        $cc['sm'] = 1; 
        $cc['xs'] = 1;
        $cc['ss'] = 1;
    }
    if($sidebar) {
    	if($maxwidth == 'none' || $maxwidth == '1770') {
	        if($cc['md'] == 1) {
	            $cc['xxl'] = 1;
	            $cc['xl'] = 1;
	        } else {
	            $cc['xxl'] = ($cc['md'] + 1);
	            $cc['xl'] = ($cc['md']);
	        }
	    } else if($maxwidth == '1470') {
	         if($cc['md'] == 1) {
	            $cc['xxl'] = 1;
	            $cc['xl'] = 1;
	        } else {
	            $cc['xxl'] = ($cc['md']);
	            $cc['xl'] = ($cc['md']);
	        }
	    } else {
	        $cc['xxl'] = $cc['md'];
	        $cc['xl'] = $cc['md'];
	    }
    } else {
	    if($maxwidth == 'none' || $maxwidth == '1770') {
	        if($cc['md'] == 1) {
	            $cc['xxl'] = 1;
	            $cc['xl'] = 1;
	        } else {
	            $cc['xxl'] = ($cc['md'] + 2);
	            $cc['xl'] = ($cc['md'] + 1);
	        }
	    } else if($maxwidth == '1470') {
	         if($cc['md'] == 1) {
	            $cc['xxl'] = 1;
	            $cc['xl'] = 1;
	        } else {
	            $cc['xxl'] = ($cc['md'] + 1);
	            $cc['xl'] = ($cc['md'] + 1);
	        }
	    } else {
	        $cc['xxl'] = $cc['md'];
	        $cc['xl'] = $cc['md'];
	    }
	} 

    return apply_filters( 'virtue_carousel_columns', $cc, $columns, $sidebar );
}
