<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


add_action( 'init', 'virtue_woocommerce_archive_hooks', 5 );
function virtue_woocommerce_archive_hooks() {
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
		$virtue = virtue_get_options();
		if ( isset( $virtue['products_per_page'] ) ) {
			return $virtue['products_per_page'];
		}
	}
	function virtue_woocommerce_archive_content_wrap_start() {
    	echo '<div class="details_product_item">';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_content_wrap_start', 5 );
	function virtue_woocommerce_archive_title_wrap_start() {
		echo '<div class="product_details">';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_wrap_start', 6 );

	// Wrap the title in a link
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_link_start', 7 );
	function virtue_woocommerce_archive_title_link_start() {
		echo '<a href="'.get_the_permalink().'" class="product_item_link">';
	}
	// Remove the woocommerce added title
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	// Add the virtue product title
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_template_loop_product_title', 10);
	function virtue_woocommerce_template_loop_product_title() {
		echo '<h5>' . get_the_title() . '</h5>';
	}
	// Close out the title wapped link
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_link_end', 15 );
	function virtue_woocommerce_archive_title_link_end() {
		echo '</a>';
	}

	function virtue_woocommerce_archive_excerpt() {
		if ( apply_filters( 'kadence_product_archive_excerpt', true ) ) : 
			global $post ?>
			<div class="product_excerpt">
				<?php
				if ( $post->post_excerpt ){
					echo apply_filters( 'archive_woocommerce_short_description', $post->post_excerpt );
				} else {
					the_excerpt();
				} ?>
			</div>
		<?php endif; 
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_excerpt', 20 );

	function virtue_woocommerce_archive_title_wrap_end() {
    	echo '</div>';
	}
	add_action( 'woocommerce_shop_loop_item_title', 'virtue_woocommerce_archive_title_wrap_end', 50 );

	function virtue_after_shop_loop_wrap_end() {
    	echo '</div>';
	}
	add_action( 'woocommerce_after_shop_loop_item', 'virtue_after_shop_loop_wrap_end', 50 );

	// Shop Page Image link open
	add_action( 'woocommerce_before_shop_loop_item_title', 'virtue_woocommerce_image_link_open', 5 );
	function virtue_woocommerce_image_link_open() {
		echo '<a href="'.get_the_permalink().'" class="product_item_link product_img_link">';
	}
	// Shop Page Image link Close
	add_action( 'woocommerce_before_shop_loop_item_title', 'virtue_woocommerce_image_link_close', 50 );
	function virtue_woocommerce_image_link_close() {
		echo '</a>';
	}
	// Remove woocommerce added archive image
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	// Add virtue archive image
	add_action( 'woocommerce_before_shop_loop_item_title', 'virtue_woocommerce_template_loop_product_thumbnail', 10 );
	function virtue_woocommerce_template_loop_product_thumbnail() {
		global $virtue, $woocommerce_loop, $post;

		// Store column count for displaying the grid
		if ( empty( $woocommerce_loop['columns'] ) ) {
			$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
		}

		if ($woocommerce_loop['columns'] == '3'){ 
			$productimgwidth = 365;
			$productimgheight = 365;
		} else {
			$productimgwidth = 268;
			$productimgheight = 268;
		}

		if(isset($virtue[ 'product_img_resize' ]) && $virtue[ 'product_img_resize' ] == 0) {
			$resizeimage = 0;
		} else {
			$resizeimage = 1;
		}

		if($resizeimage == 1) { 
			if ( has_post_thumbnail() ) {
				$image_id = get_post_thumbnail_id( $post->ID );
				$img = virtue_get_image_array( $productimgwidth, $productimgheight, true, null, null, $image_id );

				if( empty( $img[ 'alt' ] ) ) { $img[ 'alt' ] = get_the_title(); }
				ob_start();  ?> 
					<img width="<?php echo esc_attr( $img[ 'width' ] );?>" height="<?php echo esc_attr( $img[ 'height' ] );?>" src="<?php echo esc_url( $img[ 'src' ] );?>" <?php echo wp_kses_post( $img[ 'srcset' ] );?> class="attachment-shop_catalog size-<?php echo esc_attr($productimgwidth.'x'.$productimgheight);?> wp-post-image" alt="<?php echo esc_attr( $img[ 'alt' ] ); ?>">
					<?php 
					echo apply_filters('post_thumbnail_html', ob_get_clean(), $post->ID, $image_id, array($productimgwidth, $productimgheight), $attr = ''); // WPCS: XSS ok.
			} elseif ( woocommerce_placeholder_img_src() ) {
				echo woocommerce_placeholder_img( 'shop_catalog' ); // WPCS: XSS ok.
			}  
		} else { 
			echo '<div class="kad-woo-image-size">';
				echo woocommerce_template_loop_product_thumbnail(); // WPCS: XSS ok.
			echo '</div>';
		}
	}
	// Remove woocommerce archive product links (theme adds these)
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

	// define the woocommerce_loop_add_to_cart_link callback
	add_filter('woocommerce_loop_add_to_cart_args', 'virtue_add_class_woocommerce_loop_add_to_cart_link', 10, 2);
	function virtue_add_class_woocommerce_loop_add_to_cart_link( $array, $product ) {
		$array['class'] .= ' kad-btn headerfont kad_add_to_cart';
		return $array;
	}

	// Remove woocommerce category title
	remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
	// Add woocommerce category title:
	add_action( 'woocommerce_shop_loop_subcategory_title', 'virtue_woocommerce_template_loop_category_title', 10 );
	function virtue_woocommerce_template_loop_category_title( $category ) { ?>
		<h5>
		<?php
			echo esc_html( $category->name );
			if ( $category->count > 0 ) {
				echo wp_kses_post( apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category ) );
			}
		?>
		</h5>
		<?php
	}
	// Remove woocommerce category links:
	remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
	remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
	// Add woocommerce category links:
	add_action( 'woocommerce_before_subcategory', 'virtue_woocommerce_template_loop_category_link_open', 10 );
	function virtue_woocommerce_template_loop_category_link_open( $category ) {
		echo '<a href="' . esc_url( get_term_link( $category->slug, 'product_cat' ) ) . '" class="kt-woo-category-links">';
	}
	add_action( 'woocommerce_after_subcategory', 'virtue_woocommerce_template_loop_category_link_close', 10 );
	function virtue_woocommerce_template_loop_category_link_close() {
		echo '</a>';
	}
	// Remove woo output of category images
	remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
	// Add theme output of category images
    add_action( 'woocommerce_before_subcategory_title', 'virtue_woocommerce_subcategory_thumbnail', 10 );
    function virtue_woocommerce_subcategory_thumbnail( $category ) {
		global $woocommerce_loop, $virtue;
		if ( empty( $woocommerce_loop[ 'columns' ] ) ) {
			$woocommerce_loop[ 'columns' ] = apply_filters( 'loop_shop_columns', 4 );
		}
		$product_cat_column = $woocommerce_loop['columns'];

		if ( $product_cat_column == '3' ){
			$catimgwidth = 380;
		} else {
			$catimgwidth = 270;
		}
		if( isset( $virtue[ 'product_cat_img_ratio' ] ) ) {
			$img_ratio = $virtue[ 'product_cat_img_ratio' ];
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
		if($img_ratio == 'off') {
			woocommerce_subcategory_thumbnail($category);
		} else {
			$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
			if( ! empty( $thumbnail_id ) ) {
				$img = virtue_get_image_array( $catimgwidth, $catimgheight, true, null, null, $thumbnail_id, false );
				echo '<div class="kt-cat-intrinsic" style="padding-bottom:'.esc_attr( ( $img[ 'height' ]/$img[ 'width' ] ) * 100 ).'%;">';
				echo '<img src="' . esc_url( $img[ 'src' ] ) . '" width="'.esc_attr( $img[ 'width' ] ).'" height="'.esc_attr( $img[ 'height' ] ).'" alt="' . esc_attr( $img[ 'alt' ] ) . '" '. wp_kses_post( $img[ 'srcset' ] ).' />';
				echo '</div>';
			} else {
				    $cat_image = array( virtue_img_placeholder() ,$catimgwidth,$catimgheight); 
		            if ( $cat_image[0] ) {
	                    echo '<div class="kt-cat-intrinsic" style="padding-bottom:'.esc_attr( ( $catimgheight/$catimgwidth ) * 100 ).'%;">';
	                    echo '<img src="' . esc_url( $cat_image[0] ) . '" width="'.esc_attr( $cat_image[1] ).'" height="'.esc_attr( $cat_image[2] ).'" alt="' . esc_attr( $category->name ) . '" />';
	                    echo '</div>';
		            }
			}
        }

    }
    // Add support for pre woo 3.3
    add_action( 'woocommerce_before_shop_loop', 'virtue_woo_cat_loop', 60 );
	function virtue_woo_cat_loop() {
		if ( version_compare( WC_VERSION, '3.3', '<' ) ) {
			if ( ! is_search() ) {
				echo '<div class="clearfix rowtight product_category_padding">';
					woocommerce_product_subcategories(); 
				echo '</div>';
			}
		}
	}
}