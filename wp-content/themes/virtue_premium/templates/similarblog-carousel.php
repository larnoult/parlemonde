<div id="blog_carousel_container" class="carousel_outerrim">
    <?php global $post, $virtue_premium; 
    $text = get_post_meta( $post->ID, '_kad_blog_carousel_title', true );
    if( !empty($text)) {
    	echo '<h3 class="title">'.$text.'</h3>';
    } else {
    	echo '<h3 class="title">';
        echo apply_filters( 'similarposts_title', __('Similar Posts', 'virtue') );
        echo ' </h3>';
    } ?>
    <div class="blog-carouselcase fredcarousel">
    	<?php 
    	if(isset($virtue_premium['post_carousel_columns']) ) {
			$columns = $virtue_premium['post_carousel_columns'];
		} else {
			$columns = '3';
		}
		$bc = virtue_carousel_column_array( $columns, virtue_display_sidebar() );
    	if ($columns == '4') {
    		$itemsize = 'tcol-md-3 tcol-sm-3 tcol-xs-4 tcol-ss-12';
    		if (virtue_display_sidebar()) {
	    		$catimgwidth = 240;
    			$catimgheight = 240;
	    	} else {
	    		$catimgwidth = 267;
	    		$catimgheight = 267;
	    	}
    	} else if($columns == '5') {
    		$itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
    		if (virtue_display_sidebar()) {
    			$catimgwidth = 240;
    			$catimgheight = 240;
    		} else {
    			$catimgwidth = 240;
    			$catimgheight = 240;
    		}
    	} else if($columns == '6') {
    		$itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
    		if (virtue_display_sidebar()) {
    			$catimgwidth = 240;
    			$catimgheight = 240;
    		} else {
    			$catimgwidth = 240;
    			$catimgheight = 240;
    		}
    	} else {
    		$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
    		if (virtue_display_sidebar()) {
    			$catimgwidth = 266;
    			$catimgheight = 266;
    		} else {
    			$catimgwidth = 366;
    			$catimgheight = 366;
    		}
    	} ?>
		<div id="carouselcontainer-blog" class="rowtight">
    		<div id="blog_carousel" class="slick-slider blog_carousel kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="400" data-slider-scroll="1" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($bc['xxl']);?>" data-slider-xl="<?php echo esc_attr($bc['xl']);?>" data-slider-md="<?php echo esc_attr($bc['md']);?>" data-slider-sm="<?php echo esc_attr($bc['sm']);?>" data-slider-xs="<?php echo esc_attr($bc['xs']);?>" data-slider-ss="<?php echo esc_attr($bc['ss']);?>">
      		<?php if(isset($virtue_premium['blog_similar_random_order']) && $virtue_premium['blog_similar_random_order'] == 1) {
      			$oderby = 'rand';
      		} else {
      			$oderby = 'date';
      		}
      		$categories = get_the_category($post->ID);
			if ($categories) {
				$category_ids = array();
				foreach($categories as $individual_category){
					$category_ids[] = $individual_category->term_id;
				}
			}
			
			$temp = $wp_query; 
			$wp_query = null; 
			$wp_query = new WP_Query();
			$wp_query->query(array(
				'orderby' => $oderby,
				'category__in' => $category_ids,
				'post__not_in' => array($post->ID),
				'posts_per_page'=> 8
				)
			);
			if ( $wp_query ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
            <div class="<?php echo esc_attr( $itemsize );?>">
                <div <?php post_class('blog_item grid_item'); ?> itemscope itemtype="http://schema.org/BlogPosting">
					<?php 	
						if ( has_post_thumbnail( $post->ID ) ) {
							$image_id =  get_post_thumbnail_id( $post->ID );
						} else {
							$image_id = null;
						}
						$img_args = array(
							'width' 		=> $catimgwidth,
							'height' 		=> $catimgheight,
							'crop'			=> true,
							'class'			=> 'iconhover',
							'alt'			=> null,
							'id'			=> $image_id,
							'placeholder'	=> true,
							'schema'		=> true,
						);
                        ?>
						<div class="imghoverclass">
                                <a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
                                	<?php virtue_print_full_image_output( $img_args ); ?>
                                </a> 
                            </div>
                           		
				        <a href="<?php the_permalink() ?>" class="bcarousellink">
		                    <header>
								<?php
								/**
								* @hooked virtue_post_carousel_title - 10
								* @hooked virtue_post_carousel_date - 20
								*/
								do_action( 'kadence_post_carousel_small_excerpt_header' );
								?>
	                        </header>
                        	<div class="entry-content color_body">
                          		<p><?php echo strip_tags(virtue_excerpt(16)); ?></p>
                        	</div>
                   		</a>
                        <?php do_action('kadence_post_carousel_small_excerpt_footer'); ?>
                </div>
            </div>
			<?php endwhile; else: ?>
			 
			<li class="error-not-found"><?php _e('Sorry, no blog entries found.', 'virtue');?></li>
				
		  <?php endif; 
			$wp_query = null; 
			$wp_query = $temp;  // Reset
		    wp_reset_query(); ?>
													
	    </div>
    </div>
    </div>
</div><!-- Porfolio Container-->			