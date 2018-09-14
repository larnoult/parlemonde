<?php
/*
Template Name: Testimonial Grid
*/
global $post, $kt_testimonial_loop; 
    /**
    * @hooked virtue_page_title - 20
    */
	do_action('kadence_page_title_container');
	do_action('virtue_page_title_container');
    ?>
	
<div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
	<div class="row">
		<div class="main <?php echo esc_attr( virtue_main_class() ); ?>" id="ktmain" role="main">
		<?php
			do_action('kadence_page_before_content'); ?>
			<div class="entry-content" itemprop="mainContentOfPage">
				<?php get_template_part( 'templates/content', 'page' ); ?>
			</div>
			<?php

			$testimonial_category 		= get_post_meta( $post->ID, '_kad_testimonial_type', true ); 
			$testimonial_items 			= get_post_meta( $post->ID, '_kad_testimonial_items', true );
			$limit_testimonial 			= get_post_meta( $post->ID, '_kad_limit_testimonial', true );
			$testimonial_word_count 	= get_post_meta( $post->ID, '_kad_testimonial_word_count', true );
			$single_testimonial_link 	= get_post_meta( $post->ID, '_kad_single_testimonial_link', true );
			$testimonial_link_text 		= get_post_meta( $post->ID, '_kad_testimonial_link_text', true );
			$testimonial_columns 		= get_post_meta( $post->ID, '_kad_testimonial_columns', true );
			$testimonial_orderby		= get_post_meta( $post->ID, '_kad_testimonial_orderby', true );

			if ( $testimonial_category == '-1' || empty( $testimonial_category ) ) {
				$testimonial_cat_slug 	= '';
				$testimonial_cat_ID 	= '';
			} else {
				$testimonial_cat 		= get_term_by ('id',$testimonial_category,'testimonial-group' );
				$testimonial_cat_slug 	= $testimonial_cat -> slug;
				$testimonial_cat_ID 	= $testimonial_cat -> term_id;
			}
			$testimonial_category = $testimonial_cat_slug;
			if ( $testimonial_items == 'all') {
				$testimonial_items = '-1';
			}
			if ( isset( $limit_testimonial ) && 'on' == $limit_testimonial ) {
				$limit_text = 'true';
			} else {
				$limit_text = 'false';
			}
			if ( ! empty( $testimonial_word_count ) ) {
				$wordcount = $testimonial_word_count;
			} else {
				$wordcount = '25';
			}
			if ( isset( $single_testimonial_link ) && 'on' == $single_testimonial_link ) {
				$postlink = 'true';
			} else {
				$postlink = 'false';
			}
			if ( ! empty( $testimonial_link_text ) ) {
				$thelinktext = $testimonial_link_text;
			} else {
				$thelinktext = __('Read More', 'virtue');
			}
			if ( ! empty( $testimonial_orderby ) ) {
				$torderby = $testimonial_orderby;
			} else {
				$torderby = 'menu_order';
			}
			
			if ( $torderby == 'menu_order' || $torderby == 'title' ) {
				$torder = 'ASC';
			} else {
				$torder = 'DESC';
			}
			$kt_testimonial_loop = array(
				'columns' 	=> $testimonial_columns,
				'limit' 	=> $limit_text,
				'words' 	=> $wordcount,
				'link' 		=> $postlink,
				'linktext'	=> $thelinktext,
			);
			?>
			<div id="testimonialwrapper" class="rowtight init-isotope" data-fade-in="<?php echo esc_attr( virtue_animate() );?>" data-iso-selector=".t_item" data-iso-style="masonry" data-iso-filter="false"> 
			<?php
				$temp = $wp_query; 
				$wp_query = null; 
				$wp_query = new WP_Query();
				$wp_query->query(array(
					'paged' 			=> $paged,
					'post_type' 		=> 'testimonial',
					'orderby' 			=> $torderby,
					'order' 			=> $torder,
					'testimonial-group' => $testimonial_cat_slug,
					'posts_per_page' 	=> $testimonial_items
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
			/*
			* @hoooked virtue_pagination_markup - 20;
			*/
			do_action( 'virtue_pagination' );
			$wp_query = null;
			$wp_query = $temp;
			wp_reset_query();
	                    
			/**
			* @hooked virtue_page_comments - 20
			*/
			do_action('kadence_page_footer');
			do_action('virtue_page_footer');
			?>
		</div><!-- /.main -->
