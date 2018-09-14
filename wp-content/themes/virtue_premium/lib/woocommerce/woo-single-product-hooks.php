<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'virtue_woocommerce_single_product_hooks', 5 );
function virtue_woocommerce_single_product_hooks() {

	// Single product Navigation
	add_action( 'woocommerce_single_product_summary', 'virtue_single_product_navigation', 4 );
	function virtue_single_product_navigation() {
		global $virtue_premium;
		if(isset($virtue_premium['product_nav']) && $virtue_premium['product_nav'] == '1') {?>
			<div class="productnav">
				<?php previous_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'in_same_tax' => true, 'format' => '%link', 'link' => '<i class="icon-arrow-left"></i>') ); ?>
				<?php next_post_link_plus( array('order_by' => 'menu_order', 'loop' => true, 'in_same_tax' => true, 'format' => '%link', 'link' => '<i class="icon-arrow-right"></i>') ); ?>
			</div>
		<?php }
	}

	// Add Virtue Woo Video Tab
	add_filter( 'woocommerce_product_tabs', 'virtue_product_video_tab' );
	function virtue_product_video_tab( $tabs ) {
		global $post, $virtue_premium; 
		if ( $videocode = get_post_meta( $post->ID, '_kad_product_video', true ) ) {
			if( ! empty( $virtue_premium['video_tab_text'] ) ) {
				$product_video_title = $virtue_premium['video_tab_text'];
			} else {
				$product_video_title = __('Product Video', 'virtue');
			}
			$tabs['video_tab'] = array(
				'title' => $product_video_title,
				'priority' => 50,
				'callback' => 'virtue_product_video_tab_content'
			);
		}
		return $tabs;
	}
	// video tab callback function.
	function virtue_product_video_tab_content() {
		global $post,$virtue_premium; 
		if ( $videocode = get_post_meta( $post->ID, '_kad_product_video', true ) ) {
			if( ! empty( $virtue_premium['video_title_text'] ) ) {
				$product_video_title = $virtue_premium['video_title_text'];
			} else {
				$product_video_title = __('Product Video', 'virtue');
			}
			echo '<h2>'.esc_html( $product_video_title ).'</h2>';
			echo '<div class="videofit product_video_case">'.do_shortcode( $videocode ).'</div>';
		}
	}
	// Display product tabs?
	add_action( 'wp_head','virtue_tab_check' );
	if ( ! function_exists( 'virtue_tab_check' ) ) {
		function virtue_tab_check() {
			global $virtue_premium;
			if ( isset( $virtue_premium[ 'product_tabs' ] ) && $virtue_premium[ 'product_tabs' ] == "0" ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
			}
		}
	}

	// Change the tab title
	add_filter( 'woocommerce_product_tabs', 'virtue_woo_rename_tabs', 98 );
	function virtue_woo_rename_tabs( $tabs ) {
		global $virtue_premium; 
		if(!empty($virtue_premium['description_tab_text']) && !empty($tabs['description']['title'])) {$tabs['description']['title'] = $virtue_premium['description_tab_text'];}
		if(!empty($virtue_premium['additional_information_tab_text']) && !empty($tabs['additional_information']['title'])) {$tabs['additional_information']['title'] = $virtue_premium['additional_information_tab_text'];}
		if(!empty($virtue_premium['reviews_tab_text']) && !empty($tabs['reviews']['title'])) {$tabs['reviews']['title'] = $virtue_premium['reviews_tab_text'];}

		return $tabs;
	}

	// Change the tab description heading
	add_filter( 'woocommerce_product_description_heading', 'virtue_description_tab_heading', 10, 1 );
	function virtue_description_tab_heading( $title ) {
		global $virtue_premium; 
		if(!empty($virtue_premium['description_header_text'])) {$title = $virtue_premium['description_header_text'];}
		return $title;
	}

	// Change the tab aditional info heading
	add_filter( 'woocommerce_product_additional_information_heading', 'virtue_additional_information_tab_heading', 10, 1 );
	function virtue_additional_information_tab_heading( $title ) {
		global $virtue_premium; 
		if(!empty($virtue_premium['additional_information_header_text'])) {$title = $virtue_premium['additional_information_header_text'];}
		return $title;
	}

	add_filter( 'woocommerce_product_tabs', 'virtue_woo_reorder_tabs', 98 );
	function virtue_woo_reorder_tabs( $tabs ) {
		global $virtue_premium; 
		if(isset($virtue_premium['ptab_description'])) {$dpriority = $virtue_premium['ptab_description'];} else {$dpriority = 10;}
		if(isset($virtue_premium['ptab_additional'])) {$apriority = $virtue_premium['ptab_additional'];} else {$apriority = 20;}
		if(isset($virtue_premium['ptab_reviews'])) {$rpriority = $virtue_premium['ptab_reviews'];} else {$rpriority = 30;}
		if(isset($virtue_premium['ptab_video'])) {$vpriority = $virtue_premium['ptab_video'];} else {$vpriority = 40;}

		if(!empty($tabs['description'])) $tabs['description']['priority'] = $dpriority;      // Description
		if(!empty($tabs['additional_information'])) $tabs['additional_information']['priority'] = $apriority; // Additional information 
		if(!empty($tabs['reviews'])) $tabs['reviews']['priority'] = $rpriority;     // Reviews 
		if(!empty($tabs['video_tab'])) $tabs['video_tab']['priority'] = $vpriority;      // Video second

		return $tabs;
	}


	// Display related products?
	add_action('wp_head','virtue_related_products');
	if ( ! function_exists( 'virtue_related_products' ) ) {
		function virtue_related_products() {
			global $virtue_premium;
			if ( isset( $virtue_premium[ 'related_products' ] ) && $virtue_premium[ 'related_products' ] == "0" ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
			}
		}
	}

	// Redefine woocommerce_output_related_products()
	add_filter( 'woocommerce_related_products_args', 'virtue_woo_related_products_limit' );
	function virtue_woo_related_products_limit() {
		global $product, $woocommerce;
		if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
			$related = wc_get_related_products($product->get_id(), 12);
		} else {
			$related = $product->get_related(12);
		}
		$args = array(
			'post_type'           	=> 'product',
			'no_found_rows'       	=> 1,
			'posts_per_page'      	=> 8,
			'ignore_sticky_posts'   => 1,
			'orderby'               => 'rand',
			'post__in'              => $related,
			'post__not_in'          => array($product->get_id())
		);
		return $args;
	}
	add_action( 'woocommerce_before_main_content', 'kt_woo_product_breadcrumbs', 20 );
	function kt_woo_product_breadcrumbs() {
		if ( is_product() ) {
			if ( kadence_display_product_breadcrumbs() ) {
				echo '<div class="product_header clearfix">';
					kadence_breadcrumbs();
				echo '</div>';
			}
		}
	}
}

