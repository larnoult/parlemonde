<?php 
//Shortcode for Carousels
function kad_carousel_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'type' 					=> '',
		'id' 					=> (rand(10,100)),
		'columns' 				=> '4',
		'orderby' 				=> '',
		'order' 				=> '',
		'mcol' 					=> null,
		'scol' 					=> null,
		'xscol' 				=> null,
		'sscol' 				=> null,
		'img_height' 			=> '',
		'autoplay' 				=> 'true',
		'offset' 				=> null,
		'speed' 				=> '9000',
		'transspeed' 			=> '400',
		'scroll' 				=> '1',
		'portfolio_show_excerpt'  => 'false',
		'portfolio_show_types' 	  => 'false',
		'portfolio_show_lightbox' => 'false',
		'cat' 						=> '',
		'class' 					=> 'products',
		'arrows' 					=> 'true',
		'productargs' 				=> null,
		'items' 	=> '8'
), $atts));
	global $post;
	$temp_post = $post;
	$this_post_id = $post->ID;
	if ( empty( $type ) ) {
		$type = 'post';
	}
	if ( 'featured' == $type || 'featured-products' == $type ) {
		$productargs = 'featured';
		$type = 'product';
	}
	if ( 'best-products' == $type || 'best' == $type ) {
		$productargs = 'best';
		$type = 'product';
	}
	if ( 'sale-products' == $type || 'sale' == $type ) {
		$productargs = 'sale';
		$type = 'product';
	}
	if ( 'cat-products' == $type ) {
		$productargs = '';
		$type = 'product';
	}
	if( empty( $orderby ) ) {
		$orderby = 'menu_order';
	}
	if( ! empty( $order ) ) {
		$order = $order;
	} else if( $orderby == 'menu_order' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}
	if( ! empty( $cat ) ) {
		$carousel_category = $cat;
	} else {
		$carousel_category = '';
	}
	ob_start();

		virtue_build_post_content_carousel( $id, $columns, $type, $carousel_category, $items, $orderby, $order, $class, $offset, $autoplay, $speed, $scroll, $arrows, $transspeed, $productargs, $portfolio_show_lightbox, $portfolio_show_excerpt, $portfolio_show_types, $img_height, $mcol, $scol, $xscol, $sscol );

	$output = ob_get_contents();
	ob_end_clean();
	wp_reset_postdata();
	$post = $temp_post;
	setup_postdata( $post );
	return $output;
}

