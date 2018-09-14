<?php 
/**
* Shortcode for Testimonial Posts
*/
function kad_testimonial_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'orderby' => '',
		'order' => '',
		'cat' => '',
		'id' => (rand(10,100)),
		'columns' => '',
		'limit_text' => 'false',
		'offset' => null,
		'wordcount' => '25',
		'link' => 'false',
		'isostyle' => 'masonry',
		'linktext' => __('Read More', 'virtue'),
		'items' => '3'
), $atts));
	if(empty($orderby)) {
		$orderby = 'menu_order';
	}
	if ( ! empty( $order ) ) {
		$order = $order;
	} else if( $orderby == 'menu_order' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	} 
	if ( empty( $cat ) ) {
		$cat = '';
	}
	if ( empty( $columns ) ) {
		$columns = '3';
	}
	global $kt_testimonial_loop;
	$kt_testimonial_loop = array(
		'columns' 	=> $columns,
		'limit' 	=> $limit_text,
		'words' 	=> $wordcount,
		'link' 		=> $link,
		'linktext'	=> $linktext,
	);
	
	ob_start(); ?>
	<div class="home-testimonial sc-testimonial">
		<div id="testimonialwrapper-<?php echo esc_attr( $id );?>" class="rowtight reinit-isotope init-isotope-intrinsic testimonial-sc-grid testimonial-grid-columns-<?php echo esc_attr( $columns );?>" data-fade-in="<?php echo esc_attr( virtue_animate() );?>" data-iso-selector=".t_item" data-iso-style="<?php echo esc_attr( $isostyle );?>" data-iso-filter="false"> 
	        <?php 
			if( isset( $wp_query ) ) {
				$temp = $wp_query;
			} else {
				$temp = null;
			}
			$wp_query = null; 
			$wp_query = new WP_Query();
			$wp_query->query(array(
				'orderby' 			=> $orderby,
				'order' 			=> $order,
				'offset' 			=> $offset,
				'post_type' 		=> 'testimonial',
				'testimonial-group'	=> $cat,
				'posts_per_page' 	=> $items,
				)
			);
			if ( $wp_query ) : 
				while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					get_template_part( 'templates/content', 'loop-testimonial' );
				endwhile; else: ?>
				<div class="error-not-found"><?php esc_html_e( 'Sorry, no testimonial entries found.', 'virtue' );?></div>
			<?php endif; ?>
		</div> <!-- testimonialwrapper -->
			<?php 	
			$wp_query = $temp;  // Reset
			wp_reset_query(); ?>
		</div><!-- /.sc-testimonial -->
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();

	return $output;
}