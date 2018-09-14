<div id="blog_carousel_container" class="carousel_outerrim">
    <?php global $post;
    $text = get_post_meta( $post->ID, '_kad_blog_carousel_title', true );
    if(!empty($text)) {
    	echo '<h3 class="title">'.esc_html($text).'</h3>';
    } else {
    	echo '<h3 class="title">'.esc_html__('Similar Posts', 'virtue').'</h3>';
    } ?>
    <div class="blog-carouselcase fredcarousel">
        <?php if (virtue_display_sidebar()) {
        	$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
        	$catimgwidth = 266;
        	$catimgheight = 266;
        	$md = 3;
        	$sm = 3;
        	$xs = 2;
        	$ss = 1;
        } else {
        	$itemsize = 'tcol-md-3 tcol-sm-3 tcol-xs-4 tcol-ss-12';
        	$catimgwidth = 276;
        	$catimgheight = 276;
        	$md = 4;
        	$sm = 3;
        	$xs = 2;
        	$ss = 1; 
        } ?>
			<div id="carouselcontainer-blog" class="rowtight fadein-carousel">
			    <div id="blog_carousel" class="blog_carousel slick-slider kt-slickslider kt-content-carousel loading clearfix" data-slider-fade="false" data-slider-type="content-carousel" data-slider-anim-speed="300" data-slider-scroll="1" data-slider-auto="true" data-slider-speed="9000" data-slider-xxl="<?php echo esc_attr($md);?>" data-slider-xl="<?php echo esc_attr($md);?>" data-slider-md="<?php echo esc_attr($md);?>" data-slider-sm="<?php echo esc_attr($sm);?>" data-slider-xs="<?php echo esc_attr($xs);?>" data-slider-ss="<?php echo esc_attr($ss);?>">
            	<?php $categories = get_the_category($post->ID);
				if ($categories) {
					$category_ids = array();
					foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id; 
				}
					$loop = new WP_Query();
					$loop->query(array(
					  	'orderby' 	 	 => 'rand',
						'category__in' 	 => $category_ids,
						'post__not_in' 	 => array($post->ID),
						'posts_per_page' => 6
						)
					);
				 	if ( $loop ) :  
					while ( $loop->have_posts() ) : $loop->the_post(); ?>
                	<div class="<?php echo esc_attr( $itemsize );?>">
                		<div <?php post_class('blog_item grid_item'); ?> itemscope itemtype="http://schema.org/BlogPosting">
								<div class="imghoverclass">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php echo virtue_get_full_image_output( $catimgwidth, $catimgheight, true, 'iconhover', null, get_post_thumbnail_id( $post->ID ), true, true );  ?>
									</a> 
				                </div>
			              		<a href="<?php the_permalink() ?>" class="bcarousellink">
							        <header>
						               	<h5 class="entry-title" itemprop="name headline"><?php the_title(); ?></h5>
						                <div class="subhead" itemprop="datePublished">
						                	<span class="postday published"><?php echo get_the_date( get_option( 'date_format' ) ); ?></span>
						                </div>
						            </header>
		                    		<div class="entry-content" itemprop="articleBody">
		                        		<p><?php echo wp_kses_post( strip_tags( virtue_excerpt(16) ) ); ?></p>
		                    		</div>
                           		</a>
                           		<?php do_action('kadence_post_carousel_small_excerpt_footer'); ?>
	                 	</div>
	            	</div>
            		<?php endwhile; else: ?>
					<div class="error-not-found"><?php esc_html_e( 'Sorry, no blog entries found.', 'virtue' );?></div>	
					<?php endif; 
					  wp_reset_query(); ?>
													
			</div>
        </div>
    </div>
</div><!-- Blog Carousel Container-->