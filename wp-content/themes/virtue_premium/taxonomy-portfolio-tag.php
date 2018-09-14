  <?php 
    /**
    * @hooked virtue_page_title - 20
    */
     do_action('kadence_page_title_container');
    ?>
	
    <div id="content" class="container">
   		<div class="row">
      <div class="main <?php echo virtue_main_class(); ?>" role="main">
      	<?php do_action('kadence_page_before_content'); ?>
      	<?php echo category_description(); ?> 
      	<?php if (!have_posts()) : ?>
		<div class="alert">
		    <?php _e('Sorry, no results were found.', 'virtue'); ?>
		</div>
		  <?php get_search_form(); ?>
		<?php endif; ?>
		 <?php global $virtue_premium, $kt_portfolio_loop;
		 	if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 1) {$animate = 1;} else {$animate = 0;}
		 	if(!empty($virtue_premium['portfolio_tax_items'])) {$portfolio_items = $virtue_premium['portfolio_tax_items'];} else {$portfolio_items = '12';}
		 	if(!empty($virtue_premium['portfolio_tax_order'])) {$portfolio_order = $virtue_premium['portfolio_tax_order'];} else {$portfolio_order = 'menu_order';}
		 	if(!empty($virtue_premium['portfolio_tax_column'])) {$portfolio_column = $virtue_premium['portfolio_tax_column'];} else {$portfolio_column = 4;}
            if ($portfolio_column == '2') {
               	$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; 
               	$slidewidth = 559; 
               	$slideheight = 559;
           	} else if ($portfolio_column == '3'){ 
           		$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
           		$slidewidth = 366; 
           		$slideheight = 366;
           	} else if ($portfolio_column == '6'){
           		$itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6'; 
           		$slidewidth = 240; 
           		$slideheight = 240;
           	} else if ($portfolio_column == '5'){
           		$itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
           		$slidewidth = 240; 
           		$slideheight = 240;
           	} else {
           		$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
           		$slidewidth = 269; 
           		$slideheight = 269;
           	}
			if(isset($virtue_premium['portfolio_type_under_title']) && $virtue_premium['portfolio_type_under_title'] == '0') {
				$portfolio_item_types = 'false';
			} else {
				$portfolio_item_types = 'true';
			}
			if(isset($virtue_premium['portfolio_tax_show_excerpt']) && $virtue_premium['portfolio_tax_show_excerpt'] == '0') {
				$portfolio_excerpt = 'false';
			} else {
				$portfolio_excerpt = 'true';
			}
			if(isset($virtue_premium['portfolio_tax_lightbox']) && $virtue_premium['portfolio_tax_lightbox'] == '0') {
				$portfolio_lightbox = 'false';
			} else {
				$portfolio_lightbox = 'true';
			}
			if(!empty($virtue_premium['portfolio_tax_height'])) {
					$slideheight = $virtue_premium['portfolio_tax_height'];
			}
	    	if(isset($virtue_premium['portfolio_tax_masonry']) && $virtue_premium['portfolio_tax_masonry'] == 1) {
	    			$slideheight = null;
	    	}
         	$kt_portfolio_loop = array(
	         	'lightbox' => $portfolio_lightbox,
	         	'showexcerpt' => $portfolio_excerpt,
	         	'showtypes' => $portfolio_item_types,
	         	'slidewidth' => apply_filters('kt_portfolio_grid_image_width', $slidewidth),
	         	'slideheight' => apply_filters('kt_portfolio_grid_image_height', $slideheight),
         	);
            if($portfolio_order == 'menu_order' || $portfolio_order == 'title') {$p_order = 'ASC';} else {$p_order = 'DESC';}
		    ?> 
			<div id="portfoliowrapper" class="rowtight init-isotope-intrinsic" data-fade-in="<?php echo esc_attr($animate);?>" data-iso-selector=".p-item" data-iso-style="masonry" data-iso-filter="false"> 
			<?php
				global $wp_query;
				// get the query object
				$cat_obj = $wp_query->get_queried_object();
		 		$termslug = $cat_obj->slug;
				query_posts(array(
					'paged' => $paged, 
					'posts_per_page' => $portfolio_items, 
					'orderby' => $portfolio_order, 
					'order' => $p_order, 
					'post_type' => 'portfolio', 
					'portfolio-tag' => $termslug
					) 
				);
		 		
		 		while (have_posts()) : the_post(); ?>
					<div class="<?php echo esc_attr($itemsize);?> p-item">
                		<?php 
                			do_action('kadence_portfolio_loop_start');
								get_template_part('templates/content', 'loop-portfolio'); 
						  	do_action('kadence_portfolio_loop_end');
						?>
            		</div>
				<?php endwhile; ?>
            </div> <!--portfoliowrapper-->
                
                                    
            <?php
            	/*
			* @hoooked virtue_pagination_markup - 20;
			*/
			do_action( 'virtue_pagination' );

                    $wp_query = null; 
                    wp_reset_query(); ?>
</div><!-- /.main -->