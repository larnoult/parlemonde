<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	global $virtue_premium;

	if( isset( $virtue_premium['shop_slider'] ) ) {
			$shop_slider = $virtue_premium['shop_slider'];
		} else {
			$shop_slider = 0;
		} 
		if ( is_shop() and ( $shop_slider == '1' ) ) { 
			$choose_shop_slider = $virtue_premium['choose_shop_slider'];
					if ($choose_shop_slider == "rev") {
					get_template_part('templates/shop/rev', 'slider');
					} else if ($choose_shop_slider == "ksp") {
						get_template_part('templates/shop/ksp', 'slider');
					} else if ($choose_shop_slider == "flex") {
						get_template_part('templates/shop/flex', 'slider');
					} else if ($choose_shop_slider == "fullwidth") {
						get_template_part('templates/shop/flex', 'slider-fullwidth');
					} else if ($choose_shop_slider == "cyclone") {
						get_template_part('templates/shop/shortcode', 'slider');
					}
			 } 

			 do_action( 'woocommerce_above_main_content' ); 

			/**
			 * woocommerce_before_main_content hook
			 *
			 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked woocommerce_breadcrumb - 20
			 */
			do_action( 'woocommerce_before_main_content' );
		?>
		<div class="clearfix">
		<?php 
			/**
			 * woocommerce_archive_description hook.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' ); ?>
		</div>

		<?php if ( have_posts() ) {

				/**
				 * woocommerce_before_shop_loop hook
				 * and ($shop_filter == '1')
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			
				woocommerce_product_loop_start(); 

				if ( version_compare( WC_VERSION, '3.3', '>' ) ) {
					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 *
							 * @hooked WC_Structured_Data::generate_product_data() - 10
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}
				} else  {

					while ( have_posts() ) : the_post();

						wc_get_template_part( 'content', 'product' ); 

					endwhile; // end of the loop. 

				}

			woocommerce_product_loop_end(); 

			/**
			 * woocommerce_after_shop_loop hook
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
			

		} else {
			if ( version_compare( WC_VERSION, '3.3', '<' ) ) {
					if ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) {
						wc_get_template( 'loop/no-products-found.php' );
					}
				} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action( 'woocommerce_no_products_found' );

				}
		}
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );