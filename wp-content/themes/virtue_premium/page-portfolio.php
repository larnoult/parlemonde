	<?php
/*
Template Name: Portfolio Grid
*/
    /**
    * @hooked virtue_page_title - 20
    */
     do_action('kadence_page_title_container');
    ?>
	
    <div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
   		<div class="row">
      		<div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
      	  	<?php if ( ! post_password_required() ) { 
                do_action('kadence_page_before_content'); ?>
				<div class="entry-content" itemprop="mainContentOfPage">
					<?php get_template_part('templates/content', 'page'); ?>
				</div>
      			<?php 
      			global $post, $virtue_premium; 
      			
      			if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 1) {
      				$animate = 1;
      			} else {
      				$animate = 0;
      			}
      			$portfolio_category = get_post_meta( $post->ID, '_kad_portfolio_type', true );
			   	$portfolio_items = get_post_meta( $post->ID, '_kad_portfolio_items', true );
			   	$portfolio_order = get_post_meta( $post->ID, '_kad_portfolio_order', true );
			   	$portfolio_filter = get_post_meta( $post->ID, '_kad_portfolio_filter', true );
			   	$portfolio_column = get_post_meta( $post->ID, '_kad_portfolio_columns', true );
			   	$portfolio_item_excerpt = get_post_meta( $post->ID, '_kad_portfolio_item_excerpt', true );
			   	$portfolio_item_types = get_post_meta( $post->ID, '_kad_portfolio_item_types', true );
			   	$portfolio_cropheight = get_post_meta( $post->ID, '_kad_portfolio_img_crop', true );
			   	$portfolio_crop = get_post_meta( $post->ID, '_kad_portfolio_crop', true );
			   	$portfolio_style = get_post_meta( $post->ID, '_kad_portfolio_style', true );
			   	$portfolio_lightbox = get_post_meta( $post->ID, '_kad_portfolio_lightbox', true );

			   	if(isset($portfolio_order)) {
			   		$p_orderby = $portfolio_order;
			   	} else {
			   		$p_orderby = 'menu_order';
			   	}
			   	if($p_orderby == 'menu_order' || $p_orderby == 'title') {$p_order = 'ASC';} else {$p_order = 'DESC';}
				
				if($portfolio_category == '-1' || empty($portfolio_category)) {
					$portfolio_cat_slug = ''; $portfolio_cat_ID = ''; 
				} else {
					$portfolio_cat = get_term_by ('id',$portfolio_category,'portfolio-type' );
					$portfolio_cat_slug = $portfolio_cat -> slug;
					$portfolio_cat_ID = $portfolio_cat -> term_id;
				}
				$portfolio_category = $portfolio_cat_slug;
				if($portfolio_items == 'all') { 
					$portfolio_items = '-1'; 
				}

	  			if ($portfolio_filter == 'yes') { ?>
		      		<section id="options" class="clearfix">
						<?php 
						if(!empty($virtue_premium['filter_all_text'])) {$alltext = $virtue_premium['filter_all_text'];} else {$alltext = __('All', 'virtue');}
						if(!empty($virtue_premium['portfolio_filter_text'])) {$portfoliofiltertext = $virtue_premium['portfolio_filter_text'];} else {$portfoliofiltertext = __('Filter Projects', 'virtue');}
							$termtypes = array( 'child_of' => $portfolio_cat_ID,);
							$categories= get_terms('portfolio-type', $termtypes);
							$count = count($categories);
								echo '<a class="filter-trigger headerfont" data-toggle="collapse" data-target=".filter-collapse"><i class="icon-tags"></i> '.$portfoliofiltertext.'</a>';
								echo '<ul id="filters" class="clearfix option-set filter-collapse">';
								echo '<li class="postclass"><a href="#" data-filter="*" title="All" class="selected"><h5>'.$alltext.'</h5><div class="arrow-up"></div></a></li>';
								 if ( $count > 0 ){
									foreach ($categories as $category){ 
									$termname = strtolower($category->slug);
									$termname = preg_replace("/[^a-zA-Z 0-9]+/", " ", $termname);
									$termname = str_replace(' ', '-', $termname);
									echo '<li class="postclass kt-filter-'.esc_attr($termname).'"><a href="#" data-filter=".'.esc_attr($termname).'" title="" rel="'.esc_attr($termname).'"><h5>'.$category->name.'</h5><div class="arrow-up"></div></a></li>';
										}
						 		}
						 		echo "</ul>"; ?>
					</section>
		        <?php } 
                if($portfolio_style == 'mosaic'){
                	if ($portfolio_column == '3') {
				      $itemsize_normal = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12 mosiac_item_normal'; $ximgsize_normal = 400;$yimgsize_normal = 400;
				      $itemsize_wide = 'tcol-lg-8 tcol-md-8 tcol-sm-8 tcol-xs-12 tcol-ss-12 mosiac_item_wide'; $ximgsize_wide = 800;$yimgsize_wide = 400; $wide_string = '0,8,16,22,30';
				      $itemsize_tall = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12 mosiac_item_tall'; $ximgsize_tall = 400;$yimgsize_tall = 800; $tall_string = '5,12,14,27';
				      $itemsize_large = 'tcol-lg-8 tcol-md-8 tcol-sm-8 tcol-xs-12 tcol-ss-12 mosiac_item_large'; $ximgsize_large = 800;$yimgsize_large = 800; $large_string = '3,9,19,24';
				    } else {
				      $itemsize_normal = 'tcol-lg-3 tcol-md-3 tcol-sm-3 tcol-xs-6 tcol-ss-12 mosiac_item_normal'; $ximgsize_normal = 300;$yimgsize_normal = 300;
				      $itemsize_wide = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12 mosiac_item_wide'; $ximgsize_wide = 600;$yimgsize_wide = 300; $wide_string = '0,9,16,21,30';
				      $itemsize_tall = 'tcol-lg-3 tcol-md-3 tcol-sm-3 tcol-xs-6 tcol-ss-12 mosiac_item_tall'; $ximgsize_tall = 300;$yimgsize_tall = 600; $tall_string = '4,12,18,25';
				      $itemsize_large = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12 mosiac_item_large'; $ximgsize_large = 600;$yimgsize_large = 600; $large_string = '1,10,17,22';
				    }
				    global $kt_portfolio_loop_mosaic;
				    $kt_portfolio_loop_mosaic = array(
                 	'itemsize_normal' => $itemsize_normal,
                 	'ximgsize_normal' => $ximgsize_normal,
                 	'yimgsize_normal' => $yimgsize_normal,
                 	'itemsize_wide' => $itemsize_wide,
                 	'ximgsize_wide' => $ximgsize_wide,
                 	'yimgsize_wide' => $yimgsize_wide,
                 	'wide_string' => $wide_string,
                 	'itemsize_tall' => $itemsize_tall,
                 	'ximgsize_tall' => $ximgsize_tall,
                 	'yimgsize_tall' => $yimgsize_tall,
                 	'tall_string' => $tall_string,
                 	'itemsize_large' => $itemsize_large,
                 	'ximgsize_large' => $ximgsize_large,
                 	'yimgsize_large' => $yimgsize_large,
                 	'large_string' => $large_string,
                 	'item_count' => 0,
                 	);
                 	$slidewidth = 560; $slideheight = 560;

                } else {
			        if ($portfolio_column == '2') {$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; $slidewidth = 560; $slideheight = 560;} 
			        else if ($portfolio_column == '3'){ $itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; $slidewidth = 366; $slideheight = 366;} 
			        else if ($portfolio_column == '1'){ $itemsize = 'tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12'; $slidewidth = 1140; $slideheight = 1140;} 
			        else if ($portfolio_column == '6'){ $itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6'; $slidewidth = 240; $slideheight = 240; } 
			        else if ($portfolio_column == '5'){ $itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6'; $slidewidth = 240; $slideheight = 240;} 
			       	else {$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12'; $slidewidth = 270; $slideheight = 270;}
			    }
		        
		        $crop = true;
		        if (!empty($portfolio_cropheight)){
		        	$slideheight = $portfolio_cropheight; 
		        } 
		        if ($portfolio_crop == 'no'){ $slideheight = ''; $crop = false; } 
                
                if ($portfolio_lightbox == 'yes'){
                	$plb = 'true';
                } else {
            		$plb = 'false';
            	}
            	if($portfolio_item_excerpt == true) {
            			$showexcerpt = 'true';
            	} else {
            		$showexcerpt = 'false';
            	}
            	if($portfolio_item_types == true) {
            			$portfolio_item_types = 'true';
				} else {
					$portfolio_item_types = 'false';
				}

            	global $kt_portfolio_loop;
                 $kt_portfolio_loop = array(
                 	'lightbox' => $plb,
                 	'showexcerpt' => $showexcerpt,
                 	'showtypes' => $portfolio_item_types,
                 	'slidewidth' => apply_filters('kt_portfolio_grid_image_width', $slidewidth),
                 	'slideheight' => apply_filters('kt_portfolio_grid_image_height', $slideheight),
                 	);
                
                if($portfolio_style == 'mosaic'){	?>
                 <div class="kad-mosaic-portfolio-wrapper">
                 <div id="portfoliowrapper" class="init-mosaic-isotope rowtight" data-fade-in="<?php echo esc_attr($animate);?>" data-iso-selector=".p-item" data-iso-style="packery" data-iso-filter="true">
               	<?php } else { ?>
               	<div class="kad-portfolio-wrapper">
               		<div id="portfoliowrapper" class="init-isotope-intrinsic rowtight" data-fade-in="<?php echo esc_attr($animate);?>" data-iso-selector=".p-item" data-iso-style="masonry" data-iso-filter="true">    
            	<?php }
				
				  $temp = $wp_query; 
				  $wp_query = null; 
				  $wp_query = new WP_Query();
				  $wp_query->query(array(
					'paged' => $paged,
					'orderby' => $p_orderby,
					'order' => $p_order,
					'post_type' => 'portfolio',
					'portfolio-type'=>$portfolio_cat_slug,
					'posts_per_page' => $portfolio_items
					)
				  );
					
					if ( $wp_query ) : 
							 
					while ( $wp_query->have_posts() ) : $wp_query->the_post();

					if($portfolio_style == 'mosaic'){
								do_action('kadence_portfolio_loop_start');
									get_template_part('templates/content', 'loop-portfolio-mosaic'); 
								  do_action('kadence_portfolio_loop_end');

					} else {
								$terms = get_the_terms( $post->ID, 'portfolio-type' );
								if ( $terms && ! is_wp_error( $terms ) ) : 
									$links = array();
										foreach ( $terms as $term ) { $links[] = $term->slug;}
									$links = preg_replace("/[^a-zA-Z 0-9]+/", " ", $links);
									$links = str_replace(' ', '-', $links);	
									$tax = join( " ", $links );		
								else :	
									$tax = '';	
								endif;
								?>
							<div class="<?php echo esc_attr($itemsize); ?> <?php echo esc_attr(strtolower($tax)); ?> all p-item">
		                	<?php do_action('kadence_portfolio_loop_start');
									get_template_part('templates/content', 'loop-portfolio'); 
								  do_action('kadence_portfolio_loop_end');
							?>
		                    </div>
                    <?php } ?>

					<?php endwhile; else: ?>
					 
					<li class="error-not-found"><?php _e('Sorry, no portfolio entries found.', 'virtue');?></li>
						
				<?php endif; ?>
                </div> <!--portfoliowrapper-->
                </div>
                                    
                    <?php
                    /*
					* @hoooked virtue_pagination_markup - 20;
					*/
					do_action( 'virtue_pagination' );

                    $wp_query = null; 
                    $wp_query = $temp;  // Reset
                    wp_reset_query(); ?>

                   <?php 
                /**
                * @hooked virtue_page_comments - 20
                */
                do_action('kadence_page_footer');
                ?>

<?php } else { ?>
      <?php echo get_the_password_form();
    }?>

</div><!-- /.main -->