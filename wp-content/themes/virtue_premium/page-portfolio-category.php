<?php
/*
Template Name: Portfolio Category Grid
*/
    /**
    * @hooked virtue_page_title - 20
    */
    do_action('kadence_page_title_container');
    do_action('virtue_page_title_container');
    ?>
	
    <div id="content" class="container <?php echo esc_attr( virtue_container_class() ); ?>">
   		<div class="row">
      <div class="main <?php echo esc_attr( virtue_main_class() ); ?>" id="ktmain" role="main">
      	  <?php if ( ! post_password_required() ) {
            
            do_action('kadence_page_before_content'); ?>
			<div class="entry-content" itemprop="mainContentOfPage">
					<?php get_template_part('templates/content', 'page'); ?>
			</div>
      	<?php global $post, $virtue_premium; 
      		
			$portfolio_items = get_post_meta( $post->ID, '_kad_portfolio_items', true );
			$portfolio_column = get_post_meta( $post->ID, '_kad_portfolio_columns', true );
			$portfolio_item_excerpt = get_post_meta( $post->ID, '_kad_portfolio_item_excerpt', true ); 
			$portfolio_item_types = get_post_meta( $post->ID, '_kad_portfolio_item_types', true ); 
			$portfolio_cropheight = get_post_meta( $post->ID, '_kad_portfolio_img_crop', true );
			$portfolio_crop = get_post_meta( $post->ID, '_kad_portfolio_crop', true );
			if($portfolio_items == 'all') { 
				$portfolio_items = '-1'; 
			}
		    if ($portfolio_column == '2') {
		    	$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; 
		    	$slidewidth = 560; 
		    	$slideheight = 560;
		    } else if ($portfolio_column == '3'){ 
		    	$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
		    	$slidewidth = 366; 
		    	$slideheight = 366;
		    } else if ($portfolio_column == '6'){ 
		    	$itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6'; 
		    	$slidewidth = 240; 
		    	$slideheight = 240;
		    }  else if ($portfolio_column == '5'){ 
		    	$itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6'; 
		    	$slidewidth = 240; 
		    	$slideheight = 240;
		    } else {
		    	$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12'; 
		    	$slidewidth = 270; 
		    	$slideheight = 270;
		    }
		            
		    $crop = true; 
            if (!empty($portfolio_cropheight)) {
            	$slideheight = $portfolio_cropheight; 
            }
            if (isset($portfolio_crop) && $portfolio_crop == 'no') {
            	$slideheight = ''; 
            	$crop = false;
            }
            ?>
           	<div id="portfoliowrapper" class="init-isotope-intrinsic rowtight" data-fade-in="<?php echo esc_attr( virtue_animate() );?>" data-iso-selector=".p-item" data-iso-style="masonry" data-iso-filter="false"> 
   			<?php $meta = get_option('portfolio_cat_image');
					if ( empty( $meta) ) {
						$meta = array();
					}
					if ( ! is_array( $meta ) ) {
						$meta = (array) $meta;
					}
					$args = array( 'hide_empty=0');
            		$terms = get_terms("portfolio-type", $args);
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					     foreach ( $terms as $term ) { ?>
						     <div class="<?php echo esc_attr( $itemsize );?> p-item">
	                			<div class="portfolio_item grid_item postclass kt_item_fade_in kad-light-gallery kad_portfolio_fade_in">
	                				<?php $cat_term_id = $term->term_id;
										if(isset($meta[$cat_term_id])) {
											$item_meta = $meta[$cat_term_id];
										} else {
											$item_meta = array();
										}
										if( isset( $item_meta[ 'category_image' ] ) ) {
											$bg_image_array = $item_meta['category_image']; 
											$ct_image_id = $bg_image_array[0];
										} else {
											$ct_image_id = '';
										}
										$img = virtue_get_image_array( $slidewidth, $slideheight, $crop, null, null, $ct_image_id, true );
										?>
												<div class="imghoverclass">
			                                       <a href="<?php echo get_term_link( $term );  ?>" title="<?php echo esc_attr( $term->name ); ?>" class="kt-intrinsic" style="padding-bottom:<?php echo ($img[ 'height' ]/$img[ 'width' ]) * 100;?>%;">
			                                       	<img src="<?php echo esc_url( $img[ 'src' ] ); ?>" alt="<?php echo esc_attr( $img[ 'alt' ] ); ?>" width="<?php echo esc_attr( $img[ 'width' ] );?>" height="<?php echo esc_attr( $img[ 'height' ] );?>" <?php echo wp_kses_post( $img[ 'srcset' ] ); ?> class="lightboxhover" style="display: block;">
			                                       </a> 
			                                	</div>
			                          <?php $img = null; 
		                            
		                           ?>
						      		<a href="<?php echo esc_url( get_term_link( $term ) );?>" class="portfoliolink">
					              		<div class="piteminfo">   
					                          	<h5><?php echo $term->name; ?></h5>
					                        	<?php if( $portfolio_item_excerpt == true ) { 
					                        		echo '<p>'.$term->description.'</p>'; 
					                        	} ?>
					                    </div>
			                		</a>
			                	</div>
	                    	</div>
						<?php }
					} ?>
                </div> <!--portfoliowrapper-->
				<?php 
                /**
                * @hooked virtue_page_comments - 20
                */
                do_action('kadence_page_footer');
                do_action('virtue_page_footer');
                ?>
<?php } else { ?>
      <?php echo get_the_password_form();
    }?>
</div><!-- /.main -->