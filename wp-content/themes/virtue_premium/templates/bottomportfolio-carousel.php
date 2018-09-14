<div id="portfolio_carousel_container" class="carousel_outerrim">
        <?php global $post, $virtue_premium, $kt_portfolio_loop; 
        $titletext 	= get_post_meta( $post->ID, '_kad_portfolio_carousel_title', true );  
        $bporder 	= get_post_meta( $post->ID, '_kad_portfolio_carousel_order', true );
        $bpgroup 	= get_post_meta( $post->ID, '_kad_portfolio_carousel_group', true );
        if(isset($bporder)) {
        	$bp_orderby = $bporder;
        } else {
        	$bp_orderby = 'menu_order';
        }
		if($bp_orderby == 'menu_order' || $bp_orderby == 'title') {
			$bp_order = 'ASC';
		} else {
			$bp_order = 'DESC';
		}
		if( isset( $virtue_premium['portfolio_type_under_title'] ) && $virtue_premium['portfolio_type_under_title'] == '0' ) {
			$portfolio_item_types = false;
		} else {
			$portfolio_item_types = true;
		}
		if( ! empty( $bpgroup ) && $bpgroup == 'cat') {
			$typeterms =  wp_get_post_terms( $post->ID, 'portfolio-type', array( 'orderby' => 'parent', 'order' => 'ASC' ));
			$typeterm = $typeterms[0]; 
			$bp_cat_slug = $typeterm->slug; 
		} else {
			$bp_cat_slug = '';
		} 
		if ( ! empty( $titletext ) ) {
			echo '<h3 class="title">'.esc_html( $titletext ).'</h3>'; 
		} else {
			echo '<h3 class="title">'.__('Recent Projects', 'virtue').'</h3>';
		} ?> 
        <div class="portfolio-carouselcase fredcarousel">
            <?php 	if(!empty($virtue_premium['portfolio_recent_car_column'])) {
            			$portfolio_column = $virtue_premium['portfolio_recent_car_column'];
            		} else {
            			$portfolio_column = 4;
            		}
            		if(!empty($virtue_premium['portfolio_recent_car_items'])) {
            			$portfolio_items = $virtue_premium['portfolio_recent_car_items'];
            		} else {
            			$portfolio_items = 8;
            		}
            		if(!empty($virtue_premium['portfolio_recent_carousel_speed'])) {
            			$bport_speed = $virtue_premium['portfolio_recent_carousel_speed'].'000';
            		} else {
            			$bport_speed = '9000';
            		}
					if(isset($virtue_premium['portfolio_recent_carousel_scroll']) && $virtue_premium['portfolio_recent_carousel_scroll'] == 'all' ) {
						$bport_scroll = 'all';
					} else {
						$bport_scroll = '1';
					}
					$bc = virtue_carousel_column_array( $portfolio_column, virtue_display_sidebar() );
            		if ($portfolio_column == '2') {
            			$itemsize 		= 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
            			$slidewidth 	= 559;
            			$slideheight 	= 559;
            		} else if ($portfolio_column == '3'){
            			$itemsize 		= 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
            			$slidewidth 	= 366;
            			$slideheight 	= 366;
            		} else if ($portfolio_column == '6'){
            			$itemsize 		= 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
            			$slidewidth 	= 240;
            			$slideheight 	= 240;
            		} else if ($portfolio_column == '5'){
            			$itemsize 		= 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
            			$slidewidth 	= 240;
            			$slideheight 	= 240;
            		} else {
            			$itemsize 		= 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
            			$slidewidth 	= 269;
            			$slideheight 	= 269;
            		} 
            		$kt_portfolio_loop = array(
						'lightbox' => 'false',
						'showexcerpt' => 'false',
						'showtypes' => $portfolio_item_types,
						'slidewidth' => $slidewidth,
						'slideheight' => $slideheight,
                 	);
                 	?>
				<div id="carouselcontainer-portfolio" class="rowtight">
            	<div id="portfolio-carousel" class="slick-slider bottom_portfolio_carousel kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="300" data-slider-scroll="<?php echo esc_attr($bport_scroll);?>" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($bc['xxl']);?>" data-slider-xl="<?php echo esc_attr($bc['xl']);?>" data-slider-md="<?php echo esc_attr($bc['md']);?>" data-slider-sm="<?php echo esc_attr($bc['sm']);?>" data-slider-xs="<?php echo esc_attr($bc['xs']);?>" data-slider-ss="<?php echo esc_attr($bc['ss']);?>">
                 <?php  $temp = $wp_query; 
				  		$wp_query = null; 
				  		$wp_query = new WP_Query();
						$wp_query->query(array(
							'orderby' 		=> $bp_orderby,
							'order' 		=> $bp_order,
							'post_type' 	=> 'portfolio',
							'portfolio-type'=> $bp_cat_slug,
							'post__not_in' 	=> array($post->ID),
							'posts_per_page'=> $portfolio_items,
							)
						);
						if ( $wp_query ) : 
							while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
							<div class="<?php echo esc_attr($itemsize); ?> kad_portfolio_item">
								<?php do_action('kadence_portfolio_loop_start');
									get_template_part('templates/content', 'loop-portfolio'); 
							  		do_action('kadence_portfolio_loop_end');
								?>
            				</div>
					<?php endwhile;
					  	endif; 

					  $wp_query = null; 
					  $wp_query = $temp;  // Reset
						wp_reset_query(); ?>
													
			</div>
		</div>
            </div>
</div><!-- Porfolio Container-->				