/*
*
* WOO CUSTOM TABS
*
*/
function kad_custom_tab_01($tabs) {
	global $post; 
	$tab_content = apply_filters('kadence_custom_woo_tab_01_content', get_post_meta( $post->ID, '_kad_tab_content_01', true ) );
	if(!empty( $tab_content ) ) {
		$tab_title = get_post_meta( $post->ID, '_kad_tab_title_01', true );
		$tab_priority = get_post_meta( $post->ID, '_kad_tab_priority_01', true ); 
		if(!empty($tab_title)) {
			$product_tab_title = $tab_title;
		} else {
			$product_tab_title = __('Custom Tab', 'virtue');
		}
		if( ! empty( $tab_priority ) ) {
			$product_tab_priority = esc_attr($tab_priority);
		} else {
			$product_tab_priority = 45;
		}
		$tabs['kad_custom_tab_01'] = array(
			'title' => apply_filters('kadence_custom_woo_tab_01_title', $product_tab_title),
			'priority' => apply_filters('kadence_custom_woo_tab_01_priority', $product_tab_priority),
			'callback' => 'kad_product_custom_tab_content_01'
		);
	}

	return $tabs;
}
function kad_product_custom_tab_content_01() {
   global $post; $tab_content_01 = wpautop(get_post_meta( $post->ID, '_kad_tab_content_01', true ));
   echo do_shortcode('<div class="product_custom_content_case">'.apply_filters('kadence_custom_woo_tab_01_content', __($tab_content_01) ).'</div>');
}
function kad_custom_tab_02($tabs) {
  global $post;
  $tab_content = apply_filters('kadence_custom_woo_tab_02_content', get_post_meta( $post->ID, '_kad_tab_content_02', true ) );
   if(!empty($tab_content) ) {
    $tab_title = get_post_meta( $post->ID, '_kad_tab_title_02', true );
    $tab_priority = get_post_meta( $post->ID, '_kad_tab_priority_02', true ); 
    if(!empty($tab_title)) {$product_tab_title = $tab_title;} else {$product_tab_title = __('Custom Tab', 'virtue');}
    if(!empty($tab_priority)) {$product_tab_priority = esc_attr($tab_priority);} else {$product_tab_priority = 50;}
   $tabs['kad_custom_tab_02'] = array(
   'title' => apply_filters('kadence_custom_woo_tab_02_title', $product_tab_title),
   'priority' => apply_filters('kadence_custom_woo_tab_02_priority', $product_tab_priority),
   'callback' => 'kad_product_custom_tab_content_02'
   );
  }

 return $tabs;
}
function kad_product_custom_tab_content_02() {
   global $post; $tab_content_02 = wpautop(get_post_meta( $post->ID, '_kad_tab_content_02', true ));
   echo do_shortcode('<div class="product_custom_content_case">'.apply_filters('kadence_custom_woo_tab_02_content', __($tab_content_02) ).'</div>');

}
function kad_custom_tab_03($tabs) {
  global $post;
  $tab_content = apply_filters('kadence_custom_woo_tab_03_content', get_post_meta( $post->ID, '_kad_tab_content_03', true ) );
  if(!empty( $tab_content) ) {
    $tab_title = get_post_meta( $post->ID, '_kad_tab_title_03', true );
    $tab_priority = get_post_meta( $post->ID, '_kad_tab_priority_03', true ); 
    if(!empty($tab_title)) {$product_tab_title = $tab_title;} else {$product_tab_title = __('Custom Tab', 'virtue');}
    if(!empty($tab_priority)) {$product_tab_priority = esc_attr($tab_priority);} else {$product_tab_priority = 55;}
   $tabs['kad_custom_tab_03'] = array(
   'title' => apply_filters('kadence_custom_woo_tab_03_title', $product_tab_title ),
   'priority' => apply_filters('kadence_custom_woo_tab_03_priority', $product_tab_priority),
   'callback' => 'kad_product_custom_tab_content_03'
   );
  }

 return $tabs;
}
function kad_product_custom_tab_content_03() {
   global $post; $tab_content_03 = wpautop(get_post_meta( $post->ID, '_kad_tab_content_03', true ));
   echo do_shortcode('<div class="product_custom_content_case">'.apply_filters('kadence_custom_woo_tab_03_content', __($tab_content_03) ).'</div>');
}
add_action( 'init', 'kt_woo_custom_tab_init' );
function kt_woo_custom_tab_init() {
    global $virtue_premium;
     if ( isset( $virtue_premium['custom_tab_01'] ) && $virtue_premium['custom_tab_01'] == 1 ) {
    add_filter( 'woocommerce_product_tabs', 'kad_custom_tab_01');
    }
    if ( isset( $virtue_premium['custom_tab_02'] ) && $virtue_premium['custom_tab_02'] == 1 ) {
    add_filter( 'woocommerce_product_tabs', 'kad_custom_tab_02');
    }
    if ( isset( $virtue_premium['custom_tab_03'] ) && $virtue_premium['custom_tab_03'] == 1 ) {
    add_filter( 'woocommerce_product_tabs', 'kad_custom_tab_03');
    }
}

