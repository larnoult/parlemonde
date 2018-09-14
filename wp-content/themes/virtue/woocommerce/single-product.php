<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	?>
<div id="content" class="container">
   		<div class="row">
      <div class="main <?php echo esc_attr( virtue_main_class() ); ?>" role="main">
		<div class="product_header clearfix">
      		<?php
				$terms = wp_get_post_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
				if ( !empty( $terms ) ) {
					$main_term = $terms[0];
				} else {
					$main_term = "";
				}
				if ( $main_term ) {				
					echo '<div class="cat_back_btn headerfont"><i class="icon-arrow-left"></i> '.esc_html__( 'Back to', 'virtue' ) .' <a href="'.esc_url(get_term_link($main_term->slug, 'product_cat')).'">'.esc_html( $main_term->name ).'</a></div>';
				} else {
					echo '<div class="cat_back_btn headerfont"><i class="icon-arrow-left"></i> '.esc_html__( 'Back to', 'virtue' ).' <a href="'.esc_url( get_permalink( woocommerce_get_page_id( 'shop' ) ) ).'">'.__('Shop','virtue').'</a></div>';
				}
				?>
      	</div>
		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

</div>