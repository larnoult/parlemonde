<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $virtue_premium; ?>

<div class="home_blog home-margin clearfix home-padding kad-animation" data-animation="fade-in" data-delay="0">
	<?php $img_width = 360; $postwidthclass = 'col-md-12 col-sm-12';
	if(!empty($virtue_premium['blog_title'])) {
		$btitle = $virtue_premium['blog_title'];
	} else { 
		$btitle = __('Latest from the Blog', 'virtue');
	} ?>
	<div class="clearfix">
		<h3 class="hometitle"><?php echo esc_html($btitle); ?></h3>
	</div>
		<div class="row">
			<?php 
			if(isset($virtue_premium['home_post_count'])) {
				$blogcount = $virtue_premium['home_post_count'];
			} else {
				$blogcount = '2';
			} 
			if(isset($virtue_premium['home_post_word_count'])) {
				$blogwordcount = $virtue_premium['home_post_word_count'];
			} else {
				$blogwordcount = '34';
			} 
			if(!empty($virtue_premium['home_post_type'])) { 
				$blog_cat = get_term_by ('id',$virtue_premium['home_post_type'],'category');
				$blog_cat_slug = $blog_cat -> slug;
			} else {
				$blog_cat_slug = '';
			}
				$temp = $wp_query; 
				$wp_query = null; 
				$wp_query = new WP_Query();
				$wp_query->query(array(
					'posts_per_page' 	=> $blogcount,
					'category_name'		=> $blog_cat_slug,
					'post__not_in' 		=> get_option( 'sticky_posts' )
					)
				);
				if ( $wp_query ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
					<div class="<?php echo esc_attr($postwidthclass); ?> clearclass<?php echo ($xyz++%2); ?>">
				  		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	                    	<div class="rowtight">
	                     	<?php if(isset($virtue_premium['post_summery_default']) && ($virtue_premium['post_summery_default'] != 'text')) {
	                     			$textsize 	= 'tcol-md-8 tcol-sm-8 tcol-ss-12'; 
	                     			$imagesize 	= 'tcol-md-4 tcol-sm-4 tcol-ss-12';
	                    		if (has_post_thumbnail( $post->ID ) ) {
									$image_url = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'full' ); 
									$thumbnailURL = $image_url[0]; 
									$image = aq_resize($thumbnailURL, $img_width, 270, true);
									if(empty($image)) { $image = $thumbnailURL; }
							 	} else {
								 		$thumbnailURL = virtue_post_default_placeholder();
										$image = aq_resize($thumbnailURL, $img_width, 270, true);
										if(empty($image)) { $image = $thumbnailURL; }
							 	} ?>
								<div class="<?php echo esc_attr($imagesize);?>">
									<div class="imghoverclass">
		                           		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		                           			<img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo esc_attr($img_width);?>" height="270" class="iconhover" style="display:block;">
		                           		</a> 
		                            </div>
		                        </div>
                           		<?php $image = null; $thumbnailURL = null; ?> 
                       		<?php } else { 
                       			if (has_post_thumbnail( $post->ID ) ) {
                       				$textsize = 'tcol-md-8 tcol-sm-8 tcol-ss-12'; $imagesize = 'tcol-md-4 tcol-sm-4 tcol-ss-12';
									$image_url = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'full' ); 
									$thumbnailURL = $image_url[0]; 
									$image = aq_resize($thumbnailURL, $img_width, 270, true);
									if(empty($image)) { $image = $thumbnailURL; } ?> 
								<div class="<?php echo $imagesize;?>">
									 <div class="imghoverclass">
		                           		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		                           			<img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo esc_attr($img_width);?>" height="270" class="iconhover" style="display:block;">
		                           		</a> 
		                             </div>
		                        </div>

                           		<?php $image = null; $thumbnailURL = null; 
	                           	} else { 
	                           		$textsize = 'tcol-md-12 tcol-ss-12';
	                           	}
                       		}?>
	                       		<div class="<?php echo esc_attr($textsize);?> postcontent">
	                       			<div class="postmeta color_gray">
				                        <div class="postdate bg-lightgray headerfont">
				                        	<span class="postday"><?php echo get_the_date('j'); ?></span>
				                        	<?php echo get_the_date('M Y');?>
				                        </div>
				                    </div>
				                    <header class="home_blog_title">
			                          	<a href="<?php the_permalink() ?>">
			                          		<h5 class="entry-title"><?php the_title(); ?></h5>
			                          	</a>
			                          	<div class="subhead color_gray">
			                          		<span class="postauthortop" rel="tooltip" data-placement="top" data-original-title="<?php echo get_the_author() ?>">
			                          			<i class="icon-user"></i>
			                          		</span>
			                          		<span class="kad-hidepostauthortop"> | </span>
			                          		<?php $post_category = get_the_category($post->ID); if (!empty($post_category)) { ?> 
			                          		<span class="postedintop" rel="tooltip" data-placement="top" data-original-title="<?php 
			                          			foreach ($post_category as $category)  { 
			                          				echo $category->name .'&nbsp;'; 
			                          			} ?>"><i class="icon-folder"></i></span>
			                          		 <?php }?>
			                          		  <?php if(comments_open() || (get_comments_number() != 0) ) { ?>
			                          	<span class="kad-hidepostedin">|</span>
			                        	<span class="postcommentscount" rel="tooltip" data-placement="top" data-original-title="<?php echo esc_attr(get_comments_number()); ?>">
			                        		<i class="icon-bubbles"></i>
			                        	</span>
			                        	<?php }?>
			                        </div>
			                        </header>
		                        	<div class="entry-content">
		                          		<p><?php echo virtue_excerpt($blogwordcount); ?>
			                          		<a href="<?php the_permalink() ?>">
			                          			<?php if(!empty($virtue_premium['post_readmore_text'])) {
			                          				$readmore = $virtue_premium['post_readmore_text'];
			                          				} else {
			                          					$readmore = __('Read More', 'virtue');
			                          				} 
			                          				echo $readmore; ?>
			                          		</a>
		                          		</p>
		                        	</div>
		                      		<footer>
                       				</footer>
							</div>
	                   	</div>
                    </article>
                </div>

                    <?php endwhile; else: ?>
						<li class="error-not-found"><?php _e('Sorry, no blog entries found.', 'virtue');?></li>
					<?php endif; ?>
                
				
				<?php $wp_query = null; $wp_query = $temp;  // Reset ?>
				<?php wp_reset_query(); ?>

	</div>
</div> <!--home-blog -->