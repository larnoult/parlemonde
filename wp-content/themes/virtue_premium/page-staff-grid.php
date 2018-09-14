<?php
/*
Template Name: Staff Grid
*/
global $post, $virtue_premium, $kt_staff_loop; 
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
      		$staff_category 		= get_post_meta( $post->ID, '_kad_staff_type', true );
      		$staff_full_content 	= get_post_meta( $post->ID, '_kad_staff_wordlimit', true );
      		$staff_single_link 		= get_post_meta( $post->ID, '_kad_staff_single_link', true );
      		$staff_items 			= get_post_meta( $post->ID, '_kad_staff_items', true );
      		$staff_columns 			= get_post_meta( $post->ID, '_kad_staff_columns', true );
      		$staff_cropheight 		= get_post_meta( $post->ID, '_kad_staff_img_crop', true );
      		$staff_crop 			= get_post_meta( $post->ID, '_kad_staff_crop', true );
      		$staff_filter 			= get_post_meta( $post->ID, '_kad_staff_filter', true ); 
      		$staff_orderby 			= get_post_meta( $post->ID, '_kad_staff_orderby', true ); 
      		$staff_order 			= get_post_meta( $post->ID, '_kad_staff_order', true ); 
      		if ( ! empty( $staff_full_content ) ) {
      			$staff_full_content = $staff_full_content;
      		} else {
      			$staff_full_content = 'false';
      		}
      		if ( ! empty( $staff_order ) ) {
      			$staff_order = $staff_order;
      		} else {
      			$staff_order = 'ASC';
      		}
      		if ( ! empty( $staff_orderby ) ) {
      			$staff_orderby = $staff_orderby;
      		} else {
      			$staff_orderby = 'menu_order';
      		}
			if ( ! empty( $staff_single_link ) ) {
				$staff_single_link = $staff_single_link;
			} else {
				$staff_single_link = 'false';
			}
			if( $staff_category == '-1' || empty( $staff_category ) ) { 
				$staff_cat_slug = ''; $staff_cat_ID = ''; 
			} else {
				$staff_cat 		= get_term_by( 'id',$staff_category,'staff-group' );
				$staff_cat_slug = $staff_cat -> slug;
				$staff_cat_ID 	= $staff_cat -> term_id;
			}
			$staff_category = $staff_cat_slug;
			if( $staff_items == 'all' ) {
				$staff_items = '-1';
			}
			$kt_staff_loop = array(
				'content' 	=> $staff_full_content,
				'link' 		=> $staff_single_link,
				'crop' 		=> $staff_crop,
				'cropheight'=> $staff_cropheight,
				'columns' 	=> $staff_columns,
			);
			
	  		if ($staff_filter == 'yes') {
	  		$sft = "true"; ?>
      			<section id="options" class="clearfix">
			<?php 	if(!empty($virtue_premium['filter_all_text'])) {
						$alltext = $virtue_premium['filter_all_text'];
					} else {
						$alltext = __('All', 'virtue');
					}
					if(!empty($virtue_premium['portfolio_filter_text'])) {
						$stafffiltertext = $virtue_premium['portfolio_filter_text'];
					} else {
						$stafffiltertext = __('Filter Staff', 'virtue');
					}
					$termtypes  = array( 'child_of' => $staff_cat_ID,);
					$categories = get_terms('staff-group', $termtypes);
					$count      = count( $categories );
					echo '<a class="filter-trigger headerfont" data-toggle="collapse" data-target=".filter-collapse"><i class="icon-tags"></i> '.esc_html( $stafffiltertext ).'</a>';
					echo '<ul id="filters" class="clearfix option-set filter-collapse">';
					echo '<li class="postclass"><a href="#" data-filter="*" title="All" class="selected"><h5>'.esc_html( $alltext ).'</h5><div class="arrow-up"></div></a></li>';
					if ( $count > 0 ){
						foreach ( $categories as $category ){ 
							$termname = strtolower($category->name);
							$termname = preg_replace("/[^a-zA-Z 0-9]+/", " ", $termname);
							$termname = str_replace(' ', '-', $termname);
							echo '<li class="postclass"><a href="#" data-filter=".'.esc_attr( $termname ).'" title="" rel="'.esc_attr( $termname ).'"><h5>'.esc_html( $category->name ).'</h5><div class="arrow-up"></div></a></li>';
						}
			 		}
			 		echo "</ul>"; ?>
				</section>
            <?php } else {
            	$sft = "true";
            } ?>
               <div id="staffwrapper" class="rowtight init-isotope" data-fade-in="<?php echo esc_attr( virtue_animate() );?>" data-iso-selector=".s_item" data-iso-style="masonry" data-iso-filter="<?php echo esc_attr( $sft );?>"> 
           		<?php 
            	$temp = $wp_query; 
				$wp_query = null; 
				$wp_query = new WP_Query();
				$wp_query->query( array(
					'paged' 		=> $paged,
					'post_type' 	=> 'staff',
					'orderby' 		=> $staff_orderby,
					'order' 		=> $staff_order,
					'staff-group'	=> $staff_cat_slug,
					'posts_per_page'=> $staff_items
				) );
				if ( $wp_query ) : 	 
					while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
						get_template_part( 'templates/content', 'loop-staff' );
					endwhile; else: ?>
					<div class="error-not-found"><?php esc_html_e( 'Sorry, no staff entries found.', 'virtue' );?></div>
				<?php endif; ?>
                </div> <!--portfoliowrapper-->
                                    
					<?php 
					/*
					* @hoooked virtue_pagination_markup - 20;
					*/
					do_action( 'virtue_pagination' );

					$wp_query = null; 
					$wp_query = $temp;  // Reset
					wp_reset_query();

	                /**
	                * @hooked virtue_page_comments - 20
	                */
	                do_action('kadence_page_footer');
	                do_action('virtue_page_footer');
	                ?>
</div><!-- /.main -->