/*
*
* WOO RADIO VARIATION 
*
*/
function kad_woo_variation_ratio_output() {
    if ( ! function_exists( 'kad_wc_radio_variation_attribute_options' ) ) {
        function kad_wc_radio_variation_attribute_options( $args = array() ) {
            $args['class'] = 'kt-no-select2';
            echo '<div class="kt-radio-variation-container">';
            kadence_variable_swatch_wc_dropdown_variation_attribute_options($args);
            kadence_wc_radio_variation_attribute_options($args);
            echo '</div>';
        }
    }
    if ( ! function_exists( 'kadence_wc_radio_variation_attribute_options' ) ) {
      function kadence_wc_radio_variation_attribute_options( $args = array() ) {
        $args = wp_parse_args( $args, array(
          'options'          => false,
          'attribute'        => false,
          'product'          => false,
          'selected'         => false,
          'name'             => '',
          'id'               => ''
        ) );
        $options   = $args['options'];
        $product   = $args['product'];
        $attribute = $args['attribute'];
        $name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
        $id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );
        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
          $attributes = $product->get_variation_attributes();
          $options    = $attributes[ $attribute ];
        }
        echo '<fieldset class="kad_radio_variations" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">';
        if ( ! empty( $options ) ) {
          if ( $product && taxonomy_exists( $attribute ) ) {
            $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
            foreach ( $terms as $term ) {
              if ( in_array( $term->slug, $options ) ) {
                echo '<label for="'. esc_attr( sanitize_title($name) ) . esc_attr( $term->slug ) . '"><input type="radio" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" value="' . esc_attr( $term->slug ) . '" ' . checked( sanitize_title( $args['selected'] ), $term->slug, false ) . ' id="'. esc_attr( sanitize_title($name) ) . esc_attr( $term->slug ) . '" name="'. sanitize_title($name).'">' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</label>';
              }
            }
          } else {
            foreach ( $options as $option ) {
              echo '<label for="'. esc_attr( sanitize_title($name) ) . esc_attr( sanitize_title( $option ) ) .'"><input type="radio" value="' . esc_attr( $option ) . '" ' . checked( $args['selected'], $option, false ) . ' id="'. esc_attr( sanitize_title($name) ) . esc_attr( sanitize_title( $option ) ) .'" name="'. sanitize_title($name).'">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</label>';
            }
          }
        }
        echo '</fieldset>';
      }
    }

    function kadence_variable_swatch_wc_dropdown_variation_attribute_options( $args = array() ) {
        $args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'virtue' )
        ) );

        $options   = $args['options'];
        $product   = $args['product'];
        $attribute = $args['attribute'];
        $name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
        $id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );
        $class     = $args['class'];

        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[ $attribute ];
        }

        $html = '<select class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">';

        if ( $args['show_option_none'] ) {
            $html .= '<option value="">' . esc_html( $args['show_option_none'] ) . '</option>';
        }

        if ( ! empty( $options ) ) {
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

                foreach ( $terms as $term ) {
                    if ( in_array( $term->slug, $options ) ) {
                        $html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                    }
                }
            } else {
                foreach ( $options as $option ) {
                    // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                    $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                    $html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                }
            }
        }

        $html .= '</select>';

        echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html );
    }
}
add_action( 'init', 'kad_woo_variation_ratio_output');


