<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'virtue_woocommerce_archive_hooks', 5 );
function virtue_woocommerce_archive_hooks() {
	add_action( 'woocommerce_after_shop_loop', 'virtue_wc_infinite_loader', 5 );
	function virtue_wc_infinite_loader() {
		echo '<div class="scroller-status"><div class="loader-ellips infinite-scroll-request"><span class="loader-ellips__dot"></span><span class="loader-ellips__dot"></span><span class="loader-ellips__dot"></span><span class="loader-ellips__dot"></span></div></div>';
	}
	// Remove Results Count ( re-add in the page header )
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
	// Remove catalog_ordering ( re-add in the page header )
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	// Remove content_wrapper ( add virtues later )
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	// Remove breadcrumb
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
	// Remove content_wrapper ( add virtues later )
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	// Remove sidebar virtue adds it's own.
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

	// Add filters for archive short descriptions. (Helps with single column layouts)
	add_filter( 'archive_woocommerce_short_description', 'wptexturize', 10);
	add_filter( 'archive_woocommerce_short_description', 'wpautop', 10);
	add_filter( 'archive_woocommerce_short_description', 'shortcode_unautop', 10);
	add_filter( 'archive_woocommerce_short_description', 'do_shortcode', 11 );

	// Set the number of columns for cross sells to 3
	add_filter( 'woocommerce_cross_sells_columns', 'virtue_woocommerce_cross_sells_columns', 10, 1 );
	function virtue_woocommerce_cross_sells_columns( $columns ) {
		return 3;
	}
	// Limit the number of cross sells displayed to a maximum of 3
	add_filter( 'woocommerce_cross_sells_total', 'virtue_woocommerce_cross_sells_total', 10, 1 );
	function virtue_woocommerce_cross_sells_total( $limit ) {
		return 3;
	}

	// Number of products per page
	add_filter( 'loop_shop_per_page', 'virtue_products_per_page' );
	function virtue_products_per_page() {
		global $virtue_premium;
		if ( isset( $virtue_premium['products_per_page'] ) && ! empty( $virtue_premium['products_per_page'] ) ) {
			return $virtue_premium['products_per_page'];
		}
	}

	// Shop Columns
    add_filter('loop_shop_columns', 'virtue_loop_columns');
	function virtue_loop_columns() {
		global $virtue_premium;
		if ( isset( $virtue_premium['product_shop_layout'] ) && ! empty( $virtue_premium['product_shop_layout'] ) ) {
			return $virtue_premium['product_shop_layout'];
		} else {
			return 4;
		}
    }

    /*
	* woocommerce_shop_loop_item_title
	*/

    function virtue_woocommerce_archive_content_wrap_start() {
    	echo '<div class="details_product_item">';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_content_wrap_start', 5 );
	function virtue_woocommerce_archive_title_wrap_start() {
		echo '<div class="product_details">';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_wrap_start', 6 );
	function virtue_woocommerce_archive_title_link_start() {
		echo '<a href="'.esc_url( get_the_permalink() ).'" class="product_item_link product_title_link">';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_link_start', 7 );

    // Remove Woo Archive Titles
    remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
    // Add Theme archive titles
    add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_template_loop_product_title', 10);
    function virtue_woocommerce_template_loop_product_title() {
		echo '<h5>'.get_the_title().'</h5>';
    }

    function virtue_woocommerce_archive_title_link_end() {
		echo '</a>';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_link_end', 15 );

	function virtue_woocommerce_archive_excerpt() {
		global $virtue_premium, $post;
		if(isset($virtue_premium['shop_excerpt']) && $virtue_premium['shop_excerpt'] == 0) {
			echo '<div class="product_excerpt">';
				if ($post->post_excerpt){
					echo apply_filters( 'archive_woocommerce_short_description', $post->post_excerpt );
				} else {
					the_excerpt();
				}
			echo '</div>';
		}
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_excerpt', 20 );
	function virtue_woocommerce_archive_title_wrap_end() {
    	echo '</div>';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_wrap_end', 50 );

    remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );


	function virtue_after_shop_loop_wrap_end() {
    	echo '</div>';
	}
	add_action( 'woocommerce_after_shop_loop_item', 'virtue_after_shop_loop_wrap_end', 50 );

    remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
    add_action( 'woocommerce_shop_loop_subcategory_title', 'kt_woocommerce_template_loop_category_title', 10 );

	function kt_woocommerce_template_loop_category_title( $category ) {
		?>
		<h5>
		<?php echo $category->name;
			if ( $category->count > 0 ) {
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			}
		?>
		</h5>
		<?php
	}

    function kt_add_class_woocommerce_loop_add_to_cart_link($array, $product) {
      $array['class'] .= ' kad-btn headerfont kad_add_to_cart';
      return $array;
    }   
    add_filter('woocommerce_loop_add_to_cart_args', 'kt_add_class_woocommerce_loop_add_to_cart_link', 10, 2);

    remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
    remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
    add_action( 'woocommerce_before_subcategory', 'kt_woocommerce_template_loop_category_link_open', 10 );
    add_action( 'woocommerce_after_subcategory', 'kt_woocommerce_template_loop_category_link_close', 10 );

    function kt_woocommerce_template_loop_category_link_open( $category ) {
        echo '<a href="' . get_term_link( $category->slug, 'product_cat' ) . '">';
    }
    function kt_woocommerce_template_loop_category_link_close() {
        echo '</a>';
    }

    add_action( 'woocommerce_before_main_content', 'kt_woo_main_wrap_content_open', 10 );
    function kt_woo_main_wrap_content_open() {
      echo '<div id="content" class="container"><div class="row"><div class="main '.virtue_main_class().'" role="main">';
    }
    add_action( 'woocommerce_after_main_content', 'kt_woo_main_wrap_content_close', 20 );
    function kt_woo_main_wrap_content_close() {
      echo '</div>';
    }

    add_action( 'woocommerce_above_main_content', 'virtue_woocommerce_page_title_container', 20 );
    function virtue_woocommerce_page_title_container() {
    	if( is_shop() ){
    		$id = get_option( 'woocommerce_shop_page_id' ); 
    		$pagetitle = get_post_meta( $id, '_kad_show_page_title', true );
    	} else {
	    	if(get_queried_object()) {
				$cat_term_id = get_queried_object()->term_id;
				$meta = get_option('kad_cat_title');
				if (empty($meta)) $meta = array();
				if (!is_array($meta)) $meta = (array) $meta;
				$meta = isset($meta[$cat_term_id]) ? $meta[$cat_term_id] : array();
				if(isset($meta['archive_show_title'])) {
					$pagetitle = $meta['archive_show_title'];
				} else {
					$pagetitle = 'default';
				}
			}
		}
		if(empty($pagetitle) || $pagetitle == 'default') {
			if(isset($virtue_premium['page_title_show']) && $virtue_premium['page_title_show'] == '0') {
				// Do nothing
			} else {
				get_template_part('templates/woo', 'page-title');
			}
		} elseif($pagetitle == 'show') {
			get_template_part('templates/woo', 'page-title');
		} else {
			// do nothing
		}
    }
    add_action( 'kt_woocommerce_page_title_left', 'kt_woocommerce_page_title_output', 10 );
    function kt_woocommerce_page_title_output() { ?>
      <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
    <?php } 

    add_action( 'kt_woocommerce_page_title_left', 'woocommerce_result_count', 20 );

    add_action( 'kt_woocommerce_page_title_right', 'kt_woocommerce_page_title_toggle', 20 );
    function kt_woocommerce_page_title_toggle() { 
        get_template_part('templates/woo', 'toggle');
    } 
    add_action( 'kt_woocommerce_page_title_right', 'woocommerce_catalog_ordering', 30 );
    add_action( 'kt_woocommerce_page_title_right', 'kt_woocommerce_page_title_shortcode', 40 );
    function kt_woocommerce_page_title_shortcode() {
		if( kadence_display_shop_breadcrumbs() ) { 
			kadence_breadcrumbs();
		}
    }

    add_action( 'woocommerce_before_shop_loop', 'virtue_woo_cat_loop', 60 );
	function virtue_woo_cat_loop() {
		if ( version_compare( WC_VERSION, '3.3', '<' ) ) {
			if ( ! is_search() ) {
				echo '<div class="clearfix '.kadence_category_layout_css().' rowtight product_category_padding">';
					woocommerce_product_subcategories(); 
				echo '</div>';
			}
		}
	}
	add_action( 'woocommerce_before_shop_loop', 'virtue_woo_loop_filter', 40 );
	function virtue_woo_loop_filter() {
		global $virtue_premium, $wp_query;

		if ( 1 === $wp_query->found_posts || ! woocommerce_products_will_display() ) {
			return;
		}
		if( is_shop() && ! is_search() ){
			if( isset( $virtue_premium['shop_filter'] ) && 1 == $virtue_premium['shop_filter'] ) {
				echo '<div class="kad-shop-filter">';
			  			virtue_iso_filter('product_cat', null);
				echo '</div>';
			}
		} else if( is_product_category() ) {
			if( isset( $virtue_premium['cat_filter'] ) && 1 == $virtue_premium['cat_filter'] ) {
				$cat_obj = $wp_query->get_queried_object();
				$product_cat_ID  = $cat_obj->term_id;

				$children = get_terms( 'product_cat', array(
					'parent'    => $product_cat_ID,
					'hide_empty' => false
				) );
				if ( $children ) {
					$termtypes = array( 'child_of' => $product_cat_ID);
					echo '<div class="kad-shop-filter">';
						virtue_iso_filter('product_cat', $termtypes);
					echo '</div>';
				}
			}
		}
	}

}

function virtue_iso_filter($tax, $termtypes) {
	global $virtue_premium;
	echo '<div id="options" class="kt-filter-options clearfix">';	
		if(!empty($virtue_premium['filter_all_text'])) {
			$alltext = $virtue_premium['filter_all_text'];
		} else {
			$alltext = __('All', 'virtue');
		}
		if(!empty($virtue_premium['shop_filter_text'])) {
			$filter_text = $virtue_premium['shop_filter_text'];
		} else {
			$filter_text = __('Filter Products', 'virtue');
		}
		$categories = get_terms($tax, $termtypes);
		$count 		= count($categories);
			echo '<a class="filter-trigger headerfont" data-toggle="collapse" data-target=".filter-collapse"><i class="icon-tags"></i> '.esc_html($filter_text).'</a>';
			echo '<ul id="filters" class="clearfix filter-set option-set filter-collapse">';
				echo '<li class="postclass"><a href="#" data-filter="*" title="'.esc_attr($alltext).'" class="selected"><h5>'.esc_html($alltext).'</h5><div class="arrow-up"></div></a></li>';
				if ( $count > 0 ){
					foreach ($categories as $category){ 
						$term_slug = strtolower($category->slug);
						echo '<li class="postclass kt-data-filter-'.esc_attr($term_slug).'"><a href="#" data-filter=".'.esc_attr($term_slug).'"><h5>'.esc_html($category->name).'</h5><div class="arrow-up"></div></a></li>';
					}
			 	}
			echo "</ul>"; 
	echo '</div>';
}

function virtue_get_product_iso_terms_class( $id, $term_slug ) {
	$terms = get_the_terms( $id, $term_slug );
	if ( $terms && ! is_wp_error( $terms ) ) { 
		$links = array();
		foreach ( $terms as $term ) {
			$links[] = $term->slug;
		}
		$links = preg_replace("/[^a-zA-Z 0-9]+/", " ", $links);
		$links = str_replace(' ', '-', $links);	
		$tax = join( " ", $links );		
	} else {	
		$tax = '';	
	}
	return strtolower( $tax );
}

/*
*
* WOO ARCHIVE IMAGES
*
*/
function kad_woo_archive_image_output() {

    function kad_woocommerce_image_link_open() {
        echo  '<a href="'.get_the_permalink().'" class="product_item_link product_img_link">';
    }
    add_action( 'woocommerce_before_shop_loop_item_title', 'kad_woocommerce_image_link_open', 5 );
    function kad_woocommerce_image_link_close() {
        echo '</a>';
    }
    add_action( 'woocommerce_before_shop_loop_item_title', 'kad_woocommerce_image_link_close', 50 );

    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
    add_action( 'woocommerce_before_shop_loop_item_title', 'kt_woocommerce_template_loop_product_thumbnail', 10 );
    function kt_woocommerce_template_loop_product_thumbnail() {
        global $product, $woocommerce_loop, $virtue_premium, $post;

        $product_column = $woocommerce_loop['columns'];
        if ( '1' == $product_column ) {
			$productimgwidth = 300;
		} else if ( '2' == $product_column ) {
			$productimgwidth = 300;
		} else if ( '3' == $product_column ){
			$productimgwidth = 400;
		} else if ( '6' == $product_column ){
			$productimgwidth = 240;
		} else if ( '5' == $product_column ){
			$productimgwidth = 240;
		} else {
			$productimgwidth = 300;
		}
		if ( isset( $virtue_premium[ 'product_img_resize' ] ) && 0 == $virtue_premium[ 'product_img_resize' ] ) {
			$resizeimage = 0;
		} else {
			$resizeimage = 1;
			if ( isset($virtue_premium[ 'shop_img_ratio' ] ) ) {
				$img_ratio = $virtue_premium['shop_img_ratio'];
			} else {
				$img_ratio = 'square';
			}
			if($img_ratio == 'portrait') {
				$tempproductimgheight = $productimgwidth * 1.35;
				$productimgheight = floor($tempproductimgheight);
			} else if($img_ratio == 'landscape') {
				$tempproductimgheight = $productimgwidth / 1.35;
				$productimgheight = floor($tempproductimgheight);
			} else if($img_ratio == 'widelandscape') {
				$tempproductimgheight = $productimgwidth / 2;
				$productimgheight = floor($tempproductimgheight);
			} else {
				$productimgheight = $productimgwidth;
			}
		}
		if ( isset( $virtue_premium[ 'product_img_flip' ] ) && 0 == $virtue_premium[ 'product_img_flip' ] ) {
			$productimgflip = 0;
		} else {
			$productimgflip = 1;
		}

        if ( 1 == $productimgflip && 1 == $resizeimage ) { 
            // Check for an image to flip to first //
			if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
				$attachment_ids = $product->get_gallery_image_ids();
			} else {
				$attachment_ids = $product->get_gallery_attachment_ids();
			}
			if ( !empty( $attachment_ids ) ) {
				$flipclass = "kad-product-flipper";
			} else {
				$flipclass = "kad-product-noflipper";
			}
			if ( has_post_thumbnail() ) {
				$image_id = get_post_thumbnail_id( $post->ID );
			} else {
				$image_id = null;
			}
            // Make sure there is a copped image to output
            $img = virtue_get_image_array( $productimgwidth, $productimgheight, true, 'attachment-shop_catalog size-'.$productimgwidth.'x'.$productimgheight.' wp-post-image', null, $image_id, true);
				
			// Get alt and fall back to title if no alt
			if( empty( $img[ 'alt' ] ) ) {
				$img[ 'alt' ] = get_the_title();
			}
              
            echo '<div class="'.esc_attr( $flipclass ).' kt-product-intrinsic" style="padding-bottom:'. ($img[ 'height' ]/$img[ 'width' ]) * 100 .'%;">';
                echo '<div class="kad_img_flip image_flip_front">';
                    echo apply_filters('post_thumbnail_html', virtue_get_image_output( $img ), $post->ID, $image_id, array($productimgwidth, $productimgheight), $attr = '');
                echo '</div>';

                if ( ! empty( $attachment_ids ) ) {
                    $secondary_image_id = $attachment_ids['0'];
                    $second_img = virtue_get_image_array( $productimgwidth, $productimgheight, true, 'attachment-shop_catalog size-'.$productimgwidth.'x'.$productimgheight.' wp-post-image', null, $secondary_image_id, true);
                    // Get alt and fall back to title if no alt
					if( empty( $second_img[ 'alt' ] ) ) {
						$second_img[ 'alt' ] = get_the_title();
					}

                  ?>
                  <div class="kad_img_flip image_flip_back">
                  	<?php virtue_print_image_output( $second_img ); ?>
                  </div>
                <?php  } 
            echo '</div>';
        } else if ( $resizeimage == 1 ) {
			if ( has_post_thumbnail() ) {
				$image_id = get_post_thumbnail_id( $post->ID );
			} else {
				$image_id = null;
			}
			// Make sure there is a copped image to output
			$img = virtue_get_image_array( $productimgwidth, $productimgheight, true, 'attachment-shop_catalog size-'.$productimgwidth.'x'.$productimgheight.' wp-post-image', null, $image_id, true);
				
			// Get alt and fall back to title if no alt
			if( empty( $img[ 'alt' ] ) ) {
				$img[ 'alt' ] = get_the_title();
			}
			echo '<div class="kad-product-noflipper kt-product-intrinsic" style="padding-bottom:'. ($img[ 'height' ]/$img[ 'width' ]) * 100 .'%;">';
				echo apply_filters( 'post_thumbnail_html', virtue_get_image_output( $img ), $post->ID, $image_id, array( $productimgwidth, $productimgheight ), $attr = '' );
			echo '</div>';
        } else { 
			echo '<div class="kad-woo-image-size">';
				echo woocommerce_template_loop_product_thumbnail();
			echo '</div>';
        }
    }
}
add_action( 'init', 'kad_woo_archive_image_output');

/*
*
* WOO ARCHIVE CAT IMAGES
*
*/
function kad_woo_archive_cat_image_output() {
    remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
    add_action( 'woocommerce_before_subcategory_title', 'kad_woocommerce_subcategory_thumbnail', 10 );
    function kad_woocommerce_subcategory_thumbnail($category) {
        global $woocommerce_loop, $virtue_premium;

        if(is_shop() || is_product_category() || is_product_tag()) {
            if(isset($virtue_premium['product_cat_layout']) && !empty($virtue_premium['product_cat_layout'])) {
                $product_cat_column = $virtue_premium['product_cat_layout'];
            } else {
                $product_cat_column = 4;
            }
        } else {
            if ( empty( $woocommerce_loop['columns'] ) ) {
                $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
            }
            $product_cat_column = $woocommerce_loop['columns'];
        }

        if ($product_cat_column == '1') {
            $catimgwidth = 600;
        } else if ($product_cat_column == '2') {
            $catimgwidth = 600;
        } else if ($product_cat_column == '3'){
            $catimgwidth = 400;
        } else if ($product_cat_column == '6'){
            $catimgwidth = 240;
        } else if ($product_cat_column == '5'){ 
            $catimgwidth = 240;
        } else {
            $catimgwidth = 300;
        }

        if(!is_shop() && !is_product_category() && !is_product_tag()) {
            $woocommerce_loop['columns'] = $product_cat_column;
        }
        if(isset($virtue_premium['product_cat_img_ratio'])) {
            $img_ratio = $virtue_premium['product_cat_img_ratio'];
        } else {
            $img_ratio = 'widelandscape';
        }

        if($img_ratio == 'portrait') {
                $tempcatimgheight = $catimgwidth * 1.35;
                $catimgheight = floor($tempcatimgheight);
        } else if($img_ratio == 'landscape') {
                $tempcatimgheight = $catimgwidth / 1.35;
                $catimgheight = floor($tempcatimgheight);
        } else if($img_ratio == 'square') {
                $catimgheight = $catimgwidth;
        } else {
                $tempcatimgheight = $catimgwidth / 2;
                $catimgheight = floor($tempcatimgheight);
        }
        // OUTPUT 

		if($img_ratio == 'off') {
			woocommerce_subcategory_thumbnail( $category );
		} else {
			$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
			
			$img = virtue_get_image_array( $catimgwidth, $catimgheight, true, 'attachment-shop_catalog size-'.$catimgwidth.'x'.$catimgheight.' wp-post-image', null, $thumbnail_id, true);
			if( empty( $img[ 'alt' ] ) ) {
				$img[ 'alt' ] = $category->name;
			}
			if ( $img[ 'src' ] ) {
				echo '<div class="kt-cat-intrinsic" style="padding-bottom:'. ($img[ 'height' ]/$img[ 'width' ]) * 100 .'%;">';
					virtue_print_image_output( $img );
				echo '</div>';
			}
        }

    }
}
add_action( 'init', 'kad_woo_archive_cat_image_output');