if( ! function_exists( 'virtue_build_post_content_carousel' ) ) {
    function virtue_build_post_content_carousel( $id = 'content_carousel', $columns = '4', $type = 'post', $cat = null, $items = 8, $orderby = null, $order = null, $class = null, $offset = null, $auto = 'true', $speed = '9000', $scroll = '1', $arrows = 'true', $trans_speed = '400', $productargs = null, $portfolio_show_lightbox = 'false', $portfolio_show_excerpt = 'false', $portfolio_show_types = 'false', $imgheight = null, $mdcol = null, $smcol = null, $xscol = null, $sscol = null ) {
    	$cc = array();
		if ($columns == '2') {
			$cc = virtue_carousel_columns('2');
		}else if ($columns == '1') {
			$cc = virtue_carousel_columns('1');
		} else if ($columns == '3'){
			$cc = virtue_carousel_columns('3');
		} else if ($columns == '6'){
			$cc = virtue_carousel_columns('6');
		} else if ($columns == '5'){ 
			$cc = virtue_carousel_columns('5');
		} else {
			$cc = virtue_carousel_columns('4');
		} 
		$cc = apply_filters('kadence_carousel_columns', $cc, $id);
		if( !empty($xxlcol) ) {
			$cc['xxl'] = $xxlcol;
		}
		if( !empty($xlcol) ) {
			$cc['xl'] = $xlcol;
		}
		if( !empty($mdcol) ) {
			$cc['md'] = $mdcol;
		}
		if( !empty($smcol) ) {
			$cc['sm'] = $smcol;
		}
		if( !empty($xscol) ) {
			$cc['xs'] = $xscol;
		}
		if( !empty($sscol) ) {
			$cc['ss'] = $sscol;
		}
		$post_type = $type;
    	$extraargs = array();
    	if($type == 'portfolio') {
    		$tax = 'portfolio-type';
    		$margin = 'rowtight';
    		if ( $columns == '2' ) {
				$itemsize = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
				$slidewidth = 560;
			} else if ( $columns == '1' ) {
				$itemsize = 'tcol-lg-12 tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12';
				$slidewidth = 560;
			} else if ( $columns == '3' ){
				$itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
				$slidewidth = 400;
			} else if ( $columns == '8' ){
				$itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
				$slidewidth = 200;
			} else if ( $columns == '6' ){
				$itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
				$slidewidth = 240;
			} else if ( $columns == '5' ){
				$itemsize = 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
				$slidewidth = 240;
			} else {
				$itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
				$slidewidth = 300;
			}
			if ( ! empty( $imgheight ) ) {
				$slideheight = $imgheight;
			} else {
				$slideheight = $slidewidth;
			}
			global $kt_portfolio_loop;
			$kt_portfolio_loop = array(
				'lightbox' => $portfolio_show_lightbox,
				'showexcerpt' => $portfolio_show_excerpt,
				'showtypes' => $portfolio_show_types,
				'slidewidth' => apply_filters( 'kt_portfolio_grid_image_width', $slidewidth ),
				'slideheight' => apply_filters( 'kt_portfolio_grid_image_height', $slideheight ),
			);
         	if(empty($orderby)) {
				$orderby = 'menu_order';
			}
			if(empty($order)) {
				$order = 'ASC';
			}
    	} elseif($type == 'product') {
    		global $woocommerce_loop;
    		$margin = 'rowtight';
    		if(empty($orderby)) {
				$orderby = 'menu_order';
			}
			if(empty($order)) {
				$order = 'ASC';
			}
			if($columns == 1) {
		  		$woocommerce_loop['columns'] = 3;
		  	}else {
		  		$woocommerce_loop['columns'] = $columns;
	    	}
    		if('featured' == $productargs || 'featured-products' == $productargs){
    			if ( version_compare( WC_VERSION, '3.0', '>' ) ) {
			        $meta_query  = WC()->query->get_meta_query();
					$tax_query   = WC()->query->get_tax_query();
					$tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
						'operator' => 'IN',
					);
					$extraargs = array(
						'meta_query'          => $meta_query,
						'tax_query'           => $tax_query,
					);
				} else {
					$meta_query   = WC()->query->get_meta_query();
					$meta_query[] = array(
						'key'   => '_featured',
						'value' => 'yes'
					);

					$extraargs = array(
						'meta_query'	 => $meta_query
					);
				}
    		} else if ('best' == $productargs ||  'best-products' == $productargs ) {
    			$extraargs = array(
	    			'meta_key' 		=> 'total_sales',
					'orderby' 	=> 'meta_value_num',
					'tax_query' 		=> WC()->query->get_tax_query(),
				);
			} else if ('sale' == $productargs || 'sale-products' == $productargs){
				if (class_exists('woocommerce')) {
			        $extraargs = array(
		    			'meta_query' 		=> WC()->query->get_meta_query(),
						'post__in' 			=> array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
						'tax_query' 		=> WC()->query->get_tax_query(),
					);
      			}
			} else if ($productargs == 'latest'){
			        $extraargs = array(
		    			'orderby' 	=> 'date',
						'order' 	=> 'desc',
					);
			}
    		$tax = 'product_cat';
    	} else if($type == 'staff') {
    		$margin = 'rowtight';
    		$tax = 'staff-group';
    		if(empty($orderby)) {
				$orderby = 'menu_order';
			}
			if(empty($order)) {
				$order = 'ASC';
			}
			global $kt_staff_loop;
			$kt_staff_loop = array(
				'content' 	=> 'false',
				'link' 		=> 'true',
				'crop' 		=> 'true',
				'cropheight'=> $imgheight,
				'columns' 	=> $columns,
			);
    	} else if($type == 'testimonial') {
    		$margin = 'rowtight';
    		$tax = 'testimonial-group';
    		if(empty($orderby)) {
				$orderby = 'menu_order';
			}
			if(empty($order)) {
				$order = 'ASC';
			}
    	} else {
    		$post_type = 'post';
    		global $kt_blog_carousel_loop;
    		$kt_blog_carousel_loop = array(
    			'columns' => $columns,
    			'imgheight' => $imgheight
    		);
    		$margin = 'rowtight';
    		$tax = 'category_name';
    		if(empty($orderby)) {
				$orderby = 'date';
			}
			if(empty($order)) {
				$order = 'DESC';
			}
    	}
    	$args = array(
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'post_type' 		=> $post_type,
			'offset' 			=> $offset,
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> $items,
		);
		$args = array_merge($args, $extraargs);
		if ( ! empty( $cat ) ) {
			if('product' == $post_type) {
				if ( empty( $args['tax_query'] ) ) {
					$args['tax_query'] = array();
				}
				$args['tax_query'][] = array(
					array(
						'taxonomy' => $tax,
						'terms'    => array_map( 'sanitize_title', explode( ',', $cat ) ),
						'field'    => 'slug',
					),
				);
			} else {
				$ccat = array($tax => $cat);
				$args = array_merge($args, $ccat);
			}
		}
			echo '<div class="carousel_outerrim">';
			echo '<div class="carouselcontainer '.esc_attr($margin).'">';
			echo '<div id="kadence-carousel-'.esc_attr($id).'" class="slick-slider '.esc_attr($class).' carousel_shortcode kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="'.esc_attr($trans_speed).'" data-slider-scroll="'.esc_attr($scroll).'" data-slider-auto="'.esc_attr($auto).'" data-slider-speed="'.esc_attr($speed).'" data-slider-xxl="'.esc_attr($cc['xxl']).'" data-slider-xl="'.esc_attr($cc['xl']).'" data-slider-md="'.esc_attr($cc['md']).'" data-slider-sm="'.esc_attr($cc['sm']).'" data-slider-xs="'.esc_attr($cc['xs']).'" data-slider-ss="'.esc_attr($cc['ss']).'">';
				  	$loop = new WP_Query($args);
					if ( $loop ) : 
						if($type == 'portfolio') {
							while ( $loop->have_posts() ) : $loop->the_post();
								echo '<div class="'.esc_attr($itemsize).' kad_portfolio_item p-item">';
		                			do_action('kadence_portfolio_loop_start');
										get_template_part('templates/content', 'loop-portfolio'); 
									do_action('kadence_portfolio_loop_end');
								echo '</div>';
					        endwhile;
                    	} elseif($type == 'product') {
							while ( $loop->have_posts() ) : $loop->the_post(); 
                    			wc_get_template_part( 'content', 'product' ); 
                    		endwhile;
                    	} elseif($type == 'staff') {
                    		while ( $loop->have_posts() ) : $loop->the_post(); 
                    			get_template_part('templates/content', 'loop-staff'); 
                    		endwhile;
                    	} elseif($type == 'testimonal') {
                    		while ( $loop->have_posts() ) : $loop->the_post(); 
                    			get_template_part('templates/content', 'loop-testimonal');
                    		endwhile;
                    	} else {
                    		while ( $loop->have_posts() ) : $loop->the_post();
                    				get_template_part('templates/content', 'loop-post-carousel');
                    		endwhile;
                    	}
                    	wp_reset_postdata();
					endif; 

            echo '</div>';
            echo '</div>';
            echo '</div> <!--Carousel-->';
    }
}