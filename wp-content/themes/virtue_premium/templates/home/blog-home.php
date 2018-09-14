<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $virtue_premium;
	// Check for Sidebar
	if(virtue_display_sidebar()) {
		$home_sidebar = true; 
		$img_width = 410; 
		$postwidthclass = 'col-md-6 col-sm-6 home-sidebar';
		$onecoumn = false;
	} else {
		$home_sidebar = false; 
		$img_width = 270; 
		$postwidthclass = 'col-md-6 col-sm-6';
		$onecoumn = false;
	}
	//Check for 1 column count
	if(isset($virtue_premium['home_post_column']) and ($virtue_premium['home_post_column'] == '1')) {
		$img_width = 360;
		$postwidthclass = 'col-md-12 col-sm-12';
		$onecoumn = true;
	} 

	if(!empty($virtue_premium['blog_title'])) {
		$btitle = $virtue_premium['blog_title'];
	} else { 
		$btitle = __('Latest from the Blog', 'virtue');
	}
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

?>
<div class="home_blog home-margin clearfix home-padding kad-animation" data-animation="fade-in" data-delay="0">
	<div class="clearfix">
		<h3 class="hometitle">
			<?php echo $btitle; ?>
		</h3>
	</div>
		<div class="row">
			<?php 
			$temp = $wp_query; 
			$wp_query = null; 
			$wp_query = new WP_Query();
			$wp_query->query(array(
				'posts_per_page' => $blogcount,
				'category_name'=> $blog_cat_slug,
				'ignore_sticky_posts' => 1,
			));
			$xyz = 0;
			if ( $wp_query ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
			
			<div class="<?php echo esc_attr($postwidthclass); ?> clearclass<?php echo ($xyz++%2); ?>">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="" itemtype="http://schema.org/BlogPosting">
	                <div class="rowtight">
	                    <?php 
	                    // Post Image
	                    	if($home_sidebar == true) {
	                    		$textsize = 'tcol-md-12 tcol-sm-12 tcol-ss-12 kt-post-text-div';
	                    		$imagesize = 'tcol-md-12 tcol-sm-12 tcol-ss-12 kt-post-image-div';
	                    	} else {
	                    		$textsize = 'tcol-md-7 tcol-sm-12 tcol-ss-12 kt-post-text-div';
	                    		$imagesize = 'tcol-md-5 tcol-sm-12 tcol-ss-12 kt-post-image-div';
	                    	}
	                    	if($onecoumn == true) {
	                    		$textsize 	= 'tcol-md-8 tcol-sm-8 tcol-ss-12 kt-post-text-div'; 
	                     		$imagesize 	= 'tcol-md-4 tcol-sm-4 tcol-ss-12 kt-post-image-div';
	                    	}
	                    	
	                    	if (has_post_thumbnail( $post->ID ) || (isset($virtue_premium['post_summery_default']) && ($virtue_premium['post_summery_default'] != 'text') ) ) {
								$display_image = true;
							} else {
								$display_image = false;
		                       	$textsize = 'tcol-md-12 tcol-ss-12 kt-post-text-div post-excerpt-no-image';
		                    } 

		                    if($display_image == true) {
		                    	$args = array(
									'width' 		=> $img_width,
									'height' 		=> 270,
									'crop'			=> true,
									'class'			=> 'iconhover post-excerpt-image',
									'alt'			=> null,
									'id'			=> get_post_thumbnail_id( $post->ID ),
									'placeholder'	=> true,
									'lazy'			=> true,
									'schema'		=> true,
									'intrinsic'     => true,
									'intrinsic_max' => true,
								);
		                    	?>
								 <div class="<?php echo esc_attr($imagesize);?>">
									<div class="imghoverclass">
		                           		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		                           			<?php virtue_print_full_image_output($args); ?>
		                           		</a> 
		                             </div>
		                         </div>

                           	<?php } ?>
	                       		
	                       		<div class="<?php echo esc_attr($textsize);?> postcontent">
	                       			<?php 
				                        /**
				                        * @hooked virtue_post_meta_date -10
				                        */
				                        do_action( 'kadence_post_mini_excerpt_before_header' );
				                        ?>
				                    <header class="home_blog_title">
			                        	<?php 
				                        /**
				                        * @hooked  virtue_post_mini_excerpt_header_title - 10
				                        * @hooked  virtue_post_meta_tooltip_subhead - 20
				                        */
				                        do_action( 'kadence_post_mini_excerpt_header' );
				                        ?>
			                        </header>
		                        	<div class="entry-content" itemprop="articleBody">
		                        		<?php 
				                        /**
				                        * 
				                        */
				                        do_action( 'kadence_post_mini_excerpt_before_content' );
				                        ?>
		                          		<p>
		                          			<?php echo virtue_excerpt($blogwordcount); ?> 
		                          			<a href="<?php the_permalink() ?>">
		                          			<?php 
		                          			if(!empty($virtue_premium['post_readmore_text'])) {
		                          				$readmore = $virtue_premium['post_readmore_text'];
		                          			} else {
		                          				$readmore = __('Read More', 'virtue');
		                          			} 
		                          			echo $readmore; ?>
		                          			</a>
		                          		</p>
		                          		<?php 
				                        /**
				                        * 
				                        */
				                        do_action( 'kadence_post_mini_excerpt_after_content' );
				                        ?>
		                        	</div>
		                      	<footer>
		                      		<?php 
			                        /**
			                        *
			                        */
			                        do_action( 'kadence_post_mini_excerpt_footer' );
			                        ?>
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