/*
*
* WOO VARIATION ADD TO CART
*
*/
function kad_woo_variation_add_to_cart(){

    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
    add_action( 'woocommerce_single_variation', 'kt_woocommerce_single_variation', 10 );
    add_action( 'woocommerce_single_variation', 'kt_woocommerce_single_variation_add_to_cart_button', 20 );
    if ( ! function_exists( 'kt_woocommerce_single_variation_add_to_cart_button' ) ) {
       function kt_woocommerce_single_variation_add_to_cart_button() {
            global $product;
            ?>
                <div class="woocommerce-variation-add-to-cart variations_button">
					<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

					<?php
					do_action( 'woocommerce_before_add_to_cart_quantity' );

					woocommerce_quantity_input( array(
						'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
						'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
						'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
					) );

					do_action( 'woocommerce_after_add_to_cart_quantity' );
					?>

					<button type="submit" class="kad_add_to_cart headerfont kad-btn kad-btn-primary single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

					<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

					<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
					<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
					<input type="hidden" name="variation_id" class="variation_id" value="0" />
				</div>
            <?php
        }
    }
    if ( ! function_exists( 'kt_woocommerce_single_variation' ) ) {
      function kt_woocommerce_single_variation() {
        echo '<div class="single_variation headerfont"></div>';
      }
    }
}
add_action( 'init', 'kad_woo_variation_add_to_cart');

